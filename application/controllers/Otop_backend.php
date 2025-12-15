<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Otop_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '23']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('otop_model');
    }

    
public function index()
    {
        $data['qadmin'] = $this->otop_model->list_admin();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/otop', $data);
        $this->load->view('asset/js');
        $this->load->view('asset/option_js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/otop_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->otop_model->add();
        redirect('otop_backend', 'refresh');
    }


    public function editing($otop_id)
    {
        $data['rsedit'] = $this->otop_model->read_otop($otop_id);
        $data['qimg'] = $this->otop_model->read_img($otop_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/otop_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->otop_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function edit($otop_id)
    {
        $this->otop_model->edit($otop_id);
        redirect('otop_backend', 'refresh');
    }

    public function del($otop_id)
    {
        $this->otop_model->del_otop_img($otop_id);
        $this->otop_model->del_otop($otop_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('otop_backend', 'refresh');
    }

    public function updateOtopStatus()
    {
        $this->otop_model->updateOtopStatus();
    }

    // public function editing_User_Food($user_food_id)
    // {
    //     $data['rsedit'] = $this->food_model->read_user_food($user_food_id);
    //     $data['qimg'] = $this->food_model->read_img_user_food($user_food_id);

    //     // echo '<pre>';
    //     // print_r($data['rsedit']);
    //     // echo '</pre>';
    //     // exit();

    //     $this->load->view('templat/header');
    //     $this->load->view('asset/css');
    //     $this->load->view('templat/navbar_system_admin');
    //     $this->load->view('system_admin/food_user_form_edit', $data);
    //     $this->load->view('asset/js');
    //     $this->load->view('templat/footer');
    // }

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

    public function com($otop_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['otop'] = $this->otop_model->read_otop($otop_id);

        if (!empty($data['otop'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['rsCom'] = $this->otop_model->read_com_otop($otop_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['rsCom'] as $index => $com) {
                $otop_com_id = $com->otop_com_id;
                $com_reply_data = $this->otop_model->read_com_reply_otop($otop_com_id);

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
            $this->load->view('system_admin/otop_form_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com($otop_com_id)
    {
        $this->otop_model->del_reply($otop_com_id);
        $this->otop_model->del_com($otop_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($otop_com_reply_id)
    {
        $this->otop_model->del_com_reply($otop_com_reply_id);

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
