<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Camera_model extends CI_Model
{
    protected $table = 'tbl_camera'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'camera_id'; // Primary Key ของตาราง
    protected $allowedFields = ['camera_lat', 'camera_long', 'camera_api']; // ฟิลด์ที่อนุญาตให้เพิ่มข้อมูล

    public function addCamera($data)
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
    public function editCamera($camera_id, $data)
    {
        $this->db->where('camera_id', $camera_id);
        return $this->db->update($this->table, $data);
    }

    public function del_Camera($camera_id)
    {
        return $this->db->where('camera_id', $camera_id)->delete($this->table);
    }

    public function count_camera()
    {
        $this->db->select('COUNT(camera_id) as camera_total');
        $this->db->from('tbl_camera');
        $query = $this->db->get();
        return $query->result();
    }
    
}
