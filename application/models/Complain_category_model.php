<?php

class Complain_category_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * ดึงหมวดหมู่ที่เปิดใช้งาน
     */
    public function get_active_categories() {
        try {
            $this->db->where('cat_status', 1);
            $this->db->order_by('cat_order', 'ASC');
            $categories = $this->db->get('tbl_complain_category')->result();
            
            log_message('info', 'Loaded ' . count($categories) . ' active categories');
            return $categories;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_active_categories: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ดึงหมวดหมู่ทั้งหมด
     */
    public function get_all_categories() {
        try {
            $this->db->order_by('cat_order', 'ASC');
            return $this->db->get('tbl_complain_category')->result();
        } catch (Exception $e) {
            log_message('error', 'Error in get_all_categories: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ดึงหมวดหมู่ตาม ID
     */
    public function get_category_by_id($cat_id) {
        try {
            if (empty($cat_id)) return null;
            
            $this->db->where('cat_id', $cat_id);
            $category = $this->db->get('tbl_complain_category')->row();
            
            if ($category) {
                log_message('info', "Found category: {$category->cat_name} for ID: {$cat_id}");
            } else {
                log_message('warning', "Category not found for ID: {$cat_id}");
            }
            
            return $category;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_category_by_id: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * เพิ่มหมวดหมู่ใหม่
     */
    public function add_category($data) {
        try {
            // ตรวจสอบว่าชื่อหมวดหมู่ซ้ำหรือไม่
            $existing = $this->db->get_where('tbl_complain_category', ['cat_name' => $data['cat_name']])->row();
            if ($existing) {
                throw new Exception('ชื่อหมวดหมู่นี้มีอยู่แล้ว');
            }
            
            return $this->db->insert('tbl_complain_category', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Error in add_category: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * แก้ไขหมวดหมู่
     */
    public function update_category($id, $data) {
        try {
            $this->db->where('cat_id', $id);
            return $this->db->update('tbl_complain_category', $data);
        } catch (Exception $e) {
            log_message('error', 'Error in update_category: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ลบหมวดหมู่
     */
    public function delete_category($id) {
        try {
            // ตรวจสอบว่ามีการใช้งานหรือไม่
            $count = $this->db->where('complain_category_id', $id)
                             ->count_all_results('tbl_complain');
            if ($count > 0) {
                throw new Exception('ไม่สามารถลบได้ เนื่องจากมีเรื่องร้องเรียนใช้หมวดหมู่นี้อยู่');
            }
            
            $this->db->where('cat_id', $id);
            return $this->db->delete('tbl_complain_category');
            
        } catch (Exception $e) {
            log_message('error', 'Error in delete_category: ' . $e->getMessage());
            return false;
        }
    }
}