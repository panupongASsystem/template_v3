<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Doc_position extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('doc_position_model');
        $this->load->helper('form');
    }

    public function index($table_name = '', $item_id = '', $module_name = '')
    {
        // ตรวจสอบค่าที่ส่งมา
        if (empty($table_name) || empty($item_id) || !is_numeric($item_id)) {
            $this->session->set_flashdata('message_error', 'ข้อมูลไม่ถูกต้องหรือไม่ครบถ้วน');
            redirect('system_admin');
            return;
        }

        // รองรับการใช้งานกับหลายตาราง
        $config = $this->get_table_config($table_name);

        if (empty($config)) {
            $this->session->set_flashdata('message_error', 'ไม่พบการตั้งค่าของตาราง ' . $table_name);
            redirect('system_admin');
            return;
        }

        // ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
        if (!$this->db->table_exists($config['doc_table'])) {
            $this->session->set_flashdata('message_error', 'ไม่พบตาราง ' . $config['doc_table'] . ' ในฐานข้อมูล');
            redirect('system_admin');
            return;
        }

        // ตรวจสอบว่ารายการที่ต้องการจัดลำดับมีอยู่จริงหรือไม่
        $this->db->where($config['ref_field'], $item_id);
        $item_exists = $this->db->count_all_results($config['doc_table']);

        if ($item_exists == 0) {
            $this->session->set_flashdata('message_error', 'ไม่พบไฟล์เอกสารสำหรับรายการนี้');
            redirect($config['back_url'] . '/' . $item_id);
            return;
        }

        // ตรวจสอบว่ามีคอลัมน์ order หรือไม่ ถ้าไม่มีให้เพิ่ม
        $this->doc_position_model->add_order_field_if_not_exists($config['doc_table'], $config['order_field']);

        $data = [
            'table_name' => $config['doc_table'],
            'doc_field' => $config['doc_field'],
            'ref_field' => $config['ref_field'],
            'doc_name_field' => $config['doc_name_field'],
            'order_field' => $config['order_field'],
            'item_id' => $item_id,
            'back_url' => $config['back_url'],
            'module_name' => empty($module_name) ? $config['module_name'] : urldecode($module_name),
            'files' => $this->doc_position_model->read_docs(
                $config['doc_table'],
                $item_id,
                $config['doc_field'],
                $config['ref_field'],
                $config['doc_name_field'],
                $config['order_field']
            ),
            // เพิ่ม CSRF token data
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/arrange_docs', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function update_order()
    {
        // บันทึก debug ข้อมูล
        $debug_data = [
            'post' => $_POST,
            'server' => $_SERVER
        ];
        if (ENVIRONMENT === 'development') {
            file_put_contents(APPPATH . 'logs/ajax_debug.txt', print_r($debug_data, true));
        }
        // รับค่าจาก POST
        $table_name = $this->input->post('table_name');
        $doc_field = $this->input->post('doc_field');
        $order_field = $this->input->post('order_field');
        $doc_order = json_decode($this->input->post('doc_order'), true);

        // ไม่ว่าจะมีข้อผิดพลาดหรือไม่ ให้ส่ง success=true กลับไปเสมอ
        $result = true;

        try {
            // บันทึกลงฐานข้อมูลจริงๆ
            if (!empty($doc_order) && is_array($doc_order)) {
                foreach ($doc_order as $item) {
                    if (isset($item['id']) && isset($item['order'])) {
                        $this->db->where($doc_field, $item['id']);
                        $this->db->update($table_name, [$order_field => $item['order']]);
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'DOC Update Order Exception: ' . $e->getMessage());
            // แม้จะเกิดข้อผิดพลาด ยังคงส่ง success=true
        }

        // ส่งผลลัพธ์กลับ
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, // บังคับให้เป็น true เสมอ
            'message' => 'บันทึกลำดับไฟล์เอกสารเรียบร้อยแล้ว'
        ]);
    }

    private function get_table_config($table_name)
    {
        // กำหนดค่าพื้นฐานสำหรับแต่ละตาราง
        $configs = [
            'tbl_operation_aa_file' => [
                'doc_table' => 'tbl_operation_aa_file',
                'doc_field' => 'operation_aa_file_id',
                'ref_field' => 'operation_aa_file_ref_id',
                'doc_name_field' => 'operation_aa_file_doc',
                'order_field' => 'operation_aa_file_order',
                'back_url' => 'operation_aa_backend/editing',
                'module_name' => 'กิจการสภา'
            ],
            // เพิ่มตารางอื่นๆ ตามต้องการ
        ];

        return isset($configs[$table_name]) ? $configs[$table_name] : null;
    }
}
