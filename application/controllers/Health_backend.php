<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Health_backend extends CI_Controller
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
        $this->load->model('health_model');
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
        // print_r($_SESSION);
        // exit;
        $data['query'] = $this->health_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/health', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/health_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->health_model->add();
        redirect('health_backend', 'refresh');
    }


    public function editing($health_id)
    {
        $data['rsedit'] = $this->health_model->read($health_id);
        $data['qimg'] = $this->health_model->read_img($health_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/health_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($health_id)
    {
        $this->health_model->edit($health_id);
        redirect('health', 'refresh');
    }

    public function del($health_id)
    {
        $this->health_model->del_health_img($health_id);
        $this->health_model->del_health($health_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('health_backend', 'refresh');
    }

    public function updateHealthStatus()
    {
        $this->health_model->updateHealthStatus();
    }

    public function com($health_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['health'] = $this->health_model->read($health_id);

        if (!empty($data['health'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['rsCom'] = $this->health_model->read_com_health($health_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['rsCom'] as $index => $com) {
                $health_com_id = $com->health_com_id;
                $com_reply_data = $this->health_model->read_com_reply_health($health_com_id);

                // เก็บข้อมูลความคิดเห็นตอบกลับลงในอาร์เรย์ของความคิดเห็น
                $data['rsCom'][$index]->com_reply_data = $com_reply_data;
            }
            // echo '<pre>';
            // print_r($data['rsCom']);
            // echo '</pre>';
            // exit();
            // โหลดหน้า view และแสดงผล HTML
            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/health_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com($health_com_id)
    {
        $this->health_model->del_reply($health_com_id);
        $this->health_model->del_com($health_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($health_com_reply_id)
    {
        $this->health_model->del_com_reply($health_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }
}
