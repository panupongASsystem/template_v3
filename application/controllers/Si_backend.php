<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Si_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '15']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('si_model');
    }

    

    public function index()
    {
        $si = $this->si_model->list_all();

        foreach ($si as $pdf) {
            $pdf->pdf = $this->si_model->list_all_pdf($pdf->si_id);
        }
        foreach ($si as $img) {
            $img->img = $this->si_model->list_all_img($img->si_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/si', ['si' => $si]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/si_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->si_model->add();
        redirect('si_backend');
    }


    public function editing($si_id)
    {
        $data['rsedit'] = $this->si_model->read($si_id);
        $data['rsPdf'] = $this->si_model->read_pdf($si_id);
        $data['rsImg'] = $this->si_model->read_img($si_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/si_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($si_id)
    {
        $this->si_model->edit($si_id);
        redirect('si_backend');
    }

    public function update_si_status()
    {
        $this->si_model->update_si_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->si_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->si_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }
}
