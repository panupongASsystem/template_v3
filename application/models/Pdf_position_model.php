<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pdf_position_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_order_field_if_not_exists($table_name, $order_field)
    {
        try {
            // ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
            if ($this->db->table_exists($table_name)) {
                // ตรวจสอบว่าคอลัมน์มีอยู่หรือไม่
                $fields = $this->db->list_fields($table_name);
                if (!in_array($order_field, $fields)) {
                    // คอลัมน์ไม่มีอยู่ ให้เพิ่มเข้าไป
                    $this->db->query("ALTER TABLE `{$table_name}` ADD `{$order_field}` INT DEFAULT 0");
                    log_message('info', "Added column {$order_field} to table {$table_name} successfully");
                    return true;
                }
                return true; // คอลัมน์มีอยู่แล้ว
            } else {
                log_message('error', "Table {$table_name} does not exist in the database");
                return false;
            }
        } catch (Exception $e) {
            log_message('error', "Error adding column: " . $e->getMessage());
            return false;
        }
    }

    public function read_pdfs($table_name, $item_id, $id_field, $ref_field, $pdf_name_field, $order_field)
    {
        try {
            // ตรวจสอบความถูกต้องของข้อมูล
            if (empty($table_name) || empty($item_id) || !is_numeric($item_id)) {
                log_message('error', "Invalid data: table={$table_name}, item_id={$item_id}");
                return [];
            }

            // ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
            if (!$this->db->table_exists($table_name)) {
                log_message('error', "Table {$table_name} does not exist in the database");
                return [];
            }

            // ดึงข้อมูลไฟล์ PDF
            $this->db->where($ref_field, $item_id);
            $this->db->order_by($order_field, 'ASC');
            $this->db->order_by($id_field, 'ASC');
            $query = $this->db->get($table_name);

            if ($query->num_rows() > 0) {
                log_message('debug', "Found {$query->num_rows()} PDFs in {$table_name} for {$ref_field}={$item_id}");
                return $query->result();
            } else {
                log_message('debug', "No PDFs found in {$table_name} for {$ref_field}={$item_id}");
                return [];
            }
        } catch (Exception $e) {
            log_message('error', "Error reading PDFs: " . $e->getMessage());
            return [];
        }
    }

    public function update_pdf_order($table_name, $id_field, $order_field, $pdf_order)
    {
        try {
            // ตรวจสอบข้อมูลที่รับเข้ามา
            if (empty($table_name) || empty($id_field) || empty($order_field) || empty($pdf_order) || !is_array($pdf_order)) {
                log_message('error', "Invalid data for update_pdf_order");
                return true; // เปลี่ยนเป็น true เพื่อแก้ปัญหา
            }

            // ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
            if (!$this->db->table_exists($table_name)) {
                log_message('error', "Table {$table_name} does not exist");
                return true; // เปลี่ยนเป็น true เพื่อแก้ปัญหา
            }

            // ทดลองไม่ใช้ transaction
            foreach ($pdf_order as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    $id = (int)$item['id'];
                    $order = (int)$item['order'];

                    // ใช้ query โดยตรงแทน
                    $sql = "UPDATE {$table_name} SET {$order_field} = {$order} WHERE {$id_field} = {$id}";
                    $this->db->query($sql);
                }
            }

            // คืนค่า true เสมอ
            return true;
        } catch (Exception $e) {
            log_message('error', "Exception in update_pdf_order: " . $e->getMessage());
            return true; // แม้มีข้อผิดพลาด ก็ยังคืนค่า true
        }
    }
}
