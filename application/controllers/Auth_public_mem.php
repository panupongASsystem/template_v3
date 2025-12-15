<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller สำหรับการจัดการสมาชิกประชาชน พร้อม 2FA และ Security System
 * 
 * @version 2.0
 * @author AS System
 */
class Auth_public_mem extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // โหลด Models ที่จำเป็น
        $this->load->model('member_public_model');
        $this->load->model('user_log_model');
        $this->load->model('tax_user_log_model');
        $this->load->model('tenant_access_model');
        $this->load->model('email_otp_model');


        // โหลด Libraries สำหรับ 2FA และ Security
        $this->load->library('Google2FA');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->library('user_agent');
        $this->load->library('email');

        // โหลด Helpers
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('cookie');

        // ทำความสะอาด tokens
        $this->cleanup_tokens();

        // ตรวจสอบว่ากำลังทำการ logout หรือไม่
        $current_method = $this->router->fetch_method();
        if ($current_method != 'logout' && $current_method != 'clear_session') {
            $this->set_tenant_data_from_domain();
        }
    }

    /**
     * ฟังก์ชันตรวจสอบและตั้งค่า tenant data จาก domain
     */
    private function set_tenant_data_from_domain()
    {
        // ดึง domain ปัจจุบัน
        $current_domain = $_SERVER['HTTP_HOST'];

        // เรียกใช้ model เพื่อดึงข้อมูล tenant
        $tenant_info = $this->tenant_access_model->get_tenant_by_domain($current_domain);

        if ($tenant_info) {
            // บันทึกข้อมูล tenant ลงใน session เฉพาะกรณีที่ยังไม่มีข้อมูล
            if (!$this->session->userdata('tenant_id')) {
                $tenant_data = array(
                    'tenant_id' => $tenant_info->id,
                    'tenant_code' => $tenant_info->code,
                    'tenant_name' => $tenant_info->name,
                    'tenant_domain' => $tenant_info->domain
                );

                $this->session->set_userdata($tenant_data);

                // บันทึกค่าเพิ่มเติมหากมี
                if (isset($tenant_info->m_img)) {
                    $this->session->set_userdata('m_img', $tenant_info->m_img);
                }
            }
        }
        $this->session->set_userdata('permissions', 'ex_user');
    }

    /**
     * เพิ่มฟังก์ชันพิเศษเพื่อดึงข้อมูล tenant โดยไม่ต้องพึ่งพา session ที่มีอยู่
     */
    public function refresh_tenant_data()
    {
        // ล้างข้อมูล tenant ที่มีอยู่
        $this->session->unset_userdata(array(
            'tenant_id',
            'tenant_code',
            'tenant_name',
            'tenant_domain',
            'permissions'
        ));

        // ดึงข้อมูล tenant ใหม่
        $this->set_tenant_data_from_domain();

        // Redirect กลับไปที่หน้า test เพื่อดูผลลัพธ์
        redirect('Auth_public_mem/test');
    }

    /**
     * หน้าแรก - แสดงฟอร์มเข้าสู่ระบบ
     */
    public function index()
    {
        // เช็คว่าล็อกอินแล้วหรือยัง
        if ($this->session->userdata('mp_id')) {
            $this->session->set_flashdata('stay_logged_in', TRUE);
            redirect('Pages/service_systems');
        }

        // โหลดหน้า login form
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/form_login');
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer_other');
    }

    /**
     * ตรวจสอบการเข้าสู่ระบบผ่าน API สำหรับประชาชน (พร้อม 2FA)
     */

    /**
     * ยืนยัน OTP สำหรับประชาชน
     */
    /**
     * ยืนยัน OTP สำหรับประชาชน (แก้ไขให้สร้าง session ครบถ้วน)
     */





    /**
     * ตรวจสอบความพร้อมใช้งานของอีเมล (AJAX)
     */
    public function check_email()
    {
        // ป้องกันการเข้าถึงโดยตรง
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // Set headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            // Get email from POST
            $email = $this->input->post('email');

            if (empty($email)) {
                echo json_encode([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'กรุณากรอกอีเมล'
                ]);
                return;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'รูปแบบอีเมลไม่ถูกต้อง'
                ]);
                return;
            }

            // Check if email exists in database
            $this->db->where('mp_email', $email);
            $query = $this->db->get('tbl_member_public');
            $exists = ($query->num_rows() > 0);

            // Log for debugging
            log_message('info', "Email check: $email - " . ($exists ? 'EXISTS' : 'AVAILABLE'));

            echo json_encode([
                'status' => 'success',
                'available' => !$exists,
                'message' => $exists ? 'อีเมลนี้ถูกใช้งานแล้ว' : 'อีเมลสามารถใช้งานได้'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in check_email: ' . $e->getMessage());

            echo json_encode([
                'status' => 'error',
                'available' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ'
            ]);
        }

        exit; // สำคัญ: ป้องกัน output เพิ่มเติม
    }
    public function verify_otp_public()
    {
        try {
            // ตรวจสอบ session
            if (!$this->session->userdata('temp_mp_id') || !$this->session->userdata('requires_2fa') || $this->session->userdata('temp_user_type') !== 'public') {
                $response = [
                    'status' => 'error',
                    'message' => 'Session หมดอายุ',
                    'redirect' => site_url('User')
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            }

            $otp = $this->input->post('otp');
            $remember_device = $this->input->post('remember_device');
            $secret = $this->session->userdata('temp_google2fa_secret');
            $tenant_id = $this->session->userdata('tenant_id') ?: 1;

            // ตรวจสอบเวลาหมดอายุ
            $login_time = $this->session->userdata('temp_login_time');
            if (!$login_time || (time() - $login_time) > 900) {
                $this->clear_temp_session();
                $response = [
                    'status' => 'error',
                    'message' => 'หมดเวลาการยืนยัน กรุณาเข้าสู่ระบบใหม่',
                    'redirect' => site_url('User')
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
                return;
            }

            // ตรวจสอบ OTP
            if ($this->google2fa->verifyKey($secret, $otp)) {
                $user_id = $this->session->userdata('temp_mp_id');

                // *** สำคัญ: บันทึก trusted device ก่อนสร้าง session ***
                $trusted_device_saved = false;
                if ($remember_device == '1') {
                    if ($this->db->table_exists('trusted_devices')) {
                        // ล้าง device เก่าของ user นี้ก่อน
                        $this->cleanup_user_trusted_devices($user_id, $tenant_id, 'public');

                        $device_token = $this->save_trusted_device($user_id, $tenant_id, 'public');
                        if ($device_token) {
                            $trusted_device_saved = true;
                            error_log("Trusted device saved successfully for user: $user_id with token: " . substr($device_token, 0, 8) . "...");
                        }
                    }
                }

                // ดึงข้อมูลผู้ใช้ครบถ้วน
                $user_data = $this->member_public_model->get_member_by_id($user_id);
                if (!$user_data) {
                    $response = [
                        'status' => 'error',
                        'message' => 'ไม่พบข้อมูลผู้ใช้',
                        'redirect' => site_url('User')
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // สร้าง session หลังจากบันทึก trusted device แล้ว
                $this->create_complete_public_session_from_data($user_data, true, $trusted_device_saved, $tenant_id);

                // ลบข้อมูลชั่วคราว
                $this->clear_temp_session();

                // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                $redirect_url = $this->session->userdata('redirect_after_login');

                if (!$redirect_url) {
                    $redirect_url = site_url('Pages/service_systems');
                } else {
                    // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                    $this->session->unset_userdata('redirect_after_login');
                }

                $response = [
                    'status' => 'success',
                    'message' => 'ยืนยันตัวตนสำเร็จ',
                    'redirect' => $redirect_url,
                    'trusted_device_saved' => $trusted_device_saved
                ];

                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'รหัส OTP ไม่ถูกต้อง'
                ];
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }

        } catch (Exception $e) {
            error_log("Exception in verify_otp_public: " . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }
    }




    /**
     * ตรวจสอบความซ้ำของเลขประจำตัวประชาชน (AJAX)
     */
    public function check_id_number()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $id_number = $this->input->post('id_number');

            if (empty($id_number)) {
                echo json_encode([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'กรุณากรอกเลขประจำตัวประชาชน'
                ]);
                return;
            }

            // ตรวจสอบรูปแบบพื้นฐาน
            if (!preg_match('/^\d{13}$/', $id_number)) {
                echo json_encode([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'เลขประจำตัวประชาชนต้องเป็นตัวเลข 13 หลัก'
                ]);
                return;
            }

            // 🆕 ตรวจสอบ pattern ไทย
            if (!$this->validate_thai_id_pattern($id_number)) {
                echo json_encode([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'รูปแบบเลขประจำตัวประชาชนไม่ถูกต้อง'
                ]);
                return;
            }

            // ตรวจสอบความซ้ำในฐานข้อมูล (ใช้ function เดิม)
            $this->db->where('mp_number', $id_number);
            $this->db->where('mp_number IS NOT NULL');
            $this->db->where('mp_number !=', '');
            $query = $this->db->get('tbl_member_public');
            $exists = ($query->num_rows() > 0);

            echo json_encode([
                'status' => 'success',
                'available' => !$exists,
                'message' => $exists ? 'เลขประจำตัวประชาชนนี้ถูกใช้งานแล้ว' : 'เลขประจำตัวประชาชนสามารถใช้งานได้'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in check_id_number: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'available' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ'
            ]);
        }

        exit;
    }

    // 🆕  method ใหม่สำหรับตรวจสอบ pattern ไทย
    private function validate_thai_id_pattern($id_number)
    {
        if (strlen($id_number) !== 13 || !ctype_digit($id_number)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $id_number[$i] * (13 - $i);
        }

        $checkDigit = (11 - ($sum % 11)) % 10;
        if ($checkDigit === 10)
            $checkDigit = 0;

        return $checkDigit === (int) $id_number[12];
    }


    // *** เพิ่ม: ฟังก์ชันสำหรับบันทึก redirect URL เมื่อต้องการ login ***
    public function save_redirect_url()
    {
        $redirect_url = $this->input->post('redirect_url');

        if ($redirect_url) {
            $this->session->set_userdata('redirect_after_login', $redirect_url);

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'บันทึก redirect URL สำเร็จ'
                ]);
                return;
            }
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่พบ redirect URL'
                ]);
                return;
            }
        }
    }

    public function check_login()
    {
        try {
            // ตรวจสอบ input
            if ($this->input->post('mp_email') == '' || $this->input->post('mp_password') == '') {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลชื่อผู้ใช้และรหัสผ่าน']);
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
            }

            // === เพิ่ม: ตรวจสอบการถูกบล็อค ===
            $block_status = $this->check_if_blocked($fingerprint);
            if ($block_status['is_blocked']) {
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
                        'block_level' => $block_status['block_level'] ?? 1
                    ]);
                    return;
                }

                echo "<script>";
                echo "alert('" . $block_message . "');";
                echo "window.history.back();";
                echo "</script>";
                return;
            }

            $result = $this->member_public_model->fetch_user_login(
                $this->input->post('mp_email'),
                sha1($this->input->post('mp_password'))
            );

            if (!empty($result)) {
                // ตรวจสอบสถานะผู้ใช้
                if (isset($result->mp_status) && $result->mp_status == 0) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['status' => 'error', 'message' => 'บัญชีนี้ถูกระงับการใช้งาน โปรดติดต่อผู้ให้บริการ']);
                        return;
                    }
                    echo "<script>";
                    echo "alert('บัญชีนี้ถูกระงับการใช้งาน โปรดติดต่อผู้ให้บริการ');";
                    echo "window.history.back();";
                    echo "</script>";
                    return;
                }

                // **ตรวจสอบ 2FA**
                if (!empty($result->google2fa_secret) && $result->google2fa_enabled == 1) {
                    // ตรวจสอบ Trusted Device
                    $tenant_id = $this->session->userdata('tenant_id') ?: 1;

                    if ($this->is_trusted_device($result->mp_id, $tenant_id, 'public')) {
                        error_log("Trusted device found for public user: " . $result->mp_email . " - Skipping 2FA");

                        // อัพเดทการใช้งานล่าสุด
                        $this->update_trusted_device_usage($result->mp_id);

                        // === เพิ่ม: รีเซ็ต failed attempts เมื่อ login สำเร็จ ===
                        $this->reset_failed_attempts($fingerprint);
                        $this->record_login_attempt($result->mp_email, 'success', $fingerprint);

                        // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                        $redirect_url = $this->session->userdata('redirect_after_login');

                        if (!$redirect_url) {
                            $redirect_url = site_url('Pages/service_systems');
                        } else {
                            // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                            $this->session->unset_userdata('redirect_after_login');
                        }

                        // สร้าง session ปกติ (Skip 2FA)
                        $this->create_public_session($result, true, true);

                        // บันทึก log การ login
                        $log_data = array(
                            'user_id' => $result->mp_id,
                            'user_type' => 'Public',
                            'action' => 'login',
                            'ip_address' => $this->input->ip_address(),
                            'user_agent' => $this->input->user_agent()
                        );
                        $this->tax_user_log_model->insert_log($log_data);

                        $this->generate_sso_token();
                        $this->session->set_flashdata('login_success', TRUE);

                        if ($this->input->is_ajax_request()) {
                            echo json_encode([
                                'status' => 'success',
                                'message' => 'เข้าสู่ระบบสำเร็จ',
                                'redirect' => $redirect_url
                            ]);
                            return;
                        }

                        redirect($redirect_url);
                        return;

                    } else {
                        // ต้องใช้ 2FA
                        // === เพิ่ม: รีเซ็ต failed attempts เมื่อผ่านการตรวจสอบ username/password ===
                        $this->reset_failed_attempts($fingerprint);
                        $this->record_login_attempt($result->mp_email, 'success', $fingerprint);

                        $temp_data = array(
                            'temp_mp_id' => $result->mp_id,
                            'temp_mp_email' => $result->mp_email,
                            'temp_mp_fname' => $result->mp_fname,
                            'temp_mp_lname' => $result->mp_lname,
                            'temp_mp_img' => isset($result->mp_img) ? $result->mp_img : null,
                            'temp_mp_phone' => isset($result->mp_phone) ? $result->mp_phone : null,
                            'temp_mp_number' => isset($result->mp_number) ? $result->mp_number : null,
                            'temp_mp_address' => isset($result->mp_address) ? $result->mp_address : null,
                            'temp_google2fa_secret' => $result->google2fa_secret,
                            'temp_login_time' => time(),
                            'temp_user_type' => 'public',
                            'requires_2fa' => true
                        );
                        $this->session->set_userdata($temp_data);

                        if ($this->input->is_ajax_request()) {
                            echo json_encode([
                                'status' => 'requires_2fa',
                                'message' => 'ต้องการยืนยันตัวตน 2FA',
                                'show_google_auth' => true,
                                'requires_verification' => true,
                                'temp_user_type' => 'public' // *** เพิ่ม: ส่งประเภทผู้ใช้ไปยัง JavaScript ***
                            ]);
                            return;
                        }

                        // สำหรับ non-AJAX request
                        $data['requires_2fa'] = true;
                        $data['temp_user_type'] = 'public';
                        $this->load->view('frontend_templat/header');
                        $this->load->view('frontend_asset/css');
                        $this->load->view('frontend_templat/navbar_other');
                        $this->load->view('frontend/form_login', $data);
                        $this->load->view('frontend_asset/js');
                        $this->load->view('frontend_templat/footer_other');
                        return;
                    }
                } else {
                    // ไม่มี 2FA - เข้าสู่ระบบปกติ
                    // === เพิ่ม: รีเซ็ต failed attempts เมื่อ login สำเร็จ ===
                    $this->reset_failed_attempts($fingerprint);
                    $this->record_login_attempt($result->mp_email, 'success', $fingerprint);

                    $this->create_public_session($result, false);
                }

                // บันทึก log การ login
                $log_data = array(
                    'user_id' => $result->mp_id,
                    'user_type' => 'Public',
                    'action' => 'login',
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent()
                );
                $this->tax_user_log_model->insert_log($log_data);

                // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                $redirect_url = $this->session->userdata('redirect_after_login');

                if (!$redirect_url) {
                    $redirect_url = site_url('Pages/service_systems');
                } else {
                    // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                    $this->session->unset_userdata('redirect_after_login');
                }

                $this->generate_sso_token();
                $this->session->set_flashdata('login_success', TRUE);

                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'เข้าสู่ระบบสำเร็จ',
                        'redirect' => $redirect_url
                    ]);
                    return;
                }

                redirect($redirect_url);
            } else {
                // === เพิ่ม: จัดการกรณี Login ล้มเหลว ===
                $username = $this->input->post('mp_email');
                $password = $this->input->post('mp_password');

                // บันทึกความพยายามเข้าสู่ระบบที่ล้มเหลว
                $this->record_login_attempt($username, 'failed', $fingerprint);

                // จัดการการนับจำนวนครั้งที่ล้มเหลวเหมือน Staff
                // (โค้ดจัดการ block เหมือนใน Staff controller)

                // บันทึก log กิจกรรม
                $this->user_log_model->log_detect(
                    $username,
                    $password,
                    'public',
                    'failed',
                    'Public user logged in failed',
                    'auth'
                );

                $error_message = 'รหัสผ่านหรือชื่อผู้ใช้งานไม่ถูกต้อง';

                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $error_message
                    ]);
                    return;
                }

                echo "<script>";
                echo "alert('" . $error_message . "');";
                echo "window.history.back();";
                echo "</script>";
            }
        } catch (Exception $e) {
            error_log("Exception in citizen check_login: " . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในระบบ']);
                return;
            }

            echo "<script>";
            echo "alert('เกิดข้อผิดพลาดในระบบ');";
            echo "window.history.back();";
            echo "</script>";
        }
    }

    public function update_id_card()
    {
        try {
            // ล้าง output buffer และตั้งค่า header
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            ini_set('display_errors', 0);

            // ตรวจสอบ request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบการ login
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!$mp_id || !$mp_email) {
                echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // รับข้อมูลจาก POST
            $mp_number = $this->input->post('mp_number');

            // Validation
            if (empty($mp_number)) {
                echo json_encode(['success' => false, 'message' => 'กรุณากรอกเลขบัตรประจำตัวประชาชน'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (!preg_match('/^\d{13}$/', $mp_number)) {
                echo json_encode(['success' => false, 'message' => 'เลขบัตรประจำตัวประชาชนต้องเป็นตัวเลข 13 หลัก'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบว่าเลขบัตรนี้มีคนใช้แล้วหรือไม่
            $this->db->select('id, mp_email');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_number', $mp_number);
            $this->db->where('mp_id !=', $mp_id); // ยกเว้นตัวเอง
            $existing_user = $this->db->get()->row();

            if ($existing_user) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เลขบัตรประจำตัวประชาชนนี้มีผู้ใช้งานแล้ว'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // อัพเดทข้อมูล
            $update_data = [
                'mp_number' => $mp_number,
                'mp_updated_by' => $mp_email,
                'mp_updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->where('mp_id', $mp_id);
            $result = $this->db->update('tbl_member_public', $update_data);

            if ($result) {
                // อัพเดท session (ถ้าจำเป็น)
                $this->session->set_userdata('mp_number', $mp_number);

                // Log การอัพเดท
                log_message('info', "ID Card updated for user {$mp_email}: {$mp_number}");

                echo json_encode([
                    'success' => true,
                    'message' => 'อัพเดทเลขบัตรประจำตัวประชาชนสำเร็จ',
                    'data' => [
                        'mp_number' => $mp_number
                    ]
                ], JSON_UNESCAPED_UNICODE);

            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัพเดทข้อมูลได้ กรุณาลองใหม่อีกครั้ง'
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', 'Update ID Card Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }



    // public function keep_alive()
    // {
    //     ob_start();

    //     if (!$this->input->is_ajax_request()) {
    //         ob_end_clean();
    //         show_404();
    //         exit;
    //     }

    //     header('Content-Type: application/json; charset=utf-8');
    //     header('Cache-Control: no-cache, no-store, must-revalidate');
    //     header('Pragma: no-cache');
    //     header('Expires: 0');
    //     header('X-Content-Type-Options: nosniff');

    //     try {
    //         // ⭐ ขั้นตอนที่ 1: ตรวจสอบ session และ update activity ทันที
    //         $user_id = $this->session->userdata('mp_id');
    //         $is_public = $this->session->userdata('is_public');

    //         if (!$user_id || !$is_public) {
    //             ob_end_clean();
    //             echo json_encode([
    //                 'status' => 'expired',
    //                 'message' => 'No valid session found',
    //                 'redirect_url' => base_url('User'),
    //                 'timestamp' => time() * 1000
    //             ]);
    //             exit;
    //         }

    //         // ⭐ ขั้นตอนที่ 2: อัปเดต activity time ทันทีที่ได้รับ keep alive request
    //         $current_time = time();
    //         $this->session->set_userdata('last_activity_time', $current_time);

    //         // ⭐ Log การอัปเดต (สำหรับ debug)
    //         if (ENVIRONMENT === 'development') {
    //             error_log("✅ Keep alive: Updated activity time for user $user_id at $current_time");
    //         }

    //         // ⭐ ขั้นตอนที่ 3: รับข้อมูลจาก client
    //         $input = json_decode($this->input->raw_input_stream, true);

    //         $time_since_activity = $input['time_since_activity'] ?? 0;
    //         $max_idle_time = $input['max_idle_time'] ?? (32400 * 60 * 1000); // default 9 ชั่วโมง

    //         // ⭐ ขั้นตอนที่ 4: ตรวจสอบ client-side timeout เท่านั้น
    //         // (ไม่ต้องตรวจสอบ server-side เพราะเพิ่งอัปเดตแล้ว)

    //         if ($time_since_activity > $max_idle_time) {
    //             // User idle เกินเวลาที่กำหนดจาก client
    //             $this->session->sess_destroy();

    //             ob_end_clean();
    //             echo json_encode([
    //                 'status' => 'idle_timeout',
    //                 'message' => 'User inactivity timeout',
    //                 'idle_time' => $time_since_activity,
    //                 'max_idle_time' => $max_idle_time,
    //                 'redirect_url' => base_url('User'),
    //                 'timestamp' => $current_time * 1000
    //             ]);
    //             exit;
    //         }

    //         // ⭐ ขั้นตอนที่ 5: ส่ง response ว่า session ยัง alive
    //         $warning_time = $max_idle_time * 0.8; // แจ้งเตือนที่ 80% ของเวลา
    //         $should_warn = $time_since_activity > $warning_time;

    //         ob_end_clean();
    //         echo json_encode([
    //             'status' => 'alive',
    //             'message' => 'Session is active',
    //             'warning' => $should_warn,
    //             'server_time' => $current_time * 1000,
    //             'time_since_activity' => $time_since_activity,
    //             'remaining_time' => max(0, $max_idle_time - $time_since_activity),
    //             'warning_time' => $warning_time,
    //             // ⭐ เพิ่มข้อมูล debug แบบสะอาด
    //             'debug' => ENVIRONMENT === 'development' ? [
    //                 'activity_updated' => true,
    //                 'server_time' => $current_time,
    //                 'user_id_exists' => !empty($user_id),
    //                 'is_public_session' => $is_public
    //             ] : null
    //         ]);

    //     } catch (Exception $e) {
    //         error_log("Keep Alive Error: " . $e->getMessage());

    //         ob_end_clean();
    //         echo json_encode([
    //             'status' => 'error',
    //             'message' => 'Server error occurred',
    //             'timestamp' => time() * 1000
    //         ]);
    //     }

    //     exit;
    // }



    /**
     * Update User Activity สำหรับประชาชน
     */
    public function update_user_activity()
    {
        ob_start();

        if (!$this->input->is_ajax_request()) {
            ob_end_clean();
            show_404();
            exit;
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $user_id = $this->session->userdata('mp_id');

            if (!$user_id) {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'No session found',
                    'timestamp' => time() * 1000
                ]);
                exit;
            }

            // อัปเดต activity time
            $current_time = time();
            $this->session->set_userdata('last_activity_time', $current_time);

            ob_end_clean();
            echo json_encode([
                'success' => true,
                'message' => 'User activity updated',
                'timestamp' => $current_time * 1000
            ]);

        } catch (Exception $e) {
            error_log("Update User Activity Error: " . $e->getMessage());

            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Error updating activity',
                'timestamp' => time() * 1000
            ]);
        }

        exit;
    }




    /**
     * Verify Session สำหรับประชาชน (สำหรับ Cross-Tab Sync)
     */
    public function verify_session()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            exit;
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $user_id = $this->session->userdata('mp_id');
            $is_public = $this->session->userdata('is_public');

            $is_valid = false;
            if ($user_id && $is_public) {
                // ตรวจสอบเพิ่มเติมจากฐานข้อมูล
                $user_exists = $this->member_public_model->get_member_by_id($user_id);
                $is_valid = ($user_exists && $user_exists->mp_status == 1);
            }

            echo json_encode([
                'valid' => $is_valid,
                'user_type' => 'public',
                'timestamp' => time() * 1000
            ]);

        } catch (Exception $e) {
            error_log("Verify Session Error (Public): " . $e->getMessage());
            echo json_encode([
                'valid' => false,
                'error' => 'Session verification failed',
                'timestamp' => time() * 1000
            ]);
        }

        exit;
    }

    /**
     * Test JSON Response สำหรับประชาชน
     */
    public function test_json()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            exit;
        }

        ob_start();
        header('Content-Type: application/json; charset=utf-8');

        ob_end_clean();
        echo json_encode([
            'status' => 'ok',
            'message' => 'JSON test successful (Public)',
            'timestamp' => time() * 1000,
            'user_id' => $this->session->userdata('mp_id'),
            'user_type' => 'public',
            'is_public' => $this->session->userdata('is_public')
        ]);
        exit;
    }

    /**
     * ลบ trusted devices เก่าของผู้ใช้คนนี้
     */
    private function cleanup_user_trusted_devices($user_id, $tenant_id, $user_type = 'public', $keep_latest = 3)
    {
        try {
            // ลบ devices ที่หมดอายุ
            $this->db->where('user_id', $user_id)
                ->where('user_type', $user_type)
                ->where('tenant_id', $tenant_id)
                ->where('expires_at <', date('Y-m-d H:i:s'))
                ->delete('trusted_devices');

            // เก็บไว้แค่ 3 devices ล่าสุด
            $devices = $this->db->select('id')
                ->where('user_id', $user_id)
                ->where('user_type', $user_type)
                ->where('tenant_id', $tenant_id)
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->order_by('created_at', 'DESC')
                ->get('trusted_devices')
                ->result();

            if (count($devices) >= $keep_latest) {
                $devices_to_delete = array_slice($devices, $keep_latest - 1);
                foreach ($devices_to_delete as $device) {
                    $this->db->where('id', $device->id)->delete('trusted_devices');
                }
            }

            error_log("Cleaned up old trusted devices for user: $user_id");

        } catch (Exception $e) {
            error_log("Error in cleanup_user_trusted_devices: " . $e->getMessage());
        }
    }

    /**
     * สร้าง session สำหรับประชาชนแบบครบถ้วน (เฉพาะสำหรับ Auth_public_mem)
     */
    private function create_complete_public_session_from_data($user_data, $is_2fa_verified = false, $trusted_device = false, $tenant_id = null)
    {
        try {
            error_log("Creating complete public session for user: " . $user_data->mp_id);

            // ดึง tenant data ที่มีอยู่หรือดึงใหม่
            if (!$tenant_id) {
                $tenant_id = $this->session->userdata('tenant_id') ?: 1;
            }

            $tenant_code = $this->session->userdata('tenant_code') ?: 'default';
            $tenant_name = $this->session->userdata('tenant_name') ?: 'Default Organization';
            $tenant_domain = $this->session->userdata('tenant_domain') ?: $_SERVER['HTTP_HOST'];

            // ถ้าไม่มีข้อมูล tenant ให้ดึงจากฐานข้อมูล
            if ($tenant_code === 'default' || $tenant_name === 'Default Organization') {
                $tenant = $this->tenant_access_model->get_tenant_by_id($tenant_id);
                if ($tenant) {
                    $tenant_code = $tenant->code;
                    $tenant_name = $tenant->name;
                    $tenant_domain = $tenant->domain;
                }
            }

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
            error_log("Session verification: mp_id=" . $this->session->userdata('mp_id') .
                ", 2fa_verified=" . ($this->session->userdata('2fa_verified') ? 'true' : 'false') .
                ", tenant_id=" . $this->session->userdata('tenant_id') .
                ", is_public=" . ($this->session->userdata('is_public') ? 'true' : 'false'));

            return true;

        } catch (Exception $e) {
            error_log("Error creating complete public session from data: " . $e->getMessage());
            throw $e;
        }
    }


    /**
     * เข้าสู่ระบบแบบธรรมดา (ไม่ใช่ API)
     */
    public function login()
    {
        // Check if the username or password fields are empty
        if ($this->input->post('mp_email') == '' || $this->input->post('mp_password') == '') {
            echo "<script>";
            echo "alert('กรุณากรอกข้อมูลชื่อผู้ใช้และรหัสผ่าน');";
            echo "window.history.back();";
            echo "</script>";
        } else {
            $result = $this->member_public_model->fetch_user_login(
                $this->input->post('mp_email'),
                sha1($this->input->post('mp_password'))
            );

            if (!empty($result)) {
                // ตรวจสอบสถานะผู้ใช้
                if (isset($result->mp_status) && $result->mp_status == 0) {
                    echo "<script>";
                    echo "alert('บัญชีนี้ถูกระงับการใช้งาน โปรดติดต่อผู้ให้บริการ');";
                    echo "window.history.back();";
                    echo "</script>";
                    return;
                }

                // **ตรวจสอบ 2FA**
                if (!empty($result->google2fa_secret) && $result->google2fa_enabled == 1) {
                    // ตรวจสอบ Trusted Device
                    $tenant_id = $this->session->userdata('tenant_id') ?: 1;

                    if ($this->is_trusted_device($result->mp_id, $tenant_id, 'public')) {
                        error_log("Trusted device found for public user: " . $result->mp_email . " - Skipping 2FA");

                        // อัพเดทการใช้งานล่าสุด
                        $this->update_trusted_device_usage($result->mp_id);

                        // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                        $redirect_url = $this->session->userdata('redirect_after_login');

                        if (!$redirect_url) {
                            $redirect_url = 'Pages/service_systems';
                        } else {
                            // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                            $this->session->unset_userdata('redirect_after_login');
                        }

                        // สร้าง session ปกติ (Skip 2FA)
                        $this->create_public_session($result, true, true);

                        // บันทึก log การ login
                        $log_data = array(
                            'user_id' => $result->mp_id,
                            'user_type' => 'Public',
                            'action' => 'login',
                            'ip_address' => $this->input->ip_address(),
                            'user_agent' => $this->input->user_agent()
                        );
                        $this->tax_user_log_model->insert_log($log_data);

                        $this->generate_sso_token();
                        $this->session->set_flashdata('login_success', TRUE);

                        // ทำการ redirect
                        redirect(base_url($redirect_url));
                        return;

                    } else {
                        // ต้องใช้ 2FA - redirect ไปหน้า 2FA
                        $temp_data = array(
                            'temp_mp_id' => $result->mp_id,
                            'temp_mp_email' => $result->mp_email,
                            'temp_mp_fname' => $result->mp_fname,
                            'temp_mp_lname' => $result->mp_lname,
                            'temp_mp_img' => isset($result->mp_img) ? $result->mp_img : null,
                            'temp_mp_phone' => isset($result->mp_phone) ? $result->mp_phone : null,
                            'temp_mp_number' => isset($result->mp_number) ? $result->mp_number : null,
                            'temp_mp_address' => isset($result->mp_address) ? $result->mp_address : null,
                            'temp_google2fa_secret' => $result->google2fa_secret,
                            'temp_login_time' => time(),
                            'temp_user_type' => 'public',
                            'requires_2fa' => true
                        );
                        $this->session->set_userdata($temp_data);

                        // Redirect ไปหน้า 2FA verification
                        $data['requires_2fa'] = true;
                        $data['temp_user_type'] = 'public'; // *** เพิ่ม: ส่งไปยัง View ***
                        $this->load->view('frontend_templat/header');
                        $this->load->view('frontend_asset/css');
                        $this->load->view('frontend_templat/navbar_other');
                        $this->load->view('frontend/form_login', $data);
                        $this->load->view('frontend_asset/js');
                        $this->load->view('frontend_templat/footer_other');
                        return;
                    }
                } else {
                    // ไม่มี 2FA - เข้าสู่ระบบปกติ
                    $this->create_public_session($result, false);
                }

                // บันทึก log การ login
                $log_data = array(
                    'user_id' => $result->mp_id,
                    'user_type' => 'Public',
                    'action' => 'login',
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent()
                );
                $this->tax_user_log_model->insert_log($log_data);

                // *** แก้ไข: ตรวจสอบ redirect URL จาก session ***
                $redirect_url = $this->session->userdata('redirect_after_login');

                if (!$redirect_url) {
                    $redirect_url = 'Pages/service_systems';
                } else {
                    // ลบ redirect URL ออกจาก session หลังจากใช้แล้ว
                    $this->session->unset_userdata('redirect_after_login');
                }

                $this->generate_sso_token();
                $this->session->set_flashdata('login_success', TRUE);

                // ทำการ redirect
                redirect(base_url($redirect_url));
            } else {
                echo "<script>";
                echo "alert('รหัสผ่านหรือชื่อผู้ใช้งานไม่ถูกต้อง');";
                echo "window.history.back();";
                echo "</script>";
            }
        }
    }

    /**
     * หน้าจัดการโปรไฟล์สำหรับประชาชน
     */
    public function profile()
    {
        try {
            // ตรวจสอบการเข้าสู่ระบบ
            if (!$this->session->userdata('mp_id')) {
                if ($this->input->is_ajax_request()) {
                    $response = [
                        'success' => false,
                        'message' => 'กรุณาเข้าสู่ระบบก่อน'
                    ];
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
                    return;
                }
                redirect('User');
                return;
            }

            // **สำคัญ: ตรวจสอบว่าผ่าน 2FA แล้วหรือยัง**
            $user_id = $this->session->userdata('mp_id');
            $user_2fa_info = $this->member_public_model->get_2fa_info($user_id);

            // ถ้าผู้ใช้เปิด 2FA แต่ยังไม่ได้ verify
            if ($user_2fa_info && !empty($user_2fa_info->google2fa_secret) && $user_2fa_info->google2fa_enabled == 1) {
                if (!$this->session->userdata('2fa_verified')) {
                    error_log("Public user " . $this->session->userdata('mp_email') . " tried to bypass 2FA");

                    if ($this->input->is_ajax_request()) {
                        $response = [
                            'success' => false,
                            'message' => 'กรุณายืนยันตัวตนผ่าน 2FA ก่อนเข้าใช้งาน'
                        ];
                        $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
                        return;
                    }

                    // ลบ session และกลับไปหน้า login
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

                    $this->session->set_flashdata('error', 'กรุณายืนยันตัวตนผ่าน 2FA ก่อนเข้าใช้งาน');
                    redirect('User');
                    return;
                }
            }

            $mp_id = $this->session->userdata('mp_id');

            // *** จัดการ AJAX Requests สำหรับการอัพเดทข้อมูล ***
            if ($this->input->is_ajax_request() && $this->input->post()) {
                $response = $this->handle_profile_update_ajax($mp_id);

                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            // *** จัดการ POST ปกติ (Non-AJAX) ***
            if ($this->input->post()) {
                $this->handle_profile_update($mp_id);
            }

            // ดึงข้อมูลผู้ใช้สำหรับแสดงผล
            $data['user_data'] = $this->member_public_model->get_member_by_id($mp_id);
            $data['user_2fa_info'] = $user_2fa_info;

            $this->load->view('asset/css');
            $this->load->view('public_user/public_mem_profile', $data);

        } catch (Exception $e) {
            error_log('Error in profile method: ' . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                $response = [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
                ];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
            } else {
                show_error('เกิดข้อผิดพลาดในระบบโปรไฟล์');
            }
        }
    }




    /**
     * แทนที่ method delete_account() เดิมใน Auth_public_mem.php
     */
    public function delete_account()
    {
        try {
            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            // ตรวจสอบการเข้าสู่ระบบ
            if (!$this->session->userdata('mp_id')) {
                $response = [
                    'status' => 'error',
                    'message' => 'กรุณาเข้าสู่ระบบก่อน'
                ];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            $mp_id = $this->session->userdata('mp_id');
            $action = $this->input->post('action');

            if ($action !== 'delete_account') {
                $response = [
                    'status' => 'error',
                    'message' => 'Invalid action'
                ];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            // ⭐ ใช้ method ใหม่: ตรวจสอบว่าสามารถลบได้หรือไม่
            $can_delete = $this->member_public_model->can_delete_account($mp_id);

            if (!$can_delete['can_delete']) {
                $response = [
                    'status' => 'error',
                    'message' => $can_delete['reason']
                ];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            $user_data = $can_delete['user_data'];

            // ตรวจสอบ 2FA ถ้าจำเป็น
            $user_2fa_info = $this->member_public_model->get_2fa_info($mp_id);
            $requires_2fa = false;
            $verified_2fa = false;

            if ($user_2fa_info && !empty($user_2fa_info->google2fa_secret) && $user_2fa_info->google2fa_enabled == 1) {
                $requires_2fa = true;
                $otp = $this->input->post('otp');

                if (empty($otp) || strlen($otp) !== 6) {
                    $response = [
                        'status' => 'error',
                        'message' => 'กรุณากรอกรหัส OTP 6 หลัก',
                        'error_type' => 'missing_otp'
                    ];
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
                    return;
                }

                // ตรวจสอบ OTP
                if (!isset($this->google2fa)) {
                    $this->load->library('Google2FA');
                }

                if (!$this->google2fa->verifyKey($user_2fa_info->google2fa_secret, $otp)) {
                    $response = [
                        'status' => 'error',
                        'message' => 'รหัส OTP ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
                        'error_type' => 'invalid_otp'
                    ];
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
                    return;
                }

                $verified_2fa = true;
            }

            // เตรียมข้อมูลสำหรับบันทึก log การลบ
            $deletion_reason = $this->input->post('deletion_reason') ?: 'ไม่ระบุ';

            $deletion_log_data = [
                'deletion_reason' => $deletion_reason,
                'deleted_by_self' => 1,
                'required_2fa' => $requires_2fa ? 1 : 0,
                'verified_2fa' => $verified_2fa ? 1 : 0,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent()
            ];

            try {
                // ลบข้อมูลที่เกี่ยวข้องก่อน (trusted devices, auth tokens, etc.)
                $this->cleanup_user_related_data($mp_id);

                // ลบรูปโปรไฟล์ (ถ้ามี)
                $this->delete_user_profile_image($user_data);

                // ⭐ ใช้ method ใหม่: ลบบัญชีพร้อม log
                $deletion_success = $this->member_public_model->delete_account_with_log($mp_id, $deletion_log_data);

                if ($deletion_success) {
                    // บันทึก log กิจกรรม
                    if (isset($this->user_log_model)) {
                        $this->user_log_model->log_activity(
                            $user_data->mp_email,
                            'account_deleted',
                            'ผู้ใช้ลบบัญชีด้วยตนเอง',
                            'account_management'
                        );
                    }

                    // ส่ง security alert
                    $this->send_deletion_alert($user_data, $deletion_reason, $verified_2fa);

                    // ล้าง session
                    $this->session->sess_destroy();

                    $response = [
                        'status' => 'success',
                        'message' => 'ลบบัญชีสำเร็จ',
                        'redirect' => site_url('Home')
                    ];

                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'เกิดข้อผิดพลาดในการลบบัญชี'
                    ];
                }

                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));

            } catch (Exception $e) {
                error_log('Error deleting account: ' . $e->getMessage());

                $response = [
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการลบบัญชี: ' . $e->getMessage()
                ];

                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
            }

        } catch (Exception $e) {
            error_log('Fatal error in delete_account: ' . $e->getMessage());

            $response = [
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดร้ายแรงในระบบ'
            ];

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    /**
     * ⭐ เพิ่ม: ลบข้อมูลที่เกี่ยวข้องทั้งหมด
     */
    private function cleanup_user_related_data($mp_id)
    {
        try {
            // ลบ trusted devices
            if ($this->db->table_exists('trusted_devices')) {
                $this->db->where('user_id', $mp_id)
                    ->where('user_type', 'public')
                    ->delete('trusted_devices');
            }

            // ลบ auth tokens
            if ($this->db->table_exists('auth_tokens')) {
                $this->db->where('user_id', $mp_id)
                    ->delete('auth_tokens');
            }

            // ลบ login attempts
            if ($this->db->table_exists('tbl_member_login_attempts')) {
                // ดึงอีเมลก่อน
                $email = $this->session->userdata('mp_email');
                if ($email) {
                    $this->db->where('username', $email)
                        ->delete('tbl_member_login_attempts');
                }
            }

            // ลบ user logs ที่เกี่ยวข้อง (ถ้าต้องการ)
            if ($this->db->table_exists('tbl_user_logs')) {
                $this->db->where('user_id', $mp_id)
                    ->where('user_type', 'public')
                    ->delete('tbl_user_logs');
            }

            log_message('info', "Cleaned up related data for user: $mp_id");

        } catch (Exception $e) {
            error_log("Error cleaning up user data: " . $e->getMessage());
        }
    }

    /**
     * ⭐ เพิ่ม: ลบรูปโปรไฟล์
     */
    private function delete_user_profile_image($user_data)
    {
        try {
            if (!empty($user_data->mp_img)) {
                $image_paths = [
                    './docs/img/avatar/' . $user_data->mp_img,
                    './uploads/' . $user_data->mp_img,
                    'docs/img/' . $user_data->mp_img
                ];

                foreach ($image_paths as $path) {
                    if (file_exists($path)) {
                        @unlink($path);
                        log_message('info', "Deleted profile image: $path");
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error deleting profile image: " . $e->getMessage());
        }
    }

    /**
     * ⭐ เพิ่ม: ส่งแจ้งเตือนการลบบัญชี
     */
    private function send_deletion_alert($user_data, $deletion_reason, $verified_2fa)
    {
        try {
            if (isset($this->user_log_model) && method_exists($this->user_log_model, 'send_line_alert')) {
                $message = "🗑️ แจ้งเตือนการลบบัญชีผู้ใช้ 🗑️\n\n";
                $message .= "👤 ผู้ใช้: " . $user_data->mp_email . "\n";
                $message .= "📱 ประเภท: ประชาชน\n";
                $message .= "🔐 ใช้ 2FA: " . ($verified_2fa ? 'ใช่' : 'ไม่') . "\n";
                $message .= "📝 เหตุผล: " . $deletion_reason . "\n";
                $message .= "🌐 IP Address: " . $this->input->ip_address() . "\n";
                $message .= "⏰ เวลา: " . date('Y-m-d H:i:s') . "\n";
                $message .= "✅ สถานะ: ลบบัญชีสำเร็จ";

                $this->user_log_model->send_line_alert($message);
            }
        } catch (Exception $e) {
            error_log("Error sending deletion alert: " . $e->getMessage());
        }
    }





    private function handle_public_login($fingerprint)
    {
        $email = $this->input->post('mp_email');
        $password = $this->input->post('mp_password');

        // ตรวจสอบการถูกบล็อค (สำหรับประชาชน)
        $block_status = $this->check_if_blocked($fingerprint);
        if ($block_status['is_blocked']) {
            // ... block handling code ...
        }

        try {
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
                    // **ตรวจสอบ 2FA**
                    if (!empty($result->google2fa_secret) && $result->google2fa_enabled == 1) {

                        // *** แก้ไข: เพิ่ม tenant_id และ user_type ***
                        $tenant_id = $this->session->userdata('tenant_id') ?: 1;

                        if ($this->is_trusted_device($result->mp_id, $tenant_id, 'public')) {
                            error_log("Trusted device found for public user: " . $result->mp_email . " - Skipping 2FA");

                            // อัพเดทการใช้งานล่าสุด
                            $this->update_trusted_device_usage($result->mp_id, $tenant_id, 'public');

                            // รีเซ็ต failed attempts เมื่อ login สำเร็จ
                            $this->reset_failed_attempts($fingerprint);
                            $this->record_login_attempt($email, 'success', $fingerprint);

                            // สร้าง session ปกติ (Skip 2FA)
                            $this->create_public_session($result, true, true);

                            $response = [
                                'status' => 'success',
                                'message' => 'เข้าสู่ระบบสำเร็จ (Trusted Device)',
                                'redirect' => site_url('Pages/service_systems'),
                                'trusted_device' => true,
                                'user_data' => [
                                    'mp_id' => $result->mp_id,
                                    'mp_email' => $result->mp_email,
                                    'mp_fname' => $result->mp_fname,
                                    'mp_lname' => $result->mp_lname
                                ]
                            ];

                            log_message('debug', 'Public user login successful (trusted device): ' . $email);
                        } else {
                            // ต้องใช้ 2FA - ลบ session หลักทิ้งก่อน
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

                            // รีเซ็ต failed attempts เมื่อผ่านการตรวจสอบ username/password
                            $this->reset_failed_attempts($fingerprint);
                            $this->record_login_attempt($email, 'success', $fingerprint);

                            // เก็บข้อมูลชั่วคราวสำหรับ 2FA
                            $temp_data = array(
                                'temp_mp_id' => $result->mp_id,
                                'temp_mp_email' => $result->mp_email,
                                'temp_mp_fname' => $result->mp_fname,
                                'temp_mp_lname' => $result->mp_lname,
                                'temp_mp_img' => isset($result->mp_img) ? $result->mp_img : null,
                                'temp_mp_phone' => isset($result->mp_phone) ? $result->mp_phone : null,
                                'temp_mp_number' => isset($result->mp_number) ? $result->mp_number : null,
                                'temp_mp_address' => isset($result->mp_address) ? $result->mp_address : null,
                                'temp_tenant_id' => $tenant_id, // *** เพิ่ม tenant_id ***
                                'temp_google2fa_secret' => $result->google2fa_secret,
                                'temp_login_time' => time(),
                                'temp_user_type' => 'public',
                                'requires_2fa' => true
                            );
                            $this->session->set_userdata($temp_data);

                            error_log("2FA Required for public user: " . $result->mp_email);

                            $response = [
                                'status' => 'requires_2fa',
                                'message' => 'ต้องการยืนยันตัวตน 2FA',
                                'show_google_auth' => true,
                                'requires_verification' => true,
                                'user_type' => 'public',
                                'temp_user_type' => 'public'
                            ];
                        }
                    } else {
                        // ไม่มี 2FA - เข้าสู่ระบบปกติ
                        $this->create_public_session($result, false);

                        // รีเซ็ตการนับจำนวนครั้งล็อกอินที่ล้มเหลว
                        $this->reset_failed_attempts($fingerprint);
                        $this->record_login_attempt($email, 'success', $fingerprint);

                        $response = [
                            'status' => 'success',
                            'message' => 'เข้าสู่ระบบสำเร็จ',
                            'redirect' => site_url('Pages/service_systems'),
                            'user_data' => [
                                'mp_id' => $result->mp_id,
                                'mp_email' => $result->mp_email,
                                'mp_fname' => $result->mp_fname,
                                'mp_lname' => $result->mp_lname
                            ]
                        ];

                        log_message('debug', 'Public user login successful: ' . $email);
                    }
                }
            } else {
                // การเข้าสู่ระบบล้มเหลว
                $this->handle_login_failure($email, $password, $fingerprint);
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







    /**
     * หน้าจัดการ 2FA สำหรับประชาชน
     */
    public function setup_2fa()
    {
        try {
            // ตรวจสอบการเข้าสู่ระบบ
            if (!$this->session->userdata('mp_id')) {
                if ($this->input->is_ajax_request()) {
                    $response = [
                        'status' => 'error',
                        'message' => 'กรุณาเข้าสู่ระบบก่อน'
                    ];
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
                    return;
                }
                redirect('User');
                return;
            }

            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            // ตรวจสอบและโหลด Google2FA library
            if (!$this->load->library('Google2FA')) {
                log_message('error', 'Cannot load Google2FA library');

                if ($this->input->is_ajax_request()) {
                    $response = [
                        'status' => 'error',
                        'message' => 'ระบบ 2FA ไม่พร้อมใช้งาน กรุณาติดต่อผู้ดูแลระบบ'
                    ];
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode($response));
                    return;
                }
            }

            // ตรวจสอบสถานะ 2FA ปัจจุบัน
            $user_2fa_info = null;
            try {
                $user_2fa_info = $this->member_public_model->get_2fa_info($mp_id);
            } catch (Exception $e) {
                log_message('error', 'Error getting 2FA info: ' . $e->getMessage());
            }

            // *** จัดการ AJAX Requests ***
            if ($this->input->is_ajax_request()) {
                $action = $this->input->post('action');
                $response = ['status' => 'error', 'message' => 'ไม่พบ action ที่ระบุ'];

                try {
                    switch ($action) {
                        case 'enable_2fa':
                            $response = $this->_handle_enable_2fa($mp_email);
                            break;

                        case 'verify_setup':
                            $response = $this->_handle_verify_setup($mp_id);
                            break;

                        case 'disable_2fa':
                            $response = $this->_handle_disable_2fa($mp_id, $user_2fa_info);
                            break;

                        default:
                            $response = [
                                'status' => 'error',
                                'message' => 'ไม่พบ action ที่ระบุ: ' . $action
                            ];
                    }

                } catch (Exception $e) {
                    log_message('error', 'Error in setup_2fa action ' . $action . ': ' . $e->getMessage());
                    $response = [
                        'status' => 'error',
                        'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
                    ];
                }

                // ส่ง JSON response
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            // *** จัดการ Non-AJAX Requests ***
            $data = [];

            // โหลดข้อมูลสำหรับ view
            try {
                $data['user_2fa_info'] = $user_2fa_info;
                $data['user_data'] = $this->member_public_model->get_member_by_id($mp_id);
            } catch (Exception $e) {
                log_message('error', 'Error loading user data: ' . $e->getMessage());
                $data['error'] = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
            }

            $this->load->view('asset/css');
            $this->load->view('public/setup_2fa', $data);
            $this->load->view('asset/js');

        } catch (Exception $e) {
            log_message('error', 'Fatal error in setup_2fa: ' . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                $response = [
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดร้ายแรงในระบบ'
                ];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($response));
            } else {
                show_error('เกิดข้อผิดพลาดในระบบ 2FA');
            }
        }
    }




    private function _handle_enable_2fa($mp_email)
    {
        try {
            if (!isset($this->google2fa)) {
                throw new Exception('Google2FA library not available');
            }

            $secret = $this->google2fa->generateSecretKey();

            // Debug log
            error_log("2FA Generate Debug - Secret created for $mp_email: " . substr($secret, 0, 8) . "... (length: " . strlen($secret) . ")");

            // *** ใช้วิธีเดียวกับ System_admin แต่เก็บรูปแบบที่ต้องการ ***
            $current_domain = $_SERVER['HTTP_HOST'];
            $clean_domain = str_replace(['www.', 'http://', 'https://'], '', $current_domain);
            $clean_domain = strtok($clean_domain, '/');

            // ใช้รูปแบบที่ต้องการ: "ยืนยันตัวตนประชาชน : example.com : user@email.com"
            $issuer = 'ยืนยันตัวตนประชาชน';
            $account_name = $clean_domain . ' : ' . $mp_email; // เพิ่มช่องว่างรอบ :

            // *** ใช้ sprintf() และ rawurlencode() เพื่อหลีกเลี่ยงเครื่องหมาย + ***
            $otpauth_url = sprintf(
                'otpauth://totp/%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
                rawurlencode($issuer . ' : ' . $account_name), // ใช้ rawurlencode แทน urlencode
                $secret,
                rawurlencode($issuer) // ใช้ rawurlencode แทน urlencode
            );

            // สร้าง QR Code URL โดยตรง (ไม่ใช้ library)
            $qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=M&data=' . rawurlencode($otpauth_url);

            error_log("2FA Generate Debug - QR Code generated successfully for $mp_email");
            error_log("2FA Generate Debug - Issuer: $issuer");
            error_log("2FA Generate Debug - Account: $account_name");
            error_log("2FA Generate Debug - Full Display: " . $issuer . ' : ' . $account_name);
            error_log("2FA Generate Debug - OTPAuth URL: $otpauth_url");

            return [
                'status' => 'success',
                'secret' => $secret,
                'qr_code_url' => $qr_code_url,
                'google_chart_qr' => $qr_code_url, // ใช้ URL เดียวกัน
                'manual_entry_key' => $secret,
                'manual_entry_url' => $otpauth_url,
                'issuer_name' => $issuer,
                'account_name' => $account_name,
                'domain' => $clean_domain,
                'action' => 'setup',

                // ข้อมูลสำหรับแสดงใน UI
                'display_name_thai' => 'ยืนยันตัวตนประชาชน',
                'display_name_english' => 'Citizen Identity Verification',
                'app_display_info' => [
                    'issuer' => $issuer,
                    'account' => $account_name,
                    'full_display' => $issuer . ' : ' . $account_name,
                    'note' => 'ชื่อในแอป: ' . $issuer,
                    'format' => 'ยืนยันตัวตนประชาชน : example.com : user@email.com'
                ],

                // Debug information
                'debug_info' => [
                    'secret_length' => strlen($secret),
                    'email' => $mp_email,
                    'domain' => $clean_domain,
                    'timestamp' => time(),
                    'method' => 'system_admin_style_with_spaces',
                    'encoding' => 'urlencode',
                    'format' => $issuer . ' : ' . $account_name
                ]
            ];

        } catch (Exception $e) {
            error_log('Error in _handle_enable_2fa: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'ไม่สามารถสร้าง QR Code ได้: ' . $e->getMessage()
            ];
        }
    }


    public function debug_trusted_devices()
    {
        if (!$this->session->userdata('mp_id')) {
            echo "<h3>❌ ไม่ได้เข้าสู่ระบบ</h3>";
            return;
        }

        $user_id = $this->session->userdata('mp_id');
        $tenant_id = $this->session->userdata('tenant_id') ?: 1;
        $current_fingerprint = $this->generate_device_fingerprint();

        echo "<h2>🔍 Debug Trusted Devices (Public User)</h2>";
        echo "<hr>";

        echo "<h3>📋 Basic Info</h3>";
        echo "<ul>";
        echo "<li><strong>User ID:</strong> $user_id</li>";
        echo "<li><strong>Tenant ID:</strong> $tenant_id</li>";
        echo "<li><strong>User Type:</strong> public</li>";
        echo "<li><strong>Current Fingerprint:</strong><br><code>" . $current_fingerprint . "</code></li>";
        echo "</ul>";

        echo "<h3>📱 Trusted Devices</h3>";

        if ($this->db->table_exists('trusted_devices')) {
            $devices = $this->db->select('*')
                ->where('user_id', $user_id)
                ->where('user_type', 'public')
                ->where('tenant_id', $tenant_id)
                ->order_by('created_at', 'DESC')
                ->get('trusted_devices')->result();

            if (count($devices) > 0) {
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                echo "<tr><th>ID</th><th>Fingerprint</th><th>Created</th><th>Expires</th><th>Last Used</th><th>Status</th><th>Match</th></tr>";

                foreach ($devices as $device) {
                    $is_expired = (strtotime($device->expires_at) < time()) ? '⚠️ EXPIRED' : '✅ VALID';
                    $is_current = ($device->device_fingerprint === $current_fingerprint) ? '🟢 CURRENT' : '⚪ OTHER';
                    $fingerprint_short = substr($device->device_fingerprint, 0, 16) . '...';

                    echo "<tr>";
                    echo "<td>{$device->id}</td>";
                    echo "<td><code>{$fingerprint_short}</code></td>";
                    echo "<td>{$device->created_at}</td>";
                    echo "<td>{$device->expires_at}</td>";
                    echo "<td>{$device->last_used_at}</td>";
                    echo "<td>{$is_expired}</td>";
                    echo "<td>{$is_current}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>❌ No trusted devices found</p>";
            }
        } else {
            echo "<p>❌ Table 'trusted_devices' does not exist</p>";
        }

        echo "<h3>🧪 Test is_trusted_device()</h3>";
        $is_trusted = $this->is_trusted_device($user_id, $tenant_id, 'public');
        echo "<div style='background: " . ($is_trusted ? '#d4edda' : '#f8d7da') . "; padding: 15px; border-radius: 5px;'>";
        echo "<h4>" . ($is_trusted ? '✅ TRUSTED' : '❌ NOT TRUSTED') . "</h4>";
        echo "</div>";

        echo "<p><a href='" . site_url('Auth_public_mem/profile') . "'>👤 Back to Profile</a></p>";
    }



    public function debug_2fa()
    {
        // ตรวจสอบการเข้าสู่ระบบ
        if (!$this->session->userdata('mp_id')) {
            echo "Please login first";
            return;
        }

        echo "<h3>2FA Debug Information</h3>";

        // ตรวจสอบ Google2FA library
        if (!$this->load->library('Google2FA')) {
            echo "<p style='color: red;'>❌ Google2FA library NOT loaded</p>";
        } else {
            echo "<p style='color: green;'>✅ Google2FA library loaded successfully</p>";

            // ทดสอบการสร้าง secret
            try {
                $test_secret = $this->google2fa->generateSecretKey();
                echo "<p style='color: green;'>✅ Secret generation works: " . substr($test_secret, 0, 8) . "...</p>";

                // ทดสอบการสร้าง OTP
                $test_otp = $this->google2fa->getCurrentOtp($test_secret);
                echo "<p style='color: green;'>✅ OTP generation works: $test_otp</p>";

                // ทดสอบการ verify
                $verify_result = $this->google2fa->verifyKey($test_secret, $test_otp);
                echo "<p style='color: " . ($verify_result ? 'green' : 'red') . "'>";
                echo ($verify_result ? '✅' : '❌') . " OTP verification: " . ($verify_result ? 'PASS' : 'FAIL');
                echo "</p>";

            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error testing Google2FA: " . $e->getMessage() . "</p>";
            }
        }

        // ตรวจสอบข้อมูล 2FA ของผู้ใช้
        $mp_id = $this->session->userdata('mp_id');
        $user_2fa_info = $this->member_public_model->get_2fa_info($mp_id);

        echo "<h4>User 2FA Status:</h4>";
        if ($user_2fa_info) {
            echo "<ul>";
            echo "<li>User ID: " . $user_2fa_info->mp_id . "</li>";
            echo "<li>2FA Enabled: " . ($user_2fa_info->google2fa_enabled ? 'YES' : 'NO') . "</li>";
            echo "<li>Secret exists: " . (!empty($user_2fa_info->google2fa_secret) ? 'YES' : 'NO') . "</li>";
            if (!empty($user_2fa_info->google2fa_secret)) {
                echo "<li>Secret length: " . strlen($user_2fa_info->google2fa_secret) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No 2FA info found</p>";
        }

        // ตรวจสอบตารางในฐานข้อมูล
        echo "<h4>Database Check:</h4>";

        $tables_to_check = ['tbl_member_public'];
        foreach ($tables_to_check as $table) {
            if ($this->db->table_exists($table)) {
                echo "<p style='color: green;'>✅ Table '$table' exists</p>";

                // ตรวจสอบ columns
                $fields = $this->db->list_fields($table);
                $required_fields = ['google2fa_secret', 'google2fa_enabled', 'google2fa_setup_date'];

                foreach ($required_fields as $field) {
                    if (in_array($field, $fields)) {
                        echo "<p style='color: green;'>✅ Column '$field' exists</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Column '$field' NOT exists</p>";
                    }
                }
            } else {
                echo "<p style='color: red;'>❌ Table '$table' NOT exists</p>";
            }
        }

        echo "<h4>Server Time Information:</h4>";
        echo "<p>Server time: " . date('Y-m-d H:i:s') . "</p>";
        echo "<p>Timestamp: " . time() . "</p>";

        echo "<hr>";
        echo "<p><a href='" . site_url('Auth_public_mem/profile') . "'>Back to Profile</a></p>";
    }




    /**
     * หรือใช้วิธีนี้ (หากไม่พบบรรทัดข้างบน):
     * แทนที่ method _handle_verify_setup() ทั้งหมดด้วยโค้ดด้านล่าง
     */

    private function _handle_verify_setup($mp_id)
    {
        try {
            $secret = $this->input->post('secret');
            $otp = $this->input->post('otp');

            // Debug information
            error_log("2FA Setup Debug - User ID: $mp_id");
            error_log("2FA Setup Debug - Secret received: " . ($secret ? 'YES (length: ' . strlen($secret) . ')' : 'NO'));
            error_log("2FA Setup Debug - OTP received: " . ($otp ? 'YES (length: ' . strlen($otp) . ')' : 'NO'));

            if (empty($secret) || empty($otp)) {
                error_log("2FA Setup Error - Missing data. Secret: " . ($secret ? 'present' : 'missing') . ", OTP: " . ($otp ? 'present' : 'missing'));
                return [
                    'status' => 'error',
                    'message' => 'ข้อมูลไม่ครบถ้วน กรุณาลองใหม่อีกครั้ง'
                ];
            }

            // ตรวจสอบรูปแบบ OTP
            if (!preg_match('/^\d{6}$/', $otp)) {
                error_log("2FA Setup Error - Invalid OTP format: $otp");
                return [
                    'status' => 'error',
                    'message' => 'รหัส OTP ต้องเป็นตัวเลข 6 หลัก'
                ];
            }

            if (!isset($this->google2fa)) {
                throw new Exception('Google2FA library not available');
            }

            error_log("2FA Setup Debug - Attempting verification with secret: " . substr($secret, 0, 8) . "... and OTP: $otp");

            // ใช้ window ที่กว้างขึ้นเพื่อความแม่นยำ (2 = ±2 time windows = ±60 seconds)
            $verification_result = $this->google2fa->verifyKey($secret, $otp, 2);

            error_log("2FA Setup Debug - Verification result: " . ($verification_result ? 'SUCCESS' : 'FAILED'));

            if ($verification_result) {
                // บันทึก secret ลงฐานข้อมูล
                $save_result = $this->member_public_model->save_2fa_secret($mp_id, $secret);

                error_log("2FA Setup Debug - Database save result: " . ($save_result ? 'SUCCESS' : 'FAILED'));

                if ($save_result) {
                    // บันทึก log สำเร็จ
                    $this->user_log_model->log_activity(
                        $this->session->userdata('mp_email'),
                        '2fa_enable',
                        'ประชาชนเปิดใช้งาน 2FA สำเร็จ',
                        'security'
                    );

                    return [
                        'status' => 'success',
                        'message' => 'เปิดใช้งาน 2FA สำเร็จ'
                    ];
                } else {
                    error_log("2FA Setup Error - Failed to save to database for user: $mp_id");
                    return [
                        'status' => 'error',
                        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
                    ];
                }
            } else {
                error_log("2FA Setup Error - OTP verification failed for user: $mp_id with OTP: $otp");

                // ให้ข้อมูลเพิ่มเติมสำหรับการ debug
                $current_time = time();
                $expected_code_1 = $this->google2fa->getCurrentOtp($secret);
                $expected_code_2 = $this->google2fa->getOtp($secret, $current_time - 30);
                $expected_code_3 = $this->google2fa->getOtp($secret, $current_time + 30);

                error_log("2FA Debug - Current time: $current_time");
                error_log("2FA Debug - Expected codes: now=$expected_code_1, prev=$expected_code_2, next=$expected_code_3");

                return [
                    'status' => 'error',
                    'message' => 'รหัส OTP ไม่ถูกต้อง กรุณาตรวจสอบเวลาในอุปกรณ์และลองใหม่อีกครั้ง',
                    'debug_info' => [
                        'received_otp' => $otp,
                        'current_time' => $current_time,
                        'suggestion' => 'ตรวจสอบเวลาในมือถือให้ตรงกับเซิร์ฟเวอร์'
                    ]
                ];
            }

        } catch (Exception $e) {
            error_log('Error in _handle_verify_setup: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());

            return [
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ];
        }
    }
    /**
     * จัดการการปิดใช้งาน 2FA
     */
    private function _handle_disable_2fa($mp_id, $user_2fa_info)
    {
        try {
            $otp = $this->input->post('otp');

            if (empty($otp)) {
                return [
                    'status' => 'error',
                    'message' => 'กรุณากรอกรหัส OTP'
                ];
            }

            if (!$user_2fa_info || empty($user_2fa_info->google2fa_secret)) {
                return [
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูล 2FA'
                ];
            }

            if (!isset($this->google2fa)) {
                throw new Exception('Google2FA library not available');
            }

            if ($this->google2fa->verifyKey($user_2fa_info->google2fa_secret, $otp)) {
                if ($this->member_public_model->toggle_2fa($mp_id, false)) {
                    // ลบ trusted devices ทั้งหมด
                    if ($this->db->table_exists('trusted_devices')) {
                        $this->db->where('user_id', $mp_id)
                            ->where('user_type', 'public')
                            ->delete('trusted_devices');
                    }

                    return [
                        'status' => 'success',
                        'message' => 'ปิดใช้งาน 2FA สำเร็จ'
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'เกิดข้อผิดพลาดในการปิดใช้งาน 2FA'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'รหัส OTP ไม่ถูกต้อง'
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Error in _handle_disable_2fa: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ];
        }
    }


    /**
     * หน้าลงทะเบียนสมาชิกใหม่
     */
    public function register_form()
    {
        log_message('info', '📄 register_form() accessed from IP: ' . $this->input->ip_address());

        // เช็คว่าเข้าสู่ระบบแล้วหรือยัง
        if ($this->session->userdata('mp_id')) {
            log_message('info', '⚠️ User already logged in, redirecting to service_systems');
            redirect('Pages/service_systems');
        }

        // ✅ เช็ค session ว่ายืนยันอีเมลแล้วหรือยัง
        $verified_email = $this->session->userdata('registration_email_verified');
        $verified_at = $this->session->userdata('registration_verified_at');

        // เตรียมข้อมูลสำหรับส่งไปยัง view
        $data = array();

        // ⚠️ ถ้ามีการยืนยันและยังไม่หมดอายุ
        if (!empty($verified_email) && !empty($verified_at) && (time() - $verified_at <= 900)) {
            log_message('info', "✅ Valid email verification found for: $verified_email");
            $data['verified_email'] = $verified_email;
            $data['verification_timestamp'] = $verified_at;
        } else {
            // ✅ ถ้ายังไม่ยืนยัน หรือหมดอายุ - แสดงฟอร์มปกติ
            log_message('info', '📝 No valid verification found, showing normal registration form');
            $data['verified_email'] = null;
            $data['verification_timestamp'] = null;

            // ล้าง session เก่า (ถ้ามี)
            if (!empty($verified_email)) {
                log_message('info', "🧹 Clearing expired verification session for: $verified_email");
                $this->session->unset_userdata('registration_email_verified');
                $this->session->unset_userdata('registration_verified_at');
            }
        }

        // ✅ แสดงฟอร์มสมัครสมาชิก
        log_message('info', '🎨 Loading registration form view');
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('public_user/form_register', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer_other');
    }

    /**
     * จัดการการลงทะเบียน (ปรับปรุงรองรับ Email Verification)
     */
    public function register()
    {
        try {
            log_message('info', '📝 register() called from IP: ' . $this->input->ip_address());

            $this->load->library('form_validation');

            // 🆕 เช็คว่าเป็นการเรียกผ่าน AJAX หรือไม่
            $is_ajax = $this->input->is_ajax_request();
            log_message('info', '🔍 Request type: ' . ($is_ajax ? 'AJAX' : 'Form Submit'));

            // 🔒 ตรวจสอบว่ายืนยันอีเมลแล้วหรือยัง
            $post_email = $this->input->post('mp_email');
            $verified_email = $this->session->userdata('registration_email_verified');
            $verified_at = $this->session->userdata('registration_verified_at');

            log_message('info', "📧 Posted email: $post_email");
            log_message('info', "✅ Verified email: " . ($verified_email ? $verified_email : 'none'));

            // 🔒 เช็ค 1: ต้องมี session verification
            if (empty($verified_email) || empty($verified_at)) {
                log_message('warning', '⚠️ Registration blocked - No email verification session');

                if ($is_ajax) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'กรุณายืนยันอีเมลก่อนสมัครสมาชิก'
                    ]);
                    return;
                } else {
                    $this->session->set_flashdata('save_error', 'กรุณายืนยันอีเมลก่อนสมัครสมาชิก');
                    redirect('Auth_public_mem/register_form');
                    return;
                }
            }

            // 🔒 เช็ค 2: อีเมลที่ส่งมาต้องตรงกับที่ยืนยัน
            if ($verified_email !== $post_email) {
                log_message('warning', "⚠️ Registration blocked - Email mismatch. Verified: $verified_email, Posted: $post_email");

                if ($is_ajax) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'อีเมลไม่ตรงกับที่ยืนยัน กรุณายืนยันอีเมลใหม่'
                    ]);
                    return;
                } else {
                    $this->session->set_flashdata('save_error', 'อีเมลไม่ตรงกับที่ยืนยัน');
                    redirect('Auth_public_mem/register_form');
                    return;
                }
            }

            // 🔒 เช็ค 3: การยืนยันต้องไม่เกิน 15 นาที (900 วินาที)
            if ((time() - $verified_at) > 900) {
                log_message('warning', "⚠️ Registration blocked - Email verification expired for: $verified_email");

                // ล้างข้อมูล verification ที่หมดอายุ
                $this->session->unset_userdata('registration_email_verified');
                $this->session->unset_userdata('registration_verified_at');

                if ($is_ajax) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'การยืนยันอีเมลหมดอายุแล้ว (เกิน 15 นาที) กรุณายืนยันใหม่'
                    ]);
                    return;
                } else {
                    $this->session->set_flashdata('save_error', 'การยืนยันอีเมลหมดอายุแล้ว กรุณายืนยันใหม่');
                    redirect('Auth_public_mem/register_form');
                    return;
                }
            }

            // ✅ ผ่านการตรวจสอบ verification แล้ว
            log_message('info', "✅ Email verification passed for: $verified_email");

            // 🆕 ตรวจสอบ flag email_verified (backward compatibility)
            $email_verified = $this->input->post('email_verified');
            log_message('info', "🏁 Email verified flag: " . ($email_verified === 'true' ? 'true' : 'false'));

            if ($email_verified !== 'true') {
                log_message('warning', '⚠️ Registration blocked - email_verified flag not set');

                if ($is_ajax) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'กรุณายืนยันอีเมลก่อนสมัครสมาชิก'
                    ]);
                    return;
                } else {
                    $this->session->set_flashdata('save_error', 'กรุณายืนยันอีเมลก่อนสมัครสมาชิก');
                    redirect('Auth_public_mem/register_form');
                    return;
                }
            }

            // ตั้งค่า validation rules
            log_message('info', '🔍 Setting up validation rules');

            $this->form_validation->set_rules(
                'mp_email',
                'อีเมล',
                'trim|required|min_length[5]|valid_email|is_unique[tbl_member_public.mp_email]',
                array(
                    'required' => 'กรุณากรอกข้อมูล %s.',
                    'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 5 ตัว',
                    'valid_email' => 'กรุณากรอกอีเมลให้ถูกต้อง',
                    'is_unique' => 'อีเมลนี้มีผู้ใช้งานแล้ว'
                )
            );

            $this->form_validation->set_rules(
                'mp_password',
                'รหัสผ่าน',
                'trim|required|min_length[6]',
                array(
                    'required' => 'กรุณากรอกข้อมูล %s.',
                    'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 6 ตัว'
                )
            );

            $this->form_validation->set_rules(
                'confirmp_password',
                'ยืนยันรหัสผ่าน',
                'trim|required|matches[mp_password]',
                array(
                    'required' => 'กรุณายืนยันรหัสผ่าน',
                    'matches' => 'รหัสผ่านไม่ตรงกัน'
                )
            );

            $this->form_validation->set_rules(
                'mp_prefix',
                'คำนำหน้า',
                'trim|required',
                array('required' => 'กรุณาเลือก %s.')
            );

            $this->form_validation->set_rules(
                'mp_fname',
                'ชื่อจริง',
                'trim|required|min_length[2]',
                array(
                    'required' => 'กรุณากรอกข้อมูล %s.',
                    'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 2 ตัว'
                )
            );

            $this->form_validation->set_rules(
                'mp_lname',
                'นามสกุล',
                'trim|required|min_length[2]',
                array(
                    'required' => 'กรุณากรอกข้อมูล %s.',
                    'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 2 ตัว'
                )
            );

            $this->form_validation->set_rules(
                'mp_number',
                'เลขประจำตัวประชาชน',
                'trim|callback_check_id_number_optional',
                array(
                    'check_id_number_optional' => 'เลขประจำตัวประชาชนไม่ถูกต้อง หรือมีผู้ใช้งานแล้ว'
                )
            );

            $this->form_validation->set_rules(
                'mp_phone',
                'เบอร์โทรศัพท์',
                'trim|required|exact_length[10]',
                array(
                    'required' => 'กรุณากรอกข้อมูล %s.',
                    'exact_length' => 'กรุณากรอกเบอร์โทรศพท์ 10 หลัก'
                )
            );

            $this->form_validation->set_rules(
                'mp_address',
                'ที่อยู่เพิ่มเติม',
                'trim|required|min_length[5]',
                array(
                    'required' => 'กรุณากรอกข้อมูล %s.',
                    'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 5 ตัว'
                )
            );

            // ตรวจสอบ validation
            log_message('info', '🔍 Running form validation');

            if ($this->form_validation->run() == FALSE) {
                log_message('warning', '⚠️ Form validation failed');

                if ($is_ajax) {
                    // ส่ง error กลับเป็น JSON
                    $errors = $this->form_validation->error_array();
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'ข้อมูลไม่ถูกต้อง',
                        'errors' => $errors
                    ]);
                    return;
                } else {
                    // แสดงฟอร์มพร้อม error
                    $data = array(
                        'verified_email' => $verified_email,
                        'verification_timestamp' => $verified_at
                    );

                    $this->load->view('frontend_templat/header');
                    $this->load->view('frontend_asset/css');
                    $this->load->view('frontend_templat/navbar_other');
                    $this->load->view('public_user/form_register', $data);
                    $this->load->view('frontend_asset/js');
                    $this->load->view('frontend_templat/footer_other');
                }
            } else {
                log_message('info', '✅ Form validation passed');

                // 🔒 Double-check verification ก่อนบันทึก
                if (
                    empty($this->session->userdata('registration_email_verified')) ||
                    $this->session->userdata('registration_email_verified') !== $post_email
                ) {
                    log_message('error', "❌ CRITICAL: Registration blocked at final check");

                    if ($is_ajax) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'เกิดข้อผิดพลาดในการยืนยันตัวตน กรุณาลองใหม่'
                        ]);
                    } else {
                        $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดในการยืนยันตัวตน');
                        redirect('Auth_public_mem/register_form');
                    }
                    return;
                }

                // เตรียมข้อมูลสำหรับบันทึก
                log_message('info', '📦 Preparing registration data');
                $registration_data = $this->prepare_registration_data();

                log_message('info', "💾 Attempting to save member: {$registration_data['mp_email']}");

                // บันทึกข้อมูลสมาชิก
                $insert_result = $this->member_public_model->create_member($registration_data);

                if ($insert_result) {
                    log_message('info', "✅ Member registered successfully: {$registration_data['mp_email']} (ID: $insert_result)");

                    // ล้างข้อมูล verification
                    $this->session->unset_userdata('registration_email_verified');
                    $this->session->unset_userdata('registration_verified_at');

                    // บันทึก flashdata
                    $this->session->set_flashdata('save_success', TRUE);
                    $this->session->set_flashdata('show_2fa_invite', TRUE);
                    $this->session->set_flashdata('new_member_email', $registration_data['mp_email']);

                    if ($is_ajax) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'สมัครสมาชิกสำเร็จ',
                            'member_id' => $insert_result,
                            'email' => $registration_data['mp_email']
                        ]);
                    } else {
                        redirect('User');
                    }

                } else {
                    log_message('error', "❌ Failed to register member: {$registration_data['mp_email']}");

                    if ($is_ajax) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
                        ]);
                    } else {
                        $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                        redirect('Auth_public_mem/register_form');
                    }
                }
            }

        } catch (Exception $e) {
            log_message('error', '❌ Registration exception: ' . $e->getMessage());
            log_message('error', '❌ Stack trace: ' . $e->getTraceAsString());

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาติดต่อผู้ดูแลระบบ'
                ]);
            } else {
                $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดในระบบ');
                redirect('Auth_public_mem/register_form');
            }
        }
    }


    public function check_id_number_optional($id_number)
    {
        // ถ้าไม่ได้กรอก ให้ผ่าน validation
        if (empty($id_number)) {
            return TRUE;
        }

        // ถ้ากรอก ต้องเป็นตัวเลข 13 หลัก
        if (!preg_match('/^\d{13}$/', $id_number)) {
            $this->form_validation->set_message('check_id_number_optional', 'เลขประจำตัวประชาชนต้องเป็นตัวเลข 13 หลัก');
            return FALSE;
        }

        // 🆕 เพิ่มการตรวจสอบ pattern ไทย
        if (!$this->validate_thai_id_pattern($id_number)) {
            $this->form_validation->set_message('check_id_number_optional', 'รูปแบบเลขประจำตัวประชาชนไม่ถูกต้อง');
            return FALSE;
        }

        // ตรวจสอบความซ้ำในฐานข้อมูล
        if ($this->member_public_model->check_id_card_exists($id_number)) {
            $this->form_validation->set_message('check_id_number_optional', 'เลขประจำตัวประชาชนนี้มีผู้ใช้งานแล้ว');
            return FALSE;
        }

        return TRUE;
    }


    /**
     * ⭐ เพิ่ม method ใหม่: เตรียมข้อมูลสำหรับการลงทะเบียน (รูปแบบใหม่)
     */
    private function prepare_registration_data()
    {
        // ข้อมูลพื้นฐาน
        $data = [
            'mp_id' => $this->generate_member_id(),
            'mp_email' => $this->input->post('mp_email'),
            'mp_password' => sha1($this->input->post('mp_password')),
            'mp_prefix' => $this->input->post('mp_prefix'),
            'mp_fname' => $this->input->post('mp_fname'),
            'mp_lname' => $this->input->post('mp_lname'),
            'mp_phone' => $this->input->post('mp_phone'),
            'mp_status' => 1,
            'mp_registered_date' => date('Y-m-d H:i:s')
        ];

        // ⭐ เลขบัตรประชาชน - อนุญาตให้เป็น null
        $mp_number = trim($this->input->post('mp_number'));
        $data['mp_number'] = !empty($mp_number) ? $mp_number : null;

        // จัดการข้อมูลที่อยู่
        $address_data = $this->prepare_address_data();
        $data = array_merge($data, $address_data);

        // จัดการรูปภาพ
        $image_data = $this->handle_profile_image();
        if ($image_data) {
            $data['mp_img'] = $image_data;
        }

        return $data;
    }

    /**
     * ⭐ สร้าง Member ID แบบใหม่ (ไม่ใช้เลขบัตรประชาชน)
     */
    private function generate_member_id()
    {
        // ใช้ปี + timestamp + random number
        $year = substr(date('Y'), -2);
        $timestamp = time();
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        return $year . $timestamp . $random;
    }

    /**
     * ⭐ AJAX method สำหรับตรวจสอบเลขบัตรประชาชนซ้ำ
     */
    public function check_id_duplicate()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $id_number = $this->input->post('id_number');

        if (empty($id_number)) {
            echo json_encode([
                'status' => 'valid',
                'message' => 'ไม่ได้กรอกเลขบัตรประชาชน'
            ]);
            return;
        }

        if (!preg_match('/^\d{13}$/', $id_number)) {
            echo json_encode([
                'status' => 'invalid',
                'message' => 'เลขบัตรประชาชนต้องเป็นตัวเลข 13 หลัก'
            ]);
            return;
        }

        if ($this->member_public_model->check_id_card_exists($id_number)) {
            echo json_encode([
                'status' => 'duplicate',
                'message' => 'เลขบัตรประชาชนนี้มีผู้ใช้งานแล้ว'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'valid',
            'message' => 'เลขบัตรประชาชนสามารถใช้งานได้'
        ]);
    }

    /**
     * ⭐ เพิ่ม method ใหม่: เตรียมข้อมูลที่อยู่ในรูปแบบใหม่ (บันทึกลง columns แยกย่อย)
     */
    private function prepare_address_data()
    {
        // ดึงข้อมูลจากฟอร์ม
        $additional_address = $this->input->post('additional_address') ?: $this->input->post('mp_address');
        $zipcode = $this->input->post('zipcode');
        $province = $this->input->post('province');
        $amphoe = $this->input->post('amphoe');
        $district = $this->input->post('district');

        // ⭐ ถ้าไม่มีข้อมูลแยกย่อย แต่มี full_address_field (จาก JavaScript)
        $full_address_from_js = $this->input->post('full_address_field');
        if (empty($province) && !empty($full_address_from_js)) {
            // แยกข้อมูลจาก full_address ที่ JavaScript ส่งมา
            $parsed = $this->parse_address($full_address_from_js);

            $province = $province ?: $parsed['province'];
            $amphoe = $amphoe ?: $parsed['amphoe'];
            $district = $district ?: $parsed['district'];
            $zipcode = $zipcode ?: $parsed['zipcode'];
            $additional_address = $additional_address ?: $parsed['additional_address'];
        }

        // ⭐ ถ้ายังไม่มีข้อมูลแยกย่อย และมี mp_address เต็ม
        if (empty($province) && !empty($additional_address)) {
            $parsed = $this->parse_address($additional_address);

            $province = $province ?: $parsed['province'];
            $amphoe = $amphoe ?: $parsed['amphoe'];
            $district = $district ?: $parsed['district'];
            $zipcode = $zipcode ?: $parsed['zipcode'];
            $additional_address = $parsed['additional_address'] ?: $additional_address;
        }

        // Log ข้อมูลที่อยู่ที่ได้รับ
        log_message('info', '📍 Registration address data: ' . json_encode([
            'additional_address' => $additional_address,
            'district' => $district,
            'amphoe' => $amphoe,
            'province' => $province,
            'zipcode' => $zipcode
        ], JSON_UNESCAPED_UNICODE));

        return [
            'mp_address' => $additional_address, // ⭐ เก็บเฉพาะที่อยู่เพิ่มเติม
            'mp_district' => $district,
            'mp_amphoe' => $amphoe,
            'mp_province' => $province,
            'mp_zipcode' => $zipcode
        ];
    }

    /**
     * ⭐ เพิ่ม method ใหม่: ฟังก์ชันแยกที่อยู่
     */
    private function parse_address($address_string)
    {
        $parsed = [
            'additional_address' => '',
            'district' => '',
            'amphoe' => '',
            'province' => '',
            'zipcode' => ''
        ];

        if (empty($address_string)) {
            return $parsed;
        }

        try {
            // แยกรหัสไปรษณีย์ (5 หลักท้ายสุด)
            if (preg_match('/\s(\d{5})$/', $address_string, $zipcode_matches)) {
                $parsed['zipcode'] = $zipcode_matches[1];
                $address_string = preg_replace('/\s\d{5}$/', '', $address_string);
            }

            // แยกจังหวัด (จังหวัด + ชื่อ)
            if (preg_match('/จังหวัด([^\s]+)/', $address_string, $province_matches)) {
                $parsed['province'] = $province_matches[1];
                $address_string = preg_replace('/\s*จังหวัด[^\s]+/', '', $address_string);
            }

            // แยกอำเภอ (อำเภอ + ชื่อ)
            if (preg_match('/อำเภอ([^\s]+)/', $address_string, $amphoe_matches)) {
                $parsed['amphoe'] = $amphoe_matches[1];
                $address_string = preg_replace('/\s*อำเภอ[^\s]+/', '', $address_string);
            }

            // แยกตำบล (ตำบล + ชื่อ หรือ ต. + ชื่อ)
            if (preg_match('/(ตำบล|ต\.)([^\s]+)/', $address_string, $district_matches)) {
                $parsed['district'] = $district_matches[2];
                $address_string = preg_replace('/\s*(ตำบล|ต\.)[^\s]+/', '', $address_string);
            }

            // ที่เหลือเป็นที่อยู่เพิ่มเติม
            $parsed['additional_address'] = trim($address_string);

        } catch (Exception $e) {
            log_message('error', 'Error parsing address: ' . $e->getMessage());
        }

        return $parsed;
    }

    /**
     * ⭐ เพิ่ม method ใหม่: จัดการรูปภาพโปรไฟล์
     */
    private function handle_profile_image()
    {
        try {
            // ตรวจสอบว่าเลือก Avatar หรือ Upload รูป
            $avatar_choice = $this->input->post('avatar_choice');
            $avatar_url = $this->input->post('avatar_url');

            if ($avatar_choice && $avatar_url) {
                // ใช้ Avatar ที่เลือก
                return $this->download_avatar_image($avatar_url);
            } else {
                // Upload รูปภาพ
                return $this->upload_profile_image();
            }

        } catch (Exception $e) {
            log_message('error', 'Error handling profile image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ⭐ เพิ่ม method ใหม่: Download Avatar image
     */
    /**
     * Download Avatar image (แก้ไขให้ไปที่ docs/img/avatar/)
     */
    private function download_avatar_image($avatar_url)
    {
        try {
            $image_data = file_get_contents($avatar_url);
            if ($image_data) {
                $filename = uniqid() . '.jpg';

                // ใช้ path avatar โดยเฉพาะ
                $avatar_dir = './docs/img/avatar/';
                if (!is_dir($avatar_dir)) {
                    mkdir($avatar_dir, 0755, true);
                }

                $filepath = $avatar_dir . $filename;

                if (file_put_contents($filepath, $image_data)) {
                    error_log("Avatar downloaded successfully: $filepath");
                    return $filename;
                }
            }
        } catch (Exception $e) {
            error_log('Error downloading avatar: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * ⭐ เพิ่ม method ใหม่: Upload รูปภาพโปรไฟล์
     */
    /**
     * Upload รูปภาพโปรไฟล์ (แก้ไขให้ไปที่ docs/img/avatar/)
     */
    private function upload_profile_image()
    {
        if (!empty($_FILES['mp_img']['name'])) {
            // กำหนด path สำหรับ avatar โดยเฉพาะ
            $config['upload_path'] = './docs/img/avatar/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['encrypt_name'] = TRUE;

            // สร้าง directory ถ้ายังไม่มี
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('mp_img')) {
                $upload_data = $this->upload->data();
                error_log("Profile image uploaded successfully: " . $config['upload_path'] . $upload_data['file_name']);
                return $upload_data['file_name'];
            } else {
                error_log('Upload error: ' . $this->upload->display_errors());
            }
        }

        return null;
    }



    /**
     * ออกจากระบบ
     */
    public function logout()
    {
        $mp_email = $this->session->userdata('mp_email');
        $mp_id = $this->session->userdata('mp_id');

        // บันทึก log การ logout
        if ($mp_email) {
            $this->user_log_model->log_activity(
                $mp_email,
                'logout',
                'ประชาชนออกจากระบบ',
                'auth'
            );

            // บันทึก log สำหรับ tax_user_log_model ด้วย
            $log_data = array(
                'user_id' => $mp_id,
                'user_type' => 'member_public',
                'action' => 'logout',
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent()
            );
            $this->tax_user_log_model->insert_log($log_data);
        }

        // ลบ auth tokens ถ้ามี
        if ($mp_id && $this->db->table_exists('auth_tokens')) {
            $this->db->where('user_id', $mp_id)
                ->where('domain', $_SERVER['HTTP_HOST'])
                ->delete('auth_tokens');
        }

        // ล้างข้อมูล session ทั้งหมดโดยระบุ keys ที่ต้องการล้าง
        $this->session->unset_userdata(array(
            'mp_id',
            'mp_email',
            'mp_fname',
            'mp_lname',
            'mp_phone',
            'mp_number',
            'mp_address',
            'mp_img',
            'is_public',
            '2fa_verified',
            'trusted_device',
            'tenant_id',
            'tenant_code',
            'tenant_name',
            'tenant_domain',
            'permissions',
            // ล้าง temp session ด้วย
            'temp_mp_id',
            'temp_mp_email',
            'temp_mp_fname',
            'temp_mp_lname',
            'temp_mp_img',
            'temp_mp_phone',
            'temp_mp_number',
            'temp_mp_address',
            'temp_google2fa_secret',
            'temp_login_time',
            'temp_user_type',
            'requires_2fa'
        ));

        // ล้าง session ทั้งหมด
        $this->session->sess_destroy();

        // ลบ cookie ของ session
        delete_cookie('ci_session');

        // สร้าง message แบบใช้ cookie แทน flashdata
        set_cookie('logout_message', 'true', 60); // 60 วินาที

        // เปลี่ยนการ redirect ให้ใช้ header แทน
        redirect('Home', 'refresh');
    }

    public function set_redirect_and_login()
    {
        $this->session->set_userdata('redirect_after_login', 'Pages/service_systems');
        redirect('User');
    }

    public function test()
    {
        echo "Auth_public_mem controller test page.";
        echo '<pre>';
        echo 'Current URL: ' . current_url();
        echo "\nSession data: ";
        print_r($this->session->userdata());
        echo '</pre>';
        exit;
    }

    // ฟังก์ชันสำหรับล้าง session ทั้งหมด (ใช้สำหรับการทดสอบ)
    public function clear_session()
    {
        $this->session->sess_destroy();
        echo "All session data cleared. <a href='" . site_url('Auth_public_mem/test') . "'>Click here</a> to see result.";
        exit;
    }

    public function clear_all_sessions()
    {
        // ล้าง session ของ CI
        $this->session->sess_destroy();

        // ลบ cookie ของ CI session
        if (isset($_COOKIE['ci_session'])) {
            delete_cookie('ci_session');
        }

        // ลบ cookie อื่นๆ ที่อาจเกี่ยวข้อง
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // ทำลาย PHP session ถ้ามีการใช้
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        echo "All sessions and cookies cleared. <a href='" . site_url('Auth_public_mem/test') . "'>Check result</a>";
        exit;
    }

    /**
     * สร้างและบันทึก token สำหรับ SSO
     * เรียกใช้หลังจาก login สำเร็จ
     */
    private function generate_sso_token()
    {
        // ดึงข้อมูล tenant
        $tenant = $this->tenant_access_model->get_tenant_by_domain($_SERVER['HTTP_HOST']);

        if (!$tenant) {
            return false;
        }

        // สร้าง token ใหม่
        $token = hash('sha256', $this->session->userdata('mp_id') . time() . random_bytes(32));

        // ข้อมูล token ที่จะบันทึก
        $token_data = array(
            'token' => $token,
            'user_id' => $this->session->userdata('mp_id'),
            'ipaddress' => $this->input->ip_address(),
            'domain' => $_SERVER['HTTP_HOST'],
            'tenant_id' => $tenant->id,
            'tenant_code' => $tenant->code,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
            'created_at' => date('Y-m-d H:i:s')
        );

        // ลบ token เก่าที่หมดอายุ
        $this->db->where([
            'user_id' => $this->session->userdata('mp_id'),
            'domain' => $_SERVER['HTTP_HOST'],
            'expires_at <=' => date('Y-m-d H:i:s')
        ])->delete('auth_tokens');

        // บันทึก token ใหม่
        $this->db->insert('auth_tokens', $token_data);

        return $token;
    }

    /**
     * ดึง IP address ของผู้ใช้
     * @return string IP address
     */
    private function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    // *** ฟังก์ชันสำหรับจัดการการเข้าสู่ระบบล้มเหลว ***

    /**
     * จัดการการเข้าสู่ระบบล้มเหลว
     */
    private function handle_login_failure($email, $password, $fingerprint)
    {
        try {
            error_log("Handling login failure for: $email");

            $this->record_login_attempt($email, 'failed', $fingerprint);

            $attempts_info = $this->count_failed_attempts($fingerprint);
            $max_attempts = 3;
            $remaining_attempts = $max_attempts - $attempts_info;

            // บันทึก log กิจกรรม (ถ้า model มีอยู่)
            if (isset($this->user_log_model)) {
                $this->user_log_model->log_detect(
                    $email,
                    $password,
                    'public',
                    'failed',
                    'Public user login failed',
                    'auth'
                );
            }

            if ($remaining_attempts <= 0) {
                $this->block_login($fingerprint);

                // ส่ง security alert (ถ้า method มีอยู่)
                if (method_exists($this, 'send_security_alert')) {
                    $this->send_security_alert($email, $attempts_info, 'public', 1);
                }

                $response = [
                    'status' => 'blocked',
                    'message' => 'คุณถูกบล็อค 3 นาที เนื่องจากล็อกอินผิดพลาด 3 ครั้ง',
                    'remaining_time' => 180,
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

            error_log("Login failure handled - remaining attempts: $remaining_attempts");

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            error_log("Error in handle_login_failure: " . $e->getMessage());

            $response = [
                'status' => 'error',
                'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'
            ];

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    public function debug_login_status()
    {
        echo "<h2>🔍 Debug Login Status</h2>";
        echo "<hr>";

        echo "<h3>📋 Session Data</h3>";
        echo "<pre>";
        print_r($this->session->userdata());
        echo "</pre>";

        echo "<h3>📊 Models Status</h3>";
        echo "<ul>";
        echo "<li>member_public_model: " . (isset($this->member_public_model) ? '✅ Loaded' : '❌ Not Loaded') . "</li>";
        echo "<li>user_log_model: " . (isset($this->user_log_model) ? '✅ Loaded' : '❌ Not Loaded') . "</li>";
        echo "<li>Google2FA: " . (isset($this->google2fa) ? '✅ Loaded' : '❌ Not Loaded') . "</li>";
        echo "</ul>";

        echo "<h3>🗄️ Database Tables</h3>";
        echo "<ul>";
        $tables = ['tbl_member_public', 'trusted_devices', 'tbl_member_login_attempts'];
        foreach ($tables as $table) {
            $exists = $this->db->table_exists($table);
            echo "<li>$table: " . ($exists ? '✅ Exists' : '❌ Missing') . "</li>";
        }
        echo "</ul>";

        echo "<h3>🌐 Environment</h3>";
        echo "<ul>";
        echo "<li>HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "</li>";
        echo "<li>REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "</li>";
        echo "<li>User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "</li>";
        echo "</ul>";
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
     * จัดการการอัพเดทโปรไฟล์
     */
    private function handle_profile_update_ajax($mp_id)
    {
        try {
            // ตรวจสอบว่ามีการอัพโหลดรูปภาพหรือไม่
            if (!empty($_FILES['mp_img']['name'])) {
                return $this->handle_image_upload($mp_id);
            }

            // ตรวจสอบว่าเป็นการเปลี่ยนรหัสผ่านหรือไม่
            if ($this->input->post('mp_password') && $this->input->post('confirmp_password')) {
                return $this->handle_password_change($mp_id);
            }

            // การอัพเดทข้อมูลพื้นฐาน
            return $this->handle_basic_info_update($mp_id);

        } catch (Exception $e) {
            error_log('Error in handle_profile_update_ajax: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ];
        }
    }




    /**
     * จัดการการอัพโหลดรูปภาพ (เวอร์ชันปรับปรุง)
     */
    /**
     * จัดการการอัพโหลดรูปภาพ (เวอร์ชันปรับปรุง - ใช้ docs/img/avatar/)
     */
    private function handle_image_upload($mp_id)
    {
        try {
            // กำหนด path สำหรับ avatar โดยเฉพาะ
            $avatar_paths = [
                './docs/img/avatar/',
                'docs/img/avatar/',
                $_SERVER['DOCUMENT_ROOT'] . '/docs/img/avatar/',
                realpath('./') . '/docs/img/avatar/',
            ];

            $upload_path = null;

            // หา path ที่ใช้งานได้สำหรับ avatar
            foreach ($avatar_paths as $path) {
                if (!is_dir($path)) {
                    // พยายามสร้าง folder
                    if (mkdir($path, 0755, true)) {
                        $upload_path = $path;
                        break;
                    }
                } else if (is_writable($path)) {
                    $upload_path = $path;
                    break;
                }
            }

            // ถ้าไม่มี path ไหนใช้ได้ ให้สร้างใน uploads/avatars/
            if (!$upload_path) {
                $upload_path = './uploads/avatars/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }
            }

            // ตรวจสอบอีกครั้ง
            if (!is_dir($upload_path) || !is_writable($upload_path)) {
                return [
                    'success' => false,
                    'message' => 'ไม่สามารถเข้าถึง folder สำหรับอัพโหลดได้',
                    'debug_info' => [
                        'tried_paths' => $avatar_paths,
                        'final_path' => $upload_path,
                        'document_root' => $_SERVER['DOCUMENT_ROOT'],
                        'current_dir' => getcwd()
                    ]
                ];
            }

            // ตั้งค่าการอัพโหลด
            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|png|jpeg',
                'max_size' => 5120, // 5MB
                'encrypt_name' => TRUE,
                'remove_spaces' => TRUE,
                'file_ext_tolower' => TRUE
            ];

            // Initialize upload library
            $this->load->library('upload');
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('mp_img')) {
                return [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการอัพโหลด: ' . $this->upload->display_errors('', ''),
                    'debug_info' => [
                        'upload_path' => $upload_path,
                        'config' => $config
                    ]
                ];
            }

            $upload_data = $this->upload->data();
            $filename = $upload_data['file_name'];

            // ลบรูปเก่า
            $old_user_data = $this->member_public_model->get_member_by_id($mp_id);
            if ($old_user_data->mp_img) {
                // ลองลบจาก avatar paths
                $delete_paths = [
                    './docs/img/avatar/' . $old_user_data->mp_img,
                    'docs/img/avatar/' . $old_user_data->mp_img,
                    './uploads/avatars/' . $old_user_data->mp_img,
                    './docs/img/' . $old_user_data->mp_img, // เก่า
                    './uploads/' . $old_user_data->mp_img   // เก่า
                ];

                foreach ($delete_paths as $delete_path) {
                    if (file_exists($delete_path)) {
                        @unlink($delete_path);
                        error_log("Deleted old image: $delete_path");
                        break;
                    }
                }
            }

            // อัพเดทฐานข้อมูล
            $update_data = [
                'mp_img' => $filename,
                'mp_updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->member_public_model->update_full_profile($mp_id, $update_data)) {
                // อัพเดท session
                $this->session->set_userdata('mp_img', $filename);

                return [
                    'success' => true,
                    'message' => 'อัพเดทรูปภาพสำเร็จ',
                    'profile' => [
                        'mp_img' => $filename
                    ],
                    'upload_info' => [
                        'path' => $upload_path,
                        'filename' => $filename,
                        'file_size' => $upload_data['file_size'],
                        'upload_to' => 'docs/img/avatar/'
                    ]
                ];
            } else {
                // ลบไฟล์ที่อัพโหลดแล้วถ้าบันทึกข้อมูลไม่สำเร็จ
                @unlink($upload_path . $filename);

                return [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                ];
            }

        } catch (Exception $e) {
            error_log('Error in handle_image_upload: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ: ' . $e->getMessage()
            ];
        }
    }







    private function handle_password_change($mp_id)
    {
        try {
            $new_password = $this->input->post('mp_password');
            $confirm_password = $this->input->post('confirmp_password');

            // Validation
            if (empty($new_password) || empty($confirm_password)) {
                return [
                    'success' => false,
                    'message' => 'กรุณากรอกรหัสผ่านให้ครบถ้วน'
                ];
            }

            if (strlen($new_password) < 6) {
                return [
                    'success' => false,
                    'message' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'
                ];
            }

            if ($new_password !== $confirm_password) {
                return [
                    'success' => false,
                    'message' => 'รหัสผ่านไม่ตรงกัน'
                ];
            }

            // อัพเดทรหัสผ่าน
            $update_data = [
                'mp_password' => sha1($new_password),
                'mp_updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->member_public_model->update_full_profile($mp_id, $update_data)) {
                return [
                    'success' => true,
                    'message' => 'เปลี่ยนรหัสผ่านสำเร็จ'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                ];
            }

        } catch (Exception $e) {
            error_log('Error in handle_password_change: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน'
            ];
        }
    }

    private function handle_basic_info_update($mp_id)
    {
        try {
            // Validation
            $mp_prefix = $this->input->post('mp_prefix');
            $mp_fname = $this->input->post('mp_fname');
            $mp_lname = $this->input->post('mp_lname');
            $mp_phone = $this->input->post('mp_phone');
            $mp_address = $this->input->post('mp_address');
            $mp_number = $this->input->post('mp_number');

            // ⭐ เพิ่ม: รับค่าวันเกิด
            $mp_birthdate = $this->input->post('mp_birthdate');

            if (empty($mp_prefix) || empty($mp_fname) || empty($mp_lname) || empty($mp_address)) {
                return [
                    'success' => false,
                    'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
                ];
            }

            if (!empty($mp_phone) && strlen($mp_phone) !== 10) {
                return [
                    'success' => false,
                    'message' => 'เบอร์โทรศัพท์ต้องมี 10 หลัก'
                ];
            }

            // ⭐ ใช้ method validation ใหม่สำหรับ ID number
            if (!empty($mp_number)) {
                $id_validation = $this->member_public_model->validate_id_number($mp_number, $mp_id);

                if (!$id_validation['valid']) {
                    return [
                        'success' => false,
                        'message' => $id_validation['message']
                    ];
                }

                if (!$id_validation['available']) {
                    return [
                        'success' => false,
                        'message' => $id_validation['message']
                    ];
                }
            }

            // ⭐ เพิ่ม: Validate วันเกิด
            if (!empty($mp_birthdate)) {
                $birthdate_validation = $this->validate_birthdate($mp_birthdate);

                if (!$birthdate_validation['valid']) {
                    return [
                        'success' => false,
                        'message' => $birthdate_validation['message']
                    ];
                }
            }

            // เตรียมข้อมูลสำหรับอัพเดท
            $update_data = [
                'mp_prefix' => $mp_prefix,
                'mp_fname' => $mp_fname,
                'mp_lname' => $mp_lname,
                'mp_phone' => $mp_phone,
                'mp_address' => $mp_address,
                'mp_number' => !empty($mp_number) ? $mp_number : null,
                'mp_birthdate' => !empty($mp_birthdate) ? $mp_birthdate : null, // ⭐ เพิ่มบรรทัดนี้
                'mp_updated_at' => date('Y-m-d H:i:s')
            ];

            // เพิ่มข้อมูลที่อยู่ละเอียด
            $mp_district = $this->input->post('mp_district');
            $mp_amphoe = $this->input->post('mp_amphoe');
            $mp_province = $this->input->post('mp_province');
            $mp_zipcode = $this->input->post('mp_zipcode');

            if (!empty($mp_district))
                $update_data['mp_district'] = $mp_district;
            if (!empty($mp_amphoe))
                $update_data['mp_amphoe'] = $mp_amphoe;
            if (!empty($mp_province))
                $update_data['mp_province'] = $mp_province;
            if (!empty($mp_zipcode))
                $update_data['mp_zipcode'] = $mp_zipcode;

            // ⭐ ใช้ method update_full_profile ที่แก้ไขแล้ว
            if ($this->member_public_model->update_full_profile($mp_id, $update_data)) {
                // อัพเดท session
                $this->session->set_userdata([
                    'mp_fname' => $mp_fname,
                    'mp_lname' => $mp_lname
                ]);

                // ⭐ เพิ่ม log การอัพเดท ID number
                if (isset($update_data['mp_number'])) {
                    $id_info = $update_data['mp_number'] ? 'updated ID number' : 'removed ID number';
                    log_message('info', "User $mp_id $id_info in profile update");
                }

                // ⭐ เพิ่ม log การอัพเดทวันเกิด
                if (isset($update_data['mp_birthdate'])) {
                    $birth_info = $update_data['mp_birthdate'] ? 'updated birthdate to: ' . $update_data['mp_birthdate'] : 'removed birthdate';
                    log_message('info', "User $mp_id $birth_info in profile update");
                }

                // ⭐ ดึงข้อมูลใหม่ทั้งหมดส่งกลับ (รวม birthdate)
                $updated_profile = $this->member_public_model->get_member_by_id($mp_id);

                return [
                    'success' => true,
                    'message' => 'อัพเดทข้อมูลสำเร็จ',
                    'profile' => $updated_profile // ⭐ ส่งข้อมูลทั้งหมดกลับ
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                ];
            }

        } catch (Exception $e) {
            error_log('Error in handle_basic_info_update: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ตรวจสอบความถูกต้องของวันเกิด
     * 
     * @param string $birthdate รูปแบบ YYYY-MM-DD
     * @return array ['valid' => bool, 'message' => string]
     */
    private function validate_birthdate($birthdate)
    {
        try {
            // ตรวจสอบรูปแบบพื้นฐาน
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
                return [
                    'valid' => false,
                    'message' => 'รูปแบบวันที่ไม่ถูกต้อง (ต้องเป็น YYYY-MM-DD)'
                ];
            }

            // แยกส่วนของวันที่
            list($year, $month, $day) = explode('-', $birthdate);
            $year = (int) $year;
            $month = (int) $month;
            $day = (int) $day;

            // ตรวจสอบความถูกต้องของวันที่
            if (!checkdate($month, $day, $year)) {
                return [
                    'valid' => false,
                    'message' => 'วันที่ไม่ถูกต้อง'
                ];
            }

            // ตรวจสอบว่าไม่ใช่วันในอนาคต
            $birth_timestamp = strtotime($birthdate);
            $today_timestamp = strtotime(date('Y-m-d'));

            if ($birth_timestamp > $today_timestamp) {
                return [
                    'valid' => false,
                    'message' => 'วันเกิดไม่สามารถเป็นวันในอนาคตได้'
                ];
            }

            // ตรวจสอบอายุขั้นต่ำ (13 ปี)
            $min_birth_year = date('Y') - 13;

            if ($year > $min_birth_year) {
                return [
                    'valid' => false,
                    'message' => 'ต้องมีอายุอย่างน้อย 13 ปี'
                ];
            }

            // ตรวจสอบอายุสูงสุด (120 ปี)
            $max_birth_year = date('Y') - 120;

            if ($year < $max_birth_year) {
                return [
                    'valid' => false,
                    'message' => 'วันเกิดไม่สมเหตุสมผล'
                ];
            }

            return [
                'valid' => true,
                'message' => 'วันเกิดถูกต้อง'
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in validate_birthdate: ' . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบวันเกิด'
            ];
        }
    }


    /**
     * ⭐ เพิ่ม: Method สำหรับ AJAX validation เลขบัตรประชาชน
     */
    public function validate_id_number_ajax()
    {
        // ป้องกันการเข้าถึงโดยตรง
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // Set headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            $id_number = $this->input->post('id_number');
            $current_user_id = $this->session->userdata('mp_id'); // ยกเว้นตัวเอง

            // ✅ เพิ่ม debug logging
            log_message('debug', "🔍 AJAX validate_id_number: ID=$id_number, User=$current_user_id");

            // ใช้ method validation ใหม่จาก Model
            $validation_result = $this->member_public_model->validate_id_number($id_number, $current_user_id);

            log_message('debug', "📋 Model result: " . json_encode($validation_result));

            // ✅ แก้ไข: Logic ที่ถูกต้องตาม JavaScript expectations
            if ($validation_result['valid'] && $validation_result['available']) {
                // กรณี: รูปแบบถูกต้อง และ ใช้งานได้ (ไม่ซ้ำ)
                $response = [
                    'status' => 'valid',           // ✅ ตรงกับ JavaScript
                    'available' => true,
                    'message' => $validation_result['message']
                ];
                log_message('debug', "✅ Result: VALID & AVAILABLE");

            } elseif ($validation_result['valid'] && !$validation_result['available']) {
                // กรณี: รูปแบบถูกต้อง แต่ ไม่ใช้งานได้ (ซ้ำ)
                $response = [
                    'status' => 'duplicate',       // ✅ ตรงกับ JavaScript
                    'available' => false,
                    'message' => $validation_result['message']
                ];
                log_message('debug', "❌ Result: VALID but DUPLICATE");

            } else {
                // กรณี: รูปแบบไม่ถูกต้อง
                $response = [
                    'status' => 'invalid',         // ✅ ตรงกับ JavaScript
                    'available' => false,
                    'message' => $validation_result['message']
                ];
                log_message('debug', "❌ Result: INVALID FORMAT");
            }

            log_message('debug', "📤 Final response: " . json_encode($response));

            echo json_encode($response);

        } catch (Exception $e) {
            log_message('error', 'Error in validate_id_number_ajax: ' . $e->getMessage());

            echo json_encode([
                'status' => 'error',            // System error
                'available' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ'
            ]);
        }

        exit; // สำคัญ: ป้องกัน output เพิ่มเติม
    }



    // *** ฟังก์ชันสำหรับ Trusted Device Management ***

    private function is_trusted_device($user_id, $tenant_id, $user_type = 'public')
    {
        try {
            // *** สำคัญ: ใช้ string แทน int ***
            $user_id = (string) $user_id;

            $device_fingerprint = $this->generate_device_fingerprint();
            $current_time = date('Y-m-d H:i:s');

            // ค้นหา trusted device
            $trusted = $this->db->select('*')
                ->where('user_id', $user_id) // ใช้ string
                ->where('user_type', $user_type)
                ->where('tenant_id', (int) $tenant_id)
                ->where('device_fingerprint', $device_fingerprint)
                ->where('expires_at >', $current_time)
                ->get('trusted_devices');

            return $trusted->num_rows() > 0;

        } catch (Exception $e) {
            error_log("Error in is_trusted_device: " . $e->getMessage());
            return false;
        }
    }




    private function save_trusted_device($user_id, $tenant_id = null, $user_type = 'public', $duration_hours = 720)
    {
        try {
            // *** สำคัญ: ตรวจสอบ data type ***
            $user_id = (string) $user_id; // Force เป็น string

            if (!$tenant_id) {
                $tenant_id = $this->session->userdata('tenant_id') ?: 1;
            }

            $device_token = bin2hex(random_bytes(32));
            $device_fingerprint = $this->generate_device_fingerprint();
            $current_time = date('Y-m-d H:i:s');
            $expires_time = date('Y-m-d H:i:s', time() + ($duration_hours * 3600));

            error_log("=== SAVING TRUSTED DEVICE (FIXED) ===");
            error_log("User ID (string): $user_id");
            error_log("User Type: $user_type");
            error_log("Tenant ID: $tenant_id");

            // ลบ device เก่า
            $this->db->where('user_id', $user_id) // ใช้ string
                ->where('user_type', $user_type)
                ->where('tenant_id', $tenant_id)
                ->where('device_fingerprint', $device_fingerprint)
                ->delete('trusted_devices');

            // บันทึกข้อมูลใหม่
            $data = [
                'user_id' => $user_id, // ใช้ string ไม่ใช่ (int)
                'user_type' => $user_type,
                'tenant_id' => (int) $tenant_id,
                'device_token' => $device_token,
                'device_fingerprint' => $device_fingerprint,
                'device_info' => json_encode([
                    'user_agent' => $this->input->user_agent() ?: 'Unknown',
                    'ip_address' => $this->input->ip_address() ?: '0.0.0.0',
                    'saved_at' => $current_time,
                    'source' => 'public_2fa_verification'
                ]),
                'ip_address' => $this->input->ip_address() ?: '0.0.0.0',
                'user_agent' => substr($this->input->user_agent() ?: 'Unknown', 0, 500),
                'created_at' => $current_time,
                'expires_at' => $expires_time,
                'last_used_at' => $current_time
            ];

            $insert_result = $this->db->insert('trusted_devices', $data);

            if ($insert_result) {
                error_log("✅ TRUSTED DEVICE SAVED SUCCESSFULLY!");
                return $device_token;
            } else {
                error_log("❌ FAILED to save trusted device");
                return false;
            }

        } catch (Exception $e) {
            error_log("Exception in save_trusted_device: " . $e->getMessage());
            return false;
        }
    }


    public function simple_debug_trusted()
    {
        if (!$this->session->userdata('mp_id')) {
            echo "<h3>❌ ไม่ได้เข้าสู่ระบบ</h3>";
            return;
        }

        $user_id = $this->session->userdata('mp_id');
        $tenant_id = $this->session->userdata('tenant_id') ?: 1;
        $current_fingerprint = $this->generate_device_fingerprint();

        echo "<h2>🔍 Simple Debug Trusted Device</h2>";
        echo "<hr>";

        echo "<h3>📋 Basic Info</h3>";
        echo "<ul>";
        echo "<li><strong>User ID:</strong> $user_id</li>";
        echo "<li><strong>Tenant ID:</strong> $tenant_id</li>";
        echo "<li><strong>Current Fingerprint:</strong><br><code>" . substr($current_fingerprint, 0, 40) . "...</code></li>";
        echo "</ul>";

        echo "<h3>📱 Trusted Devices for this Fingerprint</h3>";

        $devices = $this->db->select('*')
            ->where('device_fingerprint', $current_fingerprint)
            ->where('user_type', 'public')
            ->order_by('created_at', 'DESC')
            ->get('trusted_devices')->result();

        if (count($devices) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>User ID</th><th>Tenant ID</th><th>Token</th><th>Created</th><th>Expires</th><th>Status</th></tr>";

            foreach ($devices as $device) {
                $is_expired = (strtotime($device->expires_at) < time()) ? '⚠️ EXPIRED' : '✅ VALID';
                $token_short = substr($device->device_token, 0, 16) . '...';

                echo "<tr>";
                echo "<td>{$device->id}</td>";
                echo "<td><strong>{$device->user_id}</strong></td>";
                echo "<td><strong>{$device->tenant_id}</strong></td>";
                echo "<td><code>{$token_short}</code></td>";
                echo "<td>{$device->created_at}</td>";
                echo "<td>{$device->expires_at}</td>";
                echo "<td>{$is_expired}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No devices found for this fingerprint</p>";
        }

        echo "<h3>🧪 Test is_trusted_device()</h3>";
        $is_trusted = $this->is_trusted_device($user_id, $tenant_id, 'public');
        echo "<div style='background: " . ($is_trusted ? '#d4edda' : '#f8d7da') . "; padding: 15px; border-radius: 5px;'>";
        echo "<h4>" . ($is_trusted ? '✅ TRUSTED' : '❌ NOT TRUSTED') . "</h4>";
        echo "</div>";

        echo "<p><a href='" . site_url('Auth_public_mem/profile') . "'>👤 Back to Profile</a></p>";
    }
    /**
     * เพิ่มฟังก์ชันใน member_public_model.php
     */
    public function get_user_by_email($email)
    {
        return $this->db->select('mp_id, mp_email, mp_fname, mp_lname')
            ->where('mp_email', $email)
            ->where('mp_status', 1)
            ->get('tbl_member_public')
            ->row();
    }


    private function generate_device_fingerprint()
    {
        // ใช้ข้อมูลที่เสถียรกว่า
        $user_agent = $this->input->user_agent() ?: $_SERVER['HTTP_USER_AGENT'] ?: '';
        $accept_language = $this->input->server('HTTP_ACCEPT_LANGUAGE') ?: '';

        // ทำความสะอาดข้อมูลให้เสถียรขึ้น
        $user_agent = trim(strtolower($user_agent));
        $accept_language = trim(strtolower($accept_language));

        // เอาเฉพาะส่วนหลักของ User Agent (ไม่รวมเวอร์ชันที่เปลี่ยนบ่อย)
        $user_agent = preg_replace('/\s+version\/[\d\.]+/i', '', $user_agent);
        $user_agent = preg_replace('/\s+chrome\/[\d\.]+/i', ' chrome', $user_agent);
        $user_agent = preg_replace('/\s+safari\/[\d\.]+/i', ' safari', $user_agent);

        $fingerprint_string = implode('|', [
            $user_agent,
            $accept_language
        ]);

        $fingerprint = hash('sha256', $fingerprint_string);

        error_log("Device fingerprint generated from: " . substr($fingerprint_string, 0, 100) . "...");
        error_log("Final fingerprint: " . substr($fingerprint, 0, 16) . "...");

        return $fingerprint;
    }





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
            error_log("Updated $affected_rows trusted device usage records for public user");

            return $affected_rows > 0;

        } catch (Exception $e) {
            error_log("Exception in update_trusted_device_usage (public): " . $e->getMessage());
            return false;
        }
    }

    // *** ฟังก์ชันสำหรับจัดการ Login Attempts ***

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

    private function send_security_alert($username, $failed_count, $user_type, $block_level = 1)
    {
        // ใช้การส่ง alert เหมือนใน Auth_api
        if (isset($this->user_log_model) && method_exists($this->user_log_model, 'send_line_alert')) {
            $message = "🔒 แจ้งเตือนการล็อกอินล้มเหลว 🔒\n\n";
            $message .= "👤 ผู้ใช้: " . $username . "\n";
            $message .= "📱 ประเภท: ประชาชน\n";
            $message .= "🔄 พยายามเข้าสู่ระบบล้มเหลว: " . $failed_count . " ครั้ง\n";
            $message .= "🌐 IP Address: " . $this->input->ip_address() . "\n";
            $message .= "⏰ เวลา: " . date('Y-m-d H:i:s') . "\n";
            $message .= "⚠️ สถานะ: ถูกบล็อกเป็นเวลา 3 นาที";

            $this->user_log_model->send_line_alert($message);
        }
    }

    private function clear_temp_session()
    {
        $this->session->unset_userdata([
            'temp_mp_id',
            'temp_mp_email',
            'temp_mp_fname',
            'temp_mp_lname',
            'temp_mp_img',
            'temp_mp_phone',
            'temp_mp_number',
            'temp_mp_address',
            'temp_google2fa_secret',
            'temp_login_time',
            'temp_user_type',
            'requires_2fa'
        ]);
    }

    private function cleanup_tokens()
    {
        // ทำความสะอาด tokens และ trusted devices ที่หมดอายุ
        if ($this->db->table_exists('trusted_devices')) {
            $this->db->where('expires_at <', date('Y-m-d H:i:s'))
                ->delete('trusted_devices');
        }

        if ($this->db->table_exists('auth_tokens')) {
            $this->db->where('expires_at <', date('Y-m-d H:i:s'))
                ->delete('auth_tokens');
        }
    }


    public function check_2fa_status()
    {
        // ตรวจสอบว่าเป็น AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // ตรวจสอบการล็อกอิน
        if (!$this->session->userdata('mp_id')) {
            echo json_encode([
                'status' => 'error',
                'message' => 'User not logged in'
            ]);
            return;
        }

        try {
            $mp_id = $this->session->userdata('mp_id');

            // ตรวจสอบข้อมูล 2FA ของผู้ใช้
            $this->db->where('mp_id', $mp_id);
            $user_2fa = $this->db->get('member_2fa')->row();

            // ตรวจสอบว่าเปิดใช้งาน 2FA แล้วหรือไม่
            $is_enabled = false;
            if ($user_2fa && !empty($user_2fa->google2fa_secret) && $user_2fa->google2fa_enabled == 1) {
                $is_enabled = true;
            }

            echo json_encode([
                'status' => 'success',
                'is_enabled' => $is_enabled,
                'has_secret' => (!empty($user_2fa->google2fa_secret) ? true : false),
                'setup_date' => ($user_2fa && $user_2fa->google2fa_setup_date ? $user_2fa->google2fa_setup_date : null)
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

}