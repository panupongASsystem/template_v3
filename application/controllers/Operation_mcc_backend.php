<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_mcc_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '68']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_mcc_model');
    }

    

public function index()
    {
        $operation_mcc = $this->operation_mcc_model->list_all();

        foreach ($operation_mcc as $pdf) {
            $pdf->pdf = $this->operation_mcc_model->list_all_pdf($pdf->operation_mcc_id);
        }
        foreach ($operation_mcc as $doc) {
            $doc->doc = $this->operation_mcc_model->list_all_doc($doc->operation_mcc_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_mcc', ['operation_mcc' => $operation_mcc]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_mcc_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_mcc_model->add();
        redirect('operation_mcc_backend');
    }


    public function editing($operation_mcc_id)
    {
        $data['rsedit'] = $this->operation_mcc_model->read($operation_mcc_id);
        $data['rsPdf'] = $this->operation_mcc_model->read_pdf($operation_mcc_id);
        $data['rsDoc'] = $this->operation_mcc_model->read_doc($operation_mcc_id);
        $data['rsImg'] = $this->operation_mcc_model->read_img($operation_mcc_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_mcc_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_mcc_id)
    {
        $this->operation_mcc_model->edit($operation_mcc_id);
        redirect('operation_mcc_backend');
    }

    public function update_operation_mcc_status()
    {
        $this->operation_mcc_model->update_operation_mcc_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_mcc_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_mcc_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_mcc_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_mcc($operation_mcc_id)
    {
        $this->operation_mcc_model->del_operation_mcc_img($operation_mcc_id);
        $this->operation_mcc_model->del_operation_mcc_pdf($operation_mcc_id);
        $this->operation_mcc_model->del_operation_mcc_doc($operation_mcc_id);
        $this->operation_mcc_model->del_operation_mcc($operation_mcc_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_mcc_backend');
    }
}
