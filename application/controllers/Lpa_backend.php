<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lpa_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
          // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
          $this->check_access_permission(['1', '77']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('lpa_model');
    }

    

public function index()
    {
        $lpa = $this->lpa_model->list_all();

        foreach ($lpa as $pdf) {
            $pdf->pdf = $this->lpa_model->list_all_pdf($pdf->lpa_id);
        }
        foreach ($lpa as $doc) {
            $doc->doc = $this->lpa_model->list_all_doc($doc->lpa_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/lpa', ['lpa' => $lpa]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/lpa_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->lpa_model->add();
        redirect('lpa_backend');
    }


    public function editing($lpa_id)
    {
        $data['rsedit'] = $this->lpa_model->read($lpa_id);
        $data['rsPdf'] = $this->lpa_model->read_pdf($lpa_id);
        $data['rsDoc'] = $this->lpa_model->read_doc($lpa_id);
        $data['rsImg'] = $this->lpa_model->read_img($lpa_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/lpa_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($lpa_id)
    {
        $this->lpa_model->edit($lpa_id);
        redirect('lpa_backend');
    }

    public function update_lpa_status()
    {
        $this->lpa_model->update_lpa_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->lpa_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->lpa_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->lpa_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_lpa($lpa_id)
    {
        $this->lpa_model->del_lpa_img($lpa_id);
        $this->lpa_model->del_lpa_pdf($lpa_id);
        $this->lpa_model->del_lpa_doc($lpa_id);
        $this->lpa_model->del_lpa($lpa_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('lpa_backend');
    }
}
