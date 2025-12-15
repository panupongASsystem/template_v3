<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Important_day_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
       		// เช็ค steb 1 ระบบที่เลือกตรงมั้ย
		                $this->check_access_permission(['1', '3']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('important_day_model');
    }

   

public function index()
    {
        // $control_important_day_status = $this->input->get('control_important_day_status');
    
        // if (!$control_important_day_status) {
        //     // ถ้าไม่มีการกรองด้วย control_important_day_status ให้ดึงทั้งหมด
        //     $control_important_days = $this->important_day_model->get_control_important_days();
        // } else {
        //     // ถ้ามีการกรองด้วย control_important_day_status ให้ดึงตามเงื่อนไข
        //     $control_important_days = $this->important_day_model->get_control_important_days($control_important_day_status);
        // }
    
        // foreach ($control_important_days as $control_important_day) {
        //     // $control_important_day->images = $this->control_important_day_model->get_images_for_control_important_day($control_important_day->control_important_day_id);
        // }

        $data['query'] = $this->important_day_model->list_all();
        $data['qControl'] = $this->important_day_model->control_list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/important_day', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding_important_day()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/important_day_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_important_day()
    {
        $this->important_day_model->add_important_day();
        redirect('important_day_backend', 'refresh');
    }

    public function editing_important_day($important_day_id)
    {
        $data['rsedit'] = $this->important_day_model->read($important_day_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/important_day_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_important_day($important_day_id)
    {
        $this->important_day_model->edit_important_day($important_day_id);
        redirect('important_day_backend', 'refresh');
    }

    public function del_important_day($important_day_id)
    {
        $this->important_day_model->del_important_day($important_day_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('important_day_backend', 'refresh');
    }

    public function updateimportant_dayStatus()
    {
        $this->important_day_model->updateimportant_dayStatus();
    }

    public function updateControlStatus()
    {
        $this->important_day_model->updateControl_important_dayStatus();
    }
}
