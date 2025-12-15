<?php
defined('BASEPATH') or exit('No direct script access allowed');

class p_rpo_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '89']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('p_rpo_model');
    }

    

public function index()
    {
        $p_rpo = $this->p_rpo_model->list_all();

        foreach ($p_rpo as $pdf) {
            $pdf->pdf = $this->p_rpo_model->list_all_pdf($pdf->p_rpo_id);
        }
        foreach ($p_rpo as $doc) {
            $doc->doc = $this->p_rpo_model->list_all_doc($doc->p_rpo_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_rpo', ['p_rpo' => $p_rpo]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_rpo_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->p_rpo_model->add();
        redirect('p_rpo_backend');
    }


    public function editing($p_rpo_id)
    {
        $data['rsedit'] = $this->p_rpo_model->read($p_rpo_id);
        $data['rsPdf'] = $this->p_rpo_model->read_pdf($p_rpo_id);
        $data['rsDoc'] = $this->p_rpo_model->read_doc($p_rpo_id);
        $data['rsImg'] = $this->p_rpo_model->read_img($p_rpo_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_rpo_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($p_rpo_id)
    {
        $this->p_rpo_model->edit($p_rpo_id);
        redirect('p_rpo_backend');
    }

    public function update_p_rpo_status()
    {
        $this->p_rpo_model->update_p_rpo_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->p_rpo_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->p_rpo_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->p_rpo_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_p_rpo($p_rpo_id)
    {
        $this->p_rpo_model->del_p_rpo_img($p_rpo_id);
        $this->p_rpo_model->del_p_rpo_pdf($p_rpo_id);
        $this->p_rpo_model->del_p_rpo_doc($p_rpo_id);
        $this->p_rpo_model->del_p_rpo($p_rpo_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('p_rpo_backend');
    }
}
