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
    
    // เฉพาะ method ที่ไม่ต้องตรวจสอบ login
    $safe_methods = [
        'test_url', 'test_simple', 'test', 'debug_installation', 
        'test_google_client', 'test_recent_logs', 'debug_logs'
    ];
    
    $method = $this->router->fetch_method();
    if (in_array($method, $safe_methods)) {
        $this->safe_load_config();
        return;
    }
    
    // โหลด Google Drive Config
    $this->safe_load_config();
    
    // โหลด Models ที่จำเป็น
    $this->load->model('Google_drive_model');
    $this->load->model('Google_drive_permissions_model');
    
    // AJAX methods ที่ต้องตรวจสอบ login
    $ajax_methods = [
        'get_member_drive_info', 'update_member_permission', 'disconnect', 
        'test_connection', 'get_setting_ajax', 'set_setting_ajax', 
        'toggle_setting', 'toggle_storage_mode', 'grant_storage_access',
        'migrate_to_centralized', 'check_system_storage_setup', 
        'get_recent_logs' // เพิ่มตรงนี้
    ];
    
    if (in_array($method, $ajax_methods)) {
        if (!$this->session->userdata('m_id')) {
            // สำหรับ AJAX ที่ไม่ได้ login ให้ส่ง JSON error
            $this->force_json_error('Please login first', 401);
            return;
        }
    }
    
    // ตรวจสอบ login สำหรับ methods อื่น
    if (!in_array($method, ['oauth_callback']) && !$this->session->userdata('m_id')) {
        if ($this->input->is_ajax_request()) {
            $this->force_json_error('Please login first', 401);
            return;
        } else {
            redirect('User');
        }
    }

    // กำหนดค่าเริ่มต้นสำหรับ Google Drive
    $this->google_config = [
        'mode' => $this->get_setting('system_storage_mode', 'user_based'),
        'oauth_ready' => false,
        'alternative_oauth' => true
    ];

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
    // ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // ตั้งค่า headers ให้ถูกต้อง
    $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_header('Cache-Control: no-cache, must-revalidate')
        ->set_header('Pragma: no-cache')
        ->set_header('X-Content-Type-Options: nosniff')
        ->set_output(json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    
    exit; // สำคัญ: หยุดการทำงานทันที
}

    /**
     * Output JSON Error แบบปลอดภัย - แก้ไข HTML/JSON Issue
     */
    private function output_json_error($message = 'Error', $status_code = 400, $debug_data = []) {
    // ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
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
    
    // ตั้งค่า headers ให้ถูกต้อง
    $this->output
        ->set_status_header($status_code)
        ->set_content_type('application/json', 'utf-8')
        ->set_header('Cache-Control: no-cache, must-revalidate')
        ->set_header('Pragma: no-cache')
        ->set_header('X-Content-Type-Options: nosniff')
        ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    
    exit; // สำคัญ: หยุดการทำงานทันที
}


   /**
 * 🔄 แก้ไข Safe Load Config
 */
private function safe_load_config() {
    try {
        if (!$this->config_loaded) {
            // โหลด config แบบปลอดภัย
            if (file_exists(APPPATH . 'config/google_drive.php')) {
                $this->config->load('google_drive');
            }
            $this->config_loaded = true;
            
            log_message('info', 'Google Drive Config loaded successfully');
        }
    } catch (Exception $e) {
        log_message('error', 'Google Drive Config Load Error: ' . $e->getMessage());
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
     * เริ่มต้น Google Client v3.1.1 (แก้ไข)
     */
    private function init_google_client() {
        try {
            if (!$this->load_google_library()) {
                log_message('error', 'Google Client Library not available');
                return false;
            }

            if (!$this->get_setting('google_drive_enabled', $this->config->item('google_drive_enabled'))) {
                log_message('info', 'Google Drive is disabled in configuration');
                return false;
            }

            // ตรวจสอบการตั้งค่า OAuth
            $validation = $this->validate_oauth_settings();
            if (!$validation['valid']) {
                log_message('error', 'OAuth settings validation failed: ' . implode(', ', $validation['errors']));
                return false;
            }

            $client_id = $this->get_setting('google_client_id', $this->config->item('google_client_id'));
            $client_secret = $this->get_setting('google_client_secret', $this->config->item('google_client_secret'));
            $redirect_uri = $this->get_setting('google_redirect_uri', $this->config->item('google_redirect_uri'));
            $scopes = $this->config->item('google_scopes');

            // สร้าง Google Client
            try {
                $this->google_client = $this->create_google_client_safely($client_id, $client_secret, $redirect_uri, $scopes);
                
                if (!$this->google_client) {
                    log_message('error', 'create_google_client_safely returned null');
                    return false;
                }

                // สร้าง Drive Service
                $this->drive_service = $this->create_drive_service_safely();
                
                if (!$this->drive_service) {
                    log_message('error', 'create_drive_service_safely returned null');
                    return false;
                }
                
                // สร้าง OAuth2 Service (Optional)
                $this->oauth2_service = $this->init_oauth2_service();

                if ($this->get_setting('debug_mode', $this->config->item('debug_mode'))) {
                    log_message('info', 'Google Client v3.1.1 initialized successfully');
                }

                return true;

            } catch (Exception $e) {
                log_message('error', 'Google Client initialization exception: ' . $e->getMessage());
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Google Client v3.1.1 init error: ' . $e->getMessage());
            return false;
        }
    }

	
	
	
	/**
 * 🆕 AJAX Function สำหรับ set_setting
 */
public function set_setting_ajax() {
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
        $value = $this->input->post('value');
        
        if (!$setting_key) {
            $this->output_json_error('Setting key required');
            return;
        }

        $result = $this->set_setting($setting_key, $value);
        
        if ($result) {
            $this->output_json_success([
                'setting_key' => $setting_key,
                'value' => $value
            ], 'บันทึกการตั้งค่าสำเร็จ');
        } else {
            $this->output_json_error('ไม่สามารถบันทึกการตั้งค่าได้');
        }

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
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

   

// เพิ่มใน Google_drive Controller

/**
 * 🆕 เพิ่ม Function get_setting() สำหรับ AJAX calls
 */
public function get_setting_ajax() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $setting_key = $this->input->get('setting_key');
        
        if (!$setting_key) {
            $this->output_json_error('Setting key required');
            return;
        }

        $value = $this->get_setting($setting_key, null);
        
        $this->output_json_success([
            'setting_key' => $setting_key,
            'value' => $value
        ], 'ดึงการตั้งค่าสำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🔄 แก้ไข Function connect() เพิ่มการสร้าง Auth URL ที่ปลอดภัย
 */
public function connect() {
    try {
        $permission_check = $this->Google_drive_model->check_drive_permission($this->session->userdata('m_id'));
        
        if (!$permission_check['allowed']) {
            $this->session->set_flashdata('error', $permission_check['reason']);
            redirect('google_drive');
        }

        // ตรวจสอบการตั้งค่า OAuth
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');
        
        if (empty($client_id) || empty($client_secret)) {
            $this->session->set_flashdata('error', 'ระบบยังไม่ได้ตั้งค่า Google OAuth กรุณาติดต่อผู้ดูแลระบบ');
            redirect('google_drive');
        }

        // สร้าง Auth URL แบบปลอดภัย
        $auth_url = $this->create_auth_url_safely();
        
        if (!$auth_url) {
            $this->session->set_flashdata('error', 'ไม่สามารถสร้าง Authorization URL ได้ กรุณาตรวจสอบการตั้งค่า');
            redirect('google_drive');
        }

        // เก็บข้อมูลใน Session
        $this->session->set_userdata('oauth_member_id', $this->session->userdata('m_id'));
        $this->session->set_userdata('oauth_start_time', time());
        
        redirect($auth_url);

    } catch (Exception $e) {
        log_message('error', 'Connect error: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        redirect('google_drive');
    }
}

/**
     * ⭐ แก้ไข OAuth Callback เพื่อรองรับ System Setup และ User Setup
     */
    public function oauth_callback() {
        try {
            $code = $this->input->get('code');
            $error = $this->input->get('error');
            $state = $this->input->get('state');
            $system_setup = $this->input->get('system_setup');
            
            log_message('info', 'OAuth callback received - State: ' . $state . ', System setup: ' . ($system_setup === '1' ? 'Yes' : 'No'));
            
            if ($error) {
                throw new Exception('Google OAuth Error: ' . $error);
            }

            if (!$code) {
                throw new Exception('ไม่ได้รับ Authorization Code จาก Google');
            }

            // ตรวจสอบว่าเป็น System Setup หรือ User Setup
            if ($system_setup === '1' || strpos($state, 'system_') === 0) {
                // เป็น System Setup
                $this->handle_system_setup_callback($code);
            } else {
                // เป็น User Setup แบบเดิม
                $this->handle_user_setup_callback($code);
            }

        } catch (Exception $e) {
            log_message('error', 'OAuth callback error: ' . $e->getMessage());
            
            // ตรวจสอบว่าควร redirect ไปที่ไหน
            if ($this->input->get('system_setup') === '1' || strpos($this->input->get('state'), 'system_') === 0) {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
                redirect('google_drive_system/setup');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
                redirect('google_drive');
            }
        }
    }

	
	

/**
     * ⭐ จัดการ System Setup Callback (แก้ไข)
     */
    private function handle_system_setup_callback($code) {
        try {
            $oauth_type = $this->session->userdata('system_oauth_type');
            $admin_id = $this->session->userdata('system_oauth_admin');

            if (!$oauth_type || !$admin_id) {
                throw new Exception('ไม่พบข้อมูล System OAuth Session');
            }

            log_message('info', 'Processing system setup callback for admin: ' . $admin_id);

            // ⭐ ใช้ Manual Token Exchange สำหรับ System Setup
            $token = $this->manual_token_exchange_fixed($code);
            
            // ดึงข้อมูลผู้ใช้จาก Google
            $user_info = $this->get_google_user_info($token['access_token']);
            
            // บันทึกข้อมูล System Storage
            $storage_data = [
                'storage_name' => 'Organization Storage',
                'google_account_email' => $user_info['email'],
                'google_access_token' => json_encode($token),
                'google_refresh_token' => $token['refresh_token'] ?? null,
                'google_token_expires' => date('Y-m-d H:i:s', time() + ($token['expires_in'] ?? 3600)),
                'is_active' => 1,
                'created_by' => $admin_id
            ];

            $storage_id = $this->create_system_storage_record($storage_data);
            
            if ($storage_id) {
                // ล้าง Session
                $this->session->unset_userdata(['system_oauth_type', 'system_oauth_admin', 'oauth_member_id']);
                
                // Log การดำเนินการ
                $this->log_action($admin_id, 'system_setup', 'เชื่อมต่อ Google Account หลักสำหรับ System Storage: ' . $user_info['email']);
                
                $this->session->set_flashdata('success', 'เชื่อมต่อ Google Account หลักเรียบร้อยแล้ว คุณสามารถสร้างโครงสร้างโฟลเดอร์ได้แล้ว');
            } else {
                throw new Exception('ไม่สามารถบันทึกข้อมูล System Storage ได้');
            }

            redirect('google_drive_system/setup');

        } catch (Exception $e) {
            log_message('error', 'Handle system setup callback error: ' . $e->getMessage());
            throw $e;
        }
    }
	
/**
     * ⭐ จัดการ User Setup Callback (แก้ไข)
     */
    private function handle_user_setup_callback($code) {
        try {
            $member_id = $this->session->userdata('oauth_member_id');
            if (!$member_id) {
                throw new Exception('ไม่พบข้อมูล OAuth Session');
            }

            log_message('info', 'Processing user setup callback for member: ' . $member_id);

            // ⭐ ใช้ Manual Token Exchange สำหรับ User Setup
            $token = $this->manual_token_exchange_fixed($code);
            
            // ดึงข้อมูลผู้ใช้จาก Google
            $user_info = $this->get_google_user_info($token['access_token']);
            
            // บันทึกข้อมูลลงฐานข้อมูล
            $member_data = [
                'google_email' => $user_info['email'],
                'google_access_token' => json_encode($token),
                'google_refresh_token' => $token['refresh_token'] ?? null,
                'google_token_expires' => date('Y-m-d H:i:s', time() + ($token['expires_in'] ?? 3600)),
                'google_connected_at' => date('Y-m-d H:i:s'),
                'google_account_verified' => 1,
                'google_drive_enabled' => 1
            ];

            $this->db->where('m_id', $member_id);
            $update_result = $this->db->update('tbl_member', $member_data);
            
            if ($update_result) {
                // ตรวจสอบว่าต้องสร้าง folders หรือไม่
                if ($this->get_setting('auto_create_folders', '1') === '1') {
                    $this->create_member_folders($member_id, $token['access_token']);
                }
                
                // ล้าง Session
                $this->session->unset_userdata(['oauth_member_id', 'oauth_start_time']);
                
                $this->session->set_flashdata('success', 'เชื่อมต่อ Google Drive เรียบร้อยแล้ว');
            } else {
                throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
            }

            redirect('google_drive');

        } catch (Exception $e) {
            log_message('error', 'Handle user setup callback error: ' . $e->getMessage());
            throw $e;
        }
    }
	
/**
 * 🆕 สร้าง System Storage Record
 */
private function create_system_storage_record($data) {
    try {
        $this->create_system_storage_table_if_not_exists();
        
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $result = $this->db->insert('tbl_google_drive_system_storage', $data);
        
        if ($result) {
            log_message('info', 'System storage record created successfully');
            return $this->db->insert_id();
        }
        
        log_message('error', 'Failed to insert system storage record');
        return false;

    } catch (Exception $e) {
        log_message('error', 'Create system storage record error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🆕 สร้างตาราง System Storage ถ้ายังไม่มี
 */
private function create_system_storage_table_if_not_exists() {
    if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
        log_message('info', 'Creating tbl_google_drive_system_storage table');
        
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
        log_message('info', 'tbl_google_drive_system_storage table created successfully');
    }
}


/**
     * แก้ไข create_member_folders ให้เรียก function ใหม่
     */
    private function create_member_folders($member_id, $access_token) {
        try {
            // ดึงข้อมูล member และตำแหน่ง
            $member = $this->db->select('m.*, p.pname')
                              ->from('tbl_member m')
                              ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                              ->where('m.m_id', $member_id)
                              ->get()
                              ->row();

            if (!$member) {
                return false;
            }

            $position_id = $member->ref_pid;

            // ตรวจสอบ folders เก่าที่มีอยู่
            $existing_folders = $this->check_existing_folders($member_id);
            
            if (!empty($existing_folders)) {
                // มี folders เก่า ให้ reactive
                return $this->reactivate_existing_folders($member_id, $access_token, $existing_folders);
            }

            // สร้าง folders ใหม่ตามสิทธิ์
            if (in_array($position_id, [1, 2])) {
                // Admin - สร้างทุก folders
                return $this->create_admin_folders_manual($member_id, $access_token);
            } elseif ($position_id == 3) {
                // Department Admin
                return $this->create_department_folders_manual($member_id, $position_id, $access_token);
            } else {
                // Position Only - ใช้ Personal Folder
                $result = $this->create_personal_folder_internal($member_id);
                return $result !== false;
            }

        } catch (Exception $e) {
            log_message('error', 'Create member folders error: ' . $e->getMessage());
            return false;
        }
    }

/**
 * 🔄 แก้ไข Function disconnect() เพิ่มการจัดการที่ปลอดภัย
 */
public function disconnect() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $member_id = $this->input->post('member_id');
        
        if (!$member_id) {
            $this->output_json_error('Member ID required');
            return;
        }

        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            if ($member_id != $this->session->userdata('m_id')) {
                $this->output_json_error('ไม่มีสิทธิ์ในการตัดการเชื่อมต่อสมาชิกคนอื่น');
                return;
            }
        }

        // ดึงข้อมูล access token
        $member = $this->db->select('google_access_token, m_fname, m_lname')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->output_json_error('ไม่พบข้อมูลสมาชิก');
            return;
        }

        // Revoke Google Token (แบบปลอดภัย)
        if (!empty($member->google_access_token)) {
            $this->safe_revoke_google_token($member->google_access_token);
            $this->remove_member_permissions_from_folders($member_id, $member->google_access_token);
        }

        // อัปเดตฐานข้อมูล
        $disconnect_data = [
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires' => null,
            'google_account_verified' => 0,
            'google_drive_enabled' => 0
        ];

        $this->db->where('m_id', $member_id);
        $result = $this->db->update('tbl_member', $disconnect_data);

        // ปิดการใช้งาน folders (ไม่ลบ)
        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $this->db->where('member_id', $member_id);
            $this->db->update('tbl_google_drive_folders', ['is_active' => 0]);
        }

        if ($result) {
            $this->log_action($member_id, 'disconnect', 'ตัดการเชื่อมต่อ Google Drive');
            $this->output_json_success([], 'ตัดการเชื่อมต่อ Google Drive เรียบร้อยแล้ว');
        } else {
            $this->output_json_error('ไม่สามารถตัดการเชื่อมต่อได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Disconnect error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🆕 เพิ่ม Function refresh_google_token()
 */
private function refresh_google_token($member_id, $refresh_token) {
    try {
        if (empty($refresh_token)) {
            return false;
        }

        $new_token = $this->refresh_access_token($refresh_token);
        
        if ($new_token) {
            // อัปเดต token ใหม่
            $this->db->where('m_id', $member_id);
            $this->db->update('tbl_member', [
                'google_access_token' => json_encode($new_token),
                'google_token_expires' => date('Y-m-d H:i:s', time() + ($new_token['expires_in'] ?? 3600))
            ]);
            
            // อัปเดต Google Client
            if ($this->google_client) {
                $this->google_client->setAccessToken($new_token);
            }
            
            return true;
        }

        return false;

    } catch (Exception $e) {
        log_message('error', 'Refresh Google token error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🔄 แก้ไข Function create_auth_url_safely() เพิ่มการตรวจสอบ Client ID
 */
private function create_auth_url_safely() {
    try {
        if (!$this->google_client) {
            // ลอง init ใหม่
            if (!$this->init_google_client()) {
                return $this->create_manual_auth_url();
            }
        }

        // ตรวจสอบ Client ID ใน Google Client
        if (method_exists($this->google_client, 'getClientId')) {
            $current_client_id = $this->google_client->getClientId();
            if (empty($current_client_id)) {
                log_message('error', 'Google Client ID is empty in client instance');
                return $this->create_manual_auth_url();
            }
        }

        // ลองสร้าง Auth URL
        try {
            $auth_url = $this->google_client->createAuthUrl();
            
            // ตรวจสอบว่ามี client_id parameter หรือไม่
            if (strpos($auth_url, 'client_id=') !== false) {
                return $auth_url;
            } else {
                log_message('error', 'createAuthUrl() missing client_id parameter');
                return $this->create_manual_auth_url();
            }
            
        } catch (Exception $e) {
            log_message('error', 'createAuthUrl failed: ' . $e->getMessage());
            return $this->create_manual_auth_url();
        }

    } catch (Exception $e) {
        log_message('error', 'Create auth URL safely error: ' . $e->getMessage());
        return $this->create_manual_auth_url();
    }
}

/**
 * 🆕 เพิ่ม Function validate_oauth_settings()
 */
private function validate_oauth_settings() {
    $client_id = $this->get_setting('google_client_id');
    $client_secret = $this->get_setting('google_client_secret');
    $redirect_uri = $this->get_setting('google_redirect_uri');
    
    $errors = [];
    
    if (empty($client_id)) {
        $errors[] = 'Google Client ID ไม่ได้ตั้งค่า';
    } elseif (!$this->validate_google_client_id($client_id)) {
        $errors[] = 'รูปแบบ Google Client ID ไม่ถูกต้อง';
    }
    
    if (empty($client_secret)) {
        $errors[] = 'Google Client Secret ไม่ได้ตั้งค่า';
    }
    
    if (empty($redirect_uri)) {
        $errors[] = 'Google Redirect URI ไม่ได้ตั้งค่า';
    } elseif (!filter_var($redirect_uri, FILTER_VALIDATE_URL)) {
        $errors[] = 'รูปแบบ Redirect URI ไม่ถูกต้อง';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
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
 * 🔄 แก้ไข Function get_member_drive_info() สำหรับ Centralized Storage
 */
public function get_member_drive_info() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        $member_id = $this->input->post('member_id');
        
        if (!$member_id) {
            $this->output_json_error('Member ID required');
            return;
        }

        // ดึงข้อมูล member
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

        // ตรวจสอบว่าใช้ระบบแบบไหน
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        
        if ($storage_mode === 'centralized') {
            // ใช้ Centralized Storage
            $drive_info = $this->get_member_centralized_storage_info($member_id);
            $is_connected = $member->storage_access_granted;
        } else {
            // ใช้ User-based Storage แบบเดิม
            $is_connected = $this->check_google_connection($member_id);
            $drive_info = null;
            
            if ($is_connected) {
                $drive_info = $this->get_drive_info($member_id);
            }
        }

        $permission = $this->get_member_permission($member_id, $member->ref_pid);
        $available_permissions = $this->get_permission_types();

        $response_data = [
            'member_id' => (int)$member_id,
            'storage_mode' => $storage_mode,
            'is_connected' => $is_connected,
            'drive_info' => $drive_info,
            'permission' => $permission,
            'available_permissions' => $available_permissions
        ];

        $this->output_json_success($response_data, 'ดึงข้อมูล Google Drive สำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'get_member_drive_info error: ' . $e->getMessage());
        $this->output_json_error($e->getMessage(), 500);
    }
}
	
	
	
	
	/**
 * 🆕 เพิ่ม Function get_member_centralized_storage_info()
 */
private function get_member_centralized_storage_info($member_id) {
    try {
        $member = $this->db->select('m.*, p.pname')
                          ->from('tbl_member m')
                          ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                          ->where('m.m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            return null;
        }

        // ดึงข้อมูล System Storage
        $system_storage = $this->get_system_storage_info();
        if (!$system_storage) {
            return null;
        }

        // ดึงโฟลเดอร์ที่เข้าถึงได้
        $accessible_folders = [];
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $permission = $this->get_member_permission($member_id, $member->ref_pid);
            $accessible_folders = $this->get_accessible_system_folders($member_id, $permission);
        }

        // ดึงไฟล์ที่อัปโหลด
        $uploaded_files = [];
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $uploaded_files = $this->db->select('COUNT(*) as total_files, SUM(file_size) as total_size')
                                      ->from('tbl_google_drive_system_files')
                                      ->where('uploaded_by', $member_id)
                                      ->get()
                                      ->row();
        }

        return (object)[
            'storage_type' => 'centralized',
            'system_storage_name' => $system_storage->storage_name,
            'system_account_email' => $system_storage->google_account_email,
            'member_quota_limit' => $member->storage_quota_limit,
            'member_quota_used' => $member->storage_quota_used,
            'quota_usage_percent' => round(($member->storage_quota_used / $member->storage_quota_limit) * 100, 2),
            'personal_folder_id' => $member->personal_folder_id,
            'last_access' => $member->last_storage_access,
            'accessible_folders' => $accessible_folders,
            'uploaded_files_count' => $uploaded_files->total_files ?? 0,
            'uploaded_files_size' => $uploaded_files->total_size ?? 0
        ];

    } catch (Exception $e) {
        log_message('error', 'Get member centralized storage info error: ' . $e->getMessage());
        return null;
    }
}

/**
 * 🆕 เพิ่ม Function get_accessible_system_folders()
 */
private function get_accessible_system_folders($member_id, $permission) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return [];
        }

        $accessible_folders = [];

        switch ($permission['access_type']) {
            case 'full':
                // Admin เข้าถึงได้ทุกโฟลเดอร์
                $accessible_folders = $this->db->select('*')
                                              ->from('tbl_google_drive_system_folders')
                                              ->where('is_active', 1)
                                              ->order_by('folder_path', 'ASC')
                                              ->get()
                                              ->result();
                break;

            case 'department':
                // เข้าถึงได้โฟลเดอร์แผนกและ shared
                $member = $this->db->select('ref_pid')->from('tbl_member')->where('m_id', $member_id)->get()->row();
                
                $this->db->select('*')
                        ->from('tbl_google_drive_system_folders')
                        ->where('is_active', 1);
                        
                $this->db->group_start()
                        ->where('folder_type', 'shared')
                        ->or_where('created_for_position', $member->ref_pid)
                        ->group_end();
                        
                $accessible_folders = $this->db->order_by('folder_path', 'ASC')->get()->result();
                break;

            case 'position_only':
                // เข้าถึงได้เฉพาะโฟลเดอร์ส่วนตัวและ shared
                $this->db->select('*')
                        ->from('tbl_google_drive_system_folders')
                        ->where('is_active', 1);
                        
                $this->db->group_start()
                        ->where('folder_type', 'shared')
                        ->or_where('folder_type', 'user')
                        ->group_end();
                        
                $accessible_folders = $this->db->order_by('folder_path', 'ASC')->get()->result();
                break;

            case 'read_only':
                // เข้าถึงได้เฉพาะโฟลเดอร์ shared
                $accessible_folders = $this->db->select('*')
                                              ->from('tbl_google_drive_system_folders')
                                              ->where('folder_type', 'shared')
                                              ->where('is_active', 1)
                                              ->order_by('folder_path', 'ASC')
                                              ->get()
                                              ->result();
                break;

            default:
                $accessible_folders = [];
        }

        return $accessible_folders;

    } catch (Exception $e) {
        log_message('error', 'Get accessible system folders error: ' . $e->getMessage());
        return [];
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
 * 🔄 แก้ไข Manual Token Exchange Method (เพิ่ม logging)
 */
private function manual_token_exchange($code) {
    try {
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');
        $redirect_uri = $this->get_setting('google_redirect_uri');
        
        log_message('info', 'Manual token exchange - Client ID: ' . substr($client_id, 0, 20) . '...');
        log_message('info', 'Manual token exchange - Redirect URI: ' . $redirect_uri);
        
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
        
        log_message('info', 'Token exchange HTTP response: ' . $http_code);
        
        if ($http_code !== 200) {
            log_message('error', 'Token exchange failed - HTTP: ' . $http_code . ', Response: ' . $response);
            throw new Exception('HTTP Error: ' . $http_code . ' - ' . $response);
        }
        
        $token = json_decode($response, true);
        
        if (!$token || isset($token['error'])) {
            log_message('error', 'Token response error: ' . $response);
            throw new Exception('Token response error: ' . $response);
        }
        
        log_message('info', 'Token exchange successful');
        return $token;
        
    } catch (Exception $e) {
        log_message('error', 'Manual token exchange error: ' . $e->getMessage());
        throw $e;
    }
}

/**
     * ⭐ ดึงข้อมูลผู้ใช้จาก Google (แก้ไข)
     */
    private function get_google_user_info($access_token) {
        try {
            $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($access_token);
            
            log_message('info', 'Getting Google user info...');
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json',
                    'User-Agent: CodeIgniter-GoogleDrive/3.1.1'
                ]
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $user_info = json_decode($response, true);
                if ($user_info && isset($user_info['email'])) {
                    log_message('info', 'Google user info retrieved for: ' . $user_info['email']);
                    return $user_info;
                }
            }
            
            log_message('error', 'Failed to get user info - HTTP: ' . $http_code . ', Response: ' . $response);
            throw new Exception('Failed to get user info: ' . $response);
            
        } catch (Exception $e) {
            log_message('error', 'Get Google user info error: ' . $e->getMessage());
            throw $e;
        }
    }

/**
     * ⭐ แก้ไขหลัก: fetchAccessTokenWithAuthCode() รองรับหลาย version
     */
    public function fetchAccessTokenWithAuthCode($code, $codeVerifier = null) {
        try {
            log_message('info', 'fetchAccessTokenWithAuthCode called with code: ' . substr($code, 0, 20) . '...');

            // Method 1: Google Client v2.x+ (ใหม่)
            if ($this->google_client && method_exists($this->google_client, 'fetchAccessTokenWithAuthCode')) {
                log_message('info', 'Using Google Client v2.x fetchAccessTokenWithAuthCode()');
                
                try {
                    $token = $this->google_client->fetchAccessTokenWithAuthCode($code);
                    
                    if (isset($token['error'])) {
                        log_message('error', 'fetchAccessTokenWithAuthCode error: ' . $token['error']);
                        throw new Exception('OAuth Error: ' . $token['error']);
                    }
                    
                    log_message('info', 'Token exchange successful via fetchAccessTokenWithAuthCode()');
                    return $token;
                    
                } catch (Exception $e) {
                    log_message('warning', 'fetchAccessTokenWithAuthCode() failed: ' . $e->getMessage());
                    // ลองใช้วิธีอื่น
                }
            }

            // Method 2: Google Client v1.x (เก่า) - authenticate() method
            if ($this->google_client && method_exists($this->google_client, 'authenticate')) {
                log_message('info', 'Using Google Client v1.x authenticate() method');
                
                try {
                    $token = $this->google_client->authenticate($code);
                    
                    if ($token) {
                        // แปลง format ถ้าจำเป็น
                        if (is_string($token)) {
                            $this->google_client->setAccessToken($token);
                            $token = $this->google_client->getAccessToken();
                        }
                        
                        log_message('info', 'Token exchange successful via authenticate()');
                        return $token;
                    }
                    
                } catch (Exception $e) {
                    log_message('warning', 'authenticate() failed: ' . $e->getMessage());
                    // ลองใช้วิธีอื่น
                }
            }

            // Method 3: Manual Token Exchange (แนะนำสำหรับทุก version)
            log_message('info', 'Using manual token exchange method (safest)');
            return $this->manual_token_exchange_fixed($code);
            
        } catch (Exception $e) {
            log_message('error', 'All token exchange methods failed: ' . $e->getMessage());
            throw new Exception('Token exchange failed: ' . $e->getMessage());
        }
    }
	
	
	
	
	/**
     * ⭐ Manual Token Exchange ที่แก้ไขแล้ว
     */
    private function manual_token_exchange_fixed($code) {
        try {
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');
            $redirect_uri = $this->get_setting('google_redirect_uri');
            
            log_message('info', 'Manual token exchange - Client ID: ' . substr($client_id, 0, 20) . '...');
            log_message('info', 'Manual token exchange - Redirect URI: ' . $redirect_uri);
            
            if (empty($client_id) || empty($client_secret)) {
                throw new Exception('OAuth credentials not configured');
            }
            
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
                    'Accept: application/json',
                    'User-Agent: CodeIgniter-GoogleDrive/3.1.1'
                ]
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                throw new Exception('cURL Error: ' . $error);
            }
            
            log_message('info', 'Token exchange HTTP response: ' . $http_code);
            
            if ($http_code !== 200) {
                log_message('error', 'Token exchange failed - HTTP: ' . $http_code . ', Response: ' . $response);
                throw new Exception('HTTP Error: ' . $http_code . ' - ' . $response);
            }
            
            $token = json_decode($response, true);
            
            if (!$token || isset($token['error'])) {
                log_message('error', 'Token response error: ' . $response);
                throw new Exception('Token response error: ' . ($token['error'] ?? 'Invalid response'));
            }
            
            log_message('info', 'Manual token exchange successful');
            return $token;
            
        } catch (Exception $e) {
            log_message('error', 'Manual token exchange error: ' . $e->getMessage());
            throw $e;
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
    // 🔥 ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // 🔥 ปิด error reporting ชั่วคราว  
    $old_error_reporting = error_reporting(0);
    
    try {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->force_json_error('Invalid request method', 400);
            return;
        }

        $client_id = $this->input->post('client_id');
        $client_secret = $this->input->post('client_secret');
        $redirect_uri = $this->input->post('redirect_uri');

        if (empty($client_id) || empty($client_secret)) {
            $this->force_json_error('กรุณาใส่ Google Client ID และ Client Secret', 400);
            return;
        }

        if (!$this->validate_google_client_id($client_id)) {
            $this->force_json_error('รูปแบบ Google Client ID ไม่ถูกต้อง (ต้องลงท้ายด้วย .apps.googleusercontent.com)', 400);
            return;
        }

        // ทดสอบการเชื่อมต่อ
        $oauth_test = $this->test_google_oauth($client_id, $client_secret, $redirect_uri);
        $drive_test = $this->test_google_drive_api($client_id, $client_secret);

        $this->force_json_success([
            'test_results' => [
                'oauth_status' => $oauth_test,
                'drive_api_status' => $drive_test,
                'library_version' => $this->get_library_version(),
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ], 'การเชื่อมต่อ Google Drive ทำงานได้ปกติ');

    } catch (Exception $e) {
        log_message('error', 'Test connection error: ' . $e->getMessage());
        $this->force_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
    } finally {
        // คืนค่า error reporting
        error_reporting($old_error_reporting);
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
    
    // เพิ่มข้อมูล System Storage
    $data['system_storage'] = $this->get_system_storage_info();
    $data['storage_mode'] = $this->get_setting('system_storage_mode', 'user_based'); // user_based หรือ centralized
    
    // โหลด View
    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/google_drive_settings', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
}

	
	
	
/**
 * 🔄 แก้ไข get_system_storage_info() ให้อัปเดตอัตโนมัติ
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

        // ดึงข้อมูลจริงจาก Google Drive API
        $access_token = $this->get_system_access_token();
        $google_storage_info = null;
        
        if ($access_token) {
            $google_storage_info = $this->get_google_drive_storage_info($access_token);
        }

        // ดึงสถิติการใช้งาน
        $total_folders = 0;
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $total_folders = $this->db->where('is_active', 1)
                                     ->count_all_results('tbl_google_drive_system_folders');
        }
                                 
        $total_files = 0;
        $system_files_size = 0;
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $file_stats = $this->db->select('COUNT(*) as count, SUM(file_size) as total_size')
                                  ->from('tbl_google_drive_system_files')
                                  ->get()
                                  ->row();
            
            $total_files = $file_stats->count ?? 0;
            $system_files_size = $file_stats->total_size ?? 0;
        }

        $active_users = $this->db->where('storage_access_granted', 1)
                                ->count_all_results('tbl_member');

        // คำนวณการใช้งานที่แยกส่วน
        if ($google_storage_info) {
            $google_account_total_usage = $google_storage_info['total_usage'];
            $google_account_drive_usage = $google_storage_info['drive_usage'];
            $google_account_limit = $google_storage_info['total_limit'];
            $google_account_gmail_usage = $google_storage_info['gmail_usage'];
            
            // คำนวณ % สำหรับแต่ละส่วน
            $total_usage_percent = $google_account_limit > 0 ? 
                round(($google_account_total_usage / $google_account_limit) * 100, 2) : 0;
            
            $drive_usage_percent = $google_account_limit > 0 ? 
                round(($google_account_drive_usage / $google_account_limit) * 100, 2) : 0;
        } else {
            // ใช้ข้อมูลจากฐานข้อมูล
            $google_account_total_usage = $system_storage->total_storage_used;
            $google_account_drive_usage = $system_files_size;
            $google_account_limit = $system_storage->max_storage_limit;
            $google_account_gmail_usage = 0;
            
            $total_usage_percent = $google_account_limit > 0 ? 
                round(($google_account_total_usage / $google_account_limit) * 100, 2) : 0;
            
            $drive_usage_percent = $google_account_limit > 0 ? 
                round(($google_account_drive_usage / $google_account_limit) * 100, 2) : 0;
        }

        return (object)[
            'id' => $system_storage->id,
            'storage_name' => $system_storage->storage_name,
            'google_account_email' => $system_storage->google_account_email,
            
            // ข้อมูล Google Account ทั้งหมด
            'google_account_total_usage' => $google_account_total_usage,
            'google_account_limit' => $google_account_limit,
            'google_account_total_percent' => $total_usage_percent,
            
            // ข้อมูลเฉพาะ Google Drive (ระบบเรา)
            'google_drive_usage' => $google_account_drive_usage,
            'google_drive_percent' => $drive_usage_percent,
            
            // ข้อมูลเฉพาะ Gmail + อื่นๆ
            'gmail_other_usage' => $google_account_gmail_usage,
            
            // ข้อมูลระบบเรา
            'system_files_count' => $total_files,
            'system_files_size' => $system_files_size,
            'total_folders' => $total_folders,
            'active_users' => $active_users,
            
            // ข้อมูลอื่นๆ
            'folder_structure_created' => $system_storage->folder_structure_created,
            'is_active' => $system_storage->is_active,
            'created_at' => $system_storage->created_at,
            'last_updated' => $system_storage->updated_at,
            
            // สำหรับ backward compatibility
            'storage_usage_percent' => $total_usage_percent, // Google Account ทั้งหมด
            'total_storage_used' => $google_account_total_usage,
            'max_storage_limit' => $google_account_limit
        ];

    } catch (Exception $e) {
        log_message('error', 'Get system storage info error: ' . $e->getMessage());
        return null;
    }
}
	
	
/**
 * 🆕 อัปเดต Storage แบบ Background (ไม่หน่วงเวลา)
 */
private function update_storage_from_api_background($system_storage) {
    try {
        $access_token = $this->get_system_access_token();
        
        if (!$access_token) {
            log_message('warning', 'No system access token for storage update');
            return false;
        }

        $storage_info = $this->get_google_drive_storage_info($access_token);
        
        if ($storage_info) {
            $this->db->where('id', $system_storage->id);
            $this->db->update('tbl_google_drive_system_storage', [
                'total_storage_used' => $storage_info['usage'],
                'max_storage_limit' => $storage_info['limit'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            log_message('info', 'Storage info updated from API: ' . 
                       round($storage_info['usage'] / 1073741824, 2) . ' GB used of ' . 
                       round($storage_info['limit'] / 1073741824, 2) . ' GB limit');
            
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        log_message('error', 'Background storage update error: ' . $e->getMessage());
        return false;
    }
}
	
	private function get_google_drive_storage_info($access_token) {
    try {
        // ดึงข้อมูล Storage Quota
        $about_url = 'https://www.googleapis.com/drive/v3/about?fields=storageQuota';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $about_url,
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
            
            if (isset($data['storageQuota'])) {
                $quota = $data['storageQuota'];
                
                return [
                    'total_limit' => (int)($quota['limit'] ?? 0),
                    'total_usage' => (int)($quota['usage'] ?? 0),
                    'drive_usage' => (int)($quota['usageInDrive'] ?? 0),
                    'gmail_usage' => (int)(($quota['usage'] ?? 0) - ($quota['usageInDrive'] ?? 0)),
                    'quota_breakdown' => [
                        'drive' => (int)($quota['usageInDrive'] ?? 0),
                        'gmail_and_others' => (int)(($quota['usage'] ?? 0) - ($quota['usageInDrive'] ?? 0))
                    ]
                ];
            }
        }
        
        return null;
        
    } catch (Exception $e) {
        log_message('error', 'Get Google Drive storage info error: ' . $e->getMessage());
        return null;
    }
}
	
	

	
	/**
 * 🆕 Force Update Storage (AJAX)
 */
public function force_update_storage() {
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
            $this->output_json_error('ไม่มีสิทธิ์ในการอัปเดต Storage');
            return;
        }

        $system_storage = $this->db->select('*')
                                  ->from('tbl_google_drive_system_storage')
                                  ->where('is_active', 1)
                                  ->get()
                                  ->row();

        if (!$system_storage) {
            $this->output_json_error('ไม่พบ System Storage');
            return;
        }

        $result = $this->update_storage_from_api_background($system_storage);
        
        if ($result) {
            // ดึงข้อมูลใหม่
            $updated_storage = $this->get_system_storage_info();
            
            $this->output_json_success([
                'storage_usage_percent' => $updated_storage->storage_usage_percent,
                'storage_usage_gb' => $updated_storage->storage_usage_gb,
                'storage_limit_gb' => $updated_storage->storage_limit_gb,
                'last_updated' => $updated_storage->last_updated
            ], 'อัปเดต Storage Usage สำเร็จ');
        } else {
            $this->output_json_error('ไม่สามารถอัปเดต Storage Usage ได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Force update storage error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

	
	
	public function get_storage_status() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $storage_info = $this->get_system_storage_info();
        
        if ($storage_info) {
            $this->output_json_success([
                'storage_usage_percent' => $storage_info->storage_usage_percent,
                'storage_usage_gb' => $storage_info->storage_usage_gb,
                'storage_limit_gb' => $storage_info->storage_limit_gb,
                'total_folders' => $storage_info->total_folders,
                'total_files' => $storage_info->total_files,
                'active_users' => $storage_info->active_users,
                'last_updated' => $storage_info->last_updated
            ], 'ดึงสถานะ Storage สำเร็จ');
        } else {
            $this->output_json_error('ไม่พบข้อมูล Storage');
        }

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
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
 * ✅ การจัดการ Special Toggle Conditions
 */
private function handle_special_toggle_conditions($setting_key, $enabled) {
    try {
        switch ($setting_key) {
            case 'google_drive_enabled':
                if (!$enabled) {
                    log_message('info', 'Google Drive disabled - all connections remain but system is inactive');
                } else {
                    // ตรวจสอบการตั้งค่า OAuth
                    $client_id = $this->get_setting('google_client_id');
                    $client_secret = $this->get_setting('google_client_secret');
                    
                    if (empty($client_id) || empty($client_secret)) {
                        log_message('warning', 'Google Drive enabled but OAuth credentials not configured');
                    }
                }
                break;

            case 'logging_enabled':
                if ($enabled) {
                    log_message('info', 'Google Drive logging enabled');
                }
                break;

            case 'cache_enabled':
                if (!$enabled) {
                    log_message('info', 'Cache disabled - clearing cache if exists');
                }
                break;
        }
    } catch (Exception $e) {
        log_message('error', 'Handle special toggle conditions error: ' . $e->getMessage());
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
 * ⭐ แก้ไข Toggle Setting Function ให้ส่ง JSON อย่างสมบูรณ์
 */
public function toggle_setting() {
    // 🔥 ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // 🔥 ปิด error reporting ชั่วคราว
    $old_error_reporting = error_reporting(0);
    
    try {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->force_json_error('Invalid request method', 400);
            return;
        }

        // ตรวจสอบสิทธิ์
        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
            $this->force_json_error('ไม่มีสิทธิ์ในการเปลี่ยนแปลงการตั้งค่า', 403);
            return;
        }

        $setting_key = $this->input->post('setting_key');
        $new_value = $this->input->post('value'); // '1' หรือ '0'

        if (!$setting_key) {
            $this->force_json_error('ไม่พบคีย์การตั้งค่า', 400);
            return;
        }

        // ตรวจสอบว่าเป็น toggle setting ที่อนุญาต
        $allowed_toggles = ['google_drive_enabled', 'auto_create_folders', 'cache_enabled', 'logging_enabled'];
        if (!in_array($setting_key, $allowed_toggles)) {
            $this->force_json_error('การตั้งค่านี้ไม่สามารถ Toggle ได้', 400);
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

            $this->force_json_success([
                'setting_key' => $setting_key,
                'new_value' => $string_value,
                'boolean_value' => $boolean_value
            ], $action_desc);
        } else {
            $this->force_json_error('ไม่สามารถบันทึกการตั้งค่าได้', 500);
        }

    } catch (Exception $e) {
        // Log error อย่างปลอดภัย
        log_message('error', 'Toggle setting error: ' . $e->getMessage());
        $this->force_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
    } finally {
        // คืนค่า error reporting
        error_reporting($old_error_reporting);
    }
}

/**
 * ⭐ แก้ไข Get All Toggle Status Function
 */
public function get_all_toggle_status() {
    // 🔥 ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // 🔥 ปิด error reporting ชั่วคราว
    $old_error_reporting = error_reporting(0);
    
    try {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->force_json_error('Invalid request method', 400);
            return;
        }
        
        // ตรวจสอบ login
        if (!$this->session->userdata('m_id')) {
            $this->force_json_error('Please login first', 401);
            return;
        }

        $toggle_settings = ['google_drive_enabled', 'auto_create_folders', 'cache_enabled', 'logging_enabled'];
        $status = [];

        foreach ($toggle_settings as $setting_key) {
            try {
                $current_value = $this->get_setting($setting_key, '0');
                $boolean_value = ($current_value === '1' || $current_value === 'true' || $current_value === true);
                
                $status[$setting_key] = [
                    'current_value' => $current_value,
                    'boolean_value' => $boolean_value,
                    'is_enabled' => $boolean_value
                ];
            } catch (Exception $e) {
                // ถ้าดึงการตั้งค่าไม่ได้ ให้ใช้ค่าเริ่มต้น
                $status[$setting_key] = [
                    'current_value' => '0',
                    'boolean_value' => false,
                    'is_enabled' => false
                ];
            }
        }

        $this->force_json_success($status, 'ดึงสถานะการตั้งค่าทั้งหมดสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get all toggle status error: ' . $e->getMessage());
        $this->force_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
    } finally {
        // คืนค่า error reporting
        error_reporting($old_error_reporting);
    }
}

/**
 * 🆕 Force JSON Error Response (แทนที่ output_json_error)
 */
private function force_json_error($message = 'Error', $status_code = 400, $debug_data = []) {
    // ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
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
    
    // ตั้งค่า headers อย่างสมบูรณ์
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');
    http_response_code($status_code);
    
    // ส่ง JSON response
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * 🆕 Force JSON Success Response (แทนที่ output_json_success)
 */
private function force_json_success($data = [], $message = 'Success') {
    // ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // ตั้งค่า headers อย่างสมบูรณ์
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');
    http_response_code(200);
    
    // ส่ง JSON response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}



/**
 * 🔄 แก้ไข Set Setting Function ให้ปลอดภัย
 */
public function set_setting($key, $value, $description = '') {
    try {
        // สร้างตารางถ้ายังไม่มี
        $this->create_settings_table_if_not_exists();

        $setting_data = [
            'setting_key' => $key,
            'setting_value' => $value,
            'setting_description' => $description ?: $this->get_setting_description($key),
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
            $result = $this->db->update('tbl_google_drive_settings', $setting_data);
        } else {
            // เพิ่มใหม่
            $setting_data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->db->insert('tbl_google_drive_settings', $setting_data);
        }

        return $result;

    } catch (Exception $e) {
        log_message('error', 'Set setting error: ' . $e->getMessage());
        return false;
    }
}

    /**
 * 🔄 แก้ไข Function manage() เพิ่มข้อมูล System Storage
 */
public function manage() {
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
    
    $data['storage_mode'] = $storage_mode;
    $data['statistics'] = $this->get_drive_statistics();
    
    if ($storage_mode === 'centralized') {
        // ใช้ Centralized Storage
        $data['system_storage'] = $this->get_system_storage_info();
        $data['storage_users'] = $this->get_storage_users();
    } else {
        // ใช้ User-based Storage แบบเดิม
        $data['connected_members'] = $this->get_connected_members();
    }

    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/google_drive_manage', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
}

	

	

/**
 * 🔄 แก้ไข count_storage_users() - นับผู้ใช้ทั้งหมด
 */
private function count_storage_users($search = '') {
    try {
        $this->db->from('tbl_member m');
        // ❌ ลบ: ->where('m.storage_access_granted', 1);

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('m.m_fname', $search)
                    ->or_like('m.m_lname', $search)
                    ->or_like('m.m_email', $search)
                    ->group_end();
        }

        return $this->db->count_all_results();

    } catch (Exception $e) {
        log_message('error', 'Count storage users error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * 🔄 แก้ไข get_drive_statistics() - ปรับสถิติให้แยกผู้ใช้ที่มีสิทธิ์และทั้งหมด
 */
private function get_drive_statistics() {
    $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
    
    if ($storage_mode === 'centralized') {
        // สถิติสำหรับ Centralized Storage
        $total_users = $this->db->count_all('tbl_member'); // ผู้ใช้ทั้งหมด
        $active_users = $this->db->where('storage_access_granted', 1)
                               ->count_all_results('tbl_member'); // ผู้ใช้ที่มีสิทธิ์

        $total_folders = 0;
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $total_folders = $this->db->where('is_active', 1)
                                     ->count_all_results('tbl_google_drive_system_folders');
        }

        $total_files = 0;
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $total_files = $this->db->count_all('tbl_google_drive_system_files');
        }

        $new_users_this_month = $this->db->where('last_storage_access >=', date('Y-m-01'))
                                        ->where('storage_access_granted', 1)
                                        ->count_all_results('tbl_member');

        return [
            'storage_mode' => 'centralized',
            'total_users' => $total_users,        // ✅ เพิ่ม: ผู้ใช้ทั้งหมด
            'active_users' => $active_users,      // ✅ เปลี่ยนชื่อ: ผู้ใช้ที่มีสิทธิ์
            'total_folders' => $total_folders,
            'total_files' => $total_files,
            'new_users_this_month' => $new_users_this_month
        ];
    } else {
        // สถิติสำหรับ User-based Storage แบบเดิม (ไม่เปลี่ยน)
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
            'storage_mode' => 'user_based',
            'connected_members' => $connected_members,
            'total_folders' => $total_folders,
            'synced_files' => $synced_files,
            'new_connections' => $new_connections
        ];
    }
}

/**
 * 🆕 เพิ่ม Function toggle_storage_mode()
 */
public function toggle_storage_mode() {
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
            $this->output_json_error('ไม่มีสิทธิ์ในการเปลี่ยนแปลงโหมด Storage');
            return;
        }

        $new_mode = $this->input->post('mode'); // 'user_based' หรือ 'centralized'
        
        if (!in_array($new_mode, ['user_based', 'centralized'])) {
            $this->output_json_error('โหมด Storage ไม่ถูกต้อง');
            return;
        }

        // ตรวจสอบเงื่อนไขสำหรับ Centralized Mode
        if ($new_mode === 'centralized') {
            $system_storage = $this->get_system_storage_info();
            if (!$system_storage) {
                $this->output_json_error('ยังไม่ได้ตั้งค่า System Storage กรุณาตั้งค่าก่อน');
                return;
            }
            
            if (!$system_storage->folder_structure_created) {
                $this->output_json_error('ยังไม่ได้สร้างโครงสร้างโฟลเดอร์ กรุณาสร้างก่อน');
                return;
            }
        }

        // อัปเดตโหมด
        $result = $this->set_setting('system_storage_mode', $new_mode);
        
        if ($result) {
            // Log การเปลี่ยนแปลง
            $this->log_action($this->session->userdata('m_id'), 'change_mode', 
                "เปลี่ยนโหมด Storage เป็น: " . ($new_mode === 'centralized' ? 'Centralized Storage' : 'User-based Storage'));

            $this->output_json_success([
                'new_mode' => $new_mode,
                'mode_name' => $new_mode === 'centralized' ? 'Centralized Storage' : 'User-based Storage'
            ], 'เปลี่ยนโหมด Storage เรียบร้อยแล้ว');
        } else {
            $this->output_json_error('ไม่สามารถเปลี่ยนโหมดได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Toggle storage mode error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🆕 เพิ่ม Function grant_storage_access()
 */
public function grant_storage_access() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $member_id = $this->input->post('member_id');
        $quota_limit = $this->input->post('quota_limit', true) ?: 1073741824; // 1GB default
        
        if (!$member_id) {
            $this->output_json_error('ไม่พบข้อมูลสมาชิก');
            return;
        }

        // ตรวจสอบว่าเป็น Centralized Mode หรือไม่
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        if ($storage_mode !== 'centralized') {
            $this->output_json_error('ฟีเจอร์นี้ใช้ได้เฉพาะโหมด Centralized Storage');
            return;
        }

        // อัปเดตสิทธิ์การเข้าถึง Storage
        $update_data = [
            'storage_access_granted' => 1,
            'storage_quota_limit' => (int)$quota_limit,
            'storage_quota_used' => 0 // เริ่มต้นที่ 0
        ];

        $this->db->where('m_id', $member_id);
        $result = $this->db->update('tbl_member', $update_data);

        if ($result) {
            // สร้างโฟลเดอร์ส่วนตัวให้ User ถ้ายังไม่มี
            $this->create_user_personal_folder($member_id);
            
            $member = $this->db->select('m_fname, m_lname')->from('tbl_member')->where('m_id', $member_id)->get()->row();
            $this->log_action($this->session->userdata('m_id'), 'grant_access', 
                "อนุญาตให้ {$member->m_fname} {$member->m_lname} เข้าใช้ Storage");

            $this->output_json_success([], 'อนุญาตการเข้าใช้ Storage เรียบร้อยแล้ว');
        } else {
            $this->output_json_error('ไม่สามารถอัปเดตสิทธิ์ได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Grant storage access error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🆕 ตรวจสอบ Personal Folder ของผู้ใช้
 */
public function check_user_personal_folder() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $member_id = $this->input->post('member_id') ?: $this->input->get('member_id');
        
        if (!$member_id) {
            $this->output_json_error('ไม่ได้ระบุ Member ID');
            return;
        }

        // ดึงข้อมูล member
        $member = $this->db->select('m_id, m_fname, m_lname, personal_folder_id, storage_access_granted')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->output_json_error('ไม่พบข้อมูลผู้ใช้');
            return;
        }

        $has_folder = !empty($member->personal_folder_id);
        $folder_info = null;

        if ($has_folder) {
            $folder_info = [
                'folder_id' => $member->personal_folder_id,
                'folder_name' => $member->m_fname . ' ' . $member->m_lname . ' (ID: ' . $member_id . ')',
                'web_view_link' => "https://drive.google.com/drive/folders/{$member->personal_folder_id}"
            ];
        }

        $this->output_json_success([
            'has_folder' => $has_folder,
            'folder_info' => $folder_info,
            'storage_access_granted' => (bool)$member->storage_access_granted
        ], $has_folder ? 'ผู้ใช้มี Personal Folder อยู่แล้ว' : 'ต้องสร้าง Personal Folder ใหม่');

    } catch (Exception $e) {
        log_message('error', 'check_user_personal_folder error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	

   public function create_user_personal_folder() {
    // 🔥 ล้าง output buffer อย่างสมบูรณ์
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // 🔥 ปิด error reporting ชั่วคราว
    $old_error_reporting = error_reporting(0);
    
    try {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->force_json_error('Invalid request method', 400);
            return;
        }

        // ตรวจสอบ login
        if (!$this->session->userdata('m_id')) {
            $this->force_json_error('Please login first', 401);
            return;
        }

        // รับ member_id
        $member_id = $this->input->post('member_id');
        
        if (!$member_id) {
            $this->force_json_error('ไม่ได้ระบุ Member ID', 400);
            return;
        }

        // ตรวจสอบสิทธิ์ (เฉพาะ admin หรือตัวเอง)
        $current_user = $this->session->userdata('m_id');
        $is_admin = in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin']);
        
        if (!$is_admin && $current_user != $member_id) {
            $this->force_json_error('ไม่มีสิทธิ์ในการสร้าง Personal Folder ให้ผู้อื่น', 403);
            return;
        }

        // ตรวจสอบว่าเป็น Centralized Storage หรือไม่
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        
        if ($storage_mode !== 'centralized') {
            $this->force_json_error('ฟีเจอร์นี้ใช้ได้เฉพาะโหมด Centralized Storage', 400);
            return;
        }

        // เรียกใช้ Enhanced function
        log_message('info', "create_user_personal_folder: Starting for member {$member_id}");
        
        $result = $this->create_personal_folder_internal($member_id);
        
        if ($result && is_array($result)) {
            // บันทึก log
            $member = $this->db->select('m_fname, m_lname')->from('tbl_member')->where('m_id', $member_id)->get()->row();
            $this->log_action($current_user, 'create_personal_folder', 
                "สร้าง Personal Folder สำหรับ {$member->m_fname} {$member->m_lname}", [
                    'target_member_id' => $member_id,
                    'folder_id' => $result['folder_id']
                ]);

            // ส่ง JSON response
            $this->force_json_success([
                'folder_id' => $result['folder_id'],
                'folder_name' => $result['folder_name'],
                'web_view_link' => $result['web_view_link'],
                'message' => $result['message']
            ], 'สร้าง Personal Folder เรียบร้อย');
        } else {
            log_message('error', "create_user_personal_folder: Failed for member {$member_id}");
            $this->force_json_error('ไม่สามารถสร้าง Personal Folder ได้', 500);
        }

    } catch (Exception $e) {
        log_message('error', 'create_user_personal_folder error: ' . $e->getMessage());
        $this->force_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
    } finally {
        // คืนค่า error reporting
        error_reporting($old_error_reporting);
        exit; // 🔥 สำคัญ: หยุดการทำงานทันที
    }
}



/**
     * 🔄 Private Function สำหรับสร้าง Personal Folder (เปลี่ยนชื่อเพื่อไม่ซ้ำ)
     */
   private function create_personal_folder_internal($member_id) {
    try {
        log_message('info', "Enhanced create_personal_folder_internal: Starting for member {$member_id}");
        
        // ดึงข้อมูล member
        $member = $this->db->select('m_id, m_fname, m_lname, personal_folder_id')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            log_message('error', "Enhanced create_personal_folder_internal: Member {$member_id} not found");
            return [
                'success' => false,
                'error' => 'ไม่พบข้อมูลสมาชิก'
            ];
        }

        // ดึง System Access Token
        $access_token = $this->get_system_access_token();
        if (!$access_token) {
            log_message('error', 'Enhanced create_personal_folder_internal: No system access token available');
            return [
                'success' => false,
                'error' => 'ไม่พบ System Access Token'
            ];
        }

        // สร้างชื่อโฟลเดอร์ (ไม่ใส่ ID)
        $folder_name = $member->m_fname . ' ' . $member->m_lname;
        log_message('info', "Enhanced create_personal_folder_internal: Folder name will be '{$folder_name}'");

        // ขั้นตอนที่ 1: ตรวจสอบในฐานข้อมูล
        $db_folder = $this->check_folder_in_database($member_id, $folder_name);
        
        if ($db_folder) {
            log_message('info', "Enhanced create_personal_folder_internal: Found existing folder in database: {$db_folder->folder_id}");
            
            // ขั้นตอนที่ 2: ตรวจสอบใน Google Drive
            $google_exists = $this->verify_folder_exists_in_google_drive($db_folder->folder_id, $access_token);
            
            if ($google_exists) {
                log_message('info', "Enhanced create_personal_folder_internal: Folder exists in both database and Google Drive");
                
                // อัปเดต member record ถ้ายังไม่มี personal_folder_id
                if (empty($member->personal_folder_id)) {
                    $this->update_member_personal_folder_id($member_id, $db_folder->folder_id);
                }
                
                return [
                    'folder_id' => $db_folder->folder_id,
                    'folder_name' => $db_folder->folder_name,
                    'web_view_link' => "https://drive.google.com/drive/folders/{$db_folder->folder_id}",
                    'message' => 'ใช้โฟลเดอร์ที่มีอยู่แล้ว',
                    'success' => true
                ];
            } else {
                log_message('warning', "Enhanced create_personal_folder_internal: Folder exists in database but not in Google Drive, recreating...");
                
                // สร้างใหม่ใน Google Drive
                return $this->recreate_folder_in_google_drive($member_id, $folder_name, $db_folder, $access_token);
            }
        } else {
            log_message('info', "Enhanced create_personal_folder_internal: No folder found in database, creating new one");
            
            // สร้างใหม่ทั้งใน Google Drive และฐานข้อมูล
            return $this->create_new_personal_folder($member_id, $folder_name, $access_token);
        }

    } catch (Exception $e) {
        log_message('error', 'Enhanced create_personal_folder_internal error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
	
	
	
	
	/**
 * ตรวจสอบโฟลเดอร์ในฐานข้อมูล
 */
private function check_folder_in_database($member_id, $folder_name) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_folders')) {
            return null;
        }

        // ตรวจสอบทั้งชื่อโฟลเดอร์และ member_id
        $folder = $this->db->select('*')
                          ->from('tbl_google_drive_system_folders')
                          ->where('folder_type', 'user')
                          ->where('is_active', 1)
                          ->group_start()
                              ->where('folder_name', $folder_name)
                              ->or_like('folder_description', "Member ID: {$member_id}")
                          ->group_end()
                          ->get()
                          ->row();

        if ($folder) {
            log_message('info', "Found folder in database: ID={$folder->folder_id}, Name={$folder->folder_name}");
        }

        return $folder;

    } catch (Exception $e) {
        log_message('error', 'Check folder in database error: ' . $e->getMessage());
        return null;
    }
}

/**
 * ตรวจสอบโฟลเดอร์ใน Google Drive
 */
private function verify_folder_exists_in_google_drive($folder_id, $access_token) {
    try {
        log_message('info', "Verifying folder {$folder_id} in Google Drive");
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}?fields=id,name,mimeType,trashed",
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
            log_message('error', "Verify folder cURL error: {$error}");
            return false;
        }

        log_message('info', "Verify folder HTTP response: {$http_code}");

        if ($http_code === 200) {
            $data = json_decode($response, true);
            
            // ตรวจสอบว่าเป็นโฟลเดอร์และไม่ถูกลบ
            if ($data && 
                isset($data['id']) && 
                $data['mimeType'] === 'application/vnd.google-apps.folder' &&
                (!isset($data['trashed']) || $data['trashed'] === false)) {
                
                log_message('info', "Folder {$folder_id} exists and is active in Google Drive");
                return true;
            } else {
                log_message('warning', "Folder {$folder_id} is trashed or not a folder");
                return false;
            }
        } else if ($http_code === 404) {
            log_message('info', "Folder {$folder_id} not found in Google Drive (404)");
            return false;
        } else {
            log_message('warning', "Folder verification failed: HTTP {$http_code}, Response: {$response}");
            return false;
        }

    } catch (Exception $e) {
        log_message('error', 'Verify folder exists in Google Drive error: ' . $e->getMessage());
        return false;
    }
}

/**
 * สร้างโฟลเดอร์ใหม่ใน Google Drive และอัปเดตฐานข้อมูล
 */
private function recreate_folder_in_google_drive($member_id, $folder_name, $db_folder, $access_token) {
    try {
        log_message('info', "Recreating folder in Google Drive: {$folder_name}");

        // หา Users folder
        $users_folder_id = $this->find_or_create_users_folder($access_token);
        if (!$users_folder_id) {
            log_message('error', 'Cannot find or create Users folder for recreation');
            return [
                'success' => false,
                'error' => 'ไม่พบโฟลเดอร์ Users'
            ];
        }

        // สร้างโฟลเดอร์ใหม่ใน Google Drive
        $new_folder = $this->create_folder_in_google_drive($folder_name, $access_token, $users_folder_id);
        
        if (!$new_folder) {
            log_message('error', "Failed to recreate folder in Google Drive: {$folder_name}");
            return [
                'success' => false,
                'error' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้'
            ];
        }

        log_message('info', "Successfully recreated folder with new ID: {$new_folder['id']}");

        // อัปเดตฐานข้อมูล
        $update_data = [
            'folder_id' => $new_folder['id'],
            'folder_name' => $folder_name,
            'parent_folder_id' => $users_folder_id,
            'folder_path' => '/Organization Drive/Users/' . $folder_name,
            'updated_at' => date('Y-m-d H:i:s'),
            'is_active' => 1
        ];

        $this->db->where('id', $db_folder->id);
        $update_result = $this->db->update('tbl_google_drive_system_folders', $update_data);

        if (!$update_result) {
            log_message('error', "Failed to update database for folder: {$new_folder['id']}");
        }

        // อัปเดต member record
        $member_update = $this->update_member_personal_folder_id($member_id, $new_folder['id']);

        if (!$member_update) {
            log_message('error', "Failed to update member personal_folder_id: {$member_id}");
        }

        log_message('info', "Successfully updated database with new folder ID: {$new_folder['id']}");

        return [
            'folder_id' => $new_folder['id'],
            'folder_name' => $folder_name,
            'web_view_link' => $new_folder['webViewLink'] ?? "https://drive.google.com/drive/folders/{$new_folder['id']}",
            'message' => 'สร้างโฟลเดอร์ใหม่เรียบร้อย (แทนที่โฟลเดอร์เก่าที่หายไป)',
            'success' => true
        ];

    } catch (Exception $e) {
        log_message('error', 'Recreate folder in Google Drive error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

	private function create_new_personal_folder($member_id, $folder_name, $access_token) {
    try {
        log_message('info', "Creating new personal folder: {$folder_name}");

        // ดึงข้อมูล member
        $member = $this->db->select('m_fname, m_lname')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            return [
                'success' => false,
                'error' => 'ไม่พบข้อมูลสมาชิก'
            ];
        }

        // หา Users folder
        $users_folder_id = $this->find_or_create_users_folder($access_token);
        if (!$users_folder_id) {
            log_message('error', 'Cannot find or create Users folder');
            return [
                'success' => false,
                'error' => 'ไม่พบโฟลเดอร์ Users'
            ];
        }

        // สร้างโฟลเดอร์ใน Google Drive
        $personal_folder = $this->create_folder_in_google_drive($folder_name, $access_token, $users_folder_id);
        
        if (!$personal_folder) {
            log_message('error', "Failed to create folder in Google Drive: {$folder_name}");
            return [
                'success' => false,
                'error' => 'ไม่สามารถสร้างโฟลเดอร์ใน Google Drive ได้'
            ];
        }

        log_message('info', "Successfully created folder with ID: {$personal_folder['id']}");

        // บันทึกในฐานข้อมูล
        $db_insert_success = true;
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $folder_data = [
                'folder_name' => $folder_name,
                'folder_id' => $personal_folder['id'],
                'parent_folder_id' => $users_folder_id,
                'folder_type' => 'user',
                'folder_path' => '/Organization Drive/Users/' . $folder_name,
                'folder_description' => 'Personal folder for ' . $member->m_fname . ' ' . $member->m_lname . ' (Member ID: ' . $member_id . ')',
                'permission_level' => 'private',
                'created_by' => $this->session->userdata('m_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ];

            $db_insert_success = $this->db->insert('tbl_google_drive_system_folders', $folder_data);
            
            if (!$db_insert_success) {
                log_message('error', "Failed to insert folder data to database for: {$personal_folder['id']}");
            }
        }

        // อัปเดต member record
        $member_update = $this->update_member_personal_folder_id($member_id, $personal_folder['id']);

        if (!$member_update) {
            log_message('error', "Failed to update member personal_folder_id: {$member_id}");
        }

        log_message('info', "Successfully created and saved new personal folder for member {$member_id}");

        return [
            'folder_id' => $personal_folder['id'],
            'folder_name' => $folder_name,
            'web_view_link' => $personal_folder['webViewLink'] ?? "https://drive.google.com/drive/folders/{$personal_folder['id']}",
            'message' => 'สร้างโฟลเดอร์ใหม่เรียบร้อย',
            'success' => true,
            'database_saved' => $db_insert_success,
            'member_updated' => $member_update
        ];

    } catch (Exception $e) {
        log_message('error', 'Create new personal folder error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

	private function update_member_personal_folder_id($member_id, $folder_id) {
    try {
        $this->db->where('m_id', $member_id);
        $result = $this->db->update('tbl_member', [
            'personal_folder_id' => $folder_id,
            'last_storage_access' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            log_message('info', "Updated member {$member_id} with personal_folder_id: {$folder_id}");
        } else {
            log_message('error', "Failed to update member {$member_id} with personal_folder_id: {$folder_id}");
        }

        return $result;

    } catch (Exception $e) {
        log_message('error', 'Update member personal folder ID error: ' . $e->getMessage());
        return false;
    }
}

	
/**
 * 🆕 ฟังก์ชันสร้าง Personal Folder สำหรับผู้ใช้
 */
private function create_personal_folder_for_user($member) {
    try {
        // สร้างชื่อโฟลเดอร์
        $folder_name = $member->m_fname . ' ' . $member->m_lname . ' (ID: ' . $member->m_id . ')';
        
        // ตรวจสอบว่ามีโฟลเดอร์ชื่อเดียวกันหรือไม่
        $existing_folder = null;
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $existing_folder = $this->db->select('folder_id')
                                       ->from('tbl_google_drive_system_folders')
                                       ->where('folder_name', $folder_name)
                                       ->where('folder_type', 'user')
                                       ->where('is_active', 1)
                                       ->get()
                                       ->row();
        }

        if ($existing_folder) {
            // มีโฟลเดอร์ชื่อเดียวกันแล้ว - อัปเดต member record
            $this->db->where('m_id', $member->m_id)
                    ->update('tbl_member', [
                        'personal_folder_id' => $existing_folder->folder_id
                    ]);

            return [
                'success' => true,
                'folder_id' => $existing_folder->folder_id,
                'folder_name' => $folder_name,
                'folder_path' => '/Organization Drive/Users/' . $folder_name,
                'message' => 'ใช้โฟลเดอร์ที่มีอยู่แล้ว'
            ];
        }

        // สร้างโฟลเดอร์ใหม่ (จำลอง - ในการใช้งานจริงต้องเรียก Google Drive API)
        $personal_folder_id = 'folder_user_' . $member->m_id . '_' . uniqid();
        $folder_path = '/Organization Drive/Users/' . $folder_name;

        // บันทึกข้อมูลโฟลเดอร์ในฐานข้อมูล (ถ้ามีตาราง)
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            // หา Users folder parent
            $users_folder = $this->db->select('folder_id')
                                   ->from('tbl_google_drive_system_folders')
                                   ->where('folder_name', 'Users')
                                   ->where('folder_type', 'system')
                                   ->where('is_active', 1)
                                   ->get()
                                   ->row();

            $folder_data = [
                'folder_name' => $folder_name,
                'folder_id' => $personal_folder_id,
                'parent_folder_id' => $users_folder ? $users_folder->folder_id : null,
                'folder_type' => 'user',
                'folder_path' => $folder_path,
                'folder_description' => 'Personal folder for ' . $member->m_fname . ' ' . $member->m_lname,
                'permission_level' => 'private',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata('m_id')
            ];

            $this->db->insert('tbl_google_drive_system_folders', $folder_data);
        }

        // อัปเดต member record
        $this->db->where('m_id', $member->m_id)
                ->update('tbl_member', [
                    'personal_folder_id' => $personal_folder_id
                ]);

        // บันทึก log
        if (method_exists($this, 'log_action')) {
            $this->log_action(
                $this->session->userdata('m_id'),
                'create_personal_folder',
                "Created personal folder for {$member->m_fname} {$member->m_lname} (ID: {$member->m_id})"
            );
        }

        return [
            'success' => true,
            'folder_id' => $personal_folder_id,
            'folder_name' => $folder_name,
            'folder_path' => $folder_path,
            'web_view_link' => null, // จะมีค่าจริงเมื่อเชื่อมต่อ Google Drive API
            'message' => 'สร้างโฟลเดอร์ใหม่เรียบร้อย'
        ];

    } catch (Exception $e) {
        log_message('error', 'Create personal folder for user error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
}
	
	
	
	
	private function find_or_create_users_folder($access_token) {
    try {
        // ✅ ตรวจสอบในฐานข้อมูลก่อน
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $users_folder = $this->db->select('folder_id')
                                   ->from('tbl_google_drive_system_folders')
                                   ->where('folder_name', 'Users')
                                   ->where('folder_type', 'system')
                                   ->where('is_active', 1)
                                   ->get()
                                   ->row();

            if ($users_folder) {
                // ตรวจสอบว่า folder ยังมีอยู่ใน Google Drive หรือไม่
                if ($this->verify_folder_exists($users_folder->folder_id, $access_token)) {
                    return $users_folder->folder_id;
                }
            }
        }

        // ✅ หา Organization Drive root folder
        $org_folder_id = $this->find_organization_drive_folder($access_token);
        if (!$org_folder_id) {
            log_message('error', 'Cannot find Organization Drive folder');
            return null;
        }

        // ✅ สร้าง Users folder ใหม่
        $users_folder = $this->create_folder_in_google_drive('Users', $access_token, $org_folder_id);
        if (!$users_folder) {
            log_message('error', 'Cannot create Users folder');
            return null;
        }

        // ✅ บันทึกในฐานข้อมูล
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $folder_data = [
                'folder_name' => 'Users',
                'folder_id' => $users_folder['id'],
                'parent_folder_id' => $org_folder_id,
                'folder_type' => 'system',
                'folder_path' => '/Organization Drive/Users',
                'permission_level' => 'system',
                'folder_description' => 'ระบบโฟลเดอร์สำหรับผู้ใช้งานทั่วไป',
                'created_by' => $this->session->userdata('m_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ];

            $this->db->insert('tbl_google_drive_system_folders', $folder_data);
        }

        log_message('info', "Created Users folder: {$users_folder['id']}");
        return $users_folder['id'];

    } catch (Exception $e) {
        log_message('error', 'Find or create users folder error: ' . $e->getMessage());
        return null;
    }
}


/**
 * 🆕 หา Organization Drive folder หลัก (Updated)
 */
private function find_organization_drive_folder($access_token) {
    try {
        log_message('info', 'find_organization_drive_folder: Starting search');
        
        // ตรวจสอบในฐานข้อมูลก่อน
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $org_folder = $this->db->select('folder_id')
                                  ->from('tbl_google_drive_system_folders')
                                  ->where('folder_name', 'Organization Drive')
                                  ->where('folder_type', 'system')
                                  ->where('parent_folder_id IS NULL')
                                  ->where('is_active', 1)
                                  ->get()
                                  ->row();

            if ($org_folder) {
                // ตรวจสอบว่า folder ยังมีอยู่ใน Google Drive
                if ($this->verify_folder_exists($org_folder->folder_id, $access_token)) {
                    log_message('info', "find_organization_drive_folder: Found existing folder {$org_folder->folder_id}");
                    return $org_folder->folder_id;
                } else {
                    log_message('warning', "find_organization_drive_folder: Folder {$org_folder->folder_id} not found in Google Drive, creating new one");
                }
            }
        }

        // สร้าง Organization Drive folder ใหม่
        log_message('info', 'find_organization_drive_folder: Creating new Organization Drive folder');
        $org_folder = $this->create_folder_in_google_drive('Organization Drive', $access_token, null);
        
        if (!$org_folder) {
            log_message('error', 'find_organization_drive_folder: Failed to create Organization Drive folder');
            return null;
        }

        // บันทึกในฐานข้อมูล
        if ($this->db->table_exists('tbl_google_drive_system_folders')) {
            $folder_data = [
                'folder_name' => 'Organization Drive',
                'folder_id' => $org_folder['id'],
                'parent_folder_id' => null,
                'folder_type' => 'system',
                'folder_path' => '/Organization Drive',
                'permission_level' => 'system',
                'folder_description' => 'โฟลเดอร์หลักขององค์กร',
                'created_by' => $this->session->userdata('m_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ];

            $this->db->insert('tbl_google_drive_system_folders', $folder_data);
            log_message('info', "find_organization_drive_folder: Saved folder info to database");
        }

        log_message('info', "find_organization_drive_folder: Created Organization Drive with ID: {$org_folder['id']}");
        return $org_folder['id'];

    } catch (Exception $e) {
        log_message('error', 'find_organization_drive_folder error: ' . $e->getMessage());
        return null;
    }
}
	
	
/**
 * 🆕 สร้าง Folder ใน Google Drive จริง (Updated with better error handling)
 */
private function create_folder_in_google_drive($folder_name, $access_token, $parent_folder_id = null) {
    try {
        log_message('info', "create_folder_in_google_drive: Creating '{$folder_name}' in parent '{$parent_folder_id}'");
        
        // เตรียมข้อมูล metadata สำหรับโฟลเดอร์
        $metadata = [
            'name' => $folder_name,
            'mimeType' => 'application/vnd.google-apps.folder'
        ];

        // เพิ่ม parent folder ถ้ามี
        if ($parent_folder_id) {
            $metadata['parents'] = [$parent_folder_id];
        }

        // เรียก Google Drive API
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/files?fields=id,name,webViewLink,mimeType,parents',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($metadata),
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
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', "create_folder_in_google_drive cURL error: {$error}");
            return null;
        }

        log_message('info', "create_folder_in_google_drive API response: HTTP {$http_code}");
        log_message('info', "create_folder_in_google_drive API response body: {$response}");

        if ($http_code === 200) {
            $folder_data = json_decode($response, true);
            
            if ($folder_data && isset($folder_data['id'])) {
                log_message('info', "create_folder_in_google_drive: Successfully created folder '{$folder_name}' with ID: {$folder_data['id']}");
                
                return [
                    'id' => $folder_data['id'],
                    'name' => $folder_data['name'],
                    'webViewLink' => $folder_data['webViewLink'] ?? 'https://drive.google.com/drive/folders/' . $folder_data['id'],
                    'mimeType' => $folder_data['mimeType'],
                    'parents' => $folder_data['parents'] ?? []
                ];
            } else {
                log_message('error', "create_folder_in_google_drive: Invalid response format: {$response}");
            }
        } else {
            log_message('error', "create_folder_in_google_drive: API error HTTP {$http_code}, Response: {$response}");
            
            // แสดงข้อผิดพลาดที่เจาะจง
            if ($http_code === 401) {
                log_message('error', 'create_folder_in_google_drive: Access token expired or invalid');
            } elseif ($http_code === 403) {
                log_message('error', 'create_folder_in_google_drive: Insufficient permissions');
            } elseif ($http_code === 404) {
                log_message('error', 'create_folder_in_google_drive: Parent folder not found');
            }
        }

        return null;

    } catch (Exception $e) {
        log_message('error', 'create_folder_in_google_drive error: ' . $e->getMessage());
        return null;
    }
}

/**
 * 🆕 ตรวจสอบว่า Folder ยังมีอยู่ใน Google Drive หรือไม่
 */
/**
 * 🆕 ตรวจสอบว่า Folder ยังมีอยู่ใน Google Drive หรือไม่ (Updated)
 */
private function verify_folder_exists($folder_id, $access_token) {
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$folder_id}?fields=id,name,mimeType,trashed",
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
            log_message('error', "verify_folder_exists cURL error: {$error}");
            return false;
        }

        if ($http_code === 200) {
            $data = json_decode($response, true);
            
            // ตรวจสอบว่าเป็นโฟลเดอร์และไม่ถูกลบ
            if ($data && 
                isset($data['id']) && 
                $data['mimeType'] === 'application/vnd.google-apps.folder' &&
                (!isset($data['trashed']) || $data['trashed'] === false)) {
                return true;
            }
        }

        log_message('info', "verify_folder_exists: Folder {$folder_id} not found or trashed (HTTP: {$http_code})");
        return false;

    } catch (Exception $e) {
        log_message('error', 'verify_folder_exists error: ' . $e->getMessage());
        return false;
    }
}
	

/**
 * 🔄 ปรับปรุง get_setting() ถ้ายังไม่มี
 */
private function get_setting($key, $default = null) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_settings')) {
            return $default;
        }

        $setting = $this->db->select('setting_value')
                           ->from('tbl_google_drive_settings')
                           ->where('setting_key', $key)
                           ->where('is_active', 1)
                           ->get()
                           ->row();

        return $setting ? $setting->setting_value : $default;

    } catch (Exception $e) {
        log_message('error', 'Get setting error: ' . $e->getMessage());
        return $default;
    }
}

   

    /**
     * ดึงข้อมูล Logs ล่าสุด (AJAX) - แก้ไข JSON Response
     */
    public function get_recent_logs() {
    // ปิด error reporting ชั่วคราว
    $old_error_reporting = error_reporting(0);
    
    try {
        // ล้าง output buffer ทั้งหมด
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->force_json_error('Invalid request method', 400);
            return;
        }
        
        // ตรวจสอบ login
        if (!$this->session->userdata('m_id')) {
            $this->force_json_error('Please login first', 401);
            return;
        }
        
        // 🎯 รวมข้อมูลจากทั้งสองตาราง
        $logs = $this->get_combined_recent_logs();
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        
        // ตั้งค่า headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');
        http_response_code(200);
        
        // ส่ง JSON response
        echo json_encode([
            'success' => true,
            'message' => 'ดึงข้อมูล logs สำเร็จ',
            'data' => [
                'logs' => $logs,
                'storage_mode' => $storage_mode,
                'count' => count($logs),
                'sources' => $this->get_logs_sources_summary($logs)
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // Log error
        log_message('error', 'get_recent_logs error: ' . $e->getMessage());
        
        // ส่ง error response
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        http_response_code(500);
        
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล logs',
            'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error',
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
    }
    
    // คืนค่า error reporting
    error_reporting($old_error_reporting);
    exit;
}

	
	
	private function get_combined_recent_logs() {
    try {
        $all_logs = [];
        $date_limit = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        // 📋 1. ดึงจาก tbl_google_drive_activity_logs (ใหม่)
        if ($this->db->table_exists('tbl_google_drive_activity_logs')) {
            $this->db->select('gdal.id, gdal.member_id, gdal.action_type, gdal.action_description, gdal.created_at')
                    ->select('"success" as status', FALSE)
                    ->select('"activity_logs" as source_table', FALSE)
                    ->select('CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as member_name', FALSE)
                    ->select('m.m_email')
                    ->from('tbl_google_drive_activity_logs gdal')
                    ->join('tbl_member m', 'gdal.member_id = m.m_id', 'left')
                    ->where('gdal.created_at >=', $date_limit)
                    ->order_by('gdal.created_at', 'desc');
            
            $activity_query = $this->db->get();
            
            if ($activity_query && $activity_query->num_rows() > 0) {
                foreach ($activity_query->result() as $log) {
                    $all_logs[] = (object)[
                        'id' => 'activity_' . $log->id,
                        'original_id' => $log->id,
                        'member_id' => $log->member_id,
                        'action_type' => $log->action_type,
                        'action_description' => $log->action_description,
                        'status' => 'success',
                        'created_at' => $log->created_at,
                        'member_name' => trim($log->member_name) ?: 'System',
                        'm_email' => $log->m_email,
                        'source_table' => 'activity_logs',
                        'priority' => 1 // ให้ความสำคัญกับ activity logs
                    ];
                }
            }
        }
        
        // 📋 2. ดึงจาก tbl_google_drive_logs (เก่า)
        if ($this->db->table_exists('tbl_google_drive_logs')) {
            $this->db->select('gdl.id, gdl.member_id, gdl.action_type, gdl.action_description, gdl.status, gdl.created_at')
                    ->select('"google_drive_logs" as source_table', FALSE)
                    ->select('CONCAT(COALESCE(m.m_fname, ""), " ", COALESCE(m.m_lname, "")) as member_name', FALSE)
                    ->select('m.m_email')
                    ->from('tbl_google_drive_logs gdl')
                    ->join('tbl_member m', 'gdl.member_id = m.m_id', 'left')
                    ->where('gdl.created_at >=', $date_limit)
                    ->order_by('gdl.created_at', 'desc');
            
            $logs_query = $this->db->get();
            
            if ($logs_query && $logs_query->num_rows() > 0) {
                foreach ($logs_query->result() as $log) {
                    $all_logs[] = (object)[
                        'id' => 'logs_' . $log->id,
                        'original_id' => $log->id,
                        'member_id' => $log->member_id,
                        'action_type' => $log->action_type,
                        'action_description' => $log->action_description,
                        'status' => $log->status ?: 'success',
                        'created_at' => $log->created_at,
                        'member_name' => trim($log->member_name) ?: 'Unknown User',
                        'm_email' => $log->m_email,
                        'source_table' => 'google_drive_logs',
                        'priority' => 2 // ความสำคัญรอง
                    ];
                }
            }
        }
        
        // 🔄 3. รวมและเรียงลำดับใหม่
        if (!empty($all_logs)) {
            // เรียงตามวันที่ล่าสุดก่อน แล้วตามความสำคัญ
            usort($all_logs, function($a, $b) {
                $time_diff = strtotime($b->created_at) - strtotime($a->created_at);
                
                // ถ้าเวลาเหมือนกัน ให้เรียงตาม priority
                if ($time_diff == 0) {
                    return $a->priority - $b->priority;
                }
                
                return $time_diff;
            });
            
            // เอาเฉพาะ 15 รายการแรก (เผื่อมีข้อมูลซ้ำ)
            $all_logs = array_slice($all_logs, 0, 15);
            
            // 🧹 4. กรองข้อมูลซ้ำ (ถ้ามี)
            $all_logs = $this->remove_duplicate_logs($all_logs);
            
            // เอาเฉพาะ 10 รายการสุดท้าย
            $all_logs = array_slice($all_logs, 0, 10);
        }
        
        // 🎨 5. ทำความสะอาดข้อมูล
        foreach ($all_logs as $log) {
            $log->member_name = $log->member_name ?: 'System';
            $log->action_description = $log->action_description ?: 'System activity';
            $log->status = $log->status ?: 'success';
            
            // เพิ่ม icon และ label ตาม action_type
            $log->icon = $this->get_action_icon($log->action_type);
            $log->type_label = $this->get_action_type_label($log->action_type);
            $log->status_color = $this->get_status_color($log->status);
        }
        
        return $all_logs;
        
    } catch (Exception $e) {
        log_message('error', 'get_combined_recent_logs error: ' . $e->getMessage());
        return [];
    }
}

	private function remove_duplicate_logs($logs) {
    try {
        $unique_logs = [];
        $seen_combinations = [];
        
        foreach ($logs as $log) {
            // สร้าง unique key จาก member_id + action_type + เวลา (ปัดเศษเป็นนาที)
            $time_minute = date('Y-m-d H:i', strtotime($log->created_at));
            $unique_key = $log->member_id . '_' . $log->action_type . '_' . $time_minute;
            
            // ถ้ายังไม่เคยเจอ combination นี้
            if (!isset($seen_combinations[$unique_key])) {
                $unique_logs[] = $log;
                $seen_combinations[$unique_key] = true;
            } else {
                // ถ้าเจอแล้ว ให้เลือกที่มาจาก activity_logs (priority สูงกว่า)
                if ($log->priority < end($unique_logs)->priority) {
                    // แทนที่ตัวสุดท้าย
                    array_pop($unique_logs);
                    $unique_logs[] = $log;
                }
            }
        }
        
        return $unique_logs;
        
    } catch (Exception $e) {
        log_message('error', 'remove_duplicate_logs error: ' . $e->getMessage());
        return $logs; // คืนค่าเดิมถ้าเกิด error
    }
}
	
	
	private function get_action_icon($action_type) {
    $icons = [
        'connect' => 'fas fa-link',
        'disconnect' => 'fas fa-unlink',
        'setup' => 'fas fa-cogs',
        'change_mode' => 'fas fa-exchange-alt',
        'grant_access' => 'fas fa-user-plus',
        'revoke_access' => 'fas fa-user-minus',
        'create_folder' => 'fas fa-folder-plus',
        'delete_folder' => 'fas fa-folder-minus',
        'upload_file' => 'fas fa-upload',
        'download_file' => 'fas fa-download',
        'share_file' => 'fas fa-share-alt',
        'update_settings' => 'fas fa-cogs',
        'toggle_setting' => 'fas fa-toggle-on',
        'login' => 'fas fa-sign-in-alt',
        'logout' => 'fas fa-sign-out-alt',
        'error' => 'fas fa-exclamation-triangle'
    ];
    
    return $icons[$action_type] ?? 'fas fa-info-circle';
}
	
	
	private function get_action_type_label($action_type) {
    $labels = [
        'connect' => 'เชื่อมต่อ',
        'disconnect' => 'ตัดการเชื่อมต่อ',
        'setup' => 'ตั้งค่าระบบ',
        'change_mode' => 'เปลี่ยนโหมด',
        'grant_access' => 'อนุญาตการเข้าใช้',
        'revoke_access' => 'เพิกถอนสิทธิ์',
        'create_folder' => 'สร้างโฟลเดอร์',
        'delete_folder' => 'ลบโฟลเดอร์',
        'upload_file' => 'อัปโหลดไฟล์',
        'download_file' => 'ดาวน์โหลดไฟล์',
        'share_file' => 'แชร์ไฟล์',
        'update_settings' => 'อัปเดตการตั้งค่า',
        'toggle_setting' => 'เปลี่ยนการตั้งค่า',
        'login' => 'เข้าสู่ระบบ',
        'logout' => 'ออกจากระบบ',
        'error' => 'ข้อผิดพลาด'
    ];
    
    return $labels[$action_type] ?? ucfirst(str_replace('_', ' ', $action_type));
}
	
	
	private function get_status_color($status) {
    $colors = [
        'success' => 'text-green-500',
        'failed' => 'text-red-500',
        'pending' => 'text-yellow-500',
        'error' => 'text-red-500',
        'warning' => 'text-orange-500'
    ];
    
    return $colors[$status] ?? 'text-gray-500';
}

	
	
	
	
	private function get_logs_sources_summary($logs) {
    $sources = [
        'activity_logs' => 0,
        'google_drive_logs' => 0,
        'total' => count($logs)
    ];
    
    foreach ($logs as $log) {
        if (isset($log->source_table)) {
            $sources[$log->source_table]++;
        }
    }
    
    return $sources;
}
	
	
	
	/**
 * 🆕 ดึง logs สำหรับ System Storage
 */
private function get_system_storage_logs() {
    return $this->get_combined_recent_logs();
}

	

	/**
 * 🆕 ดึง logs สำหรับ User-based Storage
 */
private function get_user_based_logs() {
    return $this->get_combined_recent_logs();
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
	
	
	public function dashboard() {
    // ตรวจสอบโหมด Storage
    $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
    
    if ($storage_mode === 'centralized') {
        // ถ้าเป็น Centralized Mode ให้ redirect ไป System Dashboard
        redirect('google_drive_system/dashboard');
        return;
    }

    // ตรวจสอบสิทธิ์การเข้าถึง Google Drive (User-based Mode)
    $permission_check = $this->Google_drive_model->check_drive_permission($this->session->userdata('m_id'));
    
    if (!$permission_check['allowed']) {
        $this->session->set_flashdata('error', $permission_check['reason']);
        redirect('System_admin');
    }

    $data['storage_mode'] = $storage_mode;
    $data['permission_info'] = $permission_check;
    $data['is_connected'] = $this->Google_drive_model->is_google_connected($this->session->userdata('m_id'));
    $data['member_folders'] = [];
    $data['recent_logs'] = [];

    if ($data['is_connected']) {
        $data['member_folders'] = $this->Google_drive_model->get_member_folders($this->session->userdata('m_id'));
        $data['recent_logs'] = $this->Google_drive_model->get_member_logs($this->session->userdata('m_id'), 10);
    }

    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/google_drive_dashboard', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
}
	
	
	/**
 * ดึงข้อมูลโฟลเดอร์จาก Google Drive ID
 */
public function get_folder_info() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }
        
        $folder_id = $this->input->post('folder_id') ?: $this->input->get('folder_id');
        $member_id = $this->input->post('member_id') ?: $this->session->userdata('m_id');
        
        if (!$folder_id) {
            $this->output_json_error('Folder ID required');
            return;
        }
        
        // ดึงข้อมูลโฟลเดอร์
        $folder_info = $this->fetch_folder_info($folder_id, $member_id);
        
        if ($folder_info) {
            $this->output_json_success($folder_info, 'ดึงข้อมูลโฟลเดอร์สำเร็จ');
        } else {
            $this->output_json_error('ไม่สามารถดึงข้อมูลโฟลเดอร์ได้');
        }
        
    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * ดึงข้อมูลโฟลเดอร์จาก Google Drive API
 */
private function fetch_folder_info($folder_id, $member_id = null) {
    try {
        // ถ้าไม่มี member_id ให้ใช้ session
        if (!$member_id) {
            $member_id = $this->session->userdata('m_id');
        }
        
        // ดึง access token ของ member
        $access_token = $this->get_member_access_token($member_id);
        
        if (!$access_token) {
            // ลองใช้ System Storage access token
            $access_token = $this->get_system_access_token();
        }
        
        if (!$access_token) {
            return [
                'id' => $folder_id,
                'name' => 'Folder (ไม่สามารถเข้าถึงได้)',
                'webViewLink' => "https://drive.google.com/drive/folders/{$folder_id}",
                'accessible' => false,
                'error' => 'No access token available'
            ];
        }
        
        // เรียก Google Drive API
        $folder_data = $this->call_drive_api_get_file($folder_id, $access_token);
        
        if ($folder_data) {
            return [
                'id' => $folder_data['id'],
                'name' => $folder_data['name'],
                'webViewLink' => $folder_data['webViewLink'] ?? "https://drive.google.com/drive/folders/{$folder_id}",
                'mimeType' => $folder_data['mimeType'] ?? null,
                'createdTime' => $folder_data['createdTime'] ?? null,
                'modifiedTime' => $folder_data['modifiedTime'] ?? null,
                'accessible' => true,
                'size' => $folder_data['size'] ?? null,
                'owners' => $folder_data['owners'] ?? []
            ];
        }
        
        return [
            'id' => $folder_id,
            'name' => 'Unknown Folder',
            'webViewLink' => "https://drive.google.com/drive/folders/{$folder_id}",
            'accessible' => false,
            'error' => 'API call failed'
        ];
        
    } catch (Exception $e) {
        log_message('error', 'fetch_folder_info error: ' . $e->getMessage());
        
        return [
            'id' => $folder_id,
            'name' => 'Error Folder',
            'webViewLink' => "https://drive.google.com/drive/folders/{$folder_id}",
            'accessible' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * ดึง Access Token ของ Member
 */
private function get_member_access_token($member_id) {
    try {
        $member = $this->db->select('google_access_token, google_refresh_token, google_token_expires')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->where('google_drive_enabled', 1)
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
                if ($new_token && isset($new_token['access_token'])) {
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
        
        // แปลง token จาก JSON ถ้าจำเป็น
        $token_data = $member->google_access_token;
        if (is_string($token_data) && strpos($token_data, '{') === 0) {
            $parsed_token = json_decode($token_data, true);
            return $parsed_token['access_token'] ?? $token_data;
        }
        
        return $token_data;
        
    } catch (Exception $e) {
        log_message('error', 'get_member_access_token error: ' . $e->getMessage());
        return null;
    }
}

/**
 * ดึง System Access Token (สำหรับ Centralized Storage)
 */
/**
 * 🆕 ดึง System Access Token (Updated)
 */
private function get_system_access_token() {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
            log_message('error', 'get_system_access_token: System storage table not exists');
            return null;
        }
        
        $system_storage = $this->db->select('google_access_token, google_refresh_token, google_token_expires')
                                  ->from('tbl_google_drive_system_storage')
                                  ->where('is_active', 1)
                                  ->get()
                                  ->row();
        
        if (!$system_storage || !$system_storage->google_access_token) {
            log_message('error', 'get_system_access_token: No system storage or access token');
            return null;
        }
        
        // ตรวจสอบว่า token หมดอายุหรือไม่
        if ($system_storage->google_token_expires && strtotime($system_storage->google_token_expires) <= time()) {
            log_message('info', 'get_system_access_token: Token expired, attempting refresh');
            
            // ลอง refresh token
            if ($system_storage->google_refresh_token) {
                $new_token = $this->refresh_system_access_token($system_storage->google_refresh_token);
                if ($new_token && isset($new_token['access_token'])) {
                    log_message('info', 'get_system_access_token: Token refreshed successfully');
                    return $new_token['access_token'];
                } else {
                    log_message('error', 'get_system_access_token: Failed to refresh token');
                }
            }
            return null;
        }
        
        // แปลง token จาก JSON ถ้าจำเป็น
        $token_data = $system_storage->google_access_token;
        if (is_string($token_data) && strpos($token_data, '{') === 0) {
            $parsed_token = json_decode($token_data, true);
            return $parsed_token['access_token'] ?? $token_data;
        }
        
        return $token_data;
        
    } catch (Exception $e) {
        log_message('error', 'get_system_access_token error: ' . $e->getMessage());
        return null;
    }
}
/**
 * เรียก Google Drive API เพื่อดึงข้อมูลไฟล์/โฟลเดอร์
 */
private function call_drive_api_get_file($file_id, $access_token) {
    try {
        $url = "https://www.googleapis.com/drive/v3/files/{$file_id}";
        $fields = 'id,name,mimeType,webViewLink,createdTime,modifiedTime,size,owners(displayName,emailAddress)';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?fields=' . urlencode($fields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'User-Agent: PHP-GoogleDrive-Client/1.0'
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        if ($http_code === 200) {
            $data = json_decode($response, true);
            return $data;
        } elseif ($http_code === 404) {
            log_message('info', "File not found: {$file_id}");
            return null;
        } elseif ($http_code === 403) {
            log_message('warning', "Access denied to file: {$file_id}");
            return null;
        } else {
            log_message('error', "Google Drive API error: HTTP {$http_code}, Response: {$response}");
            return null;
        }
        
    } catch (Exception $e) {
        log_message('error', 'call_drive_api_get_file error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Refresh System Access Token
 */
private function refresh_system_access_token($refresh_token) {
    try {
        $client_id = $this->get_setting('google_client_id');
        $client_secret = $this->get_setting('google_client_secret');
        
        if (!$client_id || !$client_secret) {
            log_message('error', 'refresh_system_access_token: Missing OAuth credentials');
            return null;
        }
        
        log_message('info', 'refresh_system_access_token: Attempting to refresh token');
        
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
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            log_message('error', "refresh_system_access_token cURL error: {$error}");
            return null;
        }
        
        if ($http_code === 200) {
            $token = json_decode($response, true);
            if ($token && isset($token['access_token'])) {
                // อัปเดต System Storage token
                $this->db->where('is_active', 1);
                $this->db->update('tbl_google_drive_system_storage', [
                    'google_access_token' => json_encode($token),
                    'google_token_expires' => date('Y-m-d H:i:s', time() + ($token['expires_in'] ?? 3600)),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                log_message('info', 'refresh_system_access_token: Token refreshed and saved');
                return $token;
            }
        } else {
            log_message('error', "refresh_system_access_token: HTTP {$http_code}, Response: {$response}");
        }
        
        return null;
        
    } catch (Exception $e) {
        log_message('error', 'refresh_system_access_token error: ' . $e->getMessage());
        return null;
    }
}
	
	public function debug_personal_folder() {
    $member_id = $this->input->get('member_id') ?: $this->session->userdata('m_id');
    
    echo "<h1>🧪 Debug Personal Folder Creation</h1>";
    echo "<p>Member ID: {$member_id}</p>";
    echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
    
    try {
        // Step 1: ตรวจสอบ Member
        echo "<h2>Step 1: ตรวจสอบ Member</h2>";
        $member = $this->db->select('m_id, m_fname, m_lname, personal_folder_id, storage_access_granted')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();
        
        if ($member) {
            echo "<p>✅ พบ Member: {$member->m_fname} {$member->m_lname}</p>";
            echo "<p>Storage Access: " . ($member->storage_access_granted ? 'มีสิทธิ์' : 'ไม่มีสิทธิ์') . "</p>";
            echo "<p>Personal Folder ID: " . ($member->personal_folder_id ?: 'ยังไม่มี') . "</p>";
        } else {
            echo "<p>❌ ไม่พบ Member</p>";
            return;
        }
        
        // Step 2: ตรวจสอบ System Access Token
        echo "<h2>Step 2: ตรวจสอบ System Access Token</h2>";
        $access_token = $this->get_system_access_token();
        
        if ($access_token) {
            echo "<p>✅ System Access Token: " . substr($access_token, 0, 20) . "...</p>";
        } else {
            echo "<p>❌ ไม่พบ System Access Token</p>";
            
            // ตรวจสอบ System Storage
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                echo "<p>❌ ตาราง tbl_google_drive_system_storage ไม่มี</p>";
                return;
            }
            
            $system_storage = $this->db->select('*')
                                      ->from('tbl_google_drive_system_storage')
                                      ->where('is_active', 1)
                                      ->get()
                                      ->row();
            
            if ($system_storage) {
                echo "<p>✅ พบ System Storage: {$system_storage->google_account_email}</p>";
                echo "<p>Token Expires: {$system_storage->google_token_expires}</p>";
                echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
                
                if ($system_storage->google_token_expires && strtotime($system_storage->google_token_expires) <= time()) {
                    echo "<p>⚠️ Token หมดอายุแล้ว</p>";
                }
            } else {
                echo "<p>❌ ไม่พบ System Storage</p>";
            }
            return;
        }
        
        // Step 3: ตรวจสอบ Users Folder
        echo "<h2>Step 3: ตรวจสอบ Users Folder</h2>";
        $users_folder_id = $this->find_or_create_users_folder($access_token);
        
        if ($users_folder_id) {
            echo "<p>✅ Users Folder ID: {$users_folder_id}</p>";
            
            // ตรวจสอบว่า folder มีอยู่จริงใน Google Drive
            if ($this->verify_folder_exists($users_folder_id, $access_token)) {
                echo "<p>✅ Users Folder มีอยู่จริงใน Google Drive</p>";
            } else {
                echo "<p>❌ Users Folder ไม่มีใน Google Drive</p>";
            }
        } else {
            echo "<p>❌ ไม่สามารถหาหรือสร้าง Users Folder ได้</p>";
            return;
        }
        
        // Step 4: ทดสอบสร้าง Test Folder
        echo "<h2>Step 4: ทดสอบสร้าง Test Folder</h2>";
        $test_folder_name = "TEST_" . $member->m_fname . "_" . date('Ymd_His');
        
        echo "<p>กำลังสร้าง: {$test_folder_name}</p>";
        
        $test_folder = $this->create_folder_in_google_drive($test_folder_name, $access_token, $users_folder_id);
        
        if ($test_folder) {
            echo "<p>✅ สร้าง Test Folder สำเร็จ!</p>";
            echo "<p>Folder ID: {$test_folder['id']}</p>";
            echo "<p>Folder Name: {$test_folder['name']}</p>";
            echo "<p>Web View Link: <a href='{$test_folder['webViewLink']}' target='_blank'>เปิดดู</a></p>";
            
            // ลบ Test Folder
            echo "<h3>กำลังลบ Test Folder...</h3>";
            $delete_result = $this->delete_test_folder($test_folder['id'], $access_token);
            echo "<p>" . ($delete_result ? "✅ ลบ Test Folder สำเร็จ" : "⚠️ ไม่สามารถลบ Test Folder ได้") . "</p>";
            
        } else {
            echo "<p>❌ ไม่สามารถสร้าง Test Folder ได้</p>";
            return;
        }
        
        // Step 5: สร้าง Personal Folder จริง
        echo "<h2>Step 5: สร้าง Personal Folder จริง</h2>";
        
        if ($member->personal_folder_id) {
            echo "<p>⚠️ Member มี Personal Folder อยู่แล้ว: {$member->personal_folder_id}</p>";
            
            // ตรวจสอบว่า folder ยังมีอยู่
            if ($this->verify_folder_exists($member->personal_folder_id, $access_token)) {
                echo "<p>✅ Personal Folder มีอยู่จริงใน Google Drive</p>";
                echo "<p><a href='https://drive.google.com/drive/folders/{$member->personal_folder_id}' target='_blank'>เปิดดู Personal Folder</a></p>";
            } else {
                echo "<p>❌ Personal Folder ไม่มีใน Google Drive แล้ว - ต้องสร้างใหม่</p>";
                
                // Reset personal_folder_id และสร้างใหม่
                $this->db->where('m_id', $member_id)->update('tbl_member', ['personal_folder_id' => null]);
                $result = $this->create_user_personal_folder($member_id);
                
                if ($result) {
                    echo "<p>✅ สร้าง Personal Folder ใหม่สำเร็จ!</p>";
                    echo "<p>Folder ID: {$result['folder_id']}</p>";
                    echo "<p><a href='{$result['web_view_link']}' target='_blank'>เปิดดู Personal Folder ใหม่</a></p>";
                } else {
                    echo "<p>❌ ไม่สามารถสร้าง Personal Folder ใหม่ได้</p>";
                }
            }
        } else {
            echo "<p>📁 สร้าง Personal Folder ใหม่...</p>";
            
            $result = $this->create_user_personal_folder($member_id);
            
            if ($result) {
                echo "<p>✅ สร้าง Personal Folder สำเร็จ!</p>";
                echo "<p>Folder ID: {$result['folder_id']}</p>";
                echo "<p>Folder Name: {$result['folder_name']}</p>";
                echo "<p><a href='{$result['web_view_link']}' target='_blank'>เปิดดู Personal Folder</a></p>";
            } else {
                echo "<p>❌ ไม่สามารถสร้าง Personal Folder ได้</p>";
            }
        }
        
        echo "<h2>✅ Debug เสร็จสิ้น</h2>";
        echo "<p><a href='" . site_url('google_drive/manage') . "'>กลับไปหน้าจัดการ</a></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Debug Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
	

/**
 * ทดสอบการอ่านโฟลเดอร์
 */
public function test_folder_read() {
    $folder_id = $this->input->get('folder_id') ?: '1fml911JqknIQKChNAXWvxO1E-oUMklGD';
    $member_id = $this->input->get('member_id') ?: $this->session->userdata('m_id');
    
    echo "<h1>Test Folder Read</h1>";
    echo "<p>Folder ID: {$folder_id}</p>";
    echo "<p>Member ID: {$member_id}</p>";
    echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
    
    try {
        // ทดสอบดึงข้อมูลโฟลเดอร์
        $folder_info = $this->fetch_folder_info($folder_id, $member_id);
        
        echo "<h2>ผลลัพธ์:</h2>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        
        foreach ($folder_info as $key => $value) {
            $display_value = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
            echo "<tr><td><strong>{$key}</strong></td><td>{$display_value}</td></tr>";
        }
        echo "</table>";
        
        // ทดสอบ access token
        echo "<h2>Access Token Test:</h2>";
        $member_token = $this->get_member_access_token($member_id);
        echo "<p>Member Token: " . ($member_token ? 'Available (' . substr($member_token, 0, 20) . '...)' : 'Not available') . "</p>";
        
        $system_token = $this->get_system_access_token();
        echo "<p>System Token: " . ($system_token ? 'Available (' . substr($system_token, 0, 20) . '...)' : 'Not available') . "</p>";
        
        // ลิงก์ทดสอบ
        echo "<h2>Links:</h2>";
        echo "<p><a href='{$folder_info['webViewLink']}' target='_blank'>เปิดโฟลเดอร์ใน Google Drive</a></p>";
        
        // Form ทดสอบ folder อื่น
        echo "<h2>ทดสอบ Folder อื่น:</h2>";
        echo "<form method='get'>";
        echo "<input type='text' name='folder_id' value='{$folder_id}' style='width: 400px;' placeholder='Google Drive Folder ID'>";
        echo "<input type='submit' value='ทดสอบ'>";
        echo "</form>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}

/**
 * อัปเดตชื่อโฟลเดอร์ในฐานข้อมูลจาก Google Drive
 */
   public function update_folder_names() {
    // ตรวจสอบสิทธิ์
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }
    
    echo "<h1>Update Folder Names from Google Drive</h1>";
    echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
    
    try {
        if (!$this->db->table_exists('tbl_google_drive_folders')) {
            echo "<p style='color: red;'>ไม่พบตาราง tbl_google_drive_folders</p>";
            return;
        }
        
        // ดึงรายการโฟลเดอร์ทั้งหมด
        $folders = $this->db->select('id, member_id, folder_id, folder_name')
                           ->from('tbl_google_drive_folders')
                           ->where('is_active', 1)
                           ->get()
                           ->result();
        
        echo "<p>พบโฟลเดอร์ทั้งหมด: " . count($folders) . " รายการ</p>";
        
        $updated = 0;
        $errors = 0;
        
        foreach ($folders as $folder) {
            echo "<p>กำลังอัปเดต: {$folder->folder_id} ({$folder->folder_name})... ";
            
            $folder_info = $this->fetch_folder_info($folder->folder_id, $folder->member_id);
            
            if ($folder_info && $folder_info['accessible']) {
                $new_name = $folder_info['name'];
                
                if ($new_name !== $folder->folder_name) {
                    // อัปเดตชื่อใหม่
                    $this->db->where('id', $folder->id);
                    $result = $this->db->update('tbl_google_drive_folders', [
                        'folder_name' => $new_name,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    if ($result) {
                        echo "<span style='color: green;'>✅ อัปเดตเป็น: {$new_name}</span></p>";
                        $updated++;
                    } else {
                        echo "<span style='color: red;'>❌ อัปเดตไม่สำเร็จ</span></p>";
                        $errors++;
                    }
                } else {
                    echo "<span style='color: blue;'>ไม่เปลี่ยนแปลง</span></p>";
                }
            } else {
                echo "<span style='color: orange;'>⚠️ ไม่สามารถเข้าถึงได้</span></p>";
                $errors++;
            }
            
            // หน่วงเวลาเล็กน้อยเพื่อไม่ให้ hit API มากเกินไป
            usleep(200000); // 0.2 วินาที
        }
        
        echo "<h2>สรุปผลลัพธ์:</h2>";
        echo "<p>✅ อัปเดตสำเร็จ: {$updated} รายการ</p>";
        echo "<p>❌ ข้อผิดพลาด: {$errors} รายการ</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
	
	public function check_system_storage_setup() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $setup_status = $this->get_system_setup_status();
        
        $this->output_json_success([
            'setup_status' => $setup_status,
            'ready_to_use' => $setup_status['ready_to_use']
        ], 'ตรวจสอบสถานะการตั้งค่าสำเร็จ');

    } catch (Exception $e) {
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

	
	
	public function config() {
    // ตรวจสอบสิทธิ์
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
    
    $data['storage_mode'] = $storage_mode;
    
    if ($storage_mode === 'centralized') {
        // แสดงการกำหนดสิทธิ์สำหรับ Centralized Storage
        $data['system_storage'] = $this->get_system_storage_info();
        $data['storage_users'] = $this->get_storage_users();
    }

    // ดึงข้อมูลสิทธิ์ตามตำแหน่ง
    if ($this->db->table_exists('tbl_google_drive_position_permissions') && 
        $this->db->table_exists('tbl_google_drive_permission_types')) {
        
        $data['positions_with_permissions'] = $this->Google_drive_permissions_model->get_positions_with_permissions();
        $data['members_with_custom_permissions'] = $this->Google_drive_permissions_model->get_members_with_custom_permissions();
        $data['folder_templates'] = $this->Google_drive_permissions_model->get_folder_templates();
    } else {
        $data['positions_with_permissions'] = [];
        $data['members_with_custom_permissions'] = [];
        $data['folder_templates'] = [];
    }

    $this->load->view('member/header');
    $this->load->view('member/css');
    $this->load->view('member/sidebar');
    $this->load->view('member/google_drive_config', $data);
    $this->load->view('member/js');
    $this->load->view('member/footer');
}

	public function migrate_to_centralized() {
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
            $this->output_json_error('ไม่มีสิทธิ์ในการ Migrate ระบบ');
            return;
        }

        // ตรวจสอบว่ามี System Storage พร้อมหรือไม่
        $system_storage = $this->get_system_storage_info();
        if (!$system_storage || !$system_storage->folder_structure_created) {
            $this->output_json_error('กรุณาตั้งค่า System Storage ให้เสร็จสิ้นก่อน');
            return;
        }

        // เริ่มการ Migrate
        $migrate_result = $this->perform_migration_to_centralized();
        
        if ($migrate_result['success']) {
            // เปลี่ยนโหมดเป็น Centralized
            $this->set_setting('system_storage_mode', 'centralized');
            
            $this->output_json_success([
                'migrated_users' => $migrate_result['migrated_users'],
                'total_folders' => $migrate_result['total_folders'],
                'total_files' => $migrate_result['total_files']
            ], 'Migrate ไปยัง Centralized Storage เรียบร้อยแล้ว');
        } else {
            $this->output_json_error('การ Migrate ล้มเหลว: ' . $migrate_result['message']);
        }

    } catch (Exception $e) {
        log_message('error', 'Migrate to centralized error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🆕 เพิ่ม Function perform_migration_to_centralized()
 */
private function perform_migration_to_centralized() {
    try {
        $this->db->trans_start();

        $migrated_users = 0;
        $total_folders = 0;
        $total_files = 0;

        // ดึงรายการ User ที่เชื่อมต่อ Google Drive
        $connected_users = $this->db->select('m_id, m_fname, m_lname, ref_pid')
                                   ->from('tbl_member')
                                   ->where('google_drive_enabled', 1)
                                   ->get()
                                   ->result();

        foreach ($connected_users as $user) {
            // อัปเดต User เป็น Storage Access
            $this->db->where('m_id', $user->m_id);
            $this->db->update('tbl_member', [
                'storage_access_granted' => 1,
                'storage_quota_limit' => 1073741824, // 1GB default
                'storage_quota_used' => 0,
                'google_drive_enabled' => 0, // ปิด Google Drive เดิม
                'last_storage_access' => date('Y-m-d H:i:s')
            ]);

            // สร้างโฟลเดอร์ส่วนตัวใน System Storage
            $this->create_user_personal_folder($user->m_id);

            $migrated_users++;
        }

        // นับ folders และ files ที่มีอยู่
        if ($this->db->table_exists('tbl_google_drive_folders')) {
            $total_folders = $this->db->where('is_active', 1)
                                     ->count_all_results('tbl_google_drive_folders');
        }

        if ($this->db->table_exists('tbl_google_drive_sync')) {
            $total_files = $this->db->count_all('tbl_google_drive_sync');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            return [
                'success' => true,
                'migrated_users' => $migrated_users,
                'total_folders' => $total_folders,
                'total_files' => $total_files
            ];
        } else {
            throw new Exception('Database transaction failed');
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Perform migration error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

	public function init() {
    $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
    
    if ($storage_mode === 'centralized') {
        redirect('google_drive_system/dashboard');
    } else {
        redirect('google_drive/dashboard');
    }
}
/**
 * 🆕 เพิ่ม Function get_system_setup_status()
 */
private function get_system_setup_status() {
    try {
        $status = [
            'has_system_storage' => false,
            'folder_structure_created' => false,
            'ready_to_use' => false
        ];

        // ตรวจสอบ System Storage
        $system_storage = $this->get_system_storage_info();
        if ($system_storage) {
            $status['has_system_storage'] = true;
            $status['folder_structure_created'] = (bool)$system_storage->folder_structure_created;
        }

        // ตรวจสอบว่าพร้อมใช้งานหรือไม่
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
 * 🔄 แก้ไข Function test() เพิ่มการทดสอบ System Storage
 */
public function test() {
    echo "<h1>Google Drive Controller v3.1.0 + Centralized Storage - System Test</h1>";
    echo "<p>เวลา: " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>Environment: " . ENVIRONMENT . "</p>";
    echo "<p>User: " . $this->session->userdata('m_id') . "</p>";

    // ทดสอบโหมด Storage
    $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
    echo "<h2>🗄️ Storage Mode: " . ($storage_mode === 'centralized' ? 'Centralized Storage' : 'User-based Storage') . "</h2>";

    // ทดสอบ System Storage
    if ($storage_mode === 'centralized') {
        echo "<h3>System Storage Status:</h3>";
        $system_storage = $this->get_system_storage_info();
        
        if ($system_storage) {
            echo "<p>✅ System Storage: พร้อมใช้งาน</p>";
            echo "<p>📧 Google Account: " . $system_storage->google_account_email . "</p>";
            echo "<p>💾 Storage Used: " . number_format($system_storage->storage_usage_percent, 2) . "%</p>";
            echo "<p>📁 Total Folders: " . $system_storage->total_folders . "</p>";
            echo "<p>📄 Total Files: " . $system_storage->total_files . "</p>";
            echo "<p>👥 Active Users: " . $system_storage->active_users . "</p>";
            echo "<p>🏗️ Folder Structure: " . ($system_storage->folder_structure_created ? 'สร้างแล้ว' : 'ยังไม่สร้าง') . "</p>";
        } else {
            echo "<p>❌ System Storage: ยังไม่ได้ตั้งค่า</p>";
        }

        // ทดสอบตาราง System Storage
        echo "<h3>System Storage Tables:</h3>";
        $system_tables = [
            'tbl_google_drive_system_storage',
            'tbl_google_drive_system_folders', 
            'tbl_google_drive_system_files',
            'tbl_google_drive_user_access'
        ];
        
        foreach ($system_tables as $table) {
            $exists = $this->db->table_exists($table);
            echo "<p>{$table}: " . ($exists ? "[OK]" : "[MISSING]") . "</p>";
            
            if ($exists) {
                $count = $this->db->count_all($table);
                echo "<p>└─ Records: {$count}</p>";
            }
        }
    }

    // ทดสอบการตั้งค่าใหม่
    echo "<h2>⚙️ New Settings:</h2>";
    $new_settings = [
        'system_storage_enabled',
        'system_storage_mode', 
        'auto_create_user_folders',
        'default_user_quota',
        'max_file_size_system'
    ];
    
    foreach ($new_settings as $setting) {
        $value = $this->get_setting($setting, 'ไม่ได้ตั้งค่า');
        echo "<p>{$setting}: {$value}</p>";
    }

    // ลิงก์ทดสอบ
    echo "<h2>🔗 Test Links:</h2>";
    echo "<p><a href='" . site_url('google_drive_system/dashboard') . "' target='_blank'>System Storage Dashboard</a></p>";
    echo "<p><a href='" . site_url('google_drive_system/setup') . "' target='_blank'>System Storage Setup</a></p>";
    echo "<p><a href='" . site_url('google_drive/settings') . "' target='_blank'>Google Drive Settings</a></p>";

    echo "<h2>✅ Status Summary:</h2>";
    if ($storage_mode === 'centralized' && $system_storage) {
        echo "<p style='color: green; font-weight: bold;'>[SUCCESS] Centralized Storage พร้อมใช้งาน!</p>";
    } elseif ($storage_mode === 'user_based') {
        echo "<p style='color: blue; font-weight: bold;'>[INFO] ใช้งาน User-based Storage แบบเดิม</p>";
    } else {
        echo "<p style='color: orange; font-weight: bold;'>[WARNING] Centralized Storage ยังไม่ได้ตั้งค่า</p>";
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
	
	
	
	/**
 * 🆕 Toggle Google Drive Access สำหรับ System Storage Users
 */
public function toggle_user_drive_access() {
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
            $this->output_json_error('ไม่มีสิทธิ์ในการจัดการผู้ใช้งาน');
            return;
        }

        $member_id = $this->input->post('member_id');
        $enabled = $this->input->post('enabled') ? 1 : 0; // 1 = เปิด, 0 = ปิด
        
        if (!$member_id) {
            $this->output_json_error('ไม่พบข้อมูลผู้ใช้งาน');
            return;
        }

        // ตรวจสอบว่าเป็น Centralized Mode หรือไม่
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        if ($storage_mode !== 'centralized') {
            $this->output_json_error('ฟีเจอร์นี้ใช้ได้เฉพาะโหมด Centralized Storage');
            return;
        }

        // ดึงข้อมูลผู้ใช้งาน
        $member = $this->db->select('m_id, m_fname, m_lname, google_drive_enabled, storage_access_granted')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->output_json_error('ไม่พบข้อมูลผู้ใช้งาน');
            return;
        }

        // ✅ อัปเดตสถานะ - เปลี่ยนจาก google_drive_enabled เป็น storage_access_granted
        $update_data = [
            'storage_access_granted' => $enabled  // ✅ เปลี่ยนตรงนี้
        ];

        // ถ้าปิดการใช้งาน ให้ reset ข้อมูล Google Drive (ตาม Database Schema)
        if (!$enabled) {
            $update_data = array_merge($update_data, [
                'google_email' => null
            ]);
        }

        $this->db->where('m_id', $member_id);
        $result = $this->db->update('tbl_member', $update_data);

        if ($result) {
            // บันทึก Log
            $action_desc = $enabled ? 
                "เปิดใช้งาน Google Drive สำหรับ {$member->m_fname} {$member->m_lname}" : 
                "ปิดใช้งาน Google Drive สำหรับ {$member->m_fname} {$member->m_lname}";
            
            $this->log_action($this->session->userdata('m_id'), 'toggle_user_access', $action_desc);

            $this->output_json_success([
                'member_id' => $member_id,
                'enabled' => $enabled,
                'member_name' => $member->m_fname . ' ' . $member->m_lname
            ], $action_desc);
        } else {
            $this->output_json_error('ไม่สามารถอัปเดตสถานะได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Toggle user drive access error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🆕 Bulk Toggle Google Drive Access สำหรับหลายผู้ใช้งาน
 */
public function bulk_toggle_drive_access() {
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
            $this->output_json_error('ไม่มีสิทธิ์ในการจัดการผู้ใช้งาน');
            return;
        }

        $member_ids = $this->input->post('member_ids'); // Array of member IDs
        $enabled = $this->input->post('enabled') ? 1 : 0;
        
        if (empty($member_ids) || !is_array($member_ids)) {
            $this->output_json_error('ไม่พบรายการผู้ใช้งาน');
            return;
        }

        $updated_count = 0;
        $errors = [];

        foreach ($member_ids as $member_id) {
            $member = $this->db->select('m_fname, m_lname')
                              ->from('tbl_member')
                              ->where('m_id', $member_id)
                              ->get()
                              ->row();

            if ($member) {
                // ✅ เปลี่ยนจาก google_drive_enabled เป็น storage_access_granted
                $update_data = ['storage_access_granted' => $enabled];  // ✅ เปลี่ยนตรงนี้
                
                // ถ้าปิดการใช้งาน ให้ reset ข้อมูล Google Drive (ตาม Database Schema)
                if (!$enabled) {
                    $update_data = array_merge($update_data, [
                        'google_email' => null
                    ]);
                }

                $this->db->where('m_id', $member_id);
                $result = $this->db->update('tbl_member', $update_data);

                if ($result) {
                    $updated_count++;
                } else {
                    $errors[] = "ไม่สามารถอัปเดต {$member->m_fname} {$member->m_lname}";
                }
            } else {
                $errors[] = "ไม่พบผู้ใช้งาน ID: {$member_id}";
            }
        }

        // บันทึก Log
        $action_desc = $enabled ? 
            "เปิดใช้งาน Google Drive สำหรับ {$updated_count} ผู้ใช้งาน" : 
            "ปิดใช้งาน Google Drive สำหรับ {$updated_count} ผู้ใช้งาน";
        
        $this->log_action($this->session->userdata('m_id'), 'bulk_toggle_access', $action_desc);

        $this->output_json_success([
            'updated_count' => $updated_count,
            'total_requested' => count($member_ids),
            'errors' => $errors,
            'enabled' => $enabled
        ], $action_desc . (empty($errors) ? '' : ' (มีข้อผิดพลาดบางรายการ)'));

    } catch (Exception $e) {
        log_message('error', 'Bulk toggle drive access error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🆕 ดึงสถานะ Google Drive ของ User (Fixed Database Schema)
 */
public function get_user_drive_status() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $member_id = $this->input->post('member_id') ?: $this->input->get('member_id');
        
        if (!$member_id) {
            $this->output_json_error('ไม่พบข้อมูลผู้ใช้งาน');
            return;
        }

        $member = $this->db->select('m_id, m_fname, m_lname, google_drive_enabled, 
                                   google_email, storage_access_granted')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->output_json_error('ไม่พบข้อมูลผู้ใช้งาน');
            return;
        }

        $this->output_json_success([
            'member_id' => $member->m_id,
            'member_name' => $member->m_fname . ' ' . $member->m_lname,
            'google_drive_enabled' => (bool)$member->google_drive_enabled,
            'google_email' => $member->google_email,
            'storage_access_granted' => (bool)$member->storage_access_granted,
            'has_google_connection' => !empty($member->google_email)
        ], 'ดึงสถานะผู้ใช้งานสำเร็จ');

    } catch (Exception $e) {
        log_message('error', 'Get user drive status error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🆕 Reset Google Drive Connection ของ User
 */
public function reset_user_google_connection() {
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
            $this->output_json_error('ไม่มีสิทธิ์ในการจัดการผู้ใช้งาน');
            return;
        }

        $member_id = $this->input->post('member_id');
        
        if (!$member_id) {
            $this->output_json_error('ไม่พบข้อมูลผู้ใช้งาน');
            return;
        }

        $member = $this->db->select('m_fname, m_lname, google_access_token')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->output_json_error('ไม่พบข้อมูลผู้ใช้งาน');
            return;
        }

        // Revoke Google Token (ถ้ามี)
        if (!empty($member->google_access_token)) {
            $this->safe_revoke_google_token($member->google_access_token);
        }

        // Reset ข้อมูล Google Drive (ตาม Database Schema)
        $reset_data = [
            'google_email' => null
        ];

        $this->db->where('m_id', $member_id);
        $result = $this->db->update('tbl_member', $reset_data);

        if ($result) {
            // บันทึก Log
            $this->log_action($this->session->userdata('m_id'), 'reset_google_connection', 
                "Reset Google Connection สำหรับ {$member->m_fname} {$member->m_lname}");

            $this->output_json_success([
                'member_id' => $member_id,
                'member_name' => $member->m_fname . ' ' . $member->m_lname
            ], 'Reset Google Connection เรียบร้อยแล้ว');
        } else {
            $this->output_json_error('ไม่สามารถ Reset ได้');
        }

    } catch (Exception $e) {
        log_message('error', 'Reset user google connection error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

/**
 * 🔄 แก้ไข get_storage_users() เพิ่มข้อมูล google_drive_enabled (Fixed Database Columns)
 */
private function get_storage_users($search = '', $limit = 50, $offset = 0) {
    try {
        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, 
                          m.storage_access_granted, m.storage_quota_limit, 
                          m.storage_quota_used, m.last_storage_access,
                          m.google_drive_enabled, m.google_email,
                          p.pname');
                          
        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $this->db->select('COUNT(sf.id) as total_files', false);
            $this->db->join('tbl_google_drive_system_files sf', 'm.m_id = sf.uploaded_by', 'left');
        } else {
            $this->db->select('0 as total_files', false);
        }

        $this->db->from('tbl_member m')
                ->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
                // ❌ ลบ: ->where('m.storage_access_granted', 1);
                // ✅ เปลี่ยนเป็น: แสดงผู้ใช้ทั้งหมด

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('m.m_fname', $search)
                    ->or_like('m.m_lname', $search)
                    ->or_like('m.m_email', $search)
                    ->group_end();
        }

        if ($this->db->table_exists('tbl_google_drive_system_files')) {
            $this->db->group_by('m.m_id');
        }

        // ✅ เรียงลำดับ: ผู้ที่มีสิทธิ์ขึ้นก่อน แล้วเรียงตามเวลาเข้าใช้ล่าสุด
        $this->db->order_by('m.storage_access_granted', 'desc')
                ->order_by('m.last_storage_access', 'desc')
                ->limit($limit, $offset);

        return $this->db->get()->result();

    } catch (Exception $e) {
        log_message('error', 'Get storage users error: ' . $e->getMessage());
        return [];
    }
}


/**
 * 🔄 แก้ไข get_connected_members() ให้ตรงกับ Database Schema
 */
private function get_connected_members($search = '', $limit = 50, $offset = 0) {
    $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.google_email, 
                      m.google_drive_enabled, p.pname');

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

    $this->db->order_by('m.google_drive_enabled', 'desc')
            ->limit($limit, $offset);

    return $this->db->get()->result();
}
	
	


/**
 * 🔧 Simple Toggle (Minimal Version)
 */
public function simple_toggle() {
    // ปิด error reporting
    error_reporting(0);
    
    // ล้าง output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    try {
        // ตรวจสอบพื้นฐาน
        if (!$this->input->is_ajax_request()) {
            $this->simple_json_response(false, 'Not AJAX request');
            return;
        }
        
        if (!$this->session->userdata('m_id')) {
            $this->simple_json_response(false, 'Not logged in');
            return;
        }
        
        // รับข้อมูล
        $member_id = $this->input->post('member_id');
        $enabled = $this->input->post('enabled') ? 1 : 0;
        
        if (!$member_id) {
            $this->simple_json_response(false, 'Member ID required');
            return;
        }
        
        // ✅ อัปเดตฐานข้อมูล - เปลี่ยนจาก google_drive_enabled เป็น storage_access_granted
        $this->db->where('m_id', $member_id);
        $result = $this->db->update('tbl_member', [
            'storage_access_granted' => $enabled  // ✅ เปลี่ยนตรงนี้
        ]);
        
        if ($result !== false) {
            $this->simple_json_response(true, 'Updated successfully', [
                'member_id' => $member_id,
                'enabled' => $enabled
            ]);
        } else {
            $this->simple_json_response(false, 'Database update failed');
        }
        
    } catch (Exception $e) {
        $this->simple_json_response(false, 'Exception: ' . $e->getMessage());
    }
}


/**
 * 🆕 Simple JSON Response (No Dependencies)
 */
private function simple_json_response($success, $message, $data = []) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');
    
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'time' => date('Y-m-d H:i:s')
    ]);
    
    exit;
}
	
	
	public function users() {
        $search = $this->input->get('search') ?: '';
        $page = $this->input->get('page') ?: 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $data['users'] = $this->get_system_users($search, $limit, $offset);
        $data['total_users'] = $this->count_system_users($search);
        $data['pagination'] = $this->create_pagination($data['total_users'], $limit, $page);
        $data['search'] = $search;
        $data['system_storage'] = $this->get_system_storage_info();

        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_users', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * 📁 จัดการโฟลเดอร์ System Storage
     */
    public function folders() {
        $data['folders'] = $this->get_system_folders();
        $data['folder_tree'] = $this->build_folder_tree();
        $data['system_storage'] = $this->get_system_storage_info();

        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_folders', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * 📊 รายงานการใช้งาน
     */
    public function reports() {
        $type = $this->input->get('type') ?: 'overview';
        
        $data['report_type'] = $type;
        $data['system_storage'] = $this->get_system_storage_info();
        
        switch ($type) {
            case 'storage':
                $data['storage_reports'] = $this->get_storage_reports();
                break;
            case 'users':
                $data['user_reports'] = $this->get_user_reports();
                break;
            case 'activities':
                $data['activity_reports'] = $this->get_activity_reports();
                break;
            default:
                $data['overview_reports'] = $this->get_overview_reports();
        }

        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_reports', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

    /**
     * 📈 ดูการใช้งานของผู้ใช้รายบุคคล
     */
    public function user_usage() {
        $user_id = $this->input->get('user_id');
        
        if (!$user_id) {
            show_404();
        }

        $data['user'] = $this->get_user_details($user_id);
        $data['user_storage'] = $this->get_user_storage_usage($user_id);
        $data['user_files'] = $this->get_user_files($user_id);
        $data['user_activities'] = $this->get_user_activities($user_id);
        $data['system_storage'] = $this->get_system_storage_info();

        if (!$data['user']) {
            show_404();
        }

        $this->load->view('member/header');
        $this->load->view('member/css');
        $this->load->view('member/sidebar');
        $this->load->view('member/google_drive_system_user_usage', $data);
        $this->load->view('member/js');
        $this->load->view('member/footer');
    }

  



    /**
     * 🔄 อัปเดต Storage Quota ของผู้ใช้
     */
    public function update_user_quota() {
        try {
            if (!$this->input->is_ajax_request()) {
                $this->output_json_error('Invalid request method');
                return;
            }

            $user_id = $this->input->post('user_id');
            $new_quota = $this->input->post('new_quota'); // in bytes
            $new_quota_mb = $this->input->post('new_quota_mb');
            $is_unlimited = $this->input->post('is_unlimited') === '1';

            if (!$user_id || !$new_quota) {
                $this->output_json_error('ข้อมูลไม่ครบถ้วน');
                return;
            }

            // ตรวจสอบผู้ใช้
            $user = $this->db->select('m_id, m_fname, m_lname, storage_quota_used')
                            ->from('tbl_member')
                            ->where('m_id', $user_id)
                            ->get()
                            ->row();

            if (!$user) {
                $this->output_json_error('ไม่พบข้อมูลผู้ใช้');
                return;
            }

            // อัปเดต Quota
            $this->db->where('m_id', $user_id);
            $result = $this->db->update('tbl_member', [
                'storage_quota_limit' => $new_quota,
                'last_storage_access' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                // บันทึก Log
                $quota_text = $is_unlimited ? 'Unlimited' : $new_quota_mb . ' MB';
                $this->log_action('update_quota', 
                    "อัปเดต Storage Quota ของ {$user->m_fname} {$user->m_lname} เป็น {$quota_text}");

                $this->output_json_success([
                    'user_id' => $user_id,
                    'new_quota_mb' => $new_quota_mb,
                    'is_unlimited' => $is_unlimited
                ], 'อัปเดต Storage Quota สำเร็จ');
            } else {
                $this->output_json_error('ไม่สามารถอัปเดต Quota ได้');
            }

        } catch (Exception $e) {
            log_message('error', 'Update user quota error: ' . $e->getMessage());
            $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // =========================
    // Private Helper Functions
    // =========================

    

    /**
     * ดึงสถิติระบบ
     */
    private function get_system_statistics() {
        return [
            'total_users' => $this->db->where('storage_access_granted', 1)->count_all_results('tbl_member'),
            'total_folders' => $this->db->where('is_active', 1)->count_all_results('tbl_google_drive_system_folders'),
            'total_files' => $this->db->table_exists('tbl_google_drive_system_files') ? 
                $this->db->count_all('tbl_google_drive_system_files') : 0,
            'storage_usage_gb' => 0 // คำนวณจาก system storage
        ];
    }

    /**
     * ดึงกิจกรรมล่าสุด
     */
    private function get_recent_activities() {
        try {
            if (!$this->db->table_exists('tbl_google_drive_activity_logs')) {
                return [];
            }

            return $this->db->select('gdal.*, m.m_fname, m.m_lname')
                           ->from('tbl_google_drive_activity_logs gdal')
                           ->join('tbl_member m', 'gdal.member_id = m.m_id', 'left')
                           ->order_by('gdal.created_at', 'desc')
                           ->limit(10)
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get recent activities error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงการแบ่งพื้นที่ Storage
     */
    private function get_storage_breakdown() {
        // คำนวณการใช้งาน Storage แยกตาม User/Department
        return [
            'by_users' => [],
            'by_departments' => [],
            'by_file_types' => []
        ];
    }

    /**
     * ดึงผู้ใช้งาน System Storage
     */
    private function get_system_users($search = '', $limit = 50, $offset = 0) {
        $this->db->select('m.*, p.pname')
                ->from('tbl_member m')
                ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                ->where('m.storage_access_granted', 1);

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('m.m_fname', $search)
                    ->or_like('m.m_lname', $search)
                    ->or_like('m.m_email', $search)
                    ->group_end();
        }

        return $this->db->order_by('m.last_storage_access', 'desc')
                       ->limit($limit, $offset)
                       ->get()
                       ->result();
    }

    /**
     * นับจำนวนผู้ใช้งาน
     */
    private function count_system_users($search = '') {
        $this->db->from('tbl_member m')
                ->where('m.storage_access_granted', 1);

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('m.m_fname', $search)
                    ->or_like('m.m_lname', $search)
                    ->or_like('m.m_email', $search)
                    ->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * ดึงรายละเอียดผู้ใช้
     */
    private function get_user_details($user_id) {
        return $this->db->select('m.*, p.pname')
                       ->from('tbl_member m')
                       ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                       ->where('m.m_id', $user_id)
                       ->get()
                       ->row();
    }

    /**
     * ดึงการใช้งาน Storage ของผู้ใช้
     */
    private function get_user_storage_usage($user_id) {
        // คำนวณการใช้งานของผู้ใช้รายบุคคล
        return [
            'total_files' => 0,
            'total_size' => 0,
            'quota_used_percent' => 0
        ];
    }

    /**
     * ดึงไฟล์ของผู้ใช้
     */
    private function get_user_files($user_id) {
        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return [];
        }

        return $this->db->select('*')
                       ->from('tbl_google_drive_system_files')
                       ->where('uploaded_by', $user_id)
                       ->order_by('created_at', 'desc')
                       ->limit(20)
                       ->get()
                       ->result();
    }

    /**
     * ดึงกิจกรรมของผู้ใช้
     */
    private function get_user_activities($user_id) {
        if (!$this->db->table_exists('tbl_google_drive_activity_logs')) {
            return [];
        }

        return $this->db->select('*')
                       ->from('tbl_google_drive_activity_logs')
                       ->where('member_id', $user_id)
                       ->order_by('created_at', 'desc')
                       ->limit(20)
                       ->get()
                       ->result();
    }

    /**
     * สร้าง Pagination
     */
    private function create_pagination($total, $limit, $page) {
        return [
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'total_pages' => ceil($total / $limit)
        ];
    }

    

   

    // ดึงข้อมูลสำหรับ Setup, Reports และอื่นๆ (เพิ่มได้ตามต้องการ)
    private function get_setup_status() {
        return ['completed' => true, 'steps' => []];
    }

    private function get_oauth_settings() {
        return [];
    }



    private function build_folder_tree() {
        return [];
    }

    private function get_storage_reports() {
        return [];
    }

    private function get_user_reports() {
        return [];
    }

    private function get_activity_reports() {
        return [];
    }

    private function get_overview_reports() {
        return [];
    }
	
	
	
	/**
 * 🆕 Export ผู้ใช้งาน Storage (สำหรับ Centralized Storage)
 */
public function export_users() {
    // ตรวจสอบสิทธิ์
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    try {
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        
        if ($storage_mode === 'centralized') {
            // Export สำหรับ Centralized Storage
            $users = $this->get_storage_users('', 1000); // ดึงทั้งหมด
            $filename = 'centralized_storage_users_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'ID', 'ชื่อ-นามสกุล', 'อีเมล', 'ตำแหน่ง', 'Google Drive Status', 
                'Google Email', 'Storage Quota (MB)', 'Storage Used (MB)', 'Usage %', 
                'เข้าใช้ล่าสุด'
            ];
            
            $this->export_csv_data($filename, $headers, $users, 'centralized');
        } else {
            // Export สำหรับ User-based Storage (ใช้ method เดิม)
            redirect('google_drive/export_members');
        }

    } catch (Exception $e) {
        log_message('error', 'Export users error: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาดในการ Export: ' . $e->getMessage());
    }
}

/**
 * 🆕 Export ข้อมูลเป็น CSV แบบ Universal (รองรับภาษาไทย)
 */
private function export_csv_data($filename, $headers, $data, $type = 'centralized') {
    try {
        // ตั้งค่า internal encoding เป็น UTF-8
        mb_internal_encoding('UTF-8');
        
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers สำหรับ CSV download (รองรับภาษาไทย)
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($filename));
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        
        // เปิด output stream
        $output = fopen('php://output', 'w');
        
        // เพิ่ม UTF-8 BOM สำหรับให้ Excel อ่านภาษาไทยได้
        fwrite($output, "\xEF\xBB\xBF");
        
        // เขียน headers (แปลงเป็น UTF-8)
        $utf8_headers = array_map(function($header) {
            return $this->ensure_utf8($header);
        }, $headers);
        fputcsv($output, $utf8_headers);
        
        // เขียนข้อมูล
        foreach ($data as $row) {
            if ($type === 'centralized') {
                $csv_row = [
                    $row->m_id,
                    $this->ensure_utf8($row->m_fname . ' ' . $row->m_lname),
                    $this->ensure_utf8($row->m_email ?: '-'),
                    $this->ensure_utf8($row->pname ?: '-'),
                    $row->google_drive_enabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน',
                    $this->ensure_utf8($row->google_email ?: '-'),
                    number_format($row->storage_quota_limit / 1048576, 1), // MB
                    number_format($row->storage_quota_used / 1048576, 1), // MB
                    $row->storage_quota_limit > 0 ? 
                        number_format(($row->storage_quota_used / $row->storage_quota_limit) * 100, 1) . '%' : '0%',
                    $row->last_storage_access ? 
                        date('d/m/Y H:i', strtotime($row->last_storage_access)) : 'ยังไม่เคย'
                ];
            } else {
                // สำหรับ User-based Storage (ใช้รูปแบบเดิม)
                $csv_row = [
                    $row->m_id,
                    $this->ensure_utf8($row->m_fname . ' ' . $row->m_lname),
                    $this->ensure_utf8($row->m_email ?: '-'),
                    $this->ensure_utf8($row->google_email ?: '-'),
                    $this->ensure_utf8($row->pname ?: '-'),
                    $row->total_folders ?: '0',
                    $row->google_connected_at ? 
                        date('d/m/Y H:i', strtotime($row->google_connected_at)) : '-',
                    $row->google_account_verified ? 'ยืนยันแล้ว' : 'ยังไม่ยืนยัน'
                ];
            }
            
            // แปลงทุกค่าเป็น UTF-8
            $utf8_row = array_map([$this, 'ensure_utf8'], $csv_row);
            fputcsv($output, $utf8_row);
        }
        
        fclose($output);
        exit;

    } catch (Exception $e) {
        log_message('error', 'Export CSV error: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * 🔄 แก้ไข export_members() ให้รองรับทั้งสองระบบ
 */
public function export_members() {
    // ตรวจสอบสิทธิ์
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    try {
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        
        if ($storage_mode === 'centralized') {
            // ถ้าเป็น Centralized Mode ให้ redirect ไป export_users
            redirect('google_drive/export_users');
            return;
        }

        // Export สำหรับ User-based Storage (แบบเดิม)
        $members = $this->get_connected_members('', 1000); // ดึงทั้งหมด
        $filename = 'google_drive_members_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'ID', 'ชื่อ-นามสกุล', 'อีเมล', 'Google Account', 'ตำแหน่ง', 
            'จำนวน Folders', 'วันที่เชื่อมต่อ', 'สถานะการยืนยัน'
        ];
        
        $this->export_csv_data($filename, $headers, $members, 'user_based');

    } catch (Exception $e) {
        log_message('error', 'Export members error: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาดในการ Export: ' . $e->getMessage());
    }
}

/**
 * 🆕 Export Storage Usage Report
 */
public function export_storage_report() {
    // ตรวจสอบสิทธิ์
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    try {
        $report_type = $this->input->get('type') ?: 'summary';
        $date_from = $this->input->get('date_from') ?: date('Y-m-01'); // เดือนนี้
        $date_to = $this->input->get('date_to') ?: date('Y-m-t');

        switch ($report_type) {
            case 'detailed':
                $this->export_detailed_storage_report($date_from, $date_to);
                break;
            case 'by_department':
                $this->export_department_storage_report($date_from, $date_to);
                break;
            default:
                $this->export_summary_storage_report($date_from, $date_to);
        }

    } catch (Exception $e) {
        log_message('error', 'Export storage report error: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาดในการ Export รายงาน: ' . $e->getMessage());
    }
}

/**
 * 🆕 Export รายงานการใช้งาน Storage แบบสรุป (รองรับภาษาไทย)
 */
private function export_summary_storage_report($date_from, $date_to) {
    $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
    
    if ($storage_mode === 'centralized') {
        // ตั้งค่า encoding
        mb_internal_encoding('UTF-8');
        
        // ดึงข้อมูลการใช้งาน Storage
        $system_storage = $this->get_system_storage_info();
        $users = $this->get_storage_users('', 1000);
        
        $filename = 'storage_usage_summary_' . date('Y-m-d_H-i-s') . '.csv';
        
        // ล้าง output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Headers รองรับภาษาไทย
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($filename));
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM
        fwrite($output, "\xEF\xBB\xBF");
        
        // Header ข้อมูลระบบ
        fputcsv($output, [$this->ensure_utf8('รายงานการใช้งาน Centralized Storage')]);
        fputcsv($output, [$this->ensure_utf8('Google Account:'), $this->ensure_utf8($system_storage->google_account_email ?? 'N/A')]);
        fputcsv($output, [$this->ensure_utf8('วันที่รายงาน:'), date('d/m/Y H:i:s')]);
        fputcsv($output, [$this->ensure_utf8('ช่วงเวลา:'), date('d/m/Y', strtotime($date_from)) . ' - ' . date('d/m/Y', strtotime($date_to))]);
        fputcsv($output, []); // บรรทัดว่าง
        
        // สถิติรวม
        fputcsv($output, [$this->ensure_utf8('สถิติรวม')]);
        fputcsv($output, [$this->ensure_utf8('จำนวนผู้ใช้งาน:'), count($users)]);
        fputcsv($output, [$this->ensure_utf8('Storage ใช้งาน (%):'), number_format($system_storage->storage_usage_percent ?? 0, 2) . '%']);
        fputcsv($output, [$this->ensure_utf8('จำนวน Folders:'), $system_storage->total_folders ?? 0]);
        fputcsv($output, [$this->ensure_utf8('จำนวนไฟล์:'), $system_storage->total_files ?? 0]);
        fputcsv($output, []); // บรรทัดว่าง
        
        // ข้อมูลผู้ใช้งาน
        fputcsv($output, [$this->ensure_utf8('รายละเอียดผู้ใช้งาน')]);
        fputcsv($output, [
            $this->ensure_utf8('ชื่อ-นามสกุล'), 
            $this->ensure_utf8('ตำแหน่ง'), 
            $this->ensure_utf8('Quota (MB)'), 
            $this->ensure_utf8('ใช้งาน (MB)'), 
            $this->ensure_utf8('ใช้งาน (%)'), 
            $this->ensure_utf8('เข้าใช้ล่าสุด')
        ]);
        
        foreach ($users as $user) {
            $usage_percent = $user->storage_quota_limit > 0 ? 
                round(($user->storage_quota_used / $user->storage_quota_limit) * 100, 2) : 0;
                
            fputcsv($output, [
                $this->ensure_utf8($user->m_fname . ' ' . $user->m_lname),
                $this->ensure_utf8($user->pname ?: '-'),
                number_format($user->storage_quota_limit / 1048576, 1),
                number_format($user->storage_quota_used / 1048576, 1),
                $usage_percent . '%',
                $user->last_storage_access ? 
                    date('d/m/Y H:i', strtotime($user->last_storage_access)) : $this->ensure_utf8('ยังไม่เคย')
            ]);
        }
        
        fclose($output);
    } else {
        // สำหรับ User-based Storage
        show_error('รายงาน Storage ใช้ได้เฉพาะโหมด Centralized Storage');
    }
    
    exit;
}

/**
 * 🆕 Export รายงานแยกตามแผนก (รองรับภาษาไทย)
 */
private function export_department_storage_report($date_from, $date_to) {
    // ตั้งค่า encoding
    mb_internal_encoding('UTF-8');
    
    // ดึงข้อมูลแยกตามแผนก/ตำแหน่ง
    $department_usage = $this->get_storage_usage_by_department();
    
    $filename = 'storage_by_department_' . date('Y-m-d_H-i-s') . '.csv';
    
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Headers รองรับภาษาไทย
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($filename));
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM
    fwrite($output, "\xEF\xBB\xBF");
    
    fputcsv($output, [$this->ensure_utf8('รายงานการใช้งาน Storage แยกตามแผนก/ตำแหน่ง')]);
    fputcsv($output, [$this->ensure_utf8('วันที่รายงาน:'), date('d/m/Y H:i:s')]);
    fputcsv($output, []);
    
    fputcsv($output, [
        $this->ensure_utf8('ตำแหน่ง/แผนก'), 
        $this->ensure_utf8('จำนวนคน'), 
        $this->ensure_utf8('Storage รวม (MB)'), 
        $this->ensure_utf8('เฉลี่ยต่อคน (MB)'), 
        $this->ensure_utf8('เปอร์เซ็นต์')
    ]);
    
    foreach ($department_usage as $dept) {
        fputcsv($output, [
            $this->ensure_utf8($dept->department_name),
            $dept->user_count,
            number_format($dept->total_usage / 1048576, 1),
            number_format($dept->average_usage / 1048576, 1),
            number_format($dept->usage_percentage, 2) . '%'
        ]);
    }
    
    fclose($output);
    exit;
}

/**
 * 🆕 ดึงการใช้งาน Storage แยกตามแผนก
 */
private function get_storage_usage_by_department() {
    try {
        $result = $this->db->select('p.pname as department_name, 
                                    COUNT(m.m_id) as user_count,
                                    SUM(m.storage_quota_used) as total_usage,
                                    AVG(m.storage_quota_used) as average_usage')
                          ->from('tbl_member m')
                          ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                          ->where('m.storage_access_granted', 1)
                          ->group_by('p.pid, p.pname')
                          ->order_by('total_usage', 'desc')
                          ->get()
                          ->result();

        // คำนวณเปอร์เซ็นต์
        $total_all_usage = array_sum(array_column($result, 'total_usage'));
        
        foreach ($result as $dept) {
            $dept->usage_percentage = $total_all_usage > 0 ? 
                ($dept->total_usage / $total_all_usage) * 100 : 0;
        }

        return $result;

    } catch (Exception $e) {
        log_message('error', 'Get storage usage by department error: ' . $e->getMessage());
        return [];
    }
}

/**
 * 🆕 Export รายงานแบบละเอียด (รองรับภาษาไทย)
 */
private function export_detailed_storage_report($date_from, $date_to) {
    // ตั้งค่า encoding
    mb_internal_encoding('UTF-8');
    
    // รายงานละเอียดรวมไฟล์และกิจกรรม
    $users = $this->get_storage_users('', 1000);
    
    $filename = 'detailed_storage_report_' . date('Y-m-d_H-i-s') . '.csv';
    
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Headers รองรับภาษาไทย
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($filename));
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM
    fwrite($output, "\xEF\xBB\xBF");
    
    fputcsv($output, [$this->ensure_utf8('รายงานการใช้งาน Storage แบบละเอียด')]);
    fputcsv($output, [$this->ensure_utf8('ช่วงเวลา:'), date('d/m/Y', strtotime($date_from)) . ' - ' . date('d/m/Y', strtotime($date_to))]);
    fputcsv($output, []);
    
    fputcsv($output, [
        $this->ensure_utf8('ชื่อ-นามสกุล'), 
        $this->ensure_utf8('ตำแหน่ง'), 
        $this->ensure_utf8('Google Email'), 
        $this->ensure_utf8('Storage Status'),
        $this->ensure_utf8('Quota (MB)'), 
        $this->ensure_utf8('ใช้งาน (MB)'), 
        $this->ensure_utf8('ใช้งาน (%)'), 
        $this->ensure_utf8('จำนวนไฟล์'),
        $this->ensure_utf8('เข้าใช้ล่าสุด'), 
        $this->ensure_utf8('สร้างเมื่อ')
    ]);
    
    foreach ($users as $user) {
        $usage_percent = $user->storage_quota_limit > 0 ? 
            round(($user->storage_quota_used / $user->storage_quota_limit) * 100, 2) : 0;
            
        $file_count = $this->get_user_file_count($user->m_id);
        
        fputcsv($output, [
            $this->ensure_utf8($user->m_fname . ' ' . $user->m_lname),
            $this->ensure_utf8($user->pname ?: '-'),
            $this->ensure_utf8($user->google_email ?: '-'),
            $user->google_drive_enabled ? $this->ensure_utf8('เปิดใช้งาน') : $this->ensure_utf8('ปิดใช้งาน'),
            number_format($user->storage_quota_limit / 1048576, 1),
            number_format($user->storage_quota_used / 1048576, 1),
            $usage_percent . '%',
            $file_count,
            $user->last_storage_access ? 
                date('d/m/Y H:i', strtotime($user->last_storage_access)) : $this->ensure_utf8('ยังไม่เคย'),
            date('d/m/Y', strtotime($user->m_datesave))
        ]);
    }
    
    fclose($output);
    exit;
}

/**
 * 🆕 ฟังก์ชันสำหรับตรวจสอบและแปลง encoding เป็น UTF-8
 */
private function ensure_utf8($text) {
    if (!is_string($text)) {
        return $text;
    }
    
    // ตรวจสอบว่า string เป็น UTF-8 อยู่แล้วหรือไม่
    if (mb_check_encoding($text, 'UTF-8')) {
        return $text;
    }
    
    // ลองแปลงจาก TIS-620 หรือ Windows-874 เป็น UTF-8
    $encodings_to_try = ['TIS-620', 'Windows-874', 'ISO-8859-11', 'auto'];
    
    foreach ($encodings_to_try as $encoding) {
        $converted = mb_convert_encoding($text, 'UTF-8', $encoding);
        if (mb_check_encoding($converted, 'UTF-8')) {
            return $converted;
        }
    }
    
    // ถ้าแปลงไม่ได้ ให้ใช้ค่าเดิม
    return $text;
}

/**
 * 🆕 Export ข้อมูลเป็น Excel format (รองรับภาษาไทย 100%)
 */
public function export_users_excel() {
    // ตรวจสอบสิทธิ์
    if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        show_404();
    }

    try {
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        
        if ($storage_mode === 'centralized') {
            $users = $this->get_storage_users('', 1000);
            $this->create_excel_export($users, 'centralized');
        } else {
            $members = $this->get_connected_members('', 1000);
            $this->create_excel_export($members, 'user_based');
        }

    } catch (Exception $e) {
        log_message('error', 'Export Excel error: ' . $e->getMessage());
        show_error('เกิดข้อผิดพลาดในการ Export Excel: ' . $e->getMessage());
    }
}

/**
 * 🆕 สร้างไฟล์ Excel (HTML Table format รองรับภาษาไทย)
 */
private function create_excel_export($data, $type = 'centralized') {
    $filename = ($type === 'centralized' ? 'storage_users_' : 'drive_members_') . date('Y-m-d_H-i-s') . '.xls';
    
    // ล้าง output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Headers สำหรับ Excel
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($filename));
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    
    // เริ่ม HTML Excel
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";
    echo '<head>' . "\n";
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
    echo '<style>' . "\n";
    echo 'table { border-collapse: collapse; width: 100%; }' . "\n";
    echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }' . "\n";
    echo 'th { background-color: #f2f2f2; font-weight: bold; }' . "\n";
    echo '.number { mso-number-format: "0.0"; }' . "\n";
    echo '.percent { mso-number-format: "0.0%"; }' . "\n";
    echo '.date { mso-number-format: "dd/mm/yyyy hh:mm"; }' . "\n";
    echo '</style>' . "\n";
    echo '</head>' . "\n";
    echo '<body>' . "\n";
    
    // Title
    echo '<h2>' . $this->ensure_utf8($type === 'centralized' ? 'รายงานผู้ใช้งาน Centralized Storage' : 'รายงานสมาชิก Google Drive') . '</h2>' . "\n";
    echo '<p>วันที่สร้างรายงาน: ' . date('d/m/Y H:i:s') . '</p>' . "\n";
    
    // Table
    echo '<table>' . "\n";
    echo '<thead>' . "\n";
    echo '<tr>' . "\n";
    
    if ($type === 'centralized') {
        echo '<th>' . $this->ensure_utf8('ID') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('ชื่อ-นามสกุล') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('อีเมล') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('ตำแหน่ง') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('Google Drive Status') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('Google Email') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('Storage Quota (MB)') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('Storage Used (MB)') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('Usage (%)') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('เข้าใช้ล่าสุด') . '</th>' . "\n";
    } else {
        echo '<th>' . $this->ensure_utf8('ID') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('ชื่อ-นามสกุล') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('อีเมล') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('Google Account') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('ตำแหน่ง') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('จำนวน Folders') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('วันที่เชื่อมต่อ') . '</th>' . "\n";
        echo '<th>' . $this->ensure_utf8('สถานะการยืนยัน') . '</th>' . "\n";
    }
    
    echo '</tr>' . "\n";
    echo '</thead>' . "\n";
    echo '<tbody>' . "\n";
    
    // Data rows
    foreach ($data as $row) {
        echo '<tr>' . "\n";
        
        if ($type === 'centralized') {
            $usage_percent = $row->storage_quota_limit > 0 ? 
                round(($row->storage_quota_used / $row->storage_quota_limit) * 100, 2) : 0;
                
            echo '<td>' . $row->m_id . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->m_fname . ' ' . $row->m_lname) . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->m_email ?: '-') . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->pname ?: '-') . '</td>' . "\n";
            echo '<td>' . ($row->google_drive_enabled ? $this->ensure_utf8('เปิดใช้งาน') : $this->ensure_utf8('ปิดใช้งาน')) . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->google_email ?: '-') . '</td>' . "\n";
            echo '<td class="number">' . number_format($row->storage_quota_limit / 1048576, 1) . '</td>' . "\n";
            echo '<td class="number">' . number_format($row->storage_quota_used / 1048576, 1) . '</td>' . "\n";
            echo '<td class="percent">' . $usage_percent . '</td>' . "\n";
            echo '<td class="date">' . ($row->last_storage_access ? 
                date('d/m/Y H:i', strtotime($row->last_storage_access)) : $this->ensure_utf8('ยังไม่เคย')) . '</td>' . "\n";
        } else {
            echo '<td>' . $row->m_id . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->m_fname . ' ' . $row->m_lname) . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->m_email ?: '-') . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->google_email ?: '-') . '</td>' . "\n";
            echo '<td>' . $this->ensure_utf8($row->pname ?: '-') . '</td>' . "\n";
            echo '<td>' . ($row->total_folders ?: '0') . '</td>' . "\n";
            echo '<td class="date">' . ($row->google_connected_at ? 
                date('d/m/Y H:i', strtotime($row->google_connected_at)) : '-') . '</td>' . "\n";
            echo '<td>' . ($row->google_account_verified ? $this->ensure_utf8('ยืนยันแล้ว') : $this->ensure_utf8('ยังไม่ยืนยัน')) . '</td>' . "\n";
        }
        
        echo '</tr>' . "\n";
    }
    
    echo '</tbody>' . "\n";
    echo '</table>' . "\n";
    echo '</body>' . "\n";
    echo '</html>' . "\n";
    
    exit;
}

/**
 * 🆕 ดึงจำนวนไฟล์ของผู้ใช้
 */
private function get_user_file_count($user_id) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_system_files')) {
            return 0;
        }
        
        return $this->db->where('uploaded_by', $user_id)
                       ->count_all_results('tbl_google_drive_system_files');
                       
    } catch (Exception $e) {
        return 0;
    }
}
	
	
public function enhanced_toggle_google_drive_access() {
    try {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!$this->input->is_ajax_request()) {
            $this->output_json_error('Invalid request method');
            return;
        }

        $member_id = $this->input->post('member_id');
        $enabled = $this->input->post('enabled') == '1';
        
        if (!$member_id) {
            $this->output_json_error('ไม่พบข้อมูลสมาชิก');
            return;
        }

        // ตรวจสอบโหมดการทำงาน
        $storage_mode = $this->get_setting('system_storage_mode', 'user_based');
        
        if ($storage_mode !== 'centralized') {
            // ถ้าไม่ใช่ centralized mode ใช้วิธีเดิม
            return $this->simple_toggle_google_drive_access();
        }

        // ดึงข้อมูลสมาชิกและตำแหน่ง
        $member = $this->db->select('m.*, p.pname, p.peng, p.pid')
                          ->from('tbl_member m')
                          ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                          ->where('m.m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            $this->output_json_error('ไม่พบข้อมูลสมาชิก');
            return;
        }

        // เริ่ม Transaction
        $this->db->trans_start();

        if ($enabled) {
            // 🟢 เปิดใช้งาน: Toggle + Grant Storage Access + Add Default Permissions
            $this->enable_google_drive_with_permissions($member);
        } else {
            // 🔴 ปิดใช้งาน: Toggle + Remove Permissions (Optional)
            $this->disable_google_drive_access($member_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $action = $enabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            $this->output_json_success([
                'member_id' => $member_id,
                'enabled' => $enabled,
                'personal_folder_created' => $enabled
            ], "{$action} Google Drive สำหรับ {$member->m_fname} {$member->m_lname} เรียบร้อยแล้ว");
        } else {
            $this->output_json_error('เกิดข้อผิดพลาดในการอัปเดตสิทธิ์');
        }

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Enhanced toggle Google Drive error: ' . $e->getMessage());
        $this->output_json_error('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
	
	
	
	
	/**
 * 🟢 เปิดใช้งาน Google Drive พร้อมสิทธิ์ Default
 */
private function enable_google_drive_with_permissions($member) {
    $member_id = $member->m_id;
    
    // 1. อัปเดตสถานะ Google Drive และ Storage Access
    $update_data = [
        'google_drive_enabled' => 1,
        'storage_access_granted' => 1,
        'storage_quota_limit' => 1073741824, // 1GB default
        'storage_quota_used' => 0
    ];
    
    $this->db->where('m_id', $member_id)->update('tbl_member', $update_data);

    // 2. สร้าง Personal Folder (ถ้ายังไม่มี)
    $this->create_user_personal_folder_sync($member_id);

    // 3. เพิ่มสิทธิ์ Default ตามโครงสร้างโฟลเดอร์
    $this->add_default_folder_permissions($member);

    // 4. บันทึก Activity Log
    $this->log_enhanced_activity(
        $this->session->userdata('m_id'),
        'enable_google_drive_with_permissions',
        "เปิดใช้งาน Google Drive พร้อมสิทธิ์ default สำหรับ {$member->m_fname} {$member->m_lname}",
        [
            'member_id' => $member_id,
            'member_name' => $member->m_fname . ' ' . $member->m_lname,
            'position' => $member->pname,
            'permissions_added' => true
        ]
    );
}

/**
 * 🔴 ปิดใช้งาน Google Drive
 */
private function disable_google_drive_access($member_id) {
    // อัปเดตสถานะ
    $update_data = [
        'google_drive_enabled' => 0
        // หมายเหตุ: ไม่ลบ storage_access_granted เพื่อคงข้อมูล quota
    ];
    
    $this->db->where('m_id', $member_id)->update('tbl_member', $update_data);

    // (Optional) ลบสิทธิ์ทั้งหมด หรือทำให้ inactive
    // $this->remove_all_folder_permissions($member_id);
}

/**
 * ➕ เพิ่มสิทธิ์ Default ตามโครงสร้างโฟลเดอร์
 */
private function add_default_folder_permissions($member) {
    $member_id = $member->m_id;
    $member_system = $member->m_system;
    $position_id = $member->pid;
    $granted_by = $this->session->userdata('m_id');
    $granted_by_name = $this->get_admin_name($granted_by);

    // ดึงข้อมูลโฟลเดอร์ระบบ
    $system_folders = $this->get_system_folders();

    foreach ($system_folders as $folder) {
        $permissions = $this->calculate_folder_permissions($folder, $member_system, $position_id);
        
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                $this->add_folder_permission_record($member_id, $folder->folder_id, $permission, $granted_by, $granted_by_name);
            }
        }
    }
}

/**
 * 📊 คำนวณสิทธิ์ของโฟลเดอร์ตามกฎที่กำหนด
 */
private function calculate_folder_permissions($folder, $member_system, $position_id) {
    $permissions = [];
    
    switch ($folder->folder_type) {
        case 'admin':
            // Admin folder: เฉพาะ system_admin และ super_admin
            if (in_array($member_system, ['system_admin', 'super_admin'])) {
                $permissions[] = [
                    'access_type' => 'admin',
                    'permission_source' => 'system',
                    'permission_mode' => 'direct',
                    'inherit_from_parent' => 0,
                    'apply_to_children' => 1
                ];
            }
            break;

        case 'system':
            if ($folder->folder_name === 'Departments') {
                // Departments folder: ทุกคนอ่านได้
                $permissions[] = [
                    'access_type' => 'read',
                    'permission_source' => 'system',
                    'permission_mode' => 'inherited',
                    'inherit_from_parent' => 1,
                    'apply_to_children' => 0
                ];
            } elseif ($folder->folder_name === 'Users') {
                // Users folder: ทุกคนอ่านได้
                $permissions[] = [
                    'access_type' => 'read',
                    'permission_source' => 'system',
                    'permission_mode' => 'inherited',
                    'inherit_from_parent' => 1,
                    'apply_to_children' => 0
                ];
            }
            break;

        case 'department':
            // Department folders: ตรวจสอบตำแหน่ง
            if ($folder->created_for_position == $position_id) {
                $permissions[] = [
                    'access_type' => 'write',
                    'permission_source' => 'position',
                    'permission_mode' => 'inherited',
                    'inherit_from_parent' => 1,
                    'apply_to_children' => 1
                ];
            }
            break;

        case 'shared':
            // Shared folder: ทุกคน edit/upload/delete ได้
            $permissions[] = [
                'access_type' => 'write',
                'permission_source' => 'system',
                'permission_mode' => 'direct',
                'inherit_from_parent' => 0,
                'apply_to_children' => 1
            ];
            break;

        case 'user':
            // Personal folders: เจ้าของมีสิทธิ์เต็ม
            if ($this->is_personal_folder_owner($folder->folder_id, $member_id)) {
                $permissions[] = [
                    'access_type' => 'admin',
                    'permission_source' => 'direct',
                    'permission_mode' => 'override',
                    'inherit_from_parent' => 0,
                    'apply_to_children' => 1
                ];
            }
            break;
    }

    return $permissions;
}

/**
 * 📝 เพิ่ม Record สิทธิ์ลงในฐานข้อมูล
 */
private function add_folder_permission_record($member_id, $folder_id, $permission, $granted_by, $granted_by_name) {
    // ตรวจสอบว่าตารางมีอยู่หรือไม่
    if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
        $this->create_folder_access_table();
    }

    // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
    $existing = $this->db->where([
        'member_id' => $member_id,
        'folder_id' => $folder_id,
        'is_active' => 1
    ])->get('tbl_google_drive_member_folder_access')->row();

    if ($existing) {
        // อัปเดตสิทธิ์ที่มีอยู่
        $this->db->where('id', $existing->id)->update('tbl_google_drive_member_folder_access', [
            'access_type' => $permission['access_type'],
            'permission_source' => $permission['permission_source'],
            'permission_mode' => $permission['permission_mode'],
            'inherit_from_parent' => $permission['inherit_from_parent'],
            'apply_to_children' => $permission['apply_to_children'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    } else {
        // เพิ่มสิทธิ์ใหม่
        $this->db->insert('tbl_google_drive_member_folder_access', [
            'member_id' => $member_id,
            'folder_id' => $folder_id,
            'access_type' => $permission['access_type'],
            'permission_source' => $permission['permission_source'],
            'permission_mode' => $permission['permission_mode'],
            'granted_by' => $granted_by,
            'granted_by_name' => $granted_by_name,
            'granted_at' => date('Y-m-d H:i:s'),
            'expires_at' => null, // ไม่มีวันหมดอายุสำหรับ default permissions
            'is_active' => 1,
            'inherit_from_parent' => $permission['inherit_from_parent'],
            'apply_to_children' => $permission['apply_to_children']
        ]);
    }
}

/**
 * 📂 ดึงข้อมูลโฟลเดอร์ระบบทั้งหมด
 */
private function get_system_folders() {
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
}

/**
 * 👤 ตรวจสอบว่าเป็นเจ้าของ Personal Folder หรือไม่
 */
private function is_personal_folder_owner($folder_id, $member_id) {
    // ตรวจสอบจาก tbl_member.personal_folder_id
    $member = $this->db->select('personal_folder_id')
                      ->from('tbl_member')
                      ->where('m_id', $member_id)
                      ->get()
                      ->row();

    return ($member && $member->personal_folder_id === $folder_id);
}

/**
 * 🔄 สร้าง Personal Folder แบบ Synchronous
 */
private function create_user_personal_folder_sync($member_id) {
    try {
        // ตรวจสอบว่ามี Personal Folder แล้วหรือไม่
        $member = $this->db->select('personal_folder_id, m_fname, m_lname')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if ($member && $member->personal_folder_id) {
            // มี Personal Folder แล้ว
            return true;
        }

        // สร้าง Personal Folder ใหม่
        $system_storage = $this->get_active_system_storage();
        if (!$system_storage || !$system_storage->google_access_token) {
            log_message('warning', 'Cannot create personal folder: No system storage');
            return false;
        }

        // ตรวจสอบ Token
        if (!$this->ensure_valid_access_token()) {
            log_message('warning', 'Cannot create personal folder: Invalid access token');
            return false;
        }

        // ดึง Users folder
        $users_folder = $this->db->select('folder_id')
                               ->from('tbl_google_drive_system_folders')
                               ->where('folder_name', 'Users')
                               ->where('folder_type', 'system')
                               ->where('is_active', 1)
                               ->get()
                               ->row();

        if (!$users_folder) {
            log_message('warning', 'Cannot create personal folder: Users folder not found');
            return false;
        }

        // สร้างโฟลเดอร์
        $system_storage = $this->get_active_system_storage(); // ดึงใหม่หลัง refresh token
        $token_data = json_decode($system_storage->google_access_token, true);
        
        $folder_name = $member->m_fname . ' ' . $member->m_lname . ' (ID: ' . $member_id . ')';
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
                'folder_description' => 'Personal folder for ' . $member->m_fname . ' ' . $member->m_lname,
                'permission_level' => 'private',
                'created_by' => $this->session->userdata('m_id')
            ];

            $this->save_folder_info($folder_data);

            // อัปเดต member
            $this->db->where('m_id', $member_id)->update('tbl_member', [
                'personal_folder_id' => $personal_folder['id']
            ]);

            return true;
        }

        return false;

    } catch (Exception $e) {
        log_message('error', 'Create personal folder sync error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🛠️ สร้างตาราง Folder Access ถ้ายังไม่มี
 */
private function create_folder_access_table() {
    $sql = "
        CREATE TABLE IF NOT EXISTS `tbl_google_drive_member_folder_access` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `member_id` int(11) NOT NULL COMMENT 'อ้างอิง tbl_member',
            `folder_id` varchar(255) NOT NULL COMMENT 'Google Drive Folder ID',
            `access_type` enum('read','write','admin','owner') DEFAULT 'read' COMMENT 'ประเภทการเข้าถึง',
            `permission_source` enum('direct','position','department','system') DEFAULT 'direct',
            `granted_by` int(11) DEFAULT NULL COMMENT 'ใครให้สิทธิ์',
            `granted_by_name` varchar(200) DEFAULT NULL,
            `granted_at` timestamp NULL DEFAULT current_timestamp(),
            `expires_at` datetime DEFAULT NULL COMMENT 'วันหมดอายุ (NULL = ไม่หมดอายุ)',
            `is_active` tinyint(1) DEFAULT 1,
            `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `inherit_from_parent` tinyint(1) DEFAULT 1 COMMENT 'สืบทอดสิทธิ์จาก parent หรือไม่',
            `apply_to_children` tinyint(1) DEFAULT 0 COMMENT 'ใช้สิทธิ์นี้กับ subfolder หรือไม่',
            `parent_folder_id` varchar(255) DEFAULT NULL COMMENT 'Parent folder ที่สืบทอดสิทธิ์มา',
            `permission_mode` enum('inherited','override','direct','combined') DEFAULT 'direct' COMMENT 'โหมดสิทธิ์',
            PRIMARY KEY (`id`),
            UNIQUE KEY `member_folder_unique` (`member_id`,`folder_id`),
            KEY `member_id` (`member_id`),
            KEY `folder_id` (`folder_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='User Folder Permissions';
    ";

    $this->db->query($sql);
}

/**
 * 👨‍💼 ดึงชื่อ Admin
 */
private function get_admin_name($admin_id) {
    try {
        $admin = $this->db->select('m_fname, m_lname')
                         ->from('tbl_member')
                         ->where('m_id', $admin_id)
                         ->get()
                         ->row();

        return $admin ? $admin->m_fname . ' ' . $admin->m_lname : 'Admin';
    } catch (Exception $e) {
        return 'Admin';
    }
}

/**
 * 📝 บันทึก Enhanced Activity Log
 */
private function log_enhanced_activity($admin_id, $action_type, $description, $details = []) {
    try {
        if (!$this->db->table_exists('tbl_google_drive_logs')) {
            return;
        }

        $this->db->insert('tbl_google_drive_logs', [
            'member_id' => $admin_id,
            'action_type' => $action_type,
            'action_description' => $description,
            'module' => 'google_drive_permission_system',
            'status' => 'success',
            'additional_data' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        log_message('error', 'Log enhanced activity error: ' . $e->getMessage());
    }
}
	
	
	
	
}


	


?>