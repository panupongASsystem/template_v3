<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gci_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '13']); // 1=ทั้งหมด


        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('gci_model');
    }

   

    public function index()
    {
        $gci = $this->gci_model->list_all();

        foreach ($gci as $pdf) {
            $pdf->pdf = $this->gci_model->list_all_pdf($pdf->gci_id);
        }
        foreach ($gci as $img) {
            $img->img = $this->gci_model->list_all_img($img->gci_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/gci', ['gci' => $gci]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/gci_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->gci_model->add();
        redirect('gci_backend');
    }


    public function editing($gci_id)
    {
        $data['rsedit'] = $this->gci_model->read($gci_id);
        $data['rsPdf'] = $this->gci_model->read_pdf($gci_id);
        $data['rsImg'] = $this->gci_model->read_img($gci_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/gci_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($gci_id)
    {
        $this->gci_model->edit($gci_id);
        redirect('gci_backend');
    }

    public function update_gci_status()
    {
        $this->gci_model->update_gci_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->gci_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->gci_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }
}
