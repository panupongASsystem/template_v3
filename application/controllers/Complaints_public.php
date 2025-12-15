<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaints_public extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('complain_model');
        $this->load->library('session');
        $this->load->helper(['url', 'timeago']);
    }

    /**
     * หน้าแสดงสถานะเรื่องร้องเรียนของ user ที่ login
     */
    public function status()
{
    // ตรวจสอบการ login
    if (!$this->session->userdata('mp_id')) {
        redirect('User');
        return;
    }

    $mp_email = $this->session->userdata('mp_email');
    $session_mp_id = $this->session->userdata('mp_id');
    
    $public_user = $this->get_public_user_by_session($session_mp_id, $mp_email);
    
    if (!$public_user) {
        log_message('error', 'Public user not found in database. Session MP_ID: ' . $session_mp_id . ', Email: ' . $mp_email);
        show_error('ไม่พบข้อมูลผู้ใช้ในระบบ');
        return;
    }

    $user_id = $public_user['id'];
    $mp_email = $public_user['mp_email'];

    log_message('info', "Loading complaints for public user ID: {$user_id}, Email: {$mp_email}");

    // *** เพิ่ม: ดึงข้อมูล mp_img สำหรับแสดงรูปโปรไฟล์ ***
    $data['user_info'] = [
        'id' => $user_id,
        'mp_id' => $public_user['mp_id'],
        'mp_email' => $mp_email,
        'mp_fname' => $public_user['mp_fname'],
        'mp_lname' => $public_user['mp_lname'],
        'mp_img' => $public_user['mp_img'] ?? null,        // เพิ่มข้อมูลรูปโปรไฟล์
        'mp_prefix' => $public_user['mp_prefix'] ?? ''     // เพิ่มคำนำหน้า
    ];

    // ดึงเรื่องร้องเรียนและประมวลผล
    $complaints = $this->get_user_complaints($user_id, $mp_email);
    $data['complaints'] = $this->process_complaints_data($complaints);

    // นับจำนวนตามสถานะ
    $data['status_counts'] = $this->get_status_counts($user_id, $mp_email);

    $complaint_count = count($data['complaints']);
    log_message('info', "Loaded {$complaint_count} complaints for user ID: {$user_id}");

    $this->load->view('public_user/templates/header');
    $this->load->view('public_user/css');
    $this->load->view('public_user/complaints_status', $data);
    $this->load->view('public_user/js');
    $this->load->view('public_user/templates/footer');
}
	
	
    /**
     * ดูรายละเอียดเรื่องร้องเรียนเฉพาะ
     * *** แก้ไข: ใช้ tbl_member_public.id แทน mp_id ***
     */
    public function detail($complain_id)
{
    // ตรวจสอบการ login
    if (!$this->session->userdata('mp_id')) {
        redirect('User');
        return;
    }

    // *** แก้ไข: ใช้ tbl_member_public.id แทน session mp_id ***
    $mp_email = $this->session->userdata('mp_email');
    $session_mp_id = $this->session->userdata('mp_id');
    
    $public_user = $this->get_public_user_by_session($session_mp_id, $mp_email);
    
    if (!$public_user) {
        show_error('ไม่พบข้อมูลผู้ใช้ในระบบ');
        return;
    }

    $user_id = $public_user['id'];       // ใช้ Primary Key
    $mp_email = $public_user['mp_email'];

    // ตรวจสอบว่าเรื่องร้องเรียนนี้เป็นของ user นี้หรือไม่
    $complaint = $this->verify_user_complaint($complain_id, $user_id, $mp_email);
    
    if (!$complaint) {
        log_message('warning', "Unauthorized access attempt to complaint {$complain_id} by user ID {$user_id}");
        show_404();
        return;
    }

    $data['complain_data'] = $complaint;
    $data['complain_details'] = $this->get_complaint_timeline($complain_id);
    $data['complain_images'] = $this->get_complaint_images($complain_id);

    $this->load->view('public_user/templates/header');
    $this->load->view('public_user/css');
    $this->load->view('public_user/complaint_detail', $data);
    $this->load->view('public_user/js');
    $this->load->view('public_user/templates/footer');
}
	
	
	
/*
=================================================================
แก้ไขใน method: get_public_user_by_session()
=================================================================
เพิ่มการ select mp_img
*/

private function get_public_user_by_session($session_mp_id, $mp_email)
{
    try {
        log_message('info', "Searching for public user - Session MP_ID: {$session_mp_id}, Email: {$mp_email}");
        
        // *** แก้ไข: เพิ่ม mp_img ใน SELECT ***
        $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address, mp_img');
        $this->db->from('tbl_member_public');
        
        // ค้นหาด้วย email ก่อน
        $this->db->where('mp_email', $mp_email);
        $this->db->where('mp_status', 1); // เฉพาะ account ที่ active
        
        $user = $this->db->get()->row_array();
        
        if ($user) {
            log_message('info', "Found public user by email: {$mp_email} -> ID: {$user['id']}, MP_ID: {$user['mp_id']}, Image: {$user['mp_img']}");
            return $user;
        }
        
        // ถ้าไม่เจอด้วย email ให้ลองด้วย mp_id
        if ($session_mp_id && $session_mp_id != 2147483647 && $session_mp_id != '2147483647') {
            log_message('info', "Trying to find by mp_id: {$session_mp_id}");
            
            $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address, mp_img');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $session_mp_id);
            $this->db->where('mp_status', 1);
            
            $user = $this->db->get()->row_array();
            
            if ($user) {
                log_message('info', "Found public user by mp_id: {$session_mp_id} -> ID: {$user['id']}, Email: {$user['mp_email']}, Image: {$user['mp_img']}");
                return $user;
            }
        } else {
            log_message('warning', "Skipping mp_id search due to overflow: {$session_mp_id}");
        }
        
        log_message('error', "Public user not found - Session MP_ID: {$session_mp_id}, Email: {$mp_email}");
        return null;
        
    } catch (Exception $e) {
        log_message('error', 'Error in get_public_user_by_session: ' . $e->getMessage());
        return null;
    }
}

	
	
	
	
	
	
    /**
     * ดึงเรื่องร้องเรียนของ user
     */
   private function get_user_complaints($user_id, $mp_email)
    {
        try {
            // *** เพิ่ม: ตรวจสอบประเภทข้อมูลก่อนใช้งาน ***
            if (is_array($user_id)) {
                log_message('error', 'user_id is array: ' . print_r($user_id, true));
                return [];
            }
            
            if (is_array($mp_email)) {
                log_message('error', 'mp_email is array: ' . print_r($mp_email, true));
                return [];
            }

            // แปลงเป็น string เพื่อความปลอดภัย
            $user_id = (string)$user_id;
            $mp_email = (string)$mp_email;
            
            log_message('info', "Searching complaints for user_id: '{$user_id}', email: '{$mp_email}'");

            $sql = "
                SELECT c.*, 
                       cd.complain_detail_status as latest_status,
                       cd.complain_detail_datesave as latest_update
                FROM tbl_complain c
                LEFT JOIN (
                    SELECT 
                        complain_detail_case_id,
                        complain_detail_status,
                        complain_detail_datesave,
                        ROW_NUMBER() OVER (PARTITION BY complain_detail_case_id ORDER BY complain_detail_datesave DESC) as rn
                    FROM tbl_complain_detail
                ) cd ON c.complain_id = cd.complain_detail_case_id AND cd.rn = 1
                WHERE (c.complain_user_id = ? OR c.complain_email = ?)
                ORDER BY c.complain_datesave DESC
            ";
            
            $query = $this->db->query($sql, [$user_id, $mp_email]);
            
            if (!$query) {
                log_message('error', 'Database query failed in get_user_complaints');
                log_message('error', 'Database error: ' . print_r($this->db->error(), true));
                return [];
            }
            
            $results = $query->result_array();
            $results_count = count($results);
            
            log_message('info', "Found {$results_count} complaints for user ID: {$user_id}, Email: {$mp_email}");
            
            return $results;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_user_complaints: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }


    /**
     * ประมวลผลข้อมูล complaints เพื่อเตรียมสำหรับ view
     */
    private function process_complaints_data($complaints)
{
    foreach ($complaints as &$complaint) {
        $latest_status = $complaint['latest_status'] ?: $complaint['complain_status'];
        
        $complaint['status_class'] = $this->get_status_class($latest_status);
        $complaint['status_icon'] = $this->get_status_icon($latest_status);
        $complaint['status_color'] = $this->get_status_color($latest_status);
        $complaint['latest_status_display'] = $latest_status ?: 'รอดำเนินการ';
        
        // *** แก้ไข: ปรับ filter mapping ให้ตรงกับ status counting ***
        $filter_status = 'all';
        switch ($complaint['status_class']) {
            case 'complaint-status-pending':
                $filter_status = 'pending';
                break;
            case 'complaint-status-processing':
                $filter_status = 'processing';
                break;
            case 'complaint-status-completed':
                $filter_status = 'completed';
                break;
            case 'complaint-status-cancelled':
                $filter_status = 'cancelled';
                break;
        }
        $complaint['filter_status'] = $filter_status;
    }
    
    return $complaints;
}
	
	
	
	
	
	
	
    /**
     * Helper Functions สำหรับ Status
     */
    private function get_status_class($status)
{
    // *** แก้ไข: เพิ่ม status เพิ่มเติมและจัดกลุ่มให้ชัดเจน ***
    switch ($status) {
        case 'รอดำเนินการ':
        case 'รับเรื่องแล้ว':
        case 'รอรับเรื่อง':
        case null:
        case '':
            return 'complaint-status-pending';
            
        case 'กำลังดำเนินการ':
            return 'complaint-status-processing';
            
        case 'ดำเนินการเรียบร้อย':
        case 'ดำเนินการแก้ไขเรียบร้อย':
        case 'เสร็จสิ้น':
            return 'complaint-status-completed';
            
        case 'ยกเลิก':
            return 'complaint-status-cancelled';
            
        default:
            // *** เพิ่ม: Log สำหรับ status ที่ไม่รู้จัก ***
            log_message('warning', "Unknown status encountered: '{$status}'");
            return 'complaint-status-pending';
    }
}


    private function get_status_icon($status)
{
    switch ($status) {
        case 'รอดำเนินการ':
        case 'รอรับเรื่อง':
            return 'fas fa-hourglass-half';
        case 'รับเรื่องแล้ว':
            return 'fas fa-inbox';
        case 'กำลังดำเนินการ':
            return 'fas fa-cogs';
        case 'ดำเนินการเรียบร้อย':
        case 'ดำเนินการแก้ไขเรียบร้อย':
        case 'เสร็จสิ้น':
            return 'fas fa-check-circle';
        case 'ยกเลิก':
            return 'fas fa-times-circle';
        case null:
        case '':
            return 'fas fa-question-circle';
        default:
            return 'fas fa-clock';
    }
}

    private function get_status_color($status)
{
    switch ($status) {
        case 'รอดำเนินการ':
        case 'รอรับเรื่อง':
            return '#FFC700';
        case 'รับเรื่องแล้ว':
            return '#ff7849';
        case 'กำลังดำเนินการ':
            return '#e55a2b';
        case 'ดำเนินการเรียบร้อย':
        case 'ดำเนินการแก้ไขเรียบร้อย':
        case 'เสร็จสิ้น':
            return '#00B73E';
        case 'ยกเลิก':
            return '#FF0202';
        case null:
        case '':
            return '#6c757d';
        default:
            return '#FFC700';
    }
}
    /**
     * นับจำนวนตามสถานะ
     */
   private function get_status_counts($user_id, $mp_email)
{
    $counts = [
        'total' => 0,
        'pending' => 0,
        'processing' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];

    try {
        // *** เพิ่ม: ตรวจสอบประเภทข้อมูลก่อนใช้งาน ***
        if (is_array($user_id)) {
            log_message('error', 'user_id is array in get_status_counts: ' . print_r($user_id, true));
            return $counts;
        }
        
        if (is_array($mp_email)) {
            log_message('error', 'mp_email is array in get_status_counts: ' . print_r($mp_email, true));
            return $counts;
        }

        // แปลงเป็น string เพื่อความปลอดภัย
        $user_id = (string)$user_id;
        $mp_email = (string)$mp_email;

        // *** แก้ไข: ปรับ SQL ให้ตรงกับ status mapping ***
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE 
                    WHEN COALESCE(cd.complain_detail_status, c.complain_status) IN ('รอดำเนินการ', 'รับเรื่องแล้ว', 'รอรับเรื่อง') 
                    THEN 1 ELSE 0 END) as pending,
                SUM(CASE 
                    WHEN COALESCE(cd.complain_detail_status, c.complain_status) = 'กำลังดำเนินการ' 
                    THEN 1 ELSE 0 END) as processing,
                SUM(CASE 
                    WHEN COALESCE(cd.complain_detail_status, c.complain_status) IN ('ดำเนินการเรียบร้อย', 'ดำเนินการแก้ไขเรียบร้อย', 'เสร็จสิ้น') 
                    THEN 1 ELSE 0 END) as completed,
                SUM(CASE 
                    WHEN COALESCE(cd.complain_detail_status, c.complain_status) = 'ยกเลิก' 
                    THEN 1 ELSE 0 END) as cancelled
            FROM tbl_complain c
            LEFT JOIN (
                SELECT 
                    complain_detail_case_id,
                    complain_detail_status,
                    ROW_NUMBER() OVER (PARTITION BY complain_detail_case_id ORDER BY complain_detail_datesave DESC) as rn
                FROM tbl_complain_detail
            ) cd ON c.complain_id = cd.complain_detail_case_id AND cd.rn = 1
            WHERE (c.complain_user_id = ? OR c.complain_email = ?)
        ";

        $query = $this->db->query($sql, [$user_id, $mp_email]);
        
        if (!$query) {
            log_message('error', 'Database query failed in get_status_counts');
            log_message('error', 'Database error: ' . print_r($this->db->error(), true));
            return $counts;
        }
        
        $result = $query->row_array();

        if ($result) {
            $counts['total'] = (int)$result['total'];
            $counts['pending'] = (int)$result['pending'];
            $counts['processing'] = (int)$result['processing'];
            $counts['completed'] = (int)$result['completed'];
            $counts['cancelled'] = (int)$result['cancelled'];
        }

        log_message('info', "Status counts for user ID {$user_id}: " . json_encode($counts));

    } catch (Exception $e) {
        log_message('error', 'Error in get_status_counts: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
    }

    return $counts;
}

    /**
     * ตรวจสอบว่าเรื่องร้องเรียนเป็นของ user นี้
     */
   private function verify_user_complaint($complain_id, $user_id, $mp_email)
    {
        try {
            $sql = "
                SELECT c.*
                FROM tbl_complain c
                WHERE c.complain_id = ?
                AND (c.complain_user_id = ? OR c.complain_email = ?)
            ";
            
            $query = $this->db->query($sql, [$complain_id, $user_id, $mp_email]);
            $result = $query->row_array();
            
            if ($result) {
                log_message('info', "Verified ownership for complaint {$complain_id} by user ID {$user_id}");
                return $result;
            } else {
                log_message('warning', "Access denied for complaint {$complain_id} by user ID {$user_id}");
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in verify_user_complaint: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * ดึงประวัติการดำเนินงาน พร้อมรูปภาพ
     */
    private function get_complaint_timeline($complain_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_complain_detail');
        $this->db->where('complain_detail_case_id', $complain_id);
        $this->db->order_by('complain_detail_datesave', 'ASC');
        
        $details = $this->db->get()->result_array();
        
        // ดึงรูปภาพสำหรับแต่ละ detail
        foreach ($details as &$detail) {
            $detail['detail_images'] = $this->get_detail_images($detail['complain_detail_id']);
        }
        
        return $details;
    }

    /**
     * ดึงรูปภาพประกอบเรื่องร้องเรียน (รูปหลัก)
     */
    private function get_complaint_images($complain_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_complain_img');
        $this->db->where('complain_img_ref_id', $complain_id);
        
        return $this->db->get()->result_array();
    }

    /**
     * ดึงรูปภาพการอัพเดทของแต่ละ detail (รูปที่ staff อัพเดท)
     * ใช้ตาราง tbl_complain_status_images ตามโครงสร้างฐานข้อมูลจริง
     */
    private function get_detail_images($detail_id)
    {
        $this->db->select('status_img_id as detail_img_id, 
                          complain_detail_id as detail_img_ref_id, 
                          image_filename as detail_img_img,
                          image_original_name,
                          image_size,
                          uploaded_at as detail_img_datesave,
                          uploaded_by');
        $this->db->from('tbl_complain_status_images');
        $this->db->where('complain_detail_id', $detail_id);
        $this->db->order_by('uploaded_at', 'ASC');
        
        $result = $this->db->get()->result_array();
        
        // แปลงข้อมูลให้ตรงกับ format ที่ view ต้องการ
        $formatted_result = [];
        foreach ($result as $row) {
            $formatted_result[] = [
                'detail_img_id' => $row['detail_img_id'],
                'detail_img_ref_id' => $row['detail_img_ref_id'],
                'detail_img_img' => $row['detail_img_img'],
                'detail_img_datesave' => $row['detail_img_datesave'],
                'image_original_name' => $row['image_original_name'],
                'image_size' => $row['image_size'],
                'uploaded_by' => $row['uploaded_by']
            ];
        }
        
        return $formatted_result;
    }

    /**
     * ดึงข้อมูลสถิติการร้องเรียนสำหรับ Dashboard (เพิ่มเติม)
     */
    private function get_complaint_statistics($user_id, $mp_email) // เปลี่ยน parameter
{
    $stats = [
        'this_month' => 0,
        'last_month' => 0,
        'avg_response_time' => 0,
        'completion_rate' => 0
    ];

    // *** แก้ไข: ใช้ user_id (Primary Key) แทน mp_id ***
    // เรื่องร้องเรียนเดือนนี้
    $this->db->from('tbl_complain');
    $this->db->where("(complain_user_id = '{$user_id}' OR complain_email = '{$mp_email}')");
    $this->db->where('MONTH(complain_datesave)', date('m'));
    $this->db->where('YEAR(complain_datesave)', date('Y'));
    $stats['this_month'] = $this->db->count_all_results();

    // เรื่องร้องเรียนเดือนที่แล้ว
    $last_month = date('m', strtotime('-1 month'));
    $last_month_year = date('Y', strtotime('-1 month'));
    $this->db->from('tbl_complain');
    $this->db->where("(complain_user_id = '{$user_id}' OR complain_email = '{$mp_email}')");
    $this->db->where('MONTH(complain_datesave)', $last_month);
    $this->db->where('YEAR(complain_datesave)', $last_month_year);
    $stats['last_month'] = $this->db->count_all_results();

    return $stats;
}

    /**
     * ฟังก์ชันช่วยสำหรับการแปลงขนาดไฟล์
     */
    private function format_file_size($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงรูปภาพ
     */
    private function verify_image_access($image_id, $user_id, $mp_email)
    {
        $this->db->select('csi.*, cd.complain_detail_case_id');
        $this->db->from('tbl_complain_status_images csi');
        $this->db->join('tbl_complain_detail cd', 'cd.complain_detail_id = csi.complain_detail_id');
        $this->db->join('tbl_complain c', 'c.complain_id = cd.complain_detail_case_id');
        $this->db->where('csi.status_img_id', $image_id);
        $this->db->where("(c.complain_user_id = '{$user_id}' OR c.complain_email = '{$mp_email}')");
        
        return $this->db->get()->row_array();
    }

    /**
     * API สำหรับดูรูปภาพ (เพิ่มเติม)
     */
    public function view_image($image_id)
{
    if (!$this->session->userdata('mp_id')) {
        show_404();
        return;
    }

    // *** แก้ไข: ใช้ tbl_member_public.id แทน session data ***
    $mp_email = $this->session->userdata('mp_email');
    $session_mp_id = $this->session->userdata('mp_id');
    
    $public_user = $this->get_public_user_by_session($session_mp_id, $mp_email);
    
    if (!$public_user) {
        show_404();
        return;
    }

    $user_id = $public_user['id'];       // ใช้ Primary Key
    $mp_email = $public_user['mp_email'];

    $image = $this->verify_image_access($image_id, $user_id, $mp_email);
    
    if (!$image) {
        show_404();
        return;
    }

    $file_path = FCPATH . 'docs/complain_status/' . $image['image_filename'];
    
    if (file_exists($file_path)) {
        $this->load->helper('download');
        force_download($image['image_original_name'], file_get_contents($file_path));
    } else {
        show_404();
    }
}
	
	
	
	
	
}