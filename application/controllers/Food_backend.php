<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Food_backend extends CI_Controller
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
        $this->load->model('food_model');
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
        $data['qadmin'] = $this->food_model->list_admin();
        $data['quser'] = $this->food_model->list_user();
        // $data['query'] = $this->food_model->list_all();
        $data['used_space_mb'] = $this->space_model->get_used_space();
        // $data['upload_limit_mb'] = 35;
        $data['upload_limit_mb'] = $this->session->userdata('upload_limit_mb') ?? 35; // ตั้งค่าเริ่มต้นเป็น 35 MB

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/food', $data);
        $this->load->view('asset/js');
        $this->load->view('asset/option_js');
        $this->load->view('templat/footer');
    }

    public function adding_Food()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/food_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_Food()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->food_model->addFood();
        redirect('food_backend', 'refresh');
    }


    public function editing_Food($food_id)
    {
        $data['rsedit'] = $this->food_model->read_food($food_id);
        $data['qimg'] = $this->food_model->read_img_food($food_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/food_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_food($food_id)
    {
        $this->food_model->edit_food($food_id);
        redirect('food_backend', 'refresh');
    }

    public function del_food($food_id)
    {
        $this->food_model->del_food_img($food_id);
        $this->food_model->del_food($food_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('food_backend', 'refresh');
    }

    public function updateFoodStatus()
    {
        $this->food_model->updateFoodStatus();
    }

    public function editing_User_Food($user_food_id)
    {
        $data['rsedit'] = $this->food_model->read_user_food($user_food_id);
        $data['qimg'] = $this->food_model->read_img_user_food($user_food_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/food_user_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    // public function edit_User_Food($user_food_id)
    // {
    //     $this->food_model->edit_User_Food($user_food_id);
    //     redirect('food', 'refresh');
    // }

    // public function del_User_Food($user_food_id)
    // {
    //     $this->food_model->del_user_food_img($user_food_id);
    //     $this->food_model->del_user_food($user_food_id);
    //     $this->session->set_flashdata('del_success', TRUE);
    //     redirect('food', 'refresh');
    // }

    // public function updateUserFoodStatus()
    // {
    //     $this->food_model->updateUserFoodStatus();
    // }

    public function com_food($food_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['food'] = $this->food_model->read_food($food_id);

        if (!empty($data['food'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['rsCom'] = $this->food_model->read_com_food($food_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['rsCom'] as $index => $com) {
                $food_com_id = $com->food_com_id;
                $com_reply_data = $this->food_model->read_com_reply_food($food_com_id);

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
            $this->load->view('system_admin/food_form_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com($food_com_id)
    {
        $this->food_model->del_reply($food_com_id);
        $this->food_model->del_com($food_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($food_com_reply_id)
    {
        $this->food_model->del_com_reply($food_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    // public function com_food_user($user_food_id)
    // {
    //     // อ่านข้อมูลเมนูอาหาร
    //     $data['user_food'] = $this->food_model->read_user_food($user_food_id);

    //     if (!empty($data['user_food'])) {
    //         // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
    //         $data['user_rsCom'] = $this->food_model->read_user_com_food($user_food_id);

    //         // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
    //         foreach ($data['user_rsCom'] as $index => $user_com) {
    //             $user_food_com_id = $user_com->user_food_com_id;
    //             $user_com_reply_data = $this->food_model->read_user_com_reply_food($user_food_com_id);

    //             // เก็บข้อมูลความคิดเห็นตอบกลับลงในอาร์เรย์ของความคิดเห็น
    //             $data['user_rsCom'][$index]->user_com_reply_data = $user_com_reply_data;
    //         }

    //         // echo '<pre>';
    //         // print_r($data['user_rsCom']);
    //         // echo '</pre>';
    //         // exit();

    //         // โหลดหน้า view และแสดงผล HTML
    //         $this->load->view('templat/header');
    //         $this->load->view('asset/css');
    //         $this->load->view('templat/navbar_system_admin');
    //         $this->load->view('system_admin/food_user_form_com', $data);
    //         $this->load->view('asset/js');
    //         $this->load->view('templat/footer');
    //     } else {
    //         // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
    //         // ตัวอย่างเช่นแสดงข้อความผิดพลาด
    //         echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
    //     }
    // }

    // public function del_com_user($user_food_com_id)
    // {
    //     $this->food_model->del_reply_user($user_food_com_id);
    //     $this->food_model->del_com_user($user_food_com_id);

    //     // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
    //     $response = array('success' => true);
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    //     $this->session->set_flashdata('del_success', TRUE);
    // }

    // public function del_com_reply_user($user_food_com_reply_id)
    // {
    //     $this->food_model->del_com_reply_user($user_food_com_reply_id);

    //     // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
    //     $response = array('success' => true);
    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    //     $this->session->set_flashdata('del_success', TRUE);
    // }
}
