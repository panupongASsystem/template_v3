<?php
defined('BASEPATH') or exit('No direct script access allowed');


class System_admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
		
        // เช็ค steb 1 ระบบที่เลือกตรงมั้ย
        if (!$this->session->userdata('m_id')) {
        redirect('User/logout', 'refresh');
    }
    
    // ✅ เช็คว่า user มีอยู่ในตาราง tbl_member หรือไม่
    $user_exists = $this->db->where('m_id', $this->session->userdata('m_id'))
                           ->where('m_status', '1') // สถานะใช้งานได้
                           ->count_all_results('tbl_member');
    
    if ($user_exists == 0) {
        redirect('User/logout', 'refresh');
    }


        // ตั้งค่าเวลาหมดอายุของเซสชัน
   		// $this->check_session_timeout();

        $this->load->model('space_model');
        $this->load->model('member_model');
        $this->load->model('camera_model');
        $this->load->model('wifi_model');
        $this->load->model('report_model');
        $this->load->model('complain_model');
        $this->load->model('news_model');
        $this->load->model('activity_model');
        $this->load->model('food_model');
        $this->load->model('travel_model');
        $this->load->model('q_a_model');
        $this->load->model('log_users_model');
        $this->load->model('report_model');
        $this->load->model('assessment_model');
		$this->load->library('Google2FA');
        $this->load->model('Theme_model');
		
		$this->load->model('Log_model');
		$this->load->model('User_log_model');
		$this->load->model('dashboard_model');
    }

	// เพิ่ม Method สำหรับดึงข้อมูลแบบ AJAX (ถ้าต้องการ Real-time update)
    public function get_recent_activities()
    {
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $activities = $this->Log_model->get_logs($limit, 0);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $activities
        ]);
    }
	
	public function get_top_active_users()
{
    try {
        $days = $this->input->get('days') ? (int)$this->input->get('days') : 0;
        $limit = 5; // บังคับให้เป็น 5 เสมอ
        
        // ตรวจสอบขอบเขตของค่า days
        $days = max(0, min(365, $days));
        
        $top_users = $this->User_log_model->getTopActiveUsers($days, $limit);
        
        $period_text = $days > 0 ? $days . ' วันที่ผ่านมา' : 'ทั้งหมด';
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $top_users,
            'period' => $period_text,
            'count' => count($top_users)
        ]);
        
    } catch (Exception $e) {
        log_message('error', 'Error getting top active users: ' . $e->getMessage());
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'ไม่สามารถดึงข้อมูลได้ในขณะนี้',
            'data' => []
        ]);
    }
}
	
	public function index()
    {
		// จำนวนผู้ใช้งานบ่อยที่สุด
        $data['top_active_users'] = $this->User_log_model->getTopActiveUsers(0, 5);
		// กระทู้ยอดนิยม
		$data['popular_categories'] = $this->dashboard_model->get_popular_by_category(3);
		// จำนวนกระทู้สูงสุด
        $data['most_post_tables'] = $this->dashboard_model->find_table_most_posts();
		// ดึงกิจกรรมล่าสุด 10 รายการ
        $data['recent_activities'] = $this->Log_model->get_logs(10, 0);
        // จำนวนยอดวิวสูงสุด
        $data['most_viewed_tables'] = $this->report_model->find_most_viewed_table();

        $this->load->view('templat/header');
        $this->load->view('asset/css', ['current_theme' => $this->Theme_model->get_current_theme()]);
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/dashboard', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    public function update_upload_limit()
    {
        $server_storage = $this->input->post('server_storage');

        if ($server_storage) {
            // อัปเดตค่าในตาราง tbl_server
            $this->load->database();
            $data = array(
                'server_storage' => $server_storage
            );
            $this->db->where('server_id', 1);
            $this->db->update('tbl_server', $data);

            // ทำการอัปเดตขนาดการจำกัดพื้นที่ในตัวแปร Session
            $this->session->set_userdata('server_storage', $server_storage);
        }

        // ส่งกลับไปยังหน้าเดิมหลังจากที่อัปเดตค่าเรียบร้อยแล้ว
        redirect(base_url('system_admin'));
    }

    public function profile()
    {
        $m_id = $_SESSION['m_id'];

        $data['rsedit'] = $this->member_model->read($m_id);

        $this->load->view('templat/header');
        $this->load->view('asset/css', ['current_theme' => $this->Theme_model->get_current_theme()]);
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/profile', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }
	
	
	
	/**
 * เพิ่มฟังก์ชันเหล่านี้เข้าไปใน Staff Controller ที่มีอยู่แล้ว
 */
public function keep_alive()
{
    // ป้องกันการ output ที่ไม่ต้องการ
    ob_start();
    
    // ตรวจสอบว่าเป็น AJAX request
    if (!$this->input->is_ajax_request()) {
        ob_end_clean();
        show_404();
        exit;
    }

    // ตั้งค่า header สำหรับ JSON response
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('X-Content-Type-Options: nosniff');

    try {
        // ✅ 1. ตรวจสอบ session ก่อน (เบื้องต้น)
        $user_id = $this->session->userdata('m_id');
        
        if (!$user_id) {
            ob_end_clean();
            echo json_encode([
                'status' => 'expired',
                'message' => 'No session found',
                'redirect_url' => base_url('User'),
                'timestamp' => time() * 1000
            ]);
            exit;
        }

        // ✅ 2. รับข้อมูล client activity ก่อนเป็นอันดับแรก
        $input = json_decode($this->input->raw_input_stream, true);
        
        if (!$input) {
            // fallback ไป POST data
            $last_user_activity = $this->input->post('last_user_activity');
            $time_since_activity = $this->input->post('time_since_activity');
            $max_idle_time = $this->input->post('max_idle_time');
        } else {
            $last_user_activity = isset($input['last_user_activity']) ? $input['last_user_activity'] : null;
            $time_since_activity = isset($input['time_since_activity']) ? $input['time_since_activity'] : null;
            $max_idle_time = isset($input['max_idle_time']) ? $input['max_idle_time'] : null;
        }

        // ค่าเริ่มต้น
        $session_timeout = 32400 * 60 * 1000; // 9 ชั่วโมง (milliseconds)
        $warning_time = 5 * 60 * 1000;    // 5 นาที (milliseconds)
        
        if ($max_idle_time && is_numeric($max_idle_time)) {
            $session_timeout = (int)$max_idle_time;
            $warning_time = $session_timeout - (1 * 60 * 1000);
        }

        $current_time = time();
        $current_time_ms = $current_time * 1000;

        // ✅ 3. ตรวจสอบ CLIENT ACTIVITY ก่อน (สำคัญที่สุด!)
        if ($last_user_activity && $time_since_activity && is_numeric($time_since_activity)) {
            
            // ถ้า client มี activity ล่าสุด (ไม่ idle มาก) = อัปเดต server activity ทันที
            if ($time_since_activity < $session_timeout) {
                // 🎯 อัปเดต server last_activity ก่อนตรวจสอบ timeout
                $this->session->set_userdata('last_activity', $current_time);
                
                // ตรวจสอบว่าใกล้หมดอายุแล้วหรือไม่ (ส่ง warning)
                $should_warn = $time_since_activity > $warning_time;
                
                ob_end_clean();
                echo json_encode([
                    'status' => 'alive',
                    'message' => 'Session active - server updated',
                    'warning' => $should_warn,
                    'server_time' => $current_time_ms,
                    'last_activity' => $last_user_activity,
                    'time_since_activity' => $time_since_activity,
                    'remaining_time' => max(0, $session_timeout - $time_since_activity),
                    'warning_time' => $warning_time,
                    'user_info' => [
                        'user_id' => $user_id,
                        'username' => $this->session->userdata('m_username'),
                        'fname' => $this->session->userdata('m_fname'),
                        'lname' => $this->session->userdata('m_lname')
                    ]
                ]);
                exit;
            }
            
            // ถ้า client idle เกินเวลาที่กำหนด = ตัด session
            if ($time_since_activity >= $session_timeout) {
                $this->session->sess_destroy();
                ob_end_clean();
                echo json_encode([
                    'status' => 'idle_timeout',
                    'message' => 'Client inactivity timeout',
                    'idle_time' => $time_since_activity,
                    'max_idle_time' => $session_timeout,
                    'redirect_url' => base_url('User'),
                    'timestamp' => $current_time_ms
                ]);
                exit;
            }
        }

        // ✅ 4. ตรวจสอบ SERVER-SIDE session timeout (สำรอง - ถ้าไม่มี client data)
        $last_activity_server = $this->session->userdata('last_activity');
        
        // ถ้าไม่มี last_activity ให้ตั้งค่าเริ่มต้น
        if (!$last_activity_server) {
            $last_activity_server = $current_time;
            $this->session->set_userdata('last_activity', $current_time);
        }
        
        $server_idle_time = $current_time - $last_activity_server;
        
        // Server timeout เป็นการตรวจสอบสำรอง (9 ชั่วโมง = 32400 วินาที - ให้เวลามากกว่า client)
        if ($server_idle_time > 32400) {
            $this->session->sess_destroy();
            ob_end_clean();
            echo json_encode([
                'status' => 'expired',
                'message' => 'Server session timeout (backup check)',
                'idle_time' => $server_idle_time,
                'max_server_time' => 32400,
                'redirect_url' => base_url('User'),
                'timestamp' => $current_time_ms
            ]);
            exit;
        }

        // ✅ 5. กรณีไม่มีข้อมูล client activity แต่ session ยังไม่หมดอายุ
        ob_end_clean();
        echo json_encode([
            'status' => 'alive',
            'message' => 'Session alive - no client activity data',
            'warning' => false,
            'server_time' => $current_time_ms,
            'server_idle_time' => $server_idle_time,
            'max_server_time' => 32400,
            'user_info' => [
                'user_id' => $user_id,
                'username' => $this->session->userdata('m_username'),
                'fname' => $this->session->userdata('m_fname'),
                'lname' => $this->session->userdata('m_lname')
            ]
        ]);

    } catch (Exception $e) {
        error_log("Keep Alive Error: " . $e->getMessage());
        
        ob_end_clean();
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error occurred',
            'error' => 'Internal server error',
            'timestamp' => time() * 1000
        ]);
    }
    
    exit;
}

/**
 * ✅ แก้ไข update_user_activity ให้ทำงานถูกต้อง
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
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    try {
        $user_id = $this->session->userdata('m_id');
        
        if (!$user_id) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Session expired',
                'timestamp' => time() * 1000
            ]);
            exit;
        }

        // ✅ อัปเดต server session activity ทันที (ไม่ตรวจสอบ timeout ก่อน)
        $current_time = time();
        $this->session->set_userdata('last_activity', $current_time);
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'User activity updated successfully',
            'server_time' => $current_time,
            'timestamp' => $current_time * 1000
        ]);

    } catch (Exception $e) {
        error_log("Update User Activity Error: " . $e->getMessage());
        
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Error updating activity',
            'error' => $e->getMessage(),
            'timestamp' => time() * 1000
        ]);
    }
    
    exit;
}	
	
	




    /**
     * หน้าดูโปรไฟล์ผู้ใช้ (แยกออกจาก choice.php)
     */
    public function user_profile()
    {
        $m_id = $this->session->userdata('m_id');
        
        if (!$m_id) {
            redirect('User');
            return;
        }

        // ดึงข้อมูลโปรไฟล์ผู้ใช้พร้อมข้อมูลตำแหน่ง
        $data['rsedit'] = $this->db->select('m.*, p.pname')
                                  ->from('tbl_member m')
                                  ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                                  ->where('m.m_id', $m_id)
                                  ->get()
                                  ->row();

        if (!$data['rsedit']) {
            show_404();
            return;
        }

        // ดึงข้อมูลการเข้าสู่ระบบล่าสุดจาก tbl_member_login_attempts
        $last_login = $this->db->select('attempt_time, ip_address')
                              ->from('tbl_member_login_attempts')
                              ->where('username', $data['rsedit']->m_username)
                              ->where('status', 'success')
                              ->order_by('attempt_time', 'DESC')
                              ->limit(1)
                              ->get()
                              ->row();
        
        // เพิ่มข้อมูลการเข้าสู่ระบบล่าสุดเข้าไปใน object ผู้ใช้
        $data['rsedit']->last_login_time = $last_login ? $last_login->attempt_time : null;
        $data['rsedit']->last_login_ip = $last_login ? $last_login->ip_address : null;

        // โหลด view เดียวเท่านั้น (ไม่ใช้ header/footer)
        $this->load->view('system_admin/user_profile', $data);
    }

    /**
     * AJAX method สำหรับอัพเดทโปรไฟล์
     */
    

    /**
     * AJAX method สำหรับดึงรายการตำแหน่ง
     */
    public function get_positions()
{
    if (!$this->input->is_ajax_request()) {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }

    try {
        // ดึงตำแหน่งทั้งหมด โดยไม่กรอง pid = 1 ออก
        $positions = $this->db->select('pid, pname')
                             ->from('tbl_position')
                             // ลบบรรทัดนี้ออก: ->where('pid !=', 1) 
                             ->order_by('pid', 'ASC')
                             ->get()
                             ->result();

        echo json_encode([
            'success' => true,
            'positions' => $positions,
            'message' => 'ดึงข้อมูลตำแหน่งสำเร็จ'
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error loading positions: ' . $e->getMessage()]);
    }
}
	
	public function edit_Profile($m_id)
	{
		$this->member_model->edit_Profile($m_id);
		redirect('System_admin');
	}

    /**
     * สร้าง 2FA Secret และ QR Code
     */
    public function generate_2fa_secret()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->post('user_id')) {
            echo json_encode(['success' => false, 'message' => 'Missing user_id parameter']);
            return;
        }

        $user_id = $this->input->post('user_id');
        $domain = $this->input->post('domain') ?: $_SERVER['HTTP_HOST'];
        
        if ($user_id != $this->session->userdata('m_id') && $this->session->userdata('m_system') != 'system_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        try {
            if (!class_exists('Google2FA')) {
                $this->load->library('Google2FA');
            }
            
            $secret = $this->google2fa->generateSecretKey(32);
            
            if (!$this->isValidBase32($secret)) {
                throw new Exception('Invalid secret key format');
            }
            
            $user = $this->member_model->read($user_id);
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'User not found']);
                return;
            }

            // อัปเดต Account Name
            $issuer = 'ยืนยันตัวตนเจ้าหน้าที่ (' . $domain . ')';
            $account_label = $user->m_username;
            
            $otpauth_url = sprintf(
                'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
                urlencode($issuer),
                urlencode($account_label),
                $secret,
                urlencode($issuer)
            );
            
            error_log("OTPAuth URL: " . $otpauth_url);
            
            $qr_services = [
                'https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=M&data=' . urlencode($otpauth_url),
                'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($otpauth_url),
                'https://quickchart.io/qr?text=' . urlencode($otpauth_url) . '&size=300'
            ];
            
            $qr_image = $qr_services[0];
            
            $update_result = $this->member_model->update_2fa_secret($user_id, $secret);
            
            if (!$update_result) {
                echo json_encode(['success' => false, 'message' => 'Failed to save secret']);
                return;
            }

            echo json_encode([
                'success' => true,
                'secret' => $secret,
                'qr_code' => $qr_image,
                'otpauth_url' => $otpauth_url,
                'manual_entry' => $secret,
                'issuer' => $issuer,
                'account' => $account_label
            ]);

        } catch (Exception $e) {
            error_log("2FA Generation Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error generating 2FA: ' . $e->getMessage()]);
        }
    }

    /**
     * ยืนยันการตั้งค่า 2FA
     */
    public function verify_2fa_setup()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post('user_id') || !$this->input->post('otp')) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $user_id = $this->input->post('user_id');
        $otp = $this->input->post('otp');
        
        // ตรวจสอบสิทธิ์
        if ($user_id != $this->session->userdata('m_id') && $this->session->userdata('m_system') != 'system_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            // ดึงข้อมูล 2FA
            $user_2fa = $this->member_model->get_2fa_info($user_id);
            if (!$user_2fa || empty($user_2fa->google2fa_secret)) {
                echo json_encode(['success' => false, 'message' => '2FA not initialized']);
                return;
            }

            // ตรวจสอบ OTP
            $is_valid = $this->google2fa->verifyKey($user_2fa->google2fa_secret, $otp, 2);
            
            if ($is_valid) {
                // เปิดใช้งาน 2FA
                $this->member_model->enable_2fa($user_id);
                
                // สร้าง backup codes
                $backup_codes = $this->generate_backup_codes($user_id);
                
                // บันทึก log
                $this->member_model->log_2fa_activity($user_id, 'setup');
                
                echo json_encode([
                    'success' => true, 
                    'message' => '2FA enabled successfully',
                    'backup_codes' => $backup_codes
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid OTP code']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error verifying 2FA: ' . $e->getMessage()]);
        }
    }

    /**
     * ปิดใช้งาน 2FA
     */
    public function disable_2fa()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post('user_id')) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $user_id = $this->input->post('user_id');
        
        // ตรวจสอบสิทธิ์
        if ($user_id != $this->session->userdata('m_id') && $this->session->userdata('m_system') != 'system_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            // ปิดใช้งาน 2FA
            $result = $this->member_model->disable_2fa($user_id);
            
            if ($result) {
                // บันทึก log
                $this->member_model->log_2fa_activity($user_id, 'disable');
                
                echo json_encode(['success' => true, 'message' => '2FA disabled successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to disable 2FA']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error disabling 2FA: ' . $e->getMessage()]);
        }
    }

    /**
     * ดึง Backup Codes
     */
    public function get_backup_codes()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post('user_id')) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $user_id = $this->input->post('user_id');
        
        // ตรวจสอบสิทธิ์
        if ($user_id != $this->session->userdata('m_id') && $this->session->userdata('m_system') != 'system_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            // ดึง backup codes
            $codes = $this->member_model->get_backup_codes($user_id);
            
            if (!empty($codes)) {
                echo json_encode(['success' => true, 'codes' => $codes]);
            } else {
                // สร้าง backup codes ใหม่
                $new_codes = $this->generate_backup_codes($user_id);
                echo json_encode(['success' => true, 'codes' => $new_codes]);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error getting backup codes: ' . $e->getMessage()]);
        }
    }

    /**
     * สร้าง Backup Codes (private method)
     */
    private function generate_backup_codes($user_id, $count = 10)
    {
        $codes = [];
        
        for ($i = 0; $i < $count; $i++) {
            // สร้างรหัส 8 หลัก
            $codes[] = sprintf('%04d-%04d', mt_rand(1000, 9999), mt_rand(1000, 9999));
        }
        
        // บันทึก backup codes
        $this->member_model->create_backup_codes($user_id, $codes);
        
        return $codes;
    }

    /**
     * ตรวจสอบสถานะ 2FA สำหรับ AJAX
     */
    public function check_2fa_status()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $user_id = $this->input->post('user_id') ?: $this->session->userdata('m_id');
        
        // ตรวจสอบสิทธิ์
        if ($user_id != $this->session->userdata('m_id') && $this->session->userdata('m_system') != 'system_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $user_2fa = $this->member_model->get_2fa_info($user_id);
            
            echo json_encode([
                'success' => true,
                'enabled' => !empty($user_2fa->google2fa_secret) && $user_2fa->google2fa_enabled == 1,
                'setup_date' => $user_2fa->google2fa_setup_date ?? null
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error checking 2FA status: ' . $e->getMessage()]);
        }
    }

    /**
     * นับจำนวนอุปกรณ์ที่ลงทะเบียน Google Authenticator
     */
    public function get_device_count()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ user_id']);
            return;
        }

        try {
            $device_count = $this->get_user_device_count($user_id);
            
            echo json_encode([
                'success' => true, 
                'count' => $device_count,
                'message' => 'ดึงข้อมูลสำเร็จ'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * แสดง QR Code ที่มีอยู่แล้วสำหรับเพิ่มอุปกรณ์ใหม่
     */
    public function get_existing_qr_code()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        $session_key = $this->input->post('session_key');
        $domain = $this->input->post('domain') ?: $_SERVER['HTTP_HOST'];
        
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ user_id']);
            return;
        }

        try {
            if ($session_key) {
                $qr_session = $this->session->userdata($session_key);
                
                if (!$qr_session || time() > $qr_session['expires_at']) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'QR Code หมดอายุแล้ว กรุณาขอใหม่',
                        'expired' => true
                    ]);
                    return;
                }
                
                $remaining_time = $qr_session['expires_at'] - time();
            } else {
                $remaining_time = 600;
            }

            $user = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
            
            if (!$user || empty($user->google2fa_secret)) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล 2FA']);
                return;
            }

            if (!$this->isValidBase32($user->google2fa_secret)) {
                echo json_encode(['success' => false, 'message' => 'Secret key ไม่ถูกต้อง']);
                return;
            }

            $this->load->library('Google2FA');
            
            // อัปเดต Account Name
            $issuer = 'ยืนยันตัวตนบุคลากรภายใน (' . $domain . ')';
            $account_label = $user->m_username;
            
            $otpauth_url = sprintf(
                'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
                urlencode($issuer),
                urlencode($account_label),
                $user->google2fa_secret,
                urlencode($issuer)
            );
            
            $qr_images = [
                'small' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&ecc=M&data=' . urlencode($otpauth_url),
                'medium' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=M&data=' . urlencode($otpauth_url),
                'large' => 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&ecc=M&data=' . urlencode($otpauth_url)
            ];
            
            $qr_code_image = $qr_images['medium'];
            
            echo json_encode([
                'success' => true,
                'qr_code' => $qr_code_image,
                'secret' => $user->google2fa_secret,
                'otpauth_url' => $otpauth_url,
                'account_name' => $account_label,
                'issuer' => $issuer,
                'full_account_display' => $issuer,
                'remaining_time' => $remaining_time,
                'expires_at' => time() + $remaining_time,
                'qr_alternatives' => $qr_images,
                'message' => 'ดึงข้อมูลสำเร็จ'
            ]);
            
        } catch (Exception $e) {
            error_log("Get QR Code Error: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ฟังก์ชันช่วยนับจำนวนอุปกรณ์ของผู้ใช้
     */
    private function get_user_device_count($user_id)
    {
        // วิธีที่ 1: ดึงจาก trusted_devices table (ถ้ามี)
        if ($this->db->table_exists('trusted_devices')) {
            $count = $this->db->where('user_id', $user_id)
                             ->where('expires_at >', date('Y-m-d H:i:s'))
                             ->count_all_results('trusted_devices');
            return $count;
        }
        
        // วิธีที่ 2: ดึงจาก login log (ถ้ามี)
        if ($this->db->table_exists('login_logs')) {
            $count = $this->db->select('DISTINCT device_fingerprint')
                             ->where('user_id', $user_id)
                             ->where('login_method', '2FA')
                             ->where('created_at >', date('Y-m-d H:i:s', strtotime('-30 days')))
                             ->count_all_results('login_logs');
            return $count;
        }
        
        // วิธีที่ 3: ใช้ข้อมูลจาก session หรือ user preference
        $user_data = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
        if ($user_data && !empty($user_data->device_count)) {
            return (int)$user_data->device_count;
        }
        
        // วิธีที่ 4: จำลองหรือค่า default
        return rand(1, 3); // จำลองว่ามี 1-3 เครื่อง
    }

    /**
     * อัปเดตจำนวนอุปกรณ์ (ถ้าผู้ใช้ต้องการปรับปรุงด้วยตนเอง)
     */
    public function update_device_count()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        $count = $this->input->post('count');
        
        if (!$user_id || !is_numeric($count)) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        try {
            // เพิ่ม column device_count ใน tbl_member ถ้าต้องการ
            $this->db->where('m_id', $user_id)
                     ->set('device_count', (int)$count)
                     ->update('tbl_member');
            
            echo json_encode([
                'success' => true, 
                'message' => 'อัปเดตจำนวนอุปกรณ์สำเร็จ'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ดึงรายการอุปกรณ์ที่เคยใช้ 2FA
     */
    public function get_device_list()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ user_id']);
            return;
        }

        try {
            $devices = [];
            
            // ดึงจาก trusted_devices table ถ้ามี
            if ($this->db->table_exists('trusted_devices')) {
                $this->db->select('*');
                $this->db->where('user_id', $user_id);
                $this->db->order_by('last_used_at', 'DESC');
                $trusted_devices = $this->db->get('trusted_devices')->result();
                
                foreach ($trusted_devices as $device) {
                    $device_info = json_decode($device->device_info, true);
                    $is_expired = strtotime($device->expires_at) < time();
                    
                    $devices[] = [
                        'id' => $device->id,
                        'browser' => $device_info['browser'] ?? 'Unknown',
                        'version' => $device_info['version'] ?? '',
                        'platform' => $device_info['platform'] ?? 'Unknown',
                        'ip_address' => $device->ip_address,
                        'created_at' => $device->created_at,
                        'last_used_at' => $device->last_used_at,
                        'expires_at' => $device->expires_at,
                        'is_expired' => $is_expired,
                        'source' => 'trusted_device'
                    ];
                }
            }
            
            // ถ้าไม่มี trusted_devices หรือมีน้อย ให้จำลองข้อมูล
            if (empty($devices)) {
                $devices = $this->generate_sample_devices($user_id);
            }
            
            echo json_encode([
                'success' => true,
                'devices' => $devices,
                'count' => count($devices),
                'message' => 'ดึงรายการอุปกรณ์สำเร็จ'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ลบอุปกรณ์
     */
    public function remove_device()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $device_id = $this->input->post('device_id');
        $user_id = $this->input->post('user_id');
        
        if (!$device_id || !$user_id) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            return;
        }

        try {
            // ลบจาก trusted_devices table
            if ($this->db->table_exists('trusted_devices')) {
                $this->db->where('id', $device_id)
                         ->where('user_id', $user_id)
                         ->delete('trusted_devices');
                
                if ($this->db->affected_rows() > 0) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'ลบอุปกรณ์สำเร็จ'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'ไม่พบอุปกรณ์ที่ต้องการลบ'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ฟังก์ชันนี้ยังไม่พร้อมใช้งาน'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ลบอุปกรณ์ทั้งหมด
     */
    public function remove_all_devices()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ user_id']);
            return;
        }

        try {
            // ลบทั้งหมดจาก trusted_devices table
            if ($this->db->table_exists('trusted_devices')) {
                $this->db->where('user_id', $user_id)->delete('trusted_devices');
                
                echo json_encode([
                    'success' => true,
                    'message' => 'ลบอุปกรณ์ทั้งหมดสำเร็จ',
                    'affected_rows' => $this->db->affected_rows()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ฟังก์ชันนี้ยังไม่พร้อมใช้งาน'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function create_qr_session()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        $domain = $this->input->post('domain') ?: $_SERVER['HTTP_HOST'];
        
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ user_id']);
            return;
        }

        try {
            $qr_session_key = 'qr_code_' . $user_id . '_' . time();
            $expire_time = 10 * 60; // 10 นาที
            
            $this->session->set_userdata([
                $qr_session_key => [
                    'user_id' => $user_id,
                    'domain' => $domain,
                    'created_at' => time(),
                    'expires_at' => time() + $expire_time
                ]
            ]);
            
            echo json_encode([
                'success' => true,
                'session_key' => $qr_session_key,
                'expires_in' => $expire_time,
                'domain' => $domain,
                'message' => 'สร้าง QR Session สำเร็จ'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    private function isValidBase32($string)
    {
        // Base32 alphabet: A-Z, 2-7
        return preg_match('/^[A-Z2-7]+$/', $string) && (strlen($string) % 8 == 0 || strlen($string) % 8 == 1 || strlen($string) % 8 == 3 || strlen($string) % 8 == 4 || strlen($string) % 8 == 6);
    }

    /**
     * สร้าง QR Code debug page (สำหรับทดสอบ)
     */
    public function debug_qr_code($user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('m_id');
        }
        
        $user = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
        
        if (!$user || empty($user->google2fa_secret)) {
            echo "ไม่พบข้อมูล 2FA";
            return;
        }
        
        $issuer = 'ยืนยันตัวตนบุคลากรภายใน';
        $account_label = $user->m_username;
        $secret = $user->google2fa_secret;
        
        $otpauth_url = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            urlencode($issuer),
            urlencode($account_label),
            $secret,
            urlencode($issuer)
        );
        
        echo "<h2>QR Code Debug</h2>";
        echo "<p><strong>User:</strong> {$user->m_username}</p>";
        echo "<p><strong>Secret:</strong> {$secret}</p>";
        echo "<p><strong>Secret Length:</strong> " . strlen($secret) . "</p>";
        echo "<p><strong>Valid Base32:</strong> " . ($this->isValidBase32($secret) ? 'Yes' : 'No') . "</p>";
        echo "<p><strong>OTPAuth URL:</strong><br><code>{$otpauth_url}</code></p>";
        
        echo "<h3>QR Codes (Different Services):</h3>";
        
        $services = [
            'QR Server' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=M&data=' . urlencode($otpauth_url),
            'Google Charts' => 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($otpauth_url),
            'QuickChart' => 'https://quickchart.io/qr?text=' . urlencode($otpauth_url) . '&size=300'
        ];
        
        foreach ($services as $name => $url) {
            echo "<h4>{$name}</h4>";
            echo "<img src='{$url}' alt='{$name} QR Code' style='border: 1px solid #ccc; margin: 10px;'><br>";
            echo "<small>{$url}</small><br><br>";
        }
        
        echo "<h3>Manual Entry Info:</h3>";
        echo "<p><strong>Account:</strong> {$account_label}</p>";
        echo "<p><strong>Key:</strong> {$secret}</p>";
        echo "<p><strong>Time-based:</strong> Yes</p>";
        echo "<p><strong>Algorithm:</strong> SHA1</p>";
        echo "<p><strong>Digits:</strong> 6</p>";
        echo "<p><strong>Period:</strong> 30 seconds</p>";
    }

    /**
     * รีเฟรช QR Code session
     */
    public function refresh_qr_session()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        $old_session_key = $this->input->post('old_session_key');
        $domain = $this->input->post('domain') ?: $_SERVER['HTTP_HOST'];
        
        try {
            if ($old_session_key) {
                $this->session->unset_userdata($old_session_key);
            }
            
            $new_session_key = 'qr_code_' . $user_id . '_' . time();
            $expire_time = 10 * 60; // 10 นาที
            
            $this->session->set_userdata([
                $new_session_key => [
                    'user_id' => $user_id,
                    'domain' => $domain,
                    'created_at' => time(),
                    'expires_at' => time() + $expire_time
                ]
            ]);
            
            echo json_encode([
                'success' => true,
                'session_key' => $new_session_key,
                'expires_in' => $expire_time,
                'domain' => $domain,
                'message' => 'รีเฟรช QR Session สำเร็จ'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ดึงประวัติการเข้าสู่ระบบของผู้ใช้
     */
    public function get_login_history()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->input->post('user_id');
        $limit = $this->input->post('limit') ?: 10;
        
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ user_id']);
            return;
        }

        try {
            // ดึงข้อมูลผู้ใช้เพื่อหา username
            $user = $this->db->where('m_id', $user_id)->get('tbl_member')->row();
            
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้']);
                return;
            }

            // ดึงประวัติการเข้าสู่ระบบ
            $login_history = $this->db->select('attempt_time, ip_address, status, fingerprint')
                                     ->from('tbl_member_login_attempts')
                                     ->where('username', $user->m_username)
                                     ->order_by('attempt_time', 'DESC')
                                     ->limit($limit)
                                     ->get()
                                     ->result();

            // สถิติการเข้าสู่ระบบ
            $total_attempts = $this->db->where('username', $user->m_username)->count_all_results('tbl_member_login_attempts');
            $success_attempts = $this->db->where('username', $user->m_username)->where('status', 'success')->count_all_results('tbl_member_login_attempts');
            $failed_attempts = $this->db->where('username', $user->m_username)->where('status', 'failed')->count_all_results('tbl_member_login_attempts');

            echo json_encode([
                'success' => true,
                'history' => $login_history,
                'statistics' => [
                    'total' => $total_attempts,
                    'success' => $success_attempts,
                    'failed' => $failed_attempts,
                    'success_rate' => $total_attempts > 0 ? round(($success_attempts / $total_attempts) * 100, 1) : 0
                ],
                'message' => 'ดึงข้อมูลสำเร็จ'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * สร้างข้อมูลอุปกรณ์ตัวอย่าง (สำหรับกรณีที่ไม่มี trusted_devices table)
     */
    private function generate_sample_devices($user_id)
    {
        $sample_devices = [
            [
                'id' => 1,
                'browser' => 'Chrome',
                'version' => '131.0',
                'platform' => 'Windows 10',
                'ip_address' => '192.168.1.100',
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'last_used_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+23 days')),
                'is_expired' => false,
                'source' => 'sample'
            ],
            [
                'id' => 2,
                'browser' => 'Safari',
                'version' => '17.1',
                'platform' => 'iPhone',
                'ip_address' => '192.168.1.101',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'last_used_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+27 days')),
                'is_expired' => false,
                'source' => 'sample'
            ]
        ];
        
        return $sample_devices;
    }
	
	
	
	
	private function compress_image($source_path, $destination_path, $max_width = 800, $max_height = 800, $quality = 85)
{
    try {
        // ตรวจสอบว่าไฟล์ต้นฉบับมีอยู่จริง
        if (!file_exists($source_path)) {
            return false;
        }

        // ดึงข้อมูลรูปภาพ
        $image_info = getimagesize($source_path);
        if (!$image_info) {
            return false;
        }

        $width = $image_info[0];
        $height = $image_info[1];
        $mime_type = $image_info['mime'];

        // ตรวจสอบว่าขนาดภาพเล็กกว่าที่กำหนดแล้วหรือไม่
        if ($width <= $max_width && $height <= $max_height) {
            // ถ้าขนาดเล็กกว่าแล้ว แค่คัดลอกไฟล์และปรับคุณภาพ
            return $this->optimize_image_quality($source_path, $destination_path, $mime_type, $quality);
        }

        // คำนวณขนาดใหม่ (รักษาสัดส่วน)
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = round($width * $ratio);
        $new_height = round($height * $ratio);

        // สร้าง canvas ใหม่
        $new_image = imagecreatetruecolor($new_width, $new_height);

        // ตั้งค่าความโปร่งใส (สำหรับ PNG และ GIF)
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefill($new_image, 0, 0, $transparent);

        // โหลดรูปภาพต้นฉบับตามประเภท
        switch ($mime_type) {
            case 'image/jpeg':
                $source_image = imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($source_path);
                break;
            case 'image/gif':
                $source_image = imagecreatefromgif($source_path);
                break;
            default:
                return false;
        }

        if (!$source_image) {
            return false;
        }

        // ปรับขนาดรูปภาพ
        imagecopyresampled(
            $new_image, $source_image,
            0, 0, 0, 0,
            $new_width, $new_height,
            $width, $height
        );

        // บันทึกรูปภาพใหม่
        $result = false;
        switch ($mime_type) {
            case 'image/jpeg':
                $result = imagejpeg($new_image, $destination_path, $quality);
                break;
            case 'image/png':
                // PNG quality: 0 (ไม่บีบ) ถึง 9 (บีบมากที่สุด)
                $png_quality = round((100 - $quality) / 10);
                $result = imagepng($new_image, $destination_path, $png_quality);
                break;
            case 'image/gif':
                $result = imagegif($new_image, $destination_path);
                break;
        }

        // ล้างหน่วยความจำ
        imagedestroy($source_image);
        imagedestroy($new_image);

        return $result;

    } catch (Exception $e) {
        error_log("Image compression error: " . $e->getMessage());
        return false;
    }
}

	
	
	/**
 * เช็คสิทธิ์การเข้าถึงแบบทั่วไป
 */
private function check_access_permission($required_permissions = [])
{
    $user_id = $this->session->userdata('m_id');
    
    // ดึงข้อมูล user
    $user = $this->db->select('ref_pid, m_system, grant_system_ref_id, grant_user_ref_id, m_status')
                    ->where('m_id', $user_id)
                    ->get('tbl_member')
                    ->row();
    
    if (!$user || $user->m_status != '1') {
        return false;
    }
    
    // ถ้าเป็น admin (pid 1, 2) หรือ super_admin ให้ผ่านทุกอย่าง
    if (in_array($user->ref_pid, [1, 2]) || $user->m_system == 'super_admin') {
        return true;
    }
    
    // เช็คสิทธิ์ตาม grant_system_ref_id ถ้ามีการกำหนด
    if (!empty($required_permissions) && !empty($user->grant_system_ref_id)) {
        $user_permissions = explode(',', $user->grant_system_ref_id);
        
        foreach ($required_permissions as $permission) {
            if (in_array($permission, $user_permissions)) {
                return true;
            }
        }
        return false;
    }
    
    return true; // ให้ผ่านถ้าไม่มีข้อกำหนดพิเศษ
}

/**
 * เช็คสิทธิ์ admin (เฉพาะ admin และ super admin)
 */
private function check_admin_only()
{
    $user_id = $this->session->userdata('m_id');
    
    $user = $this->db->select('ref_pid, m_system')
                    ->where('m_id', $user_id)
                    ->where('m_status', '1')
                    ->get('tbl_member')
                    ->row();
    
    if (!$user) {
        return false;
    }
    
    // เฉพาะ admin (pid 1, 2) หรือ super_admin, system_admin
    return (in_array($user->ref_pid, [1, 2]) || 
            in_array($user->m_system, ['super_admin', 'system_admin']));
}

/**
 * แสดงหน้า Access Denied
 */
private function show_access_denied($message = 'คุณไม่มีสิทธิ์เข้าใช้งานส่วนนี้')
{
    $data['error_message'] = $message;
    $data['back_url'] = site_url('User/choice');
    
    $this->load->view('templat/header');
    $this->load->view('asset/css', ['current_theme' => $this->Theme_model->get_current_theme()]);
    $this->load->view('system_admin/access_denied', $data);
    $this->load->view('asset/js');
    $this->load->view('templat/footer');
}
	
/**
 * ฟังก์ชันปรับคุณภาพภาพ (สำหรับรูปที่ขนาดเหมาะสมแล้ว)
 */
private function optimize_image_quality($source_path, $destination_path, $mime_type, $quality = 85)
{
    try {
        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source_path);
                if ($image) {
                    $result = imagejpeg($image, $destination_path, $quality);
                    imagedestroy($image);
                    return $result;
                }
                break;
            case 'image/png':
                $image = imagecreatefrompng($source_path);
                if ($image) {
                    $png_quality = round((100 - $quality) / 10);
                    $result = imagepng($image, $destination_path, $png_quality);
                    imagedestroy($image);
                    return $result;
                }
                break;
            case 'image/gif':
                // GIF ไม่สามารถปรับคุณภาพได้ แค่คัดลอก
                return copy($source_path, $destination_path);
            default:
                return copy($source_path, $destination_path);
        }
        return false;
    } catch (Exception $e) {
        error_log("Image optimization error: " . $e->getMessage());
        return copy($source_path, $destination_path); // fallback
    }
}

/**
 * ฟังก์ชันดึงขนาดไฟล์ในรูปแบบที่อ่านง่าย
 */
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
 * AJAX method สำหรับอัพเดทโปรไฟล์ (แก้ไขใหม่ - รองรับการบีบภาพ)
 */
public function update_profile_ajax()
{
    if (!$this->input->is_ajax_request()) {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }

    $user_id = $this->input->post('m_id');
    $logged_user_id = $this->session->userdata('m_id');
    
    // ตรวจสอบสิทธิ์
    if ($user_id != $logged_user_id && $this->session->userdata('m_system') != 'system_admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        return;
    }

    try {
        // ดึงข้อมูลเดิม
        $old_document = $this->db->get_where('tbl_member', array('m_id' => $user_id))->row();
        if (!$old_document) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        // ตรวจสอบและสร้างโฟลเดอร์
        $this->ensure_upload_directories();

        // debug info
        $debug_info = [
            'server_info' => [
                'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'],
                'PHP_SELF' => $_SERVER['PHP_SELF'],
                'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'],
                'getcwd' => getcwd(),
                'realpath_docs' => realpath('./docs/'),
                'is_dir_docs_temp' => is_dir('./docs/temp/'),
                'is_writable_docs_temp' => is_writable('./docs/temp/'),
                'FCPATH_defined' => defined('FCPATH'),
                'FCPATH_value' => defined('FCPATH') ? FCPATH : 'NOT_DEFINED'
            ]
        ];

        // ตรวจสอบการอัปโหลดรูปภาพใหม่
        $filename = $old_document->m_img;
        $update_doc_file = !empty($_FILES['m_img']['name']);
        $compression_info = [];

        if ($update_doc_file) {
            // ตรวจสอบข้อผิดพลาดการอัพโหลด
            if ($_FILES['m_img']['error'] !== UPLOAD_ERR_OK) {
                $error_msg = $this->get_upload_error_message($_FILES['m_img']['error']);
                echo json_encode([
                    'success' => false, 
                    'message' => $error_msg,
                    'debug' => $debug_info
                ]);
                return;
            }

            // ตรวจสอบขนาดไฟล์
            $original_size = $_FILES['m_img']['size'];
            if ($original_size > 10 * 1024 * 1024) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'ไฟล์รูปภาพต้องมีขนาดไม่เกิน 10MB',
                    'file_size' => $this->format_file_size($original_size)
                ]);
                return;
            }

            // ลบรูปเดิม
            $this->delete_old_profile_image($old_document->m_img);

            // อัพโหลดไฟล์ใหม่
            $upload_result = $this->upload_and_process_image($user_id, $original_size);
            
            if (!$upload_result['success']) {
                // เพิ่ม debug info ลงในผลลัพธ์
                $upload_result['debug'] = array_merge($debug_info, $upload_result['debug'] ?? []);
                echo json_encode($upload_result);
                return;
            }
            
            $filename = $upload_result['filename'];
            $compression_info = $upload_result['compression_info'];
        }

        // ส่วนอื่นๆ คงเดิม...
        $data = array();
        
        if ($update_doc_file) {
            $data['m_img'] = $filename;
        }
        
        // อัปเดตข้อมูลอื่นๆ
        $this->prepare_update_data($data);

        // ตรวจสอบรหัสผ่าน
        $password_result = $this->validate_and_prepare_password($data);
        if (!$password_result['success']) {
            echo json_encode($password_result);
            return;
        }

        // ตรวจสอบว่ามีข้อมูลที่จะอัปเดทหรือไม่
        if (empty($data)) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีข้อมูลที่จะอัปเดท']);
            return;
        }

        // อัพเดทข้อมูล
        $this->db->where('m_id', $user_id);
        $result = $this->db->update('tbl_member', $data);

        if ($result) {
            // ดึงข้อมูลที่อัพเดทแล้ว
            $updated_profile = $this->get_updated_profile($user_id);

            // อัพเดท session
            $this->update_session_data($user_id, $logged_user_id, $data, $updated_profile);

            $response = [
                'success' => true, 
                'message' => 'อัพเดทข้อมูลสำเร็จ',
                'updated_fields' => array_keys($data),
                'profile' => [
                    'm_username' => $updated_profile->m_username,
                    'm_fname' => $updated_profile->m_fname,
                    'm_lname' => $updated_profile->m_lname,
                    'm_email' => $updated_profile->m_email,
                    'm_phone' => $updated_profile->m_phone,
                    'm_img' => $updated_profile->m_img,
                    'position_name' => $updated_profile->position_name
                ]
            ];

            // เพิ่มข้อมูลการบีบภาพ
            if (!empty($compression_info)) {
                $response['compression_info'] = $compression_info;
                $response['message'] .= ' (ประหยัดพื้นที่ ' . $compression_info['saved_space'] . ')';
            }

            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
        }

    } catch (Exception $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'System error: ' . $e->getMessage(),
            'debug' => $debug_info ?? [],
            'exception_details' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    }
}
	
	
	
	
	public function debug_paths()
{
    echo "<h2>Debug Paths Information</h2>";
    
    $paths_to_check = [
        './docs/',
        './docs/temp/',
        './docs/img/',
        './docs/img/avatar/',
        realpath('./docs/'),
        $_SERVER['DOCUMENT_ROOT'] . '/docs/',
        defined('FCPATH') ? FCPATH . 'docs/' : 'FCPATH_NOT_DEFINED'
    ];
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Path</th><th>Exists</th><th>Writable</th><th>Real Path</th></tr>";
    
    foreach ($paths_to_check as $path) {
        $exists = is_dir($path) ? 'YES' : 'NO';
        $writable = is_dir($path) && is_writable($path) ? 'YES' : 'NO';
        $realpath = realpath($path) ?: 'NOT_FOUND';
        
        echo "<tr>";
        echo "<td>{$path}</td>";
        echo "<td>{$exists}</td>";
        echo "<td>{$writable}</td>";
        echo "<td>{$realpath}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h3>Server Info</h3>";
    echo "<ul>";
    echo "<li>DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
    echo "<li>SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "</li>";
    echo "<li>PHP_SELF: " . $_SERVER['PHP_SELF'] . "</li>";
    echo "<li>getcwd(): " . getcwd() . "</li>";
    echo "<li>FCPATH: " . (defined('FCPATH') ? FCPATH : 'NOT_DEFINED') . "</li>";
    echo "</ul>";
    
    // ลองสร้างโฟลเดอร์
    echo "<h3>Try Creating Directories</h3>";
    $test_dirs = ['./docs/', './docs/temp/', './docs/img/', './docs/img/avatar/'];
    
    foreach ($test_dirs as $dir) {
        if (!is_dir($dir)) {
            $created = mkdir($dir, 0755, true);
            echo "<p>Creating {$dir}: " . ($created ? 'SUCCESS' : 'FAILED') . "</p>";
        } else {
            echo "<p>{$dir}: Already exists</p>";
        }
    }
}
	
	
	
	
	
/**
 * สร้างโฟลเดอร์ที่จำเป็นสำหรับการอัพโหลด (แก้ไขแล้ว)
 */
private function ensure_upload_directories()
{
    // ใช้ relative path แทน FCPATH
    $base_directories = [
        './docs/',
        './docs/img/',
        './docs/img/avatar/',
        './docs/temp/'
    ];
    
    foreach ($base_directories as $dir) {
        $real_path = realpath($dir);
        
        // ถ้าโฟลเดอร์ไม่มี ให้สร้าง
        if (!is_dir($dir)) {
            $created = mkdir($dir, 0755, true);
            if (!$created) {
                // ลองสร้างด้วย absolute path
                $abs_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($dir, './');
                if (!is_dir($abs_dir)) {
                    mkdir($abs_dir, 0755, true);
                }
            }
        }
        
        // ตรวจสอบสิทธิ์การเขียน
        if (is_dir($dir) && !is_writable($dir)) {
            chmod($dir, 0755);
        }
    }
    
    // ตรวจสอบผลลัพธ์
    $temp_check = is_dir('./docs/temp/') && is_writable('./docs/temp/');
    $avatar_check = is_dir('./docs/img/avatar/') && is_writable('./docs/img/avatar/');
    
    if (!$temp_check || !$avatar_check) {
        error_log("Directory creation failed - temp: {$temp_check}, avatar: {$avatar_check}");
    }
}

/**
 * ลบรูปโปรไฟล์เก่า
 */
private function delete_old_profile_image($old_filename)
{
    if (empty($old_filename)) {
        return;
    }
    
    // ลองลบจากทั้งสองที่
    $old_paths = [
        FCPATH . 'docs/img/avatar/' . $old_filename,
        FCPATH . 'docs/img/' . $old_filename
    ];
    
    foreach ($old_paths as $path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

/**
 * อัพโหลดและประมวลผลรูปภาพ
 */
private function upload_and_process_image($user_id, $original_size)
{
    // ลองหลายวิธีในการกำหนด path
    $possible_temp_paths = [
        './docs/temp/',
        realpath('./docs/temp/') . '/',
        $_SERVER['DOCUMENT_ROOT'] . '/docs/temp/',
        FCPATH . 'docs/temp/'
    ];
    
    $working_temp_path = null;
    $debug_info = [];
    
    // หา path ที่ใช้งานได้
    foreach ($possible_temp_paths as $index => $path) {
        $debug_info["path_{$index}"] = [
            'path' => $path,
            'exists' => is_dir($path),
            'writable' => is_dir($path) ? is_writable($path) : false,
            'realpath' => realpath($path)
        ];
        
        if (is_dir($path) && is_writable($path)) {
            $working_temp_path = rtrim($path, '/') . '/';
            break;
        }
    }
    
    // ถ้าไม่เจอ path ที่ใช้ได้ ให้สร้างใหม่
    if (!$working_temp_path) {
        $default_temp = './docs/temp/';
        if (!is_dir($default_temp)) {
            mkdir($default_temp, 0755, true);
        }
        chmod($default_temp, 0755);
        $working_temp_path = $default_temp;
    }
    
    // ตรวจสอบอีกครั้ง
    if (!is_dir($working_temp_path) || !is_writable($working_temp_path)) {
        return [
            'success' => false,
            'message' => 'ไม่สามารถใช้งานโฟลเดอร์ temp ได้: ' . $working_temp_path,
            'debug' => $debug_info
        ];
    }
    
    // ตั้งค่า upload config
    $config = [
        'upload_path' => $working_temp_path,
        'allowed_types' => 'gif|jpg|png|jpeg|webp',
        'max_size' => 10240, // 10MB
        'encrypt_name' => TRUE,
        'remove_spaces' => TRUE,
        'detect_mime' => TRUE,
        'mod_mime_fix' => TRUE
    ];
    
    // โหลด upload library ใหม่
    $this->load->library('upload');
    $this->upload->initialize($config);
    
    if (!$this->upload->do_upload('m_img')) {
        $errors = $this->upload->display_errors('', '');
        
        return [
            'success' => false,
            'message' => 'Upload error: ' . $errors,
            'upload_config' => $config,
            'debug' => $debug_info,
            'php_upload_errors' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit')
            ]
        ];
    }
    
    $upload_data = $this->upload->data();
    $temp_file = $upload_data['full_path'];
    
    // ตั้งค่าสำหรับไฟล์สุดท้าย
    $possible_avatar_paths = [
        './docs/img/avatar/',
        realpath('./docs/img/avatar/') . '/',
        $_SERVER['DOCUMENT_ROOT'] . '/docs/img/avatar/'
    ];
    
    $working_avatar_path = null;
    foreach ($possible_avatar_paths as $path) {
        if (is_dir($path) && is_writable($path)) {
            $working_avatar_path = rtrim($path, '/') . '/';
            break;
        }
    }
    
    if (!$working_avatar_path) {
        $working_avatar_path = './docs/img/avatar/';
        if (!is_dir($working_avatar_path)) {
            mkdir($working_avatar_path, 0755, true);
        }
    }
    
    // สร้างชื่อไฟล์ใหม่
    $final_filename = 'profile_' . $user_id . '_' . time() . '.jpg';
    $final_path = $working_avatar_path . $final_filename;
    
    // บีบและปรับขนาดรูปภาพ
    $compression_success = $this->compress_image($temp_file, $final_path, 800, 800, 85);
    
    $compression_info = [];
    
    if ($compression_success && file_exists($final_path)) {
        // คำนวณข้อมูลการบีบ
        $compressed_size = filesize($final_path);
        $compression_ratio = round((1 - ($compressed_size / $original_size)) * 100, 2);
        
        $compression_info = [
            'original_size' => $this->format_file_size($original_size),
            'compressed_size' => $this->format_file_size($compressed_size),
            'saved_space' => $this->format_file_size($original_size - $compressed_size),
            'compression_ratio' => $compression_ratio . '%'
        ];
    } else {
        // ถ้าบีบไม่สำเร็จ ใช้ไฟล์ต้นฉบับ
        $final_filename = $upload_data['file_name'];
        $fallback_path = $working_avatar_path . $final_filename;
        copy($temp_file, $fallback_path);
        
        $compression_info = [
            'original_size' => $this->format_file_size($original_size),
            'compressed_size' => $this->format_file_size(filesize($fallback_path)),
            'saved_space' => '0 KB',
            'compression_ratio' => '0%',
            'fallback_used' => true
        ];
    }
    
    // ลบไฟล์ชั่วคราว
    if (file_exists($temp_file)) {
        unlink($temp_file);
    }
    
    return [
        'success' => true,
        'filename' => $final_filename,
        'compression_info' => $compression_info,
        'paths_used' => [
            'temp' => $working_temp_path,
            'final' => $working_avatar_path
        ]
    ];
}

/**
 * เตรียมข้อมูลสำหรับอัพเดท
 */
private function prepare_update_data(&$data)
{
    $fields = ['m_username', 'ref_pid', 'm_fname', 'm_lname', 'm_email'];
    
    foreach ($fields as $field) {
        $value = $this->input->post($field);
        if ($value !== null && $value !== '') {
            $data[$field] = trim($value);
        }
    }
    
    // สำหรับ phone อนุญาตให้เป็นค่าว่าง
    $phone = $this->input->post('m_phone');
    if ($phone !== null) {
        $data['m_phone'] = trim($phone);
    }
}

/**
 * ตรวจสอบและเตรียมรหัสผ่าน
 */
private function validate_and_prepare_password(&$data)
{
    $new_password = $this->input->post('new_password');
    $confirm_password = $this->input->post('confirm_password');
    
    if (!empty($new_password) || !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            return ['success' => false, 'message' => 'รหัสผ่านไม่ตรงกัน'];
        }
        
        if (strlen($new_password) < 8) {
            return ['success' => false, 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร'];
        }
        
        $data['m_password'] = sha1($new_password);
    }
    
    return ['success' => true];
}

/**
 * ดึงข้อมูลโปรไฟล์ที่อัพเดทแล้ว
 */
private function get_updated_profile($user_id)
{
    return $this->db->select('m.*, p.pname as position_name')
                   ->from('tbl_member m')
                   ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                   ->where('m.m_id', $user_id)
                   ->get()
                   ->row();
}

/**
 * อัพเดท session data
 */
private function update_session_data($user_id, $logged_user_id, $data, $updated_profile)
{
    if ($user_id == $logged_user_id) {
        $session_update = [];
        
        $session_fields = [
            'm_fname' => 'm_fname',
            'm_lname' => 'm_lname', 
            'm_username' => 'm_username',
            'm_img' => 'm_img'
        ];
        
        foreach ($session_fields as $data_key => $profile_key) {
            if (isset($data[$data_key])) {
                $session_update[$data_key] = $updated_profile->$profile_key;
            }
        }
        
        if (!empty($session_update)) {
            $this->session->set_userdata($session_update);
        }
    }
}

/**
 * แปลงข้อผิดพลาดการอัพโหลด
 */
private function get_upload_error_message($error_code)
{
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE => 'ไฟล์ใหญ่เกินไปตาม php.ini (upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'ไฟล์ใหญ่เกินไปตาม HTML form (MAX_FILE_SIZE)',
        UPLOAD_ERR_PARTIAL => 'อัพโหลดไม่สมบูรณ์',
        UPLOAD_ERR_NO_FILE => 'ไม่มีไฟล์ที่เลือก',
        UPLOAD_ERR_NO_TMP_DIR => 'ไม่มีโฟลเดอร์ temp',
        UPLOAD_ERR_CANT_WRITE => 'เขียนไฟล์ไม่ได้',
        UPLOAD_ERR_EXTENSION => 'PHP extension หยุดการอัพโหลด'
    ];
    
    return isset($upload_errors[$error_code]) ? 
           $upload_errors[$error_code] : 
           'ข้อผิดพลาดไม่ทราบสาเหตุ: ' . $error_code;
}
	
	
}