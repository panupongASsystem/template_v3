<?php
defined('BASEPATH') or exit('No direct script access allowed');

class P_rpobuy_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '86']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('p_rpobuy_model');
    }

    

public function index()
    {
        $p_rpobuy = $this->p_rpobuy_model->list_all();

        foreach ($p_rpobuy as $pdf) {
            $pdf->pdf = $this->p_rpobuy_model->list_all_pdf($pdf->p_rpobuy_id);
        }
        foreach ($p_rpobuy as $doc) {
            $doc->doc = $this->p_rpobuy_model->list_all_doc($doc->p_rpobuy_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_rpobuy', ['p_rpobuy' => $p_rpobuy]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_rpobuy_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->p_rpobuy_model->add();
        redirect('p_rpobuy_backend');
    }


    public function editing($p_rpobuy_id)
    {
        $data['rsedit'] = $this->p_rpobuy_model->read($p_rpobuy_id);
        $data['rsPdf'] = $this->p_rpobuy_model->read_pdf($p_rpobuy_id);
        $data['rsDoc'] = $this->p_rpobuy_model->read_doc($p_rpobuy_id);
        $data['rsImg'] = $this->p_rpobuy_model->read_img($p_rpobuy_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_rpobuy_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($p_rpobuy_id)
    {
        $this->p_rpobuy_model->edit($p_rpobuy_id);
        redirect('p_rpobuy_backend');
    }

    public function update_p_rpobuy_status()
    {
        $this->p_rpobuy_model->update_p_rpobuy_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->p_rpobuy_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->p_rpobuy_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->p_rpobuy_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_p_rpobuy($p_rpobuy_id)
    {
        $this->p_rpobuy_model->del_p_rpobuy_img($p_rpobuy_id);
        $this->p_rpobuy_model->del_p_rpobuy_pdf($p_rpobuy_id);
        $this->p_rpobuy_model->del_p_rpobuy_doc($p_rpobuy_id);
        $this->p_rpobuy_model->del_p_rpobuy($p_rpobuy_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('p_rpobuy_backend');
    }
}
