<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plan_pcra_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '66']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('plan_pcra_model');
    }

    

public function index()
    {
        $plan_pcra = $this->plan_pcra_model->list_all();

        foreach ($plan_pcra as $pdf) {
            $pdf->pdf = $this->plan_pcra_model->list_all_pdf($pdf->plan_pcra_id);
        }
        foreach ($plan_pcra as $doc) {
            $doc->doc = $this->plan_pcra_model->list_all_doc($doc->plan_pcra_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pcra', ['plan_pcra' => $plan_pcra]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pcra_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->plan_pcra_model->add();
        redirect('plan_pcra_backend');
    }


    public function editing($plan_pcra_id)
    {
        $data['rsedit'] = $this->plan_pcra_model->read($plan_pcra_id);
        $data['rsPdf'] = $this->plan_pcra_model->read_pdf($plan_pcra_id);
        $data['rsDoc'] = $this->plan_pcra_model->read_doc($plan_pcra_id);
        $data['rsImg'] = $this->plan_pcra_model->read_img($plan_pcra_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pcra_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($plan_pcra_id)
    {
        $this->plan_pcra_model->edit($plan_pcra_id);
        redirect('plan_pcra_backend');
    }

    public function update_plan_pcra_status()
    {
        $this->plan_pcra_model->update_plan_pcra_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->plan_pcra_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->plan_pcra_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->plan_pcra_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_plan_pcra($plan_pcra_id)
    {
        $this->plan_pcra_model->del_plan_pcra_img($plan_pcra_id);
        $this->plan_pcra_model->del_plan_pcra_pdf($plan_pcra_id);
        $this->plan_pcra_model->del_plan_pcra_doc($plan_pcra_id);
        $this->plan_pcra_model->del_plan_pcra($plan_pcra_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('plan_pcra_backend');
    }
}
