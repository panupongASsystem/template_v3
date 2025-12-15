<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_log_model extends CI_Model
{
    private $channelAccessToken;
    private $lineApiUrl;

    public function __construct()
    {
        parent::__construct();
        // ใช้ helper function get_config_value เพื่อดึงค่า token จากฐานข้อมูล
        $this->channelAccessToken = get_config_value('line_token');
        $this->lineApiUrl = 'https://api.line.me/v2/bot/message/multicast';
    }

    public function log_activity($username, $activity_type, $description = '', $module = '')
    {
        try {
            // ดึงข้อมูลผู้ใช้จาก session ทั้ง m_id และ mp_id
            $user_id = $this->session->userdata('m_id');
            $is_public = false;
            $full_name = '';

            // ถ้าไม่มี m_id ให้ใช้ mp_id แทน (กรณีเป็นประชาชน)
            if ($user_id === null) {
                $user_id = $this->session->userdata('mp_id');
                $is_public = true;
                $full_name = trim($this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname'));
            } else {
                // กรณีเป็นเจ้าหน้าที่
                $full_name = trim($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'));
            }

            // บันทึกข้อมูลเพิ่มเติมเกี่ยวกับประเภทผู้ใช้
            $user_type = $is_public ? 'public' : 'staff';

            $data = array(
                'user_id' => $user_id,
                'username' => $username,
                'full_name' => $full_name,
                'user_type' => $user_type, // เพิ่มฟิลด์ user_type เพื่อแยกประเภทผู้ใช้
                'activity_type' => $activity_type,
                'activity_description' => $description,
                'module' => $module,
                'ip_address' => $this->input->ip_address(),
                'device_info' => $this->_get_device_info(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            );

            // ตรวจสอบว่าตาราง tbl_member_activity_logs มีฟิลด์ user_type หรือไม่
            // ถ้าไม่มีให้ลบออกจาก $data
            if (!$this->db->field_exists('user_type', 'tbl_member_activity_logs')) {
                unset($data['user_type']);
            }

            $result = $this->db->insert('tbl_member_activity_logs', $data);
            log_message('debug', 'Activity log result: ' . ($result ? 'success' : 'failed') . ' for user_id: ' . $user_id . ' (type: ' . $user_type . ')');
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error logging activity: ' . $e->getMessage());
            return false;
        }
    }
	
    // Log detect เมื่อมีการพยายาม login หรือ login เข้าสู่ระบบไม่สำเร็จ
    public function log_detect($username, $password, $user_type, $activity_type, $description = '', $module = '')
    {
        try {
            $data = array(
                'user_id' => 'N/A',
                'username' => $username,
                'full_name' => 'Failed login attempt for username: ' . $username . ' and Password: ' . $password,
				'user_type' => $user_type,
                'activity_type' => $activity_type,
                'activity_description' => $description,
                'module' => $module,
                'ip_address' => $this->input->ip_address(),
                'device_info' => $this->_get_device_info(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            );

            $result = $this->db->insert('tbl_member_activity_logs', $data);

            log_message('debug', 'Activity log result: ' . ($result ? 'success' : 'failed'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error logging activity: ' . $e->getMessage());
            return false;
        }
    }

    //====== บันทึก login attempt brute force attack ======
    public function log_attempt($fingerprint, $username, $status)
    {
        $data = array(
            'fingerprint' => $fingerprint,
            'username' => $username,
            'status' => $status,
            'ip_address' => $this->input->ip_address(), // เพิ่ม IP address
            'attempt_time' => date('Y-m-d H:i:s')
        );

        log_message('debug', 'Attempt data: ' . json_encode($data));

        try {
            $result = $this->db->insert('tbl_member_login_attempts', $data);
            log_message('debug', 'Insert result: ' . ($result ? 'success' : 'failed'));
            log_message('debug', 'Last query: ' . $this->db->last_query());

            if (!$result) {
                log_message('error', 'DB Error: ' . $this->db->error()['message']);
            }

            return $result;
        } catch (Exception $e) {
            log_message('error', 'Exception in log_attempt: ' . $e->getMessage());
            return false;
        }
    }

    // นับจำนวนครั้งที่ login ล้มเหลวในช่วงเวลาที่กำหนด
    public function count_failed_attempts($fingerprint)
    {
        // ดึงการ login ที่สำเร็จล่าสุด
        $this->db->where('fingerprint', $fingerprint);
        $this->db->where('status', 'success');
        $this->db->order_by('attempt_time', 'DESC');
        $this->db->limit(1);
        $last_success = $this->db->get('tbl_member_login_attempts')->row();

        // ถ้ามีการ login สำเร็จล่าสุด ให้นับ failed attempts หลังจากนั้น
        if ($last_success) {
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $this->db->where('attempt_time >', $last_success->attempt_time);
            return $this->db->count_all_results('tbl_member_login_attempts');
        } else {
            // ถ้าไม่มีการ login สำเร็จ ให้นับ failed attempts ทั้งหมด
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            return $this->db->count_all_results('tbl_member_login_attempts');
        }
    }

    // ตรวจสอบว่า fingerprint ถูกบล็อคหรือไม่
    public function is_blocked($fingerprint)
    {
        // นับจำนวนครั้งที่ล็อกอินล้มเหลว
        $failed_attempts = $this->count_failed_attempts($fingerprint);

        // ดึงประวัติการถูกบล็อค
        $this->db->where('fingerprint', $fingerprint);
        $this->db->where('status', 'blocked');
        $this->db->order_by('attempt_time', 'DESC');
        $this->db->limit(1);
        $last_block = $this->db->get('tbl_member_login_attempts')->row();

        $block_history = false;
        if ($last_block) {
            // ตรวจสอบว่าเคยถูกบล็อคมาก่อนหน้านี้หรือไม่
            $block_history = true;
        }

        // ตรวจสอบสถานะการบล็อค
        if ($failed_attempts >= 3) {
            // ดึงเวลาล็อกอินล้มเหลวล่าสุด
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $this->db->order_by('attempt_time', 'DESC');
            $this->db->limit(1);
            $last_attempt = $this->db->get('tbl_member_login_attempts')->row();

            if ($last_attempt) {
                $now = time();
                $block_duration = 0;

                // กำหนดระยะเวลาการบล็อค
                if ($block_history && $failed_attempts >= 6) {
                    // กรณีที่เคยถูกบล็อคมาแล้ว และล้มเหลวครบ 6 ครั้ง
                    $block_duration = 10 * 60; // 10 นาที
                } else {
                    // กรณีล้มเหลวครบ 3 ครั้งแรก
                    $block_duration = 3 * 60; // 3 นาที
                }

                $block_until = strtotime($last_attempt->attempt_time) + $block_duration;

                if ($now < $block_until) {
                    // ยังอยู่ในช่วงเวลาบล็อค
                    return array(
                        'blocked' => true,
                        'remaining_time' => $block_until - $now, // เวลาที่เหลือในวินาที
                        'block_level' => $block_history && $failed_attempts >= 6 ? 2 : 1 // ระดับการบล็อค: 1 = 3 นาที, 2 = 10 นาที
                    );
                }
            }
        }

        // ไม่ถูกบล็อค
        return array('blocked' => false);
    }

    // ฟังก์ชันช่วยดึงข้อมูล device
    private function _get_device_info()
    {
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
    //================================End of new modify ========================================================

    public function log_session_start($user_data)
    {
        try {
            $data = array(
                'user_id' => $user_data['m_id'],
                'username' => $user_data['m_username'],
                'full_name' => trim($user_data['m_fname'] . ' ' . $user_data['m_lname']),
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'session_id' => session_id(),
                'status' => 'active',
                'login_time' => date('Y-m-d H:i:s'),
                'last_activity' => date('Y-m-d H:i:s')
            );

            $result = $this->db->insert('user_sessions', $data);
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error logging session start: ' . $e->getMessage());
            return false;
        }
    }

    public function log_session_end($session_id)
    {
        try {
            if (!$session_id) {
                return false;
            }

            $result = $this->db->where('session_id', $session_id)
                ->update('user_sessions', array(
                    'logout_time' => date('Y-m-d H:i:s'),
                    'status' => 'logged_out'
                ));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Error logging session end: ' . $e->getMessage());
            return false;
        }
    }

    public function update_last_activity()
    {
        try {
            $session_id = session_id();
            if ($session_id) {
                return $this->db->where('session_id', $session_id)
                    ->update('user_sessions', array(
                        'last_activity' => date('Y-m-d H:i:s')
                    ));
            }
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error updating last activity: ' . $e->getMessage());
            return false;
        }
    }

    // ส่วนของการแสดงผล -----------------------------------------------------------
    // ดึงข้อมูลกิจกรรมทั้งหมด
    public function get_all_activities($limit = 10, $offset = 0, $sort_by = 'created_at', $sort_dir = 'DESC')
    {
        $this->db->order_by($sort_by, $sort_dir);
        $this->db->limit($limit, $offset);
        $query = $this->db->get('tbl_member_activity_logs');
        return $query->result();
    }

    // นับจำนวนกิจกรรมทั้งหมด
    public function count_all_activities()
    {
        return $this->db->count_all('tbl_member_activity_logs');
    }

    // ค้นหากิจกรรม
    public function search_activities($search_term, $limit = 10, $offset = 0)
    {
        $this->db->like('username', $search_term);
        $this->db->or_like('full_name', $search_term);
        $this->db->or_like('activity_type', $search_term);
        $this->db->or_like('activity_description', $search_term);
        $this->db->or_like('ip_address', $search_term);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get('tbl_member_activity_logs');
        return $query->result();
    }

    // นับจำนวนกิจกรรมตามการค้นหา
    public function count_search_activities($search_term)
    {
        $this->db->like('username', $search_term);
        $this->db->or_like('full_name', $search_term);
        $this->db->or_like('activity_type', $search_term);
        $this->db->or_like('activity_description', $search_term);
        $this->db->or_like('ip_address', $search_term);
        $query = $this->db->get('tbl_member_activity_logs');
        return $query->num_rows();
    }

    // กรองกิจกรรมตามประเภท
    public function filter_by_activity_type($activity_type, $limit = 10, $offset = 0)
    {
        $this->db->where('activity_type', $activity_type);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get('tbl_member_activity_logs');
        return $query->result();
    }

    // ดึงข้อมูลสถิติกิจกรรม
    public function get_activity_stats()
    {
        // จำนวนกิจกรรมตามประเภท
        $this->db->select('activity_type, COUNT(*) as count');
        $this->db->group_by('activity_type');
        $query_types = $this->db->get('tbl_member_activity_logs');
        $activity_types = $query_types->result();

        // จำนวนกิจกรรมตาม device type
        $this->db->select('id, device_info');
        $query_devices = $this->db->get('tbl_member_activity_logs');
        $device_stats = [];

        foreach ($query_devices->result() as $row) {
            $device_info = !is_null($row->device_info) ? json_decode($row->device_info, true) : [];
            if (isset($device_info['type'])) {
                $type = $device_info['type'];
                if (!isset($device_stats[$type])) {
                    $device_stats[$type] = 0;
                }
                $device_stats[$type]++;
            }
        }

        return [
            'activity_types' => $activity_types,
            'device_stats' => $device_stats
        ];
    }

    public function filter_by_date_range($start_date, $end_date, $limit = 15, $offset = 0)
    {
        if (!empty($start_date)) {
            $this->db->where('DATE(created_at) >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('DATE(created_at) <=', $end_date);
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get('tbl_member_activity_logs');
        return $query->result();
    }

    // เพิ่มฟังก์ชันนับจำนวนตามช่วงเวลา
    public function count_by_date_range($start_date, $end_date)
    {
        if (!empty($start_date)) {
            $this->db->where('DATE(created_at) >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('DATE(created_at) <=', $end_date);
        }
        return $this->db->count_all_results('tbl_member_activity_logs');
    }

    //================================ start alert line,email ========================================================
    public function send_line_alert($message)
    {
        // ประกาศ token เป็นตัวแปรปกติภายในฟังก์ชัน (ไม่ใช้ private)
        $channelAccessToken = 'cUrLS1xxTWV4NlpIEEXiOftAQpBZKbKAbtIC5TRfQt/7alqWiiXkTO3U/U7WpFAmWOfEtVJr+HHgVJ8c+ZeUcTgb72u5AH9y8iUXokPvh8kAJqQIveN+EjcdbheIKpyvSBtUbHUenswBQq6mmNCvlQdB04t89/1O/w1cDnyilFU=';

        // ใช้ Group ID ที่ได้จาก webhook.site
        $groupId = "Ca22dd5c6d24bf3790433676526bbaf65"; // Group ID ใหม่ของคุณ

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $channelAccessToken
        ];

        // สร้างข้อความสำหรับกลุ่ม
        $data = [
            'to' => $groupId, // ส่งไปยัง Group ID แทน User ID
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/push');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // บันทึกข้อมูลการตอบกลับเพื่อการดีบัก
        log_message('debug', 'Line API Response Code: ' . $httpCode);
        log_message('debug', 'Line API Response: ' . $response);

        if ($httpCode !== 200) {
            log_message('error', 'Line API Error: ' . $response);
            return false;
        }

        return true;
    }

    /**
     * ฟังก์ชันสำหรับส่งข้อความแจ้งเตือนไปยัง Line OA ของลูกค้า
     * 
     * @param string $message ข้อความที่ต้องการส่ง
     * @return bool ผลลัพธ์การส่งข้อความ
     */
    public function send_line_customer($message)
    {
        // ตรวจสอบการตั้งค่าว่าเปิดใช้งานการแจ้งเตือนหรือไม่
        $this->db->where('keyword', 'line_notification_status');
        $query = $this->db->get('tbl_system_config');

        // ถ้าไม่พบการตั้งค่าหรือปิดการใช้งาน ให้ข้ามการส่งข้อความ
        if ($query->num_rows() == 0 || $query->row()->value == '0') {
            log_message('info', 'Line notification is disabled. Message not sent.');
            return false;
        }

        // ดึง User IDs ของลูกค้าจากฐานข้อมูล
        $userIds = $this->db->select('line_user_id')
            ->from('tbl_line')
            ->where('line_status', 'show')
            ->get()
            ->result_array();

        $to = array_column($userIds, 'line_user_id');
        if (empty($to)) {
            log_message('error', 'No Line users found with status show');
            return false;
        }

        $to = array_filter($to);
        if (empty($to)) {
            log_message('error', 'No valid Line user IDs found after filtering');
            return false;
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken
        ];

        // เตรียมข้อความที่จะส่ง
        $messages = [
            [
                'type' => 'text',
                'text' => $message
            ]
        ];

        // แบ่งผู้รับเป็นกลุ่มละไม่เกิน 500 คน ตามข้อจำกัดของ Line API
        $chunks = array_chunk($to, 500);
        $success = true;

        foreach ($chunks as $receivers) {
            $data = [
                'to' => $receivers,
                'messages' => $messages
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->lineApiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // บันทึกการตอบกลับเพื่อการดีบัก
            log_message('debug', 'Customer Line API Response Code: ' . $httpCode);
            log_message('debug', 'Customer Line API Response: ' . $response);

            if ($httpCode !== 200) {
                $success = false;
                log_message('error', 'Customer Line API Error: ' . $response);
            }

            curl_close($ch);
        }

        return $success;
    }

    /**
     * ฟังก์ชันสำหรับส่งการแจ้งเตือนทางอีเมล
     * 
     * @param string $subject หัวข้ออีเมล
     * @param string $message เนื้อหาข้อความ
     * @return bool ผลลัพธ์การส่งอีเมล
     */
    public function send_line_email($subject, $message)
    {
        // โหลด Email Library ของ CodeIgniter
        $this->load->library('email');

        // ดึงโดเมนปัจจุบัน
        $current_domain = $_SERVER['HTTP_HOST'];
        $current_domain = preg_replace('#^https?://#', '', $current_domain);
        $current_domain = preg_replace('/^www\./', '', $current_domain);
        $current_domain = strtok($current_domain, '/');
        $current_domain = strtolower(trim($current_domain));

        // สร้างอีเมลแบบ no-reply@domain.com
        $from_email = 'no-reply@' . $current_domain;
        $from_name = 'Security Alert';

        // ดึงรายชื่ออีเมลจากฐานข้อมูล
        $this->db->select('email_name');
        $this->db->from('tbl_email');
        $this->db->where('email_status', '1');
        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            log_message('error', 'No active email recipients found');
            return false;
        }

        // สร้างรายชื่ออีเมลผู้รับ
        $to_emails = [];
        foreach ($query->result() as $row) {
            $to_emails[] = $row->email_name;
        }

        // ตั้งค่า email configuration
        $config = [
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'priority' => 1 // สูงสุด
        ];

        // เริ่มต้นการตั้งค่าอีเมล
        $this->email->initialize($config);

        // ตั้งค่า sender เป็น no-reply@domain.com
        $this->email->from($from_email, $from_name);

        // ส่งถึงทุกอีเมลในรายการ
        $this->email->to($to_emails);

        // ตั้งค่าหัวข้อและเนื้อหา
        $this->email->subject($subject);

        // สร้างเนื้อหาอีเมลในรูปแบบ HTML
        $email_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #f44336; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>การแจ้งเตือนความปลอดภัย</h2>
            </div>
            <div class='content'>
                " . nl2br($message) . "
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " " . $current_domain . " - ระบบแจ้งเตือนความปลอดภัย</p>
                <p>นี่เป็นอีเมลอัตโนมัติ โปรดอย่าตอบกลับ</p>
            </div>
        </div>
    </body>
    </html>
    ";

        $this->email->message($email_message);

        // เพิ่ม header สำหรับอีเมลระบบ
        $this->email->set_header('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');
        $this->email->set_header('Auto-Submitted', 'auto-generated');

        // ส่งอีเมล
        if ($this->email->send()) {
            log_message('info', 'Security email alert sent successfully from ' . $from_email . ' to: ' . implode(', ', $to_emails));
            return true;
        } else {
            log_message('error', 'Failed to send security email alert: ' . $this->email->print_debugger());
            return false;
        }
    }

    /**
     * ฟังก์ชันสำหรับดึงรายการอีเมลทั้งหมด
     * 
     * @return array ข้อมูลอีเมลทั้งหมด
     */
    public function list_email()
    {
        $this->db->from('tbl_email as a');
        $this->db->order_by('a.email_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ฟังก์ชันสำหรับเพิ่มอีเมลใหม่
     * 
     * @return bool ผลลัพธ์การเพิ่มข้อมูล
     */
    public function add_email()
    {
        $email_name = $this->input->post('email_name');

        // ตรวจสอบว่าอีเมลซ้ำหรือไม่
        $this->db->select('email_name');
        $this->db->where('email_name', $email_name);
        $query = $this->db->get('tbl_email');
        $num = $query->num_rows();

        if ($num > 0) {
            $this->session->set_flashdata('save_again', TRUE);
            return false;
        } else {
            // ค้นหา email_id สูงสุดในตาราง
            $this->db->select_max('email_id');
            $max_id_query = $this->db->get('tbl_email');
            $max_id = 1; // ค่าเริ่มต้นถ้าไม่มีข้อมูลในตาราง

            if ($max_id_query->num_rows() > 0) {
                $max_id = (int) $max_id_query->row()->email_id + 1;
            }

            $data = array(
                'email_id' => $max_id,
                'email_name' => $this->input->post('email_name'),
                'email_by' => $this->session->userdata('m_fname'), // เพิ่มชื่อคนที่เพิ่มข้อมูล
                'email_status' => '1' // เปิดใช้งานเป็นค่าเริ่มต้น
            );

            $query = $this->db->insert('tbl_email', $data);
            $this->session->set_flashdata('save_success', TRUE);
            return $query;
        }
    }

    /**
     * ฟังก์ชันสำหรับแก้ไขข้อมูลอีเมล
     * 
     * @param int $email_id รหัสอีเมล
     * @return bool ผลลัพธ์การแก้ไขข้อมูล
     */
    public function edit_email($email_id)
    {
        $email_name = $this->input->post('email_name');

        // ตรวจสอบว่าอีเมลซ้ำกับระบบหรือไม่ (ยกเว้นอีเมลปัจจุบัน)
        $this->db->where('email_name', $email_name);
        $this->db->where_not_in('email_id', $email_id);
        $query = $this->db->get('tbl_email');
        $num = $query->num_rows();

        if ($num > 0) {
            $this->session->set_flashdata('save_again', TRUE);
            return false;
        } else {
            // อัพเดตข้อมูล
            $email_status = $this->input->post('email_status') ? '1' : '0';

            $data = array(
                'email_name' => $email_name,
                'email_by' => $this->session->userdata('m_fname'),
                'email_status' => $email_status
            );

            $this->db->where('email_id', $email_id);
            $query = $this->db->update('tbl_email', $data);

            $this->session->set_flashdata('save_success', TRUE);
            return $query;
        }
    }

    /**
     * ฟังก์ชันสำหรับลบข้อมูลอีเมล
     * 
     * @param int $email_id รหัสอีเมล
     * @return bool ผลลัพธ์การลบข้อมูล
     */
    public function del_email($email_id)
    {
        $this->db->delete('tbl_email', array('email_id' => $email_id));
        $this->session->set_flashdata('del_success', TRUE);
        return true;
    }

    /**
     * ฟังก์ชันสำหรับอัพเดตสถานะอีเมล
     * 
     * @return bool ผลลัพธ์การอัพเดตสถานะ
     */
    public function updateEmailStatus()
    {
        // ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
        if ($this->input->post()) {
            $emailId = $this->input->post('email_id');
            $newStatus = $this->input->post('new_status');

            // ทำการอัพเดตค่าในตาราง tbl_email
            $data = array(
                'email_status' => $newStatus
            );

            $this->db->where('email_id', $emailId);
            $this->db->update('tbl_email', $data);

            // ส่งการตอบกลับ
            $response = array('status' => 'success', 'message' => 'อัพเดตสถานะเรียบร้อย');
            echo json_encode($response);
            return true;
        }

        return false;
    }

    /**
     * ฟังก์ชันสำหรับอัพเดตสถานะอีเมลทั้งหมด
     * 
     * @param string $newStatus สถานะใหม่
     * @return bool ผลลัพธ์การอัพเดตสถานะ
     */
    public function updateEmailStatusAll($newStatus)
    {
        // อัพเดตค่า email_status ของทุกแถวในตาราง tbl_email
        $data = array(
            'email_status' => $newStatus
        );

        // อัพเดตทุกแถว
        $this->db->update('tbl_email', $data);

        // ส่งค่ากลับถ้าต้องการ
        if ($this->db->affected_rows() > 0) {
            return true; // สำเร็จ
        } else {
            return false; // ล้มเหลว หรือไม่มีแถวที่ถูกอัพเดต
        }
    }


    public function get_setting($setting_name)
    {
        $this->db->where('keyword', $setting_name);
        $query = $this->db->get('tbl_system_config');

        if ($query->num_rows() > 0) {
            return $query->row()->value;
        }

        return null;
    }

    public function update_setting($setting_name, $setting_value, $updated_by = null)
    {
        // ตรวจสอบว่ามีการตั้งค่านี้อยู่แล้วหรือไม่
        $this->db->where('keyword', $setting_name);
        $query = $this->db->get('tbl_system_config');

        $data = array(
            'value' => $setting_value,
            'update_date' => date('Y-m-d H:i:s')  // แก้จาก updated_at เป็น update_date
        );

        if ($updated_by) {
            $data['update_by'] = $updated_by;  // แก้จาก updated_by เป็น update_by
        }

        if ($query->num_rows() > 0) {
            // อัพเดตการตั้งค่าที่มีอยู่
            $this->db->where('keyword', $setting_name);
            return $this->db->update('tbl_system_config', $data);
        } else {
            // เพิ่มการตั้งค่าใหม่
            $data['keyword'] = $setting_name;
            $data['description'] = 'เพิ่มโดย ' . ($updated_by ?: 'ระบบ');
            $data['type'] = 'key_token';  // เพิ่มค่าเริ่มต้นสำหรับคอลัมน์ type
            return $this->db->insert('tbl_system_config', $data);
        }
    }
    //================================ end alert line,email ========================================================

	/**
 * ดึงข้อมูลผู้ใช้ที่มีกิจกรรมมากที่สุด Top 10
 * 
 * @param int $days จำนวนวันย้อนหลัง (default = 30)
 * @param int $limit จำนวนผู้ใช้ที่ต้องการ (default = 10)
 * @return array
 */
private function formatDisplayName($full_name, $username, $max_length = 15)
{
    // ลำดับความสำคัญ: full_name -> username -> user_id
    $name = trim($full_name);
    
    // ถ้า full_name ว่างให้ใช้ username
    if (empty($name) || $name === 'N/A') {
        $name = $username;
    }
    
    // ถ้ายังว่างอยู่
    if (empty($name)) {
        return 'ผู้ใช้ไม่ระบุชื่อ';
    }
    
    // ลบอีเมลออก (กรณีที่ username เป็นอีเมล)
    if (strpos($name, '@') !== false) {
        $name = explode('@', $name)[0];
    }
    
    // ตัดชื่อให้เหมาะสม
    if (mb_strlen($name, 'UTF-8') > $max_length) {
        $name = mb_substr($name, 0, $max_length - 2, 'UTF-8') . '..';
    }
    
    return $name;
}

/**
 * ดึงข้อมูลผู้ใช้ที่มีกิจกรรมมากที่สุด (แก้ไขชื่อแสดงผล)
 */
public function getTopActiveUsers($days = 0, $limit = 10)
{
    try {
        // Step 1: นับจำนวนกิจกรรม
        $this->db->select('user_id, COUNT(*) as activity_count');
        $this->db->from('tbl_member_activity_logs');
        
        if ($days > 0) {
            $start_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $this->db->where('created_at >=', $start_date);
        }
        
        $this->db->where('user_id !=', 'N/A');
        $this->db->where('user_id IS NOT NULL');
        $this->db->where('user_id !=', '');
        $this->db->where('activity_type !=', 'failed');
        
        $this->db->group_by('user_id');
        $this->db->order_by('activity_count', 'DESC');
        $this->db->limit($limit);
        
        $activity_query = $this->db->get();
        $activity_counts = $activity_query->result();
        
        if (empty($activity_counts)) {
            return [];
        }
        
        // Step 2: ดึงข้อมูลผู้ใช้
        $user_ids = array_column($activity_counts, 'user_id');
        
        $this->db->select('user_id, username, full_name, user_type, MAX(created_at) as last_activity');
        $this->db->from('tbl_member_activity_logs');
        $this->db->where_in('user_id', $user_ids);
        $this->db->group_by(['user_id']);
        $this->db->order_by('last_activity', 'DESC');
        
        $user_query = $this->db->get();
        $user_details = $user_query->result();
        
        // Step 3: รวมข้อมูลและปรับปรุงชื่อแสดงผล
        $top_users = [];
        foreach ($activity_counts as $activity) {
            $user_detail = null;
            
            foreach ($user_details as $detail) {
                if ($detail->user_id == $activity->user_id) {
                    $user_detail = $detail;
                    break;
                }
            }
            
            // สร้างชื่อแสดงผล
            $display_name = 'ผู้ใช้ ' . $activity->user_id;
            $username = '';
            $full_name = '';
            $user_type = 'unknown';
            
            if ($user_detail) {
                $username = $user_detail->username ?: '';
                $full_name = $user_detail->full_name ?: '';
                $user_type = $user_detail->user_type ?: 'unknown';
                
                // ใช้ฟังก์ชันตัดชื่อใหม่
                $display_name = $this->formatDisplayName($full_name, $username);
            }
            
            $top_users[] = (object)[
                'user_id' => $activity->user_id,
                'username' => $username,
                'full_name' => $full_name,
                'display_name' => $display_name, // เพิ่มฟิลด์นี้
                'user_type' => $user_type,
                'activity_count' => (int)$activity->activity_count,
                'last_activity' => $user_detail->last_activity ?? null,
                'badge_color' => $this->getUserTypeBadgeColor($user_type)
            ];
        }
        
        return $top_users;
        
    } catch (Exception $e) {
        log_message('error', 'Error in getTopActiveUsers: ' . $e->getMessage());
        return [];
    }
}

/**
 * กำหนดสีของ Badge ตามประเภทผู้ใช้
 * 
 * @param string $user_type ประเภทผู้ใช้
 * @return string สีของ badge
 */
private function getUserTypeBadgeColor($user_type)
{
    switch (strtolower($user_type)) {
        case 'staff':
            return 'primary';
        case 'public':
            return 'success';
        case 'admin':
            return 'danger';
        case 'moderator':
            return 'warning';
        default:
            return 'secondary';
    }
}

}
