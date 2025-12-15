<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wifi_backend extends CI_Controller
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

        $this->load->model('wifi_model');
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
        $data['query'] = $this->wifi_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/wifi', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addingWifi()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/wifi_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addWifi()
    {
        $data = array(
            'wifi_name' => $this->input->post('wifi_name'),
            'wifi_lat' => $this->input->post('wifi_lat'),
            'wifi_long' => $this->input->post('wifi_long')
        );

        $this->wifi_model->addWifi($data);
        $this->session->set_flashdata('save_success', TRUE);
        redirect('wifi_backend', 'refresh');
    }

    public function edit($wifi_id)
    {
        $data['rsedit'] = $this->wifi_model->read($wifi_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/wifi_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editWifi()
    {
        $wifi_id = $this->input->post('wifi_id'); // รับค่า wifi_id จากฟอร์มแก้ไข
        $data = array(
            'wifi_name' => $this->input->post('wifi_name'),
            'wifi_lat' => $this->input->post('wifi_lat'),
            'wifi_long' => $this->input->post('wifi_long')
        );

        // ใช้ฟังก์ชัน update เพื่อแก้ไขข้อมูล CCTV ในฐานข้อมูล
        $this->wifi_model->editWifi($wifi_id, $data);
        $this->session->set_flashdata('save_success', TRUE);
        redirect('wifi_backend', 'refresh');
    }

    public function del_Wifi($wifi_id)
    {
        $this->wifi_model->del_Wifi($wifi_id);
        redirect('wifi_backend', 'refresh');
    }


    // ใน Controller
    public function getWifis()
    {
        $wifis = $this->wifi_model->list_all(); // ดึงข้อมูลกล้องจากฐานข้อมูล ใช้ฟังก์ชัน list_all จาก model
        echo json_encode($wifis); // ส่งข้อมูลกล้องในรูปแบบ JSON กลับไปยัง JavaScript
    }
}
