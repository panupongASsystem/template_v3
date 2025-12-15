<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Debug_permissions extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // เช็ค login
        if (!$this->session->userdata('m_id')) {
            redirect('User/logout', 'refresh');
        }
        
        // เฉพาะ System Admin เท่านั้น
        if (!$this->is_system_admin()) {
            show_404();
            return;
        }
    }
    
    /**
     * ✅ ตรวจสอบว่าเป็น System Admin หรือไม่
     */
    private function is_system_admin()
    {
        $member_id = $this->session->userdata('m_id');
        
        // ดึงข้อมูลสมาชิก
        $member = $this->db->select('ref_pid, m_system')
                           ->from('tbl_member')
                           ->where('m_id', $member_id)
                           ->get()
                           ->row();
        
        if (!$member) {
            return false;
        }
        
        // เช็คว่าเป็น system_admin หรือ ref_pid = 1 (System Admin)
        return ($member->m_system === 'system_admin' || $member->ref_pid == 1);
    }
    
    /**
     * 🆕 ฟังก์ชันตรวจสอบสิทธิ์แบบรายละเอียด
     */
    private function check_user_permissions()
    {
        $member_id = $this->session->userdata('m_id');
        
        // ✅ แก้ไข: เพิ่มฟิลด์ที่จำเป็นในการ SELECT
        $this->db->select('m.m_id, m.ref_pid, m.m_system, m.grant_system_ref_id, m.grant_user_ref_id, m.m_fname, m.m_lname, m.m_email, p.pname, p.pid');
        $this->db->from('tbl_member m');
        $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
        $this->db->where('m.m_id', $member_id);
        $member = $this->db->get()->row();
        
        if (!$member) {
            return [
                'can_view_reports' => false,
                'can_manage_status' => false,
                'can_delete' => false,
                'user_role' => 'unknown',
                'reason' => 'ไม่พบข้อมูลผู้ใช้',
                'member_data' => null
            ];
        }
        
        $permissions = [
            'can_view_reports' => false,
            'can_manage_status' => false,
            'can_delete' => false,
            'user_role' => isset($member->pname) ? $member->pname : 'ไม่ระบุ',
            'position_id' => isset($member->pid) ? $member->pid : 0,
            'member_data' => $member,
            'reason' => ''
        ];
        
        // ✅ System Admin (pid = 1) - ทำได้ทุกอย่าง
        if ($member->ref_pid == 1 || $member->m_system === 'system_admin') {
            $permissions['can_view_reports'] = true;
            $permissions['can_manage_status'] = true;
            $permissions['can_delete'] = true;
            $permissions['user_role'] = 'System Admin';
            $permissions['reason'] = 'System Admin - มีสิทธิ์เต็ม';
            return $permissions;
        }
        
        // ✅ Super Admin (pid = 2) - ทำได้ทุกอย่าง
        if ($member->ref_pid == 2 || $member->m_system === 'super_admin') {
            $permissions['can_view_reports'] = true;
            $permissions['can_manage_status'] = true;
            $permissions['can_delete'] = true;
            $permissions['user_role'] = 'Super Admin';
            $permissions['reason'] = 'Super Admin - มีสิทธิ์เต็ม';
            return $permissions;
        }
        
        // ✅ User Admin (pid = 3) - ต้องเช็คสิทธิ์เพิ่มเติม
        if ($member->ref_pid == 3 || $member->m_system === 'user_admin') {
            $permissions['can_view_reports'] = true; // ดูรายงานได้เสมอ
            $permissions['user_role'] = 'User Admin';
            
            // เช็คสิทธิ์ grant_user_id = 105 สำหรับการจัดการสถานะ
            $has_complain_permission = $this->check_grant_user_permission($member, 105);
            
            if ($has_complain_permission) {
                $permissions['can_manage_status'] = true;
                $permissions['reason'] = 'User Admin - มีสิทธิ์จัดการเรื่องร้องเรียน (Grant ID: 105)';
            } else {
                $permissions['can_manage_status'] = false;
                $permissions['reason'] = 'User Admin - ไม่มีสิทธิ์จัดการเรื่องร้องเรียน (ไม่มี Grant ID: 105)';
            }
            
            // User Admin ไม่สามารถลบได้
            $permissions['can_delete'] = false;
            
            return $permissions;
        }
        
        // ✅ End User หรืออื่นๆ - ไม่มีสิทธิ์
        $permissions['reason'] = 'ไม่มีสิทธิ์เข้าถึงระบบรายงาน (Position: ' . (isset($member->pname) ? $member->pname : 'ไม่ระบุ') . ')';
        return $permissions;
    }
    
    /**
     * 🆕 ฟังก์ชันตรวจสอบสิทธิ์ grant_user_permission
     */
    private function check_grant_user_permission($member, $required_grant_id)
    {
        // วิธีที่ 1: เช็คจาก grant_user_ref_id ใน tbl_member
        if (!empty($member->grant_user_ref_id)) {
            $granted_ids = explode(',', $member->grant_user_ref_id);
            $granted_ids = array_map('trim', $granted_ids);
            
            if (in_array((string)$required_grant_id, $granted_ids)) {
                return true;
            }
        }
        
        // วิธีที่ 2: ตรวจสอบว่ามี grant_user_id = 105 ในตาราง tbl_grant_user หรือไม่
        $grant_exists = $this->db->where('grant_user_id', $required_grant_id)
                                ->count_all_results('tbl_grant_user');
        
        if ($grant_exists > 0) {
            // ถ้ามี grant นี้ในระบบ แต่ user ไม่มี ให้ถือว่าไม่มีสิทธิ์
            return false;
        }
        
        // ถ้าไม่มี grant นี้ในระบบเลย ให้ถือว่าไม่ต้องเช็ค (อาจจะเป็น grant ที่ยังไม่ได้สร้าง)
        return true;
    }
    
    /**
     * 🔍 หน้าหลัก Debug Permissions
     */
    public function index($user_id = null)
    {
        $user_id = $user_id ?: $this->session->userdata('m_id');
        
        echo "<!DOCTYPE html>";
        echo "<html><head>";
        echo "<title>Debug User Permissions</title>";
        echo "<meta charset='utf-8'>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
        echo ".container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo "table { width: 100%; border-collapse: collapse; margin: 10px 0; }";
        echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
        echo "th { background: #f0f0f0; }";
        echo ".success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".info { background: #e3f2fd; color: #0d47a1; padding: 15px; border-radius: 5px; margin: 10px 0; }";
        echo "code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; font-family: monospace; }";
        echo "</style>";
        echo "</head><body>";
        
        echo "<div class='container'>";
        echo "<h1>🔍 Debug User Permissions - User ID: {$user_id}</h1>";
        
        // บังคับให้ตรวจสอบ user อื่น
        $original_session = $this->session->userdata('m_id');
        $_SESSION['m_id'] = $user_id;
        
        try {
            $permissions = $this->check_user_permissions();
            
            echo "<h2>📋 ข้อมูลสิทธิ์:</h2>";
            echo "<table>";
            echo "<tr><th>รายการ</th><th>ค่า</th></tr>";
            
            foreach ($permissions as $key => $value) {
                if ($key == 'member_data') continue; // ข้าม member_data
                
                $display_value = is_bool($value) ? ($value ? '✅ ได้' : '❌ ไม่ได้') : htmlspecialchars($value);
                echo "<tr><td><strong>{$key}</strong></td><td>{$display_value}</td></tr>";
            }
            
            echo "</table>";
            
            // แสดงข้อมูล Member
            if (isset($permissions['member_data']) && $permissions['member_data']) {
                $member = $permissions['member_data'];
                
                echo "<h2>👤 ข้อมูล Member:</h2>";
                echo "<table>";
                echo "<tr><th>ฟิลด์</th><th>ค่า</th></tr>";
                echo "<tr><td>ID</td><td>" . (isset($member->m_id) ? $member->m_id : 'N/A') . "</td></tr>";
                echo "<tr><td>ชื่อ-นามสกุล</td><td>" . 
                     (isset($member->m_fname) ? $member->m_fname : 'N/A') . " " . 
                     (isset($member->m_lname) ? $member->m_lname : 'N/A') . "</td></tr>";
                echo "<tr><td>Email</td><td>" . (isset($member->m_email) ? $member->m_email : 'N/A') . "</td></tr>";
                echo "<tr><td>Position ID (ref_pid)</td><td>" . (isset($member->ref_pid) ? $member->ref_pid : 'N/A') . "</td></tr>";
                echo "<tr><td>Position Name</td><td>" . (isset($member->pname) ? $member->pname : 'ไม่ระบุ') . "</td></tr>";
                echo "<tr><td>System Role</td><td>" . (isset($member->m_system) ? $member->m_system : 'N/A') . "</td></tr>";
                echo "<tr><td>Grant System Ref ID</td><td>" . (isset($member->grant_system_ref_id) ? $member->grant_system_ref_id : 'N/A') . "</td></tr>";
                echo "<tr><td>Grant User Ref ID</td><td>" . (isset($member->grant_user_ref_id) ? $member->grant_user_ref_id : 'N/A') . "</td></tr>";
                echo "</table>";
                
                // ตรวจสอบ Grant User 105
                echo "<h3>🔐 การตรวจสอบ Grant User ID 105:</h3>";
                $has_grant_105 = $this->check_grant_user_permission($member, 105);
                echo "<p><strong>ผลการตรวจสอบ:</strong> " . ($has_grant_105 ? '✅ มีสิทธิ์' : '❌ ไม่มีสิทธิ์') . "</p>";
                
                // แสดงรายการ Grant ที่มี
                if (isset($member->grant_user_ref_id) && !empty($member->grant_user_ref_id)) {
                    $grants = explode(',', $member->grant_user_ref_id);
                    echo "<p><strong>Grant IDs ที่มี:</strong> " . implode(', ', array_map('trim', $grants)) . "</p>";
                } else {
                    echo "<p><strong>Grant User Ref ID:</strong> ไม่มี</p>";
                }
                
                // ✅ ตรวจสอบว่ามี Grant ID 105 ในตาราง tbl_grant_user หรือไม่
                echo "<h3>🗃️ ตรวจสอบตาราง tbl_grant_user:</h3>";
                
                try {
                    $grant_105_exists = $this->db->where('grant_user_id', 105)
                                                ->count_all_results('tbl_grant_user');
                    
                    if ($grant_105_exists > 0) {
                        $grant_105_data = $this->db->where('grant_user_id', 105)
                                                  ->get('tbl_grant_user')
                                                  ->row();
                        
                        echo "<div class='success'>";
                        echo "✅ Grant ID 105 พบในระบบ: <strong>" . 
                             htmlspecialchars(isset($grant_105_data->grant_user_name) ? $grant_105_data->grant_user_name : 'ไม่มีชื่อ') . 
                             "</strong>";
                        echo "</div>";
                    } else {
                        echo "<div class='error'>";
                        echo "❌ Grant ID 105 ไม่พบในตาราง tbl_grant_user<br>";
                        echo "<strong>แนะนำ:</strong> สร้าง Grant ID 105 ด้วยคำสั่ง:<br>";
                        echo "<code>INSERT INTO tbl_grant_user (grant_user_id, grant_user_name) VALUES (105, 'จัดการเรื่องร้องเรียน');</code>";
                        echo "</div>";
                    }
                } catch (Exception $db_error) {
                    echo "<div class='error'>";
                    echo "❌ ไม่สามารถตรวจสอบตาราง tbl_grant_user ได้: " . htmlspecialchars($db_error->getMessage());
                    echo "</div>";
                }
                
                // ✅ แสดงข้อมูล Grant ทั้งหมดในระบบ
                echo "<h3>📋 Grant ทั้งหมดในระบบ:</h3>";
                
                try {
                    $all_grants = $this->db->select('grant_user_id, grant_user_name')
                                          ->order_by('grant_user_id', 'ASC')
                                          ->get('tbl_grant_user')
                                          ->result();
                    
                    if ($all_grants && count($all_grants) > 0) {
                        echo "<table>";
                        echo "<tr><th>Grant ID</th><th>ชื่อ Grant</th></tr>";
                        
                        foreach ($all_grants as $grant) {
                            $highlight = (isset($grant->grant_user_id) && $grant->grant_user_id == 105) ? 'style="background: #fff3cd;"' : '';
                            echo "<tr {$highlight}>";
                            echo "<td>" . (isset($grant->grant_user_id) ? $grant->grant_user_id : 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars(isset($grant->grant_user_name) ? $grant->grant_user_name : 'N/A') . "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    } else {
                        echo "<div class='warning'>❌ ไม่พบ Grant ใดๆ ในระบบ</div>";
                    }
                } catch (Exception $db_error) {
                    echo "<div class='error'>";
                    echo "❌ ไม่สามารถดึงข้อมูล Grant ได้: " . htmlspecialchars($db_error->getMessage());
                    echo "</div>";
                }
            } else {
                echo "<div class='error'>❌ ไม่พบข้อมูล Member</div>";
            }
            
            // ✅ ข้อมูลสำหรับการแก้ไขปัญหา
            echo "<h2>🔧 คำแนะนำการแก้ไขปัญหา:</h2>";
            echo "<div class='info'>";
            echo "<h3>วิธีให้สิทธิ์ User Admin จัดการเรื่องร้องเรียน:</h3>";
            
            // ✅ ตรวจสอบว่าตาราง tbl_grant_user มีอยู่หรือไม่
            try {
                $table_exists = $this->db->table_exists('tbl_grant_user');
                
                if (!$table_exists) {
                    echo "<div class='error'>";
                    echo "<strong>❌ ตาราง tbl_grant_user ไม่พบในระบบ!</strong><br>";
                    echo "กรุณาสร้างตารางด้วยคำสั่ง SQL:<br>";
                    echo "<code>";
                    echo "CREATE TABLE `tbl_grant_user` (<br>";
                    echo "&nbsp;&nbsp;`grant_user_id` int(11) NOT NULL,<br>";
                    echo "&nbsp;&nbsp;`grant_user_name` varchar(255) NOT NULL COMMENT 'หัวข้อที่ต้องการให้แก้ไขได้',<br>";
                    echo "&nbsp;&nbsp;PRIMARY KEY (`grant_user_id`)<br>";
                    echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
                    echo "</code>";
                    echo "</div>";
                } else {
                    echo "<div class='success'>✅ ตาราง tbl_grant_user พบในระบบแล้ว</div>";
                }
                
                echo "<ol>";
                echo "<li><strong>สร้าง Grant ID 105</strong> (ถ้ายังไม่มี):<br>";
                echo "<code>INSERT INTO tbl_grant_user (grant_user_id, grant_user_name) VALUES (105, 'จัดการเรื่องร้องเรียน');</code></li>";
                echo "<li><strong>ให้สิทธิ์ User Admin</strong>:<br>";
                echo "<code>UPDATE tbl_member SET grant_user_ref_id = '105' WHERE ref_pid = 3 AND m_id = {$user_id};</code></li>";
                echo "<li><strong>ถ้ามี Grant อื่นอยู่แล้ว</strong>:<br>";
                echo "<code>UPDATE tbl_member SET grant_user_ref_id = CONCAT(IFNULL(grant_user_ref_id, ''), ',105') WHERE ref_pid = 3 AND m_id = {$user_id};</code></li>";
                echo "<li><strong>ตรวจสอบสิทธิ์หลังอัพเดท</strong>:<br>";
                echo "<code>SELECT m_id, m_fname, m_lname, ref_pid, grant_user_ref_id FROM tbl_member WHERE m_id = {$user_id};</code></li>";
                echo "</ol>";
                
            } catch (Exception $table_check_error) {
                echo "<div class='error'>";
                echo "❌ ไม่สามารถตรวจสอบตารางได้: " . htmlspecialchars($table_check_error->getMessage());
                echo "</div>";
            }
            
            echo "</div>";
            
            // ✅ ทดสอบสิทธิ์แบบต่างๆ
            echo "<h2>🧪 สรุปสิทธิ์:</h2>";
            echo "<table>";
            echo "<tr><th>สิทธิ์</th><th>สถานะ</th><th>หมายเหตุ</th></tr>";
            echo "<tr><td>ดูรายงาน</td><td>" . ($permissions['can_view_reports'] ? '✅ ได้' : '❌ ไม่ได้') . "</td>";
            echo "<td>" . ($permissions['can_view_reports'] ? 'สามารถเข้าดูรายงานได้' : 'ไม่สามารถเข้าดูรายงานได้') . "</td></tr>";
            echo "<tr><td>จัดการสถานะ</td><td>" . ($permissions['can_manage_status'] ? '✅ ได้' : '❌ ไม่ได้') . "</td>";
            echo "<td>" . ($permissions['can_manage_status'] ? 'สามารถเปลี่ยนสถานะเรื่องร้องเรียนได้' : 'ต้องมี Grant ID 105 เพื่อจัดการสถานะ') . "</td></tr>";
            echo "<tr><td>ลบข้อมูล</td><td>" . ($permissions['can_delete'] ? '✅ ได้' : '❌ ไม่ได้') . "</td>";
            echo "<td>" . ($permissions['can_delete'] ? 'สามารถลบเรื่องร้องเรียนได้' : 'เฉพาะ System Admin และ Super Admin เท่านั้น') . "</td></tr>";
            echo "</table>";
            
            // ✅ เพิ่มข้อมูลการแก้ไขด่วน
            echo "<h2>⚡ การแก้ไขด่วน:</h2>";
            if (!$permissions['can_manage_status'] && isset($permissions['member_data'])) {
                $member = $permissions['member_data'];
                if (isset($member->ref_pid) && $member->ref_pid == 3) {
                    echo "<div class='warning'>";
                    echo "<strong>🔧 User Admin ไม่มีสิทธิ์จัดการเรื่องร้องเรียน</strong><br>";
                    echo "รันคำสั่งนี้เพื่อให้สิทธิ์:<br>";
                    echo "<code>UPDATE tbl_member SET grant_user_ref_id = '105' WHERE m_id = {$user_id};</code>";
                    echo "</div>";
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
            echo "<pre style='margin-top: 10px; font-size: 12px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
        }
        
        // คืนค่า session เดิม
        $_SESSION['m_id'] = $original_session;
        
        echo "<hr>";
        echo "<p><a href='" . site_url('System_reports/complain') . "'>← กลับไปหน้ารายงาน</a></p>";
        echo "<p><a href='" . site_url('Debug_permissions') . "'>🔄 รีเฟรชหน้านี้</a></p>";
        echo "<p><small><strong>Debug URL:</strong> " . current_url() . "</small></p>";
        echo "<p><small><strong>เวลา:</strong> " . date('Y-m-d H:i:s') . "</small></p>";
        
        echo "</div>";
        echo "</body></html>";
    }
}
?>