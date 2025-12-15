<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbsv_gup_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
               // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '44']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('pbsv_gup_model');
    }

    

public function index()
    {
        $pbsv_gup = $this->pbsv_gup_model->list_all();

        foreach ($pbsv_gup as $pdf) {
            $pdf->pdf = $this->pbsv_gup_model->list_all_pdf($pdf->pbsv_gup_id);
        }
        foreach ($pbsv_gup as $doc) {
            $doc->doc = $this->pbsv_gup_model->list_all_doc($doc->pbsv_gup_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_gup', ['pbsv_gup' => $pbsv_gup]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_gup_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->pbsv_gup_model->add();
        redirect('pbsv_gup_backend');
    }


    public function editing($pbsv_gup_id)
    {
        $data['rsedit'] = $this->pbsv_gup_model->read($pbsv_gup_id);
        $data['rsPdf'] = $this->pbsv_gup_model->read_pdf($pbsv_gup_id);
        $data['rsDoc'] = $this->pbsv_gup_model->read_doc($pbsv_gup_id);
        $data['rsImg'] = $this->pbsv_gup_model->read_img($pbsv_gup_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_gup_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($pbsv_gup_id)
    {
        $this->pbsv_gup_model->edit($pbsv_gup_id);
        redirect('pbsv_gup_backend');
    }

    public function update_pbsv_gup_status()
    {
        $this->pbsv_gup_model->update_pbsv_gup_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->pbsv_gup_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->pbsv_gup_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->pbsv_gup_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_pbsv_gup($pbsv_gup_id)
    {
        $this->pbsv_gup_model->del_pbsv_gup_img($pbsv_gup_id);
        $this->pbsv_gup_model->del_pbsv_gup_pdf($pbsv_gup_id);
        $this->pbsv_gup_model->del_pbsv_gup_doc($pbsv_gup_id);
        $this->pbsv_gup_model->del_pbsv_gup($pbsv_gup_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('pbsv_gup_backend');
    }
}
