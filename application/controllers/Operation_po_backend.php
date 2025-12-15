<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_po_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
          // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
          $this->check_access_permission(['1', '71']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_po_model');
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
        $operation_po = $this->operation_po_model->list_all();

        foreach ($operation_po as $pdf) {
            $pdf->pdf = $this->operation_po_model->list_all_pdf($pdf->operation_po_id);
        }
        foreach ($operation_po as $doc) {
            $doc->doc = $this->operation_po_model->list_all_doc($doc->operation_po_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_po', ['operation_po' => $operation_po]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_po_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_po_model->add();
        redirect('operation_po_backend');
    }


    public function editing($operation_po_id)
    {
        $data['rsedit'] = $this->operation_po_model->read($operation_po_id);
        $data['rsPdf'] = $this->operation_po_model->read_pdf($operation_po_id);
        $data['rsDoc'] = $this->operation_po_model->read_doc($operation_po_id);
        $data['rsImg'] = $this->operation_po_model->read_img($operation_po_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_po_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_po_id)
    {
        $this->operation_po_model->edit($operation_po_id);
        redirect('operation_po_backend');
    }

    public function update_operation_po_status()
    {
        $this->operation_po_model->update_operation_po_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_po_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_po_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_po_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_po($operation_po_id)
    {
        $this->operation_po_model->del_operation_po_img($operation_po_id);
        $this->operation_po_model->del_operation_po_pdf($operation_po_id);
        $this->operation_po_model->del_operation_po_doc($operation_po_id);
        $this->operation_po_model->del_operation_po($operation_po_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_po_backend');
    }
}
