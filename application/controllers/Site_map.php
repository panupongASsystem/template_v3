<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_map extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // เช็คสิทธิ์ผู้ใช้งาน
        if(!$this->session->userdata('m_id')) {
            redirect('User/login');
        }
        // โหลดโมเดลที่จำเป็น
        $this->load->model('Member_model');
    }

    public function index() {
        $data = array();
        
        // เช็คว่าเป็น system_admin หรือไม่
        $data['is_system_admin'] = ($this->session->userdata('m_system') === 'system_admin');
        
        // ดึงข้อมูลโมดูลทั้งหมด
        $sql = "SELECT m.*,
                      (SELECT COUNT(*) FROM tbl_member_modules mm 
                       JOIN tbl_member_menu_permissions mp ON mm.id = mp.menu_id
                       WHERE mp.status = 1) as total_users,
                      COALESCE(
                          (SELECT GROUP_CONCAT(DISTINCT mm.name) 
                           FROM tbl_member_module_menus mm
                           WHERE mm.module_id = m.id AND mm.status = 1
                           ORDER BY mm.display_order), '') as menu_list
                FROM tbl_member_modules m
                ORDER BY m.display_order";
                
        $data['modules'] = $this->db->query($sql)->result();

        // Pagination
        $this->load->library('pagination');
        
        $config['base_url'] = base_url('Site_map/index');
        $config['total_rows'] = count($data['modules']);
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        
        // Bootstrap 4 Pagination Style
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-end">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['modules'] = array_slice($data['modules'], $page, $config['per_page']);
        
        $data['pagination'] = $this->pagination->create_links();
        $data['start_row'] = $page + 1;
        $data['end_row'] = $page + count($data['modules']);
        $data['total_rows'] = $config['total_rows'];

        // Load views
        $this->load->view('template/header');
        $this->load->view('template/sidebar');
        $this->load->view('site_map/index', $data);
        $this->load->view('template/footer');
    }

    public function toggle_status() {
        // ตรวจสอบว่าเป็น system_admin
        if($this->session->userdata('m_system') !== 'system_admin') {
            $response = array(
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ดำเนินการ'
            );
            echo json_encode($response);
            return;
        }

        $module_id = $this->input->post('module_id');
        $type = $this->input->post('type');
        $status = $this->input->post('status');
        
        // ตรวจสอบข้อมูลที่ส่งมา
        if(!$module_id || !$type || !isset($status)) {
            $response = array(
                'success' => false,
                'message' => 'ข้อมูลไม่ครบถ้วน'
            );
            echo json_encode($response);
            return;
        }

        // อัพเดทสถานะ
        $update_data = array();
        if($type === 'active') {
            $update_data['is_trial'] = $status ? 0 : 1;
            $message = $status ? 'เปลี่ยนเป็น Full Version เรียบร้อยแล้ว' : 'เปลี่ยนเป็น Trial Version เรียบร้อยแล้ว';
        } else {
            $update_data['status'] = $status;
            $message = $status ? 'เปิดการใช้งานเรียบร้อยแล้ว' : 'ปิดการใช้งานเรียบร้อยแล้ว';
        }
        
        $update_data['updated_by'] = $this->session->userdata('m_id');
        $update_data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $module_id);
        $result = $this->db->update('tbl_member_modules', $update_data);

        if($result) {
            $response = array(
                'success' => true,
                'message' => $message
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'ไม่สามารถอัพเดทข้อมูลได้'
            );
        }

        echo json_encode($response);
    }
}
?>