<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operation_aa_backend extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        $this->check_access_permission(['1', '82']); // 1=ทั้งหมด

        $this->load->model('member_model');
        $this->load->model('space_model');
        $this->load->model('operation_aa_model');
    }

    

    public function index()
    {
        $operation_aa = $this->operation_aa_model->list_all();

        foreach ($operation_aa as $pdf) {
            $pdf->pdf = $this->operation_aa_model->list_all_pdf($pdf->operation_aa_id);
        }
        foreach ($operation_aa as $doc) {
            $doc->doc = $this->operation_aa_model->list_all_doc($doc->operation_aa_id);
        }


        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_aa', ['operation_aa' => $operation_aa]);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function adding()
    {
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_aa_form_add');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function add()
    {
        $this->operation_aa_model->add();
        redirect('operation_aa_backend');
    }


    public function editing($operation_aa_id)
    {
        $data['rsedit'] = $this->operation_aa_model->read($operation_aa_id);
        $data['rsPdf'] = $this->operation_aa_model->read_pdf($operation_aa_id);
        $data['rsDoc'] = $this->operation_aa_model->read_doc($operation_aa_id);
        $data['rsImg'] = $this->operation_aa_model->read_img($operation_aa_id);
        // echo '<pre>';
        // print_r($data['rsfile']);
        // echo '</pre>';
        // exit();

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/operation_aa_form_edit', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function edit($operation_aa_id)
    {
        $this->operation_aa_model->edit($operation_aa_id);
        redirect('operation_aa_backend');
    }

    public function update_operation_aa_status()
    {
        $this->operation_aa_model->update_operation_aa_status();
    }

    public function del_pdf($pdf_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $pdf_id
        $this->operation_aa_model->del_pdf($pdf_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_doc($doc_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $doc_id
        $this->operation_aa_model->del_doc($doc_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_img($file_id)
    {
        // เรียกใช้ฟังก์ชันใน Model เพื่อลบไฟล์ PDF ด้วย $file_id
        $this->operation_aa_model->del_img($file_id);

        // ใส่สคริปต์ JavaScript เพื่อรีเฟรชหน้าเดิม
        echo '<script>window.history.back();</script>';
    }

    public function del_operation_aa($operation_aa_id)
    {
        $this->operation_aa_model->del_operation_aa_img($operation_aa_id);
        $this->operation_aa_model->del_operation_aa_pdf($operation_aa_id);
        $this->operation_aa_model->del_operation_aa_doc($operation_aa_id);
        $this->operation_aa_model->del_operation_aa($operation_aa_id);
        $this->session->set_flashdata('del_success', TRUE);
        redirect('operation_aa_backend');
    }

    public function delete_multiple_images()
    {
        // รับข้อมูล JSON จาก request
        $json_input = file_get_contents('php://input');
        $json_data = json_decode($json_input, true);

        // Debug: บันทึกข้อมูลที่ได้รับ
        log_message('debug', 'Received data for delete_multiple_images: ' . print_r($json_data, true));

        // ตรวจสอบว่ามีข้อมูลที่จำเป็นหรือไม่
        if (!isset($json_data['image_ids']) || empty($json_data['image_ids'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรูปภาพที่ต้องการลบ'
            ]);
            return;
        }

        $image_ids = $json_data['image_ids'];
        $operation_aa_id = isset($json_data['operation_aa_id']) ? $json_data['operation_aa_id'] : null;

        // Debug: แสดงข้อมูล IDs ที่จะลบ
        log_message('debug', 'Image IDs to delete: ' . implode(', ', $image_ids));
        log_message('debug', 'Operation AA ID: ' . $operation_aa_id);

        // ดำเนินการลบรูปภาพ
        $deleted_count = 0;

        // เริ่ม Transaction
        $this->db->trans_start();

        foreach ($image_ids as $img_id) {
            // ดึงข้อมูลรูปภาพ
            $this->db->select('operation_aa_img_img');
            $this->db->where('operation_aa_img_id', $img_id);
            $query = $this->db->get('tbl_operation_aa_img');
            $img_data = $query->row();

            if ($img_data) {
                // ลบไฟล์รูปภาพ
                $img_path = './docs/img/' . $img_data->operation_aa_img_img;
                if (file_exists($img_path)) {
                    unlink($img_path);
                }

                // ลบข้อมูลจากฐานข้อมูล
                $this->db->where('operation_aa_img_id', $img_id);
                $this->db->delete('tbl_operation_aa_img');

                $deleted_count++;
            }
        }

        // อัพเดตข้อมูลพื้นที่เก็บข้อมูล
        $this->space_model->update_server_current();

        // สิ้นสุด Transaction
        $this->db->trans_complete();

        $response = [];

        if ($this->db->trans_status() === FALSE) {
            $response = [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบรูปภาพ'
            ];
        } else {
            $response = [
                'success' => true,
                'message' => 'ลบรูปภาพจำนวน ' . $deleted_count . ' รูปเรียบร้อยแล้ว',
                'count' => $deleted_count
            ];
        }

        echo json_encode($response);
    }
}
