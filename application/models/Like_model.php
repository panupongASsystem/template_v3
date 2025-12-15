<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Like_model extends CI_Model
{
    protected $table = 'tbl_like'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'like_id'; // Primary Key ของตาราง
    protected $allowedFields = ['like_name']; // ฟิลด์ที่อนุญาตให้เพิ่มข้อมูล

    public function addLike($data)
    {
        return $this->db->insert($this->table, $data);
    }
    public function list_all()
    {
        return $this->db->order_by('camera_id', 'DESC')->get($this->table)->result();
    }
    public function read($camera_id)
    {
        return $this->db->where('camera_id', $camera_id)->get($this->table)->row();
    }
    public function countLikes($likeName)
    {
        $this->db->from('tbl_like');
        $this->db->where('like_name', $likeName);
        return $this->db->count_all_results();
    }
}
