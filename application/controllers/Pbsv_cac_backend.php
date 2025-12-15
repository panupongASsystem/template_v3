<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbsv_cac_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '41']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('pbsv_cac_model');
    }

    

public function index()
    {
        $pbsv_cac = $this->pbsv_cac_model->list_all();

        foreach ($pbsv_cac as $pdf) {
            $pdf->pdf = $this->pbsv_cac_model->list_all_pdf($pdf->pbsv_cac_id);
        }
        foreach ($pbsv_cac as $doc) {
            $doc->doc = $this->pbsv_cac_model->list_all_doc($doc->pbsv_cac_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_cac', ['pbsv_cac' => $pbsv_cac]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_cac_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->pbsv_cac_model->add();
        redirect('pbsv_cac_backend');
    }


    public function editing($pbsv_cac_id)
    {
        $data['rsedit'] = $this->pbsv_cac_model->read($pbsv_cac_id);
        $data['rsPdf'] = $this->pbsv_cac_model->read_pdf($pbsv_cac_id);
        $data['rsDoc'] = $this->pbsv_cac_model->read_doc($pbsv_cac_id);
        $data['rsImg'] = $this->pbsv_cac_model->read_img($pbsv_cac_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_cac_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($pbsv_cac_id)
    {
        $this->pbsv_cac_model->edit($pbsv_cac_id);
        redirect('pbsv_cac_backend');
    }

    public function update_pbsv_cac_status()
    {
        $this->pbsv_cac_model->update_pbsv_cac_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->pbsv_cac_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->pbsv_cac_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->pbsv_cac_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_pbsv_cac($pbsv_cac_id)
    {
        $this->pbsv_cac_model->del_pbsv_cac_img($pbsv_cac_id);
        $this->pbsv_cac_model->del_pbsv_cac_pdf($pbsv_cac_id);
        $this->pbsv_cac_model->del_pbsv_cac_doc($pbsv_cac_id);
        $this->pbsv_cac_model->del_pbsv_cac($pbsv_cac_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('pbsv_cac_backend');
    }
}
