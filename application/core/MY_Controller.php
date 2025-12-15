<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    protected $shared_data = [];
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('menu'); // โหลด menu helper
		$this->load->database();
        $this->load->library('session'); // ✅ แก้ไขจาก $this->load->session()

        // โหลดข้อมูล modules สำหรับ sidebar
        $this->shared_data['modules'] = $this->db
            ->where('status', 1)
            ->order_by('display_order', 'ASC')
            ->get('tbl_member_modules')
            ->result();
    }

    protected function load_view($view, $data = []) {
        // รวมข้อมูล shared กับข้อมูลเฉพาะของแต่ละหน้า
        $view_data = array_merge($this->shared_data, $data);
        
        $this->load->view('member/header', $view_data);
        $this->load->view('member/css', $view_data);
        $this->load->view('member/sidebar', $view_data);
        $this->load->view($view, $view_data);
        $this->load->view('member/js', $view_data);
        $this->load->view('member/footer', $view_data);
    }
	
	
	
	protected function check_access_permission($required_permissions = [], $allowed_systems = ['system_admin', 'super_admin', 'user_admin'])
    {
        // เช็คว่ามี session หรือไม่
        if (!$this->session->userdata('m_id')) {
            $this->_redirect_to_logout();
            return false;
        }
        
        // เช็คสถานะ user ในฐานข้อมูล
        $user = $this->db->select('m_id, ref_pid, m_system, grant_user_ref_id, m_status')
                         ->where('m_id', $this->session->userdata('m_id'))
                         ->where('m_status', '1')
                         ->get('tbl_member')
                         ->row();
        
        if (!$user) {
            $this->_redirect_to_logout();
            return false;
        }
        
        // เช็คระบบที่อนุญาต
        $user_system = $this->session->userdata('m_system');
        
        if (!in_array($user_system, $allowed_systems)) {
            $this->_redirect_to_logout();
            return false;
        }
        
        // เช็คสิทธิ์เพิ่มเติมสำหรับ user_admin
        if ($user_system === 'user_admin' && !empty($required_permissions)) {
            $grant_user_ref_id = explode(',', $user->grant_user_ref_id);
            
            // ถ้ามีสิทธิ์ "1" (ทั้งหมด) ให้ผ่าน
            if (in_array('1', $grant_user_ref_id)) {
                $this->_update_last_activity();
                return true;
            }
            
            // เช็คสิทธิ์เฉพาะที่ต้องการ
            $has_permission = array_intersect($required_permissions, $grant_user_ref_id);
            
            if (empty($has_permission)) {
                redirect('system_admin');
                return false;
            }
        }
        
        // อัปเดต last_activity และส่งคืน true
        $this->_update_last_activity();
        return true;
    }

    /**
     * เช็คสิทธิ์เฉพาะ Admin ระดับสูง (system_admin, super_admin)
     * 
     * @return bool
     */
    protected function check_admin_only()
    {
        return $this->check_access_permission([], ['system_admin', 'super_admin']);
    }

    /**
     * เช็คสิทธิ์เฉพาะ System Admin
     * 
     * @return bool
     */
    protected function check_system_admin_only()
    {
        return $this->check_access_permission([], ['system_admin']);
    }

    /**
     * เช็คสิทธิ์แบบ flexible (ไม่ redirect ถ้าไม่มีสิทธิ์)
     * 
     * @param array $permissions สิทธิ์ที่ต้องการ
     * @return bool
     */
    protected function has_permission($permissions = [])
    {
        $user_system = $this->session->userdata('m_system');
        
        // system_admin และ super_admin ผ่านทุกอย่าง
        if (in_array($user_system, ['system_admin', 'super_admin'])) {
            return true;
        }
        
        // user_admin ต้องเช็คสิทธิ์
        if ($user_system === 'user_admin') {
            $grant_user_ref_id = explode(',', $this->session->userdata('grant_user_ref_id'));
            
            // มีสิทธิ์ทั้งหมด
            if (in_array('1', $grant_user_ref_id)) {
                return true;
            }
            
            // เช็คสิทธิ์เฉพาะ
            return !empty(array_intersect($permissions, $grant_user_ref_id));
        }
        
        return false;
    }

    /**
     * ดึงข้อมูลสิทธิ์ของ user ปัจจุบัน
     * 
     * @return array
     */
    protected function get_user_permissions()
    {
        $grant_user_ref_id = $this->session->userdata('grant_user_ref_id');
        return $grant_user_ref_id ? explode(',', $grant_user_ref_id) : [];
    }

    /**
     * ดึงข้อมูล user ปัจจุบัน
     * 
     * @return object|null
     */
    protected function get_current_user()
    {
        static $current_user = null;
        
        if ($current_user === null) {
            $user_id = $this->session->userdata('m_id');
            if ($user_id) {
                $current_user = $this->db->select('m_id, ref_pid, m_system, m_username, m_fname, m_lname, grant_user_ref_id')
                                        ->where('m_id', $user_id)
                                        ->where('m_status', '1')
                                        ->get('tbl_member')
                                        ->row();
            }
        }
        
        return $current_user;
    }

    /**
     * เช็คว่าเป็น Admin ระดับสูงหรือไม่
     * 
     * @return bool
     */
    protected function is_high_admin()
    {
        $user_system = $this->session->userdata('m_system');
        return in_array($user_system, ['system_admin', 'super_admin']);
    }

    /**
     * แสดงหน้า Access Denied
     * 
     * @param string $message ข้อความที่จะแสดง
     */
    protected function show_access_denied($message = 'คุณไม่มีสิทธิ์เข้าใช้งานส่วนนี้')
    {
        $data['error_message'] = $message;
        $data['back_url'] = site_url('system_admin');
        
        $this->load->view('templat/header');
        $this->load->view('templat/access_denied', $data);
        $this->load->view('templat/footer');
    }

    /**
     * อัปเดต last_activity (private method)
     */
    private function _update_last_activity()
    {
        $this->session->set_userdata('last_activity', time());
    }

    /**
     * Redirect ไป logout (private method)
     */
    private function _redirect_to_logout()
    {
        redirect('User/logout', 'refresh');
    }

    /**
     * Log การเข้าใช้งาน (optional)
     * 
     * @param string $action การกระทำ
     * @param string $details รายละเอียด
     */
    protected function log_user_activity($action, $details = '')
    {
        $user_id = $this->session->userdata('m_id');
        
        if ($user_id) {
            $log_data = [
                'user_id' => $user_id,
                'action' => $action,
                'details' => $details,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // บันทึกใน log table (ถ้ามี)
            if ($this->db->table_exists('user_activity_logs')) {
                $this->db->insert('user_activity_logs', $log_data);
            }
        }
    }
	
	
	
	
	
}