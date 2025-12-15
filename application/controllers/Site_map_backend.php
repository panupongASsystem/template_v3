<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Site_map_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '25']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('site_map_model');
    }

   

public function index()
{
    $site_map = $this->site_map_model->list_all();

    foreach ($site_map as $pdf) {
        $pdf->pdf = $this->site_map_model->list_all_pdf($pdf->site_map_id);
    }
    foreach ($site_map as $img) {
        $img->img = $this->site_map_model->list_all_img($img->site_map_id);
    }


    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/site_map', ['site_map' => $site_map]);
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function adding()
{
    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/site_map_form_add');
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function add()
{
    $this->site_map_model->add();
    redirect('site_map_backend');
}


public function editing($site_map_id)
{
    $data['rsedit'] = $this->site_map_model->read($site_map_id);
    $data['rsPdf'] = $this->site_map_model->read_pdf($site_map_id);
    $data['rsImg'] = $this->site_map_model->read_img($site_map_id);
    // echo '<pre>';
    // print_r($data['rsfile']);
    // echo '</pre>';
    // exit();

    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/site_map_form_edit', $data);
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function edit($site_map_id)
{
    $this->site_map_model->edit($site_map_id);
    redirect('site_map_backend');
}

public function update_site_map_status()
{
    $this->site_map_model->update_site_map_status();
}

public function del_pdf($pdf_id)
{
    // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
    $this->site_map_model->del_pdf($pdf_id);

    // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
    echo '<script>window.history.back();</script>';
}

public function del_doc($doc_id)
{
    // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
    $this->site_map_model->del_doc($doc_id);

    // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
    echo '<script>window.history.back();</script>';
}

public function del_img($file_id)
{
    // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
    $this->site_map_model->del_img($file_id);

    // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
    echo '<script>window.history.back();</script>';
}

public function del_site_map($site_map_id)
{
    $this->site_map_model->del_site_map_img($site_map_id);
    $this->site_map_model->del_site_map_pdf($site_map_id);
    $this->site_map_model->del_site_map($site_map_id);
    $this->session->set_flashdata('del_success', TRUE);
    redirect('site_map_backend');
}
}
