<?php
/**
 * Google Client Loader v2.15.3 - Fixed Log Level Issue
 * Path: application/third_party/google_client_loader.php
 * 
 * โหลด Google API Client Library v2.15.1 อย่างปลอดภัย
 * แก้ไขปัญหา Log Level ที่ทำให้ Apache แสดง error
 * 
 * @version 2.15.3-fixed
 * @since 2025-07-05
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// ป้องกันการโหลดซ้ำ
if (class_exists('Google_Client_Loader')) {
    return true;
}

/**
 * Google Client Loader Class - แก้ไข Log Level Issue
 */
class Google_Client_Loader {

    private static $loaded = false;
    private static $load_attempts = 0;
    private static $max_attempts = 3;

    /**
     * โหลด Google Client Library v2.15.1 - แก้ไข Log Level Issue
     */
    public static function load() {
        try {
            // ตรวจสอบว่าโหลดแล้วหรือไม่
            if (self::$loaded) {
                return true;
            }

            // ตรวจสอบจำนวนครั้งที่พยายามโหลด
            self::$load_attempts++;
            if (self::$load_attempts > self::$max_attempts) {
                self::safe_log('error', 'Google Client Loader: Max load attempts exceeded');
                return false;
            }

            // ตรวจสอบว่ามี Class อยู่แล้วหรือไม่
            if (class_exists('Google\\Client')) {
                self::$loaded = true;
                self::safe_log('info', 'Google Client Loader: Already loaded');
                return true;
            }

            // ลองโหลดหลายวิธี
            $load_methods = [
                'load_via_autoload',
                'load_via_composer',
                'load_via_manual_require',
                'load_via_vendor'
            ];

            foreach ($load_methods as $method) {
                if (method_exists(__CLASS__, $method)) {
                    $result = call_user_func([__CLASS__, $method]);
                    if ($result) {
                        self::$loaded = true;
                        self::safe_log('info', "Google Client Loader: Loaded via {$method}");
                        return true;
                    }
                }
            }

            self::safe_log('error', 'Google Client Loader: All load methods failed');
            return false;

        } catch (Exception $e) {
            self::safe_log('error', 'Google Client Loader Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Safe Logging - แก้ไข Log Level Issue
     */
    private static function safe_log($level, $message) {
        try {
            // ใช้ CodeIgniter log_message แทน error_log
            if (function_exists('log_message')) {
                // Log เฉพาะใน development
                if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                    log_message($level, $message);
                }
            } else {
                // Fallback สำหรับกรณีที่ไม่มี CodeIgniter
                if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                    error_log("[{$level}] {$message}");
                }
            }
        } catch (Exception $e) {
            // Silent fail
        }
    }

    /**
     * วิธีที่ 1: โหลดผ่าน autoload.php
     */
    private static function load_via_autoload() {
        try {
            $autoload_paths = [
                APPPATH . 'third_party/google-api-php-client/autoload.php',
                APPPATH . 'third_party/google-api-php-client/vendor/autoload.php',
                FCPATH . 'vendor/autoload.php',
                dirname(APPPATH) . '/vendor/autoload.php'
            ];

            foreach ($autoload_paths as $autoload_path) {
                if (file_exists($autoload_path)) {
                    require_once $autoload_path;
                    
                    if (class_exists('Google\\Client')) {
                        // Test การสร้าง Client
                        $test_client = new Google\Client();
                        if ($test_client) {
                            self::safe_log('info', 'Google Client Loader: load_via_autoload successful');
                            return true;
                        }
                    }
                }
            }

            return false;

        } catch (Exception $e) {
            self::safe_log('error', 'load_via_autoload error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * วิธีที่ 2: โหลดผ่าน Composer
     */
    private static function load_via_composer() {
        try {
            $composer_paths = [
                FCPATH . 'vendor/autoload.php',
                dirname(APPPATH) . '/vendor/autoload.php',
                APPPATH . '../vendor/autoload.php'
            ];

            foreach ($composer_paths as $composer_path) {
                if (file_exists($composer_path)) {
                    require_once $composer_path;
                    
                    if (class_exists('Google\\Client')) {
                        self::safe_log('info', 'Google Client Loader: load_via_composer successful');
                        return true;
                    }
                }
            }

            return false;

        } catch (Exception $e) {
            self::safe_log('error', 'load_via_composer error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * วิธีที่ 3: โหลดแบบ Manual Require
     */
    private static function load_via_manual_require() {
        try {
            $google_client_paths = [
                APPPATH . 'third_party/google-api-php-client/src/Client.php',
                APPPATH . 'third_party/google-api-php-client/src/Google/Client.php'
            ];

            foreach ($google_client_paths as $client_path) {
                if (file_exists($client_path)) {
                    // โหลด Core Classes
                    self::load_core_classes();
                    
                    require_once $client_path;
                    
                    if (class_exists('Google\\Client') || class_exists('Google_Client')) {
                        self::safe_log('info', 'Google Client Loader: load_via_manual_require successful');
                        return true;
                    }
                }
            }

            return false;

        } catch (Exception $e) {
            self::safe_log('error', 'load_via_manual_require error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * วิธีที่ 4: โหลดผ่าน Vendor Directory
     */
    private static function load_via_vendor() {
        try {
            $vendor_paths = [
                APPPATH . 'third_party/vendor/autoload.php',
                APPPATH . 'vendor/autoload.php',
                FCPATH . 'application/vendor/autoload.php'
            ];

            foreach ($vendor_paths as $vendor_path) {
                if (file_exists($vendor_path)) {
                    require_once $vendor_path;
                    
                    if (class_exists('Google\\Client')) {
                        self::safe_log('info', 'Google Client Loader: load_via_vendor successful');
                        return true;
                    }
                }
            }

            return false;

        } catch (Exception $e) {
            self::safe_log('error', 'load_via_vendor error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * โหลด Core Classes สำหรับ Manual Loading
     */
    private static function load_core_classes() {
        try {
            $base_path = APPPATH . 'third_party/google-api-php-client/src/';
            
            if (!is_dir($base_path)) {
                return false;
            }

            // Core Classes ที่จำเป็น
            $core_classes = [
                'Google/Client.php',
                'Google/Service.php',
                'Google/Service/Drive.php',
                'Google/Service/Oauth2.php',
                'Google/Auth/OAuth2.php',
                'Google/Auth/HttpHandler/HttpHandlerFactory.php',
                'Google/Http/REST.php'
            ];

            foreach ($core_classes as $class_file) {
                $class_path = $base_path . $class_file;
                if (file_exists($class_path)) {
                    require_once $class_path;
                }
            }

            return true;

        } catch (Exception $e) {
            self::safe_log('error', 'load_core_classes error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบสถานะการโหลด
     */
    public static function is_loaded() {
        return self::$loaded && (class_exists('Google\\Client') || class_exists('Google_Client'));
    }

    /**
     * ดึงข้อมูลสถานะ
     */
    public static function get_status() {
        return [
            'loaded' => self::$loaded,
            'attempts' => self::$load_attempts,
            'max_attempts' => self::$max_attempts,
            'google_client_exists' => class_exists('Google\\Client'),
            'google_client_legacy_exists' => class_exists('Google_Client'),
            'google_service_drive_exists' => class_exists('Google\\Service\\Drive'),
            'php_version' => PHP_VERSION,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * ทดสอบการทำงานของ Google Client
     */
    public static function test_google_client() {
        try {
            if (!self::is_loaded()) {
                return [
                    'success' => false,
                    'message' => 'Google Client not loaded'
                ];
            }

            // ทดสอบสร้าง Client
            $client = new Google\Client();
            
            // ทดสอบ Method พื้นฐาน
            $client->setApplicationName('Test Application');
            $app_name = $client->getApplicationName();
            
            // ทดสอบสร้าง Drive Service
            $drive = new Google\Service\Drive($client);
            
            if ($app_name === 'Test Application' && $drive) {
                return [
                    'success' => true,
                    'message' => 'Google Client working properly',
                    'version' => method_exists($client, 'getLibraryVersion') ? $client->getLibraryVersion() : 'unknown'
                ];
            }

            return [
                'success' => false,
                'message' => 'Google Client test failed'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Test error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * รีเซ็ตสถานะการโหลด
     */
    public static function reset() {
        self::$loaded = false;
        self::$load_attempts = 0;
    }

    /**
     * โหลดแบบบังคับ (Force Load)
     */
    public static function force_load() {
        self::reset();
        return self::load();
    }

    /**
     * ดึง Error Messages
     */
    public static function get_debug_info() {
        $debug_info = [
            'status' => self::get_status(),
            'php_version' => PHP_VERSION,
            'extensions' => []
        ];

        // ตรวจสอบ PHP Extensions ที่จำเป็น
        $required_extensions = ['curl', 'json', 'openssl', 'mbstring'];
        foreach ($required_extensions as $ext) {
            $debug_info['extensions'][$ext] = extension_loaded($ext);
        }

        // ตรวจสอบ Path ต่างๆ
        $paths_to_check = [
            'APPPATH' => APPPATH,
            'FCPATH' => FCPATH,
            'autoload_1' => APPPATH . 'third_party/google-api-php-client/autoload.php',
            'autoload_2' => FCPATH . 'vendor/autoload.php',
            'client_path' => APPPATH . 'third_party/google-api-php-client/src/Client.php'
        ];

        foreach ($paths_to_check as $name => $path) {
            $debug_info['paths'][$name] = [
                'path' => $path,
                'exists' => file_exists($path),
                'readable' => is_readable($path)
            ];
        }

        return $debug_info;
    }

}

// Auto-load เมื่อไฟล์ถูก include
if (defined('BASEPATH')) {
    try {
        $load_result = Google_Client_Loader::load();
        
        if ($load_result) {
            $test_result = Google_Client_Loader::test_google_client();
            if ($test_result['success']) {
                // ใช้ safe_log แทน error_log
                Google_Client_Loader::safe_log('info', 'Google API Client autoload successful: Google\Client, Google_Client, Google_Service_Drive, Google\Service\Resource');
            } else {
                Google_Client_Loader::safe_log('error', 'Google API Client loaded but test failed: ' . $test_result['message']);
            }
        } else {
            Google_Client_Loader::safe_log('error', 'Google API Client autoload failed');
        }
        
    } catch (Exception $e) {
        if (function_exists('log_message')) {
            log_message('error', 'Google Client Loader autoload error: ' . $e->getMessage());
        }
    }
}

// Return true ถ้าโหลดสำเร็จ
return Google_Client_Loader::is_loaded();

?>