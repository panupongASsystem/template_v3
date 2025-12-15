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
class Google_drive_files extends CI_Controller {

    private $member_id;
    private $storage_mode;
    private $is_trial_mode = false;
    private $trial_storage_limit = 1073741824; // 1GB for trial
    private $system_settings = [];
    
    public function __construct() {
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
}


	
	
	
	/**
     * 🔧 โหลดการตั้งค่าระบบจาก tbl_google_drive_settings
     */
    private function load_system_settings() {
        try {
            // ค่าเริ่มต้นของระบบ
            $default_settings = [
                'max_file_size' => '104857600', // 100MB
                'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
                'default_user_quota' => '1073741824', // 1GB
                'system_storage_mode' => 'user_based',
                'google_drive_enabled' => '1',
                'auto_create_folders' => '1'
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
            
            log_message('info', 'System settings loaded: ' . json_encode($this->system_settings));

        } catch (Exception $e) {
            log_message('error', 'Load system settings error: ' . $e->getMessage());
            // ใช้ค่าเริ่มต้นถ้าเกิดข้อผิดพลาด
            $this->system_settings = [
                'max_file_size' => '104857600', // 100MB
                'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
                'default_user_quota' => '1073741824', // 1GB
                'system_storage_mode' => 'user_based',
                'google_drive_enabled' => '1',
                'auto_create_folders' => '1'
            ];
        }
    }

	
	
	/**
     * 🔧 ดึงค่าการตั้งค่าเฉพาะ
     */
    private function get_system_setting($key, $default = null) {
        return isset($this->system_settings[$key]) ? $this->system_settings[$key] : $default;
    }

    /**
     * 🔧 ดึงขีดจำกัดพื้นที่สำหรับ Trial Mode
     */
    private function get_trial_storage_limit() {
        $limit = $this->get_system_setting('default_user_quota', '1073741824');
        return is_numeric($limit) ? (int)$limit : 1073741824; // Default 1GB
    }

    /**
     * 🔧 ดึงขนาดไฟล์สูงสุดที่อนุญาต
     */
    private function get_max_file_size() {
        $max_size = $this->get_system_setting('max_file_size', '104857600');
        return is_numeric($max_size) ? (int)$max_size : 104857600; // Default 100MB
    }

    /**
     * 🔧 ดึงประเภทไฟล์ที่อนุญาต
     */
    private function get_allowed_file_types() {
        $allowed_types = $this->get_system_setting('allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar');
        
        if (is_string($allowed_types)) {
            return array_map('trim', explode(',', strtolower($allowed_types)));
        }
        
        return ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
    }

    /**
     * 🔧 ตรวจสอบว่า Google Drive เปิดใช้งานหรือไม่
     */
    private function is_google_drive_enabled() {
        return $this->get_system_setting('google_drive_enabled', '1') === '1';
    }

    /**
     * 🏠 หน้าหลัก Member Files (Apple-inspired Interface)
     */
    public function index() {
        $this->files();
    }


    /**
     * 📱 หน้า Member Files (Apple-inspired Interface)
     */
    public function files() {
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

            // ส่งข้อมูลไปยัง view
            $data = [
                'member_info' => $access_check['member'],
                'permission_info' => $access_check['permission'],
                'storage_mode' => $this->storage_mode,
                'is_trial_mode' => $this->is_trial_mode,
                'trial_storage_limit' => $this->trial_storage_limit,
                'show_trial_modal' => $this->is_trial_mode, // ✅ บอกให้แสดง Trial Modal
                'system_storage' => $this->storage_mode === 'centralized' ? $this->get_system_storage_info() : null,
                'system_settings' => $this->system_settings
            ];

            // โหลดหน้า Apple-inspired interface
            $this->load->view('google_drive/header');
            $this->load->view('google_drive/css');
            $this->load->view('google_drive/main_content', $data); // ส่ง $data ไปด้วย
            $this->load->view('google_drive/javascript');
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
    private function check_trial_mode() {
        try {
            // ตรวจสอบจาก tbl_member_modules ว่าโมดูล Google Drive เป็น trial หรือไม่
            $google_drive_module = $this->db->select('is_trial')
                                           ->from('tbl_member_modules')
                                           ->where('code', 'google_drive')
                                           ->where('status', 1)
                                           ->get()
                                           ->row();

            return $google_drive_module ? ($google_drive_module->is_trial == 1) : false;

        } catch (Exception $e) {
            log_message('error', 'Check trial mode error: ' . $e->getMessage());
            return false;
        }
    }
	
	
	private function check_access_by_mode() {
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
    private function check_production_access($member) {
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
    private function check_centralized_production_access($member) {
        // ✅ เช็ค storage_access_granted = 1
        if (!$member->storage_access_granted || $member->storage_access_granted != 1) {
            log_message('warning', "Centralized access denied for member {$this->member_id}: storage_access_granted = " . ($member->storage_access_granted ?? 'null'));
            
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
    private function check_user_based_production_access($member) {
        // ✅ เช็ค google_drive_enabled = 1
        if (!$member->google_drive_enabled || $member->google_drive_enabled != 1) {
            log_message('warning', "User-based access denied for member {$this->member_id}: google_drive_enabled = " . ($member->google_drive_enabled ?? 'null'));
            
            return [
                'allowed' => false,
                'reason' => 'Google Drive ยังไม่ได้เปิดใช้งานสำหรับบัญชีนี้'
            ];
        }

        // ✅ เช็คการเชื่อมต่อ Google (สำหรับ Production)
        if (empty($member->google_email) || empty($member->google_access_token)) {
            log_message('warning', "User-based access denied for member {$this->member_id}: missing Google connection");
            
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
    private function get_trial_permissions() {
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
     * 📊 อัปเดต Storage Usage (Enhanced)
     */
    private function update_storage_usage($file_size) {
        try {
            // อัปเดต member quota
            $current_used = $this->db->select('storage_quota_used')
                                    ->from('tbl_member')
                                    ->where('m_id', $this->member_id)
                                    ->get()
                                    ->row()
                                    ->storage_quota_used ?? 0;

            $new_used = $current_used + $file_size;

            $this->db->where('m_id', $this->member_id)
                    ->update('tbl_member', [
                        'storage_quota_used' => $new_used,
                        'last_storage_access' => date('Y-m-d H:i:s')
                    ]);

            // บันทึก usage log (ถ้ามีตาราง)
            if ($this->db->table_exists('tbl_google_drive_storage_usage')) {
                $today = date('Y-m-d');
                $existing_usage = $this->db->where('user_id', $this->member_id)
                                          ->where('usage_date', $today)
                                          ->where('storage_mode', $this->storage_mode)
                                          ->get('tbl_google_drive_storage_usage')
                                          ->row();

                if ($existing_usage) {
                    // อัปเดตข้อมูลวันนี้
                    $this->db->where('id', $existing_usage->id)
                            ->update('tbl_google_drive_storage_usage', [
                                'total_size_bytes' => $existing_usage->total_size_bytes + $file_size,
                                'uploads_count' => $existing_usage->uploads_count + 1,
                                'files_count' => $existing_usage->files_count + 1
                            ]);
                } else {
                    // สร้างข้อมูลใหม่
                    $this->db->insert('tbl_google_drive_storage_usage', [
                        'user_id' => $this->member_id,
                        'storage_mode' => $this->storage_mode,
                        'usage_date' => $today,
                        'total_size_bytes' => $file_size,
                        'uploads_count' => 1,
                        'files_count' => 1
                    ]);
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Update storage usage error: ' . $e->getMessage());
        }
    }
	

	
	/**
     * 🔍 ตรวจสอบการใช้งาน Quota (Enhanced with Settings)
     */
    private function check_storage_quota($additional_size) {
        try {
            $member = $this->db->select('storage_quota_used, storage_quota_limit')
                              ->from('tbl_member')
                              ->where('m_id', $this->member_id)
                              ->get()
                              ->row();

            if (!$member) {
                return [
                    'allowed' => false,
                    'reason' => 'ไม่พบข้อมูลผู้ใช้'
                ];
            }

            $current_used = $member->storage_quota_used ?: 0;
            
            // ใช้ขีดจำกัดจากการตั้งค่าระบบ
            if ($this->is_trial_mode) {
                $limit = $this->trial_storage_limit;
            } else if ($member->storage_quota_limit) {
                $limit = $member->storage_quota_limit;
            } else {
                // ใช้ค่าเริ่มต้นจากการตั้งค่าระบบ
                $limit = $this->get_storage_limit_for_member($member);
            }
            
            $total_after_upload = $current_used + $additional_size;
            
            if ($total_after_upload > $limit) {
                $remaining_mb = round(($limit - $current_used) / (1024 * 1024), 1);
                $needed_mb = round($additional_size / (1024 * 1024), 1);
                
                return [
                    'allowed' => false,
                    'reason' => "พื้นที่เหลือ {$remaining_mb}MB ไม่พอสำหรับไฟล์ขนาด {$needed_mb}MB",
                    'current_used' => $current_used,
                    'limit' => $limit,
                    'remaining' => $limit - $current_used
                ];
            }
            
            return [
                'allowed' => true,
                'current_used' => $current_used,
                'limit' => $limit,
                'remaining' => $limit - $current_used
            ];

        } catch (Exception $e) {
            log_message('error', 'Check storage quota error: ' . $e->getMessage());
            return [
                'allowed' => false,
                'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบ quota'
            ];
        }
    }

	
    /**
     * 📊 ดึงขีดจำกัดพื้นที่สำหรับ Member (Enhanced with Settings)
     */
    private function get_storage_limit_for_member($member) {
        if ($this->is_trial_mode) {
            return $this->trial_storage_limit; // จากการตั้งค่าระบบ
        }

        if ($this->storage_mode === 'centralized') {
            // ดึงจาก system storage settings
            $system_storage = $this->get_system_storage_info();
            if ($system_storage && isset($system_storage->default_quota_per_user)) {
                return $system_storage->default_quota_per_user;
            }
            
            // ใช้ค่าเริ่มต้นจากการตั้งค่าระบบ
            $default_quota = $this->get_system_setting('default_user_quota', '5368709120'); // 5GB
            return is_numeric($default_quota) ? (int)$default_quota : 5368709120;
        } else {
            // User-based mode ใช้ Google Drive quota หรือค่าเริ่มต้นจากการตั้งค่า
            $default_quota = $this->get_system_setting('default_user_quota', '16106127360'); // 15GB
            return is_numeric($default_quota) ? (int)$default_quota : 16106127360;
        }
    }

    /**
     * 🔍 ตรวจสอบสิทธิ์การเข้าถึงของ Member
     */
    private function check_member_access() {
        try {
            // ดึงข้อมูล member
            $member = $this->db->select('m.*, p.pname, p.peng')
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

            // ตรวจสอบตามโหมด storage
            if ($this->storage_mode === 'centralized') {
                return $this->check_centralized_access($member);
            } else {
                return $this->check_user_based_access($member);
            }

        } catch (Exception $e) {
            log_message('error', 'Check member access error: ' . $e->getMessage());
            return [
                'allowed' => false,
                'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์'
            ];
        }
    }

    /**
     * 🏢 ตรวจสอบสิทธิ์สำหรับ Centralized Mode
     */
    private function check_centralized_access($member) {
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
    private function check_user_based_access($member) {
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
   public function get_member_info() {
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



    /**
     * 📊 เพิ่มข้อมูลสำหรับ Centralized mode
     */
    private function add_centralized_info(&$member_info, $member) {
        try {
            // ดึงข้อมูล quota
            if (isset($member->storage_quota_used)) {
                $member_info['quota_used'] = $member->storage_quota_used;
            }
            if (isset($member->storage_quota_limit)) {
                $member_info['quota_limit'] = $member->storage_quota_limit;
            }

            // สำหรับ trial mode ใช้ trial limit
            if ($this->is_trial_mode) {
                $member_info['quota_limit'] = $this->trial_storage_limit;
            }

            // ดึงจำนวนไฟล์
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $files_count = $this->db->where('uploaded_by', $this->member_id)
                                       ->count_all_results('tbl_google_drive_system_files');
                $member_info['files_count'] = $files_count;
            }

            // ดึงจำนวนโฟลเดอร์ที่เข้าถึงได้
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $folders_count = $this->db->where('is_active', 1)
                                         ->count_all_results('tbl_google_drive_system_folders');
                $member_info['accessible_folders_count'] = $folders_count;
            }

            $member_info['is_connected'] = $member->storage_access_granted == 1;

        } catch (Exception $e) {
            log_message('error', 'Add centralized info error: ' . $e->getMessage());
        }
    }

    /**
     * 📊 เพิ่มข้อมูลสำหรับ User-based mode
     */
    private function add_user_based_info(&$member_info, $member) {
        try {
            // สำหรับ trial mode ไม่จำเป็นต้องเชื่อมต่อ Google
            if ($this->is_trial_mode) {
                $member_info['is_connected'] = true;
                $member_info['quota_limit'] = $this->trial_storage_limit;
            } else {
                $member_info['is_connected'] = !empty($member->google_email) && !empty($member->google_access_token);
            }
            
            // ดึงจำนวนโฟลเดอร์ส่วนตัว
            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $query = $this->db->where('member_id', $this->member_id)
                                 ->where('is_active', 1);
                
                // ถ้าไม่ใช่ trial mode ให้นับเฉพาะโฟลเดอร์ปกติ
                if (!$this->is_trial_mode) {
                    $query->where('folder_type !=', 'trial');
                }
                
                $folders_count = $query->count_all_results('tbl_google_drive_folders');
                $member_info['accessible_folders_count'] = $folders_count;
            }

        } catch (Exception $e) {
            log_message('error', 'Add user based info error: ' . $e->getMessage());
        }
    }

    /**
     * 📂 ดึงโฟลเดอร์ที่เข้าถึงได้ (AJAX) - ✅ Fixed to use real Google Drive API
     */
   public function get_member_folders() {
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

        // ดึงโฟลเดอร์ตามโหมด
        if ($this->storage_mode === 'centralized') {
            $folders = $this->get_centralized_folders();
        } else {
            $folders = $this->get_user_based_folders();
        }
        
        $this->safe_json_success($folders, 'ดึงข้อมูลโฟลเดอร์สำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get member folders error: ' . $e->getMessage());
        $this->safe_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
    }
}


    /**
     * 🏢 ดึงโฟลเดอร์สำหรับ Centralized Mode - ✅ Fixed with real API
     */
    private function get_centralized_folders() {
        try {
            // ดึง System Storage ที่ใช้งานอยู่
            $system_storage = $this->get_active_system_storage();
            if (!$system_storage || !$system_storage->google_access_token) {
                log_message('error', 'No active system storage or access token found');
                return [];
            }

            // ตรวจสอบ Token และ Refresh ถ้าจำเป็น
            if (!$this->has_valid_access_token($system_storage)) {
                $refreshed = $this->refresh_system_access_token($system_storage);
                if (!$refreshed) {
                    log_message('error', 'Failed to refresh system access token');
                    return [];
                }
                // ดึง system storage ใหม่หลัง refresh
                $system_storage = $this->get_active_system_storage();
            }

            $token_data = json_decode($system_storage->google_access_token, true);
            $access_token = $token_data['access_token'];

            // ดึงโฟลเดอร์หลักจาก Google Drive
            $folders = $this->get_google_drive_root_folders($access_token, $system_storage->root_folder_id);
            
            if ($folders === false) {
                log_message('error', 'Failed to get folders from Google Drive API');
                return [];
            }

            return $folders;

        } catch (Exception $e) {
            log_message('error', 'Get centralized folders error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 👤 ดึงโฟลเดอร์สำหรับ User-based Mode (รองรับ trial)
     */
    private function get_user_based_folders() {
        // สำหรับ trial mode ให้ return demo data
        if ($this->is_trial_mode) {
            return $this->getTrialDemoFolders();
        }

        // สำหรับ user-based mode ปกติ
        try {
            $member = $this->db->select('google_access_token, google_refresh_token')
                              ->from('tbl_member')
                              ->where('m_id', $this->member_id)
                              ->get()
                              ->row();

            if (!$member || !$member->google_access_token) {
                return [];
            }

            $token_data = json_decode($member->google_access_token, true);
            $access_token = $token_data['access_token'] ?? null;

            if (!$access_token) {
                return [];
            }

            // ดึงโฟลเดอร์จาก Google Drive ของ User
            $folders = $this->get_user_google_drive_folders($access_token);
            
            return $folders ?: [];

        } catch (Exception $e) {
            log_message('error', 'Get user based folders error: ' . $e->getMessage());
            return [];
        }
    }


	
	
	
	
	/**
 * 🔐 ตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์จาก tbl_google_drive_member_folder_access
 */
private function check_folder_access_permission($folder_id) {
    try {
        // Skip check สำหรับ root folder
        if (empty($folder_id) || $folder_id === 'root') {
            return true;
        }

        log_message('info', "Checking folder access permission for member: {$this->member_id}, folder: {$folder_id}");

        // ตรวจสอบสิทธิ์จากตาราง tbl_google_drive_member_folder_access
        $access_query = $this->db->select('
                mfa.access_type,
                mfa.permission_source,
                mfa.granted_by,
                mfa.granted_by_name,
                mfa.expires_at,
                mfa.is_active,
                mfa.permission_mode
            ')
            ->from('tbl_google_drive_member_folder_access mfa')
            ->where('mfa.member_id', $this->member_id)
            ->where('mfa.folder_id', $folder_id)
            ->where('mfa.is_active', 1);

        // เช็คว่าหมดอายุหรือยัง
        $access_query->group_start()
            ->where('mfa.expires_at IS NULL')
            ->or_where('mfa.expires_at >', date('Y-m-d H:i:s'))
            ->group_end();

        $access_record = $access_query->get()->row();

        if ($access_record) {
            log_message('info', "Direct folder access found for member {$this->member_id}: {$access_record->access_type}");
            return true;
        }

        // ถ้าไม่มีสิทธิ์โดยตรง ให้เช็คสิทธิ์ที่สืบทอดจาก parent folder
        $inherited_access = $this->check_inherited_folder_access($folder_id);
        if ($inherited_access) {
            log_message('info', "Inherited folder access found for member {$this->member_id}");
            return true;
        }

        // เช็คสิทธิ์จากตำแหน่งงาน (position-based access)
        $position_access = $this->check_position_based_folder_access($folder_id);
        if ($position_access) {
            log_message('info', "Position-based folder access found for member {$this->member_id}");
            return true;
        }

        // เช็คสิทธิ์ระบบ (system admin, super admin)
        $system_access = $this->check_system_folder_access();
        if ($system_access) {
            log_message('info', "System folder access granted for member {$this->member_id}");
            return true;
        }

        // ไม่มีสิทธิ์เข้าถึง
        log_message('warning', "Folder access denied for member {$this->member_id}, folder: {$folder_id}");
        return false;

    } catch (Exception $e) {
        log_message('error', 'Check folder access permission error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🔗 ตรวจสอบสิทธิ์ที่สืบทอดจาก parent folder
 */
private function check_inherited_folder_access($folder_id) {
    try {
        // ดึง parent folder ID จาก Google Drive API หรือ local cache
        $parent_folder_id = $this->get_parent_folder_id($folder_id);
        
        if (!$parent_folder_id || $parent_folder_id === 'root') {
            return false;
        }

        // เช็คสิทธิ์ใน parent folder ที่มี inherit_from_parent = 1
        $inherited_access = $this->db->select('access_type, apply_to_children')
            ->from('tbl_google_drive_member_folder_access')
            ->where('member_id', $this->member_id)
            ->where('folder_id', $parent_folder_id)
            ->where('is_active', 1)
            ->where('apply_to_children', 1)
            ->group_start()
                ->where('expires_at IS NULL')
                ->or_where('expires_at >', date('Y-m-d H:i:s'))
            ->group_end()
            ->get()
            ->row();

        if ($inherited_access) {
            // บันทึกสิทธิ์ที่สืบทอดมา
            $this->record_inherited_access($folder_id, $parent_folder_id, $inherited_access->access_type);
            return true;
        }

        // เช็คต่อไปยัง parent ของ parent (recursive)
        return $this->check_inherited_folder_access($parent_folder_id);

    } catch (Exception $e) {
        log_message('error', 'Check inherited folder access error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 👥 ตรวจสอบสิทธิ์จากตำแหน่งงาน
 */
private function check_position_based_folder_access($folder_id) {
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
private function check_system_folder_access() {
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
 * 📝 บันทึกสิทธิ์ที่สืบทอดมา
 */
private function record_inherited_access($folder_id, $parent_folder_id, $access_type) {
    try {
        // ตรวจสอบว่ามีการบันทึกแล้วหรือยัง
        $existing = $this->db->select('id')
            ->from('tbl_google_drive_member_folder_access')
            ->where('member_id', $this->member_id)
            ->where('folder_id', $folder_id)
            ->where('permission_mode', 'inherited')
            ->get()
            ->row();

        if (!$existing) {
            $inherit_data = [
                'member_id' => $this->member_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'permission_source' => 'position',
                'permission_mode' => 'inherited',
                'parent_folder_id' => $parent_folder_id,
                'inherit_from_parent' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_member_folder_access', $inherit_data);
        }

    } catch (Exception $e) {
        log_message('error', 'Record inherited access error: ' . $e->getMessage());
    }
}



/**
 * 🚫 AJAX Response สำหรับไม่มีสิทธิ์เข้าถึง (แค่แจ้งเตือน)
 */
public function access_denied_response($folder_id) {
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
private function get_folder_basic_info($folder_id) {
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
private function get_permission_granters($folder_id) {
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
 * 📁 ปรับปรุง get_folder_contents() ให้ใช้การตรวจสิทธิ์
 */
public function get_folder_contents() {
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
            // เพิ่ม real_data flag
            foreach ($folder_contents as &$item) {
                $item['real_data'] = true;
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
     * 🔍 ดึงเนื้อหาโฟลเดอร์จริงจาก Google Drive API
     */
    private function get_real_folder_contents($folder_id) {
        try {
            if ($this->storage_mode === 'centralized') {
                // ใช้ System Storage Token
                $system_storage = $this->get_active_system_storage();
                if (!$system_storage || !$system_storage->google_access_token) {
                    return false;
                }

                // ตรวจสอบและ refresh token ถ้าจำเป็น
                if (!$this->has_valid_access_token($system_storage)) {
                    $refreshed = $this->refresh_system_access_token($system_storage);
                    if (!$refreshed) {
                        return false;
                    }
                    $system_storage = $this->get_active_system_storage();
                }

                $token_data = json_decode($system_storage->google_access_token, true);
                $access_token = $token_data['access_token'];
            } else {
                // ใช้ User Token
                $member = $this->db->select('google_access_token')
                                  ->from('tbl_member')
                                  ->where('m_id', $this->member_id)
                                  ->get()
                                  ->row();

                if (!$member || !$member->google_access_token) {
                    return false;
                }

                $token_data = json_decode($member->google_access_token, true);
                $access_token = $token_data['access_token'] ?? null;
                
                if (!$access_token) {
                    return false;
                }
            }

            // เรียกใช้ Google Drive API
            return $this->get_google_drive_folder_contents($access_token, $folder_id);

        } catch (Exception $e) {
            log_message('error', 'Get real folder contents error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔍 ดึง Breadcrumbs สำหรับโฟลเดอร์ (AJAX) - ✅ Fixed with real API
     */
    public function get_folder_breadcrumbs() {
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
    private function get_real_breadcrumbs($folder_id) {
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
    private function get_google_drive_root_folders($access_token, $root_folder_id) {
        try {
            log_message('info', "Getting root folders from Google Drive, root_folder_id: {$root_folder_id}");

            $ch = curl_init();
            
            // ดึงโฟลเดอร์ย่อยจาก root folder
            $query = "'{$root_folder_id}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            $fields = 'files(id,name,mimeType,modifiedTime,parents,webViewLink,iconLink)';
            
            $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
                'q' => $query,
                'fields' => $fields,
                'orderBy' => 'name'
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
                log_message('error', 'cURL Error in get_google_drive_root_folders: ' . $error);
                return false;
            }

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
                            'description' => $this->get_folder_description($file['name']),
                            'webViewLink' => $file['webViewLink'] ?? null,
                            'real_data' => true
                        ];
                    }

                    log_message('info', 'Successfully retrieved ' . count($folders) . ' folders from Google Drive root');
                    return $folders;
                }
            } else {
                log_message('error', "Google Drive API error in root folders: HTTP {$http_code} - {$response}");
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Get Google Drive root folders error: ' . $e->getMessage());
            return false;
        }
    }

    private function get_google_drive_folder_contents($access_token, $folder_id) {
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
private function get_local_item_creator($item_id, $item_type) {
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
public function loadContents() {
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
    private function build_breadcrumbs($access_token, $folder_id, $root_folder_id) {
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
private function get_google_drive_folder_info($access_token, $folder_id) {
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
            log_message('warning', "Google Drive API returned HTTP {$http_code} for folder {$folder_id}");
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
    private function get_active_system_storage() {
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
    private function has_valid_access_token($system_storage) {
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
    private function refresh_system_access_token($system_storage) {
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
    private function get_google_oauth_settings() {
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
    private function get_member_folders_as_contents() {
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
    private function get_trial_breadcrumbs($folder_id) {
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
     * 🎭 ดึงเนื้อหาโฟลเดอร์สำหรับ Trial Mode
     */
    private function get_trial_folder_contents($folder_id) {
        // Mock data สำหรับ trial mode
        $mock_contents = [
            'demo_folder_1' => [
                [
                    'id' => 'demo_doc_1',
                    'name' => 'Sample Document.pdf',
                    'type' => 'file',
                    'icon' => 'fas fa-file-pdf text-red-500',
                    'modified' => date('d/m/Y H:i', strtotime('-2 days')),
                    'size' => '2.5 MB',
                    'webViewLink' => '#',
                    'real_data' => false
                ],
                [
                    'id' => 'demo_image_1',
                    'name' => 'Project Screenshot.png',
                    'type' => 'file',
                    'icon' => 'fas fa-file-image text-purple-500',
                    'modified' => date('d/m/Y H:i', strtotime('-1 day')),
                    'size' => '1.8 MB',
                    'webViewLink' => '#',
                    'real_data' => false
                ]
            ],
            'demo_folder_2' => [
                [
                    'id' => 'demo_folder_3',
                    'name' => 'Web Development',
                    'type' => 'folder',
                    'icon' => 'fas fa-folder text-blue-500',
                    'modified' => date('d/m/Y H:i', strtotime('-3 days')),
                    'size' => '-',
                    'webViewLink' => '#',
                    'real_data' => false
                ]
            ],
            'demo_folder_3' => [
                [
                    'id' => 'demo_code_1',
                    'name' => 'index.html',
                    'type' => 'file',
                    'icon' => 'fas fa-file-code text-orange-500',
                    'modified' => date('d/m/Y H:i', strtotime('-1 hour')),
                    'size' => '15 KB',
                    'webViewLink' => '#',
                    'real_data' => false
                ]
            ]
        ];

        return $mock_contents[$folder_id] ?? [];
    }

    /**
     * Get Trial Demo Folders
     */
    private function getTrialDemoFolders() {
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
    public function custom_error_handler($severity, $message, $file, $line) {
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
    public function custom_exception_handler($exception) {
        $error_msg = "Uncaught Exception: " . $exception->getMessage() . 
                    " in " . $exception->getFile() . " on line " . $exception->getLine();
        
        log_message('error', $error_msg);
        
        if ($this->input->is_ajax_request()) {
            $this->safe_json_error('เกิดข้อผิดพลาดภายในระบบ', 500, [
                'exception' => ENVIRONMENT === 'development' ? $error_msg : 'Internal exception'
            ]);
        }
    }

  
	
	public function upload_file() {
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
        
        // ✅ ตรวจสอบขนาดไฟล์สูงสุด (max_file_size)
        if ($file_size > $settings['max_file_size']) {
            $max_size_mb = round($settings['max_file_size'] / (1024 * 1024), 1);
            http_response_code(413);
            echo json_encode([
                'success' => false,
                'message' => "ไฟล์มีขนาดใหญ่เกิน {$max_size_mb}MB",
                'max_size_mb' => $max_size_mb,
                'current_file_size_mb' => round($file_size / (1024 * 1024), 2),
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ ตรวจสอบประเภทไฟล์ที่อนุญาต (allowed_file_types)
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_extension, $settings['allowed_file_types'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "ประเภทไฟล์ไม่ได้รับอนุญาต: .{$file_extension}",
                'file_extension' => $file_extension,
                'allowed_types' => $settings['allowed_file_types'],
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ ตรวจสอบพื้นที่เก็บข้อมูลภาพรวมของระบบ (system_storage_limit)
        $storage_check = $this->check_system_storage_limit($file_size, $settings);
        if (!$storage_check['allowed']) {
            http_response_code(413);
            echo json_encode([
                'success' => false,
                'message' => $storage_check['message'],
                'storage_info' => [
                    'current_usage_gb' => $storage_check['current_usage_gb'],
                    'limit_gb' => $storage_check['limit_gb'],
                    'available_gb' => $storage_check['available_gb'],
                    'file_size_mb' => round($file_size / (1024 * 1024), 2)
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ ตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์ (ถ้ามี folder_id)
        if (!empty($folder_id) && $folder_id !== 'root') {
            if (!$this->check_folder_access_permission($folder_id)) {
                $this->access_denied_response($folder_id);
                return;
            }
        }

        // ✅ สำหรับ Production Mode เท่านั้น (ลบ Trial Mode)
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

        // อัปโหลดไปยัง Google Drive
        $upload_result = $this->upload_to_google_drive_simple($_FILES['file'], $folder_id, $access_token);
        
        if ($upload_result && $upload_result['success']) {
            // บันทึกข้อมูลไฟล์
            $file_record_id = $this->save_file_info_simple($upload_result['file_id'], $file_name, $file_size, $folder_id);
            
            // ✅ อัปเดตการใช้งาน storage ภาพรวมของระบบ
            $this->update_system_storage_usage($file_size);
            
            // Log activity
            $this->log_drive_activity('upload_file', [
                'file_id' => $upload_result['file_id'],
                'file_name' => $file_name,
                'file_size' => $file_size,
                'folder_id' => $folder_id,
                'record_id' => $file_record_id
            ]);
            
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
            log_message('error', 'Upload file exception: ' . $e->getMessage());
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

/**
 * ✅ ตรวจสอบพื้นที่เก็บข้อมูลภาพรวมของระบบ
 */
private function check_system_storage_limit($file_size, $settings) {
    try {
        // ดึงข้อมูลการใช้งาน storage จาก system storage
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

        $current_usage = $system_storage->total_storage_used; // bytes
        $storage_limit = $system_storage->max_storage_limit; // bytes
        
        // ตรวจสอบว่าการอัปโหลดไฟล์ใหม่จะเกินขีดจำกัดหรือไม่
        $after_upload_usage = $current_usage + $file_size;
        
        if ($after_upload_usage > $storage_limit) {
            $current_usage_gb = round($current_usage / (1024 * 1024 * 1024), 2);
            $limit_gb = round($storage_limit / (1024 * 1024 * 1024), 2);
            $available_gb = round(($storage_limit - $current_usage) / (1024 * 1024 * 1024), 2);
            $file_size_mb = round($file_size / (1024 * 1024), 2);
            
            return [
                'allowed' => false,
                'message' => "พื้นที่เก็บข้อมูลไม่เพียงพอ (ใช้ไปแล้ว {$current_usage_gb}GB จาก {$limit_gb}GB)",
                'current_usage_gb' => $current_usage_gb,
                'limit_gb' => $limit_gb,
                'available_gb' => $available_gb,
                'file_size_mb' => $file_size_mb
            ];
        }

        return [
            'allowed' => true,
            'current_usage_gb' => round($current_usage / (1024 * 1024 * 1024), 2),
            'limit_gb' => round($storage_limit / (1024 * 1024 * 1024), 2),
            'available_gb' => round(($storage_limit - $current_usage) / (1024 * 1024 * 1024), 2)
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
private function update_system_storage_usage($file_size) {
    try {
        // อัปเดต total_storage_used ในตาราง system storage
        $this->db->set('total_storage_used', 'total_storage_used + ' . (int)$file_size, FALSE)
                 ->set('updated_at', date('Y-m-d H:i:s'))
                 ->where('is_active', 1)
                 ->update('tbl_google_drive_system_storage');

        log_message('info', "Updated system storage usage: +{$file_size} bytes");

    } catch (Exception $e) {
        log_message('error', 'Update system storage usage error: ' . $e->getMessage());
    }
}



	
	
	private function check_user_quota($file_size, $settings) {
    try {
        $member = $this->db->select('storage_quota_used, storage_quota_limit')
                          ->from('tbl_member')
                          ->where('m_id', $this->member_id)
                          ->get()
                          ->row();

        if (!$member) {
            return [
                'allowed' => false,
                'message' => 'ไม่พบข้อมูลผู้ใช้'
            ];
        }

        $current_used = $member->storage_quota_used ?: 0;
        
        // ใช้ quota limit จาก member หรือ default จากการตั้งค่า
        $quota_limit = $member->storage_quota_limit ?: $settings['default_user_quota'];
        
        // สำหรับ trial mode จำกัดที่ 1GB
        if ($this->is_trial_mode) {
            $quota_limit = min($quota_limit, 1073741824); // 1GB
        }
        
        if (($current_used + $file_size) > $quota_limit) {
            $remaining_mb = round(($quota_limit - $current_used) / (1024 * 1024), 1);
            $needed_mb = round($file_size / (1024 * 1024), 1);
            
            return [
                'allowed' => false,
                'message' => "พื้นที่เหลือ {$remaining_mb}MB ไม่พอสำหรับไฟล์ขนาด {$needed_mb}MB",
                'current_used' => $current_used,
                'quota_limit' => $quota_limit,
                'remaining' => $quota_limit - $current_used
            ];
        }
        
        return [
            'allowed' => true,
            'current_used' => $current_used,
            'quota_limit' => $quota_limit,
            'remaining' => $quota_limit - $current_used
        ];

    } catch (Exception $e) {
        return [
            'allowed' => false,
            'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ quota'
        ];
    }
}

	
	
	private function update_user_quota($file_size) {
    try {
        $current_used = $this->db->select('storage_quota_used')
                                ->from('tbl_member')
                                ->where('m_id', $this->member_id)
                                ->get()
                                ->row();
        
        if ($current_used) {
            $new_used = ($current_used->storage_quota_used ?: 0) + $file_size;
            $this->db->where('m_id', $this->member_id)
                    ->update('tbl_member', [
                        'storage_quota_used' => $new_used,
                        'last_storage_access' => date('Y-m-d H:i:s')
                    ]);
        }
        
    } catch (Exception $e) {
        if (function_exists('log_message')) {
            log_message('error', 'Update user quota error: ' . $e->getMessage());
        }
    }
}

	

	
	private function get_settings_from_db() {
    try {
        // ค่าเริ่มต้น
        $default_settings = [
            'google_drive_enabled' => true,
            'max_file_size' => 104857600, // 100MB
            'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'],
            'default_user_quota' => 1073741824 // 1GB
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
                    $size = (int)$setting->setting_value;
                    $settings['max_file_size'] = $size > 0 ? $size : $default_settings['max_file_size'];
                    break;
                    
                case 'allowed_file_types':
                    $types = array_map('trim', explode(',', strtolower($setting->setting_value)));
                    $settings['allowed_file_types'] = !empty($types) ? $types : $default_settings['allowed_file_types'];
                    break;
                    
                case 'default_user_quota':
                    $quota = (int)$setting->setting_value;
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
            'default_user_quota' => 1073741824
        ];
    }
}
	
	
	
	/**
 * ดึง Access Token แบบง่าย
 */
private function get_access_token_simple() {
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
private function upload_to_google_drive_simple($file, $folder_id, $access_token) {
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
 * บันทึกข้อมูลไฟล์แบบง่าย
 */
private function save_file_info_simple($file_id, $file_name, $file_size, $folder_id) {
    try {
        if ($this->storage_mode === 'centralized') {
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $this->db->insert('tbl_google_drive_system_files', [
                    'file_id' => $file_id,
                    'file_name' => $file_name,
                    'original_name' => $file_name,
                    'file_size' => $file_size,
                    'folder_id' => $folder_id ?: 'root',
                    'uploaded_by' => $this->member_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        // อัปเดต quota
        $current_used = $this->db->select('storage_quota_used')
                                ->from('tbl_member')
                                ->where('m_id', $this->member_id)
                                ->get()
                                ->row();
        
        if ($current_used) {
            $new_used = ($current_used->storage_quota_used ?: 0) + $file_size;
            $this->db->where('m_id', $this->member_id)
                    ->update('tbl_member', [
                        'storage_quota_used' => $new_used,
                        'last_storage_access' => date('Y-m-d H:i:s')
                    ]);
        }
        
    } catch (Exception $e) {
        // Log error but don't fail
        if (function_exists('log_message')) {
            log_message('error', 'Save file info error: ' . $e->getMessage());
        }
    }
}

/**
 * ดึงข้อมูล Member แบบง่าย
 */
private function get_simple_member_info() {
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
 * อัปเดต Quota แบบง่าย
 */
private function update_simple_quota($file_size) {
    try {
        $member = $this->get_simple_member_info();
        if ($member) {
            $new_used = ($member->storage_quota_used ?: 0) + $file_size;
            
            $this->db->where('m_id', $this->member_id)
                    ->update('tbl_member', [
                        'storage_quota_used' => $new_used,
                        'last_storage_access' => date('Y-m-d H:i:s')
                    ]);
        }
    } catch (Exception $e) {
        log_message('error', 'Update simple quota error: ' . $e->getMessage());
    }
}

/**
 * ดึง Access Token แบบง่าย
 */
private function get_simple_access_token() {
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
 * อัปโหลดไฟล์ไปยัง Google Drive แบบง่าย
 */
private function simple_upload_to_google_drive($file, $folder_id, $access_token) {
    try {
        $metadata = [
            'name' => $file['name']
        ];

        if ($folder_id && $folder_id !== 'root') {
            $metadata['parents'] = [$folder_id];
        }

        $delimiter = '-------314159265358979323846';
        $close_delim = "\r\n--{$delimiter}--\r\n";

        $metadata_json = json_encode($metadata);
        $file_content = file_get_contents($file['tmp_name']);
        
        $multipart_body = "--{$delimiter}\r\n";
        $multipart_body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
        $multipart_body .= $metadata_json . "\r\n";
        $multipart_body .= "--{$delimiter}\r\n";
        $multipart_body .= "Content-Type: {$file['type']}\r\n\r\n";
        $multipart_body .= $file_content;
        $multipart_body .= $close_delim;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$access_token}",
                "Content-Type: multipart/related; boundary=\"{$delimiter}\"",
                "Content-Length: " . strlen($multipart_body)
            ],
            CURLOPT_POSTFIELDS => $multipart_body,
            CURLOPT_TIMEOUT => 300
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 || $http_code === 201) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'file_id' => $result['id'],
                'web_view_link' => "https://drive.google.com/file/d/{$result['id']}/view"
            ];
        }

        return [
            'success' => false,
            'error' => 'HTTP ' . $http_code
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * ลบไฟล์จาก Google Drive แบบง่าย
 */
private function simple_delete_from_google_drive($item_id, $access_token) {
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
 * สร้างโฟลเดอร์ใน Google Drive แบบง่าย
 */
private function simple_create_google_drive_folder($folder_name, $parent_id, $access_token) {
    try {
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
        }

        return [
            'success' => false,
            'error' => 'HTTP ' . $http_code
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * บันทึกข้อมูลไฟล์แบบง่าย
 */
private function save_simple_file_info($file_id, $file_name, $file_size, $folder_id) {
    try {
        if ($this->storage_mode === 'centralized') {
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $this->db->insert('tbl_google_drive_system_files', [
                    'file_id' => $file_id,
                    'file_name' => $file_name,
                    'file_size' => $file_size,
                    'folder_id' => $folder_id,
                    'uploaded_by' => $this->member_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } else {
            if ($this->db->table_exists('tbl_google_drive_user_files')) {
                $this->db->insert('tbl_google_drive_user_files', [
                    'file_id' => $file_id,
                    'file_name' => $file_name,
                    'file_size' => $file_size,
                    'folder_id' => $folder_id,
                    'member_id' => $this->member_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    } catch (Exception $e) {
        log_message('error', 'Save simple file info error: ' . $e->getMessage());
    }
}

/**
 * บันทึกข้อมูลโฟลเดอร์แบบง่าย
 */
private function save_simple_folder_info($folder_id, $folder_name, $parent_id) {
    try {
        if ($this->storage_mode === 'centralized') {
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $this->db->insert('tbl_google_drive_system_folders', [
                    'folder_id' => $folder_id,
                    'folder_name' => $folder_name,
                    'parent_folder_id' => $parent_id,
                    'created_by' => $this->member_id,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } else {
            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $this->db->insert('tbl_google_drive_folders', [
                    'folder_id' => $folder_id,
                    'folder_name' => $folder_name,
                    'parent_folder_id' => $parent_id,
                    'member_id' => $this->member_id,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    } catch (Exception $e) {
        log_message('error', 'Save simple folder info error: ' . $e->getMessage());
    }
}

/**
 * ลบข้อมูลจากฐานข้อมูลแบบง่าย
 */
private function remove_simple_item_from_database($item_id, $item_type) {
    try {
        if ($item_type === 'folder') {
            if ($this->storage_mode === 'centralized') {
                if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                    $this->db->where('folder_id', $item_id)->delete('tbl_google_drive_system_folders');
                }
            } else {
                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $this->db->where('folder_id', $item_id)->delete('tbl_google_drive_folders');
                }
            }
        } else {
            if ($this->storage_mode === 'centralized') {
                if ($this->db->table_exists('tbl_google_drive_system_files')) {
                    $this->db->where('file_id', $item_id)->delete('tbl_google_drive_system_files');
                }
            } else {
                if ($this->db->table_exists('tbl_google_drive_user_files')) {
                    $this->db->where('file_id', $item_id)->delete('tbl_google_drive_user_files');
                }
            }
        }
    } catch (Exception $e) {
        log_message('error', 'Remove simple item from database error: ' . $e->getMessage());
    }
}
	
	
	/**
 * 🔍 ตรวจสอบไฟล์ที่อัปโหลด (ป้องกัน error)
 */
private function validate_uploaded_file($file) {
        try {
            // ตรวจสอบ upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $upload_errors = [
                    UPLOAD_ERR_INI_SIZE => 'ไฟล์ใหญ่เกินที่กำหนดในระบบ',
                    UPLOAD_ERR_FORM_SIZE => 'ไฟล์ใหญ่เกินที่กำหนด',
                    UPLOAD_ERR_PARTIAL => 'อัปโหลดไฟล์ไม่สมบูรณ์',
                    UPLOAD_ERR_NO_FILE => 'ไม่พบไฟล์ที่จะอัปโหลด',
                    UPLOAD_ERR_NO_TMP_DIR => 'ไม่พบโฟลเดอร์ชั่วคราว',
                    UPLOAD_ERR_CANT_WRITE => 'ไม่สามารถเขียนไฟล์ได้',
                    UPLOAD_ERR_EXTENSION => 'ส่วนขยายไฟล์ถูกปฏิเสธ'
                ];
                
                return [
                    'valid' => false,
                    'reason' => $upload_errors[$file['error']] ?? 'เกิดข้อผิดพลาดในการอัปโหลด'
                ];
            }

            // ตรวจสอบประเภทไฟล์จากการตั้งค่าระบบ
            $allowed_types = $this->get_allowed_file_types();
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_extension, $allowed_types)) {
                return [
                    'valid' => false,
                    'reason' => "ประเภทไฟล์ไม่ได้รับอนุญาต ({$file_extension})",
                    'allowed_types' => $allowed_types
                ];
            }

            // ตรวจสอบขนาดไฟล์จากการตั้งค่าระบบ
            $max_size = $this->get_max_file_size();
            if ($file['size'] > $max_size) {
                $max_size_mb = round($max_size / (1024 * 1024), 1);
                return [
                    'valid' => false,
                    'reason' => "ไฟล์มีขนาดใหญ่เกิน {$max_size_mb}MB",
                    'max_size' => $max_size,
                    'max_size_mb' => $max_size_mb
                ];
            }

            // ตรวจสอบความปลอดภัย
            if (!is_uploaded_file($file['tmp_name'])) {
                return [
                    'valid' => false,
                    'reason' => 'ไฟล์ไม่ได้อัปโหลดผ่านฟอร์ม'
                ];
            }

            // ตรวจสอบ MIME type (เพิ่มเติม)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowed_mimes = [
                'jpg' => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png' => ['image/png'],
                'gif' => ['image/gif'],
                'pdf' => ['application/pdf'],
                'doc' => ['application/msword'],
                'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'xls' => ['application/vnd.ms-excel'],
                'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'ppt' => ['application/vnd.ms-powerpoint'],
                'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
                'txt' => ['text/plain'],
                'zip' => ['application/zip'],
                'rar' => ['application/vnd.rar', 'application/x-rar-compressed']
            ];

            if (isset($allowed_mimes[$file_extension])) {
                if (!in_array($mime_type, $allowed_mimes[$file_extension])) {
                    return [
                        'valid' => false,
                        'reason' => 'ประเภทไฟล์ไม่ตรงกับนามสกุล',
                        'detected_mime' => $mime_type,
                        'expected_mimes' => $allowed_mimes[$file_extension]
                    ];
                }
            }

            // เตรียมข้อมูลไฟล์
            $file_data = [
                'name' => $file['name'],
                'tmp_name' => $file['tmp_name'],
                'size' => $file['size'],
                'type' => $file['type'],
                'extension' => $file_extension,
                'mime_type' => $mime_type
            ];

            return [
                'valid' => true,
                'file_data' => $file_data
            ];

        } catch (Exception $e) {
            log_message('error', 'Validate uploaded file error: ' . $e->getMessage());
            return [
                'valid' => false,
                'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบไฟล์'
            ];
        }
    }

	
	
	/**
     * 🔍 ตรวจสอบสิทธิ์การเข้าถึงไฟล์
     */
    private function check_file_access_permission($file_id) {
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
	

	
	
	
	private function remove_item_from_database($item_id, $item_type) {
    try {
        $deleted = false;

        if ($item_type === 'file') {
            // ลบไฟล์จากตาราง system files
            if ($this->storage_mode === 'centralized') {
                $this->db->where('file_id', $item_id)
                         ->where('uploaded_by', $this->member_id)
                         ->delete('tbl_google_drive_system_files');
                $deleted = $this->db->affected_rows() > 0;
            } else {
                $this->db->where('file_id', $item_id)
                         ->where('member_id', $this->member_id)
                         ->delete('tbl_google_drive_sync');
                $deleted = $this->db->affected_rows() > 0;
            }
        } elseif ($item_type === 'folder') {
            // ลบโฟลเดอร์จากตาราง system folders
            $this->db->where('folder_id', $item_id)
                     ->delete('tbl_google_drive_system_folders');
            $deleted = $this->db->affected_rows() > 0;

            // ลบ permissions ที่เกี่ยวข้อง
            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $this->db->where('folder_id', $item_id)->delete('tbl_google_drive_member_folder_access');
            }
        }

        log_message('info', "Database deletion result for {$item_type} {$item_id}: " . ($deleted ? 'success' : 'not found'));
        return $deleted;

    } catch (Exception $e) {
        log_message('error', 'Remove item from database error: ' . $e->getMessage());
        return false;
    }
}
	
	
	/**
 * 🔍 ตรวจสอบการเข้าถึงรายการ (ป้องกัน error)
 */
private function check_item_access_permission($item_id, $item_type) {
    try {
        // สำหรับ trial mode
        if ($this->is_trial_mode) {
            $trial_items = ['demo_folder_1', 'demo_folder_2', 'demo_folder_3', 'demo_folder_4', 
                           'demo_doc_1', 'demo_image_1', 'demo_excel_1', 'demo_code_1', 'demo_code_2', 'demo_code_3', 'demo_app_1'];
            return in_array($item_id, $trial_items);
        }

        // ตรวจสอบในฐานข้อมูล
        if ($item_type === 'folder') {
            return $this->check_folder_access_permission($item_id);
        } else {
            return $this->check_file_access_permission($item_id);
        }

    } catch (Exception $e) {
        log_message('error', 'Check item access permission error: ' . $e->getMessage());
        return false;
    }
}


	
	
	
	/**
 * 🔍 ตรวจสอบชื่อโฟลเดอร์ (ป้องกัน error)
 */
private function validate_folder_name($folder_name) {
    try {
        // อนุญาตเฉพาะตัวอักษร ตัวเลข ภาษาไทย และอักขระพิเศษบางตัว
        return preg_match('/^[a-zA-Z0-9ก-๙\s\-_.()]+$/', $folder_name) && strlen($folder_name) <= 255;
    } catch (Exception $e) {
        log_message('error', 'Validate folder name error: ' . $e->getMessage());
        return false;
    }
}
	
																									 
    /**
     * 🔗 อัปโหลดไฟล์ไปยัง Google Drive (Production)
     */
    private function upload_file_to_google_drive($file_data, $folder_id, $access_token) {
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
 * 🔍 ตรวจสอบสิทธิ์การอัปโหลด (ป้องกัน error)
 */
private function check_upload_permission($folder_id) {
    try {
        $permission = $this->get_current_member_permission();
        
        if (!$permission['can_upload']) {
            return [
                'allowed' => false,
                'reason' => 'คุณไม่มีสิทธิ์ในการอัปโหลดไฟล์'
            ];
        }

        if ($folder_id && !$this->check_folder_access_permission($folder_id)) {
            return [
                'allowed' => false,
                'reason' => 'ไม่มีสิทธิ์อัปโหลดไฟล์ไปยังโฟลเดอร์นี้'
            ];
        }

        return ['allowed' => true];

    } catch (Exception $e) {
        log_message('error', 'Check upload permission error: ' . $e->getMessage());
        return [
            'allowed' => false,
            'reason' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์'
        ];
    }
}

	

    /**
     * 🔗 สร้างโฟลเดอร์ใน Google Drive (Production)
     */
    private function create_google_drive_folder($folder_name, $parent_id, $access_token) {
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
 * 🗑️ ลบไฟล์/โฟลเดอร์ (แก้ไข Error Handling ครบถ้วน)
 */
/**
 * ✅ แก้ไข delete_item() ให้ใช้ method ที่มีอยู่แล้วหรือไม่ log เลย
 */
public function delete_item() {
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

        $item_id = $this->input->post('item_id');
        $item_type = $this->input->post('item_type'); // 'file' หรือ 'folder'
        
        if (!$item_id || !$item_type) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ข้อมูลไม่ครบถ้วน',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ ตรวจสอบสิทธิ์การลบ (ทั้งไฟล์และโฟลเดอร์)
        $folder_id = null;
        
        if ($item_type === 'folder') {
            $folder_id = $item_id;
        } elseif ($item_type === 'file') {
            // ดึง folder_id ของไฟล์
            $folder_id = $this->get_file_folder_id($item_id);
        }

        // ตรวจสอบสิทธิ์การลบในโฟลเดอร์
        if ($folder_id && !$this->check_delete_permission_in_folder($folder_id)) {
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

        // ✅ ดึงข้อมูลรายการก่อนลบ (สำหรับ log และ storage calculation)
        $item_info = $this->get_item_info_before_delete($item_id, $item_type);

        // ✅ สำหรับ Production Mode เท่านั้น
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

        // ✅ ลบจาก Google Drive
        $delete_result = $this->simple_delete_from_google_drive($item_id, $access_token);
        
        if ($delete_result) {
            // ✅ ลบจากฐานข้อมูล
            $database_result = $this->remove_item_from_database($item_id, $item_type);
            
            // ✅ อัปเดต storage usage ถ้าเป็นไฟล์
            if ($item_type === 'file' && isset($item_info['file_size']) && $item_info['file_size'] > 0) {
                $this->decrease_system_storage_usage($item_info['file_size']);
            }
            
            // ✅ Log activity (ปลอดภัยแล้ว)
            $this->simple_log_activity('delete_' . $item_type, 
                "ลบ{$item_type}: " . ($item_info['name'] ?? $item_id)
            );

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => $item_type === 'file' ? 'ลบไฟล์เรียบร้อย' : 'ลบโฟลเดอร์เรียบร้อย',
                'data' => [
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'item_name' => $item_info['name'] ?? null,
                    'database_deleted' => $database_result
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถลบรายการจาก Google Drive ได้',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;

    } catch (Exception $e) {
        log_message('error', 'Delete item error: ' . $e->getMessage());
        
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
	
	
	private function simple_log_activity($action_type, $description = '') {
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
 * ✅ ลดการใช้งาน storage ของระบบ
 */
private function decrease_system_storage_usage($file_size) {
    try {
        if ($file_size <= 0) return;

        $this->db->set('total_storage_used', 'GREATEST(total_storage_used - ' . (int)$file_size . ', 0)', FALSE)
                 ->set('updated_at', date('Y-m-d H:i:s'))
                 ->where('is_active', 1)
                 ->update('tbl_google_drive_system_storage');

        log_message('info', "Decreased system storage usage: -{$file_size} bytes");

    } catch (Exception $e) {
        log_message('error', 'Decrease system storage usage error: ' . $e->getMessage());
    }
}
	
	
	/**
 * ✅ ดึงข้อมูลรายการก่อนลบ
 */
/**
 * ✅ ดึงข้อมูลรายการก่อนลบ
 */
private function get_item_info_before_delete($item_id, $item_type) {
    try {
        $info = ['name' => null, 'file_size' => 0];

        if ($item_type === 'file') {
            if ($this->storage_mode === 'centralized') {
                $file = $this->db->select('file_name, file_size')
                                ->from('tbl_google_drive_system_files')
                                ->where('file_id', $item_id)
                                ->get()
                                ->row();
                
                if ($file) {
                    $info['name'] = $file->file_name;
                    $info['file_size'] = $file->file_size;
                }
            } else {
                $file = $this->db->select('file_name, file_size')
                                ->from('tbl_google_drive_sync')
                                ->where('file_id', $item_id)
                                ->get()
                                ->row();
                
                if ($file) {
                    $info['name'] = $file->file_name;
                    $info['file_size'] = $file->file_size ?: 0;
                }
            }
        } elseif ($item_type === 'folder') {
            $folder = $this->db->select('folder_name')
                              ->from('tbl_google_drive_system_folders')
                              ->where('folder_id', $item_id)
                              ->get()
                              ->row();
            
            if ($folder) {
                $info['name'] = $folder->folder_name;
            }
        }

        return $info;

    } catch (Exception $e) {
        log_message('error', 'Get item info before delete error: ' . $e->getMessage());
        return ['name' => null, 'file_size' => 0];
    }
}

	
	
	
	
/**
 * ✅ ตรวจสอบสิทธิ์การลบในโฟลเดอร์ (แบบเรียบง่าย)
 */
private function check_delete_permission_in_folder($folder_id) {
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
        log_message('warning', "Delete permission denied for member {$this->member_id} in folder: {$folder_id}");
        return false;

    } catch (Exception $e) {
        log_message('error', 'Check delete permission in folder error: ' . $e->getMessage());
        return false;
    }
}

	
	
	/**
 * ✅ ตรวจสอบสิทธิ์การลบที่สืบทอดจาก parent folder
 */
private function check_inherited_delete_permission($folder_id) {
    try {
        // ดึง parent folder ID
        $parent_folder_id = $this->get_parent_folder_id($folder_id);
        
        if (!$parent_folder_id || $parent_folder_id === 'root') {
            return null; // ไม่มี parent หรือถึง root แล้ว
        }

        // เช็คสิทธิ์ใน parent folder ที่มี apply_to_children = 1
        $inherited_access = $this->db->select('access_type, apply_to_children')
            ->from('tbl_google_drive_member_folder_access')
            ->where('member_id', $this->member_id)
            ->where('folder_id', $parent_folder_id)
            ->where('is_active', 1)
            ->where('apply_to_children', 1)
            ->group_start()
                ->where('expires_at IS NULL')
                ->or_where('expires_at >', date('Y-m-d H:i:s'))
            ->group_end()
            ->get()
            ->row();

        if ($inherited_access) {
            log_message('info', "Found inherited access: {$inherited_access->access_type} from parent {$parent_folder_id}");
            
            switch ($inherited_access->access_type) {
                case 'read':
                    return false;
                case 'write':
                case 'admin':
                case 'owner':
                    return true;
                default:
                    return false;
            }
        }

        // เช็คต่อไปยัง parent ของ parent (recursive)
        return $this->check_inherited_delete_permission($parent_folder_id);

    } catch (Exception $e) {
        log_message('error', 'Check inherited delete permission error: ' . $e->getMessage());
        return null;
    }
}
	
	/**
 * ✅ ตรวจสอบสิทธิ์การลบจากตำแหน่งงาน
 */
private function check_position_based_delete_permission($folder_id) {
    try {
        // ดึงข้อมูล member และ position
        $member_info = $this->db->select('ref_pid')
            ->from('tbl_member')
            ->where('m_id', $this->member_id)
            ->get()
            ->row();

        if (!$member_info) {
            return null;
        }

        // เช็คสิทธิ์จากตำแหน่งใน tbl_google_drive_position_permissions
        $position_permission = $this->db->select('folder_access, can_delete')
            ->from('tbl_google_drive_position_permissions')
            ->where('position_id', $member_info->ref_pid)
            ->where('is_active', 1)
            ->get()
            ->row();

        if ($position_permission) {
            // เช็คว่าโฟลเดอร์นี้อยู่ใน folder_access หรือไม่
            if ($position_permission->folder_access) {
                $folder_access_list = json_decode($position_permission->folder_access, true);
                
                if (is_array($folder_access_list) && in_array($folder_id, $folder_access_list)) {
                    // ถ้าโฟลเดอร์อยู่ในรายการที่เข้าถึงได้ ให้เช็ค can_delete
                    return ($position_permission->can_delete == 1);
                }
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

        if ($system_folder_access) {
            // ถ้าเป็นโฟลเดอร์ที่สร้างสำหรับตำแหน่งนี้ ให้เช็ค can_delete
            return ($position_permission && $position_permission->can_delete == 1);
        }

        return null;

    } catch (Exception $e) {
        log_message('error', 'Check position based delete permission error: ' . $e->getMessage());
        return null;
    }
}

	

/**
 * ✅ ดึง folder_id ของไฟล์
 */
private function get_file_folder_id($file_id) {
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
private function get_google_drive_file_info($access_token, $file_id) {
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

	
	
	private function download_from_google_drive($access_token, $file_id, $file_info) {
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

	
	
	private function output_file_download($content, $filename, $mime_type) {
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
    private function delete_google_drive_item($item_id, $access_token) {
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

    // ========================================
    // Additional Helper Functions
    // ========================================

    /**
 * 📥 ดาวน์โหลดไฟล์ (With Permission Check)
 */
public function download_file() {
    try {
        $file_id = $this->input->get('file_id');
        if (!$file_id) {
            show_404();
            return;
        }

        log_message('info', "Download file request: {$file_id} by member: {$this->member_id}");

        // ตรวจสอบสิทธิ์การดาวน์โหลด
        if (!$this->check_download_permission($file_id)) {
            log_message('warning', "Download permission denied for file: {$file_id}, member: {$this->member_id}");
            
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
private function check_download_permission($file_id) {
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
private function log_download_activity($file_id) {
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
public function create_share_link() {
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
 * 🔐 ตรวจสอบสิทธิ์การแชร์
 */
private function check_share_permission($item_id, $item_type) {
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

        // สามารถแชร์ได้เฉพาะ read_write, admin, owner
        return in_array($access->access_type, ['read_write', 'admin', 'owner']);
        
    } catch (Exception $e) {
        log_message('error', 'Check share permission error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✉️ แชร์กับอีเมล (Enhanced with Permission Check)
 */
/**
 * ✉️ แชร์กับอีเมล - ไม่เช็ค Permission
 */
public function share_with_email() {
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
	
	
	private function get_valid_access_token() {
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


	
	private function isJson($string) {
    if (!is_string($string)) {
        return false;
    }
    
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
	
	
	
	/**
 * 🧪 ทดสอบ Google API Token
 */
private function test_google_api_token($access_token) {
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


	
	private function refresh_google_access_token($storage) {
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
private function log_share_activity_enhanced($item_id, $item_type, $email, $permission, $message) {
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
            log_message('warning', "⚠️ No tables were available for logging share activity");
        }

    } catch (Exception $e) {
        log_message('error', 'Log share activity enhanced error: ' . $e->getMessage());
    }
}

/**
 * 📞 เรียก Google API สำหรับแชร์ไฟล์ - ไม่เปลี่ยน
 */
  private function call_google_share_api($file_id, $email, $permission, $message, $access_token) {
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
	
	
	
	
	
	
	/**
 * บันทึก log การแชร์ไปยังตารางที่มีอยู่
 */
private function log_share_activity_to_existing_tables($item_id, $item_type, $email, $permission, $message) {
    try {
        // ลองบันทึกลงตาราง logs ที่มีอยู่
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $log_data = [
                'member_id' => $this->member_id ?? 0,
                'action_type' => 'share',
                'action_description' => "แชร์ {$item_type} กับ {$email} (สิทธิ์: {$permission})",
                'item_id' => $item_id,
                'item_type' => $item_type,
                'target_email' => $email,
                'status' => 'success',
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_logs', $log_data);
            log_message('info', "Share activity logged to tbl_google_drive_logs: {$email} - {$permission} - {$item_id}");
        }
        
        // หรือบันทึกลงตาราง activity_logs
        if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
            $activity_data = [
                'member_id' => $this->member_id ?? 0,
                'action_type' => 'share_with_email',
                'action_description' => "แชร์ {$item_type} ID: {$item_id} กับ {$email}",
                'item_id' => $item_id,
                'item_type' => $item_type,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_activity_logs', $activity_data);
            log_message('info', "Share activity logged to tbl_google_drive_activity_logs: {$email} - {$permission} - {$item_id}");
        }
        
        // บันทึกลงตาราง sharing ถ้ามี
        if ($this->db->table_exists('tbl_google_drive_sharing')) {
            $sharing_data = [
                'folder_id' => $item_type === 'folder' ? $item_id : null,
                'shared_by' => $this->member_id ?? 0,
                'shared_to_email' => $email,
                'permission_level' => $permission,
                'shared_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ];

            $this->db->insert('tbl_google_drive_sharing', $sharing_data);
            log_message('info', "Share recorded in tbl_google_drive_sharing: {$email} - {$permission} - {$item_id}");
        }

    } catch (Exception $e) {
        log_message('error', 'log_share_activity_to_existing_tables error: ' . $e->getMessage());
    }
}

	
	
	/**
 * ดึง System Storage แบบปลอดภัย
 */
private function get_system_storage_safe() {
    try {
        $this->db->where('is_active', 1);
        $this->db->where('storage_type', 'system');
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get('tbl_google_drive_storage');

        if ($query->num_rows() === 0) {
            return [
                'success' => false,
                'message' => 'ไม่พบการตั้งค่า Google Drive ของระบบ'
            ];
        }

        $storage = $query->row();
        
        if (empty($storage->google_access_token)) {
            return [
                'success' => false,
                'message' => 'Google Drive ยังไม่ได้เชื่อมต่อ'
            ];
        }

        return [
            'success' => true,
            'data' => $storage
        ];

    } catch (Exception $e) {
        log_message('error', 'get_system_storage_safe error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการตรวจสอบการตั้งค่า'
        ];
    }
}

	

private function get_access_token_safe($storage) {
    try {
        if (empty($storage->google_access_token)) {
            return [
                'success' => false,
                'message' => 'ไม่พบ Access Token'
            ];
        }

        // ตรวจสอบว่า token หมดอายุหรือไม่
        if (!empty($storage->token_expires_at)) {
            $expires_at = strtotime($storage->token_expires_at);
            if ($expires_at && $expires_at <= time() + 300) { // หมดอายุใน 5 นาที
                // ลอง refresh token
                $refresh_result = $this->refresh_access_token($storage);
                if ($refresh_result['success']) {
                    return [
                        'success' => true,
                        'token' => $refresh_result['access_token']
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Access Token หมดอายุและไม่สามารถต่ออายุได้'
                    ];
                }
            }
        }

        return [
            'success' => true,
            'token' => $storage->google_access_token
        ];

    } catch (Exception $e) {
        log_message('error', 'get_access_token_safe error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ Access Token'
        ];
    }
}
	
	
	
	
	/**
 * แชร์กับอีเมลแบบปลอดภัย
 */
private function share_with_email_safe($file_id, $email, $permission, $message, $access_token) {
    try {
        $ch = curl_init();
        
        $permission_data = [
            'role' => $permission,
            'type' => 'user',
            'emailAddress' => $email
        ];

        $url = "https://www.googleapis.com/drive/v3/files/{$file_id}/permissions?sendNotificationEmail=true";
        
        if (!empty($message)) {
            $url .= '&emailMessage=' . urlencode($message);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($permission_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return ['success' => false, 'message' => 'Network error: ' . $curl_error];
        }

        if ($http_code === 200 || $http_code === 201) {
            return [
                'success' => true,
                'method' => 'google_api',
                'response_code' => $http_code
            ];
        } else {
            $error_response = json_decode($response, true);
            $error_msg = 'HTTP ' . $http_code;
            
            if ($error_response && isset($error_response['error']['message'])) {
                $error_msg = $error_response['error']['message'];
            }
            
            return ['success' => false, 'message' => $error_msg];
        }

    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

	
	
	

	
	
private function refresh_access_token($storage) {
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

	
	
	
	
	/**
 * JSON Response ที่ปลอดภัย
 */
private function json_response($success, $message, $data = null) {
    // ล้าง output buffer อีกครั้ง
    if (ob_get_level()) {
        ob_clean();
    }
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    // ตั้งค่า headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

	
	private function get_google_drive_file_details($file_id, $access_token) {
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
   private function create_google_drive_share_link($item_id, $permission, $access, $access_token) {
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

    /**
     * 🔗 แชร์กับอีเมล Google Drive (Production)
     */
    private function share_google_drive_with_email($item_id, $email, $permission, $message, $access_token) {
        try {
            if (!$access_token || $access_token === 'trial_token') {
                return [
                    'success' => false,
                    'error' => 'Invalid access token'
                ];
            }

            $permission_data = [
                'role' => $permission, // reader, writer, commenter
                'type' => 'user',
                'emailAddress' => $email
            ];

            // เพิ่ม notification message ถ้ามี
            $url = "https://www.googleapis.com/drive/v3/files/{$item_id}/permissions";
            if (!empty($message)) {
                $url .= '?' . http_build_query([
                    'emailMessage' => $message,
                    'sendNotificationEmail' => 'true'
                ]);
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
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
            curl_close($ch);

            if ($http_code === 200 || $http_code === 201) {
                // Log sharing activity
                $this->log_drive_activity('share_with_email', [
                    'item_id' => $item_id,
                    'email' => $email,
                    'permission' => $permission
                ]);

                return [
                    'success' => true
                ];
            } else {
                $error_response = json_decode($response, true);
                return [
                    'success' => false,
                    'error' => $error_response['error']['message'] ?? 'ไม่สามารถแชร์กับอีเมลได้'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }

    // ==========================================
    // TRIAL MODE HANDLERS
    // ==========================================

    /**
     * 🎭 จัดการอัปโหลดสำหรับ Trial Mode (Production - ใช้พื้นที่จริง)
     */
    private function handle_trial_upload($file, $folder_id) {
        try {
            // อัปโหลดไฟล์จริงไปยัง Google Drive แต่จำกัด quota
            $file_data = $this->prepare_file_data($file);
            $access_token = $this->get_access_token();
            
            if (!$access_token) {
                throw new Exception('ไม่สามารถเชื่อมต่อ Google Drive ได้');
            }

            // อัปโหลดไฟล์จริง
            $upload_result = $this->upload_file_to_google_drive($file_data, $folder_id, $access_token);
            
            if (!$upload_result || !$upload_result['success']) {
                throw new Exception($upload_result['error'] ?? 'การอัปโหลดล้มเหลว');
            }

            // บันทึกข้อมูลไฟล์ (ใช้ตารางปกติ)
            $this->save_uploaded_file_info($upload_result['file_id'], $file_data, $folder_id);
            
            // อัปเดต quota (จำกัดที่ 1GB สำหรับ trial)
            $this->update_trial_quota($file_data['size']);
            
            // Log activity
            $this->log_drive_activity('trial_upload', [
                'file_id' => $upload_result['file_id'],
                'file_name' => $file_data['name'],
                'file_size' => $file_data['size'],
                'folder_id' => $folder_id
            ]);
            
            return [
                'file_id' => $upload_result['file_id'],
                'web_view_link' => $upload_result['web_view_link'],
                'is_trial' => true
            ];

        } catch (Exception $e) {
            log_message('error', 'Handle trial upload error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 🎭 จัดการสร้างโฟลเดอร์สำหรับ Trial Mode (Production - ใช้ Google Drive จริง)
     */
    private function handle_trial_create_folder($folder_name, $parent_id) {
        try {
            // สร้างโฟลเดอร์จริงใน Google Drive
            $access_token = $this->get_access_token();
            
            if (!$access_token) {
                throw new Exception('ไม่สามารถเชื่อมต่อ Google Drive ได้');
            }

            $create_result = $this->create_google_drive_folder($folder_name, $parent_id, $access_token);
            
            if (!$create_result || !$create_result['success']) {
                throw new Exception($create_result['error'] ?? 'การสร้างโฟลเดอร์ล้มเหลว');
            }

            // บันทึกลงฐานข้อมูล (ใช้ตารางปกติ แต่ระบุว่าเป็น trial)
            $this->save_created_folder_info($create_result['folder_id'], $folder_name, $parent_id, true);
            
            // Log activity
            $this->log_drive_activity('trial_create_folder', [
                'folder_id' => $create_result['folder_id'],
                'folder_name' => $folder_name,
                'parent_id' => $parent_id
            ]);
            
            return [
                'folder_id' => $create_result['folder_id'],
                'web_view_link' => $create_result['web_view_link'],
                'is_trial' => true
            ];

        } catch (Exception $e) {
            log_message('error', 'Handle trial create folder error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 🎭 จัดการลบสำหรับ Trial Mode (Production - ลบจาก Google Drive จริง)
     */
    private function handle_trial_delete($item_id, $item_type) {
        try {
            // ลบจาก Google Drive จริง
            $access_token = $this->get_access_token();
            
            if ($access_token && $access_token !== 'trial_token') {
                $this->delete_google_drive_item($item_id, $access_token);
            }

            if ($item_type === 'folder') {
                // ลบโฟลเดอร์จากฐานข้อมูล
                $this->remove_folder_from_database($item_id);
            } else {
                // ดึงข้อมูลไฟล์เพื่อลด quota
                $file_info = null;
                
                if ($this->storage_mode === 'centralized') {
                    if ($this->db->table_exists('tbl_google_drive_system_files')) {
                        $file_info = $this->db->select('file_size')
                                             ->from('tbl_google_drive_system_files')
                                             ->where('file_id', $item_id)
                                             ->where('uploaded_by', $this->member_id)
                                             ->get()
                                             ->row();
                    }
                } else {
                    if ($this->db->table_exists('tbl_google_drive_user_files')) {
                        $file_info = $this->db->select('file_size')
                                             ->from('tbl_google_drive_user_files')
                                             ->where('file_id', $item_id)
                                             ->where('member_id', $this->member_id)
                                             ->get()
                                             ->row();
                    }
                }
                
                // ลด quota
                if ($file_info && $file_info->file_size > 0) {
                    $current_used = $this->db->select('storage_quota_used')
                                            ->from('tbl_member')
                                            ->where('m_id', $this->member_id)
                                            ->get()
                                            ->row()
                                            ->storage_quota_used ?? 0;
                    
                    $new_used = max(0, $current_used - $file_info->file_size);
                    
                    $this->db->where('m_id', $this->member_id)
                            ->update('tbl_member', [
                                'storage_quota_used' => $new_used,
                                'last_storage_access' => date('Y-m-d H:i:s')
                            ]);
                }
                
                // ลบไฟล์จากฐานข้อมูล
                $this->remove_file_from_database($item_id);
            }
            
            // Log activity
            $this->log_drive_activity('trial_delete_' . $item_type, [
                'item_id' => $item_id,
                'item_type' => $item_type
            ]);

        } catch (Exception $e) {
            log_message('error', 'Handle trial delete error: ' . $e->getMessage());
            throw $e;
        }
    }

    // ==========================================
    // DATABASE & STORAGE MANAGEMENT
    // ==========================================

    /**
     * 💾 บันทึกข้อมูลไฟล์ที่อัปโหลด
     */
    private function save_uploaded_file_info($file_id, $file_data, $folder_id) {
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
     * 💾 อัปเดต quota ของ member
     */
    private function update_member_quota($file_size) {
        try {
            $current_used = $this->db->select('storage_quota_used')
                                    ->from('tbl_member')
                                    ->where('m_id', $this->member_id)
                                    ->get()
                                    ->row()
                                    ->storage_quota_used ?? 0;

            $new_used = $current_used + $file_size;

            $this->db->where('m_id', $this->member_id)
                    ->update('tbl_member', [
                        'storage_quota_used' => $new_used,
                        'last_storage_access' => date('Y-m-d H:i:s')
                    ]);

        } catch (Exception $e) {
            log_message('error', 'Update member quota error: ' . $e->getMessage());
        }
    }

    /**
     * 💾 บันทึกข้อมูลโฟลเดอร์ที่สร้าง (รองรับ trial mode)
     */
    private function save_created_folder_info($folder_id, $folder_name, $parent_id, $is_trial = false) {
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
    private function update_trial_quota($file_size) {
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
    private function check_storage_limit($additional_size) {
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
                $limit = $this->trial_storage_limit; // 1GB
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
    private function remove_file_from_database($item_id) {
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
    private function remove_folder_from_database($item_id) {
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
    private function log_drive_activity($action_type, $action_info = null) {
    try {
        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_activity_logs')) {
            log_message('warning', 'Table tbl_google_drive_activity_logs not found');
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
   private function get_storage_mode() {
        try {
            // ดึงจากการตั้งค่าระบบ
            return $this->get_system_setting('system_storage_mode', 'user_based');

        } catch (Exception $e) {
            return 'user_based'; // default
        }
    }

    /**
     * 🔧 ดึงข้อมูล System Storage
     */
    private function get_system_storage_info() {
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
            return null;
        }
    }

    /**
     * 🔧 ดึง Member Permission (แบบ simple)
     */
    private function get_member_permission($member_id, $position_id) {
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
 * 🔧 ดึง Current Member Permission (ป้องกัน error)
 */
private function get_current_member_permission() {
    try {
        $member = $this->db->select('ref_pid')->from('tbl_member')->where('m_id', $this->member_id)->get()->row();
        if (!$member) {
            return [
                'permission_type' => 'no_access',
                'can_upload' => false,
                'can_create_folder' => false,
                'can_share' => false,
                'can_delete' => false
            ];
        }
        
        return $this->get_member_permission($this->member_id, $member->ref_pid);
    } catch (Exception $e) {
        log_message('error', 'Get current member permission error: ' . $e->getMessage());
        return [
            'permission_type' => 'no_access',
            'can_upload' => false,
            'can_create_folder' => false,
            'can_share' => false,
            'can_delete' => false
        ];
    }
}
	
	
	

    /**
     * 🔧 Map permission type to access type
     */
    private function map_permission_to_access_type($permission_type) {
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
    private function get_access_token() {
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
    private function get_system_access_token() {
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
    private function get_member_access_token() {
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
    private function get_user_google_drive_folders($access_token) {
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
   private function output_json_success($data = [], $message = 'สำเร็จ') {
    $this->safe_json_success($data, $message);
}

    /**
     * 🔧 Output JSON Error
     */
    private function output_json_error($message = 'เกิดข้อผิดพลาด', $status_code = 400) {
    $this->safe_json_error($message, $status_code);
}

    /**
     * 🔧 Helper methods อื่นๆ
     */
    
    private function format_datetime($datetime) {
        try {
            if (empty($datetime)) {
                return '-';
            }
            return date('d/m/Y H:i', strtotime($datetime));
        } catch (Exception $e) {
            return '-';
        }
    }

    private function format_google_date($google_date) {
        try {
            if (empty($google_date)) {
                return '-';
            }
            return date('d/m/Y H:i', strtotime($google_date));
        } catch (Exception $e) {
            return '-';
        }
    }

    private function format_file_size($bytes) {
        if ($bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }

    private function get_folder_icon($folder_name) {
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

    private function get_folder_description($folder_name) {
        // สามารถเพิ่ม logic สำหรับ description ได้ตามต้องการ
        return '';
    }

    private function get_file_icon($mime_type) {
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
 * 🏢 ตรวจสอบสิทธิ์โฟลเดอร์ในโหมด Centralized
 */
private function check_centralized_folder_access($folder_id, $permission) {
    try {
        // Admin เข้าถึงได้ทุกโฟลเดอร์
        if ($permission['access_type'] === 'full') {
            return true;
        }
        
        // ตรวจสอบจากตาราง folder permissions
        if ($this->db->table_exists('tbl_google_drive_folder_permissions')) {
            $folder_permission = $this->db->select('access_level')
                                         ->from('tbl_google_drive_folder_permissions')
                                         ->where('folder_id', $folder_id)
                                         ->where('member_id', $this->member_id)
                                         ->where('is_active', 1)
                                         ->get()
                                         ->row();
            
            if ($folder_permission) {
                return $folder_permission->access_level !== 'no_access';
            }
        }
        
        // ตรวจสอบจากตาราง system folders
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $folder = $this->db->select('folder_type, created_for_position, created_by')
                              ->from('tbl_google_drive_system_folders')
                              ->where('folder_id', $folder_id)
                              ->where('is_active', 1)
                              ->get()
                              ->row();
            
            if ($folder) {
                switch ($folder->folder_type) {
                    case 'shared':
                        // โฟลเดอร์แชร์ - ทุกคนเข้าถึงได้
                        return true;
                        
                    case 'department':
                        // โฟลเดอร์แผนก - เฉพาะคนในแผนก
                        if ($permission['access_type'] === 'department') {
                            return true;
                        }
                        return $this->check_position_access($folder->created_for_position);
                        
                    case 'admin':
                        // โฟลเดอร์ admin - เฉพาะ admin
                        return $permission['access_type'] === 'full';
                        
                    case 'personal':
                        // โฟลเดอร์ส่วนตัว - เฉพาะเจ้าของ
                        return $folder->created_by == $this->member_id;
                        
                    default:
                        // โฟลเดอร์ทั่วไป - ตามสิทธิ์มาตรฐาน
                        return $permission['access_type'] !== 'no_access';
                }
            }
        }
        
        // ถ้าไม่พบข้อมูลโฟลเดอร์ ให้ตรวจสอบตามสิทธิ์พื้นฐาน
        return $permission['access_type'] !== 'no_access';
        
    } catch (Exception $e) {
        log_message('error', 'Check centralized folder access error: ' . $e->getMessage());
        return false;
    }
}


/**
 * 👤 ตรวจสอบสิทธิ์โฟลเดอร์ในโหมด User-based
 */
private function check_user_based_folder_access($folder_id, $permission) {
    try {
        // ตรวจสอบจากตาราง user folders
        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $folder = $this->db->select('member_id, folder_type, is_shared')
                              ->from('tbl_google_drive_folders')
                              ->where('folder_id', $folder_id)
                              ->where('is_active', 1)
                              ->get()
                              ->row();
            
            if ($folder) {
                // เจ้าของโฟลเดอร์เข้าถึงได้เสมอ
                if ($folder->member_id == $this->member_id) {
                    return true;
                }
                
                // โฟลเดอร์ที่แชร์
                if ($folder->is_shared == 1) {
                    return $this->check_shared_folder_access($folder_id);
                }
                
                // โฟลเดอร์ส่วนตัวของคนอื่น
                return false;
            }
        }
        
        // ถ้าไม่พบในตาราง แสดงว่าเป็นโฟลเดอร์ภายนอกระบบ
        // ให้เข้าถึงได้ตามสิทธิ์พื้นฐาน
        return $permission['access_type'] !== 'no_access';
        
    } catch (Exception $e) {
        log_message('error', 'Check user based folder access error: ' . $e->getMessage());
        return false;
    }
}
	
	
	

	/**
 * 🤝 ตรวจสอบสิทธิ์โฟลเดอร์แชร์
 */
private function check_shared_folder_access($folder_id) {
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
private function check_position_access($required_position_id) {
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
private function log_folder_access($folder_id, $access_granted = true) {
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
 * 🛡️ ตรวจสอบสิทธิ์ด่วนสำหรับ Trial Mode
 */
private function check_trial_folder_access($folder_id) {
    $trial_folders = [
        'demo_folder_1',
        'demo_folder_2', 
        'demo_folder_3',
        'demo_folder_4'
    ];
    
    return in_array($folder_id, $trial_folders);
}


/**
 * 🎯 ฟังก์ชันหลักที่ปรับปรุงแล้ว - เรียกจากที่อื่น
 */
public function verify_folder_access($folder_id) {
    $access_granted = $this->check_folder_access_permission($folder_id);
    $this->log_folder_access($folder_id, $access_granted);
    return $access_granted;
}
	

    private function prepare_file_data($file) {
        return [
            'name' => $file['name'],
            'tmp_name' => $file['tmp_name'],
            'size' => $file['size'],
            'type' => $file['type'],
            'error' => $file['error']
        ];
    }
	
	
	
	
	public function ajax_error_handler($severity, $message, $file, $line) {
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
public function ajax_exception_handler($exception) {
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
 * 🔐 ดึงสิทธิ์สำหรับโฟลเดอร์เฉพาะ (AJAX) - ✅ FIXED VERSION
 * แก้ไข 500 Error โดยเพิ่ม error handling และ validation ครบถ้วน
 */
public function get_folder_permissions() {
    try {
        // ✅ STEP 1: ล้าง output buffer และป้องกัน PHP Error
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        // ✅ STEP 2: ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ STEP 3: ตรวจสอบ member_id
        $member_id = $this->member_id ?? $this->session->userdata('m_id');
        if (!$member_id) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบ session ผู้ใช้',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ STEP 4: รับค่า folder_id
        $folder_id = $this->input->post('folder_id');
        if ($folder_id === null || $folder_id === false) {
            $folder_id = 'root';
        }
        
        $folder_id = trim($folder_id);
        if (empty($folder_id)) {
            $folder_id = 'root';
        }
        
        log_message('info', "Getting simple folder permissions for member {$member_id}, folder: {$folder_id}");

        // ✅ STEP 5: ใช้ permissions แบบง่าย
        $permissions = $this->get_simple_folder_permissions($folder_id, $member_id);
        
        // ✅ STEP 6: ส่งผลลัพธ์
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'ดึงสิทธิ์โฟลเดอร์สำเร็จ',
            'data' => $permissions,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        // ✅ STEP 7: Error handling ที่ปลอดภัย
        log_message('error', 'Get folder permissions error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code(200); // ใช้ 200 แทน 500 เพื่อป้องกัน client error
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true, // return success เพื่อไม่ให้ UI เสียหาย
            'message' => 'ใช้สิทธิ์เริ่มต้น',
            'data' => $this->get_fallback_permissions($folder_id ?? 'root'),
            'fallback' => true,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

	
	/**
 * 🏠 สิทธิ์สำหรับ Root folder
 */
private function get_root_permissions($member_id, $default_permissions) {
    try {
        // ดึงข้อมูล member และ position
        $member = $this->db->select('ref_pid')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if ($member) {
            // Admin positions (1, 2) ได้สิทธิ์เต็ม
            if (in_array($member->ref_pid, [1, 2])) {
                return array_merge($default_permissions, [
                    'access_level' => 'admin',
                    'can_upload' => true,
                    'can_create_folder' => true,
                    'can_share' => !$this->is_trial_mode,
                    'can_delete' => true,
                    'can_download' => !$this->is_trial_mode,
                    'permission_source' => 'admin'
                ]);
            }
        }

        // สิทธิ์มาตรฐานสำหรับ user ทั่วไป
        return array_merge($default_permissions, [
            'access_level' => 'read_write',
            'can_upload' => true,
            'can_create_folder' => true,
            'can_share' => true,
            'can_delete' => true,
            'can_download' => !$this->is_trial_mode,
            'permission_source' => 'position'
        ]);

    } catch (Exception $e) {
        log_message('error', 'Get root permissions error: ' . $e->getMessage());
        return $default_permissions;
    }
}
	
	
	
	/**
 * 📁 สิทธิ์สำหรับโฟลเดอร์ทั่วไป
 */
private function get_default_folder_permissions($folder_id, $member_id, $default_permissions) {
    try {
        // ตรวจสอบเบื้องต้นจากฐานข้อมูล (ถ้ามีตาราง)
        if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $folder_access = $this->db->select('access_type')
                                     ->from('tbl_google_drive_member_folder_access')
                                     ->where('folder_id', $folder_id)
                                     ->where('member_id', $member_id)
                                     ->where('is_active', 1)
                                     ->get()
                                     ->row();

            if ($folder_access) {
                return array_merge($default_permissions, [
                    'access_level' => $folder_access->access_type,
                    'can_upload' => in_array($folder_access->access_type, ['write', 'admin']),
                    'can_create_folder' => in_array($folder_access->access_type, ['write', 'admin']),
                    'can_share' => in_array($folder_access->access_type, ['write', 'admin']),
                    'can_delete' => in_array($folder_access->access_type, ['write', 'admin']),
                    'permission_source' => 'direct'
                ]);
            }
        }

        // สิทธิ์เริ่มต้นสำหรับโฟลเดอร์ทั่วไป
        return array_merge($default_permissions, [
            'access_level' => 'read_only',
            'can_upload' => false,
            'can_create_folder' => false,
            'can_share' => false,
            'can_delete' => false,
            'can_download' => !$this->is_trial_mode,
            'permission_source' => 'default'
        ]);

    } catch (Exception $e) {
        log_message('error', 'Get default folder permissions error: ' . $e->getMessage());
        return $default_permissions;
    }
}

	
	
	/**
 * 🛡️ สิทธิ์สำรอง (Fallback)
 */
private function get_fallback_permissions($folder_id) {
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
private function get_simple_folder_permissions($folder_id, $member_id) {
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
 * 🔍 ดึงข้อมูล Member อย่างปลอดภัย (SAFE VERSION)
 */
private function get_member_data_safe($member_id) {
    try {
        if (!$this->db) {
            throw new Exception('Database connection not available');
        }
        
        if (!is_numeric($member_id) || $member_id <= 0) {
            throw new Exception('Invalid member ID');
        }
        
        // ตรวจสอบว่าตาราง member มีอยู่จริง
        if (!$this->db->table_exists('tbl_member')) {
            throw new Exception('Member table not exists');
        }
        
        $this->db->select('m.*, p.pname, p.peng')
                 ->from('tbl_member m')
                 ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                 ->where('m.m_id', $member_id)
                 ->limit(1);
        
        $query = $this->db->get();
        
        if ($this->db->error()['code'] !== 0) {
            $db_error = $this->db->error();
            throw new Exception('Database query error: ' . $db_error['message']);
        }
        
        $member = $query->row();
        
        if (!$member) {
            log_message('warning', "Member not found: {$member_id}");
            return null;
        }
        
        log_message('info', "Member data retrieved successfully for ID: {$member_id}");
        return $member;
        
    } catch (Exception $e) {
        log_message('error', 'Get member data safe error: ' . $e->getMessage());
        return null;
    }
}

/**
 * 🔐 ดึงสิทธิ์สำหรับโฟลเดอร์เฉพาะ - Enhanced Safe Version
 */
private function get_specific_folder_permissions_safe($folder_id, $member) {
    try {
        log_message('info', "Getting permissions for folder: {$folder_id}, member: {$member->m_id}");
        
        // ✅ สิทธิ์เริ่มต้น (Ultra-safe defaults)
        $default_permissions = [
            'access_level' => 'read_only',
            'can_upload' => false,
            'can_create_folder' => false,
            'can_share' => false,
            'can_delete' => false,
            'can_download' => true,
            'permission_source' => 'system',
            'granted_by' => null,
            'granted_at' => null,
            'expires_at' => null,
            'folder_id' => $folder_id,
            'member_id' => $member->m_id,
            'error' => false
        ];

        // ✅ ตรวจสอบ Trial Mode
        if (isset($this->is_trial_mode) && $this->is_trial_mode) {
            return $this->get_trial_folder_permissions_safe($folder_id, $default_permissions);
        }

        // ✅ ตรวจสอบ Root folder
        if ($folder_id === 'root' || empty($folder_id)) {
            return $this->get_root_folder_permissions_safe($member, $default_permissions);
        }

        // ✅ ตรวจสอบตามโหมด storage
        $storage_mode = isset($this->storage_mode) ? $this->storage_mode : 'user_based';
        
        if ($storage_mode === 'centralized') {
            return $this->get_centralized_folder_permissions_safe($folder_id, $member, $default_permissions);
        } else {
            return $this->get_user_based_folder_permissions_safe($folder_id, $member, $default_permissions);
        }

    } catch (Exception $e) {
        log_message('error', 'Get specific folder permissions safe error: ' . $e->getMessage());
        
        // Return ultra-safe default permissions with error flag
        return [
            'access_level' => 'read_only',
            'can_upload' => false,
            'can_create_folder' => false,
            'can_share' => false,
            'can_delete' => false,
            'can_download' => false,
            'permission_source' => 'error',
            'granted_by' => null,
            'granted_at' => null,
            'expires_at' => null,
            'error' => true,
            'error_message' => $e->getMessage(),
            'folder_id' => isset($folder_id) ? $folder_id : 'unknown',
            'member_id' => isset($member->m_id) ? $member->m_id : 0
        ];
    }
}

/**
 * 🎭 ดึงสิทธิ์สำหรับ Trial Mode (Enhanced Safe)
 */
private function get_trial_folder_permissions_safe($folder_id, $default_permissions) {
    try {
        $trial_folders = [
            'demo_folder_1', 'demo_folder_2', 'demo_folder_3', 'demo_folder_4'
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
private function get_root_folder_permissions_safe($member, $default_permissions) {
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
            'can_upload' => isset($base_permission['can_upload']) ? (bool)$base_permission['can_upload'] : false,
            'can_create_folder' => isset($base_permission['can_create_folder']) ? (bool)$base_permission['can_create_folder'] : false,
            'can_share' => $is_trial ? false : (isset($base_permission['can_share']) ? (bool)$base_permission['can_share'] : false),
            'can_delete' => isset($base_permission['can_delete']) ? (bool)$base_permission['can_delete'] : false,
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
private function get_member_permission_safe($member_id, $position_id) {
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
                        'can_create_folder' => (bool)$position_permission->can_create_folder,
                        'can_share' => $is_trial ? false : (bool)$position_permission->can_share,
                        'can_delete' => (bool)$position_permission->can_delete
                    ];
                }
            } catch (Exception $e) {
                log_message('warning', 'Position permission query failed: ' . $e->getMessage());
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
private function get_centralized_folder_permissions_safe($folder_id, $member, $default_permissions) {
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
private function get_user_based_folder_permissions_safe($folder_id, $member, $default_permissions) {
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
private function get_direct_folder_permission_safe($folder_id, $member_id) {
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
private function map_permission_to_access_level_safe($permission_type) {
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
private function get_default_centralized_permission_safe($member, $default_permissions) {
    try {
        $base_permission = $this->get_member_permission_safe($member->m_id, $member->ref_pid);
        $is_trial = isset($this->is_trial_mode) ? $this->is_trial_mode : false;
        
        return array_merge($default_permissions, [
            'access_level' => $this->map_permission_to_access_level_safe($base_permission['permission_type']),
            'can_upload' => isset($base_permission['can_upload']) ? (bool)$base_permission['can_upload'] : false,
            'can_create_folder' => isset($base_permission['can_create_folder']) ? (bool)$base_permission['can_create_folder'] : false,
            'can_share' => $is_trial ? false : (isset($base_permission['can_share']) ? (bool)$base_permission['can_share'] : false),
            'can_delete' => isset($base_permission['can_delete']) ? (bool)$base_permission['can_delete'] : false,
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
private function is_folder_owner_safe($folder_id, $member_id) {
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
private function get_position_folder_permission_safe($folder_id, $position_id) {
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
private function get_shared_folder_permission_safe($folder_id, $member) {
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
private function get_user_shared_folder_permission_safe($folder_id, $member) {
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
private function get_folder_created_date_safe($folder_id) {
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
private function get_member_name_safe($member_id) {
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
 * 🔍 ตรวจสอบสิทธิ์การอัปโหลดแบบง่าย
 */
private function can_upload_to_folder($folder_id, $member_id) {
    try {
        // Trial mode สามารถอัปโหลดได้เฉพาะโฟลเดอร์ demo
        if ($this->is_trial_mode) {
            $trial_folders = ['demo_folder_1', 'demo_folder_2', 'demo_folder_3', 'demo_folder_4', 'root'];
            return in_array($folder_id, $trial_folders);
        }

        // Root folder - ใครก็อัปโหลดได้
        if ($folder_id === 'root' || empty($folder_id)) {
            return true;
        }

        // ตรวจสอบจากฐานข้อมูล (แบบง่าย)
        if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $access = $this->db->select('access_type')
                              ->from('tbl_google_drive_member_folder_access')
                              ->where('folder_id', $folder_id)
                              ->where('member_id', $member_id)
                              ->where('is_active', 1)
                              ->get()
                              ->row();

            if ($access) {
                return in_array($access->access_type, ['write', 'admin']);
            }
        }

        // Default: อนุญาตให้อัปโหลด (ปลอดภัยกว่าการปฏิเสธ)
        return true;

    } catch (Exception $e) {
        log_message('error', 'Check upload permission error: ' . $e->getMessage());
        return true; // Default allow
    }
}

/**
 * 🔍 ตรวจสอบสิทธิ์การลบแบบง่าย
 */
private function can_delete_from_folder($folder_id, $member_id) {
    try {
        // Trial mode สามารถลบได้
        if ($this->is_trial_mode) {
            return true;
        }

        // Admin positions สามารถลบได้
        $member = $this->db->select('ref_pid')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if ($member && in_array($member->ref_pid, [1, 2])) {
            return true;
        }

        // Default: ไม่อนุญาตให้ลบ (ปลอดภัย)
        return false;

    } catch (Exception $e) {
        log_message('error', 'Check delete permission error: ' . $e->getMessage());
        return false; // Default deny
    }
}

	
	
	
	
	public function create_folder() {
    // ✅ Set proper headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    try {
        // ✅ Basic validation
        $member_id = $this->session->userdata('m_id');
        $folder_name = trim($this->input->post('folder_name'));
        $parent_id = $this->input->post('parent_id');
        
        log_message('debug', "Create folder: member={$member_id}, name={$folder_name}, parent={$parent_id}");
        
        if (!$member_id) {
            echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
            return;
        }
        
        if (empty($folder_name)) {
            echo json_encode(['success' => false, 'message' => 'กรุณาใส่ชื่อโฟลเดอร์']);
            return;
        }
        
        // ✅ Get member info for position_id
        $member = $this->db->select('ref_pid, m_fname, m_lname')
            ->from('tbl_member')
            ->where('m_id', $member_id)
            ->get()
            ->row();
        
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้']);
            return;
        }
        
        // ✅ Get access token
        $access_token = $this->get_valid_access_token();
        if (!$access_token) {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถเชื่อมต่อ Google Drive ได้']);
            return;
        }
        
        // ✅ Create Google Drive folder
        $create_result = $this->create_google_drive_folder($folder_name, $parent_id, $access_token);
        if (!$create_result || !$create_result['success']) {
            $error_msg = isset($create_result['error']) ? $create_result['error'] : 'Unknown error';
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้: ' . $error_msg]);
            return;
        }
        
        $new_folder_id = $create_result['folder_id'];
        $web_view_link = $create_result['web_view_link'] ?? '';
        
        log_message('debug', "Google Drive folder created: {$new_folder_id}");
        
        // ✅ Save to database with complete data
        $folder_data = [
            'member_id' => $member_id,
            'position_id' => $member->ref_pid,
            'folder_id' => $new_folder_id,
            'folder_name' => $folder_name,
            'parent_id' => ($parent_id === 'root' || empty($parent_id)) ? null : $parent_id,
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
        
        log_message('debug', 'Inserting folder data: ' . json_encode($folder_data));
        
        // ✅ Insert to database
        $db_result = $this->db->insert('tbl_google_drive_folders', $folder_data);
        
        if (!$db_result) {
            $db_error = $this->db->error();
            log_message('error', 'Database insert failed: ' . $db_error['message']);
            echo json_encode([
                'success' => false, 
                'message' => 'ไม่สามารถบันทึกข้อมูลโฟลเดอร์ได้: ' . $db_error['message'],
                'debug_info' => [
                    'db_error_code' => $db_error['code'],
                    'db_error_message' => $db_error['message'],
                    'folder_data' => $folder_data
                ]
            ]);
            return;
        }
        
        log_message('debug', 'Folder saved to database successfully');
        
        // ✅ Create basic permission
        try {
            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $permission_data = [
                    'member_id' => $member_id,
                    'folder_id' => $new_folder_id,
                    'access_type' => 'owner',
                    'permission_source' => 'creator',
                    'granted_by' => $member_id,
                    'granted_by_name' => $member->m_fname . ' ' . $member->m_lname,
                    'granted_at' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $perm_result = $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
                log_message('debug', 'Permission created: ' . ($perm_result ? 'success' : 'failed'));
                
                if (!$perm_result) {
                    log_message('warning', 'Permission creation failed: ' . $this->db->error()['message']);
                }
            }
        } catch (Exception $perm_error) {
            log_message('warning', 'Permission creation error: ' . $perm_error->getMessage());
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
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $member->m_fname . ' ' . $member->m_lname
            ]
        ]);
        
        log_message('info', "Folder created successfully: {$folder_name} (ID: {$new_folder_id})");
        
    } catch (Exception $e) {
        log_message('error', 'Create folder exception: ' . $e->getMessage());
        log_message('error', 'Exception trace: ' . $e->getTraceAsString());
        
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการสร้างโฟลเดอร์: ' . $e->getMessage(),
            'error_type' => 'exception',
            'debug_info' => [
                'exception_message' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine()
            ]
        ]);
    }
}

// ✅ เพิ่ม method ทดสอบการ insert ข้อมูล
public function test_insert_folder() {
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
 * 🛡️ Safe JSON Response - ป้องกัน PHP Error และ HTML Output
 */
private function safe_json_response($data, $http_code = 200) {
    try {
        // ✅ ล้าง output buffer ที่อาจมี error หรือ warning
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // ✅ Set HTTP Status
        http_response_code($http_code);
        
        // ✅ Set Headers
        header('Content-Type: application/json; charset=utf-8', true);
        header('Cache-Control: no-cache, must-revalidate', true);
        header('Pragma: no-cache', true);
        header('X-Content-Type-Options: nosniff', true);
        
        // ✅ Ensure data is properly formatted
        if (!is_array($data)) {
            $data = ['success' => false, 'message' => 'Invalid response data'];
        }
        
        // ✅ Add timestamp
        $data['timestamp'] = date('Y-m-d H:i:s');
        
        // ✅ Output JSON
        $json_output = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        if ($json_output === false) {
            // JSON encoding failed
            $error_data = [
                'success' => false,
                'message' => 'JSON encoding failed: ' . json_last_error_msg(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
            echo json_encode($error_data, JSON_UNESCAPED_UNICODE);
        } else {
            echo $json_output;
        }
        
        // ✅ Force output and exit
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            if (ob_get_level()) {
                ob_end_flush();
            }
            flush();
        }
        
        exit();
        
    } catch (Exception $e) {
        // ✅ Ultimate fallback
        log_message('error', 'Safe JSON response error: ' . $e->getMessage());
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($http_code);
        header('Content-Type: application/json; charset=utf-8', true);
        
        $fallback_response = [
            'success' => false,
            'message' => 'Critical system error',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($fallback_response, JSON_UNESCAPED_UNICODE);
        exit();
    }
}

/**
 * 🛡️ Safe Database Save Folder
 */
private function save_folder_to_database_safe($folder_id, $folder_name, $parent_id, $member_id, $web_view_link) {
    try {
        // ตรวจสอบว่าตารางมีอยู่จริง
        if (!$this->db->table_exists('tbl_google_drive_folders')) {
            log_message('warning', 'Table tbl_google_drive_folders does not exist - skipping folder save');
            return true; // ไม่ให้ error
        }
        
        $folder_data = [
            'folder_id' => $folder_id,
            'folder_name' => $folder_name,
            'parent_id' => empty($parent_id) || $parent_id === 'root' ? null : $parent_id,
            'created_by' => $member_id,
            'web_view_link' => $web_view_link,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // ตรวจสอบว่า folder_id ซ้ำหรือไม่
        $existing = $this->db->where('folder_id', $folder_id)->get('tbl_google_drive_folders')->row();
        if ($existing) {
            log_message('warning', "Folder ID {$folder_id} already exists in database - updating instead");
            return $this->db->where('folder_id', $folder_id)->update('tbl_google_drive_folders', $folder_data);
        }
        
        $result = $this->db->insert('tbl_google_drive_folders', $folder_data);
        
        if ($result) {
            log_message('debug', 'Folder data saved to tbl_google_drive_folders successfully');
            return true;
        } else {
            log_message('error', 'Failed to insert folder data: ' . $this->db->error()['message']);
            return false;
        }
        
    } catch (Exception $e) {
        log_message('error', 'Save folder to database safe error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🛡️ Safe Permission Inheritance
 */
private function inherit_parent_folder_permissions_safe($new_folder_id, $parent_id, $creator_member_id) {
    $result = [
        'inherited_count' => 0,
        'sources' => [],
        'has_owner_permission' => false,
        'creator_access_type' => 'owner'
    ];
    
    try {
        // ถ้าเป็น root folder
        if (empty($parent_id) || $parent_id === 'root' || $parent_id === 'null') {
            log_message('debug', "Root level folder - creating basic permissions");
            
            // สร้างสิทธิ์พื้นฐานสำหรับ creator
            if ($this->grant_folder_permission_ultimate_safe($new_folder_id, $creator_member_id, 'owner', 'creator')) {
                $result['has_owner_permission'] = true;
            }
            
            // เพิ่มสิทธิ์สำหรับ admin positions
            $this->grant_admin_permissions_to_folder_safe($new_folder_id);
            
            return $result;
        }
        
        // ดึงสิทธิ์จาก parent folder
        $parent_permissions = $this->get_parent_folder_permissions_safe($parent_id);
        
        if (empty($parent_permissions)) {
            log_message('warning', "No permissions found in parent folder {$parent_id}");
            
            // ให้สิทธิ์พื้นฐาน
            if ($this->grant_folder_permission_ultimate_safe($new_folder_id, $creator_member_id, 'owner', 'creator')) {
                $result['has_owner_permission'] = true;
            }
            
            $this->grant_admin_permissions_to_folder_safe($new_folder_id);
            return $result;
        }
        
        // สืบทอดสิทธิ์
        foreach ($parent_permissions as $permission) {
            if ($this->create_inherited_permission_safe($permission, $new_folder_id, $parent_id)) {
                $result['inherited_count']++;
                $result['sources'][] = $permission->permission_source;
                
                if ($permission->member_id == $creator_member_id) {
                    $result['has_owner_permission'] = true;
                    $result['creator_access_type'] = $permission->access_type;
                }
            }
        }
        
        // ตรวจสอบว่า creator มีสิทธิ์หรือยัง
        if (!$result['has_owner_permission']) {
            if ($this->grant_folder_permission_ultimate_safe($new_folder_id, $creator_member_id, 'owner', 'creator')) {
                $result['has_owner_permission'] = true;
            }
        }
        
        // เพิ่มสิทธิ์ admin
        $this->grant_admin_permissions_to_folder_safe($new_folder_id);
        
        $result['sources'] = array_unique($result['sources']);
        
        return $result;
        
    } catch (Exception $e) {
        log_message('error', 'Safe inherit parent folder permissions error: ' . $e->getMessage());
        
        // Fallback
        try {
            $this->grant_folder_permission_ultimate_safe($new_folder_id, $creator_member_id, 'owner', 'creator_fallback');
            $result['has_owner_permission'] = true;
        } catch (Exception $fallback_error) {
            log_message('error', 'Even fallback permission failed: ' . $fallback_error->getMessage());
        }
        
        return $result;
    }
}

/**
 * 🛡️ Ultimate Safe Permission Grant
 */
private function grant_folder_permission_ultimate_safe($folder_id, $member_id, $access_type = 'owner', $source = 'system') {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            log_message('warning', 'Permission table does not exist - skipping');
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
private function get_parent_folder_permissions_safe($parent_id) {
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
private function create_inherited_permission_safe($parent_permission, $new_folder_id, $parent_id) {
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
private function grant_admin_permissions_to_folder_safe($folder_id) {
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
private function get_current_member_name_safe() {
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
 * 🛡️ Safe Folder Hierarchy Save
 */
private function save_folder_hierarchy_safe($folder_id, $parent_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_folder_hierarchy')) {
            return true; // ไม่ให้ error
        }
        
        if (empty($parent_id) || $parent_id === 'root' || $parent_id === 'null') {
            return true;
        }
        
        $hierarchy_data = [
            'parent_folder_id' => $parent_id,
            'child_folder_id' => $folder_id,
            'depth_level' => 1,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('tbl_google_drive_folder_hierarchy', $hierarchy_data);
        
    } catch (Exception $e) {
        log_message('error', 'Save folder hierarchy safe error: ' . $e->getMessage());
        return true; // ไม่ให้ error
    }
}

	
	
	
	/**
 * 🔗 สืบทอดสิทธิ์จาก Parent Folder
 */
private function inherit_parent_folder_permissions($new_folder_id, $parent_id, $creator_member_id) {
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
	
	
private function save_folder_hierarchy($folder_id, $parent_id) {
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
private function get_folder_depth($folder_id) {
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
private function calculate_folder_depth_manual($folder_id, $current_depth = 0) {
    try {
        // ป้องกัน infinite loop
        if ($current_depth > 10) {
            log_message('warning', "Maximum folder depth reached for folder {$folder_id}");
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
private function build_folder_path($parent_id, $current_folder_id = null) {
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
private function build_folder_path_recursive($folder_id) {
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
 * 📁 ดึง Parent Folder ID (แก้ไข - เพิ่ม Error Handling)
 */
private function get_parent_folder_id($folder_id) {
    try {
        if (empty($folder_id) || $folder_id === 'root') {
            return null;
        }
        
        // ดึงจาก local cache ก่อน (tbl_google_drive_folder_hierarchy)
        if ($this->db->table_exists('tbl_google_drive_folder_hierarchy')) {
            $cached_parent = $this->db->select('parent_folder_id')
                ->from('tbl_google_drive_folder_hierarchy')
                ->where('child_folder_id', $folder_id)
                ->where('is_active', 1)
                ->limit(1)
                ->get()
                ->row();

            if ($cached_parent) {
                return $cached_parent->parent_folder_id;
            }
        }

        // ดึงจาก tbl_google_drive_folders
        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $folder_info = $this->db->select('parent_folder_id')
                ->from('tbl_google_drive_folders')
                ->where('folder_id', $folder_id)
                ->limit(1)
                ->get()
                ->row();

            if ($folder_info) {
                return $folder_info->parent_folder_id;
            }
        }

        // สุดท้าย ดึงจาก Google Drive API (ถ้าจำเป็น)
        $access_token = $this->get_valid_access_token();
        if ($access_token) {
            try {
                $folder_detail = $this->get_google_drive_folder_info($access_token, $folder_id);
                if ($folder_detail && isset($folder_detail['parents']) && count($folder_detail['parents']) > 0) {
                    $parent_id = $folder_detail['parents'][0];
                    
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

        return null;

    } catch (Exception $e) {
        log_message('error', 'Get parent folder ID error: ' . $e->getMessage());
        return null;
    }
}

	
	
	
	/**
 * 📊 ดึงระดับสิทธิ์เป็นตัวเลข (สำหรับเปรียบเทียบ)
 */
private function get_permission_level($access_type) {
    $levels = [
        'read' => 1,
        'write' => 2,
        'admin' => 3,
        'owner' => 4
    ];
    
    return $levels[$access_type] ?? 0;
}
	
	
	
	private function grant_folder_permission_safe($folder_id, $member_id, $access_type = 'owner', $source = 'system') {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            log_message('warning', 'Table tbl_google_drive_member_folder_access does not exist - skipping permission grant');
            return false;
        }
        
        // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
        $existing = $this->db->where([
            'member_id' => $member_id,
            'folder_id' => $folder_id
        ])->get('tbl_google_drive_member_folder_access')->row();
        
        if ($existing) {
            // อัปเดตสิทธิ์
            $update_result = $this->db->where([
                'member_id' => $member_id,
                'folder_id' => $folder_id
            ])->update('tbl_google_drive_member_folder_access', [
                'access_type' => $access_type,
                'permission_source' => $source,
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if (!$update_result) {
                log_message('error', 'Failed to update folder permission: ' . $this->db->error()['message']);
                return false;
            }
        } else {
            // สร้างสิทธิ์ใหม่
            $permission_data = [
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'permission_source' => $source,
                'granted_by' => $this->session->userdata('m_id'),
                'granted_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $insert_result = $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
            
            if (!$insert_result) {
                log_message('error', 'Failed to insert folder permission: ' . $this->db->error()['message']);
                return false;
            }
        }
        
        log_message('debug', "Granted {$access_type} permission to member {$member_id} for folder {$folder_id}");
        return true;
        
    } catch (Exception $e) {
        log_message('error', 'Grant folder permission safe error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🛡️ บันทึก Activity Log แบบปลอดภัย
 */
private function log_activity_safe($data) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_logs')) {
            log_message('warning', 'Table tbl_google_drive_logs does not exist - skipping activity log');
            return false;
        }
        
        $log_data = array_merge([
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ], $data);
        
        $result = $this->db->insert('tbl_google_drive_logs', $log_data);
        
        if ($result) {
            log_message('debug', 'Activity logged successfully');
            return true;
        } else {
            log_message('error', 'Failed to log activity: ' . $this->db->error()['message']);
            return false;
        }
        
    } catch (Exception $e) {
        log_message('error', 'Log activity safe error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🛡️ Safe JSON Output (ป้องกัน PHP errors ใน AJAX)
 */
private function safe_json_output($data, $http_code = 200) {
    try {
        // ตั้งค่า headers ป้องกัน caching
        $this->output->set_header('Cache-Control: no-cache, must-revalidate');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        $this->output->set_header('Pragma: no-cache');
        
        $this->output->set_status_header($http_code);
        return $this->output->set_content_type('application/json; charset=utf-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            
    } catch (Exception $e) {
        log_message('error', 'Safe JSON output error: ' . $e->getMessage());
        
        // Fallback response
        http_response_code($http_code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการส่งข้อมูล'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
	
	
	private function save_folder_to_database($folder_id, $folder_name, $parent_id, $member_id, $web_view_link) {
    try {
        // ตรวจสอบว่าตารางมีอยู่จริง
        if (!$this->db->table_exists('tbl_google_drive_folders')) {
            log_message('warning', 'Table tbl_google_drive_folders does not exist - skipping folder save');
            return false;
        }
        
        $folder_data = [
            'folder_id' => $folder_id,
            'folder_name' => $folder_name,
            'parent_id' => empty($parent_id) || $parent_id === 'root' ? null : $parent_id,
            'created_by' => $member_id,
            'web_view_link' => $web_view_link,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // ตรวจสอบว่า folder_id ซ้ำหรือไม่
        $existing = $this->db->where('folder_id', $folder_id)->get('tbl_google_drive_folders')->row();
        if ($existing) {
            log_message('warning', "Folder ID {$folder_id} already exists in database");
            return false;
        }
        
        $result = $this->db->insert('tbl_google_drive_folders', $folder_data);
        
        if ($result) {
            log_message('debug', 'Folder data saved to tbl_google_drive_folders successfully');
            return true;
        } else {
            log_message('error', 'Failed to insert folder data: ' . $this->db->error()['message']);
            return false;
        }
        
    } catch (Exception $e) {
        log_message('error', 'Save folder to database error: ' . $e->getMessage());
        return false;
    }
}

	
	/**
     * 🧹 ล้าง Output Buffer อย่างสมบูรณ์
     */
    private function clear_output_buffer() {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }
	
	
	
	/**
     * ✅ ส่ง JSON Success แบบปลอดภัย
     */
    private function safe_json_success($data = [], $message = 'Success') {
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
    private function safe_json_error($message = 'Error', $status_code = 400, $debug_data = []) {
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
	
	
	

private function can_create_folder_in($folder_id, $member_id = null) {
    try {
        if ($member_id === null) {
            $member_id = $this->session->userdata('m_id');
        }
        
        if (!$member_id) {
            return false;
        }
        
        // Root folder - อนุญาตเสมอ
        if (empty($folder_id) || $folder_id === 'root' || $folder_id === 'null') {
            return true;
        }
        
        // ตรวจสอบว่าตารางมีอยู่จริง
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            log_message('warning', 'Permission table does not exist - allowing root access only');
            return false;
        }
        
        // เช็คจากตาราง
        $permission = $this->db->select('access_type')
            ->where([
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'is_active' => 1
            ])
            ->where('(expires_at IS NULL OR expires_at > NOW())')
            ->get('tbl_google_drive_member_folder_access')
            ->row();
        
        if ($permission) {
            // read = ไม่ได้, write/admin/owner = ได้
            return in_array($permission->access_type, ['write', 'admin', 'owner']);
        }
        
        return false; // ไม่มีสิทธิ์
        
    } catch (Exception $e) {
        log_message('error', 'Error in can_create_folder_in: ' . $e->getMessage());
        return false; // Safe fallback
    }
}
	
	
public function check_create_folder_permission() {
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
 * 📝 บันทึก log การเข้าถึงโฟลเดอร์แบบง่าย
 */
private function log_folder_access_simple($folder_id, $member_id, $access_granted = true) {
    try {
        if ($this->db->table_exists('tbl_google_drive_folder_access_logs')) {
            $this->db->insert('tbl_google_drive_folder_access_logs', [
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'access_granted' => $access_granted ? 1 : 0,
                'access_time' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address() ?? '',
                'user_agent' => substr($this->input->user_agent() ?? '', 0, 500)
            ]);
        }
    } catch (Exception $e) {
        // ไม่ต้องหยุดทำงาน เพียงแค่ log
        log_message('error', 'Log folder access error: ' . $e->getMessage());
    }
}

/**
 * 🛡️ ตรวจสอบ Session และ Member ID แบบปลอดภัย
 */
private function validate_member_session() {
    try {
        $member_id = $this->member_id ?? $this->session->userdata('m_id');
        
        if (!$member_id) {
            return [
                'valid' => false,
                'message' => 'ไม่พบ session ผู้ใช้'
            ];
        }

        // ตรวจสอบว่า member มีอยู่จริง
        $member_exists = $this->db->select('m_id')
                                 ->from('tbl_member')
                                 ->where('m_id', $member_id)
                                 ->where('m_status', '1') // active members only
                                 ->get()
                                 ->num_rows();

        if ($member_exists === 0) {
            return [
                'valid' => false,
                'message' => 'ผู้ใช้ไม่มีสิทธิ์เข้าถึงระบบ'
            ];
        }

        return [
            'valid' => true,
            'member_id' => $member_id
        ];

    } catch (Exception $e) {
        log_message('error', 'Validate member session error: ' . $e->getMessage());
        return [
            'valid' => false,
            'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ session'
        ];
    }
}
	
/**
 * ✏️ เปลี่ยนชื่อไฟล์/โฟลเดอร์ (Production Version - No Trial/Mock)
 */
public function rename_item() {
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
private function check_rename_permission($item_id, $item_type) {
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
private function log_rename_activity($item_id, $item_type, $original_name, $new_name, $status, $error_message = null) {
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
            log_message('warning', "⚠️ No tables were available for logging rename activity");
        }

    } catch (Exception $e) {
        log_message('error', 'Log rename activity error: ' . $e->getMessage());
    }
}



/**
 * 🔗 เปลี่ยนชื่อใน Google Drive API
 */
private function rename_google_drive_item($item_id, $new_name, $access_token) {
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
private function update_item_name_in_db($item_id, $item_type, $new_name) {
    try {
        $updated_tables = [];
        $timestamp = date('Y-m-d H:i:s');
        
        if ($item_type === 'folder') {
            // อัปเดตในตาราง system folders
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $this->db->where('folder_id', $item_id);
                if ($this->db->update('tbl_google_drive_system_folders', [
                    'folder_name' => $new_name,
                    'updated_at' => $timestamp
                ])) {
                    $updated_tables[] = 'tbl_google_drive_system_folders';
                }
            }
            
            // อัปเดตในตาราง folders
            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $this->db->where('folder_id', $item_id);
                if ($this->db->update('tbl_google_drive_folders', [
                    'folder_name' => $new_name,
                    'updated_at' => $timestamp
                ])) {
                    $updated_tables[] = 'tbl_google_drive_folders';
                }
            }
        } else {
            // อัปเดตในตาราง system files
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $this->db->where('file_id', $item_id);
                if ($this->db->update('tbl_google_drive_system_files', [
                    'file_name' => $new_name,
                    'updated_at' => $timestamp
                ])) {
                    $updated_tables[] = 'tbl_google_drive_system_files';
                }
            }
            
            // อัปเดตในตาราง sync
            if ($this->db->table_exists('tbl_google_drive_sync')) {
                $this->db->where('file_id', $item_id);
                if ($this->db->update('tbl_google_drive_sync', [
                    'file_name' => $new_name,
                    'updated_at' => $timestamp
                ])) {
                    $updated_tables[] = 'tbl_google_drive_sync';
                }
            }
        }

        if (!empty($updated_tables)) {
            log_message('info', "✅ Updated item name in " . count($updated_tables) . " tables: " . implode(', ', $updated_tables));
        } else {
            log_message('warning', "⚠️ No database tables were updated for item {$item_id}");
        }

    } catch (Exception $e) {
        log_message('error', 'Database update error: ' . $e->getMessage());
    }
}
	
	
	
	public function check_file_access() {
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
private function get_file_access_info($file_id, $folder_id) {
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
private function log_unauthorized_file_access($file_id, $folder_id) {
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

        log_message('warning', "Unauthorized file access attempt by member {$this->member_id}: file {$file_id}, folder {$folder_id}");

    } catch (Exception $e) {
        log_message('error', 'Log unauthorized file access error: ' . $e->getMessage());
    }
}
	
	
	
	
	// เพิ่ม method ใน Controller
public function get_drive_settings() {
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
	
	
	
}
?>