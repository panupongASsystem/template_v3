<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat extends CI_Controller
{
    private $apiKey;
    private $model;
    private $endpoint;
    private $systemPrompt;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'security']);
        $this->load->library(['session']);
        $this->load->model('Chat_model');

        // โหลด Gemini config จาก database หรือ config file
        $this->load->config('gemini');

        $this->apiKey = $this->config->item('gemini_api_key');
        $this->model = $this->Chat_model->get_config('gemini_model', $this->config->item('gemini_model'));
        $this->endpoint = $this->config->item('gemini_endpoint');

        // System prompt จาก database
        $this->systemPrompt = $this->Chat_model->get_config(
            'system_prompt',
            'คุณคือ "ผู้ช่วยญาดา" ผู้ช่วยตอบคำถามทั่วไปเกี่ยวกับองค์การบริหารส่วนตำบล (อบต./เทศบาล)  
- ตอบเป็นภาษาไทยด้วยน้ำเสียงสุภาพ เป็นมิตร และเข้าใจง่าย  
- อธิบายข้อมูลเบื้องต้น เช่น บทบาท หน้าที่ การบริการประชาชน ขั้นตอนการติดต่อ และสิทธิของชาวบ้าน  
- หากเป็นคำถามที่ไม่เกี่ยวข้องกับ อบต./เทศบาล หรือเกินขอบเขต ไม่ควรตอบ ให้แนะนำกลับไปที่หน่วยงานที่เกี่ยวข้องแทน และสำคัญต้องตอบ เฉพาะเกี่ยวกับ
'
        );

        // ดึงค่าต่าง ๆ จาก config
        $orgName     = trim(strip_tags((string) get_config_value('fname', '')));
        $subdistric = trim(strip_tags((string) get_config_value('subdistric', '')));
        $district    = trim(strip_tags((string) get_config_value('district', '')));
        $province    = trim(strip_tags((string) get_config_value('province', '')));

        // ประกอบข้อความ
        $locationInfo = [];
        if ($orgName !== '')     $locationInfo[] = $orgName;
        if ($subdistric !== '') $locationInfo[] = 'ต.' . $subdistric;
        if ($district !== '')    $locationInfo[] = 'อ.' . $district;
        if ($province !== '')    $locationInfo[] = 'จ.' . $province;

        // ถ้ามีข้อมูลอย่างน้อยหนึ่งค่า -> ต่อเข้ากับ systemPrompt
        if (!empty($locationInfo)) {
            $this->systemPrompt .= " " . implode(' ', $locationInfo);
        }


        log_message('info', '[Gemini] Chat controller initialized with DB config');
    }

    public function debug_orgname()
    {
        $orgName = trim(strip_tags((string)get_config_value('fname', '')));
        if ($orgName !== '') {
        }

        // แสดงผล + หยุดการทำงาน
        echo "<pre>SystemPrompt (final)\n";
        print_r($this->systemPrompt);
        echo "</pre>";
        exit();
    }


    /**
     * สร้าง Guest ID หากยังไม่มี
     */
    private function getGuestOrMemberId()
    {
        // ถ้าเป็น member ที่ login แล้ว
        $m_id = $this->session->userdata('m_id');
        if (!empty($m_id)) {
            log_message('info', '[Gemini] Using member ID: ' . $m_id);
            return 'member_' . $m_id;
        }

        // ถ้าเป็น guest - สร้าง guest ID
        $guest_id = $this->session->userdata('guest_id');
        if (empty($guest_id)) {
            $guest_id = 'guest_' . time() . '_' . rand(1000, 9999);
            $this->session->set_userdata('guest_id', $guest_id);
            log_message('info', '[Gemini] Created new guest ID: ' . $guest_id);
        } else {
            log_message('info', '[Gemini] Using existing guest ID: ' . $guest_id);
        }

        return $guest_id;
    }

    /**
     * ดึง IP address ของผู้ใช้
     */
    private function getClientIpAddress()
    {
        $ipkeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($ipkeys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Main endpoint สำหรับแชท
     */
    public function gemini()
    {
        log_message('info', '[Gemini] gemini() method called - Request method: ' . $_SERVER['REQUEST_METHOD']);

        // ตรวจสอบ method
        if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
            log_message('warning', '[Gemini] Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['ok' => false, 'error' => 'Method Not Allowed']));
        }

        // ตรวจสอบ API Key
        if (empty($this->apiKey)) {
            log_message('error', '[Gemini] API Key not configured');
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['ok' => false, 'error' => 'API Key ไม่ได้ตั้งค่า']));
        }

        // ดึง user ID และข้อมูล
        $user_id = $this->getGuestOrMemberId();
        $user_type = strpos($user_id, 'member_') === 0 ? 'member' : 'guest';
        $ip_address = $this->getClientIpAddress();

        log_message('info', '[Gemini] Processing request for user: ' . $user_id . ' (' . $user_type . ')');

        // ตรวจสอบ rate limit
        $rate_check = $this->Chat_model->check_rate_limit($user_id, $user_type, $ip_address);

        if (!$rate_check['allowed']) {
            log_message('warning', '[Gemini] Rate limit exceeded for user: ' . $user_id);

            $error_messages = $this->Chat_model->get_config('error_messages', []);
            $error_message = isset($error_messages['rate_limit']) ?
                str_replace('{time}', $rate_check['wait_time'], $error_messages['rate_limit']) :
                'ใช้งานเกินขีดจำกัด กรุณารอ ' . $rate_check['wait_time'] . ' วินาที';

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(429)
                ->set_output(json_encode([
                    'ok' => false,
                    'error' => $error_message,
                    'rate_limit' => [
                        'current_count' => $rate_check['current_count'],
                        'limit' => $rate_check['limit'],
                        'wait_time' => $rate_check['wait_time'],
                        'reset_time' => $rate_check['reset_time']
                    ]
                ]));
        }

        // รับ JSON payload
        $raw = $this->input->raw_input_stream;
        log_message('info', '[Gemini] Raw input length: ' . strlen($raw));

        $payload = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', '[Gemini] JSON decode error: ' . json_last_error_msg());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['ok' => false, 'error' => 'Invalid JSON format']));
        }

        $userMessage = isset($payload['message']) ? trim($payload['message']) : '';
        $clientHistory = isset($payload['history']) ? $payload['history'] : [];

        log_message('info', '[Gemini] User message length: ' . strlen($userMessage));
        log_message('info', '[Gemini] Client history count: ' . count($clientHistory));

        if ($userMessage === '') {
            log_message('warning', '[Gemini] Empty message received');
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['ok' => false, 'error' => 'Message is required']));
        }

        // จำกัดความยาวข้อความจาก config
        $max_message_length = (int)$this->Chat_model->get_config('max_message_length', 4000);
        if (strlen($userMessage) > $max_message_length) {
            log_message('warning', '[Gemini] Message too long: ' . strlen($userMessage));
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['ok' => false, 'error' => 'ข้อความยาวเกินไป (ขีดจำกัด: ' . $max_message_length . ' ตัวอักษร)']));
        }

        // เตรียม conversation history
        $max_history = (int)$this->Chat_model->get_config('max_history_messages', 20);
        $history = $this->session->userdata('chat_history_' . $user_id) ?: [];
        $history = array_slice(array_merge($history, $clientHistory), -$max_history);
        $history[] = ['role' => 'user', 'content' => $userMessage];

        // เรียก Gemini API
        $startTime = microtime(true);
        $response = $this->callGeminiAPI($userMessage, $history);
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2);

        log_message('info', '[Gemini] API call completed in ' . $responseTime . 'ms');

        if ($response['success']) {
            $reply = $response['data'];

            // เก็บคำตอบลง history
            $history[] = ['role' => 'assistant', 'content' => $reply];
            $this->session->set_userdata('chat_history_' . $user_id, $history);

            // บันทึก log
            $this->Chat_model->log_conversation($user_id, $user_type, $userMessage, $reply, $responseTime / 1000, $ip_address);

            log_message('info', '[Gemini] Chat successful - Response length: ' . strlen($reply));

            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'ok' => true,
                    'reply' => $reply,
                    'rate_limit' => [
                        'remaining' => $rate_check['remaining'],
                        'limit' => $rate_check['limit']
                    ]
                ]));
        } else {
            // บันทึก error log
            $this->Chat_model->log_conversation($user_id, $user_type, $userMessage, null, $responseTime / 1000, $ip_address);

            log_message('error', '[Gemini] Chat failed: ' . $response['error']);

            return $this->output->set_content_type('application/json')
                ->set_status_header(502)
                ->set_output(json_encode(['ok' => false, 'error' => $response['error']]));
        }
    }

    /**
     * เรียก Gemini API
     */
    private function callGeminiAPI($userMessage, $history)
    {
        log_message('info', '[Gemini] callGeminiAPI started');

        // สร้าง conversation context
        $contextMessages = [];

        // เพิ่ม system prompt
        $contextMessages[] = "System: " . $this->systemPrompt;

        // เพิ่มประวัติการสนทนา (เอา 5 ข้อความล่าสุด)
        $recentHistory = array_slice($history, -6); // รวมข้อความปัจจุบัน
        foreach ($recentHistory as $msg) {
            if ($msg['role'] === 'user') {
                $contextMessages[] = "User: " . $msg['content'];
            } elseif ($msg['role'] === 'assistant') {
                $contextMessages[] = "Assistant: " . $msg['content'];
            }
        }

        $conversationContext = implode("\n\n", $contextMessages);

        // สร้าง API URL
        $url = sprintf($this->endpoint, $this->model, $this->apiKey);
        log_message('info', '[Gemini] API URL: ' . substr($url, 0, 100) . '...');

        // ดึงค่า config จาก database
        $temperature = (float)$this->Chat_model->get_config('gemini_temperature', 0.7);
        $maxTokens = (int)$this->Chat_model->get_config('gemini_max_tokens', 2000);

        // Payload สำหรับ Gemini
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $conversationContext
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => $maxTokens
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        log_message('info', '[Gemini] Payload prepared - conversation length: ' . strlen($conversationContext));

        $ch = curl_init($url);

        $curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT      => 'Khon Kaen Wellness Tourism/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ];

        curl_setopt_array($ch, $curlOptions);

        log_message('info', '[Gemini] Sending request to Gemini API...');

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);

        curl_close($ch);

        log_message('info', '[Gemini] cURL execution completed - HTTP Code: ' . $httpCode . ', Execution time: ' . $executionTime . 'ms');

        // จัดการ cURL errors
        if ($curlError) {
            log_message('error', '[Gemini] cURL Error: ' . $curlError);
            return [
                'success' => false,
                'error'   => 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
            ];
        }

        log_message('info', '[Gemini] Response received - Length: ' . strlen($response));

        $decodedResponse = json_decode($response, true);
        $jsonError = json_last_error();

        if ($jsonError !== JSON_ERROR_NONE) {
            log_message('error', '[Gemini] JSON decode error: ' . json_last_error_msg());
            log_message('error', '[Gemini] Raw response: ' . substr($response, 0, 500));
            return [
                'success' => false,
                'error'   => 'Invalid response format'
            ];
        }

        // จัดการ API errors
        if ($httpCode !== 200) {
            $errorMessage = $this->getGeminiErrorMessage($httpCode, $decodedResponse);
            log_message('error', '[Gemini] API Error (HTTP ' . $httpCode . '): ' . $errorMessage);
            log_message('error', '[Gemini] Error response: ' . substr($response, 0, 500));

            return [
                'success' => false,
                'error'   => $errorMessage
            ];
        }

        // ตรวจสอบ Gemini response structure
        if (!isset($decodedResponse['candidates'][0]['content']['parts'][0]['text'])) {
            log_message('error', '[Gemini] Invalid API Response Structure');
            log_message('error', '[Gemini] Response structure: ' . print_r($decodedResponse, true));

            // ตรวจสอบว่ามี error message หรือไม่
            if (isset($decodedResponse['error'])) {
                return [
                    'success' => false,
                    'error'   => 'Gemini API Error: ' . $decodedResponse['error']['message']
                ];
            }

            return [
                'success' => false,
                'error'   => 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'
            ];
        }

        $responseContent = $decodedResponse['candidates'][0]['content']['parts'][0]['text'];
        $usage = $decodedResponse['usageMetadata'] ?? null;

        log_message('info', '[Gemini] API call successful - Content length: ' . strlen($responseContent));

        if ($usage) {
            log_message('info', '[Gemini] Token usage - Prompt: ' . ($usage['promptTokenCount'] ?? 0) .
                ', Candidate: ' . ($usage['candidatesTokenCount'] ?? 0) .
                ', Total: ' . ($usage['totalTokenCount'] ?? 0));
        }

        return [
            'success' => true,
            'data'    => $responseContent,
            'usage'   => $usage
        ];
    }

    /**
     * แปล HTTP status code เป็นข้อความผิดพลาด
     */
    private function getGeminiErrorMessage($httpCode, $apiResponse)
    {
        log_message('info', '[Gemini] Getting error message for HTTP ' . $httpCode);

        // ดึงข้อความ error จาก config
        $error_messages = $this->Chat_model->get_config('error_messages', []);

        // ตรวจสอบ error จาก API response
        if (isset($apiResponse['error'])) {
            $errorDetails = $apiResponse['error'];
            log_message('error', '[Gemini] API Error Details: ' . print_r($errorDetails, true));

            switch ($errorDetails['code'] ?? $httpCode) {
                case 400:
                    return 'ข้อมูลที่ส่งไม่ถูกต้อง: ' . ($errorDetails['message'] ?? 'Bad Request');
                case 403:
                    return 'API Key ไม่ถูกต้องหรือไม่มีสิทธิ์เข้าใช้งาน';
                case 429:
                    return isset($error_messages['rate_limit']) ?
                        str_replace('{time}', '60', $error_messages['rate_limit']) :
                        'ใช้งานเกินขีดจำกัด กรุณารอสักครู่แล้วลองใหม่';
                case 500:
                    return 'เซิร์ฟเวอร์ Gemini มีปัญหา กรุณาลองใหม่ภายหลัง';
                default:
                    return 'เกิดข้อผิดพลาด: ' . ($errorDetails['message'] ?? 'Unknown error');
            }
        }

        // Generic HTTP error
        switch ($httpCode) {
            case 400:
                return isset($error_messages['invalid_input']) ? $error_messages['invalid_input'] :
                    'ข้อมูลที่ส่งไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
            case 403:
                return 'API Key ไม่ถูกต้อง กรุณาติดต่อผู้ดูแลระบบ';
            case 429:
                return isset($error_messages['rate_limit']) ?
                    str_replace('{time}', '60', $error_messages['rate_limit']) :
                    'ใช้งานเกินขีดจำกัด กรุณารอสักครู่แล้วลองใหม่';
            case 500:
                return isset($error_messages['system_error']) ? $error_messages['system_error'] :
                    'เซิร์ฟเวอร์มีปัญหา กรุณาลองใหม่ภายหลัง';
            default:
                return 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ (HTTP ' . $httpCode . ')';
        }
    }

    /**
     * ดึงข้อความต้อนรับ
     */
    public function get_welcome_messages()
    {
        header('Content-Type: application/json; charset=utf-8');

        $welcome_messages = $this->Chat_model->get_config('welcome_messages', [
            'สวัสดีค่ะ ผู้ช่วยญาดายินดีต้อนรับค่ะ',
            'สามารถให้คำแนะข้อมูลเบื้องต้นและคำถามทั่วไปเกี่ยวกับท้องถิ่นได้ค่ะ มีอะไรให้ช่วยเหลือไหมคะ'
        ]);

        echo json_encode([
            'ok' => true,
            'messages' => $welcome_messages
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * ทดสอบการเชื่อมต่อ Gemini API
     */
    public function test_gemini()
    {
        header('Content-Type: application/json; charset=utf-8');

        log_message('info', '[Gemini] API test started');

        $user_id = $this->getGuestOrMemberId();
        $rate_check = $this->Chat_model->check_rate_limit($user_id, 'guest', $this->getClientIpAddress());

        $result = [
            'timestamp' => date('Y-m-d H:i:s'),
            'config_status' => [
                'gemini_api_key' => !empty($this->apiKey) ? 'Loaded' : 'Missing',
                'gemini_model' => $this->model,
                'gemini_endpoint' => $this->endpoint,
                'system_prompt_length' => strlen($this->systemPrompt),
                'config_loaded' => $this->config->item('gemini_api_key') ? 'Yes' : 'No'
            ],
            'database_config' => [
                'system_prompt' => substr($this->systemPrompt, 0, 100) . '...',
                'rate_limit_requests' => $this->Chat_model->get_config('rate_limit_requests'),
                'rate_limit_window' => $this->Chat_model->get_config('rate_limit_window'),
                'max_message_length' => $this->Chat_model->get_config('max_message_length'),
                'gemini_temperature' => $this->Chat_model->get_config('gemini_temperature'),
                'gemini_max_tokens' => $this->Chat_model->get_config('gemini_max_tokens')
            ],
            'rate_limit_status' => $rate_check,
            'session_info' => [
                'session_id' => session_id(),
                'guest_id' => $this->session->userdata('guest_id'),
                'member_id' => $this->session->userdata('m_id'),
                'user_id_for_chat' => $user_id
            ],
            'environment' => [
                'base_url' => base_url(),
                'site_url' => site_url(),
                'chat_endpoint' => site_url('chat/gemini'),
                'test_endpoint' => site_url('chat/test_gemini'),
                'client_ip' => $this->getClientIpAddress()
            ]
        ];

        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * ดึงสถิติการใช้งาน (สำหรับ admin)
     */
    public function get_stats()
    {
        // ตรวจสอบสิทธิ์ admin (ปรับตามระบบของคุณ)
        if (!$this->session->userdata('is_admin')) {
            return $this->output
                ->set_status_header(403)
                ->set_output(json_encode(['error' => 'Access denied']));
        }

        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $stats = $this->Chat_model->get_usage_stats(null, $date_from, $date_to);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'stats' => $stats], JSON_UNESCAPED_UNICODE);
    }

    /**
     * ล้างข้อมูลเก่า
     */
    public function cleanup($days = 30)
    {
        // ตรวจสอบว่าถูกเรียกจาก CLI หรือมีสิทธิ์ admin
        if (!is_cli() && !$this->session->userdata('is_admin')) {
            return $this->output
                ->set_status_header(403)
                ->set_output(json_encode(['error' => 'Access denied']));
        }

        $result = $this->Chat_model->cleanup_old_data($days);

        log_message('info', '[Gemini] Cleanup completed: ' . print_r($result, true));

        if (is_cli()) {
            echo "Cleanup completed:\n";
            echo "- Rate limits deleted: " . $result['rate_limits_deleted'] . "\n";
            echo "- Logs deleted: " . $result['logs_deleted'] . "\n";
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'result' => $result], JSON_UNESCAPED_UNICODE);
        }
    }
}

/* End of file Chat.php */
/* Location: ./application/controllers/Chat.php */