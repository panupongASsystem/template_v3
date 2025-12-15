<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email OTP Model
 * จัดการ OTP ผ่านอีเมล
 */
class Email_otp_model extends CI_Model
{
    private $table = 'tbl_member_public_otp';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * สร้าง OTP 6 ตัวอักษร (A-Z, 0-9)
     * @return string
     */
    public function generate_otp()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $otp = '';

        for ($i = 0; $i < 15; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $otp;
    }

    /**
     * สร้างและบันทึก OTP
     * @param string $mp_id
     * @param string $mp_email
     * @param string $otp_type
     * @return array
     */
    public function create_otp($mp_id, $mp_email, $otp_type = 'login')
    {
        try {
            // ลบ OTP เก่าที่ยังไม่ใช้
            $this->cleanup_old_otp($mp_id, $otp_type);

            // สร้าง OTP ใหม่
            $otp_code = $this->generate_otp();
            $expires_at = date('Y-m-d H:i:s', time() + (10 * 60)); // 10 นาที

            $data = [
                'mp_id' => $mp_id,
                'mp_email' => $mp_email,
                'otp_code' => $otp_code,
                'otp_type' => $otp_type,
                'expires_at' => $expires_at,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert($this->table, $data);

            if ($this->db->affected_rows() > 0) {
                log_message('info', "OTP created for user: $mp_email (Type: $otp_type)");

                return [
                    'success' => true,
                    'otp_code' => $otp_code,
                    'expires_at' => $expires_at,
                    'otp_id' => $this->db->insert_id()
                ];
            }

            return [
                'success' => false,
                'message' => 'ไม่สามารถสร้าง OTP ได้'
            ];

        } catch (Exception $e) {
            log_message('error', 'Error creating OTP: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ];
        }
    }

    /**
     * ตรวจสอบ OTP
     * @param string $mp_id
     * @param string $otp_code
     * @param string $otp_type
     * @return array
     */
    public function verify_otp($mp_id, $otp_code, $otp_type = 'login')
    {
        try {
            $current_time = date('Y-m-d H:i:s');

            // ค้นหา OTP ที่ตรงกัน
            $this->db->where('mp_id', $mp_id);
            $this->db->where('otp_code', $otp_code);
            $this->db->where('otp_type', $otp_type);
            $this->db->where('is_used', 0);
            $this->db->where('expires_at >', $current_time);
            $otp = $this->db->get($this->table)->row();

            if (!$otp) {
                // เช็คว่า OTP หมดอายุหรือใช้ไปแล้ว
                $this->db->where('mp_id', $mp_id);
                $this->db->where('otp_code', $otp_code);
                $this->db->where('otp_type', $otp_type);
                $expired_otp = $this->db->get($this->table)->row();

                if ($expired_otp) {
                    if ($expired_otp->is_used == 1) {
                        return [
                            'success' => false,
                            'message' => 'รหัส OTP นี้ถูกใช้งานไปแล้ว'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'รหัส OTP หมดอายุแล้ว กรุณาขอรหัสใหม่'
                        ];
                    }
                }

                return [
                    'success' => false,
                    'message' => 'รหัส OTP ไม่ถูกต้อง'
                ];
            }

            // ทำเครื่องหมายว่าใช้แล้ว
            $this->db->where('id', $otp->id);
            $this->db->update($this->table, [
                'is_used' => 1,
                'used_at' => $current_time
            ]);

            log_message('info', "OTP verified successfully for user: {$otp->mp_email}");

            return [
                'success' => true,
                'message' => 'ยืนยัน OTP สำเร็จ'
            ];

        } catch (Exception $e) {
            log_message('error', 'Error verifying OTP: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ OTP'
            ];
        }
    }

    /**
     * ลบ OTP เก่าที่ยังไม่ใช้
     * @param string $mp_id
     * @param string $otp_type
     * @return bool
     */
    private function cleanup_old_otp($mp_id, $otp_type = 'login')
    {
        try {
            $this->db->where('mp_id', $mp_id);
            $this->db->where('otp_type', $otp_type);
            $this->db->where('is_used', 0);
            $this->db->delete($this->table);

            return true;
        } catch (Exception $e) {
            log_message('error', 'Error cleaning up OTP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบ OTP ที่หมดอายุทั้งหมด (ใช้ใน Cron Job)
     * @return int จำนวนที่ลบ
     */
    public function cleanup_expired_otp()
    {
        try {
            $current_time = date('Y-m-d H:i:s');

            $this->db->where('expires_at <', $current_time);
            $this->db->delete($this->table);

            $affected = $this->db->affected_rows();

            if ($affected > 0) {
                log_message('info', "Cleaned up $affected expired OTP records");
            }

            return $affected;

        } catch (Exception $e) {
            log_message('error', 'Error cleaning up expired OTP: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ตรวจสอบว่ามี OTP ที่ใช้งานได้อยู่หรือไม่
     * @param string $mp_id
     * @param string $otp_type
     * @return bool
     */
    public function has_valid_otp($mp_id, $otp_type = 'login')
    {
        $current_time = date('Y-m-d H:i:s');

        $this->db->where('mp_id', $mp_id);
        $this->db->where('otp_type', $otp_type);
        $this->db->where('is_used', 0);
        $this->db->where('expires_at >', $current_time);

        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * นับจำนวน OTP ที่ส่งไปแล้วในช่วงเวลา (ป้องกัน spam)
     * @param string $mp_id
     * @param int $minutes
     * @return int
     */
    public function count_recent_otp($mp_id, $minutes = 60)
    {
        $cutoff_time = date('Y-m-d H:i:s', time() - ($minutes * 60));

        $this->db->where('mp_id', $mp_id);
        $this->db->where('created_at >', $cutoff_time);

        return $this->db->count_all_results($this->table);
    }

    /**
     * ดึง OTP ล่าสุดของผู้ใช้ (สำหรับแสดงเวลาหมดอายุ)
     * @param string $mp_id
     * @param string $otp_type
     * @return object|null
     */
    public function get_latest_otp($mp_id, $otp_type = 'login')
    {
        $this->db->where('mp_id', $mp_id);
        $this->db->where('otp_type', $otp_type);
        $this->db->where('is_used', 0);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);

        return $this->db->get($this->table)->row();
    }


    /**
     * นับจำนวน OTP ที่ส่งโดยใช้อีเมล
     */
    public function count_recent_otp_by_email($email, $minutes = 60)
    {
        $cutoff_time = date('Y-m-d H:i:s', time() - ($minutes * 60));

        $this->db->where('mp_email', $email);
        $this->db->where('created_at >', $cutoff_time);

        return $this->db->count_all_results($this->table);
    }

    /**
     * สร้าง OTP สำหรับการลงทะเบียน (ใช้เป็น verification token)
     */
    public function create_otp_for_registration($email)
    {
        try {
            $temp_id = 'temp_' . md5($email . time());

            $this->cleanup_old_otp_by_email($email, 'register');

            $token = $this->generate_otp();
            $expires_at = date('Y-m-d H:i:s', time() + (10 * 60));

            $data = [
                'mp_id' => $temp_id,
                'mp_email' => $email,
                'otp_code' => $token,
                'otp_type' => 'register',
                'expires_at' => $expires_at,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert($this->table, $data);

            if ($this->db->affected_rows() > 0) {
                log_message('info', "Verification token created for email: $email");

                return [
                    'success' => true,
                    'otp_code' => $token,
                    'expires_at' => $expires_at,
                    'token_id' => $this->db->insert_id()
                ];
            }

            return [
                'success' => false,
                'message' => 'ไม่สามารถสร้างลิงก์ยืนยันได้'
            ];

        } catch (Exception $e) {
            log_message('error', 'Error creating verification token: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ];
        }
    }

    /**
     * ยืนยัน OTP สำหรับการลงทะเบียน
     */
    public function verify_otp_for_registration($email, $token)
    {
        try {
            $current_time = date('Y-m-d H:i:s');

            $this->db->where('mp_email', $email);
            $this->db->where('otp_code', $token);
            $this->db->where('otp_type', 'register');
            $this->db->where('is_used', 0);
            $this->db->where('expires_at >', $current_time);
            $otp = $this->db->get($this->table)->row();

            if (!$otp) {
                $this->db->where('mp_email', $email);
                $this->db->where('otp_code', $token);
                $this->db->where('otp_type', 'register');
                $expired_otp = $this->db->get($this->table)->row();

                if ($expired_otp) {
                    if ($expired_otp->is_used == 1) {
                        return [
                            'success' => false,
                            'message' => 'ลิงก์ยืนยันนี้ถูกใช้งานไปแล้ว'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'ลิงก์ยืนยันหมดอายุแล้ว กรุณาขอลิงก์ใหม่'
                        ];
                    }
                }

                return [
                    'success' => false,
                    'message' => 'ลิงก์ยืนยันไม่ถูกต้อง'
                ];
            }

            $this->db->where('id', $otp->id);
            $this->db->update($this->table, [
                'is_used' => 1,
                'used_at' => $current_time
            ]);

            log_message('info', "Email verified successfully: {$otp->mp_email}");

            return [
                'success' => true,
                'message' => 'ยืนยันอีเมลสำเร็จ'
            ];

        } catch (Exception $e) {
            log_message('error', 'Error verifying token: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ'
            ];
        }
    }

    /**
     * ดึง verification token ล่าสุดสำหรับการลงทะเบียน
     */
    public function get_latest_otp_for_registration($email)
    {
        $this->db->where('mp_email', $email);
        $this->db->where('otp_type', 'register');
        $this->db->where('is_used', 0);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);

        return $this->db->get($this->table)->row();
    }

    /**
     * ลบ OTP/Token เก่าโดยใช้อีเมล
     */
    private function cleanup_old_otp_by_email($email, $otp_type = 'register')
    {
        try {
            $this->db->where('mp_email', $email);
            $this->db->where('otp_type', $otp_type);
            $this->db->where('is_used', 0);
            $this->db->delete($this->table);

            return true;
        } catch (Exception $e) {
            log_message('error', 'Error cleaning up OTP by email: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * 🆕 สร้าง OTP พร้อมเก็บข้อมูลฟอร์มสมัครสมาชิก
     */
    public function create_otp_with_registration_data($mp_id, $mp_email, $otp_type = 'register', $registration_data = [])
    {
        try {
            log_message('info', "🔑 create_otp_with_registration_data() called for: $mp_email");

            // ลบ OTP เก่า
            $this->cleanup_old_otp($mp_id, $otp_type);

            // สร้าง OTP
            $otp_code = $this->generate_otp();
            $expires_at = date('Y-m-d H:i:s', time() + (10 * 60)); // 10 นาที

            // 🆕 แปลงข้อมูลฟอร์มเป็น JSON
            $registration_json = json_encode($registration_data, JSON_UNESCAPED_UNICODE);

            $data = [
                'mp_id' => $mp_id,
                'mp_email' => $mp_email,
                'otp_code' => $otp_code,
                'otp_type' => $otp_type,
                'expires_at' => $expires_at,
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'registration_data' => $registration_json, // 🆕 เพิ่มข้อมูลฟอร์ม
                'created_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', "💾 Inserting OTP with registration data: $otp_code");
            $this->db->insert($this->table, $data);

            if ($this->db->affected_rows() > 0) {
                $insert_id = $this->db->insert_id();
                log_message('info', "✅ OTP with registration data created (ID: $insert_id)");

                return [
                    'success' => true,
                    'otp_code' => $otp_code,
                    'expires_at' => $expires_at,
                    'otp_id' => $insert_id
                ];
            }

            log_message('error', "❌ Failed to insert OTP with registration data");
            return [
                'success' => false,
                'message' => 'ไม่สามารถสร้าง OTP ได้'
            ];

        } catch (Exception $e) {
            log_message('error', '❌ Exception in create_otp_with_registration_data: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ];
        }
    }

    /**
     * 🆕 ดึงข้อมูลฟอร์มที่เก็บไว้จาก OTP
     */
    public function get_registration_data($otp_code, $mp_email)
    {
        try {
            log_message('info', "🔍 get_registration_data() for email: $mp_email, token: $otp_code");

            $this->db->where('otp_code', $otp_code);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('otp_type', 'register');
            $otp = $this->db->get($this->table)->row();

            if ($otp && !empty($otp->registration_data)) {
                $data = json_decode($otp->registration_data, true);
                log_message('info', "✅ Registration data found and decoded");
                return $data;
            }

            log_message('info', "⚠️ No registration data found");
            return null;

        } catch (Exception $e) {
            log_message('error', '❌ Exception in get_registration_data: ' . $e->getMessage());
            return null;
        }
    }
}