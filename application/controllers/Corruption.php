<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Corruption Controller
 * จัดการระบบแจ้งเรื่องร้องเรียนการทุจริต
 */
class Corruption extends CI_Controller
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

        // โหลด models และ libraries
        $this->load->model('Corruption_model', 'corruption_model');
        $this->load->model('activity_model');
        $this->load->model('news_model');
        $this->load->model('HotNews_model');
        $this->load->model('Weather_report_model');
        $this->load->model('member_public_model');

        $this->load->library('upload');
        $this->load->library('form_validation');
        $this->load->helper(['url', 'file', 'security']);

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
    // *** หน้าแสดงฟอร์มแจ้งเรื่องร้องเรียนการทุจริต ***
    // ===================================================================


    public function report_form()
    {
        try {
            log_message('info', '=== CORRUPTION REPORT FORM START ===');

            // เตรียมข้อมูลพื้นฐานสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            // ตรวจสอบสถานะ login อย่างละเอียด
            $current_user = $this->get_current_user_for_corruption_report();

            log_message('info', 'User Check Result: ' . json_encode([
                'is_logged_in' => $current_user['is_logged_in'],
                'user_type' => $current_user['user_type'],
                'has_user_info' => !empty($current_user['user_info']),
                'user_name' => $current_user['user_info']['name'] ?? 'N/A',
                'user_id' => $current_user['user_info']['id'] ?? 'N/A'
            ]));

            // ตรวจสอบและจัดการ Staff User
            $access_denied = false;
            $access_message = '';
            $staff_info = null;

            if ($current_user['user_type'] === 'staff') {
                $access_denied = true;
                $access_message = 'ท่านเข้าสู่ระบบในฐานะเจ้าหน้าที่ ไม่สามารถแจ้งรายงานการทุจริตได้';
                $staff_info = $current_user['user_info'];

                log_message('info', 'Staff Access Denied: ' . ($staff_info['name'] ?? 'Unknown') . ' (System: ' . ($staff_info['m_system'] ?? 'N/A') . ')');
            }

            // เตรียมข้อมูลสำหรับส่งไปยัง View
            $data['is_logged_in'] = $current_user['is_logged_in'];
            $data['user_type'] = $current_user['user_type'];
            $data['user_info'] = $current_user['user_info'];
            $data['user_address'] = $current_user['user_address'];
            $data['access_denied'] = $access_denied;
            $data['access_message'] = $access_message;
            $data['staff_info'] = $staff_info;
            $data['logout_url'] = site_url('User/logout');

            // เตรียมข้อมูลสำหรับ JavaScript
            $js_user_data = [
                'is_logged_in' => $current_user['is_logged_in'],
                'user_type' => $current_user['user_type'],
                'access_denied' => $access_denied,
                'user_info' => null
            ];

            if ($current_user['is_logged_in'] && $current_user['user_type'] === 'public') {
                $js_user_data['user_info'] = [
                    'name' => $current_user['user_info']['name'] ?? '',
                    'phone' => $current_user['user_info']['phone'] ?? '',
                    'email' => $current_user['user_info']['email'] ?? '',
                    'position' => $current_user['user_info']['position'] ?? 'ประชาชน'
                ];
            } elseif ($current_user['is_logged_in'] && $current_user['user_type'] === 'staff') {
                $js_user_data['user_info'] = [
                    'name' => $current_user['user_info']['name'] ?? '',
                    'position' => $current_user['user_info']['position'] ?? 'เจ้าหน้าที่',
                    'm_system' => $current_user['user_info']['m_system'] ?? ''
                ];
            }

            $data['js_user_data'] = $js_user_data;

            // ข้อมูลตัวเลือกสำหรับฟอร์ม
            $data['corruption_types'] = $this->get_corruption_types();
            $data['reporter_relations'] = $this->get_reporter_relations();

            // ข้อมูลหน้าเว็บ
            $data['page_title'] = 'แจ้งเรื่องร้องเรียนการทุจริต';
            $data['page_description'] = 'ช่องทางการแจ้งเรื่องร้องเรียนการทุจริตและประพฤติมิชอบ';

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => site_url('Pages/service_systems')],
                ['title' => 'แจ้งเรื่องร้องเรียนการทุจริต', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // ข้อมูลเพิ่มเติมสำหรับการจัดการ
            $data['csrf_token'] = $this->security->get_csrf_hash();
            $data['form_action'] = site_url('Corruption/submit_report');
            $data['track_url'] = site_url('Corruption/track_status');

            // ตั้งค่าความปลอดภัย
            $data['max_file_size'] = 10 * 1024 * 1024; // 10MB
            $data['max_files'] = 10;
            $data['allowed_extensions'] = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];

            // สถิติพื้นฐาน
            $data['corruption_stats'] = $this->get_corruption_stats_safe();

            // ข้อมูลการแจ้งเตือน
            $data['notification_settings'] = [
                'show_guidelines' => true,
                'show_protection_notice' => true,
                'show_legal_warning' => true
            ];

            // ข้อมูลสำหรับ SEO และ Meta Tags
            $data['meta_keywords'] = 'แจ้งเรื่องร้องเรียน, การทุจริต, ศูนย์ร้องเรียน, ประพฤติมิชอบ';
            $data['meta_description'] = 'ช่องทางการแจ้งเรื่องร้องเรียนการทุจริตและประพฤติมิชอบอย่างปลอดภัยและเป็นความลับ';

            // ข้อมูลการติดต่อและช่วยเหลือ
            $data['help_info'] = [
                'hotline' => '1111',
                'email' => 'corruption@example.com',
                'office_hours' => 'จันทร์-ศุกร์ 08:30-16:30 น.',
                'emergency_contact' => '1669'
            ];

            // การตั้งค่าเฉพาะสำหรับ Development
            if (ENVIRONMENT === 'development') {
                $data['debug_mode'] = true;
                $data['debug_info'] = [
                    'session_id' => session_id(),
                    'user_ip' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent(),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'memory_usage' => memory_get_usage(true),
                    'execution_time' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
                ];

                $data['session_debug'] = [
                    'mp_id' => $this->session->userdata('mp_id'),
                    'mp_email' => $this->session->userdata('mp_email'),
                    'm_id' => $this->session->userdata('m_id'),
                    'logged_in' => $this->session->userdata('logged_in'),
                    'session_data_count' => count($this->session->all_userdata())
                ];

                $data['table_status'] = $this->corruption_model->check_required_tables();
            } else {
                $data['debug_mode'] = false;
            }

            // บันทึก Log การเข้าถึง
            log_message('info', 'Corruption Report Form Accessed: ' .
                'User Type: ' . $current_user['user_type'] .
                ', Logged In: ' . ($current_user['is_logged_in'] ? 'Yes' : 'No') .
                ', Access Denied: ' . ($access_denied ? 'Yes' : 'No') .
                ', IP: ' . $this->input->ip_address());

            // บันทึกการติดตาม
            try {
                if (method_exists($this->corruption_model, 'log_page_access')) {
                    $this->corruption_model->log_page_access(
                        'corruption_report_form',
                        $current_user,
                        $this->input->ip_address(),
                        $this->input->user_agent()
                    );
                }
            } catch (Exception $e) {
                log_message('warning', 'Could not log page access: ' . $e->getMessage());
            }

            log_message('info', '=== CORRUPTION REPORT FORM DATA PREPARED ===');

            // โหลด Views
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css', $data);
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/corruption_report_form', $data);
            $this->load->view('frontend_asset/js', $data);
            $this->load->view('frontend_templat/footer', $data);

            log_message('info', '=== CORRUPTION REPORT FORM END ===');

        } catch (Exception $e) {
            log_message('error', 'Critical Error in corruption report_form: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // บันทึกข้อผิดพลาดลงระบบ
            try {
                if (method_exists($this->corruption_model, 'log_system_error')) {
                    $this->corruption_model->log_system_error(
                        'corruption_report_form_error',
                        $e->getMessage(),
                        [
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'user_ip' => $this->input->ip_address(),
                            'user_agent' => $this->input->user_agent(),
                            'timestamp' => date('Y-m-d H:i:s')
                        ]
                    );
                }
            } catch (Exception $log_error) {
                log_message('error', 'Failed to log system error: ' . $log_error->getMessage());
            }

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาด: ' . $e->getMessage() . '<br>File: ' . $e->getFile() . '<br>Line: ' . $e->getLine(), 500);
            } else {
                $this->session->set_flashdata(
                    'error_message',
                    'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง หรือติดต่อเจ้าหน้าที่'
                );
                redirect('Pages/service_systems');
            }
        }
    }

    /**
     * บันทึกรายงานการทุจริต
     */
    /**
     * บันทึกรายงานการทุจริต
     */
    public function submit_report()
    {
        // ล้าง output buffer และตั้งค่า JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        // เปิด error reporting สำหรับ debug (ใน development)
        if (ENVIRONMENT === 'development') {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);
        }

        try {
            log_message('info', '=== CORRUPTION REPORT SUBMIT START (WITH RECAPTCHA) ===');
            log_message('info', 'POST data: ' . json_encode($_POST));
            log_message('info', 'FILES data count: ' . (isset($_FILES['evidence_files']['name']) ? count($_FILES['evidence_files']['name']) : 0));

            // ตรวจสอบ Request Method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Only POST method allowed');
            }

            // ตรวจสอบว่า database connected
            if (!$this->db->conn_id) {
                throw new Exception('Database connection failed');
            }

            // ตรวจสอบว่า Model โหลดสำเร็จหรือไม่
            if (!isset($this->corruption_model)) {
                log_message('info', 'Loading Corruption_model...');
                $this->load->model('Corruption_model', 'corruption_model');

                if (!isset($this->corruption_model)) {
                    throw new Exception('Failed to load Corruption_model');
                }
                log_message('info', 'Corruption_model loaded successfully');
            }

            // ตรวจสอบว่า tables มีอยู่จริง
            $required_tables = ['tbl_corruption_reports', 'tbl_corruption_files'];
            foreach ($required_tables as $table) {
                if (!$this->db->table_exists($table)) {
                    throw new Exception("Required table '{$table}' does not exist");
                }
            }
            log_message('info', 'All required tables exist');

            // *** ⭐ เพิ่ม: ตรวจสอบ reCAPTCHA Token ***
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'corruption_report_submit';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'corruption_form';
            $user_type_detected = $this->input->post('user_type_detected') ?: 'guest';
            $is_ajax = $this->input->post('ajax_request') === '1';
            $dev_mode = $this->input->post('dev_mode') === '1';

            log_message('info', 'reCAPTCHA info for corruption: ' . json_encode([
                'has_token' => !empty($recaptcha_token),
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => $user_type_detected,
                'is_ajax' => $is_ajax,
                'dev_mode' => $dev_mode
            ]));

            // *** ⭐ เพิ่ม: ตรวจสอบ reCAPTCHA (ยกเว้นโหมด development) ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                // *** โหลด reCAPTCHA Library ***
                if (!isset($this->recaptcha_lib)) {
                    $this->load->library('recaptcha_lib');
                }

                // *** เตรียม options สำหรับ corruption report ***
                $recaptcha_options = [
                    'action' => $recaptcha_action,
                    'source' => $recaptcha_source,
                    'user_type_detected' => $user_type_detected,
                    'form_source' => 'corruption_report_submission',
                    'client_timestamp' => $this->input->post('client_timestamp'),
                    'user_agent_info' => $this->input->post('user_agent_info'),
                    'is_anonymous' => $this->input->post('is_anonymous') === '1'
                ];

                // *** กำหนด user_type ที่ถูกต้องสำหรับ Library ***
                $library_user_type = 'citizen'; // default for corruption reports
                if ($user_type_detected === 'staff' || $user_type_detected === 'admin') {
                    $library_user_type = 'staff';
                }

                // *** เรียกใช้ reCAPTCHA verification ***
                $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $library_user_type, null, $recaptcha_options);

                log_message('info', 'reCAPTCHA verification result for corruption: ' . json_encode([
                    'success' => $recaptcha_result['success'],
                    'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                    'action' => $recaptcha_action,
                    'source' => $recaptcha_source,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]));

                // *** ตรวจสอบผลลัพธ์ reCAPTCHA ***
                if (!$recaptcha_result['success']) {
                    log_message('info', 'reCAPTCHA verification failed for corruption: ' . json_encode([
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
                        'error_code' => 'RECAPTCHA_FAILED',
                        'error_type' => 'recaptcha_failed',
                        'recaptcha_data' => $recaptcha_result['data']
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                log_message('info', 'reCAPTCHA verification successful for corruption: ' . json_encode([
                    'score' => $recaptcha_result['data']['score'],
                    'action' => $recaptcha_action,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]));

            } else if (!$dev_mode) {
                // *** ไม่มี reCAPTCHA token ***
                log_message('info', 'No reCAPTCHA token provided for corruption report');

                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการยืนยันตัวตน',
                    'error_code' => 'RECAPTCHA_MISSING',
                    'error_type' => 'recaptcha_missing'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                log_message('info', 'reCAPTCHA check skipped for corruption report (dev_mode)');
            }

            // ตรวจสอบข้อมูลผู้ใช้และสิทธิ์
            $current_user = $this->get_current_user_for_corruption_report();
            log_message('info', 'Current user type: ' . $current_user['user_type']);

            // ตรวจสอบว่าเป็น staff หรือไม่
            if ($current_user['user_type'] === 'staff') {
                log_message('info', 'Staff access denied for corruption report');
                echo json_encode([
                    'success' => false,
                    'message' => 'ท่านไม่สามารถแจ้งรายงานการทุจริตได้ กรุณาออกจากระบบ login บุคลากรภายใน',
                    'error_code' => 'STAFF_ACCESS_DENIED',
                    'error_type' => 'access_denied'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** ⭐ เพิ่ม: ตรวจสอบคำหยาบและ URL (สำหรับ corruption report) ***
            $complaint_subject = trim($this->input->post('complaint_subject'));
            $complaint_detail = trim($this->input->post('complaint_detail'));
            $combined_text = $complaint_subject . ' ' . $complaint_detail;

            // ตรวจสอบคำหยาบ
            if (method_exists($this, 'check_vulgar_word')) {
                $vulgar_result = $this->check_vulgar_word($combined_text);
                if ($vulgar_result['found']) {
                    log_message('info', 'Vulgar words detected in corruption report: ' . json_encode([
                        'vulgar_words' => $vulgar_result['words'],
                        'subject' => $complaint_subject
                    ]));

                    echo json_encode([
                        'success' => false,
                        'vulgar_detected' => true,
                        'vulgar_words' => $vulgar_result['words'],
                        'message' => 'พบคำไม่เหมาะสมในรายงาน',
                        'error_code' => 'VULGAR_CONTENT',
                        'error_type' => 'vulgar_content'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            // ตรวจสอบ URL
            if (method_exists($this, 'check_no_urls')) {
                $url_result = $this->check_no_urls($combined_text);
                if ($url_result['found']) {
                    log_message('info', 'URLs detected in corruption report: ' . json_encode([
                        'urls' => $url_result['urls'],
                        'subject' => $complaint_subject
                    ]));

                    echo json_encode([
                        'success' => false,
                        'url_detected' => true,
                        'urls' => $url_result['urls'],
                        'message' => 'ไม่อนุญาตให้มี URL หรือลิงก์ในรายงาน',
                        'error_code' => 'URL_CONTENT',
                        'error_type' => 'url_content'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            // รับข้อมูลจากฟอร์ม
            log_message('info', 'Getting form data...');
            $form_data = $this->get_form_data_enhanced_safe();
            log_message('info', 'Form validation...');

            // Validate ข้อมูล
            $validation_result = $this->validate_corruption_report_enhanced_safe($form_data);
            if (!$validation_result['success']) {
                log_message('info', 'Validation failed: ' . $validation_result['message']);
                echo json_encode($validation_result, JSON_UNESCAPED_UNICODE);
                exit;
            }
            log_message('info', 'Validation passed');

            // เตรียมข้อมูลสำหรับบันทึก
            log_message('info', 'Preparing report data...');
            $report_data = $this->prepare_report_data_enhanced_safe($form_data, $current_user);

            // *** ⭐ เพิ่ม: เพิ่มข้อมูล reCAPTCHA ลงใน report_data ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                $report_data['recaptcha_verified'] = 1;
                $report_data['recaptcha_score'] = isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : null;
                $report_data['recaptcha_action'] = $recaptcha_action;
                $report_data['verification_method'] = 'recaptcha_v3';
                log_message('info', 'Added reCAPTCHA data to corruption report: score=' . $report_data['recaptcha_score']);
            } else {
                $report_data['recaptcha_verified'] = 0;
                $report_data['verification_method'] = $dev_mode ? 'dev_mode_skip' : 'none';
                log_message('info', 'No reCAPTCHA verification for corruption report (dev_mode or no token)');
            }

            // เริ่ม Transaction
            log_message('info', 'Starting database transaction...');
            $this->db->trans_start();

            // สร้าง corruption_report_id
            $report_data['corruption_report_id'] = $this->generate_corruption_report_id_safe();
            log_message('info', 'Generated report ID: ' . $report_data['corruption_report_id']);

            // บันทึกรายงาน
            log_message('info', 'Saving corruption report...');
            $corruption_id = $this->corruption_model->add_corruption_report($report_data);

            if (!$corruption_id) {
                throw new Exception('Failed to save corruption report - no ID returned');
            }

            log_message('info', 'Corruption Report saved with ID: ' . $corruption_id);

            // จัดการไฟล์หลักฐาน (ถ้ามี)
            $file_result = ['success' => true, 'count' => 0, 'message' => 'No files uploaded'];
            if (!empty($_FILES['evidence_files']['name'][0])) {
                log_message('info', 'Processing evidence files...');
                $file_result = $this->handle_evidence_files_safe($corruption_id);
                log_message('info', 'File upload result: ' . json_encode($file_result));

                // อัปเดตจำนวนไฟล์ในรายงาน
                if ($file_result['success'] && $file_result['count'] > 0) {
                    $this->db->where('corruption_id', $corruption_id);
                    $this->db->update('tbl_corruption_reports', [
                        'evidence_file_count' => $file_result['count'],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $report_data['created_by']
                    ]);
                    log_message('info', 'Updated file count: ' . $file_result['count']);
                }
            }

            // ดึงข้อมูลรายงานที่สร้างแล้ว
            log_message('info', 'Retrieving saved report...');
            $saved_report = $this->corruption_model->get_corruption_report_by_id($corruption_id);

            if (!$saved_report) {
                throw new Exception('Failed to retrieve saved report');
            }

            // Commit transaction
            log_message('info', 'Committing transaction...');
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction commit failed');
            }

            log_message('info', 'Transaction committed successfully');

            // บันทึกประวัติการสร้างรายงาน
            try {
                $this->db->insert('tbl_corruption_history', [
                    'corruption_id' => $corruption_id,
                    'action_type' => 'created',
                    'action_description' => 'สร้างรายงานการทุจริตใหม่: ' . $report_data['complaint_subject'],
                    'action_by' => $report_data['created_by'],
                    'action_by_user_id' => $current_user['is_logged_in'] ? $current_user['user_info']['id'] : null,
                    'action_date' => date('Y-m-d H:i:s'),
                    'ip_address' => $this->input->ip_address(),
                    'is_system_action' => 0
                ]);
                log_message('info', 'History record created');
            } catch (Exception $e) {
                log_message('warning', 'Failed to create history: ' . $e->getMessage());
            }

            // *** สร้างการแจ้งเตือนสำหรับ Staff ที่มีสิทธิ์เท่านั้น ***
            try {
                $this->create_corruption_notifications_with_permission($corruption_id, $saved_report, $current_user);
            } catch (Exception $e) {
                log_message('warning', 'Failed to create notifications: ' . $e->getMessage());
            }

            // บันทึกการติดตาม
            try {
                $this->db->insert('tbl_corruption_tracking', [
                    'corruption_id' => $corruption_id,
                    'tracking_action' => 'viewed',
                    'tracking_details' => json_encode(['action' => 'report_submitted']),
                    'user_id' => $current_user['is_logged_in'] ? $current_user['user_info']['id'] : null,
                    'user_type' => $current_user['user_type'],
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent(),
                    'tracked_at' => date('Y-m-d H:i:s')
                ]);
            } catch (Exception $e) {
                log_message('warning', 'Failed to log tracking: ' . $e->getMessage());
            }

            // Success response
            $response = [
                'success' => true,
                'message' => 'ส่งรายงานการทุจริตสำเร็จ',
                'report_id' => $saved_report->corruption_report_id,
                'corruption_id' => $corruption_id,
                'files_uploaded' => $file_result['count'],
                'file_upload_success' => $file_result['success'],
                'file_upload_message' => $file_result['message'],
                'user_type' => $current_user['user_type'],
                'is_logged_in' => $current_user['is_logged_in'],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // *** ⭐ เพิ่ม: เพิ่มข้อมูล reCAPTCHA ใน response ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                $response['recaptcha_verified'] = true;
                $response['recaptcha_score'] = isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : null;
                $response['verification_method'] = 'recaptcha_v3';
            } else {
                $response['recaptcha_verified'] = false;
                $response['verification_method'] = $dev_mode ? 'dev_mode_skip' : 'none';
            }

            // เพิ่มข้อมูลไฟล์ถ้ามี
            if (!empty($file_result['files'])) {
                $response['uploaded_files'] = array_map(function ($file) {
                    return [
                        'file_id' => $file['file_id'],
                        'original_name' => $file['file_original_name'],
                        'file_size' => $file['file_size'],
                        'file_type' => $file['file_extension']
                    ];
                }, $file_result['files']);
            }

            // เพิ่มข้อผิดพลาดของไฟล์ถ้ามี
            if (!empty($file_result['errors'])) {
                $response['file_errors'] = $file_result['errors'];
            }

            log_message('info', 'Success response prepared with reCAPTCHA info');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

            log_message('info', '=== CORRUPTION REPORT SUBMIT SUCCESS (WITH RECAPTCHA) ===');

        } catch (Exception $e) {
            // Rollback on error
            if (isset($this->db) && $this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
                log_message('info', 'Transaction rolled back');
            }

            log_message('error', 'Corruption Report Submit Error: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // ส่ง error response เป็น JSON เสมอ
            $error_response = [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'SUBMIT_ERROR',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // เพิ่มข้อมูล debug สำหรับ development
            if (ENVIRONMENT === 'development') {
                $error_response['error_details'] = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString())
                ];
            }

            echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
        } catch (Error $e) {
            // จัดการ PHP Fatal Error
            log_message('error', 'PHP Fatal Error: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            echo json_encode([
                'success' => false,
                'message' => 'Internal server error occurred',
                'error_code' => 'FATAL_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }





    // ใน Corruption Controller - method admin_management()
    private function check_corruption_management_permission($staff_data)
    {
        if (!$staff_data)
            return false;

        log_message('info', 'STRICT: Checking corruption permission for staff ID: ' . $staff_data->m_id);
        log_message('info', 'STRICT: Staff system: ' . $staff_data->m_system);
        log_message('info', 'STRICT: Grant user ref ID: ' . $staff_data->grant_user_ref_id);

        // system_admin และ super_admin สามารถเข้าถึงได้เสมอ
        if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
            log_message('info', 'STRICT: Permission GRANTED - User is system_admin or super_admin');
            return true;
        }

        // user_admin ต้องมี 107 ใน grant_user_ref_id เท่านั้น
        if ($staff_data->m_system === 'user_admin') {
            if (empty($staff_data->grant_user_ref_id)) {
                log_message('info', 'STRICT: Permission DENIED - user_admin has empty grant_user_ref_id');
                return false;
            }

            try {
                // แปลง grant_user_ref_id เป็น array
                $grant_ids = explode(',', $staff_data->grant_user_ref_id);
                $grant_ids = array_map('trim', $grant_ids);

                log_message('info', 'STRICT: Grant IDs: ' . json_encode($grant_ids));

                // 🔒 เช็คเฉพาะว่ามี "107" ใน array หรือไม่
                if (in_array('107', $grant_ids)) {
                    log_message('info', 'STRICT: Permission GRANTED - Found 107 in grant_user_ref_id array');
                    return true;
                }

                // 🔒 เช็คในฐานข้อมูลเฉพาะ grant_user_id ที่ user มีเท่านั้น
                if ($this->db->table_exists('tbl_grant_user')) {
                    foreach ($grant_ids as $grant_id) {
                        if (empty($grant_id) || !is_numeric($grant_id))
                            continue;

                        $this->db->select('grant_user_id, grant_user_name');
                        $this->db->from('tbl_grant_user');
                        $this->db->where('grant_user_id', intval($grant_id));
                        $grant_data = $this->db->get()->row();

                        if ($grant_data) {
                            log_message('info', "STRICT: Checking grant_user_id {$grant_id}: grant_user_name = {$grant_data->grant_user_name}");

                            // 🔒 เช็คเฉพาะ grant_user_id = 107
                            if ($grant_data->grant_user_id == 107) {
                                log_message('info', 'STRICT: Permission GRANTED - User has grant_user_id = 107');
                                return true;
                            }

                            // 🔒 หรือเช็คจากชื่อที่มีคำว่า "ทุจริต" (เผื่อมีการเปลี่ยนแปลง)
                            $name_lower = mb_strtolower($grant_data->grant_user_name, 'UTF-8');
                            if (strpos($name_lower, 'ทุจริต') !== false) {
                                log_message('info', 'STRICT: Permission GRANTED - User has corruption-related grant');
                                return true;
                            }
                        }
                    }
                }

                log_message('info', 'STRICT: Permission DENIED - User does not have 107 or corruption-related grants');
                return false;

            } catch (Exception $e) {
                log_message('error', 'STRICT: Error checking grant permission: ' . $e->getMessage());

                // 🔒 Fallback แบบเข้มงวด: เช็คเฉพาะ "107" ใน string
                $has_107 = (strpos($staff_data->grant_user_ref_id, '107') !== false);

                log_message('info', "STRICT: Fallback check - grant_user_ref_id contains '107': " . ($has_107 ? 'GRANTED' : 'DENIED'));
                return $has_107;
            }
        }

        log_message('info', 'STRICT: Permission DENIED - User system not authorized: ' . $staff_data->m_system);
        return false;
    }


    private function get_corruption_stats_safe()
    {
        try {
            // ตรวจสอบว่า Model โหลดแล้วหรือไม่
            if (!isset($this->corruption_model)) {
                $this->load->model('Corruption_model', 'corruption_model');
            }

            // ตรวจสอบว่า table มีอยู่จริงหรือไม่
            $table_status = $this->corruption_model->check_required_tables();

            if (!$table_status['tbl_corruption_reports']) {
                log_message('warning', 'tbl_corruption_reports table does not exist');
                return $this->get_default_stats();
            }

            // ใช้ method ใหม่ใน Model
            if (method_exists($this->corruption_model, 'get_comprehensive_statistics')) {
                $stats = $this->corruption_model->get_comprehensive_statistics();
                log_message('info', 'Corruption stats loaded using comprehensive method');
                return $stats;
            }

            // Fallback: ใช้ methods แยก
            $stats = [
                'total_reports' => $this->corruption_model->count_total_reports(),
                'resolved_reports' => $this->corruption_model->count_resolved_reports(),
                'this_month_reports' => $this->corruption_model->count_this_month_reports(),
                'this_week_reports' => method_exists($this->corruption_model, 'count_this_week_reports') ?
                    $this->corruption_model->count_this_week_reports() : 0,
                'today_reports' => method_exists($this->corruption_model, 'count_today_reports') ?
                    $this->corruption_model->count_today_reports() : 0
            ];

            log_message('info', 'Corruption stats loaded using individual methods: ' . json_encode($stats));

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error loading corruption stats: ' . $e->getMessage());

            // Return default stats on error
            return $this->get_default_stats();
        }
    }





    public function check_session()
    {
        $response = array(
            'logged_in' => $this->session->userdata('logged_in') ? true : false,
            'user_id' => $this->session->userdata('user_id'),
            'timestamp' => time()
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }





    private function create_corruption_notifications_with_permission($corruption_id, $report, $current_user)
    {
        try {
            log_message('info', 'Creating corruption notifications for authorized staff and public user');

            $total_notifications_created = 0;

            // *** 1. สร้างการแจ้งเตือนสำหรับ Public User (ผู้แจ้ง) ***
            if (
                $current_user['user_type'] === 'public' &&
                $current_user['is_logged_in'] &&
                !empty($current_user['user_info']['id']) &&
                !$report->is_anonymous
            ) {

                try {
                    $public_notification_data = [
                        'type' => 'corruption_report_confirmation',
                        'title' => 'ยืนยันการรับรายงานการทุจริต',
                        'message' => $this->prepare_public_user_notification_message($report),
                        'reference_id' => $corruption_id,
                        'reference_table' => 'tbl_corruption_reports',
                        'target_user_id' => $current_user['user_info']['id'],
                        'target_role' => 'public',
                        'priority' => 'normal',
                        'icon' => 'fas fa-check-circle',
                        'url' => site_url('Corruption/track_status?report_id=' . $report->corruption_report_id),
                        'data' => json_encode([
                            'corruption_id' => $corruption_id,
                            'report_id' => $report->corruption_report_id,
                            'corruption_type' => $report->corruption_type,
                            'user_mp_id' => $current_user['user_info']['mp_id'],
                            'user_internal_id' => $current_user['user_info']['id'],
                            'notification_type' => 'confirmation'
                        ]),
                        'is_read' => 0,
                        'is_system' => 1,
                        'is_archived' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $current_user['user_info']['id']
                    ];

                    $this->db->insert('tbl_notifications', $public_notification_data);

                    if ($this->db->affected_rows() > 0) {
                        $total_notifications_created++;
                        log_message('info', "Notification created for public user: {$current_user['user_info']['name']}");
                    }

                } catch (Exception $e) {
                    log_message('error', "Failed to create notification for public user: " . $e->getMessage());
                }
            } else {
                log_message('info', 'Skipping public user notification - not eligible (guest, anonymous, or staff)');
            }

            // *** 2. สร้างการแจ้งเตือนสำหรับ Staff (แค่ 1 row) ***
            try {
                // สร้าง notification แบบ system-wide สำหรับ staff ทั้งหมด
                $staff_notification_title = 'รายงานการทุจริตใหม่';
                $staff_notification_message = $this->prepare_corruption_notification_message($report, $current_user);

                $staff_notification_data = [
                    'type' => 'new_corruption_report',
                    'title' => $staff_notification_title,
                    'message' => $staff_notification_message,
                    'reference_id' => $corruption_id,
                    'reference_table' => 'tbl_corruption_reports',
                    'target_user_id' => null, // null = ส่งให้ staff ทั้งหมดที่มีสิทธิ์
                    'target_role' => 'staff',
                    'priority' => $this->determine_corruption_notification_priority($report),
                    'icon' => 'fas fa-exclamation-triangle',
                    'url' => site_url('Corruption/report_detail/' . $report->corruption_report_id),
                    'data' => json_encode([
                        'corruption_id' => $corruption_id,
                        'report_id' => $report->corruption_report_id,
                        'corruption_type' => $report->corruption_type,
                        'is_anonymous' => $report->is_anonymous,
                        'created_at' => $report->created_at,
                        'notification_type' => 'new_report',
                        'required_permission' => 'corruption_management' // ระบุว่าต้องมีสิทธิ์นี้
                    ]),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => null
                ];

                $this->db->insert('tbl_notifications', $staff_notification_data);

                if ($this->db->affected_rows() > 0) {
                    $total_notifications_created++;
                    log_message('info', "System-wide notification created for authorized staff");
                }

            } catch (Exception $e) {
                log_message('error', "Failed to create system-wide staff notification: " . $e->getMessage());
            }

            log_message('info', "Successfully created {$total_notifications_created} total corruption notifications");

            return $total_notifications_created > 0;

        } catch (Exception $e) {
            log_message('error', 'Error creating corruption notifications: ' . $e->getMessage());
            throw $e;
        }
    }





    private function get_authorized_staff_for_corruption_notifications()
    {
        try {
            // ตรวจสอบว่า table มีอยู่จริง
            if (!$this->db->table_exists('tbl_member')) {
                log_message('error', 'Table tbl_member does not exist');
                return [];
            }

            $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, m.m_system, m.grant_user_ref_id');
            $this->db->from('tbl_member m');
            $this->db->where('m.m_status', '1'); // เฉพาะ active staff

            // สร้างเงื่อนไข: system_admin, super_admin หรือ user_admin ที่มี grant_user_ref_id = 107
            $this->db->group_start();
            // system_admin และ super_admin ทุกคน
            $this->db->where_in('m.m_system', ['system_admin', 'super_admin']);

            // หรือ user_admin ที่มี grant_user_ref_id ตรงกับ grant_user_name = 107
            $this->db->or_group_start();
            $this->db->where('m.m_system', 'user_admin');

            // ตรวจสอบว่ามี table tbl_grant_user หรือไม่
            if ($this->db->table_exists('tbl_grant_user')) {
                // JOIN กับ tbl_grant_user เพื่อตรวจสอบ grant_user_name = 107
                $this->db->join('tbl_grant_user gu', 'm.grant_user_ref_id = gu.grant_user_id', 'inner');
                $this->db->where('gu.grant_user_name', '107');
            } else {
                // ถ้าไม่มี table tbl_grant_user ให้ใช้เงื่อนไขง่ายๆ
                // ตรวจสอบว่า grant_user_ref_id = 107 โดยตรง
                $this->db->where('m.grant_user_ref_id', 107);
            }
            $this->db->group_end();
            $this->db->group_end();

            $this->db->order_by('m.m_system', 'ASC');
            $this->db->order_by('m.m_fname', 'ASC');

            $query = $this->db->get();

            if (!$query) {
                log_message('error', 'Failed to query authorized staff: ' . $this->db->last_query());
                return [];
            }

            $authorized_staff = $query->result();

            // Log รายละเอียดของ staff ที่ได้รับสิทธิ์
            foreach ($authorized_staff as $staff) {
                $grant_info = '';
                if ($staff->m_system === 'user_admin') {
                    $grant_info = " (Grant ID: {$staff->grant_user_ref_id})";
                }

                log_message('info', "Authorized staff: {$staff->m_fname} {$staff->m_lname} - System: {$staff->m_system}{$grant_info}");
            }

            return $authorized_staff;

        } catch (Exception $e) {
            log_message('error', 'Error getting authorized staff: ' . $e->getMessage());
            return [];
        }
    }






    private function prepare_public_user_notification_message($report)
    {
        try {
            $corruption_types = [
                'embezzlement' => 'การยักยอกเงิน',
                'bribery' => 'การรับสินบน',
                'abuse_of_power' => 'การใช้อำนาจหน้าที่มิชอบ',
                'conflict_of_interest' => 'ความขัดแย้งทางผลประโยชน์',
                'procurement_fraud' => 'การทุจริตในการจัดซื้อจัดจ้าง',
                'other' => 'อื่นๆ'
            ];

            $type_label = $corruption_types[$report->corruption_type] ?? $report->corruption_type;

            $message = "เรียน คุณ " . $report->reporter_name . "\n\n";
            $message .= "ระบบได้รับรายงานการทุจริตของท่านเรียบร้อยแล้ว\n\n";
            $message .= "รายละเอียดรายงาน:\n";
            $message .= "หมายเลขรายงาน: {$report->corruption_report_id}\n";
            $message .= "ประเภท: {$type_label}\n";
            $message .= "หัวข้อ: " . mb_substr($report->complaint_subject, 0, 100);

            if (mb_strlen($report->complaint_subject) > 100) {
                $message .= "...";
            }

            $message .= "\nวันที่แจ้ง: " . date('d/m/Y H:i', strtotime($report->created_at));
            $message .= "\nสถานะ: รอดำเนินการ";
            $message .= "\n\nท่านสามารถติดตามสถานะรายงานได้ด้วยหมายเลขรายงาน: {$report->corruption_report_id}";
            $message .= "\n\nขอบคุณที่ให้ความร่วมมือในการต่อต้านการทุจริต";

            return $message;

        } catch (Exception $e) {
            log_message('error', 'Error preparing public user notification message: ' . $e->getMessage());
            return "ระบบได้รับรายงานการทุจริตของท่านเรียบร้อยแล้ว - หมายเลขรายงาน: {$report->corruption_report_id}";
        }
    }




    private function prepare_corruption_notification_message($report, $current_user)
    {
        try {
            $corruption_types = [
                'embezzlement' => 'การยักยอกเงิน',
                'bribery' => 'การรับสินบน',
                'abuse_of_power' => 'การใช้อำนาจหน้าที่มิชอบ',
                'conflict_of_interest' => 'ความขัดแย้งทางผลประโยชน์',
                'procurement_fraud' => 'การทุจริตในการจัดซื้อจัดจ้าง',
                'other' => 'อื่นๆ'
            ];

            $type_label = $corruption_types[$report->corruption_type] ?? $report->corruption_type;

            $message = "มีการยื่นรายงานการทุจริตใหม่\n";
            $message .= "หมายเลขรายงาน: {$report->corruption_report_id}\n";
            $message .= "ประเภท: {$type_label}\n";
            $message .= "หัวข้อ: " . mb_substr($report->complaint_subject, 0, 100);

            if (mb_strlen($report->complaint_subject) > 100) {
                $message .= "...";
            }

            $message .= "\n";

            if ($report->is_anonymous) {
                $message .= "ผู้แจ้ง: ไม่ระบุตัวตน";
            } else {
                $reporter_name = $report->reporter_name ?? 'ไม่ระบุชื่อ';
                $message .= "ผู้แจ้ง: {$reporter_name}";
            }

            $message .= "\nวันที่แจ้ง: " . date('d/m/Y H:i', strtotime($report->created_at));

            return $message;

        } catch (Exception $e) {
            log_message('error', 'Error preparing notification message: ' . $e->getMessage());
            return "มีการยื่นรายงานการทุจริตใหม่ - หมายเลข: {$report->corruption_report_id}";
        }
    }

    /**
     * กำหนดระดับความสำคัญของการแจ้งเตือน
     */
    private function determine_corruption_notification_priority($report)
    {
        try {
            // กำหนดระดับความสำคัญตามประเภทการทุจริต
            $high_priority_types = ['embezzlement', 'bribery', 'procurement_fraud'];

            if (in_array($report->corruption_type, $high_priority_types)) {
                return 'high';
            }

            // ถ้ามีการระบุผู้กระทำผิดที่เป็นตำแหน่งสำคัญ
            if (!empty($report->perpetrator_position)) {
                $important_positions = ['ผู้อำนวยการ', 'หัวหน้า', 'ผู้จัดการ', 'นาย', 'ผู้ว่า'];
                foreach ($important_positions as $position) {
                    if (stripos($report->perpetrator_position, $position) !== false) {
                        return 'high';
                    }
                }
            }

            return 'normal';

        } catch (Exception $e) {
            log_message('error', 'Error determining notification priority: ' . $e->getMessage());
            return 'normal';
        }
    }



    public function download_file($file_id = null)
    {
        try {
            if (empty($file_id)) {
                show_404();
                return;
            }

            // ดึงข้อมูลไฟล์จากฐานข้อมูล
            $this->db->select('*');
            $this->db->from('tbl_corruption_files');
            $this->db->where('file_id', $file_id);
            $this->db->where('file_status', 'active');
            $file = $this->db->get()->row();

            if (!$file) {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file->file_path)) {
                log_message('error', "File not found: {$file->file_path}");
                show_404();
                return;
            }

            // อัปเดตจำนวนการดาวน์โหลด
            $this->db->where('file_id', $file_id);
            $this->db->update('tbl_corruption_files', [
                'download_count' => $file->download_count + 1,
                'last_downloaded' => date('Y-m-d H:i:s')
            ]);

            // ส่งไฟล์
            $this->load->helper('download');
            force_download($file->file_original_name, file_get_contents($file->file_path));

        } catch (Exception $e) {
            log_message('error', 'Error downloading file: ' . $e->getMessage());
            show_404();
        }
    }







    // เพิ่ม method สำหรับสร้าง corruption_report_id
    private function generate_corruption_report_id_safe()
    {
        try {
            $prefix = 'COR';

            // ใช้ปี พ.ศ. 2 ตัวท้าย
            $buddhist_year = date('Y') + 543; // แปลงเป็นปี พ.ศ.
            $year_suffix = substr($buddhist_year, -2); // เอา 2 ตัวท้าย เช่น 68 จาก 2568

            // สร้างเลข random 5 หนัก
            $random_number = '';
            for ($i = 0; $i < 5; $i++) {
                $random_number .= mt_rand(0, 9);
            }

            // ตรวจสอบว่าหมายเลขนี้ซ้ำหรือไม่ (วนลูปจนกว่าจะได้เลขที่ไม่ซ้ำ)
            $max_attempts = 100; // จำกัดการพยายาม
            $attempts = 0;

            do {
                $report_id = $prefix . $year_suffix . $random_number;

                // ตรวจสอบในฐานข้อมูล
                $this->db->select('corruption_report_id');
                $this->db->from('tbl_corruption_reports');
                $this->db->where('corruption_report_id', $report_id);
                $existing = $this->db->get()->row();

                if (!$existing) {
                    // ไม่ซ้ำ ใช้ได้
                    break;
                }

                // ซ้ำ สร้างใหม่
                $random_number = '';
                for ($i = 0; $i < 5; $i++) {
                    $random_number .= mt_rand(0, 9);
                }

                $attempts++;

            } while ($attempts < $max_attempts);

            if ($attempts >= $max_attempts) {
                // หากลองแล้ว 100 ครั้งยังซ้ำ ให้ใช้ timestamp แทน
                $random_number = substr(time(), -5);
                $report_id = $prefix . $year_suffix . $random_number;
            }

            log_message('info', 'Generated corruption report ID: ' . $report_id . ' (attempts: ' . $attempts . ')');

            return $report_id;

        } catch (Exception $e) {
            log_message('error', 'Error generating corruption report ID: ' . $e->getMessage());

            // fallback - ใช้ timestamp
            $buddhist_year = date('Y') + 543;
            $year_suffix = substr($buddhist_year, -2);
            $fallback_number = substr(time(), -5);

            return 'COR' . $year_suffix . $fallback_number;
        }
    }




    public function preview_report_id()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // สร้างตัวอย่างหมายเลข
            $examples = [];

            for ($i = 0; $i < 10; $i++) {
                $examples[] = $this->generate_corruption_report_id_safe();
            }

            $buddhist_year = date('Y') + 543;
            $current_thai_year = $buddhist_year;
            $year_suffix = substr($buddhist_year, -2);

            echo json_encode([
                'success' => true,
                'format' => 'COR + ปี พ.ศ. 2 ตัวท้าย + เลข random 5 หลัก',
                'current_year' => [
                    'gregorian' => date('Y'),
                    'buddhist' => $current_thai_year,
                    'suffix' => $year_suffix
                ],
                'examples' => $examples,
                'explanation' => [
                    'prefix' => 'COR (ตัวย่อของ Corruption)',
                    'year' => 'ปี พ.ศ. 2 ตัวท้าย (เช่น 68 จาก 2568)',
                    'random' => 'เลข random 5 หลัก (00000-99999)',
                    'sample' => 'COR6812345'
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }





    private function handle_evidence_files_safe($corruption_id)
    {
        try {
            log_message('info', 'Starting file upload process for corruption ID: ' . $corruption_id);

            // สร้างโฟลเดอร์สำหรับเก็บไฟล์หลักฐานการทุจริต
            $upload_path = './docs/corruption_evidence/';
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    throw new Exception('Cannot create upload directory: ' . $upload_path);
                }
                log_message('info', 'Created upload directory: ' . $upload_path);
            }

            // ตรวจสอบว่ามีไฟล์หรือไม่
            if (empty($_FILES['evidence_files']['name'][0])) {
                log_message('info', 'No files to upload');
                return ['success' => true, 'count' => 0, 'message' => 'No files to upload'];
            }

            $file_count = count($_FILES['evidence_files']['name']);
            $uploaded_files = [];
            $errors = [];
            $max_files = 10;
            $max_file_size = 10 * 1024 * 1024; // 10MB
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];

            log_message('info', "Processing {$file_count} files");

            // ตรวจสอบจำนวนไฟล์
            if ($file_count > $max_files) {
                return [
                    'success' => false,
                    'count' => 0,
                    'message' => "จำนวนไฟล์เกินกำหนด (สูงสุด {$max_files} ไฟล์)"
                ];
            }

            for ($i = 0; $i < $file_count; $i++) {
                // ข้ามไฟล์ว่าง
                if (empty($_FILES['evidence_files']['name'][$i])) {
                    log_message('info', "Skipping empty file at index {$i}");
                    continue;
                }

                $file_name = $_FILES['evidence_files']['name'][$i];
                $file_tmp = $_FILES['evidence_files']['tmp_name'][$i];
                $file_size = $_FILES['evidence_files']['size'][$i];
                $file_type = $_FILES['evidence_files']['type'][$i];
                $file_error = $_FILES['evidence_files']['error'][$i];

                log_message('info', "Processing file {$i}: {$file_name} ({$file_size} bytes)");

                // ตรวจสอบ error
                if ($file_error !== UPLOAD_ERR_OK) {
                    $error_msg = $this->get_upload_error_message($file_error);
                    log_message('error', "Upload error for file {$file_name}: {$error_msg}");
                    $errors[] = "ไฟล์ {$file_name}: {$error_msg}";
                    continue;
                }

                // ตรวจสอบว่าไฟล์อัปโหลดมาจริง
                if (!is_uploaded_file($file_tmp)) {
                    log_message('error', "Security check failed for file: {$file_name}");
                    $errors[] = "ไฟล์ {$file_name}: ตรวจสอบความปลอดภัยไม่ผ่าน";
                    continue;
                }

                // ตรวจสอบขนาดไฟล์
                if ($file_size > $max_file_size) {
                    log_message('error', "File too large: {$file_name} ({$file_size} bytes)");
                    $errors[] = "ไฟล์ {$file_name}: ขนาดใหญ่เกินไป (เกิน 10MB)";
                    continue;
                }

                // ตรวจสอบว่าไฟล์ไม่เป็น 0 bytes
                if ($file_size == 0) {
                    log_message('error', "Empty file: {$file_name}");
                    $errors[] = "ไฟล์ {$file_name}: ไฟล์ว่างเปล่า";
                    continue;
                }

                // ตรวจสอบนามสกุลไฟล์
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_types)) {
                    log_message('error', "Invalid file type: {$file_name} (extension: {$file_extension})");
                    $errors[] = "ไฟล์ {$file_name}: ประเภทไฟล์ไม่รองรับ (.{$file_extension})";
                    continue;
                }

                // ตรวจสอบ MIME type เพิ่มเติม
                if (!$this->validate_file_mime_type($file_tmp, $file_extension)) {
                    log_message('error', "Invalid MIME type for file: {$file_name}");
                    $errors[] = "ไฟล์ {$file_name}: ประเภทไฟล์ไม่ถูกต้อง";
                    continue;
                }

                // สร้างชื่อไฟล์ใหม่
                $timestamp = time();
                $random = mt_rand(1000, 9999);
                //$safe_original_name = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $file_name);
                //$new_filename = "COR_{$corruption_id}_{$timestamp}_{$random}_{$safe_original_name}";
                $new_filename = $this->generate_safe_filename($corruption_id, $file_name, $file_extension, $timestamp, $random);



                // ตรวจสอบความยาวชื่อไฟล์
                if (strlen($new_filename) > 255) {
                    $new_filename = "COR_{$corruption_id}_{$timestamp}_{$random}.{$file_extension}";
                }

                $target_path = $upload_path . $new_filename;

                // ตรวจสอบว่าไฟล์ซ้ำหรือไม่
                if (file_exists($target_path)) {
                    $new_filename = "COR_{$corruption_id}_{$timestamp}_{$random}_" . uniqid() . ".{$file_extension}";
                    $target_path = $upload_path . $new_filename;
                }

                // อัปโหลดไฟล์
                if (move_uploaded_file($file_tmp, $target_path)) {
                    // เซ็ตสิทธิ์การเข้าถึงไฟล์
                    chmod($target_path, 0644);

                    // เตรียมข้อมูลไฟล์สำหรับบันทึกในฐานข้อมูล
                    $file_data = [
                        'corruption_id' => $corruption_id,
                        'file_name' => $new_filename,
                        'file_original_name' => $file_name,
                        'file_path' => $target_path,
                        'file_size' => $file_size,
                        'file_type' => $file_type,
                        'file_extension' => $file_extension,
                        'file_description' => null,
                        'file_order' => count($uploaded_files) + 1,
                        'is_main_evidence' => (count($uploaded_files) === 0) ? 1 : 0,
                        'file_status' => 'active',
                        'uploaded_by' => 'System',
                        'uploaded_at' => date('Y-m-d H:i:s'),
                        'download_count' => 0
                    ];

                    // บันทึกข้อมูลไฟล์ลงฐานข้อมูล
                    try {
                        $this->db->insert('tbl_corruption_files', $file_data);
                        $file_id = $this->db->insert_id();

                        if ($file_id) {
                            $uploaded_files[] = array_merge($file_data, ['file_id' => $file_id]);
                            log_message('info', "File uploaded successfully: {$file_name} -> {$new_filename} (ID: {$file_id})");
                        } else {
                            log_message('error', "Failed to save file data to database: {$file_name}");
                            // ลบไฟล์ที่อัปโหลดแล้ว
                            if (file_exists($target_path)) {
                                unlink($target_path);
                            }
                            $errors[] = "ไฟล์ {$file_name}: ไม่สามารถบันทึกข้อมูลในฐานข้อมูล";
                        }
                    } catch (Exception $db_error) {
                        log_message('error', "Database error while saving file: {$db_error->getMessage()}");
                        // ลบไฟล์ที่อัปโหลดแล้ว
                        if (file_exists($target_path)) {
                            unlink($target_path);
                        }
                        $errors[] = "ไฟล์ {$file_name}: เกิดข้อผิดพลาดในฐานข้อมูล";
                    }

                } else {
                    log_message('error', "Failed to move uploaded file: {$file_name}");
                    $errors[] = "ไฟล์ {$file_name}: ไม่สามารถย้ายไฟล์ได้";
                }
            }

            $upload_count = count($uploaded_files);

            // สร้างข้อความผลลัพธ์
            $message = '';
            if ($upload_count > 0) {
                $message = "อัปโหลดไฟล์สำเร็จ {$upload_count} ไฟล์";
                if (!empty($errors)) {
                    $message .= " (มีข้อผิดพลาด " . count($errors) . " ไฟล์)";
                }
            } else {
                $message = 'ไม่สามารถอัปโหลดไฟล์ได้';
                if (!empty($errors)) {
                    $message .= ': ' . implode(', ', $errors);
                }
            }

            log_message('info', "File upload completed: {$upload_count} files uploaded, " . count($errors) . " errors");

            return [
                'success' => $upload_count > 0,
                'count' => $upload_count,
                'message' => $message,
                'files' => $uploaded_files,
                'errors' => $errors
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in handle_evidence_files_safe: ' . $e->getMessage());
            return [
                'success' => false,
                'count' => 0,
                'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์: ' . $e->getMessage(),
                'files' => [],
                'errors' => [$e->getMessage()]
            ];
        }
    }




    private function generate_safe_filename($corruption_id, $original_name, $extension, $timestamp = null, $random = null)
    {
        try {
            // ใช้ timestamp และ random ปัจจุบันถ้าไม่ได้ส่งมา
            if ($timestamp === null)
                $timestamp = time();
            if ($random === null)
                $random = mt_rand(1000, 9999);

            // ลบนามสกุลออกจากชื่อเดิม
            $name_without_ext = pathinfo($original_name, PATHINFO_FILENAME);

            // ทำความสะอาดชื่อไฟล์อย่างระมัดระวัง
            $clean_name = $this->sanitize_filename($name_without_ext);

            // จำกัดความยาวของชื่อไฟล์
            $max_name_length = 50; // จำกัดชื่อไฟล์ไม่เกิน 50 ตัวอักษร
            if (mb_strlen($clean_name, 'UTF-8') > $max_name_length) {
                $clean_name = mb_substr($clean_name, 0, $max_name_length, 'UTF-8');
                // ตัดที่คำสุดท้ายถ้าตัดกลางคำ
                $clean_name = preg_replace('/[^\s]*$/', '', $clean_name);
                $clean_name = trim($clean_name);
            }

            // ถ้าชื่อไฟล์สั้นเกินไปหรือว่างเปล่า ให้ใช้ชื่อเริ่มต้น
            if (mb_strlen($clean_name, 'UTF-8') < 3) {
                $clean_name = 'evidence_file';
            }

            // สร้างชื่อไฟล์ใหม่
            $new_filename = "COR{$corruption_id}_{$timestamp}_{$random}_{$clean_name}.{$extension}";

            // ตรวจสอบความยาวชื่อไฟล์รวม
            if (strlen($new_filename) > 200) {
                // ถ้ายาวเกินไป ให้ใช้รูปแบบสั้น
                $short_name = mb_substr($clean_name, 0, 20, 'UTF-8');
                $new_filename = "COR{$corruption_id}_{$timestamp}_{$random}_{$short_name}.{$extension}";
            }

            // ตรวจสอบอีกครั้งว่ายังยาวเกินไปหรือไม่
            if (strlen($new_filename) > 255) {
                // ใช้รูปแบบสั้นที่สุด
                $new_filename = "COR{$corruption_id}_{$timestamp}_{$random}.{$extension}";
            }

            log_message('info', "Generated filename: {$original_name} -> {$new_filename}");
            return $new_filename;

        } catch (Exception $e) {
            log_message('error', 'Error generating filename: ' . $e->getMessage());
            // fallback ชื่อไฟล์
            return "COR{$corruption_id}_" . time() . "_" . mt_rand(1000, 9999) . ".{$extension}";
        }
    }

    /**
     * ทำความสะอาดชื่อไฟล์อย่างระมัดระวัง
     */
    private function sanitize_filename($filename)
    {
        try {
            // แปลงเป็น UTF-8 ถ้าจำเป็น
            if (!mb_check_encoding($filename, 'UTF-8')) {
                $filename = mb_convert_encoding($filename, 'UTF-8', 'auto');
            }

            // เก็บอักขระภาษาไทย อังกฤษ ตัวเลข และเครื่องหมายพื้นฐาน
            $filename = preg_replace('/[^\p{L}\p{N}\s\-_\.\(\)\[\]]/u', '', $filename);

            // แทนที่ช่องว่างหลายตัวด้วยช่องว่างเดียว
            $filename = preg_replace('/\s+/', ' ', $filename);

            // แทนที่ช่องว่างด้วย underscore
            $filename = str_replace(' ', '_', $filename);

            // ลบ underscore ซ้ำๆ
            $filename = preg_replace('/_+/', '_', $filename);

            // ลบ underscore ที่จุดเริ่มต้นและจุดสิ้นสุด
            $filename = trim($filename, '_');

            // ถ้าว่างเปล่าหลังจากทำความสะอาด
            if (empty($filename)) {
                $filename = 'file';
            }

            return $filename;

        } catch (Exception $e) {
            log_message('error', 'Error sanitizing filename: ' . $e->getMessage());
            return 'file';
        }
    }



    private function validate_file_mime_type($file_path, $extension)
    {
        try {
            // รายการ MIME types ที่ยอมรับ
            $allowed_mimes = [
                'jpg' => ['image/jpeg', 'image/jpg'],
                'jpeg' => ['image/jpeg', 'image/jpg'],
                'png' => ['image/png'],
                'gif' => ['image/gif'],
                'pdf' => ['application/pdf'],
                'doc' => ['application/msword'],
                'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'xls' => ['application/vnd.ms-excel'],
                'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            ];

            if (!isset($allowed_mimes[$extension])) {
                return false;
            }

            // ตรวจสอบ MIME type
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detected_mime = finfo_file($finfo, $file_path);
                finfo_close($finfo);

                if ($detected_mime && in_array($detected_mime, $allowed_mimes[$extension])) {
                    return true;
                }
            }

            // Fallback: ตรวจสอบ magic bytes สำหรับไฟล์ประเภทสำคัญ
            $file_content = file_get_contents($file_path, false, null, 0, 16);

            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    return substr($file_content, 0, 3) === "\xFF\xD8\xFF";
                case 'png':
                    return substr($file_content, 0, 8) === "\x89PNG\r\n\x1a\n";
                case 'gif':
                    return substr($file_content, 0, 6) === "GIF87a" || substr($file_content, 0, 6) === "GIF89a";
                case 'pdf':
                    return substr($file_content, 0, 5) === "%PDF-";
                default:
                    // สำหรับไฟล์ Office อื่นๆ ให้ผ่าน
                    return true;
            }

        } catch (Exception $e) {
            log_message('error', 'Error validating MIME type: ' . $e->getMessage());
            return true; // ในกรณีเกิดข้อผิดพลาด ให้ผ่าน
        }
    }






    private function get_upload_error_message($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_OK:
                return 'อัปโหลดสำเร็จ';
            case UPLOAD_ERR_INI_SIZE:
                return 'ไฟล์ใหญ่เกินกำหนดของเซิร์ฟเวอร์';
            case UPLOAD_ERR_FORM_SIZE:
                return 'ไฟล์ใหญ่เกินกำหนดของฟอร์ม';
            case UPLOAD_ERR_PARTIAL:
                return 'อัปโหลดไฟล์ไม่สมบูรณ์';
            case UPLOAD_ERR_NO_FILE:
                return 'ไม่มีไฟล์ที่อัปโหลด';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'ไม่พบโฟลเดอร์ชั่วคราว';
            case UPLOAD_ERR_CANT_WRITE:
                return 'ไม่สามารถเขียนไฟล์ได้';
            case UPLOAD_ERR_EXTENSION:
                return 'การอัปโหลดถูกหยุดโดย extension';
            default:
                return 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ';
        }
    }





    private function create_corruption_notifications_safe($corruption_id, $report, $current_user)
    {
        try {
            log_message('info', 'Notification creation skipped for safety');
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error creating notifications: ' . $e->getMessage());
            return false;
        }
    }





    private function get_current_user_for_corruption_report()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];

        try {
            // *** รับข้อมูล Session ***
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');
            $m_id = $this->session->userdata('m_id');
            $logged_in = $this->session->userdata('logged_in');

            log_message('info', "Session Check - mp_id: {$mp_id}, mp_email: {$mp_email}, " .
                "m_id: {$m_id}, logged_in: {$logged_in}");

            // *** ตรวจสอบ Public User ก่อน ***
            if (!empty($mp_id) && !empty($mp_email)) {
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_status, mp_registered_date, mp_updated_date');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    log_message('info', "✅ Public user found: {$user_data->mp_fname} {$user_data->mp_lname} (Internal ID: {$user_data->id})");

                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_info'] = [
                        'id' => $user_data->id,
                        'mp_id' => $user_data->mp_id,
                        'name' => trim(
                            ($user_data->mp_prefix ? $user_data->mp_prefix . ' ' : '') .
                            $user_data->mp_fname . ' ' .
                            $user_data->mp_lname
                        ),
                        'phone' => $user_data->mp_phone,
                        'email' => $user_data->mp_email,
                        'position' => 'ประชาชน'
                    ];

                    return $user_info;
                }
            }

            // *** ตรวจสอบ Staff User ***
            if (!empty($m_id)) {
                $staff_data = $this->get_staff_data($m_id);

                if ($staff_data) {
                    log_message('info', "⚠️ Staff user found: {$staff_data->m_fname} {$staff_data->m_lname}");

                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_info'] = [
                        'id' => $staff_data->m_id,
                        'name' => trim($staff_data->m_fname . ' ' . $staff_data->m_lname),
                        'email' => $staff_data->m_email,
                        'phone' => $staff_data->m_phone,
                        'position' => $staff_data->pname ?? 'เจ้าหน้าที่',
                        'm_system' => $staff_data->m_system,
                        'grant_user_ref_id' => $staff_data->grant_user_ref_id
                    ];

                    return $user_info;
                }
            }

            log_message('info', "👤 No valid session found - treating as guest user");

        } catch (Exception $e) {
            log_message('error', 'Error in get_current_user_for_corruption_report: ' . $e->getMessage());
        }

        return $user_info;
    }






    // เพิ่มฟังก์ชันช่วยเหลือสำหรับการตรวจสอบ property ที่ปลอดภัย
    private function safe_property_get($object, $property, $default = '')
    {
        return isset($object->$property) ? $object->$property : $default;
    }

    // เพิ่มฟังก์ชันสำหรับจัดรูปแบบชื่อสมาชิก
    private function format_member_name($member_data)
    {
        if (!$member_data)
            return 'ไม่ระบุชื่อ';

        $prefix = $this->safe_property_get($member_data, 'mp_prefix', '');
        $fname = $this->safe_property_get($member_data, 'mp_fname', '');
        $lname = $this->safe_property_get($member_data, 'mp_lname', '');

        return trim(($prefix ? $prefix . ' ' : '') . $fname . ' ' . $lname);
    }

    // เพิ่มฟังก์ชันสำหรับจัดรูปแบบชื่อเจ้าหน้าที่
    private function format_staff_name($staff_data)
    {
        if (!$staff_data)
            return 'ไม่ระบุชื่อ';

        $fname = $this->safe_property_get($staff_data, 'm_fname', '');
        $lname = $this->safe_property_get($staff_data, 'm_lname', '');

        return trim($fname . ' ' . $lname);
    }






    private function validate_corruption_report_enhanced_safe($data)
    {
        try {
            // ตรวจสอบฟิลด์จำเป็น
            $required_fields = [
                'corruption_type' => 'ประเภทการทุจริต',
                'complaint_subject' => 'หัวข้อเรื่องร้องเรียน',
                'complaint_details' => 'รายละเอียดเหตุการณ์',
                'perpetrator_name' => 'ชื่อผู้กระทำผิด'
            ];

            foreach ($required_fields as $field => $label) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "กรุณากรอก{$label}",
                        'field' => $field
                    ];
                }
            }

            // ตรวจสอบความสัมพันธ์กับเหตุการณ์
            if (empty($data['reporter_relation'])) {
                return [
                    'success' => false,
                    'message' => 'กรุณาเลือกความสัมพันธ์กับเหตุการณ์',
                    'field' => 'reporter_relation'
                ];
            }

            // ตรวจสอบประเภทการทุจริต
            $allowed_types = ['embezzlement', 'bribery', 'abuse_of_power', 'conflict_of_interest', 'procurement_fraud', 'other'];
            if (!in_array($data['corruption_type'], $allowed_types)) {
                return [
                    'success' => false,
                    'message' => 'ประเภทการทุจริตไม่ถูกต้อง',
                    'field' => 'corruption_type'
                ];
            }

            // ตรวจสอบข้อมูลผู้แจ้ง (เฉพาะเมื่อไม่ใช่โหมดไม่ระบุตัวตน)
            if (!$data['is_anonymous']) {
                log_message('info', 'Validating reporter info for non-anonymous report');

                // ตรวจสอบว่ามีข้อมูลจาก user ที่ login หรือ guest
                $has_user_data = !empty($data['reporter_name']) && !empty($data['reporter_phone']);
                $has_guest_data = !empty($data['guest_reporter_name']) && !empty($data['guest_reporter_phone']);

                log_message('info', 'Has user data: ' . var_export($has_user_data, true));
                log_message('info', 'Has guest data: ' . var_export($has_guest_data, true));

                if (!$has_user_data && !$has_guest_data) {
                    return [
                        'success' => false,
                        'message' => 'กรุณากรอกข้อมูลผู้แจ้งให้ครบถ้วน (ชื่อ-นามสกุล และเบอร์โทรศัพท์)',
                        'field' => 'reporter_info'
                    ];
                }

                // ตรวจสอบรูปแบบเบอร์โทรศัพท์
                $phone_to_check = '';
                if (!empty($data['reporter_phone'])) {
                    $phone_to_check = $data['reporter_phone'];
                } elseif (!empty($data['guest_reporter_phone'])) {
                    $phone_to_check = $data['guest_reporter_phone'];
                }

                if (!empty($phone_to_check)) {
                    $phone_validation = $this->validate_phone_number($phone_to_check);
                    if (!$phone_validation['valid']) {
                        return [
                            'success' => false,
                            'message' => $phone_validation['message'],
                            'field' => 'reporter_phone'
                        ];
                    }
                }

                // ตรวจสอบรูปแบบอีเมล (เฉพาะเมื่อไม่ใช่โหมดไม่ระบุตัวตน)
                $email_to_check = '';
                if (!empty($data['reporter_email'])) {
                    $email_to_check = $data['reporter_email'];
                } elseif (!empty($data['guest_reporter_email'])) {
                    $email_to_check = $data['guest_reporter_email'];
                }

                if (!empty($email_to_check) && !filter_var($email_to_check, FILTER_VALIDATE_EMAIL)) {
                    return [
                        'success' => false,
                        'message' => 'รูปแบบอีเมลไม่ถูกต้อง',
                        'field' => 'reporter_email'
                    ];
                }
            } else {
                log_message('info', 'Skipping reporter info validation for anonymous report');
            }

            // ตรวจสอบวันที่ (ถ้ามี)
            if (!empty($data['incident_date']) && trim($data['incident_date']) !== '') {
                $date_validation = $this->validate_incident_date($data['incident_date']);
                if (!$date_validation['valid']) {
                    return [
                        'success' => false,
                        'message' => $date_validation['message'],
                        'field' => 'incident_date'
                    ];
                }
            }

            // ตรวจสอบเวลา (ถ้ามี)
            if (!empty($data['incident_time']) && trim($data['incident_time']) !== '') {
                $time_validation = $this->validate_incident_time($data['incident_time']);
                if (!$time_validation['valid']) {
                    return [
                        'success' => false,
                        'message' => $time_validation['message'],
                        'field' => 'incident_time'
                    ];
                }
            }

            return ['success' => true];

        } catch (Exception $e) {
            log_message('error', 'Error in validation: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล: ' . $e->getMessage()
            ];
        }
    }




    private function validate_phone_number($phone)
    {
        try {
            // ล้างช่องว่างและตัวอักษรพิเศษ
            $cleaned_phone = preg_replace('/[^0-9]/', '', $phone);

            // ตรวจสอบว่าเป็นตัวเลขทั้งหมดหรือไม่
            if (!ctype_digit($cleaned_phone)) {
                return [
                    'valid' => false,
                    'message' => 'เบอร์โทรศัพท์ต้องเป็นตัวเลขเท่านั้น'
                ];
            }

            // ตรวจสอบความยาว
            $phone_length = strlen($cleaned_phone);

            if ($phone_length < 9) {
                return [
                    'valid' => false,
                    'message' => 'เบอร์โทรศัพท์สั้นเกินไป (ต้องมีอย่างน้อย 9 หลัก)'
                ];
            }

            if ($phone_length > 15) {
                return [
                    'valid' => false,
                    'message' => 'เบอร์โทรศัพท์ยาวเกินไป (ไม่เกิน 15 หลัก)'
                ];
            }

            // ตรวจสอบรูปแบบเบอร์โทรไทย
            if ($phone_length >= 10) {
                // เบอร์มือถือไทย (08x, 09x, 06x)
                if (preg_match('/^0[689][0-9]{8}$/', $cleaned_phone)) {
                    return [
                        'valid' => true,
                        'type' => 'mobile',
                        'cleaned' => $cleaned_phone
                    ];
                }

                // เบอร์บ้านไทย (0x-xxx-xxxx)
                if (preg_match('/^0[2-7][0-9]{7,8}$/', $cleaned_phone)) {
                    return [
                        'valid' => true,
                        'type' => 'landline',
                        'cleaned' => $cleaned_phone
                    ];
                }
            }

            // เบอร์ต่างประเทศหรือรูปแบบอื่นๆ (ตัวเลขอย่างน้อย 9 หลัก)
            if ($phone_length >= 9) {
                return [
                    'valid' => true,
                    'type' => 'international',
                    'cleaned' => $cleaned_phone,
                    'warning' => 'รูปแบบเบอร์โทรศัพท์ไม่ใช่รูปแบบไทย'
                ];
            }

            return [
                'valid' => false,
                'message' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง'
            ];

        } catch (Exception $e) {
            log_message('error', 'Error validating phone number: ' . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบเบอร์โทรศัพท์'
            ];
        }
    }




    private function prepare_report_data_enhanced_safe($form_data, $current_user)
    {
        try {
            log_message('info', 'Preparing report data with current_user: ' . json_encode($current_user));

            // ตรวจสอบข้อมูลผู้แจ้ง
            $reporter_name = '';
            $reporter_phone = '';
            $reporter_email = '';
            $reporter_position = '';
            $reporter_user_id = null;

            if ($form_data['is_anonymous']) {
                // โหมดไม่ระบุตัวตน
                $reporter_name = 'ไม่ระบุตัวตน';
                $reporter_phone = '00000';
                $reporter_email = null;
                $reporter_position = 'ไม่ระบุตัวตน';
                log_message('info', 'Using anonymous reporter data');
            } else {
                // โหมดระบุตัวตน
                if ($current_user['user_type'] === 'public' && $current_user['is_logged_in']) {
                    $reporter_name = $current_user['user_info']['name'];
                    $reporter_phone = !empty($form_data['reporter_phone']) ? $form_data['reporter_phone'] : $current_user['user_info']['phone'];
                    $reporter_email = !empty($form_data['reporter_email']) ? $form_data['reporter_email'] : $current_user['user_info']['email'];
                    $reporter_position = $current_user['user_info']['position'];
                    $reporter_user_id = $current_user['user_info']['id']; // ใช้ ID จาก tbl_member_public

                    log_message('info', 'Using logged-in public user data - User ID: ' . $reporter_user_id);
                } else {
                    $reporter_name = $form_data['guest_reporter_name'];
                    $reporter_phone = $form_data['guest_reporter_phone'];
                    $reporter_email = !empty($form_data['guest_reporter_email']) ? $form_data['guest_reporter_email'] : null;
                    $reporter_position = 'ประชาชน';
                    log_message('info', 'Using guest user data');
                }
            }

            $report_data = [
                'corruption_type' => $form_data['corruption_type'],
                'corruption_type_other' => $form_data['corruption_type_other'] ?: null,
                'complaint_subject' => $form_data['complaint_subject'],
                'complaint_details' => $form_data['complaint_details'],
                'incident_date' => !empty($form_data['incident_date']) ? $form_data['incident_date'] : null,
                'incident_time' => !empty($form_data['incident_time']) ? $form_data['incident_time'] : null,
                'incident_location' => $form_data['incident_location'] ?: null,
                'perpetrator_name' => $form_data['perpetrator_name'],
                'perpetrator_department' => $form_data['perpetrator_department'] ?: null,
                'perpetrator_position' => $form_data['perpetrator_position'] ?: null,
                'other_involved' => $form_data['other_involved'] ?: null,
                'evidence_description' => $form_data['evidence_description'] ?: null,
                'evidence_file_count' => 0,
                'is_anonymous' => $form_data['is_anonymous'],
                'reporter_name' => $reporter_name,
                'reporter_phone' => $reporter_phone,
                'reporter_email' => $reporter_email,
                'reporter_position' => $reporter_position,
                'reporter_relation' => $form_data['reporter_relation'],

                // *** สำคัญ: ตรวจสอบให้แน่ใจว่า reporter_user_id ถูกต้อง ***
                'reporter_user_id' => $reporter_user_id, // ID จาก tbl_member_public (column: id)
                'reporter_user_type' => $current_user['user_type'], // 'public' สำหรับสมาชิก

                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_by' => $form_data['is_anonymous'] ? 'Anonymous User' : ($reporter_name ?: 'Guest User')
            ];

            log_message('info', 'Report data prepared - reporter_user_id: ' . $reporter_user_id . ', reporter_user_type: ' . $current_user['user_type']);

            return $report_data;

        } catch (Exception $e) {
            log_message('error', 'Error preparing report data: ' . $e->getMessage());
            throw new Exception('Failed to prepare report data: ' . $e->getMessage());
        }
    }






    private function validate_incident_date($date_string)
    {
        if (empty($date_string) || trim($date_string) === '') {
            return ['valid' => true]; // วันที่ว่างเปล่าไม่ถือว่าผิด
        }

        try {
            // ตรวจสอบรูปแบบพื้นฐาน
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_string)) {
                return [
                    'valid' => false,
                    'message' => 'รูปแบบวันที่ไม่ถูกต้อง กรุณาใช้รูปแบบ ปปปป-ดด-วว'
                ];
            }

            // แยกส่วนประกอบของวันที่
            $date_parts = explode('-', $date_string);
            $year = intval($date_parts[0]);
            $month = intval($date_parts[1]);
            $day = intval($date_parts[2]);

            // ตรวจสอบขอบเขตของค่า
            if ($year < 1900 || $year > 9999) {
                return [
                    'valid' => false,
                    'message' => 'ปีต้องอยู่ระหว่าง 1900-9999'
                ];
            }

            if ($month < 1 || $month > 12) {
                return [
                    'valid' => false,
                    'message' => 'เดือนต้องอยู่ระหว่าง 01-12'
                ];
            }

            if ($day < 1 || $day > 31) {
                return [
                    'valid' => false,
                    'message' => 'วันต้องอยู่ระหว่าง 01-31'
                ];
            }

            // ตรวจสอบความถูกต้องของวันที่
            if (!checkdate($month, $day, $year)) {
                return [
                    'valid' => false,
                    'message' => 'วันที่ไม่ถูกต้อง เช่น 31 กุมภาพันธ์ หรือ 30 กุมภาพันธ์'
                ];
            }

            // สร้าง DateTime object
            $incident_date = DateTime::createFromFormat('Y-m-d', $date_string);
            if (!$incident_date) {
                return [
                    'valid' => false,
                    'message' => 'ไม่สามารถประมวลผลวันที่ได้'
                ];
            }

            // ตรวจสอบว่าไม่ใช่วันที่ในอนาคต
            $today = new DateTime();
            $today->setTime(23, 59, 59);

            if ($incident_date > $today) {
                return [
                    'valid' => false,
                    'message' => 'วันที่เกิดเหตุไม่สามารถเป็นวันที่ในอนาคตได้'
                ];
            }

            // ตรวจสอบว่าไม่เก่าเกินไป
            $ten_years_ago = new DateTime();
            $ten_years_ago->modify('-10 years');

            if ($incident_date < $ten_years_ago) {
                return [
                    'valid' => false,
                    'message' => 'วันที่เกิดเหตุไม่สามารถเก่าเกิน 10 ปีได้'
                ];
            }

            return ['valid' => true];

        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบวันที่'
            ];
        }
    }


    // เพิ่มฟังก์ชันช่วยเหลือสำหรับการตรวจสอบเวลา
    private function validate_incident_time($time_string)
    {
        if (empty($time_string) || trim($time_string) === '') {
            return ['valid' => true]; // เวลาว่างเปล่าไม่ถือว่าผิด
        }

        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time_string)) {
            return [
                'valid' => false,
                'message' => 'รูปแบบเวลาไม่ถูกต้อง กรุณาใช้รูปแบบ ชช:นน (เช่น 14:30)'
            ];
        }

        return ['valid' => true];
    }





    // แทนที่ฟังก์ชัน prepare_report_data_enhanced ใน Corruption Controller

    private function prepare_report_data_enhanced($form_data, $current_user)
    {
        // ตรวจสอบข้อมูลผู้แจ้ง
        $reporter_name = '';
        $reporter_phone = '';
        $reporter_email = '';
        $reporter_position = '';

        if ($form_data['is_anonymous']) {
            // โหมดไม่ระบุตัวตน - ใส่ข้อมูลดีฟอลต์
            $reporter_name = 'ไม่ระบุตัวตน';
            $reporter_phone = '00000';
            $reporter_email = null; // ไม่ใส่อีเมล
            $reporter_position = 'ไม่ระบุตัวตน';
        } else {
            // โหมดระบุตัวตน
            if ($current_user['user_type'] === 'public' && $current_user['is_logged_in']) {
                // ใช้ข้อมูลจาก user ที่ login มาเติมในฟอร์ม
                $reporter_name = $current_user['user_info']['name'];
                $reporter_phone = !empty($form_data['reporter_phone']) ? $form_data['reporter_phone'] : $current_user['user_info']['phone'];
                $reporter_email = !empty($form_data['reporter_email']) ? $form_data['reporter_email'] : $current_user['user_info']['email'];
                $reporter_position = $current_user['user_info']['position'];
            } else {
                // ใช้ข้อมูลจาก guest form
                $reporter_name = $form_data['guest_reporter_name'];
                $reporter_phone = $form_data['guest_reporter_phone'];
                $reporter_email = !empty($form_data['guest_reporter_email']) ? $form_data['guest_reporter_email'] : null;
                $reporter_position = 'ประชาชน';
            }
        }

        $report_data = [
            'corruption_type' => $form_data['corruption_type'],
            'corruption_type_other' => $form_data['corruption_type_other'],
            'complaint_subject' => $form_data['complaint_subject'],
            'complaint_details' => $form_data['complaint_details'],
            'incident_date' => !empty($form_data['incident_date']) ? $form_data['incident_date'] : null,
            'incident_time' => !empty($form_data['incident_time']) ? $form_data['incident_time'] : null,
            'incident_location' => $form_data['incident_location'],
            'perpetrator_name' => $form_data['perpetrator_name'],
            'perpetrator_department' => $form_data['perpetrator_department'],
            'perpetrator_position' => $form_data['perpetrator_position'],
            'other_involved' => $form_data['other_involved'],
            'evidence_description' => $form_data['evidence_description'],
            'evidence_file_count' => 0, // จะอัปเดตหลังจากอัปโหลดไฟล์
            'is_anonymous' => $form_data['is_anonymous'],
            'reporter_name' => $reporter_name, // เก็บไว้เสมอ แต่จะเป็นข้อมูลดีฟอลต์ถ้า anonymous
            'reporter_phone' => $reporter_phone, // เก็บไว้เสมอ แต่จะเป็นข้อมูลดีฟอลต์ถ้า anonymous
            'reporter_email' => $reporter_email, // อาจเป็น null ถ้า anonymous
            'reporter_position' => $reporter_position, // เก็บไว้เสมอ แต่จะเป็นข้อมูลดีฟอลต์ถ้า anonymous
            'reporter_relation' => $form_data['reporter_relation'], // เก็บไว้เสมอ แม้จะ anonymous
            'reporter_user_id' => ($current_user['is_logged_in'] && $current_user['user_type'] === 'public' && !$form_data['is_anonymous']) ? $current_user['user_info']['id'] : null,
            'reporter_user_type' => $current_user['user_type'],
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_by' => $form_data['is_anonymous'] ?
                'Anonymous User' :
                ($current_user['is_logged_in'] && $current_user['user_type'] === 'public' ?
                    $current_user['user_info']['name'] :
                    ($reporter_name ?: 'Guest User'))
        ];

        return $report_data;
    }







    private function get_form_data_enhanced_safe()
    {
        try {
            // ตรวจสอบค่า is_anonymous จากหลายแหล่ง
            $is_anonymous = false;

            // ตรวจสอบจาก checkbox หลัก
            $anonymous_checkbox = $this->input->post('is_anonymous');
            if ($anonymous_checkbox === '1' || $anonymous_checkbox === 'on' || $anonymous_checkbox === true) {
                $is_anonymous = true;
            }

            // ตรวจสอบจาก hidden field
            $anonymous_flag = $this->input->post('anonymous_flag');
            if ($anonymous_flag === '1') {
                $is_anonymous = true;
            }

            log_message('info', 'Anonymous checkbox value: ' . var_export($anonymous_checkbox, true));
            log_message('info', 'Anonymous flag value: ' . var_export($anonymous_flag, true));
            log_message('info', 'Final is_anonymous: ' . var_export($is_anonymous, true));

            return [
                'corruption_type' => $this->input->post('corruption_type') ?: '',
                'corruption_type_other' => $this->input->post('corruption_type_other') ?: '',
                'complaint_subject' => trim($this->input->post('complaint_subject') ?: ''),
                'complaint_details' => trim($this->input->post('complaint_details') ?: ''),
                'incident_date' => $this->input->post('incident_date') ?: '',
                'incident_time' => $this->input->post('incident_time') ?: '',
                'incident_location' => trim($this->input->post('incident_location') ?: ''),
                'perpetrator_name' => trim($this->input->post('perpetrator_name') ?: ''),
                'perpetrator_department' => trim($this->input->post('perpetrator_department') ?: ''),
                'perpetrator_position' => trim($this->input->post('perpetrator_position') ?: ''),
                'other_involved' => trim($this->input->post('other_involved') ?: ''),
                'evidence_description' => trim($this->input->post('evidence_description') ?: ''),
                'is_anonymous' => $is_anonymous,
                'reporter_name' => trim($this->input->post('reporter_name') ?: ''),
                'reporter_phone' => trim($this->input->post('reporter_phone') ?: ''),
                'reporter_email' => trim($this->input->post('reporter_email') ?: ''),
                'reporter_position' => trim($this->input->post('reporter_position') ?: ''),
                'reporter_relation' => $this->get_reporter_relation_from_form_safe(),
                'guest_reporter_name' => trim($this->input->post('guest_reporter_name') ?: ''),
                'guest_reporter_phone' => trim($this->input->post('guest_reporter_phone') ?: ''),
                'guest_reporter_email' => trim($this->input->post('guest_reporter_email') ?: '')
            ];
        } catch (Exception $e) {
            log_message('error', 'Error getting form data: ' . $e->getMessage());
            throw new Exception('Failed to process form data: ' . $e->getMessage());
        }
    }





    private function get_reporter_relation_from_form_safe()
    {
        try {
            // ตรวจสอบจาก POST data ที่ส่งมาจาก JavaScript
            $relation_from_js = $this->input->post('reporter_relation');
            if (!empty($relation_from_js)) {
                return $relation_from_js;
            }

            // ตรวจสอบจาก field อื่นๆ ตามลำดับ
            $relations = [
                $this->input->post('anonymous_reporter_relation'),
                $this->input->post('guest_reporter_relation')
            ];

            foreach ($relations as $relation) {
                if (!empty($relation)) {
                    return $relation;
                }
            }

            log_message('warning', 'No reporter relation found in form data');
            return '';

        } catch (Exception $e) {
            log_message('error', 'Error getting reporter relation: ' . $e->getMessage());
            return '';
        }
    }






    public function check_user_status()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $current_user = $this->get_current_user_for_corruption_report();

            $response = [
                'success' => true,
                'user_type' => $current_user['user_type'],
                'is_logged_in' => $current_user['is_logged_in'],
                'can_report' => $current_user['user_type'] !== 'staff',
                'user_info' => null
            ];

            // ถ้าเป็น public user ส่งข้อมูลไปด้วย
            if ($current_user['user_type'] === 'public' && $current_user['is_logged_in']) {
                $response['user_info'] = [
                    'name' => $current_user['user_info']['name'],
                    'phone' => $current_user['user_info']['phone'],
                    'email' => $current_user['user_info']['email'],
                    'position' => $current_user['user_info']['position']
                ];
            }

            // ถ้าเป็น staff ส่งข้อความแจ้งเตือน
            if ($current_user['user_type'] === 'staff') {
                $response['staff_message'] = 'ท่านต้องออกจากระบบ login บุคลากรภายใน กรุณาใช้ login ของประชาชน หรือใช้ในฐานะ guest';
                $response['logout_url'] = site_url('User/logout');
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error checking user status: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }



    // ===================================================================
    // *** หน้าติดตามสถานะรายงาน ***
    // ===================================================================
    /**
     * หน้าติดตามสถานะรายงานการทุจริต
     */
    public function track_status()
    {
        try {
            $data = $this->prepare_navbar_data_safe();

            // ตรวจสอบว่ามี report ID ที่ส่งมาจาก URL หรือไม่
            $report_id = $this->input->get('report_id') ?: '';
            $data['report_id'] = $report_id;
            $data['search_performed'] = false;
            $data['corruption_report_info'] = null;
            $data['error_message'] = '';

            // ถ้ามี report ID ให้ทำการค้นหาทันที
            if (!empty($report_id)) {
                $search_result = $this->perform_report_search($report_id);
                $data = array_merge($data, $search_result);
            }

            $data['page_title'] = 'ติดตามสถานะรายงานการทุจริต';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'ติดตามสถานะรายงาน', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $data['error_message'] ?: $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/corruption_track_status', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in corruption track_status: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }


    /**
     * ค้นหารายงานด้วย Report ID (AJAX) - เพิ่ม reCAPTCHA
     */
    public function search_report()
    {
        // *** เพิ่ม: Log debug สำหรับ reCAPTCHA ***
        log_message('info', '=== CORRUPTION SEARCH REPORT START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));
        log_message('info', 'User Agent: ' . $this->input->server('HTTP_USER_AGENT'));

        // ตั้งค่า header
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
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
                log_message('info', 'Starting reCAPTCHA verification for corruption search');

                try {
                    // *** ใช้ reCAPTCHA Library ที่มีอยู่ ***
                    $recaptcha_options = [
                        'action' => $recaptcha_action ?: 'corruption_track_search',
                        'source' => $recaptcha_source ?: 'track_search_form',
                        'user_type_detected' => $user_type_detected ?: 'guest',
                        'form_source' => 'corruption_track_search',
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

                        log_message('info', 'reCAPTCHA verification successful for corruption search');
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
                log_message('info', 'No reCAPTCHA token provided for corruption search');
            } else {
                log_message('info', 'Development mode - skipping reCAPTCHA verification');
            }

            $report_id = $this->input->post('report_id');

            log_message('info', 'Search report_id: ' . ($report_id ?: 'empty'));

            if (empty($report_id)) {
                log_message('info', 'Empty report_id provided');
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาระบุหมายเลขรายงาน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ทำการค้นหา
            log_message('info', 'Performing report search for: ' . $report_id);
            $search_result = $this->perform_report_search($report_id);

            if ($search_result['search_performed'] && $search_result['corruption_report_info']) {
                log_message('info', 'Report found successfully: ' . $report_id);
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'report' => $search_result['corruption_report_info'],
                        'files' => $search_result['corruption_report_info']->files ?? [],
                        'history' => $search_result['corruption_report_info']->history ?? []
                    ],
                    'message' => 'พบข้อมูลรายงาน'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                log_message('info', 'Report not found: ' . $report_id);
                echo json_encode([
                    'success' => false,
                    'message' => $search_result['error_message'] ?: 'ไม่พบรายงานที่ต้องการ'
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in corruption search_report: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ], JSON_UNESCAPED_UNICODE);
        }

        log_message('info', '=== CORRUPTION SEARCH REPORT END ===');
        exit;
    }

    // ===================================================================
    // *** หน้าจัดการสำหรับเจ้าหน้าที่ ***
    // ===================================================================

    /**
     * หน้าจัดการรายงานการทุจริตสำหรับเจ้าหน้าที่
     */
    public function admin_management()
    {
        try {
            log_message('info', '=== CORRUPTION ADMIN MANAGEMENT START ===');

            // ตรวจสอบการ login เจ้าหน้าที่
            $m_id = $this->session->userdata('m_id');

            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์
            $staff_check = $this->get_staff_data($m_id);

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_corruption_management_permission($staff_check)) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงระบบจัดการรายงานการทุจริต');
                redirect('Dashboard');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // ตัวกรองข้อมูล
            $filters = [
                'status' => $this->input->get('status'),
                'corruption_type' => $this->input->get('corruption_type'),
                'priority' => $this->input->get('priority'),
                'anonymous' => $this->input->get('anonymous'),
                'assigned_to' => $this->input->get('assigned_to'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search')
            ];

            // Pagination
            $this->load->library('pagination');
            $per_page = 20;
            $current_page = (int) ($this->input->get('page') ?? 1);
            $offset = ($current_page - 1) * $per_page;

            // ดึงข้อมูลรายงานพร้อมตัวกรอง
            $report_result = $this->corruption_model->get_corruption_reports_with_filters($filters, $per_page, $offset);
            $corruption_reports = $report_result['data'] ?? [];
            $total_rows = $report_result['total'] ?? 0;

            // ดึงรายงานล่าสุดสำหรับแสดงใน Analytics
            $recent_reports = $this->corruption_model->get_recent_corruption_reports(10);

            // สถิติรายงาน
            $corruption_summary = $this->corruption_model->get_corruption_statistics();

            // ตัวเลือกสำหรับ Filter
            $status_options = $this->get_status_options();
            $corruption_type_options = $this->get_corruption_types();
            $priority_options = $this->get_priority_options();
            $staff_options = $this->corruption_model->get_assignable_staff();

            // สิทธิ์การใช้งาน
            $can_view = true;
            $can_edit = $this->check_corruption_edit_permission($staff_check);
            $can_delete = $this->check_corruption_delete_permission($staff_check);
            $can_assign = $this->check_corruption_assign_permission($staff_check);
            $can_update_status = $can_edit; // สิทธิ์อัปเดตสถานะเหมือนกับแก้ไข

            // Pagination Setup
            $pagination_config = [
                'base_url' => site_url('Corruption/admin_management'),
                'total_rows' => $total_rows,
                'per_page' => $per_page,
                'page_query_string' => TRUE,
                'query_string_segment' => 'page',
                'reuse_query_string' => TRUE,
                'num_links' => 3,
                'use_page_numbers' => TRUE,
                'full_tag_open' => '<div class="pagination-wrapper"><ul class="pagination">',
                'full_tag_close' => '</ul></div>',
                'first_link' => 'แรก',
                'last_link' => 'สุดท้าย',
                'first_tag_open' => '<li class="page-item">',
                'first_tag_close' => '</li>',
                'prev_link' => '&laquo;',
                'prev_tag_open' => '<li class="page-item">',
                'prev_tag_close' => '</li>',
                'next_link' => '&raquo;',
                'next_tag_open' => '<li class="page-item">',
                'next_tag_close' => '</li>',
                'last_tag_open' => '<li class="page-item">',
                'last_tag_close' => '</li>',
                'cur_tag_open' => '<li class="page-item active"><span class="page-link">',
                'cur_tag_close' => '</span></li>',
                'num_tag_open' => '<li class="page-item">',
                'num_tag_close' => '</li>',
                'anchor_class' => 'page-link'
            ];

            $this->pagination->initialize($pagination_config);

            // สร้าง user_info object สำหรับ header
            $user_info_object = $this->create_complete_user_info($staff_check);

            // รวมข้อมูลทั้งหมด
            $data = array_merge($data, [
                'corruption_reports' => $corruption_reports,
                'recent_reports' => $recent_reports,
                'corruption_summary' => $corruption_summary,
                'filters' => $filters,
                'total_rows' => $total_rows,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'pagination' => $this->pagination->create_links(),
                'staff_info' => [
                    'id' => $staff_check->m_id,
                    'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                    'system' => $staff_check->m_system
                ],
                'is_logged_in' => true,
                'user_type' => 'staff',
                'user_info' => $user_info_object,
                'current_user' => $user_info_object,
                'staff_data' => $user_info_object,

                // สิทธิ์การใช้งาน
                'can_view' => $can_view,
                'can_edit' => $can_edit,
                'can_delete' => $can_delete,
                'can_assign' => $can_assign,
                'can_update_status' => $can_update_status,

                // ตัวเลือกสำหรับ dropdowns
                'status_options' => $status_options,
                'corruption_type_options' => $corruption_type_options,
                'priority_options' => $priority_options,
                'staff_options' => $staff_options
            ]);

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Dashboard')],
                ['title' => 'จัดการรายงานการทุจริต', 'url' => '']
            ];

            $data['page_title'] = 'จัดการรายงานการทุจริต';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            log_message('info', 'Corruption admin data prepared: ' . count($corruption_reports) . ' reports loaded');
            log_message('info', 'Statistics: ' . json_encode($corruption_summary));
            log_message('info', '=== CORRUPTION ADMIN MANAGEMENT END ===');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/corruption_manage', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in corruption admin_management: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Dashboard');
            }
        }
    }



    // ===================================================================
    // *** การจัดการสถานะและการดำเนินการ ***
    // ===================================================================

    /**
     * อัปเดตสถานะรายงาน (AJAX)
     */
    public function update_status()
    {
        // ล้าง output buffer และตั้งค่า JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Only POST method allowed');
            }

            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                exit;
            }

            $report_id = $this->input->post('report_id');
            $new_status = $this->input->post('new_status');
            $new_priority = $this->input->post('new_priority');
            $notes = $this->input->post('note') ?: $this->input->post('notes') ?: '';

            // Validation
            if (empty($report_id) || empty($new_status)) {
                echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                exit;
            }

            $allowed_statuses = ['pending', 'under_review', 'investigating', 'resolved', 'dismissed', 'closed'];
            if (!in_array($new_status, $allowed_statuses)) {
                echo json_encode(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
                exit;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_corruption_edit_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์อัปเดตสถานะ']);
                exit;
            }

            // ดึงข้อมูลรายงาน
            $report = $this->corruption_model->get_corruption_report_by_report_id($report_id);
            if (!$report) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลรายงาน']);
                exit;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);
            $old_status = $report->report_status;

            // ตรวจสอบว่าสถานะเปลี่ยนแปลงจริง
            if ($old_status === $new_status && empty($new_priority) && empty($notes)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'ไม่มีการเปลี่ยนแปลง',
                    'new_status' => $new_status,
                    'updated_by' => $updated_by
                ]);
                exit;
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตสถานะ
            $update_result = $this->corruption_model->update_corruption_status(
                $report->corruption_id,
                $new_status,
                $updated_by,
                $notes
            );

            if (!$update_result) {
                $this->db->trans_rollback();
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตสถานะได้']);
                exit;
            }

            // อัปเดต priority ถ้ามี
            if (!empty($new_priority)) {
                $this->db->where('corruption_id', $report->corruption_id);
                $this->db->update('tbl_corruption_reports', [
                    'priority_level' => $new_priority,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $updated_by
                ]);
            }

            // บันทึกประวัติ
            $this->corruption_model->add_corruption_history_safe(
                $report->corruption_id,
                'status_changed',
                "เปลี่ยนสถานะจาก '{$old_status}' เป็น '{$new_status}'" .
                (!empty($new_priority) ? " และระดับความสำคัญเป็น '{$new_priority}'" : '') .
                (!empty($notes) ? " - หมายเหตุ: {$notes}" : ''),
                $updated_by,
                $m_id,
                $old_status,
                $new_status
            );

            // *** สร้าง notification สำหรับการอัปเดตสถานะ ***
            try {
                $this->create_status_update_notifications_complete($report, $old_status, $new_status, $new_priority, $notes, $staff_data);
            } catch (Exception $e) {
                log_message('warning', 'Failed to create status update notifications: ' . $e->getMessage());
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction commit failed');
            }

            log_message('info', "Corruption report status updated: {$report_id} from {$old_status} to {$new_status} by {$updated_by}");

            echo json_encode([
                'success' => true,
                'message' => 'อัปเดตสถานะสำเร็จ',
                'new_status' => $new_status,
                'old_status' => $old_status,
                'new_priority' => $new_priority,
                'updated_by' => $updated_by,
                'report_id' => $report_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            if (isset($this->db) && $this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }

            log_message('error', 'Error in update_status: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'UPDATE_STATUS_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }

        exit;
    }



    private function create_status_update_notifications_complete($report, $old_status, $new_status, $new_priority, $notes, $staff_data)
    {
        try {
            log_message('info', 'Creating status update notifications');

            $total_notifications_created = 0;
            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // *** 1. สร้างการแจ้งเตือนสำหรับผู้แจ้ง (ถ้าไม่ใช่ anonymous) ***
            if (
                !$report->is_anonymous &&
                $report->reporter_user_type === 'public' &&
                !empty($report->reporter_user_id)
            ) {

                try {
                    $user_notification_data = [
                        'type' => 'corruption_status_update',
                        'title' => 'รายงานการทุจริตมีการอัปเดต',
                        'message' => $this->prepare_status_update_message_for_user($report, $old_status, $new_status, $notes),
                        'reference_id' => $report->corruption_id,
                        'reference_table' => 'tbl_corruption_reports',
                        'target_user_id' => $report->reporter_user_id,
                        'target_role' => 'public',
                        'priority' => $this->determine_notification_priority_for_status($new_status),
                        'icon' => $this->get_status_icon($new_status),
                        'url' => site_url('Corruption/my_report_detail/' . $report->corruption_report_id),
                        'data' => json_encode([
                            'corruption_id' => $report->corruption_id,
                            'report_id' => $report->corruption_report_id,
                            'old_status' => $old_status,
                            'new_status' => $new_status,
                            'new_priority' => $new_priority,
                            'updated_by' => $updated_by,
                            'notification_type' => 'status_update'
                        ]),
                        'is_read' => 0,
                        'is_system' => 1,
                        'is_archived' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $staff_data->m_id
                    ];

                    $this->db->insert('tbl_notifications', $user_notification_data);

                    if ($this->db->affected_rows() > 0) {
                        $total_notifications_created++;
                        log_message('info', "Status update notification created for reporter: {$report->reporter_name}");
                    }

                } catch (Exception $e) {
                    log_message('error', "Failed to create status update notification for reporter: " . $e->getMessage());
                }
            }

            // *** 2. สร้างการแจ้งเตือนสำหรับ Staff (แค่ 1 row) ***
            try {
                $staff_notification_data = [
                    'type' => 'corruption_status_updated',
                    'title' => 'รายงานการทุจริตมีการอัปเดตสถานะ',
                    'message' => $this->prepare_status_update_message_for_staff($report, $old_status, $new_status, $updated_by, $notes),
                    'reference_id' => $report->corruption_id,
                    'reference_table' => 'tbl_corruption_reports',
                    'target_user_id' => null, // null = ส่งให้ staff ทั้งหมดที่มีสิทธิ์ (ยกเว้นคนที่อัปเดต)
                    'target_role' => 'staff',
                    'priority' => $this->determine_notification_priority_for_status($new_status),
                    'icon' => $this->get_status_icon($new_status),
                    'url' => site_url('Corruption/report_detail/' . $report->corruption_report_id),
                    'data' => json_encode([
                        'corruption_id' => $report->corruption_id,
                        'report_id' => $report->corruption_report_id,
                        'old_status' => $old_status,
                        'new_status' => $new_status,
                        'new_priority' => $new_priority,
                        'updated_by' => $updated_by,
                        'updated_by_id' => $staff_data->m_id,
                        'notification_type' => 'status_update',
                        'required_permission' => 'corruption_management',
                        'exclude_user_id' => $staff_data->m_id // ไม่แสดงให้คนที่อัปเดต
                    ]),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $staff_data->m_id
                ];

                $this->db->insert('tbl_notifications', $staff_notification_data);

                if ($this->db->affected_rows() > 0) {
                    $total_notifications_created++;
                    log_message('info', "System-wide status update notification created for authorized staff (excluding updater)");
                }

            } catch (Exception $e) {
                log_message('error', "Failed to create system-wide status update notification: " . $e->getMessage());
            }

            log_message('info', "Successfully created {$total_notifications_created} status update notifications");

            return $total_notifications_created > 0;

        } catch (Exception $e) {
            log_message('error', 'Error creating status update notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    // ===================================================================
    // *** Helper Functions สำหรับ Notification ***
    // ===================================================================

    /**
     * เตรียมข้อความ notification สำหรับการอัปเดตสถานะ (สำหรับผู้แจ้ง)
     */
    private function prepare_status_update_message_for_user($report, $old_status, $new_status, $notes = '')
    {
        try {
            $status_labels = [
                'pending' => 'รอดำเนินการ',
                'under_review' => 'กำลังตรวจสอบ',
                'investigating' => 'กำลังสอบสวน',
                'resolved' => 'แก้ไขแล้ว',
                'dismissed' => 'ไม่อนุมัติ',
                'closed' => 'ปิดเรื่อง'
            ];

            $old_status_label = $status_labels[$old_status] ?? $old_status;
            $new_status_label = $status_labels[$new_status] ?? $new_status;

            $message = "รายงาน \"{$report->complaint_subject}\" ";
            $message .= "เปลี่ยนสถานะจาก \"{$old_status_label}\" เป็น \"{$new_status_label}\"\n";
            $message .= "หมายเลขรายงาน: {$report->corruption_report_id}\n";
            $message .= "วันที่อัปเดต: " . date('d/m/Y H:i');

            if (!empty($notes)) {
                $message .= "\nหมายเหตุ: {$notes}";
            }

            return $message;

        } catch (Exception $e) {
            log_message('error', 'Error preparing status update message for user: ' . $e->getMessage());
            return "รายงานการทุจริตของคุณมีการอัปเดตสถานะ - หมายเลข: {$report->corruption_report_id}";
        }
    }

    /**
     * เตรียมข้อความ notification สำหรับการอัปเดตสถานะ (สำหรับเจ้าหน้าที่)
     */
    private function prepare_status_update_message_for_staff($report, $old_status, $new_status, $updated_by, $notes = '')
    {
        try {
            $status_labels = [
                'pending' => 'รอดำเนินการ',
                'under_review' => 'กำลังตรวจสอบ',
                'investigating' => 'กำลังสอบสวน',
                'resolved' => 'แก้ไขแล้ว',
                'dismissed' => 'ไม่อนุมัติ',
                'closed' => 'ปิดเรื่อง'
            ];

            $old_status_label = $status_labels[$old_status] ?? $old_status;
            $new_status_label = $status_labels[$new_status] ?? $new_status;

            $message = "รายงานการทุจริต \"{$report->complaint_subject}\" ";
            $message .= "ได้รับการอัปเดตสถานะ\n";
            $message .= "หมายเลขรายงาน: {$report->corruption_report_id}\n";
            $message .= "เปลี่ยนสถานะจาก: {$old_status_label}\n";
            $message .= "เป็น: {$new_status_label}\n";
            $message .= "โดย: {$updated_by}\n";
            $message .= "วันที่: " . date('d/m/Y H:i');

            if (!empty($notes)) {
                $message .= "\nหมายเหตุ: {$notes}";
            }

            return $message;

        } catch (Exception $e) {
            log_message('error', 'Error preparing status update message for staff: ' . $e->getMessage());
            return "รายงานการทุจริตมีการอัปเดตสถานะ - หมายเลข: {$report->corruption_report_id}";
        }
    }

    /**
     * กำหนดระดับความสำคัญของ notification ตามสถานะ
     */
    private function determine_notification_priority_for_status($status)
    {
        $priority_map = [
            'resolved' => 'high',
            'dismissed' => 'high',
            'closed' => 'normal',
            'investigating' => 'normal',
            'under_review' => 'low',
            'pending' => 'low'
        ];

        return $priority_map[$status] ?? 'normal';
    }

    /**
     * ได้ไอคอนสำหรับสถานะ
     */
    private function get_status_icon($status)
    {
        $icon_map = [
            'pending' => 'fas fa-clock',
            'under_review' => 'fas fa-search',
            'investigating' => 'fas fa-magnifying-glass',
            'resolved' => 'fas fa-check-circle',
            'dismissed' => 'fas fa-times-circle',
            'closed' => 'fas fa-archive'
        ];

        return $icon_map[$status] ?? 'fas fa-info-circle';
    }




    /**
     * มอบหมายรายงาน (AJAX)
     */
    public function assign_report()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Only POST method allowed');
            }

            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                exit;
            }

            $report_id = $this->input->post('report_id');
            $assigned_to = $this->input->post('assigned_to');
            $department = $this->input->post('department') ?: '';
            $assign_note = $this->input->post('assign_note') ?: '';

            // Validation
            if (empty($report_id) || empty($assigned_to)) {
                echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                exit;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_corruption_assign_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์มอบหมายงาน']);
                exit;
            }

            // ดึงข้อมูลรายงาน
            $report = $this->corruption_model->get_corruption_report_by_report_id($report_id);
            if (!$report) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลรายงาน']);
                exit;
            }

            $assigned_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // มอบหมายงาน
            $assign_result = $this->corruption_model->assign_corruption_report(
                $report->corruption_id,
                $assigned_to,
                $assigned_by,
                $department
            );

            if (!$assign_result) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถมอบหมายงานได้']);
                exit;
            }

            // ดึงข้อมูลผู้ได้รับมอบหมาย
            $assigned_staff = $this->get_staff_data($assigned_to);
            $assigned_name = $assigned_staff ? trim($assigned_staff->m_fname . ' ' . $assigned_staff->m_lname) : "ID: {$assigned_to}";

            // บันทึกประวัติ
            $this->corruption_model->add_corruption_history_safe(
                $report->corruption_id,
                'assigned',
                "มอบหมายงานให้ {$assigned_name}" .
                (!empty($department) ? " ({$department})" : '') .
                (!empty($assign_note) ? " - หมายเหตุ: {$assign_note}" : ''),
                $assigned_by,
                $m_id,
                null,
                $assigned_to
            );

            log_message('info', "Corruption report assigned: {$report_id} to {$assigned_name} by {$assigned_by}");

            echo json_encode([
                'success' => true,
                'message' => 'มอบหมายงานสำเร็จ',
                'assigned_to' => $assigned_to,
                'assigned_name' => $assigned_name,
                'assigned_by' => $assigned_by,
                'department' => $department,
                'report_id' => $report_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in assign_report: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'ASSIGN_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }

        exit;
    }

    /**
     * เพิ่มการตอบกลับ (AJAX)
     */
    public function add_response()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Only POST method allowed'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $report_id = $this->input->post('report_id');
            $response_message = $this->input->post('response_message');

            if (empty($report_id) || empty($response_message)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_corruption_edit_permission($staff_data)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์เพิ่มการตอบกลับ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงข้อมูลรายงาน
            $report = $this->corruption_model->get_corruption_report_by_report_id($report_id);
            if (!$report) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลรายงาน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $response_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เพิ่มการตอบกลับ
            $response_result = $this->corruption_model->add_corruption_response(
                $report->corruption_id,
                $response_message,
                $response_by
            );

            if (!$response_result) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเพิ่มการตอบกลับได้'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // บันทึกประวัติ
            $this->corruption_model->add_corruption_history_safe(
                $report->corruption_id,
                'commented',
                "เพิ่มการตอบกลับ: " . mb_substr($response_message, 0, 100) . (mb_strlen($response_message) > 100 ? '...' : ''),
                $response_by,
                $m_id
            );

            log_message('info', "Response added to corruption report: {$report_id} by {$response_by}");

            echo json_encode([
                'success' => true,
                'message' => 'เพิ่มการตอบกลับสำเร็จ',
                'response_by' => $response_by,
                'report_id' => $report_id,
                'response_preview' => mb_substr($response_message, 0, 50) . (mb_strlen($response_message) > 50 ? '...' : ''),
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in add_response: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'ADD_RESPONSE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }


    private function json_success($message, $data = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function json_error($message, $error_code = 'ERROR')
    {
        echo json_encode([
            'success' => false,
            'message' => $message,
            'error_code' => $error_code,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }




    public function delete_report()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Only POST method allowed',
                    'error_code' => 'INVALID_METHOD'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ',
                    'error_code' => 'NO_PERMISSION'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $report_id = $this->input->post('report_id');
            $delete_reason = trim($this->input->post('delete_reason'));

            // ตรวจสอบ report_id
            if (empty($report_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบหมายเลขรายงาน',
                    'error_code' => 'MISSING_REPORT_ID'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบเหตุผลการลบ (บังคับกรอก)
            if (empty($delete_reason)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกเหตุผลในการลบรายงาน',
                    'error_code' => 'MISSING_DELETE_REASON',
                    'field' => 'delete_reason'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบความยาวเหตุผล
            if (strlen($delete_reason) < 10) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เหตุผลในการลบต้องมีอย่างน้อย 10 ตัวอักษร',
                    'error_code' => 'DELETE_REASON_TOO_SHORT',
                    'field' => 'delete_reason'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (strlen($delete_reason) > 500) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เหตุผลในการลบต้องไม่เกิน 500 ตัวอักษร',
                    'error_code' => 'DELETE_REASON_TOO_LONG',
                    'field' => 'delete_reason'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_corruption_delete_permission($staff_data)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์ลบรายงาน',
                    'error_code' => 'NO_DELETE_PERMISSION'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงข้อมูลรายงาน
            $report = $this->corruption_model->get_corruption_report_by_report_id($report_id);
            if (!$report) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลรายงาน',
                    'error_code' => 'REPORT_NOT_FOUND'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $deleted_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // Archive รายงาน (ไม่ลบจริง)
            $delete_result = $this->corruption_model->archive_corruption_report($report->corruption_id, $deleted_by);

            if (!$delete_result) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถลบรายงานได้',
                    'error_code' => 'DELETE_FAILED'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // บันทึกประวัติพร้อมเหตุผลการลบ
            $this->corruption_model->add_corruption_history_safe(
                $report->corruption_id,
                'archived',
                "Archive รายงาน - เหตุผล: {$delete_reason}",
                $deleted_by,
                $m_id
            );

            log_message('info', "Corruption report archived: {$report_id} by {$deleted_by} - Reason: {$delete_reason}");

            echo json_encode([
                'success' => true,
                'message' => 'ลบรายงานสำเร็จ',
                'deleted_by' => $deleted_by,
                'report_id' => $report_id,
                'delete_reason' => $delete_reason,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in delete_report: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'DELETE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }
    /**
     * ส่งออกรายงานเป็น Excel
     */
    public function export_excel()
    {
        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบ');
                redirect('User');
                return;
            }

            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_corruption_management_permission($staff_data)) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์ส่งออกข้อมูล');
                redirect('Dashboard');
                return;
            }

            // ดึงข้อมูลรายงาน
            $filters = [
                'status' => $this->input->get('status'),
                'corruption_type' => $this->input->get('corruption_type'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to')
            ];

            $reports = $this->corruption_model->export_corruption_reports($filters);

            if (empty($reports)) {
                $this->session->set_flashdata('warning_message', 'ไม่มีข้อมูลสำหรับส่งออก');
                redirect('Corruption/admin_management');
                return;
            }

            // โหลด PhpSpreadsheet library หรือ PHPExcel
            $this->load->library('excel');

            // สร้างไฟล์ Excel
            $filename = 'รายงานการทุจริต_' . date('Y-m-d_H-i-s') . '.xlsx';

            // ส่งไฟล์ให้ดาวน์โหลด
            $this->excel->create_corruption_report_excel($reports, $filename);

        } catch (Exception $e) {
            log_message('error', 'Error in export_excel: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล');
            redirect('Corruption/admin_management');
        }
    }





    /**
     * ดูไฟล์หลักฐานในเบราว์เซอร์
     */
    public function view_evidence($file_id = null)
    {
        try {
            if (empty($file_id)) {
                show_404();
                return;
            }

            // ดึงข้อมูลไฟล์จากฐานข้อมูล
            $this->db->select('*');
            $this->db->from('tbl_corruption_files');
            $this->db->where('file_id', $file_id);
            $this->db->where('file_status', 'active');
            $file = $this->db->get()->row();

            if (!$file) {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file->file_path)) {
                log_message('error', "File not found: {$file->file_path}");
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)
            $current_user = $this->get_current_user_simple();

            $can_access = false;
            if ($current_user['user_type'] === 'staff') {
                // เจ้าหน้าที่สามารถดูได้
                $staff_data = $this->get_staff_data($current_user['user_id']);
                $can_access = $this->check_corruption_management_permission($staff_data);
            } elseif ($current_user['user_type'] === 'public') {
                // สมาชิกสามารถดูไฟล์ของตนเองได้
                $report = $this->corruption_model->get_corruption_report_by_id($file->corruption_id);
                $can_access = ($report && $report->reporter_user_id == $current_user['user_id'] && $report->reporter_user_type === 'public');
            }

            if (!$can_access) {
                show_404();
                return;
            }

            // อัปเดตจำนวนการดู
            $this->db->where('file_id', $file_id);
            $this->db->update('tbl_corruption_files', [
                'download_count' => $file->download_count + 1,
                'last_downloaded' => date('Y-m-d H:i:s')
            ]);

            // กำหนด Content-Type ตามนามสกุลไฟล์
            $file_extension = strtolower($file->file_extension);
            $content_types = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'txt' => 'text/plain'
            ];

            $content_type = $content_types[$file_extension] ?? 'application/octet-stream';

            // ตั้งค่า headers สำหรับแสดงในเบราว์เซอร์
            header('Content-Type: ' . $content_type);
            header('Content-Length: ' . filesize($file->file_path));

            // สำหรับไฟล์ที่สามารถแสดงในเบราว์เซอร์ได้ (PDF, รูปภาพ)
            if (in_array($file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'])) {
                header('Content-Disposition: inline; filename="' . $file->file_original_name . '"');
            } else {
                // สำหรับไฟล์ Office แนะนำให้ดาวน์โหลด
                header('Content-Disposition: attachment; filename="' . $file->file_original_name . '"');
            }

            // ป้องกันการแคช
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // ส่งเนื้อหาไฟล์
            readfile($file->file_path);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Error viewing evidence file: ' . $e->getMessage());
            show_404();
        }
    }




    public function report_detail($report_id = null)
    {
        try {
            // ตรวจสอบการ login เจ้าหน้าที่
            $m_id = $this->session->userdata('m_id');

            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบ report_id
            if (empty($report_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขรายงาน');
                redirect('Corruption/admin_management');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $staff_check = $this->get_staff_data($m_id);
            if (!$staff_check || !$this->check_corruption_management_permission($staff_check)) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้');
                redirect('Dashboard');
                return;
            }

            // ดึงข้อมูลรายงาน
            $report_detail = $this->corruption_model->get_corruption_report_by_report_id($report_id);

            if (!$report_detail) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลรายงานที่ระบุ');
                redirect('Corruption/admin_management');
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึง
            $access_check = $this->corruption_model->check_report_access(
                $report_detail->corruption_id,
                $m_id,
                'staff'
            );

            if (!$access_check) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
                redirect('Corruption/admin_management');
                return;
            }

            // อัปเดตจำนวนการดู
            $this->corruption_model->update_view_count($report_detail->corruption_id);

            // บันทึกการติดตาม
            $this->corruption_model->log_corruption_tracking(
                $report_detail->corruption_id,
                'viewed',
                ['action' => 'staff_view_detail'],
                ['user_id' => $m_id, 'user_type' => 'staff']
            );

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // ตรวจสอบสิทธิ์
            $data['can_edit'] = $this->check_corruption_edit_permission($staff_check);
            $data['can_delete'] = $this->check_corruption_delete_permission($staff_check);
            $data['can_assign'] = $this->check_corruption_assign_permission($staff_check);

            // ข้อมูลรายงาน
            $data['report_detail'] = $report_detail;

            // ข้อมูลตัวเลือก
            $data['status_options'] = $this->get_status_options();
            $data['priority_options'] = $this->get_priority_options();
            $data['staff_options'] = $this->corruption_model->get_assignable_staff();

            // ข้อมูลเจ้าหน้าที่
            $user_info_object = $this->create_complete_user_info($staff_check);
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;
            $data['current_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Dashboard')],
                ['title' => 'จัดการรายงานการทุจริต', 'url' => site_url('Corruption/admin_management')],
                ['title' => 'รายละเอียด #' . $report_id, 'url' => '']
            ];

            $data['page_title'] = 'รายละเอียดรายงาน #' . $report_id;

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/corruption_detail', $data); // หน้านี้ที่เราสร้าง
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in corruption report_detail: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ารายละเอียด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Corruption/admin_management');
            }
        }
    }



    // ===================================================================
    // *** การจัดการไฟล์หลักฐาน ***
    // ===================================================================

    /**
     * ดาวน์โหลดไฟล์หลักฐาน
     */
    public function download_evidence($file_id = null)
    {
        try {
            if (empty($file_id)) {
                show_404();
                return;
            }

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_simple();

            // ดึงข้อมูลไฟล์
            $file = $this->corruption_model->get_corruption_file_by_id($file_id);

            if (!$file) {
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึง
            $can_access = false;

            if ($current_user['user_type'] === 'staff') {
                // เจ้าหน้าที่สามารถดาวน์โหลดได้
                $can_access = $this->check_corruption_management_permission($this->get_staff_data($current_user['user_id']));
            } elseif ($current_user['user_type'] === 'public') {
                // สมาชิกสามารถดาวน์โหลดไฟล์ของตนเองได้
                $report = $this->corruption_model->get_corruption_report_by_id($file->corruption_id);
                $can_access = ($report && $report->reporter_user_id == $current_user['user_id'] && $report->reporter_user_type === 'public');
            }

            if (!$can_access) {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file->file_path)) {
                show_404();
                return;
            }

            // บันทึกการดาวน์โหลด
            $this->corruption_model->log_corruption_tracking(
                $file->corruption_id,
                'downloaded',
                ['file_id' => $file_id, 'file_name' => $file->file_original_name],
                $current_user
            );

            // ส่งไฟล์
            $this->load->helper('download');
            force_download($file->file_original_name, file_get_contents($file->file_path));

        } catch (Exception $e) {
            log_message('error', 'Error downloading evidence file: ' . $e->getMessage());
            show_404();
        }
    }

    /**
     * ลบไฟล์หลักฐาน (AJAX)
     */
    public function delete_evidence()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            $file_id = $this->input->post('file_id');

            if (empty($file_id)) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสไฟล์']);
                exit;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_corruption_delete_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบไฟล์']);
                exit;
            }

            // ดึงข้อมูลไฟล์
            $file = $this->corruption_model->get_corruption_file_by_id($file_id);
            if (!$file) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบไฟล์']);
                exit;
            }

            $deleted_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // ลบไฟล์
            $result = $this->corruption_model->delete_corruption_file($file_id, $deleted_by);

            if ($result) {
                // ลบไฟล์จริงจากระบบ
                if (file_exists($file->file_path)) {
                    unlink($file->file_path);
                }

                // บันทึกประวัติ
                $this->corruption_model->add_corruption_history(
                    $file->corruption_id,
                    'evidence_removed',
                    "ลบไฟล์หลักฐาน: {$file->file_original_name}",
                    $deleted_by,
                    $m_id
                );

                echo json_encode([
                    'success' => true,
                    'message' => 'ลบไฟล์สำเร็จ',
                    'file_name' => $file->file_original_name
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบไฟล์ได้']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error deleting evidence file: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }

    // ===================================================================
    // *** หน้ารายงานของสมาชิก ***
    // ===================================================================

    /**
     * หน้ารายงานการทุจริตของสมาชิก
     */
    public function my_reports()
    {
        try {
            log_message('info', '=== MY REPORTS START ===');

            // ตรวจสอบการ login ของสมาชิก
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            log_message('info', "Session data - mp_id: {$mp_id}, mp_email: {$mp_email}");

            if (!$mp_id || !$mp_email) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบเพื่อดูรายงานของคุณ');
                redirect('User');
                return;
            }

            // ตรวจสอบข้อมูลสมาชิก
            $this->db->select('id, mp_id, mp_fname, mp_lname, mp_prefix, mp_email, mp_phone, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $member_check = $this->db->get()->row();

            if (!$member_check) {
                log_message('error', "Member not found - mp_id: {$mp_id}, mp_email: {$mp_email}");
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน');
                redirect('User');
                return;
            }

            log_message('info', "Member found - ID: {$member_check->id}, Name: {$member_check->mp_fname} {$member_check->mp_lname}");

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data_safe();

            // ตัวกรองข้อมูล
            $filters = [
                'status' => $this->input->get('status'),
                'corruption_type' => $this->input->get('corruption_type'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search')
            ];

            log_message('info', 'Filters: ' . json_encode($filters));

            // *** แก้ไขส่วนนี้ - ใช้ method ใหม่ที่ปรับปรุงแล้ว ***
            $reports = $this->get_member_reports_improved($member_check->id, $filters);

            log_message('info', 'Reports found: ' . count($reports));

            // คำนวณสถิติรายงาน
            $report_stats = $this->calculate_member_stats($reports);

            // เตรียมข้อมูลสมาชิก
            $member_info = [
                'id' => $member_check->id,
                'mp_id' => $member_check->mp_id,
                'name' => trim(($member_check->mp_prefix ? $member_check->mp_prefix . ' ' : '') .
                    $member_check->mp_fname . ' ' . $member_check->mp_lname),
                'email' => $member_check->mp_email,
                'phone' => $member_check->mp_phone,
                'profile_img' => $member_check->profile_img ?? ''
            ];

            // ตัวเลือกสำหรับ dropdown
            $filter_options = [
                'status_options' => $this->get_status_options(),
                'corruption_type_options' => $this->get_corruption_types()
            ];

            // รวมข้อมูลทั้งหมด
            $data = array_merge($data, [
                'reports' => $reports,
                'report_stats' => $report_stats,
                'filters' => $filters,
                'filter_options' => $filter_options,
                'member_info' => $member_info,
                'is_logged_in' => true,
                'user_type' => 'public',
                'user_info' => (object) $member_info
            ]);

            // Page metadata
            $data['page_title'] = 'รายงานการทุจริตของฉัน';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'รายงานการทุจริตของฉัน', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            log_message('info', '=== MY REPORTS SUCCESS ===');

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/corruption_my_reports', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in corruption my_reports: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('User');
            }
        }
    }



    private function get_member_reports_improved($member_id, $filters = [])
    {
        try {
            log_message('info', "Getting reports for member ID: {$member_id}");

            // ตรวจสอบว่า table มีอยู่จริง
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return [];
            }

            // ตรวจสอบว่ามีข้อมูลสมาชิกคนนี้จริงหรือไม่
            $member_exists = $this->db->where('id', $member_id)
                ->where('mp_status', 1)
                ->count_all_results('tbl_member_public');

            if ($member_exists == 0) {
                log_message('error', "Member ID {$member_id} not found or inactive");
                return [];
            }

            // Query หาข้อมูลรายงาน
            $this->db->select('cr.*, 
                          (SELECT COUNT(*) FROM tbl_corruption_files cf 
                           WHERE cf.corruption_id = cr.corruption_id 
                           AND cf.file_status = "active") as file_count');
            $this->db->from('tbl_corruption_reports cr');

            // เงื่อนไขหลัก - ต้องเป็นรายงานของสมาชิกคนนี้
            $this->db->where('cr.reporter_user_id', $member_id);
            $this->db->where('cr.reporter_user_type', 'public');

            // ไม่เอารายงานที่ถูก archive
            $this->db->where('(cr.is_archived IS NULL OR cr.is_archived = 0)');

            // เพิ่ม filters
            if (!empty($filters['status'])) {
                $this->db->where('cr.report_status', $filters['status']);
                log_message('info', "Filter by status: {$filters['status']}");
            }

            if (!empty($filters['corruption_type'])) {
                $this->db->where('cr.corruption_type', $filters['corruption_type']);
                log_message('info', "Filter by type: {$filters['corruption_type']}");
            }

            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(cr.created_at) >=', $filters['date_from']);
                log_message('info', "Filter from date: {$filters['date_from']}");
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(cr.created_at) <=', $filters['date_to']);
                log_message('info', "Filter to date: {$filters['date_to']}");
            }

            if (!empty($filters['search'])) {
                $search_term = '%' . $this->db->escape_like_str($filters['search']) . '%';
                $this->db->group_start();
                $this->db->like('cr.corruption_report_id', $filters['search']);
                $this->db->or_like('cr.complaint_subject', $filters['search']);
                $this->db->or_like('cr.complaint_details', $filters['search']);
                $this->db->group_end();
                log_message('info', "Filter by search: {$filters['search']}");
            }

            $this->db->order_by('cr.created_at', 'DESC');

            $query = $this->db->get();

            if (!$query) {
                log_message('error', 'Query failed: ' . $this->db->last_query());
                log_message('error', 'Database error: ' . json_encode($this->db->error()));
                return [];
            }

            $reports = $query->result();

            log_message('info', 'SQL Query: ' . $this->db->last_query());
            log_message('info', 'Query executed successfully, found ' . count($reports) . ' reports');

            // ประมวลผลข้อมูลรายงาน
            foreach ($reports as &$report) {
                // จัดการชื่อผู้แจ้ง
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }

                // เพิ่มข้อมูลเสริม
                $report->days_ago = $this->calculate_days_ago($report->created_at);
                $report->status_label = $this->get_status_label($report->report_status);
                $report->type_label = $this->get_corruption_type_label($report->corruption_type);
            }

            return $reports;

        } catch (Exception $e) {
            log_message('error', 'Error in get_member_reports_improved: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }


    private function calculate_days_ago($created_at)
    {
        try {
            $created_date = new DateTime($created_at);
            $current_date = new DateTime();
            $interval = $current_date->diff($created_date);
            return $interval->days;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function get_status_label($status)
    {
        $labels = [
            'pending' => 'รอดำเนินการ',
            'under_review' => 'กำลังตรวจสอบ',
            'investigating' => 'กำลังสอบสวน',
            'resolved' => 'แก้ไขแล้ว',
            'dismissed' => 'ไม่อนุมัติ',
            'closed' => 'ปิดเรื่อง'
        ];

        return $labels[$status] ?? $status;
    }

    private function get_corruption_type_label($type)
    {
        $labels = [
            'embezzlement' => 'การยักยอกเงิน',
            'bribery' => 'การรับสินบน',
            'abuse_of_power' => 'การใช้อำนาจหน้าที่มิชอบ',
            'conflict_of_interest' => 'ความขัดแย้งทางผลประโยชน์',
            'procurement_fraud' => 'การทุจริตในการจัดซื้อจัดจ้าง',
            'other' => 'อื่นๆ'
        ];

        return $labels[$type] ?? $type;
    }



    private function calculate_member_stats($reports)
    {
        $stats = [
            'total' => count($reports),
            'pending' => 0,
            'in_progress' => 0,
            'resolved' => 0
        ];

        foreach ($reports as $report) {
            switch ($report->report_status) {
                case 'pending':
                    $stats['pending']++;
                    break;
                case 'under_review':
                case 'investigating':
                    $stats['in_progress']++;
                    break;
                case 'resolved':
                case 'dismissed':
                case 'closed':
                    $stats['resolved']++;
                    break;
            }
        }

        return $stats;
    }




    private function get_member_reports_direct($member_id, $filters = [])
    {
        try {
            log_message('info', "Getting reports for member ID: {$member_id}");

            // ตรวจสอบว่า table มีอยู่จริง
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return [];
            }

            // สร้าง SQL query
            $sql = "SELECT cr.*, 
                       (SELECT COUNT(*) FROM tbl_corruption_files cf 
                        WHERE cf.corruption_id = cr.corruption_id AND cf.file_status = 'active') as file_count
                FROM tbl_corruption_reports cr 
                WHERE cr.reporter_user_id = ? 
                AND cr.reporter_user_type = 'public' 
                AND (cr.is_archived IS NULL OR cr.is_archived = 0)";

            $params = [$member_id];

            // เพิ่ม filters
            if (!empty($filters['status'])) {
                $sql .= " AND cr.report_status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['corruption_type'])) {
                $sql .= " AND cr.corruption_type = ?";
                $params[] = $filters['corruption_type'];
            }

            if (!empty($filters['date_from'])) {
                $sql .= " AND DATE(cr.created_at) >= ?";
                $params[] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $sql .= " AND DATE(cr.created_at) <= ?";
                $params[] = $filters['date_to'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (cr.corruption_report_id LIKE ? OR cr.complaint_subject LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params[] = $search_term;
                $params[] = $search_term;
            }

            $sql .= " ORDER BY cr.created_at DESC";

            log_message('info', 'SQL Query: ' . $sql);
            log_message('info', 'Parameters: ' . json_encode($params));

            $query = $this->db->query($sql, $params);

            if (!$query) {
                log_message('error', 'Query failed: ' . $this->db->last_query());
                log_message('error', 'Database error: ' . json_encode($this->db->error()));
                return [];
            }

            $reports = $query->result();

            log_message('info', 'Query executed successfully, found ' . count($reports) . ' reports');

            return $reports;

        } catch (Exception $e) {
            log_message('error', 'Error in get_member_reports_direct: ' . $e->getMessage());
            return [];
        }
    }



    /**
     * รายละเอียดรายงานของสมาชิก
     */
    public function my_report_detail($report_id = null)
    {
        try {
            // ตรวจสอบการ login ของสมาชิก
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!$mp_id || !$mp_email) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบเพื่อดูรายงานของคุณ');
                redirect('User');
                return;
            }

            if (empty($report_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขรายงาน');
                redirect('Corruption/my_reports');
                return;
            }

            // ตรวจสอบข้อมูลสมาชิก
            $this->db->select('id, mp_fname, mp_lname, mp_email, mp_phone, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $member_check = $this->db->get()->row();

            if (!$member_check) {
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน');
                redirect('User');
                return;
            }

            // ดึงข้อมูลรายงาน
            $report_detail = $this->corruption_model->get_corruption_report_by_report_id($report_id);

            if (!$report_detail) {
                $this->session->set_flashdata('error_message', 'ไม่พบรายงานที่ระบุ');
                redirect('Corruption/my_reports');
                return;
            }

            // ตรวจสอบสิทธิ์ - ต้องเป็นเจ้าของรายงาน
            if ($report_detail->reporter_user_id != $member_check->id || $report_detail->reporter_user_type !== 'public') {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
                redirect('Corruption/my_reports');
                return;
            }

            // อัปเดตจำนวนการดู
            $this->corruption_model->update_view_count($report_detail->corruption_id);

            // บันทึกการติดตาม
            $this->corruption_model->log_corruption_tracking(
                $report_detail->corruption_id,
                'viewed',
                ['action' => 'member_view_detail'],
                ['user_id' => $member_check->id, 'user_type' => 'public']
            );

            // เตรียมข้อมูล
            $data = $this->prepare_navbar_data_safe();

            $data = array_merge($data, [
                'report_detail' => $report_detail,
                'member_info' => [
                    'id' => $member_check->id,
                    'name' => trim($member_check->mp_fname . ' ' . $member_check->mp_lname),
                    'email' => $member_check->mp_email,
                    'phone' => $member_check->mp_phone
                ],
                'is_logged_in' => true,
                'user_type' => 'public',
                'user_info' => (object) [
                    'id' => $member_check->id,
                    'name' => trim($member_check->mp_fname . ' ' . $member_check->mp_lname),
                    'email' => $member_check->mp_email,
                    'phone' => $member_check->mp_phone
                ]
            ]);

            $data['page_title'] = 'รายละเอียดรายงาน #' . $report_id;
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'รายงานการทุจริตของฉัน', 'url' => site_url('Corruption/my_reports')],
                ['title' => 'รายละเอียด #' . $report_id, 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/corruption_my_report_detail', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in corruption my_report_detail: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ารายละเอียด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Corruption/my_reports');
            }
        }
    }

    // ===================================================================
    // *** API และ AJAX Functions ***
    // ===================================================================

    /**
     * API สถิติการทุจริต
     */
    public function api_statistics()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบการเข้าสู่ระบบ (สำหรับ Admin)
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้รับสิทธิ์',
                    'corruption_statistics' => $this->get_default_corruption_stats()
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // คำนวณสถิติการทุจริต
            $stats = $this->corruption_model->get_corruption_statistics();
            $monthly_stats = $this->corruption_model->get_monthly_corruption_statistics(12);
            $urgent_reports = $this->corruption_model->get_urgent_corruption_reports();

            echo json_encode([
                'success' => true,
                'corruption_statistics' => $stats,
                'monthly_statistics' => $monthly_stats,
                'urgent_reports' => $urgent_reports,
                'last_updated' => date('Y-m-d H:i:s'),
                'server_time' => date('c')
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in api_statistics: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล',
                'corruption_statistics' => $this->get_default_corruption_stats(),
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    // ===================================================================
    // *** Helper Functions ***
    // ===================================================================

    /**
     * เตรียมข้อมูลสำหรับ navbar
     */
    private function prepare_navbar_data_safe()
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

            if (isset($this->news_model) && method_exists($this->news_model, 'news_frontend')) {
                $result = $this->news_model->news_frontend();
                $data['qNews'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->HotNews_model) && method_exists($this->HotNews_model, 'hotnews_frontend')) {
                $result = $this->HotNews_model->hotnews_frontend();
                $data['qHotnews'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->Weather_report_model) && method_exists($this->Weather_report_model, 'weather_reports_frontend')) {
                $result = $this->Weather_report_model->weather_reports_frontend();
                $data['qWeather'] = (is_array($result) || is_object($result)) ? $result : [];
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }



    public function check_login_status()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            $current_user = $this->get_current_user_for_corruption_report();

            $response = [
                'success' => true,
                'is_logged_in' => $current_user['is_logged_in'],
                'user_type' => $current_user['user_type'],
                'access_denied' => $current_user['user_type'] === 'staff',
                'user_info' => null,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // ส่งข้อมูลผู้ใช้ (ไม่รวมข้อมูลละเอียดอ่อน)
            if ($current_user['is_logged_in'] && $current_user['user_type'] === 'public') {
                $response['user_info'] = [
                    'name' => $current_user['user_info']['name'],
                    'phone' => $current_user['user_info']['phone'],
                    'email' => $current_user['user_info']['email'],
                    'position' => $current_user['user_info']['position']
                ];
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error checking login status: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ',
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal error'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }






    private function prepare_navbar_data()
    {
        return $this->prepare_navbar_data_safe();
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
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');
            $m_id = $this->session->userdata('m_id');

            // ตรวจสอบ public user ก่อน
            if (!empty($mp_id) && !empty($mp_email)) {
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_status');
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
                        'phone' => $user_data->mp_phone,
                        'email' => $user_data->mp_email
                    ];

                    if (!empty($user_data->mp_address)) {
                        $user_info['user_address'] = [
                            'full_address' => trim($user_data->mp_address . ' ' . $user_data->mp_district . ' ' . $user_data->mp_amphoe . ' ' . $user_data->mp_province . ' ' . $user_data->mp_zipcode)
                        ];
                    }

                    return $user_info;
                }
            }

            // ตรวจสอบ staff user
            if (!empty($m_id)) {
                $this->db->select('m.m_id, m.m_email, m.m_fname, m.m_lname, m.m_phone, m.m_system, m.m_status, COALESCE(p.pname, "เจ้าหน้าที่") as pname');
                $this->db->from('tbl_member m');
                $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
                $this->db->where('m.m_id', $m_id);
                $this->db->where('m.m_status', '1');
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_info'] = [
                        'id' => $user_data->m_id,
                        'name' => trim($user_data->m_fname . ' ' . $user_data->m_lname),
                        'phone' => $user_data->m_phone,
                        'email' => $user_data->m_email,
                        'm_system' => $user_data->m_system,
                        'pname' => $user_data->pname
                    ];

                    return $user_info;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error in get_current_user_detailed: ' . $e->getMessage());
        }

        return $user_info;
    }

    /**
     * ดึงข้อมูล user แบบง่าย
     */
    private function get_current_user_simple()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_id' => null,
            'name' => 'Guest User',
            'email' => '',
            'phone' => '',
            'address' => ''
        ];

        try {
            // Check public user
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!empty($mp_id) && !empty($mp_email)) {
                $this->db->select('id, mp_fname, mp_lname, mp_email, mp_phone, mp_address');
                $this->db->where('mp_id', $mp_id);
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $user_data = $this->db->get('tbl_member_public')->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_id'] = $user_data->id;
                    $user_info['name'] = trim($user_data->mp_fname . ' ' . $user_data->mp_lname);
                    $user_info['email'] = $user_data->mp_email;
                    $user_info['phone'] = $user_data->mp_phone;
                    $user_info['address'] = $user_data->mp_address;

                    return $user_info;
                }
            }

            // Check staff user
            $m_id = $this->session->userdata('m_id');

            if (!empty($m_id)) {
                $this->db->select('m_id, m_fname, m_lname, m_email, m_phone');
                $this->db->where('m_id', $m_id);
                $this->db->where('m_status', '1');
                $user_data = $this->db->get('tbl_member')->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_id'] = $user_data->m_id;
                    $user_info['name'] = trim($user_data->m_fname . ' ' . $user_data->m_lname);
                    $user_info['email'] = $user_data->m_email;
                    $user_info['phone'] = $user_data->m_phone;
                    $user_info['address'] = 'เจ้าหน้าที่';

                    return $user_info;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting current user: ' . $e->getMessage());
        }

        // If not logged in, get from POST data
        if (!$user_info['is_logged_in']) {
            $user_info['name'] = $this->input->post('reporter_name') ?: 'Guest User';
            $user_info['email'] = $this->input->post('reporter_email') ?: 'guest@example.com';
            $user_info['phone'] = $this->input->post('reporter_phone') ?: '0000000000';
            $user_info['address'] = $this->input->post('reporter_address') ?: 'ไม่ระบุ';
        }

        return $user_info;
    }

    /**
     * รับข้อมูลจากฟอร์ม
     */
    private function get_form_data()
    {
        return [
            'corruption_type' => $this->input->post('corruption_type'),
            'corruption_type_other' => $this->input->post('corruption_type_other'),
            'complaint_subject' => trim($this->input->post('complaint_subject')),
            'complaint_details' => trim($this->input->post('complaint_details')),
            'incident_date' => $this->input->post('incident_date'),
            'incident_time' => $this->input->post('incident_time'),
            'incident_location' => trim($this->input->post('incident_location')),
            'perpetrator_name' => trim($this->input->post('perpetrator_name')),
            'perpetrator_department' => trim($this->input->post('perpetrator_department')),
            'perpetrator_position' => trim($this->input->post('perpetrator_position')),
            'other_involved' => trim($this->input->post('other_involved')),
            'evidence_description' => trim($this->input->post('evidence_description')),
            'is_anonymous' => $this->input->post('is_anonymous') === '1',
            'reporter_name' => trim($this->input->post('reporter_name')),
            'reporter_phone' => trim($this->input->post('reporter_phone')),
            'reporter_email' => trim($this->input->post('reporter_email')),
            'reporter_position' => trim($this->input->post('reporter_position')),
            'reporter_relation' => $this->input->post('reporter_relation')
        ];
    }

    /**
     * Validate ข้อมูลรายงานการทุจริต
     */



    /**
     * เตรียมข้อมูลสำหรับบันทึก
     */
    private function prepare_report_data($form_data, $current_user)
    {
        $report_data = [
            'corruption_type' => $form_data['corruption_type'],
            'corruption_type_other' => $form_data['corruption_type_other'],
            'complaint_subject' => $form_data['complaint_subject'],
            'complaint_details' => $form_data['complaint_details'],
            'incident_date' => $form_data['incident_date'],
            'incident_time' => $form_data['incident_time'],
            'incident_location' => $form_data['incident_location'],
            'perpetrator_name' => $form_data['perpetrator_name'],
            'perpetrator_department' => $form_data['perpetrator_department'],
            'perpetrator_position' => $form_data['perpetrator_position'],
            'other_involved' => $form_data['other_involved'],
            'evidence_description' => $form_data['evidence_description'],
            'evidence_file_count' => 0, // จะอัปเดตหลังจากอัปโหลดไฟล์
            'is_anonymous' => $form_data['is_anonymous'],
            'reporter_name' => $form_data['is_anonymous'] ? null : $form_data['reporter_name'],
            'reporter_phone' => $form_data['is_anonymous'] ? null : $form_data['reporter_phone'],
            'reporter_email' => $form_data['is_anonymous'] ? null : $form_data['reporter_email'],
            'reporter_position' => $form_data['is_anonymous'] ? null : $form_data['reporter_position'],
            'reporter_relation' => $form_data['is_anonymous'] ? null : $form_data['reporter_relation'],
            'reporter_user_id' => $current_user['is_logged_in'] ? $current_user['user_id'] : null,
            'reporter_user_type' => $current_user['user_type'],
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_by' => $current_user['name']
        ];

        return $report_data;
    }

    /**
     * จัดการไฟล์หลักฐาน
     */
    private function handle_evidence_files($corruption_id)
    {
        try {
            $upload_path = './docs/corruption_evidence/';

            // สร้างโฟลเดอร์ถ้าไม่มี
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            if (empty($_FILES['evidence_files']['name'][0])) {
                return ['success' => false, 'message' => 'ไม่มีไฟล์ที่ต้องการอัปโหลด', 'count' => 0];
            }

            $files_info = [];
            $file_count = count($_FILES['evidence_files']['name']);
            $max_files = 10; // สูงสุด 10 ไฟล์
            $max_individual_size = 10485760; // 10MB
            $max_total_size = $max_individual_size * $max_files;

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];

            // ตรวจสอบจำนวนไฟล์
            if ($file_count > $max_files) {
                return ['success' => false, 'message' => "สามารถอัปโหลดได้ไม่เกิน {$max_files} ไฟล์", 'count' => 0];
            }

            $total_size = 0;

            for ($i = 0; $i < $file_count; $i++) {
                // ข้ามไฟล์ว่าง
                if (empty($_FILES['evidence_files']['name'][$i])) {
                    continue;
                }

                // ตรวจสอบ error
                if ($_FILES['evidence_files']['error'][$i] !== UPLOAD_ERR_OK) {
                    log_message('error', "File upload error for file {$i}: " . $_FILES['evidence_files']['error'][$i]);
                    continue;
                }

                $file_name = $_FILES['evidence_files']['name'][$i];
                $file_tmp = $_FILES['evidence_files']['tmp_name'][$i];
                $file_size = $_FILES['evidence_files']['size'][$i];
                $file_type = $_FILES['evidence_files']['type'][$i];

                // ตรวจสอบขนาดไฟล์
                if ($file_size > $max_individual_size) {
                    log_message('error', "File too large: {$file_name} ({$file_size} bytes)");
                    continue;
                }

                $total_size += $file_size;
                if ($total_size > $max_total_size) {
                    log_message('error', 'Total file size exceeds limit');
                    break;
                }

                // ตรวจสอบนามสกุลไฟล์
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_types)) {
                    log_message('error', "Invalid file type: {$file_extension}");
                    continue;
                }

                // สร้างชื่อไฟล์ใหม่
                $new_filename = 'COR_' . $corruption_id . '_' . ($i + 1) . '_' . time() . '.' . $file_extension;
                $target_path = $upload_path . $new_filename;

                // อัปโหลดไฟล์
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $file_data = [
                        'corruption_id' => $corruption_id,
                        'file_name' => $new_filename,
                        'file_original_name' => $file_name,
                        'file_path' => $target_path,
                        'file_size' => $file_size,
                        'file_type' => $file_type,
                        'file_extension' => $file_extension,
                        'file_order' => $i + 1,
                        'is_main_evidence' => ($i === 0) ? 1 : 0,
                        'uploaded_by' => 'System'
                    ];

                    // บันทึกข้อมูลไฟล์
                    if (method_exists($this->corruption_model, 'add_corruption_file')) {
                        $file_id = $this->corruption_model->add_corruption_file($corruption_id, $file_data);

                        if ($file_id) {
                            $files_info[] = $file_data;
                            log_message('info', "Evidence file uploaded: {$file_name} -> {$new_filename}");
                        }
                    }
                } else {
                    log_message('error', "Failed to move uploaded file: {$file_name}");
                }
            }

            if (empty($files_info)) {
                return ['success' => false, 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้', 'count' => 0];
            }

            return [
                'success' => true,
                'message' => "อัปโหลดไฟล์สำเร็จ " . count($files_info) . " ไฟล์",
                'count' => count($files_info),
                'files' => $files_info
            ];

        } catch (Exception $e) {
            log_message('error', 'Error handling evidence files: ' . $e->getMessage());
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์', 'count' => 0];
        }
    }


    private function json_response($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }




    private function get_default_corruption_stats()
    {
        return [
            'total_reports' => 0,
            'pending_reports' => 0,
            'investigating_reports' => 0,
            'resolved_reports' => 0,
            'this_month_reports' => 0
        ];
    }




    private function create_corruption_notifications($corruption_id, $report, $current_user)
    {
        try {
            // สร้างการแจ้งเตือนสำหรับเจ้าหน้าที่ (ถ้ามี model สำหรับ notification)
            if (method_exists($this->corruption_model, 'create_corruption_notification')) {
                $staff_notification = [
                    'type' => 'new_report',
                    'title' => 'รายงานการทุจริตใหม่',
                    'message' => "มีการยื่นรายงานการทุจริตใหม่: {$report->complaint_subject}" .
                        ($report->is_anonymous ? ' (ไม่ระบุตัวตน)' : " โดย {$report->reporter_name}"),
                    'target_role' => 'admin',
                    'priority' => 'high'
                ];

                $this->corruption_model->create_corruption_notification($corruption_id, $staff_notification);
            }

            log_message('info', "Corruption notifications created for report ID: {$corruption_id}");

        } catch (Exception $e) {
            log_message('error', 'Corruption notification creation failed: ' . $e->getMessage());
            throw $e;
        }
    }





    private function log_tracking($corruption_id, $action, $details = [], $user_info = [])
    {
        try {
            if (method_exists($this->corruption_model, 'log_corruption_tracking')) {
                $this->corruption_model->log_corruption_tracking(
                    $corruption_id,
                    $action,
                    $details,
                    $user_info
                );
            }
        } catch (Exception $e) {
            log_message('error', 'Error logging tracking: ' . $e->getMessage());
        }
    }






    /**
     * ค้นหารายงาน
     */
    private function perform_report_search($report_id)
    {
        $result = [
            'search_performed' => true,
            'corruption_report_info' => null,
            'report_id' => $report_id,
            'error_message' => ''
        ];

        try {
            log_message('info', "Starting report search for ID: {$report_id}");

            // ตรวจสอบรูปแบบ report ID
            if (!preg_match('/^COR\d+$/', $report_id)) {
                $result['error_message'] = 'รูปแบบหมายเลขรายงานไม่ถูกต้อง กรุณาใช้รูปแบบ COR ตามด้วยตัวเลข';
                return $result;
            }

            // ตรวจสอบสถานะผู้ใช้ปัจจุบัน
            $current_user = $this->get_current_user_for_corruption_report();
            log_message('info', "Current user type: {$current_user['user_type']}, logged in: " .
                ($current_user['is_logged_in'] ? 'yes' : 'no'));

            // ค้นหารายงานจากฐานข้อมูล
            $report = $this->corruption_model->get_corruption_report_by_report_id($report_id);

            if (!$report) {
                log_message('info', "Report not found: {$report_id}");
                $result['error_message'] = 'ไม่พบหมายเลขรายงานในระบบ';
                return $result;
            }

            log_message('info', "Report found - ID: {$report->corruption_id}, " .
                "Reporter type: {$report->reporter_user_type}, " .
                "Reporter ID: {$report->reporter_user_id}, " .
                "Anonymous: {$report->is_anonymous}");

            // *** ตรวจสอบสิทธิ์การเข้าถึงตามประเภทผู้ใช้ ***
            $access_granted = false;
            $access_reason = '';

            if ($current_user['user_type'] === 'staff') {
                // เจ้าหน้าที่สามารถดูได้ทุกรายงาน
                $staff_data = $this->get_staff_data($current_user['user_info']['id']);
                $access_granted = $this->check_corruption_management_permission($staff_data);
                $access_reason = 'staff_access';

            } elseif ($current_user['user_type'] === 'public' && $current_user['is_logged_in']) {
                // สมาชิกที่ login แล้วสามารถดูรายงานของตนเองได้
                if (
                    $report->reporter_user_type === 'public' &&
                    $report->reporter_user_id == $current_user['user_info']['id']
                ) {
                    $access_granted = true;
                    $access_reason = 'owner_access_public';
                } else {
                    $access_granted = false;
                    $access_reason = 'not_owner_public';
                }

            } elseif ($current_user['user_type'] === 'guest' && !$current_user['is_logged_in']) {
                // *** Guest ค้นหาได้เฉพาะรายงานของ Guest เท่านั้น ***
                if ($report->reporter_user_type === 'guest' || $report->reporter_user_type === null) {
                    $access_granted = true;
                    $access_reason = 'guest_access_guest_report';
                } else {
                    // Guest ไม่สามารถดูรายงานของ Public หรือ Staff ได้
                    $access_granted = false;
                    $access_reason = 'guest_cannot_access_public_report';
                }
            } else {
                $access_granted = false;
                $access_reason = 'unknown_user_type';
            }

            log_message('info', "Access check - Granted: " . ($access_granted ? 'yes' : 'no') .
                ", Reason: {$access_reason}");

            if (!$access_granted) {
                if ($access_reason === 'guest_cannot_access_public_report') {
                    $result['error_message'] = 'ไม่พบหมายเลขรายงานในระบบ หรือคุณไม่มีสิทธิ์เข้าถึงรายงานนี้';
                } elseif ($access_reason === 'not_owner_public') {
                    $result['error_message'] = 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้';
                } else {
                    $result['error_message'] = 'ไม่พบหมายเลขรายงานในระบบ';
                }
                return $result;
            }

            // *** จัดการข้อมูลที่จะแสดงตามประเภทผู้ใช้ ***
            if ($current_user['user_type'] === 'guest') {
                // Guest จะเห็นข้อมูลที่จำกัด
                $limited_report = new stdClass();
                $limited_report->corruption_report_id = $report->corruption_report_id;
                $limited_report->complaint_subject = $report->complaint_subject;
                $limited_report->report_status = $report->report_status;
                $limited_report->created_at = $report->created_at;
                $limited_report->updated_at = $report->updated_at;
                $limited_report->corruption_type = $report->corruption_type;
                $limited_report->incident_date = $report->incident_date;
                $limited_report->incident_time = $report->incident_time;
                $limited_report->incident_location = $report->incident_location;
                $limited_report->perpetrator_name = $report->perpetrator_name;
                $limited_report->perpetrator_department = $report->perpetrator_department;
                $limited_report->perpetrator_position = $report->perpetrator_position;

                // ข้อมูลผู้แจ้ง (แสดงตามสิทธิ์)
                if ($report->is_anonymous) {
                    $limited_report->reporter_name = 'ไม่ระบุตัวตน';
                    $limited_report->reporter_phone = null;
                    $limited_report->reporter_email = null;
                } else {
                    $limited_report->reporter_name = $report->reporter_name;
                    $limited_report->reporter_phone = $report->reporter_phone;
                    $limited_report->reporter_email = $report->reporter_email;
                }

                $limited_report->is_anonymous = $report->is_anonymous;
                $limited_report->response_message = $report->response_message;
                $limited_report->response_date = $report->response_date;
                $limited_report->response_by = $report->response_by;

                // ไม่แสดงไฟล์และประวัติสำหรับ guest
                $limited_report->files = [];
                $limited_report->history = [];

                $result['corruption_report_info'] = $limited_report;

            } elseif ($current_user['user_type'] === 'public') {
                // Public user เห็นข้อมูลครบถ้วนของรายงานตนเอง
                if ($report->is_anonymous) {
                    $report->reporter_name = 'ไม่ระบุตัวตน';
                    $report->reporter_phone = 'ไม่ระบุ';
                    $report->reporter_email = 'ไม่ระบุ';
                }
                $result['corruption_report_info'] = $report;

            } else {
                // Staff เห็นข้อมูลครบถ้วนทั้งหมด
                $result['corruption_report_info'] = $report;
            }

            // บันทึกการดู
            $this->corruption_model->update_view_count($report->corruption_id);
            $this->corruption_model->log_corruption_tracking(
                $report->corruption_id,
                'viewed',
                [
                    'action' => 'track_status',
                    'search_type' => $access_reason,
                    'user_type' => $current_user['user_type']
                ],
                $current_user
            );

            log_message('info', "Report search successful - ID: {$report_id}, " .
                "Access: {$access_reason}, User: {$current_user['user_type']}");

        } catch (Exception $e) {
            log_message('error', 'Error in perform_report_search: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $result['error_message'] = 'เกิดข้อผิดพลาดในการค้นหา';
        }

        return $result;
    }



    /**
     * สร้างการแจ้งเตือนการอัปเดตสถานะ
     */
    private function create_status_update_notifications($report, $old_status, $new_status, $updated_by)
    {
        try {
            $status_messages = [
                'under_review' => 'อยู่ระหว่างการตรวจสอบ',
                'investigating' => 'อยู่ระหว่างการสอบสวน',
                'resolved' => 'ดำเนินการเสร็จสิ้น',
                'dismissed' => 'ไม่ดำเนินการ',
                'closed' => 'ปิดเรื่อง'
            ];

            $status_display = $status_messages[$new_status] ?? $new_status;

            // แจ้งเตือนผู้ส่งรายงาน
            if (!empty($report->reporter_user_id) && $report->reporter_user_type === 'public') {
                $user_notification = [
                    'type' => 'status_update',
                    'title' => 'รายงานการทุจริตมีการอัปเดต',
                    'message' => "รายงาน \"{$report->complaint_subject}\" เปลี่ยนสถานะเป็น: {$status_display}",
                    'target_user_id' => $report->reporter_user_id,
                    'target_role' => 'reporter',
                    'priority' => 'normal'
                ];

                $this->corruption_model->create_corruption_notification($report->corruption_id, $user_notification);
            }

        } catch (Exception $e) {
            log_message('error', 'Status update notification creation failed: ' . $e->getMessage());
        }
    }

    /**
     * ตัวเลือกประเภทการทุจริต
     */
    private function get_corruption_types()
    {
        return [
            ['value' => 'embezzlement', 'label' => 'การยักยอกเงิน'],
            ['value' => 'bribery', 'label' => 'การรับสินบน'],
            ['value' => 'abuse_of_power', 'label' => 'การใช้อำนาจหน้าที่มิชอบ'],
            ['value' => 'conflict_of_interest', 'label' => 'ความขัดแย้งทางผลประโยชน์'],
            ['value' => 'procurement_fraud', 'label' => 'การทุจริตในการจัดซื้อจัดจ้าง'],
            ['value' => 'other', 'label' => 'อื่นๆ']
        ];
    }




    /**
     * ตัวเลือกความสัมพันธ์กับเหตุการณ์
     */
    private function get_reporter_relations()
    {
        return [
            ['value' => 'witness', 'label' => 'เป็นผู้พบเห็นเหตุการณ์'],
            ['value' => 'victim', 'label' => 'เป็นผู้เสียหาย'],
            ['value' => 'colleague', 'label' => 'เป็นเพื่อนร่วมงาน'],
            ['value' => 'whistleblower', 'label' => 'เป็นผู้รู้เหตุการณ์'],
            ['value' => 'other', 'label' => 'อื่นๆ']
        ];
    }

    /**
     * ตัวเลือกสถานะ
     */
    private function get_status_options()
    {
        return [
            ['value' => 'pending', 'label' => 'รอดำเนินการ'],
            ['value' => 'under_review', 'label' => 'กำลังตรวจสอบ'],
            ['value' => 'investigating', 'label' => 'กำลังสอบสวน'],
            ['value' => 'resolved', 'label' => 'แก้ไขแล้ว'],
            ['value' => 'dismissed', 'label' => 'ยกเลิก'],
            ['value' => 'closed', 'label' => 'ปิดเรื่อง']
        ];
    }

    /**
     * ตัวเลือกระดับความสำคัญ
     */
    private function get_priority_options()
    {
        return [
            ['value' => 'low', 'label' => 'ต่ำ'],
            ['value' => 'normal', 'label' => 'ปกติ'],
            ['value' => 'high', 'label' => 'สูง'],
            ['value' => 'urgent', 'label' => 'เร่งด่วน']
        ];
    }







    private function check_corruption_edit_permission($staff_data)
    {
        if (!$staff_data)
            return false;

        // ใช้สิทธิ์เดียวกันกับการจัดการ
        return $this->check_corruption_management_permission($staff_data);
    }




    private function check_corruption_delete_permission($staff_data)
    {
        if (!$staff_data)
            return false;

        // เฉพาะ system_admin และ super_admin เท่านั้นที่ลบได้
        return in_array($staff_data->m_system, ['system_admin', 'super_admin']);
    }



    private function check_corruption_assign_permission($staff_data)
    {
        if (!$staff_data)
            return false;

        // system_admin และ super_admin สามารถมอบหมายได้
        if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
            return true;
        }

        // user_admin ที่มีสิทธิ์ 107 สามารถมอบหมายได้
        if ($staff_data->m_system === 'user_admin') {
            return $this->check_corruption_management_permission($staff_data);
        }

        return false;
    }


    private function create_complete_user_info($staff_check)
    {
        if (!$staff_check) {
            return null;
        }

        $user_info = new stdClass();
        $user_info->m_id = $staff_check->m_id;
        $user_info->m_fname = $staff_check->m_fname ?? '';
        $user_info->m_lname = $staff_check->m_lname ?? '';
        $user_info->m_email = $staff_check->m_email ?? '';
        $user_info->m_phone = $staff_check->m_phone ?? '';
        $user_info->m_system = $staff_check->m_system ?? '';
        $user_info->pname = $staff_check->pname ?? 'เจ้าหน้าที่';
        $user_info->full_name = trim(($staff_check->m_fname ?? '') . ' ' . ($staff_check->m_lname ?? ''));
        $user_info->grant_user_ref_id = $staff_check->grant_user_ref_id ?? null;

        return $user_info;
    }




    private function get_staff_data($m_id)
    {
        try {
            if (!$this->db->table_exists('tbl_member')) {
                log_message('error', 'Table tbl_member does not exist');
                return null;
            }

            $this->db->select('m.*, COALESCE(p.pname, "เจ้าหน้าที่") as pname');
            $this->db->from('tbl_member m');
            $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
            $this->db->where('m.m_id', intval($m_id));
            $this->db->where('m.m_status', '1');

            $query = $this->db->get();
            return $query ? $query->row() : null;

        } catch (Exception $e) {
            log_message('error', 'Error getting staff data: ' . $e->getMessage());
            return null;
        }
    }



    private function get_default_stats()
    {
        return [
            'total_reports' => 0,
            'resolved_reports' => 0,
            'this_month_reports' => 0,
            'this_week_reports' => 0,
            'today_reports' => 0,
            'pending_reports' => 0,
            'under_review_reports' => 0,
            'investigating_reports' => 0,
            'anonymous_reports' => 0,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * เพิ่ม method สำหรับตรวจสอบสถานะระบบ
     */
    public function system_status()
    {
        // เฉพาะ development mode
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบการโหลด Model
            if (!isset($this->corruption_model)) {
                $this->load->model('Corruption_model', 'corruption_model');
            }

            $system_status = [
                'database_connected' => $this->db->conn_id ? true : false,
                'model_loaded' => isset($this->corruption_model),
                'table_status' => $this->corruption_model->check_required_tables(),
                'methods_available' => [
                    'count_total_reports' => method_exists($this->corruption_model, 'count_total_reports'),
                    'count_resolved_reports' => method_exists($this->corruption_model, 'count_resolved_reports'),
                    'count_this_month_reports' => method_exists($this->corruption_model, 'count_this_month_reports'),
                    'get_comprehensive_statistics' => method_exists($this->corruption_model, 'get_comprehensive_statistics'),
                    'log_system_error' => method_exists($this->corruption_model, 'log_system_error'),
                    'log_page_access' => method_exists($this->corruption_model, 'log_page_access')
                ],
                'stats_test' => null,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // ทดสอบการทำงานของ stats
            try {
                $system_status['stats_test'] = $this->get_corruption_stats_safe();
            } catch (Exception $e) {
                $system_status['stats_test'] = 'Error: ' . $e->getMessage();
            }

            echo json_encode($system_status, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }


    public function api_corruption_summary()
    {
        // ล้าง output buffer และตั้งค่า JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        try {
            log_message('info', '=== CORRUPTION API SUMMARY START ===');

            // ตรวจสอบการเข้าสู่ระบบและสิทธิ์อย่างปลอดภัย
            $m_id = $this->session->userdata('m_id');

            if (!$m_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ',
                    'corruption_reports' => $this->get_default_corruption_summary_api(),
                    'error_code' => 'NO_PERMISSION'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงข้อมูลเจ้าหน้าที่อย่างปลอดภัย
            $staff_data = $this->get_staff_data_safe($m_id);

            if (!$staff_data) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลเจ้าหน้าที่',
                    'corruption_reports' => $this->get_default_corruption_summary_api(),
                    'error_code' => 'STAFF_NOT_FOUND'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบสิทธิ์การเข้าถึงข้อมูลการทุจริตอย่างปลอดภัย
            if (!$this->check_corruption_permission_safe($staff_data)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลการทุจริต',
                    'corruption_reports' => $this->get_default_corruption_summary_api(),
                    'error_code' => 'NO_CORRUPTION_PERMISSION'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบการเชื่อมต่อฐานข้อมูล
            if (!$this->db->conn_id) {
                log_message('error', 'Database connection failed');
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้',
                    'corruption_reports' => $this->get_default_corruption_summary_api(),
                    'error_code' => 'DB_CONNECTION_ERROR'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบว่า table มีอยู่จริง
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('warning', 'tbl_corruption_reports table does not exist');
                echo json_encode([
                    'success' => true,
                    'message' => 'ระบบยังไม่พร้อมใช้งาน',
                    'corruption_reports' => $this->get_default_corruption_summary_api(),
                    'warning' => 'table_not_exists'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ดึงข้อมูลสถิติการทุจริตอย่างปลอดภัย
            $corruption_summary = $this->get_corruption_summary_direct();

            log_message('info', 'Corruption summary data: ' . json_encode($corruption_summary));

            echo json_encode([
                'success' => true,
                'message' => 'ดึงข้อมูลสำเร็จ',
                'corruption_reports' => $corruption_summary,
                'last_updated' => date('Y-m-d H:i:s'),
                'user_info' => [
                    'staff_id' => $staff_data->m_id ?? 'unknown',
                    'staff_name' => trim(($staff_data->m_fname ?? '') . ' ' . ($staff_data->m_lname ?? '')),
                    'system_role' => $staff_data->m_system ?? 'unknown'
                ]
            ], JSON_UNESCAPED_UNICODE);

            log_message('info', '=== CORRUPTION API SUMMARY SUCCESS ===');

        } catch (Exception $e) {
            log_message('error', 'Error in api_corruption_summary: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล',
                'corruption_reports' => $this->get_default_corruption_summary_api(),
                'error_code' => 'API_ERROR',
                'debug' => ENVIRONMENT === 'development' ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : 'Internal server error'
            ], JSON_UNESCAPED_UNICODE);
        } catch (Error $e) {
            log_message('error', 'PHP Fatal Error in api_corruption_summary: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดร้าแรงในระบบ',
                'corruption_reports' => $this->get_default_corruption_summary_api(),
                'error_code' => 'FATAL_ERROR'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * ดึงข้อมูลสถิติการทุจริตโดยใช้ Direct Database Query
     */
    private function get_corruption_summary_direct()
    {
        try {
            $summary = [
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'closed' => 0
            ];

            // นับจำนวนรายงานทั้งหมด
            try {
                $sql = "SELECT COUNT(*) as total FROM tbl_corruption_reports WHERE (is_archived IS NULL OR is_archived != 1)";
                $query = $this->db->query($sql);

                if ($query && $query->num_rows() > 0) {
                    $summary['total'] = (int) $query->row()->total;
                }
            } catch (Exception $e) {
                log_message('error', 'Error counting total reports: ' . $e->getMessage());
            }

            // นับจำนวนรายงานที่รอดำเนินการ
            try {
                $sql = "SELECT COUNT(*) as pending FROM tbl_corruption_reports 
                        WHERE (is_archived IS NULL OR is_archived != 1) 
                        AND report_status = 'pending'";
                $query = $this->db->query($sql);

                if ($query && $query->num_rows() > 0) {
                    $summary['pending'] = (int) $query->row()->pending;
                }
            } catch (Exception $e) {
                log_message('error', 'Error counting pending reports: ' . $e->getMessage());
            }

            // นับจำนวนรายงานที่กำลังดำเนินการ
            try {
                $sql = "SELECT COUNT(*) as in_progress FROM tbl_corruption_reports 
                        WHERE (is_archived IS NULL OR is_archived != 1) 
                        AND report_status IN ('under_review', 'investigating')";
                $query = $this->db->query($sql);

                if ($query && $query->num_rows() > 0) {
                    $summary['in_progress'] = (int) $query->row()->in_progress;
                }
            } catch (Exception $e) {
                log_message('error', 'Error counting in progress reports: ' . $e->getMessage());
            }

            // นับจำนวนรายงานที่เสร็จสิ้นแล้ว
            try {
                $sql = "SELECT COUNT(*) as closed FROM tbl_corruption_reports 
                        WHERE (is_archived IS NULL OR is_archived != 1) 
                        AND report_status IN ('resolved', 'dismissed', 'closed')";
                $query = $this->db->query($sql);

                if ($query && $query->num_rows() > 0) {
                    $summary['closed'] = (int) $query->row()->closed;
                }
            } catch (Exception $e) {
                log_message('error', 'Error counting closed reports: ' . $e->getMessage());
            }

            log_message('info', 'Corruption summary calculated directly: ' . json_encode($summary));

            return $summary;

        } catch (Exception $e) {
            log_message('error', 'Error calculating corruption summary directly: ' . $e->getMessage());
            return $this->get_default_corruption_summary_api();
        }
    }

    /**
     * ดึงข้อมูลเจ้าหน้าที่อย่างปลอดภัย
     */
    private function get_staff_data_safe($m_id)
    {
        try {
            if (!$this->db->table_exists('tbl_member')) {
                log_message('error', 'Table tbl_member does not exist');
                return null;
            }

            if (!$this->db->table_exists('tbl_position')) {
                log_message('warning', 'Table tbl_position does not exist, using basic query');

                // Query พื้นฐานไม่มี JOIN
                $sql = "SELECT m.*, 'เจ้าหน้าที่' as pname 
                        FROM tbl_member m 
                        WHERE m.m_id = ? AND m.m_status = '1'";

                $query = $this->db->query($sql, [intval($m_id)]);
                return $query ? $query->row() : null;
            }

            // Query แบบมี JOIN
            $sql = "SELECT m.*, COALESCE(p.pname, 'เจ้าหน้าที่') as pname 
                    FROM tbl_member m 
                    LEFT JOIN tbl_position p ON m.ref_pid = p.pid 
                    WHERE m.m_id = ? AND m.m_status = '1'";

            $query = $this->db->query($sql, [intval($m_id)]);
            return $query ? $query->row() : null;

        } catch (Exception $e) {
            log_message('error', 'Error getting staff data safely: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงข้อมูลการทุจริตอย่างปลอดภัย
     */
    private function check_corruption_permission_safe($staff_data)
    {
        try {
            if (!$staff_data) {
                log_message('info', 'STRICT API: Permission DENIED - No staff data');
                return false;
            }

            log_message('info', 'STRICT API: Checking corruption permission for staff ID: ' . $staff_data->m_id);

            // system_admin และ super_admin
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "STRICT API: Permission GRANTED - user is {$staff_data->m_system}");
                return true;
            }

            // user_admin
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('info', 'STRICT API: Permission DENIED - user_admin has empty grant_user_ref_id');
                    return false;
                }

                // แปลง grant_user_ref_id เป็น array
                $grant_ids = explode(',', $staff_data->grant_user_ref_id);
                $grant_ids = array_map('trim', $grant_ids);

                log_message('info', 'STRICT API: Grant IDs: ' . json_encode($grant_ids));

                // 🔒 เช็คเฉพาะว่ามี "107" ใน array
                if (in_array('107', $grant_ids)) {
                    log_message('info', 'STRICT API: Permission GRANTED - Found 107 in grant_user_ref_id array');
                    return true;
                }

                // 🔒 เช็คในฐานข้อมูลเฉพาะ grant_user_id ที่ user มี
                try {
                    if ($this->db->table_exists('tbl_grant_user')) {
                        foreach ($grant_ids as $grant_id) {
                            if (empty($grant_id) || !is_numeric($grant_id))
                                continue;

                            $sql = "SELECT grant_user_id, grant_user_name FROM tbl_grant_user WHERE grant_user_id = ?";
                            $query = $this->db->query($sql, [intval($grant_id)]);

                            if ($query && $query->num_rows() > 0) {
                                $grant_data = $query->row();
                                log_message('info', "STRICT API: Checking grant_user_id {$grant_id}: grant_user_name = {$grant_data->grant_user_name}");

                                // 🔒 เช็คเฉพาะ grant_user_id = 107
                                if ($grant_data->grant_user_id == 107) {
                                    log_message('info', 'STRICT API: Permission GRANTED - User has grant_user_id = 107');
                                    return true;
                                }

                                // 🔒 เช็คจากชื่อ
                                $name_lower = mb_strtolower($grant_data->grant_user_name, 'UTF-8');
                                if (strpos($name_lower, 'ทุจริต') !== false) {
                                    log_message('info', 'STRICT API: Permission GRANTED - User has corruption-related grant');
                                    return true;
                                }
                            }
                        }
                    }

                    log_message('info', 'STRICT API: Permission DENIED - User does not have corruption-related grants');
                    return false;

                } catch (Exception $e) {
                    log_message('error', 'STRICT API: Error checking grant permission: ' . $e->getMessage());

                    // 🔒 Fallback แบบเข้มงวด
                    $has_107 = (strpos($staff_data->grant_user_ref_id, '107') !== false);

                    log_message('info', "STRICT API: Fallback check - grant_user_ref_id contains '107': " . ($has_107 ? 'GRANTED' : 'DENIED'));
                    return $has_107;
                }
            }

            log_message('info', "STRICT API: Permission DENIED - user system: {$staff_data->m_system}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'STRICT API: Error checking corruption permission: ' . $e->getMessage());
            return false;
        }
    }








    /**
     * ข้อมูลเริ่มต้นสำหรับ API
     */
    private function get_default_corruption_summary_api()
    {
        return [
            'total' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'closed' => 0
        ];
    }




    private function process_reports_for_display($reports)
    {
        try {
            $processed_reports = [];

            foreach ($reports as $report) {
                // แปลงสถานะเป็นภาษาไทย
                $status_labels = [
                    'pending' => 'รอดำเนินการ',
                    'under_review' => 'กำลังตรวจสอบ',
                    'investigating' => 'กำลังสอบสวน',
                    'resolved' => 'แก้ไขแล้ว',
                    'dismissed' => 'ยกเลิก',
                    'closed' => 'ปิดเรื่อง'
                ];

                // แปลงประเภทการทุจริตเป็นภาษาไทย
                $type_labels = [
                    'embezzlement' => 'การยักยอกเงิน',
                    'bribery' => 'การรับสินบน',
                    'abuse_of_power' => 'การใช้อำนาจหน้าที่มิชอบ',
                    'conflict_of_interest' => 'ความขัดแย้งทางผลประโยชน์',
                    'procurement_fraud' => 'การทุจริตในการจัดซื้อจัดจ้าง',
                    'other' => 'อื่นๆ'
                ];

                // จัดการชื่อผู้แจ้ง
                if (!isset($report->display_reporter_name)) {
                    if ($report->is_anonymous == 1) {
                        $report->display_reporter_name = 'ไม่ระบุตัวตน';
                    } else {
                        $report->display_reporter_name = $report->reporter_name;
                    }
                }

                // เพิ่มข้อมูลที่แปลงแล้ว
                $report->status_label = $status_labels[$report->report_status] ?? $report->report_status;
                $report->type_label = $type_labels[$report->corruption_type] ?? $report->corruption_type;

                // จัดรูปแบบวันที่
                if (!empty($report->created_at)) {
                    $report->created_at_thai = date('d/m/Y H:i', strtotime($report->created_at));
                } else {
                    $report->created_at_thai = '-';
                }

                if (!empty($report->updated_at) && $report->updated_at != '0000-00-00 00:00:00') {
                    $report->updated_at_thai = date('d/m/Y H:i', strtotime($report->updated_at));
                } else {
                    $report->updated_at_thai = '-';
                }

                // คำนวณจำนวนวันที่ผ่านมา
                $created_date = new DateTime($report->created_at);
                $current_date = new DateTime();
                $interval = $current_date->diff($created_date);
                $report->days_ago = $interval->days;

                // สถานะ CSS class
                $status_classes = [
                    'pending' => 'warning',
                    'under_review' => 'info',
                    'investigating' => 'primary',
                    'resolved' => 'success',
                    'dismissed' => 'secondary',
                    'closed' => 'dark'
                ];
                $report->status_class = $status_classes[$report->report_status] ?? 'secondary';

                // ตัดหัวข้อเรื่องถ้ายาวเกินไป
                if (strlen($report->complaint_subject) > 100) {
                    $report->complaint_subject_short = mb_substr($report->complaint_subject, 0, 97, 'UTF-8') . '...';
                } else {
                    $report->complaint_subject_short = $report->complaint_subject;
                }

                $processed_reports[] = $report;
            }

            return $processed_reports;

        } catch (Exception $e) {
            log_message('error', 'Error processing reports for display: ' . $e->getMessage());
            return $reports; // ส่งคืนข้อมูลเดิมหากเกิดข้อผิดพลาด
        }
    }

    /**
     * คำนวณสถิติรายงานของสมาชิก
     */
    private function calculate_member_report_stats($reports)
    {
        try {
            $stats = [
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'resolved' => 0,
                'this_month' => 0,
                'this_week' => 0
            ];

            if (empty($reports)) {
                return $stats;
            }

            $stats['total'] = count($reports);

            $current_month = date('Y-m');
            $current_week_start = date('Y-m-d', strtotime('monday this week'));

            foreach ($reports as $report) {
                // นับตามสถานะ
                switch ($report->report_status) {
                    case 'pending':
                        $stats['pending']++;
                        break;
                    case 'under_review':
                    case 'investigating':
                        $stats['in_progress']++;
                        break;
                    case 'resolved':
                        $stats['resolved']++;
                        break;
                }

                // นับรายงานในเดือนนี้
                if (!empty($report->created_at)) {
                    $report_month = date('Y-m', strtotime($report->created_at));
                    if ($report_month == $current_month) {
                        $stats['this_month']++;
                    }

                    // นับรายงานในสัปดาห์นี้
                    $report_date = date('Y-m-d', strtotime($report->created_at));
                    if ($report_date >= $current_week_start) {
                        $stats['this_week']++;
                    }
                }
            }

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error calculating member report stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'resolved' => 0,
                'this_month' => 0,
                'this_week' => 0
            ];
        }
    }

    /**
     * ดึงข้อมูลรายงานที่ผู้ใช้สามารถเข้าถึงได้ (สำหรับสมาชิก)
     */
    public function get_user_accessible_reports($user_id, $user_type, $filters = [])
    {
        try {
            $this->db->select('cr.*, 
                          (SELECT COUNT(*) FROM tbl_corruption_files cf 
                           WHERE cf.corruption_id = cr.corruption_id AND cf.file_status = "active") as file_count');
            $this->db->from('tbl_corruption_reports cr');

            // กรองตาม user
            if ($user_type === 'public') {
                $this->db->where('cr.reporter_user_id', $user_id);
                $this->db->where('cr.reporter_user_type', 'public');
            }

            $this->db->where('cr.is_archived', 0);

            // กรองตาม filters อื่นๆ
            if (!empty($filters['status'])) {
                $this->db->where('cr.report_status', $filters['status']);
            }

            if (!empty($filters['corruption_type'])) {
                $this->db->where('cr.corruption_type', $filters['corruption_type']);
            }

            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(cr.created_at) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(cr.created_at) <=', $filters['date_to']);
            }

            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('cr.corruption_report_id', $filters['search']);
                $this->db->or_like('cr.complaint_subject', $filters['search']);
                $this->db->group_end();
            }

            $this->db->order_by('cr.created_at', 'DESC');

            $query = $this->db->get();

            $reports = $query->result();

            // จัดการชื่อผู้แจ้ง
            foreach ($reports as &$report) {
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }
            }

            return [
                'success' => true,
                'data' => $reports,
                'total' => $query->num_rows()
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in get_user_accessible_reports: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'total' => 0,
                'error' => $e->getMessage()
            ];
        }
    }








}

