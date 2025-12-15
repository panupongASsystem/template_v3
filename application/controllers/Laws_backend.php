<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laws_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_access_permission(['1', '115']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('laws_model');
    }

    

public function index()
    {
        $data['query'] = $this->laws_model->list_all();


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_topic', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_topic()
    {
        $this->laws_model->add_topic();
        redirect('laws_backend', 'refresh');
    }

    public function editing_topic($laws_topic_id)
    {
        $data['rsedit'] = $this->laws_model->read($laws_topic_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_topic_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_topic($laws_topic_id)
    {
        $this->laws_model->edit_topic($laws_topic_id);
        redirect('laws_backend', 'refresh');
    }


    public function index_laws($laws_topic_id)
    {
        $data['query'] = $this->laws_model->read($laws_topic_id);
        $data['query_laws'] = $this->laws_model->list_all_laws($laws_topic_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_laws()
    {
        $laws_ref_id = $this->input->post('laws_ref_id');

        $this->laws_model->add_laws();

        redirect('laws_backend/index_laws/' . $laws_ref_id);
    }

    public function editing_laws($laws_id)
    {
        $data['rsedit'] = $this->laws_model->read_laws($laws_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/laws_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_laws($laws_id)
    {
        $this->laws_model->edit_laws($laws_id);
        // ย้อนกลับ 2 หน้า
        echo '<script>window.history.go(-2);</script>';
    }

    public function del_laws($laws_id)
    {
        $this->laws_model->del_laws($laws_id);
        $this->session->set_flashdata('del_success', TRUE);
        // ย้อนกลับในกรณีที่ลบจะอยู่หน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_laws_all($laws_topic_id)
    {
        $this->laws_model->del_laws_all($laws_topic_id);
        $this->session->set_flashdata('del_success', TRUE);
        // ย้อนกลับในกรณีที่ลบจะอยู่หน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }
    
}
