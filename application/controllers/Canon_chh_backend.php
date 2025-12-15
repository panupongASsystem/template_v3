<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Canon_chh_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
          // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '99']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('canon_chh_model');
    }


public function index()
    {
        $canon_chh = $this->canon_chh_model->list_all();

        foreach ($canon_chh as $pdf) {
            $pdf->pdf = $this->canon_chh_model->list_all_pdf($pdf->canon_chh_id);
        }
        foreach ($canon_chh as $doc) {
            $doc->doc = $this->canon_chh_model->list_all_doc($doc->canon_chh_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/canon_chh', ['canon_chh' => $canon_chh]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/canon_chh_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->canon_chh_model->add();
        redirect('canon_chh_backend');
    }


    public function editing($canon_chh_id)
    {
        $data['rsedit'] = $this->canon_chh_model->read($canon_chh_id);
        $data['rsPdf'] = $this->canon_chh_model->read_pdf($canon_chh_id);
        $data['rsDoc'] = $this->canon_chh_model->read_doc($canon_chh_id);
        $data['rsImg'] = $this->canon_chh_model->read_img($canon_chh_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/canon_chh_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($canon_chh_id)
    {
        $this->canon_chh_model->edit($canon_chh_id);
        redirect('canon_chh_backend');
    }

    public function update_canon_chh_status()
    {
        $this->canon_chh_model->update_canon_chh_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->canon_chh_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->canon_chh_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->canon_chh_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_canon_chh($canon_chh_id)
    {
        $this->canon_chh_model->del_canon_chh_img($canon_chh_id);
        $this->canon_chh_model->del_canon_chh_pdf($canon_chh_id);
        $this->canon_chh_model->del_canon_chh_doc($canon_chh_id);
        $this->canon_chh_model->del_canon_chh($canon_chh_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('canon_chh_backend');
    }
}
