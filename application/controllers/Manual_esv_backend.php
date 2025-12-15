<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manual_esv_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_access_permission(['1', '114']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('manual_esv_model');
    }

    

public function index()
    {
        $manual_esv = $this->manual_esv_model->list_all();

        foreach ($manual_esv as $pdf) {
            $pdf->pdf = $this->manual_esv_model->list_all_pdf($pdf->manual_esv_id);
        }
        foreach ($manual_esv as $doc) {
            $doc->doc = $this->manual_esv_model->list_all_doc($doc->manual_esv_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/manual_esv', ['manual_esv' => $manual_esv]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/manual_esv_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->manual_esv_model->add();
        redirect('manual_esv_backend');
    }


    public function editing($manual_esv_id)
    {
        $data['rsedit'] = $this->manual_esv_model->read($manual_esv_id);
        $data['rsPdf'] = $this->manual_esv_model->read_pdf($manual_esv_id);
        $data['rsDoc'] = $this->manual_esv_model->read_doc($manual_esv_id);
        $data['rsImg'] = $this->manual_esv_model->read_img($manual_esv_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/manual_esv_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($manual_esv_id)
    {
        $this->manual_esv_model->edit($manual_esv_id);
        redirect('manual_esv_backend');
    }

    public function update_manual_esv_status()
    {
        $this->manual_esv_model->update_manual_esv_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->manual_esv_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->manual_esv_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->manual_esv_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_manual_esv($manual_esv_id)
    {
        $this->manual_esv_model->del_manual_esv_img($manual_esv_id);
        $this->manual_esv_model->del_manual_esv_pdf($manual_esv_id);
        $this->manual_esv_model->del_manual_esv_doc($manual_esv_id);
        $this->manual_esv_model->del_manual_esv($manual_esv_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('manual_esv_backend');
    }
}
