<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * Controller สำหรับจัดการการเข้าสู่ระบบผ่าน Token (SSO)
 * เรียกใช้ Auth_api เพื่อตรวจสอบและสร้าง Session
 */
class Auth_public_login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // โหลด model ที่จำเป็น
        $this->load->model('member_public_model');
        $this->load->model('tenant_access_model');
        $this->load->model('user_log_model');

        $this->load->helper('cookie');
        $this->load->helper('url');
    }

    /**
     * ตรวจสอบ Token และล็อกอินอัตโนมัติ
     */
    public function index()
    {
        // ตรวจสอบว่ามี token ถูกส่งมาหรือไม่
        $token = $this->input->get('token');

        if (empty($token)) {
            show_error('ไม่พบ Token หรือ Token ไม่ถูกต้อง', 403, 'เข้าสู่ระบบล้มเหลว');
            return;
        }

        // ทำความสะอาด tokens ที่หมดอายุก่อน
        $this->cleanup_tokens();

        // เรียกใช้ฟังก์ชันตรวจสอบ Token
        $token_data = $this->validate_token($token);

        if (!$token_data) {
            show_error('Token ไม่ถูกต้องหรือหมดอายุ กรุณาเข้าสู่ระบบอีกครั้ง', 403, 'เข้าสู่ระบบล้มเหลว');
            return;
        }

        // ตรวจสอบข้อมูลผู้ใช้จาก Token
        $user_data = $this->get_user_data_from_token($token_data);

        if (!$user_data) {
            show_error('ไม่พบข้อมูลผู้ใช้ในระบบ กรุณาเข้าสู่ระบบอีกครั้ง', 403, 'เข้าสู่ระบบล้มเหลว');
            return;
        }

        // สร้าง Session และล็อกอินอัตโนมัติ
        $this->create_session_from_token($user_data);

        // อัปเดตเวลาหมดอายุของ Token
        $this->update_token_expiry($token);

        // บันทึก Log การเข้าสู่ระบบ
        $this->log_sso_login($user_data);

        // สร้าง flashdata แสดงข้อความต้อนรับ
        $this->session->set_flashdata('login_success', TRUE);
        $this->session->set_flashdata('sso_login', TRUE);

        // Redirect ไปยังหน้าหลักของระบบ
        redirect('Pages/service_systems');
    }

    /**
     * ตรวจสอบความถูกต้องของ Token
     * @param string $token Token ที่ต้องการตรวจสอบ
     * @return object|bool ข้อมูล Token หรือ false ถ้าไม่ถูกต้อง
     */
    private function validate_token($token)
    {
        // ตรวจสอบว่า Token มีอยู่ในฐานข้อมูลหรือไม่
        $this->db->where('token', $token);
        $this->db->where('expires_at >', date('Y-m-d H:i:s')); // ยังไม่หมดอายุ
        $token_data = $this->db->get('auth_tokens')->row();

        // ถ้าไม่พบ Token หรือ Token หมดอายุ
        if (!$token_data) {
            return false;
        }

        return $token_data;
    }

    /**
     * ดึงข้อมูลผู้ใช้จาก Token
     * @param object $token_data ข้อมูล Token
     * @return object|bool ข้อมูลผู้ใช้หรือ false ถ้าไม่พบ
     */
    private function get_user_data_from_token($token_data)
    {
        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $user_data = $this->member_public_model->get_member_by_id($token_data->user_id);

        if (!$user_data) {
            return false;
        }

        // ตรวจสอบสถานะผู้ใช้ (ถ้ามี field mp_status)
        if (isset($user_data->mp_status) && $user_data->mp_status == 0) {
            return false;
        }

        return $user_data;
    }

    /**
     * สร้าง Session จากข้อมูล Token
     * @param object $user_data ข้อมูลผู้ใช้
     */
    private function create_session_from_token($user_data)
    {
        // ดึงข้อมูล tenant
        $tenant_id = $this->input->get('tenant_id');
        $tenant_code = $this->input->get('tenant_code');
        $tenant_domain = $this->input->get('tenant_domain', TRUE) ?: $_SERVER['HTTP_HOST'];

        // ใช้ข้อมูลผู้ใช้สร้าง Session แบบเดียวกับที่ใช้ใน Auth_api.php
        $sess = array(
            'mp_id' => $user_data->mp_id,
            'mp_email' => $user_data->mp_email,
            'mp_fname' => $user_data->mp_fname,
            'mp_lname' => $user_data->mp_lname,
            'mp_img' => isset($user_data->mp_img) ? $user_data->mp_img : null,
            'mp_phone' => isset($user_data->mp_phone) ? $user_data->mp_phone : null,
            'mp_number' => isset($user_data->mp_number) ? $user_data->mp_number : null,
            'is_public' => true,
            'is_sso_login' => true, // Flag บ่งชี้ว่าล็อกอินผ่าน SSO
            'tenant_id' => $tenant_id,
            'tenant_code' => $tenant_code,
            'tenant_name' => $this->input->get('tenant_name', TRUE),
            'tenant_domain' => $tenant_domain,
            'module_code' => $this->input->get('module_code') // รหัสโมดูลที่กำลังเข้าใช้งาน
        );

        // ตั้งค่า Session
        $this->session->set_userdata($sess);
    }

    /**
     * อัปเดตเวลาหมดอายุของ Token
     * @param string $token Token ที่ต้องการอัปเดต
     */
    private function update_token_expiry($token)
    {
        // อัปเดตเวลาหมดอายุของ Token เป็นอีก 15 นาที
        $this->db->where('token', $token);
        $this->db->update('auth_tokens', [
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
        ]);
    }

    /**
     * บันทึก Log การเข้าสู่ระบบผ่าน SSO
     * @param object $user_data ข้อมูลผู้ใช้
     */
    private function log_sso_login($user_data)
    {
        // ทำในรูปแบบเดียวกับ Auth_api.php
        $log_data = array(
            'username' => $user_data->mp_email,
            'status' => 'success',
            'attempt_time' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address(),
            'fingerprint' => md5($this->input->ip_address() . $_SERVER['HTTP_USER_AGENT'])
        );

        // บันทึกลงตาราง tbl_member_login_attempts ตาม Auth_api.php
        $this->db->insert('tbl_member_login_attempts', $log_data);

        // บันทึก log กิจกรรม
        $this->user_log_model->log_activity(
            $user_data->mp_email,
            'sso_login',
            'ประชาชนเข้าสู่ระบบผ่าน SSO จาก ' . $this->input->get('tenant_code'),
            'auth'
        );
    }

    /**
     * ทำความสะอาด tokens ที่หมดอายุ
     */
    private function cleanup_tokens()
    {
        // ลบ token ที่หมดอายุ
        $this->db->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete('auth_tokens');

        // ลบ token ที่มีข้อมูลไม่สมบูรณ์
        $this->db->where('tenant_id IS NULL')
            ->or_where('tenant_code IS NULL')
            ->or_where('tenant_code', '')
            ->delete('auth_tokens');
    }

    /**
     * ทดสอบการทำงานของระบบ SSO
     */
    public function test()
    {
        echo '<h1>ทดสอบระบบ SSO</h1>';
        echo '<p>เวลาปัจจุบัน: ' . date('Y-m-d H:i:s') . '</p>';

        echo '<h2>ข้อมูล GET:</h2>';
        echo '<pre>';
        print_r($_GET);
        echo '</pre>';

        echo '<h2>ข้อมูล Session:</h2>';
        echo '<pre>';
        print_r($this->session->userdata());
        echo '</pre>';

        // แสดงรายการ token ที่เพิ่งสร้าง
        echo '<h2>ข้อมูล Token ล่าสุด:</h2>';
        $tokens = $this->db->order_by('created_at', 'DESC')
            ->limit(5)
            ->get('auth_tokens')
            ->result();

        echo '<pre>';
        foreach ($tokens as $token) {
            echo "Token: {$token->token}\n";
            echo "User ID: {$token->user_id}\n";
            echo "User Type: {$token->user_type}\n";
            echo "Expires At: {$token->expires_at}\n";
            echo "Created At: {$token->created_at}\n";
            echo "-----------------------\n";
        }
        echo '</pre>';
    }
}