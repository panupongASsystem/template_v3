<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_pm_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '72']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_pm_model');
    }

    

public function index()
    {
        $operation_pm = $this->operation_pm_model->list_all();

        foreach ($operation_pm as $pdf) {
            $pdf->pdf = $this->operation_pm_model->list_all_pdf($pdf->operation_pm_id);
        }
        foreach ($operation_pm as $doc) {
            $doc->doc = $this->operation_pm_model->list_all_doc($doc->operation_pm_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_pm', ['operation_pm' => $operation_pm]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_pm_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_pm_model->add();
        redirect('operation_pm_backend');
    }


    public function editing($operation_pm_id)
    {
        $data['rsedit'] = $this->operation_pm_model->read($operation_pm_id);
        $data['rsPdf'] = $this->operation_pm_model->read_pdf($operation_pm_id);
        $data['rsDoc'] = $this->operation_pm_model->read_doc($operation_pm_id);
        $data['rsImg'] = $this->operation_pm_model->read_img($operation_pm_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_pm_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_pm_id)
    {
        $this->operation_pm_model->edit($operation_pm_id);
        redirect('operation_pm_backend');
    }

    public function update_operation_pm_status()
    {
        $this->operation_pm_model->update_operation_pm_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_pm_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_pm_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_pm_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_pm($operation_pm_id)
    {
        $this->operation_pm_model->del_operation_pm_img($operation_pm_id);
        $this->operation_pm_model->del_operation_pm_pdf($operation_pm_id);
        $this->operation_pm_model->del_operation_pm_doc($operation_pm_id);
        $this->operation_pm_model->del_operation_pm($operation_pm_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_pm_backend');
    }
}
