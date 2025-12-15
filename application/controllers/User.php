<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // โหลด database tenant management
        $this->tenant_db = $this->load->database('tenant_management', TRUE);

        // ทำความสะอาด tokens
        $this->cleanup_tokens();

        // ตรวจสอบ domain กับ tenant
        $current_domain = $_SERVER['HTTP_HOST'];
        $tenant = $this->tenant_db->where('domain', $current_domain)
            ->where('is_active', 1)
            ->where('deleted_at IS NULL')
            ->get('tenants')
            ->row();

        if (!$tenant) {
            show_error('Invalid or inactive tenant');
        }

        // อัพเดท session ทุกครั้ง
        $this->session->set_userdata([
            'tenant_id' => $tenant->id,
            'tenant_code' => $tenant->code,
            'tenant_name' => $tenant->name,
            'tenant_domain' => $tenant->domain
        ]);

        $this->tenant = $tenant;
        $this->load->model('member_model');
        $this->load->model('user_log_model');
        $this->load->model('System_config_model');

        // โหลด libraries ที่จำเป็น
        $this->load->library('Google2FA');
        $this->load->library('user_agent'); // เพิ่มบรรทัดนี้
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

    public function index()
    {
        header("Content-Security-Policy: frame-ancestors 'self' https://www.google.com https://www.gstatic.com");

        $previous_page = $this->session->userdata('previous_page');

        // เช็คว่าเข้าสู่ระบบแล้วหรือยัง
        if ($this->session->userdata('m_id')) {
            redirect('User/choice');
        }

        if (!empty($previous_page)) {
            redirect($previous_page);
        }
        $recaptcha = get_config_value('recaptcha');

        $api_data1 = $this->fetch_api_data('https://www.assystem.co.th/service_api/index.php');
        if ($api_data1 !== FALSE) {
            $data['api_data1'] = $api_data1;
        } else {
            $data['api_data1'] = [];
        }

        $this->load->view('asset/css');
        $this->load->view('login_form_admin', $data);
        $this->load->view('asset/js');
    }

    private function fetch_api_data($api_url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $api_data = curl_exec($curl);

        if ($api_data === false) {
            $error_message = curl_error($curl);
            echo "Error: $error_message";
        } else {
            $data = json_decode($api_data, true);
            return $data;
        }
        curl_close($curl);
    }


    /**
     * ✅ ปรับปรุง check2() method ใน User.php controller
     * เพิ่ม reCAPTCHA verification สำหรับ Staff Login
     * เพิ่ม Enhanced Debug Logs สำหรับ 2FA
     */

    public function check2()
    {
        // ✅ เพิ่ม: ตั้งค่า content type และ headers
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type('application/json');
            $this->output->set_header('X-Content-Type-Options: nosniff');
            $this->output->set_header('X-Frame-Options: DENY');
        }

        try {
            // ✅ เพิ่ม comprehensive logging
            log_message('info', "=== STAFF CHECK2 START (Enhanced with reCAPTCHA) ===");
            log_message('info', "Request method: " . $_SERVER['REQUEST_METHOD']);
            log_message('info', "POST data: " . json_encode($this->input->post()));
            log_message('info', "IP Address: " . $this->input->ip_address());
            log_message('info', "User Agent: " . $_SERVER['HTTP_USER_AGENT']);

            // ✅ Debug Session State ตอนเริ่มต้น
            log_message('debug', "Current session state at start:");
            log_message('debug', "- Session ID: " . session_id());
            log_message('debug', "- Has main session: " . ($this->session->userdata('m_id') ? 'YES' : 'NO'));
            log_message('debug', "- Has temp session: " . ($this->session->userdata('temp_m_id') ? 'YES' : 'NO'));
            log_message('debug', "- Requires 2FA: " . ($this->session->userdata('requires_2fa') ? 'YES' : 'NO'));
            log_message('debug', "- Temp user type: " . ($this->session->userdata('temp_user_type') ?? 'NONE'));
            log_message('debug', "- 2FA verified: " . ($this->session->userdata('2fa_verified') ? 'YES' : 'NO'));
            log_message('debug', "- Trusted device: " . ($this->session->userdata('trusted_device') ? 'YES' : 'NO'));

            // ✅ ขั้นตอนที่ 1: ตรวจสอบ reCAPTCHA - เพิ่มใหม่
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $no_recaptcha = $this->input->server('HTTP_X_NO_RECAPTCHA') === 'true';

            // เพิ่ม debug logs แบบละเอียด
            log_message('debug', "Staff reCAPTCHA Debug Info:");
            log_message('debug', "- Token received: " . ($recaptcha_token ? 'YES' : 'NO'));
            log_message('debug', "- Token length: " . strlen($recaptcha_token ?: ''));
            log_message('debug', "- No reCAPTCHA bypass: " . ($no_recaptcha ? 'YES' : 'NO'));

            if ($recaptcha_token) {
                log_message('debug', "- Token preview: " . substr($recaptcha_token, 0, 50) . '...');
            }

            // ✅ บังคับให้ต้องมี reCAPTCHA สำหรับ Staff Login
            if (empty($recaptcha_token) && !$no_recaptcha) {
                log_message('debug', "Staff login attempted without reCAPTCHA token");
                $error_message = 'กรุณายืนยันตัวตน reCAPTCHA ก่อนเข้าสู่ระบบ';

                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $error_message,
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]);
                    return;
                }
                echo "<script>alert('$error_message'); window.history.back();</script>";
                return;
            }

            // ตรวจสอบ reCAPTCHA เมื่อมี token
            if (!empty($recaptcha_token)) {
                log_message('debug', "Starting staff reCAPTCHA verification with Library...");

                // ✅ ใช้ reCAPTCHA Library แทนฟังก์ชันเดิม
                $recaptcha_result = $this->recaptcha_lib->verify_staff_login($recaptcha_token);

                if (!$recaptcha_result['success']) {
                    log_message('error', "Staff reCAPTCHA verification failed for IP: " . $this->input->ip_address());
                    log_message('error', "reCAPTCHA Library Error: " . $recaptcha_result['message']);

                    // ถ้ามี score ให้ log ด้วย
                    if (isset($recaptcha_result['data']['score'])) {
                        log_message('error', "reCAPTCHA Score: " . $recaptcha_result['data']['score']);
                    }

                    $error_message = 'การยืนยันตัวตน reCAPTCHA ล้มเหลว กรุณารีเฟรชหน้าแล้วลองใหม่อีกครั้ง';

                    if ($this->input->is_ajax_request()) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => $error_message,
                            'csrf_hash' => $this->security->get_csrf_hash()
                        ]);
                        return;
                    }
                    echo "<script>alert('$error_message'); window.history.back();</script>";
                    return;
                }

                log_message('info', "✅ Staff reCAPTCHA verification successful with Library");

                // ✅ เพิ่ม log รายละเอียดจาก Library
                if (isset($recaptcha_result['data']['score'])) {
                    log_message('info', "Staff reCAPTCHA Score: " . $recaptcha_result['data']['score']);
                }
                if (isset($recaptcha_result['data']['response_time'])) {
                    log_message('info', "Staff reCAPTCHA Response Time: " . $recaptcha_result['data']['response_time'] . "ms");
                }
            } else {
                log_message('debug', "⚠️ Proceeding without reCAPTCHA verification (bypass enabled)");
            }

            // ตรวจสอบ input (โค้ดเดิม)
            if ($this->input->post('m_username') == '' || $this->input->post('m_password') == '') {
                log_message('debug', "Incomplete staff login data");
                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'กรุณากรอกข้อมูลชื่อผู้ใช้และรหัสผ่าน',
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]);
                    return;
                }
                echo "<script>";
                echo "alert('กรุณากรอกข้อมูลชื่อผู้ใช้และรหัสผ่าน');";
                echo "window.history.back();";
                echo "</script>";
                return;
            }

            // === เพิ่ม: สร้าง fingerprint สำหรับตรวจสอบการบล็อค ===
            $fingerprint = $this->input->post('fingerprint');
            if (empty($fingerprint)) {
                $ip = $this->input->ip_address() ?: '0.0.0.0';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                $fingerprint = md5($ip . $userAgent);
                log_message('debug', "Generated staff fingerprint: " . $fingerprint);
            }

            // === เพิ่ม: ตรวจสอบการถูกบล็อค ===
            log_message('debug', "Checking if staff user is blocked...");
            $block_status = $this->check_if_blocked($fingerprint);

            if ($block_status['is_blocked']) {
                log_message('debug', "Staff user is blocked - Level: " . $block_status['block_level'] . ", Remaining time: " . $block_status['remaining_time']);

                $block_message = 'คุณถูกบล็อคชั่วคราว โปรดรอ';

                if (isset($block_status['block_level']) && $block_status['block_level'] == 2) {
                    $block_message = 'คุณถูกบล็อค 10 นาที เนื่องจากล็อกอินผิดพลาด 6 ครั้ง';
                } else {
                    $block_message = 'คุณถูกบล็อค 3 นาที เนื่องจากล็อกอินผิดพลาด 3 ครั้ง';
                }

                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'blocked',
                        'message' => $block_message,
                        'remaining_time' => $block_status['remaining_time'],
                        'block_level' => $block_status['block_level'] ?? 1,
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]);
                    return;
                }

                echo "<script>";
                echo "alert('" . $block_message . "');";
                echo "window.history.back();";
                echo "</script>";
                return;
            }

            log_message('debug', "Staff user is not blocked, proceeding with login...");

            // ตรวจสอบข้อมูลการเข้าสู่ระบบ (โค้ดเดิม)
            log_message('debug', "Attempting to fetch staff user login data...");
            $result = $this->member_model->fetch_user_login(
                $this->input->post('m_username'),
                sha1($this->input->post('m_password'))
            );

            if (!empty($result)) {
                log_message('info', "Valid staff user found, processing login...");
                log_message('debug', "Staff user data retrieved:");
                log_message('debug', "- m_id: " . $result->m_id);
                log_message('debug', "- m_username: " . $result->m_username);
                log_message('debug', "- m_status: " . ($result->m_status ?? 'not_set'));
                log_message('debug', "- google2fa_enabled: " . ($result->google2fa_enabled ?? 'not_set'));
                log_message('debug', "- has_google2fa_secret: " . (!empty($result->google2fa_secret) ? 'YES' : 'NO'));

                if ($result->m_status == 0) {
                    log_message('debug', "Staff account is disabled: " . $result->m_username);
                    if ($this->input->is_ajax_request()) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'คุณถูกบล็อค โปรดติดต่อผู้ให้บริการ',
                            'csrf_hash' => $this->security->get_csrf_hash()
                        ]);
                        return;
                    }
                    echo "<script>";
                    echo "alert('คุณถูกบล็อค โปรดติดต่อผู้ให้บริการ');";
                    echo "window.history.back();";
                    echo "</script>";
                    return;
                }

                // ดึงข้อมูล tenant (โค้ดเดิม)
                $current_domain = $_SERVER['HTTP_HOST'];
                $tenant = $this->tenant_db->where('domain', $current_domain)
                    ->where('is_active', 1)
                    ->where('deleted_at IS NULL')
                    ->get('tenants')
                    ->row();

                if (!$tenant) {
                    log_message('error', "Tenant not found for domain: " . $current_domain);
                    if ($this->input->is_ajax_request()) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'ไม่พบข้อมูลหน่วยงาน',
                            'csrf_hash' => $this->security->get_csrf_hash()
                        ]);
                        return;
                    }
                    echo "<script>";
                    echo "alert('ไม่พบข้อมูลหน่วยงาน');";
                    echo "window.history.back();";
                    echo "</script>";
                    return;
                }

                // ✅ **สำคัญ: ตรวจสอบ 2FA ก่อนสร้าง session หลัก** (โค้ดเดิม)
                if (!empty($result->google2fa_secret) && $result->google2fa_enabled == 1) {
                    log_message('debug', "=== 2FA VERIFICATION START ===");
                    log_message('debug', "2FA Debug Info for user: " . $result->m_username);
                    log_message('debug', "- User ID: " . $result->m_id);
                    log_message('debug', "- Tenant ID: " . $tenant->id);
                    log_message('debug', "- Tenant Domain: " . $tenant->domain);
                    log_message('debug', "- Google2FA Secret Length: " . strlen($result->google2fa_secret));
                    log_message('debug', "- Google2FA Enabled: " . $result->google2fa_enabled);
                    log_message('debug', "- User Fingerprint: " . $fingerprint);
                    log_message('debug', "- Current Time: " . date('Y-m-d H:i:s'));

                    log_message('info', "2FA is required for staff user: " . $result->m_username);

                    // *** Debug Log สำหรับ Trusted Device Check ***
                    log_message('debug', "Checking trusted device for user_id: " . $result->m_id . ", tenant_id: " . $tenant->id);

                    $trusted_device_result = $this->is_trusted_device($result->m_id, $tenant->id);
                    log_message('debug', "Trusted device check result: " . ($trusted_device_result ? 'FOUND' : 'NOT_FOUND'));

                    if ($trusted_device_result) {
                        log_message('info', "✅ Trusted device found for staff user - skipping 2FA");
                        log_message('debug', "Trusted device details:");
                        log_message('debug', "- Updating trusted device usage timestamp");
                        log_message('debug', "- Proceeding with normal login flow (Skip 2FA)");

                        error_log("Trusted device found for user: " . $result->m_username . " - Skipping 2FA");

                        // อัพเดทการใช้งานล่าสุด
                        $this->update_trusted_device_usage($result->m_id, $tenant->id);

                        // === เพิ่ม: รีเซ็ต failed attempts เมื่อ login สำเร็จ ===
                        $this->reset_failed_attempts($fingerprint);
                        $this->record_login_attempt($result->m_username, 'success', $fingerprint);

                        // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                        $redirect_url = $this->session->userdata('redirect_after_login');

                        if (!$redirect_url) {
                            // ถ้าไม่มี redirect URL ให้ใช้ default
                            $redirect_url = site_url('User/choice');
                        } else {
                            // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                            $this->session->unset_userdata('redirect_after_login');
                        }

                        // สร้าง session ปกติ (Skip 2FA)
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
                            'tenant_id' => $tenant->id,
                            'tenant_code' => $tenant->code,
                            'tenant_name' => $tenant->name,
                            'tenant_domain' => $tenant->domain,
                            '2fa_verified' => true,
                            'trusted_device' => true
                        );
                        $this->session->set_userdata($sess);

                        log_message('debug', "✅ 2FA SKIPPED - Session created successfully for trusted device");
                        log_message('debug', "=== 2FA VERIFICATION END (TRUSTED DEVICE) ===");

                        if ($this->input->is_ajax_request()) {
                            echo json_encode([
                                'status' => 'success',
                                'message' => 'เข้าสู่ระบบสำเร็จ (Trusted Device)',
                                'redirect' => $redirect_url,
                                'csrf_hash' => $this->security->get_csrf_hash()
                            ]);
                            return;
                        }

                        redirect($redirect_url);
                        return;
                    } else {
                        log_message('info', "❌ No trusted device found - 2FA required for staff");
                        log_message('debug', "2FA Required - Creating temporary session");

                        // Debug log สำหรับ session cleanup
                        log_message('debug', "Clearing main session data to prevent bypass");
                        $cleared_sessions = [
                            'm_id',
                            'm_level',
                            'grant_system_ref_id',
                            'grant_user_ref_id',
                            'm_system',
                            'm_fname',
                            'm_lname',
                            'm_username',
                            'm_img',
                            'tenant_id',
                            'tenant_code',
                            'tenant_name',
                            'tenant_domain'
                        ];
                        log_message('debug', "Cleared session keys: " . implode(', ', $cleared_sessions));

                        // ลบ session หลักทิ้งก่อน (ป้องกัน bypass)
                        $this->session->unset_userdata([
                            'm_id',
                            'm_level',
                            'grant_system_ref_id',
                            'grant_user_ref_id',
                            'm_system',
                            'm_fname',
                            'm_lname',
                            'm_username',
                            'm_img',
                            'tenant_id',
                            'tenant_code',
                            'tenant_name',
                            'tenant_domain'
                        ]);

                        // === เพิ่ม: รีเซ็ต failed attempts เมื่อผ่านการตรวจสอบ username/password ===
                        $this->reset_failed_attempts($fingerprint);
                        $this->record_login_attempt($result->m_username, 'success', $fingerprint);

                        // Debug log สำหรับ temp session creation
                        log_message('debug', "Creating temporary session for 2FA verification");
                        log_message('debug', "Temp session data prepared:");
                        log_message('debug', "- temp_m_id: " . $result->m_id);
                        log_message('debug', "- temp_m_username: " . $result->m_username);
                        log_message('debug', "- temp_tenant_id: " . $tenant->id);
                        log_message('debug', "- temp_login_time: " . time());
                        log_message('debug', "- temp_user_type: staff");
                        log_message('debug', "- requires_2fa: true");
                        log_message('debug', "- google2fa_secret_set: " . (!empty($result->google2fa_secret) ? 'YES' : 'NO'));

                        // เก็บข้อมูลชั่วคราวสำหรับ 2FA เท่านั้น
                        $temp_data = array(
                            'temp_m_id' => $result->m_id,
                            'temp_m_level' => $result->ref_pid,
                            'temp_grant_system_ref_id' => $result->grant_system_ref_id,
                            'temp_grant_user_ref_id' => $result->grant_user_ref_id,
                            'temp_m_system' => $result->m_system,
                            'temp_m_fname' => $result->m_fname,
                            'temp_m_lname' => $result->m_lname,
                            'temp_m_username' => $result->m_username,
                            'temp_m_img' => $result->m_img,
                            'temp_tenant_id' => $tenant->id,
                            'temp_tenant_code' => $tenant->code,
                            'temp_tenant_name' => $tenant->name,
                            'temp_tenant_domain' => $tenant->domain,
                            'temp_google2fa_secret' => $result->google2fa_secret,
                            'temp_login_time' => time(),
                            'temp_login_expires' => time() + 300, // ✅ เพิ่ม 5 นาทีหมดอายุ
                            'requires_2fa' => true,
                            'temp_user_type' => 'staff' // *** เพิ่ม: ระบุประเภทผู้ใช้ ***
                        );
                        $this->session->set_userdata($temp_data);

                        log_message('debug', "✅ Temporary session created successfully");
                        log_message('debug', "🔄 Redirecting to 2FA verification page");

                        error_log("2FA Required for staff user: " . $result->m_username);
                        error_log("Temp session created with requires_2fa flag");
                        error_log("Fingerprint: " . $fingerprint);
                        error_log("Session ID: " . session_id());

                        if ($this->input->is_ajax_request()) {
                            log_message('debug', "Sending AJAX response for 2FA requirement");
                            log_message('debug', "Response will include: requires_2fa, show_google_auth, temp_user_type");

                            echo json_encode([
                                'status' => 'requires_2fa',
                                'message' => 'ต้องการยืนยันตัวตน 2FA',
                                'show_google_auth' => true,
                                'requires_verification' => true,
                                'temp_user_type' => 'staff', // *** เพิ่ม: ส่งประเภทผู้ใช้ไปยัง JavaScript ***
                                'csrf_hash' => $this->security->get_csrf_hash()
                            ]);

                            log_message('debug', "✅ AJAX response sent for 2FA requirement");
                            log_message('debug', "=== 2FA VERIFICATION END (AJAX) ===");
                            return;
                        }

                        // Debug log สำหรับ non-AJAX response
                        log_message('debug', "Preparing view for 2FA verification (non-AJAX)");
                        log_message('debug', "Loading login_form_admin view with 2FA flags");

                        $data['show_google_auth'] = true;
                        $data['requires_2fa'] = true;
                        $data['temp_user_type'] = 'staff'; // *** เพิ่ม: ส่งไปยัง View ***

                        log_message('debug', "View data prepared:");
                        log_message('debug', "- show_google_auth: true");
                        log_message('debug', "- requires_2fa: true");
                        log_message('debug', "- temp_user_type: staff");

                        $data['api_data1'] = $this->fetch_api_data('https://www.assystem.co.th/service_api/index.php');
                        if ($data['api_data1'] === FALSE) {
                            $data['api_data1'] = [];
                        }

                        $this->load->view('asset/css');
                        $this->load->view('login_form_admin', $data);
                        $this->load->view('asset/js');

                        log_message('debug', "✅ Loading 2FA verification view");
                        log_message('debug', "=== 2FA VERIFICATION END (VIEW) ===");
                        return;
                    }
                } else {
                    log_message('info', "No 2FA required for staff user - proceeding with normal login");
                    log_message('debug', "2FA Status:");
                    log_message('debug', "- google2fa_secret: " . (!empty($result->google2fa_secret) ? 'SET' : 'NOT_SET'));
                    log_message('debug', "- google2fa_enabled: " . ($result->google2fa_enabled ?? 'NOT_SET'));
                    log_message('debug', "- 2FA requirement: NOT_REQUIRED");
                }

                // จัดการ remember me (เฉพาะกรณีไม่มี 2FA)
                if ($this->input->post('remember_me')) {
                    $this->input->set_cookie('remember', json_encode([
                        'm_username' => $result->m_username,
                        'm_password' => $this->input->post('m_password')
                    ]), 3600 * 24 * 30);
                } else {
                    setcookie('remember', '', time() - 3600, '/');
                }

                // === เพิ่ม: รีเซ็ต failed attempts เมื่อ login สำเร็จ (กรณีไม่มี 2FA) ===
                $this->reset_failed_attempts($fingerprint);
                $this->record_login_attempt($result->m_username, 'success', $fingerprint);

                // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                $redirect_url = $this->session->userdata('redirect_after_login');

                if (!$redirect_url) {
                    // ถ้าไม่มี redirect URL ให้ใช้ default
                    $redirect_url = site_url('User/choice');
                } else {
                    // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                    $this->session->unset_userdata('redirect_after_login');
                }

                // สร้าง session ปกติ (กรณีไม่มี 2FA)
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
                    'tenant_id' => $tenant->id,
                    'tenant_code' => $tenant->code,
                    'tenant_name' => $tenant->name,
                    'tenant_domain' => $tenant->domain,
                    '2fa_verified' => false
                );
                $this->session->set_userdata($sess);

                error_log("Normal staff login (no 2FA) for user: " . $result->m_username);
                log_message('info', "Staff user login successful (no 2FA): " . $result->m_username);

                // บันทึก activity log
                if ($this->user_log_model) {
                    $this->user_log_model->log_activity(
                        $result->m_username,
                        'login',
                        'User logged in'
                    );
                }

                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'เข้าสู่ระบบสำเร็จ',
                        'redirect' => $redirect_url,
                        'csrf_hash' => $this->security->get_csrf_hash()
                    ]);
                    return;
                }

                redirect($redirect_url);

            } else {
                log_message('debug', "Staff login failed - invalid credentials");
                log_message('debug', "Login failure context:");
                log_message('debug', "- Username: " . $this->input->post('m_username'));
                log_message('debug', "- Password length: " . strlen($this->input->post('m_password')));
                log_message('debug', "- Fingerprint: " . $fingerprint);
                log_message('debug', "- Had reCAPTCHA: " . (!empty($recaptcha_token) ? 'YES' : 'NO'));
                log_message('debug', "- Current session requires_2fa: " . ($this->session->userdata('requires_2fa') ? 'YES' : 'NO'));
                log_message('debug', "- Current session temp_user_type: " . ($this->session->userdata('temp_user_type') ?? 'NONE'));

                // === เพิ่ม: จัดการกรณี Login ล้มเหลว ===
                $username = $this->input->post('m_username');
                $password = $this->input->post('m_password');

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
                if (isset($this->user_log_model)) {
                    $this->user_log_model->log_detect(
                        $username,
                        $password,
                        'staff',
                        'failed',
                        'Staff user login failed by "' . $password . '" (Enhanced with reCAPTCHA)',
                        'auth'
                    );
                }

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

                log_message('debug', "Staff login failure details:");
                log_message('debug', "- Failed attempts: " . $attempts_info);
                log_message('debug', "- Remaining attempts: " . $remaining_attempts);
                log_message('debug', "- Block level: " . $block_level);
                log_message('debug', "- Block duration: " . $block_duration . " seconds");
                log_message('debug', "- Block history after reset: " . ($block_history_after_reset ? 'YES' : 'NO'));
                log_message('debug', "- Cutoff time: " . $cutoff_time);

                if ($remaining_attempts <= 0) {
                    log_message('debug', "Blocking staff user due to too many failed attempts: " . $username);
                    log_message('info', "🚫 BLOCKING USER: " . $username . " (Level: " . $block_level . ")");

                    // บล็อคตามระดับ
                    $this->block_login($fingerprint, $block_level);

                    // === เรียกใช้ Function แจ้งเตือนไปที่ Line ===
                    if (method_exists($this, 'send_security_alert')) {
                        log_message('debug', "Sending security alert for blocked user");
                        $this->send_security_alert($username, $attempts_info, 'staff', $block_level);
                    }

                    $block_message = ($block_level == 2) ?
                        'คุณถูกบล็อค 10 นาที เนื่องจากล็อกอินผิดพลาด 6 ครั้ง' :
                        'คุณถูกบล็อค 3 นาที เนื่องจากล็อกอินผิดพลาด 3 ครั้ง';

                    if ($this->input->is_ajax_request()) {
                        echo json_encode([
                            'status' => 'blocked',
                            'message' => $block_message,
                            'remaining_time' => $block_duration,
                            'block_level' => $block_level,
                            'csrf_hash' => $this->security->get_csrf_hash()
                        ]);
                        return;
                    }

                    echo "<script>";
                    echo "alert('" . $block_message . "');";
                    echo "window.history.back();";
                    echo "</script>";
                } else {
                    log_message('debug', "Login failed but not blocked yet - " . $remaining_attempts . " attempts remaining");

                    $error_message = 'รหัสผ่านหรือชื่อผู้ใช้งานไม่ถูกต้อง';
                    if ($remaining_attempts > 0) {
                        $error_message .= " (เหลืออีก {$remaining_attempts} ครั้ง)";
                    }

                    if ($this->input->is_ajax_request()) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => $error_message,
                            'attempts' => $attempts_info,
                            'remaining_attempts' => $remaining_attempts,
                            'next_block_level' => $block_level ? $block_level : 1,
                            'csrf_hash' => $this->security->get_csrf_hash()
                        ]);
                        return;
                    }

                    echo "<script>";
                    echo "alert('" . $error_message . "');";
                    echo "window.history.back();";
                    echo "</script>";
                }

                // บันทึก log
                log_message('debug', 'Staff user login failed: ' . $username);
                log_message('debug', 'Total failed attempts in current session: ' . $attempts_info);
            }

        } catch (Exception $e) {
            log_message('error', "FATAL ERROR in staff check2: " . $e->getMessage());
            log_message('error', "Stack trace: " . $e->getTraceAsString());
            log_message('error', "Error occurred at line: " . $e->getLine());
            log_message('error', "Error occurred in file: " . $e->getFile());

            // ✅ เพิ่ม context logging เมื่อเกิด error
            log_message('error', "Error context:");
            log_message('error', "- POST data: " . json_encode($this->input->post()));
            log_message('error', "- Session data: " . json_encode($this->session->all_userdata()));
            log_message('error', "- IP Address: " . $this->input->ip_address());
            log_message('error', "- User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'));

            $error_message = 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง';

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => $error_message,
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]);
                return;
            }

            echo "<script>alert('$error_message'); window.history.back();</script>";
        }

        log_message('info', "=== STAFF CHECK2 END ===");
        log_message('debug', "Final session state:");
        log_message('debug', "- Has main session: " . ($this->session->userdata('m_id') ? 'YES' : 'NO'));
        log_message('debug', "- Has temp session: " . ($this->session->userdata('temp_m_id') ? 'YES' : 'NO'));
        log_message('debug', "- Requires 2FA: " . ($this->session->userdata('requires_2fa') ? 'YES' : 'NO'));
        log_message('debug', "- 2FA verified: " . ($this->session->userdata('2fa_verified') ? 'YES' : 'NO'));
        log_message('debug', "- Trusted device: " . ($this->session->userdata('trusted_device') ? 'YES' : 'NO'));
    }


    public function save_redirect_url()
    {
        $redirect_url = $this->input->post('redirect_url');

        if ($redirect_url) {
            $this->session->set_userdata('redirect_after_login', $redirect_url);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'บันทึก redirect URL สำเร็จ'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'ไม่พบ redirect URL'
                ]));
        }
    }





    // === เพิ่ม: ฟังก์ชันสำหรับจัดการการบล็อค (คัดลอกมาจาก Auth_api) ===

    /**
     * ตรวจสอบว่า fingerprint นี้ถูกบล็อคอยู่หรือไม่
     */
    private function check_if_blocked($fingerprint)
    {
        try {
            // ตรวจสอบว่าตาราง tbl_member_login_attempts มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                return ['is_blocked' => false, 'remaining_time' => 0, 'block_level' => 0];
            }

            // นับจำนวนครั้งที่ล้มเหลวในช่วง 30 นาทีที่ผ่านมา
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'failed');
            $this->db->where('attempt_time >', date('Y-m-d H:i:s', time() - 1800)); // 30 นาที
            $failed_attempts = $this->db->count_all_results('tbl_member_login_attempts');

            // เช็คประวัติการบล็อค
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
                // ดึงเวลาล้มเหลวล่าสุด
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
                        $result['block_level'] = 2;
                    } else {
                        // กรณีล้มเหลวครบ 3 ครั้งแรก
                        $block_duration = 3 * 60; // 3 นาที
                        $result['block_level'] = 1;
                    }

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
     * บันทึกความพยายามเข้าสู่ระบบ
     */
    private function record_login_attempt($username, $status, $fingerprint = null)
    {
        try {
            // ตรวจสอบว่าตาราง tbl_member_login_attempts มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                error_log('Table tbl_member_login_attempts does not exist');
                return false;
            }

            // หาก fingerprint ไม่ถูกส่งมา ให้ดึงจาก POST
            if ($fingerprint === null) {
                $fingerprint = $this->input->post('fingerprint');
            }

            // หาก fingerprint ยังเป็น null ให้สร้างจาก IP และ User Agent
            if (empty($fingerprint)) {
                $ip = $this->input->ip_address() ?: '0.0.0.0';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                $fingerprint = md5($ip . $userAgent);
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
            // ตรวจสอบจำนวนครั้งที่ล้มเหลวเพื่อกำหนดระดับการบล็อค
            $failed_attempts = $this->count_failed_attempts($fingerprint);

            // เช็คประวัติการบล็อค
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('status', 'blocked');
            $this->db->order_by('attempt_time', 'DESC');
            $block_history = $this->db->get('tbl_member_login_attempts')->row();

            // ใช้ค่า block_level ที่ส่งมา หรือคำนวณหากไม่ได้ระบุ
            if ($block_level === null || $block_level <= 0) {
                $block_level = ($block_history && $failed_attempts >= 6) ? 2 : 1;
            }

            // บันทึกข้อมูลการบล็อค
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
     * นับจำนวนครั้งที่เข้าสู่ระบบล้มเหลว
     */
    private function count_failed_attempts($fingerprint)
    {
        try {
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                return 0;
            }

            // นับเฉพาะการเข้าสู่ระบบล้มเหลวภายใน 30 นาทีล่าสุด
            $cutoff_time = date('Y-m-d H:i:s', time() - 1800); // 30 นาที

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
     * รีเซ็ตการนับจำนวนครั้งล็อกอินที่ล้มเหลว
     */
    private function reset_failed_attempts($fingerprint)
    {
        try {
            if (!$this->db->table_exists('tbl_member_login_attempts')) {
                return false;
            }

            // ลบรายการความพยายามล็อกอินที่ล้มเหลวของ fingerprint นี้
            $this->db->where('fingerprint', $fingerprint)
                ->where('status', 'failed')
                ->delete('tbl_member_login_attempts');

            // บันทึกการรีเซ็ตประวัติ
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
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            $ipAddress = $this->input->ip_address() ?: '0.0.0.0';
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

    // ตรวจสอบ OTP
    public function verify_otp()
    {
        try {
            // ตรวจสอบว่ามี temp session และ requires_2fa หรือไม่
            if (!$this->session->userdata('temp_m_id') || !$this->session->userdata('requires_2fa')) {
                error_log("OTP verification attempted without proper temp session");
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'Session หมดอายุ', 'redirect' => site_url('User')]);
                    return;
                }
                redirect('User');
                return;
            }

            $otp = $this->input->post('otp');
            $remember_device = $this->input->post('remember_device'); // *** เพิ่มการรับค่า remember_device ***
            $secret = $this->session->userdata('temp_google2fa_secret');

            error_log("OTP verification attempt - OTP: $otp, Remember Device: $remember_device");

            if (empty($otp)) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกรหัส OTP']);
                    return;
                }

                echo "<script>";
                echo "alert('กรุณากรอกรหัส OTP');";
                echo "window.history.back();";
                echo "</script>";
                return;
            }

            // ตรวจสอบเวลาหมดอายุ (10 นาที)
            $login_time = $this->session->userdata('temp_login_time');
            if (!$login_time || (time() - $login_time) > 600) {
                $this->clear_temp_session();

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'หมดเวลาการยืนยัน กรุณาเข้าสู่ระบบใหม่', 'redirect' => site_url('User')]);
                    return;
                }

                echo "<script>";
                echo "alert('หมดเวลาการยืนยัน กรุณาเข้าสู่ระบบใหม่');";
                echo "window.location.href = '" . site_url('User') . "';";
                echo "</script>";
                return;
            }

            // ตรวจสอบ OTP
            if ($this->google2fa->verifyKey($secret, $otp)) {
                $user_id = $this->session->userdata('temp_m_id');
                $tenant_id = $this->session->userdata('temp_tenant_id');

                error_log("OTP verified successfully for user_id: $user_id");

                // *** เพิ่ม: บันทึก Trusted Device ถ้าผู้ใช้เลือก ***
                $trusted_device_saved = false;
                if ($remember_device == '1') {
                    error_log("Attempting to save trusted device for user_id: $user_id");

                    // ตรวจสอบว่า table trusted_devices มีอยู่หรือไม่
                    if ($this->db->table_exists('trusted_devices')) {
                        $device_token = $this->save_trusted_device($user_id, $tenant_id);
                        if ($device_token) {
                            $trusted_device_saved = true;
                            error_log("Trusted device saved successfully with token: " . substr($device_token, 0, 8) . "...");
                        } else {
                            error_log("Failed to save trusted device");
                        }
                    } else {
                        error_log("Table 'trusted_devices' does not exist");
                    }
                }

                // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                $redirect_url = $this->session->userdata('redirect_after_login');

                if (!$redirect_url) {
                    // ถ้าไม่มี redirect URL ให้ใช้ default
                    $redirect_url = site_url('User/choice');
                } else {
                    // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                    $this->session->unset_userdata('redirect_after_login');
                }

                // OTP ถูกต้อง สร้าง session จริง
                $sess = array(
                    'm_id' => $user_id,
                    'm_level' => $this->session->userdata('temp_m_level'),
                    'grant_system_ref_id' => $this->session->userdata('temp_grant_system_ref_id'),
                    'grant_user_ref_id' => $this->session->userdata('temp_grant_user_ref_id'),
                    'm_system' => $this->session->userdata('temp_m_system'),
                    'm_fname' => $this->session->userdata('temp_m_fname'),
                    'm_lname' => $this->session->userdata('temp_m_lname'),
                    'm_username' => $this->session->userdata('temp_m_username'),
                    'm_img' => $this->session->userdata('temp_m_img'),
                    'tenant_id' => $tenant_id,
                    'tenant_code' => $this->session->userdata('temp_tenant_code'),
                    'tenant_name' => $this->session->userdata('temp_tenant_name'),
                    'tenant_domain' => $this->session->userdata('temp_tenant_domain'),
                    '2fa_verified' => true, // เพิ่ม flag นี้
                    'trusted_device' => $trusted_device_saved // เพิ่ม flag นี้
                );
                $this->session->set_userdata($sess);

                error_log("2FA verification successful for user: " . $this->session->userdata('temp_m_username'));

                // ลบข้อมูลชั่วคราว
                $this->clear_temp_session();

                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'ยืนยันตัวตนสำเร็จ',
                        'redirect' => $redirect_url
                    ]);
                    return;
                }

                redirect($redirect_url);
            } else {
                error_log("Invalid OTP attempt for user: " . $this->session->userdata('temp_m_username'));

                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'รหัส OTP ไม่ถูกต้อง']);
                    return;
                }

                echo "<script>";
                echo "alert('รหัส OTP ไม่ถูกต้อง');";
                echo "window.history.back();";
                echo "</script>";
            }

        } catch (Exception $e) {
            error_log("Exception in verify_otp: " . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง']);
                return;
            }

            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง');";
            echo "window.history.back();";
            echo "</script>";
        }
    }

    // *** เพิ่มฟังก์ชันใหม่สำหรับจัดการ Trusted Device ***

    /**
     * ตรวจสอบว่าเครื่องนี้เป็น trusted device หรือไม่
     */
    private function is_trusted_device($user_id, $tenant_id, $user_type = 'staff')
    {
        $device_fingerprint = $this->generate_device_fingerprint();
        $current_time = date('Y-m-d H:i:s');

        error_log("=== TRUSTED DEVICE CHECK ($user_type) ===");
        error_log("User ID: $user_id, Tenant ID: $tenant_id, User Type: $user_type");
        error_log("Current fingerprint: $device_fingerprint");

        // ทำความสะอาด expired devices ก่อน
        $this->db->where('expires_at <', $current_time)->delete('trusted_devices');

        // ค้นหา trusted device พร้อม user_type
        $this->db->select('*');
        $this->db->where('user_id', (int) $user_id);
        $this->db->where('user_type', $user_type); // *** เพิ่มบรรทัดนี้ ***
        $this->db->where('tenant_id', (int) $tenant_id);
        $this->db->where('device_fingerprint', $device_fingerprint);
        $this->db->where('expires_at >', $current_time);
        $trusted = $this->db->get('trusted_devices');

        $is_trusted = $trusted->num_rows() > 0;

        error_log("SQL Query: " . $this->db->last_query());
        error_log("Found devices: " . $trusted->num_rows());

        if ($is_trusted) {
            $device_info = $trusted->row();
            error_log("✅ TRUSTED DEVICE FOUND for $user_type!");
            error_log("Device ID: " . $device_info->id);
            error_log("Expires at: " . $device_info->expires_at);
        } else {
            error_log("❌ NO TRUSTED DEVICE FOUND for $user_type user");
        }

        return $is_trusted;
    }

    /**
     * 2. อัปเดตฟังก์ชัน save_trusted_device
     */
    private function save_trusted_device($user_id, $tenant_id, $user_type = 'staff', $duration_hours = 720)
    {
        try {
            $device_token = bin2hex(random_bytes(32));
            $device_fingerprint = $this->generate_device_fingerprint();

            error_log("Saving trusted device for $user_type with fingerprint: " . substr($device_fingerprint, 0, 16) . "...");

            // ข้อมูลอุปกรณ์
            $browser = 'Unknown';
            $version = 'Unknown';
            $platform = 'Unknown';

            if ($this->agent) {
                $browser = $this->agent->browser() ?: 'Unknown';
                $version = $this->agent->version() ?: 'Unknown';
                $platform = $this->agent->platform() ?: 'Unknown';
            }

            $device_info = [
                'user_agent' => $this->input->user_agent() ?: 'Unknown',
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0',
                'browser' => $browser,
                'version' => $version,
                'platform' => $platform,
                'screen_resolution' => $this->input->post('screen_resolution') ?: 'Unknown',
                'timezone' => $this->input->post('timezone') ?: 'Unknown',
                'saved_at' => date('Y-m-d H:i:s')
            ];

            // ลบ trusted device เดิมของ fingerprint นี้ก่อน
            $this->db->where('user_id', $user_id)
                ->where('user_type', $user_type) // *** เพิ่มบรรทัดนี้ ***
                ->where('tenant_id', $tenant_id)
                ->where('device_fingerprint', $device_fingerprint)
                ->delete('trusted_devices');

            // ลบ trusted devices เก่าของผู้ใช้นี้
            $this->cleanup_old_trusted_devices($user_id, $tenant_id, $user_type);

            // บันทึก trusted device ใหม่
            $data = [
                'user_id' => (int) $user_id,
                'user_type' => $user_type, // *** เพิ่มบรรทัดนี้ ***
                'tenant_id' => (int) $tenant_id,
                'device_token' => $device_token,
                'device_fingerprint' => $device_fingerprint,
                'device_info' => json_encode($device_info),
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0',
                'user_agent' => substr($this->input->user_agent() ?: 'Unknown', 0, 500),
                'expires_at' => date('Y-m-d H:i:s', time() + ($duration_hours * 3600)),
                'last_used_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('trusted_devices', $data);

            if ($this->db->error()['code'] !== 0) {
                error_log("Database error in save_trusted_device: " . print_r($this->db->error(), true));
                return false;
            }

            error_log("Trusted device saved successfully for $user_type user_id: $user_id");

            return $device_token;

        } catch (Exception $e) {
            error_log("Exception in save_trusted_device: " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้าง device fingerprint (ใช้เฉพาะข้อมูลที่มีอยู่เสมอ)
     */
    private function generate_device_fingerprint()
    {
        // ใช้เฉพาะข้อมูลที่มีอยู่เสมอและไม่เปลี่ยนแปลง
        $user_agent = $this->input->user_agent() ?: '';
        $accept_language = $this->input->server('HTTP_ACCEPT_LANGUAGE') ?: '';
        $accept_encoding = $this->input->server('HTTP_ACCEPT_ENCODING') ?: '';
        $accept = $this->input->server('HTTP_ACCEPT') ?: '';

        // สร้าง fingerprint string
        $fingerprint_string = implode('|', [
            trim($user_agent),
            trim($accept_language),
            trim($accept_encoding),
            trim($accept)
        ]);

        $fingerprint = hash('sha256', $fingerprint_string);

        error_log("=== FINGERPRINT GENERATION ===");
        error_log("User Agent: $user_agent");
        error_log("Accept Language: $accept_language");
        error_log("Accept Encoding: $accept_encoding");
        error_log("Accept: $accept");
        error_log("Fingerprint String: $fingerprint_string");
        error_log("Generated Fingerprint: $fingerprint");
        error_log("=== END FINGERPRINT ===");

        return $fingerprint;
    }

    /**
     * อัพเดทการใช้งานล่าสุดของ trusted device
     */
    private function update_trusted_device_usage($user_id, $tenant_id, $user_type = 'staff')
    {
        $device_fingerprint = $this->generate_device_fingerprint();

        $this->db->where('user_id', $user_id)
            ->where('user_type', $user_type) // *** เพิ่มบรรทัดนี้ ***
            ->where('tenant_id', $tenant_id)
            ->where('device_fingerprint', $device_fingerprint)
            ->set('last_used_at', date('Y-m-d H:i:s'))
            ->update('trusted_devices');
    }

    /**
     * ลบ trusted devices เก่า (เก็บไว้แค่ 5 devices ล่าสุดต่อผู้ใช้)
     */
    private function cleanup_old_trusted_devices($user_id, $tenant_id, $user_type = 'staff', $keep_limit = 5)
    {
        // ลบ devices ที่หมดอายุ
        $this->db->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete('trusted_devices');

        // ลบ devices เก่าเกิน limit
        $devices = $this->db->where('user_id', $user_id)
            ->where('user_type', $user_type) // *** เพิ่มบรรทัดนี้ ***
            ->where('tenant_id', $tenant_id)
            ->order_by('last_used_at', 'DESC')
            ->get('trusted_devices')
            ->result();

        if (count($devices) >= $keep_limit) {
            $devices_to_delete = array_slice($devices, $keep_limit - 1);
            foreach ($devices_to_delete as $device) {
                $this->db->where('id', $device->id)->delete('trusted_devices');
            }
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


    /**
     * Debug function - ดูรายการ trusted devices (สำหรับ test)
     */
    public function debug_trusted_devices()
    {
        if (!$this->session->userdata('m_id')) {
            echo "Please login first";
            return;
        }

        $user_id = $this->session->userdata('m_id');
        $tenant_id = $this->session->userdata('tenant_id');
        $current_fingerprint = $this->generate_device_fingerprint();

        echo "<h3>Debug Trusted Devices</h3>";
        echo "<p><strong>User ID:</strong> $user_id</p>";
        echo "<p><strong>Tenant ID:</strong> $tenant_id</p>";
        echo "<p><strong>Current Device Fingerprint:</strong> " . substr($current_fingerprint, 0, 32) . "...</p>";
        echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

        echo "<h4>All Trusted Devices:</h4>";
        $devices = $this->db->where('user_id', $user_id)
            ->where('tenant_id', $tenant_id)
            ->order_by('created_at', 'DESC')
            ->get('trusted_devices')
            ->result();

        if (empty($devices)) {
            echo "<p>No trusted devices found.</p>";
        } else {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Fingerprint</th><th>Match</th><th>Expires</th><th>Status</th><th>Device Info</th></tr>";

            foreach ($devices as $device) {
                $is_match = ($device->device_fingerprint === $current_fingerprint);
                $is_expired = (strtotime($device->expires_at) < time());
                $status = $is_expired ? 'EXPIRED' : 'ACTIVE';

                echo "<tr>";
                echo "<td>" . $device->id . "</td>";
                echo "<td>" . substr($device->device_fingerprint, 0, 16) . "...</td>";
                echo "<td>" . ($is_match ? 'YES' : 'NO') . "</td>";
                echo "<td>" . $device->expires_at . "</td>";
                echo "<td style='color: " . ($is_expired ? 'red' : 'green') . "'>" . $status . "</td>";

                $device_info = json_decode($device->device_info, true);
                echo "<td>";
                if ($device_info) {
                    echo "Browser: " . ($device_info['browser'] ?? 'Unknown') . "<br>";
                    echo "Platform: " . ($device_info['platform'] ?? 'Unknown') . "<br>";
                    echo "IP: " . ($device_info['ip_address'] ?? 'Unknown') . "<br>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // เพิ่มข้อมูลการตรวจสอบ
        echo "<h4>Current Check Result:</h4>";
        $is_trusted = $this->is_trusted_device($user_id, $tenant_id);
        echo "<p><strong>Is Trusted Device:</strong> " . ($is_trusted ? 'YES' : 'NO') . "</p>";
    }

    /**
     * API สำหรับลบ trusted device
     */
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


    private function clear_temp_session()
    {
        $this->session->unset_userdata([
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
            'requires_2fa' // เพิ่มการลบ flag นี้ด้วย
        ]);
    }

    // แทนที่ในส่วน method choice() ของไฟล์ User.php

    public function choice()
    {
        if (!$this->session->userdata('m_id')) {
            redirect('User');
            return;
        }

        // **สำคัญ: ตรวจสอบว่าผ่าน 2FA แล้วหรือยัง**
        $user_id = $this->session->userdata('m_id');

        // ตรวจสอบว่ามี method get_2fa_info หรือไม่
        if (method_exists($this->member_model, 'get_2fa_info')) {
            $user_2fa_info = $this->member_model->get_2fa_info($user_id);

            // ถ้าผู้ใช้เปิด 2FA แต่ยังไม่ได้ verify
            if ($user_2fa_info && !empty($user_2fa_info->google2fa_secret) && $user_2fa_info->google2fa_enabled == 1) {
                // ตรวจสอบว่าผ่าน 2FA verification แล้วหรือยัง
                if (!$this->session->userdata('2fa_verified')) {
                    error_log("User " . $this->session->userdata('m_username') . " tried to bypass 2FA");

                    // ลบ session และกลับไปหน้า login
                    $this->session->unset_userdata([
                        'm_id',
                        'm_level',
                        'grant_system_ref_id',
                        'grant_user_ref_id',
                        'm_system',
                        'm_fname',
                        'm_lname',
                        'm_username',
                        'm_img',
                        'tenant_id',
                        'tenant_code',
                        'tenant_name',
                        'tenant_domain'
                    ]);

                    $this->session->set_flashdata('error', 'กรุณายืนยันตัวตนผ่าน 2FA ก่อนเข้าใช้งาน');
                    redirect('User');
                    return;
                }
            }
        } else {
            error_log("Method get_2fa_info not found in member_model");
        }

        $tenant = $this->tenant;

        if (!$this->session->userdata('tenant_id')) {
            $this->session->set_userdata([
                'tenant_id' => $tenant->id,
                'tenant_code' => $tenant->code,
                'tenant_name' => $tenant->name,
                'tenant_domain' => $tenant->domain
            ]);
        }

        // ✅ ดึงข้อมูลโปรไฟล์ผู้ใช้พร้อม 2FA status (แก้ไขให้ชัดเจน)
        $data['user_profile'] = $this->db->select('m.*, p.pname')
            ->from('tbl_member m')
            ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
            ->where('m.m_id', $user_id)
            ->get()
            ->row();

        // ✅ แก้ไขการเช็ค 2FA status ให้ถูกต้อง
        if ($data['user_profile']) {
            // Debug: แสดงค่าจริงจากฐานข้อมูล
            error_log("=== 2FA STATUS DEBUG ===");
            error_log("User ID: " . $user_id);
            error_log("google2fa_secret: " . ($data['user_profile']->google2fa_secret ? '[EXISTS]' : '[EMPTY]'));
            error_log("google2fa_enabled: " . $data['user_profile']->google2fa_enabled);
            error_log("google2fa_setup_date: " . $data['user_profile']->google2fa_setup_date);

            // ✅ เช็คสถานะ 2FA อย่างชัดเจน
            $has_secret = !empty($data['user_profile']->google2fa_secret) && trim($data['user_profile']->google2fa_secret) !== '';
            $is_enabled = $data['user_profile']->google2fa_enabled == 1;

            $data['user_profile']->has_2fa = $has_secret && $is_enabled;
            $data['user_profile']->need_2fa_setup = !$has_secret || !$is_enabled;

            // Debug log
            error_log("Has Secret: " . ($has_secret ? 'YES' : 'NO'));
            error_log("Is Enabled: " . ($is_enabled ? 'YES' : 'NO'));
            error_log("Final has_2fa: " . ($data['user_profile']->has_2fa ? 'YES' : 'NO'));
            error_log("Final need_2fa_setup: " . ($data['user_profile']->need_2fa_setup ? 'YES' : 'NO'));
            error_log("=== END 2FA DEBUG ===");
        }

        $api_data1 = $this->fetch_api_data('https://www.assystem.co.th/service_api/index.php');
        if ($api_data1 !== FALSE) {
            $data['api_data1'] = $api_data1;
        } else {
            $data['api_data1'] = [];
        }

        $this->load->view('asset/css');
        $this->load->view('choice', $data);


        // ✅ ตรวจสอบว่า Google Drive System พร้อมใช้งานหรือไม่
        $data['google_drive_available'] = $this->check_google_drive_availability();
        // ✅ แก้ไข: โหลด Google Drive Auto Token Refresh เฉพาะเมื่อระบบพร้อม
        if ($data['google_drive_available']) {
            $this->load->view('member/google_drive_auto_token_js');
        }


        // $this->load->view('asset/js');
    }





    /**
     * ✅ ตรวจสอบความพร้อมของ Google Drive System
     */
    private function check_google_drive_availability()
    {
        try {
            // ตรวจสอบตาราง
            if (!$this->db->table_exists('tbl_google_drive_system_storage')) {
                return false;
            }

            // ตรวจสอบว่ามี System Storage หรือไม่
            $storage = $this->db->select('id, is_active, folder_structure_created')
                ->from('tbl_google_drive_system_storage')
                ->where('is_active', 1)
                ->get()
                ->row();

            return ($storage && $storage->folder_structure_created);

        } catch (Exception $e) {
            log_message('error', 'Check Google Drive availability error: ' . $e->getMessage());
            return false;
        }
    }






    private function get_redirect_url($level)
    {
        switch ($level) {
            case 1:
                return 'System_admin';
            case 2:
                return 'System_admin';
            case 3:
                return 'System_admin';
            default:
                echo "<script>";
                echo "alert('ไม่สามารถเข้าสู่ระบบได้ เนื่องจากคุณไม่ใช่ผู้ดูแลระบบ');";
                echo "</script>";
                $this->logout();
                return 'User';
        }
    }


    public function verify_session()
    {
        // ตั้งค่า header สำหรับ AJAX
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        $response = array();

        try {
            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                $response = array(
                    'valid' => false,
                    'message' => 'Invalid request method'
                );
                echo json_encode($response);
                return;
            }

            // ตรวจสอบ Staff User
            $staff_id = $this->session->userdata('m_id');

            if ($staff_id) {
                // ตรวจสอบว่าบัญชียังใช้งานได้หรือไม่
                $this->load->model('User_model'); // ถ้ายังไม่ได้โหลด

                $user = $this->db->select('m_id, m_status, m_fname, m_lname')
                    ->from('tbl_member')
                    ->where('m_id', $staff_id)
                    ->where('m_status', '1') // สถานะใช้งานได้
                    ->get()
                    ->row();

                if ($user) {
                    // Session ยังใช้งานได้
                    $response = array(
                        'valid' => true,
                        'user_type' => 'staff',
                        'user_id' => $staff_id,
                        'user_name' => $user->m_fname . ' ' . $user->m_lname,
                        'session_id' => session_id(),
                        'timestamp' => time()
                    );
                } else {
                    // บัญชีถูกปิดหรือไม่พบ
                    $this->session->sess_destroy();
                    $response = array(
                        'valid' => false,
                        'reason' => 'account_disabled',
                        'message' => 'บัญชีถูกปิดการใช้งาน'
                    );
                }
            } else {
                // ไม่มี session staff
                $response = array(
                    'valid' => false,
                    'reason' => 'no_session',
                    'message' => 'ไม่พบ session'
                );
            }

        } catch (Exception $e) {
            // Log error
            log_message('error', 'Session verification error: ' . $e->getMessage());

            $response = array(
                'valid' => false,
                'reason' => 'server_error',
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ session'
            );
        }

        echo json_encode($response);
    }


    /**
     * ปรับปรุง logout method เพื่อรองรับ Cross-Tab Sync
     * รวม code เดิมและใหม่เข้าด้วยกัน
     */
    public function logout()
    {
        // บันทึก log การ logout ก่อน destroy session
        $user_id = $this->session->userdata('m_id');
        $user_name = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
        $username = $this->session->userdata('m_username');

        if ($user_id) {
            // Log ข้อมูลการ logout
            log_message('info', 'Staff user logout: ' . $user_id . ' (' . $user_name . ')');

            // ลบ auth tokens จาก database
            $this->db->where('user_id', $user_id)
                ->where('domain', $_SERVER['HTTP_HOST'])
                ->delete('auth_tokens');

            // บันทึก activity log
            if ($this->user_log_model) {
                $this->user_log_model->log_activity(
                    $username,
                    'logout',
                    'User logged out'
                );
            }
        }

        // Unset session data ทั้งหมด
        $this->session->unset_userdata([
            'm_id',
            'm_level',
            'm_name',
            'grant_user_ref_id',
            'm_system',
            'm_username',
            'm_fname',
            'm_lname'
        ]);

        // ลบข้อมูลชั่วคราวด้วย (กรณี logout ระหว่าง 2FA)
        $this->clear_temp_session();

        // Destroy session ทั้งหมด
        $this->session->sess_destroy();

        // ตั้งค่า cookie เพื่อแจ้ง JavaScript สำหรับ Cross-Tab Sync
        // Cookie นี้จะให้ JavaScript อ่านและ sync ข้าม tabs
        setcookie('logout_sync', json_encode([
            'action' => 'logout',
            'timestamp' => time(),
            'user_id' => $user_id,
            'session_id' => session_id()
        ]), time() + 30, '/', '', false, false); // 30 วินาที, httpOnly = false เพื่อให้ JS อ่านได้

        // ตั้งค่า cookie สำหรับ logout message
        setcookie('logout_message', 'success', time() + 10, '/');

        // ตั้งค่า flash message (สำหรับกรณีที่ไม่ใช้ JavaScript)
        $this->session->set_flashdata('logout_success', true);

        // Redirect ไปหน้า login
        redirect('Home', 'refresh');
    }

    /**
     * Method เสริมสำหรับตรวจสอบ logout sync ระหว่าง tabs
     * เรียกผ่าน AJAX เพื่อตรวจสอบสถานะ Cross-Tab Sync
     */
    public function check_logout_sync()
    {
        // ตั้งค่า header สำหรับ AJAX
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        $response = array();

        try {
            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                $response = array(
                    'synced' => false,
                    'message' => 'Invalid request method'
                );
                echo json_encode($response);
                return;
            }

            // อ่าน logout sync cookie
            $logout_sync = $this->input->cookie('logout_sync');

            if ($logout_sync) {
                $sync_data = json_decode($logout_sync, true);

                // ตรวจสอบว่าเป็น logout action และยังไม่หมดอายุ
                if (
                    $sync_data &&
                    isset($sync_data['action']) &&
                    $sync_data['action'] === 'logout' &&
                    (time() - $sync_data['timestamp']) < 30
                ) {

                    $response = array(
                        'synced' => true,
                        'action' => 'logout',
                        'message' => 'Logout detected from another tab',
                        'redirect_url' => base_url('User')
                    );
                } else {
                    $response = array(
                        'synced' => false,
                        'message' => 'No recent logout detected'
                    );
                }
            } else {
                $response = array(
                    'synced' => false,
                    'message' => 'No logout sync data found'
                );
            }

        } catch (Exception $e) {
            log_message('error', 'Logout sync check error: ' . $e->getMessage());

            $response = array(
                'synced' => false,
                'message' => 'Error checking logout sync'
            );
        }

        echo json_encode($response);
    }

    /**
     * Method สำหรับล้าง logout sync cookie (เรียกจาก JavaScript หลัง redirect)
     */
    public function clear_logout_sync()
    {
        // ลบ logout sync cookie
        setcookie('logout_sync', '', time() - 3600, '/');
        setcookie('logout_message', '', time() - 3600, '/');

        echo json_encode(['status' => 'cleared']);
    }

    // ฟังก์ชันสำหรับส่งอีเมล (เหมือนเดิม)
    public function sendEmailAjax()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $this->load->library('email');
        $email = $this->input->post('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'อีเมลไม่ถูกต้อง']);
            return;
        }

        $user = $this->db->get_where('tbl_member', ['m_email' => $email])->row_array();
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบอีเมลในระบบ']);
            return;
        }

        // *** เพิ่มการตรวจสอบ Token ที่ยังไม่หมดอายุ ***
        if (!empty($user['reset_token']) && !empty($user['reset_expiration'])) {
            $current_time = date('Y-m-d H:i:s');

            // ตรวจสอบว่า token ยังไม่หมดอายุ
            if (strtotime($user['reset_expiration']) > strtotime($current_time)) {
                // คำนวณเวลาที่เหลือ
                $remaining_seconds = strtotime($user['reset_expiration']) - strtotime($current_time);
                $remaining_minutes = ceil($remaining_seconds / 60);

                $error_message = "อีเมลรีเซ็ตรหัสผ่านได้ถูกส่งไปแล้ว กรุณารอ {$remaining_minutes} นาที ก่อนขอส่งใหม่";

                echo json_encode([
                    'status' => 'error',
                    'message' => $error_message,
                    'remaining_time' => $remaining_seconds,
                    'remaining_minutes' => $remaining_minutes,
                    'already_sent' => true
                ]);
                return;
            }
        }

        $reset_token = bin2hex(random_bytes(32));

        $this->db->set('reset_token', $reset_token);
        $this->db->set('reset_expiration', date('Y-m-d H:i:s', time() + 600)); // 10 นาที
        $this->db->where('m_email', $email);
        $this->db->update('tbl_member');

        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $reset_link = base_url('user/resetPassword/' . $reset_token);
        $domain = get_config_value('domain');
        $this->email->from('no-reply@' . $domain . '.go.th', '');
        $this->email->to($email);
        $this->email->subject('รีเซ็ตรหัสผ่าน (สำหรับบุคลากรภายใน) สำหรับงานระบบบริการออนไลน์');
        $this->email->message('
        <h3>🔐 การรีเซ็ตรหัสผ่าน</h3>
        <p>เรียน ท่านผู้ใช้งานระบบ</p>
        <p>ระบบได้รับคำขอการรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ</p>
        <p><strong>วิธีการรีเซ็ตรหัสผ่าน:</strong></p>
        <p>1. คลิก <a href="' . $reset_link . '" style="color: #007bff;">ที่นี่</a> เพื่อไปยังหน้ารีเซ็ตรหัสผ่าน</p>
        <p>2. กรอกรหัสผ่านใหม่ที่ต้องการ</p>
        <p>3. ยืนยันการเปลี่ยนแปลง</p>
        <p><strong>⚠️ ข้อควรทราบ:</strong></p>
        <ul>
            <li>ลิงก์นี้จะหมดอายุใน 10 นาที</li>
            <li>ใช้ได้เพียงครั้งเดียวเท่านั้น</li>
            <li>หากหมดอายุ กรุณาทำการขอรีเซ็ตใหม่อีกครั้ง</li>
        </ul>
        <p>หากคุณไม่ได้ทำการขอรีเซ็ตรหัสผ่าน กรุณาเพิกเฉยต่ออีเมลนี้ และรหัสผ่านของคุณจะยังคงเหมือนเดิม</p>
        <hr>
        <small>อีเมลนี้ส่งโดยอัตโนมัติ กรุณาอย่าตอบกลับ</small>
    ');

        if ($this->email->send()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'ส่งอีเมลสำเร็จ ลิงก์จะหมดอายุใน 10 นาที',
                'expires_in' => 600 // 10 นาที
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถส่งอีเมลได้ กรุณาลองใหม่อีกครั้ง']);
        }
    }

    public function resetPassword($reset_token)
    {
        $user = $this->db->get_where('tbl_member', ['reset_token' => $reset_token])->row_array();

        if ($user && $user['reset_expiration'] > date('Y-m-d H:i:s')) {
            $data['email'] = $user['m_email'];
            $data['reset_token'] = $reset_token;
            $data['show_reset_modal'] = true;

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'success',
                    'email' => $user['m_email'],
                    'reset_token' => $reset_token
                ]);
                return;
            }

            // โหลด CSS
            $this->load->view('asset/css');

            // โหลดหน้า Login Form พร้อมข้อมูล
            $this->load->view('login_form_admin', $data);

            // *** เพิ่ม JavaScript Variables สำหรับ Reset Modal ***
            echo '<script type="text/javascript">';
            echo 'window.show_reset_modal = true;';
            echo 'window.reset_email = "' . htmlspecialchars($user['m_email'], ENT_QUOTES) . '";';
            echo 'window.reset_token = "' . htmlspecialchars($reset_token, ENT_QUOTES) . '";';
            echo 'console.log("🔑 Reset password variables loaded:");';
            echo 'console.log("  - Email:", window.reset_email);';
            echo 'console.log("  - Token length:", window.reset_token.length);';
            echo '</script>';

            // โหลด JavaScript
            $this->load->view('asset/js');

        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'โทเค็นไม่ถูกต้องหรือหมดอายุ'
                ]);
                return;
            }

            echo '<script>';
            echo 'alert("โทเค็นไม่ถูกต้องหรือหมดอายุ");';
            echo 'window.location.href = "' . site_url('user') . '";';
            echo '</script>';
        }
    }

    public function changePasswordAjax()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');
        $email = $this->input->post('email');
        $reset_token = $this->input->post('reset_token');

        if (empty($new_password) || empty($confirm_password) || empty($email) || empty($reset_token)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
            return;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านและรหัสผ่านยืนยันไม่ตรงกัน']);
            return;
        }

        $user = $this->db->get_where('tbl_member', [
            'reset_token' => $reset_token,
            'm_email' => $email
        ])->row_array();

        if (!$user || $user['reset_expiration'] < date('Y-m-d H:i:s')) {
            echo json_encode(['status' => 'error', 'message' => 'โทเค็นไม่ถูกต้องหรือหมดอายุ']);
            return;
        }

        if (strlen($new_password) < 8) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร']);
            return;
        }

        $this->db->set('m_password', sha1($new_password));
        $this->db->set('reset_token', NULL);
        $this->db->set('reset_expiration', NULL);
        $this->db->where('m_email', $email);
        $update_result = $this->db->update('tbl_member');

        if ($update_result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'รหัสผ่านถูกเปลี่ยนเรียบร้อยแล้ว',
                'redirect_url' => site_url('user')
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน']);
        }
    }

    // ฟังก์ชันสำหรับประชาชน (เหมือนเดิม)
    public function sendEmailPublicAjax()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $this->load->library('email');
        $email = $this->input->post('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'อีเมลไม่ถูกต้อง']);
            return;
        }

        $user = $this->db->get_where('tbl_member_public', ['mp_email' => $email])->row_array();
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบอีเมลในระบบ']);
            return;
        }

        // *** เพิ่มการตรวจสอบ Token ที่ยังไม่หมดอายุ ***
        if (!empty($user['reset_token']) && !empty($user['reset_expiration'])) {
            $current_time = date('Y-m-d H:i:s');

            // ตรวจสอบว่า token ยังไม่หมดอายุ
            if (strtotime($user['reset_expiration']) > strtotime($current_time)) {
                // คำนวณเวลาที่เหลือ
                $remaining_seconds = strtotime($user['reset_expiration']) - strtotime($current_time);
                $remaining_minutes = ceil($remaining_seconds / 60);

                $error_message = "อีเมลรีเซ็ตรหัสผ่านได้ถูกส่งไปแล้ว กรุณารอ {$remaining_minutes} นาที ก่อนขอส่งใหม่";

                echo json_encode([
                    'status' => 'error',
                    'message' => $error_message,
                    'remaining_time' => $remaining_seconds,
                    'remaining_minutes' => $remaining_minutes,
                    'already_sent' => true
                ]);
                return;
            }
        }

        $reset_token = bin2hex(random_bytes(32));

        $this->db->set('reset_token', $reset_token);
        $this->db->set('reset_expiration', date('Y-m-d H:i:s', time() + 600)); // 10 นาที
        $this->db->where('mp_email', $email);
        $this->db->update('tbl_member_public');

        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $reset_link = base_url('user/resetPasswordPublic/' . $reset_token);
        $domain = get_config_value('domain');
        $this->email->from('no-reply@' . $domain . '.go.th', '');
        $this->email->to($email);
        $this->email->subject('รีเซ็ตรหัสผ่าน (สำหรับประชาชน) สำหรับงานระบบบริการออนไลน์');
        $this->email->message('
        <h3>🔐 การรีเซ็ตรหัสผ่าน</h3>
        <p>เรียน ท่านผู้ใช้งานระบบ</p>
        <p>ระบบได้รับคำขอการรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ</p>
        <p><strong>วิธีการรีเซ็ตรหัสผ่าน:</strong></p>
        <p>1. คลิก <a href="' . $reset_link . '" style="color: #007bff;">ที่นี่</a> เพื่อไปยังหน้ารีเซ็ตรหัสผ่าน</p>
        <p>2. กรอกรหัสผ่านใหม่ที่ต้องการ</p>
        <p>3. ยืนยันการเปลี่ยนแปลง</p>
        <p><strong>⚠️ ข้อควรทราบ:</strong></p>
        <ul>
            <li>ลิงก์นี้จะหมดอายุใน 10 นาที</li>
            <li>ใช้ได้เพียงครั้งเดียวเท่านั้น</li>
            <li>หากหมดอายุ กรุณาทำการขอรีเซ็ตใหม่อีกครั้ง</li>
        </ul>
        <p>หากคุณไม่ได้ทำการขอรีเซ็ตรหัสผ่าน กรุณาเพิกเฉยต่ออีเมลนี้ และรหัสผ่านของคุณจะยังคงเหมือนเดิม</p>
        <hr>
        <small>อีเมลนี้ส่งโดยอัตโนมัติ กรุณาอย่าตอบกลับ</small>
    ');

        if ($this->email->send()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'ส่งอีเมลสำเร็จ ลิงก์จะหมดอายุใน 10 นาที',
                'expires_in' => 600 // 10 นาที
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถส่งอีเมลได้ กรุณาลองใหม่อีกครั้ง']);
        }
    }

    public function resetPasswordPublic($reset_token)
    {
        $user = $this->db->get_where('tbl_member_public', ['reset_token' => $reset_token])->row_array();

        if ($user && $user['reset_expiration'] > date('Y-m-d H:i:s')) {
            $data['public_email'] = $user['mp_email'];
            $data['public_reset_token'] = $reset_token;
            $data['show_reset_public_modal'] = true;

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'success',
                    'public_email' => $user['mp_email'],
                    'public_reset_token' => $reset_token
                ]);
                return;
            }

            // โหลด CSS
            $this->load->view('asset/css');

            // โหลดหน้า Login Form พร้อมข้อมูล
            $this->load->view('login_form_admin', $data);

            // *** เพิ่ม JavaScript Variables สำหรับ Public Reset Modal ***
            echo '<script type="text/javascript">';
            echo 'window.show_reset_public_modal = true;';
            echo 'window.reset_public_email = "' . htmlspecialchars($user['mp_email'], ENT_QUOTES) . '";';
            echo 'window.reset_public_token = "' . htmlspecialchars($reset_token, ENT_QUOTES) . '";';
            echo 'console.log("🔑 Public reset password variables loaded:");';
            echo 'console.log("  - Email:", window.reset_public_email);';
            echo 'console.log("  - Token length:", window.reset_public_token.length);';
            echo '</script>';

            // โหลด JavaScript
            $this->load->view('asset/js');

        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'โทเค็นไม่ถูกต้องหรือหมดอายุ'
                ]);
                return;
            }

            echo '<script>';
            echo 'alert("โทเค็นไม่ถูกต้องหรือหมดอายุ");';
            echo 'window.location.href = "' . site_url('user') . '";';
            echo '</script>';
        }
    }

    public function changePasswordPublicAjax()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }

        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');
        $email = $this->input->post('email');
        $reset_token = $this->input->post('reset_token');

        if (empty($new_password) || empty($confirm_password) || empty($email) || empty($reset_token)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
            return;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านและรหัสผ่านยืนยันไม่ตรงกัน']);
            return;
        }

        $user = $this->db->get_where('tbl_member_public', [
            'reset_token' => $reset_token,
            'mp_email' => $email
        ])->row_array();

        if (!$user || $user['reset_expiration'] < date('Y-m-d H:i:s')) {
            echo json_encode(['status' => 'error', 'message' => 'โทเค็นไม่ถูกต้องหรือหมดอายุ']);
            return;
        }

        if (strlen($new_password) < 8) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร']);
            return;
        }

        $this->db->set('mp_password', sha1($new_password));
        $this->db->set('reset_token', NULL);
        $this->db->set('reset_expiration', NULL);
        $this->db->where('mp_email', $email);
        $update_result = $this->db->update('tbl_member_public');

        if ($update_result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'รหัสผ่านถูกเปลี่ยนเรียบร้อยแล้ว',
                'redirect_url' => site_url('user')
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน']);
        }
    }

    public function privacy()
    {
        $this->load->view('asset/css');
        $this->load->view('privacy');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    private function cleanup_tokens()
    {
        $this->db->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete('auth_tokens');

        $this->db->where('tenant_id IS NULL')
            ->or_where('tenant_code IS NULL')
            ->or_where('tenant_code', '')
            ->delete('auth_tokens');

        $current_domain = $_SERVER['HTTP_HOST'];
        $this->db->where('domain !=', $current_domain)
            ->delete('auth_tokens');

        $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-15 minutes')))
            ->delete('auth_tokens');

        // *** เพิ่ม: ทำความสะอาด trusted devices ที่หมดอายุ ***
        $this->db->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete('trusted_devices');
    }




    public function get_notification_count()
    {
        header('Content-Type: application/json');

        try {
            $unread_count = 0;

            if ($this->db->table_exists('tbl_notifications')) {
                $this->db->select('COUNT(*) as count');
                $this->db->from('tbl_notifications');
                $this->db->where('target_role', 'staff');
                $this->db->where('is_read', 0);
                $this->db->where('is_archived', 0);

                $query = $this->db->get();
                if ($query && $query->num_rows() > 0) {
                    $result = $query->row();
                    $unread_count = (int) $result->count;
                }
            }

            echo json_encode([
                'status' => 'success',
                'unread_count' => $unread_count
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'unread_count' => 0
            ]);
        }
    }

}