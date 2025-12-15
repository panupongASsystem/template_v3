<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Queue_model - Full Code รวม methods เดิมและใหม่
 * รองรับทั้งระบบ queue_id แบบเก่า (integer) และใหม่ (varchar)
 * รองรับ user ที่ login และ guest user พร้อมข้อมูลที่อยู่แบบละเอียด
 */
class Queue_model extends CI_Model
{
    private $channelAccessToken;
    private $lineApiUrl;
    private $use_new_queue_id_format = true; // เปลี่ยนเป็น false ถ้าต้องการใช้รูปแบบเก่า

    public function __construct()
    {
        parent::__construct();

        // ใช้ helper function get_config_value เพื่อดึงค่า token จากฐานข้อมูล
        $this->channelAccessToken = function_exists('get_config_value') ? get_config_value('line_token') : '';
        $this->lineApiUrl = 'https://api.line.me/v2/bot/message/multicast';
		
        // โหลด LINE Notification Library
        $this->load->library('line_notification');
    }

    // ===================================================================
    // *** METHODS สำหรับสร้าง Queue ID (รองรับทั้งแบบเก่าและใหม่) ***
    // ===================================================================

    /**
     * สร้าง queue_id ใหม่ - รองรับทั้งรูปแบบเก่าและใหม่
     */
    public function add_new_id_entry()
    {
        if ($this->use_new_queue_id_format) {
            return $this->generate_new_format_id();
        } else {
            return $this->generate_old_format_id();
        }
    }

    /**
     * สร้าง queue_id รูปแบบใหม่ (varchar) - Q + วันที่ + เลขลำดับ
     */
    private function generate_new_format_id()
    {
        try {
            // *** ลบการเรียกใช้ SQL function ที่ไม่มีอยู่ ***
            // $query = $this->db->query("SELECT generate_queue_id() as new_id");

            // ใช้ fallback method เป็นหลัก
            $date_prefix = date('ymd');

            // หาเลขลำดับล่าสุดของวันนี้
            $this->db->select('queue_id');
            $this->db->from('tbl_queue');
            $this->db->where('DATE(COALESCE(queue_create, queue_datesave)) =', date('Y-m-d'));
            $this->db->like('queue_id', 'Q' . $date_prefix, 'after');
            $this->db->order_by('queue_id', 'DESC');
            $this->db->limit(1);

            $last_queue = $this->db->get()->row();

            if ($last_queue && strlen($last_queue->queue_id) >= 10) {
                // ดึงเลขลำดับจาก queue_id เดิม
                $last_number = intval(substr($last_queue->queue_id, -5));
                $new_number = $last_number + 1;
            } else {
                $new_number = 1;
            }

            // สร้าง queue_id ใหม่
            $new_queue_id = 'Q' . $date_prefix . sprintf('%05d', $new_number);

            log_message('info', 'Generated new queue_id: ' . $new_queue_id);
            return $new_queue_id;

        } catch (Exception $e) {
            log_message('error', 'Error in generate_new_format_id: ' . $e->getMessage());
            // ใช้ timestamp เป็น fallback
            $fallback_id = 'Q' . date('ymdHis');
            log_message('info', 'Using fallback queue_id: ' . $fallback_id);
            return $fallback_id;
        }
    }



    private function get_pending_queues_for_alerts_report()
    {
        try {
            $sql = "
            SELECT 
                queue_id, 
                queue_topic, 
                queue_detail,
                queue_status, 
                queue_by, 
                queue_phone, 
                queue_datesave,
                queue_dateupdate,
                queue_user_type,
                DATEDIFF(NOW(), queue_datesave) as days_old
            FROM tbl_queue 
            WHERE queue_status IN (?, ?, ?, ?, ?, ?, ?)
            AND DATEDIFF(NOW(), queue_datesave) >= 7  -- *** แก้ไข: เปลี่ยนจาก 3 เป็น 7 วัน ***
            ORDER BY DATEDIFF(NOW(), queue_datesave) DESC, queue_datesave ASC 
            LIMIT 50
        ";

            $params = [
                'รอยืนยันการจอง',
                'ยืนยันการจอง',
                'คิวได้รับการยืนยัน',
                'รับเรื่องพิจารณา',
                'รับเรื่องแล้ว',
                'รอดำเนินการ',
                'กำลังดำเนินการ'
            ];

            $query = $this->db->query($sql, $params);
            $result = $query->result();

            if ($result) {
                foreach ($result as $queue) {
                    $days = intval($queue->days_old);
                    if ($days >= 14) {
                        $queue->priority = 'critical';
                        $queue->priority_label = 'วิกฤต';
                    } elseif ($days >= 7) {  // *** แก้ไข: แค่ >= 7 ***
                        $queue->priority = 'danger';
                        $queue->priority_label = 'เร่งด่วน';
                    } else {
                        $queue->priority = 'warning';
                        $queue->priority_label = 'ติดตาม';
                    }
                }
            }

            return $result ?: [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_pending_queues_for_alerts_report: ' . $e->getMessage());
            return [];
        }
    }




    public function get_queues_by_user_id($user_id, $user_type)
    {
        try {
            if (empty($user_id) || empty($user_type)) {
                return [];
            }

            $this->db->select('queue_id, queue_topic, queue_detail, queue_by, queue_phone, queue_number, queue_date, queue_time_slot, queue_status, queue_datesave, queue_dateupdate, queue_user_id, queue_user_type');
            $this->db->from('tbl_queue');
            $this->db->where('queue_user_id', $user_id);
            $this->db->where('queue_user_type', $user_type);
            $this->db->order_by('queue_datesave', 'DESC');
            $this->db->limit(50);

            $result = $this->db->get()->result();

            if ($result) {
                // เพิ่มข้อมูลเสริม
                foreach ($result as $queue) {
                    $queue->queue_date_thai = $this->format_thai_date($queue->queue_date);
                    $queue->queue_create_thai = $this->format_thai_date($queue->queue_datesave);
                    $queue->status_color = $this->get_status_color($queue->queue_status);

                    // นับจำนวนไฟล์แนบ
                    if ($this->db->table_exists('tbl_queue_files')) {
                        $this->db->select('COUNT(*) as file_count');
                        $this->db->from('tbl_queue_files');
                        $this->db->where('queue_file_ref_id', $queue->queue_id);
                        $file_result = $this->db->get()->row();
                        $queue->file_count = $file_result ? $file_result->file_count : 0;
                    } else {
                        $queue->file_count = 0;
                    }
                }
            }

            return $result ?: [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_queues_by_user_id: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * สร้าง queue_id รูปแบบเก่า (integer) - ปีไทย + เลขลำดับ
     */
    private function generate_old_format_id()
    {
        // ดึงปี พ.ศ. ปัจจุบัน (เพิ่ม 543 จาก ค.ศ.)
        $current_year_thai = date('Y') + 543;

        // ตรวจสอบ ID ล่าสุดในตาราง
        $this->db->select('MAX(CAST(queue_id AS UNSIGNED)) AS max_id');
        $this->db->from('tbl_queue');
        $query = $this->db->get();
        $result = $query->row();

        // กำหนดค่าเริ่มต้นของ ID เช่น 6700001 หรือ 6800001 ตามปี
        $default_id = (int) ($current_year_thai % 100) . '00001';

        if ($result && $result->max_id) {
            $last_id = (int) $result->max_id;
            $last_year_prefix = (int) substr($last_id, 0, 2);
            $current_year_prefix = (int) ($current_year_thai % 100);

            if ($last_year_prefix === $current_year_prefix) {
                $new_id = $last_id + 1;
            } else {
                $new_id = $default_id;
            }
        } else {
            $new_id = $default_id;
        }

        return $new_id;
    }

    // ===================================================================
    // *** METHODS ใหม่สำหรับการจองคิว - Enhanced Version ***
    // ===================================================================

    /**
     * บันทึกคิวใหม่แบบปรับปรุง - รองรับฟิลด์ใหม่ทั้งหมด
     */
    public function add_queue_enhanced($current_user)
    {
        try {
            $this->db->trans_start();

            $new_queue_id = $this->add_new_id_entry();
            $queue_data = $this->prepare_queue_data($new_queue_id, $current_user);

            if (empty($queue_data['queue_topic']) || empty($queue_data['queue_detail']) || empty($queue_data['queue_date'])) {
                throw new Exception('Missing required queue data');
            }

            $insert_result = $this->db->insert('tbl_queue', $queue_data);

            if (!$insert_result) {
                throw new Exception('Failed to insert queue data');
            }

            // จัดการไฟล์แนบ
            if (!empty($_FILES['queue_files']['name'][0])) {
                $this->handle_queue_file_uploads($new_queue_id);
            }

            // บันทึก queue detail
            $detail_result = $this->add_queue_detail($new_queue_id, $current_user);

            if (!$detail_result) {
                throw new Exception('Failed to insert queue detail');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // *** เพิ่มส่วนนี้: ส่ง notification ***
            $this->send_queue_notifications($new_queue_id, $current_user);

            // ส่ง LINE notification (เดิม)
            $this->send_queue_line_notification($new_queue_id);

            log_message('info', 'Queue created successfully with notifications: ' . $new_queue_id);
            return $new_queue_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in add_queue_enhanced: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เตรียมข้อมูลสำหรับบันทึกคิว
     */
    private function prepare_queue_data($queue_id, $current_user)
    {
        // รวมวันที่และเวลา
        $queue_date = $this->input->post('queue_date');
        $queue_time_slot = $this->input->post('queue_time_slot');
        $combined_datetime = $this->combine_datetime($queue_date, $queue_time_slot);

        // ข้อมูลพื้นฐาน
        $queue_data = [
            'queue_id' => $queue_id,
            'queue_topic' => $this->input->post('queue_topic'),
            'queue_detail' => $this->input->post('queue_detail'),
            'queue_date' => $combined_datetime,
            'queue_time_slot' => $queue_time_slot,
            'queue_status' => 'รอยืนยันการจอง',
            'queue_datesave' => date('Y-m-d H:i:s')
        ];

        // เพิ่มข้อมูล user
        if ($current_user['is_logged_in']) {
            // User ที่ login แล้ว
            $queue_data['queue_user_id'] = $current_user['user_info']['id'];
            $queue_data['queue_user_type'] = $current_user['user_type'];
            $queue_data['queue_by'] = $current_user['user_info']['name'];
            $queue_data['queue_phone'] = $current_user['user_info']['phone'];
            $queue_data['queue_email'] = $current_user['user_info']['email'];
            $queue_data['queue_number'] = $current_user['user_info']['number'] ?: '0000000000000';

            // ที่อยู่จาก user account (ถ้ามี)
            if (!empty($current_user['user_address']['parsed'])) {
                $addr = $current_user['user_address']['parsed'];
                $queue_data['queue_address'] = $addr['additional_address'];
                $queue_data['guest_district'] = $addr['district'];
                $queue_data['guest_amphoe'] = $addr['amphoe'];
                $queue_data['guest_province'] = $addr['province'];
                $queue_data['guest_zipcode'] = $addr['zipcode'];
            }
        } else {
            // Guest user
            $queue_data['queue_user_type'] = 'guest';
            $queue_data['queue_by'] = $this->input->post('queue_by');
            $queue_data['queue_phone'] = $this->input->post('queue_phone');
            $queue_data['queue_email'] = $this->input->post('queue_email');
            $queue_data['queue_number'] = $this->input->post('queue_number');

            // ที่อยู่จากฟอร์ม
            $queue_data['queue_address'] = $this->input->post('queue_address'); // ที่อยู่เพิ่มเติม
            $queue_data['guest_district'] = $this->input->post('guest_district');
            $queue_data['guest_amphoe'] = $this->input->post('guest_amphoe');
            $queue_data['guest_province'] = $this->input->post('guest_province');
            $queue_data['guest_zipcode'] = $this->input->post('guest_zipcode');
        }

        return $queue_data;
    }

    /**
     * รวมวันที่และเวลา
     */
    private function combine_datetime($date, $time_slot)
    {
        try {
            if (empty($date) || empty($time_slot)) {
                return null;
            }

            // แยกเวลาเริ่มต้นจาก time_slot (เช่น "09:00-10:00" -> "09:00")
            $start_time = explode('-', $time_slot)[0];

            return $date . ' ' . $start_time . ':00';

        } catch (Exception $e) {
            log_message('error', 'Error combining datetime: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * จัดการการอัพโหลดไฟล์แนบคิว
     */
    private function handle_queue_file_uploads($queue_id)
    {
        $this->load->library('upload');

        $upload_path = './docs/queue_files/';
        if (!is_dir($upload_path)) {
            if (!mkdir($upload_path, 0755, true)) {
                log_message('error', 'Cannot create upload directory: ' . $upload_path);
                return false;
            }
        }

        // ตั้งค่า upload
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE;
        $config['remove_spaces'] = TRUE;

        $uploaded_files = [];
        $failed_files = [];
        $file_count = count($_FILES['queue_files']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['queue_files']['error'][$i] === UPLOAD_ERR_OK) {
                // สร้างข้อมูลไฟล์แยกสำหรับแต่ละไฟล์
                $_FILES['single_file']['name'] = $_FILES['queue_files']['name'][$i];
                $_FILES['single_file']['type'] = $_FILES['queue_files']['type'][$i];
                $_FILES['single_file']['tmp_name'] = $_FILES['queue_files']['tmp_name'][$i];
                $_FILES['single_file']['error'] = $_FILES['queue_files']['error'][$i];
                $_FILES['single_file']['size'] = $_FILES['queue_files']['size'][$i];

                // ตรวจสอบขนาดไฟล์เพิ่มเติม
                if ($_FILES['single_file']['size'] > (10 * 1024 * 1024)) { // 10MB
                    $failed_files[] = $_FILES['single_file']['name'] . ' (ไฟล์ใหญ่เกินไป)';
                    continue;
                }

                $this->upload->initialize($config);

                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();

                    // บันทึกข้อมูลไฟล์ลงฐานข้อมูล
                    $file_data = [
                        'queue_file_ref_id' => $queue_id,
                        'queue_file_name' => $upload_data['file_name'],
                        'queue_file_original_name' => $upload_data['orig_name'],
                        'queue_file_type' => $upload_data['file_type'],
                        'queue_file_size' => $upload_data['file_size'] * 1024, // แปลงเป็น bytes
                        'queue_file_uploaded_at' => date('Y-m-d H:i:s')
                    ];

                    $insert_result = $this->db->insert('tbl_queue_files', $file_data);

                    if ($insert_result) {
                        $uploaded_files[] = $upload_data['file_name'];
                        log_message('info', 'Queue file uploaded: ' . $upload_data['file_name'] . ' for queue_id: ' . $queue_id);
                    } else {
                        log_message('error', 'Failed to save file info to database: ' . $upload_data['file_name']);
                        // ลบไฟล์ที่อัพโหลดแล้วแต่บันทึก DB ไม่ได้
                        if (file_exists($upload_data['full_path'])) {
                            unlink($upload_data['full_path']);
                        }
                        $failed_files[] = $upload_data['orig_name'] . ' (บันทึกข้อมูลไม่ได้)';
                    }
                } else {
                    $error_msg = $this->upload->display_errors('', '');
                    log_message('error', 'Queue file upload error: ' . $error_msg . ' for queue_id: ' . $queue_id);
                    $failed_files[] = $_FILES['single_file']['name'] . ' (' . $error_msg . ')';
                }
            } elseif ($_FILES['queue_files']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                // มี error อื่นๆ ที่ไม่ใช่ไม่มีไฟล์
                $failed_files[] = $_FILES['queue_files']['name'][$i] . ' (Error: ' . $_FILES['queue_files']['error'][$i] . ')';
            }
        }

        // Log ผลการอัพโหลด
        if (!empty($uploaded_files)) {
            log_message('info', 'Successfully uploaded ' . count($uploaded_files) . ' files for queue_id: ' . $queue_id);
        }

        if (!empty($failed_files)) {
            log_message('warning', 'Failed to upload files for queue_id ' . $queue_id . ': ' . implode(', ', $failed_files));
        }

        return count($uploaded_files) > 0; // return true ถ้าอัพโหลดได้อย่างน้อย 1 ไฟล์
    }




    /**
     * *** เพิ่มใหม่: ดึงสถิติคิวสำหรับ Dashboard ***
     */
    public function get_queue_statistics_for_dashboard()
    {
        try {
            $stats = [];

            // จำนวนคิวทั้งหมด
            $this->db->from('tbl_queue');
            $stats['total'] = $this->db->count_all_results();

            // จำนวนคิวรอยืนยัน
            $this->db->from('tbl_queue');
            $this->db->where('queue_status', 'รอยืนยันการจอง');
            $stats['pending'] = $this->db->count_all_results();

            // จำนวนคิวที่ยืนยันแล้ว + รับเรื่องแล้ว + กำลังดำเนินการ
            $this->db->from('tbl_queue');
            $this->db->where_in('queue_status', [
                'คิวได้รับการยืนยัน',
                'รับเรื่องแล้ว',
                'กำลังดำเนินการ',
                'รอดำเนินการ',
                'รับเรื่องพิจารณา'
            ]);
            $stats['in_progress'] = $this->db->count_all_results();

            // จำนวนคิวเสร็จสิ้น
            $this->db->from('tbl_queue');
            $this->db->where('queue_status', 'เสร็จสิ้น');
            $stats['completed'] = $this->db->count_all_results();

            // จำนวนคิววันนี้
            $this->db->from('tbl_queue');
            $this->db->where('DATE(queue_datesave)', date('Y-m-d'));
            $stats['today'] = $this->db->count_all_results();

            // คิวที่ค้างนาน (มากกว่า 3 วัน)
            $this->db->from('tbl_queue');
            $this->db->where_in('queue_status', [
                'รอยืนยันการจอง',
                'คิวได้รับการยืนยัน',
                'รับเรื่องแล้ว',
                'กำลังดำเนินการ',
                'รอดำเนินการ',
                'รับเรื่องพิจารณา'
            ]);
            $this->db->where('queue_datesave <=', date('Y-m-d H:i:s', strtotime('-3 days')));
            $stats['overdue'] = $this->db->count_all_results();

            // เปอร์เซ็นต์ความสำเร็จ
            if ($stats['total'] > 0) {
                $stats['success_rate'] = round(($stats['completed'] / $stats['total']) * 100, 1);
            } else {
                $stats['success_rate'] = 0;
            }

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_statistics_for_dashboard: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'today' => 0,
                'overdue' => 0,
                'success_rate' => 0
            ];
        }
    }


    // ===================================================================
    // *** METHODS สำหรับการดึงข้อมูลคิว ***
    // ===================================================================

    /**
     * ดึงรายการคิวตามเบอร์โทรศัพท์
     */
    public function get_queues_by_phone($phone)
    {
        try {
            if (empty($phone)) {
                return [];
            }

            $this->db->select('queue_id, queue_topic, queue_detail, queue_by, queue_phone, queue_number, queue_date, queue_time_slot, queue_status, queue_datesave');
            $this->db->from('tbl_queue');
            $this->db->where('queue_phone', $phone);
            $this->db->order_by('queue_datesave', 'DESC');
            $this->db->limit(20); // จำกัดแค่ 20 รายการล่าสุด

            $result = $this->db->get()->result();

            if ($result) {
                // แปลงวันที่ให้อ่านง่าย
                foreach ($result as $queue) {
                    $queue->queue_date_thai = $this->format_thai_date($queue->queue_date);
                    $queue->queue_create_thai = $this->format_thai_date($queue->queue_datesave);
                    $queue->status_color = $this->get_status_color($queue->queue_status);

                    // เพิ่มข้อมูลจำนวนไฟล์แนบ
                    if ($this->db->table_exists('tbl_queue_files')) {
                        $this->db->select('COUNT(*) as file_count');
                        $this->db->from('tbl_queue_files');
                        $this->db->where('queue_file_ref_id', $queue->queue_id);
                        $file_result = $this->db->get()->row();
                        $queue->file_count = $file_result ? $file_result->file_count : 0;
                    } else {
                        $queue->file_count = 0;
                    }
                }
            }

            return $result ?: [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_queues_by_phone: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงรายละเอียดคิวตาม queue_id
     */
    public function get_queue_details($queue_id)
    {
        try {
            if (empty($queue_id)) {
                return null;
            }

            $this->db->select('*');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $this->db->limit(1);

            $result = $this->db->get()->row();

            if ($result) {
                // แปลงวันที่ให้อ่านง่าย
                $result->queue_date_thai = $this->format_thai_date($result->queue_date);
                $result->queue_create_thai = $this->format_thai_date($result->queue_datesave);
                $result->status_color = $this->get_status_color($result->queue_status);

                log_message('debug', 'Queue found: ' . $queue_id);
                return $result;
            }

            log_message('debug', 'Queue not found: ' . $queue_id);
            return null;

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงประวัติการอัพเดทสถานะคิว
     */
    public function get_queue_history($queue_id)
    {
        try {
            if (empty($queue_id)) {
                return [];
            }

            $this->db->select('queue_detail_status, queue_detail_by, queue_detail_com, queue_detail_datesave as queue_detail_date');
            $this->db->from('tbl_queue_detail');
            $this->db->where('queue_detail_case_id', $queue_id);
            $this->db->order_by('queue_detail_datesave', 'DESC');

            $result = $this->db->get()->result();

            if ($result) {
                foreach ($result as $history) {
                    $history->queue_detail_create_thai = $this->format_thai_date($history->queue_detail_date);
                    $history->status_color = $this->get_status_color($history->queue_detail_status);
                }
            }

            return $result ?: [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_history: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงไฟล์แนบของคิว
     */
    public function get_queue_files($queue_id)
    {
        try {
            if (empty($queue_id) || !$this->db->table_exists('tbl_queue_files')) {
                return [];
            }

            $this->db->select('queue_file_name, queue_file_original_name, queue_file_type, queue_file_size, queue_file_uploaded_at');
            $this->db->from('tbl_queue_files');
            $this->db->where('queue_file_ref_id', $queue_id);
            $this->db->order_by('queue_file_uploaded_at', 'ASC');

            $result = $this->db->get()->result();

            if ($result) {
                foreach ($result as $file) {
                    // แปลงขนาดไฟล์ให้อ่านง่าย
                    $file->file_size_formatted = $this->format_file_size($file->queue_file_size);

                    // สร้าง URL สำหรับดาวน์โหลด
                    $file->download_url = site_url('Queue/download_file/' . $file->queue_file_name);

                    // กำหนดไอคอนตามประเภทไฟล์
                    $file->file_icon = $this->get_file_icon($file->queue_file_type);

                    // ตรวจสอบว่าเป็นรูปภาพ
                    $file->is_image = $this->is_image_file($file->queue_file_type);
                }
            }

            return $result ?: [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_files: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลไฟล์
     */
    public function get_file_info($file_name)
    {
        try {
            $this->db->select('queue_file_original_name, queue_file_type');
            $this->db->from('tbl_queue_files');
            $this->db->where('queue_file_name', $file_name);

            return $this->db->get()->row();

        } catch (Exception $e) {
            log_message('error', 'Error in get_file_info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูลคิวพื้นฐาน
     */
    public function get_queue_basic_info($queue_id)
    {
        try {
            $this->db->select('queue_id, queue_topic, queue_by, queue_phone, queue_status, queue_date');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);

            return $this->db->get()->row();

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_basic_info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงสถานะคิวทั้งหมด
     */
    public function get_all_queue_statuses()
    {
        try {
            $this->db->distinct();
            $this->db->select('queue_status');
            $this->db->from('tbl_queue');
            $this->db->where('queue_status IS NOT NULL');
            $this->db->order_by('queue_status', 'ASC');

            $result = $this->db->get()->result();

            return array_column($result, 'queue_status');

        } catch (Exception $e) {
            log_message('error', 'Error in get_all_queue_statuses: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงสถิติคิวเบื้องต้น
     */
    public function get_queue_statistics()
    {
        try {
            $stats = [];

            // จำนวนคิวทั้งหมด
            $this->db->from('tbl_queue');
            $stats['total'] = $this->db->count_all_results();

            // จำนวนคิวรอยืนยัน
            $this->db->from('tbl_queue');
            $this->db->where('queue_status', 'รอยืนยันการจอง');
            $stats['pending'] = $this->db->count_all_results();

            // จำนวนคิวที่ยืนยันแล้ว
            $this->db->from('tbl_queue');
            $this->db->where('queue_status', 'ยืนยันการจอง');
            $stats['confirmed'] = $this->db->count_all_results();

            // จำนวนคิวเสร็จสิ้น
            $this->db->from('tbl_queue');
            $this->db->where('queue_status', 'เสร็จสิ้น');
            $stats['completed'] = $this->db->count_all_results();

            // จำนวนคิววันนี้
            $this->db->from('tbl_queue');
            $this->db->where('DATE(queue_datesave)', date('Y-m-d'));
            $stats['today'] = $this->db->count_all_results();

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_statistics: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'confirmed' => 0,
                'completed' => 0,
                'today' => 0
            ];
        }
    }

    // ===================================================================
    // *** METHODS เดิมที่ปรับปรุงแล้ว - รองรับระบบเก่า ***
    // ===================================================================

    /**
     * เพิ่มคิวใหม่ - method เดิมที่ปรับปรุงแล้ว
     */
    public function add_queue($queue_data = null)
    {
        try {
            log_message('info', '=== Queue_model: add_queue method called ===');

            $this->db->trans_start();

            // *** สร้าง queue_id ใหม่ ***
            $new_id = $this->add_new_id_entry();
            log_message('info', 'Queue_model: Generated queue_id: ' . $new_id);

            // *** ตรวจสอบว่ามี parameter ส่งมาหรือไม่ ***
            if ($queue_data !== null && is_array($queue_data)) {
                // *** กรณีที่ Controller ส่ง parameter มา (วิธีใหม่) ***
                log_message('info', 'Queue_model: Using provided queue_data parameter');

                // ใช้ queue_id ที่ส่งมา หรือใช้ที่เพิ่งสร้าง
                if (!empty($queue_data['queue_id'])) {
                    $new_id = $queue_data['queue_id'];
                    log_message('info', 'Queue_model: Using queue_id from parameter: ' . $new_id);
                } else {
                    $queue_data['queue_id'] = $new_id;
                }

                // ตรวจสอบฟิลด์ที่จำเป็น
                $required_fields = ['queue_topic', 'queue_detail', 'queue_date'];
                foreach ($required_fields as $field) {
                    if (empty($queue_data[$field])) {
                        log_message('error', 'Queue_model: Missing required field: ' . $field);
                        $this->db->trans_rollback();
                        return false;
                    }
                }

                // เพิ่มฟิลด์เริ่มต้นถ้าไม่มี
                if (!isset($queue_data['queue_status'])) {
                    $queue_data['queue_status'] = 'รอยืนยันการจอง';
                }
                if (!isset($queue_data['queue_datesave'])) {
                    $queue_data['queue_datesave'] = date('Y-m-d H:i:s');
                }

                $final_queue_data = $queue_data;

            } else {
                // *** กรณีที่ไม่ส่ง parameter มา (วิธีเก่า - backward compatibility) ***
                log_message('info', 'Queue_model: No parameter provided, using POST data (backward compatibility)');

                // ตรวจสอบว่ามีข้อมูล POST หรือไม่
                $CI =& get_instance();
                if (!$CI->input->post('queue_topic')) {
                    log_message('error', 'Queue_model: No queue_topic in POST data');
                    $this->db->trans_rollback();
                    return false;
                }

                $final_queue_data = array(
                    'queue_id' => $new_id,
                    'queue_topic' => $CI->input->post('queue_topic'),
                    'queue_detail' => $CI->input->post('queue_detail'),
                    'queue_by' => $CI->input->post('queue_by'),
                    'queue_phone' => $CI->input->post('queue_phone'),
                    'queue_number' => $CI->input->post('queue_number'),
                    'queue_date' => $CI->input->post('queue_date'),
                    'queue_time_slot' => $CI->input->post('queue_time_slot'),
                    'queue_status' => 'รอยืนยันการจอง',
                    'queue_datesave' => date('Y-m-d H:i:s')
                );
            }

            log_message('debug', 'Queue_model: Final queue data: ' . json_encode($final_queue_data));

            // *** ตรวจสอบ queue_id ซ้ำ ***
            $existing = $this->db->select('queue_id')
                ->where('queue_id', $final_queue_data['queue_id'])
                ->get('tbl_queue')
                ->row();

            if ($existing) {
                log_message('warning', 'Queue_model: Duplicate queue_id detected: ' . $final_queue_data['queue_id']);
                // สร้าง queue_id ใหม่ด้วย timestamp
                $final_queue_data['queue_id'] = 'Q' . date('ymdHis') . rand(10, 99);
                log_message('info', 'Queue_model: Generated new queue_id to avoid duplicate: ' . $final_queue_data['queue_id']);
                $new_id = $final_queue_data['queue_id'];
            }

            // *** บันทึกข้อมูลคิว ***
            $insert_result = $this->db->insert('tbl_queue', $final_queue_data);

            if (!$insert_result) {
                log_message('error', 'Queue_model: Failed to insert queue data');
                log_message('error', 'Queue_model: Database error: ' . json_encode($this->db->error()));
                $this->db->trans_rollback();
                return false;
            }

            // *** เพิ่มรายละเอียดคิว ***
            $detail_result = $this->add_queue_detail($new_id);

            if (!$detail_result) {
                log_message('error', 'Queue_model: Failed to insert queue detail');
                $this->db->trans_rollback();
                return false;
            }

            $this->db->trans_complete();
            log_message('info', "Line notification send by Queue ID : {$new_id}");
            $this->line_notification->send_line_queue_notification($new_id);

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Queue_model: Transaction failed for queue_id: ' . $new_id);
                return false;
            }

            log_message('info', 'Queue_model: Queue created successfully: ' . $new_id);

            // *** ส่งการแจ้งเตือน (ใช้ function ที่มีอยู่แล้ว) ***
            try {
                // ดึงข้อมูลคิวที่เพิ่งสร้าง
                $queueData = $this->db->get_where('tbl_queue', array('queue_id' => $new_id))->row();

                if ($queueData) {
                    $message = "เรื่องจองคิวติดต่อราชการใหม่!\n";
                    $message .= "คิว: " . $queueData->queue_id . "\n";
                    $message .= "สถานะ: " . $queueData->queue_status . "\n";
                    $message .= "เรื่อง: " . $queueData->queue_topic . "\n";
                    $message .= "รายละเอียด: " . $queueData->queue_detail . "\n";
                    $message .= "ชื่อ-นามสกุล: " . $queueData->queue_by . "\n";
                    $message .= "เบอร์โทรศัพท์: " . $queueData->queue_phone . "\n";
                    $message .= "วันที่นัดหมาย: " . $queueData->queue_date . "\n";

                    // ส่งการแจ้งเตือน LINE
                    $this->broadcastLineOAMessage($message);
                }
            } catch (Exception $e) {
                log_message('warning', 'Queue_model: Notification failed but queue created: ' . $e->getMessage());
            }

            // *** อัพเดท storage (ใช้ function ที่มีอยู่แล้ว) ***
            try {
                if (method_exists($this, 'space_model') && isset($this->space_model)) {
                    $this->space_model->update_server_current();
                }
            } catch (Exception $e) {
                log_message('warning', 'Queue_model: Space model update failed: ' . $e->getMessage());
            }

            // *** Set flash data สำหรับ backward compatibility ***
            $CI =& get_instance();
            if (isset($CI->session)) {
                $CI->session->set_flashdata('save_success', TRUE);
            }

            return $new_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Queue_model: Exception in add_queue: ' . $e->getMessage());
            log_message('error', 'Queue_model: Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }


    /**
     * ฟังก์ชันบันทึกคิวพร้อมไฟล์แนบ - สำหรับ backward compatibility
     */
    public function add_queue_with_files()
    {
        try {
            $this->db->trans_start();

            // สร้าง queue_id ใหม่
            $new_id = $this->add_new_id_entry();

            // *** แก้ไข: ดึงข้อมูล user ที่ถูกต้อง ***
            $user_info = $this->get_current_user_info();

            log_message('info', "Creating queue with user info: " . json_encode($user_info));

            // ตรวจสอบสถานะ anonymous
            $is_anonymous = $this->input->post('is_anonymous') === '1';

            // เตรียมข้อมูลสำหรับบันทึก
            if ($is_anonymous) {
                // โหมดไม่ระบุตัวตน
                $queue_data = [
                    'queue_id' => $new_id,
                    'queue_topic' => $this->input->post('queue_topic'),
                    'queue_detail' => $this->input->post('queue_detail'),
                    'queue_by' => 'ไม่ระบุตัวตน',
                    'queue_phone' => '0000000000',
                    'queue_number' => '0000000000000',
                    'queue_date' => $this->input->post('queue_date'),
                    'queue_status' => 'รอยืนยันการจอง'
                ];
            } elseif ($user_info['is_logged_in']) {
                // ผู้ใช้ที่ login แล้ว
                $queue_data = [
                    'queue_id' => $new_id,
                    'queue_topic' => $this->input->post('queue_topic'),
                    'queue_detail' => $this->input->post('queue_detail'),
                    'queue_by' => $user_info['name'],
                    'queue_phone' => $user_info['phone'],
                    'queue_number' => $user_info['number'] ?: '0000000000000',
                    'queue_date' => $this->input->post('queue_date'),
                    'queue_status' => 'รอยืนยันการจอง'
                ];
            } else {
                // Guest user - ใช้ข้อมูลจากฟอร์ม
                $queue_data = [
                    'queue_id' => $new_id,
                    'queue_topic' => $this->input->post('queue_topic'),
                    'queue_detail' => $this->input->post('queue_detail'),
                    'queue_by' => $this->input->post('queue_by'),
                    'queue_phone' => $this->input->post('queue_phone'),
                    'queue_number' => $this->input->post('queue_number'),
                    'queue_date' => $this->input->post('queue_date'),
                    'queue_status' => 'รอยืนยันการจอง'
                ];
            }

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($queue_data['queue_topic']) || empty($queue_data['queue_detail']) || empty($queue_data['queue_date'])) {
                log_message('error', 'Missing required queue data');
                $this->db->trans_rollback();
                return false;
            }

            // บันทึกข้อมูลคิว
            $insert_result = $this->db->insert('tbl_queue', $queue_data);

            if (!$insert_result) {
                log_message('error', 'Failed to insert queue data: ' . $this->db->last_query());
                $this->db->trans_rollback();
                return false;
            }

            // จัดการไฟล์แนบ (ถ้ามี)
            if (!empty($_FILES['queue_files']['name'][0])) {
                $upload_result = $this->handle_queue_file_uploads($new_id);
                if (!$upload_result) {
                    log_message('warning', 'File upload failed for queue_id: ' . $new_id);
                }
            }

            // บันทึก queue detail
            $detail_result = $this->add_queue_detail($new_id, $user_info);

            if (!$detail_result) {
                log_message('error', 'Failed to insert queue detail for queue_id: ' . $new_id);
                $this->db->trans_rollback();
                return false;
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed for queue_id: ' . $new_id);
                return false;
            }

            // *** แก้ไข: ส่ง notification ที่ถูกต้อง ***
            $this->send_queue_notifications($new_id, $user_info, $is_anonymous);

            // อัพเดท storage
            try {
                if (method_exists($this, 'space_model') && isset($this->space_model)) {
                    $this->space_model->update_server_current();
                }
            } catch (Exception $e) {
                log_message('warning', 'Space model update failed: ' . $e->getMessage());
            }

            log_message('info', 'Queue created successfully with ID: ' . $new_id);
            return $new_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Add queue with files error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูล user ปัจจุบัน - สำหรับ backward compatibility
     */
    private function get_current_user_info()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_id' => null,
            'name' => null,
            'phone' => null,
            'email' => null,
            'number' => null
        ];

        try {
            // ตรวจสอบ public user
            if ($this->session->userdata('mp_id') && $this->session->userdata('mp_email')) {
                $mp_email = $this->session->userdata('mp_email');

                // *** แก้ไข: ใช้ id แทน mp_id ***
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_number');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    $user_info = [
                        'is_logged_in' => true,
                        'user_type' => 'public',
                        'user_id' => $user_data->id, // *** ใช้ auto increment id ***
                        'mp_id' => $user_data->mp_id,
                        'name' => trim(($user_data->mp_prefix ?: '') . ' ' . $user_data->mp_fname . ' ' . $user_data->mp_lname),
                        'phone' => $user_data->mp_phone,
                        'email' => $user_data->mp_email,
                        'number' => $user_data->mp_number
                    ];

                    log_message('info', "Public user detected: id={$user_data->id}, mp_id={$user_data->mp_id}, email={$mp_email}");
                }
            }
            // ตรวจสอบ staff user
            elseif ($this->session->userdata('m_id') && $this->session->userdata('m_email')) {
                $m_email = $this->session->userdata('m_email');

                $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system');
                $this->db->from('tbl_member');
                $this->db->where('m_email', $m_email);
                $this->db->where('m_status', '1');
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    $user_info = [
                        'is_logged_in' => true,
                        'user_type' => 'staff',
                        'user_id' => $user_data->m_id,
                        'name' => trim($user_data->m_fname . ' ' . $user_data->m_lname),
                        'phone' => $user_data->m_phone,
                        'email' => $user_data->m_email,
                        'number' => null, // Staff ไม่มีเลขบัตรประชาชน
                        'm_system' => $user_data->m_system
                    ];

                    log_message('info', "Staff user detected: id={$user_data->m_id}, email={$m_email}");
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting current user info: ' . $e->getMessage());
        }

        return $user_info;
    }

    /**
     * ส่ง notifications - เวอร์ชันเก่า
     */
    private function send_queue_notifications($queue_id, $current_user)
    {
        try {
            // โหลด Notification_lib
            if (!isset($this->Notification_lib)) {
                $this->load->library('Notification_lib');
            }

            $queue_data = $this->db->get_where('tbl_queue', ['queue_id' => $queue_id])->row();

            if (!$queue_data) {
                log_message('error', 'Queue data not found for notification: ' . $queue_id);
                return false;
            }

            // เตรียมข้อมูล
            $queue_topic = $queue_data->queue_topic;
            $queue_by = $queue_data->queue_by;
            $queue_user_id = $current_user['user_id'] ?? null;
            $queue_user_type = $current_user['user_type'] ?? 'guest';

            log_message('info', "Sending queue notifications for ID: {$queue_id}");

            // ใช้ method ที่มีอยู่แล้วใน notification_lib
            $notification_result = $this->Notification_lib->new_queue($queue_id, $queue_topic, $queue_by);

            // เพิ่ม individual notification สำหรับ public user ที่ login
            if ($queue_user_id && $queue_user_type === 'public') {
                $individual_result = $this->Notification_lib->create_custom_notification(
                    'queue',
                    'คุณได้จองคิวสำเร็จ',
                    "การจองคิว \"{$queue_topic}\" ของคุณได้รับการบันทึกเรียบร้อยแล้ว หมายเลขคิว: {$queue_id}",
                    'public',
                    [
                        'reference_id' => $queue_id,
                        'reference_table' => 'tbl_queue',
                        'target_user_id' => $queue_user_id,
                        'target_user_type' => $queue_user_type,
                        'priority' => 'high',
                        'icon' => 'fas fa-check-circle',
                        'url' => site_url("Queue/follow_queue?auto_search={$queue_id}"),
                        'data' => [
                            'queue_id' => $queue_id,
                            'topic' => $queue_topic,
                            'status' => $queue_data->queue_status,
                            'created_at' => date('Y-m-d H:i:s'),
                            'type' => 'individual_confirmation'
                        ]
                    ]
                );

                log_message('info', "Individual queue notification sent: " . ($individual_result ? 'SUCCESS' : 'FAILED'));
            }

            return $notification_result;

        } catch (Exception $e) {
            log_message('error', 'Failed to send queue notifications: ' . $e->getMessage());
            return false;
        }
    }





    private function send_queue_update_notifications($queue_id, $new_status, $updated_by = null)
    {
        try {
            // โหลด Notification_lib
            if (!isset($this->Notification_lib)) {
                $this->load->library('Notification_lib');
            }

            $queue_data = $this->db->get_where('tbl_queue', ['queue_id' => $queue_id])->row();

            if (!$queue_data) {
                log_message('error', 'Queue not found for update notification: ' . $queue_id);
                return false;
            }

            $staff_name = $updated_by ?: ($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname'));
            $queue_user_id = $queue_data->queue_user_id;
            $queue_user_type = $queue_data->queue_user_type;

            log_message('info', "Sending queue update notifications for {$queue_id}");

            // Notification สำหรับ Staff
            $staff_result = $this->Notification_lib->create_custom_notification(
                'queue',
                'อัปเดตสถานะคิว',
                "คิว #{$queue_id} อัปเดตสถานะเป็น: {$new_status} โดย {$staff_name}",
                'staff',
                [
                    'reference_id' => $queue_id,
                    'reference_table' => 'tbl_queue',
                    'priority' => 'normal',
                    'icon' => 'fas fa-edit',
                    'url' => site_url("Queue_backend/detail/{$queue_id}"),
                    'data' => [
                        'queue_id' => $queue_id,
                        'topic' => $queue_data->queue_topic,
                        'new_status' => $new_status,
                        'updated_by' => $staff_name,
                        'type' => 'staff_status_update'
                    ]
                ]
            );

            // Individual notification สำหรับเจ้าของคิว (ถ้าเป็น public user)
            $individual_result = true;
            if ($queue_user_id && $queue_user_type === 'public') {
                $individual_result = $this->Notification_lib->create_custom_notification(
                    'queue',
                    'อัปเดตสถานะคิวของคุณ',
                    "คิว \"{$queue_data->queue_topic}\" ได้รับการอัปเดตสถานะเป็น: {$new_status}",
                    'public',
                    [
                        'reference_id' => $queue_id,
                        'reference_table' => 'tbl_queue',
                        'target_user_id' => $queue_user_id,
                        'target_user_type' => $queue_user_type,
                        'priority' => 'high',
                        'icon' => 'fas fa-bell',
                        'url' => site_url("Queue/follow_queue?auto_search={$queue_id}"),
                        'data' => [
                            'queue_id' => $queue_id,
                            'topic' => $queue_data->queue_topic,
                            'new_status' => $new_status,
                            'updated_by' => $staff_name,
                            'type' => 'individual_status_update'
                        ]
                    ]
                );
            }

            return $staff_result && $individual_result;

        } catch (Exception $e) {
            log_message('error', 'Failed to send queue update notifications: ' . $e->getMessage());
            return false;
        }
    }




    /**
     * ส่งการแจ้งเตือน LINE สำหรับคิวใหม่
     */
    private function send_queue_line_notification($queue_id)
    {
        try {
            $queue_data = $this->db->get_where('tbl_queue', ['queue_id' => $queue_id])->row();

            if ($queue_data) {
                $message = "🎫 การจองคิวใหม่\n";
                $message .= "คิวที่: " . $queue_data->queue_id . "\n";
                $message .= "เรื่อง: " . $queue_data->queue_topic . "\n";
                $message .= "ผู้จอง: " . $queue_data->queue_by . "\n";
                $message .= "เบอร์โทร: " . $queue_data->queue_phone . "\n";
                $message .= "วันที่นัด: " . $this->format_thai_date($queue_data->queue_date) . "\n";
                $message .= "สถานะ: " . $queue_data->queue_status . "\n";
                $message .= "รายละเอียด: " . mb_substr($queue_data->queue_detail, 0, 100) . "...";

                // ตรวจสอบไฟล์แนบ
                $files = $this->db->get_where('tbl_queue_files', ['queue_file_ref_id' => $queue_id])->result();
                if (!empty($files)) {
                    $message .= "\n📎 ไฟล์แนบ: " . count($files) . " ไฟล์";
                }

                $this->broadcastLineOAMessage($message);
            }

        } catch (Exception $e) {
            log_message('error', 'Send queue LINE notification error: ' . $e->getMessage());
        }
    }

    /**
     * ดึงรายการคิว - method เดิมที่ปรับปรุง
     */
    public function get_queues($queue_status = null)
    {
        $this->db->select('*');
        $this->db->from('tbl_queue');

        // ตรวจสอบและเพิ่มเงื่อนไขสถานะคำร้องเรียน ถ้ามี
        if ($queue_status) {
            $this->db->where('queue_status', $queue_status);
        }

        // เรียงลำดับตาม queue_datesave จากใหม่ไปเก่า (DESC)
        $this->db->order_by('queue_datesave', 'DESC');

        // ดึงข้อมูลและส่งกลับ
        return $this->db->get()->result();
    }

    /**
     * ดึงรูปภาพสำหรับคิว - method เดิม
     */
    public function get_images_for_queue($queue_id)
    {
        // ตรวจสอบว่ามีตาราง tbl_queue_img หรือไม่
        if ($this->db->table_exists('tbl_queue_img')) {
            $this->db->select('queue_img_img');
            $this->db->from('tbl_queue_img');
            $this->db->where('queue_img_ref_id', $queue_id);
            return $this->db->get()->result();
        }
        return [];
    }

    /**
     * อ่านข้อมูลคิว - method เดิม
     */
    public function read($queue_id)
    {
        $this->db->where('queue_id', $queue_id);
        $query = $this->db->get('tbl_queue');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    /**
     * อ่านรายละเอียดคิว - method เดิม
     */
    public function read_detail($queue_id)
    {
        $this->db->where('queue_detail_case_id', $queue_id);
        $this->db->order_by('queue_detail_datesave', 'asc');
        $query = $this->db->get('tbl_queue_detail');
        return $query->result();
    }

    /**
     * อัพเดทสถานะคิว - method เดิม
     */
    public function updatequeueStatus($queueId, $queueStatus)
    {
        $data = array(
            'queue_status' => $queueStatus
        );

        $this->db->where('queue_id', $queueId);
        $result = $this->db->update('tbl_queue', $data);

        return $result;
    }

    /**
     * คิวสำหรับ dashboard - method เดิม
     */
    public function dashboard_queue()
    {
        $this->db->select('*');
        $this->db->from('tbl_queue as c');
        $this->db->limit(3);
        $this->db->order_by('c.queue_datesave', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * คิวสำหรับ intranet - method เดิม
     */
    public function intranet_queue()
    {
        $this->db->select('*');
        $this->db->from('tbl_queue as c');
        $this->db->limit(15);
        $this->db->order_by('c.queue_datesave', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * อัพเดทคิว - method เดิมที่ปรับปรุง
     */
    public function updatequeue($queue_detail_case_id, $queue_detail_status, $queue_detail_com)
    {
        try {
            $this->db->trans_start();

            // อัปเดต tbl_queue
            $this->db->set('queue_status', $queue_detail_status);
            $this->db->set('queue_dateupdate', date('Y-m-d H:i:s'));
            $this->db->where('queue_id', $queue_detail_case_id);
            $this->db->update('tbl_queue');

            // เพิ่มข้อมูลใหม่ลงใน tbl_queue_detail
            $data = array(
                'queue_detail_case_id' => $queue_detail_case_id,
                'queue_detail_status' => $queue_detail_status,
                'queue_detail_by' => $this->session->userdata('m_fname') ?: 'ระบบ',
                'queue_detail_com' => $queue_detail_com,
                'queue_detail_staff_id' => $this->session->userdata('m_id'),
                'queue_detail_datesave' => date('Y-m-d H:i:s')
            );
            $this->db->insert('tbl_queue_detail', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // *** เพิ่มส่วนนี้: ส่ง notification ***
            $this->send_queue_update_notifications($queue_detail_case_id, $queue_detail_status);

            // ส่ง LINE notification (เดิม)
            $this->send_queue_line_notification($queue_detail_case_id);

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in updatequeue: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ยกเลิกคิว - method เดิมที่ปรับปรุง
     */
    public function statusCancel($queue_detail_case_id, $queue_detail_status, $queue_detail_com)
    {
        try {
            $this->db->trans_start();

            // อัปเดต tbl_queue
            $this->db->set('queue_status', 'ยกเลิก');
            $this->db->set('queue_dateupdate', date('Y-m-d H:i:s'));
            $this->db->where('queue_id', $queue_detail_case_id);
            $this->db->update('tbl_queue');

            // เพิ่มข้อมูลใหม่ลงใน tbl_queue_detail
            $data = array(
                'queue_detail_case_id' => $queue_detail_case_id,
                'queue_detail_status' => 'ยกเลิก',
                'queue_detail_com' => $queue_detail_com,
                'queue_detail_by' => $this->session->userdata('m_fname') ?: 'ระบบ',
                'queue_detail_staff_id' => $this->session->userdata('m_id'),
                'queue_detail_datesave' => date('Y-m-d H:i:s')
            );
            $this->db->insert('tbl_queue_detail', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // *** เพิ่มส่วนนี้: ส่ง notification ***
            $this->send_queue_update_notifications($queue_detail_case_id, 'ยกเลิก');

            // ส่ง LINE notification (เดิม)
            $this->send_queue_line_notification($queue_detail_case_id);

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in statusCancel: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * ดึงรายละเอียดล่าสุด - method เดิม
     */
    public function getLatestDetail($queue_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_queue_detail');
        $this->db->where('queue_detail_case_id', $queue_id);
        $this->db->order_by('queue_detail_datesave', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return null;
    }

    /**
     * เพิ่มรายละเอียดคิว - method เดิมที่ปรับปรุง
     */
    public function add_queue_detail($queue_id, $user_info = null)
    {
        try {
            $queue_by = 'ระบบ';

            // ตรวจสอบผู้สร้าง
            if ($this->input->post('is_anonymous') === '1') {
                $queue_by = 'ไม่ระบุตัวตน';
            } elseif ($user_info && isset($user_info['is_logged_in']) && $user_info['is_logged_in']) {
                $queue_by = $user_info['name'];
            } elseif ($this->session->userdata('mp_fname')) {
                $queue_by = $this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname');
            } elseif ($this->session->userdata('m_fname')) {
                $queue_by = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');
            } elseif ($this->input->post('queue_by')) {
                $queue_by = $this->input->post('queue_by');
            }

            $data = array(
                'queue_detail_case_id' => $queue_id,
                'queue_detail_status' => 'รอยืนยันการจอง',
                'queue_detail_by' => $queue_by,
                'queue_detail_com' => 'สร้างการจองคิวใหม่',
                'queue_detail_datesave' => date('Y-m-d H:i:s')
            );

            $query = $this->db->insert('tbl_queue_detail', $data);

            if ($query) {
                log_message('info', 'Queue detail added successfully for queue_id: ' . $queue_id);
                return true;
            } else {
                log_message('error', 'Failed to insert queue detail for queue_id: ' . $queue_id);
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error in add_queue_detail: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * นับคิวปีนี้ - method เดิม
     */
    public function count_queue_year()
    {
        $this->db->select('COUNT(queue_id) AS total_queue_year');
        $this->db->from('tbl_queue');
        $this->db->where('YEAR(queue_datesave)', date('Y'));
        $query = $this->db->get();
        return $query->row()->total_queue_year;
    }

    /**
     * นับคิวสำเร็จ - method เดิม
     */
    public function count_queue_success()
    {
        $this->db->select('COUNT(queue_id) AS total_queue_success');
        $this->db->from('tbl_queue');
        $this->db->where('tbl_queue.queue_status', 'คิวได้รับการยืนยัน');
        $query = $this->db->get();
        return $query->row()->total_queue_success;
    }

    /**
     * นับคิวรอดำเนินการ - method เดิม
     */
    public function count_queue_operation()
    {
        $this->db->select('COUNT(queue_id) AS total_queue_operation');
        $this->db->from('tbl_queue');
        $this->db->where('tbl_queue.queue_status', 'รอดำเนินการ');
        $query = $this->db->get();
        return $query->row()->total_queue_operation;
    }

    /**
     * นับคิวรับเรื่องแล้ว - method เดิม
     */
    public function count_queue_accept()
    {
        $this->db->select('COUNT(queue_id) AS total_queue_accept');
        $this->db->from('tbl_queue');
        $this->db->where('tbl_queue.queue_status', 'รับเรื่องแล้ว');
        $query = $this->db->get();
        return $query->row()->total_queue_accept;
    }

    /**
     * นับคิวกำลังดำเนินการ - method เดิม
     */
    public function count_queue_doing()
    {
        $this->db->select('COUNT(queue_id) AS total_queue_doing');
        $this->db->from('tbl_queue');
        $this->db->where('tbl_queue.queue_status', 'กำลังดำเนินการ');
        $query = $this->db->get();
        return $query->row()->total_queue_doing;
    }

    /**
     * นับคิวรอยืนยัน - method เดิม
     */
    public function count_queue_wait()
    {
        $this->db->select('COUNT(queue_id) AS total_queue_wait');
        $this->db->from('tbl_queue');
        $this->db->where('tbl_queue.queue_status', 'รอยืนยันการจอง');
        $query = $this->db->get();
        return $query->row()->total_queue_wait;
    }

    /**
     * นับคิวยกเลิก - method เดิม
     */
    public function count_queue_cancel()
    {
        $this->db->select('COUNT(queue_id) AS total_queue_cancel');
        $this->db->from('tbl_queue');
        $this->db->where('tbl_queue.queue_status', 'คิวได้ถูกยกเลิก');
        $query = $this->db->get();
        return $query->row()->total_queue_cancel;
    }

    /**
     * ดึงหัวข้อคิว - method เดิม
     */
    public function get_queue_topic($queue_id)
    {
        $this->db->select('queue_topic');
        $this->db->from('tbl_queue');
        $this->db->where('queue_id', $queue_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->queue_topic;
        }
        return null;
    }

    // ===================================================================
    // *** METHODS สำหรับการส่ง LINE Notification (เดิม) ***
    // ===================================================================

    /**
     * ส่งข้อความ LINE OA - method เดิมที่ปรับปรุง
     */
    private function broadcastLineOAMessage($message, $imagePath = null)
    {
        try {
            if (empty($this->channelAccessToken)) {
                log_message('info', 'LINE token not configured');
                return false;
            }

            if (!$this->db->table_exists('tbl_line')) {
                log_message('warning', 'Table tbl_line does not exist');
                return false;
            }

            $userIds = $this->db->select('line_user_id')
                ->from('tbl_line')
                ->where('line_status', 'show')
                ->get()
                ->result_array();

            $to = array_column($userIds, 'line_user_id');
            if (empty($to)) {
                log_message('info', 'No LINE users found');
                return false;
            }

            $to = array_filter($to);
            if (empty($to)) {
                log_message('info', 'No valid LINE user IDs');
                return false;
            }

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->channelAccessToken
            ];

            $messages = [
                [
                    'type' => 'text',
                    'text' => $message
                ]
            ];

            if ($imagePath) {
                $imageUrl = $this->uploadImageToLine($imagePath);
                if ($imageUrl) {
                    $messages[] = [
                        'type' => 'image',
                        'originalContentUrl' => $imageUrl,
                        'previewImageUrl' => $imageUrl
                    ];
                }
            }

            $chunks = array_chunk($to, 500);
            $success = true;

            foreach ($chunks as $receivers) {
                $data = [
                    'to' => $receivers,
                    'messages' => $messages
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->lineApiUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($httpCode !== 200) {
                    log_message('error', 'LINE API error: HTTP ' . $httpCode . ' - ' . $response);
                    $success = false;
                }

                curl_close($ch);
            }

            return $success;

        } catch (Exception $e) {
            log_message('error', 'LINE broadcast error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัพโหลดรูปภาพไปยัง LINE - method เดิม
     */
    private function uploadImageToLine($imagePath)
    {
        // สร้าง URL ที่เข้าถึงได้จากภายนอกสำหรับรูปภาพ
        $baseUrl = 'https://www.phuengdad.go.th/docs/img/'; // แก้เป็น URL ของเว็บคุณ
        $fileName = basename($imagePath);
        return $baseUrl . $fileName;
    }

    // ===================================================================
    // *** HELPER METHODS ***
    // ===================================================================

    /**
     * แปลงวันที่เป็นภาษาไทย
     */
    private function format_thai_date($datetime)
    {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return '-';
        }

        try {
            $thai_months = [
                '01' => 'ม.ค.',
                '02' => 'ก.พ.',
                '03' => 'มี.ค.',
                '04' => 'เม.ย.',
                '05' => 'พ.ค.',
                '06' => 'มิ.ย.',
                '07' => 'ก.ค.',
                '08' => 'ส.ค.',
                '09' => 'ก.ย.',
                '10' => 'ต.ค.',
                '11' => 'พ.ย.',
                '12' => 'ธ.ค.'
            ];

            $timestamp = strtotime($datetime);
            $day = date('j', $timestamp);
            $month = $thai_months[date('m', $timestamp)];
            $year = date('Y', $timestamp) + 543;
            $time = date('H:i', $timestamp);

            return $day . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';

        } catch (Exception $e) {
            return $datetime;
        }
    }

    /**
     * กำหนดสีตามสถานะ
     */
    private function get_status_color($status)
    {
        $status_colors = [
            'รอยืนยันการจอง' => 'warning',
            'ยืนยันการจอง' => 'info',
            'คิวได้รับการยืนยัน' => 'info',
            'รับเรื่องแล้ว' => 'primary',
            'กำลังดำเนินการ' => 'primary',
            'รอดำเนินการ' => 'primary',
            'เสร็จสิ้น' => 'success',
            'ยกเลิก' => 'danger',
            'คิวได้ถูกยกเลิก' => 'danger',
            'ไม่อนุมัติ' => 'secondary'
        ];

        return isset($status_colors[$status]) ? $status_colors[$status] : 'secondary';
    }

    /**
     * แปลงขนาดไฟล์ให้อ่านง่าย
     */
    private function format_file_size($size)
    {
        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        } else {
            return $size . ' bytes';
        }
    }

    /**
     * กำหนดไอคอนตามประเภทไฟล์
     */
    private function get_file_icon($file_type)
    {
        if (strpos($file_type, 'image/') === 0) {
            return 'fa-file-image';
        } elseif (strpos($file_type, 'application/pdf') === 0) {
            return 'fa-file-pdf';
        } elseif (
            strpos($file_type, 'application/msword') === 0 ||
            strpos($file_type, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') === 0
        ) {
            return 'fa-file-word';
        } elseif (
            strpos($file_type, 'application/vnd.ms-excel') === 0 ||
            strpos($file_type, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') === 0
        ) {
            return 'fa-file-excel';
        } else {
            return 'fa-file';
        }
    }

    /**
     * ตรวจสอบว่าเป็นไฟล์รูปภาพ
     */
    private function is_image_file($file_type)
    {
        return strpos($file_type, 'image/') === 0;
    }

    /**
     * ตรวจสอบการมีอยู่ของตาราง
     */
    public function table_exists($table_name)
    {
        return $this->db->table_exists($table_name);
    }







    /**
     * ดึงข้อมูลคิวพร้อมตัวกรอง
     */
    public function get_queues_with_filters($filters = [], $limit = 20, $offset = 0)
    {
        try {
            $this->db->select('q.*, COUNT(qf.queue_file_id) as file_count');
            $this->db->from('tbl_queue q');
            $this->db->join('tbl_queue_files qf', 'q.queue_id = qf.queue_file_ref_id', 'left');

            // Apply filters
            if (!empty($filters['status'])) {
                $this->db->where('q.queue_status', $filters['status']);
            }

            if (!empty($filters['user_type'])) {
                $this->db->where('q.queue_user_type', $filters['user_type']);
            }

            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(q.queue_datesave) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(q.queue_datesave) <=', $filters['date_to']);
            }

            if (!empty($filters['search'])) {
                $this->db->group_start();
                $this->db->like('q.queue_topic', $filters['search']);
                $this->db->or_like('q.queue_detail', $filters['search']);
                $this->db->or_like('q.queue_by', $filters['search']);
                $this->db->or_like('q.queue_phone', $filters['search']);
                $this->db->or_like('q.queue_id', $filters['search']);
                $this->db->group_end();
            }

            $this->db->group_by('q.queue_id');
            $this->db->order_by('q.queue_datesave', 'DESC');

            // Count total for pagination
            $total_query = clone $this->db;
            $total_rows = $total_query->count_all_results();

            // Apply limit and offset
            $this->db->limit($limit, $offset);
            $queues = $this->db->get()->result();

            return [
                'data' => $queues,
                'total' => $total_rows
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in get_queues_with_filters: ' . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * สถิติคิวแบบละเอียด
     */
    public function get_queue_statistics_detailed()
    {
        try {
            $stats = [];

            // Total queues
            $this->db->from('tbl_queue');
            $stats['total'] = $this->db->count_all_results();

            // By status
            $status_query = $this->db->select('queue_status, COUNT(*) as count')
                ->from('tbl_queue')
                ->group_by('queue_status')
                ->get();

            $stats['by_status'] = [];
            foreach ($status_query->result() as $row) {
                $stats['by_status'][$row->queue_status] = $row->count;
            }

            // Today's queues
            $this->db->from('tbl_queue');
            $this->db->where('DATE(queue_datesave)', date('Y-m-d'));
            $stats['today'] = $this->db->count_all_results();

            // This week
            $this->db->from('tbl_queue');
            $this->db->where('WEEK(queue_datesave)', date('W'));
            $this->db->where('YEAR(queue_datesave)', date('Y'));
            $stats['this_week'] = $this->db->count_all_results();

            // This month
            $this->db->from('tbl_queue');
            $this->db->where('MONTH(queue_datesave)', date('n'));
            $this->db->where('YEAR(queue_datesave)', date('Y'));
            $stats['this_month'] = $this->db->count_all_results();

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_statistics_detailed: ' . $e->getMessage());
            return ['total' => 0, 'by_status' => [], 'today' => 0, 'this_week' => 0, 'this_month' => 0];
        }
    }

    /**
     * ตัวเลือกสถานะคิวทั้งหมด
     */
    public function get_all_queue_statuses_options()
    {
        try {
            $this->db->distinct();
            $this->db->select('queue_status');
            $this->db->from('tbl_queue');
            $this->db->where('queue_status IS NOT NULL');
            $this->db->order_by('queue_status', 'ASC');

            $result = $this->db->get()->result();

            $options = [];
            foreach ($result as $row) {
                $options[] = ['queue_status' => $row->queue_status];
            }

            return $options;

        } catch (Exception $e) {
            log_message('error', 'Error in get_all_queue_statuses_options: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * แนวโน้มคิวรายวัน
     */
    public function get_daily_queue_trends($days = 30)
    {
        try {
            $this->db->select('DATE(queue_datesave) as date, COUNT(*) as count');
            $this->db->from('tbl_queue');
            $this->db->where('queue_datesave >=', date('Y-m-d', strtotime("-{$days} days")));
            $this->db->group_by('DATE(queue_datesave)');
            $this->db->order_by('date', 'ASC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Error in get_daily_queue_trends: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * คิวที่ค้างนาน สำหรับ Alert
     */
    public function get_pending_queues_for_alerts()
    {
        try {
            // *** ใช้ SQL เดียวกับที่ใช้ใน queue_alerts ***
            $sql = "
            SELECT 
                queue_id, 
                queue_topic, 
                queue_detail,
                queue_status, 
                queue_by, 
                queue_phone, 
                queue_datesave,
                queue_dateupdate,
                queue_user_type,
                DATEDIFF(NOW(), queue_datesave) as days_old
            FROM tbl_queue 
            WHERE queue_status IN (?, ?, ?, ?, ?, ?, ?)
            AND DATEDIFF(NOW(), queue_datesave) >= 3
            ORDER BY DATEDIFF(NOW(), queue_datesave) DESC, queue_datesave ASC 
            LIMIT 50
        ";

            $params = [
                'รอยืนยันการจอง',
                'ยืนยันการจอง',
                'คิวได้รับการยืนยัน',
                'รับเรื่องพิจารณา',
                'รับเรื่องแล้ว',
                'รอดำเนินการ',
                'กำลังดำเนินการ'
            ];

            $query = $this->db->query($sql, $params);
            $result = $query->result();

            log_message('info', 'get_pending_queues_for_alerts found ' . count($result) . ' queues');

            return $result ?: [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_pending_queues_for_alerts: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * อัพเดทสถานะคิวแบบละเอียด
     */
    public function update_queue_status_enhanced($queue_id, $new_status, $note, $updated_by)
    {
        try {
            $this->db->trans_start();

            // อัพเดท tbl_queue
            $update_data = [
                'queue_status' => $new_status,
                'queue_dateupdate' => date('Y-m-d H:i:s')
            ];

            $this->db->where('queue_id', $queue_id);
            $this->db->update('tbl_queue', $update_data);

            // เพิ่มประวัติใน tbl_queue_detail
            $detail_data = [
                'queue_detail_case_id' => $queue_id,
                'queue_detail_status' => $new_status,
                'queue_detail_by' => $updated_by,
                'queue_detail_com' => $note ?: 'อัพเดทสถานะเป็น: ' . $new_status,
                'queue_detail_datesave' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_queue_detail', $detail_data);

            $this->db->trans_complete();

            return $this->db->trans_status() !== FALSE;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in update_queue_status_enhanced: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * ดึงข้อมูลคิวที่ต้องติดตามพร้อมตัวกรอง
     */
    public function get_queue_alerts_with_filters($filters = [], $limit = 25, $offset = 0)
    {
        try {
            $where_conditions = [];
            $params = [];

            // เงื่อนไขพื้นฐาน
            $where_conditions[] = "queue_status IN (?, ?, ?, ?, ?, ?, ?)";
            $params = array_merge($params, [
                'รอยืนยันการจอง',
                'ยืนยันการจอง',
                'คิวได้รับการยืนยัน',
                'รับเรื่องพิจารณา',
                'รับเรื่องแล้ว',
                'รอดำเนินการ',
                'กำลังดำเนินการ'
            ]);

            // ตัวกรองจำนวนวัน
            $days_min = !empty($filters['days_min']) ? intval($filters['days_min']) : 3;
            $where_conditions[] = "DATEDIFF(NOW(), queue_datesave) >= ?";
            $params[] = $days_min;

            if (!empty($filters['days_max'])) {
                $where_conditions[] = "DATEDIFF(NOW(), queue_datesave) <= ?";
                $params[] = intval($filters['days_max']);
            }

            // ตัวกรองสถานะ
            if (!empty($filters['status'])) {
                $where_conditions[] = "queue_status = ?";
                $params[] = $filters['status'];
            }

            // ตัวกรองตามระดับความสำคัญ
            if (!empty($filters['priority'])) {
                switch ($filters['priority']) {
                    case 'critical':
                        $where_conditions[] = "DATEDIFF(NOW(), queue_datesave) >= 14";
                        break;
                    case 'danger':
                        $where_conditions[] = "DATEDIFF(NOW(), queue_datesave) BETWEEN 7 AND 13";
                        break;
                    case 'warning':
                        $where_conditions[] = "DATEDIFF(NOW(), queue_datesave) BETWEEN 3 AND 6";
                        break;
                }
            }

            // ตัวกรองการค้นหา
            if (!empty($filters['search'])) {
                $where_conditions[] = "(queue_id LIKE ? OR queue_topic LIKE ? OR queue_by LIKE ? OR queue_phone LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
            }

            $where_clause = implode(' AND ', $where_conditions);

            // นับจำนวนทั้งหมด
            $count_sql = "
            SELECT COUNT(*) as total
            FROM tbl_queue 
            WHERE {$where_clause}
        ";

            $count_query = $this->db->query($count_sql, $params);
            $total_rows = $count_query->row()->total;

            // ดึงข้อมูลแบบแบ่งหน้า
            $data_sql = "
            SELECT 
                queue_id, 
                queue_topic, 
                queue_detail,
                queue_status, 
                queue_by, 
                queue_phone, 
                queue_datesave,
                queue_dateupdate,
                queue_user_type,
                DATEDIFF(NOW(), queue_datesave) as days_old
            FROM tbl_queue 
            WHERE {$where_clause}
            ORDER BY DATEDIFF(NOW(), queue_datesave) DESC, queue_datesave ASC 
            LIMIT ? OFFSET ?
        ";

            $params[] = $limit;
            $params[] = $offset;

            $data_query = $this->db->query($data_sql, $params);
            $data = $data_query->result();

            return [
                'data' => $data,
                'total' => $total_rows
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in get_queue_alerts_with_filters: ' . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }


}

?>