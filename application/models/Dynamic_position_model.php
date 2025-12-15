<?php

/**
 * Dynamic Position Model - ระบบจัดการตำแหน่งแบบยืดหยุ่น
 * รองรับการสร้าง 61 ตำแหน่งอัตโนมัติสำหรับทุกประเภท
 */
class Dynamic_position_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('space_model');
    }

    // ==================== Position Types Management ====================

    /**
     * ดึงข้อมูลประเภทตำแหน่งทั้งหมด
     */
    public function get_position_types($status = 'show')
    {
        // เพิ่มเงื่อนไข peng ไม่เป็นค่าว่าง
        $this->db->where('peng !=', '');
        $this->db->where('peng IS NOT NULL');

        // ถ้า $status เป็น null หรือ false ให้ดึงทั้งหมด
        if ($status !== null && $status !== false) {
            $this->db->where('pstatus', $status);
        }

        $this->db->order_by('porder', 'ASC');
        $this->db->order_by('pname', 'ASC');
        return $this->db->get('tbl_position')->result();
    }

    // เพิ่มเมธอดใหม่สำหรับดึงเฉพาะที่แสดง
    public function get_active_position_types()
    {
        return $this->get_position_types('show');
    }

    // เพิ่มเมธอดใหม่สำหรับดึงเฉพาะที่ซ่อน
    public function get_hidden_position_types()
    {
        return $this->get_position_types('hide');
    }

    // เพิ่มเมธอดสำหรับนับจำนวนแต่ละสถานะ
    public function count_position_types_by_status()
    {
        $this->db->select('pstatus, COUNT(*) as count');
        $this->db->group_by('pstatus');
        $results = $this->db->get('tbl_position')->result();

        $counts = [
            'show' => 0,
            'hide' => 0,
            'total' => 0
        ];

        foreach ($results as $result) {
            $counts[$result->pstatus] = (int)$result->count;
            $counts['total'] += (int)$result->count;
        }

        return $counts;
    }

    /**
     * ดึงข้อมูลประเภทตำแหน่งตาม ID
     */
    public function get_position_type($type_id)
    {
        return $this->db->get_where('tbl_position', ['type_id' => $type_id])->row();
    }

    /**
     * ดึงข้อมูลประเภทตำแหน่งตามชื่อ
     */
    public function get_position_type_by_name($peng)
    {
        return $this->db->get_where('tbl_position', ['peng' => $peng])->row();
    }

    /**
     * สร้างประเภทตำแหน่งใหม่ พร้อม 61 ตำแหน่งว่าง
     */
    public function create_position_type($type_data, $fields_data)
    {
        $this->db->trans_start();

        try {
            // สร้างประเภทตำแหน่ง
            $this->db->insert('tbl_position', $type_data);
            $type_id = $this->db->insert_id();

            // สร้างฟิลด์
            foreach ($fields_data as $field) {
                $field['type_id'] = $type_id;
                $this->db->insert('tbl_position_fields', $field);
            }

            // สร้าง 61 ตำแหน่งว่างอัตโนมัติ
            $this->create_61_empty_positions($type_id);

            $this->db->trans_complete();
            return $this->db->trans_status() ? $type_id : false;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Create position type failed: ' . $e->getMessage());
            return false;
        }
    }

    // ==================== Position Fields Management ====================

    /**
     * ดึงฟิลด์ทั้งหมดของประเภทตำแหน่ง
     */
    public function get_position_fields($type_id, $status = 'active')
    {
        $this->db->where('type_id', $type_id);
        if ($status) {
            $this->db->where('field_status', $status);
        }
        $this->db->order_by('field_order', 'ASC');
        return $this->db->get('tbl_position_fields')->result();
    }

    /**
     * ดึงฟิลด์เฉพาะที่เป็นไฟล์
     */
    public function get_file_fields($type_id)
    {
        $this->db->where('type_id', $type_id);
        $this->db->where('field_type', 'file');
        $this->db->where('field_status', 'active');
        return $this->db->get('tbl_position_fields')->result();
    }

    // ==================== 61 Positions Management ====================

    /**
     * สร้าง 61 ตำแหน่งว่างสำหรับประเภทที่กำหนด
     */
    public function create_61_empty_positions($type_id)
    {
        // ดึงฟิลด์ของประเภทนี้เพื่อสร้างข้อมูลว่าง
        $fields = $this->get_position_fields($type_id);
        $empty_data = [];

        foreach ($fields as $field) {
            $empty_data[$field->field_name] = '';
        }

        // สร้าง 61 ตำแหน่ง
        for ($i = 1; $i <= 61; $i++) {
            // คำนวณ row และ column
            if ($i == 1) {
                // ตำแหน่งหลัก
                $row = 1;
                $column = 0;
            } else {
                // ตำแหน่งรอง - จัดเรียงแถวละ 3 คอลัมน์
                $adjusted_position = $i - 2;
                $row = floor($adjusted_position / 3) + 2;
                $column = $adjusted_position % 3;
            }

            $position_data = [
                'type_id' => $type_id,
                'position_data' => json_encode($empty_data),
                'position_row' => $row,
                'position_column' => $column,
                'position_order' => $i,
                'position_status' => 'active',
                'created_by' => $this->session->userdata('m_fname') ?: 'system'
            ];

            $this->db->insert('tbl_dynamic_positions', $position_data);
        }

        log_message('info', "Created 61 empty positions for type_id: {$type_id}");
        return true;
    }

    /**
     * ตรวจสอบและสร้างตำแหน่งให้ครบ 61 ตำแหน่ง
     */
    public function ensure_61_positions($type_id)
    {
        // ดึงตำแหน่งที่มีอยู่
        $this->db->select('position_order');
        $this->db->where('type_id', $type_id);
        $existing_orders = $this->db->get('tbl_dynamic_positions')->result_array();
        $existing_order_list = array_column($existing_orders, 'position_order');

        // ถ้ามีครบ 61 แล้ว ไม่ต้องทำอะไร
        if (count($existing_order_list) >= 61) {
            return true;
        }

        // ดึงฟิลด์เพื่อสร้างข้อมูลว่าง
        $fields = $this->get_position_fields($type_id);
        $empty_data = [];
        foreach ($fields as $field) {
            $empty_data[$field->field_name] = '';
        }

        // สร้างตำแหน่งที่ขาดหาย
        for ($i = 1; $i <= 61; $i++) {
            if (!in_array($i, $existing_order_list)) {
                // คำนวณ row และ column
                if ($i == 1) {
                    $row = 1;
                    $column = 0;
                } else {
                    $adjusted_position = $i - 2;
                    $row = floor($adjusted_position / 3) + 2;
                    $column = $adjusted_position % 3;
                }

                $position_data = [
                    'type_id' => $type_id,
                    'position_data' => json_encode($empty_data),
                    'position_row' => $row,
                    'position_column' => $column,
                    'position_order' => $i,
                    'position_status' => 'active',
                    'created_by' => 'system'
                ];

                $this->db->insert('tbl_dynamic_positions', $position_data);
            }
        }

        return true;
    }

    /**
     * ดึงตำแหน่งทั้งหมดของประเภท (61 ตำแหน่ง) พร้อมจัดเรียงตาม slot
     */
    public function get_all_61_positions($type_id, $status = 'active')
    {
        $this->db->where('type_id', $type_id);
        if ($status) {
            $this->db->where('position_status', $status);
        }
        $this->db->order_by('position_order', 'ASC'); // เรียงตาม slot (1-61)

        $positions = $this->db->get('tbl_dynamic_positions')->result();

        // แปลง JSON data กลับเป็น object
        foreach ($positions as &$position) {
            $position->data = json_decode($position->position_data, true) ?: [];
        }

        // ถ้าไม่ครบ 61 ตำแหน่ง ให้สร้างเพิ่ม
        if (count($positions) < 61) {
            $this->ensure_61_positions($type_id);
            // ดึงข้อมูลใหม่อีกครั้ง
            return $this->get_all_61_positions($type_id, $status);
        }

        return $positions;
    }

    // ==================== Slot Management ====================

    /**
     * ดึงตำแหน่งตาม slot
     */
    public function get_position_by_slot($type_id, $slot_id)
    {
        $this->db->where('type_id', $type_id);
        $this->db->where('position_order', $slot_id);
        $position = $this->db->get('tbl_dynamic_positions')->row();

        if ($position) {
            $position->data = json_decode($position->position_data, true) ?: [];
            $position->files = $this->get_position_files($position->position_id);
        }

        return $position;
    }

    /**
     * แก้ไขฟังก์ชัน add_position_to_slot - ลบการจำกัด slot_id
     */
    public function add_position_to_slot($type_id, $slot_id, $form_data, $files = [])
    {
        // ตรวจสอบว่า slot_id ถูกต้อง (เอาเงื่อนไข > 61 ออก)
        if ($slot_id < 1) {
            return false;
        }

        // ดึงตำแหน่งจาก slot_id และ type_id
        $existing_position = $this->get_position_by_slot($type_id, $slot_id);

        if (!$existing_position) {
            // ถ้าไม่มี ให้สร้างใหม่
            return $this->create_position_in_slot($type_id, $slot_id, $form_data, $files);
        } else {
            // ถ้ามี ให้อัพเดต
            return $this->update_position_in_slot($existing_position->position_id, $form_data, $files);
        }
    }

    /**
     * แก้ไขฟังก์ชัน create_position_in_slot - ลบการจำกัด slot_id
     */
    private function create_position_in_slot($type_id, $slot_id, $form_data, $files)
    {
        // ตรวจสอบพื้นที่เก็บข้อมูล
        if (!$this->check_storage_space($files)) {
            $this->session->set_flashdata('save_error', TRUE);
            return false;
        }

        // อัพโหลดไฟล์
        $uploaded_files = $this->upload_files($files);

        // รวมข้อมูลไฟล์เข้ากับข้อมูลฟอร์ม
        foreach ($uploaded_files as $field => $file_info) {
            $form_data[$field] = $file_info['file_name'];
        }

        // คำนวณ row และ column
        if ($slot_id == 1) {
            $row = 1;
            $column = 0;
        } else {
            $adjusted_position = $slot_id - 2;
            $row = floor($adjusted_position / 3) + 2;
            $column = $adjusted_position % 3;
        }

        $data = [
            'type_id' => $type_id,
            'position_data' => json_encode($form_data),
            'position_row' => $row,
            'position_column' => $column,
            'position_order' => $slot_id,
            'position_status' => 'active',
            'created_by' => $this->session->userdata('m_fname')
        ];

        $this->db->insert('tbl_dynamic_positions', $data);
        $position_id = $this->db->insert_id();

        // บันทึกข้อมูลไฟล์
        $this->save_position_files($position_id, $uploaded_files);

        if (class_exists('Space_model')) {
            $this->space_model->update_server_current();
        }

        return $position_id;
    }

    /**
     * อัพเดตตำแหน่งใน slot ที่มีอยู่
     */
    public function update_position_in_slot($position_id, $form_data, $files)
    {
        $position = $this->get_position($position_id);
        if (!$position) return false;

        // ตรวจสอบพื้นที่สำหรับไฟล์ใหม่
        if (!$this->check_storage_space($files)) {
            $this->session->set_flashdata('save_error', TRUE);
            return false;
        }

        // จัดการไฟล์
        $this->handle_file_updates($position_id, $position->type_id, $files, $form_data);

        $data = [
            'position_data' => json_encode($form_data),
            'updated_by' => $this->session->userdata('m_fname')
        ];

        $this->db->where('position_id', $position_id);
        $result = $this->db->update('tbl_dynamic_positions', $data);

        $this->space_model->update_server_current();

        return $result;
    }

    /**
     * แก้ไขฟังก์ชัน clear_position_slot - ลบการจำกัด slot_id
     */
    public function clear_position_slot($type_id, $slot_id)
    {
        $position = $this->get_position_by_slot($type_id, $slot_id);
        if (!$position) return false;

        // ลบไฟล์ทั้งหมด
        $files = $this->get_position_files($position->position_id);
        foreach ($files as $file) {
            if (file_exists($file->file_path)) {
                unlink($file->file_path);
            }
        }
        $this->db->delete('tbl_position_files', ['position_id' => $position->position_id]);

        // ดึงฟิลด์เพื่อสร้างข้อมูลว่าง
        $fields = $this->get_position_fields($position->type_id);
        $empty_data = [];
        foreach ($fields as $field) {
            $empty_data[$field->field_name] = '';
        }

        // อัพเดตเป็นข้อมูลว่าง
        $data = [
            'position_data' => json_encode($empty_data),
            'updated_by' => $this->session->userdata('m_fname')
        ];

        $this->db->where('position_id', $position->position_id);
        return $this->db->update('tbl_dynamic_positions', $data);
    }

    /**
     * แก้ไขฟังก์ชัน is_slot_empty - ลบการจำกัด slot_id
     */
    public function is_slot_empty($type_id, $slot_id)
    {
        $position = $this->get_position_by_slot($type_id, $slot_id);
        if (!$position) return true;

        $data = $position->data;

        // ตรวจสอบว่าทุกฟิลด์ว่างหรือไม่
        foreach ($data as $value) {
            if (!empty(trim($value))) {
                return false;
            }
        }

        return true;
    }

    /**
     * นับจำนวน slot ที่มีข้อมูล
     */
    public function count_filled_slots($type_id)
    {
        $count = 0;
        for ($i = 1; $i <= 61; $i++) {
            if (!$this->is_slot_empty($type_id, $i)) {
                $count++;
            }
        }
        return $count;
    }

    // ==================== Drag & Drop Support ====================

    /**
     * อัพเดตตำแหน่งแบบ drag & drop
     */
    public function update_positions_order($type_id, $positions)
    {
        $this->db->trans_start();

        try {
            foreach ($positions as $index => $position) {
                $position_id = $position['id'];
                $new_order = $index + 2; // เริ่มจาก slot 2 (slot 1 คือตำแหน่งหลัก)

                // คำนวณ row และ column ใหม่
                if ($new_order == 1) {
                    $row = 1;
                    $column = 0;
                } else {
                    $adjusted_position = $new_order - 2;
                    $row = floor($adjusted_position / 3) + 2;
                    $column = $adjusted_position % 3;
                }

                $data = [
                    'position_row' => $row,
                    'position_column' => $column,
                    'position_order' => $new_order,
                    'updated_by' => $this->session->userdata('m_fname'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->where('position_id', $position_id);
                $this->db->where('type_id', $type_id);

                if (!$this->db->update('tbl_dynamic_positions', $data)) {
                    throw new Exception("Failed to update position ID: {$position_id}");
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception("Transaction failed");
            }

            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Update positions order failed: ' . $e->getMessage());
            return false;
        }
    }

    // ==================== File Management ====================

    /**
     * ดึงไฟล์ของตำแหน่ง
     */
    public function get_position_files($position_id, $field_name = null)
    {
        $this->db->where('position_id', $position_id);
        if ($field_name) {
            $this->db->where('field_name', $field_name);
        }
        return $this->db->get('tbl_position_files')->result();
    }

    /**
     * บันทึกข้อมูลไฟล์
     */
    private function save_position_files($position_id, $uploaded_files)
    {
        foreach ($uploaded_files as $field_name => $file_data) {
            $data = [
                'position_id' => $position_id,
                'field_name' => $field_name,
                'file_name' => $file_data['file_name'],
                'file_original_name' => $file_data['orig_name'],
                'file_size' => $file_data['file_size'],
                'file_type' => $file_data['file_type'],
                'file_path' => $file_data['full_path'],
                'uploaded_by' => $this->session->userdata('m_fname')
            ];

            $this->db->insert('tbl_position_files', $data);
        }
    }

    /**
     * จัดการอัพเดตไฟล์
     */
    private function handle_file_updates($position_id, $type_id, $files, &$form_data)
    {
        $file_fields = $this->get_file_fields($type_id);

        foreach ($file_fields as $field) {
            $field_name = $field->field_name;

            // ตรวจสอบว่ามีการอัพโหลดไฟล์ใหม่หรือไม่
            if (isset($files[$field_name]) && !empty($files[$field_name]['name'])) {

                // มีการอัพโหลดไฟล์ใหม่ - ลบไฟล์เก่าก่อน
                $old_files = $this->get_position_files($position_id, $field_name);
                foreach ($old_files as $old_file) {
                    if (file_exists($old_file->file_path)) {
                        unlink($old_file->file_path);
                    }
                }
                $this->db->delete('tbl_position_files', [
                    'position_id' => $position_id,
                    'field_name' => $field_name
                ]);

                // อัพโหลดไฟล์ใหม่
                $uploaded = $this->upload_files([$field_name => $files[$field_name]]);
                if (isset($uploaded[$field_name])) {
                    $form_data[$field_name] = $uploaded[$field_name]['file_name'];
                    $this->save_position_files($position_id, [$field_name => $uploaded[$field_name]]);
                }
            } else {
                // ไม่มีการอัพโหลดไฟล์ใหม่ - เก็บไฟล์เก่าไว้
                $existing_files = $this->get_position_files($position_id, $field_name);
                if (!empty($existing_files)) {
                    // ใช้ไฟล์เก่าที่มีอยู่
                    $form_data[$field_name] = $existing_files[0]->file_name;
                }
            }
        }
    }


    /**
     * อัพโหลดไฟล์
     */
    private function upload_files($files)
    {
        $uploaded = [];

        foreach ($files as $field_name => $file) {
            if (!empty($file['name'])) {
                // ตรวจสอบประเภทไฟล์ก่อนอัพโหลด
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/jfif'];
                if (!in_array($file['type'], $allowed_types)) {
                    log_message('error', 'Invalid file type for ' . $field_name . ': ' . $file['type']);
                    continue; // ข้ามไฟล์ที่ไม่ใช่รูปภาพ
                }

                $config = [
                    'upload_path' => './docs/img/',
                    'allowed_types' => 'gif|jpg|png|jpeg|webp|jfif', // เฉพาะรูปภาพ
                    'max_size' => 5120, // 5MB
                    'encrypt_name' => TRUE,
                    'remove_spaces' => TRUE
                ];

                $this->load->library('upload', $config);

                if ($this->upload->do_upload($field_name, $file)) {
                    $upload_data = $this->upload->data();

                    // ปรับขนาดรูปภาพถ้าใหญ่เกินไป
                    $this->resize_image_if_needed($upload_data['full_path']);

                    $uploaded[$field_name] = $upload_data;
                } else {
                    log_message('error', 'Upload failed for ' . $field_name . ': ' . $this->upload->display_errors());
                }
            }
        }

        return $uploaded;
    }

    /**
     * ปรับขนาดรูปภาพถ้าจำเป็น
     */
    private function resize_image_if_needed($file_path)
    {
        $image_info = getimagesize($file_path);
        if (!$image_info) return false;

        // ถ้ารูปใหญ่เกิน 1920x1080 ให้ปรับขนาด
        if ($image_info[0] > 1920 || $image_info[1] > 1080) {
            $this->load->library('image_lib');

            $config = [
                'image_library' => 'gd2',
                'source_image' => $file_path,
                'maintain_ratio' => TRUE,
                'width' => 1920,
                'height' => 1080,
                'quality' => 85
            ];

            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();
        }

        return true;
    }

    /**
     * Validate ข้อมูลฟอร์ม (เพิ่มการตรวจสอบไฟล์)
     */
    private function validate_form_data($type_id, $form_data, $files = [])
    {
        $fields = $this->dynamic_position_model->get_position_fields($type_id);
        $errors = [];

        foreach ($fields as $field) {
            $value = $form_data[$field->field_name] ?? '';

            // ตรวจสอบ required
            if ($field->field_required && empty(trim($value))) {
                $errors[] = $field->field_label . ' จำเป็นต้องกรอก';
            }

            // ตรวจสอบความยาว
            if ($field->field_max_length && strlen($value) > $field->field_max_length) {
                $errors[] = $field->field_label . ' ยาวเกินกำหนด (สูงสุด ' . $field->field_max_length . ' ตัวอักษร)';
            }

            // ตรวจสอบรูปแบบ email
            if ($field->field_type === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $field->field_label . ' รูปแบบไม่ถูกต้อง';
            }

            // ตรวจสอบรูปแบบ tel
            if ($field->field_type === 'tel' && !empty($value) && !preg_match('/^[0-9\-\+\s\(\)]+$/', $value)) {
                $errors[] = $field->field_label . ' รูปแบบไม่ถูกต้อง';
            }
        }

        // ตรวจสอบไฟล์
        foreach ($files as $field_name => $file) {
            if (!empty($file['name'])) {
                // ตรวจสอบขนาดไฟล์
                if ($file['size'] > (5 * 1024 * 1024)) { // 5MB
                    $errors[] = 'ไฟล์ ' . $field_name . ' มีขนาดใหญ่เกิน 5MB';
                }

                // ตรวจสอบประเภทไฟล์
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/jfif'];
                if (!in_array($file['type'], $allowed_types)) {
                    $errors[] = 'ไฟล์ ' . $field_name . ' ต้องเป็นรูปภาพเท่านั้น (JPG, PNG, GIF, WEBP)';
                }

                // ตรวจสอบนามสกุลไฟล์
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'];
                if (!in_array($file_ext, $allowed_extensions)) {
                    $errors[] = 'ไฟล์ ' . $field_name . ' ต้องมีนามสกุล .jpg, .png, .gif หรือ .webp เท่านั้น';
                }
            }
        }

        return $errors;
    }

    /**
     * แก้ไขในฟังก์ชัน save_to_slot() และ update_slot()
     */
    public function save_to_slot($peng, $slot_id)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type || $slot_id < 1 || $slot_id > 61) {
            show_404();
            return;
        }

        // รวบรวมข้อมูลจากฟอร์ม
        $form_data = $this->collect_form_data($type->type_id);
        $files = $this->collect_files($type->type_id);

        // Validate ข้อมูล (เพิ่มการตรวจสอบไฟล์)
        $errors = $this->validate_form_data($type->type_id, $form_data, $files);
        if (!empty($errors)) {
            $this->session->set_flashdata('validation_errors', $errors);
            redirect("dynamic_position_backend/add_to_slot/{$peng}/{$slot_id}", 'refresh');
            return;
        }

        $result = $this->dynamic_position_model->add_position_to_slot($type->type_id, $slot_id, $form_data, $files);

        if ($result) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
    }

    /**
     * เพิ่มฟังก์ชันใหม่สำหรับตรวจสอบไฟล์รูปภาพ
     */
    private function is_valid_image($file_path)
    {
        // ตรวจสอบด้วย getimagesize
        $image_info = getimagesize($file_path);
        if (!$image_info) {
            return false;
        }

        // ตรวจสอบ MIME type
        $allowed_mime_types = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/jfif'
        ];

        return in_array($image_info['mime'], $allowed_mime_types);
    }

    /**
     * สร้างไฟล์ .htaccess ในโฟลเดอร์ docs/img/ เพื่อป้องกันการรันไฟล์ PHP
     */
    private function create_security_htaccess()
    {
        $htaccess_content = "# ป้องกันการรันไฟล์ PHP
<Files *.php>
    Order Deny,Allow
    Deny from all
</Files>

# อนุญาตเฉพาะไฟล์รูปภาพ
<FilesMatch \"\.(jpg|jpeg|png|gif|webp|jfif)$\">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# ป้องกันการแสดงรายการไฟล์
Options -Indexes

# ป้องกัน hotlinking (ถ้าต้องการ)
RewriteEngine on
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?yourdomain.com [NC]
RewriteRule \.(jpg|jpeg|png|gif|webp|jfif)$ - [NC,F,L]";

        $htaccess_path = './docs/img/.htaccess';
        file_put_contents($htaccess_path, $htaccess_content);
    }

    /**
     * ตรวจสอบพื้นที่เก็บข้อมูล
     */
    private function check_storage_space($files)
    {
        if (empty($files)) return true;

        $used_space_mb = $this->space_model->get_used_space();
        $upload_limit_mb = $this->space_model->get_limit_storage();

        $total_space_required = 0;
        foreach ($files as $file) {
            if (!empty($file['name'])) {
                $total_space_required += $file['size'];
            }
        }

        return ($used_space_mb + ($total_space_required / (1024 * 1024 * 1024))) < $upload_limit_mb;
    }

    // ==================== Legacy Support (รองรับฟังก์ชันเดิม) ====================

    /**
     * รองรับการทำงานแบบเดิม
     */
    public function get_positions($type_id, $status = 'active')
    {
        return $this->get_all_61_positions($type_id, $status);
    }

    /**
     * ดึงข้อมูลตำแหน่งตาม ID
     */
    public function get_position($position_id)
    {
        $this->db->select('dp.*, pt.pname, pt.peng');
        $this->db->from('tbl_dynamic_positions dp');
        $this->db->join('tbl_position pt', 'dp.type_id = pt.pid');
        $this->db->where('dp.position_id', $position_id);

        $position = $this->db->get()->row();

        if ($position) {
            $position->data = json_decode($position->position_data, true) ?: [];
            $position->files = $this->get_position_files($position_id);
        }

        return $position;
    }

    /**
     * ดึงข้อมูลตำแหน่งตามแถว
     */
    public function get_positions_by_row($type_id, $row)
    {
        $this->db->where('type_id', $type_id);
        $this->db->where('position_row', $row);
        $this->db->where('position_status', 'active');
        $this->db->order_by('position_column', 'ASC');

        $positions = $this->db->get('tbl_dynamic_positions')->result();

        // แปลง JSON data
        foreach ($positions as &$position) {
            $position->data = json_decode($position->position_data, true) ?: [];
        }

        return $positions;
    }

    // ==================== เมธอดพิเศษสำหรับผู้บริหาร ====================

    /**
     * ดึงข้อมูลผู้บริหารหลัก (slot 1)
     */
    public function get_main_executive()
    {
        $executives_type = $this->get_position_type_by_name('executives');
        if (!$executives_type) return null;

        return $this->get_position_by_slot($executives_type->type_id, 1);
    }

    /**
     * ดึงข้อมูลผู้บริหารรอง (slot 2-61)
     */
    public function get_sub_executives()
    {
        $executives_type = $this->get_position_type_by_name('executives');
        if (!$executives_type) return [];

        $this->db->where('type_id', $executives_type->type_id);
        $this->db->where('position_order >', 1);
        $this->db->order_by('position_row', 'ASC');
        $this->db->order_by('position_column', 'ASC');

        $positions = $this->db->get('tbl_dynamic_positions')->result();

        // แปลง JSON data
        foreach ($positions as &$position) {
            $position->data = json_decode($position->position_data, true) ?: [];
        }

        return $positions;
    }

    /**
     * รองรับฟังก์ชันเดิม p_executives_one และ p_executives_under_one
     */
    public function p_executives_one()
    {
        $main = $this->get_main_executive();
        return $main ? [$main] : [];
    }

    public function p_executives_under_one()
    {
        return $this->get_sub_executives();
    }

    /**
     * รองรับฟังก์ชันเดิม p_executives_row_1, p_executives_row_2, etc.
     */
    public function p_executives_row_1()
    {
        $executives_type = $this->get_position_type_by_name('executives');
        return $executives_type ? $this->get_positions_by_row($executives_type->type_id, 1) : [];
    }

    public function p_executives_row_2()
    {
        $executives_type = $this->get_position_type_by_name('executives');
        return $executives_type ? $this->get_positions_by_row($executives_type->type_id, 2) : [];
    }

    public function p_executives_row_3()
    {
        $executives_type = $this->get_position_type_by_name('executives');
        return $executives_type ? $this->get_positions_by_row($executives_type->type_id, 3) : [];
    }

    public function p_executives_row_4()
    {
        $executives_type = $this->get_position_type_by_name('executives');
        return $executives_type ? $this->get_positions_by_row($executives_type->type_id, 4) : [];
    }

    public function p_executives_row_5()
    {
        $executives_type = $this->get_position_type_by_name('executives');
        return $executives_type ? $this->get_positions_by_row($executives_type->type_id, 5) : [];
    }

    // ==================== Statistics & Utilities ====================

    /**
     * ดึงสถิติการใช้งาน
     */
    public function get_usage_statistics($type_id)
    {
        $total_positions = $this->db->where('type_id', $type_id)->count_all_results('tbl_dynamic_positions');
        $filled_positions = $this->count_filled_slots($type_id);

        return [
            'total' => $total_positions,
            'filled' => $filled_positions,
            'empty' => $total_positions - $filled_positions,
            'percentage' => $total_positions > 0 ? round(($filled_positions / $total_positions) * 100, 1) : 0
        ];
    }

    /**
     * เพิ่ม slot ใหม่ให้กับประเภทตำแหน่ง
     */
    public function add_new_slot($type_id, $count = 1)
    {
        // หา slot ล่าสุด
        $this->db->select_max('position_order');
        $this->db->where('type_id', $type_id);
        $query = $this->db->get('tbl_dynamic_positions');
        $result = $query->row();

        $last_slot = $result->position_order ?? 0;

        // ดึงฟิลด์เพื่อสร้างข้อมูลว่าง
        $fields = $this->get_position_fields($type_id);
        $empty_data = [];
        foreach ($fields as $field) {
            $empty_data[$field->field_name] = '';
        }

        $added_slots = [];

        for ($i = 1; $i <= $count; $i++) {
            $new_slot = $last_slot + $i;

            // คำนวณ row และ column
            if ($new_slot == 1) {
                $row = 1;
                $column = 0;
            } else {
                $adjusted_position = $new_slot - 2;
                $row = floor($adjusted_position / 3) + 2;
                $column = $adjusted_position % 3;
            }

            $position_data = [
                'type_id' => $type_id,
                'position_data' => json_encode($empty_data),
                'position_row' => $row,
                'position_column' => $column,
                'position_order' => $new_slot,
                'position_status' => 'active',
                'created_by' => $this->session->userdata('m_fname') ?: 'system',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_dynamic_positions', $position_data);

            if ($this->db->affected_rows() > 0) {
                $added_slots[] = $new_slot;
            }
        }

        return $added_slots;
    }

    /**
     * นับจำนวน slot ทั้งหมดของประเภทตำแหน่ง
     */
    public function count_total_slots($type_id)
    {
        $this->db->where('type_id', $type_id);
        return $this->db->count_all_results('tbl_dynamic_positions');
    }

    /**
     * ดึง slot ที่ใหญ่ที่สุดของประเภทตำแหน่ง
     */
    public function get_max_slot($type_id)
    {
        $this->db->select_max('position_order');
        $this->db->where('type_id', $type_id);
        $query = $this->db->get('tbl_dynamic_positions');
        $result = $query->row();

        return $result->position_order ?? 0;
    }

    /**
     * ดึงข้อมูลตำแหน่งทั้งหมดแบบ dynamic (ไม่จำกัด 61)
     */
    public function get_all_positions_dynamic($type_id, $status = 'active')
    {
        $this->db->where('type_id', $type_id);
        if ($status) {
            $this->db->where('position_status', $status);
        }
        $this->db->order_by('position_order', 'ASC');

        $positions = $this->db->get('tbl_dynamic_positions')->result();

        // แปลง JSON data กลับเป็น object
        foreach ($positions as &$position) {
            $position->data = json_decode($position->position_data, true) ?: [];
        }

        return $positions;
    }

    /**
     * อัปเดตฟังก์ชัน ensure_61_positions ให้เป็น ensure_minimum_positions
     */
    public function ensure_minimum_positions($type_id, $minimum = 61)
    {
        // ดึงตำแหน่งที่มีอยู่
        $this->db->select('position_order');
        $this->db->where('type_id', $type_id);
        $existing_orders = $this->db->get('tbl_dynamic_positions')->result_array();
        $existing_order_list = array_column($existing_orders, 'position_order');

        $max_existing = empty($existing_order_list) ? 0 : max($existing_order_list);
        $target = max($minimum, $max_existing);

        // ดึงฟิลด์เพื่อสร้างข้อมูลว่าง
        $fields = $this->get_position_fields($type_id);
        $empty_data = [];
        foreach ($fields as $field) {
            $empty_data[$field->field_name] = '';
        }

        // สร้างตำแหน่งที่ขาดหาย
        for ($i = 1; $i <= $target; $i++) {
            if (!in_array($i, $existing_order_list)) {
                // คำนวณ row และ column
                if ($i == 1) {
                    $row = 1;
                    $column = 0;
                } else {
                    $adjusted_position = $i - 2;
                    $row = floor($adjusted_position / 3) + 2;
                    $column = $adjusted_position % 3;
                }

                $position_data = [
                    'type_id' => $type_id,
                    'position_data' => json_encode($empty_data),
                    'position_row' => $row,
                    'position_column' => $column,
                    'position_order' => $i,
                    'position_status' => 'active',
                    'created_by' => 'system'
                ];

                $this->db->insert('tbl_dynamic_positions', $position_data);
            }
        }

        return true;
    }

    /**
     * เรียงลำดับ slots ใหม่หลังจากเพิ่ม
     */
    public function reorder_slots($type_id)
    {
        $this->db->select('position_id, position_order');
        $this->db->where('type_id', $type_id);
        $this->db->order_by('position_order', 'ASC');
        $positions = $this->db->get('tbl_dynamic_positions')->result();

        $new_order = 1;
        foreach ($positions as $position) {
            // คำนวณ row และ column ใหม่
            if ($new_order == 1) {
                $row = 1;
                $column = 0;
            } else {
                $adjusted_position = $new_order - 2;
                $row = floor($adjusted_position / 3) + 2;
                $column = $adjusted_position % 3;
            }

            $this->db->where('position_id', $position->position_id);
            $this->db->update('tbl_dynamic_positions', [
                'position_order' => $new_order,
                'position_row' => $row,
                'position_column' => $column,
                'updated_by' => $this->session->userdata('m_fname') ?: 'system',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $new_order++;
        }

        return true;
    }
}
