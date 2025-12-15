<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Personnel_backend extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (
                        $this->session->userdata('m_level') != 1 &&
            $this->session->userdata('m_level') != 2 &&
            $this->session->userdata('m_level') != 3 &&
            $this->session->userdata('m_level') != 4
        ) {
            redirect('user', 'refresh');
        }

        // ตั้งค่าเวลาหมดอายุของเซสชัน
    $this->check_session_timeout();

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('personnel_model');
    }

    private function check_session_timeout() {
    $timeout = 900; // 15 นาที
    $last_activity = $this->session->userdata('last_activity');
    
    if ($last_activity && (time() - $last_activity > $timeout)) {
        $this->session->sess_destroy();
        redirect('User/logout', 'refresh');
    } else {
        $this->session->set_userdata('last_activity', time());
    }
}

public function index()
    {

        $data['query'] = $this->personnel_model->list_all();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/personnel', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding_Personnel()
    {
        $data['personnelGroup'] = $this->personnel_model->get_group();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/personnel_form_add', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function get_departments()
    {
        $group_name = $this->input->post('group_name');
        $personnelDepartments = $this->personnel_model->get_department_by_group($group_name);
        echo json_encode($personnelDepartments);
    }


    public function add_Personnel()
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->personnel_model->add_Personnel();
        redirect('personnel_backend', 'refresh');
    }

    public function editing_Personnel($personnel_id)
    {
        $data['rsedit'] = $this->personnel_model->read($personnel_id);
        $data['personnelGroup'] = $this->personnel_model->get_group();
        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/personnel_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_Personnel($personnel_id)
    {
        $this->personnel_model->edit_personnel($personnel_id);
        redirect('personnel_backend', 'refresh');
    }

    public function del_Personnel($personnel_id)
    {
        $this->personnel_model->del_Personnel($personnel_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('personnel_backend', 'refresh');
    }

    public function updatePersonnelStatus()
    {
        $this->personnel_model->updatePersonnelStatus();
    }
}
