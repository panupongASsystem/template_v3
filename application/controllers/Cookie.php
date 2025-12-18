<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cookie extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // Preflight request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }

        $this->load->model('cookie_model');
        $this->load->library('user_agent');
        $this->load->database();
    }

    /**
     * ตรวจสอบ SQL Injection patterns
     */
    private function has_sql_injection($text)
    {
        if (empty($text)) {
            return false;
        }

        $patterns = [
            '/(\s+OR\s+\d+\s*=\s*\d+)/i',
            '/(\s+AND\s+\d+\s*=\s*\d+)/i',
            '/(UNION.*SELECT)/i',
            '/(sleep\s*\()/i',
            '/(benchmark\s*\()/i',
            '/(waitfor\s+delay)/i',
            '/(\bDROP\s+TABLE\b)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * บันทึก log suspicious
     */
    private function log_suspicious_activity($data, $reason)
    {
        try {
            $log_data = [
                'ip_address' => $this->input->ip_address(),
                'fingerprint' => isset($data['fingerprint']) ? substr($data['fingerprint'], 0, 64) : null,
                'user_agent' => isset($data['device']) ? substr($data['device'], 0, 255) : '',
                'session_id' => isset($data['session_id']) ? substr($data['session_id'], 0, 128) : null,
                'payload' => json_encode($data),
                'reason' => $reason,
                'log_type' => 'suspicious',
                'is_blocked' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_cookie_logs', $log_data);
        } catch (Exception $e) {
            log_message('error', 'Failed to log suspicious activity: ' . $e->getMessage());
        }
    }

    /**
     * บันทึก log access
     */
    private function log_access($data)
    {
        try {
            $log_data = [
                'ip_address' => $this->input->ip_address(),
                'fingerprint' => isset($data['fingerprint']) ? substr($data['fingerprint'], 0, 64) : null,
                'user_agent' => isset($data['device']) ? substr($data['device'], 0, 255) : '',
                'session_id' => isset($data['session_id']) ? substr($data['session_id'], 0, 128) : null,
                'log_type' => 'access',
                'is_blocked' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_cookie_logs', $log_data);
        } catch (Exception $e) {
            log_message('error', 'Failed to log access: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจสอบว่า fingerprint ถูกบล็อกหรือไม่
     */
    private function is_blocked($fingerprint)
    {
        if (empty($fingerprint)) {
            return false;
        }

        try {
            $this->db->select('id');
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('is_blocked', 1);
            $this->db->where('blocked_until >', date('Y-m-d H:i:s'));
            $this->db->limit(1);
            $result = $this->db->get('tbl_cookie_logs')->row();

            return !empty($result);
        } catch (Exception $e) {
            log_message('error', 'Failed to check block status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบ rate limit
     */
    private function check_rate_limit($fingerprint)
    {
        if (empty($fingerprint)) {
            return true; // ถ้าไม่มี fingerprint ให้ผ่าน
        }

        try {
            $time_ago = date('Y-m-d H:i:s', time() - 60); // 1 นาทีที่แล้ว

            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('created_at >=', $time_ago);
            $this->db->where('log_type', 'access');
            $count = $this->db->count_all_results('tbl_cookie_logs');

            if ($count >= 10) {
                // บล็อก 1 ชั่วโมง
                $block_data = [
                    'ip_address' => $this->input->ip_address(),
                    'fingerprint' => $fingerprint,
                    'user_agent' => $this->input->user_agent(),
                    'reason' => 'Rate limit exceeded',
                    'blocked_until' => date('Y-m-d H:i:s', time() + 3600),
                    'is_blocked' => 1,
                    'log_type' => 'blocked',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('tbl_cookie_logs', $block_data);

                return false;
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Failed to check rate limit: ' . $e->getMessage());
            return true; // ถ้า error ให้ผ่านไปก่อน
        }
    }

    /**
     * API accept - เหมือนเดิม 100%
     */
    public function accept()
    {
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // ตรวจสอบ JSON
            if (!is_array($data)) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Invalid JSON']));
                return;
            }

            // ตรวจสอบข้อมูลพื้นฐาน
            if (empty($data['session_id']) || empty($data['device'])) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Missing required fields']));
                return;
            }

            // ตรวจสอบ SQL Injection
            if (
                $this->has_sql_injection($data['session_id']) ||
                (isset($data['fingerprint']) && $this->has_sql_injection($data['fingerprint']))
            ) {

                $this->log_suspicious_activity($data, 'SQL Injection attempt');

                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Access denied']));
                return;
            }

            // ตรวจสอบ fingerprint ถูกบล็อกหรือไม่
            if (isset($data['fingerprint']) && $this->is_blocked($data['fingerprint'])) {
                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Your browser has been blocked']));
                return;
            }

            // ตรวจสอบ Rate Limit
            if (isset($data['fingerprint']) && !$this->check_rate_limit($data['fingerprint'])) {
                $this->output
                    ->set_status_header(429)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Too many requests']));
                return;
            }

            // บันทึก log access
            $this->log_access($data);

            // สร้างข้อมูลเหมือนเดิม 100%
            $cookie_types = ['คุกกี้พื้นฐานที่จำเป็น'];
            if (!empty($data['analytics'])) {
                $cookie_types[] = 'คุกกี้ในส่วนวิเคราะห์';
            }
            if (!empty($data['marketing'])) {
                $cookie_types[] = 'คุกกี้ในส่วนการตลาด';
            }

            $cookie_type_value = (count($cookie_types) === 3) ? 'ทั้งหมด' : implode(',', $cookie_types);

            // ดึงชื่อองค์กร
            $organization_name = get_config_value('fname');

            // เตรียมข้อมูลส่ง API (เหมือนเดิม 100%)
            $consent_data = [
                'sao_name' => $organization_name,
                'session_id' => $data['session_id'],
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $data['device'],
                'status' => 'accepted',
                'category' => $cookie_type_value
            ];

            // ส่งข้อมูลไปยังเว็บ cookie (เหมือนเดิม 100%)
            $ch = curl_init('https://cookie.assystem.co.th/Cookie/receive_consent');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($consent_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // บันทึกข้อมูลลง local database
            $this->cookie_model->save_consent($consent_data);

            // ส่งผลลัพธ์กลับ (เหมือนเดิม 100%)
            $this->output
                ->set_content_type('application/json')
                ->set_output($response);

        } catch (Exception $e) {
            log_message('error', 'Cookie accept error: ' . $e->getMessage());

            // ส่ง error response
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Server error']));
        }
    }
}