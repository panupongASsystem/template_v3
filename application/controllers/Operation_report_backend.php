<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_report_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
       $this->check_access_permission(['1', '88']); // 1=ทั้งหมด
		
		
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_report_model');
    }

    public function index()
    {
        $operation_report = $this->operation_report_model->list_all();

        foreach ($operation_report as $pdf) {
            $pdf->pdf = $this->operation_report_model->list_all_pdf($pdf->operation_report_id);
        }
        foreach ($operation_report as $doc) {
            $doc->doc = $this->operation_report_model->list_all_doc($doc->operation_report_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_report', ['operation_report' => $operation_report]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_report_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_report_model->add();
        redirect('operation_report_backend');
    }


    public function editing($operation_report_id)
    {
        $data['rsedit'] = $this->operation_report_model->read($operation_report_id);
        $data['rsPdf'] = $this->operation_report_model->read_pdf($operation_report_id);
        $data['rsDoc'] = $this->operation_report_model->read_doc($operation_report_id);
        $data['rsImg'] = $this->operation_report_model->read_img($operation_report_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_report_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_report_id)
    {
        $this->operation_report_model->edit($operation_report_id);
        redirect('operation_report_backend');
    }

    public function update_operation_report_status()
    {
        $this->operation_report_model->update_operation_report_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_report_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_report_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_report_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_report($operation_report_id)
    {
        $this->operation_report_model->del_operation_report_img($operation_report_id);
        $this->operation_report_model->del_operation_report_pdf($operation_report_id);
        $this->operation_report_model->del_operation_report_doc($operation_report_id);
        $this->operation_report_model->del_operation_report($operation_report_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_report_backend');
    }
}
