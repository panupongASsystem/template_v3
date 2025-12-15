<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Corruption Model
 * จัดการข้อมูลระบบแจ้งเรื่องร้องเรียนการทุจริต
 */
class Corruption_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // โหลด LINE Notification Library
        $this->load->library('line_notification');
    }

    // ===================================================================
    // *** การจัดการรายงานการทุจริต ***
    // ===================================================================

    /**
     * เพิ่มรายงานการทุจริตใหม่
     */
    public function add_corruption_report($data)
    {
        try {
            log_message('info', 'Starting add_corruption_report...');

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($data['complaint_subject']) || empty($data['complaint_details']) || empty($data['perpetrator_name'])) {
                log_message('error', 'Missing required fields in corruption report data');
                return false;
            }

            $this->db->trans_start();

            // ตรวจสอบว่า Table มีอยู่จริง
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return false;
            }

            // เตรียมข้อมูลสำหรับบันทึก - ระมัดระวังเรื่อง null values
            $report_data = [
                'corruption_report_id' => isset($data['corruption_report_id']) ? $data['corruption_report_id'] : null,
                'corruption_type' => $data['corruption_type'],
                'corruption_type_other' => isset($data['corruption_type_other']) ? $data['corruption_type_other'] : null,
                'complaint_subject' => $data['complaint_subject'],
                'complaint_details' => $data['complaint_details'],
                'incident_date' => (!empty($data['incident_date']) && $data['incident_date'] !== '') ? $data['incident_date'] : null,
                'incident_time' => (!empty($data['incident_time']) && $data['incident_time'] !== '') ? $data['incident_time'] : null,
                'incident_location' => isset($data['incident_location']) ? $data['incident_location'] : null,
                'perpetrator_name' => $data['perpetrator_name'],
                'perpetrator_department' => isset($data['perpetrator_department']) ? $data['perpetrator_department'] : null,
                'perpetrator_position' => isset($data['perpetrator_position']) ? $data['perpetrator_position'] : null,
                'other_involved' => isset($data['other_involved']) ? $data['other_involved'] : null,
                'evidence_description' => isset($data['evidence_description']) ? $data['evidence_description'] : null,
                'evidence_file_count' => isset($data['evidence_file_count']) ? intval($data['evidence_file_count']) : 0,
                'is_anonymous' => isset($data['is_anonymous']) ? ($data['is_anonymous'] ? 1 : 0) : 0,
                'reporter_name' => isset($data['reporter_name']) ? $data['reporter_name'] : null,
                'reporter_phone' => isset($data['reporter_phone']) ? $data['reporter_phone'] : null,
                'reporter_email' => isset($data['reporter_email']) ? $data['reporter_email'] : null,
                'reporter_position' => isset($data['reporter_position']) ? $data['reporter_position'] : null,
                'reporter_relation' => isset($data['reporter_relation']) ? $data['reporter_relation'] : null,
                'reporter_user_id' => isset($data['reporter_user_id']) ? $data['reporter_user_id'] : null,
                'reporter_user_type' => isset($data['reporter_user_type']) ? $data['reporter_user_type'] : 'guest',
                'report_status' => 'pending',
                'priority_level' => isset($data['priority_level']) ? $data['priority_level'] : 'normal',
                'ip_address' => isset($data['ip_address']) ? $data['ip_address'] : null,
                'user_agent' => isset($data['user_agent']) ? $data['user_agent'] : null,
                'created_by' => isset($data['created_by']) ? $data['created_by'] : 'System',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Log ข้อมูลที่จะบันทึก (ไม่รวมข้อมูลอ่อนไหว)
            $log_data = $report_data;
            if (isset($log_data['reporter_phone']))
                $log_data['reporter_phone'] = 'xxxxx';
            if (isset($log_data['reporter_email']))
                $log_data['reporter_email'] = 'xxxxx';
            log_message('info', 'Attempting to insert corruption report: ' . json_encode($log_data));

            // ตรวจสอบความยาวของข้อมูล
            if (strlen($report_data['complaint_subject']) > 500) {
                log_message('error', 'Complaint subject too long');
                return false;
            }

            if (strlen($report_data['perpetrator_name']) > 255) {
                log_message('error', 'Perpetrator name too long');
                return false;
            }

            // บันทึกข้อมูล
            $result = $this->db->insert('tbl_corruption_reports', $report_data);

            if (!$result) {
                $error = $this->db->error();
                log_message('error', 'Database insert failed: ' . json_encode($error));
                log_message('error', 'Last query: ' . $this->db->last_query());
                return false;
            }

            $corruption_id = $this->db->insert_id();

            if (!$corruption_id) {
                log_message('error', 'Failed to get insert ID');
                return false;
            }

            // บันทึก history (ถ้าทำได้)
            try {
                $this->add_corruption_history_safe(
                    $corruption_id,
                    'created',
                    'รายงานการทุจริตถูกสร้างขึ้น',
                    $data['created_by'] ?? 'System',
                    $data['reporter_user_id'] ?? null
                );
            } catch (Exception $e) {
                log_message('warning', 'Failed to add history: ' . $e->getMessage());
                // ไม่ให้ fail เพราะ history ไม่ใช่สิ่งจำเป็น
            }

            $this->db->trans_complete();
            
            log_message('info', "Line notification send by Corruption ID : {$corruption_id}");
            $this->line_notification->send_line_corruption_notification($corruption_id);

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed in add_corruption_report');
                return false;
            }

            log_message('info', 'Corruption report created successfully with ID: ' . $corruption_id);
            return $corruption_id;

        } catch (Exception $e) {
            if (isset($this->db)) {
                $this->db->trans_rollback();
            }
            log_message('error', 'Exception in add_corruption_report: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }






    public function add_corruption_history_safe($corruption_id, $action_type, $action_description, $action_by, $action_by_user_id = null, $old_value = null, $new_value = null)
    {
        try {
            // ตรวจสอบว่า Table มีอยู่จริง
            if (!$this->db->table_exists('tbl_corruption_history')) {
                log_message('info', 'Table tbl_corruption_history does not exist, skipping history log');
                return true; // ไม่ให้เป็น error เพราะ history ไม่จำเป็นต้องมี
            }

            $history_data = [
                'corruption_id' => $corruption_id,
                'action_type' => $action_type,
                'old_value' => $old_value,
                'new_value' => $new_value,
                'action_description' => $action_description,
                'action_by' => $action_by,
                'action_by_user_id' => $action_by_user_id,
                'action_date' => date('Y-m-d H:i:s'),
                'ip_address' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null
            ];

            $result = $this->db->insert('tbl_corruption_history', $history_data);

            if (!$result) {
                log_message('warning', 'Failed to insert corruption history');
                return false;
            }

            return $this->db->insert_id();

        } catch (Exception $e) {
            log_message('error', 'Error adding corruption history: ' . $e->getMessage());
            return false; // ไม่ throw exception เพราะ history ไม่ใช่ส่วนสำคัญ
        }
    }





    /**
     * อัปเดตการตั้งค่า
     */
    public function update_corruption_setting($key, $value, $updated_by)
    {
        try {
            $this->db->where('setting_key', $key);
            $result = $this->db->update('tbl_corruption_settings', [
                'setting_value' => $value,
                'updated_by' => $updated_by,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error updating corruption setting: ' . $e->getMessage());
            return false;
        }
    }




    public function count_total_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('warning', 'Table tbl_corruption_reports does not exist');
                return 0;
            }

            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->total);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting total reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานที่แก้ไขแล้ว
     * Method ที่ Controller เรียกใช้แต่ไม่มีใน Model
     */
    public function count_resolved_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('warning', 'Table tbl_corruption_reports does not exist');
                return 0;
            }

            $this->db->select('COUNT(*) as resolved');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('report_status', 'resolved');
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->resolved);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting resolved reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานในเดือนนี้
     * Method ที่ Controller เรียกใช้แต่ไม่มีใน Model
     */
    public function count_this_month_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('warning', 'Table tbl_corruption_reports does not exist');
                return 0;
            }

            $this->db->select('COUNT(*) as this_month');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('YEAR(created_at)', date('Y'));
            $this->db->where('MONTH(created_at)', date('n'));
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->this_month);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting this month reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานในสัปดาห์นี้
     * Method เสริมสำหรับสถิติ
     */
    public function count_this_week_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return 0;
            }

            $this->db->select('COUNT(*) as this_week');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('DATE(created_at) >=', date('Y-m-d', strtotime('-7 days')));
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->this_week);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting this week reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานในวันนี้
     * Method เสริมสำหรับสถิติ
     */
    public function count_today_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return 0;
            }

            $this->db->select('COUNT(*) as today');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('DATE(created_at)', date('Y-m-d'));
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->today);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting today reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานตามสถานะ
     * Method เสริมสำหรับสถิติ
     */
    public function count_reports_by_status($status)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return 0;
            }

            $allowed_statuses = ['pending', 'under_review', 'investigating', 'resolved', 'dismissed', 'closed'];

            if (!in_array($status, $allowed_statuses)) {
                log_message('warning', 'Invalid status provided: ' . $status);
                return 0;
            }

            $this->db->select('COUNT(*) as count');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('report_status', $status);
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->count);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', "Error counting reports by status {$status}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานตามประเภทการทุจริต
     * Method เสริมสำหรับสถิติ
     */
    public function count_reports_by_type($type)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return 0;
            }

            $allowed_types = ['embezzlement', 'bribery', 'abuse_of_power', 'conflict_of_interest', 'procurement_fraud', 'other'];

            if (!in_array($type, $allowed_types)) {
                log_message('warning', 'Invalid corruption type provided: ' . $type);
                return 0;
            }

            $this->db->select('COUNT(*) as count');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('corruption_type', $type);
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->count);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', "Error counting reports by type {$type}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงสถิติรวมแบบครบถ้วน
     * Method สำหรับแทนที่การเรียก methods แยกใน Controller
     */
    public function get_comprehensive_statistics()
    {
        try {
            $stats = [
                'total_reports' => $this->count_total_reports(),
                'resolved_reports' => $this->count_resolved_reports(),
                'this_month_reports' => $this->count_this_month_reports(),
                'this_week_reports' => $this->count_this_week_reports(),
                'today_reports' => $this->count_today_reports(),

                // สถิติตามสถานะ
                'pending_reports' => $this->count_reports_by_status('pending'),
                'under_review_reports' => $this->count_reports_by_status('under_review'),
                'investigating_reports' => $this->count_reports_by_status('investigating'),
                'dismissed_reports' => $this->count_reports_by_status('dismissed'),
                'closed_reports' => $this->count_reports_by_status('closed'),

                // สถิติตามประเภท
                'embezzlement_reports' => $this->count_reports_by_type('embezzlement'),
                'bribery_reports' => $this->count_reports_by_type('bribery'),
                'abuse_of_power_reports' => $this->count_reports_by_type('abuse_of_power'),
                'conflict_of_interest_reports' => $this->count_reports_by_type('conflict_of_interest'),
                'procurement_fraud_reports' => $this->count_reports_by_type('procurement_fraud'),
                'other_reports' => $this->count_reports_by_type('other'),

                // สถิติพิเศษ
                'anonymous_reports' => $this->count_anonymous_reports(),
                'high_priority_reports' => $this->count_high_priority_reports(),
                'urgent_reports' => $this->count_urgent_reports()
            ];

            // เพิ่มเปอร์เซ็นต์ความสำเร็จ
            if ($stats['total_reports'] > 0) {
                $stats['resolution_rate'] = round(($stats['resolved_reports'] / $stats['total_reports']) * 100, 2);
            } else {
                $stats['resolution_rate'] = 0;
            }

            // เพิ่มข้อมูลเวลา
            $stats['generated_at'] = date('Y-m-d H:i:s');
            $stats['last_updated'] = $this->get_last_report_date();

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error getting comprehensive statistics: ' . $e->getMessage());
            return $this->get_default_statistics_array();
        }
    }

    /**
     * นับจำนวนรายงานไม่ระบุตัวตน
     */
    public function count_anonymous_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return 0;
            }

            $this->db->select('COUNT(*) as count');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('is_anonymous', 1);
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->count);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting anonymous reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานระดับสูง
     */
    public function count_high_priority_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return 0;
            }

            $this->db->select('COUNT(*) as count');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('priority_level', 'high');
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->count);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting high priority reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนรายงานเร่งด่วน
     */
    public function count_urgent_reports()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return 0;
            }

            $this->db->select('COUNT(*) as count');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('priority_level', 'urgent');
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return intval($query->row()->count);
            }

            return 0;

        } catch (Exception $e) {
            log_message('error', 'Error counting urgent reports: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงวันที่ของรายงานล่าสุด
     */
    public function get_last_report_date()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return null;
            }

            $this->db->select('MAX(created_at) as last_date');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('is_archived', 0);
            $query = $this->db->get();

            if ($query && $query->num_rows() > 0) {
                return $query->row()->last_date;
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Error getting last report date: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ส่งคืนสถิติเริ่มต้นแบบ array
     */
    private function get_default_statistics_array()
    {
        return [
            'total_reports' => 0,
            'resolved_reports' => 0,
            'this_month_reports' => 0,
            'this_week_reports' => 0,
            'today_reports' => 0,
            'pending_reports' => 0,
            'under_review_reports' => 0,
            'investigating_reports' => 0,
            'dismissed_reports' => 0,
            'closed_reports' => 0,
            'embezzlement_reports' => 0,
            'bribery_reports' => 0,
            'abuse_of_power_reports' => 0,
            'conflict_of_interest_reports' => 0,
            'procurement_fraud_reports' => 0,
            'other_reports' => 0,
            'anonymous_reports' => 0,
            'high_priority_reports' => 0,
            'urgent_reports' => 0,
            'resolution_rate' => 0,
            'generated_at' => date('Y-m-d H:i:s'),
            'last_updated' => null
        ];
    }

    /**
     * ตรวจสอบการมีอยู่ของ table ที่จำเป็น
     */
    public function check_required_tables()
    {
        $required_tables = [
            'tbl_corruption_reports',
            'tbl_corruption_files',
            'tbl_corruption_history',
            'tbl_corruption_tracking',
            'tbl_corruption_settings'
        ];

        $status = [];

        try {
            foreach ($required_tables as $table) {
                $status[$table] = $this->db->table_exists($table);
            }
        } catch (Exception $e) {
            log_message('error', 'Error checking required tables: ' . $e->getMessage());
            foreach ($required_tables as $table) {
                $status[$table] = false;
            }
        }

        return $status;
    }

    /**
     * สร้าง View สำหรับสถิติ (สำหรับ optimize performance)
     */
    public function create_statistics_view()
    {
        try {
            // สร้าง View สำหรับสถิติ
            $sql = "
        CREATE OR REPLACE VIEW view_corruption_statistics AS
        SELECT 
            COUNT(*) as total_reports,
            COUNT(CASE WHEN report_status = 'pending' THEN 1 END) as pending_reports,
            COUNT(CASE WHEN report_status = 'under_review' THEN 1 END) as under_review_reports,
            COUNT(CASE WHEN report_status = 'investigating' THEN 1 END) as investigating_reports,
            COUNT(CASE WHEN report_status = 'resolved' THEN 1 END) as resolved_reports,
            COUNT(CASE WHEN report_status = 'dismissed' THEN 1 END) as dismissed_reports,
            COUNT(CASE WHEN report_status = 'closed' THEN 1 END) as closed_reports,
            COUNT(CASE WHEN is_anonymous = 1 THEN 1 END) as anonymous_reports,
            COUNT(CASE WHEN corruption_type = 'embezzlement' THEN 1 END) as embezzlement_reports,
            COUNT(CASE WHEN corruption_type = 'bribery' THEN 1 END) as bribery_reports,
            COUNT(CASE WHEN corruption_type = 'abuse_of_power' THEN 1 END) as abuse_of_power_reports,
            COUNT(CASE WHEN corruption_type = 'conflict_of_interest' THEN 1 END) as conflict_of_interest_reports,
            COUNT(CASE WHEN corruption_type = 'procurement_fraud' THEN 1 END) as procurement_fraud_reports,
            COUNT(CASE WHEN priority_level = 'high' THEN 1 END) as high_priority_reports,
            COUNT(CASE WHEN priority_level = 'urgent' THEN 1 END) as urgent_reports,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_reports,
            COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as week_reports,
            COUNT(CASE WHEN YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) THEN 1 END) as month_reports,
            MAX(created_at) as last_updated
        FROM tbl_corruption_reports 
        WHERE is_archived = 0
        ";

            $result = $this->db->query($sql);

            if ($result) {
                log_message('info', 'Corruption statistics view created successfully');
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error creating statistics view: ' . $e->getMessage());
            return false;
        }
    }

    // ===== เพิ่ม methods สำหรับการจัดการข้อผิดพลาดและ logging =====

    /**
     * บันทึกข้อผิดพลาดของระบบ
     */
    public function log_system_error($error_type, $error_message, $error_details = [])
    {
        try {
            log_message('error', "Corruption System Error [{$error_type}]: {$error_message}");
            if (!empty($error_details)) {
                log_message('error', 'Error details: ' . json_encode($error_details, JSON_UNESCAPED_UNICODE));
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * บันทึกการเข้าถึงหน้า
     */
    public function log_page_access($page_name, $user_info, $ip_address, $user_agent)
    {
        try {
            log_message('info', "Page access: {$page_name} by " . ($user_info['user_type'] ?? 'guest') . " from {$ip_address}");
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error logging page access: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * ดึงการตั้งค่าทั้งหมดในกลุ่ม
     */
    public function get_corruption_settings_by_group($group = 'general')
    {
        try {
            $this->db->select('setting_key, setting_value, setting_type, setting_description');
            $this->db->from('tbl_corruption_settings');
            $this->db->where('setting_group', $group);
            $this->db->where('is_active', 1);
            $this->db->order_by('setting_key', 'ASC');

            $query = $this->db->get();
            $settings = $query->result();

            $result = [];
            foreach ($settings as $setting) {
                $key = $setting->setting_key;

                // แปลงค่าตามประเภท
                switch ($setting->setting_type) {
                    case 'number':
                        $value = floatval($setting->setting_value);
                        break;
                    case 'boolean':
                        $value = $setting->setting_value === '1' || $setting->setting_value === 'true';
                        break;
                    case 'json':
                        $value = json_decode($setting->setting_value, true);
                        break;
                    default:
                        $value = $setting->setting_value;
                }

                $result[$key] = $value;
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption settings by group: ' . $e->getMessage());
            return [];
        }
    }

    // ===================================================================
    // *** ฟังก์ชันช่วยเหลือ ***
    // ===================================================================

    /**
     * ตรวจสอบว่ามีรายงานนี้อยู่หรือไม่
     */
    public function corruption_report_exists($report_id)
    {
        try {
            $this->db->where('corruption_report_id', $report_id);
            $this->db->where('is_archived', 0);
            $count = $this->db->count_all_results('tbl_corruption_reports');

            return $count > 0;

        } catch (Exception $e) {
            log_message('error', 'Error checking corruption report exists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายงานที่ผู้ใช้มีสิทธิ์เข้าถึง
     */
    public function get_user_accessible_reports($user_id, $user_type, $filters = [], $limit = 20, $offset = 0)
    {
        try {
            // เปลี่ยนจากการใช้ CASE statement ใน SQL เป็นการจัดการใน PHP
            $this->db->select('cr.*');
            $this->db->from('tbl_corruption_reports cr');
            $this->db->where('cr.is_archived', 0);

            // กรองตามสิทธิ์ผู้ใช้
            if ($user_type === 'public') {
                // สมาชิกดูได้เฉพาะรายงานของตนเอง
                $this->db->where('cr.reporter_user_id', $user_id);
                $this->db->where('cr.reporter_user_type', 'public');
            } elseif ($user_type === 'staff') {
                // เจ้าหน้าที่ดูได้รายงานที่ถูกมอบหมาย หรือทั้งหมดถ้าเป็น admin
                $this->db->group_start();
                $this->db->where('cr.assigned_to', $user_id);
                $this->db->or_where('1=1'); // admin ดูได้ทั้งหมด (ควรเพิ่มเงื่อนไขตรวจสอบ admin)
                $this->db->group_end();
            }

            // ใช้ตัวกรอง
            $this->apply_corruption_filters($filters);

            // เรียงลำดับ
            $this->db->order_by('cr.created_at', 'DESC');

            // จำกัดจำนวน
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();
            $reports = $query->result();

            // จัดการชื่อผู้แจ้งใน PHP แทนการใช้ CASE statement ใน SQL
            foreach ($reports as &$report) {
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }
            }

            // นับจำนวนทั้งหมด
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_corruption_reports cr');
            $this->db->where('cr.is_archived', 0);

            // กรองตามสิทธิ์ผู้ใช้ (ซ้ำ)
            if ($user_type === 'public') {
                $this->db->where('cr.reporter_user_id', $user_id);
                $this->db->where('cr.reporter_user_type', 'public');
            } elseif ($user_type === 'staff') {
                $this->db->group_start();
                $this->db->where('cr.assigned_to', $user_id);
                $this->db->or_where('1=1'); // admin
                $this->db->group_end();
            }

            $this->apply_corruption_filters($filters);
            $total_query = $this->db->get();
            $total = $total_query->row()->total;

            return [
                'data' => $reports,
                'total' => $total
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting user accessible reports: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * ดึงรายการเจ้าหน้าที่ที่สามารถมอบหมายได้
     */
    public function get_assignable_staff()
    {
        try {
            if (!$this->db->table_exists('tbl_member') || !$this->db->table_exists('tbl_position')) {
                return [];
            }

            $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_email, COALESCE(p.pname, "เจ้าหน้าที่") as pname');
            $this->db->from('tbl_member m');
            $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
            $this->db->where('m.m_status', '1');
            $this->db->where_in('m.m_system', ['user_admin', 'super_admin', 'system_admin']);
            $this->db->order_by('m.m_fname', 'ASC');

            $query = $this->db->get();
            return $query ? $query->result() : [];

        } catch (Exception $e) {
            log_message('error', 'Error getting assignable staff: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงสถิติรายเดือน
     */
    public function get_monthly_corruption_statistics($months = 12)
    {
        try {
            $this->db->select('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total_reports,
                COUNT(CASE WHEN report_status = "resolved" THEN 1 END) as resolved_reports,
                COUNT(CASE WHEN corruption_type = "bribery" THEN 1 END) as bribery_reports,
                COUNT(CASE WHEN corruption_type = "embezzlement" THEN 1 END) as embezzlement_reports
            ');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('is_archived', 0);
            $this->db->where('created_at >=', date('Y-m-d', strtotime("-{$months} months")));
            $this->db->group_by('YEAR(created_at), MONTH(created_at)');
            $this->db->order_by('year DESC, month DESC');

            $query = $this->db->get();
            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting monthly corruption statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ค้นหารายงานขั้นสูง
     */
    public function advanced_search_corruption_reports($search_params)
    {
        try {
            $this->db->select('
                cr.*,
                CASE 
                    WHEN cr.is_anonymous = 1 THEN "ไม่ระบุตัวตน" 
                    ELSE cr.reporter_name 
                END as display_reporter_name
            ');
            $this->db->from('tbl_corruption_reports cr');
            $this->db->where('cr.is_archived', 0);

            // ค้นหาในหลายฟิลด์
            if (!empty($search_params['keyword'])) {
                $keyword = $this->db->escape_like_str($search_params['keyword']);
                $this->db->group_start();
                $this->db->like('cr.corruption_report_id', $keyword);
                $this->db->or_like('cr.complaint_subject', $keyword);
                $this->db->or_like('cr.complaint_details', $keyword);
                $this->db->or_like('cr.perpetrator_name', $keyword);
                $this->db->or_like('cr.perpetrator_department', $keyword);
                $this->db->or_like('cr.incident_location', $keyword);
                $this->db->group_end();
            }

            // ค้นหาตามประเภทการทุจริต
            if (!empty($search_params['corruption_types']) && is_array($search_params['corruption_types'])) {
                $this->db->where_in('cr.corruption_type', $search_params['corruption_types']);
            }

            // ค้นหาตามสถานะ
            if (!empty($search_params['statuses']) && is_array($search_params['statuses'])) {
                $this->db->where_in('cr.report_status', $search_params['statuses']);
            }

            // ค้นหาตามช่วงวันที่เกิดเหตุ
            if (!empty($search_params['incident_date_from'])) {
                $this->db->where('cr.incident_date >=', $search_params['incident_date_from']);
            }
            if (!empty($search_params['incident_date_to'])) {
                $this->db->where('cr.incident_date <=', $search_params['incident_date_to']);
            }

            // ค้นหาตามช่วงวันที่รายงาน
            if (!empty($search_params['report_date_from'])) {
                $this->db->where('DATE(cr.created_at) >=', $search_params['report_date_from']);
            }
            if (!empty($search_params['report_date_to'])) {
                $this->db->where('DATE(cr.created_at) <=', $search_params['report_date_to']);
            }

            // ค้นหาตามระดับความสำคัญ
            if (!empty($search_params['priority_levels']) && is_array($search_params['priority_levels'])) {
                $this->db->where_in('cr.priority_level', $search_params['priority_levels']);
            }

            // ค้นหาเฉพาะรายงานไม่ระบุตัวตน
            if (isset($search_params['anonymous_only']) && $search_params['anonymous_only']) {
                $this->db->where('cr.is_anonymous', 1);
            }

            // เรียงลำดับ
            $order_by = $search_params['order_by'] ?? 'created_at';
            $order_direction = $search_params['order_direction'] ?? 'DESC';
            $this->db->order_by("cr.{$order_by}", $order_direction);

            // จำกัดจำนวน
            $limit = $search_params['limit'] ?? 50;
            $offset = $search_params['offset'] ?? 0;
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();
            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error in advanced search corruption reports: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ส่งออกข้อมูลรายงานการทุจริต
     */
    public function export_corruption_reports($filters = [])
    {
        try {
            $this->db->select('
            cr.corruption_report_id,
            cr.corruption_type,
            cr.complaint_subject,
            cr.complaint_details,
            cr.incident_date,
            cr.incident_time,
            cr.incident_location,
            cr.perpetrator_name,
            cr.perpetrator_department,
            cr.perpetrator_position,
            cr.report_status,
            cr.priority_level,
            cr.is_anonymous,
            cr.reporter_name,
            cr.reporter_phone,
            cr.reporter_email,
            cr.evidence_file_count,
            cr.assigned_department,
            cr.response_message,
            cr.response_by,
            cr.response_date,
            cr.created_at,
            cr.updated_at
        ');
            $this->db->from('tbl_corruption_reports cr');
            $this->db->where('cr.is_archived', 0);

            // ใช้ตัวกรอง
            $this->apply_corruption_filters($filters);

            // เรียงลำดับ
            $this->db->order_by('cr.created_at', 'DESC');

            $query = $this->db->get();
            $reports = $query->result();

            // จัดการชื่อผู้แจ้งหลังจากดึงข้อมูล
            foreach ($reports as &$report) {
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }
            }

            return $reports;

        } catch (Exception $e) {
            log_message('error', 'Error exporting corruption reports: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ลบรายงานการทุจริต (Archive)
     */
    public function archive_corruption_report($corruption_id, $archived_by)
    {
        try {
            $this->db->where('corruption_id', $corruption_id);
            $result = $this->db->update('tbl_corruption_reports', [
                'is_archived' => 1,
                'archived_at' => date('Y-m-d H:i:s'),
                'updated_by' => $archived_by,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error archiving corruption report: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * คืนค่ารายงานจาก Archive
     */
    public function restore_corruption_report($corruption_id, $restored_by)
    {
        try {
            $this->db->where('corruption_id', $corruption_id);
            $result = $this->db->update('tbl_corruption_reports', [
                'is_archived' => 0,
                'archived_at' => null,
                'updated_by' => $restored_by,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error restoring corruption report: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงรายงาน
     */
    public function check_report_access($corruption_id, $user_id, $user_type)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return false;
            }

            if ($user_type === 'staff' || $user_type === 'admin') {
                // เจ้าหน้าที่และ admin สามารถเข้าถึงได้ทั้งหมด (ขึ้นอยู่กับสิทธิ์)
                return true;
            } elseif ($user_type === 'public') {
                // สมาชิกสามารถเข้าถึงเฉพาะรายงานของตนเอง
                $this->db->select('corruption_id');
                $this->db->from('tbl_corruption_reports');
                $this->db->where('corruption_id', intval($corruption_id));
                $this->db->where('reporter_user_id', intval($user_id));
                $this->db->where('reporter_user_type', 'public');
                $this->db->where('is_archived', 0);

                return $this->db->count_all_results() > 0;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking report access: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * รายงานที่ต้องการการดำเนินการด่วน
     */
    public function get_urgent_corruption_reports()
    {
        try {
            $this->db->select('
                corruption_id,
                corruption_report_id,
                complaint_subject,
                corruption_type,
                priority_level,
                report_status,
                created_at,
                DATEDIFF(NOW(), created_at) as days_pending
            ');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('is_archived', 0);
            $this->db->group_start();
            $this->db->where('priority_level', 'urgent');
            $this->db->or_where('priority_level', 'high');
            $this->db->or_where('(report_status = "pending" AND DATEDIFF(NOW(), created_at) > 7)');
            $this->db->or_where('(report_status = "under_review" AND DATEDIFF(NOW(), created_at) > 14)');
            $this->db->group_end();
            $this->db->order_by('priority_level DESC, created_at ASC');

            $query = $this->db->get();
            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting urgent corruption reports: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * ดึงรายงานการทุจริตตาม ID
     */
    public function get_corruption_report_by_id($corruption_id)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return null;
            }

            if (empty($corruption_id) || !is_numeric($corruption_id)) {
                log_message('error', 'Invalid corruption_id provided: ' . var_export($corruption_id, true));
                return null;
            }

            $this->db->select('*');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('corruption_id', intval($corruption_id));
            $this->db->where('is_archived', 0);

            $query = $this->db->get();

            if (!$query) {
                log_message('error', 'Query failed in get_corruption_report_by_id');
                return null;
            }

            $report = $query->row();

            if ($report) {
                // ดึงไฟล์หลักฐาน
                $report->files = $this->get_corruption_files_safe($corruption_id);

                // ดึงประวัติการดำเนินการ
                $report->history = $this->get_corruption_history_safe($corruption_id);

                // จัดการข้อมูลผู้แจ้งที่แสดง
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }

                log_message('info', "Corruption report found by ID: {$corruption_id}");
            } else {
                log_message('warning', "Corruption report not found by ID: {$corruption_id}");
            }

            return $report;

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption report by ID: ' . $e->getMessage());
            return null;
        }
    }



    public function get_corruption_history_safe($corruption_id, $limit = null)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_history')) {
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_corruption_history');
            $this->db->where('corruption_id', intval($corruption_id));
            $this->db->order_by('action_date', 'DESC');

            if ($limit && is_numeric($limit)) {
                $this->db->limit($limit);
            }

            $query = $this->db->get();
            return $query ? $query->result() : [];

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption history: ' . $e->getMessage());
            return [];
        }
    }


    public function check_system_status()
    {
        $status = [
            'database_connected' => false,
            'tables_exist' => [],
            'missing_tables' => [],
            'system_ready' => false
        ];

        try {
            // ตรวจสอบการเชื่อมต่อ database
            if ($this->db->conn_id) {
                $status['database_connected'] = true;
            }

            // ตรวจสอบ tables
            $required_tables = [
                'tbl_corruption_reports',
                'tbl_corruption_files',
                'tbl_corruption_history',
                'tbl_corruption_tracking'
            ];

            foreach ($required_tables as $table) {
                if ($this->db->table_exists($table)) {
                    $status['tables_exist'][] = $table;
                } else {
                    $status['missing_tables'][] = $table;
                }
            }

            // ระบบพร้อมใช้งานถ้ามี table หลักอย่างน้อย
            $status['system_ready'] = $status['database_connected'] &&
                in_array('tbl_corruption_reports', $status['tables_exist']);

        } catch (Exception $e) {
            log_message('error', 'Error checking system status: ' . $e->getMessage());
        }

        return $status;
    }



    public function validate_report_id_format($report_id)
    {
        try {
            // รูปแบบใหม่: COR + ปี พ.ศ. 2 ตัวท้าย + เลข 5 หลัก
            // ตัวอย่าง: COR6812345

            if (strlen($report_id) !== 10) {
                return [
                    'valid' => false,
                    'message' => 'ความยาวหมายเลขรายงานไม่ถูกต้อง (ต้อง 10 ตัวอักษร)'
                ];
            }

            if (substr($report_id, 0, 3) !== 'COR') {
                return [
                    'valid' => false,
                    'message' => 'หมายเลขรายงานต้องขึ้นต้นด้วย COR'
                ];
            }

            $year_part = substr($report_id, 3, 2);
            $number_part = substr($report_id, 5, 5);

            // ตรวจสอบว่าเป็นตัวเลขทั้งหมด
            if (!ctype_digit($year_part) || !ctype_digit($number_part)) {
                return [
                    'valid' => false,
                    'message' => 'ส่วนปีและหมายเลขต้องเป็นตัวเลขเท่านั้น'
                ];
            }

            // ตรวจสอบว่าปีอยู่ในช่วงที่สมเหตุสมผล
            $current_buddhist_year = date('Y') + 543;
            $current_year_suffix = intval(substr($current_buddhist_year, -2));
            $report_year_suffix = intval($year_part);

            // อนุญาตให้ย้อนหลังไป 10 ปี และไปข้างหน้า 2 ปี
            $min_year = $current_year_suffix - 10;
            $max_year = $current_year_suffix + 2;

            // จัดการกรณีที่ข้ามศตวรรษ
            if ($min_year < 0) {
                $min_year += 100;
            }
            if ($max_year > 99) {
                $max_year -= 100;
            }

            if ($report_year_suffix < $min_year || $report_year_suffix > $max_year) {
                return [
                    'valid' => false,
                    'message' => 'ปีในหมายเลขรายงานไม่อยู่ในช่วงที่อนุญาต'
                ];
            }

            return [
                'valid' => true,
                'year_suffix' => $year_part,
                'number' => $number_part,
                'full_buddhist_year' => 2500 + $report_year_suffix // สมมติเป็นศตวรรษที่ 25-26
            ];

        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบรูปแบบ: ' . $e->getMessage()
            ];
        }
    }




    public function get_corruption_files_safe($corruption_id)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_files')) {
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_corruption_files');
            $this->db->where('corruption_id', intval($corruption_id));
            $this->db->where('file_status', 'active');
            $this->db->order_by('file_order', 'ASC');
            $this->db->order_by('uploaded_at', 'ASC');

            $query = $this->db->get();
            return $query ? $query->result() : [];

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption files: ' . $e->getMessage());
            return [];
        }
    }




    public function update_corruption_status($identifier, $new_status, $updated_by, $notes = '')
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return false;
            }

            // หาข้อมูลรายงานก่อน
            $report = $this->get_corruption_report_flexible($identifier);
            if (!$report) {
                log_message('error', "Cannot find corruption report with identifier: {$identifier}");
                return false;
            }

            $corruption_id = $report->corruption_id;

            $update_data = [
                'report_status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $updated_by
            ];

            // เพิ่ม notes ถ้ามี
            if (!empty($notes)) {
                if ($new_status === 'resolved') {
                    $update_data['resolution_details'] = $notes;
                } else {
                    $update_data['investigation_notes'] = $notes;
                }

                $update_data['response_message'] = $notes;
                $update_data['response_by'] = $updated_by;
                $update_data['response_date'] = date('Y-m-d H:i:s');
            }

            $this->db->where('corruption_id', intval($corruption_id));
            $result = $this->db->update('tbl_corruption_reports', $update_data);

            if ($result) {
                log_message('info', "Corruption status updated: ID {$corruption_id} to {$new_status} by {$updated_by}");
            } else {
                log_message('error', "Failed to update corruption status for ID: {$corruption_id}");
                log_message('error', 'Database error: ' . json_encode($this->db->error()));
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error updating corruption status: ' . $e->getMessage());
            return false;
        }
    }





    public function get_corruption_report_flexible($identifier)
    {
        try {
            if (empty($identifier)) {
                log_message('error', 'Empty identifier provided');
                return null;
            }

            // ลองหาจาก corruption_id ก่อน (ถ้าเป็นตัวเลข)
            if (is_numeric($identifier)) {
                $report = $this->get_corruption_report_by_id($identifier);
                if ($report) {
                    return $report;
                }
            }

            // ถ้าไม่เจอ หรือไม่ใช่ตัวเลข ให้ลองหาจาก corruption_report_id
            return $this->get_corruption_report_by_report_id($identifier);

        } catch (Exception $e) {
            log_message('error', 'Error in get_corruption_report_flexible: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * ดึงรายงานการทุจริตตามรหัสรายงาน
     */
    public function get_corruption_report_by_report_id($report_id)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return null;
            }

            if (empty($report_id)) {
                log_message('error', 'Empty report_id provided');
                return null;
            }

            // ตรวจสอบรูปแบบ (ถ้าเป็นตัวเลขให้แปลงเป็น COR format หรือใช้เป็น ID)
            if (is_numeric($report_id)) {
                log_message('info', "Numeric report_id provided: {$report_id}, treating as corruption_id");
                return $this->get_corruption_report_by_id($report_id);
            }

            // ถ้าเป็น string ให้ค้นหาตาม corruption_report_id
            $this->db->select('*');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('corruption_report_id', $report_id);
            $this->db->where('is_archived', 0);

            $query = $this->db->get();
            $report = $query ? $query->row() : null;

            if ($report) {
                // ดึงไฟล์หลักฐาน
                $report->files = $this->get_corruption_files_safe($report->corruption_id);

                // ดึงประวัติการดำเนินการ
                $report->history = $this->get_corruption_history_safe($report->corruption_id);

                // จัดการข้อมูลผู้แจ้งที่แสดง
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }

                log_message('info', "Corruption report found by report_id: {$report_id}");
            } else {
                log_message('warning', "Corruption report not found by report_id: {$report_id}");
            }

            return $report;

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption report by report ID: ' . $e->getMessage());
            return null;
        }
    }





    public function generate_new_format_report_id()
    {
        try {
            $prefix = 'COR';

            // ปี พ.ศ. 2 ตัวท้าย
            $buddhist_year = date('Y') + 543;
            $year_suffix = substr($buddhist_year, -2);

            // หาเลข random ที่ไม่ซ้ำ
            $max_attempts = 50;
            $attempts = 0;

            do {
                // สร้างเลข random 5 หลัก
                $random_number = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
                $report_id = $prefix . $year_suffix . $random_number;

                // ตรวจสอบความซ้ำ
                $this->db->select('corruption_report_id');
                $this->db->from('tbl_corruption_reports');
                $this->db->where('corruption_report_id', $report_id);
                $existing = $this->db->get()->row();

                if (!$existing) {
                    return $report_id;
                }

                $attempts++;

            } while ($attempts < $max_attempts);

            // หากไม่สามารถหาเลขที่ไม่ซ้ำได้ ใช้ timestamp
            $timestamp_suffix = substr(time(), -5);
            return $prefix . $year_suffix . $timestamp_suffix;

        } catch (Exception $e) {
            log_message('error', 'Error generating new format report ID: ' . $e->getMessage());

            // fallback สุดท้าย
            $buddhist_year = date('Y') + 543;
            $year_suffix = substr($buddhist_year, -2);
            return 'COR' . $year_suffix . '00001';
        }
    }




    public function get_corruption_statistics_by_thai_year($buddhist_year = null)
    {
        try {
            if (!$buddhist_year) {
                $buddhist_year = date('Y') + 543;
            }

            $year_suffix = substr($buddhist_year, -2);

            // ค้นหารายงานที่มีปีตรงกัน
            $this->db->select('
            COUNT(*) as total_reports,
            COUNT(CASE WHEN report_status = "pending" THEN 1 END) as pending_reports,
            COUNT(CASE WHEN report_status = "resolved" THEN 1 END) as resolved_reports,
            COUNT(CASE WHEN is_anonymous = 1 THEN 1 END) as anonymous_reports
        ');
            $this->db->from('tbl_corruption_reports');
            $this->db->like('corruption_report_id', 'COR' . $year_suffix, 'after');
            $this->db->where('is_archived', 0);

            $query = $this->db->get();
            $stats = $query->row();

            if ($stats) {
                $stats->buddhist_year = $buddhist_year;
                $stats->year_suffix = $year_suffix;
            }

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error getting statistics by Thai year: ' . $e->getMessage());
            return null;
        }
    }






    /**
     * มอบหมายรายงานให้เจ้าหน้าที่
     */
    public function assign_corruption_report($identifier, $assigned_to, $assigned_by, $department = '')
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return false;
            }

            // หาข้อมูลรายงานก่อน
            $report = $this->get_corruption_report_flexible($identifier);
            if (!$report) {
                log_message('error', "Cannot find corruption report with identifier: {$identifier}");
                return false;
            }

            $corruption_id = $report->corruption_id;

            $update_data = [
                'assigned_to' => intval($assigned_to),
                'assigned_department' => $department,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $assigned_by
            ];

            // เปลี่ยนสถานะเป็น under_review ถ้ายังเป็น pending
            if ($report->report_status === 'pending') {
                $update_data['report_status'] = 'under_review';
                log_message('info', "Status changed from pending to under_review for corruption_id: {$corruption_id}");
            }

            $this->db->where('corruption_id', intval($corruption_id));
            $result = $this->db->update('tbl_corruption_reports', $update_data);

            if ($result) {
                log_message('info', "Corruption report assigned: ID {$corruption_id} to {$assigned_to} by {$assigned_by}");
            } else {
                log_message('error', "Failed to assign corruption report for ID: {$corruption_id}");
                log_message('error', 'Database error: ' . json_encode($this->db->error()));
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error assigning corruption report: ' . $e->getMessage());
            return false;
        }
    }






    /**
     * เพิ่มการตอบกลับ
     */
    public function add_corruption_response($identifier, $response_message, $response_by)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                log_message('error', 'Table tbl_corruption_reports does not exist');
                return false;
            }

            // หาข้อมูลรายงานก่อน
            $report = $this->get_corruption_report_flexible($identifier);
            if (!$report) {
                log_message('error', "Cannot find corruption report with identifier: {$identifier}");
                return false;
            }

            $corruption_id = $report->corruption_id;

            $update_data = [
                'response_message' => $response_message,
                'response_by' => $response_by,
                'response_date' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $response_by
            ];

            $this->db->where('corruption_id', intval($corruption_id));
            $result = $this->db->update('tbl_corruption_reports', $update_data);

            if ($result) {
                log_message('info', "Response added to corruption report: ID {$corruption_id} by {$response_by}");
            } else {
                log_message('error', "Failed to add response to corruption report for ID: {$corruption_id}");
                log_message('error', 'Database error: ' . json_encode($this->db->error()));
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error adding corruption response: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายการรายงานพร้อมตัวกรอง
     */
    public function get_corruption_reports_with_filters($filters = [], $limit = 20, $offset = 0)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return ['data' => [], 'total' => 0];
            }

            // Query พื้นฐาน - เอา CASE statement ออก
            $this->db->select('cr.*');
            $this->db->from('tbl_corruption_reports cr');
            $this->db->where('cr.is_archived', 0);

            // ใช้ตัวกรอง
            $this->apply_corruption_filters($filters);

            // Clone query สำหรับนับจำนวน
            $count_query = clone $this->db;
            $total = $count_query->count_all_results('', false);

            // เรียงลำดับและจำกัดจำนวน
            $this->db->order_by('cr.created_at', 'DESC');
            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get();
            $reports = $query->result();

            // เพิ่มข้อมูลไฟล์และประวัติสำหรับแต่ละรายงาน
            foreach ($reports as &$report) {
                // จัดการชื่อผู้แจ้งที่แสดง - ย้ายมาทำใน PHP
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }

                // ดึงไฟล์หลักฐาน
                $report->files = $this->get_corruption_files_safe($report->corruption_id);

                // ดึงประวัติการดำเนินการ (จำกัด 3 รายการล่าสุด)
                $report->history = $this->get_corruption_history_safe($report->corruption_id, 3);
            }

            return [
                'data' => $reports,
                'total' => $total
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in get_corruption_reports_with_filters: ' . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * ใช้ตัวกรองกับ query
     */
    private function apply_corruption_filters($filters)
    {
        if (!empty($filters['status'])) {
            $this->db->where('cr.report_status', $filters['status']);
        }

        if (!empty($filters['corruption_type'])) {
            $this->db->where('cr.corruption_type', $filters['corruption_type']);
        }

        if (!empty($filters['priority'])) {
            $this->db->where('cr.priority_level', $filters['priority']);
        }

        if (!empty($filters['anonymous'])) {
            $this->db->where('cr.is_anonymous', $filters['anonymous'] === 'yes' ? 1 : 0);
        }

        if (!empty($filters['assigned_to'])) {
            $this->db->where('cr.assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(cr.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(cr.created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $this->db->escape_like_str($filters['search']);
            $this->db->group_start();
            $this->db->like('cr.corruption_report_id', $search);
            $this->db->or_like('cr.complaint_subject', $search);
            $this->db->or_like('cr.complaint_details', $search);
            $this->db->or_like('cr.perpetrator_name', $search);
            // ค้นหาชื่อผู้แจ้งเฉพาะกรณีไม่ anonymous
            $this->db->or_group_start();
            $this->db->where('cr.is_anonymous', 0);
            $this->db->like('cr.reporter_name', $search);
            $this->db->group_end();
            $this->db->group_end();
        }
    }

    // ===================================================================
    // *** การจัดการไฟล์หลักฐาน ***
    // ===================================================================

    /**
     * เพิ่มไฟล์หลักฐาน
     */
    public function add_corruption_file($corruption_id, $file_data)
    {
        try {
            $insert_data = [
                'corruption_id' => $corruption_id,
                'file_name' => $file_data['file_name'],
                'file_original_name' => $file_data['file_original_name'],
                'file_path' => $file_data['file_path'],
                'file_size' => $file_data['file_size'],
                'file_type' => $file_data['file_type'],
                'file_extension' => $file_data['file_extension'],
                'file_description' => $file_data['file_description'] ?? null,
                'file_order' => $file_data['file_order'] ?? 1,
                'is_main_evidence' => $file_data['is_main_evidence'] ?? 0,
                'uploaded_by' => $file_data['uploaded_by'] ?? 'System',
                'uploaded_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->db->insert('tbl_corruption_files', $insert_data);

            if ($result) {
                // อัปเดตจำนวนไฟล์ใน report
                $this->update_file_count($corruption_id);
                return $this->db->insert_id();
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error adding corruption file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงไฟล์หลักฐาน
     */
    public function get_corruption_files($corruption_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_corruption_files');
            $this->db->where('corruption_id', $corruption_id);
            $this->db->where('file_status', 'active');
            $this->db->order_by('file_order', 'ASC');
            $this->db->order_by('uploaded_at', 'ASC');

            $query = $this->db->get();
            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption files: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงไฟล์ตาม ID
     */
    public function get_corruption_file_by_id($file_id)
    {
        try {
            $this->db->select('cf.*, cr.corruption_report_id, cr.is_anonymous');
            $this->db->from('tbl_corruption_files cf');
            $this->db->join('tbl_corruption_reports cr', 'cf.corruption_id = cr.corruption_id');
            $this->db->where('cf.file_id', $file_id);
            $this->db->where('cf.file_status', 'active');

            $query = $this->db->get();
            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption file by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ลบไฟล์หลักฐาน
     */
    public function delete_corruption_file($file_id, $deleted_by)
    {
        try {
            $this->db->trans_start();

            // ดึงข้อมูลไฟล์ก่อนลบ
            $file = $this->get_corruption_file_by_id($file_id);
            if (!$file) {
                return false;
            }

            // อัปเดตสถานะเป็น deleted
            $this->db->where('file_id', $file_id);
            $this->db->update('tbl_corruption_files', [
                'file_status' => 'deleted',
                'deleted_by' => $deleted_by,
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

            // อัปเดตจำนวนไฟล์
            $this->update_file_count($file->corruption_id);

            $this->db->trans_complete();

            return $this->db->trans_status() !== FALSE;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error deleting corruption file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตจำนวนไฟล์
     */
    private function update_file_count($corruption_id)
    {
        try {
            $this->db->select('COUNT(*) as file_count');
            $this->db->from('tbl_corruption_files');
            $this->db->where('corruption_id', $corruption_id);
            $this->db->where('file_status', 'active');

            $query = $this->db->get();
            $count = $query->row()->file_count;

            $this->db->where('corruption_id', $corruption_id);
            $this->db->update('tbl_corruption_reports', ['evidence_file_count' => $count]);

            return true;

        } catch (Exception $e) {
            log_message('error', 'Error updating file count: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================================================
    // *** การจัดการประวัติการดำเนินการ ***
    // ===================================================================

    /**
     * เพิ่มประวัติการดำเนินการ
     */
    public function add_corruption_history($corruption_id, $action_type, $action_description, $action_by, $action_by_user_id = null, $old_value = null, $new_value = null)
    {
        try {
            $history_data = [
                'corruption_id' => $corruption_id,
                'action_type' => $action_type,
                'old_value' => $old_value,
                'new_value' => $new_value,
                'action_description' => $action_description,
                'action_by' => $action_by,
                'action_by_user_id' => $action_by_user_id,
                'action_date' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address() ?? null
            ];

            $result = $this->db->insert('tbl_corruption_history', $history_data);
            return $result ? $this->db->insert_id() : false;

        } catch (Exception $e) {
            log_message('error', 'Error adding corruption history: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงประวัติการดำเนินการ
     */
    public function get_corruption_history($corruption_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_corruption_history');
            $this->db->where('corruption_id', $corruption_id);
            $this->db->order_by('action_date', 'DESC');

            $query = $this->db->get();
            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption history: ' . $e->getMessage());
            return [];
        }
    }

    // ===================================================================
    // *** การจัดการการแจ้งเตือน ***
    // ===================================================================

    /**
     * สร้างการแจ้งเตือน
     */
    public function create_corruption_notification($corruption_id, $notification_data)
    {
        try {
            // ตรวจสอบว่ามี table notifications หรือไม่
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('info', 'Notifications table not exists, skipping notification creation');
                return true;
            }

            $notification_data['reference_id'] = intval($corruption_id);
            $notification_data['reference_table'] = 'tbl_corruption_reports';
            $notification_data['created_at'] = date('Y-m-d H:i:s');
            $notification_data['is_read'] = 0;
            $notification_data['is_system'] = 1;

            return $this->db->insert('tbl_notifications', $notification_data);

        } catch (Exception $e) {
            log_message('error', 'Error creating corruption notification: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * ดึงการแจ้งเตือนของผู้ใช้
     */
    public function get_user_notifications($user_id, $role = null, $limit = 10)
    {
        try {
            $this->db->select('cn.*, cr.corruption_report_id, cr.complaint_subject');
            $this->db->from('tbl_corruption_notifications cn');
            $this->db->join('tbl_corruption_reports cr', 'cn.corruption_id = cr.corruption_id');

            $this->db->group_start();
            $this->db->where('cn.target_user_id', $user_id);
            if ($role) {
                $this->db->or_where('cn.target_role', $role);
            }
            $this->db->group_end();

            $this->db->where('cn.expires_at IS NULL OR cn.expires_at >', date('Y-m-d H:i:s'));
            $this->db->order_by('cn.created_at', 'DESC');

            if ($limit > 0) {
                $this->db->limit($limit);
            }

            $query = $this->db->get();
            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting user notifications: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * อ่านการแจ้งเตือน
     */
    public function mark_notification_as_read($notification_id, $user_id)
    {
        try {
            $this->db->where('notification_id', $notification_id);
            $this->db->where('target_user_id', $user_id);
            $result = $this->db->update('tbl_corruption_notifications', [
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ]);

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error marking notification as read: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================================================
    // *** สถิติและรายงาน ***
    // ===================================================================

    /**
     * ดึงสถิติการทุจริต
     */
    public function get_corruption_statistics()
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return $this->get_default_corruption_summary();
            }

            // ดึงสถิติพื้นฐาน
            $total = $this->count_total_reports();

            // สถิติตามสถานะ
            $by_status = [
                'pending' => $this->count_reports_by_status('pending'),
                'under_review' => $this->count_reports_by_status('under_review'),
                'investigating' => $this->count_reports_by_status('investigating'),
                'resolved' => $this->count_reports_by_status('resolved'),
                'dismissed' => $this->count_reports_by_status('dismissed'),
                'closed' => $this->count_reports_by_status('closed')
            ];

            // สถิติตามประเภท
            $by_type = [
                'embezzlement' => $this->count_reports_by_type('embezzlement'),
                'bribery' => $this->count_reports_by_type('bribery'),
                'abuse_of_power' => $this->count_reports_by_type('abuse_of_power'),
                'conflict_of_interest' => $this->count_reports_by_type('conflict_of_interest'),
                'procurement_fraud' => $this->count_reports_by_type('procurement_fraud'),
                'other' => $this->count_reports_by_type('other')
            ];

            // สถิติเพิ่มเติม
            $this_month = $this->count_this_month_reports();
            $anonymous = $this->count_anonymous_reports();
            $high_priority = $this->count_high_priority_reports();

            // คำนวณเปอร์เซ็นต์ความสำเร็จ
            $resolution_rate = ($total > 0) ? round(($by_status['resolved'] / $total) * 100, 2) : 0;

            return [
                'total' => $total,
                'by_status' => $by_status,
                'by_type' => $by_type,
                'this_month' => $this_month,
                'anonymous' => $anonymous,
                'high_priority' => $high_priority,
                'resolution_rate' => $resolution_rate,
                'last_updated' => date('Y-m-d H:i:s')
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting corruption statistics: ' . $e->getMessage());
            return $this->get_default_corruption_summary();
        }
    }


    private function get_default_corruption_summary()
    {
        return [
            'total' => 0,
            'by_status' => [
                'pending' => 0,
                'under_review' => 0,
                'investigating' => 0,
                'resolved' => 0,
                'dismissed' => 0,
                'closed' => 0
            ],
            'by_type' => [
                'embezzlement' => 0,
                'bribery' => 0,
                'abuse_of_power' => 0,
                'conflict_of_interest' => 0,
                'procurement_fraud' => 0,
                'other' => 0
            ],
            'this_month' => 0,
            'anonymous' => 0,
            'high_priority' => 0,
            'resolution_rate' => 0,
            'last_updated' => date('Y-m-d H:i:s')
        ];
    }



    /**
     * คำนวณสถิติแบบ manual
     */
    private function calculate_corruption_statistics()
    {
        $stats = new stdClass();

        // จำนวนรายงานทั้งหมด
        $this->db->where('is_archived', 0);
        $stats->total_reports = $this->db->count_all_results('tbl_corruption_reports');

        // จำนวนตามสถานะ
        $statuses = ['pending', 'under_review', 'investigating', 'resolved', 'dismissed'];
        foreach ($statuses as $status) {
            $this->db->where('report_status', $status);
            $this->db->where('is_archived', 0);
            $stats->{$status . '_reports'} = $this->db->count_all_results('tbl_corruption_reports');
        }

        // จำนวนรายงานไม่ระบุตัวตน
        $this->db->where('is_anonymous', 1);
        $this->db->where('is_archived', 0);
        $stats->anonymous_reports = $this->db->count_all_results('tbl_corruption_reports');

        // จำนวนตามประเภทการทุจริต
        $types = ['embezzlement', 'bribery', 'abuse_of_power', 'conflict_of_interest', 'procurement_fraud'];
        foreach ($types as $type) {
            $this->db->where('corruption_type', $type);
            $this->db->where('is_archived', 0);
            $stats->{$type . '_reports'} = $this->db->count_all_results('tbl_corruption_reports');
        }

        // จำนวนรายงานตามช่วงเวลา
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('is_archived', 0);
        $stats->today_reports = $this->db->count_all_results('tbl_corruption_reports');

        $this->db->where('DATE(created_at) >=', date('Y-m-d', strtotime('-7 days')));
        $this->db->where('is_archived', 0);
        $stats->week_reports = $this->db->count_all_results('tbl_corruption_reports');

        $this->db->where('DATE(created_at) >=', date('Y-m-d', strtotime('-30 days')));
        $this->db->where('is_archived', 0);
        $stats->month_reports = $this->db->count_all_results('tbl_corruption_reports');

        return $stats;
    }

    /**
     * สถิติเริ่มต้น
     */
    private function get_default_statistics()
    {
        return (object) [
            'total_reports' => 0,
            'pending_reports' => 0,
            'under_review_reports' => 0,
            'investigating_reports' => 0,
            'resolved_reports' => 0,
            'dismissed_reports' => 0,
            'anonymous_reports' => 0,
            'embezzlement_reports' => 0,
            'bribery_reports' => 0,
            'abuse_power_reports' => 0,
            'conflict_interest_reports' => 0,
            'procurement_fraud_reports' => 0,
            'today_reports' => 0,
            'week_reports' => 0,
            'month_reports' => 0
        ];
    }

    /**
     * ดึงรายงานล่าสุด
     */
    public function get_recent_corruption_reports($limit = 10)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return [];
            }

            $this->db->select('corruption_id, corruption_report_id, corruption_type, complaint_subject');
            $this->db->select('report_status, priority_level, is_anonymous');
            $this->db->select('reporter_name, created_at');
            $this->db->from('tbl_corruption_reports');
            $this->db->where('is_archived', 0);
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit($limit);

            $query = $this->db->get();
            $reports = $query ? $query->result() : [];

            // จัดการข้อมูลผู้แจ้งหลังจากดึงข้อมูลมาแล้ว - ทำใน PHP แทน SQL
            foreach ($reports as &$report) {
                if ($report->is_anonymous == 1) {
                    $report->display_reporter_name = 'ไม่ระบุตัวตน';
                } else {
                    $report->display_reporter_name = $report->reporter_name;
                }
            }

            return $reports;

        } catch (Exception $e) {
            log_message('error', 'Error in get_recent_corruption_reports: ' . $e->getMessage());
            return [];
        }
    }


    // ===================================================================
    // *** การติดตามการใช้งาน ***
    // ===================================================================

    /**
     * บันทึกการติดตาม
     */
    public function log_corruption_tracking($corruption_id, $action, $details = [], $user_info = [])
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_tracking')) {
                // ถ้าไม่มี table tracking ก็ไม่เป็นไร
                return true;
            }

            $tracking_data = [
                'corruption_id' => intval($corruption_id),
                'tracking_action' => $action,
                'tracking_details' => json_encode($details, JSON_UNESCAPED_UNICODE),
                'user_id' => $user_info['user_id'] ?? null,
                'user_type' => $user_info['user_type'] ?? 'guest',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'session_id' => session_id(),
                'tracked_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->insert('tbl_corruption_tracking', $tracking_data);

        } catch (Exception $e) {
            log_message('error', 'Error logging corruption tracking: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตจำนวนการดู
     */
    public function update_view_count($corruption_id)
    {
        try {
            if (!$this->db->table_exists('tbl_corruption_reports')) {
                return false;
            }

            $this->db->set('view_count', 'view_count + 1', FALSE);
            $this->db->set('last_viewed', date('Y-m-d H:i:s'));
            $this->db->where('corruption_id', intval($corruption_id));

            return $this->db->update('tbl_corruption_reports');

        } catch (Exception $e) {
            log_message('error', 'Error updating view count: ' . $e->getMessage());
            return false;
        }
    }





}

