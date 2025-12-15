<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Announce_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
          // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
           $this->check_access_permission(['1', '92']); // 1=ทั้งหมด
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('announce_model');
    }
	
    
    public function index()
    {
        $announce = $this->announce_model->list_all();

        foreach ($announce as $pdf) {
            $pdf->pdf = $this->announce_model->list_all_pdf($pdf->announce_id);
        }
        foreach ($announce as $doc) {
            $doc->doc = $this->announce_model->list_all_doc($doc->announce_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce', ['announce' => $announce]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->announce_model->add();
        redirect('announce_backend');
    }


    public function editing($announce_id)
    {
        $data['rsedit'] = $this->announce_model->read($announce_id);
        $data['rsPdf'] = $this->announce_model->read_pdf($announce_id);
        $data['rsDoc'] = $this->announce_model->read_doc($announce_id);
        $data['rsImg'] = $this->announce_model->read_img($announce_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($announce_id)
    {
        $this->announce_model->edit($announce_id);
        redirect('announce_backend');
    }

    public function update_announce_status()
    {
        $this->announce_model->update_announce_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->announce_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->announce_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->announce_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_announce($announce_id)
    {
        $this->announce_model->del_announce_img($announce_id);
        $this->announce_model->del_announce_pdf($announce_id);
        $this->announce_model->del_announce_doc($announce_id);
        $this->announce_model->del_announce($announce_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('announce_backend');
    }
}
