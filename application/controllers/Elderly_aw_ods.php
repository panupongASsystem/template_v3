<?php
// ===================================================================
// แก้ไขการส่งข้อมูล User ไป View - Elderly_aw_ods Controller (Fixed)
// ===================================================================

class Elderly_aw_ods extends CI_Controller
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

        // โหลด Elderly_aw_ods model
        $this->load->model('Elderly_aw_ods_model', 'elderly_aw_ods_model');

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

            // Debug session values
            log_message('debug', 'Session Check - mp_id: ' . var_export($mp_id, true));
            log_message('debug', 'Session Check - mp_email: ' . var_export($mp_email, true));
            log_message('debug', 'Session Check - m_id: ' . var_export($m_id, true));
            log_message('debug', 'Session Check - m_email: ' . var_export($m_email, true));

            // ตรวจสอบ Public User ก่อน
            if (!empty($mp_id) && !empty($mp_email)) {
                $public_user = $this->get_public_user_data($mp_id, $mp_email);

                if ($public_user) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_info'] = $public_user['user_info'];
                    $user_info['user_address'] = $public_user['user_address'];

                    log_message('info', 'Public user authenticated: ' . $mp_email . ' (ID: ' . $mp_id . ')');
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

                    log_message('info', 'Staff user authenticated: ' . $m_email . ' (ID: ' . $m_id . ')');
                    return $user_info;
                }
            }

            log_message('info', 'No valid login session found - user is guest');

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
            log_message('debug', "Checking public user: mp_id={$mp_id}, mp_email={$mp_email}");

            // ตรวจสอบในตาราง tbl_member_public
            $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_number, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                log_message('info', "Public user not found in database - mp_id: {$mp_id}, mp_email: {$mp_email}");
                return null;
            }

            // จัดเตรียมข้อมูล user
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

            // จัดเตรียมข้อมูลที่อยู่
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
            log_message('debug', "Checking staff user: m_id={$m_id}, m_email={$m_email}");

            // ตรวจสอบในตาราง tbl_member
            $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system, m_img, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_email', $m_email);
            $this->db->where('m_status', '1');
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                log_message('info', "Staff user not found in database - m_id: {$m_id}, m_email: {$m_email}");
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
     * *** แก้ไขฟังก์ชัน adding_elderly_aw_ods - ส่งข้อมูลไป View อย่างชัดเจน ***
     */
    public function adding_elderly_aw_ods()
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

            // *** เพิ่ม session data สำหรับ JavaScript debug ***
            $data['session_debug'] = [
                'mp_id' => $this->session->userdata('mp_id'),
                'mp_email' => $this->session->userdata('mp_email'),
                'm_id' => $this->session->userdata('m_id'),
                'm_email' => $this->session->userdata('m_email'),
                'session_count' => count($this->session->all_userdata())
            ];

            // *** เพิ่ม JavaScript variables สำหรับ debugging ***
            $data['js_debug_data'] = [
                'php_is_logged_in' => $current_user['is_logged_in'],
                'php_user_type' => $current_user['user_type'],
                'php_user_id' => $current_user['user_info']['id'] ?? null,
                'php_user_name' => $current_user['user_info']['name'] ?? null,
                'php_has_address' => !empty($current_user['user_address']),
                'timestamp' => date('Y-m-d H:i:s'),
                'environment' => ENVIRONMENT
            ];

            // *** เพิ่ม Debug Log แบบละเอียด ***
            log_message('debug', '=== ELDERLY AW ODS CONTROLLER - VIEW DATA DEBUG ===');
            log_message('debug', 'Data being sent to View: ' . json_encode([
                'is_logged_in' => $data['is_logged_in'],
                'user_type' => $data['user_type'],
                'user_info_exists' => !empty($data['user_info']),
                'user_info_id' => $data['user_info']['id'] ?? 'N/A',
                'user_info_name' => $data['user_info']['name'] ?? 'N/A',
                'user_address_exists' => !empty($data['user_address']),
                'session_debug_exists' => !empty($data['session_debug']),
                'mp_id_in_session' => $data['session_debug']['mp_id'] ?? 'N/A'
            ]));

            // *** แก้ไข: ดึงข้อมูลแบบฟอร์มพร้อมการตรวจสอบสถานะเข้มงวด ***
            $all_forms = $this->elderly_aw_ods_model->get_all_forms();

            $data['elderly_aw_form'] = [];
            $processed_forms = [];
            $active_forms_count = 0;
            $inactive_forms_count = 0;

            if ($all_forms && is_array($all_forms)) {
                log_message('debug', 'Raw forms from database: ' . count($all_forms));

                foreach ($all_forms as $form) {
                    // *** ตรวจสอบสถานะอีกครั้งเพื่อความแน่ใจ ***
                    if (
                        isset($form->elderly_aw_form_id) &&
                        isset($form->elderly_aw_form_status) &&
                        $form->elderly_aw_form_status == 1 && // เปิดใช้งาน
                        isset($form->elderly_aw_form_file) &&
                        !empty($form->elderly_aw_form_file) && // มีไฟล์
                        isset($form->elderly_aw_form_name) &&
                        !empty($form->elderly_aw_form_name) && // มีชื่อ
                        !in_array($form->elderly_aw_form_id, $processed_forms) // ไม่ซ้ำ
                    ) {
                        // *** ตรวจสอบว่าไฟล์มีอยู่จริงในระบบไฟล์หรือไม่ ***
                        $file_path = FCPATH . 'docs/file/' . $form->elderly_aw_form_file;

                        if (file_exists($file_path)) {
                            // *** เพิ่มข้อมูลเพิ่มเติมสำหรับแบบฟอร์ม ***
                            $form->file_size = filesize($file_path);
                            $form->file_extension = strtolower(pathinfo($form->elderly_aw_form_file, PATHINFO_EXTENSION));
                            $form->download_url = base_url('docs/file/' . $form->elderly_aw_form_file);

                            $data['elderly_aw_form'][] = $form;
                            $processed_forms[] = $form->elderly_aw_form_id;
                            $active_forms_count++;

                            log_message('debug', "Added active form: ID {$form->elderly_aw_form_id}, Name: {$form->elderly_aw_form_name}, Type: {$form->elderly_aw_form_type}");
                        } else {
                            log_message('info', "Form file not found: {$form->elderly_aw_form_file} for form ID: {$form->elderly_aw_form_id}");
                            $inactive_forms_count++;
                        }
                    } else {
                        // Log เหตุผลที่ไม่ผ่านการตรวจสอบ
                        $reasons = [];
                        if (!isset($form->elderly_aw_form_status) || $form->elderly_aw_form_status != 1) {
                            $reasons[] = 'status not active (' . ($form->elderly_aw_form_status ?? 'null') . ')';
                        }
                        if (!isset($form->elderly_aw_form_file) || empty($form->elderly_aw_form_file)) {
                            $reasons[] = 'no file';
                        }
                        if (!isset($form->elderly_aw_form_name) || empty($form->elderly_aw_form_name)) {
                            $reasons[] = 'no name';
                        }
                        if (in_array($form->elderly_aw_form_id ?? 'unknown', $processed_forms)) {
                            $reasons[] = 'duplicate';
                        }

                        log_message('debug', "Form excluded: ID " . ($form->elderly_aw_form_id ?? 'unknown') .
                            ", Reasons: " . implode(', ', $reasons));
                        $inactive_forms_count++;
                    }
                }
            } else {
                log_message('info', 'No forms returned from database or invalid format');
            }

            // *** Log สถิติแบบฟอร์ม ***
            log_message('info', "Forms Statistics - Active: {$active_forms_count}, Inactive/Missing: {$inactive_forms_count}, Total Processed: " . ($active_forms_count + $inactive_forms_count));

            // *** เพิ่มข้อมูลสถิติแบบฟอร์มใน data ***
            $data['forms_statistics'] = [
                'active_count' => $active_forms_count,
                'inactive_count' => $inactive_forms_count,
                'total_count' => $active_forms_count + $inactive_forms_count,
                'last_updated' => date('Y-m-d H:i:s')
            ];

            // *** ข้อมูลเพิ่มเติมสำหรับหน้า ***
            $data['page_title'] = 'ยื่นเรื่องเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'เบี้ยยังชีพผู้สูงอายุ / ผู้พิการ', 'url' => '']
            ];

            // *** Flash Messages ***
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // *** เพิ่มข้อมูลการแจ้งเตือนเกี่ยวกับแบบฟอร์ม ***
            if ($active_forms_count === 0) {
                $data['warning_message'] = $data['warning_message'] ?: 'ขณะนี้ไม่มีแบบฟอร์มที่เปิดใช้งาน กรุณาติดต่อเจ้าหน้าที่';
                log_message('info', 'No active forms available for download');
            }

            // *** การตั้งค่า Cache Control ***
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            $this->output->set_header('Pragma: no-cache');
            $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

            // *** Final Debug Check ***
            if (ENVIRONMENT === 'development') {
                log_message('debug', '=== FINAL DATA CHECK BEFORE VIEW ===');
                log_message('debug', 'Variables to be available in View:');
                log_message('debug', '- $is_logged_in: ' . var_export($data['is_logged_in'], true));
                log_message('debug', '- $user_type: ' . var_export($data['user_type'], true));
                log_message('debug', '- $user_info (exists): ' . var_export(!empty($data['user_info']), true));
                log_message('debug', '- $user_address (exists): ' . var_export(!empty($data['user_address']), true));
                log_message('debug', '- $elderly_aw_form (count): ' . count($data['elderly_aw_form']));
                log_message('debug', '- $forms_statistics: ' . json_encode($data['forms_statistics']));
                log_message('debug', '- $session_debug (exists): ' . var_export(!empty($data['session_debug']), true));
                log_message('debug', '- $js_debug_data (exists): ' . var_export(!empty($data['js_debug_data']), true));

                if (!empty($data['user_info'])) {
                    log_message('debug', '- User ID: ' . ($data['user_info']['id'] ?? 'N/A'));
                    log_message('debug', '- User Name: ' . ($data['user_info']['name'] ?? 'N/A'));
                }

                if (!empty($data['elderly_aw_form'])) {
                    log_message('debug', '- Active Forms:');
                    foreach ($data['elderly_aw_form'] as $form) {
                        log_message('debug', "  * ID: {$form->elderly_aw_form_id}, Name: {$form->elderly_aw_form_name}, Type: {$form->elderly_aw_form_type}");
                    }
                }
            }

            log_message('info', 'Loading elderly_aw_ods view with user type: ' . $data['user_type'] . ', Active forms: ' . $active_forms_count);

            // *** โหลด view พร้อมส่งข้อมูลไป ***
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/elderly_aw_ods', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

            log_message('info', 'Elderly_aw_ods view loaded successfully');
        } catch (Exception $e) {
            log_message('error', 'Critical error in adding_elderly_aw_ods: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ายื่นเรื่องเบี้ยยังชีพ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    /**
     * *** ฟังก์ชัน add_elderly_aw_ods สำหรับรับข้อมูลจากฟอร์ม - เวอร์ชันปรับปรุงสมบูรณ์ ***
     * นโยบายใหม่: ตรวจสอบ reCAPTCHA กับผู้ใช้ทุกคน ไม่มีการยกเว้น
     */
    public function add_elderly_aw_ods()
    {
        try {
            // *** 1. การตั้งค่าเริ่มต้นและตรวจสอบพื้นฐาน ***
            log_message('info', '=== ELDERLY AW ODS SUBMIT START ===');
            log_message('info', 'POST data: ' . print_r($_POST, true));
            while (ob_get_level()) {
                ob_end_clean();
            }
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                show_404();
                return;
            }

            // *** 2. รับข้อมูลและตรวจสอบสถานะ ***
            $current_user = $this->get_current_user_detailed();
            $is_guest_user = !$current_user['is_logged_in'];
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'elderly_aw_ods_submit';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'elderly_aw_ods_form';
            $dev_mode = $this->input->post('dev_mode') === '1';
            // ส่งต่อไปยัง Library
            $recaptcha_options = [
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => 'guest'
            ];

            // *** 3. ส่วนจัดการ reCAPTCHA (Logic ใหม่) ***
            $recaptcha_result = null;
            $is_dev_skip = ($dev_mode && ENVIRONMENT === 'development');

            if ($is_dev_skip) {
                log_message('info', 'DEVELOPMENT MODE: Skipping reCAPTCHA verification.');
            } else {
                // สำหรับ Production Mode, บังคับให้มี Token เสมอ
                if (empty($recaptcha_token)) {
                    log_message('error', 'Missing reCAPTCHA token in production mode.');
                    $this->json_response([
                        'success' => false,
                        'message' => 'การยืนยันความปลอดภัยไม่สมบูรณ์ กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง',
                        'error_type' => 'recaptcha_missing'
                    ]);
                    return;
                }

                // เริ่มการตรวจสอบ Token กับ Google (สำหรับผู้ใช้ทุกคน)
                $user_status_for_log = $is_guest_user ? 'GUEST' : 'LOGGED_IN';
                log_message('info', "Starting reCAPTCHA verification for {$user_status_for_log} user (Policy: All users verified).");

                try {
                    if (isset($this->recaptcha_lib)) {
                        $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, 'citizen', null, $recaptcha_options); // ใช้ type 'citizen' สำหรับทุกคน

                        if (!$recaptcha_result['success']) {
                            log_message('error', 'reCAPTCHA verification failed: ' . json_encode($recaptcha_result));
                            $this->json_response([
                                'success' => false,
                                'message' => 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง',
                                'error_type' => 'recaptcha_failed',
                                'recaptcha_data' => $recaptcha_result['data'] ?? null
                            ]);
                            return;
                        }
                        log_message('info', '✅ reCAPTCHA verification successful. Score: ' . ($recaptcha_result['data']['score'] ?? 'N/A'));
                    } else {
                        throw new Exception('reCAPTCHA library not loaded');
                    }
                } catch (Exception $e) {
                    log_message('error', 'reCAPTCHA verification error: ' . $e->getMessage());
                    $this->json_response([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการตรวจสอบความปลอดภัย',
                        'error_type' => 'recaptcha_exception'
                    ]);
                    return;
                }
            }
            // *** จบส่วนจัดการ reCAPTCHA ***

            // *** 4. การตรวจสอบความถูกต้องของข้อมูล (Form Validation) ***
            $validation_errors = [];
            if (empty($this->input->post('elderly_aw_ods_by')))
                $validation_errors[] = 'กรุณากรอกชื่อ-นามสกุล';
            if (empty($this->input->post('elderly_aw_ods_phone')))
                $validation_errors[] = 'กรุณากรอกเบอร์โทรศัพท์';
            if (empty($this->input->post('elderly_aw_ods_number')))
                $validation_errors[] = 'กรุณากรอกเลขบัตรประชาชน';

            if (!empty($validation_errors)) {
                log_message('error', 'Validation failed: ' . implode(', ', $validation_errors));
                $this->json_response([
                    'success' => false,
                    'message' => implode('<br>', $validation_errors),
                    'error_type' => 'validation_failed'
                ]);
                return;
            }

            // *** 5. การเตรียมข้อมูลเพื่อบันทึก ***
            $elderly_aw_ods_id = 'E' . date('ym') . sprintf('%05d', rand(10000, 99999));
            $save_data = [
                'elderly_aw_ods_id' => $elderly_aw_ods_id,
                'elderly_aw_ods_type' => $this->input->post('elderly_aw_ods_type') ?: 'elderly',
                'elderly_aw_ods_by' => $this->input->post('elderly_aw_ods_by'),
                'elderly_aw_ods_phone' => $this->input->post('elderly_aw_ods_phone'),
                'elderly_aw_ods_email' => $this->input->post('elderly_aw_ods_email'),
                'elderly_aw_ods_number' => $this->input->post('elderly_aw_ods_number'),
                'elderly_aw_ods_address' => $this->input->post('elderly_aw_ods_address'),
                'elderly_aw_ods_status' => 'submitted',
                'elderly_aw_ods_priority' => 'normal',
                'elderly_aw_ods_user_type' => $is_guest_user ? 'guest' : $current_user['user_type'],
                'elderly_aw_ods_datesave' => date('Y-m-d H:i:s'),
                // เพิ่มข้อมูล reCAPTCHA ลงในตาราง (ถ้ามีคอลัมน์รองรับ)
                // 'elderly_aw_ods_recaptcha_verified' => 1,
                // 'elderly_aw_ods_recaptcha_score' => $recaptcha_result['data']['score'] ?? null
            ];
            if (!$is_guest_user && isset($current_user['user_info']['id'])) {
                $save_data['elderly_aw_ods_user_id'] = $current_user['user_info']['id'];
            }

            // *** 6. การบันทึกข้อมูลและไฟล์ (Database Transaction) ***
            $this->db->trans_start();

            $this->db->insert('tbl_elderly_aw_ods', $save_data);

            $uploaded_files = $this->handle_file_uploads_for_new_elderly($elderly_aw_ods_id);
            $successful_file_inserts = 0;
            if (!empty($uploaded_files)) {
                foreach ($uploaded_files as $file) {
                    $file['elderly_aw_ods_file_ref_id'] = $elderly_aw_ods_id;
                    if ($this->db->insert('tbl_elderly_aw_ods_files', $file)) {
                        $successful_file_inserts++;
                    }
                }
            }

            $this->db->trans_complete();

            log_message('info', "Line notification send by Elderly_aw_ods ID : {$elderly_aw_ods_id}");
            $this->line_notification->send_line_elderly_aw_ods_notification($elderly_aw_ods_id);


            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed for elderly_aw_ods: ' . $elderly_aw_ods_id);
                $this->cleanup_uploaded_files_elderly($uploaded_files); // ลบไฟล์ที่อัปโหลดไปแล้วถ้า DB fail
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
                return;
            }

            // *** 7. สร้างการแจ้งเตือนและส่ง Response สำเร็จ ***
            $this->create_elderly_aw_ods_notifications($elderly_aw_ods_id, $save_data, $current_user, $successful_file_inserts);
            log_message('info', "Elderly AW ODS saved successfully: {$elderly_aw_ods_id}");
            $this->json_response([
                'success' => true,
                'message' => 'ยื่นเรื่องเบี้ยยังชีพสำเร็จ',
                'elderly_aw_ods_id' => $elderly_aw_ods_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Critical error in add_elderly_aw_ods: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดร้ายแรงในระบบ',
                'error_type' => 'system_error'
            ]);
        }
    }



    /**
     * หน้าจัดการเบี้ยยังชีพ (สำหรับ Staff)
     */
    /**
     * *** แก้ไข: หน้าจัดการเบี้ยยังชีพ (สำหรับ Staff) - ให้ m_id ที่ active เข้าได้หมด ***
     */
    public function elderly_aw_ods()
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
            $can_update_status = $this->check_elderly_update_permission($staff_check);
            $can_delete_elderly = $this->check_elderly_delete_permission($staff_check);

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_update_status'] = $can_update_status;
            $data['can_delete_elderly'] = $can_delete_elderly;
            $data['can_handle_elderly'] = true; // *** ทุกคนดูได้ ***
            $data['can_approve_elderly'] = $can_update_status; // *** ใช้สิทธิ์เดียวกับ update ***
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

            // ดึงข้อมูลเบี้ยยังชีพพร้อมกรอง
            $elderly_result = $this->elderly_aw_ods_model->get_elderly_aw_ods_with_filters($filters, $per_page, $offset);
            $elderly_aw_ods = $elderly_result['data'] ?? [];
            $total_rows = $elderly_result['total'] ?? 0;

            // *** แก้ไข: ตรวจสอบและเติมข้อมูลที่ขาดหาย ***
            if (!empty($elderly_aw_ods)) {
                foreach ($elderly_aw_ods as $index => $elderly) {
                    // เติมข้อมูลที่ขาดหาย
                    $elderly_aw_ods[$index] = $this->ensure_elderly_data_completeness($elderly);

                    // ดึงไฟล์และประวัติ
                    $elderly_aw_ods[$index]->files = $this->elderly_aw_ods_model->get_elderly_aw_ods_files($elderly->elderly_aw_ods_id);
                    $elderly_aw_ods[$index]->history = $this->elderly_aw_ods_model->get_elderly_aw_ods_history($elderly->elderly_aw_ods_id);

                    // ดึงข้อมูลเจ้าหน้าที่ที่รับผิดชอบ (ถ้ามี)
                    if (!empty($elderly_aw_ods[$index]->elderly_aw_ods_assigned_to)) {
                        $this->db->select('m_fname, m_lname, m_system');
                        $this->db->from('tbl_member');
                        $this->db->where('m_id', $elderly_aw_ods[$index]->elderly_aw_ods_assigned_to);
                        $assigned_staff = $this->db->get()->row();

                        if ($assigned_staff) {
                            $elderly_aw_ods[$index]->assigned_staff_name = trim($assigned_staff->m_fname . ' ' . $assigned_staff->m_lname);
                            $elderly_aw_ods[$index]->assigned_staff_system = $assigned_staff->m_system;
                        }
                    }
                }
            }

            // เตรียมข้อมูลสำหรับแสดงผล
            $data['elderly_aw_ods'] = $this->prepare_elderly_aw_ods_list_for_display($elderly_aw_ods);

            // สถิติเบี้ยยังชีพ
            $elderly_summary = $this->elderly_aw_ods_model->get_elderly_aw_ods_statistics();
            $data['elderly_summary'] = $elderly_summary;
            $data['status_counts'] = $this->calculate_elderly_aw_ods_status_counts($data['elderly_aw_ods']);

            // ตัวเลือกสำหรับ Filter
            $status_options = [
                ['value' => 'submitted', 'label' => 'ยื่นเรื่องแล้ว'],
                ['value' => 'reviewing', 'label' => 'กำลังพิจารณา'],
                ['value' => 'approved', 'label' => 'อนุมัติแล้ว'],
                ['value' => 'rejected', 'label' => 'ไม่อนุมัติ'],
                ['value' => 'completed', 'label' => 'เสร็จสิ้น']
            ];

            $type_options = [
                ['value' => 'elderly', 'label' => 'ผู้สูงอายุ'],
                ['value' => 'disabled', 'label' => 'ผู้พิการ']
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

            // รายการเบี้ยยังชีพล่าสุด
            $recent_elderly = $this->elderly_aw_ods_model->get_recent_elderly_aw_ods(10);

            // รายการเจ้าหน้าที่สำหรับ Assignment
            $staff_list = $this->get_staff_list_for_assignment();

            // Pagination Setup
            $pagination_config = [
                'base_url' => site_url('Elderly_aw_ods/elderly_aw_ods'),
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
                'recent_elderly' => $recent_elderly,
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
                    'can_delete' => $data['can_delete_elderly'],
                    'can_handle' => $data['can_handle_elderly'],
                    'can_approve' => $data['can_approve_elderly'],
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
                ['title' => 'จัดการเบี้ยยังชีพ', 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'จัดการเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Elderly AW ODS management final data: ' . json_encode([
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_elderly'],
                    'can_handle' => $data['can_handle_elderly'],
                    'can_approve' => $data['can_approve_elderly'],
                    'can_update_status' => $data['can_update_status'],
                    'grant_user_ref_id' => $staff_check->grant_user_ref_id,
                    'elderly_count' => count($data['elderly_aw_ods']),
                    'total_rows' => $total_rows,
                    'filters_applied' => array_filter($filters),
                    'user_info_properties' => get_object_vars($user_info_object),
                    'user_info_has_pname' => property_exists($user_info_object, 'pname'),
                    'user_info_has_elderly_number' => property_exists($user_info_object, 'elderly_aw_ods_number')
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/elderly_aw_ods', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in elderly_aw_ods management: ' . $e->getMessage());
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
    private function check_elderly_update_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถ update ได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Elderly update permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 50 
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('debug', "user_admin without grant_user_ref_id: {$staff_data->m_fname} {$staff_data->m_lname}");
                    return false;
                }

                // *** แก้ไข: เช็คที่ grant_user_ref_id โดยตรง ***
                // แปลง grant_user_ref_id เป็น array (กรณีมีหลายสิทธิ์คั่นด้วย comma)
                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));

                // เช็คว่ามีสิทธิ์ 50 หรือไม่ (สำหรับเบี้ยยังชีพ)
                $has_permission = in_array('50', $grant_ids);

                log_message('info', "user_admin update permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "grant_user_ref_id: {$staff_data->grant_user_ref_id} - " .
                    "grant_ids: " . implode(',', $grant_ids) . " - " .
                    "has_50: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            // อื่นๆ ไม่สามารถ update ได้
            log_message('info', "Elderly update permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking elderly update permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ใหม่: ฟังก์ชันเช็คสิทธิ์การลบ ***
     */
    private function check_elderly_delete_permission($staff_data)
    {
        try {
            // เฉพาะ system_admin และ super_admin ที่สามารถลบได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Elderly delete permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // อื่นๆ ลบไม่ได้
            log_message('info', "Elderly delete permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking elderly delete permission: ' . $e->getMessage());
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
     * ฟังก์ชันเช็คสิทธิ์การจัดการเบี้ยยังชีพ
     */
    private function check_elderly_handle_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถดำเนินการได้ทุกอย่าง
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Elderly handle permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_name = 50 
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('debug', "user_admin without grant_user_ref_id: {$staff_data->m_fname} {$staff_data->m_lname}");
                    return false;
                }

                // แปลง grant_user_ref_id เป็น array (กรณีมีหลายสิทธิ์คั่นด้วย comma)
                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));

                // เช็คว่ามีสิทธิ์ 50 หรือไม่ (สำหรับเบี้ยยังชีพ)
                $this->db->select('grant_user_id');
                $this->db->from('tbl_grant_user');
                $this->db->where('grant_user_name', '50'); // รหัสสำหรับเบี้ยยังชีพ
                $this->db->where_in('grant_user_id', $grant_ids);
                $grant_50 = $this->db->get()->row();

                $has_permission = !empty($grant_50);

                log_message('info', "user_admin elderly permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "grant_ids: " . implode(',', $grant_ids) . " - " .
                    "has_50: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            // end_user หรือระดับอื่นๆ ไม่สามารถดำเนินการได้
            log_message('info', "Elderly handle permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking elderly handle permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ฟังก์ชันเช็คสิทธิ์การอนุมัติเบี้ยยังชีพ
     */
    private function check_elderly_approve_permission($staff_data)
    {
        try {
            // เฉพาะ system_admin และ super_admin ที่สามารถอนุมัติได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Elderly approve permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_name = 50 (สำหรับการอนุมัติ)
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    return false;
                }

                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));

                // เช็คว่ามีสิทธิ์ 50 หรือไม่ (สำหรับการอนุมัติเบี้ยยังชีพ)
                $this->db->select('grant_user_id');
                $this->db->from('tbl_grant_user');
                $this->db->where('grant_user_name', '50'); // รหัสสำหรับการอนุมัติ
                $this->db->where_in('grant_user_id', $grant_ids);
                $grant_50 = $this->db->get()->row();

                $has_permission = !empty($grant_50);

                log_message('info', "user_admin approve permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "has_50: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking elderly approve permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงรายการเจ้าหน้าที่สำหรับการมอบหมาย
     */
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

    // *** Helper Functions ***
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
            // โหลดข้อมูล Activity
            if (isset($this->activity_model) && method_exists($this->activity_model, 'activity_frontend')) {
                $result = $this->activity_model->activity_frontend();
                $data['qActivity'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            // โหลดข้อมูล Hot News
            if (isset($this->HotNews_model) && method_exists($this->HotNews_model, 'hotnews_frontend')) {
                $result = $this->HotNews_model->hotnews_frontend();
                $data['qHotnews'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            log_message('debug', 'Navbar data loaded successfully');

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }



    // Helper functions อื่นๆ ยังคงเดิม...
    private function prepare_elderly_aw_ods_list_for_display($elderly_aw_ods_list)
    {
        $prepared_list = [];

        // ตรวจสอบ input
        if (empty($elderly_aw_ods_list) || !is_array($elderly_aw_ods_list)) {
            return $prepared_list;
        }

        foreach ($elderly_aw_ods_list as $record) {
            // สร้าง object ที่มีข้อมูลครบถ้วน
            $record_object = new stdClass();

            // *** แก้ไข: ใช้ helper function แทน ***
            $record_object->elderly_aw_ods_id = $this->get_value_safe($record, 'elderly_aw_ods_id', '');
            $record_object->elderly_aw_ods_type = $this->get_value_safe($record, 'elderly_aw_ods_type', 'elderly');
            $record_object->elderly_aw_ods_status = $this->get_value_safe($record, 'elderly_aw_ods_status', 'submitted');
            $record_object->elderly_aw_ods_by = $this->get_value_safe($record, 'elderly_aw_ods_by', '');
            $record_object->elderly_aw_ods_phone = $this->get_value_safe($record, 'elderly_aw_ods_phone', '');
            $record_object->elderly_aw_ods_email = $this->get_value_safe($record, 'elderly_aw_ods_email', '');
            $record_object->elderly_aw_ods_number = $this->get_value_safe($record, 'elderly_aw_ods_number', '');
            $record_object->elderly_aw_ods_address = $this->get_value_safe($record, 'elderly_aw_ods_address', '');
            $record_object->elderly_aw_ods_datesave = $this->get_value_safe($record, 'elderly_aw_ods_datesave', '');
            $record_object->elderly_aw_ods_updated_at = $this->get_value_safe($record, 'elderly_aw_ods_updated_at', null);
            $record_object->elderly_aw_ods_priority = $this->get_value_safe($record, 'elderly_aw_ods_priority', 'normal');
            $record_object->elderly_aw_ods_user_type = $this->get_value_safe($record, 'elderly_aw_ods_user_type', 'guest');
            $record_object->elderly_aw_ods_user_id = $this->get_value_safe($record, 'elderly_aw_ods_user_id', null);
            $record_object->elderly_aw_ods_assigned_to = $this->get_value_safe($record, 'elderly_aw_ods_assigned_to', null);
            $record_object->elderly_aw_ods_notes = $this->get_value_safe($record, 'elderly_aw_ods_notes', '');
            $record_object->elderly_aw_ods_completed_at = $this->get_value_safe($record, 'elderly_aw_ods_completed_at', null);

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
            $record_object->status_display = $this->get_elderly_aw_ods_status_display($record_object->elderly_aw_ods_status);
            $record_object->status_class = $this->get_elderly_aw_ods_status_class($record_object->elderly_aw_ods_status);
            $record_object->status_icon = $this->get_elderly_aw_ods_status_icon($record_object->elderly_aw_ods_status);
            $record_object->status_color = $this->get_elderly_aw_ods_status_color($record_object->elderly_aw_ods_status);
            $record_object->type_display = $this->get_elderly_aw_ods_type_display($record_object->elderly_aw_ods_type);
            $record_object->type_icon = $this->get_elderly_aw_ods_type_icon($record_object->elderly_aw_ods_type);

            $record_object->latest_update = $record_object->elderly_aw_ods_updated_at ?: $record_object->elderly_aw_ods_datesave;

            $prepared_list[] = $record_object;
        }

        return $prepared_list;
    }




    private function ensure_elderly_data_completeness($elderly_data)
    {
        $required_fields = [
            'elderly_aw_ods_id' => '',
            'elderly_aw_ods_type' => 'elderly',
            'elderly_aw_ods_status' => 'submitted',
            'elderly_aw_ods_by' => '',
            'elderly_aw_ods_phone' => '',
            'elderly_aw_ods_email' => '',
            'elderly_aw_ods_number' => '',
            'elderly_aw_ods_address' => '',
            'elderly_aw_ods_datesave' => '',
            'elderly_aw_ods_updated_at' => null,
            'elderly_aw_ods_priority' => 'normal',
            'elderly_aw_ods_user_type' => 'guest',
            'elderly_aw_ods_user_id' => null,
            'elderly_aw_ods_assigned_to' => null,
            'elderly_aw_ods_notes' => '',
            'elderly_aw_ods_completed_at' => null,
            'guest_province' => '',
            'guest_amphoe' => '',
            'guest_district' => '',
            'guest_zipcode' => '',
            'assigned_staff_name' => '',
            'assigned_staff_system' => '',
            'files' => [],
            'history' => []
        ];

        if (is_object($elderly_data)) {
            // ถ้าเป็น object
            foreach ($required_fields as $field => $default_value) {
                if (!property_exists($elderly_data, $field)) {
                    $elderly_data->$field = $default_value;
                } elseif ($elderly_data->$field === null && $default_value !== null) {
                    $elderly_data->$field = $default_value;
                }
            }
        } elseif (is_array($elderly_data)) {
            // ถ้าเป็น array ให้แปลงเป็น object
            $object_data = new stdClass();
            foreach ($required_fields as $field => $default_value) {
                $object_data->$field = $elderly_data[$field] ?? $default_value;
            }
            $elderly_data = $object_data;
        } else {
            // ถ้าไม่ใช่ทั้งสองอย่าง ให้สร้าง object ใหม่
            $elderly_data = new stdClass();
            foreach ($required_fields as $field => $default_value) {
                $elderly_data->$field = $default_value;
            }
        }

        return $elderly_data;
    }




    private function calculate_elderly_aw_ods_status_counts($elderly_aw_ods_list)
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
        if (empty($elderly_aw_ods_list) || !is_array($elderly_aw_ods_list)) {
            return $status_counts;
        }

        $status_counts['total'] = count($elderly_aw_ods_list);

        foreach ($elderly_aw_ods_list as $record) {
            // *** แก้ไข: รองรับทั้ง object และ array ***
            $status = '';

            if (is_object($record)) {
                // ถ้าเป็น object
                $status = $record->elderly_aw_ods_status ?? '';
            } elseif (is_array($record)) {
                // ถ้าเป็น array
                $status = $record['elderly_aw_ods_status'] ?? '';
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
    private function get_elderly_aw_ods_status_display($status)
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

    private function get_elderly_aw_ods_status_class($status)
    {
        $class_map = [
            'submitted' => 'elderly-aw-ods-status-submitted',
            'reviewing' => 'elderly-aw-ods-status-reviewing',
            'approved' => 'elderly-aw-ods-status-approved',
            'rejected' => 'elderly-aw-ods-status-rejected',
            'completed' => 'elderly-aw-ods-status-completed'
        ];

        return $class_map[$status] ?? 'elderly-aw-ods-status-unknown';
    }

    private function get_elderly_aw_ods_status_icon($status)
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

    private function get_elderly_aw_ods_status_color($status)
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

    private function get_elderly_aw_ods_type_display($type)
    {
        $type_map = [
            'elderly' => 'ผู้สูงอายุ',
            'disabled' => 'ผู้พิการ'
        ];

        return $type_map[$type] ?? $type;
    }

    private function get_elderly_aw_ods_type_icon($type)
    {
        $icon_map = [
            'elderly' => 'fas fa-user-clock',
            'disabled' => 'fas fa-wheelchair'
        ];

        return $icon_map[$type] ?? 'fas fa-heart';
    }


    /**
     * *** ใหม่: สร้างการแจ้งเตือนสำหรับเบี้ยยังชีพ ***
     */
    private function create_elderly_aw_ods_notifications($elderly_aw_ods_id, $elderly_data, $current_user, $file_count = 0)
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('debug', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($elderly_data['elderly_aw_ods_type'] === 'disabled') ? 'ผู้พิการ' : 'ผู้สูงอายุ';

            // 1. แจ้งเตือน Staff ทั้งหมด
            $staff_data_json = json_encode([
                'elderly_aw_ods_id' => $elderly_aw_ods_id,
                'type' => $elderly_data['elderly_aw_ods_type'],
                'type_display' => $type_display,
                'requester' => $elderly_data['elderly_aw_ods_by'],
                'phone' => $elderly_data['elderly_aw_ods_phone'],
                'email' => $elderly_data['elderly_aw_ods_email'],
                'id_card' => $elderly_data['elderly_aw_ods_number'],
                'user_type' => $current_user['user_type'],
                'is_guest' => ($current_user['user_type'] === 'guest'),
                'created_at' => $current_time,
                'file_count' => $file_count,
                'has_id_card' => !empty($elderly_data['elderly_aw_ods_number']),
                'notification_type' => 'staff_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'elderly_aw_ods',
                'title' => 'เบี้ยยังชีพใหม่',
                'message' => "มีการยื่นเรื่องเบี้ยยังชีพ{$type_display}ใหม่ โดย {$elderly_data['elderly_aw_ods_by']} หมายเลขอ้างอิง: {$elderly_aw_ods_id}",
                'reference_id' => 0,
                'reference_table' => 'tbl_elderly_aw_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-heart',
                'url' => site_url("Elderly_aw_ods/elderly_detail/{$elderly_aw_ods_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => ($current_user['is_logged_in'] && isset($current_user['user_info']['id'])) ? intval($current_user['user_info']['id']) : 0,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff notification created for elderly_aw_ods: {$elderly_aw_ods_id}");
            }

            // 2. แจ้งเตือน Public User ที่ login (ถ้ามี)
            if (
                $current_user['is_logged_in'] &&
                $current_user['user_type'] === 'public' &&
                isset($current_user['user_info']['id'])
            ) {

                $individual_data_json = json_encode([
                    'elderly_aw_ods_id' => $elderly_aw_ods_id,
                    'type' => $elderly_data['elderly_aw_ods_type'],
                    'type_display' => $type_display,
                    'status' => 'submitted',
                    'created_at' => $current_time,
                    'file_count' => $file_count,
                    'follow_url' => site_url("Elderly_aw_ods/my_elderly_aw_ods"),
                    'notification_type' => 'individual_confirmation'
                ], JSON_UNESCAPED_UNICODE);

                $individual_notification = [
                    'type' => 'elderly_aw_ods',
                    'title' => 'ยื่นเรื่องเบี้ยยังชีพสำเร็จ',
                    'message' => "เรื่องเบี้ยยังชีพ{$type_display} หมายเลขอ้างอิง: {$elderly_aw_ods_id} ได้รับการบันทึกเรียบร้อยแล้ว",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_elderly_aw_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($current_user['user_info']['id']),
                    'priority' => 'high',
                    'icon' => 'fas fa-check-circle',
                    'url' => site_url("Elderly_aw_ods/my_elderly_aw_ods_detail/{$elderly_aw_ods_id}"),
                    'data' => $individual_data_json,
                    'created_at' => $current_time,
                    'created_by' => intval($current_user['user_info']['id']),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $individual_result = $this->db->insert('tbl_notifications', $individual_notification);

                if ($individual_result) {
                    log_message('info', "Individual notification created for elderly_aw_ods: {$elderly_aw_ods_id}");
                }
            }

            log_message('info', "Elderly AW ODS notifications created successfully: {$elderly_aw_ods_id}");

        } catch (Exception $e) {
            log_message('error', 'Error creating elderly_aw_ods notifications: ' . $e->getMessage());
            throw $e;
        }
    }



    /**
     * *** ฟังก์ชันจัดการไฟล์สำหรับการสร้างใหม่ ***
     */
    private function handle_file_uploads_for_new_elderly($elderly_aw_ods_id)
    {
        $uploaded_files = [];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        $upload_dir = './uploads/elderly_aw_ods/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                log_message('error', 'Failed to create upload directory: ' . $upload_dir);
                return [];
            }
        }

        if (!isset($_FILES['elderly_aw_ods_files']) || !is_array($_FILES['elderly_aw_ods_files']['name'])) {
            return [];
        }

        $file_count = count($_FILES['elderly_aw_ods_files']['name']);
        $upload_timestamp = date('YmdHis') . '_' . uniqid();

        for ($i = 0; $i < $file_count; $i++) {
            // ตรวจสอบข้อผิดพลาดการอัพโหลด
            if ($_FILES['elderly_aw_ods_files']['error'][$i] !== UPLOAD_ERR_OK) {
                log_message('debug', 'File upload error at index ' . $i . ': ' . $_FILES['elderly_aw_ods_files']['error'][$i]);
                continue;
            }

            $file_tmp = $_FILES['elderly_aw_ods_files']['tmp_name'][$i];
            $file_name = $_FILES['elderly_aw_ods_files']['name'][$i];
            $file_size = $_FILES['elderly_aw_ods_files']['size'][$i];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // ตรวจสอบว่าไฟล์ว่างหรือไม่
            if (empty($file_name)) {
                continue;
            }

            // ตรวจสอบประเภทไฟล์
            if (!in_array($file_ext, $allowed_types)) {
                log_message('debug', 'Invalid file type: ' . $file_ext . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบขนาดไฟล์
            if ($file_size > $max_size) {
                log_message('debug', 'File size too large: ' . $file_size . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file_tmp) || !is_uploaded_file($file_tmp)) {
                log_message('debug', 'Uploaded file not found or invalid: ' . $file_tmp);
                continue;
            }

            // ตรวจสอบไฟล์ให้ปลอดภัย
            if (!$this->is_safe_file($file_tmp, $file_ext)) {
                log_message('debug', 'Unsafe file detected: ' . $file_name);
                continue;
            }

            // สร้างชื่อไฟล์ใหม่แบบ unique
            $clean_elderly_id = preg_replace('/[^a-zA-Z0-9]/', '', $elderly_aw_ods_id);
            $new_file_name = 'elderly_' . $clean_elderly_id . '_' . $upload_timestamp . '_' . $i . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;

            // ตรวจสอบว่าไฟล์ซ้ำในระบบไฟล์หรือไม่
            $counter = 1;
            while (file_exists($upload_path)) {
                $new_file_name = 'elderly_' . $clean_elderly_id . '_' . $upload_timestamp . '_' . $i . '_dup' . $counter . '.' . $file_ext;
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
                    'elderly_aw_ods_file_original_name' => $file_name,
                    'elderly_aw_ods_file_name' => $new_file_name,
                    'elderly_aw_ods_file_size' => $file_size,
                    'elderly_aw_ods_file_type' => $this->get_mime_type($file_ext),
                    'elderly_aw_ods_file_path' => $upload_path,
                    'elderly_aw_ods_file_uploaded_at' => date('Y-m-d H:i:s')
                ];

                log_message('info', 'File uploaded successfully: ' . $new_file_name . ' for ' . $elderly_aw_ods_id);
            } else {
                log_message('error', 'Failed to move uploaded file: ' . $file_name . ' to ' . $upload_path);
            }
        }

        log_message('info', 'Total files uploaded: ' . count($uploaded_files) . ' for ' . $elderly_aw_ods_id);
        return $uploaded_files;
    }





    private function handle_file_uploads()
    {
        $uploaded_files = [];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        $upload_dir = './uploads/elderly_aw_ods/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                log_message('error', 'Failed to create upload directory: ' . $upload_dir);
                return [];
            }
        }

        if (!isset($_FILES['elderly_aw_ods_files']) || !is_array($_FILES['elderly_aw_ods_files']['name'])) {
            return [];
        }

        $file_count = count($_FILES['elderly_aw_ods_files']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            // ตรวจสอบข้อผิดพลาดการอัพโหลด
            if ($_FILES['elderly_aw_ods_files']['error'][$i] !== UPLOAD_ERR_OK) {
                log_message('debug', 'File upload error at index ' . $i . ': ' . $_FILES['elderly_aw_ods_files']['error'][$i]);
                continue;
            }

            $file_tmp = $_FILES['elderly_aw_ods_files']['tmp_name'][$i];
            $file_name = $_FILES['elderly_aw_ods_files']['name'][$i];
            $file_size = $_FILES['elderly_aw_ods_files']['size'][$i];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // ตรวจสอบประเภทไฟล์
            if (!in_array($file_ext, $allowed_types)) {
                log_message('debug', 'Invalid file type: ' . $file_ext . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบขนาดไฟล์
            if ($file_size > $max_size) {
                log_message('debug', 'File size too large: ' . $file_size . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file_tmp) || !is_uploaded_file($file_tmp)) {
                log_message('debug', 'Uploaded file not found or invalid: ' . $file_tmp);
                continue;
            }

            // สร้างชื่อไฟล์ใหม่
            $new_file_name = 'elderly_aw_ods_' . date('YmdHis') . '_' . $i . '_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;

            // ย้ายไฟล์
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $uploaded_files[] = [
                    'original_name' => $file_name,
                    'file_name' => $new_file_name,
                    'file_size' => $file_size,
                    'file_type' => $file_ext,
                    'upload_date' => date('Y-m-d H:i:s'),
                    'upload_path' => $upload_path
                ];

                log_message('info', 'File uploaded successfully: ' . $new_file_name);
            } else {
                log_message('error', 'Failed to move uploaded file: ' . $file_name . ' to ' . $upload_path);
            }
        }

        log_message('info', 'Total files uploaded: ' . count($uploaded_files));
        return $uploaded_files;
    }



    /**
     * เตรียมข้อมูลเบี้ยยังชีพสำหรับแสดงผล (Guest-safe)
     */
    private function prepare_elderly_aw_ods_for_display($elderly_aw_ods)
    {
        if (is_object($elderly_aw_ods)) {
            $elderly_aw_ods_array = [
                'elderly_aw_ods_id' => $elderly_aw_ods->elderly_aw_ods_id ?? '',
                'elderly_aw_ods_type' => $elderly_aw_ods->elderly_aw_ods_type ?? 'elderly',
                'elderly_aw_ods_status' => $elderly_aw_ods->elderly_aw_ods_status ?? '',
                'elderly_aw_ods_by' => $elderly_aw_ods->elderly_aw_ods_by ?? '',
                'elderly_aw_ods_phone' => $elderly_aw_ods->elderly_aw_ods_phone ?? '',
                'elderly_aw_ods_datesave' => $elderly_aw_ods->elderly_aw_ods_datesave ?? '',
                'elderly_aw_ods_updated_at' => $elderly_aw_ods->elderly_aw_ods_updated_at ?? null
            ];
        } else {
            $elderly_aw_ods_array = $elderly_aw_ods;
        }

        $elderly_aw_ods_array['status_display'] = $this->get_elderly_aw_ods_status_display($elderly_aw_ods_array['elderly_aw_ods_status']);
        $elderly_aw_ods_array['status_class'] = $this->get_elderly_aw_ods_status_class($elderly_aw_ods_array['elderly_aw_ods_status']);
        $elderly_aw_ods_array['status_icon'] = $this->get_elderly_aw_ods_status_icon($elderly_aw_ods_array['elderly_aw_ods_status']);
        $elderly_aw_ods_array['status_color'] = $this->get_elderly_aw_ods_status_color($elderly_aw_ods_array['elderly_aw_ods_status']);
        $elderly_aw_ods_array['type_display'] = $this->get_elderly_aw_ods_type_display($elderly_aw_ods_array['elderly_aw_ods_type']);
        $elderly_aw_ods_array['type_icon'] = $this->get_elderly_aw_ods_type_icon($elderly_aw_ods_array['elderly_aw_ods_type']);

        return $elderly_aw_ods_array;
    }



    /**
     * ปรับปรุง Controller สำหรับ Follow Elderly AW ODS พร้อม reCAPTCHA Integration
     */

    // *** เพิ่มฟังก์ชัน follow_elderly_aw_ods สำหรับ guest user พร้อม reCAPTCHA ***
    public function follow_elderly_aw_ods()
    {
        log_message('info', '=== FOLLOW ELDERLY AW ODS START ===');

        // ตรวจสอบ staff user - ใช้ method ที่มีอยู่แล้ว
        if ($this->is_staff_user()) {
            $this->session->set_flashdata('warning_message', 'กรุณาใช้ระบบภายในสำหรับเจ้าหน้าที่');
            redirect('Dashboard');
            return;
        }

        // ตรวจสอบ staff user
        if ($this->is_staff_user()) {
            $this->session->set_flashdata('warning_message', 'กรุณาใช้ระบบภายในสำหรับเจ้าหน้าที่');
            redirect('Dashboard');
            return;
        }

        // ตรวจสอบ logged in user - หากล็อกอินแล้วให้ไปหน้า my_elderly_aw_ods
        $current_user = $this->get_current_user_detailed();
        if ($current_user['is_logged_in']) {
            redirect('Elderly_aw_ods/my_elderly_aw_ods');
            return;
        }

        try {
            // === ขั้นตอนที่ 1: จัดการ POST Request (การค้นหาด้วย reCAPTCHA) ===
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return $this->handleElderlySearchPost();
            }

            // === ขั้นตอนที่ 2: จัดการ GET Request (แสดงผลลัพธ์) ===
            return $this->handleElderlySearchGet();

        } catch (Exception $e) {
            log_message('error', 'Error in follow_elderly_aw_ods: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการโหลดหน้า', 500);
        }
    }

    /**
     * จัดการ POST Request สำหรับการค้นหาพร้อม reCAPTCHA
     */
    private function handleElderlySearchPost()
    {
        log_message('info', 'Handling POST search request for elderly AW ODS');

        $ref_id = $this->input->post('ref');
        $recaptcha_token = $this->input->post('g-recaptcha-response');
        $recaptcha_action = $this->input->post('recaptcha_action') ?: 'follow_elderly_aw_ods_search';
        $recaptcha_source = $this->input->post('recaptcha_source') ?: 'follow_elderly_aw_ods_form';

        // ตรวจสอบข้อมูลพื้นฐาน
        if (empty($ref_id)) {
            $this->session->set_flashdata('error_message', 'กรุณากรอกหมายเลขอ้างอิง');
            redirect('Elderly_aw_ods/follow_elderly_aw_ods');
            return;
        }

        // ตรวจสอบ reCAPTCHA (บังคับ)
        if (empty($recaptcha_token)) {
            $this->session->set_flashdata('error_message', 'กรุณายืนยัน reCAPTCHA');
            redirect('Elderly_aw_ods/follow_elderly_aw_ods');
            return;
        }

        // ส่งต่อไปยัง reCAPTCHA Library
        $recaptcha_options = [
            'action' => $recaptcha_action,
            'source' => $recaptcha_source,
            'user_type_detected' => 'guest'
        ];

        $recaptcha_result = $this->recaptcha_lib->verify(
            $recaptcha_token,
            'citizen',
            null,
            $recaptcha_options
        );

        if (!$recaptcha_result['success']) {
            log_message('error', 'reCAPTCHA verification failed for elderly AW ODS: ' . $recaptcha_result['message']);
            $this->session->set_flashdata('error_message', 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่');
            redirect('Elderly_aw_ods/follow_elderly_aw_ods');
            return;
        }

        log_message('info', 'reCAPTCHA verification successful for elderly AW ODS');

        // หลังจากตรวจสอบแล้ว redirect ไป GET พร้อม ref
        redirect('Elderly_aw_ods/follow_elderly_aw_ods?ref=' . urlencode($ref_id));
    }

    /**
     * จัดการ GET Request สำหรับแสดงผลลัพธ์
     */
    private function handleElderlySearchGet()
    {
        log_message('info', 'Handling GET display request for elderly AW ODS');

        try {
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();
            $data['is_logged_in'] = false; // Force guest mode
            $data['user_info'] = null;
            $data['user_type'] = 'guest';

            // ตรวจสอบ reference ID จาก URL
            $ref_id = $this->input->get('ref');
            $data['ref_id'] = $ref_id;
            $data['search_performed'] = false;

            $data['page_title'] = 'ติดตามสถานะเบี้ยยังชีพ';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'ติดตามสถานะเบี้ยยังชีพ', 'url' => '']
            ];

            // ถ้ามี ref_id ให้ค้นหาข้อมูล (เฉพาะ guest เท่านั้น)
            $elderly_aw_ods_info = null;
            if (!empty($ref_id)) {
                log_message('info', 'Searching for elderly AW ODS with ref: ' . $ref_id);

                try {
                    // *** แก้ไข: เพิ่มเงื่อนไขจำกัดเฉพาะ guest ***
                    $elderly_aw_ods_info = $this->get_elderly_aw_ods_for_guest_only($ref_id);

                    if ($elderly_aw_ods_info) {
                        $elderly_aw_ods_info = $this->prepare_elderly_aw_ods_for_display($elderly_aw_ods_info);
                        $data['search_performed'] = true;
                        log_message('info', "Guest tracking: Found elderly AW ODS {$ref_id} for guest tracking");
                    } else {
                        log_message('info', "Guest tracking: No guest elderly AW ODS found for {$ref_id}");
                        $this->session->set_flashdata('warning_message', 'ไม่พบข้อมูลหมายเลขอ้างอิง: ' . $ref_id);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching elderly_aw_ods for guest: ' . $e->getMessage());
                    $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการค้นหาข้อมูล');
                }
            }

            $data['elderly_aw_ods_info'] = $elderly_aw_ods_info;

            // Flash messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/follow_elderly_aw_ods', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

            log_message('info', '=== FOLLOW ELDERLY AW ODS END ===');

        } catch (Exception $e) {
            log_message('error', 'Critical error in handleElderlySearchGet: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าติดตามสถานะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Elderly_aw_ods/adding_elderly_aw_ods');
            }
        }
    }

    // *** ฟังก์ชันอื่นๆ ยังคงเดิม ***
    public function my_elderly_aw_ods()
    {
        try {
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();
            $data['is_logged_in'] = $login_info['is_logged_in'];
            $data['user_info'] = $login_info['user_info'];
            $data['user_type'] = $login_info['user_type'];
            $data['user_address'] = $login_info['user_address'];

            if (!$data['is_logged_in'] || !$data['user_info']) {
                log_message('info', 'Unauthorized access to my_elderly_aw_ods - user not logged in');
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อนเพื่อดูเบี้ยยังชีพของคุณ');
                redirect('User');
                return;
            }

            // ดึงรายการเบี้ยยังชีพของผู้ใช้
            $user_elderly_aw_ods = [];
            try {
                $elderly_aw_ods_result = $this->elderly_aw_ods_model->get_elderly_aw_ods_by_user(
                    $data['user_info']['id'],
                    $data['user_type']
                );

                if ($elderly_aw_ods_result) {
                    $user_elderly_aw_ods = $this->prepare_elderly_aw_ods_list_for_display($elderly_aw_ods_result);
                }

            } catch (Exception $e) {
                log_message('error', 'Error loading user elderly_aw_ods: ' . $e->getMessage());
                $user_elderly_aw_ods = [];
            }

            $data['elderly_aw_ods'] = $user_elderly_aw_ods;
            $data['status_counts'] = $this->calculate_elderly_aw_ods_status_counts($user_elderly_aw_ods);

            $data['page_title'] = 'เบี้ยยังชีพของฉัน';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'เบี้ยยังชีพของฉัน', 'url' => '']
            ];

            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_elderly_aw_ods', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in my_elderly_aw_ods: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าเบี้ยยังชีพ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Elderly_aw_ods/adding_elderly_aw_ods');
            }
        }
    }

    /**
     * *** Helper Functions ที่จำเป็นเท่านั้น ***
     */

    /**
     * ตรวจสอบว่าเป็น staff user หรือไม่ (เพิ่มใหม่)
     */
    private function is_staff_user()
    {
        // ตรวจสอบจาก session หรือ user type
        $current_user = $this->get_current_user_detailed();

        if ($current_user['is_logged_in']) {
            $user_type = $current_user['user_type'] ?? '';
            return in_array($user_type, ['staff', 'admin', 'super_admin']);
        }

        // ตรวจสอบจาก User Agent (เป็น fallback)
        $user_agent = $this->input->server('HTTP_USER_AGENT');
        $staff_patterns = ['/staff/i', '/admin/i', '/management/i'];

        foreach ($staff_patterns as $pattern) {
            if (preg_match($pattern, $user_agent)) {
                return true;
            }
        }

        return false;
    }


    /**
     * *** เพิ่มฟังก์ชันใหม่: ค้นหาเบี้ยยังชีพเฉพาะ guest เท่านั้น ***
     */
    private function get_elderly_aw_ods_for_guest_only($elderly_aw_ods_id)
    {
        try {
            if (empty($elderly_aw_ods_id)) {
                return null;
            }

            log_message('debug', "Searching elderly AW ODS for guest only: {$elderly_aw_ods_id}");

            // *** ค้นหาเฉพาะที่มี elderly_aw_ods_user_type = 'guest' ***
            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_ods');
            $this->db->where('elderly_aw_ods_id', $elderly_aw_ods_id);
            $this->db->where('elderly_aw_ods_user_type', 'guest'); // เงื่อนไขสำคัญ
            $query = $this->db->get();

            if ($query->num_rows() === 0) {
                log_message('info', "No guest elderly AW ODS found for ID: {$elderly_aw_ods_id}");
                return null;
            }

            $elderly_data = $query->row();

            // เพิ่มข้อมูลเพิ่มเติม (ถ้าจำเป็น)
            $elderly_data->files = [];
            $elderly_data->history = [];

            // ดึงไฟล์ที่เกี่ยวข้อง (ถ้ามีตาราง)
            if ($this->db->table_exists('tbl_elderly_aw_ods_files')) {
                $this->db->select('*');
                $this->db->from('tbl_elderly_aw_ods_files');
                $this->db->where('elderly_aw_ods_file_ref_id', $elderly_aw_ods_id);
                $this->db->order_by('elderly_aw_ods_file_uploaded_at', 'DESC');
                $files_query = $this->db->get();

                if ($files_query->num_rows() > 0) {
                    $elderly_data->files = $files_query->result();
                }
            }

            // ดึงประวัติที่เกี่ยวข้อง (ถ้ามีตาราง)
            if ($this->db->table_exists('tbl_elderly_aw_ods_history')) {
                $this->db->select('*');
                $this->db->from('tbl_elderly_aw_ods_history');
                $this->db->where('elderly_aw_ods_history_ref_id', $elderly_aw_ods_id);
                $this->db->order_by('action_date', 'DESC');
                $history_query = $this->db->get();

                if ($history_query->num_rows() > 0) {
                    $elderly_data->history = $history_query->result();
                }
            }

            log_message('info', "Successfully found guest elderly AW ODS: {$elderly_aw_ods_id} - Status: {$elderly_data->elderly_aw_ods_status}");

            return $elderly_data;

        } catch (Exception $e) {
            log_message('error', 'Error in get_elderly_aw_ods_for_guest_only: ' . $e->getMessage());
            return null;
        }
    }




    // *** ฟังก์ชันติดตามสถานะเบี้ยยังชีพสำหรับประชาชนทั่วไป ***
    public function track_status()
    {
        try {
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();
            $data['is_logged_in'] = $login_info['is_logged_in'];
            $data['user_info'] = $login_info['user_info'];
            $data['user_type'] = $login_info['user_type'];

            // ตรวจสอบ reference ID จาก URL
            $ref_id = $this->input->get('ref');
            $data['ref_id'] = $ref_id;

            $data['page_title'] = 'ติดตามสถานะเบี้ยยังชีพ';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'ติดตามสถานะเบี้ยยังชีพ', 'url' => '']
            ];

            // ถ้ามี ref_id ให้ค้นหาข้อมูล (เฉพาะ guest เท่านั้น)
            $elderly_aw_ods_info = null;
            if (!empty($ref_id)) {
                try {
                    // *** แก้ไข: ใช้ฟังก์ชันที่จำกัดเฉพาะ guest ***
                    $elderly_aw_ods_info = $this->get_elderly_aw_ods_for_guest_only($ref_id);

                    if ($elderly_aw_ods_info) {
                        $elderly_aw_ods_info = $this->prepare_elderly_aw_ods_for_display($elderly_aw_ods_info);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching elderly_aw_ods for guest in track_status: ' . $e->getMessage());
                }
            }

            $data['elderly_aw_ods_info'] = $elderly_aw_ods_info;
            $data['search_performed'] = !empty($ref_id);

            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/follow_elderly_aw_ods', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in track_status: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าติดตามสถานะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Elderly_aw_ods/adding_elderly_aw_ods');
            }
        }
    }

    /**
     * *** หน้าติดตามสถานะเบี้ยยังชีพสำหรับ Admin (ชื่อใหม่) ***
     */
    public function elderly_tracking_admin()
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
            $can_update_status = $this->check_elderly_update_permission($staff_check);
            $can_delete_elderly = $this->check_elderly_delete_permission($staff_check);

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_update_status'] = $can_update_status;
            $data['can_delete_elderly'] = $can_delete_elderly;
            $data['can_handle_elderly'] = true; // *** ทุกคนดูได้ ***
            $data['can_approve_elderly'] = $can_update_status; // *** ใช้สิทธิ์เดียวกับ update ***
            $data['staff_system_level'] = $staff_check->m_system;

            // *** สร้าง user_info object ที่ครบถ้วนสำหรับ header.php ***
            $user_info_object = $this->create_complete_user_info($staff_check);

            // ตรวจสอบ reference ID จาก URL parameter หรือ POST
            $ref_id = $this->input->get('ref') ?: $this->input->post('elderly_id');
            $search_phone = $this->input->get('phone') ?: $this->input->post('phone');
            $search_id_card = $this->input->get('id_card') ?: $this->input->post('id_card');

            $data['ref_id'] = $ref_id;
            $data['search_phone'] = $search_phone;
            $data['search_id_card'] = $search_id_card;

            // ค้นหาข้อมูลเบี้ยยังชีพ
            $elderly_aw_ods_results = [];
            $search_performed = false;

            if (!empty($ref_id)) {
                // ค้นหาด้วยหมายเลขอ้างอิง
                try {
                    $elderly_result = $this->elderly_aw_ods_model->get_elderly_aw_ods_detail_for_staff($ref_id);
                    if ($elderly_result) {
                        $elderly_aw_ods_results[] = $elderly_result;
                        $search_performed = true;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching elderly by ID: ' . $e->getMessage());
                }
            } elseif (!empty($search_phone)) {
                // ค้นหาด้วยเบอร์โทร
                try {
                    $elderly_list = $this->search_elderly_by_phone($search_phone);
                    if (!empty($elderly_list)) {
                        $elderly_aw_ods_results = $elderly_list;
                        $search_performed = true;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching elderly by phone: ' . $e->getMessage());
                }
            } elseif (!empty($search_id_card)) {
                // ค้นหาด้วยเลขบัตรประชาชน
                try {
                    $elderly_list = $this->search_elderly_by_id_card($search_id_card);
                    if (!empty($elderly_list)) {
                        $elderly_aw_ods_results = $elderly_list;
                        $search_performed = true;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error searching elderly by ID card: ' . $e->getMessage());
                }
            }

            // เตรียมข้อมูลผลลัพธ์สำหรับแสดงผล
            if (!empty($elderly_aw_ods_results)) {
                $data['elderly_aw_ods_results'] = $this->prepare_elderly_aw_ods_list_for_display($elderly_aw_ods_results);
            } else {
                $data['elderly_aw_ods_results'] = [];
            }

            $data['search_performed'] = $search_performed;
            $data['total_results'] = count($elderly_aw_ods_results);

            // ข้อมูลเจ้าหน้าที่
            $data['staff_info'] = [
                'id' => $staff_check->m_id,
                'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                'system' => $staff_check->m_system,
                'can_delete' => $data['can_delete_elderly'],
                'can_handle' => $data['can_handle_elderly'],
                'can_approve' => $data['can_approve_elderly'],
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
                ['title' => 'หน้าแรก', 'url' => site_url('Elderly_aw_ods/elderly_aw_ods')], // *** แก้ไข: ไม่ไป Dashboard ***
                ['title' => 'จัดการเบี้ยยังชีพ', 'url' => site_url('Elderly_aw_ods/elderly_aw_ods')],
                ['title' => 'ติดตามสถานะ (Admin)', 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'ติดตามสถานะเบี้ยยังชีพ (สำหรับเจ้าหน้าที่)';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Elderly tracking admin data: ' . json_encode([
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_update_status' => $can_update_status,
                    'can_delete_elderly' => $can_delete_elderly,
                    'search_performed' => $search_performed,
                    'total_results' => $data['total_results'],
                    'ref_id' => $ref_id,
                    'search_phone' => $search_phone,
                    'search_id_card' => $search_id_card
                ]));
            }

            // โหลด View (สำหรับ Admin)
            $this->load->view('reports/header', $data);
            $this->load->view('reports/elderly_aw_ods_tracking_admin', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in elderly_tracking_admin: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าติดตามสถานะ: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Elderly_aw_ods/elderly_aw_ods'); // *** แก้ไข: ไม่ไป Dashboard ***
            }
        }
    }

    /**
     * *** Helper: ค้นหาเบี้ยยังชีพด้วยเบอร์โทร ***
     */
    private function search_elderly_by_phone($phone)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_ods');
            $this->db->where('elderly_aw_ods_phone', $phone);
            $this->db->order_by('elderly_aw_ods_datesave', 'DESC');
            $query = $this->db->get();

            $results = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $elderly) {
                    // ดึงข้อมูลเพิ่มเติม (files, history)
                    $elderly->files = $this->elderly_aw_ods_model->get_elderly_aw_ods_files($elderly->elderly_aw_ods_id);
                    $elderly->history = $this->elderly_aw_ods_model->get_elderly_aw_ods_history($elderly->elderly_aw_ods_id);
                    $results[] = $elderly;
                }
            }

            return $results;

        } catch (Exception $e) {
            log_message('error', 'Error in search_elderly_by_phone: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * *** Helper: ค้นหาเบี้ยยังชีพด้วยเลขบัตรประชาชน ***
     */
    private function search_elderly_by_id_card($id_card)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_ods');
            $this->db->where('elderly_aw_ods_number', $id_card);
            $this->db->order_by('elderly_aw_ods_datesave', 'DESC');
            $query = $this->db->get();

            $results = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $elderly) {
                    // ดึงข้อมูลเพิ่มเติม (files, history)
                    $elderly->files = $this->elderly_aw_ods_model->get_elderly_aw_ods_files($elderly->elderly_aw_ods_id);
                    $elderly->history = $this->elderly_aw_ods_model->get_elderly_aw_ods_history($elderly->elderly_aw_ods_id);
                    $results[] = $elderly;
                }
            }

            return $results;

        } catch (Exception $e) {
            log_message('error', 'Error in search_elderly_by_id_card: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * *** แสดงรายละเอียดเบี้ยยังชีพ - สำหรับเจ้าหน้าที่ ***
     */
    public function elderly_detail($elderly_aw_ods_id = null)
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
            $can_update_status = $this->check_elderly_update_permission($staff_check);
            $can_delete_elderly = $this->check_elderly_delete_permission($staff_check);

            // ตรวจสอบ elderly_aw_ods_id
            if (empty($elderly_aw_ods_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขอ้างอิงเบี้ยยังชีพ');
                redirect('Elderly_aw_ods/elderly_aw_ods');
                return;
            }

            // ดึงข้อมูลเบี้ยยังชีพ
            $elderly_aw_ods_detail = $this->elderly_aw_ods_model->get_elderly_aw_ods_detail_for_staff($elderly_aw_ods_id);

            if (!$elderly_aw_ods_detail) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเบี้ยยังชีพที่ระบุ');
                redirect('Elderly_aw_ods/elderly_aw_ods');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_update_status'] = $can_update_status;
            $data['can_delete_elderly'] = $can_delete_elderly;
            $data['can_handle_elderly'] = true; // *** ทุกคนดูได้ ***
            $data['can_approve_elderly'] = $can_update_status; // *** ใช้สิทธิ์เดียวกับ update ***
            $data['staff_system_level'] = $staff_check->m_system;

            // *** สร้าง user_info object ที่ครบถ้วนสำหรับ header.php ***
            $user_info_object = $this->create_complete_user_info($staff_check);

            // ข้อมูลเบี้ยยังชีพ
            $data['elderly_aw_ods_detail'] = $elderly_aw_ods_detail;

            // ข้อมูลเจ้าหน้าที่
            $data['staff_info'] = [
                'id' => $staff_check->m_id,
                'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                'system' => $staff_check->m_system,
                'can_delete' => $data['can_delete_elderly'],
                'can_handle' => $data['can_handle_elderly'],
                'can_approve' => $data['can_approve_elderly'],
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
                ['title' => 'หน้าแรก', 'url' => site_url('Elderly_aw_ods/elderly_aw_ods')], // *** แก้ไข: ไม่ไป Dashboard ***
                ['title' => 'จัดการเบี้ยยังชีพ', 'url' => site_url('Elderly_aw_ods/elderly_aw_ods')],
                ['title' => 'รายละเอียด #' . $elderly_aw_ods_id, 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'รายละเอียดเบี้ยยังชีพ #' . $elderly_aw_ods_id;

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Elderly AW ODS detail data: ' . json_encode([
                    'elderly_id' => $elderly_aw_ods_id,
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_elderly'],
                    'can_handle' => $data['can_handle_elderly'],
                    'can_approve' => $data['can_approve_elderly'],
                    'can_update_status' => $data['can_update_status'],
                    'elderly_status' => $elderly_aw_ods_detail->elderly_aw_ods_status ?? 'unknown',
                    'elderly_type' => $elderly_aw_ods_detail->elderly_aw_ods_type ?? 'unknown',
                    'elderly_by' => $elderly_aw_ods_detail->elderly_aw_ods_by ?? 'unknown',
                    'files_count' => count($elderly_aw_ods_detail->files ?? []),
                    'history_count' => count($elderly_aw_ods_detail->history ?? [])
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/elderly_aw_ods_detail', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in elderly_detail: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ารายละเอียด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Elderly_aw_ods/elderly_aw_ods'); // *** แก้ไข: ไม่ไป Dashboard ***
            }
        }
    }

    /**
     * *** อัปเดตสถานะเบี้ยยังชีพ (AJAX) ***
     */
    public function update_elderly_status()
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
            $elderly_id = $this->input->post('elderly_id');
            $new_status = $this->input->post('new_status');
            $note = $this->input->post('note') ?: '';
            $new_priority = $this->input->post('new_priority') ?: 'normal';

            // Debug log สำหรับ development
            if (ENVIRONMENT === 'development') {
                log_message('info', 'Update elderly status request: ' . json_encode([
                    'elderly_id' => $elderly_id,
                    'new_status' => $new_status,
                    'note' => $note,
                    'new_priority' => $new_priority,
                    'staff_id' => $m_id
                ]));
            }

            // Validation
            if (empty($elderly_id) || empty($new_status)) {
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
            if (!$this->check_elderly_handle_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์อัปเดตสถานะ']);
                return;
            }

            // *** ดึงข้อมูลเบี้ยยังชีพเก่าก่อนอัปเดต ***
            $elderly_data = $this->elderly_aw_ods_model->get_elderly_aw_ods_by_id($elderly_id);
            if (!$elderly_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเบี้ยยังชีพ']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);
            $old_status = $elderly_data->elderly_aw_ods_status;

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
            $update_result = $this->elderly_aw_ods_model->update_elderly_aw_ods_status(
                $elderly_id,
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
                $this->db->where('elderly_aw_ods_id', $elderly_id);
                $this->db->update('tbl_elderly_aw_ods', [
                    'elderly_aw_ods_priority' => $new_priority
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
                $this->create_elderly_aw_ods_update_notifications(
                    $elderly_id,
                    $elderly_data,
                    $old_status,
                    $new_status,
                    $updated_by,
                    $staff_data,
                    $note
                );
                log_message('info', "Update notifications sent for elderly_aw_ods {$elderly_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create update notifications: ' . $e->getMessage());
                // ไม่ให้ notification error ทำให้การอัปเดตล้มเหลว
            }

            ob_end_clean();

            log_message('info', "Elderly status updated successfully: {$elderly_id} from {$old_status} to {$new_status} by {$updated_by}");

            // *** ส่ง JSON Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'อัปเดตสถานะสำเร็จ',
                'new_status' => $new_status,
                'old_status' => $old_status,
                'updated_by' => $updated_by,
                'elderly_id' => $elderly_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in update_elderly_status: ' . $e->getMessage());
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
     * *** ใหม่: สร้างการแจ้งเตือนสำหรับการอัปเดตสถานะเบี้ยยังชีพ ***
     */
    private function create_elderly_aw_ods_update_notifications($elderly_id, $elderly_data, $old_status, $new_status, $updated_by, $staff_data, $note = '')
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('debug', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($elderly_data->elderly_aw_ods_type === 'disabled') ? 'ผู้พิการ' : 'ผู้สูงอายุ';

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
                'elderly_aw_ods_id' => $elderly_id,
                'type' => $elderly_data->elderly_aw_ods_type,
                'type_display' => $type_display,
                'requester' => $elderly_data->elderly_aw_ods_by,
                'phone' => $elderly_data->elderly_aw_ods_phone,
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
                'type' => 'elderly_aw_ods_update',
                'title' => 'อัปเดตเบี้ยยังชีพ',
                'message' => "เบี้ยยังชีพ{$type_display} #{$elderly_id} {$update_message} โดย {$updated_by}",
                'reference_id' => 0,
                'reference_table' => 'tbl_elderly_aw_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-edit',
                'url' => site_url("Elderly_aw_ods/elderly_detail/{$elderly_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff update notification created for elderly_aw_ods: {$elderly_id}");
            }

            // 2. แจ้งเตือนเจ้าของเรื่อง (ถ้าเป็น public user)
            if (
                $elderly_data->elderly_aw_ods_user_type === 'public' &&
                !empty($elderly_data->elderly_aw_ods_user_id)
            ) {

                // สร้างข้อความสำหรับผู้ใช้
                $user_message = '';
                switch ($new_status) {
                    case 'reviewing':
                        $user_message = "เบี้ยยังชีพ{$type_display}ของคุณอยู่ระหว่างการพิจารณา";
                        break;
                    case 'approved':
                        $user_message = "เบี้ยยังชีพ{$type_display}ของคุณได้รับการอนุมัติแล้ว";
                        break;
                    case 'rejected':
                        $user_message = "เบี้ยยังชีพ{$type_display}ของคุณไม่ได้รับการอนุมัติ";
                        break;
                    case 'completed':
                        $user_message = "เบี้ยยังชีพ{$type_display}ของคุณดำเนินการเสร็จสิ้นแล้ว";
                        break;
                    default:
                        $user_message = "สถานะเบี้ยยังชีพ{$type_display}ของคุณมีการเปลี่ยนแปลง";
                }

                $user_data_json = json_encode([
                    'elderly_aw_ods_id' => $elderly_id,
                    'type' => $elderly_data->elderly_aw_ods_type,
                    'type_display' => $type_display,
                    'new_status' => $new_status,
                    'new_status_display' => $new_status_display,
                    'updated_by' => $updated_by,
                    'timestamp' => $current_time,
                    'notification_type' => 'user_update_notification'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification = [
                    'type' => 'elderly_aw_ods_update',
                    'title' => 'อัปเดตเบี้ยยังชีพของคุณ',
                    'message' => $user_message . " หมายเลขอ้างอิง: {$elderly_id}",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_elderly_aw_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($elderly_data->elderly_aw_ods_user_id),
                    'priority' => 'high',
                    'icon' => $this->get_status_icon($new_status),
                    'url' => site_url("Elderly_aw_ods/my_elderly_aw_ods_detail/{$elderly_id}"),
                    'data' => $user_data_json,
                    'created_at' => $current_time,
                    'created_by' => $staff_data->m_id,
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $user_result = $this->db->insert('tbl_notifications', $user_notification);

                if ($user_result) {
                    log_message('info', "User update notification created for elderly_aw_ods: {$elderly_id}");
                }
            }

            log_message('info', "Elderly AW ODS update notifications created: {$elderly_id}");

        } catch (Exception $e) {
            log_message('error', 'Error creating elderly_aw_ods update notifications: ' . $e->getMessage());
            throw $e;
        }
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
     * *** ลบข้อมูลเบี้ยยังชีพ (AJAX) ***
     */
    public function delete_elderly()
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
            $elderly_id = $this->input->post('elderly_id');
            $delete_reason = $this->input->post('delete_reason') ?: '';

            if (empty($elderly_id)) {
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

            // *** ดึงข้อมูลเบี้ยยังชีพก่อนลบ ***
            $elderly_data = $this->elderly_aw_ods_model->get_elderly_aw_ods_by_id($elderly_id);
            if (!$elderly_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเบี้ยยังชีพ']);
                return;
            }

            $deleted_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เริ่ม Transaction
            $this->db->trans_start();

            // *** สร้างการแจ้งเตือนก่อนลบ (ไม่บังคับ) ***
            try {
                $this->create_elderly_aw_ods_delete_notifications(
                    $elderly_id,
                    $elderly_data,
                    $deleted_by,
                    $staff_data,
                    $delete_reason
                );
                log_message('info', "Delete notifications created for elderly_aw_ods {$elderly_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create delete notifications: ' . $e->getMessage());
            }

            // บันทึกประวัติก่อนลบ
            if (!empty($delete_reason) && method_exists($this->elderly_aw_ods_model, 'add_elderly_aw_ods_history')) {
                $this->elderly_aw_ods_model->add_elderly_aw_ods_history(
                    $elderly_id,
                    'deleted',
                    'ลบข้อมูล - เหตุผล: ' . $delete_reason,
                    $deleted_by,
                    $elderly_data->elderly_aw_ods_status,
                    'deleted'
                );
            }

            // ลบข้อมูล
            $delete_result = $this->elderly_aw_ods_model->delete_elderly_aw_ods($elderly_id, $deleted_by);

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

            log_message('info', "Elderly deleted successfully: {$elderly_id} by {$deleted_by}");

            // *** ส่ง JSON Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'ลบข้อมูลสำเร็จ',
                'deleted_by' => $deleted_by,
                'elderly_id' => $elderly_id,
                'delete_reason' => $delete_reason,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in delete_elderly: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'DELETE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }
    /**
     * *** ใหม่: สร้างการแจ้งเตือนสำหรับการลบเบี้ยยังชีพ ***
     */
    private function create_elderly_aw_ods_delete_notifications($elderly_id, $elderly_data, $deleted_by, $staff_data, $delete_reason = '')
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('debug', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($elderly_data->elderly_aw_ods_type === 'disabled') ? 'ผู้พิการ' : 'ผู้สูงอายุ';

            // สร้างข้อความการลบ
            $delete_message = "เบี้ยยังชีพ{$type_display} #{$elderly_id} ถูกลบโดย {$deleted_by}";
            if (!empty($elderly_data->elderly_aw_ods_by)) {
                $delete_message .= " (ผู้ยื่น: {$elderly_data->elderly_aw_ods_by})";
            }
            if (!empty($delete_reason)) {
                $delete_message .= " เหตุผล: {$delete_reason}";
            }

            // สร้างข้อมูล JSON
            $notification_data_json = json_encode([
                'elderly_aw_ods_id' => $elderly_id,
                'type' => $elderly_data->elderly_aw_ods_type,
                'type_display' => $type_display,
                'deleted_by' => $deleted_by,
                'deleted_by_system' => $staff_data->m_system,
                'delete_reason' => $delete_reason ?: 'ไม่ระบุเหตุผล',
                'original_requester' => $elderly_data->elderly_aw_ods_by,
                'original_phone' => $elderly_data->elderly_aw_ods_phone,
                'original_status' => $elderly_data->elderly_aw_ods_status,
                'timestamp' => $current_time,
                'notification_type' => 'elderly_deleted'
            ], JSON_UNESCAPED_UNICODE);

            // แจ้งเตือน Staff ทั้งหมด
            $staff_notification = [
                'type' => 'elderly_aw_ods_deleted',
                'title' => 'เบี้ยยังชีพถูกลบ',
                'message' => $delete_message,
                'reference_id' => 0, // เนื่องจากถูกลบแล้ว
                'reference_table' => 'tbl_elderly_aw_ods',
                'target_role' => 'staff',
                'priority' => 'high',
                'icon' => 'fas fa-trash',
                'url' => site_url("Elderly_aw_ods/elderly_aw_ods"),
                'data' => $notification_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $notification_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($notification_result) {
                log_message('info', "Delete notification created successfully for elderly_aw_ods: {$elderly_id}");
            } else {
                $db_error = $this->db->error();
                log_message('error', "Failed to create delete notification for elderly_aw_ods: {$elderly_id}. DB Error: " . print_r($db_error, true));
            }

            // บันทึก Log การลบ
            log_message('info', "Elderly AW ODS deleted successfully by {$deleted_by} ({$staff_data->m_system}): ID={$elderly_id}, Type={$elderly_data->elderly_aw_ods_type}, Requester=" . ($elderly_data->elderly_aw_ods_by ?? 'N/A') . ", Reason=" . ($delete_reason ?: 'ไม่ระบุเหตุผล'));

        } catch (Exception $e) {
            log_message('error', 'Error creating elderly_aw_ods delete notifications: ' . $e->getMessage());
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
            $elderly_id = $this->input->post('elderly_id');
            $note = $this->input->post('note');

            if (empty($elderly_id) || empty($note)) {
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
            if (!$this->check_elderly_handle_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เพิ่มหมายเหตุ']);
                return;
            }

            // *** ดึงข้อมูลเบี้ยยังชีพ ***
            $elderly_data = $this->elderly_aw_ods_model->get_elderly_aw_ods_by_id($elderly_id);
            if (!$elderly_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเบี้ยยังชีพ']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // ดึงหมายเหตุเดิม
            $this->db->select('elderly_aw_ods_notes');
            $this->db->from('tbl_elderly_aw_ods');
            $this->db->where('elderly_aw_ods_id', $elderly_id);
            $existing_data = $this->db->get()->row();

            $old_notes = $existing_data->elderly_aw_ods_notes ?? '';
            $new_notes = $old_notes;

            if (!empty($old_notes)) {
                $new_notes .= "\n\n" . "--- " . date('d/m/Y H:i') . " โดย {$updated_by} ---\n" . $note;
            } else {
                $new_notes = "--- " . date('d/m/Y H:i') . " โดย {$updated_by} ---\n" . $note;
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตหมายเหตุ
            $this->db->where('elderly_aw_ods_id', $elderly_id);
            $update_result = $this->db->update('tbl_elderly_aw_ods', [
                'elderly_aw_ods_notes' => $new_notes,
                'elderly_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'elderly_aw_ods_updated_by' => $updated_by
            ]);

            if (!$update_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถเพิ่มหมายเหตุได้']);
                return;
            }

            // บันทึกประวัติ
            if (method_exists($this->elderly_aw_ods_model, 'add_elderly_aw_ods_history')) {
                $this->elderly_aw_ods_model->add_elderly_aw_ods_history(
                    $elderly_id,
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
                $this->create_elderly_aw_ods_note_notifications(
                    $elderly_id,
                    $elderly_data,
                    $note,
                    $updated_by,
                    $staff_data
                );
                log_message('info', "Note notifications sent for elderly_aw_ods {$elderly_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create note notifications: ' . $e->getMessage());
                // ไม่ให้ notification error ทำให้การเพิ่มหมายเหตุล้มเหลว
            }

            ob_end_clean();

            log_message('info', "Note added successfully to elderly: {$elderly_id} by {$updated_by}");

            // *** ส่ง JSON Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'เพิ่มหมายเหตุสำเร็จ',
                'updated_by' => $updated_by,
                'elderly_id' => $elderly_id,
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
    private function create_elderly_aw_ods_note_notifications($elderly_id, $elderly_data, $note, $updated_by, $staff_data)
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('debug', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($elderly_data->elderly_aw_ods_type === 'disabled') ? 'ผู้พิการ' : 'ผู้สูงอายุ';

            // ตัดข้อความหมายเหตุให้สั้น
            $short_note = mb_substr($note, 0, 100);
            if (mb_strlen($note) > 100) {
                $short_note .= '...';
            }

            // 1. แจ้งเตือน Staff ทั้งหมด
            $staff_data_json = json_encode([
                'elderly_aw_ods_id' => $elderly_id,
                'type' => $elderly_data->elderly_aw_ods_type,
                'type_display' => $type_display,
                'requester' => $elderly_data->elderly_aw_ods_by,
                'phone' => $elderly_data->elderly_aw_ods_phone,
                'note' => $note,
                'note_preview' => $short_note,
                'updated_by' => $updated_by,
                'timestamp' => $current_time,
                'notification_type' => 'staff_note_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'elderly_aw_ods_note',
                'title' => 'หมายเหตุใหม่ - เบี้ยยังชีพ',
                'message' => "มีหมายเหตุใหม่ในเบี้ยยังชีพ{$type_display} #{$elderly_id}: \"{$short_note}\" โดย {$updated_by}",
                'reference_id' => 0,
                'reference_table' => 'tbl_elderly_aw_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-sticky-note',
                'url' => site_url("Elderly_aw_ods/elderly_detail/{$elderly_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff note notification created for elderly_aw_ods: {$elderly_id}");
            }

            // 2. แจ้งเตือนเจ้าของเรื่อง (ถ้าเป็น public user) - เฉพาะหมายเหตุที่สำคัญ
            if (
                $elderly_data->elderly_aw_ods_user_type === 'public' &&
                !empty($elderly_data->elderly_aw_ods_user_id) &&
                (stripos($note, 'อนุมัติ') !== false ||
                    stripos($note, 'ไม่อนุมัติ') !== false ||
                    stripos($note, 'เสร็จสิ้น') !== false ||
                    stripos($note, 'ติดต่อ') !== false)
            ) {

                $user_data_json = json_encode([
                    'elderly_aw_ods_id' => $elderly_id,
                    'type' => $elderly_data->elderly_aw_ods_type,
                    'type_display' => $type_display,
                    'note_preview' => $short_note,
                    'updated_by' => $updated_by,
                    'timestamp' => $current_time,
                    'notification_type' => 'user_note_notification'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification = [
                    'type' => 'elderly_aw_ods_note',
                    'title' => 'ข้อมูลเพิ่มเติมเบี้ยยังชีพ',
                    'message' => "มีข้อมูลเพิ่มเติมเกี่ยวกับเบี้ยยังชีพ{$type_display} หมายเลขอ้างอิง: {$elderly_id}",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_elderly_aw_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($elderly_data->elderly_aw_ods_user_id),
                    'priority' => 'high',
                    'icon' => 'fas fa-info-circle',
                    'url' => site_url("Elderly_aw_ods/my_elderly_aw_ods_detail/{$elderly_id}"),
                    'data' => $user_data_json,
                    'created_at' => $current_time,
                    'created_by' => $staff_data->m_id,
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $user_result = $this->db->insert('tbl_notifications', $user_notification);

                if ($user_result) {
                    log_message('info', "User note notification created for elderly_aw_ods: {$elderly_id}");
                }
            }

            log_message('info', "Elderly AW ODS note notifications created: {$elderly_id}");

        } catch (Exception $e) {
            log_message('error', 'Error creating elderly_aw_ods note notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * *** ตรวจสอบการอัปเดตสถานะ (AJAX) ***
     */
    public function check_status($elderly_aw_ods_id)
    {
        try {
            if (empty($elderly_aw_ods_id)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'updated' => false]);
                return;
            }

            // ดึงข้อมูลปัจจุบัน
            $this->db->select('elderly_aw_ods_updated_at');
            $this->db->from('tbl_elderly_aw_ods');
            $this->db->where('elderly_aw_ods_id', $elderly_aw_ods_id);
            $current_data = $this->db->get()->row();

            if (!$current_data) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'updated' => false]);
                return;
            }

            // เปรียบเทียบกับเวลาล่าสุดที่ส่งมา
            $last_check = $this->input->get('last_check');
            $updated = false;

            if ($last_check && $current_data->elderly_aw_ods_updated_at) {
                $updated = (strtotime($current_data->elderly_aw_ods_updated_at) > strtotime($last_check));
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'updated' => $updated,
                'last_updated' => $current_data->elderly_aw_ods_updated_at
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in check_status: ' . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'updated' => false]);
        }
    }



    /**
     * *** ส่งออกข้อมูลเบี้ยยังชีพเป็น Excel ***
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

            // เช็คสิทธิ์การจัดการเบี้ยยังชีพ
            $can_handle_elderly = $this->check_elderly_handle_permission($staff_check);

            if (!$can_handle_elderly) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์ส่งออกข้อมูลเบี้ยยังชีพ');
                redirect('Elderly_aw_ods/elderly_aw_ods');
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
            $elderly_result = $this->elderly_aw_ods_model->get_elderly_aw_ods_with_filters($filters, 999999, 0);
            $elderly_data = $elderly_result['data'] ?? [];

            if (empty($elderly_data)) {
                $this->session->set_flashdata('warning_message', 'ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา');
                redirect('Elderly_aw_ods/elderly_aw_ods?' . http_build_query($filters));
                return;
            }

            // เตรียมข้อมูลสำหรับ Excel
            $excel_data = $this->prepare_excel_data($elderly_data);

            // สร้างชื่อไฟล์
            $filename = 'รายงานเบี้ยยังชีพ_' . date('Y-m-d_H-i-s') . '.xlsx';

            // ส่งออกเป็น Excel
            $this->generate_excel_file($excel_data, $filename, $filters, $staff_check);

            // บันทึก Log การส่งออก
            log_message('info', "Excel export completed by {$staff_check->m_fname} {$staff_check->m_lname} - {$filename} - " . count($elderly_data) . " records");

        } catch (Exception $e) {
            log_message('error', 'Error in export_excel: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการส่งออกไฟล์: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการส่งออกไฟล์ กรุณาลองใหม่อีกครั้ง');
                redirect('Elderly_aw_ods/elderly_aw_ods');
            }
        }
    }

    /**
     * *** เตรียมข้อมูลสำหรับ Excel ***
     */
    private function prepare_excel_data($elderly_data)
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
        foreach ($elderly_data as $elderly) {
            // แปลงค่าต่างๆ ให้เป็นภาษาไทย
            $type_display = '';
            switch ($elderly->elderly_aw_ods_type ?? 'elderly') {
                case 'elderly':
                    $type_display = 'ผู้สูงอายุ';
                    break;
                case 'disabled':
                    $type_display = 'ผู้พิการ';
                    break;
                default:
                    $type_display = 'ผู้สูงอายุ';
                    break;
            }

            $status_display = '';
            switch ($elderly->elderly_aw_ods_status ?? 'submitted') {
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
            switch ($elderly->elderly_aw_ods_priority ?? 'normal') {
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
            switch ($elderly->elderly_aw_ods_user_type ?? 'guest') {
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
            if (!empty($elderly->elderly_aw_ods_datesave)) {
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

                $date = date('j', strtotime($elderly->elderly_aw_ods_datesave));
                $month = $thai_months[date('m', strtotime($elderly->elderly_aw_ods_datesave))];
                $year = date('Y', strtotime($elderly->elderly_aw_ods_datesave)) + 543;
                $time = date('H:i', strtotime($elderly->elderly_aw_ods_datesave));

                $date_save = $date . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';
            }

            $date_updated = '';
            if (!empty($elderly->elderly_aw_ods_updated_at)) {
                $date = date('j', strtotime($elderly->elderly_aw_ods_updated_at));
                $month = $thai_months[date('m', strtotime($elderly->elderly_aw_ods_updated_at))];
                $year = date('Y', strtotime($elderly->elderly_aw_ods_updated_at)) + 543;
                $time = date('H:i', strtotime($elderly->elderly_aw_ods_updated_at));

                $date_updated = $date . ' ' . $month . ' ' . $year . ' เวลา ' . $time . ' น.';
            }

            // ดึงชื่อเจ้าหน้าที่ผู้รับผิดชอบ
            $assigned_staff_name = '';
            if (!empty($elderly->elderly_aw_ods_assigned_to)) {
                $this->db->select('m_fname, m_lname');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $elderly->elderly_aw_ods_assigned_to);
                $assigned_staff = $this->db->get()->row();

                if ($assigned_staff) {
                    $assigned_staff_name = trim($assigned_staff->m_fname . ' ' . $assigned_staff->m_lname);
                }
            }

            // ซ่อนเลขบัตรประชาชน
            $id_card_display = '';
            if (!empty($elderly->elderly_aw_ods_number)) {
                $id_card_display = substr($elderly->elderly_aw_ods_number, 0, 3) . '-****-****-**-' . substr($elderly->elderly_aw_ods_number, -2);
            }

            $excel_data[] = [
                $row_number,
                $elderly->elderly_aw_ods_id ?? '',
                $type_display,
                $elderly->elderly_aw_ods_by ?? '',
                $elderly->elderly_aw_ods_phone ?? '',
                $elderly->elderly_aw_ods_email ?? '',
                $id_card_display,
                $elderly->elderly_aw_ods_address ?? '',
                $elderly->guest_province ?? '',
                $elderly->guest_amphoe ?? '',
                $elderly->guest_district ?? '',
                $elderly->guest_zipcode ?? '',
                $status_display,
                $priority_display,
                $user_type_display,
                $assigned_staff_name,
                $date_save,
                $date_updated,
                $elderly->elderly_aw_ods_notes ?? ''
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
            $sheet->setTitle('รายงานเบี้ยยังชีพ');

            // เพิ่มข้อมูลหัวรายงาน
            $sheet->setCellValue('A1', 'รายงานข้อมูลเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ');
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
                'elderly' => 'ผู้สูงอายุ',
                'disabled' => 'ผู้พิการ'
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
     * *** เพิ่มใน Elderly_aw_ods Controller - Updated ตามฐานข้อมูลจริง ***
     */

    /**
     * ดึงข้อมูลเบี้ยยังชีพสำหรับแก้ไข (AJAX)
     */
    public function get_elderly_data()
    {
        // *** ป้องกัน output ใดๆ ก่อนส่ง JSON ***
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in'] || !$current_user['user_info']) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            $elderly_id = $this->input->post('elderly_id');

            if (empty($elderly_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบหมายเลขอ้างอิง']);
                return;
            }

            $elderly_data = $this->get_elderly_by_user($elderly_id, $current_user);

            if (!$elderly_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            $editable_statuses = ['submitted', 'reviewing'];
            if (!in_array($elderly_data->elderly_aw_ods_status, $editable_statuses)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถแก้ไขได้ในสถานะปัจจุบัน']);
                return;
            }

            $files = $this->get_elderly_files($elderly_id);

            $response_data = [
                'elderly_aw_ods_id' => $elderly_data->elderly_aw_ods_id,
                'elderly_aw_ods_type' => $elderly_data->elderly_aw_ods_type,
                'elderly_aw_ods_by' => $elderly_data->elderly_aw_ods_by,
                'elderly_aw_ods_phone' => $elderly_data->elderly_aw_ods_phone,
                'elderly_aw_ods_email' => $elderly_data->elderly_aw_ods_email ?? '',
                'elderly_aw_ods_number' => $elderly_data->elderly_aw_ods_number,
                'elderly_aw_ods_address' => $elderly_data->elderly_aw_ods_address,
                'elderly_aw_ods_status' => $elderly_data->elderly_aw_ods_status,
                'elderly_aw_ods_datesave' => $elderly_data->elderly_aw_ods_datesave,
                'guest_province' => $elderly_data->guest_province ?? '',
                'guest_amphoe' => $elderly_data->guest_amphoe ?? '',
                'guest_district' => $elderly_data->guest_district ?? '',
                'guest_zipcode' => $elderly_data->guest_zipcode ?? '',
                'files' => $files
            ];

            ob_end_clean();

            $this->json_response([
                'success' => true,
                'data' => $response_data
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in get_elderly_data: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'GET_DATA_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }


    /**
     * อัปเดตข้อมูลเบี้ยยังชีพ (AJAX)
     */
    public function update_elderly_data()
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

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in'] || !$current_user['user_info']) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            // รับข้อมูลที่ส่งมา
            $elderly_id = $this->input->post('elderly_id');
            $elderly_phone = $this->input->post('elderly_phone');
            $elderly_email = $this->input->post('elderly_email');
            $elderly_address = $this->input->post('elderly_address');

            // Validation
            if (empty($elderly_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบหมายเลขอ้างอิง']);
                return;
            }

            if (empty($elderly_phone) || !preg_match('/^[0-9]{10}$/', $elderly_phone)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (10 หลัก)']);
                return;
            }

            if (!empty($elderly_email) && !filter_var($elderly_email, FILTER_VALIDATE_EMAIL)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'รูปแบบอีเมลไม่ถูกต้อง']);
                return;
            }

            // ตรวจสอบสิทธิ์และสถานะ
            $elderly_data = $this->get_elderly_by_user($elderly_id, $current_user);

            if (!$elderly_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            $editable_statuses = ['submitted', 'reviewing'];
            if (!in_array($elderly_data->elderly_aw_ods_status, $editable_statuses)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถแก้ไขได้ในสถานะปัจจุบัน']);
                return;
            }

            // *** จัดการไฟล์อัปโหลด ***
            $uploaded_files = [];
            $files_uploaded_count = 0;

            if (!empty($_FILES['elderly_additional_files']['name'][0])) {
                $uploaded_files = $this->handle_elderly_file_uploads_safe($elderly_id);
                $files_uploaded_count = count($uploaded_files);
            }

            // เตรียมข้อมูลสำหรับอัปเดต
            $update_data = [
                'elderly_aw_ods_phone' => $elderly_phone,
                'elderly_aw_ods_email' => $elderly_email,
                'elderly_aw_ods_address' => $elderly_address,
                'elderly_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'elderly_aw_ods_updated_by' => $current_user['user_info']['name']
            ];

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตข้อมูลหลัก
            $this->db->where('elderly_aw_ods_id', $elderly_id);
            $update_result = $this->db->update('tbl_elderly_aw_ods', $update_data);

            if (!$update_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
                return;
            }

            // บันทึกไฟล์ใหม่
            $successful_file_inserts = 0;
            if (!empty($uploaded_files) && $this->db->table_exists('tbl_elderly_aw_ods_files')) {
                foreach ($uploaded_files as $file) {
                    $file_data = [
                        'elderly_aw_ods_file_ref_id' => $elderly_id,
                        'elderly_aw_ods_file_name' => $file['file_name'],
                        'elderly_aw_ods_file_original_name' => $file['original_name'],
                        'elderly_aw_ods_file_type' => $file['file_type'],
                        'elderly_aw_ods_file_size' => $file['file_size'],
                        'elderly_aw_ods_file_path' => $file['file_path'],
                        'elderly_aw_ods_file_category' => 'other',
                        'elderly_aw_ods_file_uploaded_at' => date('Y-m-d H:i:s'),
                        'elderly_aw_ods_file_uploaded_by' => $current_user['user_info']['name'],
                        'elderly_aw_ods_file_status' => 'active'
                    ];

                    $insert_result = $this->db->insert('tbl_elderly_aw_ods_files', $file_data);
                    if ($insert_result) {
                        $successful_file_inserts++;
                    }
                }
            }

            // บันทึกประวัติ
            if ($this->db->table_exists('tbl_elderly_aw_ods_history')) {
                $history_description = 'แก้ไขข้อมูล';
                if ($successful_file_inserts > 0) {
                    $history_description .= ' และเพิ่มเอกสาร ' . $successful_file_inserts . ' ไฟล์';
                }

                $history_data = [
                    'elderly_aw_ods_history_ref_id' => $elderly_id,
                    'action_type' => 'updated',
                    'action_description' => $history_description,
                    'action_by' => $current_user['user_info']['name'],
                    'action_date' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_elderly_aw_ods_history', $history_data);
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
                return;
            }

            // *** เพิ่ม: สร้างการแจ้งเตือนสำหรับการแก้ไขข้อมูล ***
            try {
                $this->create_elderly_aw_ods_update_data_notifications(
                    $elderly_id,
                    $elderly_data,
                    $current_user,
                    $successful_file_inserts,
                    [
                        'phone_changed' => ($elderly_data->elderly_aw_ods_phone !== $elderly_phone),
                        'email_changed' => ($elderly_data->elderly_aw_ods_email !== $elderly_email),
                        'address_changed' => ($elderly_data->elderly_aw_ods_address !== $elderly_address),
                        'old_phone' => $elderly_data->elderly_aw_ods_phone,
                        'new_phone' => $elderly_phone,
                        'old_email' => $elderly_data->elderly_aw_ods_email,
                        'new_email' => $elderly_email,
                        'old_address' => $elderly_data->elderly_aw_ods_address,
                        'new_address' => $elderly_address
                    ]
                );
                log_message('info', "Update data notifications sent for elderly_aw_ods {$elderly_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create update data notifications: ' . $e->getMessage());
                // ไม่ให้ notification error ทำให้การอัปเดตล้มเหลว
            }

            ob_end_clean();

            log_message('info', "Elderly data updated successfully: {$elderly_id} by {$current_user['user_info']['name']}");

            // *** ส่ง JSON Response สำเร็จ ***
            $this->json_response([
                'success' => true,
                'message' => 'แก้ไขข้อมูลสำเร็จ' . ($successful_file_inserts > 0 ? ' และเพิ่มเอกสาร ' . $successful_file_inserts . ' ไฟล์' : ''),
                'files_uploaded' => $successful_file_inserts,
                'elderly_id' => $elderly_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in update_elderly_data: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'UPDATE_DATA_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }


    private function create_elderly_aw_ods_update_data_notifications($elderly_id, $elderly_data, $current_user, $files_count = 0, $changes = [])
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('debug', 'Notifications table does not exist');
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $type_display = ($elderly_data->elderly_aw_ods_type === 'disabled') ? 'ผู้พิการ' : 'ผู้สูงอายุ';
            $user_name = $current_user['user_info']['name'] ?? 'ผู้ใช้';
            $user_type = $current_user['user_type'] ?? 'guest';
            $user_id = intval($current_user['user_info']['id'] ?? 0);

            // สร้างรายละเอียดการเปลี่ยนแปลง
            $change_details = [];
            if (!empty($changes['phone_changed'])) {
                $change_details[] = "เบอร์โทร: {$changes['old_phone']} → {$changes['new_phone']}";
            }
            if (!empty($changes['email_changed'])) {
                $old_email = $changes['old_email'] ?: '(ไม่ระบุ)';
                $new_email = $changes['new_email'] ?: '(ไม่ระบุ)';
                $change_details[] = "อีเมล: {$old_email} → {$new_email}";
            }
            if (!empty($changes['address_changed'])) {
                $old_addr = mb_substr($changes['old_address'] ?: '', 0, 30);
                $new_addr = mb_substr($changes['new_address'] ?: '', 0, 30);
                if (mb_strlen($changes['old_address'] ?: '') > 30)
                    $old_addr .= '...';
                if (mb_strlen($changes['new_address'] ?: '') > 30)
                    $new_addr .= '...';
                $change_details[] = "ที่อยู่: {$old_addr} → {$new_addr}";
            }

            $change_summary = !empty($change_details) ? implode(', ', $change_details) : 'แก้ไขข้อมูลทั่วไป';

            // ตรวจสอบว่ามีการเปลี่ยนแปลงจริงๆ หรือไม่
            $has_data_changes = !empty($changes['phone_changed']) || !empty($changes['email_changed']) || !empty($changes['address_changed']);
            $has_file_changes = $files_count > 0;

            // ถ้าไม่มีการเปลี่ยนแปลงใดๆ ไม่ต้องส่งการแจ้งเตือน
            if (!$has_data_changes && !$has_file_changes) {
                log_message('info', "No changes detected for elderly_aw_ods {$elderly_id}, skipping notifications");
                return;
            }

            // สร้างข้อความสำหรับการแจ้งเตือน
            $update_message = "แก้ไขข้อมูลเบี้ยยังชีพ{$type_display} #{$elderly_id}";
            if ($has_file_changes) {
                $update_message .= " และเพิ่มเอกสาร {$files_count} ไฟล์";
            }
            $update_message .= " โดย {$user_name}";

            // *** 1. แจ้งเตือน Staff ทั้งหมด (ยกเว้นคนที่แก้ไขเอง ถ้าเป็น Staff) ***
            $staff_data_json = json_encode([
                'elderly_aw_ods_id' => $elderly_id,
                'type' => $elderly_data->elderly_aw_ods_type,
                'type_display' => $type_display,
                'requester' => $elderly_data->elderly_aw_ods_by,
                'phone' => $elderly_data->elderly_aw_ods_phone,
                'email' => $elderly_data->elderly_aw_ods_email,
                'updated_by' => $user_name,
                'updated_by_type' => $user_type,
                'updated_by_id' => $user_id,
                'changes' => $changes,
                'change_summary' => $change_summary,
                'files_added' => $files_count,
                'has_data_changes' => $has_data_changes,
                'has_file_changes' => $has_file_changes,
                'timestamp' => $current_time,
                'notification_type' => 'staff_data_update_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'elderly_aw_ods_data_update',
                'title' => 'แก้ไขข้อมูลเบี้ยยังชีพ',
                'message' => $update_message,
                'reference_id' => 0,
                'reference_table' => 'tbl_elderly_aw_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-edit',
                'url' => site_url("Elderly_aw_ods/elderly_detail/{$elderly_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => $user_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            // *** เพิ่มเงื่อนไข: ถ้าผู้แก้ไขเป็น Staff ให้ระบุไม่ส่งให้ตัวเอง ***
            if ($user_type === 'staff' && $user_id > 0) {
                $staff_notification['exclude_user_id'] = $user_id; // ไม่ส่งให้ตัวเอง
            }

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff data update notification created for elderly_aw_ods: {$elderly_id} (excluded user: {$user_id})");
            }

            // *** 2. แจ้งเตือนยืนยันสำหรับผู้แก้ไขเท่านั้น (ไม่ว่าจะเป็น Public หรือ Staff) ***
            if ($user_id > 0) {
                $user_message = '';
                $user_title = '';
                $user_target_role = '';

                if ($user_type === 'public') {
                    $user_title = 'แก้ไขข้อมูลสำเร็จ';
                    $user_message = "คุณได้แก้ไขข้อมูลเบี้ยยังชีพ{$type_display} หมายเลขอ้างอิง: {$elderly_id} เรียบร้อยแล้ว";
                    $user_target_role = 'public';
                } elseif ($user_type === 'staff') {
                    $user_title = 'ดำเนินการแก้ไขสำเร็จ';
                    $user_message = "คุณได้แก้ไขข้อมูลเบี้ยยังชีพ{$type_display} หมายเลขอ้างอิง: {$elderly_id} สำเร็จ";
                    $user_target_role = 'staff';
                }

                if ($has_file_changes) {
                    $user_message .= " และเพิ่มเอกสารแนบ {$files_count} ไฟล์";
                }

                if (!empty($user_message)) {
                    $user_data_json = json_encode([
                        'elderly_aw_ods_id' => $elderly_id,
                        'type' => $elderly_data->elderly_aw_ods_type,
                        'type_display' => $type_display,
                        'changes' => $changes,
                        'change_summary' => $change_summary,
                        'files_added' => $files_count,
                        'has_data_changes' => $has_data_changes,
                        'has_file_changes' => $has_file_changes,
                        'user_type' => $user_type,
                        'timestamp' => $current_time,
                        'notification_type' => 'user_data_update_confirmation'
                    ], JSON_UNESCAPED_UNICODE);

                    $confirmation_url = ($user_type === 'public')
                        ? site_url("Elderly_aw_ods/my_elderly_aw_ods_detail/{$elderly_id}")
                        : site_url("Elderly_aw_ods/elderly_detail/{$elderly_id}");

                    $user_notification = [
                        'type' => 'elderly_aw_ods_data_update_confirm',
                        'title' => $user_title,
                        'message' => $user_message,
                        'reference_id' => 0,
                        'reference_table' => 'tbl_elderly_aw_ods',
                        'target_role' => $user_target_role,
                        'target_user_id' => $user_id,
                        'priority' => 'high',
                        'icon' => 'fas fa-check-circle',
                        'url' => $confirmation_url,
                        'data' => $user_data_json,
                        'created_at' => $current_time,
                        'created_by' => $user_id,
                        'is_read' => 0,
                        'is_system' => 1,
                        'is_archived' => 0
                    ];

                    $user_result = $this->db->insert('tbl_notifications', $user_notification);

                    if ($user_result) {
                        log_message('info', "User confirmation notification created for elderly_aw_ods: {$elderly_id} (user: {$user_id}, type: {$user_type})");
                    }
                }
            }

            // *** 3. แจ้งเตือนพิเศษสำหรับการเปลี่ยนแปลงข้อมูลสำคัญ (เฉพาะ Staff อื่นๆ) ***
            $critical_changes = [];
            if (!empty($changes['phone_changed'])) {
                $critical_changes[] = 'เบอร์โทรศัพท์';
            }
            if (!empty($changes['email_changed'])) {
                $critical_changes[] = 'อีเมล';
            }

            if (!empty($critical_changes)) {
                $critical_message = "มีการเปลี่ยนแปลงข้อมูลติดต่อสำคัญในเบี้ยยังชีพ{$type_display} #{$elderly_id}: " . implode(', ', $critical_changes) . " โดย {$user_name}";

                $critical_data_json = json_encode([
                    'elderly_aw_ods_id' => $elderly_id,
                    'type' => $elderly_data->elderly_aw_ods_type,
                    'type_display' => $type_display,
                    'requester' => $elderly_data->elderly_aw_ods_by,
                    'critical_changes' => $critical_changes,
                    'updated_by' => $user_name,
                    'updated_by_type' => $user_type,
                    'updated_by_id' => $user_id,
                    'change_details' => $change_details,
                    'timestamp' => $current_time,
                    'notification_type' => 'critical_data_change'
                ], JSON_UNESCAPED_UNICODE);

                $critical_notification = [
                    'type' => 'elderly_aw_ods_critical_update',
                    'title' => 'เปลี่ยนแปลงข้อมูลสำคัญ',
                    'message' => $critical_message,
                    'reference_id' => 0,
                    'reference_table' => 'tbl_elderly_aw_ods',
                    'target_role' => 'staff',
                    'priority' => 'high',
                    'icon' => 'fas fa-exclamation-triangle',
                    'url' => site_url("Elderly_aw_ods/elderly_detail/{$elderly_id}"),
                    'data' => $critical_data_json,
                    'created_at' => $current_time,
                    'created_by' => $user_id,
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                // *** เพิ่มเงื่อนไข: ถ้าผู้แก้ไขเป็น Staff ให้ระบุไม่ส่งให้ตัวเอง ***
                if ($user_type === 'staff' && $user_id > 0) {
                    $critical_notification['exclude_user_id'] = $user_id; // ไม่ส่งให้ตัวเอง
                }

                $critical_result = $this->db->insert('tbl_notifications', $critical_notification);

                if ($critical_result) {
                    log_message('info', "Critical data change notification created for elderly_aw_ods: {$elderly_id} (excluded user: {$user_id})");
                }
            }

            // *** สรุปการส่งการแจ้งเตือน ***
            $notification_summary = [
                'elderly_id' => $elderly_id,
                'updated_by' => $user_name,
                'user_type' => $user_type,
                'user_id' => $user_id,
                'notifications_sent' => [
                    'staff_general' => isset($staff_result) && $staff_result,
                    'user_confirmation' => isset($user_result) && $user_result,
                    'critical_changes' => isset($critical_result) && $critical_result
                ],
                'has_data_changes' => $has_data_changes,
                'has_file_changes' => $has_file_changes,
                'files_count' => $files_count,
                'change_summary' => $change_summary
            ];

            log_message('info', "Elderly AW ODS data update notifications completed: " . json_encode($notification_summary, JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', 'Error creating elderly_aw_ods data update notifications: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }





    private function handle_elderly_file_uploads_safe($elderly_id)
    {
        $uploaded_files = [];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        $upload_dir = './uploads/elderly_aw_ods/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                log_message('error', 'Failed to create upload directory: ' . $upload_dir);
                return [];
            }
        }

        // ตรวจสอบว่ามีไฟล์อัปโหลดจริงๆ
        if (!isset($_FILES['elderly_additional_files']) || !is_array($_FILES['elderly_additional_files']['name'])) {
            log_message('info', 'No files detected in $_FILES[elderly_additional_files]');
            return [];
        }

        // กรองไฟล์ที่มีชื่อไฟล์ว่าง
        $valid_files = [];
        $file_count = count($_FILES['elderly_additional_files']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if (!empty($_FILES['elderly_additional_files']['name'][$i])) {
                $valid_files[] = $i;
            }
        }

        if (empty($valid_files)) {
            log_message('info', 'No valid files found after filtering empty names');
            return [];
        }

        log_message('info', 'Processing ' . count($valid_files) . ' valid files for elderly_id: ' . $elderly_id);

        // *** สร้าง unique timestamp สำหรับป้องกันไฟล์ซ้ำ ***
        $upload_timestamp = date('YmdHis') . '_' . uniqid();

        foreach ($valid_files as $index => $i) {
            try {
                // ตรวจสอบข้อผิดพลาดการอัพโหลด
                if ($_FILES['elderly_additional_files']['error'][$i] !== UPLOAD_ERR_OK) {
                    log_message('debug', 'File upload error at index ' . $i . ': ' . $_FILES['elderly_additional_files']['error'][$i]);
                    continue;
                }

                $file_tmp = $_FILES['elderly_additional_files']['tmp_name'][$i];
                $file_name = $_FILES['elderly_additional_files']['name'][$i];
                $file_size = $_FILES['elderly_additional_files']['size'][$i];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // ตรวจสอบประเภทไฟล์
                if (!in_array($file_ext, $allowed_types)) {
                    log_message('debug', 'Invalid file type: ' . $file_ext . ' for file: ' . $file_name);
                    continue;
                }

                // ตรวจสอบขนาดไฟล์
                if ($file_size > $max_size) {
                    log_message('debug', 'File size too large: ' . $file_size . ' for file: ' . $file_name);
                    continue;
                }

                // ตรวจสอบว่าไฟล์มีอยู่จริง
                if (!file_exists($file_tmp) || !is_uploaded_file($file_tmp)) {
                    log_message('debug', 'Uploaded file not found or invalid: ' . $file_tmp);
                    continue;
                }

                // ตรวจสอบไฟล์ให้ปลอดภัย
                if (!$this->is_safe_file($file_tmp, $file_ext)) {
                    log_message('debug', 'Unsafe file detected: ' . $file_name);
                    continue;
                }

                // *** สร้างชื่อไฟล์ใหม่แบบ unique เพื่อป้องกันซ้ำ ***
                $clean_elderly_id = preg_replace('/[^a-zA-Z0-9]/', '', $elderly_id);
                $new_file_name = 'elderly_' . $clean_elderly_id . '_' . $upload_timestamp . '_' . $index . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;

                // *** ตรวจสอบว่าไฟล์ซ้ำในระบบไฟล์หรือไม่ ***
                $counter = 1;
                $original_new_file_name = $new_file_name;
                while (file_exists($upload_path)) {
                    $new_file_name = 'elderly_' . $clean_elderly_id . '_' . $upload_timestamp . '_' . $index . '_dup' . $counter . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_file_name;
                    $counter++;

                    if ($counter > 100) { // ป้องกัน infinite loop
                        log_message('error', 'Too many duplicate files, skipping: ' . $file_name);
                        continue 2;
                    }
                }

                // *** ตรวจสอบว่าไฟล์ซ้ำในฐานข้อมูลหรือไม่ ***
                if ($this->db->table_exists('tbl_elderly_aw_ods_files')) {
                    $existing_file_check = $this->db->select('elderly_aw_ods_file_id')
                        ->from('tbl_elderly_aw_ods_files')
                        ->where('elderly_aw_ods_file_ref_id', $elderly_id)
                        ->where('elderly_aw_ods_file_original_name', $file_name)
                        ->where('elderly_aw_ods_file_size', $file_size)
                        ->where('elderly_aw_ods_file_status', 'active')
                        ->get();

                    if ($existing_file_check->num_rows() > 0) {
                        log_message('debug', 'Duplicate file detected in database: ' . $file_name . ' for elderly_id: ' . $elderly_id);
                        continue;
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

                    log_message('info', 'Elderly file uploaded successfully: ' . $new_file_name . ' for ' . $elderly_id . ' (original: ' . $file_name . ')');
                } else {
                    log_message('error', 'Failed to move uploaded file: ' . $file_name . ' to ' . $upload_path);
                }

            } catch (Exception $e) {
                log_message('error', 'Error processing file at index ' . $i . ': ' . $e->getMessage());
                continue;
            }
        }

        log_message('info', 'Total elderly files uploaded successfully: ' . count($uploaded_files) . ' for ' . $elderly_id);
        return $uploaded_files;
    }



    /**
     * ลบไฟล์เบี้ยยังชีพ (AJAX)
     */
    public function delete_elderly_file()
    {
        // *** ป้องกัน output ใดๆ ก่อนส่ง JSON ***
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in'] || !$current_user['user_info']) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
                return;
            }

            $file_id = $this->input->post('file_id');
            $elderly_id = $this->input->post('elderly_id');

            if (empty($file_id) || empty($elderly_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            $elderly_data = $this->get_elderly_by_user($elderly_id, $current_user);

            if (!$elderly_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์เข้าถึง']);
                return;
            }

            $editable_statuses = ['submitted', 'reviewing'];
            if (!in_array($elderly_data->elderly_aw_ods_status, $editable_statuses)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถลบไฟล์ได้ในสถานะปัจจุบัน']);
                return;
            }

            // ดึงข้อมูลไฟล์
            $this->db->where('elderly_aw_ods_file_id', $file_id);
            $this->db->where('elderly_aw_ods_file_ref_id', $elderly_id);
            $this->db->where('elderly_aw_ods_file_status', 'active');
            $file_data = $this->db->get('tbl_elderly_aw_ods_files')->row();

            if (!$file_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบไฟล์ที่ต้องการลบ']);
                return;
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตสถานะไฟล์เป็น deleted
            $file_update_data = [
                'elderly_aw_ods_file_status' => 'deleted',
                'elderly_aw_ods_file_deleted_at' => date('Y-m-d H:i:s'),
                'elderly_aw_ods_file_deleted_by' => $current_user['user_info']['name']
            ];

            $this->db->where('elderly_aw_ods_file_id', $file_id);
            $delete_result = $this->db->update('tbl_elderly_aw_ods_files', $file_update_data);

            if (!$delete_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถลบไฟล์ได้']);
                return;
            }

            // อัปเดตเวลาแก้ไข
            $this->db->where('elderly_aw_ods_id', $elderly_id);
            $this->db->update('tbl_elderly_aw_ods', [
                'elderly_aw_ods_updated_at' => date('Y-m-d H:i:s'),
                'elderly_aw_ods_updated_by' => $current_user['user_info']['name']
            ]);

            // บันทึกประวัติ
            if ($this->db->table_exists('tbl_elderly_aw_ods_history')) {
                $history_data = [
                    'elderly_aw_ods_history_ref_id' => $elderly_id,
                    'action_type' => 'file_deleted',
                    'action_description' => 'ลบไฟล์: ' . $file_data->elderly_aw_ods_file_original_name,
                    'action_by' => $current_user['user_info']['name'],
                    'action_date' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_elderly_aw_ods_history', $history_data);
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบไฟล์']);
                return;
            }

            // ลบไฟล์จากระบบ
            if (!empty($file_data->elderly_aw_ods_file_path) && file_exists($file_data->elderly_aw_ods_file_path)) {
                @unlink($file_data->elderly_aw_ods_file_path);
            }

            ob_end_clean();

            $this->json_response([
                'success' => true,
                'message' => 'ลบไฟล์สำเร็จ',
                'file_name' => $file_data->elderly_aw_ods_file_original_name,
                'elderly_id' => $elderly_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in delete_elderly_file: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'DELETE_FILE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
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
     * Helper: ดึงข้อมูลเบี้ยยังชีพตาม user และตรวจสอบสิทธิ์
     */
    private function get_elderly_by_user($elderly_id, $current_user)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_ods');
            $this->db->where('elderly_aw_ods_id', $elderly_id);

            // ตรวจสอบสิทธิ์ตาม user type
            if ($current_user['user_type'] === 'public') {
                $this->db->where('elderly_aw_ods_user_type', 'public');
                $this->db->where('elderly_aw_ods_user_id', $current_user['user_info']['id']);
            } elseif ($current_user['user_type'] === 'staff') {
                // Staff สามารถเข้าถึงได้ทุกรายการ (ถ้ามีสิทธิ์)
                // เพิ่มการตรวจสอบสิทธิ์ staff ที่นี่ถ้าจำเป็น
            } else {
                // Guest ไม่สามารถแก้ไขได้ผ่าน login system
                return null;
            }

            $query = $this->db->get();
            return $query->num_rows() > 0 ? $query->row() : null;

        } catch (Exception $e) {
            log_message('error', 'Error in get_elderly_by_user: ' . $e->getMessage());
            return null;
        }
    }




    /**
     * Helper: ดึงไฟล์ของเบี้ยยังชีพ
     */
    private function get_elderly_files($elderly_id)
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_ods_files');
            $this->db->where('elderly_aw_ods_file_ref_id', $elderly_id);
            $this->db->where('elderly_aw_ods_file_status', 'active'); // เฉพาะไฟล์ที่ยังใช้งาน
            $this->db->order_by('elderly_aw_ods_file_uploaded_at', 'DESC');
            $query = $this->db->get();

            $files = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $file) {
                    $files[] = [
                        'file_id' => $file->elderly_aw_ods_file_id,
                        'file_name' => $file->elderly_aw_ods_file_name,
                        'original_name' => $file->elderly_aw_ods_file_original_name,
                        'file_type' => $file->elderly_aw_ods_file_type,
                        'file_size' => $file->elderly_aw_ods_file_size,
                        'file_path' => $file->elderly_aw_ods_file_path,
                        'category' => $file->elderly_aw_ods_file_category,
                        'uploaded_at' => $file->elderly_aw_ods_file_uploaded_at,
                        'uploaded_by' => $file->elderly_aw_ods_file_uploaded_by,
                        'description' => $file->elderly_aw_ods_file_description,
                        'download_url' => !empty($file->elderly_aw_ods_file_name) ? base_url('uploads/elderly_aw_ods/' . $file->elderly_aw_ods_file_name) : ''
                    ];
                }
            }

            return $files;

        } catch (Exception $e) {
            log_message('error', 'Error in get_elderly_files: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: จัดการการอัปโหลดไฟล์เบี้ยยังชีพ
     */
    private function handle_elderly_file_uploads($elderly_id)
    {
        $uploaded_files = [];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        $upload_dir = './uploads/elderly_aw_ods/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                log_message('error', 'Failed to create upload directory: ' . $upload_dir);
                return [];
            }
        }

        if (!isset($_FILES['elderly_additional_files']) || !is_array($_FILES['elderly_additional_files']['name'])) {
            return [];
        }

        $file_count = count($_FILES['elderly_additional_files']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            // ตรวจสอบข้อผิดพลาดการอัพโหลด
            if ($_FILES['elderly_additional_files']['error'][$i] !== UPLOAD_ERR_OK) {
                log_message('debug', 'File upload error at index ' . $i . ': ' . $_FILES['elderly_additional_files']['error'][$i]);
                continue;
            }

            $file_tmp = $_FILES['elderly_additional_files']['tmp_name'][$i];
            $file_name = $_FILES['elderly_additional_files']['name'][$i];
            $file_size = $_FILES['elderly_additional_files']['size'][$i];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // ตรวจสอบประเภทไฟล์
            if (!in_array($file_ext, $allowed_types)) {
                log_message('debug', 'Invalid file type: ' . $file_ext . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบขนาดไฟล์
            if ($file_size > $max_size) {
                log_message('debug', 'File size too large: ' . $file_size . ' for file: ' . $file_name);
                continue;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file_tmp) || !is_uploaded_file($file_tmp)) {
                log_message('debug', 'Uploaded file not found or invalid: ' . $file_tmp);
                continue;
            }

            // ตรวจสอบไฟล์ให้ปลอดภัย
            if (!$this->is_safe_file($file_tmp, $file_ext)) {
                log_message('debug', 'Unsafe file detected: ' . $file_name);
                continue;
            }

            // สร้างชื่อไฟล์ใหม่
            $new_file_name = 'elderly_' . $elderly_id . '_' . date('YmdHis') . '_' . $i . '_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;

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

                log_message('info', 'Elderly file uploaded successfully: ' . $new_file_name . ' for ' . $elderly_id);
            } else {
                log_message('error', 'Failed to move uploaded file: ' . $file_name . ' to ' . $upload_path);
            }
        }

        log_message('info', 'Total elderly files uploaded: ' . count($uploaded_files) . ' for ' . $elderly_id);
        return $uploaded_files;
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
     * Helper: ดึง MIME type จาก extension
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
     * หน้ารายละเอียดเบี้ยยังชีพของฉัน
     */
    public function my_elderly_aw_ods_detail($elderly_id = null)
    {
        try {
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            $login_info = $this->get_current_user_detailed();
            $data['is_logged_in'] = $login_info['is_logged_in'];
            $data['user_info'] = $login_info['user_info'];
            $data['user_type'] = $login_info['user_type'];

            if (!$data['is_logged_in'] || !$data['user_info']) {
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อน');
                redirect('User');
                return;
            }

            if (empty($elderly_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขอ้างอิง');
                redirect('Elderly_aw_ods/my_elderly_aw_ods');
                return;
            }

            // ดึงข้อมูลเบี้ยยังชีพ
            $elderly_detail = $this->get_elderly_by_user($elderly_id, $login_info);

            if (!$elderly_detail) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์เข้าถึง');
                redirect('Elderly_aw_ods/my_elderly_aw_ods');
                return;
            }

            // *** แก้ไข: ดึงไฟล์แบบเดียวกับ get_elderly_data ***
            $elderly_detail->files = $this->get_elderly_files_for_display($elderly_id);
            $elderly_detail->history = $this->get_elderly_history($elderly_id);

            $data['elderly_detail'] = $elderly_detail;
            $data['page_title'] = 'รายละเอียดเบี้ยยังชีพ #' . $elderly_id;

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_elderly_aw_ods_detail', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in my_elderly_aw_ods_detail: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด');
            redirect('Elderly_aw_ods/my_elderly_aw_ods');
        }
    }




    private function get_elderly_files_for_display($elderly_id)
    {
        try {
            if (!$this->db->table_exists('tbl_elderly_aw_ods_files')) {
                log_message('info', 'Files table does not exist');
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_ods_files');
            $this->db->where('elderly_aw_ods_file_ref_id', $elderly_id);
            $this->db->where('elderly_aw_ods_file_status', 'active');
            $this->db->order_by('elderly_aw_ods_file_uploaded_at', 'DESC');
            $query = $this->db->get();

            $files = [];
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $file) {
                    // *** ตรวจสอบไฟล์ในระบบไฟล์ ***
                    $file_exists = false;
                    $download_url = '';

                    if (!empty($file->elderly_aw_ods_file_name)) {
                        $file_path = FCPATH . 'uploads/elderly_aw_ods/' . $file->elderly_aw_ods_file_name;

                        if (file_exists($file_path)) {
                            $file_exists = true;
                            $download_url = base_url('uploads/elderly_aw_ods/' . $file->elderly_aw_ods_file_name);
                        } else {
                            log_message('debug', "File not found: {$file_path}");
                        }
                    }

                    // *** ส่งคืนข้อมูลในรูปแบบ array (เหมือน get_elderly_data) ***
                    $files[] = [
                        'file_id' => $file->elderly_aw_ods_file_id,
                        'elderly_aw_ods_file_name' => $file->elderly_aw_ods_file_name ?? '',
                        'elderly_aw_ods_file_original_name' => $file->elderly_aw_ods_file_original_name ?? '',
                        'elderly_aw_ods_file_type' => $file->elderly_aw_ods_file_type ?? '',
                        'elderly_aw_ods_file_size' => $file->elderly_aw_ods_file_size ?? 0,
                        'elderly_aw_ods_file_uploaded_at' => $file->elderly_aw_ods_file_uploaded_at ?? '',
                        'elderly_aw_ods_file_uploaded_by' => $file->elderly_aw_ods_file_uploaded_by ?? '',
                        'file_exists' => $file_exists,
                        'download_url' => $download_url
                    ];
                }
            }

            log_message('debug', "Retrieved " . count($files) . " files for display: {$elderly_id}");
            return $files;

        } catch (Exception $e) {
            log_message('error', 'Error in get_elderly_files_for_display: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * ดึงประวัติการดำเนินการ
     */
    private function get_elderly_history($elderly_id)
    {
        try {
            if (!$this->db->table_exists('tbl_elderly_aw_ods_history')) {
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_ods_history');
            $this->db->where('elderly_aw_ods_history_ref_id', $elderly_id);
            $this->db->order_by('action_date', 'DESC');
            $query = $this->db->get();

            return $query->num_rows() > 0 ? $query->result_array() : [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_elderly_history: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * API: ส่งข้อมูลสถิติเบี้ยยังชีพสำหรับ Dashboard
     */
    public function api_elderly_summary()
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
            if ($this->db->table_exists('tbl_elderly_aw_ods')) {
                // นับทั้งหมด
                $this->db->from('tbl_elderly_aw_ods');
                $statistics['total'] = $this->db->count_all_results();

                // นับตามสถานะแต่ละแบบ
                $statuses = ['submitted', 'reviewing', 'approved', 'rejected', 'completed'];

                foreach ($statuses as $status) {
                    $this->db->from('tbl_elderly_aw_ods');
                    $this->db->where('elderly_aw_ods_status', $status);
                    $statistics[$status] = $this->db->count_all_results();
                }
            }

            // จัดรูปแบบข้อมูลสำหรับ Dashboard
            $response_data = [
                'success' => true,
                'elder_allowance' => [
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

            log_message('info', 'Elderly summary API called - Total: ' . $statistics['total']);

        } catch (Exception $e) {
            log_message('error', 'Error in api_elderly_summary: ' . $e->getMessage());

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Server error',
                'elder_allowance' => [
                    'total' => 0,
                    'submitted' => 0,
                    'reviewing' => 0,
                    'completed' => 0
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * *** หน้าจัดการฟอร์มเบี้ยยังชีพ (สำหรับ Staff) ***
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
            $can_manage_forms = $this->check_forms_management_permission($staff_check);

            if (!$can_manage_forms) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์จัดการฟอร์มเบี้ยยังชีพ');
                redirect('Elderly_aw_ods/elderly_aw_ods');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // *** เพิ่มข้อมูลสิทธิ์ ***
            $data['can_add_forms'] = $this->check_forms_add_permission($staff_check);
            $data['can_edit_forms'] = $this->check_forms_edit_permission($staff_check);
            $data['can_delete_forms'] = $this->check_forms_delete_permission($staff_check);
            $data['can_toggle_forms'] = $this->check_forms_toggle_permission($staff_check);
            $data['staff_system_level'] = $staff_check->m_system;

            // *** สร้าง user_info object ที่ครบถ้วนสำหรับ header.php ***
            $user_info_object = $this->create_complete_user_info($staff_check);

            // *** ดึงข้อมูลฟอร์มทั้งหมด ***
            $all_forms = $this->get_all_forms_for_management();

            // *** เตรียมข้อมูลฟอร์มสำหรับแสดงผล ***
            $data['elderly_forms'] = $this->prepare_forms_for_management_display($all_forms);

            // *** คำนวณสถิติฟอร์ม ***
            $data['forms_statistics'] = $this->calculate_forms_statistics($all_forms);

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
                ['title' => 'หน้าแรก', 'url' => site_url('Elderly_aw_ods/elderly_aw_ods')],
                ['title' => 'จัดการเบี้ยยังชีพ', 'url' => site_url('Elderly_aw_ods/elderly_aw_ods')],
                ['title' => 'จัดการฟอร์ม', 'url' => '']
            ];

            // Page Title
            $data['page_title'] = 'จัดการฟอร์มเบี้ยยังชีพ';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Forms management data: ' . json_encode([
                    'staff_user' => $user_info_object->name,
                    'staff_system' => $staff_check->m_system,
                    'can_add_forms' => $data['can_add_forms'],
                    'can_edit_forms' => $data['can_edit_forms'],
                    'can_delete_forms' => $data['can_delete_forms'],
                    'can_toggle_forms' => $data['can_toggle_forms'],
                    'forms_count' => count($data['elderly_forms']),
                    'forms_statistics' => $data['forms_statistics']
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/elderly_forms_management', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in manage_forms: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าจัดการฟอร์ม: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Elderly_aw_ods/elderly_aw_ods');
            }
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การจัดการฟอร์มทั่วไป ***
     */
    private function check_forms_management_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถจัดการฟอร์มได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Forms management permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 50 (สิทธิ์เบี้ยยังชีพ)
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    log_message('error', "user_admin without grant_user_ref_id: {$staff_data->m_fname} {$staff_data->m_lname}");
                    return false;
                }

                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));
                $has_permission = in_array('50', $grant_ids);

                log_message('info', "user_admin forms management permission check: {$staff_data->m_fname} {$staff_data->m_lname} - " .
                    "grant_user_ref_id: {$staff_data->grant_user_ref_id} - " .
                    "has_50: " . ($has_permission ? 'YES' : 'NO'));

                return $has_permission;
            }

            // อื่นๆ ไม่สามารถจัดการฟอร์มได้
            log_message('info', "Forms management permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking forms management permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การเพิ่มฟอร์ม ***
     */
    private function check_forms_add_permission($staff_data)
    {
        try {
            // เหมือนกับสิทธิ์การจัดการทั่วไป
            return $this->check_forms_management_permission($staff_data);

        } catch (Exception $e) {
            log_message('error', 'Error checking forms add permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การแก้ไขฟอร์ม ***
     */
    private function check_forms_edit_permission($staff_data)
    {
        try {
            // เหมือนกับสิทธิ์การจัดการทั่วไป
            return $this->check_forms_management_permission($staff_data);

        } catch (Exception $e) {
            log_message('error', 'Error checking forms edit permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การลบฟอร์ม ***
     */
    private function check_forms_delete_permission($staff_data)
    {
        try {
            // เฉพาะ system_admin และ super_admin ที่สามารถลบฟอร์มได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                log_message('info', "Forms delete permission granted for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
                return true;
            }

            log_message('info', "Forms delete permission denied for {$staff_data->m_system}: {$staff_data->m_fname} {$staff_data->m_lname}");
            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking forms delete permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ตรวจสอบสิทธิ์การ toggle สถานะฟอร์ม ***
     */
    private function check_forms_toggle_permission($staff_data)
    {
        try {
            // เหมือนกับสิทธิ์การจัดการทั่วไป
            return $this->check_forms_management_permission($staff_data);

        } catch (Exception $e) {
            log_message('error', 'Error checking forms toggle permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * *** ดึงข้อมูลฟอร์มทั้งหมดสำหรับการจัดการ ***
     */
    private function get_all_forms_for_management()
    {
        try {
            if (!$this->db->table_exists('tbl_elderly_aw_form')) {
                log_message('error', 'Forms table does not exist');
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_elderly_aw_form');
            $this->db->order_by('elderly_aw_form_datesave', 'DESC');
            $query = $this->db->get();

            $forms = [];
            if ($query->num_rows() > 0) {
                $forms = $query->result();
                log_message('info', 'Retrieved ' . count($forms) . ' forms for management');
            } else {
                log_message('info', 'No forms found in database');
            }

            return $forms;

        } catch (Exception $e) {
            log_message('error', 'Error in get_all_forms_for_management: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * *** เตรียมข้อมูลฟอร์มสำหรับแสดงผลในหน้าจัดการ ***
     */
    private function prepare_forms_for_management_display($forms)
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

            if (!empty($form->elderly_aw_form_file)) {
                $file_path = FCPATH . 'docs/file/' . $form->elderly_aw_form_file;

                if (file_exists($file_path)) {
                    $file_exists = true;
                    $file_size = filesize($file_path);
                    $file_extension = strtolower(pathinfo($form->elderly_aw_form_file, PATHINFO_EXTENSION));
                    $download_url = base_url('docs/file/' . $form->elderly_aw_form_file);
                } else {
                    log_message('error', "Form file not found: {$form->elderly_aw_form_file} for form ID: {$form->elderly_aw_form_id}");
                }
            }

            // เตรียมข้อมูลที่ครบถ้วน
            $form_data = [
                'elderly_aw_form_id' => $form->elderly_aw_form_id ?? '',
                'elderly_aw_form_name' => $form->elderly_aw_form_name ?? '',
                'elderly_aw_form_type' => $form->elderly_aw_form_type ?? 'general',
                'elderly_aw_form_description' => $form->elderly_aw_form_description ?? '',
                'elderly_aw_form_file' => $form->elderly_aw_form_file ?? '',
                'elderly_aw_form_status' => $form->elderly_aw_form_status ?? 0,
                'elderly_aw_form_datesave' => $form->elderly_aw_form_datesave ?? '',
                'elderly_aw_form_by' => $form->elderly_aw_form_by ?? '',

                // ข้อมูลไฟล์
                'file_exists' => $file_exists,
                'file_size' => $file_size,
                'file_extension' => $file_extension,
                'download_url' => $download_url,
                'file_size_formatted' => $this->format_file_size($file_size),

                // ข้อมูลแสดงผล
                'status_display' => $this->get_form_status_display($form->elderly_aw_form_status ?? 0),
                'status_class' => $this->get_form_status_class($form->elderly_aw_form_status ?? 0),
                'type_display' => $this->get_form_type_display($form->elderly_aw_form_type ?? 'general'),
                'type_icon' => $this->get_form_type_icon($form->elderly_aw_form_type ?? 'general'),

                // วันที่แสดงผล
                'formatted_date' => $this->format_thai_date($form->elderly_aw_form_datesave ?? ''),

                // สถานะการใช้งาน
                'is_active' => ($form->elderly_aw_form_status ?? 0) == 1,
                'can_download' => $file_exists && (($form->elderly_aw_form_status ?? 0) == 1)
            ];

            $prepared_forms[] = (object) $form_data;
        }

        log_message('info', 'Prepared ' . count($prepared_forms) . ' forms for management display');
        return $prepared_forms;
    }

    /**
     * *** คำนวณสถิติฟอร์ม ***
     */
    private function calculate_forms_statistics($forms)
    {
        $statistics = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'elderly_type' => 0,
            'disabled_type' => 0,
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
            if (($form->elderly_aw_form_status ?? 0) == 1) {
                $statistics['active']++;
            } else {
                $statistics['inactive']++;
            }

            // นับตามประเภท
            switch ($form->elderly_aw_form_type ?? 'general') {
                case 'elderly':
                    $statistics['elderly_type']++;
                    break;
                case 'disabled':
                    $statistics['disabled_type']++;
                    break;
                case 'authorization':
                    $statistics['authorization_type']++;
                    break;
                default:
                    $statistics['general_type']++;
                    break;
            }

            // ตรวจสอบไฟล์
            if (!empty($form->elderly_aw_form_file)) {
                $file_path = FCPATH . 'docs/file/' . $form->elderly_aw_form_file;
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
     * *** Helper Functions ***
     */

    private function get_form_status_display($status)
    {
        return $status == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    }

    private function get_form_status_class($status)
    {
        return $status == 1 ? 'active' : 'inactive';
    }

    private function get_form_type_display($type)
    {
        $types = [
            'elderly' => 'ผู้สูงอายุ',
            'disabled' => 'ผู้พิการ',
            'authorization' => 'หนังสือมอบอำนาจ',
            'general' => 'ทั่วไป'
        ];

        return $types[$type] ?? 'ทั่วไป';
    }

    private function get_form_type_icon($type)
    {
        $icons = [
            'elderly' => 'fas fa-user-clock',
            'disabled' => 'fas fa-wheelchair',
            'authorization' => 'fas fa-file-signature',
            'general' => 'fas fa-file-alt'
        ];

        return $icons[$type] ?? 'fas fa-file-alt';
    }

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
            if (!$this->check_forms_add_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เพิ่มฟอร์ม']);
                return;
            }

            // รับข้อมูลจากฟอร์ม
            $form_name = $this->input->post('form_name');
            $form_type = $this->input->post('form_type') ?: 'general';
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
            $upload_result = $this->handle_form_file_upload();

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
                'elderly_aw_form_name' => $form_name,
                'elderly_aw_form_type' => $form_type,
                'elderly_aw_form_description' => $form_description,
                'elderly_aw_form_file' => $uploaded_file,
                'elderly_aw_form_status' => $form_status,
                'elderly_aw_form_by' => $updated_by,
                'elderly_aw_form_datesave' => date('Y-m-d H:i:s')
            ];

            $insert_result = $this->db->insert('tbl_elderly_aw_form', $form_data);

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

            log_message('info', "Form added successfully: ID={$form_id}, Name={$form_name}, By={$updated_by}");

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
            $this->db->from('tbl_elderly_aw_form');
            $this->db->where('elderly_aw_form_id', $form_id);
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
                    'elderly_aw_form_id' => $form_data->elderly_aw_form_id,
                    'elderly_aw_form_name' => $form_data->elderly_aw_form_name,
                    'elderly_aw_form_type' => $form_data->elderly_aw_form_type,
                    'elderly_aw_form_description' => $form_data->elderly_aw_form_description ?? '',
                    'elderly_aw_form_file' => $form_data->elderly_aw_form_file,
                    'elderly_aw_form_status' => $form_data->elderly_aw_form_status,
                    'elderly_aw_form_by' => $form_data->elderly_aw_form_by,
                    'elderly_aw_form_datesave' => $form_data->elderly_aw_form_datesave
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
            if (!$this->check_forms_edit_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์แก้ไขฟอร์ม']);
                return;
            }

            // รับข้อมูลจากฟอร์ม
            $form_id = $this->input->post('form_id');
            $form_name = $this->input->post('form_name');
            $form_type = $this->input->post('form_type') ?: 'general';
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
            $this->db->from('tbl_elderly_aw_form');
            $this->db->where('elderly_aw_form_id', $form_id);
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
                'elderly_aw_form_name' => $form_name,
                'elderly_aw_form_type' => $form_type,
                'elderly_aw_form_description' => $form_description,
                'elderly_aw_form_status' => $form_status,
                'elderly_aw_form_updated_by' => $updated_by,
                'elderly_aw_form_updated_at' => date('Y-m-d H:i:s')
            ];

            // ตรวจสอบไฟล์ใหม่
            $new_file = '';
            if (isset($_FILES['form_file']) && $_FILES['form_file']['error'] === UPLOAD_ERR_OK) {
                $upload_result = $this->handle_form_file_upload();

                if (!$upload_result['success']) {
                    $this->db->trans_rollback();
                    ob_end_clean();
                    $this->json_response(['success' => false, 'message' => $upload_result['message']]);
                    return;
                }

                $new_file = $upload_result['file_name'];
                $update_data['elderly_aw_form_file'] = $new_file;
            }

            // อัพเดตข้อมูล
            $this->db->where('elderly_aw_form_id', $form_id);
            $update_result = $this->db->update('tbl_elderly_aw_form', $update_data);

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
            if (!empty($new_file) && !empty($existing_form->elderly_aw_form_file)) {
                $old_file_path = FCPATH . 'docs/file/' . $existing_form->elderly_aw_form_file;
                if (file_exists($old_file_path)) {
                    @unlink($old_file_path);
                }
            }

            ob_end_clean();

            log_message('info', "Form updated successfully: ID={$form_id}, Name={$form_name}, By={$updated_by}");

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
            if (!$this->check_forms_delete_permission($staff_data)) {
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
            $this->db->from('tbl_elderly_aw_form');
            $this->db->where('elderly_aw_form_id', $form_id);
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
            $this->db->where('elderly_aw_form_id', $form_id);
            $delete_result = $this->db->delete('tbl_elderly_aw_form');

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
            if (!empty($form_data->elderly_aw_form_file)) {
                $file_path = FCPATH . 'docs/file/' . $form_data->elderly_aw_form_file;
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }

            ob_end_clean();

            log_message('info', "Form deleted successfully: ID={$form_id}, Name={$form_data->elderly_aw_form_name}, By={$deleted_by}");

            $this->json_response([
                'success' => true,
                'message' => 'ลบฟอร์มสำเร็จ',
                'form_id' => $form_id,
                'form_name' => $form_data->elderly_aw_form_name
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
            if (!$this->check_forms_toggle_permission($staff_data)) {
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
            $this->db->select('elderly_aw_form_name, elderly_aw_form_status');
            $this->db->from('tbl_elderly_aw_form');
            $this->db->where('elderly_aw_form_id', $form_id);
            $form_data = $this->db->get()->row();

            if (!$form_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลฟอร์ม']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // อัพเดตสถานะ
            $update_data = [
                'elderly_aw_form_status' => (int) $status,
                'elderly_aw_form_updated_by' => $updated_by,
                'elderly_aw_form_updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('elderly_aw_form_id', $form_id);
            $update_result = $this->db->update('tbl_elderly_aw_form', $update_data);

            if (!$update_result) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถอัพเดตสถานะได้']);
                return;
            }

            ob_end_clean();

            $status_text = $status == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน';

            log_message('info', "Form status toggled: ID={$form_id}, Status={$status_text}, By={$updated_by}");

            $this->json_response([
                'success' => true,
                'message' => 'เปลี่ยนสถานะฟอร์มสำเร็จ',
                'form_id' => $form_id,
                'form_name' => $form_data->elderly_aw_form_name,
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
     * *** Helper Function: จัดการอัพโหลดไฟล์ฟอร์ม ***
     */
    private function handle_form_file_upload()
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
        $new_file_name = 'form_' . date('YmdHis') . '_' . uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_file_name;

        // ตรวจสอบว่าไฟล์ซ้ำหรือไม่
        $counter = 1;
        while (file_exists($upload_path)) {
            $new_file_name = 'form_' . date('YmdHis') . '_' . uniqid() . '_' . $counter . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;
            $counter++;

            if ($counter > 100) {
                return ['success' => false, 'message' => 'ไม่สามารถสร้างชื่อไฟล์ที่ไม่ซ้ำได้'];
            }
        }

        // ย้ายไฟล์
        if (move_uploaded_file($file_tmp, $upload_path)) {
            log_message('info', 'Form file uploaded successfully: ' . $new_file_name);
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













}