<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive Hybrid Model
 * 
 * Model สำหรับจัดการข้อมูล Google Drive Hybrid System
 * รองรับทั้ง Personal และ System storage modes
 */

class Google_drive_hybrid_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ===============================================
    // USER MANAGEMENT
    // ===============================================

    /**
     * ดึงข้อมูลผู้ใช้พร้อมข้อมูล Google Drive
     */
    public function get_user_with_drive_info($user_id) {
        $this->db->select('m.*, p.pname as position_name');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->where('m.m_id', $user_id);
        
        $user = $this->db->get()->row_array();
        
        if (!$user) {
            return null;
        }

        // เพิ่มข้อมูลเริ่มต้นถ้าไม่มี
        $default_fields = [
            'google_email' => null,
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires' => null,
            'google_account_verified' => 0,
            'google_connected_at' => null,
            'use_personal_google' => 0,
            'preferred_drive_mode' => 'auto',
            'allow_external_sharing' => 0,
            'storage_access_granted' => 1,
            'storage_quota_limit' => 1073741824, // 1GB
            'storage_quota_used' => 0,
            'last_storage_access' => null
        ];

        foreach ($default_fields as $field => $default_value) {
            if (!isset($user[$field])) {
                $user[$field] = $default_value;
            }
        }

        return $user;
    }

    /**
     * อัปเดตข้อมูล Google Drive ของผู้ใช้
     */
    public function update_user_google_info($user_id, $data) {
        $this->db->where('m_id', $user_id);
        return $this->db->update('tbl_member', $data);
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึง Storage
     */
    public function check_storage_access($user_id) {
        $this->db->select('storage_access_granted, m_system');
        $this->db->where('m_id', $user_id);
        $user = $this->db->get('tbl_member')->row();

        if (!$user) {
            return false;
        }

        // Admin มีสิทธิ์เสมอ
        if (in_array($user->m_system, ['super_admin', 'system_admin'])) {
            return true;
        }

        return $user->storage_access_granted == 1;
    }

    // ===============================================
    // FILE METADATA MANAGEMENT
    // ===============================================

    /**
     * บันทึก Metadata ของไฟล์
     */
    public function save_file_metadata($data) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_file_metadata')) {
                return false;
            }

            $metadata = [
                'google_file_id' => $data['google_file_id'],
                'original_name' => $data['original_name'],
                'current_name' => $data['current_name'],
                'created_by' => $data['created_by'],
                'created_by_name' => $data['created_by_name'],
                'created_by_email' => $data['created_by_email'] ?? null,
                'file_size' => $data['file_size'],
                'mime_type' => $data['mime_type'],
                'folder_id' => $data['folder_id'],
                'storage_mode' => $data['storage_mode'],
                'owner_google_email' => $data['owner_google_email'],
                'web_view_link' => $data['web_view_link'] ?? null,
                'download_link' => $data['download_link'] ?? null,
                'is_shared_external' => $data['is_shared_external'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_file_metadata', $metadata);
            return $this->db->insert_id();

        } catch (Exception $e) {
            log_message('error', 'Save file metadata error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึง Metadata ของไฟล์
     */
    public function get_file_metadata($file_id) {
        if (!$this->db->table_exists('tbl_google_drive_file_metadata')) {
            return null;
        }

        $this->db->where('google_file_id', $file_id);
        $this->db->where('is_deleted', 0);
        return $this->db->get('tbl_google_drive_file_metadata')->row_array();
    }

    /**
     * ดึงไฟล์ทั้งหมดของผู้ใช้
     */
    public function get_user_files($user_id, $storage_mode = null) {
        if (!$this->db->table_exists('tbl_google_drive_file_metadata')) {
            return [];
        }

        $this->db->select('*');
        $this->db->where('created_by', $user_id);
        $this->db->where('is_deleted', 0);
        
        if ($storage_mode) {
            $this->db->where('storage_mode', $storage_mode);
        }
        
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('tbl_google_drive_file_metadata')->result_array();
    }

    /**
     * ทำเครื่องหมายไฟล์ว่าถูกลบ
     */
    public function mark_file_deleted($file_id) {
        if (!$this->db->table_exists('tbl_google_drive_file_metadata')) {
            return false;
        }

        $data = [
            'is_deleted' => 1,
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('google_file_id', $file_id);
        return $this->db->update('tbl_google_drive_file_metadata', $data);
    }

    // ===============================================
    // ACTIVITY LOGGING
    // ===============================================

    /**
     * บันทึก Activity Log
     */
    public function log_activity($data) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_file_activities')) {
                return false;
            }

            $log_data = [
                'google_file_id' => $data['google_file_id'] ?? null,
                'file_name' => $data['file_name'] ?? null,
                'user_id' => $data['user_id'],
                'user_name' => $data['user_name'],
                'user_email' => $data['user_email'] ?? null,
                'action_type' => $data['action_type'],
                'storage_mode' => $data['storage_mode'],
                'target_google_email' => $data['target_google_email'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
                'details' => is_array($data['details']) ? json_encode($data['details']) : $data['details'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_file_activities', $log_data);
            return $this->db->insert_id();

        } catch (Exception $e) {
            log_message('error', 'Log activity error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงประวัติการใช้งานของผู้ใช้
     */
    public function get_user_activities($user_id, $limit = 50) {
        if (!$this->db->table_exists('tbl_google_drive_file_activities')) {
            return [];
        }

        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get('tbl_google_drive_file_activities')->result_array();
    }

    /**
     * ดึงประวัติของไฟล์เฉพาะ
     */
    public function get_file_activities($file_id) {
        if (!$this->db->table_exists('tbl_google_drive_file_activities')) {
            return [];
        }

        $this->db->select('*');
        $this->db->where('google_file_id', $file_id);
        $this->db->order_by('created_at', 'DESC');
        
        return $this->db->get('tbl_google_drive_file_activities')->result_array();
    }

    // ===============================================
    // STORAGE STATISTICS
    // ===============================================

    /**
     * ดึงสถิติการใช้งาน Storage ของผู้ใช้
     */
    public function get_user_storage_stats($user_id) {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'system_files' => 0,
            'personal_files' => 0,
            'quota_used' => 0,
            'quota_limit' => 1073741824, // 1GB default
            'quota_percentage' => 0
        ];

        try {
            // ดึงข้อมูล quota จาก tbl_member
            $this->db->select('storage_quota_used, storage_quota_limit');
            $this->db->where('m_id', $user_id);
            $user_quota = $this->db->get('tbl_member')->row();
            
            if ($user_quota) {
                $stats['quota_used'] = $user_quota->storage_quota_used ?? 0;
                $stats['quota_limit'] = $user_quota->storage_quota_limit ?? 1073741824;
            }

            // ดึงสถิติไฟล์จาก metadata table
            if ($this->db->table_exists('tbl_google_drive_file_metadata')) {
                $this->db->select('
                    COUNT(*) as total_files,
                    SUM(file_size) as total_size,
                    SUM(CASE WHEN storage_mode = "system" THEN 1 ELSE 0 END) as system_files,
                    SUM(CASE WHEN storage_mode = "personal" THEN 1 ELSE 0 END) as personal_files
                ');
                $this->db->where('created_by', $user_id);
                $this->db->where('is_deleted', 0);
                
                $file_stats = $this->db->get('tbl_google_drive_file_metadata')->row();
                
                if ($file_stats) {
                    $stats['total_files'] = (int)$file_stats->total_files;
                    $stats['total_size'] = (int)$file_stats->total_size;
                    $stats['system_files'] = (int)$file_stats->system_files;
                    $stats['personal_files'] = (int)$file_stats->personal_files;
                }
            }

            // คำนวณเปอร์เซ็นต์
            if ($stats['quota_limit'] > 0) {
                $stats['quota_percentage'] = round(($stats['quota_used'] / $stats['quota_limit']) * 100, 2);
            }

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Get user storage stats error: ' . $e->getMessage());
            return $stats;
        }
    }

    /**
     * อัปเดตการใช้งาน Storage ของผู้ใช้
     */
    public function update_storage_usage($user_id, $file_size, $operation = 'add') {
        try {
            if ($operation === 'add') {
                $this->db->set('storage_quota_used', 'storage_quota_used + ' . (int)$file_size, false);
            } else {
                $this->db->set('storage_quota_used', 'GREATEST(0, storage_quota_used - ' . (int)$file_size . ')', false);
            }
            
            $this->db->set('last_storage_access', date('Y-m-d H:i:s'));
            $this->db->where('m_id', $user_id);
            
            return $this->db->update('tbl_member');

        } catch (Exception $e) {
            log_message('error', 'Update storage usage error: ' . $e->getMessage());
            return false;
        }
    }

    // ===============================================
    // SYSTEM SETTINGS
    // ===============================================

    /**
     * ดึงการตั้งค่าระบบ
     */
    public function get_setting($key, $default = null) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_settings')) {
                return $default;
            }

            $this->db->select('setting_value');
            $this->db->where('setting_key', $key);
            $this->db->where('is_active', 1);
            
            $result = $this->db->get('tbl_google_drive_settings')->row();
            
            return $result ? $result->setting_value : $default;

        } catch (Exception $e) {
            log_message('error', 'Get setting error: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * บันทึกการตั้งค่าระบบ
     */
    public function set_setting($key, $value, $description = null) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_settings')) {
                return false;
            }

            // ตรวจสอบว่ามีการตั้งค่านี้อยู่แล้วหรือไม่
            $this->db->where('setting_key', $key);
            $existing = $this->db->get('tbl_google_drive_settings')->row();

            $data = [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_description' => $description,
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($existing) {
                $this->db->where('setting_key', $key);
                return $this->db->update('tbl_google_drive_settings', $data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                return $this->db->insert('tbl_google_drive_settings', $data);
            }

        } catch (Exception $e) {
            log_message('error', 'Set setting error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงการตั้งค่าทั้งหมด
     */
    public function get_all_settings() {
        try {
            if (!$this->db->table_exists('tbl_google_drive_settings')) {
                return [];
            }

            $this->db->where('is_active', 1);
            $settings = $this->db->get('tbl_google_drive_settings')->result();
            
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->setting_key] = $setting->setting_value;
            }
            
            return $result;

        } catch (Exception $e) {
            log_message('error', 'Get all settings error: ' . $e->getMessage());
            return [];
        }
    }

    // ===============================================
    // SYSTEM STORAGE
    // ===============================================

    /**
     * ดึงข้อมูล System Storage
     */
    public function get_system_storage() {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                return null;
            }

            $this->db->where('is_active', 1);
            return $this->db->get('tbl_google_drive_system_storage')->row();

        } catch (Exception $e) {
            log_message('error', 'Get system storage error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * อัปเดตข้อมูล System Storage
     */
    public function update_system_storage($data) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                return false;
            }

            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $this->db->where('is_active', 1);
            return $this->db->update('tbl_google_drive_system_storage', $data);

        } catch (Exception $e) {
            log_message('error', 'Update system storage error: ' . $e->getMessage());
            return false;
        }
    }

    // ===============================================
    // PERMISSIONS & ACCESS CONTROL
    // ===============================================

    /**
     * ตรวจสอบสิทธิ์เข้าถึงโฟลเดอร์
     */
    public function check_folder_access($user_id, $folder_id, $access_type = 'read') {
        try {
            // Super Admin มีสิทธิ์ทุกอย่าง
            $user = $this->get_user_with_drive_info($user_id);
            if ($user && $user['m_system'] === 'super_admin') {
                return true;
            }

            // ตรวจสอบสิทธิ์โดยตรง
            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $this->db->select('access_type');
                $this->db->where('member_id', $user_id);
                $this->db->where('folder_id', $folder_id);
                $this->db->where('is_active', 1);
                
                $permission = $this->db->get('tbl_google_drive_member_folder_access')->row();
                
                if ($permission) {
                    return $this->check_access_level($permission->access_type, $access_type);
                }
            }

            // ตรวจสอบสิทธิ์ตามตำแหน่ง
            return $this->check_position_based_access($user_id, $folder_id, $access_type);

        } catch (Exception $e) {
            log_message('error', 'Check folder access error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบระดับสิทธิ์
     */
    private function check_access_level($user_access, $required_access) {
        $access_levels = [
            'read' => 1,
            'write' => 2,
            'admin' => 3,
            'owner' => 4
        ];

        $user_level = $access_levels[$user_access] ?? 0;
        $required_level = $access_levels[$required_access] ?? 0;

        return $user_level >= $required_level;
    }

    /**
     * ตรวจสอบสิทธิ์ตามตำแหน่งงาน
     */
    private function check_position_based_access($user_id, $folder_id, $access_type) {
        try {
            // สำหรับ Personal Mode ให้เข้าถึงได้ทุกอย่าง
            $user = $this->get_user_with_drive_info($user_id);
            if ($user && $user['use_personal_google'] == 1) {
                return true;
            }

            // สำหรับ System Mode ตรวจสอบตามตำแหน่ง
            // (Implementation ขึ้นอยู่กับโครงสร้างองค์กร)
            return true; // Default allow for now

        } catch (Exception $e) {
            log_message('error', 'Check position based access error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ให้สิทธิ์เข้าถึงโฟลเดอร์
     */
    public function grant_folder_access($user_id, $folder_id, $access_type, $granted_by = null) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_member_folder_access')) {
                return false;
            }

            // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
            $this->db->where('member_id', $user_id);
            $this->db->where('folder_id', $folder_id);
            $existing = $this->db->get('tbl_google_drive_member_folder_access')->row();

            $data = [
                'member_id' => $user_id,
                'folder_id' => $folder_id,
                'access_type' => $access_type,
                'permission_source' => 'direct',
                'granted_by' => $granted_by,
                'granted_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($existing) {
                $this->db->where('member_id', $user_id);
                $this->db->where('folder_id', $folder_id);
                return $this->db->update('tbl_google_drive_member_folder_access', $data);
            } else {
                return $this->db->insert('tbl_google_drive_member_folder_access', $data);
            }

        } catch (Exception $e) {
            log_message('error', 'Grant folder access error: ' . $e->getMessage());
            return false;
        }
    }

    // ===============================================
    // SHARING MANAGEMENT
    // ===============================================

    /**
     * บันทึกการแชร์ไฟล์/โฟลเดอร์
     */
    public function save_share_record($data) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_shared_access')) {
                return false;
            }

            $share_data = [
                'folder_id' => $data['item_id'],
                'shared_with_email' => $data['shared_with_email'],
                'shared_with_user_id' => $data['shared_with_user_id'] ?? null,
                'shared_by_user_id' => $data['shared_by_user_id'],
                'shared_by_name' => $data['shared_by_name'],
                'permission_level' => $data['permission_level'],
                'access_type' => $data['is_external'] ? 'external' : 'internal',
                'is_active' => 1,
                'expires_at' => $data['expires_at'] ?? null,
                'shared_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_google_drive_shared_access', $share_data);
            return $this->db->insert_id();

        } catch (Exception $e) {
            log_message('error', 'Save share record error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายการไฟล์ที่แชร์โดยผู้ใช้
     */
    public function get_user_shared_items($user_id) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_shared_access')) {
                return [];
            }

            $this->db->select('sa.*, fm.original_name, fm.current_name, fm.storage_mode');
            $this->db->from('tbl_google_drive_shared_access sa');
            $this->db->join('tbl_google_drive_file_metadata fm', 'sa.folder_id = fm.google_file_id', 'left');
            $this->db->where('sa.shared_by_user_id', $user_id);
            $this->db->where('sa.is_active', 1);
            $this->db->order_by('sa.shared_at', 'DESC');
            
            return $this->db->get()->result_array();

        } catch (Exception $e) {
            log_message('error', 'Get user shared items error: ' . $e->getMessage());
            return [];
        }
    }

    // ===============================================
    // REPORTING & ANALYTICS
    // ===============================================

    /**
     * ดึงสถิติรวมของระบบ
     */
    public function get_system_statistics() {
        $stats = [
            'total_users' => 0,
            'active_users' => 0,
            'total_files' => 0,
            'total_storage_used' => 0,
            'system_files' => 0,
            'personal_files' => 0,
            'total_activities' => 0
        ];

        try {
            // นับผู้ใช้ทั้งหมด
            $this->db->where('storage_access_granted', 1);
            $stats['total_users'] = $this->db->count_all_results('tbl_member');

            // นับผู้ใช้ที่ใช้งานจริง (30 วันล่าสุด)
            if ($this->db->table_exists('tbl_google_drive_file_activities')) {
                $this->db->distinct();
                $this->db->select('user_id');
                $this->db->where('created_at >=', date('Y-m-d', strtotime('-30 days')));
                $stats['active_users'] = $this->db->count_all_results('tbl_google_drive_file_activities');
            }

            // สถิติไฟล์
            if ($this->db->table_exists('tbl_google_drive_file_metadata')) {
                $this->db->select('
                    COUNT(*) as total_files,
                    SUM(file_size) as total_storage_used,
                    SUM(CASE WHEN storage_mode = "system" THEN 1 ELSE 0 END) as system_files,
                    SUM(CASE WHEN storage_mode = "personal" THEN 1 ELSE 0 END) as personal_files
                ');
                $this->db->where('is_deleted', 0);
                
                $file_stats = $this->db->get('tbl_google_drive_file_metadata')->row();
                
                if ($file_stats) {
                    $stats['total_files'] = (int)$file_stats->total_files;
                    $stats['total_storage_used'] = (int)$file_stats->total_storage_used;
                    $stats['system_files'] = (int)$file_stats->system_files;
                    $stats['personal_files'] = (int)$file_stats->personal_files;
                }
            }

            // นับกิจกรรมทั้งหมด
            if ($this->db->table_exists('tbl_google_drive_file_activities')) {
                $stats['total_activities'] = $this->db->count_all('tbl_google_drive_file_activities');
            }

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Get system statistics error: ' . $e->getMessage());
            return $stats;
        }
    }

    /**
     * ดึงสถิติการใช้งานรายวัน
     */
    public function get_daily_usage_stats($days = 30) {
        try {
            if (!$this->db->table_exists('tbl_google_drive_file_activities')) {
                return [];
            }

            $this->db->select('
                DATE(created_at) as date,
                COUNT(*) as total_activities,
                COUNT(DISTINCT user_id) as active_users,
                SUM(CASE WHEN action_type = "upload" THEN 1 ELSE 0 END) as uploads,
                SUM(CASE WHEN action_type = "download" THEN 1 ELSE 0 END) as downloads,
                SUM(CASE WHEN storage_mode = "personal" THEN 1 ELSE 0 END) as personal_activities,
                SUM(CASE WHEN storage_mode = "system" THEN 1 ELSE 0 END) as system_activities
            ');
            $this->db->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")));
            $this->db->group_by('DATE(created_at)');
            $this->db->order_by('date', 'ASC');
            
            return $this->db->get('tbl_google_drive_file_activities')->result_array();

        } catch (Exception $e) {
            log_message('error', 'Get daily usage stats error: ' . $e->getMessage());
            return [];
        }
    }

    // ===============================================
    // DATABASE MAINTENANCE
    // ===============================================

    /**
     * ตรวจสอบว่าตารางมีอยู่หรือไม่
     */
    public function table_exists($table_name) {
        return $this->db->table_exists($table_name);
    }

    /**
     * ตรวจสอบว่าคอลัมน์มีอยู่หรือไม่
     */
    public function column_exists($table, $column) {
        try {
            $fields = $this->db->list_fields($table);
            return in_array($column, $fields);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * ทำความสะอาดข้อมูลเก่า
     */
    public function cleanup_old_data($days = 90) {
        try {
            $cleanup_date = date('Y-m-d', strtotime("-{$days} days"));
            $deleted_count = 0;

            // ลบ Activity logs เก่า
            if ($this->db->table_exists('tbl_google_drive_file_activities')) {
                $this->db->where('created_at <', $cleanup_date);
                $this->db->delete('tbl_google_drive_file_activities');
                $deleted_count += $this->db->affected_rows();
            }

            // ลบไฟล์ที่ถูกทำเครื่องหมายลบแล้วนานกว่า 30 วัน
            if ($this->db->table_exists('tbl_google_drive_file_metadata')) {
                $delete_file_date = date('Y-m-d', strtotime('-30 days'));
                $this->db->where('is_deleted', 1);
                $this->db->where('deleted_at <', $delete_file_date);
                $this->db->delete('tbl_google_drive_file_metadata');
                $deleted_count += $this->db->affected_rows();
            }

            return [
                'success' => true,
                'deleted_records' => $deleted_count,
                'cleanup_date' => $cleanup_date
            ];

        } catch (Exception $e) {
            log_message('error', 'Cleanup old data error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * สำรองข้อมูลที่สำคัญ
     */
    public function backup_critical_data() {
        try {
            $backup_data = [
                'timestamp' => date('Y-m-d H:i:s'),
                'users' => [],
                'settings' => [],
                'system_storage' => null
            ];

            // สำรองข้อมูลผู้ใช้ที่มีการเชื่อมต่อ Google
            $this->db->select('m_id, m_username, m_fname, m_lname, google_email, use_personal_google, preferred_drive_mode');
            $this->db->where('google_email IS NOT NULL');
            $backup_data['users'] = $this->db->get('tbl_member')->result_array();

            // สำรองการตั้งค่า
            if ($this->db->table_exists('tbl_google_drive_settings')) {
                $backup_data['settings'] = $this->get_all_settings();
            }

            // สำรองข้อมูล System Storage
            $backup_data['system_storage'] = $this->get_system_storage();

            return $backup_data;

        } catch (Exception $e) {
            log_message('error', 'Backup critical data error: ' . $e->getMessage());
            return null;
        }
    }

    // ===============================================
    // UTILITY METHODS
    // ===============================================

    /**
     * ตรวจสอบว่าอีเมลเป็นของคนในองค์กรหรือไม่
     */
    public function is_internal_email($email) {
        $this->db->where('m_email', $email);
        $this->db->or_where('google_email', $email);
        return $this->db->count_all_results('tbl_member') > 0;
    }

    /**
     * ดึงข้อมูลผู้ใช้จากอีเมล
     */
    public function get_user_by_email($email) {
        $this->db->where('m_email', $email);
        $this->db->or_where('google_email', $email);
        return $this->db->get('tbl_member')->row_array();
    }

    /**
     * ตรวจสอบ Quota การใช้งาน
     */
    public function check_quota_status($user_id) {
        $user = $this->get_user_with_drive_info($user_id);
        if (!$user) {
            return null;
        }

        $quota_used = $user['storage_quota_used'] ?? 0;
        $quota_limit = $user['storage_quota_limit'] ?? 1073741824;
        $percentage = ($quota_limit > 0) ? ($quota_used / $quota_limit) * 100 : 0;

        return [
            'user_id' => $user_id,
            'quota_used' => $quota_used,
            'quota_limit' => $quota_limit,
            'quota_percentage' => round($percentage, 2),
            'quota_status' => $this->get_quota_status_text($percentage),
            'quota_available' => $quota_limit - $quota_used
        ];
    }

    /**
     * ดึงข้อความสถานะ Quota
     */
    private function get_quota_status_text($percentage) {
        if ($percentage >= 100) {
            return 'full';
        } elseif ($percentage >= 90) {
            return 'critical';
        } elseif ($percentage >= 75) {
            return 'warning';
        } else {
            return 'normal';
        }
    }

    /**
     * จัดรูปแบบขนาดไฟล์
     */
    public function format_file_size($bytes) {
        if (!$bytes || $bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * แปลงวันที่เป็นรูปแบบที่อ่านง่าย
     */
    public function time_ago($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'เมื่อสักครู่';
        if ($time < 3600) return floor($time/60) . ' นาทีที่แล้ว';
        if ($time < 86400) return floor($time/3600) . ' ชั่วโมงที่แล้ว';
        if ($time < 2592000) return floor($time/86400) . ' วันที่แล้ว';
        
        return date('d/m/Y', strtotime($datetime));
    }
}

?>