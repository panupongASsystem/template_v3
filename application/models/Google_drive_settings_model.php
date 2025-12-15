<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive Settings Model
 * จัดการการตั้งค่า Google Drive และ Sync กับ Config File
 */
class Google_drive_settings_model extends CI_Model {

    private $table = 'tbl_google_drive_settings';
    private $cache_prefix = 'gdrive_settings_';
    private $cache_ttl = 3600; // 1 hour

    public function __construct() {
        parent::__construct();
        $this->load->driver('cache');
    }

    /**
     * ดึงค่า Setting ตัวเดียว
     */
    public function get_setting($key, $default = null) {
        $cache_key = $this->cache_prefix . $key;
        
        // ลองดึงจาก Cache ก่อน
        if ($this->cache->get($cache_key)) {
            return $this->cache->get($cache_key);
        }

        try {
            if ($this->db->table_exists($this->table)) {
                $result = $this->db->select('setting_value')
                                  ->from($this->table)
                                  ->where('setting_key', $key)
                                  ->where('is_active', 1)
                                  ->get()
                                  ->row();

                $value = $result ? $result->setting_value : $default;
                
                // เก็บใน Cache
                $this->cache->save($cache_key, $value, $this->cache_ttl);
                
                return $value;
            }
        } catch (Exception $e) {
            log_message('error', 'Get setting error: ' . $e->getMessage());
        }

        return $default;
    }

    /**
     * ดึงค่า Settings ทั้งหมด
     */
    public function get_all_settings() {
        $cache_key = $this->cache_prefix . 'all';
        
        // ลองดึงจาก Cache ก่อน
        if ($cached = $this->cache->get($cache_key)) {
            return $cached;
        }

        $settings = [];

        try {
            if ($this->db->table_exists($this->table)) {
                $results = $this->db->select('setting_key, setting_value, setting_description')
                                   ->from($this->table)
                                   ->where('is_active', 1)
                                   ->get()
                                   ->result();

                foreach ($results as $result) {
                    $settings[$result->setting_key] = [
                        'value' => $result->setting_value,
                        'description' => $result->setting_description
                    ];
                }
                
                // เก็บใน Cache
                $this->cache->save($cache_key, $settings, $this->cache_ttl);
            }
        } catch (Exception $e) {
            log_message('error', 'Get all settings error: ' . $e->getMessage());
        }

        return $settings;
    }

    /**
     * บันทึกค่า Setting
     */
    public function set_setting($key, $value, $description = null) {
        try {
            if (!$this->db->table_exists($this->table)) {
                log_message('error', 'Table ' . $this->table . ' does not exist');
                return false;
            }

            // ตรวจสอบว่ามีอยู่แล้วหรือไม่
            $existing = $this->db->select('id')
                                ->from($this->table)
                                ->where('setting_key', $key)
                                ->get()
                                ->row();

            $data = [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($description !== null) {
                $data['setting_description'] = $description;
            }

            if ($existing) {
                // Update
                $this->db->where('setting_key', $key);
                $result = $this->db->update($this->table, $data);
            } else {
                // Insert
                $data['setting_key'] = $key;
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['is_active'] = 1;
                
                if ($description === null) {
                    $data['setting_description'] = $this->get_default_description($key);
                }
                
                $result = $this->db->insert($this->table, $data);
            }

            if ($result) {
                // ลบ Cache
                $this->clear_cache($key);
                return true;
            }

        } catch (Exception $e) {
            log_message('error', 'Set setting error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * บันทึกหลายค่าพร้อมกัน
     */
    public function set_multiple_settings($settings) {
        $this->db->trans_start();

        foreach ($settings as $key => $value) {
            $this->set_setting($key, $value);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // ลบ Cache ทั้งหมด
            $this->clear_all_cache();
            return true;
        }

        return false;
    }

    /**
     * ลบ Setting
     */
    public function delete_setting($key) {
        try {
            if (!$this->db->table_exists($this->table)) {
                return false;
            }

            $result = $this->db->where('setting_key', $key)
                              ->delete($this->table);

            if ($result) {
                $this->clear_cache($key);
                return true;
            }

        } catch (Exception $e) {
            log_message('error', 'Delete setting error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * เปิด/ปิด Setting
     */
    public function toggle_setting($key, $active = true) {
        try {
            if (!$this->db->table_exists($this->table)) {
                return false;
            }

            $result = $this->db->where('setting_key', $key)
                              ->update($this->table, [
                                  'is_active' => $active ? 1 : 0,
                                  'updated_at' => date('Y-m-d H:i:s')
                              ]);

            if ($result) {
                $this->clear_cache($key);
                return true;
            }

        } catch (Exception $e) {
            log_message('error', 'Toggle setting error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * ล้าง Cache ตัวเดียว
     */
    private function clear_cache($key) {
        $this->cache->delete($this->cache_prefix . $key);
        $this->cache->delete($this->cache_prefix . 'all');
    }

    /**
     * ล้าง Cache ทั้งหมด
     */
    private function clear_all_cache() {
        // ล้าง Cache ที่ขึ้นต้นด้วย prefix
        $this->cache->clean();
    }

    /**
     * ดึงคำอธิบายเริ่มต้น
     */
    private function get_default_description($key) {
        $descriptions = [
            'google_client_id' => 'Google OAuth Client ID',
            'google_client_secret' => 'Google OAuth Client Secret',
            'google_redirect_uri' => 'Google OAuth Redirect URI',
            'google_drive_enabled' => 'เปิด/ปิดการใช้งาน Google Drive',
            'auto_create_folders' => 'สร้าง Folder อัตโนมัติตามตำแหน่ง',
            'max_file_size' => 'ขนาดไฟล์สูงสุด (bytes)',
            'allowed_file_types' => 'ประเภทไฟล์ที่อนุญาต',
            'cache_enabled' => 'เปิด/ปิดการใช้ Cache',
            'cache_ttl' => 'ระยะเวลา Cache (วินาที)',
            'logging_enabled' => 'เปิด/ปิดการบันทึก Log',
            'log_retention_days' => 'จำนวนวันที่เก็บ Log',
            'auto_sync_enabled' => 'เปิด/ปิดการ Sync อัตโนมัติ',
            'sync_interval' => 'ระยะเวลาการ Sync (นาที)',
            'encrypt_tokens' => 'เข้ารหัส Token ก่อนเก็บ',
            'session_timeout' => 'ระยะเวลาหมดอายุ Session (นาที)',
            'api_rate_limit' => 'จำกัดการเรียก API ต่อนาที',
            'admin_email' => 'อีเมลผู้ดูแลระบบ'
        ];

        return isset($descriptions[$key]) ? $descriptions[$key] : '';
    }

    /**
     * สร้าง Settings เริ่มต้น
     */
    public function create_default_settings() {
        $default_settings = [
            'google_client_id' => '',
            'google_client_secret' => '',
            'google_redirect_uri' => site_url('google_drive/oauth_callback'),
            'google_drive_enabled' => '1',
            'auto_create_folders' => '1',
            'max_file_size' => '104857600',
            'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
            'cache_enabled' => '1',
            'cache_ttl' => '3600',
            'logging_enabled' => '1',
            'log_retention_days' => '90',
            'auto_sync_enabled' => '1',
            'sync_interval' => '30',
            'encrypt_tokens' => '1',
            'session_timeout' => '120',
            'api_rate_limit' => '100',
            'admin_email' => ''
        ];

        $this->db->trans_start();

        foreach ($default_settings as $key => $value) {
            // ตรวจสอบว่ามีอยู่แล้วหรือไม่
            $exists = $this->db->select('id')
                              ->from($this->table)
                              ->where('setting_key', $key)
                              ->count_all_results();

            if ($exists == 0) {
                $this->db->insert($this->table, [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_description' => $this->get_default_description($key),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->clear_all_cache();
            return true;
        }

        return false;
    }

    /**
     * Export Settings เป็น Array สำหรับ Config File
     */
    public function export_for_config() {
        $settings = $this->get_all_settings();
        $config_array = [];

        foreach ($settings as $key => $data) {
            $value = $data['value'];

            // แปลงค่าตามประเภท
            switch ($key) {
                case 'google_drive_enabled':
                case 'auto_create_folders':
                case 'cache_enabled':
                case 'logging_enabled':
                case 'auto_sync_enabled':
                case 'encrypt_tokens':
                    $config_array[$key] = ($value == '1');
                    break;

                case 'max_file_size':
                case 'cache_ttl':
                case 'log_retention_days':
                case 'sync_interval':
                case 'session_timeout':
                case 'api_rate_limit':
                    $config_array[$key] = (int)$value;
                    break;

                case 'allowed_file_types':
                    $config_array[$key] = array_map('trim', explode(',', $value));
                    break;

                default:
                    $config_array[$key] = $value;
            }
        }

        return $config_array;
    }

    /**
     * สำรองข้อมูล Settings
     */
    public function backup_settings() {
        $settings = $this->get_all_settings();
        $backup_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'settings' => $settings
        ];

        $filename = 'google_drive_settings_backup_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = APPPATH . 'logs/' . $filename;

        if (file_put_contents($filepath, json_encode($backup_data, JSON_PRETTY_PRINT))) {
            log_message('info', 'Settings backed up to: ' . $filename);
            return $filename;
        }

        return false;
    }

    /**
     * กู้คืนข้อมูล Settings
     */
    public function restore_settings($backup_file) {
        $filepath = APPPATH . 'logs/' . $backup_file;

        if (!file_exists($filepath)) {
            return false;
        }

        $backup_data = json_decode(file_get_contents($filepath), true);

        if (!$backup_data || !isset($backup_data['settings'])) {
            return false;
        }

        $this->db->trans_start();

        foreach ($backup_data['settings'] as $key => $data) {
            $this->set_setting($key, $data['value'], $data['description']);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->clear_all_cache();
            log_message('info', 'Settings restored from: ' . $backup_file);
            return true;
        }

        return false;
    }

    /**
     * ตรวจสอบความถูกต้องของ Settings
     */
    public function validate_settings($settings) {
        $errors = [];

        // ตรวจสอบ Google Client ID
        if (!empty($settings['google_client_id'])) {
            $pattern = '/^[0-9]+-[a-zA-Z0-9]+\.apps\.googleusercontent\.com$/';
            if (!preg_match($pattern, $settings['google_client_id'])) {
                $errors[] = 'รูปแบบ Google Client ID ไม่ถูกต้อง';
            }
        }

        // ตรวจสอบ Redirect URI
        if (!empty($settings['google_redirect_uri'])) {
            if (!filter_var($settings['google_redirect_uri'], FILTER_VALIDATE_URL)) {
                $errors[] = 'รูปแบบ Redirect URI ไม่ถูกต้อง';
            }
        }

        // ตรวจสอบขนาดไฟล์
        if (isset($settings['max_file_size'])) {
            $max_size = (int)$settings['max_file_size'];
            if ($max_size < 1024 || $max_size > 5368709120) { // 1KB - 5GB
                $errors[] = 'ขนาดไฟล์ต้องอยู่ระหว่าง 1KB - 5GB';
            }
        }

        // ตรวจสอบอีเมล
        if (!empty($settings['admin_email'])) {
            if (!filter_var($settings['admin_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
            }
        }

        return empty($errors) ? true : $errors;
    }
}