<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dynamic_position_backend extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dynamic_position_model');
        $this->load->model('member_model');
        $this->load->model('space_model');
		
		if (!in_array($this->session->userdata('m_system'), ['system_admin', 'super_admin', 'user_admin', 'end_user'])) {
            redirect('User/logout', 'refresh');
        }
    }

    /**
     * หน้าแรกแสดงประเภทตำแหน่งทั้งหมด
     */
    public function index()
    {
        // ดึงประเภทตำแหน่งทั้งหมด ไม่กรองสถานะ
        $position_types = $this->dynamic_position_model->get_position_types(null); // null = ไม่กรอง

        // เพิ่มข้อมูลจำนวนตำแหน่งที่มีข้อมูลแต่ละประเภท
        foreach ($position_types as &$type) {
            // ใช้ dynamic slots แทน 61
            $total_positions = $this->dynamic_position_model->count_total_slots($type->pid);
            $filled_positions = $this->dynamic_position_model->count_filled_slots($type->pid);

            $type->total_positions = $total_positions;
            $type->filled_positions = $filled_positions;
            $type->empty_positions = $total_positions - $filled_positions;
            $type->usage_percentage = $total_positions > 0 ? round(($filled_positions / $total_positions) * 100, 1) : 0;

            // เพิ่มข้อมูลสถานะการใช้งาน
            if ($type->usage_percentage >= 90) {
                $type->usage_status = 'full';
                $type->usage_color = 'danger';
            } elseif ($type->usage_percentage >= 70) {
                $type->usage_status = 'high';
                $type->usage_color = 'warning';
            } elseif ($type->usage_percentage >= 40) {
                $type->usage_status = 'medium';
                $type->usage_color = 'info';
            } else {
                $type->usage_status = 'low';
                $type->usage_color = 'secondary';
            }
        }

        $data['position_types'] = $position_types;

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/dynamic_position_types', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }


    /**
     * เพิ่ม slots ใหม่
     */
    public function add_slots($peng)
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        // อ่านข้อมูล JSON
        $json_data = json_decode(file_get_contents('php://input'), true);
        $count = isset($json_data['count']) ? (int)$json_data['count'] : 1;

        // ตรวจสอบจำนวนที่ขอเพิ่ม
        if ($count < 1 || $count > 20) {
            $response = ['success' => false, 'message' => 'จำนวน slots ที่เพิ่มได้ต้องอยู่ระหว่าง 1-20'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        try {
            $added_slots = $this->dynamic_position_model->add_new_slot($type->pid, $count);

            if (!empty($added_slots)) {
                $total_slots = $this->dynamic_position_model->count_total_slots($type->pid);

                $response = [
                    'success' => true,
                    'message' => "เพิ่ม {$count} ตำแหน่งเรียบร้อยแล้ว",
                    'added_slots' => $added_slots,
                    'total_slots' => $total_slots,
                    'new_slots_range' => [
                        'start' => min($added_slots),
                        'end' => max($added_slots)
                    ]
                ];

                // Log การดำเนินการ
                $this->log_activity('add_slots', $peng, null, "Added {$count} slots: " . implode(', ', $added_slots));
            } else {
                $response = ['success' => false, 'message' => 'ไม่สามารถเพิ่มตำแหน่งได้'];
            }
        } catch (Exception $e) {
            log_message('error', 'Add slots failed: ' . $e->getMessage());
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * อัปเดตการแสดงผลใน manage() ให้รองรับ dynamic slots
     */
    public function manage($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        // ตรวจสอบและสร้างตำแหน่งขั้นต่ำ 61 ช่อง
        $this->dynamic_position_model->ensure_minimum_positions($type->pid, 61);

        // ดึงข้อมูลทั้งหมด (dynamic)
        $data['type'] = $type;
        $data['fields'] = $this->dynamic_position_model->get_position_fields($type->pid);
        $data['all_positions'] = $this->dynamic_position_model->get_all_positions_dynamic($type->pid);
        $data['total_slots'] = $this->dynamic_position_model->count_total_slots($type->pid);

        // แยกตำแหน่งหลัก (slot 1)
        $data['main_position'] = null;
        foreach ($data['all_positions'] as $position) {
            if ($position->position_order == 1) {
                $data['main_position'] = $position;
                break;
            }
        }

        // นับจำนวนตำแหน่งที่มีข้อมูล
        $data['filled_count'] = $this->dynamic_position_model->count_filled_slots($type->pid);

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/dynamic_position_grid', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * แก้ไขฟังก์ชัน add_to_slot - ลบการจำกัด slot_id
     */
    public function add_to_slot($peng, $slot_id)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type || $slot_id < 1) { // เอาเงื่อนไข > 61 ออก
            show_404();
            return;
        }

        // ตรวจสอบว่า slot นี้มีอยู่หรือไม่ ถ้าไม่มีให้สร้าง
        $max_slot = $this->dynamic_position_model->get_max_slot($type->pid);
        if ($slot_id > $max_slot) {
            // สร้าง slots ให้ครบจนถึง slot ที่ต้องการ
            $needed_slots = $slot_id - $max_slot;
            $this->dynamic_position_model->add_new_slot($type->pid, $needed_slots);
        }

        // ตรวจสอบว่า slot นี้ว่างหรือไม่
        $existing_position = $this->dynamic_position_model->get_position_by_slot($type->pid, $slot_id);
        if ($existing_position && !$this->dynamic_position_model->is_slot_empty($type->pid, $slot_id)) {
            // ถ้ามีข้อมูลแล้ว ให้ redirect ไปหน้าแก้ไข
            redirect("dynamic_position_backend/edit_slot/{$peng}/{$slot_id}", 'refresh');
            return;
        }

        $data['type'] = $type;
        $data['slot_id'] = $slot_id;
        $data['fields'] = $this->dynamic_position_model->get_position_fields($type->pid);
        $data['action_url'] = site_url("dynamic_position_backend/save_to_slot/{$peng}/{$slot_id}");
        $data['position'] = null;

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/dynamic_position_form', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * แก้ไขฟังก์ชัน save_to_slot - ลบการจำกัด slot_id
     */
    public function save_to_slot($peng, $slot_id)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type || $slot_id < 1) { // เอาเงื่อนไข > 61 ออก
            show_404();
            return;
        }

        // ตรวจสอบว่า slot นี้มีอยู่หรือไม่ ถ้าไม่มีให้สร้าง
        $max_slot = $this->dynamic_position_model->get_max_slot($type->pid);
        if ($slot_id > $max_slot) {
            $needed_slots = $slot_id - $max_slot;
            $this->dynamic_position_model->add_new_slot($type->pid, $needed_slots);
        }

        // รวบรวมข้อมูลจากฟอร์ม
        $form_data = $this->collect_form_data($type->pid);
        $files = $this->collect_files($type->pid);

        // Validate ข้อมูล
        $errors = $this->validate_form_data($type->pid, $form_data);
        if (!empty($errors)) {
            $this->session->set_flashdata('validation_errors', $errors);
            redirect("dynamic_position_backend/add_to_slot/{$peng}/{$slot_id}", 'refresh');
            return;
        }

        $result = $this->dynamic_position_model->add_position_to_slot($type->pid, $slot_id, $form_data, $files);

        if ($result) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
    }

    /**
     * แก้ไขฟังก์ชัน edit_slot - ลบการจำกัด slot_id
     */
    public function edit_slot($peng, $slot_id)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type || $slot_id < 1) { // เอาเงื่อนไข > 61 ออก
            show_404();
            return;
        }

        $position = $this->dynamic_position_model->get_position_by_slot($type->pid, $slot_id);
        if (!$position) {
            show_404();
            return;
        }

        $data['type'] = $type;
        $data['slot_id'] = $slot_id;
        $data['fields'] = $this->dynamic_position_model->get_position_fields($type->pid);
        $data['position'] = $position;
        $data['action_url'] = site_url("dynamic_position_backend/update_slot/{$peng}/{$slot_id}");

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/dynamic_position_form', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ดึงข้อมูลสถิติการใช้งานแบบ dynamic
     */
    public function get_slot_stats($peng)
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $total_slots = $this->dynamic_position_model->count_total_slots($type->pid);
        $filled_slots = $this->dynamic_position_model->count_filled_slots($type->pid);
        $empty_slots = $total_slots - $filled_slots;
        $usage_percentage = $total_slots > 0 ? round(($filled_slots / $total_slots) * 100, 1) : 0;

        $response = [
            'success' => true,
            'stats' => [
                'total_slots' => $total_slots,
                'filled_slots' => $filled_slots,
                'empty_slots' => $empty_slots,
                'usage_percentage' => $usage_percentage
            ],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * แก้ไขฟังก์ชัน update_slot - ลบการจำกัด slot_id
     */
    public function update_slot($peng, $slot_id)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type || $slot_id < 1) { // เอาเงื่อนไข > 61 ออก
            show_404();
            return;
        }

        $position = $this->dynamic_position_model->get_position_by_slot($type->pid, $slot_id);
        if (!$position) {
            show_404();
            return;
        }

        // รวบรวมข้อมูลจากฟอร์ม
        $form_data = $this->collect_form_data($type->pid);
        $files = $this->collect_files($type->pid);

        // Validate ข้อมูล
        $errors = $this->validate_form_data($type->pid, $form_data);
        if (!empty($errors)) {
            $this->session->set_flashdata('validation_errors', $errors);
            redirect("dynamic_position_backend/edit_slot/{$peng}/{$slot_id}", 'refresh');
            return;
        }

        $result = $this->dynamic_position_model->update_position_in_slot($position->position_id, $form_data, $files);

        if ($result) {
            $this->session->set_flashdata('save_success', TRUE);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
    }

    /**
     * แก้ไขฟังก์ชัน clear_slot - ลบการจำกัด slot_id
     */
    public function clear_slot($peng, $slot_id)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type || $slot_id < 1) { // เอาเงื่อนไข > 61 ออก
            show_404();
            return;
        }

        $result = $this->dynamic_position_model->clear_position_slot($type->pid, $slot_id);

        if ($result) {
            $this->session->set_flashdata('del_success', TRUE);
        } else {
            $this->session->set_flashdata('del_error', TRUE);
        }

        redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
    }

    /**
     * รีเฟรชข้อมูล Grid
     */
    public function refresh_grid($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        // ตรวจสอบและสร้างตำแหน่งให้ครบ 61 ช่อง
        $this->dynamic_position_model->ensure_61_positions($type->pid);

        $this->session->set_flashdata('refresh_success', TRUE);
        redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
    }

    /**
     * อัพเดตตำแหน่งแบบ drag & drop
     */
    public function update_positions($peng)
    {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            $response = ['success' => false, 'message' => 'Direct access not allowed'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        // อ่านข้อมูล JSON จาก request body
        $input = file_get_contents('php://input');

        // Log ข้อมูลที่ได้รับเพื่อ debug
        log_message('info', 'Update positions input: ' . $input);

        $json_data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response = ['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        if (!isset($json_data['positions']) || !is_array($json_data['positions'])) {
            $response = ['success' => false, 'message' => 'ข้อมูลตำแหน่งไม่ถูกต้อง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        try {
            $result = $this->dynamic_position_model->update_positions_order($type->pid, $json_data['positions']);

            if ($result) {
                $response = ['success' => true, 'message' => 'อัพเดตตำแหน่งเรียบร้อยแล้ว'];

                // Log การดำเนินการ
                log_message('info', 'Position order updated successfully for type: ' . $peng);
            } else {
                $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัพเดตตำแหน่ง'];
            }
        } catch (Exception $e) {
            log_message('error', 'Update positions error: ' . $e->getMessage());
            $response = ['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ'];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * API สำหรับดึงข้อมูลตำแหน่งใน slot
     */
    public function get_slot_data($peng, $slot_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type || $slot_id < 1 || $slot_id > 61) {
            $response = ['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $position = $this->dynamic_position_model->get_position_by_slot($type->pid, $slot_id);
        $is_empty = $this->dynamic_position_model->is_slot_empty($type->pid, $slot_id);

        $response = [
            'success' => true,
            'position' => $position ? $position->data : null,
            'is_empty' => $is_empty,
            'slot_id' => $slot_id
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * สร้างประเภทตำแหน่งใหม่พร้อม 61 ช่อง
     */
    public function create_new_type()
    {
        // ตรวจสอบการส่งข้อมูล
        if ($this->input->method() === 'post') {
            $type_data = [
                'peng' => $this->input->post('peng'),
                'pname' => $this->input->post('pname'),
                'pdescription' => $this->input->post('pdescription'),
                'porder' => $this->input->post('porder') ?: 0,
                'psub' => $this->input->post('psub') ?: 0,
                'pstatus' => 'show',
                'pby' => $this->session->userdata('m_fname')
            ];

            // ตรวจสอบว่าชื่อซ้ำหรือไม่
            $existing = $this->dynamic_position_model->get_position_type_by_name($type_data['peng']);
            if ($existing) {
                $this->session->set_flashdata('error_message', 'ชื่อประเภทตำแหน่งนี้มีอยู่แล้ว');
                redirect('dynamic_position_backend/create_new_type', 'refresh');
                return;
            }

            // ฟิลด์พื้นฐาน
            $basic_fields = [
                [
                    'field_name' => 'name',
                    'field_label' => 'ชื่อ-นามสกุล',
                    'field_type' => 'text',
                    'field_required' => 1,
                    'field_order' => 1
                ],
                [
                    'field_name' => 'position',
                    'field_label' => 'ตำแหน่ง',
                    'field_type' => 'text',
                    'field_required' => 1,
                    'field_order' => 2
                ],
                [
                    'field_name' => 'phone',
                    'field_label' => 'เบอร์โทรศัพท์',
                    'field_type' => 'tel',
                    'field_required' => 0,
                    'field_order' => 3
                ],
                [
                    'field_name' => 'email',
                    'field_label' => 'อีเมล',
                    'field_type' => 'email',
                    'field_required' => 0,
                    'field_order' => 4
                ],
                [
                    'field_name' => 'image',
                    'field_label' => 'รูปภาพ',
                    'field_type' => 'file',
                    'field_required' => 0,
                    'field_order' => 5
                ]
            ];

            $result = $this->dynamic_position_model->create_position_type($type_data, $basic_fields);

            if ($result) {
                $this->session->set_flashdata('save_success', TRUE);
                redirect('dynamic_position_backend/manage/' . $type_data['peng'], 'refresh');
            } else {
                $this->session->set_flashdata('save_error', TRUE);
                redirect('dynamic_position_backend/create_new_type', 'refresh');
            }
            return;
        }

        // แสดงฟอร์ม
        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/create_position_type');
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ดูสถิติการใช้งาน
     */
    public function statistics($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        $data['type'] = $type;

        // ใช้ dynamic slots แทน 61
        $data['total_slots'] = $this->dynamic_position_model->count_total_slots($type->pid);
        $data['filled_slots'] = $this->dynamic_position_model->count_filled_slots($type->pid);
        $data['empty_slots'] = $data['total_slots'] - $data['filled_slots'];
        $data['usage_percentage'] = $data['total_slots'] > 0 ? round(($data['filled_slots'] / $data['total_slots']) * 100, 1) : 0;

        // สถิติตามแถว (dynamic)
        $data['row_stats'] = [];
        $max_row = ceil(($data['total_slots'] - 1) / 3) + 1; // คำนวณจำนวนแถวจาก total slots

        for ($row = 1; $row <= $max_row; $row++) {
            $positions_in_row = $this->dynamic_position_model->get_positions_by_row($type->pid, $row);
            $filled_in_row = 0;
            foreach ($positions_in_row as $pos) {
                if (!$this->dynamic_position_model->is_slot_empty($type->pid, $pos->position_order)) {
                    $filled_in_row++;
                }
            }

            if (count($positions_in_row) > 0) { // แสดงเฉพาะแถวที่มีข้อมูล
                $data['row_stats'][$row] = [
                    'total' => count($positions_in_row),
                    'filled' => $filled_in_row,
                    'empty' => count($positions_in_row) - $filled_in_row
                ];
            }
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/position_statistics', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    // ==================== Helper Methods ====================

    /**
     * รวบรวมข้อมูลจากฟอร์ม
     */
    private function collect_form_data($pid)
    {
        $fields = $this->dynamic_position_model->get_position_fields($pid);
        $form_data = [];

        foreach ($fields as $field) {
            if ($field->field_type !== 'file') {
                $value = $this->input->post($field->field_name);
                $form_data[$field->field_name] = $value !== null ? $value : '';
            }
        }

        return $form_data;
    }

    /**
     * รวบรวมไฟล์ที่อัพโหลด
     */
    private function collect_files($pid)
    {
        $file_fields = $this->dynamic_position_model->get_file_fields($pid);
        $files = [];

        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field->field_name]['name'])) {
                $files[$field->field_name] = $_FILES[$field->field_name];
            }
        }

        return $files;
    }

    /**
     * Validate ข้อมูลฟอร์ม
     */
    private function validate_form_data($pid, $form_data)
    {
        $fields = $this->dynamic_position_model->get_position_fields($pid);
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

        return $errors;
    }

    /**
     * ตรวจสอบการเข้าถึงตามประเภทตำแหน่ง
     */
    // private function check_type_access($peng)
    // {
    //     // ระบบสิทธิ์ (สามารถปรับแต่งได้)
    //     $user_level = $this->session->userdata('m_system');

    //     if ($user_level === 'system_admin') {
    //         return true; // Super Admin เข้าถึงได้ทั้งหมด
    //     }

    //     // กำหนดสิทธิ์ตามประเภท
    //     $access_rules = [
    //         'executives' => ['system_admin', 'hr_admin'],
    //         'faculty' => ['system_admin', 'academic_admin', 'hr_admin'],
    //         'staff' => ['system_admin', 'hr_admin']
    //     ];

    //     $allowed_levels = $access_rules[$peng] ?? ['system_admin'];

    //     return in_array($user_level, $allowed_levels);
    // }

    /**
     * Log การดำเนินการ
     */
    // private function log_activity($action, $peng, $slot_id = null, $details = null)
    // {
    //     $log_data = [
    //         'user_id' => $this->session->userdata('m_id'),
    //         'user_name' => $this->session->userdata('m_fname'),
    //         'action' => $action,
    //         'peng' => $peng,
    //         'slot_id' => $slot_id,
    //         'details' => $details,
    //         'ip_address' => $this->input->ip_address(),
    //         'user_agent' => $this->input->user_agent(),
    //         'created_at' => date('Y-m-d H:i:s')
    //     ];

    //     // บันทึก log (ถ้ามีตาราง log)
    //     // $this->db->insert('tbl_activity_logs', $log_data);

    //     // หรือใช้ CI log
    //     log_message('info', 'Position Management: ' . json_encode($log_data));
    // }

    /**
     * ส่งออกข้อมูลเป็น Excel
     */
    public function export_excel($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        $this->load->library('excel');

        $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
        $fields = $this->dynamic_position_model->get_position_fields($type->pid);

        // สร้าง Excel
        $excel = new PHPExcel();
        $sheet = $excel->getActiveSheet();

        // Header
        $col = 0;
        $sheet->setCellValueByColumnAndRow($col++, 1, 'Slot');
        foreach ($fields as $field) {
            if ($field->field_type !== 'file') {
                $sheet->setCellValueByColumnAndRow($col++, 1, $field->field_label);
            }
        }

        // Data
        $row = 2;
        foreach ($positions as $position) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col++, $row, $position->position_order);

            foreach ($fields as $field) {
                if ($field->field_type !== 'file') {
                    $value = $position->data[$field->field_name] ?? '';
                    $sheet->setCellValueByColumnAndRow($col++, $row, $value);
                }
            }
            $row++;
        }

        // ส่งออกไฟล์
        $filename = $type->pname . '_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
    }

    /**
     * Import ข้อมูลจาก Excel
     */
    public function import_excel($peng)
    {
        if ($this->input->method() !== 'post') {
            redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        // จัดการ upload file
        $config['upload_path'] = './temp/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 10240; // 10MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('excel_file')) {
            $this->session->set_flashdata('upload_error', $this->upload->display_errors());
            redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
            return;
        }

        $file_data = $this->upload->data();

        // อ่านไฟล์ Excel และ import ข้อมูล
        // ... โค้ดสำหรับอ่านและ import ...

        // ลบไฟล์ temporary
        unlink($file_data['full_path']);

        $this->session->set_flashdata('import_success', TRUE);
        redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
    }

    /**
     * อัพเดตข้อมูลประเภทตำแหน่ง
     */
    public function update_position_type()
    {
        if ($this->input->method() !== 'post') {
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        $type_id = $this->input->post('type_id');
        $pname = $this->input->post('pname');
        $pdescription = $this->input->post('pdescription');
        $porder = $this->input->post('porder') ?: 0;
        $psub = $this->input->post('psub') ?: 0;

        // Validate ข้อมูล
        if (empty($type_id) || empty($pname)) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        // ตรวจสอบว่าประเภทตำแหน่งมีอยู่จริง
        $existing_type = $this->db->get_where('tbl_position', ['pid' => $type_id])->row();
        if (!$existing_type) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        // อัพเดตข้อมูล
        $update_data = [
            'pname' => $pname,
            'pdescription' => $pdescription,
            'porder' => $porder,
            'psub' => $psub,
            'pdatesave' => date('Y-m-d H:i:s')
        ];

        $this->db->where('pid', $type_id);
        $result = $this->db->update('tbl_position', $update_data);

        if ($result) {
            $this->session->set_flashdata('update_success', TRUE);
            $this->log_activity('update_type', $existing_type->peng, null, 'Updated position type: ' . $pname);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('dynamic_position_backend', 'refresh');
    }

    // เพิ่มเมธอดใหม่สำหรับแก้ไข toggle_status ให้รองรับ 'hide' แทน 'hidden'
    public function toggle_status($type_id, $new_status)
    {
        // ตรวจสอบพารามิเตอร์ - เปลี่ยนจาก 'hidden' เป็น 'hide'
        if (!$type_id || !in_array($new_status, ['show', 'hide'])) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        // ตรวจสอบว่าประเภทตำแหน่งมีอยู่จริง
        $existing_type = $this->db->get_where('tbl_position', ['pid' => $type_id])->row();
        if (!$existing_type) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        // อัพเดตสถานะ
        $update_data = [
            'pstatus' => $new_status,
            'pdatesave' => date('Y-m-d H:i:s')
        ];

        $this->db->where('pid', $type_id);
        $result = $this->db->update('tbl_position', $update_data);

        if ($result) {
            $this->session->set_flashdata('status_success', TRUE);
            $action = $new_status === 'show' ? 'show' : 'hide';
            $this->log_activity($action . '_type', $existing_type->peng, null, 'Changed status to: ' . $new_status);
        } else {
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('dynamic_position_backend', 'refresh');
    }

    /**
     * ลบประเภทตำแหน่ง
     */
    public function delete_position_type($type_id)
    {
        // ตรวจสอบพารามิเตอร์
        if (!$type_id) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        // ตรวจสอบว่าประเภทตำแหน่งมีอยู่จริง
        $existing_type = $this->db->get_where('tbl_position', ['pid' => $type_id])->row();
        if (!$existing_type) {
            $this->session->set_flashdata('save_error', TRUE);
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        // ตรวจสอบว่ามีข้อมูลบุคลากรหรือไม่
        $positions_count = $this->db->where('type_id', $type_id)->count_all_results('tbl_dynamic_positions');
        $filled_count = $this->dynamic_position_model->count_filled_slots($type_id);

        if ($filled_count > 0) {
            $this->session->set_flashdata(
                'error_message',
                'ไม่สามารถลบประเภทตำแหน่งได้ เนื่องจากมีข้อมูลบุคลากรอยู่ ' . $filled_count . ' คน กรุณาลบข้อมูลบุคลากรก่อน'
            );
            redirect('dynamic_position_backend', 'refresh');
            return;
        }

        $this->db->trans_start();

        try {
            // ลบไฟล์ทั้งหมดที่เกี่ยวข้อง
            $this->db->select('pf.*');
            $this->db->from('tbl_position_files pf');
            $this->db->join('tbl_dynamic_positions dp', 'pf.position_id = dp.position_id');
            $this->db->where('dp.type_id', $type_id);
            $files = $this->db->get()->result();

            foreach ($files as $file) {
                if (file_exists($file->file_path)) {
                    unlink($file->file_path);
                }
            }

            // ลบข้อมูลในลำดับที่ถูกต้อง (เพื่อหลีกเลี่ยง Foreign Key Constraint)

            // 1. ลบไฟล์จากฐานข้อมูล
            $this->db->query("DELETE pf FROM tbl_position_files pf 
                         INNER JOIN tbl_dynamic_positions dp ON pf.position_id = dp.position_id 
                         WHERE dp.type_id = ?", [$type_id]);

            // 2. ลบตำแหน่งทั้งหมด
            $this->db->delete('tbl_dynamic_positions', ['type_id' => $type_id]);

            // 3. ลบฟิลด์
            $this->db->delete('tbl_position_fields', ['type_id' => $type_id]);

            // 4. ลบประเภทตำแหน่ง
            $this->db->delete('tbl_position', ['pid' => $type_id]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $this->session->set_flashdata('del_success', TRUE);
                $this->log_activity('delete_type', $existing_type->peng, null, 'Deleted position type: ' . $existing_type->pname);

                // อัพเดตพื้นที่เก็บข้อมูล
                if (class_exists('Space_model')) {
                    $this->space_model->update_server_current();
                }
            } else {
                $this->session->set_flashdata('save_error', TRUE);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Delete position type failed: ' . $e->getMessage());
            $this->session->set_flashdata('save_error', TRUE);
        }

        redirect('dynamic_position_backend', 'refresh');
    }

    /**
     * แสดงรายการประเภทตำแหน่งทั้งหมด (รวมที่ซ่อน) สำหรับการจัดการ
     */
    public function manage_types()
    {
        // ตรวจสอบสิทธิ์ (เฉพาะ Super Admin)
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_error('Access Denied', 403);
            return;
        }

        $data['position_types'] = $this->dynamic_position_model->get_position_types(null); // ดึงทั้งหมด

        // เพิ่มข้อมูลสถิติ
        foreach ($data['position_types'] as &$type) {
            $positions = $this->dynamic_position_model->get_positions($type->pid);
            $type->total_positions = count($positions);
            $type->filled_positions = $this->dynamic_position_model->count_filled_slots($type->pid);

            // นับจำนวนไฟล์
            $this->db->select('COUNT(*) as file_count, SUM(pf.file_size) as total_size');
            $this->db->from('tbl_position_files pf');
            $this->db->join('tbl_dynamic_positions dp', 'pf.position_id = dp.position_id');
            $this->db->where('dp.type_id', $type->pid);
            $file_stats = $this->db->get()->row();

            $type->file_count = $file_stats->file_count ?? 0;
            $type->total_file_size = $file_stats->total_size ?? 0;
        }

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/manage_position_types', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ส่งออกข้อมูลประเภทตำแหน่งทั้งหมด
     */
    public function export_all_types()
    {
        // ตรวจสอบสิทธิ์
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_error('Access Denied', 403);
            return;
        }

        $this->load->library('excel');

        $position_types = $this->dynamic_position_model->get_position_types(null);

        // สร้าง Excel
        $excel = new PHPExcel();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('ประเภทตำแหน่งทั้งหมด');

        // Header
        $headers = ['ID', 'ชื่อประเภท (EN)', 'ชื่อแสดงผล', 'คำอธิบาย', 'ลำดับ', 'สถานะ', 'จำนวนตำแหน่ง', 'มีข้อมูล', 'วันที่สร้าง'];
        $col = 0;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col++, 1, $header);
        }

        // Style header
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('E2EFDA');

        // Data
        $row = 2;
        foreach ($position_types as $type) {
            $filled_positions = $this->dynamic_position_model->count_filled_slots($type->pid);

            $col = 0;
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->pid);
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->peng);
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->pname);
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->pdescription);
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->porder);
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->psub);
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->pstatus === 'show' ? 'แสดง' : 'ซ่อน');
            $sheet->setCellValueByColumnAndRow($col++, $row, 61);
            $sheet->setCellValueByColumnAndRow($col++, $row, $filled_positions);
            $sheet->setCellValueByColumnAndRow($col++, $row, $type->pcreate);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ส่งออกไฟล์
        $filename = 'position_types_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
    }

    /**
     * สำรองข้อมูลทั้งหมด
     */
    public function backup_data()
    {
        // ตรวจสอบสิทธิ์
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_error('Access Denied', 403);
            return;
        }

        $this->load->dbutil();

        $backup_path = './backups/';
        if (!is_dir($backup_path)) {
            mkdir($backup_path, 0755, true);
        }

        // สำรองข้อมูลตาราง
        $tables = [
            'tbl_position',
            'tbl_position_fields',
            'tbl_dynamic_positions',
            'tbl_position_files'
        ];

        $backup_data = '';
        foreach ($tables as $table) {
            $backup_data .= $this->dbutil->backup(array('tables' => array($table)));
        }

        $filename = 'position_backup_' . date('Y-m-d_H-i-s') . '.sql';

        if (write_file($backup_path . $filename, $backup_data)) {
            // ส่งไฟล์ให้ดาวน์โหลด
            $this->load->helper('download');
            force_download($filename, $backup_data);
        } else {
            $this->session->set_flashdata('error_message', 'ไม่สามารถสร้างไฟล์สำรองข้อมูลได้');
            redirect('dynamic_position_backend', 'refresh');
        }
    }

    /**
     * ตั้งค่าระบบ
     */
    public function system_settings()
    {
        // ตรวจสอบสิทธิ์
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_error('Access Denied', 403);
            return;
        }

        if ($this->input->method() === 'post') {
            // จัดการการบันทึกการตั้งค่า
            $settings = [
                'max_file_size' => $this->input->post('max_file_size'),
                'allowed_file_types' => $this->input->post('allowed_file_types'),
                'default_positions_per_type' => $this->input->post('default_positions_per_type') ?: 61,
                'enable_drag_drop' => $this->input->post('enable_drag_drop') ? 1 : 0,
                'auto_backup' => $this->input->post('auto_backup') ? 1 : 0
            ];

            // บันทึกการตั้งค่า (สามารถใช้ตารางแยกหรือไฟล์ config)
            foreach ($settings as $key => $value) {
                $this->db->replace('tbl_system_settings', [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->session->set_flashdata('save_success', TRUE);
            redirect('dynamic_position_backend/system_settings', 'refresh');
            return;
        }

        // โหลดการตั้งค่าปัจจุบัน
        $current_settings = [];
        $settings = $this->db->get('tbl_system_settings')->result();
        foreach ($settings as $setting) {
            $current_settings[$setting->setting_key] = $setting->setting_value;
        }

        $data['settings'] = $current_settings;
        $data['position_types_count'] = $this->db->count_all('tbl_position');
        $data['total_positions'] = $this->db->count_all('tbl_dynamic_positions');
        $data['total_files'] = $this->db->count_all('tbl_position_files');

        $this->load->view('templat/header');
        $this->load->view('asset/css');
        $this->load->view('templat/navbar_system_admin');
        $this->load->view('system_admin/system_settings', $data);
        $this->load->view('asset/js');
        $this->load->view('templat/footer');
    }

    /**
     * ลบข้อมูลหลายรายการพร้อมกัน
     */
    public function bulk_clear_slots($peng)
    {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        // ตรวจสอบสิทธิ์การเข้าถึง
        if (!$this->check_type_access($peng)) {
            $response = ['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึงข้อมูลนี้'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        // อ่านข้อมูล JSON
        $json_data = json_decode(file_get_contents('php://input'), true);

        if (!isset($json_data['slots']) || !is_array($json_data['slots'])) {
            $response = ['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $slots = $json_data['slots'];
        $deleted_count = 0;
        $failed_slots = [];

        // เริ่ม transaction
        $this->db->trans_start();

        try {
            foreach ($slots as $slot_id) {
                // ตรวจสอบ slot_id
                if ($slot_id < 1 || $slot_id > 61) {
                    $failed_slots[] = "Slot {$slot_id}: ตำแหน่งไม่ถูกต้อง";
                    continue;
                }

                // ดึงข้อมูลตำแหน่ง
                $position = $this->dynamic_position_model->get_position_by_slot($type->pid, $slot_id);
                if (!$position) {
                    $failed_slots[] = "Slot {$slot_id}: ไม่พบข้อมูลตำแหน่ง";
                    continue;
                }

                // ตรวจสอบว่ามีข้อมูลหรือไม่
                if ($this->dynamic_position_model->is_slot_empty($type->pid, $slot_id)) {
                    $failed_slots[] = "Slot {$slot_id}: ตำแหน่งนี้ว่างอยู่แล้ว";
                    continue;
                }

                // ลบไฟล์ทั้งหมดในตำแหน่งนี้
                $files = $this->dynamic_position_model->get_position_files($position->position_id);
                $deleted_files_count = 0;

                foreach ($files as $file) {
                    if (file_exists($file->file_path)) {
                        if (unlink($file->file_path)) {
                            $deleted_files_count++;
                        } else {
                            log_message('error', "Cannot delete file: {$file->file_path}");
                        }
                    } else {
                        log_message('warning', "File not found: {$file->file_path}");
                    }
                }

                // ลบข้อมูลไฟล์จากฐานข้อมูล
                $this->db->delete('tbl_position_files', ['position_id' => $position->position_id]);

                // เคลียร์ข้อมูลในตำแหน่ง (ไม่ลบ record แต่ทำให้เป็นค่าว่าง)
                $result = $this->dynamic_position_model->clear_position_slot($type->pid, $slot_id);

                if ($result) {
                    $deleted_count++;

                    // Log การดำเนินการ
                    $this->log_activity(
                        'bulk_clear_slot',
                        $peng,
                        $slot_id,
                        "Cleared slot {$slot_id}, deleted {$deleted_files_count} files"
                    );
                } else {
                    $failed_slots[] = "Slot {$slot_id}: ไม่สามารถลบข้อมูลได้";
                }
            }

            $this->db->trans_complete();

            // ตรวจสอบผลลัพธ์ transaction
            if ($this->db->trans_status() === false) {
                $response = [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการดำเนินการกับฐานข้อมูล',
                    'deleted_count' => 0,
                    'failed_slots' => array_merge($failed_slots, ['Database transaction failed'])
                ];
            } else {
                // อัพเดตข้อมูลพื้นที่เก็บข้อมูล
                if (class_exists('Space_model')) {
                    $this->space_model->update_server_current();
                }

                $response = [
                    'success' => true,
                    'message' => "ลบข้อมูลเรียบร้อยแล้ว {$deleted_count} รายการ",
                    'deleted_count' => $deleted_count,
                    'failed_count' => count($failed_slots),
                    'failed_slots' => $failed_slots
                ];

                // บันทึกสถิติการลบ
                log_message('info', "Bulk delete completed: {$deleted_count} slots cleared from {$type->pname} by " . $this->session->userdata('m_fname'));
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Bulk clear slots failed: ' . $e->getMessage());

            $response = [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage(),
                'deleted_count' => 0,
                'failed_slots' => array_merge($failed_slots, [$e->getMessage()])
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * ลบข้อมูลทั้งหมดในประเภทตำแหน่ง
     */
    public function clear_all_positions($peng)
    {
        // ตรวจสอบ AJAX request
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        // ตรวจสอบสิทธิ์การเข้าถึง (เฉพาะ Super Admin)
        if ($this->session->userdata('m_system') !== 'system_admin') {
            $response = ['success' => false, 'message' => 'ไม่มีสิทธิ์ดำเนินการนี้'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $this->db->trans_start();

        try {
            // ดึงรายการตำแหน่งทั้งหมดที่มีข้อมูล
            $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
            $cleared_count = 0;
            $deleted_files_count = 0;

            foreach ($positions as $position) {
                // ตรวจสอบว่ามีข้อมูลหรือไม่
                if (!$this->dynamic_position_model->is_slot_empty($type->pid, $position->position_order)) {

                    // ลบไฟล์ทั้งหมด
                    $files = $this->dynamic_position_model->get_position_files($position->position_id);
                    foreach ($files as $file) {
                        if (file_exists($file->file_path)) {
                            if (unlink($file->file_path)) {
                                $deleted_files_count++;
                            }
                        }
                    }

                    // ลบข้อมูลไฟล์จากฐานข้อมูล
                    $this->db->delete('tbl_position_files', ['position_id' => $position->position_id]);

                    // เคลียร์ข้อมูลในตำแหน่ง
                    $result = $this->dynamic_position_model->clear_position_slot($type->pid, $position->position_order);

                    if ($result) {
                        $cleared_count++;
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                $response = [
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการดำเนินการกับฐานข้อมูล'
                ];
            } else {
                // อัพเดตข้อมูลพื้นที่เก็บข้อมูล
                if (class_exists('Space_model')) {
                    $this->space_model->update_server_current();
                }

                $response = [
                    'success' => true,
                    'message' => "ลบข้อมูลทั้งหมดเรียบร้อยแล้ว",
                    'cleared_count' => $cleared_count,
                    'deleted_files_count' => $deleted_files_count
                ];

                // Log การดำเนินการ
                $this->log_activity(
                    'clear_all_positions',
                    $peng,
                    null,
                    "Cleared all {$cleared_count} positions, deleted {$deleted_files_count} files"
                );

                log_message('info', "Clear all positions completed: {$cleared_count} positions cleared from {$type->pname} by " . $this->session->userdata('m_fname'));
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Clear all positions failed: ' . $e->getMessage());

            $response = [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * ดึงข้อมูลสถิติการใช้งานแบบ real-time
     */
    public function get_usage_stats($peng)
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $stats = $this->dynamic_position_model->get_usage_statistics($type->pid);

        $response = [
            'success' => true,
            'stats' => $stats,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * ตรวจสอบการมีอยู่ของข้อมูลใน slots ที่ระบุ
     */
    public function check_slots_data($peng)
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $json_data = json_decode(file_get_contents('php://input'), true);

        if (!isset($json_data['slots']) || !is_array($json_data['slots'])) {
            $response = ['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $slots = $json_data['slots'];
        $slots_info = [];

        foreach ($slots as $slot_id) {
            if ($slot_id < 1 || $slot_id > 61) {
                continue;
            }

            $position = $this->dynamic_position_model->get_position_by_slot($type->pid, $slot_id);
            $is_empty = $this->dynamic_position_model->is_slot_empty($type->pid, $slot_id);

            $slots_info[$slot_id] = [
                'has_data' => !$is_empty,
                'position_id' => $position ? $position->position_id : null,
                'slot_number' => $slot_id
            ];

            // เพิ่มข้อมูลพื้นฐานถ้ามีข้อมูล
            if (!$is_empty && $position) {
                $data = $position->data;
                $slots_info[$slot_id]['preview'] = [
                    'name' => $data['name'] ?? 'ไม่ระบุชื่อ',
                    'position' => $data['position'] ?? '',
                    'has_image' => !empty($data['image'])
                ];
            }
        }

        $response = [
            'success' => true,
            'slots_info' => $slots_info
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * สำรองข้อมูลประเภทตำแหน่งเฉพาะ
     */
    public function backup_position_type($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        // ตรวจสอบสิทธิ์
        if (!$this->check_type_access($peng)) {
            show_error('Access Denied', 403);
            return;
        }

        $this->load->dbutil();
        $this->load->helper('download');
        $this->load->helper('file');

        try {
            // สร้างข้อมูล backup
            $backup_data = [
                'backup_info' => [
                    'type' => $type,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session->userdata('m_fname'),
                    'version' => '1.0'
                ],
                'positions' => [],
                'files_info' => []
            ];

            // ดึงข้อมูลตำแหน่งทั้งหมด
            $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
            foreach ($positions as $position) {
                if (!$this->dynamic_position_model->is_slot_empty($type->pid, $position->position_order)) {
                    $backup_data['positions'][] = [
                        'slot' => $position->position_order,
                        'data' => $position->data,
                        'files' => $this->dynamic_position_model->get_position_files($position->position_id)
                    ];
                }
            }

            // ดึงข้อมูลฟิลด์
            $backup_data['fields'] = $this->dynamic_position_model->get_position_fields($type->pid);

            // สร้างไฟล์ JSON
            $json_data = json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $filename = "backup_{$peng}_" . date('Y-m-d_H-i-s') . '.json';

            // ส่งไฟล์ให้ดาวน์โหลด
            force_download($filename, $json_data);

            // Log การสำรองข้อมูล
            $this->log_activity('backup_type', $peng, null, "Backup created: {$filename}");
        } catch (Exception $e) {
            log_message('error', 'Backup failed: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'ไม่สามารถสร้างไฟล์สำรองข้อมูลได้');
            redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
        }
    }

    /**
     * กู้คืนข้อมูลจากไฟล์สำรอง
     */
    public function restore_from_backup($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        // ตรวจสอบสิทธิ์ (เฉพาะ Super Admin)
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_error('Access Denied', 403);
            return;
        }

        if ($this->input->method() !== 'post') {
            redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
            return;
        }

        // จัดการ upload file
        $config['upload_path'] = './temp/';
        $config['allowed_types'] = 'json';
        $config['max_size'] = 10240; // 10MB

        if (!is_dir('./temp/')) {
            mkdir('./temp/', 0755, true);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('backup_file')) {
            $this->session->set_flashdata('error_message', $this->upload->display_errors());
            redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
            return;
        }

        $file_data = $this->upload->data();

        try {
            // อ่านไฟล์ JSON
            $json_content = file_get_contents($file_data['full_path']);
            $backup_data = json_decode($json_content, true);

            if (!$backup_data || !isset($backup_data['positions'])) {
                throw new Exception('ไฟล์สำรองข้อมูลไม่ถูกต้อง');
            }

            // ตรวจสอบความเข้ากันได้
            if ($backup_data['backup_info']['type']->peng !== $peng) {
                throw new Exception('ไฟล์สำรองข้อมูลไม่ตรงกับประเภทตำแหน่งนี้');
            }

            $this->db->trans_start();

            // ลบข้อมูลเดิมทั้งหมด
            $this->clear_all_positions_internal($type->pid);

            // กู้คืนข้อมูล
            $restored_count = 0;
            foreach ($backup_data['positions'] as $position_data) {
                $slot = $position_data['slot'];
                $data = $position_data['data'];

                // ไม่รวมไฟล์ในการกู้คืน (เพื่อความปลอดภัย)
                unset($data['image']); // หรือฟิลด์ไฟล์อื่นๆ

                $result = $this->dynamic_position_model->add_position_to_slot($type->pid, $slot, $data, []);
                if ($result) {
                    $restored_count++;
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('เกิดข้อผิดพลาดในการกู้คืนข้อมูล');
            }

            // Log การกู้คืนข้อมูล
            $this->log_activity('restore_backup', $peng, null, "Restored {$restored_count} positions from backup");

            $this->session->set_flashdata('save_success', TRUE);
            $this->session->set_flashdata('restore_info', "กู้คืนข้อมูลเรียบร้อยแล้ว {$restored_count} รายการ");
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Restore failed: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'การกู้คืนข้อมูลล้มเหลว: ' . $e->getMessage());
        } finally {
            // ลบไฟล์ temporary
            if (file_exists($file_data['full_path'])) {
                unlink($file_data['full_path']);
            }
        }

        redirect("dynamic_position_backend/manage/{$peng}", 'refresh');
    }

    /**
     * ลบข้อมูลทั้งหมดภายใน (สำหรับใช้ใน transaction)
     */
    private function clear_all_positions_internal($type_id)
    {
        // ดึงรายการไฟล์ทั้งหมด
        $this->db->select('pf.*');
        $this->db->from('tbl_position_files pf');
        $this->db->join('tbl_dynamic_positions dp', 'pf.position_id = dp.position_id');
        $this->db->where('dp.type_id', $type_id);
        $files = $this->db->get()->result();

        // ลบไฟล์จากระบบ
        foreach ($files as $file) {
            if (file_exists($file->file_path)) {
                unlink($file->file_path);
            }
        }

        // ลบข้อมูลไฟล์จากฐานข้อมูล
        $this->db->query("DELETE pf FROM tbl_position_files pf 
                     INNER JOIN tbl_dynamic_positions dp ON pf.position_id = dp.position_id 
                     WHERE dp.type_id = ?", [$type_id]);

        // เคลียร์ข้อมูลในตำแหน่งทั้งหมด
        $positions = $this->dynamic_position_model->get_all_61_positions($type_id);
        foreach ($positions as $position) {
            $this->dynamic_position_model->clear_position_slot($type_id, $position->position_order);
        }
    }

    /**
     * ตรวจสอบความเสียหายของข้อมูล
     */
    public function verify_data_integrity($peng)
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $issues = [];
        $stats = [
            'total_positions' => 0,
            'filled_positions' => 0,
            'empty_positions' => 0,
            'missing_files' => 0,
            'orphaned_files' => 0,
            'corrupted_data' => 0
        ];

        try {
            // ตรวจสอบตำแหน่งทั้งหมด
            $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
            $stats['total_positions'] = count($positions);

            foreach ($positions as $position) {
                $is_empty = $this->dynamic_position_model->is_slot_empty($type->pid, $position->position_order);

                if ($is_empty) {
                    $stats['empty_positions']++;
                } else {
                    $stats['filled_positions']++;

                    // ตรวจสอบข้อมูล JSON
                    try {
                        if (empty($position->position_data) || !is_array($position->data)) {
                            $issues[] = "Slot {$position->position_order}: ข้อมูล JSON เสียหาย";
                            $stats['corrupted_data']++;
                        }
                    } catch (Exception $e) {
                        $issues[] = "Slot {$position->position_order}: ไม่สามารถอ่านข้อมูล JSON ได้";
                        $stats['corrupted_data']++;
                    }

                    // ตรวจสอบไฟล์
                    $files = $this->dynamic_position_model->get_position_files($position->position_id);
                    foreach ($files as $file) {
                        if (!file_exists($file->file_path)) {
                            $issues[] = "Slot {$position->position_order}: ไม่พบไฟล์ {$file->file_name}";
                            $stats['missing_files']++;
                        }
                    }
                }
            }

            // ตรวจสอบไฟล์ที่ไม่มีการอ้างอิง
            $all_files = glob('./docs/img/*');
            $referenced_files = [];

            $this->db->select('file_name');
            $this->db->from('tbl_position_files pf');
            $this->db->join('tbl_dynamic_positions dp', 'pf.position_id = dp.position_id');
            $this->db->where('dp.type_id', $type->pid);
            $db_files = $this->db->get()->result();

            foreach ($db_files as $file) {
                $referenced_files[] = './docs/img/' . $file->file_name;
            }

            foreach ($all_files as $file) {
                if (is_file($file) && !in_array($file, $referenced_files)) {
                    // ตรวจสอบว่าไฟล์นี้เป็นของประเภทนี้หรือไม่ (จากชื่อไฟล์หรือวันที่)
                    $file_info = pathinfo($file);
                    if (preg_match('/^[a-f0-9]{32}/', $file_info['filename'])) { // encrypted filename
                        $stats['orphaned_files']++;
                    }
                }
            }

            $response = [
                'success' => true,
                'stats' => $stats,
                'issues' => $issues,
                'is_healthy' => empty($issues),
                'checked_at' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            log_message('error', 'Data integrity check failed: ' . $e->getMessage());
            $response = [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ: ' . $e->getMessage()
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * ซ่อมแซมข้อมูลที่เสียหาย
     */
    public function repair_data($peng)
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
            return;
        }

        // ตรวจสอบสิทธิ์ (เฉพาะ Super Admin)
        if ($this->session->userdata('m_system') !== 'system_admin') {
            $response = ['success' => false, 'message' => 'ไม่มีสิทธิ์ดำเนินการนี้'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            $response = ['success' => false, 'message' => 'ไม่พบประเภทตำแหน่ง'];
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $this->db->trans_start();
        $repair_actions = [];

        try {
            // ตรวจสอบและสร้างตำแหน่งให้ครบ 61 ช่อง
            $this->dynamic_position_model->ensure_61_positions($type->pid);
            $repair_actions[] = 'ตรวจสอบและสร้างตำแหน่งให้ครบ 61 ช่อง';

            // ลบข้อมูลไฟล์ที่ไม่มีไฟล์จริง
            $this->db->select('pf.*');
            $this->db->from('tbl_position_files pf');
            $this->db->join('tbl_dynamic_positions dp', 'pf.position_id = dp.position_id');
            $this->db->where('dp.type_id', $type->pid);
            $files = $this->db->get()->result();

            $removed_file_records = 0;
            foreach ($files as $file) {
                if (!file_exists($file->file_path)) {
                    $this->db->delete('tbl_position_files', ['file_id' => $file->file_id]);
                    $removed_file_records++;
                }
            }

            if ($removed_file_records > 0) {
                $repair_actions[] = "ลบข้อมูลไฟล์ที่ไม่มีไฟล์จริง {$removed_file_records} รายการ";
            }

            // ซ่อมแซมข้อมูล JSON ที่เสียหาย
            $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
            $repaired_positions = 0;

            foreach ($positions as $position) {
                try {
                    if (empty($position->position_data) || !is_array($position->data)) {
                        // สร้างข้อมูลว่างใหม่
                        $fields = $this->dynamic_position_model->get_position_fields($type->pid);
                        $empty_data = [];
                        foreach ($fields as $field) {
                            $empty_data[$field->field_name] = '';
                        }

                        $this->db->where('position_id', $position->position_id);
                        $this->db->update('tbl_dynamic_positions', [
                            'position_data' => json_encode($empty_data),
                            'updated_by' => 'system_repair',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        $repaired_positions++;
                    }
                } catch (Exception $e) {
                    log_message('error', "Failed to repair position {$position->position_id}: " . $e->getMessage());
                }
            }

            if ($repaired_positions > 0) {
                $repair_actions[] = "ซ่อมแซมข้อมูล JSON ที่เสียหาย {$repaired_positions} รายการ";
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('การซ่อมแซมข้อมูลล้มเหลว');
            }

            // Log การซ่อมแซม
            $this->log_activity('repair_data', $peng, null, 'Data repair completed: ' . implode(', ', $repair_actions));

            $response = [
                'success' => true,
                'message' => 'ซ่อมแซมข้อมูลเรียบร้อยแล้ว',
                'actions' => $repair_actions,
                'repaired_at' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Data repair failed: ' . $e->getMessage());

            $response = [
                'success' => false,
                'message' => 'การซ่อมแซมข้อมูลล้มเหลว: ' . $e->getMessage()
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * อัพเดต Log การดำเนินการ (ปรับปรุงจากเดิม)
     */
    private function log_activity($action, $peng, $slot_id = null, $details = null)
    {
        $log_data = [
            'user_id' => $this->session->userdata('m_id'),
            'user_name' => $this->session->userdata('m_fname'),
            'action' => $action,
            'peng' => $peng,
            'slot_id' => $slot_id,
            'details' => $details,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => substr($this->input->user_agent(), 0, 255), // ป้องกันข้อมูลยาวเกินไป
            'created_at' => date('Y-m-d H:i:s')
        ];

        // บันทึก log ลงฐานข้อมูล (ถ้ามีตาราง)
        try {
            if ($this->db->table_exists('tbl_activity_logs')) {
                $this->db->insert('tbl_activity_logs', $log_data);
            }
        } catch (Exception $e) {
            // ถ้าไม่สามารถบันทึกลงฐานข้อมูลได้ ให้ใช้ CI log
            log_message('info', 'Position Management Activity: ' . json_encode($log_data));
        }

        // บันทึกลง CI log เสมอ
        log_message('info', "Position Management - {$action}: User {$log_data['user_name']} on {$peng}" .
            ($slot_id ? " slot {$slot_id}" : '') .
            ($details ? " - {$details}" : ''));
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าถึงตามประเภทตำแหน่ง (ปรับปรุงจากเดิม)
     */
    private function check_type_access($peng)
    {
        $user_level = $this->session->userdata('m_system');

        // Super Admin เข้าถึงได้ทั้งหมด
        if ($user_level === 'system_admin') {
            return true;
        }

        // กำหนดสิทธิ์ตามประเภท (สามารถปรับแต่งได้)
        $access_rules = [
            'executives' => ['system_admin', 'hr_admin'],
            'faculty' => ['system_admin', 'academic_admin', 'hr_admin'],
            'staff' => ['system_admin', 'hr_admin'],
            'student_assistant' => ['system_admin', 'hr_admin', 'academic_admin']
        ];

        $allowed_levels = $access_rules[$peng] ?? ['system_admin'];

        return in_array($user_level, $allowed_levels);
    }

    /**
     * สร้างรายงานการใช้งานประจำ
     */
    public function export_csv($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
        $fields = $this->dynamic_position_model->get_position_fields($type->pid);

        // ตั้งค่า headers สำหรับดาวน์โหลด CSV
        $filename = $type->pname . '_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // เพิ่ม BOM สำหรับ UTF-8 (เพื่อให้ Excel เปิดภาษาไทยได้ถูกต้อง)
        echo "\xEF\xBB\xBF";

        // เปิด output stream
        $output = fopen('php://output', 'w');

        // สร้าง Header row
        $headers = ['Slot'];
        foreach ($fields as $field) {
            if ($field->field_type !== 'file') {
                $headers[] = $field->field_label;
            } else {
                $headers[] = $field->field_label . ' (ชื่อไฟล์)';
            }
        }
        fputcsv($output, $headers);

        // เขียนข้อมูล
        foreach ($positions as $position) {
            $row = [$position->position_order];

            foreach ($fields as $field) {
                $value = $position->data[$field->field_name] ?? '';

                // ทำความสะอาดข้อมูล
                if ($field->field_type === 'file' && !empty($value)) {
                    $row[] = $value; // แสดงชื่อไฟล์
                } else {
                    $row[] = $value;
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * สร้างรายงานการใช้งานเป็น CSV (แทน Excel)
     */
    public function generate_usage_report($peng = null)
    {
        // ตรวจสอบสิทธิ์
        if ($this->session->userdata('m_system') !== 'system_admin') {
            show_error('Access Denied', 403);
            return;
        }

        // ถ้าไม่ระบุประเภท ให้ทำรายงานทั้งหมด
        if ($peng) {
            $types = [$this->dynamic_position_model->get_position_type_by_name($peng)];
            $filename_prefix = $peng;
        } else {
            $types = $this->dynamic_position_model->get_position_types('show');
            $filename_prefix = 'all_types';
        }

        // ตั้งค่า headers สำหรับดาวน์โหลด CSV
        $filename = 'usage_report_' . $filename_prefix . '_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // เพิ่ม BOM สำหรับ UTF-8
        echo "\xEF\xBB\xBF";

        // เปิด output stream
        $output = fopen('php://output', 'w');

        // Header
        $headers = ['ประเภทตำแหน่ง', 'ตำแหน่งทั้งหมด', 'มีข้อมูล', 'ว่าง', 'อัตราการใช้งาน (%)', 'จำนวนไฟล์', 'ขนาดไฟล์ (MB)'];
        fputcsv($output, $headers);

        // Data
        $total_stats = ['total' => 0, 'filled' => 0, 'files' => 0, 'size' => 0];

        foreach ($types as $type) {
            if (!$type) continue;

            $stats = $this->dynamic_position_model->get_usage_statistics($type->pid);

            // นับไฟล์และขนาด
            $this->db->select('COUNT(*) as file_count, COALESCE(SUM(file_size), 0) as total_size');
            $this->db->from('tbl_position_files pf');
            $this->db->join('tbl_dynamic_positions dp', 'pf.position_id = dp.position_id');
            $this->db->where('dp.type_id', $type->pid);
            $file_stats = $this->db->get()->row();

            $row = [
                $type->pname,
                $stats['total'],
                $stats['filled'],
                $stats['empty'],
                $stats['percentage'],
                $file_stats->file_count,
                round($file_stats->total_size / (1024 * 1024), 2)
            ];

            fputcsv($output, $row);

            // รวมสถิติ
            $total_stats['total'] += $stats['total'];
            $total_stats['filled'] += $stats['filled'];
            $total_stats['files'] += $file_stats->file_count;
            $total_stats['size'] += $file_stats->total_size;
        }

        // แถวสรุป
        if (count($types) > 1) {
            $summary_row = [
                'รวมทั้งหมด',
                $total_stats['total'],
                $total_stats['filled'],
                $total_stats['total'] - $total_stats['filled'],
                $total_stats['total'] > 0 ? round(($total_stats['filled'] / $total_stats['total']) * 100, 1) : 0,
                $total_stats['files'],
                round($total_stats['size'] / (1024 * 1024), 2)
            ];

            fputcsv($output, $summary_row);
        }

        fclose($output);
        exit;
    }

    /**
     * ส่งออกข้อมูลเป็น JSON (สำหรับการสำรองข้อมูลแบบสมบูรณ์)
     */
    public function export_json($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        // ดึงข้อมูลทั้งหมด
        $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
        $fields = $this->dynamic_position_model->get_position_fields($type->pid);

        // สร้างข้อมูลสำหรับ export
        $export_data = [
            'export_info' => [
                'type' => $type,
                'exported_at' => date('Y-m-d H:i:s'),
                'exported_by' => $this->session->userdata('m_fname'),
                'version' => '1.0',
                'total_positions' => count($positions),
                'filled_positions' => $this->dynamic_position_model->count_filled_slots($type->pid)
            ],
            'fields' => $fields,
            'positions' => []
        ];

        // เพิ่มข้อมูลตำแหน่ง
        foreach ($positions as $position) {
            $position_data = [
                'position_id' => $position->position_id,
                'position_order' => $position->position_order,
                'position_row' => $position->position_row,
                'position_column' => $position->position_column,
                'data' => $position->data,
                'created_at' => $position->created_at,
                'updated_at' => $position->updated_at
            ];

            // เพิ่มข้อมูลไฟล์ (ไม่รวมไฟล์จริง เฉพาะข้อมูล)
            $files = $this->dynamic_position_model->get_position_files($position->position_id);
            $position_data['files'] = [];
            foreach ($files as $file) {
                $position_data['files'][] = [
                    'field_name' => $file->field_name,
                    'file_name' => $file->file_name,
                    'file_original_name' => $file->file_original_name,
                    'file_size' => $file->file_size,
                    'file_type' => $file->file_type,
                    'uploaded_at' => $file->uploaded_at
                ];
            }

            $export_data['positions'][] = $position_data;
        }

        // ตั้งค่า headers
        $filename = $type->pname . '_complete_' . date('Y-m-d_H-i-s') . '.json';

        header('Content-Type: application/json; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // ส่งออกข้อมูล
        echo json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * ส่งออกข้อมูลแบบ HTML Table (สำหรับพิมพ์)
     */
    public function export_html($peng)
    {
        $type = $this->dynamic_position_model->get_position_type_by_name($peng);
        if (!$type) {
            show_404();
            return;
        }

        $positions = $this->dynamic_position_model->get_all_61_positions($type->pid);
        $fields = $this->dynamic_position_model->get_position_fields($type->pid);
        $filled_count = $this->dynamic_position_model->count_filled_slots($type->pid);

        // สร้าง HTML
        $html = '<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงาน' . htmlspecialchars($type->pname) . '</title>
    <style>
        body { font-family: "Sarabun", Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .empty-slot { color: #6c757d; font-style: italic; }
        .export-info { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #6c757d; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>รายงานข้อมูล' . htmlspecialchars($type->pname) . '</h1>
        <p>วันที่พิมพ์: ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <div class="stats">
        <strong>สถิติการใช้งาน:</strong>
        ตำแหน่งทั้งหมด: 61 ช่อง | 
        มีข้อมูล: ' . $filled_count . ' ช่อง | 
        ว่าง: ' . (61 - $filled_count) . ' ช่อง | 
        อัตราการใช้งาน: ' . round(($filled_count / 61) * 100, 1) . '%
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Slot</th>';

        foreach ($fields as $field) {
            if ($field->field_type !== 'file') {
                $html .= '<th>' . htmlspecialchars($field->field_label) . '</th>';
            }
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        foreach ($positions as $position) {
            $html .= '<tr>';
            $html .= '<td>' . $position->position_order . '</td>';

            $is_empty = true;
            foreach ($position->data as $value) {
                if (!empty(trim($value))) {
                    $is_empty = false;
                    break;
                }
            }

            foreach ($fields as $field) {
                if ($field->field_type !== 'file') {
                    $value = $position->data[$field->field_name] ?? '';
                    if (empty(trim($value)) && $is_empty) {
                        $html .= '<td class="empty-slot">-</td>';
                    } else {
                        $html .= '<td>' . htmlspecialchars($value) . '</td>';
                    }
                }
            }

            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>
    
    <div class="export-info">
        <p>รายงานนี้สร้างโดยระบบจัดการข้อมูลบุคลากร | ส่งออกโดย: ' . htmlspecialchars($this->session->userdata('m_fname')) . '</p>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            พิมพ์รายงาน
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ปิด
        </button>
    </div>
</body>
</html>';

        echo $html;
    }
}
