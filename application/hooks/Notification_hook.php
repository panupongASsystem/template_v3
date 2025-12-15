<?php
// application/hooks/Notification_hook.php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Notification Hook - ปรับปรุงความปลอดภัย
 * ตรวจสอบและจัดการการแจ้งเตือนหลังจากโหลดหน้าเสร็จ
 */
class Notification_hook
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * ตรวจสอบการแจ้งเตือนหลังจากโหลดหน้าเสร็จ
     */
    public function check_notifications()
    {
        try {
            if (!$this->CI->session->userdata('m_id')) {
                return;
            }

            if (!$this->table_exists('tbl_notifications')) {
                if ($this->CI->session->userdata('m_system') === 'system_admin') {
                    $this->create_notifications_table();
                }
                return;
            }

            if (!isset($this->CI->Notification_lib)) {
                $this->CI->load->library('Notification_lib');
            }

            // ✅ เปลี่ยนเป็น Archive แทนการลบ
            $this->archive_old_notifications();

        } catch (Exception $e) {
            log_message('error', 'Notification Hook Error: ' . $e->getMessage());
        }
    }
	
	
	private function archive_old_notifications()
    {
        try {
            $last_archive = $this->CI->session->userdata('last_notification_archive');
            $archive_interval = 86400; // 24 ชั่วโมง
            
            if (!$last_archive || (time() - $last_archive) > $archive_interval) {
                
                if (!isset($this->CI->Notification_model)) {
                    $this->CI->load->model('Notification_model');
                }
                
                // Archive การแจ้งเตือนที่อ่านแล้วเก่ากว่า 90 วัน
                $archived_count = $this->CI->Notification_model->archive_old_read_notifications(90);
                
                $this->CI->session->set_userdata('last_notification_archive', time());
                
                if ($archived_count > 0) {
                    log_message('info', "Archived {$archived_count} old read notifications");
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Notification archive failed: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจสอบว่าตารางมีอยู่หรือไม่
     */
     private function table_exists($table_name)
    {
        try {
            return $this->CI->db->table_exists($table_name);
        } catch (Exception $e) {
            log_message('error', 'Table check error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างตาราง notifications อัตโนมัติ
     */
    private function create_notifications_table()
    {
        try {
            // ใช้โค้ดเดียวกับใน Model
            $this->CI->load->model('Notification_model');
            $result = $this->CI->Notification_model->create_table();
            
            if ($result) {
                log_message('info', 'Notifications table created successfully');
                $this->create_welcome_notification();
            }
            
            return $result;
            
        } catch (Exception $e) {
            log_message('error', 'Failed to create notifications table: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * สร้างการแจ้งเตือนต้อนรับเมื่อสร้างตารางใหม่
     */
    private function create_welcome_notification()
    {
        try {
            $welcome_data = [
                'type' => 'system',
                'title' => 'ระบบการแจ้งเตือนพร้อมใช้งาน',
                'message' => 'ระบบการแจ้งเตือนได้ถูกติดตั้งเรียบร้อยแล้ว ข้อมูลจะถูกเก็บไว้เป็น Log ถาวร',
                'priority' => 'normal',
                'icon' => 'fas fa-bell',
                'target_role' => 'system_admin',
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->CI->session->userdata('m_id') ?? 0
            ];

            $this->CI->db->insert('tbl_notifications', $welcome_data);
            
        } catch (Exception $e) {
            log_message('error', 'Failed to create welcome notification: ' . $e->getMessage());
        }
    }

    /**
     * ทำความสะอาดการแจ้งเตือนเก่า
     */
    private function cleanup_old_notifications()
    {
        try {
            // ตรวจสอบครั้งสุดท้ายที่ทำความสะอาด
            $last_cleanup = $this->CI->session->userdata('last_notification_cleanup');
            $cleanup_interval = 86400; // 24 ชั่วโมง
            
            // ทำความสะอาดเฉพาะเมื่อถึงเวลา
            if (!$last_cleanup || (time() - $last_cleanup) > $cleanup_interval) {
                
                // โหลด model เฉพาะเมื่อต้องใช้
                if (!isset($this->CI->Notification_model)) {
                    $this->CI->load->model('Notification_model');
                }
                
                // ทำความสะอาดการแจ้งเตือนเก่า (30 วัน)
                $cutoff_date = date('Y-m-d H:i:s', strtotime('-30 days'));
                
                $this->CI->db->where('created_at <', $cutoff_date);
                $this->CI->db->where('is_read', 1);
                $deleted_rows = $this->CI->db->delete('tbl_notifications');
                
                // บันทึกเวลาที่ทำความสะอาดล่าสุด
                $this->CI->session->set_userdata('last_notification_cleanup', time());
                
                if ($deleted_rows > 0) {
                    log_message('info', "Cleaned up {$deleted_rows} old notifications");
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Notification cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจสอบการแจ้งเตือนพิเศษ (เฉพาะกรณีจำเป็น)
     */
    public function check_critical_notifications()
    {
        try {
            // ตรวจสอบเฉพาะ system_admin
            if ($this->CI->session->userdata('m_system') !== 'system_admin') {
                return;
            }

            // ตรวจสอบว่ามีตารางหรือไม่
            if (!$this->table_exists('tbl_notifications')) {
                return;
            }

            // ตรวจสอบการแจ้งเตือนระดับ critical ที่ยังไม่อ่าน
            $this->CI->db->where('priority', 'critical');
            $this->CI->db->where('is_read', 0);
            $this->CI->db->where('target_role', 'system_admin');
            $critical_count = $this->CI->db->count_all_results('tbl_notifications');

            // ถ้ามีการแจ้งเตือนวิกฤติ ให้บันทึก log
            if ($critical_count > 0) {
                log_message('warning', "Found {$critical_count} unread critical notifications");
            }

        } catch (Exception $e) {
            log_message('error', 'Critical notification check failed: ' . $e->getMessage());
        }
    }
}

// ==============================================
// แก้ไขการตั้งค่า Hooks ให้ปลอดภัยมากขึ้น
// ==============================================

// application/config/hooks.php
$hook['post_controller'] = array(
    'class' => 'Notification_hook',
    'function' => 'check_notifications',
    'filename' => 'Notification_hook.php',
    'filepath' => 'hooks'
);

// เพิ่ม hook สำหรับตรวจสอบการแจ้งเตือนวิกฤติ (ถ้าต้องการ)
$hook['post_system'] = array(
    'class' => 'Notification_hook',
    'function' => 'check_critical_notifications',
    'filename' => 'Notification_hook.php',
    'filepath' => 'hooks'
);

// ==============================================
// สำรองวิธีการติดตั้งตารางด้วยตนเอง
// ==============================================

// สร้างไฟล์ application/controllers/Install_notification.php
class Install_notification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // ตรวจสอบสิทธิ์ system_admin เท่านั้น
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_404();
            return;
        }
    }

    /**
     * หน้าติดตั้งระบบการแจ้งเตือน
     */
    public function index()
    {
        $data['table_exists'] = $this->db->table_exists('tbl_notifications');
        $data['page_title'] = 'ติดตั้งระบบการแจ้งเตือน';
        
        // ถ้ามีตารางแล้ว ให้แสดงสถิติ
        if ($data['table_exists']) {
            $data['notification_stats'] = $this->get_notification_stats();
        }
        
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/install_notification', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ติดตั้งตารางการแจ้งเตือน
     */
    public function install()
    {
        try {
            // ตรวจสอบว่ามีตารางอยู่แล้วหรือไม่
            if ($this->db->table_exists('tbl_notifications')) {
                $this->session->set_flashdata('error', 'ตารางการแจ้งเตือนมีอยู่แล้ว');
                redirect('Install_notification');
                return;
            }

            // สร้างตาราง
            $sql = "CREATE TABLE `tbl_notifications` (
                `notification_id` int(11) NOT NULL AUTO_INCREMENT,
                `type` varchar(50) NOT NULL COMMENT 'ประเภทการแจ้งเตือน',
                `title` varchar(255) NOT NULL COMMENT 'หัวข้อ',
                `message` text NOT NULL COMMENT 'ข้อความ',
                `reference_id` int(11) DEFAULT NULL COMMENT 'ID อ้างอิง',
                `reference_table` varchar(100) DEFAULT NULL COMMENT 'ตารางอ้างอิง',
                `target_user_id` int(11) DEFAULT NULL COMMENT 'ผู้ใช้เป้าหมาย',
                `target_role` varchar(50) DEFAULT NULL COMMENT 'บทบาทเป้าหมาย',
                `priority` enum('low','normal','high','critical') DEFAULT 'normal',
                `icon` varchar(100) DEFAULT 'fas fa-bell',
                `url` varchar(500) DEFAULT NULL COMMENT 'ลิงก์ที่เกี่ยวข้อง',
                `data` text DEFAULT NULL COMMENT 'ข้อมูลเพิ่มเติม JSON',
                `is_read` tinyint(1) DEFAULT 0,
                `is_system` tinyint(1) DEFAULT 1,
                `created_at` datetime NOT NULL,
                `created_by` int(11) DEFAULT NULL,
                `read_at` datetime DEFAULT NULL,
                `read_by` int(11) DEFAULT NULL,
                PRIMARY KEY (`notification_id`),
                KEY `idx_target_user` (`target_user_id`),
                KEY `idx_target_role` (`target_role`),
                KEY `idx_type` (`type`),
                KEY `idx_created_at` (`created_at`),
                KEY `idx_is_read` (`is_read`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $result = $this->db->query($sql);

            if ($result) {
                // สร้างการแจ้งเตือนต้อนรับ
                $this->create_sample_notifications();
                
                $this->session->set_flashdata('success', 'ติดตั้งระบบการแจ้งเตือนเรียบร้อยแล้ว');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการสร้างตาราง');
            }

        } catch (Exception $e) {
            log_message('error', 'Install notification error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }

        redirect('Install_notification');
    }

    /**
     * สร้างการแจ้งเตือนตัวอย่าง
     */
    private function create_sample_notifications()
    {
        $notifications = [
            [
                'type' => 'system',
                'title' => 'ระบบการแจ้งเตือนพร้อมใช้งาน',
                'message' => 'ระบบการแจ้งเตือนได้ถูกติดตั้งและพร้อมใช้งานแล้ว',
                'priority' => 'normal',
                'icon' => 'fas fa-check-circle',
                'target_role' => 'system_admin',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata('m_id')
            ],
            [
                'type' => 'system',
                'title' => 'คำแนะนำการใช้งาน',
                'message' => 'คุณสามารถดูการแจ้งเตือนทั้งหมดได้ที่ไอคอนระฆังด้านบน',
                'priority' => 'low',
                'icon' => 'fas fa-info-circle',
                'target_role' => 'system_admin',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata('m_id')
            ]
        ];

        foreach ($notifications as $notification) {
            $this->db->insert('tbl_notifications', $notification);
        }
    }

    /**
     * ดึงสถิติการแจ้งเตือน
     */
    private function get_notification_stats()
    {
        $stats = [];
        
        // จำนวนทั้งหมด
        $stats['total'] = $this->db->count_all('tbl_notifications');
        
        // จำนวนที่ยังไม่อ่าน
        $this->db->where('is_read', 0);
        $stats['unread'] = $this->db->count_all_results('tbl_notifications');
        
        // จำนวนตามประเภท
        $types = ['storage', 'complain', 'queue', 'suggestion', 'qa', 'system'];
        foreach ($types as $type) {
            $this->db->where('type', $type);
            $stats['by_type'][$type] = $this->db->count_all_results('tbl_notifications');
        }
        
        // จำนวนตาม priority
        $priorities = ['critical', 'high', 'normal', 'low'];
        foreach ($priorities as $priority) {
            $this->db->where('priority', $priority);
            $stats['by_priority'][$priority] = $this->db->count_all_results('tbl_notifications');
        }
        
        return $stats;
    }

    /**
     * ลบตารางการแจ้งเตือน (สำหรับทดสอบ)
     */
    public function uninstall()
    {
        if ($this->input->post('confirm') === 'yes') {
            try {
                $this->db->query('DROP TABLE IF EXISTS tbl_notifications');
                $this->session->set_flashdata('success', 'ลบระบบการแจ้งเตือนเรียบร้อยแล้ว');
            } catch (Exception $e) {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            }
        } else {
            $this->session->set_flashdata('error', 'กรุณายืนยันการลบ');
        }
        
        redirect('Install_notification');
    }
}