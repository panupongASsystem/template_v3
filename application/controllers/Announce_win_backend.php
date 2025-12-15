<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Announce_win_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '131']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('announce_win_model');
    }

    

    public function index()
    {
        $announce_win = $this->announce_win_model->list_all();

        foreach ($announce_win as $pdf) {
            $pdf->pdf = $this->announce_win_model->list_all_pdf($pdf->announce_win_id);
        }
        foreach ($announce_win as $doc) {
            $doc->doc = $this->announce_win_model->list_all_doc($doc->announce_win_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_win', ['announce_win' => $announce_win]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_win_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->announce_win_model->add();
        redirect('announce_win_backend');
    }


    public function editing($announce_win_id)
    {
        $data['rsedit'] = $this->announce_win_model->read($announce_win_id);
        $data['rsPdf'] = $this->announce_win_model->read_pdf($announce_win_id);
        $data['rsDoc'] = $this->announce_win_model->read_doc($announce_win_id);
        $data['rsImg'] = $this->announce_win_model->read_img($announce_win_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/announce_win_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($announce_win_id)
    {
        $this->announce_win_model->edit($announce_win_id);
        redirect('announce_win_backend');
    }

    public function update_announce_win_status()
    {
        $this->announce_win_model->update_announce_win_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->announce_win_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->announce_win_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->announce_win_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_announce_win($announce_win_id)
    {
        $this->announce_win_model->del_announce_win_img($announce_win_id);
        $this->announce_win_model->del_announce_win_pdf($announce_win_id);
        $this->announce_win_model->del_announce_win_doc($announce_win_id);
        $this->announce_win_model->del_announce_win($announce_win_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('announce_win_backend');
    }
}
