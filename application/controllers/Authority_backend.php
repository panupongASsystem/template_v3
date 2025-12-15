<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authority_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '16']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('authority_model');
    }

   

    public function index()
    {
        $authority = $this->authority_model->list_all();

        foreach ($authority as $pdf) {
            $pdf->pdf = $this->authority_model->list_all_pdf($pdf->authority_id);
        }
        foreach ($authority as $img) {
            $img->img = $this->authority_model->list_all_img($img->authority_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/authority', ['authority' => $authority]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/authority_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->authority_model->add();
        redirect('authority_backend');
    }


    public function editing($authority_id)
    {
        $data['rsedit'] = $this->authority_model->read($authority_id);
        $data['rsPdf'] = $this->authority_model->read_pdf($authority_id);
        $data['rsImg'] = $this->authority_model->read_img($authority_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/authority_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($authority_id)
    {
        $this->authority_model->edit($authority_id);
        redirect('authority_backend');
    }

    public function update_authority_status()
    {
        $this->authority_model->update_authority_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->authority_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->authority_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }
}
