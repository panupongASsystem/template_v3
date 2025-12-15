<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Road_backend extends CI_Controller
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
        $this->load->model('road_model');
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
        $data['query'] = $this->road_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/road', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing($road_id)
    {
        $data['rsedit'] = $this->road_model->read($road_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/road_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($road_id)
    {
        $this->road_model->edit($road_id);
        redirect('road_backend', 'refresh');
    }

    public function del_hotNews($hotNews_id)
    {
        $this->road_model->del_hotNews($hotNews_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('road_backend', 'refresh');
    }

    public function updateHotNewsStatus()
    {
        $this->road_model->updateHotNewsStatus();
    }
}
