<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Employee_backend extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('m_level') != 3) {
            redirect('user', 'refresh');
        }

        // ตั้งค่าเวลาหมดอายุของเซสชัน
        $this->check_session_timeout();

        $this->load->model('member_model');
        $this->load->model('doc_model');
        $this->load->model('employee_model');
    }

    private function check_session_timeout() {
    $timeout = 900; // 15 นาที
    $last_activity = $this->session->userdata('last_activity');
    
    if ($last_activity && (time() - $last_activity > $timeout)) {
        $this->session->sess_destroy();
        redirect('User/logout', 'refresh');
    } else {
        $this->session->set_userdata('last_activity', time());
    }
}

public function index()
    {
        $data['query'] = $this->employee_model->list_all();
        $data['used_space_mb'] = $this->doc_model->get_used_space();
        // $data['upload_limit_mb'] = 35;
        $data['upload_limit_mb'] = $this->session->userdata('upload_limit_mb') ?? 35; // ตั้งค่าเริ่มต้นเป็น 35 MB

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_staff');
        $this->load->view('staff/emp', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addingemp()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_staff');
        $this->load->view('staff/emp_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addemp()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->emp_model->addemp();
        redirect('employee_backend', 'refresh');
    }

    public function edit($emp_id)
    {
        $data['rsedit'] = $this->employee_model->read($emp_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_staff');
        $this->load->view('staff/emp_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editemp($emp_id)
    {
        $emp_status = $this->input->post('emp_status');
        $emp_nickname = $this->input->post('emp_nickname');
        $emp_name = $this->input->post('emp_name');
        $emp_lastname = $this->input->post('emp_lastname');
        $emp_role = $this->input->post('emp_role');
        $emp_phone = $this->input->post('emp_phone');
    
        // ส่งพารามิเตอร์ $emp_id ไปในฟังก์ชัน editemp
        $this->employee_model->editemp($emp_id, $emp_status, $emp_nickname, $emp_name, $emp_lastname, $emp_role, $emp_phone);
        redirect('employee_backend', 'refresh');
    }

    public function del_emp($emp_id)
	{
		$this->employee_model->del_emp($emp_id);
		$this->session->set_flashdata('del_success', TRUE);
		redirect('employee_backend', 'refresh');
	}

}
