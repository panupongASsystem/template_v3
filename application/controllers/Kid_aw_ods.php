<?php
// ===================================================================
// Kid_aw_ods Controller - ระบบเงินสนับสนุนเด็กแรกเกิด
// แก้ไขปัญหาการโหลด Model
// ===================================================================

class Kid_aw_ods extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // ป้องกันการแคชและการคัดลอกเนื้อหา
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('X-Frame-Options: DENY');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $this->output->set_header('X-XSS-Protection: 1; mode=block');
        $this->output->set_header('Referrer-Policy: same-origin');
        $this->output->set_header('Content-Disposition: inline');
        $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        // โหลด models ทั้งหมด
        $models_to_load = [
            'activity_model',
            'news_model',
            'announce_model',
            'order_model',
            'procurement_model',
            'mui_model',
            'guide_work_model',
            'loadform_model',
            'pppw_model',
            'msg_pres_model',
            'history_model',
            'otop_model',
            'gci_model',
            'vision_model',
            'authority_model',
            'mission_model',
            'motto_model',
            'cmi_model',
            'executivepolicy_model',
            'travel_model',
            'si_model',
            'HotNews_model',
            'Weather_report_model',
            'calender_model',
            'banner_model',
            'background_personnel_model',
            'member_public_model'
        ];

        foreach ($models_to_load as $model) {
            if (file_exists(APPPATH . 'models/' . ucfirst($model) . '.php')) {
                $this->load->model($model);
            }
        }

        // *** แก้ไข: โหลด Kid_aw_ods_model ด้วยชื่อที่ถูกต้อง ***
        $this->load->model('Kid_aw_ods_model');

        // *** เพิ่ม: ตรวจสอบว่าโหลดสำเร็จหรือไม่ ***
        if (!isset($this->Kid_aw_ods_model)) {
            log_message('error', 'Failed to load Kid_aw_ods_model');
            show_error('Model loading failed', 500);
        }

        log_message('debug', 'Kid_aw_ods_model loaded successfully');

        $this->load->library('recaptcha_lib');
        if (file_exists(APPPATH . 'config/recaptcha.php')) {
            $this->load->config('recaptcha');
            $recaptcha_config = $this->config->item('recaptcha');

            if ($recaptcha_config) {
                $this->recaptcha_lib->initialize($recaptcha_config);
                log_message('debug', 'reCAPTCHA Library initialized with config file');
            }
        }

        // โหลด LINE Notification Library
        $this->load->library('line_notification');

    }


    /**
     * *** แก้ไขการตรวจสอบ Login และส่งข้อมูลไป View ***
     */
    private function get_current_user_detailed()
    {
        // ตั้งค่าเริ่มต้น
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];

        try {
            // ตรวจสอบ session
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');
            $m_id = $this->session->userdata('m_id');
            $m_email = $this->session->userdata('m_email');

            // ตรวจสอบ Public User ก่อน
            if (!empty($mp_id) && !empty($mp_email)) {
                $public_user = $this->get_public_user_data($mp_id, $mp_email);

                if ($public_user) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_info'] = $public_user['user_info'];
                    $user_info['user_address'] = $public_user['user_address'];

                    return $user_info;
                }
            }

            // ตรวจสอบ Staff User
            if (!empty($m_id) && !empty($m_email)) {
                $staff_user = $this->get_staff_user_data($m_id, $m_email);

                if ($staff_user) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_info'] = $staff_user['user_info'];

                    return $user_info;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error in get_current_user_detailed: ' . $e->getMessage());
        }

        return $user_info;
    }

    /**
     * *** ฟังก์ชันตรวจสอบ Public User ***
     */
    private function get_public_user_data($mp_id, $mp_email)
    {
        try {
            $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_number, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                return null;
            }

            $user_info = [
                'id' => $user_data->id,
                'mp_id' => $user_data->mp_id,
                'name' => trim(($user_data->mp_prefix ? $user_data->mp_prefix . ' ' : '') . $user_data->mp_fname . ' ' . $user_data->mp_lname),
                'prefix' => $user_data->mp_prefix ?: '',
                'fname' => $user_data->mp_fname ?: '',
                'lname' => $user_data->mp_lname ?: '',
                'phone' => $user_data->mp_phone ?: '',
                'email' => $user_data->mp_email ?: '',
                'number' => $user_data->mp_number ?: ''
            ];

            $user_address = null;
            if (!empty($user_data->mp_address) || !empty($user_data->mp_district)) {
                $full_address_parts = array_filter([
                    $user_data->mp_address,
                    $user_data->mp_district ? 'ตำบล' . $user_data->mp_district : '',
                    $user_data->mp_amphoe ? 'อำเภอ' . $user_data->mp_amphoe : '',
                    $user_data->mp_province ? 'จังหวัด' . $user_data->mp_province : '',
                    $user_data->mp_zipcode ?: ''
                ]);

                $full_address = implode(' ', $full_address_parts);

                $user_address = [
                    'additional_address' => $user_data->mp_address ?: '',
                    'district' => $user_data->mp_district ?: '',
                    'amphoe' => $user_data->mp_amphoe ?: '',
                    'province' => $user_data->mp_province ?: '',
                    'zipcode' => $user_data->mp_zipcode ?: '',
                    'full_address' => $full_address,
                    'parsed' => [
                        'additional_address' => $user_data->mp_address ?: '',
                        'district' => $user_data->mp_district ?: '',
                        'amphoe' => $user_data->mp_amphoe ?: '',
                        'province' => $user_data->mp_province ?: '',
                        'zipcode' => $user_data->mp_zipcode ?: '',
                        'full_address' => $full_address
                    ]
                ];
            }

            return [
                'user_info' => $user_info,
                'user_address' => $user_address
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in get_public_user_data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * *** ฟังก์ชันตรวจสอบ Staff User ***
     */
    private function get_staff_user_data($m_id, $m_email)
    {
        try {
            $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system, m_img, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_email', $m_email);
            $this->db->where('m_status', '1');
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                return null;
            }

            $user_info = [
                'id' => $user_data->m_id,
                'm_id' => $user_data->m_id,
                'name' => trim($user_data->m_fname . ' ' . $user_data->m_lname),
                'fname' => $user_data->m_fname ?: '',
                'lname' => $user_data->m_lname ?: '',
                'phone' => $user_data->m_phone ?: '',
                'email' => $user_data->m_email ?: '',
                'm_system' => $user_data->m_system ?: '',
                'm_img' => $user_data->m_img ?: ''
            ];

            return [
                'user_info' => $user_info,
                'user_address' => null
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in get_staff_user_data: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * *** แก้ไขฟังก์ชัน adding_kid_aw_ods - ส่งข้อมูลไป View อย่างชัดเจน ***
     */
    public function adding_kid_aw_ods()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ redirect และ parameter
            $from_login = $this->input->get('from_login');
            $redirect_url = $this->input->get('redirect');

            $data['from_login'] = ($from_login === 'success');

            if ($redirect_url) {
                $this->session->set_userdata('redirect_after_login', $redirect_url);
                log_message('info', 'Redirect URL saved: ' . $redirect_url);
            }

            // *** ตรวจสอบสถานะ User Login ***
            $current_user = $this->get_current_user_detailed();

            // *** กำหนดข้อมูลสำหรับส่งไป View อย่างชัดเจน ***
            $data['is_logged_in'] = $current_user['is_logged_in'];
            $data['user_info'] = $current_user['user_info'];
            $data['user_type'] = $current_user['user_type'];
            $data['user_address'] = $current_user['user_address'];

            // *** แก้ไข: ดึงข้อมูลแบบฟอร์มพร้อมการตรวจสอบสถานะเข้มงวด ***
            $data['kid_aw_form'] = $this->get_active_forms();

            // *** เพิ่มข้อมูลสถิติแบบฟอร์ม ***
            $data['forms_statistics'] = [
                'active_count' => count($data['kid_aw_form']),
                'inactive_count' => 0,
                'total_count' => count($data['kid_aw_form']),
                'last_updated' => date('Y-m-d H:i:s')
            ];

            // *** ข้อมูลเพิ่มเติมสำหรับหน้า ***
            $data['page_title'] = 'ยื่นเรื่องเงินสนับสนุนเด็กแรกเกิด';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'เงินสนับสนุนเด็กแรกเกิด', 'url' => '']
            ];

            // *** Flash Messages ***
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // *** เพิ่มข้อมูลการแจ้งเตือนเกี่ยวกับแบบฟอร์ม ***
            if (count($data['kid_aw_form']) === 0) {
                $data['warning_message'] = $data['warning_message'] ?: 'ขณะนี้ไม่มีแบบฟอร์มที่เปิดใช้งาน กรุณาติดต่อเจ้าหน้าที่';
                log_message('info', 'No active forms available for download');
            }

            // *** การตั้งค่า Cache Control ***
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            $this->output->set_header('Pragma: no-cache');
            $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

            log_message('info', 'Loading kid_aw_ods view with user type: ' . $data['user_type'] . ', Active forms: ' . count($data['kid_aw_form']));

            // *** โหลด view พร้อมส่งข้อมูลไป ***
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/kid_aw_ods', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

            log_message('info', 'Kid_aw_ods view loaded successfully');

        } catch (Exception $e) {
            log_message('error', 'Critical error in adding_kid_aw_ods: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ายื่นเรื่องเงินสนับสนุนเด็ก: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    /**
     * หน้าจัดการเงินสนับสนุนเด็ก (สำหรับ Staff)
     */
    public function kid_aw_ods()
    {
        try {
            // *** แก้ไข: ตรวจสอบเฉพาะ Staff login แต่ไม่เช็คสิทธิ์ในการเข้าหน้า ***
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ และดึงข้อมูลระดับสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id, m_email, m_phone, m_username, m_img');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1'); // ต้องเป็น active เท่านั้น
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
                redirect('User');
                return;
            }

            // *** แก้ไข: ตรวจสอบสิทธิ์สำหรับการ Update/Delete เท่านั้น ***
            $can_update_status = $this->check_kid_update_permission($staff_check);
            $can_delete_kid = $this->check_kid_delete_permission($staff_check);

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_update_status'] = $can_update_status;
            $data['can_delete_kid'] = $can_delete_kid;
            $data['can_handle_kid'] = true; // *** ทุกคนดูได้ ***
            $data['can_approve_kid'] = $can_update_status; // *** ใช้สิทธิ์เดียวกับ update ***
            $data['staff_system_level'] = $staff_check->m_system;

            // ตัวกรองข้อมูล
            $filters = [
                'status' => $this->input->get('status'),
                'type' => $this->input->get('type'),
                'priority' => $this->input->get('priority'),
                'user_type' => $this->input->get('user_type'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search'),
                'assigned_to' => $this->input->get('assigned_to')
            ];

            // Pagination
            $this->load->library('pagination');
            $per_page = 20;
            $current_page = (int) ($this->input->get('page') ?? 1);
            $offset = ($current_page - 1) * $per_page;

            // ดึงข้อมูลเงินสนับสนุนเด็กพร้อมกรอง
            $kid_result = $this->Kid_aw_ods_model->get_kid_aw_ods_with_filters($filters, $per_page, $offset);
            $kid_aw_ods = $kid_result['data'] ?? [];
            $total_rows = $kid_result['total'] ?? 0;

            // *** แก้ไข: ตรวจสอบและเติมข้อมูลที่ขาดหาย ***
            if (!empty($kid_aw_ods)) {
                foreach ($kid_aw_ods as $index => $kid) {
                    // เติมข้อมูลที่ขาดหาย
                    $kid_aw_ods[$index] = $this->ensure_kid_data_completeness($kid);

                    // ดึงไฟล์และประวัติ
                    $kid_aw_ods[$index]->files = $this->Kid_aw_ods_model->get_kid_aw_ods_files($kid->kid_aw_ods_id);
                    $kid_aw_ods[$index]->history = $this->Kid_aw_ods_model->get_kid_aw_ods_history($kid->kid_aw_ods_id);

                    // ดึงข้อมูลเจ้าหน้าที่ที่รับผิดชอบ (ถ้ามี)
                    if (!empty($kid_aw_ods[$index]->kid_aw_ods_assigned_to)) {
                        $this->db->select('m_fname, m_lname, m_system');
                        $this->db->from('tbl_member');
                        $this->db->where('m_id', $kid_aw_ods[$index]->kid_aw_ods_assigned_to);
                        $assigned_staff = $this->db->get()->row();

                        if ($assigned_staff) {
                            $kid_aw_ods[$index]->assigned_staff_name = trim($assigned_staff->m_fname . ' ' . $assigned_staff->m_lname);
                            $kid_aw_ods[$index]->assigned_staff_system = $assigned_staff->m_system;
                        }
                    }
                }
            }

            // เตรียมข้อมูลสำหรับแสดงผล
            $data['kid_aw_ods'] = $this->prepare_kid_aw_ods_list_for_display($kid_aw_ods);

            // สถิติเงินสนับสนุนเด็ก
            $kid_summary = $this->Kid_aw_ods_model->get_kid_aw_ods_statistics();
            $data['kid_summary'] = $kid_summary;
            $data['status_counts'] = $this->calculate_kid_aw_ods_status_counts($data['kid_aw_ods']);

            // ตัวเลือกสำหรับ Filter
            $status_options = [
                ['value' => 'submitted', 'label' => 'ยื่นเรื่องแล้ว'],
                ['value' => 'reviewing', 'label' => 'กำลังพิจารณา'],
                ['value' => 'approved', 'label' => 'อนุมัติแล้ว'],
                ['value' => 'rejected', 'label' => 'ไม่อนุมัติ'],
                ['value' => 'completed', 'label' => 'เสร็จสิ้น']
            ];

            $type_options = [
                ['value' => 'children', 'label' => 'เด็กทั่วไป'],
                ['value' => 'disabled', 'label' => 'เด็กพิการ']
            ];

            $priority_options = [
                ['value' => 'low', 'label' => 'ต่ำ'],
                ['value' => 'normal', 'label' => 'ปกติ'],
                ['value' => 'high', 'label' => 'สูง'],
                ['value' => 'urgent', 'label' => 'เร่งด่วน']
            ];

            $user_type_options = [
                ['value' => 'guest', 'label' => 'ผู้ใช้ทั่วไป (Guest)'],
                ['value' => 'public', 'label' => 'สมาชิก (Public)'],
                ['value' => 'staff', 'label' => 'เจ้าหน้าที่ (Staff)']
            ];

            // รายการเงินสนับสนุนเด็กล่าสุด
            $recent_kid = $this->Kid_aw_ods_model->get_recent_kid_aw_ods(10);

            // รายการเจ้าหน้าที่สำหรับ Assignment
            $staff_list = $this->get_staff_list_for_assignment();

            // Pagination Setup
            $pagination_config = [
                'base_url' => site_url('Kid_aw_ods/kid_aw_ods'),
                'total_rows' => $total_rows,
                'per_page' => $per_page,
                'page_query_string' => TRUE,
                'query_string_segment' => 'page',
                'reuse_query_string' => TRUE,
                'num_links' => 3,
                'use_page_numbers' => TRUE,
                'cur_tag_open' => '<span class="page-link bg-primary text-white border-primary">',
                'cur_tag_close' => '</span>',
                'num_tag_open' => '<span class="page-link">',
                'num_tag_close' => '</span>',
                'prev_link' => '<i class="fas fa-chevron-left"></i> ก่อนหน้า',
                'next_link' => 'ถัดไป <i class="fas fa-chevron-right"></i>',
                'attributes' => ['class' => 'page-item']
            ];

            $this->pagination->initialize($pagination_config);

            // *** สร้าง user_info object ที่ครบถ้วนสำหรับ header.php ***
            $user_info_object = $this->create_complete_user_info($staff_check);

            // รวมข้อมูลทั้งหมด
            $data = array_merge($data, [
                'recent_kid' => $recent_kid,
                'staff_list' => $staff_list,
                'filters' => $filters,
                'status_options' => $status_options,
                'type_options' => $type_options,
                'priority_options' => $priority_options,
                'user_type_options' => $user_type_options,
                'total_rows' => $total_rows,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'pagination' => $this->pagination->create_links(),
                'staff_info' => [
                    'id' => $staff_check->m_id,
                    'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                    'system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_kid'],
                    'can_handle' => $data['can_handle_kid'],
                    'can_approve' => $data['can_approve_kid'],
                    'can_update_status' => $data['can_update_status']
                ],
                'is_logged_in' => true,
                'user_type' => 'staff',
                'user_info' => $user_info_object,

                // *** เพิ่มตัวแปรเสริมสำหรับ header.php ***
                'current_user' => $user_info_object,
                'logged_user' => $user_info_object,
                'session_user' => $user_info_object,
                'staff_data' => $user_info_object,
                'member_data' => $user_info_object,
            ]);

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Dashboard')],
                ['title' => 'จัดการเงินสนับสนุนเด็ก', 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'จัดการเงินสนับสนุนเด็กแรกเกิด';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Kid AW ODS management final data: ' . json_encode([
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_kid'],
                    'can_handle' => $data['can_handle_kid'],
                    'can_approve' => $data['can_approve_kid'],
                    'can_update_status' => $data['can_update_status'],
                    'grant_user_ref_id' => $staff_check->grant_user_ref_id,
                    'kid_count' => count($data['kid_aw_ods']),
                    'total_rows' => $total_rows,
                    'filters_applied' => array_filter($filters),
                    'user_info_properties' => get_object_vars($user_info_object),
                    'user_info_has_pname' => property_exists($user_info_object, 'pname'),
                    'user_info_has_kid_number' => property_exists($user_info_object, 'kid_aw_ods_number')
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/kid_aw_ods', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in kid_aw_ods management: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Dashboard');
            }
        }
    }

    /**
     * *** ใหม่: ฟังก์ชันเช็คสิทธิ์การ Update สถานะ ***
     */
    private function check_kid_update_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถ update ได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Kid update permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 53 (สำหรับเงินสนับสนุนเด็ก)
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('warning', "user_admin without grant_user_ref_id: {$staff_data->m_fname} {$staff_data->m_lname}");
                    return false;
                }

                // *** แก้ไข: เช็คที่ grant_user_ref_id โดยตรง ***
                // แปลง grant_user_ref_id เป็น array (กรณีมีหลายสิทธิ์คั่นด้วย comma)
                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));

                // เช็คว่ามีสิทธิ์ 53 หรือไม่ (สำหรับเงินสนับสนุนเด็ก)
                $has_permission = in_array('53', $grant_ids);

                log_message('info', "user_admin kid update permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "grant_user_ref_id: {$staff_data->grant_user_ref_id} - " .
                    "grant_ids: " . implode(',', $grant_ids) . " - " .
                    "has_53: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            // อื่นๆ ไม่สามารถ update ได้
            log_message('info', "Kid update permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking kid update permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ใหม่: ฟังก์ชันเช็คสิทธิ์การลบ ***
     */
    private function check_kid_delete_permission($staff_data)
    {
        try {
            // เฉพาะ system_admin และ super_admin ที่สามารถลบได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Kid delete permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // อื่นๆ ลบไม่ได้
            log_message('info', "Kid delete permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking kid delete permission: ' . $e->getMessage());
            return false;
        }
    }

    private function create_complete_user_info($staff_check)
    {
        $user_info_object = new stdClass();

        // ข้อมูลพื้นฐาน
        $user_info_object->m_id = $staff_check->m_id;
        $user_info_object->m_fname = $staff_check->m_fname ?? '';
        $user_info_object->m_lname = $staff_check->m_lname ?? '';
        $user_info_object->m_email = $staff_check->m_email ?? $this->session->userdata('m_email') ?? '';
        $user_info_object->m_phone = $staff_check->m_phone ?? '';
        $user_info_object->m_system = $staff_check->m_system ?? '';
        $user_info_object->m_username = $staff_check->m_username ?? '';
        $user_info_object->m_img = $staff_check->m_img ?? '';

        // ข้อมูลรวม
        $full_name = trim(($user_info_object->m_fname ?? '') . ' ' . ($user_info_object->m_lname ?? ''));
        $user_info_object->name = $full_name ?: 'ผู้ใช้ระบบ';
        $user_info_object->email = $user_info_object->m_email;
        $user_info_object->phone = $user_info_object->m_phone;
        $user_info_object->fname = $user_info_object->m_fname;
        $user_info_object->lname = $user_info_object->m_lname;
        $user_info_object->id = $user_info_object->m_id;

        // *** Properties ที่ header.php ต้องการ ***
        $user_info_object->pname = $user_info_object->name;
        $user_info_object->fullname = $user_info_object->name;
        $user_info_object->display_name = $user_info_object->name;
        $user_info_object->username = $user_info_object->m_username;
        $user_info_object->position = $user_info_object->m_system;
        $user_info_object->department = 'เจ้าหน้าที่ระบบ';
        $user_info_object->role = $user_info_object->m_system;
        $user_info_object->level = $user_info_object->m_system;

        // ข้อมูลเพิ่มเติม
        $user_info_object->avatar = $user_info_object->m_img;
        $user_info_object->profile_image = $user_info_object->m_img;
        $user_info_object->is_online = true;
        $user_info_object->last_login = date('Y-m-d H:i:s');
        $user_info_object->status = 'active';
        $user_info_object->permissions = $staff_check->grant_user_ref_id ?? '';

        // ข้อมูลสำหรับ staff
        $user_info_object->staff_id = $user_info_object->m_id;
        $user_info_object->staff_name = $user_info_object->name;
        $user_info_object->staff_position = $user_info_object->m_system;
        $user_info_object->staff_department = 'เจ้าหน้าที่ระบบ';
        $user_info_object->staff_level = $user_info_object->m_system;

        return $user_info_object;
    }

    /**
     * Helper function: แปลง array เป็น object
     */
    private function array_to_object($array)
    {
        if (is_array($array)) {
            $object = new stdClass();
            foreach ($array as $key => $value) {
                $object->$key = is_array($value) ? $this->array_to_object($value) : $value;
            }
            return $object;
        }
        return $array;
    }

    /**
     * Helper function: ตรวจสอบและแปลง user_info ให้เป็น object
     */
    private function ensure_user_info_object($user_info)
    {
        if (is_array($user_info)) {
            return $this->array_to_object($user_info);
        }

        if (is_object($user_info)) {
            return $user_info;
        }

        // ถ้าไม่ใช่ทั้ง array และ object ให้สร้าง empty object
        return new stdClass();
    }

    /**
     * หน้าหลักยื่นเรื่องเงินสนับสนุนเด็ก (สำหรับ public/guest เท่านั้น)
     */
    public function index()
    {
        // ตรวจสอบว่าเป็น staff user หรือไม่
        if ($this->is_staff_user()) {
            $this->show_staff_redirect_warning();
            return;
        }

        try {
            // เตรียมข้อมูลสำหรับ view
            $data = $this->prepare_page_data();

            // ดึงข้อมูล user ที่ login (ถ้ามี)
            $current_user = $this->get_current_user_info();
            $data['is_logged_in'] = $current_user['is_logged_in'];
            $data['user_info'] = $current_user['user_info'];
            $data['user_type'] = $current_user['user_type'];
            $data['user_address'] = $current_user['user_address'];

            // ดึงฟอร์มที่เปิดใช้งาน
            $data['kid_aw_form'] = $this->get_active_forms();

            // ข้อมูลหน้า
            $data['page_title'] = 'ยื่นเรื่องเงินสนับสนุนเด็กแรกเกิด';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'เงินสนับสนุนเด็กแรกเกิด', 'url' => '']
            ];

            // Flash messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/kid_aw_ods', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in Kid_aw_ods/index: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการโหลดหน้า', 500);
        }
    }

    /**
     * ยื่นเรื่องเงินสนับสนุนเด็ก (AJAX)
     */
    public function add_kid_aw_ods()
    {
        // *** เพิ่ม: Log debug สำหรับ reCAPTCHA ***
        log_message('info', '=== KID AW ODS SUBMIT START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));
        log_message('info', 'User Agent: ' . $this->input->server('HTTP_USER_AGENT'));

        // *** ป้องกัน output buffer ***
        while (ob_get_level()) {
            ob_end_clean();
        }

        try {
            // *** ตรวจสอบ HTTP method ***
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // *** เพิ่ม: ตรวจสอบ reCAPTCHA token ***
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action');
            $recaptcha_source = $this->input->post('recaptcha_source');
            $user_type_detected = $this->input->post('user_type_detected');
            $is_ajax = $this->input->post('ajax_request') === '1';
            $dev_mode = $this->input->post('dev_mode') === '1';

            log_message('info', 'reCAPTCHA info: ' . json_encode([
                'has_token' => !empty($recaptcha_token),
                'token_length' => !empty($recaptcha_token) ? strlen($recaptcha_token) : 0,
                'action' => $recaptcha_action ?: 'not_set',
                'source' => $recaptcha_source ?: 'not_set',
                'user_type_detected' => $user_type_detected ?: 'not_set',
                'is_ajax' => $is_ajax,
                'dev_mode' => $dev_mode
            ]));

            // *** เพิ่ม: ตรวจสอบ reCAPTCHA (ถ้ามี token) ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                log_message('info', 'Starting reCAPTCHA verification for kid aw ods submit');

                try {
                    // *** ใช้ reCAPTCHA Library ที่มีอยู่ ***
                    $recaptcha_options = [
                        'action' => $recaptcha_action ?: 'kid_aw_ods_submit',
                        'source' => $recaptcha_source ?: 'kid_aw_ods_form',
                        'user_type_detected' => $user_type_detected ?: 'guest',
                        'form_source' => 'kid_aw_ods_submit',
                        'client_timestamp' => $this->input->post('client_timestamp'),
                        'user_agent_info' => $this->input->post('user_agent_info'),
                        'is_anonymous' => $this->input->post('is_anonymous') === '1'
                    ];

                    // *** กำหนด user_type สำหรับ Library ***
                    $library_user_type = 'citizen'; // default
                    if ($user_type_detected === 'member' || $user_type_detected === 'staff') {
                        $library_user_type = 'citizen';
                    } elseif ($user_type_detected === 'admin') {
                        $library_user_type = 'staff';
                    }

                    // *** เรียกใช้ reCAPTCHA verification ***
                    if (isset($this->recaptcha_lib)) {
                        $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $library_user_type, null, $recaptcha_options);

                        log_message('info', 'reCAPTCHA verification result: ' . json_encode([
                            'success' => $recaptcha_result['success'],
                            'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                            'action' => $recaptcha_action,
                            'source' => $recaptcha_source,
                            'user_type_detected' => $user_type_detected,
                            'library_user_type' => $library_user_type
                        ]));

                        // *** ตรวจสอบผลลัพธ์ ***
                        if (!$recaptcha_result['success']) {
                            log_message('error', 'reCAPTCHA verification failed: ' . json_encode([
                                'message' => $recaptcha_result['message'],
                                'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                                'action' => $recaptcha_action,
                                'source' => $recaptcha_source
                            ]));

                            $this->json_response([
                                'success' => false,
                                'message' => 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง',
                                'error_type' => 'recaptcha_failed',
                                'recaptcha_data' => $recaptcha_result['data']
                            ]);
                            return;
                        }

                        log_message('info', 'reCAPTCHA verification successful for kid aw ods submit');
                    } else {
                        log_message('error', 'reCAPTCHA library not loaded');
                    }

                } catch (Exception $e) {
                    log_message('error', 'reCAPTCHA verification error: ' . $e->getMessage());

                    $this->json_response([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการตรวจสอบความปลอดภัย',
                        'error_type' => 'recaptcha_error'
                    ]);
                    return;
                }
            } else if (!$dev_mode) {
                log_message('info', 'No reCAPTCHA token provided for kid aw ods submit');
            } else {
                log_message('info', 'Development mode - skipping reCAPTCHA verification');
            }

            // *** แก้ไข: ปรับปรุงการตรวจสอบ AJAX Request - ทำให้หลวมกว่าเดิม ***
            $is_ajax = false;

            // วิธีที่ 1: ตรวจสอบ X-Requested-With header
            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                $is_ajax = true;
            }

            // วิธีที่ 2: ตรวจสอบ parameter พิเศษ
            if ($this->input->post('ajax_request') === '1') {
                $is_ajax = true;
            }

            // วิธีที่ 3: ตรวจสอบ CodeIgniter built-in
            if ($this->input->is_ajax_request()) {
                $is_ajax = true;
            }

            // *** ไม่บังคับ AJAX - ให้ทำงานได้ทุกกรณี ***
            log_message('debug', 'Request type check: is_ajax=' . ($is_ajax ? 'true' : 'false'));

            // *** ตรวจสอบว่าเป็น staff user หรือไม่ ***
            if ($this->is_staff_user()) {
                $this->json_response(['success' => false, 'message' => 'เจ้าหน้าที่ไม่สามารถใช้หน้านี้ได้']);
                return;
            }

            // *** Validation Rules - ใช้ CodeIgniter Form Validation ***
            $this->load->library('form_validation');

            $this->form_validation->set_rules('kid_aw_ods_by', 'ชื่อ-นามสกุล', 'required|trim|max_length[255]', [
                'required' => 'กรุณากรอก{field}',
                'max_length' => '{field}ไม่ควรเกิน 255 ตัวอักษร'
            ]);

            $this->form_validation->set_rules('kid_aw_ods_phone', 'เบอร์โทรศัพท์', 'required|trim|regex_match[/^[0-9]{10}$/]', [
                'required' => 'กรุณากรอก{field}',
                'regex_match' => '{field}ต้องเป็นตัวเลข 10 หลัก'
            ]);

            $this->form_validation->set_rules('kid_aw_ods_number', 'เลขบัตรประชาชน', 'required|trim|regex_match[/^[0-9]{13}$/]', [
                'required' => 'กรุณากรอก{field}',
                'regex_match' => '{field}ต้องเป็นตัวเลข 13 หลัก'
            ]);

            $this->form_validation->set_rules('kid_aw_ods_type', 'ประเภทเงินสนับสนุน', 'required|in_list[children,disabled]', [
                'required' => 'กรุณาเลือก{field}',
                'in_list' => '{field}ไม่ถูกต้อง'
            ]);

            // Email (optional)
            if (!empty($this->input->post('kid_aw_ods_email'))) {
                $this->form_validation->set_rules('kid_aw_ods_email', 'อีเมล', 'valid_email', [
                    'valid_email' => '{field}ไม่ถูกต้อง'
                ]);
            }

            // *** ตรวจสอบ Validation ***
            if (!$this->form_validation->run()) {
                $errors = $this->form_validation->error_array();
                $error_message = implode('<br>', $errors);

                $this->json_response([
                    'success' => false,
                    'message' => $error_message,
                    'errors' => $errors
                ]);
                return;
            }

            // *** ตรวจสอบเลขบัตรประชาชนไทย ***
            $id_card = $this->input->post('kid_aw_ods_number');
            if (!$this->validate_thai_id_card($id_card)) {
                $this->json_response([
                    'success' => false,
                    'message' => 'เลขบัตรประจำตัวประชาชนไม่ถูกต้อง'
                ]);
                return;
            }

            // *** ตรวจสอบการยื่นเรื่องซ้ำ ***
            if ($this->check_duplicate_submission($id_card, $this->input->post('kid_aw_ods_phone'))) {
                $this->json_response([
                    'success' => false,
                    'message' => 'พบการยื่นเรื่องด้วยเลขบัตรประชาชนหรือเบอร์โทรศัพท์นี้แล้วใน 24 ชั่วโมงที่ผ่านมา'
                ]);
                return;
            }

            // *** สร้าง ID ใหม่ ***
            $kid_aw_ods_id = $this->generate_kid_id();

            // *** ดึงข้อมูล user ปัจจุบัน ***
            $current_user = $this->get_current_user_detailed();

            // *** เตรียมข้อมูลสำหรับบันทึก ***
            $save_data = [
                'kid_aw_ods_id' => $kid_aw_ods_id,
                'kid_aw_ods_type' => $this->input->post('kid_aw_ods_type'),
                'kid_aw_ods_by' => $this->input->post('kid_aw_ods_by'),
                'kid_aw_ods_phone' => $this->input->post('kid_aw_ods_phone'),
                'kid_aw_ods_email' => $this->input->post('kid_aw_ods_email') ?: '',
                'kid_aw_ods_number' => $id_card,
                'kid_aw_ods_address' => $this->prepare_address_data(),
                'kid_aw_ods_status' => 'submitted',
                'kid_aw_ods_priority' => 'normal',
                'kid_aw_ods_user_type' => $current_user['user_type'],
                'kid_aw_ods_datesave' => date('Y-m-d H:i:s'),
                'kid_aw_ods_ip_address' => $this->input->ip_address(),
                'kid_aw_ods_user_agent' => substr($this->input->user_agent(), 0, 255)
            ];

            // เพิ่ม user_id ถ้า login
            if ($current_user['is_logged_in'] && $current_user['user_info']) {
                $save_data['kid_aw_ods_user_id'] = $current_user['user_info']['id'];
            }

            // เพิ่มข้อมูลที่อยู่แยก
            $this->add_guest_address_data($save_data);

            // *** จัดการไฟล์อัพโหลด ***
            $uploaded_files = $this->handle_file_uploads($kid_aw_ods_id);

            // *** บันทึกข้อมูล ***
            $this->db->trans_start();

            try {
                $insert_result = $this->Kid_aw_ods_model->add_kid_aw_ods($save_data);

                if (!$insert_result) {
                    throw new Exception('Failed to insert kid_aw_ods data');
                }

                // บันทึกไฟล์
                foreach ($uploaded_files as $file) {
                    $file_data = [
                        'kid_aw_ods_file_ref_id' => $kid_aw_ods_id,
                        'kid_aw_ods_file_name' => $file['file_name'],
                        'kid_aw_ods_file_original_name' => $file['original_name'],
                        'kid_aw_ods_file_type' => $file['file_type'],
                        'kid_aw_ods_file_size' => $file['file_size'],
                        'kid_aw_ods_file_path' => $file['file_path'],
                        'kid_aw_ods_file_uploaded_at' => date('Y-m-d H:i:s'),
                        'kid_aw_ods_file_uploaded_by' => $save_data['kid_aw_ods_by'],
                        'kid_aw_ods_file_status' => 'active'
                    ];

                    $this->Kid_aw_ods_model->add_kid_aw_ods_file($file_data);
                }

                $this->db->trans_complete();

                log_message('info', "Line notification send by Kid AW Ods ID : {$kid_aw_ods_id}");
                $this->line_notification->send_line_kid_aw_ods_notification($kid_aw_ods_id);


                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Transaction failed');
                }

            } catch (Exception $e) {
                $this->db->trans_rollback();
                $this->cleanup_uploaded_files($uploaded_files);

                log_message('error', 'Database transaction failed: ' . $e->getMessage());

                $this->json_response([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
                ]);
                return;
            }

            log_message('info', "Kid AW ODS submitted successfully: {$kid_aw_ods_id} by " . $save_data['kid_aw_ods_by']);

            // *** ส่ง Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'ยื่นเรื่องเงินสนับสนุนเด็กสำเร็จ',
                'kid_aw_ods_id' => $kid_aw_ods_id,
                'files_uploaded' => count($uploaded_files)
            ]);

        } catch (Exception $e) {
            log_message('error', 'Critical error in add_kid_aw_ods: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง',
                'error_code' => 'SYSTEM_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }

        log_message('info', '=== KID AW ODS SUBMIT END ===');
    }



    private function check_duplicate_submission($id_card, $phone)
    {
        try {
            // ตรวจสอบ 24 ชั่วโมงล่าสุด
            $this->db->select('kid_aw_ods_id');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->group_start();
            $this->db->where('kid_aw_ods_number', $id_card);
            $this->db->or_where('kid_aw_ods_phone', $phone);
            $this->db->group_end();
            $this->db->where('kid_aw_ods_datesave >=', date('Y-m-d H:i:s', strtotime('-24 hours')));
            $query = $this->db->get();

            return $query->num_rows() > 0;

        } catch (Exception $e) {
            log_message('error', 'Error checking duplicate submission: ' . $e->getMessage());
            return false; // ถ้าเกิด error ให้ผ่านไป
        }
    }


    private function cleanup_uploaded_files($uploaded_files)
    {
        foreach ($uploaded_files as $file) {
            if (isset($file['file_path']) && file_exists($file['file_path'])) {
                @unlink($file['file_path']);
                log_message('info', 'Cleaned up uploaded file: ' . $file['file_name']);
            }
        }
    }








    /**
     * จัดการอัพโหลดไฟล์
     */
    private function handle_file_uploads($kid_id)
    {
        $uploaded_files = [];

        if (empty($_FILES['kid_aw_ods_files']['name'][0])) {
            return $uploaded_files;
        }

        $upload_path = './uploads/kid_aw_ods/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_count = count($_FILES['kid_aw_ods_files']['name']);

        for ($i = 0; $i < $file_count && $i < 3; $i++) {
            if ($_FILES['kid_aw_ods_files']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file_tmp = $_FILES['kid_aw_ods_files']['tmp_name'][$i];
            $file_name = $_FILES['kid_aw_ods_files']['name'][$i];
            $file_size = $_FILES['kid_aw_ods_files']['size'][$i];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_types) || $file_size > $max_size) {
                continue;
            }

            $new_file_name = 'kid_' . $kid_id . '_' . date('YmdHis') . '_' . $i . '.' . $file_ext;
            $full_path = $upload_path . $new_file_name;

            if (move_uploaded_file($file_tmp, $full_path)) {
                $uploaded_files[] = [
                    'original_name' => $file_name,
                    'file_name' => $new_file_name,
                    'file_size' => $file_size,
                    'file_type' => $this->get_mime_type($file_ext),
                    'file_path' => $full_path
                ];
            }
        }

        return $uploaded_files;
    }





    /**
     * เพิ่มข้อมูลที่อยู่แยกสำหรับ guest
     */
    private function add_guest_address_data(&$save_data)
    {
        $guest_fields = ['guest_province', 'guest_amphoe', 'guest_district', 'guest_zipcode'];

        foreach ($guest_fields as $field) {
            $value = $this->input->post($field);
            if (!empty($value)) {
                $save_data[$field] = $value;
            }
        }
    }




    /**
     * เตรียมข้อมูลที่อยู่
     */
    private function prepare_address_data()
    {
        $address_parts = [];

        $additional = $this->input->post('kid_aw_ods_address');
        if (!empty($additional)) {
            $address_parts[] = $additional;
        }

        return implode(' ', array_filter($address_parts));
    }



    /**
     * ตรวจสอบเลขบัตรประชาชนไทย
     */
    private function validate_thai_id_card($id_card)
    {
        if (!preg_match('/^\d{13}$/', $id_card)) {
            return false;
        }

        if (preg_match('/^(\d)\1{12}$/', $id_card)) {
            return false; // ซ้ำหมด
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $id_card[$i] * (13 - $i);
        }

        $remainder = $sum % 11;
        $check_digit = $remainder < 2 ? (1 - $remainder) : (11 - $remainder);

        return $check_digit == (int) $id_card[12];
    }


    /**
     * *** ใหม่: สร้างการแจ้งเตือนสำหรับเงินสนับสนุนเด็ก ***
     */
    private function create_kid_aw_ods_notifications($kid_aw_ods_id, $kid_data, $current_user, $file_count = 0)
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('warning', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($kid_data['kid_aw_ods_type'] === 'disabled') ? 'เด็กพิการ' : 'เด็กทั่วไป';

            // 1. แจ้งเตือน Staff ทั้งหมด
            $staff_data_json = json_encode([
                'kid_aw_ods_id' => $kid_aw_ods_id,
                'type' => $kid_data['kid_aw_ods_type'],
                'type_display' => $type_display,
                'requester' => $kid_data['kid_aw_ods_by'],
                'phone' => $kid_data['kid_aw_ods_phone'],
                'email' => $kid_data['kid_aw_ods_email'],
                'id_card' => $kid_data['kid_aw_ods_number'],
                'user_type' => $current_user['user_type'],
                'is_guest' => ($current_user['user_type'] === 'guest'),
                'created_at' => $current_time,
                'file_count' => $file_count,
                'has_id_card' => !empty($kid_data['kid_aw_ods_number']),
                'notification_type' => 'staff_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'kid_aw_ods',
                'title' => 'เงินสนับสนุนเด็กใหม่',
                'message' => "มีการยื่นเรื่องเงินสนับสนุน{$type_display}ใหม่ โดย {$kid_data['kid_aw_ods_by']} หมายเลขอ้างอิง: {$kid_aw_ods_id}",
                'reference_id' => 0,
                'reference_table' => 'tbl_kid_aw_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-baby',
                'url' => site_url("Kid_aw_ods/kid_detail/{$kid_aw_ods_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => ($current_user['is_logged_in'] && isset($current_user['user_info']['id'])) ? intval($current_user['user_info']['id']) : 0,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff notification created for kid_aw_ods: {$kid_aw_ods_id}");
            }

            // 2. แจ้งเตือน Public User ที่ login (ถ้ามี)
            if (
                $current_user['is_logged_in'] &&
                $current_user['user_type'] === 'public' &&
                isset($current_user['user_info']['id'])
            ) {

                $individual_data_json = json_encode([
                    'kid_aw_ods_id' => $kid_aw_ods_id,
                    'type' => $kid_data['kid_aw_ods_type'],
                    'type_display' => $type_display,
                    'status' => 'submitted',
                    'created_at' => $current_time,
                    'file_count' => $file_count,
                    'follow_url' => site_url("Kid_aw_ods/my_kid_aw_ods"),
                    'notification_type' => 'individual_confirmation'
                ], JSON_UNESCAPED_UNICODE);

                $individual_notification = [
                    'type' => 'kid_aw_ods',
                    'title' => 'ยื่นเรื่องเงินสนับสนุนเด็กสำเร็จ',
                    'message' => "เรื่องเงินสนับสนุน{$type_display} หมายเลขอ้างอิง: {$kid_aw_ods_id} ได้รับการบันทึกเรียบร้อยแล้ว",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_kid_aw_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($current_user['user_info']['id']),
                    'priority' => 'high',
                    'icon' => 'fas fa-check-circle',
                    'url' => site_url("Kid_aw_ods/my_kid_aw_ods_detail/{$kid_aw_ods_id}"),
                    'data' => $individual_data_json,
                    'created_at' => $current_time,
                    'created_by' => intval($current_user['user_info']['id']),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $individual_result = $this->db->insert('tbl_notifications', $individual_notification);

                if ($individual_result) {
                    log_message('info', "Individual notification created for kid_aw_ods: {$kid_aw_ods_id}");
                }
            }

            log_message('info', "Kid AW ODS notifications created successfully: {$kid_aw_ods_id}");

        } catch (Exception $e) {
            log_message('error', 'Error creating kid_aw_ods notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * *** ฟังก์ชันจัดการไฟล์สำหรับการสร้างใหม่ ***
     */
    private function handle_file_uploads_for_new_kid($kid_aw_ods_id)
    {
        $uploaded_files = [];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        $upload_dir = './uploads/kid_aw_ods/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                log_message('error', 'Failed to create upload directory: ' . $upload_dir);
                return [];
            }
        }

        if (!isset($_FILES['kid_aw_ods_files']) || !is_array($_FILES['kid_aw_ods_files']['name'])) {
            return [];
        }

        $file_count = count($_FILES['kid_aw_ods_files']['name']);
        $upload_timestamp = date('YmdHis') . '_' . uniqid();

        for ($i = 0; $i < $file_count; $i++) {
            // ตรวจสอบข้อผิดพลาดการอัพโหลด
            if ($_FILES['kid_aw_ods_files']['error'][$i] !== UPLOAD_ERR_OK) {
                log_message('warning', 'File upload error at index ' . $i . ': ' . $_FILES['kid_aw_ods_files']['error'][$i]);
                continue;
            }

            $file_tmp = $_FILES['kid_aw_ods_files']['tmp_name'][$i];
            $file_name = $_FILES['kid_aw_ods_files']['name'][$i];
            $file_size = $_FILES['kid_aw_ods_files']['size'][$i];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // ตรวจสอบว่าไฟล์ว่างหรือไม่
            if (empty($file_name)) {
                continue;
            }

            // ตรวจสอบประเภทไฟล์
            if (!in_array($file_ext, $allowed_types)) {
                log_message('warning', 'Invalid file type: ' . $file_ext . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบขนาดไฟล์
            if ($file_size > $max_size) {
                log_message('warning', 'File size too large: ' . $file_size . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file_tmp) || !is_uploaded_file($file_tmp)) {
                log_message('warning', 'Uploaded file not found or invalid: ' . $file_tmp);
                continue;
            }

            // ตรวจสอบไฟล์ให้ปลอดภัย
            if (!$this->is_safe_file($file_tmp, $file_ext)) {
                log_message('warning', 'Unsafe file detected: ' . $file_name);
                continue;
            }

            // สร้างชื่อไฟล์ใหม่แบบ unique
            $clean_kid_id = preg_replace('/[^a-zA-Z0-9]/', '', $kid_aw_ods_id);
            $new_file_name = 'kid_' . $clean_kid_id . '_' . $upload_timestamp . '_' . $i . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;

            // ตรวจสอบว่าไฟล์ซ้ำในระบบไฟล์หรือไม่
            $counter = 1;
            while (file_exists($upload_path)) {
                $new_file_name = 'kid_' . $clean_kid_id . '_' . $upload_timestamp . '_' . $i . '_dup' . $counter . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                $counter++;

                if ($counter > 100) {
                    log_message('error', 'Too many duplicate files, skipping: ' . $file_name);
                    continue 2;
                }
            }

            // ย้ายไฟล์
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $uploaded_files[] = [
                    'original_name' => $file_name,
                    'file_name' => $new_file_name,
                    'file_size' => $file_size,
                    'file_type' => $this->get_mime_type($file_ext),
                    'file_path' => $upload_path,
                    'upload_date' => date('Y-m-d H:i:s')
                ];

                log_message('info', 'File uploaded successfully: ' . $new_file_name . ' for ' . $kid_aw_ods_id);
            } else {
                log_message('error', 'Failed to move uploaded file: ' . $file_name . ' to ' . $upload_path);
            }
        }

        log_message('info', 'Total files uploaded: ' . count($uploaded_files) . ' for ' . $kid_aw_ods_id);
        return $uploaded_files;
    }


    /**
     * หน้าเงินสนับสนุนเด็กของฉัน (สำหรับ public user ที่ login)
     */
    public function my_kid_aw_ods()
    {
        // ตรวจสอบว่าเป็น staff user หรือไม่
        if ($this->is_staff_user()) {
            $this->show_staff_redirect_warning();
            return;
        }

        // ตรวจสอบการ login
        $current_user = $this->get_current_user_info();
        if (!$current_user['is_logged_in'] || $current_user['user_type'] !== 'public') {
            $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบเพื่อดูข้อมูลของคุณ');
            redirect('User');
            return;
        }

        try {
            $data = $this->prepare_page_data();
            $data['is_logged_in'] = true;
            $data['user_info'] = $current_user['user_info'];
            $data['user_type'] = $current_user['user_type'];

            // ดึงรายการเงินสนับสนุนเด็กของผู้ใช้
            $user_kid_aw_ods = $this->Kid_aw_ods_model->get_kid_aw_ods_by_user(
                $current_user['user_info']['id'],
                'public'
            );

            // *** เพิ่ม: เตรียมข้อมูลและตรวจสอบสิทธิ์การแก้ไข ***
            $prepared_kid_list = [];
            foreach ($user_kid_aw_ods as $kid) {
                $prepared_kid = $this->prepare_kid_for_display($kid);

                // ตรวจสอบสถานะที่แก้ไขได้
                $editable_statuses = ['submitted', 'reviewing'];
                $prepared_kid['can_edit'] = in_array($prepared_kid['kid_aw_ods_status'] ?? 'submitted', $editable_statuses);

                $prepared_kid_list[] = $prepared_kid;
            }

            $data['kid_aw_ods'] = $prepared_kid_list;
            $data['status_counts'] = $this->calculate_status_counts($data['kid_aw_ods']);

            // *** เพิ่ม: ตัวแปรสำหรับการแก้ไขทั่วไป ***
            $data['can_edit'] = true; // ผู้ใช้ public สามารถแก้ไขข้อมูลของตัวเองได้
            $data['can_add_files'] = true; // สามารถเพิ่มไฟล์ได้
            $data['can_delete_files'] = true; // สามารถลบไฟล์ได้

            // *** เพิ่ม: ข้อมูลเสริมสำหรับ JavaScript ***
            $data['user_permissions'] = [
                'can_edit' => true,
                'can_add_files' => true,
                'can_delete_files' => true,
                'max_file_size' => 5, // MB
                'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
                'max_files_per_upload' => 5
            ];

            $data['page_title'] = 'เงินสนับสนุนเด็กของฉัน';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บัญชีของฉัน', 'url' => '#'],
                ['title' => 'เงินสนับสนุนเด็ก', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_kid_aw_ods', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in my_kid_aw_ods: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการโหลดหน้า', 500);
        }
    }

    /**
     * หน้าติดตามสถานะ (สำหรับ guest user) - เพิ่ม reCAPTCHA
     */
    public function follow_kid_aw_ods()
    {
        log_message('info', '=== FOLLOW KID AW ODS START ===');

        // ตรวจสอบ staff user
        if ($this->is_staff_user()) {
            $this->show_staff_redirect_warning();
            return;
        }

        // ตรวจสอบ logged in user
        $current_user = $this->get_current_user_info();
        if ($current_user['is_logged_in']) {
            redirect('Kid_aw_ods/my_kid_aw_ods');
            return;
        }

        try {
            // === ขั้นตอนที่ 1: จัดการ POST Request (การค้นหาด้วย reCAPTCHA) ===
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return $this->handleSearchPost();
            }

            // === ขั้นตอนที่ 2: จัดการ GET Request (แสดงผลลัพธ์) ===
            return $this->handleSearchGet();

        } catch (Exception $e) {
            log_message('error', 'Error in follow_kid_aw_ods: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการโหลดหน้า', 500);
        }
    }

    private function handleSearchPost()
    {
        log_message('info', 'Handling POST search request');

        $ref_id = $this->input->post('ref');
        $recaptcha_token = $this->input->post('g-recaptcha-response');
        $recaptcha_action = $this->input->post('recaptcha_action') ?: 'follow_kid_aw_ods_search';
        $recaptcha_source = $this->input->post('recaptcha_source') ?: 'follow_kid_aw_ods_form';

        // ส่งต่อไปยัง Library
        $recaptcha_options = [
            'action' => $recaptcha_action,
            'source' => $recaptcha_source,
            'user_type_detected' => 'guest'
        ];

        // Verify reCAPTCHA token
        $recaptcha_result = $this->recaptcha_lib->verify(
            $recaptcha_token,
            'citizen',
            null,
            $recaptcha_options
        );

        // ตรวจสอบข้อมูลพื้นฐาน
        if (empty($ref_id)) {
            $this->session->set_flashdata('error_message', 'กรุณากรอกหมายเลขอ้างอิง');
            redirect('Kid_aw_ods/follow_kid_aw_ods');
            return;
        }

        // ตรวจสอบ reCAPTCHA (บังคับ)
        if (empty($recaptcha_token)) {
            $this->session->set_flashdata('error_message', 'กรุณายืนยัน reCAPTCHA');
            redirect('Kid_aw_ods/follow_kid_aw_ods');
            return;
        }


        if (!$recaptcha_result['success']) {
            log_message('error', 'reCAPTCHA verification failed: ' . $recaptcha_result['message']);
            $this->session->set_flashdata('error_message', 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่');
            redirect('Kid_aw_ods/follow_kid_aw_ods');
            return;
        }

        log_message('info', 'reCAPTCHA verification successful');

        // หลังจากตรวจสอบแล้ว redirect ไป GET พร้อม ref
        redirect('Kid_aw_ods/follow_kid_aw_ods?ref=' . urlencode($ref_id));
    }

    private function handleSearchGet()
    {
        log_message('info', 'Handling GET display request');

        $data = $this->prepare_page_data();

        // เตรียมข้อมูลพื้นฐาน
        $current_user = $this->get_current_user_info();
        $data['is_logged_in'] = false;
        $data['user_info'] = null;
        $data['user_type'] = 'guest';

        // ค้นหาข้อมูลถ้ามี ref_id
        $ref_id = $this->input->get('ref');
        $data['ref_id'] = $ref_id;
        $data['kid_aw_ods_info'] = null;
        $data['search_performed'] = false;

        if (!empty($ref_id)) {
            log_message('info', 'Searching for kid aw ods with ref: ' . $ref_id);

            $kid_info = $this->get_kid_aw_ods_for_guest_only($ref_id);

            if ($kid_info) {
                $data['kid_aw_ods_info'] = $this->prepare_kid_for_display($kid_info);
                $data['search_performed'] = true;
                log_message('info', 'Found kid aw ods: ' . $ref_id);
            } else {
                log_message('info', 'Kid aw ods not found: ' . $ref_id);
                $this->session->set_flashdata('warning_message', 'ไม่พบข้อมูลหมายเลขอ้างอิง: ' . $ref_id);
            }
        }

        // เตรียมข้อมูลหน้า
        $data['page_title'] = 'ติดตามสถานะเงินสนับสนุนเด็ก';
        $data['breadcrumb'] = [
            ['title' => 'หน้าแรก', 'url' => base_url()],
            ['title' => 'บริการประชาชน', 'url' => '#'],
            ['title' => 'ติดตามสถานะ', 'url' => '']
        ];

        // Flash messages
        $data['success_message'] = $this->session->flashdata('success_message');
        $data['error_message'] = $this->session->flashdata('error_message');
        $data['info_message'] = $this->session->flashdata('info_message');
        $data['warning_message'] = $this->session->flashdata('warning_message');

        // แสดงผล
        $this->load->view('frontend_templat/header', $data);
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/follow_kid_aw_ods', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer', $data);

        log_message('info', '=== FOLLOW KID AW ODS END ===');
    }

    /**
     * *** เพิ่มฟังก์ชันใหม่: ค้นหาเงินสนับสนุนเด็กเฉพาะ guest เท่านั้น ***
     */
    private function get_kid_aw_ods_for_guest_only($kid_aw_ods_id)
    {
        try {
            if (empty($kid_aw_ods_id)) {
                return null;
            }

            log_message('debug', "Searching kid AW ODS for guest only: {$kid_aw_ods_id}");

            // *** ค้นหาเฉพาะที่มี kid_aw_ods_user_type = 'guest' ***
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $this->db->where('kid_aw_ods_user_type', 'guest'); // เงื่อนไขสำคัญ
            $query = $this->db->get();

            if ($query->num_rows() === 0) {
                log_message('info', "No guest kid AW ODS found for ID: {$kid_aw_ods_id}");
                return null;
            }

            $kid_data = $query->row();

            // เพิ่มข้อมูลเพิ่มเติม (ถ้าจำเป็น)
            $kid_data->files = [];
            $kid_data->history = [];

            // ดึงไฟล์ที่เกี่ยวข้อง (ถ้ามีตาราง)
            if ($this->db->table_exists('tbl_kid_aw_ods_files')) {
                $this->db->select('*');
                $this->db->from('tbl_kid_aw_ods_files');
                $this->db->where('kid_aw_ods_file_ref_id', $kid_aw_ods_id);
                $this->db->order_by('kid_aw_ods_file_uploaded_at', 'DESC');
                $files_query = $this->db->get();

                if ($files_query->num_rows() > 0) {
                    $kid_data->files = $files_query->result();
                }
            }

            // ดึงประวัติที่เกี่ยวข้อง (ถ้ามีตาราง)
            if ($this->db->table_exists('tbl_kid_aw_ods_history')) {
                $this->db->select('*');
                $this->db->from('tbl_kid_aw_ods_history');
                $this->db->where('kid_aw_ods_history_ref_id', $kid_aw_ods_id);
                $this->db->order_by('action_date', 'DESC');
                $history_query = $this->db->get();

                if ($history_query->num_rows() > 0) {
                    $kid_data->history = $history_query->result();
                }
            }

            log_message('info', "Successfully found guest kid AW ODS: {$kid_aw_ods_id} - Status: {$kid_data->kid_aw_ods_status}");

            return $kid_data;

        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_aw_ods_for_guest_only: ' . $e->getMessage());
            return null;
        }
    }

    // Helper functions
    private function prepare_navbar_data()
    {
        $data = [
            'qActivity' => [],
            'qNews' => [],
            'qAnnounce' => [],
            'qOrder' => [],
            'qProcurement' => [],
            'qMui' => [],
            'qGuide_work' => [],
            'qLoadform' => [],
            'qPppw' => [],
            'qMsg_pres' => [],
            'qHistory' => [],
            'qOtop' => [],
            'qGci' => [],
            'qVision' => [],
            'qAuthority' => [],
            'qMission' => [],
            'qMotto' => [],
            'qCmi' => [],
            'qExecutivepolicy' => [],
            'qTravel' => [],
            'qSi' => [],
            'qHotnews' => [],
            'qWeather' => [],
            'events' => [],
            'qBanner' => [],
            'qBackground_personnel' => []
        ];

        try {
            if (isset($this->activity_model) && method_exists($this->activity_model, 'activity_frontend')) {
                $result = $this->activity_model->activity_frontend();
                $data['qActivity'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->HotNews_model) && method_exists($this->HotNews_model, 'hotnews_frontend')) {
                $result = $this->HotNews_model->hotnews_frontend();
                $data['qHotnews'] = (is_array($result) || is_object($result)) ? $result : [];
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }

    private function prepare_kid_aw_ods_list_for_display($kid_aw_ods_list)
    {
        $prepared_list = [];

        // ตรวจสอบ input
        if (empty($kid_aw_ods_list) || !is_array($kid_aw_ods_list)) {
            return $prepared_list;
        }

        foreach ($kid_aw_ods_list as $record) {
            // สร้าง object ที่มีข้อมูลครบถ้วน
            $record_object = new stdClass();

            // *** แก้ไข: ใช้ helper function แทน ***
            $record_object->kid_aw_ods_id = $this->get_value_safe($record, 'kid_aw_ods_id', '');
            $record_object->kid_aw_ods_type = $this->get_value_safe($record, 'kid_aw_ods_type', 'children');
            $record_object->kid_aw_ods_status = $this->get_value_safe($record, 'kid_aw_ods_status', 'submitted');
            $record_object->kid_aw_ods_by = $this->get_value_safe($record, 'kid_aw_ods_by', '');
            $record_object->kid_aw_ods_phone = $this->get_value_safe($record, 'kid_aw_ods_phone', '');
            $record_object->kid_aw_ods_email = $this->get_value_safe($record, 'kid_aw_ods_email', '');
            $record_object->kid_aw_ods_number = $this->get_value_safe($record, 'kid_aw_ods_number', '');
            $record_object->kid_aw_ods_address = $this->get_value_safe($record, 'kid_aw_ods_address', '');
            $record_object->kid_aw_ods_datesave = $this->get_value_safe($record, 'kid_aw_ods_datesave', '');
            $record_object->kid_aw_ods_updated_at = $this->get_value_safe($record, 'kid_aw_ods_updated_at', null);
            $record_object->kid_aw_ods_priority = $this->get_value_safe($record, 'kid_aw_ods_priority', 'normal');
            $record_object->kid_aw_ods_user_type = $this->get_value_safe($record, 'kid_aw_ods_user_type', 'guest');
            $record_object->kid_aw_ods_user_id = $this->get_value_safe($record, 'kid_aw_ods_user_id', null);
            $record_object->kid_aw_ods_assigned_to = $this->get_value_safe($record, 'kid_aw_ods_assigned_to', null);
            $record_object->kid_aw_ods_notes = $this->get_value_safe($record, 'kid_aw_ods_notes', '');
            $record_object->kid_aw_ods_completed_at = $this->get_value_safe($record, 'kid_aw_ods_completed_at', null);

            // ข้อมูลเพิ่มเติม
            $record_object->guest_province = $this->get_value_safe($record, 'guest_province', '');
            $record_object->guest_amphoe = $this->get_value_safe($record, 'guest_amphoe', '');
            $record_object->guest_district = $this->get_value_safe($record, 'guest_district', '');
            $record_object->guest_zipcode = $this->get_value_safe($record, 'guest_zipcode', '');
            $record_object->assigned_staff_name = $this->get_value_safe($record, 'assigned_staff_name', '');
            $record_object->assigned_staff_system = $this->get_value_safe($record, 'assigned_staff_system', '');

            // ไฟล์และประวัติ
            $record_object->files = $this->get_value_safe($record, 'files', []);
            $record_object->history = $this->get_value_safe($record, 'history', []);

            // เพิ่มข้อมูล display properties
            $record_object->status_display = $this->get_kid_aw_ods_status_display($record_object->kid_aw_ods_status);
            $record_object->status_class = $this->get_kid_aw_ods_status_class($record_object->kid_aw_ods_status);
            $record_object->status_icon = $this->get_kid_aw_ods_status_icon($record_object->kid_aw_ods_status);
            $record_object->status_color = $this->get_kid_aw_ods_status_color($record_object->kid_aw_ods_status);
            $record_object->type_display = $this->get_kid_aw_ods_type_display($record_object->kid_aw_ods_type);
            $record_object->type_icon = $this->get_kid_aw_ods_type_icon($record_object->kid_aw_ods_type);

            $record_object->latest_update = $record_object->kid_aw_ods_updated_at ?: $record_object->kid_aw_ods_datesave;

            $prepared_list[] = $record_object;
        }

        return $prepared_list;
    }

    private function ensure_kid_data_completeness($kid_data)
    {
        $required_fields = [
            'kid_aw_ods_id' => '',
            'kid_aw_ods_type' => 'children',
            'kid_aw_ods_status' => 'submitted',
            'kid_aw_ods_by' => '',
            'kid_aw_ods_phone' => '',
            'kid_aw_ods_email' => '',
            'kid_aw_ods_number' => '',
            'kid_aw_ods_address' => '',
            'kid_aw_ods_datesave' => '',
            'kid_aw_ods_updated_at' => null,
            'kid_aw_ods_priority' => 'normal',
            'kid_aw_ods_user_type' => 'guest',
            'kid_aw_ods_user_id' => null,
            'kid_aw_ods_assigned_to' => null,
            'kid_aw_ods_notes' => '',
            'kid_aw_ods_completed_at' => null,
            'guest_province' => '',
            'guest_amphoe' => '',
            'guest_district' => '',
            'guest_zipcode' => '',
            'assigned_staff_name' => '',
            'assigned_staff_system' => '',
            'files' => [],
            'history' => []
        ];

        if (is_object($kid_data)) {
            // ถ้าเป็น object
            foreach ($required_fields as $field => $default_value) {
                if (!property_exists($kid_data, $field)) {
                    $kid_data->$field = $default_value;
                } elseif ($kid_data->$field === null && $default_value !== null) {
                    $kid_data->$field = $default_value;
                }
            }
        } elseif (is_array($kid_data)) {
            // ถ้าเป็น array ให้แปลงเป็น object
            $object_data = new stdClass();
            foreach ($required_fields as $field => $default_value) {
                $object_data->$field = $kid_data[$field] ?? $default_value;
            }
            $kid_data = $object_data;
        } else {
            // ถ้าไม่ใช่ทั้งสองอย่าง ให้สร้าง object ใหม่
            $kid_data = new stdClass();
            foreach ($required_fields as $field => $default_value) {
                $kid_data->$field = $default_value;
            }
        }

        return $kid_data;
    }

    private function calculate_kid_aw_ods_status_counts($kid_aw_ods_list)
    {
        $status_counts = [
            'total' => 0,
            'submitted' => 0,
            'reviewing' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0
        ];

        // ตรวจสอบว่า list ไม่ว่างและเป็น array
        if (empty($kid_aw_ods_list) || !is_array($kid_aw_ods_list)) {
            return $status_counts;
        }

        $status_counts['total'] = count($kid_aw_ods_list);

        foreach ($kid_aw_ods_list as $record) {
            // *** แก้ไข: รองรับทั้ง object และ array ***
            $status = '';

            if (is_object($record)) {
                // ถ้าเป็น object
                $status = $record->kid_aw_ods_status ?? '';
            } elseif (is_array($record)) {
                // ถ้าเป็น array
                $status = $record['kid_aw_ods_status'] ?? '';
            }

            // นับตามสถานะ
            switch ($status) {
                case 'submitted':
                    $status_counts['submitted']++;
                    break;
                case 'reviewing':
                    $status_counts['reviewing']++;
                    break;
                case 'approved':
                    $status_counts['approved']++;
                    break;
                case 'rejected':
                    $status_counts['rejected']++;
                    break;
                case 'completed':
                    $status_counts['completed']++;
                    break;
            }
        }

        return $status_counts;
    }

    /**
     * เพิ่ม: Helper function สำหรับดึงค่าจาก mixed data type (object/array)
     */
    private function get_value_safe($data, $key, $default = '')
    {
        if (is_object($data)) {
            return property_exists($data, $key) ? $data->$key : $default;
        } elseif (is_array($data)) {
            return array_key_exists($key, $data) ? $data[$key] : $default;
        }
        return $default;
    }

    // Status helper functions
    private function get_kid_aw_ods_status_display($status)
    {
        $status_map = [
            'submitted' => 'ยื่นเรื่องแล้ว',
            'reviewing' => 'กำลังพิจารณา',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
            'completed' => 'เสร็จสิ้น'
        ];

        return $status_map[$status] ?? $status;
    }

    private function get_kid_aw_ods_status_class($status)
    {
        $class_map = [
            'submitted' => 'kid-aw-ods-status-submitted',
            'reviewing' => 'kid-aw-ods-status-reviewing',
            'approved' => 'kid-aw-ods-status-approved',
            'rejected' => 'kid-aw-ods-status-rejected',
            'completed' => 'kid-aw-ods-status-completed'
        ];

        return $class_map[$status] ?? 'kid-aw-ods-status-unknown';
    }

    private function get_kid_aw_ods_status_icon($status)
    {
        $icon_map = [
            'submitted' => 'fas fa-file-alt',
            'reviewing' => 'fas fa-search',
            'approved' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            'completed' => 'fas fa-trophy'
        ];

        return $icon_map[$status] ?? 'fas fa-question-circle';
    }

    private function get_kid_aw_ods_status_color($status)
    {
        $color_map = [
            'submitted' => '#FFC700',
            'reviewing' => '#17a2b8',
            'approved' => '#28a745',
            'rejected' => '#dc3545',
            'completed' => '#6f42c1'
        ];

        return $color_map[$status] ?? '#6c757d';
    }

    private function get_kid_aw_ods_type_display($type)
    {
        $type_map = [
            'children' => 'เด็กทั่วไป',
            'disabled' => 'เด็กพิการ'
        ];

        return $type_map[$type] ?? $type;
    }

    private function get_kid_aw_ods_type_icon($type)
    {
        $icon_map = [
            'children' => 'fas fa-baby',
            'disabled' => 'fas fa-wheelchair'
        ];

        return $icon_map[$type] ?? 'fas fa-baby';
    }

    private function prepare_kid_aw_ods_for_display($kid_aw_ods)
    {
        if (is_object($kid_aw_ods)) {
            $kid_aw_ods_array = [
                'kid_aw_ods_id' => $kid_aw_ods->kid_aw_ods_id ?? '',
                'kid_aw_ods_type' => $kid_aw_ods->kid_aw_ods_type ?? 'children',
                'kid_aw_ods_status' => $kid_aw_ods->kid_aw_ods_status ?? '',
                'kid_aw_ods_by' => $kid_aw_ods->kid_aw_ods_by ?? '',
                'kid_aw_ods_phone' => $kid_aw_ods->kid_aw_ods_phone ?? '',
                'kid_aw_ods_datesave' => $kid_aw_ods->kid_aw_ods_datesave ?? '',
                'kid_aw_ods_updated_at' => $kid_aw_ods->kid_aw_ods_updated_at ?? null
            ];
        } else {
            $kid_aw_ods_array = $kid_aw_ods;
        }

        $kid_aw_ods_array['status_display'] = $this->get_kid_aw_ods_status_display($kid_aw_ods_array['kid_aw_ods_status']);
        $kid_aw_ods_array['status_class'] = $this->get_kid_aw_ods_status_class($kid_aw_ods_array['kid_aw_ods_status']);
        $kid_aw_ods_array['status_icon'] = $this->get_kid_aw_ods_status_icon($kid_aw_ods_array['kid_aw_ods_status']);
        $kid_aw_ods_array['status_color'] = $this->get_kid_aw_ods_status_color($kid_aw_ods_array['kid_aw_ods_status']);
        $kid_aw_ods_array['type_display'] = $this->get_kid_aw_ods_type_display($kid_aw_ods_array['kid_aw_ods_type']);
        $kid_aw_ods_array['type_icon'] = $this->get_kid_aw_ods_type_icon($kid_aw_ods_array['kid_aw_ods_type']);

        return $kid_aw_ods_array;
    }

    private function get_staff_list_for_assignment()
    {
        try {
            $this->db->select('m_id, m_fname, m_lname, m_system');
            $this->db->from('tbl_member');
            $this->db->where('m_status', '1');
            $this->db->where_in('m_system', ['system_admin', 'super_admin', 'user_admin']);
            $this->db->order_by('m_fname', 'ASC');
            $staff_query = $this->db->get();

            $staff_list = [];
            if ($staff_query->num_rows() > 0) {
                foreach ($staff_query->result() as $staff) {
                    $staff_list[] = [
                        'id' => $staff->m_id,
                        'name' => $staff->m_fname . ' ' . $staff->m_lname,
                        'system' => $staff->m_system
                    ];
                }
            }

            return $staff_list;

        } catch (Exception $e) {
            log_message('error', 'Error getting staff list: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: ตรวจสอบความปลอดภัยของไฟล์
     */
    private function is_safe_file($file_path, $extension)
    {
        // ตรวจสอบ MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);

        $allowed_mimes = [
            'jpg' => ['image/jpeg', 'image/jpg'],
            'jpeg' => ['image/jpeg', 'image/jpg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'pdf' => ['application/pdf']
        ];

        if (!isset($allowed_mimes[$extension]) || !in_array($mime_type, $allowed_mimes[$extension])) {
            return false;
        }

        // ตรวจสอบ signature ของไฟล์ (Magic bytes)
        $handle = fopen($file_path, 'rb');
        $header = fread($handle, 10);
        fclose($handle);

        $signatures = [
            'pdf' => ['%PDF'],
            'jpg' => ["\xFF\xD8\xFF"],
            'jpeg' => ["\xFF\xD8\xFF"],
            'png' => ["\x89PNG\r\n\x1a\n"],
            'gif' => ["GIF87a", "GIF89a"]
        ];

        if (isset($signatures[$extension])) {
            $valid = false;
            foreach ($signatures[$extension] as $signature) {
                if (strpos($header, $signature) === 0) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                return false;
            }
        }

        return true;
    }

    /**
     * ดึง MIME type
     */
    private function get_mime_type($extension)
    {
        $mime_types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf'
        ];

        return $mime_types[$extension] ?? 'application/octet-stream';
    }

    /**
     * คำนวณจำนวนตามสถานะ
     */
    private function calculate_status_counts($kid_list)
    {
        $counts = ['total' => 0, 'submitted' => 0, 'reviewing' => 0, 'approved' => 0, 'rejected' => 0, 'completed' => 0];

        foreach ($kid_list as $kid) {
            $counts['total']++;
            $status = is_array($kid) ? ($kid['kid_aw_ods_status'] ?? 'submitted') : ($kid->kid_aw_ods_status ?? 'submitted');
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }

        return $counts;
    }



    /**
     * เตรียมข้อมูลสำหรับแสดงผล
     */
    private function prepare_kid_for_display($kid)
    {
        // *** แก้ไข: ตรวจสอบและแปลงประเภทข้อมูล ***
        if (is_object($kid)) {
            $kid = (array) $kid;
        }

        // ตรวจสอบว่าเป็น array หรือไม่
        if (!is_array($kid)) {
            log_message('error', 'prepare_kid_for_display: Input is not array or object');
            return [];
        }

        $status_map = [
            'submitted' => 'ยื่นเรื่องแล้ว',
            'reviewing' => 'กำลังพิจารณา',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
            'completed' => 'เสร็จสิ้น'
        ];

        $type_map = [
            'children' => 'เด็กทั่วไป',
            'disabled' => 'เด็กพิการ'
        ];

        // เพิ่มข้อมูลพื้นฐาน
        $kid['status_display'] = $status_map[$kid['kid_aw_ods_status'] ?? 'submitted'] ?? 'ยื่นเรื่องแล้ว';
        $kid['type_display'] = $type_map[$kid['kid_aw_ods_type'] ?? 'children'] ?? 'เด็กทั่วไป';
        $kid['status_class'] = 'status-' . ($kid['kid_aw_ods_status'] ?? 'submitted');

        // *** เพิ่ม: ข้อมูลสถานะการแก้ไข ***
        $current_status = $kid['kid_aw_ods_status'] ?? 'submitted';
        $editable_statuses = ['submitted', 'reviewing'];
        $kid['can_edit'] = in_array($current_status, $editable_statuses);
        $kid['edit_reason'] = $kid['can_edit'] ? '' : 'ไม่สามารถแก้ไขได้ในสถานะ' . $kid['status_display'];

        // *** เพิ่ม: ข้อมูลเวลา ***
        $kid['time_since_created'] = $this->calculate_time_since($kid['kid_aw_ods_datesave'] ?? '');
        $kid['last_updated'] = $kid['kid_aw_ods_updated_at'] ?? $kid['kid_aw_ods_datesave'] ?? '';

        // *** เพิ่ม: สถานะไอคอนและสี ***
        $status_icons = [
            'submitted' => 'fas fa-file-alt',
            'reviewing' => 'fas fa-search',
            'approved' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            'completed' => 'fas fa-trophy'
        ];

        $status_colors = [
            'submitted' => '#ffc107',
            'reviewing' => '#17a2b8',
            'approved' => '#28a745',
            'rejected' => '#dc3545',
            'completed' => '#6f42c1'
        ];

        $kid['status_icon'] = $status_icons[$current_status] ?? 'fas fa-file-alt';
        $kid['status_color'] = $status_colors[$current_status] ?? '#ffc107';

        return $kid;
    }


    private function ensure_object($data)
    {
        if (is_array($data)) {
            return (object) $data;
        }

        if (is_object($data)) {
            return $data;
        }

        return new stdClass();
    }


    private function ensure_array($data)
    {
        if (is_object($data)) {
            return (array) $data;
        }

        if (is_array($data)) {
            return $data;
        }

        return [];
    }





    private function calculate_time_since($datetime)
    {
        if (empty($datetime)) {
            return 'ไม่ระบุ';
        }

        try {
            $created_time = new DateTime($datetime);
            $current_time = new DateTime();
            $interval = $current_time->diff($created_time);

            if ($interval->d > 0) {
                return $interval->d . ' วันที่แล้ว';
            } elseif ($interval->h > 0) {
                return $interval->h . ' ชั่วโมงที่แล้ว';
            } elseif ($interval->i > 0) {
                return $interval->i . ' นาทีที่แล้ว';
            } else {
                return 'เมื่อสักครู่';
            }
        } catch (Exception $e) {
            return 'ไม่ระบุ';
        }
    }





    private function json_response($data, $exit = true)
    {
        // *** ลบ output buffer ทั้งหมดก่อน ***
        while (ob_get_level()) {
            ob_end_clean();
        }

        // *** ตั้งค่า Content-Type เป็น JSON ***
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // *** ตรวจสอบข้อมูลก่อนส่ง ***
        if (!is_array($data)) {
            $data = ['success' => false, 'message' => 'Invalid response data'];
        }

        // *** เพิ่มข้อมูล debug (เฉพาะ development) ***
        if (ENVIRONMENT === 'development' && !isset($data['debug'])) {
            $data['debug'] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'memory_usage' => memory_get_usage(true),
                'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
            ];
        }

        // *** ส่ง JSON และหยุดการทำงาน ***
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if ($exit) {
            exit();
        }
    }

    /**
     * API: ส่งข้อมูลสถิติเงินสนับสนุนเด็กสำหรับ Dashboard
     */
    public function api_kid_summary()
    {
        try {
            // ตรวจสอบสิทธิ์ (ถ้าจำเป็น)
            if ($this->input->server('REQUEST_METHOD') !== 'GET') {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // นับสถิติตามสถานะ
            $statistics = [
                'total' => 0,
                'submitted' => 0,
                'reviewing' => 0,
                'completed' => 0,
                'approved' => 0,
                'rejected' => 0
            ];

            // ดึงข้อมูลสถิติจากฐานข้อมูล
            if ($this->db->table_exists('tbl_kid_aw_ods')) {
                // นับทั้งหมด
                $this->db->from('tbl_kid_aw_ods');
                $statistics['total'] = $this->db->count_all_results();

                // นับตามสถานะแต่ละแบบ
                $statuses = ['submitted', 'reviewing', 'approved', 'rejected', 'completed'];

                foreach ($statuses as $status) {
                    $this->db->from('tbl_kid_aw_ods');
                    $this->db->where('kid_aw_ods_status', $status);
                    $statistics[$status] = $this->db->count_all_results();
                }
            }

            // จัดรูปแบบข้อมูลสำหรับ Dashboard
            $response_data = [
                'success' => true,
                'kid_allowance' => [
                    'total' => (int) $statistics['total'],
                    'submitted' => (int) $statistics['submitted'],
                    'reviewing' => (int) $statistics['reviewing'],
                    'completed' => (int) $statistics['completed']
                ],
                'timestamp' => date('Y-m-d H:i:s'),
                'last_updated' => date('c')
            ];

            // ส่ง JSON Response
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

            echo json_encode($response_data, JSON_UNESCAPED_UNICODE);

            log_message('info', 'Kid summary API called - Total: ' . $statistics['total']);

        } catch (Exception $e) {
            log_message('error', 'Error in api_kid_summary: ' . $e->getMessage());

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Server error',
                'kid_allowance' => [
                    'total' => 0,
                    'submitted' => 0,
                    'reviewing' => 0,
                    'completed' => 0
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * *** เพิ่มฟังก์ชัน debug session สำหรับ JavaScript ***
     */
    public function debug_session_js()
    {
        // เฉพาะ development mode
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }

        $current_user = $this->get_current_user_detailed();

        $debug_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'session_id' => session_id(),
            'session_data_count' => count($this->session->all_userdata()),
            'session_values' => [
                'mp_id' => $this->session->userdata('mp_id'),
                'mp_email' => $this->session->userdata('mp_email'),
                'm_id' => $this->session->userdata('m_id'),
                'm_email' => $this->session->userdata('m_email')
            ],
            'current_user' => $current_user,
            'controller_result' => [
                'is_logged_in' => $current_user['is_logged_in'],
                'user_type' => $current_user['user_type'],
                'user_id' => $current_user['user_info']['id'] ?? null,
                'user_name' => $current_user['user_info']['name'] ?? null,
                'has_address' => !empty($current_user['user_address'])
            ]
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($debug_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }




    /**
     * ฟังก์ชันเช็คสิทธิ์การจัดการเงินสนับสนุนเด็ก
     */
    private function check_kid_handle_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถดำเนินการได้ทุกอย่าง
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Kid handle permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 53 
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('warning', "user_admin without grant_user_ref_id: {$staff_data->m_fname} {$staff_data->m_lname}");
                    return false;
                }

                // แปลง grant_user_ref_id เป็น array (กรณีมีหลายสิทธิ์คั่นด้วย comma)
                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));

                // เช็คว่ามีสิทธิ์ 53 หรือไม่ (สำหรับเงินสนับสนุนเด็ก)
                $has_permission = in_array('53', $grant_ids);

                log_message('info', "user_admin kid permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "grant_ids: " . implode(',', $grant_ids) . " - " .
                    "has_53: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            // end_user หรือระดับอื่นๆ ไม่สามารถดำเนินการได้
            log_message('info', "Kid handle permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking kid handle permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ฟังก์ชันเช็คสิทธิ์การอนุมัติเงินสนับสนุนเด็ก
     */
    private function check_kid_approve_permission($staff_data)
    {
        try {
            // เฉพาะ system_admin และ super_admin ที่สามารถอนุมัติได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Kid approve permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 53 (สำหรับการอนุมัติ)
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    return false;
                }

                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));

                // เช็คว่ามีสิทธิ์ 53 หรือไม่ (สำหรับการอนุมัติเงินสนับสนุนเด็ก)
                $has_permission = in_array('53', $grant_ids);

                log_message('info', "user_admin approve permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "has_53: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking kid approve permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** แสดงรายละเอียดเงินสนับสนุนเด็ก - สำหรับเจ้าหน้าที่ ***
     */
    public function kid_detail($kid_aw_ods_id = null)
    {
        try {
            // *** แก้ไข: ตรวจสอบเฉพาะ Staff login แต่ไม่เช็คสิทธิ์ในการเข้าหน้า ***
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ และดึงข้อมูลระดับสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id, m_email, m_phone, m_username, m_img');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1'); // ต้องเป็น active เท่านั้น
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
                redirect('User');
                return;
            }

            // *** แก้ไข: ตรวจสอบสิทธิ์สำหรับการ Update/Delete เท่านั้น ***
            $can_update_status = $this->check_kid_update_permission($staff_check);
            $can_delete_kid = $this->check_kid_delete_permission($staff_check);

            // ตรวจสอบ kid_aw_ods_id
            if (empty($kid_aw_ods_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขอ้างอิงเงินสนับสนุนเด็ก');
                redirect('Kid_aw_ods/kid_aw_ods');
                return;
            }

            // ดึงข้อมูลเงินสนับสนุนเด็ก
            $kid_aw_ods_detail = $this->Kid_aw_ods_model->get_kid_aw_ods_detail_for_staff($kid_aw_ods_id);

            if (!$kid_aw_ods_detail) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเงินสนับสนุนเด็กที่ระบุ');
                redirect('Kid_aw_ods/kid_aw_ods');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_update_status'] = $can_update_status;
            $data['can_delete_kid'] = $can_delete_kid;
            $data['can_handle_kid'] = true; // *** ทุกคนดูได้ ***
            $data['can_approve_kid'] = $can_update_status; // *** ใช้สิทธิ์เดียวกับ update ***
            $data['staff_system_level'] = $staff_check->m_system;

            // *** สร้าง user_info object ที่ครบถ้วนสำหรับ header.php ***
            $user_info_object = $this->create_complete_user_info($staff_check);

            // ข้อมูลเงินสนับสนุนเด็ก
            $data['kid_aw_ods_detail'] = $kid_aw_ods_detail;

            // ข้อมูลเจ้าหน้าที่
            $data['staff_info'] = [
                'id' => $staff_check->m_id,
                'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                'system' => $staff_check->m_system,
                'can_delete' => $data['can_delete_kid'],
                'can_handle' => $data['can_handle_kid'],
                'can_approve' => $data['can_approve_kid'],
                'can_update_status' => $data['can_update_status']
            ];

            // ข้อมูลผู้ใช้สำหรับ header
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;

            // *** เพิ่มตัวแปรเสริมสำหรับ header.php ***
            $data['current_user'] = $user_info_object;
            $data['logged_user'] = $user_info_object;
            $data['session_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;
            $data['member_data'] = $user_info_object;

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Kid_aw_ods/kid_aw_ods')],
                ['title' => 'จัดการเงินสนับสนุนเด็ก', 'url' => site_url('Kid_aw_ods/kid_aw_ods')],
                ['title' => 'รายละเอียด #' . $kid_aw_ods_id, 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'รายละเอียดเงินสนับสนุนเด็ก #' . $kid_aw_ods_id;

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Kid AW ODS detail data: ' . json_encode([
                    'kid_id' => $kid_aw_ods_id,
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_kid'],
                    'can_handle' => $data['can_handle_kid'],
                    'can_approve' => $data['can_approve_kid'],
                    'can_update_status' => $data['can_update_status'],
                    'kid_status' => $kid_aw_ods_detail->kid_aw_ods_status ?? 'unknown',
                    'kid_type' => $kid_aw_ods_detail->kid_aw_ods_type ?? 'unknown',
                    'kid_by' => $kid_aw_ods_detail->kid_aw_ods_by ?? 'unknown',
                    'files_count' => count($kid_aw_ods_detail->files ?? []),
                    'history_count' => count($kid_aw_ods_detail->history ?? [])
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/kid_aw_ods_detail', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in kid_detail: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ารายละเอียด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Kid_aw_ods/kid_aw_ods');
            }
        }
    }

    /**
     * *** อัปเดตสถานะเงินสนับสนุนเด็ก (AJAX) ***
     */
    public function update_kid_status()
    {
        // *** ป้องกัน output ใดๆ ก่อนส่ง JSON ***
        ob_start();

        try {
            // ตรวจสอบ HTTP method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบ Content-Type
            $content_type = $this->input->server('CONTENT_TYPE');
            if (
                strpos($content_type, 'application/x-www-form-urlencoded') === false &&
                strpos($content_type, 'multipart/form-data') === false
            ) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid content type']);
                return;
            }

            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                return;
            }

            // ตรวจสอบข้อมูลที่ได้รับ
            $kid_id = $this->input->post('kid_id');
            $new_status = $this->input->post('new_status');
            $note = $this->input->post('note') ?: '';
            $new_priority = $this->input->post('new_priority') ?: 'normal';

            // Debug log สำหรับ development
            if (ENVIRONMENT === 'development') {
                log_message('info', 'Update kid status request: ' . json_encode([
                    'kid_id' => $kid_id,
                    'new_status' => $new_status,
                    'note' => $note,
                    'new_priority' => $new_priority,
                    'staff_id' => $m_id
                ]));
            }

            // Validation
            if (empty($kid_id) || empty($new_status)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            $allowed_statuses = ['submitted', 'reviewing', 'approved', 'rejected', 'completed'];
            if (!in_array($new_status, $allowed_statuses)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $this->db->select('m_fname, m_lname, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_kid_handle_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์อัปเดตสถานะ']);
                return;
            }

            // *** ดึงข้อมูลเงินสนับสนุนเด็กเก่าก่อนอัปเดต ***
            $kid_data = $this->Kid_aw_ods_model->get_kid_aw_ods_by_id($kid_id);
            if (!$kid_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเงินสนับสนุนเด็ก']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);
            $old_status = $kid_data->kid_aw_ods_status;

            // ตรวจสอบว่าสถานะเปลี่ยนแปลงจริงๆ
            if ($old_status === $new_status) {
                ob_end_clean();
                $this->json_response([
                    'success' => true,
                    'message' => 'สถานะไม่มีการเปลี่ยนแปลง',
                    'new_status' => $new_status,
                    'updated_by' => $updated_by
                ]);
                return;
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตสถานะ
            $update_result = $this->Kid_aw_ods_model->update_kid_aw_ods_status(
                $kid_id,
                $new_status,
                $updated_by,
                $note
            );

            if (!$update_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถอัปเดตสถานะได้']);
                return;
            }

            // อัปเดตความสำคัญ (ถ้ามี)
            if (!empty($new_priority) && $new_priority !== 'normal') {
                $this->db->where('kid_aw_ods_id', $kid_id);
                $this->db->update('tbl_kid_aw_ods', [
                    'kid_aw_ods_priority' => $new_priority
                ]);
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
                return;
            }

            // *** สร้างการแจ้งเตือนสำหรับการอัปเดต (ไม่บังคับ) ***
            try {
                $this->create_kid_aw_ods_update_notifications(
                    $kid_id,
                    $kid_data,
                    $old_status,
                    $new_status,
                    $updated_by,
                    $staff_data,
                    $note
                );
                log_message('info', "Update notifications sent for kid_aw_ods {$kid_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create update notifications: ' . $e->getMessage());
                // ไม่ให้ notification error ทำให้การอัปเดตล้มเหลว
            }

            ob_end_clean();

            log_message('info', "Kid status updated successfully: {$kid_id} from {$old_status} to {$new_status} by {$updated_by}");

            // *** ส่ง JSON Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'อัปเดตสถานะสำเร็จ',
                'new_status' => $new_status,
                'old_status' => $old_status,
                'updated_by' => $updated_by,
                'kid_id' => $kid_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in update_kid_status: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'UPDATE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }

    /**
     * *** ใหม่: สร้างการแจ้งเตือนสำหรับการอัปเดตสถานะเงินสนับสนุนเด็ก ***
     */
    private function create_kid_aw_ods_update_notifications($kid_id, $kid_data, $old_status, $new_status, $updated_by, $staff_data, $note = '')
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('warning', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($kid_data->kid_aw_ods_type === 'disabled') ? 'เด็กพิการ' : 'เด็กทั่วไป';

            // แปลสถานะเป็นภาษาไทย
            $status_map = [
                'submitted' => 'ยื่นเรื่องแล้ว',
                'reviewing' => 'กำลังพิจารณา',
                'approved' => 'อนุมัติแล้ว',
                'rejected' => 'ไม่อนุมัติ',
                'completed' => 'เสร็จสิ้น'
            ];

            $old_status_display = $status_map[$old_status] ?? $old_status;
            $new_status_display = $status_map[$new_status] ?? $new_status;

            // สร้างข้อความอัปเดต
            $update_message = "เปลี่ยนสถานะจาก '{$old_status_display}' เป็น '{$new_status_display}'";
            if (!empty($note)) {
                $update_message .= " - หมายเหตุ: {$note}";
            }

            // 1. แจ้งเตือน Staff ทั้งหมด
            $staff_data_json = json_encode([
                'kid_aw_ods_id' => $kid_id,
                'type' => $kid_data->kid_aw_ods_type,
                'type_display' => $type_display,
                'requester' => $kid_data->kid_aw_ods_by,
                'phone' => $kid_data->kid_aw_ods_phone,
                'old_status' => $old_status,
                'new_status' => $new_status,
                'old_status_display' => $old_status_display,
                'new_status_display' => $new_status_display,
                'updated_by' => $updated_by,
                'note' => $note,
                'timestamp' => $current_time,
                'notification_type' => 'staff_update_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'kid_aw_ods_update',
                'title' => 'อัปเดตเงินสนับสนุนเด็ก',
                'message' => "เงินสนับสนุน{$type_display} #{$kid_id} {$update_message} โดย {$updated_by}",
                'reference_id' => 0,
                'reference_table' => 'tbl_kid_aw_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-edit',
                'url' => site_url("Kid_aw_ods/kid_detail/{$kid_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff update notification created for kid_aw_ods: {$kid_id}");
            }

            // 2. แจ้งเตือนเจ้าของเรื่อง (ถ้าเป็น public user)
            if (
                $kid_data->kid_aw_ods_user_type === 'public' &&
                !empty($kid_data->kid_aw_ods_user_id)
            ) {

                // สร้างข้อความสำหรับผู้ใช้
                $user_message = '';
                switch ($new_status) {
                    case 'reviewing':
                        $user_message = "เงินสนับสนุน{$type_display}ของคุณอยู่ระหว่างการพิจารณา";
                        break;
                    case 'approved':
                        $user_message = "เงินสนับสนุน{$type_display}ของคุณได้รับการอนุมัติแล้ว";
                        break;
                    case 'rejected':
                        $user_message = "เงินสนับสนุน{$type_display}ของคุณไม่ได้รับการอนุมัติ";
                        break;
                    case 'completed':
                        $user_message = "เงินสนับสนุน{$type_display}ของคุณดำเนินการเสร็จสิ้นแล้ว";
                        break;
                    default:
                        $user_message = "สถานะเงินสนับสนุน{$type_display}ของคุณมีการเปลี่ยนแปลง";
                }

                $user_data_json = json_encode([
                    'kid_aw_ods_id' => $kid_id,
                    'type' => $kid_data->kid_aw_ods_type,
                    'type_display' => $type_display,
                    'new_status' => $new_status,
                    'new_status_display' => $new_status_display,
                    'updated_by' => $updated_by,
                    'timestamp' => $current_time,
                    'notification_type' => 'user_update_notification'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification = [
                    'type' => 'kid_aw_ods_update',
                    'title' => 'อัปเดตเงินสนับสนุนเด็กของคุณ',
                    'message' => $user_message . " หมายเลขอ้างอิง: {$kid_id}",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_kid_aw_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($kid_data->kid_aw_ods_user_id),
                    'priority' => 'high',
                    'icon' => $this->get_status_icon($new_status),
                    'url' => site_url("Kid_aw_ods/my_kid_aw_ods_detail/{$kid_id}"),
                    'data' => $user_data_json,
                    'created_at' => $current_time,
                    'created_by' => $staff_data->m_id,
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $user_result = $this->db->insert('tbl_notifications', $user_notification);

                if ($user_result) {
                    log_message('info', "User update notification created for kid_aw_ods: {$kid_id}");
                }
            }

            log_message('info', "Kid AW ODS update notifications created: {$kid_id}");

        } catch (Exception $e) {
            log_message('error', 'Error creating kid_aw_ods update notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * *** ลบข้อมูลเงินสนับสนุนเด็ก (AJAX) ***
     */
    public function delete_kid()
    {
        // *** ป้องกัน output ใดๆ ก่อนส่ง JSON ***
        ob_start();

        try {
            // ตรวจสอบ HTTP method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                return;
            }

            // ตรวจสอบข้อมูลที่ได้รับ
            $kid_id = $this->input->post('kid_id');
            $delete_reason = $this->input->post('delete_reason') ?: '';

            if (empty($kid_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบหมายเลขอ้างอิง']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $this->db->select('m_id, m_fname, m_lname, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การลบ (เฉพาะ system_admin และ super_admin)
            if (!in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูล']);
                return;
            }

            // *** ดึงข้อมูลเงินสนับสนุนเด็กก่อนลบ ***
            $kid_data = $this->Kid_aw_ods_model->get_kid_aw_ods_by_id($kid_id);
            if (!$kid_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเงินสนับสนุนเด็ก']);
                return;
            }

            $deleted_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เริ่ม Transaction
            $this->db->trans_start();

            // *** สร้างการแจ้งเตือนก่อนลบ (ไม่บังคับ) ***
            try {
                $this->create_kid_aw_ods_delete_notifications(
                    $kid_id,
                    $kid_data,
                    $deleted_by,
                    $staff_data,
                    $delete_reason
                );
                log_message('info', "Delete notifications created for kid_aw_ods {$kid_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create delete notifications: ' . $e->getMessage());
            }

            // บันทึกประวัติก่อนลบ
            if (!empty($delete_reason) && method_exists($this->Kid_aw_ods_model, 'add_kid_aw_ods_history')) {
                $this->Kid_aw_ods_model->add_kid_aw_ods_history(
                    $kid_id,
                    'deleted',
                    'ลบข้อมูล - เหตุผล: ' . $delete_reason,
                    $deleted_by,
                    $kid_data->kid_aw_ods_status,
                    'deleted'
                );
            }

            // ลบข้อมูล
            $delete_result = $this->Kid_aw_ods_model->delete_kid_aw_ods($kid_id, $deleted_by);

            if (!$delete_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลได้']);
                return;
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล']);
                return;
            }

            ob_end_clean();

            log_message('info', "Kid deleted successfully: {$kid_id} by {$deleted_by}");

            // *** ส่ง JSON Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'ลบข้อมูลสำเร็จ',
                'deleted_by' => $deleted_by,
                'kid_id' => $kid_id,
                'delete_reason' => $delete_reason,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in delete_kid: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'DELETE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }

    /**
     * *** ใหม่: สร้างการแจ้งเตือนสำหรับการลบเงินสนับสนุนเด็ก ***
     */
    private function create_kid_aw_ods_delete_notifications($kid_id, $kid_data, $deleted_by, $staff_data, $delete_reason = '')
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('warning', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($kid_data->kid_aw_ods_type === 'disabled') ? 'เด็กพิการ' : 'เด็กทั่วไป';

            // สร้างข้อความการลบ
            $delete_message = "เงินสนับสนุน{$type_display} #{$kid_id} ถูกลบโดย {$deleted_by}";
            if (!empty($kid_data->kid_aw_ods_by)) {
                $delete_message .= " (ผู้ยื่น: {$kid_data->kid_aw_ods_by})";
            }
            if (!empty($delete_reason)) {
                $delete_message .= " เหตุผล: {$delete_reason}";
            }

            // สร้างข้อมูล JSON
            $notification_data_json = json_encode([
                'kid_aw_ods_id' => $kid_id,
                'type' => $kid_data->kid_aw_ods_type,
                'type_display' => $type_display,
                'deleted_by' => $deleted_by,
                'deleted_by_system' => $staff_data->m_system,
                'delete_reason' => $delete_reason ?: 'ไม่ระบุเหตุผล',
                'original_requester' => $kid_data->kid_aw_ods_by,
                'original_phone' => $kid_data->kid_aw_ods_phone,
                'original_status' => $kid_data->kid_aw_ods_status,
                'timestamp' => $current_time,
                'notification_type' => 'kid_deleted'
            ], JSON_UNESCAPED_UNICODE);

            // แจ้งเตือน Staff ทั้งหมด
            $staff_notification = [
                'type' => 'kid_aw_ods_deleted',
                'title' => 'เงินสนับสนุนเด็กถูกลบ',
                'message' => $delete_message,
                'reference_id' => 0, // เนื่องจากถูกลบแล้ว
                'reference_table' => 'tbl_kid_aw_ods',
                'target_role' => 'staff',
                'priority' => 'high',
                'icon' => 'fas fa-trash',
                'url' => site_url("Kid_aw_ods/kid_aw_ods"),
                'data' => $notification_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $notification_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($notification_result) {
                log_message('info', "Delete notification created successfully for kid_aw_ods: {$kid_id}");
            } else {
                $db_error = $this->db->error();
                log_message('error', "Failed to create delete notification for kid_aw_ods: {$kid_id}. DB Error: " . print_r($db_error, true));
            }

            // บันทึก Log การลบ
            log_message('info', "Kid AW ODS deleted successfully by {$deleted_by} ({$staff_data->m_system}): ID={$kid_id}, Type={$kid_data->kid_aw_ods_type}, Requester=" . ($kid_data->kid_aw_ods_by ?? 'N/A') . ", Reason=" . ($delete_reason ?: 'ไม่ระบุเหตุผล'));

        } catch (Exception $e) {
            log_message('error', 'Error creating kid_aw_ods delete notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * *** เพิ่มหมายเหตุ (AJAX) ***
     */
    public function add_note()
    {
        // *** ป้องกัน output ใดๆ ก่อนส่ง JSON ***
        ob_start();

        try {
            // ตรวจสอบ HTTP method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                return;
            }

            // ตรวจสอบข้อมูลที่ได้รับ
            $kid_id = $this->input->post('kid_id');
            $note = $this->input->post('note');

            if (empty($kid_id) || empty($note)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $this->db->select('m_id, m_fname, m_lname, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_kid_handle_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เพิ่มหมายเหตุ']);
                return;
            }

            // *** ดึงข้อมูลเงินสนับสนุนเด็ก ***
            $kid_data = $this->Kid_aw_ods_model->get_kid_aw_ods_by_id($kid_id);
            if (!$kid_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเงินสนับสนุนเด็ก']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // ดึงหมายเหตุเดิม
            $this->db->select('kid_aw_ods_notes');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_id);
            $existing_data = $this->db->get()->row();

            $old_notes = $existing_data->kid_aw_ods_notes ?? '';
            $new_notes = $old_notes;

            if (!empty($old_notes)) {
                $new_notes .= "\n\n" . "--- " . date('d/m/Y H:i') . " โดย {$updated_by} ---\n" . $note;
            } else {
                $new_notes = "--- " . date('d/m/Y H:i') . " โดย {$updated_by} ---\n" . $note;
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตหมายเหตุ
            $this->db->where('kid_aw_ods_id', $kid_id);
            $update_result = $this->db->update('tbl_kid_aw_ods', [
                'kid_aw_ods_notes' => $new_notes,
                'kid_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'kid_aw_ods_updated_by' => $updated_by
            ]);

            if (!$update_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถเพิ่มหมายเหตุได้']);
                return;
            }

            // บันทึกประวัติ
            if (method_exists($this->Kid_aw_ods_model, 'add_kid_aw_ods_history')) {
                $this->Kid_aw_ods_model->add_kid_aw_ods_history(
                    $kid_id,
                    'note_added',
                    'เพิ่มหมายเหตุ: ' . mb_substr($note, 0, 100) . (mb_strlen($note) > 100 ? '...' : ''),
                    $updated_by,
                    null,
                    null
                );
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
                return;
            }

            // *** สร้างการแจ้งเตือนสำหรับการเพิ่มหมายเหตุ (ไม่บังคับ) ***
            try {
                $this->create_kid_aw_ods_note_notifications(
                    $kid_id,
                    $kid_data,
                    $note,
                    $updated_by,
                    $staff_data
                );
                log_message('info', "Note notifications sent for kid_aw_ods {$kid_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create note notifications: ' . $e->getMessage());
                // ไม่ให้ notification error ทำให้การเพิ่มหมายเหตุล้มเหลว
            }

            ob_end_clean();

            log_message('info', "Note added successfully to kid: {$kid_id} by {$updated_by}");

            // *** ส่ง JSON Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'เพิ่มหมายเหตุสำเร็จ',
                'updated_by' => $updated_by,
                'kid_id' => $kid_id,
                'note_preview' => mb_substr($note, 0, 50) . (mb_strlen($note) > 50 ? '...' : ''),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in add_note: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'ADD_NOTE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }

    /**
     * *** ใหม่: สร้างการแจ้งเตือนสำหรับการเพิ่มหมายเหตุ ***
     */
    private function create_kid_aw_ods_note_notifications($kid_id, $kid_data, $note, $updated_by, $staff_data)
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('warning', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($kid_data->kid_aw_ods_type === 'disabled') ? 'เด็กพิการ' : 'เด็กทั่วไป';

            // ตัดข้อความหมายเหตุให้สั้น
            $short_note = mb_substr($note, 0, 100);
            if (mb_strlen($note) > 100) {
                $short_note .= '...';
            }

            // 1. แจ้งเตือน Staff ทั้งหมด
            $staff_data_json = json_encode([
                'kid_aw_ods_id' => $kid_id,
                'type' => $kid_data->kid_aw_ods_type,
                'type_display' => $type_display,
                'requester' => $kid_data->kid_aw_ods_by,
                'phone' => $kid_data->kid_aw_ods_phone,
                'note' => $note,
                'note_preview' => $short_note,
                'updated_by' => $updated_by,
                'timestamp' => $current_time,
                'notification_type' => 'staff_note_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'kid_aw_ods_note',
                'title' => 'หมายเหตุใหม่ - เงินสนับสนุนเด็ก',
                'message' => "มีหมายเหตุใหม่ในเงินสนับสนุน{$type_display} #{$kid_id}: \"{$short_note}\" โดย {$updated_by}",
                'reference_id' => 0,
                'reference_table' => 'tbl_kid_aw_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-sticky-note',
                'url' => site_url("Kid_aw_ods/kid_detail/{$kid_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff note notification created for kid_aw_ods: {$kid_id}");
            }

            // 2. แจ้งเตือนเจ้าของเรื่อง (ถ้าเป็น public user) - เฉพาะหมายเหตุที่สำคัญ
            if (
                $kid_data->kid_aw_ods_user_type === 'public' &&
                !empty($kid_data->kid_aw_ods_user_id) &&
                (stripos($note, 'อนุมัติ') !== false ||
                    stripos($note, 'ไม่อนุมัติ') !== false ||
                    stripos($note, 'เสร็จสิ้น') !== false ||
                    stripos($note, 'ติดต่อ') !== false)
            ) {

                $user_data_json = json_encode([
                    'kid_aw_ods_id' => $kid_id,
                    'type' => $kid_data->kid_aw_ods_type,
                    'type_display' => $type_display,
                    'note_preview' => $short_note,
                    'updated_by' => $updated_by,
                    'timestamp' => $current_time,
                    'notification_type' => 'user_note_notification'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification = [
                    'type' => 'kid_aw_ods_note',
                    'title' => 'ข้อมูลเพิ่มเติมเงินสนับสนุนเด็ก',
                    'message' => "มีข้อมูลเพิ่มเติมเกี่ยวกับเงินสนับสนุน{$type_display} หมายเลขอ้างอิง: {$kid_id}",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_kid_aw_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($kid_data->kid_aw_ods_user_id),
                    'priority' => 'high',
                    'icon' => 'fas fa-info-circle',
                    'url' => site_url("Kid_aw_ods/my_kid_aw_ods_detail/{$kid_id}"),
                    'data' => $user_data_json,
                    'created_at' => $current_time,
                    'created_by' => $staff_data->m_id,
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $user_result = $this->db->insert('tbl_notifications', $user_notification);

                if ($user_result) {
                    log_message('info', "User note notification created for kid_aw_ods: {$kid_id}");
                }
            }

            log_message('info', "Kid AW ODS note notifications created: {$kid_id}");

        } catch (Exception $e) {
            log_message('error', 'Error creating kid_aw_ods note notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * *** หน้าติดตามสถานะเงินสนับสนุนเด็กสำหรับ Admin (ชื่อใหม่) ***
     */
    public function kid_tracking_admin()
    {
        try {
            // *** แก้ไข: ตรวจสอบเฉพาะ Staff login แต่ไม่เช็คสิทธิ์ในการเข้าหน้า ***
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ และดึงข้อมูลระดับสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id, m_email, m_phone, m_username, m_img');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1'); // ต้องเป็น active เท่านั้น
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
                redirect('User');
                return;
            }

            // *** แก้ไข: ตรวจสอบสิทธิ์สำหรับการ Update/Delete เท่านั้น ***
            $can_update_status = $this->check_kid_update_permission($staff_check);
            $can_delete_kid = $this->check_kid_delete_permission($staff_check);

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_update_status'] = $can_update_status;
            $data['can_delete_kid'] = $can_delete_kid;
            $data['can_handle_kid'] = true; // *** ทุกคนดูได้ ***
            $data['can_approve_kid'] = $can_update_status; // *** ใช้สิทธิ์เดียวกับ update ***
            $data['staff_system_level'] = $staff_check->m_system;

            // *** สร้าง user_info object ที่ครบถ้วนสำหรับ header.php ***
            $user_info_object = $this->create_complete_user_info($staff_check);

            // ตรวจสอบ reference ID จาก URL parameter หรือ POST
            $ref_id = $this->input->get('ref') ?: $this->input->post('kid_id');
            $search_phone = $this->input->get('phone') ?: $this->input->post('phone');
            $search_id_card = $this->input->get('id_card') ?: $this->input->post('id_card');

            $data['ref_id'] = $ref_id;
            $data['search_phone'] = $search_phone;
            $data['search_id_card'] = $search_id_card;

            // ค้นหาข้อมูลเงินสนับสนุนเด็ก
            $kid_aw_ods_results = [];
            $search_performed = false;

            if (!empty($ref_id)) {
                // ค้นหาด้วยหมายเลขอ้างอิง
                try {
                    $kid_result = $this->Kid_aw_ods_model->get_kid_aw_ods_detail_for_staff($ref_id);
                    if ($kid_result) {
                        $kid_aw_ods_results[] = $kid_result;
                        $search_performed = true;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching kid by ID: ' . $e->getMessage());
                }
            } elseif (!empty($search_phone)) {
                // ค้นหาด้วยเบอร์โทร
                try {
                    $kid_list = $this->search_kid_by_phone($search_phone);
                    if (!empty($kid_list)) {
                        $kid_aw_ods_results = $kid_list;
                        $search_performed = true;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching kid by phone: ' . $e->getMessage());
                }
            } elseif (!empty($search_id_card)) {
                // ค้นหาด้วยเลขบัตรประชาชน
                try {
                    $kid_list = $this->search_kid_by_id_card($search_id_card);
                    if (!empty($kid_list)) {
                        $kid_aw_ods_results = $kid_list;
                        $search_performed = true;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching kid by ID card: ' . $e->getMessage());
                }
            }

            // เตรียมข้อมูลผลลัพธ์สำหรับแสดงผล
            if (!empty($kid_aw_ods_results)) {
                $data['kid_aw_ods_results'] = $this->prepare_kid_aw_ods_list_for_display($kid_aw_ods_results);
            } else {
                $data['kid_aw_ods_results'] = [];
            }

            $data['search_performed'] = $search_performed;
            $data['total_results'] = count($kid_aw_ods_results);

            // ข้อมูลเจ้าหน้าที่
            $data['staff_info'] = [
                'id' => $staff_check->m_id,
                'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                'system' => $staff_check->m_system,
                'can_delete' => $data['can_delete_kid'],
                'can_handle' => $data['can_handle_kid'],
                'can_approve' => $data['can_approve_kid'],
                'can_update_status' => $data['can_update_status']
            ];

            // ข้อมูลผู้ใช้สำหรับ header
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;

            // *** เพิ่มตัวแปรเสริมสำหรับ header.php ***
            $data['current_user'] = $user_info_object;
            $data['logged_user'] = $user_info_object;
            $data['session_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;
            $data['member_data'] = $user_info_object;

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Kid_aw_ods/kid_aw_ods')],
                ['title' => 'จัดการเงินสนับสนุนเด็ก', 'url' => site_url('Kid_aw_ods/kid_aw_ods')],
                ['title' => 'ติดตามสถานะ (Admin)', 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'ติดตามสถานะเงินสนับสนุนเด็ก (สำหรับเจ้าหน้าที่)';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Kid tracking admin data: ' . json_encode([
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_update_status' => $can_update_status,
                    'can_delete_kid' => $can_delete_kid,
                    'search_performed' => $search_performed,
                    'total_results' => $data['total_results'],
                    'ref_id' => $ref_id,
                    'search_phone' => $search_phone,
                    'search_id_card' => $search_id_card
                ]));
            }

            // โหลด View (สำหรับ Admin)
            $this->load->view('reports/header', $data);
            $this->load->view('reports/kid_aw_ods_tracking_admin', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in kid_tracking_admin: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าติดตามสถานะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Kid_aw_ods/kid_aw_ods');
            }
        }
    }

    /**
     * *** Helper: ค้นหาเงินสนับสนุนเด็กด้วยเบอร์โทร ***
     */
    private function search_kid_by_phone($phone)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_phone', $phone);
            $this->db->order_by('kid_aw_ods_datesave', 'DESC');
            $query = $this->db->get();

            $results = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $kid) {
                    // ดึงข้อมูลเพิ่มเติม (files, history)
                    $kid->files = $this->Kid_aw_ods_model->get_kid_aw_ods_files($kid->kid_aw_ods_id);
                    $kid->history = $this->Kid_aw_ods_model->get_kid_aw_ods_history($kid->kid_aw_ods_id);
                    $results[] = $kid;
                }
            }

            return $results;

        } catch (Exception $e) {
            log_message('error', 'Error in search_kid_by_phone: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * *** Helper: ค้นหาเงินสนับสนุนเด็กด้วยเลขบัตรประชาชน ***
     */
    private function search_kid_by_id_card($id_card)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_number', $id_card);
            $this->db->order_by('kid_aw_ods_datesave', 'DESC');
            $query = $this->db->get();

            $results = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $kid) {
                    // ดึงข้อมูลเพิ่มเติม (files, history)
                    $kid->files = $this->Kid_aw_ods_model->get_kid_aw_ods_files($kid->kid_aw_ods_id);
                    $kid->history = $this->Kid_aw_ods_model->get_kid_aw_ods_history($kid->kid_aw_ods_id);
                    $results[] = $kid;
                }
            }

            return $results;

        } catch (Exception $e) {
            log_message('error', 'Error in search_kid_by_id_card: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * *** ตรวจสอบการอัปเดตสถานะ (AJAX) ***
     */
    public function check_status($kid_aw_ods_id)
    {
        try {
            if (empty($kid_aw_ods_id)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'updated' => false]);
                return;
            }

            // ดึงข้อมูลปัจจุบัน
            $this->db->select('kid_aw_ods_updated_at');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_aw_ods_id);
            $current_data = $this->db->get()->row();

            if (!$current_data) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'updated' => false]);
                return;
            }

            // เปรียบเทียบกับเวลาล่าสุดที่ส่งมา
            $last_check = $this->input->get('last_check');
            $updated = false;

            if ($last_check && $current_data->kid_aw_ods_updated_at) {
                $updated = (strtotime($current_data->kid_aw_ods_updated_at) > strtotime($last_check));
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'updated' => $updated,
                'last_updated' => $current_data->kid_aw_ods_updated_at
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in check_status: ' . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'updated' => false]);
        }
    }

    /**
     * *** ส่งออกข้อมูลเงินสนับสนุนเด็กเป็น Excel ***
     */
    public function export_excel()
    {
        try {
            // ตรวจสอบสิทธิ์ - เฉพาะ Staff เท่านั้น
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ และดึงข้อมูลระดับสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                redirect('User');
                return;
            }

            // เช็คสิทธิ์การจัดการเงินสนับสนุนเด็ก
            $can_handle_kid = $this->check_kid_handle_permission($staff_check);

            if (!$can_handle_kid) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์ส่งออกข้อมูลเงินสนับสนุนเด็ก');
                redirect('Kid_aw_ods/kid_aw_ods');
                return;
            }

            // รับตัวกรองจาก URL parameters (เดียวกับหน้ารายการ)
            $filters = [
                'status' => $this->input->get('status'),
                'type' => $this->input->get('type'),
                'priority' => $this->input->get('priority'),
                'user_type' => $this->input->get('user_type'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search'),
                'assigned_to' => $this->input->get('assigned_to')
            ];

            // ดึงข้อมูลทั้งหมดที่ตรงกับตัวกรอง (ไม่จำกัดจำนวน)
            $kid_result = $this->kid_aw_ods_model->get_kid_aw_ods_with_filters($filters, 999999, 0);
            $kid_data = $kid_result['data'] ?? [];

            if (empty($kid_data)) {
                $this->session->set_flashdata('warning_message', 'ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา');
                redirect('Kid_aw_ods/kid_aw_ods?' . http_build_query($filters));
                return;
            }

            // เตรียมข้อมูลสำหรับ Excel
            $excel_data = $this->prepare_excel_data($kid_data);

            // สร้างชื่อไฟล์
            $filename = 'รายงานเงินสนับสนุนเด็ก_' . date('Y-m-d_H-i-s') . '.xlsx';

            // ส่งออกเป็น Excel
            $this->generate_excel_file($excel_data, $filename, $filters, $staff_check);

            // บันทึก Log การส่งออก
            log_message('info', "Excel export completed by {$staff_check->m_fname} {$staff_check->m_lname} - {$filename} - " . count($kid_data) . " records");

        } catch (Exception $e) {
            log_message('error', 'Error in export_excel: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการส่งออกไฟล์: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการส่งออกไฟล์ กรุณาลองใหม่อีกครั้ง');
                redirect('Kid_aw_ods/kid_aw_ods');
            }
        }
    }

    /**
     * *** เตรียมข้อมูลสำหรับ Excel ***
     */
    private function prepare_excel_data($kid_data)
    {
        $excel_data = [];

        // Header row
        $excel_data[] = [
            'ลำดับ',
            'หมายเลขอ้างอิง',
            'ประเภท',
            'ชื่อ-นามสกุล',
            'เบอร์โทรศัพท์',
            'อีเมล',
            'เลขบัตรประชาชน',
            'ที่อยู่',
            'จังหวัด',
            'อำเภอ',
            'ตำบล',
            'รหัสไปรษณีย์',
            'สถานะ',
            'ความสำคัญ',
            'ประเภทผู้ใช้',
            'เจ้าหน้าที่ผู้รับผิดชอบ',
            'วันที่ยื่นเรื่อง',
            'วันที่อัปเดตล่าสุด',
            'หมายเหตุ'
        ];

        // Data rows
        $row_number = 1;
        foreach ($kid_data as $kid) {
            // แปลงค่าต่างๆ ให้เป็นภาษาไทย
            $type_display = '';
            switch ($kid->kid_aw_ods_type ?? 'children') {
                case 'children':
                    $type_display = 'เด็กทั่วไป';
                    break;
                case 'disabled':
                    $type_display = 'เด็กพิการ';
                    break;
                default:
                    $type_display = 'เด็กทั่วไป';
                    break;
            }

            $status_display = '';
            switch ($kid->kid_aw_ods_status ?? 'submitted') {
                case 'submitted':
                    $status_display = 'ยื่นเรื่องแล้ว';
                    break;
                case 'reviewing':
                    $status_display = 'กำลังพิจารณา';
                    break;
                case 'approved':
                    $status_display = 'อนุมัติแล้ว';
                    break;
                case 'rejected':
                    $status_display = 'ไม่อนุมัติ';
                    break;
                case 'completed':
                    $status_display = 'เสร็จสิ้น';
                    break;
                default:
                    $status_display = 'ยื่นเรื่องแล้ว';
                    break;
            }

            $priority_display = '';
            switch ($kid->kid_aw_ods_priority ?? 'normal') {
                case 'low':
                    $priority_display = 'ต่ำ';
                    break;
                case 'normal':
                    $priority_display = 'ปกติ';
                    break;
                case 'high':
                    $priority_display = 'สูง';
                    break;
                case 'urgent':
                    $priority_display = 'เร่งด่วน';
                    break;
                default:
                    $priority_display = 'ปกติ';
                    break;
            }

            $user_type_display = '';
            switch ($kid->kid_aw_ods_user_type ?? 'guest') {
                case 'guest':
                    $user_type_display = 'ผู้ใช้ทั่วไป';
                    break;
                case 'public':
                    $user_type_display = 'สมาชิก';
                    break;
                case 'staff':
                    $user_type_display = 'เจ้าหน้าที่';
                    break;
                default:
                    $user_type_display = 'ผู้ใช้ทั่วไป';
                    break;
            }

            // จัดรูปแบบวันที่
            $date_save = '';
            if (!empty($kid->kid_aw_ods_datesave)) {
                $thai_months = [
                    '01' => 'มกราคม',
                    '02' => 'กุมภาพันธ์',
                    '03' => 'มีนาคม',
                    '04' => 'เมษายน',
                    '05' => 'พฤษภาคม',
                    '06' => 'มิถุนายน',
                    '07' => 'กรกฎาคม',
                    '08' => 'สิงหาคม',
                    '09' => 'กันยายน',
                    '10' => 'ตุลาคม',
                    '11' => 'พฤศจิกายน',
                    '12' => 'ธันวาคม'
                ];

                $date = date('j', strtotime($kid->kid_aw_ods_datesave));
                $month = $thai_months[date('m', strtotime($kid->kid_aw_ods_datesave))];
                $year = date('Y', strtotime($kid->kid_aw_ods_datesave)) + 543;
                $time = date('H:i', strtotime($kid->kid_aw_ods_datesave));

                $date_save = $date . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';
            }

            $date_updated = '';
            if (!empty($kid->kid_aw_ods_updated_at)) {
                $date = date('j', strtotime($kid->kid_aw_ods_updated_at));
                $month = $thai_months[date('m', strtotime($kid->kid_aw_ods_updated_at))];
                $year = date('Y', strtotime($kid->kid_aw_ods_updated_at)) + 543;
                $time = date('H:i', strtotime($kid->kid_aw_ods_updated_at));

                $date_updated = $date . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';
            }

            // ดึงชื่อเจ้าหน้าที่ผู้รับผิดชอบ
            $assigned_staff_name = '';
            if (!empty($kid->kid_aw_ods_assigned_to)) {
                $this->db->select('m_fname, m_lname');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $kid->kid_aw_ods_assigned_to);
                $assigned_staff = $this->db->get()->row();

                if ($assigned_staff) {
                    $assigned_staff_name = trim($assigned_staff->m_fname . ' ' . $assigned_staff->m_lname);
                }
            }

            // ซ่อนเลขบัตรประชาชน
            $id_card_display = '';
            if (!empty($kid->kid_aw_ods_number)) {
                $id_card_display = substr($kid->kid_aw_ods_number, 0, 3) . '-****-****-**-' . substr($kid->kid_aw_ods_number, -2);
            }

            $excel_data[] = [
                $row_number,
                $kid->kid_aw_ods_id ?? '',
                $type_display,
                $kid->kid_aw_ods_by ?? '',
                $kid->kid_aw_ods_phone ?? '',
                $kid->kid_aw_ods_email ?? '',
                $id_card_display,
                $kid->kid_aw_ods_address ?? '',
                $kid->guest_province ?? '',
                $kid->guest_amphoe ?? '',
                $kid->guest_district ?? '',
                $kid->guest_zipcode ?? '',
                $status_display,
                $priority_display,
                $user_type_display,
                $assigned_staff_name,
                $date_save,
                $date_updated,
                $kid->kid_aw_ods_notes ?? ''
            ];

            $row_number++;
        }

        return $excel_data;
    }

    /**
     * *** สร้างไฟล์ Excel และส่งออก ***
     */
    private function generate_excel_file($excel_data, $filename, $filters, $staff_info)
    {
        // ใช้ PhpSpreadsheet หรือ SimpleXLSXGen
        // หากไม่มี library ให้ส่งออกเป็น CSV แทน

        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // Fallback เป็น CSV
            $this->generate_csv_file($excel_data, str_replace('.xlsx', '.csv', $filename));
            return;
        }

        try {
            // สร้าง Spreadsheet object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // ตั้งชื่อ Sheet
            $sheet->setTitle('รายงานเงินสนับสนุนเด็ก');

            // เพิ่มข้อมูลหัวรายงาน
            $sheet->setCellValue('A1', 'รายงานข้อมูลเงินสนับสนุนเด็กแรกเกิด');
            $sheet->mergeCells('A1:S1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

            // ข้อมูลการส่งออก
            $sheet->setCellValue('A2', 'ส่งออกโดย: ' . $staff_info->m_fname . ' ' . $staff_info->m_lname);
            $sheet->setCellValue('A3', 'วันที่ส่งออก: ' . date('j F Y เวลา H:i น.', strtotime('+543 years')));

            // ข้อมูลตัวกรอง
            $filter_text = $this->build_filter_description($filters);
            if (!empty($filter_text)) {
                $sheet->setCellValue('A4', 'เงื่อนไขการกรอง: ' . $filter_text);
            }

            // เริ่มข้อมูลจากแถวที่ 6
            $start_row = 6;

            // เพิ่มข้อมูลลงใน Sheet
            foreach ($excel_data as $row_index => $row_data) {
                $current_row = $start_row + $row_index;

                foreach ($row_data as $col_index => $cell_value) {
                    $column_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_index + 1);
                    $sheet->setCellValue($column_letter . $current_row, $cell_value);
                }

                // จัดรูปแบบหัวตาราง
                if ($row_index === 0) {
                    $sheet->getStyle('A' . $current_row . ':S' . $current_row)->getFont()->setBold(true);
                    $sheet->getStyle('A' . $current_row . ':S' . $current_row)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFE0E0E0');
                }
            }

            // จัดรูปแบบ
            $this->format_excel_sheet($sheet, $start_row, count($excel_data));

            // ส่งออกไฟล์
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            log_message('error', 'Error generating Excel file: ' . $e->getMessage());
            // Fallback เป็น CSV
            $this->generate_csv_file($excel_data, str_replace('.xlsx', '.csv', $filename));
        }
    }

    /**
     * *** สร้างไฟล์ CSV (Fallback) ***
     */
    private function generate_csv_file($excel_data, $filename)
    {
        // ตั้งค่า Headers สำหรับ CSV
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // เพิ่ม BOM สำหรับ UTF-8
        echo "\xEF\xBB\xBF";

        // สร้างไฟล์ CSV
        $output = fopen('php://output', 'w');

        foreach ($excel_data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * *** จัดรูปแบบ Excel Sheet ***
     */
    private function format_excel_sheet($sheet, $start_row, $total_rows)
    {
        // กำหนดความกว้างคอลัมน์
        $column_widths = [
            'A' => 8,   // ลำดับ
            'B' => 15,  // หมายเลขอ้างอิง
            'C' => 12,  // ประเภท
            'D' => 25,  // ชื่อ-นามสกุล
            'E' => 15,  // เบอร์โทร
            'F' => 25,  // อีเมล
            'G' => 18,  // เลขบัตรประชาชน
            'H' => 30,  // ที่อยู่
            'I' => 15,  // จังหวัด
            'J' => 15,  // อำเภอ
            'K' => 15,  // ตำบล
            'L' => 10,  // รหัสไปรษณีย์
            'M' => 15,  // สถานะ
            'N' => 12,  // ความสำคัญ
            'O' => 15,  // ประเภทผู้ใช้
            'P' => 20,  // เจ้าหน้าที่
            'Q' => 25,  // วันที่ยื่น
            'R' => 25,  // วันที่อัปเดต
            'S' => 30   // หมายเหตุ
        ];

        foreach ($column_widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // จัดตำแหน่งข้อความ
        $end_row = $start_row + $total_rows - 1;

        // จัดกึ่งกลางสำหรับคอลัมน์เลขที่และรหัส
        $sheet->getStyle('A' . $start_row . ':A' . $end_row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B' . $start_row . ':B' . $end_row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('E' . $start_row . ':E' . $end_row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('G' . $start_row . ':G' . $end_row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('L' . $start_row . ':L' . $end_row)->getAlignment()->setHorizontal('center');

        // ขอบตาราง
        $sheet->getStyle('A' . $start_row . ':S' . $end_row)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // ปรับความสูงแถวอัตโนมัติ
        for ($row = $start_row; $row <= $end_row; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }
    }

    /**
     * *** สร้างคำอธิบายตัวกรอง ***
     */
    private function build_filter_description($filters)
    {
        $descriptions = [];

        if (!empty($filters['status'])) {
            $status_map = [
                'submitted' => 'ยื่นเรื่องแล้ว',
                'reviewing' => 'กำลังพิจารณา',
                'approved' => 'อนุมัติแล้ว',
                'rejected' => 'ไม่อนุมัติ',
                'completed' => 'เสร็จสิ้น'
            ];
            $descriptions[] = 'สถานะ: ' . ($status_map[$filters['status']] ?? $filters['status']);
        }

        if (!empty($filters['type'])) {
            $type_map = [
                'children' => 'เด็กทั่วไป',
                'disabled' => 'เด็กพิการ'
            ];
            $descriptions[] = 'ประเภท: ' . ($type_map[$filters['type']] ?? $filters['type']);
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $descriptions[] = 'ช่วงวันที่: ' . $filters['date_from'] . ' ถึง ' . $filters['date_to'];
        } elseif (!empty($filters['date_from'])) {
            $descriptions[] = 'ตั้งแต่วันที่: ' . $filters['date_from'];
        } elseif (!empty($filters['date_to'])) {
            $descriptions[] = 'จนถึงวันที่: ' . $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $descriptions[] = 'ค้นหา: ' . $filters['search'];
        }

        return implode(', ', $descriptions);
    }

    /**
     * *** Helper: ดึงไอคอนตามสถานะ ***
     */
    private function get_status_icon($status)
    {
        $icons = [
            'submitted' => 'fas fa-file-alt',
            'reviewing' => 'fas fa-search',
            'approved' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            'completed' => 'fas fa-trophy'
        ];

        return $icons[$status] ?? 'fas fa-info-circle';
    }

    /**
     * รายละเอียดเงินสนับสนุนเด็กของฉัน
     */
    public function my_kid_aw_ods_detail($kid_id = null)
    {
        if ($this->is_staff_user()) {
            $this->show_staff_redirect_warning();
            return;
        }

        $current_user = $this->get_current_user_info();
        if (!$current_user['is_logged_in'] || empty($kid_id)) {
            redirect('Kid_aw_ods/my_kid_aw_ods');
            return;
        }

        try {
            // ตรวจสอบสิทธิ์
            $kid_detail = $this->get_kid_by_user($kid_id, $current_user);
            if (!$kid_detail) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์เข้าถึง');
                redirect('Kid_aw_ods/my_kid_aw_ods');
                return;
            }

            // *** แก้ไข: แปลง object เป็น array ก่อนใช้งาน ***
            if (is_object($kid_detail)) {
                $kid_detail = (array) $kid_detail;
            }

            // *** แก้ไข: ดึงไฟล์แยกต่างหาก ***
            $kid_detail['files'] = $this->get_kid_files_for_display($kid_id);

            $data = $this->prepare_page_data();
            $data['is_logged_in'] = true;
            $data['user_info'] = $current_user['user_info'];
            $data['kid_detail'] = $this->prepare_kid_for_display($kid_detail);
            $data['page_title'] = 'รายละเอียดเงินสนับสนุนเด็ก #' . $kid_id;

            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_kid_aw_ods_detail', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in my_kid_aw_ods_detail: ' . $e->getMessage());
            redirect('Kid_aw_ods/my_kid_aw_ods');
        }
    }

    // 3. เพิ่มฟังก์ชันตรวจสอบและดาวน์โหลดไฟล์แบบปลอดภัย
    public function view_file($file_name = null)
    {
        if (empty($file_name)) {
            show_404();
            return;
        }

        // ตรวจสอบสิทธิ์การเข้าถึงไฟล์
        $current_user = $this->get_current_user_detailed();
        if (!$current_user['is_logged_in']) {
            show_404();
            return;
        }

        // ค้นหาข้อมูลไฟล์ในฐานข้อมูล
        $this->db->select('kf.*, ko.kid_aw_ods_user_type, ko.kid_aw_ods_user_id');
        $this->db->from('tbl_kid_aw_ods_files kf');
        $this->db->join('tbl_kid_aw_ods ko', 'ko.kid_aw_ods_id = kf.kid_aw_ods_file_ref_id');
        $this->db->where('kf.kid_aw_ods_file_name', $file_name);
        $this->db->where('kf.kid_aw_ods_file_status', 'active');
        $file_info = $this->db->get()->row();

        if (!$file_info) {
            show_404();
            return;
        }

        // ตรวจสอบสิทธิ์เฉพาะเจ้าของไฟล์
        if ($current_user['user_type'] === 'public') {
            if (
                $file_info->kid_aw_ods_user_type !== 'public' ||
                $file_info->kid_aw_ods_user_id != $current_user['user_info']['id']
            ) {
                show_404();
                return;
            }
        }

        // หาไฟล์จริงในระบบ
        $file_paths = [
            FCPATH . 'uploads/kid_aw_ods/' . $file_name,
            './uploads/kid_aw_ods/' . $file_name,
            FCPATH . 'docs/file/' . $file_name,
            './docs/file/' . $file_name,
            $file_info->kid_aw_ods_file_path
        ];

        $actual_file_path = null;
        foreach ($file_paths as $path) {
            if (file_exists($path)) {
                $actual_file_path = $path;
                break;
            }
        }

        if (!$actual_file_path) {
            show_404();
            return;
        }

        // ส่งไฟล์
        $mime_type = $file_info->kid_aw_ods_file_type ?: 'application/octet-stream';

        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($actual_file_path));
        header('Content-Disposition: inline; filename="' . $file_info->kid_aw_ods_file_original_name . '"');
        header('Cache-Control: private, max-age=3600');

        readfile($actual_file_path);
        exit;
    }

    // 4. เพิ่มฟังก์ชันดาวน์โหลดไฟล์
    public function download_kid_file($file_name = null)
    {
        if (empty($file_name)) {
            show_404();
            return;
        }

        // ตรวจสอบสิทธิ์การเข้าถึงไฟล์ (เหมือน view_file)
        $current_user = $this->get_current_user_detailed();
        if (!$current_user['is_logged_in']) {
            show_404();
            return;
        }

        // ค้นหาข้อมูลไฟล์ในฐานข้อมูล
        $this->db->select('kf.*, ko.kid_aw_ods_user_type, ko.kid_aw_ods_user_id');
        $this->db->from('tbl_kid_aw_ods_files kf');
        $this->db->join('tbl_kid_aw_ods ko', 'ko.kid_aw_ods_id = kf.kid_aw_ods_file_ref_id');
        $this->db->where('kf.kid_aw_ods_file_name', $file_name);
        $this->db->where('kf.kid_aw_ods_file_status', 'active');
        $file_info = $this->db->get()->row();

        if (!$file_info) {
            show_404();
            return;
        }

        // ตรวจสอบสิทธิ์เฉพาะเจ้าของไฟล์
        if ($current_user['user_type'] === 'public') {
            if (
                $file_info->kid_aw_ods_user_type !== 'public' ||
                $file_info->kid_aw_ods_user_id != $current_user['user_info']['id']
            ) {
                show_404();
                return;
            }
        }

        // หาไฟล์จริงในระบบ
        $file_paths = [
            FCPATH . 'uploads/kid_aw_ods/' . $file_name,
            './uploads/kid_aw_ods/' . $file_name,
            FCPATH . 'docs/file/' . $file_name,
            './docs/file/' . $file_name,
            $file_info->kid_aw_ods_file_path
        ];

        $actual_file_path = null;
        foreach ($file_paths as $path) {
            if (file_exists($path)) {
                $actual_file_path = $path;
                break;
            }
        }

        if (!$actual_file_path) {
            show_404();
            return;
        }

        // ส่งไฟล์แบบ download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_info->kid_aw_ods_file_original_name . '"');
        header('Content-Length: ' . filesize($actual_file_path));
        header('Cache-Control: no-cache, must-revalidate');

        readfile($actual_file_path);
        exit;
    }


    /**
     * ดึงข้อมูล user ปัจจุบัน
     */
    private function get_current_user_info()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];

        try {
            // ตรวจสอบ public user
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!empty($mp_id) && !empty($mp_email)) {
                $this->db->select('*');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_info'] = [
                        'id' => $user_data->id,
                        'mp_id' => $user_data->mp_id,
                        'name' => trim(($user_data->mp_prefix ?: '') . ' ' . $user_data->mp_fname . ' ' . $user_data->mp_lname),
                        'fname' => $user_data->mp_fname,
                        'lname' => $user_data->mp_lname,
                        'prefix' => $user_data->mp_prefix,
                        'phone' => $user_data->mp_phone,
                        'email' => $user_data->mp_email,
                        'mp_email' => $user_data->mp_email, // เพิ่มเพื่อ backward compatibility
                        'number' => $user_data->mp_number,
                        'mp_img' => $user_data->mp_img // เพิ่มสำหรับรูปโปรไฟล์
                    ];

                    if (!empty($user_data->mp_address)) {
                        $full_address = trim($user_data->mp_address . ' ' .
                            ($user_data->mp_district ? 'ตำบล' . $user_data->mp_district : '') . ' ' .
                            ($user_data->mp_amphoe ? 'อำเภอ' . $user_data->mp_amphoe : '') . ' ' .
                            ($user_data->mp_province ? 'จังหวัด' . $user_data->mp_province : '') . ' ' .
                            ($user_data->mp_zipcode ?: ''));

                        $user_info['user_address'] = [
                            'full_address' => $full_address,
                            'parsed' => [
                                'additional_address' => $user_data->mp_address,
                                'district' => $user_data->mp_district,
                                'amphoe' => $user_data->mp_amphoe,
                                'province' => $user_data->mp_province,
                                'zipcode' => $user_data->mp_zipcode,
                                'full_address' => $full_address
                            ]
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting user info: ' . $e->getMessage());
        }

        return $user_info;
    }

    /**
     * สร้าง ID ใหม่
     */
    private function generate_kid_id()
    {
        try {
            $max_attempts = 100; // จำกัดการลองไม่เกิน 100 ครั้ง
            $attempt = 0;

            // คำนวณปีไทย (พ.ศ.) 2 ตัวท้าย
            $thai_year = date('Y') + 543; // 2025 + 543 = 2568
            $year_suffix = substr($thai_year, -2); // เอา 2 ตัวท้าย = 68

            do {
                $attempt++;

                // สร้าง 5 ตัวเลขสุ่ม
                $random_numbers = '';
                for ($i = 0; $i < 5; $i++) {
                    $random_numbers .= mt_rand(0, 9);
                }

                // รวมเป็น K + ปีไทย 2 ตัวท้าย + 5 ตัวเลขสุ่ม
                $new_id = 'K' . $year_suffix . $random_numbers;

                // ตรวจสอบว่าซ้ำในฐานข้อมูลหรือไม่
                if ($this->db->table_exists('tbl_kid_aw_ods')) {
                    $this->db->select('kid_aw_ods_id');
                    $this->db->from('tbl_kid_aw_ods');
                    $this->db->where('kid_aw_ods_id', $new_id);
                    $existing = $this->db->get()->row();

                    if (!$existing) {
                        log_message('info', 'Generated new Kid ID: ' . $new_id . ' (Thai Year: ' . $thai_year . ', attempt: ' . $attempt . ')');
                        return $new_id;
                    }

                    log_message('debug', 'Kid ID duplicate found: ' . $new_id . ' (attempt: ' . $attempt . ')');
                } else {
                    // ถ้าตารางยังไม่มี ให้ return ID ทันที
                    log_message('info', 'Generated new Kid ID (table not exists): ' . $new_id);
                    return $new_id;
                }

            } while ($attempt < $max_attempts);

            // ถ้าลองแล้ว 100 ครั้งยังซ้ำ ให้ใช้ timestamp เป็น fallback
            $timestamp_suffix = substr(time(), -5); // เอา 5 ตัวท้ายของ timestamp
            $fallback_id = 'K' . $year_suffix . $timestamp_suffix;

            log_message('warning', 'Kid ID generation fallback used: ' . $fallback_id . ' after ' . $max_attempts . ' attempts');

            return $fallback_id;

        } catch (Exception $e) {
            log_message('error', 'Error generating Kid ID: ' . $e->getMessage());

            // Emergency fallback - ใช้ปีไทย 2 ตัวท้าย + timestamp 5 ตัวท้าย
            $thai_year = date('Y') + 543;
            $year_suffix = substr($thai_year, -2);
            $emergency_suffix = substr(time(), -5);
            $emergency_id = 'K' . $year_suffix . $emergency_suffix;

            log_message('error', 'Emergency Kid ID generated: ' . $emergency_id);

            return $emergency_id;
        }
    }


    /**
     * *** ปรับปรุง: ฟังก์ชันดึงฟอร์มที่เปิดใช้งาน ***
     */
    private function get_active_forms()
    {
        try {
            // *** แก้ไข: ใช้ชื่อ model ที่ถูกต้อง ***
            if (method_exists($this->Kid_aw_ods_model, 'get_all_forms')) {
                $forms = $this->Kid_aw_ods_model->get_all_forms();

                if (is_array($forms) && !empty($forms)) {
                    log_message('info', 'Retrieved ' . count($forms) . ' active forms');
                    return $forms;
                } else {
                    log_message('warning', 'No active forms found');
                    return [];
                }
            } else {
                log_message('error', 'Method get_all_forms not found in Kid_aw_ods_model');
                return [];
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting active forms: ' . $e->getMessage());
            return [];
        }
    }





    /**
     * เตรียมข้อมูลพื้นฐานสำหรับหน้า
     */
    private function prepare_page_data()
    {
        $data = [];

        // โหลดข้อมูลสำหรับ navbar
        if (method_exists($this->activity_model, 'activity_frontend')) {
            $data['qActivity'] = $this->activity_model->activity_frontend() ?: [];
        }
        if (method_exists($this->news_model, 'news_frontend')) {
            $data['qNews'] = $this->news_model->news_frontend() ?: [];
        }
        if (method_exists($this->banner_model, 'banner_frontend')) {
            $data['qBanner'] = $this->banner_model->banner_frontend() ?: [];
        }

        // ตั้งค่าเริ่มต้น
        $default_arrays = [
            'qAnnounce',
            'qOrder',
            'qProcurement',
            'qMui',
            'qGuide_work',
            'qLoadform',
            'qPppw',
            'qMsg_pres',
            'qHistory',
            'qOtop',
            'qGci',
            'qVision',
            'qAuthority',
            'qMission',
            'qMotto',
            'qCmi',
            'qExecutivepolicy',
            'qTravel',
            'qSi',
            'qHotnews',
            'qWeather',
            'events',
            'qBackground_personnel'
        ];

        foreach ($default_arrays as $key) {
            if (!isset($data[$key])) {
                $data[$key] = [];
            }
        }

        return $data;
    }




    /**
     * ตรวจสอบว่าเป็น staff user หรือไม่
     */
    private function is_staff_user()
    {
        $m_id = $this->session->userdata('m_id');
        $m_email = $this->session->userdata('m_email');

        return !empty($m_id) && !empty($m_email);
    }

    /**
     * แสดงหน้าแจ้งเตือนสำหรับ staff user
     */
    private function show_staff_redirect_warning()
    {
        $data = $this->prepare_page_data();
        $data['page_title'] = 'การเข้าถึงถูกจำกัด';
        $data['staff_name'] = $this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname');

        $this->load->view('frontend_templat/header', $data);
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/staff_redirect_warning', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer', $data);
    }




    private function get_kid_files_for_display($kid_id)
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_ods_files')) {
                log_message('info', 'Files table does not exist');
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods_files');
            $this->db->where('kid_aw_ods_file_ref_id', $kid_id);
            $this->db->where('kid_aw_ods_file_status', 'active');
            $this->db->order_by('kid_aw_ods_file_uploaded_at', 'DESC');
            $query = $this->db->get();

            $files = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $file) {
                    // *** ตรวจสอบไฟล์ในหลายโฟลเดอร์ ***
                    $file_paths_to_check = [
                        FCPATH . 'uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name,
                        './uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name,
                        FCPATH . 'docs/file/' . $file->kid_aw_ods_file_name,
                        './docs/file/' . $file->kid_aw_ods_file_name,
                        FCPATH . 'files/' . $file->kid_aw_ods_file_name,
                        './files/' . $file->kid_aw_ods_file_name
                    ];

                    $file_exists = false;
                    $download_url = '';
                    $actual_file_path = '';

                    // ลองหาไฟล์ในแต่ละตำแหน่ง
                    foreach ($file_paths_to_check as $check_path) {
                        if (file_exists($check_path)) {
                            $file_exists = true;
                            $actual_file_path = $check_path;

                            // สร้าง URL ตามตำแหน่งที่พบ
                            if (strpos($check_path, 'uploads/kid_aw_ods/') !== false) {
                                $download_url = base_url('uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name);
                            } elseif (strpos($check_path, 'docs/file/') !== false) {
                                $download_url = base_url('docs/file/' . $file->kid_aw_ods_file_name);
                            } elseif (strpos($check_path, 'files/') !== false) {
                                $download_url = base_url('files/' . $file->kid_aw_ods_file_name);
                            }
                            break;
                        }
                    }

                    // ถ้าไม่พบไฟล์ แต่ยังมีข้อมูลในฐานข้อมูล ให้ลองสร้าง URL จาก field path
                    if (!$file_exists && !empty($file->kid_aw_ods_file_path)) {
                        $path_from_db = $file->kid_aw_ods_file_path;

                        // ตรวจสอบ path จากฐานข้อมูล
                        if (file_exists($path_from_db)) {
                            $file_exists = true;
                            $actual_file_path = $path_from_db;

                            // สร้าง URL จาก path
                            $relative_path = str_replace([FCPATH, './'], '', $path_from_db);
                            $download_url = base_url($relative_path);
                        }
                    }

                    // ถ้ายังไม่พบ ให้ลองใช้ default URL
                    if (!$file_exists) {
                        $download_url = base_url('uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name);
                        log_message('warning', "File not found: {$file->kid_aw_ods_file_name} for kid: {$kid_id}");
                    }

                    // *** สร้างข้อมูลไฟล์สำหรับส่งกลับ ***
                    $files[] = [
                        'file_id' => $file->kid_aw_ods_file_id ?? '',
                        'kid_aw_ods_file_name' => $file->kid_aw_ods_file_name ?? '',
                        'kid_aw_ods_file_original_name' => $file->kid_aw_ods_file_original_name ?? '',
                        'kid_aw_ods_file_type' => $file->kid_aw_ods_file_type ?? '',
                        'kid_aw_ods_file_size' => $file->kid_aw_ods_file_size ?? 0,
                        'kid_aw_ods_file_uploaded_at' => $file->kid_aw_ods_file_uploaded_at ?? '',
                        'kid_aw_ods_file_uploaded_by' => $file->kid_aw_ods_file_uploaded_by ?? '',
                        'file_exists' => $file_exists,
                        'download_url' => $download_url,
                        'actual_path' => $actual_file_path
                    ];
                }
            }

            log_message('debug', "Retrieved " . count($files) . " files for display: {$kid_id}");
            return $files;

        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_files_for_display: ' . $e->getMessage());
            return [];
        }
    }

    // *** เพิ่มฟังก์ชันตรวจสอบไฟล์แบบ Force ***
    public function check_file_exists($file_name)
    {
        $response = ['exists' => false, 'url' => ''];

        if (empty($file_name)) {
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }

        // ลองหาไฟล์ในหลายตำแหน่ง
        $possible_paths = [
            FCPATH . 'uploads/kid_aw_ods/' . $file_name,
            './uploads/kid_aw_ods/' . $file_name,
            FCPATH . 'docs/file/' . $file_name,
            './docs/file/' . $file_name
        ];

        foreach ($possible_paths as $file_path) {
            if (file_exists($file_path)) {
                $response['exists'] = true;
                if (strpos($file_path, 'uploads/kid_aw_ods/') !== false) {
                    $response['url'] = base_url('uploads/kid_aw_ods/' . $file_name);
                } else {
                    $response['url'] = base_url('docs/file/' . $file_name);
                }
                break;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // *** เพิ่มฟังก์ชันสำหรับดาวน์โหลดไฟล์แบบ Force ***
    public function download_file($file_name = null)
    {
        if (empty($file_name)) {
            show_404();
            return;
        }

        // ลองหาไฟล์ในหลายตำแหน่ง
        $possible_paths = [
            FCPATH . 'uploads/kid_aw_ods/' . $file_name,
            './uploads/kid_aw_ods/' . $file_name,
            FCPATH . 'docs/file/' . $file_name,
            './docs/file/' . $file_name
        ];

        $file_path = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $file_path = $path;
                break;
            }
        }

        if (!$file_path) {
            show_404();
            return;
        }

        // ดึงชื่อไฟล์ต้นฉบับจากฐานข้อมูล
        $this->db->select('kid_aw_ods_file_original_name');
        $this->db->from('tbl_kid_aw_ods_files');
        $this->db->where('kid_aw_ods_file_name', $file_name);
        $file_info = $this->db->get()->row();

        $original_name = $file_info ? $file_info->kid_aw_ods_file_original_name : $file_name;

        // ส่งไฟล์
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $original_name . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }

    /**
     * ดึงประวัติ
     */
    private function get_kid_history($kid_id)
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_ods_history')) {
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods_history');
            $this->db->where('kid_aw_ods_history_ref_id', $kid_id);
            $this->db->order_by('action_date', 'DESC');
            $query = $this->db->get();

            $history = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $record) {
                    // *** แปลง object เป็น array ***
                    $history[] = (array) $record;
                }
            }

            return $history;

        } catch (Exception $e) {
            log_message('error', 'Error getting kid history: ' . $e->getMessage());
            return [];
        }
    }





    /**
     * ดึงข้อมูลตาม user และตรวจสอบสิทธิ์
     */
    private function get_kid_by_user($kid_id, $current_user)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_id);

            if ($current_user['user_type'] === 'public') {
                $this->db->where('kid_aw_ods_user_type', 'public');
                $this->db->where('kid_aw_ods_user_id', $current_user['user_info']['id']);
            }

            $kid_data = $this->db->get()->row();

            if ($kid_data) {
                // *** แก้ไข: แปลง object เป็น array ก่อนส่งกลับ ***
                $kid_array = (array) $kid_data;

                // ดึงไฟล์และประวัติแยกต่างหาก
                $kid_array['files'] = $this->get_kid_files($kid_id);
                $kid_array['history'] = $this->get_kid_history($kid_id);

                return $kid_array;
            }

            return null;

        } catch (Exception $e) {
            log_message('error', 'Error in get_kid_by_user: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * ดึงไฟล์แนบ
     */
    private function get_kid_files($kid_id)
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_ods_files')) {
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods_files');
            $this->db->where('kid_aw_ods_file_ref_id', $kid_id);
            $this->db->where('kid_aw_ods_file_status', 'active');
            $this->db->order_by('kid_aw_ods_file_uploaded_at', 'DESC');
            $query = $this->db->get();

            $files = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $file) {
                    // *** แปลง object เป็น array ***
                    $files[] = (array) $file;
                }
            }

            return $files;

        } catch (Exception $e) {
            log_message('error', 'Error getting kid files: ' . $e->getMessage());
            return [];
        }
    }





    // ===================================================================
    // *** หน้าจัดการฟอร์มเงินสนับสนุนเด็ก ***
    // ===================================================================

    /**
     * หน้าจัดการฟอร์มเงินสนับสนุนเด็ก (สำหรับ Staff)
     */
    public function manage_forms()
    {
        try {
            // *** ตรวจสอบ Staff login ***
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ และดึงข้อมูลระดับสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id, m_email, m_phone, m_username, m_img');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1'); // ต้องเป็น active เท่านั้น
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
                redirect('User');
                return;
            }

            // *** ตรวจสอบสิทธิ์ในการจัดการฟอร์ม ***
            $can_manage_forms = $this->check_kid_forms_management_permission($staff_check);

            if (!$can_manage_forms) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์จัดการฟอร์มเงินสนับสนุนเด็ก');
                redirect('Kid_aw_ods/kid_aw_ods');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_add_forms'] = $this->check_kid_forms_add_permission($staff_check);
            $data['can_edit_forms'] = $this->check_kid_forms_edit_permission($staff_check);
            $data['can_delete_forms'] = $this->check_kid_forms_delete_permission($staff_check);
            $data['can_toggle_forms'] = $this->check_kid_forms_toggle_permission($staff_check);
            $data['staff_system_level'] = $staff_check->m_system;

            // *** สร้าง user_info object ที่ครบถ้วนสำหรับ header.php ***
            $user_info_object = $this->create_complete_user_info($staff_check);

            // *** ดึงข้อมูลฟอร์มทั้งหมด ***
            $all_forms = $this->get_all_kid_forms_for_management();

            // *** เตรียมข้อมูลฟอร์มสำหรับแสดงผล ***
            $data['kid_forms'] = $this->prepare_kid_forms_for_management_display($all_forms);

            // *** คำนวณสถิติฟอร์ม ***
            $data['forms_statistics'] = $this->calculate_kid_forms_statistics($all_forms);

            // ข้อมูลเจ้าหน้าที่
            $data['staff_info'] = [
                'id' => $staff_check->m_id,
                'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                'system' => $staff_check->m_system,
                'can_add' => $data['can_add_forms'],
                'can_edit' => $data['can_edit_forms'],
                'can_delete' => $data['can_delete_forms'],
                'can_toggle' => $data['can_toggle_forms']
            ];

            // ข้อมูลผู้ใช้สำหรับ header
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;

            // *** เพิ่มตัวแปรเสริมสำหรับ header.php ***
            $data['current_user'] = $user_info_object;
            $data['logged_user'] = $user_info_object;
            $data['session_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;
            $data['member_data'] = $user_info_object;

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Kid_aw_ods/kid_aw_ods')],
                ['title' => 'จัดการเงินสนับสนุนเด็ก', 'url' => site_url('Kid_aw_ods/kid_aw_ods')],
                ['title' => 'จัดการฟอร์ม', 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'จัดการฟอร์มเงินสนับสนุนเด็ก';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Kid forms management data: ' . json_encode([
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_add_forms' => $data['can_add_forms'],
                    'can_edit_forms' => $data['can_edit_forms'],
                    'can_delete_forms' => $data['can_delete_forms'],
                    'can_toggle_forms' => $data['can_toggle_forms'],
                    'forms_count' => count($data['kid_forms']),
                    'forms_statistics' => $data['forms_statistics']
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/kid_forms_management', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in manage_forms: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าจัดการฟอร์ม: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Kid_aw_ods/kid_aw_ods');
            }
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การจัดการฟอร์มทั่วไป ***
     */
    private function check_kid_forms_management_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถจัดการฟอร์มได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Kid forms management permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 53 (สิทธิ์เงินสนับสนุนเด็ก)
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('error', "user_admin without grant_user_ref_id: {$staff_data->m_fname} {$staff_data->m_lname}");
                    return false;
                }

                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));
                $has_permission = in_array('53', $grant_ids);

                log_message('info', "user_admin kid forms management permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "grant_user_ref_id: {$staff_data->grant_user_ref_id} - " .
                    "has_53: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            // อื่นๆ ไม่สามารถจัดการฟอร์มได้
            log_message('info', "Kid forms management permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking kid forms management permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การเพิ่มฟอร์ม ***
     */
    private function check_kid_forms_add_permission($staff_data)
    {
        try {
            // เหมือนกับสิทธิ์การจัดการทั่วไป
            return $this->check_kid_forms_management_permission($staff_data);

        } catch (Exception $e) {
            log_message('error', 'Error checking kid forms add permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การแก้ไขฟอร์ม ***
     */
    private function check_kid_forms_edit_permission($staff_data)
    {
        try {
            // เหมือนกับสิทธิ์การจัดการทั่วไป
            return $this->check_kid_forms_management_permission($staff_data);

        } catch (Exception $e) {
            log_message('error', 'Error checking kid forms edit permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การลบฟอร์ม ***
     */
    private function check_kid_forms_delete_permission($staff_data)
    {
        try {
            // เฉพาะ system_admin และ super_admin ที่สามารถลบฟอร์มได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Kid forms delete permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            log_message('info', "Kid forms delete permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking kid forms delete permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การ toggle สถานะฟอร์ม ***
     */
    private function check_kid_forms_toggle_permission($staff_data)
    {
        try {
            // เหมือนกับสิทธิ์การจัดการทั่วไป
            return $this->check_kid_forms_management_permission($staff_data);

        } catch (Exception $e) {
            log_message('error', 'Error checking kid forms toggle permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ดึงข้อมูลฟอร์มทั้งหมดสำหรับการจัดการ ***
     */
    private function get_all_kid_forms_for_management()
    {
        try {
            if (!$this->db->table_exists('tbl_kid_aw_form')) {
                log_message('error', 'Kid forms table does not exist');
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_kid_aw_form');
            $this->db->order_by('kid_aw_form_datesave', 'DESC');
            $query = $this->db->get();

            $forms = [];
            if ($query->num_rows() > 0) {
                $forms = $query->result();
                log_message('info', 'Retrieved ' . count($forms) . ' kid forms for management');
            } else {
                log_message('info', 'No kid forms found in database');
            }

            return $forms;

        } catch (Exception $e) {
            log_message('error', 'Error in get_all_kid_forms_for_management: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * *** เตรียมข้อมูลฟอร์มสำหรับแสดงผลในหน้าจัดการ ***
     */
    private function prepare_kid_forms_for_management_display($forms)
    {
        $prepared_forms = [];

        if (empty($forms) || !is_array($forms)) {
            return $prepared_forms;
        }

        foreach ($forms as $form) {
            // ตรวจสอบไฟล์ว่ามีอยู่จริงในระบบไฟล์
            $file_exists = false;
            $file_size = 0;
            $file_extension = '';
            $download_url = '';

            if (!empty($form->kid_aw_form_file)) {
                $file_path = FCPATH . 'docs/file/' . $form->kid_aw_form_file;

                if (file_exists($file_path)) {
                    $file_exists = true;
                    $file_size = filesize($file_path);
                    $file_extension = strtolower(pathinfo($form->kid_aw_form_file, PATHINFO_EXTENSION));
                    $download_url = base_url('docs/file/' . $form->kid_aw_form_file);
                } else {
                    log_message('error', "Kid form file not found: {$form->kid_aw_form_file} for form ID: {$form->kid_aw_form_id}");
                }
            }

            // เตรียมข้อมูลที่ครบถ้วน
            $form_data = [
                'kid_aw_form_id' => $form->kid_aw_form_id ?? '',
                'kid_aw_form_name' => $form->kid_aw_form_name ?? '',
                'kid_aw_form_type' => $form->kid_aw_form_type ?? 'children',
                'kid_aw_form_description' => $form->kid_aw_form_description ?? '',
                'kid_aw_form_file' => $form->kid_aw_form_file ?? '',
                'kid_aw_form_status' => $form->kid_aw_form_status ?? 0,
                'kid_aw_form_datesave' => $form->kid_aw_form_datesave ?? '',
                'kid_aw_form_by' => $form->kid_aw_form_by ?? '',

                // ข้อมูลไฟล์
                'file_exists' => $file_exists,
                'file_size' => $file_size,
                'file_extension' => $file_extension,
                'download_url' => $download_url,
                'file_size_formatted' => $this->format_file_size($file_size),

                // ข้อมูลแสดงผล
                'status_display' => $this->get_kid_form_status_display($form->kid_aw_form_status ?? 0),
                'status_class' => $this->get_kid_form_status_class($form->kid_aw_form_status ?? 0),
                'type_display' => $this->get_kid_form_type_display($form->kid_aw_form_type ?? 'children'),
                'type_icon' => $this->get_kid_form_type_icon($form->kid_aw_form_type ?? 'children'),

                // วันที่แสดงผล
                'formatted_date' => $this->format_thai_date($form->kid_aw_form_datesave ?? ''),

                // สถานะการใช้งาน
                'is_active' => ($form->kid_aw_form_status ?? 0) == 1,
                'can_download' => $file_exists && (($form->kid_aw_form_status ?? 0) == 1)
            ];

            $prepared_forms[] = (object) $form_data;
        }

        log_message('info', 'Prepared ' . count($prepared_forms) . ' kid forms for management display');
        return $prepared_forms;
    }

    /**
     * *** คำนวณสถิติฟอร์ม ***
     */
    /**
     * *** คำนวณสถิติฟอร์ม ***
     */
    private function calculate_kid_forms_statistics($forms)
    {
        $statistics = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'children_type' => 0,
            // 'disabled_type' => 0,  // ← ลบบรรทัดนี้
            'general_type' => 0,
            'authorization_type' => 0,
            'files_missing' => 0,
            'files_ok' => 0
        ];

        if (empty($forms) || !is_array($forms)) {
            return $statistics;
        }

        $statistics['total'] = count($forms);

        foreach ($forms as $form) {
            // นับตามสถานะ
            if (($form->kid_aw_form_status ?? 0) == 1) {
                $statistics['active']++;
            } else {
                $statistics['inactive']++;
            }

            // นับตามประเภท - ลบการนับ disabled ออก
            switch ($form->kid_aw_form_type ?? 'children') {
                case 'children':
                    $statistics['children_type']++;
                    break;
                // case 'disabled':  // ← ลบ case นี้ออก
                //     $statistics['disabled_type']++;
                //     break;
                case 'authorization':
                    $statistics['authorization_type']++;
                    break;
                default:
                    $statistics['general_type']++;
                    break;
            }

            // ตรวจสอบไฟล์
            if (!empty($form->kid_aw_form_file)) {
                $file_path = FCPATH . 'docs/file/' . $form->kid_aw_form_file;
                if (file_exists($file_path)) {
                    $statistics['files_ok']++;
                } else {
                    $statistics['files_missing']++;
                }
            } else {
                $statistics['files_missing']++;
            }
        }

        return $statistics;
    }

    /**
     * *** Helper Functions สำหรับฟอร์ม ***
     */

    private function get_kid_form_status_display($status)
    {
        return $status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    }

    private function get_kid_form_status_class($status)
    {
        return $status == 1 ? 'active' : 'inactive';
    }

    private function get_kid_form_type_display($type)
    {
        $types = [
            'children' => 'เด็กทั่วไป',
            // 'disabled' => 'เด็กพิการ',  // ← ลบบรรทัดนี้
            'authorization' => 'หนังสือมอบอำนาจ',
            'general' => 'ทั่วไป'
        ];

        return $types[$type] ?? 'เด็กทั่วไป';
    }

    private function get_kid_form_type_icon($type)
    {
        $icons = [
            'children' => 'fas fa-baby',
            // 'disabled' => 'fas fa-wheelchair',  // ← ลบบรรทัดนี้
            'authorization' => 'fas fa-file-signature',
            'general' => 'fas fa-file-alt'
        ];

        return $icons[$type] ?? 'fas fa-baby';
    }

    // ===================================================================
    // *** AJAX Functions สำหรับการจัดการฟอร์ม ***
    // ===================================================================

    /**
     * เพิ่มฟอร์มใหม่ (AJAX)
     */
    public function add_form()
    {
        // *** ป้องกัน output ใดๆ ก่อนส่ง JSON ***
        ob_start();

        try {
            // ตรวจสอบ HTTP method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบ AJAX request
            if (!$this->input->is_ajax_request()) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Only AJAX requests allowed']);
                return;
            }

            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $this->db->select('m_id, m_fname, m_lname, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การเพิ่มฟอร์ม
            if (!$this->check_kid_forms_add_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เพิ่มฟอร์ม']);
                return;
            }

            // รับข้อมูลจากฟอร์ม
            $form_name = $this->input->post('form_name');
            $form_type = $this->input->post('form_type') ?: 'children';
            $form_description = $this->input->post('form_description') ?: '';
            $form_status = $this->input->post('form_status') ? 1 : 0;

            // Validation
            if (empty($form_name)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณากรอกชื่อฟอร์ม']);
                return;
            }

            // ตรวจสอบไฟล์อัพโหลด
            if (!isset($_FILES['form_file']) || $_FILES['form_file']['error'] !== UPLOAD_ERR_OK) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเลือกไฟล์ฟอร์ม']);
                return;
            }

            // จัดการอัพโหลดไฟล์
            $upload_result = $this->handle_kid_form_file_upload();

            if (!$upload_result['success']) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => $upload_result['message']]);
                return;
            }

            $uploaded_file = $upload_result['file_name'];
            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เริ่ม Transaction
            $this->db->trans_start();

            // บันทึกข้อมูลฟอร์ม
            $form_data = [
                'kid_aw_form_name' => $form_name,
                'kid_aw_form_type' => $form_type,
                'kid_aw_form_description' => $form_description,
                'kid_aw_form_file' => $uploaded_file,
                'kid_aw_form_status' => $form_status,
                'kid_aw_form_by' => $updated_by,
                'kid_aw_form_datesave' => date('Y-m-d H:i:s')
            ];

            $insert_result = $this->db->insert('tbl_kid_aw_form', $form_data);

            if (!$insert_result) {
                $this->db->trans_rollback();

                // ลบไฟล์ที่อัพโหลดแล้ว
                @unlink(FCPATH . 'docs/file/' . $uploaded_file);

                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
                return;
            }

            $form_id = $this->db->insert_id();

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                // ลบไฟล์ที่อัพโหลดแล้ว
                @unlink(FCPATH . 'docs/file/' . $uploaded_file);

                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
                return;
            }

            ob_end_clean();

            log_message('info', "Kid form added successfully: ID={$form_id}, Name={$form_name}, By={$updated_by}");

            $this->json_response([
                'success' => true,
                'message' => 'เพิ่มฟอร์มสำเร็จ',
                'form_id' => $form_id,
                'form_name' => $form_name,
                'file_name' => $uploaded_file
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in add_form: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * ดึงข้อมูลฟอร์มสำหรับแก้ไข (AJAX)
     */
    public function get_form_data()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            $form_id = $this->input->post('form_id');

            if (empty($form_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบหมายเลขฟอร์ม']);
                return;
            }

            // ดึงข้อมูลฟอร์ม
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_form');
            $this->db->where('kid_aw_form_id', $form_id);
            $form_data = $this->db->get()->row();

            if (!$form_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลฟอร์ม']);
                return;
            }

            ob_end_clean();

            $this->json_response([
                'success' => true,
                'form' => [
                    'kid_aw_form_id' => $form_data->kid_aw_form_id,
                    'kid_aw_form_name' => $form_data->kid_aw_form_name,
                    'kid_aw_form_type' => $form_data->kid_aw_form_type,
                    'kid_aw_form_description' => $form_data->kid_aw_form_description ?? '',
                    'kid_aw_form_file' => $form_data->kid_aw_form_file,
                    'kid_aw_form_status' => $form_data->kid_aw_form_status,
                    'kid_aw_form_by' => $form_data->kid_aw_form_by,
                    'kid_aw_form_datesave' => $form_data->kid_aw_form_datesave
                ]
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in get_form_data: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ]);
        }
    }

    /**
     * แก้ไขฟอร์ม (AJAX)
     */
    public function edit_form()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $this->db->select('m_id, m_fname, m_lname, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การแก้ไขฟอร์ม
            if (!$this->check_kid_forms_edit_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์แก้ไขฟอร์ม']);
                return;
            }

            // รับข้อมูลจากฟอร์ม
            $form_id = $this->input->post('form_id');
            $form_name = $this->input->post('form_name');
            $form_type = $this->input->post('form_type') ?: 'children';
            $form_description = $this->input->post('form_description') ?: '';
            $form_status = $this->input->post('form_status') ? 1 : 0;

            // Validation
            if (empty($form_id) || empty($form_name)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            // ตรวจสอบว่าฟอร์มมีอยู่จริง
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_form');
            $this->db->where('kid_aw_form_id', $form_id);
            $existing_form = $this->db->get()->row();

            if (!$existing_form) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลฟอร์ม']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เริ่ม Transaction
            $this->db->trans_start();

            // เตรียมข้อมูลสำหรับอัพเดต
            $update_data = [
                'kid_aw_form_name' => $form_name,
                'kid_aw_form_type' => $form_type,
                'kid_aw_form_description' => $form_description,
                'kid_aw_form_status' => $form_status,
                'kid_aw_form_updated_by' => $updated_by,
                'kid_aw_form_updated_at' => date('Y-m-d H:i:s')
            ];

            // ตรวจสอบไฟล์ใหม่
            $new_file = '';
            if (isset($_FILES['form_file']) && $_FILES['form_file']['error'] === UPLOAD_ERR_OK) {
                $upload_result = $this->handle_kid_form_file_upload();

                if (!$upload_result['success']) {
                    $this->db->trans_rollback();
                    ob_end_clean();
                    $this->json_response(['success' => false, 'message' => $upload_result['message']]);
                    return;
                }

                $new_file = $upload_result['file_name'];
                $update_data['kid_aw_form_file'] = $new_file;
            }

            // อัพเดตข้อมูล
            $this->db->where('kid_aw_form_id', $form_id);
            $update_result = $this->db->update('tbl_kid_aw_form', $update_data);

            if (!$update_result) {
                $this->db->trans_rollback();

                // ลบไฟล์ใหม่ถ้ามี
                if (!empty($new_file)) {
                    @unlink(FCPATH . 'docs/file/' . $new_file);
                }

                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถอัพเดตข้อมูลได้']);
                return;
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                // ลบไฟล์ใหม่ถ้ามี
                if (!empty($new_file)) {
                    @unlink(FCPATH . 'docs/file/' . $new_file);
                }

                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
                return;
            }

            // ลบไฟล์เก่าถ้ามีไฟล์ใหม่
            if (!empty($new_file) && !empty($existing_form->kid_aw_form_file)) {
                $old_file_path = FCPATH . 'docs/file/' . $existing_form->kid_aw_form_file;
                if (file_exists($old_file_path)) {
                    @unlink($old_file_path);
                }
            }

            ob_end_clean();

            log_message('info', "Kid form updated successfully: ID={$form_id}, Name={$form_name}, By={$updated_by}");

            $this->json_response([
                'success' => true,
                'message' => 'แก้ไขฟอร์มสำเร็จ',
                'form_id' => $form_id,
                'form_name' => $form_name,
                'has_new_file' => !empty($new_file)
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in edit_form: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * ลบฟอร์ม (AJAX)
     */
    public function delete_form()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $this->db->select('m_id, m_fname, m_lname, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การลบฟอร์ม
            if (!$this->check_kid_forms_delete_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบฟอร์ม']);
                return;
            }

            $form_id = $this->input->post('form_id');

            if (empty($form_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบหมายเลขฟอร์ม']);
                return;
            }

            // ดึงข้อมูลฟอร์มก่อนลบ
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_form');
            $this->db->where('kid_aw_form_id', $form_id);
            $form_data = $this->db->get()->row();

            if (!$form_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลฟอร์ม']);
                return;
            }

            $deleted_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เริ่ม Transaction
            $this->db->trans_start();

            // ลบข้อมูลจากฐานข้อมูล
            $this->db->where('kid_aw_form_id', $form_id);
            $delete_result = $this->db->delete('tbl_kid_aw_form');

            if (!$delete_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลได้']);
                return;
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบ']);
                return;
            }

            // ลบไฟล์จากระบบไฟล์
            if (!empty($form_data->kid_aw_form_file)) {
                $file_path = FCPATH . 'docs/file/' . $form_data->kid_aw_form_file;
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }

            ob_end_clean();

            log_message('info', "Kid form deleted successfully: ID={$form_id}, Name={$form_data->kid_aw_form_name}, By={$deleted_by}");

            $this->json_response([
                'success' => true,
                'message' => 'ลบฟอร์มสำเร็จ',
                'form_id' => $form_id,
                'form_name' => $form_data->kid_aw_form_name
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in delete_form: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * เปลี่ยนสถานะฟอร์ม (AJAX)
     */
    public function toggle_form_status()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่
            $this->db->select('m_id, m_fname, m_lname, m_system, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การ toggle สถานะ
            if (!$this->check_kid_forms_toggle_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เปลี่ยนสถานะฟอร์ม']);
                return;
            }

            $form_id = $this->input->post('form_id');
            $status = $this->input->post('status');

            if (empty($form_id) || !in_array($status, ['0', '1'])) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            // ตรวจสอบว่าฟอร์มมีอยู่จริง
            $this->db->select('kid_aw_form_name, kid_aw_form_status');
            $this->db->from('tbl_kid_aw_form');
            $this->db->where('kid_aw_form_id', $form_id);
            $form_data = $this->db->get()->row();

            if (!$form_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลฟอร์ม']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // อัพเดตสถานะ
            $update_data = [
                'kid_aw_form_status' => (int) $status,
                'kid_aw_form_updated_by' => $updated_by,
                'kid_aw_form_updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('kid_aw_form_id', $form_id);
            $update_result = $this->db->update('tbl_kid_aw_form', $update_data);

            if (!$update_result) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถอัพเดตสถานะได้']);
                return;
            }

            ob_end_clean();

            $status_text = $status == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน';

            log_message('info', "Kid form status toggled: ID={$form_id}, Status={$status_text}, By={$updated_by}");

            $this->json_response([
                'success' => true,
                'message' => 'เปลี่ยนสถานะฟอร์มสำเร็จ',
                'form_id' => $form_id,
                'form_name' => $form_data->kid_aw_form_name,
                'new_status' => (int) $status,
                'status_text' => $status_text
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in toggle_form_status: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * *** Helper Functions สำหรับฟอร์ม - เพิ่มเติม ***
     */

    private function format_file_size($size)
    {
        if ($size <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($size) - 1) / 3);

        return sprintf("%.2f %s", $size / pow(1024, $factor), $units[$factor]);
    }

    private function format_thai_date($date)
    {
        if (empty($date)) {
            return '';
        }

        try {
            $thai_months = [
                '01' => 'มกราคม',
                '02' => 'กุมภาพันธ์',
                '03' => 'มีนาคม',
                '04' => 'เมษายน',
                '05' => 'พฤษภาคม',
                '06' => 'มิถุนายน',
                '07' => 'กรกฎาคม',
                '08' => 'สิงหาคม',
                '09' => 'กันยายน',
                '10' => 'ตุลาคม',
                '11' => 'พฤศจิกายน',
                '12' => 'ธันวาคม'
            ];

            $day = date('j', strtotime($date));
            $month = $thai_months[date('m', strtotime($date))] ?? 'ม.ค.';
            $year = date('Y', strtotime($date)) + 543;
            $time = date('H:i', strtotime($date));

            return $day . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';

        } catch (Exception $e) {
            log_message('error', 'Error formatting Thai date: ' . $e->getMessage());
            return date('d/m/Y H:i', strtotime($date));
        }
    }

    /**
     * *** Helper Function: จัดการอัพโหลดไฟล์ฟอร์ม ***
     */
    private function handle_kid_form_file_upload()
    {
        $allowed_types = ['pdf', 'doc', 'docx'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        $upload_dir = FCPATH . 'docs/file/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                return ['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์อัพโหลดได้'];
            }
        }

        $file_tmp = $_FILES['form_file']['tmp_name'];
        $file_name = $_FILES['form_file']['name'];
        $file_size = $_FILES['form_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // ตรวจสอบประเภทไฟล์
        if (!in_array($file_ext, $allowed_types)) {
            return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง (อนุญาตเฉพาะ PDF, DOC, DOCX)'];
        }

        // ตรวจสอบขนาดไฟล์
        if ($file_size > $max_size) {
            return ['success' => false, 'message' => 'ขนาดไฟล์เกิน 5MB'];
        }

        // ตรวจสอบ MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowed_mimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!isset($allowed_mimes[$file_ext]) || $mime_type !== $allowed_mimes[$file_ext]) {
            return ['success' => false, 'message' => 'ไฟล์ไม่ถูกต้องหรือถูกปลอมแปลง'];
        }

        // สร้างชื่อไฟล์ใหม่
        $new_file_name = 'kid_form_' . date('YmdHis') . '_' . uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_file_name;

        // ตรวจสอบว่าไฟล์ซ้ำหรือไม่
        $counter = 1;
        while (file_exists($upload_path)) {
            $new_file_name = 'kid_form_' . date('YmdHis') . '_' . uniqid() . '_' . $counter . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;
            $counter++;

            if ($counter > 100) {
                return ['success' => false, 'message' => 'ไม่สามารถสร้างชื่อไฟล์ที่ไม่ซ้ำได้'];
            }
        }

        // ย้ายไฟล์
        if (move_uploaded_file($file_tmp, $upload_path)) {
            log_message('info', 'Kid form file uploaded successfully: ' . $new_file_name);
            return [
                'success' => true,
                'file_name' => $new_file_name,
                'original_name' => $file_name,
                'file_size' => $file_size
            ];
        } else {
            return ['success' => false, 'message' => 'ไม่สามารถอัพโหลดไฟล์ได้'];
        }
    }



    public function api_allowance_summary()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->db->table_exists('tbl_kid_aw_ods')) {
                echo json_encode([
                    'success' => true,
                    'child_allowance' => ['total' => 0, 'submitted' => 0, 'reviewing' => 0, 'completed' => 0]
                ]);
                return;
            }

            // นับจำนวนทั้งหมด
            $this->db->from('tbl_kid_aw_ods');
            $total = $this->db->count_all_results();

            // นับตามสถานะ
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_status', 'submitted');
            $submitted = $this->db->count_all_results();

            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_status', 'reviewing');
            $reviewing = $this->db->count_all_results();

            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_status', 'completed');
            $completed = $this->db->count_all_results();

            echo json_encode([
                'success' => true,
                'child_allowance' => [
                    'total' => $total,
                    'submitted' => $submitted,
                    'reviewing' => $reviewing,
                    'completed' => $completed
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'child_allowance' => ['total' => 0, 'submitted' => 0, 'reviewing' => 0, 'completed' => 0]
            ]);
        }
    }




    /**
     * ดึงข้อมูลเงินสนับสนุนเด็กสำหรับแก้ไข (AJAX)
     */
    public function get_kid_data()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in'] || $current_user['user_type'] !== 'public') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            $kid_id = $this->input->post('kid_id');
            if (empty($kid_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบหมายเลขอ้างอิง']);
                return;
            }

            // ดึงข้อมูลเงินสนับสนุนเด็ก - ตรวจสอบสิทธิ์
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_id);
            $this->db->where('kid_aw_ods_user_type', 'public');
            $this->db->where('kid_aw_ods_user_id', $current_user['user_info']['id']);
            $kid_data = $this->db->get()->row();

            if (!$kid_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            // ดึงไฟล์ที่เกี่ยวข้อง
            $files = [];
            if ($this->db->table_exists('tbl_kid_aw_ods_files')) {
                $this->db->select('*');
                $this->db->from('tbl_kid_aw_ods_files');
                $this->db->where('kid_aw_ods_file_ref_id', $kid_id);
                $this->db->where('kid_aw_ods_file_status', 'active');
                $this->db->order_by('kid_aw_ods_file_uploaded_at', 'DESC');
                $files_query = $this->db->get();

                if ($files_query->num_rows() > 0) {
                    foreach ($files_query->result() as $file) {
                        $file_path = FCPATH . 'uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name;
                        $files[] = [
                            'file_id' => $file->kid_aw_ods_file_id,
                            'original_name' => $file->kid_aw_ods_file_original_name,
                            'file_size' => $file->kid_aw_ods_file_size,
                            'file_type' => $file->kid_aw_ods_file_type,
                            'uploaded_at' => $file->kid_aw_ods_file_uploaded_at,
                            'download_url' => base_url('uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name),
                            'file_exists' => file_exists($file_path)
                        ];
                    }
                }
            }

            ob_end_clean();

            $this->json_response([
                'success' => true,
                'data' => [
                    'kid_aw_ods_id' => $kid_data->kid_aw_ods_id,
                    'kid_aw_ods_type' => $kid_data->kid_aw_ods_type,
                    'kid_aw_ods_by' => $kid_data->kid_aw_ods_by,
                    'kid_aw_ods_phone' => $kid_data->kid_aw_ods_phone,
                    'kid_aw_ods_email' => $kid_data->kid_aw_ods_email,
                    'kid_aw_ods_number' => $kid_data->kid_aw_ods_number,
                    'kid_aw_ods_address' => $kid_data->kid_aw_ods_address,
                    'kid_aw_ods_status' => $kid_data->kid_aw_ods_status,
                    'kid_aw_ods_datesave' => $kid_data->kid_aw_ods_datesave,
                    'files' => $files
                ]
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in get_kid_data: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ]);
        }
    }

    /**
     * อัปเดตข้อมูลเงินสนับสนุนเด็ก (AJAX)
     */
    public function update_kid_data()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in'] || $current_user['user_type'] !== 'public') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            $kid_id = $this->input->post('kid_id');
            $kid_phone = $this->input->post('kid_phone');
            $kid_email = $this->input->post('kid_email');
            $kid_address = $this->input->post('kid_address');

            // Validation
            if (empty($kid_id) || empty($kid_phone)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            // ตรวจสอบสิทธิ์และข้อมูลเดิม
            $this->db->select('*');
            $this->db->from('tbl_kid_aw_ods');
            $this->db->where('kid_aw_ods_id', $kid_id);
            $this->db->where('kid_aw_ods_user_type', 'public');
            $this->db->where('kid_aw_ods_user_id', $current_user['user_info']['id']);
            $existing_data = $this->db->get()->row();

            if (!$existing_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์แก้ไข']);
                return;
            }

            // ตรวจสอบสถานะที่แก้ไขได้
            $editable_statuses = ['submitted', 'reviewing'];
            if (!in_array($existing_data->kid_aw_ods_status, $editable_statuses)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถแก้ไขได้ในสถานะปัจจุบัน']);
                return;
            }

            $updated_by = $current_user['user_info']['name'];

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตข้อมูลหลัก
            $update_data = [
                'kid_aw_ods_phone' => $kid_phone,
                'kid_aw_ods_email' => $kid_email ?: null,
                'kid_aw_ods_address' => $kid_address,
                'kid_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'kid_aw_ods_updated_by' => $updated_by
            ];

            $this->db->where('kid_aw_ods_id', $kid_id);
            $update_result = $this->db->update('tbl_kid_aw_ods', $update_data);

            if (!$update_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
                return;
            }

            // จัดการไฟล์ใหม่ (ถ้ามี)
            $uploaded_files = [];
            if (isset($_FILES['kid_additional_files']) && !empty($_FILES['kid_additional_files']['name'][0])) {
                $uploaded_files = $this->handle_additional_file_uploads($kid_id);
            }

            // บันทึกประวัติ
            if (method_exists($this->Kid_aw_ods_model, 'add_kid_aw_ods_history')) {
                $this->Kid_aw_ods_model->add_kid_aw_ods_history(
                    $kid_id,
                    'updated',
                    'อัปเดตข้อมูล: เบอร์โทร, อีเมล, ที่อยู่' . (count($uploaded_files) > 0 ? ' และเพิ่มไฟล์ ' . count($uploaded_files) . ' ไฟล์' : ''),
                    $updated_by,
                    null,
                    null
                );
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
                return;
            }

            ob_end_clean();

            log_message('info', "Kid data updated: {$kid_id} by {$updated_by}");

            $this->json_response([
                'success' => true,
                'message' => 'อัปเดตข้อมูลสำเร็จ',
                'files_uploaded' => count($uploaded_files)
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in update_kid_data: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ]);
        }
    }

    /**
     * ลบไฟล์เงินสนับสนุนเด็ก (AJAX)
     */
    public function delete_kid_file()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in'] || $current_user['user_type'] !== 'public') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            $file_id = $this->input->post('file_id');
            $kid_id = $this->input->post('kid_id');

            if (empty($file_id) || empty($kid_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            // ตรวจสอบสิทธิ์
            $this->db->select('kad.*, kaf.kid_aw_ods_file_name, kaf.kid_aw_ods_file_original_name');
            $this->db->from('tbl_kid_aw_ods_files kaf');
            $this->db->join('tbl_kid_aw_ods kad', 'kad.kid_aw_ods_id = kaf.kid_aw_ods_file_ref_id');
            $this->db->where('kaf.kid_aw_ods_file_id', $file_id);
            $this->db->where('kad.kid_aw_ods_user_type', 'public');
            $this->db->where('kad.kid_aw_ods_user_id', $current_user['user_info']['id']);
            $file_data = $this->db->get()->row();

            if (!$file_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบไฟล์หรือคุณไม่มีสิทธิ์ลบ']);
                return;
            }

            $deleted_by = $current_user['user_info']['name'];

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตสถานะไฟล์เป็น deleted
            $update_file_data = [
                'kid_aw_ods_file_status' => 'deleted',
                'kid_aw_ods_file_deleted_at' => date('Y-m-d H:i:s'),
                'kid_aw_ods_file_deleted_by' => $deleted_by
            ];

            $this->db->where('kid_aw_ods_file_id', $file_id);
            $delete_result = $this->db->update('tbl_kid_aw_ods_files', $update_file_data);

            if (!$delete_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถลบไฟล์ได้']);
                return;
            }

            // บันทึกประวัติ
            if (method_exists($this->Kid_aw_ods_model, 'add_kid_aw_ods_history')) {
                $this->Kid_aw_ods_model->add_kid_aw_ods_history(
                    $kid_id,
                    'file_deleted',
                    'ลบไฟล์: ' . $file_data->kid_aw_ods_file_original_name,
                    $deleted_by,
                    null,
                    null
                );
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบไฟล์']);
                return;
            }

            // ลบไฟล์จากระบบไฟล์ (เสริม)
            $file_path = FCPATH . 'uploads/kid_aw_ods/' . $file_data->kid_aw_ods_file_name;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }

            ob_end_clean();

            log_message('info', "Kid file deleted: {$file_id} from {$kid_id} by {$deleted_by}");

            $this->json_response([
                'success' => true,
                'message' => 'ลบไฟล์สำเร็จ'
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in delete_kid_file: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ]);
        }
    }

    /**
     * Helper: จัดการอัพโหลดไฟล์เพิ่มเติม
     */
    private function handle_additional_file_uploads($kid_id)
    {
        $uploaded_files = [];

        if (empty($_FILES['kid_additional_files']['name'][0])) {
            return $uploaded_files;
        }

        $upload_path = './uploads/kid_aw_ods/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_count = count($_FILES['kid_additional_files']['name']);

        for ($i = 0; $i < $file_count && $i < 5; $i++) {
            if ($_FILES['kid_additional_files']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file_tmp = $_FILES['kid_additional_files']['tmp_name'][$i];
            $file_name = $_FILES['kid_additional_files']['name'][$i];
            $file_size = $_FILES['kid_additional_files']['size'][$i];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_types) || $file_size > $max_size) {
                continue;
            }

            $new_file_name = 'kid_' . $kid_id . '_' . date('YmdHis') . '_' . uniqid() . '.' . $file_ext;
            $full_path = $upload_path . $new_file_name;

            if (move_uploaded_file($file_tmp, $full_path)) {
                // บันทึกในฐานข้อมูล
                $file_data = [
                    'kid_aw_ods_file_ref_id' => $kid_id,
                    'kid_aw_ods_file_name' => $new_file_name,
                    'kid_aw_ods_file_original_name' => $file_name,
                    'kid_aw_ods_file_type' => $this->get_mime_type($file_ext),
                    'kid_aw_ods_file_size' => $file_size,
                    'kid_aw_ods_file_path' => $full_path,
                    'kid_aw_ods_file_category' => 'other',
                    'kid_aw_ods_file_uploaded_at' => date('Y-m-d H:i:s'),
                    'kid_aw_ods_file_uploaded_by' => 'Public User',
                    'kid_aw_ods_file_status' => 'active'
                ];

                if ($this->db->insert('tbl_kid_aw_ods_files', $file_data)) {
                    $uploaded_files[] = [
                        'original_name' => $file_name,
                        'file_name' => $new_file_name,
                        'file_size' => $file_size
                    ];
                }
            }
        }

        return $uploaded_files;
    }



}