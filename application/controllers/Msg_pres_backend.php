<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Msg_pres_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
       // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
		$this->check_access_permission(['1', '8']); // 1=ทั้งหมด
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('msg_pres_model');
    }
	
	  

    public function index()
    {
        $msg_pres = $this->msg_pres_model->list_all();

        foreach ($msg_pres as $pdf) {
            $pdf->pdf = $this->msg_pres_model->list_all_pdf($pdf->msg_pres_id);
        }
        foreach ($msg_pres as $img) {
            $img->img = $this->msg_pres_model->list_all_img($img->msg_pres_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/msg_pres', ['msg_pres' => $msg_pres]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/msg_pres_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->msg_pres_model->add();
        redirect('msg_pres_backend');
    }


    public function editing($msg_pres_id)
    {
        $data['rsedit'] = $this->msg_pres_model->read($msg_pres_id);
        $data['rsPdf'] = $this->msg_pres_model->read_pdf($msg_pres_id);
        $data['rsImg'] = $this->msg_pres_model->read_img($msg_pres_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/msg_pres_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($msg_pres_id)
    {
        $this->msg_pres_model->edit($msg_pres_id);
        redirect('msg_pres_backend');
    }

    public function update_msg_pres_status()
    {
        $this->msg_pres_model->update_msg_pres_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->msg_pres_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->msg_pres_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }
}
