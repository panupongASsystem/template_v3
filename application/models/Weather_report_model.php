<?php
class Weather_report_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function save_weather_report($data) {
        return $this->db->insert('tbl_weather_reports', $data);
    }

    public function clear_weather_reports() {
        return $this->db->empty_table('tbl_weather_reports');
    }

    public function weather_reports_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_weather_reports');
        $query = $this->db->get();
        return $query->result();
    }
}