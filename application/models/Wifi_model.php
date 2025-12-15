<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wifi_model extends CI_Model
{
    protected $table = 'tbl_wifi'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'wifi_id'; // Primary Key ของตาราง
    protected $allowedFields = ['wifi_lat', 'wifi_long']; // ฟิลด์ที่อนุญาตให้เพิ่มข้อมูล

    public function addWifi($data)
    {
        return $this->db->insert($this->table, $data);
    }
    public function list_all()
    {
        return $this->db->order_by('wifi_id', 'DESC')->get($this->table)->result();
    }
    public function read($wifi_id)
    {
        return $this->db->where('wifi_id', $wifi_id)->get($this->table)->row();
    }
    public function editWifi($wifi_id, $data)
    {
        $this->db->where('wifi_id', $wifi_id)->update($this->table, $data);
    }

    public function del_Wifi($wifi_id)
    {
        return $this->db->where('wifi_id', $wifi_id)->delete($this->table);
    }

    public function count_wifi()
    {
        $this->db->select('COUNT(wifi_id) as wifi_total');
        $this->db->from('tbl_wifi');
        $query = $this->db->get();
        return $query->result();
    }
}
