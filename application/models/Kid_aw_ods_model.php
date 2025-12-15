<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kid_aw_ods_model extends CI_Model
{
    private $channelAccessToken;
    private $lineApiUrl;

    public function __construct()
    {
        parent::__construct();

        // ใช้ helper function get_config_value เพื่อดึงค่า token จากฐานข้อมูล
        if (function_exists('get_config_value')) {
            $this->channelAccessToken = get_config_value('line_token');
        }
        $this->lineApiUrl = 'https://api.line.me/v2/bot/message/multicast';
    }

    /**
     * *** แก้ไข: ดึงแบบฟอร์มทั้งหมดโดยไม่ซ้ำ ***
     */
    public function get_all_forms()
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_form')) {
                log_message('warning', 'Table tbl_kid_aw_form does not exist');
                return [];
            }
            
            // Debug: ตรวจสอบข้อมูลทั้งหมดก่อน
            $this->db->select('kid_aw_form_id, kid_aw_form_name, kid_aw_form_status, kid_aw_form_type');
            $this->db->from('tbl_kid_aw_form');
            $all_query = $this->db->get();
            $all_forms = $all_query->result();
            
            log_message('debug', 'Total forms in database: ' . count($all_forms));
            foreach ($all_forms as $form) {
                log_message('debug', "Form ID: {$form->kid_aw_form_id}, Name: {$form->kid_aw_form_name}, Status: {$form->kid_aw_form_status}");
            }
            
            // Query สำหรับแบบฟอร์มที่เปิดใช้งาน
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_form');
            $this->db->where('kid_aw_form_status', 1); // เฉพาะที่เปิดใช้งาน
            $this->db->where('kid_aw_form_file IS NOT NULL');
            $this->db->where('kid_aw_form_file !=', '');
            $this->db->order_by('kid_aw_form_type', 'ASC');
            $this->db->order_by('kid_aw_form_name', 'ASC');
            
            // Debug SQL
            $compiled_sql = $this->db->get_compiled_select();
            log_message('debug', 'Compiled SQL: ' . $compiled_sql);
            
            // Execute query
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_form');
            $this->db->where('kid_aw_form_status', 1);
            $this->db->where('kid_aw_form_file IS NOT NULL');
            $this->db->where('kid_aw_form_file !=', '');
            $this->db->order_by('kid_aw_form_type', 'ASC');
            $this->db->order_by('kid_aw_form_name', 'ASC');
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $active_forms = $query->result();
                log_message('debug', 'Active forms count: ' . count($active_forms));
                return $active_forms;
            }
            
            log_message('info', 'No active forms found');
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_all_forms: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * อัปเดตสถานะแบบฟอร์ม
     */
    public function update_form_status($form_id, $status)
    {
        try {
            $this->db->where('kid_aw_form_id', $form_id);
            $this->db->update('tbl_kid_aw_form', [
                'kid_aw_form_status' => $status,
                'kid_aw_form_updated_by' => $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname')
            ]);
            
            if ($this->db->affected_rows() > 0) {
                log_message('info', "Form status updated: ID {$form_id} to status {$status}");
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error updating form status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เพิ่ม: ดึงแบบฟอร์มตามประเภท
     */
    public function get_forms_by_type($type)
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_form')) {
                return [];
            }
            
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_form');
            $this->db->where('kid_aw_form_status', 1);
            $this->db->where('kid_aw_form_type', $type);
            $this->db->order_by('kid_aw_form_name', 'ASC');
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->result();
            }
            
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_forms_by_type: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * *** แก้ไข: ฟังก์ชันบันทึกข้อมูลเบี้ยเลี้ยงดูเด็กแบบใหม่ ***
     */
    public function add_kid_aw_ods($data)
    {
        try {
            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($data['kid_aw_ods_id'])) {
                throw new Exception('kid_aw_ods_id is required');
            }
            
            // ตั้งค่าเวลาปัจจุบัน
            $data['kid_aw_ods_datesave'] = date('Y-m-d H:i:s');
            
            // บันทึกข้อมูล
            $this->db->trans_start();
            $insert_result = $this->db->insert('tbl_kid_aw_ods', $data);
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE || !$insert_result) {
                throw new Exception('Failed to insert kid_aw_ods data');
            }
            
            // บันทึก history
            $this->add_kid_aw_ods_history(
                $data['kid_aw_ods_id'],
                'created',
                'สร้างรายการเบี้ยเลี้ยงดูเด็กใหม่',
                $data['kid_aw_ods_by'] ?? 'System',
                null,
                'submitted'
            );
            
            log_message('info', 'Successfully added kid_aw_ods: ' . $data['kid_aw_ods_id']);
            return $data['kid_aw_ods_id'];
            
        } catch (Exception $e) {
            log_message('error', 'Error in add_kid_aw_ods: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เพิ่ม: บันทึกไฟล์แนบ
     */
    public function add_kid_aw_ods_file($file_data)
    {
        try {
            $this->db->insert('tbl_kid_aw_ods_files', $file_data);
            return $this->db->insert_id();
            
        } catch (Exception $e) {
            log_message('error', 'Error in add_kid_aw_ods_file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เพิ่ม: บันทึกประวัติการดำเนินการ
     */
    public function add_kid_aw_ods_history($kid_aw_ods_id, $action_type, $description, $action_by, $old_status = null, $new_status = null)
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_ods_history')) {
                return false;
            }
            
            $history_data = [
                'kid_aw_ods_history_ref_id' => $kid_aw_ods_id,
                'action_type' => $action_type,
                'action_description' => $description,
                'action_by' => $action_by,
                'action_date' => date('Y-m-d H:i:s'),
                'old_status' => $old_status,
                'new_status' => $new_status
            ];
            
            $this->db->insert('tbl_kid_aw_ods_history', $history_data);
            return $this->db->insert_id();
            
        } catch (Exception $e) {
            log_message('error', 'Error in add_kid_aw_ods_history: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เพิ่ม: ดึงข้อมูลเบี้ยเลี้ยงดูเด็กตาม ID
     */
    public function get_kid_aw_ods_by_id($kid_aw_ods_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->row();
            }
            
            return null;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_by_id: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * เพิ่ม: ดึงข้อมูลเบี้ยเลี้ยงดูเด็กตาม User
     */
    public function get_kid_aw_ods_by_user($user_id, $user_type)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_user_id', $user_id);
            $this->db->where('kid_aw_ods_user_type', $user_type);
            $this->db->order_by('kid_aw_ods_datesave', 'DESC');
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->result();
            }
            
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_by_user: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * เพิ่ม: ดึงข้อมูลเบี้ยเลี้ยงดูเด็กตามเบอร์โทร
     */
    public function get_kid_aw_ods_by_phone_and_user_type($phone, $user_type, $user_id = null)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_phone', $phone);
            $this->db->where('kid_aw_ods_user_type', $user_type);
            
            if ($user_id !== null) {
                $this->db->where('kid_aw_ods_user_id', $user_id);
            }
            
            $this->db->order_by('kid_aw_ods_datesave', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->row();
            }
            
            return null;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_by_phone_and_user_type: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * เพิ่ม: ดึงข้อมูลเบี้ยเลี้ยงดูเด็กตามเลขบัตรประชาชน
     */
    public function get_kid_aw_ods_by_id_card_and_user_type($id_card, $user_type, $user_id = null)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_number', $id_card);
            $this->db->where('kid_aw_ods_user_type', $user_type);
            
            if ($user_id !== null) {
                $this->db->where('kid_aw_ods_user_id', $user_id);
            }
            
            $this->db->order_by('kid_aw_ods_datesave', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->row();
            }
            
            return null;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_by_id_card_and_user_type: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * เพิ่ม: ดึงประวัติการดำเนินการ
     */
    public function get_kid_aw_ods_history($kid_aw_ods_id)
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_ods_history')) {
                return [];
            }
            
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods_history');
            $this->db->where('kid_aw_ods_history_ref_id', $kid_aw_ods_id);
            $this->db->order_by('action_date', 'ASC');
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->result();
            }
            
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_history: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * เพิ่ม: ดึงไฟล์แนบ
     */
    public function get_kid_aw_ods_files($kid_aw_ods_id)
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_ods_files')) {
                return [];
            }
            
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods_files');
            $this->db->where('kid_aw_ods_file_ref_id', $kid_aw_ods_id);
            $this->db->where('kid_aw_ods_file_status', 'active');
            $this->db->order_by('kid_aw_ods_file_uploaded_at', 'ASC');
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                return $query->result();
            }
            
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_files: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ฟังก์ชันสร้าง ID ใหม่สำหรับเบี้ยเลี้ยงดูเด็ก
     */
    public function add_new_id_entry()
    {
        // ดึงปี พ.ศ. ปัจจุบัน (เพิ่ม 543 จาก ค.ศ.)
        $current_year_thai = date('Y') + 543;

        // ตรวจสอบ ID ล่าสุดในตาราง
        $this->db->select('MAX(kid_aw_ods_id) AS max_id');
        $this->db->from('tbl_kid_aw_ods');
        $query = $this->db->get();
        $result = $query->row();

        // กำหนดค่าเริ่มต้นของ ID เช่น K6700001 หรือ K6800001 ตามปี
        $default_id = 'K' . (int)($current_year_thai % 100) . '00001';

        if ($result && $result->max_id) {
            $last_id = $result->max_id;
            $last_year_prefix = (int)substr($last_id, 1, 2); // เอาตัว K ออก
            $current_year_prefix = (int)($current_year_thai % 100);

            if ($last_year_prefix === $current_year_prefix) {
                $last_number = (int)substr($last_id, 1); // เอาตัว K ออกและเอาเลขทั้งหมด
                $new_number = $last_number + 1;
                $new_id = 'K' . $new_number;
            } else {
                $new_id = $default_id;
            }
        } else {
            $new_id = $default_id;
        }

        return $new_id;
    }

    /**
     * ดึงข้อมูลทั้งหมด
     */
    public function list_all()
    {
        $this->db->order_by('kid_aw_ods_id', 'asc');
        $query = $this->db->get('tbl_kid_aw_ods');
        return $query->result();
    }

    /**
     * ดึงข้อมูลสำหรับแก้ไข
     */
    public function read($kid_aw_ods_id)
    {
        $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
        $query = $this->db->get('tbl_kid_aw_ods');
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data;
        }
        return FALSE;
    }

    /**
     * ดึงข้อมูลสำหรับหน้า frontend
     */
    public function kid_aw_ods_frontend()
    {
        $this->db->select('*');
        $this->db->from('tbl_kid_aw_ods');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * เพิ่มจำนวนการดู
     */
    public function increment_view()
    {
        $this->db->where('kid_aw_ods_id', 1);
        $this->db->set('kid_aw_ods_view', 'kid_aw_ods_view + 1', false);
        $this->db->update('tbl_kid_aw_ods');
    }

    /**
     * เพิ่มจำนวนการดาวน์โหลด
     */
    public function increment_download_kid_aw_ods()
    {
        $this->db->where('kid_aw_ods_id', 1);
        $this->db->set('kid_aw_ods_download', 'kid_aw_ods_download + 1', false);
        $this->db->update('tbl_kid_aw_ods');
    }

    /**
     * ส่งข้อความ LINE OA
     */
    private function broadcastLineOAMessage($message, $imagePath = null)
    {
        if (empty($this->channelAccessToken)) {
            log_message('warning', 'LINE token not configured');
            return false;
        }

        $userIds = $this->db->select('line_user_id')
            ->from('tbl_line')
            ->where('line_status', 'show')
            ->get()
            ->result_array();

        $to = array_column($userIds, 'line_user_id');
        if (empty($to)) {
            return false;
        }

        $to = array_filter($to);
        if (empty($to)) {
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
                $success = false;
            }

            curl_close($ch);
        }

        return $success;
    }

    /**
     * อัปโหลดรูปภาพไปยัง LINE
     */
    private function uploadImageToLine($imagePath)
    {
        // สร้าง URL ที่เข้าถึงได้จากภายนอกสำหรับรูปภาพ
        $baseUrl = base_url('docs/img/'); // แก้เป็น URL ของเว็บคุณ
        $fileName = basename($imagePath);
        return $baseUrl . $fileName;
    }

    /**
     * ดึงข้อมูลเบี้ยเลี้ยงดูเด็กพร้อมการกรอง (สำหรับเจ้าหน้าที่)
     */
    public function get_kid_aw_ods_with_filters($filters = [], $limit = 20, $offset = 0)
    {
        try {
            // ตรวจสอบว่าตารางมีอยู่จริง
            if (!$this->db->table_exists('tbl_kid_aw_ods')) {
                log_message('error', 'Table tbl_kid_aw_ods does not exist');
                return ['data' => [], 'total' => 0];
            }

            // Query หลัก
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');

            // Apply filters
            if (!empty($filters['status'])) {
                $this->db->where('kid_aw_ods_status', $filters['status']);
            }

            if (!empty($filters['type'])) {
                $this->db->where('kid_aw_ods_type', $filters['type']);
            }

            if (!empty($filters['priority'])) {
                $this->db->where('kid_aw_ods_priority', $filters['priority']);
            }

            if (!empty($filters['user_type'])) {
                $this->db->where('kid_aw_ods_user_type', $filters['user_type']);
            }

            if (!empty($filters['assigned_to'])) {
                $this->db->where('kid_aw_ods_assigned_to', $filters['assigned_to']);
            }

            // Date range filter
            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(kid_aw_ods_datesave) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(kid_aw_ods_datesave) <=', $filters['date_to']);
            }

            // Search filter
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('kid_aw_ods_id', $search_term);
                $this->db->or_like('kid_aw_ods_by', $search_term);
                $this->db->or_like('kid_aw_ods_phone', $search_term);
                $this->db->or_like('kid_aw_ods_number', $search_term);
                $this->db->or_like('kid_aw_ods_email', $search_term);
                $this->db->group_end();
            }

            // Get total count
            $count_query = $this->db->get_compiled_select();
            $total = $this->db->query("SELECT COUNT(*) as total FROM ({$count_query}) as count_table")->row()->total;

            // Apply pagination - ต้อง reset query และ apply filters อีกครั้ง
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            
            // Apply filters อีกครั้ง
            if (!empty($filters['status'])) {
                $this->db->where('kid_aw_ods_status', $filters['status']);
            }
            if (!empty($filters['type'])) {
                $this->db->where('kid_aw_ods_type', $filters['type']);
            }
            if (!empty($filters['priority'])) {
                $this->db->where('kid_aw_ods_priority', $filters['priority']);
            }
            if (!empty($filters['user_type'])) {
                $this->db->where('kid_aw_ods_user_type', $filters['user_type']);
            }
            if (!empty($filters['assigned_to'])) {
                $this->db->where('kid_aw_ods_assigned_to', $filters['assigned_to']);
            }
            if (!empty($filters['date_from'])) {
                $this->db->where('DATE(kid_aw_ods_datesave) >=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $this->db->where('DATE(kid_aw_ods_datesave) <=', $filters['date_to']);
            }
            if (!empty($filters['search'])) {
                $search_term = $filters['search'];
                $this->db->group_start();
                $this->db->like('kid_aw_ods_id', $search_term);
                $this->db->or_like('kid_aw_ods_by', $search_term);
                $this->db->or_like('kid_aw_ods_phone', $search_term);
                $this->db->or_like('kid_aw_ods_number', $search_term);
                $this->db->or_like('kid_aw_ods_email', $search_term);
                $this->db->group_end();
            }

            $this->db->order_by('kid_aw_ods_datesave', 'DESC');
            $this->db->limit($limit, $offset);
            $query = $this->db->get();

            $data = $query->num_rows() > 0 ? $query->result() : [];

            log_message('debug', "Kid AW ODS query result: {$total} total, " . count($data) . " in current page");

            return [
                'data' => $data,
                'total' => (int)$total
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_with_filters: ' . $e->getMessage());
            log_message('error', 'Filters: ' . json_encode($filters));
            
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * ดึงสถิติเบี้ยเลี้ยงดูเด็ก
     */
    public function get_kid_aw_ods_statistics()
    {
        try {
            $stats = [];

            // สถิติตามสถานะ
            $this->db->select('kid_aw_ods_status as status, COUNT(*) as count');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->group_by('kid_aw_ods_status');
            $status_query = $this->db->get();

            $stats['by_status'] = [];
            if ($status_query->num_rows() > 0) {
                foreach ($status_query->result() as $row) {
                    $stats['by_status'][$row->status] = (int)$row->count;
                }
            }

            // สถิติตามประเภท
            $this->db->select('kid_aw_ods_type as type, COUNT(*) as count');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->group_by('kid_aw_ods_type');
            $type_query = $this->db->get();

            $stats['by_type'] = [];
            if ($type_query->num_rows() > 0) {
                foreach ($type_query->result() as $row) {
                    $stats['by_type'][$row->type] = (int)$row->count;
                }
            }

            // สถิติตามเดือน (6 เดือนล่าสุด)
            $this->db->select('DATE_FORMAT(kid_aw_ods_datesave, "%Y-%m") as month, COUNT(*) as count');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_datesave >=', date('Y-m-d', strtotime('-6 months')));
            $this->db->group_by('DATE_FORMAT(kid_aw_ods_datesave, "%Y-%m")');
            $this->db->order_by('month', 'ASC');
            $monthly_query = $this->db->get();

            $stats['by_month'] = [];
            if ($monthly_query->num_rows() > 0) {
                foreach ($monthly_query->result() as $row) {
                    $stats['by_month'][$row->month] = (int)$row->count;
                }
            }

            // สถิติรวม
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_kid_aw_ods');
            $total_query = $this->db->get();
            $stats['total'] = (int)($total_query->row()->total ?? 0);

            // สถิติวันนี้
            $this->db->select('COUNT(*) as today');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('DATE(kid_aw_ods_datesave) =', date('Y-m-d'));
            $today_query = $this->db->get();
            $stats['today'] = (int)($today_query->row()->today ?? 0);

            // สถิติเดือนนี้
            $this->db->select('COUNT(*) as this_month');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('DATE_FORMAT(kid_aw_ods_datesave, "%Y-%m") =', date('Y-m'));
            $month_query = $this->db->get();
            $stats['this_month'] = (int)($month_query->row()->this_month ?? 0);

            // เพิ่มสถิติเสริม
            $stats['yesterday'] = $this->get_yesterday_count();
            $stats['this_week'] = $this->get_this_week_count();
            $stats['last_month'] = $this->get_last_month_count();

            // ตั้งค่าเริ่มต้นสำหรับสถานะที่ไม่มีข้อมูล
            $default_statuses = ['submitted', 'reviewing', 'approved', 'rejected', 'completed'];
            foreach ($default_statuses as $status) {
                if (!isset($stats['by_status'][$status])) {
                    $stats['by_status'][$status] = 0;
                }
            }

            // ตั้งค่าเริ่มต้นสำหรับประเภทที่ไม่มีข้อมูล
            $default_types = ['children'];
            foreach ($default_types as $type) {
                if (!isset($stats['by_type'][$type])) {
                    $stats['by_type'][$type] = 0;
                }
            }

            log_message('debug', 'Kid AW ODS statistics generated successfully: ' . json_encode([
                'total' => $stats['total'],
                'today' => $stats['today'],
                'this_month' => $stats['this_month']
            ]));

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_statistics: ' . $e->getMessage());
            
            // Return default empty statistics
            return [
                'total' => 0,
                'today' => 0,
                'yesterday' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'last_month' => 0,
                'by_status' => [
                    'submitted' => 0,
                    'reviewing' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'completed' => 0
                ],
                'by_type' => [
                    'children' => 0,
                    'disabled' => 0
                ],
                'by_month' => []
            ];
        }
    }

    /**
     * ดึงจำนวนเมื่อวาน
     */
    private function get_yesterday_count()
    {
        try {
            $this->db->select('COUNT(*) as yesterday');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('DATE(kid_aw_ods_datesave) =', date('Y-m-d', strtotime('-1 day')));
            $query = $this->db->get();
            
            return (int)($query->row()->yesterday ?? 0);
        } catch (Exception $e) {
            log_message('error', 'Error getting yesterday count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงจำนวนสัปดาห์นี้
     */
    private function get_this_week_count()
    {
        try {
            $this->db->select('COUNT(*) as this_week');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('YEARWEEK(kid_aw_ods_datesave, 1) =', date('oW'));
            $query = $this->db->get();
            
            return (int)($query->row()->this_week ?? 0);
        } catch (Exception $e) {
            log_message('error', 'Error getting this week count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงจำนวนเดือนที่แล้ว
     */
    private function get_last_month_count()
    {
        try {
            $this->db->select('COUNT(*) as last_month');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('DATE_FORMAT(kid_aw_ods_datesave, "%Y-%m") =', date('Y-m', strtotime('-1 month')));
            $query = $this->db->get();
            
            return (int)($query->row()->last_month ?? 0);
        } catch (Exception $e) {
            log_message('error', 'Error getting last month count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงรายการเบี้ยเลี้ยงดูเด็กล่าสุด
     */
    public function get_recent_kid_aw_ods($limit = 10)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->order_by('kid_aw_ods_datesave', 'DESC');
            $this->db->limit($limit);
            $query = $this->db->get();

            return $query->num_rows() > 0 ? $query->result() : [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_recent_kid_aw_ods: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * อัปเดตสถานะเบี้ยเลี้ยงดูเด็ก
     */
    public function update_kid_aw_ods_status($kid_aw_ods_id, $new_status, $updated_by, $note = '')
    {
        try {
            // ดึงข้อมูลเดิม
            $this->db->select('kid_aw_ods_status');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $old_data = $this->db->get()->row();

            if (!$old_data) {
                return false;
            }

            $old_status = $old_data->kid_aw_ods_status;

            // อัปเดตสถานะ
            $update_data = [
                'kid_aw_ods_status' => $new_status,
                'kid_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'kid_aw_ods_updated_by' => $updated_by
            ];

            if (!empty($note)) {
                $update_data['kid_aw_ods_notes'] = $note;
            }

            if ($new_status === 'completed') {
                $update_data['kid_aw_ods_completed_at'] = date('Y-m-d H:i:s');
            }

            $this->db->trans_start();
            
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $result = $this->db->update('tbl_kid_aw_ods', $update_data);

            if ($result) {
                // บันทึกประวัติ
                $this->add_kid_aw_ods_history(
                    $kid_aw_ods_id,
                    'status_changed',
                    "เปลี่ยนสถานะจาก {$old_status} เป็น {$new_status}" . (!empty($note) ? " หมายเหตุ: {$note}" : ""),
                    $updated_by,
                    $old_status,
                    $new_status
                );
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return false;
            }

            log_message('info', "Kid AW ODS status updated: {$kid_aw_ods_id} from {$old_status} to {$new_status} by {$updated_by}");
            return true;

        } catch (Exception $e) {
            log_message('error', 'Error in update_kid_aw_ods_status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * มอบหมายงาน
     */
    public function assign_kid_aw_ods($kid_aw_ods_id, $assigned_to, $assigned_by, $note = '')
    {
        try {
            $update_data = [
                'kid_aw_ods_assigned_to' => $assigned_to,
                'kid_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'kid_aw_ods_updated_by' => $assigned_by
            ];

            if (!empty($note)) {
                $update_data['kid_aw_ods_notes'] = $note;
            }

            $this->db->trans_start();
            
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $result = $this->db->update('tbl_kid_aw_ods', $update_data);

            if ($result) {
                // บันทึกประวัติ
                $this->add_kid_aw_ods_history(
                    $kid_aw_ods_id,
                    'assigned',
                    "มอบหมายงานให้เจ้าหน้าที่ ID: {$assigned_to}" . (!empty($note) ? " หมายเหตุ: {$note}" : ""),
                    $assigned_by,
                    null,
                    null
                );
            }

            $this->db->trans_complete();

            return $this->db->trans_status() !== FALSE;

        } catch (Exception $e) {
            log_message('error', 'Error in assign_kid_aw_ods: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบเบี้ยเลี้ยงดูเด็ก
     */
    public function delete_kid_aw_ods($kid_aw_ods_id, $deleted_by)
    {
        try {
            $this->db->trans_start();

            // ลบไฟล์แนบ
            if ($this->db->table_exists('tbl_kid_aw_ods_files')) {
                $this->db->where('kid_aw_ods_file_ref_id', $kid_aw_ods_id);
                $this->db->delete('tbl_kid_aw_ods_files');
            }

            // ลบประวัติ
            if ($this->db->table_exists('tbl_kid_aw_ods_history')) {
                $this->db->where('kid_aw_ods_history_ref_id', $kid_aw_ods_id);
                $this->db->delete('tbl_kid_aw_ods_history');
            }

            // ลบข้อมูลหลัก
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $result = $this->db->delete('tbl_kid_aw_ods');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return false;
            }

            log_message('info', "Kid AW ODS deleted: {$kid_aw_ods_id} by {$deleted_by}");
            return true;

        } catch (Exception $e) {
            log_message('error', 'Error in delete_kid_aw_ods: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลเบี้ยเลี้ยงดูเด็กสำหรับ Staff พร้อมข้อมูลเพิ่มเติม
     */
    public function get_kid_aw_ods_detail_for_staff($kid_aw_ods_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $query = $this->db->get();

            if ($query->num_rows() === 0) {
                return null;
            }

            $kid = $query->row();
            
            // เพิ่มข้อมูลไฟล์
            $kid->files = $this->get_kid_aw_ods_files($kid_aw_ods_id);
            
            // เพิ่มข้อมูลประวัติ
            $kid->history = $this->get_kid_aw_ods_history($kid_aw_ods_id);
            
            // เพิ่มข้อมูลผู้มอบหมาย
            if (!empty($kid->kid_aw_ods_assigned_to)) {
                $this->db->select('m_fname, m_lname');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $kid->kid_aw_ods_assigned_to);
                $assigned_staff = $this->db->get()->row();
                
                if ($assigned_staff) {
                    $kid->assigned_staff_name = $assigned_staff->m_fname . ' ' . $assigned_staff->m_lname;
                }
            }

            return $kid;

        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_detail_for_staff: ' . $e->getMessage());
            return null;
        }
    }
}