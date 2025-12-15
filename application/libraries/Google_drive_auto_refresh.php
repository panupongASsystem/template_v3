<?php
// 📁 application/libraries/Google_drive_auto_refresh.php
// 🔄 Auto Token Refresh System - กระชับ เร็ว ไม่ใช้ Cron

defined('BASEPATH') OR exit('No direct script access allowed');

class Google_drive_auto_refresh {
    
    private $CI;
    private $refresh_interval = 900; // 15 นาที (900 วินาที)
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    /**
     * 🔄 เช็คและ Refresh Token อัตโนมัติ
     * เรียกใช้ใน header หรือก่อนใช้งาน Google Drive API
     */
    public function auto_check_and_refresh() {
        try {
            log_message('debug', 'Auto refresh: Starting check...');
            
            // ดึง System Storage
            $storage = $this->get_system_storage();
            if (!$storage) {
                log_message('debug', 'Auto refresh: No system storage found');
                return false;
            }
            
            // ตรวจสอบ Token
            if (!$this->need_refresh($storage)) {
                log_message('debug', 'Auto refresh: Token still valid');
                return true;
            }
            
            // ทำการ Refresh
            log_message('info', 'Auto refresh: Token needs refresh');
            return $this->perform_refresh($storage);
            
        } catch (Exception $e) {
            log_message('error', 'Auto refresh error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 🕐 ตรวจสอบว่าต้อง Refresh หรือไม่
     */
    private function need_refresh($storage) {
        // ไม่มี Token
        if (!$storage->google_access_token) {
            return false; // ไม่สามารถ refresh ได้
        }
        
        // ไม่มี Refresh Token
        $token_data = json_decode($storage->google_access_token, true);
        if (!$token_data || !isset($token_data['refresh_token'])) {
            return false; // ไม่สามารถ refresh ได้
        }
        
        // ตรวจสอบวันหมดอายุ
        if ($storage->google_token_expires) {
            $expires = strtotime($storage->google_token_expires);
            $now = time();
            
            // หมดอายุแล้ว หรือ เหลือน้อยกว่า 15 นาที
            if (($expires - $now) <= $this->refresh_interval) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 🔄 ทำการ Refresh Token
     */
    private function perform_refresh($storage) {
        try {
            $token_data = json_decode($storage->google_access_token, true);
            $refresh_token = $token_data['refresh_token'];
            
            // ดึงการตั้งค่า OAuth
            $client_id = $this->get_setting('google_client_id');
            $client_secret = $this->get_setting('google_client_secret');
            
            if (!$client_id || !$client_secret) {
                log_message('error', 'Auto refresh: Missing OAuth credentials');
                return false;
            }
            
            // ส่งคำขอ Refresh Token
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://oauth2.googleapis.com/token',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token'
                ]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $new_token = json_decode($response, true);
                
                if ($new_token && isset($new_token['access_token'])) {
                    // อัปเดต Token ในฐานข้อมูล
                    $updated_token = [
                        'access_token' => $new_token['access_token'],
                        'token_type' => $new_token['token_type'] ?? 'Bearer',
                        'expires_in' => $new_token['expires_in'] ?? 3600,
                        'refresh_token' => $refresh_token // เก็บ refresh token เดิม
                    ];
                    
                    // ถ้ามี refresh token ใหม่
                    if (isset($new_token['refresh_token'])) {
                        $updated_token['refresh_token'] = $new_token['refresh_token'];
                    }
                    
                    $expires_at = date('Y-m-d H:i:s', time() + $updated_token['expires_in']);
                    
                    // บันทึกลงฐานข้อมูล
                    $this->CI->db->where('is_active', 1)
                               ->update('tbl_google_drive_system_storage', [
                                   'google_access_token' => json_encode($updated_token),
                                   'google_token_expires' => $expires_at,
                                   'updated_at' => date('Y-m-d H:i:s')
                               ]);
                    
                    // บันทึก Log
                    $this->log_refresh_success();
                    
                    log_message('info', 'Auto refresh: SUCCESS - Token updated');
                    return true;
                }
            }
            
            log_message('error', "Auto refresh: FAILED - HTTP {$http_code}");
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Auto refresh perform error: ' . $e->getMessage());
            return false;
        }
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
     * 📝 บันทึก Log
     */
    /**
 * 📝 บันทึก Log
 */
private function log_refresh_success() {
    if ($this->CI->db->table_exists('tbl_google_drive_logs')) {
        try {
            $this->CI->db->insert('tbl_google_drive_logs', [
                'member_id' => NULL, // เปลี่ยนจาก 0 เป็น NULL
                'action_type' => 'auto_refresh',
                'action_description' => 'Auto refresh token successful',
                'status' => 'success',
                'ip_address' => $_SERVER['SERVER_ADDR'] ?? 'system',
                'user_agent' => 'Auto-Refresh-System'
            ]);
        } catch (Exception $e) {
            // ไม่ให้ log error หยุดการทำงานของระบบ
            log_message('error', 'Failed to log auto refresh: ' . $e->getMessage());
        }
    }
}
    
    /**
     * 🔍 ตรวจสอบสถานะ Token อย่างง่าย
     */
    public function get_token_status() {
        $storage = $this->get_system_storage();
        if (!$storage) return 'no_storage';
        
        if (!$storage->google_access_token) return 'no_token';
        
        if (!$storage->google_token_expires) return 'no_expiry';
        
        $expires = strtotime($storage->google_token_expires);
        $now = time();
        $diff = $expires - $now;
        
        if ($diff <= 0) return 'expired';
        if ($diff <= 300) return 'critical'; // 5 นาที
        if ($diff <= 900) return 'warning';  // 15 นาที
        
        return 'healthy';
    }
}