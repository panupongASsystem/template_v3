<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_pgn_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '70']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_pgn_model');
    }

   

public function index()
    {
        $operation_pgn = $this->operation_pgn_model->list_all();

        foreach ($operation_pgn as $pdf) {
            $pdf->pdf = $this->operation_pgn_model->list_all_pdf($pdf->operation_pgn_id);
        }
        foreach ($operation_pgn as $doc) {
            $doc->doc = $this->operation_pgn_model->list_all_doc($doc->operation_pgn_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_pgn', ['operation_pgn' => $operation_pgn]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_pgn_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_pgn_model->add();
        redirect('operation_pgn_backend');
    }


    public function editing($operation_pgn_id)
    {
        $data['rsedit'] = $this->operation_pgn_model->read($operation_pgn_id);
        $data['rsPdf'] = $this->operation_pgn_model->read_pdf($operation_pgn_id);
        $data['rsDoc'] = $this->operation_pgn_model->read_doc($operation_pgn_id);
        $data['rsImg'] = $this->operation_pgn_model->read_img($operation_pgn_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_pgn_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_pgn_id)
    {
        $this->operation_pgn_model->edit($operation_pgn_id);
        redirect('operation_pgn_backend');
    }

    public function update_operation_pgn_status()
    {
        $this->operation_pgn_model->update_operation_pgn_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_pgn_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_pgn_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_pgn_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_pgn($operation_pgn_id)
    {
        $this->operation_pgn_model->del_operation_pgn_img($operation_pgn_id);
        $this->operation_pgn_model->del_operation_pgn_pdf($operation_pgn_id);
        $this->operation_pgn_model->del_operation_pgn_doc($operation_pgn_id);
        $this->operation_pgn_model->del_operation_pgn($operation_pgn_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_pgn_backend');
    }
}
