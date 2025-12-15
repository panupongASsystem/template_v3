<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_aditn_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '83']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_aditn_model');
    }

    private function check_session_timeout() {
    $timeout = 900; // 15 นาที
    $last_activity = $this->session->userdata('last_activity');
    
    if ($last_activity && (time() - $last_activity > $timeout)) {
        $this->session->sess_destroy();
        redirect('User/logout', 'refresh');
    } else {
        $this->session->set_userdata('last_activity', time());
    }
}

public function index()
    {
        $operation_aditn = $this->operation_aditn_model->list_all();

        foreach ($operation_aditn as $pdf) {
            $pdf->pdf = $this->operation_aditn_model->list_all_pdf($pdf->operation_aditn_id);
        }
        foreach ($operation_aditn as $doc) {
            $doc->doc = $this->operation_aditn_model->list_all_doc($doc->operation_aditn_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_aditn', ['operation_aditn' => $operation_aditn]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_aditn_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_aditn_model->add();
        redirect('operation_aditn_backend');
    }


    public function editing($operation_aditn_id)
    {
        $data['rsedit'] = $this->operation_aditn_model->read($operation_aditn_id);
        $data['rsPdf'] = $this->operation_aditn_model->read_pdf($operation_aditn_id);
        $data['rsDoc'] = $this->operation_aditn_model->read_doc($operation_aditn_id);
        $data['rsImg'] = $this->operation_aditn_model->read_img($operation_aditn_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_aditn_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_aditn_id)
    {
        $this->operation_aditn_model->edit($operation_aditn_id);
        redirect('operation_aditn_backend');
    }

    public function update_operation_aditn_status()
    {
        $this->operation_aditn_model->update_operation_aditn_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_aditn_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_aditn_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_aditn_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_aditn($operation_aditn_id)
    {
        $this->operation_aditn_model->del_operation_aditn_img($operation_aditn_id);
        $this->operation_aditn_model->del_operation_aditn_pdf($operation_aditn_id);
        $this->operation_aditn_model->del_operation_aditn_doc($operation_aditn_id);
        $this->operation_aditn_model->del_operation_aditn($operation_aditn_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_aditn_backend');
    }
}
