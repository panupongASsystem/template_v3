<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ita_year_backend extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('ita_year_model');

        if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin', 'user_admin', 'end_user'])) {
            redirect('User/logout', 'refresh');
        }
    }


    public function index()
    {
        $data['query'] = $this->ita_year_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_year', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding_year()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_year_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_year()
    {
        $this->ita_year_model->add_year();
        redirect('ita_year_backend', 'refresh');
    }

    public function editing_year($ita_year_id)
    {
        $data['rsedit'] = $this->ita_year_model->read($ita_year_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_year_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_year($ita_year_id)
    {
        $this->ita_year_model->edit_year($ita_year_id);
        redirect('ita_year_backend', 'refresh');
    }

    public function index_topic($ita_year_id)
    {
        $data['query'] = $this->ita_year_model->read($ita_year_id);
        $data['query_topic'] = $this->ita_year_model->list_all_ita_topic($ita_year_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_year_topic', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function add_topic()
    {
        $ita_year_topic_ref_id = $this->input->post('ita_year_topic_ref_id');
        $ita_year_topic_name = $this->input->post('ita_year_topic_name');
        $ita_year_topic_msg = $this->input->post('ita_year_topic_msg');

        $this->ita_year_model->add_topic($ita_year_topic_ref_id, $ita_year_topic_name, $ita_year_topic_msg);

        redirect('ita_year_backend/index_topic/' . $ita_year_topic_ref_id);
    }

    public function editing_topic($ita_year_topic_id)
    {
        $data['rsedit'] = $this->ita_year_model->read_topic($ita_year_topic_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_year_topic_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function edit_topic()
    {
        $ita_year_topic_id = $this->input->post('ita_year_topic_id');
        $ita_year_topic_ref_id = $this->input->post('ita_year_topic_ref_id');
        $ita_year_topic_name = $this->input->post('ita_year_topic_name');
        $ita_year_topic_msg = $this->input->post('ita_year_topic_msg');
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->ita_year_model->edit_topic($ita_year_topic_ref_id, $ita_year_topic_name, $ita_year_topic_msg, $ita_year_topic_id);

        redirect('ita_year_backend/index_topic/' . $ita_year_topic_ref_id);
    }
    public function index_link($ita_year_topic_id)
    {
        $data['query'] = $this->ita_year_model->read_topic($ita_year_topic_id);
        $data['query_link'] = $this->ita_year_model->list_all_ita_link($ita_year_topic_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_year_link', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function add_link()
    {
        $ita_year_link_ref_id = $this->input->post('ita_year_link_ref_id');
        $ita_year_link_name = $this->input->post('ita_year_link_name');
        $ita_year_link_link1 = $this->input->post('ita_year_link_link1');
        $ita_year_link_link2 = $this->input->post('ita_year_link_link2');
        $ita_year_link_link3 = $this->input->post('ita_year_link_link3');
        $ita_year_link_link4 = $this->input->post('ita_year_link_link4');
        $ita_year_link_link5 = $this->input->post('ita_year_link_link5');
        $ita_year_link_title1 = $this->input->post('ita_year_link_title1');
        $ita_year_link_title2 = $this->input->post('ita_year_link_title2');
        $ita_year_link_title3 = $this->input->post('ita_year_link_title3');
        $ita_year_link_title4 = $this->input->post('ita_year_link_title4');
        $ita_year_link_title5 = $this->input->post('ita_year_link_title5');

        $this->ita_year_model->add_link($ita_year_link_ref_id, $ita_year_link_name, $ita_year_link_link1, $ita_year_link_link2, $ita_year_link_link3, $ita_year_link_link4, $ita_year_link_link5, $ita_year_link_title1, $ita_year_link_title2, $ita_year_link_title3, $ita_year_link_title4, $ita_year_link_title5);

        redirect('ita_year_backend/index_link/' . $ita_year_link_ref_id);
    }
    public function editing_link($ita_year_link_id)
    {
        $data['rsedit'] = $this->ita_year_model->read_link($ita_year_link_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_year_link_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function edit_link()
    {
        $ita_year_link_id = $this->input->post('ita_year_link_id');
        $ita_year_link_ref_id = $this->input->post('ita_year_link_ref_id');
        $ita_year_link_name = $this->input->post('ita_year_link_name');
        $ita_year_link_link1 = $this->input->post('ita_year_link_link1');
        $ita_year_link_link2 = $this->input->post('ita_year_link_link2');
        $ita_year_link_link3 = $this->input->post('ita_year_link_link3');
        $ita_year_link_link4 = $this->input->post('ita_year_link_link4');
        $ita_year_link_link5 = $this->input->post('ita_year_link_link5');
        $ita_year_link_title1 = $this->input->post('ita_year_link_title1');
        $ita_year_link_title2 = $this->input->post('ita_year_link_title2');
        $ita_year_link_title3 = $this->input->post('ita_year_link_title3');
        $ita_year_link_title4 = $this->input->post('ita_year_link_title4');
        $ita_year_link_title5 = $this->input->post('ita_year_link_title5');
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->ita_year_model->edit_link($ita_year_link_id, $ita_year_link_ref_id, $ita_year_link_name, $ita_year_link_link1, $ita_year_link_link2, $ita_year_link_link3, $ita_year_link_link4, $ita_year_link_link5, $ita_year_link_title1, $ita_year_link_title2, $ita_year_link_title3, $ita_year_link_title4, $ita_year_link_title5);

        redirect('ita_year_backend/index_link/' . $ita_year_link_ref_id);
    }
    public function del_ita_link($ita_year_link_id)
    {
        $this->ita_year_model->del_ita_link($ita_year_link_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function del_ita_topic($ita_year_topic_id)
    {
        $this->ita_year_model->del_ita_topic($ita_year_topic_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_ita_year($ita_year_id)
    {
        $this->ita_year_model->del_ita_year($ita_year_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function updateita_yearStatus()
    {
        $this->ita_year_model->updateita_yearStatus();
    }
}
