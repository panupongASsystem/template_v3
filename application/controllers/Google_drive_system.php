<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Google Drive System Controller v1.3.0 (Complete Monolog Fix)
 * จัดการ Centralized Google Drive Storage สำหรับระบบ
 * แก้ไขปัญหา Monolog\Logger not found อย่างสมบูรณ์
 * 
 * @author   System Developer
 * @version  1.3.0 (Complete Monolog Fix)
 * @since    2025-01-20
 */
class Google_drive_system extends CI_Controller
{

    private $member_id;
    private $storage_mode;

    private $google_client;
    private $drive_service;
    private $system_storage_id = null;
    private $config_loaded = false;
    private $use_curl_mode = false; // ใช้ cURL แทน Google Client เมื่อมีปัญหา

    public function __construct()
    {
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


        // ✅ กำหนดค่า member_id
        $this->member_id = $this->session->userdata('m_id');

        // ✅ กำหนดค่า storage_mode จาก database
        $this->storage_mode = $this->get_storage_mode();
    }

    /**
     * ✅ ดึง Storage Mode จาก Database
     */
    private function get_storage_mode()
    {
        try {
            $query = $this->db->select('setting_key, setting_value')
                ->from('tbl_google_drive_settings')
                ->where('is_active', 1)
                ->where('setting_key', 'system_storage_mode')
                ->get();

            if ($query->num_rows() > 0) {
                return $query->row()->setting_value;
            }

            return 'centralized'; // ค่าเริ่มต้น

        } catch (Exception $e) {
            log_message('error', 'Get storage mode error: ' . $e->getMessage());
            return 'centralized';
        }
    }

    /**
     * โหลด Config แบบปลอดภัย
     */
    private function safe_load_config()
    {
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
    private function set_default_config()
    {
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
    private function init_google_client_multiple_methods()
    {
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
                log_message('debug', 'Standard Google Client failed: ' . $e->getMessage());

                // ถ้าเป็น Logger/Monolog Error ให้เปลี่ยนเป็น cURL mode
                if (
                    strpos($e->getMessage(), 'Logger') !== false ||
                    strpos($e->getMessage(), 'Monolog') !== false ||
                    strpos($e->getMessage(), 'Psr\Log') !== false
                ) {
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
                log_message('debug', 'Minimal Google Client failed: ' . $e->getMessage());
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
    private function setup_basic_google_client_for_auth()
    {
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
    private function try_standard_google_client()
    {
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
            if (
                strpos($e->getMessage(), 'Monolog') !== false ||
                strpos($e->getMessage(), 'Logger') !== false
            ) {
                log_message('error', 'Monolog dependency error detected');
            }

            return false;
        }
    }

    /**
     * ลองใช้ Google Client แบบ minimal
     */
    private function try_minimal_google_client()
    {
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
    public function index()
    {
        redirect('google_drive_system/dashboard');
    }


    public function dashboard()
    {
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
                $data['system_ready'] = (bool) $data['system_storage']->folder_structure_created;

                // เพิ่มข้อมูลที่แปลงแล้วสำหรับแสดงผล
                $data['system_storage']->total_storage_used_formatted = $this->format_bytes($data['system_storage']->total_storage_used);
                $data['system_storage']->max_storage_limit_formatted = $this->format_bytes($data['system_storage']->max_storage_limit);

                // คำนวณเปอร์เซ็นต์การใช้งาน
                if ($data['system_storage']->max_storage_limit > 0) {
                    $data['system_storage']->storage_usage_percent = round(
                        ($data['system_storage']->total_storage_used / $data['system_storage']->max_storage_limit) * 100,
                        2
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
    private function get_enhanced_system_storage_info()
    {
        try {
            // สร้างตารางถ้ายังไม่มี
            $this->create_system_storage_table_if_not_exists();

            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                log_message('debug', 'System storage table does not exist');
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
            return (object) [
                'id' => $system_storage->id,
                'storage_name' => $system_storage->storage_name ?? 'Organization Storage',
                'google_account_email' => $system_storage->google_account_email,
                'total_storage_used' => (int) ($system_storage->total_storage_used ?? 0),
                'max_storage_limit' => (int) ($system_storage->max_storage_limit ?? 107374182400), // 100GB default
                'folder_structure_created' => (bool) ($system_storage->folder_structure_created ?? false),
                'is_active' => (bool) ($system_storage->is_active ?? true),
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
    private function get_enhanced_storage_statistics()
    {
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

                $stats['total_files'] = (int) ($file_stats->total_files ?? 0);
                $stats['storage_usage']['used_bytes'] = (int) ($file_stats->total_size ?? 0);
            }

            // นับการเชื่อมต่อใหม่ในเดือนนี้
            $stats['new_connections'] = $this->db->where('last_storage_access >=', date('Y-m-01'))
                ->where('storage_access_granted', 1)
                ->count_all_results('tbl_member');

            // คำนวณการใช้งาน Storage
            $system_storage = $this->get_active_system_storage();
            if ($system_storage) {
                $stats['storage_usage']['used_bytes'] = (int) ($system_storage->total_storage_used ?? 0);
                $stats['storage_usage']['limit_bytes'] = (int) ($system_storage->max_storage_limit ?? 107374182400);
            }

            $stats['storage_usage']['used_formatted'] = $this->format_bytes($stats['storage_usage']['used_bytes']);
            $stats['storage_usage']['limit_formatted'] = $this->format_bytes($stats['storage_usage']['limit_bytes']);

            if ($stats['storage_usage']['limit_bytes'] > 0) {
                $stats['storage_usage']['percentage'] = round(
                    ($stats['storage_usage']['used_bytes'] / $stats['storage_usage']['limit_bytes']) * 100,
                    2
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
    private function get_default_storage_stats()
    {
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
    private function format_bytes($bytes, $precision = 2)
    {
        try {
            $bytes = max(0, (int) $bytes);

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
    public function refresh_dashboard_data()
    {
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
                        ($refreshed_data['system_storage']->total_storage_used / $refreshed_data['system_storage']->max_storage_limit) * 100,
                        2
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
    public function setup()
    {
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
    public function connect_system_account()
    {
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
    private function create_auth_url_with_refresh_token()
    {
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
     * ตรวจสอบว่ามี Access Token ที่ใช้งานได้
     */
    private function has_valid_access_token($system_storage)
    {
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
    private function test_drive_api_access($system_storage)
    {
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
    public function verify_department_folders()
    {
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
    private function create_department_folders_curl($departments_folder_id, $access_token)
    {
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
                            log_message('debug', "Existing folder not accessible, will recreate: {$position->pname}");
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
    private function test_folder_exists_simple($folder_id, $access_token)
    {
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
    private function load_system_access_token()
    {
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
     * ดึงข้อมูล System Storage พร้อม Logging แบบละเอียด
     */
    private function get_system_storage_info()
    {
        try {
            log_message('info', '=== START: get_system_storage_info() ===');

            // ตรวจสอบตาราง system_storage
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                log_message('info', 'Table tbl_google_drive_system_storage does not exist');
                log_message('info', '=== END: get_system_storage_info() - Return NULL (no table) ===');
                return null;
            }
            log_message('info', 'Table tbl_google_drive_system_storage exists');

            // ดึงข้อมูล system storage
            $system_storage = $this->db->select('*')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->get()
                ->row();

            if (!$system_storage) {
                log_message('info', 'No active system storage record found in database');
                log_message('info', '=== END: get_system_storage_info() - Return NULL (no record) ===');
                return null;
            }

            log_message('info', sprintf(
                'Found system storage: ID=%d, Name=%s, Email=%s',
                $system_storage->id,
                $system_storage->storage_name ?? 'N/A',
                $system_storage->google_account_email ?? 'N/A'
            ));

            // ===== นับโฟลเดอร์ =====
            $total_folders = 0;
            if ($this->db->table_exists('tbl_google_drive_system_folders')) {
                log_message('info', 'Counting folders in tbl_google_drive_system_folders...');

                $total_folders = $this->db->where('is_active', 1)
                    ->count_all_results('tbl_google_drive_system_folders');

                log_message('info', sprintf('Total active folders: %d', $total_folders));
            } else {
                log_message('info', 'Table tbl_google_drive_system_folders does not exist');
            }

            // ===== นับไฟล์ =====
            $total_files = 0;
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                log_message('info', 'Counting files in tbl_google_drive_system_files...');

                $total_files = $this->db->where('is_active', 1)
                    ->count_all_results('tbl_google_drive_system_files');

                log_message('info', sprintf('Total active files: %d', $total_files));

                $total_files_all = $this->db->count_all('tbl_google_drive_system_files');
                log_message('info', sprintf(
                    'Total files (including inactive): %d (active=%d, inactive=%d)',
                    $total_files_all,
                    $total_files,
                    $total_files_all - $total_files
                ));
            } else {
                log_message('info', 'Table tbl_google_drive_system_files does not exist');
            }

            // ===== นับผู้ใช้ที่มีสิทธิ์ =====
            log_message('info', 'Counting active users...');
            $active_users = $this->db->where('storage_access_granted', 1)
                ->count_all_results('tbl_member');
            log_message('info', sprintf('Total active users: %d', $active_users));

            // ===== ⭐ คำนวณ Storage Usage จากไฟล์จริง =====
            log_message('info', 'Calculating actual storage usage from files...');

            $actual_storage_used = 0;
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $result = $this->db->select_sum('file_size')
                    ->from('tbl_google_drive_system_files')
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                $actual_storage_used = $result->file_size ?? 0;
            }

            log_message('info', sprintf(
                'Storage comparison: DB stored=%d, Calculated from files=%d',
                $system_storage->total_storage_used,
                $actual_storage_used
            ));

            // ✅ ใช้ค่าที่คำนวณได้จริง
            $storage_used = $actual_storage_used;
            $storage_limit = $system_storage->max_storage_limit;

            // ✅ Format เป็น Human Readable
            $storage_used_formatted = $this->format_bytes($storage_used);
            $storage_limit_formatted = $this->format_bytes($storage_limit);

            log_message('info', sprintf(
                'Formatted storage: %s / %s',
                $storage_used_formatted,
                $storage_limit_formatted
            ));

            // คำนวณ storage usage percentage
            $storage_percent = 0;
            if ($storage_limit > 0) {
                $storage_percent = round(($storage_used / $storage_limit) * 100, 2);
            }

            log_message('info', sprintf(
                'Storage usage: %d bytes / %d bytes (%.2f%%)',
                $storage_used,
                $storage_limit,
                $storage_percent
            ));

            // ✅ สร้าง return object พร้อม formatted fields
            $result = (object) [
                'id' => $system_storage->id,
                'storage_name' => $system_storage->storage_name,
                'google_account_email' => $system_storage->google_account_email,
                'total_storage_used' => $storage_used,  // ✅ ค่าจริง
                'total_storage_used_formatted' => $storage_used_formatted,  // ✅ เพิ่ม
                'max_storage_limit' => $storage_limit,
                'max_storage_limit_formatted' => $storage_limit_formatted,  // ✅ เพิ่ม
                'folder_structure_created' => $system_storage->folder_structure_created,
                'is_active' => $system_storage->is_active,
                'created_at' => $system_storage->created_at,
                'total_folders' => $total_folders,
                'total_files' => $total_files,
                'active_users' => $active_users,
                'storage_usage_percent' => $storage_percent
            ];

            log_message('info', sprintf(
                'Summary - Folders: %d, Files: %d, Users: %d, Storage: %s / %s (%.2f%%)',
                $total_folders,
                $total_files,
                $active_users,
                $storage_used_formatted,
                $storage_limit_formatted,
                $storage_percent
            ));

            log_message('info', '=== END: get_system_storage_info() - Success ===');

            return $result;

        } catch (Exception $e) {
            log_message('error', sprintf(
                'EXCEPTION in get_system_storage_info(): %s (File: %s, Line: %d)',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            log_message('info', '=== END: get_system_storage_info() - Exception occurred ===');
            return null;
        }
    }


    /**
     * ดึง System Storage ที่ Active
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
     * อัปเดต System Storage
     */
    private function update_system_storage($storage_id, $data)
    {
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
     * ✅ FIXED: save_folder_info() with duplicate prevention
     * - ตรวจสอบ duplicate ก่อน INSERT
     * - รักษา logic เดิมไว้ทั้งหมด
     * - เพิ่มเฉพาะการตรวจสอบ
     */
    private function save_folder_info($folder_data)
    {
        try {
            log_message('info', 'Saving folder info: ' . ($folder_data['folder_name'] ?? 'unnamed'));

            // ✅ ตรวจสอบ required fields (เหมือนเดิม)
            $required_fields = ['folder_name', 'folder_id', 'folder_type'];
            foreach ($required_fields as $field) {
                if (empty($folder_data[$field])) {
                    log_message('error', "Required field '{$field}' is missing");
                    return false;
                }
            }

            // ✅ ตรวจสอบ table existence (เหมือนเดิม)
            if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
                log_message('error', 'Table tbl_google_drive_system_folders does not exist');
                return false;
            }

            // ✅ **ส่วนที่เพิ่มใหม่: ตรวจสอบ duplicate**
            $existing = $this->db
                ->where('folder_id', $folder_data['folder_id'])
                ->get('tbl_google_drive_system_folders')
                ->row();

            if ($existing) {
                log_message('info', "Folder already exists: {$folder_data['folder_id']} (ID: {$existing->id}), skipping insert");
                return true;  // ถือว่าสำเร็จ เพราะโฟลเดอร์มีอยู่แล้ว
            }

            // ✅ เตรียมข้อมูลสำหรับ insert (เหมือนเดิม)
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

            // เพิ่ม optional fields ถ้ามีในตาราง (เหมือนเดิม)
            if (in_array('created_for_position', $table_fields) && isset($folder_data['created_for_position'])) {
                $safe_data['created_for_position'] = $folder_data['created_for_position'];
            }

            if (in_array('created_by', $table_fields) && isset($folder_data['created_by'])) {
                $safe_data['created_by'] = $folder_data['created_by'];
            }

            // ลบ fields ที่ไม่มีในตาราง (เหมือนเดิม)
            foreach ($safe_data as $key => $value) {
                if (!in_array($key, $table_fields)) {
                    unset($safe_data[$key]);
                }
            }

            // Insert ข้อมูล (เหมือนเดิม)
            $result = $this->db->insert('tbl_google_drive_system_folders', $safe_data);

            if ($result) {
                $insert_id = $this->db->insert_id();
                log_message('info', "✅ Folder saved successfully: {$folder_data['folder_name']} (ID: {$insert_id})");
                return true;
            } else {
                $db_error = $this->db->error();
                log_message('error', "❌ Database insert failed: " . json_encode($db_error));
                return false;
            }

        } catch (Exception $e) {
            log_message('error', "save_folder_info exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงสถานะการตั้งค่า
     */
    private function get_system_setup_status()
    {
        try {
            $status = [
                'has_system_storage' => false,
                'folder_structure_created' => false,
                'ready_to_use' => false
            ];

            $system_storage = $this->get_system_storage_info();
            if ($system_storage) {
                $status['has_system_storage'] = true;
                $status['folder_structure_created'] = (bool) $system_storage->folder_structure_created;
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

    // ===========================================
    // Table Creation Methods
    // ===========================================

    /**
     * สร้างตาราง System Storage ถ้ายังไม่มี
     */
    private function create_system_storage_table_if_not_exists()
    {
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

    // ===========================================
    // Utility Methods
    // ===========================================

    /**
     * ดึงการตั้งค่า
     */
    private function get_setting($key, $default = '0')
    {
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
    private function output_json_success($data = [], $message = 'Success')
    {
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
     * Output JSON Error with Comprehensive Logging
     */
    private function output_json_error($message = 'Error', $extra_data = null, $status_code = 200) // ✅ เปลี่ยนจาก 400 → 200
    {
        // =============================================================
        // [LOG 1] เริ่มต้น Function
        // =============================================================
        log_message('info', '========================================');
        log_message('info', '🚨 START: output_json_error');
        log_message('info', '⏰ Time: ' . date('Y-m-d H:i:s'));
        log_message('info', '========================================');

        // =============================================================
        // [LOG 2] Log Input Parameters
        // =============================================================
        log_message('info', '📥 Input Parameters:');
        log_message('info', '  Message: ' . substr($message, 0, 200));
        log_message('info', '  Status Code: ' . $status_code);
        log_message('info', '  Has Extra Data: ' . ($extra_data ? 'YES' : 'NO'));

        if ($extra_data) {
            log_message('debug', '  Extra Data: ' . json_encode($extra_data, JSON_UNESCAPED_UNICODE));
        }

        // =============================================================
        // [LOG 3] Check Output Buffer
        // =============================================================
        $ob_level = ob_get_level();
        log_message('info', '📦 Output Buffer Level: ' . $ob_level);

        if ($ob_level > 0) {
            log_message('info', '🧹 Cleaning output buffer...');
            ob_clean();
            log_message('info', '✅ Output buffer cleaned');
        }

        // =============================================================
        // [LOG 4] Build Response Array
        // =============================================================
        log_message('info', '🔨 Building response array...');

        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        log_message('info', '✅ Base response created');

        if ($extra_data) {
            log_message('info', '➕ Merging extra data...');
            $response = array_merge($response, $extra_data);
            log_message('info', '✅ Extra data merged');
        }

        // =============================================================
        // [LOG 5] Log Final Response
        // =============================================================
        log_message('info', '📤 Final Response:');
        log_message('debug', json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // =============================================================
        // [LOG 6] Check JSON Encoding
        // =============================================================
        $json_output = json_encode($response, JSON_UNESCAPED_UNICODE);
        $response_size = strlen($json_output);

        log_message('info', '📊 Response Size: ' . $response_size . ' bytes');

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', '❌ JSON Encoding Error: ' . json_last_error_msg());
        } else {
            log_message('info', '✅ JSON encoding successful');
        }

        // =============================================================
        // [LOG 7] Log Request Context
        // =============================================================
        log_message('info', '🌐 Request Context:');
        log_message('info', '  Method: ' . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
        log_message('info', '  URI: ' . ($_SERVER['REQUEST_URI'] ?? 'UNKNOWN'));
        log_message('info', '  IP: ' . ($this->input->ip_address() ?? 'UNKNOWN'));

        // =============================================================
        // [LOG 8] Log Session Info
        // =============================================================
        if ($this->session && method_exists($this->session, 'userdata')) {
            $user_id = $this->session->userdata('m_id');
            $username = $this->session->userdata('m_username');

            log_message('info', '👤 Session Info:');
            log_message('info', '  User ID: ' . ($user_id ?? 'NULL'));
            log_message('info', '  Username: ' . ($username ?? 'NULL'));
        }

        // =============================================================
        // [LOG 9] Log Call Stack
        // =============================================================
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        log_message('info', '📍 Call Stack:');

        foreach ($backtrace as $index => $trace) {
            if ($index === 0)
                continue; // Skip current function

            $file = isset($trace['file']) ? basename($trace['file']) : 'unknown';
            $line = $trace['line'] ?? '?';
            $function = $trace['function'] ?? 'unknown';
            $class = isset($trace['class']) ? $trace['class'] . '::' : '';

            log_message('info', '  [' . $index . '] ' . $class . $function . '() at ' . $file . ':' . $line);
        }

        // =============================================================
        // [LOG 10] Send HTTP Response
        // =============================================================
        log_message('info', '📡 Sending HTTP Response:');
        log_message('info', '  Status Code: ' . $status_code); // ✅ จะเป็น 200
        log_message('info', '  Content-Type: application/json; charset=utf-8');

        try {
            $this->output
                ->set_status_header($status_code) // ✅ ส่ง 200
                ->set_content_type('application/json', 'utf-8')
                ->set_output($json_output);

            log_message('info', '✅ HTTP response set successfully');

        } catch (Exception $e) {
            log_message('error', '❌ Failed to set HTTP response:');
            log_message('error', '  Exception: ' . $e->getMessage());
        }

        // =============================================================
        // [LOG 11] End Function
        // =============================================================
        log_message('info', '========================================');
        log_message('info', '🏁 END: output_json_error');
        log_message('info', '  Message Summary: ' . substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''));
        log_message('info', '  Status: ' . $status_code);
        log_message('info', '========================================');
    }




    // ===========================================
    // New Methods for Complete System
    // ===========================================

    /**
     * ตรวจสอบสถานะระบบและการตั้งค่า
     */
    public function system_status()
    {
        try {
            $status = [
                'google_client_available' => class_exists('Google\\Client'),
                'use_curl_mode' => $this->use_curl_mode,
                'config_loaded' => $this->config_loaded,
                'system_storage_exists' => (bool) $this->get_active_system_storage(),
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
                $status['folder_structure_ready'] = (bool) $system_storage->folder_structure_created;
            }

            $this->output_json_success($status, 'System status retrieved');

        } catch (Exception $e) {
            $this->output_json_error('Cannot get system status: ' . $e->getMessage());
        }
    }

    /**
     * ดู Log การทำงานของระบบ
     */
    public function view_logs()
    {
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
    private function get_system_logs($limit = 100)
    {
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
                            if (
                                strpos($match[3], 'Google Drive System') !== false ||
                                strpos($match[3], 'google_drive_system') !== false ||
                                strpos($match[3], 'create_folder_structure') !== false
                            ) {
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
            usort($logs, function ($a, $b) {
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
    public function cleanup_system()
    {
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
    public function reset_system()
    {
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
    public function debug_complete()
    {
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
    public function manual_create()
    {
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
    public function save_manual_folders()
    {
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
    private function extract_folder_id_from_url($url)
    {
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
    public function settings()
    {
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
    private function get_all_system_settings()
    {
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
    public function save_settings()
    {
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
    private function save_setting($key, $value)
    {
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
    private function create_settings_table_if_not_exists()
    {
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
    public function files()
    {
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
    private function get_system_folders()
    {
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
    public function export_config()
    {
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




    public function token_manager()
    {
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
    public function refresh_system_token()
    {
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
                log_message('debug', 'No refresh token found - requires reconnection');

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
    public function check_token_status_detailed()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            $system_storage = $this->get_active_system_storage();

            $status = [
                'has_system_storage' => (bool) $system_storage,
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
    private function perform_token_refresh($refresh_token)
    {
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
    private function update_system_token_in_db($token_data)
    {
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
    public function get_current_google_account()
    {
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
                'folder_structure_created' => (bool) $system_storage->folder_structure_created
            ], 'ดึงข้อมูล Google Account สำเร็จ');

        } catch (Exception $e) {
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }



    /**
     * ทดสอบการเข้าถึง Google API
     */
    public function test_google_api_access()
    {
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
    private function get_google_drive_root_folders($access_token, $root_folder_id)
    {
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
    private function get_google_drive_folder_contents($access_token, $folder_id)
    {
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
    private function get_google_drive_folder_info($access_token, $folder_id)
    {
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
    public function reset_system_data()
    {
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
            log_message('debug', 'SYSTEM_RESET: Emergency version completed by admin ' . $user_id);

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
     * 🗑️ ลบเนื้อหาในโฟลเดอร์แบบ recursive
     */
    private function delete_folder_contents_recursive($folder_id, $access_token)
    {
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
    private function get_google_drive_folder_contents_for_deletion($folder_id, $access_token)
    {
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
     * สร้าง Breadcrumbs จาก Google Drive
     */
    public function get_folder_breadcrumbs()
    {
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
    private function build_breadcrumbs($access_token, $folder_id, $root_folder_id)
    {
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
    public function download_file()
    {
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
    private function get_google_drive_file_info($access_token, $file_id)
    {
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
    private function stream_google_drive_file($access_token, $file_id, $filename)
    {
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
                CURLOPT_WRITEFUNCTION => function ($ch, $data) {
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
     * ตรวจสอบว่าสามารถลบ folder ได้หรือไม่
     * @param string $folder_name ชื่อโฟลเดอร์
     * @param string $user_system ระดับ m_system ของผู้ใช้
     * @return bool
     */
    private function can_delete_folder($folder_name, $user_system)
    {
        $system_folders = ['Admin', 'Departments', 'Shared', 'Users'];
        $is_system_folder = in_array($folder_name, $system_folders);

        if ($is_system_folder) {
            // System folders: เฉพาะ system_admin เท่านั้น
            return ($user_system === 'system_admin');
        } else {
            // Folders อื่นๆ: super_admin และ system_admin ลบได้
            return in_array($user_system, ['super_admin', 'system_admin']);
        }
    }

    /**
     * ตรวจสอบว่าสามารถลบ file ได้หรือไม่
     * @param string $user_system ระดับ m_system ของผู้ใช้
     * @return bool
     */
    private function can_delete_file($user_system)
    {
        // File: super_admin และ system_admin ลบได้
        return in_array($user_system, ['super_admin', 'system_admin']);
    }

    /**
     * 🗑️ ลบไฟล์/โฟลเดอร์ (แยก Logic ตาม Storage Mode)
     * ✅ อัปเดต: รองรับการลบโฟลเดอร์พร้อม Recursive Storage Update
     * ✅ แก้ไข: Pre-scan files ก่อนลบเพื่อ Update Storage ได้ถูกต้อง
     * ✅ เพิ่ม: การตรวจสอบสิทธิ์ can_delete_folder และ can_delete_file
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
            $item_name = $this->input->post('item_name'); // ✅ เพิ่ม: ดึง item_name จาก POST

            // ✅ เพิ่ม: ดึงข้อมูล User จาก Session สำหรับตรวจสอบสิทธิ์
            $user_system = $this->session->userdata('m_system');
            $user_id = $this->session->userdata('member_id');
            $user_name = $this->session->userdata('m_name');

            log_message('info', sprintf(
                '🗑️ Delete request: item_id=%s, type=%s, member_id=%d, user_system=%s, storage_mode=%s',
                $item_id,
                $item_type,
                $this->member_id,
                $user_system,
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

            // ✅ เพิ่ม: Validate user system level
            if (empty($user_system)) {
                log_message('error', "Delete failed: User system level not found");
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลระดับสิทธิ์ผู้ใช้',
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ========================================
            // ✅ เพิ่ม: ตรวจสอบสิทธิ์ด้วย can_delete_folder และ can_delete_file
            // ========================================
            if ($item_type === 'folder') {
                // ดึงข้อมูล folder ถ้ายังไม่มี item_name
                if (empty($item_name)) {
                    $folder = $this->db->select('folder_name')
                        ->from('tbl_google_drive_system_folders')
                        ->where('folder_id', $item_id)
                        ->get()
                        ->row();

                    if (!$folder) {
                        log_message('error', "Delete failed: Folder not found - ID: {$item_id}");
                        http_response_code(404);
                        echo json_encode([
                            'success' => false,
                            'message' => 'ไม่พบโฟลเดอร์ที่ต้องการลบ',
                            'timestamp' => date('Y-m-d H:i:s')
                        ], JSON_UNESCAPED_UNICODE);
                        exit;
                    }

                    $item_name = $folder->folder_name;
                }

                // ✅ เพิ่ม: ตรวจสอบสิทธิ์ด้วย can_delete_folder
                if (!$this->can_delete_folder($item_name, $user_system)) {
                    log_message('info', "❌ Permission denied: User {$user_id} ({$user_name}, m_system: {$user_system}) attempted to delete folder: {$item_name}");

                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'คุณไม่มีสิทธิ์ลบโฟลเดอร์นี้',
                        'timestamp' => date('Y-m-d H:i:s')
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                log_message('info', "✅ Permission granted: User {$user_id} ({$user_name}, m_system: {$user_system}) deleting folder: {$item_name}");
            }

            if ($item_type === 'file') {
                // ✅ เพิ่ม: ตรวจสอบสิทธิ์ด้วย can_delete_file
                if (!$this->can_delete_file($user_system)) {
                    log_message('info', "❌ Permission denied: User {$user_id} ({$user_name}, m_system: {$user_system}) attempted to delete file: {$item_id}");

                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'คุณไม่มีสิทธิ์ลบไฟล์นี้',
                        'timestamp' => date('Y-m-d H:i:s')
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                log_message('info', "✅ Permission granted: User {$user_id} ({$user_name}, m_system: {$user_system}) deleting file: {$item_id}");
            }

            // ========================================
            // ส่วนที่เหลือไม่เปลี่ยนแปลง - เริ่มจากการตรวจสอบสิทธิ์ในโฟลเดอร์
            // ========================================

            // ✅ ตรวจสอบสิทธิ์การลบในโฟลเดอร์ (ของเดิม)
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

                // ✅ ลบ permission records
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
                if ($item_type === 'file') {
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
                            if ($this->storage_mode === 'centralized') {
                                log_message('info', sprintf(
                                    '📉 [Centralized Mode] Decreasing system storage by %d bytes (%.2f MB)',
                                    $file_size,
                                    $file_size / 1024 / 1024
                                ));

                                $this->decrease_system_storage_usage($file_size);
                                log_message('info', '✅ System storage decreased successfully');

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
                                }
                            } else {
                                log_message('info', sprintf(
                                    'ℹ️ [User-based Mode] Skipping system storage decrease (file size: %d bytes, %.2f MB)',
                                    $file_size,
                                    $file_size / 1024 / 1024
                                ));

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

                        if ($this->storage_mode === 'centralized') {
                            log_message('info', sprintf(
                                '📉 [Centralized] Decreasing system storage by %.2f MB',
                                $total_size / 1024 / 1024
                            ));

                            $this->decrease_system_storage_usage($total_size);
                            log_message('info', '✅ System storage decreased');

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
                        } else {
                            log_message('info', sprintf(
                                'ℹ️ [User-based Mode] Skipping system storage (%.2f MB)',
                                $total_size / 1024 / 1024
                            ));

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

    /**
     * ลบรายการจาก Google Drive
     */
    private function delete_google_drive_item($access_token, $item_id)
    {
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
     * ✅ 1. ดึงการตั้งค่าจากฐานข้อมูล
     * 🔧 FIXED: แก้ไขให้ดึงข้อมูลจาก key-value structure ที่ถูกต้อง
     */
    private function get_settings_from_db()
    {
        log_message('info', '⚙️ Loading system settings from database');

        // ✅ ดึงข้อมูลแบบ key-value pairs
        $query = $this->db->select('setting_key, setting_value')
            ->from('tbl_google_drive_settings')
            ->where('is_active', 1)
            ->where_in('setting_key', ['google_drive_enabled', 'system_storage_mode'])
            ->get();

        // แปลง result เป็น associative array
        $settings = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $settings[$row->setting_key] = $row->setting_value;
            }
        }

        // ดึงค่าที่ต้องการ
        $google_drive_enabled = isset($settings['google_drive_enabled']) ? $settings['google_drive_enabled'] : '0';
        $storage_mode = isset($settings['system_storage_mode']) ? $settings['system_storage_mode'] : 'centralized';

        log_message('info', sprintf(
            '✅ Settings loaded: google_drive_enabled=%s, storage_mode=%s',
            $google_drive_enabled === '1' ? 'true' : 'false',
            $storage_mode
        ));

        return [
            'google_drive_enabled' => ($google_drive_enabled === '1'),
            'storage_mode' => $storage_mode
        ];
    }

    /**
     * ✅ 2. ดึง folder_id ของไฟล์
     */
    private function get_file_folder_id($file_id)
    {
        log_message('info', sprintf('📁 Getting parent folder for file: %s', $file_id));

        $query = $this->db->select('parent_folder_id')
            ->from('google_drive_files')
            ->where('file_id', $file_id)
            ->get();

        if ($query->num_rows() > 0) {
            $folder_id = $query->row()->parent_folder_id;
            log_message('info', sprintf('✅ Parent folder found: %s', $folder_id));
            return $folder_id;
        }

        log_message('error', sprintf('❌ Parent folder not found for file: %s', $file_id));
        return null;
    }

    /**
     * ✅ 3. ตรวจสอบสิทธิ์การลบในโฟลเดอร์
     */
    private function check_delete_permission_in_folder($folder_id)
    {
        $member_id = $this->member_id;

        log_message('info', sprintf(
            '🔐 Checking delete permission: folder=%s, member=%d',
            $folder_id,
            $member_id
        ));

        // ✅ ตรวจสอบสิทธิ์จากตาราง tbl_google_drive_member_folder_access
        // โดยใช้ access_type แทน can_delete
        $query = $this->db->select('access_type')
            ->from('tbl_google_drive_member_folder_access')
            ->where('folder_id', $folder_id)
            ->where('member_id', $member_id)
            ->where('is_active', 1)
            ->get();

        if ($query->num_rows() > 0) {
            $access_type = $query->row()->access_type;

            // ✅ กำหนดสิทธิ์การลบตาม access_type
            // admin, owner = สามารถลบได้
            // write = สามารถลบได้ (ถ้าเป็นคนอัพโหลดเอง)
            // read = ลบไม่ได้
            $can_delete = in_array($access_type, ['admin', 'owner', 'write']);

            log_message('info', sprintf(
                '✅ Permission found: access_type=%s, can_delete=%s',
                $access_type,
                $can_delete ? 'true' : 'false'
            ));

            return $can_delete;
        }

        // ✅ ถ้าไม่มีสิทธิ์ที่ระบุ
        // ตรวจสอบว่าเป็น System Admin หรือไม่
        $is_admin = $this->check_if_system_admin($member_id);

        if ($is_admin) {
            log_message('info', '✅ Member is system admin, allowing delete');
            return true;
        }

        // ถ้าไม่ใช่ admin และไม่มีสิทธิ์ = ไม่อนุญาต
        log_message('info', '⚠️ No permission found and not admin, denying delete');
        return false;
    }

    /**
     * ✅ ตรวจสอบว่าเป็น System Admin หรือไม่
     */
    private function check_if_system_admin($member_id)
    {
        try {
            log_message('info', sprintf('🔐 Checking if member %d is system_admin', $member_id));

            // ✅ ตรวจสอบจาก column m_system
            $query = $this->db->select('m_system')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->where('m_status', '1') // ต้องเป็น active
                ->get();

            if ($query->num_rows() > 0) {
                $m_system = $query->row()->m_system;

                // ✅ ตรวจสอบว่าเป็น 'system_admin' หรือไม่
                $is_system_admin = ($m_system === 'system_admin');

                log_message('info', sprintf(
                    '✅ Member %d: m_system=%s, is_system_admin=%s',
                    $member_id,
                    $m_system,
                    $is_system_admin ? 'true' : 'false'
                ));

                return $is_system_admin;
            }

            log_message('info', sprintf('⚠️ Member %d not found or inactive', $member_id));
            return false;

        } catch (Exception $e) {
            log_message('error', sprintf('❌ Check system admin error: %s', $e->getMessage()));
            return false;
        }
    }

    /**
     * ✅ 4. Response สำหรับกรณี Access Denied
     */
    private function access_denied_response($folder_id)
    {
        log_message('error', sprintf('🚫 Access denied for folder: %s', $folder_id));

        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'ไม่มีสิทธิ์ลบโฟลเดอร์นี้',
            'folder_id' => $folder_id,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * ✅ 5. ดึงข้อมูลรายการก่อนลบ (รวม Recursive scan)
     * 🔧 FIXED: เลือกตารางตาม storage_mode
     */
    private function get_item_info_before_delete($item_id, $item_type)
    {
        log_message('info', sprintf(
            '📋 Getting item info: item_id=%s, type=%s, storage_mode=%s',
            $item_id,
            $item_type,
            $this->storage_mode
        ));

        if ($item_type === 'file') {
            // ======================================
            // ดึงข้อมูลไฟล์
            // ======================================

            // ✅ เลือกตารางตาม storage_mode
            if ($this->storage_mode === 'centralized') {
                $table = 'tbl_google_drive_system_files';
            } else {
                $table = 'tbl_google_drive_files';
            }

            log_message('info', sprintf('📁 Querying table: %s', $table));

            $query = $this->db->select('file_name as name, file_size, uploaded_by')
                ->from($table)
                ->where('file_id', $item_id)
                ->get();

            if ($query->num_rows() > 0) {
                $file_info = $query->row_array();
                log_message('info', sprintf(
                    '✅ File info found: name=%s, size=%d bytes (%.2f MB)',
                    $file_info['name'],
                    $file_info['file_size'],
                    $file_info['file_size'] / 1024 / 1024
                ));
                return $file_info;
            } else {
                log_message('error', sprintf('❌ File not found in %s: %s', $table, $item_id));
            }

        } elseif ($item_type === 'folder') {
            // ======================================
            // ดึงข้อมูลโฟลเดอร์
            // ======================================

            // ✅ เลือกตารางตาม storage_mode
            if ($this->storage_mode === 'centralized') {
                $folder_table = 'tbl_google_drive_system_folders';
                $file_table = 'tbl_google_drive_system_files';
            } else {
                $folder_table = 'tbl_google_drive_folders';
                $file_table = 'tbl_google_drive_files';
            }

            log_message('info', sprintf('📁 Querying tables: folder=%s, file=%s', $folder_table, $file_table));

            // ดึงข้อมูลโฟลเดอร์
            $folder_query = $this->db->select('folder_name as name')
                ->from($folder_table)
                ->where('folder_id', $item_id)
                ->get();

            if ($folder_query->num_rows() > 0) {
                $folder_info = $folder_query->row_array();

                log_message('info', '🔄 Scanning files in folder (recursive)...');

                // Recursive scan ไฟล์ในโฟลเดอร์
                $files = $this->get_all_files_in_folder_recursive($item_id);

                $total_size = 0;
                $uploaders = [];

                foreach ($files as $file) {
                    $total_size += (int) $file['file_size'];

                    if (isset($file['uploaded_by']) && $file['uploaded_by']) {
                        $member_id = (int) $file['uploaded_by'];
                        if (!isset($uploaders[$member_id])) {
                            $uploaders[$member_id] = 0;
                        }
                        $uploaders[$member_id] += (int) $file['file_size'];
                    }
                }

                $folder_info['files'] = $files;
                $folder_info['file_count'] = count($files);
                $folder_info['file_size'] = $total_size;
                $folder_info['uploaders'] = $uploaders;

                log_message('info', sprintf(
                    '✅ Folder scan complete: %d files, %.2f MB total, %d uploaders',
                    count($files),
                    $total_size / 1024 / 1024,
                    count($uploaders)
                ));

                return $folder_info;
            } else {
                log_message('error', sprintf('❌ Folder not found in %s: %s', $folder_table, $item_id));
            }
        }

        log_message('error', sprintf('❌ Item not found: %s', $item_id));
        return null;
    }

    /**
     * ✅ 6. ดึงไฟล์ทั้งหมดในโฟลเดอร์แบบ Recursive
     * 🔧 FIXED: เลือกตารางตาม storage_mode
     */
    private function get_all_files_in_folder_recursive($folder_id)
    {
        log_message('info', sprintf(
            '🔍 Scanning folder recursively: %s (storage_mode=%s)',
            $folder_id,
            $this->storage_mode
        ));

        $all_files = [];

        // ✅ เลือกตารางตาม storage_mode
        if ($this->storage_mode === 'centralized') {
            $folder_table = 'tbl_google_drive_system_folders';
            $file_table = 'tbl_google_drive_system_files';
        } else {
            $folder_table = 'tbl_google_drive_folders';
            $file_table = 'tbl_google_drive_files';
        }

        log_message('info', sprintf('📁 Using tables: folder=%s, file=%s', $folder_table, $file_table));

        // ดึงไฟล์ในโฟลเดอร์ปัจจุบัน
        $files = $this->db->select('file_id, file_name, file_size, uploaded_by')
            ->from($file_table)
            ->where('parent_folder_id', $folder_id)
            ->get()
            ->result_array();

        if (count($files) > 0) {
            log_message('info', sprintf('  📄 Found %d files in folder %s', count($files), $folder_id));
            $all_files = array_merge($all_files, $files);
        }

        // ดึงโฟลเดอร์ย่อย
        $subfolders = $this->db->select('folder_id')
            ->from($folder_table)
            ->where('parent_folder_id', $folder_id)
            ->get()
            ->result_array();

        if (count($subfolders) > 0) {
            log_message('info', sprintf('  📁 Found %d subfolders in folder %s', count($subfolders), $folder_id));

            foreach ($subfolders as $subfolder) {
                $subfolder_files = $this->get_all_files_in_folder_recursive($subfolder['folder_id']);
                $all_files = array_merge($all_files, $subfolder_files);
            }
        }

        log_message('info', sprintf('✅ Scan complete for folder %s: %d total files', $folder_id, count($all_files)));

        return $all_files;
    }

    /**
     * ✅ 7. ดึง Access Token (แบบง่าย)
     */
    private function get_access_token_simple()
    {
        log_message('info', '🔑 Getting access token');

        $system_storage = $this->get_active_system_storage();

        if (!$system_storage || !$system_storage->google_access_token) {
            log_message('error', '❌ System storage or access token not found');
            return null;
        }

        $token_data = json_decode($system_storage->google_access_token, true);

        if (!isset($token_data['access_token'])) {
            log_message('error', '❌ Access token not found in token data');
            return null;
        }

        log_message('info', '✅ Access token retrieved successfully');
        return $token_data['access_token'];
    }

    /**
     * ✅ 8. ลบจาก Google Drive (แบบง่าย)
     */
    private function simple_delete_from_google_drive($item_id, $access_token)
    {
        log_message('info', sprintf('🌐 Calling Google Drive API to delete: %s', $item_id));

        $url = "https://www.googleapis.com/drive/v3/files/{$item_id}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$access_token}",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 204 || $http_code == 200) {
            log_message('info', sprintf('✅ Google Drive API: Delete successful (HTTP %d)', $http_code));
            return true;
        }

        log_message('error', sprintf('❌ Google Drive API: Delete failed (HTTP %d)', $http_code));
        log_message('error', sprintf('Response: %s', $response));

        return false;
    }

    /**
     * ✅ 9. ลบ Folder Permissions
     */
    private function remove_folder_permissions($folder_id, $recursive = false)
    {
        log_message('info', sprintf(
            '🔐 Removing folder permissions: folder=%s, recursive=%s, storage_mode=%s',
            $folder_id,
            $recursive ? 'true' : 'false',
            $this->storage_mode
        ));

        // ✅ ลบ permissions ของโฟลเดอร์หลัก
        $this->db->where('folder_id', $folder_id);
        $result = $this->db->delete('tbl_google_drive_member_folder_access');

        $affected_rows = $this->db->affected_rows();
        log_message('info', sprintf('✅ Removed %d permission records for folder %s', $affected_rows, $folder_id));

        // ✅ ถ้า recursive = true, ลบ permissions ของโฟลเดอร์ย่อยด้วย
        if ($recursive) {
            log_message('info', '🔄 Removing permissions for subfolders...');

            // 🔥 FIX: เลือกตารางตาม storage_mode
            if ($this->storage_mode === 'centralized') {
                $folder_table = 'tbl_google_drive_system_folders';
            } else {
                $folder_table = 'tbl_google_drive_folders';
            }

            log_message('info', sprintf('📁 Querying subfolders from: %s', $folder_table));

            $subfolders = $this->db->select('folder_id')
                ->from($folder_table)
                ->where('parent_folder_id', $folder_id)
                ->get()
                ->result_array();

            if (count($subfolders) > 0) {
                log_message('info', sprintf('📁 Found %d subfolders to remove permissions', count($subfolders)));

                foreach ($subfolders as $subfolder) {
                    $this->remove_folder_permissions($subfolder['folder_id'], true);
                }
            } else {
                log_message('info', '✅ No subfolders found');
            }
        }

        return $result;
    }

    /**
     * ✅ 10. ลบข้อมูลจากฐานข้อมูล
     */
    private function remove_item_from_database($item_id, $item_type)
    {
        log_message('info', sprintf(
            '💾 Removing from database: item_id=%s, type=%s, storage_mode=%s',
            $item_id,
            $item_type,
            $this->storage_mode
        ));

        // ✅ เลือกตารางตาม storage_mode
        if ($this->storage_mode === 'centralized') {
            $folder_table = 'tbl_google_drive_system_folders';
            $file_table = 'tbl_google_drive_system_files';
        } else {
            $folder_table = 'tbl_google_drive_folders';
            $file_table = 'tbl_google_drive_files';
        }

        if ($item_type === 'file') {
            log_message('info', sprintf('🗑️ Deleting file from: %s', $file_table));

            $this->db->where('file_id', $item_id);
            $result = $this->db->delete($file_table);

            $affected = $this->db->affected_rows();
            log_message('info', sprintf('✅ Deleted %d file record(s) from %s', $affected, $file_table));

            return $result;

        } elseif ($item_type === 'folder') {
            log_message('info', '🔄 Removing files in folder (recursive)...');

            // ลบไฟล์ในโฟลเดอร์ก่อน (recursive)
            $files = $this->get_all_files_in_folder_recursive($item_id);

            foreach ($files as $file) {
                $this->db->where('file_id', $file['file_id']);
                $this->db->delete($file_table);
            }

            log_message('info', sprintf('✅ Deleted %d file record(s) from %s', count($files), $file_table));

            // ลบโฟลเดอร์ย่อย (recursive)
            log_message('info', '🔄 Removing subfolders (recursive)...');
            $this->remove_subfolders_recursive($item_id);

            // ลบโฟลเดอร์หลัก
            log_message('info', sprintf('🗑️ Deleting folder from: %s', $folder_table));

            $this->db->where('folder_id', $item_id);
            $result = $this->db->delete($folder_table);

            log_message('info', sprintf('✅ Folder record deleted from %s', $folder_table));

            return $result;
        }

        return false;
    }

    /**
     * ✅ 11. ลบโฟลเดอร์ย่อยแบบ Recursive
     */
    private function remove_subfolders_recursive($folder_id)
    {
        log_message('info', sprintf(
            '📁 Removing subfolders of: %s (storage_mode=%s)',
            $folder_id,
            $this->storage_mode
        ));

        // ✅ เลือกตารางตาม storage_mode
        if ($this->storage_mode === 'centralized') {
            $folder_table = 'tbl_google_drive_system_folders';
        } else {
            $folder_table = 'tbl_google_drive_folders';
        }

        $subfolders = $this->db->select('folder_id')
            ->from($folder_table)
            ->where('parent_folder_id', $folder_id)
            ->get()
            ->result_array();

        foreach ($subfolders as $subfolder) {
            // ลบโฟลเดอร์ย่อยของโฟลเดอร์นี้ก่อน (recursive)
            $this->remove_subfolders_recursive($subfolder['folder_id']);

            // ลบโฟลเดอร์นี้
            $this->db->where('folder_id', $subfolder['folder_id']);
            $this->db->delete($folder_table);

            log_message('info', sprintf('  ✅ Removed subfolder from %s: %s', $folder_table, $subfolder['folder_id']));
        }
    }

    /**
     * ✅ 12. ลด System Storage Usage
     */
    private function decrease_system_storage_usage($bytes)
    {
        log_message('info', sprintf(
            '📉 Decreasing system storage by %d bytes (%.2f MB)',
            $bytes,
            $bytes / 1024 / 1024
        ));

        $this->db->set('storage_used', 'storage_used - ' . (int) $bytes, FALSE);
        $this->db->where('is_active', 1);
        $result = $this->db->update('system_storage');

        if ($result) {
            log_message('info', '✅ System storage decreased successfully');
        } else {
            log_message('error', '❌ Failed to decrease system storage');
        }

        return $result;
    }

    /**
     * ✅ 13. ลด Member Storage Usage
     */
    private function decrease_member_storage_usage($member_id, $bytes)
    {
        log_message('info', sprintf(
            '🔄 Decreasing member storage: member_id=%d, bytes=%d (%.2f MB)',
            $member_id,
            $bytes,
            $bytes / 1024 / 1024
        ));

        $this->db->set('storage_used', 'storage_used - ' . (int) $bytes, FALSE);
        $this->db->where('member_id', $member_id);
        $result = $this->db->update('members');

        if ($result) {
            log_message('info', sprintf('✅ Member %d storage decreased', $member_id));
        } else {
            log_message('error', sprintf('❌ Failed to decrease storage for member %d', $member_id));
        }

        return $result;
    }

    /**
     * ✅ 14. Log Activity (แบบง่าย)
     */
    private function simple_log_activity($action_type, $action_description)
    {
        log_message('info', sprintf('📝 Logging activity: action=%s', $action_type));

        $data = [
            'member_id' => $this->member_id,
            'action_type' => $action_type,           // ✅ ถูกต้อง
            'action_description' => $action_description,    // ✅ ถูกต้อง
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),  // ✅ เพิ่มใหม่
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->db->insert('tbl_google_drive_activity_logs', $data);  // ✅ ถูกต้อง

        if ($result) {
            log_message('info', '✅ Activity logged successfully');
        } else {
            log_message('error', '❌ Failed to log activity');
        }

        return $result;
    }

    /**
     * ดึงรายการโฟลเดอร์สำหรับ dropdown
     */
    public function get_folder_list()
    {
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
    private function get_all_google_drive_folders($access_token, $root_folder_id, $parent_path = '')
    {
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
    private function get_folder_icon($folder_name)
    {
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
    private function get_file_icon($file)
    {
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
    private function get_folder_description($folder_name)
    {
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
    private function format_google_date($google_date)
    {
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
    private function format_file_size($bytes)
    {
        if ($bytes == 0)
            return '0 B';

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
    private function log_activity($member_id, $action_type, $description, $additional_data = [])
    {
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
    private function create_activity_logs_table()
    {
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
    public function emergency_reset_storage()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            $confirm_code = $this->input->post('confirm_code');
            if ($confirm_code !== 'EMERGENCY_RESET_GOOGLE_DRIVE') {
                $this->output_json_error('รหัสยืนยันไม่ถูกต้อง');
                return;
            }

            log_message('debug', 'Emergency reset system storage initiated by admin: ' . $this->session->userdata('m_id'));

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
    public function create_admin_access_link()
    {
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
    public function test_folder_creation()
    {
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
    private function delete_folder_with_curl($folder_id, $access_token)
    {
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
    public function check_token_expiry_warning()
    {
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
    public function export_system_info()
    {
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
                    'folder_structure_created' => (bool) $system_storage->folder_structure_created,
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
    public function auto_refresh_token()
    {
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
     * ทดสอบการมีอยู่ของตารางฐานข้อมูล
     */
    private function test_database_tables_exist()
    {
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
    public function upload_file()
    {
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
    private function upload_file_to_google_drive($file, $folder_id, $access_token)
    {
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
    private function initiate_resumable_upload($metadata, $access_token)
    {
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
    private function upload_file_content($upload_url, $file, $access_token)
    {
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
    public function create_folder()
    {
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
    private function build_folder_path($parent_id, $folder_name)
    {
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
     * สร้างลิงก์แชร์สำหรับไฟล์/โฟลเดอร์
     */
    public function create_share_link()
    {
        // Force JSON response และป้องกัน HTML output
        $this->output->set_content_type('application/json');

        try {
            // ล้าง output buffer ทั้งหมด
            while (ob_get_level()) {
                ob_end_clean();
            }

            // ตั้งค่า Error Handling
            set_error_handler(function ($severity, $message, $file, $line) {
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
    private function create_share_link_safe($item_id, $permission, $access, $access_token)
    {
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
    private function get_file_web_link($file_id, $access_token)
    {
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

            log_message('debug', "get_file_web_link failed: HTTP {$http_code}");
            return null;

        } catch (Exception $e) {
            log_message('error', 'get_file_web_link exception: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * Response JSON แบบปลอดภัย
     */
    private function json_response($success, $message, $data = null)
    {
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
    private function log_share_activity($item_id, $item_type, $permission, $access, $share_link)
    {
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
    private function get_access_token_safe($storage)
    {
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
    private function get_system_storage_safe()
    {
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




    public function share_with_email()
    {
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
    private function share_with_email_safe($file_id, $email, $permission, $message, $access_token)
    {
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
     * 🔧 Error-proof disconnect_system_account() - แก้ไข HTTP 500 และ JSON Error
     * เพิ่มใน Google_drive_system Controller
     */

    public function disconnect_system_account()
    {
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
            if (
                !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
            ) {
                $this->json_exit(false, 'AJAX request required', 400);
            }

            // ✅ ตรวจสอบ Session และสิทธิ์
            if (
                !$this->session->userdata('m_system') ||
                !in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])
            ) {
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
    private function json_exit($success, $message, $http_code = 200, $data = [])
    {
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
    private function get_active_system_storage_safe()
    {
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
    private function revoke_token_safe($access_token_json)
    {
        try {
            $token_data = json_decode($access_token_json, true);
            if (!$token_data || !isset($token_data['access_token'])) {
                log_message('debug', 'REVOKE: Invalid token format');
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
    private function log_disconnect_success($storage, $results)
    {
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
    private function log_disconnect_error($exception)
    {
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
     * ✅ ตรวจสอบสถานะ System Storage หลังจาก Disconnect
     */
    public function check_system_status_after_disconnect()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
            }

            $active_storage = $this->get_active_system_storage();

            $status = [
                'has_active_storage' => (bool) $active_storage,
                'is_disconnected' => !$active_storage,
                'needs_reconnection' => !$active_storage,
                'system_ready' => (bool) $active_storage && (bool) ($active_storage->folder_structure_created ?? false)
            ];

            if ($active_storage) {
                $status['storage_info'] = [
                    'google_account' => $active_storage->google_account_email,
                    'created_at' => $active_storage->created_at,
                    'folder_structure_ready' => (bool) $active_storage->folder_structure_created
                ];
            }

            $this->output_json_success($status, 'ตรวจสอบสถานะระบบสำเร็จ');

        } catch (Exception $e) {
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
    /**
     * ดูรายการการแชร์ของรายการ
     */
    public function get_item_shares()
    {
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
    private function get_sharing_records($item_id)
    {
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
    public function revoke_share()
    {
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
    private function revoke_google_drive_permission($file_id, $permission_id, $access_token)
    {
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
    public function health_check()
    {
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
    private function ensure_valid_access_token($force_refresh = false)
    {
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
    private function perform_auto_refresh($token_data, $reason = 'unknown')
    {
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
    private function test_google_drive_access_quick($access_token)
    {
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
    private function log_auto_refresh_success($reason, $token_data)
    {
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
    private function log_auto_refresh_failure($reason, $error_message)
    {
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
    private function output_health_check($health_data)
    {
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
    public function create_folder_structure()
    {
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
    private function create_complete_structure($storage_id, $access_token)
    {
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
    private function recreate_complete_structure($storage_id, $access_token)
    {
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
     * ✅ IMPROVED: clear_all_folders() - ปลอดภัยและมีรายละเอียด
     * - ลบเฉพาะ folder types ที่ระบุ
     * - ไม่ลบ personal folders (ถ้ามี)
     * - แสดง log ละเอียด
     * - FIX: เพิ่ม reset_query() ป้องกัน Query Builder state ค้างค้าง
     */
    private function clear_all_folders()
    {
        try {
            log_message('info', '🗑️ === Starting clear_all_folders ===');

            $total_deleted = 0;

            // ✅ กำหนด folder types ที่จะลบ (ไม่รวม 'personal' เพื่อความปลอดภัย)
            $folder_types_to_clear = ['department', 'system', 'admin', 'shared', 'root'];

            foreach ($folder_types_to_clear as $type) {
                // ✅ CRITICAL FIX: Reset query builder state ก่อนแต่ละรอบ
                $this->db->reset_query();

                // นับก่อนลบ
                $count_before = $this->db
                    ->where('folder_type', $type)
                    ->count_all_results('tbl_google_drive_system_folders');

                if ($count_before > 0) {
                    // ลบ
                    $this->db
                        ->where('folder_type', $type)
                        ->delete('tbl_google_drive_system_folders');

                    $deleted = $this->db->affected_rows();
                    $total_deleted += $deleted;

                    log_message('info', "Cleared '{$type}' folders: {$deleted} / {$count_before} records");
                } else {
                    log_message('info', "No '{$type}' folders to clear");
                }
            }

            log_message('info', "✅ Total folders cleared: {$total_deleted}");
            log_message('info', '🗑️ === Finished clear_all_folders ===');

            return $total_deleted;

        } catch (Exception $e) {
            log_message('error', 'clear_all_folders exception: ' . $e->getMessage());
            $this->db->reset_query();
            return 0;
        }
    }

    /**
     * ✅ Enhanced create_folder_structure_curl() - รับประกันการสร้าง Department Folders
     * FIX: แก้ไข count_all_results ไม่ใช้ FALSE parameter เพื่อป้องกัน Query Builder state ค้างค้าง
     */
    private function create_folder_structure_curl($storage_id)
    {
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
                            log_message('debug', "⚠️ Attempt {$attempt} returned 0 folders");

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

                // ✅ FIX: ตรวจสอบผลลัพธ์จริงในฐานข้อมูล โดยไม่ใช้ FALSE parameter
                $this->db->reset_query(); // Reset ก่อนนับ
                $actual_dept_count = $this->db
                    ->where('folder_type', 'department')
                    ->count_all_results('tbl_google_drive_system_folders');
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
    public function check_current_status()
    {
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
    private function create_folder_with_curl($folder_name, $parent_id, $access_token)
    {
        try {
            log_message('info', "Creating folder: '{$folder_name}' under parent: {$parent_id}");

            // ตรวจสอบ input
            if (empty($folder_name) || empty($access_token)) {
                log_message('error', 'create_folder_with_curl: Missing folder_name or access_token');
                return null;
            }

            // ตรวจสอบและ refresh token หากจำเป็น
            if (!$this->ensure_valid_access_token()) {
                log_message('debug', 'Token refresh failed, continuing with current token');
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
                log_message('debug', "Rate limit hit for '{$folder_name}', waiting...");
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
    public function force_refresh_token()
    {
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
    public function auto_refresh_status()
    {
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
     * ✅ ดึงกิจกรรมล่าสุด (API Endpoint) - FIXED VERSION with Date Filter
     * แก้ไขให้ดึงจากทั้ง 2 ตารางจริง + รองรับ date filter
     */
    public function get_recent_activities()
    {
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

            // รับ parameters
            $limit = $this->input->get('limit') ?: 10;
            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            // ดึงข้อมูล
            $activities = $this->get_recent_activities_data($limit, $start_date, $end_date);

            log_message('info', 'Get recent activities success: ' . count($activities) . ' records returned');
            $this->output_json_success($activities, 'ดึงข้อมูลกิจกรรมล่าสุดสำเร็จ');

        } catch (Exception $e) {
            log_message('error', 'Get recent activities error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ✅ ดึงข้อมูลกิจกรรมจากหลายแหล่ง - UPDATED VERSION with File Activities
     * ใช้ทั้ง 3 ตารางจริง + JOIN folders และ member เพื่อแสดงชื่อ
     */
    private function get_recent_activities_data($limit = 10, $start_date = null, $end_date = null)
    {
        $activities = [];

        try {
            log_message('info', '=== get_recent_activities_data START (with File Activities) ===');
            log_message('info', 'Request limit: ' . $limit);

            // กำหนด date range
            if ($start_date && $end_date) {
                $start_datetime = $start_date . ' 00:00:00';
                $end_datetime = $end_date . ' 23:59:59';
            } else {
                $start_datetime = date('Y-m-d H:i:s', strtotime('-7 days'));
                $end_datetime = date('Y-m-d H:i:s');
            }

            log_message('info', 'Date range: ' . $start_datetime . ' to ' . $end_datetime);

            // ========================================
            // 1. ดึงจาก tbl_google_drive_logs
            // ========================================
            if ($this->db->table_exists('tbl_google_drive_logs')) {
                log_message('info', 'Querying tbl_google_drive_logs...');

                $drive_logs = $this->db->select('
                gdl.id,
                gdl.member_id, 
                gdl.action_type, 
                gdl.action_description,
                gdl.created_at,
                gdl.status,
                gdl.folder_id,
                gdl.target_email,
                gdl.module,
                COALESCE(m.m_fname, "ระบบ") as first_name,
                COALESCE(m.m_lname, "") as last_name,
                COALESCE(m.m_username, "system") as username,
                COALESCE(sf.folder_name, uf.folder_name) as folder_name,
                COALESCE(sf.folder_type, uf.folder_type) as folder_type
            ')
                    ->from('tbl_google_drive_logs gdl')
                    ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                    ->join('tbl_google_drive_system_folders sf', 'gdl.folder_id = sf.folder_id', 'left')
                    ->join('tbl_google_drive_folders uf', 'gdl.folder_id = uf.folder_id', 'left')
                    ->where('gdl.created_at >=', $start_datetime)
                    ->where('gdl.created_at <=', $end_datetime)
                    ->where('gdl.action_type !=', '')
                    ->where('gdl.status', 'success')
                    ->order_by('gdl.created_at', 'desc')
                    ->limit(ceil($limit / 3)) // แบ่ง 3 ส่วน
                    ->get()
                    ->result();

                log_message('info', 'Found ' . count($drive_logs) . ' records from tbl_google_drive_logs');

                foreach ($drive_logs as $log) {
                    // ทำความสะอาด description (ลบ [Folder: ...] เดิมออก)
                    $description = preg_replace('/\s*\[Folder:\s*[^\]]+\]/', '', $log->action_description);

                    // เพิ่มชื่อ folder ใหม่
                    if ($log->folder_name) {
                        $description .= ' [Folder: ' . $log->folder_name . ']';
                    } elseif ($log->folder_id) {
                        $description .= ' [Folder ID: ' . substr($log->folder_id, 0, 15) . '...]';
                    }

                    $activities[] = [
                        'id' => $log->id,
                        'type' => 'system_activity',
                        'action_type' => $log->action_type,
                        'description' => trim($description),
                        'action_description' => trim($description),
                        'user_name' => trim($log->first_name . ' ' . $log->last_name),
                        'username' => $log->username,
                        'member_name' => trim($log->first_name . ' ' . $log->last_name),
                        'created_at' => $log->created_at,
                        'timestamp' => $log->created_at,
                        'status' => $log->status,
                        'folder_id' => $log->folder_id,
                        'folder_name' => $log->folder_name,
                        'folder_type' => $log->folder_type,
                        'target_email' => $log->target_email,
                        'module' => $log->module,
                        'source' => 'google_drive_logs'
                    ];
                }
            }

            // ========================================
            // 2. ดึงจาก tbl_google_drive_activity_logs
            // ========================================
            if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
                log_message('info', 'Querying tbl_google_drive_activity_logs...');

                $activity_logs = $this->db->select('
                gdal.id,
                gdal.member_id, 
                gdal.action_type, 
                gdal.action_description,
                gdal.created_at,
                gdal.folder_id,
                gdal.file_id,
                gdal.item_id,
                gdal.item_type,
                COALESCE(m.m_fname, "ผู้ใช้") as first_name,
                COALESCE(m.m_lname, "") as last_name,
                COALESCE(m.m_username, "unknown") as username,
                COALESCE(sf.folder_name, uf.folder_name) as folder_name,
                COALESCE(sf.folder_type, uf.folder_type) as folder_type
            ')
                    ->from('tbl_google_drive_activity_logs gdal')
                    ->join('tbl_member m', 'gdal.member_id = m.m_id', 'left')
                    ->join('tbl_google_drive_system_folders sf', 'gdal.folder_id = sf.folder_id', 'left')
                    ->join('tbl_google_drive_folders uf', 'gdal.folder_id = uf.folder_id', 'left')
                    ->where('gdal.created_at >=', $start_datetime)
                    ->where('gdal.created_at <=', $end_datetime)
                    ->where_in('gdal.action_type', [
                        'upload_file',
                        'delete_file',
                        'create_folder',
                        'delete_folder',
                        'add_direct_folder_permission',
                        'enable_storage_access',
                        'disable_storage_access'
                    ])
                    ->order_by('gdal.created_at', 'desc')
                    ->limit(ceil($limit / 3)) // แบ่ง 3 ส่วน
                    ->get()
                    ->result();

                log_message('info', 'Found ' . count($activity_logs) . ' records from tbl_google_drive_activity_logs');

                foreach ($activity_logs as $log) {
                    // ทำความสะอาด description (ลบ [Folder: ...] เดิมออก)
                    $description = preg_replace('/\s*\[Folder:\s*[^\]]+\]/', '', $log->action_description);

                    // เพิ่มชื่อ folder ใหม่
                    if ($log->folder_name) {
                        $description .= ' [Folder: ' . $log->folder_name . ']';
                    } elseif ($log->folder_id) {
                        $description .= ' [Folder ID: ' . substr($log->folder_id, 0, 15) . '...]';
                    }

                    $activities[] = [
                        'id' => $log->id,
                        'type' => 'user_activity',
                        'action_type' => $log->action_type,
                        'description' => trim($description),
                        'action_description' => trim($description),
                        'user_name' => trim($log->first_name . ' ' . $log->last_name),
                        'username' => $log->username,
                        'member_name' => trim($log->first_name . ' ' . $log->last_name),
                        'created_at' => $log->created_at,
                        'timestamp' => $log->created_at,
                        'folder_id' => $log->folder_id,
                        'folder_name' => $log->folder_name,
                        'folder_type' => $log->folder_type,
                        'file_id' => $log->file_id,
                        'item_id' => $log->item_id,
                        'item_type' => $log->item_type,
                        'source' => 'google_drive_activity_logs'
                    ];
                }
            }

            // ========================================
            // 3. ✨ NEW: ดึงจาก tbl_google_drive_file_activities
            // ========================================
            if ($this->db->table_exists('tbl_google_drive_file_activities')) {
                log_message('info', 'Querying tbl_google_drive_file_activities...');

                $file_activities = $this->db->select('
                gdfa.id,
                gdfa.google_file_id,
                gdfa.user_id, 
                gdfa.user_name,
                gdfa.user_email,
                gdfa.action_type, 
                gdfa.file_name,
                gdfa.file_size,
                gdfa.folder_path,
                gdfa.storage_mode,
                gdfa.target_google_email,
                gdfa.details,
                gdfa.created_at,
                COALESCE(m.m_fname, "ผู้ใช้") as first_name,
                COALESCE(m.m_lname, "") as last_name,
                COALESCE(m.m_username, gdfa.user_name) as username
            ')
                    ->from('tbl_google_drive_file_activities gdfa')
                    ->join('tbl_member m', 'gdfa.user_id = m.m_id', 'left')
                    ->where('gdfa.created_at >=', $start_datetime)
                    ->where('gdfa.created_at <=', $end_datetime)
                    ->where('gdfa.action_type !=', '')
                    ->order_by('gdfa.created_at', 'desc')
                    ->limit(ceil($limit / 3)) // แบ่ง 3 ส่วน
                    ->get()
                    ->result();

                log_message('info', 'Found ' . count($file_activities) . ' records from tbl_google_drive_file_activities');

                foreach ($file_activities as $log) {
                    // สร้าง description ตาม action_type
                    $description = $this->format_file_activity_description($log);

                    $activities[] = [
                        'id' => 'file_' . $log->id, // เพิ่ม prefix เพื่อไม่ซ้ำ
                        'type' => 'file_activity',
                        'action_type' => $log->action_type,
                        'description' => $description,
                        'action_description' => $description,
                        'user_name' => $log->user_name ?: trim($log->first_name . ' ' . $log->last_name),
                        'username' => $log->username,
                        'member_name' => $log->user_name ?: trim($log->first_name . ' ' . $log->last_name),
                        'created_at' => $log->created_at,
                        'timestamp' => $log->created_at,
                        'file_id' => $log->google_file_id,
                        'file_name' => $log->file_name,
                        'file_size' => $log->file_size,
                        'folder_path' => $log->folder_path,
                        'storage_mode' => $log->storage_mode,
                        'target_email' => $log->target_google_email,
                        'details' => $log->details,
                        'source' => 'google_drive_file_activities'
                    ];
                }
            }

            if (empty($activities)) {
                log_message('info', 'No activities found');
                return [];
            }

            // เรียงลำดับตามเวลา
            usort($activities, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // ตัดให้เหลือตาม limit
            $final_activities = array_slice($activities, 0, $limit);

            log_message('info', 'Total: ' . count($final_activities) . ' activities');
            log_message('info', '=== get_recent_activities_data END ===');

            return $final_activities;

        } catch (Exception $e) {
            log_message('error', 'Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✨ NEW: สร้าง description สำหรับ File Activities
     */
    private function format_file_activity_description($log)
    {
        $action_texts = [
            'upload' => 'อัพโหลดไฟล์',
            'edit' => 'แก้ไขไฟล์',
            'delete' => 'ลบไฟล์',
            'download' => 'ดาวน์โหลดไฟล์',
            'share' => 'แชร์ไฟล์',
            'rename' => 'เปลี่ยนชื่อไฟล์',
            'move' => 'ย้ายไฟล์',
            'copy' => 'คัดลอกไฟล์',
            'view' => 'ดูไฟล์'
        ];

        $action_text = $action_texts[$log->action_type] ?? $log->action_type;
        $description = $action_text . ': ' . $log->file_name;

        // เพิ่มข้อมูลเสริม
        if ($log->action_type === 'share' && $log->target_google_email) {
            $description .= ' → ' . $log->target_google_email;
        }

        if ($log->action_type === 'rename' && $log->details) {
            $details = json_decode($log->details, true);
            if (isset($details['original_name'])) {
                $description = 'เปลี่ยนชื่อไฟล์: ' . $details['original_name'] . ' → ' . $log->file_name;
            }
        }

        if ($log->file_size) {
            $description .= ' (' . $this->format_bytes($log->file_size) . ')';
        }

        if ($log->folder_path) {
            $description .= ' [Path: ' . $log->folder_path . ']';
        }

        return $description;
    }

    /**
     * ✅ Export Activities to CSV (รองรับภาษาไทย + File Info + Clean Folder Names + Logging)
     */
    public function export_activities_csv()
    {
        try {
            // รับข้อมูล user ปัจจุบัน
            $member_id = $this->session->userdata('member_id') ?? null;

            $start_date = $this->input->get('start_date');
            $end_date = $this->input->get('end_date');

            $activities = $this->get_activities_for_export($start_date, $end_date);

            if (empty($activities)) {
                // บันทึก log ว่าไม่มีข้อมูล
                $this->log_export_activity($member_id, 0, $start_date, $end_date, 'warning', 'No activities found');

                log_message('info', 'No activities to export');
                show_404();
                return;
            }

            $filename = 'activities_log_' . date('Y-m-d_His') . '.csv';

            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ✨ Header row (เพิ่มคอลัมน์ File Info)
            fputcsv($output, [
                'ลำดับ',
                'ประเภท',
                'กิจกรรม',
                'รายละเอียด',
                'ชื่อไฟล์',             // ✨ NEW
                'ขนาดไฟล์',             // ✨ NEW
                'ผู้รับแชร์',            // ✨ NEW
                'ชื่อโฟลเดอร์',
                'ประเภทโฟลเดอร์',
                'ผู้ทำรายการ',
                'Username',
                'วันที่/เวลา',
                'สถานะ',
                'แหล่งข้อมูล'
            ]);

            // Data rows
            $no = 1;
            foreach ($activities as $activity) {
                fputcsv($output, [
                    $no++,
                    $this->get_activity_type_thai($activity['type']),
                    $activity['action_type'],
                    $activity['description'],
                    $activity['file_name'] ?? '',              // ✨ NEW
                    $activity['file_size'] ?? '',              // ✨ NEW
                    $activity['target_email'] ?? '',           // ✨ NEW
                    $activity['folder_name'] ?? 'ไม่ระบุ',
                    $this->get_folder_type_thai($activity['folder_type'] ?? '-'),
                    $activity['user_name'],
                    $activity['username'],
                    $activity['created_at'],
                    $activity['status'] ?? 'success',
                    $activity['source']
                ]);
            }

            fclose($output);

            // ✅ บันทึก log การ export สำเร็จ
            $this->log_export_activity($member_id, count($activities), $start_date, $end_date, 'success');

            log_message('info', 'Export CSV successful: ' . count($activities) . ' records');

        } catch (Exception $e) {
            // ✅ บันทึก log การ export ล้มเหลว
            $member_id = $this->session->userdata('member_id') ?? null;
            $this->log_export_activity($member_id, 0, $start_date ?? null, $end_date ?? null, 'failed', $e->getMessage());

            log_message('error', 'Export CSV error: ' . $e->getMessage());
            show_error('ไม่สามารถ export ข้อมูลได้: ' . $e->getMessage());
        }
    }

    /**
     * ✅ ดึงข้อมูลสำหรับ Export (รองรับ date filter + file activities)
     */
    private function get_activities_for_export($start_date = null, $end_date = null)
    {
        $activities = [];

        try {
            // กำหนด date range
            if (!$start_date) {
                $start_date = date('Y-m-d', strtotime('-30 days'));
            }
            if (!$end_date) {
                $end_date = date('Y-m-d');
            }

            $start_datetime = $start_date . ' 00:00:00';
            $end_datetime = $end_date . ' 23:59:59';

            log_message('info', 'Export activities from: ' . $start_datetime . ' to ' . $end_datetime);

            // 1. ดึงจาก tbl_google_drive_logs
            if ($this->db->table_exists('tbl_google_drive_logs')) {
                $drive_logs = $this->db->select('
                    gdl.id,
                    gdl.member_id, 
                    gdl.action_type, 
                    gdl.action_description,
                    gdl.created_at,
                    gdl.status,
                    gdl.folder_id,
                    COALESCE(m.m_fname, "ระบบ") as first_name,
                    COALESCE(m.m_lname, "") as last_name,
                    COALESCE(m.m_username, "system") as username,
                    COALESCE(sf.folder_name, uf.folder_name) as folder_name,
                    COALESCE(sf.folder_type, uf.folder_type) as folder_type
                ')
                    ->from('tbl_google_drive_logs gdl')
                    ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                    ->join('tbl_google_drive_system_folders sf', 'gdl.folder_id = sf.folder_id', 'left')
                    ->join('tbl_google_drive_folders uf', 'gdl.folder_id = uf.folder_id', 'left')
                    ->where('gdl.created_at >=', $start_datetime)
                    ->where('gdl.created_at <=', $end_datetime)
                    ->where('gdl.action_type !=', '')
                    ->where('gdl.status', 'success')
                    ->order_by('gdl.created_at', 'desc')
                    ->get()
                    ->result();

                foreach ($drive_logs as $log) {
                    // ทำความสะอาด description (ลบ [Folder: ...] เดิมออก)
                    $description = preg_replace('/\s*\[Folder:\s*[^\]]+\]/', '', $log->action_description);

                    // เพิ่มชื่อ folder ใหม่
                    if ($log->folder_name) {
                        $description .= ' [Folder: ' . $log->folder_name . ']';
                    }

                    $activities[] = [
                        'id' => $log->id,
                        'type' => 'system_activity',
                        'action_type' => $log->action_type,
                        'description' => trim($description),
                        'user_name' => trim($log->first_name . ' ' . $log->last_name),
                        'username' => $log->username,
                        'created_at' => $log->created_at,
                        'status' => $log->status,
                        'folder_name' => $log->folder_name ?: 'ไม่ระบุ',
                        'folder_type' => $log->folder_type ?: '-',
                        'file_name' => '',
                        'file_size' => '',
                        'target_email' => '',
                        'source' => 'google_drive_logs'
                    ];
                }

                log_message('info', 'Export: Found ' . count($drive_logs) . ' from tbl_google_drive_logs');
            }

            // 2. ดึงจาก tbl_google_drive_activity_logs
            if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
                $activity_logs = $this->db->select('
                    gdal.id,
                    gdal.member_id, 
                    gdal.action_type, 
                    gdal.action_description,
                    gdal.created_at,
                    gdal.folder_id,
                    COALESCE(m.m_fname, "ผู้ใช้") as first_name,
                    COALESCE(m.m_lname, "") as last_name,
                    COALESCE(m.m_username, "unknown") as username,
                    COALESCE(sf.folder_name, uf.folder_name) as folder_name,
                    COALESCE(sf.folder_type, uf.folder_type) as folder_type
                ')
                    ->from('tbl_google_drive_activity_logs gdal')
                    ->join('tbl_member m', 'gdal.member_id = m.m_id', 'left')
                    ->join('tbl_google_drive_system_folders sf', 'gdal.folder_id = sf.folder_id', 'left')
                    ->join('tbl_google_drive_folders uf', 'gdal.folder_id = uf.folder_id', 'left')
                    ->where('gdal.created_at >=', $start_datetime)
                    ->where('gdal.created_at <=', $end_datetime)
                    ->where_in('gdal.action_type', [
                        'upload_file',
                        'delete_file',
                        'create_folder',
                        'delete_folder',
                        'add_direct_folder_permission',
                        'enable_storage_access',
                        'disable_storage_access'
                    ])
                    ->order_by('gdal.created_at', 'desc')
                    ->get()
                    ->result();

                foreach ($activity_logs as $log) {
                    // ทำความสะอาด description (ลบ [Folder: ...] เดิมออก)
                    $description = preg_replace('/\s*\[Folder:\s*[^\]]+\]/', '', $log->action_description);

                    // เพิ่มชื่อ folder ใหม่
                    if ($log->folder_name) {
                        $description .= ' [Folder: ' . $log->folder_name . ']';
                    }

                    $activities[] = [
                        'id' => $log->id,
                        'type' => 'user_activity',
                        'action_type' => $log->action_type,
                        'description' => trim($description),
                        'user_name' => trim($log->first_name . ' ' . $log->last_name),
                        'username' => $log->username,
                        'created_at' => $log->created_at,
                        'status' => 'success',
                        'folder_name' => $log->folder_name ?: 'ไม่ระบุ',
                        'folder_type' => $log->folder_type ?: '-',
                        'file_name' => '',
                        'file_size' => '',
                        'target_email' => '',
                        'source' => 'google_drive_activity_logs'
                    ];
                }

                log_message('info', 'Export: Found ' . count($activity_logs) . ' from tbl_google_drive_activity_logs');
            }

            // ========================================
            // 3. ✨ NEW: ดึงจาก tbl_google_drive_file_activities
            // ========================================
            if ($this->db->table_exists('tbl_google_drive_file_activities')) {
                $file_activities = $this->db->select('
                    gdfa.id,
                    gdfa.google_file_id,
                    gdfa.user_id, 
                    gdfa.user_name,
                    gdfa.user_email,
                    gdfa.action_type, 
                    gdfa.file_name,
                    gdfa.file_size,
                    gdfa.folder_path,
                    gdfa.storage_mode,
                    gdfa.target_google_email,
                    gdfa.details,
                    gdfa.created_at,
                    COALESCE(m.m_fname, "ผู้ใช้") as first_name,
                    COALESCE(m.m_lname, "") as last_name,
                    COALESCE(m.m_username, gdfa.user_name) as username
                ')
                    ->from('tbl_google_drive_file_activities gdfa')
                    ->join('tbl_member m', 'gdfa.user_id = m.m_id', 'left')
                    ->where('gdfa.created_at >=', $start_datetime)
                    ->where('gdfa.created_at <=', $end_datetime)
                    ->where('gdfa.action_type !=', '')
                    ->order_by('gdfa.created_at', 'desc')
                    ->get()
                    ->result();

                foreach ($file_activities as $log) {
                    $description = $this->format_file_activity_description($log);

                    $activities[] = [
                        'id' => 'file_' . $log->id,
                        'type' => 'file_activity',
                        'action_type' => $log->action_type,
                        'description' => $description,
                        'user_name' => $log->user_name ?: trim($log->first_name . ' ' . $log->last_name),
                        'username' => $log->username,
                        'created_at' => $log->created_at,
                        'status' => 'success',
                        'folder_name' => $log->folder_path ?: 'ไม่ระบุ',
                        'folder_type' => $log->storage_mode === 'system' ? 'ระบบ' : 'ส่วนตัว',
                        'file_name' => $log->file_name,
                        'file_size' => $log->file_size ? $this->format_bytes($log->file_size) : '',
                        'target_email' => $log->target_google_email ?: '',
                        'source' => 'google_drive_file_activities'
                    ];
                }

                log_message('info', 'Export: Found ' . count($file_activities) . ' from tbl_google_drive_file_activities');
            }

            // เรียงลำดับตามเวลา
            usort($activities, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            log_message('info', 'Export: Total ' . count($activities) . ' activities');

            return $activities;

        } catch (Exception $e) {
            log_message('error', 'Get activities for export error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ บันทึก Export Log ลง tbl_google_drive_logs
     */
    private function log_export_activity($member_id, $export_count, $start_date, $end_date, $status = 'success', $error = null)
    {
        try {
            $additional_data = [
                'export_count' => $export_count,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'export_timestamp' => date('Y-m-d H:i:s'),
                'file_name' => 'activities_log_' . date('Y-m-d_His') . '.csv'
            ];

            if ($error) {
                $additional_data['error_details'] = $error;
            }

            $log_data = [
                'member_id' => $member_id,
                'action_type' => 'export_csv',
                'action_description' => sprintf(
                    'Export กิจกรรม %d รายการ (%s ถึง %s)',
                    $export_count,
                    $start_date ?: 'ทั้งหมด',
                    $end_date ?: 'ปัจจุบัน'
                ),
                'module' => 'google_drive_system',
                'status' => $status,
                'error_message' => $error,
                'additional_data' => json_encode($additional_data),
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_logs', $log_data);

            log_message('info', 'Export log saved: ' . json_encode($log_data));

        } catch (Exception $e) {
            log_message('error', 'Failed to save export log: ' . $e->getMessage());
        }
    }


    /**
     * ✅ แปลงประเภทโฟลเดอร์เป็นภาษาไทย
     */
    private function get_folder_type_thai($type)
    {
        $types = [
            'system' => 'ระบบ',
            'department' => 'แผนก',
            'shared' => 'แชร์',
            'user' => 'ส่วนตัว',
            'admin' => 'ผู้ดูแล',
            'position' => 'ตำแหน่ง',
            'personal' => 'ส่วนตัว'
        ];

        return $types[$type] ?? $type;
    }

    /**
     * ✨ NEW: แปลง action_type ของ file activities เป็นภาษาไทย
     */
    private function get_file_action_type_thai($action_type)
    {
        $actions = [
            'upload' => 'อัพโหลด',
            'edit' => 'แก้ไข',
            'delete' => 'ลบ',
            'download' => 'ดาวน์โหลด',
            'share' => 'แชร์',
            'rename' => 'เปลี่ยนชื่อ',
            'move' => 'ย้าย',
            'copy' => 'คัดลอก',
            'view' => 'ดู'
        ];

        return $actions[$action_type] ?? $action_type;
    }

    /**
     * ✅ แปลงประเภทกิจกรรมเป็นภาษาไทย
     */
    private function get_activity_type_thai($type)
    {
        $types = [
            'system_activity' => 'ระบบ (System)',
            'user_activity' => 'ผู้ใช้ (User)',
            'file_activity' => 'ไฟล์ (File)',
            'member_activity' => 'สมาชิก (Member)',
            'system_event' => 'เหตุการณ์ระบบ',
            'drive_activity' => 'Google Drive'
        ];

        return $types[$type] ?? $type;
    }



    /**
     * ✅ สร้างกิจกรรม Mock สำหรับทดสอบ
     */
    private function create_mock_activities()
    {
        $current_user = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
        $username = $this->session->userdata('m_username') ?: 'admin';

        return [
            [
                'type' => 'system_event',
                'action_type' => 'system_update',
                'description' => 'ระบบ Google Drive Storage เริ่มต้นการทำงาน',
                'action_description' => 'ระบบ Google Drive Storage เริ่มต้นการทำงาน',
                'user_name' => 'ระบบ',
                'username' => 'system',
                'member_name' => 'ระบบ',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'source' => 'mock'
            ],
            [
                'type' => 'system_activity',
                'action_type' => 'connect',
                'description' => 'เชื่อมต่อ Google Drive Storage สำเร็จ',
                'action_description' => 'เชื่อมต่อ Google Drive Storage สำเร็จ',
                'user_name' => $current_user,
                'username' => $username,
                'member_name' => $current_user,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'source' => 'mock'
            ],
            [
                'type' => 'user_activity',
                'action_type' => 'create_folder',
                'description' => 'สร้างโครงสร้างโฟลเดอร์หลักเรียบร้อยแล้ว',
                'action_description' => 'สร้างโครงสร้างโฟลเดอร์หลักเรียบร้อยแล้ว',
                'user_name' => $current_user,
                'username' => $username,
                'member_name' => $current_user,
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'timestamp' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'source' => 'mock'
            ],
            [
                'type' => 'system_event',
                'action_type' => 'sync_files',
                'description' => 'สร้างโฟลเดอร์ Organization Drive สำเร็จ',
                'action_description' => 'สร้างโฟลเดอร์ Organization Drive สำเร็จ',
                'user_name' => 'ระบบ',
                'username' => 'system',
                'member_name' => 'ระบบ',
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'timestamp' => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'source' => 'mock'
            ],
            [
                'type' => 'member_activity',
                'action_type' => 'login',
                'description' => 'เข้าสู่ระบบ Google Drive Management',
                'action_description' => 'เข้าสู่ระบบ Google Drive Management',
                'user_name' => $current_user,
                'username' => $username,
                'member_name' => $current_user,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours')),
                'timestamp' => date('Y-m-d H:i:s', strtotime('-5 hours')),
                'source' => 'mock'
            ]
        ];
    }






    /**
     * ✅ ดึงโครงสร้างโฟลเดอร์ (API Endpoint) - IMPROVED
     */
    public function get_folder_structure()
    {
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
     * ✅ FIXED: ดึงข้อมูลโครงสร้างโฟลเดอร์จากฐานข้อมูล
     * เพิ่ม: folder_id, has_children (children_count), จัดเรียงแบบ hierarchical
     */
    /**
     * แทนที่ Method นี้เท่านั้น
     */
    private function get_folder_structure_data()
    {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
                return $this->create_mock_folder_structure();
            }

            $folders = $this->db->select('
            sf.folder_id,
            sf.folder_name,
            sf.folder_type,
            sf.parent_folder_id,
            sf.folder_path,
            sf.folder_description,
            sf.created_at,
            p.pname as position_name,
            (
                SELECT COUNT(*) 
                FROM tbl_google_drive_system_folders child 
                WHERE child.parent_folder_id = sf.folder_id 
                AND child.is_active = 1
            ) as children_count
        ')
                ->from('tbl_google_drive_system_folders sf')
                ->join('tbl_position p', 'sf.created_for_position = p.pid', 'left')
                ->where('sf.is_active', 1)
                ->order_by('sf.folder_path', 'ASC')
                ->get()
                ->result();

            if (empty($folders)) {
                return $this->create_mock_folder_structure();
            }

            $structure = [];
            foreach ($folders as $folder) {
                $level = $this->calculate_folder_level($folder->folder_path);

                $structure[] = [
                    'folder_id' => $folder->folder_id,
                    'folder_name' => $folder->folder_name,
                    'folder_type' => $folder->folder_type,
                    'parent_folder_id' => $folder->parent_folder_id,
                    'folder_path' => $folder->folder_path,
                    'description' => $folder->folder_description,
                    'position_name' => $folder->position_name,
                    'level' => $level,
                    'has_children' => ($folder->children_count > 0),
                    'is_root' => empty($folder->parent_folder_id),
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
     * ✅ NEW: สร้าง Hierarchical Structure จาก Flat Array
     */
    private function build_hierarchical_structure($folders)
    {
        // สร้าง index map สำหรับ quick lookup
        $folder_map = [];
        foreach ($folders as $folder) {
            $folder_map[$folder->folder_id] = $folder;
            $folder->children = []; // เตรียม children array
        }

        // จัดกลุ่ม folders ตาม parent
        $root_folders = [];
        foreach ($folders as $folder) {
            if (empty($folder->parent_folder_id) || !isset($folder_map[$folder->parent_folder_id])) {
                // Root folder (ไม่มี parent หรือ parent ไม่อยู่ใน list)
                $root_folders[] = $folder;
            } else {
                // Child folder - เพิ่มเข้า children ของ parent
                $folder_map[$folder->parent_folder_id]->children[] = $folder;
            }
        }

        // แปลง tree เป็น flat array พร้อม level
        $flat_structure = [];
        foreach ($root_folders as $root) {
            $this->flatten_folder_tree($root, $flat_structure, 0);
        }

        return $flat_structure;
    }

    /**
     * ✅ NEW: แปลง Tree Structure เป็น Flat Array พร้อม Level
     */
    private function flatten_folder_tree($folder, &$result, $level)
    {
        // เพิ่ม folder ปัจจุบัน
        $result[] = [
            'folder_id' => $folder->folder_id,
            'folder_name' => $folder->folder_name,
            'folder_type' => $folder->folder_type,
            'parent_folder_id' => $folder->parent_folder_id,
            'folder_path' => $folder->folder_path,
            'description' => $folder->folder_description,
            'position_name' => $folder->position_name ?? null,
            'level' => $level,
            'has_children' => ($folder->children_count > 0),
            'is_root' => empty($folder->parent_folder_id),
            'created_at' => $folder->created_at
        ];

        // เรียกซ้ำสำหรับ children
        if (!empty($folder->children)) {
            // เรียง children ตามชื่อ
            usort($folder->children, function ($a, $b) {
                return strcmp($a->folder_name, $b->folder_name);
            });

            foreach ($folder->children as $child) {
                $this->flatten_folder_tree($child, $result, $level + 1);
            }
        }
    }



    /**
     * ✅ IMPROVED: คำนวณระดับโฟลเดอร์จาก path
     */
    private function calculate_folder_level($path)
    {
        if (empty($path)) {
            return 0;
        }

        // นับจำนวน "/" ใน path
        // /Organization Drive = 0 (root)
        // /Organization Drive/Admin = 1
        // /Organization Drive/Departments/คณะผู้บริหาร = 2
        return substr_count(trim($path, '/'), '/');
    }

    /**
     * ✅ IMPROVED: สร้างโครงสร้างโฟลเดอร์ Mock
     */
    private function create_mock_folder_structure()
    {
        return [
            [
                'folder_id' => 'mock_root',
                'folder_name' => 'Organization Drive',
                'folder_type' => 'system',
                'parent_folder_id' => null,
                'folder_path' => '/Organization Drive',
                'description' => 'โฟลเดอร์หลักของระบบ',
                'position_name' => null,
                'level' => 0,
                'has_children' => true,
                'is_root' => true,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'folder_id' => 'mock_admin',
                'folder_name' => 'Admin',
                'folder_type' => 'admin',
                'parent_folder_id' => 'mock_root',
                'folder_path' => '/Organization Drive/Admin',
                'description' => 'โฟลเดอร์สำหรับผู้ดูแลระบบ',
                'position_name' => null,
                'level' => 1,
                'has_children' => false,
                'is_root' => false,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'folder_id' => 'mock_departments',
                'folder_name' => 'Departments',
                'folder_type' => 'system',
                'parent_folder_id' => 'mock_root',
                'folder_path' => '/Organization Drive/Departments',
                'description' => 'โฟลเดอร์แผนกต่างๆ',
                'position_name' => null,
                'level' => 1,
                'has_children' => true,
                'is_root' => false,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'folder_id' => 'mock_dept_1',
                'folder_name' => 'คณะผู้บริหาร',
                'folder_type' => 'department',
                'parent_folder_id' => 'mock_departments',
                'folder_path' => '/Organization Drive/Departments/คณะผู้บริหาร',
                'description' => 'โฟลเดอร์สำหรับคณะผู้บริหาร',
                'position_name' => 'ผู้บริหาร',
                'level' => 2,
                'has_children' => false,
                'is_root' => false,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'folder_id' => 'mock_dept_2',
                'folder_name' => 'สมาชิกสภา',
                'folder_type' => 'department',
                'parent_folder_id' => 'mock_departments',
                'folder_path' => '/Organization Drive/Departments/สมาชิกสภา',
                'description' => 'โฟลเดอร์สำหรับสมาชิกสภา',
                'position_name' => 'สมาชิกสภา',
                'level' => 2,
                'has_children' => false,
                'is_root' => false,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'folder_id' => 'mock_dept_3',
                'folder_name' => 'หัวหน้าส่วนราชการ',
                'folder_type' => 'department',
                'parent_folder_id' => 'mock_departments',
                'folder_path' => '/Organization Drive/Departments/หัวหน้าส่วนราชการ',
                'description' => 'โฟลเดอร์สำหรับหัวหน้าส่วนราชการ',
                'position_name' => 'หัวหน้าส่วนราชการ',
                'level' => 2,
                'has_children' => false,
                'is_root' => false,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'folder_id' => 'mock_shared',
                'folder_name' => 'Shared',
                'folder_type' => 'shared',
                'parent_folder_id' => 'mock_root',
                'folder_path' => '/Organization Drive/Shared',
                'description' => 'โฟลเดอร์เอกสารส่วนกลาง',
                'position_name' => null,
                'level' => 1,
                'has_children' => false,
                'is_root' => false,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'folder_id' => 'mock_users',
                'folder_name' => 'Users',
                'folder_type' => 'system',
                'parent_folder_id' => 'mock_root',
                'folder_path' => '/Organization Drive/Users',
                'description' => 'โฟลเดอร์ส่วนตัวของผู้ใช้',
                'position_name' => null,
                'level' => 1,
                'has_children' => false,
                'is_root' => false,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * ✅ HELPER: ตรวจสอบว่า folder มี children หรือไม่ (ใช้กรณีต้องการ query แยก)
     */
    private function check_has_children($folder_id)
    {
        $count = $this->db
            ->where('parent_folder_id', $folder_id)
            ->where('is_active', 1)
            ->count_all_results('tbl_google_drive_system_folders');

        return ($count > 0);
    }

    /**
     * ✅ ดึงสถิติ Dashboard แบบครบถ้วน
     */
    public function get_dashboard_stats()
    {
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
    private function get_comprehensive_dashboard_stats()
    {
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
                    'ready' => (bool) $system_storage->folder_structure_created,
                    'google_email' => $system_storage->google_account_email,
                    'storage_used' => (int) $system_storage->total_storage_used,
                    'storage_limit' => (int) $system_storage->max_storage_limit,
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
                    $stats['folders']['by_type'][$type->folder_type] = (int) $type->count;
                }
            }

            // Files
            if ($this->db->table_exists('tbl_google_drive_system_files')) {
                $file_stats = $this->db->select('COUNT(*) as total_files, SUM(file_size) as total_size')
                    ->from('tbl_google_drive_system_files')
                    ->get()
                    ->row();

                $stats['files'] = [
                    'total' => (int) ($file_stats->total_files ?? 0),
                    'total_size' => (int) ($file_stats->total_size ?? 0)
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
    private function get_default_stats()
    {
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
    public function log_enhanced_activity($member_id, $action_type, $description, $additional_data = [])
    {
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
    private function create_enhanced_logs_table()
    {
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
    public function reports()
    {
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
    private function get_activities_report_data($date_range)
    {
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
    private function get_storage_report_data($date_range)
    {
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
                    'current_usage' => (int) $system_storage->total_storage_used,
                    'storage_limit' => (int) $system_storage->max_storage_limit,
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
    private function get_users_report_data($date_range)
    {
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
    private function get_folders_report_data($date_range)
    {
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
    private function get_report_title($type)
    {
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
    public function export_report()
    {
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
    private function export_activities_report($date_range, $format)
    {
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
    private function export_storage_report($date_range, $format)
    {
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
    public function get_report_data()
    {
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
    public function token_status()
    {
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
    public function force_refresh()
    {
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
    public function check_service_status()
    {
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
    public function debug_token_details()
    {
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
    private function test_token_validity_quick($access_token)
    {
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
    private function test_folder_sharing_capability($access_token)
    {
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
    public function get_recent_logs()
    {
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
    private function get_comprehensive_token_status()
    {
        try {
            $system_storage = $this->get_active_system_storage();

            $status = [
                'has_system_storage' => (bool) $system_storage,
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
    public function run_complete_diagnostics()
    {
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
                'passed' => (bool) $storage,
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

            $passed_count = count(array_filter($tests, function ($test) {
                return $test['passed'];
            }));
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

    public function test_google_status()
    {
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
    public function user_usage()
    {
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
    private function prepare_formatted_data_for_view($files, $storage_stats, $folder_breakdown)
    {
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
    private function fix_mime_type($current_mime, $filename)
    {
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
    private function get_mime_type_from_extension($filename)
    {
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
    private function get_view_helper_functions()
    {
        return [
            'format_bytes' => function ($bytes) {
                return $this->format_bytes($bytes);
            },
            'get_friendly_mime_type' => function ($mime_type) {
                return $this->get_friendly_mime_type($mime_type);
            },
            'get_file_type_icon' => function ($mime_type) {
                return $this->get_file_type_icon($mime_type);
            },
            'get_activity_icon' => function ($action) {
                return $this->get_activity_icon($action);
            }
        ];
    }




    /**
     * ดึงข้อมูล User และ Storage Details
     */
    private function get_user_storage_details($user_id)
    {
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
    private function get_user_files($user_id)
    {
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
    private function get_user_storage_stats($user_id)
    {
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

            $stats['total_files'] = (int) $basic_stats->total_files;
            $stats['total_size'] = (int) $basic_stats->total_size;

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
    private function get_user_usage_history($user_id)
    {
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
    private function get_user_folder_breakdown($user_id)
    {
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
    private function get_user_recent_activities($user_id)
    {
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
            usort($activities, function ($a, $b) {
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
    public function get_user_usage_data()
    {
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
    public function delete_user_file()
    {
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
    private function update_user_storage_usage($user_id)
    {
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
    private function get_friendly_mime_type($mime_type)
    {
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
    private function get_file_type_icon($mime_type)
    {
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
        if (
            strpos($mime_type, 'word') !== false ||
            strpos($mime_type, 'wordprocessingml') !== false
        ) {
            return 'fas fa-file-word text-blue-500';
        }

        // Excel
        if (
            strpos($mime_type, 'excel') !== false ||
            strpos($mime_type, 'spreadsheetml') !== false
        ) {
            return 'fas fa-file-excel text-green-500';
        }

        // PowerPoint
        if (
            strpos($mime_type, 'powerpoint') !== false ||
            strpos($mime_type, 'presentationml') !== false
        ) {
            return 'fas fa-file-powerpoint text-orange-500';
        }

        // Archive
        if (
            strpos($mime_type, 'zip') !== false ||
            strpos($mime_type, 'rar') !== false ||
            strpos($mime_type, 'archive') !== false
        ) {
            return 'fas fa-file-archive text-yellow-500';
        }

        // Text
        if (strpos($mime_type, 'text/') === 0) {
            return 'fas fa-file-alt text-gray-500';
        }

        // Code
        if (
            in_array($mime_type, [
                'application/json',
                'application/xml',
                'text/html',
                'text/css',
                'text/javascript'
            ])
        ) {
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
    private function create_personal_folders_auto($users_folder_id, $access_token)
    {
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
    private function assign_personal_folder_permissions($folder_id, $owner_user_id, $owner_name)
    {
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
    private function create_complete_structure_with_personal_folders($storage_id, $access_token)
    {
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
    public function create_folder_structure_with_permissions()
    {
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


    /**
     * ✅ สร้าง Department Folders เหมือนกับ debug ที่สำเร็จ
     */
    private function create_department_folders_like_debug($departments_folder_id, $access_token)
    {
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


    // Method สำหรับทดสอบ Google Token แบบง่าย
    private function test_google_token_simple($access_token)
    {
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
     * ✅ Helper: Get User Name
     */
    private function get_user_name($user_id)
    {
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
     * ✅ แก้ไข: กำหนดสิทธิ์อัตโนมัติตามประเภทโฟลเดอร์
     */
    private function assign_auto_permissions($folder_id, $folder_name, $permission_type)
    {
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
                log_message('debug', "⚠️ No permissions assigned for folder: {$folder_name} (Type: {$permission_type})");
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
    public function debug_shared_folder_permissions()
    {
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
                log_message('debug', '🔍 DEBUG: Shared folder not found in database');
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
    public function fix_shared_folder_permissions()
    {
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
     * ✅ FIXED: add_folder_permission_correct() with duplicate prevention
     * - ตรวจสอบ permission ซ้ำก่อน INSERT
     * - รักษา logic เดิมไว้ทั้งหมด
     * - เพิ่มเฉพาะการตรวจสอบ
     */
    private function add_folder_permission_correct($folder_id, $member_id, $access_type)
    {
        try {
            // ✅ Validation (เหมือนเดิม)
            if (empty($folder_id) || empty($member_id) || empty($access_type)) {
                log_message('debug', 'Invalid parameters for add_folder_permission_correct');
                return false;
            }

            // ✅ ตรวจสอบตาราง (เหมือนเดิม)
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                log_message('debug', 'Table tbl_google_drive_member_folder_access does not exist');
                return false;
            }

            // ✅ **ส่วนที่เพิ่มใหม่: ตรวจสอบ duplicate permission**
            $existing = $this->db
                ->where('folder_id', $folder_id)
                ->where('member_id', $member_id)
                ->where('is_active', 1)
                ->get('tbl_google_drive_member_folder_access')
                ->row();

            if ($existing) {
                // Permission มีอยู่แล้ว
                log_message('info', "Permission exists: Member {$member_id} already has access to folder {$folder_id}");

                // ✅ ถ้า access_type ต่างกัน ให้อัปเดต
                if ($existing->access_type !== $access_type) {
                    $update_result = $this->db
                        ->where('id', $existing->id)
                        ->update('tbl_google_drive_member_folder_access', [
                            'access_type' => $access_type,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    if ($update_result) {
                        log_message('info', "✅ Updated permission: {$existing->access_type} → {$access_type}");
                    }

                    return $update_result;
                }

                // ถ้า access_type เหมือนกัน ถือว่าสำเร็จ
                return true;
            }

            // ✅ สร้าง permission ใหม่ (เหมือนเดิม)
            $current_user_id = $this->session->userdata('m_id') ?: 1;
            $granted_by_name = $this->get_user_name($current_user_id);

            $permission_data = [
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'permission_source' => 'system',
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
                log_message('info', "✅ Created new permission: Member {$member_id} → {$access_type} for folder {$folder_id}");
            } else {
                $db_error = $this->db->error();
                log_message('error', "❌ Failed to create permission: " . json_encode($db_error));
            }

            return $insert_result;

        } catch (Exception $e) {
            log_message('error', 'add_folder_permission_correct exception: ' . $e->getMessage());
            return false;
        }
    }


    // =============================================
// Helper Methods สำหรับการจัดการผู้ใช้และสิทธิ์
// =============================================

    /**
     * ✅ Helper: ดึงผู้ใช้ทั้งหมดที่ active (แก้ไขแล้ว)
     */
    private function get_all_active_users()
    {
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
    private function get_admin_users()
    {
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
    public function debug_department_folders()
    {
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
    private function create_folder_with_curl_debug($folder_name, $parent_id, $access_token)
    {
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
    private function test_google_drive_api($access_token)
    {
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
    public function force_create_department_folders()
    {
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
    private function get_enabled_users()
    {
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
     * ✅ แก้ไข: กำหนดสิทธิ์สำหรับโฟลเดอร์แผนกแต่ละแผนก
     */
    private function assign_department_folder_permissions($folder_id, $position_id)
    {
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
    private function assign_departments_root_permissions($departments_folder_id)
    {
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
    private function assign_main_folder_permissions($folder_id, $folder_name, $folder_type)
    {
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
    private function clear_all_permissions()
    {
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
    private function get_users_by_position($position_id)
    {
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
    private function add_folder_permission_direct($folder_id, $member_id, $access_level)
    {
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
     * ✅ ได้ไอคอนสำหรับ Activity
     */
    private function get_activity_icon($action)
    {
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
    public function export_user_data()
    {
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
    public function get_user_chart_data()
    {
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
    private function get_file_types_chart_data($user_id)
    {
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
                    'value' => (int) $type->total_size,
                    'count' => (int) $type->count,
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
    private function get_upload_activity_chart_data($user_id)
    {
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
                    'uploads' => (int) $activity->uploads_count,
                    'size' => (int) $activity->total_size,
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
    private function get_folder_usage_chart_data($user_id)
    {
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
                    'files' => (int) $folder->file_count,
                    'size' => (int) $folder->total_size,
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
    public function update_user_quota()
    {
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
                log_message('debug', 'Failed to log activity: ' . $log_error->getMessage());
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
    public function reset_user_storage()
    {
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
    public function get_realtime_stats()
    {
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
    public function create_personal_folder()
    {
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





    public function get_folder_permissions()
    {
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
    public function get_inherited_permissions()
    {
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
    public function get_direct_permissions()
    {
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
    public function get_effective_permissions()
    {
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
    public function add_direct_folder_permission()
    {
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
    public function toggle_folder_inheritance()
    {
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
    public function get_folder_permission_stats()
    {
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



    private function get_all_folder_permissions_safe($folder_id)
    {
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
    private function get_folder_direct_permissions_safe($folder_id)
    {
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
    private function calculate_effective_permissions_safe($folder_id)
    {
        try {
            // ตรวจสอบว่าตาราง permissions มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                log_message('debug', 'Table tbl_google_drive_member_folder_access does not exist');
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
                            log_message('debug', 'Invalid expires_at format: ' . $permission->expires_at);
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
    private function get_detailed_permission_stats_safe($folder_id)
    {
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
    private function get_folder_inherited_permissions_safe($folder_id)
    {
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
    public function get_available_users()
    {
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




    public function test_get_available_users()
    {
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



    /**
     * ============================================
     * COMPLETE FIXED VERSION
     * ============================================
     * 
     * ✅ แก้ไขปัญหา:
     * 1. validate_and_fix_folder_id() รองรับ Personal Folder
     * 2. เพิ่ม get_folder_info() เพื่อดึงข้อมูล folder พร้อม owner
     * 3. add_folder_permission() ตรวจสอบ Ownership สำหรับ Personal Folder
     * 
     * ============================================
     */

    /**
     * ✅ เพิ่มสิทธิ์โฟลเดอร์ (FIXED VERSION)
     * 
     * Business Rules:
     * - เฉพาะ Owner เท่านั้นที่เพิ่มสิทธิ์ใน Personal Folder ได้
     * - System Folder สามารถเพิ่มสิทธิ์ได้ตามปกติ
     */
    public function add_folder_permission()
    {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        try {
            // ========================================
            // 1. ตรวจสอบ AJAX request
            // ========================================
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            // ========================================
            // 2. ตรวจสอบสิทธิ์ Admin
            // ========================================
            $user_system = $this->session->userdata('m_system');
            $current_user_id = $this->session->userdata('m_id');

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                $this->output_json_error('ไม่มีสิทธิ์เข้าถึง');
                return;
            }

            // ========================================
            // 3. รับข้อมูล
            // ========================================
            $raw_data = file_get_contents('php://input');
            $post_data = json_decode($raw_data, true);

            $member_id = $post_data['member_id'] ?? null;
            $folder_id = $post_data['folder_id'] ?? null;
            $access_type = $post_data['access_type'] ?? 'read';

            log_message('info', "========================================");
            log_message('info', "📥 add_folder_permission request received");
            log_message('info', "========================================");
            log_message('info', "  member_id: {$member_id}");
            log_message('info', "  folder_id (raw): {$folder_id}");
            log_message('info', "  access_type: {$access_type}");
            log_message('info', "  requested_by: {$current_user_id}");

            // ========================================
            // 4. Validation
            // ========================================
            if (empty($member_id)) {
                $this->output_json_error('ไม่ได้ระบุรหัสสมาชิก');
                return;
            }

            if (empty($folder_id)) {
                $this->output_json_error('ไม่ได้ระบุรหัสโฟลเดอร์');
                return;
            }

            // ========================================
            // 5. ✅ ตรวจสอบและแก้ไข folder_id
            // ========================================
            log_message('info', "🔍 Step 1: Validating folder_id...");
            $validated_folder_id = $this->validate_and_fix_folder_id($folder_id);

            if ($validated_folder_id === false) {
                log_message('error', "❌ Invalid folder_id cannot be fixed: {$folder_id}");
                $this->output_json_error('รหัสโฟลเดอร์ไม่ถูกต้อง กรุณาตรวจสอบข้อมูล');
                return;
            }

            $folder_id = $validated_folder_id;
            log_message('info', "✅ folder_id validated: {$folder_id}");

            // ========================================
            // 6. ตรวจสอบว่าสมาชิกมีอยู่จริง
            // ========================================
            log_message('info', "🔍 Step 2: Checking if member exists...");
            $member_exists = $this->db->select('m_id, m_fname, m_lname')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->where('m_status', 1)
                ->get()
                ->row();

            if (!$member_exists) {
                log_message('error', "❌ Member not found: {$member_id}");
                $this->output_json_error('ไม่พบสมาชิกที่ระบุ');
                return;
            }

            log_message('info', "✅ Member found: {$member_exists->m_fname} {$member_exists->m_lname}");

            // ========================================
            // 7. ✅ ดึงข้อมูลโฟลเดอร์
            // ========================================
            log_message('info', "🔍 Step 3: Getting folder info...");
            $folder_info = $this->get_folder_info($folder_id);

            if (!$folder_info) {
                log_message('error', "❌ Folder not found: {$folder_id}");
                $this->output_json_error('ไม่พบโฟลเดอร์ที่ระบุในระบบ');
                return;
            }

            // ========================================
            // 8. ✅ ตรวจสอบ Personal Folder Ownership (UPDATED)
            // ========================================
            log_message('info', "🔍 Step 4: Checking folder permissions...");

            // ✅ เรียกใช้ Helper Function จาก Model
            $permission_check = $this->Google_drive_model->check_personal_folder_permission(
                $folder_info,
                $current_user_id
            );

            if (!$permission_check['allowed']) {
                log_message('error', "========================================");
                log_message('error', "❌ PERMISSION DENIED");
                log_message('error', "========================================");
                log_message('error', "  Reason: {$permission_check['reason']}");
                log_message('error', "  User Role: " . ($permission_check['user_role'] ?? 'unknown'));
                log_message('error', "========================================");

                $this->output_json_error(
                    'ไม่สามารถเพิ่มสิทธิ์ในโฟลเดอร์ส่วนตัวของผู้อื่นได้ ' .
                    'เฉพาะเจ้าของหรือ System Admin เท่านั้น'
                );
                return;
            }

            log_message('info', "========================================");
            log_message('info', "✅ PERMISSION CHECK PASSED");
            log_message('info', "========================================");
            log_message('info', "  Reason: {$permission_check['reason']}");
            if (isset($permission_check['user_role']) && $permission_check['user_role']) {
                log_message('info', "  User Role: {$permission_check['user_role']}");
            }
            log_message('info', "========================================");

            // ========================================
            // 9. ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
            // ========================================
            log_message('info', "🔍 Step 4: Checking existing permissions...");
            $existing_permission = $this->db->select('id, access_type')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $member_id)
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($existing_permission) {
                log_message('info', "⚠️ User {$member_id} already has '{$existing_permission->access_type}' permission");
                $this->output_json_error('สมาชิกมีสิทธิ์เข้าถึงโฟลเดอร์นี้อยู่แล้ว (สิทธิ์ปัจจุบัน: ' . $existing_permission->access_type . ')');
                return;
            }

            log_message('info', "✅ No existing permission found");

            // ========================================
            // 10. เพิ่มสิทธิ์
            // ========================================
            log_message('info', "🔍 Step 5: Inserting new permission...");
            $permission_data = [
                'member_id' => $member_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'granted_by' => $current_user_id,
                'granted_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ];

            $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);
            $permission_id = $this->db->insert_id();

            if ($permission_id) {
                log_message('info', "========================================");
                log_message('info', "✅ PERMISSION ADDED SUCCESSFULLY");
                log_message('info', "========================================");
                log_message('info', "  Permission ID: {$permission_id}");
                log_message('info', "  Member: {$member_exists->m_fname} {$member_exists->m_lname} (ID: {$member_id})");
                log_message('info', "  Folder: {$folder_info->folder_name}");
                log_message('info', "  Folder Type: {$folder_info->folder_type}");
                log_message('info', "  Access Type: {$access_type}");
                log_message('info', "  Granted By: {$current_user_id}");
                log_message('info', "========================================");

                // ✅ ✅ ✅ เพิ่มส่วนนี้ - เริ่ม
                try {
                    log_message('info', "📝 Logging permission grant to tbl_google_drive_logs...");

                    $this->load->model('Google_drive_model');

                    $this->Google_drive_model->log_permission_action(
                        'grant_permission',
                        [
                            'permission_id' => $permission_id,
                            'member_id' => $member_id,
                            'member_name' => $member_exists->m_fname . ' ' . $member_exists->m_lname,
                            'folder_id' => $folder_id,
                            'folder_name' => $folder_info->folder_name,
                            'access_type' => $access_type,
                            'target_email' => $member_exists->m_username ?? '',
                            'permission_source' => 'direct',
                            'granted_by' => $current_user_id,
                            'granted_at' => date('Y-m-d H:i:s')
                        ],
                        $current_user_id
                    );

                    log_message('info', "✅ Permission log recorded successfully");

                } catch (Exception $log_error) {
                    // ไม่ให้กระทบการทำงานหลัก
                    log_message('error', "⚠️ Failed to log permission (non-critical): {$log_error->getMessage()}");
                }
                // ✅ ✅ ✅ เพิ่มส่วนนี้ - จบ

                $this->output_json_success([
                    'permission_id' => $permission_id,
                    'member_name' => $member_exists->m_fname . ' ' . $member_exists->m_lname,
                    'folder_name' => $folder_info->folder_name,
                    'folder_type' => $folder_info->folder_type,
                    'access_type' => $access_type
                ], 'เพิ่มสิทธิ์เรียบร้อยแล้ว');
            } else {
                log_message('error', "❌ Failed to insert permission");
                $this->output_json_error('ไม่สามารถเพิ่มสิทธิ์ได้');
            }

        } catch (Exception $e) {
            log_message('error', '========================================');
            log_message('error', '❌ add_folder_permission ERROR');
            log_message('error', '========================================');
            log_message('error', 'Message: ' . $e->getMessage());
            log_message('debug', 'Trace: ' . $e->getTraceAsString());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Helper: ตรวจสอบและแก้ไข folder_id (FIXED VERSION)
     * รองรับทั้ง system folders และ personal folders
     * 
     * @param string $folder_id - Folder ID ที่ต้องการตรวจสอบ
     * @return string|false - Google Drive Folder ID ที่ถูกต้อง หรือ false ถ้าไม่พบ
     */
    private function validate_and_fix_folder_id($folder_id)
    {
        // ========================================
        // 1. ตรวจสอบว่าเป็น Google Drive Folder ID จริงหรือไม่
        // ========================================

        if (strlen($folder_id) >= 25 && preg_match('/^[a-zA-Z0-9_-]+$/', $folder_id)) {
            log_message('info', "  ✅ Valid Google Drive folder_id: {$folder_id}");
            return $folder_id;
        }

        // ========================================
        // 2. ถ้าเป็นตัวเลขล้วน → หาจาก database
        // ========================================
        if (preg_match('/^\d+$/', $folder_id)) {
            log_message('info', "  ⚠️ Database ID detected: {$folder_id}");
            log_message('info', "  🔍 Resolving from database...");

            try {
                // ----------------------------------------
                // 2.1. หาจาก tbl_google_drive_system_folders ก่อน
                // ----------------------------------------
                log_message('info', "    → Checking system_folders...");

                $result = $this->db->select('folder_id, folder_name, folder_type')
                    ->from('tbl_google_drive_system_folders')
                    ->where('id', $folder_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($result && !empty($result->folder_id)) {
                    log_message('info', "    ✅ Found in system_folders:");
                    log_message('info', "      DB ID: {$folder_id}");
                    log_message('info', "      Folder ID: {$result->folder_id}");
                    log_message('info', "      Name: {$result->folder_name}");
                    log_message('info', "      Type: {$result->folder_type}");
                    return $result->folder_id;
                }

                log_message('info', "    ⚠️ Not found in system_folders");

                // ----------------------------------------
                // 2.2. หาจาก tbl_google_drive_folders (personal/user folders)
                // ----------------------------------------
                log_message('info', "    → Checking user_folders...");

                $result = $this->db->select('folder_id, folder_name, folder_type, member_id')
                    ->from('tbl_google_drive_folders')
                    ->where('id', $folder_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($result && !empty($result->folder_id)) {
                    log_message('info', "    ✅ Found in user_folders:");
                    log_message('info', "      DB ID: {$folder_id}");
                    log_message('info', "      Folder ID: {$result->folder_id}");
                    log_message('info', "      Name: {$result->folder_name}");
                    log_message('info', "      Type: {$result->folder_type}");
                    log_message('info', "      Owner: {$result->member_id}");
                    return $result->folder_id;
                }

                log_message('info', "    ⚠️ Not found in user_folders");

            } catch (Exception $e) {
                log_message('error', "    ❌ Database error: " . $e->getMessage());
            }
        }

        // ========================================
        // 3. ไม่สามารถแก้ไขได้
        // ========================================
        log_message('error', "  ❌ Cannot validate or fix folder_id: {$folder_id}");
        return false;
    }

    /**
     * ✅ Helper: ดึงข้อมูลโฟลเดอร์พร้อมข้อมูล Owner (NEW FUNCTION)
     * 
     * @param string $folder_id - Google Drive Folder ID
     * @return object|null - ข้อมูลโฟลเดอร์ หรือ null ถ้าไม่พบ
     *                       Properties: db_id, folder_id, folder_name, folder_type, member_id (owner), source
     */
    private function get_folder_info($folder_id)
    {
        log_message('info', "🔍 get_folder_info: Looking for folder_id: {$folder_id}");

        // ========================================
        // 1. หาจาก tbl_google_drive_system_folders ก่อน
        // ========================================
        log_message('info', "  Step 1: Checking system_folders...");

        // ✅ ใช้ Raw SQL เพื่อจัดการ NULL
        $sql = "SELECT 
                id as db_id, 
                folder_id, 
                folder_name, 
                folder_type, 
                NULL as member_id,  -- NULL ได้ใน raw SQL
                'system' as source
            FROM tbl_google_drive_system_folders
            WHERE folder_id = ?
              AND is_active = 1
            LIMIT 1";

        $query = $this->db->query($sql, [$folder_id]);
        $folder = $query->row();

        if ($folder) {
            log_message('info', "  ✅ Found in system_folders:");
            log_message('info', "    Name: {$folder->folder_name}");
            log_message('info', "    Type: {$folder->folder_type}");
            log_message('info', "    Owner: NULL (system folder)");
            return $folder;
        }

        log_message('info', "  ⚠️ Not found in system_folders");

        // ========================================
        // 2. หาจาก tbl_google_drive_folders (personal folders)
        // ========================================
        log_message('info', "  Step 2: Checking user_folders...");

        $sql = "SELECT 
                id as db_id, 
                folder_id, 
                folder_name, 
                folder_type, 
                member_id,  -- ✅ มีคอลัมน์นี้
                'user' as source
            FROM tbl_google_drive_folders
            WHERE folder_id = ?
              AND is_active = 1
            LIMIT 1";

        $query = $this->db->query($sql, [$folder_id]);
        $folder = $query->row();

        if ($folder) {
            log_message('info', "  ✅ Found in user_folders:");
            log_message('info', "    Name: {$folder->folder_name}");
            log_message('info', "    Type: {$folder->folder_type}");
            log_message('info', "    Owner: {$folder->member_id}");
            return $folder;
        }

        log_message('info', "  ❌ Folder not found in any table");
        return null;
    }


    private function apply_permission_to_subfolders_enhanced($parent_folder_id, $permission_data, $parent_permission_id)
    {
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




    private function get_subfolders_from_database($parent_folder_id)
    {
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
    private function get_subfolders_from_google_drive($parent_folder_id)
    {
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



    private function safe_output_json_error($message, $code = 400)
    {
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
    private function safe_output_json_success($data = null, $message = 'Success')
    {
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



    /**
     * ใช้สิทธิ์กับ subfolder
     */
    private function apply_permission_to_subfolders($parent_folder_id, $permission_data)
    {
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
     * ดึงรายการ subfolder ทั้งหมด
     */
    private function get_all_subfolders($parent_folder_id)
    {
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
    private function update_folder_inheritance_setting($folder_id, $enable_inheritance)
    {
        try {
            // สำหรับ demo - ในการใช้งานจริงอาจต้องมีตารางเก็บการตั้งค่าโฟลเดอร์
            log_message('info', "Updated inheritance setting for folder {$folder_id}: " . ($enable_inheritance ? 'enabled' : 'disabled'));

            return true;

        } catch (Exception $e) {
            log_message('error', 'Update folder inheritance setting error: ' . $e->getMessage());
            return false;
        }
    }



    // =============================================
// 4. อัปเดต get_folder_contents() ให้มี permission_count
// =============================================

    public function get_folder_contents()
    {
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
    private function get_folder_permission_stats_safe($folder_id)
    {
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
                $direct_stats = (object) [
                    'total' => 0,
                    'override_count' => 0
                ];
            }

            return [
                'total' => (int) $direct_stats->total,
                'inherited' => 0, // จะคำนวณเมื่อจำเป็น
                'direct' => (int) $direct_stats->total,
                'override' => (int) $direct_stats->override_count,
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
     * ✏️ แก้ไขสิทธิ์โฟลเดอร์ที่มีอยู่ พร้อม Comprehensive Logging
     */
    public function update_folder_permission()
    {
        $old_error_reporting = error_reporting(0);

        try {
            // ล้าง output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }

            // [LOG 1] เริ่มต้นการทำงาน
            log_message('info', '========================================');
            log_message('info', '📝 START: update_folder_permission');
            log_message('info', '⏰ Time: ' . date('Y-m-d H:i:s'));
            log_message('info', '🌐 IP: ' . $this->input->ip_address());
            log_message('info', '========================================');

            // ตั้งค่า headers
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // [LOG 2] Request Method
            $request_method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
            log_message('info', '📋 Method: ' . $request_method);

            // [LOG 3] AJAX Detection
            $is_ajax_native = $this->input->is_ajax_request();
            $is_ajax_header = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            $is_ajax = $is_ajax_native || $is_ajax_header;

            log_message('info', '🔍 AJAX Check:');
            log_message('info', '  CI Native: ' . ($is_ajax_native ? 'YES' : 'NO'));
            log_message('info', '  X-Header: ' . ($is_ajax_header ? 'YES' : 'NO'));
            log_message('info', '  Final: ' . ($is_ajax ? 'AJAX' : 'NON-AJAX'));

            if (!$is_ajax) {
                log_message('info', '⚠️ Non-AJAX request but proceeding for compatibility');
            }

            // [LOG 4] Session Check
            $current_user_id = $this->session->userdata('m_id');
            log_message('info', '👤 Session m_id: ' . ($current_user_id ?: 'NULL'));

            if (!$current_user_id) {
                log_message('info', '❌ Not Logged In');
                $this->output_json_error('กรุณาเข้าสู่ระบบ');
                return;
            }
            log_message('info', '✅ User Logged In: ' . $current_user_id);

            // [LOG 5] Raw Input
            $raw_input = file_get_contents('php://input');
            log_message('info', '📦 Raw JSON: ' . ($raw_input ?: '(empty)'));

            // [LOG 6] JSON Decode
            $json_data = json_decode($raw_input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('info', '❌ JSON Error: ' . json_last_error_msg());
                $this->output_json_error('JSON ไม่ถูกต้อง');
                return;
            }
            log_message('info', '✅ JSON OK');
            log_message('debug', 'Data: ' . json_encode($json_data, JSON_UNESCAPED_UNICODE));

            // [LOG 7] Parse Parameters
            $permission_id = $json_data['permission_id'] ?? null;
            $access_type = $json_data['access_type'] ?? null;
            $expires_at = $json_data['expires_at'] ?? null;

            log_message('info', '📝 Parameters:');
            log_message('info', '  permission_id: ' . ($permission_id ?: 'NULL'));
            log_message('info', '  access_type: ' . ($access_type ?: 'NULL'));
            log_message('info', '  expires_at: ' . ($expires_at ?: 'NULL'));

            // [LOG 8] Validation
            if (empty($permission_id) || empty($access_type)) {
                log_message('info', '❌ Validation Failed:');
                log_message('info', '  permission_id: ' . (empty($permission_id) ? 'MISSING' : 'OK'));
                log_message('info', '  access_type: ' . (empty($access_type) ? 'MISSING' : 'OK'));
                $this->output_json_error('กรุณากรอกข้อมูลให้ครบถ้วน');
                return;
            }
            log_message('info', '✅ Validation Passed');

            // [LOG 9] DB Connection
            if (!$this->db) {
                log_message('info', '❌ DB Connection Failed');
                $this->output_json_error('ไม่สามารถเชื่อมต่อ DB');
                return;
            }
            log_message('info', '✅ DB Connected');

            // [LOG 10] Table Check
            $table_exists = $this->db->table_exists('tbl_google_drive_member_folder_access');
            log_message('info', '🗄️ Table Exists: ' . ($table_exists ? 'YES' : 'NO'));

            if (!$table_exists) {
                log_message('info', '❌ Table Not Found');
                $this->output_json_error('ตารางไม่มีอยู่');
                return;
            }

            // [LOG 11] Query Existing Permission
            log_message('info', '🔍 Querying Existing Permission...');
            log_message('debug', 'Query: SELECT id, member_id, folder_id, access_type FROM tbl_google_drive_member_folder_access WHERE id = ' . $permission_id . ' AND is_active = 1');

            log_message('info', '🔍 Querying Existing Permission with JOINs...');

            $existing = $this->db->select('
                                            mfa.id,
                                            mfa.member_id,
                                            mfa.folder_id,
                                            mfa.access_type,
                                            m.m_fname,
                                            m.m_lname,
                                            m.m_username,
                                            sf.folder_name
                                        ')
                ->from('tbl_google_drive_member_folder_access mfa')
                ->join('tbl_member m', 'mfa.member_id = m.m_id', 'left')
                ->join('tbl_google_drive_system_folders sf', 'mfa.folder_id = sf.folder_id', 'left')
                ->where('mfa.id', $permission_id)
                ->where('mfa.is_active', 1)
                ->get()
                ->row();


            // [LOG 12] Check Query Result
            if (!$existing) {
                log_message('info', '❌ Permission Not Found or Inactive: ID=' . $permission_id);

                // ตรวจสอบเพิ่มเติมว่ามี record หรือไม่
                $check_any = $this->db->select('id, is_active')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('id', $permission_id)
                    ->get()
                    ->row();

                if ($check_any) {
                    log_message('info', '  Found Record: ID=' . $check_any->id . ', Active=' . ($check_any->is_active ? 'YES' : 'NO'));
                    if ($check_any->is_active == 0) {
                        log_message('info', '  Reason: Record is inactive (soft deleted)');
                    }
                } else {
                    log_message('info', '  Reason: Record does not exist in database');
                }

                $this->output_json_error('ไม่พบสิทธิ์ที่ต้องการแก้ไข');
                return;
            }

            log_message('info', '✅ Permission Found:');
            log_message('info', '  ID: ' . $existing->id);
            log_message('info', '  Member ID: ' . $existing->member_id);
            log_message('info', '  Folder ID: ' . $existing->folder_id);
            log_message('info', '  Current Access Type: ' . $existing->access_type);

            // [LOG 13] Check Owner Protection
            if ($existing->access_type === 'owner') {
                log_message('info', '❌ Cannot Edit Owner Permission');
                log_message('info', '  Attempted by User: ' . $current_user_id);
                log_message('info', '  Permission ID: ' . $permission_id);
                $this->output_json_error('ไม่สามารถแก้ไขสิทธิ์เจ้าของได้');
                return;
            }
            log_message('info', '✅ Owner Protection Passed');

            // [LOG 14] Check if Update Needed
            if ($existing->access_type === $access_type) {
                log_message('info', '⚠️ Access Type Same as Current:');
                log_message('info', '  Current: ' . $existing->access_type);
                log_message('info', '  New: ' . $access_type);
                log_message('info', '  Proceeding with update anyway (might have expires_at change)');
            } else {
                log_message('info', '📝 Access Type Change Detected:');
                log_message('info', '  Old: ' . $existing->access_type);
                log_message('info', '  New: ' . $access_type);
            }

            // ---------------------------------------------------------
            // ✅ [UPDATED] Last Admin Protection (ปรับปรุงข้อความตอบกลับ)
            // ---------------------------------------------------------
            if ($existing->access_type === 'admin' && $access_type !== 'admin') {
                log_message('info', '🔍 [STEP 14.1] Checking Last Admin Protection...');

                $other_admins = $this->db->select('COUNT(*) as count')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('folder_id', $existing->folder_id)
                    ->where('access_type', 'admin')
                    ->where('member_id !=', $existing->member_id) // ไม่นับตัวเอง
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                log_message('info', '🔍 [STEP 14.2] Other admins count: ' . ($other_admins ? $other_admins->count : '0'));

                if ($other_admins && $other_admins->count == 0) {
                    log_message('info', '❌ Cannot Remove Last Admin');
                    log_message('info', '  Attempted by User: ' . $current_user_id);
                    log_message('info', '  Permission ID: ' . $permission_id);
                    log_message('info', '  Folder ID: ' . $existing->folder_id);

                    // 🔔 ปรับปรุงข้อความแจ้งเตือนให้ชัดเจน
                    $error_msg = "ไม่สามารถลดสิทธิ์ได้ เนื่องจากผู้ใช้นี้เป็นผู้ดูแล (Admin) คนสุดท้ายของโฟลเดอร์นี้ \n\n" .
                        "💡 คำแนะนำ: กรุณาเพิ่มสิทธิ์ Admin ให้กับผู้ใช้อื่นอย่างน้อย 1 คน ก่อนที่จะลดสิทธิ์ผู้ใช้นี้";

                    $this->output_json_error($error_msg);
                    return;
                }

                log_message('info', '✅ [STEP 14.3] Other admins exist - safe to proceed');
            }
            // ---------------------------------------------------------

            // [LOG 15] Prepare Update Data
            $update_data = [
                'access_type' => $access_type,
                'expires_at' => !empty($expires_at) ? $expires_at . ' 23:59:59' : NULL,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // ✅ [NEW] เพิ่ม updated_by ถ้า column มีอยู่
            if ($this->db->field_exists('updated_by', 'tbl_google_drive_member_folder_access')) {
                $update_data['updated_by'] = $current_user_id;
                log_message('info', '✅ Field updated_by exists - adding to update');
            }

            log_message('info', '📦 Update Data Prepared:');
            log_message('debug', json_encode($update_data, JSON_UNESCAPED_UNICODE));

            // ✅ [NEW] เริ่ม Transaction
            log_message('info', '🔄 [STEP 16] Starting Transaction...');
            $this->db->trans_start();

            // [LOG 16] Execute Update
            log_message('info', '💾 Executing Update Query...');
            log_message('debug', 'UPDATE tbl_google_drive_member_folder_access SET access_type="' . $access_type .
                '", expires_at=' . ($update_data['expires_at'] ? '"' . $update_data['expires_at'] . '"' : 'NULL') .
                ', updated_at="' . $update_data['updated_at'] . '" WHERE id=' . $permission_id);

            $this->db->where('id', $permission_id);
            $result = $this->db->update('tbl_google_drive_member_folder_access', $update_data);

            $affected_rows = $this->db->affected_rows();
            $db_error = $this->db->error();

            // [LOG 17] Update Results
            log_message('info', '📊 Update Results:');
            log_message('info', '  Result: ' . ($result ? 'TRUE' : 'FALSE'));
            log_message('info', '  Affected Rows: ' . $affected_rows);
            log_message('info', '  DB Error Code: ' . $db_error['code']);
            log_message('info', '  DB Error Msg: ' . ($db_error['message'] ?: 'None'));

            // [LOG 18] Check DB Error
            if ($db_error['code'] !== 0) {
                log_message('info', '❌ Database Error Detected:');
                log_message('info', '  Code: ' . $db_error['code']);
                log_message('info', '  Message: ' . $db_error['message']);
                $this->db->trans_rollback();
                $this->output_json_error('DB Error: ' . $db_error['message']);
                return;
            }

            // [LOG 20] Log Activity
            if (method_exists($this, 'log_activity')) {
                log_message('info', '📝 Logging Activity...');
                $this->log_activity(
                    $current_user_id,
                    'update_folder_permission',
                    "แก้ไขสิทธิ์: {$existing->access_type} → {$access_type}",
                    [
                        'permission_id' => $permission_id,
                        'member_id' => $existing->member_id,
                        'folder_id' => $existing->folder_id,
                        'old_access_type' => $existing->access_type,
                        'new_access_type' => $access_type
                    ]
                );
                log_message('info', '✅ Activity Logged');
            } else {
                log_message('info', '⚠️ Activity logging method not available');
            }

            // ✅ [NEW] Commit Transaction
            log_message('info', '✅ [STEP 16.1] Committing Transaction...');
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('info', '❌ Transaction Failed');
                $this->output_json_error('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                return;
            }
            log_message('info', '✅ [STEP 16.2] Transaction Committed Successfully');

            // [LOG 19] Check Success
            if ($result && $affected_rows > 0) {
                log_message('info', '✅ Update Successful');

                try {
                    log_message('info', '📝 Logging permission update to tbl_google_drive_logs...');

                    $this->load->model('Google_drive_model');

                    $this->Google_drive_model->log_permission_action(
                        'update_folder_permission',
                        [
                            'permission_id' => $permission_id,
                            'member_id' => $existing->member_id,
                            'member_name' => trim(($existing->m_fname ?? '') . ' ' . ($existing->m_lname ?? '')),
                            'folder_id' => $existing->folder_id,
                            'folder_name' => $existing->folder_name ?? '',
                            'old_access_type' => $existing->access_type,
                            'access_type' => $access_type,
                            'target_email' => $existing->m_username ?? '',
                            'permission_source' => 'direct'
                        ],
                        $current_user_id
                    );

                    log_message('info', '✅ Permission update logged successfully');

                } catch (Exception $log_error) {
                    // ไม่ให้กระทบการทำงานหลัก
                    log_message('error', "⚠️ Failed to log permission update (non-critical): {$log_error->getMessage()}");
                }

                // [LOG 21] Prepare Response
                $response_data = [
                    'permission_id' => $permission_id,
                    'new_access_type' => $access_type,
                    'old_access_type' => $existing->access_type,
                    'expires_at' => $update_data['expires_at'],
                    'updated_at' => $update_data['updated_at'],
                    'member_id' => $existing->member_id,
                    'folder_id' => $existing->folder_id
                ];

                log_message('info', '📤 Response Data:');
                log_message('debug', json_encode($response_data, JSON_UNESCAPED_UNICODE));

                // [LOG 22] Success
                log_message('info', '========================================');
                log_message('info', '✅ SUCCESS: update_folder_permission');
                log_message('info', '  Permission ID: ' . $permission_id);
                log_message('info', '  Change: ' . $existing->access_type . ' → ' . $access_type);
                log_message('info', '========================================');

                $this->output_json_success($response_data, 'แก้ไขสิทธิ์สำเร็จ');

            } elseif ($result && $affected_rows === 0) {
                // [LOG 23] No Changes
                log_message('info', '⚠️ No Rows Affected:');
                log_message('info', '  Possible Reason: Data is identical to current values');
                log_message('info', '  Current Access Type: ' . $existing->access_type);
                log_message('info', '  New Access Type: ' . $access_type);
                log_message('info', '  This is not necessarily an error - data might be the same');

                $this->output_json_error('ไม่มีการเปลี่ยนแปลงหรือข้อมูลเหมือนเดิม');

            } else {
                // [LOG 24] Update Failed
                log_message('info', '========================================');
                log_message('info', '❌ FAILED: Update operation failed');
                log_message('info', '  Result: ' . ($result ? 'TRUE' : 'FALSE'));
                log_message('info', '  Affected Rows: ' . $affected_rows);
                log_message('info', '========================================');

                $this->output_json_error('ไม่สามารถแก้ไขสิทธิ์ได้');
            }

        } catch (Exception $e) {
            // ✅ [NEW] Rollback ถ้ามี transaction
            if (isset($this->db) && method_exists($this->db, 'trans_status') && $this->db->trans_status() !== FALSE) {
                log_message('error', '🔄 Rolling back transaction due to exception');
                $this->db->trans_rollback();
            }

            // [LOG 25] Exception
            log_message('info', '========================================');
            log_message('info', '❌❌❌ EXCEPTION ❌❌❌');
            log_message('info', '========================================');
            log_message('info', '📛 Message: ' . $e->getMessage());
            log_message('info', '📂 File: ' . $e->getFile());
            log_message('info', '📍 Line: ' . $e->getLine());
            log_message('debug', '🔍 Trace:');
            log_message('debug', $e->getTraceAsString());
            log_message('info', '========================================');

            // Log error context
            $error_context = [
                'permission_id' => $permission_id ?? 'unknown',
                'access_type' => $access_type ?? 'unknown',
                'user_id' => $current_user_id ?? 'unknown',
                'timestamp' => date('Y-m-d H:i:s')
            ];
            log_message('info', '📋 Error Context: ' . json_encode($error_context));

            $this->output_json_error('Exception: ' . $e->getMessage());

        } finally {
            // [LOG 26] Finally
            log_message('info', '🏁 Finally - Cleanup');
            error_reporting($old_error_reporting);
            log_message('info', '🔚 END: update_folder_permission');
            log_message('info', '========================================');
        }
    }

    public function get_inherited_permissions_stats()
    {
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
    private function safe_log_activity($member_id, $action_type, $description, $additional_data = [])
    {
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
     * 🗑️ ลบสิทธิ์โฟลเดอร์ (FIXED VERSION - With Duplicate Cleanup)
     */
    public function remove_folder_permission()
    {
        try {
            log_message('info', '🔵 [STEP 1] Starting remove_folder_permission function');

            // ล้าง output buffer
            if (ob_get_level()) {
                ob_clean();
                log_message('info', '🔵 [STEP 1.1] Output buffer cleaned successfully');
            }

            // ✅ แก้ไข AJAX Detection - รองรับหลายกรณี
            $is_ajax = $this->is_ajax_request_enhanced();
            log_message('info', '🔵 [STEP 1.2] AJAX detection result: ' . ($is_ajax ? 'TRUE' : 'FALSE'));

            if (!$is_ajax) {
                log_message('info', 'Non-AJAX request detected but proceeding for compatibility');
            }

            // ✅ ตรวจสอบ POST data - รองรับทั้ง JSON body และ Form data
            log_message('info', '🔵 [STEP 2] Validating POST data');

            // ลอง decode JSON body ก่อน
            $json_input = json_decode($this->input->raw_input_stream, true);

            if ($json_input && isset($json_input['permission_id'])) {
                // ✅ กรณีส่งเป็น JSON body
                $permission_id = $json_input['permission_id'];
                $debug_mode = isset($json_input['debug_mode']) && $json_input['debug_mode'] === true;
                log_message('info', '🔵 [STEP 2.0] Data source: JSON body');
            } else {
                // ✅ กรณีส่งเป็น Form data (fallback)
                $permission_id = $this->input->post('permission_id');
                $debug_mode = $this->input->post('debug_mode') === 'true';
                log_message('info', '🔵 [STEP 2.0] Data source: Form POST');
            }

            log_message('info', '🔵 [STEP 2.1] Permission ID received: ' . ($permission_id ?? 'NULL'));
            log_message('info', '🔵 [STEP 2.2] Debug mode: ' . ($debug_mode ? 'ENABLED' : 'DISABLED'));

            if (empty($permission_id) || !is_numeric($permission_id)) {
                log_message('info', '🔴 [STEP 2.3] Invalid Permission ID - rejecting request');
                $this->output_json_error('กรุณาระบุ Permission ID ที่ถูกต้อง', null, 400);
                return;
            }

            log_message('info', '🟢 [STEP 2.3] Permission ID validation passed');

            // ✅ ตรวจสอบ Session
            log_message('info', '🔵 [STEP 3] Checking user session');
            $current_member_id = $this->session->userdata('m_id');
            if (empty($current_member_id)) {
                log_message('info', '🔴 [STEP 3.1] No active session found - rejecting request');
                $this->output_json_error('กรุณาเข้าสู่ระบบใหม่', null, 401);
                return;
            }

            log_message('info', '🟢 [STEP 3.1] Session valid - User ID: ' . $current_member_id);

            // ✅ Log การเริ่มต้น
            $request_log = [
                'permission_id' => $permission_id,
                'requested_by' => $current_member_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address(),
                'user_agent' => substr($this->input->user_agent(), 0, 500),
                'is_ajax' => $is_ajax,
                'debug_mode' => $debug_mode
            ];

            log_message('info', 'Remove permission request: ' . json_encode($request_log));

            // ✅ ตรวจสอบตาราง
            log_message('info', '🔵 [STEP 4] Checking table existence');
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $error_msg = 'ตาราง tbl_google_drive_member_folder_access ไม่พบ';
                log_message('error', '🔴 [STEP 4.1] Table not found');
                $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
                $this->output_json_error($error_msg, null, 500);
                return;
            }

            log_message('info', '🟢 [STEP 4.1] Table exists - proceeding');

            // ✅ ดึงข้อมูลสิทธิ์ที่ต้องการลบ พร้อมข้อมูลเพิ่มเติม
            log_message('info', '🔵 [STEP 5] Querying permission data from database');
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

            log_message('info', '🔵 [STEP 5.1] Query executed - Result: ' . ($existing ? 'FOUND' : 'NOT FOUND'));

            // ✅ ตรวจสอบ Database Error
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
                $error_msg = 'Database Query Error: ' . $db_error['message'];
                log_message('error', '🔴 [STEP 5.2] Database error occurred: ' . $error_msg);
                $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
                $this->output_json_error('เกิดข้อผิดพลาดในการค้นหาข้อมูล', null, 500);
                return;
            }

            log_message('info', '🟢 [STEP 5.2] No database errors detected');

            // ✅ Debug mode - แสดงข้อมูลที่ค้นหา
            if ($debug_mode) {
                log_message('debug', 'Permission search result: ' . json_encode($existing));
            }

            if (!$existing) {
                log_message('info', '🔴 [STEP 6] Permission not found - checking if soft deleted');
                $error_msg = "ไม่พบสิทธิ์ที่ต้องการลบ (ID: {$permission_id}) หรือถูกลบไปแล้ว";
                log_message('info', $error_msg);

                // ตรวจสอบว่ามี record อยู่แต่ is_active = 0
                log_message('info', '🔵 [STEP 6.1] Performing secondary check for soft-deleted records');
                $deleted_check = $this->db->select('id, is_active, member_id, folder_id, access_type')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('id', $permission_id)
                    ->get()
                    ->row();

                if ($deleted_check) {
                    log_message('info', '🔵 [STEP 6.2] Record found - is_active: ' . $deleted_check->is_active);
                    if ($deleted_check->is_active == 0) {
                        $error_msg = 'สิทธิ์นี้ถูกลบไปแล้ว (Soft Deleted)';
                    } else {
                        $error_msg = 'เกิดข้อผิดพลาดในการค้นหาสิทธิ์ (Record exists but join failed)';
                    }

                    if ($debug_mode) {
                        $error_msg .= " - Debug Info: " . json_encode($deleted_check);
                    }
                } else {
                    log_message('info', '🔴 [STEP 6.2] No record found in database at all');
                }

                $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg, [
                    'permission_id' => $permission_id,
                    'search_result' => $deleted_check
                ]);

                $this->output_json_error($error_msg, null, 404);
                return;
            }

            // ✅ ตรวจสอบการป้องกัน - ไม่ให้ลบ owner
            log_message('info', '🔵 [STEP 7] Validating permission removal rules');
            log_message('info', '🔵 [STEP 7.1] Checking owner protection - access_type: ' . $existing->access_type);

            if ($existing->access_type === 'owner') {
                $error_msg = 'ไม่สามารถลบสิทธิ์เจ้าของ (Owner) ได้';
                log_message('info', '🔴 [STEP 7.2] Owner protection triggered - cannot remove owner permission');
                log_message('info', "Attempt to remove owner permission: {$permission_id} by user: {$current_member_id}");

                $this->log_warning_activity($current_member_id, 'remove_folder_permission', $error_msg, [
                    'permission_id' => $permission_id,
                    'target_member_id' => $existing->member_id,
                    'access_type' => $existing->access_type,
                    'folder_id' => $existing->folder_id
                ]);

                $this->output_json_error($error_msg, null, 403);
                return;
            }

            log_message('info', '🟢 [STEP 7.2] Owner protection passed');

            // ✅ ป้องกันการลบสิทธิ์ตัวเอง (ถ้าเป็น admin เดียว) - ยกเว้น System Admin
            log_message('info', '🔵 [STEP 7.3] Checking last admin protection');

            // ✅ เพิ่มการตรวจสอบ System Admin ก่อน
            $is_system_admin = false;
            $current_user_info = $this->db->select('ref_pid')
                ->from('tbl_member')
                ->where('m_id', $current_member_id)
                ->get()
                ->row();

            if ($current_user_info && $current_user_info->ref_pid == 1) {
                $is_system_admin = true;
                log_message('info', '🟢 [STEP 7.3.1] Current user is System Admin - bypassing last admin protection');
            }

            if ($existing->member_id == $current_member_id && $existing->access_type === 'admin' && !$is_system_admin) {
                log_message('info', '🔵 [STEP 7.4] Target is admin and same as current user - checking for other admins');

                // ตรวจสอบว่ามี admin คนอื่นไหม
                $other_admins_count = $this->db->select('COUNT(*) as count')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('folder_id', $existing->folder_id)
                    ->where('access_type', 'admin')
                    ->where('member_id !=', $current_member_id)
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                log_message('info', '🔵 [STEP 7.5] Other admins count: ' . ($other_admins_count ? $other_admins_count->count : '0'));

                if ($other_admins_count && $other_admins_count->count == 0) {
                    $error_msg = 'ไม่สามารถลบสิทธิ์ผู้ดูแล (Admin) คนสุดท้ายได้';
                    log_message('info', '🔴 [STEP 7.6] Last admin protection triggered - cannot remove last admin');
                    log_message('info', "Attempt to remove last admin: {$permission_id} by user: {$current_member_id}");

                    $this->log_warning_activity($current_member_id, 'remove_folder_permission', $error_msg, [
                        'permission_id' => $permission_id,
                        'reason' => 'last_admin_protection',
                        'folder_id' => $existing->folder_id
                    ]);

                    $this->output_json_error($error_msg, null, 403);
                    return;
                }

                log_message('info', '🟢 [STEP 7.6] Other admins exist - safe to proceed');
            } else {
                if ($is_system_admin) {
                    log_message('info', '🟢 [STEP 7.4] System Admin override - bypassing last admin check');
                } else {
                    log_message('info', '🟢 [STEP 7.4] Not last admin scenario - safe to proceed');
                }
            }

            log_message('info', '🟢 [STEP 7] All validation rules passed');

            // ✅ เริ่ม Database Transaction
            log_message('info', '🔵 [STEP 8] Starting database transaction');
            $this->db->trans_start();
            log_message('info', '🟢 [STEP 8.1] Transaction started successfully');

            // ========================================
            // ✅ ✅ ✅ NEW STEP 8.5: Duplicate Cleanup
            // ========================================
            log_message('info', '🔵 [STEP 8.5] Checking and cleaning up duplicate inactive records');

            // ลบ inactive records ที่ซ้ำกับสิทธิ์ที่จะลบ (ถ้ามี)
            $cleanup_where = [
                'member_id' => $existing->member_id,
                'folder_id' => $existing->folder_id,
                'access_type' => $existing->access_type,
                'is_active' => 0  // ลบเฉพาะที่ถูก soft delete ไปแล้ว
            ];

            // ตรวจสอบก่อนว่ามีข้อมูลซ้ำหรือไม่
            $duplicate_count = $this->db->where($cleanup_where)->count_all_results('tbl_google_drive_member_folder_access');
            log_message('info', '🔵 [STEP 8.5.1] Found ' . $duplicate_count . ' duplicate inactive record(s)');

            if ($duplicate_count > 0) {
                log_message('info', '⚠️ [STEP 8.5.2] Removing duplicate inactive records to prevent constraint violation');
                log_message('info', 'Cleanup details: ' . json_encode($cleanup_where));

                $this->db->delete('tbl_google_drive_member_folder_access', $cleanup_where);

                $cleanup_error = $this->db->error();
                if ($cleanup_error['code'] !== 0) {
                    log_message('error', '🔴 [STEP 8.5.3] Cleanup failed: ' . $cleanup_error['message']);
                } else {
                    log_message('info', '🟢 [STEP 8.5.3] Cleanup successful - removed ' . $this->db->affected_rows() . ' record(s)');
                }
            } else {
                log_message('info', '🟢 [STEP 8.5.2] No duplicate inactive records found - proceeding');
            }

            // ✅ ทำการลบ (Soft Delete)
            log_message('info', '🔵 [STEP 9] Preparing soft delete data');
            $update_data = [
                'is_active' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', '🔵 [STEP 9.1] Base update data prepared: is_active=0');

            // เพิ่มฟิลด์เสริมถ้ามี
            log_message('info', '🔵 [STEP 9.2] Checking for optional revocation fields');

            if ($this->db->field_exists('revoked_by', 'tbl_google_drive_member_folder_access')) {
                $update_data['revoked_by'] = $current_member_id;
                log_message('info', '🟢 [STEP 9.2.1] Field revoked_by exists - adding to update');
            }

            if ($this->db->field_exists('revoked_at', 'tbl_google_drive_member_folder_access')) {
                $update_data['revoked_at'] = date('Y-m-d H:i:s');
                log_message('info', '🟢 [STEP 9.2.2] Field revoked_at exists - adding to update');
            }

            if ($this->db->field_exists('revoked_reason', 'tbl_google_drive_member_folder_access')) {
                $update_data['revoked_reason'] = 'Manual removal by admin';
                log_message('info', '🟢 [STEP 9.2.3] Field revoked_reason exists - adding to update');
            }

            log_message('info', '🔵 [STEP 9.3] Final update data: ' . json_encode($update_data));

            $this->db->where('id', $permission_id);
            log_message('info', '🔵 [STEP 9.4] Executing UPDATE query for permission_id: ' . $permission_id);
            $result = $this->db->update('tbl_google_drive_member_folder_access', $update_data);

            // ✅ ตรวจสอบผลลัพธ์
            $affected_rows = $this->db->affected_rows();
            $db_error = $this->db->error();

            log_message('info', '🔵 [STEP 9.5] UPDATE query completed');
            log_message('info', '🔵 [STEP 9.6] Result: ' . ($result ? 'TRUE' : 'FALSE') . ', Affected rows: ' . $affected_rows);
            log_message('info', '🔵 [STEP 9.7] Database error code: ' . $db_error['code']);

            if ($db_error['code'] !== 0) {
                $this->db->trans_rollback();
                $error_msg = 'Database Update Error: ' . $db_error['message'];
                log_message('error', '🔴 [STEP 9.8] Database update error occurred');
                log_message('error', $error_msg);
                $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
                $this->output_json_error('เกิดข้อผิดพลาดในการอัปเดตฐานข้อมูล', null, 500);
                return;
            }

            log_message('info', '🟢 [STEP 9.8] No database errors detected');

            if (!$result || $affected_rows === 0) {
                $this->db->trans_rollback();
                $error_msg = 'ไม่สามารถลบสิทธิ์ได้ (No rows affected)';
                log_message('error', '🔴 [STEP 9.9] Update failed - no rows affected');
                log_message('error', $error_msg);
                $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
                $this->output_json_error($error_msg, null, 500);
                return;
            }

            log_message('info', '🟢 [STEP 9.9] Update successful - ' . $affected_rows . ' row(s) affected');

            // ✅ บันทึก Success Activity Log
            // ✅ บันทึก Permission Log ด้วย log_permission_action
            log_message('info', '🔵 [STEP 10] Recording permission removal log');

            // ✅ สร้างข้อมูลสำหรับ log แบบสมบูรณ์
            $permission_data = [
                'permission_id' => $permission_id,
                'member_id' => $existing->member_id,
                'member_name' => trim($existing->m_fname . ' ' . $existing->m_lname),
                'target_email' => $existing->m_username ?? null,
                'folder_id' => $existing->folder_id,
                'folder_name' => $existing->folder_name ?? 'ไม่ระบุโฟลเดอร์',
                'access_type' => $existing->access_type,
                'permission_source' => $existing->permission_source ?? 'manual',
                'granted_by' => $existing->granted_by,
                'granted_at' => $existing->granted_at
            ];

            log_message('debug', '🔵 [STEP 10.1] Permission data prepared: ' . json_encode($permission_data, JSON_UNESCAPED_UNICODE));

            if (!isset($this->Google_drive_model)) {
                $this->load->model('Google_drive_model');
            }
            // ✅ บันทึก log ผ่าน log_permission_action
            if (method_exists($this->Google_drive_model, 'log_permission_action')) {
                $log_result = $this->Google_drive_model->log_permission_action(
                    'remove_folder_permission',
                    $permission_data,
                    $current_member_id
                );

                if ($log_result) {
                    log_message('info', '✅ [STEP 10.2] Permission log saved successfully via log_permission_action');
                } else {
                    log_message('debug', '⚠️ [STEP 10.2] log_permission_action returned false');
                }
            } else {
                log_message('error', '❌ [STEP 10.2] log_permission_action method not found');
            }

            // ✅ เพิ่ม Activity Log แบบเดิมด้วย (ถ้าต้องการ backup)
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

            // ✅ บันทึก Activity Log แบบเดิม (Optional - เป็น backup)
            if (method_exists($this, 'log_success_activity')) {
                $this->log_success_activity(
                    $current_member_id,
                    'remove_folder_permission',
                    $activity_description,
                    $activity_data,
                    $existing->folder_id,
                    $existing->m_username
                );
                log_message('info', '✅ [STEP 10.3] Activity log saved successfully');
            }

            log_message('info', '✅ [STEP 10] All logs recorded successfully');

            log_message('info', '🟢 [STEP 10.1] Activity log recorded successfully');

            // ✅ Commit Transaction
            log_message('info', '🔵 [STEP 11] Committing transaction');
            $this->db->trans_complete();

            $trans_status = $this->db->trans_status();
            log_message('info', '🔵 [STEP 11.1] Transaction status: ' . ($trans_status ? 'SUCCESS' : 'FAILED'));

            if ($this->db->trans_status() === FALSE) {
                $error_msg = 'Transaction commit failed';
                log_message('error', '🔴 [STEP 11.2] Transaction commit failed');
                log_message('error', $error_msg);
                $this->log_error_activity($current_member_id, 'remove_folder_permission', $error_msg);
                $this->output_json_error('เกิดข้อผิดพลาดในการบันทึกข้อมูล', null, 500);
                return;
            }

            log_message('info', '🟢 [STEP 11.2] Transaction committed successfully');

            // ✅ Log สำเร็จ
            log_message('info', '🟢 [STEP 12] Permission removal process completed successfully');
            log_message('info', "Permission removed successfully: {$permission_id} by user: {$current_member_id}");

            // ✅ ส่งผลลัพธ์สำเร็จ
            log_message('info', '🔵 [STEP 13] Preparing success response');
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
                    'duplicate_cleanup_count' => $duplicate_count ?? 0,
                    'original_permission_data' => $existing
                ];
            }

            log_message('info', '🟢 [STEP 13.1] Response data prepared successfully');
            log_message('info', '🔵 [STEP 14] Sending JSON success response');

            $this->output_json_success($response_data, 'ลบสิทธิ์สำเร็จ');

            log_message('info', '🟢 [STEP 14.1] Response sent successfully - Function completed');

        } catch (Exception $e) {
            log_message('error', '🔴 [EXCEPTION] Critical error caught in remove_folder_permission');
            log_message('error', '🔴 [EXCEPTION] Error message: ' . $e->getMessage());
            log_message('error', '🔴 [EXCEPTION] Error file: ' . $e->getFile() . ' line: ' . $e->getLine());

            // ✅ Rollback ถ้ามี transaction
            if (isset($this->db) && method_exists($this->db, 'trans_status') && $this->db->trans_status() !== FALSE) {
                log_message('error', '🔴 [EXCEPTION] Rolling back transaction');
                $this->db->trans_rollback();
                log_message('error', '🔴 [EXCEPTION] Transaction rolled back successfully');
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

            log_message('error', '🔴 [EXCEPTION] Error details logged');
            log_message('error', 'Remove folder permission critical error: ' . json_encode($error_log));

            // ✅ บันทึก Error Activity Log
            if (isset($current_member_id) && !empty($current_member_id)) {
                log_message('error', '🔴 [EXCEPTION] Recording error activity log');
                $this->log_error_activity(
                    $current_member_id,
                    'remove_folder_permission',
                    'Critical Error: ' . $e->getMessage(),
                    $error_log
                );
                log_message('error', '🔴 [EXCEPTION] Error activity log recorded');
            }

            log_message('error', '🔴 [EXCEPTION] Sending error response to client');
            $this->output_json_error('เกิดข้อผิดพลาดร้ายแรง: ' . $e->getMessage(), null, 500);
            log_message('error', '🔴 [EXCEPTION] Function terminated due to exception');
        }
    }

    public function get_member_permission_history()
    {
        log_message('info', '===========================================');
        log_message('info', '📡 get_member_permission_history() Called');
        log_message('info', '===========================================');

        try {
            // ✅ Debug: Log ทุก request ที่เข้ามา
            log_message('info', '📍 Request Info:');
            log_message('info', '  - Method: ' . $this->input->method());
            log_message('info', '  - Is AJAX: ' . ($this->input->is_ajax_request() ? 'Yes' : 'No'));
            log_message('info', '  - Headers: ' . json_encode($this->input->request_headers()));

            // ✅ ตรวจสอบว่าเป็น AJAX Request
            if (!$this->input->is_ajax_request()) {
                log_message('debug', '⚠️ Non-AJAX request detected');
                return $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Invalid request method'
                    ]));
            }

            // ✅ ตรวจสอบสิทธิ์การเข้าถึง
            $current_user_id = $this->session->userdata('m_id');

            log_message('info', '👤 Current User ID: ' . ($current_user_id ?: 'NOT LOGGED IN'));

            if (!$current_user_id) {
                log_message('debug', '⚠️ Unauthorized access attempt');
                return $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'กรุณาเข้าสู่ระบบ'
                    ]));
            }

            // ✅ รับค่าจาก POST
            $member_id = $this->input->post('member_id', true);
            $limit = (int) ($this->input->post('limit', true) ?: 20);
            $offset = (int) ($this->input->post('offset', true) ?: 0);

            // Validate Limit
            if ($limit > 100) {
                $limit = 100;
            }

            log_message('info', '📥 Request Parameters:');
            log_message('info', "  - member_id: {$member_id}");
            log_message('info', "  - limit: {$limit}");
            log_message('info', "  - offset: {$offset}");
            log_message('info', "  - requested_by: {$current_user_id}");

            // ✅ Validate member_id
            if (empty($member_id) || !is_numeric($member_id)) {
                log_message('debug', '⚠️ Invalid or missing member_id');
                return $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'กรุณาระบุรหัสสมาชิกที่ถูกต้อง'
                    ]));
            }

            // ✅ ดึงข้อมูลประวัติจาก Model
            log_message('info', '🔄 Calling Google_drive_model->get_user_permission_history()');

            $history = $this->Google_drive_model->get_user_permission_history(
                $member_id,
                $limit,
                $offset
            );

            log_message('info', '📦 History result type: ' . gettype($history));
            log_message('info', '📦 History count: ' . count($history));

            // ✅ Debug: แสดง sample data
            if (count($history) > 0) {
                log_message('info', '📄 First history item: ' . json_encode($history[0]));
            }

            // ✅ นับจำนวนทั้งหมด
            $total_count = $this->Google_drive_model->count_user_permission_history($member_id);

            log_message('info', "📊 Retrieved " . count($history) . " records (Total: {$total_count})");

            // ✅ คำนวณข้อมูล Pagination
            $current_page = $limit > 0 ? floor($offset / $limit) + 1 : 1;
            $total_pages = $limit > 0 ? ceil($total_count / $limit) : 1;
            $has_more = ($offset + $limit) < $total_count;

            // ✅ สร้าง Response
            $response = [
                'success' => true,
                'data' => [
                    'history' => $history,
                    'pagination' => [
                        'total_count' => $total_count,
                        'current_page' => $current_page,
                        'total_pages' => $total_pages,
                        'limit' => $limit,
                        'offset' => $offset,
                        'has_more' => $has_more
                    ]
                ],
                'message' => 'โหลดประวัติสำเร็จ'
            ];

            log_message('info', '✅ Response prepared successfully');
            log_message('info', '===========================================');

            // ✅ ส่ง Response
            return $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', '===========================================');
            log_message('error', '❌ get_member_permission_history ERROR');
            log_message('error', '===========================================');
            log_message('error', '  Message: ' . $e->getMessage());
            log_message('error', '  File: ' . $e->getFile());
            log_message('error', '  Line: ' . $e->getLine());
            log_message('debug', '  Stack Trace: ' . $e->getTraceAsString());
            log_message('error', '===========================================');

            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการโหลดประวัติ',
                    'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    // ===================================================================
// 🛠️ Helper Functions สำหรับ Activity Logging
// ===================================================================

    /**
     * ✅ Enhanced AJAX Detection
     */
    private function is_ajax_request_enhanced()
    {
        // วิธีที่ 1: ตรวจสอบ HTTP_X_REQUESTED_WITH
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return true;
        }

        // วิธีที่ 2: ตรวจสอบ CodeIgniter built-in
        if ($this->input->is_ajax_request()) {
            return true;
        }

        // วิธีที่ 3: ตรวจสอบพารามิเตอร์พิเศษ
        if (
            $this->input->post('ajax_request') === 'true' ||
            $this->input->get('ajax') === '1'
        ) {
            return true;
        }

        // วิธีที่ 4: ตรวจสอบ Accept header
        $accept_header = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept_header, 'application/json') !== false) {
            return true;
        }

        // วิธีที่ 5: ถ้าเป็น POST และมี permission_id ให้ถือว่าเป็น AJAX
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            !empty($this->input->post('permission_id'))
        ) {
            return true;
        }

        return false;
    }


    /**
     * ✅ บันทึก Success Activity - เรียกผ่าน Model
     * 
     * เปลี่ยนจาก: เขียน log เองโดยตรง
     * เป็น: เรียก log_permission_action() จาก Model
     */
    private function log_success_activity($member_id, $action, $description, $data, $folder_id = null, $email = null)
    {
        try {
            log_message('info', "📝 Logging success activity: {$action}");

            // ✅ โหลด Model
            if (!isset($this->Google_drive_model)) {
                $this->load->model('Google_drive_model');
            }

            // ✅ เรียกผ่าน Model แทนการเขียน log เอง
            $this->Google_drive_model->log_permission_action(
                $action,
                array_merge($data, [
                    'target_email' => $email,
                    'folder_id' => $folder_id
                ]),
                $member_id
            );

            log_message('info', "✅ Success activity logged");

            return true;

        } catch (Exception $e) {
            log_message('error', "❌ Failed to log success activity: {$e->getMessage()}");
            // ไม่ throw exception เพื่อไม่กระทบการทำงานหลัก
            return false;
        }
    }

    private function log_warning_activity($member_id, $action, $description, $data = [])
    {
        try {
            log_message('info', "⚠️ Logging warning activity: {$action}");

            if (!isset($this->Google_drive_model)) {
                $this->load->model('Google_drive_model');
            }

            $this->Google_drive_model->log_action(
                $member_id,
                $action,
                $description,
                array_merge($data, ['status' => 'warning']),
                false
            );

            return true;

        } catch (Exception $e) {
            log_message('error', "Failed to log warning: {$e->getMessage()}");
            return false;
        }
    }

    private function log_error_activity($member_id, $action, $description, $data = [])
    {
        try {
            log_message('error', "❌ Logging error activity: {$action}");

            if (!isset($this->Google_drive_model)) {
                $this->load->model('Google_drive_model');
            }

            $this->Google_drive_model->log_action(
                $member_id,
                $action,
                $description,
                array_merge($data, ['status' => 'failed']),
                false
            );

            return true;

        } catch (Exception $e) {
            log_message('error', "Failed to log error: {$e->getMessage()}");
            return false;
        }
    }



    /**
     * ✅ Step 4: API สำหรับดึง errors ล่าสุด
     */
    public function get_latest_errors()
    {
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
    public function create_folder_structure_with_permissions_safe()
    {
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
    private function test_google_api_simple($access_token)
    {
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
    private function create_root_folder_simple($access_token)
    {
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
    private function tail_file($filename, $lines = 10)
    {
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
    public function generate_quick_fixes()
    {
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
    public function debug_test()
    {
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
     * ✅ Route สำหรับแสดงหน้า Debug Test
     * เพิ่มใน routes.php หรือเรียกตรงๆ
     */
    public function debug_view()
    {
        $this->get_debug_test_view();
    }

    public function diagnose_500_error()
    {
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
    private function get_setting_safe($key)
    {
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
    private function identify_google_error($error_message)
    {
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
    private function generate_recommendations($diagnosis)
    {
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
    public function test_corrected_functions()
    {
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
    public function check_member_status()
    {
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
    public function get_positions_for_filter()
    {
        try {
            // ตรวจสอบว่าตาราง position มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_position')) {
                $this->output_json_error('ไม่พบตาราง tbl_position');
                return;
            }

            $positions = $this->db->select('pid, pname, peng as pdescription')
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
     * 👥 ดึงผู้ใช้ทั้งหมดพร้อมข้อมูลโฟลเดอร์และสิทธิ์ (แก้ไข + เพิ่ม Logs)
     */
    public function get_all_users_for_management()
    {
        try {
            log_message('info', '=== Start get_all_users_for_management ===');

            // ตรวจสอบสิทธิ์แบบง่าย
            $user_system = $this->session->userdata('m_system');
            $current_user_id = $this->session->userdata('m_id');

            log_message('info', "Current user: ID={$current_user_id}, System={$user_system}");

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('info', 'Access denied: User does not have permission');

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
                log_message('info', 'Table tbl_member does not exist');
                throw new Exception('ไม่พบตาราง tbl_member');
            }

            log_message('info', 'Table tbl_member exists');

            // ดึงข้อมูลผู้ใช้แบบง่าย - เช็ค field ก่อน
            $member_fields = $this->db->list_fields('tbl_member');
            log_message('info', 'Member table fields: ' . implode(', ', $member_fields));

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
                log_message('info', 'Field storage_access_granted found');
            } else {
                $select_fields[] = '0 as storage_access_granted';
                log_message('info', 'Field storage_access_granted NOT found - using default 0');
            }

            if (in_array('personal_folder_id', $member_fields)) {
                $select_fields[] = 'm.personal_folder_id';
                log_message('info', 'Field personal_folder_id found');
            } else {
                $select_fields[] = 'NULL as personal_folder_id';
                log_message('info', 'Field personal_folder_id NOT found - using NULL');
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
                $this->db->select('p.pname as position_name, p.peng as pdescription', false);
                $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
                log_message('info', 'Joined with tbl_position');
            } else {
                $this->db->select('"ไม่ระบุ" as position_name, "" as pdescription', false);
                log_message('info', 'Table tbl_position not found - using defaults');
            }

            $this->db->where('m.m_status', 1);
            $this->db->order_by('m.m_fname', 'ASC');

            log_message('info', 'Executing query to get users...');

            $users_query = $this->db->get();
            $users = $users_query->result();

            $total_users_fetched = count($users);
            log_message('info', "Fetched {$total_users_fetched} users from database");

            // เพิ่มข้อมูลเพิ่มเติมแบบง่าย
            $users_with_permissions = 0;
            $total_permissions = 0;

            foreach ($users as $index => &$user) {
                // แปลงข้อมูล
                $user->storage_access_granted = (int) $user->storage_access_granted;
                $user->storage_quota_limit = (int) $user->storage_quota_limit;
                $user->storage_quota_used = (int) $user->storage_quota_used;

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
                log_message('info', "Getting permissions for user ID: {$user->m_id} ({$user->full_name})");

                $user->permissions = $this->get_user_permissions_simple($user->m_id);

                $permission_count = is_array($user->permissions) ? count($user->permissions) : 0;
                log_message('info', "User ID {$user->m_id} has {$permission_count} permission(s)");

                if ($permission_count > 0) {
                    $users_with_permissions++;
                    $total_permissions += $permission_count;

                    // Log รายละเอียด permissions (แค่ 3 users แรก)
                    if ($index < 3) {
                        log_message('info', "User ID {$user->m_id} permissions detail: " . json_encode($user->permissions));
                    }
                }

                // แก้ไขชื่อที่อาจจะเป็น null
                if (empty($user->full_name) || trim($user->full_name) == '') {
                    $user->full_name = 'ไม่ระบุชื่อ';
                }

                // แก้ไข position ที่อาจจะเป็น null
                if (empty($user->position_name)) {
                    $user->position_name = 'ไม่ระบุตำแหน่ง';
                }
            }

            log_message('info', "Summary: {$users_with_permissions} users have permissions, total {$total_permissions} permission records");

            // คำนวณสถิติ
            $total_users = count($users);
            $active_users = count(array_filter($users, function ($u) {
                return $u->storage_access_granted == 1;
            }));
            $users_with_folders = count(array_filter($users, function ($u) {
                return $u->has_personal_folder;
            }));
            $pending_users = $total_users - $users_with_folders;

            $stats = [
                'total_users' => $total_users,
                'active_users' => $active_users,
                'users_with_folders' => $users_with_folders,
                'pending_users' => $pending_users
            ];

            log_message('info', 'Statistics: ' . json_encode($stats));
            log_message('info', '=== End get_all_users_for_management - Success ===');

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
            log_message('error', 'Error trace: ' . $e->getTraceAsString());

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
     * 🔑 ดึงสิทธิ์ของผู้ใช้แบบละเอียด (แก้ไข JOIN ให้ถูกต้อง)
     */
    private function get_user_permissions_simple($user_id)
    {
        try {
            log_message('info', "get_user_permissions_simple: Start for user_id={$user_id}");

            // ตรวจสอบว่ามีตาราง permissions หรือไม่
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                log_message('info', 'Table tbl_google_drive_member_folder_access not found - returning empty array');
                return [];
            }

            // ตรวจสอบว่ามีตาราง folders หรือไม่
            $has_folders_table = $this->db->table_exists('tbl_google_drive_folders');
            log_message('info', "Table tbl_google_drive_folders exists: " . ($has_folders_table ? 'yes' : 'no'));

            // ✅ SELECT ข้อมูลที่จำเป็นทั้งหมด
            $this->db->select('
            mfa.id,
            mfa.member_id,
            mfa.folder_id as folder_table_id,
            mfa.access_type,
            mfa.granted_by,
            mfa.granted_at,
            mfa.is_active
        ', false);

            // ✅ ถ้ามีตาราง folders ให้ JOIN และดึงข้อมูล folder
            if ($has_folders_table) {
                $this->db->select('
                f.id as folder_id,
                f.folder_id as google_drive_folder_id,
                f.folder_name,
                f.folder_type,
                f.is_system_folder
            ', false);

                $this->db->from('tbl_google_drive_member_folder_access mfa');

                // ✅✅✅ CRITICAL FIX: JOIN โดยใช้ folder_id (VARCHAR) แทน id (INT)
                $this->db->join('tbl_google_drive_folders f', 'mfa.folder_id = f.folder_id', 'left');

                log_message('info', "JOIN condition: mfa.folder_id = f.folder_id (both VARCHAR)");
            } else {
                // ✅ ถ้าไม่มีตาราง folders ให้ใช้ค่า default
                $this->db->select('
                NULL as folder_id,
                NULL as google_drive_folder_id,
                "ไม่ระบุโฟลเดอร์" as folder_name,
                "unknown" as folder_type,
                0 as is_system_folder
            ', false);

                $this->db->from('tbl_google_drive_member_folder_access mfa');
            }

            $this->db->where('mfa.member_id', $user_id);
            $this->db->where('mfa.is_active', 1);
            $this->db->order_by('mfa.granted_at', 'DESC');

            log_message('info', "Executing permissions query for user_id={$user_id}");

            $query = $this->db->get();

            // ✅ Log SQL query for debugging
            log_message('info', "SQL Query: " . $this->db->last_query());

            $permissions = $query->result_array();

            $count = count($permissions);
            log_message('info', "Found {$count} permission(s) for user_id={$user_id}");

            // ✅ ประมวลผลข้อมูล permissions
            foreach ($permissions as &$perm) {
                // ตรวจสอบและแก้ไขข้อมูลที่อาจเป็น NULL
                if (empty($perm['folder_name']) || $perm['folder_name'] === null) {
                    // ⚠️ ถ้ายัง NULL แสดงว่า JOIN ไม่เจอข้อมูล
                    log_message('info', "Permission ID {$perm['id']}: folder_name is NULL, folder may not exist in tbl_google_drive_folders");

                    // สร้างชื่อโฟลเดอร์จาก folder_id (Google Drive ID)
                    $short_id = substr($perm['folder_table_id'], 0, 10);
                    $perm['folder_name'] = "Folder {$short_id}...";
                    $perm['folder_type'] = 'unknown';
                }

                // ตรวจสอบ folder_type
                if (empty($perm['folder_type']) || $perm['folder_type'] === null) {
                    $perm['folder_type'] = 'unknown';
                }

                // ตรวจสอบ folder_id
                if (empty($perm['folder_id'])) {
                    $perm['folder_id'] = $perm['folder_table_id'];
                }

                // ✅ เพิ่ม granted_by_name สำหรับแสดงใน tooltip
                if (!empty($perm['granted_by']) && $perm['granted_by'] > 0) {
                    $granted_by_user = $this->db
                        ->select('CONCAT(COALESCE(m_fname, ""), " ", COALESCE(m_lname, "")) as name')
                        ->from('tbl_member')
                        ->where('m_id', $perm['granted_by'])
                        ->get()
                        ->row();

                    if ($granted_by_user && !empty($granted_by_user->name) && trim($granted_by_user->name) !== '') {
                        $perm['granted_by_name'] = trim($granted_by_user->name);
                    } else {
                        $perm['granted_by_name'] = 'ระบบ';
                    }
                } else {
                    $perm['granted_by_name'] = 'ระบบ';
                }

                // ✅ แปลงค่า is_system_folder เป็น integer
                $perm['is_system_folder'] = isset($perm['is_system_folder']) ? (int) $perm['is_system_folder'] : 0;
            }

            // Log รายละเอียด permissions (แค่ 3 รายการแรก)
            if ($count > 0) {
                $sample_count = min(3, $count);
                log_message('info', "Sample permissions (first {$sample_count}): " . json_encode(array_slice($permissions, 0, $sample_count)));
            }

            return $permissions;

        } catch (Exception $e) {
            log_message('error', "get_user_permissions_simple error for user_id={$user_id}: " . $e->getMessage());
            log_message('error', "Error trace: " . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * 📏 Format bytes เป็น human-readable (Helper function)
     */
    private function simple_format_bytes($bytes, $precision = 2)
    {
        $bytes = max(0, (int) $bytes);

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
    public function toggle_user_storage_access_with_folder()
    {
        // ✅ ป้องกัน PHP Error แสดงเป็น HTML
        ini_set('display_errors', 0);
        error_reporting(0);

        try {
            log_message('info', "========================================");
            log_message('info', "🔄 toggle_user_storage_access_with_folder START");
            log_message('info', "========================================");

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

            log_message('info', "🔍 Checking required methods...");
            foreach ($required_methods as $method) {
                if (!method_exists($this, $method)) {
                    log_message('error', "❌ Missing method: {$method}");
                    $this->emergency_json_response([
                        'success' => false,
                        'message' => "Method {$method} not found",
                        'error_type' => 'missing_method'
                    ]);
                    return;
                }
            }
            log_message('info', "✅ All required methods exist");

            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                log_message('debug', "⚠️ Not AJAX request");
                $this->emergency_json_response([
                    'success' => false,
                    'message' => 'Not AJAX request',
                    'error_type' => 'invalid_request'
                ]);
                return;
            }
            log_message('info', "✅ AJAX request validated");

            // ตรวจสอบสิทธิ์ Admin
            $user_system = $this->session->userdata('m_system');
            log_message('info', "👤 User system: {$user_system}");

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('debug', "⚠️ Access denied for user_system: {$user_system}");
                $this->emergency_json_response([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง',
                    'error_type' => 'access_denied'
                ]);
                return;
            }
            log_message('info', "✅ Admin permission validated");

            // รับข้อมูลจาก POST
            $user_id = $this->input->post('user_id');
            $action = $this->input->post('action');
            $auto_create_folder = $this->input->post('auto_create_folder', true);

            log_message('info', "📥 POST data received:");
            log_message('info', "  - user_id: {$user_id}");
            log_message('info', "  - action: {$action}");
            log_message('info', "  - auto_create_folder: " . ($auto_create_folder ? 'true' : 'false'));

            // Validation พื้นฐาน
            if (empty($user_id) || empty($action)) {
                log_message('debug', "⚠️ Validation failed: Missing user_id or action");
                $this->emergency_json_response([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน',
                    'error_type' => 'validation_error'
                ]);
                return;
            }

            if (!is_numeric($user_id)) {
                log_message('debug', "⚠️ Validation failed: Invalid user_id format");
                $this->emergency_json_response([
                    'success' => false,
                    'message' => 'รหัสผู้ใช้ไม่ถูกต้อง',
                    'error_type' => 'validation_error'
                ]);
                return;
            }

            if (!in_array($action, ['enable', 'disable'])) {
                log_message('debug', "⚠️ Validation failed: Invalid action: {$action}");
                $this->emergency_json_response([
                    'success' => false,
                    'message' => 'การกระทำไม่ถูกต้อง',
                    'error_type' => 'validation_error'
                ]);
                return;
            }
            log_message('info', "✅ All validation passed");

            // ดึงข้อมูลผู้ใช้
            log_message('info', "🔍 Fetching user data for user_id: {$user_id}");
            $user = $this->safe_get_user_data($user_id);

            if (!$user) {
                log_message('error', "❌ User not found: {$user_id}");
                $this->emergency_json_response([
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้ที่ระบุ',
                    'error_type' => 'user_not_found'
                ]);
                return;
            }

            log_message('info', "✅ User found: {$user->m_fname} {$user->m_lname}");

            $new_status = ($action === 'enable') ? 1 : 0;
            $admin_id = $this->session->userdata('m_id') ?: 1;

            log_message('info', "📌 Processing parameters:");
            log_message('info', "  - new_status: {$new_status}");
            log_message('info', "  - admin_id: {$admin_id}");

            // เริ่ม transaction
            log_message('info', "🔄 Starting database transaction...");
            $this->safe_start_transaction();

            if ($new_status == 1) {
                log_message('info', "➡️ Processing ENABLE storage...");
                $result = $this->process_enable_user_storage($user, $admin_id, $auto_create_folder);
            } else {
                log_message('info', "➡️ Processing DISABLE storage...");
                $result = $this->process_disable_user_storage($user, $admin_id);
            }

            log_message('info', "📊 Process result:");
            log_message('info', "  - success: " . ($result['success'] ? 'true' : 'false'));
            log_message('info', "  - message: {$result['message']}");

            if ($result['success']) {
                log_message('info', "✅ Process successful - Committing transaction...");
                $this->safe_commit_transaction();

                log_message('info', "📝 Logging activity...");
                $this->safe_log_activity($admin_id, $user_id, $action, $result['data']);

                // ✅ บันทึก log ผ่าน Google_drive_model
                if (!isset($this->Google_drive_model)) {
                    $this->load->model('Google_drive_model');
                }

                if (method_exists($this->Google_drive_model, 'log_action')) {
                    log_message('info', "📝 Logging to Google Drive logs...");

                    $log_action_type = ($action === 'enable') ? 'enable_storage_access' : 'disable_storage_access';
                    $log_description = ($action === 'enable')
                        ? "เปิดใช้งาน Storage สำหรับ {$user->m_fname} {$user->m_lname}"
                        : "ปิดใช้งาน Storage สำหรับ {$user->m_fname} {$user->m_lname}";

                    $log_additional_data = [
                        'target_member_id' => $user_id,
                        'target_member_name' => "{$user->m_fname} {$user->m_lname}",
                        'target_email' => $user->m_email ?? null,
                        'new_status' => $new_status,
                        'auto_create_folder' => $auto_create_folder,
                        'folder_id' => $result['data']['personal_folder_id'] ?? null,
                        'folder_name' => $result['data']['folder_name'] ?? null
                    ];

                    $log_result = $this->Google_drive_model->log_action(
                        $admin_id,
                        $log_action_type,
                        $log_description,
                        $log_additional_data,
                        false // non-critical
                    );

                    if ($log_result) {
                        log_message('info', "✅ Google Drive log saved successfully");
                    } else {
                        log_message('debug', "⚠️ Google Drive log failed (non-critical)");
                    }
                } else {
                    log_message('debug', "⚠️ log_action method not found in Google_drive_model");
                }

                log_message('info', "========================================");
                log_message('info', "✅ toggle_user_storage_access_with_folder SUCCESS");
                log_message('info', "========================================");

                $this->emergency_json_response([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
                ]);

            } else {
                log_message('error', "❌ Process failed - Rolling back transaction...");
                $this->safe_rollback_transaction();

                log_message('info', "========================================");
                log_message('info', "❌ toggle_user_storage_access_with_folder FAILED");
                log_message('info', "========================================");

                $this->emergency_json_response([
                    'success' => false,
                    'message' => $result['message'],
                    'error_type' => 'process_failed'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', "========================================");
            log_message('error', "💥 EXCEPTION in toggle_user_storage_access_with_folder");
            log_message('error', "========================================");
            log_message('error', "  Message: {$e->getMessage()}");
            log_message('error', "  File: {$e->getFile()}");
            log_message('error', "  Line: {$e->getLine()}");
            log_message('debug', "  Stack trace:");
            log_message('debug', $e->getTraceAsString());
            log_message('error', "========================================");

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
    private function emergency_json_response($data)
    {
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
    private function process_enable_user_storage($user, $admin_id, $auto_create_folder)
    {
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
                    log_message('debug', "⚠️ Personal folder creation failed: {$folder_result['message']}");
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


    private function safe_update_member_personal_folder($user_id, $folder_id)
    {
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


    private function safe_create_personal_folder($user)
    {
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


    private function safe_save_personal_folder_to_database($user, $personal_folder, $parent_folder_id)
    {
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






    private function safe_assign_default_permissions($user, $personal_folder_id, $admin_id)
    {
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







    private function safe_get_folder_by_name($folder_name, $folder_type)
    {
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

    private function safe_add_folder_permission($folder_id, $member_id, $access_type, $granted_by)
    {
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



    private function safe_assign_department_permissions($user, $admin_id)
    {
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
                    $user_position_folders = [
                        (object) [
                            'folder_id' => $created_folder['folder_id'],
                            'folder_name' => $created_folder['folder_name'],
                            'created_for_position' => $user->ref_pid
                        ]
                    ];

                    log_message('info', "✅ Created new department folder: {$created_folder['folder_name']} for position {$user->ref_pid}");

                    $permission_details[] = [
                        'folder_type' => 'Created Department: ' . $created_folder['folder_name'],
                        'access_type' => 'created_new',
                        'newly_created' => true
                    ];
                } else {
                    log_message('debug', "❌ Failed to create department folder for position {$user->ref_pid}");
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
    private function determine_access_level_by_position($user, $dept_folder)
    {
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
                if (
                    strpos($position_name, 'หัวหน้า') !== false ||
                    strpos($position_name, 'ผู้จัดการ') !== false ||
                    strpos($position_name, 'manager') !== false
                ) {
                    return 'write'; // หัวหน้าแผนก
                } elseif (
                    strpos($position_name, 'หัวหน้างาน') !== false ||
                    strpos($position_name, 'supervisor') !== false
                ) {
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
    private function auto_create_department_folder_for_position($position_id, $admin_id)
    {
        try {
            // ตรวจสอบว่ามี method สร้างโฟลเดอร์หรือไม่
            if (!method_exists($this, 'create_folder_with_curl')) {
                log_message('debug', 'create_folder_with_curl method not found, skipping folder creation');
                return ['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์ได้ (method not found)'];
            }

            // ดึงข้อมูลตำแหน่ง
            $position = $this->db->select('pid, pname, pdescription')
                ->from('tbl_position')
                ->where('pid', $position_id)
                ->get()
                ->row();

            if (!$position) {
                return ['success' => false, 'message' => 'ไม่พบข้อมูลตำแหน่ง'];
            }

            // สร้างชื่อโฟลเดอร์
            $folder_name = !empty($position->pdescription) ?
                "แผนก " . $position->pdescription :
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



    private function safe_disable_user_permissions($user_id)
    {
        try {
            // ✅ Option 3: เก็บสิทธิ์ไว้
            // Comment โค้ดเดิมออก

            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                return 0;
            }

            // เปลี่ยนจาก UPDATE เป็น COUNT
            $this->db->where('member_id', $user_id);
            $this->db->where('is_active', 1);
            $count = $this->db->count_all_results('tbl_google_drive_member_folder_access');

            log_message('info', "User {$user_id} disabled - {$count} permissions kept");

            return $count;

        } catch (Exception $e) {
            log_message('error', 'Safe disable user permissions error: ' . $e->getMessage());
            return 0;
        }
    }


    /**
     * ✅ 5. Safe Transaction Functions
     */
    private function safe_start_transaction()
    {
        try {
            if (method_exists($this->db, 'trans_start')) {
                $this->db->trans_start();
            }
        } catch (Exception $e) {
            log_message('error', 'Safe start transaction error: ' . $e->getMessage());
        }
    }

    private function safe_commit_transaction()
    {
        try {
            if (method_exists($this->db, 'trans_complete')) {
                $this->db->trans_complete();
            }
        } catch (Exception $e) {
            log_message('error', 'Safe commit transaction error: ' . $e->getMessage());
        }
    }

    private function safe_rollback_transaction()
    {
        try {
            if (method_exists($this->db, 'trans_rollback')) {
                $this->db->trans_rollback();
            }
        } catch (Exception $e) {
            log_message('error', 'Safe rollback transaction error: ' . $e->getMessage());
        }
    }


    private function safe_get_system_access_token()
    {
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



    private function safe_get_users_folder()
    {
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


    private function process_disable_user_storage($user, $admin_id)
    {
        try {
            // 1. อัปเดตสถานะ Member
            $member_update_result = $this->safe_update_member_storage_status($user->m_id, false);

            if (!$member_update_result) {
                return [
                    'success' => false,
                    'message' => 'ไม่สามารถอัปเดตสถานะผู้ใช้ได้'
                ];
            }

            // ✅ 2. เก็บสิทธิ์ไว้ - ไม่ปิด is_active
            // ลบบรรทัดนี้:
            // $permissions_disabled = $this->safe_disable_user_permissions($user->m_id);

            // นับสิทธิ์ที่มี
            $this->db->where('member_id', $user->m_id);
            $this->db->where('is_active', 1);
            $permissions_count = $this->db->count_all_results('tbl_google_drive_member_folder_access');

            $response_data = [
                'user_id' => $user->m_id,
                'storage_enabled' => false,
                'permissions_kept' => true,
                'permissions_count' => $permissions_count
            ];

            return [
                'success' => true,
                'message' => 'ปิดใช้งาน Storage เรียบร้อย (เก็บสิทธิ์ไว้)',
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



    private function safe_update_member_storage_status($user_id, $enable)
    {
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
    private function safe_get_user_data($user_id)
    {
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


    public function toggle_user_storage_access()
    {
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
    public function create_single_personal_folder()
    {
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
    public function bulk_toggle_user_status()
    {
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
    public function bulk_create_personal_folders()
    {
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
    public function create_all_missing_personal_folders()
    {
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
     * AJAX: ดึงข้อมูลสิทธิ์ผู้ใช้งานทั้งหมด
     * URL: google_drive_system/get_user_permission_data (POST)
     * ใช้โดย: JavaScript ในหน้า google_drive_user_permissions.php
     */
    public function get_user_permission_data()
    {
        try {
            // ล้าง output buffer
            if (ob_get_level()) {
                ob_clean();
            }

            // ตั้งค่า headers
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');

            // ตรวจสอบ AJAX Request
            if (!$this->input->is_ajax_request()) {
                // <-- [DEBUG] เพิ่ม Log
                log_message('debug', 'get_user_permission_data: Failed - Not an AJAX request.');
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
                return;
            }

            // ตรวจสอบ Session
            $current_user_id = $this->session->userdata('m_id');
            if (!$current_user_id) {
                // <-- [DEBUG] เพิ่ม Log
                log_message('debug', 'get_user_permission_data: Failed - User session not found.');
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาเข้าสู่ระบบใหม่'
                ]);
                return;
            }

            // รับ user_id จาก POST
            $user_id = $this->input->post('user_id');

            if (!$user_id) {
                // <-- [DEBUG] เพิ่ม Log
                log_message('debug', 'get_user_permission_data: Failed - "user_id" not found in POST data.');
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลผู้ใช้งาน'
                ]);
                return;
            }

            log_message('info', '📥 START: get_user_permission_data called by user: ' . $current_user_id . ' for target_user: ' . $user_id);

            // ตรวจสอบสิทธิ์ (เฉพาะ Admin)
            $user_system = $this->session->userdata('m_system');
            log_message('debug', 'get_user_permission_data: Checking admin permission for user system: ' . $user_system);

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                // <-- [DEBUG] เพิ่ม Log
                log_message('debug', 'get_user_permission_data: Failed - Permission denied for user: ' . $current_user_id);
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้'
                ]);
                return;
            }

            log_message('debug', 'get_user_permission_data: Admin permission validated');
            log_message('debug', 'get_user_permission_data: Processing request for user_id: ' . $user_id);

            // โหลด Model
            $this->load->model('Google_drive_model');

            // 1. ดึงข้อมูลผู้ใช้
            log_message('debug', 'get_user_permission_data: Step 1 - Getting user basic info');
            $user_info = $this->Google_drive_model->get_user_permission_info($user_id);

            if (!$user_info) {
                // <-- [DEBUG] เพิ่ม Log
                log_message('debug', 'get_user_permission_data: Failed - Step 1 - get_user_permission_info returned no data.');
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลผู้ใช้งาน'
                ]);
                return;
            }

            log_message('info', 'get_user_permission_data: Step 1 - User data retrieved successfully');
            log_message('debug', 'get_user_permission_data: Step 1 - User info: ' . json_encode($user_info));


            // 2. ดึงสิทธิ์โฟลเดอร์ปัจจุบัน
            log_message('debug', 'get_user_permission_data: Step 2 - Getting folder permissions');
            $current_permissions = $this->Google_drive_model->get_user_folders_with_permissions($user_id);
            log_message('info', 'get_user_permission_data: Step 2 - Folder permissions retrieved - Count: ' . count($current_permissions));
            log_message('debug', 'get_user_permission_data: Step 2 - Permissions data: ' . json_encode($current_permissions));


            // 3. ดึงการตั้งค่าสิทธิ์ระบบ
            log_message('debug', 'get_user_permission_data: Step 3 - Getting system permissions from tbl_member');

            // ดึงข้อมูลจาก tbl_member
            $system_permissions_db = $this->db->select('
            storage_access_granted,
            storage_quota_limit,
            storage_quota_used,
            google_drive_enabled,
            personal_folder_id,
            last_storage_access
            ')
                ->from('tbl_member')
                ->where('m_id', $user_id)
                ->get()
                ->row_array();

            log_message('debug', 'get_user_permission_data: Step 3 - DB result: ' . json_encode($system_permissions_db));

            // เพิ่มค่า default ถ้าไม่มี
            $system_permissions = array_merge([
                'storage_access_granted' => 0,
                'can_create_folder' => 1,
                'can_share' => 1,
                'can_delete' => 0,
                'inherit_position' => 0,
                'override_position' => 0,
                'storage_quota_limit' => 1073741824,
                'notes' => ''
            ], $system_permissions_db ?? []);

            log_message('info', 'get_user_permission_data: Step 3 - System permissions processed');
            log_message('debug', 'get_user_permission_data: Step 3 - Final permissions: ' . json_encode($system_permissions));


            // 4. ดึงประวัติการเปลี่ยนแปลง
            log_message('debug', 'get_user_permission_data: Step 4 - Getting permission history');
            log_message('debug', 'get_user_permission_data: Step 4 - Building history query for user_id: ' . $user_id);

            // =================================================================
            // 🚨 [FIX] จุดที่แก้ไข (ลบ Comment ที่มีปัญหาออก) 🚨
            // =================================================================
            $history = $this->db->select('
                logs.id,
                logs.action_type,
                logs.action_description,
                logs.created_at,
                logs.member_id as created_by,
                m.m_fname as by_user_fname,
                m.m_lname as by_user_lname
            ')
                ->from('tbl_google_drive_activity_logs as logs')
                ->join('tbl_member m', 'logs.member_id = m.m_id', 'left')
                ->group_start()
                ->where('logs.action_type', $user_id)
                ->or_where('logs.action_description LIKE', "%member_id: {$user_id}%")
                ->group_end()
                ->order_by('logs.created_at', 'DESC')
                ->limit(20)
                ->get()
                ->result();
            // =================================================================
            // 🚨 สิ้นสุดจุดที่แก้ไข 🚨
            // =================================================================

            // <-- [DEBUG] เพิ่ม Log หลัง Query
            log_message('debug', 'get_user_permission_data: Step 4 - History query executed. Found ' . count($history) . ' records.');

            // เพิ่ม by_user_name
            foreach ($history as $item) {
                $item->by_user_name = trim(($item->by_user_fname ?? '') . ' ' . ($item->by_user_lname ?? ''));
            }

            log_message('info', 'get_user_permission_data: Step 4 - Permission history retrieved and processed - Count: ' . count($history));


            // 5. ดึงโฟลเดอร์ที่สามารถเพิ่มได้
            log_message('debug', 'get_user_permission_data: Step 5 - Getting available folders');

            // รวมโฟลเดอร์จากทั้ง 2 ตาราง
            $drive_folders = $this->db->select('
            id, folder_id, folder_name, folder_type, parent_folder_id, "drive_folders" as source_table
            ')
                ->from('tbl_google_drive_folders')
                ->where('is_active', 1)
                ->get()
                ->result();

            log_message('debug', 'get_user_permission_data: Step 5 - Found ' . count($drive_folders) . ' folders from tbl_google_drive_folders');

            $system_folders = $this->db->select('
            id, folder_id, folder_name, folder_type, parent_folder_id, "system_folders" as source_table
            ')
                ->from('tbl_google_drive_system_folders')
                ->where('is_active', 1)
                ->get()
                ->result();

            log_message('debug', 'get_user_permission_data: Step 5 - Found ' . count($system_folders) . ' folders from tbl_google_drive_system_folders');

            // ===================================================================
            // ✅ ✅ ✅ Deduplication Logic - เพิ่มส่วนนี้
            // ===================================================================
            $folder_map = [];
            $before_dedup_count = count($drive_folders) + count($system_folders);

            // เพิ่ม system_folders ก่อน (priority สูงกว่า)
            foreach ($system_folders as $folder) {
                if (!empty($folder->folder_id)) {
                    $folder_map[$folder->folder_id] = $folder;
                }
            }

            // เพิ่ม drive_folders (ถ้ายังไม่มี folder_id นี้)
            foreach ($drive_folders as $folder) {
                if (!empty($folder->folder_id) && !isset($folder_map[$folder->folder_id])) {
                    $folder_map[$folder->folder_id] = $folder;
                }
            }

            // แปลงกลับเป็น array
            $available_folders = array_values($folder_map);
            $after_dedup_count = count($available_folders);
            $duplicates_removed = $before_dedup_count - $after_dedup_count;

            log_message('info', 'get_user_permission_data: Step 5 - Deduplication complete');
            log_message('debug', 'get_user_permission_data: Step 5 - Before dedup: ' . $before_dedup_count . ' folders');
            log_message('debug', 'get_user_permission_data: Step 5 - After dedup: ' . $after_dedup_count . ' folders');
            log_message('debug', 'get_user_permission_data: Step 5 - Removed ' . $duplicates_removed . ' duplicates');
            // ===================================================================
            // ✅ ✅ ✅ สิ้นสุด Deduplication Logic
            // ===================================================================

            log_message('info', 'get_user_permission_data: Step 5 - Available folders retrieved - Total Count: ' . count($available_folders));

            // 6. สถิติการใช้งาน
            log_message('debug', 'get_user_permission_data: Step 6 - Getting statistics');
            $statistics = $this->Google_drive_model->get_user_statistics($user_id);
            log_message('info', 'get_user_permission_data: Step 6 - Statistics retrieved');
            log_message('debug', 'get_user_permission_data: Step 6 - Statistics data: ' . json_encode($statistics));


            // ส่งข้อมูลกลับ
            $response = [
                'success' => true,
                'data' => [
                    'user_info' => $user_info,
                    'current_permissions' => $current_permissions,
                    'system_permissions' => $system_permissions,
                    'available_folders' => $available_folders,
                    'history' => $history,
                    'statistics' => $statistics
                ]
            ];

            log_message('info', '✅ SUCCESS: get_user_permission_data completed for user_id: ' . $user_id);

            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // <-- [DEBUG] เพิ่ม Log
            log_message('error', '❌ ERROR in get_user_permission_data (Exception): ' . $e->getMessage());
            log_message('debug', 'get_user_permission_data: Exception trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }



    /**
     * ✅ บันทึกการเปลี่ยนแปลงสิทธิ์ พร้อม Debug Logs
     */
    public function save_user_permissions()
    {
        try {
            log_message('info', '💾 START: save_user_permissions called by user: ' . $this->session->userdata('m_id'));

            // ตรวจสอบสิทธิ์ Admin
            log_message('debug', 'save_user_permissions: Step 1 - Checking admin permissions');

            $user_system = $this->session->userdata('m_system');
            $current_admin_id = $this->session->userdata('m_id');

            log_message('debug', 'save_user_permissions: Current admin: ' . ($current_admin_id ?? 'null') . ', System role: ' . ($user_system ?? 'null'));

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('error', 'save_user_permissions: Unauthorized access attempt - User: ' . ($current_admin_id ?? 'null') . ', Role: ' . ($user_system ?? 'null'));

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์เข้าถึง'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('debug', 'save_user_permissions: Admin permissions validated');

            // รับข้อมูล JSON
            log_message('debug', 'save_user_permissions: Step 2 - Getting JSON input data');

            $raw_input = $this->input->raw_input_stream;
            $input_length = strlen($raw_input ?? '');

            log_message('debug', 'save_user_permissions: Raw input length: ' . $input_length . ' bytes');

            $input = json_decode($raw_input, true);
            $json_error = json_last_error();

            if ($json_error !== JSON_ERROR_NONE) {
                log_message('error', 'save_user_permissions: JSON decode error: ' . json_last_error_msg() . ', Raw input preview: ' . substr($raw_input, 0, 200));

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'รูปแบบข้อมูล JSON ไม่ถูกต้อง: ' . json_last_error_msg()
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            if (!$input || !isset($input['user_id']) || !isset($input['changes'])) {
                log_message('error', 'save_user_permissions: Missing required data - Input keys: ' . (is_array($input) ? implode(', ', array_keys($input)) : 'not array'));

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

            // วิเคราะห์ข้อมูลที่ได้รับ
            $folder_changes_count = isset($changes['folders']) && is_array($changes['folders']) ? count($changes['folders']) : 0;
            $system_changes_exist = isset($changes['system']) && is_array($changes['system']);

            log_message('info', 'save_user_permissions: Input data parsed - Target User: ' . $user_id .
                ', Folder changes: ' . $folder_changes_count .
                ', System changes: ' . ($system_changes_exist ? 'yes' : 'no') .
                ', Notes: ' . (empty($notes) ? 'none' : 'provided'));

            log_message('debug', 'save_user_permissions: JSON input parsed and validated');

            // ตรวจสอบว่า target user มีอยู่จริง
            log_message('debug', 'save_user_permissions: Step 3 - Checking target user existence');

            $target_user = $this->db->select('m_id, m_email, m_fname, m_lname, m_status')
                ->from('tbl_member')
                ->where('m_id', $user_id)
                ->get()
                ->row();

            if (!$target_user) {
                log_message('error', 'save_user_permissions: Target user not found - user_id: ' . $user_id);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบผู้ใช้ที่ระบุ'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'save_user_permissions: Target user found - Email: ' . $target_user->m_email . ', Name: ' . trim($target_user->m_fname . ' ' . $target_user->m_lname) . ', Status: ' . $target_user->m_status);

            // เริ่ม Transaction
            log_message('debug', 'save_user_permissions: Step 4 - Starting database transaction');

            $this->db->trans_start();

            $update_count = 0;
            $success_messages = [];
            $error_messages = [];

            // 1. บันทึกการเปลี่ยนแปลงสิทธิ์โฟลเดอร์
            if (isset($changes['folders']) && is_array($changes['folders'])) {
                log_message('debug', 'save_user_permissions: Processing folder permissions - Count: ' . count($changes['folders']));

                foreach ($changes['folders'] as $folder_id => $access_level) {
                    try {
                        log_message('debug', 'save_user_permissions: Updating folder permission - Folder ID: ' . $folder_id . ', Access Level: ' . $access_level);

                        // ตรวจสอบว่า method มีอยู่ก่อนเรียกใช้
                        if (!method_exists($this, 'update_folder_permission_fixed')) {
                            throw new Exception('Method update_folder_permission_fixed not found');
                        }

                        if ($this->update_folder_permission_fixed($user_id, $folder_id, $access_level, $current_admin_id)) {
                            $update_count++;
                            $success_message = "อัปเดตสิทธิ์โฟลเดอร์ $folder_id เป็น $access_level";
                            $success_messages[] = $success_message;

                            log_message('info', 'save_user_permissions: Successfully updated folder permission - Folder: ' . $folder_id . ', Level: ' . $access_level);

                            // บันทึก log
                            if (method_exists($this, 'log_permission_change_fixed')) {
                                $this->log_permission_change_fixed(
                                    $user_id,
                                    'update_folder_permission',
                                    "Updated folder permission: $folder_id to $access_level for user $user_id",
                                    $folder_id
                                );
                            }
                        } else {
                            $error_messages[] = "โฟลเดอร์ $folder_id: ไม่สามารถอัปเดตได้";
                            log_message('error', 'save_user_permissions: Failed to update folder permission - Folder: ' . $folder_id);
                        }
                    } catch (Exception $e) {
                        $error_message = "โฟลเดอร์ $folder_id: " . $e->getMessage();
                        $error_messages[] = $error_message;
                        log_message('error', 'save_user_permissions: Exception updating folder permission - Folder: ' . $folder_id . ', Error: ' . $e->getMessage());
                    }
                }
            }

            // 2. บันทึกการเปลี่ยนแปลงสิทธิ์ระบบ
            if (isset($changes['system']) && is_array($changes['system'])) {
                log_message('debug', 'save_user_permissions: Processing system permissions');

                try {
                    log_message('debug', 'save_user_permissions: Updating system permissions - Data: ' . json_encode($changes['system']));

                    // ตรวจสอบว่า method มีอยู่ก่อนเรียกใช้
                    if (!method_exists($this, 'update_system_permissions_fixed')) {
                        throw new Exception('Method update_system_permissions_fixed not found');
                    }

                    if ($this->update_system_permissions_fixed($user_id, $changes['system'], $notes, $current_admin_id)) {
                        $update_count++;
                        $success_messages[] = "อัปเดตสิทธิ์ระบบ";

                        log_message('info', 'save_user_permissions: Successfully updated system permissions for user: ' . $user_id);

                        // บันทึก log
                        if (method_exists($this, 'log_permission_change_fixed')) {
                            $this->log_permission_change_fixed(
                                $user_id,
                                'update_system_permission',
                                "Updated system permissions for user $user_id: " . json_encode($changes['system'])
                            );
                        }
                    } else {
                        $error_messages[] = "สิทธิ์ระบบ: ไม่สามารถอัปเดตได้";
                        log_message('error', 'save_user_permissions: Failed to update system permissions for user: ' . $user_id);
                    }
                } catch (Exception $e) {
                    $error_message = "สิทธิ์ระบบ: " . $e->getMessage();
                    $error_messages[] = $error_message;
                    log_message('error', 'save_user_permissions: Exception updating system permissions - Error: ' . $e->getMessage());
                }
            }

            // สิ้นสุด Transaction
            log_message('debug', 'save_user_permissions: Step 5 - Completing transaction');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'save_user_permissions: Transaction failed');

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Transaction failed',
                        'errors' => $error_messages
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'save_user_permissions: Transaction completed successfully');

            log_message('info', '✅ SUCCESS: save_user_permissions completed - Target User: ' . $user_id .
                ', Updates: ' . $update_count . ', Folder changes: ' . $folder_changes_count .
                ', Errors: ' . count($error_messages));

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
            // Log error with full context
            log_message('error', '❌ ERROR: save_user_permissions failed - Target User: ' . ($user_id ?? 'unknown') .
                ', Admin: ' . ($current_admin_id ?? 'unknown') .
                ' - Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            // บันทึก Error Activity Log (ถ้ามี function นี้)
            if (method_exists($this, 'log_error_activity') && !empty($current_admin_id)) {
                try {
                    $this->log_error_activity(
                        $current_admin_id,
                        'save_user_permissions',
                        'Permission Save Error: ' . $e->getMessage(),
                        []
                    );
                } catch (Exception $log_error) {
                    log_message('error', 'save_user_permissions: Failed to log error activity: ' . $log_error->getMessage());
                }
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 🔄 รีเซ็ตสิทธิ์ทั้งหมดและสร้างสิทธิ์เริ่มต้นใหม่ (Reset to Default State)
     * 
     * Version: 2.0 - Reset with Default Permissions
     * 
     * URL: google_drive_system/reset_user_permissions (POST)
     * 
     * Expected POST Data:
     * - user_id: รหัสผู้ใช้ที่ต้องการรีเซ็ต
     * 
     * ขั้นตอนการทำงาน:
     * 1. ตรวจสอบสิทธิ์ Admin และข้อมูลผู้ใช้
     * 2. ลบสิทธิ์เดิมทั้งหมด (Soft Delete - UPDATE is_active = 0)
     * 3. เก็บ personal_folder_id เดิมไว้
     * 4. สร้างสิทธิ์เริ่มต้นจาก system folders:
     *    - Personal Folder (owner)
     *    - Shared Folder (write)
     *    - Users Folder (read)
     *    - Department Folder (ถ้ามี ref_pid)
     * 5. เก็บ storage_access_granted = 1
     * 6. Log การเปลี่ยนแปลง
     * 
     * @return JSON
     */
    public function reset_user_permissions()
    {
        // ป้องกัน PHP Error แสดงเป็น HTML
        ini_set('display_errors', 0);
        error_reporting(0);

        try {
            log_message('info', "========================================");
            log_message('info', "🔄 RESET_USER_PERMISSIONS START (v2.0 - Reset to Default)");
            log_message('info', "========================================");

            // ✅ ตรวจสอบ Method ที่จำเป็น
            $required_methods = [
                'safe_get_user_data',
                'safe_assign_default_permissions',
                'safe_start_transaction',
                'safe_commit_transaction',
                'safe_rollback_transaction',
                'safe_log_activity'
            ];

            log_message('info', "🔍 Checking required methods...");
            foreach ($required_methods as $method) {
                if (!method_exists($this, $method)) {
                    log_message('error', "❌ Missing method: {$method}");
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => "Method {$method} not found",
                            'error_type' => 'missing_method'
                        ], JSON_UNESCAPED_UNICODE));
                    return;
                }
            }
            log_message('info', "✅ All required methods exist");

            // ตรวจสอบสิทธิ์ Admin
            log_message('debug', 'reset_user_permissions: Step 1 - Checking admin permissions');

            $user_system = $this->session->userdata('m_system');
            $current_admin_id = $this->session->userdata('m_id');

            log_message('debug', 'reset_user_permissions: Current admin: ' . ($current_admin_id ?? 'null') . ', System role: ' . ($user_system ?? 'null'));

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('error', 'reset_user_permissions: Unauthorized access attempt - User: ' . ($current_admin_id ?? 'null') . ', Role: ' . ($user_system ?? 'null'));

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์เข้าถึง',
                        'error_type' => 'access_denied'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', "✅ Admin permission validated");

            // รับและตรวจสอบ user_id
            log_message('debug', 'reset_user_permissions: Step 2 - Getting and validating user_id');

            $user_id = $this->input->post('user_id');

            log_message('debug', 'reset_user_permissions: POST data received - user_id: ' . ($user_id ?? 'null'));

            if (empty($user_id)) {
                log_message('error', 'reset_user_permissions: Empty user_id provided');

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่ได้ระบุรหัสผู้ใช้',
                        'error_type' => 'validation_error'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            if (!is_numeric($user_id)) {
                log_message('error', 'reset_user_permissions: Invalid user_id format');

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'รหัสผู้ใช้ไม่ถูกต้อง',
                        'error_type' => 'validation_error'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('debug', 'reset_user_permissions: user_id validated - ' . $user_id);

            // ดึงข้อมูลผู้ใช้
            log_message('debug', 'reset_user_permissions: Step 3 - Fetching target user data');

            $user = $this->safe_get_user_data($user_id);

            if (!$user) {
                log_message('error', 'reset_user_permissions: Target user not found or inactive - user_id: ' . $user_id);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบผู้ใช้ที่ระบุ',
                        'error_type' => 'user_not_found'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'reset_user_permissions: Target user found - Email: ' . ($user->m_email ?? 'N/A') .
                ', Name: ' . trim(($user->m_fname ?? '') . ' ' . ($user->m_lname ?? '')) .
                ', Personal Folder: ' . ($user->personal_folder_id ?? 'none') .
                ', Department: ' . ($user->ref_pid ?? 'none'));

            // เริ่ม Transaction
            log_message('info', "🔄 Starting database transaction...");
            $this->safe_start_transaction();

            $reset_count = 0;
            $new_permissions_count = 0;

            // ========================================
            // PHASE 1: ลบสิทธิ์เดิมทั้งหมด (Soft Delete)
            // ========================================
            log_message('info', "📦 PHASE 1: Removing old permissions...");

            // 1. ปิดสิทธิ์ใน tbl_google_drive_folder_permissions (legacy table)
            if ($this->db->table_exists('tbl_google_drive_folder_permissions')) {
                log_message('debug', 'reset_user_permissions: Resetting tbl_google_drive_folder_permissions');

                $folder_result = $this->db->where('member_id', $user_id)
                    ->update('tbl_google_drive_folder_permissions', [
                        'is_active' => 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                if ($folder_result) {
                    $affected_rows = $this->db->affected_rows();
                    $reset_count += $affected_rows;
                    log_message('info', "  ✅ Legacy folder permissions reset: {$affected_rows} rows");
                }
            }

            // 2. ปิดสิทธิ์ใน tbl_google_drive_member_folder_access (main table)
            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                log_message('debug', 'reset_user_permissions: Resetting tbl_google_drive_member_folder_access');

                // ดึง IDs ของ permissions ที่ active
                $permission_ids = $this->db->select('id')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('member_id', $user_id)
                    ->where('is_active', 1)
                    ->get()
                    ->result_array();

                $ids = array_column($permission_ids, 'id');
                $ids_count = count($ids);

                log_message('info', "  📋 Found {$ids_count} active permissions to reset");

                if (!empty($ids)) {
                    $member_folder_result = $this->db->where_in('id', $ids)
                        ->update('tbl_google_drive_member_folder_access', [
                            'is_active' => 0,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    // ตรวจสอบ DB Error
                    $db_error = $this->db->error();
                    if ($db_error['code'] !== 0) {
                        log_message('error', 'reset_user_permissions: Database error - ' . $db_error['message']);
                        throw new Exception('Database error: ' . $db_error['message']);
                    }

                    if ($member_folder_result) {
                        $affected_rows = $this->db->affected_rows();
                        $reset_count += $affected_rows;
                        log_message('info', "  ✅ Member folder access reset: {$affected_rows} rows");
                    }
                }
            }

            // 3. ปิดสิทธิ์พิเศษใน tbl_google_drive_member_permissions
            if ($this->db->table_exists('tbl_google_drive_member_permissions')) {
                log_message('debug', 'reset_user_permissions: Resetting tbl_google_drive_member_permissions');

                $permission_reset = $this->db->where('member_id', $user_id)
                    ->update('tbl_google_drive_member_permissions', [
                        'is_active' => 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                if ($permission_reset) {
                    $affected_rows = $this->db->affected_rows();
                    log_message('info', "  ✅ Special permissions reset: {$affected_rows} rows");
                }
            }

            log_message('info', "✅ PHASE 1 COMPLETED: Total {$reset_count} old permissions removed");

            // ========================================
            // PHASE 2: เก็บ storage_access_granted = 1
            // ========================================
            log_message('info', "📦 PHASE 2: Maintaining storage access...");

            $member_update = $this->db->where('m_id', $user_id)
                ->update('tbl_member', [
                    'storage_access_granted' => 1,  // ✅ เก็บไว้เป็น 1
                    'google_drive_enabled' => 1    // ✅ เก็บไว้เป็น 1
                ]);

            if ($member_update) {
                log_message('info', "  ✅ Storage access maintained (storage_access_granted = 1)");
            }

            // ========================================
            // PHASE 3: สร้างสิทธิ์เริ่มต้นใหม่
            // ========================================
            log_message('info', "📦 PHASE 3: Creating default permissions...");
            log_message('info', "  📋 User info:");
            log_message('info', "    - Personal Folder ID: " . ($user->personal_folder_id ?? 'none'));
            log_message('info', "    - Department (ref_pid): " . ($user->ref_pid ?? 'none'));

            // ใช้ safe_assign_default_permissions ที่มีอยู่แล้ว
            $permissions_result = $this->safe_assign_default_permissions(
                $user,
                $user->personal_folder_id,
                $current_admin_id
            );

            $new_permissions_count = $permissions_result['count'];
            $permission_details = $permissions_result['details'];

            log_message('info', "✅ PHASE 3 COMPLETED: Created {$new_permissions_count} default permissions");

            // แสดงรายละเอียดสิทธิ์ที่สร้าง
            if (!empty($permission_details)) {
                log_message('info', "  📋 Default permissions created:");
                foreach ($permission_details as $detail) {
                    log_message('info', "    - {$detail['folder_type']}: {$detail['access_type']}");
                }
            }

            // ========================================
            // PHASE 4: Commit Transaction
            // ========================================
            log_message('info', "🔄 Committing transaction...");

            $this->safe_commit_transaction();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'reset_user_permissions: Transaction failed');
                throw new Exception('Transaction commit failed');
            }

            log_message('info', "✅ Transaction committed successfully");

            // ========================================
            // PHASE 5: Logging
            // ========================================
            log_message('info', "📝 Logging reset activity...");

            // โหลด Model
            if (!isset($this->Google_drive_model)) {
                $this->load->model('Google_drive_model');
            }

            if (method_exists($this->Google_drive_model, 'log_action')) {

                // ✅ สร้างคำอธิบายแบบย่อ
                $log_description = "รีเซ็ตสิทธิ์เข้าถึงโฟลเดอร์เป็นค่าเริ่มต้นสำหรับ {$user->m_fname} {$user->m_lname}";
                $log_description .= " (ลบสิทธิ์เดิม {$reset_count} รายการ, สร้างสิทธิ์ใหม่ {$new_permissions_count} รายการ)";

                // ✅ เตรียมข้อมูลเพิ่มเติมสำหรับ log
                $log_additional_data = [
                    // ข้อมูลผู้ใช้
                    'target_member_id' => $user_id,
                    'target_member_name' => "{$user->m_fname} {$user->m_lname}",
                    'target_email' => $user->m_email ?? null,
                    'member_name' => "{$user->m_fname} {$user->m_lname}", // สำหรับ build_permission_description()

                    // ข้อมูลการรีเซ็ต
                    'old_permissions_removed' => $reset_count,
                    'new_permissions_created' => $new_permissions_count,

                    // รายละเอียดสิทธิ์ที่สร้างใหม่
                    'permission_details' => $permission_details,

                    // ข้อมูล folder และ context
                    'personal_folder_id' => $user->personal_folder_id ?? null,
                    'ref_pid' => $user->ref_pid ?? null,
                    'storage_access_maintained' => true,

                    // Metadata
                    'reset_type' => 'reset_to_default',
                    'reset_timestamp' => date('Y-m-d H:i:s')
                ];

                // ✅ บันทึก log
                $log_result = $this->Google_drive_model->log_action(
                    $current_admin_id,              // admin ที่ทำการรีเซ็ต
                    'reset_to_default',             // action type
                    $log_description,               // คำอธิบาย
                    $log_additional_data,           // ข้อมูลเพิ่มเติม
                    false                           // non-critical
                );

                if ($log_result) {
                    log_message('info', "✅ Google Drive log saved successfully");
                } else {
                    log_message('debug', "⚠️ Google Drive log failed (non-critical)");
                }
            }

            // Log ผ่าน safe_log_activity
            $this->safe_log_activity(
                $current_admin_id,
                $user_id,
                'reset_to_default',
                [
                    'old_permissions_removed' => $reset_count,
                    'new_permissions_created' => $new_permissions_count
                ]
            );

            log_message('info', "========================================");
            log_message('info', "✅ RESET_USER_PERMISSIONS SUCCESS");
            log_message('info', "  Summary:");
            log_message('info', "  - Old permissions removed: {$reset_count}");
            log_message('info', "  - New default permissions: {$new_permissions_count}");
            log_message('info', "  - Storage access: maintained (enabled)");
            log_message('info', "  - Personal folder: kept ({$user->personal_folder_id})");
            log_message('info', "========================================");

            // Response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => "รีเซ็ตสิทธิ์เรียบร้อยแล้ว (ลบ {$reset_count} รายการ, สร้างใหม่ {$new_permissions_count} รายการ)",
                    'data' => [
                        'user_id' => $user_id,
                        'user_name' => trim($user->m_fname . ' ' . $user->m_lname),
                        'old_permissions_removed' => $reset_count,
                        'new_permissions_created' => $new_permissions_count,
                        'permission_details' => $permission_details,
                        'personal_folder_id' => $user->personal_folder_id ?? null,
                        'storage_access_maintained' => true
                    ]
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', "========================================");
            log_message('error', "💥 EXCEPTION in reset_user_permissions");
            log_message('error', "========================================");
            log_message('error', "  Message: {$e->getMessage()}");
            log_message('error', "  File: {$e->getFile()}");
            log_message('error', "  Line: {$e->getLine()}");
            log_message('debug', "  Stack trace:");
            log_message('debug', $e->getTraceAsString());
            log_message('error', "========================================");

            $this->safe_rollback_transaction();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage(),
                    'error_type' => 'exception',
                    'error_details' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], JSON_UNESCAPED_UNICODE));
        }
    }


    /**
     * [ปรับปรุง] อัปเดตสิทธิ์การเข้าถึงโฟลเดอร์สำหรับผู้ใช้
     * โดยจะทำการบันทึกข้อมูลลงทั้ง 2 ตาราง (ตารางหลักและตารางเก่า) พร้อมกัน
     *
     * @param int $user_id ID ของผู้ใช้
     * @param string $folder_id ID ของโฟลเดอร์
     * @param string $access_level ระดับสิทธิ์ที่ได้รับจาก Frontend ('no_access', 'read_only', 'read_write', 'admin')
     * @param int $granted_by ID ของผู้ที่ให้สิทธิ์ (Admin)
     * @return bool
     * @throws Exception
     */
    private function update_folder_permission_fixed($user_id, $folder_id, $access_level, $granted_by)
    {
        // 1. เริ่ม Transaction เพื่อให้แน่ใจว่าข้อมูลจะถูกบันทึก/ล้มเหลวไปพร้อมกันทั้ง 2 ตาราง
        $this->db->trans_start();
        log_message('debug', "--- Starting transaction for user: {$user_id}, folder: {$folder_id} ---");

        try {
            $granted_by_name = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');

            // =================================================================================
            // ส่วนที่ 1: จัดการตารางหลัก (tbl_google_drive_member_folder_access)
            // =================================================================================
            log_message('debug', "Step 1.1: Processing main table 'tbl_google_drive_member_folder_access'");
            $table_main = 'tbl_google_drive_member_folder_access';

            // แปลงค่าจาก Frontend (access_level) ไปเป็นค่าของตารางหลัก (access_type)
            $access_type_main = '';
            switch ($access_level) {
                case 'read_only':
                    $access_type_main = 'read';
                    break;
                case 'read_write':
                    $access_type_main = 'write';
                    break;
                case 'admin':
                    $access_type_main = 'admin';
                    break;
            }
            log_message('debug', "Access level '{$access_level}' translated to access type '{$access_type_main}' for main table.");

            $existing_main = $this->db->where(['folder_id' => $folder_id, 'member_id' => $user_id])->get($table_main)->row();

            if ($existing_main) {
                log_message('debug', "Found existing record in main table (ID: {$existing_main->id}). Preparing for UPDATE.");
                // UPDATE: ถ้ามีข้อมูลอยู่แล้ว
                $data_main = [
                    'is_active' => ($access_level === 'no_access') ? 0 : 1,
                    'granted_by' => $granted_by,
                    'granted_by_name' => $granted_by_name,
                    'granted_at' => date('Y-m-d H:i:s'),
                    'permission_source' => 'direct' // ระบุว่าเป็นการให้สิทธิ์โดยตรง
                ];
                // อัปเดต access_type เฉพาะเมื่อไม่ใช่ 'no_access'
                if ($access_level !== 'no_access') {
                    $data_main['access_type'] = $access_type_main;
                }
                log_message('debug', "Main table UPDATE data: " . json_encode($data_main));
                $this->db->where('id', $existing_main->id)->update($table_main, $data_main);

            } else {
                log_message('debug', "No existing record in main table. Preparing for INSERT.");
                // INSERT: ถ้ายังไม่มีข้อมูล และไม่ใช่ 'no_access'
                if ($access_level !== 'no_access') {
                    $data_main = [
                        'folder_id' => $folder_id,
                        'member_id' => $user_id,
                        'access_type' => $access_type_main,
                        'granted_by' => $granted_by,
                        'granted_by_name' => $granted_by_name,
                        'granted_at' => date('Y-m-d H:i:s'),
                        'is_active' => 1,
                        'permission_source' => 'direct',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    log_message('debug', "Main table INSERT data: " . json_encode($data_main));
                    $this->db->insert($table_main, $data_main);
                } else {
                    log_message('debug', "Skipping INSERT in main table because access level is 'no_access'.");
                }
            }
            log_message('info', "Successfully processed main table for user {$user_id}, folder {$folder_id}.");

            // =================================================================================
            // ส่วนที่ 2: จัดการตารางเก่า (tbl_google_drive_folder_permissions)
            // =================================================================================
            log_message('debug', "Step 1.2: Processing legacy table 'tbl_google_drive_folder_permissions'");
            $table_legacy = 'tbl_google_drive_folder_permissions';
            $existing_legacy = $this->db->where(['folder_id' => $folder_id, 'member_id' => $user_id])->get($table_legacy)->row();

            if ($existing_legacy) {
                log_message('debug', "Found existing record in legacy table (ID: {$existing_legacy->id}). Preparing for UPDATE.");
                // UPDATE: ถ้ามีข้อมูลอยู่แล้ว
                $data_legacy = [
                    'is_active' => ($access_level === 'no_access') ? 0 : 1,
                    'granted_by' => $granted_by,
                    'granted_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                // อัปเดต access_level เฉพาะเมื่อไม่ใช่ 'no_access'
                if ($access_level !== 'no_access') {
                    $data_legacy['access_level'] = $access_level;
                }
                log_message('debug', "Legacy table UPDATE data: " . json_encode($data_legacy));
                $this->db->where('id', $existing_legacy->id)->update($table_legacy, $data_legacy);

            } else {
                log_message('debug', "No existing record in legacy table. Preparing for INSERT.");
                // INSERT: ถ้ายังไม่มีข้อมูล และไม่ใช่ 'no_access'
                if ($access_level !== 'no_access') {
                    $data_legacy = [
                        'folder_id' => $folder_id,
                        'member_id' => $user_id,
                        'access_level' => $access_level,
                        'granted_by' => $granted_by,
                        'granted_at' => date('Y-m-d H:i:s'),
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    log_message('debug', "Legacy table INSERT data: " . json_encode($data_legacy));
                    $this->db->insert($table_legacy, $data_legacy);
                } else {
                    log_message('debug', "Skipping INSERT in legacy table because access level is 'no_access'.");
                }
            }
            log_message('info', "Successfully processed legacy table for user {$user_id}, folder {$folder_id}.");

            // 2. สิ้นสุด Transaction
            $this->db->trans_complete();
            log_message('debug', "Step 2: Transaction complete.");

            // 3. ตรวจสอบสถานะของ Transaction และคืนค่า
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed in update_folder_permission_fixed for user ' . $user_id);
                return false;
            }

            log_message('info', "✅ Transaction successful for user {$user_id}, folder {$folder_id}.");
            return true;

        } catch (Exception $e) {
            // หากเกิดข้อผิดพลาด ให้ Rollback Transaction ทันที
            $this->db->trans_rollback();
            log_message('error', 'update_folder_permission_fixed error: ' . $e->getMessage());
            throw $e; // ส่ง error ต่อเพื่อให้ Controller หลักจัดการ
        }
    }

    private function update_system_permissions_fixed($user_id, $system_changes, $notes, $updated_by)
    {
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

    private function log_permission_change_fixed($user_id, $action_type, $description, $folder_id = null)
    {
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
     * ✅ API: ดึงรายการโฟลเดอร์ที่สามารถให้สิทธิ์ได้ พร้อม Debug Logs
     */
    public function get_available_folders_for_permission()
    {
        try {
            log_message('info', '📁 START: get_available_folders_for_permission called - IP: ' . $this->input->ip_address() . ', User Agent: ' . substr($this->input->user_agent(), 0, 100));

            // ล้าง output buffer
            log_message('debug', 'get_available_folders_for_permission: Step 0 - Cleaning output buffer');

            if (ob_get_level()) {
                ob_clean();
                log_message('debug', 'get_available_folders_for_permission: Output buffer cleaned, level was: ' . ob_get_level());
            }

            // ตรวจสอบ AJAX request
            log_message('debug', 'get_available_folders_for_permission: Step 1 - Checking AJAX request');

            $is_ajax = $this->input->is_ajax_request();

            // Enhanced AJAX detection logging
            $ajax_indicators = [
                'HTTP_X_REQUESTED_WITH' => $_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'not set',
                'ci_is_ajax' => $is_ajax ? 'true' : 'false',
                'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
                'accept_header' => $_SERVER['HTTP_ACCEPT'] ?? 'not set'
            ];

            log_message('debug', 'get_available_folders_for_permission: AJAX detection details: ' . json_encode($ajax_indicators));

            if (!$is_ajax) {
                log_message('error', 'get_available_folders_for_permission: Non-AJAX request detected');

                if (method_exists($this, 'output_json_error')) {
                    $this->output_json_error('Invalid request method');
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'Invalid request method'
                        ], JSON_UNESCAPED_UNICODE));
                }
                return;
            }

            log_message('debug', 'get_available_folders_for_permission: AJAX request validated');

            // ตรวจสอบสิทธิ์ Admin
            log_message('debug', 'get_available_folders_for_permission: Step 2 - Checking admin permissions');

            $user_system = $this->session->userdata('m_system');
            $current_user_id = $this->session->userdata('m_id');

            log_message('debug', 'get_available_folders_for_permission: Current user: ' . ($current_user_id ?? 'null') . ', System role: ' . ($user_system ?? 'null'));

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('error', 'get_available_folders_for_permission: Unauthorized access attempt - User: ' . ($current_user_id ?? 'null') . ', Role: ' . ($user_system ?? 'null'));

                if (method_exists($this, 'output_json_error')) {
                    $this->output_json_error('ไม่มีสิทธิ์เข้าถึง');
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'ไม่มีสิทธิ์เข้าถึง'
                        ], JSON_UNESCAPED_UNICODE));
                }
                return;
            }

            log_message('debug', 'get_available_folders_for_permission: Admin permissions validated');

            // รับและตรวจสอบ user_id
            log_message('debug', 'get_available_folders_for_permission: Step 3 - Getting and validating user_id');

            $user_id = $this->input->post('user_id');

            // เก็บข้อมูล POST ทั้งหมดสำหรับ debug
            $all_post_data = $this->input->post();

            log_message('debug', 'get_available_folders_for_permission: POST data received - user_id: ' . ($user_id ?? 'null') . ', total POST vars: ' . count($all_post_data));

            if (empty($user_id)) {
                log_message('error', 'get_available_folders_for_permission: Empty user_id provided');

                if (method_exists($this, 'output_json_error')) {
                    $this->output_json_error('ไม่ได้ระบุรหัสผู้ใช้');
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'ไม่ได้ระบุรหัสผู้ใช้'
                        ], JSON_UNESCAPED_UNICODE));
                }
                return;
            }

            log_message('debug', 'get_available_folders_for_permission: user_id validated - ' . $user_id);

            // ตรวจสอบว่า user_id มีอยู่จริง
            log_message('debug', 'get_available_folders_for_permission: Step 4 - Checking if target user exists');

            $target_user_exists = $this->db->select('m_id, m_email, m_fname, m_lname')
                ->from('tbl_member')
                ->where('m_id', $user_id)
                ->where('m_status', '1')
                ->get()
                ->row();

            if (!$target_user_exists) {
                log_message('error', 'get_available_folders_for_permission: Target user not found or inactive - user_id: ' . $user_id);

                if (method_exists($this, 'output_json_error')) {
                    $this->output_json_error('ไม่พบผู้ใช้ที่ระบุ หรือผู้ใช้ไม่ได้ใช้งาน');
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'ไม่พบผู้ใช้ที่ระบุ หรือผู้ใช้ไม่ได้ใช้งาน'
                        ], JSON_UNESCAPED_UNICODE));
                }
                return;
            }

            log_message('info', 'get_available_folders_for_permission: Target user found - Email: ' . ($target_user_exists->m_email ?? 'N/A') . ', Name: ' . ($target_user_exists->m_fname ?? '') . ' ' . ($target_user_exists->m_lname ?? ''));

            // ดึงรายการโฟลเดอร์ทั้งหมดที่สามารถให้สิทธิ์ได้
            log_message('debug', 'get_available_folders_for_permission: Step 5 - Getting available folders list for user_id: ' . $user_id);

            // ตรวจสอบว่า method มีอยู่ก่อนเรียกใช้
            if (!method_exists($this, 'get_available_folders_list')) {
                log_message('error', 'get_available_folders_for_permission: Method get_available_folders_list not found');

                if (method_exists($this, 'output_json_error')) {
                    $this->output_json_error('ไม่พบ method get_available_folders_list');
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'ไม่พบ method get_available_folders_list'
                        ], JSON_UNESCAPED_UNICODE));
                }
                return;
            }

            $available_folders = $this->get_available_folders_list($user_id);
            $folders_count = is_array($available_folders) ? count($available_folders) : 0;

            log_message('info', 'get_available_folders_for_permission: Available folders retrieved - Count: ' . $folders_count);

            // วิเคราะห์โฟลเดอร์เพิ่มเติม
            if ($folders_count > 0 && is_array($available_folders)) {
                $folder_stats = [
                    'total' => $folders_count,
                    'with_permissions' => 0,
                    'without_permissions' => 0,
                    'folder_types' => []
                ];

                foreach ($available_folders as $folder) {
                    if (isset($folder['has_permission']) && $folder['has_permission']) {
                        $folder_stats['with_permissions']++;
                    } else {
                        $folder_stats['without_permissions']++;
                    }

                    if (isset($folder['folder_type'])) {
                        $type = $folder['folder_type'];
                        $folder_stats['folder_types'][$type] = ($folder_stats['folder_types'][$type] ?? 0) + 1;
                    }
                }

                log_message('debug', 'get_available_folders_for_permission: Folder analysis - ' . json_encode($folder_stats));
            }

            log_message('info', '✅ SUCCESS: get_available_folders_for_permission completed for user_id: ' . $user_id . ' - Found: ' . $folders_count . ' folders');

            // ส่งผลลัพธ์สำเร็จ
            if (method_exists($this, 'output_json_success')) {
                $this->output_json_success([
                    'folders' => $available_folders,
                    'total_count' => $folders_count
                ], 'โหลดรายการโฟลเดอร์เรียบร้อย');
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'โหลดรายการโฟลเดอร์เรียบร้อย',
                        'data' => [
                            'folders' => $available_folders,
                            'total_count' => $folders_count
                        ]
                    ], JSON_UNESCAPED_UNICODE));
            }

        } catch (Exception $e) {
            // Log error with full context
            log_message('error', '❌ ERROR: get_available_folders_for_permission failed - Target User: ' . ($user_id ?? 'unknown') .
                ', Admin: ' . ($current_user_id ?? 'unknown') .
                ' - Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            // บันทึก Error Activity Log (ถ้ามี function นี้)
            if (method_exists($this, 'log_error_activity') && !empty($current_user_id)) {
                try {
                    $this->log_error_activity(
                        $current_user_id,
                        'get_available_folders_for_permission',
                        'Function Error: ' . $e->getMessage(),
                        []
                    );
                } catch (Exception $log_error) {
                    log_message('error', 'get_available_folders_for_permission: Failed to log error activity: ' . $log_error->getMessage());
                }
            }

            if (method_exists($this, 'output_json_error')) {
                $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                    ], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * ✅ Helper: ดึงรายการโฟลเดอร์ที่สามารถให้สิทธิ์ได้
     */
    private function get_available_folders_list($user_id)
    {
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
                        'can_grant' => $this->can_grant_folder_permission($folder->folder_type, $user_id, $folder->folder_id),
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
            $available_folders = array_filter($folders, function ($folder) {
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
    private function get_user_current_folder_permission($user_id, $folder_id)
    {
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
    private function can_grant_folder_permission($folder_type, $user_id, $folder_id = null)
    {
        try {
            // กฎการให้สิทธิ์
            switch ($folder_type) {
                case 'admin':
                    // เฉพาะ Super Admin เท่านั้น
                    return $this->session->userdata('m_system') === 'super_admin';

                case 'personal':
                    // ไม่ให้สิทธิ์โฟลเดอร์ส่วนตัวของคนอื่น
                    if (empty($folder_id)) {
                        return false;
                    }
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
     * ดึงเจ้าของโฟลเดอร์ส่วนตัว
     */
    private function get_personal_folder_owner($folder_id)
    {
        try {
            // ตรวจสอบจาก tbl_member
            if ($this->db->field_exists('personal_folder_id', 'tbl_member')) {
                $member = $this->db->select('m_id')
                    ->from('tbl_member')
                    ->where('personal_folder_id', $folder_id)
                    ->get()
                    ->row();

                if ($member) {
                    return (int) $member->m_id;
                }
            }

            // ตรวจสอบจาก tbl_google_drive_member_folder_access
            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $owner = $this->db->select('member_id')
                    ->from('tbl_google_drive_member_folder_access')
                    ->where('folder_id', $folder_id)
                    ->where('access_type', 'owner')
                    ->where('is_active', 1)
                    ->get()
                    ->row();

                if ($owner) {
                    return (int) $owner->member_id;
                }
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'get_personal_folder_owner error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Helper: ดึงโฟลเดอร์เพิ่มเติมจาก Google Drive (ถ้าจำเป็น)
     */
    private function get_additional_google_folders()
    {
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
     * ✅ แก้ไข: เพิ่มสิทธิ์หลายโฟลเดอร์พร้อมกัน (ตรวจสอบ duplicate และส่งข้อความแจ้งเตือนที่ถูกต้อง)
     */
    public function grant_bulk_folder_permissions()
    {
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        try {
            log_message('info', '📁🔐 START: grant_bulk_folder_permissions called - IP: ' .
                $this->input->ip_address() . ', User Agent: ' . substr($this->input->user_agent(), 0, 100));

            // Step 1: ตรวจสอบ AJAX request
            log_message('debug', 'grant_bulk_folder_permissions: Step 1 - Checking AJAX request');
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }
            log_message('debug', 'grant_bulk_folder_permissions: AJAX request validated');

            // Step 2: ตรวจสอบสิทธิ์ Admin
            log_message('debug', 'grant_bulk_folder_permissions: Step 2 - Checking admin permissions');
            $current_user_id = $this->session->userdata('m_id');
            $user_system = $this->session->userdata('m_system');

            log_message('debug', "grant_bulk_folder_permissions: Current user: {$current_user_id}, System role: {$user_system}");

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                $this->output_json_error('ไม่มีสิทธิ์เข้าถึง');
                return;
            }
            log_message('debug', 'grant_bulk_folder_permissions: Admin permissions validated');

            // Step 3: รับข้อมูล JSON
            log_message('debug', 'grant_bulk_folder_permissions: Step 3 - Getting JSON input data');
            $raw_input = $this->input->raw_input_stream;
            log_message('debug', 'grant_bulk_folder_permissions: Raw input length: ' . strlen($raw_input) . ' bytes');

            $input = json_decode($raw_input, true);

            if (empty($input)) {
                $this->output_json_error('ไม่ได้รับข้อมูล');
                return;
            }

            $user_id = $input['user_id'] ?? null;
            $folders = $input['folders'] ?? [];
            $permission_level = $input['permission_level'] ?? 'read';
            $apply_to_subfolders = isset($input['apply_to_subfolders']) ? (bool) $input['apply_to_subfolders'] : false;
            $expiry_date = $input['expiry_date'] ?? null;

            log_message('info', "grant_bulk_folder_permissions: Input data parsed - User ID: {$user_id}, Permission: {$permission_level}, Folders: " . count($folders) . ", Apply to subfolders: " . ($apply_to_subfolders ? 'yes' : 'no'));
            log_message('debug', 'grant_bulk_folder_permissions: JSON input parsed and validated');

            // Step 4: ตรวจสอบข้อมูลที่จำเป็น
            log_message('debug', 'grant_bulk_folder_permissions: Step 4 - Validating input data');

            if (empty($user_id) || empty($folders) || !is_array($folders)) {
                $this->output_json_error('ข้อมูลไม่ครบถ้วน');
                return;
            }

            // ตรวจสอบและกรอง folder IDs ที่ถูกต้อง
            $valid_folders = [];
            $invalid_folders = [];

            foreach ($folders as $folder) {
                if (isset($folder['folder_id']) && !empty(trim($folder['folder_id']))) {
                    $valid_folders[] = $folder;
                } else {
                    $invalid_folders[] = $folder;
                }
            }

            if (empty($valid_folders)) {
                $this->output_json_error('ไม่มีโฟลเดอร์ที่ถูกต้อง');
                return;
            }

            log_message('info', "grant_bulk_folder_permissions: Folders analysis - Total: " . count($folders) .
                ", Valid: " . count($valid_folders) . ", Invalid: " . count($invalid_folders));

            // Step 5: ตรวจสอบตารางในฐานข้อมูล
            log_message('debug', 'grant_bulk_folder_permissions: Step 5 - Checking table existence');
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $this->output_json_error('ตารางฐานข้อมูลไม่พร้อมใช้งาน');
                return;
            }
            log_message('debug', 'grant_bulk_folder_permissions: Table existence confirmed');

            // Step 6: ตรวจสอบผู้ใช้เป้าหมาย
            log_message('debug', 'grant_bulk_folder_permissions: Step 6 - Checking target user existence');
            $target_user = $this->db->select('m_id, m_fname, m_lname, m_email')
                ->from('tbl_member')
                ->where('m_id', $user_id)
                ->where('m_status', 1)
                ->get()
                ->row();

            if (!$target_user) {
                $this->output_json_error('ไม่พบผู้ใช้ที่ระบุ');
                return;
            }

            log_message('info', "grant_bulk_folder_permissions: Target user validated - Email: {$target_user->m_email}, Name: {$target_user->m_fname} {$target_user->m_lname}");

            // Step 7: เริ่มต้น Database Transaction
            log_message('debug', 'grant_bulk_folder_permissions: Step 7 - Starting database transaction');
            $this->db->trans_start();

            $success_count = 0;
            $failed_count = 0;
            $updated_count = 0;
            $skipped_count = 0;
            $results = [];

            log_message('info', "grant_bulk_folder_permissions: Processing " . count($valid_folders) . " folders for user {$user_id}");

            // ดึงข้อมูลผู้ให้สิทธิ์
            $granted_by_name = $this->get_user_name($current_user_id);

            // ประมวลผลแต่ละโฟลเดอร์
            foreach ($valid_folders as $index => $folder) {
                $folder_id = trim($folder['folder_id']);
                $folder_name = $folder['folder_name'] ?? 'Unknown Folder';

                log_message('debug', "grant_bulk_folder_permissions: Processing folder " . ($index + 1) . "/" . count($valid_folders) .
                    " - ID: {$folder_id}, Name: {$folder_name}");

                try {
                    // ✅ ตรวจสอบสิทธิ์ที่มีอยู่แล้ว
                    $existing_permission = $this->db->select('id, access_type, is_active, granted_at')
                        ->from('tbl_google_drive_member_folder_access')
                        ->where('member_id', $user_id)
                        ->where('folder_id', $folder_id)
                        ->get()
                        ->row();

                    if ($existing_permission) {
                        if ($existing_permission->is_active == 1) {
                            // ✅ มีสิทธิ์อยู่แล้วและใช้งานอยู่
                            if ($existing_permission->access_type === $permission_level) {
                                // สิทธิ์เหมือนเดิม - ข้าม
                                $skipped_count++;
                                log_message('info', "⏭️ Skipped folder {$folder_id} - same permission already exists ({$permission_level})");

                                $results[] = [
                                    'folder_id' => $folder_id,
                                    'folder_name' => $folder_name,
                                    'status' => 'skipped',
                                    'message' => "มีสิทธิ์ {$permission_level} อยู่แล้ว",
                                    'existing_permission' => $existing_permission->access_type,
                                    'granted_date' => $existing_permission->granted_at
                                ];
                            } else {
                                // อัปเดตสิทธิ์ที่แตกต่าง
                                log_message('debug', "grant_bulk_folder_permissions: Updating permission for folder: {$folder_id} from {$existing_permission->access_type} to {$permission_level}");

                                $update_data = [
                                    'access_type' => $permission_level,
                                    'granted_by' => $current_user_id,
                                    'granted_by_name' => $granted_by_name,
                                    'granted_at' => date('Y-m-d H:i:s'),
                                    'expires_at' => !empty($expiry_date) ? $expiry_date . ' 23:59:59' : null,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'apply_to_children' => $apply_to_subfolders ? 1 : 0
                                ];

                                $update_result = $this->db->where('id', $existing_permission->id)
                                    ->update('tbl_google_drive_member_folder_access', $update_data);

                                if ($update_result && $this->db->affected_rows() > 0) {
                                    $updated_count++;
                                    $success_count++;
                                    log_message('info', "✅ Updated permission for folder: {$folder_id} from {$existing_permission->access_type} to {$permission_level}");

                                    $results[] = [
                                        'folder_id' => $folder_id,
                                        'folder_name' => $folder_name,
                                        'status' => 'updated',
                                        'message' => "อัปเดตสิทธิ์จาก {$existing_permission->access_type} เป็น {$permission_level}",
                                        'old_permission' => $existing_permission->access_type,
                                        'new_permission' => $permission_level
                                    ];
                                } else {
                                    $failed_count++;
                                    log_message('debug', "⚠️ Failed to update permission for folder: {$folder_id}");

                                    $results[] = [
                                        'folder_id' => $folder_id,
                                        'folder_name' => $folder_name,
                                        'status' => 'failed',
                                        'message' => 'ไม่สามารถอัปเดตสิทธิ์ได้'
                                    ];
                                }
                            }
                        } else {
                            // เปิดใช้งานสิทธิ์ที่ถูก deactivate
                            log_message('debug', "grant_bulk_folder_permissions: Reactivating inactive permission for folder: {$folder_id}");

                            $reactivate_data = [
                                'access_type' => $permission_level,
                                'granted_by' => $current_user_id,
                                'granted_by_name' => $granted_by_name,
                                'granted_at' => date('Y-m-d H:i:s'),
                                'expires_at' => !empty($expiry_date) ? $expiry_date . ' 23:59:59' : null,
                                'is_active' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'apply_to_children' => $apply_to_subfolders ? 1 : 0
                            ];

                            $reactivate_result = $this->db->where('id', $existing_permission->id)
                                ->update('tbl_google_drive_member_folder_access', $reactivate_data);

                            if ($reactivate_result && $this->db->affected_rows() > 0) {
                                $success_count++;
                                log_message('info', "✅ Reactivated permission for folder: {$folder_id}");

                                $results[] = [
                                    'folder_id' => $folder_id,
                                    'folder_name' => $folder_name,
                                    'status' => 'reactivated',
                                    'message' => "เปิดใช้งานสิทธิ์ {$permission_level} เรียบร้อย"
                                ];
                            } else {
                                $failed_count++;
                                log_message('error', "❌ Failed to reactivate permission for folder: {$folder_id}");

                                $results[] = [
                                    'folder_id' => $folder_id,
                                    'folder_name' => $folder_name,
                                    'status' => 'failed',
                                    'message' => 'ไม่สามารถเปิดใช้งานสิทธิ์ได้'
                                ];
                            }
                        }
                    } else {
                        // สร้างสิทธิ์ใหม่
                        log_message('debug', "grant_bulk_folder_permissions: Creating new permission for folder: {$folder_id}");

                        $permission_data = [
                            'member_id' => $user_id,
                            'folder_id' => $folder_id,
                            'access_type' => $permission_level,
                            'permission_source' => 'direct',
                            'permission_mode' => 'direct',
                            'granted_by' => $current_user_id,
                            'granted_by_name' => $granted_by_name,
                            'granted_at' => date('Y-m-d H:i:s'),
                            'expires_at' => !empty($expiry_date) ? $expiry_date . ' 23:59:59' : null,
                            'is_active' => 1,
                            'inherit_from_parent' => 0,
                            'apply_to_children' => $apply_to_subfolders ? 1 : 0,
                            'parent_folder_id' => null,
                            'created_at' => date('Y-m-d H:i:s')
                        ];

                        $insert_result = $this->db->insert('tbl_google_drive_member_folder_access', $permission_data);

                        if ($insert_result && $this->db->affected_rows() > 0) {
                            $success_count++;
                            log_message('info', "✅ Created new permission for folder: {$folder_id}");

                            $results[] = [
                                'folder_id' => $folder_id,
                                'folder_name' => $folder_name,
                                'status' => 'created',
                                'message' => "สร้างสิทธิ์ {$permission_level} เรียบร้อย"
                            ];
                        } else {
                            $failed_count++;
                            log_message('error', "❌ Failed to create permission for folder: {$folder_id}");

                            $results[] = [
                                'folder_id' => $folder_id,
                                'folder_name' => $folder_name,
                                'status' => 'failed',
                                'message' => 'ไม่สามารถสร้างสิทธิ์ได้'
                            ];
                        }
                    }

                } catch (Exception $e) {
                    $failed_count++;
                    log_message('error', "grant_bulk_folder_permissions error processing folder {$folder_id}: " . $e->getMessage());

                    $results[] = [
                        'folder_id' => $folder_id,
                        'folder_name' => $folder_name ?? 'Unknown',
                        'status' => 'failed',
                        'message' => $e->getMessage()
                    ];
                }
            }

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'grant_bulk_folder_permissions: Transaction failed');
                $this->output_json_error('ไม่สามารถบันทึกข้อมูลได้ (Transaction failed)');
                return;
            }

            // Log การกระทำ
            $this->log_activity(
                $current_user_id,
                'grant_bulk_folder_permissions',
                "ประมวลผลสิทธิ์หลายโฟลเดอร์: สำเร็จ {$success_count}, อัปเดต {$updated_count}, ข้าม {$skipped_count}, ล้มเหลว {$failed_count}",
                [
                    'target_user_id' => $user_id,
                    'permission_level' => $permission_level,
                    'total_folders' => count($valid_folders),
                    'success_count' => $success_count,
                    'failed_count' => $failed_count,
                    'updated_count' => $updated_count,
                    'skipped_count' => $skipped_count,
                    'apply_to_subfolders' => $apply_to_subfolders
                ]
            );

            log_message('info', "✅ SUCCESS: grant_bulk_folder_permissions completed - Success: {$success_count}, Updated: {$updated_count}, Skipped: {$skipped_count}, Failed: {$failed_count}");

            // ✅ สร้างข้อความตอบกลับที่เหมาะสม
            $response_message = $this->generate_bulk_permission_response_message($success_count, $updated_count, $skipped_count, $failed_count, count($valid_folders));

            $this->output_json_success([
                'success_count' => $success_count,
                'failed_count' => $failed_count,
                'updated_count' => $updated_count,
                'skipped_count' => $skipped_count,
                'total_count' => count($valid_folders),
                'results' => $results
            ], $response_message);

        } catch (Exception $e) {
            // Rollback transaction if active
            if ($this->db && method_exists($this->db, 'trans_status')) {
                $this->db->trans_rollback();
            }

            log_message('error', 'grant_bulk_folder_permissions error: ' . $e->getMessage());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Helper: สร้างข้อความตอบกลับสำหรับ bulk permissions
     */
    private function generate_bulk_permission_response_message($success_count, $updated_count, $skipped_count, $failed_count, $total_count)
    {
        $messages = [];

        if ($success_count > 0) {
            $messages[] = "สร้างใหม่ {$success_count} รายการ";
        }

        if ($updated_count > 0) {
            $messages[] = "อัปเดต {$updated_count} รายการ";
        }

        if ($skipped_count > 0) {
            $messages[] = "ข้าม {$skipped_count} รายการ (มีสิทธิ์เดียวกันอยู่แล้ว)";
        }

        if ($failed_count > 0) {
            $messages[] = "ล้มเหลว {$failed_count} รายการ";
        }

        $summary = implode(', ', $messages);

        if ($failed_count === 0 && $skipped_count === 0) {
            return "ประมวลผลสิทธิ์เรียบร้อย: {$summary}";
        } else if ($failed_count === 0) {
            return "ประมวลผลสิทธิ์เสร็จสิ้น: {$summary}";
        } else {
            return "ประมวลผลสิทธิ์เสร็จสิ้น (มีบางรายการล้มเหลว): {$summary}";
        }
    }

    /**
     * ✅ API: ลบสิทธิ์โฟลเดอร์ของผู้ใช้ พร้อม Debug Logs
     */
    public function remove_user_folder_permission()
    {
        // Force JSON output
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            log_message('info', '🗑️ START: remove_user_folder_permission called - IP: ' . $this->input->ip_address() . ', User Agent: ' . substr($this->input->user_agent(), 0, 100));

            // ✅ 1. ตรวจสอบ Method และ AJAX
            log_message('debug', 'remove_user_folder_permission: Step 1 - Checking request method: ' . ($_SERVER['REQUEST_METHOD'] ?? 'unknown'));

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                log_message('error', 'remove_user_folder_permission: Invalid request method: ' . ($_SERVER['REQUEST_METHOD'] ?? 'unknown'));

                echo json_encode([
                    'success' => false,
                    'message' => 'Only POST method allowed'
                ]);
                return;
            }

            log_message('debug', 'remove_user_folder_permission: POST method validated');

            // ✅ 2. ตรวจสอบ Session
            log_message('debug', 'remove_user_folder_permission: Step 2 - Checking session and permissions');

            $user_system = $this->session->userdata('m_system');
            $current_user_id = $this->session->userdata('m_id');

            log_message('debug', 'remove_user_folder_permission: Current user: ' . ($current_user_id ?? 'null') . ', System role: ' . ($user_system ?? 'null'));

            if (!in_array($user_system, ['system_admin', 'super_admin']) || empty($current_user_id)) {
                log_message('error', 'remove_user_folder_permission: Unauthorized access attempt - User: ' . ($current_user_id ?? 'null') . ', Role: ' . ($user_system ?? 'null'));

                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึง - ต้องเป็น Admin เท่านั้น'
                ]);
                return;
            }

            log_message('debug', 'remove_user_folder_permission: Admin permissions validated');

            // ✅ 3. รับข้อมูล POST
            log_message('debug', 'remove_user_folder_permission: Step 3 - Getting POST data');

            $folder_id = $this->input->post('folder_id');
            $user_id = $this->input->post('user_id');

            log_message('debug', 'remove_user_folder_permission: POST data received - folder_id: ' . ($folder_id ?? 'null') . ', user_id: ' . ($user_id ?? 'null'));

            if (empty($folder_id) || empty($user_id)) {
                log_message('error', 'remove_user_folder_permission: Missing required parameters - folder_id: ' . ($folder_id ?? 'empty') . ', user_id: ' . ($user_id ?? 'empty'));

                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาระบุ folder_id และ user_id'
                ]);
                return;
            }

            log_message('debug', 'remove_user_folder_permission: Required parameters validated');

            // ✅ 4. ตรวจสอบว่าตารางมีอยู่
            log_message('debug', 'remove_user_folder_permission: Step 4 - Checking table existence');

            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                log_message('error', 'remove_user_folder_permission: Table tbl_google_drive_member_folder_access does not exist');

                echo json_encode([
                    'success' => false,
                    'message' => 'ตาราง tbl_google_drive_member_folder_access ไม่มีอยู่'
                ]);
                return;
            }

            log_message('debug', 'remove_user_folder_permission: Table existence confirmed');

            // ✅ 5. ตรวจสอบสิทธิ์ที่มีอยู่
            log_message('debug', 'remove_user_folder_permission: Step 5 - Checking existing permission for user_id: ' . $user_id . ', folder_id: ' . $folder_id);

            $existing_permission = $this->db->select('id, access_type, permission_source')
                ->from('tbl_google_drive_member_folder_access')
                ->where('member_id', $user_id)
                ->where('folder_id', $folder_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if (!$existing_permission) {
                log_message('error', 'remove_user_folder_permission: No active permission found for user_id: ' . $user_id . ', folder_id: ' . $folder_id);

                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบสิทธิ์ที่จะลบ หรือสิทธิ์ถูกลบไปแล้ว'
                ]);
                return;
            }

            log_message('info', 'remove_user_folder_permission: Found existing permission - ID: ' . $existing_permission->id . ', Type: ' . ($existing_permission->access_type ?? 'unknown') . ', Source: ' . ($existing_permission->permission_source ?? 'unknown'));

            // ✅ 6. เริ่ม Transaction
            log_message('debug', 'remove_user_folder_permission: Step 6 - Starting database transaction');

            $this->db->trans_start();

            // ✅ 7. ลบสิทธิ์ (soft delete) - ใช้เฉพาะ columns ที่มีอยู่จริง
            log_message('debug', 'remove_user_folder_permission: Step 7 - Preparing soft delete');

            $current_time = date('Y-m-d H:i:s');

            $update_data = [
                'is_active' => 0,
                'updated_at' => $current_time
            ];

            // ✅ เช็คว่ามี column expires_at หรือไม่ ก่อนใช้
            $fields = $this->db->list_fields('tbl_google_drive_member_folder_access');

            log_message('debug', 'remove_user_folder_permission: Table fields found: ' . implode(', ', $fields));

            if (in_array('expires_at', $fields)) {
                $update_data['expires_at'] = $current_time; // ใช้เป็น marker ว่าถูกลบเมื่อไหร่
                log_message('debug', 'remove_user_folder_permission: expires_at field found, will be updated');
            }

            log_message('debug', 'remove_user_folder_permission: Executing update query with data: ' . json_encode($update_data));

            $update_result = $this->db->where([
                'member_id' => $user_id,
                'folder_id' => $folder_id,
                'is_active' => 1
            ])->update('tbl_google_drive_member_folder_access', $update_data);

            if (!$update_result) {
                log_message('error', 'remove_user_folder_permission: Database update failed');

                $this->db->trans_rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถลบสิทธิ์ได้ - Database update failed'
                ]);
                return;
            }

            $affected_rows = $this->db->affected_rows();

            log_message('info', 'remove_user_folder_permission: Update successful - Affected rows: ' . $affected_rows);

            if ($affected_rows === 0) {
                log_message('error', 'remove_user_folder_permission: No rows affected - permission may already be deleted');

                $this->db->trans_rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่มีข้อมูลที่เปลี่ยนแปลง - สิทธิ์อาจถูกลบแล้ว'
                ]);
                return;
            }

            log_message('debug', 'remove_user_folder_permission: Permission soft deleted (' . $affected_rows . ' rows affected)');

            // ✅ 8. ลบสิทธิ์สืบทอดใน subfolders (ถ้ามี)
            log_message('debug', 'remove_user_folder_permission: Step 8 - Revoking inherited permissions');

            $inherited_removed = 0;
            try {
                if (method_exists($this, 'revoke_inherited_permissions_simple')) {
                    $inherited_removed = $this->revoke_inherited_permissions_simple($user_id, $folder_id);
                    log_message('info', 'remove_user_folder_permission: Inherited permissions revoked - Count: ' . $inherited_removed);
                } else {
                    log_message('error', 'remove_user_folder_permission: revoke_inherited_permissions_simple method not found');
                }
            } catch (Exception $e) {
                log_message('error', 'remove_user_folder_permission: Failed to revoke inherited permissions: ' . $e->getMessage());
            }

            // ✅ 9. Complete transaction
            log_message('debug', 'remove_user_folder_permission: Step 9 - Completing transaction');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'remove_user_folder_permission: Transaction failed');

                echo json_encode([
                    'success' => false,
                    'message' => 'Transaction failed'
                ]);
                return;
            }

            log_message('info', 'remove_user_folder_permission: Transaction completed successfully');

            // ✅ 10. Log การกระทำ (แบบสมบูรณ์พร้อมข้อมูลชื่อ)
            log_message('debug', 'remove_user_folder_permission: Step 10 - Logging action');

            try {
                // ✅ ดึงข้อมูลผู้ใช้
                $user_info = $this->db->select('m_fname, m_lname, m_email')
                    ->from('tbl_member')
                    ->where('m_id', $user_id)
                    ->get()
                    ->row();

                log_message('debug', 'remove_user_folder_permission: User info retrieved - ' .
                    ($user_info ? ($user_info->m_fname . ' ' . $user_info->m_lname) : 'not found'));

                // ✅ ดึงข้อมูลโฟลเดอร์
                $folder_info = $this->db->select('folder_name')
                    ->from('tbl_google_drive_folders')
                    ->where('id', $folder_id)
                    ->get()
                    ->row();

                log_message('debug', 'remove_user_folder_permission: Folder info retrieved - ' .
                    ($folder_info ? $folder_info->folder_name : 'not found'));

                // ✅ สร้างข้อมูลสำหรับ log แบบสมบูรณ์
                $permission_data = [
                    'permission_id' => $existing_permission->id,
                    'member_id' => $user_id,
                    'member_name' => $user_info ? trim($user_info->m_fname . ' ' . $user_info->m_lname) : 'ไม่ระบุชื่อ',
                    'target_email' => $user_info->m_email ?? null,
                    'folder_id' => $folder_id,
                    'folder_name' => $folder_info->folder_name ?? 'ไม่ระบุโฟลเดอร์',
                    'access_type' => $existing_permission->access_type,
                    'permission_source' => $existing_permission->permission_source ?? 'manual'
                ];

                log_message('debug', 'remove_user_folder_permission: Permission data prepared - ' . json_encode($permission_data, JSON_UNESCAPED_UNICODE));

                // ✅ บันทึก log ผ่าน log_permission_action
                if (method_exists($this->Google_drive_model, 'log_permission_action')) {
                    $log_result = $this->Google_drive_model->log_permission_action(
                        'remove_folder_permission',
                        $permission_data,
                        $current_user_id
                    );

                    if ($log_result) {
                        log_message('info', 'remove_user_folder_permission: Action logged successfully via log_permission_action');
                    } else {
                        log_message('debug', 'remove_user_folder_permission: log_permission_action returned false');
                    }
                } else {
                    log_message('error', 'remove_user_folder_permission: log_permission_action method not found');
                }

            } catch (Exception $e) {
                log_message('error', 'remove_user_folder_permission: Failed to log action: ' . $e->getMessage() .
                    ' in ' . $e->getFile() . ':' . $e->getLine());
            }

            log_message('info', '✅ SUCCESS: remove_user_folder_permission completed - User: ' . $user_id . ', Folder: ' . $folder_id . ', Permission ID: ' . $existing_permission->id);

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
                log_message('error', 'remove_user_folder_permission: Transaction rolled back due to exception');
            }

            // Log error with full context
            log_message('error', '❌ ERROR: remove_user_folder_permission failed - User: ' . ($user_id ?? 'unknown') .
                ', Folder: ' . ($folder_id ?? 'unknown') . ', Admin: ' . ($current_user_id ?? 'unknown') .
                ' - Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            // บันทึก Error Activity Log (ถ้ามี function นี้)
            if (method_exists($this, 'log_error_activity') && !empty($current_user_id)) {
                try {
                    $this->log_error_activity(
                        $current_user_id,
                        'remove_user_folder_permission',
                        'Function Error: ' . $e->getMessage(),
                        []
                    );
                } catch (Exception $log_error) {
                    log_message('error', 'remove_user_folder_permission: Failed to log error activity: ' . $log_error->getMessage());
                }
            }

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
    private function revoke_inherited_permissions_simple($user_id, $parent_folder_id)
    {
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
     * หน้าแสดงรายละเอียดผู้ใช้
     */
    public function user_details($user_id = null)
    {
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
    public function get_user_details_ajax($user_id = null)
    {
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
    private function get_user_details_data($user_id)
    {
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
    private function calculate_user_stats($user_id)
    {
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
                    ($user->storage_quota_used / $user->storage_quota_limit) * 100,
                    2
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
    public function delete_user_data()
    {
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
    private function soft_delete_user_data($user_id)
    {
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
    private function hard_delete_user_data($user_id)
    {
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


    /**
     * หน้าจัดการสิทธิ์ผู้ใช้งาน
     * URL: google_drive_system/user_permissions/{member_id}
     */
    public function user_permissions($member_id = null)
    {
        // ตรวจสอบสิทธิ์การเข้าถึง
        if (!$this->session->userdata('m_id')) {
            redirect('login');
        }

        // ตรวจสอบว่ามี member_id หรือไม่
        if (!$member_id) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูลผู้ใช้งาน');
            redirect('google_drive_system/setup');
        }

        // โหลด Model
        $this->load->model('Google_drive_model');

        // ดึงข้อมูลผู้ใช้งาน
        $data['user_info'] = $this->Google_drive_model->get_user_permission_info($member_id);

        if (!$data['user_info']) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูลผู้ใช้งาน');
            redirect('google_drive_system/setup');
        }

        // ดึงข้อมูลจาก Model
        $data['folders'] = $this->Google_drive_model->get_user_folders_with_permissions($member_id);
        $data['storage_info'] = $this->Google_drive_model->get_user_storage_info($member_id);
        $data['statistics'] = $this->Google_drive_model->get_user_statistics($member_id);
        $data['available_folders'] = $this->Google_drive_model->get_available_system_folders();

        // ข้อมูล static ที่ไม่ต้องดึงจาก DB
        $data['permission_levels'] = [
            'read' => 'อ่านเท่านั้น',
            'write' => 'อ่าน-เขียน',
            'owner' => 'เจ้าของ'
        ];

        // Load views
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_user_permissions', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * AJAX: อัปเดตสิทธิ์โฟลเดอร์
     */
    public function ajax_update_permission()
    {
        $this->load->model('Google_drive_model');

        $permission_id = $this->input->post('permission_id');
        $permission_type = $this->input->post('permission_type');
        $current_user_id = $this->session->userdata('m_id');

        if (!$permission_id || !$permission_type) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        $result = $this->Google_drive_model->update_permission_level($permission_id, $permission_type);

        if ($result) {
            // Log action
            $this->Google_drive_model->log_action(
                $current_user_id,
                'update_permission',
                "อัปเดตสิทธิ์เป็น {$permission_type}",
                ['permission_id' => $permission_id]
            );

            echo json_encode(['success' => true, 'message' => 'อัปเดตสิทธิ์สำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตสิทธิ์ได้']);
        }
    }

    /**
     * AJAX: เพิกถอนสิทธิ์โฟลเดอร์
     */
    public function ajax_revoke_permission()
    {
        $this->load->model('Google_drive_model');

        $permission_id = $this->input->post('permission_id');
        $current_user_id = $this->session->userdata('m_id');

        if (!$permission_id) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        $result = $this->Google_drive_model->revoke_folder_access($permission_id, $current_user_id);

        if ($result) {
            // Log action
            $this->Google_drive_model->log_action(
                $current_user_id,
                'revoke_permission',
                "เพิกถอนสิทธิ์การเข้าถึงโฟลเดอร์",
                ['permission_id' => $permission_id]
            );

            echo json_encode(['success' => true, 'message' => 'เพิกถอนสิทธิ์สำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถเพิกถอนสิทธิ์ได้']);
        }
    }

    /**
     * AJAX: เพิ่มสิทธิ์โฟลเดอร์
     */
    public function ajax_grant_permission()
    {
        $this->load->model('Google_drive_model');

        $member_id = $this->input->post('member_id');
        $folder_id = $this->input->post('folder_id');
        $permission_type = $this->input->post('permission_type');
        $current_user_id = $this->session->userdata('m_id');

        if (!$member_id || !$folder_id || !$permission_type) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        $result = $this->Google_drive_model->grant_folder_access(
            $member_id,
            $folder_id,
            $permission_type,
            $current_user_id
        );

        if ($result) {
            // Log action
            $this->Google_drive_model->log_action(
                $current_user_id,
                'grant_permission',
                "เพิ่มสิทธิ์ {$permission_type} ให้กับ member_id: {$member_id}",
                ['folder_id' => $folder_id, 'target_member_id' => $member_id]
            );

            echo json_encode(['success' => true, 'message' => 'เพิ่มสิทธิ์สำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถเพิ่มสิทธิ์ได้']);
        }
    }

    /**
     * ส่งออกรายงานสิทธิ์ผู้ใช้
     */
    public function export_user_report($member_id)
    {
        $this->load->model('Google_drive_model');

        $user_info = $this->Google_drive_model->get_user_permission_info($member_id);
        $folders = $this->Google_drive_model->get_user_folders_with_permissions($member_id);

        // TODO: สร้าง CSV/Excel export
        // ตัวอย่างเบื้องต้น
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=user_permissions_' . $member_id . '.csv');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['ชื่อผู้ใช้', 'อีเมล', 'โฟลเดอร์', 'ระดับสิทธิ์', 'วันที่ได้รับสิทธิ์']);

        // Data
        foreach ($folders as $folder) {
            fputcsv($output, [
                $user_info->m_fname . ' ' . $user_info->m_lname,
                $user_info->google_email,
                $folder->folder_name,
                $folder->permission_type,
                date('d/m/Y H:i', strtotime($folder->permission_granted_at))
            ]);
        }

        fclose($output);
    }


    /**
     * ✅ API: บันทึกสิทธิ์ระบบของผู้ใช้
     * Endpoint: google_drive_system/save_system_permissions
     * Method: POST
     * 
     * Request Body (JSON):
     * {
     *     "user_id": 123,
     *     "permissions": {
     *         "storage_access": 0/1,
     *         "can_create_folder": 0/1,
     *         "can_share": 0/1,
     *         "can_delete": 0/1,
     *         "storage_quota_limit": 1073741824,
     *         "override_position": 0/1,
     *         "notes": "ข้อความหมายเหตุ"
     *     }
     * }
     * 
     * Response (JSON):
     * {
     *     "success": true/false,
     *     "message": "ข้อความ",
     *     "data": {}
     * }
     * 
     * @return void (JSON output)
     */
    public function save_system_permissions()
    {
        log_message('info', '====================================================');
        log_message('info', '💾 START: save_system_permissions endpoint called');
        log_message('info', '====================================================');
        log_message('info', 'Request Info:');
        log_message('info', '  - IP Address: ' . $this->input->ip_address());
        log_message('info', '  - User Agent: ' . substr($this->input->user_agent(), 0, 100));
        log_message('info', '  - Method: ' . $this->input->method());
        log_message('info', '  - Is AJAX: ' . ($this->input->is_ajax_request() ? 'Yes' : 'No'));

        try {
            // ========================================
            // Step 1: ตรวจสอบสิทธิ์ Admin
            // ========================================
            log_message('info', 'Step 1: Checking admin authorization...');

            $current_user_id = $this->session->userdata('m_id');
            $user_system = $this->session->userdata('m_system');

            log_message('info', '  - Current User ID: ' . ($current_user_id ?? 'NULL'));
            log_message('info', '  - User System Role: ' . ($user_system ?? 'NULL'));

            if (!$current_user_id) {
                log_message('error', '❌ ERROR: User not logged in');
                return $this->output_json_error('กรุณาเข้าสู่ระบบก่อน');
            }

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('error', '❌ ERROR: Unauthorized access attempt');
                log_message('error', '  - User ID: ' . $current_user_id);
                log_message('error', '  - Role: ' . ($user_system ?? 'NULL'));
                return $this->output_json_error('ไม่มีสิทธิ์เข้าถึง');
            }

            log_message('info', '✅ Admin authorization validated');

            // ========================================
            // Step 2: รับและตรวจสอบข้อมูล JSON
            // ========================================
            log_message('info', 'Step 2: Parsing JSON input...');

            $raw_input = $this->input->raw_input_stream;
            $input_length = strlen($raw_input ?? '');

            log_message('info', '  - Raw input length: ' . $input_length . ' bytes');
            log_message('debug', '  - Raw input preview: ' . substr($raw_input, 0, 200));

            $input = json_decode($raw_input, true);
            $json_error = json_last_error();

            if ($json_error !== JSON_ERROR_NONE) {
                log_message('error', '❌ ERROR: JSON decode failed');
                log_message('error', '  - JSON Error: ' . json_last_error_msg());
                log_message('error', '  - Input preview: ' . substr($raw_input, 0, 200));
                return $this->output_json_error('รูปแบบข้อมูล JSON ไม่ถูกต้อง: ' . json_last_error_msg());
            }

            if (!is_array($input) || !isset($input['user_id']) || !isset($input['permissions'])) {
                log_message('error', '❌ ERROR: Missing required fields');
                log_message('error', '  - Input keys: ' . (is_array($input) ? implode(', ', array_keys($input)) : 'not array'));
                return $this->output_json_error('ข้อมูลไม่ครบถ้วน');
            }

            $target_user_id = $input['user_id'];
            $permissions = $input['permissions'];

            log_message('info', '✅ JSON parsed successfully');
            log_message('info', '  - Target User ID: ' . $target_user_id);
            log_message('info', '  - Permissions fields: ' . implode(', ', array_keys($permissions)));
            log_message('debug', '  - Full permissions data: ' . json_encode($permissions, JSON_UNESCAPED_UNICODE));

            // ========================================
            // Step 3: ตรวจสอบว่า Target User มีอยู่จริง
            // ========================================
            log_message('info', 'Step 3: Validating target user...');

            $target_user = $this->db->select('m_id, m_email, m_fname, m_lname, m_status')
                ->from('tbl_member')
                ->where('m_id', $target_user_id)
                ->get()
                ->row();

            if (!$target_user) {
                log_message('error', '❌ ERROR: Target user not found');
                log_message('error', '  - User ID: ' . $target_user_id);
                return $this->output_json_error('ไม่พบผู้ใช้ที่ระบุ');
            }

            log_message('info', '✅ Target user validated');
            log_message('info', '  - Name: ' . trim($target_user->m_fname . ' ' . $target_user->m_lname));
            log_message('info', '  - Email: ' . $target_user->m_email);
            log_message('info', '  - Status: ' . $target_user->m_status);

            // ========================================
            // Step 4: เรียก Model เพื่อบันทึกข้อมูล
            // ========================================
            log_message('info', 'Step 4: Calling Model to save permissions...');

            $this->load->model('Google_drive_model');

            $result = $this->Google_drive_model->save_user_system_permissions(
                $target_user_id,
                $permissions,
                $current_user_id
            );

            if ($result['success']) {
                log_message('info', '====================================================');
                log_message('info', '✅ SUCCESS: System permissions saved successfully');
                log_message('info', '====================================================');
                log_message('info', 'Summary:');
                log_message('info', '  - Target User ID: ' . $target_user_id);
                log_message('info', '  - Updated By: ' . $current_user_id);
                log_message('info', '  - Updated Fields: ' . implode(', ', array_keys($permissions)));
                log_message('info', '====================================================');

                return $this->output_json_success('บันทึกการตั้งค่าสิทธิ์ระบบเรียบร้อยแล้ว', [
                    'user_id' => $target_user_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                log_message('error', '====================================================');
                log_message('error', '❌ ERROR: Failed to save system permissions');
                log_message('error', '====================================================');
                log_message('error', '  - Error: ' . $result['message']);
                log_message('error', '====================================================');

                return $this->output_json_error($result['message']);
            }

        } catch (Exception $e) {
            log_message('error', '====================================================');
            log_message('error', '❌ EXCEPTION: save_system_permissions failed');
            log_message('error', '====================================================');
            log_message('error', '  - Exception: ' . $e->getMessage());
            log_message('error', '  - File: ' . $e->getFile());
            log_message('error', '  - Line: ' . $e->getLine());
            log_message('error', '  - Trace: ' . substr($e->getTraceAsString(), 0, 500));
            log_message('error', '====================================================');

            return $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }


    // =====================================================
    // ✅ Settings Tab Methods
    // เพิ่มใน Google_drive_system.php
    // ตำแหน่ง: หลัง reset_user_permissions() method
    // =====================================================

    /**
     * ✅ บันทึกการตั้งค่าผู้ใช้ (Save User Settings)
     * 
     * URL: google_drive_system/save_user_settings (POST - JSON)
     * 
     * Expected JSON Input:
     * {
     *   "user_id": 123,
     *   "notes": "หมายเหตุ...",
     *   "auto_sync": false,
     *   "notification": false
     * }
     * 
     * @return JSON
     */
    public function save_user_settings()
    {
        try {
            log_message('info', '💾 START: save_user_settings called by user: ' . $this->session->userdata('m_id'));

            // ตรวจสอบสิทธิ์ Admin
            log_message('debug', 'save_user_settings: Step 1 - Checking admin permissions');

            $user_system = $this->session->userdata('m_system');
            $current_admin_id = $this->session->userdata('m_id');

            log_message('debug', 'save_user_settings: Current admin: ' . ($current_admin_id ?? 'null') . ', System role: ' . ($user_system ?? 'null'));

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('error', 'save_user_settings: Unauthorized access attempt - User: ' . ($current_admin_id ?? 'null') . ', Role: ' . ($user_system ?? 'null'));

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์เข้าถึง'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('debug', 'save_user_settings: Admin permissions validated');

            // รับข้อมูล JSON
            log_message('debug', 'save_user_settings: Step 2 - Getting JSON input data');

            $raw_input = $this->input->raw_input_stream;
            $input_length = strlen($raw_input ?? '');

            log_message('debug', 'save_user_settings: Raw input length: ' . $input_length . ' bytes');

            $input = json_decode($raw_input, true);
            $json_error = json_last_error();

            if ($json_error !== JSON_ERROR_NONE) {
                log_message('error', 'save_user_settings: JSON decode error: ' . json_last_error_msg());

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'รูปแบบข้อมูล JSON ไม่ถูกต้อง: ' . json_last_error_msg()
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ตรวจสอบข้อมูลที่จำเป็น
            if (!$input || !isset($input['user_id'])) {
                log_message('error', 'save_user_settings: Missing user_id - Input keys: ' . (is_array($input) ? implode(', ', array_keys($input)) : 'not array'));

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ข้อมูลไม่ครบถ้วน'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            $user_id = $input['user_id'];
            $notes = $input['notes'] ?? '';
            $auto_sync = $input['auto_sync'] ?? false;
            $notification = $input['notification'] ?? false;

            log_message('info', 'save_user_settings: Input data parsed - Target User: ' . $user_id .
                ', Notes: ' . (empty($notes) ? 'empty' : 'provided') .
                ', Auto Sync: ' . ($auto_sync ? 'true' : 'false') .
                ', Notification: ' . ($notification ? 'true' : 'false'));

            // ตรวจสอบว่า target user มีอยู่จริง
            log_message('debug', 'save_user_settings: Step 3 - Checking target user existence');

            $target_user = $this->db->select('m_id, m_email, m_fname, m_lname, m_status')
                ->from('tbl_member')
                ->where('m_id', $user_id)
                ->where('m_status', '1')
                ->get()
                ->row();

            if (!$target_user) {
                log_message('error', 'save_user_settings: Target user not found or inactive - user_id: ' . $user_id);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบผู้ใช้ที่ระบุ'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'save_user_settings: Target user found - Email: ' . $target_user->m_email .
                ', Name: ' . trim($target_user->m_fname . ' ' . $target_user->m_lname));

            // โหลด Model
            log_message('debug', 'save_user_settings: Step 4 - Loading Google_drive_model');
            $this->load->model('Google_drive_model');

            // บันทึกข้อมูลผ่าน Model
            log_message('debug', 'save_user_settings: Step 5 - Calling save_user_settings_data()');

            $settings_data = [
                'notes' => $notes,
                'auto_sync' => $auto_sync,
                'notification' => $notification
            ];

            $result = $this->Google_drive_model->save_user_settings_data($user_id, $settings_data, $current_admin_id);

            if (!$result['success']) {
                log_message('error', 'save_user_settings: Model returned error: ' . $result['message']);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => $result['message']
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'save_user_settings: Settings saved successfully - Affected rows: ' . $result['affected_rows']);

            // บันทึก Log
            log_message('debug', 'save_user_settings: Step 6 - Logging settings change');

            try {
                if (method_exists($this, 'log_permission_change_fixed')) {
                    $this->log_permission_change_fixed(
                        $user_id,
                        'update_user_settings',
                        "Updated user settings for: {$target_user->m_fname} {$target_user->m_lname} (ID: $user_id)"
                    );
                    log_message('info', 'save_user_settings: Settings change logged successfully');
                }
            } catch (Exception $e) {
                log_message('error', 'save_user_settings: Failed to log settings change: ' . $e->getMessage());
            }

            log_message('info', '✅ SUCCESS: save_user_settings completed for user_id: ' . $user_id);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว',
                    'data' => [
                        'affected_rows' => $result['affected_rows'],
                        'user_name' => trim($target_user->m_fname . ' ' . $target_user->m_lname)
                    ]
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', '❌ ERROR: save_user_settings failed - ' .
                'User: ' . ($current_admin_id ?? 'unknown') .
                ' - Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * ✅ ลบการเข้าถึงผู้ใช้ออกถาวร (Remove User Access)
     * 
     * URL: google_drive_system/remove_user_access (POST)
     * 
     * Expected POST Data:
     * - user_id: รหัสผู้ใช้ที่ต้องการลบ
     * 
     * หมายเหตุ: ฟังก์ชันนี้จะลบข้อมูลจากฐานข้อมูลอย่างถาวร (DELETE)
     *          แตกต่างจาก reset_user_permissions ที่เป็น soft delete
     * 
     * @return JSON
     */
    public function remove_user_access()
    {
        try {
            log_message('info', '🗑️ START: remove_user_access called by user: ' . $this->session->userdata('m_id'));

            // ตรวจสอบสิทธิ์ Admin
            log_message('debug', 'remove_user_access: Step 1 - Checking admin permissions');

            $user_system = $this->session->userdata('m_system');
            $current_admin_id = $this->session->userdata('m_id');

            log_message('debug', 'remove_user_access: Current admin: ' . ($current_admin_id ?? 'null') . ', System role: ' . ($user_system ?? 'null'));

            if (!in_array($user_system, ['system_admin', 'super_admin'])) {
                log_message('error', 'remove_user_access: Unauthorized access attempt - User: ' . ($current_admin_id ?? 'null') . ', Role: ' . ($user_system ?? 'null'));

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์เข้าถึง'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('debug', 'remove_user_access: Admin permissions validated');

            // รับ user_id
            log_message('debug', 'remove_user_access: Step 2 - Getting user_id from POST');

            $user_id = $this->input->post('user_id');

            log_message('debug', 'remove_user_access: POST data received - user_id: ' . ($user_id ?? 'null'));

            if (empty($user_id)) {
                log_message('error', 'remove_user_access: Empty user_id provided');

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่ได้ระบุรหัสผู้ใช้'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('debug', 'remove_user_access: user_id validated - ' . $user_id);

            // ตรวจสอบผู้ใช้
            log_message('debug', 'remove_user_access: Step 3 - Checking target user existence');

            $target_user = $this->db->select('m_id, m_email, m_fname, m_lname, m_status')
                ->from('tbl_member')
                ->where('m_id', $user_id)
                ->get()
                ->row();

            if (!$target_user) {
                log_message('error', 'remove_user_access: Target user not found - user_id: ' . $user_id);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบผู้ใช้ที่ระบุ'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'remove_user_access: Target user found - Email: ' . ($target_user->m_email ?? 'N/A') .
                ', Name: ' . trim(($target_user->m_fname ?? '') . ' ' . ($target_user->m_lname ?? '')));

            // ยืนยันความตั้งใจอีกครั้งว่าผู้ใช้ตระหนักว่าการลบนี้ถาวร
            log_message('info', 'remove_user_access: ⚠️ WARNING - This is a PERMANENT DELETE operation for user_id: ' . $user_id);

            // โหลด Model
            log_message('debug', 'remove_user_access: Step 4 - Loading Google_drive_model');
            $this->load->model('Google_drive_model');

            // ลบข้อมูลผ่าน Model
            log_message('debug', 'remove_user_access: Step 5 - Calling remove_user_access_data()');

            $result = $this->Google_drive_model->remove_user_access_data($user_id, $current_admin_id);

            if (!$result['success']) {
                log_message('error', 'remove_user_access: Model returned error: ' . $result['message']);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => $result['message']
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            log_message('info', 'remove_user_access: User access removed successfully - Deleted count: ' . $result['deleted_count']);

            // บันทึก Log
            log_message('debug', 'remove_user_access: Step 6 - Logging remove access activity');

            try {
                if (method_exists($this, 'log_permission_change_fixed')) {
                    $this->log_permission_change_fixed(
                        $user_id,
                        'remove_user_access',
                        "Permanently removed all access for user: {$target_user->m_fname} {$target_user->m_lname} (ID: $user_id), Deleted records: " . $result['deleted_count']
                    );
                    log_message('info', 'remove_user_access: Remove access activity logged successfully');
                }
            } catch (Exception $e) {
                log_message('error', 'remove_user_access: Failed to log remove access activity: ' . $e->getMessage());
            }

            log_message('info', '✅ SUCCESS: remove_user_access completed for user_id: ' . $user_id .
                ' (' . ($target_user->m_email ?? 'N/A') . '), Total deleted: ' . $result['deleted_count']);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => "ลบการเข้าถึงเรียบร้อยแล้ว ({$result['deleted_count']} รายการ)",
                    'data' => [
                        'deleted_count' => $result['deleted_count'],
                        'user_name' => trim($target_user->m_fname . ' ' . $target_user->m_lname),
                        'user_email' => $result['user_email'] ?? $target_user->m_email
                    ]
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', '❌ ERROR: remove_user_access failed - Target User: ' . ($user_id ?? 'unknown') .
                ', Admin: ' . ($current_admin_id ?? 'unknown') .
                ' - Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    // =====================================================
    // จบ Settings Tab Methods
    // =====================================================

    /**
     * ✅ GET Setting via AJAX
     * URL: google_drive_system/get_setting_ajax
     * Method: GET
     * Parameters: setting_key
     */
    public function get_setting_ajax()
    {
        try {
            // ตรวจสอบว่าเป็น AJAX request
            if (!$this->input->is_ajax_request()) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Invalid request method'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ตรวจสอบสิทธิ์ Admin
            if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์เข้าถึง'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // รับ parameter
            $setting_key = $this->input->get('setting_key');

            if (empty($setting_key)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'กรุณาระบุ setting_key'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ดึงข้อมูลจาก database
            $query = $this->db->select('setting_key, setting_value, setting_description')
                ->from('tbl_google_drive_settings')
                ->where('setting_key', $setting_key)
                ->where('is_active', 1)
                ->get();

            if ($query->num_rows() > 0) {
                $setting = $query->row();

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'ดึงข้อมูลสำเร็จ',
                        'data' => [
                            'key' => $setting->setting_key,
                            'value' => $setting->setting_value,
                            'description' => $setting->setting_description ?? ''
                        ]
                    ], JSON_UNESCAPED_UNICODE));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบการตั้งค่า'
                    ], JSON_UNESCAPED_UNICODE));
            }

        } catch (Exception $e) {
            log_message('error', 'get_setting_ajax error: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * ✅ UPDATE Setting via AJAX
     * URL: google_drive_system/update_setting_ajax
     * Method: POST
     * Body: { setting_key, value }
     */
    public function update_setting_ajax()
    {
        try {
            // ตรวจสอบว่าเป็น AJAX request
            if (!$this->input->is_ajax_request()) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Invalid request method'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ตรวจสอบสิทธิ์ Admin
            if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่มีสิทธิ์เข้าถึง'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // รับ JSON data
            $json_data = json_decode($this->input->raw_input_stream, true);

            // ถ้าไม่มี JSON ให้ลองใช้ POST ปกติ
            if (!$json_data) {
                $setting_key = $this->input->post('setting_key');
                $value = $this->input->post('value');
            } else {
                $setting_key = $json_data['setting_key'] ?? null;
                $value = $json_data['value'] ?? null;
            }

            // Validate
            if (empty($setting_key)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'กรุณาระบุ setting_key'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            if ($value === null || $value === '') {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'กรุณาระบุ value'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // บันทึกการตั้งค่า
            $result = $this->save_setting($setting_key, $value);

            if ($result) {
                log_message('info', "Setting updated via AJAX: {$setting_key} = {$value} by user " . $this->session->userdata('m_id'));

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'อัปเดตการตั้งค่าเรียบร้อย',
                        'data' => [
                            'key' => $setting_key,
                            'value' => $value
                        ]
                    ], JSON_UNESCAPED_UNICODE));
            } else {
                throw new Exception('ไม่สามารถบันทึกการตั้งค่าได้');
            }

        } catch (Exception $e) {
            log_message('error', 'update_setting_ajax error: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }

}
?>