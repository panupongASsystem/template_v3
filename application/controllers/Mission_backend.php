<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mission_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '14']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('mission_model');
    }

    

    public function index()
    {
        $mission = $this->mission_model->list_all();

        foreach ($mission as $pdf) {
            $pdf->pdf = $this->mission_model->list_all_pdf($pdf->mission_id);
        }
        foreach ($mission as $img) {
            $img->img = $this->mission_model->list_all_img($img->mission_id);
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/mission', ['mission' => $mission]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/mission_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->mission_model->add();
        redirect('mission_backend');
    }


    public function editing($mission_id)
    {
        $data['rsedit'] = $this->mission_model->read($mission_id);
        $data['rsPdf'] = $this->mission_model->read_pdf($mission_id);
        $data['rsImg'] = $this->mission_model->read_img($mission_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/mission_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($mission_id)
    {
        $this->mission_model->edit($mission_id);
        redirect('mission_backend');
    }

    public function update_mission_status()
    {
        $this->mission_model->update_mission_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->mission_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->mission_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        redirect($_SERVER['HTTP_REFERER']);
    }
}
