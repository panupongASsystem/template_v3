<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Banner_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
		       $this->check_access_permission(['1', '5']); // 1=ทั้งหมด

		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('banner_model');
    }
	
	

    public function index()
    {

        $data['query'] = $this->banner_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/banner', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding_Banner()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/banner_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_Banner()
    {
        $this->banner_model->add_Banner();
        redirect('banner_backend', 'refresh');
    }

    public function editing_Banner($banner_id)
    {
        $data['rsedit'] = $this->banner_model->read($banner_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/banner_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_Banner($banner_id)
    {
        $this->banner_model->edit_Banner($banner_id);
        redirect('banner_backend', 'refresh');
    }

    public function del_banner($banner_id)
    {
        $this->banner_model->del_banner($banner_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('banner_backend', 'refresh');
    }

    public function updateBannerStatus()
    {
        $this->banner_model->updateBannerStatus();
    }
}
