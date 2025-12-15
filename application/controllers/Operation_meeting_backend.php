<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_meeting_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '116']); // 1=ทั้งหมด
		
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_meeting_model');
    }

    public function index()
    {
        $data['query'] = $this->operation_meeting_model->list_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_meeting_type', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_type()
    {
        $this->operation_meeting_model->add_type();
        redirect('operation_meeting_backend');
    }

    public function index_detail($operation_meeting_type_id)
    {
        // ดึงข้อมูลหลักทั้งหมด
        $query = $this->operation_meeting_model->list_all($operation_meeting_type_id);

        // ดึงข้อมูล pdf และ doc ที่เกี่ยวข้องในแต่ละ iteration
        foreach ($query as $files) {
            $files->pdf = $this->operation_meeting_model->list_all_pdf($files->operation_meeting_id);
            $files->doc = $this->operation_meeting_model->list_all_doc($files->operation_meeting_id);
        }

        // โหลด views
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_meeting', ['query' => $query]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }


    public function adding()
    {
        $data['rs_type'] = $this->operation_meeting_model->list_operation_meeting_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_meeting_form_add', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $operation_meeting_ref_id = $this->input->post('operation_meeting_ref_id');
        $this->operation_meeting_model->add();
        redirect('operation_meeting_backend/index_detail/' . $operation_meeting_ref_id);
    }


    public function editing($operation_meeting_id)
    {
        $data['rs_type'] = $this->operation_meeting_model->list_operation_meeting_type();

        $data['rsedit'] = $this->operation_meeting_model->read($operation_meeting_id);
        $data['rsPdf'] = $this->operation_meeting_model->read_pdf($operation_meeting_id);
        $data['rsDoc'] = $this->operation_meeting_model->read_doc($operation_meeting_id);
        $data['rsImg'] = $this->operation_meeting_model->read_img($operation_meeting_id);
        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_meeting_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing_type($operation_meeting_id)
    {
        $data['rs_type'] = $this->operation_meeting_model->read_type($operation_meeting_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_meeting_type_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_meeting_id)
    {
        $operation_meeting_ref_id = $this->input->post('operation_meeting_ref_id');
        $this->operation_meeting_model->edit($operation_meeting_id);
        redirect('operation_meeting_backend/index_detail/' . $operation_meeting_ref_id);
    }

    public function edit_type($operation_meeting_type_id)
    {
        $this->operation_meeting_model->edit_type($operation_meeting_type_id);
        redirect('operation_meeting_backend');
    }

    public function toggleUserOperationCdmStatus()
    {
        if ($this->input->post()) {
            $operationCdmId = $this->input->post('operation_meeting_id');
            $newStatus = $this->input->post('new_status');

            // ทำการอัพเดตค่าในตาราง tbl_operation_meeting ในฐานข้อมูลของคุณ
            $data = array(
                'operation_meeting_status' => $newStatus
            );
            $this->db->where('operation_meeting_id', $operationCdmId);
            $this->db->update('tbl_operation_meeting', $data);

            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            show_404();
        }
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_meeting_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_meeting_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_meeting_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_meeting($operation_meeting_id)
    {
        $this->operation_meeting_model->del_operation_meeting_img($operation_meeting_id);
        $this->operation_meeting_model->del_operation_meeting_pdf($operation_meeting_id);
        $this->operation_meeting_model->del_operation_meeting_doc($operation_meeting_id);
        $this->operation_meeting_model->del_operation_meeting($operation_meeting_id);
        // $this->session->set_flashdata('del_success', TRUE);
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_meeting_type($operation_meeting_type_id)
    {
        $this->operation_meeting_model->del_type_operation_meeting_pdf($operation_meeting_type_id);
        $this->operation_meeting_model->del_type_operation_meeting_doc($operation_meeting_type_id);
        $this->operation_meeting_model->del_type_operation_meeting($operation_meeting_type_id);
        $this->operation_meeting_model->del_type_operation_meeting_type($operation_meeting_type_id);
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_meeting_backend');
    }
}
