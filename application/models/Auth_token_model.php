<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model สำหรับจัดการ Auth Token
 * ปัจจุบันใช้สำหรับ Public User
 */
class Auth_token_model extends CI_Model {
    
    /**
     * ตาราง auth_tokens
     * @var string
     */
    private $table = 'auth_tokens';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * ตรวจสอบความถูกต้องของ Token
     * @param string $token Token ที่ต้องการตรวจสอบ
     * @return object|bool ข้อมูล Token หรือ false ถ้าไม่ถูกต้อง
     */
    public function validate_token($token) {
        $this->db->where('token', $token);
        $this->db->where('expires_at >', date('Y-m-d H:i:s'));
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }
    
    /**
     * สร้าง Token ใหม่
     * @param array $token_data ข้อมูล Token
     * @return string|bool Token ที่สร้างขึ้น หรือ false ถ้าล้มเหลว
     */
    public function create_token($token_data) {
        if (empty($token_data['user_id']) || empty($token_data['user_type'])) {
            return false;
        }
        
        // สร้าง Token
        $token = hash('sha256', $token_data['user_id'] . time() . random_bytes(32));
        
        // เตรียมข้อมูลสำหรับบันทึก
        $data = array(
            'token' => $token,
            'user_id' => $token_data['user_id'],
            'user_type' => $token_data['user_type'],
            'ipaddress' => isset($token_data['ipaddress']) ? $token_data['ipaddress'] : $this->input->ip_address(),
            'domain' => isset($token_data['domain']) ? $token_data['domain'] : $_SERVER['HTTP_HOST'],
            'tenant_id' => isset($token_data['tenant_id']) ? $token_data['tenant_id'] : null,
            'tenant_code' => isset($token_data['tenant_code']) ? $token_data['tenant_code'] : null,
            'user_agent' => isset($token_data['user_agent']) ? $token_data['user_agent'] : $_SERVER['HTTP_USER_AGENT'],
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        // บันทึกข้อมูล
        if ($this->db->insert($this->table, $data)) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * ดึงข้อมูล Token จาก token
     * @param string $token Token
     * @return object|bool ข้อมูล Token หรือ false ถ้าไม่พบ
     */
    public function get_token_data($token) {
        $this->db->where('token', $token);
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }
    
    /**
     * อัปเดตเวลาหมดอายุของ Token
     * @param string $token Token
     * @param int $minutes จำนวนนาทีที่ต้องการเพิ่ม (default: 15)
     * @return bool สถานะการอัปเดต
     */
    public function update_token_expiry($token, $minutes = 15) {
        $this->db->where('token', $token);
        return $this->db->update($this->table, [
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"))
        ]);
    }
    
    /**
     * ลบ Token ที่หมดอายุ
     * @return int จำนวน Token ที่ถูกลบ
     */
    public function cleanup_expired_tokens() {
        $this->db->where('expires_at <', date('Y-m-d H:i:s'));
        $this->db->delete($this->table);
        
        return $this->db->affected_rows();
    }
    
    /**
     * ลบ Token ของผู้ใช้
     * @param int $user_id ID ของผู้ใช้
     * @param string $user_type ประเภทของผู้ใช้ (default: public)
     * @return int จำนวน Token ที่ถูกลบ
     */
    public function delete_user_tokens($user_id, $user_type = 'public') {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->delete($this->table);
        
        return $this->db->affected_rows();
    }
    
    /**
     * ลบ Token เฉพาะ
     * @param string $token Token ที่ต้องการลบ
     * @return bool สถานะการลบ
     */
    public function delete_token($token) {
        $this->db->where('token', $token);
        return $this->db->delete($this->table);
    }
    
    /**
     * นับจำนวน Token ที่ยังใช้งานได้ของผู้ใช้
     * @param int $user_id ID ของผู้ใช้
     * @param string $user_type ประเภทของผู้ใช้ (default: public)
     * @return int จำนวน Token
     */
    public function count_active_tokens($user_id, $user_type = 'public') {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->where('expires_at >', date('Y-m-d H:i:s'));
        return $this->db->count_all_results($this->table);
    }
    
    /**
     * ดึง Token ที่ยังใช้งานได้ของผู้ใช้
     * @param int $user_id ID ของผู้ใช้
     * @param string $user_type ประเภทของผู้ใช้ (default: public)
     * @return array ข้อมูล Token
     */
    public function get_active_tokens($user_id, $user_type = 'public') {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->where('expires_at >', date('Y-m-d H:i:s'));
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->table);
        
        return $query->result();
    }
}