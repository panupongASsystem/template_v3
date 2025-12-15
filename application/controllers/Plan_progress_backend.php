<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plan_progress_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '129']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('plan_progress_model');
    }

    

    public function index()
    {
        $plan_progress = $this->plan_progress_model->list_all();

        foreach ($plan_progress as $pdf) {
            $pdf->pdf = $this->plan_progress_model->list_all_pdf($pdf->plan_progress_id);
        }
        foreach ($plan_progress as $doc) {
            $doc->doc = $this->plan_progress_model->list_all_doc($doc->plan_progress_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_progress', ['plan_progress' => $plan_progress]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_progress_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->plan_progress_model->add();
        redirect('plan_progress_backend');
    }


    public function editing($plan_progress_id)
    {
        $data['rsedit'] = $this->plan_progress_model->read($plan_progress_id);
        $data['rsPdf'] = $this->plan_progress_model->read_pdf($plan_progress_id);
        $data['rsDoc'] = $this->plan_progress_model->read_doc($plan_progress_id);
        $data['rsImg'] = $this->plan_progress_model->read_img($plan_progress_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_progress_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($plan_progress_id)
    {
        $this->plan_progress_model->edit($plan_progress_id);
        redirect('plan_progress_backend');
    }

    public function update_plan_progress_status()
    {
        $this->plan_progress_model->update_plan_progress_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->plan_progress_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->plan_progress_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->plan_progress_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_plan_progress($plan_progress_id)
    {
        $this->plan_progress_model->del_plan_progress_img($plan_progress_id);
        $this->plan_progress_model->del_plan_progress_pdf($plan_progress_id);
        $this->plan_progress_model->del_plan_progress_doc($plan_progress_id);
        $this->plan_progress_model->del_plan_progress($plan_progress_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('plan_progress_backend');
    }
}
