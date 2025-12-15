<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pppw_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '96']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('pppw_model');
    }

    
public function index()
    {
        $pppw = $this->pppw_model->list_all();

        foreach ($pppw as $pdf) {
            $pdf->pdf = $this->pppw_model->list_all_pdf($pdf->pppw_id);
        }
        foreach ($pppw as $doc) {
            $doc->doc = $this->pppw_model->list_all_doc($doc->pppw_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pppw', ['pppw' => $pppw]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pppw_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->pppw_model->add();
        redirect('pppw_backend');
    }


    public function editing($pppw_id)
    {
        $data['rsedit'] = $this->pppw_model->read($pppw_id);
        $data['rsPdf'] = $this->pppw_model->read_pdf($pppw_id);
        $data['rsDoc'] = $this->pppw_model->read_doc($pppw_id);
        $data['rsImg'] = $this->pppw_model->read_img($pppw_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/pppw_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($pppw_id)
    {
        $this->pppw_model->edit($pppw_id);
        redirect('pppw_backend');
    }

    public function update_pppw_status()
    {
        $this->pppw_model->update_pppw_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->pppw_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->pppw_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->pppw_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_pppw($pppw_id)
    {
        $this->pppw_model->del_pppw_img($pppw_id);
        $this->pppw_model->del_pppw_pdf($pppw_id);
        $this->pppw_model->del_pppw_doc($pppw_id);
        $this->pppw_model->del_pppw($pppw_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('pppw_backend');
    }
}
