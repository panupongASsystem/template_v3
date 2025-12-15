<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plan_pop_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '58']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('plan_pop_model');
    }

   

public function index()
    {
        $plan_pop = $this->plan_pop_model->list_all();

        foreach ($plan_pop as $pdf) {
            $pdf->pdf = $this->plan_pop_model->list_all_pdf($pdf->plan_pop_id);
        }
        foreach ($plan_pop as $doc) {
            $doc->doc = $this->plan_pop_model->list_all_doc($doc->plan_pop_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pop', ['plan_pop' => $plan_pop]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pop_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->plan_pop_model->add();
        redirect('plan_pop_backend');
    }


    public function editing($plan_pop_id)
    {
        $data['rsedit'] = $this->plan_pop_model->read($plan_pop_id);
        $data['rsPdf'] = $this->plan_pop_model->read_pdf($plan_pop_id);
        $data['rsDoc'] = $this->plan_pop_model->read_doc($plan_pop_id);
        $data['rsImg'] = $this->plan_pop_model->read_img($plan_pop_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pop_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($plan_pop_id)
    {
        $this->plan_pop_model->edit($plan_pop_id);
        redirect('plan_pop_backend');
    }

    public function update_plan_pop_status()
    {
        $this->plan_pop_model->update_plan_pop_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->plan_pop_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->plan_pop_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->plan_pop_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_plan_pop($plan_pop_id)
    {
        $this->plan_pop_model->del_plan_pop_img($plan_pop_id);
        $this->plan_pop_model->del_plan_pop_pdf($plan_pop_id);
        $this->plan_pop_model->del_plan_pop_doc($plan_pop_id);
        $this->plan_pop_model->del_plan_pop($plan_pop_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('plan_pop_backend');
    }
}
