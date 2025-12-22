<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cookie_model extends CI_Model
{
    private $log_table = 'tbl_cookie_logs';

    /**
     * บันทึกการยอมรับ cookie (เก็บใน tbl_cookie_logs)
     */
    public function save_consent($log_data)
    {
        try {
            // เพิ่ม created_at ถ้ายังไม่มี
            if (!isset($log_data['created_at'])) {
                $log_data['created_at'] = date('Y-m-d H:i:s');
            }

            $result = $this->db->insert($this->log_table, $log_data);

            if (!$result) {
                log_message('error', 'Cookie save error: ' . print_r($this->db->error(), true));
            }

            return $result;
        } catch (Exception $e) {
            log_message('error', 'Cookie save exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูล consent
     */
    public function get_consent($session_id)
    {
        try {
            $this->db->where('session_id', $session_id);
            $this->db->where('log_type', 'access');
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(1);
            return $this->db->get($this->log_table)->row();
        } catch (Exception $e) {
            log_message('error', 'Get consent error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * อัพเดต consent (ไม่ได้ใช้งาน แต่เก็บไว้เผื่อมี code เรียก)
     */
    public function update_consent($session_id, $data)
    {
        // ไม่ทำอะไร เพราะไม่มีตาราง tbl_cookie
        return true;
    }

    /**
     * ตรวจสอบว่า fingerprint ถูกบล็อกหรือไม่
     */
    public function is_fingerprint_blocked($fingerprint)
    {
        if (empty($fingerprint)) {
            return false;
        }

        try {
            $this->db->select('id, blocked_until, reason');
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('is_blocked', 1);
            $this->db->where('blocked_until >', date('Y-m-d H:i:s'));
            $this->db->order_by('blocked_until', 'DESC');
            $this->db->limit(1);
            $blocked = $this->db->get($this->log_table)->row();

            return !empty($blocked);
        } catch (Exception $e) {
            log_message('error', 'Check block error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูล block ของ fingerprint
     */
    public function get_block_info($fingerprint)
    {
        try {
            $this->db->select('blocked_until, reason');
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('is_blocked', 1);
            $this->db->where('blocked_until >', date('Y-m-d H:i:s'));
            $this->db->order_by('blocked_until', 'DESC');
            $this->db->limit(1);
            return $this->db->get($this->log_table)->row();
        } catch (Exception $e) {
            log_message('error', 'Get block info error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ตรวจสอบ Rate Limiting ตาม fingerprint
     */
    public function check_rate_limit($fingerprint, $ip_address, $max_requests = 10, $time_window = 60)
    {
        try {
            // ถ้าไม่มี fingerprint ให้ใช้ IP
            $check_field = !empty($fingerprint) ? 'fingerprint' : 'ip_address';
            $check_value = !empty($fingerprint) ? $fingerprint : $ip_address;

            if (empty($check_value)) {
                return true;
            }

            // ตรวจสอบว่าถูกบล็อกหรือไม่
            if ($check_field === 'fingerprint' && $this->is_fingerprint_blocked($fingerprint)) {
                return false;
            }

            $time_ago = date('Y-m-d H:i:s', time() - $time_window);

            // นับจำนวน access ในช่วงเวลาที่กำหนด
            $this->db->where($check_field, $check_value);
            $this->db->where('created_at >=', $time_ago);
            $this->db->where('log_type', 'access');
            $count = $this->db->count_all_results($this->log_table);

            // ถ้าเกินขอบเขต
            if ($count >= $max_requests) {
                $this->block_fingerprint($fingerprint, $ip_address, 'Rate limit exceeded', 3600);
                return false;
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Check rate limit error: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * บล็อก fingerprint ชั่วคราว
     */
    public function block_fingerprint($fingerprint, $ip_address, $reason, $duration = 3600)
    {
        try {
            // ปิด block เก่าของ fingerprint นี้
            if (!empty($fingerprint)) {
                $this->db->where('fingerprint', $fingerprint);
                $this->db->where('is_blocked', 1);
                $this->db->update($this->log_table, ['is_blocked' => 0]);
            }

            // สร้าง block ใหม่
            $block_data = [
                'ip_address' => $ip_address,
                'fingerprint' => $fingerprint,
                'user_agent' => $this->input->user_agent(),
                'payload' => '',
                'reason' => $reason,
                'blocked_until' => date('Y-m-d H:i:s', time() + $duration),
                'is_blocked' => 1,
                'log_type' => 'blocked',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert($this->log_table, $block_data);

            log_message('error', "Fingerprint Blocked: {$fingerprint} (IP: {$ip_address}) - {$reason} until " . $block_data['blocked_until']);
        } catch (Exception $e) {
            log_message('error', 'Block fingerprint error: ' . $e->getMessage());
        }
    }

    /**
     * บันทึก suspicious activities
     */
    public function log_suspicious($data)
    {
        try {
            $log_data = [
                'ip_address' => $data['ip_address'],
                'fingerprint' => isset($data['fingerprint']) ? $data['fingerprint'] : null,
                'user_agent' => $data['user_agent'],
                'session_id' => isset($data['session_id']) ? $data['session_id'] : null,
                'payload' => isset($data['payload']) ? $data['payload'] : '', // ไม่ต้อง encode ซ้ำ เพราะมันเป็น string แล้ว
                'reason' => $data['reason'],
                'log_type' => 'suspicious',
                'is_blocked' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert($this->log_table, $log_data);
            log_message('error', 'Suspicious activity: ' . json_encode($log_data, JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            log_message('error', 'Log suspicious error: ' . $e->getMessage());
        }
    }

    /**
     * ปลดบล็อก fingerprint (manual)
     */
    public function unblock_fingerprint($fingerprint)
    {
        try {
            $this->db->where('fingerprint', $fingerprint);
            $this->db->where('is_blocked', 1);
            $result = $this->db->update($this->log_table, ['is_blocked' => 0]);

            if ($result) {
                log_message('info', "Fingerprint unblocked manually: {$fingerprint}");
            }

            return $result;
        } catch (Exception $e) {
            log_message('error', 'Unblock fingerprint error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายการ fingerprint ที่ถูกบล็อก
     */
    public function get_blocked_fingerprints($active_only = true)
    {
        try {
            $this->db->select('fingerprint, ip_address, user_agent, reason, blocked_until, created_at');
            $this->db->where('is_blocked', 1);

            if ($active_only) {
                $this->db->where('blocked_until >', date('Y-m-d H:i:s'));
            }

            $this->db->group_by('fingerprint');
            $this->db->order_by('blocked_until', 'DESC');
            return $this->db->get($this->log_table)->result();
        } catch (Exception $e) {
            log_message('error', 'Get blocked fingerprints error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * นับจำนวน fingerprint ที่ถูกบล็อก (active)
     */
    public function count_blocked_fingerprints()
    {
        try {
            $this->db->select('fingerprint');
            $this->db->where('is_blocked', 1);
            $this->db->where('blocked_until >', date('Y-m-d H:i:s'));
            $this->db->group_by('fingerprint');
            return $this->db->count_all_results($this->log_table);
        } catch (Exception $e) {
            log_message('error', 'Count blocked fingerprints error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ทำความสะอาด block ที่หมดอายุแล้ว
     */
    public function clean_expired_blocks()
    {
        try {
            $this->db->where('is_blocked', 1);
            $this->db->where('blocked_until <', date('Y-m-d H:i:s'));
            $count = $this->db->count_all_results($this->log_table);

            if ($count > 0) {
                $this->db->where('is_blocked', 1);
                $this->db->where('blocked_until <', date('Y-m-d H:i:s'));
                $this->db->update($this->log_table, ['is_blocked' => 0]);

                log_message('info', "Cleaned {$count} expired blocks");
            }

            return $count;
        } catch (Exception $e) {
            log_message('error', 'Clean expired blocks error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ลบ log เก่าที่เกิน X วัน
     */
    public function clean_old_logs($days = 30)
    {
        try {
            $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $this->db->where('created_at <', $date);
            $count = $this->db->count_all_results($this->log_table);

            if ($count > 0) {
                $this->db->where('created_at <', $date);
                $this->db->delete($this->log_table);
                log_message('info', "Deleted {$count} old log records");
            }

            return $count;
        } catch (Exception $e) {
            log_message('error', 'Clean old logs error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * สถิติการใช้งาน
     */
    public function get_statistics($days = 7)
    {
        try {
            $date_from = date('Y-m-d H:i:s', strtotime("-{$days} days"));

            // จำนวน access
            $this->db->where('log_type', 'access');
            $this->db->where('created_at >=', $date_from);
            $total_access = $this->db->count_all_results($this->log_table);

            // จำนวน suspicious
            $this->db->where('log_type', 'suspicious');
            $this->db->where('created_at >=', $date_from);
            $total_suspicious = $this->db->count_all_results($this->log_table);

            // จำนวน blocked
            $this->db->where('log_type', 'blocked');
            $this->db->where('created_at >=', $date_from);
            $total_blocked = $this->db->count_all_results($this->log_table);

            // Active blocks
            $active_blocks = $this->count_blocked_fingerprints();

            return [
                'total_access' => $total_access,
                'total_suspicious' => $total_suspicious,
                'total_blocked' => $total_blocked,
                'active_blocks' => $active_blocks,
                'period_days' => $days
            ];
        } catch (Exception $e) {
            log_message('error', 'Get statistics error: ' . $e->getMessage());
            return [
                'total_access' => 0,
                'total_suspicious' => 0,
                'total_blocked' => 0,
                'active_blocks' => 0,
                'period_days' => $days
            ];
        }
    }
}
