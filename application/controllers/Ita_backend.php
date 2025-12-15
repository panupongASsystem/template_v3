<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ita_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '76']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('ita_model');
    }

    
    public function index()
    {
        $ita = $this->ita_model->list_all();

        foreach ($ita as $pdf) {
            $pdf->pdf = $this->ita_model->list_all_pdf($pdf->ita_id);
        }
        foreach ($ita as $doc) {
            $doc->doc = $this->ita_model->list_all_doc($doc->ita_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita', ['ita' => $ita]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->ita_model->add();
        redirect('ita_backend');
    }


    public function editing($ita_id)
    {
        $data['rsedit'] = $this->ita_model->read($ita_id);
        $data['rsPdf'] = $this->ita_model->read_pdf($ita_id);
        $data['rsDoc'] = $this->ita_model->read_doc($ita_id);
        $data['rsImg'] = $this->ita_model->read_img($ita_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/ita_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($ita_id)
    {
        $this->ita_model->edit($ita_id);
        redirect('ita_backend');
    }

    public function update_ita_status()
    {
        $this->ita_model->update_ita_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->ita_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->ita_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->ita_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_ita($ita_id)
    {
        $this->ita_model->del_ita_img($ita_id);
        $this->ita_model->del_ita_pdf($ita_id);
        $this->ita_model->del_ita_doc($ita_id);
        $this->ita_model->del_ita($ita_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('ita_backend');
    }
}
