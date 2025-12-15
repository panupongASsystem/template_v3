<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Travel_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '22']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('travel_model');
    }

    

public function index()
    {
        // $data['query'] = $this->travel_model->list_all();
        $data['qadmin'] = $this->travel_model->list_admin();
        $data['quser'] = $this->travel_model->list_user();
        $data['used_space_mb'] = $this->space_model->get_used_space();
        // $data['upload_limit_mb'] = 35;
        $data['upload_limit_mb'] = $this->session->userdata('upload_limit_mb') ?? 35; // ตั้งค่าเริ่มต้นเป็น 35 MB

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/travel', $data);
        $this->load->view('asset/js');
        $this->load->view('asset/option_js');
        $this->load->view('templat/footer');
    }

    public function adding_Travel()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/travel_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_Travel()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->travel_model->add_Travel();
        redirect('travel_backend', 'refresh');
    }


    public function editing_Travel($travel_id)
    {
        $data['rsedit'] = $this->travel_model->read_travel($travel_id);
        $data['qimg'] = $this->travel_model->read_img_travel($travel_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/travel_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_Travel($travel_id)
    {
        $this->travel_model->edit_Travel($travel_id);
        redirect('travel_backend', 'refresh');
    }

    public function del_Travel($travel_id)
    {
        $this->travel_model->del_travel_img($travel_id);
        $this->travel_model->del_travel($travel_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('travel_backend', 'refresh');
    }

    public function updateTravelStatus()
    {
        $this->travel_model->updateTravelStatus();
    }

    public function editing_User_Travel($user_travel_id)
    {
        $data['rsedit'] = $this->travel_model->read_user_travel($user_travel_id);
        $data['qimg'] = $this->travel_model->read_img_user_travel($user_travel_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/travel_user_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_User_Travel($user_travel_id)
    {
        $this->travel_model->edit_User_Travel($user_travel_id);
        redirect('travel_backend', 'refresh');
    }

    public function del_User_Travel($user_travel_id)
    {
        $this->travel_model->del_user_travel_img($user_travel_id);
        $this->travel_model->del_user_travel($user_travel_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('travel_backend', 'refresh');
    }

    public function updateUserTravelStatus()
    {
        $this->travel_model->updateUserTravelStatus();
    }

    public function com($travel_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['travel'] = $this->travel_model->read_travel($travel_id);

        if (!empty($data['travel'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['rsCom'] = $this->travel_model->read_com_travel($travel_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['rsCom'] as $index => $com) {
                $travel_com_id = $com->travel_com_id;
                $com_reply_data = $this->travel_model->read_com_reply_travel($travel_com_id);

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
            $this->load->view('system_admin/travel_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com($travel_com_id)
    {
        $this->travel_model->del_reply($travel_com_id);
        $this->travel_model->del_com($travel_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($travel_com_reply_id)
    {
        $this->travel_model->del_com_reply($travel_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function com_user($user_travel_id)
    {
        // อ่านข้อมูลเมนูอาหาร
        $data['user_travel'] = $this->travel_model->read_user_travel($user_travel_id);

        if (!empty($data['user_travel'])) {
            // อ่านข้อมูลความคิดเห็นที่เกี่ยวข้องกับอาหาร
            $data['user_rsCom'] = $this->travel_model->read_user_com_travel($user_travel_id);

            // อ่านข้อมูลความคิดเห็นตอบกลับที่เกี่ยวข้องกับความคิดเห็น
            foreach ($data['user_rsCom'] as $index => $user_com) {
                $user_travel_com_id = $user_com->user_travel_com_id;
                $user_com_reply_data = $this->travel_model->read_user_com_reply_travel($user_travel_com_id);

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
            $this->load->view('system_admin/travel_user_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            // หากไม่พบข้อมูลให้แสดงข้อความผิดพลาดหรือกระทำอื่นตามที่คุณต้องการ
            // ตัวอย่างเช่นแสดงข้อความผิดพลาด
            echo "ไม่พบข้อมูลอาหารที่เกี่ยวข้อง";
        }
    }

    public function del_com_user($user_travel_com_id)
    {
        $this->travel_model->del_reply_user($user_travel_com_id);
        $this->travel_model->del_com_user($user_travel_com_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply_user($user_travel_com_reply_id)
    {
        $this->travel_model->del_com_reply_user($user_travel_com_reply_id);

        // ส่งคำตอบในรูปแบบ JSON เพื่อระบุว่าการลบสำเร็จ
        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }
	
	 public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->travel_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }
}
