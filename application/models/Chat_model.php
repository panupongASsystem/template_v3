<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat_model extends CI_Model
{
    private $config_cache = [];
    private $cache_ttl = 300; // 5 minutes

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * ดึงค่า config จาก database
     */
    public function get_config($config_name, $default = null)
    {
        // Check cache first
        $cache_key = 'chat_config_' . $config_name;
        
        if (isset($this->config_cache[$cache_key]) && 
            (time() - $this->config_cache[$cache_key]['time']) < $this->cache_ttl) {
            return $this->config_cache[$cache_key]['value'];
        }

        $query = $this->db->select('config_value, config_type')
                         ->where('config_name', $config_name)
                         ->where('is_active', 1)
                         ->get('chat_config');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $value = $this->parse_config_value($row->config_value, $row->config_type);
            
            // Cache result
            $this->config_cache[$cache_key] = [
                'value' => $value,
                'time' => time()
            ];
            
            return $value;
        }

        return $default;
    }

    /**
     * แปลงค่า config ตาม type
     */
    private function parse_config_value($value, $type)
    {
        switch ($type) {
            case 'json':
                return json_decode($value, true) ?: [];
            case 'number':
                return is_numeric($value) ? (float)$value : 0;
            default:
                return $value;
        }
    }

    /**
     * ตั้งค่า config
     */
    public function set_config($config_name, $config_value, $config_type = 'text', $description = null)
    {
        $data = [
            'config_name' => $config_name,
            'config_value' => is_array($config_value) ? json_encode($config_value) : $config_value,
            'config_type' => $config_type,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($description !== null) {
            $data['description'] = $description;
        }

        // Clear cache
        unset($this->config_cache['chat_config_' . $config_name]);

        return $this->db->replace('chat_config', $data);
    }

    /**
     * ตรวจสอบ rate limit
     */
    public function check_rate_limit($user_id, $user_type = 'guest', $ip_address = null)
    {
        $rate_limit_requests = (int)$this->get_config('rate_limit_requests', 5);
        $rate_limit_window = (int)$this->get_config('rate_limit_window', 60);

        // คำนวณช่วงเวลาปัจจุบัน
        $window_start = date('Y-m-d H:i:s', time() - $rate_limit_window);

        // ลบข้อมูลเก่าที่เลยช่วงเวลาแล้ว
        $this->db->where('window_start <', $window_start)->delete('chat_rate_limits');

        // ดึงข้อมูลการใช้งานปัจจุบัน
        $query = $this->db->select('request_count, window_start')
                         ->where('user_id', $user_id)
                         ->where('window_start >=', $window_start)
                         ->get('chat_rate_limits');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $request_count = $row->request_count;

            if ($request_count >= $rate_limit_requests) {
                // คำนวณเวลาที่ต้องรอ
                $wait_time = $rate_limit_window - (time() - strtotime($row->window_start));
                return [
                    'allowed' => false,
                    'current_count' => $request_count,
                    'limit' => $rate_limit_requests,
                    'wait_time' => max(0, $wait_time),
                    'reset_time' => date('Y-m-d H:i:s', strtotime($row->window_start) + $rate_limit_window)
                ];
            }

            // อัพเดทจำนวนครั้ง
            $this->db->where('user_id', $user_id)
                     ->where('window_start >=', $window_start)
                     ->set('request_count', 'request_count + 1', FALSE)
                     ->set('last_request', 'NOW()', FALSE)
                     ->update('chat_rate_limits');

            return [
                'allowed' => true,
                'current_count' => $request_count + 1,
                'limit' => $rate_limit_requests,
                'remaining' => $rate_limit_requests - ($request_count + 1)
            ];
        } else {
            // สร้างรายการใหม่
            $data = [
                'user_id' => $user_id,
                'user_type' => $user_type,
                'request_count' => 1,
                'window_start' => date('Y-m-d H:i:s'),
                'last_request' => date('Y-m-d H:i:s'),
                'ip_address' => $ip_address
            ];
            $this->db->insert('chat_rate_limits', $data);

            return [
                'allowed' => true,
                'current_count' => 1,
                'limit' => $rate_limit_requests,
                'remaining' => $rate_limit_requests - 1
            ];
        }
    }

    /**
     * บันทึก log การสนทนา
     */
    public function log_conversation($user_id, $user_type, $message, $response = null, $response_time = null, $ip_address = null)
    {
        $data = [
            'user_id' => $user_id,
            'user_type' => $user_type,
            'message' => $message,
            'response' => $response,
            'response_time' => $response_time,
            'ip_address' => $ip_address,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('chat_logs', $data);
    }

    /**
     * ดึงสถิติการใช้งาน
     */
    public function get_usage_stats($user_id = null, $date_from = null, $date_to = null)
    {
        $this->db->select('
            COUNT(*) as total_conversations,
            COUNT(DISTINCT user_id) as unique_users,
            AVG(response_time) as avg_response_time,
            DATE(created_at) as date
        ')
        ->group_by('DATE(created_at)')
        ->order_by('created_at', 'DESC');

        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }

        if ($date_from) {
            $this->db->where('created_at >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('created_at <=', $date_to);
        }

        return $this->db->get('chat_logs')->result_array();
    }

    /**
     * ล้างข้อมูลเก่า
     */
    public function cleanup_old_data($days = 30)
    {
        $cutoff_date = date('Y-m-d H:i:s', time() - ($days * 24 * 60 * 60));
        
        // ล้าง rate limits เก่า
        $this->db->where('window_start <', $cutoff_date)->delete('chat_rate_limits');
        
        // ล้าง logs เก่า (optional)
        $this->db->where('created_at <', $cutoff_date)->delete('chat_logs');
        
        return [
            'rate_limits_deleted' => $this->db->affected_rows(),
            'logs_deleted' => $this->db->affected_rows()
        ];
    }

    /**
     * ดึงรายการ config ทั้งหมด (สำหรับ admin)
     */
    public function get_all_configs()
    {
        return $this->db->select('*')
                       ->order_by('config_name')
                       ->get('chat_config')
                       ->result_array();
    }

    /**
     * เปิด/ปิดการใช้งาน config
     */
    public function toggle_config($config_name, $is_active)
    {
        // Clear cache
        unset($this->config_cache['chat_config_' . $config_name]);
        
        return $this->db->where('config_name', $config_name)
                       ->update('chat_config', ['is_active' => $is_active ? 1 : 0]);
    }
}

/* End of file Chat_model.php */
/* Location: ./application/models/Chat_model.php */