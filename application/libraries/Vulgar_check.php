<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Library สำหรับตรวจสอบคำไม่สุภาพ
 * ใช้สำหรับตรวจสอบข้อความก่อนบันทึกลงฐานข้อมูล
 */
class Vulgar_check
{
    // CI Instance
    protected $CI;

    // API Key สำหรับเชื่อมต่อกับระบบตรวจสอบคำไม่สุภาพ
    private $api_key;

    // URL ของ API endpoint
    private $api_url;

    /**
     * Constructor
     */
    public function __construct()
    {
        // เรียกใช้งาน CI Instance
        $this->CI =& get_instance();
        log_message('debug', 'Vulgar_check Library Class Initialized.');

        // โหลดไฟล์ config และ helpers
        $this->CI->config->load('vulgar_config', TRUE);
        $this->CI->load->helper('security'); // โหลด security helper เพื่อใช้ xss_clean

        $this->api_key = $this->CI->config->item('api_key', 'vulgar_config') ?: '3fb13c3115fb7ef3cb0f2e2566a0b8c60a2d0d7489d9481b26559331e0e6cbcc';
        $this->api_url = $this->CI->config->item('api_url', 'vulgar_config') ?: 'https://assystem.co.th/vulgar_word_api/check';

        // Log ค่า config ที่ใช้
        log_message('debug', 'Vulgar_check: API URL set to: ' . $this->api_url);
        log_message('debug', 'Vulgar_check: API Key loaded (first 8 chars): ' . substr($this->api_key, 0, 8));
    }

    /**
     * ตรวจสอบคำไม่สุภาพในข้อความ (สำหรับ Form Validation Callback)
     * * @param string $text ข้อความที่ต้องการตรวจสอบ
     * @return boolean true หากไม่พบคำไม่สุภาพ, false หากพบ
     */
    public function check_vulgar($text)
    {
        log_message('debug', '--- Running check_vulgar (callback) for text: "' . $text . '" ---');

        if (empty($text)) {
            log_message('debug', 'check_vulgar: Text is empty, returning true.');
            return true;
        }

        // 1. ตรวจสอบจาก Local DB
        log_message('debug', 'check_vulgar: Checking local DB...');
        $local_result = $this->_check_from_local_db($text);

        if (isset($local_result['data']['has_vulgar_words']) && $local_result['data']['has_vulgar_words']) {
            $vulgar_words_str = html_escape(implode(', ', $local_result['data']['vulgar_words']));
            log_message('info', 'check_vulgar: Vulgar word found in local DB: ' . $vulgar_words_str);
            $this->CI->form_validation->set_message('check_vulgar', 'ตรวจพบคำไม่เหมาะสม: ' . $vulgar_words_str . ' กรุณาแก้ไขข้อความของท่าน');
            return false;
        }
        log_message('debug', 'check_vulgar: No vulgar words in local DB. Proceeding to API check.');

        // 2. ตรวจสอบจาก API
        log_message('debug', 'check_vulgar: Checking with API...');
        $api_result = $this->_check_with_api($text);

        // [IMPROVED] เปลี่ยนเป็น Fail-Closed: ถ้า API ขัดข้อง จะไม่ให้ผ่าน
        if (!isset($api_result['status']) || $api_result['status'] !== 'success') {
            log_message('error', 'check_vulgar: The vulgar word API service is currently unavailable. Blocking submission. API Response: ' . json_encode($api_result));
            $this->CI->form_validation->set_message('check_vulgar', 'ระบบตรวจสอบคำไม่สุภาพขัดข้องชั่วคราว กรุณาลองใหม่อีกครั้งในภายหลัง');
            return false; // บล็อกการส่งข้อมูลถ้า API มีปัญหา
        }

        if (isset($api_result['data']['has_vulgar_words']) && $api_result['data']['has_vulgar_words']) {
            $vulgar_words_str = html_escape(implode(', ', $api_result['data']['vulgar_words']));
            log_message('info', 'check_vulgar: Vulgar word found via API: ' . $vulgar_words_str);
            $this->CI->form_validation->set_message('check_vulgar', 'ตรวจพบคำไม่เหมาะสม: ' . $vulgar_words_str . ' กรุณาแก้ไขข้อความของท่าน');
            return false;
        }

        log_message('debug', 'check_vulgar: No vulgar words found via API. Text is clean.');
        return true;
    }

/**
 * ตรวจสอบคำไม่สุภาพและ URL ในฟอร์มทั้งฟอร์ม
 * @param array $form_data ข้อมูลฟอร์ม
 * @param array $fields_to_check ฟิลด์ที่ต้องการตรวจสอบ
 * @return array ผลการตรวจสอบ
 */
public function check_form($form_data, $fields_to_check = null)
{
    log_message('info', '--- Running check_form (Enhanced with URL Check) ---');
    if (empty($form_data)) {
        log_message('error', 'check_form: Form data is empty.');
        return ['status' => 'error', 'message' => 'ไม่พบข้อมูลฟอร์ม'];
    }
    
    if ($fields_to_check === null) {
        $fields_to_check = ['q_a_msg', 'q_a_by', 'q_a_detail', 'q_a_email', 'q_a_reply_by', 'q_a_reply_detail', 'q_a_reply_email'];
    }
    log_message('debug', 'check_form: Fields to check: ' . implode(', ', $fields_to_check));
    
    $results = [];
    $has_vulgar = false;
    $has_url = false; // *** เพิ่ม: ตัวแปรสำหรับตรวจสอบ URL ***
    $url_detected_fields = array(); // *** เพิ่ม: เก็บฟิลด์ที่พบ URL ***
    
    foreach ($fields_to_check as $field) {
        if (isset($form_data[$field]) && !empty($form_data[$field])) {
            log_message('debug', 'check_form: Checking field "' . $field . '"...');
            
            // *** เพิ่ม: ตรวจสอบ URL ก่อนตรวจสอบ vulgar (ยกเว้นฟิลด์อีเมล) ***
            if (!in_array($field, ['q_a_email', 'q_a_reply_email'])) {
                log_message('debug', 'check_form: Checking URL in field "' . $field . '"...');
                if (!$this->check_no_urls($form_data[$field])) {
                    $has_url = true;
                    $url_detected_fields[] = $field;
                    log_message('debug', 'check_form: URL detected in field "' . $field . '": ' . $form_data[$field]);
                    
                    $results[$field] = [
                        'has_vulgar' => false,
                        'has_url' => true,
                        'original_text' => $form_data[$field]
                    ];
                    continue; // พบ URL แล้ว ไม่ต้องเช็ค vulgar
                }
            }
            
            // 1. ตรวจสอบจาก local DB
            $local_result = $this->_check_from_local_db($form_data[$field]);
            if (isset($local_result['data']['has_vulgar_words']) && $local_result['data']['has_vulgar_words']) {
                log_message('debug', 'check_form: Vulgar word found in "' . $field . '" from local DB.');
                $has_vulgar = true;
                $results[$field] = [
                    'has_vulgar' => true, 
                    'has_url' => false,
                    'original_text' => $form_data[$field], 
                    'censored_text' => $local_result['data']['censored_content'], 
                    'vulgar_words' => $local_result['data']['vulgar_words']
                ];
                continue; // พบแล้ว ไม่ต้องเช็ค API
            }
            
            // 2. ตรวจสอบจาก API
            $api_result = $this->_check_with_api($form_data[$field]);
            if (isset($api_result['status'], $api_result['data']['has_vulgar_words']) && $api_result['status'] === 'success' && $api_result['data']['has_vulgar_words']) {
                log_message('debug', 'check_form: Vulgar word found in "' . $field . '" from API.');
                $has_vulgar = true;
                $results[$field] = [
                    'has_vulgar' => true, 
                    'has_url' => false,
                    'original_text' => $form_data[$field], 
                    'censored_text' => $api_result['data']['censored_content'], 
                    'vulgar_words' => $api_result['data']['vulgar_words']
                ];
            } else {
                $results[$field] = [
                    'has_vulgar' => false, 
                    'has_url' => false,
                    'original_text' => $form_data[$field]
                ];
            }
        }
    }
    
    $final_result = [
        'status' => 'success', 
        'has_vulgar' => $has_vulgar, 
        'has_url' => $has_url, // *** เพิ่ม: ผลการตรวจสอบ URL ***
        'block_submit' => ($has_vulgar || $has_url), // *** แก้ไข: บล็อกถ้าพบ vulgar หรือ URL ***
        'results' => $results,
        'url_detected_fields' => $url_detected_fields // *** เพิ่ม: ฟิลด์ที่พบ URL ***
    ];
    
    log_message('info', 'check_form: Finished. Overall has_vulgar: ' . ($has_vulgar ? 'Yes' : 'No') . ', has_url: ' . ($has_url ? 'Yes' : 'No'));
    if ($has_url) {
        log_message('debug', 'check_form: URLs detected in fields: ' . implode(', ', $url_detected_fields));
    }
    log_message('debug', 'check_form: Final result: ' . json_encode($final_result));
    
    return $final_result;
}

    /**
     * ตรวจสอบคำไม่สุภาพในข้อความหนึ่งข้อความ
     * * @param string $text ข้อความที่ต้องการตรวจสอบ
     * @return array ผลการตรวจสอบ
     */
    public function check_text($text)
    {
        log_message('info', '--- Running check_text for: "' . substr($text, 0, 100) . '..." ---');
        if (empty($text)) {
            log_message('debug', 'check_text: Input text is empty.');
            return ['status' => 'error', 'message' => 'กรุณาระบุข้อความที่ต้องการตรวจสอบ'];
        }

        // 1. ตรวจสอบจาก Local DB
        log_message('debug', 'check_text: Checking local DB...');
        $local_result = $this->_check_from_local_db($text);
        if (isset($local_result['data']['has_vulgar_words']) && $local_result['data']['has_vulgar_words']) {
            log_message('info', 'check_text: Vulgar word found in local DB. Returning result.');
            return $local_result;
        }

        // 2. ตรวจสอบจาก API
        log_message('debug', 'check_text: No vulgar words in local DB. Checking with API...');
        return $this->_check_with_api($text);
    }

    /**
     * ส่งข้อความไปตรวจสอบที่ API
     * * @param string $text ข้อความที่ต้องการตรวจสอบ
     * @return array ผลลัพธ์จาก API
     */
    private function _check_with_api($text)
    {
        log_message('debug', '_check_with_api: Sending text to API: "' . substr($text, 0, 100) . '..."');

        $ch = curl_init();
        $post_data = ['api_key' => $this->api_key, 'content' => $text];

        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            log_message('error', '_check_with_api: cURL Error: ' . $error);
            return ['status' => 'error', 'message' => 'cURL Error: ' . $error];
        }

        log_message('debug', '_check_with_api: API responded with HTTP Code: ' . $http_code);
        log_message('debug', '_check_with_api: API raw response: ' . $response);

        if ($http_code != 200) {
            log_message('error', '_check_with_api: API HTTP Status Code is not 200. It is ' . $http_code);
            return ['status' => 'error', 'message' => 'API returned non-200 status code: ' . $http_code];
        }

        $result = json_decode($response, true);
        if ($result === null) {
            log_message('error', '_check_with_api: Failed to decode JSON from API response.');
            return ['status' => 'error', 'message' => 'Invalid JSON response from API'];
        }

        if (!isset($result['status'])) {
            log_message('error', '_check_with_api: API response missing "status" key.');
            return ['status' => 'error', 'message' => 'API response format is incorrect.'];
        }

        // แก้ไข: เพิ่ม detailed logging สำหรับการทดสอบ
        if (isset($result['data']['has_vulgar_words']) && $result['data']['has_vulgar_words']) {
            $vulgar_words = $result['data']['vulgar_words'] ?? [];

            log_message('debug', '_check_with_api: API detected vulgar words: ' . json_encode($vulgar_words, JSON_UNESCAPED_UNICODE));
            log_message('debug', '_check_with_api: Original text: "' . $text . '"');
            log_message('debug', '_check_with_api: Full API response: ' . json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        log_message('info', '_check_with_api: API check completed. Has vulgar: ' . (isset($result['data']['has_vulgar_words']) && $result['data']['has_vulgar_words'] ? 'Yes' : 'No'));
        return $result;
    }

/**
 * ตรวจสอบว่ามี URL ในข้อความหรือไม่ (สำหรับ Form Validation Callback)
 * * @param string $text ข้อความที่ต้องการตรวจสอบ
 * @return boolean true หากไม่พบ URL, false หากพบ
 */
public function check_no_urls($text)
{
    log_message('debug', '--- Running check_no_urls (callback) for text: "' . $text . '" ---');
    if (empty($text)) {
        log_message('debug', 'check_no_urls: Text is empty, returning true.');
        return true;
    }

    // ตรวจสอบว่าเป็นฟิลด์อีเมลหรือไม่ (CI3 specific)
    $field_name = '';
    
    // *** แก้ไข: ตรวจสอบว่า form_validation ถูกโหลดแล้วหรือไม่ ***
    if (isset($this->CI->form_validation) && isset($this->CI->form_validation->_field_data)) {
        foreach ($this->CI->form_validation->_field_data as $field => $data) {
            if (isset($data['postdata']) && $data['postdata'] === $text) {
                $field_name = $field;
                log_message('debug', 'check_no_urls: Matched text to field: ' . $field_name);
                break;
            }
        }
    } else {
        log_message('debug', 'check_no_urls: Form validation not loaded or no field data available');
    }

    // ถ้าเป็นฟิลด์อีเมลโดยเฉพาะ ให้ข้ามการตรวจสอบ URL
    if ($field_name === 'q_a_email' || $field_name === 'q_a_reply_email') {
        log_message('debug', 'check_no_urls: Field is an email field. Bypassing URL check.');
        return true;
    }

    // ถ้าทั้งข้อความเป็นอีเมล ก็ให้ผ่าน
    if (filter_var(trim($text), FILTER_VALIDATE_EMAIL)) {
        log_message('debug', 'check_no_urls: Entire text is a valid email. Bypassing URL check.');
        return true;
    }

    // ลบอีเมลออกจากข้อความก่อนตรวจสอบ URL อื่นๆ
    $text_without_emails = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '', $text);

    $url_patterns = [
        'http(s)://' => '/https?:\/\//i',
        'www.' => '/\bwww\.[a-z0-9-]+(\.[a-z0-9-]+)*/i',
        'domain.tld' => '/\b[a-z0-9-]{2,}\.(com|net|org|info|io|co|th|biz|xyz|app|dev|me|asia)\b/i'
    ];

    foreach ($url_patterns as $name => $pattern) {
        if (preg_match($pattern, $text_without_emails)) {
            log_message('info', 'check_no_urls: URL-like pattern found. Pattern name: "' . $name . '", Text: "' . $text_without_emails . '"');
            
            // *** แก้ไข: ตรวจสอบว่า form_validation ถูกโหลดก่อนใช้ set_message ***
            if (isset($this->CI->form_validation)) {
                $this->CI->form_validation->set_message('check_no_urls', 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ');
            } else {
                log_message('debug', 'check_no_urls: Form validation not loaded, cannot set validation message');
            }
            
            return false;
        }
    }

    log_message('debug', 'check_no_urls: No URLs found. Text is clean.');
    return true;
}

    /**
     * ตรวจสอบคำไม่สุภาพจากฐานข้อมูล local
     * * @param string $text ข้อความที่ต้องการตรวจสอบ
     * @return array ผลการตรวจสอบ
     */
    private function _check_from_local_db($text)
    {
        log_message('debug', '_check_from_local_db: Starting check for text: "' . substr($text, 0, 100) . '..."');
        $result = ['status' => 'success', 'data' => ['has_vulgar_words' => false, 'vulgar_words' => [], 'censored_content' => $text]];

        try {
            $query = $this->CI->db->get('tbl_vulgar');

            if (!$query) {
                log_message('error', '_check_from_local_db: Database query failed.');
                return $result;
            }

            if ($query->num_rows() > 0) {
                log_message('debug', '_check_from_local_db: Found ' . $query->num_rows() . ' vulgar words in the database to check against.');
                $vulgar_words_list = $query->result_array();
                $found_words = [];
                $censored_text = $text;

                foreach ($vulgar_words_list as $word_data) {
                    $vulgar_word = isset($word_data['vulgar_com']) ? trim($word_data['vulgar_com']) : '';
                    if (empty($vulgar_word))
                        continue;

                    $pattern = '/' . preg_quote($vulgar_word, '/') . '/ui';

                    if (preg_match($pattern, $text)) {
                        $found_words[] = $vulgar_word;
                        $replacement = str_repeat('*', mb_strlen($vulgar_word, 'UTF-8'));
                        $censored_text = preg_replace($pattern, $replacement, $censored_text);
                        log_message('debug', '_check_from_local_db: Matched vulgar word: "' . $vulgar_word . '"');
                    }
                }

                if (!empty($found_words)) {
                    $result['data']['has_vulgar_words'] = true;
                    $result['data']['vulgar_words'] = array_unique($found_words);
                    $result['data']['censored_content'] = $censored_text;
                    log_message('info', '_check_from_local_db: Found total ' . count($result['data']['vulgar_words']) . ' unique vulgar words.');
                }
            } else {
                log_message('debug', '_check_from_local_db: Vulgar words table is empty or query returned no results.');
            }
        } catch (Exception $e) {
            log_message('error', '_check_from_local_db: Exception caught - ' . $e->getMessage());
        }

        return $result;
    }
}
