<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbsv_utilities_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '39']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('pbsv_utilities_model');
    }

    

public function index()
    {
        $pbsv_utilities = $this->pbsv_utilities_model->list_all();

        foreach ($pbsv_utilities as $pdf) {
            $pdf->pdf = $this->pbsv_utilities_model->list_all_pdf($pdf->pbsv_utilities_id);
        }
        foreach ($pbsv_utilities as $doc) {
            $doc->doc = $this->pbsv_utilities_model->list_all_doc($doc->pbsv_utilities_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_utilities', ['pbsv_utilities' => $pbsv_utilities]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_utilities_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->pbsv_utilities_model->add();
        redirect('pbsv_utilities_backend');
    }


    public function editing($pbsv_utilities_id)
    {
        $data['rsedit'] = $this->pbsv_utilities_model->read($pbsv_utilities_id);
        $data['rsPdf'] = $this->pbsv_utilities_model->read_pdf($pbsv_utilities_id);
        $data['rsDoc'] = $this->pbsv_utilities_model->read_doc($pbsv_utilities_id);
        $data['rsImg'] = $this->pbsv_utilities_model->read_img($pbsv_utilities_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_utilities_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($pbsv_utilities_id)
    {
        $this->pbsv_utilities_model->edit($pbsv_utilities_id);
        redirect('pbsv_utilities_backend');
    }

    public function update_pbsv_utilities_status()
    {
        $this->pbsv_utilities_model->update_pbsv_utilities_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->pbsv_utilities_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->pbsv_utilities_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->pbsv_utilities_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_pbsv_utilities($pbsv_utilities_id)
    {
        $this->pbsv_utilities_model->del_pbsv_utilities_img($pbsv_utilities_id);
        $this->pbsv_utilities_model->del_pbsv_utilities_pdf($pbsv_utilities_id);
        $this->pbsv_utilities_model->del_pbsv_utilities_doc($pbsv_utilities_id);
        $this->pbsv_utilities_model->del_pbsv_utilities($pbsv_utilities_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('pbsv_utilities_backend');
    }
}
