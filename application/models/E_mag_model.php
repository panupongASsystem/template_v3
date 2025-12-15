<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_mag_model extends CI_Model
{

    private $table = 'tbl_e_magazines';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * ดึงข้อมูล E-Magazine ทั้งหมด
     */
    public function get_all()
    {
        $this->db->order_by('uploaded_at', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * ดึงข้อมูล E-Magazine ตาม ID
     */
    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * เพิ่มข้อมูล E-Magazine ใหม่
     */
    public function add_pdf($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * อัปเดตข้อมูล E-Magazine
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * ลบข้อมูล E-Magazine
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * ตรวจสอบว่ามีข้อมูลอยู่หรือไม่
     */
    public function exists($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * ดึงข้อมูล E-Magazine ล่าสุด (สำหรับแสดงในหน้า home)
     */
    public function get_latest($limit = 6)
    {
        $this->db->order_by('uploaded_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * นับจำนวน E-Magazine ทั้งหมด
     */
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * ดึงข้อมูลแบบแบ่งหน้า (สำหรับ backend)
     */
    public function get_paginated($limit, $offset)
    {
        $this->db->order_by('uploaded_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * ค้นหาข้อมูล E-Magazine
     */
    public function search($keyword)
    {
        $this->db->like('original_name', $keyword);
        $this->db->order_by('uploaded_at', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * ตรวจสอบว่าไฟล์ซ้ำหรือไม่
     */
    public function is_duplicate_file($file_name)
    {
        $this->db->where('file_name', $file_name);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * ดึงข้อมูล E-Magazine โดยชื่อไฟล์
     */
    public function get_by_filename($filename)
    {
        $this->db->where('file_name', $filename);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * อัปเดตข้อมูลเฉพาะฟิลด์ที่ระบุ
     */
    public function update_field($id, $field, $value)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, array($field => $value));
    }

    /**
     * ดึงข้อมูลสำหรับแสดงในหน้า frontend (สำหรับ Ajax)
     */
    public function get_for_frontend()
    {
        $this->db->select('id, file_name, original_name, cover_image, uploaded_at, visitors');
        $this->db->order_by('uploaded_at', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * ดึงข้อมูลสำหรับแสดงในหน้า home (จำกัดจำนวน)
     */
    public function get_for_home()
     {
        $this->db->select('*');
        $this->db->from('tbl_e_magazines');
        $this->db->limit(15);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึงข้อมูลสถิติ (สำหรับ dashboard)
     */
    public function get_stats()
    {
        $stats = array();

        // จำนวนทั้งหมด
        $stats['total'] = $this->count_all();

        // จำนวนที่อัปโหลดในเดือนนี้
        $this->db->where('MONTH(uploaded_at)', date('m'));
        $this->db->where('YEAR(uploaded_at)', date('Y'));
        $stats['this_month'] = $this->db->count_all_results($this->table);

        // จำนวนที่อัปโหลดในสัปดาห์นี้
        $this->db->where('WEEK(uploaded_at)', date('W'));
        $this->db->where('YEAR(uploaded_at)', date('Y'));
        $stats['this_week'] = $this->db->count_all_results($this->table);

        // จำนวนที่อัปโหลดวันนี้
        $this->db->where('DATE(uploaded_at)', date('Y-m-d'));
        $stats['today'] = $this->db->count_all_results($this->table);

        return $stats;
    }

    /**
     * ดึงข้อมูลล่าสุด 5 รายการ (สำหรับ dashboard)
     */
    public function get_recent($limit = 5)
    {
        $this->db->select('id, original_name, uploaded_at');
        $this->db->order_by('uploaded_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * ดึงข้อมูล E-Magazine ทั้งหมดสำหรับ backend (พร้อมข้อมูลเพิ่มเติม)
     */
    public function get_all_for_backend()
    {
        $this->db->select('id, file_name, original_name, cover_image, uploaded_at');
        $this->db->order_by('uploaded_at', 'DESC');
        $query = $this->db->get($this->table);
        $result = $query->result();

        // เพิ่มข้อมูลขนาดไฟล์ถ้าต้องการ
        foreach ($result as $row) {
            $pdf_path = './docs/file/' . $row->file_name;
            $cover_path = './docs/img/' . $row->cover_image;

            // ขนาดไฟล์ PDF
            $row->pdf_size = file_exists($pdf_path) ? filesize($pdf_path) : 0;

            // ขนาดไฟล์รูป
            $row->cover_size = file_exists($cover_path) ? filesize($cover_path) : 0;

            // แปลงขนาดไฟล์เป็น KB หรือ MB
            $row->pdf_size_formatted = $this->format_file_size($row->pdf_size);
            $row->cover_size_formatted = $this->format_file_size($row->cover_size);
        }

        return $result;
    }

    /**
     * แปลงขนาดไฟล์เป็นรูปแบบที่อ่านง่าย
     */
    private function format_file_size($size)
    {
        if ($size == 0) return '0 B';

        $units = array('B', 'KB', 'MB', 'GB');
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * API สำหรับดึงข้อมูล E-Magazine ทั้งหมด (AJAX)
     */
    public function get_all_magazines()
    {
        header('Content-Type: application/json');

        $magazines = $this->E_mag_model->get_for_frontend();

        // เพิ่ม URL เต็มให้กับข้อมูล
        foreach ($magazines as &$magazine) {
            $magazine['pdf_url'] = base_url('docs/file/' . $magazine['file_name']);
            $magazine['cover_url'] = !empty($magazine['cover_image'])
                ? base_url('docs/file/covers/' . $magazine['cover_image'])
                : base_url('docs/default_cover.png'); // รูป default ถ้าไม่มีหน้าปก
        }

        echo json_encode([
            'status' => 'success',
            'data' => $magazines
        ]);
    }

    public function increment_view($id)
    {
        $this->db->where('id', $id);
        $this->db->set('visitors', 'visitors + 1', false); // บวกค่า news_view ทีละ 1
        $this->db->update('tbl_e_magazines');
    }

    /**
     * ดึงข้อมูล E-Magazine แบบ pagination สำหรับ frontend
     */
    public function get_for_frontend_paginated($limit = 20, $offset = 0)
    {
        $this->db->select('id, file_name, original_name, cover_image, uploaded_at, visitors');
        $this->db->order_by('uploaded_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * นับจำนวน E-Magazine ทั้งหมดสำหรับ pagination
     */
    public function count_all_for_frontend()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * ค้นหาข้อมูล E-Magazine แบบ pagination
     */
    public function search_paginated($keyword, $limit = 21, $offset = 0)
    {
        $this->db->select('id, file_name, original_name, cover_image, uploaded_at, visitors');
        $this->db->like('original_name', $keyword);
        $this->db->order_by('uploaded_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * นับจำนวนผลการค้นหา
     */
    public function count_search_results($keyword)
    {
        $this->db->like('original_name', $keyword);
        return $this->db->count_all_results($this->table);
    }
}
