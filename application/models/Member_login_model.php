<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * โมเดลสำหรับจัดการการเข้าสู่ระบบของสมาชิก
 */
class Member_login_model extends CI_Model {
    
    /**
     * ตาราง tbl_member_login_attempts ใช้เก็บบันทึกความพยายามเข้าสู่ระบบ
     * 
     * ประกอบด้วยคอลัมน์:
     * - attempt_id: รหัสการพยายามเข้าสู่ระบบ
     * - fingerprint: รหัสพิมพ์ลายนิ้วมือของอุปกรณ์
     * - username: ชื่อผู้ใช้ที่พยายามเข้าสู่ระบบ
     * - status: สถานะการเข้าสู่ระบบ (success, failed, blocked)
     * - attempt_time: เวลาที่พยายามเข้าสู่ระบบ
     * - ip_address: ที่อยู่ IP ที่ใช้พยายามเข้าสู่ระบบ
     */
    private $attempts_table = 'tbl_member_login_attempts';
    
    /**
     * ตาราง tbl_member_activity_logs ใช้เก็บบันทึกกิจกรรมการใช้งานระบบ
     */
    private $activity_table = 'tbl_member_activity_logs';
    
    /**
     * จำนวนครั้งสูงสุดที่อนุญาตให้เข้าสู่ระบบล้มเหลว
     */
    private $max_attempts = 5;
    
    /**
     * ระยะเวลาที่บล็อคการเข้าสู่ระบบ (วินาที)
     */
    private $block_time = 900; // 15 นาที
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * ตรวจสอบข้อมูลการเข้าสู่ระบบโดยใช้ username และ password
     * 
     * @param string $username ชื่อผู้ใช้
     * @param string $password รหัสผ่าน (ยังไม่ได้เข้ารหัส)
     * @return object|bool ข้อมูลผู้ใช้หรือ false ถ้าไม่พบข้อมูล
     */
    public function authenticate($username, $password) {
        // เข้ารหัสรหัสผ่านด้วย sha1
        $encrypted_password = sha1($password);
        
        // ตรวจสอบข้อมูลในฐานข้อมูล
        $this->db->where('m_username', $username);
        $this->db->where('m_password', $encrypted_password);
        $query = $this->db->get('tbl_member');
        
        if ($query->num_rows() === 1) {
            return $query->row();
        } else {
            return false;
        }
    }
    
    /**
     * ตรวจสอบสถานะของผู้ใช้
     * 
     * @param int $user_id รหัสผู้ใช้
     * @return bool สถานะของผู้ใช้ (true = ใช้งานได้, false = ถูกบล็อค)
     */
    public function check_user_status($user_id) {
        $this->db->where('m_id', $user_id);
        $query = $this->db->get('tbl_member');
        
        if ($query->num_rows() === 1) {
            $user = $query->row();
            return ($user->m_status == 1); // ถ้า m_status = 1 คือใช้งานได้
        }
        
        return false; // ถ้าไม่พบผู้ใช้ ให้ถือว่าไม่สามารถใช้งานได้
    }
    
    /**
     * บันทึกความพยายามเข้าสู่ระบบ
     * 
     * @param string $fingerprint รหัสพิมพ์ลายนิ้วมือของอุปกรณ์
     * @param string $username ชื่อผู้ใช้ที่พยายามเข้าสู่ระบบ
     * @param string $status สถานะการเข้าสู่ระบบ (success, failed, blocked)
     * @return bool ผลลัพธ์การบันทึกข้อมูล
     */
    public function record_attempt($fingerprint, $username, $status) {
        $data = array(
            'fingerprint' => $fingerprint,
            'username' => $username,
            'status' => $status,
            'attempt_time' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address()
        );
        
        log_message('debug', 'Attempt data: ' . json_encode($data));
        
        try {
            $result = $this->db->insert($this->attempts_table, $data);
            log_message('debug', 'Insert result: ' . ($result ? 'success' : 'failed'));
            log_message('debug', 'Last query: ' . $this->db->last_query());
            
            if (!$result) {
                log_message('error', 'DB Error: ' . $this->db->error()['message']);
            }
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Exception in record_attempt: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * นับจำนวนครั้งที่เข้าสู่ระบบล้มเหลว
     * 
     * @param string $fingerprint รหัสพิมพ์ลายนิ้วมือของอุปกรณ์
     * @return int จำนวนครั้งที่ล้มเหลว
     */
    public function count_failed_attempts($fingerprint) {
        // ดึงการ login ที่สำเร็จล่าสุด
        $this->db->where('fingerprint', $fingerprint);
        $this->db->where('status', 'success');
        $this->db->order_by('attempt_time', 'DESC');
        $this->db->limit(1);
        $last_success = $this->db->get($this->attempts_table)->row();
        
        // ถ้ามีการ login สำเร็จล่าสุด ให้นับ failed attempts หลังจากนั้น
        if ($last_success) {
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $this->db->where('attempt_time >', $last_success->attempt_time);
            return $this->db->count_all_results($this->attempts_table);
        } else {
            // ถ้าไม่มีการ login สำเร็จ ให้นับ failed attempts ทั้งหมดใน 30 นาทีล่าสุด
            $cutoff_time = date('Y-m-d H:i:s', time() - 1800); // 30 นาที
            
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $this->db->where('attempt_time >', $cutoff_time);
            return $this->db->count_all_results($this->attempts_table);
        }
    }
    
    /**
     * ตรวจสอบว่า fingerprint นี้ถูกบล็อคอยู่หรือไม่
     * 
     * @param string $fingerprint รหัสพิมพ์ลายนิ้วมือของอุปกรณ์
     * @return array ข้อมูลการบล็อค
     */
    public function is_blocked($fingerprint) {
        // ถ้ามีการล็อกอินล้มเหลว 5 ครั้งขึ้นไป
        if ($this->count_failed_attempts($fingerprint) >= $this->max_attempts) {
            // ดึงเวลาล็อกอินล้มเหลวล่าสุด
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $this->db->order_by('attempt_time', 'DESC');
            $this->db->limit(1);
            $last_attempt = $this->db->get($this->attempts_table)->row();
            
            if ($last_attempt) {
                // ตรวจสอบว่าอยู่ในช่วงเวลาบล็อค 15 นาทีหรือไม่
                $block_until = strtotime($last_attempt->attempt_time) + $this->block_time;
                $now = time();
                
                if ($now < $block_until) {
                    // ยังอยู่ในช่วงเวลาบล็อค
                    return array(
                        'is_blocked' => true,
                        'remaining_time' => $block_until - $now // เวลาที่เหลือในวินาที
                    );
                }
            }
        }
        
        // ไม่ถูกบล็อค
        return array(
            'is_blocked' => false,
            'remaining_time' => 0
        );
    }
    
    /**
     * บล็อคการเข้าสู่ระบบของ fingerprint
     * 
     * @param string $fingerprint รหัสพิมพ์ลายนิ้วมือของอุปกรณ์
     * @return bool ผลลัพธ์การบล็อค
     */
    public function block_login($fingerprint) {
        return $this->record_attempt($fingerprint, 'blocked_user', 'blocked');
    }
    
    /**
     * ล้างข้อมูลความพยายามเข้าสู่ระบบที่ล้มเหลว
     * 
     * @param string $fingerprint รหัสพิมพ์ลายนิ้วมือของอุปกรณ์
     * @return bool ผลลัพธ์การล้างข้อมูล
     */
    public function clear_failed_attempts($fingerprint) {
        try {
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $result = $this->db->delete($this->attempts_table);
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Exception in clear_failed_attempts: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * บันทึกกิจกรรมการเข้าสู่ระบบ
     * 
     * @param string $username ชื่อผู้ใช้
     * @param string $activity_type ประเภทกิจกรรม
     * @param string $description รายละเอียดกิจกรรม
     * @param string $module โมดูลที่เกี่ยวข้อง
     * @return bool ผลลัพธ์การบันทึกกิจกรรม
     */
    public function log_activity($username, $activity_type, $description = '', $module = '') {
        try {
            $data = array(
                'user_id' => $this->session->userdata('m_id'),
                'username' => $username,
                'full_name' => trim($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname')),
                'activity_type' => $activity_type,
                'activity_description' => $description,
                'module' => $module,
                'ip_address' => $this->input->ip_address(),
                'device_info' => $this->_get_device_info(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            );

            $result = $this->db->insert($this->activity_table, $data);

            log_message('debug', 'Activity log result: ' . ($result ? 'success' : 'failed'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error logging activity: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * บันทึกกิจกรรมการล็อกอินที่ล้มเหลว
     * 
     * @param string $username ชื่อผู้ใช้
     * @param string $password รหัสผ่าน
     * @param string $activity_type ประเภทกิจกรรม
     * @param string $description รายละเอียดกิจกรรม
     * @param string $module โมดูลที่เกี่ยวข้อง
     * @return bool ผลลัพธ์การบันทึกกิจกรรม
     */
    public function log_failed_login($username, $password, $activity_type, $description = '', $module = '') {
        try {
            $data = array(
                'user_id' => 'N/A',
                'username' => $username,
                'full_name' => 'Failed login attempt for username: ' . $username . ' and Password: ' . $password,
                'activity_type' => $activity_type,
                'activity_description' => $description,
                'module' => $module,
                'ip_address' => $this->input->ip_address(),
                'device_info' => $this->_get_device_info(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $result = $this->db->insert($this->activity_table, $data);
            
            log_message('debug', 'Activity log result: ' . ($result ? 'success' : 'failed'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error logging activity: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ฟังก์ชันช่วยดึงข้อมูล device
     * 
     * @return string ข้อมูลอุปกรณ์ในรูปแบบ JSON
     */
    private function _get_device_info() {
        // โหลด User Agent Library
        if (!isset($this->agent)) {
            $this->load->library('user_agent');
        }
        
        // เพิ่มข้อมูลสำหรับ fingerprinting
        $device = [
            'type' => $this->agent->is_mobile() ? 'Mobile' : 'Desktop',
            'device' => $this->agent->platform(),
            'browser' => $this->agent->browser(),
            'browser_version' => $this->agent->version(),
            'os' => $this->agent->platform(),
            'screen_resolution' => $this->input->post('screen_resolution'), // ต้องส่งมาจาก client
            'color_depth' => $this->input->post('color_depth'), // ต้องส่งมาจาก client
            'timezone' => $this->input->post('timezone'), // ต้องส่งมาจาก client
            'language' => $this->agent->languages()[0] ?? 'unknown',
            'plugins' => $this->input->post('plugins'), // ต้องส่งมาจาก client
            'do_not_track' => $this->input->post('dnt'), // ต้องส่งมาจาก client
            'canvas_fingerprint' => $this->input->post('canvas_fp'), // ต้องส่งมาจาก client
            'webgl_fingerprint' => $this->input->post('webgl_fp'), // ต้องส่งมาจาก client
            'user_agent' => $this->input->user_agent()
        ];
        
        // สร้าง hash fingerprint จากข้อมูล
        $device['fingerprint'] = hash('sha256', json_encode($device));
        
        return json_encode($device);
    }
}