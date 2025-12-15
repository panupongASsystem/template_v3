<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Announce_oap_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '130']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('announce_oap_model');
    }

   
    public function index()
    {
        $announce_oap = $this->announce_oap_model->list_all();

        foreach ($announce_oap as $pdf) {
            $pdf->pdf = $this->announce_oap_model->list_all_pdf($pdf->announce_oap_id);
        }
        foreach ($announce_oap as $doc) {
            $doc->doc = $this->announce_oap_model->list_all_doc($doc->announce_oap_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_oap', ['announce_oap' => $announce_oap]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_oap_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->announce_oap_model->add();
        redirect('announce_oap_backend');
    }


    public function editing($announce_oap_id)
    {
        $data['rsedit'] = $this->announce_oap_model->read($announce_oap_id);
        $data['rsPdf'] = $this->announce_oap_model->read_pdf($announce_oap_id);
        $data['rsDoc'] = $this->announce_oap_model->read_doc($announce_oap_id);
        $data['rsImg'] = $this->announce_oap_model->read_img($announce_oap_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_oap_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($announce_oap_id)
    {
        $this->announce_oap_model->edit($announce_oap_id);
        redirect('announce_oap_backend');
    }

    public function update_announce_oap_status()
    {
        $this->announce_oap_model->update_announce_oap_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->announce_oap_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->announce_oap_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->announce_oap_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_announce_oap($announce_oap_id)
    {
        $this->announce_oap_model->del_announce_oap_img($announce_oap_id);
        $this->announce_oap_model->del_announce_oap_pdf($announce_oap_id);
        $this->announce_oap_model->del_announce_oap_doc($announce_oap_id);
        $this->announce_oap_model->del_announce_oap($announce_oap_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('announce_oap_backend');
    }
}
