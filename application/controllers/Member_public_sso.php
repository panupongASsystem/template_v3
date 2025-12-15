<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * Controller สำหรับจัดการ SSO ของสมาชิกประชาชน
 */
class Member_public_sso extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // เพิ่มการจัดการ error เพื่อป้องกัน 500 error
        set_error_handler(function ($severity, $message, $file, $line) {
            log_message('error', "Error: ($severity) $message ที่ $file:$line");
            return true;
        });

        // โหลด libraries และ models ที่จำเป็น
        $this->load->library('session');
        $this->load->model('member_public_model');
        $this->load->model('tenant_access_model');
        
        // โหลด User_log_model ถ้ามี
        if (file_exists(APPPATH . 'models/User_log_model.php')) {
            $this->load->model('user_log_model');
        }

        // *** เพิ่ม: ตรวจสอบการล็อกอินแบบครบถ้วน ***
        $login_check = $this->comprehensive_login_check();
        if (!$login_check['success']) {
            if ($this->input->is_ajax_request()) {
                $response = [
                    'status' => 'error',
                    'message' => $login_check['message'],
                    'redirect' => $login_check['redirect']
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            } else {
                $this->session->set_flashdata('error', $login_check['message']);
                redirect($login_check['redirect']);
                return;
            }
        }

        // เพิ่ม permissions สำหรับ public user ถ้ายังไม่มี
        if (!$this->session->userdata('permissions')) {
            $this->session->set_userdata('permissions', 'ex_user');
        }
    }

    /**
     * ตรวจสอบการเข้าสู่ระบบแบบครบถ้วน รวมถึง 2FA
     */
    private function comprehensive_login_check()
{
    try {
        $mp_id = $this->session->userdata('mp_id');
        
        error_log("=== SSO LOGIN CHECK START ===");
        error_log("User ID: " . ($mp_id ?: 'NULL'));
        error_log("2FA Verified: " . ($this->session->userdata('2fa_verified') ? 'YES' : 'NO'));
        error_log("Is Public: " . ($this->session->userdata('is_public') ? 'YES' : 'NO'));
        error_log("Tenant ID: " . ($this->session->userdata('tenant_id') ?: 'NULL'));
        
        // ตรวจสอบการล็อกอินพื้นฐาน
        if (!$mp_id) {
            error_log("SSO: No mp_id in session");
            return [
                'success' => false,
                'message' => 'กรุณาเข้าสู่ระบบก่อนใช้งาน',
                'redirect' => 'User'
            ];
        }

        // *** สำคัญ: ดึงข้อมูลผู้ใช้จากฐานข้อมูลเพื่อตรวจสอบ ***
        $user_data = $this->member_public_model->get_member_by_id($mp_id);
        if (!$user_data) {
            error_log("SSO: User data not found for mp_id: $mp_id");
            
            // ล้าง session ที่เสียหาย
            $this->session->sess_destroy();
            return [
                'success' => false,
                'message' => 'ไม่พบข้อมูลผู้ใช้ กรุณาเข้าสู่ระบบใหม่',
                'redirect' => 'User'
            ];
        }

        // ตรวจสอบสถานะผู้ใช้
        if (isset($user_data->mp_status) && $user_data->mp_status != 1) {
            error_log("SSO: User account disabled for mp_id: $mp_id");
            return [
                'success' => false,
                'message' => 'บัญชีของคุณถูกระงับการใช้งาน',
                'redirect' => 'Auth_public_mem/logout'
            ];
        }

        // *** ตรวจสอบ 2FA (สำคัญมาก!) ***
        $user_2fa_info = null;
        try {
            $user_2fa_info = $this->member_public_model->get_2fa_info($mp_id);
        } catch (Exception $e) {
            error_log('Error checking 2FA info in SSO: ' . $e->getMessage());
        }

        // ถ้าผู้ใช้เปิด 2FA ต้องตรวจสอบว่าผ่านการยืนยันแล้ว
        if ($user_2fa_info && !empty($user_2fa_info->google2fa_secret) && $user_2fa_info->google2fa_enabled == 1) {
            $is_2fa_verified = $this->session->userdata('2fa_verified');
            $is_trusted_device = $this->session->userdata('trusted_device');
            
            error_log("SSO 2FA Check - User: $mp_id, 2FA Required: YES, Verified: " . ($is_2fa_verified ? 'YES' : 'NO') . ", Trusted: " . ($is_trusted_device ? 'YES' : 'NO'));

            if (!$is_2fa_verified && !$is_trusted_device) {
                error_log("SSO: 2FA required but not verified for user: $mp_id");
                return [
                    'success' => false,
                    'message' => 'กรุณายืนยันตัวตนผ่าน 2FA ก่อนเข้าใช้บริการ',
                    'redirect' => 'Auth_public_mem'
                ];
            }
        } else {
            error_log("SSO: User does not have 2FA enabled");
        }

        // *** อัพเดท session ให้ครบถ้วน (ถ้าไม่มีข้อมูล) ***
        $session_updated = $this->update_session_if_incomplete($user_data);
        if ($session_updated) {
            error_log("SSO: Session was updated with missing data");
        }

        error_log("SSO: Comprehensive login check passed for user: " . $user_data->mp_email);
        error_log("=== SSO LOGIN CHECK END ===");

        return [
            'success' => true,
            'user_data' => $user_data
        ];

    } catch (Exception $e) {
        error_log('Error in comprehensive_login_check: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์',
            'redirect' => 'User'
        ];
    }
}



    /**
     * สร้าง Token สำหรับ SSO และสร้าง URL สำหรับเข้าสู่ระบบ
     */
    /**
     * สร้าง Token สำหรับ SSO และสร้าง URL สำหรับเข้าสู่ระบบ
     */
   public function generate_sso_token()
    {
        try {
            // ตรวจสอบว่าเป็น AJAX request หรือไม่
            if (!$this->input->is_ajax_request()) {
                $response = [
                    'status' => 'error',
                    'message' => 'วิธีการเข้าถึงไม่ถูกต้อง กรุณาใช้งานผ่าน AJAX เท่านั้น'
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            }

            // *** ตรวจสอบการล็อกอินแบบครบถ้วน ***
            $login_check = $this->comprehensive_login_check();
            if (!$login_check['success']) {
                $response = [
                    'status' => 'error',
                    'message' => $login_check['message'],
                    'redirect' => site_url($login_check['redirect'])
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            }

            // รับค่าจาก POST
            $module_code = $this->input->post('module_code');
            $service_url = $this->input->post('service_url');

            log_message('debug', "generate_sso_token - module_code: $module_code, service_url: $service_url");

            // ตรวจสอบข้อมูลที่ส่งมา
            if (empty($module_code) || empty($service_url)) {
                $response = [
                    'status' => 'error',
                    'message' => 'ข้อมูลไม่ครบถ้วน กรุณาระบุ module_code และ service_url'
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึงโมดูล
            if (method_exists($this, 'check_module_access')) {
                $has_access = $this->check_module_access($module_code);
                if (!$has_access) {
                    $response = [
                        'status' => 'access_denied',
                        'message' => 'ท่านไม่มีสิทธิ์เข้าใช้งานระบบนี้ กรุณาติดต่อผู้ดูแลระบบ'
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }
            }

            // สร้างและบันทึก token
            $token = $this->generate_and_save_token();
            if (!$token) {
                $response = [
                    'status' => 'error',
                    'message' => 'ไม่สามารถสร้าง token ได้ กรุณาลองใหม่อีกครั้ง'
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            }

            // สร้าง URL สำหรับ redirect พร้อม token และข้อมูลผู้ใช้
            $redirect_url = $this->build_redirect_url($service_url, $token, $module_code);

            // บันทึก log การเข้าใช้งานระบบ
            if (isset($this->user_log_model)) {
                $this->user_log_model->log_activity(
                    $this->session->userdata('mp_email'),
                    'generate_sso_token',
                    'ผู้ใช้สร้าง SSO token สำหรับระบบ ' . $module_code,
                    'service'
                );
            }

            // ส่งผลลัพธ์กลับ
            $response = [
                'status' => 'success',
                'message' => 'สร้าง token สำเร็จ',
                'redirect_url' => $redirect_url
            ];

            log_message('debug', "generate_sso_token - success: $redirect_url");
            $this->output->set_content_type('application/json')->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Error in generate_sso_token: ' . $e->getMessage());

            $response = [
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้งหรือติดต่อผู้ดูแลระบบ'
            ];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }

		
		
		private function update_session_if_incomplete($user_data)
{
    try {
        $need_update = false;
        $update_data = array();

        error_log("SSO: Checking session completeness for user: " . $user_data->mp_id);

        // ตรวจสอบและอัพเดทข้อมูลที่ขาดหาย
        $fields_to_check = [
            'mp_email' => $user_data->mp_email,
            'mp_fname' => $user_data->mp_fname,
            'mp_lname' => $user_data->mp_lname,
            'mp_prefix' => isset($user_data->mp_prefix) ? $user_data->mp_prefix : '',
            'mp_phone' => isset($user_data->mp_phone) ? $user_data->mp_phone : '',
            'mp_number' => isset($user_data->mp_number) ? $user_data->mp_number : '',
            'mp_address' => isset($user_data->mp_address) ? $user_data->mp_address : '',
            'mp_img' => isset($user_data->mp_img) ? $user_data->mp_img : ''
        ];

        foreach ($fields_to_check as $field => $value) {
            $session_value = $this->session->userdata($field);
            if (empty($session_value) && !empty($value)) {
                $update_data[$field] = $value;
                $need_update = true;
                error_log("SSO: Missing session field '$field', will update with: " . substr($value, 0, 50));
            }
        }

        // เพิ่มข้อมูลพื้นฐานที่จำเป็น
        $basic_fields = [
            'is_public' => true,
            'user_type' => 'public',
            'permissions' => 'ex_user'
        ];

        foreach ($basic_fields as $field => $value) {
            if (!$this->session->userdata($field)) {
                $update_data[$field] = $value;
                $need_update = true;
                error_log("SSO: Missing basic field '$field', will set to: $value");
            }
        }

        // ตรวจสอบข้อมูล tenant
        $tenant_id = $this->session->userdata('tenant_id');
        $tenant_code = $this->session->userdata('tenant_code');
        
        if (!$tenant_id || !$tenant_code || $tenant_code === 'default') {
            try {
                $tenant = $this->tenant_access_model->get_tenant_by_domain($_SERVER['HTTP_HOST']);
                if ($tenant) {
                    $update_data['tenant_id'] = $tenant->id;
                    $update_data['tenant_code'] = $tenant->code;
                    $update_data['tenant_name'] = $tenant->name;
                    $update_data['tenant_domain'] = $tenant->domain;
                    $need_update = true;
                    error_log("SSO: Updated tenant info - ID: {$tenant->id}, Code: {$tenant->code}");
                }
            } catch (Exception $e) {
                error_log("SSO: Error getting tenant info: " . $e->getMessage());
            }
        }

        if ($need_update) {
            $this->session->set_userdata($update_data);
            error_log('SSO: Updated incomplete session data for user: ' . $user_data->mp_id);
            error_log('SSO: Updated fields: ' . implode(', ', array_keys($update_data)));
            
            // ตรวจสอบว่าการอัพเดทสำเร็จ
            $check_field = array_keys($update_data)[0];
            if ($this->session->userdata($check_field) !== $update_data[$check_field]) {
                error_log("SSO: WARNING - Session update might have failed");
                return false;
            }
            
            return true;
        }

        error_log("SSO: Session data is complete, no update needed");
        return false;

    } catch (Exception $e) {
        error_log('Error updating session: ' . $e->getMessage());
        return false;
    }
}
		
		public function debug_sso_session()
{
    if (!$this->session->userdata('mp_id')) {
        echo "<h3>❌ ไม่ได้เข้าสู่ระบบ</h3>";
        echo "<p><a href='" . site_url('User') . "'>เข้าสู่ระบบ</a></p>";
        return;
    }

    echo "<h2>🔍 Debug SSO Session Status</h2>";
    echo "<style>
        .debug-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d1fae5; border-color: #10b981; }
        .warning { background: #fef3c7; border-color: #f59e0b; }
        .error { background: #fee2e2; border-color: #ef4444; }
        .code { font-family: monospace; background: #f3f4f6; padding: 2px 4px; border-radius: 3px; }
    </style>";

    $mp_id = $this->session->userdata('mp_id');
    
    echo "<div class='debug-box'>";
    echo "<h3>📋 Current Session Data</h3>";
    echo "<ul>";
    echo "<li><strong>mp_id:</strong> <span class='code'>$mp_id</span></li>";
    echo "<li><strong>mp_email:</strong> <span class='code'>" . ($this->session->userdata('mp_email') ?: 'NULL') . "</span></li>";
    echo "<li><strong>mp_fname:</strong> <span class='code'>" . ($this->session->userdata('mp_fname') ?: 'NULL') . "</span></li>";
    echo "<li><strong>mp_lname:</strong> <span class='code'>" . ($this->session->userdata('mp_lname') ?: 'NULL') . "</span></li>";
    echo "<li><strong>mp_phone:</strong> <span class='code'>" . ($this->session->userdata('mp_phone') ?: 'NULL') . "</span></li>";
    echo "<li><strong>mp_address:</strong> <span class='code'>" . ($this->session->userdata('mp_address') ?: 'NULL') . "</span></li>";
    echo "<li><strong>is_public:</strong> <span class='code'>" . ($this->session->userdata('is_public') ? 'TRUE' : 'FALSE') . "</span></li>";
    echo "<li><strong>2fa_verified:</strong> <span class='code'>" . ($this->session->userdata('2fa_verified') ? 'TRUE' : 'FALSE') . "</span></li>";
    echo "<li><strong>trusted_device:</strong> <span class='code'>" . ($this->session->userdata('trusted_device') ? 'TRUE' : 'FALSE') . "</span></li>";
    echo "<li><strong>tenant_id:</strong> <span class='code'>" . ($this->session->userdata('tenant_id') ?: 'NULL') . "</span></li>";
    echo "<li><strong>tenant_code:</strong> <span class='code'>" . ($this->session->userdata('tenant_code') ?: 'NULL') . "</span></li>";
    echo "<li><strong>permissions:</strong> <span class='code'>" . ($this->session->userdata('permissions') ?: 'NULL') . "</span></li>";
    echo "</ul>";
    echo "</div>";

    // ทดสอบ comprehensive_login_check
    echo "<div class='debug-box'>";
    echo "<h3>🔐 Comprehensive Login Check Test</h3>";
    
    $login_check = $this->comprehensive_login_check();
    if ($login_check['success']) {
        echo "<div class='success'>";
        echo "<h4>✅ Login Check: PASSED</h4>";
        echo "<p>User data loaded successfully</p>";
        if (isset($login_check['user_data'])) {
            echo "<p><strong>Database user email:</strong> " . $login_check['user_data']->mp_email . "</p>";
        }
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h4>❌ Login Check: FAILED</h4>";
        echo "<p><strong>Message:</strong> " . $login_check['message'] . "</p>";
        echo "<p><strong>Redirect:</strong> " . $login_check['redirect'] . "</p>";
        echo "</div>";
    }
    echo "</div>";

    // ทดสอบการสร้าง token
    echo "<div class='debug-box'>";
    echo "<h3>🎫 Token Generation Test</h3>";
    
    try {
        $token = $this->generate_and_save_token();
        if ($token) {
            echo "<div class='success'>";
            echo "<h4>✅ Token Generated Successfully</h4>";
            echo "<p><strong>Token:</strong> <span class='code'>" . substr($token, 0, 20) . "...</span></p>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h4>❌ Token Generation Failed</h4>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<h4>❌ Token Generation Error</h4>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
    echo "</div>";

    // ทดสอบการสร้าง URL
    echo "<div class='debug-box'>";
    echo "<h3>🔗 URL Building Test</h3>";
    if (isset($token) && $token) {
        $test_systems = [
            ['code' => 'tax', 'name' => 'ระบบจ่ายภาษี', 'url' => 'localtax.assystem.co.th'],
            ['code' => 'qcar', 'name' => 'ระบบจองคิวรถ', 'url' => 'carbooking.assystem.co.th']
        ];
        
        foreach ($test_systems as $system) {
            $test_url = $this->build_redirect_url($system['url'], $token, $system['code']);
            echo "<p><strong>{$system['name']}:</strong></p>";
            echo "<p style='word-break: break-all; background: #f0f0f0; padding: 10px; font-family: monospace; font-size: 12px;'>";
            echo "<a href='$test_url' target='_blank'>$test_url</a>";
            echo "</p>";
        }
    } else {
        echo "<p>❌ Cannot test URL building - no token available</p>";
    }
    echo "</div>";

    echo "<div class='debug-box'>";
    echo "<p><a href='" . site_url('Pages/service_systems') . "'>🏠 กลับหน้าบริการ</a></p>";
    echo "<p><a href='" . site_url('Auth_public_mem/profile') . "'>👤 ไปหน้าโปรไฟล์</a></p>";
    echo "</div>";
}

    /**
     * สร้างและบันทึก token สำหรับ SSO
     * @return string|bool token หรือ false ถ้าล้มเหลว
     */
    private function generate_and_save_token()
    {
        try {
            // ดึงข้อมูล tenant
            $tenant = $this->tenant_access_model->get_tenant_by_domain($_SERVER['HTTP_HOST']);
            if (!$tenant) {
                log_message('error', 'SSO: Tenant not found for domain: ' . $_SERVER['HTTP_HOST']);
                return false;
            }

            $mp_id = $this->session->userdata('mp_id');
            $current_time = date('Y-m-d H:i:s');

            // ตรวจสอบ token ที่ยังไม่หมดอายุ
            $existing_token = $this->db->where([
                'user_id' => $mp_id,
                'domain' => $_SERVER['HTTP_HOST'],
                'expires_at >' => $current_time
            ])->get('auth_tokens')->row();

            // ถ้ามี token ที่ยังใช้ได้อยู่ ให้ใช้ token เดิม
            if ($existing_token) {
                log_message('debug', 'SSO: Using existing token for user: ' . $mp_id);
                return $existing_token->token;
            }

            // สร้าง token ใหม่
            $token = hash('sha256', $mp_id . time() . uniqid('', true));

            // *** เพิ่มข้อมูล 2FA status ใน token data ***
            $token_data = array(
                'token' => $token,
                'user_id' => $mp_id,
                'ipaddress' => $this->input->ip_address(),
                'domain' => $_SERVER['HTTP_HOST'],
                'tenant_id' => $tenant->id,
                'tenant_code' => $tenant->code,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'user_type' => 'public',  // เพิ่ม user_type
                '2fa_verified' => $this->session->userdata('2fa_verified') ? 1 : 0,  // เพิ่ม 2FA status
                'trusted_device' => $this->session->userdata('trusted_device') ? 1 : 0,  // เพิ่ม trusted device
                'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
                'created_at' => $current_time
            );

            // ลบ token เก่าที่หมดอายุ
            $this->db->where([
                'user_id' => $mp_id,
                'domain' => $_SERVER['HTTP_HOST'],
                'expires_at <=' => $current_time
            ])->delete('auth_tokens');

            // บันทึก token ใหม่
            $insert_result = $this->db->insert('auth_tokens', $token_data);
            
            if ($insert_result && $this->db->error()['code'] === 0) {
                log_message('debug', 'SSO: New token created successfully for user: ' . $mp_id);
                return $token;
            } else {
                log_message('error', 'SSO: Failed to insert token. DB Error: ' . print_r($this->db->error(), true));
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error in generate_and_save_token: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * สร้าง URL สำหรับ redirect พร้อม token และข้อมูลผู้ใช้
     * @param string $service_url URL ของบริการ
     * @param string $token Token ที่สร้างขึ้น
     * @param string $module_code รหัสโมดูล
     * @return string URL ที่สร้างขึ้น
     */
    private function build_redirect_url($service_url, $token, $module_code)
    {
        try {
            // ตรวจสอบโปรโตคอล (http/https)
            if (strpos($service_url, 'http') !== 0) {
                $service_url = 'https://' . $service_url;
            }

            // ตรวจสอบว่ามี /auth/public_login ต่อท้ายหรือไม่
            if (strpos($service_url, '/auth/public_login') === false) {
                $service_url .= '/auth/public_login';
            }

            // สร้างอาร์เรย์ข้อมูลพื้นฐานของผู้ใช้
            $user_data = [
                // ข้อมูล token และการยืนยันตัวตน
                'token' => $token,
                'module_code' => $module_code,
                'timestamp' => time(),

                // ข้อมูลพื้นฐานของผู้ใช้
                'mp_id' => $this->session->userdata('mp_id'),
                'mp_email' => $this->session->userdata('mp_email'),
                'mp_fname' => $this->session->userdata('mp_fname'),
                'mp_lname' => $this->session->userdata('mp_lname'),
                
                // *** เพิ่ม: ข้อมูล 2FA และ security status ***
                '2fa_verified' => $this->session->userdata('2fa_verified') ? '1' : '0',
                'trusted_device' => $this->session->userdata('trusted_device') ? '1' : '0',
                'user_type' => 'public'
            ];

            // เพิ่มข้อมูลเพิ่มเติมของผู้ใช้ (ถ้ามีในเซสชัน)
            $additional_fields = [
                'mp_prefix',
                'mp_phone',
                'mp_number',
                'mp_address',
                'mp_img'
            ];

            foreach ($additional_fields as $field) {
                $value = $this->session->userdata($field);
                if ($value) {
                    $user_data[$field] = $value;
                }
            }

            // เพิ่มข้อมูล tenant (ถ้ามีในเซสชัน)
            $tenant_fields = ['tenant_id', 'tenant_code', 'tenant_name'];
            foreach ($tenant_fields as $field) {
                $value = $this->session->userdata($field);
                if ($value) {
                    $user_data[$field] = $value;
                }
            }

            // สร้าง URL พร้อม query string
            $redirect_url = $service_url . '?' . http_build_query($user_data);

            log_message('debug', 'SSO: Built redirect URL with ' . count($user_data) . ' parameters');

            return $redirect_url;

        } catch (Exception $e) {
            log_message('error', 'Error in build_redirect_url: ' . $e->getMessage());
            return $service_url . '?error=1';
        }
    }
    /**
     * ตรวจสอบสิทธิ์การเข้าถึงระบบ
     * @param string $module_code รหัสโมดูล
     * @return bool true หากมีสิทธิ์, false หากไม่มีสิทธิ์
     */
    private function check_module_access($module_code)
    {
        try {
            log_message('debug', "Checking access for module code: $module_code");

            // แก้ไขให้ใช้ tenant_access_model ในการตรวจสอบสิทธิ์
            $access_result = $this->tenant_access_model->check_module_access_by_domain($_SERVER['HTTP_HOST'], $module_code);

            if (!$access_result) {
                log_message('debug', "ไม่พบสิทธิ์การเข้าถึงสำหรับโมดูล $module_code จากโดเมน {$_SERVER['HTTP_HOST']}");

                // ตรวจสอบว่ามีตาราง tbl_public_user_access หรือไม่
                if ($this->db->table_exists('tbl_public_user_access')) {
                    $module = $this->tenant_access_model->get_module_by_code($module_code);

                    if ($module) {
                        // ตรวจสอบสิทธิ์เฉพาะของผู้ใช้
                        $user_access = $this->db->where('public_user_id', $this->session->userdata('mp_id'))
                            ->where('module_id', $module->id)
                            ->get('tbl_public_user_access')
                            ->row();

                        if ($user_access) {
                            log_message('debug', "พบสิทธิ์เฉพาะสำหรับผู้ใช้ ID {$this->session->userdata('mp_id')} และโมดูล $module_code");
                            return true;
                        }
                    }
                }

                return false;
            }

            log_message('debug', "พบสิทธิ์การเข้าถึงสำหรับโมดูล $module_code จากโดเมน {$_SERVER['HTTP_HOST']}");
            return true;

        } catch (Exception $e) {
            log_message('error', "Error in check_module_access: " . $e->getMessage());
            return false;
        }
    }
		
		
		
		
		
		
		

    public function test_token_generation()
    {
        try {
            echo "<h1>ทดสอบการสร้าง Token</h1>";
            echo "<pre>";

            // ตรวจสอบข้อมูล session
            echo "ข้อมูล Session:<br>";
            print_r($this->session->userdata());
            echo "<br><br>";

            // ทดสอบการเชื่อมต่อกับ tenant_access_model
            echo "ทดสอบการเชื่อมต่อ tenant_access_model:<br>";
            $tenant = $this->tenant_access_model->get_tenant_by_domain($_SERVER['HTTP_HOST']);
            print_r($tenant);
            echo "<br><br>";

            // ตรวจสอบโครงสร้างตาราง auth_tokens
            echo "โครงสร้างตาราง auth_tokens:<br>";
            if ($this->db->table_exists('auth_tokens')) {
                $fields = $this->db->list_fields('auth_tokens');
                print_r($fields);
            } else {
                echo "ตาราง auth_tokens ไม่มีอยู่!";
            }
            echo "<br><br>";

            // ทดสอบการสร้าง token
            echo "ทดสอบการสร้าง token:<br>";
            $token = $this->generate_and_save_token();
            echo "ผลลัพธ์: " . ($token ? "สำเร็จ - $token" : "ล้มเหลว");
            echo "<br><br>";

            // ทดสอบการสร้าง URL
            if ($token) {
                echo "ทดสอบการสร้าง URL:<br>";
                $url = $this->build_redirect_url('carbooking.assystem.co.th', $token, 'qcar');
                echo $url;
                echo "<br><a href='$url' target='_blank'>ทดสอบเปิด URL</a>";
            }

            echo "</pre>";
        } catch (Exception $e) {
            echo "<h2>เกิดข้อผิดพลาด:</h2>";
            echo "<pre>";
            echo "Message: " . $e->getMessage() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Trace:\n" . $e->getTraceAsString();
            echo "</pre>";
        }
    }

    /**
     * รับคำขอจาก simple button และ redirect ไปยังระบบที่ต้องการ
     * 
     * @param string $module_code รหัสโมดูล
     * @param string $service_url URL ของบริการ
     */
    public function redirect_to_service($module_code = '', $service_url = '')
    {
        try {
            log_message('debug', "SSO redirect_to_service - module_code: $module_code, service_url: $service_url");

            // ตรวจสอบข้อมูล
            if (empty($module_code) || empty($service_url)) {
                log_message('error', 'SSO redirect failed - Missing parameters');
                $this->session->set_flashdata('error', 'ข้อมูลไม่ครบถ้วน กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
                return;
            }

            // *** ตรวจสอบการล็อกอินแบบครบถ้วนอีกครั้ง ***
            $login_check = $this->comprehensive_login_check();
            if (!$login_check['success']) {
                log_message('error', 'SSO redirect failed - Login check failed: ' . $login_check['message']);
                $this->session->set_flashdata('error', $login_check['message']);
                redirect($login_check['redirect']);
                return;
            }

            $user_data = $login_check['user_data'];

            // ตรวจสอบสิทธิ์การเข้าถึงโมดูล
            if (method_exists($this, 'check_module_access')) {
                $has_access = $this->check_module_access($module_code);
                if (!$has_access) {
                    log_message('warning', "SSO redirect failed - No access to module: $module_code for user: " . $user_data->mp_email);
                    $this->session->set_flashdata('error', 'ท่านไม่มีสิทธิ์เข้าใช้งานระบบนี้ กรุณาติดต่อผู้ดูแลระบบ');
                    redirect('Pages/service_systems');
                    return;
                }
            }

            // สร้าง token สำหรับ SSO
            $token = $this->generate_and_save_token();
            if (!$token) {
                log_message('error', 'SSO redirect failed - Cannot generate token');
                $this->session->set_flashdata('error', 'ไม่สามารถสร้าง token ได้ กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
                return;
            }

            // สร้าง URL สำหรับ redirect ไปยังระบบ
            $redirect_url = $this->build_redirect_url($service_url, $token, $module_code);

            // บันทึก log การเข้าใช้งานระบบ
            if (isset($this->user_log_model)) {
                $this->user_log_model->log_activity(
                    $user_data->mp_email,
                    'sso_redirect',
                    'ประชาชนเข้าสู่ระบบบริการ ' . $module_code . ' ผ่าน SSO',
                    'service_access'
                );
            }

            log_message('info', 'SSO redirect successful - User: ' . $user_data->mp_email . ', Service: ' . $module_code . ', URL: ' . substr($redirect_url, 0, 100) . '...');

            // Redirect ไปยัง URL ที่สร้างขึ้น
            redirect($redirect_url);

        } catch (Exception $e) {
            log_message('error', 'Error in SSO redirect_to_service: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง');
            redirect('Pages/service_systems');
        }
    }


    /**
     * ทดสอบระบบการเชื่อมต่อโดยใช้ token ที่มีอยู่แล้ว
     */
    public function test_existing_token()
    {
        // ตรวจสอบการล็อกอินก่อน
        if (!$this->session->userdata('mp_id')) {
            echo "<h2>ไม่พบข้อมูลการล็อกอิน</h2>";
            echo "<p>กรุณาเข้าสู่ระบบก่อนทดสอบ: <a href='" . site_url('User') . "'>เข้าสู่ระบบ</a></p>";
            return;
        }

        // สร้าง HTML header พร้อม CSS พื้นฐาน
        echo '<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบการใช้ Token ที่มีอยู่</title>
    <style>
        body { font-family: "Sarabun", sans-serif; margin: 0; padding: 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        h3 { color: #0066cc; margin-top: 20px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        p { margin: 10px 0; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 5px; }
        a { color: #0066cc; text-decoration: none; padding: 5px 10px; margin-right: 10px; }
        a:hover { text-decoration: underline; }
        .btn { background-color: #0066cc; color: white; padding: 8px 15px; border-radius: 5px; display: inline-block; }
        .btn:hover { background-color: #0052a3; text-decoration: none; }
        .info-label { font-weight: bold; min-width: 120px; display: inline-block; }
        .token-row { display: flex; align-items: center; margin-bottom: 5px; }
        .token-value { margin-right: 10px; font-family: monospace; }
        .expires { color: #666; font-size: 0.9em; }
        .action-links { margin-top: 20px; }
        .action-links a { margin-right: 15px; }
        .systems-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .system-link { padding: 8px 12px; background-color: #f0f7ff; border-radius: 4px; border: 1px solid #cce5ff; }
        .system-link:hover { background-color: #e3f2fd; }
        .info-section { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .params-display { max-height: 300px; overflow-y: auto; background-color: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 0.9em; }
        table { width: 100%; border-collapse: collapse; }
        table td, table th { padding: 8px; border: 1px solid #ddd; }
        table th { background-color: #f2f2f2; text-align: left; }
        .scroll-x { overflow-x: auto; }
        .address-box { 
            background-color: #f8f8f8; 
            border: 1px solid #e0e0e0; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0;
            white-space: pre-wrap;
            line-height: 1.5;
        }
        .highlight {
            background-color: #e6f7ff;
            border-left: 3px solid #1890ff;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <h1>ทดสอบการใช้ Token ที่มีอยู่</h1>';

        // แสดงข้อมูลผู้ใช้
        echo '<div class="info-section">
    <h3>ข้อมูลผู้ใช้</h3>
    <div class="grid-container">';

        $user_fields = [
            'mp_id' => 'รหัสผู้ใช้',
            'mp_email' => 'อีเมล',
            'mp_prefix' => 'คำนำหน้า',
            'mp_fname' => 'ชื่อ',
            'mp_lname' => 'นามสกุล',
            'mp_phone' => 'เบอร์โทรศัพท์',
            'mp_number' => 'เลขบัตรประชาชน'
        ];

        foreach ($user_fields as $field => $label) {
            $value = $this->session->userdata($field);
            if ($value) {
                echo '<p><span class="info-label">' . $label . ':</span> ' . $value . '</p>';
            }
        }

        echo '</div>';

        // แสดงข้อมูลที่อยู่แยกออกมาเพื่อความชัดเจน
        $mp_address = $this->session->userdata('mp_address');
        if ($mp_address) {
            echo '<div class="highlight">
            <h4>ที่อยู่</h4>
            <div class="address-box">' . nl2br($mp_address) . '</div>
        </div>';
        } else {
            echo '<p><span class="info-label">ที่อยู่:</span> <em>ไม่มีข้อมูล</em></p>';
        }

        // แสดงรูปโปรไฟล์ถ้ามี
        $mp_img = $this->session->userdata('mp_img');
        if ($mp_img) {
            echo '<p><span class="info-label">รูปโปรไฟล์:</span></p>';
            echo '<div><img src="' . $mp_img . '" alt="รูปโปรไฟล์" style="max-width: 150px; max-height: 150px; border-radius: 5px;"></div>';
        }

        // แสดงข้อมูล tenant และ domain ถ้ามี
        if ($this->session->userdata('tenant_id') || $this->session->userdata('tenant_code') || $this->session->userdata('tenant_domain')) {
            echo '<h4>ข้อมูลหน่วยงาน</h4>';
            if ($this->session->userdata('tenant_id')) {
                echo '<p><span class="info-label">Tenant ID:</span> ' . $this->session->userdata('tenant_id') . '</p>';
            }
            if ($this->session->userdata('tenant_code')) {
                echo '<p><span class="info-label">Tenant Code:</span> ' . $this->session->userdata('tenant_code') . '</p>';
            }
            if ($this->session->userdata('tenant_domain')) {
                echo '<p><span class="info-label">Domain:</span> ' . $this->session->userdata('tenant_domain') . '</p>';
            }
        }
        echo '</div>';

        // ดึงข้อมูล token ที่มีอยู่
        $tokens = $this->db->where([
            'user_id' => $this->session->userdata('mp_id'),
            'domain' => $_SERVER['HTTP_HOST'],
            'expires_at >' => date('Y-m-d H:i:s')
        ])->order_by('created_at', 'DESC')->get('auth_tokens')->result();

        // แสดงข้อมูล token
        echo '<div class="info-section">
    <h3>Token ที่ยังไม่หมดอายุ</h3>';

        if (count($tokens) > 0) {
            echo '<p>พบ ' . count($tokens) . ' token ที่ยังใช้งานได้</p>
    <ul>';

            foreach ($tokens as $i => $token) {
                $short_token = substr($token->token, 0, 10) . "...";
                $expires_at = date('d/m/Y H:i:s', strtotime($token->expires_at));
                $created_at = date('d/m/Y H:i:s', strtotime($token->created_at));

                echo '<li id="token-' . $i . '">
            <div class="token-row">
                <div class="token-value"><strong>Token: </strong>' . $short_token . '</div>
                <button onclick="toggleToken(' . $i . ')" class="toggle-btn">แสดง/ซ่อน</button>
            </div>
            <div id="token-full-' . $i . '" style="display:none; margin: 5px 0; font-family: monospace; word-break: break-all; background: #f5f5f5; padding: 5px; border-radius: 3px;">' . $token->token . '</div>
            <div class="expires">
                <div>สร้างเมื่อ: ' . $created_at . '</div>
                <div>หมดอายุ: ' . $expires_at . '</div>
            </div>
            <div class="systems-list">
                <strong>ทดสอบกับระบบ: </strong>';

                // ระบบที่ต้องการทดสอบ
                $test_systems = [
                    'qcar' => ['name' => 'ระบบจองคิวรถ', 'url' => 'carbooking.assystem.co.th'],
                    'tax' => ['name' => 'ระบบจ่ายภาษี', 'url' => 'localtax.assystem.co.th'],
                    'service' => ['name' => 'ระบบร้องเรียน', 'url' => 'publicservice.assystem.co.th']
                ];

                foreach ($test_systems as $code => $system) {
                    $test_url = $this->build_redirect_url($system['url'], $token->token, $code);
                    echo '<a href="' . $test_url . '" target="_blank" class="system-link">' . $system['name'] . '</a>';

                    // เพิ่มปุ่มดูพารามิเตอร์
                    echo '<button onclick="showParams(\'' . addslashes($test_url) . '\', \'' . $system['name'] . '\')" class="system-link">ดูพารามิเตอร์</button>';
                }

                echo '</div>
        </li>';
            }

            echo '</ul>';
        } else {
            echo '<p>ไม่พบ token ที่ยังไม่หมดอายุ</p>';
        }
        echo '</div>';

        // Modal สำหรับแสดงพารามิเตอร์
        echo '<div id="paramsModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.7); z-index:1000;">
        <div style="position:relative; width:80%; max-width:800px; margin:50px auto; background:white; padding:20px; border-radius:5px;">
            <button onclick="closeParamsModal()" style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
            <h3 id="paramsTitle"></h3>
            <div class="scroll-x">
                <table id="paramsTable">
                    <thead>
                        <tr>
                            <th>พารามิเตอร์</th>
                            <th>ค่า</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>';

        // แสดงลิงก์ดำเนินการต่างๆ
        echo '<div class="action-links">
    <a href="' . site_url('Member_public_sso/redirect_to_service/qcar/carbooking.assystem.co.th') . '" class="btn">สร้าง Token ใหม่และทดสอบกับระบบจองคิวรถ</a>
    <a href="' . site_url('Member_public_sso/redirect_to_service/tax/localtax.assystem.co.th') . '" class="btn">สร้าง Token ใหม่และทดสอบกับระบบจ่ายภาษี</a>
    <a href="' . site_url('Pages/service_systems') . '">กลับไปหน้าบริการ</a>
</div>';

        // เพิ่ม JavaScript
        echo '<script>
    function toggleToken(id) {
        var fullToken = document.getElementById("token-full-" + id);
        if (fullToken.style.display === "none") {
            fullToken.style.display = "block";
        } else {
            fullToken.style.display = "none";
        }
    }
    
    function showParams(url, systemName) {
        // แยก query string จาก URL
        var parsedUrl = new URL(url);
        var params = new URLSearchParams(parsedUrl.search);
        
        // เคลียร์ตารางเดิม
        var tbody = document.querySelector("#paramsTable tbody");
        tbody.innerHTML = "";
        
        // เพิ่มพารามิเตอร์ลงในตาราง
        params.forEach(function(value, key) {
            var row = document.createElement("tr");
            
            var keyCell = document.createElement("td");
            keyCell.textContent = key;
            row.appendChild(keyCell);
            
            var valueCell = document.createElement("td");
            
            // ไฮไลท์ค่า mp_address ด้วยสีพื้นหลัง
            if (key === "mp_address") {
                valueCell.style.backgroundColor = "#e6f7ff";
                valueCell.style.fontWeight = "bold";
            }
            
            valueCell.textContent = value;
            row.appendChild(valueCell);
            
            tbody.appendChild(row);
        });
        
        // ตั้งชื่อและแสดง modal
        document.getElementById("paramsTitle").textContent = "พารามิเตอร์สำหรับ " + systemName;
        document.getElementById("paramsModal").style.display = "block";
    }
    
    function closeParamsModal() {
        document.getElementById("paramsModal").style.display = "none";
    }
</script>
</body>
</html>';
    }

    /**
     * ทดสอบการทำงานของระบบ SSO
     */
    public function test()
    {
        echo '<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบระบบ SSO</title>
    <style>
        body { font-family: "Sarabun", sans-serif; margin: 0; padding: 20px; }
        h1, h2 { color: #333; margin-bottom: 15px; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .error { color: #ff0000; padding: 10px; border: 1px solid #ff0000; margin: 10px 0; }
        .success { color: #008000; }
        .field-label { font-weight: bold; color: #0066cc; }
        .field-value { margin-left: 10px; }
        .address-box { 
            background-color: #e6f7ff; 
            border: 1px solid #91caff; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0;
            white-space: pre-wrap;
            line-height: 1.5;
        }
        .token-box {
            background-color: #f5f5f5;
            border: 1px solid #d9d9d9;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
            margin: 10px 0;
        }
        .parameter-section {
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .highlight {
            background-color: #fff7e6;
            border-left: 3px solid #ffa940;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>';

        echo '<h1>ทดสอบระบบ SSO</h1>';

        echo '<h2>ข้อมูล Session:</h2>';
        echo '<pre>';
        print_r($this->session->userdata());
        echo '</pre>';

        echo '<h2>รายการ Token ปัจจุบัน:</h2>';
        try {
            // ดึงข้อมูล token ที่ยังไม่หมดอายุ
            $tokens = $this->db->where('user_id', $this->session->userdata('mp_id'))
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->order_by('created_at', 'DESC')
                ->get('auth_tokens')
                ->result();

            echo '<pre>';
            if (count($tokens) > 0) {
                foreach ($tokens as $token) {
                    echo "Token: {$token->token}\n";
                    echo "Expires At: {$token->expires_at}\n";
                    echo "Created At: {$token->created_at}\n";
                    echo "-----------------------\n";
                }
            } else {
                echo "ไม่พบ Token ที่ยังไม่หมดอายุ\n";
            }
            echo '</pre>';

        } catch (Exception $e) {
            echo '<div class="error">';
            echo 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            echo '</div>';
        }

        echo '<h2>ข้อมูลผู้ใช้ (สำหรับ SSO):</h2>';

        $user_fields = [
            'mp_id' => 'รหัสผู้ใช้',
            'mp_email' => 'อีเมล',
            'mp_fname' => 'ชื่อ',
            'mp_lname' => 'นามสกุล',
            'mp_prefix' => 'คำนำหน้า',
            'mp_phone' => 'เบอร์โทรศัพท์',
            'mp_number' => 'เลขบัตรประชาชน'
        ];

        foreach ($user_fields as $field => $label) {
            $value = $this->session->userdata($field);
            echo '<div><span class="field-label">' . str_pad($label, 20) . ":</span>";
            echo '<span class="field-value">' . ($value ? $value : '-') . "</span></div>";
        }

        // แสดงข้อมูลที่อยู่แยกต่างหาก
        $mp_address = $this->session->userdata('mp_address');
        echo '<div class="highlight">';
        echo '<span class="field-label">ที่อยู่:</span>';
        if ($mp_address) {
            echo '<div class="address-box">' . nl2br($mp_address) . '</div>';
        } else {
            echo ' <em>ไม่มีข้อมูล</em>';
        }
        echo '</div>';

        // แสดงข้อมูลรูปโปรไฟล์
        $mp_img = $this->session->userdata('mp_img');
        echo '<div><span class="field-label">รูปโปรไฟล์:</span>';
        if ($mp_img) {
            echo '<div><img src="' . $mp_img . '" alt="รูปโปรไฟล์" style="max-width: 150px; max-height: 150px; border-radius: 5px; margin-top: 10px;"></div>';
        } else {
            echo ' <em>ไม่มีข้อมูล</em>';
        }
        echo '</div>';

        echo '<h2>ทดสอบสร้าง Token:</h2>';
        $new_token = $this->generate_and_save_token();
        echo '<div class="success">New Token:</div>';
        echo '<div class="token-box">' . $new_token . '</div>';

        echo '<h2>ทดสอบสร้าง URL:</h2>';
        $test_url = $this->build_redirect_url('localtax.assystem.co.th', $new_token, 'tax');
        echo '<p>Test URL (พร้อมข้อมูลเพิ่มเติม):</p>';
        echo '<div style="word-break: break-all; margin: 10px 0;">';
        echo '<a href="' . $test_url . '" target="_blank">' . $test_url . '</a>';
        echo '</div>';

        echo '<p>Parameter ทั้งหมดที่ส่งไป:</p>';
        $parsed_url = parse_url($test_url);
        parse_str($parsed_url['query'], $params);

        echo '<div class="parameter-section">';
        foreach ($params as $key => $value) {
            echo '<div>';
            echo '<span class="field-label">' . $key . ':</span>';

            // ถ้าเป็น mp_address ให้แสดงในรูปแบบพิเศษ
            if ($key === 'mp_address' && !empty($value)) {
                echo '<div class="address-box">' . nl2br($value) . '</div>';
            } else {
                echo '<span class="field-value">' . $value . '</span>';
            }

            echo '</div>';
        }
        echo '</div>';

        echo '</body></html>';
    }

}