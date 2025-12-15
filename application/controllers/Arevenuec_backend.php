<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Arevenuec_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '124']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('arevenuec_model');
    }

    

    public function index()
    {
        $arevenuec = $this->arevenuec_model->list_all();

        foreach ($arevenuec as $pdf) {
            $pdf->pdf = $this->arevenuec_model->list_all_pdf($pdf->arevenuec_id);
        }
        foreach ($arevenuec as $doc) {
            $doc->doc = $this->arevenuec_model->list_all_doc($doc->arevenuec_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/arevenuec', ['arevenuec' => $arevenuec]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/arevenuec_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->arevenuec_model->add();
        redirect('arevenuec_backend');
    }


    public function editing($arevenuec_id)
    {
        $data['rsedit'] = $this->arevenuec_model->read($arevenuec_id);
        $data['rsPdf'] = $this->arevenuec_model->read_pdf($arevenuec_id);
        $data['rsDoc'] = $this->arevenuec_model->read_doc($arevenuec_id);
        $data['rsImg'] = $this->arevenuec_model->read_img($arevenuec_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/arevenuec_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($arevenuec_id)
    {
        $this->arevenuec_model->edit($arevenuec_id);
        redirect('arevenuec_backend');
    }

    public function update_arevenuec_status()
    {
        $this->arevenuec_model->update_arevenuec_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->arevenuec_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->arevenuec_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->arevenuec_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_arevenuec($arevenuec_id)
    {
        $this->arevenuec_model->del_arevenuec_img($arevenuec_id);
        $this->arevenuec_model->del_arevenuec_pdf($arevenuec_id);
        $this->arevenuec_model->del_arevenuec_doc($arevenuec_id);
        $this->arevenuec_model->del_arevenuec($arevenuec_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('arevenuec_backend');
    }
}
