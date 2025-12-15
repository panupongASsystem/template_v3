<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Finance_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '118']); // 1=ทั้งหมด
		
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('finance_model');
    }

    public function index()
    {
        $data['query'] = $this->finance_model->list_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/finance_type', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_type()
    {
        $this->finance_model->add_type();
        redirect('finance_backend');
    }

    public function index_detail($finance_type_id)
    {
        // ดึงข้อมูลหลักทั้งหมด
        $query = $this->finance_model->list_all($finance_type_id);

        // ดึงข้อมูล pdf และ doc ที่เกี่ยวข้องในแต่ละ iteration
        foreach ($query as $files) {
            $files->pdf = $this->finance_model->list_all_pdf($files->finance_id);
            $files->doc = $this->finance_model->list_all_doc($files->finance_id);
        }

        // โหลด views
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/finance', ['query' => $query]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }


    public function adding()
    {
        $data['rs_type'] = $this->finance_model->list_finance_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/finance_form_add', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $finance_ref_id = $this->input->post('finance_ref_id');
        $this->finance_model->add();
        redirect('finance_backend/index_detail/' . $finance_ref_id);
    }


    public function editing($finance_id)
    {
        $data['rs_type'] = $this->finance_model->list_finance_type();

        $data['rsedit'] = $this->finance_model->read($finance_id);
        $data['rsPdf'] = $this->finance_model->read_pdf($finance_id);
        $data['rsDoc'] = $this->finance_model->read_doc($finance_id);
        $data['rsImg'] = $this->finance_model->read_img($finance_id);
        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/finance_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing_type($finance_id)
    {
        $data['rs_type'] = $this->finance_model->read_type($finance_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/finance_type_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($finance_id)
    {
        $finance_ref_id = $this->input->post('finance_ref_id');
        $this->finance_model->edit($finance_id);
        redirect('finance_backend/index_detail/' . $finance_ref_id);
    }

    public function edit_type($finance_type_id)
    {
        $this->finance_model->edit_type($finance_type_id);
        redirect('finance_backend');
    }

    public function toggleUserOperationCdmStatus()
    {
        if ($this->input->post()) {
            $operationCdmId = $this->input->post('finance_id');
            $newStatus = $this->input->post('new_status');

            // ทำการอัพเดตค่าในตาราง tbl_finance ในฐานข้อมูลของคุณ
            $data = array(
                'finance_status' => $newStatus
            );
            $this->db->where('finance_id', $operationCdmId);
            $this->db->update('tbl_finance', $data);

            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            show_404();
        }
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->finance_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->finance_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->finance_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_finance($finance_id)
    {
        $this->finance_model->del_finance_img($finance_id);
        $this->finance_model->del_finance_pdf($finance_id);
        $this->finance_model->del_finance_doc($finance_id);
        $this->finance_model->del_finance($finance_id);
        // $this->session->set_flashdata('del_success', TRUE);
        echo '<script>window.history.back();</script>';
    }

    public function del_finance_type($finance_type_id)
    {
        // ลบไฟล์และข้อมูลที่เกี่ยวข้องตามลำดับ
        $this->finance_model->del_type_finance_pdf($finance_type_id);
        $this->finance_model->del_type_finance_doc($finance_type_id);
        $this->finance_model->del_type_finance_img($finance_type_id);
        $this->finance_model->del_type_finance($finance_type_id);
        $this->finance_model->del_type_finance_type($finance_type_id);

        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
        redirect('finance_backend');
    }
}
