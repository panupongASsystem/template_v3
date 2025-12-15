<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * API Controller สำหรับจัดการการตรวจสอบการเข้าสู่ระบบ
 * แยกเป็น Controller ต่างหากเพื่อให้ง่ายต่อการบำรุงรักษา
 * รองรับทั้งเจ้าหน้าที่และประชาชน พร้อม 2FA
 */
class Auth_api extends CI_Controller
{
    /**
     * ฐานข้อมูล tenant
     */
    protected $tenant_db;

    /**
     * ข้อมูล tenant ปัจจุบัน
     */
    protected $tenant;

    public function __construct()
    {
        parent::__construct();

        // เริ่มต้น session อย่างปลอดภัย
        if (session_status() == PHP_SESSION_NONE) {
            // ตั้งค่า session path
            $session_path = APPPATH . 'sessions/';
            if (!is_dir($session_path)) {
                mkdir($session_path, 0755, true);
            }
            session_save_path($session_path);

            // ตั้งค่า session
            ini_set('session.gc_maxlifetime', 7200);
            ini_set('session.cookie_lifetime', 7200);

            session_start();
        }

        // โหลด model สำหรับจัดการ tenant
        $this->load->model('Tenant_access_model');

        // ทำความสะอาด tokens
        $this->cleanup_tokens();

        // ตรวจสอบ domain กับ tenant
        $current_domain = $_SERVER['HTTP_HOST'];
        $tenant = $this->Tenant_access_model->get_tenant_by_domain($current_domain);

        if (!$tenant || $tenant->is_active != 1 || $tenant->deleted_at != NULL) {
            $this->tenant_error = true;
            $response = [
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลหน่วยงาน'
            ];
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }

        $this->tenant_error = false;
        $this->tenant = $tenant;

        // โหลดโมเดลที่จำเป็น
        $this->load->model('member_model');
        $this->load->model('member_public_model');
        $this->load->model('user_log_model');
        $this->load->model('System_config_model');

        // โหลด libraries
        $this->load->library('Google2FA');
        $this->load->library('user_agent');
        $this->load->library('session');
        $this->load->library('recaptcha_lib');

        if (file_exists(APPPATH . 'config/recaptcha.php')) {
            $this->load->config('recaptcha');
            $recaptcha_config = $this->config->item('recaptcha');

            if ($recaptcha_config) {
                $this->recaptcha_lib->initialize($recaptcha_config);
                log_message('debug', 'reCAPTCHA Library initialized with config file');
            }
        }

        $this->load->helper('url');
        $this->load->helper('cookie');
    }

    /**
     * ✅ โค้ดที่ 2 ที่เพิ่มฟังก์ชันที่หายไปครบถ้วน - พร้อมใช้แทนที่โค้ดเดิม
     * รักษาลำดับการทำงานเหมือนเดิม + เพิ่ม reCAPTCHA + Enhanced Security
     */
    public function check_login()
    {
        ob_clean();
        $this->output->set_content_type('application/json');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $this->output->set_header('X-Frame-Options: DENY');

        try {
            log_message('info', "=== CITIZEN CHECK_LOGIN START (Enhanced) ===");
            log_message('info', "Request method: " . $_SERVER['REQUEST_METHOD']);
            log_message('info', "POST data: " . json_encode($this->input->post()));
            log_message('info', "IP Address: " . $this->input->ip_address());
            log_message('info', "User Agent: " . $_SERVER['HTTP_USER_AGENT']);

            // ขั้นตอนที่ 1: ตรวจสอบ POST method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                log_message('error', "Invalid request method: " . $_SERVER['REQUEST_METHOD']);
                throw new Exception('ขออภัย เฉพาะการร้องขอผ่าน POST เท่านั้น');
            }

            // ขั้นตอนที่ 2: ตรวจสอบ reCAPTCHA
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $no_recaptcha = $this->input->server('HTTP_X_NO_RECAPTCHA') === 'true';

            log_message('debug', "reCAPTCHA Debug Info:");
            log_message('debug', "- Token received: " . ($recaptcha_token ? 'YES' : 'NO'));
            log_message('debug', "- Token length: " . strlen($recaptcha_token ?: ''));
            log_message('debug', "- No reCAPTCHA bypass: " . ($no_recaptcha ? 'YES' : 'NO'));

            if ($recaptcha_token) {
                log_message('debug', "- Token preview: " . substr($recaptcha_token, 0, 50) . '...');
            }

            if (empty($recaptcha_token) && !$no_recaptcha) {
                log_message('debug', "Citizen login attempted without reCAPTCHA token");
                throw new Exception('กรุณายืนยันตัวตน reCAPTCHA ก่อนเข้าสู่ระบบ');
            }

            if (!empty($recaptcha_token)) {
                log_message('debug', "Starting citizen reCAPTCHA verification with Library...");

                $recaptcha_result = $this->recaptcha_lib->verify_citizen_login($recaptcha_token);

                if (!$recaptcha_result['success']) {
                    log_message('error', "Citizen reCAPTCHA verification failed for IP: " . $this->input->ip_address());
                    log_message('error', "reCAPTCHA Library Error: " . $recaptcha_result['message']);

                    if (isset($recaptcha_result['data']['score'])) {
                        log_message('error', "reCAPTCHA Score: " . $recaptcha_result['data']['score']);
                    }

                    throw new Exception('การยืนยันตัวตน reCAPTCHA ล้มเหลว กรุณารีเฟรชหน้าแล้วลองใหม่อีกครั้ง');
                }

                log_message('info', "✅ Citizen reCAPTCHA verification successful with Library");

                if (isset($recaptcha_result['data']['score'])) {
                    log_message('info', "Citizen reCAPTCHA Score: " . $recaptcha_result['data']['score']);
                }
                if (isset($recaptcha_result['data']['response_time'])) {
                    log_message('info', "Citizen reCAPTCHA Response Time: " . $recaptcha_result['data']['response_time'] . "ms");
                }
            } else {
                log_message('debug', "⚠️ Proceeding without reCAPTCHA verification (bypass enabled)");
            }

            // ขั้นตอนที่ 3: รับข้อมูล input
            $email = $this->input->post('mp_email');
            $password = $this->input->post('mp_password');
            $fingerprint = $this->input->post('fingerprint');

            log_message('debug', "User credentials check:");
            log_message('debug', "- Email: " . ($email ?: 'EMPTY'));
            log_message('debug', "- Password: " . (empty($password) ? 'EMPTY' : 'PROVIDED'));
            log_message('debug', "- Fingerprint: " . ($fingerprint ?: 'NOT PROVIDED'));

            if (empty($fingerprint)) {
                $fingerprint = md5($this->input->ip_address() . $_SERVER['HTTP_USER_AGENT']);
                log_message('debug', "Generated fingerprint: " . $fingerprint);
            }

            // ขั้นตอนที่ 4: ตรวจสอบข้อมูลครบถ้วน
            if (empty($email) || empty($password)) {
                log_message('debug', "Incomplete login data - Email: " . ($email ? 'PROVIDED' : 'MISSING') . ", Password: " . ($password ? 'PROVIDED' : 'MISSING'));
                throw new Exception('กรุณากรอกข้อมูลอีเมลและรหัสผ่าน');
            }

            // ขั้นตอนที่ 5: ตรวจสอบการถูกบล็อค
            log_message('debug', "Checking if user is blocked...");

            try {
                $block_status = $this->check_if_blocked($fingerprint);
                log_message('debug', "Block status result: " . json_encode($block_status));

                if ($block_status['is_blocked']) {
                    log_message('debug', "User is blocked - Level: " . $block_status['block_level'] . ", Remaining time: " . $block_status['remaining_time']);

                    $block_message = 'คุณถูกบล็อคชั่วคราว โปรดรอ';
                    if (isset($block_status['block_level']) && $block_status['block_level'] == 2) {
                        $block_message = 'คุณถูกบล็อคชั่วคราว (ล็อกอินผิดพลาด 6 ครั้ง) โปรดรอ';
                    }

                    $response = [
                        'status' => 'blocked',
                        'message' => $block_message,
                        'remaining_time' => $block_status['remaining_time'],
                        'block_level' => isset($block_status['block_level']) ? $block_status['block_level'] : 1,
                        'csrf_hash' => $this->security->get_csrf_hash() // *** แก้ไข: เพิ่ม CSRF ***
                    ];

                    log_message('info', "Sending blocked response: " . json_encode($response));
                    $this->output->set_output(json_encode($response));
                    return;
                }

                log_message('debug', "User is not blocked, proceeding with login...");

            } catch (Exception $e) {
                log_message('error', "Error checking block status: " . $e->getMessage());
            }

            // ขั้นตอนที่ 6: ตรวจสอบข้อมูลการเข้าสู่ระบบ
            log_message('debug', "Attempting to fetch user login data...");

            try {
                if (!isset($this->member_public_model)) {
                    $this->load->model('member_public_model');
                    log_message('debug', "Loaded member_public_model");
                }

                $result = $this->member_public_model->fetch_user_login(
                    $email,
                    sha1($password)
                );

                log_message('debug', "User fetch result: " . (empty($result) ? 'EMPTY (Invalid credentials)' : 'FOUND'));

                if ($result) {
                    log_message('debug', "User data retrieved:");
                    log_message('debug', "- mp_id: " . $result->mp_id);
                    log_message('debug', "- mp_email: " . $result->mp_email);
                    log_message('debug', "- mp_status: " . ($result->mp_status ?? 'not_set'));
                    log_message('debug', "- google2fa_enabled: " . ($result->google2fa_enabled ?? 'not_set'));
                    log_message('debug', "- has_google2fa_secret: " . (!empty($result->google2fa_secret) ? 'YES' : 'NO'));
                }

            } catch (Exception $e) {
                log_message('error', "Error fetching user data: " . $e->getMessage());
                log_message('error', "Stack trace: " . $e->getTraceAsString());
                throw new Exception('เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล');
            }

            // ขั้นตอนที่ 7: จัดการผลลัพธ์การล็อกอิน
            if (!empty($result)) {
                log_message('info', "Valid user found, processing login...");

                if (isset($result->mp_status) && $result->mp_status == 0) {
                    log_message('debug', "User account is disabled: " . $result->mp_email);
                    throw new Exception('บัญชีนี้ถูกระงับการใช้งาน โปรดติดต่อผู้ให้บริการ');
                }

                // ตรวจสอบ 2FA
                $has_2fa_secret = !empty($result->google2fa_secret);
                $is_2fa_enabled = isset($result->google2fa_enabled) && $result->google2fa_enabled == 1;

                log_message('debug', "2FA Check:");
                log_message('debug', "- Has 2FA Secret: " . ($has_2fa_secret ? 'YES' : 'NO'));
                log_message('debug', "- 2FA Enabled: " . ($is_2fa_enabled ? 'YES' : 'NO'));

                if ($has_2fa_secret && $is_2fa_enabled) {
                    log_message('info', "2FA is required for this user");

                    try {
                        $tenant_id = $this->session->userdata('tenant_id') ?: 1;
                        log_message('debug', "Checking trusted device for user: {$result->mp_id}, tenant: $tenant_id");

                        $is_trusted = false;
                        if (method_exists($this, 'is_trusted_device')) {
                            $is_trusted = $this->is_trusted_device($result->mp_id, $tenant_id, 'public');
                            log_message('debug', "Trusted device check result: " . ($is_trusted ? 'TRUE' : 'FALSE'));
                        } else {
                            log_message('debug', "is_trusted_device method not found - skipping trusted device check");
                        }

                        if ($is_trusted) {
                            log_message('info', "Trusted device found - skipping 2FA");

                            if (method_exists($this, 'update_trusted_device_usage')) {
                                $this->update_trusted_device_usage($result->mp_id, $tenant_id, 'public');
                            }

                            $this->reset_failed_attempts($fingerprint);
                            $this->record_login_attempt($email, 'success', $fingerprint);
                            $this->create_public_session($result, true, true);

                            $response = [
                                'status' => 'success',
                                'message' => 'เข้าสู่ระบบสำเร็จ (Trusted Device)',
                                'redirect' => site_url('Pages/service_systems'),
                                'user_data' => [
                                    'mp_id' => $result->mp_id,
                                    'mp_email' => $result->mp_email,
                                    'mp_fname' => $result->mp_fname,
                                    'mp_lname' => $result->mp_lname
                                ],
                                'csrf_hash' => $this->security->get_csrf_hash() // *** แก้ไข: เพิ่ม CSRF ***
                            ];

                            log_message('info', "Login successful with trusted device bypass");
                        } else {
                            log_message('info', "No trusted device found - 2FA required");

                            $this->session->unset_userdata([
                                'mp_id',
                                'mp_email',
                                'mp_fname',
                                'mp_lname',
                                'mp_img',
                                'mp_phone',
                                'mp_number',
                                'mp_address',
                                'is_public'
                            ]);

                            $this->reset_failed_attempts($fingerprint);
                            $this->record_login_attempt($email, 'success', $fingerprint);

                            $temp_data = [
                                'temp_mp_id' => $result->mp_id,
                                'temp_mp_email' => $result->mp_email,
                                'temp_mp_fname' => $result->mp_fname,
                                'temp_mp_lname' => $result->mp_lname,
                                'temp_mp_img' => $result->mp_img ?? null,
                                'temp_mp_phone' => $result->mp_phone ?? null,
                                'temp_mp_number' => $result->mp_number ?? null,
                                'temp_mp_address' => $result->mp_address ?? null,
                                'temp_tenant_id' => $tenant_id,
                                'temp_google2fa_secret' => $result->google2fa_secret,
                                'temp_login_time' => time(),
                                'temp_user_type' => 'public',
                                'requires_2fa' => true
                            ];
                            $this->session->set_userdata($temp_data);

                            // *** แก้ไข: เพิ่ม CSRF hash ที่ถูกต้อง ***
                            $response = [
                                'status' => 'requires_2fa',
                                'message' => 'ต้องการยืนยันตัวตน 2FA',
                                'show_google_auth' => true,
                                'requires_verification' => true,
                                'user_type' => 'public',
                                'temp_user_type' => 'public',
                                'csrf_hash' => $this->security->get_csrf_hash() // *** แก้ไข: ใช้ security library ***
                            ];

                            log_message('info', "2FA challenge initiated for user: " . $result->mp_email);
                        }
                    } catch (Exception $e) {
                        log_message('error', "Error in trusted device check: " . $e->getMessage());

                        $response = [
                            'status' => 'requires_2fa',
                            'message' => 'ต้องการยืนยันตัวตน 2FA',
                            'show_google_auth' => true,
                            'requires_verification' => true,
                            'user_type' => 'public',
                            'temp_user_type' => 'public',
                            'csrf_hash' => $this->security->get_csrf_hash() // *** แก้ไข: เพิ่ม CSRF ***
                        ];
                    }
                } else {
                    log_message('info', "No 2FA required - proceeding with normal login");

                    try {
                        $this->create_public_session($result, false);
                        $this->reset_failed_attempts($fingerprint);
                        $this->record_login_attempt($email, 'success', $fingerprint);

                        if (isset($this->user_log_model)) {
                            $this->user_log_model->log_activity(
                                $email,
                                'login',
                                'ประชาชนเข้าสู่ระบบผ่านแบบฟอร์มเข้าสู่ระบบ (Enhanced with reCAPTCHA)',
                                'auth'
                            );
                        }

                        $response = [
                            'status' => 'success',
                            'message' => 'เข้าสู่ระบบสำเร็จ',
                            'redirect' => site_url('Pages/service_systems'),
                            'user_data' => [
                                'mp_id' => $result->mp_id,
                                'mp_email' => $result->mp_email,
                                'mp_fname' => $result->mp_fname,
                                'mp_lname' => $result->mp_lname
                            ],
                            'csrf_hash' => $this->security->get_csrf_hash() // *** แก้ไข: เพิ่ม CSRF ***
                        ];

                        log_message('info', "Public user login successful (no 2FA): " . $email);

                    } catch (Exception $e) {
                        log_message('error', "Error creating public session: " . $e->getMessage());
                        log_message('error', "Stack trace: " . $e->getTraceAsString());
                        throw new Exception('เกิดข้อผิดพลาดในการสร้าง session');
                    }
                }
            } else {
                log_message('debug', "User login failed - invalid credentials for: " . $email);

                try {
                    $this->handle_citizen_login_failure($email, $password, $fingerprint);
                    return;
                } catch (Exception $e) {
                    log_message('error', "Error handling login failure: " . $e->getMessage());
                    throw new Exception('อีเมลหรือรหัสผ่านไม่ถูกต้อง');
                }
            }

        } catch (Exception $e) {
            log_message('error', "FATAL ERROR in citizen check_login: " . $e->getMessage());
            log_message('error', "Stack trace: " . $e->getTraceAsString());

            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'csrf_hash' => $this->security->get_csrf_hash() // *** แก้ไข: เพิ่ม CSRF ***
            ];
        }

        // ส่งผลลัพธ์กลับ
        if (!isset($response['csrf_hash'])) {
            $response['csrf_hash'] = $this->security->get_csrf_hash(); // *** แก้ไข: fallback CSRF ***
        }

        log_message('info', "Final citizen response: " . json_encode($response));
        log_message('info', "=== CITIZEN CHECK_LOGIN END ===");

        $this->output->set_output(json_encode($response));
    }
    
    /**
     * ✅ ปรับปรุง handle_login_failure สำหรับ Citizen เพื่อรวม CSRF token และ debug logs
     */
    private function handle_citizen_login_failure($email, $password, $fingerprint)
    {
        try {
            log_message('info', "Handling citizen login failure for: " . $email);

            $this->record_login_attempt($email, 'failed', $fingerprint);

            $attempts_info = $this->count_failed_attempts($fingerprint);
            $max_attempts = 3;
            $remaining_attempts = $max_attempts - $attempts_info;

            log_message('debug', "Login failure details:");
            log_message('debug', "- Failed attempts: " . $attempts_info);
            log_message('debug', "- Max attempts: " . $max_attempts);
            log_message('debug', "- Remaining attempts: " . $remaining_attempts);

            if (isset($this->user_log_model)) {
                $this->user_log_model->log_detect(
                    $email,
                    $password,
                    'public',
                    'failed',
                    'Citizen user login failed (Enhanced with reCAPTCHA)',
                    'auth'
                );
            }

            if ($remaining_attempts <= 0) {
                log_message('debug', "Blocking user due to too many failed attempts: " . $email);

                $this->block_login($fingerprint);

                if (method_exists($this, 'send_security_alert')) {
                    $this->send_security_alert($email, $attempts_info, 'public', 1);
                }

                $response = [
                    'status' => 'blocked',
                    'message' => 'คุณถูกบล็อค 3 นาที เนื่องจากล็อกอินผิดพลาด 3 ครั้ง',
                    'remaining_time' => 180,
                    'block_level' => 1,
                    'csrf_hash' => $this->security->get_csrf_hash()
                ];

                log_message('info', "User blocked - sending blocked response");
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
                    'attempts' => $attempts_info,
                    'remaining_attempts' => $remaining_attempts,
                    'csrf_hash' => $this->security->get_csrf_hash()
                ];

                log_message('info', "Login failed - remaining attempts: " . $remaining_attempts);
            }

            log_message('debug', "Sending login failure response: " . json_encode($response));

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', "Error in handle_citizen_login_failure: " . $e->getMessage());
            log_message('error', "Exception trace: " . $e->getTraceAsString());

            $response = [
                'status' => 'error',
                'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
                'csrf_hash' => $this->security->get_csrf_hash()
            ];

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }


    /**
     * สร้าง session สำหรับประชาชน
     */
    private function create_public_session($user_data, $is_2fa_verified = false, $trusted_device = false)
    {
        try {
            error_log("Creating public session for user: " . $user_data->mp_id);

            // ดึง tenant data
            $tenant_id = $this->session->userdata('tenant_id') ?: 1;
            $tenant_code = $this->session->userdata('tenant_code') ?: 'default';
            $tenant_name = $this->session->userdata('tenant_name') ?: 'Default Organization';
            $tenant_domain = $this->session->userdata('tenant_domain') ?: $_SERVER['HTTP_HOST'];

            $sess = array(
                'mp_id' => $user_data->mp_id,
                'mp_email' => $user_data->mp_email,
                'mp_fname' => $user_data->mp_fname,
                'mp_lname' => $user_data->mp_lname,
                'mp_img' => isset($user_data->mp_img) ? $user_data->mp_img : null,
                'mp_phone' => isset($user_data->mp_phone) ? $user_data->mp_phone : null,
                'mp_number' => isset($user_data->mp_number) ? $user_data->mp_number : null,
                'mp_address' => isset($user_data->mp_address) ? $user_data->mp_address : null,
                'is_public' => true,
                'tenant_id' => $tenant_id,
                'tenant_code' => $tenant_code,
                'tenant_name' => $tenant_name,
                'tenant_domain' => $tenant_domain,
                '2fa_verified' => $is_2fa_verified,
                'trusted_device' => $trusted_device,
                'login_time' => time()
            );

            $this->session->set_userdata($sess);

            error_log("Public session created successfully");
            return true;

        } catch (Exception $e) {
            error_log("Error creating public session: " . $e->getMessage());
            throw $e;
        }
    }
    /**
     * จัดการการเข้าสู่ระบบสำหรับประชาชน พร้อม 2FA
     */
    private function handle_public_login($fingerprint)
    {
        $email = $this->input->post('mp_email');
        $password = $this->input->post('mp_password');

        // ตรวจสอบการถูกบล็อค (สำหรับประชาชน)
        $block_status = $this->check_if_blocked($fingerprint);
        if ($block_status['is_blocked']) {
            $block_message = 'คุณถูกบล็อคชั่วคราว โปรดรอ';
            if (isset($block_status['block_level']) && $block_status['block_level'] == 2) {
                $block_message = 'คุณถูกบล็อคชั่วคราว (ล็อกอินผิดพลาด 6 ครั้ง) โปรดรอ';
            }

            $response = [
                'status' => 'blocked',
                'message' => $block_message,
                'remaining_time' => $block_status['remaining_time'],
                'block_level' => isset($block_status['block_level']) ? $block_status['block_level'] : 1
            ];

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }

        try {
            if (!isset($this->member_public_model)) {
                $this->load->model('member_public_model');
            }

            $result = $this->member_public_model->fetch_user_login(
                $email,
                sha1($password)
            );

            if (!empty($result)) {
                if (isset($result->mp_status) && $result->mp_status == 0) {
                    $response = [
                        'status' => 'error',
                        'message' => 'บัญชีนี้ถูกระงับการใช้งาน โปรดติดต่อผู้ให้บริการ'
                    ];
                } else {
                    // **สำคัญ: ตรวจสอบ 2FA ก่อนสร้าง session หลัก**
                    if (!empty($result->google2fa_secret) && $result->google2fa_enabled == 1) {

                        // ตรวจสอบ Trusted Device ก่อน
                        if ($this->is_trusted_device($result->mp_id, $this->tenant->id, 'public')) {
                            error_log("Trusted device found for public user: " . $result->mp_email . " - Skipping 2FA");

                            $this->update_trusted_device_usage($result->mp_id, $this->tenant->id, 'public');
                            $this->reset_failed_attempts($fingerprint);
                            $this->record_login_attempt($email, 'success', $fingerprint);

                            // สร้าง session ปกติ (Skip 2FA)
                            $sess = array(
                                'mp_id' => $result->mp_id,
                                'mp_email' => $result->mp_email,
                                'mp_fname' => $result->mp_fname,
                                'mp_lname' => $result->mp_lname,
                                'mp_img' => isset($result->mp_img) ? $result->mp_img : null,
                                'mp_phone' => isset($result->mp_phone) ? $result->mp_phone : null,
                                'mp_number' => isset($result->mp_number) ? $result->mp_number : null,
                                'mp_address' => isset($result->mp_address) ? $result->mp_address : null,
                                'is_public' => true,
                                'tenant_id' => $this->tenant->id,
                                'tenant_code' => $this->tenant->code,
                                'tenant_name' => $this->tenant->name,
                                'tenant_domain' => $this->tenant->domain,
                                '2fa_verified' => true,
                                'trusted_device' => true
                            );
                            $this->session->set_userdata($sess);

                            $redirect_url = site_url('Pages/service_systems');
                            if (!file_exists(APPPATH . 'controllers/Pages.php')) {
                                log_message('warning', 'Pages controller not found, using default redirect');
                                $redirect_url = site_url();
                            }

                            $response = [
                                'status' => 'success',
                                'message' => 'เข้าสู่ระบบสำเร็จ',
                                'redirect' => $redirect_url,
                                'user_data' => [
                                    'mp_id' => $result->mp_id,
                                    'mp_email' => $result->mp_email,
                                    'mp_fname' => $result->mp_fname,
                                    'mp_lname' => $result->mp_lname
                                ]
                            ];

                            log_message('debug', 'Public user login successful (trusted device): ' . $email);
                        } else {
                            // ลบ session หลักทิ้งก่อน (ป้องกัน bypass)
                            $this->session->unset_userdata([
                                'mp_id',
                                'mp_email',
                                'mp_fname',
                                'mp_lname',
                                'mp_img',
                                'mp_phone',
                                'mp_number',
                                'mp_address',
                                'is_public',
                                'tenant_id',
                                'tenant_code',
                                'tenant_name',
                                'tenant_domain'
                            ]);

                            $this->reset_failed_attempts($fingerprint);
                            $this->record_login_attempt($email, 'success', $fingerprint);

                            // เก็บข้อมูลชั่วคราวสำหรับ 2FA เท่านั้น
                            $temp_data = array(
                                'temp_mp_id' => $result->mp_id,
                                'temp_mp_email' => $result->mp_email,
                                'temp_mp_fname' => $result->mp_fname,
                                'temp_mp_lname' => $result->mp_lname,
                                'temp_mp_img' => isset($result->mp_img) ? $result->mp_img : null,
                                'temp_mp_phone' => isset($result->mp_phone) ? $result->mp_phone : null,
                                'temp_mp_number' => isset($result->mp_number) ? $result->mp_number : null,
                                'temp_mp_address' => isset($result->mp_address) ? $result->mp_address : null,
                                'temp_tenant_id' => $this->tenant->id,
                                'temp_tenant_code' => $this->tenant->code,
                                'temp_tenant_name' => $this->tenant->name,
                                'temp_tenant_domain' => $this->tenant->domain,
                                'temp_google2fa_secret' => $result->google2fa_secret,
                                'temp_login_time' => time(),
                                'temp_user_type' => 'public', // *** เพิ่มการระบุประเภทผู้ใช้ ***
                                'requires_2fa' => true
                            );
                            $this->session->set_userdata($temp_data);

                            error_log("2FA Required for public user: " . $result->mp_email);
                            error_log("Temp session created with requires_2fa flag");

                            $response = [
                                'status' => 'requires_2fa',
                                'message' => 'ต้องการยืนยันตัวตน 2FA',
                                'show_google_auth' => true,
                                'requires_verification' => true,
                                'user_type' => 'public', // *** เพิ่มการส่ง user_type ใน response ***
                                'temp_user_type' => 'public', // *** เพิ่มสำหรับ JavaScript ***
                                'endpoint' => 'Auth_api/verify_otp_public' // *** เพิ่มการบอก endpoint ที่ถูกต้อง ***
                            ];
                        }
                    } else {
                        // กรณีไม่มี 2FA - สร้าง session ปกติ
                        // ... โค้ดเดิม ...

                        $response = [
                            'status' => 'success',
                            'message' => 'เข้าสู่ระบบสำเร็จ',
                            'redirect' => $redirect_url,
                            'user_data' => [
                                'mp_id' => $result->mp_id,
                                'mp_email' => $result->mp_email,
                                'mp_fname' => $result->mp_fname,
                                'mp_lname' => $result->mp_lname
                            ],
                            'user_type' => 'public' // *** เพิ่มการส่ง user_type ***
                        ];
                    }
                }
            } else {
                // ถ้าไม่พบข้อมูลผู้ใช้
                $this->handle_public_login_failure($email, $password, $fingerprint);
                return;
            }
        } catch (Exception $e) {
            log_message('error', 'Error checking public user login: ' . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล กรุณาลองใหม่อีกครั้ง'
            ];
        }

        // ส่งผลลัพธ์กลับ
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }



    public function get_temp_user_type()
    {
        $temp_user_type = $this->session->userdata('temp_user_type');
        $requires_2fa = $this->session->userdata('requires_2fa');

        $response = [
            'temp_user_type' => $temp_user_type,
            'requires_2fa' => $requires_2fa ? true : false
        ];

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


    /**
     * จัดการการเข้าสู่ระบบล้มเหลวสำหรับประชาชน
     */
    private function handle_public_login_failure($email, $password, $fingerprint)
    {
        // บันทึกความพยายามเข้าสู่ระบบที่ล้มเหลว
        $this->record_login_attempt($email, 'failed', $fingerprint);

        // นับจำนวนครั้งที่ล้มเหลว
        $attempts_info = $this->count_failed_attempts($fingerprint);
        $max_attempts = 3; // จำนวนครั้งสูงสุดที่อนุญาตให้ล้มเหลว
        $remaining_attempts = $max_attempts - $attempts_info;

        // บันทึก log กิจกรรมการเข้าสู่ระบบที่ล้มเหลว
        $this->user_log_model->log_detect(
            $email,
            $password,
            'public',
            'failed',
            'Public user login failed',
            'auth'
        );

        if ($remaining_attempts <= 0) {
            // ถ้าเกินจำนวนครั้งที่กำหนด ให้บล็อค
            $this->block_login($fingerprint);

            // === เรียกใช้ Function แจ้งเตือนไปที่ Line ===
            $this->send_security_alert($email, $attempts_info, 'public', 1);

            $response = [
                'status' => 'blocked',
                'message' => 'คุณถูกบล็อค 3 นาที เนื่องจากล็อกอินผิดพลาด 3 ครั้ง',
                'remaining_time' => 180, // 3 นาที
                'block_level' => 1
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
                'attempts' => $attempts_info,
                'remaining_attempts' => $remaining_attempts
            ];
        }

        log_message('debug', 'Public user login failed: ' . $email);

        // ส่งผลลัพธ์กลับ
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * จัดการการเข้าสู่ระบบสำหรับเจ้าหน้าที่ (เหมือนเดิม)
     */
    private function handle_staff_login($fingerprint)
    {
        $username = $this->input->post('m_username');
        $password = $this->input->post('m_password');

        // ตรวจสอบการถูกบล็อค
        $block_status = $this->check_if_blocked($fingerprint);
        if ($block_status['is_blocked']) {
            $block_message = 'คุณถูกบล็อคชั่วคราว โปรดรอ';

            // ปรับข้อความตามระดับการบล็อค
            if (isset($block_status['block_level']) && $block_status['block_level'] == 2) {
                $block_message = 'คุณถูกบล็อคชั่วคราว (ล็อกอินผิดพลาด 6 ครั้ง) โปรดรอ';
            }

            $response = [
                'status' => 'blocked',
                'message' => $block_message,
                'remaining_time' => $block_status['remaining_time'],
                'block_level' => isset($block_status['block_level']) ? $block_status['block_level'] : 1
            ];

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }

        // ตรวจสอบข้อมูลเจ้าหน้าที่
        $result = $this->member_model->fetch_user_login(
            $username,
            sha1($password)
        );

        if (!empty($result)) {
            // ตรวจสอบสถานะผู้ใช้
            if ($result->m_status == 0) {
                $response = [
                    'status' => 'error',
                    'message' => 'คุณถูกบล็อค โปรดติดต่อผู้ให้บริการ'
                ];
            } else {
                // สร้าง session สำหรับเจ้าหน้าที่
                $sess = array(
                    'm_id' => $result->m_id,
                    'm_level' => $result->ref_pid,
                    'grant_system_ref_id' => $result->grant_system_ref_id,
                    'grant_user_ref_id' => $result->grant_user_ref_id,
                    'm_system' => $result->m_system,
                    'm_fname' => $result->m_fname,
                    'm_lname' => $result->m_lname,
                    'm_username' => $result->m_username,
                    'm_img' => $result->m_img,
                    'is_public' => false,
                    'tenant_id' => $this->tenant->id,
                    'tenant_code' => $this->tenant->code,
                    'tenant_name' => $this->tenant->name,
                    'tenant_domain' => $this->tenant->domain
                );

                $this->session->set_userdata($sess);

                // บันทึก log กิจกรรม
                $this->user_log_model->log_activity(
                    $username,
                    'login',
                    'เจ้าหน้าที่เข้าสู่ระบบผ่านแบบฟอร์มเข้าสู่ระบบ',
                    'auth'
                );

                $redirect_url = site_url('User/choice');

                // รีเซ็ตการนับจำนวนครั้งล็อกอินที่ล้มเหลว
                $this->reset_failed_attempts($fingerprint);

                // บันทึกความพยายามเข้าสู่ระบบที่สำเร็จ
                $this->record_login_attempt($username, 'success', $fingerprint);

                $response = [
                    'status' => 'success',
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'redirect' => $redirect_url
                ];

                // บันทึก log
                log_message('debug', 'Staff user login successful: ' . $username);
                log_message('debug', 'Redirecting to: ' . $redirect_url);
            }
        } else {
            // จัดการ staff login failure (เหมือนเดิม)
            $this->handle_staff_login_failure($username, $password, $fingerprint);
            return;
        }

        // ส่งผลลัพธ์กลับ
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function verify_otp_public()
    {
        try {
            // ตรวจสอบ method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ใช้ได้เฉพาะ POST method เท่านั้น'
                    ]));
                return;
            }

            // ตรวจสอบ session
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }

            // ตรวจสอบ session data
            $temp_mp_id = $this->session->userdata('temp_mp_id');
            $requires_2fa = $this->session->userdata('requires_2fa');
            $temp_user_type = $this->session->userdata('temp_user_type');
            $temp_login_time = $this->session->userdata('temp_login_time');
            $secret = $this->session->userdata('temp_google2fa_secret');

            // ตรวจสอบความครบถ้วนของ session
            if (!$temp_mp_id || !$requires_2fa || $temp_user_type !== 'public') {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่',
                        'redirect' => site_url('User')
                    ]));
                return;
            }

            // ตรวจสอบ timeout
            if (!$temp_login_time || (time() - $temp_login_time) > 900) {
                $this->clear_temp_session();
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'หมดเวลาการยืนยัน กรุณาเข้าสู่ระบบใหม่',
                        'redirect' => site_url('User')
                    ]));
                return;
            }

            $otp = $this->input->post('otp');
            $remember_device = $this->input->post('remember_device');

            if (empty($otp)) {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'กรุณากรอกรหัส OTP'
                    ]));
                return;
            }

            if (empty($secret)) {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ข้อมูล 2FA ไม่สมบูรณ์ กรุณาเข้าสู่ระบบใหม่',
                        'redirect' => site_url('User')
                    ]));
                return;
            }

            // ตรวจสอบ OTP
            if (!$this->google2fa->verifyKey($secret, $otp)) {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'รหัส OTP ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง'
                    ]));
                return;
            }

            // OTP ถูกต้อง - ดึงข้อมูลผู้ใช้จากฐานข้อมูลให้ครบถ้วน
            $user_id = $temp_mp_id;
            $tenant_id = $this->session->userdata('temp_tenant_id') ?: 1;

            // *** สำคัญ: ดึงข้อมูลผู้ใช้ครบถ้วนจากฐานข้อมูล ***
            if (!isset($this->member_public_model)) {
                $this->load->model('member_public_model');
            }

            $user_data = $this->member_public_model->get_member_by_id($user_id);
            if (!$user_data) {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'ไม่พบข้อมูลผู้ใช้ กรุณาเข้าสู่ระบบใหม่',
                        'redirect' => site_url('User')
                    ]));
                return;
            }

            // บันทึก trusted device ถ้าเลือก
            $trusted_device_saved = false;
            if ($remember_device == '1') {
                if ($this->db->table_exists('trusted_devices')) {
                    $device_token = $this->save_trusted_device($user_id, $tenant_id, 'public');
                    if ($device_token) {
                        $trusted_device_saved = true;
                    }
                }
            }

            // *** แก้ไข: ใช้ฟังก์ชันสร้าง session ที่ครบถ้วน ***
            $this->create_complete_public_session($user_data, true, $trusted_device_saved, $tenant_id);

            // ลบข้อมูลชั่วคราว
            $this->clear_temp_session();

            // กำหนด redirect URL
            $redirect_url = $this->get_public_redirect_url();

            $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'ยืนยันตัวตนสำเร็จ',
                    'redirect' => $redirect_url,
                    'user_data' => [
                        'mp_id' => $user_id,
                        'mp_email' => $user_data->mp_email,
                        'mp_fname' => $user_data->mp_fname,
                        'mp_lname' => $user_data->mp_lname
                    ]
                ]));

        } catch (Exception $e) {
            error_log("Error in verify_otp_public: " . $e->getMessage());
            $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'
                ]));
        }
    }

    /**
     * สร้าง session สำหรับประชาชนแบบครบถ้วน (หลัง 2FA)
     */
    private function create_complete_public_session($user_data, $is_2fa_verified = false, $trusted_device = false, $tenant_id = null)
    {
        try {
            error_log("Creating complete public session for user: " . $user_data->mp_id);

            // ดึง tenant data
            if (!$tenant_id) {
                $tenant_id = $this->session->userdata('temp_tenant_id') ?: 1;
            }

            // ดึงข้อมูล tenant จากฐานข้อมูล
            $tenant = null;
            if (isset($this->tenant_access_model)) {
                $tenant = $this->tenant_access_model->get_tenant_by_id($tenant_id);
            } else {
                $this->load->model('tenant_access_model');
                $tenant = $this->tenant_access_model->get_tenant_by_id($tenant_id);
            }

            // ตั้งค่าข้อมูล tenant
            $tenant_code = $tenant ? $tenant->code : 'default';
            $tenant_name = $tenant ? $tenant->name : 'Default Organization';
            $tenant_domain = $tenant ? $tenant->domain : $_SERVER['HTTP_HOST'];

            // สร้าง session แบบครบถ้วน
            $sess = array(
                // ข้อมูลผู้ใช้หลัก
                'mp_id' => $user_data->mp_id,
                'mp_email' => $user_data->mp_email,
                'mp_fname' => $user_data->mp_fname,
                'mp_lname' => $user_data->mp_lname,
                'mp_prefix' => isset($user_data->mp_prefix) ? $user_data->mp_prefix : '',
                'mp_img' => isset($user_data->mp_img) ? $user_data->mp_img : null,
                'mp_phone' => isset($user_data->mp_phone) ? $user_data->mp_phone : null,
                'mp_number' => isset($user_data->mp_number) ? $user_data->mp_number : null,
                'mp_address' => isset($user_data->mp_address) ? $user_data->mp_address : null,

                // ข้อมูลระบบ
                'is_public' => true,
                'user_type' => 'public',
                'permissions' => 'ex_user',

                // ข้อมูล tenant
                'tenant_id' => $tenant_id,
                'tenant_code' => $tenant_code,
                'tenant_name' => $tenant_name,
                'tenant_domain' => $tenant_domain,

                // ข้อมูลความปลอดภัย
                '2fa_verified' => $is_2fa_verified,
                'trusted_device' => $trusted_device,

                // ข้อมูลเวลา
                'login_time' => time(),
                'login_timestamp' => time()
            );

            // ตั้งค่า session
            $this->session->set_userdata($sess);

            // ตรวจสอบว่า session ถูกตั้งค่าสำเร็จ
            $mp_id_check = $this->session->userdata('mp_id');
            if (!$mp_id_check) {
                throw new Exception('Failed to set session data');
            }

            error_log("Complete public session created successfully for user: " . $user_data->mp_id);
            error_log("Session data set: mp_id=" . $this->session->userdata('mp_id') .
                ", 2fa_verified=" . ($this->session->userdata('2fa_verified') ? 'true' : 'false') .
                ", tenant_id=" . $this->session->userdata('tenant_id'));

            return true;

        } catch (Exception $e) {
            error_log("Error creating complete public session: " . $e->getMessage());
            throw $e;
        }
    }


    private function get_public_redirect_url()
    {
        // ลำดับความสำคัญของ URL
        $urls = [
            'Pages/service_systems',
            'Public/dashboard',
            'Welcome/public',
            'Home/index'
        ];

        foreach ($urls as $url) {
            $controller = explode('/', $url)[0];
            if (file_exists(APPPATH . 'controllers/' . $controller . '.php')) {
                error_log("Using redirect URL: " . site_url($url));
                return site_url($url);
            }
        }

        error_log("No suitable controller found, using base URL");
        return site_url();
    }




    // *** เพิ่มฟังก์ชันสำหรับ Trusted Device Management ***

    /**
     * ตรวจสอบว่าเครื่องนี้เป็น trusted device หรือไม่
     */
    private function is_trusted_device($user_id, $tenant_id, $user_type = 'public')
    {
        try {
            $device_fingerprint = $this->generate_device_fingerprint();
            $current_time = date('Y-m-d H:i:s');

            error_log("=== TRUSTED DEVICE CHECK ===");
            error_log("User ID: $user_id, Tenant ID: $tenant_id, User Type: $user_type");
            error_log("Device Fingerprint: " . substr($device_fingerprint, 0, 16) . "...");

            // ทำความสะอาด devices ที่หมดอายุก่อน
            $this->db->where('expires_at <', $current_time)->delete('trusted_devices');

            // ค้นหา trusted device
            $trusted = $this->db->select('*')
                ->where('user_id', (int) $user_id)
                ->where('user_type', $user_type)
                ->where('tenant_id', (int) $tenant_id)
                ->where('device_fingerprint', $device_fingerprint)
                ->where('expires_at >', $current_time)
                ->get('trusted_devices');

            $is_trusted = $trusted->num_rows() > 0;

            error_log("SQL Query: " . $this->db->last_query());
            error_log("Found devices: " . $trusted->num_rows());
            error_log("Result: " . ($is_trusted ? 'TRUSTED' : 'NOT TRUSTED'));
            error_log("=== END CHECK ===");

            return $is_trusted;

        } catch (Exception $e) {
            error_log("Error in is_trusted_device: " . $e->getMessage());
            return false;
        }
    }


    /**
     * บันทึก trusted device
     */
    private function save_trusted_device($user_id, $tenant_id = null, $user_type = 'public', $duration_hours = 720)
    {
        try {
            // ใช้ tenant_id จาก session หากไม่ได้ส่งมา
            if (!$tenant_id) {
                $tenant_id = $this->session->userdata('tenant_id') ?: 1;
            }

            $device_token = bin2hex(random_bytes(32));
            $device_fingerprint = $this->generate_device_fingerprint();
            $current_time = date('Y-m-d H:i:s');
            $expires_time = date('Y-m-d H:i:s', time() + ($duration_hours * 3600));

            error_log("=== SAVING TRUSTED DEVICE ===");
            error_log("User ID: $user_id, User Type: $user_type, Tenant ID: $tenant_id");
            error_log("Duration: $duration_hours hours");
            error_log("Expires: $expires_time");

            // ลบ devices เก่าที่ใช้ fingerprint เดียวกัน
            $deleted = $this->db->where('device_fingerprint', $device_fingerprint)
                ->where('user_type', $user_type)
                ->where('tenant_id', $tenant_id)
                ->delete('trusted_devices');

            error_log("Deleted $deleted old devices with same fingerprint");

            // เตรียมข้อมูลอุปกรณ์
            $device_info = [
                'user_agent' => $this->input->user_agent() ?: 'Unknown',
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0',
                'screen_resolution' => $this->input->post('screen_resolution') ?: 'Unknown',
                'timezone' => $this->input->post('timezone') ?: 'Unknown',
                'saved_at' => $current_time,
                'source' => 'public_login'
            ];

            // บันทึกข้อมูล trusted device
            $data = [
                'user_id' => (int) $user_id,
                'user_type' => $user_type,
                'tenant_id' => (int) $tenant_id,
                'device_token' => $device_token,
                'device_fingerprint' => $device_fingerprint,
                'device_info' => json_encode($device_info),
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0',
                'user_agent' => substr($this->input->user_agent() ?: 'Unknown', 0, 500),
                'created_at' => $current_time,
                'expires_at' => $expires_time,
                'last_used_at' => $current_time
            ];

            $insert_result = $this->db->insert('trusted_devices', $data);

            if ($insert_result && $this->db->error()['code'] === 0) {
                $insert_id = $this->db->insert_id();
                error_log("✅ TRUSTED DEVICE SAVED SUCCESSFULLY!");
                error_log("Device ID: $insert_id, Token: " . substr($device_token, 0, 8) . "...");

                return $device_token;
            } else {
                error_log("❌ FAILED to save trusted device");
                error_log("Database error: " . print_r($this->db->error(), true));
                return false;
            }

        } catch (Exception $e) {
            error_log("Exception in save_trusted_device: " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้าง device fingerprint
     */
    private function generate_device_fingerprint()
    {
        $user_agent = $this->input->user_agent() ?: '';
        $accept_language = $this->input->server('HTTP_ACCEPT_LANGUAGE') ?: '';
        $accept_encoding = $this->input->server('HTTP_ACCEPT_ENCODING') ?: '';
        $accept = $this->input->server('HTTP_ACCEPT') ?: '';

        $fingerprint_string = implode('|', [
            trim($user_agent),
            trim($accept_language),
            trim($accept_encoding),
            trim($accept)
        ]);

        return hash('sha256', $fingerprint_string);
    }


    /**
     * อัพเดทการใช้งานล่าสุดของ trusted device
     */
    private function update_trusted_device_usage($user_id, $tenant_id = null, $user_type = 'public')
    {
        try {
            // ใช้ tenant_id จาก session หากไม่ได้ส่งมา
            if (!$tenant_id) {
                $tenant_id = $this->session->userdata('tenant_id') ?: 1;
            }

            $device_fingerprint = $this->generate_device_fingerprint();
            $current_time = date('Y-m-d H:i:s');

            $updated = $this->db->where('user_id', (int) $user_id)
                ->where('user_type', $user_type)
                ->where('tenant_id', (int) $tenant_id)
                ->where('device_fingerprint', $device_fingerprint)
                ->where('expires_at >', $current_time)
                ->set('last_used_at', $current_time)
                ->update('trusted_devices');

            $affected_rows = $this->db->affected_rows();
            error_log("Updated $affected_rows trusted device usage records");

            return $affected_rows > 0;

        } catch (Exception $e) {
            error_log("Exception in update_trusted_device_usage: " . $e->getMessage());
            return false;
        }
    }


    /**
     * ลบ trusted devices เก่า
     */
    private function cleanup_old_trusted_devices($user_id, $tenant_id, $user_type = 'public', $keep_limit = 3)
    {
        try {
            $current_time = date('Y-m-d H:i:s');

            // ลบ devices ที่หมดอายุ
            $expired_deleted = $this->db->where('expires_at <', $current_time)
                ->delete('trusted_devices');

            if ($expired_deleted > 0) {
                error_log("Cleanup: Deleted $expired_deleted expired devices");
            }

            // ลบ devices เก่าเกิน limit (เก็บไว้แค่ 3 devices ล่าสุด)
            $devices = $this->db->select('id, last_used_at')
                ->where('user_id', $user_id)
                ->where('user_type', $user_type)
                ->where('tenant_id', $tenant_id)
                ->where('expires_at >', $current_time)
                ->order_by('last_used_at', 'DESC')
                ->get('trusted_devices')
                ->result();

            if (count($devices) > $keep_limit) {
                $devices_to_delete = array_slice($devices, $keep_limit);
                foreach ($devices_to_delete as $device) {
                    $this->db->where('id', $device->id)->delete('trusted_devices');
                    error_log("Cleanup: Deleted old device ID: " . $device->id);
                }
            }

        } catch (Exception $e) {
            error_log("Error in cleanup_old_trusted_devices: " . $e->getMessage());
        }
    }

    public function get_user_trusted_devices($user_id, $user_type, $tenant_id = null)
    {
        $this->db->select('
        id, 
        device_token, 
        device_info, 
        ip_address,
        user_agent,
        created_at,
        expires_at,
        last_used_at
    ');
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);

        if ($tenant_id) {
            $this->db->where('tenant_id', $tenant_id);
        }

        $this->db->where('expires_at >', date('Y-m-d H:i:s'));
        $this->db->order_by('last_used_at', 'DESC');

        return $this->db->get('trusted_devices')->result();
    }


    public function remove_trusted_device($device_id, $user_id = null, $user_type = null)
    {
        $this->db->where('id', $device_id);

        // เพิ่มความปลอดภัยโดยตรวจสอบ user_id และ user_type
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }

        if ($user_type) {
            $this->db->where('user_type', $user_type);
        }

        $result = $this->db->delete('trusted_devices');

        return $this->db->affected_rows() > 0;
    }

    public function get_trusted_devices_stats($tenant_id = null)
    {
        $this->db->select('
        user_type,
        COUNT(*) as total_devices,
        COUNT(DISTINCT user_id) as unique_users,
        AVG(TIMESTAMPDIFF(DAY, created_at, expires_at)) as avg_duration_days
    ');

        if ($tenant_id) {
            $this->db->where('tenant_id', $tenant_id);
        }

        $this->db->where('expires_at >', date('Y-m-d H:i:s'));
        $this->db->group_by('user_type');

        return $this->db->get('trusted_devices')->result();
    }



    public function api_get_my_trusted_devices()
    {
        // ตรวจสอบการเข้าสู่ระบบ
        $user_id = null;
        $user_type = null;

        if ($this->session->userdata('m_id')) {
            // Staff user
            $user_id = $this->session->userdata('m_id');
            $user_type = 'staff';
        } elseif ($this->session->userdata('mp_id')) {
            // Public user
            $user_id = $this->session->userdata('mp_id');
            $user_type = 'public';
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $tenant_id = $this->session->userdata('tenant_id');
        $devices = $this->get_user_trusted_devices($user_id, $user_type, $tenant_id);

        // ปรับแต่งข้อมูลที่จะส่งกลับ
        $formatted_devices = [];
        foreach ($devices as $device) {
            $device_info = json_decode($device->device_info, true);

            $formatted_devices[] = [
                'id' => $device->id,
                'browser' => $device_info['browser'] ?? 'Unknown',
                'platform' => $device_info['platform'] ?? 'Unknown',
                'ip_address' => $device->ip_address,
                'created_at' => $device->created_at,
                'last_used_at' => $device->last_used_at,
                'expires_at' => $device->expires_at,
                'is_current' => $this->is_current_device($device->device_token)
            ];
        }

        echo json_encode([
            'status' => 'success',
            'user_type' => $user_type,
            'devices' => $formatted_devices,
            'total' => count($formatted_devices)
        ]);
    }


    private function is_current_device($device_token)
    {
        $current_fingerprint = $this->generate_device_fingerprint();

        $device = $this->db->select('device_fingerprint')
            ->where('device_token', $device_token)
            ->get('trusted_devices')
            ->row();

        return $device && $device->device_fingerprint === $current_fingerprint;
    }

    /**
     * ลบข้อมูล temp session
     */
    private function clear_temp_session()
    {
        error_log("Clearing temp session data");

        $temp_keys = [
            'temp_mp_id',
            'temp_mp_email',
            'temp_mp_fname',
            'temp_mp_lname',
            'temp_mp_img',
            'temp_mp_phone',
            'temp_mp_number',
            'temp_mp_address',
            'temp_m_id',
            'temp_m_level',
            'temp_grant_system_ref_id',
            'temp_grant_user_ref_id',
            'temp_m_system',
            'temp_m_fname',
            'temp_m_lname',
            'temp_m_username',
            'temp_m_img',
            'temp_tenant_id',
            'temp_tenant_code',
            'temp_tenant_name',
            'temp_tenant_domain',
            'temp_google2fa_secret',
            'temp_login_time',
            'temp_user_type',
            'requires_2fa'
        ];

        $this->session->unset_userdata($temp_keys);
        error_log("Temp session cleared");
    }


    /**
     * จัดการการเข้าสู่ระบบล้มเหลวสำหรับเจ้าหน้าที่ (เหมือนเดิม)
     */
    private function handle_staff_login_failure($username, $password, $fingerprint)
    {
        // บันทึกความพยายามเข้าสู่ระบบที่ล้มเหลว
        $this->record_login_attempt($username, 'failed', $fingerprint);

        // เช็คประวัติการบล็อค
        $this->db->where('fingerprint', $fingerprint);
        $this->db->where('status', 'blocked');
        $this->db->order_by('attempt_time', 'DESC');
        $this->db->limit(1);
        $block_history = $this->db->get('tbl_member_login_attempts')->row();

        // หาเวลารีเซ็ตล่าสุด
        $this->db->where('fingerprint', $fingerprint);
        $this->db->where_in('status', ['success', 'reset_history']);
        $this->db->order_by('attempt_time', 'DESC');
        $this->db->limit(1);
        $last_reset = $this->db->get('tbl_member_login_attempts')->row();

        $cutoff_time = '';

        if ($last_reset) {
            $cutoff_time = $last_reset->attempt_time;
        } else {
            $cutoff_time = date('Y-m-d H:i:s', time() - 1800); // 30 นาที
        }

        // ตรวจสอบจำนวนครั้งที่ล้มเหลวหลังจากรีเซ็ตล่าสุด
        $this->db->where('fingerprint', $fingerprint);
        $this->db->where('status', 'failed');
        $this->db->where('attempt_time >', $cutoff_time);
        $attempts_info = $this->db->count_all_results('tbl_member_login_attempts');

        // บันทึก log กิจกรรม
        $this->user_log_model->log_detect(
            $username,
            $password,
            'staff',
            'failed',
            'User logged in failed',
            'auth'
        );

        // ตรวจสอบว่าเคยถูกบล็อคหลังจากรีเซ็ตล่าสุดหรือไม่
        $block_history_after_reset = false;
        if ($block_history && $last_reset) {
            if (strtotime($block_history->attempt_time) > strtotime($last_reset->attempt_time)) {
                $block_history_after_reset = true;
            }
        } elseif ($block_history && !$last_reset) {
            $block_history_after_reset = true;
        }

        $remaining_attempts = 0;
        $block_level = 0;
        $block_duration = 0;

        if ($block_history_after_reset) {
            // เคยถูกบล็อคมาแล้วหลังจากรีเซ็ตล่าสุด นับเหลืออีกกี่ครั้งถึงบล็อครอบ 2
            $remaining_attempts = 6 - $attempts_info;
            if ($remaining_attempts <= 0) {
                $block_level = 2; // บล็อครอบที่ 2 (10 นาที)
                $block_duration = 10 * 60; // 10 นาที
            } else {
                $block_level = 1; // ยังอยู่ในรอบแรก
                $block_duration = 3 * 60; // 3 นาที
            }
        } else {
            // ยังไม่เคยถูกบล็อคหลังจากรีเซ็ตล่าสุด นับเหลืออีกกี่ครั้งถึงบล็อครอบแรก
            $remaining_attempts = 3 - $attempts_info;
            if ($remaining_attempts <= 0) {
                $block_level = 1; // บล็อครอบแรก (3 นาที)
                $block_duration = 3 * 60; // 3 นาที
            }
        }

        if ($remaining_attempts <= 0) {
            // บล็อคตามระดับ
            $this->block_login($fingerprint, $block_level);

            // === เรียกใช้ Function แจ้งเตือนไปที่ Line ===
            $this->send_security_alert($username, $attempts_info, 'staff', $block_level);

            $block_message = ($block_level == 2) ?
                'คุณถูกบล็อค 10 นาที เนื่องจากล็อกอินผิดพลาด 6 ครั้ง' :
                'คุณถูกบล็อค 3 นาที เนื่องจากล็อกอินผิดพลาด 3 ครั้ง';

            $response = [
                'status' => 'blocked',
                'message' => $block_message,
                'remaining_time' => $block_duration,
                'block_level' => $block_level
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'รหัสผ่านหรือชื่อผู้ใช้งานไม่ถูกต้อง',
                'attempts' => $attempts_info,
                'remaining_attempts' => $remaining_attempts,
                'next_block_level' => $block_level ? $block_level : 1
            ];
        }

        // บันทึก log
        log_message('debug', 'Staff user login failed: ' . $username);

        // ส่งผลลัพธ์กลับ
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * ตรวจสอบการออกจากระบบผ่าน API
     */
    public function logout()
    {
        $user_id = $this->session->userdata('m_id') ?: $this->session->userdata('mp_id');
        $is_public = $this->session->userdata('is_public');
        $username = $is_public ? $this->session->userdata('mp_email') : $this->session->userdata('m_username');

        if ($user_id) {
            // ลบ tokens ของผู้ใช้
            $this->db->where('user_id', $user_id)
                ->where('domain', $_SERVER['HTTP_HOST'])
                ->delete('auth_tokens');

            // บันทึก log กิจกรรม
            $this->user_log_model->log_activity(
                $username,
                'logout',
                'User logged out',
                'auth'
            );
        }

        // ลบ session
        if ($is_public) {
            $this->session->unset_userdata([
                'mp_id',
                'mp_fname',
                'mp_lname',
                'mp_email',
                'mp_img',
                'mp_phone',
                'mp_number',
                'mp_address',
                'is_public',
                'tenant_id',
                'tenant_code',
                'tenant_name',
                'tenant_domain',
                '2fa_verified',
                'trusted_device'
            ]);
        } else {
            $this->session->unset_userdata([
                'm_id',
                'm_level',
                'm_name',
                'grant_user_ref_id',
                'm_system',
                'm_username',
                'm_fname',
                'm_lname',
                'is_public',
                'tenant_id',
                'tenant_code',
                'tenant_name',
                'tenant_domain'
            ]);
        }

        $response = [
            'status' => 'success',
            'message' => 'ออกจากระบบสำเร็จ',
            'redirect' => site_url('User')
        ];

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * ตรวจสอบว่า fingerprint นี้ถูกบล็อคอยู่หรือไม่
     */
    private function check_if_blocked($fingerprint)
    {
        try {
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                return ['is_blocked' => false, 'remaining_time' => 0, 'block_level' => 0];
            }

            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $this->db->where('attempt_time >', date('Y-m-d H:i:s', time() - 1800));
            $failed_attempts = $this->db->count_all_results('tbl_member_login_attempts');

            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'blocked');
            $this->db->order_by('attempt_time', 'DESC');
            $this->db->limit(1);
            $block_history = $this->db->get('tbl_member_login_attempts')->row();

            $result = [
                'is_blocked' => false,
                'remaining_time' => 0,
                'block_level' => 0
            ];

            if ($failed_attempts >= 3) {
                $this->db->where('fingerprint', $fingerprint);
                $this->db->where('status', 'failed');
                $this->db->order_by('attempt_time', 'DESC');
                $this->db->limit(1);
                $last_attempt = $this->db->get('tbl_member_login_attempts')->row();

                if ($last_attempt) {
                    $now = time();
                    $block_duration = 3 * 60; // 3 นาที
                    $result['block_level'] = 1;

                    $block_until = strtotime($last_attempt->attempt_time) + $block_duration;

                    if ($now < $block_until) {
                        $result['is_blocked'] = true;
                        $result['remaining_time'] = $block_until - $now;
                    }
                }
            }

            return $result;

        } catch (Exception $e) {
            error_log('Error in check_if_blocked: ' . $e->getMessage());
            return ['is_blocked' => false, 'remaining_time' => 0, 'block_level' => 0];
        }
    }

    /**
     * นับจำนวนครั้งที่เข้าสู่ระบบล้มเหลว
     */
    private function count_failed_attempts($fingerprint)
    {
        try {
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                return 0;
            }

            $cutoff_time = date('Y-m-d H:i:s', time() - 1800);

            $failed_attempts = $this->db->where('fingerprint', $fingerprint)
                ->where('status', 'failed')
                ->where('attempt_time >', $cutoff_time)
                ->count_all_results('tbl_member_login_attempts');

            return $failed_attempts;

        } catch (Exception $e) {
            error_log('Error in count_failed_attempts: ' . $e->getMessage());
            return 0;
        }
    }


    /**
     * บันทึกความพยายามเข้าสู่ระบบ
     */
    private function record_login_attempt($username, $status, $fingerprint = null)
    {
        try {
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                return false;
            }

            if (empty($fingerprint)) {
                $fingerprint = md5($this->input->ip_address() . $_SERVER['HTTP_USER_AGENT']);
            }

            $data = [
                'fingerprint' => $fingerprint,
                'username' => $username,
                'status' => $status,
                'attempt_time' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0'
            ];

            return $this->db->insert('tbl_member_login_attempts', $data);

        } catch (Exception $e) {
            error_log('Error in record_login_attempt: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * บล็อคการเข้าสู่ระบบ
     */
    private function block_login($fingerprint, $block_level = 1)
    {
        try {
            $data = [
                'fingerprint' => $fingerprint,
                'username' => 'blocked_user',
                'status' => 'blocked',
                'attempt_time' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0',
                'block_level' => $block_level
            ];

            $this->db->insert('tbl_member_login_attempts', $data);

        } catch (Exception $e) {
            error_log('Error in block_login: ' . $e->getMessage());
        }
    }


    /**
     * รีเซ็ตการนับจำนวนครั้งล็อกอินที่ล้มเหลว
     */
    private function reset_failed_attempts($fingerprint)
    {
        try {
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                return false;
            }

            $this->db->where('fingerprint', $fingerprint)
                ->where('status', 'failed')
                ->delete('tbl_member_login_attempts');

            $data = [
                'fingerprint' => $fingerprint,
                'username' => 'system',
                'status' => 'reset_history',
                'attempt_time' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0'
            ];

            $this->db->insert('tbl_member_login_attempts', $data);
            return true;

        } catch (Exception $e) {
            error_log('Error in reset_failed_attempts: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ทำความสะอาด tokens
     */
    private function cleanup_tokens()
    {
        // 1. ลบ token ที่หมดอายุ
        $this->db->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete('auth_tokens');

        // 2. ลบ token ที่มีข้อมูลไม่สมบูรณ์
        $this->db->where('tenant_id IS NULL')
            ->or_where('tenant_code IS NULL')
            ->or_where('tenant_code', '')
            ->delete('auth_tokens');

        // 3. ลบ token ที่ไม่ตรงกับ domain ปัจจุบัน
        $current_domain = $_SERVER['HTTP_HOST'];
        $this->db->where('domain !=', $current_domain)
            ->delete('auth_tokens');

        // 4. ลบ token ที่เก่าเกิน 15 นาที
        $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-3 minutes')))
            ->delete('auth_tokens');

        // *** เพิ่ม: ทำความสะอาด trusted devices ที่หมดอายุ ***
        $this->db->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete('trusted_devices');
    }

    /**
     * ส่ง Line แจ้งเตือนความปลอดภัยเมื่อ login ผิดเกิน 3 ครั้ง
     */
    private function send_security_alert($username, $failed_count, $user_type, $block_level = 1)
    {
        try {
            // ดึงโดเมนปัจจุบันและทำความสะอาด
            $current_domain = $_SERVER['HTTP_HOST'];
            $current_domain = preg_replace('#^https?://#', '', $current_domain);
            $current_domain = preg_replace('/^www\./', '', $current_domain);
            $current_domain = strtok($current_domain, '/');
            $current_domain = strtolower(trim($current_domain));

            // ตั้งค่า default ชื่อองค์กร
            $organization_name = ucfirst(strtok($current_domain, '.'));

            try {
                // ดึงข้อมูลองค์กรจาก API (ถ้ามี)
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => 'https://assystem.co.th/api/organization/info/' . urlencode($current_domain),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 1,
                    CURLOPT_HTTPHEADER => ['X-Original-Domain: ' . $current_domain]
                ]);

                $response_api = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($response_api && $http_code === 200) {
                    $result_api = json_decode($response_api, true);
                    if (isset($result_api['organization_name']) && !empty($result_api['organization_name'])) {
                        $organization_name = $result_api['organization_name'];
                    }
                }
            } catch (Exception $e) {
                error_log('Error fetching organization info: ' . $e->getMessage());
            }

            // ดึงข้อมูลอุปกรณ์
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $ipAddress = $this->input->ip_address();
            $timestamp = date('Y-m-d H:i:s');

            // วิเคราะห์ระบบปฏิบัติการ
            $deviceOS = 'ไม่ทราบ';
            if (strpos($userAgent, 'Android') !== false) {
                $deviceOS = 'Android';
            } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
                $deviceOS = 'iOS';
            } elseif (strpos($userAgent, 'Windows NT 10.0') !== false) {
                $deviceOS = 'Windows 10';
            } elseif (strpos($userAgent, 'Windows NT 6.3') !== false) {
                $deviceOS = 'Windows 8.1';
            } elseif (strpos($userAgent, 'Windows NT 6.2') !== false) {
                $deviceOS = 'Windows 8';
            } elseif (strpos($userAgent, 'Windows NT 6.1') !== false) {
                $deviceOS = 'Windows 7';
            } elseif (strpos($userAgent, 'Mac') !== false) {
                $deviceOS = 'macOS';
            } elseif (strpos($userAgent, 'Linux') !== false && strpos($userAgent, 'Android') === false) {
                $deviceOS = 'Linux';
            } elseif (strpos($userAgent, 'Win') !== false) {
                $deviceOS = 'Windows';
            }

            $deviceType = strpos($userAgent, 'Mobile') !== false ? 'Mobile' : 'Desktop';

            $deviceBrowser = 'ไม่ทราบ';
            if (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Edg') === false) {
                $deviceBrowser = 'Chrome';
            } elseif (strpos($userAgent, 'Firefox') !== false) {
                $deviceBrowser = 'Firefox';
            } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
                $deviceBrowser = 'Safari';
            } elseif (strpos($userAgent, 'Edg') !== false) {
                $deviceBrowser = 'Edge';
            } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
                $deviceBrowser = 'Internet Explorer';
            }

            $deviceInfo = "\nประเภทอุปกรณ์: " . $deviceType;
            $deviceInfo .= "\nระบบปฏิบัติการ: " . $deviceOS;
            $deviceInfo .= "\nเบราว์เซอร์: " . $deviceBrowser;

            // กำหนดข้อความตามระดับการบล็อค
            $block_status = '';
            if ($block_level == 2) {
                $block_status = "ถูกบล็อกเป็นเวลา 10 นาที (ครั้งที่ 2)";
            } else {
                $block_status = "ถูกบล็อกเป็นเวลา 3 นาที";
            }

            // สร้างข้อความแจ้งเตือน
            $message = "🔒 แจ้งเตือนการล็อกอินล้มเหลว 🔒\n\n";
            $message .= "ระบบแอดมิน " . $organization_name . "\n";
            $message .= "-------------------------------\n";
            $message .= "👤 ผู้ใช้: " . $username . "\n";
            $message .= "📱 ประเภท: " . ($user_type == 'staff' ? 'บุคลากรภายใน' : 'ประชาชน') . "\n";
            $message .= "🔄 พยายามเข้าสู่ระบบล้มเหลว: " . $failed_count . " ครั้ง\n";
            $message .= "🌐 IP Address: " . $ipAddress . "\n";
            $message .= "🔗 เว็บไซต์: " . $current_domain . "\n";
            $message .= "📱 รายละเอียดอุปกรณ์: " . $deviceInfo . "\n";
            $message .= "⏰ เวลา: " . $timestamp . "\n";
            $message .= "⚠️ สถานะ: " . $block_status;

            // === ส่งไปทั้งสองกลุ่มพร้อมกัน ===

            // 1. ส่งไปกลุ่มผู้ดูแลระบบ
            if (isset($this->user_log_model) && method_exists($this->user_log_model, 'send_line_alert')) {
                $admin_result = $this->user_log_model->send_line_alert($message);
                error_log('Security alert to admin group: ' . ($admin_result ? 'success' : 'failed'));
            }

            // 2. ส่งไปกลุ่มลูกค้า  
            if (isset($this->user_log_model) && method_exists($this->user_log_model, 'send_line_customer')) {
                $customer_result = $this->user_log_model->send_line_customer($message);
                error_log('Security alert to customer group: ' . ($customer_result ? 'success' : 'failed'));
            }

            // 3. ส่งแจ้งเตือนทางอีเมล
            if (isset($this->user_log_model) && method_exists($this->user_log_model, 'send_line_email')) {
                $email_subject = "แจ้งเตือนความปลอดภัย: มีการพยายามเข้าถึงระบบที่ล้มเหลว";
                $email_result = $this->user_log_model->send_line_email($email_subject, $message);
                error_log('Security alert via email: ' . ($email_result ? 'success' : 'failed'));
            }

            error_log('Security alert sent for user: ' . $username . ' (type: ' . $user_type . ', attempts: ' . $failed_count . ', level: ' . $block_level . ')');

        } catch (Exception $e) {
            error_log('Error sending security alert: ' . $e->getMessage());
        }
    }

    /**
     * ข้อมูลเวอร์ชันของระบบ (เพื่อการตรวจสอบเบื้องต้น)
     */
    public function version()
    {
        $response = [
            'status' => 'success',
            'message' => 'ระบบตรวจสอบการเข้าสู่ระบบ API',
            'version' => '1.2.0',
            'features' => ['2FA Support', 'Trusted Devices', 'Public User Support'],
            'models_loaded' => [
                'member_model' => isset($this->member_model),
                'member_public_model' => isset($this->member_public_model),
                'user_log_model' => isset($this->user_log_model)
            ],
            'tenant' => [
                'id' => isset($this->tenant->id) ? $this->tenant->id : null,
                'code' => isset($this->tenant->code) ? $this->tenant->code : null,
                'domain' => isset($this->tenant->domain) ? $this->tenant->domain : null
            ],
            'session_active' => (session_status() == PHP_SESSION_ACTIVE)
        ];

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * ตรวจสอบสถานะการเข้าสู่ระบบ (เพื่อการทดสอบ)
     */
    public function check_status()
    {
        // ตรวจสอบว่ามีการเข้าสู่ระบบหรือไม่
        $is_logged_in = false;
        $is_public = false;
        $user_data = [];

        if ($this->session->userdata('m_id')) {
            $is_logged_in = true;
            $is_public = false;
            $user_data = [
                'user_id' => $this->session->userdata('m_id'),
                'username' => $this->session->userdata('m_username'),
                'name' => $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'),
                'level' => $this->session->userdata('m_level')
            ];
        } elseif ($this->session->userdata('mp_id')) {
            $is_logged_in = true;
            $is_public = true;
            $user_data = [
                'user_id' => $this->session->userdata('mp_id'),
                'email' => $this->session->userdata('mp_email'),
                'name' => $this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname')
            ];
        }

        $response = [
            'status' => 'success',
            'is_logged_in' => $is_logged_in,
            'is_public' => $is_public,
            'user_data' => $user_data,
            'session_id' => session_id(),
            'has_2fa' => $this->session->userdata('2fa_verified') ? true : false,
            'trusted_device' => $this->session->userdata('trusted_device') ? true : false
        ];

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }



    public function logout_public()
    {
        // บันทึก log การ logout
        $user_id = $this->session->userdata('mp_id');
        $user_email = $this->session->userdata('mp_email');

        if ($user_id) {
            log_message('info', 'Public user logout: ' . $user_id . ' (' . $user_email . ')');
        }

        // ลบ session
        $this->session->sess_destroy();

        // ตั้งค่า cookie เพื่อแจ้ง JavaScript
        setcookie('logout_message', 'success', time() + 10, '/');

        // ตั้งค่า flash message
        $this->session->set_flashdata('logout_success', true);

        // Redirect ไปหน้า login
        redirect('User');
    }

    // ===== Helper Functions =====

    /**
     * ฟังก์ชันตรวจสอบ session timeout
     */
    private function check_session_timeout($last_activity_key = 'last_activity')
    {
        $last_activity = $this->session->userdata($last_activity_key);
        $timeout_duration = 7200; // 2 ชั่วโมง (ปรับได้ตาม config)

        if ($last_activity && (time() - $last_activity) > $timeout_duration) {
            // Session หมดอายุ
            $this->session->sess_destroy();
            return false;
        }

        // อัพเดท last activity
        $this->session->set_userdata($last_activity_key, time());
        return true;
    }

    /**
     * ฟังก์ชันบันทึก activity log
     */
    private function log_user_activity($user_id, $user_type, $activity, $details = '')
    {
        $log_data = array(
            'user_id' => $user_id,
            'user_type' => $user_type, // 'staff' หรือ 'public'
            'activity' => $activity,   // 'login', 'logout', 'session_check'
            'details' => $details,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'timestamp' => date('Y-m-d H:i:s')
        );

        // บันทึกลง database (ถ้ามีตาราง activity_logs)
        // $this->db->insert('activity_logs', $log_data);

        // หรือบันทึกลง log file
        log_message('info', 'User Activity: ' . json_encode($log_data));
    }



}