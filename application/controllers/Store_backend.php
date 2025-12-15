<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Store_backend extends CI_Controller
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
        $this->load->model('store_model');
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
        $data['qadmin'] = $this->store_model->list_admin();
        $data['quser'] = $this->store_model->list_user();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/store', $data);
        $this->load->view('asset/js');
        $this->load->view('asset/option_js');
        $this->load->view('templat/footer');
    }

    public function adding_store()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/store_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_store()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->store_model->add_store();
        redirect('store_backend', 'refresh');
    }


    public function editing_store($store_id)
    {
        $data['rsedit'] = $this->store_model->read_store($store_id);
        $data['qimg'] = $this->store_model->read_img_store($store_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/store_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_store($store_id)
    {
        $this->store_model->edit_store($store_id);
        redirect('store_backend', 'refresh');
    }

    public function del_store($store_id)
    {
        $this->store_model->del_store_img($store_id);
        $this->store_model->del_store($store_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('store_backend', 'refresh');
    }

    public function updateStoreStatus()
    {
        $this->store_model->updateStoreStatus();
    }

    public function editing_user_store($user_store_id)
    {
        $data['rsedit'] = $this->store_model->read_user_store($user_store_id);
        $data['qimg'] = $this->store_model->read_img_user_store($user_store_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/store_user_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_user_store($user_store_id)
    {
        $this->store_model->edit_user_store($user_store_id);
        redirect('store_backend', 'refresh');
    }

    public function del_user_store($user_store_id)
    {
        $this->store_model->del_user_store_img($user_store_id);
        $this->store_model->del_user_store($user_store_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('store_backend', 'refresh');
    }

    public function updateUserStoreStatus()
    {
        $this->store_model->updateUserStoreStatus();
    }

    public function com_store($store_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['store'] = $this->store_model->read_store($store_id);

        if (!empty($data['store'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['rsCom'] = $this->store_model->read_com_store($store_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['rsCom'] as $index => $com) {
                $store_com_id = $com->store_com_id;
                $com_reply_data = $this->store_model->read_com_reply_store($store_com_id);

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
            $this->load->view('system_admin/store_form_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com($store_com_id)
    {
        $this->store_model->del_reply($store_com_id);
        $this->store_model->del_com($store_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($store_com_reply_id)
    {
        $this->store_model->del_com_reply($store_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function com_store_user($user_store_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['user_store'] = $this->store_model->read_user_store($user_store_id);

        if (!empty($data['user_store'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['user_rsCom'] = $this->store_model->read_user_com_store($user_store_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['user_rsCom'] as $index => $user_com) {
                $user_store_com_id = $user_com->user_store_com_id;
                $user_com_reply_data = $this->store_model->read_user_com_reply_store($user_store_com_id);

                // เก็บข้อมูลความคิดเห็นตอบกลับลงในอาร์เรย์ของความคิดเห็น
                $data['user_rsCom'][$index]->user_com_reply_data = $user_com_reply_data;
            }

            // echo '<pre>';
            // print_r($data['user_rsCom']);
            // echo '</pre>';
            // exit();

            // โหลดหน้า view และแสดงผล HTML
            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/store_user_form_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com_user($user_store_com_id)
    {
        $this->store_model->del_reply_user($user_store_com_id);
        $this->store_model->del_com_user($user_store_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply_user($user_store_com_reply_id)
    {
        $this->store_model->del_com_reply_user($user_store_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }
}
