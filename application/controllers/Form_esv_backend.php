<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Form_esv_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
               $this->check_access_permission(['1', '112']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('form_esv_model');
    }

    

public function index()
    {

        $data['query'] = $this->form_esv_model->list_topic();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/form_esv_topic', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding_topic()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/form_esv_form_add_topic');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_topic()
    {
        $this->form_esv_model->add_topic();
        redirect('form_esv_backend');
    }

    public function editing_topic($form_esv_topic_id)
    {
        $data['rsedit'] = $this->form_esv_model->read_topic($form_esv_topic_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/form_esv_topic_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_topic($form_esv_topic_id)
    {
        $this->form_esv_model->edit_topic($form_esv_topic_id);
        redirect('form_esv_backend');
    }

    public function index_content($form_esv_topic_id)
    {
        $data['rsedit'] = $this->form_esv_model->read_topic($form_esv_topic_id);
        $data['query'] = $this->form_esv_model->list_all_topic($form_esv_topic_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/form_esv', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/form_esv_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->form_esv_model->add();
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function editing($form_esv_id)
    {
        $data['rsedit'] = $this->form_esv_model->read($form_esv_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/form_esv_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($form_esv_id)
    {
        $form_esv_ref_id = $this->input->post('form_esv_ref_id');

        $this->form_esv_model->edit($form_esv_id);
        redirect('form_esv_backend/index_content/' . $form_esv_ref_id);
    }

    public function del_form_esv($form_esv_id)
    {
        $this->form_esv_model->del_form_esv($form_esv_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function del_form_esv_topic($form_esv_topic_id)
    {
        $this->form_esv_model->del_form_esv_topic_all($form_esv_topic_id);
        $this->form_esv_model->del_form_esv_topic($form_esv_topic_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('form_esv_backend');
    }

    public function updateform_esvStatus()
    {
        $this->form_esv_model->updateform_esvStatus();
    }
}
