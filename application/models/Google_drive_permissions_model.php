 <?php

defined('BASEPATH') OR exit('No direct script access allowed');


 class Google_drive_permissions_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * ดึงประเภทสิทธิ์ทั้งหมด
     */
    public function get_permission_types($active_only = true) {
        $this->db->select('*')
                ->from('tbl_google_drive_permission_types')
                ->order_by('type_name', 'ASC');
        
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        
        return $this->db->get()->result();
    }

    /**
     * ดึงสิทธิ์ของสมาชิก
     */
    public function get_member_permission($member_id) {
        // ใช้ View ที่สร้างไว้
        $result = $this->db->select('*')
                          ->from('view_google_drive_member_permissions')
                          ->where('m_id', $member_id)
                          ->get()
                          ->row();

        if ($result) {
            // แปลง JSON folder_access กลับเป็น array
            if ($result->folder_access) {
                $result->folder_access_array = json_decode($result->folder_access, true);
            } else {
                $result->folder_access_array = [];
            }
        }

        return $result;
    }

    /**
     * ดึงสิทธิ์ตามตำแหน่ง
     */
    public function get_position_permission($position_id) {
        $result = $this->db->select('pp.*, pt.type_name, pt.description')
                          ->from('tbl_google_drive_position_permissions pp')
                          ->join('tbl_google_drive_permission_types pt', 'pp.permission_type = pt.type_code', 'left')
                          ->where('pp.position_id', $position_id)
                          ->where('pp.is_active', 1)
                          ->get()
                          ->row();

        if ($result && $result->folder_access) {
            $result->folder_access_array = json_decode($result->folder_access, true);
        }

        return $result;
    }

    /**
     * ตั้งค่าสิทธิ์ตำแหน่ง
     */
    public function set_position_permission($position_id, $data, $created_by = null) {
        // แปลง array เป็น JSON
        if (isset($data['folder_access']) && is_array($data['folder_access'])) {
            $data['folder_access'] = json_encode($data['folder_access']);
        }

        $data['updated_by'] = $created_by;
        $data['updated_at'] = date('Y-m-d H:i:s');

        // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
        $existing = $this->db->where('position_id', $position_id)->get('tbl_google_drive_position_permissions')->row();

        if ($existing) {
            // อัปเดต
            $this->db->where('position_id', $position_id);
            return $this->db->update('tbl_google_drive_position_permissions', $data);
        } else {
            // เพิ่มใหม่
            $data['position_id'] = $position_id;
            $data['created_by'] = $created_by;
            return $this->db->insert('tbl_google_drive_position_permissions', $data);
        }
    }

    /**
     * ตั้งค่าสิทธิ์สมาชิกเฉพาะ
     */
    public function set_member_permission($member_id, $data, $created_by = null) {
        // แปลง array เป็น JSON
        if (isset($data['folder_access']) && is_array($data['folder_access'])) {
            $data['folder_access'] = json_encode($data['folder_access']);
        }

        $data['updated_by'] = $created_by;
        $data['updated_at'] = date('Y-m-d H:i:s');

        // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
        $existing = $this->db->where('member_id', $member_id)->get('tbl_google_drive_member_permissions')->row();

        if ($existing) {
            // อัปเดต
            $this->db->where('member_id', $member_id);
            return $this->db->update('tbl_google_drive_member_permissions', $data);
        } else {
            // เพิ่มใหม่
            $data['member_id'] = $member_id;
            $data['created_by'] = $created_by;
            return $this->db->insert('tbl_google_drive_member_permissions', $data);
        }
    }

    /**
     * ลบสิทธิ์สมาชิกเฉพาะ (กลับไปใช้สิทธิ์ตำแหน่ง)
     */
    public function remove_member_permission($member_id) {
        return $this->db->where('member_id', $member_id)->delete('tbl_google_drive_member_permissions');
    }

    /**
     * ดึงรายการ Folder ที่สามารถเข้าถึงได้
     */
    public function get_accessible_folders($member_id) {
        $permission = $this->get_member_permission($member_id);
        
        if (!$permission || !$permission->folder_access_array) {
            return [];
        }

        $accessible_folders = [];
        
        foreach ($permission->folder_access_array as $folder_type) {
            switch ($folder_type) {
                case 'all':
                    // เข้าถึงได้ทุก folder
                    $accessible_folders = $this->get_all_folders();
                    break;
                    
                case 'shared':
                    // เข้าถึง folder ส่วนกลาง
                    $shared_folders = $this->get_folders_by_type('shared');
                    $accessible_folders = array_merge($accessible_folders, $shared_folders);
                    break;
                    
                case 'department':
                    // เข้าถึง folder ของแผนก
                    $dept_folders = $this->get_department_folders($permission->position_id);
                    $accessible_folders = array_merge($accessible_folders, $dept_folders);
                    break;
                    
                case 'own_position':
                    // เข้าถึงเฉพาะ folder ของตำแหน่งตัวเอง
                    $own_folders = $this->get_position_folders($permission->position_id);
                    $accessible_folders = array_merge($accessible_folders, $own_folders);
                    break;
                    
                default:
                    // กรณีระบุ folder ID เฉพาะ
                    if (is_string($folder_type) && strpos($folder_type, 'folder_') === 0) {
                        $folder_id = str_replace('folder_', '', $folder_type);
                        $folder = $this->get_folder_by_id($folder_id);
                        if ($folder) {
                            $accessible_folders[] = $folder;
                        }
                    }
                    break;
            }
        }

        // ลบ duplicate
        return array_unique($accessible_folders, SORT_REGULAR);
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึง folder
     */
    public function can_access_folder($member_id, $folder_id) {
        $accessible_folders = $this->get_accessible_folders($member_id);
        
        foreach ($accessible_folders as $folder) {
            if ($folder->folder_id === $folder_id) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * ตรวจสอบสิทธิ์การดำเนินการ
     */
    public function can_perform_action($member_id, $action) {
        $permission = $this->get_member_permission($member_id);
        
        if (!$permission) {
            return false;
        }

        switch ($action) {
            case 'create_folder':
                return (bool)$permission->can_create_folder;
            case 'share':
                return (bool)$permission->can_share;
            case 'delete':
                return (bool)$permission->can_delete;
            default:
                return false;
        }
    }

    /**
     * ดึง Folder Templates
     */
    public function get_folder_templates($permission_type = null) {
        $this->db->select('*')
                ->from('tbl_google_drive_folder_templates')
                ->where('is_active', 1);
        
        if ($permission_type) {
            $this->db->where('permission_type', $permission_type);
        }
        
        $templates = $this->db->get()->result();
        
        // แปลง JSON structure
        foreach ($templates as $template) {
            if ($template->folder_structure) {
                $template->structure_array = json_decode($template->folder_structure, true);
            }
        }
        
        return $templates;
    }

    /**
     * สร้าง Folder ตาม Template
     */
    public function create_folders_from_template($member_id, $template_id) {
        $template = $this->db->where('id', $template_id)
                           ->where('is_active', 1)
                           ->get('tbl_google_drive_folder_templates')
                           ->row();

        if (!$template) {
            return false;
        }

        $structure = json_decode($template->folder_structure, true);
        $member = $this->db->select('m.*, p.pname')
                          ->from('tbl_member m')
                          ->join('tbl_position p', 'm.ref_pid = p.pid')
                          ->where('m.m_id', $member_id)
                          ->get()
                          ->row();

        if (!$member) {
            return false;
        }

        // สร้าง folders ตาม template
        $created_folders = [];
        
        if (isset($structure['main_folder'])) {
            // แทนที่ placeholder
            $main_folder_name = str_replace('{position_name}', $member->pname, $structure['main_folder']);
            
            // สร้าง main folder
            $main_folder_data = [
                'member_id' => $member_id,
                'position_id' => $member->ref_pid,
                'folder_id' => 'main_' . $member_id . '_' . uniqid(),
                'folder_name' => $main_folder_name,
                'folder_type' => 'main',
                'folder_url' => 'https://drive.google.com/drive/folders/main_' . $member_id . '_' . uniqid(),
                'created_by' => $member_id
            ];

            $this->db->insert('tbl_google_drive_folders', $main_folder_data);
            $created_folders[] = $main_folder_data;

            // สร้าง subfolders
            if (isset($structure['subfolders']) && is_array($structure['subfolders'])) {
                foreach ($structure['subfolders'] as $subfolder_name) {
                    $subfolder_data = [
                        'member_id' => $member_id,
                        'position_id' => $member->ref_pid,
                        'folder_id' => 'sub_' . $member_id . '_' . uniqid(),
                        'folder_name' => $subfolder_name,
                        'folder_type' => 'subfolder',
                        'parent_folder_id' => $main_folder_data['folder_id'],
                        'folder_url' => 'https://drive.google.com/drive/folders/sub_' . $member_id . '_' . uniqid(),
                        'created_by' => $member_id
                    ];

                    $this->db->insert('tbl_google_drive_folders', $subfolder_data);
                    $created_folders[] = $subfolder_data;
                }
            }
        }

        return $created_folders;
    }

    /**
     * ดึงรายการตำแหน่งพร้อมสิทธิ์
     */
    public function get_positions_with_permissions() {
        return $this->db->select('p.pid, p.pname, p.pdescription, 
                                 pp.permission_type, pt.type_name,
                                 pp.can_create_folder, pp.can_share, pp.can_delete')
                       ->from('tbl_position p')
                       ->join('tbl_google_drive_position_permissions pp', 'p.pid = pp.position_id', 'left')
                       ->join('tbl_google_drive_permission_types pt', 'pp.permission_type = pt.type_code', 'left')
                       ->where('p.pstatus', 'show')
                       ->order_by('p.porder', 'ASC')
                       ->get()
                       ->result();
    }

    /**
     * ดึงสมาชิกที่มีสิทธิ์พิเศษ
     */
    public function get_members_with_custom_permissions() {
        return $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email,
                                 mp.permission_type, pt.type_name,
                                 mp.override_position, mp.notes')
                       ->from('tbl_member m')
                       ->join('tbl_google_drive_member_permissions mp', 'm.m_id = mp.member_id')
                       ->join('tbl_google_drive_permission_types pt', 'mp.permission_type = pt.type_code')
                       ->where('mp.is_active', 1)
                       ->order_by('m.m_fname', 'ASC')
                       ->get()
                       ->result();
    }

    // Helper Methods
    private function get_all_folders() {
        return $this->db->where('is_active', 1)->get('tbl_google_drive_folders')->result();
    }

    private function get_folders_by_type($type) {
        return $this->db->where('folder_type', $type)
                       ->where('is_active', 1)
                       ->get('tbl_google_drive_folders')
                       ->result();
    }

    private function get_department_folders($position_id) {
        // Logic สำหรับดึง folder ของแผนก
        return $this->db->where('position_id', $position_id)
                       ->where('folder_type', 'department')
                       ->where('is_active', 1)
                       ->get('tbl_google_drive_folders')
                       ->result();
    }

    private function get_position_folders($position_id) {
        return $this->db->where('position_id', $position_id)
                       ->where('is_active', 1)
                       ->get('tbl_google_drive_folders')
                       ->result();
    }

    private function get_folder_by_id($folder_id) {
        return $this->db->where('folder_id', $folder_id)
                       ->where('is_active', 1)
                       ->get('tbl_google_drive_folders')
                       ->row();
    }
}