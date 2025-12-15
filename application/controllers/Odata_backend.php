<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Odata_backend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '55']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('odata_model');

        $this->load->library('upload');
    }

    

public function index()
    {
        $data['query'] = $this->odata_model->list_odata();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/odata', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add_odata()
    {
        $this->odata_model->add_odata();
        redirect('odata_backend', 'refresh');
    }

    public function editing_odata($odata_id)
    {
        $data['rsedit'] = $this->odata_model->read($odata_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/odata_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit_odata($odata_id)
    {
        $this->odata_model->edit_odata($odata_id);
        redirect('odata_backend', 'refresh');
    }

    public function index_odata_sub($odata_id)
    {
        $data['query'] = $this->odata_model->read($odata_id);
        $data['query_odata_sub'] = $this->odata_model->list_all_odata_sub($odata_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/odata_sub', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function add_odata_sub()
    {
        $odata_sub_ref_id = $this->input->post('odata_sub_ref_id');
        $odata_sub_name = $this->input->post('odata_sub_name');

        $this->odata_model->add_odata_sub($odata_sub_ref_id, $odata_sub_name);

        redirect('odata_backend/index_odata_sub/' . $odata_sub_ref_id);
    }

    public function editing_odata_sub($odata_sub_id)
    {
        $data['rsedit'] = $this->odata_model->read_odata_sub($odata_sub_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/odata_sub_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function edit_odata_sub()
    {
        $odata_sub_id = $this->input->post('odata_sub_id');
        $odata_sub_ref_id = $this->input->post('odata_sub_ref_id');
        $odata_sub_name = $this->input->post('odata_sub_name');
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->odata_model->edit_odata_sub($odata_sub_ref_id, $odata_sub_name, $odata_sub_id);

        redirect('odata_backend/index_odata_sub/' . $odata_sub_ref_id);
    }
    public function index_odata_sub_file($odata_sub_id)
    {
        $data['query'] = $this->odata_model->read_odata_sub($odata_sub_id);
        $data['query_odata_sub_file'] = $this->odata_model->list_all_odata_sub_file($odata_sub_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/odata_sub_file', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function add_odata_sub_file()
    {
        // รับค่าไอดีและชื่อจากฟอร์ม
        $odata_sub_file_ref_id = $this->input->post('odata_sub_file_ref_id');
        $odata_sub_file_name = $this->input->post('odata_sub_file_name');

        // ตรวจสอบว่ามีไฟล์ที่อัปโหลดมาหรือไม่
        if (!empty($_FILES['odata_sub_file_doc']['name'])) {
            // ตั้งค่า upload library
            $config['upload_path'] = './docs/file'; // เปลี่ยนเป็นเส้นทางที่เหมาะสมของคุณ
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx'; // ประเภทไฟล์ที่อนุญาต

            // โหลด library upload ด้วย config ที่กำหนด
            $this->upload->initialize($config);

            // ทำการอัปโหลดไฟล์
            if ($this->upload->do_upload('odata_sub_file_doc')) {
                // ถ้าอัปโหลดสำเร็จ
                $data = $this->upload->data();
                $uploaded_filename = $data['file_name'];

                // เรียกใช้ฟังก์ชัน Model เพื่อบันทึกข้อมูลไฟล์
                $this->odata_model->add_odata_sub_file($odata_sub_file_ref_id, $odata_sub_file_name, $uploaded_filename);

                // ตั้งค่า flashdata เพื่อแจ้งเตือนว่าการบันทึกสำเร็จ
                $this->session->set_flashdata('save_success', TRUE);
            } else {
                // ถ้าอัปโหลดล้มเหลว
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('upload_error', $error);
            }
        } else {
            // ถ้าไม่มีไฟล์ที่อัปโหลด
            $this->session->set_flashdata('upload_error', 'No file selected for upload.');
        }

        // เปลี่ยนเส้นทางกลับไปที่หน้าที่กำหนด
        redirect('odata_backend/index_odata_sub_file/' . $odata_sub_file_ref_id);
    }
    public function editing_odata_sub_file($odata_sub_file_id)
    {
        $data['rsedit'] = $this->odata_model->read_odata_sub_file($odata_sub_file_id);

        // echo '<pre>';
        // print_r($data['rsedit']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/odata_sub_file_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
    public function edit_odata_sub_file()
    {
        // รับข้อมูลจากฟอร์ม
        $odata_sub_file_id = $this->input->post('odata_sub_file_id');
        $odata_sub_file_ref_id = $this->input->post('odata_sub_file_ref_id');
        $odata_sub_file_name = $this->input->post('odata_sub_file_name');

        $uploaded_filename = null;

        // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
        if (!empty($_FILES['odata_sub_file_doc']['name'])) {
            // ตั้งค่า upload library
            $config['upload_path'] = './docs/file';
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx';

            // โหลด library upload ด้วย config ที่กำหนด
            $this->upload->initialize($config);

            // ทำการอัปโหลดไฟล์
            if ($this->upload->do_upload('odata_sub_file_doc')) {
                // ถ้าอัปโหลดสำเร็จ
                $data = $this->upload->data();
                $uploaded_filename = $data['file_name'];
            } else {
                // ถ้าอัปโหลดล้มเหลว
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('upload_error', $error);
                redirect('odata_backend/index_odata_sub_file/' . $odata_sub_file_ref_id);
                return;
            }
        }

        // เรียกใช้ฟังก์ชัน Model เพื่ออัปเดตข้อมูล
        $this->odata_model->edit_odata_sub_file($odata_sub_file_id, $odata_sub_file_ref_id, $odata_sub_file_name, $uploaded_filename);

        // เปลี่ยนเส้นทางกลับไปที่หน้าที่กำหนด
        redirect('odata_backend/index_odata_sub_file/' . $odata_sub_file_ref_id);
    }

    public function del_odata_sub_file($odata_sub_file_id)
    {
        $this->odata_model->del_odata_sub_file($odata_sub_file_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function del_odata_sub($odata_sub_id)
    {
        $this->odata_model->del_odata_sub($odata_sub_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function del_odata($odata_id)
    {
        $this->odata_model->del_odata($odata_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect($_SERVER['HTTP_REFERER']);
    }
}
