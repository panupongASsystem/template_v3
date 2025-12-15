<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        // โหลดฐานข้อมูล (หากไม่ได้ autoload)
        $this->load->database();
		log_message('debug', 'Api_model Initialized and database loaded.');
    }
	
	    /**
     * ตรวจสอบความถูกต้องของ Token
     * @param string $token
     * @param string $tenant_code
     * @return object|null คืนค่า object ของ token ถ้าถูกต้อง, หรือ null ถ้าไม่ถูกต้อง
     */
    public function validate_token($token, $tenant_code)
    {
        // ย้ายโค้ดส่วนที่คิวรีฐานข้อมูลมาไว้ที่นี่
        $query = $this->db->where([
            'token' => $token,
            'tenant_code' => $tenant_code,
            'expires_at >' => date('Y-m-d H:i:s')
        ])->get('auth_tokens');
        
        return $query->row();
    }

    /**
     * ฟังก์ชันสำหรับเพิ่มข้อมูลการจองลงในตาราง tbl_calender
     */
    public function insert_booking($data)
    {
        // Log ข้อมูลที่ได้รับจาก Controller
        log_message('debug', 'Api_model: insert_booking called with data: ' . json_encode($data));

        // ใช้ Query Builder ของ CodeIgniter ในการ INSERT ข้อมูล
        if ($this->db->insert('tbl_calender', $data)) {
            // ตรวจสอบว่ามีแถวที่ได้รับผลกระทบหรือไม่ (มีการเพิ่มข้อมูลจริง)
            $affected_rows = $this->db->affected_rows();
            log_message('info', 'Api_model: Insert successful. Affected rows: ' . $affected_rows);
            return $affected_rows > 0;
        }
        
        // Log กรณีที่การ insert ล้มเหลว
        log_message('error', 'Api_model: db->insert() returned false.');
        return false;
    }

}