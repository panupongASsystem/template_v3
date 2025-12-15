<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Taepts_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '119']); // 1=ทั้งหมด
		
		
        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('taepts_model');
    }

    public function index()
    {
        $data['query'] = $this->taepts_model->list_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/taepts_type', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_type()
    {
        $this->taepts_model->add_type();
        redirect('taepts_backend');
    }

    public function index_detail($taepts_type_id)
    {
        // ดึงข้อมูลหลักทั้งหมด
        $query = $this->taepts_model->list_all($taepts_type_id);

        // ดึงข้อมูล pdf และ doc ที่เกี่ยวข้องในแต่ละ iteration
        foreach ($query as $files) {
            $files->pdf = $this->taepts_model->list_all_pdf($files->taepts_id);
            $files->doc = $this->taepts_model->list_all_doc($files->taepts_id);
        }

        // โหลด views
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/taepts', ['query' => $query]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }


    public function adding()
    {
        $data['rs_type'] = $this->taepts_model->list_taepts_type();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/taepts_form_add', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $taepts_ref_id = $this->input->post('taepts_ref_id');
        $this->taepts_model->add();
        redirect('taepts_backend/index_detail/' . $taepts_ref_id);
    }


    public function editing($taepts_id)
    {
        $data['rs_type'] = $this->taepts_model->list_taepts_type();

        $data['rsedit'] = $this->taepts_model->read($taepts_id);
        $data['rsPdf'] = $this->taepts_model->read_pdf($taepts_id);
        $data['rsDoc'] = $this->taepts_model->read_doc($taepts_id);
        $data['rsImg'] = $this->taepts_model->read_img($taepts_id);
        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/taepts_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function editing_type($taepts_id)
    {
        $data['rs_type'] = $this->taepts_model->read_type($taepts_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/taepts_type_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($taepts_id)
    {
        $taepts_ref_id = $this->input->post('taepts_ref_id');
        $this->taepts_model->edit($taepts_id);
        redirect('taepts_backend/index_detail/' . $taepts_ref_id);
    }

    public function edit_type($taepts_type_id)
    {
        $this->taepts_model->edit_type($taepts_type_id);
        redirect('taepts_backend');
    }

    public function toggleUserOperationCdmStatus()
    {
        if ($this->input->post()) {
            $operationCdmId = $this->input->post('taepts_id');
            $newStatus = $this->input->post('new_status');

            // ทำการอัพเดตค่าในตาราง tbl_taepts ในฐานข้อมูลของคุณ
            $data = array(
                'taepts_status' => $newStatus
            );
            $this->db->where('taepts_id', $operationCdmId);
            $this->db->update('tbl_taepts', $data);

            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
        } else {
            show_404();
        }
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->taepts_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->taepts_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->taepts_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_taepts($taepts_id)
    {
        $this->taepts_model->del_taepts_img($taepts_id);
        $this->taepts_model->del_taepts_pdf($taepts_id);
        $this->taepts_model->del_taepts_doc($taepts_id);
        $this->taepts_model->del_taepts($taepts_id);
        // $this->session->set_flashdata('del_success', TRUE);
        echo '<script>window.history.back();</script>';
    }

    public function del_taepts_type($taepts_type_id)
    {
        $this->taepts_model->del_type_taepts_pdf($taepts_type_id);
        $this->taepts_model->del_type_taepts_doc($taepts_type_id);
		$this->taepts_model->del_type_taepts_img($taepts_type_id);
        $this->taepts_model->del_type_taepts($taepts_type_id);
        $this->taepts_model->del_type_taepts_type($taepts_type_id);
        $this->space_model->update_server_current();
        $this->session->set_flashdata('del_success', TRUE);
        redirect('taepts_backend');
    }
}
