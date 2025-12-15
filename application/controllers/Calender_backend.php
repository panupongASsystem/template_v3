<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calender_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
       // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
		       $this->check_access_permission(['1', '7']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('calender_model');
    }

   

public function index()
    {
        $data['qadmin'] = $this->calender_model->list_admin();
        $data['qimg'] = $this->calender_model->list_admin();
        // $data['quser'] = $this->calender_model->list_user();
        // $data['query'] = $this->calender_model->list_all();
        $data['used_space_mb'] = $this->space_model->get_used_space();
        // $data['upload_limit_mb'] = 35;
        $data['upload_limit_mb'] = $this->session->userdata('upload_limit_mb') ?? 35; // ตั้งค่าเริ่มต้นเป็น 35 MB

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/calender', $data);
        $this->load->view('asset/js');
        $this->load->view('asset/option_js');
        $this->load->view('templat/footer');
    }

    public function adding_calender()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/calender_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_calender()
    {
        $this->calender_model->add_calender();
        redirect('calender_backend', 'refresh');
    }

    public function editing_calender($calender_id)
    {
        $data['rsedit'] = $this->calender_model->read_calender($calender_id);
        $data['qimg'] = $this->calender_model->read_img_calender($calender_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/calender_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_calender($calender_id)
    {
        $this->calender_model->edit_calender($calender_id);
        redirect('calender_backend', 'refresh');
    }

    public function del_calender($calender_id)
    {
        // $this->calender_model->del_calender_img($calender_id);
        $this->calender_model->del_calender($calender_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('calender_backend', 'refresh');
    }

    public function updatecalenderStatus()
    {
        $this->calender_model->updatecalenderStatus();
    }

    public function editing_User_calender($user_travel_id)
    {
        $data['rsedit'] = $this->calender_model->read_user_calender($user_travel_id);
        $data['qimg'] = $this->calender_model->read_user_img_calender($user_travel_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/calender_user_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_User_calender($user_calender_id)
    {
        $user_calender_name = $this->input->post('user_calender_name');
        $user_calender_detail = $this->input->post('user_calender_detail');
        $user_calender_phone = $this->input->post('user_calender_phone');

        $this->calender_model->edit_User_calender($user_calender_id, $user_calender_name, $user_calender_detail, $user_calender_phone);
        redirect('calender_backend', 'refresh');
    }

    public function del_User_calender($user_calender_id)
    {
        $this->calender_model->del_user_calender_img($user_calender_id);
        $this->calender_model->del_user_calender($user_calender_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('calender_backend', 'refresh');
    }

    public function updateUsercalenderStatus()
    {
        $this->calender_model->updateUsercalenderStatus();
    }

    public function com($calender_id)
    {
        $data['calender'] = $this->calender_model->read_calender($calender_id);

        if (!empty($data['calender'])) {
            $data['rsCom'] = $this->calender_model->read_com_calender($calender_id);

            foreach ($data['rsCom'] as $index => $com) {
                $calender_com_id = $com->calender_com_id;
                $com_reply_data = $this->calender_model->read_com_reply_calender($calender_com_id);

                $data['rsCom'][$index]->com_reply_data = $com_reply_data;
            }

            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/calender_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            echo "ไม่พบข้อมูลที่เกี่ยวข้อง";
        }
    }

    public function del_com($calender_com_id)
    {
        $this->calender_model->del_reply($calender_com_id);
        $this->calender_model->del_com($calender_com_id);

        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply($calender_com_reply_id)
    {
        $this->calender_model->del_com_reply($calender_com_reply_id);

        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function com_user($user_calender_id)
    {
        $data['user_calender'] = $this->calender_model->read_user_calender($user_calender_id);

        if (!empty($data['user_calender'])) {
            $data['user_rsCom'] = $this->calender_model->read_user_com_calender($user_calender_id);

            foreach ($data['user_rsCom'] as $index => $user_com) {
                $user_calender_com_id = $user_com->user_calender_com_id;
                $user_com_reply_data = $this->calender_model->read_user_com_reply_calender($user_calender_com_id);

                $data['user_rsCom'][$index]->user_com_reply_data = $user_com_reply_data;
            }

            $this->load->view('templat/header');
            $this->load->view('asset/css');
            $this->load->view('templat/navbar_system_admin');
            $this->load->view('system_admin/calender_user_com', $data);
            $this->load->view('asset/js');
            $this->load->view('templat/footer');
        } else {
            echo "ไม่พบข้อมูลที่เกี่ยวข้อง";
        }
    }

    public function del_com_user($user_calender_com_id)
    {
        $this->calender_model->del_reply_user($user_calender_com_id);
        $this->calender_model->del_com_user($user_calender_com_id);

        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function del_com_reply_user($user_calender_com_reply_id)
    {
        $this->calender_model->del_com_reply_user($user_calender_com_reply_id);

        $response = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($response);
        $this->session->set_flashdata('del_success', TRUE);
    }

    public function get_events() {
        $events = $this->calender_model->get_events();
        echo json_encode($events);
    }

    public function test_get_events() {
        $events = $this->calender_model->get_events();
        echo '<pre>';
        print_r($events);
        echo '</pre>';
    }
}
?>
