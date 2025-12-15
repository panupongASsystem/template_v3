<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggestions extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // ป้องกันการแคชและการคัดลอกเนื้อหา
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('X-Frame-Options: DENY');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $this->output->set_header('X-XSS-Protection: 1; mode=block');
        $this->output->set_header('Referrer-Policy: same-origin');
        $this->output->set_header('Content-Disposition: inline');
        $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        // โหลด models ทั้งหมด (รวมเดิมและใหม่)
        $this->load->model('activity_model');
        $this->load->model('news_model');
        $this->load->model('announce_model');
        $this->load->model('order_model');
        $this->load->model('procurement_model');
        $this->load->model('mui_model');
        $this->load->model('guide_work_model');
        $this->load->model('loadform_model');
        $this->load->model('pppw_model');
        $this->load->model('msg_pres_model');
        $this->load->model('history_model');
        $this->load->model('otop_model');
        $this->load->model('gci_model');
        $this->load->model('vision_model');
        $this->load->model('authority_model');
        $this->load->model('mission_model');
        $this->load->model('motto_model');
        $this->load->model('cmi_model');
        $this->load->model('executivepolicy_model');
        $this->load->model('travel_model');
        $this->load->model('si_model');

        // โหลด models สำหรับ navbar
        $this->load->model('HotNews_model');
        $this->load->model('Weather_report_model');
        $this->load->model('calender_model');
        $this->load->model('banner_model');
        $this->load->model('background_personnel_model');
        $this->load->model('member_public_model');

        // โหลด Suggestions model
        $this->load->model('Suggestions_model', 'suggestions_model');


        $this->load->library('recaptcha_lib');
        if (file_exists(APPPATH . 'config/recaptcha.php')) {
            $this->load->config('recaptcha');
            $recaptcha_config = $this->config->item('recaptcha');

            if ($recaptcha_config) {
                $this->recaptcha_lib->initialize($recaptcha_config);
                log_message('debug', 'reCAPTCHA Library initialized with config file');
            }
        }
    }

    // ===================================================================
    // *** หน้าแสดงฟอร์มส่งข้อเสนอแนะ ***
    // ===================================================================

    /**
     * ฟังก์ชันแสดงหน้าส่งข้อเสนอแนะ
     */
    public function adding_suggestions()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data();

            // *** ตรวจสอบการ redirect และ parameter ***
            $from_login = $this->input->get('from_login');
            $redirect_url = $this->input->get('redirect');

            $data['from_login'] = ($from_login === 'success');

            if ($redirect_url) {
                $this->session->set_userdata('redirect_after_login', $redirect_url);
                log_message('info', 'Redirect URL saved: ' . $redirect_url);
            }

            // *** ตรวจสอบสถานะ User Login แบบเดียวกับ Queue ***
            $current_user = $this->get_current_user_detailed();

            // *** เพิ่ม debug แบบเดียวกับ Queue ***
            log_message('debug', 'Suggestions - Current user status: ' . json_encode([
                'is_logged_in' => $current_user['is_logged_in'],
                'user_type' => $current_user['user_type'],
                'has_user_info' => !empty($current_user['user_info'])
            ]));

            $data['is_logged_in'] = $current_user['is_logged_in'];
            $data['user_info'] = $current_user['user_info'];
            $data['user_type'] = $current_user['user_type'];
            $data['user_address'] = $current_user['user_address'];

            // *** ข้อมูลเพิ่มเติมสำหรับหน้า ***
            $data['page_title'] = 'รับฟังความคิดเห็น';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'รับฟังความคิดเห็น', 'url' => '']
            ];

            // *** Flash Messages ***
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // *** Debug information แบบเดียวกับ Queue ***
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Suggestions - Adding suggestions page final data: ' . json_encode([
                    'is_logged_in' => $data['is_logged_in'],
                    'user_type' => $data['user_type'],
                    'from_login' => $data['from_login'],
                    'has_redirect' => !empty($redirect_url),
                    'has_user_info' => !empty($data['user_info']),
                    'has_user_address' => !empty($data['user_address']),
                    'session_mp_id' => $this->session->userdata('mp_id'),
                    'session_m_id' => $this->session->userdata('m_id')
                ]));
            }

            // โหลด view พร้อมส่งข้อมูลไป
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/suggestions', $data); // ส่ง $data ไปยัง view
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in adding_suggestions: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าส่งข้อเสนอแนะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    public function add_suggestions()
    {
        // ล้าง output buffer และบังคับ JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');

        ini_set('display_errors', 0);
        error_reporting(0);

        try {
            log_message('info', '=== SUGGESTIONS SUBMIT START (WITH RECAPTCHA) ===');
            log_message('info', 'POST: ' . print_r($_POST, true));
            log_message('info', 'FILES: ' . print_r($_FILES, true));

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** ⭐ เพิ่ม: ตรวจสอบ reCAPTCHA Token ***
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'suggestions_submit';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'suggestions_form';
            $user_type_detected = $this->input->post('user_type_detected') ?: 'guest';
            $is_ajax = $this->input->post('ajax_request') === '1';
            $dev_mode = $this->input->post('dev_mode') === '1';

            log_message('info', 'reCAPTCHA info for suggestions: ' . json_encode([
                'has_token' => !empty($recaptcha_token),
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => $user_type_detected,
                'is_ajax' => $is_ajax,
                'dev_mode' => $dev_mode
            ]));

            // *** ⭐ เพิ่ม: ตรวจสอบ reCAPTCHA (ยกเว้นโหมด development) ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                // *** เตรียม options สำหรับ suggestions ***
                $recaptcha_options = [
                    'action' => $recaptcha_action,
                    'source' => $recaptcha_source,
                    'user_type_detected' => $user_type_detected,
                    'form_source' => 'suggestions_submission',
                    'client_timestamp' => $this->input->post('client_timestamp'),
                    'user_agent_info' => $this->input->post('user_agent_info')
                ];

                // *** กำหนด user_type ที่ถูกต้องสำหรับ Library ***
                $library_user_type = 'citizen'; // default for suggestions
                if ($user_type_detected === 'staff' || $user_type_detected === 'admin') {
                    $library_user_type = 'staff';
                }

                // *** เรียกใช้ reCAPTCHA verification ***
                $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $library_user_type, null, $recaptcha_options);

                log_message('info', 'reCAPTCHA verification result for suggestions: ' . json_encode([
                    'success' => $recaptcha_result['success'],
                    'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                    'action' => $recaptcha_action,
                    'source' => $recaptcha_source,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]));

                // *** ตรวจสอบผลลัพธ์ reCAPTCHA ***
                if (!$recaptcha_result['success']) {
                    log_message('info', 'reCAPTCHA verification failed for suggestions: ' . json_encode([
                        'message' => $recaptcha_result['message'],
                        'user_type_detected' => $user_type_detected,
                        'library_user_type' => $library_user_type,
                        'action' => $recaptcha_action,
                        'source' => $recaptcha_source,
                        'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A'
                    ]));

                    echo json_encode([
                        'success' => false,
                        'message' => 'การยืนยันตัวตนไม่ผ่าน: ' . $recaptcha_result['message'],
                        'error_type' => 'recaptcha_failed',
                        'recaptcha_data' => $recaptcha_result['data']
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                log_message('info', 'reCAPTCHA verification successful for suggestions: ' . json_encode([
                    'score' => $recaptcha_result['data']['score'],
                    'action' => $recaptcha_action,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]));

            } else if (!$dev_mode) {
                // *** ไม่มี reCAPTCHA token ***
                log_message('info', 'No reCAPTCHA token provided for suggestions');

                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการยืนยันตัวตน',
                    'error_type' => 'recaptcha_missing'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                log_message('info', 'reCAPTCHA check skipped for suggestions (dev_mode)');
            }

            // *** ⭐ เพิ่ม: ตรวจสอบคำหยาบและ URL (สำหรับ suggestions) ***
            $suggestions_topic = trim($this->input->post('suggestions_topic'));
            $suggestions_detail = trim($this->input->post('suggestions_detail'));
            $combined_text = $suggestions_topic . ' ' . $suggestions_detail;

            // ตรวจสอบคำหยาบ
            if (method_exists($this, 'check_vulgar_word')) {
                $vulgar_result = $this->check_vulgar_word($combined_text);
                if ($vulgar_result['found']) {
                    log_message('info', 'Vulgar words detected in suggestions: ' . json_encode([
                        'vulgar_words' => $vulgar_result['words'],
                        'topic' => $suggestions_topic
                    ]));

                    echo json_encode([
                        'success' => false,
                        'vulgar_detected' => true,
                        'vulgar_words' => $vulgar_result['words'],
                        'message' => 'พบคำไม่เหมาะสมในความคิดเห็น',
                        'error_type' => 'vulgar_content'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            // ตรวจสอบ URL
            if (method_exists($this, 'check_no_urls')) {
                $url_result = $this->check_no_urls($combined_text);
                if ($url_result['found']) {
                    log_message('info', 'URLs detected in suggestions: ' . json_encode([
                        'urls' => $url_result['urls'],
                        'topic' => $suggestions_topic
                    ]));

                    echo json_encode([
                        'success' => false,
                        'url_detected' => true,
                        'urls' => $url_result['urls'],
                        'message' => 'ไม่อนุญาตให้มี URL หรือลิงก์ในความคิดเห็น',
                        'error_type' => 'url_content'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            $this->load->library('form_validation');

            $post_data = $this->input->post();
            log_message('debug', 'Suggestions - POST Data: ' . json_encode($post_data));

            $current_user = $this->get_current_user_detailed();

            // *** สร้าง custom suggestion_id ก่อนเป็นอันดับแรก ***
            $suggestion_id = $this->generate_suggestion_id();
            log_message('info', 'Generated custom suggestion ID: ' . $suggestion_id);

            // กำหนด validation rules พื้นฐาน
            $validation_rules = [
                [
                    'field' => 'suggestions_topic',
                    'label' => 'เรื่องที่ต้องการเสนอแนะ',
                    'rules' => 'trim|required|min_length[4]|max_length[200]'
                ],
                [
                    'field' => 'suggestions_detail',
                    'label' => 'รายละเอียด',
                    'rules' => 'trim|required|min_length[10]|max_length[2000]'
                ]
            ];

            // ถ้าไม่ได้ login ให้ validate ข้อมูลส่วนตัว
            if (!$current_user['is_logged_in']) {
                $personal_rules = [
                    [
                        'field' => 'suggestions_by',
                        'label' => 'ชื่อ-นามสกุล',
                        'rules' => 'trim|required|min_length[4]|max_length[100]'
                    ],
                    [
                        'field' => 'suggestions_phone',
                        'label' => 'เบอร์โทรศัพท์',
                        'rules' => 'trim|required|exact_length[10]|numeric'
                    ],
                    [
                        'field' => 'suggestions_number',
                        'label' => 'เลขบัตรประชาชน',
                        'rules' => 'trim|required|exact_length[13]|numeric|callback_validate_thai_id_card'
                    ],
                    [
                        'field' => 'suggestions_address',
                        'label' => 'ที่อยู่',
                        'rules' => 'trim|required|min_length[2]|max_length[500]'
                    ]
                ];
                $validation_rules = array_merge($validation_rules, $personal_rules);
            }

            // Email validation (optional)
            if (!empty($this->input->post('suggestions_email'))) {
                $validation_rules[] = [
                    'field' => 'suggestions_email',
                    'label' => 'อีเมล',
                    'rules' => 'trim|valid_email|max_length[255]'
                ];
            }

            $this->form_validation->set_rules($validation_rules);

            if ($this->form_validation->run() == FALSE) {
                log_message('info', 'Validation failed for suggestions: ' . validation_errors());
                echo json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ถูกต้อง',
                    'errors' => strip_tags(validation_errors())
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** เตรียมข้อมูลให้ครบถ้วนพร้อม custom ID ***
            $suggestion_data = [
                'suggestions_id' => $suggestion_id, // *** กำหนด custom ID ***
                'suggestion_type' => $this->input->post('suggestion_type') ?: 'suggestion',
                'suggestions_topic' => $this->input->post('suggestions_topic'),
                'suggestions_detail' => $this->input->post('suggestions_detail'),
                'suggestions_ip_address' => $this->input->ip_address(),
                'suggestions_user_agent' => $this->input->user_agent(),
                'suggestions_is_anonymous' => 0,
                'suggestions_status' => 'received',
                'suggestions_priority' => 'normal'
            ];

            // *** ⭐ เพิ่ม: เพิ่มข้อมูล reCAPTCHA ลงใน suggestion_data ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                $suggestion_data['recaptcha_verified'] = 1;
                $suggestion_data['recaptcha_score'] = isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : null;
                $suggestion_data['recaptcha_action'] = $recaptcha_action;
                $suggestion_data['verification_method'] = 'recaptcha_v3';
                log_message('info', 'Added reCAPTCHA data to suggestions: score=' . $suggestion_data['recaptcha_score']);
            } else {
                $suggestion_data['recaptcha_verified'] = 0;
                $suggestion_data['verification_method'] = $dev_mode ? 'dev_mode_skip' : 'none';
                log_message('info', 'No reCAPTCHA verification for suggestions (dev_mode or no token)');
            }

            // เพิ่มข้อมูลตาม user type
            if ($current_user['is_logged_in']) {
                if ($current_user['user_type'] === 'public') {
                    $suggestion_data['suggestions_user_id'] = $current_user['user_info']['id'];
                    $suggestion_data['suggestions_user_type'] = 'public';
                    $suggestion_data['suggestions_by'] = $current_user['user_info']['name'];
                    $suggestion_data['suggestions_phone'] = $current_user['user_info']['phone'];
                    $suggestion_data['suggestions_email'] = $current_user['user_info']['email'];
                    $suggestion_data['suggestions_number'] = $current_user['user_info']['number'] ?: $this->input->post('suggestions_number');

                    // ใช้ที่อยู่จาก user profile หรือที่กรอกในฟอร์ม
                    if (!empty($current_user['user_address']['full_address'])) {
                        $suggestion_data['suggestions_address'] = $current_user['user_address']['full_address'];
                        // เพิ่มข้อมูลที่อยู่แยกจาก user profile
                        if (isset($current_user['user_address']['parsed'])) {
                            $parsed = $current_user['user_address']['parsed'];
                            $suggestion_data['guest_province'] = $parsed['province'] ?? '';
                            $suggestion_data['guest_amphoe'] = $parsed['amphoe'] ?? '';
                            $suggestion_data['guest_district'] = $parsed['district'] ?? '';
                            $suggestion_data['guest_zipcode'] = $parsed['zipcode'] ?? '';
                        }
                    } else {
                        $suggestion_data['suggestions_address'] = $this->input->post('suggestions_address') ?: 'ไม่ระบุ';
                    }

                } elseif ($current_user['user_type'] === 'staff') {
                    $suggestion_data['suggestions_user_id'] = $current_user['user_info']['id'];
                    $suggestion_data['suggestions_user_type'] = 'staff';
                    $suggestion_data['suggestions_by'] = $current_user['user_info']['name'];
                    $suggestion_data['suggestions_phone'] = $current_user['user_info']['phone'];
                    $suggestion_data['suggestions_email'] = $current_user['user_info']['email'];
                    $suggestion_data['suggestions_address'] = $this->input->post('suggestions_address') ?: 'เจ้าหน้าที่';
                    $suggestion_data['suggestions_number'] = ''; // Staff ไม่ต้องมีเลขบัตรประชาชน
                }
            } else {
                // Guest user
                $suggestion_data['suggestions_user_type'] = 'guest';
                $suggestion_data['suggestions_by'] = $this->input->post('suggestions_by');
                $suggestion_data['suggestions_phone'] = $this->input->post('suggestions_phone');
                $suggestion_data['suggestions_email'] = $this->input->post('suggestions_email') ?: null;
                $suggestion_data['suggestions_number'] = $this->input->post('suggestions_number');
                $suggestion_data['suggestions_address'] = $this->input->post('suggestions_address');

                // เพิ่มข้อมูลที่อยู่แยกสำหรับ guest
                $suggestion_data['guest_province'] = $this->input->post('guest_province') ?: '';
                $suggestion_data['guest_amphoe'] = $this->input->post('guest_amphoe') ?: '';
                $suggestion_data['guest_district'] = $this->input->post('guest_district') ?: '';
                $suggestion_data['guest_zipcode'] = $this->input->post('guest_zipcode') ?: '';
            }

            log_message('debug', 'Suggestions - Suggestion Data: ' . json_encode($suggestion_data));

            // *** บันทึกลงฐานข้อมูลแบบปลอดภัย ***
            $this->db->trans_start();

            // *** ส่ง custom ID ไปยัง Model ***
            $saved_suggestion_id = $this->suggestions_model->add_suggestion($suggestion_data);

            if (!$saved_suggestion_id) {
                throw new Exception('Database insert failed');
            }

            // *** ตรวจสอบว่า ID ที่ได้กลับมาตรงกับที่สร้าง ***
            if ($saved_suggestion_id !== $suggestion_id) {
                log_message('warning', "ID mismatch: Generated = {$suggestion_id}, Saved = {$saved_suggestion_id}");
            }

            // จัดการไฟล์แนบ (ถ้ามี)
            $uploaded_files = [];
            if (!empty($_FILES['suggestions_files']['name'][0])) {
                $uploaded_files = $this->handle_file_uploads($saved_suggestion_id);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // สร้างการแจ้งเตือน
            try {
                if ($this->db->table_exists('tbl_notifications')) {

                    $reference_id_value = 0;

                    // 1. Notification สำหรับ Staff
                    $staff_data_json = json_encode([
                        'suggestion_id' => $saved_suggestion_id,
                        'topic' => $suggestion_data['suggestions_topic'],
                        'type' => $suggestion_data['suggestion_type'],
                        'requester' => $suggestion_data['suggestions_by'],
                        'phone' => $suggestion_data['suggestions_phone'],
                        'user_type' => $current_user['user_type'],
                        'is_guest' => ($current_user['user_type'] === 'guest'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'file_count' => count($uploaded_files),
                        'has_id_card' => !empty($suggestion_data['suggestions_number']),
                        'has_address_details' => !empty($suggestion_data['guest_province']),
                        'type' => 'staff_notification'
                    ], JSON_UNESCAPED_UNICODE);

                    if ($staff_data_json === false) {
                        log_message('error', 'JSON encoding failed for staff notification');
                        $staff_data_json = '{}';
                    }

                    $staff_notification = [
                        'type' => 'suggestion',
                        'title' => 'ข้อเสนอแนะใหม่',
                        'message' => "มีข้อเสนอแนะใหม่: {$suggestion_data['suggestions_topic']} โดย {$suggestion_data['suggestions_by']}",
                        'reference_id' => $reference_id_value,
                        'reference_table' => 'tbl_suggestions',
                        'target_role' => 'staff',
                        'priority' => 'normal',
                        'icon' => 'fas fa-lightbulb',
                        'url' => site_url("Suggestions/suggestion_detail/{$saved_suggestion_id}"),
                        'data' => $staff_data_json,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => ($current_user['is_logged_in'] && isset($current_user['user_info']['id']) && !empty($current_user['user_info']['id'])) ? intval($current_user['user_info']['id']) : 0,
                        'is_read' => 0,
                        'is_system' => 1,
                        'is_archived' => 0
                    ];

                    $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

                    if ($staff_result) {
                        log_message('info', "Staff notification created successfully for suggestion: {$saved_suggestion_id}");
                    } else {
                        $db_error = $this->db->error();
                        log_message('error', "Failed to create staff notification for suggestion: {$saved_suggestion_id}. DB Error: " . print_r($db_error, true));
                    }

                    // 2. เฉพาะ Public User ที่ login แล้วเท่านั้น - สร้าง Individual Notification
                    $individual_result = true;

                    if (
                        $current_user['is_logged_in'] === true &&
                        $current_user['user_type'] === 'public' &&
                        isset($current_user['user_info']['id']) &&
                        !empty($current_user['user_info']['id'])
                    ) {

                        log_message('info', "Creating individual notification for public user: {$current_user['user_info']['id']}");

                        $individual_data_json = json_encode([
                            'suggestion_id' => $saved_suggestion_id,
                            'topic' => $suggestion_data['suggestions_topic'],
                            'type' => $suggestion_data['suggestion_type'],
                            'status' => $suggestion_data['suggestions_status'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'file_count' => count($uploaded_files),
                            'follow_url' => site_url("Suggestions/my_suggestions"),
                            'type' => 'individual_confirmation'
                        ], JSON_UNESCAPED_UNICODE);

                        if ($individual_data_json === false) {
                            log_message('error', 'JSON encoding failed for individual notification');
                            $individual_data_json = '{}';
                        }

                        $individual_notification = [
                            'type' => 'suggestion',
                            'title' => 'คุณได้ส่งข้อเสนอแนะสำเร็จ',
                            'message' => "ข้อเสนอแนะ \"{$suggestion_data['suggestions_topic']}\" ของคุณได้รับการบันทึกเรียบร้อยแล้ว หมายเลขอ้างอิง: {$saved_suggestion_id}",
                            'reference_id' => $reference_id_value,
                            'reference_table' => 'tbl_suggestions',
                            'target_role' => 'public',
                            'target_user_id' => intval($current_user['user_info']['id']),
                            'priority' => 'high',
                            'icon' => 'fas fa-check-circle',
                            'url' => site_url("Suggestions/my_suggestion_detail/{$saved_suggestion_id}"),
                            'data' => $individual_data_json,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => intval($current_user['user_info']['id']),
                            'is_read' => 0,
                            'is_system' => 1,
                            'is_archived' => 0
                        ];

                        $individual_result = $this->db->insert('tbl_notifications', $individual_notification);

                        if ($individual_result) {
                            log_message('info', "Individual notification created successfully for public user: {$current_user['user_info']['id']}, suggestion: {$saved_suggestion_id}");
                        } else {
                            $db_error = $this->db->error();
                            log_message('error', "Failed to create individual notification for public user: {$current_user['user_info']['id']}, suggestion: {$saved_suggestion_id}. DB Error: " . print_r($db_error, true));
                        }

                    } else {
                        log_message('info', "Skipping individual notification - user_type: {$current_user['user_type']}, is_logged_in: " . ($current_user['is_logged_in'] ? 'true' : 'false'));
                    }

                    $notification_summary = [
                        'staff_notification' => isset($staff_result) && $staff_result ? 'SUCCESS' : 'FAILED',
                        'individual_notification' => $individual_result ? 'SUCCESS' : 'SKIPPED/FAILED',
                        'user_type' => $current_user['user_type'],
                        'is_logged_in' => $current_user['is_logged_in']
                    ];

                    log_message('info', "Suggestion notification summary for {$saved_suggestion_id}: " . json_encode($notification_summary));
                }

            } catch (Exception $e) {
                log_message('error', 'Notification creation failed for suggestion ' . $saved_suggestion_id . ': ' . $e->getMessage());
                log_message('error', 'Notification creation stack trace: ' . $e->getTraceAsString());
            }

            // *** ส่งผลลัพธ์ JSON เท่านั้น พร้อม custom ID และ reCAPTCHA data ***
            $response = [
                'success' => true,
                'message' => 'ส่งข้อเสนอแนะสำเร็จ',
                'suggestion_id' => $saved_suggestion_id, // *** ส่ง custom ID กลับ ***
                'user_type' => $current_user['user_type'],
                'is_logged_in' => $current_user['is_logged_in'],
                'data' => [
                    'topic' => $suggestion_data['suggestions_topic'],
                    'type' => $suggestion_data['suggestion_type'],
                    'by' => $suggestion_data['suggestions_by'],
                    'has_id_card' => !empty($suggestion_data['suggestions_number']),
                    'has_address_details' => !empty($suggestion_data['guest_province']),
                    'files_uploaded' => count($uploaded_files),
                    'custom_id_format' => strlen($saved_suggestion_id) === 7 ? 'new_format' : 'old_format' // *** Debug info ***
                ]
            ];

            // *** ⭐ เพิ่ม: เพิ่มข้อมูล reCAPTCHA ใน response ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                $response['recaptcha_verified'] = true;
                $response['recaptcha_score'] = isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : null;
                $response['verification_method'] = 'recaptcha_v3';
                log_message('info', 'Added reCAPTCHA info to suggestions response');
            } else {
                $response['recaptcha_verified'] = false;
                $response['verification_method'] = $dev_mode ? 'dev_mode_skip' : 'none';
                log_message('info', 'No reCAPTCHA info in suggestions response (dev_mode or no token)');
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            log_message('info', '=== SUGGESTIONS SUBMIT SUCCESS (WITH RECAPTCHA) ===');
            exit;

        } catch (Exception $e) {
            log_message('error', 'Add suggestion error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * หน้าติดตามข้อเสนอแนะสำหรับ Guest และ Public User
     */
    public function follow_suggestions()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();
            $data['is_logged_in'] = $login_info['is_logged_in'];
            $data['user_info'] = $login_info['user_info'];
            $data['user_type'] = $login_info['user_type'];

            // รับ suggestion_id จาก URL parameter
            $search_ref = $this->input->get('ref');
            $search_type = $this->input->get('type') ?: 'ref';

            // ข้อมูลเพิ่มเติมสำหรับหน้า
            $data['page_title'] = 'ติดตามสถานะความคิดเห็น';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'ติดตามสถานะความคิดเห็น', 'url' => '']
            ];

            $data['search_ref'] = $search_ref;
            $data['search_type'] = $search_type;
            $data['suggestion_result'] = null;
            $data['search_performed'] = false;
            $data['access_allowed'] = false;

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // ถ้ามี ref ใน URL ให้ค้นหาทันที
            if (!empty($search_ref)) {
                $data['search_performed'] = true;

                log_message('info', "Follow suggestions search - Type: {$search_type}, Value: {$search_ref}, User Type: {$login_info['user_type']}");

                try {
                    // ค้นหาข้อเสนอแนะตาม search_type
                    $suggestion_data = $this->search_suggestion_by_type_and_user($search_type, $search_ref, $login_info);

                    if ($suggestion_data && $this->check_suggestion_access_by_user_type($suggestion_data, $login_info)) {
                        $data['suggestion_result'] = $this->prepare_suggestion_data_for_display($suggestion_data);
                        $data['access_allowed'] = true;

                        // ดึงประวัติการดำเนินการ
                        $suggestion_history = $this->suggestions_model->get_suggestion_history($suggestion_data->suggestions_id);
                        $data['suggestion_history'] = $this->prepare_suggestion_history_for_display($suggestion_history);

                        // ดึงไฟล์แนับ
                        $suggestion_files = $this->suggestions_model->get_suggestion_files($suggestion_data->suggestions_id);
                        $prepared_files = [];
                        if ($suggestion_files) {
                            foreach ($suggestion_files as $file) {
                                $file_data = (array) $file;

                                // เพิ่มข้อมูลเสริม
                                $file_data['is_image'] = $this->is_image_file($file_data['suggestions_file_type']);
                                $file_data['file_icon'] = $this->get_file_icon($file_data['suggestions_file_type']);
                                $file_data['file_size_formatted'] = $this->format_file_size($file_data['suggestions_file_size']);

                                $prepared_files[] = (object) $file_data;
                            }
                        }
                        $data['suggestion_files'] = $prepared_files;

                        log_message('info', "Successfully found and accessed suggestion: {$search_ref} (User Type: {$login_info['user_type']})");

                    } else {
                        if ($suggestion_data) {
                            $data['error_message'] = 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลความคิดเห็นนี้';

                            if ($login_info['user_type'] === 'public') {
                                $data['error_message'] .= ' (คุณจะเห็นเฉพาะความคิดเห็นที่ส่งผ่านบัญชีของคุณเท่านั้น)';
                            } else {
                                $data['error_message'] .= ' (คุณจะเห็นเฉพาะความคิดเห็นที่ส่งโดยไม่ได้เข้าสู่ระบบเท่านั้น)';
                            }

                            log_message('warning', "Access denied for suggestion: {$search_ref} (User Type: {$login_info['user_type']}, Suggestion User Type: {$suggestion_data->suggestions_user_type})");
                        } else {
                            $data['error_message'] = 'ไม่พบข้อมูลความคิดเห็นตามหมายเลขอ้างอิงที่ระบุ';

                            if ($login_info['user_type'] === 'public') {
                                $data['error_message'] .= ' หรือความคิดเห็นนั้นอาจส่งโดยไม่ได้เข้าสู่ระบบ';
                            } else {
                                $data['error_message'] .= ' หรือความคิดเห็นนั้นอาจส่งผ่านบัญชีสมาชิก';
                            }

                            log_message('warning', "Suggestion not found for ref: {$search_ref}");
                        }
                    }

                } catch (Exception $e) {
                    log_message('error', 'Error searching suggestion by ref: ' . $e->getMessage());
                    $data['error_message'] = 'เกิดข้อผิดพลาดในการค้นหาข้อมูล กรุณาลองใหม่อีกครั้ง';
                }
            }

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Follow suggestions page data: ' . json_encode([
                    'search_ref' => $search_ref,
                    'search_type' => $search_type,
                    'user_type' => $login_info['user_type'],
                    'is_logged_in' => $login_info['is_logged_in'],
                    'user_id' => $login_info['user_info']['id'] ?? null,
                    'search_performed' => $data['search_performed'],
                    'found_suggestion' => !empty($data['suggestion_result']),
                    'access_allowed' => $data['access_allowed']
                ]));
            }

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/follow_suggestions', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in follow_suggestions: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าติดตามสถานะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Suggestions/adding_suggestions');
            }
        }
    }


    /**
     * AJAX: ค้นหาข้อเสนอแนะด้วยหมายเลขอ้างอิง, เบอร์โทร หรือเลขบัตรประชาชน (เพิ่ม reCAPTCHA)
     */
    public function search_suggestion()
    {
        // *** เพิ่ม: Log debug สำหรับ reCAPTCHA ***
        log_message('info', '=== SUGGESTIONS SEARCH START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));
        log_message('info', 'User Agent: ' . $this->input->server('HTTP_USER_AGENT'));

        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** เพิ่ม: ตรวจสอบ reCAPTCHA token ***
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action');
            $recaptcha_source = $this->input->post('recaptcha_source');
            $user_type_detected = $this->input->post('user_type_detected');
            $is_ajax = $this->input->post('ajax_request') === '1';
            $dev_mode = $this->input->post('dev_mode') === '1';

            log_message('info', 'reCAPTCHA info: ' . json_encode([
                'has_token' => !empty($recaptcha_token),
                'token_length' => !empty($recaptcha_token) ? strlen($recaptcha_token) : 0,
                'action' => $recaptcha_action ?: 'not_set',
                'source' => $recaptcha_source ?: 'not_set',
                'user_type_detected' => $user_type_detected ?: 'not_set',
                'is_ajax' => $is_ajax,
                'dev_mode' => $dev_mode
            ]));

            // *** เพิ่ม: ตรวจสอบ reCAPTCHA (ถ้ามี token) ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                log_message('info', 'Starting reCAPTCHA verification for suggestions search');

                try {
                    // *** ใช้ reCAPTCHA Library ที่มีอยู่ ***
                    $recaptcha_options = [
                        'action' => $recaptcha_action ?: 'suggestions_search',
                        'source' => $recaptcha_source ?: 'suggestions_search_form',
                        'user_type_detected' => $user_type_detected ?: 'guest',
                        'form_source' => 'suggestions_search',
                        'client_timestamp' => $this->input->post('client_timestamp'),
                        'user_agent_info' => $this->input->post('user_agent_info'),
                        'is_anonymous' => $this->input->post('is_anonymous') === '1'
                    ];

                    // *** กำหนด user_type สำหรับ Library ***
                    $library_user_type = 'citizen'; // default
                    if ($user_type_detected === 'member' || $user_type_detected === 'staff') {
                        $library_user_type = 'citizen';
                    } elseif ($user_type_detected === 'admin') {
                        $library_user_type = 'staff';
                    }

                    // *** เรียกใช้ reCAPTCHA verification ***
                    if (isset($this->recaptcha_lib)) {
                        $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $library_user_type, null, $recaptcha_options);

                        log_message('info', 'reCAPTCHA verification result: ' . json_encode([
                            'success' => $recaptcha_result['success'],
                            'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                            'action' => $recaptcha_action,
                            'source' => $recaptcha_source,
                            'user_type_detected' => $user_type_detected,
                            'library_user_type' => $library_user_type
                        ]));

                        // *** ตรวจสอบผลลัพธ์ ***
                        if (!$recaptcha_result['success']) {
                            log_message('error', 'reCAPTCHA verification failed: ' . json_encode([
                                'message' => $recaptcha_result['message'],
                                'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                                'action' => $recaptcha_action,
                                'source' => $recaptcha_source
                            ]));

                            echo json_encode([
                                'success' => false,
                                'message' => 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง',
                                'error_type' => 'recaptcha_failed',
                                'recaptcha_data' => $recaptcha_result['data']
                            ], JSON_UNESCAPED_UNICODE);
                            exit;
                        }

                        log_message('info', 'reCAPTCHA verification successful for suggestions search');
                    } else {
                        log_message('error', 'reCAPTCHA library not loaded');
                    }

                } catch (Exception $e) {
                    log_message('error', 'reCAPTCHA verification error: ' . $e->getMessage());

                    echo json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการตรวจสอบความปลอดภัย',
                        'error_type' => 'recaptcha_error'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            } else if (!$dev_mode) {
                log_message('info', 'No reCAPTCHA token provided for suggestions search');
            } else {
                log_message('info', 'Development mode - skipping reCAPTCHA verification');
            }

            $search_type = $this->input->post('search_type'); // 'ref', 'phone', 'id_card'
            $search_value = trim($this->input->post('search_value'));

            if (empty($search_value)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกข้อมูลที่ต้องการค้นหา'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();

            log_message('info', "Search request - Type: {$search_type}, Value: {$search_value}, User Type: {$login_info['user_type']}, Logged In: " . ($login_info['is_logged_in'] ? 'YES' : 'NO'));

            $suggestions = [];

            try {
                switch ($search_type) {
                    case 'ref':
                        // ค้นหาด้วยหมายเลขอ้างอิง
                        $suggestion = $this->suggestions_model->get_suggestion_by_id($search_value);

                        if ($suggestion) {
                            log_message('info', "Found suggestion by ref {$search_value}: User Type = {$suggestion->suggestions_user_type}, User ID = {$suggestion->suggestions_user_id}");

                            if ($this->check_suggestion_access_by_user_type($suggestion, $login_info)) {
                                $suggestions = [$suggestion];
                                log_message('info', "Access granted for suggestion {$search_value}");
                            } else {
                                log_message('warning', "Access denied for suggestion {$search_value}");
                            }
                        } else {
                            log_message('info', "No suggestion found with ref: {$search_value}");
                        }
                        break;

                    case 'phone':
                        // ตรวจสอบรูปแบบเบอร์โทร
                        if (!preg_match('/^0\d{9}$/', $search_value)) {
                            echo json_encode([
                                'success' => false,
                                'message' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง (ต้องเป็น 10 หลัก เริ่มต้นด้วย 0)'
                            ], JSON_UNESCAPED_UNICODE);
                            exit;
                        }

                        // ค้นหาตาม user type
                        if ($login_info['is_logged_in'] && $login_info['user_type'] === 'public') {
                            // Public user: ค้นหาเฉพาะของตัวเองที่เป็น public user type
                            $suggestions = $this->suggestions_model->get_suggestions_by_phone_and_user_type(
                                $search_value,
                                'public',
                                $login_info['user_info']['id']
                            );

                            log_message('info', "Phone search for PUBLIC user {$login_info['user_info']['id']}: " . count($suggestions) . " results");

                        } else {
                            // Guest user: ค้นหาเฉพาะที่เป็น guest user type
                            $suggestions = $this->suggestions_model->get_suggestions_by_phone_and_user_type(
                                $search_value,
                                'guest'
                            );

                            log_message('info', "Phone search for GUEST user: " . count($suggestions) . " results");
                        }
                        break;

                    case 'id_card':
                        // ตรวจสอบรูปแบบเลขบัตรประชาชน
                        if (!preg_match('/^\d{13}$/', $search_value)) {
                            echo json_encode([
                                'success' => false,
                                'message' => 'รูปแบบเลขบัตรประชาชนไม่ถูกต้อง (ต้องเป็น 13 หลัก)'
                            ], JSON_UNESCAPED_UNICODE);
                            exit;
                        }

                        // ตรวจสอบความถูกต้องด้วยอัลกอริทึม
                        if (!$this->validate_thai_id_card_backend($search_value)) {
                            echo json_encode([
                                'success' => false,
                                'message' => 'เลขบัตรประชาชนไม่ถูกต้องตามมาตรฐาน'
                            ], JSON_UNESCAPED_UNICODE);
                            exit;
                        }

                        // ค้นหาตาม user type
                        if ($login_info['is_logged_in'] && $login_info['user_type'] === 'public') {
                            // Public user: ค้นหาเฉพาะของตัวเองที่เป็น public user type
                            $suggestions = $this->suggestions_model->get_suggestions_by_id_card_and_user_type(
                                $search_value,
                                'public',
                                $login_info['user_info']['id']
                            );

                            log_message('info', "ID card search for PUBLIC user {$login_info['user_info']['id']}: " . count($suggestions) . " results");

                        } else {
                            // Guest user: ค้นหาเฉพาะที่เป็น guest user type
                            $suggestions = $this->suggestions_model->get_suggestions_by_id_card_and_user_type(
                                $search_value,
                                'guest'
                            );

                            log_message('info', "ID card search for GUEST user: " . count($suggestions) . " results");
                        }
                        break;

                    default:
                        echo json_encode([
                            'success' => false,
                            'message' => 'ประเภทการค้นหาไม่ถูกต้อง'
                        ], JSON_UNESCAPED_UNICODE);
                        exit;
                }

                if (empty($suggestions)) {
                    $message = 'ไม่พบข้อมูลความคิดเห็นตามข้อมูลที่ระบุ';

                    if ($login_info['is_logged_in'] && $login_info['user_type'] === 'public') {
                        $message .= ' (คุณจะเห็นเฉพาะความคิดเห็นที่ส่งผ่านบัญชีของคุณเท่านั้น)';
                    } else {
                        $message .= ' (คุณจะเห็นเฉพาะความคิดเห็นที่ส่งโดยไม่ได้เข้าสู่ระบบเท่านั้น)';
                    }

                    log_message('info', "No suggestions found for search: {$search_type} = {$search_value}");

                    echo json_encode([
                        'success' => false,
                        'message' => $message
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                // เตรียมข้อมูลสำหรับส่งกลับ
                $prepared_suggestions = [];
                foreach ($suggestions as $suggestion) {
                    $prepared_data = $this->prepare_suggestion_data_for_display($suggestion);

                    // เพิ่มข้อมูลสำหรับการแสดงผล
                    $prepared_data['short_detail'] = mb_substr($prepared_data['suggestions_detail'], 0, 100) . '...';

                    $prepared_suggestions[] = $prepared_data;
                }

                log_message('info', "Search successful: {$search_type} = {$search_value}, found " . count($suggestions) . " results");

                echo json_encode([
                    'success' => true,
                    'message' => 'พบข้อมูล ' . count($suggestions) . ' รายการ',
                    'data' => $prepared_suggestions,
                    'search_type' => $search_type,
                    'search_value' => $search_value,
                    'user_type' => $login_info['user_type'],
                    'user_logged_in' => $login_info['is_logged_in']
                ], JSON_UNESCAPED_UNICODE);

            } catch (Exception $e) {
                log_message('error', 'Error in search_suggestion inner try: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());

                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการค้นหา กรุณาลองใหม่อีกครั้ง',
                    'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', 'Critical error in search_suggestion: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        log_message('info', '=== SUGGESTIONS SEARCH END ===');
        exit;
    }


    /**
     * เตรียมข้อมูลข้อเสนอแนะสำหรับการแสดงผล (แก้ไขสุดท้าย)
     */
    private function prepare_suggestion_data_for_display($suggestion_data)
    {
        if (!$suggestion_data) {
            return null;
        }

        $data = (array) $suggestion_data;

        // เพิ่มข้อมูลสำหรับการแสดงผล (สถานะ, ประเภท, ความสำคัญ)
        $data['status_display'] = $this->get_suggestion_status_display($data['suggestions_status']);
        $data['status_class'] = $this->get_suggestion_status_class($data['suggestions_status']);
        $data['status_icon'] = $this->get_suggestion_status_icon($data['suggestions_status']);
        $data['status_color'] = $this->get_suggestion_status_color($data['suggestions_status']);
        $data['type_display'] = $this->get_suggestion_type_display($data['suggestion_type']);
        $data['type_icon'] = $this->get_suggestion_type_icon($data['suggestion_type']);
        $data['priority_display'] = $this->get_suggestion_priority_display($data['suggestions_priority']);

        // *** 1. เก็บข้อมูลต้นฉบับก่อน (FIRST PRIORITY) ***
        $data['suggestions_by_original'] = $data['suggestions_by'] ?? '';
        $data['suggestions_phone_original'] = $data['suggestions_phone'] ?? '';
        $data['suggestions_email_original'] = $data['suggestions_email'] ?? '';
        $data['suggestions_number_original'] = $data['suggestions_number'] ?? '';

        // *** 2. สร้างข้อมูลที่ถูกเซ็นเซอร์ทันที (SECOND PRIORITY) ***
        $data['suggestions_by_censored'] = $this->censor_name($data['suggestions_by'] ?? '');
        $data['suggestions_phone_censored'] = $this->censor_phone($data['suggestions_phone'] ?? '');
        $data['suggestions_email_censored'] = $this->censor_email($data['suggestions_email'] ?? '');
        $data['suggestions_number_censored'] = $this->censor_id_card($data['suggestions_number'] ?? '');

        // *** 3. ตรวจสอบและแก้ไขข้อมูลที่อาจว่างเปล่า ***
        if (empty($data['suggestions_by_censored']) && !empty($data['suggestions_by_original'])) {
            $data['suggestions_by_censored'] = $this->censor_name($data['suggestions_by_original']);
        }
        if (empty($data['suggestions_phone_censored']) && !empty($data['suggestions_phone_original'])) {
            $data['suggestions_phone_censored'] = $this->censor_phone($data['suggestions_phone_original']);
        }
        if (empty($data['suggestions_email_censored']) && !empty($data['suggestions_email_original'])) {
            $data['suggestions_email_censored'] = $this->censor_email($data['suggestions_email_original']);
        }
        if (empty($data['suggestions_number_censored']) && !empty($data['suggestions_number_original'])) {
            $data['suggestions_number_censored'] = $this->censor_id_card($data['suggestions_number_original']);
        }

        // *** 4. ตั้งค่าเริ่มต้นสำหรับฟิลด์ที่ยังว่าง ***
        if (empty($data['suggestions_by_censored'])) {
            $data['suggestions_by_censored'] = '';
        }
        if (empty($data['suggestions_phone_censored'])) {
            $data['suggestions_phone_censored'] = '';
        }
        if (empty($data['suggestions_email_censored'])) {
            $data['suggestions_email_censored'] = '';
        }
        if (empty($data['suggestions_number_censored'])) {
            $data['suggestions_number_censored'] = '';
        }

        // *** 5. การจัดการข้อมูลที่อยู่แบบครบถ้วน (THIRD PRIORITY) ***
        $data['address_info'] = $this->prepare_address_data_for_display($data);

        // เพิ่มข้อมูลที่อยู่เข้าไปใน root level เพื่อง่ายต่อการใช้งาน
        if ($data['address_info'] && $data['address_info']['has_address']) {
            $data['full_address_display'] = $data['address_info']['display_full'];
            $data['short_address_display'] = $data['address_info']['display_short'];
            $data['province_name'] = $data['address_info']['province'];
            $data['amphoe_name'] = $data['address_info']['amphoe'];
            $data['district_name'] = $data['address_info']['district'];
            $data['zipcode_display'] = $data['address_info']['zipcode'];
            $data['address_source_type'] = $data['address_info']['source_type'];
        } else {
            $data['full_address_display'] = 'ไม่ระบุที่อยู่';
            $data['short_address_display'] = 'ไม่ระบุที่อยู่';
            $data['province_name'] = '';
            $data['amphoe_name'] = '';
            $data['district_name'] = '';
            $data['zipcode_display'] = '';
            $data['address_source_type'] = null;
        }

        // *** 6. Format วันที่ (LAST PRIORITY) ***
        if (!empty($data['suggestions_datesave'])) {
            $date = new DateTime($data['suggestions_datesave']);
            $data['formatted_date'] = $date->format('d/m/Y H:i');
            $data['date_thai'] = $this->format_thai_date($data['suggestions_datesave']);
        }

        if (!empty($data['suggestions_updated_at'])) {
            $update_date = new DateTime($data['suggestions_updated_at']);
            $data['updated_date'] = $update_date->format('d/m/Y H:i');
            $data['updated_thai'] = $this->format_thai_date($data['suggestions_updated_at']);
        }

        // *** 7. Debug Log เพื่อตรวจสอบ ***
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Prepare suggestion data result: ' . json_encode([
                'suggestion_id' => $data['suggestions_id'] ?? 'N/A',
                'has_by_censored' => !empty($data['suggestions_by_censored']),
                'has_phone_censored' => !empty($data['suggestions_phone_censored']),
                'has_email_censored' => !empty($data['suggestions_email_censored']),
                'has_number_censored' => !empty($data['suggestions_number_censored']),
                'by_censored_value' => $data['suggestions_by_censored'] ?? 'EMPTY',
                'phone_censored_value' => $data['suggestions_phone_censored'] ?? 'EMPTY',
                'email_censored_value' => $data['suggestions_email_censored'] ?? 'EMPTY',
                'number_censored_value' => $data['suggestions_number_censored'] ?? 'EMPTY'
            ]));
        }

        return $data;
    }

    // *** เพิ่มฟังก์ชันเซ็นเซอร์ที่ปรับปรุงแล้ว ***

    /**
     * เซ็นเซอร์ชื่อ (ปรับปรุงแล้ว)
     */
    private function censor_name($name)
    {
        try {
            if (empty($name) || trim($name) === '') {
                return '';
            }

            $name = trim($name);
            $words = preg_split('/\s+/u', $name); // ใช้ unicode-aware split
            $censored_words = [];

            foreach ($words as $word) {
                if (empty($word))
                    continue;

                $word_length = mb_strlen($word, 'UTF-8');

                if ($word_length <= 1) {
                    $censored_words[] = $word;
                } elseif ($word_length <= 3) {
                    $censored_words[] = mb_substr($word, 0, 1, 'UTF-8') . str_repeat('*', $word_length - 1);
                } else {
                    $censored_words[] = mb_substr($word, 0, 1, 'UTF-8') . str_repeat('*', $word_length - 2) . mb_substr($word, -1, 1, 'UTF-8');
                }
            }

            $result = implode(' ', $censored_words);
            return !empty($result) ? $result : '';

        } catch (Exception $e) {
            log_message('error', 'Error in censor_name: ' . $e->getMessage());
            return !empty($name) ? '***' : '';
        }
    }

    /**
     * เซ็นเซอร์เบอร์โทรศัพท์ (ปรับปรุงแล้ว)
     */
    private function censor_phone($phone)
    {
        try {
            if (empty($phone) || trim($phone) === '') {
                return '';
            }

            // ลบอักขระที่ไม่ใช่ตัวเลข
            $clean_phone = preg_replace('/[^0-9]/', '', trim($phone));

            if (empty($clean_phone)) {
                return '';
            }

            $phone_length = strlen($clean_phone);

            if ($phone_length < 4) {
                return str_repeat('*', $phone_length);
            } elseif ($phone_length === 10 && substr($clean_phone, 0, 1) === '0') {
                // เบอร์โทรไทย 10 หลัก
                return substr($clean_phone, 0, 3) . '-***-*' . substr($clean_phone, -2);
            } elseif ($phone_length >= 8) {
                // เบอร์โทรยาว
                return substr($clean_phone, 0, 2) . str_repeat('*', $phone_length - 4) . substr($clean_phone, -2);
            } else {
                // เบอร์โทรสั้น
                return substr($clean_phone, 0, 1) . str_repeat('*', $phone_length - 2) . substr($clean_phone, -1);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in censor_phone: ' . $e->getMessage());
            return !empty($phone) ? '***-***-****' : '';
        }
    }

    /**
     * เซ็นเซอร์อีเมล (ปรับปรุงแล้ว)
     */
    private function censor_email($email)
    {
        try {
            if (empty($email) || trim($email) === '') {
                return '';
            }

            $email = trim($email);

            // ตรวจสอบรูปแบบอีเมลพื้นฐาน
            if (!strpos($email, '@')) {
                return str_repeat('*', min(strlen($email), 10));
            }

            $parts = explode('@', $email);
            if (count($parts) !== 2) {
                return str_repeat('*', min(strlen($email), 10));
            }

            $username = $parts[0];
            $domain = $parts[1];

            if (empty($username) || empty($domain)) {
                return '***@***.***';
            }

            // เซ็นเซอร์ username
            $username_length = strlen($username);
            if ($username_length <= 2) {
                $censored_username = str_repeat('*', $username_length);
            } elseif ($username_length <= 4) {
                $censored_username = substr($username, 0, 1) . str_repeat('*', $username_length - 1);
            } else {
                $censored_username = substr($username, 0, 2) . str_repeat('*', $username_length - 4) . substr($username, -2);
            }

            return $censored_username . '@' . $domain;

        } catch (Exception $e) {
            log_message('error', 'Error in censor_email: ' . $e->getMessage());
            return !empty($email) ? '***@***.***' : '';
        }
    }

    /**
     * เซ็นเซอร์เลขบัตรประชาชน (ปรับปรุงแล้ว)
     */
    private function censor_id_card($id_card)
    {
        try {
            if (empty($id_card) || trim($id_card) === '') {
                return '';
            }

            // ลบอักขระที่ไม่ใช่ตัวเลข
            $clean_id = preg_replace('/[^0-9]/', '', trim($id_card));

            if (empty($clean_id)) {
                return '';
            }

            $id_length = strlen($clean_id);

            if ($id_length === 13) {
                // เลขบัตรประชาชนไทย 13 หลัก
                return substr($clean_id, 0, 3) . '-****-****-**-' . substr($clean_id, -2);
            } elseif ($id_length > 6) {
                // เลขบัตรอื่นๆ ที่ยาว
                return substr($clean_id, 0, 2) . str_repeat('*', $id_length - 4) . substr($clean_id, -2);
            } else {
                // เลขบัตรสั้น
                return str_repeat('*', $id_length);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in censor_id_card: ' . $e->getMessage());
            return !empty($id_card) ? '***-****-****-**-**' : '';
        }
    }







    public function validate_thai_id_card($id_card)
    {
        if (empty($id_card) || !preg_match('/^\d{13}$/', $id_card)) {
            $this->form_validation->set_message('validate_thai_id_card', 'เลขบัตรประจำตัวประชาชนต้องเป็นตัวเลข 13 หลัก');
            return FALSE;
        }

        // ตรวจสอบเลขซ้ำทั้งหมด
        if (preg_match('/^(\d)\1{12}$/', $id_card)) {
            $this->form_validation->set_message('validate_thai_id_card', 'เลขบัตรประจำตัวประชาชนไม่ถูกต้อง (ตัวเลขซ้ำทั้งหมด)');
            return FALSE;
        }

        // ตรวจสอบด้วยอัลกอริทึม MOD 11
        $digits = str_split($id_card);
        $weights = [13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $check_digit = $remainder < 2 ? (1 - $remainder) : (11 - $remainder);

        if ($check_digit != (int) $digits[12]) {
            $this->form_validation->set_message('validate_thai_id_card', 'เลขบัตรประจำตัวประชาชนไม่ถูกต้องตามมาตรฐาน');
            return FALSE;
        }

        return TRUE;
    }


    // ===================================================================
    // *** หน้าติดตามข้อเสนอแนะ ***
    // ===================================================================

    /**
     * หน้าแสดงข้อเสนอแนะของฉัน
     */
    public function my_suggestions()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();
            $data['is_logged_in'] = $login_info['is_logged_in'];
            $data['user_info'] = $login_info['user_info'];
            $data['user_type'] = $login_info['user_type'];
            $data['user_address'] = $login_info['user_address'];

            // ถ้าไม่ได้ login ให้ redirect ไป login
            if (!$data['is_logged_in']) {
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อนเพื่อดูข้อเสนอแนะของคุณ');
                redirect('User');
                return;
            }

            // ดึงรายการข้อเสนอแนะของผู้ใช้
            $user_suggestions = [];
            if ($data['user_info'] && isset($data['user_info']['id']) && !empty($data['user_info']['id'])) {
                try {
                    $suggestions_result = $this->suggestions_model->get_suggestions_by_user(
                        $data['user_info']['id'],
                        $data['user_type']
                    );

                    log_message('info', 'Found ' . count($suggestions_result) . ' suggestions for user_id: ' . $data['user_info']['id'] . ' (type: ' . $data['user_type'] . ')');

                    if ($suggestions_result) {
                        $user_suggestions = array_map(function ($suggestion) {
                            if (is_object($suggestion)) {
                                $suggestion_array = [
                                    'suggestions_id' => $suggestion->suggestions_id ?? '',
                                    'suggestion_type' => $suggestion->suggestion_type ?? 'suggestion',
                                    'suggestions_topic' => $suggestion->suggestions_topic ?? '',
                                    'suggestions_detail' => $suggestion->suggestions_detail ?? '',
                                    'suggestions_status' => $suggestion->suggestions_status ?? '',
                                    'suggestions_by' => $suggestion->suggestions_by ?? '',
                                    'suggestions_phone' => $suggestion->suggestions_phone ?? '',
                                    'suggestions_datesave' => $suggestion->suggestions_datesave ?? '',
                                    'suggestions_updated_at' => $suggestion->suggestions_updated_at ?? null,
                                    'suggestions_priority' => $suggestion->suggestions_priority ?? 'normal'
                                ];
                            } else {
                                $suggestion_array = $suggestion;
                            }

                            // *** แก้ไข: เพิ่มข้อมูลสำหรับการแสดงผล ***
                            $suggestion_array['status_display'] = $this->get_suggestion_status_display($suggestion_array['suggestions_status']);
                            $suggestion_array['status_class'] = $this->get_suggestion_status_class($suggestion_array['suggestions_status']);
                            $suggestion_array['status_icon'] = $this->get_suggestion_status_icon($suggestion_array['suggestions_status']);
                            $suggestion_array['status_color'] = $this->get_suggestion_status_color($suggestion_array['suggestions_status']);
                            $suggestion_array['type_display'] = $this->get_suggestion_type_display($suggestion_array['suggestion_type']);
                            $suggestion_array['type_icon'] = $this->get_suggestion_type_icon($suggestion_array['suggestion_type']);

                            // อัพเดทล่าสุด
                            $suggestion_array['latest_update'] = $suggestion_array['suggestions_updated_at'] ?: $suggestion_array['suggestions_datesave'];

                            return $suggestion_array;
                        }, $suggestions_result);
                    }

                } catch (Exception $e) {
                    log_message('error', 'Error loading user suggestions in my_suggestions: ' . $e->getMessage());
                    $user_suggestions = [];
                }
            } else {
                log_message('warning', 'No user_id found for logged in user in my_suggestions');
            }

            $data['suggestions'] = $user_suggestions;

            // *** แก้ไข: คำนวณสถิติข้อเสนอแนะแบบถูกต้อง ***
            $data['status_counts'] = $this->calculate_status_counts_correctly($user_suggestions);

            // Debug สถิติ
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Status counts calculated: ' . json_encode($data['status_counts']));
                log_message('debug', 'Total suggestions processed: ' . count($user_suggestions));
            }

            // ข้อมูลเพิ่มเติมสำหรับหน้า
            $data['page_title'] = 'ข้อเสนอแนะของฉัน';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'ข้อเสนอแนะของฉัน', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'My suggestions page data: ' . json_encode([
                    'user_type' => $data['user_type'],
                    'user_id' => $data['user_info']['id'] ?? 'not_set',
                    'suggestions_count' => count($user_suggestions),
                    'status_counts' => $data['status_counts']
                ]));
            }

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_suggestions', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in my_suggestions: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าข้อเสนอแนะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Suggestions/adding_suggestions');
            }
        }
    }





    private function calculate_status_counts_correctly($suggestions)
    {
        // เริ่มต้นด้วยค่า 0 ทั้งหมด
        $status_counts = [
            'total' => 0,
            'received' => 0,
            'reviewing' => 0,
            'replied' => 0,
            'closed' => 0
        ];

        try {
            // นับจำนวนรวม
            $status_counts['total'] = count($suggestions);

            // นับตามสถานะ
            foreach ($suggestions as $suggestion) {
                $status = isset($suggestion['suggestions_status']) ? $suggestion['suggestions_status'] :
                    (isset($suggestion->suggestions_status) ? $suggestion->suggestions_status : '');

                switch ($status) {
                    case 'received':
                        $status_counts['received']++;
                        break;
                    case 'reviewing':
                        $status_counts['reviewing']++;
                        break;
                    case 'replied':
                        $status_counts['replied']++;
                        break;
                    case 'closed':
                        $status_counts['closed']++;
                        break;
                    default:
                        // สถานะที่ไม่รู้จัก นับเป็น received
                        if (!empty($status)) {
                            $status_counts['received']++;
                            log_message('warning', 'Unknown suggestion status: ' . $status);
                        }
                        break;
                }
            }

            // ตรวจสอบความถูกต้อง
            $calculated_total = $status_counts['received'] + $status_counts['reviewing'] +
                $status_counts['replied'] + $status_counts['closed'];

            if ($calculated_total !== $status_counts['total']) {
                log_message('warning', 'Status count mismatch: Total=' . $status_counts['total'] .
                    ', Calculated=' . $calculated_total);

                // ปรับ total ให้ตรงกับการนับจริง
                $status_counts['total'] = $calculated_total;
            }

            log_message('info', 'Status counts calculated successfully: ' . json_encode($status_counts));

            return $status_counts;

        } catch (Exception $e) {
            log_message('error', 'Error calculating status counts: ' . $e->getMessage());

            // คืนค่าเริ่มต้นถ้าเกิดข้อผิดพลาด
            return [
                'total' => count($suggestions),
                'received' => 0,
                'reviewing' => 0,
                'replied' => 0,
                'closed' => 0
            ];
        }
    }

    /**
     * *** ฟังก์ชันใหม่: ตรวจสอบและแก้ไขข้อมูลสถิติ ***
     */
    private function validate_and_fix_status_counts($status_counts, $suggestions_count)
    {
        try {
            // ตรวจสอบว่าผลรวมตรงกัน
            $sum = $status_counts['received'] + $status_counts['reviewing'] +
                $status_counts['replied'] + $status_counts['closed'];

            if ($sum !== $status_counts['total'] || $status_counts['total'] !== $suggestions_count) {
                log_message('warning', 'Status counts validation failed. Fixing...');

                // แก้ไขข้อมูล
                $status_counts['total'] = max($suggestions_count, $sum);

                // ถ้าผลรวมไม่ตรง ให้ปรับ received
                if ($sum !== $status_counts['total']) {
                    $difference = $status_counts['total'] - $sum;
                    $status_counts['received'] = max(0, $status_counts['received'] + $difference);
                }
            }

            // ตรวจสอบว่าไม่มีค่าติดลบ
            foreach ($status_counts as $key => $value) {
                if ($value < 0) {
                    $status_counts[$key] = 0;
                }
            }

            return $status_counts;

        } catch (Exception $e) {
            log_message('error', 'Error validating status counts: ' . $e->getMessage());
            return $status_counts;
        }
    }


    /**
     * หน้ารายละเอียดข้อเสนอแนะของฉัน
     */
    public function my_suggestion_detail($suggestion_id = null)
    {
        try {
            // ตรวจสอบ suggestion_id
            if (empty($suggestion_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขข้อเสนอแนะที่ระบุ');
                redirect('Suggestions/my_suggestions');
                return;
            }

            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();
            $data['is_logged_in'] = $login_info['is_logged_in'];
            $data['user_info'] = $login_info['user_info'];
            $data['user_type'] = $login_info['user_type'];
            $data['user_address'] = $login_info['user_address'];

            // ถ้าไม่ได้ login ให้ redirect ไป login
            if (!$data['is_logged_in']) {
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อนเพื่อดูรายละเอียดข้อเสนอแนะ');
                redirect('User');
                return;
            }

            // ดึงข้อมูลข้อเสนอแนะ
            $suggestion_data = $this->suggestions_model->get_suggestion_by_id($suggestion_id);

            if (!$suggestion_data) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลข้อเสนอแนะที่ระบุ');
                redirect('Suggestions/my_suggestions');
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึง
            $has_permission = $this->check_suggestion_access_permission($suggestion_data, $data['user_type'], $data['user_info']);

            if (!$has_permission) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลข้อเสนอแนะนี้');
                redirect('Suggestions/my_suggestions');
                return;
            }

            // ดึงประวัติการดำเนินการ
            $suggestion_history = $this->suggestions_model->get_suggestion_history($suggestion_id);

            // ดึงไฟล์แนบ
            $suggestion_files = $this->suggestions_model->get_suggestion_files($suggestion_id);

            // เตรียมข้อมูลไฟล์
            $prepared_files = [];
            if ($suggestion_files) {
                foreach ($suggestion_files as $file) {
                    $file_data = (array) $file;

                    // เพิ่มข้อมูลเสริม
                    $file_data['is_image'] = $this->is_image_file($file_data['suggestions_file_type']);
                    $file_data['file_icon'] = $this->get_file_icon($file_data['suggestions_file_type']);
                    $file_data['file_size_formatted'] = $this->format_file_size($file_data['suggestions_file_size']);

                    $prepared_files[] = (object) $file_data;
                }
            }

            // เตรียมข้อมูลสำหรับ view
            $data['suggestion_data'] = $this->prepare_suggestion_data_for_display($suggestion_data);
            $data['suggestion_history'] = $this->prepare_suggestion_history_for_display($suggestion_history);
            $data['suggestion_files'] = $prepared_files;

            // ข้อมูลเพิ่มเติมสำหรับหน้า
            $data['page_title'] = 'รายละเอียดข้อเสนอแนะ #' . $suggestion_id;
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'ข้อเสนอแนะของฉัน', 'url' => site_url('Suggestions/my_suggestions')],
                ['title' => 'รายละเอียด', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'My suggestion detail page data: ' . json_encode([
                    'suggestion_id' => $suggestion_id,
                    'user_type' => $data['user_type'],
                    'has_history' => !empty($suggestion_history),
                    'has_files' => !empty($prepared_files)
                ]));
            }

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_suggestion_detail', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in my_suggestion_detail: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดรายละเอียดข้อเสนอแนะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Suggestions/my_suggestions');
            }
        }
    }


    // ===================================================================
    // *** หน้าสำหรับ Staff (Backend) ***
    // ===================================================================

    /**
     * หน้ารายงานข้อเสนอแนะ (สำหรับ Staff)
     */
    /**
     * หน้ารายงานข้อเสนอแนะ (สำหรับ Staff) - อัปเดตเพิ่มการเช็คสิทธิ์การลบ
     */
    public function suggestions_report()
    {
        try {
            // ตรวจสอบสิทธิ์ - เฉพาะ Staff เท่านั้น
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ และดึงข้อมูลระดับสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                redirect('User');
                return;
            }

            // เช็คสิทธิ์การรับเรื่องเสนอแนะ
            $can_handle_suggestions = $this->check_suggestion_handle_permission($staff_check);

            // เตรียมข้อมูลพื้นฐานสำหรับ reports
            $data = $this->prepare_reports_base_data('รายงานข้อเสนอแนะ');

            // เพิ่มข้อมูลสิทธิ์
            $data['can_delete_suggestions'] = in_array($staff_check->m_system, ['system_admin', 'super_admin']);
            $data['can_handle_suggestions'] = $can_handle_suggestions; // ใหม่: สิทธิ์รับเรื่องเสนอแนะ
            $data['staff_system_level'] = $staff_check->m_system;

            // ตัวกรองข้อมูล
            $filters = [
                'status' => $this->input->get('status'),
                'type' => $this->input->get('type'),
                'priority' => $this->input->get('priority'),
                'user_type' => $this->input->get('user_type'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search')
            ];

            // Pagination
            $this->load->library('pagination');
            $per_page = 20;
            $current_page = (int) ($this->input->get('page') ?? 1);
            $offset = ($current_page - 1) * $per_page;

            // ดึงข้อมูลข้อเสนอแนะพร้อมกรอง
            $suggestions_result = $this->suggestions_model->get_suggestions_with_filters($filters, $per_page, $offset);
            $suggestions = $suggestions_result['data'] ?? [];
            $total_rows = $suggestions_result['total'] ?? 0;

            // ดึงข้อมูลไฟล์แนบ
            if (!empty($suggestions)) {
                foreach ($suggestions as $suggestion) {
                    $suggestion->files = $this->suggestions_model->get_suggestion_files($suggestion->suggestions_id);
                }
            }

            // สถิติข้อเสนอแนะ
            $suggestion_summary = $this->suggestions_model->get_suggestions_statistics();

            // ตัวเลือกสำหรับ Filter
            $status_options = $this->suggestions_model->get_all_status_options();
            $type_options = $this->suggestions_model->get_all_type_options();
            $priority_options = $this->suggestions_model->get_all_priority_options();
            $user_type_options = [
                ['value' => 'guest', 'label' => 'ผู้ใช้ทั่วไป (Guest)'],
                ['value' => 'public', 'label' => 'สมาชิก (Public)'],
                ['value' => 'staff', 'label' => 'เจ้าหน้าที่ (Staff)']
            ];

            // ข้อเสนอแนะล่าสุด
            $recent_suggestions = $this->suggestions_model->get_recent_suggestions(10);

            // Pagination Setup
            $pagination_config = [
                'base_url' => site_url('Suggestions/suggestions_report'),
                'total_rows' => $total_rows,
                'per_page' => $per_page,
                'page_query_string' => TRUE,
                'query_string_segment' => 'page',
                'reuse_query_string' => TRUE,
                'num_links' => 3,
                'use_page_numbers' => TRUE,
                'cur_tag_open' => '<span class="page-link bg-primary text-white border-primary">',
                'cur_tag_close' => '</span>',
                'num_tag_open' => '<span class="page-link">',
                'num_tag_close' => '</span>',
                'prev_link' => '<i class="fas fa-chevron-left"></i> ก่อนหน้า',
                'next_link' => 'ถัดไป <i class="fas fa-chevron-right"></i>',
                'attributes' => ['class' => 'page-item']
            ];

            $this->pagination->initialize($pagination_config);

            // รวมข้อมูลทั้งหมด
            $data = array_merge($data, [
                'suggestions' => $suggestions,
                'suggestion_summary' => $suggestion_summary,
                'recent_suggestions' => $recent_suggestions,
                'filters' => $filters,
                'status_options' => $status_options,
                'type_options' => $type_options,
                'priority_options' => $priority_options,
                'user_type_options' => $user_type_options,
                'total_rows' => $total_rows,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'pagination' => $this->pagination->create_links(),
                'staff_info' => [
                    'name' => $staff_check->m_fname . ' ' . $staff_check->m_lname,
                    'system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_suggestions'],
                    'can_handle' => $data['can_handle_suggestions'] // ใหม่
                ]
            ]);

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('/')],
                ['title' => 'ระบบรายงาน', 'url' => site_url('System_reports')],
                ['title' => 'รายงานข้อเสนอแนะ', 'url' => '']
            ];

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Suggestions report final data: ' . json_encode([
                    'staff_user' => $staff_check->m_fname . ' ' . $staff_check->m_lname,
                    'staff_system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_suggestions'],
                    'can_handle' => $data['can_handle_suggestions'], // ใหม่
                    'grant_user_ref_id' => $staff_check->grant_user_ref_id,
                    'is_logged_in' => $data['is_logged_in'],
                    'user_type' => $data['user_type'],
                    'suggestions_count' => count($suggestions),
                    'total_rows' => $total_rows
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/suggestions_report', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Error in suggestions_report: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('System_reports');
            }
        }
    }

    /**
     * ฟังก์ชันใหม่: เช็คสิทธิ์การรับเรื่องเสนอแนะ
     */
    private function check_suggestion_handle_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถดำเนินการได้ทุกอย่าง
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_name = 108
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('warning', "user_admin without grant_user_ref_id: {$staff_data->m_fname} {$staff_data->m_lname}");
                    return false;
                }

                // แปลง grant_user_ref_id เป็น array (กรณีมีหลายสิทธิ์คั่นด้วย comma)
                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));

                // เช็คว่ามีสิทธิ์ 108 หรือไม่
                $this->db->select('grant_user_id');
                $this->db->from('tbl_grant_user');
                $this->db->where('grant_user_name', '108');
                $this->db->where_in('grant_user_id', $grant_ids);
                $grant_108 = $this->db->get()->row();

                $has_permission = !empty($grant_108);

                log_message('info', "user_admin permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "grant_ids: " . implode(',', $grant_ids) . " - " .
                    "has_108: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            // end_user หรือระดับอื่นๆ ไม่สามารถดำเนินการได้
            log_message('info', "Permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking suggestion handle permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * หน้ารายละเอียดข้อเสนอแนะ (สำหรับ Staff)
     */
    public function suggestion_detail($suggestion_id = null)
    {
        try {
            // ตรวจสอบ suggestion_id
            if (empty($suggestion_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขข้อเสนอแนะที่ระบุ');
                redirect('Suggestions/suggestions_report');
                return;
            }

            // ตรวจสอบสิทธิ์ - เฉพาะ Staff เท่านั้น
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                redirect('User');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_reports_base_data('รายละเอียดข้อเสนอแนะ #' . $suggestion_id);

            // ดึงข้อมูลข้อเสนอแนะพร้อมข้อมูลผู้ส่ง
            $suggestion_data = $this->suggestions_model->get_suggestion_by_id($suggestion_id);

            if (!$suggestion_data) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลข้อเสนอแนะที่ระบุ');
                redirect('Suggestions/suggestions_report');
                return;
            }

            // Staff สามารถดูได้ทุกข้อเสนอแนะ
            log_message('info', 'Staff user accessing suggestion detail: ' . $staff_check->m_fname . ' ' . $staff_check->m_lname . ' viewing suggestion: ' . $suggestion_id);

            // ดึงประวัติการดำเนินการ
            $suggestion_history = $this->suggestions_model->get_suggestion_history($suggestion_id);

            // ดึงไฟล์แนบ
            $suggestion_files = $this->suggestions_model->get_suggestion_files($suggestion_id);

            // เตรียมข้อมูลไฟล์
            $prepared_files = [];
            if ($suggestion_files) {
                foreach ($suggestion_files as $file) {
                    $file_data = (array) $file;

                    // เพิ่มข้อมูลเสริม
                    $file_data['is_image'] = $this->is_image_file($file_data['suggestions_file_type']);
                    $file_data['file_icon'] = $this->get_file_icon($file_data['suggestions_file_type']);
                    $file_data['file_size_formatted'] = $this->format_file_size($file_data['suggestions_file_size']);

                    $prepared_files[] = (object) $file_data;
                }
            }

            // ดึงข้อมูลผู้ใช้ที่ส่งข้อเสนอแนะ
            $user_details = $this->get_suggestion_user_details($suggestion_data);

            // เตรียมข้อมูลสำหรับ view
            $data['suggestion_data'] = $this->prepare_suggestion_data_for_display($suggestion_data);
            $data['suggestion_history'] = $this->prepare_suggestion_history_for_display($suggestion_history);
            $data['suggestion_files'] = $prepared_files;
            $data['user_details'] = $user_details;

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('/')],
                ['title' => 'ระบบรายงาน', 'url' => site_url('System_reports')],
                ['title' => 'รายงานข้อเสนอแนะ', 'url' => site_url('Suggestions/suggestions_report')],
                ['title' => 'รายละเอียดข้อเสนอแนะ #' . $suggestion_id, 'url' => '']
            ];

            // เพิ่มข้อมูลสำหรับ Staff Actions
            $data['can_update_status'] = true;
            $data['staff_info'] = [
                'name' => $staff_check->m_fname . ' ' . $staff_check->m_lname,
                'system' => $staff_check->m_system
            ];

            // สถานะที่สามารถเปลี่ยนได้
            $data['available_statuses'] = $this->suggestions_model->get_all_status_options();
            $data['available_priorities'] = $this->suggestions_model->get_all_priority_options();

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Suggestion detail page data (Staff): ' . json_encode([
                    'suggestion_id' => $suggestion_id,
                    'staff_user' => $staff_check->m_fname . ' ' . $staff_check->m_lname,
                    'user_type' => $data['user_type'],
                    'has_history' => !empty($suggestion_history),
                    'has_files' => !empty($prepared_files),
                    'can_update_status' => $data['can_update_status']
                ]));
            }

            // โหลด view
            $this->load->view('reports/header', $data);
            $this->load->view('reports/suggestion_detail', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Critical error in suggestion_detail (Staff): ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดรายละเอียดข้อเสนอแนะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Suggestions/suggestions_report');
            }
        }
    }

    // ===================================================================
    // *** AJAX Functions ***
    // ===================================================================

    /**
     * อัปเดตสถานะข้อเสนอแนะ (สำหรับ Staff)
     */


    public function update_suggestion_status()
    {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            // ตรวจสอบ request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบ staff session
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No session m_id found'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบ Staff ใน Database พร้อม grant_user_ref_id
            $this->db->select('m_id, m_fname, m_lname, m_email, m_status, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $staff_data = $this->db->get()->row();

            if (!$staff_data || $staff_data->m_status != '1') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Staff not found or inactive'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** เช็คสิทธิ์การรับเรื่องเสนอแนะ ***
            $can_handle = $this->check_suggestion_handle_permission($staff_data);
            if (!$can_handle) {
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์ในการรับเรื่องเสนอแนะ (เฉพาะ System Admin, Super Admin และ User Admin ที่มีสิทธิ์ 108 เท่านั้น)'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // รับข้อมูล POST
            $suggestion_id = $this->input->post('suggestion_id');
            $new_status = $this->input->post('new_status');
            $new_priority = $this->input->post('new_priority');
            $reply_message = $this->input->post('reply_message');

            // Validation
            if (empty($suggestion_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาระบุหมายเลขข้อเสนอแนะ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบว่าข้อเสนอแนะมีอยู่จริง
            $suggestion_data = $this->suggestions_model->get_suggestion_by_id($suggestion_id);

            if (!$suggestion_data) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อเสนอแนะที่ระบุ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // เริ่ม transaction
            $this->db->trans_start();

            $staff_name = $staff_data->m_fname . ' ' . $staff_data->m_lname;
            $current_time = date('Y-m-d H:i:s');

            // เตรียมข้อมูลสำหรับอัปเดต
            $update_data = [];

            if (!empty($new_status)) {
                $update_data['suggestions_status'] = $new_status;
            }

            if (!empty($new_priority)) {
                $update_data['suggestions_priority'] = $new_priority;
            }

            if (!empty($reply_message)) {
                $update_data['suggestions_reply'] = $reply_message;
                $update_data['suggestions_replied_by'] = $staff_name;
                $update_data['suggestions_replied_at'] = $current_time;
            }

            $update_data['suggestions_updated_by'] = $staff_name;
            $update_data['suggestions_updated_at'] = $current_time;

            if (empty($update_data) || count($update_data) <= 2) { // เฉพาะ updated_by และ updated_at
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่มีข้อมูลสำหรับอัปเดต'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // สร้างคำอธิบายการดำเนินการที่เป็นมิตรกับผู้ใช้
            $action_description = $this->build_user_friendly_action_description($update_data, $staff_name);

            // อัปเดตข้อเสนอแนะ
            $update_result = $this->suggestions_model->update_suggestion($suggestion_id, $update_data, $staff_name, $action_description);

            if (!$update_result) {
                $this->db->trans_rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัปเดตข้อเสนอแนะได้'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Commit Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Transaction failed'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // สร้างการแจ้งเตือน
            try {
                $this->create_suggestion_update_notifications($suggestion_id, $suggestion_data, $update_data, $staff_data);
                log_message('info', "Notifications sent successfully for suggestion {$suggestion_id} update by {$staff_name} ({$staff_data->m_system})");
            } catch (Exception $e) {
                log_message('error', 'Failed to create notifications for suggestion update: ' . $e->getMessage());
            }

            // ส่งผลลัพธ์สำเร็จ
            echo json_encode([
                'success' => true,
                'message' => 'อัปเดตข้อเสนอแนะสำเร็จ',
                'data' => [
                    'suggestion_id' => $suggestion_id,
                    'updated_by' => $staff_name,
                    'staff_system' => $staff_data->m_system,
                    'updates' => $update_data,
                    'timestamp' => $current_time,
                    'action_description' => $action_description
                ]
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // Rollback ถ้ามี transaction
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }

            echo json_encode([
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'debug' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }



    private function build_user_friendly_action_description($update_data, $staff_name)
    {
        $descriptions = [];

        if (isset($update_data['suggestions_status'])) {
            $status_display = $this->get_suggestion_status_display($update_data['suggestions_status']);
            $descriptions[] = "เปลี่ยนสถานะเป็น: {$status_display}";
        }

        if (isset($update_data['suggestions_priority'])) {
            $priority_display = $this->get_suggestion_priority_display($update_data['suggestions_priority']);
            $descriptions[] = "เปลี่ยนความสำคัญเป็น: {$priority_display}";
        }

        if (isset($update_data['suggestions_reply'])) {
            $descriptions[] = "ตอบกลับความคิดเห็น";
        }

        $action_text = !empty($descriptions) ? implode(', ', $descriptions) : 'อัปเดตข้อมูล';

        return "{$action_text} โดย {$staff_name}";
    }



    // ===================================================================
    // *** FILE HANDLING Functions ***
    // ===================================================================

    /**
     * ดาวน์โหลดไฟล์แนบ
     */
    public function download_file($file_name)
    {
        try {
            if (empty($file_name)) {
                show_404();
                return;
            }

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in']) {
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อนดาวน์โหลดไฟล์');
                redirect('User');
                return;
            }

            // ลองหาไฟล์ในหลายโฟลเดอร์
            $possible_paths = [
                './docs/suggestions_files/' . $file_name,
                './docs/files/suggestions/' . $file_name,
                './uploads/suggestions/' . $file_name
            ];

            $file_path = null;
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    $file_path = $path;
                    break;
                }
            }

            if (!$file_path) {
                log_message('error', 'Suggestion file not found: ' . $file_name);
                show_404();
                return;
            }

            // ดึงข้อมูลไฟล์จากฐานข้อมูล
            $file_info = $this->suggestions_model->get_file_info($file_name);

            if (!$file_info) {
                log_message('error', 'Suggestion file info not found in database: ' . $file_name);
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์การดาวน์โหลด
            $suggestion_data = $this->suggestions_model->get_suggestion_by_id($file_info->suggestions_file_ref_id);

            if (!$suggestion_data) {
                log_message('error', 'Suggestion not found for file: ' . $file_name);
                show_404();
                return;
            }

            $has_permission = $this->check_suggestion_access_permission($suggestion_data, $current_user['user_type'], $current_user['user_info']);
            if (!$has_permission) {
                log_message('warning', 'Download permission denied for file: ' . $file_name . ' by user: ' . ($current_user['user_info']['name'] ?? 'unknown'));
                show_error('คุณไม่มีสิทธิ์ดาวน์โหลดไฟล์นี้', 403);
                return;
            }

            $original_name = $file_info->suggestions_file_original_name;
            $file_type = $file_info->suggestions_file_type;

            // ตั้งค่า header สำหรับดาวน์โหลด
            header('Content-Type: ' . $file_type);
            header('Content-Disposition: attachment; filename="' . $original_name . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // Log การดาวน์โหลด
            log_message('info', 'File downloaded: ' . $original_name . ' by user: ' . ($current_user['user_info']['name'] ?? 'unknown'));

            // อ่านและส่งไฟล์
            readfile($file_path);

        } catch (Exception $e) {
            log_message('error', 'Error in download_suggestion_file: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการดาวน์โหลดไฟล์', 500);
        }
    }

    /**
     * แสดงรูปภาพแนบ
     */
    public function view_image($file_name)
    {
        try {
            if (empty($file_name)) {
                log_message('error', 'view_suggestion_image: Empty file name');
                show_404();
                return;
            }

            log_message('info', 'Attempting to view suggestion image: ' . $file_name);

            // ตรวจสอบไฟล์ที่ docs/suggestions_files/
            $file_path = './docs/suggestions_files/' . $file_name;

            if (!file_exists($file_path)) {
                log_message('error', 'Suggestion image file not found at: ' . $file_path);

                // ลองหาในโฟลเดอร์อื่น
                $alternative_paths = [
                    './docs/img/suggestions/' . $file_name,
                    './docs/files/suggestions/' . $file_name,
                    './uploads/suggestions/' . $file_name
                ];

                $found = false;
                foreach ($alternative_paths as $alt_path) {
                    if (file_exists($alt_path)) {
                        $file_path = $alt_path;
                        $found = true;
                        log_message('info', 'Found file at alternative path: ' . $alt_path);
                        break;
                    }
                }

                if (!$found) {
                    log_message('error', 'File not found in any location: ' . $file_name);
                    show_404();
                    return;
                }
            } else {
                log_message('info', 'File found at: ' . $file_path);
            }

            // ตรวจสอบว่าเป็นไฟล์รูปภาพ
            $file_info = pathinfo($file_path);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

            if (!in_array(strtolower($file_info['extension']), $allowed_extensions)) {
                log_message('error', 'Invalid image extension: ' . $file_info['extension']);
                show_error('ไฟล์นี้ไม่ใช่รูปภาพ', 400);
                return;
            }

            // ตรวจสอบสิทธิ์แบบหลวม
            $current_user = $this->get_current_user_detailed();

            if ($current_user['is_logged_in']) {
                log_message('info', 'User logged in, checking permissions for: ' . $current_user['user_type']);

                // ดึงข้อมูลไฟล์จากฐานข้อมูล
                $file_data = $this->suggestions_model->get_file_info($file_name);

                if ($file_data) {
                    // ตรวจสอบข้อเสนอแนะที่เกี่ยวข้อง
                    $suggestion_data = $this->suggestions_model->get_suggestion_by_id($file_data->suggestions_file_ref_id);

                    if ($suggestion_data) {
                        // Staff สามารถดูได้ทั้งหมด
                        if ($current_user['user_type'] === 'staff') {
                            log_message('info', 'Staff access granted');
                        }
                        // Public user ตรวจสอบสิทธิ์
                        elseif ($current_user['user_type'] === 'public') {
                            $has_permission = $this->check_suggestion_access_permission($suggestion_data, $current_user['user_type'], $current_user['user_info']);
                            if (!$has_permission) {
                                log_message('warning', 'Access denied for public user');
                                show_404();
                                return;
                            }
                            log_message('info', 'Public user access granted');
                        } else {
                            log_message('warning', 'Access denied - user type: ' . $current_user['user_type']);
                            show_404();
                            return;
                        }
                    } else {
                        log_message('warning', 'Suggestion data not found for file: ' . $file_name);
                    }
                } else {
                    log_message('warning', 'File data not found in database: ' . $file_name);
                    // ไม่พบในฐานข้อมูล แต่ไฟล์มีอยู่ - อนุญาตให้ดูได้ (อาจเป็นไฟล์เก่า)
                }
            } else {
                log_message('info', 'Guest user accessing image');
                // Guest user - อนุญาตให้ดูได้บางกรณี
            }

            // กำหนด MIME type
            $mime_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'bmp' => 'image/bmp'
            ];

            $extension = strtolower($file_info['extension']);
            $mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'image/jpeg';

            // ตั้งค่า headers
            header('Content-Type: ' . $mime_type);
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: public, max-age=3600');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file_path)) . ' GMT');

            log_message('info', 'Serving suggestion image: ' . $file_name . ' (Type: ' . $mime_type . ', Size: ' . filesize($file_path) . ')');

            // ส่งไฟล์
            readfile($file_path);

        } catch (Exception $e) {
            log_message('error', 'Error in view_suggestion_image: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            show_404();
        }
    }

    // ===================================================================
    // *** HELPER FUNCTIONS ***
    // ===================================================================

    /**
     * เตรียมข้อมูลสำหรับ navbar
     */
    private function prepare_navbar_data()
    {
        $data = [
            'qActivity' => [],
            'qNews' => [],
            'qAnnounce' => [],
            'qOrder' => [],
            'qProcurement' => [],
            'qMui' => [],
            'qGuide_work' => [],
            'qLoadform' => [],
            'qPppw' => [],
            'qMsg_pres' => [],
            'qHistory' => [],
            'qOtop' => [],
            'qGci' => [],
            'qVision' => [],
            'qAuthority' => [],
            'qMission' => [],
            'qMotto' => [],
            'qCmi' => [],
            'qExecutivepolicy' => [],
            'qTravel' => [],
            'qSi' => [],
            'qHotnews' => [],
            'qWeather' => [],
            'events' => [],
            'qBanner' => [],
            'qBackground_personnel' => []
        ];

        try {
            // โหลดข้อมูลที่จำเป็นสำหรับ navbar อย่างปลอดภัย
            if (isset($this->activity_model) && method_exists($this->activity_model, 'activity_frontend')) {
                $result = $this->activity_model->activity_frontend();
                $data['qActivity'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->HotNews_model) && method_exists($this->HotNews_model, 'hotnews_frontend')) {
                $result = $this->HotNews_model->hotnews_frontend();
                $data['qHotnews'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->Weather_report_model) && method_exists($this->Weather_report_model, 'weather_reports_frontend')) {
                $result = $this->Weather_report_model->weather_reports_frontend();
                $data['qWeather'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->calender_model) && method_exists($this->calender_model, 'get_events')) {
                $result = $this->calender_model->get_events();
                $data['events'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->banner_model) && method_exists($this->banner_model, 'banner_frontend')) {
                $result = $this->banner_model->banner_frontend();
                $data['qBanner'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->background_personnel_model) && method_exists($this->background_personnel_model, 'background_personnel_frontend')) {
                $result = $this->background_personnel_model->background_personnel_frontend();
                $data['qBackground_personnel'] = (is_array($result) || is_object($result)) ? $result : [];
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * ดึงข้อมูล user ปัจจุบันแบบละเอียด
     */
    private function get_current_user_detailed()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];

        try {
            // *** คัดลอกจาก Queue: ตรวจสอบ session ที่ชัดเจนและถูกต้อง ***
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');
            $m_id = $this->session->userdata('m_id');
            $m_email = $this->session->userdata('m_email');

            // Debug session
            log_message('debug', 'Suggestions - Session check - mp_id: ' . ($mp_id ? $mp_id : 'NULL') .
                ', mp_email: ' . ($mp_email ? $mp_email : 'NULL') .
                ', m_id: ' . ($m_id ? $m_id : 'NULL') .
                ', m_email: ' . ($m_email ? $m_email : 'NULL'));

            // *** ตรวจสอบ public user ก่อน (ต้องมีทั้ง mp_id และ mp_email) ***
            if (!empty($mp_id) && !empty($mp_email)) {
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_number, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_status');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_info'] = [
                        'id' => $user_data->id,
                        'mp_id' => $user_data->mp_id,
                        'name' => trim(($user_data->mp_prefix ? $user_data->mp_prefix . ' ' : '') . $user_data->mp_fname . ' ' . $user_data->mp_lname),
                        'prefix' => $user_data->mp_prefix,
                        'fname' => $user_data->mp_fname,
                        'lname' => $user_data->mp_lname,
                        'phone' => $user_data->mp_phone,
                        'email' => $user_data->mp_email,
                        'number' => $user_data->mp_number
                    ];

                    // ข้อมูลที่อยู่
                    if (!empty($user_data->mp_address) || !empty($user_data->mp_district)) {
                        $user_info['user_address'] = [
                            'additional_address' => $user_data->mp_address ?: '',
                            'district' => $user_data->mp_district ?: '',
                            'amphoe' => $user_data->mp_amphoe ?: '',
                            'province' => $user_data->mp_province ?: '',
                            'zipcode' => $user_data->mp_zipcode ?: '',
                            'full_address' => trim($user_data->mp_address . ' ' . $user_data->mp_district . ' ' . $user_data->mp_amphoe . ' ' . $user_data->mp_province . ' ' . $user_data->mp_zipcode),
                            'parsed' => [
                                'additional_address' => $user_data->mp_address ?: '',
                                'district' => $user_data->mp_district ?: '',
                                'amphoe' => $user_data->mp_amphoe ?: '',
                                'province' => $user_data->mp_province ?: '',
                                'zipcode' => $user_data->mp_zipcode ?: '',
                                'full_address' => trim($user_data->mp_address . ' ' . $user_data->mp_district . ' ' . $user_data->mp_amphoe . ' ' . $user_data->mp_province . ' ' . $user_data->mp_zipcode)
                            ]
                        ];
                    }

                    log_message('info', 'Suggestions - Public user login detected: ' . $user_data->mp_email);
                    return $user_info;
                } else {
                    log_message('warning', 'Suggestions - Public user not found in database or inactive: mp_id=' . $mp_id . ', mp_email=' . $mp_email);
                }
            }

            // *** ตรวจสอบ staff user (ต้องมีทั้ง m_id และ m_email) ***
            if (!empty($m_id) && !empty($m_email)) {
                // *** คัดลอกจาก Queue: ใช้ JOIN ที่ถูกต้องตามโครงสร้างตาราง ***
                $this->db->select('m.m_id, m.m_email, m.m_fname, m.m_lname, m.m_phone, m.m_system, m.m_img, m.m_status, COALESCE(p.pname, "เจ้าหน้าที่") as pname');
                $this->db->from('tbl_member m');
                $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
                $this->db->where('m.m_id', $m_id);
                $this->db->where('m.m_email', $m_email);
                $this->db->where('m.m_status', '1');
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_info'] = [
                        'id' => $user_data->m_id,
                        'm_id' => $user_data->m_id,
                        'name' => trim($user_data->m_fname . ' ' . $user_data->m_lname),
                        'fname' => $user_data->m_fname,
                        'lname' => $user_data->m_lname,
                        'phone' => $user_data->m_phone,
                        'email' => $user_data->m_email,
                        'm_system' => $user_data->m_system,
                        'm_img' => $user_data->m_img,
                        'pname' => $user_data->pname
                    ];

                    log_message('info', 'Suggestions - Staff user login detected: ' . $user_data->m_email . ' (' . $user_data->pname . ')');
                    return $user_info;
                } else {
                    log_message('warning', 'Suggestions - Staff user not found in database or inactive: m_id=' . $m_id . ', m_email=' . $m_email);
                }
            }

            // *** ถ้าไม่มี session หรือไม่พบใน DB = Guest ***
            log_message('info', 'Suggestions - No valid login session found - user is guest');

        } catch (Exception $e) {
            log_message('error', 'Error in get_current_user_detailed (Suggestions): ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        }

        // *** Return guest เป็นค่า default ***
        return [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];
    }

    // ===================================================================
// เพิ่มฟังก์ชันใหม่สำหรับ Debug Session
// ===================================================================

    /**
     * Debug session data - เพิ่มฟังก์ชันนี้ใน Controller
     */
    public function debug_session()
    {
        // เฉพาะ development environment เท่านั้น
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }

        echo "<h2>Debug Session Data</h2>";
        echo "<h3>All Session Data:</h3>";
        echo "<pre>";
        print_r($this->session->all_userdata());
        echo "</pre>";

        echo "<h3>Specific Session Keys:</h3>";
        echo "mp_id: " . ($this->session->userdata('mp_id') ?: 'NOT_SET') . "<br>";
        echo "mp_email: " . ($this->session->userdata('mp_email') ?: 'NOT_SET') . "<br>";
        echo "mp_status: " . ($this->session->userdata('mp_status') ?: 'NOT_SET') . "<br>";
        echo "m_id: " . ($this->session->userdata('m_id') ?: 'NOT_SET') . "<br>";
        echo "m_email: " . ($this->session->userdata('m_email') ?: 'NOT_SET') . "<br>";
        echo "m_status: " . ($this->session->userdata('m_status') ?: 'NOT_SET') . "<br>";

        echo "<h3>Current User Info:</h3>";
        $current_user = $this->get_current_user_detailed();
        echo "<pre>";
        print_r($current_user);
        echo "</pre>";

        echo "<h3>PHP Session:</h3>";
        echo "Session Status: " . session_status() . "<br>";
        echo "Session ID: " . session_id() . "<br>";
        echo "Session Save Path: " . session_save_path() . "<br>";

        if (isset($_SESSION)) {
            echo "<h4>Raw $_SESSION:</h4>";
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
        }
    }



    /**
     * สร้าง suggestion_id
     */
    private function generate_suggestion_id()
    {
        $max_attempts = 100; // จำกัดจำนวนครั้งในการสร้าง ID ไม่ให้เกิน 100 ครั้ง
        $attempts = 0;

        do {
            // ปีไทย 2 ตัวท้าย (เช่น 2568 -> 68)
            $thai_year = date('Y') + 543;
            $year_suffix = substr($thai_year, -2);

            // สุ่ม 5 ตัวหลัง
            $random_digits = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

            $suggestion_id = $year_suffix . $random_digits;

            // ตรวจสอบว่า ID ซ้ำหรือไม่
            $this->db->where('suggestions_id', $suggestion_id);
            $exists = $this->db->get('tbl_suggestions')->num_rows();

            $attempts++;

            if ($attempts >= $max_attempts) {
                log_message('error', 'Max attempts reached for generating suggestion ID');
                // ใช้ timestamp เป็นทางเลือกสุดท้าย
                $suggestion_id = $year_suffix . substr(time(), -5);
                break;
            }

        } while ($exists > 0); // สร้างใหม่ถ้าซ้ำ

        log_message('info', "Generated suggestion ID: {$suggestion_id} (attempts: {$attempts})");

        return $suggestion_id;
    }


    /**
     * จัดการไฟล์แนบ
     */
    private function handle_file_uploads($suggestion_id)
    {
        $this->load->library('upload');

        $upload_path = './docs/suggestions_files/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|pdf',
            'max_size' => 5120, // 5MB
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE
        ];

        $uploaded_files = [];
        $file_count = is_array($_FILES['suggestions_files']['name']) ? count($_FILES['suggestions_files']['name']) : 0;

        for ($i = 0; $i < $file_count; $i++) {
            if (!empty($_FILES['suggestions_files']['name'][$i]) && $_FILES['suggestions_files']['error'][$i] === UPLOAD_ERR_OK) {
                $_FILES['single_file'] = [
                    'name' => $_FILES['suggestions_files']['name'][$i],
                    'type' => $_FILES['suggestions_files']['type'][$i],
                    'tmp_name' => $_FILES['suggestions_files']['tmp_name'][$i],
                    'error' => $_FILES['suggestions_files']['error'][$i],
                    'size' => $_FILES['suggestions_files']['size'][$i]
                ];

                $this->upload->initialize($config);

                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();

                    // *** บันทึกข้อมูลไฟล์พร้อม custom ID ***
                    $file_data = [
                        'suggestions_file_ref_id' => $suggestion_id, // *** VARCHAR ID ***
                        'suggestions_file_name' => $upload_data['file_name'],
                        'suggestions_file_original_name' => $upload_data['orig_name'],
                        'suggestions_file_type' => $upload_data['file_type'],
                        'suggestions_file_size' => $upload_data['file_size'] * 1024,
                        'suggestions_file_path' => $upload_data['full_path'],
                        'suggestions_file_uploaded_at' => date('Y-m-d H:i:s'),
                        'suggestions_file_status' => 'active'
                    ];

                    $file_id = $this->suggestions_model->add_suggestion_file($file_data);

                    if ($file_id) {
                        $uploaded_files[] = [
                            'file_id' => $file_id,
                            'file_name' => $upload_data['file_name'],
                            'original_name' => $upload_data['orig_name'],
                            'file_type' => $upload_data['file_type'],
                            'file_size' => $file_data['suggestions_file_size']
                        ];

                        log_message('info', "File uploaded for suggestion {$suggestion_id}: {$upload_data['orig_name']}");
                    } else {
                        log_message('error', "Failed to save file data for: {$upload_data['orig_name']}");
                    }
                } else {
                    $upload_errors = $this->upload->display_errors('', '');
                    log_message('error', "File upload failed for {$_FILES['suggestions_files']['name'][$i]}: {$upload_errors}");
                }
            }
        }

        return $uploaded_files;
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงข้อเสนอแนะ
     */
    private function check_suggestion_access_permission($suggestion_data, $user_type, $user_info)
    {
        // Staff สามารถดูได้ทุกข้อเสนอแนะ
        if ($user_type === 'staff') {
            return true;
        }

        // Guest ไม่สามารถเข้าถึงรายละเอียดได้
        if ($user_type === 'guest' || empty($user_info)) {
            return false;
        }

        // Public user สามารถดูได้เฉพาะข้อเสนอแนะของตนเอง
        if ($user_type === 'public') {
            return ($suggestion_data->suggestions_phone === $user_info['phone']);
        }

        return false;
    }









    public function get_suggestion_address_for_view($suggestion_data, $format = 'full')
    {
        try {
            if (!$suggestion_data) {
                return 'ไม่มีข้อมูล';
            }

            $address_info = null;

            // ถ้าเป็น array
            if (is_array($suggestion_data) && isset($suggestion_data['address_info'])) {
                $address_info = $suggestion_data['address_info'];
            }
            // ถ้าเป็น object
            elseif (is_object($suggestion_data) && isset($suggestion_data->address_info)) {
                $address_info = $suggestion_data->address_info;
            }
            // ถ้าไม่มีให้สร้างใหม่
            else {
                $address_info = $this->prepare_address_data_for_display((array) $suggestion_data);
            }

            if (!$address_info || !$address_info['has_address']) {
                return 'ไม่ระบุที่อยู่';
            }

            switch ($format) {
                case 'short':
                    return $address_info['display_short'];

                case 'province':
                    return $address_info['province'] ?: 'ไม่ระบุจังหวัด';

                case 'full':
                default:
                    return $address_info['display_full'];
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestion address for view: ' . $e->getMessage());
            return 'เกิดข้อผิดพลาด';
        }
    }

    /**
     * *** เพิ่มใหม่: Helper function สำหรับตรวจสอบว่ามีข้อมูลที่อยู่ครบถ้วนหรือไม่ ***
     */
    public function has_complete_address($suggestion_data)
    {
        try {
            $address_info = null;

            if (is_array($suggestion_data) && isset($suggestion_data['address_info'])) {
                $address_info = $suggestion_data['address_info'];
            } elseif (is_object($suggestion_data) && isset($suggestion_data->address_info)) {
                $address_info = $suggestion_data->address_info;
            } else {
                $address_info = $this->prepare_address_data_for_display((array) $suggestion_data);
            }

            return $address_info &&
                $address_info['has_address'] &&
                !empty($address_info['province']) &&
                !empty($address_info['amphoe']) &&
                !empty($address_info['district']);

        } catch (Exception $e) {
            log_message('error', 'Error checking complete address: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * เตรียมประวัติข้อเสนอแนะสำหรับการแสดงผล
     */
    private function prepare_suggestion_history_for_display($suggestion_history)
    {
        if (empty($suggestion_history)) {
            return [];
        }

        $prepared_history = [];
        foreach ($suggestion_history as $history) {
            $item = (array) $history;

            // Format วันที่
            if (!empty($item['action_date'])) {
                $date = new DateTime($item['action_date']);
                $item['formatted_date'] = $date->format('d/m/Y H:i');
                $item['date_thai'] = $this->format_thai_date($item['action_date']);
            }

            $prepared_history[] = $item;
        }

        return $prepared_history;
    }

    /**
     * ดึงรายละเอียดผู้ใช้ที่ส่งข้อเสนอแนะ
     */
    private function get_suggestion_user_details($suggestion)
    {
        $details = [
            'user_type_display' => 'ไม่ทราบ',
            'user_info' => null,
            'full_address' => null
        ];

        try {
            if ($suggestion->suggestions_user_type === 'public' && !empty($suggestion->suggestions_user_id)) {
                $this->db->select('*');
                $this->db->from('tbl_member_public');
                $this->db->where('id', $suggestion->suggestions_user_id);
                $user_info = $this->db->get()->row();

                if ($user_info) {
                    $details['user_type_display'] = 'สมาชิก';
                    $details['user_info'] = $user_info;

                    // รวมที่อยู่
                    $address_parts = array_filter([
                        $user_info->mp_address,
                        $user_info->mp_district ? 'ต.' . $user_info->mp_district : '',
                        $user_info->mp_amphoe ? 'อ.' . $user_info->mp_amphoe : '',
                        $user_info->mp_province ? 'จ.' . $user_info->mp_province : '',
                        $user_info->mp_zipcode
                    ]);
                    $details['full_address'] = implode(' ', $address_parts);
                }
            } elseif ($suggestion->suggestions_user_type === 'staff' && !empty($suggestion->suggestions_user_id)) {
                $this->db->select('*');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $suggestion->suggestions_user_id);
                $user_info = $this->db->get()->row();

                if ($user_info) {
                    $details['user_type_display'] = 'เจ้าหน้าที่';
                    $details['user_info'] = $user_info;
                }
            } elseif ($suggestion->suggestions_user_type === 'guest') {
                $details['user_type_display'] = 'ผู้ใช้ทั่วไป';
                $details['full_address'] = $suggestion->suggestions_address;
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting suggestion user details: ' . $e->getMessage());
        }

        return $details;
    }

    /**
     * สร้างการแจ้งเตือนสำหรับการอัปเดตข้อเสนอแนะ
     */
    private function create_suggestion_update_notifications($suggestion_id, $suggestion_data, $update_data, $staff_data)
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('warning', 'tbl_notifications table does not exist, skipping notification creation');
                return;
            }

            $staff_name = $staff_data->m_fname . ' ' . $staff_data->m_lname;
            $current_time = date('Y-m-d H:i:s');

            // สร้างข้อความแจ้งเตือนตามการอัปเดต
            $update_messages = [];
            if (isset($update_data['suggestions_status'])) {
                $update_messages[] = "สถานะ: {$update_data['suggestions_status']}";
            }
            if (isset($update_data['suggestions_priority'])) {
                $update_messages[] = "ความสำคัญ: {$update_data['suggestions_priority']}";
            }
            if (isset($update_data['suggestions_reply'])) {
                $update_messages[] = "มีการตอบกลับ";
            }

            $update_summary = implode(', ', $update_messages);

            // 1. แจ้งเตือนสำหรับ Staff ทั้งหมด
            $staff_data_json = json_encode([
                'suggestion_id' => $suggestion_id,
                'topic' => $suggestion_data->suggestions_topic,
                'requester' => $suggestion_data->suggestions_by,
                'phone' => $suggestion_data->suggestions_phone,
                'updates' => $update_data,
                'updated_by' => $staff_name,
                'timestamp' => $current_time,
                'type' => 'staff_update_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'suggestion_update',
                'title' => 'อัปเดตข้อเสนอแนะ',
                'message' => "ข้อเสนอแนะ #{$suggestion_id} ได้รับการอัปเดต: {$update_summary} โดย {$staff_name}",
                'reference_id' => 0,
                'reference_table' => 'tbl_suggestions',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-edit',
                'url' => site_url("Suggestions/suggestion_detail/{$suggestion_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff notification created successfully for suggestion update: {$suggestion_id}");
            }

            // 2. แจ้งเตือนสำหรับเจ้าของข้อเสนอแนะ (ถ้าเป็น public user)
            if ($suggestion_data->suggestions_user_type === 'public' && !empty($suggestion_data->suggestions_user_id)) {

                $user_message = "ข้อเสนอแนะของคุณได้รับการอัปเดต";
                if (isset($update_data['suggestions_reply'])) {
                    $user_message = "ข้อเสนอแนะของคุณได้รับการตอบกลับ";
                } elseif (isset($update_data['suggestions_status'])) {
                    $user_message = "สถานะข้อเสนอแนะของคุณเปลี่ยนเป็น: {$update_data['suggestions_status']}";
                }

                $user_data_json = json_encode([
                    'suggestion_id' => $suggestion_id,
                    'suggestion_topic' => $suggestion_data->suggestions_topic,
                    'updates' => $update_data,
                    'updated_by' => $staff_name,
                    'timestamp' => $current_time,
                    'type' => 'user_update_notification'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification = [
                    'type' => 'suggestion_update',
                    'title' => 'อัปเดตข้อเสนอแนะของคุณ',
                    'message' => $user_message,
                    'reference_id' => 0,
                    'reference_table' => 'tbl_suggestions',
                    'target_role' => 'public',
                    'target_user_id' => intval($suggestion_data->suggestions_user_id),
                    'priority' => 'high',
                    'icon' => 'fas fa-reply',
                    'url' => site_url("Suggestions/my_suggestion_detail/{$suggestion_id}"),
                    'data' => $user_data_json,
                    'created_at' => $current_time,
                    'created_by' => $staff_data->m_id,
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $user_result = $this->db->insert('tbl_notifications', $user_notification);

                if ($user_result) {
                    log_message('info', "User notification created successfully for suggestion update: {$suggestion_id}");
                }
            }

            log_message('info', "Notifications created successfully for suggestion {$suggestion_id} update");

        } catch (Exception $e) {
            log_message('error', 'Failed to create suggestion update notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * เตรียมข้อมูลพื้นฐานสำหรับ reports
     */
    private function prepare_reports_base_data($page_title = 'รายงาน')
    {
        try {
            // ดึงข้อมูล user แบบปลอดภัย
            $user_data = $this->get_safe_user_info_for_reports();

            $base_data = [
                'page_title' => $page_title,
                'user_info' => $user_data['user_info'],
                'is_logged_in' => $user_data['is_logged_in'],
                'user_type' => $user_data['user_type'],
                'tenant_name' => $user_data['tenant_name'] ?: 'ระบบรายงาน',
                'system_name' => 'ระบบรายงาน'
            ];

            // ตรวจสอบขั้นสุดท้าย
            if (!is_object($base_data['user_info'])) {
                log_message('warning', 'user_info is still not object, forcing conversion');
                $base_data['user_info'] = $this->create_default_user_object();
            }

            return $base_data;

        } catch (Exception $e) {
            log_message('error', 'Error in prepare_reports_base_data: ' . $e->getMessage());

            return [
                'page_title' => $page_title,
                'user_info' => $this->create_default_user_object(),
                'is_logged_in' => false,
                'user_type' => 'guest',
                'tenant_name' => 'ระบบรายงาน',
                'system_name' => 'ระบบรายงาน'
            ];
        }
    }

    /**
     * ดึงข้อมูล user แบบปลอดภัยสำหรับ reports
     */
    private function get_safe_user_info_for_reports()
    {
        $result = [
            'user_info' => null,
            'is_logged_in' => false,
            'user_type' => 'guest',
            'tenant_name' => $this->session->userdata('tenant_name') ?: 'ระบบรายงาน'
        ];

        try {
            // ตรวจสอบ staff user
            $m_id = $this->session->userdata('m_id');
            if (!empty($m_id)) {
                $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_img, m_system, m_status');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $m_id);
                $staff_data = $this->db->get()->row();

                if ($staff_data && ($staff_data->m_status == '1' || $staff_data->m_status == 1)) {
                    $result['user_info'] = (object) [
                        'm_id' => $staff_data->m_id,
                        'm_email' => $staff_data->m_email ?: '',
                        'm_fname' => $staff_data->m_fname ?: 'Unknown',
                        'm_lname' => $staff_data->m_lname ?: 'User',
                        'm_phone' => $staff_data->m_phone ?: '',
                        'm_img' => $staff_data->m_img ?: '',
                        'm_system' => $staff_data->m_system ?: '',
                        'm_status' => $staff_data->m_status,
                        'pname' => 'เจ้าหน้าที่',
                        'peng' => 'Staff'
                    ];
                    $result['is_logged_in'] = true;
                    $result['user_type'] = 'staff';

                    return $result;
                }
            }

            // ตรวจสอบ public user
            $mp_id = $this->session->userdata('mp_id');
            if (!empty($mp_id)) {
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_status');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $public_data = $this->db->get()->row();

                if ($public_data && ($public_data->mp_status == 1 || $public_data->mp_status == '1')) {
                    $result['user_info'] = (object) [
                        'm_id' => $public_data->id,
                        'm_email' => $public_data->mp_email ?: '',
                        'm_fname' => $public_data->mp_fname ?: 'Unknown',
                        'm_lname' => $public_data->mp_lname ?: 'User',
                        'm_phone' => $public_data->mp_phone ?: '',
                        'm_img' => '',
                        'm_system' => 'public',
                        'm_status' => $public_data->mp_status,
                        'pname' => 'สมาชิก',
                        'peng' => 'Public Member'
                    ];
                    $result['is_logged_in'] = true;
                    $result['user_type'] = 'public';

                    return $result;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error in get_safe_user_info_for_reports: ' . $e->getMessage());
        }

        // กรณี guest หรือเกิดข้อผิดพลาด
        $result['user_info'] = $this->create_default_user_object();
        return $result;
    }

    /**
     * สร้าง default user object
     */
    private function create_default_user_object()
    {
        return (object) [
            'm_id' => 0,
            'm_email' => '',
            'm_fname' => 'Guest',
            'm_lname' => 'User',
            'm_phone' => '',
            'm_img' => '',
            'm_system' => '',
            'm_status' => '',
            'pname' => 'ผู้เยี่ยมชม',
            'peng' => 'Visitor'
        ];
    }

    /**
     * Helper functions สำหรับ status
     */
    private function get_suggestion_status_display($status)
    {
        $status_map = [
            'received' => 'ได้รับแล้ว',
            'reviewing' => 'กำลังพิจารณา',
            'replied' => 'ตอบกลับแล้ว',
            'closed' => 'ปิดเรื่อง'
        ];

        return $status_map[$status] ?? $status;
    }

    private function get_suggestion_status_class($status)
    {
        $class_map = [
            'received' => 'suggestion-status-received',
            'reviewing' => 'suggestion-status-reviewing',
            'replied' => 'suggestion-status-replied',
            'closed' => 'suggestion-status-closed'
        ];

        return $class_map[$status] ?? 'suggestion-status-unknown';
    }

    private function get_suggestion_status_icon($status)
    {
        $icon_map = [
            'received' => 'fas fa-inbox',
            'reviewing' => 'fas fa-search',
            'replied' => 'fas fa-reply',
            'closed' => 'fas fa-check-circle'
        ];

        return $icon_map[$status] ?? 'fas fa-question-circle';
    }

    private function get_suggestion_status_color($status)
    {
        $color_map = [
            'received' => '#FFC700',
            'reviewing' => '#17a2b8',
            'replied' => '#28a745',
            'closed' => '#6c757d'
        ];

        return $color_map[$status] ?? '#6c757d';
    }

    /**
     * Helper functions สำหรับ type
     */
    private function get_suggestion_type_display($type)
    {
        $type_map = [
            'suggestion' => 'ข้อเสนอแนะ',
            'feedback' => 'ความคิดเห็น',
            'improvement' => 'การปรับปรุง'
        ];

        return $type_map[$type] ?? $type;
    }

    private function get_suggestion_type_icon($type)
    {
        $icon_map = [
            'suggestion' => 'fas fa-lightbulb',
            'feedback' => 'fas fa-comment-dots',
            'improvement' => 'fas fa-chart-line'
        ];

        return $icon_map[$type] ?? 'fas fa-comment';
    }

    /**
     * Helper functions สำหรับ priority
     */
    private function get_suggestion_priority_display($priority)
    {
        $priority_map = [
            'low' => 'ต่ำ',
            'normal' => 'ปกติ',
            'high' => 'สูง',
            'urgent' => 'เร่งด่วน'
        ];

        return $priority_map[$priority] ?? $priority;
    }

    /**
     * Helper functions สำหรับไฟล์
     */
    private function is_image_file($mime_type)
    {
        $image_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        return in_array(strtolower($mime_type), $image_types);
    }

    private function get_file_icon($mime_type)
    {
        $icons = [
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'image/jpeg' => 'fa-file-image',
            'image/jpg' => 'fa-file-image',
            'image/png' => 'fa-file-image',
            'image/gif' => 'fa-file-image',
            'image/webp' => 'fa-file-image',
            'text/plain' => 'fa-file-alt'
        ];

        return $icons[strtolower($mime_type)] ?? 'fa-file';
    }

    private function format_file_size($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Format วันที่เป็นภาษาไทย
     */
    private function format_thai_date($datetime)
    {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return '-';
        }

        try {
            $thai_months = [
                '01' => 'ม.ค.',
                '02' => 'ก.พ.',
                '03' => 'มี.ค.',
                '04' => 'เม.ย.',
                '05' => 'พ.ค.',
                '06' => 'มิ.ย.',
                '07' => 'ก.ค.',
                '08' => 'ส.ค.',
                '09' => 'ก.ย.',
                '10' => 'ต.ค.',
                '11' => 'พ.ย.',
                '12' => 'ธ.ค.'
            ];

            $timestamp = strtotime($datetime);
            $day = date('j', $timestamp);
            $month = $thai_months[date('m', $timestamp)];
            $year = date('Y', $timestamp) + 543;
            $time = date('H:i', $timestamp);

            return $day . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';

        } catch (Exception $e) {
            return $datetime;
        }
    }

    // ===================================================================
    // *** PUBLIC HELPER FUNCTIONS สำหรับ VIEW ***
    // ===================================================================

    public function get_suggestion_status_class_for_view($status)
    {
        return $this->get_suggestion_status_class($status);
    }

    public function get_suggestion_status_color_for_view($status)
    {
        return $this->get_suggestion_status_color($status);
    }

    public function get_suggestion_status_icon_for_view($status)
    {
        return $this->get_suggestion_status_icon($status);
    }

    public function get_suggestion_type_display_for_view($type)
    {
        return $this->get_suggestion_type_display($type);
    }

    public function get_suggestion_type_icon_for_view($type)
    {
        return $this->get_suggestion_type_icon($type);
    }






    /**
     * ค้นหาข้อเสนอแนะตาม type และ user permissions (แก้ไขใหม่)
     */
    private function search_suggestion_by_type_and_user($search_type, $search_value, $login_info)
    {
        $suggestion_data = null;

        try {
            log_message('info', "Searching suggestion - Type: {$search_type}, Value: {$search_value}, User Type: {$login_info['user_type']}, Logged In: " . ($login_info['is_logged_in'] ? 'YES' : 'NO'));

            switch ($search_type) {
                case 'ref':
                    // ค้นหาด้วยหมายเลขอ้างอิง
                    $suggestion_data = $this->suggestions_model->get_suggestion_by_id($search_value);

                    if ($suggestion_data) {
                        log_message('info', "Found suggestion by ref: {$search_value} (User Type: {$suggestion_data->suggestions_user_type}, User ID: {$suggestion_data->suggestions_user_id})");
                    } else {
                        log_message('info', "No suggestion found by ref: {$search_value}");
                    }
                    break;

                case 'phone':
                    // ตรวจสอบรูปแบบเบอร์โทร
                    if (!preg_match('/^0\d{9}$/', $search_value)) {
                        log_message('warning', "Invalid phone format: {$search_value}");
                        return null;
                    }

                    // ค้นหาด้วยเบอร์โทรตาม user type
                    if ($login_info['is_logged_in'] && $login_info['user_type'] === 'public') {
                        // Public user: ค้นหาเฉพาะข้อเสนอแนะของตัวเองและที่เป็น public user type
                        $suggestions = $this->suggestions_model->get_suggestions_by_phone_and_user_type(
                            $search_value,
                            'public',
                            $login_info['user_info']['id']
                        );

                        log_message('info', "Phone search for PUBLIC user ID {$login_info['user_info']['id']}: " . count($suggestions) . " results");

                    } else {
                        // Guest user: ค้นหาเฉพาะข้อเสนอแนะที่เป็น guest user type
                        $suggestions = $this->suggestions_model->get_suggestions_by_phone_and_user_type(
                            $search_value,
                            'guest'
                        );

                        log_message('info', "Phone search for GUEST user: " . count($suggestions) . " results");
                    }

                    if (!empty($suggestions)) {
                        $suggestion_data = $suggestions[0]; // เอาอันแรก
                        log_message('info', "Selected first suggestion from phone search: {$suggestion_data->suggestions_id}");
                    }
                    break;

                case 'id_card':
                    // ตรวจสอบรูปแบบเลขบัตรประชาชน
                    if (!preg_match('/^\d{13}$/', $search_value) || !$this->validate_thai_id_card_backend($search_value)) {
                        log_message('warning', "Invalid ID card format: {$search_value}");
                        return null;
                    }

                    // ค้นหาด้วยเลขบัตรประชาชนตาม user type
                    if ($login_info['is_logged_in'] && $login_info['user_type'] === 'public') {
                        // Public user: ค้นหาเฉพาะข้อเสนอแนะของตัวเองและที่เป็น public user type
                        $suggestions = $this->suggestions_model->get_suggestions_by_id_card_and_user_type(
                            $search_value,
                            'public',
                            $login_info['user_info']['id']
                        );

                        log_message('info', "ID card search for PUBLIC user ID {$login_info['user_info']['id']}: " . count($suggestions) . " results");

                    } else {
                        // Guest user: ค้นหาเฉพาะข้อเสนอแนะที่เป็น guest user type
                        $suggestions = $this->suggestions_model->get_suggestions_by_id_card_and_user_type(
                            $search_value,
                            'guest'
                        );

                        log_message('info', "ID card search for GUEST user: " . count($suggestions) . " results");
                    }

                    if (!empty($suggestions)) {
                        $suggestion_data = $suggestions[0]; // เอาอันแรก
                        log_message('info', "Selected first suggestion from ID card search: {$suggestion_data->suggestions_id}");
                    }
                    break;

                default:
                    log_message('warning', "Invalid search type: {$search_type}");
                    return null;
            }

            return $suggestion_data;

        } catch (Exception $e) {
            log_message('error', 'Error in search_suggestion_by_type_and_user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงตาม user type
     */
    private function check_suggestion_access_by_user_type($suggestion_data, $login_info)
    {
        try {
            // Staff สามารถดูได้ทุกข้อเสนอแนะ
            if ($login_info['user_type'] === 'staff') {
                log_message('info', 'Access granted for STAFF user');
                return true;
            }

            // Public user ที่ login แล้ว
            if ($login_info['is_logged_in'] && $login_info['user_type'] === 'public') {
                // ต้องเป็นข้อเสนอแนะของตัวเองและเป็น public user type
                $has_access = ($suggestion_data->suggestions_user_type === 'public' &&
                    $suggestion_data->suggestions_user_id == $login_info['user_info']['id']);

                log_message('info', 'Access check for PUBLIC user: ' . ($has_access ? 'GRANTED' : 'DENIED') .
                    " (suggestion_user_type: {$suggestion_data->suggestions_user_type}, " .
                    "suggestion_user_id: {$suggestion_data->suggestions_user_id}, " .
                    "current_user_id: {$login_info['user_info']['id']})");

                return $has_access;
            }

            // Guest user (ไม่ได้ login หรือ user_type เป็น guest)
            if (!$login_info['is_logged_in'] || $login_info['user_type'] === 'guest') {
                // สามารถดูได้เฉพาะข้อเสนอแนะที่เป็น guest user type เท่านั้น
                $has_access = ($suggestion_data->suggestions_user_type === 'guest');

                log_message('info', 'Access check for GUEST user: ' . ($has_access ? 'GRANTED' : 'DENIED') .
                    " (suggestion_user_type: {$suggestion_data->suggestions_user_type})");

                return $has_access;
            }

            log_message('warning', 'Unknown user type or login status for access check');
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error in check_suggestion_access_by_user_type: ' . $e->getMessage());
            return false;
        }
    }





    private function validate_thai_id_card_backend($id_card)
    {
        try {
            // ตรวจสอบว่าไม่เป็นค่าว่าง
            if (empty($id_card)) {
                log_message('debug', 'ID Card validation failed: empty value');
                return false;
            }

            // ลบช่องว่างและตัวอักษรที่ไม่ใช่ตัวเลข
            $id_card = preg_replace('/[^0-9]/', '', $id_card);

            // ตรวจสอบว่าเป็นตัวเลข 13 หลักเท่านั้น
            if (!preg_match('/^\d{13}$/', $id_card)) {
                log_message('debug', "ID Card validation failed: invalid format - {$id_card}");
                return false;
            }

            // ตรวจสอบเลขซ้ำทั้งหมด (เช่น 1111111111111)
            if (preg_match('/^(\d)\1{12}$/', $id_card)) {
                log_message('debug', "ID Card validation failed: all same digits - {$id_card}");
                return false;
            }

            // ตรวจสอบเลขบัตรที่เป็นที่รู้จักว่าไม่ถูกต้อง
            $invalid_patterns = [
                '0000000000000',
                '1234567890123',
                '9876543210987'
            ];

            if (in_array($id_card, $invalid_patterns)) {
                log_message('debug', "ID Card validation failed: known invalid pattern - {$id_card}");
                return false;
            }

            // ตรวจสอบด้วยอัลกอริทึม MOD 11 (Check Digit Algorithm)
            $digits = str_split($id_card);
            $weights = [13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int) $digits[$i] * $weights[$i];
            }

            $remainder = $sum % 11;
            $check_digit = ($remainder < 2) ? (1 - $remainder) : (11 - $remainder);

            $is_valid = ($check_digit == (int) $digits[12]);

            if (!$is_valid) {
                log_message('debug', "ID Card validation failed: check digit mismatch - {$id_card} (expected: {$check_digit}, got: {$digits[12]})");
            } else {
                log_message('debug', "ID Card validation successful: {$id_card}");
            }

            return $is_valid;

        } catch (Exception $e) {
            log_message('error', 'Error in validate_thai_id_card_backend: ' . $e->getMessage());
            return false;
        }
    }


    private function prepare_address_data_for_display($suggestion_data)
    {
        if (!$suggestion_data) {
            return null;
        }

        $address_data = [
            'has_address' => false,
            'source_type' => null,
            'additional_address' => '',
            'district' => '',
            'amphoe' => '',
            'province' => '',
            'zipcode' => '',
            'full_address' => '',
            'formatted_address' => '',
            'display_short' => 'ไม่ระบุที่อยู่',
            'display_full' => 'ไม่ระบุที่อยู่'
        ];

        try {
            // ใช้ข้อมูลจาก Model ถ้ามี
            if (isset($suggestion_data['full_address_details']) && is_array($suggestion_data['full_address_details'])) {
                $address_details = $suggestion_data['full_address_details'];

                $address_data = array_merge($address_data, [
                    'has_address' => $address_details['has_address'] ?? false,
                    'source_type' => $address_details['source_type'] ?? null,
                    'additional_address' => $address_details['additional_address'] ?? '',
                    'district' => $address_details['district'] ?? '',
                    'amphoe' => $address_details['amphoe'] ?? '',
                    'province' => $address_details['province'] ?? '',
                    'zipcode' => $address_details['zipcode'] ?? '',
                    'full_address' => $address_details['full_address'] ?? '',
                    'formatted_address' => $address_details['formatted_address'] ?? ''
                ]);
            }

            // ใช้ข้อมูลจาก display_address ถ้ามี
            if (isset($suggestion_data['display_address']) && is_array($suggestion_data['display_address'])) {
                $display_details = $suggestion_data['display_address'];

                $address_data['display_short'] = $display_details['short'] ?? 'ไม่ระบุที่อยู่';
                $address_data['display_full'] = $display_details['full'] ?? 'ไม่ระบุที่อยู่';
            }

            // ถ้าไม่มีข้อมูลจาก Model ให้สร้างเอง
            if (!$address_data['has_address']) {
                $address_data = $this->build_address_from_raw_data($suggestion_data, $address_data);
            }

            return $address_data;

        } catch (Exception $e) {
            log_message('error', 'Error preparing address data for display: ' . $e->getMessage());
            return $address_data;
        }
    }


    private function build_address_from_raw_data($suggestion_data, $default_address_data)
    {
        try {
            $user_type = $suggestion_data['suggestions_user_type'] ?? 'guest';
            $address_data = $default_address_data;

            // กำหนดข้อมูลตาม user type
            if ($user_type === 'public') {
                // Public User: ใช้ข้อมูลจาก profile ก่อน
                if (!empty($suggestion_data['mp_province_profile']) || !empty($suggestion_data['mp_address_profile'])) {
                    $address_data = array_merge($address_data, [
                        'has_address' => true,
                        'source_type' => 'public_profile',
                        'additional_address' => $suggestion_data['mp_address_profile'] ?? '',
                        'district' => $suggestion_data['mp_district_profile'] ?? '',
                        'amphoe' => $suggestion_data['mp_amphoe_profile'] ?? '',
                        'province' => $suggestion_data['mp_province_profile'] ?? '',
                        'zipcode' => $suggestion_data['mp_zipcode_profile'] ?? ''
                    ]);
                }
                // ถ้าไม่มีใน profile ให้ใช้ข้อมูลจากฟอร์ม
                elseif (!empty($suggestion_data['suggestions_address']) || !empty($suggestion_data['guest_province'])) {
                    $address_data = array_merge($address_data, [
                        'has_address' => true,
                        'source_type' => 'suggestion_form',
                        'additional_address' => $suggestion_data['suggestions_address'] ?? '',
                        'district' => $suggestion_data['guest_district'] ?? '',
                        'amphoe' => $suggestion_data['guest_amphoe'] ?? '',
                        'province' => $suggestion_data['guest_province'] ?? '',
                        'zipcode' => $suggestion_data['guest_zipcode'] ?? ''
                    ]);
                }

            } elseif ($user_type === 'guest') {
                // Guest User: ใช้ข้อมูลจากฟอร์ม
                if (!empty($suggestion_data['suggestions_address']) || !empty($suggestion_data['guest_province'])) {
                    $address_data = array_merge($address_data, [
                        'has_address' => true,
                        'source_type' => 'guest',
                        'additional_address' => $suggestion_data['suggestions_address'] ?? '',
                        'district' => $suggestion_data['guest_district'] ?? '',
                        'amphoe' => $suggestion_data['guest_amphoe'] ?? '',
                        'province' => $suggestion_data['guest_province'] ?? '',
                        'zipcode' => $suggestion_data['guest_zipcode'] ?? ''
                    ]);
                }

            } elseif ($user_type === 'staff') {
                // Staff: ใช้ข้อมูลจากฟอร์ม
                if (!empty($suggestion_data['suggestions_address'])) {
                    $address_data = array_merge($address_data, [
                        'has_address' => true,
                        'source_type' => 'staff',
                        'additional_address' => $suggestion_data['suggestions_address'] ?? ''
                    ]);
                }
            }

            // สร้างที่อยู่รวม
            if ($address_data['has_address']) {
                $address_parts = [];

                if (!empty($address_data['additional_address'])) {
                    $address_parts[] = $address_data['additional_address'];
                }

                if (!empty($address_data['district'])) {
                    $address_parts[] = 'ตำบล' . $address_data['district'];
                }

                if (!empty($address_data['amphoe'])) {
                    $address_parts[] = 'อำเภอ' . $address_data['amphoe'];
                }

                if (!empty($address_data['province'])) {
                    $address_parts[] = 'จังหวัด' . $address_data['province'];
                }

                if (!empty($address_data['zipcode'])) {
                    $address_parts[] = $address_data['zipcode'];
                }

                $address_data['full_address'] = implode(' ', $address_parts);

                // สร้าง formatted address
                $formatted_parts = [];

                if (!empty($address_data['additional_address'])) {
                    $formatted_parts[] = $address_data['additional_address'];
                }

                $location_parts = [];
                if (!empty($address_data['district']))
                    $location_parts[] = 'ต.' . $address_data['district'];
                if (!empty($address_data['amphoe']))
                    $location_parts[] = 'อ.' . $address_data['amphoe'];
                if (!empty($address_data['province']))
                    $location_parts[] = 'จ.' . $address_data['province'];
                if (!empty($address_data['zipcode']))
                    $location_parts[] = $address_data['zipcode'];

                if (!empty($location_parts)) {
                    $formatted_parts[] = implode(' ', $location_parts);
                }

                $address_data['formatted_address'] = implode('< >', $formatted_parts);

                // สร้าง display data
                $address_data['display_short'] = !empty($address_data['province']) ?
                    'จังหวัด' . $address_data['province'] : 'ไม่ระบุจังหวัด';

                $address_data['display_full'] = !empty($address_data['formatted_address']) ?
                    $address_data['formatted_address'] : $address_data['full_address'];
            }

            return $address_data;

        } catch (Exception $e) {
            log_message('error', 'Error building address from raw data: ' . $e->getMessage());
            return $default_address_data;
        }
    }









    /**
     * *** เพิ่มใหม่: ตรวจสอบสิทธิ์การดูข้อมูลต้นฉบับ ***
     */
    private function can_view_original_data($user_type, $user_info = null)
    {
        // เฉพาะ Staff เท่านั้นที่สามารถดูข้อมูลต้นฉบับได้
        return ($user_type === 'staff');
    }

    /**
     * *** เพิ่มใหม่: Helper functions สำหรับ View ***
     */
    public function get_censored_email_for_view($email, $user_type = 'guest')
    {
        if ($this->can_view_original_data($user_type)) {
            return htmlspecialchars($email);
        } else {
            return htmlspecialchars($this->censor_email($email));
        }
    }

    public function get_censored_phone_for_view($phone, $user_type = 'guest')
    {
        if ($this->can_view_original_data($user_type)) {
            return htmlspecialchars($phone);
        } else {
            return htmlspecialchars($this->censor_phone($phone));
        }
    }

    public function get_censored_id_card_for_view($id_card, $user_type = 'guest')
    {
        if ($this->can_view_original_data($user_type)) {
            return htmlspecialchars($id_card);
        } else {
            return htmlspecialchars($this->censor_id_card($id_card));
        }
    }

    public function get_censored_name_for_view($name, $user_type = 'guest')
    {
        if ($this->can_view_original_data($user_type)) {
            return htmlspecialchars($name);
        } else {
            return htmlspecialchars($this->censor_name($name));
        }
    }

    /**
     * API: ดึงสถิติรับฟังความคิดเห็น สำหรับ Dashboard
     */
    public function api_suggestions_summary()
    {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Access-Control-Allow-Origin: *');

        try {
            // โหลด Model ถ้าจำเป็น
            if (!isset($this->suggestions_model)) {
                $this->load->model('Suggestions_model', 'suggestions_model');
            }

            // ดึงสถิติจาก Model
            $suggestions_stats = $this->get_suggestions_dashboard_stats();

            echo json_encode([
                'success' => true,
                'suggestions' => $suggestions_stats,
                'timestamp' => date('Y-m-d H:i:s'),
                'debug' => ENVIRONMENT === 'development' ? [
                    'table_exists' => $this->db->table_exists('tbl_suggestions'),
                    'query_executed' => true,
                    'model_loaded' => isset($this->suggestions_model)
                ] : null
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in Suggestions/api_suggestions_summary: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'suggestions' => [
                    'total' => 0,
                    'new' => 0,
                    'reviewed' => 0,
                    'implemented' => 0
                ],
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'System error'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * ดึงสถิติสำหรับ Dashboard
     */
    private function get_suggestions_dashboard_stats()
    {
        try {
            // ใช้ Model method ถ้ามี
            if (method_exists($this->suggestions_model, 'get_dashboard_summary')) {
                return $this->suggestions_model->get_dashboard_summary();
            }

            // ดึงข้อมูลโดยตรงจากฐานข้อมูล
            if (!$this->db->table_exists('tbl_suggestions')) {
                return [
                    'total' => 0,
                    'new' => 0,
                    'reviewed' => 0,
                    'implemented' => 0
                ];
            }

            // นับจำนวนรวม
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_suggestions');
            $total_result = $this->db->get()->row();
            $total = $total_result ? (int) $total_result->total : 0;

            // นับตามสถานะ
            $this->db->select('suggestions_status, COUNT(*) as count');
            $this->db->from('tbl_suggestions');
            $this->db->group_by('suggestions_status');
            $status_results = $this->db->get()->result();

            $stats = [
                'total' => $total,
                'new' => 0,        // received
                'reviewed' => 0,   // reviewing + replied  
                'implemented' => 0 // closed
            ];

            foreach ($status_results as $result) {
                $count = (int) $result->count;

                switch ($result->suggestions_status) {
                    case 'received':
                        $stats['new'] = $count;
                        break;

                    case 'reviewing':
                    case 'replied':
                        $stats['reviewed'] += $count;
                        break;

                    case 'closed':
                        $stats['implemented'] = $count;
                        break;

                    default:
                        // สถานะอื่นๆ นับเป็น new
                        $stats['new'] += $count;
                        break;
                }
            }

            log_message('info', 'Dashboard suggestions stats: ' . json_encode($stats));

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error getting dashboard stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'new' => 0,
                'reviewed' => 0,
                'implemented' => 0
            ];
        }
    }







    public function delete_suggestion()
    {
        // ป้องกัน error output ทั้งหมด
        ini_set('display_errors', 0);
        error_reporting(0);

        // ล้าง output buffer และบังคับ JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }

        // เปิด output buffering ใหม่
        ob_start();

        try {
            // ตั้งค่า headers ทันที
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST');
            header('Access-Control-Allow-Headers: Content-Type');

            // ตรวจสอบ request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            // ตรวจสอบ staff session
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                throw new Exception('กรุณาเข้าสู่ระบบก่อน');
            }

            // ตรวจสอบ Staff ใน Database
            $this->db->select('m_id, m_fname, m_lname, m_email, m_status, m_system');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $staff_data = $this->db->get()->row();

            if (!$staff_data || $staff_data->m_status != '1') {
                throw new Exception('ไม่พบข้อมูลผู้ใช้หรือบัญชีถูกปิดใช้งาน');
            }

            // ตรวจสอบสิทธิ์ - เฉพาะ system_admin และ super_admin เท่านั้น
            if (!in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                throw new Exception('คุณไม่มีสิทธิ์ในการลบข้อเสนอแนะ (เฉพาะ System Admin และ Super Admin เท่านั้น)');
            }

            // รับข้อมูล POST
            $suggestion_id = $this->input->post('suggestion_id');
            $delete_reason = $this->input->post('delete_reason');

            // Validation
            if (empty($suggestion_id)) {
                throw new Exception('กรุณาระบุหมายเลขข้อเสนอแนะที่ต้องการลบ');
            }

            // ตรวจสอบว่าข้อเสนอแนะมีอยู่จริง
            $suggestion_data = $this->suggestions_model->get_suggestion_by_id($suggestion_id);

            if (!$suggestion_data) {
                throw new Exception('ไม่พบข้อเสนอแนะที่ระบุ');
            }

            // เริ่ม transaction
            $this->db->trans_start();

            $staff_name = $staff_data->m_fname . ' ' . $staff_data->m_lname;
            $current_time = date('Y-m-d H:i:s');

            // บันทึกประวัติการลบก่อน (แบบปลอดภัย)
            try {
                $delete_history_data = [
                    'action_type' => 'deleted',
                    'action_description' => 'ลบข้อเสนอแนะโดย ' . $staff_name . ' (' . $staff_data->m_system . ')',
                    'action_by' => $staff_name,
                    'action_date' => $current_time,
                    'old_status' => $suggestion_data->suggestions_status ?? 'unknown',
                    'new_status' => 'deleted',
                    'additional_data' => json_encode([
                        'delete_reason' => $delete_reason ?: 'ไม่ระบุเหตุผล',
                        'deleted_by_system' => $staff_data->m_system,
                        'deleted_by_id' => $staff_data->m_id,
                        'suggestion_topic' => $suggestion_data->suggestions_topic ?? '',
                        'suggestion_by' => $suggestion_data->suggestions_by ?? '',
                        'suggestion_phone' => $suggestion_data->suggestions_phone ?? '',
                        'original_date' => $suggestion_data->suggestions_datesave ?? ''
                    ], JSON_UNESCAPED_UNICODE)
                ];

                // บันทึกประวัติก่อนลบ (ใช้ method ที่ปลอดภัย)
                if (method_exists($this->suggestions_model, 'add_suggestion_history')) {
                    $history_result = $this->suggestions_model->add_suggestion_history(
                        $suggestion_id,
                        $delete_history_data['action_type'],
                        $delete_history_data['action_description'],
                        $delete_history_data['action_by'],
                        $delete_history_data['old_status'],
                        $delete_history_data['new_status'],
                        json_decode($delete_history_data['additional_data'], true)
                    );
                } else {
                    // Fallback: บันทึกประวัติด้วยวิธีง่ายๆ
                    $history_result = true;
                    log_message('info', 'History logged for deletion: ' . $suggestion_id);
                }

            } catch (Exception $e) {
                log_message('error', 'Error saving delete history: ' . $e->getMessage());
                $history_result = true; // อย่าให้หยุดการลบเพราะบันทึกประวัติไม่ได้
            }

            // ลบไฟล์แนบ (แบบปลอดภัย)
            try {
                if (method_exists($this->suggestions_model, 'get_suggestion_files')) {
                    $files = $this->suggestions_model->get_suggestion_files($suggestion_id);
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            // ลบไฟล์จริงจาก storage
                            $file_path = './docs/suggestions_files/' . ($file->suggestions_file_name ?? '');
                            if (file_exists($file_path)) {
                                @unlink($file_path);
                            }

                            // ลบจากฐานข้อมูล
                            if (method_exists($this->suggestions_model, 'delete_suggestion_file')) {
                                $this->suggestions_model->delete_suggestion_file($file->suggestions_file_id ?? 0, $staff_name);
                            } else {
                                // Fallback: ลบด้วย query ธรรมดา
                                $this->db->where('suggestions_file_id', $file->suggestions_file_id ?? 0);
                                $this->db->delete('tbl_suggestions_files');
                            }
                        }
                        log_message('info', "Deleted " . count($files) . " files for suggestion: {$suggestion_id}");
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'Error deleting suggestion files: ' . $e->getMessage());
                // อย่าให้หยุดการลบเพราะลบไฟล์ไม่ได้
            }

            // ลบข้อเสนอแนะ (แบบปลอดภัย)
            try {
                if (method_exists($this->suggestions_model, 'delete_suggestion')) {
                    $delete_result = $this->suggestions_model->delete_suggestion($suggestion_id, $staff_name);
                } else {
                    // Fallback: ลบด้วย query ธรรมดา
                    $this->db->where('suggestions_id', $suggestion_id);
                    $delete_result = $this->db->delete('tbl_suggestions');
                }

                if (!$delete_result) {
                    throw new Exception('ไม่สามารถลบข้อเสนอแนะได้');
                }

            } catch (Exception $e) {
                $this->db->trans_rollback();
                throw new Exception('ไม่สามารถลบข้อเสนอแนะได้: ' . $e->getMessage());
            }

            // Commit Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // สร้างการแจ้งเตือน (แบบปลอดภัย)
            try {
                if ($this->db->table_exists('tbl_notifications')) {

                    $notification_message = "ข้อเสนอแนะ #{$suggestion_id} ถูกลบโดย {$staff_name}";
                    if (!empty($suggestion_data->suggestions_topic)) {
                        $notification_message .= " (หัวข้อ: " . mb_substr($suggestion_data->suggestions_topic, 0, 50) . ")";
                    }

                    $notification_data = [
                        'suggestion_id' => $suggestion_id,
                        'topic' => $suggestion_data->suggestions_topic ?? '',
                        'deleted_by' => $staff_name,
                        'deleted_by_system' => $staff_data->m_system,
                        'delete_reason' => $delete_reason ?: 'ไม่ระบุเหตุผล',
                        'original_requester' => $suggestion_data->suggestions_by ?? '',
                        'original_phone' => $suggestion_data->suggestions_phone ?? '',
                        'timestamp' => $current_time,
                        'type' => 'suggestion_deleted'
                    ];

                    $notification_data_json = json_encode($notification_data, JSON_UNESCAPED_UNICODE);
                    if ($notification_data_json === false) {
                        $notification_data_json = '{}';
                    }

                    $staff_notification = [
                        'type' => 'suggestion_deleted',
                        'title' => 'ข้อเสนอแนะถูกลบ',
                        'message' => $notification_message,
                        'reference_id' => 0,
                        'reference_table' => 'tbl_suggestions',
                        'target_role' => 'staff',
                        'priority' => 'high',
                        'icon' => 'fas fa-trash',
                        'url' => site_url("Suggestions/suggestions_report"),
                        'data' => $notification_data_json,
                        'created_at' => $current_time,
                        'created_by' => $staff_data->m_id,
                        'is_read' => 0,
                        'is_system' => 1,
                        'is_archived' => 0
                    ];

                    $notification_result = $this->db->insert('tbl_notifications', $staff_notification);

                    if ($notification_result) {
                        log_message('info', "Delete notification created successfully for suggestion: {$suggestion_id}");
                    }
                }

            } catch (Exception $e) {
                log_message('error', 'Failed to create delete notification: ' . $e->getMessage());
                // อย่าให้หยุดการลบเพราะสร้าง notification ไม่ได้
            }

            // บันทึก Log การลบ
            log_message('info', "Suggestion deleted successfully by {$staff_name} ({$staff_data->m_system}): ID={$suggestion_id}, Topic=" . ($suggestion_data->suggestions_topic ?? 'N/A') . ", Reason=" . ($delete_reason ?: 'ไม่ระบุเหตุผล'));

            // เตรียม response
            $response = [
                'success' => true,
                'message' => 'ลบข้อเสนอแนะสำเร็จแล้ว',
                'data' => [
                    'suggestion_id' => $suggestion_id,
                    'deleted_by' => $staff_name,
                    'deleted_by_system' => $staff_data->m_system,
                    'delete_reason' => $delete_reason ?: 'ไม่ระบุเหตุผล',
                    'timestamp' => $current_time,
                    'original_topic' => $suggestion_data->suggestions_topic ?? '',
                    'original_requester' => $suggestion_data->suggestions_by ?? ''
                ]
            ];

            // ล้าง buffer และส่ง JSON
            ob_clean();
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // Rollback ถ้ามี transaction
            if (isset($this->db) && method_exists($this->db, 'trans_status')) {
                if ($this->db->trans_status() !== FALSE) {
                    $this->db->trans_rollback();
                }
            }

            log_message('error', 'Critical error in delete_suggestion: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            $error_response = [
                'success' => false,
                'message' => $e->getMessage(),
                'debug' => ENVIRONMENT === 'development' ? [
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ];

            // ล้าง buffer และส่ง error JSON
            ob_clean();
            echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
        }

        // บังคับจบการทำงาน
        ob_end_flush();
        exit;
    }


}




?>