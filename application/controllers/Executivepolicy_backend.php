<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Executivepolicy_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
               $this->check_access_permission(['1', '20']); // 1=ทั้งหมด

		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('executivepolicy_model');
    }
	
	 

public function index()
{
    $executivepolicy = $this->executivepolicy_model->list_all();

    foreach ($executivepolicy as $pdf) {
        $pdf->pdf = $this->executivepolicy_model->list_all_pdf($pdf->executivepolicy_id);
    }
    foreach ($executivepolicy as $img) {
        $img->img = $this->executivepolicy_model->list_all_img($img->executivepolicy_id);
    }

    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/executivepolicy', ['executivepolicy' => $executivepolicy]);
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function adding()
{
    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/executivepolicy_form_add');
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function add()
{
    $this->executivepolicy_model->add();
    redirect('executivepolicy_backend');
}


public function editing($executivepolicy_id)
{
    $data['rsedit'] = $this->executivepolicy_model->read($executivepolicy_id);
    $data['rsPdf'] = $this->executivepolicy_model->read_pdf($executivepolicy_id);
    $data['rsImg'] = $this->executivepolicy_model->read_img($executivepolicy_id);
    // echo '<pre>';
    // print_r($data['rsfile']);
    // echo '</pre>';
    // exit();

    $this->load->view('templat/header');
    $this->load->view('asset/css');
    $this->load->view('templat/navbar_system_admin');
    $this->load->view('system_admin/executivepolicy_form_edit', $data);
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}

public function edit($executivepolicy_id)
{
    $this->executivepolicy_model->edit($executivepolicy_id);
    redirect('executivepolicy_backend');
}

public function update_executivepolicy_status()
{
    $this->executivepolicy_model->update_executivepolicy_status();
}

public function del_pdf($pdf_id)
{
    // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
    $this->executivepolicy_model->del_pdf($pdf_id);

    // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
    redirect($_SERVER['HTTP_REFERER']);
}

public function del_img($file_id)
{
    // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
    $this->executivepolicy_model->del_img($file_id);

    // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
    redirect($_SERVER['HTTP_REFERER']);
}
}