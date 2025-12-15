<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Questions_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
        $this->load->model('questions_model');
                  // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
                 $this->check_access_permission(['1', '111']); // 1=ทั้งหมด

    }

   
public function index()
    {
        // print_r($_SESSION);
        $data['query'] = $this->questions_model->list();
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // exit;
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/questions', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function add()
    {
        // 		echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;

        $this->questions_model->add();
        redirect('Questions_backend', 'refresh');
    }

    public function editing($questions_id)
    {
        $data['rsedit'] = $this->questions_model->read($questions_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/questions_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function edit($questions_id)
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->questions_model->edit($questions_id);
        redirect('Questions_backend', 'refresh');
    }

    public function del($questions_id)
    {
        // print_r($_POST);
        $this->questions_model->del($questions_id);
        redirect('Questions_backend', 'refresh');
    }
}
