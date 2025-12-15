<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive System Model v1.0.0
 * จัดการข้อมูล Centralized Google Drive Storage
 * 
 * @author   System Developer
 * @version  1.0.0
 * @since    2025-01-20
 */
class Google_drive_system_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * ดึงข้อมูล System Storage ที่ active
     */
    public function get_active_system_storage() {
        try {
            return $this->db->select('*')
                           ->from('tbl_google_drive_system_storage')
                           ->where('is_active', 1)
                           ->order_by('created_at', 'DESC')
                           ->limit(1)
                           ->get()
                           ->row();
        } catch (Exception $e) {
            log_message('error', 'Get active system storage error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูล System Storage พร้อมสถิติ
     */
    public function get_system_storage_info() {
        try {
            $system_storage = $this->get_active_system_storage();
            if (!$system_storage) {
                return null;
            }

            // นับจำนวน folders และ files
            $total_folders = $this->db->where('is_active', 1)
                                     ->count_all_results('tbl_google_drive_system_folders');

            $total_files = $this->db->count_all('tbl_google_drive_system_files');

            $active_users = $this->db->where('storage_access_granted', 1)
                                    ->count_all_results('tbl_member');

            // คำนวณการใช้งาน
            $used_space = $this->db->select_sum('file_size')
                                  ->from('tbl_google_drive_system_files')
                                  ->get()
                                  ->row();

            $storage_used = $used_space->file_size ?: 0;
            $usage_percent = $system_storage->max_storage_limit > 0 
                           ? round(($storage_used / $system_storage->max_storage_limit) * 100, 2) 
                           : 0;

            return (object)[
                'id' => $system_storage->id,
                'storage_name' => $system_storage->storage_name,
                'google_account_email' => $system_storage->google_account_email,
                'total_storage_used' => $storage_used,
                'max_storage_limit' => $system_storage->max_storage_limit,
                'folder_structure_created' => $system_storage->folder_structure_created,
                'is_active' => $system_storage->is_active,
                'created_at' => $system_storage->created_at,
                'updated_at' => $system_storage->updated_at,
                'total_folders' => $total_folders,
                'total_files' => $total_files,
                'active_users' => $active_users,
                'storage_usage_percent' => $usage_percent
            ];

        } catch (Exception $e) {
            log_message('error', 'Get system storage info error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * สร้าง System Storage ใหม่
     */
    public function create_system_storage($data) {
        try {
            $this->db->trans_start();

            // ปิดการใช้งาน storage เก่า (ถ้ามี)
            $this->db->where('is_active', 1);
            $this->db->update('tbl_google_drive_system_storage', ['is_active' => 0]);

            // เพิ่ม storage ใหม่
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_google_drive_system_storage', $data);
            $storage_id = $this->db->insert_id();

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                return $storage_id;
            } else {
                return false;
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Create system storage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดต System Storage
     */
    public function update_system_storage($storage_id, $data) {
        try {
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $this->db->where('id', $storage_id);
            return $this->db->update('tbl_google_drive_system_storage', $data);

        } catch (Exception $e) {
            log_message('error', 'Update system storage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงสถานะการ setup
     */
    public function get_setup_status() {
        try {
            $system_storage = $this->get_active_system_storage();
            
            $status = [
                'has_system_storage' => !empty($system_storage),
                'google_account_connected' => false,
                'folder_structure_created' => false,
                'ready_to_use' => false
            ];

            if ($system_storage) {
                $status['google_account_connected'] = !empty($system_storage->google_account_email);
                $status['folder_structure_created'] = $system_storage->folder_structure_created == 1;
                $status['ready_to_use'] = $status['google_account_connected'] && $status['folder_structure_created'];
            }

            return $status;

        } catch (Exception $e) {
            log_message('error', 'Get setup status error: ' . $e->getMessage());
            return [
                'has_system_storage' => false,
                'google_account_connected' => false,
                'folder_structure_created' => false,
                'ready_to_use' => false
            ];
        }
    }

    /**
     * ดึงสถิติการใช้งาน Storage
     */
    public function get_storage_statistics() {
        try {
            $stats = [
                'total_folders' => 0,
                'total_files' => 0,
                'total_size' => 0,
                'active_users' => 0,
                'uploads_today' => 0,
                'uploads_this_month' => 0,
                'folder_types' => [],
                'file_types' => [],
                'top_users' => []
            ];

            // นับ folders
            $stats['total_folders'] = $this->db->where('is_active', 1)
                                              ->count_all_results('tbl_google_drive_system_folders');

            // นับ files และ size
            $file_stats = $this->db->select('COUNT(*) as total, SUM(file_size) as total_size')
                                  ->from('tbl_google_drive_system_files')
                                  ->get()
                                  ->row();

            $stats['total_files'] = $file_stats->total ?: 0;
            $stats['total_size'] = $file_stats->total_size ?: 0;

            // นับ active users
            $stats['active_users'] = $this->db->where('storage_access_granted', 1)
                                             ->count_all_results('tbl_member');

            // uploads วันนี้
            $stats['uploads_today'] = $this->db->where('DATE(created_at)', date('Y-m-d'))
                                              ->count_all_results('tbl_google_drive_system_files');

            // uploads เดือนนี้
            $stats['uploads_this_month'] = $this->db->where('YEAR(created_at)', date('Y'))
                                                   ->where('MONTH(created_at)', date('m'))
                                                   ->count_all_results('tbl_google_drive_system_files');

            // ประเภท folders
            $folder_types = $this->db->select('folder_type, COUNT(*) as count')
                                    ->from('tbl_google_drive_system_folders')
                                    ->where('is_active', 1)
                                    ->group_by('folder_type')
                                    ->get()
                                    ->result();

            foreach ($folder_types as $type) {
                $stats['folder_types'][$type->folder_type] = $type->count;
            }

            // ประเภทไฟล์ยอดนิยม
            $file_types = $this->db->select('
                                    SUBSTRING_INDEX(file_name, ".", -1) as extension,
                                    COUNT(*) as count
                                ')
                                ->from('tbl_google_drive_system_files')
                                ->group_by('extension')
                                ->order_by('count', 'DESC')
                                ->limit(10)
                                ->get()
                                ->result();

            foreach ($file_types as $type) {
                $stats['file_types'][$type->extension] = $type->count;
            }

            // ผู้ใช้ที่อัปโหลดมากที่สุด
            $top_users = $this->db->select('
                                    m.m_fname, m.m_lname, 
                                    COUNT(sf.id) as file_count,
                                    SUM(sf.file_size) as total_size
                                ')
                               ->from('tbl_google_drive_system_files sf')
                               ->join('tbl_member m', 'sf.uploaded_by = m.m_id')
                               ->group_by('sf.uploaded_by')
                               ->order_by('file_count', 'DESC')
                               ->limit(5)
                               ->get()
                               ->result();

            $stats['top_users'] = $top_users;

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Get storage statistics error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงกิจกรรมล่าสุด
     */
    public function get_recent_activities($limit = 20) {
        try {
            return $this->db->select('
                                gal.*, 
                                m.m_fname, m.m_lname,
                                CONCAT(m.m_fname, " ", m.m_lname) as member_name
                            ')
                           ->from('tbl_google_drive_activity_logs gal')
                           ->join('tbl_member m', 'gal.member_id = m.m_id', 'left')
                           ->order_by('gal.created_at', 'DESC')
                           ->limit($limit)
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get recent activities error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงโครงสร้างโฟลเดอร์
     */
    public function get_folder_structure($parent_id = null) {
        try {
            $folders = $this->db->select('*')
                               ->from('tbl_google_drive_system_folders')
                               ->where('is_active', 1);

            if ($parent_id) {
                $this->db->where('parent_folder_id', $parent_id);
            } else {
                $this->db->where('parent_folder_id IS NULL');
            }

            $folders = $this->db->order_by('folder_name', 'ASC')
                               ->get()
                               ->result();

            // ดึง subfolders สำหรับแต่ละ folder
            foreach ($folders as &$folder) {
                $folder->subfolders = $this->get_folder_structure($folder->folder_id);
                
                // นับไฟล์ในโฟลเดอร์
                $file_count = $this->db->where('folder_id', $folder->folder_id)
                                      ->count_all_results('tbl_google_drive_system_files');
                $folder->file_count = $file_count;
            }

            return $folders;

        } catch (Exception $e) {
            log_message('error', 'Get folder structure error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลโฟลเดอร์จาก ID
     */
    public function get_folder_by_id($folder_id) {
        try {
            return $this->db->select('*')
                           ->from('tbl_google_drive_system_folders')
                           ->where('folder_id', $folder_id)
                           ->where('is_active', 1)
                           ->get()
                           ->row();

        } catch (Exception $e) {
            log_message('error', 'Get folder by ID error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึง breadcrumbs ของโฟลเดอร์
     */
    public function get_folder_breadcrumbs($folder_id) {
        try {
            $breadcrumbs = [];
            $current_folder = $this->get_folder_by_id($folder_id);

            while ($current_folder) {
                array_unshift($breadcrumbs, [
                    'folder_id' => $current_folder->folder_id,
                    'folder_name' => $current_folder->folder_name,
                    'folder_type' => $current_folder->folder_type
                ]);

                if ($current_folder->parent_folder_id) {
                    $current_folder = $this->get_folder_by_id($current_folder->parent_folder_id);
                } else {
                    break;
                }
            }

            return $breadcrumbs;

        } catch (Exception $e) {
            log_message('error', 'Get folder breadcrumbs error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงโฟลเดอร์ย่อย
     */
    public function get_subfolders($parent_folder_id = null) {
        try {
            $this->db->select('*')
                    ->from('tbl_google_drive_system_folders')
                    ->where('is_active', 1);

            if ($parent_folder_id) {
                $this->db->where('parent_folder_id', $parent_folder_id);
            } else {
                $this->db->where('parent_folder_id IS NULL');
            }

            return $this->db->order_by('folder_name', 'ASC')
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get subfolders error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงไฟล์ในโฟลเดอร์
     */
    public function get_files_in_folder($folder_id = null, $search = '') {
        try {
            $this->db->select('
                        sf.*, 
                        m.m_fname, m.m_lname,
                        CONCAT(m.m_fname, " ", m.m_lname) as uploader_name
                    ')
                    ->from('tbl_google_drive_system_files sf')
                    ->join('tbl_member m', 'sf.uploaded_by = m.m_id', 'left');

            if ($folder_id) {
                $this->db->where('sf.folder_id', $folder_id);
            }

            if (!empty($search)) {
                $this->db->group_start()
                        ->like('sf.file_name', $search)
                        ->or_like('sf.original_name', $search)
                        ->group_end();
            }

            return $this->db->order_by('sf.created_at', 'DESC')
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get files in folder error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * บันทึกข้อมูลโฟลเดอร์
     */
    public function save_folder_info($data) {
        try {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('tbl_google_drive_system_folders', $data);

        } catch (Exception $e) {
            log_message('error', 'Save folder info error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * บันทึกข้อมูลไฟล์
     */
    public function save_file_info($data) {
        try {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('tbl_google_drive_system_files', $data);

        } catch (Exception $e) {
            log_message('error', 'Save file info error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลไฟล์จาก ID
     */
    public function get_file_by_id($file_id) {
        try {
            return $this->db->select('*')
                           ->from('tbl_google_drive_system_files')
                           ->where('file_id', $file_id)
                           ->get()
                           ->row();

        } catch (Exception $e) {
            log_message('error', 'Get file by ID error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ตรวจสอบว่าโฟลเดอร์มีเนื้อหาหรือไม่
     */
    public function folder_has_content($folder_id) {
        try {
            // ตรวจสอบไฟล์
            $file_count = $this->db->where('folder_id', $folder_id)
                                  ->count_all_results('tbl_google_drive_system_files');

            if ($file_count > 0) {
                return true;
            }

            // ตรวจสอบโฟลเดอร์ย่อย
            $subfolder_count = $this->db->where('parent_folder_id', $folder_id)
                                       ->where('is_active', 1)
                                       ->count_all_results('tbl_google_drive_system_folders');

            return $subfolder_count > 0;

        } catch (Exception $e) {
            log_message('error', 'Folder has content error: ' . $e->getMessage());
            return true; // ถ้าไม่แน่ใจ ให้ถือว่ามีเนื้อหา
        }
    }

    /**
     * ลบโฟลเดอร์
     */
    public function delete_folder($folder_id) {
        try {
            $this->db->where('folder_id', $folder_id);
            return $this->db->update('tbl_google_drive_system_folders', [
                'is_active' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Delete folder error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบไฟล์
     */
    public function delete_file($file_id) {
        try {
            $this->db->where('file_id', $file_id);
            return $this->db->delete('tbl_google_drive_system_files');

        } catch (Exception $e) {
            log_message('error', 'Delete file error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * บันทึกกิจกรรม
     */
    public function log_activity($member_id, $action_type, $description, $additional_data = []) {
        try {
            $data = [
                'member_id' => $member_id,
                'action_type' => $action_type,
                'action_description' => $description,
                'folder_id' => $additional_data['folder_id'] ?? null,
                'file_id' => $additional_data['file_id'] ?? null,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->insert('tbl_google_drive_activity_logs', $data);

        } catch (Exception $e) {
            log_message('error', 'Log activity error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายงานการใช้งานของ Users
     */
    public function get_user_storage_reports() {
        try {
            return $this->db->select('
                                m.m_id, m.m_fname, m.m_lname, m.m_email,
                                m.storage_quota_limit, m.storage_quota_used,
                                COUNT(sf.id) as total_files,
                                SUM(sf.file_size) as total_size,
                                MAX(sf.created_at) as last_upload,
                                p.pname as position_name
                            ')
                           ->from('tbl_member m')
                           ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                           ->join('tbl_google_drive_system_files sf', 'm.m_id = sf.uploaded_by', 'left')
                           ->where('m.storage_access_granted', 1)
                           ->group_by('m.m_id')
                           ->order_by('total_size', 'DESC')
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get user storage reports error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงรายงานไฟล์
     */
    public function get_file_reports() {
        try {
            return $this->db->select('
                                sf.*,
                                m.m_fname, m.m_lname,
                                CONCAT(m.m_fname, " ", m.m_lname) as uploader_name,
                                sfo.folder_name
                            ')
                           ->from('tbl_google_drive_system_files sf')
                           ->join('tbl_member m', 'sf.uploaded_by = m.m_id', 'left')
                           ->join('tbl_google_drive_system_folders sfo', 'sf.folder_id = sfo.folder_id', 'left')
                           ->order_by('sf.file_size', 'DESC')
                           ->limit(100)
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get file reports error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงรายงานกิจกรรม
     */
    public function get_activity_reports($days = 30) {
        try {
            $start_date = date('Y-m-d', strtotime("-{$days} days"));
            
            return $this->db->select('
                                gal.*,
                                m.m_fname, m.m_lname,
                                CONCAT(m.m_fname, " ", m.m_lname) as member_name
                            ')
                           ->from('tbl_google_drive_activity_logs gal')
                           ->join('tbl_member m', 'gal.member_id = m.m_id', 'left')
                           ->where('DATE(gal.created_at) >=', $start_date)
                           ->order_by('gal.created_at', 'DESC')
                           ->get()
                           ->result();

        } catch (Exception $e) {
            log_message('error', 'Get activity reports error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงรายงานภาพรวม
     */
    public function get_overview_reports() {
        try {
            $overview = [
                'daily_uploads' => [],
                'file_types_distribution' => [],
                'user_activity' => [],
                'storage_growth' => []
            ];

            // การอัปโหลดรายวัน (7 วันล่าสุด)
            $daily_uploads = $this->db->select('
                                        DATE(created_at) as date,
                                        COUNT(*) as count,
                                        SUM(file_size) as total_size
                                    ')
                                   ->from('tbl_google_drive_system_files')
                                   ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                   ->group_by('DATE(created_at)')
                                   ->order_by('date', 'ASC')
                                   ->get()
                                   ->result();

            $overview['daily_uploads'] = $daily_uploads;

            // การแจกแจงประเภทไฟล์
            $file_types = $this->db->select('
                                    SUBSTRING_INDEX(file_name, ".", -1) as extension,
                                    COUNT(*) as count,
                                    SUM(file_size) as total_size
                                ')
                                ->from('tbl_google_drive_system_files')
                                ->group_by('extension')
                                ->order_by('count', 'DESC')
                                ->limit(10)
                                ->get()
                                ->result();

            $overview['file_types_distribution'] = $file_types;

            // กิจกรรมผู้ใช้
            $user_activity = $this->db->select('
                                        m.m_fname, m.m_lname,
                                        COUNT(gal.id) as activity_count,
                                        MAX(gal.created_at) as last_activity
                                    ')
                                   ->from('tbl_google_drive_activity_logs gal')
                                   ->join('tbl_member m', 'gal.member_id = m.m_id')
                                   ->where('gal.created_at >=', date('Y-m-d', strtotime('-30 days')))
                                   ->group_by('gal.member_id')
                                   ->order_by('activity_count', 'DESC')
                                   ->limit(10)
                                   ->get()
                                   ->result();

            $overview['user_activity'] = $user_activity;

            return $overview;

        } catch (Exception $e) {
            log_message('error', 'Get overview reports error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์
     */
    public function check_folder_access($member_id, $folder_id) {
        try {
            // ดึงข้อมูล member และ folder
            $member = $this->db->select('m.*, p.pid as position_id')
                              ->from('tbl_member m')
                              ->join('tbl_position p', 'm.ref_pid = p.pid')
                              ->where('m.m_id', $member_id)
                              ->get()
                              ->row();

            $folder = $this->get_folder_by_id($folder_id);

            if (!$member || !$folder) {
                return false;
            }

            // Admin มีสิทธิ์เข้าถึงทุกอย่าง
            if (in_array($member->position_id, [1, 2])) {
                return true;
            }

            // ตรวจสอบตามประเภทโฟลเดอร์
            switch ($folder->folder_type) {
                case 'shared':
                    return true; // ทุกคนเข้าถึงได้
                
                case 'department':
                    return $folder->created_for_position == $member->position_id;
                
                case 'user':
                    return $folder->created_by == $member_id;
                
                case 'admin':
                    return in_array($member->position_id, [1, 2, 3]);
                
                default:
                    return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Check folder access error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดต Storage Quota ของ User
     */
    public function update_user_storage_quota($member_id, $file_size, $operation = 'add') {
        try {
            $member = $this->db->select('storage_quota_used')
                              ->from('tbl_member')
                              ->where('m_id', $member_id)
                              ->get()
                              ->row();

            if (!$member) {
                return false;
            }

            $new_quota = $member->storage_quota_used;
            
            if ($operation === 'add') {
                $new_quota += $file_size;
            } else {
                $new_quota = max(0, $new_quota - $file_size);
            }

            $this->db->where('m_id', $member_id);
            return $this->db->update('tbl_member', [
                'storage_quota_used' => $new_quota,
                'last_storage_access' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Update user storage quota error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ล้างข้อมูลที่ไม่ใช้แล้ว
     */
    public function cleanup_unused_data($days = 30) {
        try {
            $this->db->trans_start();

            // ลบ activity logs เก่า
            $cutoff_date = date('Y-m-d', strtotime("-{$days} days"));
            $this->db->where('created_at <', $cutoff_date);
            $this->db->delete('tbl_google_drive_activity_logs');

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Cleanup unused data error: ' . $e->getMessage());
            return false;
        }
    }
}