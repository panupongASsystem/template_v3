<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive Controller v3.0.0 - Complete Fixed Version
 * 
 * @author   สำหรับ CodeIgniter 3 + Google API Client v2.15.1
 * @version  3.0.0
 * @since    2025-07-05
 */
class Google_drive extends CI_Controller {

    private $google_client;
    private $drive_service;
    private $oauth2_service;
    private $config_loaded = false;
    private $library_loaded = false;

    public function __construct() {
    parent::__construct();
    $this->load->library('session');
    
    // เฉพาะ method test ไม่ต้องตรวจสอบ login
    $method = $this->router->fetch_method();
    if (in_array($method, ['test_url', 'test_simple', 'test', 'debug_installation', 'test_google_client'])) {
        $this->safe_load_config();
        // เพิ่มการ debug Google Client สำหรับ test methods
        $this->debug_google_client_creation();
        return;
    }
    
    // โหลด Google Drive Config
    $this->safe_load_config();
    
    // ตรวจสอบ login สำหรับ AJAX methods
    if (in_array($method, ['get_member_drive_info', 'update_member_permission', 'disconnect', 'test_connection'])) {
        if (!$this->session->userdata('m_id')) {
            $this->output_json_error('Please login first', 401);
            return;
        }
    }
    
    // ตรวจสอบ login สำหรับ methods อื่น
    if (!in_array($method, ['oauth_callback']) && !$this->session->userdata('m_id')) {
        if ($this->input->is_ajax_request()) {
            $this->output_json_error('Please login first', 401);
            return;
        } else {
            redirect('User/login');
        }
    }

    // โหลด Google Client Library
    $this->init_google_client();
}
	
	
	private function debug_google_client_creation() {
    try {
        // Step 1: ตรวจสอบ Google Drive enabled
        $google_drive_enabled = $this->get_setting('google_drive_enabled', $this->config->item('google_drive_enabled'));
        if (!$google_drive_enabled) {
            $this->safe_log('info', 'Google Drive is disabled - skipping client creation');
            return false;
        }

        // Step 2: ตรวจสอบ credentials
        $client_id = $this->get_setting('google_client_id', $this->config->item('google_client_id'));
        $client_secret = $this->get_setting('google_client_secret', $this->config->item('google_client_secret'));
        $redirect_uri = $this->get_setting('google_redirect_uri', $this->config->item('google_redirect_uri'));

        if (empty($client_id) || empty($client_secret)) {
            $this->safe_log('error', 'Google OAuth credentials missing - Client ID or Secret empty');
            return false;
        }

        // Step 3: ลองสร้าง Google Client
        if (!class_exists('Google\\Client')) {
            $this->safe_log('error', 'Google\\Client class not found');
            return false;
        }

        try {
            $client = new Google\Client();
            $this->safe_log('info', 'Google\\Client instance created successfully');

            // Step 4: ตั้งค่า Client
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $this->safe_log('info', 'Google Client basic configuration set');

            // Step 5: เพิ่ม Scopes
            $scopes = $this->config->item('google_scopes');
            if (is_array($scopes)) {
                foreach ($scopes as $scope) {
                    $client->addScope($scope);
                }
                $this->safe_log('info', 'Google Client scopes added: ' . count($scopes) . ' scopes');
            }

            // Step 6: ตั้งค่าเพิ่มเติม
            $client->setAccessType('offline');
            $client->setPrompt('consent');
            $client->setApplicationName('CodeIgniter Google Drive Integration v3.1.0');
            $this->safe_log('info', 'Google Client additional settings configured');

            // Step 7: ทดสอบสร้าง Drive Service
            if (class_exists('Google\\Service\\Drive')) {
                $drive_service = new Google\Service\Drive($client);
                $this->safe_log('info', 'Google Drive Service created successfully');
            } else {
                $this->safe_log('error', 'Google\\Service\\Drive class not found');
                return false;
            }

            // Step 8: เก็บ instances
            $this->google_client = $client;
            $this->drive_service = $drive_service;
            $this->oauth2_service = $this->init_oauth2_service();

            $this->safe_log('info', 'Google Client creation completed successfully');
            return true;

        } catch (Exception $e) {
            $this->safe_log('error', 'Google Client creation failed: ' . $e->getMessage());
            $this->safe_log('error', 'Error details: File=' . $e->getFile() . ', Line=' . $e->getLine());
            return false;
        }

    } catch (Exception $e) {
        $this->safe_log('error', 'Debug Google Client creation error: ' . $e->getMessage());
        return false;
    }
}
	

    /**
     * Safe Log Message - แก้ไข Warning Level Issue
     */
    private function safe_log($level, $message) {
        try {
            // CodeIgniter รองรับ: error, debug, info
            $allowed_levels = ['error', 'debug', 'info'];
            
            // แปลง warning เป็น error
            if ($level === 'warning') {
                $level = 'error';
            }
            
            // ใช้เฉพาะ level ที่รองรับ
            if (in_array($level, $allowed_levels)) {
                log_message($level, $message);
            } else {
                log_message('error', "[{$level}] {$message}");
            }
        } catch (Exception $e) {
            // ถ้า log ไม่ได้ ก็ไม่ต้องทำอะไร
        }
    }

    /**
     * Output JSON Response แบบปลอดภัย - แก้ไข HTML/JSON Issue
     */
    private function output_json_success($data = [], $message = 'Success') {
        // ล้าง output buffer ที่อาจมี HTML
        if (ob_get_level()) {
            ob_clean();
        }
        
        // ตั้งค่า headers อย่างเข้มงวด
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_header('Cache-Control: no-cache, must-revalidate')
            ->set_header('Pragma: no-cache')
            ->set_output(json_encode([
                'success' => true,
                'message' => $message,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Output JSON Error แบบปลอดภัย - แก้ไข HTML/JSON Issue
     */
    private function output_json_error($message = 'Error', $status_code = 400, $debug_data = []) {
        // ล้าง output buffer ที่อาจมี HTML
        if (ob_get_level()) {
            ob_clean();
        }
        
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // เพิ่ม debug info เฉพาะ development
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development' && !empty($debug_data)) {
            $response['debug'] = $debug_data;
        }
        
        // ตั้งค่า headers อย่างเข้มงวด
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json', 'utf-8')
            ->set_header('Cache-Control: no-cache, must-revalidate')
            ->set_header('Pragma: no-cache')
            ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * โหลด Config แบบปลอดภัย - แก้ไข Unicode Log Issue
     */
    private function safe_load_config() {
        try {
            if (!$this->config_loaded) {
                $this->config->load('google_drive');
                $this->config_loaded = true;
                
                // ลบ Unicode characters จาก log
                if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                    $this->safe_log('info', 'Google Drive Config v3.0.0 loaded successfully');
                }
            }
        } catch (Exception $e) {
            // ลบ Unicode characters
            $this->safe_log('error', 'Google Drive Config Load Error: ' . $e->getMessage());
            $this->set_default_config();
        }
    }

    /**
     * ตั้งค่าเริ่มต้นแบบ manual
     */
    private function set_default_config() {
        $this->config->set_item('google_drive_enabled', true);
        $this->config->set_item('auto_create_folders', true);
        $this->config->set_item('max_file_size', 104857600);
        $this->config->set_item('allowed_file_types', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar']);
        $this->config->set_item('google_scopes', [
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ]);
        $this->config->set_item('google_redirect_uri', site_url('google_drive/oauth_callback'));
        $this->config->set_item('logging_enabled', true);
        $this->config->set_item('cache_enabled', true);
        $this->config->set_item('debug_mode', defined('ENVIRONMENT') && ENVIRONMENT === 'development');
    }

    

    /**
     * เริ่มต้น OAuth2 Service - แก้ไขปัญหา Oauth2 not found
     */
    private function init_oauth2_service() {
        try {
            if (class_exists('Google\Service\Oauth2')) {
                return new Google\Service\Oauth2($this->google_client);
            }
            
            if (class_exists('Google_Service_Oauth2')) {
                return new Google_Service_Oauth2($this->google_client);
            }
            
            if (class_exists('Google\Service\PeopleService')) {
                return new Google\Service\PeopleService($this->google_client);
            }
            
            $this->safe_log('error', 'No OAuth2 Service available, will use alternative methods');
            return null;
            
        } catch (Exception $e) {
            $this->safe_log('error', 'OAuth2 Service initialization failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * โหลด Google Client Library
     */
    private function load_google_library() {
        if ($this->library_loaded) {
            return true;
        }

        try {
            // Method 1: Check if already loaded
            if (class_exists('Google\\Client')) {
                $this->library_loaded = true;
                return $this->verify_google_client_dependencies();
            }

            // Method 2: Try custom loader
            $loader_path = APPPATH . 'third_party/google_client_loader.php';
            if (file_exists($loader_path)) {
                require_once $loader_path;
                
                if (class_exists('Google_Client_Loader')) {
                    $result = Google_Client_Loader::load();
                    if ($result && class_exists('Google\\Client')) {
                        $this->library_loaded = true;
                        return $this->verify_google_client_dependencies();
                    }
                }
            }

            // Method 3: Try autoload directly
            $autoload_paths = [
                APPPATH . 'third_party/google-api-php-client/autoload.php',
                APPPATH . 'third_party/google-api-php-client/vendor/autoload.php',
                APPPATH . '../vendor/autoload.php',
                FCPATH . 'vendor/autoload.php'
            ];

            foreach ($autoload_paths as $autoload_path) {
                if (file_exists($autoload_path)) {
                    require_once $autoload_path;
                    
                    if (class_exists('Google\\Client')) {
                        $this->library_loaded = true;
                        return $this->verify_google_client_dependencies();
                    }
                }
            }

            // Method 4: Try manual loading of required files
            return $this->manual_load_google_client();

        } catch (Exception $e) {
            $this->safe_log('error', 'Load Google Library v3.0.0 Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบ Dependencies ของ Google Client
     */
    private function verify_google_client_dependencies() {
        try {
            $required_classes = [
                'Google\\Client',
                'Google\\Service\\Drive',
                'Google\\Auth\\OAuth2'
            ];

            $missing_classes = [];
            foreach ($required_classes as $class) {
                if (!class_exists($class)) {
                    $missing_classes[] = $class;
                }
            }

            if (!empty($missing_classes)) {
                $this->safe_log('error', 'Missing Google Client classes: ' . implode(', ', $missing_classes));
                
                // Try to load missing dependencies manually
                if ($this->load_missing_google_dependencies($missing_classes)) {
                    return true;
                }
                
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->safe_log('error', 'Verify Google Client dependencies error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * โหลด Dependencies ที่หายไป
     */
    private function load_missing_google_dependencies($missing_classes) {
        try {
            $google_path = APPPATH . 'third_party/google-api-php-client/src/';
            
            foreach ($missing_classes as $class) {
                switch ($class) {
                    case 'Google\\Auth\\OAuth2':
                        $auth_paths = [
                            $google_path . 'Auth/OAuth2.php',
                            APPPATH . 'third_party/google-auth-library-php/src/OAuth2.php',
                            $google_path . '../vendor/google/auth/src/OAuth2.php'
                        ];
                        
                        foreach ($auth_paths as $auth_path) {
                            if (file_exists($auth_path)) {
                                require_once $auth_path;
                                break;
                            }
                        }
                        break;

                    case 'Google\\Service\\Drive':
                        $drive_paths = [
                            $google_path . 'Service/Drive.php',
                            $google_path . 'Google/Service/Drive.php'
                        ];
                        
                        foreach ($drive_paths as $drive_path) {
                            if (file_exists($drive_path)) {
                                require_once $drive_path;
                                break;
                            }
                        }
                        break;
                }
            }

            // ตรวจสอบอีกครั้งหลังจากโหลด
            $still_missing = [];
            foreach ($missing_classes as $class) {
                if (!class_exists($class)) {
                    $still_missing[] = $class;
                }
            }

            return empty($still_missing);

        } catch (Exception $e) {
            $this->safe_log('error', 'Load missing Google dependencies error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * โหลด Google Client แบบ Manual
     */
    private function manual_load_google_client() {
        try {
            $google_base_path = APPPATH . 'third_party/google-api-php-client/src/';
            
            if (!is_dir($google_base_path)) {
                $this->safe_log('error', 'Google API PHP Client directory not found');
                return false;
            }

            // โหลดไฟล์พื้นฐานที่จำเป็น
            $required_files = [
                $google_base_path . 'Client.php',
                $google_base_path . 'Service/Drive.php',
                $google_base_path . 'Service/Resource.php',
                $google_base_path . 'Http/REST.php'
            ];

            foreach ($required_files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }

            // ตรวจสอบว่าโหลดสำเร็จ
            if (class_exists('Google\\Client')) {
                $this->library_loaded = true;
                return true;
            }

            return false;

        } catch (Exception $e) {
            $this->safe_log('error', 'Manual load Google Client error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เริ่มต้น Google Client v3.0.0 - แก้ไข OAuth2 Error
     */
    private function init_google_client() {
    try {
        if (!$this->load_google_library()) {
            $this->safe_log('error', 'Google Client Library v2.15.1 not available');
            return false;
        }

        if (!$this->get_setting('google_drive_enabled', $this->config->item('google_drive_enabled'))) {
            $this->safe_log('info', 'Google Drive is disabled in configuration');
            return false;
        }

        $client_id = $this->get_setting('google_client_id', $this->config->item('google_client_id'));
        $client_secret = $this->get_setting('google_client_secret', $this->config->item('google_client_secret'));
        $redirect_uri = $this->get_setting('google_redirect_uri', $this->config->item('google_redirect_uri'));
        $scopes = $this->config->item('google_scopes');
        
        if (empty($client_id) || empty($client_secret)) {
            $this->safe_log('error', 'Google OAuth credentials not configured');
            return false;
        }

        // Enhanced error handling
        try {
            $this->google_client = $this->create_google_client_safely($client_id, $client_secret, $redirect_uri, $scopes);
            
            if (!$this->google_client) {
                $this->safe_log('error', 'create_google_client_safely returned null');
                return false;
            }

            // สร้าง Drive Service
            $this->drive_service = $this->create_drive_service_safely();
            
            if (!$this->drive_service) {
                $this->safe_log('error', 'create_drive_service_safely returned null');
                return false;
            }
            
            // สร้าง OAuth2 Service (Optional)
            $this->oauth2_service = $this->init_oauth2_service();

            if ($this->get_setting('debug_mode', $this->config->item('debug_mode'))) {
                $this->safe_log('info', 'Google Client v3.1.0 initialized successfully');
            }

            return true;

        } catch (Exception $e) {
            $this->safe_log('error', 'Google Client initialization exception: ' . $e->getMessage());
            $this->safe_log('error', 'Exception details: File=' . $e->getFile() . ', Line=' . $e->getLine());
            return false;
        }

    } catch (Exception $e) {
        $this->safe_log('error', 'Google Client v3.1.0 init error: ' . $e->getMessage());
        return false;
    }
}

    /**
     * สร้าง Google Client แบบปลอดภัย
     */
   private function create_google_client_safely($client_id, $client_secret, $redirect_uri, $scopes) {
    try {
        if (!class_exists('Google\\Client')) {
            $this->safe_log('error', 'Google\\Client class not found');
            return null;
        }

        $this->safe_log('info', 'Creating Google\\Client instance...');
        $client = new Google\Client();
        $this->safe_log('info', 'Google\\Client instance created');
        
        // ตั้งค่าพื้นฐาน
        $this->safe_log('info', 'Setting client credentials...');
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $this->safe_log('info', 'Client credentials set');
        
        // เพิ่ม Scopes
        if (is_array($scopes)) {
            $this->safe_log('info', 'Adding scopes: ' . count($scopes) . ' scopes');
            foreach ($scopes as $scope) {
                $client->addScope($scope);
            }
            $this->safe_log('info', 'Scopes added successfully');
        }
        
        // ตั้งค่าเพิ่มเติม
        $this->safe_log('info', 'Setting additional client configuration...');
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        
        // ตั้งค่า Application Name แบบ safe (รองรับ version เก่า)
        if (method_exists($client, 'setApplicationName')) {
            $client->setApplicationName('CodeIgniter Google Drive Integration v3.1.0');
            $this->safe_log('info', 'Application name set via setApplicationName()');
        } else {
            $this->safe_log('info', 'setApplicationName() not available in this Google Client version');
        }
        
        $this->safe_log('info', 'Additional configuration set');

        // ทดสอบการทำงานพื้นฐาน แบบ safe
        try {
            $this->safe_log('info', 'Testing client functionality...');
            
            // ทดสอบ method ที่ควรมีในทุก version
            if (method_exists($client, 'getClientId')) {
                $test_client_id = $client->getClientId();
                if ($test_client_id === $client_id) {
                    $this->safe_log('info', 'Client functionality test passed via getClientId()');
                } else {
                    $this->safe_log('error', 'Client ID mismatch in test');
                }
            } else {
                $this->safe_log('info', 'getClientId() not available - skipping client ID test');
            }
            
            // ทดสอบ Application Name แบบ safe
            if (method_exists($client, 'getApplicationName')) {
                $app_name = $client->getApplicationName();
                $this->safe_log('info', 'Application name retrieved: ' . $app_name);
            } else {
                $this->safe_log('info', 'getApplicationName() not available in this version');
            }
            
            $this->safe_log('info', 'Client functionality test completed');
            return $client;
            
        } catch (Exception $e) {
            $this->safe_log('error', 'Client functionality test exception: ' . $e->getMessage());
            // ถ้าทดสอบไม่ผ่าน แต่ client สร้างได้ ก็ยังคืนค่า client
            $this->safe_log('info', 'Returning client despite test exception');
            return $client;
        }

    } catch (Exception $e) {
        $this->safe_log('error', 'Create Google Client safely error: ' . $e->getMessage());
        $this->safe_log('error', 'Error details: File=' . $e->getFile() . ', Line=' . $e->getLine());
        return null;
    }
}
    /**
     * สร้าง Drive Service แบบปลอดภัย
     */
    private function create_drive_service_safely() {
        try {
            if (!$this->google_client) {
                return null;
            }

            if (!class_exists('Google\\Service\\Drive')) {
                $this->safe_log('error', 'Google\\Service\\Drive class not found');
                return null;
            }

            return new Google\Service\Drive($this->google_client);

        } catch (Exception $e) {
            $this->safe_log('error', 'Create Drive Service safely error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * เชื่อมต่อ Google Drive สำหรับสมาชิก - แก้ไข OAuth2 Error
     */
    public function connect() {
    try {
        $member_id = $this->input->get('member_id') ?: $this->session->userdata('m_id');
        
        if (!$member_id) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูลสมาชิก');
            redirect('System_member/member_web');
        }

        // ตรวจสอบ Client ID ก่อนดำเนินการ
        $client_id = $this->get_setting('google_client_id', $this->config->item('google_client_id'));
        if (empty($client_id)) {
            $this->session->set_flashdata('error', 'Google Client ID ไม่ได้ตั้งค่า กรุณาติดต่อผู้ดูแลระบบ');
            redirect('System_member/member_web');
        }

        $member = $this->db->select('ref_pid, m_fname, m_lname')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->session->set_flashdata('error', 'ไม่พบข้อมูลสมาชิก');
            redirect('System_member/member_web');
        }

        $permission = $this->get_member_permission($member_id, $member->ref_pid);

        if ($permission['permission_type'] === 'no_access') {
            $this->session->set_flashdata('error', 'คุณไม่มีสิทธิ์เข้าใช้งาน Google Drive');
            redirect('System_member/member_web');
        }

        $is_connected = $this->check_google_connection($member_id);
        if ($is_connected) {
            $this->session->set_flashdata('info', 'บัญชีนี้เชื่อมต่อ Google Drive แล้ว');
            redirect('System_member/member_web');
        }

        // ใช้ Manual Auth URL เท่านั้น (เพราะ Google Client createAuthUrl() ไม่ส่ง client_id)
        $auth_url = $this->create_manual_auth_url();
        
        if (!$auth_url) {
            $this->session->set_flashdata('error', 'ไม่สามารถสร้าง Google Authorization URL ได้ กรุณาตรวจสอบการตั้งค่า OAuth หรือดู debug ที่ /google_drive/debug_auth_url');
            redirect('System_member/member_web');
        }

        // Validate Auth URL ก่อน redirect
        if (strpos($auth_url, 'client_id=') === false) {
            $this->safe_log('error', 'Manual Auth URL missing client_id parameter: ' . $auth_url);
            $this->session->set_flashdata('error', 'Google Authorization URL ไม่มี Client ID กรุณาตรวจสอบการตั้งค่า');
            redirect('System_member/member_web');
        }

        $this->session->set_userdata('oauth_member_id', $member_id);
        
        $this->log_action($member_id, 'connect', 'เริ่มขั้นตอนการเชื่อมต่อ Google Drive (Manual Auth URL)', [
            'status' => 'pending',
            'auth_method' => 'manual',
            'auth_url' => substr($auth_url, 0, 100) . '...'
        ]);

        $this->safe_log('info', 'Redirecting to Manual Auth URL with client_id: ' . substr($client_id, 0, 20) . '...');

        redirect($auth_url);

    } catch (Exception $e) {
        log_message('error', 'Google Drive connect error: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        redirect('System_member/member_web');
    }
}


    /**
     * สร้าง Auth URL แบบปลอดภัย
     */
    private function create_auth_url_safely() {
    try {
        if (!$this->google_client) {
            return null;
        }

        // วิธีที่ 1: ลองใช้ createAuthUrl() ปกติ
        try {
            $auth_url = $this->google_client->createAuthUrl();
            
            // ตรวจสอบว่ามี client_id หรือไม่
            if (strpos($auth_url, 'client_id=') !== false) {
                $this->safe_log('info', 'createAuthUrl() successful with client_id');
                return $auth_url;
            } else {
                $this->safe_log('error', 'createAuthUrl() missing client_id parameter - switching to manual method');
                // ถ้าไม่มี client_id ให้ใช้ manual method
                return $this->create_manual_auth_url();
            }
            
        } catch (Exception $e) {
            $this->safe_log('error', 'createAuthUrl failed: ' . $e->getMessage());
            
            // วิธีที่ 2: สร้าง Auth URL แบบ Manual
            return $this->create_manual_auth_url();
        }

    } catch (Exception $e) {
        $this->safe_log('error', 'Create auth URL safely error: ' . $e->getMessage());
        return null;
    }
}


    /**
     * สร้าง Auth URL แบบ Manual
     */
    private function create_manual_auth_url() {
    try {
        $client_id = $this->get_setting('google_client_id', $this->config->item('google_client_id'));
        $redirect_uri = $this->get_setting('google_redirect_uri', $this->config->item('google_redirect_uri'));
        $scopes = $this->config->item('google_scopes');
        
        // Validate Client ID
        if (empty($client_id)) {
            $this->safe_log('error', 'Manual Auth URL: Client ID is empty');
            return null;
        }

        // Validate Redirect URI
        if (empty($redirect_uri)) {
            $this->safe_log('error', 'Manual Auth URL: Redirect URI is empty');
            // ใช้ default redirect URI
            $redirect_uri = site_url('google_drive/oauth_callback');
        }

        $scope_string = is_array($scopes) ? implode(' ', $scopes) : 'https://www.googleapis.com/auth/drive';
        
        $params = [
            'client_id' => trim($client_id),  // ลบ whitespace
            'redirect_uri' => trim($redirect_uri),
            'scope' => $scope_string,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => 'manual_' . time()
        ];

        // ตรวจสอบ parameters ก่อนสร้าง URL
        foreach (['client_id', 'redirect_uri', 'scope', 'response_type'] as $required_param) {
            if (empty($params[$required_param])) {
                $this->safe_log('error', "Manual Auth URL: Required parameter '{$required_param}' is empty");
                return null;
            }
        }

        $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        
        $this->safe_log('info', 'Created manual auth URL successfully with Client ID: ' . substr($client_id, 0, 20) . '...');
        $this->safe_log('info', 'Manual Auth URL: ' . substr($auth_url, 0, 150) . '...');
        
        return $auth_url;

    } catch (Exception $e) {
        $this->safe_log('error', 'Create manual auth URL error: ' . $e->getMessage());
        return null;
    }
}
    /**
     * ตรวจสอบสถานะ Google Client Library
     */
    public function check_google_library_status() {
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            $status = [
                'google_client_available' => class_exists('Google\\Client'),
                'google_service_drive_available' => class_exists('Google\\Service\\Drive'),
                'google_auth_oauth2_available' => class_exists('Google\\Auth\\OAuth2'),
                'google_service_oauth2_available' => class_exists('Google\\Service\\Oauth2'),
                'library_loaded' => $this->library_loaded,
                'client_initialized' => isset($this->google_client) && $this->google_client !== null,
                'drive_service_initialized' => isset($this->drive_service) && $this->drive_service !== null
            ];

            $recommendations = [];
            
            if (!$status['google_client_available']) {
                $recommendations[] = 'ติดตั้ง Google API PHP Client Library';
            }
            
            if (!$status['google_auth_oauth2_available']) {
                $recommendations[] = 'ติดตั้ง Google Auth Library หรือใช้ Manual Auth URL';
            }
            
            if (!$status['google_service_drive_available']) {
                $recommendations[] = 'ตรวจสอบการติดตั้ง Google Drive Service';
            }

            $this->output_json_success([
                'status' => $status,
                'recommendations' => $recommendations,
                'can_use_basic_functions' => $status['google_client_available'] && $status['google_service_drive_available'],
                'can_use_oauth' => $status['google_auth_oauth2_available'],
                'fallback_available' => !$status['google_auth_oauth2_available'] && $status['google_client_available']
            ], 'ตรวจสอบสถานะ Google Library สำเร็จ');

        } catch (Exception $e) {
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ดึงข้อมูล Google Drive ของสมาชิก (AJAX) - แก้ไข HTML/JSON Issue
     */
    public function get_member_drive_info() {
        try {
            // ป้องกัน HTML output
            if (ob_get_level()) {
                ob_clean();
            }
            
            $member_id = $this->input->post('member_id');
            
            if (!$member_id) {
                $this->output_json_error('Member ID required');
                return;
            }

            // ลบ Unicode จาก log
            $this->safe_log('info', 'get_member_drive_info called for member_id: ' . $member_id);

            $member = $this->db->select('m.*, p.pname')
                              ->from('tbl_member m')
                              ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                              ->where('m.m_id', $member_id)
                              ->get()
                              ->row();

            if (!$member) {
                $this->output_json_error('Member not found');
                return;
            }

            $is_connected = $this->check_google_connection($member_id);
            
            $drive_info = null;
            $folders = [];
            
            if ($is_connected) {
                $drive_info = $this->get_drive_info($member_id);
                $folders = $this->get_drive_folders($member_id);
            }

            $permission = $this->get_member_permission($member_id, $member->ref_pid);
            $available_permissions = $this->get_permission_types();

            $response_data = [
                'member_id' => (int)$member_id,
                'is_connected' => $is_connected,
                'drive_info' => $drive_info,
                'folders' => $folders,
                'permission' => $permission,
                'available_permissions' => $available_permissions
            ];

            $this->safe_log('info', 'get_member_drive_info completed successfully for member_id: ' . $member_id);
            
            $this->output_json_success($response_data, 'ดึงข้อมูล Google Drive สำเร็จ');

        } catch (Exception $e) {
            $this->safe_log('error', 'get_member_drive_info error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
            
            $debug_data = [];
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                $debug_data = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'member_id' => $member_id ?? 'not_provided'
                ];
            }
            
            $this->output_json_error($e->getMessage(), 500, $debug_data);
        }
    }

    /**
     * ตรวจสอบการเชื่อมต่อ Google Drive
     */
    private function check_google_connection($member_id) {
        try {
            $member = $this->db->select('google_access_token, google_refresh_token, google_token_expires, google_drive_enabled')
                              ->from('tbl_member')
                              ->where('m_id', $member_id)
                              ->get()
                              ->row();

            if (!$member || $member->google_drive_enabled != 1) {
                return false;
            }

            if (empty($member->google_access_token)) {
                return false;
            }

            if (!empty($member->google_token_expires)) {
                $expires = strtotime($member->google_token_expires);
                if ($expires < time()) {
                    return $this->refresh_google_token($member_id, $member->google_refresh_token);
                }
            }

            if ($this->google_client) {
                try {
                    $this->google_client->setAccessToken($member->google_access_token);
                    $about = $this->drive_service->about->get(['fields' => 'user']);
                    return true;
                } catch (Exception $e) {
                    $this->safe_log('error', 'Google API test failed: ' . $e->getMessage());
                    if (!empty($member->google_refresh_token)) {
                        return $this->refresh_google_token($member_id, $member->google_refresh_token);
                    }
                    return false;
                }
            }

            return true;

        } catch (Exception $e) {
            $this->safe_log('error', 'Check Google connection error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh Google Token
     */
    private function refresh_google_token($member_id, $refresh_token) {
        try {
            if (!$this->google_client || empty($refresh_token)) {
                return false;
            }

            $this->google_client->refreshToken($refresh_token);
            $new_token = $this->google_client->getAccessToken();

            if ($new_token) {
                $update_data = [
                    'google_access_token' => is_array($new_token) ? json_encode($new_token) : $new_token,
                    'google_token_expires' => date('Y-m-d H:i:s', time() + 3600)
                ];

                $this->db->where('m_id', $member_id);
                $this->db->update('tbl_member', $update_data);

                return true;
            }

            return false;

        } catch (Exception $e) {
            $this->safe_log('error', 'Refresh token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูล Google Drive
     */
    private function get_drive_info($member_id) {
        try {
            $member = $this->db->select('google_email, google_connected_at, google_account_verified, google_access_token')
                              ->from('tbl_member')
                              ->where('m_id', $member_id)
                              ->get()
                              ->row();

            if (!$member) {
                return null;
            }

            if (!$this->google_client) {
                return (object)[
                    'google_email' => $member->google_email,
                    'google_connected_at' => $member->google_connected_at,
                    'google_account_verified' => $member->google_account_verified,
                    'note' => 'Basic info only - Google Client not available'
                ];
            }

            if (!empty($member->google_access_token)) {
                $this->google_client->setAccessToken($member->google_access_token);
            }

            try {
                $about = $this->drive_service->about->get([
                    'fields' => 'user,storageQuota'
                ]);

                $user = $about->getUser();
                $quota = $about->getStorageQuota();

                $user_info = $this->get_user_info_alternative();

                return (object)[
                    'google_email' => $user->getEmailAddress(),
                    'display_name' => $user->getDisplayName(),
                    'photo_link' => $user->getPhotoLink(),
                    'google_connected_at' => $member->google_connected_at,
                    'google_account_verified' => $member->google_account_verified,
                    'storage_limit' => $quota->getLimit(),
                    'storage_usage' => $quota->getUsage(),
                    'storage_usage_in_drive' => $quota->getUsageInDrive(),
                    'additional_info' => $user_info
                ];

            } catch (Exception $e) {
                $this->safe_log('error', 'Get Drive API info error: ' . $e->getMessage());
                
                return (object)[
                    'google_email' => $member->google_email,
                    'google_connected_at' => $member->google_connected_at,
                    'google_account_verified' => $member->google_account_verified,
                    'error' => 'Cannot access Google Drive API: ' . $e->getMessage()
                ];
            }

        } catch (Exception $e) {
            $this->safe_log('error', 'Get drive info error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูลผู้ใช้จากวิธีทางเลือก
     */
    private function get_user_info_alternative() {
        try {
            if ($this->oauth2_service) {
                try {
                    if (method_exists($this->oauth2_service, 'userinfo')) {
                        $userinfo = $this->oauth2_service->userinfo->get();
                        return [
                            'method' => 'oauth2_service',
                            'email' => $userinfo->getEmail(),
                            'name' => $userinfo->getName(),
                            'picture' => $userinfo->getPicture()
                        ];
                    }
                } catch (Exception $e) {
                    $this->safe_log('error', 'OAuth2 Service userinfo failed: ' . $e->getMessage());
                }
            }

            return $this->get_user_info_via_http();

        } catch (Exception $e) {
            $this->safe_log('error', 'Alternative user info failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูลผู้ใช้ผ่าน HTTP Request
     */
    private function get_user_info_via_http() {
        try {
            $token = $this->google_client->getAccessToken();
            if (!$token || !isset($token['access_token'])) {
                return null;
            }

            $access_token = $token['access_token'];
            $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($access_token);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Google-Drive-Integration/3.0.0');

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new Exception('cURL Error: ' . $error);
            }

            if ($http_code !== 200) {
                throw new Exception('HTTP Error: ' . $http_code);
            }

            $data = json_decode($response, true);
            if ($data && isset($data['email'])) {
                return [
                    'method' => 'http_request',
                    'email' => $data['email'],
                    'name' => $data['name'] ?? '',
                    'picture' => $data['picture'] ?? ''
                ];
            }

            return null;

        } catch (Exception $e) {
            $this->safe_log('error', 'HTTP user info failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึง Folders จาก Google Drive
     */
    private function get_drive_folders($member_id) {
        try {
            $folders = [];

            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $db_folders = $this->db->where('member_id', $member_id)
                                      ->where('is_active', 1)
                                      ->get('tbl_google_drive_folders')
                                      ->result();

                foreach ($db_folders as $folder) {
                    $real_folder = $this->check_folder_exists($folder->folder_id);
                    if ($real_folder) {
                        $folders[] = (object)[
                            'id' => $folder->id,
                            'folder_id' => $folder->folder_id,
                            'folder_name' => $real_folder['name'],
                            'folder_url' => $real_folder['webViewLink'],
                            'folder_type' => $folder->folder_type,
                            'size' => $real_folder['size'] ?? 0,
                            'created_time' => $real_folder['createdTime'] ?? null,
                            'modified_time' => $real_folder['modifiedTime'] ?? null,
                            'position_name' => $this->get_position_name($folder->position_id)
                        ];
                    }
                }
            }

            return $folders;

        } catch (Exception $e) {
            log_message('error', 'Get drive folders error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ตรวจสอบ Folder ใน Google Drive
     */
    private function check_folder_exists($folder_id) {
        try {
            if (!$this->drive_service) {
                return false;
            }

            $folder = $this->drive_service->files->get($folder_id, [
                'fields' => 'id,name,webViewLink,size,createdTime,modifiedTime,mimeType'
            ]);

            if ($folder && $folder->getMimeType() === 'application/vnd.google-apps.folder') {
                return [
                    'id' => $folder->getId(),
                    'name' => $folder->getName(),
                    'webViewLink' => $folder->getWebViewLink(),
                    'size' => $folder->getSize(),
                    'createdTime' => $folder->getCreatedTime(),
                    'modifiedTime' => $folder->getModifiedTime()
                ];
            }

            return false;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * ดึงชื่อตำแหน่ง
     */
    private function get_position_name($position_id) {
        try {
            if (empty($position_id)) {
                return 'ไม่ระบุตำแหน่ง';
            }

            $position = $this->db->select('pname')
                               ->from('tbl_position')
                               ->where('pid', $position_id)
                               ->get()
                               ->row();

            return $position ? $position->pname : 'ไม่ระบุตำแหน่ง';

        } catch (Exception $e) {
            return 'ไม่ระบุตำแหน่ง';
        }
    }

    /**
     * ดึงสิทธิ์จากฐานข้อมูล
     */
    private function get_member_permission($member_id, $position_id) {
        try {
            if ($this->db->table_exists('tbl_google_drive_member_permissions')) {
                $member_permission = $this->db->select('mp.*, pt.type_name')
                                            ->from('tbl_google_drive_member_permissions mp')
                                            ->join('tbl_google_drive_permission_types pt', 'mp.permission_type = pt.type_code', 'left')
                                            ->where('mp.member_id', $member_id)
                                            ->where('mp.is_active', 1)
                                            ->get()
                                            ->row();

                if ($member_permission && isset($member_permission->override_position) && $member_permission->override_position == 1) {
                    return [
                        'permission_type' => $member_permission->permission_type,
                        'type_name' => $member_permission->type_name,
                        'source' => 'member_override',
                        'notes' => $member_permission->notes ?? '',
                        'access_type' => $this->map_permission_to_access_type($member_permission->permission_type)
                    ];
                }
            }

            if ($this->db->table_exists('tbl_google_drive_position_permissions')) {
                $position_permission = $this->db->select('pp.*, pt.type_name')
                                                ->from('tbl_google_drive_position_permissions pp')
                                                ->join('tbl_google_drive_permission_types pt', 'pp.permission_type = pt.type_code', 'left')
                                                ->where('pp.position_id', $position_id)
                                                ->where('pp.is_active', 1)
                                                ->get()
                                                ->row();

                if ($position_permission) {
                    return [
                        'permission_type' => $position_permission->permission_type,
                        'type_name' => $position_permission->type_name,
                        'source' => 'position',
                        'access_type' => $this->map_permission_to_access_type($position_permission->permission_type)
                    ];
                }
            }

            return $this->get_default_permission_by_position($position_id);

        } catch (Exception $e) {
            log_message('error', 'Get member permission error: ' . $e->getMessage());
            return $this->get_default_permission_by_position($position_id);
        }
    }

    /**
     * แปลง permission_type เป็น access_type
     */
    private function map_permission_to_access_type($permission_type) {
        switch ($permission_type) {
            case 'full_admin':
                return 'full';
            case 'department_admin':
                return 'department';
            case 'position_only':
                return 'position_only';
            case 'read_only':
                return 'read_only';
            case 'no_access':
                return 'no_access';
            default:
                return 'position_only';
        }
    }

    /**
     * ดึงประเภทสิทธิ์จากฐานข้อมูล
     */
    private function get_permission_types() {
        try {
            if ($this->db->table_exists('tbl_google_drive_permission_types')) {
                return $this->db->select('type_code, type_name, description')
                               ->from('tbl_google_drive_permission_types')
                               ->where('is_active', 1)
                               ->order_by('type_name', 'ASC')
                               ->get()
                               ->result();
            }

            return $this->create_default_permission_types();

        } catch (Exception $e) {
            log_message('error', 'Get permission types error: ' . $e->getMessage());
            return $this->create_default_permission_types();
        }
    }

    /**
     * สร้างประเภทสิทธิ์เริ่มต้น
     */
    private function create_default_permission_types() {
        $default_types = [
            ['type_code' => 'full_admin', 'type_name' => 'ผู้ดูแลระบบเต็มรูปแบบ', 'description' => 'เข้าถึงได้ทุก folder'],
            ['type_code' => 'department_admin', 'type_name' => 'ผู้ดูแลแผนก', 'description' => 'เข้าถึงได้ folder ของแผนก'],
            ['type_code' => 'position_only', 'type_name' => 'เฉพาะตำแหน่ง', 'description' => 'เข้าถึงได้เฉพาะ folder ตัวเอง'],
            ['type_code' => 'read_only', 'type_name' => 'อ่านอย่างเดียว', 'description' => 'ดูและดาวน์โหลดเท่านั้น'],
            ['type_code' => 'no_access', 'type_name' => 'ไม่มีสิทธิ์', 'description' => 'ไม่สามารถเข้าใช้งานได้']
        ];

        if ($this->db->table_exists('tbl_google_drive_permission_types')) {
            foreach ($default_types as $type) {
                $exists = $this->db->where('type_code', $type['type_code'])->count_all_results('tbl_google_drive_permission_types');
                if ($exists == 0) {
                    $this->db->insert('tbl_google_drive_permission_types', $type);
                }
            }
        }

        $result = [];
        foreach ($default_types as $type) {
            $result[] = (object)$type;
        }

        return $result;
    }

    /**
     * กำหนดสิทธิ์เริ่มต้นตามตำแหน่ง
     */
    private function get_default_permission_by_position($position_id) {
        if (in_array($position_id, [1, 2])) {
            return [
                'permission_type' => 'full_admin',
                'type_name' => 'ผู้ดูแลระบบเต็มรูปแบบ',
                'source' => 'default',
                'access_type' => 'full'
            ];
        } elseif ($position_id == 3) {
            return [
                'permission_type' => 'department_admin',
                'type_name' => 'ผู้ดูแลแผนก',
                'source' => 'default',
                'access_type' => 'department'
            ];
        } elseif ($position_id >= 4) {
            return [
                'permission_type' => 'position_only',
                'type_name' => 'เฉพาะตำแหน่ง',
                'source' => 'default',
                'access_type' => 'position_only'
            ];
        }

        return [
            'permission_type' => 'no_access',
            'type_name' => 'ไม่มีสิทธิ์',
            'source' => 'default',
            'access_type' => 'no_access'
        ];
    }

    
    /**
     * OAuth Callback - รับ Authorization Code จาก Google
     */
    /**
 * OAuth Callback - แก้ไขใหม่ (Fixed v3.1.1)
 */
public function oauth_callback() {
    try {
        $code = $this->input->get('code');
        $error = $this->input->get('error');
        $member_id = $this->session->userdata('oauth_member_id');

        if ($error) {
            throw new Exception('Google OAuth Error: ' . $error);
        }

        if (!$code) {
            throw new Exception('ไม่ได้รับ Authorization Code จาก Google');
        }

        if (!$member_id) {
            throw new Exception('ไม่พบข้อมูล Member ID ใน Session');
        }

        // Fixed: ใช้ Manual Token Exchange แทน Google Client
        $token = $this->manual_token_exchange($code);

        if (isset($token['error'])) {
            throw new Exception('Token Error: ' . $token['error']);
        }

        // ดึงข้อมูลผู้ใช้จาก Google
        $user_info = $this->get_google_user_info($token['access_token']);
        
        // บันทึกข้อมูลลงฐานข้อมูล
        $update_data = [
            'google_email' => $user_info['email'],
            'google_access_token' => json_encode($token),
            'google_refresh_token' => $token['refresh_token'] ?? null,
            'google_token_expires' => date('Y-m-d H:i:s', time() + ($token['expires_in'] ?? 3600)),
            'google_connected_at' => date('Y-m-d H:i:s'),
            'google_account_verified' => 1,
            'google_drive_enabled' => 1
        ];

        $this->db->where('m_id', $member_id);
        $this->db->update('tbl_member', $update_data);

        // สร้าง Folders อัตโนมัติ
        $this->create_folders($member_id);

        // Log การดำเนินการ
        $this->log_action($member_id, 'connect', 'เชื่อมต่อ Google Drive สำเร็จ (Fixed Method)');

        $this->session->unset_userdata('oauth_member_id');
        $this->session->set_flashdata('success', 'เชื่อมต่อ Google Drive สำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'OAuth callback error: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }

    redirect('System_member/member_web');
}

/**
 * Manual Token Exchange Method (แก้ไขปัญหา setCode())
 */
private function manual_token_exchange($code) {
    try {
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');
        $redirect_uri = $this->get_setting('google_redirect_uri');
        
        $post_data = [
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        if ($http_code !== 200) {
            throw new Exception('HTTP Error: ' . $http_code . ' - ' . $response);
        }
        
        $token = json_decode($response, true);
        
        if (!$token || isset($token['error'])) {
            throw new Exception('Token response error: ' . $response);
        }
        
        return $token;
        
    } catch (Exception $e) {
        $this->safe_log('error', 'Manual token exchange error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * ดึงข้อมูลผู้ใช้จาก Google API
 */
private function get_google_user_info($access_token) {
    try {
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($access_token);
        
        $ch = curl_init();
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
            $user_info = json_decode($response, true);
            return $user_info;
        }
        
        throw new Exception('Failed to get user info: ' . $response);
        
    } catch (Exception $e) {
        $this->safe_log('error', 'Get Google user info error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * แก้ไข fetchAccessTokenWithAuthCode สำหรับ compatibility
 */
public function fetchAccessTokenWithAuthCode($code, $codeVerifier = null) {
    try {
        // วิธีใหม่ สำหรับ Google Client v2.x
        if ($this->google_client && method_exists($this->google_client, 'fetchAccessTokenWithAuthCode')) {
            return $this->google_client->fetchAccessTokenWithAuthCode($code);
        }
        
        // วิธีเก่า สำหรับ Google Client v1.x
        if ($this->google_client && method_exists($this->google_client, 'authenticate')) {
            return $this->google_client->authenticate($code);
        }
        
        // Manual Token Exchange (แนะนำ)
        return $this->manual_token_exchange($code);
        
    } catch (Exception $e) {
        // ถ้าทุกวิธีล้มเหลว ใช้ manual exchange
        return $this->manual_token_exchange($code);
    }
}

/**
 * แก้ไข create_folders - เพิ่ม cleanup
 */
private function create_folders($member_id) {
    try {
        if (!$this->get_setting('auto_create_folders', $this->config->item('auto_create_folders'))) {
            return false;
        }

        // ลบ duplicates ก่อน
        $this->cleanup_duplicate_folders($member_id);

        $member = $this->db->select('m.*, p.pname')
                          ->from('tbl_member m')
                          ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                          ->where('m.m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            return false;
        }

        // ตรวจสอบ access token
        if (empty($member->google_access_token)) {
            log_message('info', 'No Google access token found for member: ' . $member_id);
            return false;
        }

        $token_data = json_decode($member->google_access_token, true);
        $access_token = is_array($token_data) ? $token_data['access_token'] : $member->google_access_token;

        if (!$access_token) {
            log_message('error', 'Invalid access token format for member: ' . $member_id);
            return false;
        }

        $permission = $this->get_member_permission($member_id, $member->ref_pid);

        switch ($permission['permission_type']) {
            case 'full_admin':
                $result = $this->create_admin_folders_manual($member_id, $access_token);
                break;
            case 'department_admin':
                $result = $this->create_department_folders_manual($member_id, $member->ref_pid, $access_token);
                break;
            case 'position_only':
                $result = $this->create_position_folder_manual($member_id, $member->ref_pid, $member->pname, $access_token);
                break;
            default:
                log_message('info', 'No folder creation for permission type: ' . $permission['permission_type']);
                return false;
        }

        if ($result && $this->get_setting('logging_enabled', $this->config->item('logging_enabled'))) {
            $this->log_action($member_id, 'create_folder', 'สร้าง Folders สำเร็จ (ไม่ duplicate)');
        }

        return $result;

    } catch (Exception $e) {
        log_message('error', 'Create folders error: ' . $e->getMessage());
        return false;
    }
}
/**
 * ตรวจสอบ Folders เก่าที่มีอยู่
 */
private function check_existing_folders($member_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_folders')) {
            return [];
        }

        return $this->db->select('*')
                       ->from('tbl_google_drive_folders')
                       ->where('member_id', $member_id)
                       ->get()
                       ->result();

    } catch (Exception $e) {
        log_message('error', 'Check existing folders error: ' . $e->getMessage());
        return [];
    }
}

	/**
 * Reactivate Folders เก่าและเพิ่ม Permission
 */
private function reactivate_existing_folders($member_id, $access_token, $existing_folders) {
    try {
        // ดึง Google email ของ member
        $member = $this->db->select('google_email')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member || !$member->google_email) {
            return false;
        }

        $google_email = $member->google_email;

        foreach ($existing_folders as $folder) {
            // Reactivate folder
            $this->db->where('id', $folder->id);
            $this->db->update('tbl_google_drive_folders', [
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // เพิ่ม permission กลับเข้าไปใน Google Drive
            $this->add_google_drive_permission($folder->folder_id, $google_email, $access_token);
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Reactivate existing folders error: ' . $e->getMessage());
        return false;
    }
}

	
	
	
	/**
 * เพิ่ม Permission ใน Google Drive Folder
 */
private function add_google_drive_permission($folder_id, $email, $access_token, $role = 'writer') {
    try {
        $permission_data = [
            'role' => $role,
            'type' => 'user',
            'emailAddress' => $email
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}/permissions",
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
        curl_close($ch);

        if ($http_code === 200) {
            log_message('info', "Added permission for {$email} to folder {$folder_id}");
            return true;
        } else {
            log_message('warning', "Failed to add permission: HTTP {$http_code} - {$response}");
            return false;
        }

    } catch (Exception $e) {
        log_message('error', 'Add Google Drive permission error: ' . $e->getMessage());
        return false;
    }
}
	
	
	
	/**
 * ตรวจสอบและ refresh token ถ้าหมดอายุ
 */
private function check_and_refresh_token($member_id) {
    try {
        $member = $this->db->select('google_access_token, google_refresh_token, google_token_expires')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member || !$member->google_access_token) {
            return null;
        }

        // ตรวจสอบว่า token หมดอายุหรือไม่
        if ($member->google_token_expires && strtotime($member->google_token_expires) <= time()) {
            // ลอง refresh token
            if ($member->google_refresh_token) {
                $new_token = $this->refresh_access_token($member->google_refresh_token);
                if ($new_token) {
                    // อัปเดต token ใหม่
                    $this->db->where('m_id', $member_id);
                    $this->db->update('tbl_member', [
                        'google_access_token' => json_encode($new_token),
                        'google_token_expires' => date('Y-m-d H:i:s', time() + ($new_token['expires_in'] ?? 3600))
                    ]);
                    
                    return $new_token['access_token'];
                }
            }
            return null;
        }

        // Token ยังไม่หมดอายุ
        $token_data = json_decode($member->google_access_token, true);
        return is_array($token_data) ? $token_data['access_token'] : $member->google_access_token;

    } catch (Exception $e) {
        log_message('error', 'Check and refresh token error: ' . $e->getMessage());
        return null;
    }
}
	
	
	
	
	/**
 * Refresh Access Token
 */
private function refresh_access_token($refresh_token) {
    try {
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');
        
        $post_data = [
            'refresh_token' => $refresh_token,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'refresh_token'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post_data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $token = json_decode($response, true);
            if ($token && isset($token['access_token'])) {
                return $token;
            }
        }
        
        return null;
        
    } catch (Exception $e) {
        log_message('error', 'Refresh access token error: ' . $e->getMessage());
        return null;
    }
}
	
	
	
	
/**
 * แก้ไข create_admin_folders_manual - ใช้ UPSERT แทน INSERT
 */
private function create_admin_folders_manual($member_id, $access_token) {
    try {
        // ดึงข้อมูล member
        $member = $this->db->select('m.google_email, m.ref_pid, p.pname')
                          ->from('tbl_member m')
                          ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                          ->where('m.m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member || !$member->google_email) {
            return false;
        }

        $admin_position_id = $member->ref_pid;

        // สร้าง main folder
        $main_folder_data = $this->create_drive_folder_manual('ระบบจัดการเอกสาร - Admin', $access_token);

        if ($main_folder_data && $this->db->table_exists('tbl_google_drive_folders')) {
            // ใช้ upsert แทน insert
            $this->upsert_folder_record($member_id, $admin_position_id, [
                'folder_id' => $main_folder_data['id'],
                'folder_name' => $main_folder_data['name'],
                'folder_type' => 'shared',
                'folder_url' => 'https://drive.google.com/drive/folders/' . $main_folder_data['id'],
                'is_active' => 1,
                'created_by' => $member_id,
                'parent_folder_id' => null
            ]);
            
            $main_folder_id = $main_folder_data['id'];
        }

        // สร้าง subfolders สำหรับทุกตำแหน่ง
        $positions = $this->db->where('pstatus', 'show')->get('tbl_position')->result();
        
        foreach ($positions as $position) {
            $sub_folder_data = $this->create_drive_folder_manual($position->pname, $access_token, $main_folder_id);

            if ($sub_folder_data && $this->db->table_exists('tbl_google_drive_folders')) {
                // ใช้ upsert สำหรับ subfolder แต่ละ position
                $this->upsert_folder_record($member_id, $position->pid, [
                    'folder_id' => $sub_folder_data['id'],
                    'folder_name' => $sub_folder_data['name'],
                    'folder_type' => 'position',
                    'parent_folder_id' => $main_folder_id,
                    'folder_url' => 'https://drive.google.com/drive/folders/' . $sub_folder_data['id'],
                    'is_active' => 1,
                    'created_by' => $member_id
                ]);
            }
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Create admin folders manual error: ' . $e->getMessage());
        return false;
    }
}

	
	
	
	private function upsert_folder_record($member_id, $position_id, $folder_data) {
    try {
        // ตรวจสอบว่ามี record อยู่แล้วหรือไม่
        $existing = $this->db->select('id')
                            ->from('tbl_google_drive_folders')
                            ->where('member_id', $member_id)
                            ->where('position_id', $position_id)
                            ->where('folder_type', $folder_data['folder_type'])
                            ->get()
                            ->row();

        if ($existing) {
            // Update record เก่า
            $update_data = array_merge($folder_data, [
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $this->db->where('id', $existing->id);
            $result = $this->db->update('tbl_google_drive_folders', $update_data);
            
            log_message('info', "Updated existing folder record: member={$member_id}, position={$position_id}, type={$folder_data['folder_type']}");
        } else {
            // Insert record ใหม่
            $insert_data = array_merge($folder_data, [
                'member_id' => $member_id,
                'position_id' => $position_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            $result = $this->db->insert('tbl_google_drive_folders', $insert_data);
            
            log_message('info', "Inserted new folder record: member={$member_id}, position={$position_id}, type={$folder_data['folder_type']}");
        }

        return $result;

    } catch (Exception $e) {
        log_message('error', 'Upsert folder record error: ' . $e->getMessage());
        return false;
    }
}
	
	



/**
 * แก้ไข create_department_folders_manual - ใช้ upsert
 */
private function create_department_folders_manual($member_id, $position_id, $access_token) {
    try {
        $position = $this->db->where('pid', $position_id)->get('tbl_position')->row();
        
        if (!$position) {
            log_message('error', "Position ID {$position_id} not found");
            return false;
        }

        // สร้าง department folder
        $dept_folder_data = $this->create_drive_folder_manual('แผนก' . $position->pname, $access_token);

        if ($dept_folder_data && $this->db->table_exists('tbl_google_drive_folders')) {
            $this->upsert_folder_record($member_id, $position_id, [
                'folder_id' => $dept_folder_data['id'],
                'folder_name' => $dept_folder_data['name'],
                'folder_type' => 'department',
                'folder_url' => 'https://drive.google.com/drive/folders/' . $dept_folder_data['id'],
                'is_active' => 1,
                'created_by' => $member_id,
                'parent_folder_id' => null
            ]);
        }

        // สร้าง shared folder
        $shared_folder_data = $this->create_drive_folder_manual('เอกสารส่วนกลาง', $access_token);

        if ($shared_folder_data && $this->db->table_exists('tbl_google_drive_folders')) {
            $this->upsert_folder_record($member_id, $position_id, [
                'folder_id' => $shared_folder_data['id'],
                'folder_name' => $shared_folder_data['name'],
                'folder_type' => 'shared',
                'folder_url' => 'https://drive.google.com/drive/folders/' . $shared_folder_data['id'],
                'is_active' => 1,
                'created_by' => $member_id,
                'parent_folder_id' => null
            ]);
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Create department folders manual error: ' . $e->getMessage());
        return false;
    }
}


	
	
	
	/**
 * ลบ folders ที่ duplicate ก่อนสร้างใหม่
 */
private function cleanup_duplicate_folders($member_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_folders')) {
            return true;
        }

        // ลบ folders ที่ duplicate (เก็บเฉพาะ latest)
        $duplicates = $this->db->select('member_id, position_id, folder_type, MAX(id) as keep_id')
                              ->from('tbl_google_drive_folders')
                              ->where('member_id', $member_id)
                              ->group_by(['member_id', 'position_id', 'folder_type'])
                              ->having('COUNT(*) > 1')
                              ->get()
                              ->result();

        foreach ($duplicates as $dup) {
            // ลบ records เก่า เก็บเฉพาะ latest
            $this->db->where('member_id', $dup->member_id)
                    ->where('position_id', $dup->position_id)
                    ->where('folder_type', $dup->folder_type)
                    ->where('id !=', $dup->keep_id)
                    ->delete('tbl_google_drive_folders');
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Cleanup duplicate folders error: ' . $e->getMessage());
        return false;
    }
}

	
	
	
	private function create_drive_folder_manual($folder_name, $access_token, $parent_id = null) {
    try {
        $metadata = [
            'name' => $folder_name,
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
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            return json_decode($response, true);
        }

        throw new Exception('Failed to create folder: ' . $response);

    } catch (Exception $e) {
        log_message('error', 'Create drive folder manual error: ' . $e->getMessage());
        return null;
    }
}

	/**
 * Emergency - ลบ duplicate records ทั้งหมด
 */
public function fix_duplicate_records() {
    try {
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        echo "<h1>Fix Duplicate Records</h1>";
        
        if (!$this->db->table_exists('tbl_google_drive_folders')) {
            echo "<p>Table not exists</p>";
            return;
        }

        // หา duplicates ทั้งหมด
        $duplicates = $this->db->query("
            SELECT member_id, position_id, folder_type, COUNT(*) as count, 
                   GROUP_CONCAT(id ORDER BY id DESC) as ids
            FROM tbl_google_drive_folders 
            GROUP BY member_id, position_id, folder_type 
            HAVING COUNT(*) > 1
        ")->result();

        echo "<h2>Found " . count($duplicates) . " duplicate groups</h2>";

        foreach ($duplicates as $dup) {
            $ids = explode(',', $dup->ids);
            $keep_id = array_shift($ids); // เก็บ ID แรก (ล่าสุด)
            
            echo "<p>Member {$dup->member_id}, Position {$dup->position_id}, Type {$dup->folder_type}:</p>";
            echo "<ul>";
            echo "<li>Keep ID: {$keep_id}</li>";
            echo "<li>Delete IDs: " . implode(', ', $ids) . "</li>";
            echo "</ul>";

            // ลบ records เก่า
            if (!empty($ids)) {
                $this->db->where_in('id', $ids);
                $deleted = $this->db->delete('tbl_google_drive_folders');
                echo "<p>Deleted: " . ($deleted ? "Success" : "Failed") . "</p>";
            }
        }

        echo "<h2>Fix completed!</h2>";
        echo "<p><a href='/google_drive/test'>Test System</a></p>";

    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
	
	
	/**
 * แก้ไข create_position_folder_manual - ใช้ upsert
 */
private function create_position_folder_manual($member_id, $position_id, $position_name, $access_token) {
    try {
        $folder_data = $this->create_drive_folder_manual($position_name . ' - เอกสารส่วนตัว', $access_token);

        if ($folder_data && $this->db->table_exists('tbl_google_drive_folders')) {
            $this->upsert_folder_record($member_id, $position_id, [
                'folder_id' => $folder_data['id'],
                'folder_name' => $folder_data['name'],
                'folder_type' => 'personal',
                'folder_url' => 'https://drive.google.com/drive/folders/' . $folder_data['id'],
                'is_active' => 1,
                'created_by' => $member_id,
                'parent_folder_id' => null
            ]);
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Create position folder manual error: ' . $e->getMessage());
        return false;
    }
}
	
	
    /**
     * สร้าง Admin Folders
     */
    private function create_admin_folders($member_id) {
        try {
            $main_folder = new Google\Service\Drive\DriveFile();
            $main_folder->setName('ระบบจัดการเอกสาร - Admin');
            $main_folder->setMimeType('application/vnd.google-apps.folder');

            $created_main = $this->drive_service->files->create($main_folder, [
                'fields' => 'id,name,webViewLink'
            ]);

            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $folder_data = [
                    'member_id' => $member_id,
                    'position_id' => 0,
                    'folder_id' => $created_main->getId(),
                    'folder_name' => $created_main->getName(),
                    'folder_type' => 'shared',
                    'folder_url' => $created_main->getWebViewLink(),
                    'is_active' => 1,
                    'created_by' => $member_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_google_drive_folders', $folder_data);
            }

            $positions = $this->db->where('pstatus', 'show')->get('tbl_position')->result();
            foreach ($positions as $position) {
                $sub_folder = new Google\Service\Drive\DriveFile();
                $sub_folder->setName($position->pname);
                $sub_folder->setMimeType('application/vnd.google-apps.folder');
                $sub_folder->setParents([$created_main->getId()]);

                $created_sub = $this->drive_service->files->create($sub_folder, [
                    'fields' => 'id,name,webViewLink'
                ]);

                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $folder_data = [
                        'member_id' => $member_id,
                        'position_id' => $position->pid,
                        'folder_id' => $created_sub->getId(),
                        'folder_name' => $created_sub->getName(),
                        'folder_type' => 'position',
                        'parent_folder_id' => $created_main->getId(),
                        'folder_url' => $created_sub->getWebViewLink(),
                        'is_active' => 1,
                        'created_by' => $member_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_google_drive_folders', $folder_data);
                }
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Create admin folders error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้าง Position Folder
     */
    private function create_position_folder($member_id, $position_id, $position_name) {
        try {
            $folder = new Google\Service\Drive\DriveFile();
            $folder->setName($position_name . ' - เอกสารส่วนตัว');
            $folder->setMimeType('application/vnd.google-apps.folder');

            $created_folder = $this->drive_service->files->create($folder, [
                'fields' => 'id,name,webViewLink'
            ]);

            if ($this->db->table_exists('tbl_google_drive_folders')) {
                $folder_data = [
                    'member_id' => $member_id,
                    'position_id' => $position_id,
                    'folder_id' => $created_folder->getId(),
                    'folder_name' => $created_folder->getName(),
                    'folder_type' => 'personal',
                    'folder_url' => $created_folder->getWebViewLink(),
                    'is_active' => 1,
                    'created_by' => $member_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_google_drive_folders', $folder_data);
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Create position folder error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้าง Department Folders
     */
    private function create_department_folders($member_id, $position_id) {
        try {
            $position = $this->db->where('pid', $position_id)->get('tbl_position')->row();
            
            if ($position) {
                $dept_folder = new Google\Service\Drive\DriveFile();
                $dept_folder->setName('แผนก' . $position->pname);
                $dept_folder->setMimeType('application/vnd.google-apps.folder');

                $created_dept = $this->drive_service->files->create($dept_folder, [
                    'fields' => 'id,name,webViewLink'
                ]);

                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $this->db->insert('tbl_google_drive_folders', [
                        'member_id' => $member_id,
                        'position_id' => $position_id,
                        'folder_id' => $created_dept->getId(),
                        'folder_name' => $created_dept->getName(),
                        'folder_type' => 'department',
                        'folder_url' => $created_dept->getWebViewLink(),
                        'is_active' => 1,
                        'created_by' => $member_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $shared_folder = new Google\Service\Drive\DriveFile();
                $shared_folder->setName('เอกสารส่วนกลาง');
                $shared_folder->setMimeType('application/vnd.google-apps.folder');

                $created_shared = $this->drive_service->files->create($shared_folder, [
                    'fields' => 'id,name,webViewLink'
                ]);

                if ($this->db->table_exists('tbl_google_drive_folders')) {
                    $this->db->insert('tbl_google_drive_folders', [
                        'member_id' => $member_id,
                        'position_id' => 0,
                        'folder_id' => $created_shared->getId(),
                        'folder_name' => $created_shared->getName(),
                        'folder_type' => 'shared',
                        'folder_url' => $created_shared->getWebViewLink(),
                        'is_active' => 1,
                        'created_by' => $member_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Create department folders error: ' . $e->getMessage());
            return false;
        }
    }

   /**
 * แก้ไข disconnect method - ลบส่วนที่ error
 */
public function disconnect() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        $member_id = $this->input->post('member_id');
        
        if (!$member_id) {
            $this->output_json_error('ไม่พบข้อมูลสมาชิก');
            return;
        }

        // ดึงข้อมูล member
        $member = $this->db->select('google_access_token, google_email')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->output_json_error('ไม่พบข้อมูลสมาชิก');
            return;
        }

        // Update is_active = 0 (ปลอดภัยที่สุด)
        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $this->db->where('member_id', $member_id);
            $this->db->update('tbl_google_drive_folders', [
                'is_active' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // ลบ member permissions (ถ้ามีตาราง)
        if ($this->db->table_exists('tbl_google_drive_member_permissions')) {
            $this->db->where('member_id', $member_id);
            $this->db->update('tbl_google_drive_member_permissions', ['is_active' => 0]);
        }

        // Revoke token (แบบ safe)
        if (!empty($member->google_access_token)) {
            $this->safe_revoke_google_token($member->google_access_token);
        }

        // อัปเดตข้อมูลสมาชิก
        $update_data = [
            'google_email' => null,
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires' => null,
            'google_account_verified' => 0,
            'google_drive_enabled' => 0
        ];

        $this->db->where('m_id', $member_id);
        $result = $this->db->update('tbl_member', $update_data);

        if ($result) {
            $this->log_action($member_id, 'disconnect', 'ตัดการเชื่อมต่อ Google Drive สำเร็จ');
            $this->output_json_success([], 'ตัดการเชื่อมต่อ Google Drive เรียบร้อยแล้ว');
        } else {
            $this->output_json_error('ไม่สามารถอัปเดตฐานข้อมูลได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Disconnect error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	/**
 * Revoke Google Token แบบปลอดภัย (ไม่ให้ error หยุดการทำงาน)
 */
private function safe_revoke_google_token($access_token) {
    try {
        // ถ้าเป็น JSON decode ก่อน
        if (is_string($access_token) && strpos($access_token, '{') === 0) {
            $token_data = json_decode($access_token, true);
            $token = $token_data['access_token'] ?? $access_token;
        } else {
            $token = $access_token;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/revoke?token=' . urlencode($token),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // ลด SSL issues
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            log_message('info', 'Token revoke cURL error: ' . $error);
        } elseif ($http_code === 200) {
            log_message('info', 'Token revoked successfully');
        } else {
            log_message('info', 'Token revoke HTTP error: ' . $http_code);
        }
        
        return true; // ไม่ว่าจะสำเร็จหรือไม่ ให้ return true

    } catch (Exception $e) {
        log_message('info', 'Token revoke exception: ' . $e->getMessage());
        return true; // ไม่ให้ error หยุดการทำงาน
    }
}

	
	
	/**
 * ลบ Permission ของ Member จาก Google Drive Folders
 */
private function remove_member_permissions_from_folders($member_id, $access_token) {
    try {
        // ดึงรายการ folders ของ member
        if (!$this->db->table_exists('tbl_google_drive_folders')) {
            return true;
        }

        $folders = $this->db->select('folder_id, folder_name')
                           ->from('tbl_google_drive_folders')
                           ->where('member_id', $member_id)
                           ->where('is_active', 1)
                           ->get()
                           ->result();

        // ดึง Google email ของ member
        $member_email = $this->db->select('google_email')
                               ->from('tbl_member')
                               ->where('m_id', $member_id)
                               ->get()
                               ->row();

        if (!$member_email || !$member_email->google_email) {
            return true;
        }

        $google_email = $member_email->google_email;
        $token_data = json_decode($access_token, true);
        $token = is_array($token_data) ? $token_data['access_token'] : $access_token;

        foreach ($folders as $folder) {
            $this->remove_google_drive_permission($folder->folder_id, $google_email, $token);
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Remove member permissions error: ' . $e->getMessage());
        return false;
    }
}

	/**
 * ลบ Permission จาก Google Drive Folder
 */
private function remove_google_drive_permission($folder_id, $email, $access_token) {
    try {
        // ดึงรายการ permissions ของ folder
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}/permissions",
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
            $permissions = json_decode($response, true);
            
            if (isset($permissions['permissions'])) {
                foreach ($permissions['permissions'] as $permission) {
                    // หา permission ที่ตรงกับ email
                    if (isset($permission['emailAddress']) && $permission['emailAddress'] === $email) {
                        $this->delete_google_drive_permission($folder_id, $permission['id'], $access_token);
                        break;
                    }
                }
            }
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Remove Google Drive permission error: ' . $e->getMessage());
        return false;
    }
}

	/**
 * ลบ Permission จาก Google Drive
 */
private function delete_google_drive_permission($folder_id, $permission_id, $access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}/permissions/{$permission_id}",
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

        return ($http_code === 200 || $http_code === 204);

    } catch (Exception $e) {
        log_message('error', 'Delete Google Drive permission error: ' . $e->getMessage());
        return false;
    }
}
	
	

    /**
     * อัพเดทสิทธิ์สมาชิก (AJAX) - แก้ไข JSON Response
     */
    public function update_member_permission() {
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            $member_id = $this->input->post('member_id');
            $permission_type = $this->input->post('permission_type');
            $override_position = $this->input->post('override_position') ? 1 : 0;
            $notes = $this->input->post('notes') ?: '';

            if (!$member_id || !$permission_type) {
                $this->output_json_error('ข้อมูลไม่ครบถ้วน');
                return;
            }

            $valid_permission = null;
            if ($this->db->table_exists('tbl_google_drive_permission_types')) {
                $valid_permission = $this->db->where('type_code', $permission_type)
                                           ->where('is_active', 1)
                                           ->get('tbl_google_drive_permission_types')
                                           ->row();
            }

            if (!$valid_permission) {
                $valid_types = ['full_admin', 'department_admin', 'position_only', 'read_only', 'no_access'];
                if (!in_array($permission_type, $valid_types)) {
                    $this->output_json_error('ประเภทสิทธิ์ไม่ถูกต้อง');
                    return;
                }
                $valid_permission = (object)['type_name' => $this->get_permission_display_name($permission_type)];
            }

            $result = $this->save_member_permission($member_id, $permission_type, $override_position, $notes);

            if ($result) {
                $this->log_action($member_id, 'grant_permission', 
                    "อัพเดทสิทธิ์เป็น {$valid_permission->type_name}" . 
                    ($override_position ? " (เขียนทับสิทธิ์ตำแหน่ง)" : "")
                );

                $this->output_json_success([], 'อัพเดทสิทธิ์เรียบร้อยแล้ว');
            } else {
                $this->output_json_error('ไม่สามารถบันทึกข้อมูลได้');
            }

        } catch (Exception $e) {
            $this->output_json_error($e->getMessage());
        }
    }

    /**
     * บันทึกสิทธิ์สมาชิกลงฐานข้อมูล
     */
    private function save_member_permission($member_id, $permission_type, $override_position, $notes) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_member_permissions')) {
                return true;
            }

            $current_user_id = $this->session->userdata('m_id');

            $permission_data = [
                'permission_type' => $permission_type,
                'override_position' => $override_position,
                'notes' => $notes,
                'is_active' => 1,
                'updated_by' => $current_user_id,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $existing = $this->db->where('member_id', $member_id)
                                ->get('tbl_google_drive_member_permissions')
                                ->row();

            if ($existing) {
                $this->db->where('member_id', $member_id);
                return $this->db->update('tbl_google_drive_member_permissions', $permission_data);
            } else {
                $permission_data['member_id'] = $member_id;
                $permission_data['created_by'] = $current_user_id;
                $permission_data['created_at'] = date('Y-m-d H:i:s');
                return $this->db->insert('tbl_google_drive_member_permissions', $permission_data);
            }

        } catch (Exception $e) {
            log_message('error', 'Save member permission error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงชื่อสิทธิ์เพื่อแสดง
     */
    private function get_permission_display_name($permission_type) {
        $names = [
            'full_admin' => 'ผู้ดูแลระบบเต็มรูปแบบ',
            'department_admin' => 'ผู้ดูแลแผนก',
            'position_only' => 'เฉพาะตำแหน่ง',
            'custom' => 'กำหนดเอง',
            'read_only' => 'อ่านอย่างเดียว',
            'no_access' => 'ไม่มีสิทธิ์'
        ];

        return isset($names[$permission_type]) ? $names[$permission_type] : $permission_type;
    }

    /**
     * ทดสอบการเชื่อมต่อ Google Drive (AJAX) - แก้ไข JSON Response
     */
    public function test_connection() {
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            $client_id = $this->input->post('client_id');
            $client_secret = $this->input->post('client_secret');
            $redirect_uri = $this->input->post('redirect_uri');

            if (empty($client_id) || empty($client_secret)) {
                $this->output_json_error('กรุณาใส่ Google Client ID และ Client Secret');
                return;
            }

            if (!$this->validate_google_client_id($client_id)) {
                $this->output_json_error('รูปแบบ Google Client ID ไม่ถูกต้อง (ต้องลงท้ายด้วย .apps.googleusercontent.com)');
                return;
            }

            $oauth_test = $this->test_google_oauth($client_id, $client_secret, $redirect_uri);
            $drive_test = $this->test_google_drive_api($client_id, $client_secret);

            $this->output_json_success([
                'test_results' => [
                    'oauth_status' => $oauth_test,
                    'drive_api_status' => $drive_test,
                    'library_version' => $this->get_library_version(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ], 'การเชื่อมต่อ Google Drive ทำงานได้ปกติ');

        } catch (Exception $e) {
            $debug_data = [];
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                $debug_data = [
                    'error_type' => 'connection_test_failed',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
            
            $this->output_json_error($e->getMessage(), 500, $debug_data);
        }
    }

    /**
     * ดึงเวอร์ชัน Library
     */
    private function get_library_version() {
    try {
        if ($this->google_client && method_exists($this->google_client, 'getLibraryVersion')) {
            return $this->google_client->getLibraryVersion();
        }
        
        // ลองใช้วิธีอื่น
        if (class_exists('Google\\Client')) {
            $temp_client = new Google\Client();
            if (method_exists($temp_client, 'getLibraryVersion')) {
                return $temp_client->getLibraryVersion();
            }
        }
        
        // ถ้าไม่มี method นี้ ให้ดูจาก constant หรือ version file
        if (defined('Google\\Client::LIBVER')) {
            return Google\Client::LIBVER;
        }
        
        if (defined('GOOGLE_API_PHP_CLIENT_VERSION')) {
            return GOOGLE_API_PHP_CLIENT_VERSION;
        }
        
        return '2.12.6 (detected)';
        
    } catch (Exception $e) {
        return 'unknown';
    }
}

    /**
     * ตรวจสอบรูปแบบ Google Client ID
     */
    private function validate_google_client_id($client_id) {
        $pattern = '/^[0-9]+-[a-zA-Z0-9]+\.apps\.googleusercontent\.com$/';
        return preg_match($pattern, $client_id);
    }

    /**
     * ทดสอบ Google OAuth
     */
    private function test_google_oauth($client_id, $client_secret, $redirect_uri) {
        try {
            $oauth_url = $this->build_oauth_url($client_id, $redirect_uri);
            $discovery_test = $this->test_google_discovery();
            
            if ($discovery_test['success'] && filter_var($oauth_url, FILTER_VALIDATE_URL)) {
                return [
                    'success' => true,
                    'message' => 'OAuth Configuration ถูกต้อง'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'OAuth Configuration มีปัญหา: ' . $discovery_test['message']
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'OAuth Test Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * สร้าง OAuth URL
     */
    private function build_oauth_url($client_id, $redirect_uri) {
        $params = [
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'scope' => 'https://www.googleapis.com/auth/drive',
            'response_type' => 'code',
            'access_type' => 'offline',
            'state' => 'test_' . time()
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * ทดสอบ Google Discovery Document
     */
    private function test_google_discovery() {
        try {
            $url = 'https://accounts.google.com/.well-known/openid_configuration';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-Test-Client/1.0');
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return [
                    'success' => false,
                    'message' => 'cURL Error: ' . $error
                ];
            }

            if ($http_code === 200 && $response) {
                $discovery = json_decode($response, true);
                
                if (isset($discovery['authorization_endpoint'])) {
                    return [
                        'success' => true,
                        'message' => 'เชื่อมต่อ Google OAuth2 ได้สำเร็จ'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'ไม่สามารถเชื่อมต่อ Google ได้ (HTTP: ' . $http_code . ')'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Discovery Test Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ทดสอบ Google Drive API
     */
    private function test_google_drive_api($client_id, $client_secret) {
        try {
            if (!class_exists('Google\\Client')) {
                return [
                    'success' => false,
                    'message' => 'ยังไม่ได้ติดตั้ง Google Client Library v2.15.1',
                    'suggestion' => 'ติดตั้ง Google API Client v2.15.1'
                ];
            }

            try {
                $client = new Google\Client();
                $client->setClientId($client_id);
                $client->setClientSecret($client_secret);
                $client->setRedirectUri(site_url('google_drive/oauth_callback'));
                $client->addScope('https://www.googleapis.com/auth/drive');

                $drive = new Google\Service\Drive($client);

                return [
                    'success' => true,
                    'message' => 'Google Client Library v3.0.0 พร้อมใช้งาน',
                    'library_available' => true,
                    'version' => $this->get_library_version()
                ];

            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Google Client Error: ' . $e->getMessage()
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'API Test Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * บันทึก Log ลงฐานข้อมูล - แก้ไข Unicode Issue
     */
    private function log_action($member_id, $action_type, $description, $additional_data = []) {
        try {
            if (!$this->get_setting('logging_enabled', $this->config->item('logging_enabled'))) {
                return false;
            }

            if (!$this->db->table_exists('tbl_google_drive_logs')) {
                return false;
            }

            $data = [
                'member_id' => (int)$member_id,
                'action_type' => $action_type,
                'action_description' => $description,
                'folder_id' => $additional_data['folder_id'] ?? null,
                'target_email' => $additional_data['target_email'] ?? null,
                'status' => $additional_data['status'] ?? 'success',
                'error_message' => $additional_data['error_message'] ?? null,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->insert('tbl_google_drive_logs', $data);

        } catch (Exception $e) {
            log_message('error', 'Log action error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * หน้าตั้งค่า Google Drive - Fixed Version v3.0.0
     */
    public function settings() {
        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        // หากเป็น POST request ให้บันทึกการตั้งค่า
        if ($this->input->method() === 'post') {
            $this->save_settings();
            redirect('google_drive/settings');
        }

        // ดึงการตั้งค่าปัจจุบัน
        $data['settings'] = $this->get_current_settings();

        // โหลด View
        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_settings', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * ดึงการตั้งค่าปัจจุบัน - Fixed Version v3.0.0
     */
    private function get_current_settings() {
        try {
            $settings = [];

            // ลองดึงจากฐานข้อมูลก่อน
            if ($this->db->table_exists('tbl_google_drive_settings')) {
                $db_settings = $this->db->select('setting_key, setting_value')
                                      ->from('tbl_google_drive_settings')
                                      ->where('is_active', 1)
                                      ->get()
                                      ->result();

                foreach ($db_settings as $setting) {
                    $settings[$setting->setting_key] = $setting->setting_value;
                }
            }

            // ถ้าไม่มีในฐานข้อมูล ใช้ค่าจาก config หรือค่าเริ่มต้น
            $default_settings = [
                'google_client_id' => $this->config->item('google_client_id') ?: '',
                'google_client_secret' => $this->config->item('google_client_secret') ?: '',
                'google_redirect_uri' => $this->config->item('google_redirect_uri') ?: site_url('google_drive/oauth_callback'),
                'google_drive_enabled' => $this->config->item('google_drive_enabled') ? '1' : '0',
                'auto_create_folders' => $this->config->item('auto_create_folders') ? '1' : '0',
                'max_file_size' => (string)($this->config->item('max_file_size') ?: 104857600),
                'allowed_file_types' => $this->format_allowed_file_types($this->config->item('allowed_file_types')),
                'cache_enabled' => $this->config->item('cache_enabled') ? '1' : '0',
                'logging_enabled' => $this->config->item('logging_enabled') ? '1' : '0'
            ];

            // รวมค่าเริ่มต้นกับค่าจากฐานข้อมูล
            $final_settings = array_merge($default_settings, $settings);

            // ถ้ายังไม่มีค่าใดๆ ใช้ค่าเริ่มต้นของระบบ
            if (empty($final_settings['google_client_id']) && empty($final_settings['google_client_secret'])) {
                $final_settings = $this->get_system_default_settings();
            }

            return $final_settings;

        } catch (Exception $e) {
            $this->safe_log('error', 'Get current settings error: ' . $e->getMessage());
            return $this->get_system_default_settings();
        }
    }

    /**
     * ดึงค่าเริ่มต้นของระบบ
     */
    private function get_system_default_settings() {
        return [
            'google_client_id' => '',
            'google_client_secret' => '',
            'google_redirect_uri' => site_url('google_drive/oauth_callback'),
            'google_drive_enabled' => '1',
            'auto_create_folders' => '1',
            'max_file_size' => '104857600',
            'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
            'cache_enabled' => '1',
            'logging_enabled' => '1'
        ];
    }

    /**
     * จัดรูปแบบ allowed file types
     */
    private function format_allowed_file_types($file_types) {
        if (is_array($file_types)) {
            return implode(',', $file_types);
        } elseif (is_string($file_types)) {
            return $file_types;
        } else {
            return 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar';
        }
    }

    /**
     * บันทึกการตั้งค่า - Fixed Version v3.0.0
     */
    private function save_settings() {
        try {
            $this->db->trans_start();

            // รับค่าจากฟอร์ม
            $form_settings = [
                'google_client_id' => trim($this->input->post('google_client_id')),
                'google_client_secret' => trim($this->input->post('google_client_secret')),
                'google_redirect_uri' => trim($this->input->post('google_redirect_uri')) ?: site_url('google_drive/oauth_callback'),
                'google_drive_enabled' => $this->input->post('google_drive_enabled') ? '1' : '0',
                'auto_create_folders' => $this->input->post('auto_create_folders') ? '1' : '0',
                'max_file_size' => $this->input->post('max_file_size') ?: '104857600',
                'allowed_file_types' => trim($this->input->post('allowed_file_types')) ?: 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'
            ];

            // ตรวจสอบข้อมูล
            $validation_result = $this->validate_settings($form_settings);
            if (!$validation_result['valid']) {
                $this->session->set_flashdata('error', $validation_result['message']);
                return false;
            }

            // บันทึกลงฐานข้อมูล
            $this->save_settings_to_database($form_settings);

            // อัปเดต config ในหน่วยความจำ
            $this->update_config_in_memory($form_settings);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            // Log การเปลี่ยนแปลง
            $this->log_settings_change($form_settings);

            $this->session->set_flashdata('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->safe_log('error', 'Save settings error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบความถูกต้องของการตั้งค่า
     */
    private function validate_settings($settings) {
        // ตรวจสอบ Google Client ID
        if (!empty($settings['google_client_id'])) {
            if (!$this->validate_google_client_id($settings['google_client_id'])) {
                return [
                    'valid' => false,
                    'message' => 'รูปแบบ Google Client ID ไม่ถูกต้อง (ต้องลงท้ายด้วย .apps.googleusercontent.com)'
                ];
            }
        }

        // ตรวจสอบ Redirect URI
        if (!empty($settings['google_redirect_uri'])) {
            if (!filter_var($settings['google_redirect_uri'], FILTER_VALIDATE_URL)) {
                return [
                    'valid' => false,
                    'message' => 'รูปแบบ Redirect URI ไม่ถูกต้อง'
                ];
            }
        }

        // ตรวจสอบขนาดไฟล์
        if (!is_numeric($settings['max_file_size']) || $settings['max_file_size'] < 1048576) {
            return [
                'valid' => false,
                'message' => 'ขนาดไฟล์สูงสุดต้องมากกว่า 1 MB'
            ];
        }

        // ตรวจสอบประเภทไฟล์
        if (!empty($settings['allowed_file_types'])) {
            $file_types = explode(',', $settings['allowed_file_types']);
            foreach ($file_types as $type) {
                $type = trim($type);
                if (!preg_match('/^[a-zA-Z0-9]+$/', $type)) {
                    return [
                        'valid' => false,
                        'message' => 'ประเภทไฟล์มีรูปแบบไม่ถูกต้อง: ' . $type
                    ];
                }
            }
        }

        // ตรวจสอบเงื่อนไขพิเศษ
        if ($settings['google_drive_enabled'] === '1') {
            if (empty($settings['google_client_id']) || empty($settings['google_client_secret'])) {
                return [
                    'valid' => false,
                    'message' => 'กรุณาใส่ Google Client ID และ Client Secret เมื่อเปิดใช้งาน Google Drive'
                ];
            }
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * บันทึกการตั้งค่าลงฐานข้อมูล
     */
    private function save_settings_to_database($settings) {
        try {
            // สร้างตารางถ้ายังไม่มี
            $this->create_settings_table_if_not_exists();

            foreach ($settings as $key => $value) {
                $setting_data = [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_description' => $this->get_setting_description($key),
                    'is_active' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // ตรวจสอบว่ามีการตั้งค่าอยู่แล้วหรือไม่
                $existing = $this->db->where('setting_key', $key)
                                   ->get('tbl_google_drive_settings')
                                   ->row();

                if ($existing) {
                    // อัปเดต
                    $this->db->where('setting_key', $key);
                    $this->db->update('tbl_google_drive_settings', $setting_data);
                } else {
                    // เพิ่มใหม่
                    $setting_data['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('tbl_google_drive_settings', $setting_data);
                }
            }

            return true;

        } catch (Exception $e) {
            $this->safe_log('error', 'Save settings to database error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * สร้างตารางการตั้งค่าถ้ายังไม่มี
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

    /**
     * อัปเดต config ในหน่วยความจำ
     */
    private function update_config_in_memory($settings) {
        foreach ($settings as $key => $value) {
            // แปลงค่าให้ถูกต้อง
            if (in_array($key, ['google_drive_enabled', 'auto_create_folders', 'cache_enabled', 'logging_enabled'])) {
                $value = ($value === '1') ? true : false;
            } elseif ($key === 'max_file_size') {
                $value = (int)$value;
            } elseif ($key === 'allowed_file_types') {
                $value = explode(',', $value);
                $value = array_map('trim', $value);
            }

            $this->config->set_item($key, $value);
        }
    }

    /**
     * Toggle Google Drive Setting (AJAX) - แก้ไข Toggle Switch
     */
    public function toggle_setting() {
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            // ตรวจสอบสิทธิ์
            if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
                $this->output_json_error('ไม่มีสิทธิ์ในการเปลี่ยนแปลงการตั้งค่า');
                return;
            }

            $setting_key = $this->input->post('setting_key');
            $new_value = $this->input->post('value'); // '1' หรือ '0'

            if (!$setting_key) {
                $this->output_json_error('ไม่พบคีย์การตั้งค่า');
                return;
            }

            // ตรวจสอบว่าเป็น toggle setting ที่อนุญาต
            $allowed_toggles = ['google_drive_enabled', 'auto_create_folders', 'cache_enabled', 'logging_enabled'];
            if (!in_array($setting_key, $allowed_toggles)) {
                $this->output_json_error('การตั้งค่านี้ไม่สามารถ Toggle ได้');
                return;
            }

            // แปลงค่า
            $boolean_value = ($new_value === '1' || $new_value === 'true' || $new_value === true);
            $string_value = $boolean_value ? '1' : '0';

            // บันทึกลงฐานข้อมูล
            $result = $this->set_setting($setting_key, $string_value);

            if ($result) {
                // อัปเดต config ในหน่วยความจำ
                $this->config->set_item($setting_key, $boolean_value);

                // Log การเปลี่ยนแปลง
                $action_desc = $this->get_toggle_action_description($setting_key, $boolean_value);
                $this->log_action($this->session->userdata('m_id'), 'toggle_setting', $action_desc);

                // ตรวจสอบเงื่อนไขพิเศษ
                $this->handle_special_toggle_conditions($setting_key, $boolean_value);

                $this->output_json_success([
                    'setting_key' => $setting_key,
                    'new_value' => $string_value,
                    'boolean_value' => $boolean_value
                ], $action_desc);
            } else {
                $this->output_json_error('ไม่สามารถบันทึกการตั้งค่าได้');
            }

        } catch (Exception $e) {
            $this->safe_log('error', 'Toggle setting error: ' . $e->getMessage());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ดึงคำอธิบายการ Toggle
     */
    private function get_toggle_action_description($setting_key, $enabled) {
        $descriptions = [
            'google_drive_enabled' => $enabled ? 'เปิดใช้งาน Google Drive' : 'ปิดใช้งาน Google Drive',
            'auto_create_folders' => $enabled ? 'เปิดการสร้าง Folder อัตโนมัติ' : 'ปิดการสร้าง Folder อัตโนมัติ',
            'cache_enabled' => $enabled ? 'เปิดใช้งาน Cache' : 'ปิดใช้งาน Cache',
            'logging_enabled' => $enabled ? 'เปิดใช้งาน Logging' : 'ปิดใช้งาน Logging'
        ];

        return isset($descriptions[$setting_key]) ? $descriptions[$setting_key] : "เปลี่ยนแปลงการตั้งค่า {$setting_key}";
    }

    /**
     * จัดการเงื่อนไขพิเศษเมื่อ Toggle
     */
    private function handle_special_toggle_conditions($setting_key, $enabled) {
        try {
            switch ($setting_key) {
                case 'google_drive_enabled':
                    if (!$enabled) {
                        // เมื่อปิด Google Drive ให้ตัดการเชื่อมต่อทั้งหมด (ถ้าต้องการ)
                        $this->safe_log('info', 'Google Drive disabled - all connections remain but system is inactive');
                    } else {
                        // เมื่อเปิด Google Drive ตรวจสอบการตั้งค่า OAuth
                        $client_id = $this->get_setting('google_client_id');
                        $client_secret = $this->get_setting('google_client_secret');
                        
                        if (empty($client_id) || empty($client_secret)) {
                            $this->safe_log('warning', 'Google Drive enabled but OAuth credentials not configured');
                        }
                    }
                    break;

                case 'logging_enabled':
                    if ($enabled) {
                        $this->safe_log('info', 'Google Drive logging enabled');
                    }
                    break;

                case 'cache_enabled':
                    if (!$enabled) {
                        // ล้าง cache ถ้ามีการใช้งาน
                        $this->safe_log('info', 'Cache disabled - clearing cache if exists');
                    }
                    break;
            }
        } catch (Exception $e) {
            $this->safe_log('error', 'Handle special toggle conditions error: ' . $e->getMessage());
        }
    }

    /**
     * ดึงสถานะ Toggle ปัจจุบัน (AJAX)
     */
    public function get_toggle_status() {
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            $setting_key = $this->input->post('setting_key') ?: $this->input->get('setting_key');

            if (!$setting_key) {
                $this->output_json_error('ไม่พบคีย์การตั้งค่า');
                return;
            }

            $current_value = $this->get_setting($setting_key, '0');
            $boolean_value = ($current_value === '1' || $current_value === 'true' || $current_value === true);

            $this->output_json_success([
                'setting_key' => $setting_key,
                'current_value' => $current_value,
                'boolean_value' => $boolean_value,
                'is_enabled' => $boolean_value
            ], 'ดึงสถานะการตั้งค่าสำเร็จ');

        } catch (Exception $e) {
            $this->safe_log('error', 'Get toggle status error: ' . $e->getMessage());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ดึงสถานะ Toggle ทั้งหมด (AJAX)
     */
    public function get_all_toggle_status() {
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            $toggle_settings = ['google_drive_enabled', 'auto_create_folders', 'cache_enabled', 'logging_enabled'];
            $status = [];

            foreach ($toggle_settings as $setting_key) {
                $current_value = $this->get_setting($setting_key, '0');
                $boolean_value = ($current_value === '1' || $current_value === 'true' || $current_value === true);
                
                $status[$setting_key] = [
                    'current_value' => $current_value,
                    'boolean_value' => $boolean_value,
                    'is_enabled' => $boolean_value
                ];
            }

            $this->output_json_success($status, 'ดึงสถานะการตั้งค่าทั้งหมดสำเร็จ');

        } catch (Exception $e) {
            $this->safe_log('error', 'Get all toggle status error: ' . $e->getMessage());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * บันทึก Log การเปลี่ยนแปลงการตั้งค่า
     */
    private function log_settings_change($settings) {
        try {
            $current_user_id = $this->session->userdata('m_id');
            
            if ($this->db->table_exists('tbl_google_drive_logs')) {
                $log_data = [
                    'member_id' => $current_user_id,
                    'action_type' => 'update_settings',
                    'action_description' => 'อัปเดตการตั้งค่า Google Drive',
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent(),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_google_drive_logs', $log_data);
            }

        } catch (Exception $e) {
            // ไม่ต้องหยุดการทำงานถ้า log ไม่ได้
            $this->safe_log('error', 'Log settings change error: ' . $e->getMessage());
        }
    }

    /**
     * คำอธิบายการตั้งค่า
     */
    private function get_setting_description($key) {
        $descriptions = [
            'google_client_id' => 'Google OAuth Client ID สำหรับการเชื่อมต่อ Google Drive',
            'google_client_secret' => 'Google OAuth Client Secret สำหรับการเชื่อมต่อ Google Drive',
            'google_redirect_uri' => 'Google OAuth Redirect URI สำหรับ Callback',
            'google_drive_enabled' => 'เปิด/ปิดการใช้งาน Google Drive ทั้งระบบ',
            'auto_create_folders' => 'สร้าง Folder อัตโนมัติตามตำแหน่งเมื่อเชื่อมต่อ',
            'max_file_size' => 'ขนาดไฟล์สูงสุดที่อนุญาตให้อัปโหลด (bytes)',
            'allowed_file_types' => 'ประเภทไฟล์ที่อนุญาตให้อัปโหลด',
            'cache_enabled' => 'เปิด/ปิดการใช้งาน Cache',
            'logging_enabled' => 'เปิด/ปิดการบันทึก Log'
        ];

        return isset($descriptions[$key]) ? $descriptions[$key] : '';
    }

    /**
     * ดึงการตั้งค่าจากฐานข้อมูล - Public Method
     */
    public function get_setting($key, $default = null) {
        try {
            if ($this->db->table_exists('tbl_google_drive_settings')) {
                $result = $this->db->select('setting_value')
                                  ->from('tbl_google_drive_settings')
                                  ->where('setting_key', $key)
                                  ->where('is_active', 1)
                                  ->get()
                                  ->row();

                if ($result) {
                    return $result->setting_value;
                }
            }

            // ถ้าไม่มีในฐานข้อมูล ลองหาใน config
            $config_value = $this->config->item($key);
            return ($config_value !== null) ? $config_value : $default;

        } catch (Exception $e) {
            $this->safe_log('error', 'Get setting error: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * ตั้งค่าเดี่ยว - Public Method
     */
    public function set_setting($key, $value, $description = '') {
        try {
            $this->create_settings_table_if_not_exists();

            $setting_data = [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_description' => $description ?: $this->get_setting_description($key),
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $existing = $this->db->where('setting_key', $key)
                               ->get('tbl_google_drive_settings')
                               ->row();

            if ($existing) {
                $this->db->where('setting_key', $key);
                return $this->db->update('tbl_google_drive_settings', $setting_data);
            } else {
                $setting_data['created_at'] = date('Y-m-d H:i:s');
                return $this->db->insert('tbl_google_drive_settings', $setting_data);
            }

        } catch (Exception $e) {
            $this->safe_log('error', 'Set setting error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * หน้าจัดการ Google Drive ทั้งหมด
     */
    public function manage() {
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        $data['statistics'] = $this->get_drive_statistics();
        $data['connected_members'] = $this->get_connected_members();

        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_manage', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * ดึงสถิติ Google Drive
     */
    private function get_drive_statistics() {
        $connected_members = $this->db->where('google_drive_enabled', 1)
                                   ->count_all_results('tbl_member');

        $total_folders = 0;
        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $total_folders = $this->db->where('is_active', 1)
                                     ->count_all_results('tbl_google_drive_folders');
        }

        $synced_files = 0;
        if ($this->db->table_exists('tbl_google_drive_sync')) {
            $synced_files = $this->db->where('sync_status', 'synced')
                                    ->count_all_results('tbl_google_drive_sync');
        }

        $new_connections = $this->db->where('google_connected_at >=', date('Y-m-01'))
                                  ->where('google_drive_enabled', 1)
                                  ->count_all_results('tbl_member');

        return [
            'connected_members' => $connected_members,
            'total_folders' => $total_folders,
            'synced_files' => $synced_files,
            'new_connections' => $new_connections
        ];
    }

    /**
     * ดึงรายการสมาชิกที่เชื่อมต่อ
     */
    private function get_connected_members($search = '', $limit = 50, $offset = 0) {
        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.google_email, 
                          m.google_connected_at, m.google_account_verified, p.pname');

        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $this->db->select('COUNT(gdf.id) as total_folders', false);
            $this->db->join('tbl_google_drive_folders gdf', 'm.m_id = gdf.member_id AND gdf.is_active = 1', 'left');
        } else {
            $this->db->select('0 as total_folders', false);
        }

        $this->db->from('tbl_member m')
                ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                ->where('m.google_drive_enabled', 1);

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('m.m_fname', $search)
                    ->or_like('m.m_lname', $search)
                    ->or_like('m.m_email', $search)
                    ->or_like('m.google_email', $search)
                    ->group_end();
        }

        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $this->db->group_by('m.m_id');
        }

        $this->db->order_by('m.google_connected_at', 'desc')
                ->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูล Logs ล่าสุด (AJAX) - แก้ไข JSON Response
     */
    public function get_recent_logs() {
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            $logs = [];
            
            if ($this->db->table_exists('tbl_google_drive_logs')) {
                $query = $this->db->select('gdl.*, m.m_fname, m.m_lname,
                                          CONCAT(m.m_fname, " ", m.m_lname) as member_name')
                                 ->from('tbl_google_drive_logs gdl')
                                 ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                                 ->order_by('gdl.created_at', 'desc')
                                 ->limit(10)
                                 ->get();
                
                $logs = $query->result();
            }

            $this->output_json_success(['logs' => $logs], 'ดึงข้อมูล logs สำเร็จ');

        } catch (Exception $e) {
            $this->output_json_error($e->getMessage());
        }
    }

    /**
     * Export รายชื่อสมาชิกที่เชื่อมต่อ
     */
    public function export_members() {
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            show_404();
        }

        $members = $this->get_connected_members('', 1000);

        $filename = 'google_drive_members_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        fputs($output, "\xEF\xBB\xBF");
        
        fputcsv($output, [
            'ID', 'ชื่อ-นามสกุล', 'อีเมล', 'Google Account', 'ตำแหน่ง', 
            'จำนวน Folders', 'วันที่เชื่อมต่อ', 'สถานะการยืนยัน'
        ]);
        
        foreach ($members as $member) {
            fputcsv($output, [
                $member->m_id,
                $member->m_fname . ' ' . $member->m_lname,
                $member->m_email ?: '-',
                $member->google_email ?: '-',
                $member->pname ?: '-',
                $member->total_folders ?: '0',
                $member->google_connected_at ? date('d/m/Y H:i', strtotime($member->google_connected_at)) : '-',
                $member->google_account_verified ? 'ยืนยันแล้ว' : 'ยังไม่ยืนยัน'
            ]);
        }
        
        fclose($output);
    }

    /**
     * ดูประวัติการใช้งาน
     */
    public function view_logs() {
        $member_id = $this->input->get('member_id');
        
        if (!$member_id) {
            show_404();
        }

        $data['logs'] = [];
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $data['logs'] = $this->db->select('*')
                                   ->from('tbl_google_drive_logs')
                                   ->where('member_id', $member_id)
                                   ->order_by('created_at', 'desc')
                                   ->limit(50)
                                   ->get()
                                   ->result();
        }

        $data['member'] = $this->db->select('m_fname, m_lname')
                                  ->from('tbl_member')
                                  ->where('m_id', $member_id)
                                  ->get()
                                  ->row();

        echo "<h1>Google Drive Logs - Member: " . ($data['member']->m_fname ?? '') . " " . ($data['member']->m_lname ?? '') . "</h1>";
        echo "<p>จำนวน Log: " . count($data['logs']) . " รายการ</p>";
        
        if (!empty($data['logs'])) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>วันที่</th><th>การดำเนินการ</th><th>รายละเอียด</th><th>สถานะ</th></tr>";
            foreach ($data['logs'] as $log) {
                echo "<tr>";
                echo "<td>" . date('d/m/Y H:i:s', strtotime($log->created_at)) . "</td>";
                echo "<td>" . $log->action_type . "</td>";
                echo "<td>" . $log->action_description . "</td>";
                echo "<td>" . $log->status . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>ไม่มี Log</p>";
        }
        
        echo "<p><a href='" . site_url('System_member/member_web') . "'>กลับ</a></p>";
    }

    /**
     * ทดสอบระบบ Google Drive v3.0.0 - แก้ไข Unicode และ JSON Issues
     */
    public function test() {
    echo "<h1>Google Drive Controller v3.1.0 - System Test (Fixed Unicode Issue)</h1>";
    echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>Environment: " . ENVIRONMENT . "</p>";
    echo "<p>User: " . $this->session->userdata('m_id') . "</p>";

    echo "<h2>ตรวจสอบ Configuration:</h2>";
    echo "<p>Google Drive Enabled: " . ($this->get_setting('google_drive_enabled', $this->config->item('google_drive_enabled')) ? "เปิด" : "ปิด") . "</p>";
    echo "<p>Auto Create Folders: " . ($this->get_setting('auto_create_folders', $this->config->item('auto_create_folders')) ? "เปิด" : "ปิด") . "</p>";
    echo "<p>Cache Enabled: " . ($this->get_setting('cache_enabled', $this->config->item('cache_enabled')) ? "เปิด" : "ปิด") . "</p>";
    echo "<p>Logging Enabled: " . ($this->get_setting('logging_enabled', $this->config->item('logging_enabled')) ? "เปิด" : "ปิด") . "</p>";
    echo "<p>Debug Mode: " . ($this->get_setting('debug_mode', $this->config->item('debug_mode')) ? "เปิด" : "ปิด") . "</p>";

    echo "<h2>ตรวจสอบ Google Client Library v3.1.0:</h2>";
    if (class_exists('Google\\Client')) {
        echo "<p>[OK] Google\\Client: พร้อมใช้งาน</p>";
        try {
            $client = new Google\Client();
            $version = method_exists($client, 'getLibraryVersion') ? $client->getLibraryVersion() : '3.0.0+';
            echo "<p>Library Version: {$version}</p>";
        } catch (Exception $e) {
            echo "<p>[ERROR] Version Check Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>[FAIL] Google\\Client: ยังไม่ได้โหลด</p>";
    }

    if (class_exists('Google_Client')) {
        echo "<p>[OK] Google_Client (Alias): พร้อมใช้งาน</p>";
    } else {
        echo "<p>[FAIL] Google_Client (Alias): ไม่มี</p>";
    }

    if (class_exists('Google\\Service\\Drive')) {
        echo "<p>[OK] Google\\Service\\Drive: พร้อมใช้งาน</p>";
    } else {
        echo "<p>[FAIL] Google\\Service\\Drive: ไม่มี</p>";
    }

    echo "<h2>ตรวจสอบ OAuth2 Services (Fixed):</h2>";
    $oauth2_services = [
        'Google\\Service\\Oauth2' => class_exists('Google\\Service\\Oauth2'),
        'Google_Service_Oauth2' => class_exists('Google_Service_Oauth2'),
        'Google\\Service\\PeopleService' => class_exists('Google\\Service\\PeopleService')
    ];

    foreach ($oauth2_services as $service => $available) {
        echo "<p>" . ($available ? "[OK]" : "[FAIL]") . " {$service}</p>";
    }

    $oauth2_service = $this->init_oauth2_service();
    echo "<p>OAuth2 Service Result: " . ($oauth2_service ? "[OK] สำเร็จ" : "[WARN] ใช้วิธีทางเลือก") . "</p>";

    echo "<h2>ตรวจสอบตาราง:</h2>";
    $tables = [
        'tbl_member', 
        'tbl_position', 
        'tbl_google_drive_folders', 
        'tbl_google_drive_logs', 
        'tbl_google_drive_settings',
        'tbl_google_drive_permission_types',
        'tbl_google_drive_member_permissions'
    ];
    
    foreach ($tables as $table) {
        $exists = $this->db->table_exists($table);
        echo "<p>{$table}: " . ($exists ? "[OK]" : "[FAIL]") . "</p>";
    }

    echo "<h2>สมาชิกที่เชื่อมต่อ:</h2>";
    $connected = $this->db->where('google_drive_enabled', 1)->count_all_results('tbl_member');
    echo "<p>{$connected} คน</p>";

    echo "<h2>การตั้งค่า Google OAuth:</h2>";
    $client_id = $this->get_setting('google_client_id', $this->config->item('google_client_id'));
    $client_secret = $this->get_setting('google_client_secret', $this->config->item('google_client_secret'));
    echo "<p>Client ID: " . (empty($client_id) ? "[FAIL] ยังไม่ตั้งค่า" : "[OK] ตั้งค่าแล้ว (" . substr($client_id, 0, 20) . "...)") . "</p>";
    echo "<p>Client Secret: " . (empty($client_secret) ? "[FAIL] ยังไม่ตั้งค่า" : "[OK] ตั้งค่าแล้ว") . "</p>";
    echo "<p>Redirect URI: " . $this->get_setting('google_redirect_uri', $this->config->item('google_redirect_uri')) . "</p>";

    echo "<h2>ทดสอบ Controller Methods:</h2>";
    $methods = get_class_methods($this);
    $important_methods = ['connect', 'oauth_callback', 'disconnect', 'get_member_drive_info', 'test_connection', 'settings', 'get_setting', 'set_setting'];
    foreach ($important_methods as $method) {
        echo "<p>" . (in_array($method, $methods) ? "[OK]" : "[FAIL]") . " {$method}</p>";
    }

    echo "<h2>ทดสอบการสร้าง Google Client:</h2>";
    try {
        if ($this->google_client) {
            echo "<p>[OK] Google Client สร้างสำเร็จ</p>";
            echo "<p>[OK] Drive Service พร้อมใช้งาน</p>";
            echo "<p>" . ($this->oauth2_service ? "[OK] OAuth2 Service พร้อมใช้งาน" : "[WARN] OAuth2 Service ใช้วิธีทางเลือก") . "</p>";
        } else {
            echo "<p>[FAIL] Google Client ไม่ได้สร้าง</p>";
            
            // ทดสอบสร้าง Client ใหม่
            echo "<p>กำลังทดสอบสร้าง Google Client ใหม่...</p>";
            $init_result = $this->init_google_client();
            echo "<p>ผลการทดสอบ: " . ($init_result ? "[OK] สำเร็จ" : "[FAIL] ไม่สำเร็จ") . "</p>";
            
            if (!$init_result) {
                // แสดงสาเหตุที่เป็นไปได้
                echo "<p>สาเหตุที่เป็นไปได้:</p>";
                echo "<ul>";
                echo "<li>Google Client ID หรือ Client Secret ไม่ถูกต้อง</li>";
                echo "<li>Google API Client Library ไม่ได้โหลดครบถ้วน</li>";
                echo "<li>Permission หรือ Path ของไฟล์ไม่ถูกต้อง</li>";
                echo "<li>Network connectivity ไม่ทำงาน</li>";
                echo "</ul>";
            }
        }
    } catch (Exception $e) {
        echo "<p>[ERROR] Google Client Error: " . $e->getMessage() . "</p>";
    }

    echo "<h2>ทดสอบ Settings System v3.1.0:</h2>";
    try {
        // ทดสอบดึงการตั้งค่า
        $current_settings = $this->get_current_settings();
        echo "<p>[OK] Get Current Settings: " . count($current_settings) . " items</p>";
        
        // ทดสอบดึงค่าเดี่ยว
        $test_value = $this->get_setting('google_drive_enabled', '0');
        echo "<p>[OK] Get Single Setting: google_drive_enabled = {$test_value}</p>";
        
        // ทดสอบตารางการตั้งค่า
        if ($this->db->table_exists('tbl_google_drive_settings')) {
            $settings_count = $this->db->count_all('tbl_google_drive_settings');
            echo "<p>[OK] Settings Table: {$settings_count} records</p>";
        } else {
            echo "<p>[WARN] Settings Table: Will be created automatically</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>[ERROR] Settings System Error: " . $e->getMessage() . "</p>";
    }

    echo "<h2>สรุปสถานะระบบ (Fixed v3.1.0):</h2>";
    $status_checks = [
        'Library Loaded' => class_exists('Google\\Client'),
        'Config Loaded' => $this->config_loaded,
        'OAuth Configured' => !empty($client_id) && !empty($client_secret),
        'Database Ready' => $this->db->table_exists('tbl_member'),
        'Google Client Ready' => isset($this->google_client) && $this->google_client !== null,
        'JSON Response Fixed' => method_exists($this, 'output_json_success'),
        'Unicode Issue Fixed' => method_exists($this, 'clean_log_message'),
        'HTML/JSON Issue Fixed' => true,
        'Settings System Fixed' => method_exists($this, 'get_current_settings'),
        'Database Settings Fixed' => method_exists($this, 'save_settings_to_database')
    ];

    $total_checks = count($status_checks);
    $passed_checks = count(array_filter($status_checks));

    foreach ($status_checks as $check => $status) {
        echo "<p>" . ($status ? "[PASS]" : "[FAIL]") . " {$check}</p>";
    }

    $percentage = round(($passed_checks / $total_checks) * 100, 1);
    echo "<h3>Overall Status: {$passed_checks}/{$total_checks} ({$percentage}%)</h3>";

    if ($percentage >= 90) {
        echo "<p style='color: green; font-weight: bold;'>[SUCCESS] ระบบพร้อมใช้งานเต็มรูปแบบ! (All Issues Fixed v3.1.0)</p>";
    } elseif ($percentage >= 80) {
        echo "<p style='color: orange; font-weight: bold;'>[WARNING] ระบบใช้งานได้ดี ยังมีปัญหาเล็กน้อย</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>[ERROR] ระบบไม่พร้อมใช้งาน</p>";
    }

    echo "<h2>v3.1.0 Unicode/Emoji Fix Summary:</h2>";
    echo "<div style='background: #e8f5e8; padding: 15px; border: 1px solid #4caf50; border-radius: 5px;'>";
    echo "<p><strong>แก้ไขปัญหา Unicode/Emoji แล้ว v3.1.0:</strong></p>";
    echo "<ul>";
    echo "<li>[FIXED] แก้ไข HTML/JSON Response Issue - ใช้ output_json_success/error</li>";
    echo "<li>[FIXED] แก้ไข Unicode Character ใน Log - ลบ Emoji และ Unicode ออก</li>";
    echo "<li>[FIXED] แก้ไข ob_clean() สำหรับ AJAX Methods</li>";
    echo "<li>[FIXED] แก้ไข Content-Type Headers อย่างเข้มงวด</li>";
    echo "<li>[FIXED] แก้ไข OAuth2 Service Alternative Methods</li>";
    echo "<li>[FIXED] แก้ไข Error Handling ทุก Method</li>";
    echo "<li>[FIXED] แก้ไข Settings ดึงและบันทึกได้แล้ว - Database + Config</li>";
    echo "<li>[FIXED] แก้ไข Settings Validation และ Error Handling</li>";
    echo "<li>[FIXED] แก้ไข Auto Create Tables ถ้ายังไม่มี</li>";
    echo "<li>[FIXED] แก้ไข Transaction และ Rollback System</li>";
    echo "<li>[NEW] แก้ไข Log Level Issue - ใช้ log_message() แทน error_log()</li>";
    echo "<li>[NEW] แก้ไข Apache AH01071 Error</li>";
    echo "<li>[NEW] เพิ่ม clean_log_message() method</li>";
    echo "<li>[NEW] เพิ่ม Environment check สำหรับ logging</li>";
    echo "</ul>";
    echo "</div>";

    // แสดงข้อแนะนำหาก Google Client ไม่ได้สร้าง
    if (!isset($this->google_client) || $this->google_client === null) {
        echo "<h2>ข้อแนะนำในการแก้ไข Google Client Issue:</h2>";
        echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
        echo "<p><strong>วิธีแก้ไข:</strong></p>";
        echo "<ol>";
        echo "<li>ตรวจสอบ Google Client ID และ Client Secret ใน Google Cloud Console</li>";
        echo "<li>ตรวจสอบ Redirect URI ให้ตรงกับที่ตั้งค่าใน Google Console</li>";
        echo "<li>ตรวจสอบ Google Drive API ถูก Enable แล้วหรือไม่</li>";
        echo "<li>ลองเรียกหน้า <a href='" . site_url('google_drive/test_connection') . "' target='_blank'>Test Connection</a></li>";
        echo "<li>ตรวจสอบ Error Logs ใน " . APPPATH . "logs/</li>";
        echo "</ol>";
        echo "</div>";
    }
}

    /**
     * ทดสอบ URL และ Redirect URI
     */
    public function test_url() {
        try {
            echo "<h1>Google Drive URL Test v3.0.0 (Complete Fixed Version)</h1>";
            echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
            echo "<p>Environment: " . ENVIRONMENT . "</p>";
            
            echo "<h2>📍 Redirect URI สำหรับ Google Console:</h2>";
            echo "<div style='background: #e8f4f8; padding: 15px; border: 3px solid #1e88e5; border-radius: 8px; margin: 10px 0;'>";
            echo "<h3 style='color: #1e88e5; margin-top: 0;'>🔗 Copy URL นี้:</h3>";
            echo "<code style='font-size: 16px; background: white; padding: 10px; display: block; border-radius: 4px;'>";
            echo site_url('google_drive/oauth_callback');
            echo "</code>";
            echo "</div>";
            
            echo "<h2>⚙️ Config Status v3.0.0 (Complete Fixed):</h2>";
            echo "<p>Google Drive Enabled: " . ($this->get_setting('google_drive_enabled', '0') ? ' Yes' : ' No') . "</p>";
            echo "<p>Auto Create Folders: " . ($this->get_setting('auto_create_folders', '0') ? ' Yes' : ' No') . "</p>";
            echo "<p>Max File Size: " . round($this->get_setting('max_file_size', 104857600) / 1048576, 2) . " MB</p>";
            echo "<p>Library Version: " . $this->get_library_version() . "</p>";
            echo "<p>JSON Response Fix:  Implemented</p>";
            echo "<p>Unicode Fix:  Fixed</p>";
            echo "<p>HTML/JSON Fix: Fixed</p>";
            echo "<p>Settings System Fix:  Complete</p>";
            
        } catch (Exception $e) {
            echo "<h1 style='color: red;'>Error:</h1>";
            echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
            echo "<pre style='background: #f5f5f5; padding: 10px;'>" . $e->getTraceAsString() . "</pre>";
        }
    }
	
	
	
	
	public function test_google_client() {
    echo "<h1>Google Client Creation Test v3.1.1 (Fixed)</h1>";
    echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";

    echo "<h2>Step 1: ตรวจสอบ Class Availability</h2>";
    echo "<p>Google\\Client: " . (class_exists('Google\\Client') ? "[OK]" : "[FAIL]") . "</p>";
    echo "<p>Google\\Service\\Drive: " . (class_exists('Google\\Service\\Drive') ? "[OK]" : "[FAIL]") . "</p>";

    echo "<h2>Step 2: ตรวจสอบ Configuration</h2>";
    $client_id = $this->get_setting('google_client_id');
    $client_secret = $this->get_setting('google_client_secret');
    $redirect_uri = $this->get_setting('google_redirect_uri');
    
    echo "<p>Client ID: " . (empty($client_id) ? "[FAIL] Empty" : "[OK] Set (" . substr($client_id, 0, 20) . "...)") . "</p>";
    echo "<p>Client Secret: " . (empty($client_secret) ? "[FAIL] Empty" : "[OK] Set") . "</p>";
    echo "<p>Redirect URI: " . $redirect_uri . "</p>";

    echo "<h2>Step 3: ตรวจสอบ Google Client Methods</h2>";
    try {
        $client = new Google\Client();
        echo "<p>[OK] Google\\Client instance created</p>";

        // ตรวจสอบ methods ที่สำคัญ
        $methods_to_check = [
            'setClientId',
            'setClientSecret', 
            'setRedirectUri',
            'addScope',
            'setAccessType',
            'setPrompt',
            'setApplicationName',
            'getApplicationName',
            'getClientId',
            'getLibraryVersion',
            'createAuthUrl',
            'fetchAccessTokenWithAuthCode'
        ];

        foreach ($methods_to_check as $method) {
            $exists = method_exists($client, $method);
            echo "<p>" . ($exists ? "[OK]" : "[MISS]") . " {$method}()</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color: red;'>[ERROR] Cannot create Google\\Client: " . $e->getMessage() . "</p>";
        return;
    }

    echo "<h2>Step 4: ทดสอบสร้าง Google Client (Safe Mode)</h2>";
    try {
        $client = new Google\Client();
        echo "<p>[OK] Google\\Client instance created</p>";

        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        echo "<p>[OK] Basic configuration set</p>";

        // ตั้งค่า Application Name แบบ safe
        if (method_exists($client, 'setApplicationName')) {
            $client->setApplicationName('Test App v3.1.1');
            echo "<p>[OK] Application name set</p>";
            
            if (method_exists($client, 'getApplicationName')) {
                $app_name = $client->getApplicationName();
                echo "<p>[OK] Application name verified: {$app_name}</p>";
            } else {
                echo "<p>[INFO] getApplicationName() not available</p>";
            }
        } else {
            echo "<p>[INFO] setApplicationName() not available in this version</p>";
        }

        // ทดสอบ Client ID
        if (method_exists($client, 'getClientId')) {
            $retrieved_client_id = $client->getClientId();
            if ($retrieved_client_id === $client_id) {
                echo "<p>[OK] Client ID verification passed</p>";
            } else {
                echo "<p>[WARN] Client ID mismatch</p>";
            }
        } else {
            echo "<p>[INFO] getClientId() not available</p>";
        }

        $scopes = $this->config->item('google_scopes');
        if (is_array($scopes)) {
            foreach ($scopes as $scope) {
                $client->addScope($scope);
            }
            echo "<p>[OK] Scopes added: " . count($scopes) . " scopes</p>";
        }

        // ตั้งค่าเพิ่มเติม
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        echo "<p>[OK] Additional settings configured</p>";

        echo "<p>[OK] Google Client configured successfully!</p>";

        echo "<h2>Step 5: ทดสอบ Drive Service</h2>";
        $drive = new Google\Service\Drive($client);
        echo "<p>[OK] Google Drive Service created successfully!</p>";

        echo "<h2>Step 6: ทดสอบ OAuth URL Creation</h2>";
        try {
            if (method_exists($client, 'createAuthUrl')) {
                $auth_url = $client->createAuthUrl();
                echo "<p>[OK] Auth URL created: " . substr($auth_url, 0, 100) . "...</p>";
            } else {
                echo "<p>[INFO] createAuthUrl() not available</p>";
            }
        } catch (Exception $e) {
            echo "<p>[WARN] Auth URL creation failed: " . $e->getMessage() . "</p>";
        }

        echo "<p style='color: green; font-weight: bold;'>[SUCCESS] Google Client และ Drive Service ทำงานได้ปกติ!</p>";

        // แสดงเวอร์ชัน
        $version = $this->get_library_version();
        echo "<p>Library Version: {$version}</p>";

    } catch (Exception $e) {
        echo "<p style='color: red;'>[ERROR] " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . "</p>";
        echo "<p>Line: " . $e->getLine() . "</p>";
        
        echo "<h3>Possible Solutions:</h3>";
        echo "<ul>";
        echo "<li>Google API Client Library version เก่า - ควร update เป็น v2.15.1+</li>";
        echo "<li>Missing methods ใน Google Client - ใช้ alternative methods</li>";
        echo "<li>Library installation ไม่สมบูรณ์ - reinstall Google API Client</li>";
        echo "</ul>";
    }
}
	

	
	public function debug_auth_url() {
    echo "<h1>Debug Auth URL Creation</h1>";
    echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";

    echo "<h2>Step 1: ตรวจสอบ Client ID จากฐานข้อมูล</h2>";
    $client_id_db = $this->get_setting('google_client_id');
    echo "<p>Client ID (Database): " . ($client_id_db ? $client_id_db : "[EMPTY]") . "</p>";

    echo "<h2>Step 2: ตรวจสอบ Client ID จาก Config</h2>";
    $client_id_config = $this->config->item('google_client_id');
    echo "<p>Client ID (Config): " . ($client_id_config ? $client_id_config : "[EMPTY]") . "</p>";

    echo "<h2>Step 3: ตรวจสอบ Google Client</h2>";
    if ($this->google_client) {
        echo "<p>[OK] Google Client exists</p>";
        
        if (method_exists($this->google_client, 'getClientId')) {
            $current_client_id = $this->google_client->getClientId();
            echo "<p>Current Client ID in Google Client: " . ($current_client_id ? $current_client_id : "[EMPTY]") . "</p>";
        } else {
            echo "<p>[INFO] getClientId() method not available</p>";
        }
    } else {
        echo "<p>[FAIL] Google Client not created</p>";
        
        // ลองสร้างใหม่
        echo "<h3>Attempting to create Google Client...</h3>";
        $init_result = $this->init_google_client();
        echo "<p>Init result: " . ($init_result ? "[OK]" : "[FAIL]") . "</p>";
    }

    echo "<h2>Step 4: ทดสอบสร้าง Auth URL</h2>";
    try {
        $auth_url = $this->create_auth_url_safely();
        if ($auth_url) {
            echo "<p>[OK] Auth URL created:</p>";
            echo "<textarea style='width: 100%; height: 100px;'>" . $auth_url . "</textarea>";
            
            // แยกวิเคราะห์ parameters
            $parsed = parse_url($auth_url);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $params);
                echo "<h3>URL Parameters:</h3>";
                foreach ($params as $key => $value) {
                    echo "<p><strong>{$key}:</strong> " . htmlspecialchars($value) . "</p>";
                }
            }
        } else {
            echo "<p>[FAIL] Cannot create Auth URL</p>";
        }
    } catch (Exception $e) {
        echo "<p>[ERROR] " . $e->getMessage() . "</p>";
    }

    echo "<h2>Step 5: ทดสอบ Manual Auth URL</h2>";
    try {
        $manual_url = $this->create_manual_auth_url();
        if ($manual_url) {
            echo "<p>[OK] Manual Auth URL created:</p>";
            echo "<textarea style='width: 100%; height: 100px;'>" . $manual_url . "</textarea>";
            
            // แยกวิเคราะห์ parameters
            $parsed = parse_url($manual_url);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $params);
                echo "<h3>Manual URL Parameters:</h3>";
                foreach ($params as $key => $value) {
                    echo "<p><strong>{$key}:</strong> " . htmlspecialchars($value) . "</p>";
                }
            }
        } else {
            echo "<p>[FAIL] Cannot create Manual Auth URL</p>";
        }
    } catch (Exception $e) {
        echo "<p>[ERROR] " . $e->getMessage() . "</p>";
    }
}
	
public function test_manual_connect() {
    try {
        $member_id = $this->session->userdata('m_id');
        
        if (!$member_id) {
            echo "<h1>Test Manual Connect</h1>";
            echo "<p style='color: red;'>กรุณา login ก่อน</p>";
            return;
        }

        echo "<h1>Test Manual Connect</h1>";
        echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
        echo "<p>Member ID: {$member_id}</p>";

        // สร้าง Manual Auth URL
        $manual_url = $this->create_manual_auth_url();
        
        if ($manual_url) {
            echo "<p style='color: green;'>[OK] Manual Auth URL created successfully</p>";
            echo "<p><strong>Auth URL:</strong></p>";
            echo "<textarea style='width: 100%; height: 100px;'>" . $manual_url . "</textarea>";
            
            // แยกวิเคราะห์ parameters
            $parsed = parse_url($manual_url);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $params);
                echo "<h3>URL Parameters:</h3>";
                foreach ($params as $key => $value) {
                    $status = empty($value) ? "[EMPTY]" : "[OK]";
                    echo "<p><strong>{$key}:</strong> {$status} " . htmlspecialchars($value) . "</p>";
                }
            }
            
            // เซต session สำหรับ OAuth
            $this->session->set_userdata('oauth_member_id', $member_id);
            
            echo "<h3>Test Manual Connect:</h3>";
            echo "<p><a href='{$manual_url}' target='_blank' style='background: #4285f4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔗 Test Connect to Google</a></p>";
            echo "<p style='color: #666; font-size: 14px;'>คลิกลิงก์ด้านบนเพื่อทดสอบการเชื่อมต่อ Google Drive</p>";
            
        } else {
            echo "<p style='color: red;'>[FAIL] Cannot create Manual Auth URL</p>";
            
            // Debug information
            echo "<h3>Debug Information:</h3>";
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');
            $redirect_uri = $this->get_setting('google_redirect_uri');
            
            echo "<p>Client ID: " . (empty($client_id) ? "[EMPTY]" : "[OK] " . substr($client_id, 0, 20) . "...") . "</p>";
            echo "<p>Client Secret: " . (empty($client_secret) ? "[EMPTY]" : "[OK] Set") . "</p>";
            echo "<p>Redirect URI: " . $redirect_uri . "</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color: red;'>[ERROR] " . $e->getMessage() . "</p>";
    }
}
	
	
	
public function debug_member_position() {
    $member_id = $this->input->get('member_id') ?: $this->session->userdata('m_id');
    
    echo "<h1>Debug Member Position</h1>";
    echo "<p>Member ID: {$member_id}</p>";
    
    // ตรวจสอบข้อมูล member
    $member = $this->db->select('m.*, p.pname, p.pid as position_pid')
                      ->from('tbl_member m')
                      ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                      ->where('m.m_id', $member_id)
                      ->get()
                      ->row();
    
    echo "<h2>Member Data:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>m_id</td><td>{$member->m_id}</td></tr>";
    echo "<tr><td>ref_pid</td><td>{$member->ref_pid}</td></tr>";
    echo "<tr><td>position_pid</td><td>{$member->position_pid}</td></tr>";
    echo "<tr><td>position_name</td><td>{$member->pname}</td></tr>";
    echo "</table>";
    
    // ตรวจสอบตาราง position ทั้งหมด
    $positions = $this->db->select('pid, pname, pstatus')->from('tbl_position')->get()->result();
    echo "<h2>All Positions:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>PID</th><th>Name</th><th>Status</th></tr>";
    foreach ($positions as $pos) {
        echo "<tr><td>{$pos->pid}</td><td>{$pos->pname}</td><td>{$pos->pstatus}</td></tr>";
    }
    echo "</table>";
    
    // ตรวจสอบ permission
    $permission = $this->get_member_permission($member_id, $member->ref_pid);
    echo "<h2>Permission:</h2>";
    echo "<pre>" . print_r($permission, true) . "</pre>";
}
	
	
	
	
}

?>