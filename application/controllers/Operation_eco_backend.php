<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_eco_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
         // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
         $this->check_access_permission(['1', '73']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_eco_model');
    }

    

public function index()
    {
        $data['query'] = $this->operation_eco_model->list_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_eco_type', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_type()
    {
        $this->operation_eco_model->add_type();
        redirect('operation_eco_backend');
    }

    public function index_detail($operation_eco_type_id)
    {
        // ดึงข้อมูลหลักทั้งหมด
        $query = $this->operation_eco_model->list_all($operation_eco_type_id);

        // ดึงข้อมูล pdf และ doc ที่เกี่ยวข้องในแต่ละ iteration
        foreach ($query as $files) {
            $files->pdf = $this->operation_eco_model->list_all_pdf($files->operation_eco_id);
            $files->doc = $this->operation_eco_model->list_all_doc($files->operation_eco_id);
        }

        // โหลด views
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_eco', ['query' => $query]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }


    public function adding()
    {
        $data['rs_type'] = $this->operation_eco_model->list_operation_eco_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_eco_form_add', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $operation_eco_ref_id = $this->input->post('operation_eco_ref_id');
        $this->operation_eco_model->add();
        redirect('operation_eco_backend/index_detail/' . $operation_eco_ref_id);
    }


    public function editing($operation_eco_id)
    {
        $data['rs_type'] = $this->operation_eco_model->list_operation_eco_type();

        $data['rsedit'] = $this->operation_eco_model->read($operation_eco_id);
        $data['rsPdf'] = $this->operation_eco_model->read_pdf($operation_eco_id);
        $data['rsDoc'] = $this->operation_eco_model->read_doc($operation_eco_id);
        $data['rsImg'] = $this->operation_eco_model->read_img($operation_eco_id);
        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_eco_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing_type($operation_eco_id)
    {
        $data['rs_type'] = $this->operation_eco_model->read_type($operation_eco_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_eco_type_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_eco_id)
    {
        $operation_eco_ref_id = $this->input->post('operation_eco_ref_id');
        $this->operation_eco_model->edit($operation_eco_id);
        redirect('operation_eco_backend/index_detail/' . $operation_eco_ref_id);
    }

    public function edit_type($operation_eco_type_id)
    {
        $this->operation_eco_model->edit_type($operation_eco_type_id);
        redirect('operation_eco_backend');
    }

    public function toggleUserOperationCdmStatus()
    {
        if ($this->input->post()) {
            $operationCdmId = $this->input->post('operation_eco_id');
            $newStatus = $this->input->post('new_status');

            // ทำการอัพเดตค่าในตาราง tbl_operation_eco ในฐานข้อมูลของคุณ
            $data = array(
                'operation_eco_status' => $newStatus
            );
            $this->db->where('operation_eco_id', $operationCdmId);
            $this->db->update('tbl_operation_eco', $data);

            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            show_404();
        }
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_eco_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_eco_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_eco_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_eco($operation_eco_id)
    {
        $this->operation_eco_model->del_operation_eco_img($operation_eco_id);
        $this->operation_eco_model->del_operation_eco_pdf($operation_eco_id);
        $this->operation_eco_model->del_operation_eco_doc($operation_eco_id);
        $this->operation_eco_model->del_operation_eco($operation_eco_id);
        $this->session->set_flashdata('del_success', TRUE);
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_eco_type($operation_eco_type_id)
    {
        $this->operation_eco_model->del_type_operation_eco_pdf($operation_eco_type_id);
        $this->operation_eco_model->del_type_operation_eco_doc($operation_eco_type_id);
		$this->operation_eco_model->del_type_operation_eco_img($operation_eco_type_id);
        $this->operation_eco_model->del_type_operation_eco($operation_eco_type_id);
        $this->operation_eco_model->del_type_operation_eco_type($operation_eco_type_id);
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_eco_backend');
    }
}
