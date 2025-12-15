<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plan_pdl_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // 🎯 เช็คสิทธิ์แผนพัฒนาท้องถิ่น (ใช้ฟังก์ชันจาก MY_Controller)
        $this->check_access_permission(['1', '56']); // 1=ทั้งหมด, 56=แผนพัฒนาท้องถิ่น
        
        // โหลด models
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('plan_pdl_model');
    }
	
	



public function index()
    {
        $plan_pdl = $this->plan_pdl_model->list_all();

        foreach ($plan_pdl as $pdf) {
            $pdf->pdf = $this->plan_pdl_model->list_all_pdf($pdf->plan_pdl_id);
        }
        foreach ($plan_pdl as $doc) {
            $doc->doc = $this->plan_pdl_model->list_all_doc($doc->plan_pdl_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pdl', ['plan_pdl' => $plan_pdl]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pdl_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->plan_pdl_model->add();
        redirect('plan_pdl_backend');
    }


    public function editing($plan_pdl_id)
    {
        $data['rsedit'] = $this->plan_pdl_model->read($plan_pdl_id);
        $data['rsPdf'] = $this->plan_pdl_model->read_pdf($plan_pdl_id);
        $data['rsDoc'] = $this->plan_pdl_model->read_doc($plan_pdl_id);
        $data['rsImg'] = $this->plan_pdl_model->read_img($plan_pdl_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/plan_pdl_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($plan_pdl_id)
    {
        $this->plan_pdl_model->edit($plan_pdl_id);
        redirect('plan_pdl_backend');
    }

    public function update_plan_pdl_status()
    {
        $this->plan_pdl_model->update_plan_pdl_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->plan_pdl_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->plan_pdl_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->plan_pdl_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_plan_pdl($plan_pdl_id)
    {
        $this->plan_pdl_model->del_plan_pdl_img($plan_pdl_id);
        $this->plan_pdl_model->del_plan_pdl_pdf($plan_pdl_id);
        $this->plan_pdl_model->del_plan_pdl_doc($plan_pdl_id);
        $this->plan_pdl_model->del_plan_pdl($plan_pdl_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('plan_pdl_backend');
    }
}
