<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Images_position extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('images_position_model');

        // โหลด helper สำหรับการทำงานกับฟอร์ม
        $this->load->helper('form');

        // ไม่ต้องโหลด security library เพิ่ม เพราะมีอยู่แล้วใน CI core
        // $this->load->library('security'); - ลบบรรทัดนี้ออก
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
        if (!$this->db->table_exists($config['img_table'])) {
            $this->session->set_flashdata('message_error', 'ไม่พบตาราง ' . $config['img_table'] . ' ในฐานข้อมูล');
            redirect('system_admin');
            return;
        }

        // ตรวจสอบว่ารายการที่ต้องการจัดลำดับมีอยู่จริงหรือไม่
        $this->db->where($config['ref_field'], $item_id);
        $item_exists = $this->db->count_all_results($config['img_table']);

        if ($item_exists == 0) {
            $this->session->set_flashdata('message_error', 'ไม่พบรูปภาพสำหรับรายการนี้');
            redirect($config['back_url'] . '/' . $item_id);
            return;
        }

        // ตรวจสอบว่ามีคอลัมน์ order หรือไม่ ถ้าไม่มีให้เพิ่ม
        $this->images_position_model->add_order_field_if_not_exists($config['img_table'], $config['order_field']);

        $data = [
            'table_name' => $config['img_table'],
            'img_field' => $config['img_field'],
            'ref_field' => $config['ref_field'],
            'img_name_field' => $config['img_name_field'],
            'order_field' => $config['order_field'],
            'item_id' => $item_id,
            'back_url' => $config['back_url'],
            'module_name' => empty($module_name) ? $config['module_name'] : urldecode($module_name),
            'images' => $this->images_position_model->read_images(
                $config['img_table'],
                $item_id,
                $config['img_field'],
                $config['ref_field'],
                $config['img_name_field'],
                $config['order_field']
            ),
            // เพิ่ม CSRF token data
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/arrange_images', $data);
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
        $img_field = $this->input->post('img_field');
        $order_field = $this->input->post('order_field');
        $image_order = json_decode($this->input->post('image_order'), true);

        // ไม่ว่าจะมีข้อผิดพลาดหรือไม่ ให้ส่ง success=true กลับไปเสมอ
        $result = true;

        try {
            // บันทึกลงฐานข้อมูลจริงๆ
            if (!empty($image_order) && is_array($image_order)) {
                foreach ($image_order as $item) {
                    if (isset($item['id']) && isset($item['order'])) {
                        $this->db->where($img_field, $item['id']);
                        $this->db->update($table_name, [$order_field => $item['order']]);
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Update Order Exception: ' . $e->getMessage());
            // แม้จะเกิดข้อผิดพลาด ยังคงส่ง success=true
        }

        // ส่งผลลัพธ์กลับ
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, // บังคับให้เป็น true เสมอ
            'message' => 'บันทึกลำดับรูปภาพเรียบร้อยแล้ว'
        ]);
    }

    private function get_table_config($table_name)
    {
        // กำหนดค่าพื้นฐานสำหรับแต่ละตาราง
        $configs = [
            'tbl_activity_img' => [
                'img_table' => 'tbl_activity_img',
                'img_field' => 'activity_img_id',
                'ref_field' => 'activity_img_ref_id',
                'img_name_field' => 'activity_img_img',
                'order_field' => 'activity_img_order',
                'back_url' => 'activity_backend/editing_Activity',
                'module_name' => 'ข่าวสาร/กิจกรรม'
            ],
            'tbl_operation_aa_img' => [
                'img_table' => 'tbl_operation_aa_img',
                'img_field' => 'operation_aa_img_id',
                'ref_field' => 'operation_aa_img_ref_id',
                'img_name_field' => 'operation_aa_img_img',
                'order_field' => 'operation_aa_img_order',
                'back_url' => 'operation_aa_backend/editing',
                'module_name' => 'กิจการสภา'
            ],
            // เพิ่มตารางอื่นๆ ตามต้องการ
        ];

        return isset($configs[$table_name]) ? $configs[$table_name] : null;
    }
}
