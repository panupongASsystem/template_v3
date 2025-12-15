<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transport_backend extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (
            $this->session->userdata('m_level') != 1 &&
            $this->session->userdata('m_level') != 2 &&
            $this->session->userdata('m_level') != 3 &&
            $this->session->userdata('m_level') != 4
        ) {
            redirect('user', 'refresh');
        }

        // ตั้งค่าเวลาหมดอายุของเซสชัน
    $this->check_session_timeout();

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('transport_model');
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
		$data['query'] = $this->transport_model->list_all();
        // $data['query'] = $this->employee_model->list_all();
        $data['used_space_mb'] = $this->space_model->get_used_space();
        // $data['upload_limit_mb'] = 35;
        $data['upload_limit_mb'] = $this->session->userdata('upload_limit_mb') ?? 35; // ตั้งค่าเริ่มต้นเป็น 35 MB

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/transport', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addingtransport()
    {
        $data['rstp'] = $this->transport_model->list_transport();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/transport_form_add', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addtransport()
    {

        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->emp_model->addemp();
        redirect('transport_backend', 'refresh');
    }

    public function edit($transport_id)
    {
        $data['rsedit'] = $this->transport_model->read($transport_id);
        $data['rstp'] = $this->transport_model->list_transport();

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/transport_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edittransport($transport_id)
    {
		$this->transport_model->edittransport($transport_id);
		redirect('transport_backend', 'refresh');
    }

    // $transport_id, $transport_ref_type_id, $transport_head, $transport_head2, $transport_head3, $transport_detail, $transport_detail2
    
    // $transport_ref_type_id = $this->input->post('transport_ref_type_id');
    // $transport_head = $this->input->post('transport_head');
    // $transport_head2 = $this->input->post('transport_head2');
    // $transport_head3 = $this->input->post('transport_head3');
    // $transport_detail = $this->input->post('transport_detail');
    // $transport_detail2 = $this->input->post('transport_detail2');

    public function del_emp($emp_id)
	{
		$this->employee_model->del_emp($emp_id);
		$this->session->set_flashdata('del_success', TRUE);
		redirect('transport_backend', 'refresh');
	}

}
