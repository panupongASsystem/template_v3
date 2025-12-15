<?php
defined('BASEPATH') or exit('No direct script access allowed');

class P_reb_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '90']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('p_reb_model');
    }

   

public function index()
    {
        $p_reb = $this->p_reb_model->list_all();

        foreach ($p_reb as $pdf) {
            $pdf->pdf = $this->p_reb_model->list_all_pdf($pdf->p_reb_id);
        }
        foreach ($p_reb as $doc) {
            $doc->doc = $this->p_reb_model->list_all_doc($doc->p_reb_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_reb', ['p_reb' => $p_reb]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_reb_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->p_reb_model->add();
        redirect('p_reb_backend');
    }


    public function editing($p_reb_id)
    {
        $data['rsedit'] = $this->p_reb_model->read($p_reb_id);
        $data['rsPdf'] = $this->p_reb_model->read_pdf($p_reb_id);
        $data['rsDoc'] = $this->p_reb_model->read_doc($p_reb_id);
        $data['rsImg'] = $this->p_reb_model->read_img($p_reb_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_reb_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($p_reb_id)
    {
        $this->p_reb_model->edit($p_reb_id);
        redirect('p_reb_backend');
    }

    public function update_p_reb_status()
    {
        $this->p_reb_model->update_p_reb_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->p_reb_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->p_reb_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->p_reb_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_p_reb($p_reb_id)
    {
        $this->p_reb_model->del_p_reb_img($p_reb_id);
        $this->p_reb_model->del_p_reb_pdf($p_reb_id);
        $this->p_reb_model->del_p_reb_doc($p_reb_id);
        $this->p_reb_model->del_p_reb($p_reb_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('p_reb_backend');
    }
}
