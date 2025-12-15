<?php
/**
 * Google Drive Configuration v2.15.1 - Fixed OAuth2 Issue
 * Path: application/config/google_drive.php
 * 
 * การตั้งค่า Google Drive Integration สำหรับ CodeIgniter 3
 * แก้ไขปัญหา Google\Service\Oauth2 not found
 * 
 * @version 2.15.1-fixed
 * @since 2025-07-05
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// ป้องกันการโหลดซ้ำ
if (defined('GOOGLE_DRIVE_CONFIG_LOADED')) {
    return;
}
define('GOOGLE_DRIVE_CONFIG_LOADED', true);

/*
|--------------------------------------------------------------------------
| Google Drive Configuration - Fixed OAuth2 Issue
|--------------------------------------------------------------------------
|
| การตั้งค่าเบื้องต้นสำหรับ Google Drive Integration
| แก้ไขปัญหา OAuth2 Service ที่ไม่พบใน Google API Client v2.15.1
|
*/

// ตั้งค่าพื้นฐาน
$config['google_drive_enabled'] = true;
$config['auto_create_folders'] = true;
$config['max_file_size'] = 104857600; // 100MB
$config['allowed_file_types'] = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];

// Google OAuth Credentials (จะดึงจากฐานข้อมูลจริง)
$config['google_client_id'] = '';
$config['google_client_secret'] = '';
$config['google_redirect_uri'] = site_url('google_drive/oauth_callback');

// Google API Scopes - แก้ไขให้เหมาะสม
$config['google_scopes'] = [
    'https://www.googleapis.com/auth/drive',
    'https://www.googleapis.com/auth/drive.file',
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile'
];

// การตั้งค่าเพิ่มเติม
$config['logging_enabled'] = true;
$config['cache_enabled'] = true;
$config['debug_mode'] = defined('ENVIRONMENT') && ENVIRONMENT === 'development';

/*
|--------------------------------------------------------------------------
| Load Google Client Library - Fixed OAuth2 Issue
|--------------------------------------------------------------------------
*/

/**
 * โหลด Google Client Library อย่างปลอดภัย
 */
function load_google_library() {
    try {
        // ตรวจสอบว่าโหลดแล้วหรือไม่
        if (class_exists('Google\\Client')) {
            return true;
        }

        // โหลดผ่าน Google_Client_Loader
        $loader_path = APPPATH . 'third_party/google_client_loader.php';
        if (file_exists($loader_path)) {
            require_once $loader_path;
            
            if (class_exists('Google_Client_Loader')) {
                return Google_Client_Loader::load();
            }
        }

        // โหลดโดยตรงผ่าน autoload.php
        $autoload_path = APPPATH . 'third_party/google-api-php-client/autoload.php';
        if (file_exists($autoload_path)) {
            require_once $autoload_path;
            return class_exists('Google\\Client');
        }

        return false;

    } catch (Exception $e) {
        error_log('Load Google Library Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * ตรวจสอบความพร้อมของ Google Client - แก้ไข OAuth2 Issue
 */
function check_google_client_availability() {
    try {
        if (!class_exists('Google\\Client')) {
            return [
                'available' => false,
                'message' => 'Google\\Client class not found',
                'oauth2_available' => false
            ];
        }

        // ทดสอบสร้าง Client
        $client = new Google\Client();
        
        // ทดสอบสร้าง Drive Service (จำเป็น)
        $drive = new Google\Service\Drive($client);
        
        // ทดสอบ OAuth2 Service (ไม่บังคับ)
        $oauth2_available = false;
        $oauth2_method = 'none';
        
        try {
            // ลองสร้าง OAuth2 Service หลายวิธี
            if (class_exists('Google\\Service\\Oauth2')) {
                $oauth2 = new Google\Service\Oauth2($client);
                $oauth2_available = true;
                $oauth2_method = 'Google\\Service\\Oauth2';
            } elseif (class_exists('Google_Service_Oauth2')) {
                $oauth2 = new Google_Service_Oauth2($client);
                $oauth2_available = true;
                $oauth2_method = 'Google_Service_Oauth2';
            } elseif (class_exists('Google\\Service\\PeopleService')) {
                $oauth2 = new Google\Service\PeopleService($client);
                $oauth2_available = true;
                $oauth2_method = 'Google\\Service\\PeopleService (alternative)';
            }
        } catch (Exception $e) {
            // OAuth2 Service ไม่สำเร็จ - ไม่เป็นไร
            $oauth2_available = false;
            $oauth2_method = 'HTTP Request (fallback)';
        }

        return [
            'available' => true,
            'message' => 'Google Client is available',
            'oauth2_available' => $oauth2_available,
            'oauth2_method' => $oauth2_method,
            'drive_available' => true,
            'version' => method_exists($client, 'getLibraryVersion') ? $client->getLibraryVersion() : '2.15.1+'
        ];

    } catch (Exception $e) {
        return [
            'available' => false,
            'message' => 'Google Client test failed: ' . $e->getMessage(),
            'oauth2_available' => false
        ];
    }
}

/**
 * ดึงการตั้งค่าจากฐานข้อมูล - แก้ไข Error Handling
 */
function get_google_drive_settings() {
    try {
        $CI =& get_instance();
        
        if (!$CI->db->table_exists('tbl_google_drive_settings')) {
            return [];
        }

        $settings = $CI->db->select('setting_key, setting_value')
                          ->from('tbl_google_drive_settings')
                          ->where('is_active', 1)
                          ->get()
                          ->result();

        $config_settings = [];
        foreach ($settings as $setting) {
            $config_settings[$setting->setting_key] = $setting->setting_value;
        }

        return $config_settings;

    } catch (Exception $e) {
        error_log('Get Google Drive Settings Error: ' . $e->getMessage());
        return [];
    }
}

/**
 * อัพเดทการตั้งค่าจากฐานข้อมูล
 */
function update_config_from_database() {
    try {
        $CI =& get_instance();
        $db_settings = get_google_drive_settings();

        if (!empty($db_settings)) {
            // อัพเดท config จากฐานข้อมูล
            foreach ($db_settings as $key => $value) {
                $CI->config->set_item($key, $value);
            }

            // แปลงค่าบางตัวให้เป็นประเภทที่ถูกต้อง
            if (isset($db_settings['google_drive_enabled'])) {
                $CI->config->set_item('google_drive_enabled', $db_settings['google_drive_enabled'] == '1');
            }
            
            if (isset($db_settings['auto_create_folders'])) {
                $CI->config->set_item('auto_create_folders', $db_settings['auto_create_folders'] == '1');
            }
            
            if (isset($db_settings['allowed_file_types'])) {
                $types = explode(',', $db_settings['allowed_file_types']);
                $CI->config->set_item('allowed_file_types', array_map('trim', $types));
            }
        }

    } catch (Exception $e) {
        error_log('Update Config from Database Error: ' . $e->getMessage());
    }
}

/**
 * สร้างตารางเริ่มต้นถ้าไม่มี
 */
function create_default_tables() {
    try {
        $CI =& get_instance();
        
        // สร้างตาราง tbl_google_drive_settings ถ้าไม่มี
        if (!$CI->db->table_exists('tbl_google_drive_settings')) {
            $sql = "
            CREATE TABLE `tbl_google_drive_settings` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `setting_key` varchar(100) NOT NULL COMMENT 'คีย์การตั้งค่า',
                `setting_value` text NOT NULL COMMENT 'ค่าการตั้งค่า',
                `setting_description` text DEFAULT NULL COMMENT 'คำอธิบายการตั้งค่า',
                `is_active` tinyint(1) DEFAULT 1 COMMENT '0=ปิดใช้งาน, 1=เปิดใช้งาน',
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_setting_key` (`setting_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ";
            
            $CI->db->query($sql);
            
            // เพิ่มข้อมูลเริ่มต้น
            $default_settings = [
                ['setting_key' => 'google_client_id', 'setting_value' => '', 'setting_description' => 'Google OAuth Client ID'],
                ['setting_key' => 'google_client_secret', 'setting_value' => '', 'setting_description' => 'Google OAuth Client Secret'],
                ['setting_key' => 'google_redirect_uri', 'setting_value' => site_url('google_drive/oauth_callback'), 'setting_description' => 'Google OAuth Redirect URI'],
                ['setting_key' => 'google_drive_enabled', 'setting_value' => '1', 'setting_description' => 'เปิด/ปิดการใช้งาน Google Drive'],
                ['setting_key' => 'auto_create_folders', 'setting_value' => '1', 'setting_description' => 'สร้าง Folder อัตโนมัติตามตำแหน่ง'],
                ['setting_key' => 'max_file_size', 'setting_value' => '104857600', 'setting_description' => 'ขนาดไฟล์สูงสุด (100MB)'],
                ['setting_key' => 'allowed_file_types', 'setting_value' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar', 'setting_description' => 'ประเภทไฟล์ที่อนุญาต']
            ];
            
            foreach ($default_settings as $setting) {
                $CI->db->insert('tbl_google_drive_settings', $setting);
            }
        }

    } catch (Exception $e) {
        error_log('Create Default Tables Error: ' . $e->getMessage());
    }
}

/**
 * ดึงสถานะ Google Drive - แก้ไข OAuth2 Issue
 */
function get_google_drive_status() {
    try {
        // โหลด Google Library
        $library_loaded = load_google_library();
        
        // ตรวจสอบความพร้อม
        $client_status = check_google_client_availability();
        
        // ตรวจสอบการตั้งค่า
        $settings = get_google_drive_settings();
        $credentials_configured = !empty($settings['google_client_id']) && !empty($settings['google_client_secret']);
        
        // ตรวจสอบตาราง
        $CI =& get_instance();
        $tables_exist = $CI->db->table_exists('tbl_google_drive_settings');
        
        return [
            'library_loaded' => $library_loaded,
            'client_available' => $client_status['available'],
            'oauth2_available' => $client_status['oauth2_available'] ?? false,
            'oauth2_method' => $client_status['oauth2_method'] ?? 'unknown',
            'credentials_configured' => $credentials_configured,
            'tables_exist' => $tables_exist,
            'settings' => $settings,
            'version' => $client_status['version'] ?? 'unknown',
            'ready' => $library_loaded && $client_status['available'] && $credentials_configured && $tables_exist
        ];

    } catch (Exception $e) {
        error_log('Get Google Drive Status Error: ' . $e->getMessage());
        return [
            'library_loaded' => false,
            'client_available' => false,
            'oauth2_available' => false,
            'credentials_configured' => false,
            'tables_exist' => false,
            'ready' => false,
            'error' => $e->getMessage()
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Initialize Google Drive Config - Fixed OAuth2 Issue
|--------------------------------------------------------------------------
*/

try {
    // โหลด Google Client Library
    $library_loaded = load_google_library();
    
    if ($library_loaded) {
        // สร้างตารางเริ่มต้นถ้าไม่มี
        create_default_tables();
        
        // อัพเดท config จากฐานข้อมูล
        update_config_from_database();
        
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            $status = get_google_drive_status();
            error_log('[INFO] Google Drive Config: Ready=' . ($status['ready'] ? 'Yes' : 'No') . 
                     ', OAuth2=' . ($status['oauth2_available'] ? $status['oauth2_method'] : 'Alternative Methods'));
        }
    }

} catch (Exception $e) {
    error_log('Google Drive Config Initialization Error: ' . $e->getMessage());
}

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

/**
 * ตรวจสอบว่า Google Drive พร้อมใช้งานหรือไม่
 */
if (!function_exists('is_google_drive_ready')) {
    function is_google_drive_ready() {
        $status = get_google_drive_status();
        return $status['ready'];
    }
}

/**
 * ดึงการตั้งค่า Google Drive
 */
if (!function_exists('get_google_drive_config')) {
    function get_google_drive_config($key = null) {
        $CI =& get_instance();
        
        if ($key) {
            return $CI->config->item($key);
        }
        
        return [
            'google_drive_enabled' => $CI->config->item('google_drive_enabled'),
            'auto_create_folders' => $CI->config->item('auto_create_folders'),
            'max_file_size' => $CI->config->item('max_file_size'),
            'allowed_file_types' => $CI->config->item('allowed_file_types'),
            'google_client_id' => $CI->config->item('google_client_id'),
            'google_redirect_uri' => $CI->config->item('google_redirect_uri'),
            'google_scopes' => $CI->config->item('google_scopes')
        ];
    }
}

/*
|--------------------------------------------------------------------------
| End of file google_drive.php
|--------------------------------------------------------------------------
*/