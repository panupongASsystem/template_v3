<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Camera_backend extends CI_Controller
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

        $this->load->model('camera_model');
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
        $data['query'] = $this->camera_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/camera', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addingCamera()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/camera_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function addCamera()
    {
        $data = array(
            'camera_name' => $this->input->post('camera_name'),
            'camera_lat' => $this->input->post('camera_lat'),
            'camera_long' => $this->input->post('camera_long'),
            'camera_api' => $this->input->post('camera_api')
        );

        $this->camera_model->addCamera($data);
        $this->session->set_flashdata('save_success', TRUE);
        redirect('camera_backend', 'refresh');
    }

    public function edit($camera_id)
    {
        $data['rsedit'] = $this->camera_model->read($camera_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/camera_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editCamera()
    {
        $camera_id = $this->input->post('camera_id'); // รับค่า camera_id จากฟอร์มแก้ไข
        $data = array(
            'camera_name' => $this->input->post('camera_name'),
            'camera_lat' => $this->input->post('camera_lat'),
            'camera_long' => $this->input->post('camera_long'),
            'camera_api' => $this->input->post('camera_api')
        );

        // ใช้ฟังก์ชัน update เพื่อแก้ไขข้อมูล CCTV ในฐานข้อมูล
        $this->camera_model->editCamera($camera_id, $data);
        $this->session->set_flashdata('save_success', TRUE);
        // หลังจากบันทึกแล้ว สามารถทำการ redirect ไปยังหน้ารายการ CCTV หรือหน้าอื่นๆ ตามที่คุณต้องการ
        redirect('camera_backend', 'refresh');
    }

    public function del_Camera($camera_id)
    {
        $this->camera_model->del_Camera($camera_id);
        redirect('camera_backend', 'refresh');
    }


    // ใน Controller
    public function getCameras()
    {
        $cameras = $this->camera_model->list_all(); // ดึงข้อมูลกล้องจากฐานข้อมูล ใช้ฟังก์ชัน list_all จาก model

        echo json_encode($cameras); // ส่งข้อมูลกล้องในรูปแบบ JSON กลับไปยัง JavaScript
    }

    public function cctv($camera_id)
    {
        $data['rsedit'] = $this->camera_model->read($camera_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/camera_cctv', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
}
