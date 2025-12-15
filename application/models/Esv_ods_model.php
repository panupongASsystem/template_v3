<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Esv_ods_model extends CI_Model
{
    protected $table_name = 'tbl_esv_ods';
    protected $primary_key = 'esv_ods_id';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // โหลด LINE Notification Library
        $this->load->library('line_notification');
    }

    // ===================================================================
    // *** CREATE Operations ***
    // ===================================================================

    /**
     * เพิ่มเอกสารใหม่ (แก้ไขการโหลด Library)
     */
    public function add_document($data)
    {
        $insert_id = false;

        try {
            log_message('info', 'ESV ODS add_document started');
            $this->db->insert('tbl_esv_ods', $data);

            if ($this->db->affected_rows() > 0) {
                $insert_id = $this->db->insert_id();
                log_message('info', 'ESV ODS document inserted with ID: ' . $insert_id);

                // *** แก้ไขการเรียกใช้ Line Notification ***
                log_message('info', '=== LINE NOTIFICATION DEBUG START ===');

                // วิธีที่ 1: ใช้ get_instance() เพื่อเข้าถึง CI instance
                $CI =& get_instance();

                // ตรวจสอบใน CI instance
                if (property_exists($CI, 'line_notification') && is_object($CI->line_notification)) {
                    log_message('info', 'Found line_notification in CI instance');

                    if (method_exists($CI->line_notification, 'send_line_esv_ods_notification')) {
                        log_message('info', 'Calling send_line_esv_ods_notification via CI instance');
                        $CI->line_notification->send_line_esv_ods_notification($insert_id);
                        log_message('info', 'Line notification sent successfully via CI instance');
                    } else {
                        log_message('warning', 'Method send_line_esv_ods_notification not found in CI instance');
                    }
                } else {
                    log_message('info', 'line_notification not found in CI instance - loading library');

                    // โหลด library ใน CI instance
                    $CI->load->library('line_notification');

                    if (property_exists($CI, 'line_notification') && is_object($CI->line_notification)) {
                        log_message('info', 'line_notification loaded successfully in CI instance');

                        if (method_exists($CI->line_notification, 'send_line_esv_ods_notification')) {
                            $CI->line_notification->send_line_esv_ods_notification($insert_id);
                            log_message('info', 'Line notification sent after loading in CI instance');
                        } else {
                            log_message('warning', 'Method not found after loading in CI instance');
                        }
                    } else {
                        log_message('error', 'Failed to load line_notification in CI instance');

                        // วิธีที่ 2: สร้าง instance ใหม่โดยตรง
                        log_message('info', 'Trying to create Line_notification instance manually');

                        if (class_exists('Line_notification')) {
                            log_message('info', 'Line_notification class exists - creating instance');

                            try {
                                $line_notification = new Line_notification();

                                if (method_exists($line_notification, 'send_line_esv_ods_notification')) {
                                    log_message('info', 'Calling send_line_esv_ods_notification via manual instance');
                                    $line_notification->send_line_esv_ods_notification($insert_id);
                                    log_message('info', 'Line notification sent successfully via manual instance');
                                } else {
                                    log_message('warning', 'Method not found in manual instance');

                                    // ลองใช้ method อื่น
                                    if (method_exists($line_notification, 'broadcastLineOAMessage')) {
                                        $manual_message = "📄 เอกสาร ESV ODS ใหม่\nID: " . $insert_id . "\nรหัส: " . ($data['esv_ods_reference_id'] ?? 'N/A') . "\nหัวข้อ: " . ($data['esv_ods_topic'] ?? 'N/A');
                                        $line_notification->broadcastLineOAMessage($manual_message);
                                        log_message('info', 'Line notification sent via broadcastLineOAMessage');
                                    }
                                }
                            } catch (Exception $manual_e) {
                                log_message('error', 'Error creating manual Line_notification instance: ' . $manual_e->getMessage());
                            }
                        } else {
                            log_message('error', 'Line_notification class does not exist');

                            // วิธีที่ 3: ใช้ helper function ถ้ามี
                            if (function_exists('send_line_message')) {
                                $helper_message = "📄 เอกสาร ESV ODS ใหม่\nID: " . $insert_id;
                                send_line_message($helper_message);
                                log_message('info', 'Line notification sent via helper function');
                            } else {
                                log_message('info', 'No alternative line notification method available');
                            }
                        }
                    }
                }

                log_message('info', '=== LINE NOTIFICATION DEBUG END ===');
                log_message('info', 'ESV ODS add_document completed successfully');
                return $insert_id;

            } else {
                log_message('error', 'ESV ODS document insert failed - no affected rows');
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'Error adding ESV ODS document: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * เพิ่มไฟล์ใน tbl_esv_files
     */
    public function add_file($data)
    {
        try {
            $file_data = [
                'esv_file_esv_ods_id' => $data['document_id'],
                'esv_file_name' => $data['file_name'],
                'esv_file_original_name' => $data['original_name'],
                'esv_file_path' => $data['file_path'],
                'esv_file_size' => $data['file_size'],
                'esv_file_type' => $data['file_type'],
                'esv_file_extension' => $data['file_extension'],
                'esv_file_description' => $data['description'] ?? null,
                'esv_file_is_main' => $data['is_main'] ?? 0,
                'esv_file_order' => $data['order'] ?? 1,
                'esv_file_status' => 'active',
                'esv_file_uploaded_by' => $data['uploaded_by'] ?? 'system',
                'esv_file_uploaded_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->db->insert('tbl_esv_files', $file_data);

            if ($result) {
                return $this->db->insert_id();
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error adding file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เพิ่มหลายไฟล์พร้อมกัน
     */
    public function add_multiple_files($document_id, $files_data)
    {
        try {
            $this->db->trans_start();

            $success_count = 0;
            $file_ids = [];

            foreach ($files_data as $file_data) {
                $file_data['document_id'] = $document_id;
                $file_id = $this->add_file($file_data);

                if ($file_id) {
                    $success_count++;
                    $file_ids[] = $file_id;
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return false;
            }

            return [
                'success_count' => $success_count,
                'total_files' => count($files_data),
                'file_ids' => $file_ids
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error adding multiple files: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เพิ่มหมวดหมู่ใหม่
     */
    public function add_category($data)
    {
        try {
            $insert_data = [
                'esv_category_name' => $data['name'],
                'esv_category_group' => $data['group'] ?? 'ทั่วไป',
                'esv_category_description' => $data['description'] ?? null,
                'esv_category_department_id' => $data['department_id'] ?? null,
                'esv_category_fee' => $data['fee'] ?? 0.00,
                'esv_category_process_days' => $data['process_days'] ?? null,
                'esv_category_order' => $data['order'] ?? 0,
                'esv_category_status' => 'active',
                'esv_category_created_by' => $data['created_by'],
                'esv_category_created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->insert('tbl_esv_category', $insert_data)) {
                return $this->db->insert_id();
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error adding category: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================================================
    // *** READ Operations ***
    // ===================================================================

    /**
     * ดึงข้อมูลหมวดหมู่ทั้งหมด
     */
    public function get_all_categories()
    {
        try {
            $this->db->select('c.esv_category_id, c.esv_category_name, c.esv_category_description, 
                              c.esv_category_department_id, c.esv_category_fee, c.esv_category_process_days, 
                              c.esv_category_group, p.pname as department_name');
            $this->db->from('tbl_esv_category c');
            $this->db->join('tbl_position p', 'c.esv_category_department_id = p.pid', 'left');
            $this->db->where('c.esv_category_status', 'active');
            $this->db->order_by('c.esv_category_group', 'ASC');
            $this->db->order_by('c.esv_category_order', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error in get_all_categories: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงเอกสารตาม ID
     */
    public function get_document_by_id($document_id)
    {
        try {
            $this->db->select('d.*, dt.esv_type_name, dc.esv_category_name, dp.pname as department_name');
            $this->db->from($this->table_name . ' d');
            $this->db->join('tbl_esv_type dt', 'd.esv_ods_type_id = dt.esv_type_id', 'left');
            $this->db->join('tbl_esv_category dc', 'd.esv_ods_category_id = dc.esv_category_id', 'left');
            $this->db->join('tbl_position dp', 'd.esv_ods_department_id = dp.pid', 'left');
            $this->db->where('d.esv_ods_id', $document_id);

            $query = $this->db->get();
            $document = $query->row();

            if ($document) {
                // ดึงไฟล์แนบ
                $document->files = $this->get_document_files($document->esv_ods_id);

                // ดึงประวัติ
                $document->history = $this->get_document_history($document->esv_ods_id);
            }

            return $document;

        } catch (Exception $e) {
            log_message('error', 'Error getting document by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ดึงข้อมูลเอกสารพร้อมตัวกรอง (สำหรับหน้าจัดการ)
     */
    public function get_documents_with_filters($filters = [], $limit = 20, $offset = 0)
    {
        try {
            // Query สำหรับนับจำนวนรวม
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_esv_ods e');
            $this->apply_filters_to_query($filters);
            $total_query = $this->db->get();
            $total = $total_query->row()->total;

            // Query สำหรับดึงข้อมูล
            $this->db->select('e.*, p.pname as department_name, c.esv_category_name, t.esv_type_name');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');

            if ($this->db->table_exists('tbl_esv_category')) {
                $this->db->join('tbl_esv_category c', 'e.esv_ods_category_id = c.esv_category_id', 'left');
            }

            if ($this->db->table_exists('tbl_esv_type')) {
                $this->db->join('tbl_esv_type t', 'e.esv_ods_type_id = t.esv_type_id', 'left');
            }

            $this->apply_filters_to_query($filters);
            $this->db->order_by('e.esv_ods_datesave', 'DESC');
            $this->db->limit($limit, $offset);

            $query = $this->db->get();

            return [
                'data' => $query->result(),
                'total' => $total
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting documents with filters: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }



    private function apply_filters_to_query($filters)
    {
        if (!empty($filters['status'])) {
            $this->db->where('e.esv_ods_status', $filters['status']);
        }

        if (!empty($filters['user_type'])) {
            $this->db->where('e.esv_ods_user_type', $filters['user_type']);
        }

        if (!empty($filters['department'])) {
            $this->db->where('e.esv_ods_department_id', $filters['department']);
        }

        if (!empty($filters['category'])) {
            $this->db->where('e.esv_ods_category_id', $filters['category']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(e.esv_ods_datesave) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(e.esv_ods_datesave) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('e.esv_ods_topic', $filters['search']);
            $this->db->or_like('e.esv_ods_detail', $filters['search']);
            $this->db->or_like('e.esv_ods_reference_id', $filters['search']);
            $this->db->or_like('e.esv_ods_by', $filters['search']);
            $this->db->group_end();
        }
    }



    /**
     * ใช้ตัวกรองกับ query
     */
    private function apply_filters($filters)
    {
        if (!empty($filters['status'])) {
            $this->db->where('e.esv_ods_status', $filters['status']);
        }

        if (!empty($filters['department'])) {
            $this->db->where('e.esv_ods_department_id', $filters['department']);
        }

        if (!empty($filters['category'])) {
            $this->db->where('e.esv_ods_category_id', $filters['category']);
        }

        if (!empty($filters['user_type'])) {
            $this->db->where('e.esv_ods_user_type', $filters['user_type']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(e.esv_ods_datesave) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(e.esv_ods_datesave) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $this->db->escape_like_str($filters['search']);
            $this->db->group_start();
            $this->db->like('e.esv_ods_reference_id', $search);
            $this->db->or_like('e.esv_ods_topic', $search);
            $this->db->or_like('e.esv_ods_detail', $search);
            $this->db->or_like('e.esv_ods_by', $search);
            $this->db->or_like('e.esv_ods_phone', $search);
            $this->db->or_like('e.esv_ods_email', $search);
            $this->db->group_end();
        }
    }

    /**
     * ดึงรายละเอียดเอกสารสำหรับเจ้าหน้าที่
     */
    public function get_document_detail_for_staff($reference_id)
    {
        try {
            $this->db->select('e.*, p.pname as department_name, c.esv_category_name, t.esv_type_name');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');

            // ตรวจสอบตารางก่อนทำ join
            if ($this->db->table_exists('tbl_esv_category')) {
                $this->db->join('tbl_esv_category c', 'e.esv_ods_category_id = c.esv_category_id', 'left');
            }

            if ($this->db->table_exists('tbl_esv_type')) {
                $this->db->join('tbl_esv_type t', 'e.esv_ods_type_id = t.esv_type_id', 'left');
            }

            $this->db->where('e.esv_ods_reference_id', $reference_id);

            $query = $this->db->get();
            $document = $query->row();

            if ($document) {
                // ดึงไฟล์และประวัติ
                $document->files = $this->get_document_files($document->esv_ods_id);
                $document->history = $this->get_document_history($document->esv_ods_id);
                $document->file_count = count($document->files);
                $document->main_file = $this->get_main_file($document->esv_ods_id);
            }

            return $document;

        } catch (Exception $e) {
            log_message('error', 'Error getting document detail for staff: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * ค้นหาเอกสารตาม reference_id (สำหรับ tracking)
     */
    public function get_document_by_reference($reference_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_reference_id', $reference_id);
            $query = $this->db->get();

            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Error getting document by reference: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * ดึงเอกสารตาม user
     */
    public function get_documents_by_user($user_id, $user_type = 'public')
    {
        try {
            $this->db->select('e.*, p.pname as department_name, c.esv_category_name, t.esv_type_name');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');

            // ตรวจสอบตารางก่อนทำ join
            if ($this->db->table_exists('tbl_esv_category')) {
                $this->db->join('tbl_esv_category c', 'e.esv_ods_category_id = c.esv_category_id', 'left');
            }

            if ($this->db->table_exists('tbl_esv_type')) {
                $this->db->join('tbl_esv_type t', 'e.esv_ods_type_id = t.esv_type_id', 'left');
            }

            $this->db->where('e.esv_ods_user_id', $user_id);
            $this->db->where('e.esv_ods_user_type', $user_type);
            $this->db->order_by('e.esv_ods_datesave', 'DESC');

            $query = $this->db->get();

            // ตรวจสอบ error
            if ($this->db->error()['code'] !== 0) {
                $error = $this->db->error();
                log_message('error', 'Database error in get_documents_by_user: ' . $error['message']);
                return [];
            }

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting documents by user: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลหมวดหมู่ตามแผนก
     */
    public function get_categories_by_department($department_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_esv_category');
            $this->db->where('esv_category_department_id', $department_id);
            $this->db->where('esv_category_status', 'active');
            $this->db->order_by('esv_category_group', 'ASC');
            $this->db->order_by('esv_category_order', 'ASC');

            $query = $this->db->get();
            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error in get_categories_by_department: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงข้อมูลหมวดหมู่
     */
    public function get_category_info($category_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_esv_category');
            $this->db->where('esv_category_id', $category_id);
            $this->db->where('esv_category_status', 'active');

            $query = $this->db->get();
            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Error in get_category_info: ' . $e->getMessage());
            return null;
        }
    }

    // ===================================================================
    // *** File Operations ***
    // ===================================================================

    /**
     * ดึงไฟล์แนบเอกสาร
     */
    public function get_document_files($document_id)
    {
        try {
            // ตรวจสอบว่าตาราง tbl_esv_files มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_esv_files')) {
                log_message('debug', 'Table tbl_esv_files does not exist');
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_esv_files');
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->where('esv_file_status', 'active');
            $this->db->order_by('esv_file_order', 'ASC');
            $this->db->order_by('esv_file_uploaded_at', 'ASC');

            $query = $this->db->get();

            // ตรวจสอบ error แบบ CodeIgniter 3
            if ($this->db->error()['code'] !== 0) {
                $error = $this->db->error();
                log_message('error', 'Database error in get_document_files: ' . $error['message']);
                return [];
            }

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting document files: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงไฟล์หลัก (main file)
     */
    public function get_main_file($document_id)
    {
        try {
            // ตรวจสอบว่าตาราง tbl_esv_files มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_esv_files')) {
                return null;
            }

            $this->db->select('*');
            $this->db->from('tbl_esv_files');
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->where('esv_file_status', 'active');
            $this->db->where('esv_file_is_main', 1);
            $this->db->limit(1);

            $query = $this->db->get();

            // ตรวจสอบ error แบบ CodeIgniter 3
            if ($this->db->error()['code'] !== 0) {
                $error = $this->db->error();
                log_message('error', 'Database error in get_main_file: ' . $error['message']);
                return null;
            }

            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Error getting main file: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * ดึงข้อมูลไฟล์ตาม ID
     */
    public function get_file_by_id($file_id)
    {
        try {
            $this->db->select('f.*, d.esv_ods_reference_id, d.esv_ods_by, d.esv_ods_status');
            $this->db->from('tbl_esv_files f');
            $this->db->join('tbl_esv_ods d', 'f.esv_file_esv_ods_id = d.esv_ods_id');
            $this->db->where('f.esv_file_id', $file_id);
            $this->db->where('f.esv_file_status', 'active');

            $query = $this->db->get();
            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Error in get_file_by_id: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * นับขนาดไฟล์รวม
     */
    public function get_total_file_size($document_id)
    {
        try {
            if (empty($document_id)) {
                return 0;
            }

            $this->db->select_sum('esv_file_size');
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->where('esv_file_status', 'active');
            $query = $this->db->get('tbl_esv_files');

            $result = $query->row();
            return $result ? (int) $result->esv_file_size : 0;

        } catch (Exception $e) {
            log_message('error', 'Error in get_total_file_size: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * อัปเดตจำนวนการดาวน์โหลด
     */
    public function increment_download_count($file_id)
    {
        try {
            $this->db->where('esv_file_id', $file_id);
            $this->db->set('esv_file_download_count', 'esv_file_download_count + 1', FALSE);
            return $this->db->update('tbl_esv_files');

        } catch (Exception $e) {
            log_message('error', 'Error in increment_download_count: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบไฟล์ (soft delete)
     */
    public function delete_file($file_id, $deleted_by = 'system')
    {
        try {
            $update_data = [
                'esv_file_status' => 'deleted',
                'esv_file_deleted_by' => $deleted_by,
                'esv_file_deleted_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('esv_file_id', $file_id);
            return $this->db->update('tbl_esv_files', $update_data);

        } catch (Exception $e) {
            log_message('error', 'Error in delete_file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * เปลี่ยนไฟล์หลัก
     */
    public function set_main_file($document_id, $file_id)
    {
        try {
            $this->db->trans_start();

            // เคลียร์ main file เก่า
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->update('tbl_esv_files', ['esv_file_is_main' => 0]);

            // ตั้งไฟล์ใหม่เป็น main
            $this->db->where('esv_file_id', $file_id);
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->update('tbl_esv_files', ['esv_file_is_main' => 1]);

            $this->db->trans_complete();

            return $this->db->trans_status() !== FALSE;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in set_main_file: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================================================
    // *** UPDATE Operations ***
    // ===================================================================

    /**
     * อัปเดตข้อมูลเอกสาร
     */
    public function update_document($document_id, $data, $updated_by)
    {
        try {
            $update_data = $data;
            $update_data['esv_ods_updated_at'] = date('Y-m-d H:i:s');
            $update_data['esv_ods_updated_by'] = $updated_by;

            // ลบ esv_ods_file ออกถ้ามี (ไม่ใช้แล้ว)
            unset($update_data['esv_ods_file']);

            $this->db->where($this->primary_key, $document_id);
            $result = $this->db->update($this->table_name, $update_data);

            if ($result) {
                // เพิ่มประวัติ
                $this->add_document_history(
                    $document_id,
                    'updated',
                    'อัปเดตข้อมูลเอกสาร',
                    $updated_by
                );

                log_message('info', "Document updated: ID={$document_id}");
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error updating document: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตสถานะเอกสาร
     */
    public function update_document_status($reference_id, $new_status, $updated_by, $note = '')
    {
        try {
            $update_data = [
                'esv_ods_status' => $new_status,
                'esv_ods_updated_at' => date('Y-m-d H:i:s'),
                'esv_ods_updated_by' => $updated_by
            ];

            if (!empty($note)) {
                $update_data['esv_ods_response'] = $note;
                $update_data['esv_ods_response_by'] = $updated_by;
                $update_data['esv_ods_response_date'] = date('Y-m-d H:i:s');
            }

            $this->db->where('esv_ods_reference_id', $reference_id);
            $result = $this->db->update('tbl_esv_ods', $update_data);

            if ($result && $this->db->affected_rows() > 0) {
                // บันทึกประวัติ
                $this->add_document_history_safe($reference_id, $new_status, $updated_by, $note);
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error updating document status: ' . $e->getMessage());
            return false;
        }
    }




    private function add_document_history_safe($reference_id, $action, $by, $note = '')
    {
        try {
            // ตรวจสอบว่าตาราง tbl_esv_history มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_esv_history')) {
                return false;
            }

            // ดึง document_id จาก reference_id
            $this->db->select('esv_ods_id');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_reference_id', $reference_id);
            $document = $this->db->get()->row();

            if (!$document) {
                return false;
            }

            $history_data = [
                'esv_history_esv_ods_id' => $document->esv_ods_id,
                'esv_history_action' => 'status_updated',
                'esv_history_new_status' => $action,
                'esv_history_description' => 'อัปเดตสถานะเป็น: ' . $action . (!empty($note) ? ' - ' . $note : ''),
                'esv_history_by' => $by,
                'esv_history_created_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->insert('tbl_esv_history', $history_data);

        } catch (Exception $e) {
            log_message('error', 'Error adding document history: ' . $e->getMessage());
            return false;
        }
    }




    /**
     * ลบเอกสาร (soft delete)
     */
    public function delete_document($reference_id, $deleted_by)
    {
        try {
            $this->db->trans_start();

            $update_data = [
                'esv_ods_status' => 'cancelled',
                'esv_ods_updated_at' => date('Y-m-d H:i:s'),
                'esv_ods_updated_by' => $deleted_by
            ];

            $this->db->where('esv_ods_reference_id', $reference_id);
            $result = $this->db->update('tbl_esv_ods', $update_data);

            if ($result) {
                // บันทึกประวัติ
                $this->add_document_history(
                    $this->get_document_id_by_reference($reference_id),
                    'cancelled',
                    "ยกเลิกเอกสารโดย: {$deleted_by}",
                    $deleted_by,
                    null,
                    'cancelled'
                );
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return false;
            }

            return $result;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in delete_document: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================================================
    // *** History Operations ***
    // ===================================================================

    /**
     * บันทึกประวัติการดำเนินการ
     */
    public function add_document_history($document_id, $action, $description, $action_by, $old_value = null, $new_value = null)
    {
        try {
            if (!$this->db->table_exists('tbl_esv_history')) {
                return false;
            }

            $history_data = [
                'esv_history_esv_ods_id' => $document_id,
                'esv_history_action' => $action,
                'esv_history_description' => $description,
                'esv_history_by' => $action_by,
                'esv_history_old_status' => $old_value,
                'esv_history_new_status' => $new_value,
                'esv_history_created_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->insert('tbl_esv_history', $history_data);

        } catch (Exception $e) {
            log_message('error', 'Error in add_document_history: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงประวัติเอกสาร
     */
    public function get_document_history($document_id)
    {
        try {
            // ตรวจสอบว่าตาราง tbl_esv_history มีอยู่หรือไม่
            if (!$this->db->table_exists('tbl_esv_history')) {
                log_message('debug', 'Table tbl_esv_history does not exist');
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_esv_history');
            $this->db->where('esv_history_esv_ods_id', $document_id);
            $this->db->order_by('esv_history_created_at', 'DESC');

            $query = $this->db->get();

            // ตรวจสอบ error แบบ CodeIgniter 3
            if ($this->db->error()['code'] !== 0) {
                $error = $this->db->error();
                log_message('error', 'Database error in get_document_history: ' . $error['message']);
                return [];
            }

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting document history: ' . $e->getMessage());
            return [];
        }
    }

    // ===================================================================
    // *** Statistics & Utility Functions ***
    // ===================================================================

    /**
     * ดึงสถิติเอกสาร
     */
    public function get_document_statistics()
    {
        try {
            $stats = [
                'total' => 0,
                'pending' => 0,
                'processing' => 0,
                'completed' => 0,
                'rejected' => 0,
                'cancelled' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0
            ];

            // นับรวม
            $this->db->select('COUNT(*) as total');
            $this->db->from('tbl_esv_ods');
            $total_query = $this->db->get();
            $stats['total'] = $total_query->row()->total;

            // นับตามสถานะ
            $statuses = ['pending', 'processing', 'completed', 'rejected', 'cancelled'];
            foreach ($statuses as $status) {
                $this->db->select('COUNT(*) as count');
                $this->db->from('tbl_esv_ods');
                $this->db->where('esv_ods_status', $status);
                $query = $this->db->get();
                $stats[$status] = $query->row()->count;
            }

            // นับตามช่วงเวลา
            $this->db->select('COUNT(*) as count');
            $this->db->from('tbl_esv_ods');
            $this->db->where('DATE(esv_ods_datesave)', date('Y-m-d'));
            $today_query = $this->db->get();
            $stats['today'] = $today_query->row()->count;

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Error getting document statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงเอกสารล่าสุด
     */
    public function get_recent_documents($limit = 10)
    {
        try {
            $this->db->select('e.esv_ods_reference_id, e.esv_ods_topic, e.esv_ods_by, 
                              e.esv_ods_status, e.esv_ods_datesave, p.pname as department_name');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');
            $this->db->where('e.esv_ods_status !=', 'cancelled');
            $this->db->order_by('e.esv_ods_datesave', 'DESC');
            $this->db->limit($limit);

            $query = $this->db->get();
            $documents = $query->result();

            // เพิ่มข้อมูลไฟล์
            foreach ($documents as $document) {
                $document_id = $this->get_document_id_by_reference($document->esv_ods_reference_id);
                if ($document_id) {
                    $document->file_count = $this->count_document_files($document_id);
                }
            }

            return $documents;

        } catch (Exception $e) {
            log_message('error', 'Error in get_recent_documents: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * นับจำนวนไฟล์ของเอกสาร
     */
    public function count_document_files($document_id)
    {
        try {
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->where('esv_file_status', 'active');
            return $this->db->count_all_results('tbl_esv_files');

        } catch (Exception $e) {
            log_message('error', 'Error in count_document_files: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึง ID เอกสารจาก reference_id
     */
    private function get_document_id_by_reference($reference_id)
    {
        try {
            $this->db->select('esv_ods_id');
            $this->db->where('esv_ods_reference_id', $reference_id);
            $result = $this->db->get('tbl_esv_ods')->row();

            return $result ? $result->esv_ods_id : null;

        } catch (Exception $e) {
            log_message('error', 'Error in get_document_id_by_reference: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ตรวจสอบโครงสร้างตาราง
     */
    public function check_table_structure()
    {
        try {
            if (!$this->db->table_exists('tbl_esv_ods')) {
                log_message('error', 'Table tbl_esv_ods does not exist');
                return false;
            }

            $fields = $this->db->list_fields('tbl_esv_ods');
            log_message('debug', 'ESV Model - Table fields: ' . json_encode($fields));

            $required_fields = [
                'esv_ods_id',
                'esv_ods_reference_id',
                'esv_ods_topic',
                'esv_ods_by',
                'esv_ods_status',
                'esv_ods_datesave'
            ];

            foreach ($required_fields as $field) {
                if (!in_array($field, $fields)) {
                    log_message('error', "Required field {$field} not found in tbl_esv_ods");
                    return false;
                }
            }

            // ตรวจสอบตาราง tbl_esv_files
            if (!$this->db->table_exists('tbl_esv_files')) {
                log_message('warning', 'Table tbl_esv_files does not exist');
            } else {
                log_message('info', 'Table tbl_esv_files exists');
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Error checking table structure: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงตัวเลือกสถานะทั้งหมด
     */
    public function get_all_status_options()
    {
        return [
            ['value' => 'pending', 'label' => 'รอดำเนินการ'],
            ['value' => 'processing', 'label' => 'กำลังดำเนินการ'],
            ['value' => 'completed', 'label' => 'เสร็จสิ้น'],
            ['value' => 'rejected', 'label' => 'ไม่อนุมัติ'],
            ['value' => 'cancelled', 'label' => 'ยกเลิก']
        ];
    }

    /**
     * ดึงตัวเลือกความสำคัญทั้งหมด
     */
    public function get_all_priority_options()
    {
        return [
            ['value' => 'normal', 'label' => 'ปกติ'],
            ['value' => 'urgent', 'label' => 'เร่งด่วน'],
            ['value' => 'very_urgent', 'label' => 'เร่งด่วนมาก']
        ];
    }

    /**
     * สำหรับ Dashboard Summary
     */
    public function get_dashboard_summary()
    {
        try {
            $summary = [
                'total' => 0,
                'pending' => 0,
                'processing' => 0,
                'completed' => 0,
                'total_files' => 0
            ];

            // นับจำนวนรวม
            $this->db->from($this->table_name);
            $this->db->where('esv_ods_status !=', 'cancelled');
            $summary['total'] = $this->db->count_all_results();

            // นับตามสถานะ
            $this->db->select('esv_ods_status, COUNT(*) as count');
            $this->db->from($this->table_name);
            $this->db->where('esv_ods_status !=', 'cancelled');
            $this->db->group_by('esv_ods_status');
            $query = $this->db->get();

            foreach ($query->result() as $row) {
                switch ($row->esv_ods_status) {
                    case 'pending':
                        $summary['pending'] = (int) $row->count;
                        break;
                    case 'processing':
                        $summary['processing'] = (int) $row->count;
                        break;
                    case 'completed':
                        $summary['completed'] = (int) $row->count;
                        break;
                }
            }

            // นับไฟล์ทั้งหมด
            if ($this->db->table_exists('tbl_esv_files')) {
                $this->db->where('esv_file_status', 'active');
                $summary['total_files'] = $this->db->count_all_results('tbl_esv_files');
            }

            return $summary;

        } catch (Exception $e) {
            log_message('error', 'Error getting dashboard summary: ' . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'processing' => 0,
                'completed' => 0,
                'total_files' => 0
            ];
        }
    }
}