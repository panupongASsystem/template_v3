<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Google_drive_client {
    
    private $CI;
    private $client;
    private $drive_service;
    private $is_initialized = false;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
        
        // ลองเชื่อมต่อ Google Client
        $this->initialize_client();
    }
    
    /**
     * 🔄 เริ่มต้น Google Client
     */
    private function initialize_client() {
        try {
            // ตรวจสอบว่า Google Client Library มีอยู่หรือไม่
            if (!class_exists('Google\\Client')) {
                log_message('error', 'Google Client Library not found. Please install via Composer.');
                return false;
            }
            
            // สร้าง Google Client
            $this->client = new Google\Client();
            
            // ดึงการตั้งค่า OAuth
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');
            $redirect_uri = site_url('google_drive/oauth_callback');
            
            if (empty($client_id) || empty($client_secret)) {
                log_message('warning', 'Google OAuth credentials not configured');
                return false;
            }
            
            // ตั้งค่า Client
            $this->client->setClientId($client_id);
            $this->client->setClientSecret($client_secret);
            $this->client->setRedirectUri($redirect_uri);
            
            // เพิ่ม Scopes
            $this->client->addScope([
                'https://www.googleapis.com/auth/drive',
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/userinfo.email'
            ]);
            
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            $this->client->setApplicationName('Google Drive System v2.0');
            
            // สร้าง Drive Service
            $this->drive_service = new Google\Service\Drive($this->client);
            
            // โหลด System Access Token
            $this->load_system_token();
            
            $this->is_initialized = true;
            log_message('info', 'Google Client initialized successfully');
            return true;
            
        } catch (Exception $e) {
            log_message('error', 'Google Client initialization failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 🔑 โหลด System Access Token
     */
    private function load_system_token() {
        try {
            $storage = $this->get_system_storage();
            if (!$storage || !$storage->google_access_token) {
                return false;
            }
            
            $token_data = json_decode($storage->google_access_token, true);
            if (!$token_data) {
                return false;
            }
            
            // ตั้งค่า Access Token
            $this->client->setAccessToken($token_data);
            
            // ตรวจสอบและ Refresh Token หากจำเป็น
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    
                    // บันทึก Token ใหม่
                    $new_token = $this->client->getAccessToken();
                    $this->save_system_token($new_token);
                    
                    log_message('info', 'System token refreshed automatically');
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            log_message('error', 'Load system token error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 💾 บันทึก System Token
     */
    private function save_system_token($token_data) {
        try {
            $expires_at = isset($token_data['expires_in']) ? 
                date('Y-m-d H:i:s', time() + $token_data['expires_in']) : null;
            
            $this->CI->db->where('is_active', 1)
                        ->update('tbl_google_drive_system_storage', [
                            'google_access_token' => json_encode($token_data),
                            'google_token_expires' => $expires_at,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
            
            return true;
            
        } catch (Exception $e) {
            log_message('error', 'Save system token error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 📁 สร้างโฟลเดอร์
     */
    public function create_folder($name, $parent_id = null) {
        if (!$this->is_initialized) {
            return ['success' => false, 'error' => 'Google Client not initialized'];
        }
        
        try {
            $folder_metadata = new Google\Service\Drive\DriveFile([
                'name' => $name,
                'mimeType' => 'application/vnd.google-apps.folder'
            ]);
            
            if ($parent_id) {
                $folder_metadata->setParents([$parent_id]);
            }
            
            $folder = $this->drive_service->files->create($folder_metadata, [
                'fields' => 'id,name,webViewLink'
            ]);
            
            return [
                'success' => true,
                'folder' => [
                    'id' => $folder->getId(),
                    'name' => $folder->getName(),
                    'webViewLink' => $folder->getWebViewLink()
                ]
            ];
            
        } catch (Exception $e) {
            log_message('error', 'Create folder error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * 📂 ดึงรายการไฟล์/โฟลเดอร์
     */
    public function list_files($folder_id = null, $page_token = null) {
        if (!$this->is_initialized) {
            return ['success' => false, 'error' => 'Google Client not initialized'];
        }
        
        try {
            $params = [
                'pageSize' => 100,
                'fields' => 'nextPageToken, files(id, name, mimeType, size, modifiedTime, webViewLink, parents)'
            ];
            
            if ($folder_id) {
                $params['q'] = "'{$folder_id}' in parents and trashed=false";
            } else {
                $params['q'] = "trashed=false";
            }
            
            if ($page_token) {
                $params['pageToken'] = $page_token;
            }
            
            $results = $this->drive_service->files->listFiles($params);
            
            return [
                'success' => true,
                'files' => $results->getFiles(),
                'nextPageToken' => $results->getNextPageToken()
            ];
            
        } catch (Exception $e) {
            log_message('error', 'List files error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * 🔗 สร้างลิงก์แชร์
     */
    public function create_share_link($file_id, $permission = 'reader') {
        if (!$this->is_initialized) {
            return ['success' => false, 'error' => 'Google Client not initialized'];
        }
        
        try {
            // สร้าง Permission
            $permission_obj = new Google\Service\Drive\Permission([
                'role' => $permission,
                'type' => 'anyone'
            ]);
            
            $this->drive_service->permissions->create($file_id, $permission_obj);
            
            // ดึงลิงก์แชร์
            $file = $this->drive_service->files->get($file_id, [
                'fields' => 'webViewLink,webContentLink'
            ]);
            
            return [
                'success' => true,
                'share_link' => $file->getWebViewLink()
            ];
            
        } catch (Exception $e) {
            log_message('error', 'Create share link error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * 🔍 ทดสอบการเชื่อมต่อ
     */
    public function test_connection() {
        if (!$this->is_initialized) {
            return ['success' => false, 'error' => 'Google Client not initialized'];
        }
        
        try {
            $about = $this->drive_service->about->get(['fields' => 'user,storageQuota']);
            
            return [
                'success' => true,
                'user' => $about->getUser(),
                'storage' => $about->getStorageQuota()
            ];
            
        } catch (Exception $e) {
            log_message('error', 'Test connection error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * 🔄 สร้าง Authorization URL
     */
    public function create_auth_url($state = null) {
        if (!$this->client) {
            return null;
        }
        
        if ($state) {
            $this->client->setState($state);
        }
        
        return $this->client->createAuthUrl();
    }
    
    /**
     * 🔑 แลกเปลี่ยน Authorization Code เป็น Token
     */
    public function exchange_code($code) {
        if (!$this->client) {
            return ['success' => false, 'error' => 'Google Client not available'];
        }
        
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                return ['success' => false, 'error' => $token['error_description']];
            }
            
            return ['success' => true, 'token' => $token];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * ⚙️ ดึงการตั้งค่า
     */
    private function get_setting($key) {
        if (!$this->CI->db->table_exists('tbl_google_drive_settings')) {
            return null;
        }
        
        $result = $this->CI->db->select('setting_value')
                            ->from('tbl_google_drive_settings')
                            ->where('setting_key', $key)
                            ->where('is_active', 1)
                            ->get()
                            ->row();
        
        return $result ? $result->setting_value : null;
    }
    
    /**
     * 📊 ดึง System Storage
     */
    private function get_system_storage() {
        if (!$this->CI->db->table_exists('tbl_google_drive_system_storage')) {
            return null;
        }
        
        return $this->CI->db->select('*')
                          ->from('tbl_google_drive_system_storage')
                          ->where('is_active', 1)
                          ->get()
                          ->row();
    }
    
    /**
     * ✅ ตรวจสอบสถานะ
     */
    public function is_ready() {
        return $this->is_initialized && $this->client && $this->drive_service;
    }
    
    /**
     * 🔗 ดึง Google Client Instance
     */
    public function get_client() {
        return $this->client;
    }
    
    /**
     * 🚗 ดึง Drive Service Instance
     */
    public function get_drive_service() {
        return $this->drive_service;
    }
}
