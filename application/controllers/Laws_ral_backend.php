<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laws_ral_backend extends CI_Controller
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
        $this->load->model('laws_ral_model');
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

        $data['query'] = $this->laws_ral_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_ral', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_ral_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->laws_ral_model->add();
        redirect('laws_ral_backend', 'refresh');
    }

    public function editing($laws_ral_id)
    {
        $data['rsedit'] = $this->laws_ral_model->read($laws_ral_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_ral_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($laws_ral_id)
    {
        $this->laws_ral_model->edit($laws_ral_id);
        redirect('laws_ral_backend', 'refresh');
    }

    public function del_laws_ral($laws_ral_id)
    {
        $this->laws_ral_model->del_laws_ral($laws_ral_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('laws_ral_backend', 'refresh');
    }

    public function updatelaws_ralStatus()
    {
        $this->laws_ral_model->updatelaws_ralStatus();
    }
}
