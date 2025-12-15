<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * ดึงข้อมูล System_config ทั้งหมดจาก tbl_system_config
     * @return array
     */
    public function get_config()
    {
        // Query ข้อมูลจากตาราง tbl_system_config และแปลงเป็น array key => value
        $query = $this->db->get('tbl_system_config');
        
        $config = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $config[$row['keyword']] = $row['value'];
            }
        }
        
        // ถ้าไม่มีข้อมูล ใช้ค่า default
        if (empty($config)) {
            $config = $this->get_default_config();
        }
        
        return $config;
    }

    /**
     * ดึงข้อมูล config ตาม key
     * @param string $key
     * @return mixed
     */
    public function get_config_by_key($key)
    {
        $this->db->where('keyword', $key);
        $query = $this->db->get('tbl_system_config');
        
        if ($query->num_rows() > 0) {
            return $query->row()->value;
        }
        
        // ถ้าไม่มีใน database ดึงจาก default
        $default = $this->get_default_config();
        return isset($default[$key]) ? $default[$key] : null;
    }

    /**
     * ค่า default สำหรับ config
     * @return array
     */
    private function get_default_config()
    {
        return [
            'fname' => 'องค์การบริหารส่วนตำบลตัวอย่าง',
            'name' => 'อบต.ตัวอย่าง',
            'phone_1' => '0-1234-5678',
            'phone_2' => '0-1234-5679',
            'fax' => '0-1234-5680',
            'email_1' => 'info@example.go.th',
            'email_2' => 'admin@example.go.th',
            'address' => '123 หมู่ 1 ตำบลตัวอย่าง อำเภอเมือง จังหวัดตัวอย่าง 10000',
            'website' => 'https://www.example.go.th',
            'facebook' => 'https://www.facebook.com/example',
            'line' => '@example',
            'youtube' => 'https://www.youtube.com/@example',
            'map_latitude' => '13.7563',
            'map_longitude' => '100.5018',
            'office_hours' => 'วันจันทร์ - วันศุกร์ เวลา 08:30 - 16:30 น.',
            'logo' => 'logo.png',
            'updated_at' => date('Y-m-d H:i:s'),
            'pdpa_version' => '2.0',
            'policy_version' => '2.0',
            'cookie_version' => '1.0'
        ];
    }
}
