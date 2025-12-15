<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pbsv_statistics_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '127']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('pbsv_statistics_model');
    }

    

public function index()
    {
        $pbsv_statistics = $this->pbsv_statistics_model->list_all();

        foreach ($pbsv_statistics as $pdf) {
            $pdf->pdf = $this->pbsv_statistics_model->list_all_pdf($pdf->pbsv_statistics_id);
        }
        foreach ($pbsv_statistics as $doc) {
            $doc->doc = $this->pbsv_statistics_model->list_all_doc($doc->pbsv_statistics_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_statistics', ['pbsv_statistics' => $pbsv_statistics]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_statistics_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->pbsv_statistics_model->add();
        redirect('pbsv_statistics_backend');
    }


    public function editing($pbsv_statistics_id)
    {
        $data['rsedit'] = $this->pbsv_statistics_model->read($pbsv_statistics_id);
        $data['rsPdf'] = $this->pbsv_statistics_model->read_pdf($pbsv_statistics_id);
        $data['rsDoc'] = $this->pbsv_statistics_model->read_doc($pbsv_statistics_id);
        $data['rsImg'] = $this->pbsv_statistics_model->read_img($pbsv_statistics_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pbsv_statistics_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($pbsv_statistics_id)
    {
        $this->pbsv_statistics_model->edit($pbsv_statistics_id);
        redirect('pbsv_statistics_backend');
    }

    public function update_pbsv_statistics_status()
    {
        $this->pbsv_statistics_model->update_pbsv_statistics_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->pbsv_statistics_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->pbsv_statistics_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->pbsv_statistics_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_pbsv_statistics($pbsv_statistics_id)
    {
        $this->pbsv_statistics_model->del_pbsv_statistics_img($pbsv_statistics_id);
        $this->pbsv_statistics_model->del_pbsv_statistics_pdf($pbsv_statistics_id);
        $this->pbsv_statistics_model->del_pbsv_statistics_doc($pbsv_statistics_id);
        $this->pbsv_statistics_model->del_pbsv_statistics($pbsv_statistics_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('pbsv_statistics_backend');
    }
}
