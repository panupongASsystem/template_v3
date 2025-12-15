<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vision_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '17']); // 1=ทั้งหมด
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('vision_model');
    }
	
	 

public function index()
{
    $vision = $this->vision_model->list_all();

    foreach ($vision as $pdf) {
        $pdf->pdf = $this->vision_model->list_all_pdf($pdf->vision_id);
    }
    foreach ($vision as $img) {
        $img->img = $this->vision_model->list_all_img($img->vision_id);
    }

    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/vision', ['vision' => $vision]);
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function adding()
{
    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/vision_form_add');
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function add()
{
    $this->vision_model->add();
    redirect('vision_backend');
}


public function editing($vision_id)
{
    $data['rsedit'] = $this->vision_model->read($vision_id);
    $data['rsPdf'] = $this->vision_model->read_pdf($vision_id);
    $data['rsImg'] = $this->vision_model->read_img($vision_id);
    // echo '<pre>';
    // print_r($data['rsfile']);
    // echo '</pre>';
    // exit();

    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/vision_form_edit', $data);
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function edit($vision_id)
{
    $this->vision_model->edit($vision_id);
    redirect('vision_backend');
}

public function update_vision_status()
{
    $this->vision_model->update_vision_status();
}

public function del_pdf($pdf_id)
{
    // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
    $this->vision_model->del_pdf($pdf_id);

    // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
    redirect($_SERVER['HTTP_REFERER']);
}

public function del_img($file_id)
{
    // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
    $this->vision_model->del_img($file_id);

    // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
    redirect($_SERVER['HTTP_REFERER']);
}
}