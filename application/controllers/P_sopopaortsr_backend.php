<?php
defined('BASEPATH') or exit('No direct script access allowed');

class P_sopopaortsr_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '87']); // 1=ทั้งหมด
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('p_sopopaortsr_model');
    }

   

public function index()
    {
        $p_sopopaortsr = $this->p_sopopaortsr_model->list_all();

        foreach ($p_sopopaortsr as $pdf) {
            $pdf->pdf = $this->p_sopopaortsr_model->list_all_pdf($pdf->p_sopopaortsr_id);
        }
        foreach ($p_sopopaortsr as $doc) {
            $doc->doc = $this->p_sopopaortsr_model->list_all_doc($doc->p_sopopaortsr_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_sopopaortsr', ['p_sopopaortsr' => $p_sopopaortsr]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_sopopaortsr_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->p_sopopaortsr_model->add();
        redirect('p_sopopaortsr_backend');
    }


    public function editing($p_sopopaortsr_id)
    {
        $data['rsedit'] = $this->p_sopopaortsr_model->read($p_sopopaortsr_id);
        $data['rsPdf'] = $this->p_sopopaortsr_model->read_pdf($p_sopopaortsr_id);
        $data['rsDoc'] = $this->p_sopopaortsr_model->read_doc($p_sopopaortsr_id);
        $data['rsImg'] = $this->p_sopopaortsr_model->read_img($p_sopopaortsr_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/p_sopopaortsr_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($p_sopopaortsr_id)
    {
        $this->p_sopopaortsr_model->edit($p_sopopaortsr_id);
        redirect('p_sopopaortsr_backend');
    }

    public function update_p_sopopaortsr_status()
    {
        $this->p_sopopaortsr_model->update_p_sopopaortsr_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->p_sopopaortsr_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->p_sopopaortsr_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->p_sopopaortsr_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_p_sopopaortsr($p_sopopaortsr_id)
    {
        $this->p_sopopaortsr_model->del_p_sopopaortsr_img($p_sopopaortsr_id);
        $this->p_sopopaortsr_model->del_p_sopopaortsr_pdf($p_sopopaortsr_id);
        $this->p_sopopaortsr_model->del_p_sopopaortsr_doc($p_sopopaortsr_id);
        $this->p_sopopaortsr_model->del_p_sopopaortsr($p_sopopaortsr_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('p_sopopaortsr_backend');
    }
}
