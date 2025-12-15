<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Elderly_aw_ods_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '50']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('elderly_aw_ods_model');
    }

    

public function index()
    {
        $data['query'] = $this->elderly_aw_ods_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/elderly_aw_ods', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing($elderly_aw_ods_id)
    {
        $data['rsedit'] = $this->elderly_aw_ods_model->read($elderly_aw_ods_id);
        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/elderly_aw_ods_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($elderly_aw_ods_id)
    {
        $this->elderly_aw_ods_model->edit($elderly_aw_ods_id);
        redirect('elderly_aw_ods_backend');
    }
}
