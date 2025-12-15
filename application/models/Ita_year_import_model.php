<?php
class Ita_year_import_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ตรวจสอบว่ามีปีนี้อยู่แล้วหรือไม่
     */
    public function check_year_exists($year)
    {
        $this->db->where('ita_year_year', $year);
        $query = $this->db->get('tbl_ita_year');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    /**
     * ลบข้อมูลปีเก่า (รวม topic และ link)
     */
    public function delete_year_data($ita_year_id)
    {
        // ดึง topic_id ทั้งหมดของปีนี้
        $this->db->select('ita_year_topic_id');
        $this->db->where('ita_year_topic_ref_id', $ita_year_id);
        $topics = $this->db->get('tbl_ita_year_topic')->result();

        // ลบ links ของแต่ละ topic
        foreach ($topics as $topic) {
            $this->db->where('ita_year_link_ref_id', $topic->ita_year_topic_id);
            $this->db->delete('tbl_ita_year_link');
        }

        // ลบ topics
        $this->db->where('ita_year_topic_ref_id', $ita_year_id);
        $this->db->delete('tbl_ita_year_topic');

        // ลบปี
        $this->db->where('ita_year_id', $ita_year_id);
        $this->db->delete('tbl_ita_year');
    }

    /**
     * เพิ่มข้อมูลปี
     */
    public function insert_year($year_info)
    {
        $data = array(
            'ita_year_year' => $year_info['ita_year_year'],
            'ita_year_by' => 'import_system',
            'ita_year_datesave' => date('Y-m-d H:i:s')
        );

        $this->db->insert('tbl_ita_year', $data);
        return $this->db->insert_id();
    }

    /**
     * เพิ่มข้อมูล Topic
     */
    public function insert_topic($topic_data)
    {
        $this->db->insert('tbl_ita_year_topic', $topic_data);
        return $this->db->insert_id();
    }

    /**
     * เพิ่มข้อมูล Link
     */
    public function insert_link($link_data)
    {
        $this->db->insert('tbl_ita_year_link', $link_data);
        return $this->db->insert_id();
    }
}