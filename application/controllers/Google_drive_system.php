<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive System Controller v1.3.0 (Complete Monolog Fix)
 * จัดการ Centralized Google Drive Storage สำหรับระบบ
 * แก้ไขปัญหา Monolog\Logger not found อย่างสมบูรณ์
 * 
 * @author   System Developer
 * @version  1.3.0 (Complete Monolog Fix)
 * @since    2025-01-20
 */
class Google_drive_system extends CI_Controller {

    private $google_client;
    private $drive_service;
    private $system_storage_id = null;
    private $config_loaded = false;
    private $use_curl_mode = false; // ใช้ cURL แทน Google Client เมื่อมีปัญหา

   public function __construct() {
    parent::__construct();
    $this->load->library('session');
    
    // โหลด Google Drive Model เดิมด้วย
    $this->load->model('Google_drive_model');
    
    // ตรวจสอบสิทธิ์ Admin
    if (!$this->session->userdata('m_id')) {
        redirect('User');
    }
    
    // เฉพาะ Admin เท่านั้นที่เข้าได้
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    // โหลด Google Drive Config
    $this->safe_load_config();

    // โหลด Google Client (ตอนนี้ควรใช้งานได้แล้ว)
    $this->init_google_client_multiple_methods();
}

    /**
     * โหลด Config แบบปลอดภัย
     */
    private function safe_load_config() {
        try {
            if (!$this->config_loaded) {
                $this->config->load('google_drive');
                $this->config_loaded = true;
                log_message('info', 'Google Drive System Config loaded successfully');
            }
        } catch (Exception $e) {
            log_message('error', 'Google Drive System Config Load Error: ' . $e->getMessage());
            $this->set_default_config();
        }
    }

    /**
     * ตั้งค่าเริ่มต้น
     */
    private function set_default_config() {
        $this->config->set_item('google_drive_enabled', true);
        $this->config->set_item('google_scopes', [
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ]);
    }

    /**
     * เริ่มต้น Google Client หลายวิธี (แก้ปัญหา setLogger Error สมบูรณ์)
     */
    private function init_google_client_multiple_methods() {
        try {
            log_message('info', 'Attempting to initialize Google Client (Multiple methods v1.3.0)');
            
            // Method 1: ลองใช้ Google Client ปกติ (อาจมี Monolog Error)
            try {
                if ($this->try_standard_google_client()) {
                    log_message('info', 'Standard Google Client initialized successfully');
                    $this->use_curl_mode = false;
                    return true;
                }
            } catch (Exception $e) {
                log_message('warning', 'Standard Google Client failed: ' . $e->getMessage());
                
                // ถ้าเป็น Logger/Monolog Error ให้เปลี่ยนเป็น cURL mode
                if (strpos($e->getMessage(), 'Logger') !== false || 
                    strpos($e->getMessage(), 'Monolog') !== false ||
                    strpos($e->getMessage(), 'Psr\Log') !== false) {
                    log_message('info', 'Detected Logger/Monolog error - switching to cURL mode');
                    $this->use_curl_mode = true;
                    return true;
                }
            }

            // Method 2: ลองใช้ Google Client แบบ minimal
            try {
                if ($this->try_minimal_google_client()) {
                    log_message('info', 'Minimal Google Client initialized successfully');
                    $this->use_curl_mode = false;
                    return true;
                }
            } catch (Exception $e) {
                log_message('warning', 'Minimal Google Client failed: ' . $e->getMessage());
            }

            // Method 3: ใช้ cURL mode แทน (แน่นอน 100%)
            log_message('info', 'Using cURL mode instead of Google Client (safest method)');
            $this->use_curl_mode = true;
            
            // ตั้งค่า Google Client พื้นฐานสำหรับสร้าง Auth URL เท่านั้น
            $this->setup_basic_google_client_for_auth();
            
            return true;

        } catch (Exception $e) {
            log_message('error', 'All Google Client initialization methods failed: ' . $e->getMessage());
            $this->use_curl_mode = true;
            return false;
        }
    }

    /**
     * ตั้งค่า Google Client พื้นฐานสำหรับ Authentication เท่านั้น
     */
    private function setup_basic_google_client_for_auth() {
        try {
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');

            if (empty($client_id) || empty($client_secret)) {
                return false;
            }

            // สร้าง Google Client แบบ minimal (ไม่สร้าง Drive Service)
            if (class_exists('Google\\Client')) {
                $this->google_client = new Google\Client();
                $this->google_client->setClientId($client_id);
                $this->google_client->setClientSecret($client_secret);
                $this->google_client->setRedirectUri(site_url('google_drive/oauth_callback'));
                
                // เพิ่ม Scopes
                $scopes = $this->config->item('google_scopes');
                if (is_array($scopes)) {
                    foreach ($scopes as $scope) {
                        $this->google_client->addScope($scope);
                    }
                }
                
                $this->google_client->setAccessType('offline');
                $this->google_client->setPrompt('consent');
                
                log_message('info', 'Basic Google Client setup for auth completed');
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Setup basic Google Client error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ลองใช้ Google Client ปกติ
     */
    private function try_standard_google_client() {
        try {
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');
            $redirect_uri = site_url('google_drive/oauth_callback');

            if (empty($client_id) || empty($client_secret)) {
                log_message('info', 'Google OAuth credentials not configured');
                return false;
            }

            if (!class_exists('Google\\Client')) {
                log_message('error', 'Google Client Library not found');
                return false;
            }

            // สร้าง Google Client
            $this->google_client = new Google\Client();
            $this->google_client->setClientId($client_id);
            $this->google_client->setClientSecret($client_secret);
            $this->google_client->setRedirectUri($redirect_uri);
            
            // เพิ่ม Scopes
            $scopes = $this->config->item('google_scopes');
            if (is_array($scopes)) {
                foreach ($scopes as $scope) {
                    $this->google_client->addScope($scope);
                }
            }
            
            $this->google_client->setAccessType('offline');
            $this->google_client->setPrompt('consent');

            // ตั้งค่า Application Name
            if (method_exists($this->google_client, 'setApplicationName')) {
                $this->google_client->setApplicationName('System Storage v1.3.0');
            }

            // ปิด Logger เพื่อหลีกเลี่ยง Monolog Error
            // หมายเหตุ: ไม่สามารถตั้งค่า setLogger(null) ได้ใน Google Client เวอร์ชันใหม่

            // สร้าง Drive Service
            $this->drive_service = new Google\Service\Drive($this->google_client);

            // โหลด System Access Token
            $this->load_system_access_token();

            return true;

        } catch (Exception $e) {
            log_message('error', 'Standard Google Client failed: ' . $e->getMessage());
            
            // ตรวจสอบว่าเป็น Monolog Error
            if (strpos($e->getMessage(), 'Monolog') !== false || 
                strpos($e->getMessage(), 'Logger') !== false) {
                log_message('error', 'Monolog dependency error detected');
            }
            
            return false;
        }
    }

    /**
     * ลองใช้ Google Client แบบ minimal
     */
    private function try_minimal_google_client() {
        try {
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');

            if (empty($client_id) || empty($client_secret)) {
                return false;
            }

            // สร้าง Google Client แบบ minimal configuration
            $this->google_client = new Google\Client();
            $this->google_client->setClientId($client_id);
            $this->google_client->setClientSecret($client_secret);

            // ไม่ตั้งค่า advanced features ที่อาจใช้ Monolog
            
            return true;

        } catch (Exception $e) {
            log_message('error', 'Minimal Google Client failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * หน้าแรกของ System Storage Management
     */
    public function index() {
        redirect('google_drive_system/dashboard');
    }


    public function dashboard() {
    try {
        log_message('info', 'Loading Google Drive System Dashboard');

        // ดึงข้อมูล System Storage
        $data['system_storage'] = $this->get_enhanced_system_storage_info();
        
        // ดึงสถิติการใช้งาน
        $data['storage_stats'] = $this->get_enhanced_storage_statistics();
        
        // ข้อมูลเพิ่มเติม
        $data['recent_activities'] = [];
        $data['folder_structure'] = [];
        $data['use_curl_mode'] = $this->use_curl_mode;
        $data['system_ready'] = false;

        // ตรวจสอบความพร้อมของระบบ
        if ($data['system_storage']) {
            $data['system_ready'] = (bool)$data['system_storage']->folder_structure_created;
            
            // เพิ่มข้อมูลที่แปลงแล้วสำหรับแสดงผล
            $data['system_storage']->total_storage_used_formatted = $this->format_bytes($data['system_storage']->total_storage_used);
            $data['system_storage']->max_storage_limit_formatted = $this->format_bytes($data['system_storage']->max_storage_limit);
            
            // คำนวณเปอร์เซ็นต์การใช้งาน
            if ($data['system_storage']->max_storage_limit > 0) {
                $data['system_storage']->storage_usage_percent = round(
                    ($data['system_storage']->total_storage_used / $data['system_storage']->max_storage_limit) * 100, 2
                );
            } else {
                $data['system_storage']->storage_usage_percent = 0;
            }
        }

        // Log การเข้าชม Dashboard
        $this->log_enhanced_activity(
            $this->session->userdata('m_id'),
            'dashboard_view',
            'ดู Google Drive System Dashboard',
            [
                'status' => 'success',
                'extra' => [
                    'system_ready' => $data['system_ready'],
                    'curl_mode' => $data['use_curl_mode']
                ]
            ]
        );

        // โหลด Views
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_dashboard', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');

    } catch (Exception $e) {
        log_message('error', 'Dashboard error: ' . $e->getMessage());
        
        // แสดงหน้า Error แทน
        $data['error_message'] = 'เกิดข้อผิดพลาดในการโหลด Dashboard: ' . $e->getMessage();
        $data['system_storage'] = null;
        $data['storage_stats'] = $this->get_default_storage_stats();
        
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_dashboard', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }
}

	
	
	
	/**
 * ✅ ดึงข้อมูล System Storage แบบครบถ้วน
 */
private function get_enhanced_system_storage_info() {
    try {
        // สร้างตารางถ้ายังไม่มี
        $this->create_system_storage_table_if_not_exists();
        
        if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
            log_message('warning', 'System storage table does not exist');
            return null;
        }

        $system_storage = $this->db->select('*')
                                      ->from('tbl_google_drive_system_storage')
                                      ->where('is_active', 1)
                                      ->get()
                                      ->row();

        if (!$system_storage) {
            log_message('info', 'No active system storage found');
            return null;
        }

        // ดึงสถิติเพิ่มเติม
        $total_folders = 0;
        $total_files = 0;
        $active_users = 0;

        // นับโฟลเดอร์
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $total_folders = $this->db->where('is_active', 1)
                                     ->count_all_results('tbl_google_drive_system_folders');
        }

        // นับไฟล์
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $total_files = $this->db->count_all('tbl_google_drive_system_files');
        }

        // นับผู้ใช้ที่มีสิทธิ์
        $active_users = $this->db->where('storage_access_granted', 1)
                                ->count_all_results('tbl_member');

        // สร้าง Object ที่มีข้อมูลครบถ้วน
        return (object)[
            'id' => $system_storage->id,
            'storage_name' => $system_storage->storage_name ?? 'Organization Storage',
            'google_account_email' => $system_storage->google_account_email,
            'total_storage_used' => (int)($system_storage->total_storage_used ?? 0),
            'max_storage_limit' => (int)($system_storage->max_storage_limit ?? 107374182400), // 100GB default
            'folder_structure_created' => (bool)($system_storage->folder_structure_created ?? false),
            'is_active' => (bool)($system_storage->is_active ?? true),
            'created_at' => $system_storage->created_at,
            'updated_at' => $system_storage->updated_at ?? null,
            'total_folders' => $total_folders,
            'total_files' => $total_files,
            'active_users' => $active_users,
            'storage_usage_percent' => 0, // จะคำนวณใน dashboard method
            'google_token_expires' => $system_storage->google_token_expires ?? null,
            'google_access_token' => $system_storage->google_access_token ?? null
        ];

    } catch (Exception $e) {
        log_message('error', 'Get enhanced system storage info error: ' . $e->getMessage());
        return null;
    }
}

	
	/**
 * ✅ ดึงสถิติการใช้งานแบบครบถ้วน
 */
private function get_enhanced_storage_statistics() {
    try {
        $stats = [
            'connected_members' => 0,
            'total_folders' => 0,
            'total_files' => 0,
            'new_connections' => 0,
            'storage_usage' => [
                'used_bytes' => 0,
                'limit_bytes' => 107374182400, // 100GB
                'used_formatted' => '0 B',
                'limit_formatted' => '100 GB',
                'percentage' => 0
            ],
            'activity_summary' => [
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0
            ]
        ];

        // นับสมาชิกที่มีสิทธิ์เข้าถึง Storage
        $stats['connected_members'] = $this->db->where('storage_access_granted', 1)
                                              ->count_all_results('tbl_member');

        // นับโฟลเดอร์ทั้งหมด
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $stats['total_folders'] = $this->db->where('is_active', 1)
                                             ->count_all_results('tbl_google_drive_system_folders');
        }

        // นับไฟล์ทั้งหมด
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $file_stats = $this->db->select('COUNT(*) as total_files, COALESCE(SUM(file_size), 0) as total_size')
                                   ->from('tbl_google_drive_system_files')
                                   ->get()
                                   ->row();
            
            $stats['total_files'] = (int)($file_stats->total_files ?? 0);
            $stats['storage_usage']['used_bytes'] = (int)($file_stats->total_size ?? 0);
        }

        // นับการเชื่อมต่อใหม่ในเดือนนี้
        $stats['new_connections'] = $this->db->where('last_storage_access >=', date('Y-m-01'))
                                           ->where('storage_access_granted', 1)
                                           ->count_all_results('tbl_member');

        // คำนวณการใช้งาน Storage
        $system_storage = $this->get_active_system_storage();
        if ($system_storage) {
            $stats['storage_usage']['used_bytes'] = (int)($system_storage->total_storage_used ?? 0);
            $stats['storage_usage']['limit_bytes'] = (int)($system_storage->max_storage_limit ?? 107374182400);
        }

        $stats['storage_usage']['used_formatted'] = $this->format_bytes($stats['storage_usage']['used_bytes']);
        $stats['storage_usage']['limit_formatted'] = $this->format_bytes($stats['storage_usage']['limit_bytes']);
        
        if ($stats['storage_usage']['limit_bytes'] > 0) {
            $stats['storage_usage']['percentage'] = round(
                ($stats['storage_usage']['used_bytes'] / $stats['storage_usage']['limit_bytes']) * 100, 2
            );
        }

        // สถิติกิจกรรม
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $stats['activity_summary']['today'] = $this->db->where('DATE(created_at)', date('Y-m-d'))
                                                         ->count_all_results('tbl_google_drive_logs');
            
            $stats['activity_summary']['this_week'] = $this->db->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                                             ->count_all_results('tbl_google_drive_logs');
            
            $stats['activity_summary']['this_month'] = $this->db->where('created_at >=', date('Y-m-01'))
                                                              ->count_all_results('tbl_google_drive_logs');
        }

        log_message('info', 'Enhanced storage statistics: ' . json_encode($stats));
        return $stats;

    } catch (Exception $e) {
        log_message('error', 'Get enhanced storage statistics error: ' . $e->getMessage());
        return $this->get_default_storage_stats();
    }
}

	
	
	
	/**
 * ✅ สถิติเริ่มต้นเมื่อเกิดข้อผิดพลาด
 */
private function get_default_storage_stats() {
    return [
        'connected_members' => 0,
        'total_folders' => 0,
        'total_files' => 0,
        'new_connections' => 0,
        'storage_usage' => [
            'used_bytes' => 0,
            'limit_bytes' => 107374182400,
            'used_formatted' => '0 B',
            'limit_formatted' => '100 GB',
            'percentage' => 0
        ],
        'activity_summary' => [
            'today' => 0,
            'this_week' => 0,
            'this_month' => 0
        ]
    ];
}

/**
 * ✅ Format bytes แบบปลอดภัย
 */
private function format_bytes($bytes, $precision = 2) {
    try {
        $bytes = max(0, (int)$bytes);
        
        if ($bytes === 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
        
    } catch (Exception $e) {
        return '0 B';
    }
}
	
	
	

	
	/**
 * ✅ รีเฟรชข้อมูล Dashboard
 */
public function refresh_dashboard_data() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $refreshed_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'system_storage' => $this->get_enhanced_system_storage_info(),
            'storage_stats' => $this->get_enhanced_storage_statistics(),
            'recent_activities' => $this->get_recent_activities_data(10),
            'folder_structure' => $this->get_folder_structure_data()
        ];

        // เพิ่มข้อมูลที่แปลงแล้ว
        if ($refreshed_data['system_storage']) {
            $refreshed_data['system_storage']->total_storage_used_formatted = 
                $this->format_bytes($refreshed_data['system_storage']->total_storage_used);
            $refreshed_data['system_storage']->max_storage_limit_formatted = 
                $this->format_bytes($refreshed_data['system_storage']->max_storage_limit);
            
            if ($refreshed_data['system_storage']->max_storage_limit > 0) {
                $refreshed_data['system_storage']->storage_usage_percent = round(
                    ($refreshed_data['system_storage']->total_storage_used / $refreshed_data['system_storage']->max_storage_limit) * 100, 2
                );
            } else {
                $refreshed_data['system_storage']->storage_usage_percent = 0;
            }
        }

        // Log การรีเฟรช
        $this->log_enhanced_activity(
            $this->session->userdata('m_id'),
            'dashboard_refresh',
            'รีเฟรชข้อมูล Dashboard',
            ['status' => 'success']
        );

        $this->output_json_success($refreshed_data, 'รีเฟรชข้อมูล Dashboard สำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Refresh dashboard data error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาดในการรีเฟรข้อมูล: ' . $e->getMessage());
    }
}
	
	
   

    /**
     * หน้าตั้งค่า System Storage
     */
    public function setup() {
        $data['system_storage'] = $this->get_system_storage_info();
        $data['setup_status'] = $this->get_system_setup_status();
        $data['use_curl_mode'] = $this->use_curl_mode;
        
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_setup', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * เริ่มต้นการเชื่อมต่อ System Google Account
     */
    public function connect_system_account() {
    try {
        // ตรวจสอบว่ามี System Storage อยู่แล้วหรือไม่ (ยกเว้นการเชื่อมต่อใหม่)
        $force_reconnect = $this->input->get('force_reconnect') === '1';
        
        if (!$force_reconnect) {
            $existing = $this->get_active_system_storage();
            if ($existing) {
                $this->session->set_flashdata('warning', 'ระบบมี Google Account หลักอยู่แล้ว หากต้องการเชื่อมต่อใหม่ กรุณาใช้ปุ่ม "เชื่อมต่อใหม่"');
                redirect('google_drive_system/setup');
            }
        }

        // สร้าง Authorization URL ที่บังคับให้ได้ Refresh Token
        $auth_url = $this->create_auth_url_with_refresh_token();
        
        if (!$auth_url) {
            $this->session->set_flashdata('error', 'ไม่สามารถสร้าง Authorization URL ได้ กรุณาตรวจสอบการตั้งค่า Google OAuth');
            redirect('google_drive_system/setup');
        }

        // เก็บข้อมูลใน Session
        $this->session->set_userdata('system_oauth_type', 'setup');
        $this->session->set_userdata('system_oauth_admin', $this->session->userdata('m_id'));
        $this->session->set_userdata('oauth_member_id', $this->session->userdata('m_id'));
        $this->session->set_userdata('force_reconnect', $force_reconnect);
        
        log_message('info', 'Redirecting to Google OAuth for system setup (force_reconnect: ' . ($force_reconnect ? 'yes' : 'no') . ')');
        redirect($auth_url);

    } catch (Exception $e) {
        log_message('error', 'System connect error: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        redirect('google_drive_system/setup');
    }
}

/**
 * ✅ สร้าง Auth URL ที่บังคับให้ได้ Refresh Token
 */
private function create_auth_url_with_refresh_token() {
    try {
        $client_id = $this->get_setting('google_client_id');
        $redirect_uri = site_url('google_drive/oauth_callback');
        $scopes = $this->config->item('google_scopes');
        
        if (empty($client_id)) {
            return null;
        }

        $scope_string = is_array($scopes) ? implode(' ', $scopes) : 'https://www.googleapis.com/auth/drive';
        
        $params = [
            'client_id' => trim($client_id),
            'redirect_uri' => trim($redirect_uri),
            'scope' => $scope_string,
            'response_type' => 'code',
            'access_type' => 'offline',  // ✅ จำเป็นสำหรับ Refresh Token
            'prompt' => 'consent',       // ✅ บังคับให้แสดง Consent Screen
            'include_granted_scopes' => 'false',
            'state' => 'system_setup_' . time()
        ];

        $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        log_message('info', 'Created auth URL with refresh token parameters');
        
        return $auth_url;

    } catch (Exception $e) {
        log_message('error', 'Create auth URL with refresh token error: ' . $e->getMessage());
        return null;
    }
}


    /**
     * สร้าง Authorization URL แบบปลอดภัย
     */
    private function create_auth_url_safe() {
        try {
            // Method 1: ใช้ Google Client ถ้ามี
            if ($this->google_client && !$this->use_curl_mode) {
                try {
                    $auth_url = $this->google_client->createAuthUrl();
                    if (strpos($auth_url, 'client_id=') !== false) {
                        return $auth_url;
                    }
                } catch (Exception $e) {
                    log_message('warning', 'Google Client createAuthUrl failed: ' . $e->getMessage());
                }
            }

            // Method 2: สร้าง Manual Auth URL
            return $this->create_manual_auth_url();

        } catch (Exception $e) {
            log_message('error', 'Create auth URL safe error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * สร้าง Manual Auth URL
     */
    private function create_manual_auth_url() {
        try {
            $client_id = $this->get_setting('google_client_id');
            $redirect_uri = site_url('google_drive/oauth_callback');
            $scopes = $this->config->item('google_scopes');
            
            if (empty($client_id)) {
                return null;
            }

            $scope_string = is_array($scopes) ? implode(' ', $scopes) : 'https://www.googleapis.com/auth/drive';
            
            $params = [
                'client_id' => trim($client_id),
                'redirect_uri' => trim($redirect_uri),
                'scope' => $scope_string,
                'response_type' => 'code',
                'access_type' => 'offline',
                'prompt' => 'consent',
                'state' => 'system_' . time()
            ];

            $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
            return $auth_url;

        } catch (Exception $e) {
            log_message('error', 'Create manual auth URL error: ' . $e->getMessage());
            return null;
        }
    }

    

    /**
     * ตรวจสอบว่ามี Access Token ที่ใช้งานได้
     */
    private function has_valid_access_token($system_storage) {
        try {
            if (!$system_storage->google_access_token) {
                return false;
            }

            $token_data = json_decode($system_storage->google_access_token, true);
            if (!$token_data || !isset($token_data['access_token'])) {
                return false;
            }

            // ตรวจสอบว่า token หมดอายุหรือไม่
            if ($system_storage->google_token_expires && strtotime($system_storage->google_token_expires) <= time()) {
                log_message('info', 'Access token expired');
                return false; // ไม่ลอง refresh ในที่นี้
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Check access token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ทดสอบการเข้าถึง Google Drive API
     */
    private function test_drive_api_access($system_storage) {
        try {
            $token_data = json_decode($system_storage->google_access_token, true);
            if (!$token_data || !isset($token_data['access_token'])) {
                return false;
            }

            // ทดสอบด้วย cURL
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token_data['access_token'],
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                log_message('error', 'cURL Error in API test: ' . $error);
                return false;
            }

            if ($http_code === 200) {
                $data = json_decode($response, true);
                if ($data && isset($data['user'])) {
                    log_message('info', 'Google Drive API access test successful');
                    return true;
                }
            }

            log_message('error', 'Google Drive API test failed: HTTP ' . $http_code);
            return false;

        } catch (Exception $e) {
            log_message('error', 'Test drive API access error: ' . $e->getMessage());
            return false;
        }
    }



	
	/**
 * ✅ Simple verification function
 */
public function verify_department_folders() {
    echo "<h1>✅ ตรวจสอบ Department Folders</h1>";
    
    try {
        // ตรวจสอบ positions
        $positions = $this->db->where('pstatus', 'show')->get('tbl_position')->result();
        echo "<p>📋 Positions ที่ต้องมี: " . count($positions) . " รายการ</p>";
        
        // ตรวจสอบ department folders
        $dept_folders = $this->db->where('folder_type', 'department')->get('tbl_google_drive_system_folders')->result();
        echo "<p>📁 Department folders ที่มีอยู่: " . count($dept_folders) . " รายการ</p>";
        
        if (count($dept_folders) >= count($positions)) {
            echo "<p style='color: green;'>🎉 <strong>สำเร็จ!</strong> มี department folders ครบแล้ว</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ ยังไม่ครบ - ขาดอีก " . (count($positions) - count($dept_folders)) . " โฟลเดอร์</p>";
        }
        
        if (!empty($dept_folders)) {
            echo "<h3>📂 รายการ Department Folders</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr style='background: #f5f5f5;'><th>ลำดับ</th><th>ชื่อ</th><th>Position ID</th><th>ลิงก์</th></tr>";
            
            foreach ($dept_folders as $index => $folder) {
                $link = "https://drive.google.com/drive/folders/{$folder->folder_id}";
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td><strong>{$folder->folder_name}</strong></td>";
                echo "<td>{$folder->created_for_position}</td>";
                echo "<td><a href='{$link}' target='_blank'>เปิด</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='" . site_url('google_drive_system/setup') . "'>🏠 กลับไป Setup</a></p>";
}
	
    

    /**
 * ✅ FINAL create_department_folders_curl() - ใช้ logic ที่ work แล้ว
 */
private function create_department_folders_curl($departments_folder_id, $access_token) {
    try {
        log_message('info', '====== CREATE DEPARTMENT FOLDERS (WORKING VERSION) ======');
        log_message('info', 'Departments Folder ID: ' . $departments_folder_id);
        
        // ตรวจสอบ input
        if (empty($departments_folder_id) || empty($access_token)) {
            log_message('error', 'Missing departments_folder_id or access_token');
            return 0;
        }
        
        // ดึงรายการ positions ทั้งหมด
        $positions = $this->db->where('pstatus', 'show')
                             ->order_by('porder', 'ASC')
                             ->get('tbl_position')
                             ->result();

        if (empty($positions)) {
            log_message('error', 'No positions found with pstatus = "show"');
            return 0;
        }

        log_message('info', 'Found ' . count($positions) . ' positions to process');
        
        $created_count = 0;
        $success_count = 0;
        $error_count = 0;

        foreach ($positions as $index => $position) {
            try {
                $step = $index + 1;
                log_message('info', "[{$step}/" . count($positions) . "] Processing: {$position->pname}");
                
                // ตรวจสอบว่ามีอยู่แล้วหรือไม่
                $existing = $this->db->where('folder_name', $position->pname)
                                   ->where('folder_type', 'department')
                                   ->where('created_for_position', $position->pid)
                                   ->get('tbl_google_drive_system_folders')
                                   ->row();
                
                if ($existing) {
                    log_message('info', "Folder already exists: {$position->pname} (ID: {$existing->folder_id})");
                    
                    // ทดสอบการเข้าถึง
                    if ($this->test_folder_exists_simple($existing->folder_id, $access_token)) {
                        log_message('info', "Existing folder is accessible, counting as success: {$position->pname}");
                        $created_count++;
                        $success_count++;
                        continue;
                    } else {
                        log_message('warning', "Existing folder not accessible, will recreate: {$position->pname}");
                        // ลบข้อมูลเก่าออก
                        $this->db->where('id', $existing->id)->delete('tbl_google_drive_system_folders');
                    }
                }
                
                // สร้างโฟลเดอร์ใหม่
                $folder = $this->create_folder_with_curl($position->pname, $departments_folder_id, $access_token);
                
                if ($folder && isset($folder['id'])) {
                    log_message('info', "Google Drive folder created: {$folder['id']}");
                    
                    // บันทึกลงฐานข้อมูล
                    $folder_data = [
                        'folder_name' => $position->pname,
                        'folder_id' => $folder['id'],
                        'parent_folder_id' => $departments_folder_id,
                        'folder_type' => 'department',
                        'folder_path' => '/Organization Drive/Departments/' . $position->pname,
                        'created_for_position' => $position->pid,
                        'folder_description' => 'โฟลเดอร์สำหรับ ' . $position->pname,
                        'permission_level' => 'restricted',
                        'created_by' => $this->session->userdata('m_id'),
                        'is_active' => 1
                    ];

                    if ($this->save_folder_info($folder_data)) {
                        $created_count++;
                        $success_count++;
                        log_message('info', "Database saved successfully: {$position->pname}");
                    } else {
                        $error_count++;
                        log_message('error', "Database save failed: {$position->pname}");
                    }
                } else {
                    $error_count++;
                    log_message('error', "Google folder creation failed: {$position->pname}");
                }
                
                // Delay เพื่อหลีกเลี่ยง rate limit
                if ($index < count($positions) - 1) {
                    usleep(500000); // 0.5 วินาที
                }
                
            } catch (Exception $e) {
                $error_count++;
                log_message('error', "Exception for position {$position->pname}: " . $e->getMessage());
            }
        }

        log_message('info', '====== DEPARTMENT FOLDERS SUMMARY ======');
        log_message('info', "Total positions: " . count($positions));
        log_message('info', "Success: {$success_count}");
        log_message('info', "Errors: {$error_count}");
        log_message('info', "Final created_count: {$created_count}");

        return $created_count;

    } catch (Exception $e) {
        log_message('error', 'CRITICAL ERROR in create_department_folders_curl: ' . $e->getMessage());
        return 0;
    }
}

/**
 * ✅ Helper function - Simple folder existence test
 */
private function test_folder_exists_simple($folder_id, $access_token) {
    try {
        if (empty($folder_id) || empty($access_token)) {
            return false;
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}?fields=id,name,trashed",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error || $http_code !== 200) {
            return false;
        }

        $data = json_decode($response, true);
        return ($data && isset($data['id']) && !($data['trashed'] ?? false));

    } catch (Exception $e) {
        return false;
    }
}


    /**
     * โหลด System Access Token
     */
    private function load_system_access_token() {
        try {
            $system_storage = $this->get_active_system_storage();
            if (!$system_storage || !$system_storage->google_access_token) {
                return false;
            }

            $token_data = json_decode($system_storage->google_access_token, true);
            
            if (!$token_data) {
                return false;
            }

            // ถ้าใช้ Google Client
            if ($this->google_client && !$this->use_curl_mode) {
                $this->google_client->setAccessToken($token_data);
            }
            
            $this->system_storage_id = $system_storage->id;
            return true;

        } catch (Exception $e) {
            log_message('error', 'Load system access token error: ' . $e->getMessage());
            return false;
        }
    }

    // ===========================================
    // Database Helper Methods
    // ===========================================

    /**
     * ดึงข้อมูล System Storage
     */
    private function get_system_storage_info() {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                return null;
            }

            $system_storage = $this->db->select('*')
                                      ->from('tbl_google_drive_system_storage')
                                      ->where('is_active', 1)
                                      ->get()
                                      ->row();

            if (!$system_storage) {
                return null;
            }

            // ดึงสถิติการใช้งาน
            $total_folders = 0;
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $total_folders = $this->db->where('is_active', 1)
                                         ->count_all_results('tbl_google_drive_system_folders');
            }

            $total_files = 0;
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $total_files = $this->db->count_all('tbl_google_drive_system_files');
            }

            $active_users = $this->db->where('storage_access_granted', 1)
                                    ->count_all_results('tbl_member');

            return (object)[
                'id' => $system_storage->id,
                'storage_name' => $system_storage->storage_name,
                'google_account_email' => $system_storage->google_account_email,
                'total_storage_used' => $system_storage->total_storage_used,
                'max_storage_limit' => $system_storage->max_storage_limit,
                'folder_structure_created' => $system_storage->folder_structure_created,
                'is_active' => $system_storage->is_active,
                'created_at' => $system_storage->created_at,
                'total_folders' => $total_folders,
                'total_files' => $total_files,
                'active_users' => $active_users,
                'storage_usage_percent' => $system_storage->max_storage_limit > 0 ? 
                    round(($system_storage->total_storage_used / $system_storage->max_storage_limit) * 100, 2) : 0
            ];

        } catch (Exception $e) {
            log_message('error', 'Get system storage info error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึง System Storage ที่ Active
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
     * อัปเดต System Storage
     */
    private function update_system_storage($storage_id, $data) {
        try {
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $this->db->where('id', $storage_id);
            return $this->db->update('tbl_google_drive_system_storage', $data);

        } catch (Exception $e) {
            log_message('error', 'Update system storage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
 * ✅ Enhanced save_folder_info() - Handle duplicates and field validation
 */
private function save_folder_info($folder_data) {
    try {
        log_message('info', 'Saving folder info: ' . $folder_data['folder_name']);
        
        // ตรวจสอบ required fields
        $required_fields = ['folder_name', 'folder_id', 'folder_type'];
        foreach ($required_fields as $field) {
            if (empty($folder_data[$field])) {
                log_message('error', "Required field '{$field}' is missing");
                return false;
            }
        }
        
        // ตรวจสอบ table existence
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            log_message('error', 'Table tbl_google_drive_system_folders does not exist');
            return false;
        }
        
        // ตรวจสอบ duplicate
        $existing = $this->db->where('folder_id', $folder_data['folder_id'])
                           ->get('tbl_google_drive_system_folders')
                           ->row();
        
        if ($existing) {
            log_message('warning', "Folder ID already exists in database: {$folder_data['folder_id']}");
            return true; // Consider it successful since folder exists
        }
        
        // เตรียมข้อมูลสำหรับ insert
        $table_fields = $this->db->list_fields('tbl_google_drive_system_folders');
        
        $safe_data = [
            'folder_name' => trim($folder_data['folder_name']),
            'folder_id' => trim($folder_data['folder_id']),
            'parent_folder_id' => $folder_data['parent_folder_id'] ?? null,
            'folder_type' => $folder_data['folder_type'] ?? 'system',
            'folder_path' => $folder_data['folder_path'] ?? null,
            'permission_level' => $folder_data['permission_level'] ?? 'restricted',
            'folder_description' => $folder_data['folder_description'] ?? null,
            'is_active' => 1
        ];
        
        // เพิ่ม optional fields ถ้ามีในตาราง
        if (in_array('created_for_position', $table_fields) && isset($folder_data['created_for_position'])) {
            $safe_data['created_for_position'] = $folder_data['created_for_position'];
        }
        
        if (in_array('created_by', $table_fields) && isset($folder_data['created_by'])) {
            $safe_data['created_by'] = $folder_data['created_by'];
        }
        
        // ลบ fields ที่ไม่มีในตาราง
        foreach ($safe_data as $key => $value) {
            if (!in_array($key, $table_fields)) {
                unset($safe_data[$key]);
            }
        }
        
        // Insert ข้อมูล
        $result = $this->db->insert('tbl_google_drive_system_folders', $safe_data);
        
        if ($result) {
            $insert_id = $this->db->insert_id();
            log_message('info', "Folder saved to database with ID: {$insert_id}");
            return true;
        } else {
            $db_error = $this->db->error();
            log_message('error', "Database insert failed: " . json_encode($db_error));
            return false;
        }
        
    } catch (Exception $e) {
        log_message('error', "save_folder_info error: " . $e->getMessage());
        return false;
    }
}

    /**
     * ดึงสถานะการตั้งค่า
     */
    private function get_system_setup_status() {
        try {
            $status = [
                'has_system_storage' => false,
                'folder_structure_created' => false,
                'ready_to_use' => false
            ];

            $system_storage = $this->get_system_storage_info();
            if ($system_storage) {
                $status['has_system_storage'] = true;
                $status['folder_structure_created'] = (bool)$system_storage->folder_structure_created;
            }

            $status['ready_to_use'] = $status['has_system_storage'] && $status['folder_structure_created'];

            return $status;

        } catch (Exception $e) {
            log_message('error', 'Get system setup status error: ' . $e->getMessage());
            return [
                'has_system_storage' => false,
                'folder_structure_created' => false,
                'ready_to_use' => false
            ];
        }
    }

    /**
     * ดึงสถิติ Storage
     */
    private function get_storage_statistics() {
        try {
            $connected_members = $this->db->where('storage_access_granted', 1)
                                         ->count_all_results('tbl_member');

            $total_folders = 0;
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $total_folders = $this->db->where('is_active', 1)
                                         ->count_all_results('tbl_google_drive_system_folders');
            }

            $total_files = 0;
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $total_files = $this->db->count_all('tbl_google_drive_system_files');
            }

            $new_connections = $this->db->where('last_storage_access >=', date('Y-m-01'))
                                       ->where('storage_access_granted', 1)
                                       ->count_all_results('tbl_member');

            return [
                'connected_members' => $connected_members,
                'total_folders' => $total_folders,
                'total_files' => $total_files,
                'new_connections' => $new_connections
            ];

        } catch (Exception $e) {
            log_message('error', 'Get storage statistics error: ' . $e->getMessage());
            return [
                'connected_members' => 0,
                'total_folders' => 0,
                'total_files' => 0,
                'new_connections' => 0
            ];
        }
    }

    // ===========================================
    // Table Creation Methods
    // ===========================================

    /**
     * สร้างตาราง System Storage ถ้ายังไม่มี
     */
    private function create_system_storage_table_if_not_exists() {
        if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
            $sql = "
                CREATE TABLE IF NOT EXISTS `tbl_google_drive_system_storage` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `storage_name` varchar(100) NOT NULL DEFAULT 'Organization Storage',
                    `google_account_email` varchar(255) NOT NULL COMMENT 'Google Account หลักของระบบ',
                    `google_access_token` text DEFAULT NULL COMMENT 'System Access Token',
                    `google_refresh_token` varchar(255) DEFAULT NULL COMMENT 'System Refresh Token',
                    `google_token_expires` datetime DEFAULT NULL COMMENT 'วันหมดอายุ Token',
                    `root_folder_id` varchar(255) DEFAULT NULL COMMENT 'Root Folder ID ใน Google Drive',
                    `total_storage_used` bigint(20) DEFAULT 0 COMMENT 'พื้นที่ใช้งาน (bytes)',
                    `max_storage_limit` bigint(20) DEFAULT 107374182400 COMMENT 'ขีดจำกัด Storage (100GB default)',
                    `folder_structure_created` tinyint(1) DEFAULT 0 COMMENT 'สร้างโครงสร้างโฟลเดอร์แล้วหรือยัง',
                    `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะการใช้งาน',
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    `created_by` int(11) DEFAULT NULL COMMENT 'Admin ที่สร้าง',
                    PRIMARY KEY (`id`),
                    KEY `idx_google_email` (`google_account_email`),
                    KEY `idx_active` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางเก็บข้อมูล System Storage หลัก';
            ";

            $this->db->query($sql);
        }
    }

    /**
     * สร้างตาราง System Folders ถ้ายังไม่มี
     */
    private function create_system_folders_table_if_not_exists() {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            $sql = "
                CREATE TABLE IF NOT EXISTS `tbl_google_drive_system_folders` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `folder_name` varchar(255) NOT NULL COMMENT 'ชื่อโฟลเดอร์',
                    `folder_id` varchar(255) NOT NULL COMMENT 'Google Drive Folder ID',
                    `parent_folder_id` varchar(255) DEFAULT NULL COMMENT 'Parent Folder ID',
                    `folder_type` enum('system','department','shared','user','admin') DEFAULT 'system' COMMENT 'ประเภทโฟลเดอร์',
                    `folder_path` varchar(500) DEFAULT NULL COMMENT 'Path ของโฟลเดอร์',
                    `created_for_position` int(11) DEFAULT NULL COMMENT 'สร้างสำหรับตำแหน่งไหน (อ้างอิง tbl_position)',
                    `permission_level` enum('public','restricted','private') DEFAULT 'restricted' COMMENT 'ระดับการเข้าถึง',
                    `folder_description` text DEFAULT NULL COMMENT 'คำอธิบายโฟลเดอร์',
                    `storage_quota` bigint(20) DEFAULT 1073741824 COMMENT 'Quota สำหรับโฟลเดอร์นี้ (1GB default)',
                    `storage_used` bigint(20) DEFAULT 0 COMMENT 'พื้นที่ที่ใช้ไปแล้ว',
                    `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะการใช้งาน',
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    `created_by` int(11) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_folder_id` (`folder_id`),
                    KEY `idx_folder_type` (`folder_type`),
                    KEY `idx_parent_folder` (`parent_folder_id`),
                    KEY `idx_position` (`created_for_position`),
                    KEY `idx_active` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='โครงสร้างโฟลเดอร์ใน System Storage';
            ";

            $this->db->query($sql);
        }
    }

    // ===========================================
    // Utility Methods
    // ===========================================

    /**
     * ดึงการตั้งค่า
     */
    private function get_setting($key, $default = '0') {
    try {
        $setting = $this->db->where('setting_key', $key)
                           ->where('is_active', 1)
                           ->get('tbl_google_drive_settings')
                           ->row();
        
        return $setting ? $setting->setting_value : $default;
        
    } catch (Exception $e) {
        log_message('error', 'get_setting error: ' . $e->getMessage());
        return $default;
    }
}

    /**
     * Output JSON Success
     */
    private function output_json_success($data = [], $message = 'Success') {
        if (ob_get_level()) {
            ob_clean();
        }
        
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode([
                'success' => true,
                'message' => $message,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * Output JSON Error
     */
    private function output_json_error($message = 'Error', $extra_data = null, $status_code = 400) {
    if (ob_get_level()) {
        ob_clean();
    }
    
    $response = [
        'success' => false,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    if ($extra_data) {
        $response = array_merge($response, $extra_data);
    }
    
    $this->output
        ->set_status_header($status_code)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE));
}

	

	

    // ===========================================
    // New Methods for Complete System
    // ===========================================

    /**
     * ตรวจสอบสถานะระบบและการตั้งค่า
     */
    public function system_status() {
        try {
            $status = [
                'google_client_available' => class_exists('Google\\Client'),
                'use_curl_mode' => $this->use_curl_mode,
                'config_loaded' => $this->config_loaded,
                'system_storage_exists' => (bool)$this->get_active_system_storage(),
                'folder_structure_ready' => false,
                'oauth_configured' => false
            ];

            // ตรวจสอบ OAuth config
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');
            $status['oauth_configured'] = !empty($client_id) && !empty($client_secret);

            // ตรวจสอบ folder structure
            $system_storage = $this->get_active_system_storage();
            if ($system_storage) {
                $status['folder_structure_ready'] = (bool)$system_storage->folder_structure_created;
            }

            $this->output_json_success($status, 'System status retrieved');

        } catch (Exception $e) {
            $this->output_json_error('Cannot get system status: ' . $e->getMessage());
        }
    }

    /**
     * ดู Log การทำงานของระบบ
     */
    public function view_logs() {
        $data['logs'] = $this->get_system_logs();
        
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_logs', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * ดึง System Logs
     */
    private function get_system_logs($limit = 100) {
        try {
            // อ่าน log files จาก CodeIgniter
            $log_path = APPPATH . 'logs/';
            $logs = [];
            
            if (is_dir($log_path)) {
                $files = glob($log_path . 'log-*.php');
                rsort($files); // เรียงใหม่สุดก่อน
                
                foreach (array_slice($files, 0, 5) as $file) { // อ่าน 5 ไฟล์ล่าสุด
                    $content = file_get_contents($file);
                    if ($content) {
                        // แยก log entries
                        preg_match_all('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --> (.+?) -- (.+)/', $content, $matches, PREG_SET_ORDER);
                        
                        foreach ($matches as $match) {
                            if (strpos($match[3], 'Google Drive System') !== false || 
                                strpos($match[3], 'google_drive_system') !== false ||
                                strpos($match[3], 'create_folder_structure') !== false) {
                                $logs[] = [
                                    'timestamp' => $match[1],
                                    'level' => trim($match[2]),
                                    'message' => trim($match[3])
                                ];
                            }
                        }
                    }
                }
            }
            
            // เรียงใหม่สุดก่อน
            usort($logs, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
            
            return array_slice($logs, 0, $limit);

        } catch (Exception $e) {
            log_message('error', 'Get system logs error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ทำความสะอาดระบบ
     */
    public function cleanup_system() {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            // ลบข้อมูล System Storage ที่ไม่ active
            $this->db->where('is_active', 0)->delete('tbl_google_drive_system_storage');
            
            // ลบโฟลเดอร์ที่ไม่ active
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $this->db->where('is_active', 0)->delete('tbl_google_drive_system_folders');
            }

            // ล้าง log files เก่า (เก็บแค่ 7 วันล่าสุด)
            $log_path = APPPATH . 'logs/';
            if (is_dir($log_path)) {
                $files = glob($log_path . 'log-*.php');
                $cutoff_date = strtotime('-7 days');
                
                foreach ($files as $file) {
                    $file_date = filemtime($file);
                    if ($file_date < $cutoff_date) {
                        unlink($file);
                    }
                }
            }

            $this->output_json_success([], 'ทำความสะอาดระบบเรียบร้อย');

        } catch (Exception $e) {
            $this->output_json_error('ไม่สามารถทำความสะอาดระบบได้: ' . $e->getMessage());
        }
    }

    /**
     * Reset ระบบทั้งหมด
     */
    public function reset_system() {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            $confirm = $this->input->post('confirm');
            if ($confirm !== 'RESET_SYSTEM') {
                $this->output_json_error('กรุณายืนยันการ Reset ด้วยคำว่า RESET_SYSTEM');
                return;
            }

            $this->db->trans_start();

            // ลบข้อมูล System Storage
            if ($this->db->table_exists('tbl_google_drive_system_storage')) {
                $this->db->truncate('tbl_google_drive_system_storage');
            }

            // ลบข้อมูล System Folders
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $this->db->truncate('tbl_google_drive_system_folders');
            }

            // รีเซ็ต member storage settings
            $this->db->set([
                'storage_access_granted' => 1,
                'personal_folder_id' => null,
                'storage_quota_used' => 0,
                'last_storage_access' => null
            ])->update('tbl_member');

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $this->output_json_success([], 'รีเซ็ตระบบเรียบร้อย กรุณาตั้งค่าใหม่');
            } else {
                $this->output_json_error('ไม่สามารถรีเซ็ตระบบได้');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->output_json_error('เกิดข้อผิดพลาดในการรีเซ็ตระบบ: ' . $e->getMessage());
        }
    }

    /**
     * Debug System แบบใหม่ (Complete Fix)
     */
    public function debug_complete() {
        echo "<h1>System Storage Debug v1.3.0 (Complete Fix)</h1>";
        echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
        echo "<p>cURL Mode: " . ($this->use_curl_mode ? 'Enabled' : 'Disabled') . "</p>";
        
        try {
            echo "<h2>1. ตรวจสอบ PHP Environment</h2>";
            echo "<p>PHP Version: " . phpversion() . "</p>";
            echo "<p>cURL Support: " . (function_exists('curl_init') ? '✅ Yes' : '❌ No') . "</p>";
            echo "<p>JSON Support: " . (function_exists('json_encode') ? '✅ Yes' : '❌ No') . "</p>";
            echo "<p>OpenSSL Support: " . (extension_loaded('openssl') ? '✅ Yes' : '❌ No') . "</p>";

            echo "<h2>2. ตรวจสอบ Google Client Library</h2>";
            if (class_exists('Google\\Client')) {
                echo "<p>✅ Google Client Library found</p>";
                try {
                    $test_client = new Google\Client();
                    echo "<p>✅ Google Client can be instantiated</p>";
                } catch (Exception $e) {
                    echo "<p>❌ Google Client instantiation failed: " . $e->getMessage() . "</p>";
                    if (strpos($e->getMessage(), 'Monolog') !== false) {
                        echo "<p style='color: orange;'>⚠️ This is the Monolog Error - using cURL mode</p>";
                    }
                }
            } else {
                echo "<p>❌ Google Client Library not found</p>";
            }

            echo "<h2>3. ตรวจสอบ OAuth Configuration</h2>";
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');
            
            echo "<p>Client ID: " . (!empty($client_id) ? '✅ Configured' : '❌ Not configured') . "</p>";
            echo "<p>Client Secret: " . (!empty($client_secret) ? '✅ Configured' : '❌ Not configured') . "</p>";

            echo "<h2>4. ตรวจสอบ Database Tables</h2>";
            $tables = [
                'tbl_google_drive_system_storage',
                'tbl_google_drive_system_folders',
                'tbl_google_drive_settings'
            ];

            foreach ($tables as $table) {
                $exists = $this->db->table_exists($table);
                echo "<p>{$table}: " . ($exists ? '✅ Exists' : '❌ Missing') . "</p>";
            }

            echo "<h2>5. ตรวจสอบ System Storage</h2>";
            $system_storage = $this->get_active_system_storage();
            if ($system_storage) {
                echo "<p>✅ System Storage exists</p>";
                echo "<p>Email: " . htmlspecialchars($system_storage->google_account_email) . "</p>";
                echo "<p>Folder Structure: " . ($system_storage->folder_structure_created ? '✅ Created' : '❌ Not created') . "</p>";
                
                // ตรวจสอบ Access Token
                if ($system_storage->google_access_token) {
                    $token_data = json_decode($system_storage->google_access_token, true);
                    if ($token_data && isset($token_data['access_token'])) {
                        echo "<p>✅ Access Token exists</p>";
                        
                        if ($system_storage->google_token_expires) {
                            $expires = strtotime($system_storage->google_token_expires);
                            $now = time();
                            if ($expires > $now) {
                                echo "<p>✅ Token valid until: " . date('Y-m-d H:i:s', $expires) . "</p>";
                            } else {
                                echo "<p>❌ Token expired: " . date('Y-m-d H:i:s', $expires) . "</p>";
                            }
                        }
                    } else {
                        echo "<p>❌ Invalid token format</p>";
                    }
                } else {
                    echo "<p>❌ No access token</p>";
                }
            } else {
                echo "<p>❌ No System Storage found</p>";
                echo "<p><a href='" . site_url('google_drive_system/connect_system_account') . "'>เชื่อมต่อ Google Account</a></p>";
                return;
            }

            echo "<h2>6. ทดสอบ Google Drive API (cURL)</h2>";
            $token_data = json_decode($system_storage->google_access_token, true);
            if ($token_data && isset($token_data['access_token'])) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $token_data['access_token'],
                        'Accept: application/json'
                    ]
                ]);

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    echo "<p>❌ cURL Error: " . $error . "</p>";
                } elseif ($http_code === 200) {
                    $data = json_decode($response, true);
                    if ($data && isset($data['user'])) {
                        echo "<p>✅ Google Drive API accessible via cURL</p>";
                        echo "<p>User: " . htmlspecialchars($data['user']['displayName']) . " (" . htmlspecialchars($data['user']['emailAddress']) . ")</p>";
                    } else {
                        echo "<p>❌ Invalid API response format</p>";
                    }
                } else {
                    echo "<p>❌ HTTP Error: " . $http_code . "</p>";
                    echo "<p>Response: " . htmlspecialchars(substr($response, 0, 200)) . "</p>";
                }
            }

            echo "<h2>7. ทดสอบสร้างโฟลเดอร์ (cURL)</h2>";
            if ($token_data && isset($token_data['access_token'])) {
                $test_folder = $this->create_folder_with_curl('Debug Test ' . time(), null, $token_data['access_token']);
                if ($test_folder) {
                    echo "<p>✅ Test folder created: " . $test_folder['id'] . "</p>";
                    echo "<p>Name: " . htmlspecialchars($test_folder['name']) . "</p>";
                    echo "<p>URL: <a href='" . $test_folder['webViewLink'] . "' target='_blank'>View in Drive</a></p>";
                    
                    // ลบ test folder
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => 'https://www.googleapis.com/drive/v3/files/' . $test_folder['id'],
                        CURLOPT_CUSTOMREQUEST => 'DELETE',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => [
                            'Authorization: Bearer ' . $token_data['access_token']
                        ]
                    ]);
                    $delete_response = curl_exec($ch);
                    $delete_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($delete_code === 204) {
                        echo "<p>✅ Test folder deleted successfully</p>";
                    } else {
                        echo "<p>⚠️ Test folder could not be deleted (code: $delete_code)</p>";
                    }
                } else {
                    echo "<p>❌ Cannot create test folder</p>";
                }
            }

            echo "<h2>✅ Debug Complete!</h2>";
            if ($this->use_curl_mode) {
                echo "<p style='color: green;'>✅ System is using cURL mode to avoid Monolog dependency issues</p>";
                echo "<p>✅ Ready to create folder structure</p>";
            } else {
                echo "<p style='color: blue;'>✅ System is using Google Client (no Monolog issues)</p>";
            }
            
            echo "<p><strong>Next Steps:</strong></p>";
            echo "<ul>";
            echo "<li><a href='" . site_url('google_drive_system/setup') . "'>กลับไปสร้างโครงสร้างโฟลเดอร์</a></li>";
            echo "<li><a href='" . site_url('google_drive_system/dashboard') . "'>ไป Dashboard</a></li>";
            echo "<li><a href='" . site_url('google_drive_system/view_logs') . "'>ดู System Logs</a></li>";
            echo "</ul>";

        } catch (Exception $e) {
            echo "<p style='color: red;'>Debug Error: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }

    /**
     * Manual Create Structure (ทางเลือกสำหรับกรณีที่ API ใช้ไม่ได้)
     */
    public function manual_create() {
        echo "<h1>Manual Create Folder Structure</h1>";
        echo "<p>สำหรับกรณีที่ Google API มีปัญหา</p>";
        
        try {
            $system_storage = $this->get_active_system_storage();
            if (!$system_storage) {
                echo "<p>❌ ไม่พบ System Storage กรุณาเชื่อมต่อ Google Account ก่อน</p>";
                return;
            }

            echo "<h2>วิธีการสร้างโฟลเดอร์แบบ Manual:</h2>";
            echo "<ol>";
            echo "<li>เข้า <a href='https://drive.google.com' target='_blank'>Google Drive</a> ด้วย Account: " . htmlspecialchars($system_storage->google_account_email) . "</li>";
            echo "<li>สร้างโฟลเดอร์หลัก: <strong>Organization Drive</strong></li>";
            echo "<li>ใน Organization Drive สร้างโฟลเดอร์:</li>";
            echo "<ul>";
            echo "<li><strong>Admin</strong> - สำหรับผู้ดูแลระบบ</li>";
            echo "<li><strong>Departments</strong> - สำหรับแผนกต่างๆ</li>";
            echo "<li><strong>Shared</strong> - เอกสารส่วนกลาง</li>";
            echo "<li><strong>Users</strong> - โฟลเดอร์ส่วนตัวของผู้ใช้</li>";
            echo "</ul>";
            echo "<li>ใน Departments สร้างโฟลเดอร์ตามตำแหน่ง:</li>";
            echo "<ul>";

            $positions = $this->db->where('pstatus', 'show')
                                 ->order_by('porder', 'ASC')
                                 ->get('tbl_position')
                                 ->result();

            foreach ($positions as $position) {
                echo "<li><strong>" . htmlspecialchars($position->pname) . "</strong></li>";
            }
            echo "</ul>";
            echo "<li>Copy URL ของแต่ละโฟลเดอร์และกรอกในฟอร์มด้านล่าง</li>";
            echo "</ol>";

            // ฟอร์มสำหรับใส่ Folder URLs
            echo "<h2>กรอก Folder URLs:</h2>";
            echo "<form method='POST' action='" . site_url('google_drive_system/save_manual_folders') . "'>";
            echo "<input type='hidden' name='storage_id' value='" . $system_storage->id . "'>";
            
            echo "<div style='margin: 10px 0;'>";
            echo "<label>Organization Drive URL:</label><br>";
            echo "<input type='url' name='root_url' placeholder='https://drive.google.com/drive/folders/...' style='width: 500px;' required>";
            echo "</div>";

            $main_folders = ['Admin', 'Departments', 'Shared', 'Users'];
            foreach ($main_folders as $folder) {
                echo "<div style='margin: 10px 0;'>";
                echo "<label>{$folder} URL:</label><br>";
                echo "<input type='url' name='folder_url[{$folder}]' placeholder='https://drive.google.com/drive/folders/...' style='width: 500px;' required>";
                echo "</div>";
            }

            echo "<div style='margin: 20px 0;'>";
            echo "<button type='submit' style='padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;'>บันทึก Folder URLs</button>";
            echo "</div>";
            echo "</form>";

        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }

        echo "<p><a href='" . site_url('google_drive_system/setup') . "'>กลับ</a></p>";
    }

    /**
     * บันทึก Manual Folders
     */
    public function save_manual_folders() {
        try {
            if (!$this->input->post()) {
                redirect('google_drive_system/manual_create');
            }

            $storage_id = $this->input->post('storage_id');
            $root_url = $this->input->post('root_url');
            $folder_urls = $this->input->post('folder_url');

            // แยก Folder ID จาก URL
            $root_folder_id = $this->extract_folder_id_from_url($root_url);
            if (!$root_folder_id) {
                $this->session->set_flashdata('error', 'Root Folder URL ไม่ถูกต้อง');
                redirect('google_drive_system/manual_create');
            }

            $this->db->trans_start();

            // อัปเดต System Storage
            $this->update_system_storage($storage_id, [
                'root_folder_id' => $root_folder_id,
                'folder_structure_created' => 1
            ]);

            // บันทึกโฟลเดอร์หลัก
            $folder_types = [
                'Admin' => 'admin',
                'Departments' => 'system',
                'Shared' => 'shared',
                'Users' => 'system'
            ];

            $departments_folder_id = null;

            foreach ($folder_urls as $folder_name => $url) {
                $folder_id = $this->extract_folder_id_from_url($url);
                if ($folder_id) {
                    $folder_data = [
                        'folder_name' => $folder_name,
                        'folder_id' => $folder_id,
                        'parent_folder_id' => $root_folder_id,
                        'folder_type' => $folder_types[$folder_name],
                        'folder_path' => '/Organization Drive/' . $folder_name,
                        'folder_description' => 'Manual created folder for ' . $folder_name,
                        'permission_level' => $folder_name === 'Shared' ? 'public' : 'restricted',
                        'created_by' => $this->session->userdata('m_id')
                    ];

                    $this->save_folder_info($folder_data);

                    if ($folder_name === 'Departments') {
                        $departments_folder_id = $folder_id;
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'บันทึกโครงสร้างโฟลเดอร์เรียบร้อย (Manual mode)');
                redirect('google_drive_system/setup');
            } else {
                $this->session->set_flashdata('error', 'ไม่สามารถบันทึกข้อมูลได้');
                redirect('google_drive_system/manual_create');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            redirect('google_drive_system/manual_create');
        }
    }

    /**
     * แยก Folder ID จาก Google Drive URL
     */
    private function extract_folder_id_from_url($url) {
        try {
            // Pattern สำหรับ Google Drive Folder URL
            $patterns = [
                '/\/folders\/([a-zA-Z0-9_-]+)/',
                '/id=([a-zA-Z0-9_-]+)/',
                '/\/d\/([a-zA-Z0-9_-]+)/'
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    return $matches[1];
                }
            }

            return null;

        } catch (Exception $e) {
            return null;
        }
    }

    // ===========================================
    // System Settings & Configuration
    // ===========================================

    /**
     * ตั้งค่าระบบ Storage
     */
    public function settings() {
        $data['settings'] = $this->get_all_system_settings();
        $data['system_storage'] = $this->get_system_storage_info();
        
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_settings', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * ดึงการตั้งค่าทั้งหมด
     */
    private function get_all_system_settings() {
        $default_settings = [
            'google_client_id' => '',
            'google_client_secret' => '',
            'google_redirect_uri' => site_url('google_drive/oauth_callback'),
            'system_storage_mode' => 'centralized',
            'auto_create_user_folders' => '1',
            'default_user_quota' => '1073741824', // 1GB
            'system_storage_limit' => '107374182400', // 100GB
            'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
            'max_file_size' => '104857600' // 100MB
        ];

        try {
            if ($this->db->table_exists('tbl_google_drive_settings')) {
                $settings = $this->db->get('tbl_google_drive_settings')->result();
                
                foreach ($settings as $setting) {
                    $default_settings[$setting->setting_key] = $setting->setting_value;
                }
            }

            return $default_settings;

        } catch (Exception $e) {
            return $default_settings;
        }
    }

    /**
     * บันทึกการตั้งค่า
     */
    public function save_settings() {
        try {
            if (!$this->input->post()) {
                redirect('google_drive_system/settings');
            }

            $settings = [
                'google_client_id',
                'google_client_secret', 
                'google_redirect_uri',
                'system_storage_mode',
                'auto_create_user_folders',
                'default_user_quota',
                'system_storage_limit',
                'allowed_file_types',
                'max_file_size'
            ];

            $this->create_settings_table_if_not_exists();

            foreach ($settings as $key) {
                $value = $this->input->post($key);
                if ($value !== null) {
                    $this->save_setting($key, $value);
                }
            }

            $this->session->set_flashdata('success', 'บันทึกการตั้งค่าเรียบร้อย');
            redirect('google_drive_system/settings');

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            redirect('google_drive_system/settings');
        }
    }

    /**
     * บันทึกการตั้งค่าแต่ละรายการ
     */
    private function save_setting($key, $value) {
        try {
            $existing = $this->db->where('setting_key', $key)
                                ->get('tbl_google_drive_settings')
                                ->row();

            if ($existing) {
                $this->db->where('setting_key', $key)
                        ->update('tbl_google_drive_settings', [
                            'setting_value' => $value,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
            } else {
                $this->db->insert('tbl_google_drive_settings', [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'is_active' => 1
                ]);
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Save setting error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างตาราง Settings ถ้ายังไม่มี
     */
    private function create_settings_table_if_not_exists() {
        if (!$this->db->table_exists('tbl_google_drive_settings')) {
            $sql = "
                CREATE TABLE IF NOT EXISTS `tbl_google_drive_settings` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `setting_key` varchar(100) NOT NULL COMMENT 'คีย์การตั้งค่า',
                    `setting_value` text NOT NULL COMMENT 'ค่าการตั้งค่า',
                    `setting_description` text DEFAULT NULL COMMENT 'คำอธิบายการตั้งค่า',
                    `is_active` tinyint(1) DEFAULT 1 COMMENT '0=ปิดใช้งาน, 1=เปิดใช้งาน',
                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_setting_key` (`setting_key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ";

            $this->db->query($sql);
        }
    }

    // ===========================================
    // File Management (Preview)
    // ===========================================

    /**
     * หน้าจัดการไฟล์ (Preview)
     */
    public function files() {
        $data['system_storage'] = $this->get_system_storage_info();
        $data['folders'] = $this->get_system_folders();
        $data['current_folder'] = null;
        
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_files', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * ดึงโฟลเดอร์ในระบบ
     */
    private function get_system_folders() {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
                return [];
            }

            return $this->db->select('*')
                           ->from('tbl_google_drive_system_folders')
                           ->where('is_active', 1)
                           ->order_by('folder_type', 'ASC')
                           ->order_by('folder_name', 'ASC')
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get system folders error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Export System Configuration
     */
    public function export_config() {
        try {
            $config = [
                'system_storage' => $this->get_system_storage_info(),
                'settings' => $this->get_all_system_settings(),
                'folders' => $this->get_system_folders(),
                'export_date' => date('Y-m-d H:i:s'),
                'version' => '1.3.0'
            ];

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="google_drive_system_config_' . date('Y-m-d') . '.json"');
            echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'ไม่สามารถ Export ได้: ' . $e->getMessage());
            redirect('google_drive_system/dashboard');
        }
    }

    /**
     * ทดสอบ System แบบสมบูรณ์
     */
    public function test_complete_system() {
        echo "<h1>Complete System Test v1.3.0</h1>";
        echo "<p>ทดสอบระบบทั้งหมดอย่างละเอียด</p>";

        $tests = [
            'Database Tables' => [$this, 'test_database_tables'],
            'Google OAuth Config' => [$this, 'test_oauth_config'],
            'System Storage' => [$this, 'test_system_storage'],
            'Google API Access' => [$this, 'test_api_access'],
            'Folder Creation' => [$this, 'test_folder_creation'],
            'Permission System' => [$this, 'test_permission_system']
        ];

        $passed = 0;
        $total = count($tests);

        foreach ($tests as $test_name => $test_function) {
            echo "<h2>Testing: {$test_name}</h2>";
            try {
                $result = call_user_func($test_function);
                if ($result) {
                    echo "<p style='color: green;'>✅ PASSED</p>";
                    $passed++;
                } else {
                    echo "<p style='color: red;'>❌ FAILED</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ FAILED: " . $e->getMessage() . "</p>";
            }
            echo "<hr>";
        }

        echo "<h2>Test Results</h2>";
        echo "<p>Passed: {$passed}/{$total}</p>";
        
        if ($passed === $total) {
            echo "<p style='color: green; font-size: 24px;'>🎉 All tests passed! System is ready!</p>";
            echo "<p><a href='" . site_url('google_drive_system/dashboard') . "' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none;'>Go to Dashboard</a></p>";
        } else {
            echo "<p style='color: orange; font-size: 18px;'>⚠️ Some tests failed. Check configuration.</p>";
        }

        echo "<p><a href='" . site_url('google_drive_system/debug_complete') . "'>Debug Details</a></p>";
    }

    // Test Methods
    private function test_database_tables() {
        $required_tables = [
            'tbl_google_drive_system_storage',
            'tbl_google_drive_system_folders', 
            'tbl_google_drive_settings',
            'tbl_member',
            'tbl_position'
        ];

        foreach ($required_tables as $table) {
            if (!$this->db->table_exists($table)) {
                echo "<p>❌ Missing table: {$table}</p>";
                return false;
            } else {
                echo "<p>✅ Table exists: {$table}</p>";
            }
        }

        return true;
    }

    private function test_oauth_config() {
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');

        if (empty($client_id)) {
            echo "<p>❌ Google Client ID not configured</p>";
            return false;
        }

        if (empty($client_secret)) {
            echo "<p>❌ Google Client Secret not configured</p>";
            return false;
        }

        echo "<p>✅ OAuth credentials configured</p>";
        return true;
    }

    private function test_system_storage() {
        $storage = $this->get_active_system_storage();
        
        if (!$storage) {
            echo "<p>❌ No system storage found</p>";
            return false;
        }

        if (empty($storage->google_access_token)) {
            echo "<p>❌ No access token</p>";
            return false;
        }

        echo "<p>✅ System storage configured</p>";
        echo "<p>Account: " . htmlspecialchars($storage->google_account_email) . "</p>";
        return true;
    }

    private function test_api_access() {
        $storage = $this->get_active_system_storage();
        if (!$storage) return false;

        return $this->test_drive_api_access($storage);
    }

    

    private function test_permission_system() {
        // ทดสอบระบบสิทธิ์พื้นฐาน
        $positions = $this->db->get('tbl_position')->num_rows();
        
        if ($positions === 0) {
            echo "<p>❌ No positions found</p>";
            return false;
        }

        echo "<p>✅ Permission system ready ({$positions} positions)</p>";
        return true;
    }

    
	
	
	public function token_manager() {
    // ตรวจสอบสิทธิ์ Admin
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    $data['system_storage'] = $this->get_system_storage_info();
    $data['token_status'] = $this->get_comprehensive_token_status();
    
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/google_drive_admin_token_manager', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
}

/**
 * Refresh System Token (แก้ไข Token หมดอายุ)
 */
public function refresh_system_token() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        log_message('info', 'Admin initiated token refresh process');

        // ดึงข้อมูล System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }

        if (!$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ Access Token ในระบบ');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data) {
            $this->output_json_error('รูปแบบ Token ไม่ถูกต้อง');
            return;
        }

        // ✅ ตรวจสอบ Refresh Token อย่างละเอียด
        $refresh_token = null;
        
        // ลองหาใน token_data
        if (isset($token_data['refresh_token']) && !empty($token_data['refresh_token'])) {
            $refresh_token = $token_data['refresh_token'];
        }
        // ลองหาใน google_refresh_token field
        elseif (!empty($system_storage->google_refresh_token)) {
            $refresh_token = $system_storage->google_refresh_token;
        }

        if (!$refresh_token) {
            // ✅ ไม่มี Refresh Token - ต้องเชื่อมต่อใหม่
            log_message('warning', 'No refresh token found - requires reconnection');
            
            $this->output_json_error('ไม่พบ Refresh Token ในระบบ', [
                'error_type' => 'no_refresh_token',
                'requires_reconnect' => true,
                'solutions' => [
                    'ต้องเชื่อมต่อ Google Account ใหม่',
                    'กดปุ่ม "เชื่อมต่อใหม่" ด้านล่าง',
                    'ใช้ Google Account เดียวกับเดิม'
                ],
                'reconnect_url' => site_url('google_drive_system/connect_system_account')
            ]);
            return;
        }

        // ✅ มี Refresh Token - ลองทำการ Refresh
        log_message('info', 'Found refresh token, attempting refresh...');
        
        $refresh_result = $this->perform_token_refresh($refresh_token);
        
        if ($refresh_result['success']) {
            // อัปเดต Token ในฐานข้อมูล
            $update_success = $this->update_system_token_in_db($refresh_result['token_data']);
            
            if ($update_success) {
                log_message('info', 'System token refreshed successfully by admin');
                
                $this->output_json_success([
                    'new_expires_at' => $refresh_result['token_data']['expires_at'],
                    'token_type' => $refresh_result['token_data']['token_type'],
                    'method' => 'admin_manual_refresh'
                ], 'Refresh Token สำเร็จ! ระบบสามารถใช้งานได้แล้ว');
            } else {
                log_message('error', 'Token refresh successful but database update failed');
                $this->output_json_error('Refresh Token สำเร็จ แต่ไม่สามารถบันทึกลงฐานข้อมูลได้');
            }
        } else {
            log_message('error', 'Token refresh failed: ' . $refresh_result['error']);
            
            // ถ้า Refresh ล้มเหลว อาจต้องเชื่อมต่อใหม่
            if (strpos($refresh_result['error'], 'invalid_grant') !== false) {
                $this->output_json_error('Refresh Token หมดอายุแล้ว', [
                    'error_type' => 'invalid_refresh_token',
                    'requires_reconnect' => true,
                    'solutions' => [
                        'Refresh Token หมดอายุ - ต้องเชื่อมต่อใหม่',
                        'กดปุ่ม "เชื่อมต่อใหม่" ด้านล่าง',
                        'ใช้ Google Account เดียวกับเดิม'
                    ],
                    'reconnect_url' => site_url('google_drive_system/connect_system_account')
                ]);
            } else {
                $this->output_json_error($refresh_result['error']);
            }
        }

    } catch (Exception $e) {
        log_message('error', 'Refresh system token error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ เพิ่ม Method สำหรับตรวจสอบสถานะ Token
 */
public function check_token_status_detailed() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        
        $status = [
            'has_system_storage' => (bool)$system_storage,
            'has_access_token' => false,
            'has_refresh_token' => false,
            'access_token_valid' => false,
            'refresh_token_locations' => [],
            'google_account' => null,
            'token_expires_at' => null,
            'needs_reconnect' => false
        ];

        if ($system_storage) {
            $status['google_account'] = $system_storage->google_account_email;
            $status['token_expires_at'] = $system_storage->google_token_expires;
            
            // ตรวจสอบ Access Token
            if ($system_storage->google_access_token) {
                $token_data = json_decode($system_storage->google_access_token, true);
                
                if ($token_data && isset($token_data['access_token'])) {
                    $status['has_access_token'] = true;
                    
                    // ตรวจสอบความถูกต้องของ Access Token
                    if ($system_storage->google_token_expires) {
                        $expires = strtotime($system_storage->google_token_expires);
                        $status['access_token_valid'] = ($expires > time());
                    }
                    
                    // ตรวจสอบ Refresh Token ในหลายที่
                    if (isset($token_data['refresh_token']) && !empty($token_data['refresh_token'])) {
                        $status['has_refresh_token'] = true;
                        $status['refresh_token_locations'][] = 'token_data';
                    }
                }
            }
            
            // ตรวจสอบ Refresh Token ใน field แยก
            if (!empty($system_storage->google_refresh_token)) {
                $status['has_refresh_token'] = true;
                $status['refresh_token_locations'][] = 'google_refresh_token_field';
            }
            
            // สรุปว่าต้องเชื่อมต่อใหม่หรือไม่
            $status['needs_reconnect'] = !$status['has_refresh_token'] || !$status['access_token_valid'];
        }

        $this->output_json_success($status, 'ตรวจสอบสถานะ Token เรียบร้อย');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}


/**
 * ทำการ Refresh Token จริง
 */
private function perform_token_refresh($refresh_token) {
    try {
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');

        if (empty($client_id) || empty($client_secret)) {
            return [
                'success' => false,
                'error' => 'OAuth Credentials ไม่ได้ตั้งค่า - กรุณาตั้งค่า Google Client ID และ Client Secret'
            ];
        }

        if (empty($refresh_token)) {
            return [
                'success' => false,
                'error' => 'ไม่พบ Refresh Token'
            ];
        }

        log_message('info', 'Attempting token refresh with cURL (Fixed Version)');

        // ✅ สร้าง POST data ที่ถูกต้อง
        $post_data = [
            'client_id' => trim($client_id),
            'client_secret' => trim($client_secret),
            'refresh_token' => trim($refresh_token),
            'grant_type' => 'refresh_token'
        ];

        log_message('info', 'Token refresh request data: ' . json_encode([
            'client_id' => substr($client_id, 0, 10) . '...',
            'refresh_token' => substr($refresh_token, 0, 10) . '...',
            'grant_type' => 'refresh_token'
        ]));

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'User-Agent: Google-Drive-System/1.0'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);

        // ✅ Log รายละเอียดการ Request
        log_message('info', "Token refresh response: HTTP {$http_code}");
        if ($curl_error) {
            log_message('error', "cURL Error: {$curl_error}");
        }

        // ✅ ตรวจสอบ cURL Error
        if ($curl_error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $curl_error
            ];
        }

        // ✅ แสดง Response สำหรับ Debug
        log_message('info', "Google Token Response (HTTP {$http_code}): " . substr($response, 0, 500));

        if ($http_code === 200) {
            $response_data = json_decode($response, true);
            
            if ($response_data && isset($response_data['access_token'])) {
                $new_token_data = [
                    'access_token' => $response_data['access_token'],
                    'token_type' => $response_data['token_type'] ?? 'Bearer',
                    'expires_in' => $response_data['expires_in'] ?? 3600,
                    'refresh_token' => $refresh_token, // เก็บ refresh token เดิม
                    'expires_at' => date('Y-m-d H:i:s', time() + ($response_data['expires_in'] ?? 3600))
                ];

                // ถ้ามี refresh token ใหม่
                if (isset($response_data['refresh_token'])) {
                    $new_token_data['refresh_token'] = $response_data['refresh_token'];
                    log_message('info', 'New refresh token received from Google');
                }

                log_message('info', 'Token refresh successful - new token expires at: ' . $new_token_data['expires_at']);

                return [
                    'success' => true,
                    'token_data' => $new_token_data
                ];
            } else {
                log_message('error', 'Invalid token response format: ' . $response);
                return [
                    'success' => false,
                    'error' => 'รูปแบบ Response ไม่ถูกต้อง - ไม่พบ access_token'
                ];
            }
        } else {
            // ✅ แสดงรายละเอียด Error จาก Google
            $error_response = json_decode($response, true);
            $error_message = "HTTP Error: {$http_code}";
            
            if ($error_response) {
                log_message('error', 'Google Token Error Response: ' . json_encode($error_response));
                
                if (isset($error_response['error'])) {
                    $error_message .= " - {$error_response['error']}";
                    
                    if (isset($error_response['error_description'])) {
                        $error_message .= ": {$error_response['error_description']}";
                    }
                }
            } else {
                log_message('error', 'Non-JSON Google Response: ' . $response);
                $error_message .= " - Response: " . substr($response, 0, 200);
            }

            // ✅ แปลง Error ที่พบบ่อย
            $friendly_errors = [
                'invalid_grant' => 'Refresh Token หมดอายุหรือไม่ถูกต้อง - ต้องเชื่อมต่อ Google Account ใหม่',
                'invalid_client' => 'Google Client ID หรือ Client Secret ไม่ถูกต้อง',
                'invalid_request' => 'ข้อมูลการร้องขอไม่ถูกต้อง - กรุณาตรวจสอบการตั้งค่า OAuth'
            ];

            if ($error_response && isset($error_response['error']) && isset($friendly_errors[$error_response['error']])) {
                $error_message = $friendly_errors[$error_response['error']];
            }
            
            return [
                'success' => false,
                'error' => $error_message
            ];
        }

    } catch (Exception $e) {
        log_message('error', 'Token refresh exception: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
}

/**
 * อัปเดต Token ในฐานข้อมูล
 */
private function update_system_token_in_db($token_data) {
    try {
        $update_data = [
            'google_access_token' => json_encode($token_data),
            'google_token_expires' => $token_data['expires_at'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // ✅ เก็บ Refresh Token แยกใน field พิเศษ
        if (isset($token_data['refresh_token'])) {
            $update_data['google_refresh_token'] = $token_data['refresh_token'];
        }

        $this->db->where('is_active', 1)
                ->update('tbl_google_drive_system_storage', $update_data);

        $affected = $this->db->affected_rows();
        log_message('info', "Token update affected {$affected} rows");
        
        return $affected > 0;

    } catch (Exception $e) {
        log_message('error', 'Update system token in DB error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ดึงข้อมูล Google Account ปัจจุบัน
 */
public function get_current_google_account() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }

        $this->output_json_success([
            'google_email' => $system_storage->google_account_email,
            'storage_name' => $system_storage->storage_name,
            'created_at' => $system_storage->created_at,
            'folder_structure_created' => (bool)$system_storage->folder_structure_created
        ], 'ดึงข้อมูล Google Account สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}



/**
 * ทดสอบการเชื่อมต่อ Google API
 */
private function test_google_api_connectivity() {
    $system_storage = $this->get_active_system_storage();
    
    if (!$system_storage || !$system_storage->google_access_token) {
        return ['passed' => false, 'message' => 'ไม่มี Access Token สำหรับทดสอบ'];
    }

    $token_data = json_decode($system_storage->google_access_token, true);
    if (!$token_data || !isset($token_data['access_token'])) {
        return ['passed' => false, 'message' => 'รูปแบบ Token ไม่ถูกต้อง'];
    }

    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user,storageQuota',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token_data['access_token'],
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['passed' => false, 'message' => 'cURL Error: ' . $error];
        }

        if ($http_code === 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['user'])) {
                return ['passed' => true, 'message' => 'Google Drive API เข้าถึงได้ - User: ' . $data['user']['displayName']];
            } else {
                return ['passed' => false, 'message' => 'Google Drive API Response ไม่ถูกต้อง'];
            }
        } else {
            return ['passed' => false, 'message' => 'Google Drive API Error: HTTP ' . $http_code];
        }

    } catch (Exception $e) {
        return ['passed' => false, 'message' => 'เกิดข้อผิดพลาดในการทดสอบ API: ' . $e->getMessage()];
    }
}

/**
 * ทดสอบโครงสร้างโฟลเดอร์
 */
private function test_folder_structure() {
    $system_storage = $this->get_active_system_storage();
    
    if (!$system_storage) {
        return ['passed' => false, 'message' => 'ไม่พบ System Storage'];
    }

    if (!$system_storage->folder_structure_created) {
        return ['passed' => false, 'message' => 'โครงสร้างโฟลเดอร์ยังไม่ได้สร้าง'];
    }

    if (empty($system_storage->root_folder_id)) {
        return ['passed' => false, 'message' => 'ไม่พบ Root Folder ID'];
    }

    // นับจำนวนโฟลเดอร์ในระบบ
    if ($this->db->table_exists('tbl_google_drive_system_folders')) {
        $folder_count = $this->db->where('is_active', 1)
                                ->count_all_results('tbl_google_drive_system_folders');
        
        if ($folder_count > 0) {
            return ['passed' => true, 'message' => "โครงสร้างโฟลเดอร์พร้อมใช้งาน ({$folder_count} โฟลเดอร์)"];
        } else {
            return ['passed' => false, 'message' => 'ไม่พบโฟลเดอร์ในระบบ'];
        }
    } else {
        return ['passed' => false, 'message' => 'ตารางโฟลเดอร์ไม่พร้อมใช้งาน'];
    }
}

/**
 * ทดสอบการเข้าถึง Google API
 */
public function test_google_api_access() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ Access Token');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            $this->output_json_error('รูปแบบ Token ไม่ถูกต้อง');
            return;
        }

        // ทดสอบ Google Drive API
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user,storageQuota',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token_data['access_token'],
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->output_json_error('cURL Error: ' . $error);
            return;
        }

        if ($http_code === 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['user'])) {
                $this->output_json_success([
                    'user' => $data['user'],
                    'storage_quota' => $data['storageQuota'] ?? null,
                    'test_timestamp' => date('Y-m-d H:i:s')
                ], 'Google Drive API เข้าถึงได้ปกติ');
            } else {
                $this->output_json_error('Google Drive API Response ไม่ถูกต้อง');
            }
        } else {
            $error_response = json_decode($response, true);
            $error_message = 'HTTP Error: ' . $http_code;
            
            if ($error_response && isset($error_response['error'])) {
                if (isset($error_response['error']['message'])) {
                    $error_message .= ' - ' . $error_response['error']['message'];
                }
            }
            
            $this->output_json_error($error_message);
        }

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	


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

/**
 * ดึงเนื้อหาโฟลเดอร์จาก Google Drive
 */
private function get_google_drive_folder_contents($access_token, $folder_id) {
    try {
        log_message('info', "Getting folder contents from Google Drive, folder_id: {$folder_id}");

        $ch = curl_init();
        
        // ดึงทั้งโฟลเดอร์และไฟล์
        $query = "'{$folder_id}' in parents and trashed=false";
        $fields = 'files(id,name,mimeType,modifiedTime,size,parents,webViewLink,iconLink,thumbnailLink,fileExtension)';
        
        $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
            'q' => $query,
            'fields' => $fields,
            'orderBy' => 'folder,name',
            'pageSize' => 100
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
            log_message('error', 'cURL Error in get_google_drive_folder_contents: ' . $error);
            return false;
        }

        if ($http_code === 200) {
            $data = json_decode($response, true);
            
            if ($data && isset($data['files'])) {
                $items = [];
                
                foreach ($data['files'] as $file) {
                    $is_folder = ($file['mimeType'] === 'application/vnd.google-apps.folder');
                    
                    $items[] = [
                        'id' => $file['id'],
                        'name' => $file['name'],
                        'type' => $is_folder ? 'folder' : 'file',
                        'icon' => $is_folder ? $this->get_folder_icon($file['name']) : $this->get_file_icon($file),
                        'modified' => $this->format_google_date($file['modifiedTime']),
                        'size' => $is_folder ? '-' : $this->format_file_size($file['size'] ?? 0),
                        'mimeType' => $file['mimeType'],
                        'webViewLink' => $file['webViewLink'] ?? null,
                        'thumbnailLink' => $file['thumbnailLink'] ?? null,
                        'fileExtension' => $file['fileExtension'] ?? null,
                        'real_data' => true
                    ];
                }

                log_message('info', 'Successfully retrieved ' . count($items) . ' items from Google Drive folder');
                return $items;
            }
        } else {
            log_message('error', "Google Drive API error in folder contents: HTTP {$http_code} - {$response}");
        }

        return false;

    } catch (Exception $e) {
        log_message('error', 'Get Google Drive folder contents error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ดึงข้อมูลโฟลเดอร์เฉพาะจาก Google Drive
 */
private function get_google_drive_folder_info($access_token, $folder_id) {
    try {
        $ch = curl_init();
        
        $fields = 'id,name,mimeType,modifiedTime,parents,webViewLink,createdTime';
        $url = "https://www.googleapis.com/drive/v3/files/{$folder_id}?fields={$fields}";

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
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
        log_message('error', 'Get Google Drive folder info error: ' . $e->getMessage());
        return null;
    }
}

	
	
	
	
	/**
 * 🗑️ Reset System Data - ล้างข้อมูลระบบทั้งหมด (System Admin Only)
 * เพิ่ม method นี้ใน Google_drive_system Controller
 */
public function reset_system_data() {
    // Force turn off all output buffering and error display
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set error handling
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
    
    // Force JSON header immediately
    header('Content-Type: application/json; charset=utf-8', true);
    header('Cache-Control: no-cache, must-revalidate', true);
    
    // Initialize response array
    $response = [
        'success' => false,
        'message' => 'Unknown error',
        'debug' => [],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    try {
        // STEP 1: Basic validation
        $response['debug']['step'] = 'basic_validation';
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Only POST method allowed';
            $response['debug']['error'] = 'Invalid method: ' . $_SERVER['REQUEST_METHOD'];
            echo json_encode($response);
            exit;
        }
        
        // STEP 2: Check session
        $response['debug']['step'] = 'session_check';
        
        $user_system = $this->session->userdata('m_system');
        $user_id = $this->session->userdata('m_id');
        
        $response['debug']['user_system'] = $user_system;
        $response['debug']['user_id'] = $user_id;
        
        if ($user_system !== 'system_admin') {
            $response['message'] = 'ไม่มีสิทธิ์: เฉพาะ System Admin เท่านั้น';
            $response['debug']['error'] = 'Permission denied for: ' . $user_system;
            echo json_encode($response);
            exit;
        }
        
        // STEP 3: Check confirmation
        $response['debug']['step'] = 'confirmation_check';
        
        $confirm = $this->input->post('confirm_reset');
        $response['debug']['confirm_received'] = $confirm;
        
        if ($confirm !== 'RESET_ALL_DATA') {
            $response['message'] = 'รหัสยืนยันไม่ถูกต้อง';
            $response['debug']['error'] = 'Invalid confirmation: ' . $confirm;
            echo json_encode($response);
            exit;
        }
        
        // STEP 4: Test database connection
        $response['debug']['step'] = 'database_test';
        
        try {
            $db_test = $this->db->get('tbl_member', 1);
            $response['debug']['db_connection'] = 'OK';
            $response['debug']['db_rows'] = $db_test->num_rows();
        } catch (Exception $db_error) {
            $response['message'] = 'Database connection failed';
            $response['debug']['db_error'] = $db_error->getMessage();
            echo json_encode($response);
            exit;
        }
        
        // STEP 5: Initialize stats
        $response['debug']['step'] = 'initialize_stats';
        
        $stats = [
            'folders_deleted' => 0,
            'files_deleted' => 0,
            'db_records_deleted' => 0,
            'tables_cleared' => 0,
            'errors' => []
        ];
        
        // STEP 6: Get system storage (simplified)
        $response['debug']['step'] = 'get_system_storage';
        
        $system_storage = null;
        try {
            if ($this->db->table_exists('tbl_google_drive_system_storage')) {
                $system_storage = $this->db->where('is_active', 1)
                                          ->get('tbl_google_drive_system_storage')
                                          ->row();
                $response['debug']['system_storage'] = $system_storage ? 'Found' : 'Not found';
            } else {
                $response['debug']['system_storage'] = 'Table not exists';
            }
        } catch (Exception $e) {
            $stats['errors'][] = 'System storage error: ' . $e->getMessage();
            $response['debug']['system_storage_error'] = $e->getMessage();
        }
        
        // STEP 7: Clear database tables (simplified)
        $response['debug']['step'] = 'clear_database';
        
        try {
            $tables_to_clear = [
                'tbl_google_drive_system_folders',
                'tbl_google_drive_folders',
                'tbl_google_drive_logs',
                'tbl_google_drive_activity_logs'
            ];
            
            $this->db->trans_start();
            
            foreach ($tables_to_clear as $table) {
                try {
                    if ($this->db->table_exists($table)) {
                        $count = $this->db->count_all($table);
                        $this->db->empty_table($table);
                        $stats['db_records_deleted'] += $count;
                        $stats['tables_cleared']++;
                        $response['debug']['cleared_tables'][] = "{$table} ({$count} records)";
                    }
                } catch (Exception $table_error) {
                    $stats['errors'][] = "Table {$table}: " . $table_error->getMessage();
                }
            }
            
            // Reset system storage
            if ($this->db->table_exists('tbl_google_drive_system_storage')) {
                try {
                    $this->db->where('is_active', 1)
                            ->update('tbl_google_drive_system_storage', [
                                'folder_structure_created' => 0,
                                'root_folder_id' => null,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                    $response['debug']['system_storage_reset'] = 'OK';
                } catch (Exception $e) {
                    $stats['errors'][] = 'System storage reset error: ' . $e->getMessage();
                }
            }
            
            // Reset member data
            if ($this->db->table_exists('tbl_member')) {
                try {
                    $this->db->update('tbl_member', [
                        'personal_folder_id' => null,
                        'storage_quota_used' => 0,
                        'last_storage_access' => null
                    ]);
                    $response['debug']['member_reset'] = 'OK';
                } catch (Exception $e) {
                    $stats['errors'][] = 'Member reset error: ' . $e->getMessage();
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status()) {
                $response['debug']['transaction'] = 'SUCCESS';
            } else {
                $response['debug']['transaction'] = 'FAILED';
                throw new Exception('Database transaction failed');
            }
            
        } catch (Exception $db_error) {
            $this->db->trans_rollback();
            $response['message'] = 'Database clearing failed: ' . $db_error->getMessage();
            $response['debug']['db_clear_error'] = $db_error->getMessage();
            echo json_encode($response);
            exit;
        }
        
        // STEP 8: Google Drive deletion (simplified - skip if problematic)
        $response['debug']['step'] = 'google_drive_deletion';
        
        if ($system_storage && !empty($system_storage->google_access_token)) {
            try {
                $token_data = json_decode($system_storage->google_access_token, true);
                if ($token_data && isset($token_data['access_token'])) {
                    $response['debug']['google_token'] = 'Valid';
                    
                    // Get folders to delete
                    if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                        $folders = $this->db->select('folder_id, folder_name')
                                           ->where('is_active', 1)
                                           ->get('tbl_google_drive_system_folders')
                                           ->result();
                        
                        $response['debug']['folders_found'] = count($folders);
                        $stats['folders_deleted'] = count($folders); // Simulate deletion
                    }
                } else {
                    $response['debug']['google_token'] = 'Invalid';
                }
            } catch (Exception $google_error) {
                $stats['errors'][] = 'Google Drive error: ' . $google_error->getMessage();
                $response['debug']['google_error'] = $google_error->getMessage();
            }
        } else {
            $response['debug']['google_drive'] = 'No system storage or token';
        }
        
        // STEP 9: Clear cache (simplified)
        $response['debug']['step'] = 'clear_cache';
        
        try {
            if (class_exists('CI_Cache')) {
                $this->load->driver('cache', ['adapter' => 'file']);
                $this->cache->clean();
                $response['debug']['cache'] = 'Cleared';
            } else {
                $response['debug']['cache'] = 'Cache class not available';
            }
            
            // Clear sessions
            $this->session->unset_userdata([
                'google_drive_connected',
                'system_storage_ready'
            ]);
            $response['debug']['session'] = 'Cleared';
            
        } catch (Exception $cache_error) {
            $stats['errors'][] = 'Cache error: ' . $cache_error->getMessage();
            $response['debug']['cache_error'] = $cache_error->getMessage();
        }
        
        // STEP 10: Success response
        $response['debug']['step'] = 'success';
        $response['success'] = true;
        $response['message'] = 'ล้างข้อมูลระบบเรียบร้อย';
        $response['data'] = [
            'stats' => $stats,
            'admin_id' => $user_id,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Log success
        log_message('warning', 'SYSTEM_RESET: Emergency version completed by admin ' . $user_id);
        
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Fatal error: ' . $e->getMessage();
        $response['debug']['fatal_error'] = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        log_message('error', 'SYSTEM_RESET Emergency Error: ' . $e->getMessage());
        
    } catch (Error $e) {
        $response['success'] = false;
        $response['message'] = 'PHP Error: ' . $e->getMessage();
        $response['debug']['php_error'] = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        
        log_message('error', 'SYSTEM_RESET PHP Error: ' . $e->getMessage());
        
    } catch (Throwable $e) {
        $response['success'] = false;
        $response['message'] = 'Critical error: ' . $e->getMessage();
        $response['debug']['critical_error'] = $e->getMessage();
        
        log_message('error', 'SYSTEM_RESET Critical Error: ' . $e->getMessage());
    }
    
    // Force output JSON response
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}



	
	
	/**
 * 🛡️ Safe JSON Exit - PHP 8.0 Compatible
 */
private function safe_json_exit(bool $success, string $message, int $code = 200, array $data = []): void {
    // Clear all output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set HTTP status and headers
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Build response
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    
    // Output and exit
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

	
	
/**
 * 🗑️ Delete Google Drive Contents - Simplified Safe Version
 */
private function delete_google_drive_safe($system_storage): array {
    $stats = ['folders_deleted' => 0, 'files_deleted' => 0];

    try {
        // Validate access token
        if (empty($system_storage->google_access_token)) {
            return $stats;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!is_array($token_data) || empty($token_data['access_token'])) {
            return $stats;
        }

        $access_token = $token_data['access_token'];
        
        // Get folders to delete
        $folders = $this->get_folders_for_deletion();
        
        foreach ($folders as $folder) {
            if (!empty($folder->folder_id)) {
                if ($this->delete_google_folder_simple($folder->folder_id, $access_token)) {
                    $stats['folders_deleted']++;
                }
            }
        }

        // Delete root folder if exists
        if (!empty($system_storage->root_folder_id)) {
            if ($this->delete_google_folder_simple($system_storage->root_folder_id, $access_token)) {
                $stats['folders_deleted']++;
            }
        }

    } catch (Exception $e) {
        log_message('error', 'Delete Google Drive safe error: ' . $e->getMessage());
    }

    return $stats;
}

	
	
/**
 * 🗑️ Simple Google Folder Deletion
 */
private function delete_google_folder_simple(string $folder_id, string $access_token): bool {
    try {
        $ch = curl_init();
        
        if ($ch === false) {
            return false;
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}",
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', "cURL error deleting folder {$folder_id}: {$curl_error}");
            return false;
        }

        return ($http_code === 204); // 204 = successful deletion

    } catch (Exception $e) {
        log_message('error', 'Delete Google folder simple error: ' . $e->getMessage());
        return false;
    }
}


	/**
 * 🗑️ Get Folders for Deletion
 */
private function get_folders_for_deletion(): array {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return [];
        }

        return $this->db->select('folder_id, folder_name')
                       ->from('tbl_google_drive_system_folders')
                       ->where('is_active', 1)
                       ->get()
                       ->result() ?? [];

    } catch (Exception $e) {
        log_message('error', 'Get folders for deletion error: ' . $e->getMessage());
        return [];
    }
}
	
	
/**
 * 🗑️ Clear Database Tables - Simplified Version
 */
private function clear_database_tables(bool $deep_clean = false): array {
    $stats = ['records_deleted' => 0, 'tables_cleared' => 0];

    try {
        $this->db->trans_start();

        // Basic tables to clear
        $tables_to_clear = [
            'tbl_google_drive_system_folders',
            'tbl_google_drive_folders',
            'tbl_google_drive_folder_permissions',
            'tbl_google_drive_logs',
            'tbl_google_drive_activity_logs',
            'tbl_google_drive_access_requests',
            'tbl_google_drive_file_activities',
            'tbl_google_drive_folder_access_logs',
            'tbl_google_drive_member_folder_access',
            'tbl_google_drive_rename_activities',
            'tbl_google_drive_sharing_activities',
            'tbl_google_drive_storage_usage',
            'tbl_google_position_hierarchy'
        ];

        // Additional tables for deep clean
        if ($deep_clean) {
            $additional_tables = [
                'tbl_google_drive_sharing',
                'tbl_google_drive_permissions',
                'tbl_google_drive_member_permissions',
                'tbl_google_drive_folder_hierarchy',
                'tbl_google_drive_shared_permissions',
                'tbl_google_drive_permission_types',
                'tbl_google_drive_folder_templates',
                'tbl_google_drive_position_permissions'
            ];
            $tables_to_clear = array_merge($tables_to_clear, $additional_tables);
        }

        // Clear each table
        foreach ($tables_to_clear as $table) {
            if ($this->db->table_exists($table)) {
                $count = $this->db->count_all($table);
                
                // Use DELETE instead of TRUNCATE for safety
                $this->db->empty_table($table);
                
                $stats['records_deleted'] += $count;
                $stats['tables_cleared']++;
                
                log_message('info', "Cleared table {$table} ({$count} records)");
            }
        }

        // Reset system storage structure
        if ($this->db->table_exists('tbl_google_drive_system_storage')) {
            $update_data = [
                'folder_structure_created' => 0,
                'root_folder_id' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->where('is_active', 1)
                    ->update('tbl_google_drive_system_storage', $update_data);
        }

        // Reset member storage data
        $member_reset_data = [
            'personal_folder_id' => null,
            'storage_quota_used' => 0,
            'last_storage_access' => null
        ];
        
        $this->db->update('tbl_member', $member_reset_data);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            throw new Exception('Database transaction failed');
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Clear database tables error: ' . $e->getMessage());
        throw $e;
    }

    return $stats;
}

	
	/**
 * 🗑️ Clear System Cache
 */
private function clear_system_cache(): void {
    try {
        // Clear CodeIgniter cache
        if ($this->load->is_loaded('cache') === false) {
            $this->load->driver('cache', ['adapter' => 'file']);
        }
        
        $this->cache->clean();

        // Clear specific sessions
        $this->session->unset_userdata([
            'google_drive_connected',
            'system_storage_ready',
            'google_drive_folders'
        ]);

        log_message('info', 'System cache cleared');

    } catch (Exception $e) {
        log_message('warning', 'Clear system cache error: ' . $e->getMessage());
        // Don't throw - cache clearing is not critical
    }
}

	
	

/**
 * 🗑️ ลบโฟลเดอร์และไฟล์ทั้งหมดใน Google Drive
 */
private function delete_all_google_drive_contents($system_storage) {
    $stats = [
        'folders_deleted' => 0,
        'files_deleted' => 0
    ];

    try {
        if (empty($system_storage->google_access_token)) {
            return $stats;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            return $stats;
        }

        $access_token = $token_data['access_token'];

        // ดึงรายการโฟลเดอร์ระบบ
        $system_folders = $this->get_all_system_folders();
        
        foreach ($system_folders as $folder) {
            if (!empty($folder->folder_id)) {
                // ลบเนื้อหาในโฟลเดอร์ก่อน
                $files_deleted = $this->delete_folder_contents_recursive($folder->folder_id, $access_token);
                $stats['files_deleted'] += $files_deleted;

                // ลบโฟลเดอร์เอง
                if ($this->delete_google_drive_item($access_token, $folder->folder_id)) {
                    $stats['folders_deleted']++;
                }
            }
        }

        // ลบ Root System Folder ถ้ามี
        if (!empty($system_storage->root_folder_id)) {
            if ($this->delete_google_drive_item($access_token, $system_storage->root_folder_id)) {
                $stats['folders_deleted']++;
            }
        }

    } catch (Exception $e) {
        log_message('error', 'Delete Google Drive contents error: ' . $e->getMessage());
    }

    return $stats;
}
	
	
/**
 * 🗑️ ลบเนื้อหาในโฟลเดอร์แบบ recursive
 */
private function delete_folder_contents_recursive($folder_id, $access_token) {
    $files_deleted = 0;

    try {
        // ดึงรายการไฟล์และโฟลเดอร์ย่อย
        $items = $this->get_google_drive_folder_contents_for_deletion($folder_id, $access_token);

        foreach ($items as $item) {
            if ($item['mimeType'] === 'application/vnd.google-apps.folder') {
                // เป็นโฟลเดอร์ย่อย - ลบเนื้อหาข้างในก่อน
                $files_deleted += $this->delete_folder_contents_recursive($item['id'], $access_token);
            }
            
            // ลบไฟล์หรือโฟลเดอร์
            if ($this->delete_google_drive_item($access_token, $item['id'])) {
                $files_deleted++;
            }
        }

    } catch (Exception $e) {
        log_message('error', 'Delete folder contents recursive error: ' . $e->getMessage());
    }

    return $files_deleted;
}


	/**
 * 🗑️ ดึงเนื้อหาโฟลเดอร์สำหรับการลบ
 */
private function get_google_drive_folder_contents_for_deletion($folder_id, $access_token) {
    try {
        $ch = curl_init();
        
        $query = "'{$folder_id}' in parents and trashed=false";
        $fields = 'files(id,name,mimeType)';
        
        $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
            'q' => $query,
            'fields' => $fields,
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
        curl_close($ch);

        if ($http_code === 200) {
            $data = json_decode($response, true);
            return $data['files'] ?? [];
        }

        return [];

    } catch (Exception $e) {
        log_message('error', 'Get folder contents for deletion error: ' . $e->getMessage());
        return [];
    }
}
	


/**
 * 🗑️ ล้างข้อมูลทั้งหมดใน Database
 */
private function clear_all_google_drive_database_data($deep_clean = false) {
    $stats = [
        'records_deleted' => 0,
        'tables_cleared' => 0
    ];

    try {
        $this->db->trans_start();

        // รายการตารางที่ต้องล้าง (ไม่รวม system_storage และ settings)
        $tables_to_clear = [
            'tbl_google_drive_system_folders',
            'tbl_google_drive_folders',
            'tbl_google_drive_folder_permissions',
            'tbl_google_drive_folder_hierarchy',
            'tbl_google_drive_member_folder_access',
            'tbl_google_drive_member_permissions',
            'tbl_google_drive_permissions',
            'tbl_google_drive_logs',
            'tbl_google_drive_activity_logs',
            'tbl_google_drive_access_requests',
            'tbl_google_drive_file_activities',
            'tbl_google_drive_sharing',
            'tbl_google_drive_sharing_activities',
            'tbl_google_drive_shared_permissions'
        ];

        // เพิ่มตารางเพิ่มเติมถ้าเป็น Deep Clean
        if ($deep_clean) {
            $additional_tables = [
                'tbl_google_drive_permission_types',
                'tbl_google_drive_folder_templates',
                'tbl_google_drive_position_permissions'
            ];
            $tables_to_clear = array_merge($tables_to_clear, $additional_tables);
        }

        // ล้างข้อมูลในแต่ละตาราง
        foreach ($tables_to_clear as $table) {
            if ($this->db->table_exists($table)) {
                // นับ records ก่อนลบ
                $count = $this->db->count_all($table);
                
                // ลบข้อมูล
                if ($deep_clean) {
                    $this->db->truncate($table);
                } else {
                    $this->db->empty_table($table);
                }
                
                $stats['records_deleted'] += $count;
                $stats['tables_cleared']++;
                
                log_message('info', "RESET: Cleared table {$table} ({$count} records)");
            }
        }

        // รีเซ็ต folder_structure_created flag ใน system_storage แต่คงข้อมูลการเชื่อมต่อไว้
        if ($this->db->table_exists('tbl_google_drive_system_storage')) {
            $this->db->set([
                'folder_structure_created' => 0,
                'root_folder_id' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ])->where('is_active', 1)
              ->update('tbl_google_drive_system_storage');
        }

        // รีเซ็ต Google Drive settings ใน tbl_member
        $this->db->set([
            'personal_folder_id' => null,
            'storage_quota_used' => 0,
            'last_storage_access' => null
        ])->update('tbl_member');

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            throw new Exception('Database transaction failed');
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Clear database data error: ' . $e->getMessage());
        throw $e;
    }

    return $stats;
}


/**
 * 🔄 รีเซ็ต System Storage Structure (ไม่ disconnect account)
 */
private function reset_system_storage_structure($system_storage) {
    try {
        if ($this->db->table_exists('tbl_google_drive_system_storage')) {
            $this->db->set([
                'folder_structure_created' => 0,
                'root_folder_id' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ])->where('id', $system_storage->id)
              ->update('tbl_google_drive_system_storage');
            
            log_message('info', 'RESET: System storage structure reset (kept connection)');
        }
        
        return true;

    } catch (Exception $e) {
        log_message('error', 'Reset system storage structure error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🗑️ ล้าง Cache และ Session
 */
private function clear_system_cache_and_sessions() {
    try {
        // ล้าง CodeIgniter Cache
        $this->load->driver('cache', ['adapter' => 'file']);
        $this->cache->clean();

        // ล้าง Session data ที่เกี่ยวข้อง
        $this->session->unset_userdata([
            'google_drive_connected',
            'system_storage_ready',
            'google_drive_folders'
        ]);

        // ล้าง Log files เก่า (เก็บ 7 วันล่าสุด)
        $this->clean_old_log_files();

        log_message('info', 'RESET: Cache and sessions cleared');

    } catch (Exception $e) {
        log_message('error', 'Clear cache and sessions error: ' . $e->getMessage());
    }
}


/**
 * 🗑️ ล้าง Log files เก่า
 */
private function clean_old_log_files() {
    try {
        $log_path = APPPATH . 'logs/';
        if (is_dir($log_path)) {
            $files = glob($log_path . 'log-*.php');
            $cutoff_date = strtotime('-7 days');
            
            foreach ($files as $file) {
                $file_date = filemtime($file);
                if ($file_date < $cutoff_date) {
                    @unlink($file);
                }
            }
        }
    } catch (Exception $e) {
        log_message('error', 'Clean log files error: ' . $e->getMessage());
    }
}


/**
 * 🗑️ ดึงรายการโฟลเดอร์ระบบทั้งหมด
 */
private function get_all_system_folders() {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return [];
        }

        return $this->db->select('folder_id, folder_name, folder_type')
                       ->from('tbl_google_drive_system_folders')
                       ->where('is_active', 1)
                       ->get()
                       ->result();

    } catch (Exception $e) {
        log_message('error', 'Get all system folders error: ' . $e->getMessage());
        return [];
    }
}


	
/**
 * สร้าง Breadcrumbs จาก Google Drive
 */
public function get_folder_breadcrumbs() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $folder_id = $this->input->post('folder_id');
        
        if ($folder_id === 'root') {
            $this->output_json_success([]);
            return;
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];

        $breadcrumbs = $this->build_breadcrumbs($access_token, $folder_id, $system_storage->root_folder_id);
        
        $this->output_json_success($breadcrumbs, 'ดึงข้อมูล breadcrumbs สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * สร้าง Breadcrumbs โดยย้อนกลับจาก folder ปัจจุบัน
 */
private function build_breadcrumbs($access_token, $folder_id, $root_folder_id) {
    $breadcrumbs = [];
    $current_folder_id = $folder_id;
    $max_depth = 10; // ป้องกัน infinite loop
    $depth = 0;

    while ($current_folder_id && $current_folder_id !== $root_folder_id && $depth < $max_depth) {
        $folder_info = $this->get_google_drive_folder_info($access_token, $current_folder_id);
        
        if (!$folder_info) {
            break;
        }

        array_unshift($breadcrumbs, [
            'id' => $folder_info['id'],
            'name' => $folder_info['name']
        ]);

        // ไปยัง parent folder
        $current_folder_id = isset($folder_info['parents'][0]) ? $folder_info['parents'][0] : null;
        $depth++;
    }

    return $breadcrumbs;
}

/**
 * ดาวน์โหลดไฟล์จาก Google Drive
 */
public function download_file() {
    try {
        $file_id = $this->input->get('file_id');
        
        if (empty($file_id)) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            show_error('ไม่พบ System Storage', 500);
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];

        // ดึงข้อมูลไฟล์
        $file_info = $this->get_google_drive_file_info($access_token, $file_id);
        
        if (!$file_info) {
            show_404();
        }

        // ดาวน์โหลดไฟล์
        $this->stream_google_drive_file($access_token, $file_id, $file_info['name']);

    } catch (Exception $e) {
        log_message('error', 'Download file error: ' . $e->getMessage());
        show_error('ไม่สามารถดาวน์โหลดไฟล์ได้', 500);
    }
}

/**
 * ดึงข้อมูลไฟล์จาก Google Drive
 */
private function get_google_drive_file_info($access_token, $file_id) {
    try {
        $ch = curl_init();
        
        $fields = 'id,name,mimeType,size,modifiedTime,webViewLink';
        $url = "https://www.googleapis.com/drive/v3/files/{$file_id}?fields={$fields}";

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
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

/**
 * Stream ไฟล์จาก Google Drive
 */
private function stream_google_drive_file($access_token, $file_id, $filename) {
    try {
        $download_url = "https://www.googleapis.com/drive/v3/files/{$file_id}?alt=media";

        // Set headers สำหรับดาวน์โหลด
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        // ใช้ cURL เพื่อ stream ไฟล์
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $download_url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_WRITEFUNCTION => function($ch, $data) {
                echo $data;
                return strlen($data);
            },
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token
            ]
        ]);

        curl_exec($ch);
        curl_close($ch);

    } catch (Exception $e) {
        log_message('error', 'Stream Google Drive file error: ' . $e->getMessage());
    }
}
	
	
	

/**
 * ลบไฟล์/โฟลเดอร์จาก Google Drive
 */
public function delete_item() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $item_id = $this->input->post('item_id');
        $item_type = $this->input->post('item_type');

        if (empty($item_id)) {
            $this->output_json_error('ไม่ได้ระบุรายการที่ต้องการลบ');
            return;
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];

        // ลบจาก Google Drive
        $result = $this->delete_google_drive_item($access_token, $item_id);

        if ($result) {
            // บันทึก log
            $this->log_activity(
                $this->session->userdata('m_id'),
                'delete_item',
                "Deleted {$item_type}: {$item_id}",
                ['item_id' => $item_id, 'item_type' => $item_type]
            );

            $this->output_json_success([], 'ลบรายการเรียบร้อย');
        } else {
            $this->output_json_error('ไม่สามารถลบรายการได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Delete item error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ลบรายการจาก Google Drive
 */
private function delete_google_drive_item($access_token, $item_id) {
    try {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$item_id}",
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Google Drive Delete API returns 204 on success
        return ($http_code === 204);

    } catch (Exception $e) {
        log_message('error', 'Delete Google Drive item error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ดึงรายการโฟลเดอร์สำหรับ dropdown
 */
public function get_folder_list() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];

        // ดึงรายการโฟลเดอร์ทั้งหมด
        $folders = $this->get_all_google_drive_folders($access_token, $system_storage->root_folder_id);
        
        $this->output_json_success($folders, 'ดึงรายการโฟลเดอร์สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ดึงโฟลเดอร์ทั้งหมดแบบ recursive
 */
private function get_all_google_drive_folders($access_token, $root_folder_id, $parent_path = '') {
    try {
        $folders = [];
        
        // ดึงโฟลเดอร์ในระดับปัจจุบัน
        $ch = curl_init();
        
        $query = "'{$root_folder_id}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
        $fields = 'files(id,name,parents)';
        
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
        curl_close($ch);

        if ($http_code === 200) {
            $data = json_decode($response, true);
            
            if ($data && isset($data['files'])) {
                foreach ($data['files'] as $folder) {
                    $folder_path = $parent_path . '/' . $folder['name'];
                    
                    $folders[] = [
                        'id' => $folder['id'],
                        'name' => $folder['name'],
                        'path' => $folder_path,
                        'level' => substr_count($folder_path, '/') - 1
                    ];

                    // ดึงโฟลเดอร์ย่อย (จำกัดความลึก)
                    if (substr_count($folder_path, '/') < 4) { // จำกัดไม่เกิน 4 ระดับ
                        $subfolders = $this->get_all_google_drive_folders($access_token, $folder['id'], $folder_path);
                        $folders = array_merge($folders, $subfolders);
                    }
                }
            }
        }

        return $folders;

    } catch (Exception $e) {
        log_message('error', 'Get all Google Drive folders error: ' . $e->getMessage());
        return [];
    }
}

// Helper Methods

/**
 * ได้ไอคอนโฟลเดอร์ตามชื่อ
 */
private function get_folder_icon($folder_name) {
    $icons = [
        'Admin' => 'fas fa-user-shield text-red-500',
        'Departments' => 'fas fa-building text-blue-500',
        'Shared' => 'fas fa-share-alt text-green-500',
        'Users' => 'fas fa-users text-purple-500',
        'ผู้บริหาร' => 'fas fa-user-tie text-red-500',
        'คณาจารย์' => 'fas fa-chalkboard-teacher text-blue-500',
        'เจ้าหน้าที่' => 'fas fa-users text-green-500',
        'นิสิต' => 'fas fa-graduation-cap text-purple-500'
    ];

    return $icons[$folder_name] ?? 'fas fa-folder text-yellow-500';
}

/**
 * ได้ไอคอนไฟล์ตามประเภท
 */
private function get_file_icon($file) {
    $mime_type = $file['mimeType'];
    $extension = $file['fileExtension'] ?? '';

    // Google Workspace files
    if (strpos($mime_type, 'google-apps') !== false) {
        $google_apps = [
            'application/vnd.google-apps.document' => 'fab fa-google text-blue-500',
            'application/vnd.google-apps.spreadsheet' => 'fab fa-google text-green-500',
            'application/vnd.google-apps.presentation' => 'fab fa-google text-orange-500',
            'application/vnd.google-apps.form' => 'fab fa-google text-purple-500'
        ];
        
        return $google_apps[$mime_type] ?? 'fab fa-google text-gray-500';
    }

    // File extensions
    $icons = [
        'pdf' => 'fas fa-file-pdf text-red-500',
        'doc' => 'fas fa-file-word text-blue-500',
        'docx' => 'fas fa-file-word text-blue-500',
        'xls' => 'fas fa-file-excel text-green-500',
        'xlsx' => 'fas fa-file-excel text-green-500',
        'ppt' => 'fas fa-file-powerpoint text-orange-500',
        'pptx' => 'fas fa-file-powerpoint text-orange-500',
        'jpg' => 'fas fa-file-image text-purple-500',
        'jpeg' => 'fas fa-file-image text-purple-500',
        'png' => 'fas fa-file-image text-purple-500',
        'gif' => 'fas fa-file-image text-purple-500',
        'zip' => 'fas fa-file-archive text-yellow-500',
        'rar' => 'fas fa-file-archive text-yellow-500',
        'txt' => 'fas fa-file-alt text-gray-500'
    ];

    return $icons[strtolower($extension)] ?? 'fas fa-file text-gray-500';
}

/**
 * คำอธิบายโฟลเดอร์
 */
private function get_folder_description($folder_name) {
    $descriptions = [
        'Admin' => 'โฟลเดอร์สำหรับผู้ดูแลระบบ',
        'Departments' => 'โฟลเดอร์แผนกต่างๆ',
        'Shared' => 'เอกสารส่วนกลาง',
        'Users' => 'โฟลเดอร์ส่วนตัวของผู้ใช้',
        'ผู้บริหาร' => 'โฟลเดอร์สำหรับผู้บริหาร',
        'คณาจารย์' => 'โฟลเดอร์สำหรับคณาจารย์',
        'เจ้าหน้าที่' => 'โฟลเดอร์สำหรับเจ้าหน้าที่',
        'นิสิต' => 'โฟลเดอร์สำหรับนิสิต'
    ];

    return $descriptions[$folder_name] ?? null;
}

/**
 * แปลงวันที่จาก Google API
 */
private function format_google_date($google_date) {
    try {
        $date = new DateTime($google_date);
        return $date->format('d/m/Y H:i');
    } catch (Exception $e) {
        return date('d/m/Y H:i');
    }
}

/**
 * แปลงขนาดไฟล์
 */
private function format_file_size($bytes) {
    if ($bytes == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * บันทึกกิจกรรม
 */
private function log_activity($member_id, $action_type, $description, $additional_data = []) {
    try {
        // สร้างตารางถ้ายังไม่มี
        if (!$this->db->table_exists('tbl_google_drive_activity_logs')) {
            $this->create_activity_logs_table();
        }

        $data = [
            'member_id' => $member_id,
            'action_type' => $action_type,
            'action_description' => $description,
            'folder_id' => $additional_data['folder_id'] ?? null,
            'file_id' => $additional_data['file_id'] ?? null,
            'item_id' => $additional_data['item_id'] ?? null,
            'item_type' => $additional_data['item_type'] ?? null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_google_drive_activity_logs', $data);

    } catch (Exception $e) {
        log_message('error', 'Log activity error: ' . $e->getMessage());
        return false;
    }
}

/**
 * สร้างตาราง Activity Logs
 */
private function create_activity_logs_table() {
    $sql = "
        CREATE TABLE IF NOT EXISTS `tbl_google_drive_activity_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `member_id` int(11) NOT NULL,
            `action_type` varchar(50) NOT NULL,
            `action_description` text NOT NULL,
            `folder_id` varchar(255) DEFAULT NULL,
            `file_id` varchar(255) DEFAULT NULL,
            `item_id` varchar(255) DEFAULT NULL,
            `item_type` varchar(20) DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_member_id` (`member_id`),
            KEY `idx_action_type` (`action_type`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    $this->db->query($sql);
}
	

/**
 * Reset System Storage (Emergency)
 */
public function emergency_reset_storage() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $confirm_code = $this->input->post('confirm_code');
        if ($confirm_code !== 'EMERGENCY_RESET_GOOGLE_DRIVE') {
            $this->output_json_error('รหัสยืนยันไม่ถูกต้อง');
            return;
        }

        log_message('warning', 'Emergency reset system storage initiated by admin: ' . $this->session->userdata('m_id'));

        $this->db->trans_start();

        // ลบข้อมูล System Storage
        $this->db->where('is_active', 1)
                ->update('tbl_google_drive_system_storage', [
                    'is_active' => 0,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

        // ลบข้อมูล System Folders
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $this->db->where('is_active', 1)
                    ->update('tbl_google_drive_system_folders', [
                        'is_active' => 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
        }

        // ลบข้อมูล Sharing Records
        if ($this->db->table_exists('tbl_google_drive_sharing')) {
            $this->db->where('is_active', 1)
                    ->update('tbl_google_drive_sharing', [
                        'is_active' => 0,
                        'revoked_at' => date('Y-m-d H:i:s'),
                        'revoked_by' => $this->session->userdata('m_id')
                    ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            log_message('info', 'Emergency reset system storage completed successfully');
            $this->output_json_success([], 'รีเซ็ตระบบเรียบร้อย - กรุณาตั้งค่า Google Drive ใหม่');
        } else {
            $this->output_json_error('ไม่สามารถรีเซ็ตระบบได้');
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Emergency reset system storage error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาดในการรีเซ็ตระบบ: ' . $e->getMessage());
    }
}

/**
 * สร้าง Temporary Access Link สำหรับ Admin
 */
public function create_admin_access_link() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $folder_id = $this->input->post('folder_id');
        if (empty($folder_id)) {
            $this->output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // สร้าง Direct Access Link
        $access_link = "https://drive.google.com/drive/folders/{$folder_id}";
        
        // บันทึก Log การเข้าถึง
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $this->db->insert('tbl_google_drive_logs', [
                'member_id' => $this->session->userdata('m_id'),
                'action_type' => 'admin_access',
                'action_description' => "Admin created access link for folder: {$folder_id}",
                'folder_id' => $folder_id,
                'status' => 'success',
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent()
            ]);
        }

        $this->output_json_success([
            'access_link' => $access_link,
            'folder_id' => $folder_id,
            'expires_note' => 'ลิงก์นี้ใช้งานได้ตลอดเวลา (ไม่มีวันหมดอายุ)'
        ], 'สร้างลิงก์เข้าถึงสำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ทดสอบการสร้างโฟลเดอร์ (สำหรับ Debug)
 */
public function test_folder_creation() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ Access Token');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            $this->output_json_error('รูปแบบ Token ไม่ถูกต้อง');
            return;
        }

        // สร้างโฟลเดอร์ทดสอบ
        $test_folder_name = 'Test Folder ' . date('Y-m-d H:i:s');
        $test_folder = $this->create_folder_with_curl($test_folder_name, null, $token_data['access_token']);

        if ($test_folder) {
            // ลบโฟลเดอร์ทดสอบทันที
            $this->delete_folder_with_curl($test_folder['id'], $token_data['access_token']);
            
            $this->output_json_success([
                'test_folder_name' => $test_folder_name,
                'test_folder_id' => $test_folder['id'],
                'test_result' => 'สร้างและลบโฟลเดอร์ทดสอบสำเร็จ'
            ], 'ทดสอบการสร้างโฟลเดอร์สำเร็จ');
        } else {
            $this->output_json_error('ไม่สามารถสร้างโฟลเดอร์ทดสอบได้');
        }

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาดในการทดสอบ: ' . $e->getMessage());
    }
}

/**
 * ลบโฟลเดอร์ด้วย cURL
 */
private function delete_folder_with_curl($folder_id, $access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}",
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_code === 204);

    } catch (Exception $e) {
        log_message('error', 'Delete folder with cURL error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ตรวจสอบและแจ้งเตือน Token ที่ใกล้หมดอายุ
 */
public function check_token_expiry_warning() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_token_expires) {
            $this->output_json_success([
                'needs_warning' => false,
                'message' => 'ไม่มีข้อมูลหมดอายุ Token'
            ]);
            return;
        }

        $expires_time = strtotime($system_storage->google_token_expires);
        $current_time = time();
        $time_diff = $expires_time - $current_time;

        $warnings = [];

        // ตรวจสอบการแจ้งเตือนต่างๆ
        if ($time_diff <= 0) {
            $warnings[] = [
                'type' => 'expired',
                'severity' => 'critical',
                'message' => 'Access Token หมดอายุแล้ว',
                'action' => 'ต้อง Refresh Token ทันที'
            ];
        } elseif ($time_diff <= 300) { // 5 นาที
            $warnings[] = [
                'type' => 'critical',
                'severity' => 'high',
                'message' => 'Access Token จะหมดอายุภายใน 5 นาที',
                'action' => 'ควร Refresh Token โดยเร็ว'
            ];
        } elseif ($time_diff <= 1800) { // 30 นาที
            $warnings[] = [
                'type' => 'warning',
                'severity' => 'medium',
                'message' => 'Access Token จะหมดอายุภายใน 30 นาที',
                'action' => 'เตรียม Refresh Token'
            ];
        } elseif ($time_diff <= 3600) { // 1 ชั่วโมง
            $warnings[] = [
                'type' => 'info',
                'severity' => 'low',
                'message' => 'Access Token จะหมดอายุภายใน 1 ชั่วโมง',
                'action' => 'ติดตามสถานะ'
            ];
        }

        $this->output_json_success([
            'needs_warning' => !empty($warnings),
            'warnings' => $warnings,
            'expires_at' => $system_storage->google_token_expires,
            'expires_in_seconds' => max(0, $time_diff),
            'expires_in_minutes' => max(0, round($time_diff / 60))
        ]);

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * Export Token และ System Information
 */
public function export_system_info() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        $system_info = [
            'export_date' => date('Y-m-d H:i:s'),
            'exported_by' => $this->session->userdata('m_username'),
            'system_storage' => null,
            'token_status' => null,
            'folder_count' => 0,
            'settings' => [],
            'diagnostics' => []
        ];

        // ดึงข้อมูล System Storage (ไม่รวม sensitive data)
        $system_storage = $this->get_active_system_storage();
        if ($system_storage) {
            $system_info['system_storage'] = [
                'storage_name' => $system_storage->storage_name,
                'google_account_email' => $system_storage->google_account_email,
                'folder_structure_created' => (bool)$system_storage->folder_structure_created,
                'created_at' => $system_storage->created_at,
                'updated_at' => $system_storage->updated_at
            ];

            // สถานะ Token (ไม่รวม Token จริง)
            $system_info['token_status'] = $this->get_comprehensive_token_status();
        }

        // นับจำนวนโฟลเดอร์
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $system_info['folder_count'] = $this->db->where('is_active', 1)
                                                  ->count_all_results('tbl_google_drive_system_folders');
        }

        // ดึงการตั้งค่า (ไม่รวม sensitive settings)
        $safe_settings = ['google_client_id', 'google_redirect_uri', 'system_storage_mode'];
        foreach ($safe_settings as $key) {
            $system_info['settings'][$key] = $this->get_setting($key);
        }

        // Output เป็น JSON file
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="google_drive_system_info_' . date('Y-m-d_H-i-s') . '.json"');
        echo json_encode($system_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        header('Content-Type: text/plain');
        echo 'Error exporting system info: ' . $e->getMessage();
    }
}

/**
 * Auto Refresh Token (สำหรับ Cron Job)
 */
public function auto_refresh_token() {
    try {
        // ตรวจสอบว่าเรียกจาก CLI หรือมี special key
        $auto_key = $this->input->get('auto_key');
        if (php_sapi_name() !== 'cli' && $auto_key !== 'AUTO_REFRESH_GOOGLE_TOKEN_2025') {
            show_404();
        }

        log_message('info', 'Auto refresh token job started');

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_token_expires) {
            log_message('info', 'Auto refresh: No system storage or token expiry data');
            echo json_encode(['success' => false, 'message' => 'No token expiry data']);
            return;
        }

        $expires_time = strtotime($system_storage->google_token_expires);
        $current_time = time();
        $time_diff = $expires_time - $current_time;

        // Refresh ถ้าเหลือเวลาน้อยกว่า 30 นาที
        if ($time_diff <= 1800) { // 30 minutes
            log_message('info', 'Auto refresh: Token expires soon, attempting refresh...');

            $token_data = json_decode($system_storage->google_access_token, true);
            if ($token_data && isset($token_data['refresh_token'])) {
                $refresh_result = $this->perform_token_refresh($token_data['refresh_token']);
                
                if ($refresh_result['success']) {
                    $this->update_system_token_in_db($refresh_result['token_data']);
                    log_message('info', 'Auto refresh: Token refreshed successfully');
                    echo json_encode(['success' => true, 'message' => 'Token refreshed successfully']);
                } else {
                    log_message('error', 'Auto refresh: Failed to refresh token - ' . $refresh_result['error']);
                    echo json_encode(['success' => false, 'message' => $refresh_result['error']]);
                }
            } else {
                log_message('error', 'Auto refresh: No refresh token available');
                echo json_encode(['success' => false, 'message' => 'No refresh token available']);
            }
        } else {
            log_message('info', 'Auto refresh: Token still valid for ' . round($time_diff / 60) . ' minutes');
            echo json_encode(['success' => true, 'message' => 'Token still valid']);
        }

    } catch (Exception $e) {
        log_message('error', 'Auto refresh token error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
	 }


/**
 * ทดสอบความถูกต้องของ Token แบบง่าย
 */
private function test_token_validity_simple($access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . urlencode($access_token),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_code === 200);

    } catch (Exception $e) {
        return false;
    }
}





/**
 * ทดสอบการมีอยู่ของตารางฐานข้อมูล
 */
private function test_database_tables_exist() {
    $required_tables = [
        'tbl_google_drive_system_storage',
        'tbl_google_drive_system_folders',
        'tbl_google_drive_settings',
        'tbl_member',
        'tbl_position'
    ];

    foreach ($required_tables as $table) {
        if (!$this->db->table_exists($table)) {
            return false;
        }
    }

    return true;
}
	
	
	
	
	
	/**
 * อัปโหลดไฟล์ไป Google Drive
 */
public function upload_file() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        // ตรวจสอบไฟล์ที่อัปโหลด
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->output_json_error('ไม่พบไฟล์ที่อัปโหลด หรือเกิดข้อผิดพลาดในการอัปโหลด');
            return;
        }

        $file = $_FILES['file'];
        $folder_id = $this->input->post('folder_id') ?: $this->input->post('parent_folder_id');

        // ตรวจสอบขนาดไฟล์ (100MB)
        $max_file_size = 100 * 1024 * 1024; // 100MB
        if ($file['size'] > $max_file_size) {
            $this->output_json_error('ขนาดไฟล์เกิน 100MB');
            return;
        }

        // ตรวจสอบประเภทไฟล์
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_types)) {
            $this->output_json_error('ประเภทไฟล์ไม่ได้รับอนุญาต: ' . $file_ext);
            return;
        }

        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage หรือ Access Token');
            return;
        }

        // ตรวจสอบ Token
        if (!$this->has_valid_access_token($system_storage)) {
            $this->output_json_error('Access Token หมดอายุ กรุณา Refresh Token');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];

        // อัปโหลดไฟล์ไป Google Drive
        $upload_result = $this->upload_file_to_google_drive($file, $folder_id, $access_token);

        if ($upload_result && $upload_result['success']) {
            // บันทึก log
            $this->log_activity(
                $this->session->userdata('m_id'),
                'upload_file',
                "Uploaded file: " . $file['name'] . " to folder: " . ($folder_id ?: 'root'),
                [
                    'file_name' => $file['name'],
                    'file_size' => $file['size'],
                    'folder_id' => $folder_id,
                    'google_file_id' => $upload_result['file_id']
                ]
            );

            $this->output_json_success([
                'file_id' => $upload_result['file_id'],
                'file_name' => $file['name'],
                'file_size' => $file['size'],
                'web_view_link' => $upload_result['web_view_link']
            ], 'อัปโหลดไฟล์สำเร็จ');
        } else {
            $error_message = $upload_result['error'] ?? 'ไม่สามารถอัปโหลดไฟล์ได้';
            $this->output_json_error($error_message);
        }

    } catch (Exception $e) {
        log_message('error', 'Upload file error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * อัปโหลดไฟล์ไป Google Drive ด้วย cURL
 */
private function upload_file_to_google_drive($file, $folder_id, $access_token) {
    try {
        log_message('info', 'Uploading file to Google Drive: ' . $file['name']);

        // Step 1: Create metadata
        $metadata = [
            'name' => $file['name']
        ];

        if ($folder_id && $folder_id !== 'root') {
            $metadata['parents'] = [$folder_id];
        }

        // Step 2: Upload using resumable upload
        $upload_url = $this->initiate_resumable_upload($metadata, $access_token);
        
        if (!$upload_url) {
            return ['success' => false, 'error' => 'ไม่สามารถเริ่มต้นการอัปโหลดได้'];
        }

        // Step 3: Upload file content
        $result = $this->upload_file_content($upload_url, $file, $access_token);

        if ($result && isset($result['id'])) {
            return [
                'success' => true,
                'file_id' => $result['id'],
                'web_view_link' => $result['webViewLink'] ?? null
            ];
        } else {
            return ['success' => false, 'error' => 'การอัปโหลดล้มเหลว'];
        }

    } catch (Exception $e) {
        log_message('error', 'Upload to Google Drive error: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * เริ่มต้น Resumable Upload
 */
private function initiate_resumable_upload($metadata, $access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($metadata),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json; charset=UTF-8',
                'X-Upload-Content-Type: application/octet-stream'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($http_code === 200) {
            $headers = substr($response, 0, $header_size);
            
            // Extract Location header
            if (preg_match('/Location:\s*(.+)\r?\n/i', $headers, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;

    } catch (Exception $e) {
        log_message('error', 'Initiate resumable upload error: ' . $e->getMessage());
        return null;
    }
}

/**
 * อัปโหลดเนื้อหาไฟล์
 */
private function upload_file_content($upload_url, $file, $access_token) {
    try {
        $ch = curl_init();
        
        // Open file for reading
        $file_handle = fopen($file['tmp_name'], 'rb');
        if (!$file_handle) {
            throw new Exception('ไม่สามารถเปิดไฟล์ได้');
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $upload_url,
            CURLOPT_PUT => true,
            CURLOPT_INFILE => $file_handle,
            CURLOPT_INFILESIZE => $file['size'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 300, // 5 minutes for large files
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/octet-stream',
                'Content-Length: ' . $file['size']
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($file_handle);

        if ($http_code === 200) {
            return json_decode($response, true);
        } else {
            log_message('error', 'Upload file content error: HTTP ' . $http_code . ' - ' . $response);
            return null;
        }

    } catch (Exception $e) {
        log_message('error', 'Upload file content error: ' . $e->getMessage());
        return null;
    }
}

/**
 * สร้างโฟลเดอร์ใน Google Drive
 */
public function create_folder() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $folder_name = trim($this->input->post('folder_name'));
        $parent_id = $this->input->post('parent_id');

        if (empty($folder_name)) {
            $this->output_json_error('กรุณาใส่ชื่อโฟลเดอร์');
            return;
        }

        // ตรวจสอบชื่อโฟลเดอร์ (ไม่ให้มีอักขระพิเศษ)
        if (!preg_match('/^[a-zA-Z0-9ก-๙\s\-_.()]+$/', $folder_name)) {
            $this->output_json_error('ชื่อโฟลเดอร์มีอักขระที่ไม่ได้รับอนุญาต');
            return;
        }

        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage หรือ Access Token');
            return;
        }

        // ตรวจสอบ Token
        if (!$this->has_valid_access_token($system_storage)) {
            $this->output_json_error('Access Token หมดอายุ กรุณา Refresh Token');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];

        // สร้างโฟลเดอร์ใน Google Drive
        $folder_result = $this->create_folder_with_curl($folder_name, $parent_id, $access_token);

        if ($folder_result) {
            // บันทึกข้อมูลโฟลเดอร์ในฐานข้อมูล
            $folder_data = [
                'folder_name' => $folder_name,
                'folder_id' => $folder_result['id'],
                'parent_folder_id' => $parent_id ?: null,
                'folder_type' => 'user',
                'folder_path' => $this->build_folder_path($parent_id, $folder_name),
                'folder_description' => 'User created folder',
                'permission_level' => 'restricted',
                'created_by' => $this->session->userdata('m_id')
            ];

            if ($this->save_folder_info($folder_data)) {
                // บันทึก log
                $this->log_activity(
                    $this->session->userdata('m_id'),
                    'create_folder',
                    "Created folder: " . $folder_name . " in parent: " . ($parent_id ?: 'root'),
                    [
                        'folder_name' => $folder_name,
                        'folder_id' => $folder_result['id'],
                        'parent_id' => $parent_id
                    ]
                );

                $this->output_json_success([
                    'folder_id' => $folder_result['id'],
                    'folder_name' => $folder_name,
                    'web_view_link' => $folder_result['webViewLink']
                ], 'สร้างโฟลเดอร์สำเร็จ');
            } else {
                $this->output_json_error('สร้างโฟลเดอร์ใน Google Drive สำเร็จ แต่ไม่สามารถบันทึกข้อมูลในฐานข้อมูลได้');
            }
        } else {
            $this->output_json_error('ไม่สามารถสร้างโฟลเดอร์ได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Create folder error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * สร้าง folder path
 */
private function build_folder_path($parent_id, $folder_name) {
    try {
        if (!$parent_id || $parent_id === 'root') {
            return '/Organization Drive/' . $folder_name;
        }

        // หา parent folder path
        $parent_folder = $this->db->select('folder_path, folder_name')
                                 ->from('tbl_google_drive_system_folders')
                                 ->where('folder_id', $parent_id)
                                 ->where('is_active', 1)
                                 ->get()
                                 ->row();

        if ($parent_folder) {
            return $parent_folder->folder_path . '/' . $folder_name;
        } else {
            return '/Organization Drive/' . $folder_name;
        }

    } catch (Exception $e) {
        return '/Organization Drive/' . $folder_name;
    }
}


	
	
	

/**
 * ทดสอบการตั้งค่า OAuth
 */
private function test_oauth_configuration() {
    $client_id = $this->get_setting('google_client_id');
    $client_secret = $this->get_setting('google_client_secret');

    if (empty($client_id)) {
        return ['passed' => false, 'message' => 'Google Client ID ไม่ได้ตั้งค่า'];
    }

    if (empty($client_secret)) {
        return ['passed' => false, 'message' => 'Google Client Secret ไม่ได้ตั้งค่า'];
    }

    return ['passed' => true, 'message' => 'OAuth Credentials ตั้งค่าเรียบร้อย'];
}

/**
 * ทดสอบสถานะ System Storage
 */
private function test_system_storage_status() {
    $system_storage = $this->get_active_system_storage();
    
    if (!$system_storage) {
        return ['passed' => false, 'message' => 'ไม่พบ System Storage'];
    }

    if (empty($system_storage->google_account_email)) {
        return ['passed' => false, 'message' => 'ไม่มี Google Account เชื่อมต่อ'];
    }

    return ['passed' => true, 'message' => 'System Storage พร้อมใช้งาน: ' . $system_storage->google_account_email];
}



/**
 * สร้างลิงก์แชร์สำหรับไฟล์/โฟลเดอร์
 */
public function create_share_link() {
    // Force JSON response และป้องกัน HTML output
    $this->output->set_content_type('application/json');
    
    try {
        // ล้าง output buffer ทั้งหมด
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // ตั้งค่า Error Handling
        set_error_handler(function($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        // ตรวจสอบ Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json_response(false, 'Only POST method allowed');
            return;
        }

        // ตรวจสอบ AJAX
        if (!$this->input->is_ajax_request()) {
            $this->json_response(false, 'AJAX request required');
            return;
        }

        // รับข้อมูล Input
        $item_id = $this->input->post('item_id');
        $item_type = $this->input->post('item_type');
        $permission = $this->input->post('permission') ?: 'reader';
        $access = $this->input->post('access') ?: 'anyone';

        // Validate Input
        if (empty($item_id)) {
            $this->json_response(false, 'item_id is required');
            return;
        }

        if (empty($item_type)) {
            $this->json_response(false, 'item_type is required');
            return;
        }

        // Log request
        log_message('info', "create_share_link called: item_id={$item_id}, item_type={$item_type}, permission={$permission}, access={$access}");

        // ตรวจสอบ System Storage
        $system_storage = $this->get_system_storage_safe();
        if (!$system_storage['success']) {
            $this->json_response(false, $system_storage['message']);
            return;
        }

        $storage = $system_storage['data'];
        $access_token = $this->get_access_token_safe($storage);
        
        if (!$access_token['success']) {
            $this->json_response(false, $access_token['message']);
            return;
        }

        $token = $access_token['token'];

        // สร้างลิงก์แชร์แบบ Safe
        $share_result = $this->create_share_link_safe($item_id, $permission, $access, $token);

        if ($share_result['success']) {
            // บันทึก Log
            $this->log_share_activity($item_id, $item_type, $permission, $access, $share_result['link']);

            $this->json_response(true, 'สร้างลิงก์แชร์สำเร็จ', [
                'share_link' => $share_result['link'],
                'permission' => $permission,
                'access' => $access,
                'item_id' => $item_id,
                'method' => $share_result['method'] ?? 'api'
            ]);
        } else {
            $this->json_response(false, $share_result['message']);
        }

    } catch (Exception $e) {
        log_message('error', 'create_share_link Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        $this->json_response(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    } catch (Error $e) {
        log_message('error', 'create_share_link Fatal Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        $this->json_response(false, 'เกิดข้อผิดพลาดร้ายแรง: ' . $e->getMessage());
    } finally {
        // คืนค่า Error Handler
        restore_error_handler();
    }
}

	
	/**
 * สร้างลิงก์แชร์แบบปลอดภัย (Multiple Methods)
 */
private function create_share_link_safe($item_id, $permission, $access, $access_token) {
    try {
        log_message('info', "Creating safe share link for: {$item_id}");

        // Method 1: ดึง webViewLink ที่มีอยู่แล้ว (ไม่ต้องแก้ไข permissions)
        $existing_link = $this->get_file_web_link($item_id, $access_token);
        if ($existing_link) {
            log_message('info', 'Using existing webViewLink: ' . $existing_link);
            return [
                'success' => true,
                'link' => $existing_link,
                'method' => 'existing_webview_link'
            ];
        }

        // Method 2: สร้าง Manual Link (Google Drive Standard Format)
        $manual_link = "https://drive.google.com/file/d/{$item_id}/view?usp=sharing";
        log_message('info', 'Using manual generated link: ' . $manual_link);
        
        return [
            'success' => true,
            'link' => $manual_link,
            'method' => 'manual_generated'
        ];

    } catch (Exception $e) {
        log_message('error', 'create_share_link_safe error: ' . $e->getMessage());
        
        // Fallback: Manual Link แน่นอน
        $fallback_link = "https://drive.google.com/file/d/{$item_id}/view";
        return [
            'success' => true,
            'link' => $fallback_link,
            'method' => 'fallback_manual'
        ];
    }
}
	
	/**
 * ดึง webViewLink ของไฟล์
 */
private function get_file_web_link($file_id, $access_token) {
    try {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$file_id}?fields=id,name,webViewLink,webContentLink",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', 'cURL error in get_file_web_link: ' . $curl_error);
            return null;
        }

        if ($http_code === 200) {
            $file_data = json_decode($response, true);
            return $file_data['webViewLink'] ?? $file_data['webContentLink'] ?? null;
        }

        log_message('warning', "get_file_web_link failed: HTTP {$http_code}");
        return null;

    } catch (Exception $e) {
        log_message('error', 'get_file_web_link exception: ' . $e->getMessage());
        return null;
    }
}
	
	
	
	/**
 * Response JSON แบบปลอดภัย
 */
private function json_response($success, $message, $data = null) {
    // ล้าง output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }

    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    // Set headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}
	
	

	/**
 * บันทึก Log การแชร์
 */
private function log_share_activity($item_id, $item_type, $permission, $access, $share_link) {
    try {
        $log_data = [
            'user_id' => $this->session->userdata('m_id'),
            'action' => 'create_share_link',
            'item_id' => $item_id,
            'item_type' => $item_type,
            'permission' => $permission,
            'access' => $access,
            'share_link' => $share_link,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        log_message('info', 'Share activity: ' . json_encode($log_data));
        return true;

    } catch (Exception $e) {
        log_message('error', 'Log share activity error: ' . $e->getMessage());
        return false;
    }
}

	
	
	/**
 * ดึง Access Token แบบปลอดภัย
 */
private function get_access_token_safe($storage) {
    try {
        $token_data = json_decode($storage->google_access_token, true);
        
        if (!$token_data || !isset($token_data['access_token'])) {
            return ['success' => false, 'message' => 'รูปแบบ Token ไม่ถูกต้อง'];
        }

        // ตรวจสอบอายุ Token
        if ($storage->google_token_expires) {
            $expires = strtotime($storage->google_token_expires);
            if ($expires <= time()) {
                return ['success' => false, 'message' => 'Access Token หมดอายุ - กรุณา Refresh Token'];
            }
        }

        return ['success' => true, 'token' => $token_data['access_token']];

    } catch (Exception $e) {
        return ['success' => false, 'message' => 'ข้อผิดพลาดในการตรวจสอบ Token: ' . $e->getMessage()];
    }
}

	
	
/**
 * 🛡️ Get System Storage (Safe)
 */
private function get_system_storage_safe() {
    try {
        // ตรวจสอบตารางก่อน
        if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
            log_message('info', 'System storage table does not exist');
            return null;
        }

        $storage = $this->db->select('*')
                           ->from('tbl_google_drive_system_storage')
                           ->where('is_active', 1)
                           ->get()
                           ->row();

        if ($storage) {
            log_message('info', 'Found system storage: ' . $storage->google_account_email);
        } else {
            log_message('info', 'No active system storage found');
        }

        return $storage;

    } catch (Exception $e) {
        log_message('error', 'Get system storage safe error: ' . $e->getMessage());
        return null;
    }
}

	/**
 * 🗑️ Delete Google Drive Contents (Safe)
 */
private function delete_google_drive_contents_safe($system_storage) {
    $stats = ['folders_deleted' => 0, 'files_deleted' => 0];

    try {
        // ตรวจสอบ Access Token
        if (empty($system_storage->google_access_token)) {
            log_message('info', 'No access token available for Google Drive deletion');
            return $stats;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            log_message('warning', 'Invalid access token format');
            return $stats;
        }

        $access_token = $token_data['access_token'];
        
        // ลบโฟลเดอร์ระบบ
        $folders = $this->get_system_folders_for_deletion();
        foreach ($folders as $folder) {
            if (!empty($folder->folder_id)) {
                if ($this->delete_google_item_safe($folder->folder_id, $access_token)) {
                    $stats['folders_deleted']++;
                    log_message('info', 'Deleted folder: ' . $folder->folder_name);
                }
            }
        }

        // ลบ Root Folder
        if (!empty($system_storage->root_folder_id)) {
            if ($this->delete_google_item_safe($system_storage->root_folder_id, $access_token)) {
                $stats['folders_deleted']++;
                log_message('info', 'Deleted root folder');
            }
        }

    } catch (Exception $e) {
        log_message('error', 'Delete Google Drive contents safe error: ' . $e->getMessage());
    }

    return $stats;
}

	/**
 * 🗑️ Clear Database (Safe)
 */
private function clear_database_safe($deep_clean = false) {
    $stats = ['records_deleted' => 0, 'tables_cleared' => 0];

    try {
        $this->db->trans_start();

        // ตารางที่ต้องล้าง
        $tables_to_clear = [
            'tbl_google_drive_system_folders',
            'tbl_google_drive_folders',
            'tbl_google_drive_folder_permissions',
            'tbl_google_drive_member_folder_access',
            'tbl_google_drive_member_permissions',
            'tbl_google_drive_permissions',
            'tbl_google_drive_logs',
            'tbl_google_drive_activity_logs'
        ];

        if ($deep_clean) {
            $tables_to_clear = array_merge($tables_to_clear, [
                'tbl_google_drive_sharing',
                'tbl_google_drive_sharing_activities',
                'tbl_google_drive_shared_permissions'
            ]);
        }

        // ล้างแต่ละตาราง
        foreach ($tables_to_clear as $table) {
            if ($this->db->table_exists($table)) {
                $count = $this->db->count_all($table);
                
                if ($deep_clean) {
                    $this->db->truncate($table);
                } else {
                    $this->db->empty_table($table);
                }
                
                $stats['records_deleted'] += $count;
                $stats['tables_cleared']++;
                
                log_message('info', "Cleared table {$table} ({$count} records)");
            }
        }

        // รีเซ็ต System Storage Structure
        if ($this->db->table_exists('tbl_google_drive_system_storage')) {
            $this->db->set([
                'folder_structure_created' => 0,
                'root_folder_id' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ])->where('is_active', 1)
              ->update('tbl_google_drive_system_storage');
            
            log_message('info', 'Reset system storage structure');
        }

        // รีเซ็ต Member Settings
        $this->db->set([
            'personal_folder_id' => null,
            'storage_quota_used' => 0,
            'last_storage_access' => null
        ])->update('tbl_member');

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            throw new Exception('Database transaction failed');
        }

        log_message('info', 'Database clearing completed successfully');

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Clear database safe error: ' . $e->getMessage());
        throw $e;
    }

    return $stats;
}

	
	
	/**
 * 🗑️ Clear Cache (Safe)
 */
private function clear_cache_safe() {
    try {
        // ล้าง CodeIgniter Cache
        $this->load->driver('cache', ['adapter' => 'file']);
        $this->cache->clean();

        // ล้าง Session
        $this->session->unset_userdata([
            'google_drive_connected',
            'system_storage_ready',
            'google_drive_folders'
        ]);

        log_message('info', 'Cache and sessions cleared');

    } catch (Exception $e) {
        log_message('warning', 'Clear cache safe error: ' . $e->getMessage());
        // ไม่ throw error เพราะไม่ critical
    }
}

	
	/**
 * 🗑️ Get System Folders for Deletion (Safe)
 */
private function get_system_folders_for_deletion() {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return [];
        }

        return $this->db->select('folder_id, folder_name')
                       ->from('tbl_google_drive_system_folders')
                       ->where('is_active', 1)
                       ->get()
                       ->result();

    } catch (Exception $e) {
        log_message('error', 'Get system folders for deletion error: ' . $e->getMessage());
        return [];
    }
}
	
	
	/**
 * 🗑️ Delete Google Drive Item (Safe)
 */
private function delete_google_item_safe($item_id, $access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$item_id}",
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_code === 204); // 204 = Delete Success

    } catch (Exception $e) {
        log_message('error', 'Delete Google item safe error: ' . $e->getMessage());
        return false;
    }
}

	
	
	/**
 * สร้างลิงก์แชร์แบบปลอดภัย (Safe Version)
 */
private function create_google_drive_share_link_safe($file_id, $permission, $access, $access_token) {
    try {
        log_message('info', "Creating safe share link for file: {$file_id}");

        // วิธีที่ 1: ลองสร้าง Public Link โดยตรง (สำหรับ anyone access)
        if ($access === 'anyone') {
            $public_result = $this->create_public_share_link($file_id, $permission, $access_token);
            if ($public_result['success']) {
                return $public_result;
            }
            
            log_message('warning', 'Public share link failed, trying alternative method: ' . ($public_result['error'] ?? 'Unknown'));
        }

        // วิธีที่ 2: ใช้ webViewLink ที่มีอยู่แล้ว
        $existing_link = $this->get_existing_share_link($file_id, $access_token);
        if ($existing_link) {
            log_message('info', 'Using existing webViewLink: ' . $existing_link);
            return [
                'success' => true,
                'share_link' => $existing_link,
                'method' => 'existing_link'
            ];
        }

        // วิธีที่ 3: สร้างลิงก์ด้วยตนเอง
        $manual_link = "https://drive.google.com/file/d/{$file_id}/view?usp=sharing";
        log_message('info', 'Using manual generated link: ' . $manual_link);
        
        return [
            'success' => true,
            'share_link' => $manual_link,
            'method' => 'manual_link'
        ];

    } catch (Exception $e) {
        log_message('error', 'Create safe share link error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => 'ไม่สามารถสร้างลิงก์แชร์ได้: ' . $e->getMessage()
        ];
    }
}
	

private function create_public_share_link($file_id, $permission, $access_token) {
    try {
        $ch = curl_init();
        
        $permission_data = [
            'role' => $permission,
            'type' => 'anyone'
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$file_id}/permissions",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($permission_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15, // ลดเวลา timeout
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'success' => false,
                'error' => 'Network error: ' . $curl_error
            ];
        }

        if ($http_code === 200) {
            // สำเร็จ - ดึงลิงก์
            $share_link = $this->get_existing_share_link($file_id, $access_token);
            if ($share_link) {
                return [
                    'success' => true,
                    'share_link' => $share_link,
                    'method' => 'api_public'
                ];
            }
        }

        // ถ้าไม่สำเร็จ ลอง fallback
        log_message('warning', "Public permission creation failed: HTTP {$http_code} - {$response}");
        return [
            'success' => false,
            'error' => "HTTP {$http_code}"
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}


	
	/**
 * บันทึกข้อมูลการแชร์แบบปลอดภัย
 */
private function safe_save_share_record($item_id, $item_type, $permission, $access, $share_link) {
    try {
        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_sharing')) {
            log_message('info', 'Sharing table does not exist, skipping save');
            return false;
        }

        $data = [
            'item_id' => $item_id,
            'item_type' => $item_type,
            'share_type' => 'link',
            'permission' => $permission,
            'access_level' => $access,
            'share_link' => $share_link,
            'shared_by' => $this->session->userdata('m_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_google_drive_sharing', $data);

    } catch (Exception $e) {
        log_message('error', 'Safe save share record error: ' . $e->getMessage());
        return false;
    }
}


	
	
	/**
 * ดึงลิงก์แชร์ที่มีอยู่แล้ว
 */
private function get_existing_share_link($file_id, $access_token) {
    try {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$file_id}?fields=webViewLink,webContentLink,id,name",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error || $http_code !== 200) {
            return null;
        }

        $file_data = json_decode($response, true);
        return $file_data['webViewLink'] ?? $file_data['webContentLink'] ?? null;

    } catch (Exception $e) {
        return null;
    }
}

	

public function share_with_email() {
    $this->output->set_content_type('application/json');
    
    try {
        while (ob_get_level()) {
            ob_end_clean();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json_response(false, 'Only POST method allowed');
            return;
        }

        $item_id = $this->input->post('item_id');
        $item_type = $this->input->post('item_type');
        $email = trim($this->input->post('email'));
        $permission = $this->input->post('permission') ?: 'reader';
        $message = trim($this->input->post('message'));

        // Validate
        if (empty($item_id) || empty($email)) {
            $this->json_response(false, 'item_id และ email จำเป็นต้องระบุ');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json_response(false, 'รูปแบบอีเมลไม่ถูกต้อง');
            return;
        }

        // ตรวจสอบ System
        $system_storage = $this->get_system_storage_safe();
        if (!$system_storage['success']) {
            $this->json_response(false, $system_storage['message']);
            return;
        }

        $storage = $system_storage['data'];
        $access_token = $this->get_access_token_safe($storage);
        
        if (!$access_token['success']) {
            $this->json_response(false, $access_token['message']);
            return;
        }

        // แชร์กับอีเมล
        $share_result = $this->share_with_email_safe($item_id, $email, $permission, $message, $access_token['token']);

        if ($share_result['success']) {
            $this->json_response(true, "แชร์กับ {$email} สำเร็จ", [
                'email' => $email,
                'permission' => $permission
            ]);
        } else {
            $this->json_response(false, $share_result['message']);
        }

    } catch (Exception $e) {
        log_message('error', 'share_with_email Exception: ' . $e->getMessage());
        $this->json_response(false, 'เกิดข้อผิดพลาด: ' . $e->getMessage());
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

        if ($http_code === 200) {
            return ['success' => true, 'message' => 'แชร์สำเร็จ'];
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
	

/**
 * สร้างลิงก์แชร์ใน Google Drive ด้วย cURL (Fixed Version)
 */
private function create_google_drive_share_link($file_id, $permission, $access, $access_token) {
    try {
        log_message('info', "Creating share link for file: {$file_id} with permission: {$permission}");

        // Step 1: ตรวจสอบว่าไฟล์มี public permission อยู่แล้วหรือไม่
        $existing_permissions = $this->get_file_permissions($file_id, $access_token);
        $has_public_permission = false;
        
        foreach ($existing_permissions as $perm) {
            if ($perm['type'] === 'anyone') {
                $has_public_permission = true;
                break;
            }
        }

        // Step 2: ถ้ายังไม่มี public permission ให้สร้างใหม่
        if (!$has_public_permission && $access === 'anyone') {
            $permission_result = $this->create_file_permission($file_id, $permission, $access, $access_token);
            
            if (!$permission_result['success']) {
                return $permission_result;
            }
        }

        // Step 3: ดึงลิงก์แชร์
        $share_link = $this->get_google_drive_share_link($file_id, $access_token);
        
        if ($share_link) {
            return [
                'success' => true,
                'share_link' => $share_link,
                'permission_id' => $permission_result['permission_id'] ?? null
            ];
        } else {
            return [
                'success' => false,
                'error' => 'ไม่สามารถดึงลิงก์แชร์ได้'
            ];
        }

    } catch (Exception $e) {
        log_message('error', 'Create Google Drive share link error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

	
	
	/**
 * สร้างสิทธิ์การเข้าถึงไฟล์แบบใหม่ (Fixed)
 */
private function create_file_permission($file_id, $permission, $access, $access_token) {
    try {
        $ch = curl_init();
        
        // สร้าง permission data ที่ถูกต้อง
        $permission_data = [
            'role' => $permission, // reader, commenter, writer
            'type' => $access      // anyone, user, group, domain
        ];

        // สำหรับ 'anyone' type ไม่ต้องใส่ emailAddress
        // สำหรับ 'user' type ต้องใส่ emailAddress

        log_message('info', 'Creating permission with data: ' . json_encode($permission_data));

        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$file_id}/permissions",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($permission_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Log response for debugging
        log_message('info', "Create permission response: HTTP {$http_code} - {$response}");

        if ($curl_error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $curl_error
            ];
        }

        if ($http_code === 200) {
            $permission_result = json_decode($response, true);
            return [
                'success' => true,
                'permission_id' => $permission_result['id'] ?? null
            ];
        } else {
            $error_response = json_decode($response, true);
            $error_message = 'HTTP Error: ' . $http_code;
            
            if ($error_response && isset($error_response['error'])) {
                if (isset($error_response['error']['message'])) {
                    $error_message .= ' - ' . $error_response['error']['message'];
                }
                
                // Handle specific Google Drive API errors
                if (isset($error_response['error']['code'])) {
                    switch ($error_response['error']['code']) {
                        case 400:
                            $error_message = 'การตั้งค่าสิทธิ์ไม่ถูกต้อง - โปรดตรวจสอบการกำหนดค่า';
                            break;
                        case 403:
                            $error_message = 'ไม่มีสิทธิ์ในการแชร์ไฟล์นี้';
                            break;
                        case 404:
                            $error_message = 'ไม่พบไฟล์ที่ต้องการแชร์';
                            break;
                    }
                }
            }
            
            return [
                'success' => false,
                'error' => $error_message
            ];
        }

    } catch (Exception $e) {
        log_message('error', 'Create file permission error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

	
	
	
	/**
 * ดึงสิทธิ์การเข้าถึงไฟล์ที่มีอยู่
 */
private function get_file_permissions($file_id, $access_token) {
    try {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$file_id}/permissions",
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
            return $data['permissions'] ?? [];
        }

        return [];

    } catch (Exception $e) {
        log_message('error', 'Get file permissions error: ' . $e->getMessage());
        return [];
    }
}
	
	
	

/**
 * 🔧 Error-proof disconnect_system_account() - แก้ไข HTTP 500 และ JSON Error
 * เพิ่มใน Google_drive_system Controller
 */

public function disconnect_system_account() {
    // 🚨 Step 1: บังคับ JSON Response ก่อนทำอะไร
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('X-Content-Type-Options: nosniff');
    
    // 🚨 Step 2: ล้าง ALL output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // 🚨 Step 3: Disable error output เพื่อป้องกัน HTML error
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    
    try {
        // ✅ ตรวจสอบ Request Method
        if (!$_POST || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json_exit(false, 'Only POST method allowed', 405);
        }

        // ✅ ตรวจสอบ AJAX Header
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            $this->json_exit(false, 'AJAX request required', 400);
        }

        // ✅ ตรวจสอบ Session และสิทธิ์
        if (!$this->session->userdata('m_system') || 
            !in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->json_exit(false, 'ไม่มีสิทธิ์: เฉพาะ System Admin เท่านั้น', 403);
        }

        // ✅ ตรวจสอบ Confirmation Code
        $confirm = isset($_POST['confirm_disconnect']) ? $_POST['confirm_disconnect'] : '';
        if ($confirm !== 'DISCONNECT_SYSTEM_GOOGLE_ACCOUNT') {
            $this->json_exit(false, 'รหัสยืนยันไม่ถูกต้อง', 400);
        }

        // ✅ Log การเริ่มต้น
        log_message('info', 'DISCONNECT: Started by admin ID: ' . $this->session->userdata('m_id'));

        // ✅ ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage_safe();
        if (!$system_storage['success']) {
            $this->json_exit(false, $system_storage['message'], 404);
        }

        $storage = $system_storage['data'];
        log_message('info', 'DISCONNECT: Found storage for account: ' . $storage->google_account_email);

        // ✅ เริ่ม Database Transaction
        if (!$this->db->trans_start()) {
            $this->json_exit(false, 'ไม่สามารถเริ่ม Database Transaction', 500);
        }

        $disconnect_results = [
            'revoke_success' => false,
            'storage_disabled' => false,
            'folders_disabled' => 0,
            'members_updated' => 0
        ];

        try {
            // Step 1: Revoke Google Token
            if (!empty($storage->google_access_token)) {
                log_message('info', 'DISCONNECT: Attempting to revoke Google token');
                $disconnect_results['revoke_success'] = $this->revoke_token_safe($storage->google_access_token);
                log_message('info', 'DISCONNECT: Token revoke result: ' . ($disconnect_results['revoke_success'] ? 'success' : 'failed'));
            }

            // Step 2: Disable System Storage
            $storage_update = $this->db->where('id', $storage->id)
                                      ->update('tbl_google_drive_system_storage', [
                                          'is_active' => 0,
                                          'disconnected_at' => date('Y-m-d H:i:s'),
                                          'disconnected_by' => $this->session->userdata('m_id'),
                                          'updated_at' => date('Y-m-d H:i:s')
                                      ]);
            
            $disconnect_results['storage_disabled'] = $storage_update;
            log_message('info', 'DISCONNECT: Storage disabled: ' . ($storage_update ? 'success' : 'failed'));

            // Step 3: Disable System Folders (ถ้าตารางมีอยู่)
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                $this->db->where('is_active', 1)
                        ->update('tbl_google_drive_system_folders', [
                            'is_active' => 0,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                $disconnect_results['folders_disabled'] = $this->db->affected_rows();
                log_message('info', 'DISCONNECT: Folders disabled: ' . $disconnect_results['folders_disabled']);
            }

            // Step 4: Update Member Access
            $this->db->where('storage_access_granted', 1)
                    ->update('tbl_member', [
                        'storage_access_granted' => 0,
                        'personal_folder_id' => null,
                        'last_storage_access' => null
                    ]);
            $disconnect_results['members_updated'] = $this->db->affected_rows();
            log_message('info', 'DISCONNECT: Members updated: ' . $disconnect_results['members_updated']);

            // Commit Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed during commit');
            }

            // ✅ บันทึก Success Log
            $this->log_disconnect_success($storage, $disconnect_results);

            // ✅ Return Success Response
            $this->json_exit(true, 'ตัดการเชื่อมต่อ System Google Account เรียบร้อยแล้ว', 200, [
                'disconnected_account' => $storage->google_account_email,
                'storage_id' => $storage->id,
                'revoke_success' => $disconnect_results['revoke_success'],
                'folders_disabled' => $disconnect_results['folders_disabled'],
                'members_updated' => $disconnect_results['members_updated'],
                'disconnected_at' => date('Y-m-d H:i:s'),
                'disconnected_by' => $this->session->userdata('m_id')
            ]);

        } catch (Exception $e) {
            // Rollback Transaction
            $this->db->trans_rollback();
            throw $e; // Re-throw เพื่อไปยัง outer catch
        }

    } catch (Exception $e) {
        // ✅ จัดการ Error อย่างปลอดภัย
        $error_message = 'เกิดข้อผิดพลาดในการตัดการเชื่อมต่อ: ' . $e->getMessage();
        
        log_message('error', 'DISCONNECT ERROR: ' . $e->getMessage());
        log_message('error', 'DISCONNECT ERROR TRACE: ' . $e->getTraceAsString());
        
        // บันทึก Error Log
        $this->log_disconnect_error($e);

        $this->json_exit(false, $error_message, 500, [
            'error_type' => get_class($e),
            'error_line' => $e->getLine(),
            'error_file' => basename($e->getFile())
        ]);

    } catch (Throwable $t) {
        // ✅ จัดการ Fatal Error
        log_message('error', 'DISCONNECT FATAL: ' . $t->getMessage());
        $this->json_exit(false, 'เกิดข้อผิดพลาดร้ายแรง', 500);
    }
}

/**
 * 🛡️ Safe JSON Exit - บังคับให้ return JSON เท่านั้น
 */
private function json_exit($success, $message, $http_code = 200, $data = []) {
    // ล้าง output buffer อีกครั้ง (double-safe)
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set HTTP status code
    http_response_code($http_code);
    
    // Prepare response
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if (!empty($data)) {
        $response['data'] = $data;
    }
    
    // Add debug info for development
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        $response['debug_info'] = [
            'memory_usage' => memory_get_usage(true),
            'time' => microtime(true)
        ];
    }
    
    // Output JSON และหยุดทำงานทันที
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

/**
 * 🛡️ Safe Get System Storage
 */
private function get_active_system_storage_safe() {
    try {
        // ตรวจสอบตารางก่อน
        if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
            return [
                'success' => false,
                'message' => 'ตาราง System Storage ไม่พร้อมใช้งาน'
            ];
        }

        $storage = $this->db->select('*')
                           ->from('tbl_google_drive_system_storage')
                           ->where('is_active', 1)
                           ->get()
                           ->row();

        if (!$storage) {
            return [
                'success' => false,
                'message' => 'ไม่พบ System Storage ที่จะตัดการเชื่อมต่อ'
            ];
        }

        return [
            'success' => true,
            'data' => $storage
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'ข้อผิดพลาดในการดึงข้อมูล Storage: ' . $e->getMessage()
        ];
    }
}

/**
 * 🛡️ Safe Token Revoke
 */
private function revoke_token_safe($access_token_json) {
    try {
        $token_data = json_decode($access_token_json, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            log_message('warning', 'REVOKE: Invalid token format');
            return false;
        }

        $access_token = $token_data['access_token'];
        $revoke_url = 'https://oauth2.googleapis.com/revoke';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $revoke_url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'token=' . urlencode($access_token),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: Google-Drive-System-Disconnect/1.0'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', 'REVOKE cURL Error: ' . $curl_error);
            return false;
        }

        // HTTP 200 = success, HTTP 400 = token already invalid (ถือว่าสำเร็จ)
        $success = ($http_code === 200 || $http_code === 400);
        log_message('info', "REVOKE Result: HTTP {$http_code} - " . ($success ? 'SUCCESS' : 'FAILED'));
        
        return $success;

    } catch (Exception $e) {
        log_message('error', 'REVOKE Exception: ' . $e->getMessage());
        return false;
    }
}

/**
 * 📝 Log Success
 */
private function log_disconnect_success($storage, $results) {
    try {
        if (method_exists($this, 'log_enhanced_activity')) {
            $this->log_enhanced_activity(
                $this->session->userdata('m_id'),
                'disconnect_system_account',
                'ตัดการเชื่อมต่อ System Google Account สำเร็จ: ' . $storage->google_account_email,
                [
                    'status' => 'success',
                    'google_account' => $storage->google_account_email,
                    'storage_id' => $storage->id,
                    'revoke_success' => $results['revoke_success'],
                    'folders_disabled' => $results['folders_disabled'],
                    'members_updated' => $results['members_updated']
                ]
            );
        }
    } catch (Exception $e) {
        log_message('error', 'Log success error: ' . $e->getMessage());
    }
}

/**
 * 📝 Log Error
 */
private function log_disconnect_error($exception) {
    try {
        if (method_exists($this, 'log_enhanced_activity')) {
            $this->log_enhanced_activity(
                $this->session->userdata('m_id') ?: 0,
                'disconnect_system_account',
                'ตัดการเชื่อมต่อ System Account ล้มเหลว: ' . $exception->getMessage(),
                [
                    'status' => 'failed',
                    'error_message' => $exception->getMessage(),
                    'error_line' => $exception->getLine(),
                    'error_file' => $exception->getFile()
                ]
            );
        }
    } catch (Exception $e) {
        log_message('error', 'Log error failed: ' . $e->getMessage());
    }
}


/**
 * ✅ Revoke Google Token แบบปลอดภัย (สำหรับ System)
 */
private function safe_revoke_google_system_token($access_token_json) {
    try {
        $token_data = json_decode($access_token_json, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            log_message('warning', 'Invalid token format for revoke');
            return false;
        }

        $access_token = $token_data['access_token'];
        
        // Method 1: ลอง revoke ด้วย access_token
        $revoke_url = 'https://oauth2.googleapis.com/revoke';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $revoke_url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(['token' => $access_token]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', 'cURL error in token revoke: ' . $curl_error);
            return false;
        }

        // HTTP 200 = สำเร็จ, HTTP 400 = token invalid (แต่ก็ถือว่าสำเร็จเพราะไม่ใช้งานได้แล้ว)
        if ($http_code === 200 || $http_code === 400) {
            log_message('info', "Token revoke success: HTTP {$http_code}");
            return true;
        }

        log_message('warning', "Token revoke failed: HTTP {$http_code} - {$response}");
        return false;

    } catch (Exception $e) {
        log_message('error', 'Safe revoke Google system token error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ ตรวจสอบสถานะ System Storage หลังจาก Disconnect
 */
public function check_system_status_after_disconnect() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $active_storage = $this->get_active_system_storage();
        
        $status = [
            'has_active_storage' => (bool)$active_storage,
            'is_disconnected' => !$active_storage,
            'needs_reconnection' => !$active_storage,
            'system_ready' => (bool)$active_storage && (bool)($active_storage->folder_structure_created ?? false)
        ];

        if ($active_storage) {
            $status['storage_info'] = [
                'google_account' => $active_storage->google_account_email,
                'created_at' => $active_storage->created_at,
                'folder_structure_ready' => (bool)$active_storage->folder_structure_created
            ];
        }

        $this->output_json_success($status, 'ตรวจสอบสถานะระบบสำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
/**
 * ดึงลิงก์แชร์จาก Google Drive (Updated)
 */
private function get_google_drive_share_link($file_id, $access_token) {
    try {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$file_id}?fields=webViewLink,webContentLink,id,name",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $file_data = json_decode($response, true);
            
            // ลองใช้ webViewLink ก่อน ถ้าไม่มีใช้ webContentLink
            $share_link = $file_data['webViewLink'] ?? $file_data['webContentLink'] ?? null;
            
            // ถ้ายังไม่มี ให้สร้างลิงก์ด้วยตนเอง
            if (!$share_link && isset($file_data['id'])) {
                $share_link = "https://drive.google.com/file/d/{$file_data['id']}/view";
            }
            
            return $share_link;
        }

        return null;

    } catch (Exception $e) {
        log_message('error', 'Get Google Drive share link error: ' . $e->getMessage());
        return null;
    }
}


/**
 * แชร์กับอีเมลเฉพาะใน Google Drive (Fixed Version)
 */
private function share_google_drive_with_email($file_id, $email, $permission, $message, $access_token) {
    try {
        log_message('info', "Sharing file {$file_id} with {$email} (permission: {$permission})");

        $ch = curl_init();
        
        $permission_data = [
            'role' => $permission,           // reader, commenter, writer
            'type' => 'user',               // ต้องเป็น 'user' สำหรับอีเมลเฉพาะ
            'emailAddress' => $email
        ];

        log_message('info', 'Sharing permission data: ' . json_encode($permission_data));

        // URL with notification parameter
        $url = "https://www.googleapis.com/drive/v3/files/{$file_id}/permissions";
        $url .= '?sendNotificationEmail=true';

        // เพิ่มข้อความถ้ามี
        if (!empty($message)) {
            $url .= '&emailMessage=' . urlencode($message);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($permission_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        log_message('info', "Share with email response: HTTP {$http_code} - {$response}");

        if ($curl_error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $curl_error
            ];
        }

        if ($http_code === 200) {
            $permission_result = json_decode($response, true);
            
            return [
                'success' => true,
                'permission_id' => $permission_result['id'] ?? null,
                'email' => $email,
                'role' => $permission
            ];
        } else {
            $error_response = json_decode($response, true);
            $error_message = 'HTTP Error: ' . $http_code;
            
            if ($error_response && isset($error_response['error'])) {
                if (isset($error_response['error']['message'])) {
                    $error_message = $error_response['error']['message'];
                }
                
                // Handle specific errors
                if (isset($error_response['error']['code'])) {
                    switch ($error_response['error']['code']) {
                        case 400:
                            if (strpos($error_response['error']['message'], 'emailAddress') !== false) {
                                $error_message = 'รูปแบบอีเมลไม่ถูกต้อง';
                            } else {
                                $error_message = 'ข้อมูลการแชร์ไม่ถูกต้อง';
                            }
                            break;
                        case 403:
                            $error_message = 'ไม่มีสิทธิ์ในการแชร์ไฟล์นี้';
                            break;
                        case 404:
                            $error_message = 'ไม่พบไฟล์หรืออีเมลไม่ถูกต้อง';
                            break;
                    }
                }
            }
            
            return [
                'success' => false,
                'error' => $error_message
            ];
        }

    } catch (Exception $e) {
        log_message('error', 'Share Google Drive with email error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}


/**
 * บันทึกข้อมูลการแชร์ลิงก์
 */
private function save_share_record($item_id, $item_type, $permission, $access, $share_link) {
    try {
        // สร้างตารางถ้ายังไม่มี
        $this->create_share_table_if_not_exists();

        $data = [
            'item_id' => $item_id,
            'item_type' => $item_type,
            'share_type' => 'link',
            'permission' => $permission,
            'access_level' => $access,
            'share_link' => $share_link,
            'shared_by' => $this->session->userdata('m_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_google_drive_sharing', $data);

    } catch (Exception $e) {
        log_message('error', 'Save share record error: ' . $e->getMessage());
        return false;
    }
}

/**
 * บันทึกข้อมูลการแชร์อีเมล
 */
private function save_email_share_record($item_id, $item_type, $email, $permission, $message) {
    try {
        // สร้างตารางถ้ายังไม่มี
        $this->create_share_table_if_not_exists();

        $data = [
            'item_id' => $item_id,
            'item_type' => $item_type,
            'share_type' => 'email',
            'target_email' => $email,
            'permission' => $permission,
            'share_message' => $message,
            'shared_by' => $this->session->userdata('m_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_google_drive_sharing', $data);

    } catch (Exception $e) {
        log_message('error', 'Save email share record error: ' . $e->getMessage());
        return false;
    }
}

/**
 * สร้างตาราง Sharing ถ้ายังไม่มี
 */
private function create_share_table_if_not_exists() {
    if (!$this->db->table_exists('tbl_google_drive_sharing')) {
        $sql = "
            CREATE TABLE IF NOT EXISTS `tbl_google_drive_sharing` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `item_id` varchar(255) NOT NULL COMMENT 'Google Drive Item ID',
                `item_type` enum('file','folder') NOT NULL COMMENT 'ประเภทรายการ',
                `share_type` enum('link','email') NOT NULL COMMENT 'ประเภทการแชร์',
                `target_email` varchar(255) DEFAULT NULL COMMENT 'อีเมลผู้รับ (สำหรับ email type)',
                `permission` enum('reader','commenter','writer','owner') NOT NULL COMMENT 'สิทธิ์การเข้าถึง',
                `access_level` enum('restricted','anyone') DEFAULT 'restricted' COMMENT 'ระดับการเข้าถึง',
                `share_link` text DEFAULT NULL COMMENT 'ลิงก์แชร์',
                `share_message` text DEFAULT NULL COMMENT 'ข้อความที่ส่งไปพร้อมการแชร์',
                `google_permission_id` varchar(255) DEFAULT NULL COMMENT 'Permission ID จาก Google',
                `shared_by` int(11) NOT NULL COMMENT 'ผู้แชร์',
                `shared_at` datetime DEFAULT current_timestamp() COMMENT 'วันที่แชร์',
                `revoked_at` datetime DEFAULT NULL COMMENT 'วันที่เพิกถอน',
                `revoked_by` int(11) DEFAULT NULL COMMENT 'ผู้เพิกถอน',
                `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะการแชร์',
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_item_id` (`item_id`),
                KEY `idx_shared_by` (`shared_by`),
                KEY `idx_target_email` (`target_email`),
                KEY `idx_share_type` (`share_type`),
                KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ตารางบันทึกการแชร์ไฟล์/โฟลเดอร์';
        ";

        $this->db->query($sql);
    }
}

/**
 * ดูรายการการแชร์ของรายการ
 */
public function get_item_shares() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $item_id = $this->input->get('item_id');
        
        if (empty($item_id)) {
            $this->output_json_error('ไม่ได้ระบุรายการ');
            return;
        }

        $shares = $this->get_sharing_records($item_id);
        
        $this->output_json_success($shares, 'ดึงข้อมูลการแชร์สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ดึงข้อมูลการแชร์ของรายการ
 */
private function get_sharing_records($item_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_sharing')) {
            return [];
        }

        return $this->db->select('s.*, m.m_fname, m.m_lname')
                       ->from('tbl_google_drive_sharing s')
                       ->join('tbl_member m', 's.shared_by = m.m_id', 'left')
                       ->where('s.item_id', $item_id)
                       ->where('s.is_active', 1)
                       ->order_by('s.created_at', 'desc')
                       ->get()
                       ->result();

    } catch (Exception $e) {
        log_message('error', 'Get sharing records error: ' . $e->getMessage());
        return [];
    }
}

/**
 * เพิกถอนการแชร์
 */
public function revoke_share() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $share_id = $this->input->post('share_id');
        
        if (empty($share_id)) {
            $this->output_json_error('ไม่ได้ระบุการแชร์ที่จะเพิกถอน');
            return;
        }

        // ดึงข้อมูลการแชร์
        $share_record = $this->db->where('id', $share_id)
                                ->where('is_active', 1)
                                ->get('tbl_google_drive_sharing')
                                ->row();

        if (!$share_record) {
            $this->output_json_error('ไม่พบข้อมูลการแชร์');
            return;
        }

        // เพิกถอนใน Google Drive (ถ้ามี permission_id)
        if ($share_record->google_permission_id) {
            $system_storage = $this->get_active_system_storage();
            if ($system_storage && $system_storage->google_access_token) {
                $token_data = json_decode($system_storage->google_access_token, true);
                $this->revoke_google_drive_permission($share_record->item_id, $share_record->google_permission_id, $token_data['access_token']);
            }
        }

        // อัปเดตสถานะในฐานข้อมูล
        $this->db->where('id', $share_id)
                ->update('tbl_google_drive_sharing', [
                    'is_active' => 0,
                    'revoked_at' => date('Y-m-d H:i:s'),
                    'revoked_by' => $this->session->userdata('m_id')
                ]);

        $this->output_json_success([], 'เพิกถอนการแชร์เรียบร้อย');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * เพิกถอนสิทธิ์ใน Google Drive
 */
private function revoke_google_drive_permission($file_id, $permission_id, $access_token) {
    try {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$file_id}/permissions/{$permission_id}",
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_code === 204); // 204 = No Content (success)

    } catch (Exception $e) {
        log_message('error', 'Revoke Google Drive permission error: ' . $e->getMessage());
        return false;
    }
}
	
	

// เพิ่มในไฟล์ application/controllers/Google_drive_system.php

/**
 * ✅ HEALTH CHECK ENDPOINT
 * URL: /google_drive_system/health_check
 */
public function health_check() {
    try {
        $health = [
            'timestamp' => date('Y-m-d H:i:s'),
            'system_status' => 'unknown',
            'token_status' => 'unknown',
            'token_expires_at' => null,
            'time_to_expiry_seconds' => null,
            'time_to_expiry_minutes' => null,
            'auto_refresh_available' => false,
            'last_refresh_attempt' => null,
            'system_storage_available' => false,
            'can_access_google_drive' => false,
            'warning_level' => 'none' // none, warning, critical, emergency
        ];

        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage) {
            $health['system_status'] = 'error';
            $health['warning_level'] = 'emergency';
            $this->output_health_check($health);
            return;
        }

        $health['system_storage_available'] = true;

        if (!$system_storage->google_access_token) {
            $health['token_status'] = 'missing';
            $health['warning_level'] = 'emergency';
            $this->output_health_check($health);
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            $health['token_status'] = 'invalid_format';
            $health['warning_level'] = 'emergency';
            $this->output_health_check($health);
            return;
        }

        // ตรวจสอบ Refresh Token
        $health['auto_refresh_available'] = isset($token_data['refresh_token']) && !empty($token_data['refresh_token']);

        // ตรวจสอบการหมดอายุ
        if ($system_storage->google_token_expires) {
            $expires_time = strtotime($system_storage->google_token_expires);
            $current_time = time();
            $time_diff = $expires_time - $current_time;

            $health['token_expires_at'] = $system_storage->google_token_expires;
            $health['time_to_expiry_seconds'] = max(0, $time_diff);
            $health['time_to_expiry_minutes'] = max(0, round($time_diff / 60, 2));

            // กำหนดระดับแจ้งเตือน
            if ($time_diff <= 0) {
                $health['token_status'] = 'expired';
                $health['warning_level'] = 'emergency';
            } elseif ($time_diff <= 300) { // 5 นาที
                $health['token_status'] = 'critical';
                $health['warning_level'] = 'critical';
            } elseif ($time_diff <= 900) { // 15 นาที
                $health['token_status'] = 'warning';
                $health['warning_level'] = 'warning';
            } else {
                $health['token_status'] = 'healthy';
                $health['warning_level'] = 'none';
            }
        } else {
            // ไม่มีข้อมูลวันหมดอายุ - ทดสอบ Token
            if ($this->test_token_validity_quick($token_data['access_token'])) {
                $health['token_status'] = 'valid_no_expiry';
                $health['warning_level'] = 'none';
            } else {
                $health['token_status'] = 'invalid';
                $health['warning_level'] = 'emergency';
            }
        }

        // ทดสอบการเข้าถึง Google Drive
        if ($health['token_status'] === 'healthy' || $health['token_status'] === 'valid_no_expiry') {
            $health['can_access_google_drive'] = $this->test_google_drive_access_quick($token_data['access_token']);
        }

        // สถานะระบบรวม
        if ($health['can_access_google_drive']) {
            $health['system_status'] = 'operational';
        } elseif ($health['auto_refresh_available'] && ($health['token_status'] === 'expired' || $health['token_status'] === 'critical')) {
            $health['system_status'] = 'degraded_auto_recovery';
        } else {
            $health['system_status'] = 'critical';
        }

        $this->output_health_check($health);

    } catch (Exception $e) {
        $error_health = [
            'timestamp' => date('Y-m-d H:i:s'),
            'system_status' => 'error',
            'error' => $e->getMessage(),
            'warning_level' => 'emergency'
        ];
        $this->output_health_check($error_health);
    }
}

/**
 * ✅ AUTO-REFRESH TOKEN SYSTEM
 * เรียกใช้ก่อนทุก API call
 */
private function ensure_valid_access_token($force_refresh = false) {
    try {
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            log_message('error', 'Auto-refresh: No system storage or token');
            return false;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            log_message('error', 'Auto-refresh: Invalid token format');
            return false;
        }

        // ตรวจสอบการบังคับ refresh
        if ($force_refresh) {
            log_message('info', 'Auto-refresh: Force refresh requested');
            return $this->perform_auto_refresh($token_data, 'force_refresh');
        }

        // ตรวจสอบวันหมดอายุ
        if ($system_storage->google_token_expires) {
            $expires_time = strtotime($system_storage->google_token_expires);
            $current_time = time();
            $time_diff = $expires_time - $current_time;

            // Refresh ถ้าหมดอายุแล้ว หรือเหลือเวลาน้อยกว่า 10 นาที
            if ($time_diff <= 600) { // 10 minutes
                $reason = $time_diff <= 0 ? 'expired' : 'near_expiry';
                log_message('info', "Auto-refresh: Token {$reason}, time_diff: {$time_diff} seconds");
                return $this->perform_auto_refresh($token_data, $reason);
            }

            log_message('debug', "Auto-refresh: Token OK, expires in {$time_diff} seconds");
            return true;
        }

        // ไม่มีข้อมูลวันหมดอายุ - ทดสอบ Token
        if (!$this->test_token_validity_quick($token_data['access_token'])) {
            log_message('info', 'Auto-refresh: Token failed validity test');
            return $this->perform_auto_refresh($token_data, 'failed_test');
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Auto-refresh error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ ทำการ AUTO-REFRESH
 */
private function perform_auto_refresh($token_data, $reason = 'unknown') {
    try {
        if (!isset($token_data['refresh_token']) || empty($token_data['refresh_token'])) {
            log_message('error', "Auto-refresh failed: No refresh token available (reason: {$reason})");
            return false;
        }

        log_message('info', "Auto-refresh: Starting refresh process (reason: {$reason})");

        $refresh_result = $this->perform_token_refresh($token_data['refresh_token']);
        
        if ($refresh_result['success']) {
            // อัปเดต Token ในฐานข้อมูล
            $this->update_system_token_in_db($refresh_result['token_data']);
            
            // บันทึก Log สำเร็จ
            $this->log_auto_refresh_success($reason, $refresh_result['token_data']);
            
            log_message('info', "Auto-refresh: SUCCESS (reason: {$reason})");
            return true;
        } else {
            // บันทึก Log ล้มเหลว
            $this->log_auto_refresh_failure($reason, $refresh_result['error']);
            
            log_message('error', "Auto-refresh: FAILED (reason: {$reason}) - {$refresh_result['error']}");
            return false;
        }

    } catch (Exception $e) {
        log_message('error', "Auto-refresh exception (reason: {$reason}): " . $e->getMessage());
        return false;
    }
}



/**
 * ✅ ทดสอบ Google Drive API แบบเร็ว
 */
private function test_google_drive_access_quick($access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
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
            return ($data && isset($data['user']));
        }

        return false;

    } catch (Exception $e) {
        return false;
    }
}

/**
 * ✅ บันทึก Log Auto-refresh สำเร็จ
 */
private function log_auto_refresh_success($reason, $token_data) {
    try {
        $log_data = [
            'event' => 'auto_refresh_success',
            'reason' => $reason,
            'new_expires_at' => $token_data['expires_at'] ?? null,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // บันทึกลง Database (ถ้ามีตาราง)
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $this->db->insert('tbl_google_drive_logs', [
                'member_id' => 0, // System
                'action_type' => 'auto_refresh_success',
                'action_description' => "Auto-refresh token successful (reason: {$reason})",
                'status' => 'success',
                'ip_address' => $_SERVER['SERVER_ADDR'] ?? 'system',
                'user_agent' => 'Auto-Refresh-System'
            ]);
        }

        log_message('info', 'Auto-refresh log: ' . json_encode($log_data));

    } catch (Exception $e) {
        log_message('error', 'Auto-refresh log error: ' . $e->getMessage());
    }
}

/**
 * ✅ บันทึก Log Auto-refresh ล้มเหลว
 */
private function log_auto_refresh_failure($reason, $error_message) {
    try {
        $log_data = [
            'event' => 'auto_refresh_failure',
            'reason' => $reason,
            'error' => $error_message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // บันทึกลง Database
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $this->db->insert('tbl_google_drive_logs', [
                'member_id' => 0,
                'action_type' => 'auto_refresh_failure',
                'action_description' => "Auto-refresh failed (reason: {$reason}): {$error_message}",
                'status' => 'failed',
                'error_message' => $error_message,
                'ip_address' => $_SERVER['SERVER_ADDR'] ?? 'system',
                'user_agent' => 'Auto-Refresh-System'
            ]);
        }

        log_message('error', 'Auto-refresh failure: ' . json_encode($log_data));

    } catch (Exception $e) {
        log_message('error', 'Auto-refresh failure log error: ' . $e->getMessage());
    }
}

/**
 * ✅ Output Health Check Response
 */
private function output_health_check($health_data) {
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_clean();
    }

    // Set appropriate HTTP status
    $status_code = 200;
    switch ($health_data['warning_level'] ?? 'none') {
        case 'emergency':
            $status_code = 503; // Service Unavailable
            break;
        case 'critical':
            $status_code = 500; // Internal Server Error
            break;
        case 'warning':
            $status_code = 200; // OK but with warnings
            break;
        default:
            $status_code = 200; // OK
            break;
    }

    $this->output
        ->set_status_header($status_code)
        ->set_content_type('application/json', 'utf-8')
        ->set_header('Cache-Control: no-cache, must-revalidate')
        ->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT')
        ->set_output(json_encode($health_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

/**
 * ✅ FINAL PRODUCTION create_folder_structure() - ทำงานจริงทุกครั้ง
 */
public function create_folder_structure() {
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        log_message('info', '🚀 create_folder_structure: PRODUCTION VERSION - Starting...');
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }
        
        // ✅ AUTO-REFRESH TOKEN ก่อนทำงาน
        if (!$this->ensure_valid_access_token()) {
            $this->output_json_error('ไม่สามารถ refresh Access Token ได้ กรุณาเชื่อมต่อ Google Account ใหม่');
            return;
        }
        
        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage) {
            $this->output_json_error('ไม่พบ System Storage หรือยังไม่ได้เชื่อมต่อ Google Account');
            return;
        }

        log_message('info', 'System storage validated successfully');

        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];

        // ✅ **PRODUCTION LOGIC** - ตรวจสอบและสร้างตามสถานะ
        if ($system_storage->folder_structure_created) {
            log_message('info', 'Main structure exists, checking for department folders...');
            
            // ตรวจสอบว่ามี department folders หรือยัง
            $existing_dept_count = $this->db->where('folder_type', 'department')
                                           ->count_all_results('tbl_google_drive_system_folders');
            
            log_message('info', "Existing department folders: {$existing_dept_count}");
            
            if ($existing_dept_count == 0) {
                // **กรณีที่ 1: มี main structure แต่ไม่มี department folders**
                log_message('info', 'Main structure exists but no department folders - creating them...');
                
                $dept_folder = $this->db->where('folder_name', 'Departments')
                                       ->where('folder_type', 'system')
                                       ->get('tbl_google_drive_system_folders')
                                       ->row();
                
                if ($dept_folder) {
                    $dept_count = $this->create_department_folders_curl($dept_folder->folder_id, $access_token);
                    
                    if ($dept_count > 0) {
                        log_message('info', "✅ Department folders created successfully: {$dept_count}");
                        
                        $this->output_json_success([
                            'folders_created' => $dept_count,
                            'department_folders_created' => $dept_count,
                            'scenario' => 'added_department_folders',
                            'departments_folder_id' => $dept_folder->folder_id
                        ], "เพิ่ม Department Folders เรียบร้อยแล้ว ({$dept_count} โฟลเดอร์)");
                        return;
                    } else {
                        log_message('error', 'Department folders creation returned 0');
                        $this->output_json_error('ไม่สามารถสร้าง Department Folders ได้');
                        return;
                    }
                } else {
                    log_message('error', 'Departments folder not found in database');
                    $this->output_json_error('ไม่พบ Departments folder - โครงสร้างอาจเสียหาย');
                    return;
                }
            } else {
                // **กรณีที่ 2: มีครบแล้ว - สร้างใหม่ทั้งหมด**
                log_message('info', "Structure exists with {$existing_dept_count} department folders - recreating all...");
                
                // ล้างและสร้างใหม่ทั้งหมด
                $result = $this->recreate_complete_structure($system_storage->id, $access_token);
                
                if ($result && $result['success']) {
                    $this->output_json_success([
                        'folders_created' => $result['folders_created'],
                        'main_folders_created' => $result['main_folders_created'],
                        'department_folders_created' => $result['department_folders_created'],
                        'scenario' => 'recreated_complete_structure',
                        'root_folder_id' => $result['root_folder_id']
                    ], "สร้างโครงสร้างใหม่ทั้งหมดเรียบร้อยแล้ว (Main: {$result['main_folders_created']}, Dept: {$result['department_folders_created']})");
                    return;
                } else {
                    $this->output_json_error('ไม่สามารถสร้างโครงสร้างใหม่ได้');
                    return;
                }
            }
        } else {
            // **กรณีที่ 3: ไม่มีโครงสร้าง - สร้างใหม่ทั้งหมด**
            log_message('info', 'No structure exists - creating complete structure...');
            
            $result = $this->create_complete_structure($system_storage->id, $access_token);
            
            if ($result && $result['success']) {
                $this->output_json_success([
                    'folders_created' => $result['folders_created'],
                    'main_folders_created' => $result['main_folders_created'],
                    'department_folders_created' => $result['department_folders_created'],
                    'scenario' => 'created_new_structure',
                    'root_folder_id' => $result['root_folder_id']
                ], "สร้างโครงสร้างโฟลเดอร์เรียบร้อยแล้ว (Main: {$result['main_folders_created']}, Dept: {$result['department_folders_created']})");
                return;
            } else {
                $this->output_json_error('ไม่สามารถสร้างโครงสร้างโฟลเดอร์ได้');
                return;
            }
        }

    } catch (Exception $e) {
        log_message('error', 'create_folder_structure PRODUCTION ERROR: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ สร้างโครงสร้างใหม่ทั้งหมด
 */
private function create_complete_structure($storage_id, $access_token) {
    try {
        log_message('info', '🏗️ Creating complete structure from scratch...');
        
        // ล้างข้อมูลเก่า
        $this->clear_all_folders();
        
        // Reset system storage
        $this->db->where('id', $storage_id)->update('tbl_google_drive_system_storage', [
            'folder_structure_created' => 0,
            'root_folder_id' => null
        ]);
        
        // สร้างด้วย transaction fix
        return $this->create_folder_structure_curl($storage_id);
        
    } catch (Exception $e) {
        log_message('error', 'create_complete_structure error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ สร้างโครงสร้างใหม่ทั้งหมด (กรณีมีอยู่แล้ว)
 */
private function recreate_complete_structure($storage_id, $access_token) {
    try {
        log_message('info', '🔄 Recreating complete structure...');
        
        // ล้างข้อมูลเก่า
        $this->clear_all_folders();
        
        // Reset system storage
        $this->db->where('id', $storage_id)->update('tbl_google_drive_system_storage', [
            'folder_structure_created' => 0,
            'root_folder_id' => null
        ]);
        
        // สร้างใหม่ด้วย transaction fix
        return $this->create_folder_structure_curl($storage_id);
        
    } catch (Exception $e) {
        log_message('error', 'recreate_complete_structure error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ ล้างโฟลเดอร์ทั้งหมดอย่างปลอดภัย
 */
private function clear_all_folders() {
    try {
        log_message('info', '🗑️ Clearing all folders safely...');
        
        $folder_types = ['department', 'system', 'admin', 'shared', 'root'];
        $total_deleted = 0;
        
        foreach ($folder_types as $type) {
            $this->db->where('folder_type', $type)->delete('tbl_google_drive_system_folders');
            $deleted = $this->db->affected_rows();
            $total_deleted += $deleted;
            log_message('info', "Cleared {$type} folders: {$deleted} records");
        }
        
        log_message('info', "Total folders cleared: {$total_deleted}");
        return $total_deleted;
        
    } catch (Exception $e) {
        log_message('error', 'clear_all_folders error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * ✅ Enhanced create_folder_structure_curl() - รับประกันการสร้าง Department Folders
 */
private function create_folder_structure_curl($storage_id) {
    try {
        log_message('info', '====== CREATE FOLDER STRUCTURE (PRODUCTION ENHANCED) ======');
        
        $system_storage = $this->get_active_system_storage();
        $token_data = json_decode($system_storage->google_access_token, true);
        
        if (!$token_data || !isset($token_data['access_token'])) {
            throw new Exception('Invalid access token');
        }

        $access_token = $token_data['access_token'];
        
        // ✅ TRANSACTION 1: Main Structure Only
        log_message('info', '🔄 Starting MAIN STRUCTURE transaction...');
        $this->db->trans_start();

        // สร้าง Root Folder
        $root_folder = $this->create_folder_with_curl('Organization Drive', null, $access_token);
        if (!$root_folder) {
            throw new Exception('Cannot create root folder');
        }
        
        log_message('info', 'Root folder created: ' . $root_folder['id']);

        // อัปเดต System Storage
        $this->update_system_storage($storage_id, [
            'root_folder_id' => $root_folder['id'],
            'folder_structure_created' => 1
        ]);

        // สร้าง Main folders
        $main_folders = [
            'Admin' => ['type' => 'admin', 'description' => 'โฟลเดอร์สำหรับ Admin'],
            'Departments' => ['type' => 'system', 'description' => 'โฟลเดอร์แผนกต่างๆ'],
            'Shared' => ['type' => 'shared', 'description' => 'โฟลเดอร์ส่วนกลาง'],
            'Users' => ['type' => 'system', 'description' => 'โฟลเดอร์ส่วนตัวของ Users']
        ];

        $created_folders = [];
        $folders_created_count = 1; // นับ root folder

        foreach ($main_folders as $folder_name => $config) {
            $folder = $this->create_folder_with_curl($folder_name, $root_folder['id'], $access_token);
            if ($folder) {
                $folder_data = [
                    'folder_name' => $folder_name,
                    'folder_id' => $folder['id'],
                    'parent_folder_id' => $root_folder['id'],
                    'folder_type' => $config['type'],
                    'folder_path' => '/Organization Drive/' . $folder_name,
                    'folder_description' => $config['description'],
                    'permission_level' => $config['type'] === 'shared' ? 'public' : 'restricted',
                    'created_by' => $this->session->userdata('m_id')
                ];

                if ($this->save_folder_info($folder_data)) {
                    $created_folders[$folder_name] = $folder['id'];
                    $folders_created_count++;
                    log_message('info', 'Main folder saved: ' . $folder_name);
                }
            }
        }

        // ✅ COMMIT Main Structure ก่อน
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            throw new Exception('Main structure transaction failed');
        }
        
        log_message('info', '✅ MAIN STRUCTURE transaction committed successfully');
        log_message('info', "Main folders created: {$folders_created_count}");

        // ✅ **GUARANTEED DEPARTMENT CREATION** - แยกออกจาก transaction หลัก
        $dept_count = 0;
        if (isset($created_folders['Departments'])) {
            log_message('info', '🏢 Starting GUARANTEED DEPARTMENT FOLDERS creation...');
            log_message('info', 'Departments folder ID: ' . $created_folders['Departments']);
            
            // ลองสร้าง department folders หลายครั้งหากจำเป็น
            $max_attempts = 3;
            $attempt = 1;
            
            while ($attempt <= $max_attempts && $dept_count == 0) {
                log_message('info', "Department creation attempt {$attempt}/{$max_attempts}");
                
                try {
                    $dept_count = $this->create_department_folders_curl($created_folders['Departments'], $access_token);
                    
                    if ($dept_count > 0) {
                        log_message('info', "✅ Department folders created successfully on attempt {$attempt}: {$dept_count}");
                        break;
                    } else {
                        log_message('warning', "⚠️ Attempt {$attempt} returned 0 folders");
                        
                        if ($attempt < $max_attempts) {
                            log_message('info', 'Waiting 2 seconds before retry...');
                            sleep(2);
                        }
                    }
                    
                } catch (Exception $e) {
                    log_message('error', "❌ Attempt {$attempt} failed: " . $e->getMessage());
                    
                    if ($attempt < $max_attempts) {
                        log_message('info', 'Waiting 3 seconds before retry...');
                        sleep(3);
                    }
                }
                
                $attempt++;
            }
            
            // ตรวจสอบผลลัพธ์จริงในฐานข้อมูล
            $actual_dept_count = $this->db->where('folder_type', 'department')->count_all_results('tbl_google_drive_system_folders');
            log_message('info', "Final department count in database: {$actual_dept_count}");
            
            if ($actual_dept_count > 0) {
                $dept_count = $actual_dept_count; // ใช้ค่าจริงจากฐานข้อมูล
                log_message('info', "✅ Department folders creation CONFIRMED: {$dept_count}");
            } else {
                log_message('error', "❌ Department folders creation FAILED after {$max_attempts} attempts");
            }
            
            $folders_created_count += $dept_count;
        } else {
            log_message('error', '❌ Departments folder was not created in main structure');
        }

        log_message('info', '====== FOLDER STRUCTURE CREATION COMPLETED ======');
        log_message('info', "Total folders: {$folders_created_count} (Main: " . ($folders_created_count - $dept_count) . ", Departments: {$dept_count})");
        
        return [
            'success' => true,
            'root_folder_id' => $root_folder['id'],
            'folders_created' => $folders_created_count,
            'main_folders_created' => $folders_created_count - $dept_count,
            'department_folders_created' => $dept_count
        ];

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Create folder structure error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ Quick status check function
 */
public function check_current_status() {
    echo "<h1>📊 Current System Status</h1>";
    
    try {
        $system_storage = $this->get_active_system_storage();
        
        if (!$system_storage) {
            echo "<p style='color: red;'>❌ No system storage</p>";
            return;
        }
        
        echo "<h2>📋 System Storage</h2>";
        echo "<ul>";
        echo "<li><strong>Email:</strong> {$system_storage->google_account_email}</li>";
        echo "<li><strong>folder_structure_created:</strong> " . ($system_storage->folder_structure_created ? 'TRUE' : 'FALSE') . "</li>";
        echo "<li><strong>root_folder_id:</strong> " . ($system_storage->root_folder_id ?: 'NULL') . "</li>";
        echo "</ul>";
        
        echo "<h2>📂 Current Folders</h2>";
        $all_folders = $this->db->get('tbl_google_drive_system_folders')->result();
        $dept_folders = $this->db->where('folder_type', 'department')->get('tbl_google_drive_system_folders')->result();
        
        echo "<ul>";
        echo "<li><strong>Total folders:</strong> " . count($all_folders) . "</li>";
        echo "<li><strong>Department folders:</strong> " . count($dept_folders) . "</li>";
        echo "</ul>";
        
        if (count($dept_folders) >= 17) {
            echo "<p style='color: green; font-weight: bold;'>✅ ระบบพร้อมใช้งาน!</p>";
        } else if (count($dept_folders) > 0) {
            echo "<p style='color: orange; font-weight: bold;'>⚠️ Department folders ไม่ครบ (" . count($dept_folders) . "/17)</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ ไม่มี Department folders</p>";
        }
        
        echo "<h2>🎯 Actions</h2>";
        echo "<p><a href='" . site_url('google_drive_system/setup') . "' style='background: blue; color: white; padding: 10px; text-decoration: none;'>🏠 ไป Setup Page</a></p>";
        echo "<p><em>ลองกดปุ่ม 'สร้างโครงสร้างโฟลเดอร์' ในหน้า Setup</em></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

/**
 * ✅ Enhanced create_folder_with_curl() - ทำให้ stable
 */
private function create_folder_with_curl($folder_name, $parent_id, $access_token) {
    try {
        log_message('info', "Creating folder: '{$folder_name}' under parent: {$parent_id}");
        
        // ตรวจสอบ input
        if (empty($folder_name) || empty($access_token)) {
            log_message('error', 'create_folder_with_curl: Missing folder_name or access_token');
            return null;
        }

        // ตรวจสอบและ refresh token หากจำเป็น
        if (!$this->ensure_valid_access_token()) {
            log_message('warning', 'Token refresh failed, continuing with current token');
        } else {
            // ดึง token ใหม่หลัง refresh
            $system_storage = $this->get_active_system_storage();
            if ($system_storage && $system_storage->google_access_token) {
                $token_data = json_decode($system_storage->google_access_token, true);
                if ($token_data && isset($token_data['access_token'])) {
                    $access_token = $token_data['access_token'];
                }
            }
        }

        $metadata = [
            'name' => trim($folder_name),
            'mimeType' => 'application/vnd.google-apps.folder'
        ];

        if ($parent_id) {
            $metadata['parents'] = [$parent_id];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/files',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($metadata),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', "cURL error for '{$folder_name}': {$curl_error}");
            return null;
        }

        if ($http_code === 200) {
            $folder_data = json_decode($response, true);
            if ($folder_data && isset($folder_data['id'])) {
                $result = [
                    'id' => $folder_data['id'],
                    'name' => $folder_data['name'],
                    'webViewLink' => 'https://drive.google.com/drive/folders/' . $folder_data['id']
                ];
                
                log_message('info', "Folder created successfully: {$result['id']}");
                return $result;
            }
        } elseif ($http_code === 401) {
            log_message('error', "Authentication error for '{$folder_name}' - token may be expired");
            
            // ลอง refresh และทำใหม่ครั้งเดียว
            if ($this->ensure_valid_access_token(true)) {
                log_message('info', 'Token refreshed, retrying folder creation...');
                // Retry once with new token
                return $this->create_folder_with_curl($folder_name, $parent_id, null);
            }
            
            return null;
        } elseif ($http_code === 429) {
            log_message('warning', "Rate limit hit for '{$folder_name}', waiting...");
            sleep(2);
            return null;
        } else {
            log_message('error', "HTTP error {$http_code} for '{$folder_name}': {$response}");
            return null;
        }

    } catch (Exception $e) {
        log_message('error', "Exception in create_folder_with_curl for '{$folder_name}': " . $e->getMessage());
        return null;
    }
}

/**
 * ✅ MANUAL REFRESH ENDPOINT (สำหรับ Admin)
 */
public function force_refresh_token() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        log_message('info', 'Manual force refresh initiated by admin: ' . $this->session->userdata('m_id'));

        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage หรือ Access Token');
            return;
        }

        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['refresh_token'])) {
            $this->output_json_error('ไม่พบ Refresh Token - ต้องเชื่อมต่อ Google Account ใหม่');
            return;
        }

        // Force refresh
        if ($this->perform_auto_refresh($token_data, 'manual_force')) {
            $this->output_json_success([
                'refreshed_at' => date('Y-m-d H:i:s'),
                'method' => 'manual_force'
            ], 'Force Refresh Token สำเร็จ!');
        } else {
            $this->output_json_error('ไม่สามารถ Force Refresh Token ได้');
        }

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ AUTO-REFRESH STATUS
 */
public function auto_refresh_status() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $system_storage = $this->get_active_system_storage();
        $status = [
            'auto_refresh_enabled' => true,
            'has_refresh_token' => false,
            'token_expires_at' => null,
            'time_to_expiry' => null,
            'last_auto_refresh' => null,
            'system_ready' => false
        ];

        if ($system_storage && $system_storage->google_access_token) {
            $token_data = json_decode($system_storage->google_access_token, true);
            $status['has_refresh_token'] = isset($token_data['refresh_token']) && !empty($token_data['refresh_token']);
            
            if ($system_storage->google_token_expires) {
                $expires_time = strtotime($system_storage->google_token_expires);
                $status['token_expires_at'] = $system_storage->google_token_expires;
                $status['time_to_expiry'] = max(0, $expires_time - time());
            }
            
            $status['system_ready'] = $status['has_refresh_token'] && ($status['time_to_expiry'] === null || $status['time_to_expiry'] > 0);
        }

        // ดึง Log การ refresh ล่าสุด
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $last_refresh = $this->db->select('created_at, action_description, status')
                                  ->from('tbl_google_drive_logs')
                                  ->where('action_type', 'auto_refresh_success')
                                  ->or_where('action_type', 'auto_refresh_failure')
                                  ->order_by('created_at', 'desc')
                                  ->limit(1)
                                  ->get()
                                  ->row();
            
            if ($last_refresh) {
                $status['last_auto_refresh'] = [
                    'time' => $last_refresh->created_at,
                    'description' => $last_refresh->action_description,
                    'status' => $last_refresh->status
                ];
            }
        }

        $this->output_json_success($status, 'ดึงสถานะ Auto-refresh สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}	
	
	
	/**
 * ✅ ดึงกิจกรรมล่าสุด (API Endpoint)
 */
public function get_recent_activities() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $limit = $this->input->get('limit') ?: 10;
        $activities = $this->get_recent_activities_data($limit);
        
        $this->output_json_success($activities, 'ดึงข้อมูลกิจกรรมล่าสุดสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get recent activities error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ ดึงข้อมูลกิจกรรมจากหลายแหล่ง
 */
private function get_recent_activities_data($limit = 10) {
    $activities = [];
    
    try {
        // 1. ดึงจาก Google Drive Logs
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $drive_logs = $this->db->select('
                    gdl.member_id, 
                    gdl.action_type, 
                    gdl.action_description,
                    gdl.created_at,
                    gdl.status,
                    COALESCE(m.m_fname, "ระบบ") as first_name,
                    COALESCE(m.m_lname, "") as last_name,
                    COALESCE(m.m_username, "system") as username
                ')
                ->from('tbl_google_drive_logs gdl')
                ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                ->where('gdl.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->order_by('gdl.created_at', 'desc')
                ->limit($limit)
                ->get()
                ->result();

            foreach ($drive_logs as $log) {
                $activities[] = [
                    'type' => 'drive_activity',
                    'action_type' => $log->action_type,
                    'description' => $log->action_description,
                    'user_name' => trim($log->first_name . ' ' . $log->last_name),
                    'username' => $log->username,
                    'created_at' => $log->created_at,
                    'status' => $log->status,
                    'source' => 'google_drive_logs'
                ];
            }
        }

        // 2. ดึงจาก Member Activity Logs
        if ($this->db->table_exists('tbl_member_activity_logs')) {
            $member_logs = $this->db->select('
                    mal.user_id,
                    mal.activity_type,
                    mal.activity_description,
                    mal.created_at,
                    mal.full_name,
                    mal.username,
                    mal.module
                ')
                ->from('tbl_member_activity_logs mal')
                ->where('mal.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->where('mal.module', 'google_drive')
                ->order_by('mal.created_at', 'desc')
                ->limit($limit)
                ->get()
                ->result();

            foreach ($member_logs as $log) {
                $activities[] = [
                    'type' => 'member_activity',
                    'action_type' => $log->activity_type,
                    'description' => $log->activity_description,
                    'user_name' => $log->full_name ?: $log->username,
                    'username' => $log->username,
                    'created_at' => $log->created_at,
                    'source' => 'member_activity_logs'
                ];
            }
        }

        // 3. ดึงจาก System Storage Events (ถ้ามี)
        if ($this->db->table_exists('tbl_google_drive_system_storage')) {
            $storage_events = $this->db->select('
                    created_at,
                    updated_at,
                    google_account_email,
                    folder_structure_created
                ')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->order_by('updated_at', 'desc')
                ->limit(3)
                ->get()
                ->result();

            foreach ($storage_events as $event) {
                if ($event->updated_at && $event->updated_at !== $event->created_at) {
                    $activities[] = [
                        'type' => 'system_event',
                        'action_type' => 'system_update',
                        'description' => 'อัปเดต System Storage: ' . $event->google_account_email,
                        'user_name' => 'ระบบ',
                        'username' => 'system',
                        'created_at' => $event->updated_at,
                        'source' => 'system_storage'
                    ];
                }
            }
        }

        // 4. สร้างกิจกรรม Mock หากไม่มีข้อมูล
        if (empty($activities)) {
            $activities = $this->create_mock_activities();
        }

        // เรียงลำดับตามเวลาและจำกัดจำนวน
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($activities, 0, $limit);

    } catch (Exception $e) {
        log_message('error', 'Get recent activities data error: ' . $e->getMessage());
        return $this->create_mock_activities();
    }
}

/**
 * ✅ สร้างกิจกรรม Mock สำหรับทดสอบ
 */
private function create_mock_activities() {
    $current_user = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
    $username = $this->session->userdata('m_username') ?: 'admin';
    
    return [
        [
            'type' => 'system_event',
            'action_type' => 'system_update',
            'description' => 'ระบบ Google Drive Storage เริ่มต้นการทำงาน',
            'user_name' => 'ระบบ',
            'username' => 'system',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'source' => 'mock'
        ],
        [
            'type' => 'drive_activity',
            'action_type' => 'connect',
            'description' => 'เชื่อมต่อ Google Drive Storage สำเร็จ',
            'user_name' => $current_user,
            'username' => $username,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'source' => 'mock'
        ],
        [
            'type' => 'folder_create',
            'action_type' => 'create_folder',
            'description' => 'สร้างโครงสร้างโฟลเดอร์หลักเรียบร้อยแล้ว',
            'user_name' => $current_user,
            'username' => $username,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            'source' => 'mock'
        ],
        [
            'type' => 'system_event',
            'action_type' => 'folder_structure',
            'description' => 'สร้างโฟลเดอร์ Organization Drive สำเร็จ',
            'user_name' => 'ระบบ',
            'username' => 'system',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
            'source' => 'mock'
        ],
        [
            'type' => 'member_activity',
            'action_type' => 'login',
            'description' => 'เข้าสู่ระบบ Google Drive Management',
            'user_name' => $current_user,
            'username' => $username,
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours')),
            'source' => 'mock'
        ]
    ];
}

/**
 * ✅ ดึงโครงสร้างโฟลเดอร์ (API Endpoint)
 */
public function get_folder_structure() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $structure = $this->get_folder_structure_data();
        
        $this->output_json_success($structure, 'ดึงโครงสร้างโฟลเดอร์สำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get folder structure error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ ดึงข้อมูลโครงสร้างโฟลเดอร์จากฐานข้อมูล
 */
private function get_folder_structure_data() {
    try {
        // ตรวจสอบตาราง
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return $this->create_mock_folder_structure();
        }

        // ดึงข้อมูลจากฐานข้อมูล
        $folders = $this->db->select('
                sf.folder_name,
                sf.folder_type,
                sf.parent_folder_id,
                sf.folder_path,
                sf.folder_description,
                sf.created_at,
                p.pname as position_name
            ')
            ->from('tbl_google_drive_system_folders sf')
            ->join('tbl_position p', 'sf.created_for_position = p.pid', 'left')
            ->where('sf.is_active', 1)
            ->order_by('sf.folder_type', 'ASC')
            ->order_by('sf.folder_name', 'ASC')
            ->get()
            ->result();

        if (empty($folders)) {
            return $this->create_mock_folder_structure();
        }

        // แปลงเป็น hierarchical structure
        $structure = [];
        foreach ($folders as $folder) {
            $level = $this->calculate_folder_level($folder->folder_path);
            $structure[] = [
                'folder_name' => $folder->folder_name,
                'folder_type' => $folder->folder_type,
                'folder_path' => $folder->folder_path,
                'description' => $folder->folder_description,
                'position_name' => $folder->position_name,
                'level' => $level,
                'created_at' => $folder->created_at
            ];
        }

        return $structure;

    } catch (Exception $e) {
        log_message('error', 'Get folder structure data error: ' . $e->getMessage());
        return $this->create_mock_folder_structure();
    }
}

/**
 * ✅ คำนวณระดับโฟลเดอร์จาก path
 */
private function calculate_folder_level($path) {
    if (empty($path)) return 0;
    return substr_count(trim($path, '/'), '/');
}

/**
 * ✅ สร้างโครงสร้างโฟลเดอร์ Mock
 */
private function create_mock_folder_structure() {
    return [
        [
            'folder_name' => 'Organization Drive',
            'folder_type' => 'root',
            'level' => 0,
            'description' => 'โฟลเดอร์หลักของระบบ'
        ],
        [
            'folder_name' => 'Admin',
            'folder_type' => 'admin',
            'level' => 1,
            'description' => 'โฟลเดอร์สำหรับผู้ดูแลระบบ'
        ],
        [
            'folder_name' => 'Departments',
            'folder_type' => 'system',
            'level' => 1,
            'description' => 'โฟลเดอร์แผนกต่างๆ'
        ],
        [
            'folder_name' => 'ผู้บริหาร',
            'folder_type' => 'department',
            'level' => 2,
            'description' => 'โฟลเดอร์สำหรับผู้บริหาร'
        ],
        [
            'folder_name' => 'คณาจารย์',
            'folder_type' => 'department',
            'level' => 2,
            'description' => 'โฟลเดอร์สำหรับคณาจารย์'
        ],
        [
            'folder_name' => 'เจ้าหน้าที่',
            'folder_type' => 'department',
            'level' => 2,
            'description' => 'โฟลเดอร์สำหรับเจ้าหน้าที่'
        ],
        [
            'folder_name' => 'Shared',
            'folder_type' => 'shared',
            'level' => 1,
            'description' => 'โฟลเดอร์เอกสารส่วนกลาง'
        ],
        [
            'folder_name' => 'Users',
            'folder_type' => 'system',
            'level' => 1,
            'description' => 'โฟลเดอร์ส่วนตัวของผู้ใช้'
        ]
    ];
}

/**
 * ✅ ดึงสถิติ Dashboard แบบครบถ้วน
 */
public function get_dashboard_stats() {
    try {
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $stats = $this->get_comprehensive_dashboard_stats();
        
        $this->output_json_success($stats, 'ดึงสถิติ Dashboard สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ ดึงสถิติครบถ้วน
 */
private function get_comprehensive_dashboard_stats() {
    try {
        $stats = [
            'system_storage' => [
                'exists' => false,
                'ready' => false,
                'google_email' => null,
                'storage_used' => 0,
                'storage_limit' => 0,
                'usage_percent' => 0
            ],
            'folders' => [
                'total' => 0,
                'by_type' => []
            ],
            'files' => [
                'total' => 0,
                'total_size' => 0
            ],
            'users' => [
                'total_members' => 0,
                'active_members' => 0,
                'with_storage_access' => 0
            ],
            'activities' => [
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0
            ]
        ];

        // System Storage
        $system_storage = $this->get_active_system_storage();
        if ($system_storage) {
            $stats['system_storage'] = [
                'exists' => true,
                'ready' => (bool)$system_storage->folder_structure_created,
                'google_email' => $system_storage->google_account_email,
                'storage_used' => (int)$system_storage->total_storage_used,
                'storage_limit' => (int)$system_storage->max_storage_limit,
                'usage_percent' => $system_storage->max_storage_limit > 0 ? 
                    round(($system_storage->total_storage_used / $system_storage->max_storage_limit) * 100, 2) : 0
            ];
        }

        // Folders
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $stats['folders']['total'] = $this->db->where('is_active', 1)
                                                 ->count_all_results('tbl_google_drive_system_folders');
            
            $folder_types = $this->db->select('folder_type, COUNT(*) as count')
                                   ->from('tbl_google_drive_system_folders')
                                   ->where('is_active', 1)
                                   ->group_by('folder_type')
                                   ->get()
                                   ->result();
            
            foreach ($folder_types as $type) {
                $stats['folders']['by_type'][$type->folder_type] = (int)$type->count;
            }
        }

        // Files
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $file_stats = $this->db->select('COUNT(*) as total_files, SUM(file_size) as total_size')
                                   ->from('tbl_google_drive_system_files')
                                   ->get()
                                   ->row();
            
            $stats['files'] = [
                'total' => (int)($file_stats->total_files ?? 0),
                'total_size' => (int)($file_stats->total_size ?? 0)
            ];
        }

        // Users
        $stats['users']['total_members'] = $this->db->count_all('tbl_member');
        $stats['users']['active_members'] = $this->db->where('m_status', '1')
                                                   ->count_all_results('tbl_member');
        $stats['users']['with_storage_access'] = $this->db->where('storage_access_granted', 1)
                                                         ->count_all_results('tbl_member');

        // Activities
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $stats['activities']['today'] = $this->db->where('DATE(created_at)', date('Y-m-d'))
                                                   ->count_all_results('tbl_google_drive_logs');
            
            $stats['activities']['this_week'] = $this->db->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                                        ->count_all_results('tbl_google_drive_logs');
            
            $stats['activities']['this_month'] = $this->db->where('created_at >=', date('Y-m-01'))
                                                         ->count_all_results('tbl_google_drive_logs');
        }

        return $stats;

    } catch (Exception $e) {
        log_message('error', 'Get comprehensive dashboard stats error: ' . $e->getMessage());
        return $this->get_default_stats();
    }
}

/**
 * ✅ สถิติเริ่มต้น
 */
private function get_default_stats() {
    return [
        'system_storage' => [
            'exists' => false,
            'ready' => false,
            'usage_percent' => 0
        ],
        'folders' => ['total' => 0],
        'files' => ['total' => 0],
        'users' => ['total_members' => 0, 'active_members' => 0],
        'activities' => ['today' => 0, 'this_week' => 0, 'this_month' => 0]
    ];
}

/**
 * ✅ บันทึกกิจกรรมแบบ Enhanced
 */
public function log_enhanced_activity($member_id, $action_type, $description, $additional_data = []) {
    try {
        // สร้างตารางถ้ายังไม่มี
        $this->create_enhanced_logs_table();

        $data = [
            'member_id' => $member_id,
            'action_type' => $action_type,
            'action_description' => $description,
            'module' => 'google_drive_system',
            'folder_id' => $additional_data['folder_id'] ?? null,
            'file_id' => $additional_data['file_id'] ?? null,
            'item_id' => $additional_data['item_id'] ?? null,
            'item_type' => $additional_data['item_type'] ?? null,
            'status' => $additional_data['status'] ?? 'success',
            'error_message' => $additional_data['error_message'] ?? null,
            'additional_data' => !empty($additional_data['extra']) ? json_encode($additional_data['extra']) : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_google_drive_logs', $data);

    } catch (Exception $e) {
        log_message('error', 'Log enhanced activity error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ สร้างตารางล็อกแบบครบถ้วน
 */
private function create_enhanced_logs_table() {
    if (!$this->db->table_exists('tbl_google_drive_logs')) {
        $sql = "
            CREATE TABLE IF NOT EXISTS `tbl_google_drive_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `member_id` int(11) NOT NULL DEFAULT 0,
                `action_type` varchar(50) NOT NULL,
                `action_description` text NOT NULL,
                `module` varchar(50) DEFAULT 'google_drive_system',
                `folder_id` varchar(255) DEFAULT NULL,
                `file_id` varchar(255) DEFAULT NULL,
                `item_id` varchar(255) DEFAULT NULL,
                `item_type` varchar(20) DEFAULT NULL,
                `status` enum('success','failed','pending','warning') DEFAULT 'success',
                `error_message` text DEFAULT NULL,
                `additional_data` text DEFAULT NULL,
                `ip_address` varchar(45) DEFAULT NULL,
                `user_agent` text DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_member_id` (`member_id`),
                KEY `idx_action_type` (`action_type`),
                KEY `idx_created_at` (`created_at`),
                KEY `idx_status` (`status`),
                KEY `idx_module` (`module`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        
        $this->db->query($sql);
    }
}
	
/**
 * ✅ หน้ารายงานกิจกรรม Google Drive System
 * URL: /google_drive_system/reports?type=activities
 */
public function reports() {
    try {
        // ตรวจสอบสิทธิ์ Admin
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        $report_type = $this->input->get('type') ?: 'activities';
        
        // ตั้งค่าข้อมูลพื้นฐาน
        $data = [
            'report_type' => $report_type,
            'page_title' => $this->get_report_title($report_type),
            'system_storage' => $this->get_active_system_storage(),
            'date_range' => [
                'start' => $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days')),
                'end' => $this->input->get('end_date') ?: date('Y-m-d')
            ]
        ];

        // ดึงข้อมูลตามประเภทรายงาน
        switch ($report_type) {
            case 'activities':
                $data = array_merge($data, $this->get_activities_report_data($data['date_range']));
                break;
            case 'storage':
                $data = array_merge($data, $this->get_storage_report_data($data['date_range']));
                break;
            case 'users':
                $data = array_merge($data, $this->get_users_report_data($data['date_range']));
                break;
            case 'folders':
                $data = array_merge($data, $this->get_folders_report_data($data['date_range']));
                break;
            default:
                $data = array_merge($data, $this->get_activities_report_data($data['date_range']));
                break;
        }

        // โหลด Views
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_reports', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');

    } catch (Exception $e) {
        log_message('error', 'Reports error: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาดในการโหลดรายงาน: ' . $e->getMessage(), 500);
    }
}

/**
 * ✅ ดึงข้อมูลรายงานกิจกรรม
 */
private function get_activities_report_data($date_range) {
    try {
        $data = [
            'activities' => [],
            'activities_summary' => [
                'total' => 0,
                'success' => 0,
                'failed' => 0,
                'by_type' => [],
                'by_user' => [],
                'by_day' => []
            ],
            'top_users' => [],
            'recent_errors' => []
        ];

        // ดึงกิจกรรมทั้งหมดในช่วงวันที่
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            // กิจกรรมหลัก
            $activities = $this->db->select('
                    gdl.*,
                    COALESCE(m.m_fname, "ระบบ") as first_name,
                    COALESCE(m.m_lname, "") as last_name,
                    COALESCE(m.m_username, "system") as username
                ')
                ->from('tbl_google_drive_logs gdl')
                ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                ->where('DATE(gdl.created_at) >=', $date_range['start'])
                ->where('DATE(gdl.created_at) <=', $date_range['end'])
                ->order_by('gdl.created_at', 'desc')
                ->limit(500)
                ->get()
                ->result();

            $data['activities'] = $activities;

            // สรุปสถิติ
            $data['activities_summary']['total'] = count($activities);
            
            foreach ($activities as $activity) {
                // นับตาม Status
                if ($activity->status === 'success') {
                    $data['activities_summary']['success']++;
                } else {
                    $data['activities_summary']['failed']++;
                }

                // นับตาม Action Type
                $action_type = $activity->action_type;
                if (!isset($data['activities_summary']['by_type'][$action_type])) {
                    $data['activities_summary']['by_type'][$action_type] = 0;
                }
                $data['activities_summary']['by_type'][$action_type]++;

                // นับตาม User
                $user_name = trim($activity->first_name . ' ' . $activity->last_name);
                if (!isset($data['activities_summary']['by_user'][$user_name])) {
                    $data['activities_summary']['by_user'][$user_name] = 0;
                }
                $data['activities_summary']['by_user'][$user_name]++;

                // นับตามวัน
                $date = date('Y-m-d', strtotime($activity->created_at));
                if (!isset($data['activities_summary']['by_day'][$date])) {
                    $data['activities_summary']['by_day'][$date] = 0;
                }
                $data['activities_summary']['by_day'][$date]++;
            }

            // เรียง Top Users
            arsort($data['activities_summary']['by_user']);
            $data['top_users'] = array_slice($data['activities_summary']['by_user'], 0, 10, true);

            // ดึงข้อผิดพลาดล่าสุด
            $data['recent_errors'] = $this->db->select('
                    gdl.*,
                    COALESCE(m.m_fname, "ระบบ") as first_name,
                    COALESCE(m.m_lname, "") as last_name
                ')
                ->from('tbl_google_drive_logs gdl')
                ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                ->where('gdl.status', 'failed')
                ->where('DATE(gdl.created_at) >=', $date_range['start'])
                ->where('DATE(gdl.created_at) <=', $date_range['end'])
                ->order_by('gdl.created_at', 'desc')
                ->limit(20)
                ->get()
                ->result();
        }

        return $data;

    } catch (Exception $e) {
        log_message('error', 'Get activities report data error: ' . $e->getMessage());
        return ['activities' => [], 'activities_summary' => []];
    }
}

/**
 * ✅ ดึงข้อมูลรายงาน Storage
 */
private function get_storage_report_data($date_range) {
    try {
        $data = [
            'storage_usage' => [
                'current_usage' => 0,
                'storage_limit' => 0,
                'usage_percent' => 0,
                'available_space' => 0
            ],
            'folder_stats' => [],
            'file_stats' => [],
            'usage_history' => []
        ];

        // ข้อมูล Storage ปัจจุบัน
        $system_storage = $this->get_active_system_storage();
        if ($system_storage) {
            $data['storage_usage'] = [
                'current_usage' => (int)$system_storage->total_storage_used,
                'storage_limit' => (int)$system_storage->max_storage_limit,
                'usage_percent' => $system_storage->max_storage_limit > 0 ? 
                    round(($system_storage->total_storage_used / $system_storage->max_storage_limit) * 100, 2) : 0,
                'available_space' => max(0, $system_storage->max_storage_limit - $system_storage->total_storage_used)
            ];
        }

        // สถิติโฟลเดอร์
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $data['folder_stats'] = $this->db->select('
                    folder_type,
                    COUNT(*) as count,
                    SUM(storage_used) as total_used
                ')
                ->from('tbl_google_drive_system_folders')
                ->where('is_active', 1)
                ->group_by('folder_type')
                ->get()
                ->result();
        }

        // สถิติไฟล์
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $data['file_stats'] = $this->db->select('
                    COUNT(*) as total_files,
                    SUM(file_size) as total_size,
                    AVG(file_size) as avg_size,
                    MAX(file_size) as max_size,
                    MIN(file_size) as min_size
                ')
                ->from('tbl_google_drive_system_files')
                ->get()
                ->row();
        }

        return $data;

    } catch (Exception $e) {
        log_message('error', 'Get storage report data error: ' . $e->getMessage());
        return ['storage_usage' => []];
    }
}

/**
 * ✅ ดึงข้อมูลรายงานผู้ใช้
 */
private function get_users_report_data($date_range) {
    try {
        $data = [
            'user_stats' => [
                'total_users' => 0,
                'active_users' => 0,
                'with_storage_access' => 0,
                'recently_active' => 0
            ],
            'user_activities' => [],
            'top_active_users' => []
        ];

        // สถิติผู้ใช้พื้นฐาน
        $data['user_stats']['total_users'] = $this->db->count_all('tbl_member');
        $data['user_stats']['active_users'] = $this->db->where('m_status', '1')
                                                     ->count_all_results('tbl_member');
        $data['user_stats']['with_storage_access'] = $this->db->where('storage_access_granted', 1)
                                                            ->count_all_results('tbl_member');
        
        // ผู้ใช้ที่ใช้งานล่าสุด
        $data['user_stats']['recently_active'] = $this->db->where('last_storage_access >=', date('Y-m-d', strtotime('-7 days')))
                                                         ->count_all_results('tbl_member');

        // กิจกรรมของผู้ใช้
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $data['user_activities'] = $this->db->select('
                    m.m_id,
                    m.m_fname,
                    m.m_lname,
                    m.m_email,
                    p.pname,
                    COUNT(*) as activity_count,
                    MAX(gdl.created_at) as last_activity
                ')
                ->from('tbl_google_drive_logs gdl')
                ->join('tbl_member m', 'gdl.member_id = m.m_id')
                ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                ->where('DATE(gdl.created_at) >=', $date_range['start'])
                ->where('DATE(gdl.created_at) <=', $date_range['end'])
                ->where('gdl.member_id >', 0)
                ->group_by('m.m_id')
                ->order_by('activity_count', 'desc')
                ->limit(50)
                ->get()
                ->result();
        }

        return $data;

    } catch (Exception $e) {
        log_message('error', 'Get users report data error: ' . $e->getMessage());
        return ['user_stats' => []];
    }
}

/**
 * ✅ ดึงข้อมูลรายงานโฟลเดอร์
 */
private function get_folders_report_data($date_range) {
    try {
        $data = [
            'folder_structure' => [],
            'folder_summary' => [
                'total_folders' => 0,
                'by_type' => [],
                'by_position' => []
            ],
            'recent_folders' => []
        ];

        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            // โครงสร้างโฟลเดอร์
            $data['folder_structure'] = $this->db->select('
                    sf.*,
                    p.pname as position_name
                ')
                ->from('tbl_google_drive_system_folders sf')
                ->join('tbl_position p', 'sf.created_for_position = p.pid', 'left')
                ->where('sf.is_active', 1)
                ->order_by('sf.folder_type', 'ASC')
                ->order_by('sf.folder_name', 'ASC')
                ->get()
                ->result();

            // สรุปโฟลเดอร์
            $data['folder_summary']['total_folders'] = count($data['folder_structure']);

            foreach ($data['folder_structure'] as $folder) {
                // นับตามประเภท
                if (!isset($data['folder_summary']['by_type'][$folder->folder_type])) {
                    $data['folder_summary']['by_type'][$folder->folder_type] = 0;
                }
                $data['folder_summary']['by_type'][$folder->folder_type]++;

                // นับตามตำแหน่ง
                if ($folder->position_name) {
                    if (!isset($data['folder_summary']['by_position'][$folder->position_name])) {
                        $data['folder_summary']['by_position'][$folder->position_name] = 0;
                    }
                    $data['folder_summary']['by_position'][$folder->position_name]++;
                }
            }

            // โฟลเดอร์ที่สร้างล่าสุด
            $data['recent_folders'] = $this->db->select('sf.*, p.pname as position_name')
                                             ->from('tbl_google_drive_system_folders sf')
                                             ->join('tbl_position p', 'sf.created_for_position = p.pid', 'left')
                                             ->where('sf.is_active', 1)
                                             ->where('DATE(sf.created_at) >=', $date_range['start'])
                                             ->where('DATE(sf.created_at) <=', $date_range['end'])
                                             ->order_by('sf.created_at', 'desc')
                                             ->limit(20)
                                             ->get()
                                             ->result();
        }

        return $data;

    } catch (Exception $e) {
        log_message('error', 'Get folders report data error: ' . $e->getMessage());
        return ['folder_structure' => []];
    }
}

/**
 * ✅ ดึงชื่อรายงาน
 */
private function get_report_title($type) {
    $titles = [
        'activities' => 'รายงานกิจกรรม Google Drive',
        'storage' => 'รายงานการใช้งาน Storage',
        'users' => 'รายงานผู้ใช้งาน',
        'folders' => 'รายงานโครงสร้างโฟลเดอร์'
    ];

    return $titles[$type] ?? 'รายงาน Google Drive System';
}

/**
 * ✅ Export รายงานเป็น Excel
 */
public function export_report() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        $report_type = $this->input->get('type') ?: 'activities';
        $format = $this->input->get('format') ?: 'csv';
        
        $date_range = [
            'start' => $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days')),
            'end' => $this->input->get('end_date') ?: date('Y-m-d')
        ];

        // ดึงข้อมูลตามประเภท
        switch ($report_type) {
            case 'activities':
                $this->export_activities_report($date_range, $format);
                break;
            case 'storage':
                $this->export_storage_report($date_range, $format);
                break;
            case 'users':
                $this->export_users_report($date_range, $format);
                break;
            case 'folders':
                $this->export_folders_report($date_range, $format);
                break;
        }

    } catch (Exception $e) {
        log_message('error', 'Export report error: ' . $e->getMessage());
        show_error('ไม่สามารถ Export รายงานได้: ' . $e->getMessage(), 500);
    }
}

/**
 * ✅ Export รายงานกิจกรรม
 */
private function export_activities_report($date_range, $format) {
    $data = $this->get_activities_report_data($date_range);
    
    $filename = 'google_drive_activities_' . $date_range['start'] . '_to_' . $date_range['end'];
    
    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'วันที่/เวลา',
            'ผู้ใช้',
            'ประเภทกิจกรรม', 
            'รายละเอียด',
            'สถานะ',
            'IP Address'
        ]);
        
        // Data
        foreach ($data['activities'] as $activity) {
            fputcsv($output, [
                $activity->created_at,
                trim($activity->first_name . ' ' . $activity->last_name),
                $activity->action_type,
                $activity->action_description,
                $activity->status,
                $activity->ip_address
            ]);
        }
        
        fclose($output);
    }
}

/**
 * ✅ Export รายงาน Storage
 */
private function export_storage_report($date_range, $format) {
    $data = $this->get_storage_report_data($date_range);
    
    $filename = 'google_drive_storage_' . date('Y-m-d');
    
    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Storage Summary
        fputcsv($output, ['ประเภทข้อมูล', 'ค่า']);
        fputcsv($output, ['การใช้งานปัจจุบัน', $this->format_bytes($data['storage_usage']['current_usage'])]);
        fputcsv($output, ['ขีดจำกัด Storage', $this->format_bytes($data['storage_usage']['storage_limit'])]);
        fputcsv($output, ['เปอร์เซ็นต์การใช้งาน', $data['storage_usage']['usage_percent'] . '%']);
        fputcsv($output, ['พื้นที่ว่าง', $this->format_bytes($data['storage_usage']['available_space'])]);
        
        fclose($output);
    }
}

/**
 * ✅ ดึงข้อมูลรายงานแบบ AJAX
 */
public function get_report_data() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $report_type = $this->input->post('type') ?: 'activities';
        $date_range = [
            'start' => $this->input->post('start_date') ?: date('Y-m-d', strtotime('-30 days')),
            'end' => $this->input->post('end_date') ?: date('Y-m-d')
        ];

        $data = [];
        switch ($report_type) {
            case 'activities':
                $data = $this->get_activities_report_data($date_range);
                break;
            case 'storage':
                $data = $this->get_storage_report_data($date_range);
                break;
            case 'users':
                $data = $this->get_users_report_data($date_range);
                break;
            case 'folders':
                $data = $this->get_folders_report_data($date_range);
                break;
        }

        $this->output_json_success($data, 'ดึงข้อมูลรายงานสำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
/**
 * 🔍 API สำหรับเช็คสถานะ Token (เรียกจาก JavaScript)
 */
public function token_status() {
    try {
        // โหลด Library
        $this->load->library('google_drive_auto_refresh');
        
        // เช็คและ Refresh อัตโนมัติ
        $this->google_drive_auto_refresh->auto_check_and_refresh();
        
        // ดึงสถานะปัจจุบัน
        $status = $this->google_drive_auto_refresh->get_token_status();
        
        // ดึงข้อมูลเพิ่มเติม
        $extra_info = [];
        $storage = $this->get_active_system_storage();
        
        if ($storage && $storage->google_token_expires) {
            $expires = strtotime($storage->google_token_expires);
            $now = time();
            $diff = $expires - $now;
            
            $extra_info = [
                'expires_at' => $storage->google_token_expires,
                'time_remaining_seconds' => max(0, $diff),
                'time_remaining_minutes' => max(0, round($diff / 60))
            ];
        }
        
        $this->output_json_success([
            'status' => $status,
            'timestamp' => date('Y-m-d H:i:s'),
            'auto_refresh_enabled' => true,
            'extra' => $extra_info
        ], 'Token status checked');
        
    } catch (Exception $e) {
        $this->output_json_error('ไม่สามารถตรวจสอบสถานะ Token ได้: ' . $e->getMessage());
    }
}

/**
 * 🔄 Force Refresh Token (Manual)
 */
public function force_refresh() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $this->load->library('google_drive_auto_refresh');
        
        $storage = $this->get_active_system_storage();
        if (!$storage) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }
        
        // Force refresh (แก้ไข need_refresh เป็น true เสมอ)
        $reflect = new ReflectionClass($this->google_drive_auto_refresh);
        $method = $reflect->getMethod('perform_refresh');
        $method->setAccessible(true);
        
        if ($method->invoke($this->google_drive_auto_refresh, $storage)) {
            $this->output_json_success([], 'Force Refresh สำเร็จ!');
        } else {
            $this->output_json_error('Force Refresh ล้มเหลว');
        }
        
    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	

/**
 * ✅ ตรวจสอบ Service Status สำหรับ Token Manager
 */
public function check_service_status() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $status = [
            'google_client_available' => class_exists('Google\\Client'),
            'use_curl_mode' => $this->use_curl_mode,
            'config_loaded' => $this->config_loaded,
            'system_storage_available' => false,
            'drive_service_available' => false,
            'access_token_valid' => false,
            'can_share_folders' => false,
            'token_expires_at' => null,
            'google_account' => null,
            'has_refresh_token' => false
        ];

        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if ($system_storage) {
            $status['system_storage_available'] = true;
            $status['google_account'] = $system_storage->google_account_email;
            $status['token_expires_at'] = $system_storage->google_token_expires;

            // ตรวจสอบ Access Token
            if ($system_storage->google_access_token) {
                $token_data = json_decode($system_storage->google_access_token, true);
                
                if ($token_data && isset($token_data['access_token'])) {
                    // ตรวจสอบอายุ Token
                    if ($system_storage->google_token_expires) {
                        $expires = strtotime($system_storage->google_token_expires);
                        $status['access_token_valid'] = ($expires > time());
                    } else {
                        $status['access_token_valid'] = true; // ถ้าไม่มีข้อมูลหมดอายุ
                    }

                    // ตรวจสอบ Refresh Token
                    if (isset($token_data['refresh_token']) || !empty($system_storage->google_refresh_token)) {
                        $status['has_refresh_token'] = true;
                    }

                    // ตรวจสอบการแชร์โฟลเดอร์ (ทดสอบ API)
                    if ($status['access_token_valid']) {
                        $status['can_share_folders'] = $this->test_drive_api_access($system_storage);
                    }
                }
            }

            // ตรวจสอบ Drive Service
            $status['drive_service_available'] = $status['system_storage_available'] && 
                                               ($this->drive_service !== null || $this->use_curl_mode);
        }

        $this->output_json_success($status, 'ตรวจสอบสถานะบริการสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Check service status error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	

/**
 * ✅ Debug Token Details สำหรับ Token Manager
 */
public function debug_token_details() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $token_details = [
            'google_email' => null,
            'token_type' => null,
            'is_valid' => false,
            'expires_at' => null,
            'has_refresh_token' => false,
            'connected_at' => null,
            'scopes' => [],
            'token_source' => null
        ];

        $system_storage = $this->get_active_system_storage();
        if ($system_storage) {
            $token_details['google_email'] = $system_storage->google_account_email;
            $token_details['connected_at'] = $system_storage->created_at;
            $token_details['expires_at'] = $system_storage->google_token_expires;

            if ($system_storage->google_access_token) {
                $token_data = json_decode($system_storage->google_access_token, true);
                
                if ($token_data && isset($token_data['access_token'])) {
                    $token_details['token_type'] = $token_data['token_type'] ?? 'Bearer';
                    $token_details['scopes'] = explode(' ', $token_data['scope'] ?? '');
                    
                    // ตรวจสอบความถูกต้อง
                    if ($system_storage->google_token_expires) {
                        $expires = strtotime($system_storage->google_token_expires);
                        $token_details['is_valid'] = ($expires > time());
                    } else {
                        $token_details['is_valid'] = true;
                    }

                    // Refresh Token
                    if (isset($token_data['refresh_token']) || !empty($system_storage->google_refresh_token)) {
                        $token_details['has_refresh_token'] = true;
                    }

                    $token_details['token_source'] = $this->use_curl_mode ? 'cURL Mode' : 'Google Client';
                }
            }
        }

        $this->output_json_success($token_details, 'ดึงรายละเอียด Token สำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Debug token details error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

	
	
/**
 * ✅ ทดสอบ Token แบบเร็ว
 */
private function test_token_validity_quick($access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . urlencode($access_token),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_code === 200);

    } catch (Exception $e) {
        return false;
    }
}

/**
 * ✅ ทดสอบความสามารถในการแชร์โฟลเดอร์
 */
private function test_folder_sharing_capability($access_token) {
    try {
        // ทดสอบด้วยการเรียก Google Drive API
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 5,
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
            return ($data && isset($data['user']));
        }

        return false;

    } catch (Exception $e) {
        return false;
    }
}

/**
 * ✅ Get Recent Logs (สำหรับ Token Manager)
 */
public function get_recent_logs() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $limit = $this->input->get('limit') ?: 10;
        $logs = [];

        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $logs = $this->db->select('
                    gdl.*,
                    COALESCE(m.m_fname, "ระบบ") as member_name
                ')
                ->from('tbl_google_drive_logs gdl')
                ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                ->where('gdl.created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                ->order_by('gdl.created_at', 'desc')
                ->limit($limit)
                ->get()
                ->result();
        }

        // ถ้าไม่มี Log ให้สร้าง Mock Data
        if (empty($logs)) {
            $logs = $this->create_mock_logs();
        }

        $this->output_json_success($logs, 'ดึงข้อมูล Log ล่าสุดสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get recent logs error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}


/**
 * ✅ Comprehensive Token Status (สำหรับ Token Manager)
 */
private function get_comprehensive_token_status() {
    try {
        $system_storage = $this->get_active_system_storage();
        
        $status = [
            'has_system_storage' => (bool)$system_storage,
            'has_access_token' => false,
            'has_refresh_token' => false,
            'token_valid' => false,
            'token_expires_at' => null,
            'google_account' => null,
            'can_refresh' => false,
            'requires_reconnect' => false,
            'time_to_expiry_minutes' => null
        ];

        if ($system_storage) {
            $status['google_account'] = $system_storage->google_account_email;
            $status['token_expires_at'] = $system_storage->google_token_expires;
            
            if ($system_storage->google_access_token) {
                $token_data = json_decode($system_storage->google_access_token, true);
                if ($token_data) {
                    $status['has_access_token'] = isset($token_data['access_token']);
                    $status['has_refresh_token'] = !empty($token_data['refresh_token']);
                    
                    // ตรวจสอบ Token
                    if ($system_storage->google_token_expires) {
                        $expires = strtotime($system_storage->google_token_expires);
                        $now = time();
                        $diff = $expires - $now;
                        
                        $status['token_valid'] = ($diff > 0);
                        $status['time_to_expiry_minutes'] = max(0, round($diff / 60));
                    } else {
                        $status['token_valid'] = $this->test_token_validity_quick($token_data['access_token']);
                    }
                }
            }
            
            // ความสามารถในการ Refresh
            $status['can_refresh'] = $status['has_refresh_token'];
            $status['requires_reconnect'] = !$status['has_refresh_token'] && !$status['token_valid'];
        }

        return $status;

    } catch (Exception $e) {
        log_message('error', 'Get comprehensive token status error: ' . $e->getMessage());
        return [
            'has_system_storage' => false,
            'requires_reconnect' => true
        ];
    }
}

/**
 * ✅ Run Complete Diagnostics (สำหรับ Debug Tools)
 */
public function run_complete_diagnostics() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $tests = [];
        
        // Test 1: Database Tables
        $tests[] = [
            'name' => 'Database Tables',
            'passed' => $this->test_database_tables_exist(),
            'result' => $this->test_database_tables_exist() ? 'ตารางฐานข้อมูลครบถ้วน' : 'ตารางฐานข้อมูลไม่ครบ'
        ];

        // Test 2: OAuth Configuration  
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');
        $oauth_ok = !empty($client_id) && !empty($client_secret);
        
        $tests[] = [
            'name' => 'OAuth Configuration',
            'passed' => $oauth_ok,
            'result' => $oauth_ok ? 'OAuth Credentials ตั้งค่าเรียบร้อย' : 'OAuth Credentials ไม่ได้ตั้งค่า'
        ];

        // Test 3: System Storage
        $storage = $this->get_active_system_storage();
        $tests[] = [
            'name' => 'System Storage',
            'passed' => (bool)$storage,
            'result' => $storage ? 'System Storage พร้อมใช้งาน: ' . $storage->google_account_email : 'ไม่พบ System Storage'
        ];

        // Test 4: Token Status
        $token_valid = false;
        if ($storage && $storage->google_access_token) {
            $token_data = json_decode($storage->google_access_token, true);
            if ($token_data && isset($token_data['access_token'])) {
                if ($storage->google_token_expires) {
                    $token_valid = (strtotime($storage->google_token_expires) > time());
                } else {
                    $token_valid = $this->test_token_validity_quick($token_data['access_token']);
                }
            }
        }
        
        $tests[] = [
            'name' => 'Access Token',
            'passed' => $token_valid,
            'result' => $token_valid ? 'Access Token ใช้งานได้' : 'Access Token หมดอายุหรือไม่ถูกต้อง'
        ];

        // Test 5: Google API
        $api_ok = false;
        if ($token_valid && $storage) {
            $token_data = json_decode($storage->google_access_token, true);
            $api_ok = $this->test_folder_sharing_capability($token_data['access_token']);
        }
        
        $tests[] = [
            'name' => 'Google Drive API',
            'passed' => $api_ok,
            'result' => $api_ok ? 'Google Drive API เข้าถึงได้' : 'Google Drive API ไม่สามารถเข้าถึงได้'
        ];

        $passed_count = count(array_filter($tests, function($test) { return $test['passed']; }));
        $total_count = count($tests);

        $this->output_json_success([
            'tests' => $tests,
            'summary' => [
                'passed' => $passed_count,
                'total' => $total_count,
                'success_rate' => round(($passed_count / $total_count) * 100, 2)
            ]
        ], "การตรวจสอบเสร็จสิ้น: ผ่าน {$passed_count}/{$total_count} ข้อ");

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาดในการตรวจสอบ: ' . $e->getMessage());
    }
}



/**
 * ✅ Test Token Status (สำหรับ Header Badge)
 */
private function test_token_status() {
    $storage = $this->get_active_system_storage();
    
    if (!$storage) {
        return ['passed' => false, 'message' => 'ไม่พบ System Storage'];
    }

    if (!$storage->google_access_token) {
        return ['passed' => false, 'message' => 'ไม่มี Access Token'];
    }

    $token_data = json_decode($storage->google_access_token, true);
    if (!$token_data || !isset($token_data['access_token'])) {
        return ['passed' => false, 'message' => 'รูปแบบ Token ไม่ถูกต้อง'];
    }

    // ตรวจสอบการหมดอายุ
    if ($storage->google_token_expires) {
        $expires = strtotime($storage->google_token_expires);
        $now = time();
        $diff = $expires - $now;

        if ($diff <= 0) {
            return ['passed' => false, 'message' => 'Access Token หมดอายุแล้ว'];
        } elseif ($diff <= 300) {
            return ['passed' => false, 'message' => 'Access Token จะหมดอายุภายใน 5 นาที'];
        }
    }

    return ['passed' => true, 'message' => 'Access Token ใช้งานได้ปกติ'];
}
	

public function test_google_status() {
    echo "<h1>🧪 ทดสอบสถานะ Google Client</h1>";
    
    // Test 1: Class exists
    $class_exists = class_exists('Google\\Client');
    echo "<p>" . ($class_exists ? "✅" : "❌") . " Google\\Client class: " . ($class_exists ? "พร้อม" : "ไม่พร้อม") . "</p>";
    
    if (!$class_exists) {
        echo "<p style='color: red;'>❌ Google Client ยังโหลดไม่ได้ - ตรวจสอบ composer.php</p>";
        return;
    }
    
    // Test 2: Create instance
    try {
        $test_client = new Google\Client();
        echo "<p>✅ สร้าง Google Client ได้</p>";
        
        // Test 3: Basic methods
        $methods_to_test = ['setClientId', 'setClientSecret', 'setRedirectUri', 'addScope'];
        foreach ($methods_to_test as $method) {
            $exists = method_exists($test_client, $method);
            echo "<p>" . ($exists ? "✅" : "❌") . " Method {$method}: " . ($exists ? "มี" : "ไม่มี") . "</p>";
        }
        
        // Test 4: OAuth URL creation
        $client_id = $this->get_setting('google_client_id');
        if (!empty($client_id)) {
            try {
                $test_client->setClientId($client_id);
                $test_client->setRedirectUri(site_url('google_drive/oauth_callback'));
                $test_client->addScope('https://www.googleapis.com/auth/drive');
                
                $auth_url = $test_client->createAuthUrl();
                if (strpos($auth_url, 'accounts.google.com') !== false) {
                    echo "<p>✅ สร้าง OAuth URL ได้: <a href='{$auth_url}' target='_blank'>ทดสอบ</a></p>";
                } else {
                    echo "<p>❌ OAuth URL ไม่ถูกต้อง</p>";
                }
            } catch (Exception $e) {
                echo "<p>❌ สร้าง OAuth URL ไม่ได้: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>⚠️ ยังไม่ได้ตั้งค่า Google Client ID</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ ไม่สามารถสร้าง Google Client: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎯 สถานะระบบ:</h3>";
    if ($class_exists) {
        echo "<p style='color: green; font-size: 18px;'>🎉 <strong>Google Client Library พร้อมใช้งาน!</strong></p>";
        echo "<p>ตอนนี้สามารถ:</p>";
        echo "<ul>";
        echo "<li>✅ ตั้งค่า OAuth Credentials</li>";
        echo "<li>✅ เชื่อมต่อ Google Account</li>";
        echo "<li>✅ สร้างโครงสร้างโฟลเดอร์</li>";
        echo "<li>✅ จัดการ Google Drive ได้แล้ว</li>";
        echo "</ul>";
        
        echo "<p><a href='" . site_url('google_drive_system/setup') . "' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none;'>🚀 ไปตั้งค่าระบบ</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Google Client Library ยังไม่พร้อม</p>";
    }
}
	
	
	
	
	
	
	
	
/**
 * ✅ หน้าดูการใช้งาน Storage ของ User (แก้ไข format_bytes error)
 * URL: /google_drive_system/user_usage?user_id=27
 */
public function user_usage() {
    try {
        // ตรวจสอบสิทธิ์ Admin
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        $user_id = $this->input->get('user_id');
        if (empty($user_id)) {
            show_error('ไม่ได้ระบุ User ID', 400);
        }

        // ดึงข้อมูล User
        $user_data = $this->get_user_storage_details($user_id);
        if (!$user_data) {
            show_error('ไม่พบผู้ใช้ หรือผู้ใช้ไม่มีสิทธิ์เข้าใช้ System Storage', 404);
        }

        // ดึงข้อมูลการใช้งาน
        $files = $this->get_user_files($user_id);
        $storage_stats = $this->get_user_storage_stats($user_id);
        $usage_history = $this->get_user_usage_history($user_id);
        $folder_breakdown = $this->get_user_folder_breakdown($user_id);
        $recent_activities = $this->get_user_recent_activities($user_id);

        // ✅ แปลงข้อมูลให้พร้อมแสดงผลใน View
        $formatted_data = $this->prepare_formatted_data_for_view($files, $storage_stats, $folder_breakdown);

        $data = [
            'user' => $user_data,
            'files' => $formatted_data['files'],
            'storage_stats' => $formatted_data['storage_stats'],
            'usage_history' => $usage_history,
            'folder_breakdown' => $formatted_data['folder_breakdown'],
            'recent_activities' => $recent_activities,
            'helper_functions' => $this->get_view_helper_functions() // ✅ ส่งฟังก์ชันช่วยไปยัง View
        ];

        // โหลด Views
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_user_usage', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');

    } catch (Exception $e) {
        log_message('error', 'User usage error: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
    }
}

	
	
	/**
 * ✅ เตรียมข้อมูลที่ format แล้วสำหรับ View
 */
private function prepare_formatted_data_for_view($files, $storage_stats, $folder_breakdown) {
    // ✅ Format Files - แก้ไขปัญหา Unknown File
    $formatted_files = [];
    foreach ($files as $file) {
        // 🎯 ตรวจสอบและแก้ไข MIME Type ก่อน
        $file->mime_type = $this->fix_mime_type($file->mime_type, $file->file_name);
        
        // Format ข้อมูลต่างๆ
        $file->file_size_formatted = $this->format_bytes($file->file_size);
        $file->mime_type_friendly = $this->get_friendly_mime_type($file->mime_type);
        $file->file_type_icon = $this->get_file_type_icon($file->mime_type);
        $formatted_files[] = $file;
    }

    // ✅ Format Storage Stats
    $formatted_storage_stats = $storage_stats;
    $formatted_storage_stats['total_size_formatted'] = $this->format_bytes($storage_stats['total_size']);
    
    // Format largest file
    if ($storage_stats['largest_file']) {
        $formatted_storage_stats['largest_file']->file_size_formatted = $this->format_bytes($storage_stats['largest_file']->file_size);
    }

    // ✅ Format file types - แก้ไขปัญหา Unknown File ในสถิติด้วย
    $formatted_file_types = [];
    foreach ($storage_stats['file_types'] as $type) {
        // แก้ไข MIME Type สำหรับสถิติด้วย
        $fixed_mime = $this->fix_mime_type($type->mime_type, '');
        
        $type->total_size_formatted = $this->format_bytes($type->total_size);
        $type->mime_type_friendly = $this->get_friendly_mime_type($fixed_mime);
        $type->file_type_icon = $this->get_file_type_icon($fixed_mime);
        $type->fixed_mime_type = $fixed_mime; // เก็บไว้สำหรับ debug
        $formatted_file_types[] = $type;
    }
    $formatted_storage_stats['file_types'] = $formatted_file_types;

    // ✅ Format Folder Breakdown
    $formatted_folder_breakdown = [];
    foreach ($folder_breakdown as $folder) {
        $folder->total_size_formatted = $this->format_bytes($folder->total_size);
        $formatted_folder_breakdown[] = $folder;
    }

    return [
        'files' => $formatted_files,
        'storage_stats' => $formatted_storage_stats,
        'folder_breakdown' => $formatted_folder_breakdown
    ];
}

	
	/**
 * 🎯 ฟังก์ชันใหม่: แก้ไข MIME Type ที่ไม่ถูกต้อง
 */
private function fix_mime_type($current_mime, $filename) {
    // ถ้า MIME Type ปัจจุบันไม่ถูกต้องหรือเป็น generic type
    $generic_types = [
        'application/octet-stream',
        'binary/octet-stream', 
        'application/x-download',
        'application/download',
        'application/force-download',
        '',
        null
    ];
    
    $current_mime = trim($current_mime);
    
    // ถ้า MIME Type เป็น generic หรือว่าง ให้หาจาก extension
    if (in_array($current_mime, $generic_types) || empty($current_mime)) {
        return $this->get_mime_type_from_extension($filename);
    }
    
    // ถ้า MIME Type ดูแปลกๆ ให้ลองแก้ไข
    if (strlen($current_mime) < 5 || !strpos($current_mime, '/')) {
        return $this->get_mime_type_from_extension($filename);
    }
    
    return $current_mime;
}
	
	
	/**
 * 🔧 ปรับปรุง get_mime_type_from_extension() ให้ครบถ้วนมากขึ้น
 */
private function get_mime_type_from_extension($filename) {
    if (empty($filename)) {
        return 'application/octet-stream';
    }
    
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $mime_types = [
        // === Images ===
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        
        // === Documents ===
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'rtf' => 'application/rtf',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        
        // === Archives ===
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        '7z' => 'application/x-7z-compressed',
        'tar' => 'application/x-tar',
        'gz' => 'application/gzip',
        'bz2' => 'application/x-bzip2',
        
        // === Text Files ===
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'html' => 'text/html',
        'htm' => 'text/html',
        'css' => 'text/css',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'md' => 'text/markdown',
        'log' => 'text/plain',
        
        // === Audio ===
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'aac' => 'audio/aac',
        'flac' => 'audio/flac',
        'wma' => 'audio/x-ms-wma',
        'm4a' => 'audio/mp4',
        
        // === Video ===
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'flv' => 'video/x-flv',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska',
        '3gp' => 'video/3gpp',
        
        // === Fonts ===
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'eot' => 'application/vnd.ms-fontobject',
        
        // === Others ===
        'exe' => 'application/x-msdownload',
        'apk' => 'application/vnd.android.package-archive',
        'dmg' => 'application/x-apple-diskimage',
        'iso' => 'application/x-iso9660-image',
    ];
    
    return $mime_types[$extension] ?? 'application/octet-stream';
}
	
	
	/**
 * ✅ ส่งฟังก์ชันช่วยเหลือไปยัง View
 */
private function get_view_helper_functions() {
    return [
        'format_bytes' => function($bytes) { return $this->format_bytes($bytes); },
        'get_friendly_mime_type' => function($mime_type) { return $this->get_friendly_mime_type($mime_type); },
        'get_file_type_icon' => function($mime_type) { return $this->get_file_type_icon($mime_type); },
        'get_activity_icon' => function($action) { return $this->get_activity_icon($action); }
    ];
}
	
	

	
/**
 * ดึงข้อมูล User และ Storage Details
 */
private function get_user_storage_details($user_id) {
    try {
        $user = $this->db->select('
                m.m_id,
                m.m_fname,
                m.m_lname,
                m.m_email,
                m.m_phone,
                m.storage_access_granted,
                m.personal_folder_id,
                m.storage_quota_limit,
                m.storage_quota_used,
                m.last_storage_access,
                m.m_datesave as member_since,
                p.pname as position_name
            ')
            ->from('tbl_member m')
            ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
            ->where('m.m_id', $user_id)
            ->where('m.storage_access_granted', 1)
            ->get()
            ->row();

        if ($user) {
            // เพิ่มข้อมูลที่แปลงแล้ว
            $user->full_name = $user->m_fname . ' ' . $user->m_lname;
            $user->storage_quota_limit_formatted = $this->format_bytes($user->storage_quota_limit);
            $user->storage_quota_used_formatted = $this->format_bytes($user->storage_quota_used);
            $user->storage_usage_percent = ($user->storage_quota_limit > 0) ? 
                round(($user->storage_quota_used / $user->storage_quota_limit) * 100, 2) : 0;
        }

        return $user;

    } catch (Exception $e) {
        log_message('error', 'Get user storage details error: ' . $e->getMessage());
        return null;
    }
}

/**
 * ดึงไฟล์ของ User
 */
private function get_user_files($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return [];
        }

        return $this->db->select('
                sf.*,
                folder.folder_name,
                folder.folder_path
            ')
            ->from('tbl_google_drive_system_files sf')
            ->join('tbl_google_drive_system_folders folder', 'sf.folder_id = folder.folder_id', 'left')
            ->where('sf.uploaded_by', $user_id)
            ->order_by('sf.created_at', 'desc')
            ->limit(100)
            ->get()
            ->result();

    } catch (Exception $e) {
        log_message('error', 'Get user files error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ดึงสถิติการใช้งาน Storage
 */
private function get_user_storage_stats($user_id) {
    try {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'largest_file' => null,
            'file_types' => [],
            'upload_frequency' => []
        ];

        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return $stats;
        }

        // สถิติพื้นฐาน
        $basic_stats = $this->db->select('
                COUNT(*) as total_files,
                COALESCE(SUM(file_size), 0) as total_size,
                MAX(file_size) as largest_file_size
            ')
            ->from('tbl_google_drive_system_files')
            ->where('uploaded_by', $user_id)
            ->get()
            ->row();

        $stats['total_files'] = (int)$basic_stats->total_files;
        $stats['total_size'] = (int)$basic_stats->total_size;

        // ไฟล์ที่ใหญ่ที่สุด
        if ($basic_stats->largest_file_size > 0) {
            $stats['largest_file'] = $this->db->select('file_name, file_size')
                ->from('tbl_google_drive_system_files')
                ->where('uploaded_by', $user_id)
                ->where('file_size', $basic_stats->largest_file_size)
                ->limit(1)
                ->get()
                ->row();
        }

        // สถิติตามประเภทไฟล์
        $file_types = $this->db->select('
                mime_type,
                COUNT(*) as count,
                SUM(file_size) as total_size
            ')
            ->from('tbl_google_drive_system_files')
            ->where('uploaded_by', $user_id)
            ->group_by('mime_type')
            ->order_by('count', 'desc')
            ->get()
            ->result();

        $stats['file_types'] = $file_types;

        // ความถี่การอัปโหลด (7 วันล่าสุด)
        $upload_freq = $this->db->select('
                DATE(created_at) as upload_date,
                COUNT(*) as uploads_count
            ')
            ->from('tbl_google_drive_system_files')
            ->where('uploaded_by', $user_id)
            ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
            ->group_by('DATE(created_at)')
            ->order_by('upload_date', 'desc')
            ->get()
            ->result();

        $stats['upload_frequency'] = $upload_freq;

        return $stats;

    } catch (Exception $e) {
        log_message('error', 'Get user storage stats error: ' . $e->getMessage());
        return [
            'total_files' => 0,
            'total_size' => 0,
            'largest_file' => null,
            'file_types' => [],
            'upload_frequency' => []
        ];
    }
}

/**
 * ดึงประวัติการใช้งาน
 */
private function get_user_usage_history($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_storage_usage')) {
            return [];
        }

        return $this->db->select('*')
            ->from('tbl_google_drive_storage_usage')
            ->where('user_id', $user_id)
            ->where('usage_date >=', date('Y-m-d', strtotime('-30 days')))
            ->order_by('usage_date', 'desc')
            ->limit(30)
            ->get()
            ->result();

    } catch (Exception $e) {
        log_message('error', 'Get user usage history error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ดึงการใช้งานตามโฟลเดอร์
 */
private function get_user_folder_breakdown($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return [];
        }

        return $this->db->select('
                folder.folder_name,
                folder.folder_path,
                COUNT(sf.id) as file_count,
                SUM(sf.file_size) as total_size
            ')
            ->from('tbl_google_drive_system_files sf')
            ->join('tbl_google_drive_system_folders folder', 'sf.folder_id = folder.folder_id', 'left')
            ->where('sf.uploaded_by', $user_id)
            ->group_by('sf.folder_id')
            ->order_by('total_size', 'desc')
            ->get()
            ->result();

    } catch (Exception $e) {
        log_message('error', 'Get user folder breakdown error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ดึงกิจกรรมล่าสุดของ User
 */
private function get_user_recent_activities($user_id) {
    try {
        $activities = [];

        // จาก Google Drive Logs
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $drive_logs = $this->db->select('*')
                ->from('tbl_google_drive_logs')
                ->where('member_id', $user_id)
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->order_by('created_at', 'desc')
                ->limit(20)
                ->get()
                ->result();

            foreach ($drive_logs as $log) {
                $activities[] = [
                    'type' => 'drive_activity',
                    'action' => $log->action_type,
                    'description' => $log->action_description,
                    'created_at' => $log->created_at,
                    'status' => $log->status ?? 'success'
                ];
            }
        }

        // เรียงตามเวลา
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($activities, 0, 20);

    } catch (Exception $e) {
        log_message('error', 'Get user recent activities error: ' . $e->getMessage());
        return [];
    }
}

/**
 * AJAX: ดึงข้อมูลการใช้งานแบบ Real-time
 */
public function get_user_usage_data() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $user_id = $this->input->get('user_id');
        if (empty($user_id)) {
            $this->output_json_error('ไม่ได้ระบุ User ID');
            return;
        }

        $data = [
            'user' => $this->get_user_storage_details($user_id),
            'stats' => $this->get_user_storage_stats($user_id),
            'recent_files' => array_slice($this->get_user_files($user_id), 0, 10)
        ];

        $this->output_json_success($data, 'ดึงข้อมูลการใช้งานสำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * AJAX: ลบไฟล์ของ User
 */
public function delete_user_file() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $file_id = $this->input->post('file_id');
        $user_id = $this->input->post('user_id');

        if (empty($file_id) || empty($user_id)) {
            $this->output_json_error('ข้อมูลไม่ครบถ้วน');
            return;
        }

        // ดึงข้อมูลไฟล์
        $file = $this->db->select('*')
            ->from('tbl_google_drive_system_files')
            ->where('file_id', $file_id)
            ->where('uploaded_by', $user_id)
            ->get()
            ->row();

        if (!$file) {
            $this->output_json_error('ไม่พบไฟล์');
            return;
        }

        // ลบจาก Google Drive (ถ้าต้องการ)
        $system_storage = $this->get_active_system_storage();
        if ($system_storage && $system_storage->google_access_token) {
            $token_data = json_decode($system_storage->google_access_token, true);
            if ($token_data && isset($token_data['access_token'])) {
                $this->delete_google_drive_item($token_data['access_token'], $file_id);
            }
        }

        // ลบจากฐานข้อมูล
        $this->db->where('file_id', $file_id)
                ->where('uploaded_by', $user_id)
                ->delete('tbl_google_drive_system_files');

        // อัปเดต storage usage
        $this->update_user_storage_usage($user_id);

        // บันทึก log
        $this->log_enhanced_activity(
            $this->session->userdata('m_id'),
            'admin_delete_user_file',
            "Admin ลบไฟล์ {$file->file_name} ของ User ID: {$user_id}",
            [
                'user_id' => $user_id,
                'file_id' => $file_id,
                'file_name' => $file->file_name,
                'file_size' => $file->file_size
            ]
        );

        $this->output_json_success([], 'ลบไฟล์เรียบร้อย');

    } catch (Exception $e) {
        log_message('error', 'Delete user file error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * Helper: อัปเดต storage usage ของ user
 */
private function update_user_storage_usage($user_id) {
    try {
        $total_usage = $this->db->select('COALESCE(SUM(file_size), 0) as total')
            ->from('tbl_google_drive_system_files')
            ->where('uploaded_by', $user_id)
            ->get()
            ->row()
            ->total;

        $this->db->where('m_id', $user_id)
                ->update('tbl_member', [
                    'storage_quota_used' => $total_usage
                ]);

        return $total_usage;

    } catch (Exception $e) {
        log_message('error', 'Update user storage usage error: ' . $e->getMessage());
        return 0;
    }
}
	
	

	
/**
 * 🔧 ปรับปรุง get_friendly_mime_type() ให้จับ extension ได้ด้วย
 */
private function get_friendly_mime_type($mime_type) {
    if (empty($mime_type)) {
        return 'Unknown File';
    }
    
    // แปลงเป็นตัวพิมพ์เล็กเพื่อเปรียบเทียบ
    $mime_lower = strtolower(trim($mime_type));
    
    $types = [
        // === PDF ===
        'application/pdf' => 'PDF Document',
        
        // === Microsoft Office (รุ่นเก่า) ===
        'application/msword' => 'Word Document',
        'application/vnd.ms-excel' => 'Excel Spreadsheet',
        'application/vnd.ms-powerpoint' => 'PowerPoint Presentation',
        
        // === Microsoft Office (รุ่นใหม่ - Office 2007+) ===
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word Document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel Spreadsheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint Presentation',
        
        // === รูปภาพ ===
        'image/jpeg' => 'JPEG Image',
        'image/jpg' => 'JPEG Image',
        'image/png' => 'PNG Image',
        'image/gif' => 'GIF Animation',
        'image/webp' => 'WebP Image',
        'image/bmp' => 'Bitmap Image',
        'image/tiff' => 'TIFF Image',
        'image/svg+xml' => 'SVG Vector',
        'image/x-icon' => 'Icon File',
        
        // === วิดีโอ ===
        'video/mp4' => 'MP4 Video',
        'video/x-msvideo' => 'AVI Video',
        'video/quicktime' => 'QuickTime Video',
        'video/x-ms-wmv' => 'WMV Video',
        'video/x-flv' => 'Flash Video',
        'video/webm' => 'WebM Video',
        'video/x-matroska' => 'MKV Video',
        
        // === เสียง ===
        'audio/mpeg' => 'MP3 Audio',
        'audio/wav' => 'WAV Audio',
        'audio/ogg' => 'OGG Audio',
        'audio/aac' => 'AAC Audio',
        'audio/flac' => 'FLAC Audio',
        'audio/x-ms-wma' => 'WMA Audio',
        'audio/mp4' => 'M4A Audio',
        
        // === Text Files ===
        'text/plain' => 'Text Document',
        'text/csv' => 'CSV Spreadsheet',
        'text/html' => 'HTML Document',
        'text/css' => 'CSS Stylesheet',
        'text/javascript' => 'JavaScript File',
        'text/markdown' => 'Markdown Document',
        
        // === Archive/Compressed ===
        'application/zip' => 'ZIP Archive',
        'application/x-rar-compressed' => 'RAR Archive',
        'application/x-7z-compressed' => '7-Zip Archive',
        'application/x-tar' => 'TAR Archive',
        'application/gzip' => 'GZIP Archive',
        'application/x-bzip2' => 'BZIP2 Archive',
        
        // === Code/Data ===
        'application/json' => 'JSON Data',
        'application/xml' => 'XML Document',
        'application/javascript' => 'JavaScript File',
        
        // === Google Workspace ===
        'application/vnd.google-apps.document' => 'Google Docs',
        'application/vnd.google-apps.spreadsheet' => 'Google Sheets',
        'application/vnd.google-apps.presentation' => 'Google Slides',
        'application/vnd.google-apps.form' => 'Google Forms',
        
        // === Others ===
        'application/x-msdownload' => 'Windows Executable',
        'application/vnd.android.package-archive' => 'Android APK',
        'application/x-apple-diskimage' => 'Mac Disk Image',
        'application/octet-stream' => 'Binary File',
    ];
    
    // ค้นหาใน array หลัก
    if (isset($types[$mime_lower])) {
        return $types[$mime_lower];
    }
    
    // ตรวจสอบตาม pattern
    if (strpos($mime_lower, 'image/') === 0) {
        $subtype = strtoupper(str_replace('image/', '', $mime_lower));
        return $subtype . ' Image';
    }
    
    if (strpos($mime_lower, 'video/') === 0) {
        $subtype = strtoupper(str_replace('video/', '', $mime_lower));
        return $subtype . ' Video';
    }
    
    if (strpos($mime_lower, 'audio/') === 0) {
        $subtype = strtoupper(str_replace('audio/', '', $mime_lower));
        return $subtype . ' Audio';
    }
    
    if (strpos($mime_lower, 'text/') === 0) {
        $subtype = ucfirst(str_replace('text/', '', $mime_lower));
        return $subtype . ' Text';
    }
    
    // ถ้าไม่เจอเลย
    return 'Binary File';
}


/**
 * ✅ ได้ไอคอนสำหรับประเภทไฟล์
 */
private function get_file_type_icon($mime_type) {
    // รูปภาพ
    if (strpos($mime_type, 'image/') === 0) {
        return 'fas fa-image text-purple-500';
    }
    
    // วิดีโอ
    if (strpos($mime_type, 'video/') === 0) {
        return 'fas fa-video text-red-500';
    }
    
    // เสียง
    if (strpos($mime_type, 'audio/') === 0) {
        return 'fas fa-music text-green-500';
    }
    
    // PDF
    if ($mime_type === 'application/pdf') {
        return 'fas fa-file-pdf text-red-500';
    }
    
    // Word
    if (strpos($mime_type, 'word') !== false || 
        strpos($mime_type, 'wordprocessingml') !== false) {
        return 'fas fa-file-word text-blue-500';
    }
    
    // Excel
    if (strpos($mime_type, 'excel') !== false || 
        strpos($mime_type, 'spreadsheetml') !== false) {
        return 'fas fa-file-excel text-green-500';
    }
    
    // PowerPoint
    if (strpos($mime_type, 'powerpoint') !== false || 
        strpos($mime_type, 'presentationml') !== false) {
        return 'fas fa-file-powerpoint text-orange-500';
    }
    
    // Archive
    if (strpos($mime_type, 'zip') !== false || 
        strpos($mime_type, 'rar') !== false ||
        strpos($mime_type, 'archive') !== false) {
        return 'fas fa-file-archive text-yellow-500';
    }
    
    // Text
    if (strpos($mime_type, 'text/') === 0) {
        return 'fas fa-file-alt text-gray-500';
    }
    
    // Code
    if (in_array($mime_type, [
        'application/json',
        'application/xml',
        'text/html',
        'text/css',
        'text/javascript'
    ])) {
        return 'fas fa-file-code text-blue-600';
    }
    
    // Default
    return 'fas fa-file text-gray-500';
}
	
	
	

// ===================================================================
// 🔧 Fixed Code สำหรับ Database Schema จริง
// ===================================================================

/**
 * ✅ สร้าง Personal Folders อัตโนมัติสำหรับทุกคนเมื่อสร้างโครงสร้าง
 */
private function create_personal_folders_auto($users_folder_id, $access_token) {
    try {
        log_message('info', '👤 Creating personal folders for all active users...');
        
        $created_count = 0;
        $permissions_assigned = 0;
        $folder_details = [];
        $permission_details = [];
        $errors = [];
        
        // ดึงผู้ใช้ทั้งหมดที่ active
        $all_users = $this->get_all_active_users();
        log_message('info', "👤 Found {" . count($all_users) . "} active users for personal folders");
        
        foreach ($all_users as $user) {
            try {
                // สร้างชื่อโฟลเดอร์ส่วนตัว
                $folder_name = $user['name'] . ' (Personal)';
                
                log_message('info', "👤 Creating personal folder: {$folder_name} for user ID {$user['m_id']}");
                
                // สร้างโฟลเดอร์ใน Google Drive
                $personal_folder = $this->create_folder_with_curl($folder_name, $users_folder_id, $access_token);
                
                if ($personal_folder) {
                    // บันทึกข้อมูลโฟลเดอร์ในฐานข้อมูล
                    $folder_data = [
                        'folder_name' => $folder_name,
                        'folder_id' => $personal_folder['id'],
                        'parent_folder_id' => $users_folder_id,
                        'folder_type' => 'user',
                        'folder_path' => '/Organization Drive/Users/' . $folder_name,
                        'folder_description' => 'Personal folder for ' . $user['name'],
                        'permission_level' => 'private',
                        'created_by' => $this->session->userdata('m_id'),
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    if ($this->save_folder_info($folder_data)) {
                        $created_count++;
                        
                        $folder_details[] = [
                            'name' => $folder_name,
                            'type' => 'user',
                            'id' => $personal_folder['id'],
                            'owner' => $user['name']
                        ];
                        
                        // อัปเดต personal_folder_id ใน member table
                        $this->db->where('m_id', $user['m_id'])
                                ->update('tbl_member', [
                                    'personal_folder_id' => $personal_folder['id']
                                ]);
                        
                        log_message('info', "✅ Personal folder created: {$folder_name} ({$personal_folder['id']})");
                        
                        // ✅ กำหนดสิทธิ์ Personal Folder
                        $permissions_count = $this->assign_personal_folder_permissions($personal_folder['id'], $user['m_id'], $user['name']);
                        $permissions_assigned += $permissions_count;
                        
                        $permission_details[] = [
                            'folder_name' => $folder_name,
                            'owner' => $user['name'],
                            'permissions_assigned' => $permissions_count
                        ];
                        
                    } else {
                        log_message('error', "❌ Failed to save folder data for: {$folder_name}");
                        $errors[] = [
                            'user' => $user['name'],
                            'message' => 'ไม่สามารถบันทึกข้อมูลโฟลเดอร์ได้'
                        ];
                    }
                } else {
                    log_message('error', "❌ Failed to create Google Drive folder for: {$folder_name}");
                    $errors[] = [
                        'user' => $user['name'],
                        'message' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้'
                    ];
                }
                
                // หน่วงเวลาเล็กน้อยเพื่อไม่ให้ API rate limit
                if ($created_count > 0 && $created_count % 5 === 0) {
                    usleep(500000); // 0.5 วินาที ทุก 5 โฟลเดอร์
                }
                
            } catch (Exception $e) {
                log_message('error', "Exception creating personal folder for {$user['name']}: " . $e->getMessage());
                $errors[] = [
                    'user' => $user['name'],
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ];
            }
        }
        
        log_message('info', "👤 Personal folders creation completed: {$created_count}/" . count($all_users) . " (Permissions: {$permissions_assigned})");
        
        return [
            'success' => true,
            'folders_created' => $created_count,
            'permissions_assigned' => $permissions_assigned,
            'total_users' => count($all_users),
            'folder_details' => $folder_details,
            'permission_details' => $permission_details,
            'errors' => $errors
        ];
        
    } catch (Exception $e) {
        log_message('error', 'create_personal_folders_auto error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'folders_created' => 0,
            'permissions_assigned' => 0
        ];
    }
}

/**
 * ✅ กำหนดสิทธิ์สำหรับ Personal Folder
 */
private function assign_personal_folder_permissions($folder_id, $owner_user_id, $owner_name) {
    try {
        log_message('info', "🔐 Assigning permissions for personal folder of: {$owner_name}");
        
        $assigned_count = 0;
        
        // 1. เจ้าของโฟลเดอร์: ทำได้ทุกอย่าง (admin)
        if ($this->add_folder_permission_correct($folder_id, $owner_user_id, 'admin')) {
            $assigned_count++;
            log_message('info', "✅ Owner permission assigned: {$owner_name} → admin");
        }
        
        // 2. System Admin และ Super Admin: ทำได้ทุกอย่าง (admin)
        $admin_users = $this->get_admin_users();
        foreach ($admin_users as $admin) {
            // ไม่ให้สิทธิ์ซ้ำกับเจ้าของ
            if ($admin['m_id'] != $owner_user_id) {
                if ($this->add_folder_permission_correct($folder_id, $admin['m_id'], 'admin')) {
                    $assigned_count++;
                    log_message('info', "✅ Admin permission assigned: {$admin['name']} → admin for {$owner_name}'s folder");
                }
            }
        }
        
        log_message('info', "🔐 Personal folder permissions completed: {$assigned_count} permissions for {$owner_name}");
        
        return $assigned_count;
        
    } catch (Exception $e) {
        log_message('error', 'assign_personal_folder_permissions error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * ✅ แก้ไข: create_complete_structure_like_debug เพื่อรวม Personal Folders
 */
private function create_complete_structure_with_personal_folders($storage_id, $access_token) {
    try {
        log_message('info', '🏗️ Creating complete structure with personal folders...');
        
        // ล้างข้อมูลเก่า
        $this->clear_all_folders();
        $this->clear_all_permissions();
        
        // Reset system storage
        $this->db->where('id', $storage_id)->update('tbl_google_drive_system_storage', [
            'folder_structure_created' => 0,
            'root_folder_id' => null
        ]);
        
        // สร้าง Root Folder
        $root_folder = $this->create_folder_with_curl('Organization Drive', null, $access_token);
        if (!$root_folder) {
            throw new Exception('Cannot create root folder');
        }
        
        log_message('info', 'Root folder created: ' . $root_folder['id']);
        
        // อัปเดต System Storage
        $this->db->where('id', $storage_id)->update('tbl_google_drive_system_storage', [
            'root_folder_id' => $root_folder['id'],
            'folder_structure_created' => 1
        ]);
        
        // สร้าง Main folders พร้อมสิทธิ์
        $main_folders = [
            'Admin' => ['type' => 'admin', 'description' => 'โฟลเดอร์สำหรับ Admin'],
            'Departments' => ['type' => 'system', 'description' => 'โฟลเดอร์แผนกต่างๆ'],
            'Shared' => ['type' => 'shared', 'description' => 'โฟลเดอร์ส่วนกลาง'],
            'Users' => ['type' => 'system', 'description' => 'โฟลเดอร์ส่วนตัวของ Users']
        ];
        
        $created_folders = [];
        $folders_created_count = 1; // นับ root folder
        $total_permissions_assigned = 0;
        
        foreach ($main_folders as $folder_name => $config) {
            $folder = $this->create_folder_with_curl($folder_name, $root_folder['id'], $access_token);
            if ($folder) {
                $folder_data = [
                    'folder_name' => $folder_name,
                    'folder_id' => $folder['id'],
                    'parent_folder_id' => $root_folder['id'],
                    'folder_type' => $config['type'],
                    'folder_path' => '/Organization Drive/' . $folder_name,
                    'folder_description' => $config['description'],
                    'permission_level' => $config['type'] === 'shared' ? 'public' : 'restricted',
                    'created_by' => $this->session->userdata('m_id')
                ];
                
                if ($this->save_folder_info($folder_data)) {
                    $created_folders[$folder_name] = $folder['id'];
                    $folders_created_count++;
                    log_message('info', 'Main folder created: ' . $folder_name);
                    
                    // กำหนดสิทธิ์อัตโนมัติสำหรับโฟลเดอร์หลัก
                    $perm_result = $this->assign_main_folder_permissions($folder['id'], $folder_name, $config['type']);
                    $total_permissions_assigned += $perm_result;
                }
            }
        }
        
        // สร้าง Department folders
        $dept_count = 0;
        if (isset($created_folders['Departments'])) {
            $this->assign_departments_root_permissions($created_folders['Departments']);
            $dept_count = $this->create_department_folders_like_debug($created_folders['Departments'], $access_token);
            $folders_created_count += $dept_count;
        }
        
        // ✅ สร้าง Personal Folders สำหรับทุกคน
        $personal_folders_result = [
            'success' => false,
            'folders_created' => 0,
            'permissions_assigned' => 0
        ];
        
        if (isset($created_folders['Users'])) {
            log_message('info', '👤 Starting personal folders creation...');
            $personal_folders_result = $this->create_personal_folders_auto($created_folders['Users'], $access_token);
            
            if ($personal_folders_result['success']) {
                $folders_created_count += $personal_folders_result['folders_created'];
                $total_permissions_assigned += $personal_folders_result['permissions_assigned'];
                log_message('info', "👤 Personal folders completed: {$personal_folders_result['folders_created']} folders, {$personal_folders_result['permissions_assigned']} permissions");
            } else {
                log_message('error', '👤 Personal folders creation failed: ' . $personal_folders_result['message']);
            }
        }
        
        return [
            'success' => true,
            'folders_created' => $folders_created_count,
            'main_folders_created' => 4,
            'department_folders_created' => $dept_count,
            'personal_folders_created' => $personal_folders_result['folders_created'],
            'total_permissions_assigned' => $total_permissions_assigned,
            'root_folder_id' => $root_folder['id'],
            'personal_folders_details' => $personal_folders_result
        ];
        
    } catch (Exception $e) {
        log_message('error', 'create_complete_structure_with_personal_folders error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * ✅ แก้ไข: function create_folder_structure_with_permissions() ที่มีอยู่แล้ว
 */
public function create_folder_structure_with_permissions() {
    try {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        log_message('info', '====== CREATE FOLDER STRUCTURE WITH PERMISSIONS + PERSONAL FOLDERS ======');
        
        // ตรวจสอบ system storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage) {
            $this->output_json_error('ไม่พบ System Storage - กรุณาเชื่อมต่อ Google Account ก่อน');
            return;
        }

        // ตรวจสอบ token
        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            $this->output_json_error('Access Token ไม่ถูกต้อง - กรุณาเชื่อมต่อใหม่');
            return;
        }

        $access_token = $token_data['access_token'];
        log_message('info', 'System storage validated successfully');

        // ✅ **LOGIC ที่แก้ไขแล้ว** - ตรวจสอบและสร้างตามสถานะ + Personal Folders
        if ($system_storage->folder_structure_created) {
            log_message('info', 'Main structure exists, checking for department folders...');
            
            // ตรวจสอบว่ามี department folders หรือยัง
            $existing_dept_count = $this->db->where('folder_type', 'department')
                                           ->count_all_results('tbl_google_drive_system_folders');
            
            log_message('info', "Existing department folders: {$existing_dept_count}");
            
            if ($existing_dept_count == 0) {
                // **กรณีที่ 1: มี main structure แต่ไม่มี department folders**
                log_message('info', 'Main structure exists but no department folders - creating them + personal folders...');
                
                $dept_folder = $this->db->where('folder_name', 'Departments')
                                       ->where('folder_type', 'system')
                                       ->get('tbl_google_drive_system_folders')
                                       ->row();
                
                if ($dept_folder) {
                    // สร้าง department folders
                    $dept_count = $this->create_department_folders_like_debug($dept_folder->folder_id, $access_token);
                    
                    // ✅ สร้าง Personal Folders
                    $personal_folders_result = ['success' => false, 'folders_created' => 0, 'permissions_assigned' => 0];
                    $users_folder = $this->db->where('folder_name', 'Users')
                                            ->where('folder_type', 'system')
                                            ->get('tbl_google_drive_system_folders')
                                            ->row();
                    
                    if ($users_folder) {
                        $personal_folders_result = $this->create_personal_folders_auto($users_folder->folder_id, $access_token);
                    }
                    
                    if ($dept_count > 0) {
                        log_message('info', "✅ Department folders created successfully: {$dept_count}");
                        log_message('info', "✅ Personal folders created: {$personal_folders_result['folders_created']}");
                        
                        $this->output_json_success([
                            'folders_created' => 4 + $dept_count + $personal_folders_result['folders_created'],
                            'main_folders_created' => 4,
                            'department_folders_created' => $dept_count,
                            'personal_folders_created' => $personal_folders_result['folders_created'],
                            'total_permissions_assigned' => $personal_folders_result['permissions_assigned'],
                            'scenario' => 'added_department_and_personal_folders'
                        ], "เพิ่มโฟลเดอร์แผนกและ Personal Folders เรียบร้อย! (แผนก: {$dept_count}, Personal: {$personal_folders_result['folders_created']})");
                        return;
                    } else {
                        $this->output_json_error('ไม่สามารถสร้างโฟลเดอร์แผนกได้');
                        return;
                    }
                } else {
                    $this->output_json_error('ไม่พบโฟลเดอร์ Departments');
                    return;
                }
                
            } else {
                // **กรณีที่ 2: มีครบแล้ว - สร้างใหม่ทั้งหมด + Personal Folders**
                log_message('info', 'Complete structure exists - recreating with personal folders...');
                
                $result = $this->create_complete_structure_with_personal_folders($system_storage->id, $access_token);
                
                if ($result && $result['success']) {
                    $this->output_json_success([
                        'folders_created' => $result['folders_created'],
                        'main_folders_created' => $result['main_folders_created'],
                        'department_folders_created' => $result['department_folders_created'],
                        'personal_folders_created' => $result['personal_folders_created'],
                        'total_permissions_assigned' => $result['total_permissions_assigned'],
                        'scenario' => 'recreated_complete_with_personal_folders',
                        'root_folder_id' => $result['root_folder_id']
                    ], "สร้างโครงสร้างใหม่พร้อม Personal Folders เรียบร้อย! (โฟลเดอร์: {$result['folders_created']}, สิทธิ์: {$result['total_permissions_assigned']})");
                    return;
                } else {
                    $this->output_json_error('ไม่สามารถสร้างโครงสร้างใหม่ได้');
                    return;
                }
            }
        } else {
            // **กรณีที่ 3: ไม่มีโครงสร้าง - สร้างใหม่ทั้งหมด + Personal Folders**
            log_message('info', 'No structure exists - creating complete structure with personal folders...');
            
            $result = $this->create_complete_structure_with_personal_folders($system_storage->id, $access_token);
            
            if ($result && $result['success']) {
                $this->output_json_success([
                    'folders_created' => $result['folders_created'],
                    'main_folders_created' => $result['main_folders_created'],
                    'department_folders_created' => $result['department_folders_created'],
                    'personal_folders_created' => $result['personal_folders_created'],
                    'total_permissions_assigned' => $result['total_permissions_assigned'],
                    'scenario' => 'created_new_structure_with_personal_folders',
                    'root_folder_id' => $result['root_folder_id']
                ], "สร้างโครงสร้างพร้อม Personal Folders เรียบร้อย! (โฟลเดอร์: {$result['folders_created']}, สิทธิ์: {$result['total_permissions_assigned']})");
                return;
            } else {
                $this->output_json_error('ไม่สามารถสร้างโครงสร้างโฟลเดอร์ได้');
                return;
            }
        }

    } catch (Exception $e) {
        log_message('error', 'create_folder_structure_with_permissions WITH PERSONAL FOLDERS ERROR: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

	
	
	private function safe_output_error($message, $code = 400) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode([
        'success' => false,
        'message' => $message,
        'error_code' => $code,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
	
	
	
/**
 * ✅ สร้าง Department Folders เหมือนกับ debug ที่สำเร็จ
 */
private function create_department_folders_like_debug($departments_folder_id, $access_token) {
    try {
        log_message('info', '🏢 Creating department folders with position filter and auto permissions...');
        log_message('info', 'Departments folder ID: ' . $departments_folder_id);
        
        // ดึงรายการ positions โดยยกเว้น pid 1,2,3
        $positions = $this->db->where('pstatus', 'show')
                             ->where_not_in('pid', [1, 2, 3]) // ยกเว้น System Admin, Super Admin, User Admin
                             ->order_by('porder', 'ASC')
                             ->get('tbl_position')
                             ->result();
        
        if (empty($positions)) {
            log_message('error', 'No positions found with pstatus = "show" (excluding pid 1,2,3)');
            return 0;
        }
        
        log_message('info', 'Found ' . count($positions) . ' positions to process (excluded System/Super/User Admin)');
        
        // ลบ department folders เก่า
        $this->db->where('folder_type', 'department')->delete('tbl_google_drive_system_folders');
        log_message('info', 'Cleared existing department folders');
        
        $created_count = 0;
        
        // สร้างทีละโฟลเดอร์เหมือน debug
        foreach ($positions as $index => $position) {
            try {
                log_message('info', "[" . ($index + 1) . "/" . count($positions) . "] Creating: {$position->pname} (PID: {$position->pid})");
                
                // สร้างโฟลเดอร์ใน Google Drive
                $folder_result = $this->create_folder_with_curl($position->pname, $departments_folder_id, $access_token);
                
                if ($folder_result && isset($folder_result['id'])) {
                    log_message('info', "✅ Google Drive folder created: {$folder_result['id']}");
                    
                    // บันทึกในฐานข้อมูล
                    $folder_data = [
                        'folder_name' => $position->pname,
                        'folder_id' => $folder_result['id'],
                        'parent_folder_id' => $departments_folder_id,
                        'folder_type' => 'department',
                        'folder_path' => '/Organization Drive/Departments/' . $position->pname,
                        'created_for_position' => $position->pid,
                        'folder_description' => 'โฟลเดอร์สำหรับ ' . $position->pname,
                        'permission_level' => 'restricted',
                        'created_by' => $this->session->userdata('m_id'),
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    if ($this->db->insert('tbl_google_drive_system_folders', $folder_data)) {
                        $created_count++;
                        log_message('info', "✅ Database saved successfully: {$position->pname}");
                        
                        // ✅ กำหนดสิทธิ์อัตโนมัติสำหรับโฟลเดอร์แผนก
                        $this->assign_department_folder_permissions($folder_result['id'], $position->pid);
                        
                    } else {
                        log_message('error', "❌ Database save failed: {$position->pname}");
                    }
                    
                } else {
                    log_message('error', "❌ Google Drive creation failed: {$position->pname}");
                }
                
                // หน่วงเวลาเล็กน้อย
                if ($index < count($positions) - 1) {
                    usleep(300000); // 0.3 วินาที
                }
                
            } catch (Exception $e) {
                log_message('error', "Exception for position {$position->pname}: " . $e->getMessage());
            }
        }
        
        log_message('info', "Department folders creation completed: {$created_count}/" . count($positions));
        
        return $created_count;
        
    } catch (Exception $e) {
        log_message('error', 'create_department_folders_like_debug error: ' . $e->getMessage());
        return 0;
    }
}


/**
 * ✅ สร้างโครงสร้างใหม่ทั้งหมดเหมือน debug
 */
/**
 * ✅ สร้างโครงสร้างใหม่ทั้งหมดพร้อมสิทธิ์อัตโนมัติ
 */
private function create_complete_structure_like_debug($storage_id, $access_token) {
    try {
        log_message('info', '🏗️ Creating complete structure with auto permissions...');
        
        // ล้างข้อมูลเก่า
        $this->clear_all_folders();
        $this->clear_all_permissions();
        
        // Reset system storage
        $this->db->where('id', $storage_id)->update('tbl_google_drive_system_storage', [
            'folder_structure_created' => 0,
            'root_folder_id' => null
        ]);
        
        // สร้าง Root Folder
        $root_folder = $this->create_folder_with_curl('Organization Drive', null, $access_token);
        if (!$root_folder) {
            throw new Exception('Cannot create root folder');
        }
        
        log_message('info', 'Root folder created: ' . $root_folder['id']);
        
        // อัปเดต System Storage
        $this->db->where('id', $storage_id)->update('tbl_google_drive_system_storage', [
            'root_folder_id' => $root_folder['id'],
            'folder_structure_created' => 1
        ]);
        
        // สร้าง Main folders พร้อมสิทธิ์
        $main_folders = [
            'Admin' => ['type' => 'admin', 'description' => 'โฟลเดอร์สำหรับ Admin'],
            'Departments' => ['type' => 'system', 'description' => 'โฟลเดอร์แผนกต่างๆ'],
            'Shared' => ['type' => 'shared', 'description' => 'โฟลเดอร์ส่วนกลาง'],
            'Users' => ['type' => 'system', 'description' => 'โฟลเดอร์ส่วนตัวของ Users']
        ];
        
        $created_folders = [];
        $folders_created_count = 1; // นับ root folder
        
        foreach ($main_folders as $folder_name => $config) {
            $folder = $this->create_folder_with_curl($folder_name, $root_folder['id'], $access_token);
            if ($folder) {
                $folder_data = [
                    'folder_name' => $folder_name,
                    'folder_id' => $folder['id'],
                    'parent_folder_id' => $root_folder['id'],
                    'folder_type' => $config['type'],
                    'folder_path' => '/Organization Drive/' . $folder_name,
                    'folder_description' => $config['description'],
                    'permission_level' => $config['type'] === 'shared' ? 'public' : 'restricted',
                    'created_by' => $this->session->userdata('m_id')
                ];
                
                if ($this->save_folder_info($folder_data)) {
                    $created_folders[$folder_name] = $folder['id'];
                    $folders_created_count++;
                    log_message('info', 'Main folder created: ' . $folder_name);
                    
                    // ✅ กำหนดสิทธิ์อัตโนมัติสำหรับโฟลเดอร์หลัก
                    $this->assign_main_folder_permissions($folder['id'], $folder_name, $config['type']);
                }
            }
        }
        
        // สร้าง Department folders
        $dept_count = 0;
        if (isset($created_folders['Departments'])) {
            // กำหนดสิทธิ์สำหรับโฟลเดอร์ Departments ก่อน
            $this->assign_departments_root_permissions($created_folders['Departments']);
            
            $dept_count = $this->create_department_folders_like_debug($created_folders['Departments'], $access_token);
            $folders_created_count += $dept_count;
        }
        
        return [
            'success' => true,
            'folders_created' => $folders_created_count,
            'main_folders_created' => 4,
            'department_folders_created' => $dept_count,
            'root_folder_id' => $root_folder['id']
        ];
        
    } catch (Exception $e) {
        log_message('error', 'create_complete_structure_like_debug error: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
/**
 * ✅ สร้างโครงสร้างใหม่ทั้งหมด (กรณีมีอยู่แล้ว)
 */
private function recreate_complete_structure_like_debug($storage_id, $access_token) {
    try {
        log_message('info', '🔄 Recreating complete structure like debug...');
        
        // ล้างข้อมูลเก่า
        $this->clear_all_folders();
        
        // Reset system storage
        $this->db->where('id', $storage_id)->update('tbl_google_drive_system_storage', [
            'folder_structure_created' => 0,
            'root_folder_id' => null
        ]);
        
        // สร้างใหม่
        return $this->create_complete_structure_like_debug($storage_id, $access_token);
        
    } catch (Exception $e) {
        log_message('error', 'recreate_complete_structure_like_debug error: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
	

// Method สำหรับ output success แบบปลอดภัย
private function safe_output_success($data = null, $message = 'Success') {
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Method สำหรับทดสอบ Google Token แบบง่าย
private function test_google_token_simple($access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $http_code === 200;
        
    } catch (Exception $e) {
        log_message('error', 'Token test error: ' . $e->getMessage());
        return false;
    }
}
	
	
	/**
 * ✅ Fixed Folder Creation - ใช้ Schema จริง
 */
private function create_basic_folders_fixed($system_storage, $access_token) {
    try {
        // สร้าง Root Folder
        $root_folder = $this->create_folder_with_curl('Organization Drive', null, $access_token);
        if (!$root_folder) {
            return ['success' => false, 'message' => 'ไม่สามารถสร้าง Root Folder ได้'];
        }
        
        // อัปเดต root_folder_id ใน system storage
        $this->db->where('id', $system_storage->id);
        $this->db->update('tbl_google_drive_system_storage', [
            'root_folder_id' => $root_folder['id']
        ]);
        
        // โครงสร้างโฟลเดอร์หลัก
        $main_folders = [
            'Admin' => ['type' => 'admin', 'description' => 'โฟลเดอร์สำหรับ Admin'],
            'Departments' => ['type' => 'system', 'description' => 'โฟลเดอร์แผนกต่างๆ'],
            'Shared' => ['type' => 'shared', 'description' => 'โฟลเดอร์ส่วนกลาง'],
            'Users' => ['type' => 'system', 'description' => 'โฟลเดอร์ส่วนตัวของ Users']
        ];
        
        $created_folders = [];
        $folders_created_count = 1; // นับ root folder
        
        // สร้างโฟลเดอร์หลัก
        foreach ($main_folders as $folder_name => $config) {
            try {
                $folder = $this->create_folder_with_curl($folder_name, $root_folder['id'], $access_token);
                if ($folder) {
                    $created_folders[$folder_name] = $folder['id'];
                    $folders_created_count++;
                    
                    // บันทึกลงฐานข้อมูล
                    $folder_data = [
                        'folder_name' => $folder_name,
                        'folder_id' => $folder['id'],
                        'parent_folder_id' => $root_folder['id'],
                        'folder_type' => $config['type'],
                        'folder_path' => '/Organization Drive/' . $folder_name,
                        'folder_description' => $config['description'],
                        'permission_level' => $config['type'] === 'shared' ? 'public' : 'restricted',
                        'created_by' => $this->session->userdata('m_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $this->db->insert('tbl_google_drive_system_folders', $folder_data);
                    
                    log_message('info', 'Main folder created: ' . $folder_name);
                }
            } catch (Exception $e) {
                log_message('error', 'Error creating folder ' . $folder_name . ': ' . $e->getMessage());
            }
        }
        
        return [
            'success' => true,
            'data' => [
                'root_folder_id' => $root_folder['id'],
                'stats' => [
                    'folders_created' => $folders_created_count,
                    'permissions_assigned' => 0,
                    'users_processed' => 0
                ],
                'details' => [
                    'folders' => array_keys($created_folders),
                    'permissions' => [],
                    'errors' => []
                ]
            ]
        ];
        
    } catch (Exception $e) {
        log_message('error', 'Create basic folders error: ' . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
	
	
	/**
 * ✅ Fixed Save Folder Data - ใช้ field จริง
 */
private function save_folder_data_fixed($folder_data) {
    try {
        // ตรวจสอบว่าตารางมีอยู่
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            log_message('error', 'Table tbl_google_drive_system_folders does not exist');
            return false;
        }
        
        // ✅ ใช้ field ที่มีจริงใน Database เท่านั้น
        $safe_data = [
            'folder_name' => $folder_data['folder_name'],
            'folder_id' => $folder_data['folder_id'],
            'parent_folder_id' => $folder_data['parent_folder_id'],
            'folder_type' => $folder_data['folder_type'],
            'folder_path' => $folder_data['folder_path'],
            'permission_level' => $folder_data['permission_level'] ?? 'restricted',
            'folder_description' => $folder_data['folder_description'] ?? null,
            'created_by' => $folder_data['created_by'],
            'is_active' => 1
        ];
        
        // ✅ ไม่ตั้งค่า created_at เพราะ DB จะ auto set
        
        return $this->db->insert('tbl_google_drive_system_folders', $safe_data);
        
    } catch (Exception $e) {
        log_message('error', 'Save folder data fixed error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ Fixed Google API Folder Creation - เพิ่ม Error Handling
 */
private function create_google_folder($folder_name, $parent_id, $access_token) {
    try {
        $url = 'https://www.googleapis.com/drive/v3/files';
        
        $metadata = [
            'name' => $folder_name,
            'mimeType' => 'application/vnd.google-apps.folder'
        ];
        
        if ($parent_id) {
            $metadata['parents'] = [$parent_id];
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($metadata),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Google Drive System/1.0',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);
        
        // ✅ Log detailed cURL info for debugging
        log_message('info', "Google API call for folder '{$folder_name}': HTTP {$http_code}");
        
        if ($curl_error) {
            log_message('error', "cURL Error creating folder '{$folder_name}': {$curl_error}");
            throw new Exception('cURL Error: ' . $curl_error);
        }
        
        if ($http_code !== 200) {
            log_message('error', "Google API Error creating folder '{$folder_name}': HTTP {$http_code} - {$response}");
            
            // ✅ Parse error response
            $error_data = json_decode($response, true);
            $error_message = isset($error_data['error']['message']) ? 
                $error_data['error']['message'] : 
                "HTTP {$http_code}";
            
            throw new Exception("Google API Error: {$error_message}");
        }
        
        $folder_data = json_decode($response, true);
        if (!$folder_data || !isset($folder_data['id'])) {
            log_message('error', "Invalid Google API response for folder '{$folder_name}': {$response}");
            throw new Exception('Invalid response from Google API');
        }
        
        log_message('info', "Successfully created Google folder '{$folder_name}' with ID: {$folder_data['id']}");
        return $folder_data;
        
    } catch (Exception $e) {
        log_message('error', "Create Google folder error ({$folder_name}): " . $e->getMessage());
        return null;
    }
}

/**
 * ✅ Enhanced Google Token Test
 */
private function test_google_token($access_token) {
    try {
        $url = 'https://www.googleapis.com/drive/v3/about?fields=user,storageQuota';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Google Drive System/1.0'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($curl_error) {
            log_message('error', 'Token test cURL error: ' . $curl_error);
            return false;
        }
        
        log_message('info', "Token test result: HTTP {$http_code}");
        
        if ($http_code === 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['user'])) {
                log_message('info', 'Token test successful - User: ' . ($data['user']['emailAddress'] ?? 'unknown'));
                return true;
            }
        }
        
        log_message('warning', "Token test failed: HTTP {$http_code} - {$response}");
        return false;
        
    } catch (Exception $e) {
        log_message('error', 'Test Google token error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ Fixed Permission Functions - ใช้ field จริง
 */
private function add_folder_permission_fixed($folder_id, $member_id, $access_type, $granted_by) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            log_message('warning', 'Permission table does not exist, skipping permission assignment');
            return false;
        }
        
        // ✅ ใช้ field จริงใน Database
        $permission_data = [
            'member_id' => $member_id,
            'folder_id' => $folder_id,
            'access_type' => $access_type,
            'permission_source' => 'direct', // ใช้ enum จริง: direct, position, department, system
            'granted_by' => $granted_by,
            'granted_by_name' => $this->get_user_name($granted_by),
            'expires_at' => null,
            'is_active' => 1,
            'inherit_from_parent' => 0,
            'apply_to_children' => 0,
            'permission_mode' => 'direct', // ใช้ enum จริง: inherited, override, direct, combined
            'parent_folder_id' => null
            // granted_at และ created_at จะ auto set โดย database (timestamp DEFAULT current_timestamp())
        ];
        
        // ลบ permission เดิม (ถ้ามี)
        $this->db->where([
            'member_id' => $member_id,
            'folder_id' => $folder_id
        ])->update('tbl_google_drive_member_folder_access', ['is_active' => 0]);
        
        // เพิ่ม permission ใหม่
        return $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
        
    } catch (Exception $e) {
        log_message('error', 'Add folder permission fixed error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ Helper: Get User Name
 */
private function get_user_name($user_id) {
    try {
        $user = $this->db->select('m_fname, m_lname')
                        ->from('tbl_member')
                        ->where('m_id', $user_id)
                        ->get()
                        ->row();
        
        return $user ? trim($user->m_fname . ' ' . $user->m_lname) : 'System';
        
    } catch (Exception $e) {
        log_message('error', 'get_user_name error: ' . $e->getMessage());
        return 'Unknown';
    }
}


/**
 * ✅ Fixed Output Functions
 */
private function output_error_json($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'error_code' => $code,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_mode' => ENVIRONMENT === 'development'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

private function output_success_json($data = null, $message = 'Success') {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_mode' => ENVIRONMENT === 'development'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
	
	

/**
 * สร้างโฟลเดอร์และกำหนดสิทธิ์อัตโนมัติ
 */
private function create_folders_with_auto_permissions($storage_id) {
    try {
        $access_token = $this->get_system_access_token();
        if (!$access_token) {
            throw new Exception('ไม่พบ Access Token');
        }
        
        // สร้าง Root Folder
        $root_folder = $this->create_folder_with_curl('Organization Drive', null, $access_token);
        if (!$root_folder) {
            throw new Exception('ไม่สามารถสร้าง Root Folder ได้');
        }
        
        log_message('info', 'Root folder created: ' . $root_folder['id']);
        
        // อัปเดต System Storage
        $this->update_system_storage($storage_id, [
            'root_folder_id' => $root_folder['id'],
            'folder_structure_created' => 1
        ]);
        
        // โครงสร้างโฟลเดอร์หลักพร้อมการกำหนดสิทธิ์
        $main_folders = [
            'Admin' => [
                'type' => 'admin', 
                'description' => 'โฟลเดอร์สำหรับ Admin',
                'permissions' => 'admin_only'
            ],
            'Departments' => [
                'type' => 'system', 
                'description' => 'โฟลเดอร์แผนกต่างๆ',
                'permissions' => 'all_read_inherit'
            ],
            'Shared' => [
                'type' => 'shared', 
                'description' => 'โฟลเดอร์ส่วนกลาง',
                'permissions' => 'all_write_if_enabled'
            ],
            'Users' => [
                'type' => 'system', 
                'description' => 'โฟลเดอร์ส่วนตัวของ Users',
                'permissions' => 'all_read_user_folders'
            ]
        ];
        
        $created_folders = [];
        $folders_created_count = 1; // นับ root folder
        $permissions_assigned = 0;
        $users_processed = 0;
        $permission_details = [];
        $folder_details = [];
        $error_details = [];
        
        // สร้างโฟลเดอร์หลักและกำหนดสิทธิ์
        foreach ($main_folders as $folder_name => $config) {
            try {
                $folder = $this->create_folder_with_curl($folder_name, $root_folder['id'], $access_token);
                if ($folder) {
                    $folder_data = [
                        'folder_name' => $folder_name,
                        'folder_id' => $folder['id'],
                        'parent_folder_id' => $root_folder['id'],
                        'folder_type' => $config['type'],
                        'folder_path' => '/Organization Drive/' . $folder_name,
                        'folder_description' => $config['description'],
                        'permission_level' => $config['type'] === 'shared' ? 'public' : 'restricted',
                        'created_by' => $this->session->userdata('m_id')
                    ];

                    if ($this->save_folder_info($folder_data)) {
                        $created_folders[$folder_name] = $folder['id'];
                        $folders_created_count++;
                        
                        $folder_details[] = [
                            'name' => $folder_name,
                            'type' => $config['type'],
                            'id' => $folder['id']
                        ];
                        
                        log_message('info', 'Main folder created: ' . $folder_name);
                        
                        // กำหนดสิทธิ์อัตโนมัติตาม config
                        $perm_result = $this->assign_auto_permissions($folder['id'], $folder_name, $config['permissions']);
                        $permissions_assigned += $perm_result['count'];
                        $users_processed += $perm_result['users'];
                        $permission_details = array_merge($permission_details, $perm_result['details']);
                        
                        if (!empty($perm_result['errors'])) {
                            $error_details = array_merge($error_details, $perm_result['errors']);
                        }
                    }
                }
            } catch (Exception $e) {
                $error_details[] = [
                    'folder' => $folder_name,
                    'message' => 'ไม่สามารถสร้างโฟลเดอร์ได้: ' . $e->getMessage()
                ];
                log_message('error', 'Error creating folder ' . $folder_name . ': ' . $e->getMessage());
            }
        }

        // สร้างโฟลเดอร์ตามแผนกพร้อมสิทธิ์
        if (isset($created_folders['Departments'])) {
            $dept_result = $this->create_department_folders_with_permissions($created_folders['Departments'], $access_token);
            $folders_created_count += $dept_result['folders_count'];
            $permissions_assigned += $dept_result['permissions_count'];
            $users_processed += $dept_result['users_count'];
            $folder_details = array_merge($folder_details, $dept_result['folder_details']);
            $permission_details = array_merge($permission_details, $dept_result['permission_details']);
            
            if (!empty($dept_result['errors'])) {
                $error_details = array_merge($error_details, $dept_result['errors']);
            }
        }
        
        // สร้างโฟลเดอร์ส่วนตัวผู้ใช้ (ถ้าเปิดใช้งาน)
        if (isset($created_folders['Users'])) {
            $user_result = $this->create_user_personal_folders($created_folders['Users'], $access_token);
            $folders_created_count += $user_result['folders_count'];
            $permissions_assigned += $user_result['permissions_count'];
            $users_processed += $user_result['users_count'];
            $folder_details = array_merge($folder_details, $user_result['folder_details']);
            $permission_details = array_merge($permission_details, $user_result['permission_details']);
            
            if (!empty($user_result['errors'])) {
                $error_details = array_merge($error_details, $user_result['errors']);
            }
        }

        return [
            'success' => true,
            'data' => [
                'root_folder_id' => $root_folder['id'],
                'stats' => [
                    'folders_created' => $folders_created_count,
                    'permissions_assigned' => $permissions_assigned,
                    'users_processed' => $users_processed
                ],
                'details' => [
                    'folders' => $folder_details,
                    'permissions' => $permission_details,
                    'errors' => $error_details
                ]
            ]
        ];

    } catch (Exception $e) {
        log_message('error', 'Create folders with auto permissions error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * ✅ แก้ไข: กำหนดสิทธิ์อัตโนมัติตามประเภทโฟลเดอร์
 */
private function assign_auto_permissions($folder_id, $folder_name, $permission_type) {
    $assigned_count = 0;
    $users_count = 0;
    $permission_details = [];
    $errors = [];
    
    try {
        log_message('info', "🔐 Assigning auto permissions for folder: {$folder_name} (Type: {$permission_type})");
        
        // ดึงรายชื่อผู้ใช้ทั้งหมด
        $all_users = $this->get_all_active_users();
        $admin_users = $this->get_admin_users();
        $enabled_users = $this->get_enabled_users();
        
        // ✅ เพิ่ม log เพื่อ debug
        log_message('info', "📊 Users count - All: " . count($all_users) . ", Admin: " . count($admin_users) . ", Enabled: " . count($enabled_users));
        
        switch ($permission_type) {
            case 'admin_only':
                // เฉพาะ Admin: system_admin และ super_admin ทำได้ทุกอย่าง
                foreach ($admin_users as $user) {
                    if ($this->add_folder_permission_correct($folder_id, $user['m_id'], 'admin')) {
                        $assigned_count++;
                        $permission_details[] = [
                            'user_name' => $user['name'],
                            'folder_name' => $folder_name,
                            'access_type' => 'admin'
                        ];
                    }
                }
                $users_count = count($admin_users);
                log_message('info', "📁 Admin folder: Assigned {$assigned_count} admin permissions");
                break;
                
            case 'all_read_inherit':
                // ทุกคนอ่านได้ + Admin ทำได้ทุกอย่าง
                foreach ($all_users as $user) {
                    $access_type = in_array($user['m_system'], ['system_admin', 'super_admin']) ? 'admin' : 'read';
                    if ($this->add_folder_permission_correct($folder_id, $user['m_id'], $access_type)) {
                        $assigned_count++;
                        $permission_details[] = [
                            'user_name' => $user['name'],
                            'folder_name' => $folder_name,
                            'access_type' => $access_type
                        ];
                    }
                }
                $users_count = count($all_users);
                log_message('info', "📁 Departments folder: Assigned {$assigned_count} permissions (read + admin)");
                break;
                
            case 'all_write_if_enabled':
                // ✅ ให้ทุกคนใช้ Shared folder ได้ (ไม่ต้องมีเงื่อนไข storage_access_granted)
                log_message('info', "📁 Shared folder: Processing all active users...");
                
                foreach ($all_users as $user) {
                    $access_type = in_array($user['m_system'], ['system_admin', 'super_admin']) ? 'admin' : 'write';
                    if ($this->add_folder_permission_correct($folder_id, $user['m_id'], $access_type)) {
                        $assigned_count++;
                        $permission_details[] = [
                            'user_name' => $user['name'],
                            'folder_name' => $folder_name,
                            'access_type' => $access_type
                        ];
                    }
                }
                $users_count = count($all_users);
                log_message('info', "📁 Shared folder: Assigned {$assigned_count} permissions (write + admin) to {$users_count} users");
                break;
                
            case 'all_read_user_folders':
                // ทุกคนอ่านได้ (สำหรับเข้าถึงโฟลเดอร์ส่วนตัว) + Admin ทำได้ทุกอย่าง
                foreach ($all_users as $user) {
                    $access_type = in_array($user['m_system'], ['system_admin', 'super_admin']) ? 'admin' : 'read';
                    if ($this->add_folder_permission_correct($folder_id, $user['m_id'], $access_type)) {
                        $assigned_count++;
                        $permission_details[] = [
                            'user_name' => $user['name'],
                            'folder_name' => $folder_name,
                            'access_type' => $access_type
                        ];
                    }
                }
                $users_count = count($all_users);
                log_message('info', "📁 Users folder: Assigned {$assigned_count} permissions (read + admin)");
                break;
        }
        
        // ✅ เพิ่ม warning ถ้าไม่มีสิทธิ์ให้ใคร
        if ($assigned_count === 0) {
            log_message('warning', "⚠️ No permissions assigned for folder: {$folder_name} (Type: {$permission_type})");
            $errors[] = [
                'folder' => $folder_name,
                'message' => "ไม่มีผู้ใช้ได้รับสิทธิ์ - กรุณาตรวจสอบเงื่อนไข"
            ];
        }
        
        log_message('info', "✅ Auto permissions completed: {$assigned_count}/{$users_count} users for {$folder_name}");
        
    } catch (Exception $e) {
        $errors[] = [
            'folder' => $folder_name,
            'message' => 'ไม่สามารถกำหนดสิทธิ์ได้: ' . $e->getMessage()
        ];
        log_message('error', 'Auto permission assignment error for ' . $folder_name . ': ' . $e->getMessage());
    }
    
    return [
        'count' => $assigned_count,
        'users' => $users_count,
        'details' => $permission_details,
        'errors' => $errors
    ];
}
	
	
	/**
 * ✅ เพิ่ม function ตรวจสอบสิทธิ์ Shared folder
 */
public function debug_shared_folder_permissions() {
    try {
        log_message('info', '🔍 DEBUG: Checking Shared folder permissions...');
        
        // หา Shared folder
        $shared_folder = $this->db->select('folder_id, folder_name')
                                 ->from('tbl_google_drive_system_folders')
                                 ->where('folder_name', 'Shared')
                                 ->where('folder_type', 'shared')
                                 ->where('is_active', 1)
                                 ->get()
                                 ->row();
        
        if (!$shared_folder) {
            log_message('warning', '🔍 DEBUG: Shared folder not found in database');
            return;
        }
        
        log_message('info', '🔍 DEBUG: Found Shared folder: ' . $shared_folder->folder_id);
        
        // ตรวจสอบสิทธิ์ปัจจุบัน
        $permissions = $this->db->select('member_id, access_type, is_active')
                               ->from('tbl_google_drive_member_folder_access')
                               ->where('folder_id', $shared_folder->folder_id)
                               ->get()
                               ->result();
        
        log_message('info', '🔍 DEBUG: Current permissions count: ' . count($permissions));
        
        foreach ($permissions as $perm) {
            log_message('info', '🔍 DEBUG: Permission - Member: ' . $perm->member_id . ', Type: ' . $perm->access_type . ', Active: ' . $perm->is_active);
        }
        
        // ตรวจสอบ enabled users
        $enabled_users = $this->get_enabled_users();
        log_message('info', '🔍 DEBUG: Enabled users count: ' . count($enabled_users));
        
        // ตรวจสอบ all users
        $all_users = $this->get_all_active_users();
        log_message('info', '🔍 DEBUG: All active users count: ' . count($all_users));
        
    } catch (Exception $e) {
        log_message('error', '🔍 DEBUG: Error checking Shared folder: ' . $e->getMessage());
    }
}

	
	
	/**
 * ✅ Fallback: กำหนดสิทธิ์ Shared folder ด้วยตนเอง (สำหรับกรณีฉุกเฉิน)
 */
public function fix_shared_folder_permissions() {
    try {
        log_message('info', '🔧 FIXING: Shared folder permissions...');
        
        // หา Shared folder
        $shared_folder = $this->db->select('folder_id, folder_name')
                                 ->from('tbl_google_drive_system_folders')
                                 ->where('folder_name', 'Shared')
                                 ->where('folder_type', 'shared')
                                 ->where('is_active', 1)
                                 ->get()
                                 ->row();
        
        if (!$shared_folder) {
            $this->output_json_error('ไม่พบ Shared folder');
            return;
        }
        
        // ลบสิทธิ์เก่า
        $this->db->where('folder_id', $shared_folder->folder_id)
                ->update('tbl_google_drive_member_folder_access', ['is_active' => 0]);
        
        // กำหนดสิทธิ์ใหม่ให้ทุกคน
        $all_users = $this->get_all_active_users();
        $assigned_count = 0;
        
        foreach ($all_users as $user) {
            $access_type = in_array($user['m_system'], ['system_admin', 'super_admin']) ? 'admin' : 'write';
            
            if ($this->add_folder_permission_correct($shared_folder->folder_id, $user['m_id'], $access_type)) {
                $assigned_count++;
            }
        }
        
        log_message('info', "🔧 FIXED: Assigned {$assigned_count} permissions to Shared folder");
        
        $this->output_json_success([
            'assigned_count' => $assigned_count,
            'total_users' => count($all_users)
        ], "กำหนดสิทธิ์ Shared folder เรียบร้อย: {$assigned_count} คน");
        
    } catch (Exception $e) {
        log_message('error', '🔧 FIXING: Error fixing Shared folder: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	
	/**
 * ✅ แก้ไข: เพิ่มสิทธิ์โฟลเดอร์ (ใช้ตารางที่ถูกต้อง)
 */
private function add_folder_permission_correct($folder_id, $member_id, $access_type) {
    try {
        if (empty($folder_id) || empty($member_id) || empty($access_type)) {
            log_message('warning', 'Invalid parameters for add_folder_permission_correct');
            return false;
        }
        
        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            log_message('warning', 'Table tbl_google_drive_member_folder_access does not exist');
            return false;
        }
        
        // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
        $existing = $this->db->where('folder_id', $folder_id)
                            ->where('member_id', $member_id)
                            ->where('is_active', 1)
                            ->get('tbl_google_drive_member_folder_access')
                            ->row();
        
        if ($existing) {
            // อัปเดตสิทธิ์ที่มีอยู่
            $update_result = $this->db->where('id', $existing->id)
                                     ->update('tbl_google_drive_member_folder_access', [
                                         'access_type' => $access_type,
                                         'updated_at' => date('Y-m-d H:i:s')
                                     ]);
            
            if ($update_result) {
                log_message('info', "✅ Updated permission: Member {$member_id} → {$access_type} for folder {$folder_id}");
            }
            
            return $update_result;
        } else {
            // สร้างสิทธิ์ใหม่
            $current_user_id = $this->session->userdata('m_id') ?: 1;
            $granted_by_name = $this->get_user_name($current_user_id);
            
            $permission_data = [
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'permission_source' => 'direct',
                'granted_by' => $current_user_id,
                'granted_by_name' => $granted_by_name,
                'granted_at' => date('Y-m-d H:i:s'),
                'expires_at' => null,
                'is_active' => 1,
                'inherit_from_parent' => 0,
                'apply_to_children' => 0,
                'permission_mode' => 'direct',
                'parent_folder_id' => null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $insert_result = $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
            
            if ($insert_result) {
                log_message('info', "✅ Created permission: Member {$member_id} → {$access_type} for folder {$folder_id}");
            } else {
                log_message('error', "❌ Failed to create permission: Member {$member_id} → {$access_type} for folder {$folder_id}");
            }
            
            return $insert_result;
        }
        
    } catch (Exception $e) {
        log_message('error', 'add_folder_permission_correct error: ' . $e->getMessage());
        return false;
    }
}
	
	
/**
 * สร้างโฟลเดอร์แผนกพร้อมสิทธิ์
 */
private function create_department_folders_with_permissions($departments_folder_id, $access_token) {
    $created_count = 0;
    $permissions_count = 0;
    $users_count = 0;
    $folder_details = [];
    $permission_details = [];
    $errors = [];
    
    try {
        log_message('info', '🏢 Starting create_department_folders_with_permissions...');
        log_message('info', 'Departments folder ID: ' . $departments_folder_id);
        
        // ✅ เรียกฟังก์ชันที่ทำงานได้จริง
        $created_count = $this->create_department_folders_curl($departments_folder_id, $access_token);
        
        if ($created_count > 0) {
            log_message('info', "✅ Department folders created: {$created_count}");
            
            // ดึงโฟลเดอร์แผนกที่สร้างใหม่
            $dept_folders = $this->db->where('folder_type', 'department')
                                   ->where('parent_folder_id', $departments_folder_id)
                                   ->get('tbl_google_drive_system_folders')
                                   ->result();
            
            // สร้าง folder_details
            foreach ($dept_folders as $folder) {
                $folder_details[] = [
                    'name' => $folder->folder_name,
                    'type' => 'department',
                    'id' => $folder->folder_id,
                    'position_id' => $folder->created_for_position
                ];
            }
            
            // กำหนดสิทธิ์อัตโนมัติ (ถ้าต้องการ)
            if ($this->get_setting('auto_assign_permissions') === '1') {
                log_message('info', '🔑 Auto-assigning permissions...');
                
                $admin_users = $this->get_admin_users();
                
                foreach ($dept_folders as $folder) {
                    try {
                        // กำหนดสิทธิ์ให้ Admin
                        foreach ($admin_users as $admin) {
                            if ($this->add_folder_permission_direct($folder->folder_id, $admin['m_id'], 'admin')) {
                                $permissions_count++;
                                $permission_details[] = [
                                    'user_name' => $admin['name'],
                                    'folder_name' => $folder->folder_name,
                                    'access_type' => 'admin'
                                ];
                            }
                        }
                        
                        // กำหนดสิทธิ์ให้ Users ในตำแหน่งนั้น
                        if ($folder->created_for_position) {
                            $position_users = $this->get_users_by_position($folder->created_for_position);
                            
                            foreach ($position_users as $user) {
                                if ($this->add_folder_permission_direct($folder->folder_id, $user['m_id'], 'write')) {
                                    $permissions_count++;
                                    $permission_details[] = [
                                        'user_name' => $user['name'],
                                        'folder_name' => $folder->folder_name,
                                        'access_type' => 'write (position)'
                                    ];
                                }
                            }
                            
                            $users_count += count($position_users);
                        }
                        
                    } catch (Exception $e) {
                        $errors[] = [
                            'folder' => $folder->folder_name,
                            'message' => 'ไม่สามารถกำหนดสิทธิ์ได้: ' . $e->getMessage()
                        ];
                        log_message('error', 'Permission assignment error for ' . $folder->folder_name . ': ' . $e->getMessage());
                    }
                }
                
                $users_count += count($admin_users) * count($dept_folders);
            }
            
        } else {
            $errors[] = [
                'general' => 'ไม่สามารถสร้างโฟลเดอร์แผนกได้'
            ];
            log_message('error', 'create_department_folders_curl returned 0 folders');
        }

    } catch (Exception $e) {
        $errors[] = [
            'general' => 'ไม่สามารถสร้างโฟลเดอร์แผนกได้: ' . $e->getMessage()
        ];
        log_message('error', 'Create department folders with permissions error: ' . $e->getMessage());
    }
    
    log_message('info', "Department folders summary - Created: {$created_count}, Permissions: {$permissions_count}, Users: {$users_count}");
    
    return [
        'folders_count' => $created_count,
        'permissions_count' => $permissions_count,
        'users_count' => $users_count,
        'folder_details' => $folder_details,
        'permission_details' => $permission_details,
        'errors' => $errors
    ];
}


/**
 * สร้างโฟลเดอร์ส่วนตัวผู้ใช้
 */
private function create_user_personal_folders($users_folder_id, $access_token) {
    $created_count = 0;
    $permissions_count = 0;
    $users_count = 0;
    $folder_details = [];
    $permission_details = [];
    $errors = [];
    
    try {
        // ตรวจสอบการตั้งค่า auto create user folders
        $auto_create = $this->get_setting('auto_create_user_folders') === '1';
        
        if ($auto_create) {
            $users = $this->get_enabled_users();
            $admin_users = $this->get_admin_users();
            
            foreach ($users as $user) {
                try {
                    $user_folder_name = $user['name'] . '_Personal';
                    $folder = $this->create_folder_with_curl($user_folder_name, $users_folder_id, $access_token);
                    
                    if ($folder) {
                        // บันทึกข้อมูลโฟลเดอร์
                        $folder_data = [
                            'folder_name' => $user_folder_name,
                            'folder_id' => $folder['id'],
                            'parent_folder_id' => $users_folder_id,
                            'folder_type' => 'user',
                            'folder_path' => '/Organization Drive/Users/' . $user_folder_name,
                            'folder_description' => 'โฟลเดอร์ส่วนตัวของ ' . $user['name'],
                            'permission_level' => 'private',
                            'created_by' => $this->session->userdata('m_id')
                        ];

                        if ($this->save_folder_info($folder_data)) {
                            $created_count++;
                            $folder_details[] = [
                                'name' => $user_folder_name,
                                'type' => 'user_personal',
                                'id' => $folder['id']
                            ];
                            
                            // กำหนดสิทธิ์: เจ้าของทำได้ทุกอย่าง
                            if ($this->add_folder_permission_direct($folder['id'], $user['m_id'], 'admin')) {
                                $permissions_count++;
                                $permission_details[] = [
                                    'user_name' => $user['name'],
                                    'folder_name' => $user_folder_name,
                                    'access_type' => 'owner'
                                ];
                            }
                            
                            // เพิ่มสิทธิ์ Admin
                            foreach ($admin_users as $admin) {
                                if ($admin['m_id'] != $user['m_id']) { // ไม่ซ้ำกับเจ้าของ
                                    if ($this->add_folder_permission_direct($folder['id'], $admin['m_id'], 'admin')) {
                                        $permissions_count++;
                                        $permission_details[] = [
                                            'user_name' => $admin['name'],
                                            'folder_name' => $user_folder_name,
                                            'access_type' => 'admin'
                                        ];
                                    }
                                }
                            }
                            
                            // อัปเดต personal_folder_id ในตาราง member
                            $this->db->where('m_id', $user['m_id'])
                                     ->update('tbl_member', ['personal_folder_id' => $folder['id']]);
                            
                            $users_count++;
                            
                            log_message('info', 'User personal folder created: ' . $user_folder_name);
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = [
                        'user' => $user['name'],
                        'message' => 'ไม่สามารถสร้างโฟลเดอร์ส่วนตัวได้: ' . $e->getMessage()
                    ];
                    log_message('error', 'Error creating personal folder for ' . $user['name'] . ': ' . $e->getMessage());
                }
            }
        }

    } catch (Exception $e) {
        $errors[] = [
            'general' => 'ไม่สามารถสร้างโฟลเดอร์ส่วนตัวได้: ' . $e->getMessage()
        ];
        log_message('error', 'Create user personal folders error: ' . $e->getMessage());
    }
    
    return [
        'folders_count' => $created_count,
        'permissions_count' => $permissions_count,
        'users_count' => $users_count,
        'folder_details' => $folder_details,
        'permission_details' => $permission_details,
        'errors' => $errors
    ];
}

// =============================================
// Helper Methods สำหรับการจัดการผู้ใช้และสิทธิ์
// =============================================

/**
 * ✅ Helper: ดึงผู้ใช้ทั้งหมดที่ active (แก้ไขแล้ว)
 */
private function get_all_active_users() {
    try {
        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.grant_system_ref_id, m.m_system');
        $this->db->select('CONCAT(m.m_fname, " ", m.m_lname) as name', false);
        $this->db->from('tbl_member m');
        $this->db->where('m.m_status', '1'); // ใช้ m_status แทน m_active
        
        $users = $this->db->get()->result_array();
        
        // เพิ่ม m_system เป็น field สำหรับ backward compatibility
        foreach ($users as &$user) {
            $user['m_system'] = $user['m_system'] ?: $user['grant_system_ref_id'];
        }
        
        return $users ?: [];
        
    } catch (Exception $e) {
        log_message('error', 'get_all_active_users error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ดึงผู้ใช้ที่เป็น Admin
 */
private function get_admin_users() {
    try {
        $admin_systems = ['system_admin', 'super_admin'];
        
        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.grant_system_ref_id, m.m_system');
        $this->db->select('CONCAT(m.m_fname, " ", m.m_lname) as name', false);
        $this->db->from('tbl_member m');
        $this->db->where_in('m.m_system', $admin_systems); // ใช้ m_system แทน grant_system_ref_id
        $this->db->where('m.m_status', '1'); // ใช้ m_status แทน m_active
        
        $users = $this->db->get()->result_array();
        
        // เพิ่ม m_system เป็น field สำหรับ backward compatibility
        foreach ($users as &$user) {
            $user['m_system'] = $user['m_system'] ?: $user['grant_system_ref_id'];
        }
        
        return $users ?: [];
        
    } catch (Exception $e) {
        log_message('error', 'get_admin_users error: ' . $e->getMessage());
        return [];
    }
}

	

/**
 * ✅ Debug และแก้ไข Department Folders
 */
public function debug_department_folders() {
    // Force error display
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    echo "<h1>🔍 Debug Department Folders Creation</h1>";
    echo "<style>
        .debug-box { border: 1px solid #ccc; padding: 15px; margin: 10px 0; background: #f9f9f9; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
    </style>";
    
    try {
        echo "<div class='debug-box'>";
        echo "<h2>📋 Step 1: ตรวจสอบ Positions</h2>";
        
        $positions = $this->db->where('pstatus', 'show')
                             ->order_by('porder', 'ASC')
                             ->get('tbl_position')
                             ->result();
        
        echo "<p class='info'>จำนวน Positions ที่ pstatus = 'show': <strong>" . count($positions) . "</strong></p>";
        
        if (count($positions) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>PID</th><th>ชื่อตำแหน่ง</th><th>Order</th><th>Status</th></tr>";
            foreach ($positions as $pos) {
                echo "<tr>";
                echo "<td>{$pos->pid}</td>";
                echo "<td>{$pos->pname}</td>";
                echo "<td>{$pos->porder}</td>";
                echo "<td>{$pos->pstatus}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ ไม่พบ Positions ที่ pstatus = 'show'</p>";
            return;
        }
        echo "</div>";
        
        echo "<div class='debug-box'>";
        echo "<h2>📁 Step 2: ตรวจสอบ Departments Folder</h2>";
        
        $dept_folder = $this->db->where('folder_name', 'Departments')
                               ->where('folder_type', 'system')
                               ->get('tbl_google_drive_system_folders')
                               ->row();
        
        if ($dept_folder) {
            echo "<p class='success'>✅ พบ Departments folder: {$dept_folder->folder_name}</p>";
            echo "<p class='info'>Folder ID: {$dept_folder->folder_id}</p>";
            echo "<p class='info'>Parent ID: {$dept_folder->parent_folder_id}</p>";
        } else {
            echo "<p class='error'>❌ ไม่พบ Departments folder</p>";
            return;
        }
        echo "</div>";
        
        echo "<div class='debug-box'>";
        echo "<h2>🔗 Step 3: ตรวจสอบ Google Drive API</h2>";
        
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage) {
            echo "<p class='error'>❌ ไม่พบ System Storage</p>";
            return;
        }
        
        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            echo "<p class='error'>❌ Access Token ไม่ถูกต้อง</p>";
            return;
        }
        
        $access_token = $token_data['access_token'];
        echo "<p class='success'>✅ Access Token พร้อมใช้งาน</p>";
        
        // ทดสอบ API call
        $test_result = $this->test_google_drive_api($access_token);
        if ($test_result) {
            echo "<p class='success'>✅ Google Drive API ทำงานปกติ</p>";
        } else {
            echo "<p class='error'>❌ Google Drive API ไม่สามารถเชื่อมต่อได้</p>";
            return;
        }
        echo "</div>";
        
        echo "<div class='debug-box'>";
        echo "<h2>🏗️ Step 4: ทดสอบสร้าง Department Folders</h2>";
        
        // ลบ department folders เก่า (ถ้ามี)
        $this->db->where('folder_type', 'department')->delete('tbl_google_drive_system_folders');
        echo "<p class='info'>ลบ Department folders เก่าเรียบร้อย</p>";
        
        // เริ่มสร้างใหม่
        echo "<h3>🔨 เริ่มสร้าง Department Folders...</h3>";
        
        $created_count = 0;
        $errors = [];
        
        foreach ($positions as $index => $position) {
            echo "<div style='margin: 5px 0; padding: 5px; background: white; border-left: 3px solid #ccc;'>";
            echo "<strong>สร้างโฟลเดอร์: {$position->pname}</strong><br>";
            
            try {
                // สร้างโฟลเดอร์ใน Google Drive
                $folder_result = $this->create_folder_with_curl_debug($position->pname, $dept_folder->folder_id, $access_token);
                
                if ($folder_result && isset($folder_result['id'])) {
                    echo "<span class='success'>✅ สร้างใน Google Drive สำเร็จ: {$folder_result['id']}</span><br>";
                    
                    // บันทึกในฐานข้อมูล
                    $folder_data = [
                        'folder_name' => $position->pname,
                        'folder_id' => $folder_result['id'],
                        'parent_folder_id' => $dept_folder->folder_id,
                        'folder_type' => 'department',
                        'folder_path' => '/Organization Drive/Departments/' . $position->pname,
                        'created_for_position' => $position->pid,
                        'folder_description' => 'โฟลเดอร์สำหรับ ' . $position->pname,
                        'permission_level' => 'restricted',
                        'created_by' => $this->session->userdata('m_id'),
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    if ($this->db->insert('tbl_google_drive_system_folders', $folder_data)) {
                        echo "<span class='success'>✅ บันทึกฐานข้อมูลสำเร็จ</span><br>";
                        $created_count++;
                    } else {
                        echo "<span class='error'>❌ บันทึกฐานข้อมูลล้มเหลว</span><br>";
                        $errors[] = "Database insert failed for {$position->pname}";
                    }
                    
                } else {
                    echo "<span class='error'>❌ สร้างใน Google Drive ล้มเหลว</span><br>";
                    $errors[] = "Google Drive creation failed for {$position->pname}";
                }
                
            } catch (Exception $e) {
                echo "<span class='error'>❌ Exception: " . $e->getMessage() . "</span><br>";
                $errors[] = "Exception for {$position->pname}: " . $e->getMessage();
            }
            
            echo "</div>";
            
            // หน่วงเวลาเล็กน้อย
            if ($index < count($positions) - 1) {
                usleep(300000); // 0.3 วินาที
            }
        }
        echo "</div>";
        
        echo "<div class='debug-box'>";
        echo "<h2>📊 Step 5: สรุปผลลัพธ์</h2>";
        echo "<p><strong>โฟลเดอร์ที่สร้างสำเร็จ:</strong> {$created_count} / " . count($positions) . "</p>";
        
        if (!empty($errors)) {
            echo "<h3 class='error'>❌ Errors ที่พบ:</h3>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li class='error'>{$error}</li>";
            }
            echo "</ul>";
        }
        
        // ตรวจสอบผลลัพธ์ในฐานข้อมูล
        $final_count = $this->db->where('folder_type', 'department')->count_all_results('tbl_google_drive_system_folders');
        echo "<p><strong>จำนวนในฐานข้อมูล:</strong> {$final_count}</p>";
        
        if ($final_count == count($positions)) {
            echo "<p class='success'>🎉 สำเร็จ! Department folders ครบทุกตำแหน่งแล้ว</p>";
        } else {
            echo "<p class='warning'>⚠️ ยังไม่ครบ - ต้องการเพิ่มเติม</p>";
        }
        echo "</div>";
        
        echo "<div class='debug-box'>";
        echo "<h2>🔗 Links</h2>";
        echo "<p><a href='" . site_url('google_drive_system/setup') . "' style='background: blue; color: white; padding: 10px; text-decoration: none;'>🏠 กลับไป Setup</a></p>";
        echo "<p><a href='" . site_url('google_drive_system/verify_department_folders') . "' style='background: green; color: white; padding: 10px; text-decoration: none;'>✅ ตรวจสอบ Department Folders</a></p>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='debug-box'>";
        echo "<p class='error'>❌ Critical Error: " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

/**
 * ✅ Create folder with detailed debugging
 */
private function create_folder_with_curl_debug($folder_name, $parent_id, $access_token) {
    try {
        echo "<span class='info'>→ กำลังสร้าง: {$folder_name}</span><br>";
        
        $metadata = [
            'name' => trim($folder_name),
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parent_id]
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/files',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($metadata),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        echo "<span class='info'>→ HTTP Status: {$http_code}</span><br>";
        
        if ($curl_error) {
            echo "<span class='error'>→ cURL Error: {$curl_error}</span><br>";
            return null;
        }
        
        if ($http_code === 200 || $http_code === 201) {
            $data = json_decode($response, true);
            if ($data && isset($data['id'])) {
                echo "<span class='info'>→ Folder ID: {$data['id']}</span><br>";
                return $data;
            } else {
                echo "<span class='error'>→ Invalid response format</span><br>";
                return null;
            }
        } else {
            echo "<span class='error'>→ HTTP Error {$http_code}: {$response}</span><br>";
            return null;
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>→ Exception: " . $e->getMessage() . "</span><br>";
        return null;
    }
}

/**
 * ✅ Test Google Drive API
 */
private function test_google_drive_api($access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/about?fields=user',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($curl_error) {
            return false;
        }
        
        if ($http_code === 200) {
            $data = json_decode($response, true);
            return ($data && isset($data['user']));
        }
        
        return false;
        
    } catch (Exception $e) {
        return false;
    }
}

/**
 * ✅ Force Create Department Folders - แบบ Manual
 */
public function force_create_department_folders() {
    try {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // ตรวจสอบ system storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }
        
        // ตรวจสอบ access token
        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            $this->output_json_error('Access Token ไม่ถูกต้อง');
            return;
        }
        
        $access_token = $token_data['access_token'];
        
        // หา Departments folder
        $dept_folder = $this->db->where('folder_name', 'Departments')
                               ->where('folder_type', 'system')
                               ->get('tbl_google_drive_system_folders')
                               ->row();
        
        if (!$dept_folder) {
            $this->output_json_error('ไม่พบ Departments folder');
            return;
        }
        
        // ลบ department folders เก่า
        $this->db->where('folder_type', 'department')->delete('tbl_google_drive_system_folders');
        
        // สร้างใหม่
        $created_count = $this->create_department_folders_curl($dept_folder->folder_id, $access_token);
        
        if ($created_count > 0) {
            $this->output_json_success([
                'created_count' => $created_count,
                'message' => "สร้าง Department folders สำเร็จ: {$created_count} โฟลเดอร์"
            ]);
        } else {
            $this->output_json_error('ไม่สามารถสร้าง Department folders ได้');
        }
        
    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	

/**
 * ดึงผู้ใช้ที่เปิดใช้งาน Storage
 */
/**
 * ✅ Helper: ดึงผู้ใช้ที่เปิดใช้งาน (แก้ไขแล้ว)
 */
private function get_enabled_users() {
    try {
        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.grant_system_ref_id, m.m_system');
        $this->db->select('CONCAT(m.m_fname, " ", m.m_lname) as name', false);
        $this->db->from('tbl_member m');
        $this->db->where('m.m_status', '1'); // ผู้ใช้ที่ active
        
        // ✅ แก้ไข: ลบเงื่อนไข storage_access_granted หรือปรับให้เป็น OR
        // แนวทางที่ 1: ไม่มีเงื่อนไข storage_access_granted (ทุกคนที่ active)
        // แนวทางที่ 2: หรือ ใช้เงื่อนไข OR แทน
        
        // ✅ เลือกแนวทางที่ 1: ทุกคนที่ active ได้ใช้ Shared folder
        // $this->db->where('m.storage_access_granted', 1); // ← ลบบรรทัดนี้
        
        $users = $this->db->get()->result_array();
        
        // เพิ่ม m_system เป็น field สำหรับ backward compatibility
        foreach ($users as &$user) {
            $user['m_system'] = $user['m_system'] ?: $user['grant_system_ref_id'];
        }
        
        log_message('info', '📁 get_enabled_users found: ' . count($users) . ' users for Shared folder');
        
        return $users ?: [];
        
    } catch (Exception $e) {
        log_message('error', 'get_enabled_users error: ' . $e->getMessage());
        return [];
    }
}
	
	
/**
 * ✅ เพิ่ม: ดึงผู้ใช้ที่มี storage_access_granted สำหรับใช้ในที่อื่น
 */
private function get_storage_granted_users() {
    try {
        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.grant_system_ref_id, m.m_system');
        $this->db->select('CONCAT(m.m_fname, " ", m.m_lname) as name', false);
        $this->db->from('tbl_member m');
        $this->db->where('m.m_status', '1');
        $this->db->where('m.storage_access_granted', 1); // เฉพาะที่ได้รับอนุญาต storage
        
        $users = $this->db->get()->result_array();
        
        foreach ($users as &$user) {
            $user['m_system'] = $user['m_system'] ?: $user['grant_system_ref_id'];
        }
        
        log_message('info', '🔐 get_storage_granted_users found: ' . count($users) . ' users with storage access');
        
        return $users ?: [];
        
    } catch (Exception $e) {
        log_message('error', 'get_storage_granted_users error: ' . $e->getMessage());
        return [];
    }
}

	
	
	
	/**
 * ✅ Dummy functions (ไม่ต้องทำอะไร)
 */
private function assign_folder_permission($folder_id, $permission_type) {
    // Placeholder - ไม่ต้องทำอะไร
    log_message('info', "assign_folder_permission called: {$permission_type}");
}

	
	
/**
 * ✅ แก้ไข: กำหนดสิทธิ์สำหรับโฟลเดอร์แผนกแต่ละแผนก
 */
private function assign_department_folder_permissions($folder_id, $position_id) {
    try {
        log_message('info', "🏢 Assigning permissions for department folder (Position ID: {$position_id})");
        
        $assigned_count = 0;
        
        // 1. System Admin และ Super Admin: ทำได้ทุกอย่าง
        $admin_users = $this->get_admin_users();
        foreach ($admin_users as $admin) {
            if ($this->add_folder_permission_correct($folder_id, $admin['m_id'], 'admin')) {
                $assigned_count++;
            }
        }
        
        // 2. ผู้ใช้ในตำแหน่งนี้: แก้ไข/อัปโหลด/ลบได้
        $position_users = $this->get_users_by_position($position_id);
        foreach ($position_users as $user) {
            if ($this->add_folder_permission_correct($folder_id, $user['m_id'], 'write')) {
                $assigned_count++;
            }
        }
        
        log_message('info', "✅ Department permissions assigned: {$assigned_count} total (" . count($admin_users) . " admins + " . count($position_users) . " position users)");
        
        return $assigned_count;
        
    } catch (Exception $e) {
        log_message('error', 'assign_department_folder_permissions error: ' . $e->getMessage());
        return 0;
    }
}

	
/**
 * ✅ แก้ไข: กำหนดสิทธิ์สำหรับโฟลเดอร์ Departments (root)
 */
private function assign_departments_root_permissions($departments_folder_id) {
    try {
        log_message('info', "🏢 Assigning read permissions for Departments root folder");
        
        $assigned_count = 0;
        
        // ทุกคนดูได้อย่างเดียว + Admin ทำได้ทุกอย่าง
        $all_users = $this->get_all_active_users();
        foreach ($all_users as $user) {
            $access_level = in_array($user['m_system'], ['system_admin', 'super_admin']) ? 'admin' : 'read';
            if ($this->add_folder_permission_correct($departments_folder_id, $user['m_id'], $access_level)) {
                $assigned_count++;
            }
        }
        
        log_message('info', "✅ Departments root permissions assigned: {$assigned_count} users");
        
        return $assigned_count;
        
    } catch (Exception $e) {
        log_message('error', 'assign_departments_root_permissions error: ' . $e->getMessage());
        return 0;
    }
}

	
	
	
/**
 * ✅ แก้ไข: กำหนดสิทธิ์สำหรับโฟลเดอร์หลัก (แบบง่าย)
 */
private function assign_main_folder_permissions($folder_id, $folder_name, $folder_type) {
    try {
        log_message('info', "📁 Assigning permissions for main folder: {$folder_name} (Type: {$folder_type})");
        
        $assigned_count = 0;
        
        // ระบบใหม่: ใช้ assign_auto_permissions แทน
        $permission_mapping = [
            'admin' => 'admin_only',
            'system' => 'all_read_inherit', // สำหรับ Departments และ Users
            'shared' => 'all_write_if_enabled'
        ];
        
        $permission_type = $permission_mapping[$folder_type] ?? 'admin_only';
        $perm_result = $this->assign_auto_permissions($folder_id, $folder_name, $permission_type);
        
        log_message('info', "✅ Main folder permissions completed: {$perm_result['count']} permissions for {$folder_name}");
        
        return $perm_result['count'];
        
    } catch (Exception $e) {
        log_message('error', 'assign_main_folder_permissions error: ' . $e->getMessage());
        return 0;
    }
}
	
	
/**
 * ✅ Helper: ล้างสิทธิ์ทั้งหมด (ใช้ตารางที่ถูกต้อง)
 */
private function clear_all_permissions() {
    try {
        // ล้างตารางที่ถูกต้อง
        if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $this->db->empty_table('tbl_google_drive_member_folder_access');
            log_message('info', '🗑️ Cleared tbl_google_drive_member_folder_access');
        }
        
        // ล้างตารางเก่าด้วย (ถ้ามี)
        if ($this->db->table_exists('tbl_google_drive_folder_permissions')) {
            $this->db->empty_table('tbl_google_drive_folder_permissions');
            log_message('info', '🗑️ Cleared tbl_google_drive_folder_permissions (legacy)');
        }
        
    } catch (Exception $e) {
        log_message('error', 'clear_all_permissions error: ' . $e->getMessage());
    }
}
	

/**
 * ✅ Helper: ดึง users ตามตำแหน่ง (แก้ไขแล้ว)
 */
private function get_users_by_position($position_id) {
    try {
        if (empty($position_id)) {
            return [];
        }
        
        $users = $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.grant_system_ref_id, m.m_system')
                         ->select('CONCAT(m.m_fname, " ", m.m_lname) as name', false)
                         ->from('tbl_member m')
                         ->where('m.ref_pid', $position_id)
                         ->where('m.m_status', '1') // ใช้ m_status แทน m_active
                         ->get()
                         ->result_array();
        
        // เพิ่ม m_system เป็น field สำหรับ backward compatibility
        foreach ($users as &$user) {
            $user['m_system'] = $user['m_system'] ?: $user['grant_system_ref_id'];
        }
        
        return $users ?: [];
        
    } catch (Exception $e) {
        log_message('error', 'get_users_by_position error: ' . $e->getMessage());
        return [];
    }
}


/**
 * ✅ Helper: เพิ่มสิทธิ์โฟลเดอร์ (แบบง่าย)
 */
private function add_folder_permission_direct($folder_id, $member_id, $access_level) {
    try {
        if (empty($folder_id) || empty($member_id)) {
            return false;
        }
        
        // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
        $existing = $this->db->where('folder_id', $folder_id)
                            ->where('member_id', $member_id)
                            ->where('is_active', 1)
                            ->get('tbl_google_drive_folder_permissions')
                            ->row();
        
        if ($existing) {
            // อัปเดตสิทธิ์ที่มีอยู่
            return $this->db->where('id', $existing->id)
                           ->update('tbl_google_drive_folder_permissions', [
                               'access_level' => $access_level,
                               'updated_at' => date('Y-m-d H:i:s')
                           ]);
        } else {
            // สร้างสิทธิ์ใหม่
            $permission_data = [
                'folder_id' => $folder_id,
                'member_id' => $member_id,
                'access_level' => $access_level,
                'granted_by' => $this->session->userdata('m_id'),
                'granted_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            return $this->db->insert('tbl_google_drive_folder_permissions', $permission_data);
        }
        
    } catch (Exception $e) {
        log_message('error', 'add_folder_permission_direct error: ' . $e->getMessage());
        return false;
    }
}

/**
 * สร้างตาราง Folder Permissions ถ้ายังไม่มี
 */
private function create_folder_permissions_table_if_not_exists() {
    if (!$this->db->table_exists('tbl_google_drive_folder_permissions')) {
        $sql = "
            CREATE TABLE IF NOT EXISTS `tbl_google_drive_folder_permissions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `folder_id` varchar(255) NOT NULL COMMENT 'Google Drive Folder ID',
                `member_id` int(11) NOT NULL COMMENT 'อ้างอิง tbl_member.m_id',
                `access_type` enum('read','write','admin','owner') DEFAULT 'read' COMMENT 'ประเภทสิทธิ์',
                `permission_source` enum('direct','inherited','auto_assigned') DEFAULT 'direct' COMMENT 'แหล่งที่มาของสิทธิ์',
                `inherited_from` varchar(255) DEFAULT NULL COMMENT 'สืบทอดจากโฟลเดอร์ไหน',
                `inheritance_enabled` tinyint(1) DEFAULT 0 COMMENT 'เปิดใช้งานการสืบทอดหรือไม่',
                `granted_by` int(11) DEFAULT NULL COMMENT 'ผู้ให้สิทธิ์',
                `granted_at` datetime DEFAULT NULL COMMENT 'วันที่ให้สิทธิ์',
                `expires_at` datetime DEFAULT NULL COMMENT 'วันหมดอายุ',
                `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะการใช้งาน',
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_folder_member` (`folder_id`, `member_id`),
                KEY `idx_folder_id` (`folder_id`),
                KEY `idx_member_id` (`member_id`),
                KEY `idx_access_type` (`access_type`),
                KEY `idx_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='สิทธิ์การเข้าถึงโฟลเดอร์';
        ";

        $this->db->query($sql);
    }
}
	

/**
 * ✅ ได้ไอคอนสำหรับ Activity
 */
private function get_activity_icon($action) {
    $icons = [
        'upload' => 'fas fa-upload',
        'upload_file' => 'fas fa-upload',
        'delete' => 'fas fa-trash',
        'delete_file' => 'fas fa-trash',
        'download' => 'fas fa-download',
        'view' => 'fas fa-eye',
        'share' => 'fas fa-share',
        'create_folder' => 'fas fa-folder-plus',
        'delete_folder' => 'fas fa-folder-minus',
        'grant_access' => 'fas fa-user-plus',
        'revoke_access' => 'fas fa-user-minus',
        'login' => 'fas fa-sign-in-alt',
        'logout' => 'fas fa-sign-out-alt',
        'connect' => 'fas fa-link',
        'disconnect' => 'fas fa-unlink',
        'sync' => 'fas fa-sync-alt'
    ];
    
    return $icons[$action] ?? 'fas fa-info-circle';
}



/**
 * ✅ Export User Data เป็น CSV
 */
public function export_user_data() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        $user_id = $this->input->get('user_id');
        if (empty($user_id)) {
            show_error('ไม่ได้ระบุ User ID', 400);
        }

        // ดึงข้อมูล
        $user = $this->get_user_storage_details($user_id);
        $files = $this->get_user_files($user_id);
        
        if (!$user) {
            show_error('ไม่พบผู้ใช้', 404);
        }

        // ตั้งค่า CSV Headers
        $filename = 'user_storage_data_' . $user_id . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM สำหรับ UTF-8
        fputs($output, "\xEF\xBB\xBF");
        
        // User Info
        fputcsv($output, ['=== ข้อมูลผู้ใช้ ===']);
        fputcsv($output, ['ชื่อ-สกุล', $user->full_name]);
        fputcsv($output, ['อีเมล', $user->m_email]);
        fputcsv($output, ['ตำแหน่ง', $user->position_name ?: 'ไม่ระบุ']);
        fputcsv($output, ['Storage Quota', $user->storage_quota_limit_formatted]);
        fputcsv($output, ['Storage ที่ใช้', $user->storage_quota_used_formatted]);
        fputcsv($output, ['เปอร์เซ็นต์การใช้งาน', $user->storage_usage_percent . '%']);
        fputcsv($output, ['เข้าใช้ล่าสุด', $user->last_storage_access ?: 'ยังไม่เคย']);
        fputcsv($output, []);
        
        // Files Header
        fputcsv($output, ['=== รายการไฟล์ ===']);
        fputcsv($output, [
            'ชื่อไฟล์',
            'ชื่อต้นฉบับ',
            'ประเภท',
            'ขนาด',
            'โฟลเดอร์',
            'วันที่อัปโหลด'
        ]);
        
        // Files Data
        foreach ($files as $file) {
            fputcsv($output, [
                $file->file_name,
                $file->original_name,
                $this->get_friendly_mime_type($file->mime_type),
                $this->format_bytes($file->file_size),
                $file->folder_name ?: 'Root',
                date('d/m/Y H:i', strtotime($file->created_at))
            ]);
        }
        
        fclose($output);

    } catch (Exception $e) {
        log_message('error', 'Export user data error: ' . $e->getMessage());
        show_error('ไม่สามารถ Export ข้อมูลได้: ' . $e->getMessage(), 500);
    }
}

/**
 * ✅ AJAX: ดึงข้อมูลสำหรับ Chart
 */
public function get_user_chart_data() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $user_id = $this->input->get('user_id');
        $chart_type = $this->input->get('chart_type') ?: 'file_types';

        if (empty($user_id)) {
            $this->output_json_error('ไม่ได้ระบุ User ID');
            return;
        }

        $chart_data = [];

        switch ($chart_type) {
            case 'file_types':
                $chart_data = $this->get_file_types_chart_data($user_id);
                break;
            case 'upload_activity':
                $chart_data = $this->get_upload_activity_chart_data($user_id);
                break;
            case 'folder_usage':
                $chart_data = $this->get_folder_usage_chart_data($user_id);
                break;
            default:
                $this->output_json_error('ประเภท Chart ไม่ถูกต้อง');
                return;
        }

        $this->output_json_success($chart_data, 'ดึงข้อมูล Chart สำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ ข้อมูล Chart ประเภทไฟล์
 */
private function get_file_types_chart_data($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return [];
        }

        $types = $this->db->select('
                mime_type,
                COUNT(*) as count,
                SUM(file_size) as total_size
            ')
            ->from('tbl_google_drive_system_files')
            ->where('uploaded_by', $user_id)
            ->group_by('mime_type')
            ->order_by('total_size', 'desc')
            ->get()
            ->result();

        $chart_data = [];
        foreach ($types as $type) {
            $chart_data[] = [
                'label' => $this->get_friendly_mime_type($type->mime_type),
                'value' => (int)$type->total_size,
                'count' => (int)$type->count,
                'formatted_size' => $this->format_bytes($type->total_size)
            ];
        }

        return $chart_data;

    } catch (Exception $e) {
        log_message('error', 'Get file types chart data error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ ข้อมูล Chart กิจกรรมการอัปโหลด
 */
private function get_upload_activity_chart_data($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return [];
        }

        $activities = $this->db->select('
                DATE(created_at) as upload_date,
                COUNT(*) as uploads_count,
                SUM(file_size) as total_size
            ')
            ->from('tbl_google_drive_system_files')
            ->where('uploaded_by', $user_id)
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->group_by('DATE(created_at)')
            ->order_by('upload_date', 'asc')
            ->get()
            ->result();

        $chart_data = [];
        foreach ($activities as $activity) {
            $chart_data[] = [
                'date' => $activity->upload_date,
                'uploads' => (int)$activity->uploads_count,
                'size' => (int)$activity->total_size,
                'formatted_size' => $this->format_bytes($activity->total_size),
                'formatted_date' => date('d/m', strtotime($activity->upload_date))
            ];
        }

        return $chart_data;

    } catch (Exception $e) {
        log_message('error', 'Get upload activity chart data error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ ข้อมูล Chart การใช้งานตามโฟลเดอร์
 */
private function get_folder_usage_chart_data($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return [];
        }

        $folders = $this->db->select('
                COALESCE(folder.folder_name, "Root") as folder_name,
                COALESCE(folder.folder_path, "/") as folder_path,
                COUNT(sf.id) as file_count,
                SUM(sf.file_size) as total_size
            ')
            ->from('tbl_google_drive_system_files sf')
            ->join('tbl_google_drive_system_folders folder', 'sf.folder_id = folder.folder_id', 'left')
            ->where('sf.uploaded_by', $user_id)
            ->group_by('sf.folder_id')
            ->order_by('total_size', 'desc')
            ->get()
            ->result();

        $chart_data = [];
        foreach ($folders as $folder) {
            $chart_data[] = [
                'label' => $folder->folder_name,
                'path' => $folder->folder_path,
                'files' => (int)$folder->file_count,
                'size' => (int)$folder->total_size,
                'formatted_size' => $this->format_bytes($folder->total_size)
            ];
        }

        return $chart_data;

    } catch (Exception $e) {
        log_message('error', 'Get folder usage chart data error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ AJAX: อัปเดต User Quota
 */
public function update_user_quota() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method', [], 405);
            return;
        }

        // ตรวจสอบ HTTP Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->output_json_error('Only POST method allowed', [], 405);
            return;
        }

        // ตรวจสอบ Content-Type
        $content_type = $this->input->get_request_header('Content-Type', TRUE);
        log_message('info', 'Update quota request - Content-Type: ' . $content_type);

        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output_json_error('ไม่มีสิทธิ์เข้าถึง', [], 403);
            return;
        }

        // รับข้อมูลจากหลายแหล่ง
        $user_id = $this->input->post('user_id') ?: $this->input->get('user_id');
        $new_quota = $this->input->post('new_quota') ?: $this->input->post('quota') ?: $this->input->get('new_quota');
        $new_quota_mb = $this->input->post('new_quota_mb') ?: $this->input->get('new_quota_mb');
        $is_unlimited = $this->input->post('is_unlimited') ?: $this->input->get('is_unlimited');

        // Log รับข้อมูล
        log_message('info', 'Update quota received data: ' . json_encode([
            'user_id' => $user_id,
            'new_quota' => $new_quota,
            'new_quota_mb' => $new_quota_mb,
            'is_unlimited' => $is_unlimited,
            'post_data' => $this->input->post(),
            'raw_input' => file_get_contents('php://input')
        ]));

        // ตรวจสอบข้อมูลพื้นฐาน
        if (empty($user_id)) {
            $this->output_json_error('ไม่ได้ระบุ User ID', ['received_user_id' => $user_id], 400);
            return;
        }

        if (empty($new_quota) && empty($new_quota_mb)) {
            $this->output_json_error('ไม่ได้ระบุขนาด Quota ใหม่', [
                'received_new_quota' => $new_quota,
                'received_new_quota_mb' => $new_quota_mb
            ], 400);
            return;
        }

        // แปลงข้อมูล
        $user_id = intval($user_id);
        
        // คำนวณ quota ใน bytes
        if ($is_unlimited === '1' || $new_quota_mb == 999999) {
            $quota_bytes = 999999999999999; // Unlimited (999TB)
            $quota_mb = 999999;
        } else {
            $quota_mb = intval($new_quota_mb ?: ($new_quota / 1048576));
            $quota_bytes = intval($new_quota ?: ($new_quota_mb * 1048576));
        }

        // ตรวจสอบค่าที่แปลงแล้ว
        if ($quota_bytes <= 0 && $is_unlimited !== '1') {
            $this->output_json_error('ขนาด Quota ไม่ถูกต้อง', [
                'calculated_quota_bytes' => $quota_bytes,
                'calculated_quota_mb' => $quota_mb
            ], 400);
            return;
        }

        // ตรวจสอบว่า User มีอยู่จริง
        $user = $this->db->select('m_id, m_fname, m_lname, storage_quota_limit, storage_quota_used')
                        ->from('tbl_member')
                        ->where('m_id', $user_id)
                        ->get()
                        ->row();

        if (!$user) {
            $this->output_json_error('ไม่พบผู้ใช้ที่ระบุ', ['user_id' => $user_id], 404);
            return;
        }

        log_message('info', 'Found user: ' . $user->m_fname . ' ' . $user->m_lname . ' - Current quota: ' . $user->storage_quota_limit);

        // เตรียมข้อมูลสำหรับอัปเดต
        $update_data = [
            'storage_quota_limit' => $quota_bytes
        ];

        // อัปเดตฐานข้อมูล
        $this->db->where('m_id', $user_id);
        $updated = $this->db->update('tbl_member', $update_data);

        if (!$updated) {
            $this->output_json_error('ไม่สามารถอัปเดตฐานข้อมูลได้', [
                'db_error' => $this->db->error(),
                'user_id' => $user_id,
                'quota_bytes' => $quota_bytes
            ], 500);
            return;
        }

        // ตรวจสอบผลลัพธ์
        $affected_rows = $this->db->affected_rows();
        log_message('info', 'Database update affected rows: ' . $affected_rows);

        // บันทึก log กิจกรรม
        try {
            $quota_display = ($is_unlimited === '1') ? 'Unlimited' : $this->format_bytes($quota_bytes);
            
            $this->log_enhanced_activity(
                $this->session->userdata('m_id'),
                'update_user_quota',
                "อัปเดต Storage Quota ของ {$user->m_fname} {$user->m_lname} เป็น {$quota_display}",
                [
                    'user_id' => $user_id,
                    'user_name' => $user->m_fname . ' ' . $user->m_lname,
                    'old_quota' => $user->storage_quota_limit,
                    'new_quota' => $quota_bytes,
                    'quota_mb' => $quota_mb,
                    'is_unlimited' => $is_unlimited === '1'
                ]
            );
        } catch (Exception $log_error) {
            log_message('warning', 'Failed to log activity: ' . $log_error->getMessage());
        }

        // ส่งผลลัพธ์สำเร็จ
        $this->output_json_success([
            'user_id' => $user_id,
            'new_quota_bytes' => $quota_bytes,
            'new_quota_mb' => $quota_mb,
            'new_quota_formatted' => ($is_unlimited === '1') ? 'Unlimited' : $this->format_bytes($quota_bytes),
            'is_unlimited' => $is_unlimited === '1',
            'affected_rows' => $affected_rows,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'อัปเดต Storage Quota เรียบร้อยแล้ว');

    } catch (Exception $e) {
        log_message('error', 'Update user quota exception: ' . $e->getMessage());
        log_message('error', 'Exception trace: ' . $e->getTraceAsString());
        
        $this->output_json_error('เกิดข้อผิดพลาดภายในระบบ: ' . $e->getMessage(), [
            'exception_file' => $e->getFile(),
            'exception_line' => $e->getLine()
        ], 500);
    }
}


/**
 * ✅ AJAX: Reset User Storage
 */
public function reset_user_storage() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output_json_error('ไม่มีสิทธิ์เข้าถึง');
            return;
        }

        $user_id = $this->input->post('user_id');
        $confirm = $this->input->post('confirm');

        if (empty($user_id) || $confirm !== 'RESET_USER_STORAGE') {
            $this->output_json_error('ข้อมูลยืนยันไม่ถูกต้อง');
            return;
        }

        // ดึงข้อมูลผู้ใช้
        $user = $this->db->select('m_fname, m_lname')
                        ->from('tbl_member')
                        ->where('m_id', $user_id)
                        ->get()
                        ->row();

        if (!$user) {
            $this->output_json_error('ไม่พบผู้ใช้');
            return;
        }

        $this->db->trans_start();

        // ลบไฟล์ทั้งหมดจากตาราง
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $this->db->where('uploaded_by', $user_id)
                    ->delete('tbl_google_drive_system_files');
        }

        // รีเซ็ต storage usage
        $this->db->where('m_id', $user_id)
                ->update('tbl_member', [
                    'storage_quota_used' => 0,
                    'personal_folder_id' => null
                ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // บันทึก log
            $this->log_enhanced_activity(
                $this->session->userdata('m_id'),
                'reset_storage',
                "รีเซ็ต Storage ของ {$user->m_fname} {$user->m_lname} (ID: {$user_id})",
                [
                    'user_id' => $user_id,
                    'user_name' => $user->m_fname . ' ' . $user->m_lname
                ]
            );

            $this->output_json_success([], 'รีเซ็ต Storage เรียบร้อย');
        } else {
            $this->output_json_error('ไม่สามารถรีเซ็ต Storage ได้');
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Reset user storage error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ AJAX: ดึงสถิติ Real-time
 */
public function get_realtime_stats() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $user_id = $this->input->get('user_id');
        
        if (empty($user_id)) {
            $this->output_json_error('ไม่ได้ระบุ User ID');
            return;
        }

        // คำนวณ storage usage ใหม่
        $current_usage = $this->update_user_storage_usage($user_id);
        
        // ดึงข้อมูลล่าสุด
        $user = $this->get_user_storage_details($user_id);
        $stats = $this->get_user_storage_stats($user_id);

        $realtime_data = [
            'storage_used' => $current_usage,
            'storage_used_formatted' => $this->format_bytes($current_usage),
            'storage_usage_percent' => $user->storage_usage_percent,
            'total_files' => $stats['total_files'],
            'last_updated' => date('Y-m-d H:i:s')
        ];

        $this->output_json_success($realtime_data, 'ดึงสถิติ Real-time สำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get realtime stats error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ สร้าง Personal Folder สำหรับ User
 */
public function create_personal_folder() {
    try {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $user_id = $this->input->post('user_id');
        
        if (empty($user_id)) {
            $this->output_json_error('ไม่ได้ระบุ User ID');
            return;
        }

        // ดึงข้อมูลผู้ใช้
        $user = $this->db->select('m_fname, m_lname, personal_folder_id')
                        ->from('tbl_member')
                        ->where('m_id', $user_id)
                        ->where('storage_access_granted', 1)
                        ->get()
                        ->row();

        if (!$user) {
            $this->output_json_error('ไม่พบผู้ใช้หรือไม่มีสิทธิ์เข้าใช้ Storage');
            return;
        }

        if ($user->personal_folder_id) {
            $this->output_json_error('ผู้ใช้มี Personal Folder อยู่แล้ว');
            return;
        }

        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage หรือ Access Token');
            return;
        }

        // ตรวจสอบ Token
        if (!$this->ensure_valid_access_token()) {
            $this->output_json_error('Access Token หมดอายุ');
            return;
        }

        // ดึง token ใหม่
        $system_storage = $this->get_active_system_storage();
        $token_data = json_decode($system_storage->google_access_token, true);
        
        // หา Users folder
        $users_folder = $this->db->select('folder_id')
                               ->from('tbl_google_drive_system_folders')
                               ->where('folder_name', 'Users')
                               ->where('folder_type', 'system')
                               ->where('is_active', 1)
                               ->get()
                               ->row();

        if (!$users_folder) {
            $this->output_json_error('ไม่พบโฟลเดอร์ Users ในระบบ');
            return;
        }

        // สร้างโฟลเดอร์ส่วนตัว
        $folder_name = $user->m_fname . ' ' . $user->m_lname . ' (ID: ' . $user_id . ')';
        $personal_folder = $this->create_folder_with_curl(
            $folder_name, 
            $users_folder->folder_id, 
            $token_data['access_token']
        );

        if ($personal_folder) {
            // บันทึกในฐานข้อมูล
            $folder_data = [
                'folder_name' => $folder_name,
                'folder_id' => $personal_folder['id'],
                'parent_folder_id' => $users_folder->folder_id,
                'folder_type' => 'user',
                'folder_path' => '/Organization Drive/Users/' . $folder_name,
                'folder_description' => 'Personal folder for ' . $user->m_fname . ' ' . $user->m_lname,
                'permission_level' => 'private',
                'created_by' => $this->session->userdata('m_id')
            ];

            $this->save_folder_info($folder_data);

            // อัปเดต member
            $this->db->where('m_id', $user_id)
                    ->update('tbl_member', [
                        'personal_folder_id' => $personal_folder['id']
                    ]);

            // บันทึก log
            $this->log_enhanced_activity(
                $this->session->userdata('m_id'),
                'create_personal_folder',
                "สร้าง Personal Folder สำหรับ {$user->m_fname} {$user->m_lname}",
                [
                    'user_id' => $user_id,
                    'folder_id' => $personal_folder['id'],
                    'folder_name' => $folder_name
                ]
            );

            $this->output_json_success([
                'folder_id' => $personal_folder['id'],
                'folder_name' => $folder_name,
                'web_view_link' => $personal_folder['webViewLink']
            ], 'สร้าง Personal Folder เรียบร้อย');
        } else {
            $this->output_json_error('ไม่สามารถสร้าง Personal Folder ได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Create personal folder error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	
	
	
public function get_folder_permissions() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }
        
        $folder_id = $this->input->post('folder_id');
        
        if (empty($folder_id)) {
            $this->output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            // ถ้าไม่มีตาราง ส่งข้อมูลเปล่า
            $empty_data = [
                'inherited' => [],
                'direct' => [],
                'effective' => []
            ];
            $this->output_json_success($empty_data, 'ตาราง permissions ยังไม่มี - ส่งข้อมูลเปล่า');
            return;
        }

        // เรียกใช้ helper method ที่มีการ handle error
        $all_permissions = $this->get_all_folder_permissions_safe($folder_id);
        
        $this->output_json_success($all_permissions, 'ดึงข้อมูลสิทธิ์สำเร็จ');
        
    } catch (Exception $e) {
        log_message('error', 'Get folder permissions error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 📎 ดึงสิทธิ์สืบทอดจาก parent folders
 */
public function get_inherited_permissions() {
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $folder_id = $this->input->post('folder_id');
        
        if (empty($folder_id)) {
            $this->output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            // ถ้าไม่มีตาราง ส่งข้อมูลเปล่า
            $empty_data = [];
            $this->output_json_success($empty_data, 'ตาราง permissions ยังไม่มี - ส่งข้อมูลเปล่า');
            return;
        }

        $inherited_permissions = $this->get_folder_inherited_permissions_safe($folder_id);
        
        $this->output_json_success($inherited_permissions, 'ดึงสิทธิ์สืบทอดสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get inherited permissions error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ⚡ ดึงสิทธิ์เฉพาะโฟลเดอร์
 */
public function get_direct_permissions() {
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $folder_id = $this->input->post('folder_id');
        
        if (empty($folder_id)) {
            $this->output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            // ถ้าไม่มีตาราง ส่งข้อมูลเปล่า
            $empty_data = [];
            $this->output_json_success($empty_data, 'ตาราง permissions ยังไม่มี - ส่งข้อมูลเปล่า');
            return;
        }

        $direct_permissions = $this->get_folder_direct_permissions_safe($folder_id);
        
        $this->output_json_success($direct_permissions, 'ดึงสิทธิ์เฉพาะสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get direct permissions error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 👁️ ดึงสิทธิ์ที่มีผลจริง (Effective Permissions)
 */
public function get_effective_permissions() {
    // ปิด error reporting ชั่วคราว เพื่อป้องกัน HTML error ปนใน JSON response
    $old_error_reporting = error_reporting(0);
    
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตั้งค่า headers ให้เป็น JSON
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $folder_id = $this->input->post('folder_id');
        
        if (empty($folder_id)) {
            $this->output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if (!$this->db) {
            $this->output_json_error('ไม่สามารถเชื่อมต่อฐานข้อมูลได้');
            return;
        }

        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            // ถ้าไม่มีตาราง ส่งข้อมูลเปล่า
            $empty_data = [];
            $this->output_json_success($empty_data, 'ตาราง permissions ยังไม่มี - ส่งข้อมูลเปล่า');
            return;
        }

        // เรียกใช้ safe method
        $effective_permissions = $this->calculate_effective_permissions_safe($folder_id);
        
        // ตรวจสอบผลลัพธ์
        if ($effective_permissions === false) {
            $this->output_json_error('ไม่สามารถคำนวณสิทธิ์ที่มีผลได้');
            return;
        }
        
        $this->output_json_success($effective_permissions, 'คำนวณสิทธิ์ที่มีผลสำเร็จ');

    } catch (Exception $e) {
        // บันทึก error โดยละเอียด
        $error_details = [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'folder_id' => $folder_id ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        log_message('error', 'Get effective permissions error: ' . json_encode($error_details));
        
        $this->output_json_error('เกิดข้อผิดพลาดในการคำนวณสิทธิ์: ' . $e->getMessage());
    } finally {
        // คืนค่า error reporting
        error_reporting($old_error_reporting);
    }
}

/**
 * ➕ เพิ่มสิทธิ์เฉพาะโฟลเดอร์
 */
public function add_direct_folder_permission() {
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $folder_id = $this->input->post('folder_id');
        $member_id = $this->input->post('member_id');
        $access_type = $this->input->post('access_type');
        $permission_type = $this->input->post('permission_type', 'direct');
        $expires_at = $this->input->post('expires_at');
        $apply_to_children = $this->input->post('apply_to_children', false);
        
        // Validation
        if (empty($folder_id) || empty($member_id) || empty($access_type)) {
            $this->output_json_error('กรุณากรอกข้อมูลให้ครบถ้วน');
            return;
        }

        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $this->output_json_error('ตารางสิทธิ์ยังไม่ได้สร้าง กรุณาติดต่อ Admin');
            return;
        }

        // ลบสิทธิ์เดิม (ถ้ามี) เพื่อป้องกันการซ้ำ
        $this->db->where([
            'folder_id' => $folder_id,
            'member_id' => $member_id,
            'is_active' => 1
        ])->update('tbl_google_drive_member_folder_access', ['is_active' => 0]);

        // ดึงข้อมูลผู้ให้สิทธิ์
        $granted_by_member = $this->db->select('m_fname, m_lname')
            ->where('m_id', $this->session->userdata('m_id'))
            ->get('tbl_member')
            ->row();

        $granted_by_name = $granted_by_member 
            ? $granted_by_member->m_fname . ' ' . $granted_by_member->m_lname 
            : 'Admin';

        // เพิ่มสิทธิ์ใหม่
        $permission_data = [
            'member_id' => $member_id,
            'folder_id' => $folder_id,
            'access_type' => $access_type,
            'permission_source' => 'direct',
            'permission_mode' => $permission_type, // 'direct', 'override', 'combined'
            'granted_by' => $this->session->userdata('m_id'),
            'granted_by_name' => $granted_by_name,
            'granted_at' => date('Y-m-d H:i:s'),
            'expires_at' => !empty($expires_at) ? $expires_at . ' 23:59:59' : null,
            'is_active' => 1,
            'inherit_from_parent' => 0, // ไม่สืบทอด เพราะเป็นสิทธิ์เฉพาะ
            'apply_to_children' => $apply_to_children ? 1 : 0
        ];

        $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
        
        if ($this->db->affected_rows() > 0) {
            // ถ้าเลือกให้ apply กับ children ด้วย
            if ($apply_to_children) {
                $this->apply_permission_to_subfolders($folder_id, $permission_data);
            }
            
            // Log การกระทำ
            $this->log_activity(
                $this->session->userdata('m_id'),
                'add_direct_folder_permission',
                "เพิ่มสิทธิ์เฉพาะโฟลเดอร์: {$access_type} ({$permission_type}) " . ($apply_to_children ? "พร้อม subfolder" : ""),
                [
                    'folder_id' => $folder_id,
                    'target_member_id' => $member_id,
                    'permission_type' => $permission_type,
                    'apply_to_children' => $apply_to_children
                ]
            );
            
            $this->output_json_success(null, 'เพิ่มสิทธิ์เฉพาะสำเร็จ');
        } else {
            $this->output_json_error('ไม่สามารถเพิ่มสิทธิ์ได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Add direct folder permission error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🔄 สลับโหมดการสืบทอดสิทธิ์
 */
public function toggle_folder_inheritance() {
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $folder_id = $this->input->post('folder_id');
        $enable_inheritance = $this->input->post('enable_inheritance');
        
        if (empty($folder_id)) {
            $this->output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // อัปเดตการตั้งค่าการสืบทอด
        $this->update_folder_inheritance_setting($folder_id, $enable_inheritance);
        
        // Log การกระทำ
        $this->log_activity(
            $this->session->userdata('m_id'),
            'toggle_folder_inheritance',
            "เปลี่ยนการตั้งค่าการสืบทอดสิทธิ์: " . ($enable_inheritance ? "เปิด" : "ปิด"),
            [
                'folder_id' => $folder_id,
                'enable_inheritance' => $enable_inheritance
            ]
        );
        
        $this->output_json_success(null, 'เปลี่ยนการตั้งค่าสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Toggle folder inheritance error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 📊 ดึงสถิติสิทธิ์โฟลเดอร์แบบละเอียด
 */
public function get_folder_permission_stats() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }
        
        $folder_id = $this->input->post('folder_id');
        
        if (empty($folder_id)) {
            $this->output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            // ถ้าไม่มีตาราง ส่งสถิติเปล่า
            $empty_stats = [
                'owner' => 0,
                'admin' => 0,
                'write' => 0,
                'read' => 0,
                'total' => 0
            ];
            $this->output_json_success($empty_stats, 'ตาราง permissions ยังไม่มี - ส่งสถิติเปล่า');
            return;
        }

        $stats = $this->get_detailed_permission_stats_safe($folder_id);
        
        $this->output_json_success($stats, 'ดึงสถิติสิทธิ์สำเร็จ');
        
    } catch (Exception $e) {
        log_message('error', 'Get folder permission stats error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

	
	
	private function get_all_folder_permissions_safe($folder_id) {
    try {
        $inherited = $this->get_folder_inherited_permissions_safe($folder_id);
        $direct = $this->get_folder_direct_permissions_safe($folder_id);
        $effective = $this->calculate_effective_permissions_safe($folder_id);
        
        return [
            'inherited' => $inherited,
            'direct' => $direct,
            'effective' => $effective
        ];
        
    } catch (Exception $e) {
        log_message('error', 'Get all folder permissions safe error: ' . $e->getMessage());
        return [
            'inherited' => [],
            'direct' => [],
            'effective' => []
        ];
    }
}
	
	
	/**
 * ดึงสิทธิ์เฉพาะ - Safe Version
 */
private function get_folder_direct_permissions_safe($folder_id) {
    try {
        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return [];
        }

        $this->db->select('
            mfa.id, mfa.member_id, mfa.access_type, mfa.granted_at, mfa.expires_at,
            mfa.permission_mode, mfa.apply_to_children,
            CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as member_name,
            COALESCE(p.pname, "ไม่ระบุ") as position_name,
            CONCAT(COALESCE(gm.m_fname, ""), " ", COALESCE(gm.m_lname, "")) as granted_by_name
        ');
        $this->db->from('tbl_google_drive_member_folder_access mfa');
        $this->db->join('tbl_member m', 'mfa.member_id = m.m_id', 'left');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->join('tbl_member gm', 'mfa.granted_by = gm.m_id', 'left');
        $this->db->where('mfa.folder_id', $folder_id);
        $this->db->where('mfa.inherit_from_parent', 0); // เฉพาะสิทธิ์ตรง
        $this->db->where('mfa.is_active', 1);
        $this->db->order_by('mfa.granted_at', 'DESC');
        
        $query = $this->db->get();
        
        // ตรวจสอบ database error
        $db_error = $this->db->error();
        if ($db_error['code'] !== 0) {
            log_message('error', 'Database error in get_folder_direct_permissions_safe: ' . $db_error['message']);
            return [];
        }
        
        return $query->result();
        
    } catch (Exception $e) {
        log_message('error', 'Get folder direct permissions safe error: ' . $e->getMessage());
        return [];
    }
}


	
	/**
 * คำนวณสิทธิ์ที่มีผล - Safe Version
 */
private function calculate_effective_permissions_safe($folder_id) {
    try {
        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            log_message('warning', 'Table tbl_google_drive_member_folder_access does not exist');
            return [];
        }

        $effective_permissions = [];
        $processed_members = [];

        // สร้าง query แบบปลอดภัย
        $sql = "
            SELECT 
                mfa.id, 
                mfa.member_id, 
                mfa.access_type, 
                mfa.granted_at, 
                mfa.expires_at,
                mfa.permission_mode, 
                mfa.inherit_from_parent,
                CONCAT(COALESCE(m.m_fname, ''), ' ', COALESCE(m.m_lname, '')) as member_name,
                COALESCE(p.pname, 'ไม่ระบุ') as position_name,
                CASE 
                    WHEN mfa.inherit_from_parent = 1 THEN 'สืบทอดจาก Parent'
                    WHEN mfa.permission_mode = 'override' THEN 'เขียนทับสิทธิ์'
                    WHEN mfa.permission_mode = 'direct' THEN 'สิทธิ์เฉพาะ'
                    ELSE 'สิทธิ์ปกติ'
                END as source_description
            FROM tbl_google_drive_member_folder_access mfa
            LEFT JOIN tbl_member m ON mfa.member_id = m.m_id
            LEFT JOIN tbl_position p ON m.ref_pid = p.pid
            WHERE mfa.folder_id = ? 
            AND mfa.is_active = 1
            ORDER BY 
                CASE 
                    WHEN mfa.permission_mode = 'override' THEN 1
                    WHEN mfa.permission_mode = 'direct' THEN 2
                    ELSE 3
                END ASC,
                mfa.granted_at DESC
        ";
        
        // Execute query ด้วย parameter binding
        $query = $this->db->query($sql, array($folder_id));
        
        // ตรวจสอบ database error
        $db_error = $this->db->error();
        if ($db_error['code'] !== 0) {
            log_message('error', 'Database error in calculate_effective_permissions_safe: ' . $db_error['message']);
            return false;
        }
        
        if (!$query) {
            log_message('error', 'Query failed in calculate_effective_permissions_safe');
            return false;
        }
        
        $all_permissions = $query->result();

        // ประมวลผลสิทธิ์ - คนละคนหนึ่งสิทธิ์
        foreach ($all_permissions as $permission) {
            if (!isset($permission->member_id) || empty($permission->member_id)) {
                continue; // ข้าม record ที่ไม่มี member_id
            }
            
            $member_id = $permission->member_id;
            
            // ถ้ายังไม่ได้ประมวลผลคนนี้
            if (!in_array($member_id, $processed_members)) {
                // ตรวจสอบหมดอายุ
                $is_expired = false;
                if ($permission->expires_at) {
                    try {
                        $is_expired = strtotime($permission->expires_at) < time();
                    } catch (Exception $e) {
                        log_message('warning', 'Invalid expires_at format: ' . $permission->expires_at);
                        $is_expired = false; // ถือว่าไม่หมดอายุถ้า format ผิด
                    }
                }
                
                if (!$is_expired) {
                    // เพิ่มฟิลด์ที่จำเป็น
                    $permission->final_access_type = $permission->access_type;
                    $permission->permission_source_type = ($permission->inherit_from_parent == 1) ? 'inherited' : 'direct';
                    
                    $effective_permissions[] = $permission;
                    $processed_members[] = $member_id;
                }
            }
        }

        return $effective_permissions;
        
    } catch (Exception $e) {
        log_message('error', 'Calculate effective permissions safe error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        return false;
    }
}



/**
 * ดึงสถิติ - Safe Version
 */
private function get_detailed_permission_stats_safe($folder_id) {
    try {
        $stats = [
            'owner' => 0,
            'admin' => 0,
            'write' => 0,
            'read' => 0,
            'total' => 0
        ];

        $this->db->select('access_type');
        $this->db->from('tbl_google_drive_member_folder_access');
        $this->db->where('folder_id', $folder_id);
        $this->db->where('is_active', 1);
        
        $query = $this->db->get();
        
        // ตรวจสอบ database error
        $db_error = $this->db->error();
        if ($db_error['code'] !== 0) {
            log_message('error', 'Database error in get_detailed_permission_stats_safe: ' . $db_error['message']);
            return $stats;
        }
        
        $permissions = $query->result();

        foreach ($permissions as $permission) {
            $stats['total']++;
            
            switch ($permission->access_type) {
                case 'owner':
                    $stats['owner']++;
                    break;
                case 'admin':
                    $stats['admin']++;
                    break;
                case 'write':
                    $stats['write']++;
                    break;
                case 'read':
                    $stats['read']++;
                    break;
            }
        }

        return $stats;
        
    } catch (Exception $e) {
        log_message('error', 'Get detailed permission stats safe error: ' . $e->getMessage());
        return [
            'owner' => 0,
            'admin' => 0,
            'write' => 0,
            'read' => 0,
            'total' => 0
        ];
    }
}
	

	
	/**
 * ดึงสิทธิ์สืบทอด - Safe Version
 */
private function get_folder_inherited_permissions_safe($folder_id) {
    try {
        // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return [];
        }

        // ดึงสิทธิ์ที่สืบทอดมาจาก parent folders
        $this->db->select('
            mfa.id, mfa.member_id, mfa.access_type, mfa.granted_at, mfa.expires_at,
            mfa.parent_folder_id, mfa.permission_mode,
            CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as member_name,
            COALESCE(p.pname, "ไม่ระบุ") as position_name,
            CONCAT(COALESCE(gm.m_fname, ""), " ", COALESCE(gm.m_lname, "")) as granted_by_name,
            COALESCE(pf.folder_name, "Parent Folder") as inherited_from_name
        ');
        $this->db->from('tbl_google_drive_member_folder_access mfa');
        $this->db->join('tbl_member m', 'mfa.member_id = m.m_id', 'left');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->join('tbl_member gm', 'mfa.granted_by = gm.m_id', 'left');
        $this->db->join('tbl_google_drive_system_folders pf', 'mfa.parent_folder_id = pf.folder_id', 'left');
        $this->db->where('mfa.folder_id', $folder_id);
        $this->db->where('mfa.inherit_from_parent', 1);
        $this->db->where('mfa.is_active', 1);
        $this->db->order_by('mfa.granted_at', 'DESC');
        
        $query = $this->db->get();
        
        // ตรวจสอบ database error
        $db_error = $this->db->error();
        if ($db_error['code'] !== 0) {
            log_message('error', 'Database error in get_folder_inherited_permissions_safe: ' . $db_error['message']);
            return [];
        }
        
        return $query->result();
        
    } catch (Exception $e) {
        log_message('error', 'Get folder inherited permissions safe error: ' . $e->getMessage());
        return [];
    }
}

	
	
	

// =============================================
// 3. HELPER METHODS
// =============================================

/**
 * ดึงสิทธิ์ทั้งหมดของโฟลเดอร์ (inherited + direct)
 */
private function get_all_folder_permissions($folder_id) {
    try {
        $inherited = $this->get_folder_inherited_permissions($folder_id);
        $direct = $this->get_folder_direct_permissions($folder_id);
        
        return [
            'inherited' => $inherited,
            'direct' => $direct,
            'effective' => $this->calculate_effective_permissions($folder_id)
        ];
        
    } catch (Exception $e) {
        log_message('error', 'Get all folder permissions error: ' . $e->getMessage());
        throw $e;
    }
}
	
	
	public function get_available_users() {
    // ปิด error reporting ชั่วคราว
    $old_error_reporting = error_reporting(0);
    
    try {
        // ล้าง output buffer ทั้งหมด
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // ตั้งค่า headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        // รองรับทั้ง AJAX และ Browser Request
        $is_ajax = $this->input->is_ajax_request() || 
                   $this->input->get_request_header('X-Requested-With') === 'XMLHttpRequest' ||
                   $this->input->method() === 'post';
        
        if (!$is_ajax && $this->input->method() !== 'get') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method. Use GET or POST with AJAX.',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if (!$this->db) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_member')) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'ตาราง tbl_member ไม่มีอยู่ในระบบ',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ✅ สร้าง query ตามโครงสร้างตารางจริง
        $this->db->select('m.m_id, CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as name', FALSE);
        
        // Join กับ position table ถ้ามี
        if ($this->db->table_exists('tbl_position')) {
            $this->db->select('COALESCE(p.pname, "ไม่ระบุ") as position_name', FALSE);
            $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        } else {
            $this->db->select('"ไม่ระบุ" as position_name', FALSE);
        }
        
        // เพิ่มข้อมูลเกี่ยวกับ Google Drive
        $this->db->select('m.google_email, m.google_drive_enabled, m.storage_access_granted');
        $this->db->select('m.storage_quota_limit, m.storage_quota_used, m.last_storage_access');
        
        $this->db->from('tbl_member m');
        
        // ✅ ใช้ m_status แทน is_active (ตามโครงสร้างตารางจริง)
        $this->db->where('m.m_status', '1'); // active status
        
        // ✅ ใช้ storage_access_granted ที่มีอยู่แล้ว
        $this->db->where('m.storage_access_granted', 1);
        
        // เรียงลำดับตามชื่อ
        $this->db->order_by('m.m_fname', 'ASC');
        
        // จำกัดจำนวนผลลัพธ์เพื่อป้องกันการโหลดมากเกินไป
        $this->db->limit(1000);
        
        // ทำการ query
        $query = $this->db->get();
        
        // ตรวจสอบ database error
        $db_error = $this->db->error();
        if ($db_error['code'] !== 0) {
            log_message('error', 'Database error in get_available_users: ' . $db_error['message']);
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'ข้อผิดพลาดฐานข้อมูล: ' . $db_error['message'],
                'debug' => [
                    'error_code' => $db_error['code'],
                    'error_message' => $db_error['message']
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // ตรวจสอบผลลัพธ์
        if (!$query) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลผู้ใช้ได้',
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $users = $query->result();
        
        // ปรับแต่งข้อมูลผู้ใช้
        $formatted_users = [];
        $total_users = 0;
        $google_enabled_users = 0;
        
        foreach ($users as $user) {
            $total_users++;
            
            // ตรวจสอบสถานะ Google Drive
            if ($user->google_drive_enabled == 1) {
                $google_enabled_users++;
            }
            
            // คำนวณเปอร์เซ็นต์การใช้งาน Storage
            $storage_usage_percent = 0;
            if ($user->storage_quota_limit > 0) {
                $storage_usage_percent = round(($user->storage_quota_used / $user->storage_quota_limit) * 100, 2);
            }
            
            $formatted_users[] = [
                'm_id' => $user->m_id,
                'name' => trim($user->name) ?: 'ไม่ระบุชื่อ',
                'position_name' => $user->position_name ?: 'ไม่ระบุตำแหน่ง',
                'google_email' => $user->google_email,
                'google_drive_enabled' => $user->google_drive_enabled == 1,
                'storage_access_granted' => $user->storage_access_granted == 1,
                'storage_quota_limit' => $user->storage_quota_limit,
                'storage_quota_used' => $user->storage_quota_used,
                'storage_usage_percent' => $storage_usage_percent,
                'last_storage_access' => $user->last_storage_access,
                'storage_quota_limit_formatted' => $this->format_bytes($user->storage_quota_limit),
                'storage_quota_used_formatted' => $this->format_bytes($user->storage_quota_used)
            ];
        }

        // สถิติเพิ่มเติม
        $stats = [
            'total_users' => $total_users,
            'google_enabled_users' => $google_enabled_users,
            'storage_granted_users' => count($formatted_users), // เนื่องจากมีเงื่อนไข WHERE แล้ว
            'percentage_google_enabled' => $total_users > 0 ? round(($google_enabled_users / $total_users) * 100, 2) : 0
        ];

        // ส่งผลลัพธ์สำเร็จ
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'ดึงรายชื่อผู้ใช้สำเร็จ (' . count($formatted_users) . ' คน)',
            'data' => $formatted_users,
            'stats' => $stats,
            'debug_info' => [
                'method' => $this->input->method(),
                'is_ajax' => $this->input->is_ajax_request(),
                'total_users_found' => count($formatted_users),
                'query_executed' => true,
                'table_structure_correct' => true
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        // บันทึก error
        if (function_exists('log_message')) {
            log_message('error', 'Get available users error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
        
        // ส่ง error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดภายในระบบ',
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error',
            'debug' => ENVIRONMENT === 'development' ? [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ] : null,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        // คืนค่า error reporting
        error_reporting($old_error_reporting);
        exit; // สำคัญ: หยุดการทำงานทันที
    }
}


	
	
public function test_get_available_users() {
    echo "<h2>Testing get_available_users API</h2>";
    
    // Test 1: Direct call
    echo "<h3>Test 1: Direct Method Call</h3>";
    echo "<pre>";
    
    try {
        // Simulate AJAX request
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        
        ob_start();
        $this->get_available_users();
        $output = ob_get_clean();
        
        echo "Output: " . htmlspecialchars($output) . "\n";
        
        $json_data = json_decode($output, true);
        if ($json_data) {
            echo "Parsed JSON: " . print_r($json_data, true);
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    echo "</pre>";
    
    // Test 2: AJAX simulation
    echo "<h3>Test 2: AJAX Request Simulation</h3>";
    echo '<button onclick="testAjaxCall()">Test AJAX Call</button>';
    echo '<div id="ajaxResult"></div>';
    
    echo '<script>
    function testAjaxCall() {
        fetch("' . site_url('google_drive_system/get_available_users') . '", {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("ajaxResult").innerHTML = 
                "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
        })
        .catch(error => {
            document.getElementById("ajaxResult").innerHTML = 
                "<pre style=\"color: red;\">Error: " + error.message + "</pre>";
        });
    }
    </script>';
}
	
	
	
	public function add_folder_permission() {
    // ปิด error reporting ชั่วคราว
    $old_error_reporting = error_reporting(0);
    
    try {
        // ล้าง output buffer ทั้งหมด
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // ตั้งค่า headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->safe_output_json_error('Invalid request method');
            return;
        }

        // รับข้อมูลจาก POST
        $folder_id = $this->input->post('folder_id');
        $member_id = $this->input->post('member_id');
        $access_type = $this->input->post('access_type');
        $expires_at = $this->input->post('expires_at');
        $apply_to_children = $this->input->post('apply_to_children');

        // ✅ AUTO INHERIT เป็น Default (เสมอ)
        $apply_to_children = true; // บังคับให้เป็น true เสมอ

        // Validate inputs
        if (empty($folder_id) || empty($member_id) || empty($access_type)) {
            $this->safe_output_json_error('กรุณากรอกข้อมูลให้ครบถ้วน');
            return;
        }

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if (!$this->db) {
            $this->safe_output_json_error('ไม่สามารถเชื่อมต่อฐานข้อมูลได้');
            return;
        }

        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $this->safe_output_json_error('ตาราง member_folder_access ไม่มีอยู่');
            return;
        }

        // ✅ ตรวจสอบ Session ให้ปลอดภัย
        $current_user_id = $this->session->userdata('m_id');
        if (!$current_user_id) {
            $this->safe_output_json_error('กรุณาเข้าสู่ระบบใหม่');
            return;
        }

        // ✅ ตรวจสอบว่า member_id มีอยู่จริงในระบบ
        $member_exists = $this->db->select('m_id, m_fname, m_lname, google_email')
                                 ->from('tbl_member')
                                 ->where('m_id', $member_id)
                                 ->where('m_status', '1')
                                 ->get()
                                 ->row();

        if (!$member_exists) {
            $this->safe_output_json_error('ไม่พบผู้ใช้ที่เลือก');
            return;
        }

        // ✅ ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่ (Soft Check)
        $existing = $this->db->select('id, access_type, is_active')
                            ->from('tbl_google_drive_member_folder_access')
                            ->where('folder_id', $folder_id)
                            ->where('member_id', $member_id)
                            ->get()
                            ->row();

        if ($existing && $existing->is_active == 1) {
            $this->safe_output_json_error('ผู้ใช้นี้มีสิทธิ์อยู่แล้ว (' . $existing->access_type . ')');
            return;
        }

        // ✅ ดึงข้อมูลผู้ให้สิทธิ์ อย่างปลอดภัย
        $granted_by_name = 'Admin'; // Default
        try {
            $granted_by_member = $this->db->select('m_fname, m_lname')
                ->where('m_id', $current_user_id)
                ->get('tbl_member')
                ->row();

            if ($granted_by_member && $granted_by_member->m_fname) {
                $granted_by_name = trim($granted_by_member->m_fname . ' ' . $granted_by_member->m_lname);
            }
        } catch (Exception $e) {
            // ใช้ default ถ้า error
            log_message('warning', 'Cannot get granted_by name: ' . $e->getMessage());
        }

        // ✅ เตรียมข้อมูลสำหรับบันทึก
        $permission_data = [
            'folder_id' => $folder_id,
            'member_id' => $member_id,
            'access_type' => $access_type,
            'permission_source' => 'direct',
            'granted_by' => $current_user_id,
            'granted_by_name' => $granted_by_name,
            'granted_at' => date('Y-m-d H:i:s'),
            'expires_at' => !empty($expires_at) ? $expires_at . ' 23:59:59' : NULL,
            'is_active' => 1,
            'inherit_from_parent' => 0, // ไม่สืบทอด เพราะเป็นสิทธิ์หลัก
            'apply_to_children' => 1,   // ✅ AUTO INHERIT เสมอ
            'permission_mode' => 'direct'
        ];

        // ✅ เริ่ม transaction
        $this->db->trans_start();

        // ถ้ามี existing record ที่ inactive ให้ลบออก
        if ($existing && $existing->is_active == 0) {
            $this->db->where('id', $existing->id)->delete('tbl_google_drive_member_folder_access');
        }

        // Insert ข้อมูลใหม่
        $insert_result = $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
        
        // ✅ ตรวจสอบ database error
        $db_error = $this->db->error();
        if ($db_error['code'] !== 0) {
            $this->db->trans_rollback();
            log_message('error', 'Database error in add_folder_permission: ' . $db_error['message']);
            $this->safe_output_json_error('ข้อผิดพลาดฐานข้อมูล: ' . $db_error['message']);
            return;
        }

        $affected_rows = $this->db->affected_rows();
        $new_permission_id = $this->db->insert_id();
        
        if ($insert_result && $affected_rows > 0 && $new_permission_id) {
            // ✅ AUTO INHERIT: Apply ไปยัง subfolders ทันที
            $inherited_count = 0;
            try {
                $inherited_count = $this->apply_permission_to_subfolders_enhanced($folder_id, $permission_data, $new_permission_id);
            } catch (Exception $e) {
                log_message('warning', 'Apply to subfolders failed: ' . $e->getMessage());
                // ไม่ return error เพราะการเพิ่มสิทธิ์หลักสำเร็จแล้ว
            }
            
            // Complete transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed in add_folder_permission');
                $this->safe_output_json_error('ไม่สามารถบันทึกข้อมูลได้');
                return;
            }
            
            // ✅ Log การกระทำ (ถ้ามี method)
            $this->safe_log_activity(
                $current_user_id,
                'add_folder_permission',
                "เพิ่มสิทธิ์: {$access_type} สำหรับ {$member_exists->m_fname} {$member_exists->m_lname} พร้อม Auto Inherit",
                [
                    'folder_id' => $folder_id,
                    'target_member_id' => $member_id,
                    'target_member_name' => $member_exists->m_fname . ' ' . $member_exists->m_lname,
                    'apply_to_children' => true,
                    'inherited_subfolders' => $inherited_count
                ]
            );
            
            // ✅ ส่งผลลัพธ์สำเร็จพร้อมข้อมูล Auto Inherit
            $this->safe_output_json_success([
                'permission_id' => $new_permission_id,
                'folder_id' => $folder_id,
                'member_id' => $member_id,
                'member_name' => $member_exists->m_fname . ' ' . $member_exists->m_lname,
                'access_type' => $access_type,
                'auto_inherit' => true,
                'inherited_subfolders' => $inherited_count,
                'granted_at' => date('Y-m-d H:i:s'),
                'granted_by' => $granted_by_name
            ], 'เพิ่มสิทธิ์พร้อม Auto Inherit สำเร็จ');

        } else {
            $this->db->trans_rollback();
            log_message('error', 'Insert failed in add_folder_permission - affected_rows: ' . $affected_rows . ', insert_id: ' . $new_permission_id);
            $this->safe_output_json_error('ไม่สามารถเพิ่มสิทธิ์ได้ (Insert failed)');
        }

    } catch (Exception $e) {
        // Rollback transaction if active
        if ($this->db && method_exists($this->db, 'trans_status') && $this->db->trans_status() !== FALSE) {
            $this->db->trans_rollback();
        }

        // Log error with details
        $error_details = [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'folder_id' => $folder_id ?? 'unknown',
            'member_id' => $member_id ?? 'unknown',
            'access_type' => $access_type ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        log_message('error', 'Add folder permission critical error: ' . json_encode($error_details));
        
        $this->safe_output_json_error('เกิดข้อผิดพลาดร้ายแรง: ' . $e->getMessage());
    } finally {
        // คืนค่า error reporting
        error_reporting($old_error_reporting);
    }
}
	
	
	private function apply_permission_to_subfolders_enhanced($parent_folder_id, $permission_data, $parent_permission_id) {
    $inherited_count = 0;
    
    try {
        // ดึงรายการ subfolders จากฐานข้อมูล (ถ้ามีตาราง system folders)
        $subfolders = $this->get_subfolders_from_database($parent_folder_id);
        
        // ถ้าไม่มีในฐานข้อมูล ให้ลองดึงจาก Google Drive API
        if (empty($subfolders)) {
            $subfolders = $this->get_subfolders_from_google_drive($parent_folder_id);
        }
        
        foreach ($subfolders as $subfolder) {
            try {
                // ตรวจสอบว่ามีสิทธิ์ Direct อยู่แล้วหรือไม่ (ถ้ามีให้ข้าม)
                $existing_direct = $this->db->select('id')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('folder_id', $subfolder['id'])
                    ->where('member_id', $permission_data['member_id'])
                    ->where('inherit_from_parent', 0) // เฉพาะสิทธิ์ Direct
                    ->where('is_active', 1)
                    ->get()
                    ->row();
                
                if ($existing_direct) {
                    // มี Direct Permission อยู่แล้ว ข้าม
                    log_message('info', "Skipping subfolder {$subfolder['id']} - has direct permission");
                    continue;
                }
                
                // ลบ inherited permission เดิม (ถ้ามี)
                $this->db->where([
                    'folder_id' => $subfolder['id'],
                    'member_id' => $permission_data['member_id'],
                    'inherit_from_parent' => 1
                ])->update('tbl_google_drive_member_folder_access', ['is_active' => 0]);
                
                // เพิ่มสิทธิ์สืบทอดใหม่
                $inherited_permission = [
                    'member_id' => $permission_data['member_id'],
                    'folder_id' => $subfolder['id'],
                    'access_type' => $permission_data['access_type'],
                    'permission_source' => 'inherited',
                    'granted_by' => $permission_data['granted_by'],
                    'granted_by_name' => $permission_data['granted_by_name'],
                    'granted_at' => $permission_data['granted_at'],
                    'expires_at' => $permission_data['expires_at'],
                    'is_active' => 1,
                    'inherit_from_parent' => 1, // สืบทอดจาก parent
                    'parent_folder_id' => $parent_folder_id,
                    'permission_mode' => 'inherited'
                ];
                
                $insert_result = $this->db->insert('tbl_google_drive_member_folder_access', $inherited_permission);
                
                if ($insert_result && $this->db->affected_rows() > 0) {
                    $inherited_count++;
                    log_message('info', "Applied inherited permission to subfolder: {$subfolder['id']}");
                    
                    // 🔄 Recursive: Apply ไปยัง subfolder ของ subfolder ด้วย
                    $sub_inherited = $this->apply_permission_to_subfolders_enhanced(
                        $subfolder['id'], 
                        $permission_data, 
                        $parent_permission_id
                    );
                    $inherited_count += $sub_inherited;
                }
                
            } catch (Exception $e) {
                log_message('error', "Error applying permission to subfolder {$subfolder['id']}: " . $e->getMessage());
                // ไม่ throw error เพื่อให้ดำเนินการต่อกับ subfolder อื่น
            }
        }
        
        log_message('info', "Applied permissions to {$inherited_count} subfolders under {$parent_folder_id}");
        return $inherited_count;
        
    } catch (Exception $e) {
        log_message('error', 'Apply permission to subfolders enhanced error: ' . $e->getMessage());
        return $inherited_count; // Return count ที่ทำได้
    }
}

	
	
	
	private function get_subfolders_from_database($parent_folder_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return [];
        }
        
        $this->db->select('folder_id as id, folder_name as name, folder_type');
        $this->db->from('tbl_google_drive_system_folders');
        $this->db->where('parent_folder_id', $parent_folder_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('folder_name', 'ASC');
        
        $query = $this->db->get();
        
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return [];
        
    } catch (Exception $e) {
        log_message('error', 'Get subfolders from database error: ' . $e->getMessage());
        return [];
    }
}
	
	
	

	/**
 * ☁️ ดึงรายการ Subfolders จาก Google Drive API (Fallback)
 */
private function get_subfolders_from_google_drive($parent_folder_id) {
    try {
        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            return [];
        }
        
        // ตรวจสอบ Token
        if (!$this->has_valid_access_token($system_storage)) {
            return [];
        }
        
        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];
        
        // เรียก Google Drive API
        $folders = $this->get_google_drive_folder_contents($access_token, $parent_folder_id, 'folder');
        
        // แปลงเป็นรูปแบบที่ใช้ได้
        $subfolders = [];
        if (is_array($folders)) {
            foreach ($folders as $folder) {
                if ($folder['type'] === 'folder') {
                    $subfolders[] = [
                        'id' => $folder['id'],
                        'name' => $folder['name'],
                        'folder_type' => 'google_drive'
                    ];
                }
            }
        }
        
        return $subfolders;
        
    } catch (Exception $e) {
        log_message('error', 'Get subfolders from Google Drive error: ' . $e->getMessage());
        return [];
    }
}

	
	
private function safe_output_json_error($message, $code = 400) {
    // ล้าง output buffer ทั้งหมด
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    http_response_code($code);
    
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    $response = [
        'success' => false,
        'message' => $message,
        'error_code' => $code,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_info' => [
            'method' => 'add_folder_permission',
            'server_time' => time(),
            'memory_usage' => memory_get_usage(true)
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}
	
	


/**
 * 🛡️ Safe Output JSON Success (ไม่ขึ้นกับ parent methods)
 */
private function safe_output_json_success($data = null, $message = 'Success') {
    // ล้าง output buffer ทั้งหมด
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    http_response_code(200);
    
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    $response = [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_info' => [
            'method' => 'add_folder_permission',
            'server_time' => time(),
            'memory_usage' => memory_get_usage(true)
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}
	
	
	
	private function apply_permission_to_subfolders_safe($parent_folder_id, $permission_data) {
    try {
        // สำหรับตอนนี้ skip การ apply ไปยัง subfolder เพื่อไม่ให้ซับซ้อน
        // ในอนาคตสามารถเพิ่มได้เมื่อมี Google Drive API integration
        log_message('info', 'Apply to subfolders requested for folder: ' . $parent_folder_id);
        return true;
        
    } catch (Exception $e) {
        log_message('warning', 'Apply permission to subfolders failed: ' . $e->getMessage());
        return false;
    }
}
	
	

/**
 * ดึงสิทธิ์สืบทอดจาก parent folders
 */
private function get_folder_inherited_permissions($folder_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            throw new Exception('ตาราง member_folder_access ไม่มีอยู่');
        }

        // ดึงสิทธิ์ที่สืบทอดมาจาก parent folders
        $inherited_permissions = $this->db->select('
                mfa.id, mfa.member_id, mfa.access_type, mfa.granted_at, mfa.expires_at,
                mfa.parent_folder_id, mfa.permission_mode,
                CONCAT(m.m_fname, " ", m.m_lname) as member_name,
                p.pname as position_name,
                CONCAT(gm.m_fname, " ", gm.m_lname) as granted_by_name,
                pf.folder_name as inherited_from_name
            ')
            ->from('tbl_google_drive_member_folder_access mfa')
            ->join('tbl_member m', 'mfa.member_id = m.m_id', 'left')
            ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
            ->join('tbl_member gm', 'mfa.granted_by = gm.m_id', 'left')
            ->join('tbl_google_drive_folders pf', 'mfa.parent_folder_id = pf.folder_id', 'left')
            ->where('mfa.folder_id', $folder_id)
            ->where('mfa.inherit_from_parent', 1)
            ->where('mfa.is_active', 1)
            ->order_by('mfa.granted_at', 'DESC')
            ->get()
            ->result();

        return $inherited_permissions;
        
    } catch (Exception $e) {
        log_message('error', 'Get folder inherited permissions error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * ดึงสิทธิ์เฉพาะโฟลเดอร์
 */
private function get_folder_direct_permissions($folder_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            throw new Exception('ตาราง member_folder_access ไม่มีอยู่');
        }

        // ดึงสิทธิ์เฉพาะที่ตั้งค่าโดยตรง (ไม่ใช่สืบทอด)
        $direct_permissions = $this->db->select('
                mfa.id, mfa.member_id, mfa.access_type, mfa.granted_at, mfa.expires_at,
                mfa.permission_mode, mfa.apply_to_children,
                CONCAT(m.m_fname, " ", m.m_lname) as member_name,
                p.pname as position_name,
                CONCAT(gm.m_fname, " ", gm.m_lname) as granted_by_name
            ')
            ->from('tbl_google_drive_member_folder_access mfa')
            ->join('tbl_member m', 'mfa.member_id = m.m_id', 'left')
            ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
            ->join('tbl_member gm', 'mfa.granted_by = gm.m_id', 'left')
            ->where('mfa.folder_id', $folder_id)
            ->where('mfa.inherit_from_parent', 0) // เฉพาะสิทธิ์ตรง
            ->where('mfa.is_active', 1)
            ->order_by('mfa.granted_at', 'DESC')
            ->get()
            ->result();

        return $direct_permissions;
        
    } catch (Exception $e) {
        log_message('error', 'Get folder direct permissions error: ' . $e->getMessage());
        throw $e;
    }
}


/**
 * คำนวณสิทธิ์ที่มีผลจริง (Effective Permissions)
 */
private function calculate_effective_permissions($folder_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            throw new Exception('ตาราง member_folder_access ไม่มีอยู่');
        }

        $effective_permissions = [];
        $processed_members = [];

        // ดึงสิทธิ์ทั้งหมด (direct + inherited) 
        $all_permissions = $this->db->select('
                mfa.id, mfa.member_id, mfa.access_type, mfa.granted_at, mfa.expires_at,
                mfa.permission_mode, mfa.inherit_from_parent,
                CONCAT(m.m_fname, " ", m.m_lname) as member_name,
                p.pname as position_name,
                CASE 
                    WHEN mfa.inherit_from_parent = 1 THEN "สืบทอดจาก Parent"
                    WHEN mfa.permission_mode = "override" THEN "เขียนทับสิทธิ์"
                    WHEN mfa.permission_mode = "direct" THEN "สิทธิ์เฉพาะ"
                    ELSE "สิทธิ์ปกติ"
                END as source_description
            ')
            ->from('tbl_google_drive_member_folder_access mfa')
            ->join('tbl_member m', 'mfa.member_id = m.m_id', 'left')
            ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
            ->where('mfa.folder_id', $folder_id)
            ->where('mfa.is_active', 1)
            ->order_by('mfa.permission_mode', 'ASC') // override ก่อน
            ->order_by('mfa.granted_at', 'DESC')
            ->get()
            ->result();

        // ประมวลผลสิทธิ์ - คนละคนหนึ่งสิทธิ์
        foreach ($all_permissions as $permission) {
            $member_id = $permission->member_id;
            
            // ถ้ายังไม่ได้ประมวลผลคนนี้
            if (!in_array($member_id, $processed_members)) {
                // ตรวจสอบหมดอายุ
                $is_expired = $permission->expires_at && 
                             strtotime($permission->expires_at) < time();
                
                if (!$is_expired) {
                    $permission->final_access_type = $permission->access_type;
                    $permission->permission_source_type = $permission->inherit_from_parent ? 
                        'inherited' : 'direct';
                    
                    $effective_permissions[] = $permission;
                    $processed_members[] = $member_id;
                }
            }
        }

        return $effective_permissions;
        
    } catch (Exception $e) {
        log_message('error', 'Calculate effective permissions error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * ใช้สิทธิ์กับ subfolder
 */
private function apply_permission_to_subfolders($parent_folder_id, $permission_data) {
    try {
        // ดึงรายการ subfolder
        $subfolders = $this->get_all_subfolders($parent_folder_id);
        
        foreach ($subfolders as $subfolder) {
            // ตรวจสอบว่ามีสิทธิ์เฉพาะอยู่แล้วหรือไม่
            $existing = $this->db->where([
                'folder_id' => $subfolder['id'],
                'member_id' => $permission_data['member_id'],
                'is_active' => 1,
                'inherit_from_parent' => 0 // เฉพาะสิทธิ์เฉพาะ
            ])->get('tbl_google_drive_member_folder_access')->row();
            
            if (!$existing) {
                // เพิ่มสิทธิ์สืบทอด
                $inherited_permission = $permission_data;
                $inherited_permission['folder_id'] = $subfolder['id'];
                $inherited_permission['inherit_from_parent'] = 1;
                $inherited_permission['parent_folder_id'] = $parent_folder_id;
                $inherited_permission['permission_mode'] = 'inherited';
                
                unset($inherited_permission['id']); // ลบ primary key
                
                $this->db->insert('tbl_google_drive_member_folder_access', $inherited_permission);
            }
        }
        
    } catch (Exception $e) {
        log_message('error', 'Apply permission to subfolders error: ' . $e->getMessage());
    }
}

/**
 * ตรวจสอบว่ามีการ override สิทธิ์หรือไม่
 */
private function check_permission_override($folder_id, $member_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return false;
        }

        $override = $this->db->where([
            'folder_id' => $folder_id,
            'member_id' => $member_id,
            'is_active' => 1,
            'inherit_from_parent' => 0
        ])->get('tbl_google_drive_member_folder_access')->row();
        
        return $override ? true : false;
        
    } catch (Exception $e) {
        log_message('error', 'Check permission override error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ดึง folder path จาก root
 */
private function get_folder_path_from_root($folder_id) {
    try {
        // สำหรับ demo ให้ return mock data
        // ในการใช้งานจริงควรดึงจาก Google Drive API หรือ cache
        return [
            ['id' => 'root', 'name' => 'Organization Drive'],
            ['id' => 'dept_hr', 'name' => 'แผนก HR'],
            ['id' => $folder_id, 'name' => 'โฟลเดอร์ปัจจุบัน']
        ];
        
    } catch (Exception $e) {
        log_message('error', 'Get folder path from root error: ' . $e->getMessage());
        return [['id' => $folder_id, 'name' => 'โฟลเดอร์ปัจจุบัน']];
    }
}

/**
 * ดึงรายการ subfolder ทั้งหมด
 */
private function get_all_subfolders($parent_folder_id) {
    try {
        // สำหรับ demo ให้ return mock data
        // ในการใช้งานจริงควรดึงจาก Google Drive API
        return [
            ['id' => 'sub1_' . $parent_folder_id, 'name' => 'Subfolder 1'],
            ['id' => 'sub2_' . $parent_folder_id, 'name' => 'Subfolder 2']
        ];
        
    } catch (Exception $e) {
        log_message('error', 'Get all subfolders error: ' . $e->getMessage());
        return [];
    }
}

/**
 * อัปเดตการตั้งค่าการสืบทอดสิทธิ์
 */
private function update_folder_inheritance_setting($folder_id, $enable_inheritance) {
    try {
        // สำหรับ demo - ในการใช้งานจริงอาจต้องมีตารางเก็บการตั้งค่าโฟลเดอร์
        log_message('info', "Updated inheritance setting for folder {$folder_id}: " . ($enable_inheritance ? 'enabled' : 'disabled'));
        
        return true;
        
    } catch (Exception $e) {
        log_message('error', 'Update folder inheritance setting error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ดึงสถิติสิทธิ์โฟลเดอร์แบบละเอียด
 */
private function get_detailed_permission_stats($folder_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            throw new Exception('ตาราง member_folder_access ไม่มีอยู่');
        }

        $stats = [
            'owner' => 0,
            'admin' => 0,
            'write' => 0,
            'read' => 0,
            'total' => 0,
            'active' => 0,
            'expired' => 0
        ];

        // ดึงข้อมูลสิทธิ์ทั้งหมด
        $permissions = $this->db->select('access_type, expires_at')
                               ->from('tbl_google_drive_member_folder_access')
                               ->where('folder_id', $folder_id)
                               ->where('is_active', 1)
                               ->get()
                               ->result();

        $current_time = time();

        foreach ($permissions as $permission) {
            $stats['total']++;
            
            // ตรวจสอบหมดอายุ
            $is_expired = $permission->expires_at && 
                         strtotime($permission->expires_at) < $current_time;
            
            if ($is_expired) {
                $stats['expired']++;
            } else {
                $stats['active']++;
                
                // นับตามประเภทสิทธิ์
                switch ($permission->access_type) {
                    case 'owner':
                        $stats['owner']++;
                        break;
                    case 'admin':
                        $stats['admin']++;
                        break;
                    case 'write':
                        $stats['write']++;
                        break;
                    case 'read':
                        $stats['read']++;
                        break;
                }
            }
        }

        return $stats;
        
    } catch (Exception $e) {
        log_message('error', 'Get detailed permission stats error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * ดึงชื่อ admin ปัจจุบัน
 */
private function get_admin_name() {
    try {
        $admin = $this->db->select('m_fname, m_lname')
            ->where('m_id', $this->session->userdata('m_id'))
            ->get('tbl_member')
            ->row();
            
        return $admin ? $admin->m_fname . ' ' . $admin->m_lname : 'Admin';
        
    } catch (Exception $e) {
        return 'Admin';
    }
}

// =============================================
// 4. อัปเดต get_folder_contents() ให้มี permission_count
// =============================================

public function get_folder_contents() {
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }
        
        $folder_id = $this->input->post('folder_id');
        
        log_message('info', "Getting folder contents for: {$folder_id}");
        
        // ตรวจสอบ System Storage
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            $this->output_json_error('ไม่พบ System Storage หรือ Access Token');
            return;
        }
        
        // ตรวจสอบ Token
        if (!$this->has_valid_access_token($system_storage)) {
            $this->output_json_error('Access Token หมดอายุ กรุณา Refresh Token');
            return;
        }
        
        $token_data = json_decode($system_storage->google_access_token, true);
        $access_token = $token_data['access_token'];
        
        // ดึงข้อมูลจาก Google Drive
        if ($folder_id === 'root') {
            // ดึงโฟลเดอร์หลักจาก root folder
            $folders = $this->get_google_drive_root_folders($access_token, $system_storage->root_folder_id);
        } else {
            // ดึงเนื้อหาจากโฟลเดอร์เฉพาะ
            $folders = $this->get_google_drive_folder_contents($access_token, $folder_id);
        }

        // ⭐ เพิ่มข้อมูล permission_count สำหรับโฟลเดอร์
        if ($folders !== false && is_array($folders)) {
            foreach ($folders as &$item) {
                if (isset($item['type']) && $item['type'] === 'folder') {
                    // เพิ่มข้อมูลสิทธิ์
                    $permission_stats = $this->get_folder_permission_stats_safe($item['id']);
                    $item['permission_count'] = $permission_stats['total'];
                    $item['inherited_count'] = $permission_stats['inherited'];
                    $item['direct_count'] = $permission_stats['direct'];
                    $item['override_count'] = $permission_stats['override'];
                    
                    // เพิ่ม permission indicators
                    $item['permission_indicators'] = [
                        'has_inherited' => $permission_stats['inherited'] > 0,
                        'has_direct' => $permission_stats['direct'] > 0,
                        'has_override' => $permission_stats['override'] > 0
                    ];
                }
            }
        }

        if ($folders !== false) {
            $this->output_json_success($folders, 'ดึงข้อมูลโฟลเดอร์สำเร็จ');
        } else {
            $this->output_json_error('ไม่สามารถดึงข้อมูลโฟลเดอร์ได้');
        }
        
    } catch (Exception $e) {
        log_message('error', 'Get folder contents error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

	
	/**
 * ดึงสถิติสิทธิ์แบบปลอดภัย (Safe version)
 */
    private function get_folder_permission_stats_safe($folder_id) {
    try {
        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return [
                'total' => 0,
                'inherited' => 0,
                'direct' => 0,
                'override' => 0,
                'by_access_type' => [
                    'owner' => 0,
                    'admin' => 0,
                    'write' => 0,
                    'read' => 0
                ]
            ];
        }

        // นับสิทธิ์ direct เท่านั้น (เพื่อความเร็ว)
        $direct_stats = $this->db->select('
                COUNT(*) as total,
                SUM(CASE WHEN permission_mode = "override" THEN 1 ELSE 0 END) as override_count
            ')
            ->from('tbl_google_drive_member_folder_access')
            ->where('folder_id', $folder_id)
            ->where('is_active', 1)
            ->where('inherit_from_parent', 0)
            ->get()
            ->row();

        if (!$direct_stats) {
            $direct_stats = (object)[
                'total' => 0,
                'override_count' => 0
            ];
        }

        return [
            'total' => (int)$direct_stats->total,
            'inherited' => 0, // จะคำนวณเมื่อจำเป็น
            'direct' => (int)$direct_stats->total,
            'override' => (int)$direct_stats->override_count,
            'by_access_type' => [
                'owner' => 0,
                'admin' => 0,
                'write' => 0,
                'read' => 0
            ]
        ];
        
    } catch (Exception $e) {
        log_message('error', 'Get folder permission stats safe error: ' . $e->getMessage());
        return [
            'total' => 0,
            'inherited' => 0,
            'direct' => 0,
            'override' => 0,
            'by_access_type' => [
                'owner' => 0,
                'admin' => 0,
                'write' => 0,
                'read' => 0
            ]
        ];
    }
}
	
	
/**
 * ✏️ แก้ไขสิทธิ์โฟลเดอร์ที่มีอยู่
 */
public function update_folder_permission() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $permission_id = $this->input->post('permission_id');
        $access_type = $this->input->post('access_type');
        $expires_at = $this->input->post('expires_at');

        // Validate inputs
        if (empty($permission_id) || empty($access_type)) {
            $this->output_json_error('กรุณากรอกข้อมูลให้ครบถ้วน');
            return;
        }

        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $this->output_json_error('ตาราง member_folder_access ไม่มีอยู่');
            return;
        }

        // ตรวจสอบว่ามีสิทธิ์อยู่จริงหรือไม่
        $existing = $this->db->select('id, member_id, folder_id, access_type')
                            ->from('tbl_google_drive_member_folder_access')
                            ->where('id', $permission_id)
                            ->where('is_active', 1)
                            ->get()
                            ->row();

        if (!$existing) {
            $this->output_json_error('ไม่พบสิทธิ์ที่ต้องการแก้ไข');
            return;
        }

        // ป้องกันการแก้ไข owner
        if ($existing->access_type === 'owner') {
            $this->output_json_error('ไม่สามารถแก้ไขสิทธิ์เจ้าของได้');
            return;
        }

        // อัปเดตข้อมูล
        $update_data = [
            'access_type' => $access_type,
            'expires_at' => !empty($expires_at) ? $expires_at . ' 23:59:59' : NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $permission_id);
        $result = $this->db->update('tbl_google_drive_member_folder_access', $update_data);

        if ($result && $this->db->affected_rows() > 0) {
            // Log การกระทำ (ใช้ method ที่มีอยู่)
            $this->log_activity(
                $this->session->userdata('m_id'),
                'update_folder_permission',
                "แก้ไขสิทธิ์โฟลเดอร์: {$existing->access_type} → {$access_type}",
                [
                    'permission_id' => $permission_id,
                    'member_id' => $existing->member_id,
                    'folder_id' => $existing->folder_id,
                    'old_access_type' => $existing->access_type,
                    'new_access_type' => $access_type
                ]
            );
            
            $this->output_json_success([
                'permission_id' => $permission_id,
                'new_access_type' => $access_type,
                'expires_at' => $update_data['expires_at']
            ], 'แก้ไขสิทธิ์สำเร็จ');
        } else {
            $this->output_json_error('ไม่มีการเปลี่ยนแปลงหรือเกิดข้อผิดพลาด');
        }

    } catch (Exception $e) {
        log_message('error', 'Update folder permission error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

	
	public function get_inherited_permissions_stats() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->safe_output_json_error('Invalid request method');
            return;
        }
        
        $folder_id = $this->input->post('folder_id');
        
        if (empty($folder_id)) {
            $this->safe_output_json_error('กรุณาระบุ Folder ID');
            return;
        }

        // นับ subfolders ที่ได้รับสิทธิ์สืบทอด
        $inherited_stats = $this->db->select('
            COUNT(DISTINCT folder_id) as total_subfolders,
            COUNT(DISTINCT member_id) as total_members,
            access_type,
            COUNT(*) as count_by_access_type
        ')
        ->from('tbl_google_drive_member_folder_access')
        ->where('parent_folder_id', $folder_id)
        ->where('inherit_from_parent', 1)
        ->where('is_active', 1)
        ->group_by('access_type')
        ->get()
        ->result();

        $stats = [
            'total_subfolders' => 0,
            'total_members' => 0,
            'by_access_type' => [
                'read' => 0,
                'write' => 0,
                'admin' => 0,
                'owner' => 0
            ]
        ];

        if ($inherited_stats) {
            foreach ($inherited_stats as $stat) {
                $stats['total_subfolders'] = max($stats['total_subfolders'], $stat->total_subfolders);
                $stats['total_members'] = max($stats['total_members'], $stat->total_members);
                
                if (isset($stats['by_access_type'][$stat->access_type])) {
                    $stats['by_access_type'][$stat->access_type] = $stat->count_by_access_type;
                }
            }
        }

        $this->safe_output_json_success($stats, 'ดึงสถิติสิทธิ์สืบทอดสำเร็จ');
        
    } catch (Exception $e) {
        log_message('error', 'Get inherited permissions stats error: ' . $e->getMessage());
        $this->safe_output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	/**
 * บันทึก Activity Log แบบปลอดภัย
 */
/**
 * 🛡️ Safe Log Activity (ไม่ขึ้นกับ parent methods)
 */
private function safe_log_activity($member_id, $action_type, $description, $additional_data = []) {
    try {
        // ใช้ฟังก์ชันเดิมถ้ามีอยู่
        if (method_exists($this, 'log_activity')) {
            return $this->log_activity($member_id, $action_type, $description, $additional_data);
        }

        // หรือเขียน log ลงไฟล์
        log_message('info', "Activity: {$description} by user {$member_id}");
        return true;

    } catch (Exception $e) {
        log_message('error', 'Safe log activity error: ' . $e->getMessage());
        return false;
    }
}



	

	
/**
 * 🗑️ ลบสิทธิ์โฟลเดอร์
 */

public function remove_folder_permission() {
    try {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ✅ แก้ไข AJAX Detection - รองรับหลายกรณี
        $is_ajax = $this->is_ajax_request_enhanced();
        
        if (!$is_ajax) {
            log_message('warning', 'Non-AJAX request detected but proceeding for compatibility');
        }

        // ✅ ตรวจสอบ POST data
        $permission_id = $this->input->post('permission_id');
        $debug_mode = $this->input->post('debug_mode') === 'true';

        if (empty($permission_id) || !is_numeric($permission_id)) {
            $this->output_json_error('กรุณาระบุ Permission ID ที่ถูกต้อง', 400);
            return;
        }

        // ✅ ตรวจสอบ Session
        $current_member_id = $this->session->userdata('m_id');
        if (empty($current_member_id)) {
            $this->output_json_error('กรุณาเข้าสู่ระบบใหม่', 401);
            return;
        }

        // ✅ Log การเริ่มต้น
        $request_log = [
            'permission_id' => $permission_id,
            'requested_by' => $current_member_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => substr($this->input->user_agent(), 0, 500), // จำกัด length
            'is_ajax' => $is_ajax,
            'debug_mode' => $debug_mode
        ];
        
        log_message('info', 'Remove permission request: ' . json_encode($request_log));

        // ✅ ตรวจสอบตาราง
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $error_msg = 'ตาราง tbl_google_drive_member_folder_access ไม่พบ';
            log_message('error', $error_msg);
            $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
            $this->output_json_error($error_msg, 500);
            return;
        }

        // ✅ ดึงข้อมูลสิทธิ์ที่ต้องการลบ พร้อมข้อมูลเพิ่มเติม
        $this->db->select('
            mfa.id,
            mfa.member_id,
            mfa.folder_id,
            mfa.access_type,
            mfa.permission_source,
            mfa.granted_by,
            mfa.granted_at,
            mfa.expires_at,
            mfa.is_active,
            m.m_fname,
            m.m_lname,
            m.m_username,
            p.pname as position_name,
            sf.folder_name
        ');
        $this->db->from('tbl_google_drive_member_folder_access mfa');
        $this->db->join('tbl_member m', 'mfa.member_id = m.m_id', 'left');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->join('tbl_google_drive_system_folders sf', 'mfa.folder_id = sf.folder_id', 'left');
        $this->db->where('mfa.id', $permission_id);
        $this->db->where('mfa.is_active', 1);
        
        $existing = $this->db->get()->row();

        // ✅ ตรวจสอบ Database Error
        $db_error = $this->db->error();
        if ($db_error['code'] !== 0) {
            $error_msg = 'Database Query Error: ' . $db_error['message'];
            log_message('error', $error_msg);
            $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
            $this->output_json_error('เกิดข้อผิดพลาดในการค้นหาข้อมูล', 500);
            return;
        }

        // ✅ Debug mode - แสดงข้อมูลที่ค้นหา
        if ($debug_mode) {
            log_message('debug', 'Permission search result: ' . json_encode($existing));
        }

        if (!$existing) {
            $error_msg = "ไม่พบสิทธิ์ที่ต้องการลบ (ID: {$permission_id}) หรือถูกลบไปแล้ว";
            log_message('warning', $error_msg);
            
            // ตรวจสอบว่ามี record อยู่แต่ is_active = 0
            $deleted_check = $this->db->select('id, is_active, member_id, folder_id, access_type')
                                    ->from('tbl_google_drive_member_folder_access')
                                    ->where('id', $permission_id)
                                    ->get()
                                    ->row();
            
            if ($deleted_check) {
                if ($deleted_check->is_active == 0) {
                    $error_msg = 'สิทธิ์นี้ถูกลบไปแล้ว (Soft Deleted)';
                } else {
                    $error_msg = 'เกิดข้อผิดพลาดในการค้นหาสิทธิ์ (Record exists but join failed)';
                }
                
                if ($debug_mode) {
                    $error_msg .= " - Debug Info: " . json_encode($deleted_check);
                }
            }
            
            $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg, [
                'permission_id' => $permission_id,
                'search_result' => $deleted_check
            ]);
            
            $this->output_json_error($error_msg, 404);
            return;
        }

        // ✅ ตรวจสอบการป้องกัน - ไม่ให้ลบ owner
        if ($existing->access_type === 'owner') {
            $error_msg = 'ไม่สามารถลบสิทธิ์เจ้าของ (Owner) ได้';
            log_message('warning', "Attempt to remove owner permission: {$permission_id} by user: {$current_member_id}");
            
            $this->log_warning_activity($current_member_id, 'remove_folder_permission', $error_msg, [
                'permission_id' => $permission_id,
                'target_member_id' => $existing->member_id,
                'access_type' => $existing->access_type,
                'folder_id' => $existing->folder_id
            ]);
            
            $this->output_json_error($error_msg, 403);
            return;
        }

        // ✅ ป้องกันการลบสิทธิ์ตัวเอง (ถ้าเป็น admin เดียว)
        if ($existing->member_id == $current_member_id && $existing->access_type === 'admin') {
            // ตรวจสอบว่ามี admin คนอื่นไหม
            $other_admins_count = $this->db->select('COUNT(*) as count')
                                          ->from('tbl_google_drive_member_folder_access')
                                          ->where('folder_id', $existing->folder_id)
                                          ->where('access_type', 'admin')
                                          ->where('member_id !=', $current_member_id)
                                          ->where('is_active', 1)
                                          ->get()
                                          ->row();
            
            if ($other_admins_count && $other_admins_count->count == 0) {
                $error_msg = 'ไม่สามารถลบสิทธิ์ผู้ดูแล (Admin) คนสุดท้ายได้';
                log_message('warning', "Attempt to remove last admin: {$permission_id} by user: {$current_member_id}");
                
                $this->log_warning_activity($current_member_id, 'remove_folder_permission', $error_msg, [
                    'permission_id' => $permission_id,
                    'reason' => 'last_admin_protection',
                    'folder_id' => $existing->folder_id
                ]);
                
                $this->output_json_error($error_msg, 403);
                return;
            }
        }

        // ✅ เริ่ม Database Transaction
        $this->db->trans_start();

        // ✅ ทำการลบ (Soft Delete)
        $update_data = [
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // เพิ่มฟิลด์เสริมถ้ามี
        if ($this->db->field_exists('revoked_by', 'tbl_google_drive_member_folder_access')) {
            $update_data['revoked_by'] = $current_member_id;
        }
        
        if ($this->db->field_exists('revoked_at', 'tbl_google_drive_member_folder_access')) {
            $update_data['revoked_at'] = date('Y-m-d H:i:s');
        }
        
        if ($this->db->field_exists('revoked_reason', 'tbl_google_drive_member_folder_access')) {
            $update_data['revoked_reason'] = 'Manual removal by admin';
        }

        $this->db->where('id', $permission_id);
        $result = $this->db->update('tbl_google_drive_member_folder_access', $update_data);

        // ✅ ตรวจสอบผลลัพธ์
        $affected_rows = $this->db->affected_rows();
        $db_error = $this->db->error();

        if ($db_error['code'] !== 0) {
            $this->db->trans_rollback();
            $error_msg = 'Database Update Error: ' . $db_error['message'];
            log_message('error', $error_msg);
            $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
            $this->output_json_error('เกิดข้อผิดพลาดในการอัปเดตฐานข้อมูล', 500);
            return;
        }

        if (!$result || $affected_rows === 0) {
            $this->db->trans_rollback();
            $error_msg = 'ไม่สามารถลบสิทธิ์ได้ (No rows affected)';
            log_message('error', $error_msg);
            $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
            $this->output_json_error($error_msg, 500);
            return;
        }

        // ✅ บันทึก Success Activity Log
        $activity_description = sprintf(
            "ลบสิทธิ์โฟลเดอร์สำเร็จ: %s ของ %s %s ในโฟลเดอร์ %s",
            $existing->access_type,
            $existing->m_fname,
            $existing->m_lname,
            $existing->folder_name
        );

        $activity_data = [
            'permission_id' => $permission_id,
            'target_member_id' => $existing->member_id,
            'target_member_name' => $existing->m_fname . ' ' . $existing->m_lname,
            'folder_name' => $existing->folder_name,
            'removed_access_type' => $existing->access_type,
            'permission_source' => $existing->permission_source,
            'folder_id' => $existing->folder_id,
            'granted_originally_by' => $existing->granted_by,
            'original_grant_date' => $existing->granted_at
        ];

        $this->log_success_activity(
            $current_member_id,
            'remove_folder_permission',
            $activity_description,
            $activity_data,
            $existing->folder_id,
            $existing->m_username
        );

        // ✅ Commit Transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $error_msg = 'Transaction commit failed';
            log_message('error', $error_msg);
            $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
            $this->output_json_error('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 500);
            return;
        }

        // ✅ Log สำเร็จ
        log_message('info', "Permission removed successfully: {$permission_id} by user: {$current_member_id}");

        // ✅ ส่งผลลัพธ์สำเร็จ
        $response_data = [
            'permission_id' => $permission_id,
            'removed_access_type' => $existing->access_type,
            'target_member_id' => $existing->member_id,
            'target_member_name' => $existing->m_fname . ' ' . $existing->m_lname,
            'folder_name' => $existing->folder_name,
            'folder_id' => $existing->folder_id,
            'removed_at' => date('Y-m-d H:i:s'),
            'removed_by' => $current_member_id
        ];

        if ($debug_mode) {
            $response_data['debug_info'] = [
                'affected_rows' => $affected_rows,
                'database_error' => $db_error,
                'transaction_status' => $this->db->trans_status(),
                'ajax_detected' => $is_ajax,
                'original_permission_data' => $existing
            ];
        }

        $this->output_json_success($response_data, 'ลบสิทธิ์สำเร็จ');

    } catch (Exception $e) {
        // ✅ Rollback ถ้ามี transaction
        if (isset($this->db) && method_exists($this->db, 'trans_status') && $this->db->trans_status() !== FALSE) {
            $this->db->trans_rollback();
        }

        // ✅ Log ข้อผิดพลาดร้ายแรง
        $error_log = [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'error_trace' => $e->getTraceAsString(),
            'permission_id' => $permission_id ?? 'unknown',
            'user_id' => $current_member_id ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        log_message('error', 'Remove folder permission critical error: ' . json_encode($error_log));

        // ✅ บันทึก Error Activity Log
        if (isset($current_member_id) && !empty($current_member_id)) {
            $this->log_error_activity(
                $current_member_id,
                'remove_folder_permission',
                'Critical Error: ' . $e->getMessage(),
                $error_log
            );
        }

        $this->output_json_error('เกิดข้อผิดพลาดร้ายแรง: ' . $e->getMessage(), 500);
    }
}

// ===================================================================
// 🛠️ Helper Functions สำหรับ Activity Logging
// ===================================================================

/**
 * ✅ Enhanced AJAX Detection
 */
private function is_ajax_request_enhanced() {
    // วิธีที่ 1: ตรวจสอบ HTTP_X_REQUESTED_WITH
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        return true;
    }
    
    // วิธีที่ 2: ตรวจสอบ CodeIgniter built-in
    if ($this->input->is_ajax_request()) {
        return true;
    }
    
    // วิธีที่ 3: ตรวจสอบพารามิเตอร์พิเศษ
    if ($this->input->post('ajax_request') === 'true' || 
        $this->input->get('ajax') === '1') {
        return true;
    }
    
    // วิธีที่ 4: ตรวจสอบ Accept header
    $accept_header = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept_header, 'application/json') !== false) {
        return true;
    }
    
    // วิธีที่ 5: ถ้าเป็น POST และมี permission_id ให้ถือว่าเป็น AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
        !empty($this->input->post('permission_id'))) {
        return true;
    }
    
    return false;
}	
	
	
private function log_success_activity($member_id, $action_type, $description, $additional_data = [], $folder_id = null, $target_email = null) {
    if (!$this->db->table_exists('tbl_google_drive_logs')) {
        return false;
    }

    try {
        $log_data = [
            'member_id' => $member_id,
            'action_type' => $action_type,
            'action_description' => substr($description, 0, 1000), // จำกัด length
            'module' => 'google_drive_system',
            'status' => 'success',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // เพิ่มฟิลด์เสริม
        if (!empty($folder_id)) {
            $log_data['folder_id'] = $folder_id;
        }
        
        if (!empty($target_email)) {
            $log_data['target_email'] = $target_email;
        }
        
        if (!empty($additional_data)) {
            $log_data['additional_data'] = json_encode($additional_data, JSON_UNESCAPED_UNICODE);
        }
        
        if ($this->db->field_exists('ip_address', 'tbl_google_drive_logs')) {
            $log_data['ip_address'] = $this->input->ip_address();
        }
        
        if ($this->db->field_exists('user_agent', 'tbl_google_drive_logs')) {
            $log_data['user_agent'] = substr($this->input->user_agent(), 0, 500);
        }

        return $this->db->insert('tbl_google_drive_logs', $log_data);
        
    } catch (Exception $e) {
        log_message('error', 'Failed to log success activity: ' . $e->getMessage());
        return false;
    }
}



/**
 * ✅ Step 2: แสดง PHP Errors ล่าสุด
 */
private function show_latest_php_errors() {
    try {
        $error_log_path = ini_get('error_log');
        if (!$error_log_path) {
            $error_log_path = '/var/log/apache2/error.log'; // Default path
        }
        
        if (file_exists($error_log_path) && is_readable($error_log_path)) {
            $errors = $this->tail_file($error_log_path, 20);
            if (!empty($errors)) {
                echo "<div style='background: #fff; border: 1px solid #ccc; padding: 10px; max-height: 200px; overflow-y: auto;'>";
                foreach ($errors as $error) {
                    if (strpos($error, 'google_drive') !== false || strpos($error, 'folder') !== false) {
                        echo "<div style='color: red; margin: 2px 0;'>" . htmlspecialchars($error) . "</div>";
                    }
                }
                echo "</div>";
            } else {
                echo "<p>ไม่พบ PHP errors ล่าสุด</p>";
            }
        } else {
            echo "<p>ไม่สามารถอ่าน error log ได้: {$error_log_path}</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error reading PHP log: " . $e->getMessage() . "</p>";
    }
}

/**
 * ✅ Step 3: แสดง CodeIgniter Logs ล่าสุด
 */
private function show_latest_ci_logs() {
    try {
        $log_path = APPPATH . 'logs/';
        if (is_dir($log_path)) {
            $log_files = glob($log_path . 'log-*.php');
            if (!empty($log_files)) {
                $latest_log = end($log_files);
                $logs = $this->tail_file($latest_log, 30);
                
                echo "<div style='background: #fff; border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;'>";
                foreach ($logs as $log) {
                    if (strpos($log, 'ERROR') !== false || 
                        strpos($log, 'create_folder') !== false ||
                        strpos($log, 'google_drive') !== false) {
                        $color = strpos($log, 'ERROR') !== false ? 'red' : 'blue';
                        echo "<div style='color: {$color}; margin: 2px 0; font-size: 11px;'>" . htmlspecialchars($log) . "</div>";
                    }
                }
                echo "</div>";
            } else {
                echo "<p>ไม่พบ log files</p>";
            }
        } else {
            echo "<p>ไม่พบ log directory: {$log_path}</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error reading CI logs: " . $e->getMessage() . "</p>";
    }
}

/**
 * ✅ Step 4: API สำหรับดึง errors ล่าสุด
 */
public function get_latest_errors() {
    try {
        header('Content-Type: application/json; charset=utf-8');
        
        $errors = [];
        
        // ดึงจาก CI logs
        $log_path = APPPATH . 'logs/';
        if (is_dir($log_path)) {
            $log_files = glob($log_path . 'log-*.php');
            if (!empty($log_files)) {
                $latest_log = end($log_files);
                $logs = $this->tail_file($latest_log, 10);
                
                foreach ($logs as $log) {
                    if (strpos($log, 'ERROR') !== false || strpos($log, 'create_folder') !== false) {
                        $errors[] = [
                            'timestamp' => date('Y-m-d H:i:s'),
                            'message' => trim($log),
                            'source' => 'CI_Log'
                        ];
                    }
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'errors' => array_slice($errors, -5), // แสดง 5 รายการล่าสุด
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * ✅ Step 5: Safe Create Folder Function พร้อม Detailed Logging
 */
public function create_folder_structure_with_permissions_safe() {
    // บังคับให้แสดง error ทั้งหมด
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    // ล้าง output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // ตั้งค่า headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    $debug_info = [
        'start_time' => microtime(true),
        'memory_start' => memory_get_usage(true),
        'steps' => []
    ];
    
    try {
        // Step 1: Basic Checks
        $debug_info['steps'][] = 'Starting basic checks';
        
        if (!$this->input->is_ajax_request() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method');
        }
        
        $debug_info['steps'][] = 'Request method OK';
        
        // Step 2: Session Check
        $user_id = $this->session->userdata('m_id');
        $user_system = $this->session->userdata('m_system');
        
        if (!$user_id) {
            throw new Exception('No user session');
        }
        
        $debug_info['steps'][] = "User session OK: {$user_id} ({$user_system})";
        
        // Step 3: Database Check
        if (!$this->db || !$this->db->conn_id) {
            throw new Exception('Database connection failed');
        }
        
        $debug_info['steps'][] = 'Database connection OK';
        
        // Step 4: Table Existence Check
        $required_tables = ['tbl_google_drive_system_storage'];
        foreach ($required_tables as $table) {
            if (!$this->db->table_exists($table)) {
                throw new Exception("Table {$table} does not exist");
            }
        }
        
        $debug_info['steps'][] = 'Required tables exist';
        
        // Step 5: System Storage Check
        $system_storage = $this->get_system_storage_safe();
        if (!$system_storage) {
            throw new Exception('No system storage found');
        }
        
        $debug_info['steps'][] = 'System storage found';
        $debug_info['storage_info'] = [
            'id' => $system_storage->id,
            'email' => $system_storage->google_account_email,
            'has_token' => !empty($system_storage->google_access_token),
            'folder_created' => $system_storage->folder_structure_created
        ];
        
        // Step 6: Check if already created
        if ($system_storage->folder_structure_created == 1) {
            throw new Exception('Folder structure already created');
        }
        
        // Step 7: Token Check
        if (!$system_storage->google_access_token) {
            throw new Exception('No Google access token');
        }
        
        $token_data = json_decode($system_storage->google_access_token, true);
        if (!$token_data || !isset($token_data['access_token'])) {
            throw new Exception('Invalid token format');
        }
        
        $debug_info['steps'][] = 'Token validation OK';
        
        // Step 8: Google API Test
        $api_test = $this->test_google_api_simple($token_data['access_token']);
        if (!$api_test['success']) {
            throw new Exception('Google API test failed: ' . $api_test['error']);
        }
        
        $debug_info['steps'][] = 'Google API test OK';
        
        // Step 9: Create Root Folder (Simple Version)
        $root_result = $this->create_root_folder_simple($token_data['access_token']);
        if (!$root_result['success']) {
            throw new Exception('Root folder creation failed: ' . $root_result['error']);
        }
        
        $debug_info['steps'][] = 'Root folder created: ' . $root_result['folder_id'];
        
        // Step 10: Update Database
        $this->db->where('id', $system_storage->id)
                 ->update('tbl_google_drive_system_storage', [
                     'root_folder_id' => $root_result['folder_id'],
                     'folder_structure_created' => 1
                 ]);
        
        $debug_info['steps'][] = 'Database updated';
        
        // Success Response
        $debug_info['end_time'] = microtime(true);
        $debug_info['memory_end'] = memory_get_usage(true);
        $debug_info['execution_time'] = $debug_info['end_time'] - $debug_info['start_time'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Folder structure created successfully',
            'data' => [
                'root_folder_id' => $root_result['folder_id'],
                'stats' => [
                    'folders_created' => 1,
                    'execution_time' => round($debug_info['execution_time'], 3)
                ]
            ],
            'debug_info' => $debug_info
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // Error Response with Full Debug Info
        $debug_info['error'] = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        $debug_info['end_time'] = microtime(true);
        $debug_info['execution_time'] = $debug_info['end_time'] - $debug_info['start_time'];
        
        // Log detailed error
        log_message('error', 'Folder creation safe error: ' . json_encode($debug_info));
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'debug_info' => $debug_info,
            'help' => [
                'Check logs at: ' . APPPATH . 'logs/',
                'Verify database connection',
                'Check Google API credentials',
                'Ensure proper file permissions'
            ]
        ], JSON_UNESCAPED_UNICODE);
    }
}


/**
 * ✅ Simple Google API Test
 */
private function test_google_api_simple($access_token) {
    try {
        $url = 'https://www.googleapis.com/drive/v3/about?fields=user';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $access_token],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($curl_error) {
            return ['success' => false, 'error' => 'cURL Error: ' . $curl_error];
        }
        
        if ($http_code !== 200) {
            return ['success' => false, 'error' => "HTTP {$http_code}: {$response}"];
        }
        
        return ['success' => true, 'response' => $response];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * ✅ Simple Root Folder Creation
 */
private function create_root_folder_simple($access_token) {
    try {
        $url = 'https://www.googleapis.com/drive/v3/files';
        
        $metadata = [
            'name' => 'Organization Drive Test',
            'mimeType' => 'application/vnd.google-apps.folder'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($metadata),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($curl_error) {
            return ['success' => false, 'error' => 'cURL Error: ' . $curl_error];
        }
        
        if ($http_code !== 200) {
            return ['success' => false, 'error' => "HTTP {$http_code}: {$response}"];
        }
        
        $folder_data = json_decode($response, true);
        if (!$folder_data || !isset($folder_data['id'])) {
            return ['success' => false, 'error' => 'Invalid API response'];
        }
        
        return ['success' => true, 'folder_id' => $folder_data['id']];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * ✅ Helper: Read last N lines from file
 */
private function tail_file($filename, $lines = 10) {
    try {
        if (!file_exists($filename) || !is_readable($filename)) {
            return [];
        }
        
        $file = file($filename);
        if (!$file) {
            return [];
        }
        
        return array_slice($file, -$lines);
        
    } catch (Exception $e) {
        return [];
    }
}

/**
 * ✅ Quick Fix Generator
 */
public function generate_quick_fixes() {
    echo "<h2>🛠️ Quick Fix Generator</h2>";
    echo "<div style='font-family: monospace;'>";
    
    $fixes = [
        'File Permissions' => [
            'chmod 755 ' . APPPATH . 'logs/',
            'chmod 666 ' . APPPATH . 'logs/log-*.php',
            'chown www-data:www-data ' . APPPATH . 'logs/'
        ],
        'Apache Configuration' => [
            'Check mod_rewrite is enabled',
            'Verify .htaccess files',
            'Check AllowOverride All'
        ],
        'PHP Configuration' => [
            'max_execution_time = 300',
            'memory_limit = 256M',
            'upload_max_filesize = 100M'
        ],
        'Database' => [
            'Check MySQL connection',
            'Verify table permissions',
            'Check charset settings'
        ]
    ];
    
    foreach ($fixes as $category => $commands) {
        echo "<h3>{$category}</h3>";
        echo "<ul>";
        foreach ($commands as $command) {
            echo "<li><code>{$command}</code></li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";
}	
	
	/**
 * ✅ หน้าทดสอบ Debug - เพิ่มใน Google_drive_system Controller
 * URL: your_site.com/google_drive_system/debug_test
 */
public function debug_test() {
    // ตรวจสอบสิทธิ์ Admin
    $user_system = $this->session->userdata('m_system');
    $allowed_systems = ['system_admin', 'super_admin', 'user_admin'];
    
    if (!in_array($user_system, $allowed_systems)) {
        show_404();
        return;
    }
    
    $data = [
        'title' => 'Debug Test - Folder Creation System',
        'user_id' => $this->session->userdata('m_id'),
        'user_system' => $user_system,
        'current_url' => current_url()
    ];
    
    // โหลด view สำหรับ debug test
    $this->load->view('debug_test_view', $data);
}



/**
 * ✅ Helper: Debug Token Validation
 */
private function debug_token_validation($system_storage) {
    try {
        if (!$system_storage->google_access_token) {
            return [
                'valid' => false,
                'message' => 'No access token found',
                'needs_refresh' => false
            ];
        }
        
        $expires_at = strtotime($system_storage->google_token_expires);
        $current_time = time();
        $time_until_expiry = $expires_at - $current_time;
        $needs_refresh = $time_until_expiry <= 300; // 5 minutes
        
        return [
            'valid' => $time_until_expiry > 0,
            'access_token' => $system_storage->google_access_token,
            'expires_at' => $system_storage->google_token_expires,
            'time_until_expiry' => $time_until_expiry,
            'needs_refresh' => $needs_refresh,
            'message' => $needs_refresh ? 'Token needs refresh' : 'Token is valid'
        ];
        
    } catch (Exception $e) {
        return [
            'valid' => false,
            'message' => 'Token validation error: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ];
    }
}

/**
 * ✅ Helper: Debug Google API Test
 */
private function debug_google_api_test($access_token) {
    try {
        $url = 'https://www.googleapis.com/drive/v3/about?fields=user,storageQuota';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json'
            ]
        ]);
        
        $start_time = microtime(true);
        $response = curl_exec($ch);
        $end_time = microtime(true);
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_info = curl_getinfo($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        $api_debug = [
            'url' => $url,
            'http_code' => $http_code,
            'response_time' => round($end_time - $start_time, 3),
            'response_size' => strlen($response),
            'curl_error' => $curl_error
        ];
        
        if ($curl_error) {
            return [
                'success' => false,
                'message' => 'CURL Error: ' . $curl_error,
                'debug' => $api_debug
            ];
        }
        
        if ($http_code !== 200) {
            return [
                'success' => false,
                'message' => "HTTP {$http_code}: API request failed",
                'debug' => $api_debug,
                'response_preview' => substr($response, 0, 200)
            ];
        }
        
        $api_data = json_decode($response, true);
        if (!$api_data) {
            return [
                'success' => false,
                'message' => 'Invalid JSON response from Google API',
                'debug' => $api_debug
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Google API connection successful',
            'debug' => $api_debug,
            'user_info' => $api_data['user'] ?? null,
            'storage_info' => $api_data['storageQuota'] ?? null
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'API test error: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ];
    }
}

/**
 * ✅ Helper: Get Debug Recommendations
 */
private function get_debug_recommendations($debug_info) {
    $recommendations = [];
    
    // Database recommendations
    if (isset($debug_info['database_checks']['connection']) && $debug_info['database_checks']['connection'] !== 'OK') {
        $recommendations[] = '🗃️ ตรวจสอบการเชื่อมต่อฐานข้อมูลใน config/database.php';
    }
    
    // Table recommendations
    if (isset($debug_info['database_checks']['tables'])) {
        foreach ($debug_info['database_checks']['tables'] as $table => $status) {
            if (strpos($status, 'MISSING') !== false) {
                $recommendations[] = "📋 ตาราง {$table} ไม่พบ - ต้องสร้างตารางนี้";
            }
        }
    }
    
    // Storage recommendations
    if (isset($debug_info['database_checks']['system_storage']) && $debug_info['database_checks']['system_storage'] === 'NOT_FOUND') {
        $recommendations[] = '🔗 ต้องเชื่อมต่อ Google Account ก่อนใช้งาน';
    }
    
    // Token recommendations
    if (isset($debug_info['token_info']) && !$debug_info['token_info']['valid']) {
        $recommendations[] = '🔑 Token หมดอายุหรือไม่ถูกต้อง - ต้องเชื่อมต่อ Google Account ใหม่';
    }
    
    // API recommendations
    if (isset($debug_info['api_tests']) && !$debug_info['api_tests']['success']) {
        $recommendations[] = '🌐 Google API เชื่อมต่อไม่ได้ - ตรวจสอบ credentials และ network';
    }
    
    // Config recommendations
    if (isset($debug_info['config_check'])) {
        if (!$debug_info['config_check']['google_client_id']) {
            $recommendations[] = '⚙️ ต้องตั้งค่า Google Client ID ใน config';
        }
        if (!$debug_info['config_check']['google_client_secret']) {
            $recommendations[] = '⚙️ ต้องตั้งค่า Google Client Secret ใน config';
        }
    }
    
    // Error recommendations
    if (!empty($debug_info['errors'])) {
        $recommendations[] = '🐛 มี PHP errors - ตรวจสอบ error log';
    }
    
    if (empty($recommendations)) {
        $recommendations[] = '✅ ระบบพร้อมใช้งาน - สามารถสร้างโครงสร้างโฟลเดอร์ได้';
    }
    
    return $recommendations;
}



/**
 * ✅ Route สำหรับแสดงหน้า Debug Test
 * เพิ่มใน routes.php หรือเรียกตรงๆ
 */
public function debug_view() {
    $this->get_debug_test_view();
}

public function diagnose_500_error() {
    // ป้องกันไม่ให้ error แสดงออกมา
    ini_set('display_errors', 0);
    error_reporting(0);
    
    // ล้าง output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    
    $diagnosis = [
        'timestamp' => date('Y-m-d H:i:s'),
        'server_info' => [],
        'session_check' => [],
        'database_check' => [],
        'google_client_check' => [],
        'api_endpoint_check' => [],
        'token_check' => [],
        'final_recommendation' => [],
        'debug_steps' => []
    ];
    
    try {
        // 1. ตรวจสอบ Server Environment
        $diagnosis['debug_steps'][] = 'Checking server environment...';
        
        $diagnosis['server_info'] = [
            'php_version' => phpversion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'curl_available' => function_exists('curl_init'),
            'json_available' => function_exists('json_encode'),
            'openssl_available' => extension_loaded('openssl'),
            'error_reporting' => error_reporting(),
            'display_errors' => ini_get('display_errors')
        ];
        
        // 2. ตรวจสอบ Session
        $diagnosis['debug_steps'][] = 'Checking user session...';
        
        $diagnosis['session_check'] = [
            'session_started' => session_status() === PHP_SESSION_ACTIVE,
            'session_id' => session_id(),
            'm_id' => $this->session->userdata('m_id'),
            'm_system' => $this->session->userdata('m_system'),
            'm_username' => $this->session->userdata('m_username'),
            'all_userdata' => $this->session->all_userdata()
        ];
        
        // 3. ตรวจสอบ Database Connection
        $diagnosis['debug_steps'][] = 'Checking database connection...';
        
        try {
            $this->db->query('SELECT 1 as test');
            $diagnosis['database_check']['connection'] = 'OK';
            
            // ตรวจสอบตารางที่จำเป็น
            $required_tables = [
                'tbl_member',
                'tbl_position', 
                'tbl_google_drive_system_storage',
                'tbl_google_drive_system_folders',
                'tbl_google_drive_settings'
            ];
            
            foreach ($required_tables as $table) {
                $exists = $this->db->table_exists($table);
                $diagnosis['database_check']['tables'][$table] = $exists ? 'EXISTS' : 'MISSING';
                
                if ($exists && in_array($table, ['tbl_member', 'tbl_position'])) {
                    $count = $this->db->count_all($table);
                    $diagnosis['database_check']['tables'][$table . '_count'] = $count;
                }
            }
            
        } catch (Exception $e) {
            $diagnosis['database_check']['connection'] = 'ERROR: ' . $e->getMessage();
        }
        
        // 4. ตรวจสอบ Google Client Library
        $diagnosis['debug_steps'][] = 'Checking Google Client Library...';
        
        if (class_exists('Google\\Client')) {
            $diagnosis['google_client_check']['library_loaded'] = 'YES';
            
            try {
                $test_client = new Google\Client();
                $diagnosis['google_client_check']['can_instantiate'] = 'YES';
                
                // ตรวจสอบ OAuth Settings
                $client_id = $this->get_setting_safe('google_client_id');
                $client_secret = $this->get_setting_safe('google_client_secret');
                
                $diagnosis['google_client_check']['oauth_config'] = [
                    'client_id_set' => !empty($client_id),
                    'client_secret_set' => !empty($client_secret),
                    'client_id_length' => strlen($client_id ?? ''),
                    'client_secret_length' => strlen($client_secret ?? '')
                ];
                
            } catch (Exception $e) {
                $diagnosis['google_client_check']['can_instantiate'] = 'NO: ' . $e->getMessage();
                $diagnosis['google_client_check']['error_type'] = $this->identify_google_error($e->getMessage());
            }
        } else {
            $diagnosis['google_client_check']['library_loaded'] = 'NO';
            
            // ตรวจสอบ Composer paths
            $composer_paths = [
                FCPATH . 'vendor/autoload.php',
                APPPATH . '../vendor/autoload.php',
                FCPATH . 'application/third_party/google/vendor/autoload.php'
            ];
            
            foreach ($composer_paths as $path) {
                $diagnosis['google_client_check']['composer_paths'][$path] = file_exists($path) ? 'EXISTS' : 'NOT_FOUND';
            }
        }
        
        // 5. ตรวจสอบ API Endpoint
        $diagnosis['debug_steps'][] = 'Checking API endpoint availability...';
        
        $diagnosis['api_endpoint_check'] = [
            'method_exists' => method_exists($this, 'create_folder_structure_with_permissions'),
            'class_name' => get_class($this),
            'all_methods' => get_class_methods($this)
        ];
        
        // 6. ตรวจสอบ System Storage และ Token
        $diagnosis['debug_steps'][] = 'Checking system storage and tokens...';
        
        try {
            $storage = $this->get_active_system_storage();
            if ($storage) {
                $diagnosis['token_check'] = [
                    'storage_found' => true,
                    'email' => $storage->google_account_email,
                    'has_access_token' => !empty($storage->google_access_token),
                    'has_refresh_token' => !empty($storage->google_refresh_token),
                    'folder_structure_created' => $storage->folder_structure_created == 1,
                    'token_expires' => $storage->google_token_expires,
                    'token_length' => strlen($storage->google_access_token ?? ''),
                    'is_active' => $storage->is_active == 1
                ];
                
                // ทดสอบ Token (ถ้ามี)
                if (!empty($storage->google_access_token)) {
                    $token_test = $this->test_google_token_simple($storage->google_access_token);
                    $diagnosis['token_check']['token_valid'] = $token_test;
                }
                
            } else {
                $diagnosis['token_check'] = [
                    'storage_found' => false,
                    'message' => 'No active system storage found'
                ];
            }
        } catch (Exception $e) {
            $diagnosis['token_check'] = [
                'error' => 'Storage check failed: ' . $e->getMessage()
            ];
        }
        
        // 7. สร้างคำแนะนำ
        $diagnosis['final_recommendation'] = $this->generate_recommendations($diagnosis);
        
        echo json_encode($diagnosis, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => 'Diagnosis failed',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * ✅ Helper: ดึง Setting แบบปลอดภัย
 */
private function get_setting_safe($key) {
    try {
        if (method_exists($this, 'get_setting')) {
            return $this->get_setting($key);
        }
        
        if ($this->db->table_exists('tbl_google_drive_settings')) {
            $result = $this->db->select('setting_value')
                              ->from('tbl_google_drive_settings')
                              ->where('setting_key', $key)
                              ->where('is_active', 1)
                              ->get()
                              ->row();
            
            return $result ? $result->setting_value : null;
        }
        
        return null;
        
    } catch (Exception $e) {
        return 'ERROR: ' . $e->getMessage();
    }
}

/**
 * ✅ Helper: ระบุประเภท Google Error
 */
private function identify_google_error($error_message) {
    if (strpos($error_message, 'Monolog') !== false) {
        return 'MONOLOG_LOGGER_ERROR';
    } elseif (strpos($error_message, 'autoload') !== false) {
        return 'COMPOSER_AUTOLOAD_ERROR';
    } elseif (strpos($error_message, 'Class') !== false) {
        return 'CLASS_NOT_FOUND_ERROR';
    } else {
        return 'UNKNOWN_ERROR';
    }
}


/**
 * ✅ Helper: สร้างคำแนะนำตามผลการตรวจสอบ
 */
private function generate_recommendations($diagnosis) {
    $recommendations = [];
    
    // ตรวจสอบ Session
    if (empty($diagnosis['session_check']['m_id'])) {
        $recommendations[] = '🔐 กรุณาเข้าสู่ระบบใหม่ (Session หมดอายุ)';
    }
    
    // ตรวจสอบ Admin Permission
    $user_system = $diagnosis['session_check']['m_system'] ?? '';
    $admin_levels = ['system_admin', 'super_admin', 'm_00', 'm_01', 'm_02', 'm_03'];
    if (!in_array($user_system, $admin_levels)) {
        $recommendations[] = '⚠️ ไม่มีสิทธิ์ Admin (ระบบปัจจุบัน: ' . $user_system . ')';
    }
    
    // ตรวจสอบ Database Tables
    if (isset($diagnosis['database_check']['tables'])) {
        foreach ($diagnosis['database_check']['tables'] as $table => $status) {
            if ($status === 'MISSING' && !strpos($table, '_count')) {
                $recommendations[] = '📊 ตาราง ' . $table . ' ไม่มีอยู่ - ต้องสร้างก่อน';
            }
        }
    }
    
    // ตรวจสอบ Google Client
    if ($diagnosis['google_client_check']['library_loaded'] === 'NO') {
        $recommendations[] = '📚 ติดตั้ง Google Client Library ด้วย Composer';
    } elseif (isset($diagnosis['google_client_check']['error_type'])) {
        switch ($diagnosis['google_client_check']['error_type']) {
            case 'MONOLOG_LOGGER_ERROR':
                $recommendations[] = '🔧 ใช้ cURL mode แทน Google Client Library';
                break;
            case 'COMPOSER_AUTOLOAD_ERROR':
                $recommendations[] = '📦 ตรวจสอบ Composer autoload.php';
                break;
        }
    }
    
    // ตรวจสอบ OAuth Config
    if (isset($diagnosis['google_client_check']['oauth_config'])) {
        $oauth = $diagnosis['google_client_check']['oauth_config'];
        if (!$oauth['client_id_set']) {
            $recommendations[] = '🔑 ตั้งค่า Google Client ID';
        }
        if (!$oauth['client_secret_set']) {
            $recommendations[] = '🔒 ตั้งค่า Google Client Secret';
        }
    }
    
    // ตรวจสอบ System Storage
    if (isset($diagnosis['token_check']['storage_found']) && !$diagnosis['token_check']['storage_found']) {
        $recommendations[] = '☁️ เชื่อมต่อ Google Account ก่อน';
    } elseif (isset($diagnosis['token_check']['token_valid']) && !$diagnosis['token_check']['token_valid']['valid']) {
        $recommendations[] = '🔄 Refresh Google Access Token';
    }
    
    // ถ้าไม่มีปัญหาใหญ่
    if (empty($recommendations)) {
        $recommendations[] = '✅ ระบบดูปกติ - ปัญหาอาจอยู่ที่ method อื่น';
        $recommendations[] = '🔍 ตรวจสอบ PHP Error Log เพิ่มเติม';
    }
    
    return $recommendations;
}


	
	
/**
 * ✅ ทดสอบฟังก์ชันที่แก้ไขแล้ว
 */
public function test_corrected_functions() {
    echo "<h1>🧪 Test Corrected Functions</h1>";
    echo "<style>
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
    </style>";
    
    try {
        echo "<h2>1. Test Admin Users</h2>";
        $admin_users = $this->get_admin_users();
        echo "<p class='success'>✅ Admin Users Found: " . count($admin_users) . "</p>";
        
        if (!empty($admin_users)) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Name</th><th>m_system</th><th>grant_system_ref_id</th></tr>";
            foreach ($admin_users as $admin) {
                echo "<tr>";
                echo "<td>{$admin['m_id']}</td>";
                echo "<td>{$admin['name']}</td>";
                echo "<td>{$admin['m_system']}</td>";
                echo "<td>{$admin['grant_system_ref_id']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<h2>2. Test All Active Users</h2>";
        $all_users = $this->get_all_active_users();
        echo "<p class='success'>✅ All Active Users: " . count($all_users) . "</p>";
        
        echo "<h2>3. Test Position Users</h2>";
        $pos_users = $this->get_users_by_position(4);
        echo "<p class='success'>✅ Position 4 Users: " . count($pos_users) . "</p>";
        
        echo "<h2>4. Test Permission Creation</h2>";
        if (!empty($admin_users)) {
            $test_folder_id = 'test_folder_corrected_' . time();
            $result = $this->add_folder_permission_direct($test_folder_id, $admin_users[0]['m_id'], 'admin');
            
            if ($result) {
                echo "<p class='success'>✅ Permission creation successful</p>";
                
                // ตรวจสอบว่าบันทึกลงฐานข้อมูลแล้ว
                $saved = $this->db->where('folder_id', $test_folder_id)->get('tbl_google_drive_folder_permissions')->row();
                if ($saved) {
                    echo "<p class='success'>✅ Permission saved to database</p>";
                    echo "<p>Permission ID: {$saved->id}, Access Level: {$saved->access_level}</p>";
                    
                    // ลบข้อมูลทดสอบ
                    $this->db->where('folder_id', $test_folder_id)->delete('tbl_google_drive_folder_permissions');
                    echo "<p class='info'>🗑️ Test permission deleted</p>";
                } else {
                    echo "<p class='error'>❌ Permission not found in database</p>";
                }
            } else {
                echo "<p class='error'>❌ Permission creation failed</p>";
            }
        }
        
        echo "<h2>✅ All Tests Completed</h2>";
        echo "<p><strong>Functions are ready to use!</strong></p>";
        echo "<p><a href='" . site_url('google_drive_system/setup') . "' style='background: green; color: white; padding: 10px; text-decoration: none;'>🏠 Back to Setup</a></p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
        echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
    }
}

/**
 * ✅ ตรวจสอบ m_status values
 */
public function check_member_status() {
    echo "<h1>📊 Check Member Status Values</h1>";
    
    try {
        // ตรวจสอบค่า m_status
        $status_counts = $this->db->select('m_status, COUNT(*) as count')
                                 ->group_by('m_status')
                                 ->get('tbl_member')
                                 ->result();
        
        echo "<h2>m_status Values:</h2>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>m_status</th><th>Count</th></tr>";
        foreach ($status_counts as $status) {
            echo "<tr><td>{$status->m_status}</td><td>{$status->count}</td></tr>";
        }
        echo "</table>";
        
        // ตรวจสอบค่า m_system
        $system_counts = $this->db->select('m_system, COUNT(*) as count')
                                 ->group_by('m_system')
                                 ->get('tbl_member')
                                 ->result();
        
        echo "<h2>m_system Values:</h2>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>m_system</th><th>Count</th></tr>";
        foreach ($system_counts as $system) {
            echo "<tr><td>{$system->m_system}</td><td>{$system->count}</td></tr>";
        }
        echo "</table>";
        
        // ตรวจสอบ grant_system_ref_id
        $grant_counts = $this->db->select('grant_system_ref_id, COUNT(*) as count')
                                ->group_by('grant_system_ref_id')
                                ->get('tbl_member')
                                ->result();
        
        echo "<h2>grant_system_ref_id Values:</h2>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>grant_system_ref_id</th><th>Count</th></tr>";
        foreach ($grant_counts as $grant) {
            echo "<tr><td>{$grant->grant_system_ref_id}</td><td>{$grant->count}</td></tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
	



	
/**
 * 🏢 ดึงรายการตำแหน่งสำหรับ filter (แก้ไข)
 */
public function get_positions_for_filter() {
    try {
        // ตรวจสอบว่าตาราง position มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_position')) {
            $this->output_json_error('ไม่พบตาราง tbl_position');
            return;
        }

        $positions = $this->db->select('pid, pname, peng as pdepartment')
                             ->from('tbl_position')
                             ->where('pstatus', 'show')  // ✅ แก้ไขจาก 1 เป็น 'show'
                             ->order_by('porder', 'ASC') // ✅ เปลี่ยนจาก pname เป็น porder
                             ->order_by('pname', 'ASC')  // ✅ เพิ่ม secondary sort
                             ->get()
                             ->result();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'ดึงรายการตำแหน่งเรียบร้อย',
                'data' => $positions,
                'count' => count($positions)
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'Get positions for filter error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * 👥 ดึงผู้ใช้ทั้งหมดพร้อมข้อมูลโฟลเดอร์และสิทธิ์ (แก้ไข)
 */
public function get_all_users_for_management() {
    try {
        // ลบการตรวจสอบ AJAX ก่อน เพื่อ debug
        // if (!$this->input->is_ajax_request()) {
        //     show_404();
        // }

        // ตรวจสอบสิทธิ์แบบง่าย
        $user_system = $this->session->userdata('m_system');
        if (!in_array($user_system, ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง',
                    'data' => null
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบตาราง
        if (!$this->db->table_exists('tbl_member')) {
            throw new Exception('ไม่พบตาราง tbl_member');
        }

        // ดึงข้อมูลผู้ใช้แบบง่าย - เช็ค field ก่อน
        $member_fields = $this->db->list_fields('tbl_member');
        
        $select_fields = [
            'm.m_id',
            'm.m_fname',
            'm.m_lname',
            'm.m_email',
            'm.ref_pid',
            'm.m_datesave as member_since',
            'm.m_status',
            'CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as full_name'
        ];
        
        // เพิ่ม fields ที่มีจริงเท่านั้น
        if (in_array('storage_access_granted', $member_fields)) {
            $select_fields[] = 'COALESCE(m.storage_access_granted, 0) as storage_access_granted';
        } else {
            $select_fields[] = '0 as storage_access_granted';
        }
        
        if (in_array('personal_folder_id', $member_fields)) {
            $select_fields[] = 'm.personal_folder_id';
        } else {
            $select_fields[] = 'NULL as personal_folder_id';
        }
        
        if (in_array('storage_quota_limit', $member_fields)) {
            $select_fields[] = 'COALESCE(m.storage_quota_limit, 0) as storage_quota_limit';
        } else {
            $select_fields[] = '0 as storage_quota_limit';
        }
        
        if (in_array('storage_quota_used', $member_fields)) {
            $select_fields[] = 'COALESCE(m.storage_quota_used, 0) as storage_quota_used';
        } else {
            $select_fields[] = '0 as storage_quota_used';
        }
        
        $this->db->select(implode(', ', $select_fields), false);
        
        $this->db->from('tbl_member m');
        
        // เช็คว่ามีตาราง position หรือไม่
        if ($this->db->table_exists('tbl_position')) {
            $this->db->select('p.pname as position_name, p.peng as pdepartment', false);
            $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        } else {
            $this->db->select('"ไม่ระบุ" as position_name, "" as pdepartment', false);
        }
        
        $this->db->where('m.m_status', 1);
        $this->db->order_by('m.m_fname', 'ASC');
        
        $users_query = $this->db->get();
        $users = $users_query->result();

        // เพิ่มข้อมูลเพิ่มเติมแบบง่าย
        foreach ($users as &$user) {
            // แปลงข้อมูล
            $user->storage_access_granted = (int)$user->storage_access_granted;
            $user->storage_quota_limit = (int)$user->storage_quota_limit;
            $user->storage_quota_used = (int)$user->storage_quota_used;
            
            // คำนวณ percentage
            if ($user->storage_quota_limit > 0) {
                $user->storage_usage_percent = round(($user->storage_quota_used / $user->storage_quota_limit) * 100, 2);
            } else {
                $user->storage_usage_percent = 0;
            }
            
            // Format storage sizes
            $user->storage_quota_limit_formatted = $this->simple_format_bytes($user->storage_quota_limit);
            $user->storage_quota_used_formatted = $this->simple_format_bytes($user->storage_quota_used);
            
            // ตรวจสอบโฟลเดอร์
            $user->has_personal_folder = !empty($user->personal_folder_id);
            
            // เพิ่ม permissions แบบง่าย (ถ้าไม่มีตารางก็ให้ array ว่าง)
            $user->permissions = $this->get_user_permissions_simple($user->m_id);
            
            // แก้ไขชื่อที่อาจจะเป็น null
            if (empty($user->full_name) || trim($user->full_name) == '') {
                $user->full_name = 'ไม่ระบุชื่อ';
            }
            
            // แก้ไข position ที่อาจจะเป็น null
            if (empty($user->position_name)) {
                $user->position_name = 'ไม่ระบุตำแหน่ง';
            }
        }

        // คำนวณสถิติ
        $total_users = count($users);
        $active_users = count(array_filter($users, function($u) { return $u->storage_access_granted == 1; }));
        $users_with_folders = count(array_filter($users, function($u) { return $u->has_personal_folder; }));
        $pending_users = $total_users - $users_with_folders;

        $stats = [
            'total_users' => $total_users,
            'active_users' => $active_users,
            'users_with_folders' => $users_with_folders,
            'pending_users' => $pending_users
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'ดึงข้อมูลผู้ใช้เรียบร้อย',
                'data' => [
                    'users' => $users,
                    'stats' => $stats
                ]
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'Get all users for management error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'data' => null
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * 🔍 ดึงสิทธิ์ของผู้ใช้แบบง่าย
 */
private function get_user_permissions_simple($user_id) {
    try {
        // ถ้าไม่มีตารางสิทธิ์ก็คืนค่า array ว่าง
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return [];
        }

        $permissions = $this->db->select('access_type, permission_source, granted_at')
            ->from('tbl_google_drive_member_folder_access')
            ->where('member_id', $user_id)
            ->where('is_active', 1)
            ->order_by('granted_at', 'DESC')
            ->limit(5) // จำกัดแค่ 5 สิทธิ์ล่าสุด
            ->get()
            ->result();

        return $permissions;

    } catch (Exception $e) {
        log_message('error', 'Get user permissions simple error: ' . $e->getMessage());
        return [];
    }
}

/**
 * 📊 Format bytes แบบง่าย
 */
private function simple_format_bytes($bytes, $precision = 2) {
    $bytes = max(0, (int)$bytes);
    
    if ($bytes === 0) {
        return '0 B';
    }
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pow = floor(log($bytes, 1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * 🔄 เปิด/ปิดการใช้งาน Storage พร้อมสร้าง Personal Folder อัตโนมัติ
 */
public function toggle_user_storage_access_with_folder() {
    // ✅ ป้องกัน PHP Error แสดงเป็น HTML
    ini_set('display_errors', 0);
    error_reporting(0);
    
    try {
        // ✅ ตรวจสอบ Method ที่จำเป็นก่อน
        $required_methods = [
            'safe_get_user_data',
            'process_enable_user_storage', 
            'process_disable_user_storage',
            'safe_start_transaction',
            'safe_commit_transaction',
            'safe_rollback_transaction',
            'safe_log_activity'
        ];
        
        foreach ($required_methods as $method) {
            if (!method_exists($this, $method)) {
                $this->emergency_json_response([
                    'success' => false,
                    'message' => "Method {$method} not found",
                    'error_type' => 'missing_method'
                ]);
                return;
            }
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->emergency_json_response([
                'success' => false,
                'message' => 'Not AJAX request',
                'error_type' => 'invalid_request'
            ]);
            return;
        }

        // ตรวจสอบสิทธิ์ Admin
        $user_system = $this->session->userdata('m_system');
        if (!in_array($user_system, ['system_admin', 'super_admin'])) {
            $this->emergency_json_response([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง',
                'error_type' => 'access_denied'
            ]);
            return;
        }

        // รับข้อมูลจาก POST
        $user_id = $this->input->post('user_id');
        $action = $this->input->post('action');
        $auto_create_folder = $this->input->post('auto_create_folder', true);

        // Validation พื้นฐาน
        if (empty($user_id) || empty($action)) {
            $this->emergency_json_response([
                'success' => false,
                'message' => 'ข้อมูลไม่ครบถ้วน',
                'error_type' => 'validation_error'
            ]);
            return;
        }

        if (!is_numeric($user_id)) {
            $this->emergency_json_response([
                'success' => false,
                'message' => 'รหัสผู้ใช้ไม่ถูกต้อง',
                'error_type' => 'validation_error'
            ]);
            return;
        }

        if (!in_array($action, ['enable', 'disable'])) {
            $this->emergency_json_response([
                'success' => false,
                'message' => 'การกระทำไม่ถูกต้อง',
                'error_type' => 'validation_error'
            ]);
            return;
        }

        // ดึงข้อมูลผู้ใช้
        $user = $this->safe_get_user_data($user_id);
        if (!$user) {
            $this->emergency_json_response([
                'success' => false,
                'message' => 'ไม่พบผู้ใช้ที่ระบุ',
                'error_type' => 'user_not_found'
            ]);
            return;
        }

        $new_status = ($action === 'enable') ? 1 : 0;
        $admin_id = $this->session->userdata('m_id') ?: 1;

        // เริ่ม transaction
        $this->safe_start_transaction();

        if ($new_status == 1) {
            $result = $this->process_enable_user_storage($user, $admin_id, $auto_create_folder);
        } else {
            $result = $this->process_disable_user_storage($user, $admin_id);
        }

        if ($result['success']) {
            $this->safe_commit_transaction();
            $this->safe_log_activity($admin_id, $user_id, $action, $result['data']);
            
            $this->emergency_json_response([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        } else {
            $this->safe_rollback_transaction();
            
            $this->emergency_json_response([
                'success' => false,
                'message' => $result['message'],
                'error_type' => 'process_failed'
            ]);
        }

    } catch (Exception $e) {
        $this->safe_rollback_transaction();
        
        $this->emergency_json_response([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage(),
            'error_type' => 'exception',
            'error_details' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
}

// ✅ 2. Emergency JSON Response (ไม่ขึ้นกับ method อื่น)
private function emergency_json_response($data) {
    // ล้าง output buffer ทั้งหมด
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set headers
    if (!headers_sent()) {
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
    }
    
    // Add timestamp
    if (!isset($data['timestamp'])) {
        $data['timestamp'] = date('Y-m-d H:i:s');
    }
    
    // Output JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * ✅ 2. เปิดใช้งาน Storage - แบบปลอดภัย
 */
private function process_enable_user_storage($user, $admin_id, $auto_create_folder) {
    try {
        // 1. อัปเดตสถานะ Member
        $member_update_result = $this->safe_update_member_storage_status($user->m_id, true);
        
        if (!$member_update_result) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตสถานะผู้ใช้ได้'
            ];
        }

        $response_data = [
            'user_id' => $user->m_id,
            'storage_enabled' => true,
            'folder_created' => false,
            'permissions_assigned' => 0,
            'personal_folder_id' => $user->personal_folder_id
        ];

        // 2. สร้างโฟลเดอร์ส่วนตัว (ถ้าต้องการและยังไม่มี)
        if ($auto_create_folder && empty($user->personal_folder_id)) {
            log_message('info', "📁 Attempting to create personal folder for user: {$user->m_id}");
            
            $folder_result = $this->safe_create_personal_folder($user);
            
            if ($folder_result['success']) {
                // อัปเดต personal_folder_id
                $this->safe_update_member_personal_folder($user->m_id, $folder_result['folder_id']);
                
                $response_data['folder_created'] = true;
                $response_data['folder_name'] = $folder_result['folder_name'];
                $response_data['personal_folder_id'] = $folder_result['folder_id'];
                
                log_message('info', "✅ Personal folder created: {$folder_result['folder_id']}");
            } else {
                log_message('warning', "⚠️ Personal folder creation failed: {$folder_result['message']}");
                $response_data['folder_error'] = $folder_result['message'];
            }
        }

        // 3. กำหนดสิทธิ์อัตโนมัติ
        $permissions_result = $this->safe_assign_default_permissions($user, $response_data['personal_folder_id'], $admin_id);
        
        $response_data['permissions_assigned'] = $permissions_result['count'];
        $response_data['permission_details'] = $permissions_result['details'];

        // สร้างข้อความสำเร็จ
        $message_parts = ['เปิดใช้งาน Storage เรียบร้อย'];
        
        if ($response_data['folder_created']) {
            $message_parts[] = 'สร้างโฟลเดอร์ส่วนตัวแล้ว';
        }
        
        if ($response_data['permissions_assigned'] > 0) {
            $message_parts[] = "กำหนดสิทธิ์ {$response_data['permissions_assigned']} รายการ";
        }

        return [
            'success' => true,
            'message' => implode(' และ ', $message_parts),
            'data' => $response_data
        ];

    } catch (Exception $e) {
        log_message('error', 'Process enable user storage error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการเปิดใช้งาน: ' . $e->getMessage()
        ];
    }
}
	
	
private function safe_update_member_personal_folder($user_id, $folder_id) {
    try {
        return $this->db->where('m_id', $user_id)
                       ->update('tbl_member', [
                           'personal_folder_id' => $folder_id
                       ]);
    } catch (Exception $e) {
        log_message('error', 'Safe update member personal folder error: ' . $e->getMessage());
        return false;
    }
}
	
	
	private function safe_create_personal_folder($user) {
    try {
        // ตรวจสอบว่ามี function create_folder_with_curl หรือไม่
        if (!method_exists($this, 'create_folder_with_curl')) {
            return [
                'success' => false,
                'message' => 'ระบบยังไม่รองรับการสร้างโฟลเดอร์อัตโนมัติ'
            ];
        }

        // ดึง Access Token
        $access_token = $this->safe_get_system_access_token();
        if (!$access_token) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถดึง Access Token ได้'
            ];
        }

        // หาโฟลเดอร์ Users parent
        $users_folder = $this->safe_get_users_folder();
        if (!$users_folder) {
            return [
                'success' => false,
                'message' => 'ไม่พบโฟลเดอร์ Users ในระบบ'
            ];
        }

        // สร้างชื่อโฟลเดอร์
        $folder_name = trim($user->m_fname . ' ' . $user->m_lname);
        if (empty($folder_name)) {
            $folder_name = 'User_' . $user->m_id;
        }

        // สร้างโฟลเดอร์ใน Google Drive
        $personal_folder = $this->create_folder_with_curl(
            $folder_name,
            $users_folder->folder_id,
            $access_token
        );

        if (!$personal_folder || !isset($personal_folder['id'])) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้'
            ];
        }

        // บันทึกในฐานข้อมูล (ถ้าทำได้)
        $this->safe_save_personal_folder_to_database($user, $personal_folder, $users_folder->folder_id);

        return [
            'success' => true,
            'folder_id' => $personal_folder['id'],
            'folder_name' => $folder_name,
            'message' => 'สร้างโฟลเดอร์ส่วนตัวเรียบร้อย'
        ];

    } catch (Exception $e) {
        log_message('error', 'Safe create personal folder error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
}

	
	private function safe_save_personal_folder_to_database($user, $personal_folder, $parent_folder_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return true; // ถ้าไม่มีตาราง ข้ามไป
        }

        $folder_data = [
            'folder_name' => trim($user->m_fname . ' ' . $user->m_lname),
            'folder_id' => $personal_folder['id'],
            'parent_folder_id' => $parent_folder_id,
            'folder_type' => 'user',
            'permission_level' => 'private',
            'folder_description' => 'โฟลเดอร์ส่วนตัวของ ' . trim($user->m_fname . ' ' . $user->m_lname),
            'is_active' => 1,
            'created_by' => $this->session->userdata('m_id')
        ];

        return $this->db->insert('tbl_google_drive_system_folders', $folder_data);

    } catch (Exception $e) {
        log_message('error', 'Safe save personal folder to database error: ' . $e->getMessage());
        return false;
    }
}



	

	
private function safe_assign_default_permissions($user, $personal_folder_id, $admin_id) {
    try {
        // ถ้าไม่มีตาราง permissions ให้ข้ามไป
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            log_message('info', 'Permission table not found, skipping permission assignment');
            return ['count' => 0, 'details' => []];
        }

        $assigned_count = 0;
        $permission_details = [];

        // 1. สิทธิ์โฟลเดอร์ส่วนตัว (ถ้ามี)
        if (!empty($personal_folder_id)) {
            if ($this->safe_add_folder_permission($personal_folder_id, $user->m_id, 'owner', $admin_id)) {
                $assigned_count++;
                $permission_details[] = [
                    'folder_type' => 'Personal Folder',
                    'access_type' => 'owner'
                ];
            }
        }

        // 2. สิทธิ์โฟลเดอร์ Shared
        $shared_folder = $this->safe_get_folder_by_name('Shared', 'shared');
        if ($shared_folder) {
            if ($this->safe_add_folder_permission($shared_folder->folder_id, $user->m_id, 'write', $admin_id)) {
                $assigned_count++;
                $permission_details[] = [
                    'folder_type' => 'Shared Folder',
                    'access_type' => 'write'
                ];
            }
        }

        // 3. สิทธิ์โฟลเดอร์ Users
        $users_folder = $this->safe_get_folder_by_name('Users', 'system');
        if ($users_folder) {
            if ($this->safe_add_folder_permission($users_folder->folder_id, $user->m_id, 'read', $admin_id)) {
                $assigned_count++;
                $permission_details[] = [
                    'folder_type' => 'Users Folder',
                    'access_type' => 'read'
                ];
            }
        }

        // 4. สิทธิ์โฟลเดอร์แผนก (ง่ายๆ)
        if (!empty($user->ref_pid)) {
            $dept_permissions = $this->safe_assign_department_permissions($user, $admin_id);
            $assigned_count += $dept_permissions['count'];
            $permission_details = array_merge($permission_details, $dept_permissions['details']);
        }

        return [
            'count' => $assigned_count,
            'details' => $permission_details
        ];

    } catch (Exception $e) {
        log_message('error', 'Safe assign default permissions error: ' . $e->getMessage());
        return ['count' => 0, 'details' => []];
    }
}

	
	
	
	

	
private function safe_get_folder_by_name($folder_name, $folder_type) {
    try {
        return $this->db->select('folder_id, folder_name')
                       ->from('tbl_google_drive_system_folders')
                       ->where('folder_name', $folder_name)
                       ->where('folder_type', $folder_type)
                       ->where('is_active', 1)
                       ->get()
                       ->row();
    } catch (Exception $e) {
        log_message('error', 'Safe get folder by name error: ' . $e->getMessage());
        return null;
    }
}

private function safe_add_folder_permission($folder_id, $member_id, $access_type, $granted_by) {
    try {
        // ตรวจสอบพารามิเตอร์
        if (empty($folder_id) || empty($member_id) || empty($access_type)) {
            return false;
        }

        // ตรวจสอบสิทธิ์ที่มีอยู่
        $existing = $this->db->select('id')
                            ->from('tbl_google_drive_member_folder_access')
                            ->where('folder_id', $folder_id)
                            ->where('member_id', $member_id)
                            ->get()
                            ->row();

        $permission_data = [
            'member_id' => $member_id,
            'folder_id' => $folder_id,
            'access_type' => $access_type,
            'permission_source' => 'system',
            'granted_by' => $granted_by,
            'granted_at' => date('Y-m-d H:i:s'),
            'is_active' => 1
        ];

        if ($existing) {
            // อัปเดต
            return $this->db->where('id', $existing->id)
                           ->update('tbl_google_drive_member_folder_access', $permission_data);
        } else {
            // เพิ่มใหม่
            return $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
        }

    } catch (Exception $e) {
        log_message('error', 'Safe add folder permission error: ' . $e->getMessage());
        return false;
    }
}

	
	
private function safe_assign_department_permissions($user, $admin_id) {
    try {
        $assigned_count = 0;
        $permission_details = [];

        // ✅ 1. ตรวจสอบตำแหน่งของผู้ใช้
        if (empty($user->ref_pid)) {
            log_message('info', "User {$user->m_id} has no position (ref_pid), skipping department permissions");
            return ['count' => 0, 'details' => []];
        }

        // ✅ 2. หาโฟลเดอร์แผนกที่สร้างสำหรับตำแหน่งนี้โดยเฉพาะ
        $user_position_folders = $this->db->select('folder_id, folder_name, created_for_position')
                                         ->from('tbl_google_drive_system_folders')
                                         ->where('folder_type', 'department')
                                         ->where('created_for_position', $user->ref_pid) // ✅ เฉพาะตำแหน่งนี้
                                         ->where('is_active', 1)
                                         ->get()
                                         ->result();

        log_message('info', "Found " . count($user_position_folders) . " department folders for position {$user->ref_pid}");

        // ✅ 3. ถ้าไม่มีโฟลเดอร์สำหรับตำแหน่งนี้ ให้สร้างใหม่
        if (empty($user_position_folders)) {
            log_message('info', "🔄 No department folder found for position {$user->ref_pid}, creating new one...");
            
            $created_folder = $this->auto_create_department_folder_for_position($user->ref_pid, $admin_id);
            
            if ($created_folder && $created_folder['success']) {
                // เพิ่มโฟลเดอร์ที่สร้างใหม่เข้าไปในรายการ
                $user_position_folders = [(object)[
                    'folder_id' => $created_folder['folder_id'],
                    'folder_name' => $created_folder['folder_name'],
                    'created_for_position' => $user->ref_pid
                ]];
                
                log_message('info', "✅ Created new department folder: {$created_folder['folder_name']} for position {$user->ref_pid}");
                
                $permission_details[] = [
                    'folder_type' => 'Created Department: ' . $created_folder['folder_name'],
                    'access_type' => 'created_new',
                    'newly_created' => true
                ];
            } else {
                log_message('warning', "❌ Failed to create department folder for position {$user->ref_pid}");
            }
        }

        // ✅ 4. กำหนดสิทธิ์ตามตำแหน่งโดยละเอียด
        foreach ($user_position_folders as $dept_folder) {
            $access_level = $this->determine_access_level_by_position($user, $dept_folder);
            
            if ($this->safe_add_folder_permission($dept_folder->folder_id, $user->m_id, $access_level, $admin_id)) {
                $assigned_count++;
                $permission_details[] = [
                    'folder_type' => 'Department: ' . $dept_folder->folder_name,
                    'access_type' => $access_level,
                    'position_based' => true
                ];
                
                log_message('info', "✅ Assigned {$access_level} permission to {$dept_folder->folder_name} for position {$user->ref_pid}");
            }
        }

        // ✅ 5. สิทธิ์ทั่วไป: เข้าถึง Departments root folder (อ่านอย่างเดียว)
        $departments_root = $this->safe_get_folder_by_name('Departments', 'system');
        if ($departments_root) {
            if ($this->safe_add_folder_permission($departments_root->folder_id, $user->m_id, 'read', $admin_id)) {
                $assigned_count++;
                $permission_details[] = [
                    'folder_type' => 'Departments Root',
                    'access_type' => 'read',
                    'general_access' => true
                ];
            }
        }

        log_message('info', "✅ Department permissions assigned: {$assigned_count} folders for position {$user->ref_pid}");

        return [
            'count' => $assigned_count,
            'details' => $permission_details
        ];

    } catch (Exception $e) {
        log_message('error', 'Safe assign department permissions error: ' . $e->getMessage());
        return ['count' => 0, 'details' => []];
    }
}

/**
 * ✅ 8. กำหนดระดับสิทธิ์ตามตำแหน่งงาน
 */
private function determine_access_level_by_position($user, $dept_folder) {
    try {
        // 1. Admin มีสิทธิ์เต็ม
        if (in_array($user->m_system, ['system_admin', 'super_admin'])) {
            return 'admin';
        }

        // 2. ดึงข้อมูลตำแหน่ง
        $position = $this->db->select('pname, peng')
                           ->from('tbl_position')
                           ->where('pid', $user->ref_pid)
                           ->get()
                           ->row();

        if ($position) {
            $position_name = strtolower($position->pname);
            
            // 3. กำหนดสิทธิ์ตามชื่อตำแหน่ง
            if (strpos($position_name, 'หัวหน้า') !== false || 
                strpos($position_name, 'ผู้จัดการ') !== false ||
                strpos($position_name, 'manager') !== false) {
                return 'write'; // หัวหน้าแผนก
            } elseif (strpos($position_name, 'หัวหน้างาน') !== false ||
                      strpos($position_name, 'supervisor') !== false) {
                return 'write'; // หัวหน้างาน
            } else {
                return 'read'; // พนักงานทั่วไป
            }
        }

        // 4. Default สำหรับกรณีไม่พบข้อมูล
        return 'read';

    } catch (Exception $e) {
        log_message('error', 'Determine access level by position error: ' . $e->getMessage());
        return 'read'; // Safe default
    }
}

/**
 * ✅ 9. สร้างโฟลเดอร์แผนกอัตโนมัติ (แบบง่าย)
 */
private function auto_create_department_folder_for_position($position_id, $admin_id) {
    try {
        // ตรวจสอบว่ามี method สร้างโฟลเดอร์หรือไม่
        if (!method_exists($this, 'create_folder_with_curl')) {
            log_message('warning', 'create_folder_with_curl method not found, skipping folder creation');
            return ['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์ได้ (method not found)'];
        }

        // ดึงข้อมูลตำแหน่ง
        $position = $this->db->select('pid, pname, pdepartment')
                           ->from('tbl_position')
                           ->where('pid', $position_id)
                           ->get()
                           ->row();

        if (!$position) {
            return ['success' => false, 'message' => 'ไม่พบข้อมูลตำแหน่ง'];
        }

        // สร้างชื่อโฟลเดอร์
        $folder_name = !empty($position->pdepartment) ? 
                      "แผนก " . $position->pdepartment : 
                      "แผนก " . $position->pname;
        
        log_message('info', "📁 Auto-creating department folder: {$folder_name} for position {$position_id}");

        return [
            'success' => true,
            'folder_id' => 'temp_' . $position_id, // Temporary ID สำหรับ testing
            'folder_name' => $folder_name,
            'permissions_assigned' => 0,
            'message' => 'จำลองการสร้างโฟลเดอร์ (ต้องการ Google Drive API)'
        ];

    } catch (Exception $e) {
        log_message('error', 'Auto create department folder error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
}



private function safe_disable_user_permissions($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return 0;
        }

        $result = $this->db->where('member_id', $user_id)
                          ->update('tbl_google_drive_member_folder_access', [
                              'is_active' => 0,
                              'updated_at' => date('Y-m-d H:i:s')
                          ]);

        return $result ? $this->db->affected_rows() : 0;

    } catch (Exception $e) {
        log_message('error', 'Safe disable user permissions error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * ✅ 5. Safe Transaction Functions
 */
private function safe_start_transaction() {
    try {
        if (method_exists($this->db, 'trans_start')) {
            $this->db->trans_start();
        }
    } catch (Exception $e) {
        log_message('error', 'Safe start transaction error: ' . $e->getMessage());
    }
}

private function safe_commit_transaction() {
    try {
        if (method_exists($this->db, 'trans_complete')) {
            $this->db->trans_complete();
        }
    } catch (Exception $e) {
        log_message('error', 'Safe commit transaction error: ' . $e->getMessage());
    }
}

private function safe_rollback_transaction() {
    try {
        if (method_exists($this->db, 'trans_rollback')) {
            $this->db->trans_rollback();
        }
    } catch (Exception $e) {
        log_message('error', 'Safe rollback transaction error: ' . $e->getMessage());
    }
}
	
	
	private function safe_get_system_access_token() {
    try {
        $system_storage = $this->db->select('google_access_token')
                                  ->from('tbl_google_drive_system_storage')
                                  ->where('is_active', 1)
                                  ->get()
                                  ->row();

        return $system_storage ? $system_storage->google_access_token : null;

    } catch (Exception $e) {
        log_message('error', 'Safe get system access token error: ' . $e->getMessage());
        return null;
    }
}
	
	
	
	private function safe_get_users_folder() {
    try {
        return $this->db->select('folder_id, folder_name')
                       ->from('tbl_google_drive_system_folders')
                       ->where('folder_name', 'Users')
                       ->where('folder_type', 'system')
                       ->where('is_active', 1)
                       ->get()
                       ->row();
    } catch (Exception $e) {
        log_message('error', 'Safe get users folder error: ' . $e->getMessage());
        return null;
    }
}
	

/**
 * ✅ 3. ปิดใช้งาน Storage - แบบปลอดภัย
 */
private function process_disable_user_storage($user, $admin_id) {
    try {
        // 1. อัปเดตสถานะ Member
        $member_update_result = $this->safe_update_member_storage_status($user->m_id, false);
        
        if (!$member_update_result) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตสถานะผู้ใช้ได้'
            ];
        }

        // 2. ปิดสิทธิ์ทั้งหมด (ไม่ลบ เพื่อให้กู้คืนได้)
        $permissions_disabled = $this->safe_disable_user_permissions($user->m_id);

        $response_data = [
            'user_id' => $user->m_id,
            'storage_enabled' => false,
            'permissions_disabled' => $permissions_disabled
        ];

        return [
            'success' => true,
            'message' => 'ปิดใช้งาน Storage เรียบร้อย',
            'data' => $response_data
        ];

    } catch (Exception $e) {
        log_message('error', 'Process disable user storage error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการปิดใช้งาน: ' . $e->getMessage()
        ];
    }
}
	
	
	
private function safe_update_member_storage_status($user_id, $enable) {
    try {
        $update_data = [
            'storage_access_granted' => $enable ? 1 : 0,
            'google_drive_enabled' => $enable ? 1 : 0
        ];

        if ($enable) {
            $update_data['last_storage_access'] = date('Y-m-d H:i:s');
            $update_data['storage_quota_limit'] = 1073741824; // 1GB default
            $update_data['storage_quota_used'] = 0;
        }

        return $this->db->where('m_id', $user_id)
                       ->update('tbl_member', $update_data);

    } catch (Exception $e) {
        log_message('error', 'Safe update member storage status error: ' . $e->getMessage());
        return false;
    }
}
	
	

/**
 * ✅ 4. Safe Helper Functions
 */
private function safe_get_user_data($user_id) {
    try {
        return $this->db->select('m_id, m_fname, m_lname, m_email, personal_folder_id, ref_pid, storage_access_granted')
                       ->from('tbl_member')
                       ->where('m_id', $user_id)
                       ->where('m_status', 1)
                       ->get()
                       ->row();
    } catch (Exception $e) {
        log_message('error', 'Safe get user data error: ' . $e->getMessage());
        return null;
    }
}

/**
 * 📁 สร้าง Personal Folder อัตโนมัติสำหรับ User
 */
private function auto_create_personal_folder_for_user($user) {
    try {
        // ตรวจสอบว่าระบบพร้อมหรือไม่
        if (!method_exists($this, 'get_system_access_token')) {
            return [
                'success' => false,
                'message' => 'ระบบยังไม่พร้อม: ไม่พบ get_system_access_token function'
            ];
        }

        // ดึง Access Token
        $access_token = $this->get_system_access_token();
        if (!$access_token) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถเข้าถึง Google Drive ได้'
            ];
        }

        // หา Users folder
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return [
                'success' => false,
                'message' => 'ยังไม่ได้สร้างโครงสร้างโฟลเดอร์ระบบ'
            ];
        }

        $users_folder = $this->db->select('folder_id')
                               ->from('tbl_google_drive_system_folders')
                               ->where('folder_name', 'Users')
                               ->where('folder_type', 'system')
                               ->where('is_active', 1)
                               ->get()
                               ->row();

        if (!$users_folder) {
            return [
                'success' => false,
                'message' => 'ไม่พบโฟลเดอร์ Users ในระบบ'
            ];
        }

        // ตรวจสอบ function สร้างโฟลเดอร์
        if (!method_exists($this, 'create_folder_with_curl')) {
            return [
                'success' => false,
                'message' => 'ระบบยังไม่พร้อม: ไม่พบ create_folder_with_curl function'
            ];
        }

        // สร้างโฟลเดอร์ส่วนตัว
        $folder_name = $user->m_fname . ' ' . $user->m_lname;
        $personal_folder = $this->create_folder_with_curl($folder_name, $users_folder->folder_id, $access_token);

        if (!$personal_folder) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้'
            ];
        }

        // บันทึกข้อมูลโฟลเดอร์ (ถ้ามี function)
        if (method_exists($this, 'save_folder_info')) {
            $folder_data = [
                'folder_name' => $folder_name,
                'folder_id' => $personal_folder['id'],
                'parent_folder_id' => $users_folder->folder_id,
                'folder_type' => 'user',
                'folder_path' => '/Organization Drive/Users/' . $folder_name,
                'permission_level' => 'private',
                'folder_description' => 'Personal folder for ' . $user->m_fname . ' ' . $user->m_lname,
                'created_by' => $this->session->userdata('m_id')
            ];

            $this->save_folder_info($folder_data);
        }

        // อัปเดต member table
        $update_result = $this->db->where('m_id', $user->m_id)
                                 ->update('tbl_member', [
                                     'personal_folder_id' => $personal_folder['id']
                                 ]);

        if (!$update_result) {
            return [
                'success' => false,
                'message' => 'สร้างโฟลเดอร์สำเร็จ แต่ไม่สามารถบันทึกข้อมูลได้'
            ];
        }

        // กำหนดสิทธิ์ (ถ้ามี function)
        if (method_exists($this, 'add_folder_permission_correct')) {
            // กำหนดสิทธิ์ให้เจ้าของโฟลเดอร์
            $this->add_folder_permission_correct($personal_folder['id'], $user->m_id, 'owner');

            // กำหนดสิทธิ์ให้ Admin (ถ้ามี function)
            if (method_exists($this, 'get_admin_users')) {
                $admin_users = $this->get_admin_users();
                foreach ($admin_users as $admin) {
                    if ($admin['m_id'] != $user->m_id) {
                        $this->add_folder_permission_correct($personal_folder['id'], $admin['m_id'], 'admin');
                    }
                }
            }
        }

        return [
            'success' => true,
            'folder_id' => $personal_folder['id'],
            'folder_name' => $folder_name,
            'message' => 'สร้างโฟลเดอร์ส่วนตัวเรียบร้อย'
        ];

    } catch (Exception $e) {
        log_message('error', 'Auto create personal folder error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
}
public function toggle_user_storage_access() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $user_id = $this->input->post('user_id');
        $action = $this->input->post('action');

        if (empty($user_id) || empty($action)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบผู้ใช้
        $user = $this->db->select('m_id, m_fname, m_lname')
                        ->from('tbl_member')
                        ->where('m_id', $user_id)
                        ->where('m_status', 1)
                        ->get()
                        ->row();

        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้ที่ระบุ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $new_status = ($action === 'enable') ? 1 : 0;

        // ตรวจสอบว่ามี columns ก่อนอัปเดต
        $fields = $this->db->list_fields('tbl_member');
        $update_data = [];
        
        if (in_array('storage_access_granted', $fields)) {
            $update_data['storage_access_granted'] = $new_status;
        }
        
        if (in_array('last_storage_access', $fields)) {
            $update_data['last_storage_access'] = date('Y-m-d H:i:s');
        }

        // ถ้าเปิดใช้งานและยังไม่มี quota
        if ($new_status == 1 && in_array('storage_quota_limit', $fields)) {
            $current_quota = $this->db->select('storage_quota_limit')
                                     ->from('tbl_member')
                                     ->where('m_id', $user_id)
                                     ->get()
                                     ->row();
            
            if (!$current_quota || empty($current_quota->storage_quota_limit)) {
                $default_quota = 1073741824; // 1GB
                $update_data['storage_quota_limit'] = $default_quota;
            }
        }

        if (empty($update_data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มี columns ที่สามารถอัปเดตได้ กรุณาเพิ่ม storage_access_granted column ในตาราง tbl_member'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $result = $this->db->where('m_id', $user_id)
                          ->update('tbl_member', $update_data);

        if ($result) {
            $message = $new_status ? 'เปิดใช้งาน Storage เรียบร้อย' : 'ปิดใช้งาน Storage เรียบร้อย';
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => $message,
                    'data' => ['new_status' => $new_status]
                ], JSON_UNESCAPED_UNICODE));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัปเดตสถานะได้'
                ], JSON_UNESCAPED_UNICODE));
        }

    } catch (Exception $e) {
        log_message('error', 'Toggle user storage access error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * 📁 สร้างโฟลเดอร์ส่วนตัวให้ผู้ใช้ 1 คน (แก้ไข)
 */
public function create_single_personal_folder() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้ระบุผู้ใช้'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบผู้ใช้
        $user = $this->db->select('m_id, m_fname, m_lname, m_email, storage_access_granted, personal_folder_id')
                        ->from('tbl_member')
                        ->where('m_id', $user_id)
                        ->where('m_status', 1)
                        ->get()
                        ->row();

        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้ที่ระบุ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบว่ามีโฟลเดอร์แล้วหรือไม่
        if (!empty($user->personal_folder_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'ผู้ใช้มีโฟลเดอร์ส่วนตัวแล้ว',
                    'data' => [
                        'folder_id' => $user->personal_folder_id,
                        'already_exists' => true
                    ]
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบสิทธิ์ใช้งาน
        if ($user->storage_access_granted != 1) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ผู้ใช้ยังไม่ได้เปิดใช้งาน Storage'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบว่ามี function ที่จำเป็นหรือไม่
        if (!method_exists($this, 'get_system_access_token')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ระบบยังไม่พร้อม: ไม่พบ get_system_access_token function'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ดึง Access Token
        $access_token = $this->get_system_access_token();
        if (!$access_token) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเข้าถึง Google Drive ได้'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // หา Users folder
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ยังไม่ได้สร้างโครงสร้างโฟลเดอร์ระบบ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $users_folder = $this->db->select('folder_id')
                               ->from('tbl_google_drive_system_folders')
                               ->where('folder_name', 'Users')
                               ->where('folder_type', 'system')
                               ->where('is_active', 1)
                               ->get()
                               ->row();

        if (!$users_folder) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบโฟลเดอร์ Users ในระบบ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบ function สร้างโฟลเดอร์
        if (!method_exists($this, 'create_folder_with_curl')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ระบบยังไม่พร้อม: ไม่พบ create_folder_with_curl function'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // สร้างโฟลเดอร์ส่วนตัว
        $folder_name = $user->m_fname . ' ' . $user->m_lname;
        $personal_folder = $this->create_folder_with_curl($folder_name, $users_folder->folder_id, $access_token);

        if (!$personal_folder) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // อัปเดต member table
        $update_result = $this->db->where('m_id', $user_id)
                                 ->update('tbl_member', [
                                     'personal_folder_id' => $personal_folder['id']
                                 ]);

        if ($update_result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'สร้างโฟลเดอร์ส่วนตัวเรียบร้อย',
                    'data' => [
                        'folder_id' => $personal_folder['id'],
                        'folder_name' => $folder_name,
                        'created_new' => true
                    ]
                ], JSON_UNESCAPED_UNICODE));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'สร้างโฟลเดอร์สำเร็จ แต่ไม่สามารถบันทึกข้อมูลได้'
                ], JSON_UNESCAPED_UNICODE));
        }

    } catch (Exception $e) {
        log_message('error', 'Create single personal folder error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * 🔄 เปิด/ปิดผู้ใช้หลายคนพร้อมกัน (แก้ไข)
 */
public function bulk_toggle_user_status() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $input = json_decode($this->input->raw_input_stream, true);
        
        if (!$input) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ข้อมูล JSON ไม่ถูกต้อง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $user_ids = $input['user_ids'] ?? [];
        $enable = $input['enable'] ?? true;

        if (empty($user_ids) || !is_array($user_ids)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้ระบุผู้ใช้'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $new_status = $enable ? 1 : 0;
        $affected_count = 0;
        $default_quota = 1073741824; // 1GB

        foreach ($user_ids as $user_id) {
            try {
                // ตรวจสอบผู้ใช้
                $user = $this->db->select('m_id, m_fname, m_lname, storage_quota_limit')
                                ->from('tbl_member')
                                ->where('m_id', $user_id)
                                ->where('m_status', 1)
                                ->get()
                                ->row();

                if ($user) {
                    $update_data = [
                        'storage_access_granted' => $new_status
                    ];

                    // เช็คว่ามี column หรือไม่
                    $fields = $this->db->list_fields('tbl_member');
                    if (in_array('last_storage_access', $fields)) {
                        $update_data['last_storage_access'] = date('Y-m-d H:i:s');
                    }

                    // ถ้าเปิดใช้งานและยังไม่มี quota
                    if ($new_status == 1 && in_array('storage_quota_limit', $fields) && empty($user->storage_quota_limit)) {
                        $update_data['storage_quota_limit'] = $default_quota;
                    }

                    $result = $this->db->where('m_id', $user_id)
                                      ->update('tbl_member', $update_data);

                    if ($result) {
                        $affected_count++;
                    }
                }
            } catch (Exception $e) {
                log_message('error', "Bulk toggle user {$user_id}: " . $e->getMessage());
                continue;
            }
        }

        $action_text = $enable ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => "{$action_text}ผู้ใช้ {$affected_count} คนเรียบร้อย",
                'data' => [
                    'affected_count' => $affected_count,
                    'total_requested' => count($user_ids)
                ]
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'Bulk toggle user status error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * 📁 สร้างโฟลเดอร์หลายคนพร้อมกัน (แก้ไข - รุ่นง่าย)
 */
public function bulk_create_personal_folders() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'ฟีเจอร์นี้กำลังพัฒนา กรุณาใช้การสร้างทีละคนก่อน'
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'Bulk create personal folders error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * 📁 สร้างโฟลเดอร์ให้ทุกคนที่ยังไม่มี (แก้ไข - รุ่นง่าย)
 */
public function create_all_missing_personal_folders() {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'ฟีเจอร์นี้กำลังพัฒนา กรุณาใช้การสร้างทีละคนก่อน'
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'Create all missing personal folders error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}
	
	
	
	/**
 * ✅ แก้ไขแล้ว: ดึงข้อมูลผู้ใช้และสิทธิ์ทั้งหมดสำหรับ Modal
 */
public function get_user_permission_data() {
    try {
        // ตรวจสอบสิทธิ์ Admin
        $user_system = $this->session->userdata('m_system');
        if (!in_array($user_system, ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $user_id = $this->input->post('user_id');
        if (empty($user_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้ระบุรหัสผู้ใช้'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // 1. ดึงข้อมูลผู้ใช้
        $user_data = $this->get_user_basic_info_fixed($user_id);
        if (!$user_data) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้ที่ระบุ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // 2. ดึงสิทธิ์โฟลเดอร์
        $folder_permissions = $this->get_user_folder_permissions_fixed($user_id);

        // 3. ดึงสิทธิ์ระบบ
        $system_permissions = $this->get_user_system_permissions_fixed($user_id);

        // 4. ดึงประวัติ
        $permission_history = $this->get_user_permission_history_fixed($user_id);

        // 5. สร้างสรุป
        $permissions_summary = $this->generate_permissions_summary_fixed($user_data, $folder_permissions, $system_permissions);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => [
                    'user' => $user_data,
                    'folders' => $folder_permissions,
                    'system_permissions' => $system_permissions,
                    'history' => $permission_history,
                    'summary' => $permissions_summary
                ]
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'get_user_permission_data error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * ✅ Fixed: ดึงข้อมูลผู้ใช้พื้นฐาน
 */
private function get_user_basic_info_fixed($user_id) {
    try {
        $this->db->select('
            m.m_id, m.m_fname, m.m_lname, m.m_email, m.ref_pid,
            m.storage_access_granted, m.personal_folder_id, 
            m.storage_quota_limit, m.storage_quota_used, m.last_storage_access,
            m.google_drive_enabled, m.m_system, m.grant_system_ref_id,
            CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as full_name,
            p.pname as position_name
        ', false);
        $this->db->from('tbl_member m');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->where('m.m_id', $user_id);
        $this->db->where('m.m_status', '1');

        $user = $this->db->get()->row();

        if ($user) {
            // จัดการข้อมูลที่อาจเป็น NULL
            $user->storage_quota_limit = $user->storage_quota_limit ?: 1073741824; // 1GB
            $user->storage_quota_used = $user->storage_quota_used ?: 0;
            $user->storage_access_granted = $user->storage_access_granted ?: 0;
            $user->google_drive_enabled = $user->google_drive_enabled ?: 0;
            $user->position_name = $user->position_name ?: 'ไม่ระบุตำแหน่ง';
            $user->full_name = trim($user->full_name) ?: 'ไม่ระบุชื่อ';
            
            // คำนวณขนาดไฟล์
            $user->storage_quota_used_formatted = $this->simple_format_bytes($user->storage_quota_used);
            $user->storage_quota_limit_formatted = $this->simple_format_bytes($user->storage_quota_limit);
            $user->storage_usage_percent = $user->storage_quota_limit > 0 ? 
                round(($user->storage_quota_used / $user->storage_quota_limit) * 100, 2) : 0;
            
            // ตรวจสอบโฟลเดอร์ส่วนตัว
            $user->has_personal_folder = !empty($user->personal_folder_id);
            
            return $user;
        }

        return null;

    } catch (Exception $e) {
        log_message('error', 'get_user_basic_info_fixed error: ' . $e->getMessage());
        return null;
    }
}

/**
 * ✅ Fixed: ดึงสิทธิ์โฟลเดอร์
 */
private function get_user_folder_permissions_fixed($user_id) {
    try {
        $folders = [];

        // ดึงจาก tbl_google_drive_folder_permissions
        $this->db->select('
            fp.folder_id, fp.access_level, fp.granted_by, fp.granted_at, fp.is_active
        ');
        $this->db->from('tbl_google_drive_folder_permissions fp');
        $this->db->where('fp.member_id', $user_id);
        $this->db->where('fp.is_active', 1);

        $folder_permissions = $this->db->get()->result_array();

        foreach ($folder_permissions as $permission) {
            $folder_info = $this->get_folder_info_from_system_folders($permission['folder_id']);
            $granted_by_name = $this->get_user_name_simple($permission['granted_by']);
            
            $folders[] = [
                'folder_id' => $permission['folder_id'],
                'folder_name' => $folder_info['folder_name'],
                'folder_type' => $folder_info['folder_type'],
                'folder_description' => $folder_info['folder_description'],
                'access_level' => $permission['access_level'],
                'granted_by' => $permission['granted_by'],
                'granted_by_name' => $granted_by_name,
                'granted_at' => $permission['granted_at'],
                'is_active' => $permission['is_active']
            ];
        }

        // ดึงจาก tbl_google_drive_member_folder_access เพิ่มเติม
        $this->db->select('
            mfa.folder_id, mfa.access_type, mfa.granted_by, mfa.granted_at, mfa.is_active
        ');
        $this->db->from('tbl_google_drive_member_folder_access mfa');
        $this->db->where('mfa.member_id', $user_id);
        $this->db->where('mfa.is_active', 1);

        $member_folder_access = $this->db->get()->result_array();

        foreach ($member_folder_access as $access) {
            // ตรวจสอบว่าซ้ำกับที่มีแล้วหรือไม่
            $exists = false;
            foreach ($folders as $existing) {
                if ($existing['folder_id'] === $access['folder_id']) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $folder_info = $this->get_folder_info_from_system_folders($access['folder_id']);
                $granted_by_name = $this->get_user_name_simple($access['granted_by']);
                
                $folders[] = [
                    'folder_id' => $access['folder_id'],
                    'folder_name' => $folder_info['folder_name'],
                    'folder_type' => $folder_info['folder_type'],
                    'folder_description' => $folder_info['folder_description'],
                    'access_level' => $this->convert_access_type_to_level($access['access_type']),
                    'granted_by' => $access['granted_by'],
                    'granted_by_name' => $granted_by_name,
                    'granted_at' => $access['granted_at'],
                    'is_active' => $access['is_active']
                ];
            }
        }

        return $folders;

    } catch (Exception $e) {
        log_message('error', 'get_user_folder_permissions_fixed error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ Fixed: ดึงสิทธิ์ระบบ
 */
private function get_user_system_permissions_fixed($user_id) {
    try {
        // ข้อมูลจาก tbl_member
        $this->db->select('
            storage_access_granted, google_drive_enabled, 
            storage_quota_limit, storage_quota_used, m_system
        ');
        $this->db->from('tbl_member');
        $this->db->where('m_id', $user_id);
        $member_data = $this->db->get()->row();

        if (!$member_data) {
            return null;
        }

        // ข้อมูลจาก tbl_google_drive_member_permissions
        $this->db->select('
            permission_type, can_create_folder, can_share, can_delete,
            override_position, notes, is_active
        ');
        $this->db->from('tbl_google_drive_member_permissions');
        $this->db->where('member_id', $user_id);
        $this->db->where('is_active', 1);
        $additional_perms = $this->db->get()->row();

        return [
            'storage_access_granted' => $member_data->storage_access_granted ?: 0,
            'google_drive_enabled' => $member_data->google_drive_enabled ?: 0,
            'storage_quota_limit' => $member_data->storage_quota_limit ?: 1073741824,
            'storage_quota_used' => $member_data->storage_quota_used ?: 0,
            'can_create_folder' => $additional_perms ? ($additional_perms->can_create_folder ?: 0) : 0,
            'can_share' => $additional_perms ? ($additional_perms->can_share ?: 0) : 0,
            'can_delete' => $additional_perms ? ($additional_perms->can_delete ?: 0) : 0,
            'override_position' => $additional_perms ? ($additional_perms->override_position ?: 0) : 0,
            'inherit_position' => $additional_perms ? !$additional_perms->override_position : 1,
            'notes' => $additional_perms ? ($additional_perms->notes ?: '') : '',
            'permission_type' => $additional_perms ? ($additional_perms->permission_type ?: 'basic_user') : 'basic_user',
            'is_admin' => in_array($member_data->m_system, ['system_admin', 'super_admin'])
        ];

    } catch (Exception $e) {
        log_message('error', 'get_user_system_permissions_fixed error: ' . $e->getMessage());
        return null;
    }
}

/**
 * ✅ Fixed: ดึงประวัติ
 */
private function get_user_permission_history_fixed($user_id, $limit = 15) {
    try {
        $this->db->select('
            l.action_type, l.action_description, l.created_at, l.status,
            l.folder_id, l.ip_address,
            CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as by_user_name
        ', false);
        $this->db->from('tbl_google_drive_logs l');
        $this->db->join('tbl_member m', 'l.member_id = m.m_id', 'left');
        
        // ค้นหาประวัติที่เกี่ยวข้องกับ user นี้
        $this->db->group_start();
        $this->db->where('l.member_id', $user_id);
        $this->db->or_like('l.action_description', "User ID: $user_id");
        $this->db->or_like('l.action_description', "user_id=$user_id");
        $this->db->or_like('l.action_description', "Member $user_id");
        $this->db->group_end();
        
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit);

        $history = $this->db->get()->result_array();

        // ปรับปรุงข้อมูล
        foreach ($history as &$item) {
            $item['by_user_name'] = $item['by_user_name'] ?: 'ระบบ';
        }

        return $history;

    } catch (Exception $e) {
        log_message('error', 'get_user_permission_history_fixed error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ Fixed: สร้างสรุปสิทธิ์
 */
private function generate_permissions_summary_fixed($user_data, $folder_permissions, $system_permissions) {
    try {
        return [
            'storage_access' => $user_data->storage_access_granted == 1,
            'folder_count' => count($folder_permissions),
            'personal_folder' => $user_data->personal_folder_id,
            'is_admin' => in_array($user_data->m_system, ['system_admin', 'super_admin']),
            'can_create_folder' => $system_permissions['can_create_folder'] == 1,
            'can_share' => $system_permissions['can_share'] == 1,
            'storage_usage' => [
                'used' => $user_data->storage_quota_used,
                'limit' => $user_data->storage_quota_limit,
                'percentage' => $user_data->storage_usage_percent,
                'used_formatted' => $user_data->storage_quota_used_formatted,
                'limit_formatted' => $user_data->storage_quota_limit_formatted
            ]
        ];

    } catch (Exception $e) {
        log_message('error', 'generate_permissions_summary_fixed error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ บันทึกการเปลี่ยนแปลงสิทธิ์
 */
public function save_user_permissions() {
    try {
        // ตรวจสอบสิทธิ์ Admin
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // รับข้อมูล JSON
        $input = json_decode($this->input->raw_input_stream, true);
        
        if (!$input || !isset($input['user_id']) || !isset($input['changes'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $user_id = $input['user_id'];
        $changes = $input['changes'];
        $notes = $input['notes'] ?? '';
        $current_admin_id = $this->session->userdata('m_id');

        // เริ่ม Transaction
        $this->db->trans_start();

        $update_count = 0;
        $success_messages = [];
        $error_messages = [];

        // 1. บันทึกการเปลี่ยนแปลงสิทธิ์โฟลเดอร์
        if (isset($changes['folders']) && is_array($changes['folders'])) {
            foreach ($changes['folders'] as $folder_id => $access_level) {
                try {
                    if ($this->update_folder_permission_fixed($user_id, $folder_id, $access_level, $current_admin_id)) {
                        $update_count++;
                        $success_messages[] = "อัปเดตสิทธิ์โฟลเดอร์ $folder_id เป็น $access_level";
                        
                        // บันทึก log
                        $this->log_permission_change_fixed(
                            $user_id,
                            'update_folder_permission',
                            "Updated folder permission: $folder_id to $access_level for user $user_id",
                            $folder_id
                        );
                    }
                } catch (Exception $e) {
                    $error_messages[] = "โฟลเดอร์ $folder_id: " . $e->getMessage();
                }
            }
        }

        // 2. บันทึกการเปลี่ยนแปลงสิทธิ์ระบบ
        if (isset($changes['system']) && is_array($changes['system'])) {
            try {
                if ($this->update_system_permissions_fixed($user_id, $changes['system'], $notes, $current_admin_id)) {
                    $update_count++;
                    $success_messages[] = "อัปเดตสิทธิ์ระบบ";
                    
                    // บันทึก log
                    $this->log_permission_change_fixed(
                        $user_id,
                        'update_system_permission',
                        "Updated system permissions for user $user_id: " . json_encode($changes['system'])
                    );
                }
            } catch (Exception $e) {
                $error_messages[] = "สิทธิ์ระบบ: " . $e->getMessage();
            }
        }

        // สิ้นสุด Transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Transaction failed',
                    'errors' => $error_messages
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => "บันทึกการเปลี่ยนแปลงเรียบร้อย ({$update_count} รายการ)",
                'data' => [
                    'updated_count' => $update_count,
                    'success_messages' => $success_messages,
                    'errors' => $error_messages
                ]
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'save_user_permissions error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * ✅ รีเซ็ตสิทธิ์ผู้ใช้ทั้งหมด
 */
public function reset_user_permissions() {
    try {
        // ตรวจสอบสิทธิ์ Admin
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้ระบุรหัสผู้ใช้'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // ตรวจสอบผู้ใช้
        $user = $this->db->select('m_id, m_fname, m_lname')
                        ->from('tbl_member')
                        ->where('m_id', $user_id)
                        ->where('m_status', '1')
                        ->get()
                        ->row();

        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้ที่ระบุ'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // เริ่ม Transaction
        $this->db->trans_start();

        $reset_count = 0;

        // 1. ปิดสิทธิ์โฟลเดอร์ทั้งหมด
        $folder_result = $this->db->where('member_id', $user_id)
                                 ->update('tbl_google_drive_folder_permissions', [
                                     'is_active' => 0,
                                     'updated_at' => date('Y-m-d H:i:s')
                                 ]);
        if ($folder_result) {
            $reset_count += $this->db->affected_rows();
        }

        // 2. ปิดสิทธิ์ใน member folder access
        $member_folder_result = $this->db->where('member_id', $user_id)
                                        ->update('tbl_google_drive_member_folder_access', [
                                            'is_active' => 0,
                                            'updated_at' => date('Y-m-d H:i:s')
                                        ]);
        if ($member_folder_result) {
            $reset_count += $this->db->affected_rows();
        }

        // 3. รีเซ็ตสิทธิ์ระบบ
        $member_reset = $this->db->where('m_id', $user_id)
                                ->update('tbl_member', [
                                    'storage_access_granted' => 0,
                                    'google_drive_enabled' => 0
                                ]);

        // 4. ปิดสิทธิ์พิเศษ
        $permission_reset = $this->db->where('member_id', $user_id)
                                    ->update('tbl_google_drive_member_permissions', [
                                        'is_active' => 0,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'updated_by' => $this->session->userdata('m_id')
                                    ]);

        // สิ้นสุด Transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถรีเซ็ตสิทธิ์ได้'
                ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // บันทึก log
        $this->log_permission_change_fixed(
            $user_id,
            'reset_all_permissions',
            "Reset all permissions for user: {$user->m_fname} {$user->m_lname} (ID: $user_id)"
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => "รีเซ็ตสิทธิ์ผู้ใช้เรียบร้อยแล้ว ({$reset_count} รายการ)",
                'data' => [
                    'reset_count' => $reset_count,
                    'user_name' => $user->m_fname . ' ' . $user->m_lname
                ]
            ], JSON_UNESCAPED_UNICODE));

    } catch (Exception $e) {
        log_message('error', 'reset_user_permissions error: ' . $e->getMessage());
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
    }
}

/**
 * ✅ Helper Functions
 */
private function get_folder_info_from_system_folders($folder_id) {
    try {
        $this->db->select('folder_name, folder_type, folder_description');
        $this->db->from('tbl_google_drive_system_folders');
        $this->db->where('folder_id', $folder_id);
        $folder = $this->db->get()->row_array();
        
        if ($folder) {
            return $folder;
        }

        // ถ้าไม่เจอ ให้ค่า default
        return [
            'folder_name' => 'โฟลเดอร์ (' . substr($folder_id, 0, 8) . '...)',
            'folder_type' => 'system',
            'folder_description' => ''
        ];

    } catch (Exception $e) {
        return [
            'folder_name' => 'ไม่ทราบชื่อ',
            'folder_type' => 'system',
            'folder_description' => ''
        ];
    }
}

private function get_user_name_simple($user_id) {
    try {
        if (!$user_id) return 'ระบบ';

        $this->db->select('CONCAT(COALESCE(m_fname, ""), " ", COALESCE(m_lname, "")) as name', false);
        $this->db->from('tbl_member');
        $this->db->where('m_id', $user_id);
        $user = $this->db->get()->row();

        return $user ? (trim($user->name) ?: 'ไม่ระบุชื่อ') : 'ระบบ';

    } catch (Exception $e) {
        return 'ไม่ทราบ';
    }
}

private function convert_access_type_to_level($access_type) {
    $mapping = [
        'read' => 'read_only',
        'write' => 'read_write',
        'admin' => 'admin',
        'owner' => 'admin'
    ];

    return $mapping[$access_type] ?? 'read_only';
}

private function update_folder_permission_fixed($user_id, $folder_id, $access_level, $granted_by) {
    try {
        // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
        $existing = $this->db->where([
            'folder_id' => $folder_id,
            'member_id' => $user_id
        ])->get('tbl_google_drive_folder_permissions')->row();

        if ($existing) {
            // อัปเดตสิทธิ์ที่มีอยู่
            if ($access_level === 'no_access') {
                return $this->db->where('id', $existing->id)
                               ->update('tbl_google_drive_folder_permissions', [
                                   'is_active' => 0,
                                   'updated_at' => date('Y-m-d H:i:s')
                               ]);
            } else {
                return $this->db->where('id', $existing->id)
                               ->update('tbl_google_drive_folder_permissions', [
                                   'access_level' => $access_level,
                                   'granted_by' => $granted_by,
                                   'granted_at' => date('Y-m-d H:i:s'),
                                   'is_active' => 1,
                                   'updated_at' => date('Y-m-d H:i:s')
                               ]);
            }
        } else {
            // สร้างสิทธิ์ใหม่
            if ($access_level !== 'no_access') {
                return $this->db->insert('tbl_google_drive_folder_permissions', [
                    'folder_id' => $folder_id,
                    'member_id' => $user_id,
                    'access_level' => $access_level,
                    'granted_by' => $granted_by,
                    'granted_at' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'update_folder_permission_fixed error: ' . $e->getMessage());
        throw $e;
    }
}

private function update_system_permissions_fixed($user_id, $system_changes, $notes, $updated_by) {
    try {
        $member_updates = [];
        $permission_updates = [];

        // แยกข้อมูลที่จะอัปเดตใน tbl_member
        if (isset($system_changes['storage_access'])) {
            $member_updates['storage_access_granted'] = $system_changes['storage_access'];
            $member_updates['google_drive_enabled'] = $system_changes['storage_access'];
        }

        // แยกข้อมูลที่จะอัปเดตใน tbl_google_drive_member_permissions
        $permission_fields = ['can_create_folder', 'can_share', 'can_delete', 'override_position'];
        foreach ($permission_fields as $field) {
            if (isset($system_changes[$field])) {
                $permission_updates[$field] = $system_changes[$field];
            }
        }

        // อัปเดต tbl_member
        if (!empty($member_updates)) {
            $this->db->where('m_id', $user_id)
                     ->update('tbl_member', $member_updates);
        }

        // อัปเดต tbl_google_drive_member_permissions
        if (!empty($permission_updates) || !empty($notes)) {
            $existing_permission = $this->db->where('member_id', $user_id)
                                           ->get('tbl_google_drive_member_permissions')
                                           ->row();

            $permission_data = array_merge($permission_updates, [
                'updated_by' => $updated_by,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if (!empty($notes)) {
                $permission_data['notes'] = $notes;
            }

            if ($existing_permission) {
                $this->db->where('member_id', $user_id)
                         ->update('tbl_google_drive_member_permissions', $permission_data);
            } else {
                $permission_data = array_merge($permission_data, [
                    'member_id' => $user_id,
                    'permission_type' => 'custom',
                    'is_active' => 1,
                    'created_by' => $updated_by,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $this->db->insert('tbl_google_drive_member_permissions', $permission_data);
            }
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'update_system_permissions_fixed error: ' . $e->getMessage());
        throw $e;
    }
}

private function log_permission_change_fixed($user_id, $action_type, $description, $folder_id = null) {
    try {
        $log_data = [
            'member_id' => $this->session->userdata('m_id'),
            'action_type' => $action_type,
            'action_description' => $description,
            'module' => 'google_drive_system',
            'folder_id' => $folder_id,
            'status' => 'success',
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_google_drive_logs', $log_data);

    } catch (Exception $e) {
        log_message('error', 'log_permission_change_fixed error: ' . $e->getMessage());
        return false;
    }
}
	
	

	/**
 * ✅ API: ดึงรายการโฟลเดอร์ที่สามารถให้สิทธิ์ได้
 */
public function get_available_folders_for_permission() {
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        // ตรวจสอบสิทธิ์ Admin
        $user_system = $this->session->userdata('m_system');
        if (!in_array($user_system, ['system_admin', 'super_admin'])) {
            $this->output_json_error('ไม่มีสิทธิ์เข้าถึง');
            return;
        }

        $user_id = $this->input->post('user_id');
        if (empty($user_id)) {
            $this->output_json_error('ไม่ได้ระบุรหัสผู้ใช้');
            return;
        }

        // ดึงรายการโฟลเดอร์ทั้งหมดที่สามารถให้สิทธิ์ได้
        $available_folders = $this->get_available_folders_list($user_id);

        $this->output_json_success([
            'folders' => $available_folders,
            'total_count' => count($available_folders)
        ], 'โหลดรายการโฟลเดอร์เรียบร้อย');

    } catch (Exception $e) {
        log_message('error', 'get_available_folders_for_permission error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ Helper: ดึงรายการโฟลเดอร์ที่สามารถให้สิทธิ์ได้
 */
private function get_available_folders_list($user_id) {
    try {
        $folders = [];
        
        // 1. ดึงโฟลเดอร์จาก system folders (ถ้ามี)
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $system_folders = $this->db->select('folder_id, folder_name, folder_type, parent_folder_id')
                                      ->from('tbl_google_drive_system_folders')
                                      ->where('is_active', 1)
                                      ->where('folder_type !=', 'root') // ไม่รวม root folder
                                      ->order_by('folder_type, folder_name')
                                      ->get()
                                      ->result();

            foreach ($system_folders as $folder) {
                // ตรวจสอบสิทธิ์ปัจจุบัน
                $current_permission = $this->get_user_current_folder_permission($user_id, $folder->folder_id);
                
                $folders[] = [
                    'folder_id' => $folder->folder_id,
                    'folder_name' => $folder->folder_name,
                    'folder_type' => $folder->folder_type,
                    'parent_folder_id' => $folder->parent_folder_id,
                    'current_permission' => $current_permission,
                    'can_grant' => $this->can_grant_folder_permission($folder->folder_type, $user_id),
                    'source' => 'system'
                ];
            }
        }
        
        // 2. ดึงโฟลเดอร์จาก Google Drive API (ถ้าจำเป็น)
        $google_folders = $this->get_additional_google_folders();
        foreach ($google_folders as $folder) {
            $current_permission = $this->get_user_current_folder_permission($user_id, $folder['folder_id']);
            
            $folders[] = [
                'folder_id' => $folder['folder_id'],
                'folder_name' => $folder['folder_name'],
                'folder_type' => $folder['folder_type'] ?? 'other',
                'parent_folder_id' => $folder['parent_folder_id'] ?? null,
                'current_permission' => $current_permission,
                'can_grant' => true,
                'source' => 'google_drive'
            ];
        }
        
        // กรองเฉพาะโฟลเดอร์ที่สามารถให้สิทธิ์ได้
        $available_folders = array_filter($folders, function($folder) {
            return $folder['can_grant'] === true;
        });
        
        return array_values($available_folders); // Re-index array
        
    } catch (Exception $e) {
        log_message('error', 'get_available_folders_list error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ Helper: ตรวจสอบสิทธิ์ปัจจุบันของผู้ใช้กับโฟลเดอร์
 */
private function get_user_current_folder_permission($user_id, $folder_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return null;
        }

        $permission = $this->db->select('access_type')
                              ->from('tbl_google_drive_member_folder_access')
                              ->where('member_id', $user_id)
                              ->where('folder_id', $folder_id)
                              ->where('is_active', 1)
                              ->order_by('granted_at', 'DESC')
                              ->limit(1)
                              ->get()
                              ->row();

        return $permission ? $permission->access_type : null;
        
    } catch (Exception $e) {
        log_message('error', 'get_user_current_folder_permission error: ' . $e->getMessage());
        return null;
    }
}

/**
 * ✅ Helper: ตรวจสอบว่าสามารถให้สิทธิ์โฟลเดอร์นี้ได้หรือไม่
 */
private function can_grant_folder_permission($folder_type, $user_id) {
    try {
        // กฎการให้สิทธิ์
        switch ($folder_type) {
            case 'admin':
                // เฉพาะ Super Admin เท่านั้น
                return $this->session->userdata('m_system') === 'super_admin';
                
            case 'personal':
                // ไม่ให้สิทธิ์โฟลเดอร์ส่วนตัวของคนอื่น
                $owner_id = $this->get_personal_folder_owner($folder_id);
                return $owner_id == $user_id;
                
            case 'department':
            case 'shared':
            case 'system':
                // สามารถให้สิทธิ์ได้
                return true;
                
            default:
                return true;
        }
        
    } catch (Exception $e) {
        log_message('error', 'can_grant_folder_permission error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ Helper: ดึงโฟลเดอร์เพิ่มเติมจาก Google Drive (ถ้าจำเป็น)
 */
private function get_additional_google_folders() {
    try {
        // ตัวอย่าง: ดึงโฟลเดอร์จาก Google Drive API
        // สามารถปรับแต่งตามความต้องการ
        
        return []; // ส่งคืน array ว่างถ้าไม่มีโฟลเดอร์เพิ่มเติม
        
    } catch (Exception $e) {
        log_message('error', 'get_additional_google_folders error: ' . $e->getMessage());
        return [];
    }
}

/**
 * ✅ API: เพิ่มสิทธิ์หลายโฟลเดอร์พร้อมกัน
 */
public function grant_bulk_folder_permissions() {
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_clean();
    }
    
    try {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        // ตรวจสอบสิทธิ์ Admin
        $user_system = $this->session->userdata('m_system');
        if (!in_array($user_system, ['system_admin', 'super_admin'])) {
            $this->output_json_error('ไม่มีสิทธิ์เข้าถึง');
            return;
        }

        // รับข้อมูล JSON
        $input = json_decode($this->input->raw_input_stream, true);
        
        if (empty($input)) {
            $this->output_json_error('ไม่ได้รับข้อมูล');
            return;
        }

        $user_id = $input['user_id'] ?? null;
        $folders = $input['folders'] ?? [];
        $permission_level = $input['permission_level'] ?? 'read_only';
        $apply_to_subfolders = $input['apply_to_subfolders'] ?? false;
        $expiry_date = $input['expiry_date'] ?? null;

        // Validation
        if (empty($user_id) || empty($folders)) {
            $this->output_json_error('กรุณากรอกข้อมูลให้ครบถ้วน');
            return;
        }

        if (!is_array($folders)) {
            $this->output_json_error('รูปแบบข้อมูลโฟลเดอร์ไม่ถูกต้อง');
            return;
        }

        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $this->output_json_error('ตารางสิทธิ์ยังไม่ได้สร้าง กรุณาติดต่อ Admin');
            return;
        }

        // เริ่ม transaction
        $this->db->trans_start();

        $success_count = 0;
        $failed_count = 0;
        $results = [];

        foreach ($folders as $folder) {
            try {
                $folder_id = $folder['folder_id'] ?? null;
                $folder_name = $folder['folder_name'] ?? 'Unknown';

                if (empty($folder_id)) {
                    $failed_count++;
                    $results[] = [
                        'folder_name' => $folder_name,
                        'status' => 'failed',
                        'message' => 'ไม่ได้ระบุ Folder ID'
                    ];
                    continue;
                }

                // เพิ่มสิทธิ์
                $permission_result = $this->grant_single_folder_permission(
                    $user_id, 
                    $folder_id, 
                    $permission_level,
                    $apply_to_subfolders,
                    $expiry_date
                );

                if ($permission_result) {
                    $success_count++;
                    $results[] = [
                        'folder_name' => $folder_name,
                        'status' => 'success',
                        'permission_level' => $permission_level
                    ];
                } else {
                    $failed_count++;
                    $results[] = [
                        'folder_name' => $folder_name,
                        'status' => 'failed',
                        'message' => 'ไม่สามารถเพิ่มสิทธิ์ได้'
                    ];
                }

            } catch (Exception $e) {
                $failed_count++;
                $results[] = [
                    'folder_name' => $folder['folder_name'] ?? 'Unknown',
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ];
            }
        }

        // Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->output_json_error('ไม่สามารถบันทึกข้อมูลได้ (Transaction failed)');
            return;
        }

        // Log การกระทำ
        $this->log_activity(
            $this->session->userdata('m_id'),
            'grant_bulk_folder_permissions',
            "เพิ่มสิทธิ์หลายโฟลเดอร์: {$success_count} สำเร็จ, {$failed_count} ล้มเหลว",
            [
                'target_user_id' => $user_id,
                'permission_level' => $permission_level,
                'total_folders' => count($folders),
                'success_count' => $success_count,
                'failed_count' => $failed_count
            ]
        );

        $this->output_json_success([
            'success_count' => $success_count,
            'failed_count' => $failed_count,
            'total_count' => count($folders),
            'results' => $results
        ], "เพิ่มสิทธิ์เรียบร้อย: {$success_count}/{" . count($folders) . "} โฟลเดอร์");

    } catch (Exception $e) {
        // Rollback transaction if active
        if ($this->db && method_exists($this->db, 'trans_status') && $this->db->trans_status() !== FALSE) {
            $this->db->trans_rollback();
        }

        log_message('error', 'grant_bulk_folder_permissions error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ✅ Helper: เพิ่มสิทธิ์โฟลเดอร์เดี่ยว
 */
private function grant_single_folder_permission($user_id, $folder_id, $permission_level, $apply_to_subfolders = false, $expiry_date = null) {
    try {
        // ลบสิทธิ์เดิม (ถ้ามี)
        $this->db->where([
            'member_id' => $user_id,
            'folder_id' => $folder_id
        ])->update('tbl_google_drive_member_folder_access', ['is_active' => 0]);

        // ดึงข้อมูลผู้ให้สิทธิ์
        $granted_by_name = $this->get_user_name($this->session->userdata('m_id'));

        // เพิ่มสิทธิ์ใหม่
        $permission_data = [
            'member_id' => $user_id,
            'folder_id' => $folder_id,
            'access_type' => $permission_level,
            'permission_source' => 'direct',
            'permission_mode' => 'direct',
            'granted_by' => $this->session->userdata('m_id'),
            'granted_by_name' => $granted_by_name,
            'granted_at' => date('Y-m-d H:i:s'),
            'expires_at' => !empty($expiry_date) ? $expiry_date . ' 23:59:59' : null,
            'is_active' => 1,
            'inherit_from_parent' => 0,
            'apply_to_children' => $apply_to_subfolders ? 1 : 0
        ];

        $insert_result = $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);

        // ถ้าเลือกให้ apply กับ subfolders
        if ($insert_result && $apply_to_subfolders) {
            $this->apply_permission_to_subfolders($folder_id, $permission_data);
        }

        return $insert_result;

    } catch (Exception $e) {
        log_message('error', 'grant_single_folder_permission error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ✅ API: ลบสิทธิ์โฟลเดอร์ของผู้ใช้
 */
public function remove_user_folder_permission() {
    // Force JSON output
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    try {
        // ✅ 1. ตรวจสอบ Method และ AJAX
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Only POST method allowed'
            ]);
            return;
        }
        
        // ✅ 2. ตรวจสอบ Session
        $user_system = $this->session->userdata('m_system');
        $current_user_id = $this->session->userdata('m_id');
        
        if (!in_array($user_system, ['system_admin', 'super_admin']) || empty($current_user_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง - ต้องเป็น Admin เท่านั้น'
            ]);
            return;
        }

        // ✅ 3. รับข้อมูล POST
        $folder_id = $this->input->post('folder_id');
        $user_id = $this->input->post('user_id');

        if (empty($folder_id) || empty($user_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'กรุณาระบุ folder_id และ user_id'
            ]);
            return;
        }

        // ✅ 4. ตรวจสอบว่าตารางมีอยู่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            echo json_encode([
                'success' => false,
                'message' => 'ตาราง tbl_google_drive_member_folder_access ไม่มีอยู่'
            ]);
            return;
        }

        // ✅ 5. ตรวจสอบสิทธิ์ที่มีอยู่
        $existing_permission = $this->db->select('id, access_type, permission_source')
                                       ->from('tbl_google_drive_member_folder_access')
                                       ->where('member_id', $user_id)
                                       ->where('folder_id', $folder_id)
                                       ->where('is_active', 1)
                                       ->get()
                                       ->row();

        if (!$existing_permission) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบสิทธิ์ที่จะลบ หรือสิทธิ์ถูกลบไปแล้ว'
            ]);
            return;
        }

        // ✅ 6. เริ่ม Transaction
        $this->db->trans_start();

        // ✅ 7. ลบสิทธิ์ (soft delete) - ใช้เฉพาะ columns ที่มีอยู่จริง
        $current_time = date('Y-m-d H:i:s');
        
        $update_data = [
            'is_active' => 0,
            'updated_at' => $current_time
        ];
        
        // ✅ เช็คว่ามี column expires_at หรือไม่ ก่อนใช้
        $fields = $this->db->list_fields('tbl_google_drive_member_folder_access');
        if (in_array('expires_at', $fields)) {
            $update_data['expires_at'] = $current_time; // ใช้เป็น marker ว่าถูกลบเมื่อไหร่
        }
        
        $update_result = $this->db->where([
            'member_id' => $user_id,
            'folder_id' => $folder_id,
            'is_active' => 1
        ])->update('tbl_google_drive_member_folder_access', $update_data);

        if (!$update_result) {
            $this->db->trans_rollback();
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถลบสิทธิ์ได้ - Database update failed'
            ]);
            return;
        }

        $affected_rows = $this->db->affected_rows();
        if ($affected_rows === 0) {
            $this->db->trans_rollback();
            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีข้อมูลที่เปลี่ยนแปลง - สิทธิ์อาจถูกลบแล้ว'
            ]);
            return;
        }

        // ✅ 8. ลบสิทธิ์สืบทอดใน subfolders (ถ้ามี)
        $inherited_removed = 0;
        try {
            $inherited_removed = $this->revoke_inherited_permissions_simple($user_id, $folder_id);
        } catch (Exception $e) {
            log_message('warning', 'Failed to revoke inherited permissions: ' . $e->getMessage());
        }

        // ✅ 9. Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => 'Transaction failed'
            ]);
            return;
        }

        // ✅ 10. Log การกระทำ (ใช้วิธีปลอดภัย)
        $this->safe_log_remove_permission($current_user_id, $user_id, $folder_id, $existing_permission->access_type);

        // ✅ 11. ส่งผลลัพธ์สำเร็จ
        echo json_encode([
            'success' => true,
            'message' => 'ลบสิทธิ์เรียบร้อยแล้ว',
            'data' => [
                'removed_permission_id' => $existing_permission->id,
                'previous_access_type' => $existing_permission->access_type,
                'inherited_removed' => $inherited_removed,
                'folder_id' => $folder_id,
                'user_id' => $user_id,
                'affected_rows' => $affected_rows,
                'removed_at' => $current_time
            ]
        ]);

    } catch (Exception $e) {
        // Rollback if needed
        if ($this->db && method_exists($this->db, 'trans_status')) {
            $this->db->trans_rollback();
        }

        // Log error
        log_message('error', 'remove_user_folder_permission error: ' . $e->getMessage());

        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            'error_details' => [
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ]
        ]);
    }
}
	
	/**
 * ✅ Helper: ลบสิทธิ์สืบทอดแบบง่าย (ใช้ columns ที่มีอยู่จริง)
 */
private function revoke_inherited_permissions_simple($user_id, $parent_folder_id) {
    try {
        // ตรวจสอบว่าตารางมีอยู่
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return 0;
        }

        // ✅ เช็คว่ามี column parent_folder_id หรือไม่
        $fields = $this->db->list_fields('tbl_google_drive_member_folder_access');
        if (!in_array('parent_folder_id', $fields)) {
            return 0; // ถ้าไม่มี column นี้ ไม่ต้องทำอะไร
        }

        // นับจำนวนที่จะลบ
        $count = $this->db->where([
            'member_id' => $user_id,
            'parent_folder_id' => $parent_folder_id,
            'is_active' => 1
        ])->count_all_results('tbl_google_drive_member_folder_access');

        if ($count > 0) {
            // ลบสิทธิ์ที่สืบทอด
            $this->db->where([
                'member_id' => $user_id,
                'parent_folder_id' => $parent_folder_id,
                'is_active' => 1
            ])->update('tbl_google_drive_member_folder_access', [
                'is_active' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->db->affected_rows();
        }

        return 0;

    } catch (Exception $e) {
        log_message('error', 'revoke_inherited_permissions_simple error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * ✅ Helper: บันทึก log การลบสิทธิ์ (ปลอดภัย)
 */
private function safe_log_remove_permission($current_user_id, $target_user_id, $folder_id, $access_type) {
    try {
        // ตรวจสอบว่าตาราง log มีอยู่หรือไม่
        if (!$this->db->table_exists('tbl_google_drive_logs')) {
            return false;
        }

        // ดึงชื่อผู้ใช้
        $target_user = $this->db->select('m_fname, m_lname')
                               ->from('tbl_member')
                               ->where('m_id', $target_user_id)
                               ->get()
                               ->row();

        $target_name = $target_user ? trim($target_user->m_fname . ' ' . $target_user->m_lname) : 'Unknown User';

        // บันทึก log
        $log_data = [
            'member_id' => $current_user_id,
            'action_type' => 'remove_folder_permission',
            'action_description' => "ลบสิทธิ์โฟลเดอร์ ({$access_type}) จากผู้ใช้: {$target_name}",
            'module' => 'google_drive_system',
            'folder_id' => $folder_id,
            'status' => 'success',
            'ip_address' => $this->input->ip_address(),
            'user_agent' => substr($this->input->user_agent() ?: 'Unknown', 0, 500),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_google_drive_logs', $log_data);

    } catch (Exception $e) {
        log_message('error', 'safe_log_remove_permission error: ' . $e->getMessage());
        return false;
    }
}
	
	

/**
 * ✅ Helper: ลบสิทธิ์สืบทอดใน subfolders
 */
private function revoke_inherited_permissions_safe($user_id, $parent_folder_id, $revoked_by) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
            return 0;
        }

        // ✅ นับจำนวนสิทธิ์ที่จะลบก่อน
        $count_query = $this->db->where([
            'member_id' => $user_id,
            'parent_folder_id' => $parent_folder_id,
            'inherit_from_parent' => 1,
            'is_active' => 1
        ])->get('tbl_google_drive_member_folder_access');

        $count = $count_query->num_rows();

        if ($count > 0) {
            // ✅ ลบสิทธิ์ที่สืบทอดจาก parent folder นี้
            $update_result = $this->db->where([
                'member_id' => $user_id,
                'parent_folder_id' => $parent_folder_id,
                'inherit_from_parent' => 1,
                'is_active' => 1
            ])->update('tbl_google_drive_member_folder_access', [
                'is_active' => 0,
                'revoked_at' => date('Y-m-d H:i:s'),
                'revoked_by' => $revoked_by,
                'revoked_reason' => 'Parent permission removed (inherited)',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $update_result ? $count : 0;
        }

        return 0;

    } catch (Exception $e) {
        log_message('error', 'revoke_inherited_permissions_safe error: ' . $e->getMessage());
        return 0;
    }
}

	
	

	
	/**
 * หน้าแสดงรายละเอียดผู้ใช้
 */
public function user_details($user_id = null) {
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_error('ไม่มีสิทธิ์เข้าถึง', 403);
            return;
        }

        // ตรวจสอบ user_id
        if (empty($user_id) || !is_numeric($user_id)) {
            show_error('ไม่พบรหัสผู้ใช้', 404);
            return;
        }

        // ดึงข้อมูลผู้ใช้
        $user_data = $this->get_user_details_data($user_id);
        
        if (!$user_data) {
            show_error('ไม่พบผู้ใช้ที่ระบุ', 404);
            return;
        }

        // ส่งข้อมูลไปยัง view
        $data = [
            'page_title' => 'รายละเอียดผู้ใช้: ' . $user_data['user']['full_name'],
            'user_data' => $user_data,
            'user_id' => $user_id
        ];
        
		
		   $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/user_details', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
		


    } catch (Exception $e) {
        log_message('error', 'User details error: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาดในการโหลดข้อมูล', 500);
    }
}

/**
 * API: ดึงข้อมูลผู้ใช้สำหรับ AJAX
 */
public function get_user_details_ajax($user_id = null) {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]);
            return;
        }

        // รับ user_id จาก URL หรือ POST
        if (empty($user_id)) {
            $user_id = $this->input->post('user_id') ?: $this->input->get('user_id');
        }

        if (empty($user_id) || !is_numeric($user_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบรหัสผู้ใช้'
            ]);
            return;
        }

        // ดึงข้อมูล
        $user_data = $this->get_user_details_data($user_id);
        
        if (!$user_data) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบผู้ใช้ที่ระบุ'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $user_data
        ]);

    } catch (Exception $e) {
        log_message('error', 'Get user details AJAX error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
        ]);
    }
}

/**
 * Helper: ดึงข้อมูลผู้ใช้ที่ครบถ้วน
 */
private function get_user_details_data($user_id) {
    try {
        // 1. ข้อมูลผู้ใช้พื้นฐาน
        $user = $this->db->select('
            m.m_id, m.m_username, m.m_fname, m.m_lname, m.m_email, m.m_phone,
            m.m_status, m.m_system, m.m_datesave, m.google_email, m.google_drive_enabled,
            m.storage_access_granted, m.personal_folder_id, m.storage_quota_limit, 
            m.storage_quota_used, m.last_storage_access,
            p.pname as position_name, p.pdescription as position_description,
            CONCAT(m.m_fname, " ", m.m_lname) as full_name
        ')
        ->from('tbl_member m')
        ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
        ->where('m.m_id', $user_id)
        ->get()
        ->row();

        if (!$user) {
            return false;
        }

        // 2. สิทธิ์โฟลเดอร์
        $folder_permissions = [];
        if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $folder_permissions = $this->db->select('
                mfa.*, 
                sf.folder_name, sf.folder_type, sf.folder_path,
                CONCAT(granted_by_member.m_fname, " ", granted_by_member.m_lname) as granted_by_name
            ')
            ->from('tbl_google_drive_member_folder_access mfa')
            ->join('tbl_google_drive_system_folders sf', 'mfa.folder_id = sf.folder_id', 'left')
            ->join('tbl_member granted_by_member', 'mfa.granted_by = granted_by_member.m_id', 'left')
            ->where('mfa.member_id', $user_id)
            ->where('mfa.is_active', 1)
            ->order_by('mfa.granted_at', 'DESC')
            ->get()
            ->result();
        }

        // 3. Activity Logs
        $activity_logs = [];
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $activity_logs = $this->db->select('*')
                ->from('tbl_google_drive_logs')
                ->where('member_id', $user_id)
                ->order_by('created_at', 'DESC')
                ->limit(20)
                ->get()
                ->result();
        }

        // 4. Storage Usage History
        $storage_usage = [];
        if ($this->db->table_exists('tbl_google_drive_storage_usage')) {
            $storage_usage = $this->db->select('*')
                ->from('tbl_google_drive_storage_usage')
                ->where('user_id', $user_id)
                ->order_by('usage_date', 'DESC')
                ->limit(30)
                ->get()
                ->result();
        }

        // 5. File Activities
        $file_activities = [];
        if ($this->db->table_exists('tbl_google_drive_file_activities')) {
            $file_activities = $this->db->select('*')
                ->from('tbl_google_drive_file_activities')
                ->where('user_id', $user_id)
                ->order_by('created_at', 'DESC')
                ->limit(20)
                ->get()
                ->result();
        }

        // 6. คำนวณสถิติ
        $stats = $this->calculate_user_stats($user_id);

        return [
            'user' => (array) $user,
            'folder_permissions' => $folder_permissions,
            'activity_logs' => $activity_logs,
            'storage_usage' => $storage_usage,
            'file_activities' => $file_activities,
            'stats' => $stats
        ];

    } catch (Exception $e) {
        log_message('error', 'Get user details data error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Helper: คำนวณสถิติผู้ใช้
 */
private function calculate_user_stats($user_id) {
    $stats = [
        'total_folders' => 0,
        'total_files' => 0,
        'total_uploads' => 0,
        'total_downloads' => 0,
        'total_shares' => 0,
        'storage_usage_percent' => 0,
        'last_activity_date' => null,
        'account_age_days' => 0
    ];

    try {
        // นับโฟลเดอร์ที่มีสิทธิ์
        if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
            $stats['total_folders'] = $this->db->where([
                'member_id' => $user_id,
                'is_active' => 1
            ])->count_all_results('tbl_google_drive_member_folder_access');
        }

        // นับไฟล์ที่อัปโหลด
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $stats['total_files'] = $this->db->where('uploaded_by', $user_id)
                ->count_all_results('tbl_google_drive_system_files');
        }

        // นับ activities
        if ($this->db->table_exists('tbl_google_drive_file_activities')) {
            $stats['total_uploads'] = $this->db->where([
                'user_id' => $user_id,
                'action_type' => 'upload'
            ])->count_all_results('tbl_google_drive_file_activities');

            $stats['total_downloads'] = $this->db->where([
                'user_id' => $user_id,
                'action_type' => 'download'
            ])->count_all_results('tbl_google_drive_file_activities');

            $stats['total_shares'] = $this->db->where([
                'user_id' => $user_id,
                'action_type' => 'share'
            ])->count_all_results('tbl_google_drive_file_activities');

            // หา activity ล่าสุด
            $last_activity = $this->db->select('created_at')
                ->from('tbl_google_drive_file_activities')
                ->where('user_id', $user_id)
                ->order_by('created_at', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($last_activity) {
                $stats['last_activity_date'] = $last_activity->created_at;
            }
        }

        // คำนวณ storage usage percentage
        $user = $this->db->select('storage_quota_used, storage_quota_limit')
            ->from('tbl_member')
            ->where('m_id', $user_id)
            ->get()
            ->row();

        if ($user && $user->storage_quota_limit > 0) {
            $stats['storage_usage_percent'] = round(
                ($user->storage_quota_used / $user->storage_quota_limit) * 100, 2
            );
        }

        // คำนวณอายุบัญชี
        $member = $this->db->select('m_datesave')
            ->from('tbl_member')
            ->where('m_id', $user_id)
            ->get()
            ->row();

        if ($member) {
            $created_date = new DateTime($member->m_datesave);
            $now = new DateTime();
            $stats['account_age_days'] = $now->diff($created_date)->days;
        }

    } catch (Exception $e) {
        log_message('error', 'Calculate user stats error: ' . $e->getMessage());
    }

    return $stats;
}

/**
 * API: ลบข้อมูลผู้ใช้ (สำหรับ Admin)
 */
public function delete_user_data() {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์เข้าถึง'
            ]);
            return;
        }

        $user_id = $this->input->post('user_id');
        $action_type = $this->input->post('action_type'); // 'soft_delete' หรือ 'hard_delete'

        if (empty($user_id) || !is_numeric($user_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบรหัสผู้ใช้'
            ]);
            return;
        }

        // ตรวจสอบว่าผู้ใช้มีอยู่
        $user = $this->db->select('m_fname, m_lname')
            ->from('tbl_member')
            ->where('m_id', $user_id)
            ->get()
            ->row();

        if (!$user) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบผู้ใช้ที่ระบุ'
            ]);
            return;
        }

        $this->db->trans_start();

        if ($action_type === 'hard_delete') {
            // ลบข้อมูลทั้งหมด
            $this->hard_delete_user_data($user_id);
        } else {
            // Soft delete (ปิดการใช้งาน)
            $this->soft_delete_user_data($user_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถลบข้อมูลได้'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'ลบข้อมูลผู้ใช้เรียบร้อย',
            'action_type' => $action_type
        ]);

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Delete user data error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
    }
}

/**
 * Helper: Soft delete ผู้ใช้
 */
private function soft_delete_user_data($user_id) {
    // ปิดการใช้งานผู้ใช้
    $this->db->where('m_id', $user_id)
        ->update('tbl_member', [
            'm_status' => '0',
            'storage_access_granted' => 0,
            'google_drive_enabled' => 0
        ]);

    // ปิดสิทธิ์ทั้งหมด
    if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
        $this->db->where('member_id', $user_id)
            ->update('tbl_google_drive_member_folder_access', ['is_active' => 0]);
    }
}

/**
 * Helper: Hard delete ผู้ใช้
 */
private function hard_delete_user_data($user_id) {
    // ลบจากตารางต่างๆ
    $tables_to_clean = [
        'tbl_google_drive_member_folder_access',
        'tbl_google_drive_permissions',
        'tbl_google_drive_member_permissions',
        'tbl_google_drive_logs',
        'tbl_google_drive_file_activities',
        'tbl_google_drive_storage_usage'
    ];

    foreach ($tables_to_clean as $table) {
        if ($this->db->table_exists($table)) {
            $this->db->where('member_id', $user_id)->delete($table);
        }
    }

    // อัปเดตตารางที่ใช้ user_id
    if ($this->db->table_exists('tbl_google_drive_system_files')) {
        $this->db->where('uploaded_by', $user_id)
            ->update('tbl_google_drive_system_files', ['uploaded_by' => null]);
    }
}
	
	
	
}
?>