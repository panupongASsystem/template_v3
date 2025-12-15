<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kid_aw_form_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '52']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('kid_aw_form_model');
    }

    

public function index()
    {
        $data['query'] = $this->kid_aw_form_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/kid_aw_form', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing($kid_aw_form_id)
    {
        $data['rsedit'] = $this->kid_aw_form_model->read($kid_aw_form_id);
        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/kid_aw_form_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($kid_aw_form_id)
    {
        $this->kid_aw_form_model->edit($kid_aw_form_id);
        redirect('kid_aw_form_backend');
    }
}
