<?php
defined('BASEPATH') or exit('No direct script access allowed');

class History_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '11']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('history_model');
    }

   

    public function index()
    {
        $history = $this->history_model->list_all();

        foreach ($history as $pdf) {
            $pdf->pdf = $this->history_model->list_all_pdf($pdf->history_id);
        }
        foreach ($history as $img) {
            $img->img = $this->history_model->list_all_img($img->history_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/history', ['history' => $history]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/history_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->history_model->add();
        redirect('history_backend');
    }


    public function editing($history_id)
    {
        $data['rsedit'] = $this->history_model->read($history_id);
        $data['rsPdf'] = $this->history_model->read_pdf($history_id);
        $data['rsImg'] = $this->history_model->read_img($history_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/history_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($history_id)
    {
        $this->history_model->edit($history_id);
        redirect('history_backend');
    }

    public function update_history_status()
    {
        $this->history_model->update_history_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->history_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->history_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }
}