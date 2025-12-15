<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email Verification Controller
 * จัดการการยืนยันอีเมลผ่าน Link แทน OTP
 */
class Email_verification_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('email_otp_model');
        $this->load->model('member_public_model');
        $this->load->library('email');
        $this->load->helper('url');
    }

    /**
     * ส่ง Verification Link ไปยังอีเมล
     */
    public function send_verification_link()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            $email = $this->input->post('email');
            $form_data = $this->input->post();
            unset($form_data['email']);

            log_message('info', "📋 Form data fields: " . implode(', ', array_keys($form_data)));

            // Validation
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'อีเมลไม่ถูกต้อง'
                ]);
                return;
            }

            // ตรวจสอบว่าอีเมลนี้ถูกใช้แล้วหรือไม่
            $this->db->where('mp_email', $email);
            $existing = $this->db->get('tbl_member_public')->row();

            if ($existing) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'อีเมลนี้ถูกใช้งานแล้ว'
                ]);
                return;
            }

            // ป้องกัน spam
            $recent_count = $this->email_otp_model->count_recent_otp($email, 30);
            if ($recent_count >= 5) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'คุณขอลิงก์ยืนยันบ่อยเกินไป'
                ]);
                return;
            }

            // 🆕 จัดการ Upload รูปภาพ (ถ้ามี)
            $uploaded_filename = null;
            if (!empty($_FILES['mp_img']['name'])) {
                $uploaded_filename = $this->handle_temp_upload();

                if ($uploaded_filename) {
                    $form_data['uploaded_filename'] = $uploaded_filename;
                    log_message('info', "✅ File uploaded: $uploaded_filename");
                }
            }

            // สร้าง verification token
            $token_result = $this->email_otp_model->create_otp_with_registration_data(
                $email,
                $email,
                'register',
                $form_data
            );

            if (!$token_result['success']) {
                // ลบไฟล์ temp ถ้า upload ไว้แล้ว
                if ($uploaded_filename) {
                    $this->delete_temp_file($uploaded_filename);
                }

                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่สามารถสร้างลิงก์ยืนยันได้'
                ]);
                return;
            }

            // สร้าง verification link
            $verification_link = site_url('Email_verification_controller/verify_and_register/' . $token_result['otp_code'] . '/' . urlencode($email));

            // ส่งอีเมล
            $send_result = $this->send_verification_email($email, $verification_link, $token_result['otp_code']);

            if ($send_result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'ส่งลิงก์ยืนยันไปยังอีเมลของคุณแล้ว',
                    'expires_in' => 10
                ]);
            } else {
                // ลบไฟล์ temp ถ้าส่งอีเมลไม่สำเร็จ
                if ($uploaded_filename) {
                    $this->delete_temp_file($uploaded_filename);
                }

                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่สามารถส่งอีเมลได้'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in send_verification_link: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ]);
        }
    }

    /**
     * ยืนยันบัญชีผ่าน Link
     */
    public function verify_account($token = null, $email = null)
    {
        try {
            if (empty($token) || empty($email)) {
                $data['status'] = 'error';
                $data['message'] = 'ลิงก์ไม่ถูกต้อง';
                $this->load->view('public_user/account_verify', $data);
                return;
            }

            $email = urldecode($email);

            // ตรวจสอบ token (ใช้ฟังก์ชัน verify_otp เดิม)
            $verify_result = $this->email_otp_model->verify_otp($email, $token, 'register');

            if ($verify_result['success']) {
                // บันทึกใน session ว่ายืนยันแล้ว
                $this->session->set_userdata('registration_email_verified', $email);
                $this->session->set_userdata('registration_verified_at', time());

                $data['status'] = 'success';
                $data['message'] = 'ยืนยันอีเมลสำเร็จ! คุณสามารถดำเนินการลงทะเบียนต่อได้';
                $data['verified_email'] = $email;
                $data['redirect_url'] = site_url('Auth_public_mem/register_form');
            } else {
                $data['status'] = 'error';
                $data['message'] = $verify_result['message'];
            }

            $this->load->view('public_user/account_verify', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in verify_account: ' . $e->getMessage());
            $data['status'] = 'error';
            $data['message'] = 'เกิดข้อผิดพลาดในการยืนยันบัญชี';
            $this->load->view('public_user/account_verify', $data);
        }
    }

    /**
     * 🆕 จัดการ Upload รูปโปรไฟล์ชั่วคราว
     * @return string|null ชื่อไฟล์ถ้าสำเร็จ, null ถ้าล้มเหลว
     */
    private function handle_temp_upload()
    {
        try {
            if (empty($_FILES['mp_img']['name'])) {
                return null;
            }

            // Upload config
            $config['upload_path'] = './docs/img/temp/';
            $config['allowed_types'] = 'jpg|jpeg|png';  // ❌ ไม่เอา gif
            $config['max_size'] = 2048; // 2MB
            $config['encrypt_name'] = TRUE;

            // สร้าง directory ถ้ายังไม่มี
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('mp_img')) {
                $upload_data = $this->upload->data();
                log_message('info', "Temp file uploaded: {$upload_data['file_name']}");
                return $upload_data['file_name'];
            } else {
                $error = strip_tags($this->upload->display_errors());
                log_message('error', "Upload failed: $error");
                return null;
            }

        } catch (Exception $e) {
            log_message('error', 'Error in handle_temp_upload: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🆕 ลบไฟล์ temp ที่ไม่ได้ใช้
     * @param string $filename
     * @return bool
     */
    private function delete_temp_file($filename)
    {
        try {
            $temp_path = './docs/img/temp/' . $filename;

            if (file_exists($temp_path)) {
                unlink($temp_path);
                log_message('info', "Temp file deleted: $filename");
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error deleting temp file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ส่งอีเมล Verification Link
     * 🔧 ปรับให้เหมือนฟังก์ชันแรก (ส่งหา Gmail ได้)
     */
    private function send_verification_email($to_email, $verification_link, $token)
    {
        try {
            log_message('info', "📧 Starting email verification process for: $to_email");

            // 1. เตรียมข้อมูล
            $tenant_name = $this->session->userdata('tenant_name') ?: 'ระบบยืนยันตัวตน';
            log_message('info', "🏢 Tenant name: $tenant_name");

            $email_body = $this->create_verification_email_body([
                'tenant_name' => $tenant_name,
                'verification_link' => $verification_link,
                'token' => $token,
                'to_email' => $to_email,
                'expires_minutes' => 10
            ]);
            log_message('info', "📝 Email body created successfully");

            // 2. ตั้งค่าอีเมล (เหมือนฟังก์ชันแรก)
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            log_message('info', "⚙️ Email config initialized with mailtype: html");

            $domain = get_config_value('domain');
            $from_email = 'no-reply@' . $domain . '.go.th';
            $this->email->from($from_email, '');
            log_message('info', "📤 From email set to: $from_email");

            // 3. ตั้งค่าผู้รับและเนื้อหา
            $this->email->to($to_email);
            log_message('info', "📥 To email set to: $to_email");

            $email_subject = 'ยืนยันการลงทะเบียน - ' . $tenant_name;
            $this->email->subject($email_subject);
            log_message('info', "📋 Subject set to: $email_subject");

            $this->email->message($email_body);
            log_message('info', "💬 Email message set successfully");

            // 4. ส่งอีเมล
            log_message('info', "🚀 Attempting to send email...");

            if ($this->email->send()) {
                log_message('info', "✅ Verification email sent successfully to: $to_email");
                log_message('info', "🔗 Verification link: $verification_link");
                log_message('info', "🔑 Token: $token");
                return true;
            } else {
                $debug_info = $this->email->print_debugger();
                log_message('error', "❌ Failed to send verification email to: $to_email");
                log_message('error', "❌ Debug info: $debug_info");
                return false;
            }

        } catch (Exception $e) {
            log_message('error', "❌ Exception in send_verification_email for: $to_email");
            log_message('error', "❌ Error message: " . $e->getMessage());
            log_message('error', "❌ Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * สร้าง Email Body แบบง่าย ชัดเจน
     */
    private function create_verification_email_body($data)
    {
        $html = '<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการลงทะเบียน</title>
    <style>
        body {
            font-family: "Sarabun", "Tahoma", sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #45a049;
        }
        .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            color: #ff6b6b;
            font-size: 14px;
            margin-top: 20px;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">🔐 ยืนยันการลงทะเบียน</h1>
            <p style="margin: 10px 0 0 0;">' . $data['tenant_name'] . '</p>
        </div>
        
        <div class="content">
            <h2>สวัสดีค่ะ/ครับ</h2>
            
            <p>คุณได้ทำการลงทะเบียนบัญชีใหม่ด้วยอีเมล: <strong>' . $data['to_email'] . '</strong></p>
            
            <p>กรุณายืนยันการลงทะเบียนโดยคลิกปุ่มด้านล่าง:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . $data['verification_link'] . '" class="button">
                    ✓ ยืนยันการลงทะเบียน
                </a>
            </div>
            
            <div class="info-box">
                <h3 style="margin-top: 0;">ข้อมูลการยืนยัน:</h3>
                <p><strong>รหัสยืนยัน:</strong> ' . $data['token'] . '</p>
                <p><strong>อีเมล:</strong> ' . $data['to_email'] . '</p>
                <p><strong>หมดอายุภายใน:</strong> ' . $data['expires_minutes'] . ' นาที</p>
            </div>
            
            <p><strong>หากปุ่มด้านบนไม่ทำงาน</strong> กรุณาคัดลอกลิงก์ด้านล่างและวางในเบราว์เซอร์:</p>
            <p style="word-break: break-all; background-color: #f9f9f9; padding: 10px; border-radius: 4px;">
                ' . $data['verification_link'] . '
            </p>
            
            <div class="warning">
                <p><strong>⚠️ คำเตือน:</strong></p>
                <ul>
                    <li>ลิงก์นี้จะหมดอายุใน ' . $data['expires_minutes'] . ' นาที</li>
                    <li>อย่าแชร์ลิงก์นี้ให้ผู้อื่น</li>
                    <li>หากคุณไม่ได้ลงทะเบียน กรุณาเพิกเฉยอีเมลนี้</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>อีเมลนี้ถูกส่งอัตโนมัติ กรุณาอย่าตอบกลับ</p>
            <p>© ' . date('Y') . ' ' . $data['tenant_name'] . ' สงวนลิขสิทธิ์</p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * ตรวจสอบสถานะ verification link
     */
    public function check_verification_status()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            $email = $this->input->post('email');

            if (empty($email)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่พบอีเมล'
                ]);
                return;
            }

            // ตรวจสอบว่ามี verification link ที่ยังไม่หมดอายุหรือไม่
            $latest = $this->email_otp_model->get_latest_otp($email, 'register');

            if ($latest) {
                $remaining_seconds = strtotime($latest->expires_at) - time();

                echo json_encode([
                    'status' => 'success',
                    'has_link' => true,
                    'is_used' => ($latest->is_used == 1),
                    'remaining_seconds' => max(0, $remaining_seconds),
                    'expired' => ($remaining_seconds <= 0)
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'has_link' => false
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in check_verification_status: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด'
            ]);
        }
    }

    /**
     * 🆕 ยืนยันอีเมลและสมัครสมาชิกอัตโนมัติ
     */
    public function verify_and_register($token = null, $email = null)
    {
        try {
            log_message('info', "🔍 verify_and_register() called with token: $token, email: $email");

            if (empty($token) || empty($email)) {
                log_message('error', '❌ Missing token or email parameter');
                $data['status'] = 'error';
                $data['message'] = 'ลิงก์ไม่ถูกต้อง';

                $this->load->view('frontend_templat/header');
                $this->load->view('frontend_asset/css');
                $this->load->view('frontend_templat/navbar_other');
                $this->load->view('public_user/account_verify', $data);
                $this->load->view('frontend_asset/js');
                $this->load->view('frontend_templat/footer_other');
                return;
            }

            $email = urldecode($email);
            log_message('info', "📧 Processing auto-registration for: $email");

            // ตรวจสอบ token
            $verify_result = $this->email_otp_model->verify_otp($email, $token, 'register');

            if (!$verify_result['success']) {
                log_message('error', "❌ Token verification failed: {$verify_result['message']}");
                $data['status'] = 'error';
                $data['message'] = $verify_result['message'];

                $this->load->view('frontend_templat/header');
                $this->load->view('frontend_asset/css');
                $this->load->view('frontend_templat/navbar_other');
                $this->load->view('public_user/account_verify', $data);
                $this->load->view('frontend_asset/js');
                $this->load->view('frontend_templat/footer_other');
                return;
            }

            // ✅ Token ถูกต้อง - ดึงข้อมูลฟอร์มที่เก็บไว้
            log_message('info', "✅ Token verified, retrieving registration data...");
            $form_data = $this->email_otp_model->get_registration_data($token, $email);

            if (empty($form_data)) {
                log_message('error', "❌ No registration data found for token: $token");
                $data['status'] = 'error';
                $data['message'] = 'ไม่พบข้อมูลการสมัครสมาชิก กรุณาลองใหม่';

                $this->load->view('frontend_templat/header');
                $this->load->view('frontend_asset/css');
                $this->load->view('frontend_templat/navbar_other');
                $this->load->view('public_user/account_verify', $data);
                $this->load->view('frontend_asset/js');
                $this->load->view('frontend_templat/footer_other');
                return;
            }

            log_message('info', "📋 Registration data retrieved successfully");

            // 🆕 เตรียมข้อมูลสำหรับบันทึก
            $registration_data = $this->prepare_registration_data_from_form($form_data, $email);

            // ========================================
            // 🆕 เพิ่มส่วนนี้: จัดการรูปโปรไฟล์
            // ========================================
            if (!empty($form_data['uploaded_filename'])) {
                log_message('info', "🖼️ Processing uploaded profile image: {$form_data['uploaded_filename']}");

                $moved_filename = $this->move_temp_profile_image($form_data['uploaded_filename']);

                if ($moved_filename) {
                    // ✅ ย้ายไฟล์สำเร็จ - อัปเดตข้อมูล
                    $registration_data['mp_img'] = $moved_filename;
                    log_message('info', "✅ Profile image moved successfully: $moved_filename");
                } else {
                    // ⚠️ ย้ายไฟล์ไม่สำเร็จ - ใช้ avatar เดิม (ถ้ามี)
                    log_message('warning', "⚠️ Failed to move profile image, using avatar instead");
                }
            } else {
                log_message('info', "ℹ️ No uploaded file, using avatar or default");
            }
            // ========================================

            // 🆕 บันทึกข้อมูลสมาชิก
            log_message('info', "💾 Creating member account for: $email");
            $member_id = $this->member_public_model->create_member($registration_data);

            if ($member_id) {
                // ✅ สมัครสำเร็จ
                log_message('info', "✅ Member registered successfully: $email (ID: $member_id)");

                $data['status'] = 'success';
                $data['message'] = 'ยืนยันอีเมลและสมัครสมาชิกสำเร็จ!';
                $data['verified_email'] = $email;
                $data['member_id'] = $member_id;
                $data['redirect_url'] = site_url('User');
                $data['auto_registered'] = true;

            } else {
                // ❌ สมัครไม่สำเร็จ
                log_message('error', "❌ Failed to create member account for: $email");

                // ลบไฟล์รูปที่ย้ายมาแล้ว (ถ้ามี)
                if (!empty($registration_data['mp_img']) && !filter_var($registration_data['mp_img'], FILTER_VALIDATE_URL)) {
                    $this->cleanup_uploaded_file($registration_data['mp_img']);
                }

                $data['status'] = 'error';
                $data['message'] = 'เกิดข้อผิดพลาดในการสมัครสมาชิก กรุณาติดต่อผู้ดูแลระบบ';
            }

            // ✅ โหลด view พร้อม template
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('public_user/account_verify', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer_other');

        } catch (Exception $e) {
            log_message('error', '❌ Exception in verify_and_register: ' . $e->getMessage());
            log_message('error', '❌ Exception trace: ' . $e->getTraceAsString());

            $data['status'] = 'error';
            $data['message'] = 'เกิดข้อผิดพลาดในระบบ';

            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('public_user/account_verify', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer_other');
        }
    }

    /**
     * 🆕 เตรียมข้อมูลสมัครสมาชิกจากฟอร์ม
     */
    private function prepare_registration_data_from_form($form_data, $email)
    {
        $this->load->helper('string');

        log_message('info', '📋 prepare_registration_data_from_form called for: ' . $email);
        log_message('info', '📦 Form data keys: ' . implode(', ', array_keys($form_data)));

        // ✅ ตรวจสอบวันเกิด
        $birthdate = null;
        if (!empty($form_data['mp_birthdate'])) {
            $birthdate = $form_data['mp_birthdate'];
            log_message('info', '📅 Birthdate received: ' . $birthdate);

            // 🔄 แปลงจากพุทธศักราช (DD/MM/YYYY) เป็นคริสต์ศักราช (YYYY-MM-DD)
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $birthdate, $matches)) {
                log_message('info', '🔄 Converting Buddhist date to Christian date');

                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $buddhist_year = intval($matches[3]);

                // ตรวจสอบว่าเป็นปี พ.ศ. (มากกว่า 2400)
                if ($buddhist_year >= 2400) {
                    $christian_year = $buddhist_year - 543;
                    $birthdate = sprintf('%04d-%02d-%02d', $christian_year, $month, $day);
                    log_message('info', '✅ Date converted: ' . $matches[0] . ' → ' . $birthdate);
                } else {
                    log_message('error', '❌ Invalid Buddhist year: ' . $buddhist_year);
                    $birthdate = null;
                }
            }
            // Validate format YYYY-MM-DD
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
                log_message('info', '✅ Birthdate format valid (YYYY-MM-DD)');
            } else {
                log_message('error', '❌ Birthdate format invalid: ' . $birthdate);
                $birthdate = null;
            }

            // Validate ว่าวันที่ถูกต้องหรือไม่
            if ($birthdate !== null) {
                $date = DateTime::createFromFormat('Y-m-d', $birthdate);
                if (!$date || $date->format('Y-m-d') !== $birthdate) {
                    log_message('error', '❌ Invalid date value: ' . $birthdate);
                    $birthdate = null;
                } else {
                    log_message('info', '✅ Date validation passed');
                }
            }
        } else {
            log_message('info', '⚠️ No birthdate in form data');
        }

        $data = [
            'mp_id' => $this->generate_member_id(),
            'mp_email' => $email,
            'mp_password' => sha1($form_data['mp_password'] ?? ''),
            'mp_prefix' => $form_data['mp_prefix'] ?? '',
            'mp_fname' => $form_data['mp_fname'] ?? '',
            'mp_lname' => $form_data['mp_lname'] ?? '',
            'mp_phone' => $form_data['mp_phone'] ?? '',
            'mp_number' => !empty($form_data['mp_number']) ? $form_data['mp_number'] : null,
            'mp_birthdate' => $birthdate, // ✅ เพิ่มบรรทัดนี้
            'mp_address' => $form_data['mp_address'] ?? '',
            'mp_district' => $form_data['district'] ?? '',
            'mp_amphoe' => $form_data['amphoe'] ?? '',
            'mp_province' => $form_data['province'] ?? '',
            'mp_zipcode' => $form_data['zipcode'] ?? '',
            'mp_img' => null,
            'mp_by' => 'auto_register',
            'mp_status' => 1
        ];

        log_message('info', '📊 Prepared data - mp_birthdate: ' . ($data['mp_birthdate'] ?? 'NULL'));

        // Avatar handling
        if (!empty($form_data['avatar_url'])) {
            $data['mp_img'] = $form_data['avatar_url'];
            log_message('info', '🖼️ Avatar URL set: ' . $data['mp_img']);
        } elseif (!empty($form_data['avatar_choice'])) {
            $avatar_number = str_replace('avatar', '', $form_data['avatar_choice']);
            $data['mp_img'] = 'https://i.pravatar.cc/150?img=' . $avatar_number;
            log_message('info', '🖼️ Avatar choice set: ' . $data['mp_img']);
        }

        log_message('info', "✅ Registration data prepared for: $email");
        return $data;
    }


    /**
     * 🆕 สร้าง Member ID แบบ unique
     */
    private function generate_member_id()
    {
        $year = substr(date('Y'), -2);
        $timestamp = time();
        $random = random_string('numeric', 3);
        return $year . $timestamp . $random;
    }

    private function move_temp_profile_image($filename)
    {
        try {
            if (empty($filename)) {
                return null;
            }

            $temp_path = './docs/img/temp/' . $filename;
            $final_path = './docs/img/' . $filename;

            // ตรวจสอบว่าไฟล์ temp มีอยู่จริง
            if (!file_exists($temp_path)) {
                log_message('error', "Temp file not found: $temp_path");
                return null;
            }

            // ตรวจสอบว่า final directory มีอยู่
            if (!is_dir('./docs/img/')) {
                mkdir('./docs/img/', 0755, true);
            }

            // ย้ายไฟล์
            if (rename($temp_path, $final_path)) {
                log_message('info', "File moved: $filename");
                return $filename; // ✅ return แค่ชื่อไฟล์
            }

            return null;
        } catch (Exception $e) {
            log_message('error', 'Error: ' . $e->getMessage());
            return null;
        }
    }

    private function cleanup_uploaded_file($filename)
    {
        try {
            if (empty($filename)) {
                return false;
            }

            $file_path = './docs/img/' . $filename;

            if (file_exists($file_path)) {
                unlink($file_path);
                log_message('info', "Cleanup: $filename");
                return true;
            }

            return false;
        } catch (Exception $e) {
            log_message('error', 'Cleanup error: ' . $e->getMessage());
            return false;
        }
    }

}