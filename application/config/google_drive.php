<?php
/**
 * Google Drive Configuration v3.2.0 - Complete with Centralized Storage
 * Path: application/config/google_drive.php
 * 
 * การตั้งค่า Google Drive Integration สำหรับ CodeIgniter 3 + Centralized Storage
 * รวมเอาส่วนดีของ config เดิมและเพิ่มการรองรับ System Storage
 * 
 * @version 3.2.0
 * @since 2025-07-06
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// ป้องกันการโหลดซ้ำ
if (defined('GOOGLE_DRIVE_CONFIG_LOADED')) {
    return;
}
define('GOOGLE_DRIVE_CONFIG_LOADED', true);

/*
|--------------------------------------------------------------------------
| Google Drive Basic Configuration
|--------------------------------------------------------------------------
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

// Google API Scopes
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
| Centralized Storage Configuration
|--------------------------------------------------------------------------
*/

// โหมดการทำงาน: 'user_based' หรือ 'centralized'
$config['system_storage_mode'] = 'user_based';

// เปิด/ปิดการใช้งาน System Storage
$config['system_storage_enabled'] = false;

// ขีดจำกัด Storage ของระบบ (bytes) - 100GB เริ่มต้น
$config['system_storage_limit'] = 107374182400;

// Quota เริ่มต้นสำหรับ User (bytes) - 1GB เริ่มต้น
$config['default_user_quota'] = 1073741824;

// ขนาดไฟล์สูงสุดสำหรับ System Storage (bytes) - 100MB เริ่มต้น
$config['max_file_size_system'] = 104857600;

// ประเภทไฟล์ที่อนุญาตใน System Storage
$config['allowed_file_types_system'] = [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp',
    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico',
    'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv',
    'mp3', 'wav', 'flac', 'aac', 'ogg', 'wma',
    'zip', 'rar', '7z', 'tar', 'gz',
    'csv', 'json', 'xml', 'html', 'css', 'js'
];

/*
|--------------------------------------------------------------------------
| Folder Templates Configuration
|--------------------------------------------------------------------------
*/

// Template สำหรับ Admin (position_id = 1, 2)
$config['admin_folder_template'] = [
    'main_folder' => 'ระบบจัดการเอกสาร - Admin',
    'subfolders' => [
        'เอกสารส่วนตัว',
        'เอกสารสำคัญ',
        'รายงาน',
        'การประชุม',
        'แบบฟอร์ม',
        'ภาพและสื่อ'
    ]
];

// Template สำหรับ Department Admin (position_id = 3)
$config['department_folder_template'] = [
    'main_folder' => 'แผนก{position_name}',
    'subfolders' => [
        'เอกสารส่วนกลาง',
        'เอกสารส่วนตัว',
        'โครงการ',
        'รายงาน',
        'การประชุม'
    ]
];

// Template สำหรับ Staff (position_id >= 4)
$config['staff_folder_template'] = [
    'main_folder' => '{position_name} - เอกสารส่วนตัว',
    'subfolders' => [
        'งานประจำ',
        'โครงการ',
        'เอกสารอ้างอิง'
    ]
];

/*
|--------------------------------------------------------------------------
| Permission Settings
|--------------------------------------------------------------------------
*/

// สิทธิ์เริ่มต้นตามตำแหน่ง
$config['default_permissions'] = [
    1 => 'full_admin',      // System Admin
    2 => 'full_admin',      // Super Admin  
    3 => 'department_admin', // Department Admin
    4 => 'position_only'    // Staff และอื่นๆ
];

// ประเภทสิทธิ์ที่มี
$config['permission_types'] = [
    'full_admin' => [
        'name' => 'ผู้ดูแลระบบเต็มรูปแบบ',
        'description' => 'เข้าถึงได้ทุก folder และจัดการระบบได้',
        'folder_access' => ['all'],
        'can_create_folder' => true,
        'can_share' => true,
        'can_delete' => true
    ],
    'department_admin' => [
        'name' => 'ผู้ดูแลแผนก',
        'description' => 'เข้าถึงได้ folder ของแผนกและส่วนกลาง',
        'folder_access' => ['department', 'shared'],
        'can_create_folder' => true,
        'can_share' => true,
        'can_delete' => true
    ],
    'position_only' => [
        'name' => 'เฉพาะตำแหน่ง',
        'description' => 'เข้าถึงได้เฉพาะ folder ของตำแหน่งตัวเอง',
        'folder_access' => ['own'],
        'can_create_folder' => true,
        'can_share' => false,
        'can_delete' => false
    ],
    'read_only' => [
        'name' => 'อ่านอย่างเดียว',
        'description' => 'ดูและดาวน์โหลดเท่านั้น',
        'folder_access' => ['shared'],
        'can_create_folder' => false,
        'can_share' => false,
        'can_delete' => false
    ],
    'no_access' => [
        'name' => 'ไม่มีสิทธิ์',
        'description' => 'ไม่สามารถเข้าใช้งานได้',
        'folder_access' => [],
        'can_create_folder' => false,
        'can_share' => false,
        'can_delete' => false
    ]
];

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
                    return true;
                }
            }
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
 * ดึงการตั้งค่าจากฐานข้อมูล
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
 * ดึงข้อมูล System Storage
 */
function get_system_storage_info() {
    try {
        $CI =& get_instance();
        
        if (!$CI->db->table_exists('tbl_google_drive_system_storage')) {
            return null;
        }

        $system_storage = $CI->db->select('*')
                                ->from('tbl_google_drive_system_storage')
                                ->where('is_active', 1)
                                ->get()
                                ->row();

        if (!$system_storage) {
            return null;
        }

        // ดึงสถิติเพิ่มเติม
        $total_folders = 0;
        if ($CI->db->table_exists('tbl_google_drive_system_folders')) {
            $total_folders = $CI->db->where('is_active', 1)
                                   ->count_all_results('tbl_google_drive_system_folders');
        }

        $total_files = 0;
        if ($CI->db->table_exists('tbl_google_drive_system_files')) {
            $total_files = $CI->db->count_all('tbl_google_drive_system_files');
        }

        $active_users = $CI->db->where('storage_access_granted', 1)
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
            'storage_usage_percent' => round(($system_storage->total_storage_used / $system_storage->max_storage_limit) * 100, 2)
        ];

    } catch (Exception $e) {
        error_log('Get system storage info error: ' . $e->getMessage());
        return null;
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
            
            if (isset($db_settings['system_storage_enabled'])) {
                $CI->config->set_item('system_storage_enabled', $db_settings['system_storage_enabled'] == '1');
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
                ['setting_key' => 'allowed_file_types', 'setting_value' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar', 'setting_description' => 'ประเภทไฟล์ที่อนุญาต'],
                ['setting_key' => 'system_storage_mode', 'setting_value' => 'user_based', 'setting_description' => 'โหมด Storage: user_based หรือ centralized'],
                ['setting_key' => 'system_storage_enabled', 'setting_value' => '0', 'setting_description' => 'เปิด/ปิดการใช้งาน System Storage'],
                ['setting_key' => 'default_user_quota', 'setting_value' => '1073741824', 'setting_description' => 'Quota เริ่มต้นสำหรับ User (1GB)']
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
 * ดึงสถานะ Google Drive พร้อม System Storage
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
        
        // ตรวจสอบ System Storage
        $system_storage = get_system_storage_info();
        $storage_mode = $settings['system_storage_mode'] ?? 'user_based';
        
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
            'storage_mode' => $storage_mode,
            'system_storage' => $system_storage,
            'system_storage_ready' => ($storage_mode === 'centralized' && $system_storage && $system_storage->folder_structure_created),
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
| Initialize Google Drive Config
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
            error_log('[INFO] Google Drive Config v3.2.0: Ready=' . ($status['ready'] ? 'Yes' : 'No') . 
                     ', Mode=' . ($status['storage_mode'] ?? 'unknown') .
                     ', OAuth2=' . ($status['oauth2_available'] ? $status['oauth2_method'] : 'Alternative'));
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
 * ตรวจสอบว่าใช้ Centralized Storage หรือไม่
 */
if (!function_exists('is_centralized_storage')) {
    function is_centralized_storage() {
        $CI =& get_instance();
        $mode = $CI->config->item('system_storage_mode');
        return ($mode === 'centralized');
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
            'google_scopes' => $CI->config->item('google_scopes'),
            'system_storage_mode' => $CI->config->item('system_storage_mode'),
            'system_storage_enabled' => $CI->config->item('system_storage_enabled'),
            'default_user_quota' => $CI->config->item('default_user_quota')
        ];
    }
}

/**
 * ดึงสิทธิ์เริ่มต้นตามตำแหน่ง
 */
if (!function_exists('get_default_permission_by_position')) {
    function get_default_permission_by_position($position_id) {
        $CI =& get_instance();
        $default_permissions = $CI->config->item('default_permissions');
        
        if (isset($default_permissions[$position_id])) {
            return $default_permissions[$position_id];
        }
        
        // กรณีไม่พบ ให้ค่าเริ่มต้น
        if ($position_id <= 2) {
            return 'full_admin';
        } elseif ($position_id == 3) {
            return 'department_admin';
        } else {
            return 'position_only';
        }
    }
}

/*
|--------------------------------------------------------------------------
| End of file google_drive.php
|--------------------------------------------------------------------------
*/