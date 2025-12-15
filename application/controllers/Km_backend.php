<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Km_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
                $this->check_access_permission(['1', '97']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('km_model');
    }

  

public function index()
    {
        $km = $this->km_model->list_all();

        foreach ($km as $pdf) {
            $pdf->pdf = $this->km_model->list_all_pdf($pdf->km_id);
        }
        foreach ($km as $doc) {
            $doc->doc = $this->km_model->list_all_doc($doc->km_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/km', ['km' => $km]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/km_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->km_model->add();
        redirect('km_backend');
    }


    public function editing($km_id)
    {
        $data['rsedit'] = $this->km_model->read($km_id);
        $data['rsPdf'] = $this->km_model->read_pdf($km_id);
        $data['rsDoc'] = $this->km_model->read_doc($km_id);
        $data['rsImg'] = $this->km_model->read_img($km_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/km_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($km_id)
    {
        $this->km_model->edit($km_id);
        redirect('km_backend');
    }

    public function update_km_status()
    {
        $this->km_model->update_km_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->km_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->km_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->km_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_km($km_id)
    {
        $this->km_model->del_km_img($km_id);
        $this->km_model->del_km_pdf($km_id);
        $this->km_model->del_km_doc($km_id);
        $this->km_model->del_km($km_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('km_backend');
    }
}
