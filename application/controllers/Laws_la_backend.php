<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laws_la_backend extends CI_Controller
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
        $this->load->model('laws_la_model');
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

        $data['query'] = $this->laws_la_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_la', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_la_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->laws_la_model->add();
        redirect('laws_la_backend', 'refresh');
    }

    public function editing($laws_la_id)
    {
        $data['rsedit'] = $this->laws_la_model->read($laws_la_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_la_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($laws_la_id)
    {
        $this->laws_la_model->edit($laws_la_id);
        redirect('laws_la_backend', 'refresh');
    }

    public function del_laws_la($laws_la_id)
    {
        $this->laws_la_model->del_laws_la($laws_la_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('laws_la_backend', 'refresh');
    }

    public function updatelaws_laStatus()
    {
        $this->laws_la_model->updatelaws_laStatus();
    }
}
