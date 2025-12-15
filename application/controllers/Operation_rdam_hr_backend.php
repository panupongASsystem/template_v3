<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_rdam_hr_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
               // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '81']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_rdam_hr_model');
    }

    

public function index()
    {
        $operation_rdam_hr = $this->operation_rdam_hr_model->list_all();

        foreach ($operation_rdam_hr as $pdf) {
            $pdf->pdf = $this->operation_rdam_hr_model->list_all_pdf($pdf->operation_rdam_hr_id);
        }
        foreach ($operation_rdam_hr as $doc) {
            $doc->doc = $this->operation_rdam_hr_model->list_all_doc($doc->operation_rdam_hr_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_rdam_hr', ['operation_rdam_hr' => $operation_rdam_hr]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_rdam_hr_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_rdam_hr_model->add();
        redirect('operation_rdam_hr_backend');
    }


    public function editing($operation_rdam_hr_id)
    {
        $data['rsedit'] = $this->operation_rdam_hr_model->read($operation_rdam_hr_id);
        $data['rsPdf'] = $this->operation_rdam_hr_model->read_pdf($operation_rdam_hr_id);
        $data['rsDoc'] = $this->operation_rdam_hr_model->read_doc($operation_rdam_hr_id);
        $data['rsImg'] = $this->operation_rdam_hr_model->read_img($operation_rdam_hr_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_rdam_hr_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_rdam_hr_id)
    {
        $this->operation_rdam_hr_model->edit($operation_rdam_hr_id);
        redirect('operation_rdam_hr_backend');
    }

    public function update_operation_rdam_hr_status()
    {
        $this->operation_rdam_hr_model->update_operation_rdam_hr_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_rdam_hr_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_rdam_hr_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_rdam_hr_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_rdam_hr($operation_rdam_hr_id)
    {
        $this->operation_rdam_hr_model->del_operation_rdam_hr_img($operation_rdam_hr_id);
        $this->operation_rdam_hr_model->del_operation_rdam_hr_pdf($operation_rdam_hr_id);
        $this->operation_rdam_hr_model->del_operation_rdam_hr_doc($operation_rdam_hr_id);
        $this->operation_rdam_hr_model->del_operation_rdam_hr($operation_rdam_hr_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_rdam_hr_backend');
    }
}
