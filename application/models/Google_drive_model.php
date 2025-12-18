<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Google Drive Model
 * จัดการข้อมูลการเชื่อมต่อ Google Drive
 * 
 * ============================================
 * รายการตารางที่ใช้งานในระบบ
 * ============================================
 * 
 * 1. tbl_member - ตารางข้อมูลสมาชิก/ผู้ใช้งานระบบ
 * 2. tbl_position - ตารางข้อมูลตำแหน่ง/แผนก
 * 3. tbl_google_drive_folders - ตารางเก็บข้อมูลโฟลเดอร์ Google Drive
 * 4. tbl_google_drive_permissions - ตารางเก็บสิทธิ์การเข้าถึงโฟลเดอร์ (เวอร์ชันเก่า)
 * 5. tbl_google_drive_member_folder_access - ตารางเก็บสิทธิ์การเข้าถึงโฟลเดอร์ (เวอร์ชันใหม่)
 * 6. tbl_google_drive_logs - ตารางบันทึก Log การทำงาน
 * 7. tbl_google_drive_sync - ตารางเก็บข้อมูลไฟล์ที่ Sync
 * 8. tbl_google_drive_settings - ตารางการตั้งค่าระบบ Google Drive
 * 9. tbl_member_user_permissions - ตารางสิทธิ์ผู้ใช้งานในระบบ
 * 10. tbl_member_module_menus - ตารางเมนูโมดูลในระบบ
 * 
 * ============================================
 */
class Google_drive_model extends CI_Model
{

    // ========================================
    // ✅ รายการ Action Types สำหรับ History Log
    // ========================================

    /**
     * Action Types ที่ต้องการแสดงใน Permission History
     * 
     * ใช้ร่วมกันใน:
     * - get_user_permission_history()
     * - get_folder_permission_history()
     * - count_user_permission_history()
     * 
     * หมายเหตุ: แก้ไขที่นี่ที่เดียว จะมีผลกับทุกฟังก์ชัน
     */
    private $history_action_types = [
        // ========================================
        // Permission Actions - การจัดการสิทธิ์
        // ========================================
        'grant_permission',          // เพิ่มสิทธิ์ใหม่
        'update_folder_permission',  // แก้ไขสิทธิ์
        'remove_folder_permission',  // ลบสิทธิ์
        'restore_folder_permission', // คืนสิทธิ์

        // ========================================
        // Storage Actions - การจัดการ Storage
        // ========================================
        'enable_storage_access',     // เปิดใช้งาน Storage
        'disable_storage_access',    // ปิดใช้งาน Storage

        // ========================================
        // System Actions - การจัดการระบบ (NEW!)
        // ========================================
        'update_system_permissions', // แก้ไขสิทธิ์ระบบ
        'reset_to_default'          // รีเซ็ตเป็นค่าเริ่มต้น
    ];

    /**
     * ประกาศตัวแปรตารางที่ใช้ในระบบ
     */
    // ตารางหลัก
    protected $tbl_member = 'tbl_member';
    protected $tbl_position = 'tbl_position';

    // ตารางเฉพาะ Google Drive
    protected $tbl_google_drive_folders = 'tbl_google_drive_folders';
    protected $tbl_google_drive_permissions = 'tbl_google_drive_permissions';
    protected $tbl_google_drive_member_folder_access = 'tbl_google_drive_member_folder_access';
    protected $tbl_google_drive_logs = 'tbl_google_drive_logs';
    protected $tbl_google_drive_sync = 'tbl_google_drive_sync';
    protected $tbl_google_drive_settings = 'tbl_google_drive_settings';

    // ตารางสิทธิ์และโมดูล
    protected $tbl_member_user_permissions = 'tbl_member_user_permissions';
    protected $tbl_member_module_menus = 'tbl_member_module_menus';

    public function __construct()
    {
        parent::__construct();
        log_message('info', 'Google_drive_model: Model initialized');
    }

    /**
     * ดึงข้อมูล Google Drive ของสมาชิก
     */
    public function get_member_google_drive($member_id)
    {
        log_message('info', 'Google_drive_model: get_member_google_drive() called for member_id: ' . $member_id);

        $result = $this->db->select('google_email, google_connected_at, google_account_verified, google_drive_enabled, google_token_expires')
            ->from($this->tbl_member)
            ->where('m_id', $member_id)
            ->get()
            ->row();

        log_message('info', 'Google_drive_model: get_member_google_drive() result: ' . ($result ? 'found' : 'not found'));

        return $result;
    }

    /**
     * ตรวจสอบว่าสมาชิกเชื่อมต่อ Google Drive แล้วหรือไม่
     */
    public function is_google_connected($member_id)
    {
        log_message('info', 'Google_drive_model: is_google_connected() called for member_id: ' . $member_id);

        $result = $this->db->select('google_access_token, google_token_expires')
            ->from($this->tbl_member)
            ->where('m_id', $member_id)
            ->where('google_drive_enabled', 1)
            ->get()
            ->row();

        if (!$result || empty($result->google_access_token)) {
            log_message('info', 'Google_drive_model: is_google_connected() - No token found or token is empty');
            return false;
        }

        // ตรวจสอบว่า token หมดอายุหรือไม่
        if ($result->google_token_expires && strtotime($result->google_token_expires) <= time()) {
            log_message('info', 'Google_drive_model: is_google_connected() - Token expired');
            return false;
        }

        log_message('info', 'Google_drive_model: is_google_connected() - Connected successfully');
        return true;
    }

    /**
     * บันทึกข้อมูล Google OAuth Token
     */
    public function save_google_tokens($member_id, $data)
    {
        log_message('info', 'Google_drive_model: save_google_tokens() called for member_id: ' . $member_id);

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
        $result = $this->db->update($this->tbl_member, $update_data);

        log_message('info', 'Google_drive_model: save_google_tokens() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * ลบการเชื่อมต่อ Google Drive
     */
    public function disconnect_google_drive($member_id)
    {
        log_message('info', 'Google_drive_model: disconnect_google_drive() called for member_id: ' . $member_id);

        $update_data = [
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires' => null,
            'google_account_verified' => 0,
            'google_drive_enabled' => 0
        ];

        $this->db->where('m_id', $member_id);
        $result = $this->db->update($this->tbl_member, $update_data);

        log_message('info', 'Google_drive_model: disconnect_google_drive() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * ดึงข้อมูล Access Token
     */
    public function get_access_token($member_id)
    {
        log_message('info', 'Google_drive_model: get_access_token() called for member_id: ' . $member_id);

        $result = $this->db->select('google_access_token, google_refresh_token, google_token_expires')
            ->from($this->tbl_member)
            ->where('m_id', $member_id)
            ->get()
            ->row();

        if (!$result) {
            log_message('info', 'Google_drive_model: get_access_token() - No result found');
            return false;
        }

        // ตรวจสอบว่า token หมดอายุหรือไม่
        if ($result->google_token_expires && strtotime($result->google_token_expires) <= time()) {
            log_message('info', 'Google_drive_model: get_access_token() - Token expired, returning refresh token');
            // Token หมดอายุ - ต้อง refresh
            return ['expired' => true, 'refresh_token' => $result->google_refresh_token];
        }

        log_message('info', 'Google_drive_model: get_access_token() - Returning valid access token');
        return ['access_token' => $result->google_access_token];
    }

    /**
     * อัปเดต Access Token หลังจาก Refresh
     */
    public function update_access_token($member_id, $access_token, $expires_in)
    {
        log_message('info', 'Google_drive_model: update_access_token() called for member_id: ' . $member_id);

        $update_data = [
            'google_access_token' => $access_token,
            'google_token_expires' => date('Y-m-d H:i:s', time() + $expires_in)
        ];

        $this->db->where('m_id', $member_id);
        $result = $this->db->update($this->tbl_member, $update_data);

        log_message('info', 'Google_drive_model: update_access_token() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * บันทึกข้อมูล Folder
     */
    public function save_folder($member_id, $position_id, $folder_data)
    {
        log_message('info', 'Google_drive_model: save_folder() called for member_id: ' . $member_id . ', position_id: ' . $position_id);

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
            ->get($this->tbl_google_drive_folders)
            ->row();

        if ($existing) {
            log_message('info', 'Google_drive_model: save_folder() - Updating existing folder');
            // อัปเดต
            $data['updated_by'] = $member_id;
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $existing->id);
            $result = $this->db->update($this->tbl_google_drive_folders, $data);
        } else {
            log_message('info', 'Google_drive_model: save_folder() - Inserting new folder');
            // เพิ่มใหม่
            $result = $this->db->insert($this->tbl_google_drive_folders, $data);
        }

        log_message('info', 'Google_drive_model: save_folder() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * ดึงข้อมูล Folders ของสมาชิก
     */
    public function get_member_folders($member_id)
    {
        log_message('info', 'Google_drive_model: get_member_folders() called for member_id: ' . $member_id);

        $result = $this->db->select('gdf.*, p.pname as position_name')
            ->from($this->tbl_google_drive_folders . ' gdf')
            ->join($this->tbl_position . ' p', 'gdf.position_id = p.pid', 'left')
            ->where('gdf.member_id', $member_id)
            ->where('gdf.is_active', 1)
            ->order_by('gdf.created_at', 'desc')
            ->get()
            ->result();

        log_message('info', 'Google_drive_model: get_member_folders() found ' . count($result) . ' folders');

        return $result;
    }

    /**
     * บันทึก Permission
     */
    public function save_permission($folder_id, $member_id, $permission_data)
    {
        log_message('info', 'Google_drive_model: save_permission() called for folder_id: ' . $folder_id . ', member_id: ' . $member_id);

        $data = [
            'folder_id' => $folder_id,
            'member_id' => $member_id,
            'google_email' => $permission_data['google_email'],
            'permission_type' => $permission_data['permission_type'],
            'google_permission_id' => $permission_data['google_permission_id'] ?? null
        ];

        $result = $this->db->insert($this->tbl_google_drive_permissions, $data);

        log_message('info', 'Google_drive_model: save_permission() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * ดึงข้อมูล Permissions ของ Folder
     */
    public function get_folder_permissions($folder_id)
    {
        log_message('info', 'Google_drive_model: get_folder_permissions() called for folder_id: ' . $folder_id);

        $result = $this->db->select('gdp.*, m.m_fname, m.m_lname')
            ->from($this->tbl_google_drive_permissions . ' gdp')
            ->join($this->tbl_member . ' m', 'gdp.member_id = m.m_id', 'left')
            ->where('gdp.folder_id', $folder_id)
            ->where('gdp.is_active', 1)
            ->get()
            ->result();

        log_message('info', 'Google_drive_model: get_folder_permissions() found ' . count($result) . ' permissions');

        return $result;
    }

    /**
     * เพิกถอน Permission
     */
    public function revoke_permission($permission_id)
    {
        log_message('info', 'Google_drive_model: revoke_permission() called for permission_id: ' . $permission_id);

        $data = [
            'is_active' => 0,
            'revoked_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $permission_id);
        $result = $this->db->update($this->tbl_google_drive_permissions, $data);

        log_message('info', 'Google_drive_model: revoke_permission() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }
    /**
     * ✅ บันทึก Log แบบปลอดภัย (ไม่กระทบการทำงานหลัก)
     * 
     * @param int    $member_id          ผู้ทำการเปลี่ยนแปลง
     * @param string $action_type        ประเภท action
     * @param string $description        คำอธิบาย
     * @param array  $additional_data    ข้อมูลเพิ่มเติม
     * @param bool   $critical           ถ้า true จะ throw exception เมื่อ log ไม่สำเร็จ
     * @return bool
     */
    public function log_action($member_id, $action_type, $description, $additional_data = [], $critical = false)
    {
        try {
            log_message('info', "📝 Logging action: {$action_type} by member: {$member_id}");

            // ✅ Prepare data
            $data = [
                'member_id' => $member_id,
                'action_type' => $action_type,
                'action_description' => $description,
                'module' => 'google_drive_system',
                'folder_id' => $additional_data['folder_id'] ?? null,
                'target_email' => $additional_data['target_email'] ?? null,
                'status' => $additional_data['status'] ?? 'success',
                'error_message' => $additional_data['error_message'] ?? null,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => substr($this->input->user_agent(), 0, 500)
            ];

            // ✅ เก็บข้อมูลเพิ่มเติมเป็น JSON (ยกเว้นที่ใส่แล้ว)
            $json_fields = ['folder_id', 'target_email', 'status', 'error_message'];
            $filtered_additional = array_diff_key($additional_data, array_flip($json_fields));

            if (!empty($filtered_additional)) {
                $data['additional_data'] = json_encode($filtered_additional, JSON_UNESCAPED_UNICODE);
            }

            // ✅ Insert log
            $result = $this->db->insert($this->tbl_google_drive_logs, $data);

            if ($result) {
                log_message('info', "✅ Log saved successfully (ID: {$this->db->insert_id()})");
                return true;
            } else {
                $db_error = $this->db->error();
                log_message('error', "❌ Failed to save log: {$db_error['message']}");

                if ($critical) {
                    throw new Exception("Failed to save critical log: {$db_error['message']}");
                }

                return false;
            }

        } catch (Exception $e) {
            log_message('error', "❌ Exception in log_action: {$e->getMessage()}");

            if ($critical) {
                throw $e;
            }

            return false;
        }
    }


    /**
     * ✅ บันทึก Permission Log โดยเฉพาะ
     */
    public function log_permission_action($action_type, $permission_data, $current_user_id)
    {
        try {
            // ✅ สร้างคำอธิบายตามภาษา
            $description = $this->build_permission_description($action_type, $permission_data);

            // ✅ เตรียมข้อมูลสำหรับ additional_data
            $additional = [
                'permission_id' => $permission_data['permission_id'] ?? null,
                'target_member_id' => $permission_data['member_id'] ?? null,
                'target_member_name' => $permission_data['member_name'] ?? null,
                'folder_name' => $permission_data['folder_name'] ?? null,
                'folder_id' => $permission_data['folder_id'] ?? null,
                'access_type' => $permission_data['access_type'] ?? null,
                'old_access_type' => $permission_data['old_access_type'] ?? null,
                'permission_source' => $permission_data['permission_source'] ?? null,
                'granted_by' => $permission_data['granted_by'] ?? null,
                'granted_at' => $permission_data['granted_at'] ?? null
            ];

            // ✅ เรียก log_action
            return $this->log_action(
                $current_user_id,
                $action_type,
                $description,
                array_merge($additional, [
                    'target_email' => $permission_data['target_email'] ?? null,
                    'folder_id' => $permission_data['folder_id'] ?? null
                ]),
                false // non-critical
            );

        } catch (Exception $e) {
            log_message('error', "❌ Failed to log permission action: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * ✅ สร้างคำอธิบายภาษาไทย (อัปเดต: เพิ่ม update_system_permissions)
     */
    private function build_permission_description($action_type, $data)
    {
        $member_name = $data['member_name'] ?? 'ไม่ระบุชื่อ';
        $folder_name = $data['folder_name'] ?? 'ไม่ระบุโฟลเดอร์';

        switch ($action_type) {
            case 'grant_permission':
                return "เพิ่มสิทธิ์ {$data['access_type']} ให้ {$member_name} ในโฟลเดอร์ {$folder_name}";

            case 'update_folder_permission':
                $old = $data['old_access_type'] ?? 'unknown';
                $new = $data['access_type'] ?? 'unknown';
                return "แก้ไขสิทธิ์ {$member_name} จาก {$old} เป็น {$new} ในโฟลเดอร์ {$folder_name}";

            case 'remove_folder_permission':
                return "ลบสิทธิ์ {$data['access_type']} ของ {$member_name} ในโฟลเดอร์ {$folder_name}";

            case 'restore_folder_permission':
                return "คืนสิทธิ์ {$data['access_type']} ให้ {$member_name} ในโฟลเดอร์ {$folder_name}";

            case 'update_system_permissions':
                // สำหรับการแก้ไขสิทธิ์ระบบ
                $changes = [];

                // ตรวจสอบการเปลี่ยนแปลงแต่ละประเภท
                if (isset($data['storage_access'])) {
                    $changes[] = 'Storage Access: ' . ($data['storage_access'] ? 'เปิด' : 'ปิด');
                }
                if (isset($data['can_create_folder'])) {
                    $changes[] = 'สร้างโฟลเดอร์: ' . ($data['can_create_folder'] ? 'ได้' : 'ไม่ได้');
                }
                if (isset($data['can_share'])) {
                    $changes[] = 'แชร์ไฟล์: ' . ($data['can_share'] ? 'ได้' : 'ไม่ได้');
                }
                if (isset($data['can_delete'])) {
                    $changes[] = 'ลบไฟล์: ' . ($data['can_delete'] ? 'ได้' : 'ไม่ได้');
                }
                if (isset($data['storage_quota_limit'])) {
                    $quota_gb = round($data['storage_quota_limit'] / 1073741824, 2);
                    $changes[] = "Quota: {$quota_gb} GB";
                }
                if (isset($data['override_position'])) {
                    $changes[] = 'แทนที่สิทธิ์ตำแหน่ง: ' . ($data['override_position'] ? 'ใช่' : 'ไม่');
                }

                $description = "แก้ไขสิทธิ์ระบบสำหรับ {$member_name}";
                if (!empty($changes)) {
                    $description .= ' - ' . implode(', ', $changes);
                }

                return $description;

            case 'reset_to_default':
                // ✅ NEW: รองรับการรีเซ็ตสิทธิ์เป็นค่าเริ่มต้น
                $old_count = $data['old_permissions_removed'] ?? 0;
                $new_count = $data['new_permissions_created'] ?? 0;

                $description = "รีเซ็ตสิทธิ์เข้าถึงโฟลเดอร์เป็นค่าเริ่มต้นสำหรับ {$member_name}";

                // เพิ่มรายละเอียดจำนวนสิทธิ์
                if ($old_count > 0 || $new_count > 0) {
                    $details = [];

                    if ($old_count > 0) {
                        $details[] = "ลบสิทธิ์เดิม {$old_count} รายการ";
                    }

                    if ($new_count > 0) {
                        $details[] = "สร้างสิทธิ์ใหม่ {$new_count} รายการ";
                    }

                    $description .= ' (' . implode(', ', $details) . ')';
                }

                // เพิ่มรายละเอียดโฟลเดอร์ที่ได้รับสิทธิ์
                if (!empty($data['permission_details']) && is_array($data['permission_details'])) {
                    $folder_types = array_map(function ($detail) {
                        return $detail['folder_type'] ?? 'Unknown';
                    }, $data['permission_details']);

                    if (!empty($folder_types)) {
                        $description .= ' - โฟลเดอร์: ' . implode(', ', $folder_types);
                    }
                }

                // เพิ่มข้อมูล personal folder ถ้ามี
                if (!empty($data['personal_folder_id'])) {
                    $description .= ' [เก็บ Personal Folder: ' . substr($data['personal_folder_id'], 0, 8) . '...]';
                }

                return $description;

            default:
                return "เปลี่ยนแปลงสิทธิ์ของ {$member_name} ในโฟลเดอร์ {$folder_name}";
        }
    }


    /**
     * ✅ ดึงประวัติการเปลี่ยนแปลง Permission ของผู้ใช้งาน
     * รวมทั้ง Storage Access Actions
     */
    public function get_user_permission_history($user_id, $limit = 20, $offset = 0)
    {
        try {
            log_message('info', "========================================");
            log_message('info', "📜 Getting permission history");
            log_message('info', "========================================");
            log_message('info', "  User ID: {$user_id}");
            log_message('info', "  Limit: {$limit}");
            log_message('info', "  Offset: {$offset}");
            log_message('info', "  Allowed Action Types: " . implode(', ', $this->history_action_types));

            // ตรวจสอบว่าตาราง logs มีอยู่
            if (!$this->db->table_exists('tbl_google_drive_logs')) {
                log_message('error', '❌ Table tbl_google_drive_logs does not exist');
                return [];
            }

            log_message('info', '✅ Table exists, building query...');

            // สร้าง Query
            $this->db->select("
            l.id,
            l.action_type,
            l.action_description,
            l.created_at,
            l.status,
            l.folder_id,
            l.ip_address,
            l.additional_data,
            CONCAT(COALESCE(m.m_fname, ''), ' ', COALESCE(m.m_lname, '')) as by_user_name,
            m.m_username as by_user_email
        ", false);

            $this->db->from('tbl_google_drive_logs l');
            $this->db->join('tbl_member m', 'l.member_id = m.m_id', 'left');

            // ========================================
            // ✅ Filter ด้วย Class Property
            // ========================================
            log_message('info', '🔍 Applying action type filter...');
            $this->db->where_in('l.action_type', $this->history_action_types);

            log_message('info', '🔍 Applying user filter...');

            // Filter โดย user_id
            $this->db->group_start();
            $this->db->where(
                "JSON_UNQUOTE(JSON_EXTRACT(l.additional_data, '$.target_member_id')) = ",
                $user_id,
                false
            );
            $this->db->or_where('l.member_id', $user_id);
            $this->db->group_end();

            log_message('info', '📅 Ordering by created_at DESC');
            $this->db->order_by('l.created_at', 'DESC');

            log_message('info', "📊 Limiting to {$limit} records, offset {$offset}");
            $this->db->limit($limit, $offset);

            // Execute Query
            log_message('info', '⚡ Executing query...');
            $query = $this->db->get();
            $results = $query->result_array();

            $count = count($results);
            log_message('info', "✅ Query successful - Found {$count} records");

            if ($count > 0) {
                log_message('debug', 'Last Query: ' . $this->db->last_query());
            }

            // Process และ Format ข้อมูล
            log_message('info', '🔄 Processing results...');
            $history = [];

            foreach ($results as $index => $row) {
                $additional = [];
                if (!empty($row['additional_data'])) {
                    $decoded = json_decode($row['additional_data'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $additional = $decoded;
                    } else {
                        log_message('warning', "⚠️ Failed to decode JSON for log ID {$row['id']}: " . json_last_error_msg());
                    }
                }

                $history[] = [
                    'id' => $row['id'],
                    'action_type' => $row['action_type'],
                    'action_description' => $row['action_description'],
                    'created_at' => $row['created_at'],
                    'status' => $row['status'],
                    'folder_id' => $row['folder_id'],
                    'ip_address' => $row['ip_address'],
                    'by_user_name' => trim($row['by_user_name']) ?: 'ระบบ',
                    'by_user_email' => $row['by_user_email'],

                    'details' => [
                        'permission_id' => $additional['permission_id'] ?? null,
                        'target_member_id' => $additional['target_member_id'] ?? null,
                        'target_member_name' => $additional['target_member_name'] ?? null,
                        'folder_name' => $additional['folder_name'] ?? null,
                        'access_type' => $additional['access_type'] ?? null,
                        'old_access_type' => $additional['old_access_type'] ?? null,
                        'permission_source' => $additional['permission_source'] ?? null,
                        'granted_by' => $additional['granted_by'] ?? null,
                        'granted_at' => $additional['granted_at'] ?? null
                    ]
                ];

                if ($index < 3) {
                    log_message('debug', "  Record #{$index}: {$row['action_type']} - {$row['action_description']}");
                }
            }

            log_message('info', "========================================");
            log_message('info', "✅ get_user_permission_history completed");
            log_message('info', "  Returned: {$count} records");
            log_message('info', "========================================");

            return $history;

        } catch (Exception $e) {
            log_message('error', "========================================");
            log_message('error', "❌ get_user_permission_history ERROR");
            log_message('error', "========================================");
            log_message('error', "  User ID: {$user_id}");
            log_message('error', "  Message: {$e->getMessage()}");
            log_message('error', "  File: {$e->getFile()}");
            log_message('error', "  Line: {$e->getLine()}");
            log_message('debug', "  Stack trace:");
            log_message('debug', $e->getTraceAsString());
            log_message('error', "========================================");

            return [];
        }
    }

    /**
     * ✅ นับจำนวนประวัติการเปลี่ยนแปลง Permission ทั้งหมดของ User
     */
    public function count_user_permission_history($user_id)
    {
        try {
            log_message('info', "🔢 Counting permission history for user: {$user_id}");

            // ตรวจสอบว่าตาราง logs มีอยู่
            if (!$this->db->table_exists('tbl_google_drive_logs')) {
                log_message('error', '❌ Table tbl_google_drive_logs does not exist');
                return 0;
            }

            // สร้าง Query
            $this->db->from('tbl_google_drive_logs l');

            // ========================================
            // ✅ Filter ด้วย Class Property
            // ========================================
            $this->db->where_in('l.action_type', $this->history_action_types);

            // Filter โดย user_id
            $this->db->group_start();
            $this->db->where(
                "JSON_UNQUOTE(JSON_EXTRACT(l.additional_data, '$.target_member_id')) = ",
                $user_id,
                false
            );
            $this->db->or_where('l.member_id', $user_id);
            $this->db->group_end();

            // นับจำนวน
            $count = $this->db->count_all_results();

            log_message('info', "✅ Total count: {$count}");

            return (int) $count;

        } catch (Exception $e) {
            log_message('error', "❌ count_user_permission_history error: {$e->getMessage()}");
            log_message('debug', 'Stack trace: ' . $e->getTraceAsString());
            return 0;
        }
    }

    /**
     * ✅ ดึงประวัติการเปลี่ยนแปลง Permission ของ Folder เฉพาะ
     */
    public function get_folder_permission_history($folder_id, $limit = 20, $offset = 0)
    {
        try {
            log_message('info', "========================================");
            log_message('info', "📁 Getting folder permission history");
            log_message('info', "========================================");
            log_message('info', "  Folder ID: {$folder_id}");
            log_message('info', "  Limit: {$limit}");
            log_message('info', "  Offset: {$offset}");
            log_message('info', "  Allowed Action Types: " . implode(', ', $this->history_action_types));

            // ตรวจสอบว่าตาราง logs มีอยู่
            if (!$this->db->table_exists('tbl_google_drive_logs')) {
                log_message('error', '❌ Table tbl_google_drive_logs does not exist');
                return [];
            }

            log_message('info', '✅ Table exists, building query...');

            // สร้าง Query
            $this->db->select("
            l.id,
            l.action_type,
            l.action_description,
            l.created_at,
            l.status,
            l.ip_address,
            l.additional_data,
            l.folder_id,
            CONCAT(COALESCE(m.m_fname, ''), ' ', COALESCE(m.m_lname, '')) as by_user_name,
            m.m_username as by_user_email
        ", false);

            $this->db->from('tbl_google_drive_logs l');
            $this->db->join('tbl_member m', 'l.member_id = m.m_id', 'left');

            // ========================================
            // ✅ Filter ด้วย Class Property
            // ========================================
            log_message('info', '🔍 Applying action type filter...');
            $this->db->where_in('l.action_type', $this->history_action_types);

            // Filter by folder_id
            log_message('info', '🔍 Filtering by folder_id...');
            $this->db->where('l.folder_id', $folder_id);

            log_message('info', '📅 Ordering by created_at DESC');
            $this->db->order_by('l.created_at', 'DESC');

            log_message('info', "📊 Limiting to {$limit} records, offset {$offset}");
            $this->db->limit($limit, $offset);

            // Execute Query
            log_message('info', '⚡ Executing query...');
            $query = $this->db->get();
            $results = $query->result_array();

            $count = count($results);
            log_message('info', "✅ Query successful - Found {$count} records");

            if ($count > 0) {
                log_message('debug', 'Last Query: ' . $this->db->last_query());
            }

            // Process และ Format ข้อมูล (เหมือน get_user_permission_history)
            log_message('info', '🔄 Processing results...');
            $history = [];

            foreach ($results as $index => $row) {
                $additional = [];
                if (!empty($row['additional_data'])) {
                    $decoded = json_decode($row['additional_data'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $additional = $decoded;
                    } else {
                        log_message('debug', "⚠️ Failed to decode JSON for log ID {$row['id']}: " . json_last_error_msg());
                    }
                }

                $history[] = [
                    'id' => $row['id'],
                    'action_type' => $row['action_type'],
                    'action_description' => $row['action_description'],
                    'created_at' => $row['created_at'],
                    'status' => $row['status'],
                    'ip_address' => $row['ip_address'],
                    'folder_id' => $row['folder_id'],
                    'by_user_name' => trim($row['by_user_name']) ?: 'ระบบ',
                    'by_user_email' => $row['by_user_email'],

                    'details' => [
                        'permission_id' => $additional['permission_id'] ?? null,
                        'target_member_id' => $additional['target_member_id'] ?? null,
                        'target_member_name' => $additional['target_member_name'] ?? null,
                        'folder_name' => $additional['folder_name'] ?? null,
                        'access_type' => $additional['access_type'] ?? null,
                        'old_access_type' => $additional['old_access_type'] ?? null,
                        'permission_source' => $additional['permission_source'] ?? null,
                        'granted_by' => $additional['granted_by'] ?? null,
                        'granted_at' => $additional['granted_at'] ?? null
                    ]
                ];

                if ($index < 3) {
                    log_message('debug', "  Record #{$index}: {$row['action_type']} - {$row['action_description']}");
                }
            }

            log_message('info', "========================================");
            log_message('info', "✅ get_folder_permission_history completed");
            log_message('info', "  Returned: {$count} records");
            log_message('info', "========================================");

            return $history;

        } catch (Exception $e) {
            log_message('error', "========================================");
            log_message('error', "❌ get_folder_permission_history ERROR");
            log_message('error', "========================================");
            log_message('error', "  Folder ID: {$folder_id}");
            log_message('error', "  Message: {$e->getMessage()}");
            log_message('error', "  File: {$e->getFile()}");
            log_message('error', "  Line: {$e->getLine()}");
            log_message('debug', "  Stack trace:");
            log_message('debug', $e->getTraceAsString());
            log_message('error', "========================================");

            return [];
        }
    }




    /**
     * ดึงข้อมูล Logs ของสมาชิก
     */
    public function get_member_logs($member_id, $limit = 50)
    {
        log_message('info', 'Google_drive_model: get_member_logs() called for member_id: ' . $member_id . ', limit: ' . $limit);

        $result = $this->db->select('*')
            ->from($this->tbl_google_drive_logs)
            ->where('member_id', $member_id)
            ->order_by('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->result();

        log_message('info', 'Google_drive_model: get_member_logs() found ' . count($result) . ' logs');

        return $result;
    }

    /**
     * ดึงข้อมูลสมาชิกที่เชื่อมต่อ Google Drive
     */
    public function get_connected_members($search = '', $limit = 10, $offset = 0)
    {
        log_message('info', 'Google_drive_model: get_connected_members() called with search: "' . $search . '", limit: ' . $limit . ', offset: ' . $offset);

        $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.google_email, 
                          m.google_connected_at, m.google_account_verified, p.pname,
                          COUNT(gdf.id) as total_folders')
            ->from($this->tbl_member . ' m')
            ->join($this->tbl_position . ' p', 'm.ref_pid = p.pid', 'left')
            ->join($this->tbl_google_drive_folders . ' gdf', 'm.m_id = gdf.member_id AND gdf.is_active = 1', 'left')
            ->where('m.google_drive_enabled', 1);

        if (!empty($search)) {
            log_message('info', 'Google_drive_model: get_connected_members() - Applying search filter');
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

        $result = $this->db->get()->result();

        log_message('info', 'Google_drive_model: get_connected_members() found ' . count($result) . ' members');

        return $result;
    }

    /**
     * นับจำนวนสมาชิกที่เชื่อมต่อ Google Drive
     */
    public function count_connected_members($search = '')
    {
        log_message('info', 'Google_drive_model: count_connected_members() called with search: "' . $search . '"');

        $this->db->from($this->tbl_member . ' m')
            ->where('m.google_drive_enabled', 1);

        if (!empty($search)) {
            log_message('info', 'Google_drive_model: count_connected_members() - Applying search filter');
            $this->db->group_start()
                ->like('m.m_fname', $search)
                ->or_like('m.m_lname', $search)
                ->or_like('m.m_email', $search)
                ->or_like('m.google_email', $search)
                ->group_end();
        }

        $count = $this->db->count_all_results();

        log_message('info', 'Google_drive_model: count_connected_members() result: ' . $count);

        return $count;
    }

    /**
     * ดึงการตั้งค่า Google Drive
     */
    public function get_setting($key)
    {
        log_message('info', 'Google_drive_model: get_setting() called for key: ' . $key);

        $result = $this->db->select('setting_value')
            ->from($this->tbl_google_drive_settings)
            ->where('setting_key', $key)
            ->where('is_active', 1)
            ->get()
            ->row();

        $value = $result ? $result->setting_value : null;

        log_message('info', 'Google_drive_model: get_setting() result: ' . ($value !== null ? 'found' : 'not found'));

        return $value;
    }

    /**
     * อัปเดตการตั้งค่า Google Drive
     */
    public function update_setting($key, $value)
    {
        log_message('info', 'Google_drive_model: update_setting() called for key: ' . $key);

        $existing = $this->db->where('setting_key', $key)
            ->get($this->tbl_google_drive_settings)
            ->row();

        if ($existing) {
            log_message('info', 'Google_drive_model: update_setting() - Updating existing setting');
            $this->db->where('setting_key', $key);
            $result = $this->db->update($this->tbl_google_drive_settings, ['setting_value' => $value]);
        } else {
            log_message('info', 'Google_drive_model: update_setting() - Inserting new setting');
            $result = $this->db->insert($this->tbl_google_drive_settings, [
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }

        log_message('info', 'Google_drive_model: update_setting() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * ดึงข้อมูลสถิติ Google Drive
     */
    public function get_drive_statistics()
    {
        log_message('info', 'Google_drive_model: get_drive_statistics() called');

        // จำนวนสมาชิกที่เชื่อมต่อ
        $connected_members = $this->db->where('google_drive_enabled', 1)
            ->count_all_results($this->tbl_member);

        // จำนวน Folders ทั้งหมด
        $total_folders = $this->db->where('is_active', 1)
            ->count_all_results($this->tbl_google_drive_folders);

        // จำนวนไฟล์ที่ Sync
        $synced_files = $this->db->where('sync_status', 'synced')
            ->count_all_results($this->tbl_google_drive_sync);

        // การเชื่อมต่อใหม่ในเดือนนี้
        $new_connections = $this->db->where('google_connected_at >=', date('Y-m-01'))
            ->where('google_drive_enabled', 1)
            ->count_all_results($this->tbl_member);

        $stats = [
            'connected_members' => $connected_members,
            'total_folders' => $total_folders,
            'synced_files' => $synced_files,
            'new_connections' => $new_connections
        ];

        log_message('info', 'Google_drive_model: get_drive_statistics() result: ' . json_encode($stats));

        return $stats;
    }

    /**
     * ล้างข้อมูล Member ที่ถูกลบ
     */
    public function cleanup_deleted_member_data($member_id)
    {
        log_message('info', 'Google_drive_model: cleanup_deleted_member_data() called for member_id: ' . $member_id);

        $this->db->trans_start();

        // ลบ folders
        $this->db->where('member_id', $member_id)->delete($this->tbl_google_drive_folders);
        log_message('info', 'Google_drive_model: cleanup_deleted_member_data() - Deleted folders');

        // ลบ permissions
        $this->db->where('member_id', $member_id)->delete($this->tbl_google_drive_permissions);
        log_message('info', 'Google_drive_model: cleanup_deleted_member_data() - Deleted permissions');

        // ลบ sync files
        $this->db->where('member_id', $member_id)->delete($this->tbl_google_drive_sync);
        log_message('info', 'Google_drive_model: cleanup_deleted_member_data() - Deleted sync files');

        // เก็บ logs ไว้เพื่อ audit (ไม่ลบ)

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        log_message('info', 'Google_drive_model: cleanup_deleted_member_data() result: ' . ($status ? 'success' : 'failed'));

        return $status;
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึง Google Drive ตามตำแหน่ง
     */
    public function check_drive_permission($member_id)
    {
        log_message('info', 'Google_drive_model: check_drive_permission() called for member_id: ' . $member_id);

        $member = $this->db->select('m.ref_pid, m.m_id')
            ->from($this->tbl_member . ' m')
            ->where('m.m_id', $member_id)
            ->get()
            ->row();

        if (!$member) {
            log_message('error', 'Google_drive_model: check_drive_permission() - Member not found');
            return ['allowed' => false, 'reason' => 'ไม่พบข้อมูลสมาชิก'];
        }

        $position_id = $member->ref_pid;
        log_message('info', 'Google_drive_model: check_drive_permission() - position_id: ' . $position_id);

        // ID 1 และ 2 = สิทธิ์เต็ม
        if (in_array($position_id, [1, 2])) {
            log_message('info', 'Google_drive_model: check_drive_permission() - Full access granted (position 1 or 2)');
            return ['allowed' => true, 'access_type' => 'full', 'position_id' => $position_id];
        }

        // ID 3 = ตรวจสอบสิทธิ์ในโมดูล Google Drive (module_id = 2)
        if ($position_id == 3) {
            log_message('info', 'Google_drive_model: check_drive_permission() - Checking module permissions for position 3');

            $permission = $this->db->select('mup.*')
                ->from($this->tbl_member_user_permissions . ' mup')
                ->join($this->tbl_member_module_menus . ' mmm', 'mup.system_id = mmm.id')
                ->where('mup.member_id', $member_id)
                ->where('mmm.module_id', 2)
                ->where('mup.is_active', 1)
                ->get()
                ->row();

            if ($permission) {
                log_message('info', 'Google_drive_model: check_drive_permission() - Full access granted (position 3 with module permission)');
                return ['allowed' => true, 'access_type' => 'full', 'position_id' => $position_id];
            } else {
                log_message('info', 'Google_drive_model: check_drive_permission() - Access denied (position 3 without module permission)');
                return ['allowed' => false, 'reason' => 'ไม่มีสิทธิ์เข้าใช้งาน Google Drive'];
            }
        }

        // ID 4 ขึ้นไป = สิทธิ์เฉพาะ folder ของตำแหน่งตัวเอง
        if ($position_id >= 4) {
            log_message('info', 'Google_drive_model: check_drive_permission() - Position-only access granted (position >= 4)');
            return ['allowed' => true, 'access_type' => 'position_only', 'position_id' => $position_id];
        }

        log_message('info', 'Google_drive_model: check_drive_permission() - Access denied (no matching permission)');
        return ['allowed' => false, 'reason' => 'ตำแหน่งไม่มีสิทธิ์เข้าใช้งาน'];
    }


    /**
     * ดึงข้อมูลผู้ใช้งานพร้อมสถานะ Storage
     */
    public function get_user_permission_info($member_id)
    {
        log_message('info', 'Google_drive_model: get_user_permission_info() called for member_id: ' . $member_id);

        $result = $this->db->select('
        m.m_id,
        m.m_fname,
        m.m_lname,
        m.m_email,
        m.m_phone,
        m.google_email,
        m.google_drive_enabled,
        m.storage_access_granted,
        m.personal_folder_id,
        m.storage_quota_limit,
        m.storage_quota_used,
        m.last_storage_access,
        p.pname as position_name,
        p.pid as position_id,
        IF(m.google_email IS NOT NULL, 1, 0) as google_account_verified,
        m.m_datesave as google_connected_at
    ')
            ->from($this->tbl_member . ' m')
            ->join($this->tbl_position . ' p', 'm.ref_pid = p.pid', 'left')
            ->where('m.m_id', $member_id)
            ->get()
            ->row();

        log_message('info', 'Google_drive_model: get_user_permission_info() result: ' . ($result ? 'found' : 'not found'));

        return $result;
    }

    /**
     * ดึงข้อมูลโฟลเดอร์พร้อมสิทธิ์ของผู้ใช้
     * รองรับทั้ง tbl_google_drive_folders และ tbl_google_drive_system_folders
     */
    /**
     * ✅ FIXED: get_user_folders_with_permissions with Deduplication
     * 
     * แก้ไข:
     * 1. เพิ่ม deduplication logic
     * 2. ให้ system_folders มี priority
     * 3. เพิ่ม INFO logging (ไม่ใช้ WARNING)
     */
    public function get_user_folders_with_permissions($member_id)
    {
        log_message('info', '=== get_user_folders_with_permissions: Start ===');
        log_message('info', 'Member ID: ' . $member_id);

        // ✅ Query 1: ดึงจาก tbl_google_drive_folders
        $this->db->select('
        gdf.id as folder_db_id,
        gdf.folder_id,
        gdf.folder_name,
        gdf.folder_type,
        gdf.folder_url,
        gdf.is_system_folder,
        gdmfa.access_type as permission_type,
        gdmfa.id as permission_id,
        gdmfa.granted_at as permission_granted_at,
        gdmfa.granted_by,
        m.m_fname as granted_by_fname,
        m.m_lname as granted_by_lname,
        "user_folders" as source_table
    ', false)
            ->from($this->tbl_google_drive_member_folder_access . ' gdmfa')
            ->join(
                $this->tbl_google_drive_folders . ' gdf',
                'gdmfa.folder_id = gdf.folder_id',
                'inner'
            )
            ->join($this->tbl_member . ' m', 'gdmfa.granted_by = m.m_id', 'left')
            ->where('gdmfa.member_id', $member_id)
            ->where('gdmfa.is_active', 1)
            ->where('gdf.is_active', 1);

        $query1 = $this->db->get_compiled_select();
        log_message('info', '📋 Query 1 (user_folders): ' . $query1);

        // ✅ Query 2: ดึงจาก tbl_google_drive_system_folders
        $this->db->select('
        gsf.id as folder_db_id,
        gsf.folder_id,
        gsf.folder_name,
        gsf.folder_type,
        "" as folder_url,
        1 as is_system_folder,
        gdmfa.access_type as permission_type,
        gdmfa.id as permission_id,
        gdmfa.granted_at as permission_granted_at,
        gdmfa.granted_by,
        m.m_fname as granted_by_fname,
        m.m_lname as granted_by_lname,
        "system_folders" as source_table
    ', false)
            ->from($this->tbl_google_drive_member_folder_access . ' gdmfa')
            ->join(
                'tbl_google_drive_system_folders gsf',
                '(gdmfa.folder_id = gsf.folder_id OR gdmfa.folder_id = CAST(gsf.id AS CHAR))',
                'inner'
            )
            ->join($this->tbl_member . ' m', 'gdmfa.granted_by = m.m_id', 'left')
            ->where('gdmfa.member_id', $member_id)
            ->where('gdmfa.is_active', 1)
            ->where('gsf.is_active', 1);

        $query2 = $this->db->get_compiled_select();
        log_message('info', '📋 Query 2 (system_folders): ' . $query2);

        // ✅ Execute queries separately
        $user_folders_result = $this->db->query($query1)->result();
        $system_folders_result = $this->db->query($query2)->result();

        log_message('info', '📊 Query 1 returned: ' . count($user_folders_result) . ' rows');
        log_message('info', '📊 Query 2 returned: ' . count($system_folders_result) . ' rows');

        // ✅ DEDUPLICATION: ให้ system_folders มี priority
        $folders_map = [];
        $duplicate_count = 0;

        // เพิ่ม system_folders ก่อน (priority สูง)
        foreach ($system_folders_result as $folder) {
            $folders_map[$folder->folder_id] = $folder;
            log_message('info', sprintf(
                '  ✅ Added from system_folders: %s [%s] (folder_id: %s)',
                $folder->folder_name,
                $folder->folder_type,
                substr($folder->folder_id, 0, 15) . '...'
            ));
        }

        // เพิ่ม user_folders (เฉพาะที่ยังไม่มี)
        foreach ($user_folders_result as $folder) {
            if (!isset($folders_map[$folder->folder_id])) {
                $folders_map[$folder->folder_id] = $folder;
                log_message('info', sprintf(
                    '  ✅ Added from user_folders: %s [%s] (folder_id: %s)',
                    $folder->folder_name,
                    $folder->folder_type,
                    substr($folder->folder_id, 0, 15) . '...'
                ));
            } else {
                $duplicate_count++;
                log_message('info', sprintf(
                    '  ⏭️ Skipped duplicate: %s [%s] (folder_id: %s) - already exists in system_folders',
                    $folder->folder_name,
                    $folder->folder_type,
                    substr($folder->folder_id, 0, 15) . '...'
                ));
            }
        }

        // Convert กลับเป็น indexed array
        $unique_folders = array_values($folders_map);

        // ✅ Sorting
        usort($unique_folders, function ($a, $b) {
            // Sort by is_system_folder DESC
            if ($a->is_system_folder != $b->is_system_folder) {
                return $b->is_system_folder - $a->is_system_folder;
            }

            // Sort by folder_type priority
            $type_priority = [
                'admin' => 1,
                'system' => 2,
                'department' => 3,
                'shared' => 4,
                'user' => 5,
                'personal' => 6
            ];

            $a_priority = $type_priority[$a->folder_type] ?? 99;
            $b_priority = $type_priority[$b->folder_type] ?? 99;

            if ($a_priority != $b_priority) {
                return $a_priority - $b_priority;
            }

            // Sort by folder_name ASC
            return strcmp($a->folder_name, $b->folder_name);
        });

        log_message('info', '✅ Deduplication complete:');
        log_message('info', '   - Total from queries: ' . (count($user_folders_result) + count($system_folders_result)));
        log_message('info', '   - Duplicates removed: ' . $duplicate_count);
        log_message('info', '   - Unique folders: ' . count($unique_folders));

        // ✅ Summary log
        log_message('info', '📁 Final folder list:');
        foreach ($unique_folders as $idx => $folder) {
            log_message('info', sprintf(
                '   %d. %s (%s) [%s] - source: %s',
                $idx + 1,
                $folder->folder_name,
                $folder->permission_type,
                $folder->folder_type,
                $folder->source_table
            ));
        }

        log_message('info', '=== get_user_folders_with_permissions: End ===');

        return $unique_folders;
    }



    /**
     * ดึงข้อมูลการใช้งาน Storage
     */
    public function get_user_storage_info($member_id)
    {
        log_message('info', 'Google_drive_model: get_user_storage_info() called for member_id: ' . $member_id);

        // TODO: เชื่อมต่อ Google Drive API เพื่อดึงข้อมูลจริง
        // ตัวอย่างเบื้องต้น: ดึงจากฐานข้อมูลหรือ cache

        $storage_data = $this->db->select('storage_quota_used, storage_quota_limit')
            ->from($this->tbl_member)
            ->where('m_id', $member_id)
            ->get()
            ->row();

        $used = $storage_data ? ($storage_data->storage_used ?? 0) : 0;
        $total = $storage_data ? ($storage_data->storage_quota ?? 1073741824) : 1073741824; // 1 GB default

        $percentage = $total > 0 ? round(($used / $total) * 100, 2) : 0;

        $info = [
            'used' => $used,
            'total' => $total,
            'percentage' => $percentage,
            'used_formatted' => $this->format_bytes($used),
            'total_formatted' => $this->format_bytes($total)
        ];

        log_message('info', 'Google_drive_model: get_user_storage_info() result: used=' . $used . ', total=' . $total . ', percentage=' . $percentage . '%');

        return $info;
    }

    /**
     * ดึงสถิติการใช้งานของผู้ใช้
     * รองรับทั้ง tbl_google_drive_folders และ tbl_google_drive_system_folders
     */
    /**
     * ✅ FIXED: get_user_statistics
     * 
     * แก้ไข JOIN condition ให้รองรับทั้ง 2 กรณี
     */
    public function get_user_statistics($member_id)
    {
        log_message('info', '=== get_user_statistics: Start ===');
        log_message('info', 'Member ID: ' . $member_id);

        // ✅ จำนวนโฟลเดอร์ทั้งหมดที่มีสิทธิ์เข้าถึง
        $folders_count = $this->db->where('member_id', $member_id)
            ->where('is_active', 1)
            ->count_all_results($this->tbl_google_drive_member_folder_access);

        // ✅ โฟลเดอร์ที่แชร์ (shared) - จาก user_folders
        $shared_count_user = $this->db->select('COUNT(*) as count')
            ->from($this->tbl_google_drive_member_folder_access . ' gdmfa')
            ->join(
                $this->tbl_google_drive_folders . ' gdf',
                'gdmfa.folder_id = gdf.folder_id',
                'inner'
            )
            ->where('gdmfa.member_id', $member_id)
            ->where('gdmfa.is_active', 1)
            ->where('gdf.folder_type', 'shared')
            ->where('gdf.is_active', 1)
            ->get()
            ->row()
            ->count;

        // ✅ โฟลเดอร์ที่แชร์ (shared) - จาก system_folders
        $shared_count_system = $this->db->select('COUNT(*) as count')
            ->from($this->tbl_google_drive_member_folder_access . ' gdmfa')
            ->join(
                'tbl_google_drive_system_folders gsf',
                '(gdmfa.folder_id = gsf.folder_id OR gdmfa.folder_id = CAST(gsf.id AS CHAR))',  // ✅ รองรับทั้ง 2 กรณี
                'inner'
            )
            ->where('gdmfa.member_id', $member_id)
            ->where('gdmfa.is_active', 1)
            ->where('gsf.folder_type', 'shared')
            ->where('gsf.is_active', 1)
            ->get()
            ->row()
            ->count;

        $shared_count = $shared_count_user + $shared_count_system;

        // ✅ โฟลเดอร์ที่เป็นเจ้าของ (owner)
        $owned_count = $this->db->where('member_id', $member_id)
            ->where('is_active', 1)
            ->where('access_type', 'owner')
            ->count_all_results($this->tbl_google_drive_member_folder_access);

        // ✅ โฟลเดอร์ที่มีสิทธิ์เขียน (write/admin)
        $write_count = $this->db->where('member_id', $member_id)
            ->where('is_active', 1)
            ->where_in('access_type', ['write', 'admin', 'owner'])
            ->count_all_results($this->tbl_google_drive_member_folder_access);

        // ✅ จำนวนไฟล์ที่มี
        $file_count = $this->db->where('member_id', $member_id)
            ->where('sync_status', 'synced')
            ->count_all_results($this->tbl_google_drive_sync);

        $stats = [
            'total_folders' => $folders_count,
            'shared_folders' => $shared_count,
            'owned_folders' => $owned_count,
            'write_folders' => $write_count,
            'file_count' => $file_count
        ];

        log_message('info', '✅ Statistics: ' . json_encode($stats));
        log_message('info', '=== get_user_statistics: End ===');

        return $stats;
    }

    /**
     * ✅ NEW: normalize_folder_id
     * 
     * แปลง DB ID → Google Drive ID (ถ้าจำเป็น)
     * ใช้สำหรับป้องกันปัญหาในอนาคต
     */
    private function normalize_folder_id($folder_id)
    {
        log_message('info', '🔄 normalize_folder_id: Input = ' . $folder_id);

        // ✅ ถ้าเป็น Google Drive ID อยู่แล้ว (ยาว + มีตัวอักษร)
        if (strlen($folder_id) >= 20 && preg_match('/[a-zA-Z_-]/', $folder_id)) {
            log_message('info', '✅ Already Google Drive ID');
            return $folder_id;
        }

        // ✅ ถ้าเป็นตัวเลขสั้นๆ → น่าจะเป็น DB ID
        if (is_numeric($folder_id) && strlen($folder_id) < 10) {
            log_message('info', '⚠️ Looks like DB ID, converting...');

            // ลองหาใน system_folders ก่อน
            $system_folder = $this->db->select('folder_id, folder_name')
                ->from('tbl_google_drive_system_folders')
                ->where('id', $folder_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($system_folder) {
                log_message('info', "✅ Found in system_folders: {$system_folder->folder_id} ({$system_folder->folder_name})");
                return $system_folder->folder_id;
            }

            // ถ้าไม่เจอ ลองหาใน user_folders
            $user_folder = $this->db->select('folder_id, folder_name')
                ->from($this->tbl_google_drive_folders)
                ->where('id', $folder_id)
                ->where('is_active', 1)
                ->get()
                ->row();

            if ($user_folder) {
                log_message('info', "✅ Found in user_folders: {$user_folder->folder_id} ({$user_folder->folder_name})");
                return $user_folder->folder_id;
            }

            log_message('error', "❌ Cannot normalize folder_id: {$folder_id}");
            return null;
        }

        log_message('info', '⚠️ Unknown folder_id format, returning as-is');
        return $folder_id;
    }




    /**
     * ดึงรายการโฟลเดอร์ระบบที่พร้อมใช้งาน
     */
    public function get_available_system_folders()
    {
        log_message('info', 'Google_drive_model: get_available_system_folders() called');

        $result = $this->db->select('id, folder_id, folder_name, folder_type, folder_url')
            ->from($this->tbl_google_drive_folders)
            ->where('is_system_folder', 1)
            ->where('is_active', 1)
            ->order_by('folder_type', 'ASC')
            ->order_by('folder_name', 'ASC')
            ->get()
            ->result();

        log_message('info', 'Google_drive_model: get_available_system_folders() found ' . count($result) . ' system folders');

        return $result;
    }

    /**
     * ฟังก์ชันช่วยแปลง bytes เป็น human-readable format
     */
    private function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * เปลี่ยนระดับสิทธิ์ (อัปเดตจากเดิม)
     */
    public function update_permission_level($permission_id, $new_permission_type)
    {
        log_message('info', 'Google_drive_model: update_permission_level() called for permission_id: ' . $permission_id . ', new_type: ' . $new_permission_type);

        $result = $this->db->where('id', $permission_id)
            ->update($this->tbl_google_drive_member_folder_access, [
                'access_type' => $new_permission_type,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('m_id')
            ]);

        log_message('info', 'Google_drive_model: update_permission_level() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * เพิกถอนสิทธิ์การเข้าถึงโฟลเดอร์ (อัปเดตจากเดิม)
     */
    public function revoke_folder_access($permission_id, $revoked_by)
    {
        log_message('info', 'Google_drive_model: revoke_folder_access() called for permission_id: ' . $permission_id . ', revoked_by: ' . $revoked_by);

        $result = $this->db->where('id', $permission_id)
            ->update($this->tbl_google_drive_member_folder_access, [
                'is_active' => 0,
                'revoked_by' => $revoked_by,
                'revoked_at' => date('Y-m-d H:i:s')
            ]);

        log_message('info', 'Google_drive_model: revoke_folder_access() result: ' . ($result ? 'success' : 'failed'));

        return $result;
    }

    /**
     * ✅ FIXED: grant_folder_access
     * 
     * ใช้ normalize_folder_id ก่อนบันทึก
     */
    public function grant_folder_access($member_id, $folder_id, $permission_type, $granted_by)
    {
        log_message('info', '=== grant_folder_access: Start ===');
        log_message('info', "Member: {$member_id}, Folder: {$folder_id}, Type: {$permission_type}");

        // ✅ แปลง folder_id ให้เป็น Google Drive ID
        $normalized_folder_id = $this->normalize_folder_id($folder_id);

        if (!$normalized_folder_id) {
            log_message('error', '❌ Invalid folder_id');
            return false;
        }

        log_message('info', "✅ Normalized folder_id: {$normalized_folder_id}");

        // ตรวจสอบว่ามีสิทธิ์อยู่แล้วหรือไม่
        $existing = $this->db->where('member_id', $member_id)
            ->where('folder_id', $normalized_folder_id)
            ->where('is_active', 1)
            ->get($this->tbl_google_drive_member_folder_access)
            ->row();

        if ($existing) {
            log_message('info', '📝 Updating existing permission ID: ' . $existing->id);

            $result = $this->db->where('id', $existing->id)
                ->update($this->tbl_google_drive_member_folder_access, [
                    'access_type' => $permission_type,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $granted_by
                ]);
        } else {
            log_message('info', '➕ Inserting new permission');

            $data = [
                'member_id' => $member_id,
                'folder_id' => $normalized_folder_id,  // ✅ ใช้ Google Drive ID เสมอ
                'access_type' => $permission_type,
                'granted_by' => $granted_by,
                'granted_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ];

            $result = $this->db->insert($this->tbl_google_drive_member_folder_access, $data);
        }

        log_message('info', '=== grant_folder_access: ' . ($result ? 'Success' : 'Failed') . ' ===');

        return $result;
    }

    /**
     * ============================================
     * HELPER FUNCTIONS สำหรับจัดการ Personal Folder Permissions
     * ============================================
     */

    /**
     * ✅ ตรวจสอบว่า User มีสิทธิ์จัดการ Personal Folder หรือไม่
     * 
     * เงื่อนไขที่อนุญาต:
     * 1. เป็น Owner ของโฟลเดอร์
     * 2. เป็น System Admin (m_system = 'system_admin' OR ref_pid = 1)
     * 
     * @param object $folder - ข้อมูลโฟลเดอร์ (ต้องมี folder_type, member_id, folder_name, folder_id)
     * @param int $current_user_id - User ID ที่กำลังดำเนินการ
     * @return array - ['allowed' => bool, 'reason' => string, 'user_role' => string|null]
     */
    public function check_personal_folder_permission($folder, $current_user_id)
    {
        // ========================================
        // 1. ตรวจสอบว่าเป็น Personal Folder หรือไม่
        // ========================================
        if ($folder->folder_type !== 'personal') {
            log_message('info', "📁 Not a personal folder ({$folder->folder_type}) - Permission granted");
            return [
                'allowed' => true,
                'reason' => 'Not a personal folder',
                'user_role' => null
            ];
        }

        log_message('info', "========================================");
        log_message('info', "🔒 Personal Folder Permission Check");
        log_message('info', "========================================");
        log_message('info', "  Folder: {$folder->folder_name}");
        log_message('info', "  Folder ID: {$folder->folder_id}");
        log_message('info', "  Owner: {$folder->member_id}");
        log_message('info', "  Current User: {$current_user_id}");

        // ========================================
        // 2. เงื่อนไขที่ 1: ตรวจสอบว่าเป็น Owner หรือไม่
        // ========================================
        if ($folder->member_id == $current_user_id) {
            log_message('info', "  ✅ Rule 1: User is the OWNER - Permission granted");
            log_message('info', "========================================");
            return [
                'allowed' => true,
                'reason' => 'User is the owner',
                'user_role' => 'owner'
            ];
        }

        log_message('info', "  ⚠️ User is NOT the owner (Owner: {$folder->member_id}, Current: {$current_user_id})");

        // ========================================
        // 3. เงื่อนไขที่ 2: ตรวจสอบว่าเป็น System Admin หรือไม่
        // ========================================
        log_message('info', "  🔍 Checking System Admin privileges...");

        // ดึงข้อมูล User จาก tbl_member
        $user_info = $this->db->select('m_id, m_fname, m_lname, m_system, ref_pid')
            ->from($this->tbl_member)
            ->where('m_id', $current_user_id)
            ->where('m_status', 1)
            ->get()
            ->row();

        if (!$user_info) {
            log_message('error', "  ❌ User not found in database (m_id: {$current_user_id})");
            log_message('info', "========================================");
            return [
                'allowed' => false,
                'reason' => 'User not found',
                'user_role' => null
            ];
        }

        log_message('info', "  User Info:");
        log_message('info', "    Name: {$user_info->m_fname} {$user_info->m_lname}");
        log_message('info', "    m_system: " . ($user_info->m_system ?? 'NULL'));
        log_message('info', "    ref_pid: {$user_info->ref_pid}");

        // 3.1. ตรวจสอบ m_system = 'system_admin'
        if ($user_info->m_system === 'system_admin') {
            log_message('info', "  ========================================");
            log_message('info', "  ✅ Rule 2a: User has m_system = 'system_admin'");
            log_message('info', "  ✅ Permission granted (System Admin by Role)");
            log_message('info', "  ========================================");

            // Log activity สำหรับ audit
            $this->log_personal_folder_admin_access(
                $current_user_id,
                $folder->member_id,
                $folder->folder_id,
                $folder->folder_name,
                'system_admin_by_role'
            );

            return [
                'allowed' => true,
                'reason' => 'System Admin (by role)',
                'user_role' => 'system_admin'
            ];
        }

        // 3.2. ตรวจสอบ ref_pid = 1 (System Admin position)
        if ($user_info->ref_pid == 1) {
            log_message('info', "  ========================================");
            log_message('info', "  ✅ Rule 2b: User has ref_pid = 1 (System Admin position)");
            log_message('info', "  ✅ Permission granted (System Admin by Position)");
            log_message('info', "  ========================================");

            // Log activity สำหรับ audit
            $this->log_personal_folder_admin_access(
                $current_user_id,
                $folder->member_id,
                $folder->folder_id,
                $folder->folder_name,
                'system_admin_by_position'
            );

            return [
                'allowed' => true,
                'reason' => 'System Admin (by position)',
                'user_role' => 'system_admin'
            ];
        }

        // ========================================
        // 4. ไม่ผ่านทั้งสองเงื่อนไข → ปฏิเสธ
        // ========================================
        log_message('error', "========================================");
        log_message('error', "❌ ACCESS DENIED - Personal Folder Protection");
        log_message('error', "========================================");
        log_message('error', "  Reason: Not owner AND not System Admin");
        log_message('error', "  User {$current_user_id} ({$user_info->m_fname} {$user_info->m_lname})");
        log_message('error', "    - m_system: " . ($user_info->m_system ?? 'NULL'));
        log_message('error', "    - ref_pid: {$user_info->ref_pid}");
        log_message('error', "  Cannot manage personal folder of User {$folder->member_id}");
        log_message('error', "========================================");

        return [
            'allowed' => false,
            'reason' => 'Not owner and not System Admin',
            'user_role' => $user_info->m_system ?? 'unknown'
        ];
    }

    /**
     * ✅ Log การเข้าถึง Personal Folder โดย System Admin (สำหรับ Audit Trail)
     * 
     * @param int $admin_id - User ID ของ Admin ที่เข้าถึง
     * @param int $owner_id - User ID ของ Owner
     * @param string $folder_id - Google Drive Folder ID
     * @param string $folder_name - ชื่อโฟลเดอร์
     * @param string $access_type - ประเภทการเข้าถึง (system_admin_by_role, system_admin_by_position)
     * @return bool - สำเร็จหรือไม่
     */
    public function log_personal_folder_admin_access($admin_id, $owner_id, $folder_id, $folder_name, $access_type)
    {
        try {
            // ดึงข้อมูล Admin และ Owner
            $admin_info = $this->db->select('m_fname, m_lname, m_email')
                ->from($this->tbl_member)
                ->where('m_id', $admin_id)
                ->get()
                ->row();

            $owner_info = $this->db->select('m_fname, m_lname, m_email')
                ->from($this->tbl_member)
                ->where('m_id', $owner_id)
                ->get()
                ->row();

            // สร้าง log data
            $log_data = [
                'action_type' => 'personal_folder_admin_access',
                'admin_id' => $admin_id,
                'admin_name' => $admin_info ? "{$admin_info->m_fname} {$admin_info->m_lname}" : 'Unknown',
                'owner_id' => $owner_id,
                'owner_name' => $owner_info ? "{$owner_info->m_fname} {$owner_info->m_lname}" : 'Unknown',
                'folder_id' => $folder_id,
                'folder_name' => $folder_name,
                'access_type' => $access_type,
                'action' => 'manage_permission',
                'ip_address' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown',
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // ✅ บันทึกลง tbl_google_drive_logs (ถ้ามี)
            if ($this->db->table_exists($this->tbl_google_drive_logs)) {
                $this->db->insert($this->tbl_google_drive_logs, [
                    'member_id' => $admin_id,
                    'action' => 'personal_folder_admin_access',
                    'target_id' => $owner_id,
                    'folder_id' => $folder_id,
                    'details' => json_encode($log_data, JSON_UNESCAPED_UNICODE),
                    'ip_address' => $log_data['ip_address'],
                    'created_at' => $log_data['created_at']
                ]);
            }

            // ✅ บันทึกลง log file
            log_message('info', "========================================");
            log_message('info', "📝 AUDIT LOG - System Admin Access");
            log_message('info', "========================================");
            log_message('info', "  Timestamp: {$log_data['created_at']}");
            log_message('info', "  Admin: {$log_data['admin_name']} (ID: {$admin_id})");
            log_message('info', "  Owner: {$log_data['owner_name']} (ID: {$owner_id})");
            log_message('info', "  Folder: {$folder_name}");
            log_message('info', "  Folder ID: {$folder_id}");
            log_message('info', "  Access Type: {$access_type}");
            log_message('info', "  Action: manage_permission");
            log_message('info', "  IP: {$log_data['ip_address']}");
            log_message('info', "========================================");

            return true;

        } catch (Exception $e) {
            log_message('error', "❌ Error logging admin access: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ ตรวจสอบว่า User เป็น System Admin หรือไม่
     * 
     * เงื่อนไข:
     * - m_system = 'system_admin' OR ref_pid = 1
     * 
     * @param int $user_id - User ID ที่ต้องการตรวจสอบ
     * @return array - ['is_admin' => bool, 'admin_type' => string|null]
     */
    public function is_system_admin($user_id)
    {
        $user_info = $this->db->select('m_system, ref_pid')
            ->from($this->tbl_member)
            ->where('m_id', $user_id)
            ->where('m_status', 1)
            ->get()
            ->row();

        if (!$user_info) {
            return [
                'is_admin' => false,
                'admin_type' => null
            ];
        }

        // ตรวจสอบ m_system
        if ($user_info->m_system === 'system_admin') {
            return [
                'is_admin' => true,
                'admin_type' => 'by_role'
            ];
        }

        // ตรวจสอบ ref_pid
        if ($user_info->ref_pid == 1) {
            return [
                'is_admin' => true,
                'admin_type' => 'by_position'
            ];
        }

        return [
            'is_admin' => false,
            'admin_type' => null
        ];
    }

    /**
     * ✅ ดึงข้อมูล User พร้อมสิทธิ์
     * 
     * @param int $user_id - User ID
     * @return object|null - ข้อมูล User พร้อม is_system_admin flag
     */
    public function get_user_with_permissions($user_id)
    {
        $user = $this->db->select('m_id, m_fname, m_lname, m_email, m_system, ref_pid')
            ->from($this->tbl_member)
            ->where('m_id', $user_id)
            ->where('m_status', 1)
            ->get()
            ->row();

        if (!$user) {
            return null;
        }

        // เพิ่ม flag is_system_admin
        $admin_check = $this->is_system_admin($user_id);
        $user->is_system_admin = $admin_check['is_admin'];
        $user->admin_type = $admin_check['admin_type'];

        return $user;
    }


    /**
     * ✅ บันทึกสิทธิ์ระบบของผู้ใช้ (FINAL - ใช้ log_permission_action)
     * 
     * อัปเดตสิทธิ์ระบบของผู้ใช้ทั้งใน tbl_member และ tbl_google_drive_member_permissions
     * 
     * @param int $user_id - User ID ที่ต้องการอัปเดต
     * @param array $permissions - ข้อมูลสิทธิ์ที่ต้องการบันทึก
     * @param int $updated_by - User ID ของผู้ที่ทำการอัปเดต
     * @return array ['success' => bool, 'message' => string]
     */
    public function save_user_system_permissions($user_id, $permissions, $updated_by)
    {
        log_message('info', '====================================================');
        log_message('info', '💾 MODEL: save_user_system_permissions started');
        log_message('info', '====================================================');
        log_message('info', 'Parameters:');
        log_message('info', '  - Target User ID: ' . $user_id);
        log_message('info', '  - Updated By: ' . $updated_by);
        log_message('info', '  - Permissions: ' . json_encode($permissions, JSON_UNESCAPED_UNICODE));
        log_message('info', '====================================================');

        try {
            // ========================================
            // เริ่ม Database Transaction
            // ========================================
            log_message('info', 'Starting database transaction...');
            $this->db->trans_start();

            // ========================================
            // Step 1: อัปเดต tbl_member
            // ========================================
            log_message('info', 'Step 1: Preparing tbl_member updates...');

            $member_updates = [];

            // 1.1. Storage Access
            if (isset($permissions['storage_access'])) {
                $member_updates['storage_access_granted'] = (int) $permissions['storage_access'];
                $member_updates['google_drive_enabled'] = (int) $permissions['storage_access'];
                log_message('info', '  - storage_access: ' . $permissions['storage_access'] . ' → storage_access_granted & google_drive_enabled');
            }

            // 1.2. Storage Quota Limit
            if (isset($permissions['storage_quota_limit'])) {
                $quota_value = (int) $permissions['storage_quota_limit'];
                $member_updates['storage_quota_limit'] = $quota_value;
                log_message('info', '  - storage_quota_limit: ' . $quota_value . ' bytes (' . $this->format_bytes_simple($quota_value) . ')');
            }

            // 1.3. ถ้ามีข้อมูลที่ต้องอัปเดต
            if (!empty($member_updates)) {
                log_message('info', 'Updating tbl_member...');
                log_message('debug', '  - Fields to update: ' . implode(', ', array_keys($member_updates)));
                log_message('debug', '  - Values: ' . json_encode($member_updates));

                $this->db->where('m_id', $user_id)
                    ->update($this->tbl_member, $member_updates);

                $affected_rows = $this->db->affected_rows();
                log_message('info', '✅ tbl_member updated - Affected rows: ' . $affected_rows);
            } else {
                log_message('info', 'ℹ️ No updates needed for tbl_member');
            }

            // ========================================
            // Step 2: อัปเดต tbl_google_drive_member_permissions
            // ========================================
            log_message('info', 'Step 2: Preparing tbl_google_drive_member_permissions updates...');

            $permission_updates = [];

            // 2.1. Permission Fields
            $permission_fields = [
                'can_create_folder' => 'สร้างโฟลเดอร์',
                'can_share' => 'แชร์ไฟล์',
                'can_delete' => 'ลบไฟล์',
                'override_position' => 'แทนที่สิทธิ์ตำแหน่ง'
            ];

            foreach ($permission_fields as $field => $description) {
                if (isset($permissions[$field])) {
                    $permission_updates[$field] = (int) $permissions[$field];
                    log_message('info', '  - ' . $field . ': ' . $permissions[$field] . ' (' . $description . ')');
                }
            }

            // 2.2. Notes
            if (isset($permissions['notes'])) {
                $permission_updates['notes'] = trim($permissions['notes']);
                log_message('info', '  - notes: ' . (empty($permission_updates['notes']) ? '(empty)' : substr($permission_updates['notes'], 0, 50)));
            }

            // 2.3. ถ้ามีข้อมูลที่ต้องอัปเดต
            if (!empty($permission_updates)) {
                log_message('info', 'Checking existing permission record...');

                // ตรวจสอบว่ามี record อยู่แล้วหรือไม่
                $existing = $this->db->where('member_id', $user_id)
                    ->get('tbl_google_drive_member_permissions')
                    ->row();

                log_message('info', 'Existing record: ' . ($existing ? 'Found (ID: ' . $existing->id . ')' : 'Not found'));

                // เพิ่มข้อมูล metadata
                $permission_updates['updated_by'] = $updated_by;
                $permission_updates['updated_at'] = date('Y-m-d H:i:s');

                if ($existing) {
                    // UPDATE record ที่มีอยู่
                    log_message('info', 'Updating existing permission record...');
                    log_message('debug', '  - Record ID: ' . $existing->id);
                    log_message('debug', '  - Fields to update: ' . implode(', ', array_keys($permission_updates)));

                    $this->db->where('member_id', $user_id)
                        ->update('tbl_google_drive_member_permissions', $permission_updates);

                    $affected_rows = $this->db->affected_rows();
                    log_message('info', '✅ Permission record updated - Affected rows: ' . $affected_rows);

                } else {
                    // INSERT record ใหม่
                    log_message('info', 'Creating new permission record...');

                    $permission_updates['member_id'] = $user_id;
                    $permission_updates['permission_type'] = 'custom';
                    $permission_updates['is_active'] = 1;
                    $permission_updates['created_by'] = $updated_by;
                    $permission_updates['created_at'] = date('Y-m-d H:i:s');

                    log_message('debug', '  - Fields to insert: ' . implode(', ', array_keys($permission_updates)));

                    $this->db->insert('tbl_google_drive_member_permissions', $permission_updates);

                    $insert_id = $this->db->insert_id();
                    log_message('info', '✅ Permission record created - Insert ID: ' . $insert_id);
                }
            } else {
                log_message('info', 'ℹ️ No updates needed for tbl_google_drive_member_permissions');
            }

            // ========================================
            // Step 3: บันทึก Activity Log (ใช้ log_permission_action)
            // ========================================
            log_message('info', 'Step 3: Logging activity using log_permission_action()...');

            // ดึงข้อมูล User
            $target_user = $this->db->select('m_fname, m_lname, m_email')
                ->from($this->tbl_member)
                ->where('m_id', $user_id)
                ->get()
                ->row();

            // เตรียมข้อมูลสำหรับ log
            $permission_data = [
                'member_id' => $user_id,
                'member_name' => $target_user ? $target_user->m_fname . ' ' . $target_user->m_lname : 'User ID ' . $user_id,
                'target_email' => $target_user ? $target_user->m_email : null,
                // รวมข้อมูล permissions ทั้งหมด
                'storage_access' => $permissions['storage_access'] ?? null,
                'can_create_folder' => $permissions['can_create_folder'] ?? null,
                'can_share' => $permissions['can_share'] ?? null,
                'can_delete' => $permissions['can_delete'] ?? null,
                'storage_quota_limit' => $permissions['storage_quota_limit'] ?? null,
                'override_position' => $permissions['override_position'] ?? null,
                'notes' => $permissions['notes'] ?? null
            ];

            // เรียกใช้ log_permission_action ที่มีอยู่แล้ว
            $log_result = $this->log_permission_action(
                'update_system_permissions',
                $permission_data,
                $updated_by
            );

            if ($log_result) {
                log_message('info', '✅ Activity logged successfully via log_permission_action()');
            } else {
                log_message('info', '⚠️ Activity log failed (non-critical, continuing)');
            }

            // ========================================
            // Commit Transaction
            // ========================================
            log_message('info', 'Committing database transaction...');
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', '❌ ERROR: Database transaction failed');
                throw new Exception('Database transaction failed');
            }

            log_message('info', '✅ Transaction committed successfully');
            log_message('info', '====================================================');
            log_message('info', '✅ MODEL: save_user_system_permissions completed');
            log_message('info', '====================================================');

            return [
                'success' => true,
                'message' => 'บันทึกสิทธิ์ระบบเรียบร้อยแล้ว'
            ];

        } catch (Exception $e) {
            log_message('error', '====================================================');
            log_message('error', '❌ MODEL ERROR: save_user_system_permissions failed');
            log_message('error', '====================================================');
            log_message('error', '  - Exception: ' . $e->getMessage());
            log_message('error', '  - File: ' . $e->getFile());
            log_message('error', '  - Line: ' . $e->getLine());
            log_message('error', '====================================================');

            // Rollback transaction
            $this->db->trans_rollback();
            log_message('info', '🔄 Transaction rolled back');

            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }


    /**
     * ✅ Helper: แปลง bytes เป็นรูปแบบที่อ่านง่าย (แบบง่าย)
     * 
     * @param int $bytes - จำนวน bytes
     * @return string - เช่น "1 GB", "512 MB"
     */
    private function format_bytes_simple($bytes)
    {
        if ($bytes == 0)
            return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = 1024;
        $exponent = floor(log($bytes) / log($base));
        $exponent = min($exponent, count($units) - 1);

        $value = $bytes / pow($base, $exponent);

        return round($value, 2) . ' ' . $units[$exponent];
    }


    // =====================================================
// Methods สำหรับ Settings Tab
// เพิ่มใน Google_drive_model.php
// ตำแหน่ง: ก่อน closing class
// =====================================================

    /**
     * ✅ บันทึกการตั้งค่าผู้ใช้ (User Settings)
     * 
     * @param int    $member_id              รหัสสมาชิก
     * @param array  $settings_data          ข้อมูลการตั้งค่า
     * @param int    $updated_by             ผู้แก้ไข
     * @return array ['success' => bool, 'message' => string, 'affected_rows' => int]
     */
    public function save_user_settings_data($member_id, $settings_data, $updated_by)
    {
        try {
            log_message('info', "Google_drive_model: save_user_settings_data() called for member_id: {$member_id}");

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($member_id)) {
                log_message('error', 'Google_drive_model: save_user_settings_data() - Empty member_id');
                return [
                    'success' => false,
                    'message' => 'ไม่ได้ระบุรหัสผู้ใช้',
                    'affected_rows' => 0
                ];
            }

            // ตรวจสอบว่ามี record อยู่แล้วหรือไม่
            $existing = $this->db->select('id, member_id')
                ->from('tbl_google_drive_member_permissions')
                ->where('member_id', $member_id)
                ->get()
                ->row();

            // เตรียมข้อมูลสำหรับบันทึก
            $data = [
                'notes' => $settings_data['notes'] ?? '',
                'auto_sync' => isset($settings_data['auto_sync']) ? (int) $settings_data['auto_sync'] : 0,
                'notification_enabled' => isset($settings_data['notification']) ? (int) $settings_data['notification'] : 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $updated_by
            ];

            log_message('debug', 'Google_drive_model: save_user_settings_data() - Data to save: ' . json_encode($data));

            // ถ้ามี record อยู่แล้ว ให้ UPDATE
            if ($existing) {
                log_message('info', "Google_drive_model: save_user_settings_data() - Updating existing record (id: {$existing->id})");

                $this->db->where('id', $existing->id);
                $result = $this->db->update('tbl_google_drive_member_permissions', $data);

                if (!$result) {
                    $db_error = $this->db->error();
                    log_message('error', 'Google_drive_model: save_user_settings_data() - Update failed: ' . json_encode($db_error));
                    throw new Exception('Database error: ' . $db_error['message']);
                }

                $affected_rows = $this->db->affected_rows();
                log_message('info', "Google_drive_model: save_user_settings_data() - Updated successfully, affected_rows: {$affected_rows}");

                return [
                    'success' => true,
                    'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว',
                    'affected_rows' => $affected_rows
                ];
            }

            // ถ้ายังไม่มี record ให้ INSERT (พร้อมข้อมูลเริ่มต้น)
            log_message('info', 'Google_drive_model: save_user_settings_data() - Creating new record');

            $data['member_id'] = $member_id;
            $data['permission_type'] = 'custom'; // ค่าเริ่มต้น
            $data['can_create_folder'] = 0;
            $data['can_share'] = 0;
            $data['can_delete'] = 0;
            $data['is_active'] = 1;
            $data['override_position'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $updated_by;

            $result = $this->db->insert('tbl_google_drive_member_permissions', $data);

            if (!$result) {
                $db_error = $this->db->error();
                log_message('error', 'Google_drive_model: save_user_settings_data() - Insert failed: ' . json_encode($db_error));
                throw new Exception('Database error: ' . $db_error['message']);
            }

            $insert_id = $this->db->insert_id();
            log_message('info', "Google_drive_model: save_user_settings_data() - Inserted successfully, new id: {$insert_id}");

            return [
                'success' => true,
                'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว',
                'affected_rows' => 1,
                'insert_id' => $insert_id
            ];

        } catch (Exception $e) {
            log_message('error', 'Google_drive_model: save_user_settings_data() - Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'affected_rows' => 0
            ];
        }
    }

    /**
     * ✅ ลบการเข้าถึงผู้ใช้ออกถาวร (Remove User Access)
     * 
     * หมายเหตุ: ฟังก์ชันนี้จะลบข้อมูลจากฐานข้อมูลอย่างถาวร (DELETE)
     *          แตกต่างจาก reset_user_permissions ที่เป็น soft delete (UPDATE is_active=0)
     * 
     * @param int    $member_id              รหัสสมาชิก
     * @param int    $removed_by             ผู้ลบ
     * @return array ['success' => bool, 'message' => string, 'deleted_count' => int]
     */
    public function remove_user_access_data($member_id, $removed_by)
    {
        try {
            log_message('info', "Google_drive_model: remove_user_access_data() called for member_id: {$member_id}");

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($member_id)) {
                log_message('error', 'Google_drive_model: remove_user_access_data() - Empty member_id');
                return [
                    'success' => false,
                    'message' => 'ไม่ได้ระบุรหัสผู้ใช้',
                    'deleted_count' => 0
                ];
            }

            // ตรวจสอบว่าผู้ใช้มีอยู่จริง
            $user = $this->db->select('m_id, m_email, m_fname, m_lname')
                ->from('tbl_member')
                ->where('m_id', $member_id)
                ->get()
                ->row();

            if (!$user) {
                log_message('error', "Google_drive_model: remove_user_access_data() - User not found: {$member_id}");
                return [
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้ที่ระบุ',
                    'deleted_count' => 0
                ];
            }

            log_message('info', "Google_drive_model: remove_user_access_data() - Target user: {$user->m_email}");

            // เริ่ม Transaction
            $this->db->trans_start();

            $deleted_count = 0;

            // 1. ลบข้อมูลจาก tbl_google_drive_member_folder_access
            log_message('debug', 'Google_drive_model: remove_user_access_data() - Step 1: Deleting from tbl_google_drive_member_folder_access');

            if ($this->db->table_exists('tbl_google_drive_member_folder_access')) {
                $this->db->where('member_id', $member_id);
                $result = $this->db->delete('tbl_google_drive_member_folder_access');

                if ($result) {
                    $affected = $this->db->affected_rows();
                    $deleted_count += $affected;
                    log_message('info', "Google_drive_model: remove_user_access_data() - Deleted {$affected} rows from tbl_google_drive_member_folder_access");
                }
            }

            // 2. ลบข้อมูลจาก tbl_google_drive_member_permissions
            log_message('debug', 'Google_drive_model: remove_user_access_data() - Step 2: Deleting from tbl_google_drive_member_permissions');

            if ($this->db->table_exists('tbl_google_drive_member_permissions')) {
                $this->db->where('member_id', $member_id);
                $result = $this->db->delete('tbl_google_drive_member_permissions');

                if ($result) {
                    $affected = $this->db->affected_rows();
                    $deleted_count += $affected;
                    log_message('info', "Google_drive_model: remove_user_access_data() - Deleted {$affected} rows from tbl_google_drive_member_permissions");
                }
            }

            // 3. อัปเดตข้อมูลใน tbl_member (ปิด Google Drive access)
            log_message('debug', 'Google_drive_model: remove_user_access_data() - Step 3: Updating tbl_member');

            $member_update_data = [
                'storage_access_granted' => 0,
                'google_drive_enabled' => 0,
                'storage_quota_used' => 0,
                'last_storage_access' => null
            ];

            $this->db->where('m_id', $member_id);
            $this->db->update('tbl_member', $member_update_data);

            log_message('info', 'Google_drive_model: remove_user_access_data() - Updated tbl_member');

            // สิ้นสุด Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Google_drive_model: remove_user_access_data() - Transaction failed');
                return [
                    'success' => false,
                    'message' => 'ไม่สามารถลบข้อมูลได้',
                    'deleted_count' => 0
                ];
            }

            log_message('info', "Google_drive_model: remove_user_access_data() - Transaction completed successfully, total deleted: {$deleted_count}");

            return [
                'success' => true,
                'message' => 'ลบการเข้าถึงเรียบร้อยแล้ว',
                'deleted_count' => $deleted_count,
                'user_email' => $user->m_email
            ];

        } catch (Exception $e) {
            // Rollback transaction if needed
            if ($this->db->trans_status() !== null) {
                $this->db->trans_rollback();
            }

            log_message('error', 'Google_drive_model: remove_user_access_data() - Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'deleted_count' => 0
            ];
        }
    }

    // =====================================================
    // จบ Methods สำหรับ Settings Tab
    // =====================================================


}