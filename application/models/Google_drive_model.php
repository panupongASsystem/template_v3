<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive Model
 * จัดการข้อมูลการเชื่อมต่อ Google Drive
 */
class Google_drive_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * ดึงข้อมูล Google Drive ของสมาชิก
     */
    public function get_member_google_drive($member_id) {
        return $this->db->select('google_email, google_connected_at, google_account_verified, google_drive_enabled, google_token_expires')
                       ->from('tbl_member')
                       ->where('m_id', $member_id)
                       ->get()
                       ->row();
    }

    /**
     * ตรวจสอบว่าสมาชิกเชื่อมต่อ Google Drive แล้วหรือไม่
     */
    public function is_google_connected($member_id) {
        $result = $this->db->select('google_access_token, google_token_expires')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->where('google_drive_enabled', 1)
                          ->get()
                          ->row();
        
        if (!$result || empty($result->google_access_token)) {
            return false;
        }

        // ตรวจสอบว่า token หมดอายุหรือไม่
        if ($result->google_token_expires && strtotime($result->google_token_expires) <= time()) {
            return false;
        }

        return true;
    }

    /**
     * บันทึกข้อมูล Google OAuth Token
     */
    public function save_google_tokens($member_id, $data) {
        $update_data = [
            'google_email' => $data['email'],
            'google_access_token' => $data['access_token'],
            'google_refresh_token' => $data['refresh_token'],
            'google_token_expires' => date('Y-m-d H:i:s', time() + $data['expires_in']),
            'google_connected_at' => date('Y-m-d H:i:s'),
            'google_account_verified' => 1,
            'google_drive_enabled' => 1
        ];

        $this->db->where('m_id', $member_id);
        return $this->db->update('tbl_member', $update_data);
    }

    /**
     * ลบการเชื่อมต่อ Google Drive
     */
    public function disconnect_google_drive($member_id) {
        $update_data = [
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires' => null,
            'google_account_verified' => 0,
            'google_drive_enabled' => 0
        ];

        $this->db->where('m_id', $member_id);
        return $this->db->update('tbl_member', $update_data);
    }

    /**
     * ดึงข้อมูล Access Token
     */
    public function get_access_token($member_id) {
        $result = $this->db->select('google_access_token, google_refresh_token, google_token_expires')
                          ->from('tbl_member')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if (!$result) {
            return false;
        }

        // ตรวจสอบว่า token หมดอายุหรือไม่
        if ($result->google_token_expires && strtotime($result->google_token_expires) <= time()) {
            // Token หมดอายุ - ต้อง refresh
            return ['expired' => true, 'refresh_token' => $result->google_refresh_token];
        }

        return ['access_token' => $result->google_access_token];
    }

    /**
     * อัปเดต Access Token หลังจาก Refresh
     */
    public function update_access_token($member_id, $access_token, $expires_in) {
        $update_data = [
            'google_access_token' => $access_token,
            'google_token_expires' => date('Y-m-d H:i:s', time() + $expires_in)
        ];

        $this->db->where('m_id', $member_id);
        return $this->db->update('tbl_member', $update_data);
    }

    /**
     * บันทึกข้อมูล Folder
     */
    public function save_folder($member_id, $position_id, $folder_data) {
        $data = [
            'member_id' => $member_id,
            'position_id' => $position_id,
            'folder_id' => $folder_data['folder_id'],
            'folder_name' => $folder_data['folder_name'],
            'folder_type' => $folder_data['folder_type'] ?? 'position',
            'parent_folder_id' => $folder_data['parent_folder_id'] ?? null,
            'folder_url' => $folder_data['folder_url'] ?? null,
            'created_by' => $member_id
        ];

        // ตรวจสอบว่ามี folder นี้อยู่แล้วหรือไม่
        $existing = $this->db->where('member_id', $member_id)
                           ->where('position_id', $position_id)
                           ->get('tbl_google_drive_folders')
                           ->row();

        if ($existing) {
            // อัปเดต
            $data['updated_by'] = $member_id;
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $existing->id);
            return $this->db->update('tbl_google_drive_folders', $data);
        } else {
            // เพิ่มใหม่
            return $this->db->insert('tbl_google_drive_folders', $data);
        }
    }

    /**
     * ดึงข้อมูล Folders ของสมาชิก
     */
    public function get_member_folders($member_id) {
        return $this->db->select('gdf.*, p.pname as position_name')
                       ->from('tbl_google_drive_folders gdf')
                       ->join('tbl_position p', 'gdf.position_id = p.pid', 'left')
                       ->where('gdf.member_id', $member_id)
                       ->where('gdf.is_active', 1)
                       ->order_by('gdf.created_at', 'desc')
                       ->get()
                       ->result();
    }

    /**
     * บันทึก Permission
     */
    public function save_permission($folder_id, $member_id, $permission_data) {
        $data = [
            'folder_id' => $folder_id,
            'member_id' => $member_id,
            'google_email' => $permission_data['google_email'],
            'permission_type' => $permission_data['permission_type'],
            'google_permission_id' => $permission_data['google_permission_id'] ?? null
        ];

        return $this->db->insert('tbl_google_drive_permissions', $data);
    }

    /**
     * ดึงข้อมูล Permissions ของ Folder
     */
    public function get_folder_permissions($folder_id) {
        return $this->db->select('gdp.*, m.m_fname, m.m_lname')
                       ->from('tbl_google_drive_permissions gdp')
                       ->join('tbl_member m', 'gdp.member_id = m.m_id', 'left')
                       ->where('gdp.folder_id', $folder_id)
                       ->where('gdp.is_active', 1)
                       ->get()
                       ->result();
    }

    /**
     * เพิกถอน Permission
     */
    public function revoke_permission($permission_id) {
        $data = [
            'is_active' => 0,
            'revoked_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $permission_id);
        return $this->db->update('tbl_google_drive_permissions', $data);
    }

    /**
     * บันทึก Log
     */
    public function log_action($member_id, $action_type, $description, $additional_data = []) {
        $data = [
            'member_id' => $member_id,
            'action_type' => $action_type,
            'action_description' => $description,
            'folder_id' => $additional_data['folder_id'] ?? null,
            'target_email' => $additional_data['target_email'] ?? null,
            'status' => $additional_data['status'] ?? 'success',
            'error_message' => $additional_data['error_message'] ?? null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        ];

        return $this->db->insert('tbl_google_drive_logs', $data);
    }

    /**
     * ดึงข้อมูล Logs ของสมาชิก
     */
    public function get_member_logs($member_id, $limit = 50) {
        return $this->db->select('*')
                       ->from('tbl_google_drive_logs')
                       ->where('member_id', $member_id)
                       ->order_by('created_at', 'desc')
                       ->limit($limit)
                       ->get()
                       ->result();
    }

    /**
     * ดึงข้อมูลสมาชิกที่เชื่อมต่อ Google Drive
     */
    public function get_connected_members($search = '', $limit = 10, $offset = 0) {
        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.google_email, 
                          m.google_connected_at, m.google_account_verified, p.pname,
                          COUNT(gdf.id) as total_folders')
                ->from('tbl_member m')
                ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                ->join('tbl_google_drive_folders gdf', 'm.m_id = gdf.member_id AND gdf.is_active = 1', 'left')
                ->where('m.google_drive_enabled', 1);

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('m.m_fname', $search)
                    ->or_like('m.m_lname', $search)
                    ->or_like('m.m_email', $search)
                    ->or_like('m.google_email', $search)
                    ->group_end();
        }

        $this->db->group_by('m.m_id')
                ->order_by('m.google_connected_at', 'desc')
                ->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * นับจำนวนสมาชิกที่เชื่อมต่อ Google Drive
     */
    public function count_connected_members($search = '') {
        $this->db->from('tbl_member m')
                ->where('m.google_drive_enabled', 1);

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('m.m_fname', $search)
                    ->or_like('m.m_lname', $search)
                    ->or_like('m.m_email', $search)
                    ->or_like('m.google_email', $search)
                    ->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * ดึงการตั้งค่า Google Drive
     */
    public function get_setting($key) {
        $result = $this->db->select('setting_value')
                          ->from('tbl_google_drive_settings')
                          ->where('setting_key', $key)
                          ->where('is_active', 1)
                          ->get()
                          ->row();

        return $result ? $result->setting_value : null;
    }

    /**
     * อัปเดตการตั้งค่า Google Drive
     */
    public function update_setting($key, $value) {
        $existing = $this->db->where('setting_key', $key)->get('tbl_google_drive_settings')->row();

        if ($existing) {
            $this->db->where('setting_key', $key);
            return $this->db->update('tbl_google_drive_settings', ['setting_value' => $value]);
        } else {
            return $this->db->insert('tbl_google_drive_settings', [
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    }

    /**
     * ดึงข้อมูลสถิติ Google Drive
     */
    public function get_drive_statistics() {
        // จำนวนสมาชิกที่เชื่อมต่อ
        $connected_members = $this->db->where('google_drive_enabled', 1)
                                   ->count_all_results('tbl_member');

        // จำนวน Folders ทั้งหมด
        $total_folders = $this->db->where('is_active', 1)
                                 ->count_all_results('tbl_google_drive_folders');

        // จำนวนไฟล์ที่ Sync
        $synced_files = $this->db->where('sync_status', 'synced')
                                ->count_all_results('tbl_google_drive_sync');

        // การเชื่อมต่อใหม่ในเดือนนี้
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
     * ล้างข้อมูล Member ที่ถูกลบ
     */
    public function cleanup_deleted_member_data($member_id) {
        $this->db->trans_start();

        // ลบ folders
        $this->db->where('member_id', $member_id)->delete('tbl_google_drive_folders');
        
        // ลบ permissions
        $this->db->where('member_id', $member_id)->delete('tbl_google_drive_permissions');
        
        // ลบ sync files
        $this->db->where('member_id', $member_id)->delete('tbl_google_drive_sync');
        
        // เก็บ logs ไว้เพื่อ audit (ไม่ลบ)

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึง Google Drive ตามตำแหน่ง
     */
    public function check_drive_permission($member_id) {
        $member = $this->db->select('m.ref_pid, m.m_id')
                          ->from('tbl_member m')
                          ->where('m.m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            return ['allowed' => false, 'reason' => 'ไม่พบข้อมูลสมาชิก'];
        }

        $position_id = $member->ref_pid;

        // ID 1 และ 2 = สิทธิ์เต็ม
        if (in_array($position_id, [1, 2])) {
            return ['allowed' => true, 'access_type' => 'full', 'position_id' => $position_id];
        }

        // ID 3 = ตรวจสอบสิทธิ์ในโมดูล Google Drive (module_id = 2)
        if ($position_id == 3) {
            $permission = $this->db->select('mup.*')
                                  ->from('tbl_member_user_permissions mup')
                                  ->join('tbl_member_module_menus mmm', 'mup.system_id = mmm.id')
                                  ->where('mup.member_id', $member_id)
                                  ->where('mmm.module_id', 2)
                                  ->where('mup.is_active', 1)
                                  ->get()
                                  ->row();

            if ($permission) {
                return ['allowed' => true, 'access_type' => 'full', 'position_id' => $position_id];
            } else {
                return ['allowed' => false, 'reason' => 'ไม่มีสิทธิ์เข้าใช้งาน Google Drive'];
            }
        }

        // ID 4 ขึ้นไป = สิทธิ์เฉพาะ folder ของตำแหน่งตัวเอง
        if ($position_id >= 4) {
            return ['allowed' => true, 'access_type' => 'position_only', 'position_id' => $position_id];
        }

        return ['allowed' => false, 'reason' => 'ตำแหน่งไม่มีสิทธิ์เข้าใช้งาน'];
    }
}