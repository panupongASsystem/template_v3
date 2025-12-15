<?php
defined('BASEPATH') or exit('No direct script access allowed');

class operation_mr_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '74']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_mr_model');
    }

    
public function index()
    {
        $operation_mr = $this->operation_mr_model->list_all();

        foreach ($operation_mr as $pdf) {
            $pdf->pdf = $this->operation_mr_model->list_all_pdf($pdf->operation_mr_id);
        }
        foreach ($operation_mr as $doc) {
            $doc->doc = $this->operation_mr_model->list_all_doc($doc->operation_mr_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_mr', ['operation_mr' => $operation_mr]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_mr_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_mr_model->add();
        redirect('operation_mr_backend');
    }


    public function editing($operation_mr_id)
    {
        $data['rsedit'] = $this->operation_mr_model->read($operation_mr_id);
        $data['rsPdf'] = $this->operation_mr_model->read_pdf($operation_mr_id);
        $data['rsDoc'] = $this->operation_mr_model->read_doc($operation_mr_id);
        $data['rsImg'] = $this->operation_mr_model->read_img($operation_mr_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_mr_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_mr_id)
    {
        $this->operation_mr_model->edit($operation_mr_id);
        redirect('operation_mr_backend');
    }

    public function update_operation_mr_status()
    {
        $this->operation_mr_model->update_operation_mr_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_mr_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_mr_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_mr_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_mr($operation_mr_id)
    {
        $this->operation_mr_model->del_operation_mr_img($operation_mr_id);
        $this->operation_mr_model->del_operation_mr_pdf($operation_mr_id);
        $this->operation_mr_model->del_operation_mr_doc($operation_mr_id);
        $this->operation_mr_model->del_operation_mr($operation_mr_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_mr_backend');
    }
}
