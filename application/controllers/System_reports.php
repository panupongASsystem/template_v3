<?php
defined('BASEPATH') or exit('No direct script access allowed');

class System_reports extends CI_Controller
{
    private $tenant_code = null;
    private $current_domain = null;

    public function __construct()
    {
        parent::__construct();

        // เช็ค login
        if (!$this->session->userdata('m_id')) {
            redirect('User/logout', 'refresh');
        }

        // ✅ เช็คว่า user มีอยู่ในตาราง tbl_member หรือไม่
        $user_exists = $this->db->where('m_id', $this->session->userdata('m_id'))
            ->where('m_status', '1')
            ->count_all_results('tbl_member');

        if ($user_exists == 0) {
            redirect('User/logout', 'refresh');
        }

        // ✅ เช็คสิทธิ์การเข้าถึงระบบรายงาน
        if (!$this->check_reports_access()) {
            show_404();
        }

        // 🆕 กำหนด tenant code แบบ dynamic
        $this->determine_tenant_code();

        // โหลด models และ libraries ที่จำเป็น
        $this->load->model('Reports_model');
        $this->load->model('space_model');
        $this->load->model('complain_model');
        $this->load->model('member_model');
        $this->load->model('Theme_model');
        $this->load->model('Storage_updater_model');
        $this->load->library('pagination');
        $this->load->library('Notification_lib');
    }

    /**
     * 🆕 กำหนด tenant code แบบ dynamic จาก database config หรือ domain
     */
    private function determine_tenant_code()
    {
        $this->current_domain = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // วิธีที่ 1: อ่านจาก database config ปัจจุบัน
        $current_db = $this->db->database;

        // แมป database name กับ tenant code
        $db_tenant_mapping = [
            'tempc2_db' => 'tempc2',
            'sawang_db' => 'sawang',
            'thongtanee_db' => 'thongtanee',
            'assystem_webanalytics' => 'webanalytics',
            'boriboon_db' => 'boriboon',
            'paeng_db' => 'paeng'
        ];

        if (isset($db_tenant_mapping[$current_db])) {
            $this->tenant_code = $db_tenant_mapping[$current_db];
        } else {
            // วิธีที่ 2: ดึงจาก domain แบบ dynamic
            $this->tenant_code = $this->extract_tenant_from_domain($this->current_domain);
        }

        log_message('info', 'System Reports - Tenant Code: ' . $this->tenant_code .
            ', DB: ' . $current_db .
            ', Domain: ' . $this->current_domain);
    }






    private function extract_tenant_from_domain($domain)
    {
        // ลบ www. ออก
        $domain = preg_replace('/^www\./', '', $domain);

        // Pattern: subdomain.domain.tld → subdomain
        if (preg_match('/^([^.]+)\.(?:[^.]+\.)?(?:co\.th|go\.th|ac\.th|or\.th|in\.th|com|net|org)$/i', $domain, $matches)) {
            $tenant = strtolower($matches[1]);

            // กรองคำที่ไม่ควรเป็น tenant
            $excluded = ['www', 'mail', 'ftp', 'admin', 'api', 'cdn', 'static'];

            if (!in_array($tenant, $excluded) && strlen($tenant) >= 2) {
                return $tenant;
            }
        }

        // localhost/development
        if (preg_match('/^(localhost|127\.0\.0\.1)/i', $domain)) {
            return 'tempc2';
        }

        // fallback: ใช้ส่วนแรกของ domain
        $parts = explode('.', $domain);
        $first_part = strtolower($parts[0]);

        return strlen($first_part) >= 2 ? $first_part : 'tempc2';
    }






    private function check_user_permissions()
    {
        $member_id = $this->session->userdata('m_id');

        // ดึงข้อมูล member พร้อม position
        $this->db->select('m.m_id, m.ref_pid, m.m_system, m.grant_system_ref_id, m.grant_user_ref_id, p.pname, p.pid');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->where('m.m_id', $member_id);
        $member = $this->db->get()->row();

        if (!$member) {
            return [
                'can_view_reports' => false,
                'can_manage_status' => false,
                'can_delete' => false,
                'user_role' => 'unknown',
                'reason' => 'ไม่พบข้อมูลผู้ใช้'
            ];
        }

        $permissions = [
            'can_view_reports' => false,
            'can_manage_status' => false,
            'can_delete' => false,
            'user_role' => $member->pname ?? 'ไม่ระบุ',
            'position_id' => $member->pid,
            'member_data' => $member,
            'reason' => ''
        ];

        // ✅ System Admin (pid = 1) - ทำได้ทุกอย่าง
        if ($member->ref_pid == 1 || $member->m_system === 'system_admin') {
            $permissions['can_view_reports'] = true;
            $permissions['can_manage_status'] = true;
            $permissions['can_delete'] = true;
            $permissions['user_role'] = 'System Admin';
            $permissions['reason'] = 'System Admin - มีสิทธิ์เต็ม';

            log_message('info', "System Admin access granted for user {$member_id}");
            return $permissions;
        }

        // ✅ Super Admin (pid = 2) - ทำได้ทุกอย่าง
        if ($member->ref_pid == 2 || $member->m_system === 'super_admin') {
            $permissions['can_view_reports'] = true;
            $permissions['can_manage_status'] = true;
            $permissions['can_delete'] = true;
            $permissions['user_role'] = 'Super Admin';
            $permissions['reason'] = 'Super Admin - มีสิทธิ์เต็ม';

            log_message('info', "Super Admin access granted for user {$member_id}");
            return $permissions;
        }

        // ✅ User Admin (pid = 3) - ต้องเช็คสิทธิ์เพิ่มเติม
        if ($member->ref_pid == 3 || $member->m_system === 'user_admin') {
            $permissions['can_view_reports'] = true; // ดูรายงานได้เสมอ
            $permissions['user_role'] = 'User Admin';

            // เช็คสิทธิ์ grant_user_id = 105 สำหรับการจัดการสถานะ
            $has_complain_permission = $this->check_grant_user_permission($member, 105);

            if ($has_complain_permission) {
                $permissions['can_manage_status'] = true;
                $permissions['reason'] = 'User Admin - มีสิทธิ์จัดการเรื่องร้องเรียน (Grant ID: 105)';

                log_message('info', "User Admin with complain permission granted for user {$member_id}");
            } else {
                $permissions['can_manage_status'] = false;
                $permissions['reason'] = 'User Admin - ไม่มีสิทธิ์จัดการเรื่องร้องเรียน (ไม่มี Grant ID: 105)';

                log_message('info', "User Admin without complain permission for user {$member_id}");
            }

            // User Admin ไม่สามารถลบได้
            $permissions['can_delete'] = false;

            return $permissions;
        }

        // ✅ End User หรืออื่นๆ - ไม่มีสิทธิ์
        $permissions['reason'] = 'ไม่มีสิทธิ์เข้าถึงระบบรายงาน (Position: ' . ($member->pname ?? 'ไม่ระบุ') . ')';

        log_message('info', "Access denied for user {$member_id} - Position: {$member->ref_pid}");
        return $permissions;
    }

    /**
     * 🆕 ฟังก์ชันตรวจสอบสิทธิ์ grant_user_permission
     */
    private function check_grant_user_permission($member, $required_grant_id)
    {
        // วิธีที่ 1: เช็คจาก grant_user_ref_id ใน tbl_member
        if (!empty($member->grant_user_ref_id)) {
            $granted_ids = explode(',', $member->grant_user_ref_id);
            $granted_ids = array_map('trim', $granted_ids);

            if (in_array((string) $required_grant_id, $granted_ids)) {
                log_message('info', "Grant permission found in member data: {$required_grant_id}");
                return true;
            }
        }

        // วิธีที่ 2: ตรวจสอบว่ามี grant_user_id = 105 ในตาราง tbl_grant_user หรือไม่
        $grant_exists = $this->db->where('grant_user_id', $required_grant_id)
            ->count_all_results('tbl_grant_user');

        if ($grant_exists > 0) {
            log_message('info', "Grant ID {$required_grant_id} exists in tbl_grant_user table");

            // ถ้ามี grant นี้ในระบบ แต่ user ไม่มี ให้ถือว่าไม่มีสิทธิ์
            return false;
        }

        // ถ้าไม่มี grant นี้ในระบบเลย ให้ถือว่าไม่ต้องเช็ค (อาจจะเป็น grant ที่ยังไม่ได้สร้าง)
        log_message('warning', "Grant ID {$required_grant_id} not found in system - allowing access");
        return true;
    }

    /**
     * ✅ ปรับปรุงฟังก์ชันเดิม
     */
    private function check_reports_access()
    {
        $permissions = $this->check_user_permissions();
        return $permissions['can_view_reports'];
    }



    private function can_manage_status()
    {
        $permissions = $this->check_user_permissions();
        return $permissions['can_manage_status'];
    }



    private function can_delete()
    {
        $permissions = $this->check_user_permissions();
        return $permissions['can_delete'];
    }




    private function get_user_permissions_for_view()
    {
        return $this->check_user_permissions();
    }




    public function clear_all_complain_data()
    {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        header('Content-Type: application/json');

        try {
            // ✅ ตรวจสอบสิทธิ์ System Admin อย่างเข้มงวด
            $member_id = $this->session->userdata('m_id');

            if (!$member_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้เข้าสู่ระบบ'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // ดึงข้อมูล member เพื่อตรวจสอบสิทธิ์
            $this->db->select('m_id, ref_pid, m_system, m_fname, m_lname');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $member_id);
            $member = $this->db->get()->row();

            if (!$member) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลผู้ใช้'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // ✅ เฉพาะ System Admin (pid = 1) เท่านั้น
            if ($member->ref_pid != 1 && $member->m_system !== 'system_admin') {
                log_message('warning', "Unauthorized clear_all_data attempt by user {$member_id} (Position: {$member->ref_pid}, System: {$member->m_system})");

                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ในการล้างข้อมูล - เฉพาะ System Admin เท่านั้น',
                    'user_role' => $member->m_system,
                    'position_id' => $member->ref_pid
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // ✅ ตรวจสอบข้อมูลที่ส่งมา
            $input = json_decode($this->input->raw_input_stream, true);

            if (!$input || $input['confirm_action'] !== 'DELETE_ALL_COMPLAINS') {
                echo json_encode([
                    'success' => false,
                    'message' => 'การยืนยันไม่ถูกต้อง'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            log_message('info', "=== CLEAR ALL COMPLAIN DATA START ===");
            log_message('info', "System Admin {$member->m_fname} {$member->m_lname} (ID: {$member_id}) initiated clear all data");

            // ✅ เริ่ม transaction
            $this->db->trans_start();

            $deleted_counts = [
                'complains' => 0,
                'images' => 0,
                'details' => 0,
                'status_images' => 0,
                'notifications' => 0,
                'notification_reads' => 0
            ];

            // 1. นับจำนวนข้อมูลก่อนลบ
            $deleted_counts['complains'] = $this->db->count_all('tbl_complain');
            $deleted_counts['details'] = $this->db->count_all('tbl_complain_detail');
            $deleted_counts['images'] = $this->db->count_all('tbl_complain_img');

            // ตรวจสอบตาราง status images
            if ($this->db->table_exists('tbl_complain_status_images')) {
                $deleted_counts['status_images'] = $this->db->count_all('tbl_complain_status_images');
            }

            // ✅ ตรวจสอบ notifications เรื่องร้องเรียน
            if ($this->db->table_exists('tbl_notifications')) {
                $deleted_counts['notifications'] = $this->db->where('reference_table', 'tbl_complain')
                    ->count_all_results('tbl_notifications');
            }

            // ✅ เพิ่ม: ตรวจสอบ notification reads
            $deleted_counts['notification_reads'] = 0;
            if ($this->db->table_exists('tbl_notification_reads')) {
                // นับ notification reads ที่เกี่ยวข้องกับเรื่องร้องเรียน
                $this->db->select('nr.*');
                $this->db->from('tbl_notification_reads nr');
                $this->db->join('tbl_notifications n', 'nr.notification_id = n.notification_id', 'inner');
                $this->db->where('n.reference_table', 'tbl_complain');
                $deleted_counts['notification_reads'] = $this->db->count_all_results();
            }

            log_message('info', "Data to be deleted: " . json_encode($deleted_counts));

            // 2. ลบไฟล์รูปภาพจากระบบไฟล์
            $this->delete_complain_files();

            // 3. ลบข้อมูลจากฐานข้อมูล (ตามลำดับ Foreign Key)

            // ✅ ลบ notification reads ก่อน (มี FK ไปยัง notifications)
            if ($this->db->table_exists('tbl_notification_reads') && $this->db->table_exists('tbl_notifications')) {
                // ลบ notification reads ที่เกี่ยวข้องกับเรื่องร้องเรียน
                $this->db->query("
                    DELETE nr FROM tbl_notification_reads nr 
                    INNER JOIN tbl_notifications n ON nr.notification_id = n.notification_id 
                    WHERE n.reference_table = 'tbl_complain'
                ");
                log_message('info', "Deleted complain-related notification reads");
            }

            // ลบ notifications ที่เกี่ยวข้องกับเรื่องร้องเรียน
            if ($this->db->table_exists('tbl_notifications')) {
                $this->db->where('reference_table', 'tbl_complain');
                $this->db->delete('tbl_notifications');
                log_message('info', "Deleted complain notifications");
            }

            // ลบ status images
            if ($this->db->table_exists('tbl_complain_status_images')) {
                $this->db->empty_table('tbl_complain_status_images');
                log_message('info', "Deleted status images");
            }

            // ลบรูปภาพประกอบ
            $this->db->empty_table('tbl_complain_img');
            log_message('info', "Deleted complain images");

            // ลบประวัติการดำเนินงาน
            $this->db->empty_table('tbl_complain_detail');
            log_message('info', "Deleted complain details");

            // ลบเรื่องร้องเรียนหลัก
            $this->db->empty_table('tbl_complain');
            log_message('info', "Deleted all complains");

            // ✅ Reset AUTO_INCREMENT (เริ่มนับใหม่)
            $this->db->query("ALTER TABLE tbl_complain AUTO_INCREMENT = 1");
            $this->db->query("ALTER TABLE tbl_complain_detail AUTO_INCREMENT = 1");
            $this->db->query("ALTER TABLE tbl_complain_img AUTO_INCREMENT = 1");

            if ($this->db->table_exists('tbl_complain_status_images')) {
                $this->db->query("ALTER TABLE tbl_complain_status_images AUTO_INCREMENT = 1");
            }

            log_message('info', "Reset AUTO_INCREMENT for all tables");

            // ✅ Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed during clear all data');
                throw new Exception('เกิดข้อผิดพลาดในการลบข้อมูล');
            }

            log_message('info', "=== CLEAR ALL COMPLAIN DATA COMPLETED SUCCESSFULLY ===");
            log_message('info', "All complain data cleared by System Admin {$member->m_fname} {$member->m_lname} (ID: {$member_id})");

            echo json_encode([
                'success' => true,
                'message' => 'ล้างข้อมูลเรื่องร้องเรียนทั้งหมดเรียบร้อยแล้ว',
                'deleted_counts' => $deleted_counts,
                'cleared_by' => $member->m_fname . ' ' . $member->m_lname,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->trans_rollback();

            log_message('error', 'Error in clear_all_complain_data: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการล้างข้อมูล: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 🗑️ ฟังก์ชันลบไฟล์รูปภาพจากระบบไฟล์
     */
    private function delete_complain_files()
    {
        try {
            $deleted_files = 0;

            // ลบรูปภาพจาก tbl_complain_img
            $images = $this->db->select('complain_img_img')
                ->get('tbl_complain_img')
                ->result();

            foreach ($images as $image) {
                $file_path = FCPATH . 'docs/complain/' . $image->complain_img_img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                    $deleted_files++;
                }
            }

            // ลบรูปภาพสถานะ (ถ้ามี)
            if ($this->db->table_exists('tbl_complain_status_images')) {
                $status_images = $this->db->select('image_filename')
                    ->get('tbl_complain_status_images')
                    ->result();

                foreach ($status_images as $image) {
                    $file_path = FCPATH . 'docs/complain/status/' . $image->image_filename;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                        $deleted_files++;
                    }
                }
            }

            // ลบโฟลเดอร์ว่าง (ถ้ามี)
            $folders_to_check = [
                FCPATH . 'docs/complain/status/',
                FCPATH . 'docs/complain/'
            ];

            foreach ($folders_to_check as $folder) {
                if (is_dir($folder) && count(scandir($folder)) == 2) { // เฉพาะ . และ ..
                    rmdir($folder);
                }
            }

            log_message('info', "Deleted {$deleted_files} files from filesystem");

        } catch (Exception $e) {
            log_message('error', 'Error deleting files: ' . $e->getMessage());
            // ไม่ throw exception เพื่อให้การลบข้อมูลในฐานข้อมูลดำเนินต่อไป
        }
    }



    public function index()
    {
        // ✅ อัปเดตข้อมูลพื้นที่จัดเก็บทุกครั้งที่เข้าหน้า main
        $this->auto_update_storage_data();

        // ดึงข้อมูลพื้นฐานสำหรับแสดงในหน้าเมนู
        $data['user_info'] = $this->get_user_info();
        $data['reports_summary'] = $this->Reports_model->get_reports_summary();

        // 🆕 เพิ่มข้อมูลสิทธิ์ผู้ใช้
        $data['user_permissions'] = $this->get_user_permissions_for_view();

        // *** เพิ่มใหม่: ดึงข้อมูลสถิติคิว ***
        try {
            $this->load->model('Queue_model', 'queue_model');
            $queue_stats = $this->queue_model->get_queue_statistics_for_dashboard();
            $data['reports_summary']['queue_stats'] = $queue_stats;

            log_message('info', 'Index - Queue stats loaded successfully');
        } catch (Exception $e) {
            log_message('error', 'Index Queue Stats Error: ' . $e->getMessage());
            // ถ้าเกิด error ให้ใช้ข้อมูลเปล่า
            $data['reports_summary']['queue_stats'] = [
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'today' => 0,
                'overdue' => 0,
                'success_rate' => 0
            ];
        }

        // ✅ เพิ่มข้อมูลสถิติเว็บไซต์ใหม่
        try {
            $this->load->model('External_stats_model');
            $current_tenant = $this->External_stats_model->get_current_tenant_code();

            if ($current_tenant) {
                // ดึงข้อมูลสถิติเว็บไซต์แบบ 7 วันล่าสุด
                $web_stats = $this->External_stats_model->get_stats_summary('7days');
                $data['reports_summary']['web_stats'] = [
                    'total_pageviews' => $web_stats['total_pageviews'] ?? 0,
                    'total_visitors' => $web_stats['total_visitors'] ?? 0,
                    'online_users' => $web_stats['online_users'] ?? 0,
                    'avg_pages_per_visitor' => $web_stats['avg_pageviews_per_visitor'] ?? 0
                ];

                log_message('info', 'Index - Web stats loaded for tenant: ' . $current_tenant);
            } else {
                // ถ้าเชื่อมต่อไม่ได้ให้ใช้ข้อมูลเปล่า
                $data['reports_summary']['web_stats'] = [
                    'total_pageviews' => 0,
                    'total_visitors' => 0,
                    'online_users' => 0,
                    'avg_pages_per_visitor' => 0
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'Index Web Stats Error: ' . $e->getMessage());
            // ถ้าเกิด error ให้ใช้ข้อมูลเปล่า
            $data['reports_summary']['web_stats'] = [
                'total_pageviews' => 0,
                'total_visitors' => 0,
                'online_users' => 0,
                'avg_pages_per_visitor' => 0
            ];
        }

        $data['page_title'] = 'หน้าหลัก - ระบบรายงาน';
        $data['tenant_code'] = $this->tenant_code;
        $data['current_domain'] = $this->current_domain;

        // 🆕 เพิ่มข้อมูล debug สำหรับ system admin
        $data['is_system_admin'] = $this->is_system_admin();

        // ใช้ reports header/footer แทน
        $this->load->view('reports/header', $data);
        $this->load->view('reports/index', $data);
        $this->load->view('reports/footer');
    }

    /**
     * *** เพิ่มใหม่: API สำหรับดึงข้อมูลสรุปคิว ***
     */
    public function api_queue_summary()
    {
        try {
            header('Content-Type: application/json');

            // โหลด Queue_model
            $this->load->model('Queue_model', 'queue_model');

            // ดึงข้อมูลสถิติคิว
            $queue_stats = $this->queue_model->get_queue_statistics_for_dashboard();

            echo json_encode([
                'success' => true,
                'queue_stats' => $queue_stats,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in api_queue_summary: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล',
                'queue_stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'in_progress' => 0,
                    'completed' => 0,
                    'today' => 0,
                    'overdue' => 0,
                    'success_rate' => 0
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
    }




    public function update_complain_status()
    {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // ✅ ตรวจสอบสิทธิ์การจัดการสถานะ
        if (!$this->can_manage_status()) {
            $permissions = $this->get_user_permissions_for_view();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ในการจัดการสถานะเรื่องร้องเรียน',
                    'reason' => $permissions['reason'] ?? 'ไม่มีสิทธิ์',
                    'user_role' => $permissions['user_role'] ?? 'ไม่ระบุ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบการ login ของ staff
        if (!$this->session->userdata('m_id')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าใช้งาน'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        try {
            log_message('info', '=== UPDATE_COMPLAIN_STATUS START ===');

            // รับข้อมูลจากฟอร์ม
            $complain_id = $this->input->post('complain_id');
            $new_status = $this->input->post('new_status');
            $comment = $this->input->post('comment', true); // XSS clean

            log_message('info', "Input - Complain ID: {$complain_id}, Status: {$new_status}, Comment: {$comment}");

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($complain_id) || empty($new_status)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ข้อมูลไม่ครบถ้วน'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // *** ดึงข้อมูล complain ก่อนอัปเดต ***
            $this->db->where('complain_id', $complain_id);
            $complain = $this->db->get('tbl_complain')->row();

            if (!$complain) {
                log_message('error', 'Complain not found: ' . $complain_id);
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบเรื่องร้องเรียนที่ระบุ'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', "Found complain - ID: {$complain->complain_id}, Topic: {$complain->complain_topic}, Current Status: {$complain->complain_status}");
            log_message('info', "User info - ID: " . ($complain->complain_user_id ?: 'NULL') . ", Type: " . ($complain->complain_user_type ?: 'NULL'));

            // ข้อมูล staff ที่ทำการอัพเดท
            $staff_id = $this->session->userdata('m_id');
            $staff_name = trim($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'));

            // 🆕 เพิ่มข้อมูลสิทธิ์ใน log
            $permissions = $this->get_user_permissions_for_view();
            log_message('info', "Staff info - ID: {$staff_id}, Name: {$staff_name}, Role: {$permissions['user_role']}");

            // *** เริ่ม transaction ***
            $this->db->trans_start();

            // อัปเดตสถานะ
            $update_complain = [
                'complain_status' => $new_status,
                'complain_dateupdate' => date('Y-m-d H:i:s')
            ];

            $this->db->where('complain_id', $complain_id);
            $update_result = $this->db->update('tbl_complain', $update_complain);

            log_message('info', "Update complain result: " . ($update_result ? 'SUCCESS' : 'FAILED'));

            if (!$update_result) {
                log_message('error', 'Failed to update complain status');
                throw new Exception('Failed to update complain status');
            }

            // เพิ่มรายการในตาราง tbl_complain_detail
            $detail_data = [
                'complain_detail_case_id' => $complain_id,
                'complain_detail_status' => $new_status,
                'complain_detail_com' => $comment ?: 'อัพเดทสถานะโดยระบบ',
                'complain_detail_by' => $staff_name,
                'complain_detail_datesave' => date('Y-m-d H:i:s'),
                'complain_detail_staff_id' => $staff_id
            ];

            $detail_result = $this->db->insert('tbl_complain_detail', $detail_data);
            log_message('info', "Insert detail result: " . ($detail_result ? 'SUCCESS' : 'FAILED'));

            if (!$detail_result) {
                log_message('error', 'Failed to insert complain detail');
                throw new Exception('Failed to insert complain detail');
            }

            // *** แก้ไข: สร้าง notification ภายใน transaction ***
            log_message('info', '=== STARTING NOTIFICATION CREATION ===');

            $notification_result = false;
            try {
                // โหลด library
                if (!isset($this->notification_lib)) {
                    $this->load->library('notification_lib');
                    log_message('info', 'Notification_lib loaded');
                }

                // สร้าง notification
                $notification_result = $this->notification_lib->complain_status_updated(
                    $complain->complain_id,
                    $new_status,
                    $staff_name,
                    $complain->complain_user_id ?? null,
                    $complain->complain_user_type ?? null
                );

                log_message('info', "Notification creation result: " . ($notification_result ? 'SUCCESS' : 'FAILED'));

                // ✅ เพิ่ม: ตรวจสอบว่า notification ถูกสร้างจริง
                if ($notification_result) {
                    $check_count = $this->db->where('reference_id', $complain_id)
                        ->where('reference_table', 'tbl_complain')
                        ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-2 minutes')))
                        ->count_all_results('tbl_notifications');

                    log_message('info', "Verification: Found {$check_count} notifications in database");

                    if ($check_count == 0) {
                        log_message('info', 'Notification creation returned true but no records found in database');
                    }
                }

            } catch (Exception $notification_error) {
                log_message('error', 'Notification creation failed: ' . $notification_error->getMessage());
                $notification_result = false;
            }

            // *** Commit transaction ***
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed');
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'Database transaction completed successfully');
            log_message('info', "Staff {$staff_name} (ID: {$staff_id}) updated complain {$complain_id} status to: {$new_status}");

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'อัพเดทสถานะเรียบร้อยแล้ว',
                    'new_status' => $new_status,
                    'updated_by' => $staff_name,
                    'updated_at' => date('d/m/Y H:i'),
                    'notification_sent' => $notification_result,
                    'user_role' => $permissions['user_role'],
                    'debug' => [
                        'complain_id' => $complain_id,
                        'user_id' => $complain->complain_user_id ?? 'NULL',
                        'user_type' => $complain->complain_user_type ?? 'NULL',
                        'notification_created' => $notification_result,
                        'staff_id' => $staff_id,
                        'staff_name' => $staff_name,
                        'staff_role' => $permissions['user_role']
                    ]
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->trans_rollback();

            log_message('error', 'Error updating complain status: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }

        log_message('info', '=== UPDATE_COMPLAIN_STATUS END ===');
    }

    /**
     * ✅ แก้ไข: อัปเดตสถานะพร้อมรูปภาพ (เพิ่ม notification)
     */
    public function update_complain_status_with_images()
    {
        header('Content-Type: application/json');

        if ($this->input->method() !== 'post') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // ✅ ตรวจสอบสิทธิ์การจัดการสถานะ
        if (!$this->can_manage_status()) {
            $permissions = $this->get_user_permissions_for_view();

            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ในการจัดการสถานะเรื่องร้องเรียน',
                'reason' => $permissions['reason'] ?? 'ไม่มีสิทธิ์',
                'user_role' => $permissions['user_role'] ?? 'ไม่ระบุ'
            ]);
            return;
        }

        try {
            log_message('info', '=== UPDATE_COMPLAIN_STATUS_WITH_IMAGES START ===');

            $complain_id = $this->input->post('complain_id');
            $new_status = $this->input->post('new_status');
            $status_note = $this->input->post('status_note');
            $current_user = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
            $staff_id = $this->session->userdata('m_id');

            // 🆕 เพิ่มข้อมูลสิทธิ์ใน log
            $permissions = $this->get_user_permissions_for_view();
            log_message('info', "Input - Complain ID: {$complain_id}, Status: {$new_status}, Staff Role: {$permissions['user_role']}");

            // Validation
            if (empty($complain_id) || empty($new_status)) {
                throw new Exception('ข้อมูลไม่ครบถ้วน');
            }

            // ✅ เพิ่ม: ดึงข้อมูล complain สำหรับ notification
            $this->db->where('complain_id', $complain_id);
            $complain = $this->db->get('tbl_complain')->row();

            if (!$complain) {
                throw new Exception('ไม่พบเรื่องร้องเรียนที่ระบุ');
            }

            log_message('info', "Found complain - User ID: " . ($complain->complain_user_id ?: 'NULL') . ", Type: " . ($complain->complain_user_type ?: 'NULL'));

            // *** เริ่ม transaction ***
            $this->db->trans_start();

            // อัปเดตสถานะ (เหมือนเดิม)
            $detail_data = [
                'complain_detail_case_id' => $complain_id,
                'complain_detail_status' => $new_status,
                'complain_detail_com' => $status_note ?: "อัปเดตสถานะเป็น '{$new_status}'",
                'complain_detail_by' => $current_user,
                'complain_detail_staff_id' => $staff_id,
                'complain_detail_datesave' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_complain_detail', $detail_data);
            $detail_id = $this->db->insert_id();

            log_message('info', "Insert detail result: " . ($detail_id ? 'SUCCESS' : 'FAILED') . " - Detail ID: {$detail_id}");

            // อัปเดตสถานะหลัก
            $this->db->where('complain_id', $complain_id);
            $update_result = $this->db->update('tbl_complain', [
                'complain_status' => $new_status,
                'complain_dateupdate' => date('Y-m-d H:i:s')
            ]);

            log_message('info', "Update complain result: " . ($update_result ? 'SUCCESS' : 'FAILED'));

            // จัดการอัปโหลดรูปภาพ (ใหม่)
            $uploaded_images = $this->handle_status_images_upload($detail_id);

            log_message('info', "Uploaded images: " . count($uploaded_images));

            // ✅ เพิ่ม: สร้าง notification
            $notification_result = false;
            try {
                log_message('info', '=== STARTING NOTIFICATION CREATION (WITH IMAGES) ===');

                if (!isset($this->notification_lib)) {
                    $this->load->library('notification_lib');
                    log_message('info', 'Notification_lib loaded for images update');
                }

                $notification_result = $this->notification_lib->complain_status_updated(
                    $complain->complain_id,
                    $new_status,
                    $current_user,
                    $complain->complain_user_id ?? null,
                    $complain->complain_user_type ?? null
                );

                log_message('info', "Notification creation result (with images): " . ($notification_result ? 'SUCCESS' : 'FAILED'));

            } catch (Exception $notification_error) {
                log_message('error', 'Notification creation failed (with images): ' . $notification_error->getMessage());
                $notification_result = false;
            }

            // *** Commit transaction ***
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed (with images)');
                throw new Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
            }

            log_message('info', 'Database transaction completed successfully (with images)');

            echo json_encode([
                'success' => true,
                'message' => "อัปเดตสถานะเป็น '{$new_status}' เรียบร้อย",
                'uploaded_images' => count($uploaded_images),
                'detail_id' => $detail_id,
                'notification_sent' => $notification_result,
                'user_role' => $permissions['user_role'],
                'debug' => [
                    'complain_id' => $complain_id,
                    'user_id' => $complain->complain_user_id ?? 'NULL',
                    'user_type' => $complain->complain_user_type ?? 'NULL',
                    'notification_created' => $notification_result,
                    'staff_name' => $current_user,
                    'staff_role' => $permissions['user_role']
                ]
            ]);

        } catch (Exception $e) {
            // Rollback transaction on error
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }

            log_message('error', 'Error updating complain status with images: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        log_message('info', '=== UPDATE_COMPLAIN_STATUS_WITH_IMAGES END ===');
    }




    private function handle_status_images_upload($detail_id)
    {
        $uploaded_files = [];

        if (!empty($_FILES['status_images']['name'][0])) {
            $this->load->library('upload');

            // ✅ แก้ไข: เปลี่ยน path ให้ตรงกับ view
            $upload_path = './docs/complain/status/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
                log_message('info', '✅ Created directory: ' . $upload_path);
            }

            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'jpg|jpeg|png|gif|webp',
                'max_size' => 5120, // 5MB
                'encrypt_name' => true,
                'remove_spaces' => true
            ];

            $files_count = count($_FILES['status_images']['name']);
            log_message('info', '📁 Processing ' . $files_count . ' status images for detail_id: ' . $detail_id);

            for ($i = 0; $i < $files_count && $i < 5; $i++) {
                if ($_FILES['status_images']['error'][$i] === UPLOAD_ERR_OK) {
                    // จัดเตรียมไฟล์สำหรับ upload
                    $_FILES['single_file']['name'] = $_FILES['status_images']['name'][$i];
                    $_FILES['single_file']['type'] = $_FILES['status_images']['type'][$i];
                    $_FILES['single_file']['tmp_name'] = $_FILES['status_images']['tmp_name'][$i];
                    $_FILES['single_file']['error'] = $_FILES['status_images']['error'][$i];
                    $_FILES['single_file']['size'] = $_FILES['status_images']['size'][$i];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('single_file')) {
                        $upload_data = $this->upload->data();

                        // ✅ แก้ไข: ตรวจสอบ table ก่อนบันทึก
                        if ($this->db->table_exists('tbl_complain_status_images')) {
                            // บันทึกข้อมูลรูปภาพลงฐานข้อมูล
                            $image_data = [
                                'complain_detail_id' => $detail_id,
                                'image_filename' => $upload_data['file_name'],
                                'image_original_name' => $upload_data['orig_name'],
                                'image_size' => $upload_data['file_size'] * 1024,
                                'uploaded_by' => $this->session->userdata('m_id'),
                                'uploaded_at' => date('Y-m-d H:i:s')
                            ];

                            $insert_result = $this->db->insert('tbl_complain_status_images', $image_data);

                            if ($insert_result) {
                                $uploaded_files[] = $upload_data['file_name'];
                                log_message('info', '✅ Status image uploaded: ' . $upload_data['file_name']);
                            } else {
                                log_message('error', '❌ Failed to insert image data: ' . $upload_data['file_name']);
                            }
                        } else {
                            log_message('error', '❌ Table tbl_complain_status_images does not exist');
                            // สร้างตารางอัตโนมัติ
                            $this->create_status_images_table();

                            // ลองบันทึกอีกครั้ง
                            $image_data = [
                                'complain_detail_id' => $detail_id,
                                'image_filename' => $upload_data['file_name'],
                                'image_original_name' => $upload_data['orig_name'],
                                'image_size' => $upload_data['file_size'] * 1024,
                                'uploaded_by' => $this->session->userdata('m_id'),
                                'uploaded_at' => date('Y-m-d H:i:s')
                            ];

                            $this->db->insert('tbl_complain_status_images', $image_data);
                            $uploaded_files[] = $upload_data['file_name'];
                        }

                    } else {
                        log_message('error', '❌ Image upload failed: ' . $this->upload->display_errors());
                    }
                }
            }
        }

        log_message('info', '📊 Total status images uploaded: ' . count($uploaded_files));
        return $uploaded_files;
    }





    private function create_status_images_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_complain_status_images` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `complain_detail_id` int(11) NOT NULL,
        `image_filename` varchar(255) NOT NULL,
        `image_original_name` varchar(255) DEFAULT NULL,
        `image_size` int(11) DEFAULT NULL,
        `uploaded_by` int(11) DEFAULT NULL,
        `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `complain_detail_id` (`complain_detail_id`),
        KEY `uploaded_by` (`uploaded_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $result = $this->db->query($sql);

        if ($result) {
            log_message('info', '✅ Created table: tbl_complain_status_images');
        } else {
            log_message('error', '❌ Failed to create table: tbl_complain_status_images');
        }

        return $result;
    }



    private function create_complain_status_notification($complain, $new_status, $staff_name)
    {
        try {
            log_message('info', "=== CREATING COMPLAIN STATUS NOTIFICATION ===");
            log_message('info', "Complain ID: {$complain->complain_id}");
            log_message('info', "New Status: {$new_status}");
            log_message('info', "Updated By: {$staff_name}");
            log_message('info', "Target User ID: {$complain->complain_user_id}");
            log_message('info', "Target User Type: {$complain->complain_user_type}");

            // *** ตรวจสอบและโหลด Notification_lib ***
            if (!isset($this->notification_lib)) {
                $this->load->library('notification_lib');
                log_message('info', 'Notification_lib loaded in controller');
            }

            // ตรวจสอบว่า library โหลดสำเร็จ
            if (!isset($this->notification_lib)) {
                log_message('error', 'Failed to load Notification_lib');
                return false;
            }

            log_message('info', 'Notification_lib is ready');

            // *** ตรวจสอบว่ามีการอัปเดตสถานะจริงหรือไม่ ***
            if ($complain->complain_status === $new_status) {
                log_message('info', 'Status not changed, skipping notification');
                return true; // ไม่ error แต่ไม่ต้องส่ง notification
            }

            // *** ส่งการแจ้งเตือน ***
            log_message('info', 'Calling complain_status_updated method...');

            $notification_result = $this->notification_lib->complain_status_updated(
                $complain->complain_id,
                $new_status,
                $staff_name,
                $complain->complain_user_id,
                $complain->complain_user_type
            );

            log_message('info', "Notification library method result: " . ($notification_result ? 'SUCCESS' : 'FAILED'));

            if ($notification_result) {
                log_message('info', "✅ Status update notification sent successfully for complain {$complain->complain_id}");

                // *** ตรวจสอบว่า notification ถูกสร้างจริงใน database ***
                $this->verify_notification_created($complain->complain_id);

            } else {
                log_message('warning', "❌ Failed to send status update notification for complain {$complain->complain_id}");
            }

            return $notification_result;

        } catch (Exception $e) {
            log_message('error', 'Exception in create_complain_status_notification: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }





    // ✅ เพิ่ม method ตรวจสอบ
    private function verify_notifications_created($complain_id, $debug_info)
    {
        $notifications = $this->db->where('reference_id', $complain_id)
            ->where('reference_table', 'tbl_complain')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-2 minutes')))
            ->get('tbl_notifications')
            ->result();

        log_message('info', "=== NOTIFICATION VERIFICATION ===");
        log_message('info', "Found " . count($notifications) . " notifications for complain {$complain_id}");

        $staff_found = false;
        $individual_found = false;
        $public_found = false;

        foreach ($notifications as $notif) {
            log_message('info', "- ID: {$notif->notification_id}, Role: {$notif->target_role}, User: {$notif->target_user_id}, Type: {$notif->type}");

            if ($notif->target_role === 'staff') {
                $staff_found = true;
            }

            if ($notif->target_role === 'public' && !empty($notif->target_user_id)) {
                $individual_found = true;
            }

            if ($notif->target_role === 'public' && empty($notif->target_user_id)) {
                $public_found = true;
            }
        }

        log_message('info', "Staff notification: " . ($staff_found ? 'FOUND' : 'MISSING'));
        log_message('info', "Individual notification: " . ($individual_found ? 'FOUND' : 'MISSING'));
        log_message('info', "Public notification: " . ($public_found ? 'FOUND' : 'MISSING'));

        // ตรวจสอบว่าควรมี public notification หรือไม่
        if ($debug_info['should_create_public'] = ($debug_info['is_public_type'] && $debug_info['has_user_id'])) {
            if (!$individual_found) {
                log_message('warning', "❌ Missing individual notification for public user!");
            }
            if (!$public_found) {
                log_message('warning', "❌ Missing general public notification!");
            }
        }
    }



    private function create_enhanced_status_notification($complain, $new_status, $staff_name)
    {
        try {
            log_message('info', "=== CREATING STATUS NOTIFICATION ===");

            // *** แก้ไข: โหลด library แบบแน่นอน ***
            if (!isset($this->notification_lib)) {
                $this->load->library('notification_lib');
                log_message('info', 'Notification_lib loaded');
            }

            // ตรวจสอบว่า library โหลดสำเร็จ
            if (!isset($this->notification_lib)) {
                log_message('error', 'Failed to load Notification_lib');
                return false;
            }

            // *** ดึงข้อมูล user ที่เป็นเจ้าของ case ***
            $complain_user_id = $complain->complain_user_id;
            $complain_user_type = $complain->complain_user_type;

            log_message('info', "Notification target - User ID: {$complain_user_id}, Type: {$complain_user_type}");
            log_message('info', "Complain details - ID: {$complain->complain_id}, Topic: {$complain->complain_topic}");

            // *** ส่งการแจ้งเตือน ***
            $notification_result = $this->notification_lib->complain_status_updated(
                $complain->complain_id,
                $new_status,
                $staff_name,
                $complain_user_id,
                $complain_user_type
            );

            log_message('info', "Notification library result: " . ($notification_result ? 'SUCCESS' : 'FAILED'));

            if ($notification_result) {
                log_message('info', "✅ Status update notification sent successfully for complain {$complain->complain_id}");

                // *** เพิ่ม: ตรวจสอบว่า notification ถูกสร้างจริงใน database ***
                $check_notifications = $this->db->where('reference_id', $complain->complain_id)
                    ->where('reference_table', 'tbl_complain')
                    ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 minute')))
                    ->get('tbl_notifications')
                    ->result();

                log_message('info', "Database check: " . count($check_notifications) . " notifications found for complain {$complain->complain_id}");

                foreach ($check_notifications as $notif) {
                    log_message('info', "Found notification: ID={$notif->notification_id}, Type={$notif->type}, Role={$notif->target_role}, UserID={$notif->target_user_id}");
                }

            } else {
                log_message('warning', "❌ Failed to send status update notification for complain {$complain->complain_id}");
            }

            return $notification_result;

        } catch (Exception $e) {
            log_message('error', 'Exception in create_enhanced_status_notification: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }





    private function verify_notification_created($complain_id)
    {
        try {
            log_message('info', '=== VERIFYING NOTIFICATION IN DATABASE ===');

            // ตรวจสอบ notification ที่สร้างในช่วง 2 นาทีล่าสุด
            $recent_notifications = $this->db->where('reference_id', $complain_id)
                ->where('reference_table', 'tbl_complain')
                ->where('type', 'complain')
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-2 minutes')))
                ->order_by('created_at', 'DESC')
                ->get('tbl_notifications')
                ->result();

            log_message('info', "Found " . count($recent_notifications) . " recent notifications for complain {$complain_id}");

            foreach ($recent_notifications as $notif) {
                log_message('info', "Notification Details:");
                log_message('info', "- ID: {$notif->notification_id}");
                log_message('info', "- Type: {$notif->type}");
                log_message('info', "- Target Role: {$notif->target_role}");

                // ✅ แก้ไข: ใช้ null coalescing operator
                $target_user_id = $notif->target_user_id ?? 'NULL';
                $target_user_type = $notif->target_user_type ?? 'NULL';

                log_message('info', "- Target User ID: {$target_user_id}");
                log_message('info', "- Target User Type: {$target_user_type}");
                log_message('info', "- Title: {$notif->title}");
                log_message('info', "- Created: {$notif->created_at}");
            }

            return count($recent_notifications) > 0;

        } catch (Exception $e) {
            log_message('error', 'Error verifying notification: ' . $e->getMessage());
            return false;
        }
    }





    public function debug_complain_notification($complain_id = null)
    {
        if (ENVIRONMENT !== 'development' && $this->session->userdata('m_system') !== 'system_admin') {
            show_404();
            return;
        }

        $complain_id = $complain_id ?: $this->uri->segment(3);

        if (!$complain_id) {
            echo "กรุณาระบุ complain_id เช่น: /System_reports/debug_complain_notification/68612339";
            return;
        }

        echo "<h2>🔍 Debug Complain Notification #{$complain_id}</h2>";

        try {
            // 1. ตรวจสอบข้อมูล complain
            $complain = $this->db->get_where('tbl_complain', ['complain_id' => $complain_id])->row();

            if (!$complain) {
                echo "❌ ไม่พบ complain ID: {$complain_id}<br>";
                return;
            }

            echo "<h3>ข้อมูล Complain:</h3>";
            echo "<ul>";
            echo "<li>ID: {$complain->complain_id}</li>";
            echo "<li>Topic: " . htmlspecialchars($complain->complain_topic) . "</li>";
            echo "<li>Status: " . htmlspecialchars($complain->complain_status) . "</li>";

            // ✅ แก้ไข: ตรวจสอบ property ก่อนแสดง
            $user_id = isset($complain->complain_user_id) ? $complain->complain_user_id : 'NULL';
            $user_type = isset($complain->complain_user_type) ? $complain->complain_user_type : 'NULL';

            echo "<li>User ID: {$user_id}</li>";
            echo "<li>User Type: {$user_type}</li>";
            echo "<li>Created: {$complain->complain_datesave}</li>";
            echo "</ul>";

            // 2. ตรวจสอบ notifications ที่มีอยู่
            echo "<h3>Notifications ที่เกี่ยวข้อง:</h3>";
            $notifications = $this->db->where('reference_id', $complain_id)
                ->where('reference_table', 'tbl_complain')
                ->order_by('created_at', 'DESC')
                ->get('tbl_notifications')
                ->result();

            if ($notifications) {
                echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
                echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Type</th><th>Title</th><th>Target Role</th><th>Target User</th><th>Created</th></tr>";

                foreach ($notifications as $notif) {
                    // ✅ แก้ไข: ใช้ isset() และ null coalescing
                    $target_user_id = isset($notif->target_user_id) ? $notif->target_user_id : 'NULL';
                    $target_user_type = isset($notif->target_user_type) ? $notif->target_user_type : 'NULL';
                    $title = isset($notif->title) ? htmlspecialchars($notif->title) : 'NULL';
                    $type = isset($notif->type) ? htmlspecialchars($notif->type) : 'NULL';
                    $target_role = isset($notif->target_role) ? htmlspecialchars($notif->target_role) : 'NULL';

                    echo "<tr>";
                    echo "<td>{$notif->notification_id}</td>";
                    echo "<td>{$type}</td>";
                    echo "<td>{$title}</td>";
                    echo "<td>{$target_role}</td>";
                    echo "<td>{$target_user_id} ({$target_user_type})</td>";
                    echo "<td>{$notif->created_at}</td>";
                    echo "</tr>";
                }

                echo "</table>";

                echo "<p><strong>จำนวน notifications ทั้งหมด:</strong> " . count($notifications) . " รายการ</p>";
            } else {
                echo "<p style='color: orange;'>ไม่มี notifications สำหรับ complain นี้</p>";
            }

            // 3. แสดงข้อมูล complain detail
            echo "<h3>ข้อมูลเพิ่มเติม:</h3>";

            // ตรวจสอบว่า user type เป็น anonymous
            if ($user_type === 'anonymous' || empty($user_id)) {
                echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<strong>⚠️ หมายเหตุ:</strong> เรื่องร้องเรียนนี้ถูกส่งโดย <strong>anonymous user</strong><br>";
                echo "- ไม่มี User ID หรือเป็น guest user<br>";
                echo "- ระบบจะสร้าง notification เฉพาะสำหรับ Staff เท่านั้น<br>";
                echo "- ไม่มีการสร้าง public notification เพราะไม่มี logged-in user";
                echo "</div>";
            }

            // 4. ทดสอบการสร้าง notification (เฉพาะ system admin)
            if ($this->session->userdata('m_system') === 'system_admin') {
                echo "<h3>ทดสอบสร้าง Notification:</h3>";
                $this->load->library('notification_lib');

                // ส่งข้อมูลที่ปลอดภัย
                $safe_user_id = (!empty($user_id) && $user_id !== 'NULL') ? $user_id : null;
                $safe_user_type = (!empty($user_type) && $user_type !== 'NULL') ? $user_type : null;

                $test_result = $this->notification_lib->complain_status_updated(
                    $complain_id,
                    'ทดสอบ Debug - ' . date('H:i:s'),
                    'ระบบ Debug',
                    $safe_user_id,
                    $safe_user_type
                );

                if ($test_result) {
                    echo "<p style='color: green;'>✅ สร้าง notification สำเร็จ</p>";

                    // ตรวจสอบอีกครั้ง
                    $new_notifications = $this->db->where('reference_id', $complain_id)
                        ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 minute')))
                        ->get('tbl_notifications')
                        ->result();

                    echo "<p>พบ notification ใหม่: <strong>" . count($new_notifications) . "</strong> รายการ</p>";

                    if ($new_notifications) {
                        echo "<h4>Notifications ที่สร้างใหม่:</h4>";
                        echo "<ul>";
                        foreach ($new_notifications as $new_notif) {
                            $new_target_user_id = isset($new_notif->target_user_id) ? $new_notif->target_user_id : 'NULL';
                            $new_target_user_type = isset($new_notif->target_user_type) ? $new_notif->target_user_type : 'NULL';
                            $new_title = isset($new_notif->title) ? htmlspecialchars($new_notif->title) : 'NULL';

                            echo "<li>ID: {$new_notif->notification_id} | Title: {$new_title} | Target: {$new_target_user_id} ({$new_target_user_type})</li>";
                        }
                        echo "</ul>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ ไม่สามารถสร้าง notification ได้</p>";
                }
            }

            // 5. แสดงข้อมูล session
            echo "<h3>ข้อมูล Session:</h3>";
            echo "<ul>";
            echo "<li>Staff ID: " . ($this->session->userdata('m_id') ?: 'NULL') . "</li>";
            echo "<li>Staff Name: " . ($this->session->userdata('m_fname') ?: 'NULL') . " " . ($this->session->userdata('m_lname') ?: 'NULL') . "</li>";
            echo "<li>System Role: " . ($this->session->userdata('m_system') ?: 'NULL') . "</li>";
            echo "</ul>";

        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
            echo "<pre style='margin-top: 10px; font-size: 12px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
        }
    }





    private function create_status_update_notification_with_target_user($complain, $new_status, $staff_name)
    {
        try {
            // ตรวจสอบว่ามี Notification library หรือไม่
            if (!class_exists('Notification_lib')) {
                if (file_exists(APPPATH . 'libraries/Notification_lib.php')) {
                    $this->load->library('notification_lib');
                } else {
                    log_message('info', 'Notification_lib not found, skipping notification');
                    return;
                }
            }

            // *** ดึงข้อมูล user ที่เป็นเจ้าของ case ***
            $complain_user_id = $complain->complain_user_id;
            $complain_user_type = $complain->complain_user_type;

            log_message('info', "Creating notification for complain {$complain->complain_id} - User ID: {$complain_user_id}, Type: {$complain_user_type}");

            // ส่งการแจ้งเตือนแบบใหม่ที่รองรับ target_user_id
            $notification_result = $this->notification_lib->complain_status_updated(
                $complain->complain_id,
                $new_status,
                $staff_name,
                $complain_user_id,
                $complain_user_type
            );

            if ($notification_result) {
                log_message('info', "Status update notification sent successfully for complain {$complain->complain_id}");
            } else {
                log_message('warning', "Failed to send status update notification for complain {$complain->complain_id}");
            }

        } catch (Exception $e) {
            log_message('error', 'Failed to create status update notification: ' . $e->getMessage());
            // ไม่ให้ error ของ notification ขัดขวางการอัพเดทสถานะ
        }
    }





    /**
     * API สำหรับดึงข้อมูลหมวดหมู่
     */
    public function complain_categories_api($cat_id = null)
    {
        header('Content-Type: application/json');

        try {
            if ($cat_id) {
                // ดึงหมวดหมู่เดียว
                $category = $this->Reports_model->get_category_by_id($cat_id);
                echo json_encode([
                    'success' => true,
                    'category' => $category
                ]);
            } else {
                // ดึงหมวดหมู่ทั้งหมด
                $categories = $this->Reports_model->get_all_categories();
                echo json_encode([
                    'success' => true,
                    'categories' => $categories
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * บันทึกหมวดหมู่
     */
    public function save_complain_category()
    {
        header('Content-Type: application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_category_permission()) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ในการจัดการหมวดหมู่'
            ]);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $data = [
                'cat_name' => trim($input['cat_name']),
                'cat_icon' => trim($input['cat_icon']) ?: 'fas fa-exclamation-circle',
                'cat_color' => trim($input['cat_color']) ?: '#e55a2b',
                'cat_order' => intval($input['cat_order']) ?: 0,
                'cat_status' => intval($input['cat_status']) ?: 1
            ];

            if (empty($data['cat_name'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกชื่อหมวดหมู่'
                ]);
                return;
            }

            if (!empty($input['cat_id'])) {
                // อัปเดต
                $result = $this->Reports_model->update_category($input['cat_id'], $data);
            } else {
                // เพิ่มใหม่
                $data['cat_created_by'] = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
                $result = $this->Reports_model->insert_category($data);
            }

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'บันทึกเรียบร้อย' : 'เกิดข้อผิดพลาดในการบันทึก'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ลบหมวดหมู่
     */
    public function delete_complain_category()
    {
        header('Content-Type: application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_category_permission()) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ในการลบหมวดหมู่'
            ]);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $cat_id = intval($input['cat_id']);

            if ($cat_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'รหัสหมวดหมู่ไม่ถูกต้อง'
                ]);
                return;
            }

            // ตรวจสอบว่ามีการใช้งานหรือไม่
            $usage_count = $this->Reports_model->count_category_usage($cat_id);
            if ($usage_count > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => "ไม่สามารถลบได้ เนื่องจากมีเรื่องร้องเรียน {$usage_count} เรื่องที่ใช้หมวดหมู่นี้"
                ]);
                return;
            }

            $result = $this->Reports_model->delete_category($cat_id);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'ลบหมวดหมู่เรียบร้อย' : 'เกิดข้อผิดพลาดในการลบ'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ตรวจสอบสิทธิ์การจัดการหมวดหมู่
     */
    private function check_category_permission()
    {
        $permissions = $this->get_user_permissions_for_view();
        return $permissions['can_manage_status'] || $permissions['position_id'] <= 2;
    }






    public function complain_detail($complain_id)
    {
        // ตรวจสอบการ login
        if (!$this->session->userdata('m_id')) {
            redirect('login');
            return;
        }

        if (empty($complain_id)) {
            show_404();
            return;
        }

        // ดึงข้อมูลเรื่องร้องเรียนพร้อมข้อมูล user ที่เกี่ยวข้อง
        $this->db->where('complain_id', $complain_id);
        $complain = $this->db->get('tbl_complain')->row();

        if (!$complain) {
            show_404();
            return;
        }

        $data['complain'] = $complain;
        $data['user_details'] = $this->get_complain_user_details($complain);
        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'รายละเอียดเรื่องร้องเรียน #' . $complain_id;
        $data['tenant_code'] = $this->tenant_code;

        // ✅ แก้ไข: ดึงประวัติการดำเนินงาน (ไม่รวมรูปภาพก่อน)
        $this->db->select('*');
        $this->db->from('tbl_complain_detail');
        $this->db->where('complain_detail_case_id', $complain_id);
        $this->db->order_by('complain_detail_datesave', 'ASC');
        $complain_details = $this->db->get()->result();

        // ✅ แก้ไข: ดึงรูปภาพแยกต่างหากสำหรับแต่ละ detail
        foreach ($complain_details as $detail) {
            $detail->status_images = [];

            // ตรวจสอบว่ามีตาราง tbl_complain_status_images หรือไม่
            if ($this->db->table_exists('tbl_complain_status_images')) {
                $this->db->select('*');
                $this->db->from('tbl_complain_status_images');
                $this->db->where('complain_detail_id', $detail->complain_detail_id);
                $this->db->order_by('uploaded_at', 'ASC');
                $status_images = $this->db->get()->result();

                if ($status_images) {
                    $detail->status_images = $status_images;
                    log_message('info', '🖼️ Found ' . count($status_images) . ' status images for detail_id: ' . $detail->complain_detail_id);
                } else {
                    log_message('info', '📭 No status images found for detail_id: ' . $detail->complain_detail_id);
                }
            } else {
                log_message('warning', '⚠️ Table tbl_complain_status_images does not exist');
                // สร้างตารางอัตโนมัติ
                $this->create_status_images_table();
            }
        }

        $data['complain_details'] = $complain_details;

        // ดึงรูปภาพประกอบหลัก (ไม่เกี่ยวกับการอัปเดตสถานะ)
        $this->db->where('complain_img_ref_id', $complain_id);
        $data['complain_images'] = $this->db->get('tbl_complain_img')->result();

        // ✅ เพิ่ม: Debug ข้อมูลสำหรับตรวจสอบ
        if (ENVIRONMENT === 'development') {
            log_message('info', '🔍 Debug - Complain Details Count: ' . count($complain_details));
            foreach ($complain_details as $index => $detail) {
                log_message('info', "Detail #{$index}: ID={$detail->complain_detail_id}, Images=" . count($detail->status_images));
            }
        }

        // โหลด view พร้อมส่งข้อมูลที่ครบถ้วน
        $this->load->view('reports/header', $data);
        $this->load->view('reports/complain_detail', $data);
        $this->load->view('reports/footer');
    }

    // ✅ เพิ่ม: Method ตรวจสอบและแก้ไขปัญหารูปภาพ
    public function fix_status_images($complain_id = null)
    {
        // เฉพาะ system admin
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_404();
            return;
        }

        if ($complain_id) {
            echo "<h2>🔧 แก้ไขรูปภาพสถานะสำหรับ Complain #{$complain_id}</h2>";

            // ตรวจสอบตาราง
            if (!$this->db->table_exists('tbl_complain_status_images')) {
                echo "<p style='color: red;'>❌ ไม่พบตาราง tbl_complain_status_images</p>";
                echo "<p>🔧 กำลังสร้างตาราง...</p>";

                if ($this->create_status_images_table()) {
                    echo "<p style='color: green;'>✅ สร้างตารางสำเร็จ</p>";
                } else {
                    echo "<p style='color: red;'>❌ ไม่สามารถสร้างตารางได้</p>";
                    return;
                }
            } else {
                echo "<p style='color: green;'>✅ ตาราง tbl_complain_status_images พร้อมใช้งาน</p>";
            }

            // ตรวจสอบโฟลเดอร์
            $upload_path = './docs/complain/status/';
            if (!is_dir($upload_path)) {
                echo "<p style='color: orange;'>📁 กำลังสร้างโฟลเดอร์: {$upload_path}</p>";
                if (mkdir($upload_path, 0755, true)) {
                    echo "<p style='color: green;'>✅ สร้างโฟลเดอร์สำเร็จ</p>";
                } else {
                    echo "<p style='color: red;'>❌ ไม่สามารถสร้างโฟลเดอร์ได้</p>";
                }
            } else {
                echo "<p style='color: green;'>✅ โฟลเดอร์พร้อมใช้งาน: {$upload_path}</p>";
            }

            // ตรวจสอบข้อมูลใน complain_detail
            $this->db->where('complain_detail_case_id', $complain_id);
            $details = $this->db->get('tbl_complain_detail')->result();

            echo "<h3>📋 ข้อมูล Complain Details:</h3>";
            echo "<ul>";
            foreach ($details as $detail) {
                echo "<li>Detail ID: {$detail->complain_detail_id} | Status: {$detail->complain_detail_status} | Date: {$detail->complain_detail_datesave}</li>";

                // ตรวจสอบรูปภาพของ detail นี้
                $this->db->where('complain_detail_id', $detail->complain_detail_id);
                $images = $this->db->get('tbl_complain_status_images')->result();

                if ($images) {
                    echo "<ul>";
                    foreach ($images as $img) {
                        $file_path = $upload_path . $img->image_filename;
                        $file_exists = file_exists($file_path) ? '✅' : '❌';
                        echo "<li>{$file_exists} {$img->image_filename} ({$img->image_original_name})</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<ul><li>📭 ไม่มีรูปภาพ</li></ul>";
                }
            }
            echo "</ul>";

        } else {
            echo "<h2>🔧 เครื่องมือแก้ไขรูปภาพสถานะ</h2>";
            echo "<p>ใช้: <code>/System_reports/fix_status_images/[complain_id]</code></p>";
            echo "<p>ตัวอย่าง: <code>/System_reports/fix_status_images/68123456</code></p>";
        }
    }





    /**
     * ✅ เพิ่ม: ฟังก์ชันดึงข้อมูล user ตาม user_type
     */
    private function get_complain_user_details($complain)
    {
        $user_details = [
            'user_type_display' => 'ไม่ทราบ',
            'full_address' => null,
            'detailed_address' => null,
            'user_info' => null
        ];

        try {
            $user_type = $complain->complain_user_type ?? '';
            $user_id = $complain->complain_user_id ?? null;

            log_message('info', "Getting user details - Type: {$user_type}, ID: {$user_id}");

            switch ($user_type) {
                case 'public':
                    $user_details['user_type_display'] = 'สมาชิกสาธารณะ';

                    if (!empty($user_id)) {
                        // ✅ ดึงข้อมูลจาก tbl_member_public
                        $this->db->where('id', $user_id); // หรือ mp_id ตามที่เก็บใน complain_user_id
                        $public_user = $this->db->get('tbl_member_public')->row();

                        if ($public_user) {
                            $user_details['user_info'] = $public_user;

                            // ✅ สร้างที่อยู่เต็ม
                            $address_parts = array_filter([
                                $public_user->mp_address,
                                $public_user->mp_district ? 'ต.' . $public_user->mp_district : null,
                                $public_user->mp_amphoe ? 'อ.' . $public_user->mp_amphoe : null,
                                $public_user->mp_province ? 'จ.' . $public_user->mp_province : null,
                                $public_user->mp_zipcode ? $public_user->mp_zipcode : null
                            ]);

                            $user_details['full_address'] = implode(' ', $address_parts);
                            $user_details['detailed_address'] = [
                                'district' => $public_user->mp_district ?? '',
                                'amphoe' => $public_user->mp_amphoe ?? '',
                                'province' => $public_user->mp_province ?? '',
                                'zipcode' => $public_user->mp_zipcode ?? ''
                            ];

                            log_message('info', "Found public user: {$public_user->mp_fname} {$public_user->mp_lname}");
                        }
                    }
                    break;

                case 'staff':
                    $user_details['user_type_display'] = 'เจ้าหน้าที่';

                    if (!empty($user_id)) {
                        // ดึงข้อมูลจาก tbl_member
                        $this->db->where('m_id', $user_id);
                        $staff_user = $this->db->get('tbl_member')->row();

                        if ($staff_user) {
                            $user_details['user_info'] = $staff_user;
                            $user_details['full_address'] = 'ข้อมูลจากระบบเจ้าหน้าที่';
                            log_message('info', "Found staff user: {$staff_user->m_fname} {$staff_user->m_lname}");
                        }
                    }
                    break;

                case 'guest':
                    $user_details['user_type_display'] = 'ผู้เยี่ยมชม';

                    // ✅ ใช้ข้อมูลจาก complain table สำหรับ guest
                    if (!empty($complain->guest_district) || !empty($complain->guest_amphoe) || !empty($complain->guest_province)) {
                        $address_parts = array_filter([
                            $complain->guest_district && $complain->guest_district !== 'ไม่ระบุ' ? 'ต.' . $complain->guest_district : null,
                            $complain->guest_amphoe && $complain->guest_amphoe !== 'ไม่ระบุ' ? 'อ.' . $complain->guest_amphoe : null,
                            $complain->guest_province && $complain->guest_province !== 'ไม่ระบุ' ? 'จ.' . $complain->guest_province : null,
                            $complain->guest_zipcode && $complain->guest_zipcode !== '00000' ? $complain->guest_zipcode : null
                        ]);

                        $user_details['detailed_address'] = [
                            'district' => $complain->guest_district !== 'ไม่ระบุ' ? $complain->guest_district : '',
                            'amphoe' => $complain->guest_amphoe !== 'ไม่ระบุ' ? $complain->guest_amphoe : '',
                            'province' => $complain->guest_province !== 'ไม่ระบุ' ? $complain->guest_province : '',
                            'zipcode' => $complain->guest_zipcode !== '00000' ? $complain->guest_zipcode : ''
                        ];

                        if (!empty($address_parts)) {
                            $user_details['full_address'] = implode(' ', $address_parts);
                        }
                    }
                    break;

                case 'anonymous':
                    $user_details['user_type_display'] = 'ไม่ระบุตัวตน';

                    // ✅ ใช้ข้อมูลจาก complain table สำหรับ anonymous
                    if (!empty($complain->guest_district) || !empty($complain->guest_amphoe) || !empty($complain->guest_province)) {
                        $address_parts = array_filter([
                            $complain->guest_district && $complain->guest_district !== 'ไม่ระบุ' ? 'ต.' . $complain->guest_district : null,
                            $complain->guest_amphoe && $complain->guest_amphoe !== 'ไม่ระบุ' ? 'อ.' . $complain->guest_amphoe : null,
                            $complain->guest_province && $complain->guest_province !== 'ไม่ระบุ' ? 'จ.' . $complain->guest_province : null,
                            $complain->guest_zipcode && $complain->guest_zipcode !== '00000' ? $complain->guest_zipcode : null
                        ]);

                        $user_details['detailed_address'] = [
                            'district' => $complain->guest_district !== 'ไม่ระบุ' ? $complain->guest_district : '',
                            'amphoe' => $complain->guest_amphoe !== 'ไม่ระบุ' ? $complain->guest_amphoe : '',
                            'province' => $complain->guest_province !== 'ไม่ระบุ' ? $complain->guest_province : '',
                            'zipcode' => $complain->guest_zipcode !== '00000' ? $complain->guest_zipcode : ''
                        ];

                        if (!empty($address_parts)) {
                            $user_details['full_address'] = implode(' ', $address_parts);
                        }
                    }
                    break;

                default:
                    $user_details['user_type_display'] = 'ไม่ทราบประเภท';
                    log_message('warning', "Unknown user type: {$user_type}");
                    break;
            }

            log_message('info', "User details prepared: " . json_encode($user_details));

        } catch (Exception $e) {
            log_message('error', 'Error getting user details: ' . $e->getMessage());
        }

        return $user_details;
    }




    public function delete_complain()
    {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // ✅ ตรวจสอบสิทธิ์การลบ
        if (!$this->can_delete()) {
            $permissions = $this->get_user_permissions_for_view();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์ในการลบข้อมูล - เฉพาะ System Admin และ Super Admin เท่านั้น',
                    'reason' => $permissions['reason'] ?? 'ไม่มีสิทธิ์',
                    'user_role' => $permissions['user_role'] ?? 'ไม่ระบุ',
                    'required_roles' => ['System Admin', 'Super Admin']
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบการ login ของ staff
        if (!$this->session->userdata('m_id')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าใช้งาน'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        try {
            $complain_id = $this->input->post('complain_id');

            if (empty($complain_id)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบหมายเลขเรื่องร้องเรียน'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ตรวจสอบว่าเรื่องร้องเรียนมีอยู่จริง
            $this->db->where('complain_id', $complain_id);
            $complain = $this->db->get('tbl_complain')->row();

            if (!$complain) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบเรื่องร้องเรียนที่ระบุ'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // เริ่ม transaction
            $this->db->trans_start();

            // ลบรูปภาพประกอบ (ถ้ามี)
            $images = $this->db->get_where('tbl_complain_img', ['complain_img_ref_id' => $complain_id])->result();
            foreach ($images as $image) {
                $image_path = FCPATH . 'docs/complain/' . $image->complain_img_img;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // ลบข้อมูลในตาราง
            $this->db->where('complain_img_ref_id', $complain_id);
            $this->db->delete('tbl_complain_img');

            $this->db->where('complain_detail_case_id', $complain_id);
            $this->db->delete('tbl_complain_detail');

            $this->db->where('complain_id', $complain_id);
            $this->db->delete('tbl_complain');

            // ตรวจสอบ transaction
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            $this->db->trans_commit();

            // Log การทำงาน
            $staff_name = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
            $permissions = $this->get_user_permissions_for_view();

            log_message('info', "Staff {$staff_name} ({$permissions['user_role']}) deleted complain {$complain_id}");

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'ลบเรื่องร้องเรียนเรียบร้อยแล้ว',
                    'deleted_by' => $staff_name,
                    'user_role' => $permissions['user_role']
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->trans_rollback();

            log_message('error', 'Error deleting complain: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }





    private function create_status_update_notification($complain, $new_status, $staff_name)
    {
        try {
            // ตรวจสอบว่ามี Notification library หรือไม่
            if (!class_exists('Notification_lib')) {
                // ลองโหลด library
                if (file_exists(APPPATH . 'libraries/Notification_lib.php')) {
                    $this->load->library('notification_lib');
                } else {
                    log_message('info', 'Notification_lib not found, skipping notification');
                    return;
                }
            }

            // สร้างข้อความแจ้งเตือน
            $title = 'อัพเดทสถานะเรื่องร้องเรียน #' . $complain->complain_id;
            $message = "เรื่อง: {$complain->complain_topic}\nสถานะใหม่: {$new_status}\nโดย: {$staff_name}";

            // ส่งการแจ้งเตือนให้ public user (ถ้าเป็นสมาชิก)
            if (!empty($complain->complain_user_id) && $complain->complain_user_id != '0') {
                $this->notification_lib->create_custom_notification(
                    'complain_status_update',
                    $title,
                    $message,
                    'public',
                    [
                        'priority' => 'normal',
                        'icon' => 'fas fa-info-circle',
                        'url' => site_url('Pages/follow_complain?auto_search=' . $complain->complain_id),
                        'target_user_id' => $complain->complain_user_id,
                        'data' => [
                            'complain_id' => $complain->complain_id,
                            'old_status' => $complain->complain_status,
                            'new_status' => $new_status,
                            'updated_by' => $staff_name
                        ]
                    ]
                );
            }

            log_message('info', "Status update notification sent for complain {$complain->complain_id}");

        } catch (Exception $e) {
            log_message('error', 'Failed to create status update notification: ' . $e->getMessage());
            // ไม่ให้ error ของ notification ขัดขวางการอัพเดทสถานะ
        }
    }






    private function is_system_admin()
    {
        $member_id = $this->session->userdata('m_id');

        // ดึงข้อมูลสมาชิก
        $member = $this->db->select('ref_pid, m_system')
            ->from('tbl_member')
            ->where('m_id', $member_id)
            ->get()
            ->row();

        if (!$member) {
            return false;
        }

        // เช็คว่าเป็น system_admin หรือ ref_pid = 1 (System Admin)
        return ($member->m_system === 'system_admin' || $member->ref_pid == 1);
    }

    /**
     * ✅ ฟังก์ชันอัปเดตข้อมูลพื้นที่จัดเก็บอัตโนมัติ
     */
    private function auto_update_storage_data()
    {
        try {
            $last_update = $this->get_last_storage_update();
            $current_time = time();
            $update_interval = 1800;

            if (!$last_update || ($current_time - strtotime($last_update)) > $update_interval) {
                $this->Storage_updater_model->update_storage_usage();

                // ตรวจสอบและส่งการแจ้งเตือน Storage
                $storage_info = $this->Reports_model->get_storage_detailed_report();
                $percentage = $storage_info['percentage_used'] ?? 0;

                // แจ้งเตือนเมื่อใช้งานเกิน 80%
                if ($percentage >= 80) {
                    $this->Notification_lib->storage_warning(
                        $percentage,
                        $storage_info['server_current'],
                        $storage_info['server_storage']
                    );
                }

                error_log("Storage data auto-updated at " . date('Y-m-d H:i:s'));
            }
        } catch (Exception $e) {
            error_log("Auto storage update failed: " . $e->getMessage());
        }
    }


    /**
     * ✅ ดึงเวลาอัปเดตล่าสุด
     */
    private function get_last_storage_update()
    {
        // ตรวจสอบจาก tbl_storage_history
        if ($this->db->table_exists('tbl_storage_history')) {
            $latest = $this->db->select('created_at')
                ->from('tbl_storage_history')
                ->order_by('created_at', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($latest) {
                return $latest->created_at;
            }
        }

        // ถ้าไม่มี history ให้ดูจาก tbl_server
        $server = $this->db->get('tbl_server')->row();
        return $server && isset($server->server_updated) ? $server->server_updated : null;
    }

    /**
     * รายงานพื้นที่จัดเก็บข้อมูล
     */
    public function storage()
    {
        // ✅ อัปเดตข้อมูลก่อนแสดงหน้ารายงาน
        $this->auto_update_storage_data();

        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'รายงานพื้นที่จัดเก็บข้อมูล';
        $data['tenant_code'] = $this->tenant_code;

        // ดึงข้อมูลการใช้พื้นที่จัดเก็บ
        $data['storage_info'] = $this->Reports_model->get_storage_detailed_report();
        $data['storage_history'] = $this->Reports_model->get_storage_usage_history();
        $data['storage_by_type'] = $this->Reports_model->get_storage_usage_by_file_type();
        $data['storage_trends'] = $this->Reports_model->get_storage_trends();

        // ดึงข้อมูลจำนวนไฟล์แต่ละประเภท
        $data['file_stats'] = $this->Reports_model->get_file_statistics();

        // ใช้ reports header/footer แทน
        $this->load->view('reports/header', $data);
        $this->load->view('reports/storage', $data);
        $this->load->view('reports/footer');
    }

    /**
     * รายงานเรื่องร้องเรียน
     */
    public function complain()
    {
        // กำหนดการแบ่งหน้า
        $config['base_url'] = site_url('System_reports/complain');
        $config['total_rows'] = $this->Reports_model->count_complains();
        $config['per_page'] = 20;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        // การออกแบบ pagination
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'หน้าแรก';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'หน้าสุดท้าย';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'ถัดไป';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'ก่อนหน้า';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        // ดึงข้อมูลตามการกรอง
        $filters = $this->input->get();
        $page = $this->input->get('page') ? ($this->input->get('page') - 1) * $config['per_page'] : 0;

        $data['user_info'] = $this->get_user_info();

        // 🆕 เพิ่มข้อมูลสิทธิ์ผู้ใช้
        $data['user_permissions'] = $this->get_user_permissions_for_view();

        $data['page_title'] = 'รายงานเรื่องร้องเรียน';
        $data['tenant_code'] = $this->tenant_code;
        $data['complains'] = $this->Reports_model->get_complains_with_details($config['per_page'], $page, $filters);
        $data['complain_summary'] = $this->Reports_model->get_complain_summary();
        $data['complain_stats'] = $this->Reports_model->get_complain_statistics();
        $data['complain_trends'] = $this->Reports_model->get_complain_trends();

        // สร้าง pagination links
        $data['pagination'] = $this->pagination->create_links();
        $data['current_page'] = $this->input->get('page') ?: 1;
        $data['total_rows'] = $config['total_rows'];
        $data['per_page'] = $config['per_page'];

        // ข้อมูลสำหรับ filters
        $data['status_options'] = $this->Reports_model->get_complain_status_options();
        $data['type_options'] = $this->Reports_model->get_complain_type_options();
        $data['filters'] = $filters;

        $data['pending_complains'] = $this->Reports_model->get_pending_complains_for_alerts();
        $data['alerts_summary'] = $this->Reports_model->get_case_alerts_summary();

        // ใช้ reports header/footer แทน
        $this->load->view('reports/header', $data);
        $data['view_file'] = 'reports/complain'; // เพิ่มตัวแปรนี้
        $this->load->view('reports/complain', $data);
        $this->load->view('reports/footer');
    }

    /**
     * 🔄 สถิติการใช้งานเว็บไซต์ - ปรับปรุงให้รองรับ tenant
     */
    public function website_stats()
    {
        try {
            // โหลด External_stats_model
            $this->load->model('External_stats_model');

            // ตรวจสอบการเชื่อมต่อและ tenant code
            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            $current_prefix = $this->External_stats_model->get_current_table_prefix();

            // 🆕 เพิ่มการรับ period parameter
            $selected_period = $this->input->get('period') ?: '7days';

            log_message('info', 'Website Stats - Requested Tenant: ' . $this->tenant_code .
                ', Resolved Tenant: ' . $current_tenant .
                ', Table Prefix: ' . $current_prefix .
                ', Selected Period: ' . $selected_period);

            // ดึงข้อมูลสถิติ
            $data['page_title'] = 'สถิติการใช้งานเว็บไซต์';
            $data['user_info'] = $this->get_user_info();
            $data['tenant_code'] = $this->tenant_code;
            $data['current_domain'] = $this->current_domain;
            $data['selected_period'] = $selected_period; // 🆕 เพิ่มบรรทัดนี้

            // ดึงข้อมูลจาก External_stats_model
            $data['stats_summary'] = $this->External_stats_model->get_stats_summary($selected_period);
            $data['top_domains'] = $this->External_stats_model->get_top_domains(10, $selected_period);
            $data['daily_stats'] = $this->External_stats_model->get_daily_stats($selected_period);
            $data['device_stats'] = $this->External_stats_model->get_device_summary();
            $data['platform_stats'] = $this->External_stats_model->get_platform_summary();
            $data['hourly_stats'] = $this->External_stats_model->get_hourly_visits();
            $data['browser_stats'] = $this->External_stats_model->get_browser_stats();
            $data['country_stats'] = $this->External_stats_model->get_country_stats();

            // ข้อมูล debug
            $data['debug_info'] = [
                'requested_tenant' => $this->tenant_code,
                'resolved_tenant' => $current_tenant,
                'table_prefix' => $current_prefix,
                'connection_status' => !empty($current_tenant) ? 'Connected' : 'Failed',
                'data_found' => !empty($data['stats_summary']['total_pageviews']),
                'current_domain' => $this->current_domain,
                'tenant_code' => $this->tenant_code,
                'external_db_connected' => !empty($current_tenant) ? 'Yes' : 'No'
            ];

            // 🆕 เพิ่มข้อมูล debug connection สำหรับ system admin
            $data['is_system_admin'] = $this->is_system_admin();

            $this->load->view('reports/header', $data);
            $this->load->view('reports/website_stats', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Website Stats Error for tenant ' . $this->tenant_code . ': ' . $e->getMessage());

            // แสดงหน้า error พร้อมข้อมูล debug
            $data['page_title'] = 'สถิติการใช้งานเว็บไซต์ - ข้อผิดพลาด';
            $data['user_info'] = $this->get_user_info();
            $data['error_message'] = 'ไม่สามารถดึงข้อมูลสถิติได้: ' . $e->getMessage();
            $data['tenant_code'] = $this->tenant_code;
            $data['current_domain'] = $this->current_domain;
            $data['selected_period'] = '7days'; // 🆕 เพิ่มบรรทัดนี้สำหรับ error case
            $data['debug_info'] = [
                'tenant_code' => $this->tenant_code,
                'current_domain' => $this->current_domain,
                'error_details' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];

            // 🆕 เพิ่มข้อมูล debug connection สำหรับ system admin แม้เกิด error
            $data['debug_connection_info'] = $this->get_debug_connection_info();
            $data['is_system_admin'] = $this->is_system_admin();

            $this->load->view('reports/header', $data);
            $this->load->view('reports/website_stats_error', $data);
            $this->load->view('reports/footer');
        }
    }




    public function api_check_permissions()
    {
        header('Content-Type: application/json');

        if (!$this->session->userdata('m_id')) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่ได้เข้าสู่ระบบ',
                'permissions' => [
                    'can_view_reports' => false,
                    'can_manage_status' => false,
                    'can_delete' => false
                ]
            ]);
            return;
        }

        $permissions = $this->get_user_permissions_for_view();

        echo json_encode([
            'success' => true,
            'permissions' => [
                'can_view_reports' => $permissions['can_view_reports'],
                'can_manage_status' => $permissions['can_manage_status'],
                'can_delete' => $permissions['can_delete'],
                'user_role' => $permissions['user_role'],
                'position_id' => $permissions['position_id'],
                'reason' => $permissions['reason']
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }







    /**
     * ✅ ส่งออกรายงาน Alerts เป็น Excel/CSV
     */
    public function export_alerts_excel()
    {
        try {
            // รับข้อมูลจาก POST
            $alert_data_json = $this->input->post('alert_data');

            if (empty($alert_data_json)) {
                show_error('ไม่พบข้อมูลสำหรับส่งออก');
            }

            $alert_data = json_decode($alert_data_json, true);

            if (!$alert_data) {
                show_error('ข้อมูลไม่ถูกต้อง');
            }

            // สร้างชื่อไฟล์
            $filename = 'รายงาน_Case_ค้างนาน_' . date('Y-m-d_His') . '.csv';

            // ส่งออกเป็น CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF"); // BOM for UTF-8

            // Header
            fputcsv($output, ['รายงาน Case ที่ไม่มีการอัพเดท'], ',', '"');
            fputcsv($output, ['Tenant Code', $this->tenant_code], ',', '"');
            fputcsv($output, ['วันที่ส่งออก', $alert_data['export_date']], ',', '"');
            fputcsv($output, ['จำนวน Case รวม', $alert_data['total']], ',', '"');
            fputcsv($output, [''], ',', '"');

            // Critical Cases (14+ วัน)
            if (!empty($alert_data['critical'])) {
                fputcsv($output, ['=== Case ค้าง 14+ วัน (วิกฤติ) ==='], ',', '"');
                fputcsv($output, ['รหัส Case', 'หัวข้อ', 'จำนวนวันที่ค้าง', 'สถานะปัจจุบัน', 'วันที่แจ้ง'], ',', '"');

                foreach ($alert_data['critical'] as $case) {
                    fputcsv($output, [
                        '#' . $case['id'],
                        $case['topic'],
                        $case['days'] . ' วัน',
                        $case['status'],
                        date('d/m/Y', strtotime($case['date']))
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // Danger Cases (7-13 วัน)
            if (!empty($alert_data['danger'])) {
                fputcsv($output, ['=== Case ค้าง 7-13 วัน (เร่งด่วน) ==='], ',', '"');
                fputcsv($output, ['รหัส Case', 'หัวข้อ', 'จำนวนวันที่ค้าง', 'สถานะปัจจุบัน', 'วันที่แจ้ง'], ',', '"');

                foreach ($alert_data['danger'] as $case) {
                    fputcsv($output, [
                        '#' . $case['id'],
                        $case['topic'],
                        $case['days'] . ' วัน',
                        $case['status'],
                        date('d/m/Y', strtotime($case['date']))
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // Warning Cases (3-6 วัน)
            if (!empty($alert_data['warning'])) {
                fputcsv($output, ['=== Case ค้าง 3-6 วัน (ติดตาม) ==='], ',', '"');
                fputcsv($output, ['รหัส Case', 'หัวข้อ', 'จำนวนวันที่ค้าง', 'สถานะปัจจุบัน', 'วันที่แจ้ง'], ',', '"');

                foreach ($alert_data['warning'] as $case) {
                    fputcsv($output, [
                        '#' . $case['id'],
                        $case['topic'],
                        $case['days'] . ' วัน',
                        $case['status'],
                        date('d/m/Y', strtotime($case['date']))
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // สรุปและคำแนะนำ
            fputcsv($output, ['=== สรุปและคำแนะนำ ==='], ',', '"');
            fputcsv($output, ['จำนวน Case วิกฤติ (14+ วัน)', count($alert_data['critical'])], ',', '"');
            fputcsv($output, ['จำนวน Case เร่งด่วน (7-13 วัน)', count($alert_data['danger'])], ',', '"');
            fputcsv($output, ['จำนวน Case ติดตาม (3-6 วัน)', count($alert_data['warning'])], ',', '"');
            fputcsv($output, [''], ',', '"');
            fputcsv($output, ['คำแนะนำ:'], ',', '"');
            fputcsv($output, ['1. ควรดำเนินการ Case วิกฤติก่อน'], ',', '"');
            fputcsv($output, ['2. ติดตาม Case เร่งด่วนอย่างใกล้ชิด'], ',', '"');
            fputcsv($output, ['3. วางแผนการทำงานให้มีประสิทธิภาพ'], ',', '"');

            fclose($output);

        } catch (Exception $e) {
            log_message('error', 'Export Alerts Excel Error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งออกไฟล์: ' . $e->getMessage());
        }
    }



    /**
     * ✅ Export Complain Detail - แก้ไขให้มี Preview ก่อน Export
     */
    public function export_complain_detail($complain_id = null)
    {
        try {
            // รับข้อมูลจาก URL parameter หรือ POST
            if (!$complain_id) {
                $complain_id = $this->uri->segment(3);
            }

            $export_type = $this->input->get('type') ?? $this->input->post('export_type') ?? 'preview';

            if (empty($complain_id)) {
                show_error('ไม่พบ ID เรื่องร้องเรียน', 400);
                return;
            }

            // ดึงข้อมูลจากฐานข้อมูลโดยใช้ชื่อตารางที่ถูกต้อง
            $this->db->where('complain_id', $complain_id);
            $query = $this->db->get('tbl_complain');  // แก้ไขชื่อตารางที่นี่

            if ($query->num_rows() == 0) {
                show_error('ไม่พบเรื่องร้องเรียน ID: ' . $complain_id, 404);
                return;
            }

            $complain = $query->row();

            // ดึงข้อมูล detail และรูปภาพ
            $this->db->where('complain_detail_case_id', $complain_id);
            $this->db->order_by('complain_detail_datesave', 'ASC');
            $complain_details = $this->db->get('tbl_complain_detail')->result();

            $this->db->where('complain_img_ref_id', $complain_id);
            $complain_images = $this->db->get('tbl_complain_img')->result();

            // เตรียมข้อมูลสำหรับ view
            $data = array(
                'page_title' => 'รายงานเรื่องร้องเรียน #' . $complain->complain_id,
                'complain_data' => array(
                    'complain_id' => $complain->complain_id,
                    'complain_topic' => $complain->complain_topic ?? '',
                    'complain_status' => $complain->complain_status ?? '',
                    'complain_by' => $complain->complain_by ?? '',
                    'complain_phone' => $complain->complain_phone ?? '',
                    'complain_detail' => $complain->complain_detail ?? '',
                    'complain_datesave' => $complain->complain_datesave ?? '',
                    'complain_type' => $complain->complain_type ?? '',
                    'complain_email' => $complain->complain_email ?? '',
                    'complain_address' => $complain->complain_address ?? ''
                ),
                'complain_details' => $complain_details,
                'complain_images' => $complain_images,
                'export_date' => date('d/m/Y H:i:s'),
                'tenant_code' => $this->session->userdata('tenant_code') ?? 'system',
                'tenant_name' => $this->session->userdata('tenant_name') ?? 'ระบบจัดการเรื่องร้องเรียน',
                'filename' => 'complain_report_' . $complain->complain_id . '_' . date('YmdHis')
            );

            if ($export_type === 'preview' || empty($export_type)) {
                // แสดงหน้า preview
                $this->load->view('reports/complain_preview', $data);
            } elseif ($export_type === 'pdf') {
                // ส่งออก PDF
                $this->export_complain_pdf($data);
            } elseif ($export_type === 'csv') {
                // ส่งออก CSV
                $this->export_complain_csv($data);
            } else {
                show_error('รูปแบบการส่งออกไม่ถูกต้อง', 400);
            }

        } catch (Exception $e) {
            log_message('error', 'Export complain error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งออกรายงาน: ' . $e->getMessage(), 500);
        }
    }






    private function export_complain_csv($data)
    {
        try {
            $complain = $data['complain_data'];

            // เตรียมข้อมูล CSV
            $csv_data = array(
                array('ฟิลด์', 'ข้อมูล'),
                array('หมายเลขเรื่อง', '#' . $complain['complain_id']),
                array('หัวข้อ', $complain['complain_topic']),
                array('ประเภท', $complain['complain_type']),
                array('สถานะ', $complain['complain_status']),
                array('ผู้แจ้ง', $complain['complain_by']),
                array('เบอร์ติดต่อ', $complain['complain_phone']),
                array('อีเมล', $complain['complain_email'] ?? ''),
                array('ที่อยู่', $complain['complain_address'] ?? ''),
                array('วันที่แจ้ง', $complain['complain_datesave']),
                array('รายละเอียด', '"' . str_replace('"', '""', $complain['complain_detail']) . '"'),
                array('วันที่ส่งออก', $data['export_date'])
            );

            $filename = 'complain_report_' . $complain['complain_id'] . '_' . date('YmdHis') . '.csv';

            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // เพิ่ม BOM สำหรับ UTF-8
            echo "\xEF\xBB\xBF";

            $output = fopen('php://output', 'w');

            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
            exit;

        } catch (Exception $e) {
            log_message('error', 'CSV export error: ' . $e->getMessage());
            show_error('ไม่สามารถสร้างไฟล์ CSV ได้: ' . $e->getMessage(), 500);
        }
    }



    private function export_complain_pdf($data)
    {
        try {
            // สร้าง HTML สำหรับ PDF (ใช้ view เดียวกับ preview)
            $html = $this->load->view('reports/complain_preview', $data, true);

            // ลบส่วนที่ไม่ต้องการใน PDF
            $html = str_replace('no-print', 'print-only', $html);

            // ใช้ mPDF หรือ TCPDF (ถ้ามี)
            if (class_exists('mPDF')) {
                require_once APPPATH . 'third_party/mpdf/vendor/autoload.php';

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'margin_left' => 15,
                    'margin_right' => 15,
                    'margin_top' => 16,
                    'margin_bottom' => 16,
                ]);

                $mpdf->WriteHTML($html);
                $filename = 'complain_report_' . $data['complain_data']['complain_id'] . '_' . date('YmdHis') . '.pdf';
                $mpdf->Output($filename, 'D');
            } else {
                // Fallback: ส่งกลับเป็น HTML สำหรับพิมพ์
                echo $html;
                echo '<script>window.print();</script>';
            }

        } catch (Exception $e) {
            log_message('error', 'PDF export error: ' . $e->getMessage());
            show_error('ไม่สามารถสร้างไฟล์ PDF ได้: ' . $e->getMessage(), 500);
        }
    }




    /**
     * ✅ ส่งออกเป็น PDF จริง
     */
    private function export_complain_detail_pdf($data, $filename)
    {
        try {
            // ตรวจสอบ mPDF library
            $mpdf_path = APPPATH . 'third_party/mpdf/vendor/autoload.php';
            if (!file_exists($mpdf_path)) {
                log_message('error', 'mPDF library not found at: ' . $mpdf_path);

                // Fallback เป็น CSV
                $csv_filename = str_replace('.pdf', '.csv', $filename);
                $this->export_complain_detail_csv($data, $csv_filename);
                return;
            }

            require_once($mpdf_path);

            // สร้าง HTML สำหรับ PDF
            $html = $this->generate_complain_pdf_html($data);

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 25,
                'margin_bottom' => 25,
                'default_font' => 'dejavusans',
                'default_font_size' => 12,
                'tempDir' => sys_get_temp_dir()
            ]);

            $mpdf->WriteHTML($html);

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $mpdf->Output($filename, 'D');

        } catch (Exception $e) {
            log_message('error', 'Complain PDF export error: ' . $e->getMessage());

            // Fallback เป็น CSV
            $csv_filename = str_replace('.pdf', '.csv', $filename);
            $this->export_complain_detail_csv($data, $csv_filename);
        }
    }

    /**
     * ✅ ส่งออกเป็น CSV
     */
    private function export_complain_detail_csv($data, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF"); // BOM for UTF-8

        $complain_data = $data['complain_data'];

        // Header
        fputcsv($output, ['รายละเอียดเรื่องร้องเรียน #' . $complain_data['complain_id']], ',', '"');
        fputcsv($output, ['วันที่ส่งออก', $data['export_date']], ',', '"');
        fputcsv($output, ['Tenant Code', $data['tenant_code']], ',', '"');
        fputcsv($output, [''], ',', '"');

        // ข้อมูลเบื้องต้น
        fputcsv($output, ['=== ข้อมูลเบื้องต้น ==='], ',', '"');
        fputcsv($output, ['หมายเลขเรื่อง', '#' . $complain_data['complain_id']], ',', '"');
        fputcsv($output, ['หัวข้อ', $complain_data['complain_topic']], ',', '"');
        fputcsv($output, ['สถานะ', $complain_data['complain_status']], ',', '"');
        fputcsv($output, ['ผู้แจ้ง', $complain_data['complain_by']], ',', '"');
        fputcsv($output, ['เบอร์ติดต่อ', $complain_data['complain_phone']], ',', '"');
        fputcsv($output, ['วันที่แจ้ง', date('d/m/Y H:i', strtotime($complain_data['complain_datesave']))], ',', '"');
        fputcsv($output, ['รายละเอียด', $complain_data['complain_detail']], ',', '"');

        fclose($output);
    }

    /**
     * ✅ สร้าง HTML สำหรับ PDF
     */
    private function generate_complain_pdf_html($data)
    {
        $complain_data = $data['complain_data'];
        $tenant_name = htmlspecialchars($data['tenant_name'], ENT_QUOTES, 'UTF-8');
        $export_date = $data['export_date'];

        $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { 
                font-family: "DejaVu Sans", sans-serif; 
                font-size: 12px; 
                line-height: 1.4;
                color: #333;
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 2px solid #667eea;
                padding-bottom: 15px;
            }
            .title { 
                font-size: 18px; 
                font-weight: bold; 
                color: #667eea;
                margin-bottom: 8px;
            }
            .subtitle { 
                font-size: 14px; 
                color: #666; 
                margin: 3px 0;
            }
            .section {
                margin: 20px 0;
                background: #f9f9f9;
                padding: 15px;
                border-radius: 5px;
                border-left: 4px solid #667eea;
            }
            .section-title {
                font-size: 14px;
                font-weight: bold;
                color: #667eea;
                margin-bottom: 10px;
            }
            .info-row { 
                margin: 10px 0;
                padding: 5px 0;
                border-bottom: 1px dotted #ccc;
            }
            .info-label { 
                font-weight: bold;
                color: #555;
                display: inline-block;
                width: 120px;
            }
            .info-value { 
                color: #333;
            }
            .status {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 15px;
                background: #667eea;
                color: white;
                font-size: 11px;
                font-weight: bold;
            }
            .footer {
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #ccc;
                text-align: center;
                font-size: 10px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">รายละเอียดเรื่องร้องเรียน #' . htmlspecialchars($complain_data['complain_id']) . '</div>
            <div class="subtitle">' . $tenant_name . '</div>
            <div class="subtitle">วันที่ออกรายงาน: ' . $export_date . '</div>
        </div>

        <div class="section">
            <div class="section-title">ข้อมูลเบื้องต้น</div>
            <div class="info-row">
                <span class="info-label">หัวข้อ:</span>
                <span class="info-value">' . htmlspecialchars($complain_data['complain_topic']) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">สถานะ:</span>
                <span class="info-value status">' . htmlspecialchars($complain_data['complain_status']) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">ผู้แจ้ง:</span>
                <span class="info-value">' . htmlspecialchars($complain_data['complain_by']) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">เบอร์ติดต่อ:</span>
                <span class="info-value">' . htmlspecialchars($complain_data['complain_phone']) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">วันที่แจ้ง:</span>
                <span class="info-value">' . date('d/m/Y H:i', strtotime($complain_data['complain_datesave'])) . '</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">รายละเอียด</div>
            <div style="padding: 10px; background: white; border-radius: 3px;">
                ' . nl2br(htmlspecialchars($complain_data['complain_detail'])) . '
            </div>
        </div>
        
        <div class="footer">
            รายงานนี้สร้างโดยระบบอัตโนมัติ<br>
            ' . $tenant_name . ' | ' . $export_date . '
        </div>
    </body>
    </html>';

        return $html;
    }

    /**
     * ✅ สร้างชื่อไฟล์
     */
    private function generate_complain_filename($complain_data, $export_type)
    {
        $complain_id = $complain_data['complain_id'] ?? 'unknown';
        $date_suffix = date('Y-m-d_His');

        $extension = ($export_type === 'pdf') ? 'pdf' : 'csv';

        return "เรื่องร้องเรียน_{$complain_id}_{$date_suffix}.{$extension}";
    }

    /**
     * ✅ AJAX export จาก preview
     */
    public function ajax_export_complain_from_preview()
    {
        try {
            $export_type = $this->input->post('export_type');
            $complain_data_json = $this->input->post('complain_data');

            if (empty($complain_data_json)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('success' => false, 'message' => 'ไม่พบข้อมูลเรื่องร้องเรียน')));
                return;
            }

            $complain_data = json_decode($complain_data_json, true);

            if ($complain_data === null) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง')));
                return;
            }

            $data = array(
                'page_title' => 'รายงานเรื่องร้องเรียน #' . $complain_data['complain_id'],
                'complain_data' => $complain_data,
                'export_date' => date('d/m/Y H:i:s'),
                'tenant_code' => $this->session->userdata('tenant_code') ?? 'system',
                'tenant_name' => $this->session->userdata('tenant_name') ?? 'ระบบจัดการเรื่องร้องเรียน'
            );

            if ($export_type === 'pdf') {
                $this->export_complain_pdf($data);
            } elseif ($export_type === 'csv') {
                $this->export_complain_csv($data);
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('success' => false, 'message' => 'รูปแบบการส่งออกไม่ถูกต้อง')));
            }

        } catch (Exception $e) {
            log_message('error', 'AJAX export error: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage())));
        }
    }






    /**
     * ✅ แสดง Print Preview สำหรับรายงาน Alerts
     */
    public function export_alerts_pdf()
    {
        try {
            // รับข้อมูลจาก POST
            $alert_data_json = $this->input->post('alert_data');

            if (empty($alert_data_json)) {
                show_error('ไม่พบข้อมูลสำหรับส่งออก');
            }

            $alert_data = json_decode($alert_data_json, true);

            if (!$alert_data) {
                show_error('ข้อมูลไม่ถูกต้อง');
            }

            // เตรียมข้อมูลสำหรับ preview
            $data = [
                'alert_data' => $alert_data,
                'tenant_code' => $this->tenant_code,
                'tenant_name' => $this->get_tenant_name(),
                'export_date' => $alert_data['export_date'],
                'page_title' => 'รายงาน Case ที่ไม่มีการอัพเดท',
                'total_alerts' => $alert_data['total'],
                'critical_cases' => $alert_data['critical'],
                'danger_cases' => $alert_data['danger'],
                'warning_cases' => $alert_data['warning']
            ];

            // โหลด view สำหรับ print preview
            $this->load->view('reports/alerts_preview', $data);

        } catch (Exception $e) {
            log_message('error', 'Export Alerts Preview Error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการแสดงรายงาน: ' . $e->getMessage());
        }
    }

    /**
     * ✅ เพิ่ม method สำหรับ export PDF จริง
     */
    public function export_alerts_pdf_download()
    {
        try {
            // รับข้อมูลจาก POST 
            $alert_data_json = $this->input->post('alert_data');

            if (empty($alert_data_json)) {
                show_error('ไม่พบข้อมูลสำหรับส่งออก');
            }

            $alert_data = json_decode($alert_data_json, true);

            // ตรวจสอบ mPDF
            $mpdf_path = APPPATH . 'third_party/mpdf/vendor/autoload.php';
            if (!file_exists($mpdf_path)) {
                // Fallback เป็น CSV
                $this->export_alerts_excel();
                return;
            }

            require_once($mpdf_path);

            // สร้าง HTML สำหรับ PDF
            $html = $this->generate_alerts_pdf_html($alert_data);

            // สร้าง PDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'default_font' => 'dejavusans',
                'default_font_size' => 12
            ]);

            $mpdf->WriteHTML($html);

            $filename = 'รายงาน_Case_ค้างนาน_' . date('Y-m-d_His') . '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $mpdf->Output($filename, 'D');

        } catch (Exception $e) {
            log_message('error', 'Export Alerts PDF Download Error: ' . $e->getMessage());
            $this->export_alerts_excel();
        }
    }

    /**
     * ✅ สร้าง HTML สำหรับ PDF รายงาน Alerts
     */
    private function generate_alerts_pdf_html($alert_data)
    {
        $tenant_name = htmlspecialchars($this->tenant_code ?: 'ระบบ', ENT_QUOTES, 'UTF-8');
        $export_date = $alert_data['export_date'];

        $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #333; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #dc2626; padding-bottom: 15px; }
            .title { font-size: 18px; font-weight: bold; color: #dc2626; margin-bottom: 8px; }
            .subtitle { font-size: 14px; color: #666; margin: 3px 0; }
            .summary-box { background: #fef2f2; border: 1px solid #fecaca; padding: 15px; margin: 15px 0; border-radius: 5px; }
            .section-title { font-size: 14px; font-weight: bold; color: #dc2626; margin: 20px 0 10px 0; border-bottom: 1px solid #dc2626; }
            .critical { color: #dc2626; }
            .danger { color: #f59e0b; }
            .warning { color: #10b981; }
            .case-item { margin: 10px 0; padding: 8px; background: #f9fafb; border-left: 4px solid #e5e7eb; }
            .case-item.critical { border-left-color: #dc2626; }
            .case-item.danger { border-left-color: #f59e0b; }
            .case-item.warning { border-left-color: #10b981; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">รายงาน Case ที่ไม่มีการอัพเดท</div>
            <div class="subtitle">' . $tenant_name . '</div>
            <div class="subtitle">วันที่ออกรายงาน: ' . $export_date . '</div>
        </div>
        
        <div class="summary-box">
            <h3 style="margin-top: 0; color: #dc2626;">สรุปภาพรวม</h3>
            <p><strong>จำนวน Case ทั้งหมดที่ค้าง:</strong> ' . $alert_data['total'] . ' รายการ</p>
            <p><strong class="critical">• Case วิกฤติ (14+ วัน):</strong> ' . count($alert_data['critical']) . ' รายการ</p>
            <p><strong class="danger">• Case เร่งด่วน (7-13 วัน):</strong> ' . count($alert_data['danger']) . ' รายการ</p>
            <p><strong class="warning">• Case ติดตาม (3-6 วัน):</strong> ' . count($alert_data['warning']) . ' รายการ</p>
        </div>';

        // Critical Cases
        if (!empty($alert_data['critical'])) {
            $html .= '<div class="section-title critical">Case วิกฤติ (ค้าง 14+ วัน)</div>';
            foreach ($alert_data['critical'] as $case) {
                $html .= '<div class="case-item critical">
                <strong>#' . htmlspecialchars($case['id']) . '</strong> - ' . htmlspecialchars($case['topic']) . '<br>
                <small>ค้าง ' . $case['days'] . ' วัน | สถานะ: ' . htmlspecialchars($case['status']) . ' | วันที่แจ้ง: ' . date('d/m/Y', strtotime($case['date'])) . '</small>
            </div>';
            }
        }

        // Danger Cases
        if (!empty($alert_data['danger'])) {
            $html .= '<div class="section-title danger">Case เร่งด่วน (ค้าง 7-13 วัน)</div>';
            foreach ($alert_data['danger'] as $case) {
                $html .= '<div class="case-item danger">
                <strong>#' . htmlspecialchars($case['id']) . '</strong> - ' . htmlspecialchars($case['topic']) . '<br>
                <small>ค้าง ' . $case['days'] . ' วัน | สถานะ: ' . htmlspecialchars($case['status']) . ' | วันที่แจ้ง: ' . date('d/m/Y', strtotime($case['date'])) . '</small>
            </div>';
            }
        }

        // Warning Cases
        if (!empty($alert_data['warning'])) {
            $html .= '<div class="section-title warning">Case ติดตาม (ค้าง 3-6 วัน)</div>';
            foreach ($alert_data['warning'] as $case) {
                $html .= '<div class="case-item warning">
                <strong>#' . htmlspecialchars($case['id']) . '</strong> - ' . htmlspecialchars($case['topic']) . '<br>
                <small>ค้าง ' . $case['days'] . ' วัน | สถานะ: ' . htmlspecialchars($case['status']) . ' | วันที่แจ้ง: ' . date('d/m/Y', strtotime($case['date'])) . '</small>
            </div>';
            }
        }

        // คำแนะนำ
        $html .= '<div class="section-title">คำแนะนำในการดำเนินงาน</div>
        <div style="margin: 15px 0;">
            <p><strong>1. Case วิกฤติ (14+ วัน):</strong> ต้องดำเนินการทันทีเป็นลำดับแรก</p>
            <p><strong>2. Case เร่งด่วน (7-13 วัน):</strong> วางแผนดำเนินการภายในสัปดาห์นี้</p>
            <p><strong>3. Case ติดตาม (3-6 วัน):</strong> ติดตามให้ไม่เลื่อนไปเป็น Case เร่งด่วน</p>
            <p><strong>4. การบริหารจัดการ:</strong> ควรมีการประชุมทีมเพื่อจัดลำดับความสำคัญ</p>
        </div>
        
        <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ccc; text-align: center; font-size: 10px; color: #666;">
            รายงานนี้สร้างโดยระบบอัตโนมัติ | ' . $tenant_name . ' | ' . $export_date . '
        </div>
    </body>
    </html>';

        return $html;
    }




    /**
     * 🆕 AJAX endpoint สำหรับรีเฟรช debug info
     */
    public function ajax_refresh_debug_info()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request() || !$this->is_system_admin()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
            return;
        }

        try {
            $debug_info = $this->get_debug_connection_info();

            echo json_encode([
                'success' => true,
                'data' => $debug_info,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Ajax Debug Refresh Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล debug: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * ✅ AJAX endpoint สำหรับ website stats - แก้ไขการจัดการ period และ date range
     */
    public function ajax_website_stats()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $this->load->model('External_stats_model');

            // ✅ รับ parameters จาก request
            $period = $this->input->get('period') ?: '7days';
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');
            $date_range = $this->input->get('dateRange');

            // ✅ กำหนด period ที่จะใช้ในการ query
            $query_period = $this->determine_query_period($period, $date_range, $start_date, $end_date);

            log_message('info', 'AJAX Website Stats - Original Period: ' . $period .
                ', Date Range: ' . $date_range .
                ', Query Period: ' . json_encode($query_period));

            // ตรวจสอบการเชื่อมต่อ
            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            $current_prefix = $this->External_stats_model->get_current_table_prefix();

            if (!$current_tenant) {
                echo json_encode([
                    'success' => false,
                    'error' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้',
                    'debug_info' => [
                        'tenant_code' => $this->tenant_code,
                        'current_domain' => $this->current_domain,
                        'resolved_tenant' => $current_tenant
                    ]
                ]);
                return;
            }

            // ✅ ดึงข้อมูลใหม่ด้วย period ที่แก้ไขแล้ว
            $response_data = [
                'success' => true,
                'data' => [
                    'stats_summary' => $this->External_stats_model->get_stats_summary($query_period),
                    'top_domains' => $this->External_stats_model->get_top_domains(20, $query_period),
                    'daily_stats' => $this->External_stats_model->get_daily_stats($query_period),
                    'device_stats' => $this->External_stats_model->get_device_summary(),
                    'platform_stats' => $this->External_stats_model->get_platform_summary(),
                    'hourly_stats' => $this->External_stats_model->get_hourly_visits(),
                    'browser_stats' => $this->External_stats_model->get_browser_stats(),
                    'country_stats' => $this->External_stats_model->get_country_stats()
                ],
                'period' => $period,
                'query_period' => $query_period,
                'tenant_info' => [
                    'requested' => $this->tenant_code,
                    'resolved' => $current_tenant,
                    'prefix' => $current_prefix,
                    'domain' => $this->current_domain
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            echo json_encode($response_data);

        } catch (Exception $e) {
            log_message('error', 'AJAX Website Stats Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage(),
                'debug_info' => [
                    'tenant_code' => $this->tenant_code,
                    'current_domain' => $this->current_domain,
                    'error_details' => $e->getMessage()
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * ✅ กำหนด period สำหรับ query ฐานข้อมูล
     */
    private function determine_query_period($period, $date_range = null, $start_date = null, $end_date = null)
    {
        // ✅ แก้ไข: ตรวจสอบและทำความสะอาดข้อมูล input
        log_message('debug', 'determine_query_period - Period: ' . json_encode($period) .
            ', Date Range: ' . $date_range .
            ', Start: ' . $start_date .
            ', End: ' . $end_date);

        // ถ้ามี custom date range และข้อมูลครบ
        if ($date_range === 'custom' && !empty($start_date) && !empty($end_date)) {
            return [
                'type' => 'custom',
                'start' => $this->parse_date_input($start_date),
                'end' => $this->parse_date_input($end_date)
            ];
        }

        // ถ้ามี date range อื่นๆ
        if (!empty($date_range)) {
            switch ($date_range) {
                case 'daily':
                    return '7days';
                case 'monthly':
                    return 'current_month';
                case 'custom':
                    // ถ้าเลือก custom แต่ไม่มีวันที่ ให้ fallback เป็น 7days
                    log_message('warning', 'Custom date range selected but dates are missing, using 7days');
                    return '7days';
                default:
                    return !empty($period) && is_string($period) ? $period : '7days';
            }
        }

        // ✅ แก้ไข: ตรวจสอบ period ให้แน่ใจว่าเป็น string
        if (is_string($period) && !empty($period)) {
            return $period;
        }

        // Fallback เป็น 7days
        log_message('warning', 'Invalid period format, using 7days fallback. Period: ' . json_encode($period));
        return '7days';
    }

    /**
     * ✅ แปลงรูปแบบวันที่จาก input
     */
    private function parse_date_input($date_input)
    {
        if (empty($date_input)) {
            return date('Y-m-d');
        }

        // รองรับทั้ง YYYY-MM-DD และ DD/MM/YYYY
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date_input)) {
            // YYYY-MM-DD format
            return date('Y-m-d', strtotime($date_input));
        } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date_input, $matches)) {
            // DD/MM/YYYY format
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            return $year . '-' . $month . '-' . $day;
        }

        // Fallback
        return date('Y-m-d', strtotime($date_input));
    }

    /**
     * ✅ ส่งออกรายงานสถิติเว็บไซต์แบบ Enhanced - แก้ไข PDF generation
     */
    public function export_website_stats_enhanced()
    {
        try {
            // รับข้อมูลจาก form
            $export_type = $this->input->post('type'); // pdf, csv, excel
            $date_range = $this->input->post('dateRange');
            $start_date = $this->input->post('startDate');
            $end_date = $this->input->post('endDate');
            $file_name = $this->input->post('fileName');
            $period = $this->input->post('period');

            log_message('info', 'Export Request - Type: ' . $export_type .
                ', Range: ' . $date_range .
                ', Start: ' . $start_date .
                ', End: ' . $end_date .
                ', Period: ' . $period);

            // ✅ แก้ไข: เพิ่มการตรวจสอบ library ก่อนดำเนินการ
            if ($export_type === 'pdf') {
                $mpdf_path = APPPATH . 'third_party/mpdf/vendor/autoload.php';
                if (!file_exists($mpdf_path)) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => 'ไม่พบ mPDF library กรุณาติดตั้ง mPDF หรือเลือกส่งออกเป็น CSV แทน',
                        'alternative' => 'กรุณาเลือก CSV หรือ Excel แทน',
                        'library_missing' => 'mPDF'
                    ]);
                    return;
                }
            }

            if ($export_type === 'excel') {
                if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => 'ไม่พบ PhpSpreadsheet library กรุณาติดตั้ง PhpSpreadsheet หรือเลือกส่งออกเป็น CSV แทน',
                        'alternative' => 'กรุณาเลือก CSV แทน',
                        'library_missing' => 'PhpSpreadsheet'
                    ]);
                    return;
                }
            }

            // ✅ Validation
            if (!$export_type || !in_array($export_type, ['pdf', 'csv', 'excel'])) {
                log_message('error', 'Invalid export type: ' . $export_type);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'ประเภทไฟล์ไม่ถูกต้อง รองรับเฉพาะ PDF, CSV และ Excel'
                ]);
                return;
            }

            // ดำเนินการส่งออกตามปกติ...
            $this->load->model('External_stats_model');

            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            if (!$current_tenant) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้ กรุณาตรวจสอบการตั้งค่า'
                ]);
                return;
            }

            // กำหนดช่วงเวลา
            $export_period = $this->determine_query_period($period, $date_range, $start_date, $end_date);

            // รับ options
            $options = $this->get_export_options();

            // ดึงข้อมูล
            $export_data = $this->gather_export_data($export_period, $options);

            if (empty($export_data) || empty($export_data['summary'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'ไม่พบข้อมูลสำหรับการส่งออก กรุณาตรวจสอบช่วงวันที่'
                ]);
                return;
            }

            // สร้างชื่อไฟล์
            $filename = $this->generate_export_filename($file_name, $export_type, $export_period);

            // เพิ่มข้อมูล metadata
            $export_data['period_info'] = $this->get_period_description($export_period);
            $export_data['export_date'] = date('d/m/Y H:i:s');
            $export_data['tenant_code'] = $this->tenant_code;

            // ✅ ส่งออกตามประเภทไฟล์ (ไม่มี fallback)
            switch ($export_type) {
                case 'pdf':
                    $this->export_stats_pdf_improved($export_data, $filename, $options);
                    break;
                case 'csv':
                    $this->export_stats_csv_improved($export_data, $filename);
                    break;
                case 'excel':
                    $this->export_stats_excel_improved($export_data, $filename, $options);
                    break;
                default:
                    throw new Exception('Unsupported export type: ' . $export_type);
            }

        } catch (Exception $e) {
            log_message('error', 'Export Error: ' . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'ไม่สามารถส่งออกรายงานได้: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'tenant_code' => $this->tenant_code ?? 'unknown',
                    'export_type' => $export_type ?? 'unknown'
                ]
            ]);
        }
    }
    /**
     * ✅ รวบรวมข้อมูลสำหรับส่งออก
     */
    private function gather_export_data($period, $options)
    {
        $data = [];

        try {
            log_message('info', 'Gathering export data for period: ' . json_encode($period));

            // ข้อมูลสรุปหลัก
            $summary = $this->External_stats_model->get_stats_summary($period);
            $data['summary'] = $summary;

            if (!$summary || !isset($summary['total_pageviews'])) {
                log_message('warning', 'No summary data available');
                $data['summary'] = [
                    'total_pageviews' => 0,
                    'total_visitors' => 0,
                    'total_domains' => 0,
                    'online_users' => 0,
                    'avg_pageviews_per_visitor' => 0
                ];
            }

            // ✅ แก้ไข: ใช้ชื่อ key เดียวกันทั้งในหน้าเว็บและ PDF
            if (isset($options['includeTopDomains']) && $options['includeTopDomains']) {
                $top_pages = $this->External_stats_model->get_top_domains(50, $period) ?: [];
                $data['top_domains'] = $top_pages; // ใช้ชื่อเดิมเพื่อความเข้ากันได้

                log_message('info', 'Top pages/domains loaded: ' . count($top_pages));
            }

            // ข้อมูลอื่นๆ ตามเดิม
            if (isset($options['includeBrowserStats']) && $options['includeBrowserStats']) {
                $data['browser_stats'] = $this->External_stats_model->get_browser_stats() ?: [];
            }

            if (isset($options['includeCountryStats']) && $options['includeCountryStats']) {
                $data['country_stats'] = $this->External_stats_model->get_country_stats() ?: [];
            }

            if (isset($options['includeHourlyStats']) && $options['includeHourlyStats']) {
                $data['hourly_stats'] = $this->External_stats_model->get_hourly_visits() ?: [];
            }

            if (isset($options['includeDeviceStats']) && $options['includeDeviceStats']) {
                $data['device_stats'] = $this->External_stats_model->get_device_summary() ?: [];
            }

            if (isset($options['includeCharts']) && $options['includeCharts']) {
                $data['daily_stats'] = $this->External_stats_model->get_daily_stats($period) ?: [];
            }

            log_message('info', 'Export data gathered successfully with ' . count($data) . ' sections');

            return $data;

        } catch (Exception $e) {
            log_message('error', 'Error in gather_export_data: ' . $e->getMessage());

            return [
                'summary' => [
                    'total_pageviews' => 0,
                    'total_visitors' => 0,
                    'total_domains' => 0,
                    'online_users' => 0,
                    'avg_pageviews_per_visitor' => 0
                ]
            ];
        }
    }

    /**
     * ✅ รับ export options
     */
    private function get_export_options()
    {
        $options = [];

        $checkboxes = [
            'includeCharts',
            'includeTopDomains',
            'includeBrowserStats',
            'includeCountryStats',
            'includeHourlyStats',
            'includeDeviceStats'
        ];

        foreach ($checkboxes as $checkbox) {
            $value = $this->input->post('options[' . $checkbox . ']');
            $options[$checkbox] = ($value === 'true' || $value === '1' || $value === 1 || $value === true);
        }

        return $options;
    }

    /**
     * ✅ สร้างชื่อไฟล์
     */
    private function generate_export_filename($custom_name, $export_type, $period)
    {
        if (!empty($custom_name)) {
            $custom_name = preg_replace('/\.(pdf|csv|xlsx?)$/i', '', $custom_name);
            return $custom_name . '.' . $export_type;
        }

        $tenant_code = $this->tenant_code ?: 'website';
        $date_suffix = date('Y-m-d_His');

        $file_extension = ($export_type === 'excel') ? 'xlsx' : $export_type;

        return "website_stats_{$tenant_code}_{$date_suffix}.{$file_extension}";
    }

    /**
     * ✅ ส่งออกเป็น CSV - แก้ไขให้แสดงหน้าเว็บแทนโดเมน
     */
    private function export_stats_csv_improved($data, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        if (!$output) {
            throw new Exception('Cannot create output stream');
        }

        // BOM สำหรับ UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        try {
            // ข้อมูลสรุป
            fputcsv($output, ['รายงานสถิติการใช้งานเว็บไซต์'], ',', '"');
            fputcsv($output, ['Tenant Code', $this->tenant_code], ',', '"');
            fputcsv($output, ['วันที่ส่งออก', date('d/m/Y H:i:s')], ',', '"');
            fputcsv($output, [''], ',', '"');

            // สถิติหลัก
            fputcsv($output, ['สถิติหลัก'], ',', '"');
            fputcsv($output, ['การเข้าชมทั้งหมด', $data['summary']['total_pageviews'] ?? 0], ',', '"');
            fputcsv($output, ['ผู้เยี่ยมชมทั้งหมด', $data['summary']['total_visitors'] ?? 0], ',', '"');
            fputcsv($output, ['เว็บไซต์ทั้งหมด', $data['summary']['total_domains'] ?? 0], ',', '"');
            fputcsv($output, ['ผู้ใช้ออนไลน์', $data['summary']['online_users'] ?? 0], ',', '"');
            fputcsv($output, [''], ',', '"');

            // ✅ แก้ไข: หน้าที่เข้าชมยอดนิยม (แทนโดเมน)
            if (isset($data['top_domains']) && !empty($data['top_domains'])) {
                fputcsv($output, ['หน้าที่เข้าชมยอดนิยม'], ',', '"');
                fputcsv($output, ['ลำดับ', 'ชื่อหน้า', 'URL', 'การเข้าชม', 'ผู้เยี่ยมชม'], ',', '"');

                foreach ($data['top_domains'] as $index => $page) {
                    // ✅ รองรับทั้งข้อมูลแบบใหม่ (page) และแบบเก่า (domain)
                    $page_title = '';
                    $page_url = '';

                    if (isset($page->page_title) && isset($page->page_url)) {
                        // ข้อมูลแบบใหม่ (page data)
                        $page_title = $page->page_title ?? 'ไม่ระบุ';
                        $page_url = $page->page_url ?? '';
                    } else {
                        // ข้อมูลแบบเก่า (domain data)
                        $page_title = $page->domain_name ?? 'ไม่ระบุ';
                        $page_url = $page->domain_name ?? '';
                    }

                    fputcsv($output, [
                        $index + 1,
                        $page_title,
                        $page_url,
                        $page->total_views ?? 0,
                        $page->unique_visitors ?? 0
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // สถิติเบราว์เซอร์
            if (isset($data['browser_stats']) && !empty($data['browser_stats'])) {
                fputcsv($output, ['สถิติเบราว์เซอร์'], ',', '"');
                fputcsv($output, ['เบราว์เซอร์', 'จำนวนผู้ใช้', 'เปอร์เซ็นต์'], ',', '"');

                $total_browsers = array_sum(array_column($data['browser_stats'], 'count'));
                foreach ($data['browser_stats'] as $browser) {
                    $percentage = $total_browsers > 0 ? ($browser->count / $total_browsers) * 100 : 0;
                    fputcsv($output, [
                        $browser->browser ?? 'N/A',
                        $browser->count ?? 0,
                        number_format($percentage, 2)
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // ข้อมูลรายวัน
            if (isset($data['daily_stats']) && !empty($data['daily_stats'])) {
                fputcsv($output, ['สถิติรายวัน'], ',', '"');
                fputcsv($output, ['วันที่', 'การเข้าชม', 'ผู้เยี่ยมชม'], ',', '"');

                foreach ($data['daily_stats'] as $daily) {
                    fputcsv($output, [
                        date('d/m/Y', strtotime($daily->date)),
                        $daily->pageviews ?? 0,
                        $daily->visitors ?? 0
                    ], ',', '"');
                }
            }

        } catch (Exception $e) {
            log_message('error', 'CSV export error: ' . $e->getMessage());
            throw $e;
        } finally {
            fclose($output);
        }
    }

    /**
     * ✅ ส่งออกเป็น PDF - แก้ไขให้ไม่ error
     */
    private function export_stats_pdf_improved($data, $filename, $options)
    {
        try {
            // ตรวจสอบ mPDF library
            $mpdf_path = APPPATH . 'third_party/mpdf/vendor/autoload.php';
            if (!file_exists($mpdf_path)) {
                log_message('error', 'mPDF library not found at: ' . $mpdf_path);

                // ✅ แก้ไข: ส่ง error message แทนการ fallback
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'ไม่พบ mPDF library กรุณาติดตั้ง mPDF หรือเลือกส่งออกเป็น CSV แทน',
                    'debug' => [
                        'mpdf_path' => $mpdf_path,
                        'file_exists' => file_exists($mpdf_path)
                    ]
                ]);
                return;
            }

            require_once($mpdf_path);

            // ตรวจสอบว่า class mPDF พร้อมใช้งาน
            if (!class_exists('\Mpdf\Mpdf')) {
                log_message('error', 'mPDF class not found after require');

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'ไม่สามารถโหลด mPDF class ได้ กรุณาตรวจสอบการติดตั้ง',
                    'debug' => [
                        'mpdf_path' => $mpdf_path,
                        'class_exists' => class_exists('\Mpdf\Mpdf')
                    ]
                ]);
                return;
            }

            // สร้าง HTML ที่ปลอดภัยสำหรับ PDF
            $html = $this->generate_safe_pdf_html($data, $options);

            // ตั้งค่า PDF แบบ conservative
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 25,
                'margin_bottom' => 25,
                'default_font' => 'dejavusans',
                'default_font_size' => 12,
                'tempDir' => sys_get_temp_dir()
            ]);

            $mpdf->SetDisplayMode('fullpage');
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;

            $mpdf->WriteHTML($html);

            // ✅ แก้ไข: ตรวจสอบให้แน่ใจว่าเป็นไฟล์ PDF
            if (!str_ends_with($filename, '.pdf')) {
                $filename = str_replace(['.csv', '.xlsx', '.excel'], '.pdf', $filename);
                if (!str_ends_with($filename, '.pdf')) {
                    $filename .= '.pdf';
                }
            }

            // ส่งออกไฟล์
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');

            $mpdf->Output($filename, 'D');

            log_message('info', 'PDF exported successfully: ' . $filename);

        } catch (Exception $e) {
            log_message('error', 'PDF export error: ' . $e->getMessage());

            // ✅ แก้ไข: ส่ง error response แทนการ fallback
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการสร้าง PDF: ' . $e->getMessage(),
                'debug' => [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'filename' => $filename
                ]
            ]);
        }
    }

    /**
     * ✅ เพิ่ม - ตรวจสอบ Library ก่อนส่งออก
     */
    public function check_export_libraries()
    {
        header('Content-Type: application/json');

        $libraries = [
            'mpdf' => [
                'path' => APPPATH . 'third_party/mpdf/vendor/autoload.php',
                'class' => '\Mpdf\Mpdf',
                'available' => false
            ],
            'phpspreadsheet' => [
                'class' => '\PhpOffice\PhpSpreadsheet\Spreadsheet',
                'available' => false
            ],
            'phpword' => [
                'path' => APPPATH . 'third_party/phpword/vendor/autoload.php',
                'class' => '\PhpOffice\PhpWord\PhpWord',
                'available' => false
            ]
        ];

        // ตรวจสอบ mPDF
        if (file_exists($libraries['mpdf']['path'])) {
            require_once($libraries['mpdf']['path']);
            $libraries['mpdf']['available'] = class_exists($libraries['mpdf']['class']);
        }

        // ตรวจสอบ PhpSpreadsheet
        $libraries['phpspreadsheet']['available'] = class_exists($libraries['phpspreadsheet']['class']);

        // ตรวจสอบ PhpWord
        if (file_exists($libraries['phpword']['path'])) {
            require_once($libraries['phpword']['path']);
            $libraries['phpword']['available'] = class_exists($libraries['phpword']['class']);
        }

        echo json_encode([
            'success' => true,
            'libraries' => $libraries,
            'recommendations' => [
                'pdf' => $libraries['mpdf']['available'] ? 'PDF พร้อมใช้งาน' : 'ต้องติดตั้ง mPDF library',
                'excel' => $libraries['phpspreadsheet']['available'] ? 'Excel พร้อมใช้งาน' : 'ต้องติดตั้ง PhpSpreadsheet library',
                'word' => $libraries['phpword']['available'] ? 'Word พร้อมใช้งาน' : 'ต้องติดตั้ง PhpWord library',
                'csv' => 'CSV พร้อมใช้งานเสมอ (ไม่ต้องติดตั้ง library เพิ่ม)'
            ]
        ]);
    }



    /**
     * ✅ แก้ไข - ดึงโดเมนที่มีผู้เข้าชมมากที่สุด รองรับ custom date range - แสดงข้อมูล pages
     */
    public function get_top_domains($limit = 10, $period = '7days')
    {
        if (!$this->external_db) {
            return array();
        }

        $pageviews_table = $this->find_existing_pageviews_table();

        if (!$pageviews_table) {
            log_message('error', 'No pageviews table found for get_top_domains');
            return array();
        }

        // กำหนดช่วงเวลา
        $date_condition = $this->build_date_condition_from_period($period);

        try {
            // ✅ เปลี่ยนให้ดึงข้อมูล page แทน domain พร้อมปรับปรุงการสร้างชื่อหน้า
            $sql = "
            SELECT 
                COALESCE(p.page_url, '/') as page_url,
                '{$this->current_domain}' as domain_name,
                COUNT(*) as total_views,
                COUNT(DISTINCT p.visitor_id) as unique_visitors,
                COUNT(DISTINCT DATE(p.created_at)) as active_days,
                -- ✅ ปรับปรุงการสร้างชื่อหน้าให้ดีขึ้น
                CASE 
                    WHEN p.page_url = '/' OR p.page_url = '' OR p.page_url IS NULL THEN 'หน้าแรก'
                    WHEN p.page_url LIKE '%index%' THEN 'หน้าแรก'
                    WHEN p.page_url LIKE '%about%' OR p.page_url LIKE '%เกี่ยวกับ%' THEN 'เกี่ยวกับเรา'
                    WHEN p.page_url LIKE '%contact%' OR p.page_url LIKE '%ติดต่อ%' THEN 'ติดต่อเรา'
                    WHEN p.page_url LIKE '%service%' OR p.page_url LIKE '%บริการ%' THEN 'บริการ'
                    WHEN p.page_url LIKE '%product%' OR p.page_url LIKE '%สินค้า%' THEN 'สินค้า'
                    WHEN p.page_url LIKE '%news%' OR p.page_url LIKE '%ข่าว%' THEN 'ข่าวสาร'
                    WHEN p.page_url LIKE '%blog%' OR p.page_url LIKE '%บล็อก%' THEN 'บล็อก'
                    WHEN p.page_url LIKE '%complain%' OR p.page_url LIKE '%ร้องเรียน%' THEN 'แจ้งเรื่องร้องเรียน'
                    WHEN p.page_url LIKE '%queue%' OR p.page_url LIKE '%คิว%' THEN 'ระบบคิว'
                    WHEN p.page_url LIKE '%login%' OR p.page_url LIKE '%เข้าสู่ระบบ%' THEN 'เข้าสู่ระบบ'
                    WHEN p.page_url LIKE '%register%' OR p.page_url LIKE '%สมัคร%' THEN 'สมัครสมาชิก'
                    WHEN p.page_url LIKE '%download%' OR p.page_url LIKE '%ดาวน์โหลด%' THEN 'ดาวน์โหลด'
                    WHEN p.page_url LIKE '%gallery%' OR p.page_url LIKE '%แกลเลอรี่%' THEN 'แกลเลอรี่'
                    WHEN p.page_url LIKE '%search%' OR p.page_url LIKE '%ค้นหา%' THEN 'ค้นหา'
                    ELSE COALESCE(
                        NULLIF(
                            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(p.page_url, '_', ' '), '/', -1), '?', 1)), 
                            ''
                        ), 
                        'หน้าอื่นๆ'
                    )
                END as page_title
            FROM {$pageviews_table} p
            WHERE {$date_condition}
            GROUP BY p.page_url
            ORDER BY total_views DESC
            LIMIT {$limit}
        ";

            log_message('debug', 'Top pages SQL: ' . $sql);
            log_message('debug', 'Period parameter: ' . json_encode($period));

            $query = $this->external_db->query($sql);

            if (!$query) {
                log_message('error', 'Query failed in get_top_domains (pages)');
                return array();
            }

            $results = $query->result();
            log_message('info', 'get_top_domains (pages) found ' . count($results) . ' results');

            return $results;

        } catch (Exception $e) {
            log_message('error', 'Get top domains (pages) error: ' . $e->getMessage());

            // Fallback: SQL แบบง่าย
            try {
                $sql_fallback = "
                SELECT 
                    COALESCE(p.page_url, '/') as page_url,
                    '{$this->current_domain}' as domain_name,
                    COUNT(*) as total_views,
                    COUNT(DISTINCT p.visitor_id) as unique_visitors,
                    1 as active_days,
                    'หน้าเว็บ' as page_title
                FROM {$pageviews_table} p
                WHERE {$date_condition}
                GROUP BY p.page_url
                ORDER BY total_views DESC
                LIMIT {$limit}
            ";

                $query = $this->external_db->query($sql_fallback);
                $results = $query ? $query->result() : array();

                log_message('info', 'get_top_domains (pages) fallback found ' . count($results) . ' results');
                return $results;

            } catch (Exception $e2) {
                log_message('error', 'Get top domains (pages) fallback error: ' . $e2->getMessage());
                return array();
            }
        }
    }





    /**
     * ✅ สร้าง HTML ที่ปลอดภัยสำหรับ PDF - แก้ไขให้รองรับข้อมูล pages
     */




    private function generate_safe_pdf_html($data, $options)
    {
        $tenant_name = htmlspecialchars($this->tenant_code ?: 'เว็บไซต์', ENT_QUOTES, 'UTF-8');
        $export_date = date('d/m/Y H:i:s');

        $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            @page {
                margin: 20mm;
            }
            body { 
                font-family: "DejaVu Sans", sans-serif; 
                font-size: 12px; 
                line-height: 1.4;
                color: #333;
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 2px solid #333;
                padding-bottom: 15px;
            }
            .title { 
                font-size: 18px; 
                font-weight: bold; 
                margin-bottom: 8px;
            }
            .subtitle { 
                font-size: 14px; 
                color: #666; 
                margin: 3px 0;
            }
            .summary-section {
                background: #f9f9f9;
                border: 1px solid #ddd;
                padding: 15px;
                margin: 15px 0;
            }
            .stat-row { 
                display: flex; 
                justify-content: space-between; 
                margin: 10px 0;
                padding: 5px 0;
                border-bottom: 1px dotted #ccc;
            }
            .stat-label { 
                font-weight: bold; 
            }
            .stat-value { 
                font-weight: bold; 
            }
            .table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 15px 0;
                font-size: 11px;
            }
            .table th, .table td { 
                border: 1px solid #ccc; 
                padding: 6px; 
                text-align: left; 
            }
            .table th { 
                background: #e9e9e9;
                font-weight: bold;
            }
            .section-title {
                font-size: 14px;
                font-weight: bold;
                border-bottom: 1px solid #333;
                padding-bottom: 3px;
                margin: 20px 0 10px 0;
            }
            .footer {
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #ccc;
                text-align: center;
                font-size: 10px;
                color: #666;
            }
            .page-url {
                font-size: 10px;
                color: #666;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">รายงานสถิติการใช้งานเว็บไซต์</div>
            <div class="subtitle">Tenant: ' . $tenant_name . '</div>
            <div class="subtitle">วันที่ส่งออก: ' . $export_date . '</div>
        </div>

        <div class="summary-section">
            <h3 style="margin-top: 0;">สรุปสถิติ</h3>
            <div class="stat-row">
                <span class="stat-label">การเข้าชมทั้งหมด:</span>
                <span class="stat-value">' . number_format($data['summary']['total_pageviews'] ?? 0) . '</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">ผู้เยี่ยมชมทั้งหมด:</span>
                <span class="stat-value">' . number_format($data['summary']['total_visitors'] ?? 0) . '</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">เว็บไซต์ทั้งหมด:</span>
                <span class="stat-value">' . number_format($data['summary']['total_domains'] ?? 0) . '</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">ผู้ใช้ออนไลน์:</span>
                <span class="stat-value">' . number_format($data['summary']['online_users'] ?? 0) . '</span>
            </div>
        </div>';

        // ✅ แก้ไข: หน้าที่เข้าชมยอดนิยม (แทน domains)
        if (isset($data['top_domains']) && !empty($data['top_domains'])) {
            $html .= '
        <div class="section-title">หน้าที่เข้าชมยอดนิยม</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">อันดับ</th>
                    <th style="width: 50%;">ชื่อหน้า</th>
                    <th style="width: 20%;">การเข้าชม</th>
                    <th style="width: 20%;">ผู้เยี่ยมชม</th>
                </tr>
            </thead>
            <tbody>';

            foreach (array_slice($data['top_domains'], 0, 15) as $index => $page) {
                // ✅ รองรับทั้งข้อมูลแบบใหม่ (page) และแบบเก่า (domain)
                $page_title = '';
                $page_url = '';

                if (isset($page->page_title) && isset($page->page_url)) {
                    // ข้อมูลแบบใหม่ (page data)
                    $page_title = htmlspecialchars($page->page_title ?? 'ไม่ระบุ', ENT_QUOTES, 'UTF-8');
                    $page_url = htmlspecialchars($page->page_url ?? '', ENT_QUOTES, 'UTF-8');
                } else {
                    // ข้อมูลแบบเก่า (domain data)
                    $page_title = htmlspecialchars($page->domain_name ?? 'ไม่ระบุ', ENT_QUOTES, 'UTF-8');
                    $page_url = '';
                }

                $html .= '
                <tr>
                    <td>' . ($index + 1) . '</td>
                    <td>
                        <div>' . $page_title . '</div>
                        ' . ($page_url ? '<div class="page-url">' . $page_url . '</div>' : '') . '
                    </td>
                    <td>' . number_format($page->total_views ?? 0) . '</td>
                    <td>' . number_format($page->unique_visitors ?? 0) . '</td>
                </tr>';
            }

            $html .= '
            </tbody>
        </table>';
        }

        // สถิติเบราว์เซอร์
        if (isset($data['browser_stats']) && !empty($data['browser_stats'])) {
            $html .= '
        <div class="section-title">สถิติเบราว์เซอร์</div>
        <table class="table">
            <thead>
                <tr>
                    <th>เบราว์เซอร์</th>
                    <th>จำนวนผู้ใช้</th>
                    <th>เปอร์เซ็นต์</th>
                </tr>
            </thead>
            <tbody>';

            $total_browsers = array_sum(array_column($data['browser_stats'], 'count'));
            foreach (array_slice($data['browser_stats'], 0, 10) as $browser) {
                $percentage = $total_browsers > 0 ? ($browser->count / $total_browsers) * 100 : 0;
                $browser_name = htmlspecialchars($browser->browser ?? 'N/A', ENT_QUOTES, 'UTF-8');
                $html .= '
                <tr>
                    <td>' . $browser_name . '</td>
                    <td>' . number_format($browser->count ?? 0) . '</td>
                    <td>' . number_format($percentage, 1) . '%</td>
                </tr>';
            }

            $html .= '
            </tbody>
        </table>';
        }

        $html .= '
        <div class="footer">
            รายงานนี้สร้างโดยระบบอัตโนมัติ<br>
            วันที่: ' . $export_date . ' | Tenant: ' . $tenant_name . '
        </div>
    </body>
    </html>';

        return $html;
    }

    /**
     * ✅ ส่งออกเป็น Excel - แก้ไขให้แสดงหน้าเว็บ
     */
    private function export_stats_excel_improved($data, $filename, $options)
    {
        // ตรวจสอบ PhpSpreadsheet
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            log_message('warning', 'PhpSpreadsheet not available, fallback to CSV');
            $csv_filename = str_replace(['.excel', '.xlsx'], '.csv', $filename);
            $this->export_stats_csv_improved($data, $csv_filename);
            return;
        }

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Sheet สรุป
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('สรุปสถิติ');

            $summary = isset($data['summary']) && is_array($data['summary']) ? $data['summary'] : [];
            $tenant_code = $this->tenant_code ?: 'website';

            // Header
            $sheet->setCellValue('A1', 'รายงานสถิติการใช้งานเว็บไซต์');
            $sheet->setCellValue('A2', 'Tenant: ' . $tenant_code);
            $sheet->setCellValue('A3', 'วันที่ส่งออก: ' . date('d/m/Y H:i:s'));

            // สถิติหลัก
            $sheet->setCellValue('A5', 'สถิติหลัก');
            $sheet->setCellValue('A6', 'การเข้าชมทั้งหมด');
            $sheet->setCellValue('B6', (int) ($summary['total_pageviews'] ?? 0));
            $sheet->setCellValue('A7', 'ผู้เยี่ยมชมทั้งหมด');
            $sheet->setCellValue('B7', (int) ($summary['total_visitors'] ?? 0));
            $sheet->setCellValue('A8', 'เว็บไซต์ทั้งหมด');
            $sheet->setCellValue('B8', (int) ($summary['total_domains'] ?? 0));
            $sheet->setCellValue('A9', 'ผู้ใช้ออนไลน์');
            $sheet->setCellValue('B9', (int) ($summary['online_users'] ?? 0));

            // ✅ แก้ไข: หน้าที่เข้าชมยอดนิยม
            if (isset($data['top_domains']) && is_array($data['top_domains']) && !empty($data['top_domains'])) {
                $pagesSheet = $spreadsheet->createSheet();
                $pagesSheet->setTitle('หน้าที่เข้าชมยอดนิยม');

                $pagesSheet->setCellValue('A1', 'ลำดับ');
                $pagesSheet->setCellValue('B1', 'ชื่อหน้า');
                $pagesSheet->setCellValue('C1', 'URL');
                $pagesSheet->setCellValue('D1', 'การเข้าชม');
                $pagesSheet->setCellValue('E1', 'ผู้เยี่ยมชม');

                $row = 2;
                foreach ($data['top_domains'] as $index => $page) {
                    if (is_object($page)) {
                        // รองรับทั้งข้อมูลแบบใหม่และแบบเก่า
                        $page_title = '';
                        $page_url = '';

                        if (isset($page->page_title) && isset($page->page_url)) {
                            // ข้อมูลแบบใหม่ (page data)
                            $page_title = $page->page_title ?? 'ไม่ระบุ';
                            $page_url = $page->page_url ?? '';
                        } else {
                            // ข้อมูลแบบเก่า (domain data)
                            $page_title = $page->domain_name ?? 'ไม่ระบุ';
                            $page_url = $page->domain_name ?? '';
                        }

                        $pagesSheet->setCellValue('A' . $row, $index + 1);
                        $pagesSheet->setCellValue('B' . $row, $page_title);
                        $pagesSheet->setCellValue('C' . $row, $page_url);
                        $pagesSheet->setCellValue('D' . $row, (int) ($page->total_views ?? 0));
                        $pagesSheet->setCellValue('E' . $row, (int) ($page->unique_visitors ?? 0));
                        $row++;
                    }
                }
            }

            // ตั้งค่าการแสดงผล
            $spreadsheet->setActiveSheetIndex(0);

            // ส่งออกไฟล์
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');

        } catch (Exception $e) {
            log_message('error', 'Excel export error: ' . $e->getMessage());
            // Fallback เป็น CSV
            $csv_filename = str_replace(['.excel', '.xlsx'], '.csv', $filename);
            $this->export_stats_csv_improved($data, $csv_filename);
        }
    }
    /**
     * ✅ AJAX endpoint สำหรับ preview ข้อมูลก่อนส่งออก - แก้ไข
     */
    public function ajax_export_preview()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            if (!isset($this->External_stats_model)) {
                $this->load->model('External_stats_model');
            }

            // รับข้อมูลจาก request
            $date_range = $this->input->post('dateRange') ?: 'daily';
            $start_date = $this->input->post('startDate');
            $end_date = $this->input->post('endDate');
            $period = $this->input->post('period');

            // รับ options
            $options_post = $this->input->post('options');
            $options = [];

            if (is_array($options_post)) {
                foreach ($options_post as $key => $value) {
                    $options[$key] = ($value === 'true' || $value === true || $value === '1');
                }
            }

            // ตรวจสอบการเชื่อมต่อ
            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            if (!$current_tenant) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'summary' => [
                            'total_pageviews' => '0',
                            'total_visitors' => '0',
                            'total_domains' => '0',
                            'online_users' => '0'
                        ],
                        'top_domains' => [],
                        'export_counts' => [
                            'domains' => 0,
                            'browsers' => 0,
                            'countries' => 0,
                            'hourly_data' => 0,
                            'daily_data' => 0
                        ]
                    ],
                    'period_info' => $this->get_period_description($this->determine_query_period($period, $date_range, $start_date, $end_date)),
                    'tenant_code' => $this->tenant_code,
                    'message' => 'ใช้ข้อมูลจากหน้าปัจจุบัน (ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้)'
                ]);
                return;
            }

            // ✅ แก้ไข: กำหนดช่วงเวลาอย่างปลอดภัย
            $query_period = $this->determine_query_period($period, $date_range, $start_date, $end_date);

            // ✅ แก้ไข: ตรวจสอบให้แน่ใจว่า query_period ไม่เป็น array ที่ invalid
            if (is_array($query_period) && !isset($query_period['type'])) {
                log_message('error', 'Invalid query_period array: ' . json_encode($query_period));
                $query_period = '7days';
            }

            // ดึงข้อมูลสำหรับ preview
            $preview_data = [];

            try {
                $summary = $this->External_stats_model->get_stats_summary($query_period);
                $preview_data['summary'] = [
                    'total_pageviews' => number_format((int) ($summary['total_pageviews'] ?? 0)),
                    'total_visitors' => number_format((int) ($summary['total_visitors'] ?? 0)),
                    'total_domains' => number_format((int) ($summary['total_domains'] ?? 0)),
                    'online_users' => number_format((int) ($summary['online_users'] ?? 0))
                ];
            } catch (Exception $e) {
                log_message('error', 'Error getting summary for preview: ' . $e->getMessage());
                $preview_data['summary'] = [
                    'total_pageviews' => '0',
                    'total_visitors' => '0',
                    'total_domains' => '0',
                    'online_users' => '0'
                ];
            }

            // Top domains preview
            if (isset($options['includeTopDomains']) && $options['includeTopDomains']) {
                try {
                    $top_domains = $this->External_stats_model->get_top_domains(5, $query_period);
                    $preview_data['top_domains'] = array_map(function ($domain) {
                        return [
                            'domain_name' => $domain->domain_name ?? 'N/A',
                            'total_views' => number_format((int) ($domain->total_views ?? 0)),
                            'unique_visitors' => number_format((int) ($domain->unique_visitors ?? 0))
                        ];
                    }, $top_domains ?: []);
                } catch (Exception $e) {
                    log_message('error', 'Error getting top domains for preview: ' . $e->getMessage());
                    $preview_data['top_domains'] = [];
                }
            }

            // Export counts
            $preview_data['export_counts'] = [
                'domains' => 0,
                'browsers' => 0,
                'countries' => 0,
                'hourly_data' => 0,
                'daily_data' => 0
            ];

            // ปลอดภัยในการนับข้อมูล
            try {
                if (isset($options['includeTopDomains']) && $options['includeTopDomains']) {
                    $domains = $this->External_stats_model->get_top_domains(50, $query_period);
                    $preview_data['export_counts']['domains'] = count($domains ?: []);
                }

                if (isset($options['includeBrowserStats']) && $options['includeBrowserStats']) {
                    $browsers = $this->External_stats_model->get_browser_stats();
                    $preview_data['export_counts']['browsers'] = count($browsers ?: []);
                }

                if (isset($options['includeCountryStats']) && $options['includeCountryStats']) {
                    $countries = $this->External_stats_model->get_country_stats();
                    $preview_data['export_counts']['countries'] = count($countries ?: []);
                }

                if (isset($options['includeHourlyStats']) && $options['includeHourlyStats']) {
                    $preview_data['export_counts']['hourly_data'] = 24;
                }

                if (isset($options['includeCharts']) && $options['includeCharts']) {
                    $daily = $this->External_stats_model->get_daily_stats($query_period);
                    $preview_data['export_counts']['daily_data'] = count($daily ?: []);
                }
            } catch (Exception $e) {
                log_message('error', 'Error counting export data: ' . $e->getMessage());
            }

            echo json_encode([
                'success' => true,
                'data' => $preview_data,
                'period_info' => $this->get_period_description($query_period),
                'tenant_code' => $this->tenant_code,
                'debug' => [
                    'date_range' => $date_range,
                    'options_received' => $options,
                    'query_period' => $query_period,
                    'tenant' => $current_tenant
                ]
            ]);

        } catch (Exception $e) {
            log_message('error', 'Export Preview Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการสร้างตัวอย่าง: ' . $e->getMessage()
            ]);
        }
    }




    /**
     * ดึงข้อมูลผู้ใช้ปัจจุบัน
     */
    private function get_user_info()
    {
        $user_id = $this->session->userdata('m_id');
        return $this->db->select('m.*, p.pname')
            ->from('tbl_member m')
            ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
            ->where('m.m_id', $user_id)
            ->get()
            ->row();
    }


    /**
     * 🆕 สรุปสถิติการใช้งานเว็บไซต์แบบครอบคลุม
     */
    public function website_stats_summary()
    {
        try {
            // โหลด External_stats_model
            $this->load->model('External_stats_model');

            // ตรวจสอบการเชื่อมต่อ
            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            if (!$current_tenant) {
                show_error('ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้ กรุณาตรวจสอบการตั้งค่า');
            }

            // รับ period จาก URL parameter
            $period = $this->input->get('period') ?: '30days';
            $custom_start = $this->input->get('start_date');
            $custom_end = $this->input->get('end_date');

            // กำหนด period สำหรับ query
            $query_period = $this->determine_query_period($period, null, $custom_start, $custom_end);

            $data['page_title'] = 'สรุปสถิติการใช้งานเว็บไซต์';
            $data['user_info'] = $this->get_user_info();
            $data['tenant_code'] = $this->tenant_code;
            $data['current_domain'] = $this->current_domain;
            $data['selected_period'] = $period;
            $data['query_period'] = $query_period;

            // ดึงข้อมูลสถิติครอบคลุม
            $data['summary_data'] = $this->get_comprehensive_stats_summary($query_period);

            // เพิ่มข้อมูล debug สำหรับ system admin
            $data['debug_connection_info'] = $this->get_debug_connection_info();
            $data['is_system_admin'] = $this->is_system_admin();

            $this->load->view('reports/header', $data);
            $this->load->view('reports/website_stats_summary', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Website Stats Summary Error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการดึงข้อมูลสถิติ: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 รวบรวมข้อมูลสถิติแบบครอบคลุมทุกหัวข้อ
     */
    private function get_comprehensive_stats_summary($period)
    {
        $summary = [];

        try {
            // 1. สถิติหลัก
            $summary['overview'] = $this->External_stats_model->get_stats_summary($period);

            // 2. สถิติรายวัน (สำหรับกราฟ)
            $summary['daily_stats'] = $this->External_stats_model->get_daily_stats($period);

            // 3. เว็บไซต์/โดเมนยอดนิยม - ✅ แก้ไข: ใช้ชื่อ key ที่ตรงกัน
            $summary['top_domains'] = $this->External_stats_model->get_top_domains(20, $period);

            // 4. สถิติเบราว์เซอร์
            $summary['browser_stats'] = $this->External_stats_model->get_browser_stats();

            // 5. สถิติอุปกรณ์
            $summary['device_stats'] = $this->External_stats_model->get_device_summary();

            // 6. สถิติแพลตฟอร์ม
            $summary['platform_stats'] = $this->External_stats_model->get_platform_summary();

            // 7. สถิติประเทศ
            $summary['country_stats'] = $this->External_stats_model->get_country_stats();

            // 8. สถิติรายชั่วโมง
            $summary['hourly_stats'] = $this->External_stats_model->get_hourly_visits();

            // 9. คำนวณสถิติเพิ่มเติม
            $summary['calculated_stats'] = $this->calculate_additional_stats($summary);

            // 10. สร้างข้อสรุปและคำแนะนำ
            $summary['insights'] = $this->generate_insights($summary, $period);

            return $summary;

        } catch (Exception $e) {
            log_message('error', 'Error gathering comprehensive stats: ' . $e->getMessage());
            return $this->get_empty_summary();
        }
    }


    /**
     * 🆕 คำนวณสถิติเพิ่มเติม
     */


    /**
     * 🆕 สร้างข้อสรุปและคำแนะนำ
     */
    private function generate_insights($summary, $period)
    {
        $insights = [];
        $calculated = $summary['calculated_stats'] ?? [];

        // ข้อสรุปหลัก
        $insights['main_summary'] = [];

        if (($calculated['total_pageviews'] ?? 0) > 0) {
            $insights['main_summary'][] = sprintf(
                'ในช่วง%s มีการเข้าชมทั้งหมด %s ครั้ง จากผู้เยี่ยมชม %s คน',
                $this->get_period_description($period),
                number_format($calculated['total_pageviews']),
                number_format($calculated['total_visitors'])
            );

            if (($calculated['avg_pages_per_visitor'] ?? 0) >= 2) {
                $insights['main_summary'][] = sprintf(
                    'ผู้เยี่ยมชมแต่ละคนดูเฉลี่ย %.1f หน้า แสดงว่ามีความสนใจในเนื้อหา',
                    $calculated['avg_pages_per_visitor']
                );
            }

            if (($calculated['bounce_rate_estimate'] ?? 0) > 70) {
                $insights['main_summary'][] = 'อัตราการเด้งออกค่อนข้างสูง ควรปรับปรุงเนื้อหาให้น่าสนใจมากขึ้น';
            }
        }

        // ข้อสรุปเกี่ยวกับเวลา
        $insights['time_analysis'] = [];

        if (!empty($calculated['peak_hour'])) {
            $insights['time_analysis'][] = sprintf(
                'ช่วงเวลาที่มีผู้เข้าชมมากที่สุดคือ %s (%s ครั้ง)',
                $calculated['peak_hour'],
                number_format($calculated['peak_hour_visits'])
            );
        }

        if (!empty($calculated['quiet_hour'])) {
            $insights['time_analysis'][] = sprintf(
                'ช่วงเวลาที่เงียบที่สุดคือ %s (%s ครั้ง)',
                $calculated['quiet_hour'],
                number_format($calculated['quiet_hour_visits'])
            );
        }

        // ข้อสรุปเกี่ยวกับอุปกรณ์และเบราว์เซอร์
        $insights['technology_analysis'] = [];

        if (!empty($calculated['top_browser'])) {
            $insights['technology_analysis'][] = sprintf(
                'เบราว์เซอร์ยอดนิยมคือ %s (%s%%)',
                $calculated['top_browser'],
                $calculated['top_browser_percentage']
            );
        }

        if (!empty($calculated['top_device'])) {
            $insights['technology_analysis'][] = sprintf(
                'อุปกรณ์ที่ใช้มากที่สุดคือ %s (%s%%)',
                $calculated['top_device'],
                $calculated['top_device_percentage']
            );
        }

        // คำแนะนำ
        $insights['recommendations'] = [];

        if (($calculated['bounce_rate_estimate'] ?? 0) > 60) {
            $insights['recommendations'][] = 'ควรปรับปรุงหน้าแรกให้น่าสนใจมากขึ้น เพื่อลดอัตราการเด้งออก';
        }

        if (($calculated['avg_pages_per_visitor'] ?? 0) < 2) {
            $insights['recommendations'][] = 'ควรเพิ่มลิงก์ภายในและเนื้อหาที่เกี่ยวข้องเพื่อให้ผู้เยี่ยมชมอ่านต่อ';
        }

        if (($calculated['device_diversity'] ?? 0) > 0) {
            $mobile_usage = 0;
            foreach (($summary['device_stats'] ?? []) as $device) {
                if (stripos($device->device, 'mobile') !== false || stripos($device->device, 'phone') !== false) {
                    $mobile_usage += $device->count;
                }
            }
            $total_devices = array_sum(array_column($summary['device_stats'] ?? [], 'count'));
            $mobile_percentage = $total_devices > 0 ? ($mobile_usage / $total_devices) * 100 : 0;

            if ($mobile_percentage > 50) {
                $insights['recommendations'][] = sprintf(
                    'การใช้งานผ่านมือถือสูง (%.1f%%) ควรให้ความสำคัญกับ Mobile-First Design',
                    $mobile_percentage
                );
            }
        }

        return $insights;
    }

    /**
     * 🆕 ส่งออกรายงานสรุปสถิติ
     */
    public function export_stats_summary()
    {
        try {
            // รับข้อมูลจาก form
            $export_type = $this->input->post('export_type') ?: 'preview';
            $period = $this->input->post('period') ?: '7days';
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $file_name = $this->input->post('file_name');
            $include_charts = $this->input->post('include_charts') === 'true';
            $include_recommendations = $this->input->post('include_recommendations') === 'true';

            log_message('info', 'Export request - Type: ' . $export_type .
                ', Period: ' . $period .
                ', Start: ' . $start_date .
                ', End: ' . $end_date);

            // Validation - เปลี่ยนให้รองรับ preview และ csv
            if (!in_array($export_type, ['preview', 'csv'])) {
                show_error('ประเภทไฟล์ไม่ถูกต้อง รองรับเฉพาะ Preview และ CSV');
            }

            // โหลด model และตรวจสอบการเชื่อมต่อ
            $this->load->model('External_stats_model');

            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            if (!$current_tenant) {
                show_error('ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้');
            }

            // กำหนดช่วงเวลา
            $query_period = $this->determine_export_period($period, $start_date, $end_date);

            log_message('info', 'Export query period: ' . json_encode($query_period));

            // ดึงข้อมูลสถิติ
            $summary_data = $this->get_comprehensive_stats_summary($query_period);

            // ✅ แก้ไข: ใช้ fallback data ถ้าไม่มีข้อมูล
            $has_data = $this->validate_summary_data($summary_data);

            if (!$has_data) {
                log_message('info', 'No real data found, using fallback data for export');
                $summary_data = $this->create_fallback_summary_data($query_period);
            }

            // สร้างชื่อไฟล์
            $filename = $this->generate_summary_filename($file_name, $export_type, $query_period);

            // เพิ่มข้อมูล metadata
            $export_data = [
                'summary_data' => $summary_data,
                'period_info' => $this->get_period_description($query_period),
                'export_date' => date('d/m/Y H:i:s'),
                'tenant_code' => $this->tenant_code,
                'tenant_name' => $this->get_tenant_name(),
                'include_charts' => $include_charts,
                'include_recommendations' => $include_recommendations,
                'include_detailed_stats' => true, // เพิ่มข้อมูลรายละเอียด
                'period' => $period,
                'original_period' => $period,
                'is_fallback_data' => !$has_data
            ];

            log_message('info', 'Exporting ' . $export_type . ' file: ' . $filename .
                ' (Fallback: ' . (!$has_data ? 'Yes' : 'No') . ')');

            // ✅ แก้ไข: ส่งออกตามประเภทไฟล์ - เหลือแค่ preview และ csv
            switch ($export_type) {
                case 'preview':
                    $this->export_summary_preview($export_data);
                    break;
                case 'csv':
                    $this->export_summary_csv($export_data, $filename);
                    break;
                default:
                    throw new Exception('Unsupported export type: ' . $export_type);
            }

        } catch (Exception $e) {
            log_message('error', 'Export Summary Error: ' . $e->getMessage());

            // ✅ ลองส่งออกด้วยข้อมูล minimal
            try {
                $this->export_minimal_report('csv', $period ?? '7days');
            } catch (Exception $e2) {
                show_error('ไม่สามารถส่งออกรายงานได้: ' . $e->getMessage());
            }
        }
    }


    private function export_summary_preview($data)
    {
        // ✅ ส่งข้อมูลครบเหมือนกับหน้า print
        $preview_data = [
            // ข้อมูลหลัก
            'summary_data' => $data['summary_data'],
            'period_info' => $data['period_info'],
            'export_date' => $data['export_date'],
            'tenant_code' => $data['tenant_code'],
            'tenant_name' => $data['tenant_name'],

            // Options
            'include_charts' => $data['include_charts'],
            'include_recommendations' => $data['include_recommendations'],
            'include_detailed_stats' => true,

            // Period info
            'period' => $data['period'],
            'original_period' => $data['original_period'],
            'is_fallback_data' => $data['is_fallback_data'],

            // ✅ เพิ่ม: ข้อมูลสำหรับ page title และ meta
            'page_title' => 'รายงานสรุปสถิติการใช้งานเว็บไซต์ - ' . ($data['tenant_name'] ?? $data['tenant_code']),
            'meta_description' => 'รายงานสถิติการเข้าชมเว็บไซต์ ' . $data['period_info'] . ' สร้างเมื่อ ' . $data['export_date']
        ];

        // ✅ ใช้ view ใหม่ที่มีการจัดการข้อมูล pages ที่ดีกว่า
        $this->load->view('reports/preview_report', $preview_data);
    }





    private function validate_summary_data($summary_data)
    {
        if (empty($summary_data) || !is_array($summary_data)) {
            return false;
        }

        $overview = $summary_data['overview'] ?? [];
        $pageviews = (int) ($overview['total_pageviews'] ?? 0);
        $visitors = (int) ($overview['total_visitors'] ?? 0);
        $domains = count($summary_data['top_domains'] ?? []);

        // ถือว่ามีข้อมูลถ้ามีอย่างใดอย่างหนึ่ง
        return ($pageviews > 0 || $visitors > 0 || $domains > 0);
    }


    private function create_fallback_summary_data($query_period)
    {
        $period_desc = $this->get_period_description($query_period);

        return [
            'overview' => [
                'total_pageviews' => 0,
                'total_visitors' => 0,
                'total_domains' => 0,
                'online_users' => 0,
                'avg_pageviews_per_visitor' => 0
            ],
            'daily_stats' => [],
            'top_domains' => [], // ✅ แก้ไข: เพิ่ม key นี้
            'browser_stats' => [],
            'device_stats' => [],
            'platform_stats' => [],
            'country_stats' => [],
            'hourly_stats' => [],
            'calculated_stats' => [
                'total_pageviews' => 0,
                'total_visitors' => 0,
                'avg_pages_per_visitor' => 0,
                'bounce_rate_estimate' => 0,
                'avg_daily_pageviews' => 0,
                'avg_daily_visitors' => 0,
                'peak_day_pageviews' => 0,
                'peak_day_visitors' => 0
            ],
            'insights' => [
                'main_summary' => [
                    'ไม่พบข้อมูลในช่วง ' . $period_desc,
                    'อาจเป็นเพราะ: ยังไม่มีการเข้าชมเว็บไซต์, ช่วงเวลาที่เลือกไม่มีข้อมูล, หรือระบบติดตามยังไม่ได้เปิดใช้งาน'
                ],
                'time_analysis' => [],
                'technology_analysis' => [],
                'recommendations' => [
                    'ตรวจสอบการติดตั้งระบบติดตามการเข้าชม (Web Analytics)',
                    'ตรวจสอบการตั้งค่าระบบให้ถูกต้อง',
                    'ลองเลือกช่วงเวลาที่ผ่านมาแล้ว'
                ]
            ]
        ];
    }


    /**
     * 🆕 ส่งออกรายงานแบบ minimal เมื่อเกิด error
     */
    private function export_minimal_report($export_type, $period)
    {
        $filename = "รายงานสถิติ_" . date('Y-m-d_His') . "." .
            ($export_type === 'excel' ? 'xlsx' : $export_type);

        switch ($export_type) {
            case 'csv':
                $this->export_minimal_csv($filename, $period);
                break;
            case 'pdf':
            default:
                $this->export_minimal_pdf($filename, $period);
                break;
        }
    }

    /**
     * 🆕 ส่งออก CSV แบบ minimal
     */
    private function export_minimal_csv($filename, $period)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF"); // BOM

        fputcsv($output, ['รายงานสถิติการใช้งานเว็บไซต์'], ',', '"');
        fputcsv($output, ['Tenant Code', $this->tenant_code], ',', '"');
        fputcsv($output, ['วันที่ส่งออก', date('d/m/Y H:i:s')], ',', '"');
        fputcsv($output, ['Period', $period], ',', '"');
        fputcsv($output, [''], ',', '"');
        fputcsv($output, ['สถานะ', 'ไม่พบข้อมูลในช่วงเวลาที่เลือก'], ',', '"');
        fputcsv($output, ['หมายเหตุ', 'กรุณาตรวจสอบระบบ Web Analytics'], ',', '"');

        fclose($output);
    }

    /**
     * 🆕 ส่งออก PDF แบบ minimal
     */
    private function export_minimal_pdf($filename, $period)
    {
        // ถ้าไม่มี mPDF ให้ส่งออกเป็น CSV แทน
        $mpdf_path = APPPATH . 'third_party/mpdf/vendor/autoload.php';
        if (!file_exists($mpdf_path)) {
            $csv_filename = str_replace('.pdf', '.csv', $filename);
            $this->export_minimal_csv($csv_filename, $period);
            return;
        }

        require_once($mpdf_path);

        $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: "DejaVu Sans", sans-serif; font-size: 14px; }
            .header { text-align: center; margin-bottom: 30px; }
            .content { margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>รายงานสถิติการใช้งานเว็บไซต์</h2>
            <p>Tenant: ' . htmlspecialchars($this->tenant_code) . '</p>
            <p>วันที่: ' . date('d/m/Y H:i:s') . '</p>
        </div>
        <div class="content">
            <h3>สถานะ</h3>
            <p>ไม่พบข้อมูลในช่วงเวลาที่เลือก (Period: ' . htmlspecialchars($period) . ')</p>
            
            <h3>คำแนะนำ</h3>
            <ul>
                <li>ตรวจสอบการติดตั้งระบบ Web Analytics</li>
                <li>ตรวจสอบการเชื่อมต่อฐานข้อมูล</li>
                <li>ลองเลือกช่วงเวลาที่ผ่านมาแล้ว</li>
            </ul>
        </div>
    </body>
    </html>';

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'dejavusans'
            ]);

            $mpdf->WriteHTML($html);

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $mpdf->Output($filename, 'D');

        } catch (Exception $e) {
            $csv_filename = str_replace('.pdf', '.csv', $filename);
            $this->export_minimal_csv($csv_filename, $period);
        }
    }
    /**
     * 🆕 กำหนด period สำหรับ export ให้ตรงกับ filter system
     */
    private function determine_export_period($period, $start_date = null, $end_date = null)
    {
        $today = date('Y-m-d');

        // ถ้าเป็น custom และมีวันที่
        if ($period === 'custom' && !empty($start_date) && !empty($end_date)) {
            // ✅ ตรวจสอบวันที่ว่าไม่เกินวันนี้
            if ($start_date > $today) {
                $start_date = $today;
            }
            if ($end_date > $today) {
                $end_date = $today;
            }

            // ✅ ตรวจสอบให้ start <= end
            if ($start_date > $end_date) {
                $end_date = $start_date;
            }

            return [
                'type' => 'custom',
                'start_date' => $start_date,
                'end_date' => $end_date
            ];
        }

        // ถ้าเป็น predefined period
        $valid_periods = ['today', '7days', '30days', 'current_month'];
        if (in_array($period, $valid_periods)) {
            return [
                'type' => 'predefined',
                'period' => $period,
                'start_date' => $this->get_period_start_date($period),
                'end_date' => $this->get_period_end_date($period)
            ];
        }

        // Fallback เป็น 7days
        return [
            'type' => 'predefined',
            'period' => '7days',
            'start_date' => date('Y-m-d', strtotime('-6 days')),
            'end_date' => date('Y-m-d')
        ];
    }

    /**
     * 🆕 ดึงวันที่เริ่มต้นของ period
     */
    private function get_period_start_date($period)
    {
        switch ($period) {
            case 'today':
                return date('Y-m-d');
            case '7days':
                return date('Y-m-d', strtotime('-6 days'));
            case '30days':
                return date('Y-m-d', strtotime('-29 days'));
            case 'current_month':
                return date('Y-m-01');
            default:
                return date('Y-m-d', strtotime('-6 days'));
        }
    }

    /**
     * 🆕 ดึงวันที่สิ้นสุดของ period
     */
    private function get_period_end_date($period)
    {
        switch ($period) {
            case 'today':
                return date('Y-m-d');
            case '7days':
            case '30days':
            case 'current_month':
                return date('Y-m-d');
            default:
                return date('Y-m-d');
        }
    }

    /**
     * ✅ แก้ไข: ปรับปรุง get_period_description ให้รองรับ array format
     */
    private function get_period_description($period)
    {
        if (is_array($period)) {
            if ($period['type'] === 'custom') {
                $start_formatted = date('d/m/Y', strtotime($period['start_date']));
                $end_formatted = date('d/m/Y', strtotime($period['end_date']));
                return 'ช่วงวันที่ ' . $start_formatted . ' ถึง ' . $end_formatted;
            } elseif ($period['type'] === 'predefined') {
                return $this->get_predefined_period_description($period['period']);
            }
        }

        // ถ้าเป็น string
        if (is_string($period)) {
            return $this->get_predefined_period_description($period);
        }

        return 'ช่วงเวลาที่กำหนด';
    }

    /**
     * 🆕 ดึงคำอธิบาย predefined period
     */
    private function get_predefined_period_description($period)
    {
        switch ($period) {
            case 'today':
                return 'วันนี้ (' . date('d/m/Y') . ')';
            case '7days':
                return '7 วันล่าสุด (' . date('d/m/Y', strtotime('-6 days')) . ' - ' . date('d/m/Y') . ')';
            case '30days':
                return '30 วันล่าสุด (' . date('d/m/Y', strtotime('-29 days')) . ' - ' . date('d/m/Y') . ')';
            case 'current_month':
                return 'เดือนปัจจุบัน (' . date('d/m/Y', strtotime(date('Y-m-01'))) . ' - ' . date('d/m/Y') . ')';
            default:
                return 'ช่วงเวลาที่กำหนด';
        }
    }

    /**
     * 🆕 สร้างชื่อไฟล์สำหรับรายงานสรุป
     */
    private function generate_summary_filename($custom_name, $export_type, $period)
    {
        if ($export_type === 'preview') {
            return 'preview'; // ไม่ต้องใช้ชื่อไฟล์สำหรับ preview
        }

        if (!empty($custom_name)) {
            $custom_name = preg_replace('/\.(pdf|csv|xlsx?|docx?)$/i', '', $custom_name);
            return $custom_name . '.csv';
        }

        $tenant_code = $this->tenant_code ?: 'website';
        $date_suffix = date('Y-m-d_His');

        // สร้าง period suffix
        $period_suffix = '';
        if (is_array($period)) {
            if ($period['type'] === 'custom') {
                $period_suffix = '_custom_' . str_replace('-', '', $period['start_date']) . '_' . str_replace('-', '', $period['end_date']);
            } elseif ($period['type'] === 'predefined') {
                $period_suffix = '_' . $period['period'];
            }
        } else {
            $period_suffix = '_' . $period;
        }

        return "สรุปสถิติเว็บไซต์_{$tenant_code}{$period_suffix}_{$date_suffix}.csv";
    }



    public function ajax_export_from_preview()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $export_type = $this->input->post('export_type');
            $report_data_json = $this->input->post('report_data');

            if (empty($report_data_json)) {
                throw new Exception('ไม่พบข้อมูลรายงาน');
            }

            $report_data = json_decode($report_data_json, true);
            if (!$report_data) {
                throw new Exception('ข้อมูลรายงานไม่ถูกต้อง');
            }

            // สร้างชื่อไฟล์
            $tenant_code = $report_data['tenant_code'] ?? 'website';
            $date_suffix = date('Y-m-d_His');
            $filename = "รายงานสถิติ_{$tenant_code}_{$date_suffix}.csv";

            // ส่งออกเป็น CSV เท่านั้น
            $this->export_summary_csv($report_data, $filename);

        } catch (Exception $e) {
            log_message('error', 'AJAX Export from Preview Error: ' . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการส่งออกไฟล์: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * 🆕 ส่งออกรายงานสรุปเป็น PDF
     */
    private function export_summary_pdf($data, $filename)
    {
        try {
            $mpdf_path = APPPATH . 'third_party/mpdf/vendor/autoload.php';
            if (!file_exists($mpdf_path)) {
                $csv_filename = str_replace('.pdf', '.csv', $filename);
                $this->export_summary_csv($data, $csv_filename);
                return;
            }

            require_once($mpdf_path);

            // สร้าง HTML สำหรับ PDF
            $html = $this->generate_summary_pdf_html($data);

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'default_font' => 'dejavusans',
                'default_font_size' => 11,
                'tempDir' => sys_get_temp_dir()
            ]);

            $mpdf->SetDisplayMode('fullpage');
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;

            $mpdf->WriteHTML($html);

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $mpdf->Output($filename, 'D');

        } catch (Exception $e) {
            log_message('error', 'PDF export error: ' . $e->getMessage());
            $csv_filename = str_replace('.pdf', '.csv', $filename);
            $this->export_summary_csv($data, $csv_filename);
        }
    }

    /**
     * 🆕 ดึงชื่อ tenant
     */
    private function get_tenant_name()
    {
        try {
            $tenant_db = $this->load->database('tenant_management', TRUE);
            if ($tenant_db) {
                $result = $tenant_db->where('code', $this->tenant_code)
                    ->where('is_active', 1)
                    ->get('tenants')
                    ->row();
                $tenant_db->close();
                return $result ? $result->name : $this->tenant_code;
            }
        } catch (Exception $e) {
            log_message('error', 'Get tenant name error: ' . $e->getMessage());
        }

        return $this->tenant_code ?: 'ไม่ระบุ';
    }

    /**
     * 🆕 สร้าง summary เปล่า
     */
    private function get_empty_summary()
    {
        return [
            'overview' => [
                'total_pageviews' => 0,
                'total_visitors' => 0,
                'total_domains' => 0,
                'online_users' => 0,
                'avg_pageviews_per_visitor' => 0
            ],
            'daily_stats' => [],
            'top_domains' => [], // ✅ แก้ไข: เพิ่ม key นี้
            'browser_stats' => [],
            'device_stats' => [],
            'platform_stats' => [],
            'country_stats' => [],
            'hourly_stats' => [],
            'calculated_stats' => [
                'total_pageviews' => 0,
                'total_visitors' => 0,
                'avg_pages_per_visitor' => 0,
                'bounce_rate_estimate' => 0,
                'avg_daily_pageviews' => 0,
                'avg_daily_visitors' => 0,
                'peak_day_pageviews' => 0,
                'peak_day_visitors' => 0
            ],
            'insights' => [
                'main_summary' => [
                    'ไม่พบข้อมูลในช่วงเวลาที่เลือก',
                    'อาจเป็นเพราะ: ยังไม่มีการเข้าชมเว็บไซต์, ช่วงเวลาที่เลือกไม่มีข้อมูล, หรือระบบติดตามยังไม่ได้เปิดใช้งาน'
                ],
                'time_analysis' => [],
                'technology_analysis' => [],
                'recommendations' => [
                    'ตรวจสอบการติดตั้งระบบติดตามการเข้าชม (Web Analytics)',
                    'ตรวจสอบการตั้งค่าระบบให้ถูกต้อง',
                    'ลองเลือกช่วงเวลาที่ผ่านมาแล้ว'
                ]
            ]
        ];
    }



    /**
     * 🆕 สร้าง HTML สำหรับรายงาน PDF สรุปสถิติ
     */
    /**
     * ✅ แก้ไข: generate_summary_pdf_html - รองรับ fallback data
     */
    private function generate_summary_pdf_html($data)
    {
        $summary_data = $data['summary_data'];
        $tenant_name = htmlspecialchars($data['tenant_name'], ENT_QUOTES, 'UTF-8');
        $export_date = $data['export_date'];
        $period_info = htmlspecialchars($data['period_info'], ENT_QUOTES, 'UTF-8');
        $overview = $summary_data['overview'] ?? [];
        $calculated = $summary_data['calculated_stats'] ?? [];
        $insights = $summary_data['insights'] ?? [];

        // ✅ เช็คว่าเป็น fallback data หรือไม่
        $is_fallback = isset($data['is_fallback_data']) && $data['is_fallback_data'];

        $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #333; }
            .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #667eea; padding-bottom: 15px; }
            .title { font-size: 18px; font-weight: bold; color: #667eea; margin-bottom: 8px; }
            .subtitle { font-size: 14px; color: #666; margin: 3px 0; }
            .warning-box { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 5px; }
            .summary-section { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">รายงานสรุปสถิติการใช้งานเว็บไซต์</div>
            <div class="subtitle">' . $tenant_name . '</div>
            <div class="subtitle">ช่วงเวลา: ' . $period_info . '</div>
            <div class="subtitle">วันที่ออกรายงาน: ' . $export_date . '</div>
        </div>';

        // ✅ แสดงคำเตือนถ้าเป็น fallback data
        if ($is_fallback) {
            $html .= '
        <div class="warning-box">
            <h3 style="color: #856404; margin-top: 0;">⚠️ ข้อมูลไม่เพียงพอ</h3>
            <p>ไม่พบข้อมูลสถิติในช่วงเวลาที่เลือก อาจเป็นเพราะ:</p>
            <ul>
                <li>ยังไม่มีการเข้าชมเว็บไซต์ในช่วงนี้</li>
                <li>ระบบติดตามยังไม่ได้เปิดใช้งาน</li>
                <li>เลือกช่วงเวลาในอนาคต</li>
            </ul>
        </div>';
        }

        // สถิติหลัก
        $html .= '
        <div class="summary-section">
            <h3 style="margin-top: 0;">สรุปสถิติ</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px dotted #ccc;">การเข้าชมทั้งหมด:</td>
                    <td style="padding: 8px; border-bottom: 1px dotted #ccc; font-weight: bold;">' . number_format($overview['total_pageviews'] ?? 0) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px dotted #ccc;">ผู้เยี่ยมชมทั้งหมด:</td>
                    <td style="padding: 8px; border-bottom: 1px dotted #ccc; font-weight: bold;">' . number_format($overview['total_visitors'] ?? 0) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px;">ผู้ใช้ออนไลน์:</td>
                    <td style="padding: 8px; font-weight: bold;">' . number_format($overview['online_users'] ?? 0) . '</td>
                </tr>
            </table>
        </div>';

        // ข้อสรุป
        if (!empty($insights['main_summary'])) {
            $html .= '<h3>ข้อสรุป</h3><ul>';
            foreach ($insights['main_summary'] as $summary) {
                $html .= '<li>' . htmlspecialchars($summary, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $html .= '</ul>';
        }

        // คำแนะนำ
        if (!empty($insights['recommendations'])) {
            $html .= '<h3>คำแนะนำ</h3><ul>';
            foreach ($insights['recommendations'] as $recommendation) {
                $html .= '<li>' . htmlspecialchars($recommendation, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $html .= '</ul>';
        }

        $html .= '
        <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ccc; text-align: center; font-size: 9px; color: #666;">
            รายงานนี้สร้างโดยระบบอัตโนมัติ | ' . $tenant_name . ' | ' . $export_date . '
        </div>
    </body>
    </html>';

        return $html;
    }

    /**
     * 🆕 แปลงรหัสประเทศเป็นชื่อภาษาไทย
     */
    private function get_country_name_thai($country_code)
    {
        $country_map = [
            'TH' => 'ประเทศไทย',
            'US' => 'สหรัฐอมेรिกา',
            'CN' => 'จีน',
            'JP' => 'ญี่ปุ่น',
            'KR' => 'เกาหลีใต้',
            'SG' => 'สิงคโปร์',
            'MY' => 'มาเลเซีย',
            'ID' => 'อินโดนีเซีย',
            'VN' => 'เวียดนาม',
            'PH' => 'ฟิลิปปินส์',
            'GB' => 'สหราชอาณาจักร',
            'DE' => 'เยอรมนี',
            'FR' => 'ฝรั่งเศส',
            'AU' => 'ออสเตรเลีย',
            'CA' => 'แคนาดา',
            'IN' => 'อินเดีย'
        ];

        return $country_map[$country_code] ?? $country_code;
    }

    /**
     * ✅ ส่งออกรายงานสรุปเป็น CSV - แก้ไขให้แสดงหน้าเว็บ
     */
    private function export_summary_csv($data, $filename)
    {
        try {
            // ตรวจสอบว่าสามารถสร้าง output stream ได้
            if (headers_sent()) {
                throw new Exception('Headers already sent, cannot export CSV');
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');

            if (!$output) {
                throw new Exception('Cannot create output stream for CSV');
            }

            // BOM สำหรับ UTF-8
            fwrite($output, "\xEF\xBB\xBF");

            $summary_data = $data['summary_data'];
            $overview = $summary_data['overview'] ?? [];
            $calculated = $summary_data['calculated_stats'] ?? [];
            $insights = $summary_data['insights'] ?? [];

            // Header ของรายงาน
            fputcsv($output, ['รายงานสรุปสถิติการใช้งานเว็บไซต์'], ',', '"');
            fputcsv($output, ['หน่วยงาน', $data['tenant_name']], ',', '"');
            fputcsv($output, ['ช่วงเวลา', $data['period_info']], ',', '"');
            fputcsv($output, ['วันที่ออกรายงาน', $data['export_date']], ',', '"');

            // แสดงสถานะข้อมูล
            if (isset($data['is_fallback_data']) && $data['is_fallback_data']) {
                fputcsv($output, ['สถานะ', 'ไม่พบข้อมูลในช่วงเวลาที่เลือก'], ',', '"');
            }
            fputcsv($output, [''], ',', '"');

            // สรุปผลหลัก
            fputcsv($output, ['=== สรุปผลหลัก ==='], ',', '"');
            fputcsv($output, ['รายการ', 'จำนวน'], ',', '"');
            fputcsv($output, ['การเข้าชมทั้งหมด', number_format($overview['total_pageviews'] ?? 0)], ',', '"');
            fputcsv($output, ['ผู้เยี่ยมชมทั้งหมด', number_format($overview['total_visitors'] ?? 0)], ',', '"');
            fputcsv($output, ['เว็บไซต์ทั้งหมด', number_format($overview['total_domains'] ?? 0)], ',', '"');
            fputcsv($output, ['ผู้ใช้ออนไลน์', number_format($overview['online_users'] ?? 0)], ',', '"');
            fputcsv($output, [''], ',', '"');

            // สถิติเพิ่มเติม
            if (!empty($calculated)) {
                fputcsv($output, ['=== สถิติเพิ่มเติม ==='], ',', '"');
                fputcsv($output, ['รายการ', 'ค่า'], ',', '"');
                fputcsv($output, ['เฉลี่ยหน้าต่อผู้เยี่ยมชม', number_format($calculated['avg_pages_per_visitor'] ?? 0, 2) . ' หน้า'], ',', '"');
                fputcsv($output, ['ประมาณการอัตราการเด้งออก', number_format($calculated['bounce_rate_estimate'] ?? 0, 1) . '%'], ',', '"');
                fputcsv($output, ['เฉลี่ยการเข้าชมต่อวัน', number_format($calculated['avg_daily_pageviews'] ?? 0) . ' ครั้ง'], ',', '"');
                fputcsv($output, ['เฉลี่ยผู้เยี่ยมชมต่อวัน', number_format($calculated['avg_daily_visitors'] ?? 0) . ' คน'], ',', '"');

                if (!empty($calculated['peak_hour'])) {
                    fputcsv($output, ['ช่วงเวลาคึกคัก', $calculated['peak_hour'] . ' (' . number_format($calculated['peak_hour_visits'] ?? 0) . ' ครั้ง)'], ',', '"');
                    fputcsv($output, ['ช่วงเวลาเงียบ', $calculated['quiet_hour'] . ' (' . number_format($calculated['quiet_hour_visits'] ?? 0) . ' ครั้ง)'], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // ✅ แก้ไข: หน้าที่เข้าชมยอดนิยม
            if (!empty($summary_data['top_domains'])) {
                fputcsv($output, ['=== หน้าที่เข้าชมยอดนิยม ==='], ',', '"');
                fputcsv($output, ['อันดับ', 'ชื่อหน้า', 'URL', 'การเข้าชม', 'ผู้เยี่ยมชม'], ',', '"');

                foreach (array_slice($summary_data['top_domains'], 0, 15) as $index => $page) {
                    // ✅ รองรับทั้งข้อมูลแบบใหม่ (page) และแบบเก่า (domain)
                    $page_title = '';
                    $page_url = '';

                    if (isset($page->page_title) && isset($page->page_url)) {
                        // ข้อมูลแบบใหม่ (page data)
                        $page_title = $page->page_title ?? 'ไม่ระบุ';
                        $page_url = $page->page_url ?? '';
                    } else {
                        // ข้อมูลแบบเก่า (domain data)
                        $page_title = $page->domain_name ?? 'ไม่ระบุ';
                        $page_url = $page->domain_name ?? '';
                    }

                    fputcsv($output, [
                        $index + 1,
                        $page_title,
                        $page_url,
                        $page->total_views ?? 0,
                        $page->unique_visitors ?? 0
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // สถิติเบราว์เซอร์
            if (!empty($summary_data['browser_stats'])) {
                fputcsv($output, ['=== สถิติเบราว์เซอร์ ==='], ',', '"');
                fputcsv($output, ['อันดับ', 'เบราว์เซอร์', 'จำนวนผู้ใช้', 'เปอร์เซ็นต์'], ',', '"');

                $total_browsers = array_sum(array_column($summary_data['browser_stats'], 'count'));
                foreach ($summary_data['browser_stats'] as $index => $browser) {
                    $percentage = $total_browsers > 0 ? ($browser->count / $total_browsers) * 100 : 0;
                    fputcsv($output, [
                        $index + 1,
                        $browser->browser ?? 'N/A',
                        $browser->count ?? 0,
                        number_format($percentage, 1) . '%'
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // สถิติอุปกรณ์
            if (!empty($summary_data['device_stats'])) {
                fputcsv($output, ['=== สถิติอุปกรณ์ ==='], ',', '"');
                fputcsv($output, ['อันดับ', 'ประเภทอุปกรณ์', 'จำนวนผู้ใช้', 'เปอร์เซ็นต์'], ',', '"');

                $total_devices = array_sum(array_column($summary_data['device_stats'], 'count'));
                foreach ($summary_data['device_stats'] as $index => $device) {
                    $percentage = $total_devices > 0 ? ($device->count / $total_devices) * 100 : 0;
                    fputcsv($output, [
                        $index + 1,
                        $device->device ?? 'N/A',
                        $device->count ?? 0,
                        number_format($percentage, 1) . '%'
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // สถิติรายวัน
            if (!empty($summary_data['daily_stats'])) {
                fputcsv($output, ['=== สถิติรายวัน ==='], ',', '"');
                fputcsv($output, ['วันที่', 'การเข้าชม', 'ผู้เยี่ยมชม'], ',', '"');

                foreach ($summary_data['daily_stats'] as $daily) {
                    fputcsv($output, [
                        date('d/m/Y', strtotime($daily->date)),
                        $daily->pageviews ?? 0,
                        $daily->visitors ?? 0
                    ], ',', '"');
                }
                fputcsv($output, [''], ',', '"');
            }

            // ข้อสรุปและคำแนะนำ
            if ($data['include_recommendations'] && !empty($insights)) {
                // ข้อสรุปหลัก
                if (!empty($insights['main_summary'])) {
                    fputcsv($output, ['=== ข้อสรุปหลัก ==='], ',', '"');
                    foreach ($insights['main_summary'] as $summary) {
                        fputcsv($output, ['• ' . $summary], ',', '"');
                    }
                    fputcsv($output, [''], ',', '"');
                }

                // การวิเคราะห์เวลา
                if (!empty($insights['time_analysis'])) {
                    fputcsv($output, ['=== การวิเคราะห์เวลา ==='], ',', '"');
                    foreach ($insights['time_analysis'] as $analysis) {
                        fputcsv($output, ['• ' . $analysis], ',', '"');
                    }
                    fputcsv($output, [''], ',', '"');
                }

                // การวิเคราะห์เทคโนโลยี
                if (!empty($insights['technology_analysis'])) {
                    fputcsv($output, ['=== การวิเคราะห์เทคโนโลยี ==='], ',', '"');
                    foreach ($insights['technology_analysis'] as $analysis) {
                        fputcsv($output, ['• ' . $analysis], ',', '"');
                    }
                    fputcsv($output, [''], ',', '"');
                }

                // คำแนะนำ
                if (!empty($insights['recommendations'])) {
                    fputcsv($output, ['=== คำแนะนำเพื่อการปรับปรุง ==='], ',', '"');
                    foreach ($insights['recommendations'] as $recommendation) {
                        fputcsv($output, ['• ' . $recommendation], ',', '"');
                    }
                }
            }

            fclose($output);

            log_message('info', 'CSV export completed successfully: ' . $filename);

        } catch (Exception $e) {
            log_message('error', 'CSV export error: ' . $e->getMessage());

            // ถ้า output stream ยังเปิดอยู่ให้ปิด
            if (isset($output) && is_resource($output)) {
                fclose($output);
            }

            throw new Exception('ไม่สามารถส่งออกไฟล์ CSV ได้: ' . $e->getMessage());
        }
    }


    /**
     * 🆕 ส่งออกรายงานสรุปเป็น Excel
     */
    private function export_summary_excel($data, $filename)
    {
        // ตรวจสอบ PhpSpreadsheet
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            log_message('warning', 'PhpSpreadsheet not available, fallback to CSV');
            $csv_filename = str_replace(['.excel', '.xlsx'], '.csv', $filename);
            $this->export_summary_csv($data, $csv_filename);
            return;
        }

        try {
            $summary_data = $data['summary_data'];
            $overview = $summary_data['overview'] ?? [];
            $calculated = $summary_data['calculated_stats'] ?? [];

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // === Sheet 1: สรุปผล ===
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('สรุปผล');

            $row = 1;

            // Header
            $sheet->setCellValue('A' . $row, 'รายงานสรุปสถิติการใช้งานเว็บไซต์');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
            $row += 2;

            $sheet->setCellValue('A' . $row, 'หน่วยงาน:');
            $sheet->setCellValue('B' . $row, $data['tenant_name']);
            $row++;
            $sheet->setCellValue('A' . $row, 'ช่วงเวลา:');
            $sheet->setCellValue('B' . $row, $data['period_info']);
            $row++;
            $sheet->setCellValue('A' . $row, 'วันที่ออกรายงาน:');
            $sheet->setCellValue('B' . $row, $data['export_date']);
            $row += 2;

            // สรุปผลหลัก
            $sheet->setCellValue('A' . $row, 'สรุปผลหลัก');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $row++;

            $sheet->setCellValue('A' . $row, 'รายการ');
            $sheet->setCellValue('B' . $row, 'จำนวน');
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $row++;

            $main_stats = [
                'การเข้าชมทั้งหมด' => $overview['total_pageviews'] ?? 0,
                'ผู้เยี่ยมชมทั้งหมด' => $overview['total_visitors'] ?? 0,
                'เว็บไซต์ทั้งหมด' => $overview['total_domains'] ?? 0,
                'ผู้ใช้ออนไลน์' => $overview['online_users'] ?? 0
            ];

            foreach ($main_stats as $label => $value) {
                $sheet->setCellValue('A' . $row, $label);
                $sheet->setCellValue('B' . $row, number_format($value));
                $row++;
            }
            $row++;

            // สถิติเพิ่มเติม
            $sheet->setCellValue('A' . $row, 'สถิติเพิ่มเติม');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $row++;

            $sheet->setCellValue('A' . $row, 'รายการ');
            $sheet->setCellValue('B' . $row, 'ค่า');
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $row++;

            $additional_stats = [
                'เฉลี่ยหน้าต่อผู้เยี่ยมชม' => number_format($calculated['avg_pages_per_visitor'] ?? 0, 2) . ' หน้า',
                'ประมาณการอัตราการเด้งออก' => number_format($calculated['bounce_rate_estimate'] ?? 0, 1) . '%',
                'เฉลี่ยการเข้าชมต่อวัน' => number_format($calculated['avg_daily_pageviews'] ?? 0) . ' ครั้ง',
                'เฉลี่ยผู้เยี่ยมชมต่อวัน' => number_format($calculated['avg_daily_visitors'] ?? 0) . ' คน',
                'วันที่มีการเข้าชมสูงสุด' => number_format($calculated['peak_day_pageviews'] ?? 0) . ' ครั้ง',
                'วันที่มีผู้เยี่ยมชมมากสุด' => number_format($calculated['peak_day_visitors'] ?? 0) . ' คน'
            ];

            if (!empty($calculated['peak_hour'])) {
                $additional_stats['ช่วงเวลาคึกคัก'] = $calculated['peak_hour'] . ' (' . number_format($calculated['peak_hour_visits'] ?? 0) . ' ครั้ง)';
                $additional_stats['ช่วงเวลาเงียบ'] = $calculated['quiet_hour'] . ' (' . number_format($calculated['quiet_hour_visits'] ?? 0) . ' ครั้ง)';
            }

            foreach ($additional_stats as $label => $value) {
                $sheet->setCellValue('A' . $row, $label);
                $sheet->setCellValue('B' . $row, $value);
                $row++;
            }

            // Auto-size columns
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);

            // === Sheet 2: เว็บไซต์ยอดนิยม ===
            if (!empty($summary_data['top_domains'])) {
                $domainsSheet = $spreadsheet->createSheet();
                $domainsSheet->setTitle('เว็บไซต์ยอดนิยม');

                $domainsSheet->setCellValue('A1', 'เว็บไซต์ยอดนิยม');
                $domainsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $domainsSheet->setCellValue('A3', 'อันดับ');
                $domainsSheet->setCellValue('B3', 'ชื่อเว็บไซต์');
                $domainsSheet->setCellValue('C3', 'การเข้าชม');
                $domainsSheet->setCellValue('D3', 'ผู้เยี่ยมชม');
                $domainsSheet->getStyle('A3:D3')->getFont()->setBold(true);

                $row = 4;
                foreach ($summary_data['top_domains'] as $index => $domain) {
                    $domainsSheet->setCellValue('A' . $row, $index + 1);
                    $domainsSheet->setCellValue('B' . $row, $domain->domain_name ?? 'N/A');
                    $domainsSheet->setCellValue('C' . $row, $domain->total_views ?? 0);
                    $domainsSheet->setCellValue('D' . $row, $domain->unique_visitors ?? 0);
                    $row++;
                }

                // Auto-size columns
                foreach (['A', 'B', 'C', 'D'] as $col) {
                    $domainsSheet->getColumnDimension($col)->setAutoSize(true);
                }
            }

            // === Sheet 3: สถิติเบราว์เซอร์ ===
            if (!empty($summary_data['browser_stats'])) {
                $browserSheet = $spreadsheet->createSheet();
                $browserSheet->setTitle('สถิติเบราว์เซอร์');

                $browserSheet->setCellValue('A1', 'สถิติเบราว์เซอร์');
                $browserSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $browserSheet->setCellValue('A3', 'อันดับ');
                $browserSheet->setCellValue('B3', 'เบราว์เซอร์');
                $browserSheet->setCellValue('C3', 'จำนวนผู้ใช้');
                $browserSheet->setCellValue('D3', 'เปอร์เซ็นต์');
                $browserSheet->getStyle('A3:D3')->getFont()->setBold(true);

                $total_browsers = array_sum(array_column($summary_data['browser_stats'], 'count'));
                $row = 4;
                foreach ($summary_data['browser_stats'] as $index => $browser) {
                    $percentage = $total_browsers > 0 ? ($browser->count / $total_browsers) * 100 : 0;
                    $browserSheet->setCellValue('A' . $row, $index + 1);
                    $browserSheet->setCellValue('B' . $row, $browser->browser ?? 'N/A');
                    $browserSheet->setCellValue('C' . $row, $browser->count ?? 0);
                    $browserSheet->setCellValue('D' . $row, number_format($percentage, 1) . '%');
                    $row++;
                }

                // Auto-size columns
                foreach (['A', 'B', 'C', 'D'] as $col) {
                    $browserSheet->getColumnDimension($col)->setAutoSize(true);
                }
            }

            // === Sheet 4: สถิติอุปกรณ์ ===
            if (!empty($summary_data['device_stats'])) {
                $deviceSheet = $spreadsheet->createSheet();
                $deviceSheet->setTitle('สถิติอุปกรณ์');

                $deviceSheet->setCellValue('A1', 'สถิติอุปกรณ์');
                $deviceSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $deviceSheet->setCellValue('A3', 'อันดับ');
                $deviceSheet->setCellValue('B3', 'ประเภทอุปกรณ์');
                $deviceSheet->setCellValue('C3', 'จำนวนผู้ใช้');
                $deviceSheet->setCellValue('D3', 'เปอร์เซ็นต์');
                $deviceSheet->getStyle('A3:D3')->getFont()->setBold(true);

                $total_devices = array_sum(array_column($summary_data['device_stats'], 'count'));
                $row = 4;
                foreach ($summary_data['device_stats'] as $index => $device) {
                    $percentage = $total_devices > 0 ? ($device->count / $total_devices) * 100 : 0;
                    $deviceSheet->setCellValue('A' . $row, $index + 1);
                    $deviceSheet->setCellValue('B' . $row, $device->device ?? 'N/A');
                    $deviceSheet->setCellValue('C' . $row, $device->count ?? 0);
                    $deviceSheet->setCellValue('D' . $row, number_format($percentage, 1) . '%');
                    $row++;
                }

                // Auto-size columns
                foreach (['A', 'B', 'C', 'D'] as $col) {
                    $deviceSheet->getColumnDimension($col)->setAutoSize(true);
                }
            }

            // === Sheet 5: สถิติรายวัน ===
            if (!empty($summary_data['daily_stats'])) {
                $dailySheet = $spreadsheet->createSheet();
                $dailySheet->setTitle('สถิติรายวัน');

                $dailySheet->setCellValue('A1', 'สถิติรายวัน');
                $dailySheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $dailySheet->setCellValue('A3', 'วันที่');
                $dailySheet->setCellValue('B3', 'การเข้าชม');
                $dailySheet->setCellValue('C3', 'ผู้เยี่ยมชม');
                $dailySheet->getStyle('A3:C3')->getFont()->setBold(true);

                $row = 4;
                foreach ($summary_data['daily_stats'] as $daily) {
                    $dailySheet->setCellValue('A' . $row, date('d/m/Y', strtotime($daily->date)));
                    $dailySheet->setCellValue('B' . $row, $daily->pageviews ?? 0);
                    $dailySheet->setCellValue('C' . $row, $daily->visitors ?? 0);
                    $row++;
                }

                // Auto-size columns
                foreach (['A', 'B', 'C'] as $col) {
                    $dailySheet->getColumnDimension($col)->setAutoSize(true);
                }
            }

            // === Sheet 6: ข้อสรุปและคำแนะนำ ===
            if ($data['include_recommendations'] && !empty($summary_data['insights'])) {
                $insightsSheet = $spreadsheet->createSheet();
                $insightsSheet->setTitle('ข้อสรุปและคำแนะนำ');

                $row = 1;
                $insightsSheet->setCellValue('A' . $row, 'ข้อสรุปและคำแนะนำ');
                $insightsSheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $row += 2;

                $insights = $summary_data['insights'];

                // ข้อสรุปหลัก
                if (!empty($insights['main_summary'])) {
                    $insightsSheet->setCellValue('A' . $row, 'ข้อสรุปหลัก');
                    $insightsSheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    foreach ($insights['main_summary'] as $summary) {
                        $insightsSheet->setCellValue('A' . $row, '• ' . $summary);
                        $row++;
                    }
                    $row++;
                }

                // การวิเคราะห์เวลา
                if (!empty($insights['time_analysis'])) {
                    $insightsSheet->setCellValue('A' . $row, 'การวิเคราะห์เวลา');
                    $insightsSheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    foreach ($insights['time_analysis'] as $analysis) {
                        $insightsSheet->setCellValue('A' . $row, '• ' . $analysis);
                        $row++;
                    }
                    $row++;
                }

                // การวิเคราะห์เทคโนโลยี
                if (!empty($insights['technology_analysis'])) {
                    $insightsSheet->setCellValue('A' . $row, 'การวิเคราะห์เทคโนโลยี');
                    $insightsSheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    foreach ($insights['technology_analysis'] as $analysis) {
                        $insightsSheet->setCellValue('A' . $row, '• ' . $analysis);
                        $row++;
                    }
                    $row++;
                }

                // คำแนะนำ
                if (!empty($insights['recommendations'])) {
                    $insightsSheet->setCellValue('A' . $row, 'คำแนะนำเพื่อการปรับปรุง');
                    $insightsSheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    foreach ($insights['recommendations'] as $recommendation) {
                        $insightsSheet->setCellValue('A' . $row, '• ' . $recommendation);
                        $row++;
                    }
                }

                // Auto-size column
                $insightsSheet->getColumnDimension('A')->setWidth(80);
            }

            // ตั้งค่าการแสดงผล
            $spreadsheet->setActiveSheetIndex(0);

            // ส่งออกไฟล์
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');

        } catch (Exception $e) {
            log_message('error', 'Excel export error: ' . $e->getMessage());
            // Fallback เป็น CSV
            $csv_filename = str_replace(['.excel', '.xlsx'], '.csv', $filename);
            $this->export_summary_csv($data, $csv_filename);
        }
    }

    /**
     * 🆕 ส่งออกรายงานสรุปเป็น Word Document
     */
    private function export_summary_word($data, $filename)
    {
        try {
            // ตรวจสอบ PhpWord library
            $phpword_path = APPPATH . 'third_party/phpword/vendor/autoload.php';
            if (!file_exists($phpword_path)) {
                log_message('warning', 'PhpWord not available, fallback to PDF');
                $pdf_filename = str_replace(['.word', '.docx'], '.pdf', $filename);
                $this->export_summary_pdf($data, $pdf_filename);
                return;
            }

            require_once($phpword_path);

            $summary_data = $data['summary_data'];
            $overview = $summary_data['overview'] ?? [];
            $calculated = $summary_data['calculated_stats'] ?? [];
            $insights = $summary_data['insights'] ?? [];

            // สร้าง Document
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->setDefaultFontName('TH SarabunPSK');
            $phpWord->setDefaultFontSize(16);

            // กำหนด styles
            $phpWord->addTitleStyle(1, ['size' => 20, 'bold' => true, 'color' => '1e3d59']);
            $phpWord->addTitleStyle(2, ['size' => 18, 'bold' => true, 'color' => '2e74b5']);
            $phpWord->addTitleStyle(3, ['size' => 16, 'bold' => true, 'color' => '70ad47']);

            // สร้าง section
            $section = $phpWord->addSection([
                'marginTop' => 800,
                'marginBottom' => 800,
                'marginLeft' => 800,
                'marginRight' => 800
            ]);

            // Header
            $section->addTitle('รายงานสรุปสถิติการใช้งานเว็บไซต์', 1);
            $section->addTextBreak();

            // ข้อมูลรายงาน
            $infoTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
            $infoTable->addRow();
            $infoTable->addCell(3000)->addText('หน่วยงาน:', ['bold' => true]);
            $infoTable->addCell(6000)->addText($data['tenant_name']);

            $infoTable->addRow();
            $infoTable->addCell(3000)->addText('ช่วงเวลา:', ['bold' => true]);
            $infoTable->addCell(6000)->addText($data['period_info']);

            $infoTable->addRow();
            $infoTable->addCell(3000)->addText('วันที่ออกรายงาน:', ['bold' => true]);
            $infoTable->addCell(6000)->addText($data['export_date']);

            $section->addTextBreak(2);

            // สรุปผลหลัก
            $section->addTitle('สรุปผลหลัก', 2);

            $summaryTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);

            // Header row
            $summaryTable->addRow();
            $summaryTable->addCell(4000, ['bgColor' => 'e7f3ff'])->addText('รายการ', ['bold' => true]);
            $summaryTable->addCell(3000, ['bgColor' => 'e7f3ff'])->addText('จำนวน', ['bold' => true]);

            // Data rows
            $main_stats = [
                'การเข้าชมทั้งหมด' => number_format($overview['total_pageviews'] ?? 0),
                'ผู้เยี่ยมชมทั้งหมด' => number_format($overview['total_visitors'] ?? 0),
                'เว็บไซต์ทั้งหมด' => number_format($overview['total_domains'] ?? 0),
                'ผู้ใช้ออนไลน์' => number_format($overview['online_users'] ?? 0)
            ];

            foreach ($main_stats as $label => $value) {
                $summaryTable->addRow();
                $summaryTable->addCell(4000)->addText($label);
                $summaryTable->addCell(3000)->addText($value, ['bold' => true, 'color' => '2e74b5']);
            }

            $section->addTextBreak();

            // สถิติเพิ่มเติม
            $section->addTitle('สถิติเพิ่มเติม', 3);

            $additionalTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);

            // Header row
            $additionalTable->addRow();
            $additionalTable->addCell(5000, ['bgColor' => 'fff2cc'])->addText('รายการ', ['bold' => true]);
            $additionalTable->addCell(3000, ['bgColor' => 'fff2cc'])->addText('ค่า', ['bold' => true]);

            $additional_stats = [
                'เฉลี่ยหน้าต่อผู้เยี่ยมชม' => number_format($calculated['avg_pages_per_visitor'] ?? 0, 2) . ' หน้า',
                'ประมาณการอัตราการเด้งออก' => number_format($calculated['bounce_rate_estimate'] ?? 0, 1) . '%',
                'เฉลี่ยการเข้าชมต่อวัน' => number_format($calculated['avg_daily_pageviews'] ?? 0) . ' ครั้ง',
                'เฉลี่ยผู้เยี่ยมชมต่อวัน' => number_format($calculated['avg_daily_visitors'] ?? 0) . ' คน'
            ];

            if (!empty($calculated['peak_hour'])) {
                $additional_stats['ช่วงเวลาคึกคัก'] = $calculated['peak_hour'] . ' (' . number_format($calculated['peak_hour_visits'] ?? 0) . ' ครั้ง)';
                $additional_stats['ช่วงเวลาเงียบ'] = $calculated['quiet_hour'] . ' (' . number_format($calculated['quiet_hour_visits'] ?? 0) . ' ครั้ง)';
            }

            foreach ($additional_stats as $label => $value) {
                $additionalTable->addRow();
                $additionalTable->addCell(5000)->addText($label);
                $additionalTable->addCell(3000)->addText($value, ['bold' => true]);
            }

            // เว็บไซต์ยอดนิยม
            if (!empty($summary_data['top_domains'])) {
                $section->addPageBreak();
                $section->addTitle('เว็บไซต์ยอดนิยม', 2);

                $domainsTable = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);

                // Header
                $domainsTable->addRow();
                $domainsTable->addCell(1000, ['bgColor' => 'e7f3ff'])->addText('อันดับ', ['bold' => true]);
                $domainsTable->addCell(4000, ['bgColor' => 'e7f3ff'])->addText('ชื่อเว็บไซต์', ['bold' => true]);
                $domainsTable->addCell(2000, ['bgColor' => 'e7f3ff'])->addText('การเข้าชม', ['bold' => true]);
                $domainsTable->addCell(2000, ['bgColor' => 'e7f3ff'])->addText('ผู้เยี่ยมชม', ['bold' => true]);

                foreach (array_slice($summary_data['top_domains'], 0, 15) as $index => $domain) {
                    $domainsTable->addRow();
                    $domainsTable->addCell(1000)->addText($index + 1);
                    $domainsTable->addCell(4000)->addText($domain->domain_name ?? 'N/A');
                    $domainsTable->addCell(2000)->addText(number_format($domain->total_views ?? 0));
                    $domainsTable->addCell(2000)->addText(number_format($domain->unique_visitors ?? 0));
                }
            }

            // ข้อสรุปและคำแนะนำ
            if ($data['include_recommendations'] && !empty($insights)) {
                $section->addPageBreak();
                $section->addTitle('ข้อสรุปและการวิเคราะห์', 2);

                if (!empty($insights['main_summary'])) {
                    $section->addTitle('ข้อสรุปหลัก', 3);
                    foreach ($insights['main_summary'] as $summary) {
                        $section->addListItem($summary, 0, null, 'multilevel');
                    }
                    $section->addTextBreak();
                }

                if (!empty($insights['time_analysis'])) {
                    $section->addTitle('การวิเคราะห์เวลา', 3);
                    foreach ($insights['time_analysis'] as $analysis) {
                        $section->addListItem($analysis, 0, null, 'multilevel');
                    }
                    $section->addTextBreak();
                }

                if (!empty($insights['technology_analysis'])) {
                    $section->addTitle('การวิเคราะห์เทคโนโลยี', 3);
                    foreach ($insights['technology_analysis'] as $analysis) {
                        $section->addListItem($analysis, 0, null, 'multilevel');
                    }
                    $section->addTextBreak();
                }

                if (!empty($insights['recommendations'])) {
                    $section->addTitle('คำแนะนำเพื่อการปรับปรุง', 3);
                    foreach ($insights['recommendations'] as $recommendation) {
                        $section->addListItem($recommendation, 0, null, 'multilevel');
                    }
                }
            }

            // ส่งออกไฟล์
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');

        } catch (Exception $e) {
            log_message('error', 'Word export error: ' . $e->getMessage());
            // Fallback เป็น PDF
            $pdf_filename = str_replace(['.word', '.docx'], '.pdf', $filename);
            $this->export_summary_pdf($data, $pdf_filename);
        }
    }


    public function ajax_filter_stats()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $this->load->model('External_stats_model');

            // รับ parameters จาก request
            $period = $this->input->get('period') ?: '7days';
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            log_message('info', 'AJAX Filter Stats - Period: ' . $period .
                ', Start: ' . $start_date .
                ', End: ' . $end_date);

            // ตรวจสอบการเชื่อมต่อ
            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            if (!$current_tenant) {
                echo json_encode([
                    'success' => false,
                    'error' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้',
                    'debug_info' => [
                        'tenant_code' => $this->tenant_code,
                        'current_domain' => $this->current_domain
                    ]
                ]);
                return;
            }

            // กำหนด period สำหรับ query (ใช้ฟังก์ชันใหม่)
            $query_period = $this->determine_filter_period($period, $start_date, $end_date);

            log_message('info', 'Query period determined: ' . json_encode($query_period));

            // ดึงข้อมูลสถิติ (ส่ง query_period ไปยัง model)
            $response_data = [
                'success' => true,
                'data' => [
                    'stats_summary' => $this->External_stats_model->get_stats_summary($query_period),
                    'top_domains' => $this->External_stats_model->get_top_domains(10, $query_period),
                    'daily_stats' => $this->External_stats_model->get_daily_stats($query_period),
                    'device_stats' => $this->External_stats_model->get_device_summary($query_period),
                    'platform_stats' => $this->External_stats_model->get_platform_summary($query_period),
                    'hourly_stats' => $this->External_stats_model->get_hourly_visits($query_period),
                    'browser_stats' => $this->External_stats_model->get_browser_stats($query_period),
                    'country_stats' => $this->External_stats_model->get_country_stats($query_period)
                ],
                'period' => $period,
                'query_period' => $query_period,
                'debug_info' => [
                    'original_period' => $period,
                    'original_start' => $start_date,
                    'original_end' => $end_date,
                    'calculated_period' => $query_period,
                    'today' => date('Y-m-d')
                ],
                'tenant_info' => [
                    'requested' => $this->tenant_code,
                    'resolved' => $current_tenant,
                    'domain' => $this->current_domain
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // คำนวณสถิติเพิ่มเติม
            $calculated_stats = $this->calculate_additional_stats($response_data['data']);
            $response_data['data']['calculated_stats'] = $calculated_stats;

            echo json_encode($response_data);

        } catch (Exception $e) {
            log_message('error', 'AJAX Filter Stats Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage(),
                'debug_info' => [
                    'tenant_code' => $this->tenant_code,
                    'current_domain' => $this->current_domain,
                    'error_details' => $e->getMessage(),
                    'period' => $period ?? 'undefined'
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }



    public function debug_period()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        header('Content-Type: application/json');

        $period = $this->input->get('period') ?: '7days';
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $query_period = $this->determine_filter_period($period, $start_date, $end_date);

        echo json_encode([
            'success' => true,
            'input' => [
                'period' => $period,
                'start_date' => $start_date,
                'end_date' => $end_date
            ],
            'calculated' => $query_period,
            'today' => date('Y-m-d'),
            'examples' => [
                'today' => $this->get_predefined_period('today'),
                '7days' => $this->get_predefined_period('7days'),
                '30days' => $this->get_predefined_period('30days'),
                'current_month' => $this->get_predefined_period('current_month')
            ],
            'validation' => [
                'start_date_valid' => $start_date ? $this->validate_date_format($start_date) : null,
                'end_date_valid' => $end_date ? $this->validate_date_format($end_date) : null
            ]
        ]);
    }

    /**
     * 🆕 กำหนด period สำหรับ filter
     */
    private function determine_filter_period($period, $start_date = null, $end_date = null)
    {
        $today = date('Y-m-d');

        log_message('info', 'determine_filter_period - Input: period=' . $period .
            ', start_date=' . $start_date .
            ', end_date=' . $end_date);

        // ถ้าเป็น custom date range และมีข้อมูลครบ
        if ($period === 'custom' && !empty($start_date) && !empty($end_date)) {
            // ตรวจสอบรูปแบบวันที่
            if ($this->validate_date_format($start_date) && $this->validate_date_format($end_date)) {
                $result = [
                    'type' => 'custom',
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ];
                log_message('info', 'Custom period result: ' . json_encode($result));
                return $result;
            } else {
                log_message('warning', 'Invalid custom date format, using 7days fallback');
                return $this->get_predefined_period('7days');
            }
        }

        // ถ้าเป็น predefined period
        $valid_periods = ['today', '7days', '30days', '90days', 'current_month'];
        if (in_array($period, $valid_periods)) {
            $result = $this->get_predefined_period($period);
            log_message('info', 'Predefined period result: ' . json_encode($result));
            return $result;
        }

        // Fallback เป็น 7days
        log_message('warning', 'Invalid period: ' . $period . ', using 7days fallback');
        return $this->get_predefined_period('7days');
    }

    private function get_predefined_period($period)
    {
        $today = date('Y-m-d');

        switch ($period) {
            case 'today':
                return [
                    'type' => 'predefined',
                    'period' => 'today',
                    'start_date' => $today,
                    'end_date' => $today
                ];

            case '7days':
                return [
                    'type' => 'predefined',
                    'period' => '7days',
                    'start_date' => date('Y-m-d', strtotime('-6 days')), // 6 วันก่อน + วันนี้ = 7 วัน
                    'end_date' => $today
                ];

            case '30days':
                return [
                    'type' => 'predefined',
                    'period' => '30days',
                    'start_date' => date('Y-m-d', strtotime('-29 days')), // 29 วันก่อน + วันนี้ = 30 วัน
                    'end_date' => $today
                ];

            case '90days':
                return [
                    'type' => 'predefined',
                    'period' => '90days',
                    'start_date' => date('Y-m-d', strtotime('-89 days')), // 89 วันก่อน + วันนี้ = 90 วัน
                    'end_date' => $today
                ];

            case 'current_month':
                return [
                    'type' => 'predefined',
                    'period' => 'current_month',
                    'start_date' => date('Y-m-01'), // วันที่ 1 ของเดือนปัจจุบัน
                    'end_date' => $today
                ];

            default:
                // Fallback เป็น 7days
                return [
                    'type' => 'predefined',
                    'period' => '7days',
                    'start_date' => date('Y-m-d', strtotime('-6 days')),
                    'end_date' => $today
                ];
        }
    }

    /**
     * 🆕 ตรวจสอบรูปแบบวันที่
     */
    private function validate_date_format($date)
    {
        if (empty($date)) {
            return false;
        }

        // ตรวจสอบรูปแบบ YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return false;
            }

            // ตรวจสอบว่าวันที่ถูกต้องจริง
            $formatted_date = date('Y-m-d', $timestamp);
            if ($formatted_date !== $date) {
                return false;
            }

            // ตรวจสอบว่าไม่เป็นวันที่ในอนาคต
            $today = date('Y-m-d');
            if ($date > $today) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * 🆕 คำนวณสถิติเพิ่มเติมสำหรับ filter
     */
    /**
     * ✅ แก้ไข: calculate_additional_stats - เวอร์ชันสมบูรณ์
     */
    private function calculate_additional_stats($data)
    {
        $calculated = [];

        try {
            // ข้อมูลพื้นฐาน - ป้องกัน null/undefined
            $overview = isset($data['stats_summary']) ? $data['stats_summary'] : ($data['overview'] ?? []);
            $calculated['total_pageviews'] = (int) ($overview['total_pageviews'] ?? 0);
            $calculated['total_visitors'] = (int) ($overview['total_visitors'] ?? 0);
            $calculated['online_users'] = (int) ($overview['online_users'] ?? 0);

            // คำนวณอัตราต่างๆ
            if ($calculated['total_visitors'] > 0) {
                $calculated['avg_pages_per_visitor'] = round($calculated['total_pageviews'] / $calculated['total_visitors'], 2);
                $calculated['bounce_rate_estimate'] = max(0, min(100, round((1 - ($calculated['avg_pages_per_visitor'] - 1) / 3) * 100, 1)));
            } else {
                $calculated['avg_pages_per_visitor'] = 0;
                $calculated['bounce_rate_estimate'] = 0;
            }

            // สถิติจากข้อมูลรายวัน
            $daily_stats = $data['daily_stats'] ?? [];
            if (!empty($daily_stats) && is_array($daily_stats)) {
                $daily_pageviews = array_map(function ($item) {
                    return (int) ($item->pageviews ?? 0);
                }, $daily_stats);

                $daily_visitors = array_map(function ($item) {
                    return (int) ($item->visitors ?? 0);
                }, $daily_stats);

                $calculated['avg_daily_pageviews'] = count($daily_pageviews) > 0 ? round(array_sum($daily_pageviews) / count($daily_pageviews)) : 0;
                $calculated['avg_daily_visitors'] = count($daily_visitors) > 0 ? round(array_sum($daily_visitors) / count($daily_visitors)) : 0;
                $calculated['peak_day_pageviews'] = count($daily_pageviews) > 0 ? max($daily_pageviews) : 0;
                $calculated['peak_day_visitors'] = count($daily_visitors) > 0 ? max($daily_visitors) : 0;

                // หาวันที่มีการเข้าชมสูงสุด
                if (!empty($daily_pageviews)) {
                    $max_index = array_search(max($daily_pageviews), $daily_pageviews);
                    if ($max_index !== false && isset($daily_stats[$max_index])) {
                        $calculated['peak_date'] = date('d/m/Y', strtotime($daily_stats[$max_index]->date));
                    }
                }
            } else {
                $calculated['avg_daily_pageviews'] = 0;
                $calculated['avg_daily_visitors'] = 0;
                $calculated['peak_day_pageviews'] = 0;
                $calculated['peak_day_visitors'] = 0;
                $calculated['peak_date'] = '';
            }

            // สถิติเบราว์เซอร์
            $browser_stats = $data['browser_stats'] ?? [];
            if (!empty($browser_stats) && is_array($browser_stats)) {
                $total_browser_users = array_sum(array_map(function ($item) {
                    return (int) ($item->count ?? 0);
                }, $browser_stats));

                $calculated['browser_diversity'] = count($browser_stats);
                $calculated['top_browser'] = $browser_stats[0]->browser ?? 'ไม่ระบุ';
                $calculated['top_browser_percentage'] = $total_browser_users > 0 ?
                    round(($browser_stats[0]->count ?? 0) / $total_browser_users * 100, 1) : 0;
            } else {
                $calculated['browser_diversity'] = 0;
                $calculated['top_browser'] = 'ไม่ระบุ';
                $calculated['top_browser_percentage'] = 0;
            }

            // สถิติอุปกรณ์
            $device_stats = $data['device_stats'] ?? [];
            if (!empty($device_stats) && is_array($device_stats)) {
                $total_device_users = array_sum(array_map(function ($item) {
                    return (int) ($item->count ?? 0);
                }, $device_stats));

                $calculated['device_diversity'] = count($device_stats);
                $calculated['top_device'] = $device_stats[0]->device ?? 'ไม่ระบุ';
                $calculated['top_device_percentage'] = $total_device_users > 0 ?
                    round(($device_stats[0]->count ?? 0) / $total_device_users * 100, 1) : 0;

                // คำนวณ Mobile vs Desktop
                $mobile_count = 0;
                $desktop_count = 0;
                foreach ($device_stats as $device) {
                    $device_name = strtolower($device->device ?? '');
                    if (strpos($device_name, 'mobile') !== false || strpos($device_name, 'phone') !== false) {
                        $mobile_count += (int) ($device->count ?? 0);
                    } elseif (strpos($device_name, 'desktop') !== false) {
                        $desktop_count += (int) ($device->count ?? 0);
                    }
                }

                $calculated['mobile_percentage'] = $total_device_users > 0 ? round($mobile_count / $total_device_users * 100, 1) : 0;
                $calculated['desktop_percentage'] = $total_device_users > 0 ? round($desktop_count / $total_device_users * 100, 1) : 0;
            } else {
                $calculated['device_diversity'] = 0;
                $calculated['top_device'] = 'ไม่ระบุ';
                $calculated['top_device_percentage'] = 0;
                $calculated['mobile_percentage'] = 0;
                $calculated['desktop_percentage'] = 0;
            }

            // สถิติชั่วโมง
            $hourly_stats = $data['hourly_stats'] ?? [];
            if (!empty($hourly_stats) && is_array($hourly_stats)) {
                $hourly_counts = array_map(function ($item) {
                    return (int) ($item->count ?? 0);
                }, $hourly_stats);

                if (!empty($hourly_counts)) {
                    $peak_hour_index = array_search(max($hourly_counts), $hourly_counts);
                    $quiet_hour_index = array_search(min($hourly_counts), $hourly_counts);

                    $calculated['peak_hour'] = sprintf('%02d:00-%02d:59', $peak_hour_index, $peak_hour_index);
                    $calculated['quiet_hour'] = sprintf('%02d:00-%02d:59', $quiet_hour_index, $quiet_hour_index);
                    $calculated['peak_hour_visits'] = max($hourly_counts);
                    $calculated['quiet_hour_visits'] = min($hourly_counts);
                }
            } else {
                $calculated['peak_hour'] = '';
                $calculated['quiet_hour'] = '';
                $calculated['peak_hour_visits'] = 0;
                $calculated['quiet_hour_visits'] = 0;
            }

            // สถิติประเทศ
            $country_stats = $data['country_stats'] ?? [];
            if (!empty($country_stats) && is_array($country_stats)) {
                $calculated['top_country'] = $country_stats[0]->country ?? 'ไม่ระบุ';
                $total_country_users = array_sum(array_map(function ($item) {
                    return (int) ($item->count ?? 0);
                }, $country_stats));
                $calculated['top_country_percentage'] = $total_country_users > 0 ?
                    round(($country_stats[0]->count ?? 0) / $total_country_users * 100, 1) : 0;
            } else {
                $calculated['top_country'] = 'ไม่ระบุ';
                $calculated['top_country_percentage'] = 0;
            }

            return $calculated;

        } catch (Exception $e) {
            log_message('error', 'Error in calculate_additional_stats: ' . $e->getMessage());

            // Return default values
            return [
                'total_pageviews' => 0,
                'total_visitors' => 0,
                'online_users' => 0,
                'avg_pages_per_visitor' => 0,
                'bounce_rate_estimate' => 0,
                'avg_daily_pageviews' => 0,
                'avg_daily_visitors' => 0,
                'peak_day_pageviews' => 0,
                'peak_day_visitors' => 0,
                'peak_date' => '',
                'browser_diversity' => 0,
                'top_browser' => 'ไม่ระบุ',
                'top_browser_percentage' => 0,
                'device_diversity' => 0,
                'top_device' => 'ไม่ระบุ',
                'top_device_percentage' => 0,
                'mobile_percentage' => 0,
                'desktop_percentage' => 0,
                'peak_hour' => '',
                'quiet_hour' => '',
                'peak_hour_visits' => 0,
                'quiet_hour_visits' => 0,
                'top_country' => 'ไม่ระบุ',
                'top_country_percentage' => 0
            ];
        }
    }

    /**
     * ✅ เพิ่ม: ฟังก์ชันสำหรับ debug การส่งออก
     */
    public function debug_export()
    {
        header('Content-Type: application/json');

        try {
            $this->load->model('External_stats_model');

            $period = $this->input->get('period') ?: '7days';
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            $query_period = $this->determine_export_period($period, $start_date, $end_date);
            $model_period = $this->convert_period_for_model($query_period);

            $debug_info = [
                'input' => [
                    'period' => $period,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ],
                'query_period' => $query_period,
                'model_period' => $model_period,
                'tenant_info' => [
                    'code' => $this->tenant_code,
                    'domain' => $this->current_domain,
                    'external_tenant' => $this->External_stats_model->get_current_tenant_code()
                ],
                'data_test' => []
            ];

            // ทดสอบดึงข้อมูล
            $summary = $this->External_stats_model->get_stats_summary($model_period);
            $debug_info['data_test']['summary'] = $summary;

            $domains = $this->External_stats_model->get_top_domains(5, $model_period);
            $debug_info['data_test']['domains_count'] = count($domains ?? []);

            echo json_encode($debug_info, JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            echo json_encode([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * 🆕 หน้าสรุปสถิติพร้อม filter system
     */
    public function website_stats_summary_filtered()
    {
        try {
            // โหลด External_stats_model
            $this->load->model('External_stats_model');

            // ตรวจสอบการเชื่อมต่อ
            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            if (!$current_tenant) {
                show_error('ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้ กรุณาตรวจสอบการตั้งค่า');
            }

            // รับ period จาก URL parameter
            $period = $this->input->get('period') ?: '7days';
            $custom_start = $this->input->get('start_date');
            $custom_end = $this->input->get('end_date');

            // กำหนด period สำหรับ query เริ่มต้น
            $query_period = $this->determine_filter_period($period, $custom_start, $custom_end);

            $data['page_title'] = 'สรุปสถิติการใช้งานเว็บไซต์';
            $data['user_info'] = $this->get_user_info();
            $data['tenant_code'] = $this->tenant_code;
            $data['current_domain'] = $this->current_domain;
            $data['selected_period'] = $period;
            $data['query_period'] = $query_period;

            // ดึงข้อมูลสถิติเริ่มต้น
            $data['summary_data'] = $this->get_filtered_stats_summary($query_period);

            // เพิ่มข้อมูล debug สำหรับ system admin
            $data['debug_connection_info'] = $this->get_debug_connection_info();
            $data['is_system_admin'] = $this->is_system_admin();

            $this->load->view('reports/header', $data);
            $this->load->view('reports/website_stats_summary_filtered', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Website Stats Summary Filtered Error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการดึงข้อมูลสถิติ: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 รวบรวมข้อมูลสถิติสำหรับ filter system
     */
    private function get_filtered_stats_summary($period)
    {
        $summary = [];

        try {
            // 1. สถิติหลัก
            $summary['overview'] = $this->External_stats_model->get_stats_summary($period);

            // 2. สถิติรายวัน (สำหรับกราฟ)
            $summary['daily_stats'] = $this->External_stats_model->get_daily_stats($period);

            // 3. เว็บไซต์/โดเมนยอดนิยม
            $summary['top_domains'] = $this->External_stats_model->get_top_domains(10, $period);

            // 4. สถิติเบราว์เซอร์
            $summary['browser_stats'] = $this->External_stats_model->get_browser_stats();

            // 5. สถิติอุปกรณ์
            $summary['device_stats'] = $this->External_stats_model->get_device_summary();

            // 6. สถิติแพลตฟอร์ม
            $summary['platform_stats'] = $this->External_stats_model->get_platform_summary();

            // 7. สถิติประเทศ
            $summary['country_stats'] = $this->External_stats_model->get_country_stats();

            // 8. สถิติรายชั่วโมง
            $summary['hourly_stats'] = $this->External_stats_model->get_hourly_visits();

            // 9. คำนวณสถิติเพิ่มเติม
            $summary['calculated_stats'] = $this->calculate_additional_stats($summary);

            return $summary;

        } catch (Exception $e) {
            log_message('error', 'Error gathering filtered stats: ' . $e->getMessage());
            return $this->get_empty_summary();
        }
    }

    /**
     * 🆕 AJAX endpoint สำหรับ quick filter (แบบเร็ว)
     */
    public function ajax_quick_filter()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $this->load->model('External_stats_model');

            $period = $this->input->post('period') ?: '7days';

            // ดึงเฉพาะข้อมูลสำคัญ (เร็วกว่า)
            $quick_data = [
                'stats_summary' => $this->External_stats_model->get_stats_summary($period),
                'daily_stats' => $this->External_stats_model->get_daily_stats($period),
                'top_domains' => $this->External_stats_model->get_top_domains(5, $period)
            ];

            echo json_encode([
                'success' => true,
                'data' => $quick_data,
                'period' => $period,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Quick Filter Error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🆕 Endpoint สำหรับ export ข้อมูลที่ filter แล้ว
     */
    public function export_filtered_stats()
    {
        try {
            // รับข้อมูลจาก form
            $export_type = $this->input->post('export_type') ?: 'pdf';
            $period = $this->input->post('period') ?: '7days';
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $include_charts = $this->input->post('include_charts') === 'true';
            $include_recommendations = $this->input->post('include_recommendations') === 'true';

            // Validation
            if (!in_array($export_type, ['pdf', 'csv', 'excel', 'word'])) {
                show_error('ประเภทไฟล์ไม่ถูกต้อง');
            }

            // โหลด model และตรวจสอบการเชื่อมต่อ
            $this->load->model('External_stats_model');

            $current_tenant = $this->External_stats_model->get_current_tenant_code();
            if (!$current_tenant) {
                show_error('ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้');
            }

            // กำหนดช่วงเวลา
            $query_period = $this->determine_filter_period($period, $start_date, $end_date);

            // ดึงข้อมูลสถิติ
            $summary_data = $this->get_filtered_stats_summary($query_period);

            if (empty($summary_data['overview']['total_pageviews'])) {
                show_error('ไม่พบข้อมูลสำหรับการส่งออก กรุณาตรวจสอบช่วงวันที่');
            }

            // สร้างชื่อไฟล์
            $filename = $this->generate_filtered_filename($export_type, $query_period);

            // เพิ่มข้อมูล metadata
            $export_data = [
                'summary_data' => $summary_data,
                'period_info' => $this->get_period_description($query_period),
                'export_date' => date('d/m/Y H:i:s'),
                'tenant_code' => $this->tenant_code,
                'tenant_name' => $this->get_tenant_name(),
                'include_charts' => $include_charts,
                'include_recommendations' => $include_recommendations,
                'period' => $period
            ];

            // ส่งออกตามประเภทไฟล์
            switch ($export_type) {
                case 'pdf':
                    $this->export_summary_pdf($export_data, $filename);
                    break;
                case 'csv':
                    $this->export_summary_csv($export_data, $filename);
                    break;
                case 'excel':
                    $this->export_summary_excel($export_data, $filename);
                    break;
                case 'word':
                    $this->export_summary_word($export_data, $filename);
                    break;
            }

        } catch (Exception $e) {
            log_message('error', 'Export Filtered Stats Error: ' . $e->getMessage());
            show_error('ไม่สามารถส่งออกรายงานได้: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 สร้างชื่อไฟล์สำหรับ filtered export
     */
    private function generate_filtered_filename($export_type, $period)
    {
        $tenant_code = $this->tenant_code ?: 'website';
        $date_suffix = date('Y-m-d_His');

        $period_suffix = '';
        if (is_array($period) && $period['type'] === 'custom') {
            $period_suffix = '_custom_' . str_replace('-', '', $period['start']) . '_' . str_replace('-', '', $period['end']);
        } else {
            $period_suffix = '_' . $period;
        }

        $extension = match ($export_type) {
            'excel' => 'xlsx',
            'word' => 'docx',
            default => $export_type
        };

        return "สถิติเว็บไซต์_{$tenant_code}{$period_suffix}_{$date_suffix}.{$extension}";
    }





    /**
     * ✅ Export Preview สำหรับรายงาน Storage - เพิ่มใน System_reports Controller
     */
    public function export_excel($report_type = 'storage')
    {
        try {
            // ตรวจสอบสิทธิ์
            if (!$this->check_reports_access()) {
                show_404();
            }

            switch ($report_type) {
                case 'storage':
                    $this->export_storage_preview();
                    break;
                case 'complain':
                    $this->export_complain_preview();
                    break;
                default:
                    show_error('ประเภทรายงานไม่ถูกต้อง');
            }

        } catch (Exception $e) {
            log_message('error', 'Export Preview Error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งออกรายงาน: ' . $e->getMessage());
        }
    }

    /**
     * ✅ หน้า Preview สำหรับรายงาน Storage
     */
    private function export_storage_preview()
    {
        // อัปเดตข้อมูลก่อนส่งออก
        $this->auto_update_storage_data();

        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'รายงานพื้นที่จัดเก็บข้อมูล';
        $data['tenant_code'] = $this->tenant_code;
        $data['current_domain'] = $this->current_domain;

        // ดึงข้อมูลการใช้พื้นที่จัดเก็บ
        $data['storage_info'] = $this->Reports_model->get_storage_detailed_report();
        $data['storage_history'] = $this->Reports_model->get_storage_usage_history();
        $data['storage_by_type'] = $this->Reports_model->get_storage_usage_by_file_type();
        $data['storage_trends'] = $this->Reports_model->get_storage_trends();

        // ดึงข้อมูลจำนวนไฟล์แต่ละประเภท
        $data['file_stats'] = $this->Reports_model->get_file_statistics();

        // ข้อมูลสำหรับรายงาน
        $data['export_date'] = date('d/m/Y H:i:s');
        $data['report_type'] = 'storage';
        $data['report_title'] = 'รายงานพื้นที่จัดเก็บข้อมูล';

        // เพิ่มข้อมูลสรุป
        $data['summary_stats'] = $this->calculate_storage_summary($data);

        // โหลด view สำหรับ preview/print
        $this->load->view('reports/storage_preview', $data);
    }

    /**
     * ✅ คำนวณสรุปข้อมูล Storage
     */
    private function calculate_storage_summary($data)
    {
        $storage_info = $data['storage_info'];
        $file_stats = $data['file_stats'];

        $summary = [
            'total_space' => $storage_info['server_storage'] ?? 100,
            'used_space' => $storage_info['server_current'] ?? 0,
            'free_space' => $storage_info['free_space'] ?? 0,
            'usage_percentage' => $storage_info['percentage_used'] ?? 0,
            'total_files' => $file_stats['total_files'] ?? 0,
            'image_files' => $file_stats['image_files'] ?? 0,
            'document_files' => $file_stats['document_files'] ?? 0,
            'other_files' => $file_stats['other_files'] ?? 0,
            'status' => $storage_info['status'] ?? 'normal',
            'last_updated' => $storage_info['last_updated'] ?? null,

            // คำนวณข้อมูลเพิ่มเติม
            'avg_file_size' => 0,
            'storage_efficiency' => 0,
            'growth_trend' => 'stable'
        ];

        // คำนวณขนาดไฟล์เฉลี่ย
        if ($summary['total_files'] > 0 && $summary['used_space'] > 0) {
            $summary['avg_file_size'] = ($summary['used_space'] * 1024) / $summary['total_files']; // MB per file
        }

        // คำนวณประสิทธิภาพการจัดเก็บ
        if ($summary['total_space'] > 0) {
            $summary['storage_efficiency'] = 100 - $summary['usage_percentage'];
        }

        // วิเคราะห์แนวโน้ม (จากข้อมูล history)
        $history = $data['storage_history'] ?? [];
        if (count($history) >= 2) {
            $latest = $history[0]->usage_gb ?? 0;
            $previous = $history[1]->usage_gb ?? 0;

            if ($latest > $previous * 1.1) {
                $summary['growth_trend'] = 'increasing';
            } elseif ($latest < $previous * 0.9) {
                $summary['growth_trend'] = 'decreasing';
            }
        }

        return $summary;
    }

    /**
     * ✅ Preview สำหรับรายงานเรื่องร้องเรียน
     */
    private function export_complain_preview()
    {
        // ดึงข้อมูลเรื่องร้องเรียน
        $filters = $this->input->get();

        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'รายงานเรื่องร้องเรียน';
        $data['tenant_code'] = $this->tenant_code;
        $data['current_domain'] = $this->current_domain;

        // ดึงข้อมูลทั้งหมด (ไม่จำกัดจำนวน)
        $data['complains'] = $this->Reports_model->get_complains_with_details(0, 0, $filters);
        $data['complain_summary'] = $this->Reports_model->get_complain_summary();
        $data['complain_stats'] = $this->Reports_model->get_complain_statistics();
        $data['complain_trends'] = $this->Reports_model->get_complain_trends();

        // ข้อมูลสำหรับรายงาน
        $data['export_date'] = date('d/m/Y H:i:s');
        $data['report_type'] = 'complain';
        $data['report_title'] = 'รายงานเรื่องร้องเรียน';
        $data['filters'] = $filters;

        // โหลด view สำหรับ preview/print
        $this->load->view('reports/complain_preview', $data);
    }

    /**
     * ✅ AJAX API สำหรับอัปเดตข้อมูล Storage
     */
    public function api_update_storage()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            // ตรวจสอบสิทธิ์
            if (!$this->check_reports_access()) {
                echo json_encode(['success' => false, 'error' => 'ไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            // อัปเดตข้อมูล
            $this->Storage_updater_model->update_storage_usage();

            // ดึงข้อมูลใหม่
            $storage_info = $this->Reports_model->get_storage_detailed_report();

            echo json_encode([
                'success' => true,
                'message' => 'อัปเดตข้อมูลสำเร็จ',
                'data' => $storage_info,
                'updated_at' => date('Y-m-d H:i:s'),
                'total_space' => $storage_info['server_storage'],
                'used_space' => $storage_info['server_current'],
                'free_space' => $storage_info['free_space'],
                'percentage' => $storage_info['percentage_used']
            ]);

        } catch (Exception $e) {
            log_message('error', 'Storage Update API Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ✅ AJAX API สำหรับดึงข้อมูลการตั้งค่า Storage ปัจจุบัน (System Admin)
     */
    public function api_current_storage_settings()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            // ตรวจสอบสิทธิ์ System Admin
            if ($this->session->userdata('m_system') !== 'system_admin') {
                echo json_encode(['success' => false, 'error' => 'ไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            // ดึงข้อมูลปัจจุบัน
            $storage_info = $this->Reports_model->get_storage_detailed_report();

            echo json_encode([
                'success' => true,
                'settings' => [
                    'total_space' => $storage_info['server_storage'],
                    'current_usage' => $storage_info['server_current'],
                    'free_space' => $storage_info['free_space'],
                    'percentage_used' => $storage_info['percentage_used'],
                    'last_updated' => $storage_info['last_updated']
                ]
            ]);

        } catch (Exception $e) {
            log_message('error', 'Current Storage Settings API Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ✅ AJAX API สำหรับประวัติการตั้งค่า Storage (System Admin)
     */
    public function api_storage_settings_history()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            // ตรวจสอบสิทธิ์ System Admin
            if ($this->session->userdata('m_system') !== 'system_admin') {
                echo json_encode(['success' => false, 'error' => 'ไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            // ดึงประวัติการเปลี่ยนแปลง
            $history = [];

            // ตรวจสอบว่ามีตาราง storage history หรือไม่
            if ($this->db->table_exists('tbl_storage_settings_history')) {
                $query = $this->db->select('old_size, new_size, updated_by, updated_at')
                    ->from('tbl_storage_settings_history')
                    ->order_by('updated_at', 'DESC')
                    ->limit(5)
                    ->get();

                if ($query && $query->num_rows() > 0) {
                    $history = $query->result();
                }
            }

            echo json_encode([
                'success' => true,
                'history' => $history
            ]);

        } catch (Exception $e) {
            log_message('error', 'Storage Settings History API Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการดึงประวัติ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ✅ AJAX API สำหรับอัปเดตขนาด Storage (System Admin เท่านั้น)
     */
    public function api_admin_update_storage_size()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            // ตรวจสอบสิทธิ์ System Admin
            if ($this->session->userdata('m_system') !== 'system_admin') {
                echo json_encode(['success' => false, 'error' => 'ไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            // รับข้อมูลจาก request
            $input = json_decode($this->input->raw_input_stream, true);
            $new_size = isset($input['new_size']) ? floatval($input['new_size']) : 0;

            if ($new_size <= 0 || $new_size > 10000) {
                echo json_encode(['success' => false, 'error' => 'ขนาดไม่ถูกต้อง (1-10,000 GB)']);
                return;
            }

            // ดึงข้อมูลเดิม
            $current_info = $this->Reports_model->get_storage_detailed_report();
            $old_size = $current_info['server_storage'];

            // อัปเดตใน tbl_server
            $this->db->where('server_id', 1);
            $this->db->update('tbl_server', [
                'server_storage' => $new_size,
                'server_updated' => date('Y-m-d H:i:s')
            ]);

            // บันทึกประวัติ (ถ้ามีตาราง)
            if ($this->db->table_exists('tbl_storage_settings_history')) {
                $this->db->insert('tbl_storage_settings_history', [
                    'old_size' => $old_size,
                    'new_size' => $new_size,
                    'updated_by' => $this->session->userdata('username') ?: $this->session->userdata('m_id'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // ดึงข้อมูลใหม่
            $updated_info = $this->Reports_model->get_storage_detailed_report();

            echo json_encode([
                'success' => true,
                'message' => 'อัปเดตขนาดพื้นที่จัดเก็บสำเร็จ',
                'old_size' => $old_size,
                'new_size' => $new_size,
                'current_usage' => $updated_info['server_current'],
                'new_percentage' => $updated_info['percentage_used'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Admin Update Storage Size API Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ✅ API สำหรับรีเฟรชข้อมูลสถิติเว็บไซต์ในหน้า index
     */
    public function api_web_stats_summary()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $this->load->model('External_stats_model');
            $current_tenant = $this->External_stats_model->get_current_tenant_code();

            if ($current_tenant) {
                $web_stats = $this->External_stats_model->get_stats_summary('7days');

                echo json_encode([
                    'success' => true,
                    'web_stats' => [
                        'total_pageviews' => $web_stats['total_pageviews'] ?? 0,
                        'total_visitors' => $web_stats['total_visitors'] ?? 0,
                        'online_users' => $web_stats['online_users'] ?? 0,
                        'avg_pages_per_visitor' => $web_stats['avg_pageviews_per_visitor'] ?? 0
                    ],
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลสถิติได้',
                    'web_stats' => [
                        'total_pageviews' => 0,
                        'total_visitors' => 0,
                        'online_users' => 0,
                        'avg_pages_per_visitor' => 0
                    ]
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'API Web Stats Summary Error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'web_stats' => [
                    'total_pageviews' => 0,
                    'total_visitors' => 0,
                    'online_users' => 0,
                    'avg_pages_per_visitor' => 0
                ]
            ]);
        }
    }




    public function notifications()
    {
        // ตรวจสอบการ login
        if (!$this->session->userdata('m_id')) {
            redirect('User');
            return;
        }

        $data = [];
        $data['page_title'] = 'แจ้งเตือน';
        $data['debug_mode'] = (ENVIRONMENT === 'development' || $this->session->userdata('m_system') === 'system_admin');

        try {
            // โหลด libraries
            $this->load->library('notification_lib');
            $this->load->helper('timeago');

            // ดึงข้อมูล user
            $user_id = $this->session->userdata('m_id');
            $data['user_info'] = $this->get_user_info();

            // ตรวจสอบสิทธิ์และส่งไปยัง View
            $has_corruption_permission = $this->check_staff_corruption_permission($user_id);
            $data['has_corruption_permission'] = $has_corruption_permission;
            $data['is_system_admin'] = $this->is_system_admin();

            // Pagination setup
            $limit = 20;
            $start = $this->uri->segment(3) ? $this->uri->segment(3) : 0;

            // *** แก้ไข: ใช้ method เดียวกันสำหรับทั้งสามตัว ***
            $data['notifications'] = $this->notification_lib->get_staff_notifications_with_corruption_filter($user_id, $limit, $start);
            $data['unread_count'] = $this->notification_lib->get_staff_unread_count_with_corruption_filter($user_id);

            // *** แก้ไข: ใช้ method เดียวกันสำหรับนับทั้งหมด (ไม่ใช้ limit/offset) ***
            $data['total_notifications'] = $this->get_total_staff_notifications_unified($user_id);

            $data['method_used'] = 'Unified Methods - Same Logic for All Counts';

            // *** Debug สำหรับตรวจสอบ ***
            if ($data['debug_mode']) {
                log_message('info', "Debug Counts - Total: {$data['total_notifications']}, Unread: {$data['unread_count']}, Loaded: " . count($data['notifications']));
                log_message('info', "User ID: {$user_id}, Corruption Permission: " . ($has_corruption_permission ? 'YES' : 'NO'));
            }

            // Pagination config
            if ($data['total_notifications'] > 0) {
                $config['base_url'] = site_url('System_reports/notifications');
                $config['total_rows'] = $data['total_notifications'];
                $config['per_page'] = $limit;
                $config['uri_segment'] = 3;
                $config['first_link'] = 'หน้าแรก';
                $config['last_link'] = 'หน้าสุดท้าย';
                $config['next_link'] = 'ถัดไป';
                $config['prev_link'] = 'ก่อนหน้า';

                // Bootstrap 4 styling
                $config['full_tag_open'] = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
                $config['full_tag_close'] = '</ul></nav>';
                $config['first_tag_open'] = '<li class="page-item">';
                $config['first_tag_close'] = '</li>';
                $config['last_tag_open'] = '<li class="page-item">';
                $config['last_tag_close'] = '</li>';
                $config['next_tag_open'] = '<li class="page-item">';
                $config['next_tag_close'] = '</li>';
                $config['prev_tag_open'] = '<li class="page-item">';
                $config['prev_tag_close'] = '</li>';
                $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
                $config['cur_tag_close'] = '</span></li>';
                $config['num_tag_open'] = '<li class="page-item">';
                $config['num_tag_close'] = '</li>';
                $config['attributes'] = array('class' => 'page-link');

                $this->pagination->initialize($config);
                try {
                    // ตรวจสอบและทำความสะอาดค่าก่อนส่งให้ pagination
                    $config['cur_page'] = max(1, (int) ($start / $limit) + 1);

                    // Re-initialize กับค่าที่ปลอดภัย
                    $this->pagination->initialize($config);
                    $data['pagination'] = $this->pagination->create_links();
                } catch (Exception $e) {
                    log_message('error', 'Pagination error: ' . $e->getMessage());
                    $data['pagination'] = '';
                }
            } else {
                $data['pagination'] = '';
            }

            // Debug information
            if ($data['debug_mode']) {
                $data['debug_info'] = [
                    'user_id' => $user_id,
                    'method_used' => $data['method_used'],
                    'total_notifications' => $data['total_notifications'],
                    'notifications_loaded' => count($data['notifications']),
                    'unread_count' => $data['unread_count'],
                    'corruption_permission' => $has_corruption_permission,
                    'is_system_admin' => $data['is_system_admin'],
                    'sql_debug' => 'Check logs for SQL queries'
                ];

                log_message('info', 'Notifications Debug Info: ' . json_encode($data['debug_info']));
            }

            // เพิ่มข้อมูลสำหรับ view
            $data['current_page'] = floor($start / $limit) + 1;
            $data['total_pages'] = ceil($data['total_notifications'] / $limit);

            // โหลด views
            $this->load->view('reports/header', $data);

            if (!empty($data['notifications'])) {
                $this->load->view('reports/notifications_all', $data);
            } else {
                $this->load->view('reports/notifications_empty', $data);
            }

            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Critical error in notifications: ' . $e->getMessage());

            // Error handling
            $data['error_message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            $data['total_notifications'] = 0;
            $data['notifications'] = [];
            $data['unread_count'] = 0;
            $data['pagination'] = '';
            $data['has_corruption_permission'] = false;
            $data['is_system_admin'] = false;

            $this->load->view('reports/header', $data);
            $this->load->view('reports/notifications_error', $data);
            $this->load->view('reports/footer');
        }
    }





    private function get_total_staff_notifications_unified($user_id)
    {
        try {
            // ตรวจสอบสิทธิ์ Corruption (เหมือนกับ unread count)
            $has_corruption_permission = $this->check_staff_corruption_permission($user_id);

            log_message('info', "get_total_staff_notifications_unified: User={$user_id}, Corruption Permission=" . ($has_corruption_permission ? 'YES' : 'NO'));

            // ใช้ SQL เดียวกันกับ count_staff_unread_notifications แต่ไม่ JOIN กับ tbl_notification_reads
            if ($has_corruption_permission) {
                // มีสิทธิ์: นับทั้งหมด รวม corruption
                $sql = "
                SELECT COUNT(*) as total_count
                FROM tbl_notifications n
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (
                      (n.reference_table = 'tbl_corruption_reports')
                      OR
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
            ";
                $params = [$user_id];
            } else {
                // ไม่มีสิทธิ์: ซ่อน corruption notifications
                $sql = "
                SELECT COUNT(*) as total_count
                FROM tbl_notifications n
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                  AND (
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
            ";
                $params = [$user_id];
            }

            log_message('info', "Total Count SQL: " . preg_replace('/\s+/', ' ', trim($sql)));
            log_message('info', "Total Count Params: " . json_encode($params));

            $query = $this->db->query($sql, $params);

            if ($query) {
                $result = $query->row();
                $count = $result ? (int) $result->total_count : 0;
                log_message('info', "get_total_staff_notifications_unified: Found {$count} total notifications");
                return $count;
            }

            log_message('error', 'get_total_staff_notifications_unified: Query failed');
            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error in get_total_staff_notifications_unified: ' . $e->getMessage());
            return 0;
        }
    }


    private function ensure_notification_lib_consistency($user_id)
    {
        try {
            // ถ้า notification_lib มี method get_total_count ให้ override
            if (method_exists($this->notification_lib, 'get_total_count')) {
                // ใช้ custom method แทน
                return $this->get_total_staff_notifications_unified($user_id);
            } else {
                // ใช้ custom method เท่านั้น
                return $this->get_total_staff_notifications_unified($user_id);
            }
        } catch (Exception $e) {
            log_message('error', 'Error in ensure_notification_lib_consistency: ' . $e->getMessage());
            return 0;
        }
    }


    /**
     * *** เพิ่ม: Method ใหม่สำหรับนับทั้งหมดพร้อม corruption filter ***
     */
    private function count_total_staff_notifications_with_corruption_filter($user_id)
    {
        try {
            // ใช้ method เดียวกันกับการดึงข้อมูล
            if (method_exists($this->notification_lib, 'get_total_count')) {
                return $this->notification_lib->get_total_count('staff');
            }

            // Fallback: ใช้ method ที่มีการกรอง corruption
            return $this->count_total_staff_notifications($user_id);

        } catch (Exception $e) {
            log_message('error', 'Error in count_total_staff_notifications_with_corruption_filter: ' . $e->getMessage());
            return 0;
        }
    }






    private function get_staff_notifications_with_read_status($user_id, $limit, $offset)
    {
        try {
            // *** เพิ่ม: ตรวจสอบสิทธิ์ Corruption ***
            $has_corruption_permission = $this->check_staff_corruption_permission($user_id);

            // สร้าง SQL ตามสิทธิ์
            if ($has_corruption_permission) {
                // มีสิทธิ์: แสดงทั้งหมด รวม corruption
                $sql = "
                SELECT n.*, 
                       nr.read_at as user_read_at, 
                       CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (
                      (n.reference_table = 'tbl_corruption_reports')
                      OR
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ";
                $params = [$user_id, $user_id, (int) $limit, (int) $offset];
            } else {
                // ไม่มีสิทธิ์: ซ่อน corruption notifications
                $sql = "
                SELECT n.*, 
                       nr.read_at as user_read_at, 
                       CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                  AND (
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
                ORDER BY n.created_at DESC
                LIMIT ? OFFSET ?
            ";
                $params = [$user_id, $user_id, (int) $limit, (int) $offset];
            }

            $query = $this->db->query($sql, $params);

            if ($query && $query->num_rows() > 0) {
                $results = $query->result();

                // แปลง JSON data กลับเป็น object
                foreach ($results as $notification) {
                    if ($notification->data && is_string($notification->data)) {
                        $notification->data = json_decode($notification->data);
                    }
                }

                log_message('info', "Custom Raw Query found {$query->num_rows()} notifications for user {$user_id} (Corruption Permission: " . ($has_corruption_permission ? 'YES' : 'NO') . ")");
                return $results;
            }

            return [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_staff_notifications_with_read_status: ' . $e->getMessage());
            return [];
        }
    }









    private function count_staff_unread_notifications($user_id)
    {
        try {
            // *** เพิ่ม: ตรวจสอบสิทธิ์ Corruption ***
            $has_corruption_permission = $this->check_staff_corruption_permission($user_id);

            if ($has_corruption_permission) {
                // มีสิทธิ์: นับทั้งหมด รวม corruption
                $sql = "
                SELECT COUNT(*) as unread_count
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (
                      (n.reference_table = 'tbl_corruption_reports')
                      OR
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
            ";
                $params = [$user_id, $user_id];
            } else {
                // ไม่มีสิทธิ์: ซ่อน corruption notifications
                $sql = "
                SELECT COUNT(*) as unread_count
                FROM tbl_notifications n
                LEFT JOIN tbl_notification_reads nr ON (
                    n.notification_id = nr.notification_id 
                    AND nr.user_id = ? 
                    AND nr.user_type = 'staff'
                )
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0 
                  AND nr.id IS NULL
                  AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                  AND (
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
            ";
                $params = [$user_id, $user_id];
            }

            $query = $this->db->query($sql, $params);

            if ($query) {
                $result = $query->row();
                return $result ? (int) $result->unread_count : 0;
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error in count_staff_unread_notifications: ' . $e->getMessage());
            return 0;
        }
    }





    private function count_total_staff_notifications($user_id)
    {
        try {
            // *** เพิ่ม: ตรวจสอบสิทธิ์ Corruption ***
            $has_corruption_permission = $this->check_staff_corruption_permission($user_id);

            if ($has_corruption_permission) {
                // มีสิทธิ์: นับทั้งหมด รวม corruption
                $sql = "
                SELECT COUNT(*) as total_count
                FROM tbl_notifications n
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (
                      (n.reference_table = 'tbl_corruption_reports')
                      OR
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.reference_table IS NULL OR n.reference_table NOT IN ('tbl_corruption_reports'))
                      AND (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
            ";
                $params = [$user_id];
            } else {
                // ไม่มีสิทธิ์: ซ่อน corruption notifications
                $sql = "
                SELECT COUNT(*) as total_count
                FROM tbl_notifications n
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                  AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                  AND (
                      (n.type IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required') 
                       AND n.target_user_id = ? AND n.target_user_id IS NOT NULL)
                      OR 
                      (n.type NOT IN ('complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated', 'esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'))
                  )
            ";
                $params = [$user_id];
            }

            $query = $this->db->query($sql, $params);

            if ($query) {
                $result = $query->row();
                return $result ? (int) $result->total_count : 0;
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error in count_total_staff_notifications: ' . $e->getMessage());
            return 0;
        }
    }




    private function count_total_individual_notifications($target_role)
    {
        try {
            $user_id = $this->session->userdata('m_id');

            // ใช้ notification_lib ถ้ามี method นับทั้งหมด
            if (method_exists($this->notification_lib, 'get_total_count')) {
                return $this->notification_lib->get_total_count($target_role);
            }

            // Fallback: นับเองแบบมีการกรอง corruption
            return $this->count_total_staff_notifications($user_id);

        } catch (Exception $e) {
            log_message('error', 'Error in count_total_individual_notifications: ' . $e->getMessage());
            return 0;
        }
    }






    /**
     * ✅ แก้ไข: API ดึงการแจ้งเตือนล่าสุดสำหรับ Staff - ใช้ role 'staff'
     */
    public function get_recent_notifications()
    {
        $this->output->set_header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['status' => 'error', 'message' => 'Not logged in'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $limit = max(1, min(50, (int) ($this->input->get('limit') ?: 5)));

            // *** ใช้ method เดียวกัน ***
            $this->load->library('notification_lib');

            $notifications = $this->notification_lib->get_staff_notifications_with_corruption_filter($m_id, $limit, 0);
            $unread_count = $this->notification_lib->get_staff_unread_count_with_corruption_filter($m_id);

            // แปลงข้อมูลสำหรับ JSON response
            $notification_array = [];
            foreach ($notifications as $notif) {
                $notification_array[] = [
                    'notification_id' => (int) $notif->notification_id,
                    'title' => $notif->title ?: 'แจ้งเตือน',
                    'message' => $notif->message ?: '',
                    'type' => $notif->type ?: 'info',
                    'priority' => $notif->priority ?: 'normal',
                    'is_read_by_user' => (int) ($notif->is_read_by_user ?? 0),
                    'created_at' => $notif->created_at,
                    'url' => $notif->url ?: '#',
                    'icon' => $notif->icon ?: 'bi bi-bell',
                    'reference_table' => $notif->reference_table
                ];
            }

            echo json_encode([
                'status' => 'success',
                'notifications' => $notification_array,
                'unread_count' => $unread_count,
                'total_count' => count($notification_array),
                'method_used' => 'unified_notification_lib'
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            error_log('Notification Error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด',
                'notifications' => [],
                'unread_count' => 0
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * *** เพิ่ม: ฟังก์ชันตรวจสอบสิทธิ์ corruption ***
     */
    private function check_staff_corruption_permission($user_id)
    {
        try {
            if (!$this->db->table_exists('tbl_member')) {
                return false;
            }

            $this->db->select('m.m_id, m.m_system, m.grant_user_ref_id');
            $this->db->from('tbl_member m');
            $this->db->where('m.m_id', intval($user_id));
            $this->db->where('m.m_status', '1');
            $query = $this->db->get();

            if (!$query || $query->num_rows() == 0) {
                log_message('info', 'check_staff_corruption_permission: Staff not found for ID ' . $user_id);
                return false;
            }

            $staff_data = $query->row();

            log_message('info', 'check_staff_corruption_permission: Staff ID ' . $staff_data->m_id . ', System: ' . $staff_data->m_system . ', Grant: ' . $staff_data->grant_user_ref_id);

            // system_admin และ super_admin
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', 'check_staff_corruption_permission: GRANTED - system/super admin');
                return true;
            }

            // user_admin ที่มี grant 107
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('info', 'check_staff_corruption_permission: DENIED - user_admin without grants');
                    return false;
                }

                try {
                    $grant_ids = explode(',', $staff_data->grant_user_ref_id);
                    $grant_ids = array_map('trim', $grant_ids);

                    log_message('info', 'check_staff_corruption_permission: Grant IDs: ' . json_encode($grant_ids));

                    if (in_array('107', $grant_ids)) {
                        log_message('info', 'check_staff_corruption_permission: GRANTED - found 107 in grants');
                        return true;
                    }

                    // เช็คใน tbl_grant_user
                    if ($this->db->table_exists('tbl_grant_user')) {
                        foreach ($grant_ids as $grant_id) {
                            if (empty($grant_id) || !is_numeric($grant_id))
                                continue;

                            $this->db->select('grant_user_id, grant_user_name');
                            $this->db->from('tbl_grant_user');
                            $this->db->where('grant_user_id', intval($grant_id));
                            $grant_query = $this->db->get();

                            if ($grant_query && $grant_query->num_rows() > 0) {
                                $grant_data = $grant_query->row();

                                if ($grant_data->grant_user_id == 107) {
                                    log_message('info', 'check_staff_corruption_permission: GRANTED - grant_user_id = 107');
                                    return true;
                                }

                                $name_lower = mb_strtolower($grant_data->grant_user_name, 'UTF-8');
                                if (strpos($name_lower, 'ทุจริต') !== false) {
                                    log_message('info', 'check_staff_corruption_permission: GRANTED - corruption-related grant');
                                    return true;
                                }
                            }
                        }
                    }

                    log_message('info', 'check_staff_corruption_permission: DENIED - no valid corruption grants');
                    return false;

                } catch (Exception $e) {
                    log_message('error', 'check_staff_corruption_permission: Error checking grants: ' . $e->getMessage());
                    // Fallback check
                    $has_107 = (strpos($staff_data->grant_user_ref_id, '107') !== false);
                    log_message('info', 'check_staff_corruption_permission: Fallback check result: ' . ($has_107 ? 'GRANTED' : 'DENIED'));
                    return $has_107;
                }
            }

            log_message('info', 'check_staff_corruption_permission: DENIED - not authorized system: ' . $staff_data->m_system);
            return false;

        } catch (Exception $e) {
            log_message('error', 'check_staff_corruption_permission: Exception: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * ✅ แก้ไข: API ทำเครื่องหมายทุกการแจ้งเตือนว่าอ่านแล้วสำหรับ Staff - ใช้ role 'staff'
     */
    public function mark_all_notifications_read()
    {
        $this->output->set_header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['status' => 'error', 'message' => 'Not logged in'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงการแจ้งเตือนที่ยังไม่อ่าน
            $unread_sql = "
            SELECT n.notification_id 
            FROM tbl_notifications n
            LEFT JOIN tbl_notification_reads nr ON (
                n.notification_id = nr.notification_id 
                AND nr.user_id = ? 
                AND nr.user_type = 'staff'
            )
            WHERE n.target_role = 'staff' 
              AND n.is_archived = 0 
              AND nr.id IS NULL
        ";

            $unread_query = $this->db->query($unread_sql, [$m_id]);
            $marked_count = 0;

            if ($unread_query && $unread_query->num_rows() > 0) {
                foreach ($unread_query->result() as $notif) {
                    $insert_sql = "INSERT IGNORE INTO tbl_notification_reads 
                               (notification_id, user_id, user_type, read_at) 
                               VALUES (?, ?, 'staff', NOW())";
                    if ($this->db->query($insert_sql, [$notif->notification_id, $m_id])) {
                        $marked_count++;
                    }
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => $marked_count > 0 ? "ทำเครื่องหมายสำเร็จ {$marked_count} รายการ" : 'ไม่มีการแจ้งเตือนที่ยังไม่อ่าน',
                'marked_count' => $marked_count
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            error_log('Mark All Error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * API: ทำเครื่องหมายการแจ้งเตือนว่าอ่านแล้วสำหรับ Staff (AJAX) - ไม่เปลี่ยน role เพราะใช้ individual system
     */
    public function mark_notification_read()
    {
        $this->output->set_header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $m_id = $this->session->userdata('m_id');
            $notification_id = (int) $this->input->post('notification_id');

            if (!$m_id || !$notification_id) {
                echo json_encode(['status' => 'error', 'message' => 'Missing parameters'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบว่าอ่านแล้วหรือยัง
            $check_sql = "SELECT id FROM tbl_notification_reads 
                      WHERE notification_id = ? AND user_id = ? AND user_type = 'staff'";
            $exists = $this->db->query($check_sql, [$notification_id, $m_id])->num_rows() > 0;

            if (!$exists) {
                // บันทึกสถานะการอ่าน
                $insert_sql = "INSERT INTO tbl_notification_reads 
                           (notification_id, user_id, user_type, read_at) 
                           VALUES (?, ?, 'staff', NOW())";
                $this->db->query($insert_sql, [$notification_id, $m_id]);
            }

            // นับ unread ใหม่
            $unread_sql = "SELECT COUNT(*) as count FROM tbl_notifications n
                      LEFT JOIN tbl_notification_reads nr ON (n.notification_id = nr.notification_id 
                                AND nr.user_id = ? AND nr.user_type = 'staff')
                      WHERE n.target_role = 'staff' AND n.is_archived = 0 AND nr.id IS NULL";
            $unread_query = $this->db->query($unread_sql, [$m_id]);
            $unread_count = $unread_query ? (int) $unread_query->row()->count : 0;

            echo json_encode([
                'status' => 'success',
                'message' => 'ทำเครื่องหมายเรียบร้อย',
                'notification_id' => $notification_id,
                'new_unread_count' => $unread_count
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            error_log('Mark Read Error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }







    private function get_current_user_info_for_notification()
    {
        $user_info = [
            'user_id' => null,
            'user_type' => 'guest',
            'role' => 'guest'
        ];

        $m_id = $this->session->userdata('m_id');
        $m_email = $this->session->userdata('m_email');

        // *** Debug session data ***
        log_message('info', "Session m_id: " . ($m_id ?: 'NULL'));
        log_message('info', "Session m_email: " . ($m_email ?: 'NULL'));

        if ($m_id && $m_email) {
            // *** แก้ไข: ตรวจสอบ overflow และใช้ fallback ***
            if ($m_id == 2147483647 || $m_id == '2147483647') {
                log_message('info', "Detected user_id overflow: {$m_id}");

                // ใช้ m_id จาก database โดยตรง
                $staff_user = $this->db->select('m_id')
                    ->where('m_email', $m_email)
                    ->where('m_status', 1)
                    ->get('tbl_member')
                    ->row();

                if ($staff_user && $staff_user->m_id != 2147483647) {
                    $fixed_user_id = $staff_user->m_id;
                    log_message('info', "Fixed staff user_id from DB: {$fixed_user_id}");
                } else {
                    // *** Fallback: ใช้ email hash ***
                    $fixed_user_id = crc32($m_email);
                    log_message('info', "Using email hash as user_id: {$fixed_user_id}");
                }
            } else {
                $fixed_user_id = $m_id;
            }

            $user_info = [
                'user_id' => $fixed_user_id,
                'user_type' => 'staff',
                'role' => 'staff'
            ];

            log_message('info', "Final user info: ID={$fixed_user_id}, Type=staff, Email={$m_email}");
        } else {
            log_message('error', "Missing session data - m_id: " . ($m_id ? 'OK' : 'MISSING') . ", m_email: " . ($m_email ? 'OK' : 'MISSING'));
        }

        return $user_info;
    }





    /**
     * API: ลบการแจ้งเตือน (archive) สำหรับ Staff - ไม่เปลี่ยนเพราะไม่เกี่ยวกับ role
     */
    public function archive_notification()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            // ตรวจสอบการ login
            if (!$this->session->userdata('m_id')) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Please login first'
                    ]));
                return;
            }

            $notification_id = $this->input->post('notification_id');

            if (!$notification_id) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่พบ notification ID'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            $result = $this->Notification_model->archive_notification($notification_id, $this->session->userdata('m_id'));

            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'message' => 'ลบการแจ้งเตือนสำเร็จ'
                    ], JSON_UNESCAPED_UNICODE));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่สามารถลบได้'
                    ], JSON_UNESCAPED_UNICODE));
            }

        } catch (Exception $e) {
            log_message('error', 'Error in archive_notification for staff: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการลบ'
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    public function clear_all_notifications()
    {
        try {
            // *** เพิ่ม Debug และ Error Handling ที่ดีขึ้น ***
            log_message('info', 'clear_all_notifications method called by IP: ' . $this->input->ip_address());

            // *** แก้ไข: ตรวจสอบ method ที่อนุญาต ***
            if (!$this->input->is_ajax_request() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                log_message('warning', 'clear_all_notifications: Invalid request method or not AJAX');
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(405)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Method not allowed. Use POST with AJAX.'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ตรวจสอบ session ก่อน
            $m_system = $this->session->userdata('m_system');
            $m_username = $this->session->userdata('m_username');
            $m_id = $this->session->userdata('m_id');

            log_message('info', "clear_all_notifications: Session check - m_system: {$m_system}, m_username: {$m_username}, m_id: {$m_id}");

            // *** แก้ไข: ตรวจสอบสิทธิ์ System Admin แบบเข้มงวด ***
            if (empty($m_system) || $m_system !== 'system_admin') {
                log_message('warning', "clear_all_notifications: Access denied for user {$m_username} with level {$m_system}");

                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่มีสิทธิ์ในการดำเนินการ (ต้องเป็น System Admin)',
                        'debug' => [
                            'current_level' => $m_system ?: 'null',
                            'required_level' => 'system_admin',
                            'username' => $m_username ?: 'null',
                            'session_active' => !empty($m_id)
                        ]
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ตรวจสอบการเชื่อมต่อฐานข้อมูล
            if (!$this->db->conn_id) {
                log_message('error', 'clear_all_notifications: Database connection failed');
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // นับจำนวนข้อมูลก่อนลบ
            $notification_count = $this->db->count_all('tbl_notifications');
            $read_count = $this->db->count_all('tbl_notification_reads');

            log_message('info', "clear_all_notifications: Before deletion - notifications: {$notification_count}, reads: {$read_count}");

            // เริ่มต้น transaction เพื่อความปลอดภัย
            $this->db->trans_start();

            // *** 1. ลบทุก records ใน tbl_notification_reads ก่อน (เพราะมี foreign key constraint) ***
            $this->db->empty_table('tbl_notification_reads');
            $deleted_reads = $read_count; // เก็บค่าเดิมไว้เพราะ affected_rows อาจไม่ถูกต้องหลัง empty_table

            // *** 2. ลบทุก records ใน tbl_notifications ***
            $this->db->empty_table('tbl_notifications');
            $deleted_notifications = $notification_count; // เก็บค่าเดิมไว้เพราะ affected_rows อาจไม่ถูกต้องหลัง empty_table

            // *** 3. Reset AUTO_INCREMENT values ***
            $this->db->query('ALTER TABLE tbl_notifications AUTO_INCREMENT = 1');
            $this->db->query('ALTER TABLE tbl_notification_reads AUTO_INCREMENT = 1');

            // สิ้นสุด transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'clear_all_notifications: Transaction failed');
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล (Transaction failed)',
                        'debug' => [
                            'transaction_status' => 'failed',
                            'db_error' => $this->db->error()
                        ]
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // *** ตรวจสอบว่าข้อมูลถูกลบจริงหรือไม่ ***
            $remaining_notifications = $this->db->count_all('tbl_notifications');
            $remaining_reads = $this->db->count_all('tbl_notification_reads');

            if ($remaining_notifications > 0 || $remaining_reads > 0) {
                log_message('error', "clear_all_notifications: Deletion incomplete - remaining notifications: {$remaining_notifications}, remaining reads: {$remaining_reads}");
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'การลบข้อมูลไม่สมบูรณ์',
                        'debug' => [
                            'remaining_notifications' => $remaining_notifications,
                            'remaining_reads' => $remaining_reads
                        ]
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // บันทึก log สำคัญ
            log_message('warning', "CRITICAL: System Admin {$m_username} (ID: {$m_id}) cleared ALL notifications: {$deleted_notifications} notifications, {$deleted_reads} read records");

            // *** สร้าง response ที่สมบูรณ์ ***
            $response = [
                'status' => 'success',
                'message' => 'ล้างการแจ้งเตือนทั้งหมดสำเร็จ',
                'deleted_notifications' => $deleted_notifications,
                'deleted_reads' => $deleted_reads,
                'reset_auto_increment' => true,
                'admin_user' => $m_username,
                'admin_id' => $m_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'verification' => [
                    'remaining_notifications' => $remaining_notifications,
                    'remaining_reads' => $remaining_reads,
                    'tables_reset' => true
                ],
                'debug' => [
                    'method' => 'clear_all_notifications',
                    'transaction_status' => 'success',
                    'affected_tables' => ['tbl_notifications', 'tbl_notification_reads'],
                    'request_method' => $_SERVER['REQUEST_METHOD'],
                    'is_ajax' => $this->input->is_ajax_request()
                ]
            ];

            log_message('info', 'clear_all_notifications: Success response prepared');

            // *** แก้ไข: ให้แน่ใจว่าส่ง JSON เท่านั้น ***
            $this->output
                ->set_content_type('application/json; charset=utf-8')
                ->set_status_header(200)
                ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            // *** สำคัญ: หยุดการทำงานของ PHP ทันทีเพื่อป้องกัน output เพิ่มเติม ***
            return;

        } catch (Exception $e) {
            log_message('error', 'Error in clear_all_notifications: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile());

            $this->output
                ->set_content_type('application/json; charset=utf-8')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage(),
                    'debug' => [
                        'error_line' => $e->getLine(),
                        'error_file' => basename($e->getFile()),
                        'error_message' => $e->getMessage(),
                        'error_code' => $e->getCode()
                    ]
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            // *** สำคัญ: หยุดการทำงานของ PHP ทันทีเพื่อป้องกัน output เพิ่มเติม ***
            return;
        }
    }



    private function get_elderly_summary_data()
    {
        try {
            $statistics = [
                'total' => 0,
                'submitted' => 0,
                'reviewing' => 0,
                'completed' => 0
            ];

            if ($this->db->table_exists('tbl_elderly_aw_ods')) {
                // นับทั้งหมด
                $this->db->from('tbl_elderly_aw_ods');
                $statistics['total'] = $this->db->count_all_results();

                // นับตามสถานะ
                foreach (['submitted', 'reviewing', 'completed'] as $status) {
                    $this->db->from('tbl_elderly_aw_ods');
                    $this->db->where('elderly_aw_ods_status', $status);
                    $statistics[$status] = $this->db->count_all_results();
                }
            }

            return $statistics;

        } catch (Exception $e) {
            log_message('error', 'Error getting elderly summary: ' . $e->getMessage());
            return [
                'total' => 0,
                'submitted' => 0,
                'reviewing' => 0,
                'completed' => 0
            ];
        }
    }


    public function api_summary_data()
    {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            // ตรวจสอบสิทธิ์
            if (!$this->check_reports_access()) {
                echo json_encode(['success' => false, 'error' => 'ไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            // ดึงข้อมูลสรุปทั้งหมด
            $summary_data = $this->Reports_model->get_reports_summary();

            // เพิ่มข้อมูลสถิติเรื่องร้องเรียน
            if (method_exists($this->Reports_model, 'get_complain_summary')) {
                $complain_summary = $this->Reports_model->get_complain_summary();
                $summary_data['complains'] = [
                    'total' => $complain_summary['total'] ?? 0,
                    'pending' => $complain_summary['pending'] ?? 0,
                    'in_progress' => $complain_summary['in_progress'] ?? 0,
                    'completed' => $complain_summary['completed'] ?? 0
                ];
            } else {
                $summary_data['complains'] = [
                    'total' => 0,
                    'pending' => 0,
                    'in_progress' => 0,
                    'completed' => 0
                ];
            }

            echo json_encode([
                'success' => true,
                'data' => $summary_data,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in api_summary_data: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage(),
                'data' => [
                    'storage' => [
                        'percentage' => 0,
                        'free' => 0,
                        'used' => 0,
                        'total' => 100
                    ],
                    'complains' => [
                        'total' => 0,
                        'pending' => 0,
                        'in_progress' => 0,
                        'completed' => 0
                    ]
                ]
            ]);
        }
    }








    /**
     * หน้า Assessment Admin - รายงานและสถิติการประเมิน
     */
    public function assessment_admin()
    {
        // เช็คสิทธิ์ admin
        if (!$this->check_admin_access()) {
            show_404();
        }

        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'รายงานผลการประเมินความพึงพอใจ';
        $data['tenant_code'] = $this->tenant_code;

        // เพิ่มข้อมูลสิทธิ์การจัดการฟอร์ม
        $data['user_permissions'] = [
            'can_manage_form' => $this->check_form_management_access(),
            'user_system' => $this->session->userdata('m_system'),
            'user_id' => $this->session->userdata('m_id')
        ];

        // โหลด Assessment Model
        $this->load->model('assessment_model');

        try {
            // ดึงข้อมูลสำหรับ Dashboard
            $data['statistics'] = $this->get_assessment_statistics();
            $data['categories'] = $this->assessment_model->get_categories(false); // รวมที่ปิดใช้งาน
            $data['settings'] = $this->assessment_model->get_all_settings();

            // ✅ ใช้ฟังก์ชันใหม่ที่กรองตาม categories
            $data['recent_responses'] = $this->get_recent_responses_filtered(10);

            // เพิ่มข้อมูลการแจกแจงคะแนน (1-5)
            $data['score_distribution'] = $this->get_score_distribution();

            // เพิ่มข้อมูลสถิติรายวัน (7 วันล่าสุด)
            $data['daily_stats'] = $this->get_daily_statistics();

            // ✅ ใช้ฟังก์ชันที่อัพเดทแล้วสำหรับ feedback
            $data['feedback_comments'] = $this->get_recent_feedback();

            // เพิ่มจำนวนคำถามในแต่ละหมวด
            foreach ($data['categories'] as &$category) {
                $category->question_count = $this->count_questions_in_category($category->id);
                $category->scoring_question_count = $this->count_scoring_questions_in_category($category->id);
            }

            // เพิ่มจำนวนคำถามและผู้ตอบในแต่ละหมวดสำหรับสถิติ (เฉพาะหมวดประเมิน)
            if (!empty($data['statistics']['categories'])) {
                foreach ($data['statistics']['categories'] as $cat_id => &$cat_data) {
                    $cat_data['question_count'] = $this->count_scoring_questions_in_category($cat_id);
                    // response_count ถูกคำนวณไว้แล้วใน get_assessment_statistics()
                }
            }

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            $data['has_data'] = $data['statistics']['total_responses'] > 0;

        } catch (Exception $e) {
            // หากเกิดข้อผิดพลาด ให้แสดงข้อมูลว่าง
            log_message('error', 'Assessment Admin Error: ' . $e->getMessage());

            $data['statistics'] = [
                'total_responses' => 0,
                'today_responses' => 0,
                'total_questions' => 0,
                'average_score' => 0,
                'categories' => []
            ];
            $data['categories'] = [];
            $data['settings'] = [];
            $data['recent_responses'] = [];
            $data['score_distribution'] = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
            $data['daily_stats'] = [];
            $data['feedback_comments'] = [];
            $data['has_data'] = false;

            // แสดงข้อความแจ้งเตือน
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่อีกครั้ง');
        }

        // ใช้ reports header/footer
        $this->load->view('reports/header', $data);
        $this->load->view('reports/assessment_admin', $data);
        $this->load->view('reports/footer');
    }

    /**
     * หน้าแสดงรายการคำตอบแบบประเมินทั้งหมด
     */
    public function all_assessment_responses()
    {
        // เช็คสิทธิ์ admin
        if (!$this->check_admin_access()) {
            show_404();
        }

        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'รายการคำตอบแบบประเมินทั้งหมด';
        $data['tenant_code'] = $this->tenant_code;

        // เพิ่มข้อมูลสิทธิ์การจัดการฟอร์ม
        $data['user_permissions'] = [
            'can_manage_form' => $this->check_form_management_access(),
            'user_system' => $this->session->userdata('m_system'),
            'user_id' => $this->session->userdata('m_id')
        ];

        // โหลด Assessment Model
        $this->load->model('assessment_model');

        try {
            // ดึงข้อมูลจาก Model
            $data['categories'] = $this->assessment_model->get_categories(false);
            $data['statistics'] = $this->get_assessment_statistics();

            // การแบ่งหน้า (Pagination)
            $limit = 20; // แสดง 20 รายการต่อหน้า
            $offset = $this->input->get('page') ? ($this->input->get('page') - 1) * $limit : 0;

            // ตัวกรองข้อมูล
            $filters = [
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'score' => $this->input->get('score')
            ];

            // ดึงข้อมูลพร้อม pagination และ filter จาก Model
            $data['responses'] = $this->assessment_model->get_paginated_responses($limit, $offset, $filters);
            $data['total_responses'] = $this->assessment_model->count_total_responses($filters);
            $data['current_page'] = $this->input->get('page') ? intval($this->input->get('page')) : 1;
            $data['total_pages'] = ceil($data['total_responses'] / $limit);
            $data['limit'] = $limit;
            $data['offset'] = $offset;

            // ตัวกรองข้อมูล
            $data['filter_date_from'] = $filters['date_from'];
            $data['filter_date_to'] = $filters['date_to'];
            $data['filter_score'] = $filters['score'];

            // เพิ่มข้อมูลสำหรับ filtering
            $data['score_options'] = [
                '' => 'ทุกคะแนน',
                '5' => 'ดีมาก (5)',
                '4' => 'ดี (4)',
                '3' => 'ปานกลาง (3)',
                '2' => 'ต้องปรับปรุง (2)',
                '1' => 'ต้องปรับปรุงมาก (1)'
            ];

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            $data['has_data'] = $data['total_responses'] > 0;

        } catch (Exception $e) {
            // หากเกิดข้อผิดพลาด ให้แสดงข้อมูลว่าง
            log_message('error', 'All Assessment Responses Error: ' . $e->getMessage());

            $data['responses'] = [];
            $data['categories'] = [];
            $data['statistics'] = [
                'total_responses' => 0,
                'today_responses' => 0,
                'total_questions' => 0,
                'average_score' => 0,
                'categories' => []
            ];
            $data['total_responses'] = 0;
            $data['current_page'] = 1;
            $data['total_pages'] = 0;
            $data['has_data'] = false;
            $data['score_options'] = [];

            // แสดงข้อความแจ้งเตือน
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่อีกครั้ง');
        }

        // ใช้ reports header/footer
        $this->load->view('reports/header', $data);
        $this->load->view('reports/all_assessment_responses', $data);
        $this->load->view('reports/footer');
    }


    /**
     * ดึงสถิติการประเมินรวม
     */
    private function get_assessment_statistics()
    {
        $stats = [];

        try {
            // ตรวจสอบว่าตารางมีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_assessment_responses')) {
                return [
                    'total_responses' => 0,
                    'today_responses' => 0,
                    'total_questions' => 0,
                    'average_score' => 0,
                    'categories' => []
                ];
            }

            // จำนวนผู้ตอบทั้งหมด
            $stats['total_responses'] = $this->db->where('is_completed', 1)
                ->count_all_results('tbl_assessment_responses');

            // จำนวนผู้ตอบวันนี้
            $today = date('Y-m-d');
            $stats['today_responses'] = $this->db->where('is_completed', 1)
                ->where('DATE(completed_at)', $today)
                ->count_all_results('tbl_assessment_responses');

            // จำนวนคำถามทั้งหมด (เฉพาะที่ใช้ประเมิน)
            if ($this->db->table_exists('tbl_assessment_questions')) {
                $this->db->select('COUNT(*) as total');
                $this->db->from('tbl_assessment_questions q');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('q.is_active', 1);
                $this->db->where('q.question_type', 'radio');
                $this->db->where('c.is_active', 1);
                $this->db->where('c.is_scoring', 1);

                $result = $this->db->get()->row();
                $stats['total_questions'] = $result ? intval($result->total) : 0;
            } else {
                $stats['total_questions'] = 0;
            }

            // คะแนนเฉลี่ยรวม (เฉพาะหมวดที่นำไปคำนวณ)
            $stats['average_score'] = 0;

            if ($this->db->table_exists('tbl_assessment_answers') && $stats['total_responses'] > 0) {
                $this->db->select('AVG(CAST(answer_value AS DECIMAL(3,2))) as avg_score, COUNT(*) as total_valid_answers');
                $this->db->from('tbl_assessment_answers a');
                $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('r.is_completed', 1);
                $this->db->where('q.question_type', 'radio');
                $this->db->where('a.answer_value REGEXP', '^[1-5]$');
                $this->db->where('c.is_active', 1);
                $this->db->where('c.is_scoring', 1); // เฉพาะหมวดที่นำไปคำนวณ

                $result = $this->db->get()->row();

                if ($result && $result->avg_score && $result->total_valid_answers > 0) {
                    $stats['average_score'] = floatval($result->avg_score);
                }
            }

            // คะแนนเฉลี่ยแต่ละหมวด (เฉพาะหมวดที่นำไปคำนวณ)
            $stats['categories'] = [];

            if ($stats['total_responses'] > 0 && class_exists('Assessment_model')) {
                $this->load->model('assessment_model');

                if (method_exists($this->assessment_model, 'get_categories')) {
                    // ดึงเฉพาะหมวดที่นำไปคำนวณ
                    $this->db->select('*');
                    $this->db->from('tbl_assessment_categories');
                    $this->db->where('is_active', 1);
                    $this->db->where('is_scoring', 1); // เฉพาะหมวดที่นำไปคำนวณ
                    $this->db->order_by('category_order', 'ASC');
                    $scoring_categories = $this->db->get()->result();

                    foreach ($scoring_categories as $category) {
                        $category_info = [
                            'name' => $category->category_name,
                            'avg_score' => 0,
                            'question_count' => 0,
                            'response_count' => 0
                        ];

                        try {
                            // นับจำนวนคำถามประเมิน
                            $this->db->select('COUNT(*) as count');
                            $this->db->from('tbl_assessment_questions');
                            $this->db->where('category_id', $category->id);
                            $this->db->where('question_type', 'radio');
                            $this->db->where('is_active', 1);
                            $q_count_result = $this->db->get()->row();
                            $category_info['question_count'] = $q_count_result ? intval($q_count_result->count) : 0;

                            // นับจำนวนผู้ตอบ (เฉพาะคำถามประเมิน)
                            $this->db->select('COUNT(DISTINCT r.id) as count');
                            $this->db->from('tbl_assessment_responses r');
                            $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
                            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                            $this->db->where('r.is_completed', 1);
                            $this->db->where('q.category_id', $category->id);
                            $this->db->where('q.question_type', 'radio');
                            $this->db->where('a.answer_value REGEXP', '^[1-5]$');
                            $r_count_result = $this->db->get()->row();
                            $category_info['response_count'] = $r_count_result ? intval($r_count_result->count) : 0;

                            // คำนวณคะแนนเฉลี่ย
                            $questions = $this->assessment_model->get_questions($category->id);
                            $category_scores = [];

                            foreach ($questions as $question) {
                                if ($question->question_type === 'radio') {
                                    $this->db->select('AVG(CAST(answer_value AS DECIMAL(3,2))) as avg_score, COUNT(*) as total_answers');
                                    $this->db->from('tbl_assessment_answers a');
                                    $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
                                    $this->db->where('a.question_id', $question->id);
                                    $this->db->where('r.is_completed', 1);
                                    $this->db->where('a.answer_value REGEXP', '^[1-5]$');

                                    $q_result = $this->db->get()->row();

                                    if ($q_result && $q_result->avg_score !== null && $q_result->total_answers > 0) {
                                        $avg_score = floatval($q_result->avg_score);
                                        if ($avg_score > 0) {
                                            $category_scores[] = $avg_score;
                                        }
                                    }
                                }
                            }

                            // คำนวณคะแนนเฉลี่ยหมวด
                            if (!empty($category_scores) && count($category_scores) > 0) {
                                $total_score = array_sum($category_scores);
                                $count_scores = count($category_scores);

                                if ($count_scores > 0) {
                                    $category_info['avg_score'] = $total_score / $count_scores;
                                }
                            }

                        } catch (Exception $e) {
                            log_message('error', 'Category Statistics Error for ID ' . $category->id . ': ' . $e->getMessage());
                        }

                        // เพิ่มเฉพาะหมวดที่มีคำถามประเมิน
                        if ($category_info['question_count'] > 0) {
                            $stats['categories'][$category->id] = $category_info;
                        }
                    }
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Get Assessment Statistics Error: ' . $e->getMessage());
            $stats = [
                'total_responses' => 0,
                'today_responses' => 0,
                'total_questions' => 0,
                'average_score' => 0,
                'categories' => []
            ];
        }

        return $stats;
    }


    /**
     * ดึงการแจกแจงคะแนน (1-5)
     */
    private function get_score_distribution()
    {
        $distribution = [];

        try {
            if (!$this->db->table_exists('tbl_assessment_answers')) {
                return ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
            }

            // ตรวจสอบจำนวนคำตอบทั้งหมด (เฉพาะหมวดที่นำไปคำนวณ)
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.question_type', 'radio');
            $this->db->where('a.answer_value REGEXP', '^[1-5]$');
            $this->db->where('c.is_active', 1);
            $this->db->where('c.is_scoring', 1); // เฉพาะหมวดที่นำไปคำนวณ

            $total_result = $this->db->get()->row();
            $total_answers = $total_result ? intval($total_result->total) : 0;

            if ($total_answers == 0) {
                return ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
            }

            // ดึงข้อมูลการแจกแจงคะแนน
            for ($score = 1; $score <= 5; $score++) {
                $this->db->select('COUNT(*) as count');
                $this->db->from('tbl_assessment_answers a');
                $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('r.is_completed', 1);
                $this->db->where('q.question_type', 'radio');
                $this->db->where('a.answer_value', $score);
                $this->db->where('c.is_active', 1);
                $this->db->where('c.is_scoring', 1); // เฉพาะหมวดที่นำไปคำนวณ

                $result = $this->db->get()->row();
                $distribution[$score] = $result ? intval($result->count) : 0;
            }

        } catch (Exception $e) {
            log_message('error', 'Get Score Distribution Error: ' . $e->getMessage());
            $distribution = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0];
        }

        return $distribution;
    }

    /**
     * ดึงสถิติรายวัน (7 วันล่าสุด)
     */
    private function get_daily_statistics()
    {
        $stats = [];

        try {
            if (!$this->db->table_exists('tbl_assessment_responses')) {
                return [];
            }

            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));

                $count = $this->db->where('is_completed', 1)
                    ->where('DATE(completed_at)', $date)
                    ->count_all_results('tbl_assessment_responses');

                $thai_months = [
                    'Jan' => 'ม.ค.',
                    'Feb' => 'ก.พ.',
                    'Mar' => 'มี.ค.',
                    'Apr' => 'เม.ย.',
                    'May' => 'พ.ค.',
                    'Jun' => 'มิ.ย.',
                    'Jul' => 'ก.ค.',
                    'Aug' => 'ส.ค.',
                    'Sep' => 'ก.ย.',
                    'Oct' => 'ต.ค.',
                    'Nov' => 'พ.ย.',
                    'Dec' => 'ธ.ค.'
                ];

                $date_thai = date('j M', strtotime($date));
                $date_thai = str_replace(array_keys($thai_months), array_values($thai_months), $date_thai);

                $stats[] = [
                    'date_eng' => $date,
                    'date_thai' => $date_thai,
                    'count' => intval($count)
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'Get Daily Statistics Error: ' . $e->getMessage());
            $stats = [];
        }

        return $stats;
    }

    /**
     * ดึงข้อเสนอแนะล่าสุด (5 รายการ)
     */
    private function get_recent_feedback()
    {
        $feedback = [];

        try {
            if (!$this->db->table_exists('tbl_assessment_answers')) {
                return [];
            }

            $this->db->select('a.answer_text, r.completed_at, c.category_name, q.question_text');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.question_type', 'textarea');
            $this->db->where('a.answer_text !=', '');
            $this->db->where('a.answer_text IS NOT NULL');

            // ✅ เพิ่มเงื่อนไขใหม่: อิงตาม categories
            $this->db->where('c.is_active', 1);        // หมวดที่เปิดใช้งาน
            $this->db->where('c.is_scoring', 0);       // หมวดที่ไม่นำไปคำนวณคะแนน
            $this->db->where('q.is_active', 1);        // คำถามที่เปิดใช้งาน

            $this->db->order_by('r.completed_at', 'DESC');
            $this->db->limit(5);

            $feedback = $this->db->get()->result();

            foreach ($feedback as &$comment) {
                if (strlen($comment->answer_text) > 200) {
                    $comment->answer_text = substr($comment->answer_text, 0, 200) . '...';
                }
                $comment->created_at = $comment->completed_at;
            }

        } catch (Exception $e) {
            log_message('error', 'Get Recent Feedback Error: ' . $e->getMessage());
            $feedback = [];
        }

        return $feedback;
    }




    private function get_recent_responses_filtered($limit = 10)
    {
        $responses = [];

        try {
            if (!$this->db->table_exists('tbl_assessment_responses')) {
                return [];
            }

            // ขั้นตอนที่ 1: ดึงรายการ response ที่สมบูรณ์ก่อน
            $this->db->select('r.id, r.completed_at, r.ip_address, r.browser_fingerprint');
            $this->db->from('tbl_assessment_responses r');
            $this->db->where('r.is_completed', 1);
            $this->db->order_by('r.completed_at', 'DESC');
            $this->db->limit($limit);

            $base_responses = $this->db->get()->result();

            if (empty($base_responses)) {
                return [];
            }

            // ขั้นตอนที่ 2: นับคำตอบของแต่ละ response แยกต่างหาก
            foreach ($base_responses as $response) {
                // นับคำตอบทั้งหมดที่ valid (จากหมวดที่เปิดใช้งาน)
                $this->db->select('COUNT(*) as total_answers');
                $this->db->from('tbl_assessment_answers a');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('a.response_id', $response->id);
                $this->db->where('c.is_active', 1);  // หมวดที่เปิดใช้งาน
                $this->db->where('q.is_active', 1);  // คำถามที่เปิดใช้งาน
                // เพิ่มเงื่อนไขว่าต้องมีคำตอบจริง
                $this->db->where('(a.answer_text IS NOT NULL AND a.answer_text != "") OR (a.answer_value IS NOT NULL AND a.answer_value != "")');

                $total_result = $this->db->get()->row();
                $total_answers = $total_result ? intval($total_result->total_answers) : 0;

                // นับคำตอบประเภทประเมินคะแนน (radio + scoring categories)
                $this->db->select('COUNT(*) as scoring_answers');
                $this->db->from('tbl_assessment_answers a');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('a.response_id', $response->id);
                $this->db->where('c.is_active', 1);
                $this->db->where('c.is_scoring', 1);  // หมวดที่นำไปคำนวณคะแนน
                $this->db->where('q.is_active', 1);
                $this->db->where('q.question_type', 'radio');
                $this->db->where('a.answer_value IS NOT NULL');
                $this->db->where('a.answer_value !=', '');

                $scoring_result = $this->db->get()->row();
                $scoring_answers = $scoring_result ? intval($scoring_result->scoring_answers) : 0;

                // นับคำตอบประเภทข้อเสนอแนะ (textarea + non-scoring categories)
                $this->db->select('COUNT(*) as feedback_answers');
                $this->db->from('tbl_assessment_answers a');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
                $this->db->where('a.response_id', $response->id);
                $this->db->where('c.is_active', 1);
                $this->db->where('c.is_scoring', 0);  // หมวดที่ไม่นำไปคำนวณคะแนน
                $this->db->where('q.is_active', 1);
                $this->db->where('q.question_type', 'textarea');
                $this->db->where('a.answer_text IS NOT NULL');
                $this->db->where('a.answer_text !=', '');

                $feedback_result = $this->db->get()->row();
                $feedback_answers = $feedback_result ? intval($feedback_result->feedback_answers) : 0;

                // เก็บข้อมูลที่นับได้
                $response->answer_count = $total_answers;
                $response->scoring_answers = $scoring_answers;
                $response->feedback_answers = $feedback_answers;
                $response->has_scoring = $scoring_answers > 0;
                $response->has_feedback = $feedback_answers > 0;

                // เอาเฉพาะ response ที่มีคำตอบจากหมวดที่เปิดใช้งาน
                if ($total_answers > 0) {
                    $responses[] = $response;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Get Recent Responses Filtered Error: ' . $e->getMessage());
            $responses = [];
        }

        return $responses;
    }






    private function count_scoring_questions_in_category($category_id)
    {
        try {
            if (!$this->db->table_exists('tbl_assessment_questions')) {
                return 0;
            }

            $count = $this->db->where('category_id', $category_id)
                ->where('question_type', 'radio')
                ->where('is_active', 1)
                ->count_all_results('tbl_assessment_questions');

            return intval($count);

        } catch (Exception $e) {
            log_message('error', 'Count Scoring Questions Error: ' . $e->getMessage());
            return 0;
        }
    }



    /**
     * นับจำนวนคำถามในหมวดหมู่
     */
    private function count_questions_in_category($category_id)
    {
        try {
            if (!$this->db->table_exists('tbl_assessment_questions')) {
                return 0;
            }

            $count = $this->db->where('category_id', $category_id)
                ->where('is_active', 1)
                ->count_all_results('tbl_assessment_questions');

            return intval($count);

        } catch (Exception $e) {
            log_message('error', 'Count Questions Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนผู้ตอบในแต่ละหมวด
     */
    private function get_category_response_count($category_id)
    {
        try {
            if (
                !$this->db->table_exists('tbl_assessment_responses') ||
                !$this->db->table_exists('tbl_assessment_answers') ||
                !$this->db->table_exists('tbl_assessment_questions')
            ) {
                return 0;
            }

            $this->db->select('COUNT(DISTINCT r.id) as count');
            $this->db->from('tbl_assessment_responses r');
            $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.category_id', $category_id);

            $result = $this->db->get()->row();
            return $result ? intval($result->count) : 0;
        } catch (Exception $e) {
            log_message('error', 'Get Category Response Count Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * เช็คสิทธิ์ Admin
     */
    private function check_admin_access()
    {
        $user_id = $this->session->userdata('m_id');

        if (empty($user_id)) {
            return false;
        }

        // ตรวจสอบจาก Database โดยตรง
        $this->db->select('m_status, m_system');
        $this->db->where('m_id', $user_id);
        $user = $this->db->get('tbl_member')->row();

        if ($user && $user->m_status === '1') {
            return true;
        }

        return false;
    }


    public function debug_session()
    {
        // เพิ่มฟังก์ชันนี้เพื่อ debug
        header('Content-Type: application/json');

        $session_data = [
            'm_id' => $this->session->userdata('m_id'),
            'm_status' => $this->session->userdata('m_status'),
            'm_system' => $this->session->userdata('m_system'),
            'm_level' => $this->session->userdata('m_level'),
            'm_fname' => $this->session->userdata('m_fname'),
            'm_lname' => $this->session->userdata('m_lname'),
            'ref_pid' => $this->session->userdata('ref_pid'),
            'all_session' => $this->session->all_userdata()
        ];

        echo json_encode($session_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API: ส่งออกรายงานการประเมิน
     */
    public function export_assessment_report()
    {
        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            show_404();
        }

        try {
            // โหลด Assessment Model
            $this->load->model('assessment_model');

            // ดึงข้อมูลรายงาน
            $responses = $this->assessment_model->get_responses();
            $categories = $this->assessment_model->get_categories();
            $statistics = $this->get_assessment_statistics();

            // สร้าง CSV header
            $csv_data = [];
            $headers = ['วันที่ส่ง', 'เวลา', 'IP Address'];

            // เพิ่ม header คำถาม
            foreach ($categories as $category) {
                $questions = $this->assessment_model->get_questions($category->id);
                foreach ($questions as $question) {
                    $headers[] = $question->question_order . '. ' . $question->question_text;
                }
            }
            $csv_data[] = $headers;

            // เขียนข้อมูลการตอบ
            foreach ($responses as $response) {
                $detail = $this->assessment_model->get_response_detail($response->id);

                $row = [
                    date('d/m/Y', strtotime($response->completed_at)),
                    date('H:i:s', strtotime($response->completed_at)),
                    $response->ip_address
                ];

                // สร้าง array สำหรับคำตอบ indexed โดย question_id
                $answers = [];
                foreach ($detail as $answer) {
                    $value = $answer->answer_text ?: $answer->answer_value;
                    $answers[$answer->question_id] = $value;
                }

                // เพิ่มคำตอบตามลำดับคำถาม
                foreach ($categories as $category) {
                    $questions = $this->assessment_model->get_questions($category->id);
                    foreach ($questions as $question) {
                        $row[] = isset($answers[$question->id]) ? $answers[$question->id] : '';
                    }
                }

                $csv_data[] = $row;
            }

            // เพิ่มสถิติท้ายไฟล์
            $csv_data[] = []; // บรรทัดว่าง
            $csv_data[] = ['=== สถิติการประเมิน ==='];
            $csv_data[] = ['จำนวนผู้ตอบทั้งหมด', $statistics['total_responses']];
            $csv_data[] = ['คะแนนเฉลี่ยรวม', number_format($statistics['average_score'], 2) . '/5.00'];
            $csv_data[] = []; // บรรทัดว่าง
            $csv_data[] = ['คะแนนเฉลี่ยแต่ละหมวด'];

            if (!empty($statistics['categories'])) {
                foreach ($statistics['categories'] as $cat_data) {
                    $csv_data[] = [$cat_data['name'], number_format($cat_data['avg_score'], 2) . '/5.00'];
                }
            }

            // Output CSV
            $filename = 'assessment_results_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // เพิ่ม BOM สำหรับ UTF-8
            echo "\xEF\xBB\xBF";

            $output = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);

        } catch (Exception $e) {
            log_message('error', 'Export Assessment Report Error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งออกรายงาน', 500);
        }
    }

    /**
     * หน้าจัดการแบบฟอร์ม (แยกออกมาใหม่)
     */
    /**
     * หน้าจัดการแบบฟอร์มประเมิน
     */
    private function check_form_management_access()
    {
        $user_id = $this->session->userdata('m_id');
        $user_system = $this->session->userdata('m_system');
        $grant_user_ref_id = $this->session->userdata('grant_user_ref_id');

        if (empty($user_id) || empty($user_system)) {
            return false;
        }

        // System Admin และ Super Admin เข้าได้
        if (in_array($user_system, ['system_admin', 'super_admin'])) {
            return true;
        }

        // User Admin ต้องมี grant '125'
        if ($user_system === 'user_admin') {
            if (!empty($grant_user_ref_id)) {
                $grants = array_map('trim', explode(',', $grant_user_ref_id));
                return in_array('125', $grants);
            }
            return false;
        }

        return false;
    }

    // ✅ 2. ฟังก์ชันสำหรับหน้าจัดการฟอร์ม (แก้ไขจากเดิม)
    public function assessment_form_management()
    {
        // ✅ เช็คสิทธิ์การจัดการฟอร์ม (ไม่ใช่ admin ทั่วไป)
        if (!$this->check_form_management_access()) {
            // แสดงข้อความแจ้งเตือนแทน 404
            $this->session->set_flashdata('error_message', 'ไม่มีสิทธิ์เข้าถึงระบบจัดการฟอร์มประเมิน กรุณาติดต่อผู้ดูแลระบบ');
            redirect('System_reports/assessment_admin');
            return;
        }

        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'จัดการแบบฟอร์มประเมิน';
        $data['tenant_code'] = $this->tenant_code;

        // โหลด Assessment Model
        $this->load->model('assessment_model');

        try {
            // ดึงข้อมูลสำหรับการจัดการฟอร์ม
            $data['statistics'] = $this->get_assessment_statistics();
            $data['categories'] = $this->assessment_model->get_categories(false);
            $data['settings'] = $this->assessment_model->get_all_settings();
            $data['recent_responses'] = $this->assessment_model->get_responses(10);

            // เพิ่มจำนวนคำถามในแต่ละหมวด
            foreach ($data['categories'] as &$category) {
                $category->question_count = $this->count_questions_in_category($category->id);
            }

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');

            // ✅ เพิ่มข้อมูลสิทธิ์สำหรับ UI
            $data['user_permissions'] = $this->get_user_form_permissions();

            // ใช้ reports header/footer
            $this->load->view('reports/header', $data);
            $this->load->view('reports/assessment_form_management', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Assessment Form Management Error: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด');
            redirect('System_reports/assessment_admin');
        }
    }



    private function get_user_form_permissions()
    {
        $user_system = $this->session->userdata('m_system');
        $grant_user_ref_id = $this->session->userdata('grant_user_ref_id');

        $permissions = [
            'can_manage_form' => $this->check_form_management_access(),
            'can_view_only' => true, // ทุกคนดูได้ (ที่เข้า assessment_admin ได้)
            'user_type' => $user_system,
            'grants' => $grant_user_ref_id ? explode(',', $grant_user_ref_id) : [],
            'has_grant_125' => false
        ];

        // ตรวจสอบ grant 125
        if (!empty($grant_user_ref_id)) {
            $grants = array_map('trim', explode(',', $grant_user_ref_id));
            $permissions['has_grant_125'] = in_array('125', $grants);
        }

        return $permissions;
    }



    public function check_form_access_js()
    {
        // สำหรับใช้ใน JavaScript
        header('Content-Type: application/json');

        $result = [
            'can_access' => $this->check_form_management_access(),
            'user_system' => $this->session->userdata('m_system'),
            'message' => ''
        ];

        if (!$result['can_access']) {
            $user_system = $this->session->userdata('m_system');

            if ($user_system === 'user_admin') {
                $result['message'] = 'ต้องการสิทธิ์ Grant ID: 125 เพื่อจัดการฟอร์มประเมิน';
            } else {
                $result['message'] = 'เฉพาะ System Admin และ Super Admin เท่านั้นที่สามารถจัดการฟอร์มได้';
            }
        }

        echo json_encode($result);
        exit;
    }



    /**
     * API: ดึงข้อมูลหมวดหมู่สำหรับ Management
     */
    public function api_get_category_management($id = null)
    {
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        if (!$id || !is_numeric($id)) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่พบ ID หมวดหมู่'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            // ดึงข้อมูลหมวดหมู่พร้อมข้อมูลเสริม
            $this->db->select('*, COALESCE(is_scoring, 1) as is_scoring'); // ✅ ตั้งค่าเริ่มต้นเป็น 1 ถ้าไม่มี
            $this->db->where('id', intval($id));
            $category = $this->db->get('tbl_assessment_categories')->row();

            if ($category) {
                // ✅ เพิ่มข้อมูลจำนวนคำถาม
                $this->db->where('category_id', $category->id);
                $this->db->where('is_active', 1);
                $total_questions = $this->db->count_all_results('tbl_assessment_questions');

                // เพิ่มข้อมูลจำนวนคำถามประเภท radio
                $this->db->where('category_id', $category->id);
                $this->db->where('question_type', 'radio');
                $this->db->where('is_active', 1);
                $radio_questions = $this->db->count_all_results('tbl_assessment_questions');

                // เพิ่มข้อมูลการตอบ
                $this->db->select('COUNT(DISTINCT r.id) as response_count');
                $this->db->from('tbl_assessment_responses r');
                $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
                $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
                $this->db->where('r.is_completed', 1);
                $this->db->where('q.category_id', $category->id);
                $response_result = $this->db->get()->row();
                $response_count = $response_result ? intval($response_result->response_count) : 0;

                // ✅ เตรียมข้อมูลส่งกลับ
                $category_data = [
                    'id' => intval($category->id),
                    'category_name' => $category->category_name,
                    'category_order' => intval($category->category_order),
                    'is_active' => intval($category->is_active),
                    'is_scoring' => intval($category->is_scoring),
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                    'total_questions' => $total_questions,
                    'radio_questions' => $radio_questions,
                    'response_count' => $response_count
                ];

                $this->output->set_output(json_encode([
                    'success' => true,
                    'category' => $category_data
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบหมวดหมู่ที่ต้องการ'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Get Category Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    private function check_api_management_access()
    {
        return $this->check_form_management_access();
    }

    // ✅ 4. อัปเดต API Methods ให้ตรวจสอบสิทธิ์
    public function api_add_category_management()
    {
        $this->output->set_content_type('application/json');

        // ✅ ตรวจสอบสิทธิ์การจัดการฟอร์ม
        if (!$this->check_form_management_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์จัดการฟอร์มประเมิน กรุณาติดต่อผู้ดูแลระบบเพื่อขอสิทธิ์ (Grant ID: 125)'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            // รับข้อมูลจากฟอร์ม
            $category_name = trim($this->input->post('category_name'));
            $category_order = $this->input->post('category_order');
            $is_active = $this->input->post('is_active');
            $is_scoring = $this->input->post('is_scoring');

            // ✅ Validation ข้อมูลอินพุต
            if (empty($category_name)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกชื่อหมวดหมู่'
                ]));
                return;
            }

            // ตรวจสอบความยาวชื่อหมวดหมู่
            if (strlen($category_name) < 3) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ชื่อหมวดหมู่ต้องมีอย่างน้อย 3 ตัวอักษร'
                ]));
                return;
            }

            if (strlen($category_name) > 255) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ชื่อหมวดหมู่ต้องไม่เกิน 255 ตัวอักษร'
                ]));
                return;
            }

            // ตรวจสอบลำดับ
            if (empty($category_order) || !is_numeric($category_order) || intval($category_order) < 1) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกลำดับหมวดหมู่ที่ถูกต้อง (ตัวเลขมากกว่า 0)'
                ]));
                return;
            }

            // ✅ ตรวจสอบชื่อหมวดหมู่ซ้ำ
            $this->db->where('category_name', $category_name);
            $this->db->where('is_active', 1);
            $existing_category = $this->db->get('tbl_assessment_categories')->row();

            if ($existing_category) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ชื่อหมวดหมู่นี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น'
                ]));
                return;
            }

            // ✅ ตรวจสอบลำดับซ้ำ
            $this->db->where('category_order', intval($category_order));
            $this->db->where('is_active', 1);
            $existing_order = $this->db->get('tbl_assessment_categories')->row();

            if ($existing_order) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ลำดับนี้มีการใช้งานแล้ว กรุณาเลือกลำดับอื่น'
                ]));
                return;
            }

            // ✅ เตรียมข้อมูลสำหรับบันทึก
            $data = [
                'category_name' => $category_name,
                'category_order' => intval($category_order),
                'is_active' => ($is_active === '1' || $is_active === 1 || $is_active === true) ? 1 : 0,
                'is_scoring' => ($is_scoring === '1' || $is_scoring === 1 || $is_scoring === true) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // ✅ บันทึกข้อมูล
            $this->db->trans_start();

            if ($this->assessment_model->add_category($data)) {
                $category_id = $this->db->insert_id();

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Transaction failed');
                }

                // ✅ Log การเพิ่มหมวดหมู่
                log_message('info', "New category added: ID={$category_id}, Name='{$category_name}', User=" . $this->session->userdata('m_id'));

                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'เพิ่มหมวดหมู่เรียบร้อยแล้ว',
                    'data' => [
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'is_scoring' => $data['is_scoring']
                    ]
                ]));
            } else {
                $this->db->trans_rollback();
                throw new Exception('Failed to insert category');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'API Add Category Error: ' . $e->getMessage());

            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: อัพเดทหมวดหมู่
     */
    public function api_update_category_management()
    {
        $this->output->set_content_type('application/json');

        // ✅ ตรวจสอบสิทธิ์การจัดการฟอร์ม
        if (!$this->check_form_management_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์จัดการฟอร์มประเมิน กรุณาติดต่อผู้ดูแลระบบเพื่อขอสิทธิ์ (Grant ID: 125)'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            // รับข้อมูลจากฟอร์ม
            $category_id = $this->input->post('category_id');
            $category_name = trim($this->input->post('category_name'));
            $category_order = $this->input->post('category_order');
            $is_active = $this->input->post('is_active');
            $is_scoring = $this->input->post('is_scoring');

            // ✅ Validation ข้อมูลอินพุต
            if (empty($category_id) || !is_numeric($category_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ ID หมวดหมู่ที่ต้องการแก้ไข'
                ]));
                return;
            }

            // ตรวจสอบว่าหมวดหมู่มีอยู่จริง
            $this->db->where('id', intval($category_id));
            $existing_category = $this->db->get('tbl_assessment_categories')->row();

            if (!$existing_category) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบหมวดหมู่ที่ต้องการแก้ไข'
                ]));
                return;
            }

            if (empty($category_name)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกชื่อหมวดหมู่'
                ]));
                return;
            }

            // ตรวจสอบความยาวชื่อหมวดหมู่
            if (strlen($category_name) < 3) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ชื่อหมวดหมู่ต้องมีอย่างน้อย 3 ตัวอักษร'
                ]));
                return;
            }

            if (strlen($category_name) > 255) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ชื่อหมวดหมู่ต้องไม่เกิน 255 ตัวอักษร'
                ]));
                return;
            }

            // ตรวจสอบลำดับ
            if (empty($category_order) || !is_numeric($category_order) || intval($category_order) < 1) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกลำดับหมวดหมู่ที่ถูกต้อง (ตัวเลขมากกว่า 0)'
                ]));
                return;
            }

            // ✅ ตรวจสอบชื่อหมวดหมู่ซ้ำ (ยกเว้นตัวเอง)
            $this->db->where('category_name', $category_name);
            $this->db->where('id !=', intval($category_id));
            $this->db->where('is_active', 1);
            $duplicate_name = $this->db->get('tbl_assessment_categories')->row();

            if ($duplicate_name) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ชื่อหมวดหมู่นี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น'
                ]));
                return;
            }

            // ✅ ตรวจสอบลำดับซ้ำ (ยกเว้นตัวเอง)
            $this->db->where('category_order', intval($category_order));
            $this->db->where('id !=', intval($category_id));
            $this->db->where('is_active', 1);
            $duplicate_order = $this->db->get('tbl_assessment_categories')->row();

            if ($duplicate_order) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ลำดับนี้มีการใช้งานแล้ว กรุณาเลือกลำดับอื่น'
                ]));
                return;
            }

            // ✅ เตรียมข้อมูลสำหรับอัพเดท (ลบการเช็ค Radio Questions)
            $old_is_scoring = intval($existing_category->is_scoring ?? 1);
            $new_is_scoring = ($is_scoring === '1' || $is_scoring === 1 || $is_scoring === true) ? 1 : 0;

            $data = [
                'category_name' => $category_name,
                'category_order' => intval($category_order),
                'is_active' => ($is_active === '1' || $is_active === 1 || $is_active === true) ? 1 : 0,
                'is_scoring' => $new_is_scoring,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // ✅ อัพเดทข้อมูล
            $this->db->trans_start();

            if ($this->assessment_model->update_category(intval($category_id), $data)) {
                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Transaction failed');
                }

                // ✅ Log การอัพเดทหมวดหมู่
                log_message('info', "Category updated: ID={$category_id}, Name='{$category_name}', is_scoring={$new_is_scoring}, User=" . $this->session->userdata('m_id'));

                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'อัพเดทหมวดหมู่เรียบร้อยแล้ว',
                    'data' => [
                        'category_id' => intval($category_id),
                        'category_name' => $category_name,
                        'is_scoring' => $new_is_scoring,
                        'changed_scoring' => $old_is_scoring !== $new_is_scoring
                    ]
                ]));
            } else {
                $this->db->trans_rollback();
                throw new Exception('Failed to update category');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'API Update Category Error: ' . $e->getMessage());

            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัพเดทหมวดหมู่: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: ลบหมวดหมู่
     */
    public function api_delete_category_management()
    {
        $this->output->set_content_type('application/json');

        if (!$this->check_api_management_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์จัดการฟอร์มประเมิน'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $input = json_decode($this->input->raw_input_stream, true);
            $category_id = isset($input['category_id']) ? $input['category_id'] : null;

            if (empty($category_id) || !is_numeric($category_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ ID หมวดหมู่'
                ]));
                return;
            }

            // ตรวจสอบว่ามีคำถามในหมวดหมู่นี้หรือไม่
            $questions = $this->assessment_model->get_questions($category_id, false);
            if (!empty($questions)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถลบหมวดหมู่ที่มีคำถามอยู่ได้ กรุณาลบคำถามก่อน'
                ]));
                return;
            }

            if ($this->assessment_model->delete_category($category_id)) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'ลบหมวดหมู่เรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการลบหมวดหมู่'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Delete Category Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: ดึงคำถามในหมวดหมู่
     */
    public function api_get_questions_management($category_id = null)
    {
        // Set JSON header first
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        if (!$category_id) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่พบ ID หมวดหมู่'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $questions = $this->assessment_model->get_questions($category_id, false);
            $category = $this->assessment_model->get_category($category_id);

            $this->output->set_output(json_encode([
                'success' => true,
                'questions' => $questions,
                'category_name' => $category ? $category->category_name : ''
            ]));

        } catch (Exception $e) {
            log_message('error', 'API Get Questions Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: ดึงข้อมูลคำถาม
     */
    public function api_get_question_management($id = null)
    {
        // Set JSON header first
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        if (!$id) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่พบ ID คำถาม'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');
            $question = $this->assessment_model->get_question($id);

            if ($question) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'question' => $question
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบคำถาม'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Get Question Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: เพิ่มคำถาม
     */
    public function api_add_question_management()
    {
        // Set JSON header first
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $category_id = $this->input->post('category_id');
            $question_text = $this->input->post('question_text');
            $question_order = $this->input->post('question_order');
            $question_type = $this->input->post('question_type');
            $is_required = $this->input->post('is_required');
            $is_active = $this->input->post('is_active');

            // Basic validation
            if (empty($question_text)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกคำถาม'
                ]));
                return;
            }

            if (empty($category_id) || !is_numeric($category_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณาเลือกหมวดหมู่'
                ]));
                return;
            }

            $data = [
                'category_id' => intval($category_id),
                'question_text' => trim($question_text),
                'question_order' => intval($question_order ?: 1),
                'question_type' => $question_type ?: 'radio',
                'is_required' => $is_required ? 1 : 0,
                'is_active' => $is_active ? 1 : 0
            ];

            $question_id = $this->assessment_model->add_question($data);

            if ($question_id) {
                // สร้างตัวเลือกพื้นฐานถ้าเป็น radio
                if ($data['question_type'] === 'radio') {
                    $this->create_default_options_management($question_id, $data['category_id']);
                }

                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'เพิ่มคำถามเรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการเพิ่มคำถาม'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Add Question Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: อัพเดทคำถาม
     */
    public function api_update_question_management()
    {
        // Set JSON header first
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $question_id = $this->input->post('question_id');
            $question_text = $this->input->post('question_text');
            $question_order = $this->input->post('question_order');
            $question_type = $this->input->post('question_type');
            $is_required = $this->input->post('is_required');
            $is_active = $this->input->post('is_active');

            // Basic validation
            if (empty($question_id) || !is_numeric($question_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ ID คำถาม'
                ]));
                return;
            }

            if (empty($question_text)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกคำถาม'
                ]));
                return;
            }

            $data = [
                'question_text' => trim($question_text),
                'question_order' => intval($question_order ?: 1),
                'question_type' => $question_type ?: 'radio',
                'is_required' => $is_required ? 1 : 0,
                'is_active' => $is_active ? 1 : 0
            ];

            if ($this->assessment_model->update_question($question_id, $data)) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'อัพเดทคำถามเรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการอัพเดท'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Update Question Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: ลบคำถาม
     */
    public function api_delete_question_management()
    {
        // Set JSON header first
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $input = json_decode($this->input->raw_input_stream, true);
            $question_id = isset($input['question_id']) ? $input['question_id'] : null;

            if (empty($question_id) || !is_numeric($question_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ ID คำถาม'
                ]));
                return;
            }

            if ($this->assessment_model->delete_question($question_id)) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'ลบคำถามเรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการลบคำถาม'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Delete Question Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: บันทึกการตั้งค่า
     */
    public function api_save_settings_management()
    {
        // Set JSON header first
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $settings = $this->input->post();
            $success_count = 0;

            foreach ($settings as $key => $value) {
                if ($key !== 'submit') {
                    if ($this->assessment_model->update_setting($key, $value)) {
                        $success_count++;
                    }
                }
            }

            if ($success_count > 0) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีการเปลี่ยนแปลง'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Save Settings Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * สร้างตัวเลือกพื้นฐานสำหรับคำถามใหม่
     */
    private function create_default_options_management($question_id, $category_id)
    {
        try {
            $this->load->model('assessment_model');

            $category = $this->assessment_model->get_category($category_id);
            $question = $this->assessment_model->get_question($question_id);

            // ถ้าเป็น textarea ไม่ต้องสร้าง options
            if ($question->question_type === 'textarea') {
                return;
            }

            // ถ้าเป็นหมวดข้อมูลทั่วไป ไม่ต้องสร้างตัวเลือกคะแนน
            if (
                strpos($category->category_name, 'ข้อมูลทั่วไป') !== false ||
                strpos($category->category_name, 'ข้อเสนอแนะ') !== false
            ) {
                return;
            }

            // ถ้าเป็นหมวดประเมิน ให้สร้างตัวเลือกคะแนน 1-5
            if (
                strpos($category->category_name, 'การให้บริการ') !== false ||
                strpos($category->category_name, 'บุคลากร') !== false ||
                strpos($category->category_name, 'สถานที่') !== false
            ) {

                $rating_options = [
                    ['text' => 'ควรปรับปรุง (1 คะแนน)', 'value' => '1'],
                    ['text' => 'พอใช้ (2 คะแนน)', 'value' => '2'],
                    ['text' => 'ปานกลาง (3 คะแนน)', 'value' => '3'],
                    ['text' => 'ดี (4 คะแนน)', 'value' => '4'],
                    ['text' => 'ดีมาก (5 คะแนน)', 'value' => '5']
                ];

                foreach ($rating_options as $index => $option) {
                    $option_data = [
                        'question_id' => $question_id,
                        'option_text' => $option['text'],
                        'option_value' => $option['value'],
                        'option_order' => $index + 1,
                        'is_active' => 1
                    ];
                    $this->assessment_model->add_option($option_data);
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Create Default Options Error: ' . $e->getMessage());
        }
    }




    /**
     * API Methods สำหรับจัดการ Options - เพิ่มใน System_reports.php
     */

    /**
     * API: ดึงตัวเลือกของคำถาม
     */
    public function api_get_question_options($question_id = null)
    {
        $this->output->set_content_type('application/json');

        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        if (!$question_id) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่พบ ID คำถาม'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');
            $options = $this->assessment_model->get_options($question_id, false);

            $this->output->set_output(json_encode([
                'success' => true,
                'options' => $options
            ]));

        } catch (Exception $e) {
            log_message('error', 'API Get Question Options Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: เพิ่มตัวเลือกใหม่
     */
    public function api_add_option()
    {
        $this->output->set_content_type('application/json');

        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $question_id = $this->input->post('question_id');
            $option_text = $this->input->post('option_text');
            $option_value = $this->input->post('option_value');
            $option_order = $this->input->post('option_order');
            $is_active = $this->input->post('is_active');

            if (empty($question_id) || !is_numeric($question_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ ID คำถาม'
                ]));
                return;
            }

            if (empty($option_text)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกข้อความตัวเลือก'
                ]));
                return;
            }

            $data = [
                'question_id' => intval($question_id),
                'option_text' => trim($option_text),
                'option_value' => trim($option_value ?: $option_text),
                'option_order' => intval($option_order ?: 1),
                'is_active' => $is_active ? 1 : 0
            ];

            if ($this->assessment_model->add_option($data)) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'เพิ่มตัวเลือกเรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการเพิ่มตัวเลือก'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Add Option Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: อัพเดทตัวเลือก
     */
    public function api_update_option()
    {
        $this->output->set_content_type('application/json');

        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $option_id = $this->input->post('option_id');
            $option_text = $this->input->post('option_text');
            $option_value = $this->input->post('option_value');
            $option_order = $this->input->post('option_order');
            $is_active = $this->input->post('is_active');

            if (empty($option_id) || !is_numeric($option_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ ID ตัวเลือก'
                ]));
                return;
            }

            if (empty($option_text)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกข้อความตัวเลือก'
                ]));
                return;
            }

            $data = [
                'option_text' => trim($option_text),
                'option_value' => trim($option_value ?: $option_text),
                'option_order' => intval($option_order ?: 1),
                'is_active' => $is_active ? 1 : 0
            ];

            if ($this->assessment_model->update_option($option_id, $data)) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'อัพเดทตัวเลือกเรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการอัพเดท'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Update Option Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * API: ลบตัวเลือก
     */
    public function api_delete_option()
    {
        $this->output->set_content_type('application/json');

        if (!$this->check_admin_access()) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]));
            return;
        }

        try {
            $this->load->model('assessment_model');

            $input = json_decode($this->input->raw_input_stream, true);
            $option_id = isset($input['option_id']) ? $input['option_id'] : null;

            if (empty($option_id) || !is_numeric($option_id)) {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ ID ตัวเลือก'
                ]));
                return;
            }

            if ($this->assessment_model->delete_option($option_id)) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'ลบตัวเลือกเรียบร้อยแล้ว'
                ]));
            } else {
                $this->output->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการลบตัวเลือก'
                ]));
            }

        } catch (Exception $e) {
            log_message('error', 'API Delete Option Error: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]));
        }
    }





    /**
     * หน้าแสดงข้อเสนอแนะและความคิดเห็นทั้งหมด
     */
    public function assessment_comments()
    {
        // ✅ เช็คสิทธิ์ admin
        if (!$this->check_admin_access()) {
            show_404();
        }

        $data['user_info'] = $this->get_user_info();
        $data['page_title'] = 'ข้อเสนอแนะและความคิดเห็นจากการประเมิน';
        $data['tenant_code'] = $this->tenant_code;

        // โหลด Assessment Model
        $this->load->model('assessment_model');

        // การแบ่งหน้า
        $config['base_url'] = site_url('System_reports/assessment_comments');
        $config['total_rows'] = $this->get_total_comments();
        $config['per_page'] = 20;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        // Pagination styling
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'หน้าแรก';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'หน้าสุดท้าย';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'ถัดไป';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'ก่อนหน้า';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        // ดึงข้อมูลตาม pagination
        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['comments'] = $this->get_all_comments($config['per_page'], $page);
        $data['pagination'] = $this->pagination->create_links();

        // สถิติข้อเสนอแนะ
        $data['stats'] = $this->get_comments_statistics();

        // ตัวกรอง
        $data['filter'] = $this->input->get();

        // ใช้ reports header/footer
        $this->load->view('reports/header', $data);
        $this->load->view('reports/assessment_comments', $data);
        $this->load->view('reports/footer');
    }

    /**
     * ดึงข้อเสนอแนะทั้งหมด พร้อม pagination และ filter
     */
    private function get_all_comments($limit = 20, $offset = 0)
    {
        try {
            $this->db->select('
            a.answer_text, 
            r.completed_at, 
            r.ip_address,
            q.question_text,
            c.category_name,
            r.id as response_id
        ');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.question_type', 'textarea');
            $this->db->where('a.answer_text !=', '');
            $this->db->where('a.answer_text IS NOT NULL');

            // ตัวกรอง
            $search = $this->input->get('search');
            if (!empty($search)) {
                $this->db->like('a.answer_text', $search);
            }

            $date_from = $this->input->get('date_from');
            if (!empty($date_from)) {
                $this->db->where('DATE(r.completed_at) >=', $date_from);
            }

            $date_to = $this->input->get('date_to');
            if (!empty($date_to)) {
                $this->db->where('DATE(r.completed_at) <=', $date_to);
            }

            $category = $this->input->get('category');
            if (!empty($category)) {
                $this->db->where('c.id', $category);
            }

            $this->db->order_by('r.completed_at', 'DESC');
            $this->db->limit($limit, $offset);

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Get All Comments Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * นับจำนวนข้อเสนอแนะทั้งหมด
     */
    private function get_total_comments()
    {
        try {
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.question_type', 'textarea');
            $this->db->where('a.answer_text !=', '');
            $this->db->where('a.answer_text IS NOT NULL');

            // ตัวกรอง
            $search = $this->input->get('search');
            if (!empty($search)) {
                $this->db->like('a.answer_text', $search);
            }

            $date_from = $this->input->get('date_from');
            if (!empty($date_from)) {
                $this->db->where('DATE(r.completed_at) >=', $date_from);
            }

            $date_to = $this->input->get('date_to');
            if (!empty($date_to)) {
                $this->db->where('DATE(r.completed_at) <=', $date_to);
            }

            $category = $this->input->get('category');
            if (!empty($category)) {
                $this->db->where('c.id', $category);
            }

            return $this->db->count_all_results();

        } catch (Exception $e) {
            log_message('error', 'Get Total Comments Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * สถิติข้อเสนอแนะ
     */
    private function get_comments_statistics()
    {
        $stats = [];

        try {
            // จำนวนข้อเสนอแนะทั้งหมด
            $stats['total_comments'] = $this->get_total_comments_count();

            // ข้อเสนอแนะวันนี้
            $today = date('Y-m-d');
            $stats['today_comments'] = $this->get_comments_by_date($today);

            // ข้อเสนอแนะ 7 วันล่าสุด
            $stats['week_comments'] = $this->get_comments_by_date_range(date('Y-m-d', strtotime('-7 days')), date('Y-m-d'));

            // ข้อเสนอแนะเดือนนี้
            $stats['month_comments'] = $this->get_comments_by_date_range(date('Y-m-01'), date('Y-m-t'));

            // คำที่พบบ่อย (Top keywords)
            $stats['top_keywords'] = $this->get_top_keywords();

            // ข้อเสนอแนะแต่ละหมวด
            $stats['by_category'] = $this->get_comments_by_category();

        } catch (Exception $e) {
            log_message('error', 'Get Comments Statistics Error: ' . $e->getMessage());
            $stats = [
                'total_comments' => 0,
                'today_comments' => 0,
                'week_comments' => 0,
                'month_comments' => 0,
                'top_keywords' => [],
                'by_category' => []
            ];
        }

        return $stats;
    }

    /**
     * นับจำนวนข้อเสนอแนะทั้งหมด (ไม่มี filter)
     */
    private function get_total_comments_count()
    {
        $this->db->from('tbl_assessment_answers a');
        $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
        $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
        $this->db->where('r.is_completed', 1);
        $this->db->where('q.question_type', 'textarea');
        $this->db->where('a.answer_text !=', '');
        $this->db->where('a.answer_text IS NOT NULL');

        return $this->db->count_all_results();
    }

    /**
     * นับข้อเสนอแนะตามวันที่
     */
    private function get_comments_by_date($date)
    {
        $this->db->from('tbl_assessment_answers a');
        $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
        $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
        $this->db->where('r.is_completed', 1);
        $this->db->where('q.question_type', 'textarea');
        $this->db->where('a.answer_text !=', '');
        $this->db->where('a.answer_text IS NOT NULL');
        $this->db->where('DATE(r.completed_at)', $date);

        return $this->db->count_all_results();
    }

    /**
     * นับข้อเสนอแนะตามช่วงวันที่
     */
    private function get_comments_by_date_range($date_from, $date_to)
    {
        $this->db->from('tbl_assessment_answers a');
        $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
        $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
        $this->db->where('r.is_completed', 1);
        $this->db->where('q.question_type', 'textarea');
        $this->db->where('a.answer_text !=', '');
        $this->db->where('a.answer_text IS NOT NULL');
        $this->db->where('DATE(r.completed_at) >=', $date_from);
        $this->db->where('DATE(r.completed_at) <=', $date_to);

        return $this->db->count_all_results();
    }

    /**
     * ดึงคำที่พบบ่อยในข้อเสนอแนะ
     */
    private function get_top_keywords()
    {
        try {
            // ดึงข้อความทั้งหมด
            $this->db->select('a.answer_text');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.question_type', 'textarea');
            $this->db->where('a.answer_text !=', '');
            $this->db->where('a.answer_text IS NOT NULL');

            $comments = $this->db->get()->result();

            // นับคำที่พบบ่อย (คำที่มีความยาวมากกว่า 2 ตัวอักษร)
            $word_count = [];
            $ignore_words = ['และ', 'ใน', 'ที่', 'เป็น', 'มี', 'จาก', 'ของ', 'กับ', 'ไป', 'มา', 'ให้', 'จะ', 'ได้', 'แล้ว', 'ไม่', 'ยัง', 'หรือ', 'เพื่อ'];

            foreach ($comments as $comment) {
                $words = preg_split('/[\s,\.!?;:]+/', $comment->answer_text);
                foreach ($words as $word) {
                    $word = trim($word);
                    if (mb_strlen($word, 'UTF-8') > 2 && !in_array($word, $ignore_words)) {
                        $word_count[$word] = isset($word_count[$word]) ? $word_count[$word] + 1 : 1;
                    }
                }
            }

            // เรียงตามความถี่และเอา 10 อันดับแรก
            arsort($word_count);
            return array_slice($word_count, 0, 10, true);

        } catch (Exception $e) {
            log_message('error', 'Get Top Keywords Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * นับข้อเสนอแนะแต่ละหมวด
     */
    private function get_comments_by_category()
    {
        try {
            $this->db->select('c.category_name, COUNT(*) as count');
            $this->db->from('tbl_assessment_answers a');
            $this->db->join('tbl_assessment_responses r', 'a.response_id = r.id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.is_completed', 1);
            $this->db->where('q.question_type', 'textarea');
            $this->db->where('a.answer_text !=', '');
            $this->db->where('a.answer_text IS NOT NULL');
            $this->db->group_by('c.id');
            $this->db->order_by('count', 'DESC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Get Comments By Category Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ส่งออกข้อเสนอแนะเป็น CSV
     */
    public function export_comments_csv()
    {
        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            show_404();
        }

        try {
            // ดึงข้อมูลทั้งหมด (ไม่จำกัด pagination)
            $comments = $this->get_all_comments(999999, 0);

            // สร้าง CSV
            $csv_data = [];
            $csv_data[] = ['วันที่', 'เวลา', 'หมวดหมู่', 'คำถาม', 'ข้อเสนอแนะ', 'IP Address'];

            foreach ($comments as $comment) {
                $csv_data[] = [
                    date('d/m/Y', strtotime($comment->completed_at)),
                    date('H:i:s', strtotime($comment->completed_at)),
                    $comment->category_name,
                    $comment->question_text,
                    $comment->answer_text,
                    $comment->ip_address
                ];
            }

            // Output CSV
            $filename = 'assessment_comments_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // เพิ่ม BOM สำหรับ UTF-8
            echo "\xEF\xBB\xBF";

            $output = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);

        } catch (Exception $e) {
            log_message('error', 'Export Comments CSV Error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งออกข้อมูล', 500);
        }
    }




    /**
     * API: ดึงรายละเอียดการตอบแบบประเมินแต่ละคน
     */
    public function get_response_detail($response_id)
    {
        // ตรวจสอบสิทธิ์
        if (!$this->check_admin_access()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']));
            return;
        }

        try {
            // โหลด Assessment Model
            $this->load->model('assessment_model');

            // ดึงข้อมูลการตอบ
            $this->db->select('
            r.*,
            a.question_id,
            a.answer_text,
            a.answer_value,
            q.question_text,
            q.question_type,
            q.question_order,
            c.category_name,
            c.category_order
        ');
            $this->db->from('tbl_assessment_responses r');
            $this->db->join('tbl_assessment_answers a', 'r.id = a.response_id');
            $this->db->join('tbl_assessment_questions q', 'a.question_id = q.id');
            $this->db->join('tbl_assessment_categories c', 'q.category_id = c.id');
            $this->db->where('r.id', $response_id);
            $this->db->where('r.is_completed', 1);
            $this->db->order_by('c.category_order, q.question_order');

            $details = $this->db->get()->result();

            if (empty($details)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบข้อมูลการตอบแบบประเมิน'
                    ]));
                return;
            }

            // จัดรูปแบบข้อมูล
            $response_info = [
                'id' => $details[0]->id,
                'completed_at' => $details[0]->completed_at,
                'ip_address' => $details[0]->ip_address,
                'answers' => []
            ];

            foreach ($details as $detail) {
                $answer_value = '';

                // แปลงคำตอบตามประเภทคำถาม
                if ($detail->question_type === 'radio') {
                    if ($detail->answer_value) {
                        switch ($detail->answer_value) {
                            case '1':
                                $answer_value = '1 - ควรปรับปรุง';
                                break;
                            case '2':
                                $answer_value = '2 - พอใช้';
                                break;
                            case '3':
                                $answer_value = '3 - ปานกลาง';
                                break;
                            case '4':
                                $answer_value = '4 - ดี';
                                break;
                            case '5':
                                $answer_value = '5 - ดีมาก';
                                break;
                            default:
                                $answer_value = $detail->answer_value;
                        }
                    }
                } else {
                    $answer_value = $detail->answer_text ?: $detail->answer_value;
                }

                $response_info['answers'][] = [
                    'question_id' => $detail->question_id,
                    'question_text' => $detail->question_text,
                    'question_type' => $detail->question_type,
                    'category_name' => $detail->category_name,
                    'answer_text' => $detail->answer_text,
                    'answer_value' => $answer_value
                ];
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'response' => $response_info
                ]));

        } catch (Exception $e) {
            log_message('error', 'Get Response Detail Error: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
                ]));
        }
    }



    public function api_assessment_summary()
    {
        // Set header เป็น JSON
        $this->output->set_content_type('application/json');

        try {
            // โหลด Assessment Model
            $this->load->model('assessment_model');

            // ดึงสถิติการประเมิน
            $statistics = $this->get_assessment_statistics();

            // จำนวนคำถามทั้งหมด
            $total_questions = $this->db->where('is_active', 1)
                ->count_all_results('tbl_assessment_questions');

            // จัดรูปแบบข้อมูลส่งออก
            $assessment_data = [
                'total' => $statistics['total_responses'] ?? 0,
                'today' => $statistics['today_responses'] ?? 0,
                'avg_score' => $statistics['average_score'] ?? 0,
                'questions' => $total_questions ?? 0
            ];

            // ส่งผลลัพธ์
            $this->output->set_output(json_encode([
                'success' => true,
                'assessment' => $assessment_data,
                'timestamp' => date('Y-m-d H:i:s')
            ]));

        } catch (Exception $e) {
            log_message('error', 'Assessment API Error: ' . $e->getMessage());

            // ส่งข้อมูลเริ่มต้นถ้าเกิด error
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
                'assessment' => [
                    'total' => 0,
                    'today' => 0,
                    'avg_score' => 0,
                    'questions' => 0
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ]));
        }
    }


    public function api_assessment_details()
    {
        $this->output->set_content_type('application/json');

        try {
            // โหลด Assessment Model
            $this->load->model('assessment_model');

            // ดึงข้อมูลเพิ่มเติม
            $score_distribution = $this->get_score_distribution();
            $daily_stats = $this->get_daily_statistics();
            $recent_feedback = $this->get_recent_feedback();

            $this->output->set_output(json_encode([
                'success' => true,
                'score_distribution' => $score_distribution,
                'daily_stats' => $daily_stats,
                'recent_feedback' => $recent_feedback,
                'timestamp' => date('Y-m-d H:i:s')
            ]));

        } catch (Exception $e) {
            log_message('error', 'Assessment Details API Error: ' . $e->getMessage());

            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลรายละเอียด',
                'timestamp' => date('Y-m-d H:i:s')
            ]));
        }
    }


    /**
     * ล้างข้อมูลการตอบ Assessment - เฉพาะ System Admin
     */
    public function clear_assessment_data()
    {
        $this->output->set_content_type('application/json');

        // ตรวจสอบสิทธิ์ - เฉพาะ System Admin
        if ($this->session->userdata('m_system') !== 'system_admin') {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ล้างข้อมูล - เฉพาะ System Admin เท่านั้น'
            ]));
            return;
        }

        if ($this->input->method() !== 'post') {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'Method ไม่ถูกต้อง'
            ]));
            return;
        }

        try {
            $this->db->trans_start();

            // นับจำนวนข้อมูลก่อนลบ
            $answers_count = $this->db->count_all('tbl_assessment_answers');
            $responses_count = $this->db->count_all('tbl_assessment_responses');

            // ลบข้อมูล
            $this->db->empty_table('tbl_assessment_answers');
            $this->db->empty_table('tbl_assessment_responses');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            log_message('info', "Assessment data cleared by System Admin - User ID: " . $this->session->userdata('m_id'));

            $this->output->set_output(json_encode([
                'success' => true,
                'message' => 'ล้างข้อมูลการตอบเรียบร้อยแล้ว',
                'cleared_data' => [
                    'answers' => $answers_count,
                    'responses' => $responses_count
                ]
            ]));

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Clear Assessment Data Error: ' . $e->getMessage());

            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการล้างข้อมูล'
            ]));
        }
    }

    public function get_assessment_data_count()
    {
        $this->output->set_content_type('application/json');

        if ($this->session->userdata('m_system') !== 'system_admin') {
            $this->output->set_output(json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']));
            return;
        }

        $answers_count = $this->db->count_all('tbl_assessment_answers');
        $responses_count = $this->db->count_all('tbl_assessment_responses');
        $completed_responses = $this->db->where('is_completed', 1)->count_all_results('tbl_assessment_responses');

        $this->output->set_output(json_encode([
            'success' => true,
            'data' => [
                'answers' => $answers_count,
                'responses' => $responses_count,
                'completed_responses' => $completed_responses
            ]
        ]));
    }







}