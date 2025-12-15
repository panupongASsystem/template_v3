<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Motto_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '19']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('motto_model');
    }

    

public function index()
    {
        $data['query'] = $this->motto_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/motto', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/motto_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/motto_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function save()
    {
        if ($this->motto_model->add()) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }
        redirect('motto_backend');
    }



    public function editing($motto_id)
    {
        $data['rsedit'] = $this->motto_model->read($motto_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/motto_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($motto_id)
    {
        $this->motto_model->edit($motto_id);
        redirect('motto_backend');
    }
}
