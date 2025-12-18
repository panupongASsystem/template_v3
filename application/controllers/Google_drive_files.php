<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive Files Controller - Enhanced with Settings Integration
 * 
 * Apple-inspired interface สำหรับ staff/member ในการจัดการไฟล์
 * รองรับทั้ง user-based และ centralized storage modes
 * เพิ่มการตรวจสอบ Trial และ Storage Limits
 * ✅ Fixed: เชื่อมต่อ Google Drive API จริง
 * ✅ Enhanced: ดึงการตั้งค่าจาก tbl_google_drive_settings
 * 
 * Route: google_drive_files/*
 */
class Google_drive_files extends CI_Controller
{

    private $member_id;
    private $storage_mode;
    private $is_trial_mode = false;
    private $trial_storage_limit = 5368709120; // 5GB for trial
    private $full_version_storage_limit = 107374182400; // 100GB
    private $system_settings = [];

    public function __construct()
    {
        parent::__construct();

        // โหลด libraries และ models ที่จำเป็น
        $this->load->helper(['url', 'file', 'security']);
        $this->load->database();

        // ตั้งค่า error handler สำหรับ AJAX requests
        if ($this->input->is_ajax_request()) {
            // กันไม่ให้ PHP error แสดงเป็น HTML
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

            // ตั้งค่า custom error handler
            set_error_handler([$this, 'ajax_error_handler']);
            set_exception_handler([$this, 'ajax_exception_handler']);
        }

        // ตรวจสอบการ login
        if (!$this->session->userdata('m_id')) {
            if ($this->input->is_ajax_request()) {
                $this->safe_json_error('กรุณาเข้าสู่ระบบ', 401);
                exit;
            }
            redirect('User');
        }

        $this->member_id = $this->session->userdata('m_id');

        // โหลดการตั้งค่าระบบก่อน
        $this->load_system_settings();

        $this->storage_mode = $this->get_storage_mode();
        $this->is_trial_mode = $this->check_trial_mode();


        // อัปเดต trial storage limit จากการตั้งค่า
        $this->trial_storage_limit = $this->get_trial_storage_limit();
        $this->full_version_storage_limit = $this->get_full_version_storage_limit(); // จาก system_storage_limit

        // ✅ เพิ่ม Log เพื่อตรวจสอบ
        log_message('info', sprintf(
            '🎯 [Constructor] Trial Mode: %s, Storage Limit: %d bytes (%.2f GB)',
            $this->is_trial_mode ? 'YES' : 'NO',
            $this->trial_storage_limit,
            $this->trial_storage_limit / (1024 * 1024 * 1024)
        ));
    }




    /**
     * 🔧 โหลดการตั้งค่าระบบจาก tbl_google_drive_settings
     */
    private function load_system_settings()
    {
        try {
            // ค่าเริ่มต้นของระบบ
            $default_settings = [
                'max_file_size' => '104857600', // 100MB
                'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
                'default_user_quota' => '5368709120', // 5GB
                'system_storage_mode' => 'user_based',
                'google_drive_enabled' => '1',
                'auto_create_folders' => '1',
                'trial_storage_limit' => '5368709120', // ✅ เพิ่ม: 5GB สำหรับ Trial Mode
                'system_storage_limit' => '214748364800' // ✅ เพิ่ม: 200GB สำหรับระบบ
            ];

            // ดึงการตั้งค่าจากฐานข้อมูล
            if ($this->db->table_exists('tbl_google_drive_settings')) {
                $db_settings = $this->db->select('setting_key, setting_value')
                    ->from('tbl_google_drive_settings')
                    ->where('is_active', 1)
                    ->get()
                    ->result();

                foreach ($db_settings as $setting) {
                    $default_settings[$setting->setting_key] = $setting->setting_value;
                }
            }

            $this->system_settings = $default_settings;

            // ✅ โหลดค่าสำคัญไปยัง Properties
            $this->storage_mode = $this->system_settings['system_storage_mode'] ?? 'centralized';

            // ✅ โหลด trial_storage_limit ไปยัง property
            $this->trial_storage_limit = isset($this->system_settings['trial_storage_limit'])
                ? (int) $this->system_settings['trial_storage_limit']
                : 5368709120; // Default 5GB

            log_message('info', sprintf(
                '⚙️ System settings loaded: storage_mode=%s, trial_limit=%s bytes (%s GB)',
                $this->storage_mode,
                $this->trial_storage_limit,
                round($this->trial_storage_limit / (1024 * 1024 * 1024), 2)
            ));

            log_message('info', 'All settings: ' . json_encode($this->system_settings));

        } catch (Exception $e) {
            log_message('error', 'Load system settings error: ' . $e->getMessage());

            // ใช้ค่าเริ่มต้นถ้าเกิดข้อผิดพลาด
            $this->system_settings = [
                'max_file_size' => '104857600', // 100MB
                'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
                'default_user_quota' => '5368709120', // 5GB
                'system_storage_mode' => 'user_based',
                'google_drive_enabled' => '1',
                'auto_create_folders' => '1',
                'trial_storage_limit' => '5368709120', // ✅ 5GB สำหรับ Trial Mode
                'system_storage_limit' => '214748364800' // ✅ 200GB
            ];

            // ✅ ตั้งค่า Properties แม้เกิด Error
            $this->storage_mode = 'centralized';
            $this->trial_storage_limit = 5368709120;
        }
    }


    /**
     * 🔧 ดึงค่าการตั้งค่าเฉพาะ
     */
    private function get_system_setting($key, $default = null)
    {
        return isset($this->system_settings[$key]) ? $this->system_settings[$key] : $default;
    }

    private function get_trial_storage_limit()
    {
        // ✅ ดึงจาก trial_storage_limit
        $limit = $this->get_system_setting('trial_storage_limit', '5368709120');
        return is_numeric($limit) ? (int) $limit : 5368709120; // Default 5GB
    }

    private function get_full_version_storage_limit()
    {
        // ✅ ใช้ system_storage_limit ที่มีอยู่แล้ว
        $limit = $this->get_system_setting('system_storage_limit', '107374182400');
        return is_numeric($limit) ? (int) $limit : 107374182400; // Default 100GB
    }

    /**
     * 🔧 ดึงขนาดไฟล์สูงสุดที่อนุญาต
     */
    private function get_max_file_size()
    {
        $max_size = $this->get_system_setting('max_file_size', '104857600');
        return is_numeric($max_size) ? (int) $max_size : 104857600; // Default 100MB
    }

    /**
     * 🔧 ดึงประเภทไฟล์ที่อนุญาต
     */
    private function get_allowed_file_types()
    {
        $allowed_types = $this->get_system_setting('allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar');

        if (is_string($allowed_types)) {
            return array_map('trim', explode(',', strtolower($allowed_types)));
        }

        return ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
    }

    /**
     * 🔧 ตรวจสอบว่า Google Drive เปิดใช้งานหรือไม่
     */
    private function is_google_drive_enabled()
    {
        return $this->get_system_setting('google_drive_enabled', '1') === '1';
    }

    /**
     * 🏠 หน้าหลัก Member Files (Apple-inspired Interface)
     */
    public function index()
    {
        $this->files();
    }


    /**
     * 📱 หน้า Member Files (Apple-inspired Interface)
     */
    public function files()
    {
        try {
            // ✅ ตรวจสอบว่า Google Drive เปิดใช้งานหรือไม่
            if (!$this->is_google_drive_enabled()) {
                $this->session->set_flashdata('error', 'Google Drive ถูกปิดใช้งานโดยระบบ');
                redirect('member/dashboard');
                return;
            }

            // ✅ NEW: ตรวจสอบสิทธิ์ตาม Mode
            $access_check = $this->check_access_by_mode();
            if (!$access_check['allowed']) {
                $this->session->set_flashdata('error', $access_check['reason']);
                redirect('member/dashboard');
                return;
            }

            // ✅ ดึงค่า trial_storage_limit จาก Database
            $trial_storage_limit = null;
            if ($this->is_trial_mode) {
                // ดึงจาก property ที่โหลดไว้แล้ว
                $trial_storage_limit = $this->trial_storage_limit;

                // ถ้ายังไม่มี ให้ดึงจาก system_settings
                if (!$trial_storage_limit && isset($this->system_settings['trial_storage_limit'])) {
                    $trial_storage_limit = $this->system_settings['trial_storage_limit'];
                }

                // Log เพื่อ debug
                log_message('info', sprintf(
                    '📊 Trial Storage Limit: %s bytes (%s GB)',
                    $trial_storage_limit,
                    round($trial_storage_limit / (1024 * 1024 * 1024), 2)
                ));
            }

            // ส่งข้อมูลไปยัง view
            $data = [
                'member_info' => $access_check['member'],
                'permission_info' => $access_check['permission'],
                'storage_mode' => $this->storage_mode,
                'is_trial_mode' => $this->is_trial_mode,
                'trial_storage_limit' => $trial_storage_limit,
                'show_trial_modal' => $this->is_trial_mode, // ✅ บอกให้แสดง Trial Modal
                'system_storage' => $this->storage_mode === 'centralized' ? $this->get_system_storage_info() : null,
                'system_settings' => $this->system_settings
            ];

            // โหลดหน้า Apple-inspired interface
            $this->load->view('google_drive/header');
            $this->load->view('google_drive/css');
            $this->load->view('google_drive/main_content', $data); // ส่ง $data ไปด้วย
            $this->load->view('google_drive/javascript', $data);
            $this->load->view('google_drive/footer');
            $this->load->view('member/google_drive_auto_token_js');

        } catch (Exception $e) {
            log_message('error', 'Member files page error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการโหลดหน้า Member Files');
        }
    }




    /**
     * 🔍 ตรวจสอบโหมด Trial
     */
    private function check_trial_mode()
    {
        try {
            // ตรวจสอบจาก tbl_member_modules ว่าโมดูล Google Drive เป็น trial หรือไม่
            $google_drive_module = $this->db->select('is_trial, status')
                ->from('tbl_member_modules')
                ->where('code', 'google_drive')
                // ✅ ลบ ->where('status', 1) ออก
                ->get()
                ->row();

            if (!$google_drive_module) {
                log_message('info', 'Google Drive module not found in tbl_member_modules');
                return false;
            }

            log_message('info', sprintf(
                'Google Drive module status: status=%d, is_trial=%d',
                $google_drive_module->status,
                $google_drive_module->is_trial
            ));

            return $google_drive_module->is_trial == 1;

        } catch (Exception $e) {
            log_message('error', 'Check trial mode error: ' . $e->getMessage());
            return false;
        }
    }


    private function check_access_by_mode()
    {
        try {
            // ดึงข้อมูล member พื้นฐาน
            $member = $this->db->select('m.*, p.pname')
                ->from('tbl_member m')
                ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                ->where('m.m_id', $this->member_id)
                ->get()
                ->row();

            if (!$member) {
                return [
                    'allowed' => false,
                    'reason' => 'ไม่พบข้อมูลผู้ใช้'
                ];
            }

            // ✅ TRIAL MODE: ให้เข้าได้หมด (แค่มี m_id)
            if ($this->is_trial_mode) {
                log_message('info', "Trial mode access granted for member: {$this->member_id}");

                return [
                    'allowed' => true,
                    'member' => $member,
                    'permission' => $this->get_trial_permissions(),
                    'access_type' => 'trial'
                ];
            }

            // ✅ PRODUCTION MODE: ตรวจสอบ storage_access_granted
            return $this->check_production_access($member);

        } catch (Exception $e) {
            log_message('error', 'Check access by mode error: ' . $e->getMessage());
            return [
                'allowed' => false,
                'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์'
            ];
        }
    }

    /**
     * ✅ NEW: ตรวจสอบสิทธิ์สำหรับ Production Mode
     */
    private function check_production_access($member)
    {
        try {
            // ตรวจสอบตามโหมด storage
            if ($this->storage_mode === 'centralized') {
                return $this->check_centralized_production_access($member);
            } else {
                return $this->check_user_based_production_access($member);
            }

        } catch (Exception $e) {
            log_message('error', 'Check production access error: ' . $e->getMessage());
            return [
                'allowed' => false,
                'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์ Production'
            ];
        }
    }

    /**
     * ✅ NEW: ตรวจสอบ Centralized Production Access
     */
    private function check_centralized_production_access($member)
    {
        // ✅ เช็ค storage_access_granted = 1
        if (!$member->storage_access_granted || $member->storage_access_granted != 1) {
            log_message('debug', "Centralized access denied for member {$this->member_id}: storage_access_granted = " . ($member->storage_access_granted ?? 'null'));

            return [
                'allowed' => false,
                'reason' => 'คุณยังไม่ได้รับสิทธิ์ในการเข้าถึง Google Drive (Centralized Storage)'
            ];
        }

        // ดึง permission
        $permission = $this->get_member_permission($this->member_id, $member->ref_pid);

        log_message('info', "Centralized production access granted for member: {$this->member_id}");

        return [
            'allowed' => true,
            'member' => $member,
            'permission' => $permission,
            'access_type' => 'centralized_production'
        ];
    }

    /**
     * ✅ NEW: ตรวจสอบ User-based Production Access
     */
    private function check_user_based_production_access($member)
    {
        // ✅ เช็ค google_drive_enabled = 1
        if (!$member->google_drive_enabled || $member->google_drive_enabled != 1) {
            log_message('debug', "User-based access denied for member {$this->member_id}: google_drive_enabled = " . ($member->google_drive_enabled ?? 'null'));

            return [
                'allowed' => false,
                'reason' => 'Google Drive ยังไม่ได้เปิดใช้งานสำหรับบัญชีนี้'
            ];
        }

        // ✅ เช็คการเชื่อมต่อ Google (สำหรับ Production)
        if (empty($member->google_email) || empty($member->google_access_token)) {
            log_message('debug', "User-based access denied for member {$this->member_id}: missing Google connection");

            return [
                'allowed' => false,
                'reason' => 'กรุณาเชื่อมต่อ Google Drive ก่อนใช้งาน'
            ];
        }

        // ดึง permission
        $permission = $this->get_member_permission($this->member_id, $member->ref_pid);

        log_message('info', "User-based production access granted for member: {$this->member_id}");

        return [
            'allowed' => true,
            'member' => $member,
            'permission' => $permission,
            'access_type' => 'user_based_production'
        ];
    }

    /**
     * ✅ NEW: ดึงสิทธิ์สำหรับ Trial Mode
     */
    private function get_trial_permissions()
    {
        return [
            'permission_type' => 'trial',
            'access_type' => 'trial',
            'can_upload' => true,
            'can_create_folder' => true,
            'can_share' => false, // ปิดการแชร์ใน trial
            'can_delete' => true,
            'can_download' => false, // ปิดการดาวน์โหลดใน trial
            'storage_limit' => $this->trial_storage_limit,
            'is_trial' => true
        ];
    }






    /**
     * 📊 ดึงขีดจำกัดพื้นที่สำหรับ Member (Enhanced with Settings)
     */
    private function get_storage_limit_for_member($member)
    {
        // Trial Mode: ใช้ 5GB
        if ($this->is_trial_mode) {
            return $this->trial_storage_limit; // จาก trial_storage_limit
        }

        // Full Version: ใช้ system_storage_limit
        return $this->full_version_storage_limit; // จาก system_storage_limit (1000GB)
    }


    /**
     * 🏢 ตรวจสอบสิทธิ์สำหรับ Centralized Mode
     */
    private function check_centralized_access($member)
    {
        // ตรวจสอบ storage access
        if (!$member->storage_access_granted) {
            return [
                'allowed' => false,
                'reason' => 'คุณยังไม่ได้รับสิทธิ์ในการเข้าถึง Centralized Storage'
            ];
        }

        // ดึง permission
        $permission = $this->get_member_permission($this->member_id, $member->ref_pid);

        return [
            'allowed' => true,
            'member' => $member,
            'permission' => $permission
        ];
    }

    /**
     * 👤 ตรวจสอบสิทธิ์สำหรับ User-based Mode
     */
    private function check_user_based_access($member)
    {
        // ตรวจสอบ Google Drive enabled
        if (!$member->google_drive_enabled) {
            return [
                'allowed' => false,
                'reason' => 'Google Drive ยังไม่ได้เปิดใช้งานสำหรับบัญชีนี้'
            ];
        }

        // สำหรับ trial mode ไม่จำเป็นต้องเชื่อมต่อ Google
        if (!$this->is_trial_mode) {
            // ตรวจสอบการเชื่อมต่อ Google
            if (empty($member->google_email) || empty($member->google_access_token)) {
                return [
                    'allowed' => false,
                    'reason' => 'กรุณาเชื่อมต่อ Google Drive ก่อนใช้งาน'
                ];
            }
        }

        // ดึง permission
        $permission = $this->get_member_permission($this->member_id, $member->ref_pid);

        return [
            'allowed' => true,
            'member' => $member,
            'permission' => $permission
        ];
    }

    /**
     * 📊 ดึงข้อมูล Member สำหรับ Dashboard (Enhanced with Settings)
     */
    public function get_member_info()
    {
        try {
            // ล้าง output buffer ก่อนเริ่มทำงาน
            $this->clear_output_buffer();

            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                $this->safe_json_error('Invalid request method', 400);
                return;
            }

            // ตรวจสอบ session
            if (!$this->member_id) {
                $this->safe_json_error('ไม่พบ session ผู้ใช้', 401);
                return;
            }

            // ดึงข้อมูล member พื้นฐาน
            $member = $this->db->select('m.*, p.pname')
                ->from('tbl_member m')
                ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                ->where('m.m_id', $this->member_id)
                ->get()
                ->row();

            if (!$member) {
                $this->safe_json_error('ไม่พบข้อมูลผู้ใช้', 404);
                return;
            }

            // ข้อมูลพื้นฐาน (ใช้การตั้งค่าจากระบบ)
            $storage_limit = $this->get_storage_limit_for_member($member);

            $member_info = [
                'member_id' => $this->member_id,
                'name' => $member->m_fname . ' ' . $member->m_lname,
                'email' => $member->m_email,
                'google_email' => $member->google_email ?? '',
                'position' => $member->pname,
                'storage_mode' => $this->storage_mode,
                'is_trial_mode' => $this->is_trial_mode,
                'quota_used' => 0,
                'quota_limit' => $storage_limit,
                'files_count' => 0,
                'accessible_folders_count' => 0,
                'last_access' => $member->pcreate ?? date('Y-m-d H:i:s'),
                'is_connected' => true,
                'permission' => [
                    'permission_type' => 'position_only',
                    'can_upload' => true,
                    'can_create_folder' => false,
                    'can_share' => false,
                    'can_delete' => false
                ],
                'system_settings' => [
                    'max_file_size' => $this->get_max_file_size(),
                    'max_file_size_mb' => round($this->get_max_file_size() / (1024 * 1024), 1),
                    'allowed_file_types' => $this->get_allowed_file_types(),
                    'google_drive_enabled' => $this->is_google_drive_enabled()
                ]
            ];

            // ดึงข้อมูลเพิ่มเติมตามโหมด storage
            if ($this->storage_mode === 'centralized') {
                $this->add_centralized_info($member_info, $member);
            } else {
                $this->add_user_based_info($member_info, $member);
            }

            $this->safe_json_success($member_info, 'ดึงข้อมูลสำเร็จ');

        } catch (Exception $e) {
            log_message('error', 'Get member info error: ' . $e->getMessage());
            $this->safe_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }



    private function add_centralized_info(&$member_info, $member)
    {
        try {
            $system_storage = $this->db->select('total_storage_used, max_storage_limit')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($system_storage) {
                $member_info['quota_used'] = $system_storage->total_storage_used;
            }

            // ✅ เลือก limit ตาม mode
            if ($this->is_trial_mode) {
                $member_info['quota_limit'] = $this->trial_storage_limit; // 5GB
            } else {
                $member_info['quota_limit'] = $this->full_version_storage_limit; // 1000GB
            }

            // ดึงจำนวนไฟล์
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $files_count = $this->db->where('uploaded_by', $this->member_id)
                    ->where('is_active', 1)
                    ->count_all_results('tbl_google_drive_system_files');
                $member_info['files_count'] = $files_count;
            }

            // ดึงจำนวนโฟลเดอร์
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $folders_count = $this->db->where('is_active', 1)
                    ->count_all_results('tbl_google_drive_system_folders');
                $member_info['accessible_folders_count'] = $folders_count;
            }

            $member_info['is_connected'] = $system_storage && $system_storage->is_active == 1;

        } catch (Exception $e) {
            log_message('error', 'Add centralized info error: ' . $e->getMessage());
        }
    }


    /**
     * ✅ เพิ่มข้อมูล User-based Mode (รองรับ Trial Mode) - FIXED
     */
    private function add_user_based_info(&$member_info, $member)
    {
        try {
            log_message('info', sprintf(
                '👤 [User-based Info] Adding info for member_id=%d [Trial: %s]',
                $this->member_id,
                $this->is_trial_mode ? 'YES' : 'NO'
            ));

            $quota_used = isset($member->storage_quota_used) ? (int) $member->storage_quota_used : 0;

            // ✅ เลือก limit ตาม mode
            if ($this->is_trial_mode) {
                $quota_limit = $this->trial_storage_limit; // 5GB
                log_message('info', sprintf(
                    '🎯 [Trial Mode] Using trial limit: %s',
                    $this->format_file_size($quota_limit)
                ));
            } else {
                $quota_limit = $this->full_version_storage_limit; // 1000GB
                log_message('info', sprintf(
                    '💎 [Full Version] Using full version limit: %s',
                    $this->format_file_size($quota_limit)
                ));
            }

            $member_info['quota_used'] = $quota_used;
            $member_info['quota_limit'] = $quota_limit;

            log_message('info', sprintf(
                '📊 [Storage Values] Used: %s, Limit: %s',
                $this->format_file_size($quota_used),
                $this->format_file_size($quota_limit)
            ));

            // ดึงจำนวนไฟล์
            if ($this->db->table_exists('tbl_google_drive_user_files')) {
                $files_count = $this->db->where('owner_id', $this->member_id)
                    ->where('is_active', 1)
                    ->count_all_results('tbl_google_drive_user_files');
                $member_info['files_count'] = $files_count;
            }

            // ดึงจำนวนโฟลเดอร์
            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $query = $this->db->where('member_id', $this->member_id)
                    ->where('is_active', 1);

                if (!$this->is_trial_mode) {
                    $query->where('folder_type !=', 'trial');
                }

                $folders_count = $query->count_all_results('tbl_google_drive_folders');
                $member_info['accessible_folders_count'] = $folders_count;
            }

            // ตรวจสอบการเชื่อมต่อ Google
            if ($this->is_trial_mode) {
                $member_info['is_connected'] = true;
            } else {
                $member_info['is_connected'] = !empty($member->google_email) && !empty($member->google_access_token);
            }

            log_message('info', sprintf(
                '✅ User-based info completed: Used=%s, Limit=%s, Files=%d, Folders=%d',
                $this->format_file_size($quota_used),
                $this->format_file_size($quota_limit),
                $member_info['files_count'] ?? 0,
                $member_info['accessible_folders_count'] ?? 0
            ));

        } catch (Exception $e) {
            log_message('error', 'Add user-based info error: ' . $e->getMessage());
        }
    }

    /**
     * 📂 ดึงโฟลเดอร์ที่เข้าถึงได้ (AJAX) - ✅ Enhanced with detailed logging
     */
    public function get_member_folders()
    {
        try {
            // ล้าง output buffer ก่อนเริ่มทำงาน
            $this->clear_output_buffer();

            log_message('info', '========================================');
            log_message('info', '📂 START: get_member_folders()');
            log_message('info', '========================================');

            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                log_message('error', '❌ Invalid request method - Not AJAX');
                $this->safe_json_error('Invalid request method', 400);
                return;
            }

            log_message('info', '✅ AJAX request validated');

            // ตรวจสอบ session
            if (!$this->member_id) {
                log_message('error', '❌ No member session found');
                $this->safe_json_error('ไม่พบ session ผู้ใช้', 401);
                return;
            }

            log_message('info', '✅ Member ID: ' . $this->member_id);
            log_message('info', '📊 Storage Mode: ' . $this->storage_mode);

            // ดึงโฟลเดอร์ตามโหมด
            if ($this->storage_mode === 'centralized') {
                log_message('info', '🏢 Using Centralized Mode');
                $folders = $this->get_centralized_folders();
            } else {
                log_message('info', '👤 Using User-based Mode');
                $folders = $this->get_user_based_folders();
            }

            log_message('info', '📦 Total folders retrieved: ' . count($folders));

            if (!empty($folders)) {
                log_message('info', '📋 First folder structure: ' . json_encode($folders[0]));
                log_message('info', '📋 Folder names: ' . implode(', ', array_column($folders, 'name')));
            } else {
                log_message('error', '⚠️ No folders returned from API');
            }

            log_message('info', '========================================');
            log_message('info', '✅ END: get_member_folders() - SUCCESS');
            log_message('info', '========================================');

            $this->safe_json_success($folders, 'ดึงข้อมูลโฟลเดอร์สำเร็จ');

        } catch (Exception $e) {
            log_message('error', '========================================');
            log_message('error', '💥 EXCEPTION in get_member_folders()');
            log_message('error', '❌ Error: ' . $e->getMessage());
            log_message('error', '📍 File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            log_message('error', '📚 Trace: ' . $e->getTraceAsString());
            log_message('error', '========================================');

            $this->safe_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }

    /**
     * ✅ ดึง Centralized Folders ที่ Member มีสิทธิ์เข้าถึง
     * 🔧 Fixed: ลบ hardcoded parent_folder_id ที่ผิด, ใช้ dynamic query แทน
     */
    private function get_centralized_folders()
    {
        try {
            log_message('info', "Getting centralized folders for member: {$this->member_id}");

            // ✅ เช็คว่าเป็น System Admin หรือไม่
            $is_system_admin = $this->check_system_folder_access();

            if ($is_system_admin) {
                // ✅ Admin เห็นทุก root folder (Admin, Departments, Shared, Users)
                // ดึง parent_folder_id จาก folder ที่มี folder_type เป็น root
                $sql = "
                SELECT 
                    folder_id as id,
                    folder_name as name,
                    'folder' as type,
                    folder_type,
                    permission_level,
                    created_at
                FROM tbl_google_drive_system_folders
                WHERE folder_type IN ('admin', 'system', 'shared') 
                  AND parent_folder_id = (
                      SELECT DISTINCT parent_folder_id 
                      FROM tbl_google_drive_system_folders 
                      WHERE folder_type IN ('admin', 'system', 'shared')
                        AND is_active = 1
                      LIMIT 1
                  )
                  AND is_active = 1
                ORDER BY 
                    CASE folder_type
                        WHEN 'admin' THEN 1
                        WHEN 'system' THEN 2
                        WHEN 'shared' THEN 3
                        ELSE 4
                    END,
                    folder_name
            ";

                $query = $this->db->query($sql);
                log_message('info', "Admin query - Found: " . $query->num_rows() . " root folders");

            } else {
                // ✅ User ทั่วไป: เห็นเฉพาะ folder ที่มีสิทธิ์
                // ดึงทุก folder ที่ user มี permission (ไม่จำกัด parent)
                $sql = "
                SELECT DISTINCT
                    sf.folder_id as id,
                    sf.folder_name as name,
                    'folder' as type,
                    sf.folder_type,
                    sf.permission_level,
                    sf.created_at,
                    sf.parent_folder_id,
                    mfa.access_type
                FROM tbl_google_drive_system_folders sf
                INNER JOIN tbl_google_drive_member_folder_access mfa 
                    ON sf.folder_id = mfa.folder_id
                WHERE mfa.member_id = ?
                  AND mfa.is_active = 1
                  AND sf.is_active = 1
                  AND (mfa.expires_at IS NULL OR mfa.expires_at > NOW())
                ORDER BY 
                    CASE sf.folder_type
                        WHEN 'admin' THEN 1
                        WHEN 'system' THEN 2
                        WHEN 'shared' THEN 3
                        WHEN 'department' THEN 4
                        WHEN 'user' THEN 5
                        ELSE 6
                    END,
                    sf.folder_name
            ";

                $query = $this->db->query($sql, [$this->member_id]);
                log_message('info', "User query - Found: " . $query->num_rows() . " accessible folders");
            }

            $folders = $query->result_array();

            log_message('info', sprintf(
                "Raw folders from DB: %d folders found",
                count($folders)
            ));

            if (!empty($folders)) {
                log_message('info', 'First 3 folders: ' . json_encode(array_slice($folders, 0, 3)));
            }

            // ✅ Format เป็น contents structure
            $contents = [];
            foreach ($folders as $folder) {
                $contents[] = [
                    'id' => $folder['id'],
                    'name' => $folder['name'],
                    'type' => 'folder',
                    'icon' => $this->get_folder_icon($folder['folder_type']),
                    'mimeType' => 'application/vnd.google-apps.folder',
                    'real_data' => true,
                    'folder_type' => $folder['folder_type'],
                    'permission_level' => $folder['permission_level'],
                    'access_type' => $folder['access_type'] ?? 'read',
                    'created_at' => $folder['created_at']
                ];
            }

            log_message('info', sprintf(
                "✅ Member %d has access to %d centralized folders",
                $this->member_id,
                count($contents)
            ));

            return $contents;

        } catch (Exception $e) {
            log_message('error', '❌ Get centralized folders error: ' . $e->getMessage());
            log_message('error', '📍 Trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * ✅ ดึง User-Based Folders
     */
    private function get_user_based_folders()
    {
        try {
            log_message('info', "Getting user-based folders for member: {$this->member_id}");

            // ✅ เฉพาะ folder ที่ member เป็นเจ้าของหรือมีสิทธิ์
            $sql = "
            SELECT DISTINCT
                f.folder_id as id,
                f.folder_name as name,
                'folder' as type,
                f.folder_type,
                f.is_shared,
                f.created_at
            FROM tbl_google_drive_folders f
            WHERE (
                f.member_id = ?  -- เป็นเจ้าของ
                OR f.folder_id IN (
                    SELECT folder_id 
                    FROM tbl_google_drive_member_folder_access
                    WHERE member_id = ? 
                      AND is_active = 1
                      AND (expires_at IS NULL OR expires_at > NOW())
                )
            )
            AND f.parent_folder_id IS NULL  -- Root level only
            AND f.is_active = 1
            ORDER BY f.folder_name
        ";

            $query = $this->db->query($sql, [$this->member_id, $this->member_id]);
            $folders = $query->result_array();

            // Format เป็น contents structure
            $contents = [];
            foreach ($folders as $folder) {
                $contents[] = [
                    'id' => $folder['id'],
                    'name' => $folder['name'],
                    'type' => 'folder',
                    'icon' => 'fas fa-folder',
                    'mimeType' => 'application/vnd.google-apps.folder',
                    'real_data' => true,
                    'is_shared' => $folder['is_shared'],
                    'created_at' => $folder['created_at']
                ];
            }

            log_message('info', sprintf(
                "Member %d has %d user-based folders",
                $this->member_id,
                count($contents)
            ));

            return $contents;

        } catch (Exception $e) {
            log_message('error', 'Get user-based folders error: ' . $e->getMessage());
            return [];
        }
    }




    /**
     * ✅ แก้ไข: เช็คสิทธิ์แม้ที่ Root Level
     */
    private function check_folder_access_permission($folder_id)
    {
        try {
            // ✅ เฉพาะ "root" หรือ "google_drive_root" เท่านั้นที่ bypass
            // (หมายถึง root ของ Google Drive ทั้งหมด)
            if ($folder_id === 'root' || $folder_id === 'google_drive_root') {
                return true;
            }

            // ✅ สำหรับ folder อื่นๆ ทั้งหมด (รวม Users, Admin, Departments, Shared)
            // ต้องเช็คสิทธิ์

            log_message('info', "🔐 Checking folder access: member={$this->member_id}, folder={$folder_id}");

            // 1️⃣ เช็ค Direct Permission
            $direct_access = $this->db->select('access_type, permission_source')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $this->member_id)
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->group_start()
                ->where('expires_at IS NULL')
                ->or_where('expires_at >', date('Y-m-d H:i:s'))
                ->group_end()
                ->get()
                ->row();

            if ($direct_access) {
                log_message('info', "✅ Direct access granted: {$direct_access->access_type}");
                return true;
            }

            // 2️⃣ เช็ค Inherited Access
            $inherited_access = $this->check_inherited_folder_access($folder_id);
            if ($inherited_access) {
                log_message('info', "✅ Inherited access granted");
                return true;
            }

            // 3️⃣ เช็ค Position-Based Access
            $position_access = $this->check_position_based_folder_access($folder_id);
            if ($position_access) {
                log_message('info', "✅ Position-based access granted");
                return true;
            }

            // 4️⃣ เช็ค System Admin
            $system_access = $this->check_system_folder_access();
            if ($system_access) {
                log_message('info', "✅ System admin access granted");
                return true;
            }

            // ❌ ไม่มีสิทธิ์
            log_message('info', "❌ Access DENIED for folder: {$folder_id}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Check folder access error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * ✅ ปรับปรุง: เช็คการสืบทอดสิทธิ์จาก Parent Folder
     */
    private function check_inherited_folder_access($folder_id)
    {
        try {
            // ดึง parent folder ID
            $parent_folder_id = $this->get_parent_folder_id($folder_id);

            if (!$parent_folder_id || $parent_folder_id === 'root') {
                log_message('info', "No parent folder or reached root");
                return false;
            }

            log_message('info', "Checking parent folder: {$parent_folder_id}");

            // ✅ เช็คก่อนว่า User มีสิทธิ์เข้า Parent หรือไม่
            $parent_access = $this->db->select('access_type, apply_to_children')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $this->member_id)
                ->where('folder_id', $parent_folder_id)
                ->where('is_active', 1)
                ->where('apply_to_children', 1)  // ✅ ต้องมีการอนุญาตให้สืบทอด
                ->group_start()
                ->where('expires_at IS NULL')
                ->or_where('expires_at >', date('Y-m-d H:i:s'))
                ->group_end()
                ->get()
                ->row();

            if ($parent_access) {
                log_message('info', "✅ Found inheritable permission from parent");

                // ✅ บันทึกสิทธิ์ที่สืบทอดมา (optional)
                $this->record_inherited_access($folder_id, $parent_folder_id, $parent_access->access_type);

                return true;
            }

            // ✅ เช็คต่อไปยัง parent ของ parent (recursive)
            log_message('info', "No inheritable permission, checking parent's parent...");
            return $this->check_inherited_folder_access($parent_folder_id);

        } catch (Exception $e) {
            log_message('error', 'Check inherited folder access error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 👥 ตรวจสอบสิทธิ์จากตำแหน่งงาน
     */
    private function check_position_based_folder_access($folder_id)
    {
        try {
            // ดึงข้อมูล member และ position
            $member_info = $this->db->select('ref_pid')
                ->from('tbl_member')
                ->where('m_id', $this->member_id)
                ->get()
                ->row();

            if (!$member_info) {
                return false;
            }

            // เช็คสิทธิ์จากตำแหน่งใน tbl_google_drive_position_permissions
            $position_permission = $this->db->select('folder_access, can_create_folder, can_share, can_delete')
                ->from('tbl_google_drive_position_permissions')
                ->where('position_id', $member_info->ref_pid)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($position_permission && $position_permission->folder_access) {
                $folder_access_list = json_decode($position_permission->folder_access, true);

                if (is_array($folder_access_list) && in_array($folder_id, $folder_access_list)) {
                    return true;
                }
            }

            // เช็คจาก system folder ที่สร้างสำหรับตำแหน่งนี้
            $system_folder_access = $this->db->select('folder_id')
                ->from('tbl_google_drive_system_folders')
                ->where('created_for_position', $member_info->ref_pid)
                ->where('is_active', 1)
                ->where('folder_id', $folder_id)
                ->get()
                ->row();

            return $system_folder_access ? true : false;

        } catch (Exception $e) {
            log_message('error', 'Check position based folder access error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🛡️ ตรวจสอบสิทธิ์ระบบ (Admin)
     */
    private function check_system_folder_access()
    {
        try {
            $member = $this->db->select('m_system')
                ->from('tbl_member')
                ->where('m_id', $this->member_id)
                ->get()
                ->row();

            if ($member && in_array($member->m_system, ['system_admin', 'super_admin'])) {
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Check system folder access error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📝 บันทึกสิทธิ์ที่สืบทอดมา (ปรับปรุง - เพิ่ม Conflict Handling)
     */
    private function record_inherited_access($folder_id, $parent_folder_id, $access_type)
    {
        try {
            // ✅ Validate input
            if (empty($folder_id) || empty($parent_folder_id) || empty($access_type)) {
                log_message('warning', 'Invalid parameters for record_inherited_access');
                return false;
            }

            // ✅ ตรวจสอบว่ามีการบันทึกแล้วหรือยัง
            $existing = $this->db->select('id, access_type, permission_mode, updated_at')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $this->member_id)
                ->where('folder_id', $folder_id)
                ->get()
                ->row();

            if ($existing) {
                // ✅ มี record อยู่แล้ว - ตรวจสอบว่าควร update หรือไม่

                // ถ้าเป็น 'direct' permission อยู่แล้ว ไม่ต้อง override
                if ($existing->permission_mode === 'direct') {
                    log_message('debug', "Direct permission exists for folder {$folder_id}, skipping inheritance record");
                    return true; // ไม่ถือว่าเป็น error
                }

                // ถ้าเป็น 'inherited' หรือ permission เก่า → อัปเดต
                if ($existing->permission_mode === 'inherited' || $existing->access_type !== $access_type) {
                    $update_data = [
                        'access_type' => $access_type,
                        'permission_mode' => 'inherited',
                        'parent_folder_id' => $parent_folder_id,
                        'inherit_from_parent' => 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->where('id', $existing->id);
                    $this->db->update('tbl_google_drive_member_folder_access', $update_data);

                    log_message('info', "Updated inherited access for folder {$folder_id}: {$access_type}");
                    return true;
                }

                log_message('debug', "Inherited permission already up-to-date for folder {$folder_id}");
                return true;
            }

            // ✅ ไม่มี record → สร้างใหม่
            $inherit_data = [
                'member_id' => $this->member_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'permission_source' => 'position', // หรือ 'inherited' ตามต้องการ
                'permission_mode' => 'inherited',
                'parent_folder_id' => $parent_folder_id,
                'inherit_from_parent' => 1,
                'apply_to_children' => 0, // ไม่สืบทอดต่อโดยอัตโนมัติ
                'is_active' => 1,
                'granted_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_member_folder_access', $inherit_data);

            if ($this->db->affected_rows() > 0) {
                log_message('info', "Recorded inherited access for folder {$folder_id}: {$access_type}");
                return true;
            } else {
                log_message('warning', "Failed to insert inherited access for folder {$folder_id}");
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Record inherited access error: ' . $e->getMessage());
            // ⚠️ ไม่ throw exception เพื่อไม่ให้การทำงานหลักล้มเหลว
            return false;
        }
    }



    /**
     * 🚫 AJAX Response สำหรับไม่มีสิทธิ์เข้าถึง (แค่แจ้งเตือน)
     */
    public function access_denied_response($folder_id)
    {
        try {
            // ดึงข้อมูลโฟลเดอร์สำหรับแสดงใน modal
            $folder_info = $this->get_folder_basic_info($folder_id);

            // ดึงรายชื่อผู้ที่สามารถให้สิทธิ์ได้
            $permission_granters = $this->get_permission_granters($folder_id);

            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error_type' => 'access_denied',
                'message' => 'ไม่มีสิทธิ์เข้าถึงโฟลเดอร์นี้',
                'folder_info' => [
                    'folder_id' => $folder_id,
                    'folder_name' => $folder_info['name'] ?? 'ไม่ทราบชื่อโฟลเดอร์',
                    'folder_path' => $folder_info['path'] ?? '',
                    'folder_type' => $folder_info['type'] ?? 'unknown'
                ],
                'permission_granters' => $permission_granters,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Access denied response error: ' . $e->getMessage());

            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error_type' => 'access_denied',
                'message' => 'ไม่มีสิทธิ์เข้าถึงโฟลเดอร์นี้',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * 📋 ดึงข้อมูลพื้นฐานของโฟลเดอร์
     */
    private function get_folder_basic_info($folder_id)
    {
        try {
            // ดึงจาก system folders ก่อน
            $system_folder = $this->db->select('folder_name, folder_path, folder_type')
                ->from('tbl_google_drive_system_folders')
                ->where('folder_id', $folder_id)
                ->get()
                ->row();

            if ($system_folder) {
                return [
                    'name' => $system_folder->folder_name,
                    'path' => $system_folder->folder_path,
                    'type' => $system_folder->folder_type
                ];
            }

            // ถ้าไม่พบให้ดึงจาก Google Drive API
            $access_token = $this->get_access_token_simple();
            if ($access_token) {
                $folder_info = $this->get_google_drive_folder_info($access_token, $folder_id);
                if ($folder_info) {
                    return [
                        'name' => $folder_info['name'],
                        'path' => $this->build_folder_path($folder_id),
                        'type' => 'user'
                    ];
                }
            }

            return ['name' => 'ไม่ทราบชื่อโฟลเดอร์', 'path' => '', 'type' => 'unknown'];

        } catch (Exception $e) {
            log_message('error', 'Get folder basic info error: ' . $e->getMessage());
            return ['name' => 'ไม่ทราบชื่อโฟลเดอร์', 'path' => '', 'type' => 'unknown'];
        }
    }

    /**
     * 👨‍💼 ดึงรายชื่อผู้ที่สามารถให้สิทธิ์ได้
     */
    private function get_permission_granters($folder_id)
    {
        try {
            // ดึงผู้ที่มีสิทธิ์ admin ในโฟลเดอร์นี้
            $admins = $this->db->select('
                m.m_fname, 
                m.m_lname, 
                m.m_email,
                mfa.access_type
            ')
                ->from('tbl_google_drive_member_folder_access mfa')
                ->join('tbl_member m', 'm.m_id = mfa.member_id')
                ->where('mfa.folder_id', $folder_id)
                ->where('mfa.access_type', 'admin')
                ->where('mfa.is_active', 1)
                ->get()
                ->result();

            $granters = [];
            foreach ($admins as $admin) {
                $granters[] = [
                    'name' => $admin->m_fname . ' ' . $admin->m_lname,
                    'email' => $admin->m_email,
                    'role' => 'โฟลเดอร์ผู้ดูแล'
                ];
            }

            // เพิ่ม system admin
            $system_admins = $this->db->select('m_fname, m_lname, m_email')
                ->from('tbl_member')
                ->where_in('m_system', ['system_admin', 'super_admin'])
                ->where('m_status', '1')
                ->get()
                ->result();

            foreach ($system_admins as $sys_admin) {
                $granters[] = [
                    'name' => $sys_admin->m_fname . ' ' . $sys_admin->m_lname,
                    'email' => $sys_admin->m_email,
                    'role' => 'ผู้ดูแลระบบ'
                ];
            }

            return $granters;

        } catch (Exception $e) {
            log_message('error', 'Get permission granters error: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * 📁 ปรับปรุง get_folder_contents() ให้รวม permission data และส่ง Parent ID
     */
    public function get_folder_contents()
    {
        try {
            // ล้าง output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');

            if (!$this->input->is_ajax_request()) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $settings = $this->get_settings_from_db();
            if (!$settings['google_drive_enabled']) {
                http_response_code(503);
                echo json_encode([
                    'success' => false,
                    'message' => 'Google Drive ถูกปิดใช้งานโดยระบบ',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $folder_id = $this->input->post('folder_id');
            log_message('info', "Getting folder contents for: {$folder_id}");

            // ตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์
            if (!empty($folder_id) && $folder_id !== 'root') {
                if (!$this->check_folder_access_permission($folder_id)) {
                    $this->access_denied_response($folder_id);
                    return;
                }
            }

            // ดึงข้อมูลโฟลเดอร์ (Production เท่านั้น)
            if (empty($folder_id) || $folder_id === 'root') {
                $folder_contents = $this->get_member_folders_as_contents();
            } else {
                $access_token = $this->get_access_token_simple();

                if (!$access_token) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'ไม่สามารถเชื่อมต่อ Google Drive ได้',
                        'timestamp' => date('Y-m-d H:i:s')
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                $folder_contents = $this->get_google_drive_folder_contents($access_token, $folder_id);
            }

            if ($folder_contents !== false && is_array($folder_contents)) {
                // ✅ เพิ่ม permissions และ creator info สำหรับแต่ละ item
                foreach ($folder_contents as &$item) {
                    $item['real_data'] = true;

                    $item_id = $item['id'];

                    // 🔥 [FIX] ส่ง folder_id ปัจจุบันเข้าไปบอกว่าเป็น Parent (ถ้าไม่ใช่ root)
                    // เพื่อบังคับให้ระบบรู้ว่า item นี้เป็นลูกของใคร โดยไม่ต้อง query DB
                    $current_parent_id = (!empty($folder_id) && $folder_id !== 'root') ? $folder_id : null;

                    // ส่ง $current_parent_id ไปช่วยตรวจสอบสิทธิ์แม่
                    $permission_info = $this->get_item_permission_info($item_id, $current_parent_id);

                    $item['access_type'] = $permission_info['access_type'];
                    $item['access_label'] = $permission_info['access_label'];
                    $item['can_edit'] = $permission_info['can_edit'];
                    $item['can_delete'] = $permission_info['can_delete'];
                    $item['can_share'] = $permission_info['can_share'];

                    // ✅ เพิ่ม creator info
                    $item['creator_name'] = $permission_info['creator_name'] ?? 'ไม่ระบุ';
                    $item['uploaded_by'] = $permission_info['creator_name'] ?? 'ไม่ระบุ';
                }

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'ดึงเนื้อหาโฟลเดอร์สำเร็จ',
                    'data' => $folder_contents,
                    'count' => count($folder_contents),
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถดึงเนื้อหาโฟลเดอร์ได้',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            }
            exit;

        } catch (Exception $e) {
            log_message('error', 'Get folder contents exception: ' . $e->getMessage());

            while (ob_get_level()) {
                ob_end_clean();
            }

            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดภายในระบบ',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * 🔐 ดึงข้อมูล Permission สำหรับ Item (Folder/File) - Version 2 with Inheritance
     * ปรับปรุง: รับ $known_parent_id เพื่อตรวจสอบสิทธิ์แม่ทันที
     */
    private function get_item_permission_info($item_id, $known_parent_id = null)
    {
        try {
            log_message('info', "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            log_message('info', "🔐 START: get_item_permission_info for item: {$item_id}");
            log_message('info', "👤 Member ID: {$this->member_id}");

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // LEVEL 1: ตรวจสอบ Direct Permission ของ Item
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            log_message('info', "🔍 LEVEL 1: Checking Direct Permission for item");

            $sql = "
            SELECT 
                mfa.access_type,
                mfa.permission_source,
                mfa.granted_by_name,
                CONCAT(m.m_fname, ' ', m.m_lname) as creator_name
            FROM tbl_google_drive_member_folder_access mfa
            LEFT JOIN tbl_member m ON mfa.member_id = m.m_id
            WHERE mfa.member_id = ?
              AND mfa.folder_id = ?
              AND mfa.is_active = 1
              AND (mfa.expires_at IS NULL OR mfa.expires_at > NOW())
            LIMIT 1
        ";

            $query = $this->db->query($sql, [$this->member_id, $item_id]);
            $access = $query->row();

            if ($access) {
                // ✅ มี Direct Permission
                $access_type = $access->access_type;
                $creator_name = trim($access->creator_name) ?: 'ไม่ระบุ';

                log_message('info', "✅ LEVEL 1 SUCCESS: Direct permission found");
                log_message('info', "   └─ Access Type: {$access_type}");
                log_message('info', "   └─ Creator: {$creator_name}");
                log_message('info', "   └─ Source: Direct Permission (tbl_google_drive_member_folder_access)");
            } else {
                // ❌ ไม่มี Direct Permission → ตรวจสอบ Level 2
                log_message('info', "⚠️  LEVEL 1 FAILED: No direct permission found");

                // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                // LEVEL 2: ตรวจสอบ Parent Folder Permission (Inheritance)
                // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                log_message('info', "🔍 LEVEL 2: Checking Parent Folder Permission (Inheritance)");

                $parent_permission = null;

                // 🔥 [FIX] ใช้ Parent ID ที่ส่งเข้ามาเลย (ไม่ต้อง Query หา Parent)
                if (!empty($known_parent_id)) {
                    log_message('info', "   ℹ️ Using Known Parent ID: {$known_parent_id}");
                    // เช็คสิทธิ์ของโฟลเดอร์แม่โดยตรง
                    $parent_permission = $this->check_specific_folder_permission($known_parent_id);
                }

                // ถ้าไม่มี known_id ค่อยลองหาแบบเดิม (เผื่อกรณีอื่น)
                if (!$parent_permission) {
                    $parent_permission = $this->get_parent_folder_permission($item_id);
                }

                if ($parent_permission) {
                    // ✅ พบ Parent Folder Permission → Inherit
                    $access_type = $parent_permission['access_type'];
                    $creator_name = $this->get_item_creator_from_metadata($item_id);

                    log_message('info', "✅ LEVEL 2 SUCCESS: Inherited from parent folder");
                    log_message('info', "   └─ Parent Folder ID: {$parent_permission['folder_id']}");
                    log_message('info', "   └─ Parent Access Type: {$access_type}");
                    log_message('info', "   └─ Inheritance Rule: Item inherits parent permission");
                    log_message('info', "   └─ Source: Parent Folder Inheritance");
                } else {
                    // ❌ ไม่มี Parent Permission → ตรวจสอบ Level 3
                    log_message('info', "⚠️  LEVEL 2 FAILED: No parent folder permission found");

                    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    // LEVEL 3: ตรวจสอบ Position Permission
                    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    log_message('info', "🔍 LEVEL 3: Checking Position Permission");

                    $position_permission = $this->get_member_position_permission();

                    if ($position_permission) {
                        // ✅ มี Position Permission
                        log_message('info', "✅ LEVEL 3 SUCCESS: Position permission found");
                        log_message('info', "   └─ Position: {$position_permission->position_name}");
                        log_message('info', "   └─ Permission Type: {$position_permission->permission_type}");
                        log_message('info', "   └─ can_create_folder: {$position_permission->can_create_folder}");

                        // Map permission_type to access_type
                        if ($position_permission->permission_type === 'full_admin') {
                            $access_type = 'admin';
                        } else if ($position_permission->permission_type === 'department_admin') {
                            $access_type = 'write';
                        } else if ($position_permission->permission_type === 'position_only') {
                            // ✅ ปรับปรุง: ตรวจสอบ can_create_folder
                            if ($position_permission->can_create_folder == 1) {
                                $access_type = 'write';
                            } else {
                                $access_type = 'read';
                            }
                        } else {
                            $access_type = 'read';
                        }

                        log_message('info', "   └─ Final Access Type: {$access_type}");
                        log_message('info', "   └─ Source: Position Permission");
                    } else {
                        // ❌ ไม่มี Position Permission → ตรวจสอบ Level 4
                        log_message('info', "⚠️  LEVEL 3 FAILED: No position permission found");

                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        // LEVEL 4: ตรวจสอบ m_system Role
                        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                        log_message('info', "🔍 LEVEL 4: Checking m_system Role");

                        $member_sql = "SELECT m_system FROM tbl_member WHERE m_id = ? LIMIT 1";
                        $member_query = $this->db->query($member_sql, [$this->member_id]);
                        $member = $member_query->row();

                        if ($member && ($member->m_system === 'system_admin' || $member->m_system === 'super_admin')) {
                            $access_type = 'admin';
                            log_message('info', "✅ LEVEL 4 SUCCESS: System role found");
                            log_message('info', "   └─ Source: System Role (m_system)");
                        } else {
                            // ❌ ไม่มีสิทธิ์พิเศษ → ใช้ default
                            $access_type = 'read';
                            log_message('info', "⚠️  LEVEL 4 FAILED: No special system role");
                            log_message('info', "   └─ Default Access Type: read");
                        }
                    }

                    // ดึง creator จาก metadata
                    $creator_name = $this->get_item_creator_from_metadata($item_id);
                }
            }

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // สร้าง Permission Result
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

            // แปลง access_type เป็น label ภาษาไทย
            $access_labels = [
                'admin' => 'แอดมิน',
                'owner' => 'เจ้าของ',
                'write' => 'แก้ไข',
                'commenter' => 'แสดงความคิดเห็น',
                'read' => 'อ่านอย่างเดียว'
            ];

            $access_label = $access_labels[$access_type] ?? 'อ่านอย่างเดียว';

            // กำหนด permissions ตาม access_type
            $permissions_map = [
                'admin' => ['can_edit' => true, 'can_delete' => true, 'can_share' => true],
                'owner' => ['can_edit' => true, 'can_delete' => true, 'can_share' => true],
                'write' => ['can_edit' => true, 'can_delete' => false, 'can_share' => false],
                'commenter' => ['can_edit' => false, 'can_delete' => false, 'can_share' => false],
                'read' => ['can_edit' => false, 'can_delete' => false, 'can_share' => false]
            ];

            $perm = $permissions_map[$access_type] ?? $permissions_map['read'];

            $result = [
                'access_type' => $access_type,
                'access_label' => $access_label,
                'can_edit' => $perm['can_edit'],
                'can_delete' => $perm['can_delete'],
                'can_share' => $perm['can_share'],
                'creator_name' => $creator_name
            ];

            return $result;

        } catch (Exception $e) {
            log_message('error', '💥 EXCEPTION in get_item_permission_info: ' . $e->getMessage());
            return [
                'access_type' => 'read',
                'access_label' => 'อ่านอย่างเดียว',
                'can_edit' => false,
                'can_delete' => false,
                'can_share' => false,
                'creator_name' => 'ไม่ระบุ'
            ];
        }
    }

    /**
     * 🛠️ Helper Function: เช็คสิทธิ์ของโฟลเดอร์ที่ระบุ (ใช้สำหรับ Known Parent)
     * ฟังก์ชันใหม่
     */
    private function check_specific_folder_permission($folder_id)
    {
        $sql = "
            SELECT folder_id, access_type
            FROM tbl_google_drive_member_folder_access
            WHERE member_id = ?
              AND folder_id = ?
              AND is_active = 1
              AND (expires_at IS NULL OR expires_at > NOW())
            LIMIT 1
        ";
        $query = $this->db->query($sql, [$this->member_id, $folder_id]);
        $row = $query->row();

        return $row ? ['folder_id' => $row->folder_id, 'access_type' => $row->access_type] : null;
    }

    /**
     * 🔗 ดึงข้อมูล Permission ของ Parent Folder (สำหรับ Inheritance)
     * 
     * @param string $item_id Google Drive ID ของ Item
     * @return array|null ['folder_id' => ..., 'access_type' => ...] หรือ null ถ้าไม่มี
     */
    private function get_parent_folder_permission($item_id)
    {
        try {
            log_message('info', "   🔍 Searching for parent folder of item: {$item_id}");

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // หาว่า Item นี้อยู่ในโฟลเดอร์ไหน
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

            // ตรวจสอบว่าเป็นโฟลเดอร์ใน tbl_google_drive_folders หรือไม่
            $folder_sql = "
            SELECT parent_folder_id
            FROM tbl_google_drive_folders
            WHERE folder_id = ?
              AND is_active = 1
            LIMIT 1
        ";

            $folder_query = $this->db->query($folder_sql, [$item_id]);
            $folder = $folder_query->row();

            $parent_folder_id = null;

            if ($folder && !empty($folder->parent_folder_id)) {
                $parent_folder_id = $folder->parent_folder_id;
                log_message('info', "   └─ Found parent from tbl_google_drive_folders: {$parent_folder_id}");
            } else {
                // ถ้าไม่ได้อยู่ใน tbl_google_drive_folders ลองดูใน tbl_google_drive_system_files
                $file_sql = "
                SELECT parent_folder_id
                FROM tbl_google_drive_system_files
                WHERE file_id = ?
                  AND is_active = 1
                LIMIT 1
            ";

                $file_query = $this->db->query($file_sql, [$item_id]);
                $file = $file_query->row();

                if ($file && !empty($file->parent_folder_id)) {
                    $parent_folder_id = $file->parent_folder_id;
                    log_message('info', "   └─ Found parent from tbl_google_drive_system_files: {$parent_folder_id}");
                }
            }

            // ถ้าไม่พบ parent_folder_id
            if (empty($parent_folder_id)) {
                log_message('info', "   └─ No parent folder found (item may be at root level)");
                return null;
            }

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // ตรวจสอบ Permission ของ Parent Folder
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

            log_message('info', "   🔍 Checking permission for parent folder: {$parent_folder_id}");

            $permission_sql = "
            SELECT 
                mfa.folder_id,
                mfa.access_type
            FROM tbl_google_drive_member_folder_access mfa
            WHERE mfa.member_id = ?
              AND mfa.folder_id = ?
              AND mfa.is_active = 1
              AND (mfa.expires_at IS NULL OR mfa.expires_at > NOW())
            LIMIT 1
        ";

            $permission_query = $this->db->query($permission_sql, [$this->member_id, $parent_folder_id]);
            $permission = $permission_query->row();

            if ($permission) {
                log_message('info', "   ✅ Parent folder permission found:");
                log_message('info', "      └─ Folder ID: {$permission->folder_id}");
                log_message('info', "      └─ Access Type: {$permission->access_type}");

                return [
                    'folder_id' => $permission->folder_id,
                    'access_type' => $permission->access_type
                ];
            } else {
                log_message('info', "   └─ No permission found for parent folder");
                return null;
            }

        } catch (Exception $e) {
            log_message('error', '💥 EXCEPTION in get_parent_folder_permission:');
            log_message('error', "   └─ Message: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * 👤 ดึงชื่อ creator จาก metadata
     */
    private function get_item_creator_from_metadata($item_id)
    {
        try {
            // ✅ ลองหาจาก tbl_google_drive_folders
            $sql = "
            SELECT 
                f.created_by,
                CONCAT(m.m_fname, ' ', m.m_lname) as creator_name
            FROM tbl_google_drive_folders f
            LEFT JOIN tbl_member m ON f.created_by = m.m_id
            WHERE f.folder_id = ?
            LIMIT 1
        ";

            $query = $this->db->query($sql, [$item_id]);
            $folder = $query->row();

            if ($folder && $folder->creator_name) {
                log_message('info', "   📁 Found folder creator: {$folder->creator_name}");
                return trim($folder->creator_name);
            }

            // ✅ ลองหาจาก tbl_google_drive_system_files
            $sql = "
            SELECT 
                f.uploaded_by,
                CONCAT(m.m_fname, ' ', m.m_lname) as creator_name
            FROM tbl_google_drive_system_files f
            LEFT JOIN tbl_member m ON f.uploaded_by = m.m_id
            WHERE f.file_id = ?
            LIMIT 1
        ";

            $query = $this->db->query($sql, [$item_id]);
            $file = $query->row();

            if ($file && $file->creator_name) {
                log_message('info', "   📄 Found file creator: {$file->creator_name}");
                return trim($file->creator_name);
            }

            log_message('info', "   └─ No creator found for item");
            return 'ไม่ระบุ';

        } catch (Exception $e) {
            log_message('error', '💥 Get item creator from metadata error: ' . $e->getMessage());
            return 'ไม่ระบุ';
        }
    }

    /**
     * 👤 ดึงข้อมูล Position Permission ของ Member
     */
    private function get_member_position_permission()
    {
        try {
            $sql = "
            SELECT 
                pp.permission_type,
                pp.can_create_folder,
                pp.can_share,
                pp.can_delete,
                pp.folder_access,
                p.pname as position_name,
                m.ref_pid as position_id
            FROM tbl_member m
            INNER JOIN tbl_google_drive_position_permissions pp ON m.ref_pid = pp.position_id
            LEFT JOIN tbl_position p ON pp.position_id = p.pid
            WHERE m.m_id = ?
              AND pp.is_active = 1
            LIMIT 1
        ";

            $query = $this->db->query($sql, [$this->member_id]);
            $result = $query->row();

            return $result;

        } catch (Exception $e) {
            log_message('error', '💥 Get member position permission error: ' . $e->getMessage());
            return null;
        }
    }




    /**
     * 🔍 ดึง Breadcrumbs สำหรับโฟลเดอร์ (AJAX) - ✅ Fixed with real API
     */
    public function get_folder_breadcrumbs()
    {
        try {
            // ล้าง output buffer ก่อนเริ่มทำงาน
            $this->clear_output_buffer();

            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                $this->safe_json_error('Invalid request method', 400);
                return;
            }

            $folder_id = $this->input->post('folder_id');
            if (!$folder_id || $folder_id === 'root') {
                $this->safe_json_success([], 'ดึง breadcrumbs สำเร็จ');
                return;
            }

            // สำหรับ trial mode
            if ($this->is_trial_mode) {
                $breadcrumbs = $this->get_trial_breadcrumbs($folder_id);
            } else {
                // ดึงจาก Google Drive API จริง
                $breadcrumbs = $this->get_real_breadcrumbs($folder_id);
            }

            $this->safe_json_success($breadcrumbs, 'ดึง breadcrumbs สำเร็จ');

        } catch (Exception $e) {
            log_message('error', 'Get folder breadcrumbs error: ' . $e->getMessage());
            $this->safe_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }

    /**
     * 🔍 ดึง Real Breadcrumbs จาก Google Drive API
     */
    private function get_real_breadcrumbs($folder_id)
    {
        try {
            if ($this->storage_mode === 'centralized') {
                $system_storage = $this->get_active_system_storage();
                if (!$system_storage) {
                    return [];
                }

                $token_data = json_decode($system_storage->google_access_token, true);
                $access_token = $token_data['access_token'];
                $root_folder_id = $system_storage->root_folder_id;
            } else {
                $member = $this->db->select('google_access_token')
                    ->from('tbl_member')
                    ->where('m_id', $this->member_id)
                    ->get()
                    ->row();

                if (!$member) {
                    return [];
                }

                $token_data = json_decode($member->google_access_token, true);
                $access_token = $token_data['access_token'] ?? null;
                $root_folder_id = 'root';
            }

            return $this->build_breadcrumbs($access_token, $folder_id, $root_folder_id);

        } catch (Exception $e) {
            log_message('error', 'Get real breadcrumbs error: ' . $e->getMessage());
            return [];
        }
    }

    // ========================================
    // Google Drive API Functions
    // ========================================

    /**
     * ดึงโฟลเดอร์หลักจาก Google Drive
     */
    private function get_google_drive_root_folders($access_token, $root_folder_id)
    {
        try {
            log_message('info', '========================================');
            log_message('info', '📂 START: get_google_drive_root_folders()');
            log_message('info', '========================================');
            log_message('info', "📁 Root Folder ID: {$root_folder_id}");
            log_message('info', "🔑 Access Token Length: " . strlen($access_token));

            $ch = curl_init();

            // ดึงโฟลเดอร์ย่อยจาก root folder
            $query = "'{$root_folder_id}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            $fields = 'files(id,name,mimeType,modifiedTime,parents,webViewLink,iconLink)';
            $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
                'q' => $query,
                'fields' => $fields,
                'orderBy' => 'name'
            ]);

            log_message('info', '🔍 Query: ' . $query);
            log_message('info', '📋 Fields: ' . $fields);
            log_message('info', '🔗 API URL: ' . $url);

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            log_message('info', '📡 Sending cURL request to Google Drive API...');

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            log_message('info', '📊 HTTP Response Code: ' . $http_code);

            if ($error) {
                log_message('info', '❌ cURL Error detected: ' . $error);
            } else {
                log_message('info', '✅ cURL executed without errors');
            }

            curl_close($ch);

            if ($error) {
                log_message('error', 'cURL Error in get_google_drive_root_folders: ' . $error);
                log_message('info', '========================================');
                log_message('info', '❌ END: get_google_drive_root_folders() - cURL Error');
                log_message('info', '========================================');
                return false;
            }

            if ($http_code === 200) {
                log_message('info', '✅ HTTP 200 - Success response');
                log_message('info', '📄 Response Length: ' . strlen($response) . ' bytes');
                log_message('info', '📄 Response Preview: ' . substr($response, 0, 200) . '...');

                $data = json_decode($response, true);

                if ($data && isset($data['files'])) {
                    log_message('info', '✅ JSON decoded successfully');
                    log_message('info', '📦 Files array exists in response');
                    log_message('info', '📊 Total files in response: ' . count($data['files']));

                    $folders = [];
                    foreach ($data['files'] as $index => $file) {
                        log_message('info', "  ├─ Processing file [{$index}]: {$file['name']}");
                        log_message('info', "  │   ├─ ID: {$file['id']}");
                        log_message('info', "  │   ├─ MimeType: {$file['mimeType']}");
                        log_message('info', "  │   ├─ Modified: {$file['modifiedTime']}");

                        $folder_data = [
                            'id' => $file['id'],
                            'name' => $file['name'],
                            'type' => 'folder',
                            'icon' => $this->get_folder_icon($file['name']),
                            'modified' => $this->format_google_date($file['modifiedTime']),
                            'size' => '-',
                            'description' => $this->get_folder_description($file['name']),
                            'webViewLink' => $file['webViewLink'] ?? null,
                            'real_data' => true
                        ];

                        log_message('info', "  │   ├─ Icon: {$folder_data['icon']}");
                        log_message('info', "  │   ├─ Description: {$folder_data['description']}");
                        log_message('info', "  │   └─ WebViewLink: " . ($folder_data['webViewLink'] ?? 'null'));

                        $folders[] = $folder_data;
                    }

                    log_message('info', '✅ Successfully processed all folders');
                    log_message('info', '📦 Total folders prepared: ' . count($folders));
                    log_message('info', 'Successfully retrieved ' . count($folders) . ' folders from Google Drive root');

                    if (count($folders) > 0) {
                        log_message('info', '📋 Folder list:');
                        foreach ($folders as $folder) {
                            log_message('info', "  ├─ {$folder['name']} (ID: {$folder['id']}, Type: {$folder['type']})");
                        }

                        log_message('info', '📋 First folder structure:');
                        log_message('info', json_encode($folders[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    } else {
                        log_message('info', '⚠️ No folders found in root folder');
                    }

                    log_message('info', '========================================');
                    log_message('info', '✅ END: get_google_drive_root_folders() - SUCCESS');
                    log_message('info', '========================================');

                    return $folders;
                } else {
                    log_message('error', '❌ Invalid JSON structure or missing files array');
                    log_message('error', '📄 Response: ' . $response);
                    log_message('info', '========================================');
                    log_message('info', '❌ END: get_google_drive_root_folders() - Invalid Response');
                    log_message('info', '========================================');
                }
            } else {
                log_message('error', "Google Drive API error in root folders: HTTP {$http_code} - {$response}");
                log_message('error', '❌ HTTP Code: ' . $http_code);
                log_message('error', '📄 Full Response: ' . $response);
                log_message('info', '========================================');
                log_message('info', '❌ END: get_google_drive_root_folders() - HTTP Error');
                log_message('info', '========================================');
            }

            return false;

        } catch (Exception $e) {
            log_message('error', '========================================');
            log_message('error', '💥 EXCEPTION in get_google_drive_root_folders()');
            log_message('error', '========================================');
            log_message('error', 'Get Google Drive root folders error: ' . $e->getMessage());
            log_message('error', '📍 File: ' . $e->getFile());
            log_message('error', '📍 Line: ' . $e->getLine());
            log_message('error', '📚 Trace: ' . $e->getTraceAsString());
            log_message('info', '========================================');
            log_message('info', '❌ END: get_google_drive_root_folders() - EXCEPTION');
            log_message('info', '========================================');
            return false;
        }
    }

    private function get_google_drive_folder_contents($access_token, $folder_id)
    {
        try {
            log_message('info', "Getting folder contents from Google Drive: {$folder_id}");

            $ch = curl_init();

            // Query สำหรับดึงข้อมูลจาก Google Drive
            $query = "'{$folder_id}' in parents and trashed=false";
            $fields = 'files(id,name,mimeType,size,modifiedTime,parents,webViewLink,iconLink,owners)';

            $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
                'q' => $query,
                'fields' => $fields,
                'orderBy' => 'folder,name',
                'pageSize' => 1000
            ]);

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                log_message('error', 'cURL Error: ' . $error);
                return false;
            }

            if ($http_code === 200) {
                $data = json_decode($response, true);

                if ($data && isset($data['files'])) {
                    $items = [];

                    foreach ($data['files'] as $file) {
                        $is_folder = ($file['mimeType'] === 'application/vnd.google-apps.folder');

                        // ดึงข้อมูลผู้สร้าง/เจิ้าของจาก Google Drive
                        $creator_name = 'ไม่ระบุ';
                        if (isset($file['owners']) && !empty($file['owners'])) {
                            $owner = $file['owners'][0];
                            $creator_name = $owner['displayName'] ?? $owner['emailAddress'] ?? 'ไม่ระบุ';
                        }

                        // ตรวจสอบข้อมูลในฐานข้อมูลท้องถิ่น
                        $local_creator = $this->get_local_item_creator($file['id'], $is_folder ? 'folder' : 'file');
                        if (!empty($local_creator)) {
                            $creator_name = $local_creator;
                        }

                        $items[] = [
                            'id' => $file['id'],
                            'name' => $file['name'],
                            'type' => $is_folder ? 'folder' : 'file',
                            'icon' => $is_folder ?
                                $this->get_folder_icon($file['name']) :
                                $this->get_file_icon($file['mimeType']),
                            'modified' => $this->format_google_date($file['modifiedTime']),
                            'size' => $is_folder ? '-' : $this->format_file_size($file['size'] ?? 0),
                            'creator_name' => $creator_name,  // ← เพิ่มข้อมูลนี้
                            'webViewLink' => $file['webViewLink'] ?? null,
                            'real_data' => true
                        ];
                    }

                    return $items;
                }
            }

            log_message('error', "Google Drive API error: HTTP {$http_code}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Get Google Drive folder contents error: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * 🔍 ดึงข้อมูลผู้สร้างจากฐานข้อมูลท้องถิ่น
     */
    private function get_local_item_creator($item_id, $item_type)
    {
        try {
            if ($item_type === 'folder') {
                // ดึงข้อมูลจากตาราง tbl_google_drive_system_folders เท่านั้น
                $query = $this->db->select('f.created_by, m.m_fname, m.m_lname')
                    ->from('tbl_google_drive_system_folders f')
                    ->join('tbl_member m', 'f.created_by = m.m_id', 'left')
                    ->where('f.folder_id', $item_id)
                    ->limit(1)
                    ->get();
            } else {
                // ดึงข้อมูลจากตาราง tbl_google_drive_system_files เท่านั้น
                $query = $this->db->select('f.uploaded_by, m.m_fname, m.m_lname')
                    ->from('tbl_google_drive_system_files f')
                    ->join('tbl_member m', 'f.uploaded_by = m.m_id', 'left')
                    ->where('f.file_id', $item_id)
                    ->limit(1)
                    ->get();
            }

            $result = $query->row();

            if ($result && !empty($result->m_fname)) {
                return trim($result->m_fname . ' ' . $result->m_lname);
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Get local item creator error: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * 📂 แก้ไข method loadContents - เพิ่มข้อมูลผู้สร้าง
     */
    public function loadContents()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $folder_id = $this->input->post('folder_id') ?: 'root';

            log_message('info', "Loading contents for folder: {$folder_id}");

            // ตรวจสอบการเข้าถึง
            if (!$this->check_folder_access($folder_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึงโฟลเดอร์นี้'
                ]);
                return;
            }

            // ดึงข้อมูลจาก Google Drive
            $access_token = $this->get_valid_access_token();

            if (!$access_token) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเชื่อมต่อ Google Drive ได้'
                ]);
                return;
            }

            // ใช้ method ใหม่ที่มีข้อมูลผู้สร้าง
            $folder_contents = $this->get_google_drive_folder_contents($access_token, $folder_id);

            if ($folder_contents !== false && is_array($folder_contents)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'ดึงเนื้อหาโฟลเดอร์สำเร็จ',
                    'data' => $folder_contents,
                    'count' => count($folder_contents),
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถดึงเนื้อหาโฟลเดอร์ได้',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', 'Load contents error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
            ]);
        }
    }



    /**
     * สร้าง Breadcrumbs จาก Google Drive
     */
    private function build_breadcrumbs($access_token, $folder_id, $root_folder_id)
    {
        try {
            $breadcrumbs = [];
            $current_folder_id = $folder_id;

            while ($current_folder_id && $current_folder_id !== $root_folder_id && $current_folder_id !== 'root') {
                $folder_info = $this->get_google_drive_folder_info($access_token, $current_folder_id);

                if (!$folder_info) {
                    break;
                }

                array_unshift($breadcrumbs, [
                    'id' => $folder_info['id'],
                    'name' => $folder_info['name']
                ]);

                if (isset($folder_info['parents']) && !empty($folder_info['parents'])) {
                    $current_folder_id = $folder_info['parents'][0];
                } else {
                    break;
                }
            }

            return $breadcrumbs;

        } catch (Exception $e) {
            log_message('error', 'Build breadcrumbs error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 🔍 ดึงข้อมูล Google Drive Folder (Helper Function)
     */
    private function get_google_drive_folder_info($access_token, $folder_id)
    {
        try {
            $url = "https://www.googleapis.com/drive/v3/files/{$folder_id}?fields=id,name,parents";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $data = json_decode($response, true);
                return $data;
            } else {
                log_message('debug', "Google Drive API returned HTTP {$http_code} for folder {$folder_id}");
                return null;
            }

        } catch (Exception $e) {
            log_message('error', 'Get Google Drive folder info error: ' . $e->getMessage());
            return null;
        }
    }

    // ========================================
    // System Storage Functions  
    // ========================================

    /**
     * ดึง Active System Storage
     */
    private function get_active_system_storage()
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                return null;
            }

            return $this->db->select('*')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->get()
                ->row();

        } catch (Exception $e) {
            log_message('error', 'Get active system storage error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ตรวจสอบ Valid Access Token
     */
    private function has_valid_access_token($system_storage)
    {
        try {
            if (!$system_storage || !$system_storage->google_access_token) {
                return false;
            }

            $token_data = json_decode($system_storage->google_access_token, true);

            if (!$token_data || !isset($token_data['expires_at'])) {
                return false;
            }

            // ตรวจสอบว่า token หมดอายุหรือยัง (เผื่อ 5 นาที)
            $expires_at = $token_data['expires_at'];
            $current_time = time();

            return ($expires_at - 300) > $current_time;

        } catch (Exception $e) {
            log_message('error', 'Check valid access token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh System Access Token
     */
    private function refresh_system_access_token($system_storage)
    {
        try {
            if (!$system_storage || !$system_storage->google_refresh_token) {
                return false;
            }

            $token_data = json_decode($system_storage->google_access_token, true);
            $refresh_token = $system_storage->google_refresh_token;

            // ดึงการตั้งค่า OAuth
            $google_settings = $this->get_google_oauth_settings();
            if (!$google_settings) {
                return false;
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://oauth2.googleapis.com/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'client_id' => $google_settings['client_id'],
                    'client_secret' => $google_settings['client_secret'],
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token'
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $new_token_data = json_decode($response, true);

                if ($new_token_data && isset($new_token_data['access_token'])) {
                    // อัปเดต token ข้อมูล
                    $updated_token = [
                        'access_token' => $new_token_data['access_token'],
                        'token_type' => $new_token_data['token_type'] ?? 'Bearer',
                        'expires_in' => $new_token_data['expires_in'] ?? 3600,
                        'expires_at' => time() + ($new_token_data['expires_in'] ?? 3600),
                        'scope' => $token_data['scope'] ?? ''
                    ];

                    // บันทึกลงฐานข้อมูล
                    $this->db->where('id', $system_storage->id)
                        ->update('tbl_google_drive_system_storage', [
                            'google_access_token' => json_encode($updated_token),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    log_message('info', 'System access token refreshed successfully');
                    return true;
                }
            }

            log_message('error', 'Failed to refresh system access token: ' . $response);
            return false;

        } catch (Exception $e) {
            log_message('error', 'Refresh system access token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงการตั้งค่า Google OAuth
     */
    private function get_google_oauth_settings()
    {
        try {
            $settings = [];

            if ($this->db->table_exists('tbl_google_drive_settings')) {
                $setting_rows = $this->db->where_in('setting_key', ['google_client_id', 'google_client_secret'])
                    ->get('tbl_google_drive_settings')
                    ->result();

                foreach ($setting_rows as $row) {
                    if ($row->setting_key === 'google_client_id') {
                        $settings['client_id'] = $row->setting_value;
                    } elseif ($row->setting_key === 'google_client_secret') {
                        $settings['client_secret'] = $row->setting_value;
                    }
                }
            }

            return (isset($settings['client_id']) && isset($settings['client_secret'])) ? $settings : null;

        } catch (Exception $e) {
            log_message('error', 'Get Google OAuth settings error: ' . $e->getMessage());
            return null;
        }
    }

    // ========================================
    // Trial & Demo Functions
    // ========================================

    /**
     * ✨ แปลงโฟลเดอร์เป็น Contents สำหรับ Root Level
     */
    private function get_member_folders_as_contents()
    {
        try {
            if ($this->storage_mode === 'centralized') {
                $folders = $this->get_centralized_folders();
            } else {
                $folders = $this->get_user_based_folders();
            }

            return $folders;

        } catch (Exception $e) {
            log_message('error', 'Get member folders as contents error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 🎭 ดึง Trial Breadcrumbs
     */
    private function get_trial_breadcrumbs($folder_id)
    {
        // Mock breadcrumbs สำหรับ trial mode
        $mock_breadcrumbs = [
            'demo_folder_1' => [
                ['id' => 'demo_folder_1', 'name' => 'Documents']
            ],
            'demo_folder_2' => [
                ['id' => 'demo_folder_2', 'name' => 'Projects']
            ],
            'demo_folder_3' => [
                ['id' => 'demo_folder_2', 'name' => 'Projects'],
                ['id' => 'demo_folder_3', 'name' => 'Web Development']
            ]
        ];

        return $mock_breadcrumbs[$folder_id] ?? [];
    }

    /**
     * Get Trial Demo Folders
     */
    private function getTrialDemoFolders()
    {
        return [
            [
                'id' => 'demo_folder_1',
                'name' => 'Documents (Demo)',
                'type' => 'folder',
                'icon' => 'fas fa-folder text-blue-500',
                'modified' => $this->format_datetime(date('Y-m-d H:i:s')),
                'size' => '-',
                'description' => 'ตัวอย่างโฟลเดอร์เอกสาร',
                'folder_type' => 'trial',
                'permission_level' => 'trial',
                'real_data' => false,
                'webViewLink' => '#trial-mode'
            ],
            [
                'id' => 'demo_folder_2',
                'name' => 'Projects (Demo)',
                'type' => 'folder',
                'icon' => 'fas fa-folder text-purple-500',
                'modified' => $this->format_datetime(date('Y-m-d H:i:s', strtotime('-1 day'))),
                'size' => '-',
                'description' => 'ตัวอย่างโฟลเดอร์โปรเจกต์',
                'folder_type' => 'trial',
                'permission_level' => 'trial',
                'real_data' => false,
                'webViewLink' => '#trial-mode'
            ]
        ];
    }

    // ========================================
    // File Upload Functions
    // ========================================



    /**
     * 🛠️ Custom Error Handler
     */
    public function custom_error_handler($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $error_msg = "PHP Error: {$message} in {$file} on line {$line}";
        log_message('error', $error_msg);

        // สำหรับ AJAX requests ให้ส่ง JSON error
        if ($this->input->is_ajax_request()) {
            $this->safe_json_error('เกิดข้อผิดพลาดภายในระบบ', 500, [
                'error_details' => ENVIRONMENT === 'development' ? $error_msg : 'Internal error'
            ]);
        }

        return true;
    }

    /**
     * 🛠️ Custom Exception Handler
     */
    public function custom_exception_handler($exception)
    {
        $error_msg = "Uncaught Exception: " . $exception->getMessage() .
            " in " . $exception->getFile() . " on line " . $exception->getLine();

        log_message('error', $error_msg);

        if ($this->input->is_ajax_request()) {
            $this->safe_json_error('เกิดข้อผิดพลาดภายในระบบ', 500, [
                'exception' => ENVIRONMENT === 'development' ? $error_msg : 'Internal exception'
            ]);
        }
    }



    /**
     * 📁 อัปโหลดไฟล์ (ปรับปรุงความปลอดภัย: API Fallback Permission Check + Member Quota Check)
     * ✅ อัปเดต: เพิ่มการบันทึก Member Storage Usage
     * ✅ อัปเดต: เพิ่มการตรวจสอบ Member Quota ก่อนอัปโหลด (CRITICAL FIX)
     */
    public function upload_file()
    {
        try {
            // ล้าง output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            // ตั้งค่า header
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');

            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงการตั้งค่าจากฐานข้อมูล
            $settings = $this->get_settings_from_db();

            // ตรวจสอบว่า Google Drive เปิดใช้งานหรือไม่
            if (!$settings['google_drive_enabled']) {
                http_response_code(503);
                echo json_encode([
                    'success' => false,
                    'message' => 'Google Drive ถูกปิดใช้งานโดยระบบ',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบไฟล์
            if (empty($_FILES['file']['name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบไฟล์ที่จะอัปโหลด',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $folder_id = $this->input->post('folder_id') ?: null;
            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            log_message('info', sprintf(
                '📤 Upload request: file=%s, size=%d bytes (%.2f MB), type=.%s, folder=%s, member=%d',
                $file_name,
                $file_size,
                $file_size / 1024 / 1024,
                $file_extension,
                $folder_id ?: 'root',
                $this->member_id
            ));

            // ============================================
            // 🔥 [REFACTORED] Pre-Upload Validation
            // ตรวจสอบ: File Type, File Size, System Storage, Member Quota
            // ============================================
            log_message('info', '🔍 Starting pre-upload validation...');

            $validation = $this->pre_upload_validation($file_size, $file_extension, $settings);

            if (!$validation['allowed']) {
                http_response_code($validation['http_code']);
                echo json_encode($validation['response'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            log_message('info', '✅ Pre-upload validations passed (file type, size, system storage, member quota)');

            // ============================================
            // 🔐 Access Token & Permission Check
            // ============================================

            // ดึง Access Token ก่อนเพื่อใช้ในการตรวจสอบสิทธิ์ (API Fallback)
            $access_token = $this->get_access_token_simple();
            if (!$access_token) {
                log_message('info', '❌ Upload failed: Cannot get access token');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเชื่อมต่อ Google Drive ได้ (Token Error)',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ✅ ตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์ (Security Logic)
            if (!empty($folder_id) && $folder_id !== 'root') {

                // 1. พยายามหา Parent จาก Local DB ก่อน (เร็ว)
                $parent_id = $this->get_local_parent_id($folder_id);

                // 2. ถ้าไม่เจอใน Local ให้ดึงจาก Google Drive API (ชัวร์กว่า)
                if (!$parent_id) {
                    log_message('info', "⚠️ Local parent not found for {$folder_id}. Fetching from API...");
                    $parent_id = $this->fetch_parent_id_from_api($folder_id, $access_token);
                }

                // 3. ตรวจสอบสิทธิ์โดยส่ง Parent ID ที่หาได้เข้าไปด้วย
                $permission_info = $this->get_item_permission_info($folder_id, $parent_id);

                if (!$permission_info['can_edit']) {
                    log_message('error', "⛔ Upload blocked: No write permission for folder {$folder_id}");
                    $this->access_denied_response($folder_id);
                    return;
                }

                log_message('info', '✅ Folder permission check passed');
            }

            // ============================================
            // 🌐 Upload to Google Drive
            // ============================================

            log_message('info', '🌐 Uploading to Google Drive...');
            $upload_result = $this->upload_to_google_drive_simple($_FILES['file'], $folder_id, $access_token);

            if ($upload_result && $upload_result['success']) {
                log_message('info', sprintf(
                    '✅ Uploaded to Google Drive: file_id=%s',
                    $upload_result['file_id']
                ));

                // ============================================
                // 💾 Save File Info & Update Storage
                // ============================================

                // บันทึกข้อมูลไฟล์
                log_message('info', '💾 Saving file info to database...');
                $file_record_id = $this->save_file_info_simple(
                    $upload_result['file_id'],
                    $file_name,
                    $file_size,
                    $folder_id
                );

                log_message('info', sprintf(
                    '✅ File saved to database: record_id=%d',
                    $file_record_id
                ));

                // ✅ อัปเดตการใช้งาน storage ระบบ
                if ($this->storage_mode === 'centralized') {
                    log_message('info', '🔄 Increasing system storage usage...');
                    $result = $this->increase_system_storage_usage($file_size);

                    if ($result) {
                        log_message('info', '✅ System storage increased successfully');
                    } else {
                        log_message('error', '❌ System storage increase failed');
                    }
                }

                // ✅ อัปเดตการใช้งาน storage ของ Member
                // ✅ เพิ่ม Log แจ้งว่า Update ไปแล้ว
                log_message('info', 'ℹ️ [Member Storage] Already updated in save_file_info_simple()');

                // ============================================
                // 📝 Log Activity
                // ============================================

                $this->log_drive_activity('upload_file', [
                    'file_id' => $upload_result['file_id'],
                    'file_name' => $file_name,
                    'file_size' => $file_size,
                    'folder_id' => $folder_id,
                    'record_id' => $file_record_id,
                    'member_id' => $this->member_id
                ]);

                log_message('info', sprintf(
                    '🎉 Upload completed successfully: file=%s, size=%.2f MB, member=%d',
                    $file_name,
                    $file_size / 1024 / 1024,
                    $this->member_id
                ));

                // ============================================
                // ✅ Success Response
                // ============================================

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'อัปโหลดไฟล์สำเร็จ',
                    'data' => [
                        'file_id' => $upload_result['file_id'],
                        'file_name' => $file_name,
                        'file_size' => $file_size,
                        'file_size_mb' => round($file_size / (1024 * 1024), 2),
                        'web_view_link' => $upload_result['web_view_link'],
                        'folder_id' => $folder_id,
                        'record_id' => $file_record_id
                    ],
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            } else {
                $error_message = isset($upload_result['error']) ? $upload_result['error'] : 'ไม่สามารถอัปโหลดไฟล์ได้';

                log_message('info', sprintf(
                    '❌ Upload to Google Drive failed: %s',
                    $error_message
                ));

                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $error_message,
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            }
            exit;

        } catch (Exception $e) {
            // Log error
            if (function_exists('log_message')) {
                log_message('error', '💥 Upload file exception: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            }

            while (ob_get_level()) {
                ob_end_clean();
            }

            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดภายในระบบ',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    private function get_local_parent_id($item_id)
    {
        if ($this->storage_mode === 'centralized') {
            // Centralized Mode: ค้นหา system tables

            // 1. ค้นหาใน system_folders
            $folder = $this->db->select('parent_folder_id')
                ->from('tbl_google_drive_system_folders')
                ->where('folder_id', $item_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($folder)
                return $folder->parent_folder_id;

            // 2. ค้นหาใน system_files
            $file = $this->db->select('parent_folder_id')
                ->from('tbl_google_drive_system_files')
                ->where('file_id', $item_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($file)
                return $file->parent_folder_id;

        } else {
            // User-based Mode: ค้นหา user tables

            // 1. ค้นหาใน folders
            $folder = $this->db->select('parent_folder_id')
                ->from('tbl_google_drive_folders')
                ->where('folder_id', $item_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($folder)
                return $folder->parent_folder_id;

            // 2. ค้นหาใน files
            $file = $this->db->select('parent_folder_id')
                ->from('tbl_google_drive_files')
                ->where('file_id', $item_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($file)
                return $file->parent_folder_id;
        }

        return null;
    }

    /**
     * 🌐 Helper: ค้นหา Parent ID จาก Google Drive API
     * (ใช้เมื่อ Local DB ไม่สมบูรณ์ เพื่อความปลอดภัย)
     */
    private function fetch_parent_id_from_api($file_id, $access_token)
    {
        try {
            $url = "https://www.googleapis.com/drive/v3/files/{$file_id}?fields=parents";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$access_token}",
                "Content-Type: application/json"
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $data = json_decode($response, true);
                if (!empty($data['parents']) && is_array($data['parents'])) {
                    log_message('info', "✅ API Parent Check: Found parent for {$file_id} -> {$data['parents'][0]}");
                    return $data['parents'][0];
                }
            }

            log_message('error', "❌ API Parent Check Failed for {$file_id}: HTTP {$http_code}");
            return null;

        } catch (Exception $e) {
            log_message('error', "API Parent Check Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ ตรวจสอบพื้นที่เก็บข้อมูลภาพรวมของระบบ
     * 🔥 Trial Mode: จำกัด 5GB | Production Mode: ใช้ค่าจาก DB
     */
    private function check_system_storage_limit($file_size, $settings)
    {
        try {
            $system_storage = $this->db->select('total_storage_used, max_storage_limit')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->get()
                ->row();

            if (!$system_storage) {
                return [
                    'allowed' => false,
                    'message' => 'ไม่พบข้อมูลการตั้งค่า storage ของระบบ'
                ];
            }

            $current_usage = $system_storage->total_storage_used;

            // 🔥 ใช้ limit ตาม mode
            $storage_limit = $this->is_trial_mode
                ? (5 * 1024 * 1024 * 1024)              // 5GB for trial
                : $system_storage->max_storage_limit;    // DB value for production

            $after_upload_usage = $current_usage + $file_size;

            if ($after_upload_usage > $storage_limit) {
                $current_gb = round($current_usage / 1024 ** 3, 2);
                $limit_gb = round($storage_limit / 1024 ** 3, 2);
                $available_gb = round(($storage_limit - $current_usage) / 1024 ** 3, 2);

                return [
                    'allowed' => false,
                    'message' => $this->is_trial_mode
                        ? "พื้นที่เก็บข้อมูลไม่เพียงพอ (Trial: {$current_gb}/{$limit_gb}GB)"
                        : "พื้นที่เก็บข้อมูลไม่เพียงพอ (ใช้แล้ว {$current_gb}/{$limit_gb}GB)",
                    'current_usage_gb' => $current_gb,
                    'limit_gb' => $limit_gb,
                    'available_gb' => $available_gb,
                    'is_trial_mode' => $this->is_trial_mode
                ];
            }

            return [
                'allowed' => true,
                'current_usage_gb' => round($current_usage / 1024 ** 3, 2),
                'limit_gb' => round($storage_limit / 1024 ** 3, 2),
                'available_gb' => round(($storage_limit - $current_usage) / 1024 ** 3, 2),
                'is_trial_mode' => $this->is_trial_mode
            ];

        } catch (Exception $e) {
            log_message('error', 'Check system storage limit error: ' . $e->getMessage());
            return [
                'allowed' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบพื้นที่เก็บข้อมูล'
            ];
        }
    }

    /**
     * ✅ อัปเดตการใช้งาน storage ภาพรวมของระบบ
     */
    private function update_system_storage_usage($file_size)
    {
        try {
            // อัปเดต total_storage_used ในตาราง system storage
            $this->db->set('total_storage_used', 'total_storage_used + ' . (int) $file_size, FALSE)
                ->set('updated_at', date('Y-m-d H:i:s'))
                ->where('is_active', 1)
                ->update('tbl_google_drive_system_storage');

            log_message('info', "Updated system storage usage: +{$file_size} bytes");

        } catch (Exception $e) {
            log_message('error', 'Update system storage usage error: ' . $e->getMessage());
        }
    }



    private function get_settings_from_db()
    {
        try {
            // ค่าเริ่มต้น
            $default_settings = [
                'google_drive_enabled' => true,
                'max_file_size' => 104857600, // 100MB
                'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'],
                'default_user_quota' => 5368709120 // 1GB
            ];

            // ตรวจสอบว่ามีตารางหรือไม่
            if (!$this->db->table_exists('tbl_google_drive_settings')) {
                return $default_settings;
            }

            // ดึงการตั้งค่าจากฐานข้อมูล
            $db_settings = $this->db->select('setting_key, setting_value')
                ->from('tbl_google_drive_settings')
                ->where('is_active', 1)
                ->get()
                ->result();

            $settings = $default_settings;

            foreach ($db_settings as $setting) {
                switch ($setting->setting_key) {
                    case 'google_drive_enabled':
                        $settings['google_drive_enabled'] = ($setting->setting_value === '1');
                        break;

                    case 'max_file_size':
                        $size = (int) $setting->setting_value;
                        $settings['max_file_size'] = $size > 0 ? $size : $default_settings['max_file_size'];
                        break;

                    case 'allowed_file_types':
                        $types = array_map('trim', explode(',', strtolower($setting->setting_value)));
                        $settings['allowed_file_types'] = !empty($types) ? $types : $default_settings['allowed_file_types'];
                        break;

                    case 'default_user_quota':
                        $quota = (int) $setting->setting_value;
                        $settings['default_user_quota'] = $quota > 0 ? $quota : $default_settings['default_user_quota'];
                        break;
                }
            }

            return $settings;

        } catch (Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Get settings from DB error: ' . $e->getMessage());
            }

            // Return default settings หากเกิดข้อผิดพลาด
            return [
                'google_drive_enabled' => true,
                'max_file_size' => 104857600,
                'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'],
                'default_user_quota' => 5368709120
            ];
        }
    }



    /**
     * ดึง Access Token แบบง่าย
     */
    private function get_access_token_simple()
    {
        try {
            if ($this->storage_mode === 'centralized') {
                // ดึงจาก system storage
                if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                    return null;
                }

                $system_storage = $this->db->select('google_access_token')
                    ->from('tbl_google_drive_system_storage')
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($system_storage && $system_storage->google_access_token) {
                    $token_data = json_decode($system_storage->google_access_token, true);
                    return isset($token_data['access_token']) ? $token_data['access_token'] : null;
                }
            } else {
                // ดึงจาก member
                $member = $this->db->select('google_access_token')
                    ->from('tbl_member')
                    ->where('m_id', $this->member_id)
                    ->get()
                    ->row();

                if ($member && $member->google_access_token) {
                    $token_data = json_decode($member->google_access_token, true);
                    return isset($token_data['access_token']) ? $token_data['access_token'] : null;
                }
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * อัปโหลดไฟล์ไป Google Drive แบบง่าย
     */
    private function upload_to_google_drive_simple($file, $folder_id, $access_token)
    {
        try {
            if (!$access_token || !file_exists($file['tmp_name'])) {
                return ['success' => false, 'error' => 'ข้อมูลไม่ถูกต้อง'];
            }

            $metadata = ['name' => $file['name']];
            if ($folder_id && $folder_id !== 'root') {
                $metadata['parents'] = [$folder_id];
            }

            $boundary = uniqid('boundary_');
            $metadata_json = json_encode($metadata);
            $file_content = file_get_contents($file['tmp_name']);

            if ($file_content === false) {
                return ['success' => false, 'error' => 'ไม่สามารถอ่านไฟล์ได้'];
            }

            $body = "--{$boundary}\r\n";
            $body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
            $body .= $metadata_json . "\r\n";
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: {$file['type']}\r\n\r\n";
            $body .= $file_content . "\r\n";
            $body .= "--{$boundary}--\r\n";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$access_token}",
                    "Content-Type: multipart/related; boundary=\"{$boundary}\"",
                    "Content-Length: " . strlen($body)
                ],
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                return ['success' => false, 'error' => 'การเชื่อมต่อล้มเหลว: ' . $curl_error];
            }

            if ($http_code === 200 || $http_code === 201) {
                $result = json_decode($response, true);

                if ($result && isset($result['id'])) {
                    return [
                        'success' => true,
                        'file_id' => $result['id'],
                        'web_view_link' => "https://drive.google.com/file/d/{$result['id']}/view"
                    ];
                }
            }

            return ['success' => false, 'error' => "HTTP {$http_code}"];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * บันทึกข้อมูลไฟล์แบบง่าย (ปรับปรุงแล้ว)
     * ✅ แก้ไข: Auto-create folder record ถ้ายังไม่มีใน DB
     * ✅ แก้ไข: ใช้ NULL แทน 'root'
     * ✅ เพิ่ม: return insert_id
     * ✅ เพิ่ม: parent_folder_id สำหรับ permission inheritance
     */
    private function save_file_info_simple($file_id, $file_name, $file_size, $folder_id)
    {
        try {
            log_message('info', '💾 START: save_file_info_simple()');
            log_message('info', sprintf(
                '   ├─ file_id: %s, file_name: %s, size: %d bytes, folder_id: %s',
                $file_id,
                $file_name,
                $file_size,
                $folder_id ?: 'NULL'
            ));

            if ($this->storage_mode === 'centralized') {
                if (!$this->db->table_exists('tbl_google_drive_system_files')) {
                    log_message('error', '❌ Table tbl_google_drive_system_files not found');
                    return null;
                }

                // ✅ [FIX 1] ตรวจสอบและสร้าง folder record ถ้ายังไม่มี
                $parent_folder_id = null;
                if (!empty($folder_id) && $folder_id !== 'root') {
                    // ตรวจสอบว่า folder_id มีอยู่ใน tbl_google_drive_system_folders หรือไม่
                    $folder_exists = $this->db
                        ->where('folder_id', $folder_id)
                        ->where('is_active', 1)
                        ->count_all_results('tbl_google_drive_system_folders') > 0;

                    if (!$folder_exists) {
                        log_message('info', sprintf(
                            '⚠️ Folder %s not found in DB, attempting to create record...',
                            $folder_id
                        ));

                        // พยายามสร้าง folder record
                        $folder_created = $this->ensure_folder_exists_in_db($folder_id);

                        if (!$folder_created) {
                            log_message('error', sprintf(
                                '❌ Cannot create folder record for %s, setting folder_id to NULL',
                                $folder_id
                            ));
                            $folder_id = null; // fallback to root
                        } else {
                            log_message('info', '✅ Folder record created successfully');

                            // ดึง parent_folder_id ที่เพิ่งสร้าง
                            $folder_info = $this->db
                                ->select('parent_folder_id')
                                ->where('folder_id', $folder_id)
                                ->get('tbl_google_drive_system_folders')
                                ->row();

                            if ($folder_info) {
                                $parent_folder_id = $folder_info->parent_folder_id;
                            }
                        }
                    } else {
                        // Folder มีอยู่แล้ว ดึง parent_folder_id
                        $folder_info = $this->db
                            ->select('parent_folder_id')
                            ->where('folder_id', $folder_id)
                            ->get('tbl_google_drive_system_folders')
                            ->row();

                        if ($folder_info) {
                            $parent_folder_id = $folder_info->parent_folder_id;
                        }
                    }
                }

                // ✅ [FIX 2] ใช้ NULL แทน 'root'
                $insert_data = [
                    'file_id' => $file_id,
                    'file_name' => $file_name,
                    'original_name' => $file_name,
                    'file_size' => $file_size,
                    'folder_id' => $folder_id ?: null, // ✅ ใช้ NULL แทน 'root'
                    'parent_folder_id' => $parent_folder_id, // ✅ เพิ่ม parent สำหรับ inheritance
                    'uploaded_by' => $this->member_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                log_message('info', '📝 Inserting file record into database...');
                log_message('info', '   └─ Data: ' . json_encode($insert_data, JSON_UNESCAPED_UNICODE));

                $this->db->insert('tbl_google_drive_system_files', $insert_data);
                $insert_id = $this->db->insert_id();

                if ($insert_id) {
                    log_message('info', sprintf('✅ File record saved: insert_id=%d', $insert_id));
                } else {
                    log_message('error', '❌ Failed to save file record');
                    return null;
                }
            } else {
                log_message('info', '⚠️ Skipped file record (storage_mode != centralized)');
                $insert_id = null;
            }

            // ✅ [EXISTING] อัปเดต quota ของ member
            log_message('info', sprintf('🔄 Updating storage quota for member_id=%d...', $this->member_id));

            $current_used = $this->db->select('storage_quota_used')
                ->from('tbl_member')
                ->where('m_id', $this->member_id)
                ->get()
                ->row();

            if ($current_used) {
                $old_used = $current_used->storage_quota_used ?: 0;
                $new_used = $old_used + $file_size;

                $this->db->where('m_id', $this->member_id)
                    ->update('tbl_member', [
                        'storage_quota_used' => $new_used,
                        'last_storage_access' => date('Y-m-d H:i:s')
                    ]);

                log_message('info', sprintf(
                    '✅ Member quota updated: %d → %d bytes (+%d)',
                    $old_used,
                    $new_used,
                    $file_size
                ));
            } else {
                log_message('error', sprintf(
                    '❌ Cannot find member_id=%d in tbl_member',
                    $this->member_id
                ));
            }

            log_message('info', '✅ END: save_file_info_simple()');
            return $insert_id; // ✅ [FIX 3] return insert_id

        } catch (Exception $e) {
            // Log error with full details
            log_message('error', '💥 save_file_info_simple exception: ' . $e->getMessage());
            log_message('error', '   └─ Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * ตรวจสอบและสร้าง folder record ใน DB ถ้ายังไม่มี
     * 
     * @param string $folder_id Google Drive Folder ID
     * @return bool true ถ้าสร้างสำเร็จหรือมีอยู่แล้ว, false ถ้าล้มเหลว
     */
    private function ensure_folder_exists_in_db($folder_id)
    {
        try {
            log_message('info', sprintf('🔍 Checking if folder %s exists in DB...', $folder_id));

            // ตรวจสอบอีกครั้งเผื่อมีการ race condition
            $exists = $this->db
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->count_all_results('tbl_google_drive_system_folders') > 0;

            if ($exists) {
                log_message('info', '✅ Folder already exists in DB');
                return true;
            }

            // ดึงข้อมูล folder จาก Google Drive API
            log_message('info', '📡 Fetching folder info from Google Drive API...');
            $access_token = $this->get_access_token_simple();

            if (!$access_token) {
                log_message('error', '❌ Cannot get access token');
                return false;
            }

            $folder_info = $this->fetch_folder_info_from_api($folder_id, $access_token);

            if (!$folder_info) {
                log_message('error', sprintf('❌ Cannot fetch folder info for %s from API', $folder_id));
                return false;
            }

            log_message('info', sprintf(
                '📁 Folder info from API: name=%s, parent=%s',
                $folder_info['name'],
                $folder_info['parent_id'] ?: 'NULL'
            ));

            // กำหนด folder_type ตาม parent (ถ้ามี)
            $folder_type = 'system'; // default
            $parent_folder_id = $folder_info['parent_id'];

            if ($parent_folder_id) {
                // ดูว่า parent เป็น type อะไร
                $parent_info = $this->db
                    ->select('folder_type')
                    ->where('folder_id', $parent_folder_id)
                    ->get('tbl_google_drive_system_folders')
                    ->row();

                if ($parent_info) {
                    // ถ้า parent เป็น shared, subfolder ก็เป็น shared
                    if ($parent_info->folder_type === 'shared') {
                        $folder_type = 'shared';
                    } elseif ($parent_info->folder_type === 'department') {
                        $folder_type = 'department';
                    }
                }
            }

            // INSERT folder record
            $folder_data = [
                'folder_name' => $folder_info['name'],
                'folder_id' => $folder_id,
                'parent_folder_id' => $parent_folder_id,
                'folder_type' => $folder_type,
                'permission_level' => 'restricted', // default
                'folder_description' => 'Auto-created by upload system',
                'storage_quota' => 5368709120, // 5GB default
                'storage_used' => 0,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->member_id
            ];

            log_message('info', '💾 Inserting folder record...');
            $this->db->insert('tbl_google_drive_system_folders', $folder_data);

            if ($this->db->affected_rows() > 0) {
                log_message('info', sprintf(
                    '✅ Folder record created: %s (%s)',
                    $folder_info['name'],
                    $folder_id
                ));
                return true;
            } else {
                log_message('error', '❌ Failed to insert folder record');
                return false;
            }

        } catch (Exception $e) {
            log_message('error', '💥 ensure_folder_exists_in_db exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูล folder จาก Google Drive API
     * 
     * @param string $folder_id Google Drive Folder ID
     * @param string $access_token Access Token
     * @return array|null ['id', 'name', 'parent_id'] หรือ null ถ้าล้มเหลว
     */
    private function fetch_folder_info_from_api($folder_id, $access_token)
    {
        try {
            $url = "https://www.googleapis.com/drive/v3/files/{$folder_id}";
            $url .= "?fields=id,name,parents&supportsAllDrives=true";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            log_message('info', sprintf('📡 API Response: HTTP %d', $http_code));

            if ($http_code === 200) {
                $data = json_decode($response, true);

                return [
                    'id' => $data['id'],
                    'name' => $data['name'] ?? 'Unknown Folder',
                    'parent_id' => isset($data['parents'][0]) ? $data['parents'][0] : null
                ];
            } else {
                log_message('error', sprintf(
                    '❌ API Error: HTTP %d, Response: %s',
                    $http_code,
                    substr($response, 0, 500)
                ));
                return null;
            }

        } catch (Exception $e) {
            log_message('error', '💥 fetch_folder_info_from_api exception: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * ดึงข้อมูล Member แบบง่าย
     */
    private function get_simple_member_info()
    {
        try {
            return $this->db->select('storage_quota_used, storage_quota_limit')
                ->from('tbl_member')
                ->where('m_id', $this->member_id)
                ->get()
                ->row();
        } catch (Exception $e) {
            log_message('error', 'Get simple member info error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึง Access Token แบบง่าย
     */
    private function get_simple_access_token()
    {
        try {
            if ($this->storage_mode === 'centralized') {
                // System storage token
                if ($this->db->table_exists('tbl_google_drive_system_storage')) {
                    $system_storage = $this->db->select('google_access_token')
                        ->from('tbl_google_drive_system_storage')
                        ->where('is_active', 1)
                        ->get()
                        ->row();

                    if ($system_storage) {
                        $token_data = json_decode($system_storage->google_access_token, true);
                        return $token_data['access_token'] ?? null;
                    }
                }
            } else {
                // User token
                $member = $this->db->select('google_access_token')
                    ->from('tbl_member')
                    ->where('m_id', $this->member_id)
                    ->get()
                    ->row();

                if ($member && $member->google_access_token) {
                    $token_data = json_decode($member->google_access_token, true);
                    return $token_data['access_token'] ?? null;
                }
            }

            return null;
        } catch (Exception $e) {
            log_message('error', 'Get simple access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ลบไฟล์จาก Google Drive แบบง่าย
     */
    private function simple_delete_from_google_drive($item_id, $access_token)
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$item_id}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$access_token}"
                ],
                CURLOPT_TIMEOUT => 60
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $http_code === 200 || $http_code === 204;

        } catch (Exception $e) {
            log_message('error', 'Simple delete from Google Drive error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * 🔍 ตรวจสอบสิทธิ์การเข้าถึงไฟล์
     */
    private function check_file_access_permission($file_id)
    {
        try {
            if ($this->storage_mode === 'centralized') {
                if ($this->db->table_exists('tbl_google_drive_system_files')) {
                    $file = $this->db->select('uploaded_by, folder_id')
                        ->from('tbl_google_drive_system_files')
                        ->where('file_id', $file_id)
                        ->get()
                        ->row();

                    if ($file) {
                        // ถ้าเป็นเจ้าของไฟล์
                        if ($file->uploaded_by == $this->member_id) {
                            return true;
                        }

                        // ตรวจสอบสิทธิ์โฟลเดอร์
                        return $this->check_folder_access_permission($file->folder_id);
                    }
                }
            } else {
                if ($this->db->table_exists('tbl_google_drive_user_files')) {
                    $file = $this->db->select('member_id')
                        ->from('tbl_google_drive_user_files')
                        ->where('file_id', $file_id)
                        ->where('member_id', $this->member_id)
                        ->get()
                        ->row();

                    return !empty($file);
                }
            }

            return true; // Default ให้เข้าถึงได้

        } catch (Exception $e) {
            log_message('error', 'Check file access permission error: ' . $e->getMessage());
            return false;
        }
    }





    /**
     * 💾 ลบรายการจาก Database (รองรับ Storage Mode)
     * ✅ แก้ไข: เพิ่มการเช็ค storage_mode เพื่อเลือก Table ที่ถูกต้อง
     * 
     * @param string $item_id   File ID หรือ Folder ID จาก Google Drive
     * @param string $item_type 'file' หรือ 'folder'
     * @return bool true = ลบสำเร็จ, false = ไม่พบหรือล้มเหลว
     */
    private function remove_item_from_database($item_id, $item_type)
    {
        try {
            $this->db->trans_start();
            $deleted = false;

            log_message('info', sprintf(
                '🗑️ [DB Delete] Starting deletion: %s=%s, storage_mode=%s, member=%d',
                $item_type,
                $item_id,
                $this->storage_mode,
                $this->member_id
            ));

            // ✅ [FILE] ลบไฟล์
            if ($item_type === 'file') {

                if ($this->storage_mode === 'centralized') {
                    // ✅ Centralized Mode: ลบจาก System Files
                    log_message('info', '💾 [Centralized] Deleting from tbl_google_drive_system_files');

                    $file_info = $this->db
                        ->select('id, file_name, uploaded_by, is_active')
                        ->where('file_id', $item_id)
                        ->where('is_active', 1)
                        ->get('tbl_google_drive_system_files')
                        ->row();

                    if ($file_info) {
                        log_message('info', sprintf(
                            '🔍 File found: id=%d, name=%s, uploaded_by=%d',
                            $file_info->id,
                            $file_info->file_name,
                            $file_info->uploaded_by
                        ));

                        // Soft Delete (set is_active = 0)
                        $this->db
                            ->where('file_id', $item_id)
                            ->where('is_active', 1)
                            ->update('tbl_google_drive_system_files', [
                                'is_active' => 0,
                                'deleted_at' => date('Y-m-d H:i:s'),
                                'deleted_by' => $this->member_id
                            ]);

                        $affected = $this->db->affected_rows();
                        $deleted = ($affected > 0);

                        if ($deleted) {
                            log_message('info', sprintf(
                                '✅ File soft deleted (affected: %d)',
                                $affected
                            ));
                        }
                    } else {
                        log_message('info', '⚠️ File not found or already deleted');
                    }

                } else {
                    // ✅ User-based Mode: ลบจาก Member Files
                    log_message('info', '💾 [User-based] Deleting from tbl_google_drive_member_files');

                    $this->db
                        ->where('file_id', $item_id)
                        ->where('uploaded_by', $this->member_id)
                        ->delete('tbl_google_drive_member_files');

                    $affected = $this->db->affected_rows();
                    $deleted = ($affected > 0);

                    if ($deleted) {
                        log_message('info', sprintf(
                            '✅ File deleted from member files (affected: %d)',
                            $affected
                        ));
                    } else {
                        log_message('info', '⚠️ File not found in member files');
                    }
                }
            }
            // ✅ [FOLDER] ลบโฟลเดอร์
            elseif ($item_type === 'folder') {

                if ($this->storage_mode === 'centralized') {
                    // ✅ Centralized Mode: ลบจาก System Folders
                    log_message('info', '💾 [Centralized] Deleting from tbl_google_drive_system_folders');

                    $this->db
                        ->where('folder_id', $item_id)
                        ->delete('tbl_google_drive_system_folders');

                    $affected = $this->db->affected_rows();
                    $deleted = ($affected > 0);

                    if ($deleted) {
                        log_message('info', sprintf(
                            '✅ Folder deleted (affected: %d)',
                            $affected
                        ));

                        // ลบ Permissions
                        if ($this->db->table_exists('tbl_google_drive_system_folder_access')) {
                            $this->db
                                ->where('folder_id', $item_id)
                                ->delete('tbl_google_drive_system_folder_access');

                            log_message('info', sprintf(
                                'ℹ️ Deleted %d permission records',
                                $this->db->affected_rows()
                            ));
                        }
                    }

                } else {
                    // ✅ User-based Mode: ลบจาก User Folders
                    log_message('info', '💾 [User-based] Deleting from tbl_google_drive_folders');

                    $this->db
                        ->where('folder_id', $item_id)
                        ->delete('tbl_google_drive_folders');

                    $affected = $this->db->affected_rows();
                    $deleted = ($affected > 0);

                    if ($deleted) {
                        log_message('info', sprintf(
                            '✅ Folder deleted (affected: %d)',
                            $affected
                        ));

                        // ลบ Permissions
                        if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                            $this->db
                                ->where('folder_id', $item_id)
                                ->delete('tbl_google_drive_member_folder_access');

                            log_message('info', sprintf(
                                'ℹ️ Deleted %d permission records',
                                $this->db->affected_rows()
                            ));
                        }
                    }
                }
            }

            $this->db->trans_complete();

            $status = $deleted ? 'SUCCESS' : 'NOT FOUND';
            log_message('info', sprintf(
                '📊 [DB Delete] Result: %s for %s %s (mode: %s)',
                $status,
                $item_type,
                $item_id,
                $this->storage_mode
            ));

            return $this->db->trans_status() && $deleted;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', sprintf(
                '💥 [DB Delete] Exception: %s',
                $e->getMessage()
            ));
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }


    /**
     * 🔗 อัปโหลดไฟล์ไปยัง Google Drive (Production)
     */
    private function upload_file_to_google_drive($file_data, $folder_id, $access_token)
    {
        try {
            // ตรวจสอบ access token
            if (!$access_token || $access_token === 'trial_token') {
                return [
                    'success' => false,
                    'error' => 'Invalid access token'
                ];
            }

            // เตรียม metadata
            $metadata = [
                'name' => $file_data['name']
            ];

            // กำหนด parent folder ถ้ามี
            if ($folder_id && $folder_id !== 'root') {
                $metadata['parents'] = [$folder_id];
            }

            // URL สำหรับ Google Drive API
            $upload_url = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart';

            // สร้าง multipart content
            $delimiter = '-------314159265358979323846';
            $close_delim = "\r\n--{$delimiter}--\r\n";

            $metadata_json = json_encode($metadata);

            // อ่านไฟล์
            $file_content = file_get_contents($file_data['tmp_name']);

            // สร้าง multipart body
            $multipart_body = "--{$delimiter}\r\n";
            $multipart_body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
            $multipart_body .= $metadata_json . "\r\n";
            $multipart_body .= "--{$delimiter}\r\n";
            $multipart_body .= "Content-Type: {$file_data['type']}\r\n\r\n";
            $multipart_body .= $file_content;
            $multipart_body .= $close_delim;

            // ตั้งค่า cURL
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $upload_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$access_token}",
                    "Content-Type: multipart/related; boundary=\"{$delimiter}\"",
                    "Content-Length: " . strlen($multipart_body)
                ],
                CURLOPT_POSTFIELDS => $multipart_body,
                CURLOPT_TIMEOUT => 300, // 5 minutes
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                log_message('error', 'cURL error: ' . $curl_error);
                return [
                    'success' => false,
                    'error' => 'การเชื่อมต่อล้มเหลว: ' . $curl_error
                ];
            }

            if ($http_code === 200 || $http_code === 201) {
                $result = json_decode($response, true);

                if (isset($result['id'])) {
                    return [
                        'success' => true,
                        'file_id' => $result['id'],
                        'web_view_link' => $result['webViewLink'] ?? "https://drive.google.com/file/d/{$result['id']}/view"
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'ไม่ได้รับ file ID จาก Google Drive'
                    ];
                }
            } else {
                $error_response = json_decode($response, true);
                $error_message = 'HTTP ' . $http_code;

                if (isset($error_response['error']['message'])) {
                    $error_message .= ': ' . $error_response['error']['message'];
                }

                log_message('error', 'Google Drive API error: ' . $response);

                return [
                    'success' => false,
                    'error' => $error_message
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Upload to Google Drive error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'เกิดข้อผิดพลาดในการอัปโหลด: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 📁 สร้างโฟลเดอร์ใหม่ (แก้ไข Error Handling ครบถ้วน)
     */



    /**
     * 🔗 สร้างโฟลเดอร์ใน Google Drive (Production)
     */
    private function create_google_drive_folder($folder_name, $parent_id, $access_token)
    {
        try {
            if (!$access_token || $access_token === 'trial_token') {
                return [
                    'success' => false,
                    'error' => 'Invalid access token'
                ];
            }

            $metadata = [
                'name' => $folder_name,
                'mimeType' => 'application/vnd.google-apps.folder'
            ];

            if ($parent_id && $parent_id !== 'root') {
                $metadata['parents'] = [$parent_id];
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://www.googleapis.com/drive/v3/files',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$access_token}",
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS => json_encode($metadata),
                CURLOPT_TIMEOUT => 60
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200 || $http_code === 201) {
                $result = json_decode($response, true);

                return [
                    'success' => true,
                    'folder_id' => $result['id'],
                    'web_view_link' => "https://drive.google.com/drive/folders/{$result['id']}"
                ];
            } else {
                $error_response = json_decode($response, true);
                return [
                    'success' => false,
                    'error' => $error_response['error']['message'] ?? 'ไม่สามารถสร้างโฟลเดอร์ได้'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 🗑️ ลบไฟล์/โฟลเดอร์ (แยก Logic ตาม Storage Mode)
     * ✅ อัปเดต: รองรับการลบโฟลเดอร์พร้อม Recursive Storage Update
     * ✅ แก้ไข: Pre-scan files ก่อนลบเพื่อ Update Storage ได้ถูกต้อง
     */
    public function delete_item()
    {
        try {
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');

            if (!$this->input->is_ajax_request()) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงการตั้งค่าจากฐานข้อมูล
            $settings = $this->get_settings_from_db();

            if (!$settings['google_drive_enabled']) {
                log_message('info', '❌ Delete blocked: Google Drive is disabled');
                http_response_code(503);
                echo json_encode([
                    'success' => false,
                    'message' => 'Google Drive ถูกปิดใช้งานโดยระบบ',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $item_id = $this->input->post('item_id');
            $item_type = $this->input->post('item_type');

            log_message('info', sprintf(
                '🗑️ Delete request: item_id=%s, type=%s, member_id=%d, storage_mode=%s',
                $item_id,
                $item_type,
                $this->member_id,
                $this->storage_mode
            ));

            if (!$item_id || !$item_type) {
                log_message('info', '❌ Delete failed: Missing item_id or item_type');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ✅ ตรวจสอบสิทธิ์การลบ
            $folder_id = null;

            if ($item_type === 'folder') {
                $folder_id = $item_id;
            } elseif ($item_type === 'file') {
                $folder_id = $this->get_file_folder_id($item_id);
                log_message('info', sprintf('📁 File parent folder: %s', $folder_id));
            }

            if ($folder_id && !$this->check_delete_permission_in_folder($folder_id)) {
                log_message('info', sprintf(
                    '❌ Delete denied: No delete permission in folder %s',
                    $folder_id
                ));
                if ($item_type === 'folder') {
                    $this->access_denied_response($folder_id);
                    return;
                } else {
                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์ลบไฟล์ในโฟลเดอร์นี้',
                        'timestamp' => date('Y-m-d H:i:s')
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            log_message('info', '✅ Delete permission granted');

            // ✅ ดึงข้อมูลรายการก่อนลบ (รวม uploaded_by และ recursive scan สำหรับโฟลเดอร์)
            $item_info = $this->get_item_info_before_delete($item_id, $item_type);

            if ($item_info && $item_info['name']) {
                if ($item_type === 'file') {
                    log_message('info', sprintf(
                        '📋 Item info: name=%s, size=%d bytes (%.2f MB), uploaded_by=%s',
                        $item_info['name'],
                        $item_info['file_size'],
                        $item_info['file_size'] / 1024 / 1024,
                        isset($item_info['uploaded_by']) ? $item_info['uploaded_by'] : 'unknown'
                    ));
                } else {
                    // โฟลเดอร์
                    $file_count = isset($item_info['file_count']) ? $item_info['file_count'] : 0;
                    $uploader_count = isset($item_info['uploaders']) ? count($item_info['uploaders']) : 0;

                    log_message('info', sprintf(
                        '📋 Folder info: name=%s, contains %d files (%.2f MB total), %d uploaders',
                        $item_info['name'],
                        $file_count,
                        $item_info['file_size'] / 1024 / 1024,
                        $uploader_count
                    ));
                }
            } else {
                log_message('info', '⚠️ Item info not found in database');
            }

            // ✅ ดึง Access Token
            $access_token = $this->get_access_token_simple();
            if (!$access_token) {
                log_message('info', '❌ Delete failed: Cannot get access token');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเชื่อมต่อ Google Drive ได้',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ✅ ลบจาก Google Drive
            log_message('info', sprintf('🌐 Deleting from Google Drive: %s', $item_id));
            $delete_result = $this->simple_delete_from_google_drive($item_id, $access_token);

            if ($delete_result) {
                log_message('info', '✅ Deleted from Google Drive successfully');

                // ✅ ลบ permission records (เพิ่มส่วนนี้)
                if ($item_type === 'folder') {
                    log_message('info', '🔐 Removing folder permissions...');
                    $perm_result = $this->remove_folder_permissions($item_id, true);

                    if ($perm_result) {
                        log_message('info', '✅ Folder permissions removed successfully');
                    } else {
                        log_message('info', '⚠️ Folder permissions removal failed or skipped');
                    }
                }

                // ✅ ลบจากฐานข้อมูล
                log_message('info', '💾 Removing from database...');
                $database_result = $this->remove_item_from_database($item_id, $item_type);

                if ($database_result) {
                    log_message('info', '✅ Removed from database successfully');
                } else {
                    log_message('info', '⚠️ Database removal failed or no records found');
                }

                // ✅ อัปเดต storage usage (ทั้ง System + Member)
                // 🔥 [UPDATED] รองรับทั้ง FILE และ FOLDER

                if ($item_type === 'file') {
                    // ========================================
                    // [FILE] Logic เดิม - ไม่เปลี่ยนแปลง
                    // ========================================

                    if (isset($item_info['file_size'])) {
                        $file_size = (int) $item_info['file_size'];
                        $uploaded_by = isset($item_info['uploaded_by']) ? (int) $item_info['uploaded_by'] : null;

                        log_message('info', sprintf(
                            '📏 File details: size=%d bytes (%.2f MB), uploaded_by=%s',
                            $file_size,
                            $file_size / 1024 / 1024,
                            $uploaded_by ? $uploaded_by : 'unknown'
                        ));

                        if ($file_size > 0) {

                            // ✅ [CENTRALIZED MODE] ลด System Storage + Member Storage
                            if ($this->storage_mode === 'centralized') {
                                log_message('info', sprintf(
                                    '📉 [Centralized Mode] Decreasing system storage by %d bytes (%.2f MB)',
                                    $file_size,
                                    $file_size / 1024 / 1024
                                ));

                                $this->decrease_system_storage_usage($file_size);
                                log_message('info', '✅ System storage decreased successfully');

                                // ลด Member Storage
                                if ($uploaded_by) {
                                    log_message('info', sprintf(
                                        '🔄 Decreasing member storage for member_id=%d...',
                                        $uploaded_by
                                    ));

                                    $member_decrease_result = $this->decrease_member_storage_usage($uploaded_by, $file_size);

                                    if ($member_decrease_result) {
                                        log_message('info', '✅ Member storage decreased successfully');
                                    } else {
                                        log_message('info', '⚠️ Member storage decrease failed or skipped');
                                    }
                                } else {
                                    log_message('info', '⚠️ Cannot decrease member storage: uploaded_by not found');
                                    log_message('info', '⚠️ File may be orphaned or uploaded before tracking was implemented');
                                }

                            }
                            // ✅ [USER-BASED MODE] ลด Member Storage เท่านั้น
                            else {
                                log_message('info', sprintf(
                                    'ℹ️ [User-based Mode] Skipping system storage decrease (file size: %d bytes, %.2f MB)',
                                    $file_size,
                                    $file_size / 1024 / 1024
                                ));
                                log_message('info', 'ℹ️ User-based mode uses individual Google Drive accounts, not system storage');

                                // ลด Member Storage
                                if ($uploaded_by) {
                                    log_message('info', sprintf(
                                        '🔄 [User-based Mode] Decreasing member storage for member_id=%d...',
                                        $uploaded_by
                                    ));

                                    $member_decrease_result = $this->decrease_member_storage_usage($uploaded_by, $file_size);

                                    if ($member_decrease_result) {
                                        log_message('info', '✅ Member storage decreased successfully');
                                    } else {
                                        log_message('info', '⚠️ Member storage decrease failed or skipped');
                                    }
                                } else {
                                    log_message('info', '⚠️ Cannot decrease member storage: uploaded_by not found');
                                }
                            }

                        } else {
                            log_message('info', '⚠️ File size is 0, skipping all storage updates');
                        }
                    } else {
                        log_message('info', '⚠️ File size not found in item_info, skipping storage update');
                    }

                } elseif ($item_type === 'folder') {
                    // ========================================
                    // [FOLDER] Logic ใหม่ - รองรับ Recursive
                    // ========================================

                    // ตรวจสอบว่ามีไฟล์ในโฟลเดอร์หรือไม่
                    if (isset($item_info['files']) && count($item_info['files']) > 0) {

                        $total_size = (int) $item_info['file_size'];
                        $uploaders = $item_info['uploaders'];
                        $file_count = $item_info['file_count'];

                        log_message('info', sprintf(
                            '📊 [Folder Delete] Processing storage updates: %d files, %.2f MB total, %d uploaders',
                            $file_count,
                            $total_size / 1024 / 1024,
                            count($uploaders)
                        ));

                        // ✅ [CENTRALIZED MODE] ลด System Storage + Member Storage
                        if ($this->storage_mode === 'centralized') {

                            // ลด System Storage
                            log_message('info', sprintf(
                                '📉 [Centralized] Decreasing system storage by %.2f MB',
                                $total_size / 1024 / 1024
                            ));

                            $this->decrease_system_storage_usage($total_size);
                            log_message('info', '✅ System storage decreased');

                            // ลด Member Storage สำหรับทุกคนที่มีไฟล์ในโฟลเดอร์
                            log_message('info', sprintf(
                                '🔄 Processing member storage updates for %d members...',
                                count($uploaders)
                            ));

                            foreach ($uploaders as $member_id => $size) {
                                log_message('info', sprintf(
                                    '  → Member %d: %.2f MB',
                                    $member_id,
                                    $size / 1024 / 1024
                                ));

                                $result = $this->decrease_member_storage_usage($member_id, $size);

                                if ($result) {
                                    log_message('info', sprintf(
                                        '  ✅ Member %d storage decreased',
                                        $member_id
                                    ));
                                } else {
                                    log_message('error', sprintf(
                                        '  ❌ Failed to decrease storage for member %d',
                                        $member_id
                                    ));
                                }
                            }

                            log_message('info', '✅ All member storage updates completed');
                        }
                        // ✅ [USER-BASED MODE] ลด Member Storage เท่านั้น
                        else {
                            log_message('info', sprintf(
                                'ℹ️ [User-based Mode] Skipping system storage (%.2f MB)',
                                $total_size / 1024 / 1024
                            ));

                            // ลด Member Storage
                            log_message('info', sprintf(
                                '🔄 [User-based] Processing member storage for %d members...',
                                count($uploaders)
                            ));

                            foreach ($uploaders as $member_id => $size) {
                                log_message('info', sprintf(
                                    '  → Member %d: %.2f MB',
                                    $member_id,
                                    $size / 1024 / 1024
                                ));

                                $this->decrease_member_storage_usage($member_id, $size);
                            }

                            log_message('info', '✅ All member storage updates completed');
                        }

                        log_message('info', '🎉 Folder deletion with storage updates completed');

                    } else {
                        log_message('info', '📁 Folder is empty, no storage update needed');
                    }
                }

                // ✅ Log activity
                $activity_detail = "ลบ{$item_type}: " . ($item_info['name'] ?? $item_id);
                if ($item_type === 'folder' && isset($item_info['file_count'])) {
                    $activity_detail .= sprintf(' (%d ไฟล์)', $item_info['file_count']);
                }

                $this->simple_log_activity('delete_' . $item_type, $activity_detail);

                log_message('info', '🎉 Delete operation completed successfully');

                // ✅ Prepare response data
                $response_data = [
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'item_name' => $item_info['name'] ?? null,
                    'storage_mode' => $this->storage_mode,
                    'database_deleted' => $database_result
                ];

                // เพิ่มข้อมูลตาม item_type
                if ($item_type === 'file') {
                    $response_data['file_size'] = $item_info['file_size'] ?? 0;
                    $response_data['uploaded_by'] = $item_info['uploaded_by'] ?? null;
                } else {
                    $response_data['file_count'] = $item_info['file_count'] ?? 0;
                    $response_data['total_size'] = $item_info['file_size'] ?? 0;
                    $response_data['affected_members'] = isset($item_info['uploaders']) ? count($item_info['uploaders']) : 0;
                }

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => $item_type === 'file' ? 'ลบไฟล์เรียบร้อย' : 'ลบโฟลเดอร์เรียบร้อย',
                    'data' => $response_data,
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);

            } else {
                log_message('info', '❌ Failed to delete from Google Drive');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถลบรายการจาก Google Drive ได้',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            }
            exit;

        } catch (Exception $e) {
            log_message('error', '💥 Delete item exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            while (ob_get_level()) {
                ob_end_clean();
            }

            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดภายในระบบ',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }


    private function simple_log_activity($action_type, $description = '')
    {
        try {
            // ตรวจสอบว่าตารางมีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_google_drive_activity_logs')) {
                return false;
            }

            $log_data = [
                'member_id' => $this->member_id ?: 0,
                'action_type' => $action_type,
                'action_description' => $description ?: $action_type,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => substr($this->input->user_agent(), 0, 500),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_activity_logs', $log_data);
            return true;

        } catch (Exception $e) {
            log_message('error', 'Simple log activity error: ' . $e->getMessage());
            return false;
        }
    }




    /**
     * 📊 อัปเดต System Storage Usage (ทั้ง Settings และ Storage Table)
     * แก้ไข: เพิ่มการอัปเดต tbl_google_drive_system_storage ด้วย
     */

    /**
     * 📈 เพิ่ม System Storage Usage
     */
    private function increase_system_storage_usage($file_size)
    {
        try {
            if ($file_size <= 0) {
                log_message('info', '⚠️ [System Storage] File size is 0 or negative, skipping increase');
                return false;
            }

            $this->db->trans_start();

            // ✅ 1. อัปเดต Settings Table (เดิม)
            $settings = $this->get_settings_from_db();

            if ($settings['system_storage_mode'] === 'centralized') {
                $current_usage = isset($settings['system_storage_used'])
                    ? (int) $settings['system_storage_used']
                    : 0;

                $new_usage = $current_usage + $file_size;

                $this->db->where('setting_key', 'system_storage_used')
                    ->update('tbl_google_drive_settings', [
                        'setting_value' => $new_usage,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                log_message('info', sprintf(
                    '📈 [Settings Table] System storage increased: %s → %s (+%s)',
                    $this->format_bytes($current_usage),
                    $this->format_bytes($new_usage),
                    $this->format_bytes($file_size)
                ));
            }

            // ✅ 2. อัปเดต System Storage Table (ใหม่)
            $this->db->set('total_storage_used', 'total_storage_used + ' . (int) $file_size, FALSE)
                ->set('updated_at', date('Y-m-d H:i:s'))
                ->where('is_active', 1)
                ->update('tbl_google_drive_system_storage');

            $affected = $this->db->affected_rows();

            if ($affected > 0) {
                log_message('info', sprintf(
                    '✅ [Storage Table] System storage increased: +%d bytes (%.2f MB) - affected: %d',
                    $file_size,
                    $file_size / 1024 / 1024,
                    $affected
                ));
            } else {
                log_message('info', '⚠️ [Storage Table] No active storage record found or update failed');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', sprintf(
                '💥 [System Storage] Increase error: %s',
                $e->getMessage()
            ));
            return false;
        }
    }

    /**
     * 📉 ลด System Storage Usage
     */
    private function decrease_system_storage_usage($file_size)
    {
        try {
            if ($file_size <= 0) {
                log_message('info', '⚠️ [System Storage] File size is 0 or negative, skipping decrease');
                return false;
            }

            $this->db->trans_start();

            // ✅ 1. อัปเดต Settings Table (เดิม)
            $settings = $this->get_settings_from_db();

            if ($settings['system_storage_mode'] === 'centralized') {
                $current_usage = isset($settings['system_storage_used'])
                    ? (int) $settings['system_storage_used']
                    : 0;

                $new_usage = max(0, $current_usage - $file_size); // ป้องกันติดลบ

                $this->db->where('setting_key', 'system_storage_used')
                    ->update('tbl_google_drive_settings', [
                        'setting_value' => $new_usage,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                log_message('info', sprintf(
                    '📉 [Settings Table] System storage decreased: %s → %s (-%s)',
                    $this->format_bytes($current_usage),
                    $this->format_bytes($new_usage),
                    $this->format_bytes($file_size)
                ));

                // Warning ถ้าเกือบติดลบ
                if ($current_usage < $file_size) {
                    log_message('info', sprintf(
                        '⚠️ [Settings Table] Storage would go negative (%d - %d = %d), clamped to 0',
                        $current_usage,
                        $file_size,
                        $current_usage - $file_size
                    ));
                }
            }

            // ✅ 2. อัปเดต System Storage Table (ใหม่) - ใช้ GREATEST เพื่อป้องกันติดลบ
            $this->db->set('total_storage_used', 'GREATEST(total_storage_used - ' . (int) $file_size . ', 0)', FALSE)
                ->set('updated_at', date('Y-m-d H:i:s'))
                ->where('is_active', 1)
                ->update('tbl_google_drive_system_storage');

            $affected = $this->db->affected_rows();

            if ($affected > 0) {
                log_message('info', sprintf(
                    '✅ [Storage Table] System storage decreased: -%d bytes (%.2f MB) - affected: %d',
                    $file_size,
                    $file_size / 1024 / 1024,
                    $affected
                ));
            } else {
                log_message('info', '⚠️ [Storage Table] No active storage record found or update failed');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', sprintf(
                '💥 [System Storage] Decrease error: %s',
                $e->getMessage()
            ));
            return false;
        }
    }

    /**
     * 🔄 Helper: Format bytes เป็น human-readable
     */
    private function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * 📋 ดึงข้อมูลรายการก่อนลบ (แยก Logic ตาม Storage Mode)
     * ✅ อัปเดต: เพิ่ม return uploaded_by
     */
    private function get_item_info_before_delete($item_id, $item_type)
    {
        try {
            log_message('info', sprintf(
                '📋 Getting item info: item_id=%s, type=%s, storage_mode=%s',
                $item_id,
                $item_type,
                $this->storage_mode
            ));

            $info = ['name' => null, 'file_size' => 0, 'uploaded_by' => null];

            if ($item_type === 'file') {
                // ✅ Logic เดิมสำหรับไฟล์
                if ($this->storage_mode === 'centralized') {
                    $file = $this->db->select('file_name, file_size, uploaded_by')
                        ->from('tbl_google_drive_system_files')
                        ->where('file_id', $item_id)
                        ->where('is_active', 1)
                        ->get()
                        ->row();

                    if ($file) {
                        $info['name'] = $file->file_name;
                        $info['file_size'] = (int) $file->file_size;
                        $info['uploaded_by'] = (int) $file->uploaded_by;
                    }
                }
            }
            // 🔥 [NEW] แก้ไข Logic สำหรับโฟลเดอร์
            elseif ($item_type === 'folder') {
                log_message('info', '🔍 Searching folder in tbl_google_drive_system_folders');

                // ดึงข้อมูลโฟลเดอร์
                $folder = $this->db->select('folder_name')
                    ->from('tbl_google_drive_system_folders')
                    ->where('folder_id', $item_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($folder) {
                    $info['name'] = $folder->folder_name;

                    log_message('info', sprintf('✅ Folder found: %s', $folder->folder_name));

                    // 🔥 [NEW] สแกนไฟล์ทั้งหมดในโฟลเดอร์ (recursive)
                    log_message('info', '🔄 Scanning files recursively...');

                    $scan_result = $this->get_all_files_in_folder_recursive($item_id);

                    // เก็บข้อมูลสำหรับอัปเดต storage
                    $info['file_size'] = $scan_result['total_size'];
                    $info['files'] = $scan_result['files'];
                    $info['uploaders'] = $scan_result['uploaders'];
                    $info['file_count'] = count($scan_result['files']);

                    log_message('info', sprintf(
                        '📊 Folder scan result: %d files, %.2f MB total, %d uploaders',
                        $info['file_count'],
                        $info['file_size'] / 1024 / 1024,
                        count($info['uploaders'])
                    ));

                    // Log รายละเอียด uploader
                    foreach ($info['uploaders'] as $member_id => $size) {
                        log_message('info', sprintf(
                            '  👤 Member %d: %.2f MB',
                            $member_id,
                            $size / 1024 / 1024
                        ));
                    }
                }
            }

            return $info;

        } catch (Exception $e) {
            log_message('error', '💥 Get item info error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['name' => null, 'file_size' => 0, 'uploaded_by' => null];
        }
    }

    /**
     * 🔍 หาไฟล์ทั้งหมดในโฟลเดอร์ (ไม่ recursive)
     */
    private function get_files_in_folder($folder_id)
    {
        log_message('info', sprintf('🔍 Scanning files in folder: %s', $folder_id));

        $files = $this->db
            ->select('file_id, file_name, file_size, uploaded_by, folder_id')
            ->from('tbl_google_drive_system_files')
            ->where('folder_id', $folder_id)
            ->where('is_active', 1)
            ->get()
            ->result();

        log_message('info', sprintf('✅ Found %d files', count($files)));

        return $files;
    }

    /**
     * 📁 หาโฟลเดอร์ย่อยทั้งหมด (ไม่ recursive)
     */
    private function get_subfolders($parent_folder_id)
    {
        log_message('info', sprintf('🔍 Scanning subfolders in: %s', $parent_folder_id));

        $folders = $this->db
            ->select('folder_id, folder_name, parent_folder_id')
            ->from('tbl_google_drive_system_folders')
            ->where('parent_folder_id', $parent_folder_id)
            ->where('is_active', 1)
            ->get()
            ->result();

        log_message('info', sprintf('✅ Found %d subfolders', count($folders)));

        return $folders;
    }

    /**
     * 🔄 หาไฟล์ทั้งหมดแบบ Recursive
     * 
     * @return array [
     *   'files' => [file objects...],
     *   'total_size' => total bytes,
     *   'uploaders' => ['member_id' => total_size, ...]
     * ]
     */
    private function get_all_files_in_folder_recursive($folder_id)
    {
        log_message('info', sprintf('🔄 Starting recursive scan for folder: %s', $folder_id));

        $result = [
            'files' => [],
            'total_size' => 0,
            'uploaders' => []
        ];

        // 1. หาไฟล์ในโฟลเดอร์นี้
        $files = $this->get_files_in_folder($folder_id);

        foreach ($files as $file) {
            $result['files'][] = $file;
            $result['total_size'] += (int) $file->file_size;

            $uploader_id = (int) $file->uploaded_by;
            if (!isset($result['uploaders'][$uploader_id])) {
                $result['uploaders'][$uploader_id] = 0;
            }
            $result['uploaders'][$uploader_id] += (int) $file->file_size;

            log_message('info', sprintf(
                '  📄 File: %s (%.2f MB) by member %d',
                $file->file_name,
                $file->file_size / 1024 / 1024,
                $uploader_id
            ));
        }

        // 2. หาโฟลเดอร์ย่อย และสแกนแบบ recursive
        $subfolders = $this->get_subfolders($folder_id);

        foreach ($subfolders as $subfolder) {
            log_message('info', sprintf(
                '  📁 Scanning subfolder: %s',
                $subfolder->folder_name
            ));

            // Recursive call
            $sub_result = $this->get_all_files_in_folder_recursive($subfolder->folder_id);

            // Merge results
            $result['files'] = array_merge($result['files'], $sub_result['files']);
            $result['total_size'] += $sub_result['total_size'];

            foreach ($sub_result['uploaders'] as $member_id => $size) {
                if (!isset($result['uploaders'][$member_id])) {
                    $result['uploaders'][$member_id] = 0;
                }
                $result['uploaders'][$member_id] += $size;
            }
        }

        log_message('info', sprintf(
            '✅ Recursive scan complete: %d files, %.2f MB total',
            count($result['files']),
            $result['total_size'] / 1024 / 1024
        ));

        return $result;
    }



    /**
     * ✅ ตรวจสอบสิทธิ์การลบในโฟลเดอร์ (แบบเรียบง่าย)
     */
    private function check_delete_permission_in_folder($folder_id)
    {
        try {
            // Skip check สำหรับ root folder
            if (empty($folder_id) || $folder_id === 'root') {
                return true;
            }

            log_message('info', "Checking delete permission for member: {$this->member_id} in folder: {$folder_id}");

            // ตรวจสอบสิทธิ์จาก tbl_google_drive_member_folder_access
            $access_record = $this->db->select('access_type')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $this->member_id)
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->group_start()
                ->where('expires_at IS NULL')
                ->or_where('expires_at >', date('Y-m-d H:i:s'))
                ->group_end()
                ->get()
                ->row();

            if ($access_record) {
                $access_type = $access_record->access_type;
                log_message('info', "Found folder access: {$access_type} for member {$this->member_id}");

                // ✅ เช็คสิทธิ์การลบตาม access_type
                switch ($access_type) {
                    case 'read':
                        return false; // อ่านอย่างเดียว - ลบไม่ได้
                    case 'write':
                    case 'admin':
                    case 'owner':
                        return true; // เขียน, ผู้ดูแล, เจ้าของ - ลบได้
                    default:
                        return false;
                }
            }

            // เช็คสิทธิ์ระบบ (system admin, super admin)
            $system_access = $this->check_system_folder_access();
            if ($system_access) {
                log_message('info', "System delete permission granted for member {$this->member_id}");
                return true;
            }

            // ไม่มีสิทธิ์ลบ
            log_message('debug', "Delete permission denied for member {$this->member_id} in folder: {$folder_id}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Check delete permission in folder error: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * ✅ ดึง folder_id ของไฟล์
     */
    private function get_file_folder_id($file_id)
    {
        try {
            // ตรวจสอบจาก system files ก่อน
            if ($this->storage_mode === 'centralized') {
                $file = $this->db->select('folder_id')
                    ->from('tbl_google_drive_system_files')
                    ->where('file_id', $file_id)
                    ->get()
                    ->row();

                if ($file) {
                    return $file->folder_id;
                }
            } else {
                $file = $this->db->select('folder_id')
                    ->from('tbl_google_drive_sync')
                    ->where('file_id', $file_id)
                    ->get()
                    ->row();

                if ($file) {
                    return $file->folder_id;
                }
            }

            return 'root'; // default ถ้าไม่พบ

        } catch (Exception $e) {
            log_message('error', 'Get file folder ID error: ' . $e->getMessage());
            return 'root';
        }
    }


    /**
     * ✅ ดึงข้อมูลไฟล์จาก Google Drive API
     */
    private function get_google_drive_file_info($access_token, $file_id)
    {
        try {
            $ch = curl_init();

            $url = "https://www.googleapis.com/drive/v3/files/{$file_id}?fields=id,name,parents,mimeType";

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                return json_decode($response, true);
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Get Google Drive file info error: ' . $e->getMessage());
            return null;
        }
    }



    private function download_from_google_drive($access_token, $file_id, $file_info)
    {
        try {
            $ch = curl_init();

            // ใช้ Google Drive API สำหรับดาวน์โหลด
            $download_url = "https://www.googleapis.com/drive/v3/files/{$file_id}?alt=media";

            curl_setopt_array($ch, [
                CURLOPT_URL => $download_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 120, // 2 minutes for large files
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                return [
                    'success' => false,
                    'error' => 'การเชื่อมต่อล้มเหลว: ' . $curl_error
                ];
            }

            if ($http_code === 200) {
                return [
                    'success' => true,
                    'content' => $response
                ];
            } else {
                return [
                    'success' => false,
                    'error' => "HTTP {$http_code}"
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }



    private function output_file_download($content, $filename, $mime_type)
    {
        try {
            // ล้าง output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            // ตั้งค่า headers
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($content));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            // ส่งไฟล์
            echo $content;
            exit;

        } catch (Exception $e) {
            log_message('error', 'Output file download error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งไฟล์');
        }
    }


    /**
     * 🔗 ลบไฟล์/โฟลเดอร์จาก Google Drive (Production)
     */
    private function delete_google_drive_item($item_id, $access_token)
    {
        try {
            if (!$access_token || $access_token === 'trial_token') {
                return false;
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$item_id}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$access_token}"
                ],
                CURLOPT_TIMEOUT => 60
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $http_code === 200 || $http_code === 204;

        } catch (Exception $e) {
            log_message('error', 'Delete Google Drive item error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📷 Preview ไฟล์ (แก้ไขให้ MS Office แสดง popup download)
     */
    public function preview_file()
    {
        try {
            // ✅ ดึง file_id
            $file_id = $this->input->get('file_id');
            if (!$file_id) {
                show_404();
                return;
            }

            log_message('info', "Preview file request: {$file_id} by member: {$this->member_id}");

            // ✅ ตรวจสอบสิทธิ์การเข้าถึงไฟล์
            if (!$this->check_file_access_permission($file_id)) {
                log_message('warning', "Preview permission denied for file: {$file_id}, member: {$this->member_id}");

                http_response_code(403);
                header('Content-Type: text/html; charset=utf-8');
                echo '<h2>Access Denied</h2><p>คุณไม่มีสิทธิ์เข้าถึงไฟล์นี้</p>';
                return;
            }

            // ✅ ดึง Access Token
            $access_token = $this->get_access_token_simple();
            if (!$access_token) {
                log_message('error', "Cannot get access token for preview");

                http_response_code(500);
                header('Content-Type: text/html; charset=utf-8');
                echo '<h2>Error</h2><p>ไม่สามารถเชื่อมต่อ Google Drive ได้</p>';
                return;
            }

            // ✅ ดึงข้อมูลไฟล์
            $file_info = $this->get_google_drive_file_info($access_token, $file_id);
            if (!$file_info) {
                log_message('error', "Cannot get file info for preview: {$file_id}");
                show_404();
                return;
            }

            // 🔍 ตรวจสอบว่าเป็น MS Office file หรือไม่
            $file_name = $file_info['name'] ?? '';
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $office_extensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

            if (in_array($extension, $office_extensions)) {
                // ✅ MS Office files - แสดง popup download พร้อมตัวเลือก
                $this->show_office_file_popup($file_id, $file_info);
                return;
            }

            // ✅ ไฟล์ประเภทอื่น - ดาวน์โหลดและแสดงผลปกติ
            $download_result = $this->download_from_google_drive($access_token, $file_id, $file_info);

            if ($download_result['success']) {
                // ✅ ส่งไฟล์กลับไปยัง browser
                $mime_type = $file_info['mimeType'] ?? 'application/octet-stream';

                header('Content-Type: ' . $mime_type);
                header('Content-Length: ' . strlen($download_result['content']));
                header('Content-Disposition: inline; filename="' . $file_info['name'] . '"');
                header('Cache-Control: public, max-age=3600'); // Cache 1 ชั่วโมง
                header('X-Content-Type-Options: nosniff');

                echo $download_result['content'];
                exit;
            } else {
                log_message('error', "Preview failed for file: {$file_id}, error: " . $download_result['error']);

                http_response_code(500);
                header('Content-Type: text/html; charset=utf-8');
                echo '<h2>Error</h2><p>ไม่สามารถโหลดไฟล์ได้: ' . htmlspecialchars($download_result['error']) . '</p>';
            }

        } catch (Exception $e) {
            log_message('error', 'Preview file error: ' . $e->getMessage());

            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
            echo '<h2>Error</h2><p>เกิดข้อผิดพลาดในการแสดงไฟล์</p>';
        }
    }

    /**
     * 📋 แสดง popup สำหรับ MS Office files
     */
    private function show_office_file_popup($file_id, $file_info)
    {
        $file_name = $file_info['name'] ?? 'Unknown';
        $web_view_link = $file_info['webViewLink'] ?? '';

        // ✅ สร้าง HTML popup
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>เปิดไฟล์ Office</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body {
                    font-family: 'Noto Sans Thai', sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
            </style>
        </head>

        <body>
            <script>
                // 📋 แสดง SweetAlert2 popup
                Swal.fire({
                    title: '📄 เปิดไฟล์ Office',
                    html: `
                    <div style="text-align: left; padding: 20px;">
                        <p style="margin-bottom: 20px;">
                            <strong>ไฟล์:</strong> <?php echo htmlspecialchars($file_name); ?>
                        </p>
                        <p style="margin-bottom: 15px; color: #666;">
                            เลือกวิธีที่คุณต้องการเปิดไฟล์:
                        </p>
                    </div>
                `,
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: '📥 ดาวน์โหลด',
                    denyButtonText: '👁️ ดูในหน้าใหม่',
                    cancelButtonText: '❌ ยกเลิก',
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    width: 600,
                    padding: '2em',
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ✅ ดาวน์โหลดไฟล์
                        window.location.href = '<?php echo base_url('google_drive_files/download_file?file_id=' . $file_id); ?>';

                        // แสดงข้อความสำเร็จ
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'กำลังดาวน์โหลด...',
                                text: 'ไฟล์กำลังถูกดาวน์โหลดไปยังเครื่องของคุณ',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }, 500);
                    } else if (result.isDenied) {
                        // ✅ เปิดใน Google Drive (หน้าใหม่)
                        window.open('<?php echo $web_view_link; ?>', '_blank');

                        // แสดงข้อความสำเร็จ
                        Swal.fire({
                            icon: 'success',
                            title: 'เปิดในหน้าใหม่แล้ว!',
                            text: 'ไฟล์ถูกเปิดใน Google Drive',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // ยกเลิก - ปิดหน้าต่าง
                        window.close();
                    }
                });
            </script>
        </body>

        </html>
        <?php
    }

    // ========================================
    // Additional Helper Functions
    // ========================================

    /**
     * 📥 ดาวน์โหลดไฟล์ (With Permission Check)
     */
    public function download_file()
    {
        try {
            $file_id = $this->input->get('file_id');
            if (!$file_id) {
                show_404();
                return;
            }

            log_message('info', "Download file request: {$file_id} by member: {$this->member_id}");

            // ตรวจสอบสิทธิ์การดาวน์โหลด
            if (!$this->check_download_permission($file_id)) {
                log_message('debug', "Download permission denied for file: {$file_id}, member: {$this->member_id}");

                $this->session->set_flashdata('error', 'คุณไม่มีสิทธิ์ดาวน์โหลดไฟล์นี้');
                redirect('google_drive_files');
                return;
            }

            // ดึง System Access Token
            $access_token = $this->get_system_access_token();
            if (!$access_token) {
                log_message('error', "Cannot get system access token for download");

                $this->session->set_flashdata('error', 'ไม่สามารถเชื่อมต่อ Google Drive ได้');
                redirect('google_drive_files');
                return;
            }

            // ดึงข้อมูลไฟล์จาก Google Drive API
            $file_info = $this->get_google_drive_file_info($access_token, $file_id);
            if (!$file_info) {
                log_message('error', "Cannot get file info for: {$file_id}");

                $this->session->set_flashdata('error', 'ไม่พบไฟล์ที่ต้องการดาวน์โหลด');
                redirect('google_drive_files');
                return;
            }

            // ดาวน์โหลดไฟล์จาก Google Drive
            $download_result = $this->download_from_google_drive($access_token, $file_id, $file_info);

            if ($download_result['success']) {
                // บันทึก log การดาวน์โหลด
                $this->log_download_activity($file_id, $file_info['name'], true);

                // ส่งไฟล์ให้ browser
                $this->output_file_download($download_result['content'], $file_info['name'], $file_info['mimeType']);
            } else {
                log_message('error', "Download failed for file: {$file_id}, error: " . $download_result['error']);

                $this->session->set_flashdata('error', 'ไม่สามารถดาวน์โหลดไฟล์ได้: ' . $download_result['error']);
                redirect('google_drive_files');
            }

        } catch (Exception $e) {
            log_message('error', 'Download file error: ' . $e->getMessage());

            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการดาวน์โหลด');
            redirect('google_drive_files');
        }
    }

    /**
     * 🔐 ตรวจสอบสิทธิ์การดาวน์โหลด
     */
    private function check_download_permission($file_id)
    {
        try {
            // หาโฟลเดอร์ที่ไฟล์อยู่
            $folder_id = $this->get_file_folder_id($file_id);

            if (!$folder_id) {
                return false;
            }

            // ใช้ function เดียวกับ check_file_access
            return $this->check_folder_access_permission($folder_id);

        } catch (Exception $e) {
            log_message('error', 'Check download permission error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * 📝 บันทึก log การดาวน์โหลด
     */
    private function log_download_activity($file_id)
    {
        try {
            $member_id = $this->session->userdata('m_id');
            $timestamp = date('Y-m-d H:i:s');

            // บันทึกลง tbl_google_drive_logs
            if ($this->db->table_exists('tbl_google_drive_logs')) {
                $log_data = [
                    'member_id' => $member_id,
                    'action_type' => 'download',
                    'action_description' => "ดาวน์โหลดไฟล์ ID: {$file_id}",
                    'item_id' => $file_id,
                    'item_type' => 'file',
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent(),
                    'created_at' => $timestamp
                ];
                $this->db->insert('tbl_google_drive_logs', $log_data);
            }

            // บันทึกลง tbl_google_drive_activity_logs
            if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
                $activity_data = [
                    'member_id' => $member_id,
                    'action_type' => 'download',
                    'action_description' => "ดาวน์โหลดไฟล์ ID: {$file_id}",
                    'item_id' => $file_id,
                    'item_type' => 'file',
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent(),
                    'created_at' => $timestamp
                ];
                $this->db->insert('tbl_google_drive_activity_logs', $activity_data);
            }

        } catch (Exception $e) {
            log_message('error', 'Log download activity error: ' . $e->getMessage());
        }
    }

    /**
     * 🔗 สร้างลิงก์แชร์ (AJAX) (With Permission Check)
     */
    public function create_share_link()
    {
        try {
            if (ob_get_level()) {
                ob_clean();
            }

            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            $item_id = $this->input->post('item_id');
            $item_type = $this->input->post('item_type');
            $permission = $this->input->post('permission', true) ?: 'reader';
            $access = $this->input->post('access', true) ?: 'restricted';

            if (!$item_id || !$item_type) {
                $this->output_json_error('ข้อมูลไม่ครบถ้วน');
                return;
            }



            // สร้างลิงก์แชร์ทันที
            $access_token = $this->get_access_token();
            $share_result = $this->create_google_drive_share_link($item_id, $permission, $access, $access_token);

            if ($share_result && $share_result['success']) {
                $this->output_json_success($share_result['data'], 'สร้างลิงก์แชร์สำเร็จ');
            } else {
                $this->output_json_error($share_result['error'] ?? 'ไม่สามารถสร้างลิงก์แชร์ได้');
            }

        } catch (Exception $e) {
            log_message('error', 'Create share link error: ' . $e->getMessage());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }


    /**
     * ✉️ แชร์กับอีเมล (Enhanced with Permission Check)
     */
    /**
     * ✉️ แชร์กับอีเมล - ไม่เช็ค Permission
     */
    public function share_with_email()
    {
        // บังคับให้เป็น JSON response ทันที
        header('Content-Type: application/json');

        try {
            // ล้าง output buffer
            if (ob_get_level()) {
                ob_clean();
            }

            // ตรวจสอบพื้นฐาน
            if (!$this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Not AJAX request']);
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Not POST method']);
                exit;
            }

            // รับข้อมูล
            $item_id = $this->input->post('item_id');
            $item_type = $this->input->post('item_type');
            $email = trim($this->input->post('email'));
            $permission = $this->input->post('permission') ?: 'reader';
            $message = trim($this->input->post('message'));

            // Log สำหรับ debug
            log_message('info', "share_with_email called: item_id={$item_id}, email={$email}, permission={$permission}");

            // ตรวจสอบข้อมูลพื้นฐาน
            if (empty($item_id)) {
                echo json_encode(['success' => false, 'message' => 'item_id is required']);
                exit;
            }

            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'email is required']);
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                exit;
            }

            // ✅ ปรับปรุงการดึง Access Token
            $access_token = $this->get_valid_access_token();

            if (!$access_token) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถดึง Access Token ที่ถูกต้องได้']);
                exit;
            }

            // ✅ ทดสอบ Token ก่อนใช้งาน
            $token_test = $this->test_google_api_token($access_token);
            if (!$token_test['valid']) {
                echo json_encode(['success' => false, 'message' => 'Access Token ไม่ถูกต้อง: ' . $token_test['error']]);
                exit;
            }

            // เรียก Google API
            $result = $this->call_google_share_api($item_id, $email, $permission, $message, $access_token);

            // บันทึก log ถ้าสำเร็จ
            if ($result['success']) {
                $this->log_share_activity_enhanced($item_id, $item_type, $email, $permission, $message);
            }

            echo json_encode($result);
            exit;

        } catch (Exception $e) {
            // Log error
            log_message('error', 'share_with_email error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ]);
            exit;
        }
    }


    private function get_valid_access_token()
    {
        try {
            // ตรวจสอบตาราง system storage
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                log_message('error', 'Google Drive system storage table not found');
                return false;
            }

            // ดึงข้อมูล storage ที่ active
            $this->db->where('is_active', 1);
            $this->db->order_by('id', 'ASC');
            $this->db->limit(1);
            $query = $this->db->get('tbl_google_drive_system_storage');

            if ($query->num_rows() === 0) {
                log_message('error', 'No active Google Drive system storage found');
                return false;
            }

            $storage = $query->row();

            // ✅ ตรวจสอบและแก้ไข format ของ access token
            if (empty($storage->google_access_token)) {
                log_message('error', 'Google access token is empty');
                return false;
            }

            // ✅ ถ้า token เป็น JSON ให้ decode ก่อน
            $access_token = $storage->google_access_token;
            if ($this->isJson($access_token)) {
                $token_data = json_decode($access_token, true);
                if (isset($token_data['access_token'])) {
                    $access_token = $token_data['access_token'];
                    log_message('info', 'Extracted access_token from JSON format');
                } else {
                    log_message('error', 'JSON token format invalid - no access_token field');
                    return false;
                }
            }

            // ✅ ตรวจสอบว่า token หมดอายุหรือไม่
            $token_expired = false;
            if (!empty($storage->google_token_expires)) {
                $expires_at = strtotime($storage->google_token_expires);
                if ($expires_at && $expires_at <= time() + 600) { // หมดอายุใน 10 นาที
                    $token_expired = true;
                    log_message('info', 'Access token will expire soon, attempting refresh...');
                }
            }

            // ✅ ถ้า token หมดอายุและมี refresh token ให้ refresh
            if ($token_expired && !empty($storage->google_refresh_token)) {
                $refreshed_token = $this->refresh_google_access_token($storage);
                if ($refreshed_token) {
                    return $refreshed_token;
                } else {
                    log_message('error', 'Failed to refresh access token');
                }
            }

            // ✅ ตรวจสอบว่า token เป็น string ที่ถูกต้อง
            if (!is_string($access_token) || strlen($access_token) < 10) {
                log_message('error', 'Invalid access token format: ' . gettype($access_token));
                return false;
            }

            log_message('info', 'Valid access token retrieved: ' . substr($access_token, 0, 20) . '...');
            return $access_token;

        } catch (Exception $e) {
            log_message('error', 'get_valid_access_token error: ' . $e->getMessage());
            return false;
        }
    }



    private function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }



    /**
     * 🧪 ทดสอบ Google API Token
     */
    private function test_google_api_token($access_token)
    {
        try {
            // ✅ ตรวจสอบ token format ก่อน
            if (!is_string($access_token) || empty($access_token)) {
                return ['valid' => false, 'error' => 'Token is not a valid string'];
            }

            if (strlen($access_token) < 10) {
                return ['valid' => false, 'error' => 'Token too short'];
            }

            // ทดสอบ token โดยเรียก API ง่ายๆ
            $url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . urlencode($access_token);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                return ['valid' => false, 'error' => 'cURL Error: ' . $curl_error];
            }

            log_message('info', "Token validation response: HTTP {$http_code}");
            log_message('info', "Token validation body: " . substr($response, 0, 300));

            if ($http_code === 200) {
                $token_info = json_decode($response, true);

                // ตรวจสอบ scope ที่จำเป็น
                $token_scopes = explode(' ', $token_info['scope'] ?? '');
                $has_drive_scope = false;

                foreach ($token_scopes as $scope) {
                    if (strpos($scope, 'drive') !== false) {
                        $has_drive_scope = true;
                        break;
                    }
                }

                if (!$has_drive_scope) {
                    return ['valid' => false, 'error' => 'Token ไม่มีสิทธิ์ Google Drive'];
                }

                return ['valid' => true, 'token_info' => $token_info];
            } else {
                $error_data = json_decode($response, true);
                $error_msg = 'Invalid Value';

                if (isset($error_data['error_description'])) {
                    $error_msg = $error_data['error_description'];
                } elseif (isset($error_data['error'])) {
                    $error_msg = $error_data['error'];
                }

                return ['valid' => false, 'error' => $error_msg];
            }

        } catch (Exception $e) {
            return ['valid' => false, 'error' => 'Exception: ' . $e->getMessage()];
        }
    }



    private function refresh_google_access_token($storage)
    {
        try {
            if (empty($storage->google_refresh_token)) {
                log_message('error', 'No refresh token available');
                return false;
            }

            // ✅ ดึงค่า Client ID และ Client Secret
            $google_client_id = '';
            $google_client_secret = '';

            // วิธีที่ 1: จาก config
            if ($this->config->item('google_client_id')) {
                $google_client_id = $this->config->item('google_client_id');
                $google_client_secret = $this->config->item('google_client_secret');
            }
            // วิธีที่ 2: จาก database storage
            elseif (!empty($storage->google_client_id)) {
                $google_client_id = $storage->google_client_id;
                $google_client_secret = $storage->google_client_secret;
            }
            // วิธีที่ 3: ค่าคงที่ (ถ้ามี)
            else {
                // ใส่ค่าจริงของคุณที่นี่
                $google_client_id = 'YOUR_GOOGLE_CLIENT_ID';
                $google_client_secret = 'YOUR_GOOGLE_CLIENT_SECRET';
            }

            if (empty($google_client_id) || empty($google_client_secret)) {
                log_message('error', 'Google Client ID or Secret not found');
                return false;
            }

            // ✅ ตรวจสอบ refresh token format
            $refresh_token = $storage->google_refresh_token;
            if ($this->isJson($refresh_token)) {
                $refresh_data_obj = json_decode($refresh_token, true);
                if (isset($refresh_data_obj['refresh_token'])) {
                    $refresh_token = $refresh_data_obj['refresh_token'];
                }
            }

            $refresh_data = [
                'client_id' => $google_client_id,
                'client_secret' => $google_client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ];

            log_message('info', 'Attempting to refresh token with client_id: ' . substr($google_client_id, 0, 20) . '...');

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://oauth2.googleapis.com/token',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($refresh_data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            log_message('info', "Refresh token response: HTTP {$http_code}");
            log_message('info', "Response body: " . substr($response, 0, 500));

            if ($curl_error) {
                log_message('error', 'Refresh token cURL error: ' . $curl_error);
                return false;
            }

            if ($http_code === 200) {
                $token_data = json_decode($response, true);

                if (isset($token_data['access_token'])) {
                    // ✅ บันทึก access token เป็น string (ไม่ใช่ JSON)
                    $new_access_token = $token_data['access_token'];
                    $expires_in = $token_data['expires_in'] ?? 3600;

                    $update_data = [
                        'google_access_token' => $new_access_token, // เก็บเป็น string ธรรมดา
                        'google_token_expires' => date('Y-m-d H:i:s', time() + $expires_in),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->where('id', $storage->id);
                    $this->db->update('tbl_google_drive_system_storage', $update_data);

                    log_message('info', 'Access token refreshed successfully');
                    return $new_access_token;
                } else {
                    log_message('error', 'Refresh response missing access_token: ' . $response);
                }
            } else {
                $error_data = json_decode($response, true);
                $error_msg = isset($error_data['error_description']) ?
                    $error_data['error_description'] :
                    "HTTP {$http_code}";
                log_message('error', "Refresh token failed: {$error_msg}");
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'refresh_google_access_token error: ' . $e->getMessage());
            return false;
        }
    }




    /**
     * 📝 Enhanced Log Share Activity - บันทึกครบทุกตาราง (No Custom Table)
     */
    private function log_share_activity_enhanced($item_id, $item_type, $email, $permission, $message)
    {
        try {
            $member_id = $this->member_id ?? $this->session->userdata('m_id') ?? 0;
            $timestamp = date('Y-m-d H:i:s');
            $ip_address = $this->input->ip_address();
            $user_agent = $this->input->user_agent();

            $logged_tables = [];

            // 1. บันทึกลง tbl_google_drive_logs (ตารางหลัก)
            if ($this->db->table_exists('tbl_google_drive_logs')) {
                $log_data = [
                    'member_id' => $member_id,
                    'action_type' => 'share',
                    'action_description' => "แชร์ {$item_type} '{$item_id}' กับ {$email} (สิทธิ์: {$permission})",
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'target_email' => $email,
                    'status' => 'success',
                    'additional_data' => json_encode([
                        'permission' => $permission,
                        'message' => $message,
                        'share_method' => 'email'
                    ]),
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'created_at' => $timestamp
                ];

                if ($this->db->insert('tbl_google_drive_logs', $log_data)) {
                    $logged_tables[] = 'tbl_google_drive_logs';
                }
            }

            // 2. บันทึกลง tbl_google_drive_activity_logs
            if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
                $activity_data = [
                    'member_id' => $member_id,
                    'action_type' => 'share_with_email',
                    'action_description' => "แชร์ {$item_type} ID: {$item_id} กับ {$email}",
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'created_at' => $timestamp
                ];

                if ($this->db->insert('tbl_google_drive_activity_logs', $activity_data)) {
                    $logged_tables[] = 'tbl_google_drive_activity_logs';
                }
            }

            // 3. บันทึกลง tbl_google_drive_sharing
            if ($this->db->table_exists('tbl_google_drive_sharing')) {
                $sharing_data = [
                    'folder_id' => $item_type === 'folder' ? $item_id : null,
                    'shared_by' => $member_id,
                    'shared_to_email' => $email,
                    'permission_level' => $permission,
                    'shared_at' => $timestamp,
                    'is_active' => 1
                ];

                if ($this->db->insert('tbl_google_drive_sharing', $sharing_data)) {
                    $logged_tables[] = 'tbl_google_drive_sharing';
                }
            }

            // 4. บันทึกลง tbl_google_drive_file_activities (ถ้าเป็นไฟล์)
            if ($item_type === 'file' && $this->db->table_exists('tbl_google_drive_file_activities')) {
                $file_activity_data = [
                    'google_file_id' => $item_id,
                    'user_id' => $member_id,
                    'user_name' => $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'),
                    'user_email' => $this->session->userdata('m_email'),
                    'action_type' => 'share',
                    'file_name' => 'Shared File', // อาจต้องดึงชื่อไฟล์จริง
                    'target_google_email' => $email,
                    'storage_mode' => 'system',
                    'details' => json_encode([
                        'permission' => $permission,
                        'message' => $message,
                        'share_method' => 'email'
                    ]),
                    'created_at' => $timestamp
                ];

                if ($this->db->insert('tbl_google_drive_file_activities', $file_activity_data)) {
                    $logged_tables[] = 'tbl_google_drive_file_activities';
                }
            }

            // Log สรุป
            if (!empty($logged_tables)) {
                log_message('info', "✅ Share activity logged to " . count($logged_tables) . " tables: " . implode(', ', $logged_tables));
            } else {
                log_message('debug', "⚠️ No tables were available for logging share activity");
            }

        } catch (Exception $e) {
            log_message('error', 'Log share activity enhanced error: ' . $e->getMessage());
        }
    }

    /**
     * 📞 เรียก Google API สำหรับแชร์ไฟล์ - ไม่เปลี่ยน
     */
    private function call_google_share_api($file_id, $email, $permission, $message, $access_token)
    {
        try {
            log_message('info', "Calling Google Share API for file: {$file_id} to {$email}");

            // ✅ ปรับปรุงข้อมูล permission
            $permission_data = [
                'role' => $permission,
                'type' => 'user',
                'emailAddress' => $email
            ];

            // ✅ ปรับปรุง URL และ parameters
            $url = "https://www.googleapis.com/drive/v3/files/{$file_id}/permissions";
            $params = [
                'sendNotificationEmail' => 'true',
                'supportsAllDrives' => 'true' // รองรับ Shared Drives
            ];

            if (!empty($message)) {
                $params['emailMessage'] = $message;
            }

            $url .= '?' . http_build_query($params);

            // ✅ ปรับปรุง cURL options
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($permission_data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60, // เพิ่ม timeout
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'User-Agent: GoogleDriveSystem/1.0' // เพิ่ม User-Agent
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);

            // ✅ เพิ่ม debug info
            $curl_info = curl_getinfo($ch);
            log_message('info', "cURL Info: " . json_encode([
                'url' => $curl_info['url'],
                'http_code' => $curl_info['http_code'],
                'total_time' => $curl_info['total_time']
            ]));

            curl_close($ch);

            // Log response for debugging
            log_message('info', "Google API Response - HTTP Code: {$http_code}");
            if ($response) {
                log_message('info', "Google API Response Body: " . substr($response, 0, 1000));
            }
            if ($curl_error) {
                log_message('error', "cURL Error: {$curl_error}");
            }

            if ($curl_error) {
                return [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' . $curl_error
                ];
            }

            if ($http_code === 200 || $http_code === 201) {
                $permission_result = json_decode($response, true);

                return [
                    'success' => true,
                    'message' => "แชร์กับ {$email} สำเร็จ",
                    'data' => [
                        'email' => $email,
                        'permission' => $permission,
                        'http_code' => $http_code,
                        'item_id' => $file_id,
                        'permission_id' => $permission_result['id'] ?? null
                    ]
                ];
            } else {
                $error_response = json_decode($response, true);
                $error_msg = "HTTP {$http_code}";

                if ($error_response && isset($error_response['error']['message'])) {
                    $error_msg = $error_response['error']['message'];
                } elseif ($error_response && isset($error_response['error'])) {
                    $error_msg = is_array($error_response['error']) ?
                        json_encode($error_response['error']) :
                        $error_response['error'];
                }

                // ✅ เพิ่ม specific error handling
                if (strpos($error_msg, 'invalid authentication') !== false) {
                    $error_msg = 'Access Token ไม่ถูกต้องหรือหมดอายุ - กรุณาเชื่อมต่อ Google Drive ใหม่';
                } elseif (strpos($error_msg, 'insufficient permission') !== false) {
                    $error_msg = 'ไม่มีสิทธิ์ในการแชร์ไฟล์นี้';
                } elseif (strpos($error_msg, 'File not found') !== false) {
                    $error_msg = 'ไม่พบไฟล์ในระบบ Google Drive';
                }

                return [
                    'success' => false,
                    'message' => $error_msg,
                    'debug' => [
                        'http_code' => $http_code,
                        'response' => $response ? substr($response, 0, 500) : 'No response',
                        'error_details' => $error_response
                    ]
                ];
            }

        } catch (Exception $e) {
            log_message('error', "call_google_share_api exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }


    private function refresh_access_token($storage)
    {
        try {
            if (empty($storage->google_refresh_token)) {
                return ['success' => false, 'message' => 'ไม่พบ Refresh Token'];
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://oauth2.googleapis.com/token',
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'client_id' => $this->config->item('google_client_id'),
                    'client_secret' => $this->config->item('google_client_secret'),
                    'refresh_token' => $storage->google_refresh_token,
                    'grant_type' => 'refresh_token'
                ])
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $data = json_decode($response, true);
                if (isset($data['access_token'])) {
                    // อัปเดต token ในฐานข้อมูล
                    $update_data = [
                        'google_access_token' => $data['access_token'],
                        'token_expires_at' => date('Y-m-d H:i:s', time() + $data['expires_in']),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->where('id', $storage->id);
                    $this->db->update('tbl_google_drive_storage', $update_data);

                    return [
                        'success' => true,
                        'access_token' => $data['access_token']
                    ];
                }
            }

            return ['success' => false, 'message' => 'ไม่สามารถต่ออายุ Token ได้'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }




    private function get_google_drive_file_details($file_id, $access_token)
    {
        try {
            $url = "https://www.googleapis.com/drive/v3/files/{$file_id}?fields=id,name,webViewLink,webContentLink";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                return json_decode($response, true);
            }

            return [];

        } catch (Exception $e) {
            log_message('error', "get_google_drive_file_details error: " . $e->getMessage());
            return [];
        }
    }



    /**
     * 🔗 สร้างลิงก์แชร์ Google Drive - ปรับปรุงแล้ว
     */
    private function create_google_drive_share_link($item_id, $permission, $access, $access_token)
    {
        try {
            if (!$access_token || $access_token === 'trial_token') {
                return [
                    'success' => false,
                    'error' => 'Invalid access token'
                ];
            }

            // สร้าง permission
            $permission_data = [
                'role' => $permission, // reader, writer, commenter
                'type' => $access === 'anyone' ? 'anyone' : 'anyone' // ใช้ anyone สำหรับ public link
            ];

            // ถ้าเป็น restricted access ให้เปลี่ยนเป็น anyone แทน
            // เพราะ Google Drive ต้องการ 'anyone' สำหรับ shareable link
            if ($access === 'restricted') {
                $permission_data['type'] = 'anyone';
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$item_id}/permissions",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$access_token}",
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS => json_encode($permission_data),
                CURLOPT_TIMEOUT => 60
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                return [
                    'success' => false,
                    'error' => 'cURL Error: ' . $curl_error
                ];
            }

            if ($http_code === 200 || $http_code === 201) {
                // ดึงข้อมูลไฟล์เพื่อได้ webViewLink
                $file_info = $this->get_google_drive_file_details($item_id, $access_token);

                // สร้าง shareable link
                $share_link = isset($file_info['webViewLink']) ?
                    $file_info['webViewLink'] :
                    "https://drive.google.com/file/d/{$item_id}/view?usp=sharing";

                return [
                    'success' => true,
                    'data' => [
                        'webViewLink' => $share_link, // ใช้ webViewLink เพื่อให้เข้ากันได้กับ JavaScript
                        'share_link' => $share_link,
                        'permission' => $permission,
                        'access' => $access,
                        'file_id' => $item_id
                    ]
                ];
            } else {
                $error_response = json_decode($response, true);
                $error_msg = isset($error_response['error']['message']) ?
                    $error_response['error']['message'] :
                    "HTTP {$http_code}";

                return [
                    'success' => false,
                    'error' => $error_msg
                ];
            }

        } catch (Exception $e) {
            log_message('error', "create_google_drive_share_link exception: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }


    // ==========================================
    // DATABASE & STORAGE MANAGEMENT
    // ==========================================

    /**
     * 💾 บันทึกข้อมูลไฟล์ที่อัปโหลด
     */
    private function save_uploaded_file_info($file_id, $file_data, $folder_id)
    {
        try {
            if ($this->storage_mode === 'centralized') {
                // บันทึกลง system files table
                if ($this->db->table_exists('tbl_google_drive_system_files')) {
                    $data = [
                        'file_id' => $file_id,
                        'file_name' => $file_data['name'],
                        'file_size' => $file_data['size'],
                        'file_type' => $file_data['type'],
                        'folder_id' => $folder_id,
                        'uploaded_by' => $this->member_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_google_drive_system_files', $data);
                }
            } else {
                // บันทึกลง user files table (ถ้ามี)
                if ($this->db->table_exists('tbl_google_drive_user_files')) {
                    $data = [
                        'file_id' => $file_id,
                        'file_name' => $file_data['name'],
                        'file_size' => $file_data['size'],
                        'file_type' => $file_data['type'],
                        'folder_id' => $folder_id,
                        'member_id' => $this->member_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_google_drive_user_files', $data);
                }
            }

            // Log activity
            $this->log_drive_activity('upload_file', [
                'file_id' => $file_id,
                'file_name' => $file_data['name'],
                'file_size' => $file_data['size'],
                'folder_id' => $folder_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Save uploaded file info error: ' . $e->getMessage());
        }
    }



    /**
     * 💾 บันทึกข้อมูลโฟลเดอร์ที่สร้าง (รองรับ trial mode)
     */
    private function save_created_folder_info($folder_id, $folder_name, $parent_id, $is_trial = false)
    {
        try {
            $folder_type = $is_trial ? 'trial' : 'user';

            if ($this->storage_mode === 'centralized') {
                // บันทึกลง system folders table
                if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                    $data = [
                        'folder_id' => $folder_id,
                        'folder_name' => $folder_name,
                        'parent_folder_id' => $parent_id,
                        'created_by' => $this->member_id,
                        'folder_type' => $folder_type,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_google_drive_system_folders', $data);
                }
            } else {
                // บันทึกลง user folders table
                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $data = [
                        'folder_id' => $folder_id,
                        'folder_name' => $folder_name,
                        'parent_folder_id' => $parent_id,
                        'member_id' => $this->member_id,
                        'folder_type' => $folder_type,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_google_drive_folders', $data);
                }
            }

            // Log activity
            $activity_type = $is_trial ? 'trial_create_folder' : 'create_folder';
            $this->log_drive_activity($activity_type, [
                'folder_id' => $folder_id,
                'folder_name' => $folder_name,
                'parent_id' => $parent_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Save created folder info error: ' . $e->getMessage());
        }
    }

    /**
     * 📊 อัปเดต Trial Quota (ใช้พื้นที่จริงแต่จำกัดที่ 1GB)
     */
    private function update_trial_quota($file_size)
    {
        try {
            $current_used = $this->db->select('storage_quota_used')
                ->from('tbl_member')
                ->where('m_id', $this->member_id)
                ->get()
                ->row()
                ->storage_quota_used ?: 0;

            $new_used = $current_used + $file_size;

            // อัปเดต quota และตั้ง limit เป็น 1GB สำหรับ trial
            $this->db->where('m_id', $this->member_id)
                ->update('tbl_member', [
                    'storage_quota_used' => $new_used,
                    'storage_quota_limit' => $this->trial_storage_limit, // 1GB
                    'last_storage_access' => date('Y-m-d H:i:s')
                ]);

        } catch (Exception $e) {
            log_message('error', 'Update trial quota error: ' . $e->getMessage());
        }
    }

    /**
     * 🔍 ตรวจสอบ Storage Limit (รองรับ trial mode)
     */
    private function check_storage_limit($additional_size)
    {
        try {
            $member = $this->db->select('storage_quota_used, storage_quota_limit')
                ->from('tbl_member')
                ->where('m_id', $this->member_id)
                ->get()
                ->row();

            if (!$member) {
                return false;
            }

            $current_used = $member->storage_quota_used ?: 0;

            // ใช้ trial limit ถ้าอยู่ใน trial mode
            if ($this->is_trial_mode) {
                $limit = $this->trial_storage_limit; // 5GB
            } else {
                $limit = $member->storage_quota_limit ?: (5 * 1024 * 1024 * 1024); // 5GB default
            }

            return ($current_used + $additional_size) <= $limit;

        } catch (Exception $e) {
            log_message('error', 'Check storage limit error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 💾 ลบข้อมูลไฟล์จากฐานข้อมูล
     */
    private function remove_file_from_database($item_id)
    {
        try {
            if ($this->storage_mode === 'centralized') {
                if ($this->db->table_exists('tbl_google_drive_system_files')) {
                    $this->db->where('file_id', $item_id)->delete('tbl_google_drive_system_files');
                }
            } else {
                if ($this->db->table_exists('tbl_google_drive_user_files')) {
                    $this->db->where('file_id', $item_id)->delete('tbl_google_drive_user_files');
                }
            }

            // Log activity
            $this->log_drive_activity('delete_file', ['file_id' => $item_id]);

        } catch (Exception $e) {
            log_message('error', 'Remove file from database error: ' . $e->getMessage());
        }
    }

    /**
     * 💾 ลบข้อมูลโฟลเดอร์จากฐานข้อมูล
     */
    private function remove_folder_from_database($item_id)
    {
        try {
            if ($this->storage_mode === 'centralized') {
                if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                    $this->db->where('folder_id', $item_id)->delete('tbl_google_drive_system_folders');
                }
            } else {
                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $this->db->where('folder_id', $item_id)->delete('tbl_google_drive_folders');
                }
            }

            // Log activity
            $this->log_drive_activity('delete_folder', ['folder_id' => $item_id]);

        } catch (Exception $e) {
            log_message('error', 'Remove folder from database error: ' . $e->getMessage());
        }
    }

    /**
     * 📝 บันทึก activity log
     */
    private function log_drive_activity($action_type, $action_info = null)
    {
        try {
            // ตรวจสอบว่าตารางมีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_google_drive_activity_logs')) {
                log_message('debug', 'Table tbl_google_drive_activity_logs not found');
                return false;
            }

            // เตรียมข้อมูล log พื้นฐาน
            $log_data = [
                'member_id' => $this->member_id ?: 0,
                'action_type' => $action_type,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => substr($this->input->user_agent(), 0, 500), // จำกัดความยาว
                'created_at' => date('Y-m-d H:i:s')
            ];

            // สร้าง action_description จากข้อมูลที่ส่งมา
            $description = $action_type;
            if ($action_info) {
                if (isset($action_info['file_name'])) {
                    $description .= ': ' . $action_info['file_name'];
                }
                if (isset($action_info['file_size'])) {
                    $size_mb = round($action_info['file_size'] / (1024 * 1024), 2);
                    $description .= " ({$size_mb}MB)";
                }
                if (isset($action_info['folder_id']) && $action_info['folder_id'] !== 'root') {
                    $description .= " [Folder: {$action_info['folder_id']}]";
                }
            }
            $log_data['action_description'] = $description;

            // ตรวจสอบและเพิ่ม columns เพิ่มเติมถ้ามี
            $columns = $this->db->list_fields('tbl_google_drive_activity_logs');

            if (in_array('folder_id', $columns) && isset($action_info['folder_id'])) {
                $log_data['folder_id'] = $action_info['folder_id'];
            }

            if (in_array('file_id', $columns) && isset($action_info['file_id'])) {
                $log_data['file_id'] = $action_info['file_id'];
            }

            if (in_array('item_id', $columns) && isset($action_info['file_id'])) {
                $log_data['item_id'] = $action_info['file_id'];
            }

            if (in_array('item_type', $columns)) {
                if (isset($action_info['file_id'])) {
                    $log_data['item_type'] = 'file';
                } elseif (isset($action_info['folder_id'])) {
                    $log_data['item_type'] = 'folder';
                }
            }

            // บันทึกลงฐานข้อมูล
            $this->db->insert('tbl_google_drive_activity_logs', $log_data);

            log_message('info', "Drive activity logged: {$action_type} - {$description}");
            return true;

        } catch (Exception $e) {
            log_message('error', 'Log drive activity error: ' . $e->getMessage());
            // ไม่ throw exception เพื่อไม่ให้การทำงานหลักล้มเหลว
            return false;
        }
    }


    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * 🔧 ดึงโหมด Storage
     */
    private function get_storage_mode()
    {
        try {
            // ดึงจากการตั้งค่าระบบ
            return $this->get_system_setting('system_storage_mode', 'user_based');

        } catch (Exception $e) {
            return 'user_based'; // default
        }
    }

    /**
     * 🔧 ดึงข้อมูล System Storage (Dynamic from Settings + Real Calculation)
     * ✅ ใช้ get_system_setting() และ format_bytes() ที่มีอยู่แล้ว
     * ✅ คำนวณจากไฟล์จริงใน tbl_google_drive_system_files
     * ✅ รองรับ Trial Mode และ Full Version
     * ✅ ยึดค่าจาก Database เป็นหลัก (Dynamic)
     */
    private function get_system_storage_info()
    {
        try {
            log_message('info', '=== START: get_system_storage_info() ===');

            // ตรวจสอบว่าตารางมีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                log_message('error', 'Table tbl_google_drive_system_storage does not exist');
                return null;
            }

            log_message('info', 'Table tbl_google_drive_system_storage exists');

            // ดึงข้อมูล system storage
            $storage = $this->db->select('*')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->get()
                ->row();

            if (!$storage) {
                log_message('warning', 'No active system storage found');
                return null;
            }

            log_message('info', 'Found system storage: ID=' . $storage->id . ', Name=' . $storage->storage_name);

            // ===== คำนวณข้อมูลเพิ่มเติม =====

            // 1. นับจำนวน folders, files, users
            $total_folders = $this->db->where('is_active', 1)
                ->count_all_results('tbl_google_drive_system_folders');
            log_message('info', 'Total active folders: ' . $total_folders);

            $total_files = $this->db->where('is_active', 1)
                ->count_all_results('tbl_google_drive_system_files');
            log_message('info', 'Total active files: ' . $total_files);

            // นับ users ที่มีไฟล์อัปโหลด
            $active_users_result = $this->db->select('COUNT(DISTINCT uploaded_by) as user_count')
                ->from('tbl_google_drive_system_files')
                ->where('is_active', 1)
                ->get()
                ->row();
            $active_users = $active_users_result ? $active_users_result->user_count : 0;
            log_message('info', 'Total active users: ' . $active_users);

            // 2. คำนวณพื้นที่ใช้งานจริงจากไฟล์ (แทนค่าใน DB ที่อาจไม่ตรง)
            $storage_result = $this->db->select('SUM(file_size) as total_size')
                ->from('tbl_google_drive_system_files')
                ->where('is_active', 1)
                ->get()
                ->row();

            $real_storage_used = $storage_result && $storage_result->total_size
                ? (int) $storage_result->total_size
                : 0;

            log_message('info', 'Storage comparison: DB stored=' . $storage->total_storage_used .
                ', Calculated from files=' . $real_storage_used);

            // ✅ ใช้ค่าจากการคำนวณจริง
            $storage->total_storage_used = $real_storage_used;

            // ===== ดึงค่า Storage Limit จาก Settings (ใช้ get_system_setting() ที่มีอยู่แล้ว) =====

            if ($this->is_trial_mode) {
                // Trial Mode: ดึงค่าจาก trial_storage_limit
                $storage_limit = $this->get_system_setting('trial_storage_limit', '5368709120');
                $storage_limit = is_numeric($storage_limit) ? (int) $storage_limit : 5368709120;

                log_message('info', 'Trial Mode: storage_limit=' . $storage_limit .
                    ' (' . $this->format_bytes($storage_limit) . ')');

                $storage->mode_label = 'โหมดทดลอง';
                $storage->mode_description = 'จำกัดความจุ ' . $this->format_bytes($storage_limit);
                $storage->max_storage_limit = $storage_limit;
            } else {
                // Full Version: ดึงค่าจาก system_storage_limit
                $storage_limit = $this->get_system_setting('system_storage_limit', '214748364800');
                $storage_limit = is_numeric($storage_limit) ? (int) $storage_limit : 214748364800;

                log_message('info', 'Full Version: storage_limit=' . $storage_limit .
                    ' (' . $this->format_bytes($storage_limit) . ')');

                $storage->mode_label = 'เวอร์ชั่นเต็ม';
                $storage->mode_description = 'ใช้งานได้เต็มรูปแบบ';
                $storage->max_storage_limit = $storage_limit;
            }

            // ===== คำนวณ formatted values (ใช้ format_bytes() ที่มีอยู่แล้ว) =====

            $storage->total_storage_used_formatted = $this->format_bytes($storage->total_storage_used);
            $storage->max_storage_limit_formatted = $this->format_bytes($storage->max_storage_limit);

            // คำนวณเปอร์เซ็นต์
            $storage->storage_usage_percent = $storage->max_storage_limit > 0
                ? round(($storage->total_storage_used / $storage->max_storage_limit) * 100, 2)
                : 0;

            // ===== เพิ่มข้อมูลเสริม =====

            $storage->is_trial = $this->is_trial_mode;
            $storage->total_folders = $total_folders;
            $storage->total_files = $total_files;
            $storage->active_users = $active_users;

            // ===== เก็บข้อมูล User Storage ไว้สำหรับอนาคต =====

            // ดึงค่า default_user_quota จาก settings (ใช้ get_system_setting() ที่มีอยู่แล้ว)
            $user_quota = $this->get_system_setting('default_user_quota', '2147483648');
            $user_quota = is_numeric($user_quota) ? (int) $user_quota : 2147483648;

            $storage->user_quota_limit = $user_quota;
            $storage->user_quota_limit_formatted = $this->format_bytes($user_quota);

            // User quota text (สำหรับแสดงผล)
            if ($this->is_trial_mode) {
                $storage->user_quota_text = 'จำกัด ' . $this->format_bytes($user_quota);
            } else {
                $storage->user_quota_text = $this->format_bytes($user_quota) . ' ต่อผู้ใช้';
            }

            // Legacy fields (เก็บไว้เผื่อใช้ในส่วนอื่น - ความเข้ากันได้)
            $storage->system_quota_text = $storage->max_storage_limit_formatted;

            // ===== Log สรุป =====

            log_message('info', 'Formatted storage: ' .
                $storage->total_storage_used_formatted . ' / ' .
                $storage->max_storage_limit_formatted);

            log_message('info', 'Storage usage: ' .
                $storage->total_storage_used . ' bytes / ' .
                $storage->max_storage_limit . ' bytes (' .
                $storage->storage_usage_percent . '%)');

            log_message('info', 'Summary - Mode: ' . $storage->mode_label .
                ', Storage: ' . $storage->total_storage_used_formatted . ' / ' .
                $storage->max_storage_limit_formatted .
                ' (' . $storage->storage_usage_percent . '%)');

            log_message('info', 'User Quota: ' . $storage->user_quota_text .
                ' (' . $storage->user_quota_limit . ' bytes)');

            log_message('info', '=== END: get_system_storage_info() - Success ===');

            return $storage;

        } catch (Exception $e) {
            log_message('error', '=== ERROR in get_system_storage_info(): ' . $e->getMessage() . ' ===');
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }


    /**
     * 🔧 ดึง Member Permission (แบบ simple)
     */
    private function get_member_permission($member_id, $position_id)
    {
        try {
            // ตรวจสอบ permission แบบพื้นฐาน
            $default_permissions = [
                'permission_type' => 'position_only',
                'access_type' => 'position_only',
                'can_upload' => true,
                'can_create_folder' => false,
                'can_share' => false,
                'can_delete' => false
            ];

            // สำหรับ trial mode มีข้อจำกัด
            if ($this->is_trial_mode) {
                $default_permissions['can_create_folder'] = true; // อนุญาตสร้างโฟลเดอร์ใน trial
                $default_permissions['can_share'] = false; // ไม่อนุญาตแชร์ใน trial
                $default_permissions['can_delete'] = true; // อนุญาตลบใน trial
            }

            // ตรวจสอบจาก member permissions table (ถ้ามี)
            if ($this->db->table_exists('tbl_google_drive_member_permissions')) {
                $member_permission = $this->db->select('*')
                    ->from('tbl_google_drive_member_permissions')
                    ->where('member_id', $member_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($member_permission) {
                    $permissions = [
                        'permission_type' => $member_permission->permission_type,
                        'access_type' => $this->map_permission_to_access_type($member_permission->permission_type),
                        'can_upload' => true,
                        'can_create_folder' => $member_permission->can_create_folder,
                        'can_share' => $this->is_trial_mode ? false : $member_permission->can_share,
                        'can_delete' => $member_permission->can_delete
                    ];

                    return $permissions;
                }
            }

            // ตรวจสอบจาก position permissions (ถ้ามี)
            if ($this->db->table_exists('tbl_google_drive_position_permissions')) {
                $position_permission = $this->db->select('*')
                    ->from('tbl_google_drive_position_permissions')
                    ->where('position_id', $position_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($position_permission) {
                    $permissions = [
                        'permission_type' => $position_permission->permission_type,
                        'access_type' => $this->map_permission_to_access_type($position_permission->permission_type),
                        'can_upload' => true,
                        'can_create_folder' => $position_permission->can_create_folder,
                        'can_share' => $this->is_trial_mode ? false : $position_permission->can_share,
                        'can_delete' => $position_permission->can_delete
                    ];

                    return $permissions;
                }
            }

            // Default สำหรับ admin positions
            if (in_array($position_id, [1, 2])) {
                return [
                    'permission_type' => 'full_admin',
                    'access_type' => 'full',
                    'can_upload' => true,
                    'can_create_folder' => true,
                    'can_share' => !$this->is_trial_mode,
                    'can_delete' => true
                ];
            }

            return $default_permissions;

        } catch (Exception $e) {
            log_message('error', 'Get member permission error: ' . $e->getMessage());
            return $default_permissions;
        }
    }



    /**
     * 🔧 Map permission type to access type
     */
    private function map_permission_to_access_type($permission_type)
    {
        $mapping = [
            'full_admin' => 'full',
            'department_admin' => 'department',
            'position_only' => 'position_only',
            'custom' => 'custom',
            'read_only' => 'read_only',
            'no_access' => 'no_access'
        ];

        return $mapping[$permission_type] ?? 'position_only';
    }

    /**
     * 🔧 ดึง Access Token
     */
    private function get_access_token()
    {
        try {
            // สำหรับ trial mode ไม่ต้องใช้ access token
            if ($this->is_trial_mode) {
                // ถ้าเป็น trial mode แต่อยู่ใน centralized mode
                if ($this->storage_mode === 'centralized') {
                    return $this->get_system_access_token();
                } else {
                    return 'trial_token';
                }
            }

            if ($this->storage_mode === 'centralized') {
                // ดึง system access token
                return $this->get_system_access_token();
            } else {
                // ดึง member access token
                return $this->get_member_access_token();
            }
        } catch (Exception $e) {
            log_message('error', 'Get access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔧 ดึง System Access Token
     */
    private function get_system_access_token()
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                return null;
            }

            $system_storage = $this->db->select('google_access_token')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->get()
                ->row();

            if (!$system_storage) {
                return null;
            }

            $token_data = json_decode($system_storage->google_access_token, true);
            return $token_data['access_token'] ?? null;

        } catch (Exception $e) {
            log_message('error', 'Get system access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔧 ดึง Member Access Token
     */
    private function get_member_access_token()
    {
        try {
            $member = $this->db->select('google_access_token')
                ->from('tbl_member')
                ->where('m_id', $this->member_id)
                ->get()
                ->row();

            if (!$member) {
                return null;
            }

            $token_data = json_decode($member->google_access_token, true);
            return $token_data['access_token'] ?? null;

        } catch (Exception $e) {
            log_message('error', 'Get member access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔧 ดึงโฟลเดอร์ส่วนตัวของ User จาก Google Drive
     */
    private function get_user_google_drive_folders($access_token)
    {
        try {
            $ch = curl_init();

            // ดึงโฟลเดอร์ที่ user สร้างขึ้น
            $query = "mimeType='application/vnd.google-apps.folder' and trashed=false and 'me' in owners";
            $fields = 'files(id,name,mimeType,modifiedTime,parents,webViewLink)';

            $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
                'q' => $query,
                'fields' => $fields,
                'orderBy' => 'name',
                'pageSize' => 50
            ]);

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $data = json_decode($response, true);

                if ($data && isset($data['files'])) {
                    $folders = [];

                    foreach ($data['files'] as $file) {
                        $folders[] = [
                            'id' => $file['id'],
                            'name' => $file['name'],
                            'type' => 'folder',
                            'icon' => $this->get_folder_icon($file['name']),
                            'modified' => $this->format_google_date($file['modifiedTime']),
                            'size' => '-',
                            'description' => '',
                            'webViewLink' => $file['webViewLink'] ?? null,
                            'real_data' => true
                        ];
                    }

                    return $folders;
                }
            }

            return [];

        } catch (Exception $e) {
            log_message('error', 'Get user Google Drive folders error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 🔧 Output JSON Success
     */
    private function output_json_success($data = [], $message = 'สำเร็จ')
    {
        $this->safe_json_success($data, $message);
    }

    /**
     * 🔧 Output JSON Error
     */
    private function output_json_error($message = 'เกิดข้อผิดพลาด', $status_code = 400)
    {
        $this->safe_json_error($message, $status_code);
    }

    /**
     * 🔧 Helper methods อื่นๆ
     */

    private function format_datetime($datetime)
    {
        try {
            if (empty($datetime)) {
                return '-';
            }
            return date('d/m/Y H:i', strtotime($datetime));
        } catch (Exception $e) {
            return '-';
        }
    }

    private function format_google_date($google_date)
    {
        try {
            if (empty($google_date)) {
                return '-';
            }
            return date('d/m/Y H:i', strtotime($google_date));
        } catch (Exception $e) {
            return '-';
        }
    }

    private function format_file_size($bytes)
    {
        if ($bytes == 0)
            return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }

    private function get_folder_icon($folder_name)
    {
        $folder_name_lower = strtolower($folder_name);

        if (strpos($folder_name_lower, 'document') !== false || strpos($folder_name_lower, 'เอกสาร') !== false) {
            return 'fas fa-folder text-blue-500';
        } elseif (strpos($folder_name_lower, 'image') !== false || strpos($folder_name_lower, 'รูปภาพ') !== false) {
            return 'fas fa-folder text-purple-500';
        } elseif (strpos($folder_name_lower, 'project') !== false || strpos($folder_name_lower, 'โปรเจกต์') !== false) {
            return 'fas fa-folder text-green-500';
        } elseif (strpos($folder_name_lower, 'backup') !== false || strpos($folder_name_lower, 'สำรอง') !== false) {
            return 'fas fa-folder text-orange-500';
        } else {
            return 'fas fa-folder text-blue-500';
        }
    }

    private function get_folder_description($folder_name)
    {
        // สามารถเพิ่ม logic สำหรับ description ได้ตามต้องการ
        return '';
    }

    private function get_file_icon($mime_type)
    {
        $icon_map = [
            // Documents
            'application/pdf' => 'fas fa-file-pdf text-red-500',
            'application/msword' => 'fas fa-file-word text-blue-600',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word text-blue-600',

            // Spreadsheets
            'application/vnd.ms-excel' => 'fas fa-file-excel text-green-600',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel text-green-600',
            'application/vnd.google-apps.spreadsheet' => 'fas fa-file-excel text-green-600',

            // Presentations
            'application/vnd.ms-powerpoint' => 'fas fa-file-powerpoint text-orange-600',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fas fa-file-powerpoint text-orange-600',
            'application/vnd.google-apps.presentation' => 'fas fa-file-powerpoint text-orange-600',

            // Google Docs
            'application/vnd.google-apps.document' => 'fas fa-file-word text-blue-600',

            // Images
            'image/jpeg' => 'fas fa-file-image text-purple-500',
            'image/jpg' => 'fas fa-file-image text-purple-500',
            'image/png' => 'fas fa-file-image text-purple-500',
            'image/gif' => 'fas fa-file-image text-purple-500',
            'image/webp' => 'fas fa-file-image text-purple-500',

            // Text files
            'text/plain' => 'fas fa-file-alt text-gray-600',
            'text/csv' => 'fas fa-file-csv text-green-500',

            // Archives
            'application/zip' => 'fas fa-file-archive text-yellow-600',
            'application/rar' => 'fas fa-file-archive text-yellow-600',
            'application/x-7z-compressed' => 'fas fa-file-archive text-yellow-600',

            // Video
            'video/mp4' => 'fas fa-file-video text-red-600',
            'video/avi' => 'fas fa-file-video text-red-600',
            'video/mov' => 'fas fa-file-video text-red-600',

            // Audio
            'audio/mp3' => 'fas fa-file-audio text-purple-600',
            'audio/wav' => 'fas fa-file-audio text-purple-600',
            'audio/ogg' => 'fas fa-file-audio text-purple-600',

            // Code
            'text/javascript' => 'fas fa-file-code text-yellow-500',
            'text/html' => 'fas fa-file-code text-orange-500',
            'text/css' => 'fas fa-file-code text-blue-500',
            'application/json' => 'fas fa-file-code text-green-500',
        ];

        return $icon_map[$mime_type] ?? 'fas fa-file text-gray-500';
    }




    /**
     * 🤝 ตรวจสอบสิทธิ์โฟลเดอร์แชร์
     */
    private function check_shared_folder_access($folder_id)
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_shared_permissions')) {
                // ถ้าไม่มีตารางแชร์ ให้อนุญาตทั่วไป
                return true;
            }

            $shared_permission = $this->db->select('permission_level')
                ->from('tbl_google_drive_shared_permissions')
                ->where('folder_id', $folder_id)
                ->where('shared_with_member_id', $this->member_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($shared_permission) {
                return $shared_permission->permission_level !== 'no_access';
            }

            // ตรวจสอบการแชร์ระดับตำแหน่ง
            $member = $this->db->select('ref_pid')->from('tbl_member')->where('m_id', $this->member_id)->get()->row();
            if ($member) {
                $position_permission = $this->db->select('permission_level')
                    ->from('tbl_google_drive_shared_permissions')
                    ->where('folder_id', $folder_id)
                    ->where('shared_with_position_id', $member->ref_pid)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($position_permission) {
                    return $position_permission->permission_level !== 'no_access';
                }
            }

            // ถ้าไม่พบสิทธิ์การแชร์ = ไม่สามารถเข้าถึงได้
            return false;

        } catch (Exception $e) {
            log_message('error', 'Check shared folder access error: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * 🏛️ ตรวจสอบสิทธิ์ตามตำแหน่ง
     */
    private function check_position_access($required_position_id)
    {
        try {
            if (empty($required_position_id)) {
                return true;
            }

            $member = $this->db->select('ref_pid')->from('tbl_member')->where('m_id', $this->member_id)->get()->row();

            // ตรวจสอบตำแหน่งตรงกัน
            if ($member && $member->ref_pid == $required_position_id) {
                return true;
            }

            // ตรวจสอบตำแหน่งระดับเหนือ (ถ้ามีตาราง hierarchy)
            if ($this->db->table_exists('tbl_google_position_hierarchy')) {
                $hierarchy = $this->db->select('child_position_id')
                    ->from('tbl_google_position_hierarchy')
                    ->where('parent_position_id', $member->ref_pid)
                    ->where('child_position_id', $required_position_id)
                    ->get()
                    ->row();

                if ($hierarchy) {
                    return true;
                }
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Check position access error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * 📊 เพิ่มฟังก์ชันดึงสถิติการเข้าถึงโฟลเดอร์
     */
    private function log_folder_access($folder_id, $access_granted = true)
    {
        try {
            if ($this->db->table_exists('tbl_google_drive_folder_access_logs')) {
                $log_data = [
                    'member_id' => $this->member_id,
                    'folder_id' => $folder_id,
                    'access_granted' => $access_granted ? 1 : 0,
                    'access_time' => date('Y-m-d H:i:s'),
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent()
                ];

                $this->db->insert('tbl_google_drive_folder_access_logs', $log_data);
            }
        } catch (Exception $e) {
            log_message('error', 'Log folder access error: ' . $e->getMessage());
        }
    }




    /**
     * 🎯 ฟังก์ชันหลักที่ปรับปรุงแล้ว - เรียกจากที่อื่น
     */
    public function verify_folder_access($folder_id)
    {
        $access_granted = $this->check_folder_access_permission($folder_id);
        $this->log_folder_access($folder_id, $access_granted);
        return $access_granted;
    }


    private function prepare_file_data($file)
    {
        return [
            'name' => $file['name'],
            'tmp_name' => $file['tmp_name'],
            'size' => $file['size'],
            'type' => $file['type'],
            'error' => $file['error']
        ];
    }




    public function ajax_error_handler($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $error_msg = "PHP Error: {$message} in {$file} on line {$line}";
        log_message('error', $error_msg);

        // สำหรับ AJAX requests ให้ส่ง JSON error
        if ($this->input->is_ajax_request()) {
            $this->safe_json_error('เกิดข้อผิดพลาดภายในระบบ', 500, [
                'error_details' => ENVIRONMENT === 'development' ? $error_msg : 'Internal error'
            ]);
            exit;
        }

        return true;
    }

    /**
     * 🛠️ Custom Exception Handler สำหรับ AJAX
     */
    public function ajax_exception_handler($exception)
    {
        $error_msg = "Uncaught Exception: " . $exception->getMessage() .
            " in " . $exception->getFile() . " on line " . $exception->getLine();

        log_message('error', $error_msg);

        if ($this->input->is_ajax_request()) {
            $this->safe_json_error('เกิดข้อผิดพลาดภายในระบบ', 500, [
                'exception' => ENVIRONMENT === 'development' ? $error_msg : 'Internal exception'
            ]);
            exit;
        }
    }




    /**
     * 🔐 ดึงสิทธิ์สำหรับโฟลเดอร์เฉพาะ (AJAX) - ✅ FIXED VERSION WITH DEBUG LOGGING
     * แก้ไข 500 Error โดยเพิ่ม error handling และ validation ครบถ้วน
     * เพิ่ม comprehensive INFO-level logging สำหรับ debug
     */
    public function get_folder_permissions()
    {
        try {
            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            log_message('info', '🔐 START: get_folder_permissions()');
            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            // ✅ STEP 1: ล้าง output buffer และป้องกัน PHP Error
            while (ob_get_level()) {
                ob_end_clean();
            }
            log_message('info', '✅ STEP 1: Output buffer cleared');

            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            log_message('info', '✅ STEP 1: Headers set');

            // ✅ STEP 2: ตรวจสอบ AJAX request
            $is_ajax = $this->input->is_ajax_request();
            log_message('info', '🔍 STEP 2: Checking AJAX request');
            log_message('info', "   └─ Is AJAX: " . ($is_ajax ? 'YES' : 'NO'));

            if (!$is_ajax) {
                log_message('info', '❌ STEP 2 FAILED: Not an AJAX request');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            log_message('info', '✅ STEP 2: AJAX request validated');

            // ✅ STEP 3: ตรวจสอบ member_id
            log_message('info', '🔍 STEP 3: Checking member session');
            $member_id = $this->member_id ?? $this->session->userdata('m_id');
            log_message('info', "   └─ Member ID from property: " . ($this->member_id ?? 'null'));
            log_message('info', "   └─ Member ID from session: " . ($this->session->userdata('m_id') ?? 'null'));
            log_message('info', "   └─ Final Member ID: " . ($member_id ?? 'null'));

            if (!$member_id) {
                log_message('info', '❌ STEP 3 FAILED: No member session found');
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ session ผู้ใช้',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            log_message('info', '✅ STEP 3: Member ID validated: ' . $member_id);

            // ✅ STEP 4: รับค่า folder_id
            log_message('info', '🔍 STEP 4: Getting folder_id from POST');
            $folder_id_raw = $this->input->post('folder_id');
            log_message('info', "   └─ Raw folder_id: " . ($folder_id_raw === null ? 'NULL' : ($folder_id_raw === false ? 'FALSE' : "'{$folder_id_raw}'")));

            if ($folder_id_raw === null || $folder_id_raw === false) {
                $folder_id = 'root';
                log_message('info', "   └─ folder_id is null/false, using default: 'root'");
            } else {
                $folder_id = trim($folder_id_raw);
                log_message('info', "   └─ folder_id after trim: '{$folder_id}'");
            }

            if (empty($folder_id)) {
                $folder_id = 'root';
                log_message('info', "   └─ folder_id is empty, using default: 'root'");
            }

            log_message('info', '✅ STEP 4: Final folder_id: ' . $folder_id);
            log_message('info', "📊 Request Summary:");
            log_message('info', "   ├─ Member ID: {$member_id}");
            log_message('info', "   ├─ Folder ID: {$folder_id}");
            log_message('info', "   └─ Timestamp: " . date('Y-m-d H:i:s'));

            // ✅ STEP 5: ใช้ permissions แบบง่าย
            log_message('info', '🔍 STEP 5: Calling get_simple_folder_permissions()');
            log_message('info', "   ├─ folder_id: {$folder_id}");
            log_message('info', "   └─ member_id: {$member_id}");

            $permissions = $this->get_simple_folder_permissions($folder_id, $member_id);

            log_message('info', '✅ STEP 5: Permissions retrieved');
            log_message('info', '📋 Permission Details:');
            log_message('info', '   ├─ access_level: ' . ($permissions['access_level'] ?? 'N/A'));
            log_message('info', '   ├─ can_upload: ' . (($permissions['can_upload'] ?? false) ? 'true' : 'false'));
            log_message('info', '   ├─ can_create_folder: ' . (($permissions['can_create_folder'] ?? false) ? 'true' : 'false'));
            log_message('info', '   ├─ can_share: ' . (($permissions['can_share'] ?? false) ? 'true' : 'false'));
            log_message('info', '   ├─ can_delete: ' . (($permissions['can_delete'] ?? false) ? 'true' : 'false'));
            log_message('info', '   ├─ can_download: ' . (($permissions['can_download'] ?? false) ? 'true' : 'false'));
            log_message('info', '   └─ permission_source: ' . ($permissions['permission_source'] ?? 'N/A'));

            // ✅ STEP 6: ส่งผลลัพธ์
            log_message('info', '🔍 STEP 6: Preparing response');
            $response = [
                'success' => true,
                'message' => 'ดึงสิทธิ์โฟลเดอร์สำเร็จ',
                'data' => $permissions,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            log_message('info', '✅ STEP 6: Response prepared');
            log_message('info', '📤 Response JSON: ' . json_encode($response, JSON_UNESCAPED_UNICODE));

            http_response_code(200);
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            log_message('info', '✅ END: get_folder_permissions() - SUCCESS');
            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            exit;

        } catch (Exception $e) {
            // ✅ STEP 7: Error handling ที่ปลอดภัย
            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            log_message('info', '❌ ERROR in get_folder_permissions()');
            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            log_message('error', '💥 Exception caught: ' . $e->getMessage());
            log_message('error', '📁 File: ' . $e->getFile());
            log_message('error', '📍 Line: ' . $e->getLine());
            log_message('error', '📚 Stack trace:');
            log_message('error', $e->getTraceAsString());

            while (ob_get_level()) {
                ob_end_clean();
            }

            log_message('info', '🔄 Using fallback permissions due to error');
            $fallback = $this->get_fallback_permissions($folder_id ?? 'root');
            log_message('info', '📋 Fallback Permissions:');
            log_message('info', '   └─ ' . json_encode($fallback, JSON_UNESCAPED_UNICODE));

            http_response_code(200); // ใช้ 200 แทน 500 เพื่อป้องกัน client error
            header('Content-Type: application/json; charset=utf-8');

            $error_response = [
                'success' => true, // return success เพื่อไม่ให้ UI เสียหาย
                'message' => 'ใช้สิทธิ์เริ่มต้น',
                'data' => $fallback,
                'fallback' => true,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            echo json_encode($error_response, JSON_UNESCAPED_UNICODE);

            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            log_message('info', '✅ END: get_folder_permissions() - FALLBACK MODE');
            log_message('info', '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            exit;
        }
    }


    /**
     * 🏠 สิทธิ์สำหรับ Root folder - WITH DEBUG LOGGING
     */
    private function get_root_permissions($member_id, $default_permissions)
    {
        try {
            log_message('info', '   ┌─ get_root_permissions() START');
            log_message('info', "   ├─ Member ID: {$member_id}");

            // ดึงข้อมูล member และ position
            log_message('info', '   ├─ Querying member position from database...');
            $member = $this->db->select('ref_pid')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if ($member) {
                log_message('info', '   ├─ ✅ Member found in database');
                log_message('info', "   ├─ Position ID: {$member->ref_pid}");

                // Admin positions (1, 2) ได้สิทธิ์เต็ม
                if (in_array($member->ref_pid, [1, 2])) {
                    log_message('info', '   ├─ ✅ Member is ADMIN (position 1 or 2)');
                    log_message('info', '   ├─ Granting FULL permissions');
                    $admin_perms = array_merge($default_permissions, [
                        'access_level' => 'admin',
                        'can_upload' => true,
                        'can_create_folder' => true,
                        'can_share' => !$this->is_trial_mode,
                        'can_delete' => true,
                        'can_download' => !$this->is_trial_mode,
                        'permission_source' => 'admin'
                    ]);
                    log_message('info', '   └─ get_root_permissions() END - ADMIN PERMISSIONS');
                    return $admin_perms;
                }

                log_message('info', '   ├─ Member is REGULAR USER (not admin)');
            } else {
                log_message('info', '   ├─ ⚠️ Member NOT found in database');
            }

            // สิทธิ์มาตรฐานสำหรับ user ทั่วไป
            log_message('info', '   ├─ Granting STANDARD permissions (read_write)');
            $standard_perms = array_merge($default_permissions, [
                'access_level' => 'read_write',
                'can_upload' => true,
                'can_create_folder' => true,
                'can_share' => true,
                'can_delete' => true,
                'can_download' => !$this->is_trial_mode,
                'permission_source' => 'position'
            ]);
            log_message('info', '   └─ get_root_permissions() END - STANDARD PERMISSIONS');
            return $standard_perms;

        } catch (Exception $e) {
            log_message('info', '   ├─ ❌ ERROR in get_root_permissions()');
            log_message('error', '   ├─ Exception: ' . $e->getMessage());
            log_message('info', '   └─ Returning DEFAULT permissions');
            return $default_permissions;
        }
    }

    /**
     * 📁 สิทธิ์สำหรับโฟลเดอร์ทั่วไป - FIXED VERSION WITH COMPLETE CHECKS
     */
    private function get_default_folder_permissions($folder_id, $member_id, $default_permissions)
    {
        try {
            log_message('info', '   ┌─ get_default_folder_permissions() START');
            log_message('info', "   ├─ Folder ID: {$folder_id}");
            log_message('info', "   ├─ Member ID: {$member_id}");

            // ========================================
            // LEVEL 1: เช็ค m_system Role (สูงสุด)
            // ========================================
            log_message('info', '   ├─ LEVEL 1: Checking m_system role...');
            $member = $this->db->select('m_system')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if ($member && in_array($member->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "   ├─ ✅ LEVEL 1 SUCCESS: m_system = {$member->m_system}");
                log_message('info', '   ├─ Granting FULL ADMIN permissions');

                $admin_perms = array_merge($default_permissions, [
                    'access_level' => 'admin',
                    'can_upload' => true,
                    'can_create_folder' => true,
                    'can_share' => true,
                    'can_delete' => true,
                    'can_download' => true,
                    'permission_source' => 'm_system_role',
                    'role_type' => $member->m_system
                ]);

                log_message('info', '   └─ get_default_folder_permissions() END - ADMIN (m_system)');
                return $admin_perms;
            }
            log_message('info', '   ├─ ⚠️ LEVEL 1 FAILED: Not a system/super admin');

            // ========================================
            // LEVEL 2: เช็ค Direct Permission
            // ========================================
            log_message('info', '   ├─ LEVEL 2: Checking direct folder access...');
            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                log_message('info', '   ├─ ✅ Table exists, querying folder access...');

                $folder_access = $this->db->select('access_type')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('folder_id', $folder_id)
                    ->where('member_id', $member_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($folder_access) {
                    log_message('info', '   ├─ ✅ LEVEL 2 SUCCESS: Direct folder access found');
                    log_message('info', "   ├─ Access Type: {$folder_access->access_type}");

                    $direct_perms = array_merge($default_permissions, [
                        'access_level' => $folder_access->access_type,
                        'can_upload' => in_array($folder_access->access_type, ['write', 'admin', 'owner']),
                        'can_create_folder' => in_array($folder_access->access_type, ['write', 'admin', 'owner']),
                        'can_share' => in_array($folder_access->access_type, ['write', 'admin', 'owner']),
                        'can_delete' => in_array($folder_access->access_type, ['admin', 'owner']),
                        'can_download' => true,
                        'permission_source' => 'direct'
                    ]);

                    log_message('info', '   └─ get_default_folder_permissions() END - DIRECT PERMISSIONS');
                    return $direct_perms;
                }
                log_message('info', '   ├─ ⚠️ LEVEL 2 FAILED: No direct folder access found');
            }

            // ========================================
            // LEVEL 3: เช็ค Position Permission
            // ========================================
            log_message('info', '   ├─ LEVEL 3: Checking position-based access...');
            $position_access = $this->check_position_permission_for_folder($folder_id, $member_id);

            if ($position_access) {
                log_message('info', '   ├─ ✅ LEVEL 3 SUCCESS: Position permission found');
                log_message('info', '   └─ get_default_folder_permissions() END - POSITION PERMISSIONS');
                return $position_access;
            }
            log_message('info', '   ├─ ⚠️ LEVEL 3 FAILED: No position permission found');

            // ========================================
            // LEVEL 4: Default Read-Only
            // ========================================
            log_message('info', '   ├─ LEVEL 4: Using READ ONLY default permissions');
            $readonly_perms = array_merge($default_permissions, [
                'access_level' => 'read_only',
                'can_upload' => false,
                'can_create_folder' => false,
                'can_share' => false,
                'can_delete' => false,
                'can_download' => !$this->is_trial_mode,
                'permission_source' => 'default'
            ]);

            log_message('info', '   └─ get_default_folder_permissions() END - READ ONLY');
            return $readonly_perms;

        } catch (Exception $e) {
            log_message('info', '   ├─ ❌ ERROR in get_default_folder_permissions()');
            log_message('error', '   ├─ Exception: ' . $e->getMessage());
            log_message('info', '   └─ Returning DEFAULT permissions');
            return $default_permissions;
        }
    }

    /**
     * 🔐 Helper: เช็ค Position Permission สำหรับ folder
     */
    private function check_position_permission_for_folder($folder_id, $member_id)
    {
        try {
            // ดึง position_id ของ member
            $member_info = $this->db->select('ref_pid')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if (!$member_info) {
                log_message('info', '   ├─ ⚠️ Member info not found');
                return false;
            }

            log_message('info', "   ├─ Member Position ID: {$member_info->ref_pid}");

            // เช็คสิทธิ์จาก position permissions
            $position_perm = $this->db->select('permission_type, folder_access, can_create_folder, can_share, can_delete')
                ->from('tbl_google_drive_position_permissions')
                ->where('position_id', $member_info->ref_pid)
                ->where('is_active', 1)
                ->get()
                ->row();

            if (!$position_perm) {
                log_message('info', '   ├─ ⚠️ No position permission record found');
                return false;
            }

            log_message('info', "   ├─ Permission Type: {$position_perm->permission_type}");
            log_message('info', "   ├─ Folder Access: {$position_perm->folder_access}");

            // ตรวจสอบ folder_access
            $folder_access_list = json_decode($position_perm->folder_access, true);

            // ถ้า permission_type = 'full_admin' หรือ folder_access มี "all"
            if (
                $position_perm->permission_type === 'full_admin' ||
                (is_array($folder_access_list) && in_array('all', $folder_access_list))
            ) {

                log_message('info', '   ├─ ✅ Full admin access granted via position');
                return [
                    'access_level' => 'admin',
                    'can_upload' => true,
                    'can_create_folder' => (bool) $position_perm->can_create_folder,
                    'can_share' => (bool) $position_perm->can_share,
                    'can_delete' => (bool) $position_perm->can_delete,
                    'can_download' => true,
                    'permission_source' => 'position',
                    'permission_type' => $position_perm->permission_type
                ];
            }

            // ถ้า folder_access เป็น array และมี folder_id ที่ต้องการ
            if (is_array($folder_access_list) && in_array($folder_id, $folder_access_list)) {
                log_message('info', '   ├─ ✅ Specific folder access granted via position');
                return [
                    'access_level' => 'write',
                    'can_upload' => true,
                    'can_create_folder' => (bool) $position_perm->can_create_folder,
                    'can_share' => (bool) $position_perm->can_share,
                    'can_delete' => (bool) $position_perm->can_delete,
                    'can_download' => true,
                    'permission_source' => 'position',
                    'permission_type' => $position_perm->permission_type
                ];
            }

            // ถ้า permission_type = 'department_admin' และ folder เป็น shared/department
            if (
                $position_perm->permission_type === 'department_admin' &&
                (is_array($folder_access_list) &&
                    (in_array('shared', $folder_access_list) || in_array('department', $folder_access_list)))
            ) {

                // เช็คว่า folder นี้เป็น shared/department folder หรือไม่
                $is_shared_dept = $this->is_shared_or_department_folder($folder_id);

                if ($is_shared_dept) {
                    log_message('info', '   ├─ ✅ Department admin access granted');
                    return [
                        'access_level' => 'write',
                        'can_upload' => true,
                        'can_create_folder' => (bool) $position_perm->can_create_folder,
                        'can_share' => (bool) $position_perm->can_share,
                        'can_delete' => (bool) $position_perm->can_delete,
                        'can_download' => true,
                        'permission_source' => 'position',
                        'permission_type' => $position_perm->permission_type
                    ];
                }
            }

            log_message('info', '   ├─ ⚠️ Position permission exists but does not apply to this folder');
            return false;

        } catch (Exception $e) {
            log_message('error', '   ├─ Exception in check_position_permission_for_folder: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔍 Helper: ตรวจสอบว่า folder เป็น shared/department folder หรือไม่
     */
    private function is_shared_or_department_folder($folder_id)
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
                return false;
            }

            $folder = $this->db->select('folder_type')
                ->from('tbl_google_drive_system_folders')
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            return $folder && in_array($folder->folder_type, ['shared', 'department']);

        } catch (Exception $e) {
            log_message('error', 'Error checking folder type: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🛡️ สิทธิ์สำรอง (Fallback)
     */
    private function get_fallback_permissions($folder_id)
    {
        return [
            'access_level' => 'read_only',
            'can_upload' => false,
            'can_create_folder' => false,
            'can_share' => false,
            'can_delete' => false,
            'can_download' => false,
            'permission_source' => 'fallback',
            'granted_by' => 'System',
            'granted_at' => date('Y-m-d H:i:s'),
            'expires_at' => null,
            'folder_id' => $folder_id,
            'member_id' => $this->member_id ?? 0,
            'is_trial' => $this->is_trial_mode ?? false,
            'error' => true,
            'error_message' => 'ใช้สิทธิ์เริ่มต้นเนื่องจากเกิดข้อผิดพลาด'
        ];
    }








    /**
     * 🔐 ดึงสิทธิ์โฟลเดอร์แบบง่าย (Simple & Safe)
     */
    private function get_simple_folder_permissions($folder_id, $member_id)
    {
        try {
            // สิทธิ์เริ่มต้น
            $default_permissions = [
                'access_level' => 'read_write',
                'can_upload' => true,
                'can_create_folder' => true,
                'can_share' => true,
                'can_delete' => true,
                'can_download' => true,
                'permission_source' => 'default',
                'granted_by' => 'System',
                'granted_at' => date('Y-m-d H:i:s'),
                'expires_at' => null,
                'folder_id' => $folder_id,
                'member_id' => $member_id,
                'is_trial' => $this->is_trial_mode ?? false
            ];

            // ✅ สำหรับ Trial Mode
            if (isset($this->is_trial_mode) && $this->is_trial_mode) {
                return $this->get_trial_permissions($folder_id, $default_permissions);
            }

            // ✅ สำหรับ Root folder
            if ($folder_id === 'root' || empty($folder_id)) {
                return $this->get_root_permissions($member_id, $default_permissions);
            }

            // ✅ สำหรับโฟลเดอร์อื่นๆ ให้สิทธิ์มาตรฐาน
            return $this->get_default_folder_permissions($folder_id, $member_id, $default_permissions);

        } catch (Exception $e) {
            log_message('error', 'Get simple folder permissions error: ' . $e->getMessage());
            return $this->get_fallback_permissions($folder_id);
        }
    }


    /**
     * 🎭 ดึงสิทธิ์สำหรับ Trial Mode (Enhanced Safe)
     */
    private function get_trial_folder_permissions_safe($folder_id, $default_permissions)
    {
        try {
            $trial_folders = [
                'demo_folder_1',
                'demo_folder_2',
                'demo_folder_3',
                'demo_folder_4'
            ];

            if (in_array($folder_id, $trial_folders) || $folder_id === 'root') {
                return array_merge($default_permissions, [
                    'access_level' => 'read_write',
                    'can_upload' => true,
                    'can_create_folder' => true,
                    'can_share' => true, // ล็อคใน trial
                    'can_delete' => true,
                    'can_download' => true, // ล็อคใน trial
                    'permission_source' => 'trial',
                    'granted_by' => 'System',
                    'granted_at' => date('Y-m-d H:i:s'),
                    'expires_at' => null,
                    'is_trial' => true,
                    'folder_id' => $folder_id
                ]);
            }

            return array_merge($default_permissions, [
                'access_level' => 'no_access',
                'can_upload' => false,
                'can_create_folder' => false,
                'can_share' => false,
                'can_delete' => false,
                'can_download' => false,
                'permission_source' => 'trial',
                'is_trial' => true,
                'folder_id' => $folder_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Get trial folder permissions error: ' . $e->getMessage());
            return array_merge($default_permissions, [
                'access_level' => 'no_access',
                'permission_source' => 'trial_error',
                'error' => true,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 🏠 ดึงสิทธิ์สำหรับ Root folder (Enhanced Safe)
     */
    private function get_root_folder_permissions_safe($member, $default_permissions)
    {
        try {
            // ใช้ method ที่มีอยู่แล้วแต่เพิ่ม error handling
            $base_permission = $this->get_member_permission_safe($member->m_id, $member->ref_pid);

            if (!$base_permission) {
                return array_merge($default_permissions, [
                    'permission_source' => 'default_fallback'
                ]);
            }

            $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

            return array_merge($default_permissions, [
                'access_level' => $this->map_permission_to_access_level_safe($base_permission['permission_type']),
                'can_upload' => isset($base_permission['can_upload']) ? (bool) $base_permission['can_upload'] : false,
                'can_create_folder' => isset($base_permission['can_create_folder']) ? (bool) $base_permission['can_create_folder'] : false,
                'can_share' => $is_trial ? false : (isset($base_permission['can_share']) ? (bool) $base_permission['can_share'] : false),
                'can_delete' => isset($base_permission['can_delete']) ? (bool) $base_permission['can_delete'] : false,
                'can_download' => !$is_trial,
                'permission_source' => 'position',
                'granted_by' => 'System',
                'granted_at' => isset($member->pcreate) ? $member->pcreate : date('Y-m-d H:i:s'),
                'expires_at' => null,
                'folder_id' => 'root',
                'member_id' => $member->m_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Get root folder permissions error: ' . $e->getMessage());
            return array_merge($default_permissions, [
                'permission_source' => 'error',
                'error' => true,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 🔧 ดึง Member Permission อย่างปลอดภัย (Safe Version)
     */
    private function get_member_permission_safe($member_id, $position_id)
    {
        try {
            // ตรวจสอบว่า method เดิมมีอยู่และใช้งานได้
            if (method_exists($this, 'get_member_permission')) {
                $result = $this->get_member_permission($member_id, $position_id);
                if (is_array($result) && !empty($result)) {
                    return $result;
                }
            }

            // Fallback: สร้างสิทธิ์พื้นฐาน
            $default_permissions = [
                'permission_type' => 'position_only',
                'access_type' => 'position_only',
                'can_upload' => true,
                'can_create_folder' => false,
                'can_share' => false,
                'can_delete' => false
            ];

            // สำหรับ trial mode มีข้อจำกัด
            if (isset($this->is_trial_mode) && $this->is_trial_mode) {
                $default_permissions['can_create_folder'] = true;
                $default_permissions['can_share'] = false;
                $default_permissions['can_delete'] = true;
            }

            // ตรวจสอบจาก position permissions (ถ้ามีตาราง)
            if ($this->db && $this->db->table_exists('tbl_google_drive_position_permissions')) {
                try {
                    $position_permission = $this->db->select('*')
                        ->from('tbl_google_drive_position_permissions')
                        ->where('position_id', $position_id)
                        ->where('is_active', 1)
                        ->limit(1)
                        ->get()
                        ->row();

                    if ($position_permission) {
                        $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

                        return [
                            'permission_type' => $position_permission->permission_type,
                            'access_type' => $this->map_permission_to_access_type($position_permission->permission_type),
                            'can_upload' => true,
                            'can_create_folder' => (bool) $position_permission->can_create_folder,
                            'can_share' => $is_trial ? false : (bool) $position_permission->can_share,
                            'can_delete' => (bool) $position_permission->can_delete
                        ];
                    }
                } catch (Exception $e) {
                    log_message('debug', 'Position permission query failed: ' . $e->getMessage());
                }
            }

            // Default สำหรับ admin positions
            if (in_array($position_id, [1, 2])) {
                return [
                    'permission_type' => 'full_admin',
                    'access_type' => 'full',
                    'can_upload' => true,
                    'can_create_folder' => true,
                    'can_share' => !isset($this->is_trial_mode) || !$this->is_trial_mode,
                    'can_delete' => true
                ];
            }

            return $default_permissions;

        } catch (Exception $e) {
            log_message('error', 'Get member permission safe error: ' . $e->getMessage());

            // Return ultra-safe defaults
            return [
                'permission_type' => 'read_only',
                'access_type' => 'read_only',
                'can_upload' => false,
                'can_create_folder' => false,
                'can_share' => false,
                'can_delete' => false
            ];
        }
    }

    /**
     * 🏢 ดึงสิทธิ์สำหรับ Centralized Mode (Enhanced Safe)
     */
    private function get_centralized_folder_permissions_safe($folder_id, $member, $default_permissions)
    {
        try {
            // 1. ตรวจสอบสิทธิ์เฉพาะโฟลเดอร์ (Direct Permission)
            $direct_permission = $this->get_direct_folder_permission_safe($folder_id, $member->m_id);
            if ($direct_permission && $direct_permission['access_level'] !== 'no_access') {
                return $direct_permission;
            }

            // 2. ตรวจสอบสิทธิ์จากตำแหน่ง
            if (isset($member->ref_pid) && $member->ref_pid) {
                $position_permission = $this->get_position_folder_permission_safe($folder_id, $member->ref_pid);
                if ($position_permission && $position_permission['access_level'] !== 'no_access') {
                    return $position_permission;
                }
            }

            // 3. ตรวจสอบสิทธิ์จากการแชร์
            $shared_permission = $this->get_shared_folder_permission_safe($folder_id, $member);
            if ($shared_permission && $shared_permission['access_level'] !== 'no_access') {
                return $shared_permission;
            }

            // 4. สิทธิ์เริ่มต้น
            return $this->get_default_centralized_permission_safe($member, $default_permissions);

        } catch (Exception $e) {
            log_message('error', 'Get centralized folder permissions error: ' . $e->getMessage());
            return array_merge($default_permissions, [
                'permission_source' => 'error',
                'error' => true,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 👤 ดึงสิทธิ์สำหรับ User-based Mode (Enhanced Safe)
     */
    private function get_user_based_folder_permissions_safe($folder_id, $member, $default_permissions)
    {
        try {
            // 1. ตรวจสอบเจ้าของโฟลเดอร์
            if ($this->is_folder_owner_safe($folder_id, $member->m_id)) {
                $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

                return array_merge($default_permissions, [
                    'access_level' => 'owner',
                    'can_upload' => true,
                    'can_create_folder' => true,
                    'can_share' => !$is_trial,
                    'can_delete' => true,
                    'can_download' => !$is_trial,
                    'permission_source' => 'owner',
                    'granted_by' => 'Self',
                    'granted_at' => $this->get_folder_created_date_safe($folder_id),
                    'expires_at' => null,
                    'folder_id' => $folder_id,
                    'member_id' => $member->m_id
                ]);
            }

            // 2. ตรวจสอบการแชร์
            $shared_permission = $this->get_user_shared_folder_permission_safe($folder_id, $member);
            if ($shared_permission && $shared_permission['access_level'] !== 'no_access') {
                return $shared_permission;
            }

            // 3. สิทธิ์เริ่มต้น (ไม่มีสิทธิ์)
            return array_merge($default_permissions, [
                'access_level' => 'no_access',
                'can_upload' => false,
                'can_create_folder' => false,
                'can_share' => false,
                'can_delete' => false,
                'can_download' => false,
                'permission_source' => 'none',
                'folder_id' => $folder_id,
                'member_id' => $member->m_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Get user based folder permissions error: ' . $e->getMessage());
            return array_merge($default_permissions, [
                'access_level' => 'no_access',
                'permission_source' => 'error',
                'error' => true,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 🔍 ตรวจสอบสิทธิ์โดยตรงของโฟลเดอร์ (Enhanced Safe)
     */
    private function get_direct_folder_permission_safe($folder_id, $member_id)
    {
        try {
            if (!$this->db || !$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                return null;
            }

            $permission = $this->db->select('access_type, permission_source, granted_by, granted_by_name, granted_at, expires_at')
                ->from('tbl_google_drive_member_folder_access')
                ->where('folder_id', $folder_id)
                ->where('member_id', $member_id)
                ->where('is_active', 1)
                ->where('(expires_at IS NULL OR expires_at > NOW())')
                ->order_by('granted_at', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($permission) {
                $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

                return [
                    'access_level' => $permission->access_type,
                    'can_upload' => in_array($permission->access_type, ['write', 'admin', 'owner']),
                    'can_create_folder' => in_array($permission->access_type, ['write', 'admin', 'owner']),
                    'can_share' => !$is_trial && in_array($permission->access_type, ['write', 'admin', 'owner']),
                    'can_delete' => in_array($permission->access_type, ['write', 'admin', 'owner']),
                    'can_download' => !$is_trial,
                    'permission_source' => $permission->permission_source,
                    'granted_by' => $permission->granted_by_name,
                    'granted_at' => $permission->granted_at,
                    'expires_at' => $permission->expires_at,
                    'folder_id' => $folder_id,
                    'member_id' => $member_id
                ];
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Get direct folder permission safe error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔄 Map Permission Type เป็น Access Level (Enhanced Safe)
     */
    private function map_permission_to_access_level_safe($permission_type)
    {
        if (empty($permission_type)) {
            return 'read_only';
        }

        $mapping = [
            'full_admin' => 'owner',
            'department_admin' => 'admin',
            'position_only' => 'read_write',
            'custom' => 'read_write',
            'read_only' => 'read_only',
            'no_access' => 'no_access'
        ];

        return isset($mapping[$permission_type]) ? $mapping[$permission_type] : 'read_only';
    }

    /**
     * ⚙️ สิทธิ์เริ่มต้นสำหรับ Centralized Mode (Enhanced Safe)
     */
    private function get_default_centralized_permission_safe($member, $default_permissions)
    {
        try {
            $base_permission = $this->get_member_permission_safe($member->m_id, $member->ref_pid);
            $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

            return array_merge($default_permissions, [
                'access_level' => $this->map_permission_to_access_level_safe($base_permission['permission_type']),
                'can_upload' => isset($base_permission['can_upload']) ? (bool) $base_permission['can_upload'] : false,
                'can_create_folder' => isset($base_permission['can_create_folder']) ? (bool) $base_permission['can_create_folder'] : false,
                'can_share' => $is_trial ? false : (isset($base_permission['can_share']) ? (bool) $base_permission['can_share'] : false),
                'can_delete' => isset($base_permission['can_delete']) ? (bool) $base_permission['can_delete'] : false,
                'can_download' => !$is_trial,
                'permission_source' => 'default',
                'granted_by' => 'System',
                'granted_at' => date('Y-m-d H:i:s'),
                'expires_at' => null,
                'member_id' => $member->m_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Get default centralized permission error: ' . $e->getMessage());
            return array_merge($default_permissions, [
                'permission_source' => 'error',
                'error' => true,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 👤 ตรวจสอบเจ้าของโฟลเดอร์ (Enhanced Safe)
     */
    private function is_folder_owner_safe($folder_id, $member_id)
    {
        try {
            if (!$this->db || !$this->db->table_exists('tbl_google_drive_folders')) {
                return false;
            }

            $folder = $this->db->select('member_id')
                ->from('tbl_google_drive_folders')
                ->where('folder_id', $folder_id)
                ->where('member_id', $member_id)
                ->where('is_active', 1)
                ->limit(1)
                ->get()
                ->row();

            return !empty($folder);

        } catch (Exception $e) {
            log_message('error', 'Check folder owner safe error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🏛️ ตรวจสอบสิทธิ์จากตำแหน่ง (Enhanced Safe)
     */
    private function get_position_folder_permission_safe($folder_id, $position_id)
    {
        try {
            if (!$this->db || !$this->db->table_exists('tbl_google_drive_system_folders')) {
                return null;
            }

            $folder = $this->db->select('folder_type, created_for_position, permission_level')
                ->from('tbl_google_drive_system_folders')
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->limit(1)
                ->get()
                ->row();

            if ($folder && $folder->folder_type === 'position' && $folder->created_for_position == $position_id) {
                $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

                return [
                    'access_level' => 'read_write',
                    'can_upload' => true,
                    'can_create_folder' => true,
                    'can_share' => !$is_trial,
                    'can_delete' => true,
                    'can_download' => !$is_trial,
                    'permission_source' => 'position',
                    'granted_by' => 'System',
                    'granted_at' => date('Y-m-d H:i:s'),
                    'expires_at' => null,
                    'folder_id' => $folder_id,
                    'position_id' => $position_id
                ];
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Get position folder permission safe error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🤝 ตรวจสอบสิทธิ์จากการแชร์ (Enhanced Safe)
     */
    private function get_shared_folder_permission_safe($folder_id, $member)
    {
        try {
            if (!$this->db || !$this->db->table_exists('tbl_google_drive_shared_permissions')) {
                return null;
            }

            // ตรวจสอบแชร์กับ member โดยตรง
            $shared = $this->db->select('permission_level, shared_by_member_id, shared_at, expires_at')
                ->from('tbl_google_drive_shared_permissions')
                ->where('folder_id', $folder_id)
                ->where('shared_with_member_id', $member->m_id)
                ->where('is_active', 1)
                ->where('(expires_at IS NULL OR expires_at > NOW())')
                ->limit(1)
                ->get()
                ->row();

            if ($shared) {
                $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

                return [
                    'access_level' => $shared->permission_level,
                    'can_upload' => in_array($shared->permission_level, ['write', 'admin']),
                    'can_create_folder' => $shared->permission_level === 'admin',
                    'can_share' => !$is_trial && $shared->permission_level === 'admin',
                    'can_delete' => $shared->permission_level === 'admin',
                    'can_download' => !$is_trial,
                    'permission_source' => 'shared',
                    'granted_by' => $this->get_member_name_safe($shared->shared_by_member_id),
                    'granted_at' => $shared->shared_at,
                    'expires_at' => $shared->expires_at,
                    'folder_id' => $folder_id,
                    'member_id' => $member->m_id
                ];
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Get shared folder permission safe error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🤝 ตรวจสอบการแชร์โฟลเดอร์ (User-based Mode, Enhanced Safe)
     */
    private function get_user_shared_folder_permission_safe($folder_id, $member)
    {
        try {
            if (!$this->db || !$this->db->table_exists('tbl_google_drive_folders')) {
                return null;
            }

            $folder = $this->db->select('is_shared, share_settings')
                ->from('tbl_google_drive_folders')
                ->where('folder_id', $folder_id)
                ->where('is_shared', 1)
                ->where('is_active', 1)
                ->limit(1)
                ->get()
                ->row();

            if ($folder && $folder->share_settings) {
                $share_settings = json_decode($folder->share_settings, true);

                if (is_array($share_settings) && isset($share_settings['members']) && is_array($share_settings['members'])) {
                    foreach ($share_settings['members'] as $shared_member) {
                        if (isset($shared_member['member_id']) && $shared_member['member_id'] == $member->m_id) {
                            $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;

                            return [
                                'access_level' => $shared_member['permission'] ?? 'read_only',
                                'can_upload' => in_array($shared_member['permission'] ?? 'read_only', ['write', 'admin']),
                                'can_create_folder' => ($shared_member['permission'] ?? 'read_only') === 'admin',
                                'can_share' => false,
                                'can_delete' => ($shared_member['permission'] ?? 'read_only') === 'admin',
                                'can_download' => !$is_trial,
                                'permission_source' => 'user_shared',
                                'granted_by' => $shared_member['granted_by'] ?? 'Unknown',
                                'granted_at' => $shared_member['granted_at'] ?? date('Y-m-d H:i:s'),
                                'expires_at' => $shared_member['expires_at'] ?? null,
                                'folder_id' => $folder_id,
                                'member_id' => $member->m_id
                            ];
                        }
                    }
                }
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Get user shared folder permission safe error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 📅 ดึงวันที่สร้างโฟลเดอร์ (Enhanced Safe)
     */
    private function get_folder_created_date_safe($folder_id)
    {
        try {
            if (!$this->db) {
                return date('Y-m-d H:i:s');
            }

            $storage_mode = isset($this->storage_mode) ? $this->storage_mode : 'user_based';

            if ($storage_mode === 'centralized') {
                if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                    $folder = $this->db->select('created_at')
                        ->from('tbl_google_drive_system_folders')
                        ->where('folder_id', $folder_id)
                        ->limit(1)
                        ->get()
                        ->row();
                    return $folder ? $folder->created_at : date('Y-m-d H:i:s');
                }
            } else {
                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $folder = $this->db->select('created_at')
                        ->from('tbl_google_drive_folders')
                        ->where('folder_id', $folder_id)
                        ->limit(1)
                        ->get()
                        ->row();
                    return $folder ? $folder->created_at : date('Y-m-d H:i:s');
                }
            }

            return date('Y-m-d H:i:s');

        } catch (Exception $e) {
            log_message('error', 'Get folder created date safe error: ' . $e->getMessage());
            return date('Y-m-d H:i:s');
        }
    }

    /**
     * 👤 ดึงชื่อ Member (Enhanced Safe)
     */
    private function get_member_name_safe($member_id)
    {
        try {
            if (!$this->db || !$member_id) {
                return 'Unknown';
            }

            $member = $this->db->select('m_fname, m_lname')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->limit(1)
                ->get()
                ->row();

            return $member ? ($member->m_fname . ' ' . $member->m_lname) : 'Unknown';

        } catch (Exception $e) {
            log_message('error', 'Get member name safe error: ' . $e->getMessage());
            return 'Unknown';
        }
    }

    /**
     * 📁 สร้างโฟลเดอร์ใหม่ (รองรับทั้ง Centralized และ User-based Mode)
     * ✅ แก้ไข: เพิ่มการแปลง parent_id='root' แบบ Dynamic
     */
    public function create_folder()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            // ✅ Basic validation
            $member_id = $this->session->userdata('m_id');
            $folder_name = trim($this->input->post('folder_name'));
            $parent_id = $this->input->post('parent_id');

            log_message('info', sprintf(
                "📁 Create folder request: member=%d, name=%s, parent=%s, storage_mode=%s",
                $member_id,
                $folder_name,
                $parent_id ?: 'root',
                $this->storage_mode
            ));

            if (!$member_id) {
                echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            if (empty($folder_name)) {
                echo json_encode(['success' => false, 'message' => 'กรุณาใส่ชื่อโฟลเดอร์']);
                return;
            }

            // ✅ Get member info
            $member = $this->db->select('ref_pid, m_fname, m_lname')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if (!$member) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้']);
                return;
            }

            // ============================================
            // 🔥 [NEW FIX] แปลง parent_id='root' แบบ Dynamic
            // ============================================
            $original_parent_id = $parent_id;

            if ($this->storage_mode === 'centralized') {
                // 📂 Centralized Mode: แปลง 'root' เป็น Organization Drive Root
                if ($parent_id === 'root' || empty($parent_id)) {
                    $org_root = $this->get_organization_root_folder_id();

                    if ($org_root) {
                        $parent_id = $org_root;
                        log_message('info', sprintf(
                            '🔄 [Centralized Mode] Converted parent_id: "%s" → "%s"',
                            $original_parent_id ?: 'empty',
                            $parent_id
                        ));
                    } else {
                        // ❌ Critical: ไม่สามารถหา root ได้
                        log_message('error', '❌ [Centralized Mode] Cannot find Organization Drive root folder');
                        echo json_encode([
                            'success' => false,
                            'message' => 'ไม่สามารถค้นหา Organization Drive ได้ กรุณาติดต่อผู้ดูแลระบบ',
                            'error_detail' => 'Organization Drive root folder not found in database'
                        ]);
                        return;
                    }
                } else {
                    log_message('info', sprintf(
                        '📂 [Centralized Mode] Using specified parent_id: %s',
                        $parent_id
                    ));
                }
            } else {
                // 📂 User-based Mode: ใช้ parent_id ตามที่ผู้ใช้ระบุ
                if ($parent_id === 'root' || empty($parent_id)) {
                    $parent_id = null; // หรือใช้ user's personal root folder
                    log_message('info', '📂 [User-based Mode] Using user root (parent_id=null)');
                }
            }
            // ============================================
            // [END FIX]
            // ============================================

            // ✅ Get access token
            $access_token = $this->get_valid_access_token();
            if (!$access_token) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถเชื่อมต่อ Google Drive ได้']);
                return;
            }

            // ✅ Create Google Drive folder (ตอนนี้ parent_id ถูกต้องแล้ว)
            $create_result = $this->create_google_drive_folder($folder_name, $parent_id, $access_token);
            if (!$create_result || !$create_result['success']) {
                $error_msg = isset($create_result['error']) ? $create_result['error'] : 'Unknown error';
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้: ' . $error_msg]);
                return;
            }

            $new_folder_id = $create_result['folder_id'];
            $web_view_link = $create_result['web_view_link'] ?? '';

            log_message('info', sprintf('✅ Google Drive folder created: %s', $new_folder_id));

            // ✅ เช็ค storage_mode แล้วบันทึกลง Table ที่ถูกต้อง
            $db_result = false;

            if ($this->storage_mode === 'centralized') {
                // ✅ CENTRALIZED MODE: บันทึกลง tbl_google_drive_system_folders
                log_message('info', '💾 [Centralized Mode] Saving to tbl_google_drive_system_folders');

                $folder_data = [
                    'folder_name' => $folder_name,
                    'folder_id' => $new_folder_id,
                    'parent_folder_id' => $parent_id, // ใช้ parent_id ที่แปลงแล้ว
                    'folder_type' => 'system',
                    'permission_level' => 'restricted',
                    'folder_description' => "สร้างโดย {$member->m_fname} {$member->m_lname}",
                    'folder_url' => $web_view_link,
                    'storage_quota' => 5368709120, // 1GB
                    'storage_used' => 0,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => $member_id
                ];

                $db_result = $this->db->insert('tbl_google_drive_system_folders', $folder_data);

            } else {
                // ✅ USER-BASED MODE: บันทึกลง tbl_google_drive_folders
                log_message('info', '💾 [User-based Mode] Saving to tbl_google_drive_folders');

                $folder_data = [
                    'member_id' => $member_id,
                    'position_id' => $member->ref_pid,
                    'folder_id' => $new_folder_id,
                    'folder_name' => $folder_name,
                    'folder_type' => 'position',
                    'is_shared' => 0,
                    'parent_folder_id' => ($parent_id === 'root' || empty($parent_id)) ? null : $parent_id,
                    'folder_url' => $web_view_link,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => $member_id,
                    'updated_by' => $member_id,
                    'is_system_folder' => 0,
                    'migration_status' => 'migrated'
                ];

                $db_result = $this->db->insert('tbl_google_drive_folders', $folder_data);
            }

            if (!$db_result) {
                $db_error = $this->db->error();
                log_message('error', '❌ Database insert failed: ' . $db_error['message']);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถบันทึกข้อมูลโฟลเดอร์ได้: ' . $db_error['message']
                ]);
                return;
            }

            log_message('info', '✅ Folder saved to database successfully');

            // ✅ Create permissions
            try {
                $this->create_folder_permissions($new_folder_id, $member_id, $member);
            } catch (Exception $perm_error) {
                log_message('debug', '⚠️ Permission creation error: ' . $perm_error->getMessage());
            }

            // ✅ Success response
            echo json_encode([
                'success' => true,
                'message' => "สร้างโฟลเดอร์ \"{$folder_name}\" สำเร็จ",
                'data' => [
                    'folder_id' => $new_folder_id,
                    'folder_name' => $folder_name,
                    'web_view_link' => $web_view_link,
                    'parent_id' => $parent_id,
                    'original_parent_id' => $original_parent_id,
                    'storage_mode' => $this->storage_mode,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);

            log_message('info', sprintf(
                '🎉 Folder created successfully: %s (ID: %s, Parent: %s, Mode: %s)',
                $folder_name,
                $new_folder_id,
                $parent_id,
                $this->storage_mode
            ));

        } catch (Exception $e) {
            log_message('error', '💥 Create folder exception: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการสร้างโฟลเดอร์: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🔐 สร้าง Permissions สำหรับโฟลเดอร์ใหม่ (FIXED VERSION)
     * ✅ ใช้ tbl_google_drive_member_folder_access สำหรับทุก mode
     * ✅ เพิ่ม duplicate check
     * ✅ แก้ไข permission data ให้ตรงกับ schema
     */
    private function create_folder_permissions($folder_id, $member_id, $member)
    {
        try {
            log_message('info', sprintf(
                '🔐 Creating permissions for folder %s (creator: %d, mode: %s)',
                $folder_id,
                $member_id,
                $this->storage_mode
            ));

            // ✅ ใช้ตารางเดียวสำหรับทุก mode
            $permission_table = 'tbl_google_drive_member_folder_access';

            if (!$this->db->table_exists($permission_table)) {
                log_message('error', sprintf('❌ Permission table %s not found!', $permission_table));
                return false;
            }

            // ✅ Check existing permission
            $existing = $this->db->select('id, access_type, is_active')
                ->from($permission_table)
                ->where('folder_id', $folder_id)
                ->where('member_id', $member_id)
                ->get()
                ->row();

            if ($existing) {
                log_message('info', sprintf(
                    '📋 Permission exists: id=%d, type=%s, active=%d',
                    $existing->id,
                    $existing->access_type,
                    $existing->is_active
                ));

                // Update to admin if not already admin/owner
                if (!in_array($existing->access_type, ['admin', 'owner'])) {
                    log_message('info', '🔄 Upgrading permission to admin');

                    $this->db->where('id', $existing->id)
                        ->update($permission_table, [
                            'access_type' => 'admin',
                            'is_active' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    log_message('info', '✅ Permission upgraded to admin');
                    return true;
                } else {
                    log_message('info', '✅ Permission already admin/owner, no update needed');
                    return true;
                }
            }

            // ✅ Insert new permission (fields ตรงกับ schema)
            $permission_data = [
                'folder_id' => $folder_id,
                'member_id' => $member_id,
                'access_type' => 'admin',  // ใช้ admin แทน owner (ถ้า ENUM ไม่มี owner)
                'granted_by' => $member_id,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // เพิ่ม granted_at ถ้า column มีจริง
            $columns = $this->db->list_fields($permission_table);
            if (in_array('granted_at', $columns)) {
                $permission_data['granted_at'] = date('Y-m-d H:i:s');
            }

            log_message('info', '💾 Inserting new permission record');
            log_message('debug', 'Permission data: ' . json_encode($permission_data));

            $perm_result = $this->db->insert($permission_table, $permission_data);

            if ($perm_result) {
                $permission_id = $this->db->insert_id();
                log_message('info', sprintf(
                    '✅ Permission created successfully: id=%d, table=%s',
                    $permission_id,
                    $permission_table
                ));
                return true;
            } else {
                $db_error = $this->db->error();
                log_message('error', sprintf(
                    '❌ Permission creation failed: %s (code: %d)',
                    $db_error['message'],
                    $db_error['code']
                ));
                return false;
            }

        } catch (Exception $e) {
            log_message('error', '💥 create_folder_permissions exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * 🔍 ดึง Organization Drive Root ID แบบ Dynamic
     */
    /**
     * 🔍 ดึง Organization Drive Root ID (FINAL VERSION)
     */
    private function get_organization_root_folder_id()
    {
        // ============================================
        // 🚀 Cache Level 1: Static Memory
        // ============================================
        static $cached_root_id = null;
        if ($cached_root_id !== null) {
            log_message('debug', '⚡ [L1 Cache] Using static cached root ID: ' . $cached_root_id);
            return $cached_root_id;
        }

        // ============================================
        // 🚀 Cache Level 2: Session (with Validation)
        // ============================================
        $session_root = $this->session->userdata('org_drive_root_id');
        if (!empty($session_root)) {
            // ✅ Validate: ห้าม cache Departments folder!
            if ($session_root === '14m3EEFnPOlg2DEu60gkeXd1jg_NE6_uf') {
                log_message('warning', '⚠️ [L2 Cache] Invalid cached value (Departments folder), clearing...');
                $this->session->unset_userdata('org_drive_root_id');
                // Continue to query
            } else {
                log_message('debug', '⚡ [L2 Cache] Using session cached root ID: ' . $session_root);
                $cached_root_id = $session_root;
                return $cached_root_id;
            }
        }

        try {
            // ============================================
            // ✅ Method 1: Query จาก Main Folders (Primary)
            // ============================================
            log_message('info', '🔍 [Method 1] Querying from main folders...');

            $main_folders = ['Admin', 'Departments', 'Shared', 'Users'];

            // 🔥 FIX: เพิ่ม COUNT(*) และ ORDER BY
            $result = $this->db->select('parent_folder_id, COUNT(*) as folder_count')
                ->from('tbl_google_drive_system_folders')
                ->where_in('folder_name', $main_folders)
                ->where('parent_folder_id IS NOT NULL', null, false)
                ->where('is_active', 1)
                ->group_by('parent_folder_id')
                ->having('COUNT(*) >=', 3) // อย่างน้อย 3 จาก 4 folders
                ->order_by('folder_count', 'DESC') // 🔥 เพิ่ม ORDER BY
                ->limit(1)
                ->get()
                ->row();

            if ($result && !empty($result->parent_folder_id)) {
                $root_id = $result->parent_folder_id;

                // 🔥 FIX: Validate ก่อน cache
                if ($root_id === '14m3EEFnPOlg2DEu60gkeXd1jg_NE6_uf') {
                    log_message('error', '❌ [Method 1] Got Departments folder! This should not happen. Skipping to Method 2...');
                    // Don't cache, continue to Method 2
                } else {
                    // ✅ ปลอดภัย - บันทึกลง cache
                    $cached_root_id = $root_id;
                    $this->session->set_userdata('org_drive_root_id', $root_id);

                    log_message('info', sprintf(
                        '✅ [Method 1] Found Organization Drive root: %s (used by %d main folders)',
                        $root_id,
                        $result->folder_count
                    ));

                    return $root_id;
                }
            } else {
                log_message('warning', '⚠️ [Method 1] No result found or COUNT < 3');
            }

            // ============================================
            // ⚠️ Method 2: Query จาก Folder Path (Fallback 1)
            // ============================================
            log_message('warning', '⚠️ [Method 1] Failed, trying Method 2...');

            // หา parent_folder_id ที่มี folder_path = "/Organization Drive/[folder]"
            // และไม่มี subfolder (level 1 เท่านั้น)
            $fallback = $this->db->select('parent_folder_id, COUNT(*) as folder_count')
                ->from('tbl_google_drive_system_folders')
                ->where('folder_path LIKE', '/Organization Drive/%')
                ->where('folder_path NOT LIKE', '/Organization Drive/%/%') // ไม่เอา subfolder
                ->where('parent_folder_id IS NOT NULL', null, false)
                ->where('is_active', 1)
                ->group_by('parent_folder_id')
                ->order_by('folder_count', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($fallback && !empty($fallback->parent_folder_id)) {
                $root_id = $fallback->parent_folder_id;

                // 🔥 FIX: Validate ก่อน cache
                if ($root_id === '14m3EEFnPOlg2DEu60gkeXd1jg_NE6_uf') {
                    log_message('error', '❌ [Method 2] Got Departments folder! Skipping to Method 3...');
                    // Don't cache, continue to Method 3
                } else {
                    // ✅ ปลอดภัย - บันทึกลง cache
                    $cached_root_id = $root_id;
                    $this->session->set_userdata('org_drive_root_id', $root_id);

                    log_message('info', sprintf(
                        '✅ [Method 2 - Fallback] Found Organization Drive root: %s (used by %d level-1 folders)',
                        $root_id,
                        $fallback->folder_count
                    ));

                    return $root_id;
                }
            } else {
                log_message('warning', '⚠️ [Method 2] No result found');
            }

            // ============================================
            // ❌ Method 3: Query แบบเดิม (Last Resort - อันตราย!)
            // ============================================
            log_message('warning', '⚠️ [Method 2] Failed, using Method 3 (Last Resort)...');

            $last_resort = $this->db->select('parent_folder_id, COUNT(*) as folder_count')
                ->from('tbl_google_drive_system_folders')
                ->where('parent_folder_id IS NOT NULL', null, false)
                ->where('is_active', 1)
                ->group_by('parent_folder_id')
                ->order_by('folder_count', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($last_resort && !empty($last_resort->parent_folder_id)) {
                $root_id = $last_resort->parent_folder_id;

                log_message('warning', sprintf(
                    '⚠️ [Method 3 - Last Resort] Using root: %s (used by %d folders) - May not be correct!',
                    $root_id,
                    $last_resort->folder_count
                ));

                // ⚠️ ไม่ cache เพราะอาจผิด
                return $root_id;
            }

            // ❌ ไม่พบ root folder เลย
            log_message('error', '❌ Could not find Organization Drive root from any method');
            return null;

        } catch (Exception $e) {
            log_message('error', '❌ Error getting organization root: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔄 Clear Root Folder Cache (เพิ่มใหม่ - สำหรับ Admin)
     */
    public function clear_root_cache()
    {
        header('Content-Type: application/json');

        // Optional: ตรวจสอบ admin (ถ้าต้องการ)
        // if (!$this->session->userdata('is_admin')) {
        //     echo json_encode(['success' => false, 'message' => 'Access denied']);
        //     return;
        // }

        // Clear cache
        $this->session->unset_userdata('org_drive_root_id');

        log_message('info', '🗑️ Root folder cache cleared manually');

        echo json_encode([
            'success' => true,
            'message' => 'Cache cleared successfully',
            'cleared_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 🔄 Clear Root Folder Cache (Private method)
     */
    private function clear_root_folder_cache()
    {
        $this->session->unset_userdata('org_drive_root_id');
        log_message('info', '🔄 Root folder cache cleared');
    }


    /**
     * 🗑️ ลบ Permission Records สำหรับโฟลเดอร์
     * ✅ รองรับ Recursive (ลบ permissions ของ subfolders ด้วย)
     * 
     * @param string $folder_id Google Drive Folder ID
     * @param bool $recursive ลบ permissions ของ subfolder ด้วยหรือไม่
     * @return bool
     */
    private function remove_folder_permissions($folder_id, $recursive = true)
    {
        try {
            log_message('info', sprintf(
                '🗑️ Removing permissions for folder %s (recursive: %s)',
                $folder_id,
                $recursive ? 'yes' : 'no'
            ));

            $permission_table = 'tbl_google_drive_member_folder_access';

            if (!$this->db->table_exists($permission_table)) {
                log_message('info', sprintf('⚠️ Permission table %s not found, skipping', $permission_table));
                return false;
            }

            $folder_ids_to_delete = [$folder_id];

            // ✅ ถ้า recursive = true ให้หา subfolder IDs
            if ($recursive) {
                log_message('info', '🔍 Finding subfolders for recursive permission deletion...');

                // Query subfolders จาก system_folders
                $subfolders_system = $this->db->select('folder_id')
                    ->from('tbl_google_drive_system_folders')
                    ->where('parent_folder_id', $folder_id)
                    ->where('is_active', 1)
                    ->get()
                    ->result_array();

                // Query subfolders จาก user folders
                $subfolders_user = $this->db->select('folder_id')
                    ->from('tbl_google_drive_folders')
                    ->where('parent_folder_id', $folder_id)
                    ->where('is_active', 1)
                    ->get()
                    ->result_array();

                // Merge subfolder IDs
                $all_subfolders = array_merge($subfolders_system, $subfolders_user);
                $subfolder_ids = array_column($all_subfolders, 'folder_id');

                if (count($subfolder_ids) > 0) {
                    log_message('info', sprintf('📁 Found %d subfolders', count($subfolder_ids)));
                    $folder_ids_to_delete = array_merge($folder_ids_to_delete, $subfolder_ids);
                } else {
                    log_message('info', '📁 No subfolders found');
                }
            }

            // ✅ ลบ permission records
            log_message('info', sprintf(
                '💾 Deleting permissions for %d folder(s)',
                count($folder_ids_to_delete)
            ));

            $this->db->where_in('folder_id', $folder_ids_to_delete)
                ->delete($permission_table);

            $affected_rows = $this->db->affected_rows();

            log_message('info', sprintf(
                '✅ Deleted %d permission record(s)',
                $affected_rows
            ));

            return true;

        } catch (Exception $e) {
            log_message('error', '💥 remove_folder_permissions exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }


    // ✅ เพิ่ม method ทดสอบการ insert ข้อมูล
    public function test_insert_folder()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $member_id = $this->session->userdata('m_id');

            if (!$member_id) {
                echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            // Get member info
            $member = $this->db->select('ref_pid, m_fname, m_lname')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if (!$member) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้']);
                return;
            }

            // Test data
            $test_folder_data = [
                'member_id' => $member_id,
                'position_id' => $member->ref_pid,
                'folder_id' => 'test_folder_' . time(),
                'folder_name' => 'Test Folder ' . date('Y-m-d H:i:s'),
                'parent_id' => null,
                'folder_type' => 'position',
                'is_shared' => 0,
                'parent_folder_id' => null,
                'folder_url' => 'https://drive.google.com/test',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => $member_id,
                'updated_by' => $member_id,
                'is_system_folder' => 0,
                'migration_status' => 'migrated'
            ];

            log_message('debug', 'Test insert data: ' . json_encode($test_folder_data));

            // Try insert
            $result = $this->db->insert('tbl_google_drive_folders', $test_folder_data);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'ทดสอบการ insert สำเร็จ',
                    'data' => [
                        'insert_id' => $this->db->insert_id(),
                        'test_data' => $test_folder_data
                    ]
                ]);
            } else {
                $db_error = $this->db->error();
                echo json_encode([
                    'success' => false,
                    'message' => 'ทดสอบการ insert ล้มเหลว',
                    'error' => $db_error,
                    'test_data' => $test_folder_data
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }



    /**
     * 🛡️ Ultimate Safe Permission Grant
     */
    private function grant_folder_permission_ultimate_safe($folder_id, $member_id, $access_type = 'owner', $source = 'system')
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                log_message('debug', 'Permission table does not exist - skipping');
                return true; // ไม่ให้ error
            }

            // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
            $existing = $this->db->where([
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'is_active' => 1
            ])->get('tbl_google_drive_member_folder_access')->row();

            $permission_data = [
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'permission_source' => $source,
                'permission_mode' => 'direct',
                'granted_by' => $this->session->userdata('m_id'),
                'granted_by_name' => $this->get_current_member_name_safe(),
                'granted_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($existing) {
                return $this->db->where('id', $existing->id)->update('tbl_google_drive_member_folder_access', $permission_data);
            } else {
                return $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
            }

        } catch (Exception $e) {
            log_message('error', 'Ultimate safe permission grant error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🛡️ Safe Get Parent Permissions
     */
    private function get_parent_folder_permissions_safe($parent_id)
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                return [];
            }

            return $this->db->select('member_id, access_type, permission_source, granted_by, granted_by_name, granted_at, expires_at')
                ->from('tbl_google_drive_member_folder_access')
                ->where('folder_id', $parent_id)
                ->where('is_active', 1)
                ->group_start()
                ->where('expires_at IS NULL')
                ->or_where('expires_at >', date('Y-m-d H:i:s'))
                ->group_end()
                ->get()
                ->result();

        } catch (Exception $e) {
            log_message('error', 'Get parent folder permissions safe error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 🛡️ Safe Create Inherited Permission
     */
    private function create_inherited_permission_safe($parent_permission, $new_folder_id, $parent_id)
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                return false;
            }

            // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
            $existing = $this->db->where([
                'member_id' => $parent_permission->member_id,
                'folder_id' => $new_folder_id,
                'is_active' => 1
            ])->get('tbl_google_drive_member_folder_access')->row();

            if ($existing) {
                return true; // มีอยู่แล้ว
            }

            $inherited_permission = [
                'member_id' => $parent_permission->member_id,
                'folder_id' => $new_folder_id,
                'access_type' => $parent_permission->access_type,
                'permission_source' => $parent_permission->permission_source,
                'permission_mode' => 'inherited',
                'parent_folder_id' => $parent_id,
                'inherit_from_parent' => 1,
                'apply_to_children' => 1,
                'granted_by' => $parent_permission->granted_by ?: $this->session->userdata('m_id'),
                'granted_by_name' => $parent_permission->granted_by_name ?: 'System',
                'granted_at' => date('Y-m-d H:i:s'),
                'expires_at' => $parent_permission->expires_at,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->insert('tbl_google_drive_member_folder_access', $inherited_permission);

        } catch (Exception $e) {
            log_message('error', 'Create inherited permission safe error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🛡️ Safe Grant Admin Permissions
     */
    private function grant_admin_permissions_to_folder_safe($folder_id)
    {
        try {
            if (!$this->db->table_exists('tbl_member') || !$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                return;
            }

            $admin_positions = [1, 2];

            foreach ($admin_positions as $position_id) {
                $admins = $this->db->select('m_id')
                    ->from('tbl_member')
                    ->where('ref_pid', $position_id)
                    ->where('m_status', '1')
                    ->get()
                    ->result();

                foreach ($admins as $admin) {
                    $this->grant_folder_permission_ultimate_safe($folder_id, $admin->m_id, 'admin', 'position');
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Grant admin permissions safe error: ' . $e->getMessage());
        }
    }

    /**
     * 🛡️ Safe Get Current Member Name
     */
    private function get_current_member_name_safe()
    {
        try {
            $member_id = $this->session->userdata('m_id');
            if (!$member_id || !$this->db->table_exists('tbl_member')) {
                return 'System';
            }

            $member = $this->db->select('m_fname, m_lname')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->limit(1)
                ->get()
                ->row();

            return $member ? ($member->m_fname . ' ' . $member->m_lname) : 'System';

        } catch (Exception $e) {
            return 'System';
        }
    }


    /**
     * 🔗 สืบทอดสิทธิ์จาก Parent Folder
     */
    private function inherit_parent_folder_permissions($new_folder_id, $parent_id, $creator_member_id)
    {
        $result = [
            'inherited_count' => 0,
            'sources' => [],
            'has_owner_permission' => false,
            'creator_access_type' => 'owner'
        ];

        try {
            // ถ้าเป็น root folder ไม่ต้องสืบทอดสิทธิ์
            if (empty($parent_id) || $parent_id === 'root' || $parent_id === 'null') {
                log_message('debug', "No parent folder to inherit from (root level)");
                return $result;
            }

            // ดึงสิทธิ์จาก parent folder ที่มี apply_to_children = 1
            $parent_permissions = $this->db->select('member_id, access_type, permission_source, granted_by, granted_by_name, expires_at')
                ->from('tbl_google_drive_member_folder_access')
                ->where('folder_id', $parent_id)
                ->where('is_active', 1)
                ->where('apply_to_children', 1)
                ->group_start()
                ->where('expires_at IS NULL')
                ->or_where('expires_at >', date('Y-m-d H:i:s'))
                ->group_end()
                ->get()
                ->result();

            log_message('debug', "Found " . count($parent_permissions) . " inheritable permissions from parent folder {$parent_id}");

            foreach ($parent_permissions as $permission) {
                // ตรวจสอบว่า member นี้มีสิทธิ์ในโฟลเดอร์ใหม่อยู่แล้วหรือไม่
                $existing = $this->db->select('id, access_type')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('member_id', $permission->member_id)
                    ->where('folder_id', $new_folder_id)
                    ->get()
                    ->row();

                if ($existing) {
                    // ถ้ามีอยู่แล้ว ให้เลือกสิทธิ์ที่สูงกว่า
                    $current_level = $this->get_permission_level($existing->access_type);
                    $inherited_level = $this->get_permission_level($permission->access_type);

                    if ($inherited_level > $current_level) {
                        // อัปเดตเป็นสิทธิ์ที่สูงกว่า
                        $this->db->where('id', $existing->id)
                            ->update('tbl_google_drive_member_folder_access', [
                                'access_type' => $permission->access_type,
                                'permission_mode' => 'combined',
                                'parent_folder_id' => $parent_id,
                                'inherit_from_parent' => 1,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);

                        log_message('debug', "Updated permission for member {$permission->member_id} to {$permission->access_type} (upgraded from {$existing->access_type})");
                    }
                    continue;
                }

                // สร้างสิทธิ์ใหม่ที่สืบทอดมา
                $inherited_permission = [
                    'member_id' => $permission->member_id,
                    'folder_id' => $new_folder_id,
                    'access_type' => $permission->access_type,
                    'permission_source' => $permission->permission_source,
                    'permission_mode' => 'inherited',
                    'parent_folder_id' => $parent_id,
                    'inherit_from_parent' => 1,
                    'apply_to_children' => 1, // สืบทอดต่อไปยัง subfolder
                    'granted_by' => $permission->granted_by,
                    'granted_by_name' => $permission->granted_by_name,
                    'granted_at' => date('Y-m-d H:i:s'),
                    'expires_at' => $permission->expires_at,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $insert_result = $this->db->insert('tbl_google_drive_member_folder_access', $inherited_permission);

                if ($insert_result) {
                    $result['inherited_count']++;
                    $result['sources'][] = $permission->permission_source;

                    // ตรวจสอบว่า creator มีสิทธิ์ owner หรือไม่
                    if ($permission->member_id == $creator_member_id) {
                        $result['has_owner_permission'] = true;
                        $result['creator_access_type'] = $permission->access_type;
                    }

                    log_message('debug', "Inherited {$permission->access_type} permission for member {$permission->member_id} from parent folder");
                } else {
                    log_message('error', "Failed to inherit permission for member {$permission->member_id}: " . $this->db->error()['message']);
                }
            }

            // ถ้าไม่มีสิทธิ์ใน parent folder หรือไม่มี apply_to_children
            // ให้ลองสืบทอดจาก parent ของ parent (recursive)
            if ($result['inherited_count'] === 0) {
                $grandparent_id = $this->get_parent_folder_id($parent_id);
                if ($grandparent_id && $grandparent_id !== 'root') {
                    log_message('debug', "No inheritable permissions from immediate parent, checking grandparent {$grandparent_id}");
                    $grandparent_result = $this->inherit_parent_folder_permissions($new_folder_id, $grandparent_id, $creator_member_id);

                    // รวมผลลัพธ์
                    $result['inherited_count'] += $grandparent_result['inherited_count'];
                    $result['sources'] = array_merge($result['sources'], $grandparent_result['sources']);
                    if ($grandparent_result['has_owner_permission']) {
                        $result['has_owner_permission'] = true;
                        $result['creator_access_type'] = $grandparent_result['creator_access_type'];
                    }
                }
            }

            // ลบ sources ที่ซ้ำ
            $result['sources'] = array_unique($result['sources']);

            log_message('info', "Permission inheritance completed for folder {$new_folder_id}: {$result['inherited_count']} permissions inherited");

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Inherit parent folder permissions error: ' . $e->getMessage());
            return $result;
        }
    }


    private function save_folder_hierarchy($folder_id, $parent_id)
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_folder_hierarchy')) {
                log_message('info', 'Table tbl_google_drive_folder_hierarchy does not exist - skipping hierarchy save');
                return false;
            }

            // ถ้าเป็น root folder ไม่ต้องบันทึก hierarchy
            if (empty($parent_id) || $parent_id === 'root' || $parent_id === 'null') {
                return false;
            }

            // ตรวจสอบว่ามีอยู่แล้วหรือไม่
            $existing = $this->db->where([
                'parent_folder_id' => $parent_id,
                'child_folder_id' => $folder_id
            ])->get('tbl_google_drive_folder_hierarchy')->row();

            if (!$existing) {
                // คำนวณ depth level
                $parent_depth = $this->get_folder_depth($parent_id);
                $depth_level = $parent_depth + 1;

                // สร้าง folder path
                $folder_path = $this->build_folder_path($parent_id, $folder_id);

                $hierarchy_data = [
                    'parent_folder_id' => $parent_id,
                    'child_folder_id' => $folder_id,
                    'folder_path' => json_encode($folder_path, JSON_UNESCAPED_UNICODE),
                    'depth_level' => $depth_level,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $result = $this->db->insert('tbl_google_drive_folder_hierarchy', $hierarchy_data);

                if ($result) {
                    log_message('debug', "Folder hierarchy saved: {$parent_id} -> {$folder_id} (depth: {$depth_level})");
                    return true;
                } else {
                    log_message('error', 'Failed to save folder hierarchy: ' . $this->db->error()['message']);
                    return false;
                }
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Save folder hierarchy error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📏 คำนวณความลึกของโฟลเดอร์ (แก้ไข)
     */
    private function get_folder_depth($folder_id)
    {
        try {
            if (empty($folder_id) || $folder_id === 'root') {
                return 0;
            }

            // ตรวจสอบว่าตารางมีอยู่จริง
            if (!$this->db->table_exists('tbl_google_drive_folder_hierarchy')) {
                // ถ้าไม่มีตาราง hierarchy ให้คำนวณแบบ manual
                return $this->calculate_folder_depth_manual($folder_id);
            }

            $hierarchy = $this->db->select('depth_level')
                ->from('tbl_google_drive_folder_hierarchy')
                ->where('child_folder_id', $folder_id)
                ->where('is_active', 1)
                ->limit(1)
                ->get()
                ->row();

            if ($hierarchy) {
                return $hierarchy->depth_level;
            }

            // ถ้าไม่มีข้อมูลใน hierarchy ให้คำนวณใหม่
            return $this->calculate_folder_depth_manual($folder_id);

        } catch (Exception $e) {
            log_message('error', 'Get folder depth error: ' . $e->getMessage());
            return 0;
        }
    }


    /**
     * 🧮 คำนวณความลึกแบบ Manual
     */
    private function calculate_folder_depth_manual($folder_id, $current_depth = 0)
    {
        try {
            // ป้องกัน infinite loop
            if ($current_depth > 10) {
                log_message('debug', "Maximum folder depth reached for folder {$folder_id}");
                return $current_depth;
            }

            $parent_id = $this->get_parent_folder_id($folder_id);

            if (empty($parent_id) || $parent_id === 'root') {
                return $current_depth + 1;
            }

            return $this->calculate_folder_depth_manual($parent_id, $current_depth + 1);

        } catch (Exception $e) {
            log_message('error', 'Calculate folder depth manual error: ' . $e->getMessage());
            return $current_depth;
        }
    }



    /**
     * 🛤️ สร้าง Folder Path (แก้ไข - รองรับ 1 หรือ 2 พารามิเตอร์)
     */
    private function build_folder_path($parent_id, $current_folder_id = null)
    {
        try {
            $path = [];

            // ถ้าส่งมาแค่ 1 parameter ให้ถือว่าเป็น current_folder_id
            if ($current_folder_id === null) {
                $current_folder_id = $parent_id;
                $parent_id = $this->get_parent_folder_id($current_folder_id);
            }

            // ดึง path ของ parent
            if ($parent_id && $parent_id !== 'root') {
                $parent_hierarchy = $this->db->select('folder_path')
                    ->from('tbl_google_drive_folder_hierarchy')
                    ->where('child_folder_id', $parent_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($parent_hierarchy && $parent_hierarchy->folder_path) {
                    $parent_path = json_decode($parent_hierarchy->folder_path, true);
                    if (is_array($parent_path)) {
                        $path = $parent_path;
                    }
                } else {
                    // ถ้าไม่มี hierarchy ของ parent ให้สร้างแบบ recursive
                    $path = $this->build_folder_path_recursive($parent_id);
                }
            }

            // เพิ่ม current folder เข้าไปใน path (ถ้ามี)
            if ($current_folder_id) {
                $path[] = $current_folder_id;
            }

            return $path;

        } catch (Exception $e) {
            log_message('error', 'Build folder path error: ' . $e->getMessage());
            return $current_folder_id ? [$current_folder_id] : [];
        }
    }

    /**
     * 🔄 สร้าง Folder Path แบบ Recursive
     */
    private function build_folder_path_recursive($folder_id)
    {
        try {
            $path = [];

            if (empty($folder_id) || $folder_id === 'root') {
                return $path;
            }

            // หา parent ของ folder นี้
            $parent_id = $this->get_parent_folder_id($folder_id);

            // ถ้ามี parent ให้ไปสร้าง path ของ parent ก่อน
            if ($parent_id && $parent_id !== 'root') {
                $path = $this->build_folder_path_recursive($parent_id);
            }

            // เพิ่ม folder ปัจจุบันเข้าไปใน path
            $path[] = $folder_id;

            return $path;

        } catch (Exception $e) {
            log_message('error', 'Build folder path recursive error: ' . $e->getMessage());
            return [$folder_id];
        }
    }

    /**
     * 📁 ดึง Parent Folder ID (ปรับปรุง - เพิ่ม System Folders Support)
     */
    private function get_parent_folder_id($folder_id)
    {
        try {
            if (empty($folder_id) || $folder_id === 'root') {
                return null;
            }

            // ✅ 1. ดึงจาก local cache ก่อน (tbl_google_drive_folder_hierarchy)
            if ($this->db->table_exists('tbl_google_drive_folder_hierarchy')) {
                $cached_parent = $this->db->select('parent_folder_id')
                    ->from('tbl_google_drive_folder_hierarchy')
                    ->where('child_folder_id', $folder_id)
                    ->where('is_active', 1)
                    ->limit(1)
                    ->get()
                    ->row();

                if ($cached_parent) {
                    log_message('debug', "Parent found in cache: {$cached_parent->parent_folder_id}");
                    return $cached_parent->parent_folder_id;
                }
            }

            // ✅ 2. ดึงจาก tbl_google_drive_system_folders (Centralized)
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $system_folder = $this->db->select('parent_folder_id')
                    ->from('tbl_google_drive_system_folders')
                    ->where('folder_id', $folder_id)
                    ->where('is_active', 1)
                    ->limit(1)
                    ->get()
                    ->row();

                if ($system_folder) {
                    log_message('debug', "Parent found in system folders: " . ($system_folder->parent_folder_id ?? 'null'));
                    return $system_folder->parent_folder_id;
                }
            }

            // ✅ 3. ดึงจาก tbl_google_drive_folders (User-based)
            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $folder_info = $this->db->select('parent_folder_id')
                    ->from('tbl_google_drive_folders')
                    ->where('folder_id', $folder_id)
                    ->where('is_active', 1)
                    ->limit(1)
                    ->get()
                    ->row();

                if ($folder_info) {
                    log_message('debug', "Parent found in user folders: " . ($folder_info->parent_folder_id ?? 'null'));
                    return $folder_info->parent_folder_id;
                }
            }

            // ✅ 4. สุดท้าย ดึงจาก Google Drive API (ถ้าจำเป็น)
            $access_token = $this->get_valid_access_token();
            if ($access_token) {
                try {
                    $folder_detail = $this->get_google_drive_folder_info($access_token, $folder_id);

                    if ($folder_detail && isset($folder_detail['parents']) && count($folder_detail['parents']) > 0) {
                        $parent_id = $folder_detail['parents'][0];

                        log_message('info', "Parent found via Google API: {$parent_id}");

                        // บันทึกลง cache (ถ้าตารางมีอยู่)
                        if ($this->db->table_exists('tbl_google_drive_folder_hierarchy')) {
                            $this->save_folder_hierarchy($folder_id, $parent_id);
                        }

                        return $parent_id;
                    }
                } catch (Exception $api_error) {
                    log_message('error', 'Google Drive API error in get_parent_folder_id: ' . $api_error->getMessage());
                }
            }

            log_message('debug', "No parent folder found for: {$folder_id}");
            return null;

        } catch (Exception $e) {
            log_message('error', 'Get parent folder ID error: ' . $e->getMessage());
            return null;
        }
    }




    /**
     * 📊 ดึงระดับสิทธิ์เป็นตัวเลข (สำหรับเปรียบเทียบ)
     */
    private function get_permission_level($access_type)
    {
        $levels = [
            'read' => 1,
            'write' => 2,
            'admin' => 3,
            'owner' => 4
        ];

        return $levels[$access_type] ?? 0;
    }



    /**
     * 🧹 ล้าง Output Buffer อย่างสมบูรณ์
     */
    private function clear_output_buffer()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }



    /**
     * ✅ ส่ง JSON Success แบบปลอดภัย
     */
    private function safe_json_success($data = [], $message = 'Success')
    {
        try {
            $this->clear_output_buffer();

            $response = [
                'success' => true,
                'message' => $message,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8', true);
            header('Cache-Control: no-cache, no-store, must-revalidate', true);
            header('Pragma: no-cache', true);
            header('X-Content-Type-Options: nosniff', true);

            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // Force output และ stop execution
            if (ob_get_level()) {
                ob_end_flush();
            }
            exit();
        } catch (Exception $e) {
            log_message('error', 'Safe JSON Success error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'JSON Error'], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    /**
     * ❌ ส่ง JSON Error แบบปลอดภัย
     */
    private function safe_json_error($message = 'Error', $status_code = 400, $debug_data = [])
    {
        try {
            $this->clear_output_buffer();

            $response = [
                'success' => false,
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // เพิ่ม debug info เฉพาะ development
            if (ENVIRONMENT === 'development' && !empty($debug_data)) {
                $response['debug'] = $debug_data;
            }

            http_response_code($status_code);
            header('Content-Type: application/json; charset=utf-8', true);
            header('Cache-Control: no-cache, no-store, must-revalidate', true);
            header('Pragma: no-cache', true);
            header('X-Content-Type-Options: nosniff', true);

            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // Force output และ stop execution
            if (ob_get_level()) {
                ob_end_flush();
            }
            exit();
        } catch (Exception $e) {
            log_message('error', 'Safe JSON Error error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Critical JSON Error'], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }


    public function check_create_folder_permission()
    {
        try {
            $member_id = $this->session->userdata('m_id');
            $folder_id = $this->input->post('folder_id');

            // Validate member
            if (!$member_id) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบข้อมูลผู้ใช้'
                    ]));
            }

            // Log for debugging
            log_message('debug', "Check create folder permission - Member: {$member_id}, Folder: " . ($folder_id ?: 'root'));

            // Handle root folder
            if (empty($folder_id) || $folder_id === 'root' || $folder_id === 'null') {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'can_create_folder' => true,
                        'access_type' => 'root',
                        'permission_source' => 'root_folder',
                        'message' => 'สิทธิ์เริ่มต้นสำหรับโฟลเดอร์หลัก',
                        'folder_id' => 'root'
                    ]));
            }

            // ✅ ตรวจสอบว่า folder_id เป็น string ที่ถูกต้อง
            if (!is_string($folder_id) || strlen($folder_id) < 10) {
                log_message('error', "Invalid folder_id format: " . print_r($folder_id, true));
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'รูปแบบ folder ID ไม่ถูกต้อง'
                    ]));
            }

            // ✅ เช็คสิทธิ์จากฐานข้อมูลด้วย try-catch
            try {
                $query = $this->db->select('access_type, permission_source, granted_by_name, granted_at, expires_at')
                    ->where([
                        'member_id' => $member_id,
                        'folder_id' => $folder_id,
                        'is_active' => 1
                    ])
                    ->where('(expires_at IS NULL OR expires_at > NOW())')
                    ->get('tbl_google_drive_member_folder_access');

                // Log query for debugging
                log_message('debug', "Permission query: " . $this->db->last_query());

                $permission = $query->row();

            } catch (Exception $db_error) {
                log_message('error', "Database error in check_create_folder_permission: " . $db_error->getMessage());

                // ส่งกลับ fallback permission
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'can_create_folder' => false,
                        'access_type' => 'error',
                        'permission_source' => 'database_error',
                        'message' => 'เกิดข้อผิดพลาดในฐานข้อมูล กรุณาลองใหม่อีกครั้ง',
                        'folder_id' => $folder_id,
                        'debug_error' => ENVIRONMENT === 'development' ? $db_error->getMessage() : null
                    ]));
            }

            if ($permission) {
                // ✅ มีสิทธิ์ในระบบ
                $valid_create_types = ['write', 'admin', 'owner'];
                $can_create = in_array($permission->access_type, $valid_create_types);

                log_message('debug', "Permission found - Type: {$permission->access_type}, Can create: " . ($can_create ? 'Yes' : 'No'));

                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'can_create_folder' => $can_create,
                        'access_type' => $permission->access_type,
                        'permission_source' => $permission->permission_source,
                        'granted_by' => $permission->granted_by_name,
                        'granted_at' => $permission->granted_at,
                        'expires_at' => $permission->expires_at,
                        'folder_id' => $folder_id,
                        'message' => $can_create ?
                            "มีสิทธิ์สร้างโฟลเดอร์ (access_type: {$permission->access_type})" :
                            "ไม่มีสิทธิ์สร้างโฟลเดอร์ - ต้องการ write, admin หรือ owner (ปัจจุบัน: {$permission->access_type})"
                    ]));

            } else {
                // ❌ ไม่มีสิทธิ์ในระบบ
                log_message('debug', "No permission found for member {$member_id} in folder {$folder_id}");

                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'can_create_folder' => false,
                        'access_type' => 'no_access',
                        'permission_source' => 'none',
                        'folder_id' => $folder_id,
                        'message' => 'ไม่พบสิทธิ์การเข้าถึงโฟลเดอร์นี้ในฐานข้อมูล'
                    ]));
            }

        } catch (Exception $e) {
            // ✅ จัดการ error ทั่วไป
            log_message('error', 'Check create folder permission general error: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());

            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง',
                    'error_type' => 'system_error',
                    'debug_error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
                ]));
        }
    }


    /**
     * ✏️ เปลี่ยนชื่อไฟล์/โฟลเดอร์ (Production Version - No Trial/Mock)
     */
    public function rename_item()
    {
        try {
            // ล้าง output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/json; charset=utf-8');

            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit;
            }

            $item_id = $this->input->post('item_id');
            $item_type = $this->input->post('item_type');
            $new_name = trim($this->input->post('new_name'));
            $original_name = $this->input->post('original_name');

            // ตรวจสอบข้อมูลพื้นฐาน
            if (!$item_id || !$item_type || !$new_name) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                exit;
            }

            // ตรวจสอบชื่อใหม่
            if (strlen($new_name) > 255 || !preg_match('/^[a-zA-Z0-9ก-๙\s\-_.()]+$/', $new_name)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ชื่อไม่ถูกต้องหรือยาวเกินไป']);
                exit;
            }

            // ตรวจสอบสิทธิ์การเปลี่ยนชื่อ
            if (!$this->check_rename_permission($item_id, $item_type)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เปลี่ยนชื่อ']);
                exit;
            }

            // เชื่อมต่อ Google Drive
            $access_token = $this->get_simple_access_token();
            if (!$access_token) {
                $this->log_rename_activity($item_id, $item_type, $original_name, $new_name, 'failed', 'ไม่สามารถเชื่อมต่อ Google Drive ได้');

                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถเชื่อมต่อ Google Drive ได้']);
                exit;
            }

            // เปลี่ยนชื่อใน Google Drive
            $rename_result = $this->rename_google_drive_item($item_id, $new_name, $access_token);

            if ($rename_result['success']) {
                // อัปเดตชื่อในฐานข้อมูลท้องถิ่น
                $this->update_item_name_in_db($item_id, $item_type, $new_name);

                // บันทึก log สำเร็จ
                $this->log_rename_activity($item_id, $item_type, $original_name, $new_name, 'success');

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'เปลี่ยนชื่อสำเร็จ',
                    'data' => [
                        'item_id' => $item_id,
                        'item_type' => $item_type,
                        'new_name' => $new_name,
                        'original_name' => $original_name
                    ]
                ]);
            } else {
                // บันทึก log ความล้มเหลว
                $this->log_rename_activity($item_id, $item_type, $original_name, $new_name, 'failed', $rename_result['error']);

                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $rename_result['error'] ?? 'ไม่สามารถเปลี่ยนชื่อได้'
                ]);
            }
            exit;

        } catch (Exception $e) {
            // บันทึก log exception
            $this->log_rename_activity(
                $item_id ?? 'unknown',
                $item_type ?? 'unknown',
                $original_name ?? 'unknown',
                $new_name ?? 'unknown',
                'error',
                $e->getMessage()
            );

            while (ob_get_level()) {
                ob_end_clean();
            }

            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายใน']);
            exit;
        }
    }

    /**
     * 🔐 ตรวจสอบสิทธิ์การเปลี่ยนชื่อ
     */
    private function check_rename_permission($item_id, $item_type)
    {
        try {
            $member_id = $this->session->userdata('m_id');

            if (!$member_id) {
                return false;
            }

            // หาโฟลเดอร์ที่ item อยู่
            $folder_id = ($item_type === 'folder') ? $item_id : $this->get_file_folder_id($item_id);

            if (!$folder_id) {
                return false;
            }

            // ตรวจสอบสิทธิ์จาก tbl_google_drive_member_folder_access
            $access = $this->db->select('access_type')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $member_id)
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if (!$access) {
                return false;
            }

            // ตรวจสอบว่ามีสิทธิ์เขียนหรือไม่ (สามารถเปลี่ยนชื่อได้)
            return in_array($access->access_type, ['read_write', 'admin', 'owner']);

        } catch (Exception $e) {
            log_message('error', 'Check rename permission error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📝 บันทึก Log การเปลี่ยนชื่อ (Comprehensive Logging)
     */
    private function log_rename_activity($item_id, $item_type, $original_name, $new_name, $status, $error_message = null)
    {
        try {
            $member_id = $this->member_id ?? $this->session->userdata('m_id') ?? 0;
            $timestamp = date('Y-m-d H:i:s');
            $ip_address = $this->input->ip_address();
            $user_agent = $this->input->user_agent();

            $logged_tables = [];

            // 1. บันทึกลง tbl_google_drive_logs (ตารางหลัก)
            if ($this->db->table_exists('tbl_google_drive_logs')) {
                $action_description = "เปลี่ยนชื่อ {$item_type} จาก '{$original_name}' เป็น '{$new_name}'";
                if ($error_message) {
                    $action_description .= " (ล้มเหลว: {$error_message})";
                }

                $log_data = [
                    'member_id' => $member_id,
                    'action_type' => 'rename',
                    'action_description' => $action_description,
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'status' => $status,
                    'error_message' => $error_message,
                    'additional_data' => json_encode([
                        'original_name' => $original_name,
                        'new_name' => $new_name,
                        'item_id' => $item_id,
                        'item_type' => $item_type
                    ]),
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'created_at' => $timestamp
                ];

                if ($this->db->insert('tbl_google_drive_logs', $log_data)) {
                    $logged_tables[] = 'tbl_google_drive_logs';
                }
            }

            // 2. บันทึกลง tbl_google_drive_activity_logs
            if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
                $activity_data = [
                    'member_id' => $member_id,
                    'action_type' => 'rename_' . $item_type,
                    'action_description' => "เปลี่ยนชื่อ {$item_type} ID: {$item_id} จาก '{$original_name}' เป็น '{$new_name}'",
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'created_at' => $timestamp
                ];

                if ($this->db->insert('tbl_google_drive_activity_logs', $activity_data)) {
                    $logged_tables[] = 'tbl_google_drive_activity_logs';
                }
            }

            // 3. บันทึกลง tbl_google_drive_file_activities (ถ้าเป็นไฟล์)
            if ($item_type === 'file' && $this->db->table_exists('tbl_google_drive_file_activities')) {
                $file_activity_data = [
                    'google_file_id' => $item_id,
                    'user_id' => $member_id,
                    'user_name' => $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'),
                    'user_email' => $this->session->userdata('m_email'),
                    'action_type' => 'rename',
                    'file_name' => $new_name,
                    'storage_mode' => $this->storage_mode ?? 'system',
                    'details' => json_encode([
                        'original_name' => $original_name,
                        'new_name' => $new_name,
                        'status' => $status,
                        'error_message' => $error_message
                    ]),
                    'created_at' => $timestamp
                ];

                if ($this->db->insert('tbl_google_drive_file_activities', $file_activity_data)) {
                    $logged_tables[] = 'tbl_google_drive_file_activities';
                }
            }



            // Log สรุป
            if (!empty($logged_tables)) {
                log_message('info', "✅ Rename activity logged to " . count($logged_tables) . " tables: " . implode(', ', $logged_tables));
            } else {
                log_message('debug', "⚠️ No tables were available for logging rename activity");
            }

        } catch (Exception $e) {
            log_message('error', 'Log rename activity error: ' . $e->getMessage());
        }
    }



    /**
     * 🔗 เปลี่ยนชื่อใน Google Drive API
     */
    private function rename_google_drive_item($item_id, $new_name, $access_token)
    {
        try {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$item_id}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$access_token}",
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS => json_encode(['name' => $new_name]),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                return ['success' => false, 'error' => 'การเชื่อมต่อล้มเหลว: ' . $curl_error];
            }

            if ($http_code === 200) {
                log_message('info', "✅ Successfully renamed item {$item_id} to '{$new_name}'");
                return ['success' => true, 'data' => json_decode($response, true)];
            } else {
                $error_response = json_decode($response, true);
                $error_message = isset($error_response['error']['message']) ?
                    $error_response['error']['message'] :
                    'HTTP ' . $http_code;

                log_message('error', "❌ Failed to rename item {$item_id}: {$error_message}");
                return ['success' => false, 'error' => $error_message];
            }

        } catch (Exception $e) {
            log_message('error', 'Rename Google Drive item error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * 📝 อัปเดตชื่อในฐานข้อมูลท้องถิ่น
     */
    private function update_item_name_in_db($item_id, $item_type, $new_name)
    {
        try {
            $updated_tables = [];
            $timestamp = date('Y-m-d H:i:s');

            if ($item_type === 'folder') {
                // อัปเดตในตาราง system folders
                if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                    $this->db->where('folder_id', $item_id);
                    if (
                        $this->db->update('tbl_google_drive_system_folders', [
                            'folder_name' => $new_name,
                            'updated_at' => $timestamp
                        ])
                    ) {
                        $updated_tables[] = 'tbl_google_drive_system_folders';
                    }
                }

                // อัปเดตในตาราง folders
                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $this->db->where('folder_id', $item_id);
                    if (
                        $this->db->update('tbl_google_drive_folders', [
                            'folder_name' => $new_name,
                            'updated_at' => $timestamp
                        ])
                    ) {
                        $updated_tables[] = 'tbl_google_drive_folders';
                    }
                }
            } else {
                // อัปเดตในตาราง system files
                if ($this->db->table_exists('tbl_google_drive_system_files')) {
                    $this->db->where('file_id', $item_id);
                    if (
                        $this->db->update('tbl_google_drive_system_files', [
                            'file_name' => $new_name,
                            'updated_at' => $timestamp
                        ])
                    ) {
                        $updated_tables[] = 'tbl_google_drive_system_files';
                    }
                }

                // อัปเดตในตาราง sync
                if ($this->db->table_exists('tbl_google_drive_sync')) {
                    $this->db->where('file_id', $item_id);
                    if (
                        $this->db->update('tbl_google_drive_sync', [
                            'file_name' => $new_name,
                            'updated_at' => $timestamp
                        ])
                    ) {
                        $updated_tables[] = 'tbl_google_drive_sync';
                    }
                }
            }

            if (!empty($updated_tables)) {
                log_message('info', "✅ Updated item name in " . count($updated_tables) . " tables: " . implode(', ', $updated_tables));
            } else {
                log_message('debug', "⚠️ No database tables were updated for item {$item_id}");
            }

        } catch (Exception $e) {
            log_message('error', 'Database update error: ' . $e->getMessage());
        }
    }



    public function check_file_access()
    {
        try {
            // ล้าง output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');

            if (!$this->input->is_ajax_request()) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $file_id = $this->input->post('file_id');

            if (empty($file_id)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบ File ID',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            log_message('info', "Checking file access for member: {$this->member_id}, file: {$file_id}");

            // ตรวจสอบว่าระบบ Google Drive เปิดใช้งานหรือไม่
            $settings = $this->get_settings_from_db();
            if (!$settings['google_drive_enabled']) {
                http_response_code(503);
                echo json_encode([
                    'success' => false,
                    'message' => 'Google Drive ถูกปิดใช้งานโดยระบบ',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // หาโฟลเดอร์ที่ไฟล์อยู่ (ใช้ function เดิม)
            $folder_id = $this->get_file_folder_id($file_id);

            if (!$folder_id) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบไฟล์ในระบบ',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์ (ใช้ function เดิม)
            $has_access = $this->check_folder_access_permission($folder_id);

            if ($has_access) {
                // ดึงข้อมูลเพิ่มเติมสำหรับ log
                $access_info = $this->get_file_access_info($file_id, $folder_id);

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'มีสิทธิ์เข้าถึงไฟล์',
                    'access_info' => $access_info,
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            } else {
                // บันทึก log การพยายามเข้าถึงที่ไม่ได้รับอนุญาต
                $this->log_unauthorized_file_access($file_id, $folder_id);

                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึงไฟล์นี้',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            }
            exit;

        } catch (Exception $e) {
            log_message('error', 'Check file access exception: ' . $e->getMessage());

            while (ob_get_level()) {
                ob_end_clean();
            }

            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดภายในระบบ',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * 📊 ดึงข้อมูลการเข้าถึงไฟล์
     */
    private function get_file_access_info($file_id, $folder_id)
    {
        try {
            $access_info = [
                'file_id' => $file_id,
                'folder_id' => $folder_id,
                'member_id' => $this->member_id,
                'access_method' => 'folder_permission',
                'granted_at' => date('Y-m-d H:i:s')
            ];

            // ตรวจสอบว่าได้สิทธิ์มาจากอะไร
            $direct_access = $this->db->select('access_type, permission_source, granted_by_name')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $this->member_id)
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($direct_access) {
                $access_info['access_type'] = $direct_access->access_type;
                $access_info['permission_source'] = $direct_access->permission_source;
                $access_info['granted_by'] = $direct_access->granted_by_name;
            }

            return $access_info;

        } catch (Exception $e) {
            log_message('error', 'Get file access info error: ' . $e->getMessage());
            return [
                'file_id' => $file_id,
                'folder_id' => $folder_id,
                'member_id' => $this->member_id,
                'access_method' => 'unknown',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 📝 บันทึก log การเข้าถึงที่ไม่ได้รับอนุญาต
     */
    private function log_unauthorized_file_access($file_id, $folder_id)
    {
        try {
            $log_data = [
                'member_id' => $this->member_id,
                'action_type' => 'unauthorized_file_access',
                'action_description' => "พยายามเข้าถึงไฟล์ {$file_id} ในโฟลเดอร์ {$folder_id} โดยไม่มีสิทธิ์",
                'file_id' => $file_id,
                'folder_id' => $folder_id,
                'item_id' => $file_id,
                'item_type' => 'file',
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // บันทึกลง activity logs
            if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
                $this->db->insert('tbl_google_drive_activity_logs', $log_data);
            }

            // บันทึกลง folder access logs
            if ($this->db->table_exists('tbl_google_drive_folder_access_logs')) {
                $access_log_data = [
                    'member_id' => $this->member_id,
                    'folder_id' => $folder_id,
                    'access_granted' => 0,
                    'access_time' => date('Y-m-d H:i:s'),
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent()
                ];
                $this->db->insert('tbl_google_drive_folder_access_logs', $access_log_data);
            }

            log_message('debug', "Unauthorized file access attempt by member {$this->member_id}: file {$file_id}, folder {$folder_id}");

        } catch (Exception $e) {
            log_message('error', 'Log unauthorized file access error: ' . $e->getMessage());
        }
    }




    // เพิ่ม method ใน Controller
    public function get_drive_settings()
    {
        try {
            // ดึงการตั้งค่าจาก database
            $settings_query = $this->db->select('setting_key, setting_value')
                ->from('tbl_google_drive_settings')
                ->where_in('setting_key', ['allowed_file_types', 'max_file_size', 'support_folder_upload'])
                ->get();

            $settings = array();
            foreach ($settings_query->result() as $row) {
                $settings[$row->setting_key] = $row->setting_value;
            }

            // ตั้งค่าเริ่มต้นถ้าไม่มีใน DB
            if (empty($settings['allowed_file_types'])) {
                $settings['allowed_file_types'] = json_encode(['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar']);
            }

            if (empty($settings['max_file_size'])) {
                $settings['max_file_size'] = '104857600'; // 100MB
            }

            if (empty($settings['support_folder_upload'])) {
                $settings['support_folder_upload'] = '1'; // เปิดใช้งาน
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'settings' => $settings,
                    'message' => 'ดึงการตั้งค่าสำเร็จ'
                ]));

        } catch (Exception $e) {
            log_message('error', 'Error getting drive settings: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถดึงการตั้งค่าได้: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * 📊 ดึงข้อมูล Storage (AJAX) - รองรับ Trial Mode
     * ✅ แก้ไข: ใช้ get_system_storage_info() และดึงค่าจาก settings แทน hard-code
     */
    public function get_storage_info()
    {
        try {
            while (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: application/json; charset=utf-8');

            if (!$this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                exit;
            }

            $member_id = $this->member_id ?? $this->session->userdata('m_id');
            if (!$member_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบ session ผู้ใช้']);
                exit;
            }

            log_message('info', sprintf(
                '📊 [Storage Info] Getting storage info for member_id=%d, mode=%s, trial=%s',
                $member_id,
                $this->storage_mode,
                $this->is_trial_mode ? 'YES' : 'NO'
            ));

            if ($this->storage_mode === 'centralized') {
                // ✅ Centralized Mode: ใช้ get_system_storage_info() ที่แก้ไขแล้ว
                log_message('info', '🏢 [Centralized Mode] Getting system storage info');

                $system_storage = $this->get_system_storage_info();

                if (!$system_storage) {
                    log_message('info', '❌ System storage not found');
                    echo json_encode([
                        'success' => false,
                        'message' => 'ไม่พบข้อมูล System Storage'
                    ]);
                    exit;
                }

                // ✅ ใช้ค่าจาก get_system_storage_info() ที่คำนวณแล้ว
                $quota_used = $system_storage->total_storage_used;  // คำนวณจากไฟล์จริงแล้ว
                $quota_limit = $system_storage->max_storage_limit;  // ดึงจาก settings แล้ว
                $percentage = $system_storage->storage_usage_percent;
                $used_formatted = $system_storage->total_storage_used_formatted;
                $limit_formatted = $system_storage->max_storage_limit_formatted;

                log_message('info', sprintf(
                    '✅ [System Storage] Returning: %s / %s (%.2f%%) [Trial: %s]',
                    $used_formatted,
                    $limit_formatted,
                    $percentage,
                    $this->is_trial_mode ? 'YES' : 'NO'
                ));

                echo json_encode([
                    'success' => true,
                    'data' => [
                        'storage_mode' => 'centralized',
                        'quota_used' => $quota_used,
                        'quota_limit' => $quota_limit,
                        'quota_used_formatted' => $used_formatted,
                        'quota_limit_formatted' => $limit_formatted,
                        'percentage' => $percentage,
                        'is_trial' => $this->is_trial_mode,
                        'details' => "{$used_formatted} / {$limit_formatted} ({$percentage}%)"
                    ],
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);

            } else {
                // ✅ User-based Mode: แสดงพื้นที่ส่วนตัว (รองรับ Trial)
                log_message('info', '👤 [User-based Mode] Getting member storage info');

                $member = $this->db->select('storage_quota_used, storage_quota_limit')
                    ->from('tbl_member')
                    ->where('m_id', $member_id)
                    ->get()
                    ->row();

                if (!$member) {
                    log_message('info', sprintf(
                        '❌ Member not found: member_id=%d',
                        $member_id
                    ));
                    echo json_encode([
                        'success' => false,
                        'message' => 'ไม่พบข้อมูลผู้ใช้'
                    ]);
                    exit;
                }

                $quota_used = isset($member->storage_quota_used) ? (int) $member->storage_quota_used : 0;

                // 🔥 FIX: เช็ค Trial Mode ก่อน ไม่ว่า DB จะมีค่าอะไร
                if ($this->is_trial_mode) {
                    // ✅ Trial Mode: ดึงจาก settings แทน hard-code
                    $quota_limit = $this->get_system_setting('trial_storage_limit', '5368709120');
                    $quota_limit = is_numeric($quota_limit) ? (int) $quota_limit : 5368709120;

                    log_message('info', sprintf(
                        '🎯 [Trial Mode Override] Using trial_storage_limit from settings: %s',
                        $this->format_file_size($quota_limit)
                    ));
                } else {
                    // Production Mode: ใช้ค่าจาก DB
                    $quota_limit = isset($member->storage_quota_limit) ? (int) $member->storage_quota_limit : 0;

                    // ถ้าไม่มี limit ให้ดึงจาก settings
                    if ($quota_limit <= 0) {
                        $default_quota = $this->get_system_setting('default_user_quota', '2147483648');
                        $quota_limit = is_numeric($default_quota) ? (int) $default_quota : 2147483648;

                        log_message('info', sprintf(
                            '⚠️ No quota limit in DB, using default_user_quota from settings: %s',
                            $this->format_file_size($quota_limit)
                        ));
                    }

                    log_message('info', sprintf(
                        '💼 [Production Mode] Using DB limit: %s',
                        $this->format_file_size($quota_limit)
                    ));
                }

                $percentage = ($quota_limit > 0) ? round(($quota_used / $quota_limit) * 100, 2) : 0;
                $used_formatted = $this->format_file_size($quota_used);
                $limit_formatted = $this->format_file_size($quota_limit);

                log_message('info', sprintf(
                    '✅ [Member Storage] Returning: %s / %s (%.2f%%) [Trial: %s]',
                    $used_formatted,
                    $limit_formatted,
                    $percentage,
                    $this->is_trial_mode ? 'YES' : 'NO'
                ));

                echo json_encode([
                    'success' => true,
                    'data' => [
                        'storage_mode' => 'user_based',
                        'quota_used' => $quota_used,
                        'quota_limit' => $quota_limit,
                        'quota_used_formatted' => $used_formatted,
                        'quota_limit_formatted' => $limit_formatted,
                        'percentage' => $percentage,
                        'is_trial' => $this->is_trial_mode,
                        'details' => "{$used_formatted} / {$limit_formatted} ({$percentage}%)"
                    ],
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
            }

            exit;

        } catch (Exception $e) {
            log_message('error', sprintf(
                '💥 [Storage Info] Error: %s',
                $e->getMessage()
            ));

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * 📊 คำนวณพื้นที่ใช้งานจริงของระบบจาก Google Drive
     */
    private function calculate_actual_system_storage_usage()
    {
        try {
            // ดึงข้อมูลไฟล์ทั้งหมดจาก System Storage
            if (!$this->db->table_exists('tbl_google_drive_system_files')) {
                log_message('warning', 'Table tbl_google_drive_system_files not found');
                return 0;
            }

            $result = $this->db->select_sum('file_size')
                ->from('tbl_google_drive_system_files')
                ->where('is_active', 1)
                ->get()
                ->row();

            $total_size = isset($result->file_size) ? (int) $result->file_size : 0;

            log_message('info', sprintf(
                '📊 [Calculate Storage] Total actual usage: %d bytes (%.2f MB)',
                $total_size,
                $total_size / (1024 * 1024)
            ));

            return $total_size;

        } catch (Exception $e) {
            log_message('error', 'Calculate actual system storage usage error: ' . $e->getMessage());
            return 0;
        }
    }


    /**
     * 📊 ดึงสถิติไฟล์และโฟลเดอร์ (AJAX)
     */
    public function get_file_stats()
    {
        try {
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/json; charset=utf-8');

            if (!$this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                exit;
            }

            $member_id = $this->member_id ?? $this->session->userdata('m_id');

            // นับจำนวนไฟล์
            $files_count = 0;
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $files_count = $this->db->where('uploaded_by', $member_id)
                    ->where('is_active', 1)
                    ->count_all_results('tbl_google_drive_system_files');
            }

            // นับจำนวนโฟลเดอร์ที่เข้าถึงได้
            $folders_count = 0;
            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $folders_count = $this->db->where('member_id', $member_id)
                    ->where('is_active', 1)
                    ->count_all_results('tbl_google_drive_member_folder_access');
            }

            // ดึงเวลาเข้าใช้ล่าสุด
            $last_access = null;
            if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
                $last_log = $this->db->select('created_at')
                    ->from('tbl_google_drive_activity_logs')
                    ->where('member_id', $member_id)
                    ->order_by('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->row();

                if ($last_log) {
                    $last_access = $this->format_datetime($last_log->created_at);
                }
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'files_count' => $files_count,
                    'folders_count' => $folders_count,
                    'last_access' => $last_access ?: 'ยังไม่มีข้อมูล'
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Get file stats error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด'
            ]);
            exit;
        }
    }

    /**
     * ✅ อัปเดตการใช้งาน storage ของ Member
     * เพิ่มพื้นที่ใช้งานเมื่อมีการอัปโหลดไฟล์
     * 
     * @param int $member_id  ID ของ Member ที่อัปโหลดไฟล์
     * @param int $file_size  ขนาดไฟล์ที่อัปโหลด (bytes)
     * @return bool  true = สำเร็จ, false = ล้มเหลว
     */
    private function update_member_storage_usage($member_id, $file_size)
    {
        try {
            log_message('info', sprintf(
                '📊 [Member Storage] Starting update for member_id=%d, file_size=%d bytes (%.2f MB)',
                $member_id,
                $file_size,
                $file_size / 1024 / 1024
            ));

            // ตรวจสอบว่า member_id ถูกต้อง
            if (!$member_id || $member_id <= 0) {
                log_message('info', '❌ [Member Storage] Invalid member_id, skipping update');
                return false;
            }

            // ตรวจสอบว่า file_size มีค่า
            if ($file_size <= 0) {
                log_message('info', '❌ [Member Storage] File size is 0 or negative, skipping update');
                return false;
            }

            // ดึงค่า storage_quota_used ปัจจุบันก่อน
            $current = $this->db->select('storage_quota_used, storage_quota_limit')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if (!$current) {
                log_message('info', sprintf(
                    '❌ [Member Storage] Member not found: member_id=%d',
                    $member_id
                ));
                return false;
            }

            $before_usage = (int) ($current->storage_quota_used ?? 0);
            $quota_limit = (int) ($current->storage_quota_limit ?? 0);

            log_message('info', sprintf(
                '📋 [Member Storage] Current usage: %d bytes (%.2f MB), Limit: %d bytes (%.2f MB)',
                $before_usage,
                $before_usage / 1024 / 1024,
                $quota_limit,
                $quota_limit / 1024 / 1024
            ));

            // อัปเดต storage_quota_used
            $affected_rows = $this->db->set('storage_quota_used', 'storage_quota_used + ' . (int) $file_size, FALSE)
                ->where('m_id', $member_id)
                ->update('tbl_member');

            if ($affected_rows) {
                $after_usage = $before_usage + $file_size;
                $percentage = ($quota_limit > 0) ? round(($after_usage / $quota_limit) * 100, 2) : 0;

                log_message('info', sprintf(
                    '✅ [Member Storage] Updated successfully: %d → %d bytes (%.2f → %.2f MB)',
                    $before_usage,
                    $after_usage,
                    $before_usage / 1024 / 1024,
                    $after_usage / 1024 / 1024
                ));

                log_message('info', sprintf(
                    '📈 [Member Storage] Usage: %.2f%% (%s / %s)',
                    $percentage,
                    $this->format_file_size($after_usage),
                    $this->format_file_size($quota_limit)
                ));

                return true;
            } else {
                log_message('info', sprintf(
                    '⚠️ [Member Storage] Update affected 0 rows for member_id=%d',
                    $member_id
                ));
                return false;
            }

        } catch (Exception $e) {
            log_message('error', sprintf(
                '💥 [Member Storage] Update error for member_id=%d: %s',
                $member_id,
                $e->getMessage()
            ));
            return false;
        }
    }

    /**
     * ✅ ลดการใช้งาน storage ของ Member
     * ลดพื้นที่ใช้งานเมื่อมีการลบไฟล์
     * 
     * @param int $member_id  ID ของ Member ที่เป็นเจ้าของไฟล์
     * @param int $file_size  ขนาดไฟล์ที่ถูกลบ (bytes)
     * @return bool  true = สำเร็จ, false = ล้มเหลว
     */
    private function decrease_member_storage_usage($member_id, $file_size)
    {
        try {
            log_message('info', sprintf(
                '📉 [Member Storage] Starting decrease for member_id=%d, file_size=%d bytes (%.2f MB)',
                $member_id,
                $file_size,
                $file_size / 1024 / 1024
            ));

            // ตรวจสอบว่า member_id ถูกต้อง
            if (!$member_id || $member_id <= 0) {
                log_message('info', '❌ [Member Storage] Invalid member_id, skipping decrease');
                return false;
            }

            // ตรวจสอบว่า file_size มีค่า
            if ($file_size <= 0) {
                log_message('info', '❌ [Member Storage] File size is 0 or negative, skipping decrease');
                return false;
            }

            // ดึงค่า storage_quota_used ปัจจุบันก่อน
            $current = $this->db->select('storage_quota_used, storage_quota_limit')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if (!$current) {
                log_message('info', sprintf(
                    '❌ [Member Storage] Member not found: member_id=%d',
                    $member_id
                ));
                return false;
            }

            $before_usage = (int) ($current->storage_quota_used ?? 0);
            $quota_limit = (int) ($current->storage_quota_limit ?? 0);

            log_message('info', sprintf(
                '📋 [Member Storage] Current usage: %d bytes (%.2f MB), Limit: %d bytes (%.2f MB)',
                $before_usage,
                $before_usage / 1024 / 1024,
                $quota_limit,
                $quota_limit / 1024 / 1024
            ));

            // ลด storage_quota_used (ป้องกันติดลบด้วย GREATEST)
            $affected_rows = $this->db->set(
                'storage_quota_used',
                'GREATEST(storage_quota_used - ' . (int) $file_size . ', 0)',
                FALSE
            )
                ->where('m_id', $member_id)
                ->update('tbl_member');

            if ($affected_rows) {
                // คำนวณค่าหลังลด (ใช้ GREATEST เพื่อป้องกันติดลบ)
                $after_usage = max($before_usage - $file_size, 0);
                $percentage = ($quota_limit > 0) ? round(($after_usage / $quota_limit) * 100, 2) : 0;

                log_message('info', sprintf(
                    '✅ [Member Storage] Decreased successfully: %d → %d bytes (%.2f → %.2f MB)',
                    $before_usage,
                    $after_usage,
                    $before_usage / 1024 / 1024,
                    $after_usage / 1024 / 1024
                ));

                log_message('info', sprintf(
                    '📉 [Member Storage] Usage: %.2f%% (%s / %s)',
                    $percentage,
                    $this->format_file_size($after_usage),
                    $this->format_file_size($quota_limit)
                ));

                // ตรวจสอบว่าค่าติดลบหรือไม่
                if ($before_usage < $file_size) {
                    log_message('info', sprintf(
                        '⚠️ [Member Storage] Storage would go negative (%d - %d = %d), clamped to 0',
                        $before_usage,
                        $file_size,
                        $before_usage - $file_size
                    ));
                }

                return true;
            } else {
                log_message('info', sprintf(
                    '⚠️ [Member Storage] Decrease affected 0 rows for member_id=%d',
                    $member_id
                ));
                return false;
            }

        } catch (Exception $e) {
            log_message('error', sprintf(
                '💥 [Member Storage] Decrease error for member_id=%d: %s',
                $member_id,
                $e->getMessage()
            ));
            return false;
        }
    }


    /**
     * 🔄 Sync Storage Usage สำหรับข้อมูลเก่า
     * รัน 1 ครั้งหลัง Deploy หรือเป็นระยะ (Cron Job)
     */
    public function sync_all_storage_usage()
    {
        try {
            log_message('info', '🔄 ========================================');
            log_message('info', '🔄 Starting storage sync...');
            log_message('info', '🔄 Timestamp: ' . date('Y-m-d H:i:s'));
            log_message('info', '🔄 ========================================');

            $this->db->trans_start();

            // ============================================
            // ✅ STEP 1: Sync System Storage
            // ============================================
            log_message('info', '📊 Step 1: Syncing System Storage...');

            $system_query = $this->db->query("
            SELECT 
                COUNT(*) as file_count,
                COALESCE(SUM(file_size), 0) as total_size
            FROM tbl_google_drive_system_files
            WHERE is_active = 1
        ");

            $system_data = $system_query->row();
            $total_files_size = (int) $system_data->total_size;
            $file_count = (int) $system_data->file_count;

            // ✅ Update System Storage Table
            $this->db->where('is_active', 1)
                ->update('tbl_google_drive_system_storage', [
                    'total_storage_used' => $total_files_size,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            $system_affected = $this->db->affected_rows();

            log_message('info', sprintf(
                '✅ System Storage: %d files, %d bytes (%.2f MB) - affected: %d rows',
                $file_count,
                $total_files_size,
                $total_files_size / 1024 / 1024,
                $system_affected
            ));

            // ✅ Update Settings Table (ถ้ามี) - แก้ไขตรงนี้
            try {
                $settings = $this->get_settings_from_db();

                // ✅ ตรวจสอบว่ามี Key นี้หรือไม่
                if (isset($settings['system_storage_mode']) && $settings['system_storage_mode'] === 'centralized') {
                    $this->db->where('setting_key', 'system_storage_used')
                        ->update('tbl_google_drive_settings', [
                            'setting_value' => $total_files_size,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    log_message('info', '✅ Settings Table updated');
                } else {
                    log_message('info', 'ℹ️ Skipping Settings Table update (not centralized mode or key not found)');
                }
            } catch (Exception $e) {
                log_message('debug', '⚠️ Settings Table update skipped: ' . $e->getMessage());
            }

            // ============================================
            // ✅ STEP 2: Sync Member Storage (มีไฟล์)
            // ============================================
            log_message('info', '📊 Step 2: Syncing Member Storage...');

            $members_with_files = $this->db->query("
            SELECT 
                uploaded_by,
                COUNT(*) as file_count,
                COALESCE(SUM(file_size), 0) as total_size
            FROM tbl_google_drive_system_files
            WHERE is_active = 1
              AND uploaded_by IS NOT NULL
            GROUP BY uploaded_by
        ")->result();

            $members_updated = 0;
            $member_ids_with_files = [];

            foreach ($members_with_files as $member) {
                $member_id = (int) $member->uploaded_by;
                $total_size = (int) $member->total_size;
                $file_count_member = (int) $member->file_count;

                // Update Member Storage
                $this->db->where('m_id', $member_id)
                    ->update('tbl_member', [
                        'storage_quota_used' => $total_size
                    ]);

                $affected = $this->db->affected_rows();

                if ($affected > 0) {
                    $members_updated++;
                }

                $member_ids_with_files[] = $member_id;

                // Get member name
                $member_info = $this->db->select('m_fname, m_lname')
                    ->from('tbl_member')
                    ->where('m_id', $member_id)
                    ->get()
                    ->row();

                $member_name = $member_info
                    ? "{$member_info->m_fname} {$member_info->m_lname}"
                    : "Unknown";

                log_message('info', sprintf(
                    '✅ Member %d (%s): %d files, %d bytes (%.2f MB)',
                    $member_id,
                    $member_name,
                    $file_count_member,
                    $total_size,
                    $total_size / 1024 / 1024
                ));
            }

            // ============================================
            // ✅ STEP 3: Reset Members ที่ไม่มีไฟล์
            // ============================================
            log_message('info', '📊 Step 3: Resetting members without files...');

            $members_reset = 0;

            if (empty($member_ids_with_files)) {
                // ไม่มีไฟล์เลย → Reset ทุกคนที่มีค่า > 0
                $this->db->where('storage_quota_used >', 0)
                    ->update('tbl_member', [
                        'storage_quota_used' => 0
                    ]);

                $members_reset = $this->db->affected_rows();

                log_message('info', sprintf(
                    '✅ No files found. Reset %d members to 0',
                    $members_reset
                ));
            } else {
                // มีไฟล์ → Reset เฉพาะคนที่ไม่มีไฟล์
                $this->db->where('storage_quota_used >', 0)
                    ->where_not_in('m_id', $member_ids_with_files)
                    ->update('tbl_member', [
                        'storage_quota_used' => 0
                    ]);

                $members_reset = $this->db->affected_rows();

                log_message('info', sprintf(
                    '✅ Reset %d members without files',
                    $members_reset
                ));
            }

            // ============================================
            // ✅ STEP 4: Commit Transaction
            // ============================================
            $this->db->trans_complete();

            if (!$this->db->trans_status()) {
                throw new Exception('Transaction failed');
            }

            // ============================================
            // ✅ STEP 5: Verify Results
            // ============================================
            log_message('info', '📊 Step 4: Verifying results...');

            $verification = $this->db->query("
            SELECT 
                (SELECT total_storage_used 
                 FROM tbl_google_drive_system_storage 
                 WHERE is_active = 1
                ) AS system_storage,
                
                (SELECT SUM(storage_quota_used) 
                 FROM tbl_member
                ) AS sum_member_storage,
                
                (SELECT SUM(file_size) 
                 FROM tbl_google_drive_system_files 
                 WHERE is_active = 1
                ) AS sum_files,
                
                (SELECT COUNT(*) 
                 FROM tbl_member 
                 WHERE storage_quota_used > 0
                ) AS members_with_usage
        ")->row();

            $system_mb = $verification->system_storage / 1024 / 1024;
            $member_mb = $verification->sum_member_storage / 1024 / 1024;
            $files_mb = $verification->sum_files / 1024 / 1024;

            log_message('info', sprintf(
                '📊 Verification Results:
            - System Storage:      %d bytes (%.2f MB)
            - Sum Member Storage:  %d bytes (%.2f MB)
            - Sum Active Files:    %d bytes (%.2f MB)
            - Members with usage:  %d members',
                $verification->system_storage,
                $system_mb,
                $verification->sum_member_storage,
                $member_mb,
                $verification->sum_files,
                $files_mb,
                $verification->members_with_usage
            ));

            // ✅ ตรวจสอบความสอดคล้อง
            $is_consistent = (
                $verification->system_storage == $verification->sum_files
            );

            if ($is_consistent) {
                log_message('info', '✅ Storage data is CONSISTENT');
            } else {
                log_message('debug', sprintf(
                    '⚠️ Storage data is INCONSISTENT! System=%d, Files=%d, Diff=%d bytes (%.2f MB)',
                    $verification->system_storage,
                    $verification->sum_files,
                    abs($verification->system_storage - $verification->sum_files),
                    abs($system_mb - $files_mb)
                ));
            }

            log_message('info', '🔄 ========================================');
            log_message('info', '🔄 Storage sync completed successfully');
            log_message('info', '🔄 ========================================');

            // ============================================
            // ✅ Return Summary
            // ============================================
            $response = [
                'success' => true,
                'message' => 'Storage synced successfully',
                'summary' => [
                    'file_count' => $file_count,
                    'system_storage_bytes' => $total_files_size,
                    'system_storage_mb' => round($system_mb, 2),
                    'members_updated' => $members_updated,
                    'members_reset' => $members_reset,
                    'members_with_usage' => (int) $verification->members_with_usage,
                    'is_consistent' => $is_consistent
                ],
                'verification' => [
                    'system_storage_mb' => round($system_mb, 2),
                    'sum_member_storage_mb' => round($member_mb, 2),
                    'sum_files_mb' => round($files_mb, 2),
                    'difference_mb' => round(abs($system_mb - $files_mb), 2)
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // ✅ แสดงผลเป็น JSON
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;

        } catch (Exception $e) {
            $this->db->trans_rollback();

            log_message('error', '💥 Sync storage error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            $response = [
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }
    }


    /**
     * 🔍 ตรวจสอบความถูกต้องก่อนอัปโหลด (Pre-Upload Validation)
     * 
     * ตรวจสอบ:
     * 1. File type allowed
     * 2. File size within limit
     * 3. System storage available
     * 4. Member quota available
     * 
     * @param int $file_size ขนาดไฟล์ (bytes)
     * @param string $file_extension นามสกุลไฟล์
     * @param array $settings การตั้งค่าระบบ
     * @return array ['allowed' => bool, 'http_code' => int, 'response' => array]
     */
    private function pre_upload_validation($file_size, $file_extension, $settings)
    {
        try {
            // ============================================
            // 1️⃣ ตรวจสอบประเภทไฟล์ (File Type Check)
            // ============================================
            if (!in_array($file_extension, $settings['allowed_file_types'])) {
                log_message('info', sprintf(
                    '❌ Validation failed: File type not allowed (.%s)',
                    $file_extension
                ));

                return [
                    'allowed' => false,
                    'http_code' => 400,
                    'response' => [
                        'success' => false,
                        'message' => "ประเภทไฟล์ไม่ได้รับอนุญาต: .{$file_extension}",
                        'validation_error' => 'invalid_file_type',
                        'details' => [
                            'file_extension' => $file_extension,
                            'allowed_types' => $settings['allowed_file_types']
                        ],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ];
            }

            // ============================================
            // 2️⃣ ตรวจสอบขนาดไฟล์ (File Size Check)
            // ============================================
            if ($file_size > $settings['max_file_size']) {
                $max_size_mb = round($settings['max_file_size'] / (1024 * 1024), 1);
                $current_file_size_mb = round($file_size / (1024 * 1024), 2);

                log_message('info', sprintf(
                    '❌ Validation failed: File too large (%.2f MB > %.2f MB)',
                    $current_file_size_mb,
                    $max_size_mb
                ));

                return [
                    'allowed' => false,
                    'http_code' => 413,
                    'response' => [
                        'success' => false,
                        'message' => "ไฟล์มีขนาดใหญ่เกิน {$max_size_mb} MB",
                        'validation_error' => 'file_too_large',
                        'details' => [
                            'max_size_mb' => $max_size_mb,
                            'current_file_size_mb' => $current_file_size_mb
                        ],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ];
            }

            // ============================================
            // 3️⃣ ตรวจสอบพื้นที่ระบบ (System Storage Check)
            // ============================================
            $storage_check = $this->check_system_storage_limit($file_size, $settings);

            if (!$storage_check['allowed']) {
                log_message('info', sprintf(
                    '❌ Validation failed: Insufficient system storage (%.2f GB used, %.2f GB limit)',
                    $storage_check['current_usage_gb'],
                    $storage_check['limit_gb']
                ));

                return [
                    'allowed' => false,
                    'http_code' => 413,
                    'response' => [
                        'success' => false,
                        'message' => $storage_check['message'],
                        'validation_error' => 'system_storage_exceeded',
                        'details' => [
                            'storage_info' => [
                                'current_usage_gb' => $storage_check['current_usage_gb'],
                                'limit_gb' => $storage_check['limit_gb'],
                                'available_gb' => $storage_check['available_gb'],
                                'file_size_mb' => round($file_size / (1024 * 1024), 2)
                            ]
                        ],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ];
            }

            // ============================================
            // 4️⃣ ตรวจสอบโควต้าสมาชิก (Member Quota Check) 🔥 NEW
            // ============================================
            if (!$this->check_storage_limit($file_size)) {
                // ดึงข้อมูล Member quota สำหรับแสดงรายละเอียด
                $member = $this->db->select('storage_quota_used, storage_quota_limit')
                    ->from('tbl_member')
                    ->where('m_id', $this->member_id)
                    ->get()
                    ->row();

                if (!$member) {
                    log_message('error', sprintf(
                        '❌ Validation failed: Member not found (member_id=%d)',
                        $this->member_id
                    ));

                    return [
                        'allowed' => false,
                        'http_code' => 500,
                        'response' => [
                            'success' => false,
                            'message' => 'ไม่พบข้อมูลสมาชิก',
                            'validation_error' => 'member_not_found',
                            'timestamp' => date('Y-m-d H:i:s')
                        ]
                    ];
                }

                $current_used = $member->storage_quota_used ?: 0;
                $quota_limit = $this->is_trial_mode
                    ? $this->trial_storage_limit
                    : ($member->storage_quota_limit ?: (5 * 1024 * 1024 * 1024)); // 5GB default

                $current_used_mb = round($current_used / (1024 * 1024), 2);
                $limit_mb = round($quota_limit / (1024 * 1024), 2);
                $available_mb = round(($quota_limit - $current_used) / (1024 * 1024), 2);
                $file_size_mb = round($file_size / (1024 * 1024), 2);

                log_message('info', sprintf(
                    '❌ Validation failed: Member quota exceeded (%.2f MB used, %.2f MB limit, file: %.2f MB, trial: %s)',
                    $current_used_mb,
                    $limit_mb,
                    $file_size_mb,
                    $this->is_trial_mode ? 'yes' : 'no'
                ));

                return [
                    'allowed' => false,
                    'http_code' => 413,
                    'response' => [
                        'success' => false,
                        'message' => $this->is_trial_mode
                            ? 'พื้นที่จัดเก็บของคุณเต็มแล้ว (Trial Mode: 1GB)'
                            : 'พื้นที่จัดเก็บของคุณเต็มแล้ว',
                        'validation_error' => 'member_quota_exceeded',
                        'details' => [
                            'quota_info' => [
                                'current_used_mb' => $current_used_mb,
                                'limit_mb' => $limit_mb,
                                'available_mb' => $available_mb,
                                'file_size_mb' => $file_size_mb,
                                'is_trial_mode' => $this->is_trial_mode,
                                'would_exceed_by_mb' => round(($current_used + $file_size - $quota_limit) / (1024 * 1024), 2)
                            ]
                        ],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ];
            }

            // ============================================
            // ✅ ผ่านการตรวจสอบทั้งหมด
            // ============================================
            log_message('info', sprintf(
                '✅ All pre-upload validations passed (file: %.2f MB, type: .%s)',
                round($file_size / (1024 * 1024), 2),
                $file_extension
            ));

            return [
                'allowed' => true,
                'http_code' => 200,
                'response' => [
                    'success' => true,
                    'message' => 'Validation passed'
                ]
            ];

        } catch (Exception $e) {
            log_message('error', sprintf(
                '💥 Pre-upload validation exception: %s',
                $e->getMessage()
            ));

            return [
                'allowed' => false,
                'http_code' => 500,
                'response' => [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการตรวจสอบไฟล์',
                    'validation_error' => 'exception',
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
        }
    }



}
?>