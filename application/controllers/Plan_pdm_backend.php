<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plan_pdm_backend extends CI_Controller
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
        $this->load->model('plan_pdm_model');
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
        $plan_pdm = $this->plan_pdm_model->list_all();

        foreach ($plan_pdm as $pdf) {
            $pdf->pdf = $this->plan_pdm_model->list_all_pdf($pdf->plan_pdm_id);
        }
        foreach ($plan_pdm as $doc) {
            $doc->doc = $this->plan_pdm_model->list_all_doc($doc->plan_pdm_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pdm', ['plan_pdm' => $plan_pdm]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pdm_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->plan_pdm_model->add();
        redirect('plan_pdm_backend');
    }


    public function editing($plan_pdm_id)
    {
        $data['rsedit'] = $this->plan_pdm_model->read($plan_pdm_id);
        $data['rsPdf'] = $this->plan_pdm_model->read_pdf($plan_pdm_id);
        $data['rsDoc'] = $this->plan_pdm_model->read_doc($plan_pdm_id);
        $data['rsImg'] = $this->plan_pdm_model->read_img($plan_pdm_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pdm_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($plan_pdm_id)
    {
        $this->plan_pdm_model->edit($plan_pdm_id);
        redirect('plan_pdm_backend');
    }

    public function update_plan_pdm_status()
    {
        $this->plan_pdm_model->update_plan_pdm_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->plan_pdm_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->plan_pdm_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->plan_pdm_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_plan_pdm($plan_pdm_id)
    {
        $this->plan_pdm_model->del_plan_pdm_img($plan_pdm_id);
        $this->plan_pdm_model->del_plan_pdm_pdf($plan_pdm_id);
        $this->plan_pdm_model->del_plan_pdm_doc($plan_pdm_id);
        $this->plan_pdm_model->del_plan_pdm($plan_pdm_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('plan_pdm_backend');
    }
}
