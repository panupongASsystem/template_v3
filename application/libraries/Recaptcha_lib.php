<?php
/**
 * ====================================================================
 * reCAPTCHA Library for CodeIgniter 3
 * ====================================================================
 * 
 * ไฟล์: application/libraries/Recaptcha_lib.php
 * 
 * วิธีการใช้งาน:
 * 1. วางไฟล์นี้ใน application/libraries/
 * 2. ใน Controller: $this->load->library('recaptcha_lib');
 * 3. เรียกใช้: $this->recaptcha_lib->verify($token, 'citizen');
 * 
 * ====================================================================
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Recaptcha_lib
{
    /**
     * CodeIgniter instance
     */
    protected $CI;

    /**
     * Library configuration
     */
    protected $config = array(
        'secret_key' => '',
        'site_key' => '',
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
        'timeout' => 10,
        'min_score_default' => 0.5,
        'min_score_staff' => 0.7,
        'min_score_citizen' => 0.5,
        'enabled' => true,
        'debug_mode' => false
    );

    /**
     * Valid user types
     */
    protected $valid_user_types = array('staff', 'citizen', 'admin', 'user');

    /**
     * Constructor
     */
    public function __construct($config = array())
    {
        $this->CI =& get_instance();

        // โหลด Helper และ Library ที่จำเป็น
        $this->CI->load->helper('url');

        // Initialize configuration
        $this->initialize($config);

        log_message('info', 'Recaptcha_lib Library Initialized');
    }

    /**
     * Initialize library configuration
     * 
     * @param array $config Configuration array
     * @return void
     */
    public function initialize($config = array())
    {
        // Merge with default config
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }

        // โหลด secret key จาก database หรือ config file
        if (empty($this->config['secret_key'])) {
            if (function_exists('get_config_value')) {
                $this->config['secret_key'] = get_config_value('secret_key_recaptchar');
                $this->config['site_key'] = get_config_value('recaptcha');
            }
        }

        // ตรวจสอบว่า debug mode เปิดอยู่หรือไม่
        if (ENVIRONMENT === 'development') {
            $this->config['debug_mode'] = true;
        }

        $this->_log_debug('reCAPTCHA Library configuration loaded');
    }

    /**
     * ✅ หลักฟังก์ชันสำหรับตรวจสอบ reCAPTCHA
     * 
     * @param string $token reCAPTCHA response token
     * @param string $user_type ประเภทผู้ใช้ ('staff', 'citizen', 'admin', 'user')
     * @param float|null $min_score คะแนนขั้นต่ำ (null = ใช้ default ตาม user_type)
     * @param array $options ตัวเลือกเพิ่มเติม
     * @return array ผลลัพธ์การตรวจสอบ
     */
    public function verify($token, $user_type = 'citizen', $min_score = null, $options = array())
    {
        $start_time = microtime(true);

        try {
            // ตรวจสอบว่า reCAPTCHA เปิดใช้งานหรือไม่
            if (!$this->config['enabled']) {
                return $this->_create_response(true, 'reCAPTCHA disabled', array(
                    'bypassed' => true,
                    'reason' => 'disabled'
                ));
            }

            // Validate inputs
            $validation = $this->_validate_inputs($token, $user_type);
            if (!$validation['success']) {
                return $validation;
            }

            // กำหนด min_score ถ้าไม่ได้ระบุ
            if ($min_score === null) {
                $min_score = $this->_get_min_score($user_type);
            }

            // ✅ เพิ่ม: ดึง action และ source จาก options
            $action = isset($options['action']) ? $options['action'] : 'default_action';
            $source = isset($options['source']) ? $options['source'] : 'unknown';

            $this->_log_debug("Starting {$user_type} reCAPTCHA verification", array(
                'token_length' => strlen($token),
                'min_score' => $min_score,
                'user_type' => $user_type,
                'action' => $action,
                'source' => $source
            ));

            // ส่งคำขอไป Google reCAPTCHA API
            $api_response = $this->_call_recaptcha_api($token);
            if (!$api_response['success']) {
                return $api_response;
            }

            // ตรวจสอบผลลัพธ์จาก Google
            $verification = $this->_verify_response($api_response['data'], $user_type, $min_score, $action);

            // เพิ่มข้อมูล timing และ context
            $verification['data']['response_time'] = round((microtime(true) - $start_time) * 1000, 2);
            $verification['data']['action'] = $action;
            $verification['data']['source'] = $source;

            $this->_log_info("reCAPTCHA verification completed", array(
                'user_type' => $user_type,
                'action' => $action,
                'source' => $source,
                'success' => $verification['success'],
                'score' => isset($verification['data']['score']) ? $verification['data']['score'] : 'N/A',
                'response_time' => $verification['data']['response_time'] . 'ms'
            ));

            // ✅ บันทึกลง Database พร้อม action และ source
            $this->_log_to_database($verification, $user_type, strlen($token), $action, $source);

            return $verification;

        } catch (Exception $e) {
            $this->_log_error('reCAPTCHA verification exception', array(
                'error' => $e->getMessage(),
                'user_type' => $user_type,
                'action' => isset($action) ? $action : 'unknown',
                'source' => isset($source) ? $source : 'unknown'
            ));

            $error_verification = $this->_create_response(false, 'Verification exception: ' . $e->getMessage(), array(
                'exception' => true,
                'response_time' => round((microtime(true) - $start_time) * 1000, 2),
                'action' => isset($action) ? $action : 'unknown',
                'source' => isset($source) ? $source : 'unknown'
            ));

            // ✅ บันทึก Exception ลง Database
            $this->_log_to_database($error_verification, $user_type, strlen($token), isset($action) ? $action : 'error', isset($source) ? $source : 'unknown');

            return $error_verification;
        }
    }

    /**
     * ✅ ฟังก์ชันย่อยสำหรับ Citizen (เพื่อ backward compatibility)
     */
    public function verify_citizen($token, $min_score = null)
    {
        return $this->verify($token, 'citizen', $min_score);
    }

    /**
     * ✅ ฟังก์ชันย่อยสำหรับ Staff (เพื่อ backward compatibility)
     */
    public function verify_staff($token, $min_score = null)
    {
        return $this->verify($token, 'staff', $min_score);
    }

    /**
     * ✅ ฟังก์ชันย่อยสำหรับ Admin
     */
    public function verify_admin($token, $min_score = 0.8)
    {
        return $this->verify($token, 'admin', $min_score);
    }

    /**
     * ✅ ตรวจสอบแบบ Simple (return เฉพาะ boolean)
     */
    public function is_valid($token, $user_type = 'citizen', $min_score = null)
    {
        $result = $this->verify($token, $user_type, $min_score);
        return $result['success'];
    }

    /**
     * ✅ ดึงคะแนน reCAPTCHA
     */
    public function get_score($token, $user_type = 'citizen')
    {
        $result = $this->verify($token, $user_type);
        return isset($result['data']['score']) ? $result['data']['score'] : null;
    }

    /**
     * ✅ สร้าง JavaScript สำหรับ Frontend
     */
    public function generate_js($action = 'login', $callback = null)
    {
        $site_key = $this->config['site_key'];

        if (empty($site_key)) {
            return '<!-- reCAPTCHA: Site key not configured -->';
        }

        $callback_js = $callback ? "callback: {$callback}," : '';

        $js = "<script src=\"https://www.google.com/recaptcha/api.js?render={$site_key}\"></script>\n";
        $js .= "<script>\n";
        $js .= "function executeRecaptcha(action) {\n";
        $js .= "    return new Promise((resolve, reject) => {\n";
        $js .= "        grecaptcha.ready(() => {\n";
        $js .= "            grecaptcha.execute('{$site_key}', { action: action || '{$action}' })\n";
        $js .= "                .then(resolve)\n";
        $js .= "                .catch(reject);\n";
        $js .= "        });\n";
        $js .= "    });\n";
        $js .= "}\n";
        $js .= "</script>";

        return $js;
    }

    /**
     * ✅ ตั้งค่า Library
     */
    public function set_config($key, $value = null)
    {
        if (is_array($key)) {
            $this->config = array_merge($this->config, $key);
        } else {
            $this->config[$key] = $value;
        }

        return $this;
    }

    /**
     * ✅ ดึงค่า Configuration
     */
    public function get_config($key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    /**
     * ✅ เปิด/ปิด reCAPTCHA
     */
    public function enable($enabled = true)
    {
        $this->config['enabled'] = (bool) $enabled;
        return $this;
    }

    /**
     * ✅ ดึงสถิติการใช้งาน (ถ้ามี table logs)
     */
    public function get_stats($date_from = null, $date_to = null)
    {
        // ตัวอย่างการดึงสถิติจาก database
        if ($this->CI->db->table_exists('tbl_member_recaptcha_logs')) {
            $this->CI->db->select('
                user_type,
                COUNT(*) as total_attempts,
                SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful,
                AVG(score) as avg_score,
                AVG(response_time) as avg_response_time
            ');

            if ($date_from) {
                $this->CI->db->where('created_at >=', $date_from);
            }
            if ($date_to) {
                $this->CI->db->where('created_at <=', $date_to);
            }

            $this->CI->db->group_by('user_type');
            $result = $this->CI->db->get('tbl_member_recaptcha_logs')->result_array();

            return $result;
        }

        return array();
    }

    // =====================================
    // Private Helper Methods
    // =====================================

    /**
     * ตรวจสอบ Input ที่ส่งมา
     */
    private function _validate_inputs($token, $user_type)
    {
        // ตรวจสอบ token
        if (empty($token)) {
            return $this->_create_response(false, 'reCAPTCHA token is required');
        }

        if (strlen($token) < 100) {
            return $this->_create_response(false, 'reCAPTCHA token too short');
        }

        // ตรวจสอบ user type
        if (!in_array($user_type, $this->valid_user_types)) {
            return $this->_create_response(false, 'Invalid user type: ' . $user_type);
        }

        // ตรวจสอบ secret key
        if (empty($this->config['secret_key'])) {
            return $this->_create_response(false, 'reCAPTCHA secret key not configured');
        }

        return $this->_create_response(true, 'Validation passed');
    }

    /**
     * เรียก Google reCAPTCHA API
     */
    private function _call_recaptcha_api($token)
    {
        $post_data = http_build_query(array(
            'secret' => $this->config['secret_key'],
            'response' => $token,
            'remoteip' => $this->_get_client_ip()
        ));

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $post_data,
                'timeout' => $this->config['timeout']
            )
        );
        $context = stream_context_create($options);

        $start_time = microtime(true);
        $response_json = @file_get_contents($this->config['verify_url'], false, $context);
        $response_time = (microtime(true) - $start_time) * 1000;

        if ($response_json === FALSE) {
            return $this->_create_response(false, 'Cannot contact reCAPTCHA verification server');
        }

        $response_data = json_decode($response_json, true);
        if ($response_data === null) {
            return $this->_create_response(false, 'Invalid JSON response from reCAPTCHA server');
        }

        $response_data['api_response_time'] = round($response_time, 2);

        return $this->_create_response(true, 'API call successful', $response_data);
    }

    /**
     * ตรวจสอบผลลัพธ์จาก Google
     */
    private function _verify_response($response_data, $user_type, $min_score, $expected_action = null)
    {
        // ตรวจสอบ success flag
        if (!isset($response_data['success']) || !$response_data['success']) {
            $error_codes = isset($response_data['error-codes']) ? implode(', ', $response_data['error-codes']) : 'Unknown error';
            return $this->_create_response(false, 'reCAPTCHA verification failed: ' . $error_codes, $response_data);
        }

        // ตรวจสอบ score
        if (isset($response_data['score'])) {
            if ($response_data['score'] < $min_score) {
                return $this->_create_response(false, "Score too low: {$response_data['score']} (required: {$min_score})", $response_data);
            }
        }

        // ✅ ปรับปรุง: ตรวจสอบ action แบบยืดหยุ่น
        if (isset($response_data['action']) && $expected_action) {
            if ($response_data['action'] !== $expected_action) {
                $this->_log_debug("Action mismatch: {$response_data['action']} (expected: {$expected_action})");
                // ไม่ return false เพื่อความยืดหยุ่น แต่จะ log ไว้
            }
        }

        return $this->_create_response(true, 'reCAPTCHA verification successful', $response_data);
    }

    /**
     * กำหนด min_score ตาม user type
     */
    private function _get_min_score($user_type)
    {
        switch ($user_type) {
            case 'staff':
                return $this->config['min_score_staff'];
            case 'admin':
                return 0.8;
            case 'citizen':
            case 'user':
            default:
                return $this->config['min_score_citizen'];
        }
    }

    /**
     * กำหนด expected actions ตาม user type
     */
    private function _get_expected_actions($user_type)
    {
        $actions = array('login'); // default action

        switch ($user_type) {
            case 'staff':
                $actions[] = 'staff_login';
                break;
            case 'citizen':
                $actions[] = 'citizen_login';
                break;
            case 'admin':
                $actions[] = 'admin_login';
                break;
        }

        return $actions;
    }

    /**
     * ดึง Client IP Address
     */
    private function _get_client_ip()
    {
        if (method_exists($this->CI->input, 'ip_address')) {
            return $this->CI->input->ip_address();
        }

        // Fallback
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }

        return '0.0.0.0';
    }

    /**
     * สร้าง Response Array
     */
    private function _create_response($success, $message, $data = array())
    {
        return array(
            'success' => (bool) $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        );
    }

    /**
     * บันทึก Debug Log
     */
    private function _log_debug($message, $data = array())
    {
        if ($this->config['debug_mode']) {
            $log_message = "reCAPTCHA Debug: {$message}";
            if (!empty($data)) {
                $log_message .= " | Data: " . json_encode($data);
            }
            log_message('debug', $log_message);
        }
    }

    /**
     * บันทึก Info Log
     */
    private function _log_info($message, $data = array())
    {
        $log_message = "reCAPTCHA: {$message}";
        if (!empty($data)) {
            $log_message .= " | " . json_encode($data);
        }
        log_message('info', $log_message);
    }

    /**
     * บันทึก Error Log
     */
    private function _log_error($message, $data = array())
    {
        $log_message = "reCAPTCHA Error: {$message}";
        if (!empty($data)) {
            $log_message .= " | " . json_encode($data);
        }
        log_message('error', $log_message);
    }

    /**
     * บันทึกลง Database (ถ้าต้องการ)
     */
    private function _log_to_database($result, $user_type, $token_length, $action = null, $source = null)
    {
        if ($this->CI->db->table_exists('tbl_member_recaptcha_logs')) {
            $log_data = array(
                'user_type' => $user_type,
                'success' => $result['success'] ? 1 : 0,
                'score' => isset($result['data']['score']) ? $result['data']['score'] : null,
                'response_time' => isset($result['data']['response_time']) ? $result['data']['response_time'] : null,
                'ip_address' => $this->_get_client_ip(),
                'token_length' => $token_length,
                'action' => $action,
                'source' => $source,
                'created_at' => date('Y-m-d H:i:s')
            );

            $this->CI->db->insert('tbl_member_recaptcha_logs', $log_data);
        }
    }

    /**
     * ✅ ตรวจสอบ reCAPTCHA สำหรับ Staff Login (ผู้ดูแลระบบ)
     */
    public function verify_staff_login($token, $min_score = 0.7)
    {
        return $this->verify($token, 'staff', $min_score, [
            'action' => 'staff_login',
            'source' => 'admin_login_form'
        ]);
    }

    /**
     * ✅ ตรวจสอบ reCAPTCHA สำหรับ Citizen Login (ประชาชน)
     */
    public function verify_citizen_login($token, $min_score = 0.5)
    {
        return $this->verify($token, 'citizen', $min_score, [
            'action' => 'citizen_login',
            'source' => 'citizen_login_form'
        ]);
    }

    /**
     * ✅ ตรวจสอบ reCAPTCHA สำหรับ Assessment (แบบประเมิน)
     */
    public function verify_assessment($token, $min_score = 0.3)
    {
        return $this->verify($token, 'citizen', $min_score, [
            'action' => 'assessment_submit',
            'source' => 'assessment_form'
        ]);
    }

    /**
     * ✅ ตรวจสอบ reCAPTCHA สำหรับ Q&A Admin (ผู้ดูแลตอบคำถาม)
     */
    public function verify_qa_admin($token, $min_score = 0.7)
    {
        return $this->verify($token, 'admin', $min_score, [
            'action' => 'qa_admin_submit',
            'source' => 'qa_admin_form'
        ]);
    }

    /**
     * ✅ ตรวจสอบ reCAPTCHA สำหรับ Q&A Public (ประชาชนถามคำถาม)
     */
    public function verify_qa_public($token, $min_score = 0.4)
    {
        return $this->verify($token, 'citizen', $min_score, [
            'action' => 'qa_public_submit',
            'source' => 'qa_public_form'
        ]);
    }

    /**
     * ✅ ตรวจสอบ reCAPTCHA สำหรับ Contact/Complaint (ติดต่อ/ร้องเรียน)
     */
    public function verify_contact($token, $min_score = 0.4)
    {
        return $this->verify($token, 'citizen', $min_score, [
            'action' => 'contact_submit',
            'source' => 'contact_form'
        ]);
    }
}