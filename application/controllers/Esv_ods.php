<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Esv_ods extends CI_Controller
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
        $this->load->model('activity_model');
        $this->load->model('news_model');
        $this->load->model('announce_model');
        $this->load->model('order_model');
        $this->load->model('procurement_model');
        $this->load->model('mui_model');
        $this->load->model('guide_work_model');
        $this->load->model('loadform_model');
        $this->load->model('pppw_model');
        $this->load->model('msg_pres_model');
        $this->load->model('history_model');
        $this->load->model('otop_model');
        $this->load->model('gci_model');
        $this->load->model('vision_model');
        $this->load->model('authority_model');
        $this->load->model('mission_model');
        $this->load->model('motto_model');
        $this->load->model('cmi_model');
        $this->load->model('executivepolicy_model');
        $this->load->model('travel_model');
        $this->load->model('si_model');

        // โหลด models สำหรับ navbar
        $this->load->model('HotNews_model');
        $this->load->model('Weather_report_model');
        $this->load->model('calender_model');
        $this->load->model('banner_model');
        $this->load->model('background_personnel_model');
        $this->load->model('member_public_model');

        // โหลด ESV model
        $this->load->model('Esv_ods_model', 'esv_model');
        $this->load->library('recaptcha_lib');
        if (file_exists(APPPATH . 'config/recaptcha.php')) {
            $this->load->config('recaptcha');
            $recaptcha_config = $this->config->item('recaptcha');

            if ($recaptcha_config) {
                $this->recaptcha_lib->initialize($recaptcha_config);
                log_message('debug', 'reCAPTCHA Library initialized with config file');
            }
        }
    }

    /**
     * ฟังก์ชันแสดงหน้ายื่นเอกสาร - FIXED VERSION
     */
    public function submit_document()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            // ตรวจสอบการ redirect และ parameter
            $from_login = $this->input->get('from_login');
            $redirect_url = $this->input->get('redirect');

            $data['from_login'] = ($from_login === 'success');

            if ($redirect_url) {
                $this->session->set_userdata('redirect_after_login', $redirect_url);
                log_message('info', 'ESV: Redirect URL saved: ' . $redirect_url);
            }

            // *** ตรวจสอบสถานะ User Login - Enhanced ***
            $current_user = $this->get_current_user_detailed();

            // *** FIX: การส่งข้อมูลไป View ***
            $data['is_logged_in'] = $current_user['is_logged_in'];
            $data['user_type'] = $current_user['user_type'];
            $data['user_address'] = $current_user['user_address'];

            // *** FIX: ให้แน่ใจว่า user_info มี address อยู่แล้ว ***
            $data['user_info'] = $current_user['user_info'];

            // *** FIX: Double check - ถ้า user_info ไม่มี address ให้ merge จาก user_address ***
            if ($data['is_logged_in'] && !empty($current_user['user_address']) && empty($data['user_info']['address'])) {
                $data['user_info']['address'] = $current_user['user_address'];
                log_message('info', 'ESV: Merged user_address into user_info as fallback');
            }

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
                'php_user_info_has_address' => !empty($data['user_info']['address']),
                'timestamp' => date('Y-m-d H:i:s'),
                'environment' => ENVIRONMENT
            ];

            // *** เพิ่ม Debug Log แบบละเอียด ***
            log_message('debug', '=== ESV CONTROLLER - VIEW DATA DEBUG ===');
            log_message('debug', 'Data being sent to View: ' . json_encode([
                'is_logged_in' => $data['is_logged_in'],
                'user_type' => $data['user_type'],
                'user_info_exists' => !empty($data['user_info']),
                'user_info_id' => $data['user_info']['id'] ?? 'N/A',
                'user_info_name' => $data['user_info']['name'] ?? 'N/A',
                'user_info_has_address' => !empty($data['user_info']['address']),
                'user_address_exists' => !empty($data['user_address']),
                'session_debug_exists' => !empty($data['session_debug']),
                'mp_id_in_session' => $data['session_debug']['mp_id'] ?? 'N/A'
            ]));

            // *** FIX: Debug address structure ***
            if (!empty($data['user_info']['address'])) {
                log_message('debug', 'ESV: user_info.address structure: ' . json_encode($data['user_info']['address'], JSON_UNESCAPED_UNICODE));
            } else {
                log_message('debug', 'ESV: user_info.address is still empty after processing');
            }

            // ดึงข้อมูลสำหรับ dropdown
            $data['departments'] = $this->get_departments();
            $data['document_types'] = $this->get_document_types();
            $data['categories'] = $this->get_categories();

            $data['page_title'] = 'ยื่นเอกสารออนไลน์';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'ยื่นเอกสารออนไลน์', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // *** การตั้งค่า Cache Control ***
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            $this->output->set_header('Pragma: no-cache');
            $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

            // *** Final Debug Check ***
            if (ENVIRONMENT === 'development') {
                log_message('debug', '=== FINAL ESV DATA CHECK BEFORE VIEW ===');
                log_message('debug', 'Variables to be available in View:');
                log_message('debug', '- $is_logged_in: ' . var_export($data['is_logged_in'], true));
                log_message('debug', '- $user_type: ' . var_export($data['user_type'], true));
                log_message('debug', '- $user_info (exists): ' . var_export(!empty($data['user_info']), true));
                log_message('debug', '- $user_info.address (exists): ' . var_export(!empty($data['user_info']['address']), true));
                log_message('debug', '- $user_address (exists): ' . var_export(!empty($data['user_address']), true));
                log_message('debug', '- $session_debug (exists): ' . var_export(!empty($data['session_debug']), true));
                log_message('debug', '- $js_debug_data (exists): ' . var_export(!empty($data['js_debug_data']), true));

                if (!empty($data['user_info'])) {
                    log_message('debug', '- User ID: ' . ($data['user_info']['id'] ?? 'N/A'));
                    log_message('debug', '- User Name: ' . ($data['user_info']['name'] ?? 'N/A'));
                    log_message('debug', '- User Address Full: ' . ($data['user_info']['address']['full_address'] ?? 'N/A'));
                }
            }

            log_message('info', 'Loading ESV submit_document view with user type: ' . $data['user_type']);

            // โหลด view พร้อมส่งข้อมูลไป
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/esv_submit_document_new', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

            log_message('info', 'ESV submit_document view loaded successfully');

        } catch (Exception $e) {
            log_message('error', 'Critical error in ESV submit_document: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ายื่นเอกสาร: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    /**
     * ฟังก์ชันบันทึกเอกสาร - Enhanced with reCAPTCHA & Address Validation
     * นโยบายใหม่: ตรวจสอบ reCAPTCHA กับผู้ใช้ทุกคน ไม่มีการยกเว้น
     */
    public function submit()
    {
        try {
            // *** 1. การตั้งค่าเริ่มต้นและตรวจสอบพื้นฐาน ***
            log_message('info', '=== ESV SUBMIT START (Enhanced with reCAPTCHA) ===');
            log_message('info', 'POST data: ' . print_r($_POST, true));

            // Clear ALL output buffers to prevent any HTML output
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Start clean output buffering
            ob_start();

            // Set headers early but don't send yet
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');

            // Basic checks
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                show_404();
                return;
            }

            // *** 2. รับข้อมูลและตรวจสอบสถานะ ***
            $current_user = $this->get_current_user_detailed();
            $is_guest_user = !$current_user['is_logged_in'];
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'esv_submit';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'esv_form';
            $dev_mode = $this->input->post('dev_mode') === '1';

            // ส่งต่อไปยัง Library
            $recaptcha_options = [
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => $is_guest_user ? 'guest' : 'member'
            ];

            // *** 3. ส่วนจัดการ reCAPTCHA (Logic ใหม่) ***
            $recaptcha_result = null;
            $is_dev_skip = ($dev_mode && ENVIRONMENT === 'development');

            if ($is_dev_skip) {
                log_message('info', 'ESV DEVELOPMENT MODE: Skipping reCAPTCHA verification.');
            } else {
                // สำหรับ Production Mode, บังคับให้มี Token เสมอ
                if (empty($recaptcha_token)) {
                    log_message('error', 'ESV: Missing reCAPTCHA token in production mode.');
                    ob_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'การยืนยันความปลอดภัยไม่สมบูรณ์ กรุณารีเฟรชหน้าและลองใหม่อีกครั้ง',
                        'error_type' => 'recaptcha_missing'
                    ], JSON_UNESCAPED_UNICODE);
                    ob_end_flush();
                    exit;
                }

                // เริ่มการตรวจสอบ Token กับ Google (สำหรับผู้ใช้ทุกคน)
                $user_status_for_log = $is_guest_user ? 'GUEST' : 'LOGGED_IN';
                log_message('info', "ESV: Starting reCAPTCHA verification for {$user_status_for_log} user (Policy: All users verified).");

                try {
                    if (isset($this->recaptcha_lib)) {
                        $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, 'citizen', null, $recaptcha_options);

                        if (!$recaptcha_result['success']) {
                            log_message('error', 'ESV: reCAPTCHA verification failed: ' . json_encode($recaptcha_result));
                            ob_clean();
                            echo json_encode([
                                'success' => false,
                                'message' => 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง',
                                'error_type' => 'recaptcha_failed',
                                'recaptcha_data' => $recaptcha_result['data'] ?? null
                            ], JSON_UNESCAPED_UNICODE);
                            ob_end_flush();
                            exit;
                        }
                        log_message('info', 'ESV: ✅ reCAPTCHA verification successful. Score: ' . ($recaptcha_result['data']['score'] ?? 'N/A'));
                    } else {
                        throw new Exception('reCAPTCHA library not loaded');
                    }
                } catch (Exception $e) {
                    log_message('error', 'ESV: reCAPTCHA verification error: ' . $e->getMessage());
                    ob_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการตรวจสอบความปลอดภัย',
                        'error_type' => 'recaptcha_exception'
                    ], JSON_UNESCAPED_UNICODE);
                    ob_end_flush();
                    exit;
                }
            }
            // *** จบส่วนจัดการ reCAPTCHA ***

            // *** 4. การตรวจสอบความถูกต้องของข้อมูล (Form Validation) ***
            $validation_errors = [];

            $topic = $this->input->post('esv_ods_topic');
            $detail = $this->input->post('esv_ods_detail');

            if (empty($topic))
                $validation_errors[] = 'กรุณากรอกหัวข้อเรื่อง';
            if (empty($detail))
                $validation_errors[] = 'กรุณากรอกรายละเอียด';

            // ตรวจสอบข้อมูลสำหรับ guest user
            if ($is_guest_user) {
                if (empty($this->input->post('esv_ods_by')))
                    $validation_errors[] = 'กรุณากรอกชื่อ-นามสกุล';
                if (empty($this->input->post('esv_ods_phone')))
                    $validation_errors[] = 'กรุณากรอกเบอร์โทรศัพท์';
                if (empty($this->input->post('esv_ods_email')))
                    $validation_errors[] = 'กรุณากรอกอีเมล';
                if (empty($this->input->post('esv_ods_id_card')))
                    $validation_errors[] = 'กรุณากรอกเลขบัตรประชาชน';

                // ตรวจสอบที่อยู่สำหรับ guest user
                if (empty($this->input->post('esv_additional_address')))
                    $validation_errors[] = 'กรุณากรอกที่อยู่เพิ่มเติม';
                if (empty($this->input->post('esv_province')))
                    $validation_errors[] = 'กรุณาเลือกจังหวัด';
                if (empty($this->input->post('esv_amphoe')))
                    $validation_errors[] = 'กรุณาเลือกอำเภอ';
                if (empty($this->input->post('esv_district')))
                    $validation_errors[] = 'กรุณาเลือกตำบล';
            }

            // ตรวจสอบไฟล์ - แก้ไขส่วนนี้
            if (empty($_FILES['esv_ods_file']['name'][0])) {
                $validation_errors[] = 'กรุณาแนบไฟล์อย่างน้อย 1 ไฟล์';
            }

            if (!empty($validation_errors)) {
                log_message('error', 'ESV: Validation failed: ' . implode(', ', $validation_errors));
                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $validation_errors),
                    'error_type' => 'validation_failed'
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }

            log_message('info', 'ESV Submit - Basic validation passed');

            // *** 5. การเตรียมข้อมูลเพื่อบันทึก ***
            $reference_id = $this->generate_reference_id();

            // *** สร้างที่อยู่แบบ Enhanced - ใช้ function เดิม ***
            $full_address = $this->build_esv_full_address($current_user, $is_guest_user);

            // Prepare document data (ลบ reCAPTCHA fields ออก)
            $document_data = [
                'esv_ods_reference_id' => $reference_id,
                'esv_ods_topic' => $topic,
                'esv_ods_detail' => $detail,
                'esv_ods_by' => $is_guest_user ? $this->input->post('esv_ods_by') : $current_user['user_info']['name'],
                'esv_ods_phone' => $is_guest_user ? $this->input->post('esv_ods_phone') : $current_user['user_info']['phone'],
                'esv_ods_email' => $is_guest_user ? $this->input->post('esv_ods_email') : $current_user['user_info']['email'],
                'esv_ods_address' => $full_address,
                'esv_ods_user_type' => $is_guest_user ? 'guest' : $current_user['user_type'],
                'esv_ods_status' => 'pending',
                'esv_ods_tracking_code' => $reference_id,
                'esv_ods_priority' => 'normal',
                'esv_ods_ip_address' => $this->input->ip_address(),
                'esv_ods_datesave' => date('Y-m-d H:i:s')
            ];

            // เพิ่มข้อมูลเพิ่มเติม
            $department_id = $this->input->post('esv_ods_department_id');
            $category_id = $this->input->post('esv_ods_category_id');
            $document_type = $this->input->post('document_type');

            if (!empty($department_id) && $department_id !== 'other') {
                $document_data['esv_ods_department_id'] = $department_id;
            } else {
                $document_data['esv_ods_department_other'] = $this->input->post('esv_ods_department_other');
            }

            if (!empty($category_id) && $category_id !== 'other') {
                $document_data['esv_ods_category_id'] = $category_id;
            } else {
                $document_data['esv_ods_category_other'] = $this->input->post('esv_ods_category_other');
            }

            if (!empty($document_type)) {
                $document_data['esv_ods_type_id'] = $document_type;
            }

            // เพิ่มข้อมูลผู้ใช้
            if (!$is_guest_user && isset($current_user['user_info']['id'])) {
                $document_data['esv_ods_user_id'] = $current_user['user_info']['id'];
            } else if ($is_guest_user) {
                // Guest user - เพิ่มเลขบัตรประชาชน
                $id_card = $this->input->post('esv_ods_id_card');
                if (!empty($id_card)) {
                    $document_data['esv_ods_id_card'] = $id_card;
                }

                // *** สำหรับ Guest User ไม่ต้องเพิ่มฟิลด์แยก ***
                // ใช้ field esv_ods_address เดียวกับ logged user (มีการสร้างไว้แล้วใน $full_address)
            }

            log_message('info', 'ESV Submit - Document data prepared: ' . json_encode($document_data, JSON_UNESCAPED_UNICODE));

            // *** 6. การบันทึกข้อมูลและไฟล์ (Database Transaction) ***
            $this->db->trans_start();

            // Insert document - ใช้ method เดิม
            $document_id = $this->esv_model->add_document($document_data);

            if (!$document_id) {
                throw new Exception('Failed to save document');
            }

            log_message('info', 'ESV Submit - Document saved with ID: ' . $document_id);

            // Handle multiple file uploads - ใช้ function เดิม
            $upload_result = $this->handle_multiple_file_upload_fixed($reference_id, $document_id);

            if (!$upload_result || empty($upload_result['files'])) {
                throw new Exception('Failed to upload files');
            }

            // บันทึกข้อมูลไฟล์ใน tbl_esv_files - ใช้ method เดิม
            $files_saved = $this->save_multiple_file_records($document_id, $upload_result['files']);

            if (!$files_saved) {
                throw new Exception('Failed to save file records');
            }

            // บันทึกประวัติเริ่มต้น - ใช้ method เดิม แต่แก้ไข user_id issue
            $this->save_initial_history($document_id, $document_data, $current_user);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'ESV: Transaction failed for reference_id: ' . $reference_id);
                // ลบไฟล์ที่อัปโหลดไปแล้วถ้า DB fail
                $this->cleanup_uploaded_files($upload_result['files']);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }

            // *** 7. สร้างการแจ้งเตือนและส่ง Response สำเร็จ ***
            try {
                $this->create_document_notifications($document_id, $document_data, $current_user);
            } catch (Exception $e) {
                log_message('error', 'ESV: Failed to create notifications: ' . $e->getMessage());
            }

            log_message('info', "ESV: Document saved successfully: {$reference_id}");

            // Success response - Enhanced with clean output
            ob_clean(); // Clear any accidental output
            echo json_encode([
                'success' => true,
                'message' => 'ยื่นเอกสารสำเร็จ',
                'reference_id' => $reference_id,
                'tracking_code' => $reference_id,
                'document_id' => $document_id,
                'files_uploaded' => count($upload_result['files']),
                'user_type' => $current_user['user_type'],
                'is_logged_in' => $current_user['is_logged_in'],
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

            ob_end_flush();
            exit;

        } catch (Exception $e) {
            // Rollback on error
            $this->db->trans_rollback();

            log_message('error', 'Critical error in ESV submit: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            ob_clean(); // Clear any accidental output
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดร้ายแรงในระบบ',
                'error_type' => 'system_error'
            ], JSON_UNESCAPED_UNICODE);

            ob_end_flush();
            exit;
        }
    }

    /**
     * *** เพิ่ม: ฟังก์ชันสร้างที่อยู่แบบ Enhanced ***
     */
    private function build_esv_full_address($current_user, $is_guest_user)
    {
        $full_address = '';

        log_message('debug', '🏠 === ESV ADDRESS BUILDING DEBUG ===');
        log_message('debug', 'is_guest_user: ' . var_export($is_guest_user, true));
        log_message('debug', 'current_user: ' . json_encode($current_user));

        if (!$is_guest_user && !empty($current_user['user_info'])) {
            log_message('debug', '🏠 Processing address for LOGGED user');

            // Method 1: ใช้ user_address ที่มีอยู่
            if (!empty($current_user['user_address'])) {
                $addr = $current_user['user_address'];
                log_message('debug', '📍 Using user_address object: ' . json_encode($addr));

                $address_parts = [];
                if (!empty($addr['additional_address']))
                    $address_parts[] = $addr['additional_address'];
                if (!empty($addr['district']))
                    $address_parts[] = 'ตำบล' . $addr['district'];
                if (!empty($addr['amphoe']))
                    $address_parts[] = 'อำเภอ' . $addr['amphoe'];
                if (!empty($addr['province']))
                    $address_parts[] = 'จังหวัด' . $addr['province'];
                if (!empty($addr['zipcode']))
                    $address_parts[] = $addr['zipcode'];

                $full_address = implode(' ', $address_parts);
            }

            // Method 2: Fallback หาจาก user_info
            if (empty($full_address) && !empty($current_user['user_info'])) {
                $user_info = $current_user['user_info'];

                if (!empty($user_info['address'])) {
                    $full_address = $user_info['address'];
                    log_message('debug', '📍 Using user_info address: ' . $full_address);
                } elseif (!empty($user_info['full_address'])) {
                    $full_address = $user_info['full_address'];
                    log_message('debug', '📍 Using user_info full_address: ' . $full_address);
                }
            }

            // Method 3: Emergency default สำหรับ logged user
            if (empty($full_address)) {
                $full_address = 'ที่อยู่ไม่ระบุ - User: ' . ($current_user['user_info']['name'] ?? 'Unknown') . ' - ' . date('Y-m-d H:i:s');
                log_message('debug', '🆘 Using emergency default address for logged user: ' . $full_address);
            }

        } else {
            log_message('debug', '🏠 Processing address for GUEST user');

            // สำหรับ guest user - ใช้ข้อมูลจากฟอร์ม
            $additional_address = $this->input->post('esv_additional_address');
            $province = $this->input->post('esv_province');
            $amphoe = $this->input->post('esv_amphoe');
            $district = $this->input->post('esv_district');

            log_message('debug', '📍 Guest address components: ' . json_encode([
                'additional_address' => $additional_address,
                'province' => $province,
                'amphoe' => $amphoe,
                'district' => $district
            ]));

            if (!empty($additional_address)) {
                $address_parts = [$additional_address];
                if (!empty($district))
                    $address_parts[] = 'ตำบล' . $district;
                if (!empty($amphoe))
                    $address_parts[] = 'อำเภอ' . $amphoe;
                if (!empty($province))
                    $address_parts[] = 'จังหวัด' . $province;

                $full_address = implode(' ', $address_parts);
            }

            // Emergency default สำหรับ guest
            if (empty($full_address)) {
                $full_address = 'ที่อยู่ไม่ระบุ - Guest User - ' . date('Y-m-d H:i:s');
                log_message('debug', '🆘 Using emergency default address for guest: ' . $full_address);
            }
        }

        // *** VALIDATION: ตรวจสอบความยาวขั้นต่ำ ***
        if (strlen($full_address) < 10) {
            log_message('debug', '⚠️ Address too short, padding with additional info...');
            $full_address .= ' (ESV System)';
        }

        log_message('debug', '🏠 Final fullAddress result: ' . $full_address);
        log_message('debug', '🏠 Address length: ' . strlen($full_address));
        log_message('debug', '🏠 === ESV ADDRESS BUILDING END ===');

        return $full_address;
    }


    /**
     * *** เพิ่ม: ฟังก์ชันตรวจสอบว่ามี method ที่จำเป็นหรือไม่ ***
     * ฟังก์ชันนี้จะตรวจสอบว่า method ต่างๆ ที่เรียกใช้มีอยู่จริงหรือไม่
     */
    private function check_required_methods()
    {
        $required_methods = [
            'get_current_user_detailed',
            'generate_reference_id',
            'handle_multiple_file_upload_fixed',
            'save_multiple_file_records',
            'save_initial_history',
            'create_document_notifications',
            'cleanup_uploaded_files',
            'get_departments',
            'get_document_types',
            'get_categories',
            'prepare_navbar_data_safe'
        ];

        $missing_methods = [];

        foreach ($required_methods as $method) {
            if (!method_exists($this, $method)) {
                $missing_methods[] = $method;
            }
        }

        if (!empty($missing_methods)) {
            log_message('error', 'ESV Controller: Missing required methods: ' . implode(', ', $missing_methods));
            return false;
        }

        return true;
    }

    /**
     * *** เพิ่ม: ฟังก์ชันตรวจสอบว่ามี Model ที่จำเป็นหรือไม่ ***
     */
    private function check_required_models()
    {
        if (!isset($this->esv_model)) {
            log_message('error', 'ESV Controller: esv_model not loaded');
            return false;
        }

        // ตรวจสอบ method ใน model
        $required_model_methods = ['add_document'];

        foreach ($required_model_methods as $method) {
            if (!method_exists($this->esv_model, $method)) {
                log_message('error', 'ESV Model: Missing required method: ' . $method);
                return false;
            }
        }

        return true;
    }

    /**
     * *** เพิ่ม: ฟังก์ชัน Debug Controller State ***
     * ใช้สำหรับ debug ว่า Controller มี method และ library ที่จำเป็นหรือไม่
     */
    public function debug_controller_state()
    {
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }

        echo "<h2>ESV Controller Debug Information</h2>";

        echo "<h3>Required Methods Check:</h3>";
        if ($this->check_required_methods()) {
            echo "<p style='color: green;'>✅ All required methods exist</p>";
        } else {
            echo "<p style='color: red;'>❌ Some required methods are missing (check logs)</p>";
        }

        echo "<h3>Required Models Check:</h3>";
        if ($this->check_required_models()) {
            echo "<p style='color: green;'>✅ All required models and methods exist</p>";
        } else {
            echo "<p style='color: red;'>❌ Some required models/methods are missing (check logs)</p>";
        }

        echo "<h3>Libraries Check:</h3>";

        // ตรวจสอบ reCAPTCHA library
        if (isset($this->recaptcha_lib)) {
            echo "<p style='color: green;'>✅ reCAPTCHA Library loaded</p>";
        } else {
            echo "<p style='color: red;'>❌ reCAPTCHA Library not loaded</p>";
        }

        // ตรวจสอบ database
        if (isset($this->db)) {
            echo "<p style='color: green;'>✅ Database loaded</p>";
        } else {
            echo "<p style='color: red;'>❌ Database not loaded</p>";
        }

        echo "<h3>Session Information:</h3>";
        echo "<pre>";
        echo "Session Data:\n";
        print_r($this->session->all_userdata());
        echo "</pre>";

        echo "<h3>Environment:</h3>";
        echo "<p>Environment: " . ENVIRONMENT . "</p>";
        echo "<p>Base URL: " . base_url() . "</p>";
    }


    //////////////////////////////////////////////////////////////////////////////

    private function handle_multiple_file_upload_fixed($reference_id, $document_id)
    {
        try {
            log_message('info', 'Starting file upload process');

            // สร้าง upload directory
            $upload_path = './docs/esv_files/';
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', 'Cannot create upload directory: ' . $upload_path);
                    return false;
                }
            }

            // ตรวจสอบว่ามีไฟล์หรือไม่
            if (empty($_FILES['esv_ods_file']['name'][0])) {
                log_message('error', 'No files uploaded');
                return false;
            }

            $files_info = [];
            $file_count = count($_FILES['esv_ods_file']['name']);
            $max_files = 5;
            $max_total_size = 15 * 1024 * 1024; // 15MB
            $max_individual_size = 5 * 1024 * 1024; // 5MB per file
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

            log_message('info', "Processing {$file_count} files");

            // ตรวจสอบจำนวนไฟล์
            if ($file_count > $max_files) {
                throw new Exception("Too many files. Maximum {$max_files} files allowed");
            }

            $total_size = 0;

            for ($i = 0; $i < $file_count; $i++) {
                // ข้ามไฟล์ว่าง
                if (empty($_FILES['esv_ods_file']['name'][$i])) {
                    continue;
                }

                // ตรวจสอบ error
                if ($_FILES['esv_ods_file']['error'][$i] !== UPLOAD_ERR_OK) {
                    log_message('error', "File upload error for file {$i}: " . $_FILES['esv_ods_file']['error'][$i]);
                    continue;
                }

                $file_name = $_FILES['esv_ods_file']['name'][$i];
                $file_tmp = $_FILES['esv_ods_file']['tmp_name'][$i];
                $file_size = $_FILES['esv_ods_file']['size'][$i];
                $file_type = $_FILES['esv_ods_file']['type'][$i];

                // ตรวจสอบขนาดไฟล์
                if ($file_size > $max_individual_size) {
                    log_message('error', "File too large: {$file_name} ({$file_size} bytes)");
                    continue;
                }

                $total_size += $file_size;
                if ($total_size > $max_total_size) {
                    log_message('error', 'Total file size exceeds limit');
                    break;
                }

                // ตรวจสอบนามสกุลไฟล์
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_types)) {
                    log_message('error', "Invalid file type: {$file_extension}");
                    continue;
                }

                // สร้างชื่อไฟล์ใหม่
                $new_filename = $reference_id . '_' . ($i + 1) . '_' . time() . '.' . $file_extension;
                $target_path = $upload_path . $new_filename;

                // อัปโหลดไฟล์
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $files_info[] = [
                        'file_name' => $new_filename,
                        'original_name' => $file_name,
                        'file_type' => $file_type,
                        'file_size' => $file_size,
                        'file_path' => $target_path,
                        'file_order' => $i + 1,
                        'file_extension' => $file_extension
                    ];

                    log_message('info', "File uploaded successfully: {$file_name} -> {$new_filename}");
                } else {
                    log_message('error', "Failed to move uploaded file: {$file_name}");
                }
            }

            if (empty($files_info)) {
                log_message('error', 'No files were successfully uploaded');
                return false;
            }

            log_message('info', "Successfully uploaded " . count($files_info) . " files");

            return [
                'files' => $files_info,
                'total_files' => count($files_info),
                'total_size' => $total_size
            ];

        } catch (Exception $e) {
            log_message('error', 'File upload exception: ' . $e->getMessage());
            return false;
        }
    }




    /**
     * บันทึกข้อมูลไฟล์หลายไฟล์ใน tbl_esv_files
     */


    public function download_file($file_id)
    {
        try {
            // ตรวจสอบสิทธิ์ (สามารถปรับแต่งตามต้องการ)
            $this->load->helper('download');

            // ดึงข้อมูลไฟล์
            $this->db->select('f.*, d.esv_ods_reference_id, d.esv_ods_by');
            $this->db->from('tbl_esv_files f');
            $this->db->join('tbl_esv_ods d', 'f.esv_file_esv_ods_id = d.esv_ods_id');
            $this->db->where('f.esv_file_id', $file_id);
            $this->db->where('f.esv_file_status', 'active');
            $file = $this->db->get()->row();

            if (!$file) {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file->esv_file_path)) {
                show_error('ไม่พบไฟล์ที่ต้องการ', 404);
                return;
            }

            // อัปเดตจำนวนการดาวน์โหลด
            $this->db->where('esv_file_id', $file_id);
            $this->db->set('esv_file_download_count', 'esv_file_download_count + 1', FALSE);
            $this->db->update('tbl_esv_files');

            // ดาวน์โหลดไฟล์
            $data = file_get_contents($file->esv_file_path);
            force_download($file->esv_file_original_name, $data);

        } catch (Exception $e) {
            log_message('error', 'Error downloading file: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการดาวน์โหลดไฟล์', 500);
        }
    }

    /**
     * ลบไฟล์ (สำหรับ Admin)
     */
    public function delete_file()
    {
        // ตรวจสอบการเข้าสู่ระบบและสิทธิ์
        $m_id = $this->session->userdata('m_id');
        if (!$m_id) {
            $this->json_response(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
            return;
        }

        try {
            $file_id = $this->input->post('file_id');

            if (empty($file_id)) {
                $this->json_response(['success' => false, 'message' => 'ไม่พบรหัสไฟล์']);
                return;
            }

            // ดึงข้อมูลไฟล์
            $this->db->where('esv_file_id', $file_id);
            $file = $this->db->get('tbl_esv_files')->row();

            if (!$file) {
                $this->json_response(['success' => false, 'message' => 'ไม่พบไฟล์']);
                return;
            }

            // Soft delete
            $this->db->where('esv_file_id', $file_id);
            $result = $this->db->update('tbl_esv_files', [
                'esv_file_status' => 'deleted',
                'esv_file_deleted_at' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                // ลบไฟล์จริงจากระบบ (ถ้าต้องการ)
                if (file_exists($file->esv_file_path)) {
                    unlink($file->esv_file_path);
                }

                $this->json_response([
                    'success' => true,
                    'message' => 'ลบไฟล์สำเร็จ',
                    'file_name' => $file->esv_file_original_name
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถลบไฟล์ได้']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error deleting file: ' . $e->getMessage());
            $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }
    }





    private function get_current_user_simple()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_id' => null,
            'name' => 'Guest User',
            'email' => '',
            'phone' => '',
            'address' => ''
        ];

        try {
            // Check public user
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!empty($mp_id) && !empty($mp_email)) {
                $this->db->select('id, mp_fname, mp_lname, mp_email, mp_phone, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode');
                $this->db->where('mp_id', $mp_id);
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $user_data = $this->db->get('tbl_member_public')->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_id'] = $user_data->id;
                    $user_info['name'] = trim($user_data->mp_fname . ' ' . $user_data->mp_lname);
                    $user_info['email'] = $user_data->mp_email;
                    $user_info['phone'] = $user_data->mp_phone;
                    $user_info['address'] = $user_data->mp_address . ' ' . $user_data->mp_district . ' ' . $user_data->mp_amphoe . ' ' . $user_data->mp_province . ' ' . $user_data->mp_zipcode;


                    return $user_info;
                }
            }

            // Check staff user
            $m_id = $this->session->userdata('m_id');
            $m_email = $this->session->userdata('m_email');

            if (!empty($m_id) && !empty($m_email)) {
                $this->db->select('m_id, m_fname, m_lname, m_email, m_phone');
                $this->db->where('m_id', $m_id);
                // $this->db->where('m_email', $m_email);
                $this->db->where('m_status', '1');
                $user_data = $this->db->get('tbl_member')->row();

                if ($user_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_id'] = $user_data->m_id;
                    $user_info['name'] = trim($user_data->m_fname . ' ' . $user_data->m_lname);
                    $user_info['email'] = $user_data->m_email;
                    $user_info['phone'] = $user_data->m_phone;
                    $user_info['address'] = 'เจ้าหน้าที่';

                    return $user_info;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting current user: ' . $e->getMessage());
        }

        // If not logged in, get from POST data
        if (!$user_info['is_logged_in']) {
            $user_info['name'] = $this->input->post('esv_ods_by') ?: 'Guest User';
            $user_info['email'] = $this->input->post('esv_ods_email') ?: 'guest@example.com';
            $user_info['phone'] = $this->input->post('esv_ods_phone') ?: '0000000000';
            $user_info['address'] = $this->input->post('esv_additional_address') ?: 'ไม่ระบุ';
        }

        return $user_info;
    }

    /**
     * Handle file upload (simplified)
     */
    private function handle_simple_file_upload($reference_id, $document_id)
    {
        try {
            $upload_path = './docs/esv_files/';

            // Create directory if not exists
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            if (empty($_FILES['esv_ods_file']['name'][0])) {
                return false;
            }

            // Take only the first file for simplicity
            $file = [
                'name' => $_FILES['esv_ods_file']['name'][0],
                'type' => $_FILES['esv_ods_file']['type'][0],
                'tmp_name' => $_FILES['esv_ods_file']['tmp_name'][0],
                'error' => $_FILES['esv_ods_file']['error'][0],
                'size' => $_FILES['esv_ods_file']['size'][0]
            ];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                log_message('error', 'File upload error: ' . $file['error']);
                return false;
            }

            // Generate safe filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safe_filename = $reference_id . '_' . time() . '.' . $extension;
            $target_path = $upload_path . $safe_filename;

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                log_message('info', 'File uploaded successfully: ' . $safe_filename);

                // Save file record
                $file_data = [
                    'esv_file_esv_ods_id' => $document_id,
                    'esv_file_name' => $safe_filename,
                    'esv_file_original_name' => $file['name'],
                    'esv_file_path' => $target_path,
                    'esv_file_size' => $file['size'],
                    'esv_file_type' => $file['type'],
                    'esv_file_extension' => $extension,
                    'esv_file_is_main' => 1,
                    'esv_file_status' => 'active',
                    'esv_file_uploaded_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_esv_files', $file_data);

                return [
                    'file_name' => $safe_filename,
                    'original_name' => $file['name'],
                    'size' => $file['size']
                ];
            } else {
                log_message('error', 'Failed to move uploaded file');
                return false;
            }

        } catch (Exception $e) {
            log_message('error', 'File upload exception: ' . $e->getMessage());
            return false;
        }
    }





    private function send_json_response($data, $exit = true)
    {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Set headers
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // Validate data
        if (!is_array($data)) {
            $data = ['success' => false, 'message' => 'Invalid response data'];
        }

        // Add debug info in development
        if (ENVIRONMENT === 'development' && !isset($data['debug'])) {
            $data['debug'] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'memory_usage' => memory_get_usage(true),
                'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
            ];
        }

        // Send response
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if ($exit) {
            exit();
        }
    }




    private function handle_multiple_file_upload($reference_id)
    {
        $this->load->library('upload');

        $upload_path = './docs/esv_files/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|pdf|doc|docx',
            'max_size' => 5120, // 5MB per file
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE
        ];

        $this->upload->initialize($config);

        $files_info = [];
        $total_size = 0;
        $max_total_size = 15 * 1024 * 1024; // 15MB

        // ตรวจสอบว่ามีไฟล์หรือไม่
        if (empty($_FILES['esv_ods_file']['name'][0])) {
            log_message('error', 'No files uploaded');
            return false;
        }

        $file_count = count($_FILES['esv_ods_file']['name']);

        // ตรวจสอบจำนวนไฟล์
        if ($file_count > 5) {
            log_message('error', 'Too many files: ' . $file_count);
            return false;
        }

        for ($i = 0; $i < $file_count; $i++) {
            // ข้ามไฟล์ว่าง
            if (empty($_FILES['esv_ods_file']['name'][$i])) {
                continue;
            }

            // ตรวจสอบ error
            if ($_FILES['esv_ods_file']['error'][$i] !== UPLOAD_ERR_OK) {
                log_message('error', 'File upload error for file ' . $i . ': ' . $_FILES['esv_ods_file']['error'][$i]);
                continue;
            }

            // ตรวจสอบขนาดไฟล์
            $file_size = $_FILES['esv_ods_file']['size'][$i];
            if ($file_size > 5 * 1024 * 1024) { // 5MB per file
                log_message('error', 'File too large: ' . $_FILES['esv_ods_file']['name'][$i]);
                continue;
            }

            $total_size += $file_size;
            if ($total_size > $max_total_size) {
                log_message('error', 'Total file size exceeds limit');
                break;
            }

            // เตรียม $_FILES สำหรับ upload แต่ละไฟล์
            $_FILES['temp_file'] = [
                'name' => $_FILES['esv_ods_file']['name'][$i],
                'type' => $_FILES['esv_ods_file']['type'][$i],
                'tmp_name' => $_FILES['esv_ods_file']['tmp_name'][$i],
                'error' => $_FILES['esv_ods_file']['error'][$i],
                'size' => $_FILES['esv_ods_file']['size'][$i]
            ];

            if ($this->upload->do_upload('temp_file')) {
                $upload_data = $this->upload->data();

                $files_info[] = [
                    'file_name' => $upload_data['file_name'],
                    'original_name' => $upload_data['orig_name'],
                    'file_type' => $upload_data['file_type'],
                    'file_size' => $upload_data['file_size'] * 1024,
                    'file_path' => 'docs/esv_forms/' . $upload_data['file_name'],
                    'file_order' => $i + 1
                ];

                log_message('info', "File uploaded for document {$reference_id}: {$upload_data['orig_name']}");
            } else {
                $upload_errors = $this->upload->display_errors('', '');
                log_message('error', "File upload failed for file {$i}: {$upload_errors}");
            }
        }

        return empty($files_info) ? false : $files_info;
    }





    private function save_multiple_file_records($document_id, $files_info)
    {
        try {
            log_message('info', 'Saving file records for document: ' . $document_id);

            if (empty($files_info) || !is_array($files_info)) {
                log_message('error', 'No file info provided');
                return false;
            }

            foreach ($files_info as $index => $file_info) {
                $file_data = [
                    'esv_file_esv_ods_id' => $document_id,
                    'esv_file_name' => $file_info['file_name'],
                    'esv_file_original_name' => $file_info['original_name'],
                    'esv_file_path' => $file_info['file_path'],
                    'esv_file_size' => $file_info['file_size'],
                    'esv_file_type' => $file_info['file_type'],
                    'esv_file_extension' => $file_info['file_extension'],
                    'esv_file_is_main' => ($index === 0) ? 1 : 0, // ไฟล์แรกเป็น main
                    'esv_file_order' => $file_info['file_order'],
                    'esv_file_status' => 'active',
                    'esv_file_uploaded_at' => date('Y-m-d H:i:s')
                ];

                $result = $this->db->insert('tbl_esv_files', $file_data);

                if (!$result) {
                    log_message('error', 'Failed to insert file record: ' . json_encode($file_data));
                    return false;
                }

                log_message('info', "File record saved: {$file_info['original_name']}");
            }

            log_message('info', 'All file records saved successfully');
            return true;

        } catch (Exception $e) {
            log_message('error', 'Error saving multiple file records: ' . $e->getMessage());
            return false;
        }
    }





    private function save_initial_history($document_id, $document_data, $current_user)
    {
        try {
            if (!$this->db->table_exists('tbl_esv_history')) {
                return;
            }

            $history_data = [
                'esv_history_esv_ods_id' => $document_id,
                'esv_history_action' => 'created',
                'esv_history_old_status' => null,
                'esv_history_new_status' => 'pending',
                'esv_history_description' => 'เอกสารได้ถูกสร้างและยื่นเรียบร้อยแล้ว',
                'esv_history_by' => $document_data['esv_ods_by'],
                'esv_history_created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_esv_history', $history_data);

        } catch (Exception $e) {
            log_message('error', 'Error saving initial history: ' . $e->getMessage());
        }
    }
    /**
     * ล้าง form draft
     */
    private function clear_form_draft($current_user)
    {
        // สำหรับ guest user จะล้าง localStorage ใน frontend
        // สำหรับ logged-in user อาจจะมี session draft ใน database
    }






    // ===================================================================
    // *** API Functions ***
    // ===================================================================

    /**
     * AJAX: ดึงหมวดหมู่ตามแผนก
     */
    public function get_all_categories()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $categories = $this->esv_model->get_all_categories();
            echo json_encode($categories, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            log_message('error', 'Error in get_all_categories: ' . $e->getMessage());
            echo json_encode([], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * แก้ไข get_categories_by_department ใน Controller
     */
    public function get_categories_by_department()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $department_id = $this->input->post('department_id');

            if (empty($department_id) || $department_id === 'other') {
                // ถ้าไม่มีแผนก ให้ส่งหมวดหมู่ทั้งหมด
                $categories = $this->esv_model->get_all_categories();
                echo json_encode($categories, JSON_UNESCAPED_UNICODE);
                exit;
            }

            $categories = $this->esv_model->get_categories_by_department($department_id);

            echo json_encode($categories, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in get_categories_by_department: ' . $e->getMessage());
            echo json_encode([], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * AJAX: ดึงข้อมูลหมวดหมู่
     */
    public function get_category_info()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $category_id = $this->input->post('category_id');

            if (empty($category_id) || $category_id === 'other') {
                echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $category_info = $this->esv_model->get_category_info($category_id);

            if ($category_info) {
                echo json_encode([
                    'success' => true,
                    'data' => $category_info
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in get_category_info: ' . $e->getMessage());
            echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * หน้าติดตามสถานะเอกสาร - เพิ่ม reCAPTCHA Integration
     */
    public function track()
    {
        log_message('info', '=== ESV TRACK START ===');

        try {
            // === ขั้นตอนที่ 1: จัดการ POST Request (การค้นหาด้วย reCAPTCHA) ===
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return $this->handleEsvTrackPost();
            }

            // === ขั้นตอนที่ 2: จัดการ GET Request (แสดงผลลัพธ์) ===
            return $this->handleEsvTrackGet();

        } catch (Exception $e) {
            log_message('error', 'Error in track: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    /**
     * จัดการ POST Request สำหรับการค้นหาพร้อม reCAPTCHA
     */
    private function handleEsvTrackPost()
    {
        log_message('info', 'Handling POST search request for ESV track');

        $tracking_code = $this->input->post('tracking_code');
        $recaptcha_token = $this->input->post('g-recaptcha-response');
        $recaptcha_action = $this->input->post('recaptcha_action') ?: 'esv_track_search';
        $recaptcha_source = $this->input->post('recaptcha_source') ?: 'esv_track_form';

        // ตรวจสอบข้อมูลพื้นฐาน
        if (empty($tracking_code)) {
            $this->session->set_flashdata('error_message', 'กรุณากรอกรหัสติดตาม');
            redirect('Esv_ods/track');
            return;
        }

        // *** เพิ่ม: ตรวจสอบ reCAPTCHA Token (หากมี) ***
        if (!empty($recaptcha_token)) {
            // มี reCAPTCHA token ให้ทำการ verify
            if (!$this->verifyEsvTrackRecaptcha($recaptcha_token, $recaptcha_action, $recaptcha_source)) {
                $this->session->set_flashdata('error_message', 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่');
                redirect('Esv_ods/track');
                return;
            }
            log_message('info', 'reCAPTCHA verification successful for ESV track');
        } else {
            // ไม่มี reCAPTCHA token - อนุญาตให้ดำเนินการต่อ (fallback)
            log_message('info', 'ESV track search without reCAPTCHA token - fallback mode');
        }

        // หลังจากตรวจสอบแล้ว redirect ไป GET พร้อม tracking code
        redirect('Esv_ods/track?code=' . urlencode($tracking_code));
    }

    /**
     * จัดการ GET Request สำหรับแสดงผลลัพธ์ (เดิม + เพิ่มเติม)
     */
    private function handleEsvTrackGet()
    {
        log_message('info', 'Handling GET display request for ESV track');

        $data = $this->prepare_navbar_data_safe();

        // ตรวจสอบว่ามี tracking code ที่ส่งมาจาก URL หรือไม่
        $tracking_code = $this->input->get('code') ?: '';
        $data['tracking_code'] = $tracking_code;
        $data['search_performed'] = false;
        $data['esv_document_info'] = null;
        $data['error_message'] = '';

        // ถ้ามี tracking code ให้ทำการค้นหาทันที
        if (!empty($tracking_code)) {
            $search_result = $this->perform_document_search($tracking_code);
            $data = array_merge($data, $search_result);
        }

        $data['page_title'] = 'ติดตามสถานะเอกสารออนไลน์';
        $data['breadcrumb'] = [
            ['title' => 'หน้าแรก', 'url' => base_url()],
            ['title' => 'บริการประชาชน', 'url' => '#'],
            ['title' => 'ติดตามสถานะเอกสาร', 'url' => '']
        ];

        // Flash Messages
        $data['success_message'] = $this->session->flashdata('success_message');
        $data['error_message'] = $data['error_message'] ?: $this->session->flashdata('error_message');
        $data['info_message'] = $this->session->flashdata('info_message');
        $data['warning_message'] = $this->session->flashdata('warning_message');

        // *** สำคัญ: แก้ไขการโหลด view เพื่อป้องกัน JavaScript error ***
        // โหลด view โดยไม่โหลด JS ที่ขัดแย้ง
        $this->load->view('frontend_templat/header', $data);
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/esv_track_status', $data);
        // ไม่โหลด frontend_asset/js เพื่อป้องกัน conflict
        $this->load->view('frontend_templat/footer', $data);

        log_message('info', '=== ESV TRACK END ===');
    }


    /**
     * AJAX: ค้นหาเอกสารด้วย tracking code - ปรับปรุงเพิ่ม reCAPTCHA
     */
    public function search_document()
    {
        log_message('info', 'ESV track AJAX search request');

        // ตั้งค่า header (เดิม)
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $tracking_code = $this->input->post('tracking_code');
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'esv_track_search';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'esv_track_form';

            if (empty($tracking_code)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาระบุรหัสติดตาม'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** เพิ่ม: ตรวจสอบ reCAPTCHA Token สำหรับ AJAX (หากมี) ***
            if (!empty($recaptcha_token)) {
                if (!$this->verifyEsvTrackRecaptcha($recaptcha_token, $recaptcha_action, $recaptcha_source)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                log_message('info', 'reCAPTCHA verification successful for ESV track AJAX');
            } else {
                log_message('info', 'ESV track AJAX search without reCAPTCHA token - fallback mode');
            }

            // ทำการค้นหา (ใช้ method ที่มีอยู่แล้ว)
            $search_result = $this->perform_document_search_safe($tracking_code);

            if ($search_result['search_performed'] && $search_result['esv_document_info']) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'document' => $search_result['esv_document_info'],
                        'files' => $search_result['esv_document_info']->files ?? [],
                        'history' => $search_result['esv_document_info']->history ?? []
                    ],
                    'message' => 'พบข้อมูลเอกสาร'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $search_result['error_message'] ?: 'ไม่พบเอกสารที่ต้องการ'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            log_message('error', 'Error in search_document: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * ตรวจสอบ reCAPTCHA สำหรับ ESV Track
     */
    private function verifyEsvTrackRecaptcha($recaptcha_token, $recaptcha_action, $recaptcha_source)
    {
        try {
            // ตรวจสอบว่ามี reCAPTCHA library หรือไม่
            if (!isset($this->recaptcha_lib)) {
                log_message('debug', 'reCAPTCHA library not loaded for ESV track');
                return true; // อนุญาตให้ดำเนินการต่อ
            }

            // เตรียม options สำหรับ reCAPTCHA
            $recaptcha_options = [
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => 'guest'
            ];

            // ส่งไปยัง reCAPTCHA Library
            $recaptcha_result = $this->recaptcha_lib->verify(
                $recaptcha_token,
                'citizen',
                null,
                $recaptcha_options
            );

            if (!$recaptcha_result['success']) {
                log_message('error', 'reCAPTCHA verification failed for ESV track: ' . $recaptcha_result['message']);
                return false;
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'Error in verifyEsvTrackRecaptcha: ' . $e->getMessage());
            // กรณีเกิด error ให้อนุญาตดำเนินการต่อ (graceful fallback)
            return true;
        }
    }



    /**
     * ฟังก์ชันค้นหาเอกสาร (คงเดิม - ไม่แก้ไข)
     */
    private function perform_document_search($tracking_code)
    {
        $result = [
            'search_performed' => true,
            'esv_document_info' => null,
            'tracking_code' => $tracking_code,
            'error_message' => ''
        ];

        try {
            // ตรวจสอบรูปแบบ tracking code
            if (!preg_match('/^ESV\d+$/', $tracking_code)) {
                $result['error_message'] = 'รูปแบบรหัสติดตามไม่ถูกต้อง กรุณาใช้รูปแบบ ESV ตามด้วยตัวเลข';
                return $result;
            }

            // ค้นหาเอกสารในฐานข้อมูล
            $this->db->select('e.*, p.pname as department_name, c.esv_category_name, t.esv_type_name');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');
            $this->db->join('tbl_esv_category c', 'e.esv_ods_category_id = c.esv_category_id', 'left');
            $this->db->join('tbl_esv_type t', 'e.esv_ods_type_id = t.esv_type_id', 'left');
            $this->db->where('e.esv_ods_reference_id', $tracking_code);

            $query = $this->db->get();
            $document = $query->row();

            if (!$document) {
                $result['error_message'] = 'ไม่พบรหัสติดตามในระบบ';
                return $result;
            }

            // ตรวจสอบว่าเป็นเอกสารของ Guest User หรือไม่
            if ($document->esv_ods_user_type !== 'guest') {
                if ($document->esv_ods_user_type === 'public') {
                    $result['error_message'] = 'เอกสารนี้เป็นของสมาชิก กรุณาเข้าสู่ระบบเพื่อดูข้อมูล';
                } elseif ($document->esv_ods_user_type === 'staff') {
                    $result['error_message'] = 'เอกสารนี้เป็นของเจ้าหน้าที่ ไม่สามารถดูได้จากหน้านี้';
                } else {
                    $result['error_message'] = 'ไม่สามารถเข้าถึงเอกสารนี้ได้';
                }
                return $result;
            }

            // ดึงไฟล์แนบ
            if (method_exists($this->esv_model, 'get_document_files')) {
                $document->files = $this->esv_model->get_document_files($document->esv_ods_id);
            } else {
                $document->files = [];
            }

            // ดึงประวัติการดำเนินการ
            if (method_exists($this->esv_model, 'get_document_history')) {
                $document->history = $this->esv_model->get_document_history($document->esv_ods_id);
            } else {
                $document->history = [];
            }

            // เพิ่มข้อมูลเสริม
            $document->file_count = count($document->files);

            if (method_exists($this->esv_model, 'get_main_file')) {
                $document->main_file = $this->esv_model->get_main_file($document->esv_ods_id);
            }

            // บันทึกการดู (เพิ่มจำนวนครั้งที่ดู)
            $this->db->where('esv_ods_id', $document->esv_ods_id);
            $this->db->set('esv_ods_viewed_count', 'esv_ods_viewed_count + 1', FALSE);
            $this->db->set('esv_ods_last_viewed', date('Y-m-d H:i:s'));
            $this->db->update('tbl_esv_ods');

            $result['esv_document_info'] = $document;

            log_message('info', "Document tracked: {$tracking_code} by Guest User");

        } catch (Exception $e) {
            log_message('error', 'Error in perform_document_search: ' . $e->getMessage());
            $result['error_message'] = 'เกิดข้อผิดพลาดในการค้นหา';
        }

        return $result;
    }

    ////////////////////////////////////////////////////




    private function prepare_navbar_data_safe()
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
            // โหลดข้อมูลที่จำเป็นสำหรับ navbar อย่างปลอดภัย
            if (isset($this->activity_model) && method_exists($this->activity_model, 'activity_frontend')) {
                $result = $this->activity_model->activity_frontend();
                $data['qActivity'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->HotNews_model) && method_exists($this->HotNews_model, 'hotnews_frontend')) {
                $result = $this->HotNews_model->hotnews_frontend();
                $data['qHotnews'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->Weather_report_model) && method_exists($this->Weather_report_model, 'weather_reports_frontend')) {
                $result = $this->Weather_report_model->weather_reports_frontend();
                $data['qWeather'] = (is_array($result) || is_object($result)) ? $result : [];
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }






    private function perform_document_search_safe($tracking_code)
    {
        $result = [
            'search_performed' => true,
            'esv_document_info' => null,
            'tracking_code' => $tracking_code,
            'error_message' => ''
        ];

        try {
            // ตรวจสอบรูปแบบ tracking code
            if (!preg_match('/^ESV\d+$/', $tracking_code)) {
                $result['error_message'] = 'รูปแบบรหัสติดตามไม่ถูกต้อง กรุณาใช้รูปแบบ ESV ตามด้วยตัวเลข';
                return $result;
            }

            // ค้นหาเอกสารในฐานข้อมูลโดยตรง
            $this->db->select('e.*, p.pname as department_name');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');
            $this->db->where('e.esv_ods_reference_id', $tracking_code);

            $query = $this->db->get();
            $document = $query->row();

            if (!$document) {
                $result['error_message'] = 'ไม่พบรหัสติดตามในระบบ';
                return $result;
            }

            // ตรวจสอบว่าเป็นเอกสารของ Guest User หรือไม่
            if ($document->esv_ods_user_type !== 'guest') {
                if ($document->esv_ods_user_type === 'public') {
                    $result['error_message'] = 'เอกสารนี้เป็นของสมาชิก กรุณาเข้าสู่ระบบเพื่อดูข้อมูล';
                } elseif ($document->esv_ods_user_type === 'staff') {
                    $result['error_message'] = 'เอกสารนี้เป็นของเจ้าหน้าที่ ไม่สามารถดูได้จากหน้านี้';
                } else {
                    $result['error_message'] = 'ไม่สามารถเข้าถึงเอกสารนี้ได้';
                }
                return $result;
            }

            // ดึงไฟล์แนบโดยตรง
            $document->files = $this->get_document_files_safe($document->esv_ods_id);

            // ดึงประวัติการดำเนินการโดยตรง
            $document->history = $this->get_document_history_safe($document->esv_ods_id);

            // เพิ่มข้อมูลเสริม
            $document->file_count = count($document->files);
            $document->main_file = $this->get_main_file_safe($document->esv_ods_id);

            // บันทึกการดู (เพิ่มจำนวนครั้งที่ดู)
            $this->db->where('esv_ods_id', $document->esv_ods_id);
            $this->db->set('esv_ods_viewed_count', 'esv_ods_viewed_count + 1', FALSE);
            $this->db->set('esv_ods_last_viewed', date('Y-m-d H:i:s'));
            $this->db->update('tbl_esv_ods');

            $result['esv_document_info'] = $document;

            log_message('info', "Document tracked: {$tracking_code} by Guest User");

        } catch (Exception $e) {
            log_message('error', 'Error in perform_document_search_safe: ' . $e->getMessage());
            $result['error_message'] = 'เกิดข้อผิดพลาดในการค้นหา';
        }

        return $result;
    }

    private function get_document_history_safe($document_id)
    {
        try {
            if (!$this->db->table_exists('tbl_esv_history')) {
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_esv_history');
            $this->db->where('esv_history_esv_ods_id', $document_id);
            $this->db->order_by('esv_history_created_at', 'DESC');

            $query = $this->db->get();

            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
                log_message('error', 'Database error in get_document_history_safe: ' . $db_error['message']);
                return [];
            }

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting document history: ' . $e->getMessage());
            return [];
        }
    }







    private function get_main_file_safe($document_id)
    {
        try {
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

            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
                log_message('error', 'Database error in get_main_file_safe: ' . $db_error['message']);
                return null;
            }

            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Error getting main file: ' . $e->getMessage());
            return null;
        }
    }



    private function get_document_files_safe($document_id)
    {
        try {
            if (!$this->db->table_exists('tbl_esv_files')) {
                return [];
            }

            $this->db->select('*');
            $this->db->from('tbl_esv_files');
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->where('esv_file_status', 'active');
            $this->db->order_by('esv_file_order', 'ASC');
            $this->db->order_by('esv_file_uploaded_at', 'ASC');

            $query = $this->db->get();

            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
                log_message('error', 'Database error in get_document_files_safe: ' . $db_error['message']);
                return [];
            }

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting document files: ' . $e->getMessage());
            return [];
        }
    }










    public function my_documents()
    {
        try {
            log_message('info', '=== MY DOCUMENTS START ===');

            // ตรวจสอบการ login ของสมาชิก
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            log_message('info', "Session Check - mp_id: $mp_id, mp_email: $mp_email");

            if (!$mp_id || !$mp_email) {
                log_message('debug', 'User not logged in - redirecting to login');
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบเพื่อดูเอกสารของคุณ');
                redirect('User');
                return;
            }

            // ตรวจสอบข้อมูลสมาชิก
            $this->db->select('id, mp_id, mp_fname, mp_lname, mp_prefix, mp_email, mp_phone, mp_status, mp_img');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $member_check = $this->db->get()->row();

            log_message('info', 'Member check result: ' . json_encode($member_check, JSON_UNESCAPED_UNICODE));

            if (!$member_check) {
                log_message('error', 'Member not found or inactive');
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน');
                redirect('User');
                return;
            }

            log_message('info', 'Member login successful: ' . $member_check->mp_fname . ' ' . $member_check->mp_lname);

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data_safe();

            // ตัวกรองข้อมูลจาก URL parameters
            $filters = [
                'status' => $this->input->get('status'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search'),
                'category' => $this->input->get('category'),
                'department' => $this->input->get('department')
            ];

            log_message('info', 'Applied filters: ' . json_encode($filters, JSON_UNESCAPED_UNICODE));

            // ดึงเอกสารของสมาชิกคนนี้
            $documents = $this->get_member_documents_enhanced($member_check->id, $filters);

            log_message('info', 'Found ' . count($documents) . ' documents for member');

            // เตรียมข้อมูลเอกสารสำหรับแสดงผล
            $processed_documents = $this->process_documents_for_display($documents);

            // คำนวณสถิติเอกสาร
            $document_stats = $this->calculate_member_document_stats($documents);

            log_message('info', 'Document stats: ' . json_encode($document_stats, JSON_UNESCAPED_UNICODE));

            // เตรียมข้อมูลสมาชิก
            $member_info = [
                'id' => $member_check->id,
                'mp_id' => $member_check->mp_id,
                'name' => $this->format_member_name($member_check),
                'email' => $member_check->mp_email,
                'phone' => $member_check->mp_phone,
                'img' => $member_check->mp_img,
                'initials' => $this->generate_member_initials($member_check)
            ];

            // เตรียมตัวเลือกสำหรับ dropdown (ถ้าต้องการ)
            $filter_options = [
                'status_options' => $this->get_status_options(),
                'departments' => $this->get_departments_simple(),
                'categories' => $this->get_categories_simple()
            ];

            // รวมข้อมูลทั้งหมด
            $data = array_merge($data, [
                'documents' => $processed_documents,
                'document_stats' => $document_stats,
                'filters' => $filters,
                'filter_options' => $filter_options,
                'member_info' => $member_info,
                'is_logged_in' => true,
                'user_type' => 'public',
                'user_info' => (object) $member_info,
                'current_user' => (object) $member_info,
                'logged_user' => (object) $member_info,
                'session_user' => (object) $member_info,
                'member_data' => (object) $member_info
            ]);

            // Page metadata
            $data['page_title'] = 'เอกสารออนไลน์ของฉัน';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'เอกสารของฉัน', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            log_message('info', '=== MY DOCUMENTS END ===');

            // โหลด view ตามรูปแบบ public_user
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/esv_my_documents', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in my_documents: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('User');
            }
        }
    }


    private function get_status_display_info($status)
    {
        $status_map = [
            'pending' => [
                'display' => 'รอดำเนินการ',
                'class' => 'esv-status-pending',
                'icon' => 'fas fa-clock',
                'color' => '#d97706'
            ],
            'processing' => [
                'display' => 'กำลังดำเนินการ',
                'class' => 'esv-status-processing',
                'icon' => 'fas fa-cog fa-spin',
                'color' => '#0284c7'
            ],
            'completed' => [
                'display' => 'เสร็จสิ้น',
                'class' => 'esv-status-completed',
                'icon' => 'fas fa-check-circle',
                'color' => '#059669'
            ],
            'rejected' => [
                'display' => 'ไม่อนุมัติ',
                'class' => 'esv-status-rejected',
                'icon' => 'fas fa-times-circle',
                'color' => '#dc2626'
            ],
            'cancelled' => [
                'display' => 'ยกเลิก',
                'class' => 'esv-status-cancelled',
                'icon' => 'fas fa-ban',
                'color' => '#6b7280'
            ]
        ];

        return $status_map[$status] ?? $status_map['pending'];
    }


    private function get_status_options()
    {
        return [
            ['value' => '', 'label' => 'ทุกสถานะ'],
            ['value' => 'pending', 'label' => 'รอดำเนินการ'],
            ['value' => 'processing', 'label' => 'กำลังดำเนินการ'],
            ['value' => 'completed', 'label' => 'เสร็จสิ้น'],
            ['value' => 'rejected', 'label' => 'ไม่อนุมัติ'],
            ['value' => 'cancelled', 'label' => 'ยกเลิก']
        ];
    }


    private function get_categories_simple()
    {
        try {
            if (!$this->db->table_exists('tbl_esv_category')) {
                return [];
            }

            $this->db->select('esv_category_id, esv_category_name');
            $this->db->from('tbl_esv_category');
            $this->db->where('esv_category_status', 'active');
            $this->db->order_by('esv_category_name', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting categories simple: ' . $e->getMessage());
            return [];
        }
    }



    private function get_departments_simple()
    {
        try {
            $this->db->select('pid, pname');
            $this->db->from('tbl_position');
            $this->db->where('pstatus', 'show');
            $this->db->where('pid >=', 4);
            $this->db->order_by('pname', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting departments simple: ' . $e->getMessage());
            return [];
        }
    }




    private function generate_member_initials($member)
    {
        $initials = '';

        if (!empty($member->mp_fname)) {
            $initials .= mb_substr($member->mp_fname, 0, 1);
        }

        if (!empty($member->mp_lname)) {
            $initials .= mb_substr($member->mp_lname, 0, 1);
        }

        return !empty($initials) ? strtoupper($initials) : 'U';
    }



    private function format_member_name($member)
    {
        $name_parts = [];

        if (!empty($member->mp_prefix)) {
            $name_parts[] = $member->mp_prefix;
        }

        if (!empty($member->mp_fname)) {
            $name_parts[] = $member->mp_fname;
        }

        if (!empty($member->mp_lname)) {
            $name_parts[] = $member->mp_lname;
        }

        return !empty($name_parts) ? implode(' ', $name_parts) : 'สมาชิก';
    }






    private function calculate_member_document_stats($documents)
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'rejected' => 0,
            'cancelled' => 0
        ];

        if (!empty($documents) && is_array($documents)) {
            $stats['total'] = count($documents);

            foreach ($documents as $doc) {
                $status = $doc->esv_ods_status ?? 'pending';
                if (isset($stats[$status])) {
                    $stats[$status]++;
                }
            }
        }

        return $stats;
    }






    private function get_member_documents_enhanced($member_id, $filters = [])
    {
        try {
            log_message('info', "Getting enhanced documents for member: $member_id");

            // เริ่มต้น query
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

            // กรองเฉพาะเอกสารของสมาชิกคนนี้
            $this->db->where('e.esv_ods_user_id', $member_id);
            $this->db->where('e.esv_ods_user_type', 'public');

            // ใช้ตัวกรอง
            $this->apply_member_filters($filters);

            // เรียงลำดับ
            $this->db->order_by('e.esv_ods_datesave', 'DESC');

            $query = $this->db->get();

            log_message('info', 'Documents query: ' . $this->db->last_query());

            // ตรวจสอบ error
            $db_error = $this->db->error();
            if ($db_error['code'] !== 0) {
                log_message('error', 'Database error in get_member_documents_enhanced: ' . $db_error['message']);
                return [];
            }

            $documents = $query->result();

            // เพิ่มข้อมูลไฟล์และประวัติให้แต่ละเอกสาร
            foreach ($documents as $doc) {
                $doc->files = $this->get_document_files_safe($doc->esv_ods_id);
                $doc->history = $this->get_document_history_safe($doc->esv_ods_id);
                $doc->file_count = count($doc->files);
                $doc->main_file = $this->get_main_file_safe($doc->esv_ods_id);
            }

            log_message('info', 'Successfully retrieved ' . count($documents) . ' documents');

            return $documents;

        } catch (Exception $e) {
            log_message('error', 'Error getting member documents: ' . $e->getMessage());
            return [];
        }
    }




    private function apply_member_filters($filters)
    {
        if (!empty($filters['status'])) {
            $this->db->where('e.esv_ods_status', $filters['status']);
            log_message('info', 'Applied status filter: ' . $filters['status']);
        }

        if (!empty($filters['department'])) {
            $this->db->where('e.esv_ods_department_id', $filters['department']);
            log_message('info', 'Applied department filter: ' . $filters['department']);
        }

        if (!empty($filters['category'])) {
            $this->db->where('e.esv_ods_category_id', $filters['category']);
            log_message('info', 'Applied category filter: ' . $filters['category']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(e.esv_ods_datesave) >=', $filters['date_from']);
            log_message('info', 'Applied date_from filter: ' . $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(e.esv_ods_datesave) <=', $filters['date_to']);
            log_message('info', 'Applied date_to filter: ' . $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $this->db->escape_like_str($filters['search']);
            $this->db->group_start();
            $this->db->like('e.esv_ods_reference_id', $search);
            $this->db->or_like('e.esv_ods_topic', $search);
            $this->db->or_like('e.esv_ods_detail', $search);
            $this->db->group_end();
            log_message('info', 'Applied search filter: ' . $filters['search']);
        }
    }






    private function process_documents_for_display($documents)
    {
        $processed = [];

        foreach ($documents as $doc) {
            // สร้างข้อมูลแสดงผลสถานะ
            $status_info = $this->get_status_display_info($doc->esv_ods_status);

            // เพิ่มข้อมูลการแสดงผล
            $doc->status_display = $status_info['display'];
            $doc->status_class = $status_info['class'];
            $doc->status_icon = $status_info['icon'];
            $doc->status_color = $status_info['color'];

            // ข้อมูลวันที่
            $doc->formatted_date = $this->format_thai_datetime($doc->esv_ods_datesave);
            $doc->formatted_updated = $this->format_thai_datetime($doc->esv_ods_updated_at);

            // ข้อมูลเพิ่มเติม
            $doc->department_display = $doc->department_name ?? 'ไม่ระบุ';
            $doc->category_display = $doc->esv_category_name ?? 'ทั่วไป';
            $doc->type_display = $doc->esv_type_name ?? 'เอกสารทั่วไป';

            // ตัดรายละเอียดให้สั้น
            if (!empty($doc->esv_ods_detail)) {
                $doc->detail_preview = mb_strlen($doc->esv_ods_detail) > 200
                    ? mb_substr($doc->esv_ods_detail, 0, 200) . '...'
                    : $doc->esv_ods_detail;
            } else {
                $doc->detail_preview = '';
            }

            // ลิงก์ดูรายละเอียด
            $doc->detail_url = site_url('Esv_ods/my_document_detail/' . $doc->esv_ods_reference_id);
            $doc->track_url = site_url('Esv_ods/track?code=' . urlencode($doc->esv_ods_reference_id));

            $processed[] = $doc;
        }

        return $processed;
    }







    public function my_document_detail($reference_id = null)
    {
        try {
            // ตรวจสอบการ login ของสมาชิก
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!$mp_id || !$mp_email) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบเพื่อดูเอกสารของคุณ');
                redirect('User');
                return;
            }

            if (empty($reference_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขอ้างอิงเอกสาร');
                redirect('Esv_ods/my_documents');
                return;
            }

            // ตรวจสอบข้อมูลสมาชิก
            $this->db->select('id, mp_id, mp_fname, mp_lname, mp_email, mp_phone, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $member_check = $this->db->get()->row();

            if (!$member_check) {
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเอกสาร
            $this->db->select('e.*, p.pname as department_name, c.esv_category_name, t.esv_type_name');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');
            $this->db->join('tbl_esv_category c', 'e.esv_ods_category_id = c.esv_category_id', 'left');
            $this->db->join('tbl_esv_type t', 'e.esv_ods_type_id = t.esv_type_id', 'left');
            $this->db->where('e.esv_ods_reference_id', $reference_id);
            $this->db->where('e.esv_ods_user_id', $member_check->id);
            $this->db->where('e.esv_ods_user_type', 'public');

            $document_detail = $this->db->get()->row();

            if (!$document_detail) {
                $this->session->set_flashdata('error_message', 'ไม่พบเอกสารที่ระบุ หรือคุณไม่มีสิทธิ์เข้าถึง');
                redirect('Esv_ods/my_documents');
                return;
            }

            // ดึงไฟล์แนบและประวัติ
            $document_detail->files = $this->esv_model->get_document_files($document_detail->esv_ods_id);
            $document_detail->history = $this->esv_model->get_document_history($document_detail->esv_ods_id);
            $document_detail->file_count = count($document_detail->files);
            $document_detail->main_file = $this->esv_model->get_main_file($document_detail->esv_ods_id);

            // อัปเดตการดู
            $this->db->where('esv_ods_id', $document_detail->esv_ods_id);
            $this->db->set('esv_ods_viewed_count', 'esv_ods_viewed_count + 1', FALSE);
            $this->db->set('esv_ods_last_viewed', date('Y-m-d H:i:s'));
            $this->db->update('tbl_esv_ods');

            // เตรียมข้อมูล
            $data = $this->prepare_navbar_data();

            $data = array_merge($data, [
                'document_detail' => $document_detail,
                'member_info' => [
                    'id' => $member_check->id,
                    'name' => trim($member_check->mp_fname . ' ' . $member_check->mp_lname),
                    'email' => $member_check->mp_email,
                    'phone' => $member_check->mp_phone
                ],
                'is_logged_in' => true,
                'user_type' => 'public',
                'user_info' => (object) [
                    'id' => $member_check->id,
                    'name' => trim($member_check->mp_fname . ' ' . $member_check->mp_lname),
                    'email' => $member_check->mp_email,
                    'phone' => $member_check->mp_phone
                ]
            ]);

            $data['page_title'] = 'รายละเอียดเอกสาร #' . $reference_id;
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'เอกสารของฉัน', 'url' => site_url('Esv_ods/my_documents')],
                ['title' => 'รายละเอียด #' . $reference_id, 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/esv_my_document_detail', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in my_document_detail: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ารายละเอียด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Esv_ods/my_documents');
            }
        }
    }





    public function track_redirect()
    {
        // ตรวจสอบ user type และ redirect ไปยังหน้าที่เหมาะสม
        $mp_id = $this->session->userdata('mp_id');
        $mp_email = $this->session->userdata('mp_email');
        $m_id = $this->session->userdata('m_id');

        if (!empty($mp_id) && !empty($mp_email)) {
            // เป็น public user (สมาชิก) - redirect ไปหน้าเอกสารของฉัน
            redirect('Esv_ods/my_documents');
        } elseif (!empty($m_id)) {
            // เป็น staff user - redirect ไปหน้าจัดการ
            redirect('Esv_ods/admin_management');
        } else {
            // เป็น guest user - ไปหน้า track
            redirect('Esv_ods/track');
        }
    }



    // ===================================================================
    // *** Admin Functions ***
    // ===================================================================

    /**
     * หน้าจัดการเอกสารสำหรับเจ้าหน้าที่
     */
    /**
     * หน้าจัดการเอกสารสำหรับเจ้าหน้าที่
     */
    public function admin_management()
    {
        try {
            log_message('info', '=== ADMIN MANAGEMENT DEBUG START ===');

            // ตรวจสอบการ login เจ้าหน้าที่
            $m_id = $this->session->userdata('m_id');

            log_message('info', 'Session m_id: ' . ($m_id ?: 'NULL'));
            log_message('info', 'Session m_email: ' . ($this->session->userdata('m_email') ?: 'NULL'));
            log_message('info', 'All session data: ' . json_encode($this->session->userdata(), JSON_UNESCAPED_UNICODE));

            if (!$m_id) {
                log_message('debug', 'No m_id in session - redirecting to login');
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์ - เช็คแค่ m_id
            $this->db->select('m_id, m_fname, m_lname, m_email, m_system, m_status, grant_user_ref_id, m_phone, m_username, m_img, ref_pid');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            log_message('info', 'Staff check query: ' . $this->db->last_query());
            log_message('info', 'Staff check result: ' . json_encode($staff_check, JSON_UNESCAPED_UNICODE));

            if (!$staff_check) {
                log_message('error', 'Staff not found in database or inactive');
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน หรือไม่พบข้อมูลในระบบ');
                redirect('User');
                return;
            }

            log_message('info', 'Staff login successful: ' . $staff_check->m_fname . ' ' . $staff_check->m_lname);

            // ตรวจสอบสิทธิ์การจัดการเอกสาร
            $can_update_status = $this->check_esv_update_permission($staff_check);
            $can_delete_document = $this->check_esv_delete_permission($staff_check);

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // เพิ่มข้อมูลสิทธิ์
            $data['can_update_status'] = $can_update_status;
            $data['can_delete_document'] = $can_delete_document;
            $data['can_handle_document'] = true; // ทุกคนดูได้
            $data['can_approve_document'] = $can_update_status;
            $data['staff_system_level'] = $staff_check->m_system;

            // ตัวกรองข้อมูล
            $filters = [
                'status' => $this->input->get('status'),
                'department' => $this->input->get('department'),
                'category' => $this->input->get('category'),
                'user_type' => $this->input->get('user_type'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search')
            ];

            // Pagination
            $this->load->library('pagination');
            $per_page = 20;
            $current_page = (int) ($this->input->get('page') ?? 1);
            $offset = ($current_page - 1) * $per_page;

            // ดึงข้อมูลเอกสารพร้อมตัวกรอง
            $document_result = $this->esv_model->get_documents_with_filters($filters, $per_page, $offset);
            $documents = $document_result['data'] ?? [];
            $total_rows = $document_result['total'] ?? 0;

            // เตรียมข้อมูลเอกสารสำหรับแสดงผล
            if (!empty($documents)) {
                foreach ($documents as $index => $doc) {
                    $documents[$index] = $this->ensure_document_data_completeness($doc);

                    // ดึงไฟล์และประวัติ
                    $documents[$index]->files = $this->esv_model->get_document_files($doc->esv_ods_id);
                    $documents[$index]->history = $this->esv_model->get_document_history($doc->esv_ods_id);
                }
            }

            $data['documents'] = $this->prepare_documents_for_display($documents);

            // สถิติเอกสาร
            $document_summary = $this->esv_model->get_document_statistics();
            $data['document_summary'] = $document_summary;
            $data['status_counts'] = $this->calculate_document_status_counts($data['documents']);

            // ตัวเลือกสำหรับ Filter
            $status_options = [
                ['value' => 'pending', 'label' => 'รอดำเนินการ'],
                ['value' => 'processing', 'label' => 'กำลังดำเนินการ'],
                ['value' => 'completed', 'label' => 'เสร็จสิ้น'],
                ['value' => 'rejected', 'label' => 'ไม่อนุมัติ'],
                ['value' => 'cancelled', 'label' => 'ยกเลิก']
            ];

            $user_type_options = [
                ['value' => 'guest', 'label' => 'ผู้ใช้ทั่วไป (Guest)'],
                ['value' => 'public', 'label' => 'สมาชิก (Public)'],
                ['value' => 'staff', 'label' => 'เจ้าหน้าที่ (Staff)']
            ];

            // รายการเอกสารล่าสุด
            $recent_documents = $this->esv_model->get_recent_documents(10);

            // รายการแผนกและหมวดหมู่
            $data['departments'] = $this->get_departments();
            $data['categories'] = $this->get_categories();

            // Pagination Setup
            $pagination_config = [
                'base_url' => site_url('Esv_ods/admin_management'),
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

            // สร้าง user_info object สำหรับ header
            $user_info_object = $this->create_complete_user_info($staff_check);

            // รวมข้อมูลทั้งหมด
            $data = array_merge($data, [
                'recent_documents' => $recent_documents,
                'filters' => $filters,
                'status_options' => $status_options,
                'user_type_options' => $user_type_options,
                'total_rows' => $total_rows,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'pagination' => $this->pagination->create_links(),
                'staff_info' => [
                    'id' => $staff_check->m_id,
                    'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                    'system' => $staff_check->m_system,
                    'can_delete' => $data['can_delete_document'],
                    'can_handle' => $data['can_handle_document'],
                    'can_approve' => $data['can_approve_document'],
                    'can_update_status' => $data['can_update_status']
                ],
                'is_logged_in' => true,
                'user_type' => 'staff',
                'user_info' => $user_info_object,
                'current_user' => $user_info_object,
                'logged_user' => $user_info_object,
                'session_user' => $user_info_object,
                'staff_data' => $user_info_object,
                'member_data' => $user_info_object,
            ]);

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Dashboard')],
                ['title' => 'จัดการเอกสารออนไลน์', 'url' => '']
            ];

            $data['page_title'] = 'จัดการเอกสารออนไลน์';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            log_message('info', '=== ADMIN MANAGEMENT DEBUG END ===');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/esv_admin_management', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in admin_management: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Dashboard');
            }
        }
    }

    /**
     * รายละเอียดเอกสารสำหรับเจ้าหน้าที่
     */
    public function document_detail($reference_id = null)
    {
        try {
            log_message('info', '=== DOCUMENT DETAIL DEBUG START ===');

            // ตรวจสอบการ login เจ้าหน้าที่
            $m_id = $this->session->userdata('m_id');

            log_message('info', 'Session m_id: ' . ($m_id ?: 'NULL'));
            log_message('info', 'Reference ID: ' . ($reference_id ?: 'NULL'));

            if (!$m_id) {
                log_message('debug', 'No m_id in session for document detail');
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์ - เช็คแค่ m_id
            $this->db->select('m_id, m_fname, m_lname, m_email, m_system, m_status, grant_user_ref_id, m_phone, m_username, m_img, ref_pid');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            log_message('info', 'Staff check result: ' . json_encode($staff_check, JSON_UNESCAPED_UNICODE));

            if (!$staff_check) {
                log_message('error', 'Staff not found in database for document detail');
                $this->session->set_flashdata('error_message', 'บัญชีของคุณไม่ได้เปิดใช้งาน');
                redirect('User');
                return;
            }

            // ตรวจสอบ reference_id
            if (empty($reference_id)) {
                log_message('debug', 'No reference_id provided');
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขอ้างอิงเอกสาร');
                redirect('Esv_ods/admin_management');
                return;
            }

            // ดึงข้อมูลเอกสาร
            $document_detail = $this->esv_model->get_document_detail_for_staff($reference_id);

            log_message('info', 'Document detail found: ' . ($document_detail ? 'YES' : 'NO'));

            if (!$document_detail) {
                log_message('debug', 'Document not found: ' . $reference_id);
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเอกสารที่ระบุ');
                redirect('Esv_ods/admin_management');
                return;
            }

            // ตรวจสอบสิทธิ์
            $can_update_status = $this->check_esv_update_permission($staff_check);
            $can_delete_document = $this->check_esv_delete_permission($staff_check);

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            $data['can_update_status'] = $can_update_status;
            $data['can_delete_document'] = $can_delete_document;
            $data['can_handle_document'] = true;
            $data['can_approve_document'] = $can_update_status;
            $data['staff_system_level'] = $staff_check->m_system;

            // สร้าง user_info object สำหรับ header
            $user_info_object = $this->create_complete_user_info($staff_check);

            // ข้อมูลเอกสาร
            $data['document_detail'] = $document_detail;

            // ข้อมูลเจ้าหน้าที่
            $data['staff_info'] = [
                'id' => $staff_check->m_id,
                'name' => trim($staff_check->m_fname . ' ' . $staff_check->m_lname),
                'system' => $staff_check->m_system,
                'can_delete' => $data['can_delete_document'],
                'can_handle' => $data['can_handle_document'],
                'can_approve' => $data['can_approve_document'],
                'can_update_status' => $data['can_update_status']
            ];

            // ข้อมูลผู้ใช้สำหรับ header
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;
            $data['current_user'] = $user_info_object;
            $data['logged_user'] = $user_info_object;
            $data['session_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;
            $data['member_data'] = $user_info_object;

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Esv_ods/admin_management')],
                ['title' => 'จัดการเอกสารออนไลน์', 'url' => site_url('Esv_ods/admin_management')],
                ['title' => 'รายละเอียด #' . $reference_id, 'url' => '']
            ];

            $data['page_title'] = 'รายละเอียดเอกสาร #' . $reference_id;

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            log_message('info', '=== DOCUMENT DETAIL DEBUG END ===');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/esv_document_detail', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in document_detail: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้ารายละเอียด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Esv_ods/admin_management');
            }
        }
    }

    /**
     * อัปเดตสถานะเอกสาร (AJAX)
     */
    public function update_document_status()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบสิทธิ์ - เช็คแค่ m_id
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                return;
            }

            $reference_id = $this->input->post('reference_id');
            $new_status = $this->input->post('new_status');
            $note = $this->input->post('note') ?: '';
            $new_priority = $this->input->post('new_priority') ?: 'normal';

            // Validation
            if (empty($reference_id) || empty($new_status)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            $allowed_statuses = ['pending', 'processing', 'completed', 'rejected', 'cancelled'];
            if (!in_array($new_status, $allowed_statuses)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่ - เช็คแค่ m_id
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

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_esv_handle_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์อัปเดตสถานะ']);
                return;
            }

            // ดึงข้อมูลเอกสารเก่าก่อนอัปเดต
            $document_data = $this->esv_model->get_document_by_reference($reference_id);
            if (!$document_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเอกสาร']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);
            $old_status = $document_data->esv_ods_status;

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
            $update_result = $this->esv_model->update_document_status(
                $reference_id,
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
                $this->db->where('esv_ods_reference_id', $reference_id);
                $this->db->update('tbl_esv_ods', [
                    'esv_ods_priority' => $new_priority
                ]);
            }

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
                return;
            }

            // สร้างการแจ้งเตือนสำหรับการอัปเดต
            try {
                $this->create_document_update_notifications(
                    $reference_id,
                    $document_data,
                    $old_status,
                    $new_status,
                    $updated_by,
                    $staff_data,
                    $note
                );
                log_message('info', "Update notifications sent for document {$reference_id}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create update notifications: ' . $e->getMessage());
            }

            ob_end_clean();

            log_message('info', "Document status updated successfully: {$reference_id} from {$old_status} to {$new_status} by {$updated_by}");

            $this->json_response([
                'success' => true,
                'message' => 'อัปเดตสถานะสำเร็จ',
                'new_status' => $new_status,
                'old_status' => $old_status,
                'updated_by' => $updated_by,
                'reference_id' => $reference_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in update_document_status: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'UPDATE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }

    /**
     * เพิ่มหมายเหตุ (AJAX)
     */
    public function add_document_note()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบสิทธิ์ - เช็คแค่ m_id
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                return;
            }

            $reference_id = $this->input->post('reference_id');
            $note = $this->input->post('note');

            if (empty($reference_id) || empty($note)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่ - เช็คแค่ m_id
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

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_esv_handle_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์เพิ่มหมายเหตุ']);
                return;
            }

            // ดึงข้อมูลเอกสาร
            $document_data = $this->esv_model->get_document_by_reference($reference_id);
            if (!$document_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเอกสาร']);
                return;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // ดึงหมายเหตุเดิม
            $this->db->select('esv_ods_response');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_reference_id', $reference_id);
            $existing_data = $this->db->get()->row();

            $old_response = $existing_data->esv_ods_response ?? '';
            $new_response = $old_response;

            if (!empty($old_response)) {
                $new_response .= "\n\n" . "--- " . date('d/m/Y H:i') . " โดย {$updated_by} ---\n" . $note;
            } else {
                $new_response = "--- " . date('d/m/Y H:i') . " โดย {$updated_by} ---\n" . $note;
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตหมายเหตุ
            $this->db->where('esv_ods_reference_id', $reference_id);
            $update_result = $this->db->update('tbl_esv_ods', [
                'esv_ods_response' => $new_response,
                'esv_ods_response_by' => $updated_by,
                'esv_ods_response_date' => date('Y-m-d H:i:s'),
                'esv_ods_updated_at' => date('Y-m-d H:i:s'),
                'esv_ods_updated_by' => $updated_by
            ]);

            if (!$update_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถเพิ่มหมายเหตุได้']);
                return;
            }

            // บันทึกประวัติ
            if (method_exists($this->esv_model, 'add_document_history')) {
                $this->esv_model->add_document_history(
                    $document_data->esv_ods_id,
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

            ob_end_clean();

            log_message('info', "Note added successfully to document: {$reference_id} by {$updated_by}");

            $this->json_response([
                'success' => true,
                'message' => 'เพิ่มหมายเหตุสำเร็จ',
                'updated_by' => $updated_by,
                'reference_id' => $reference_id,
                'note_preview' => mb_substr($note, 0, 50) . (mb_strlen($note) > 50 ? '...' : ''),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Error in add_document_note: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'ADD_NOTE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }
    // ===================================================================
    // *** Helper Functions ***
    // ===================================================================

    /**
     * เตรียมข้อมูลสำหรับ navbar
     */
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
            // โหลดข้อมูลที่จำเป็นสำหรับ navbar อย่างปลอดภัย
            if (isset($this->activity_model) && method_exists($this->activity_model, 'activity_frontend')) {
                $result = $this->activity_model->activity_frontend();
                $data['qActivity'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->HotNews_model) && method_exists($this->HotNews_model, 'hotnews_frontend')) {
                $result = $this->HotNews_model->hotnews_frontend();
                $data['qHotnews'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->Weather_report_model) && method_exists($this->Weather_report_model, 'weather_reports_frontend')) {
                $result = $this->Weather_report_model->weather_reports_frontend();
                $data['qWeather'] = (is_array($result) || is_object($result)) ? $result : [];
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * ดึงข้อมูล user ปัจจุบันแบบละเอียด - FIXED VERSION
     */
    private function get_current_user_detailed()
    {
        log_message('info', '=== GET CURRENT USER DEBUG START ===');

        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];

        try {
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');
            $m_id = $this->session->userdata('m_id');
            $m_email = $this->session->userdata('m_email');

            log_message('info', "Session Check - mp_id: $mp_id, mp_email: $mp_email, m_id: $m_id, m_email: $m_email");

            // ตรวจสอบ public user ก่อน
            if (!empty($mp_id) && !empty($mp_email)) {
                log_message('info', 'Checking PUBLIC user...');

                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_number, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_status');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    log_message('info', 'PUBLIC user found: ' . json_encode($user_data, JSON_UNESCAPED_UNICODE));

                    // สร้าง address object
                    $address_object = null;
                    if (!empty($user_data->mp_address) || !empty($user_data->mp_district)) {
                        $address_parts = array_filter([
                            trim($user_data->mp_address),
                            $user_data->mp_district ? 'ตำบล' . $user_data->mp_district : '',
                            $user_data->mp_amphoe ? 'อำเภอ' . $user_data->mp_amphoe : '',
                            $user_data->mp_province ? 'จังหวัด' . $user_data->mp_province : '',
                            $user_data->mp_zipcode
                        ]);

                        $address_object = [
                            'full_address' => implode(' ', $address_parts),
                            'additional_address' => trim($user_data->mp_address),
                            'district' => $user_data->mp_district,
                            'amphoe' => $user_data->mp_amphoe,
                            'province' => $user_data->mp_province,
                            'zipcode' => $user_data->mp_zipcode
                        ];
                    }

                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_info'] = [
                        'id' => $user_data->id,
                        'mp_id' => $user_data->mp_id,
                        'name' => trim(($user_data->mp_prefix ? $user_data->mp_prefix . ' ' : '') . $user_data->mp_fname . ' ' . $user_data->mp_lname),
                        'phone' => $user_data->mp_phone,
                        'email' => $user_data->mp_email,
                        'number' => $user_data->mp_number,
                        'address' => $address_object
                    ];

                    // เก็บ address แยกด้วยเพื่อ backward compatibility
                    $user_info['user_address'] = $address_object;

                    log_message('info', 'PUBLIC user login successful with address: ' . json_encode($address_object, JSON_UNESCAPED_UNICODE));
                    log_message('info', '=== GET CURRENT USER DEBUG END ===');
                    return $user_info;
                } else {
                    log_message('debug', 'PUBLIC user not found or inactive');
                }
            }

            // ตรวจสอบ staff user - ไม่ใช้ JOIN เลย
            if (!empty($m_id)) {
                log_message('info', 'Checking STAFF user with m_id: ' . $m_id);

                // ดึงข้อมูลจาก tbl_member เฉพาะตาราง
                $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system, m_img, m_status, ref_pid, grant_user_ref_id');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $m_id);
                $this->db->where('m_status', '1');
                $user_data = $this->db->get()->row();

                if ($user_data) {
                    log_message('info', 'STAFF user found: ' . json_encode($user_data, JSON_UNESCAPED_UNICODE));

                    // สร้าง default address สำหรับ staff
                    $address_object = [
                        'full_address' => 'หน่วยงานภายใน',
                        'additional_address' => 'หน่วยงานภายใน',
                        'district' => '',
                        'amphoe' => '',
                        'province' => '',
                        'zipcode' => ''
                    ];

                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_info'] = [
                        'id' => $user_data->m_id,
                        'name' => trim($user_data->m_fname . ' ' . $user_data->m_lname),
                        'phone' => $user_data->m_phone,
                        'email' => $user_data->m_email,
                        'm_system' => $user_data->m_system,
                        'ref_pid' => $user_data->ref_pid,
                        'grant_user_ref_id' => $user_data->grant_user_ref_id,
                        'address' => $address_object
                    ];

                    // เก็บ address แยกด้วยเพื่อ backward compatibility
                    $user_info['user_address'] = $address_object;

                    log_message('info', 'STAFF user login successful with address: ' . json_encode($address_object, JSON_UNESCAPED_UNICODE));
                    log_message('info', '=== GET CURRENT USER DEBUG END ===');
                    return $user_info;
                } else {
                    log_message('debug', 'STAFF user not found or inactive');
                }
            }

            log_message('info', 'No valid user found - defaulting to GUEST');

        } catch (Exception $e) {
            log_message('error', 'Error in get_current_user_detailed: ' . $e->getMessage());
            log_message('error', 'Error Trace: ' . $e->getTraceAsString());
        }

        log_message('info', 'Final user_info: ' . json_encode($user_info, JSON_UNESCAPED_UNICODE));
        log_message('info', '=== GET CURRENT USER DEBUG END ===');

        return $user_info;
    }

    /**
     * ดึงรายการแผนก (เริ่มจาก ID 4)
     */
    private function get_departments()
    {
        try {
            $this->db->select('pid, pname');
            $this->db->from('tbl_position');
            $this->db->where('pstatus', 'show');
            $this->db->where('pid >=', 4); // เริ่มจาก ID 4
            $this->db->order_by('pname', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting departments: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * ดึงรายการประเภทเอกสาร
     */
    private function get_document_types()
    {
        try {
            $this->db->select('esv_type_id, esv_type_name, esv_type_description, esv_type_icon, esv_type_color');
            $this->db->from('tbl_esv_type');
            $this->db->where('esv_type_status', 'active');
            $this->db->order_by('esv_type_order', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting document types: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงรายการหมวดหมู่เอกสาร
     */
    private function get_categories()
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

            $categories = $query->result();

            // จัดกลุ่มหมวดหมู่
            $grouped_categories = [];
            foreach ($categories as $category) {
                $group = $category->esv_category_group ?: 'ทั่วไป';
                if (!isset($grouped_categories[$group])) {
                    $grouped_categories[$group] = [];
                }
                $grouped_categories[$group][] = $category;
            }

            return $grouped_categories;

        } catch (Exception $e) {
            log_message('error', 'Error getting categories: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * สร้าง reference_id
     */
    private function generate_reference_id()
    {
        $max_attempts = 50;
        $attempts = 0;

        try {
            do {
                // ปีไทย 2 ตัวท้าย + ESV + เลขสุ่ม 4 ตัว
                $thai_year = date('Y') + 543;
                $year_suffix = substr($thai_year, -2);
                $random_digits = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

                $reference_id = 'ESV' . $year_suffix . $random_digits;

                // ตรวจสอบว่า ID ซ้ำหรือไม่
                $this->db->where('esv_ods_reference_id', $reference_id);
                $exists = $this->db->get('tbl_esv_ods')->num_rows();

                $attempts++;

                if ($attempts >= $max_attempts) {
                    // ถ้าพยายามเกินจำนวนที่กำหนด ให้ใช้ timestamp
                    $reference_id = 'ESV' . $year_suffix . substr(time(), -4);
                    log_message('debug', 'Max attempts reached for generating reference ID, using timestamp: ' . $reference_id);
                    break;
                }

            } while ($exists > 0);

            log_message('info', "Generated ESV reference ID: {$reference_id} (attempts: {$attempts})");

            return $reference_id;

        } catch (Exception $e) {
            log_message('error', 'Error generating reference ID: ' . $e->getMessage());

            // Fallback reference ID
            $thai_year = date('Y') + 543;
            $year_suffix = substr($thai_year, -2);
            $fallback_id = 'ESV' . $year_suffix . substr(time(), -4);

            log_message('info', 'Using fallback reference ID: ' . $fallback_id);

            return $fallback_id;
        }
    }

    /**
     * จัดการไฟล์แนบ
     */
    private function handle_file_upload($reference_id)
    {
        $this->load->library('upload');

        $upload_path = './docs/esv_files/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|pdf|doc|docx',
            'max_size' => 10240, // 10MB
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE
        ];

        $this->upload->initialize($config);

        if ($this->upload->do_upload('esv_ods_file')) {
            $upload_data = $this->upload->data();

            log_message('info', "File uploaded for document {$reference_id}: {$upload_data['orig_name']}");

            return [
                'file_name' => $upload_data['file_name'],
                'original_name' => $upload_data['orig_name'],
                'file_type' => $upload_data['file_type'],
                'file_size' => $upload_data['file_size'] * 1024,
                'file_path' => 'docs/esv_forms/' . $upload_data['file_name']
            ];
        } else {
            $upload_errors = $this->upload->display_errors('', '');
            log_message('error', "File upload failed: {$upload_errors}");
            return false;
        }
    }

    /**
     * บันทึกข้อมูลไฟล์แนบ
     */
    private function save_file_record($document_id, $file_info)
    {
        try {
            $file_data = [
                'esv_file_esv_ods_id' => $document_id,
                'esv_file_name' => $file_info['file_name'],
                'esv_file_original_name' => $file_info['original_name'],
                'esv_file_path' => $file_info['file_path'],
                'esv_file_size' => $file_info['file_size'],
                'esv_file_type' => $file_info['file_type'],
                'esv_file_extension' => pathinfo($file_info['original_name'], PATHINFO_EXTENSION),
                'esv_file_is_main' => 1,
                'esv_file_status' => 'active',
                'esv_file_uploaded_at' => date('Y-m-d H:i:s')
            ];

            return $this->db->insert('tbl_esv_files', $file_data);

        } catch (Exception $e) {
            log_message('error', 'Error saving file record: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างการแจ้งเตือน
     */
    private function create_document_notifications($document_id, $document_data, $current_user)
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                return;
            }

            $current_time = date('Y-m-d H:i:s');

            // Staff notification
            $staff_data_json = json_encode([
                'document_id' => $document_id,
                'reference_id' => $document_data['esv_ods_reference_id'],
                'topic' => $document_data['esv_ods_topic'],
                'requester' => $document_data['esv_ods_by'],
                'phone' => $document_data['esv_ods_phone'],
                'user_type' => $current_user['user_type'],
                'created_at' => $current_time,
                'type' => 'staff_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'esv_document',
                'title' => 'เอกสารใหม่',
                'message' => "มีการยื่นเอกสารใหม่: {$document_data['esv_ods_topic']} โดย {$document_data['esv_ods_by']}",
                'reference_id' => 0,
                'reference_table' => 'tbl_esv_ods',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-file-upload',
                'url' => site_url("Esv_ods/document_detail/{$document_data['esv_ods_reference_id']}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => ($current_user['is_logged_in'] && isset($current_user['user_id'])) ? intval($current_user['user_id']) : 0,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $this->db->insert('tbl_notifications', $staff_notification);

            // User notification for logged in users
            if ($current_user['is_logged_in'] && $current_user['user_type'] === 'public') {
                $individual_data_json = json_encode([
                    'document_id' => $document_id,
                    'reference_id' => $document_data['esv_ods_reference_id'],
                    'topic' => $document_data['esv_ods_topic'],
                    'status' => $document_data['esv_ods_status'],
                    'created_at' => $current_time,
                    'type' => 'individual_confirmation'
                ], JSON_UNESCAPED_UNICODE);

                $individual_notification = [
                    'type' => 'esv_document',
                    'title' => 'คุณได้ยื่นเอกสารสำเร็จ',
                    'message' => "เอกสาร \"{$document_data['esv_ods_topic']}\" ของคุณได้รับการบันทึกเรียบร้อยแล้ว หมายเลขอ้างอิง: {$document_data['esv_ods_reference_id']}",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_esv_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($current_user['user_info']['id']),
                    'priority' => 'high',
                    'icon' => 'fas fa-check-circle',
                    'url' => site_url("Esv_ods/my_document_detail/{$document_data['esv_ods_reference_id']}"),
                    'data' => $individual_data_json,
                    'created_at' => $current_time,
                    'created_by' => intval($current_user['user_info']['id']),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $this->db->insert('tbl_notifications', $individual_notification);
            }

        } catch (Exception $e) {
            log_message('error', 'Notification creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * สร้างการแจ้งเตือนการอัปเดต
     */
    private function create_document_update_notifications($reference_id, $document_data, $old_status, $new_status, $updated_by, $staff_data, $note = '')
    {
        try {
            if (!$this->db->table_exists('tbl_notifications')) {
                return;
            }

            $current_time = date('Y-m-d H:i:s');
            $status_display_old = $this->get_status_display($old_status);
            $status_display_new = $this->get_status_display($new_status);

            // 1. *** Notification สำหรับ Staff (ใหม่) ***
            $staff_data_json = json_encode([
                'document_id' => $document_data->esv_ods_id,
                'reference_id' => $reference_id,
                'topic' => $document_data->esv_ods_topic,
                'requester' => $document_data->esv_ods_by,
                'old_status' => $old_status,
                'new_status' => $new_status,
                'updated_by' => $updated_by,
                'note' => $note,
                'user_type' => $document_data->esv_ods_user_type,
                'updated_at' => $current_time,
                'type' => 'status_update_staff'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'esv_document_update',
                'title' => 'สถานะเอกสารมีการเปลี่ยนแปลง',
                'message' => "เอกสาร \"{$document_data->esv_ods_topic}\" (#{$reference_id}) เปลี่ยนสถานะจาก {$status_display_old} เป็น {$status_display_new} โดย {$updated_by}" . (!empty($note) ? " - หมายเหตุ: " . mb_substr($note, 0, 50) . (mb_strlen($note) > 50 ? '...' : '') : ''),
                'reference_id' => 0,
                'reference_table' => 'tbl_esv_ods',
                'target_role' => 'staff',
                'priority' => $this->get_notification_priority_by_status($new_status),
                'icon' => $this->get_status_notification_icon($new_status),
                'url' => site_url("Esv_ods/document_detail/{$reference_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => intval($staff_data->m_id),
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $this->db->insert('tbl_notifications', $staff_notification);
            log_message('info', "Staff notification created for status update: {$reference_id}");

            // 2. *** Notification สำหรับเจ้าของเอกสาร (เดิม - ปรับปรุง) ***
            if (!empty($document_data->esv_ods_user_id) && $document_data->esv_ods_user_type === 'public') {
                $user_data_json = json_encode([
                    'document_id' => $document_data->esv_ods_id,
                    'reference_id' => $reference_id,
                    'topic' => $document_data->esv_ods_topic,
                    'old_status' => $old_status,
                    'new_status' => $new_status,
                    'updated_by' => $updated_by,
                    'note' => $note,
                    'updated_at' => $current_time,
                    'type' => 'status_update_user'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification_message = "เอกสาร \"{$document_data->esv_ods_topic}\" ได้รับการอัปเดตสถานะเป็น: {$status_display_new}";

                // เพิ่มข้อความพิเศษตามสถานะ
                $special_message = $this->get_status_special_message($new_status);
                if ($special_message) {
                    $user_notification_message .= " - " . $special_message;
                }

                if (!empty($note)) {
                    $user_notification_message .= " หมายเหตุ: " . $note;
                }

                $user_notification = [
                    'type' => 'esv_document_update',
                    'title' => 'เอกสารของคุณมีการอัปเดต',
                    'message' => $user_notification_message,
                    'reference_id' => 0,
                    'reference_table' => 'tbl_esv_ods',
                    'target_role' => 'public',
                    'target_user_id' => intval($document_data->esv_ods_user_id),
                    'priority' => 'high',
                    'icon' => $this->get_status_notification_icon($new_status),
                    'url' => site_url("Esv_ods/my_document_detail/{$reference_id}"),
                    'data' => $user_data_json,
                    'created_at' => $current_time,
                    'created_by' => intval($staff_data->m_id),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $this->db->insert('tbl_notifications', $user_notification);
                log_message('info', "User notification created for status update: {$reference_id}");
            }

            // 3. *** Notification พิเศษสำหรับสถานะสำคัญ ***
            if (in_array($new_status, ['completed', 'rejected'])) {
                $this->create_important_status_notification($reference_id, $document_data, $new_status, $updated_by, $staff_data, $note);
            }

        } catch (Exception $e) {
            log_message('error', 'Update notification creation failed: ' . $e->getMessage());
            throw $e;
        }
    }



    private function create_important_status_notification($reference_id, $document_data, $status, $updated_by, $staff_data, $note)
    {
        try {
            $current_time = date('Y-m-d H:i:s');

            if ($status === 'completed') {
                $title = 'เอกสารดำเนินการเสร็จสิ้น';
                $message = "เอกสาร \"{$document_data->esv_ods_topic}\" (#{$reference_id}) ดำเนินการเสร็จสิ้นแล้ว โดย {$updated_by}";
                $icon = 'fas fa-check-circle';
                $priority = 'high';
            } elseif ($status === 'rejected') {
                $title = 'เอกสารไม่ได้รับการอนุมัติ';
                $message = "เอกสาร \"{$document_data->esv_ods_topic}\" (#{$reference_id}) ไม่ได้รับการอนุมัติ โดย {$updated_by}";
                $icon = 'fas fa-times-circle';
                $priority = 'urgent';
            } else {
                return; // ไม่ส่ง notification สำหรับสถานะอื่น
            }

            $important_data_json = json_encode([
                'document_id' => $document_data->esv_ods_id,
                'reference_id' => $reference_id,
                'topic' => $document_data->esv_ods_topic,
                'status' => $status,
                'updated_by' => $updated_by,
                'note' => $note,
                'user_type' => $document_data->esv_ods_user_type,
                'created_at' => $current_time,
                'type' => 'important_status_notification'
            ], JSON_UNESCAPED_UNICODE);

            // Notification สำหรับ Admin/Supervisor
            $admin_notification = [
                'type' => 'esv_important_update',
                'title' => $title . ' (แจ้ง Admin)',
                'message' => $message . (!empty($note) ? " หมายเหตุ: " . $note : ''),
                'reference_id' => 0,
                'reference_table' => 'tbl_esv_ods',
                'target_role' => 'admin',
                'priority' => $priority,
                'icon' => $icon,
                'url' => site_url("Esv_ods/document_detail/{$reference_id}"),
                'data' => $important_data_json,
                'created_at' => $current_time,
                'created_by' => intval($staff_data->m_id),
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $this->db->insert('tbl_notifications', $admin_notification);

            log_message('info', "Important status notification created: {$status} for {$reference_id}");

        } catch (Exception $e) {
            log_message('error', 'Important status notification failed: ' . $e->getMessage());
        }
    }





    private function get_status_notification_icon($status)
    {
        $icon_map = [
            'pending' => 'fas fa-clock',
            'processing' => 'fas fa-cog fa-spin',
            'completed' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            'cancelled' => 'fas fa-ban'
        ];

        return $icon_map[$status] ?? 'fas fa-info-circle';
    }

    /**
     * ข้อความพิเศษตามสถานะ
     */
    private function get_status_special_message($status)
    {
        $message_map = [
            'completed' => 'เอกสารของคุณดำเนินการเสร็จสิ้นแล้ว',
            'rejected' => 'เอกสารไม่ได้รับการอนุมัติ กรุณาตรวจสอบหมายเหตุ',
            'processing' => 'เอกสารของคุณอยู่ระหว่างการดำเนินการ',
            'cancelled' => 'เอกสารถูกยกเลิก',
            'pending' => 'เอกสารรอการดำเนินการ'
        ];

        return $message_map[$status] ?? '';
    }




    private function get_notification_priority_by_status($status)
    {
        $priority_map = [
            'pending' => 'normal',
            'processing' => 'normal',
            'completed' => 'high',
            'rejected' => 'urgent',
            'cancelled' => 'normal'
        ];

        return $priority_map[$status] ?? 'normal';
    }






    /**
     * Helper functions สำหรับสิทธิ์
     */
    private function check_esv_update_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถ update ได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 109 
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    return false;
                }

                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));
                return in_array('109', $grant_ids);
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking esv update permission: ' . $e->getMessage());
            return false;
        }
    }



    private function check_esv_handle_permission($staff_data)
    {
        try {
            // system_admin และ super_admin สามารถดำเนินการได้ทุกอย่าง
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                return true;
            }

            // user_admin ต้องมี grant_user_ref_id = 109 
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    return false;
                }

                $grant_ids = array_map('trim', explode(',', $staff_data->grant_user_ref_id));
                return in_array('109', $grant_ids); // เปลี่ยนจาก '54' เป็น '109'
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking esv handle permission: ' . $e->getMessage());
            return false;
        }
    }


    private function ensure_document_data_completeness($doc)
    {
        // ตรวจสอบและกำหนดค่าพื้นฐาน
        if (!isset($doc->esv_ods_status)) {
            $doc->esv_ods_status = 'pending';
        }

        if (!isset($doc->status_display)) {
            $doc->status_display = $this->get_status_display($doc->esv_ods_status);
        }

        if (!isset($doc->status_icon)) {
            $doc->status_icon = $this->get_document_status_icon($doc->esv_ods_status);
        }

        return $doc;
    }

    private function get_document_status_icon($status)
    {
        $icon_map = [
            'pending' => 'fas fa-clock',
            'processing' => 'fas fa-cog fa-spin',
            'completed' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            'cancelled' => 'fas fa-ban'
        ];

        return $icon_map[$status] ?? 'fas fa-question-circle';
    }

    private function prepare_documents_for_display($documents)
    {
        $prepared_list = [];

        if (empty($documents) || !is_array($documents)) {
            return $prepared_list;
        }

        foreach ($documents as $record) {
            $record_object = new stdClass();

            $record_object->esv_ods_id = $this->get_value_safe($record, 'esv_ods_id', '');
            $record_object->esv_ods_reference_id = $this->get_value_safe($record, 'esv_ods_reference_id', '');
            $record_object->esv_ods_topic = $this->get_value_safe($record, 'esv_ods_topic', '');
            $record_object->esv_ods_detail = $this->get_value_safe($record, 'esv_ods_detail', '');
            $record_object->esv_ods_status = $this->get_value_safe($record, 'esv_ods_status', 'pending');
            $record_object->esv_ods_by = $this->get_value_safe($record, 'esv_ods_by', '');
            $record_object->esv_ods_phone = $this->get_value_safe($record, 'esv_ods_phone', '');
            $record_object->esv_ods_email = $this->get_value_safe($record, 'esv_ods_email', '');
            $record_object->esv_ods_address = $this->get_value_safe($record, 'esv_ods_address', '');
            $record_object->esv_ods_datesave = $this->get_value_safe($record, 'esv_ods_datesave', '');
            $record_object->esv_ods_updated_at = $this->get_value_safe($record, 'esv_ods_updated_at', null);
            $record_object->esv_ods_priority = $this->get_value_safe($record, 'esv_ods_priority', 'normal');
            $record_object->esv_ods_user_type = $this->get_value_safe($record, 'esv_ods_user_type', 'guest');
            $record_object->esv_ods_user_id = $this->get_value_safe($record, 'esv_ods_user_id', null);
            $record_object->esv_ods_response = $this->get_value_safe($record, 'esv_ods_response', '');

            // ข้อมูลเพิ่มเติม
            $record_object->files = $this->get_value_safe($record, 'files', []);
            $record_object->history = $this->get_value_safe($record, 'history', []);

            // เพิ่มข้อมูล display properties
            $record_object->status_display = $this->get_status_display($record_object->esv_ods_status);
            $record_object->status_class = $this->get_document_status_class($record_object->esv_ods_status);
            $record_object->status_icon = $this->get_document_status_icon($record_object->esv_ods_status);
            $record_object->status_color = $this->get_document_status_color($record_object->esv_ods_status);

            $record_object->latest_update = $record_object->esv_ods_updated_at ?: $record_object->esv_ods_datesave;

            $prepared_list[] = $record_object;
        }

        return $prepared_list;
    }

    private function get_document_status_class($status)
    {
        $class_map = [
            'pending' => 'document-status-pending',
            'processing' => 'document-status-processing',
            'completed' => 'document-status-completed',
            'rejected' => 'document-status-rejected',
            'cancelled' => 'document-status-cancelled'
        ];

        return $class_map[$status] ?? 'document-status-unknown';
    }

    private function get_document_status_color($status)
    {
        $color_map = [
            'pending' => '#FFC700',
            'processing' => '#17a2b8',
            'completed' => '#28a745',
            'rejected' => '#dc3545',
            'cancelled' => '#6c757d'
        ];

        return $color_map[$status] ?? '#6c757d';
    }

    private function calculate_document_status_counts($documents)
    {
        $counts = [
            'total' => 0,
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'rejected' => 0,
            'cancelled' => 0
        ];

        if (!empty($documents) && is_array($documents)) {
            $counts['total'] = count($documents);

            foreach ($documents as $doc) {
                $status = $this->get_value_safe($doc, 'esv_ods_status', 'pending');
                if (isset($counts[$status])) {
                    $counts[$status]++;
                }
            }
        }

        return $counts;
    }

    private function create_complete_user_info($staff_check)
    {
        try {
            // ตรวจสอบว่า $staff_check มีข้อมูลครบถ้วน
            if (!$staff_check || !is_object($staff_check)) {
                log_message('error', 'Invalid staff_check data in create_complete_user_info');
                return $this->get_default_user_info();
            }

            // ตรวจสอบ properties ที่จำเป็น
            $required_props = ['m_id', 'm_fname', 'm_lname'];
            foreach ($required_props as $prop) {
                if (!property_exists($staff_check, $prop)) {
                    log_message('error', "Missing required property: {$prop} in create_complete_user_info");
                    return $this->get_default_user_info();
                }
            }

            // ดึงข้อมูลแผนกจากตาราง tbl_position
            $position_name = 'เจ้าหน้าที่';
            $ref_pid = null;

            if (!empty($staff_check->ref_pid ?? null)) {
                try {
                    $this->db->select('pname');
                    $this->db->from('tbl_position');
                    $this->db->where('pid', $staff_check->ref_pid);
                    $position = $this->db->get()->row();

                    if ($position && !empty($position->pname)) {
                        $position_name = $position->pname;
                    }
                    $ref_pid = $staff_check->ref_pid;
                } catch (Exception $e) {
                    log_message('error', 'Error getting position name: ' . $e->getMessage());
                }
            } elseif (!empty($staff_check->grant_user_ref_id ?? null)) {
                // ลองใช้ grant_user_ref_id แทน
                try {
                    $this->db->select('pname');
                    $this->db->from('tbl_position');
                    $this->db->where('pid', $staff_check->grant_user_ref_id);
                    $position = $this->db->get()->row();

                    if ($position && !empty($position->pname)) {
                        $position_name = $position->pname;
                    }
                    $ref_pid = $staff_check->grant_user_ref_id;
                } catch (Exception $e) {
                    log_message('error', 'Error getting position name from grant_user_ref_id: ' . $e->getMessage());
                }
            }

            // สร้าง user info object พร้อมตรวจสอบ properties
            return (object) [
                'id' => $staff_check->m_id ?? 0,
                'name' => trim(($staff_check->m_fname ?? '') . ' ' . ($staff_check->m_lname ?? '')),
                'email' => $staff_check->m_email ?? '', // แก้ไขตรงนี้
                'phone' => $staff_check->m_phone ?? '',
                'username' => $staff_check->m_username ?? '',
                'system' => $staff_check->m_system ?? 'end_user',
                'img' => $staff_check->m_img ?? null,
                'pname' => $position_name,

                // เพิ่มฟิลด์ทั้งหมดที่อาจจะต้องใช้
                'm_id' => $staff_check->m_id ?? 0,
                'm_fname' => $staff_check->m_fname ?? '',
                'm_lname' => $staff_check->m_lname ?? '',
                'm_email' => $staff_check->m_email ?? '', // แก้ไขตรงนี้
                'm_phone' => $staff_check->m_phone ?? '',
                'm_username' => $staff_check->m_username ?? '',
                'm_system' => $staff_check->m_system ?? 'end_user',
                'm_img' => $staff_check->m_img ?? null,
                'm_status' => $staff_check->m_status ?? '1',

                // เพิ่มฟิลด์เพื่อความสมบูรณ์
                'ref_pid' => $ref_pid,
                'grant_system_ref_id' => $staff_check->grant_user_ref_id ?? '',
                'grant_user_ref_id' => $staff_check->grant_user_ref_id ?? '',
                'm_by' => 'system',
                'm_datesave' => date('Y-m-d H:i:s')
            ];

        } catch (Exception $e) {
            log_message('error', 'Exception in create_complete_user_info: ' . $e->getMessage());
            return $this->get_default_user_info();
        }
    }


    /**
     * Helper: ดึงค่าจาก mixed data type อย่างปลอดภัย
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

    /**
     * JSON Response Helper
     */
    private function json_response($data, $exit = true)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        if (!is_array($data)) {
            $data = ['success' => false, 'message' => 'Invalid response data'];
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if ($exit) {
            exit();
        }
    }

    /**
     * Helper สำหรับแสดงสีใน Badge
     */
    public function get_status_badge_color($status)
    {
        switch ($status) {
            case 'pending':
                return 'debug';
            case 'processing':
                return 'info';
            case 'completed':
                return 'success';
            case 'rejected':
                return 'danger';
            case 'cancelled':
                return 'secondary';
            default:
                return 'light';
        }
    }

    public function get_user_type_badge_color($user_type)
    {
        switch ($user_type) {
            case 'guest':
                return 'light';
            case 'public':
                return 'primary';
            case 'staff':
                return 'success';
            default:
                return 'secondary';
        }
    }



    public function format_thai_datetime($datetime)
    {
        if (empty($datetime)) {
            return '';
        }

        try {
            $date = new DateTime($datetime);
            $thai_months = [
                1 => 'มกราคม',
                2 => 'กุมภาพันธ์',
                3 => 'มีนาคม',
                4 => 'เมษายน',
                5 => 'พฤษภาคม',
                6 => 'มิถุนายน',
                7 => 'กรกฎาคม',
                8 => 'สิงหาคม',
                9 => 'กันยายน',
                10 => 'ตุลาคม',
                11 => 'พฤศจิกายน',
                12 => 'ธันวาคม'
            ];

            $day = $date->format('j');
            $month = $thai_months[(int) $date->format('n')];
            $year = $date->format('Y') + 543;
            $time = $date->format('H:i');

            return "{$day} {$month} {$year} เวลา {$time} น.";

        } catch (Exception $e) {
            log_message('error', 'Error formatting Thai datetime: ' . $e->getMessage());
            return $datetime;
        }
    }





    public function filter_my_documents()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบการ login
            $mp_id = $this->session->userdata('mp_id');
            if (!$mp_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            // ดึงข้อมูลสมาชิก
            $this->db->select('id');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_status', 1);
            $member = $this->db->get('tbl_member_public')->row();

            if (!$member) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลสมาชิก']);
                exit;
            }

            // รับ filters
            $filters = [
                'status' => $this->input->post('status'),
                'search' => $this->input->post('search'),
                'date_from' => $this->input->post('date_from'),
                'date_to' => $this->input->post('date_to')
            ];

            // ดึงเอกสาร
            $documents = $this->get_member_documents_enhanced($member->id, $filters);
            $processed_documents = $this->process_documents_for_display($documents);
            $stats = $this->calculate_member_document_stats($documents);

            echo json_encode([
                'success' => true,
                'documents' => $processed_documents,
                'stats' => $stats,
                'total' => count($processed_documents)
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in filter_my_documents: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }




    public function get_user_address_simple()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $response = ['success' => false, 'address_data' => null];

            // ตรวจสอบการ login
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!$mp_id || !$mp_email) {
                echo json_encode($response);
                return;
            }

            // ดึงข้อมูลที่อยู่จากฐานข้อมูล
            $this->db->select('mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_phone');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $user_data = $this->db->get()->row();

            if ($user_data) {
                $response['success'] = true;
                $response['address_data'] = [
                    'additional_address' => $user_data->mp_address,
                    'district' => $user_data->mp_district,
                    'amphoe' => $user_data->mp_amphoe,
                    'province' => $user_data->mp_province,
                    'zipcode' => $user_data->mp_zipcode,
                    'phone' => $user_data->mp_phone
                ];
            }

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }



    // เพิ่ม methods เหล่านี้ลงใน class Esv_ods ใน Controller

    /**
     * ตรวจสอบการซ้ำของเลขบัตรประชาชน (AJAX)
     */
    public function check_id_card_duplicate()
    {
        // ล้าง output buffer และบังคับ JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Method not allowed'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $id_card = $this->input->post('id_card');

            // ตรวจสอบความถูกต้องของเลขบัตรประชาชน
            if (empty($id_card)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกเลขบัตรประชาชน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (!preg_match('/^\d{13}$/', $id_card)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'รูปแบบเลขบัตรประชาชนไม่ถูกต้อง'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบด้วยอัลกอริธึม
            if (!$this->validate_thai_id_card($id_card)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เลขบัตรประจำตัวประชาชนไม่ถูกต้อง'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบการซ้ำในระบบ
            $duplicate_check = $this->check_id_card_exists($id_card);

            if ($duplicate_check['exists']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เลขบัตรประชาชนนี้มีในระบบแล้ว',
                    'details' => $duplicate_check['details']
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ผ่านการตรวจสอบทั้งหมด
            echo json_encode([
                'success' => true,
                'message' => 'เลขบัตรประชาชนถูกต้องและไม่ซ้ำในระบบ'
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in check_id_card_duplicate: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }






    private function check_id_card_exists($id_card)
    {
        $result = [
            'exists' => false,
            'details' => []
        ];

        try {
            // ตรวจสอบใน tbl_esv_ods
            $this->db->select('esv_ods_id, esv_ods_reference_id, esv_ods_topic, esv_ods_by, esv_ods_datesave, esv_ods_status');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_id_card', $id_card);
            $this->db->order_by('esv_ods_datesave', 'DESC');
            $this->db->limit(5); // เอาแค่ 5 รายการล่าสุด
            $esv_documents = $this->db->get()->result();

            // ตรวจสอบใน tbl_member_public
            $this->db->select('mp_id, mp_fname, mp_lname, mp_email, mp_phone, mp_registered_date, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_number', $id_card);
            $public_member = $this->db->get()->row();

            if (!empty($esv_documents) || !empty($public_member)) {
                $result['exists'] = true;
                $result['details'] = [
                    'esv_documents' => $esv_documents,
                    'public_member' => $public_member,
                    'total_documents' => count($esv_documents)
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Error checking ID card exists: ' . $e->getMessage());
        }

        return $result;
    }






    private function validate_thai_id_card($id_card)
    {
        // ตรวจสอบรูปแบบ 13 หลัก
        if (!$id_card || !preg_match('/^\d{13}$/', $id_card)) {
            return false;
        }

        // ตรวจสอบเลขซ้ำ (เช่น 1111111111111)
        if (preg_match('/^(\d)\1{12}$/', $id_card)) {
            return false;
        }

        // คำนวณ Check Digit ตามอัลกอริธึมไทย
        $digits = str_split($id_card);
        $weights = [13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($digits[$i]) * $weights[$i];
        }

        $remainder = $sum % 11;
        $check_digit = $remainder < 2 ? (1 - $remainder) : (11 - $remainder);

        return $check_digit == intval($digits[12]);
    }




    public function validate_id_card($id_card)
    {
        // ตรวจสอบความถูกต้องของเลขบัตรประชาชน
        if (!$this->validate_thai_id_card($id_card)) {
            $this->form_validation->set_message('validate_id_card', 'เลขบัตรประจำตัวประชาชนไม่ถูกต้อง');
            return FALSE;
        }

        // ตรวจสอบการซ้ำในระบบ
        $duplicate_check = $this->check_id_card_exists($id_card);
        if ($duplicate_check['exists']) {
            $this->form_validation->set_message('validate_id_card', 'เลขบัตรประชาชนนี้มีในระบบแล้ว');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * ฟังก์ชันสำหรับซ่อนเลขบัตรประชาชน
     * @param string $id_card
     * @return string
     */
    public function mask_id_card($id_card)
    {
        if (empty($id_card) || strlen($id_card) !== 13) {
            return $id_card;
        }

        return substr($id_card, 0, 1) . '-****-*****-' . substr($id_card, 10, 2) . '-' . substr($id_card, 12, 1);
    }

    /**
     * ฟังก์ชันดึงเอกสารตามเลขบัตรประชาชน (สำหรับ Admin)
     */
    public function get_documents_by_id_card()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์ Admin
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้รับสิทธิ์'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $id_card = $this->input->post('id_card');

            if (empty($id_card) || !preg_match('/^\d{13}$/', $id_card)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'รูปแบบเลขบัตรประชาชนไม่ถูกต้อง'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ค้นหาเอกสารใน tbl_esv_ods
            $this->db->select('esv_ods_id, esv_ods_reference_id, esv_ods_topic, esv_ods_by, 
                          esv_ods_status, esv_ods_datesave, esv_ods_user_type');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_id_card', $id_card);
            $this->db->order_by('esv_ods_datesave', 'DESC');
            $documents = $this->db->get()->result();

            // ค้นหาข้อมูลใน tbl_member_public
            $this->db->select('mp_id, mp_fname, mp_lname, mp_email, mp_phone, mp_registered_date');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_number', $id_card);
            $this->db->where('mp_status', 1);
            $public_user = $this->db->get()->row();

            $result = [
                'success' => true,
                'id_card_masked' => $this->mask_id_card($id_card),
                'documents' => $documents,
                'public_user' => $public_user,
                'total_documents' => count($documents)
            ];

            echo json_encode($result, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in get_documents_by_id_card: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการค้นหา'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * เพิ่มฟิลด์เลขบัตรประชาชนในฐานข้อมูล (Migration)
     */
    public function add_id_card_field()
    {
        try {
            // ตรวจสอบว่ามีฟิลด์แล้วหรือไม่
            if (!$this->db->field_exists('esv_ods_id_card', 'tbl_esv_ods')) {

                $sql = "ALTER TABLE `tbl_esv_ods` 
                   ADD COLUMN `esv_ods_id_card` VARCHAR(13) NULL 
                   COMMENT 'เลขบัตรประจำตัวประชาชน (สำหรับ Guest User)' 
                   AFTER `esv_ods_address`";

                $this->db->query($sql);

                // เพิ่ม index
                $this->db->query("ALTER TABLE `tbl_esv_ods` ADD INDEX `idx_id_card` (`esv_ods_id_card`)");

                echo "เพิ่มฟิลด์ esv_ods_id_card สำเร็จ";
            } else {
                echo "ฟิลด์ esv_ods_id_card มีอยู่แล้ว";
            }

        } catch (Exception $e) {
            echo "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }


    public function view_file($file_id = null)
    {
        try {
            if (empty($file_id)) {
                show_404();
                return;
            }

            // ตรวจสอบการ login ของสมาชิก
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!$mp_id || !$mp_email) {
                show_404();
                return;
            }

            // ดึงข้อมูลไฟล์พร้อมตรวจสอบสิทธิ์
            $this->db->select('f.*, d.esv_ods_user_id, d.esv_ods_user_type, d.esv_ods_reference_id');
            $this->db->from('tbl_esv_files f');
            $this->db->join('tbl_esv_ods d', 'f.esv_file_esv_ods_id = d.esv_ods_id');
            $this->db->where('f.esv_file_id', $file_id);
            $this->db->where('f.esv_file_status', 'active');
            $file = $this->db->get()->row();

            if (!$file) {
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์ - ต้องเป็นเจ้าของเอกสาร
            $this->db->select('id');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $member = $this->db->get()->row();

            if (!$member || $file->esv_ods_user_id != $member->id || $file->esv_ods_user_type !== 'public') {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($file->esv_file_path)) {
                show_404();
                return;
            }

            // *** ลบส่วนอัปเดตการดู เพราะไม่มีคอลัมน์ในฐานข้อมูล ***
            // อัปเดตจำนวนการดู (ถ้ามี field นี้)
            // $this->db->where('esv_file_id', $file_id);
            // $this->db->set('esv_file_viewed_count', 'COALESCE(esv_file_viewed_count, 0) + 1', FALSE);
            // $this->db->set('esv_file_last_viewed', date('Y-m-d H:i:s'));
            // $this->db->update('tbl_esv_files');

            // บันทึก log การเปิดไฟล์แทน
            log_message('info', "File viewed: {$file->esv_file_original_name} (ID: {$file_id}) by member {$member->id}");

            // กำหนด Content-Type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file->esv_file_path);
            finfo_close($finfo);

            // ถ้าไม่สามารถตรวจจับได้ ใช้ extension
            if (!$mime_type) {
                $extension = strtolower(pathinfo($file->esv_file_original_name, PATHINFO_EXTENSION));
                $mime_types = [
                    'pdf' => 'application/pdf',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                $mime_type = $mime_types[$extension] ?? 'application/octet-stream';
            }

            // ส่ง headers
            header('Content-Type: ' . $mime_type);
            header('Content-Length: ' . filesize($file->esv_file_path));
            header('Content-Disposition: inline; filename="' . $file->esv_file_original_name . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // ส่งไฟล์
            readfile($file->esv_file_path);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Error in view_file: ' . $e->getMessage());
            show_404();
        }
    }


    public function update_my_document()
    {
        // ล้าง output buffer และบังคับ JSON response
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบการ login ของสมาชิก
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!$mp_id || !$mp_email) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาเข้าสู่ระบบ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบข้อมูลสมาชิก
            $this->db->select('id, mp_fname, mp_lname');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_id', $mp_id);
            $this->db->where('mp_email', $mp_email);
            $this->db->where('mp_status', 1);
            $member = $this->db->get()->row();

            if (!$member) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลสมาชิก'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // รับข้อมูล
            $document_id = $this->input->post('document_id');
            $topic = trim($this->input->post('topic'));
            $detail = trim($this->input->post('detail'));
            $phone = trim($this->input->post('phone'));
            $email = trim($this->input->post('email'));
            $address = trim($this->input->post('address'));

            // Validation
            if (empty($document_id) || empty($topic) || empty($detail) || empty($phone) || empty($address)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบเอกสารและสิทธิ์
            $this->db->select('esv_ods_id, esv_ods_status, esv_ods_user_id, esv_ods_user_type, esv_ods_reference_id');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_id', $document_id);
            $this->db->where('esv_ods_user_id', $member->id);
            $this->db->where('esv_ods_user_type', 'public');
            $document = $this->db->get()->row();

            if (!$document) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบเอกสารหรือคุณไม่มีสิทธิ์แก้ไข'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบสถานะที่แก้ไขได้
            $editable_statuses = ['pending', 'processing'];
            if (!in_array($document->esv_ods_status, $editable_statuses)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถแก้ไขเอกสารในสถานะนี้ได้'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัปเดตข้อมูลเอกสาร
            $update_data = [
                'esv_ods_topic' => $topic,
                'esv_ods_detail' => $detail,
                'esv_ods_phone' => $phone,
                'esv_ods_email' => $email,
                'esv_ods_address' => $address,
                'esv_ods_updated_at' => date('Y-m-d H:i:s'),
                'esv_ods_updated_by' => trim($member->mp_fname . ' ' . $member->mp_lname)
            ];

            $this->db->where('esv_ods_id', $document_id);
            $update_result = $this->db->update('tbl_esv_ods', $update_data);

            if (!$update_result) {
                $this->db->trans_rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัปเดตข้อมูลได้'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // จัดการไฟล์แนบใหม่
            $files_uploaded = 0;
            if (!empty($_FILES['additional_files']['name'][0])) {
                $upload_result = $this->handle_additional_files($document_id, $document->esv_ods_reference_id);

                if ($upload_result['success']) {
                    $files_uploaded = $upload_result['count'];
                } else {
                    // ถ้าอัปโหลดไฟล์ไม่สำเร็จ แต่อัปเดตข้อมูลสำเร็จแล้ว ให้แจ้งเตือน
                    log_message('debug', 'File upload failed but document updated: ' . $upload_result['message']);
                }
            }

            // บันทึกประวัติ
            $this->add_document_history_safe($document_id, 'updated', 'แก้ไขข้อมูลเอกสาร', $member);

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึก'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ส่งผลลัพธ์
            $message = 'อัปเดตเอกสารสำเร็จ';
            if ($files_uploaded > 0) {
                $message .= " และอัปโหลดไฟล์ {$files_uploaded} ไฟล์";
            }

            echo json_encode([
                'success' => true,
                'message' => $message,
                'files_uploaded' => $files_uploaded,
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);

            log_message('info', "Document updated successfully: {$document->esv_ods_reference_id} by member {$member->id}");

        } catch (Exception $e) {
            log_message('error', 'Error in update_my_document: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }



    private function handle_additional_files($document_id, $reference_id)
    {
        $result = ['success' => false, 'count' => 0, 'message' => ''];

        try {
            $upload_path = './docs/esv_files/';

            // สร้างโฟลเดอร์ถ้าไม่มี
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            if (empty($_FILES['additional_files']['name'][0])) {
                $result['message'] = 'ไม่มีไฟล์ที่ต้องการอัปโหลด';
                return $result;
            }

            $files_info = [];
            $file_count = count($_FILES['additional_files']['name']);
            $max_files = 5;
            $max_individual_size = 5 * 1024 * 1024; // 5MB per file
            $max_total_size = 15 * 1024 * 1024; // 15MB total
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

            // ตรวจสอบจำนวนไฟล์
            if ($file_count > $max_files) {
                $result['message'] = "สามารถอัปโหลดได้ไม่เกิน {$max_files} ไฟล์";
                return $result;
            }

            $total_size = 0;

            for ($i = 0; $i < $file_count; $i++) {
                // ข้ามไฟล์ว่าง
                if (empty($_FILES['additional_files']['name'][$i])) {
                    continue;
                }

                // ตรวจสอบ error
                if ($_FILES['additional_files']['error'][$i] !== UPLOAD_ERR_OK) {
                    log_message('error', "File upload error for file {$i}: " . $_FILES['additional_files']['error'][$i]);
                    continue;
                }

                $file_name = $_FILES['additional_files']['name'][$i];
                $file_tmp = $_FILES['additional_files']['tmp_name'][$i];
                $file_size = $_FILES['additional_files']['size'][$i];
                $file_type = $_FILES['additional_files']['type'][$i];

                // ตรวจสอบขนาดไฟล์
                if ($file_size > $max_individual_size) {
                    log_message('error', "File too large: {$file_name} ({$file_size} bytes)");
                    continue;
                }

                $total_size += $file_size;
                if ($total_size > $max_total_size) {
                    log_message('error', 'Total file size exceeds limit');
                    break;
                }

                // ตรวจสอบนามสกุลไฟล์
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_types)) {
                    log_message('error', "Invalid file type: {$file_extension}");
                    continue;
                }

                // สร้างชื่อไฟล์ใหม่
                $new_filename = $reference_id . '_add_' . ($i + 1) . '_' . time() . '.' . $file_extension;
                $target_path = $upload_path . $new_filename;

                // อัปโหลดไฟล์
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $files_info[] = [
                        'file_name' => $new_filename,
                        'original_name' => $file_name,
                        'file_type' => $file_type,
                        'file_size' => $file_size,
                        'file_path' => $target_path,
                        'file_order' => $i + 1,
                        'file_extension' => $file_extension
                    ];

                    log_message('info', "Additional file uploaded: {$file_name} -> {$new_filename}");
                } else {
                    log_message('error', "Failed to move uploaded file: {$file_name}");
                }
            }

            if (empty($files_info)) {
                $result['message'] = 'ไม่สามารถอัปโหลดไฟล์ได้';
                return $result;
            }

            // บันทึกข้อมูลไฟล์ในฐานข้อมูล
            foreach ($files_info as $file_info) {
                $file_data = [
                    'esv_file_esv_ods_id' => $document_id,
                    'esv_file_name' => $file_info['file_name'],
                    'esv_file_original_name' => $file_info['original_name'],
                    'esv_file_path' => $file_info['file_path'],
                    'esv_file_size' => $file_info['file_size'],
                    'esv_file_type' => $file_info['file_type'],
                    'esv_file_extension' => $file_info['file_extension'],
                    'esv_file_is_main' => 0, // ไฟล์เพิ่มเติมไม่ใช่ไฟล์หลัก
                    'esv_file_order' => $file_info['file_order'] + 100, // เพิ่ม 100 เพื่อแยกจากไฟล์เดิม
                    'esv_file_status' => 'active',
                    'esv_file_uploaded_at' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_esv_files', $file_data);
            }

            $result['success'] = true;
            $result['count'] = count($files_info);
            $result['message'] = "อัปโหลดไฟล์สำเร็จ " . count($files_info) . " ไฟล์";

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error in handle_additional_files: ' . $e->getMessage());
            $result['message'] = 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์';
            return $result;
        }
    }



    private function add_document_history_safe($document_id, $action, $description, $member)
    {
        try {
            if (!$this->db->table_exists('tbl_esv_history')) {
                return;
            }

            $history_data = [
                'esv_history_esv_ods_id' => $document_id,
                'esv_history_action' => $action,
                'esv_history_description' => $description,
                'esv_history_by' => trim($member->mp_fname . ' ' . $member->mp_lname),
                'esv_history_old_status' => null,
                'esv_history_new_status' => null,
                'esv_history_created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_esv_history', $history_data);

            log_message('info', "Document history added: {$action} for document {$document_id}");

        } catch (Exception $e) {
            log_message('error', 'Error adding document history: ' . $e->getMessage());
        }
    }



    public function api_esv_summary()
    {
        // ล้าง output buffer และตั้งค่า header
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            log_message('info', '=== ESV API SUMMARY START ===');

            // ตรวจสอบการเข้าสู่ระบบ (สำหรับ Admin)
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่ได้รับสิทธิ์',
                    'esv_documents' => $this->get_default_esv_stats()
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // คำนวณสถิติเอกสารออนไลน์
            $esv_stats = $this->calculate_esv_statistics_detailed();

            log_message('info', 'ESV statistics calculated: ' . json_encode($esv_stats, JSON_UNESCAPED_UNICODE));

            echo json_encode([
                'success' => true,
                'esv_documents' => $esv_stats,
                'last_updated' => date('Y-m-d H:i:s'),
                'server_time' => date('c')
            ], JSON_UNESCAPED_UNICODE);

            log_message('info', '=== ESV API SUMMARY END ===');

        } catch (Exception $e) {
            log_message('error', 'Error in api_esv_summary: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล',
                'esv_documents' => $this->get_default_esv_stats(),
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * คำนวณสถิติระบบยื่นเอกสารออนไลน์แบบละเอียด
     */
    private function calculate_esv_statistics_detailed()
    {
        try {
            // ตรวจสอบตารางก่อน
            if (!$this->db->table_exists('tbl_esv_ods')) {
                return $this->get_default_esv_stats();
            }

            $current_date = date('Y-m-d');
            $current_month = date('Y-m');
            $current_year = date('Y');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $last_week = date('Y-m-d', strtotime('-7 days'));
            $last_month = date('Y-m-d', strtotime('-30 days'));

            // === สถิติพื้นฐาน ===

            // จำนวนเอกสารทั้งหมด
            $total = $this->db->count_all('tbl_esv_ods');

            // จำนวนตามสถานะ
            $status_counts = $this->get_esv_status_counts();

            // === สถิติตามช่วงเวลา ===

            // วันนี้
            $this->db->where('DATE(esv_ods_datesave)', $current_date);
            $today_total = $this->db->count_all_results('tbl_esv_ods');

            // เมื่อวาน
            $this->db->where('DATE(esv_ods_datesave)', $yesterday);
            $yesterday_total = $this->db->count_all_results('tbl_esv_ods');

            // สัปดาห์นี้ (7 วันล่าสุด)
            $this->db->where('DATE(esv_ods_datesave) >=', $last_week);
            $week_total = $this->db->count_all_results('tbl_esv_ods');

            // เดือนนี้
            $this->db->where('DATE(esv_ods_datesave) LIKE', $current_month . '%');
            $month_total = $this->db->count_all_results('tbl_esv_ods');

            // ปีนี้
            $this->db->where('YEAR(esv_ods_datesave)', $current_year);
            $year_total = $this->db->count_all_results('tbl_esv_ods');

            // === สถิติตามประเภทผู้ใช้ ===
            $user_type_stats = $this->get_esv_user_type_statistics();

            // === สถิติแผนก ===
            $department_stats = $this->get_esv_department_statistics();

            // === สถิติประสิทธิภาพ ===
            $performance_stats = $this->get_esv_performance_statistics();

            // === สถิติไฟล์แนบ ===
            $file_stats = $this->get_esv_file_statistics();

            // === คำนวณอัตราการเปลี่ยนแปลง ===
            $daily_change = $yesterday_total > 0 ?
                round((($today_total - $yesterday_total) / $yesterday_total) * 100, 1) :
                ($today_total > 0 ? 100 : 0);

            // === เอกสารล่าสุด ===
            $recent_documents = $this->get_recent_esv_documents(5);

            // === สถิติรายเดือน (12 เดือนล่าสุด) ===
            $monthly_stats = $this->get_esv_monthly_statistics();

            // === สถิติรายสัปดาห์ (4 สัปดาห์ล่าสุด) ===
            $weekly_stats = $this->get_esv_weekly_statistics();

            // === สถิติประเภทเอกสาร ===
            $document_type_stats = $this->get_esv_document_type_statistics();

            // === เวลาเฉลี่ยในการดำเนินการ ===
            $avg_processing_time = $this->calculate_esv_avg_processing_time();

            return [
                // สถิติพื้นฐาน
                'total' => intval($total),
                'pending' => intval($status_counts['pending']),
                'processing' => intval($status_counts['processing']),
                'completed' => intval($status_counts['completed']),
                'rejected' => intval($status_counts['rejected']),
                'cancelled' => intval($status_counts['cancelled']),

                // สถิติตามช่วงเวลา
                'today_total' => intval($today_total),
                'yesterday_total' => intval($yesterday_total),
                'week_total' => intval($week_total),
                'month_total' => intval($month_total),
                'year_total' => intval($year_total),

                // อัตราการเปลี่ยนแปลง
                'daily_change_percent' => floatval($daily_change),
                'daily_change_direction' => $daily_change >= 0 ? 'increase' : 'decrease',

                // สถิติผู้ใช้
                'user_types' => $user_type_stats,

                // สถิติแผนก
                'departments' => $department_stats,

                // สถิติประสิทธิภาพ
                'performance' => $performance_stats,

                // สถิติไฟล์
                'files' => $file_stats,

                // ข้อมูลเพิ่มเติม
                'recent_documents' => $recent_documents,
                'monthly_stats' => $monthly_stats,
                'weekly_stats' => $weekly_stats,
                'document_types' => $document_type_stats,
                'avg_processing_time' => $avg_processing_time,

                // ข้อมูลเวลา
                'last_calculated' => date('Y-m-d H:i:s'),
                'calculation_date' => $current_date
            ];

        } catch (Exception $e) {
            log_message('error', 'Error calculating ESV statistics: ' . $e->getMessage());
            return $this->get_default_esv_stats();
        }
    }

    /**
     * ดึงสถิติตามสถานะ
     */
    private function get_esv_status_counts()
    {
        $counts = [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'rejected' => 0,
            'cancelled' => 0
        ];

        try {
            $this->db->select('esv_ods_status, COUNT(*) as count');
            $this->db->from('tbl_esv_ods');
            $this->db->group_by('esv_ods_status');
            $query = $this->db->get();

            foreach ($query->result() as $row) {
                $status = $row->esv_ods_status;
                if (isset($counts[$status])) {
                    $counts[$status] = intval($row->count);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting ESV status counts: ' . $e->getMessage());
        }

        return $counts;
    }

    /**
     * สถิติตามประเภทผู้ใช้
     */
    private function get_esv_user_type_statistics()
    {
        $stats = [
            'guest' => ['count' => 0, 'percentage' => 0],
            'public' => ['count' => 0, 'percentage' => 0],
            'staff' => ['count' => 0, 'percentage' => 0]
        ];

        try {
            $this->db->select('esv_ods_user_type, COUNT(*) as count');
            $this->db->from('tbl_esv_ods');
            $this->db->group_by('esv_ods_user_type');
            $query = $this->db->get();

            $total = 0;
            $results = [];

            foreach ($query->result() as $row) {
                $user_type = $row->esv_ods_user_type ?: 'guest';
                $count = intval($row->count);
                $results[$user_type] = $count;
                $total += $count;
            }

            // คำนวณเปอร์เซ็นต์
            foreach ($results as $type => $count) {
                if (isset($stats[$type])) {
                    $stats[$type]['count'] = $count;
                    $stats[$type]['percentage'] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting ESV user type statistics: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * สถิติตามแผนก
     */
    private function get_esv_department_statistics()
    {
        $stats = [];

        try {
            $this->db->select('p.pname as department_name, COUNT(e.esv_ods_id) as count');
            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');
            $this->db->group_by('e.esv_ods_department_id, p.pname');
            $this->db->order_by('count', 'DESC');
            $this->db->limit(10); // Top 10 แผนก
            $query = $this->db->get();

            foreach ($query->result() as $row) {
                $stats[] = [
                    'department_name' => $row->department_name ?: 'ไม่ระบุแผนก',
                    'count' => intval($row->count)
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting ESV department statistics: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * สถิติประสิทธิภาพ
     */
    private function get_esv_performance_statistics()
    {
        $stats = [
            'completion_rate' => 0,
            'avg_response_time' => 0,
            'satisfaction_rate' => 0,
            'pending_over_7_days' => 0,
            'processing_over_14_days' => 0
        ];

        try {
            // อัตราการดำเนินการเสร็จ
            $total_docs = $this->db->count_all('tbl_esv_ods');

            if ($total_docs > 0) {
                $this->db->where('esv_ods_status', 'completed');
                $completed_docs = $this->db->count_all_results('tbl_esv_ods');
                $stats['completion_rate'] = round(($completed_docs / $total_docs) * 100, 1);
            }

            // เอกสารค้างเกิน 7 วัน
            $this->db->where('esv_ods_status', 'pending');
            $this->db->where('DATE(esv_ods_datesave) <=', date('Y-m-d', strtotime('-7 days')));
            $stats['pending_over_7_days'] = $this->db->count_all_results('tbl_esv_ods');

            // เอกสารกำลังดำเนินการเกิน 14 วัน
            $this->db->where('esv_ods_status', 'processing');
            $this->db->where('DATE(esv_ods_datesave) <=', date('Y-m-d', strtotime('-14 days')));
            $stats['processing_over_14_days'] = $this->db->count_all_results('tbl_esv_ods');

            // เวลาตอบสนองเฉลี่ย (วัน)
            $this->db->select('AVG(DATEDIFF(COALESCE(esv_ods_updated_at, NOW()), esv_ods_datesave)) as avg_days');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_status !=', 'pending');
            $avg_query = $this->db->get()->row();

            if ($avg_query && $avg_query->avg_days) {
                $stats['avg_response_time'] = round($avg_query->avg_days, 1);
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting ESV performance statistics: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * สถิติไฟล์แนบ
     */
    private function get_esv_file_statistics()
    {
        $stats = [
            'total_files' => 0,
            'total_size_mb' => 0,
            'avg_files_per_document' => 0,
            'most_common_type' => 'pdf'
        ];

        try {
            if ($this->db->table_exists('tbl_esv_files')) {
                // จำนวนไฟล์ทั้งหมด
                $this->db->where('esv_file_status', 'active');
                $stats['total_files'] = $this->db->count_all_results('tbl_esv_files');

                // ขนาดไฟล์รวม
                $this->db->select('SUM(esv_file_size) as total_size');
                $this->db->from('tbl_esv_files');
                $this->db->where('esv_file_status', 'active');
                $size_query = $this->db->get()->row();

                if ($size_query && $size_query->total_size) {
                    $stats['total_size_mb'] = round($size_query->total_size / (1024 * 1024), 2);
                }

                // ไฟล์เฉลี่ยต่อเอกสาร
                $total_docs = $this->db->count_all('tbl_esv_ods');
                if ($total_docs > 0) {
                    $stats['avg_files_per_document'] = round($stats['total_files'] / $total_docs, 1);
                }

                // นามสกุลไฟล์ที่พบมากที่สุด
                $this->db->select('esv_file_extension, COUNT(*) as count');
                $this->db->from('tbl_esv_files');
                $this->db->where('esv_file_status', 'active');
                $this->db->group_by('esv_file_extension');
                $this->db->order_by('count', 'DESC');
                $this->db->limit(1);
                $type_query = $this->db->get()->row();

                if ($type_query) {
                    $stats['most_common_type'] = $type_query->esv_file_extension;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting ESV file statistics: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * ดึงเอกสารล่าสุด
     */
    private function get_recent_esv_documents($limit = 5)
    {
        $documents = [];

        try {
            $this->db->select('esv_ods_id, esv_ods_reference_id, esv_ods_topic, esv_ods_by, 
                          esv_ods_status, esv_ods_user_type, esv_ods_datesave');
            $this->db->from('tbl_esv_ods');
            $this->db->order_by('esv_ods_datesave', 'DESC');
            $this->db->limit($limit);
            $query = $this->db->get();

            foreach ($query->result() as $row) {
                $documents[] = [
                    'id' => $row->esv_ods_id,
                    'reference_id' => $row->esv_ods_reference_id,
                    'topic' => mb_substr($row->esv_ods_topic, 0, 50) . (mb_strlen($row->esv_ods_topic) > 50 ? '...' : ''),
                    'by' => $row->esv_ods_by,
                    'status' => $row->esv_ods_status,
                    'user_type' => $row->esv_ods_user_type,
                    'created_at' => $row->esv_ods_datesave,
                    'formatted_date' => $this->format_thai_datetime($row->esv_ods_datesave)
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting recent ESV documents: ' . $e->getMessage());
        }

        return $documents;
    }

    /**
     * สถิติรายเดือน
     */
    private function get_esv_monthly_statistics()
    {
        $stats = [];

        try {
            $this->db->select('YEAR(esv_ods_datesave) as year, MONTH(esv_ods_datesave) as month, 
                          COUNT(*) as count, esv_ods_status');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_datesave >=', date('Y-m-d', strtotime('-12 months')));
            $this->db->group_by('year, month, esv_ods_status');
            $this->db->order_by('year DESC, month DESC');
            $query = $this->db->get();

            $monthly_data = [];
            foreach ($query->result() as $row) {
                $key = $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT);
                if (!isset($monthly_data[$key])) {
                    $monthly_data[$key] = [
                        'year' => $row->year,
                        'month' => $row->month,
                        'total' => 0,
                        'pending' => 0,
                        'processing' => 0,
                        'completed' => 0,
                        'rejected' => 0,
                        'cancelled' => 0
                    ];
                }

                $monthly_data[$key]['total'] += intval($row->count);
                $monthly_data[$key][$row->esv_ods_status] = intval($row->count);
            }

            // เรียงลำดับและตัดให้เหลือ 12 เดือน
            $stats = array_slice(array_values($monthly_data), 0, 12);

        } catch (Exception $e) {
            log_message('error', 'Error getting ESV monthly statistics: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * สถิติรายสัปดาห์
     */
    private function get_esv_weekly_statistics()
    {
        $stats = [];

        try {
            for ($i = 0; $i < 4; $i++) {
                $week_start = date('Y-m-d', strtotime("-{$i} weeks monday"));
                $week_end = date('Y-m-d', strtotime("-{$i} weeks sunday"));

                $this->db->where('DATE(esv_ods_datesave) >=', $week_start);
                $this->db->where('DATE(esv_ods_datesave) <=', $week_end);
                $count = $this->db->count_all_results('tbl_esv_ods');

                $stats[] = [
                    'week_start' => $week_start,
                    'week_end' => $week_end,
                    'count' => $count,
                    'week_label' => 'สัปดาห์ที่ ' . (4 - $i)
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting ESV weekly statistics: ' . $e->getMessage());
        }

        return array_reverse($stats);
    }

    /**
     * สถิติประเภทเอกสาร
     */
    private function get_esv_document_type_statistics()
    {
        $stats = [];

        try {
            if ($this->db->table_exists('tbl_esv_type')) {
                $this->db->select('t.esv_type_name, COUNT(e.esv_ods_id) as count');
                $this->db->from('tbl_esv_ods e');
                $this->db->join('tbl_esv_type t', 'e.esv_ods_type_id = t.esv_type_id', 'left');
                $this->db->group_by('e.esv_ods_type_id, t.esv_type_name');
                $this->db->order_by('count', 'DESC');
                $this->db->limit(5);
                $query = $this->db->get();

                foreach ($query->result() as $row) {
                    $stats[] = [
                        'type_name' => $row->esv_type_name ?: 'ไม่ระบุประเภท',
                        'count' => intval($row->count)
                    ];
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting ESV document type statistics: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * คำนวณเวลาเฉลี่ยในการดำเนินการ
     */
    private function calculate_esv_avg_processing_time()
    {
        $stats = [
            'avg_days' => 0,
            'median_days' => 0,
            'fastest_days' => 0,
            'slowest_days' => 0
        ];

        try {
            // คำนวณเฉพาะเอกสารที่เสร็จสิ้น
            $this->db->select('DATEDIFF(esv_ods_updated_at, esv_ods_datesave) as processing_days');
            $this->db->from('tbl_esv_ods');
            $this->db->where('esv_ods_status', 'completed');
            $this->db->where('esv_ods_updated_at IS NOT NULL');
            $this->db->order_by('processing_days', 'ASC');
            $query = $this->db->get();

            $processing_times = [];
            foreach ($query->result() as $row) {
                $days = intval($row->processing_days);
                if ($days >= 0) { // ป้องกันค่าลบ
                    $processing_times[] = $days;
                }
            }

            if (!empty($processing_times)) {
                $stats['avg_days'] = round(array_sum($processing_times) / count($processing_times), 1);
                $stats['fastest_days'] = min($processing_times);
                $stats['slowest_days'] = max($processing_times);

                // Median
                sort($processing_times);
                $count = count($processing_times);
                $middle = floor($count / 2);

                if ($count % 2 == 0) {
                    $stats['median_days'] = ($processing_times[$middle - 1] + $processing_times[$middle]) / 2;
                } else {
                    $stats['median_days'] = $processing_times[$middle];
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error calculating ESV processing time: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * ค่าเริ่มต้นเมื่อเกิดข้อผิดพลาด
     */
    private function get_default_esv_stats()
    {
        return [
            'total' => 0,
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'rejected' => 0,
            'cancelled' => 0,
            'today_total' => 0,
            'yesterday_total' => 0,
            'week_total' => 0,
            'month_total' => 0,
            'year_total' => 0,
            'daily_change_percent' => 0,
            'daily_change_direction' => 'stable',
            'user_types' => [
                'guest' => ['count' => 0, 'percentage' => 0],
                'public' => ['count' => 0, 'percentage' => 0],
                'staff' => ['count' => 0, 'percentage' => 0]
            ],
            'departments' => [],
            'performance' => [
                'completion_rate' => 0,
                'avg_response_time' => 0,
                'satisfaction_rate' => 0,
                'pending_over_7_days' => 0,
                'processing_over_14_days' => 0
            ],
            'files' => [
                'total_files' => 0,
                'total_size_mb' => 0,
                'avg_files_per_document' => 0,
                'most_common_type' => 'pdf'
            ],
            'recent_documents' => [],
            'monthly_stats' => [],
            'weekly_stats' => [],
            'document_types' => [],
            'avg_processing_time' => [
                'avg_days' => 0,
                'median_days' => 0,
                'fastest_days' => 0,
                'slowest_days' => 0
            ],
            'last_calculated' => date('Y-m-d H:i:s'),
            'calculation_date' => date('Y-m-d')
        ];
    }






    /**
     * ลบเอกสาร (Hard Delete) - สำหรับ Admin เท่านั้น
     */
    public function delete_document()
    {
        ob_start();

        try {
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            // ตรวจสอบสิทธิ์ - เฉพาะ system_admin และ super_admin เท่านั้น
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์ กรุณาเข้าสู่ระบบ']);
                return;
            }

            $reference_id = $this->input->post('reference_id');
            $reason = $this->input->post('reason');
            $action = $this->input->post('action'); // hard_delete

            // Validation
            if (empty($reference_id)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบหมายเลขอ้างอิงเอกสาร']);
                return;
            }

            if (empty($reason)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'กรุณาระบุเหตุผลในการลบ']);
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_email');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเจ้าหน้าที่']);
                return;
            }

            // ตรวจสอบสิทธิ์การลบ - เฉพาะ system_admin และ super_admin
            if (!$this->check_esv_delete_permission($staff_data)) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบเอกสาร (เฉพาะ System Admin)']);
                return;
            }

            // ดึงข้อมูลเอกสารที่จะลบ
            $document_data = $this->esv_model->get_document_by_reference($reference_id);
            if (!$document_data) {
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่พบข้อมูลเอกสาร']);
                return;
            }

            $deleted_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เริ่ม Transaction
            $this->db->trans_start();

            // 1. ดึงรายการไฟล์ที่จะลบ
            $files_to_delete = $this->get_document_files_for_deletion($document_data->esv_ods_id);

            // 2. บันทึก Log การลับก่อนลบจริง
            $this->log_document_deletion($document_data, $deleted_by, $reason, $files_to_delete);

            // 3. ลบไฟล์จริงจาก server
            $this->delete_physical_files($files_to_delete);

            // 4. ลบข้อมูลจากฐานข้อมูล (CASCADE จะลบ files และ history อัตโนมัติ)
            $this->db->where('esv_ods_id', $document_data->esv_ods_id);
            $delete_result = $this->db->delete('tbl_esv_ods');

            if (!$delete_result) {
                $this->db->trans_rollback();
                ob_end_clean();
                $this->json_response(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลจากฐานข้อมูลได้']);
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

            log_message('info', "Document permanently deleted: {$reference_id} by {$deleted_by}. Reason: {$reason}");

            $this->json_response([
                'success' => true,
                'message' => 'ลบเอกสารออกจากระบบเรียบร้อยแล้ว',
                'reference_id' => $reference_id,
                'deleted_by' => $deleted_by,
                'deleted_files' => count($files_to_delete),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            ob_end_clean();
            $this->db->trans_rollback();
            log_message('error', 'Error in delete_document: ' . $e->getMessage());

            $this->json_response([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error_code' => 'DELETE_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }

    /**
     * ดึงรายการไฟล์สำหรับการลบ
     */
    private function get_document_files_for_deletion($document_id)
    {
        try {
            if (!$this->db->table_exists('tbl_esv_files')) {
                return [];
            }

            $this->db->select('esv_file_id, esv_file_name, esv_file_original_name, esv_file_path, esv_file_size');
            $this->db->from('tbl_esv_files');
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->where('esv_file_status', 'active');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting files for deletion: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ลบไฟล์จริงจาก server
     */
    private function delete_physical_files($files)
    {
        $deleted_count = 0;
        $failed_count = 0;

        foreach ($files as $file) {
            try {
                if (file_exists($file->esv_file_path)) {
                    if (unlink($file->esv_file_path)) {
                        $deleted_count++;
                        log_message('info', "Physical file deleted: {$file->esv_file_path}");
                    } else {
                        $failed_count++;
                        log_message('error', "Failed to delete physical file: {$file->esv_file_path}");
                    }
                } else {
                    log_message('debug', "Physical file not found: {$file->esv_file_path}");
                }
            } catch (Exception $e) {
                $failed_count++;
                log_message('error', "Exception deleting file {$file->esv_file_path}: " . $e->getMessage());
            }
        }

        log_message('info', "File deletion summary - Deleted: {$deleted_count}, Failed: {$failed_count}");

        return ['deleted' => $deleted_count, 'failed' => $failed_count];
    }

    /**
     * บันทึก Log การลบเอกสาร
     */
    private function log_document_deletion($document_data, $deleted_by, $reason, $files)
    {
        try {
            // สร้าง deletion log ในตาราง log หรือไฟล์
            $deletion_log = [
                'action' => 'DOCUMENT_DELETED',
                'reference_id' => $document_data->esv_ods_reference_id,
                'document_id' => $document_data->esv_ods_id,
                'document_topic' => $document_data->esv_ods_topic,
                'document_by' => $document_data->esv_ods_by,
                'document_status' => $document_data->esv_ods_status,
                'document_created' => $document_data->esv_ods_datesave,
                'deleted_by' => $deleted_by,
                'deletion_reason' => $reason,
                'deleted_at' => date('Y-m-d H:i:s'),
                'files_count' => count($files),
                'files_list' => array_column($files, 'esv_file_original_name')
            ];

            // บันทึกลงไฟล์ log
            $log_message = "ESV DOCUMENT DELETION: " . json_encode($deletion_log, JSON_UNESCAPED_UNICODE);
            log_message('info', $log_message);

            // หากมีตาราง deletion_log ก็สามารถบันทึกได้
            if ($this->db->table_exists('tbl_deletion_log')) {
                $this->db->insert('tbl_deletion_log', [
                    'table_name' => 'tbl_esv_ods',
                    'record_id' => $document_data->esv_ods_id,
                    'reference_id' => $document_data->esv_ods_reference_id,
                    'deleted_data' => json_encode($deletion_log, JSON_UNESCAPED_UNICODE),
                    'deleted_by' => $deleted_by,
                    'deletion_reason' => $reason,
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Error logging document deletion: ' . $e->getMessage());
        }
    }

    /**
     * ตรวจสอบสิทธิ์การลบเอกสาร
     */
    private function check_esv_delete_permission($staff_data)
    {
        try {
            // เฉพาะ system_admin และ super_admin ที่สามารถลบได้
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                return true;
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Error checking esv delete permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ส่งออกข้อมูลเอกสาร ESV เป็น Excel
     */
    public function export_excel()
    {
        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบสิทธิ์การส่งออก (เช่นเดียวกับการดู)
            if (!$this->check_esv_handle_permission($staff_check)) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์ส่งออกข้อมูล');
                redirect('Dashboard');
                return;
            }

            // ดึงตัวกรองจาก GET parameters
            $filters = [
                'status' => $this->input->get('status'),
                'department' => $this->input->get('department'),
                'category' => $this->input->get('category'),
                'user_type' => $this->input->get('user_type'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'search' => $this->input->get('search')
            ];

            // ดึงข้อมูลเอกสารทั้งหมดที่ตรงกับตัวกรอง (ไม่จำกัด pagination)
            $documents = $this->get_documents_for_export($filters);

            // สร้างไฟล์ Excel
            $this->generate_excel_file($documents, $filters, $staff_check);

        } catch (Exception $e) {
            log_message('error', 'Error in export_excel: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการส่งออก Excel: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการส่งออกข้อมูล');
                redirect('Esv_ods/admin_management');
            }
        }
    }

    /**
     * ดึงข้อมูลเอกสารสำหรับส่งออก Excel
     */
    private function get_documents_for_export($filters = [])
    {
        try {
            // Query หลัก
            $this->db->select('
            e.esv_ods_id,
            e.esv_ods_reference_id,
            e.esv_ods_topic,
            e.esv_ods_detail,
            e.esv_ods_status,
            e.esv_ods_priority,
            e.esv_ods_by,
            e.esv_ods_phone,
            e.esv_ods_email,
            e.esv_ods_address,
            e.esv_ods_user_type,
            e.esv_ods_datesave,
            e.esv_ods_updated_at,
            e.esv_ods_response,
            e.esv_ods_response_by,
            e.esv_ods_response_date,
            COALESCE(p.pname, e.esv_ods_department_other, "ไม่ระบุ") as department_name,
            COALESCE(c.esv_category_name, e.esv_ods_category_other, "ทั่วไป") as category_name,
            COALESCE(t.esv_type_name, "เอกสารทั่วไป") as type_name
        ');

            $this->db->from('tbl_esv_ods e');
            $this->db->join('tbl_position p', 'e.esv_ods_department_id = p.pid', 'left');
            $this->db->join('tbl_esv_category c', 'e.esv_ods_category_id = c.esv_category_id', 'left');
            $this->db->join('tbl_esv_type t', 'e.esv_ods_type_id = t.esv_type_id', 'left');

            // ใช้ตัวกรอง
            $this->apply_export_filters($filters);

            // เรียงลำดับ
            $this->db->order_by('e.esv_ods_datesave', 'DESC');

            $query = $this->db->get();
            $documents = $query->result();

            // เพิ่มข้อมูลไฟล์สำหรับแต่ละเอกสาร
            foreach ($documents as $doc) {
                $doc->files = $this->get_document_files_for_export($doc->esv_ods_id);
                $doc->file_count = count($doc->files);
                $doc->file_names = implode(', ', array_column($doc->files, 'esv_file_original_name'));
            }

            log_message('info', 'Retrieved ' . count($documents) . ' documents for Excel export');

            return $documents;

        } catch (Exception $e) {
            log_message('error', 'Error getting documents for export: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ใช้ตัวกรองสำหรับ Export
     */
    private function apply_export_filters($filters)
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
            $this->db->group_end();
        }
    }

    /**
     * ดึงไฟล์สำหรับ Export
     */
    private function get_document_files_for_export($document_id)
    {
        try {
            if (!$this->db->table_exists('tbl_esv_files')) {
                return [];
            }

            $this->db->select('esv_file_original_name, esv_file_size, esv_file_extension');
            $this->db->from('tbl_esv_files');
            $this->db->where('esv_file_esv_ods_id', $document_id);
            $this->db->where('esv_file_status', 'active');
            $this->db->order_by('esv_file_order', 'ASC');

            return $this->db->get()->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting files for export: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * สร้างไฟล์ Excel
     */
    private function generate_excel_file($documents, $filters, $staff_info)
    {
        try {
            // โหลด PhpSpreadsheet (ถ้ามี) หรือใช้วิธีอื่น
            if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                $this->generate_excel_with_phpspreadsheet($documents, $filters, $staff_info);
            } else {
                // ใช้ CSV format แทน Excel ถ้าไม่มี PhpSpreadsheet
                $this->generate_csv_file($documents, $filters, $staff_info);
            }

        } catch (Exception $e) {
            log_message('error', 'Error generating Excel file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * สร้างไฟล์ CSV (fallback ถ้าไม่มี PhpSpreadsheet)
     */
    private function generate_csv_file($documents, $filters, $staff_info)
    {
        try {
            // สร้างชื่อไฟล์
            $filename = 'ESV_Documents_' . date('Y-m-d_H-i-s') . '.csv';

            // ตั้งค่า headers
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // เปิด output stream
            $output = fopen('php://output', 'w');

            // เพิ่ม BOM สำหรับ UTF-8
            fwrite($output, "\xEF\xBB\xBF");

            // Header ไฟล์
            fputcsv($output, ['รายงานเอกสารออนไลน์ (ESV)'], ',', '"');
            fputcsv($output, ['สร้างโดย: ' . trim($staff_info->m_fname . ' ' . $staff_info->m_lname)], ',', '"');
            fputcsv($output, ['วันที่ส่งออก: ' . date('d/m/') . (date('Y') + 543) . ' เวลา ' . date('H:i:s')], ',', '"');
            fputcsv($output, ['จำนวนรายการ: ' . count($documents) . ' รายการ'], ',', '"');

            // ข้อมูลตัวกรอง
            if (!empty(array_filter($filters))) {
                fputcsv($output, [], ',', '"'); // บรรทัดว่าง
                fputcsv($output, ['ตัวกรองที่ใช้:'], ',', '"');

                if (!empty($filters['status'])) {
                    fputcsv($output, ['- สถานะ: ' . $this->get_status_display($filters['status'])], ',', '"');
                }
                if (!empty($filters['date_from'])) {
                    fputcsv($output, ['- จากวันที่: ' . $filters['date_from']], ',', '"');
                }
                if (!empty($filters['date_to'])) {
                    fputcsv($output, ['- ถึงวันที่: ' . $filters['date_to']], ',', '"');
                }
            }

            fputcsv($output, [], ',', '"'); // บรรทัดว่าง

            // Header คอลัมน์
            $headers = [
                'ลำดับ',
                'หมายเลขอ้างอิง',
                'วันที่ยื่น',
                'เรื่อง',
                'รายละเอียด',
                'สถานะ',
                'ความสำคัญ',
                'ผู้ยื่น',
                'เบอร์โทร',
                'อีเมล',
                'ประเภทผู้ใช้',
                'แผนก',
                'หมวดหมู่',
                'ประเภทเอกสาร',
                'จำนวนไฟล์',
                'ชื่อไฟล์',
                'คำตอบ',
                'ผู้ตอบกลับ',
                'วันที่ตอบกลับ',
                'วันที่อัปเดต'
            ];

            fputcsv($output, $headers, ',', '"');

            // ข้อมูลเอกสาร
            $row_number = 1;
            foreach ($documents as $doc) {
                $row = [
                    $row_number++,
                    $doc->esv_ods_reference_id,
                    $this->format_thai_date($doc->esv_ods_datesave),
                    $doc->esv_ods_topic,
                    mb_substr(strip_tags($doc->esv_ods_detail), 0, 100) . (mb_strlen($doc->esv_ods_detail) > 100 ? '...' : ''),
                    $this->get_status_display($doc->esv_ods_status),
                    $this->get_priority_display($doc->esv_ods_priority),
                    $doc->esv_ods_by,
                    $doc->esv_ods_phone,
                    $doc->esv_ods_email,
                    $this->get_user_type_display($doc->esv_ods_user_type),
                    $doc->department_name,
                    $doc->category_name,
                    $doc->type_name,
                    $doc->file_count,
                    $doc->file_names,
                    mb_substr(strip_tags($doc->esv_ods_response ?? ''), 0, 100) . (mb_strlen($doc->esv_ods_response ?? '') > 100 ? '...' : ''),
                    $doc->esv_ods_response_by ?? '',
                    !empty($doc->esv_ods_response_date) ? $this->format_thai_date($doc->esv_ods_response_date) : '',
                    !empty($doc->esv_ods_updated_at) ? $this->format_thai_date($doc->esv_ods_updated_at) : ''
                ];

                fputcsv($output, $row, ',', '"');
            }

            fclose($output);

            log_message('info', "CSV export completed: {$filename} by " . trim($staff_info->m_fname . ' ' . $staff_info->m_lname));

        } catch (Exception $e) {
            log_message('error', 'Error generating CSV file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper: จัดรูปแบบวันที่เป็นภาษาไทย
     */
    private function format_thai_date($datetime)
    {
        if (empty($datetime)) {
            return '';
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

            $date = date('j', strtotime($datetime));
            $month = $thai_months[date('m', strtotime($datetime))];
            $year = date('Y', strtotime($datetime)) + 543;
            $time = date('H:i', strtotime($datetime));

            return "{$date} {$month} {$year} {$time}";

        } catch (Exception $e) {
            return $datetime;
        }
    }

    /**
     * Helper: แสดงสถานะ
     */
    private function get_status_display($status)
    {
        $status_map = [
            'pending' => 'รอดำเนินการ',
            'processing' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จสิ้น',
            'rejected' => 'ไม่อนุมัติ',
            'cancelled' => 'ยกเลิก'
        ];

        return $status_map[$status] ?? $status;
    }

    /**
     * Helper: แสดงความสำคัญ
     */
    private function get_priority_display($priority)
    {
        $priority_map = [
            'normal' => 'ปกติ',
            'urgent' => 'เร่งด่วน',
            'very_urgent' => 'เร่งด่วนมาก'
        ];

        return $priority_map[$priority] ?? 'ปกติ';
    }

    /**
     * Helper: แสดงประเภทผู้ใช้
     */
    private function get_user_type_display($user_type)
    {
        $type_map = [
            'guest' => 'ผู้ใช้ทั่วไป',
            'public' => 'สมาชิก',
            'staff' => 'เจ้าหน้าที่'
        ];

        return $type_map[$user_type] ?? 'ไม่ระบุ';
    }



    // เพิ่ม Methods เหล่านี้ลงใน Esv_ods Controller

    /**
     * หน้าจัดการประเภทเอกสาร
     */
    public function manage_document_types()
    {
        try {
            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_esv_handle_permission($staff_check)) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์จัดการข้อมูลประเภทเอกสาร');
                redirect('Esv_ods/admin_management');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // ดึงรายการประเภทเอกสารทั้งหมด
            $data['document_types'] = $this->get_all_document_types();

            // สิทธิ์การใช้งาน
            $data['can_add'] = true;
            $data['can_edit'] = true;
            $data['can_delete'] = $this->check_esv_delete_permission($staff_check);

            // ข้อมูลเจ้าหน้าที่
            $user_info_object = $this->create_complete_user_info($staff_check);
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;
            $data['current_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;

            // Page metadata
            $data['page_title'] = 'จัดการประเภทเอกสาร';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Dashboard')],
                ['title' => 'จัดการเอกสารออนไลน์', 'url' => site_url('Esv_ods/admin_management')],
                ['title' => 'จัดการประเภทเอกสาร', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/esv_manage_document_types', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in manage_document_types: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า');
            redirect('Esv_ods/admin_management');
        }
    }

    /**
     * หน้าจัดการหมวดหมู่เอกสาร
     */
    public function manage_categories()
    {
        try {
            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_esv_handle_permission($staff_check)) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์จัดการข้อมูลหมวดหมู่เอกสาร');
                redirect('Esv_ods/admin_management');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // ดึงรายการหมวดหมู่เอกสารทั้งหมด
            $data['categories'] = $this->get_all_categories_with_department();
            $data['departments'] = $this->get_departments();

            // *** เพิ่มส่วนนี้ - ดึงรายการประเภทเอกสารสำหรับ dropdown ***
            $data['document_types'] = $this->get_document_types_for_category();

            // สิทธิ์การใช้งาน
            $data['can_add'] = true;
            $data['can_edit'] = true;
            $data['can_delete'] = $this->check_esv_delete_permission($staff_check);

            // ข้อมูลเจ้าหน้าที่
            $user_info_object = $this->create_complete_user_info($staff_check);
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;
            $data['current_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;

            // Page metadata
            $data['page_title'] = 'จัดการหมวดหมู่เอกสาร';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Dashboard')],
                ['title' => 'จัดการเอกสารออนไลน์', 'url' => site_url('Esv_ods/admin_management')],
                ['title' => 'จัดการหมวดหมู่เอกสาร', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/esv_manage_categories', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in manage_categories: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า');
            redirect('Esv_ods/admin_management');
        }
    }




    private function get_document_types_for_category()
    {
        try {
            $this->db->select('esv_type_id, esv_type_name');
            $this->db->from('tbl_esv_type');
            $this->db->where('esv_type_status', 'active');
            $this->db->order_by('esv_type_order', 'ASC');
            $this->db->order_by('esv_type_name', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting document types for category: ' . $e->getMessage());
            return [];
        }
    }





    /**
     * ดึงประเภทเอกสารทั้งหมด
     */
    private function get_all_document_types()
    {
        try {
            $this->db->select('*');
            $this->db->from('tbl_esv_type');
            $this->db->order_by('esv_type_order', 'ASC');
            $this->db->order_by('esv_type_name', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting all document types: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงหมวดหมู่เอกสารทั้งหมดพร้อมข้อมูลแผนก
     */
    private function get_all_categories_with_department()
    {
        try {
            $this->db->select('c.*, p.pname as department_name, t.esv_type_name as type_name');
            $this->db->from('tbl_esv_category c');
            $this->db->join('tbl_position p', 'c.esv_category_department_id = p.pid', 'left');
            $this->db->join('tbl_esv_type t', 'c.esv_category_group = t.esv_type_id', 'left');
            $this->db->order_by('c.esv_category_group', 'ASC');
            $this->db->order_by('c.esv_category_order', 'ASC');
            $this->db->order_by('c.esv_category_name', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting all categories with department: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * บันทึกประเภทเอกสาร (AJAX)
     */
    public function save_document_type()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_handle_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ดำเนินการ']);
                exit;
            }

            // รับข้อมูล
            $type_id = $this->input->post('type_id');
            $type_name = trim($this->input->post('type_name'));
            $type_description = trim($this->input->post('type_description'));
            $type_icon = trim($this->input->post('type_icon'));
            $type_color = trim($this->input->post('type_color'));
            $type_order = (int) $this->input->post('type_order');
            $type_status = $this->input->post('type_status');

            // Validation
            if (empty($type_name)) {
                echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อประเภทเอกสาร']);
                exit;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เตรียมข้อมูล
            $data = [
                'esv_type_name' => $type_name,
                'esv_type_description' => $type_description ?: null,
                'esv_type_icon' => $type_icon ?: 'fas fa-file-alt',
                'esv_type_color' => $type_color ?: '#8b9cc7',
                'esv_type_order' => $type_order,
                'esv_type_status' => $type_status ?: 'active',
                'esv_type_updated_by' => $updated_by,
                'esv_type_updated_at' => date('Y-m-d H:i:s')
            ];

            if (empty($type_id)) {
                // เพิ่มใหม่
                $data['esv_type_created_by'] = $updated_by;
                $data['esv_type_created_at'] = date('Y-m-d H:i:s');

                $result = $this->db->insert('tbl_esv_type', $data);
                $message = 'เพิ่มประเภทเอกสารสำเร็จ';
            } else {
                // แก้ไข
                $this->db->where('esv_type_id', $type_id);
                $result = $this->db->update('tbl_esv_type', $data);
                $message = 'อัปเดตประเภทเอกสารสำเร็จ';
            }

            if ($result) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in save_document_type: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }

    /**
     * บันทึกหมวดหมู่เอกสาร (AJAX)
     */
    public function save_category()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_handle_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ดำเนินการ']);
                exit;
            }

            // รับข้อมูล
            $category_id = $this->input->post('category_id');
            $category_name = trim($this->input->post('category_name'));
            $category_description = trim($this->input->post('category_description'));
            $category_group = $this->input->post('category_group'); // ตอนนี้จะเป็น type_id
            $category_department_id = $this->input->post('category_department_id');
            $category_icon = trim($this->input->post('category_icon'));
            $category_color = trim($this->input->post('category_color'));
            $category_order = (int) $this->input->post('category_order');
            $category_process_days = $this->input->post('category_process_days');
            $category_fee = $this->input->post('category_fee');
            $category_status = $this->input->post('category_status');

            // Validation
            if (empty($category_name)) {
                echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อหมวดหมู่เอกสาร']);
                exit;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เตรียมข้อมูล - เก็บ type_id ในฟิลด์ category_group
            $data = [
                'esv_category_name' => $category_name,
                'esv_category_description' => $category_description ?: null,
                'esv_category_group' => $category_group ?: null, // เก็บเป็น type_id
                'esv_category_department_id' => !empty($category_department_id) ? $category_department_id : null,
                'esv_category_icon' => $category_icon ?: 'fas fa-folder',
                'esv_category_color' => $category_color ?: '#8b9cc7',
                'esv_category_order' => $category_order,
                'esv_category_process_days' => !empty($category_process_days) ? $category_process_days : null,
                'esv_category_fee' => !empty($category_fee) ? $category_fee : 0.00,
                'esv_category_status' => $category_status ?: 'active',
                'esv_category_updated_by' => $updated_by,
                'esv_category_updated_at' => date('Y-m-d H:i:s')
            ];

            if (empty($category_id)) {
                // เพิ่มใหม่
                $data['esv_category_created_by'] = $updated_by;
                $data['esv_category_created_at'] = date('Y-m-d H:i:s');

                $result = $this->db->insert('tbl_esv_category', $data);
                $message = 'เพิ่มหมวดหมู่เอกสารสำเร็จ';
            } else {
                // แก้ไข
                $this->db->where('esv_category_id', $category_id);
                $result = $this->db->update('tbl_esv_category', $data);
                $message = 'อัปเดตหมวดหมู่เอกสารสำเร็จ';
            }

            if ($result) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in save_category: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }



    /**
     * ลบประเภทเอกสาร (AJAX)
     */
    public function delete_document_type()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_delete_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูล']);
                exit;
            }

            $type_id = $this->input->post('type_id');

            if (empty($type_id)) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสประเภทเอกสาร']);
                exit;
            }

            // ตรวจสอบว่ามีการใช้งานอยู่หรือไม่
            $this->db->where('esv_ods_type_id', $type_id);
            $usage_count = $this->db->count_all_results('tbl_esv_ods');

            if ($usage_count > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => "ไม่สามารถลบได้ เนื่องจากมีเอกสาร {$usage_count} รายการใช้ประเภทนี้อยู่"
                ]);
                exit;
            }

            // ลบข้อมูล
            $this->db->where('esv_type_id', $type_id);
            $result = $this->db->delete('tbl_esv_type');

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'ลบประเภทเอกสารสำเร็จ']);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบ']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in delete_document_type: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }

    /**
     * ลบหมวดหมู่เอกสาร (AJAX)
     */
    public function delete_category()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_delete_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูล']);
                exit;
            }

            $category_id = $this->input->post('category_id');

            if (empty($category_id)) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสหมวดหมู่เอกสาร']);
                exit;
            }

            // ตรวจสอบว่ามีการใช้งานอยู่หรือไม่
            $this->db->where('esv_ods_category_id', $category_id);
            $usage_count = $this->db->count_all_results('tbl_esv_ods');

            if ($usage_count > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => "ไม่สามารถลบได้ เนื่องจากมีเอกสาร {$usage_count} รายการใช้หมวดหมู่นี้อยู่"
                ]);
                exit;
            }

            // ลบข้อมูล
            $this->db->where('esv_category_id', $category_id);
            $result = $this->db->delete('tbl_esv_category');

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'ลบหมวดหมู่เอกสารสำเร็จ']);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบ']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in delete_category: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }

    /**
     * Helper: ดึงข้อมูลเจ้าหน้าที่
     */
    private function get_staff_data($m_id)
    {
        try {
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');

            return $this->db->get()->row();

        } catch (Exception $e) {
            log_message('error', 'Error getting staff data: ' . $e->getMessage());
            return null;
        }
    }





    /**
     * หน้าจัดการแบบฟอร์ม
     */
    public function manage_forms()
    {
        try {
            // ตรวจสอบการ login และสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ดึงข้อมูลเจ้าหน้าที่และตรวจสอบสิทธิ์
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status, grant_user_ref_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบสิทธิ์การจัดการ
            if (!$this->check_esv_handle_permission($staff_check)) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์จัดการข้อมูลแบบฟอร์ม');
                redirect('Esv_ods/admin_management');
                return;
            }

            // เตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_navbar_data();

            // ดึงรายการแบบฟอร์มทั้งหมด
            $data['forms'] = $this->get_all_forms_with_details();
            $data['document_types'] = $this->get_document_types_for_category();
            $data['categories'] = $this->get_categories_for_forms();

            // สิทธิ์การใช้งาน
            $data['can_add'] = true;
            $data['can_edit'] = true;
            $data['can_delete'] = $this->check_esv_delete_permission($staff_check);

            // ข้อมูลเจ้าหน้าที่
            $user_info_object = $this->create_complete_user_info($staff_check);
            $data['is_logged_in'] = true;
            $data['user_type'] = 'staff';
            $data['user_info'] = $user_info_object;
            $data['current_user'] = $user_info_object;
            $data['staff_data'] = $user_info_object;

            // Page metadata
            $data['page_title'] = 'จัดการแบบฟอร์ม';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('Dashboard')],
                ['title' => 'จัดการเอกสารออนไลน์', 'url' => site_url('Esv_ods/admin_management')],
                ['title' => 'จัดการแบบฟอร์ม', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');
            $data['warning_message'] = $this->session->flashdata('warning_message');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/esv_manage_forms', $data);
            $this->load->view('reports/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in manage_forms: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า');
            redirect('Esv_ods/admin_management');
        }
    }

    /**
     * บันทึกแบบฟอร์ม (AJAX)
     */
    public function save_form()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_handle_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ดำเนินการ']);
                exit;
            }

            // รับข้อมูล
            $form_id = $this->input->post('form_id');
            $form_name = trim($this->input->post('form_name'));
            $form_description = trim($this->input->post('form_description'));
            $form_type_id = $this->input->post('form_type_id');
            $form_category_id = $this->input->post('form_category_id');
            $form_order = (int) $this->input->post('form_order');
            $form_status = $this->input->post('form_status');

            // Validation
            if (empty($form_name)) {
                echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อแบบฟอร์ม']);
                exit;
            }

            // ตรวจสอบไฟล์
            $file_uploaded = false;
            $file_info = null;

            if (!empty($_FILES['form_file']['name'])) {
                $file_info = $this->handle_form_file_upload();
                if (!$file_info['success']) {
                    echo json_encode(['success' => false, 'message' => $file_info['message']]);
                    exit;
                }
                $file_uploaded = true;
            } elseif (empty($form_id)) {
                // กรณีเพิ่มใหม่ต้องมีไฟล์
                echo json_encode(['success' => false, 'message' => 'กรุณาเลือกไฟล์แบบฟอร์ม']);
                exit;
            }

            $updated_by = trim($staff_data->m_fname . ' ' . $staff_data->m_lname);

            // เตรียมข้อมูล
            $data = [
                'form_name' => $form_name,
                'form_description' => $form_description ?: null,
                'form_type_id' => !empty($form_type_id) ? $form_type_id : null,
                'form_category_id' => !empty($form_category_id) ? $form_category_id : null,
                'form_order' => $form_order,
                'form_status' => $form_status ?: 'active',
                'form_updated_by' => $updated_by,
                'form_updated_at' => date('Y-m-d H:i:s')
            ];

            // เพิ่มข้อมูลไฟล์ถ้ามีการอัปโหลด
            if ($file_uploaded) {
                $data['form_file'] = $file_info['data']['file_name'];
                $data['form_file_original'] = $file_info['data']['original_name'];
                $data['form_file_size'] = $file_info['data']['file_size'];
                $data['form_file_path'] = $file_info['data']['file_path'];
            }

            if (empty($form_id)) {
                // เพิ่มใหม่
                $data['form_created_by'] = $updated_by;
                $data['form_created_at'] = date('Y-m-d H:i:s');

                $result = $this->db->insert('tbl_esv_forms', $data);
                $message = 'เพิ่มแบบฟอร์มสำเร็จ';
            } else {
                // แก้ไข
                $this->db->where('form_id', $form_id);
                $result = $this->db->update('tbl_esv_forms', $data);
                $message = 'อัปเดตแบบฟอร์มสำเร็จ';
            }

            if ($result) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in save_form: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }

    /**
     * ลบแบบฟอร์ม (AJAX)
     */
    public function delete_form()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่ได้รับสิทธิ์']);
                exit;
            }

            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_delete_permission($staff_data)) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูล']);
                exit;
            }

            $form_id = $this->input->post('form_id');

            if (empty($form_id)) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสแบบฟอร์ม']);
                exit;
            }

            // ดึงข้อมูลแบบฟอร์มเพื่อลบไฟล์
            $this->db->select('form_file_path');
            $this->db->from('tbl_esv_forms');
            $this->db->where('form_id', $form_id);
            $form = $this->db->get()->row();

            if (!$form) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลแบบฟอร์ม']);
                exit;
            }

            // ลบข้อมูลจากฐานข้อมูล
            $this->db->where('form_id', $form_id);
            $result = $this->db->delete('tbl_esv_forms');

            if ($result) {
                // ลบไฟล์จริง
                if (!empty($form->form_file_path) && file_exists($form->form_file_path)) {
                    unlink($form->form_file_path);
                }

                echo json_encode(['success' => true, 'message' => 'ลบแบบฟอร์มสำเร็จ']);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบ']);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in delete_form: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในระบบ']);
        }

        exit;
    }

    /**
     * ดาวน์โหลดแบบฟอร์ม
     */
    public function download_form($form_id = null)
    {
        try {
            if (empty($form_id)) {
                show_404();
                return;
            }

            // ดึงข้อมูลแบบฟอร์ม
            $this->db->select('*');
            $this->db->from('tbl_esv_forms');
            $this->db->where('form_id', $form_id);
            $this->db->where('form_status', 'active');
            $form = $this->db->get()->row();

            if (!$form || empty($form->form_file_path)) {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($form->form_file_path)) {
                show_404();
                return;
            }

            // อัปเดตจำนวนการดาวน์โหลด
            $this->db->where('form_id', $form_id);
            $this->db->set('form_download_count', 'COALESCE(form_download_count, 0) + 1', FALSE);
            $this->db->update('tbl_esv_forms');

            // บันทึก log
            log_message('info', "Form downloaded: {$form->form_name} (ID: {$form_id})");

            // ส่งไฟล์
            $this->load->helper('download');
            force_download($form->form_file_original ?: $form->form_file, file_get_contents($form->form_file_path));

        } catch (Exception $e) {
            log_message('error', 'Error in download_form: ' . $e->getMessage());
            show_404();
        }
    }

    /**
     * ดึงรายการแบบฟอร์มทั้งหมดพร้อมรายละเอียด
     */
    private function get_all_forms_with_details()
    {
        try {
            $this->db->select('f.*, t.esv_type_name as type_name, t.esv_type_icon as type_icon, 
                          t.esv_type_color as type_color, c.esv_category_name as category_name');
            $this->db->from('tbl_esv_forms f');
            $this->db->join('tbl_esv_type t', 'f.form_type_id = t.esv_type_id', 'left');
            $this->db->join('tbl_esv_category c', 'f.form_category_id = c.esv_category_id', 'left');
            $this->db->order_by('f.form_order', 'ASC');
            $this->db->order_by('f.form_name', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting all forms with details: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงหมวดหมู่สำหรับแบบฟอร์ม
     */
    private function get_categories_for_forms()
    {
        try {
            $this->db->select('esv_category_id, esv_category_name');
            $this->db->from('tbl_esv_category');
            $this->db->where('esv_category_status', 'active');
            $this->db->order_by('esv_category_order', 'ASC');
            $this->db->order_by('esv_category_name', 'ASC');
            $query = $this->db->get();

            return $query->result();

        } catch (Exception $e) {
            log_message('error', 'Error getting categories for forms: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * จัดการอัปโหลดไฟล์แบบฟอร์ม
     */
    private function handle_form_file_upload()
    {
        try {
            $upload_path = './docs/esv_forms/';

            // สร้างโฟลเดอร์ถ้าไม่มี
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'pdf|doc|docx|xls|xlsx',
                'max_size' => 5120, // 5MB
                'encrypt_name' => TRUE,
                'remove_spaces' => TRUE
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('form_file')) {
                $upload_data = $this->upload->data();

                return [
                    'success' => true,
                    'data' => [
                        'file_name' => $upload_data['file_name'],
                        'original_name' => $upload_data['orig_name'],
                        'file_size' => $upload_data['file_size'] * 1024,
                        'file_path' => 'docs/esv_forms/' . $upload_data['file_name'],
                        'file_type' => $upload_data['file_type']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => strip_tags($this->upload->display_errors())
                ];
            }

        } catch (Exception $e) {
            log_message('error', 'Error handling form file upload: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'
            ];
        }
    }


    /**
     * API: ดึงแบบฟอร์มตามประเภทและหมวดหมู่ (สำหรับหน้ายื่นเอกสาร)
     */
    public function get_forms_by_type_category()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $type_id = $this->input->post('type_id');
            $category_id = $this->input->post('category_id');

            $this->db->select('form_id, form_name, form_description, form_file_original, form_download_count');
            $this->db->from('tbl_esv_forms');
            $this->db->where('form_status', 'active');

            // กรองตามประเภทเอกสาร
            if (!empty($type_id)) {
                $this->db->where('form_type_id', $type_id);
            }

            // กรองตามหมวดหมู่
            if (!empty($category_id)) {
                $this->db->where('form_category_id', $category_id);
            }

            $this->db->order_by('form_order', 'ASC');
            $this->db->order_by('form_name', 'ASC');
            $query = $this->db->get();

            $forms = $query->result();

            // เพิ่มข้อมูลเสริม
            foreach ($forms as $form) {
                $form->download_url = site_url('Esv_ods/download_form/' . $form->form_id);
                $form->download_count_text = number_format($form->form_download_count) . ' ครั้ง';
            }

            echo json_encode([
                'success' => true,
                'forms' => $forms,
                'total' => count($forms)
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in get_forms_by_type_category: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'forms' => [],
                'total' => 0,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * ดึงแบบฟอร์มยอดนิยม (สำหรับแสดงในหน้าหลัก)
     */
    public function get_popular_forms($limit = 4)
    {
        try {
            $this->db->select('f.form_id, f.form_name, f.form_description, f.form_file_original, 
                          f.form_download_count, t.esv_type_name, c.esv_category_name');
            $this->db->from('tbl_esv_forms f');
            $this->db->join('tbl_esv_type t', 'f.form_type_id = t.esv_type_id', 'left');
            $this->db->join('tbl_esv_category c', 'f.form_category_id = c.esv_category_id', 'left');
            $this->db->where('f.form_status', 'active');
            $this->db->order_by('f.form_download_count', 'DESC');
            $this->db->limit($limit);
            $query = $this->db->get();

            $forms = $query->result();

            // เพิ่มข้อมูลเสริม
            foreach ($forms as $form) {
                $form->download_url = site_url('Esv_ods/download_form/' . $form->form_id);
                $form->download_count_text = number_format($form->form_download_count) . ' ครั้ง';
            }

            return $forms;

        } catch (Exception $e) {
            log_message('error', 'Error getting popular forms: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงแบบฟอร์มทั้งหมดสำหรับ Frontend
     */
    public function get_all_forms_frontend()
    {
        try {
            $this->db->select('f.form_id, f.form_name, f.form_description, f.form_file_original, 
                          f.form_download_count, t.esv_type_name, t.esv_type_color, t.esv_type_icon,
                          c.esv_category_name');
            $this->db->from('tbl_esv_forms f');
            $this->db->join('tbl_esv_type t', 'f.form_type_id = t.esv_type_id', 'left');
            $this->db->join('tbl_esv_category c', 'f.form_category_id = c.esv_category_id', 'left');
            $this->db->where('f.form_status', 'active');
            $this->db->order_by('f.form_order', 'ASC');
            $this->db->order_by('f.form_name', 'ASC');
            $query = $this->db->get();

            $forms = $query->result();

            // จัดกลุ่มตามประเภทเอกสาร
            $grouped_forms = [];
            foreach ($forms as $form) {
                $type_name = $form->esv_type_name ?: 'ทั่วไป';
                if (!isset($grouped_forms[$type_name])) {
                    $grouped_forms[$type_name] = [
                        'type_name' => $type_name,
                        'type_color' => $form->esv_type_color ?: '#8b9cc7',
                        'type_icon' => $form->esv_type_icon ?: 'fas fa-wpforms',
                        'forms' => []
                    ];
                }

                // เพิ่มข้อมูลเสริม
                $form->download_url = site_url('Esv_ods/download_form/' . $form->form_id);
                $form->download_count_text = number_format($form->form_download_count) . ' ครั้ง';

                $grouped_forms[$type_name]['forms'][] = $form;
            }

            return array_values($grouped_forms);

        } catch (Exception $e) {
            log_message('error', 'Error getting all forms frontend: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * หน้าแสดงแบบฟอร์มสำหรับประชาชน
     */
    public function forms()
    {
        try {
            // เตรียมข้อมูลพื้นฐานสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            // ดึงข้อมูลแบบฟอร์ม
            $data['grouped_forms'] = $this->get_all_forms_frontend();
            $data['popular_forms'] = $this->get_popular_forms(4);
            $data['total_forms'] = count($this->get_all_forms_frontend());

            // ข้อมูลประเภทและหมวดหมู่สำหรับ filter
            $data['document_types'] = $this->get_document_types();
            $data['categories'] = $this->get_categories_simple();

            $data['page_title'] = 'แบบฟอร์มเอกสาร';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'แบบฟอร์มเอกสาร', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/esv_forms', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in forms page: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    /**
     * สร้างการแจ้งเตือนเมื่อมีการดาวน์โหลดแบบฟอร์ม (ถ้าต้องการ)
     */
    private function log_form_download($form_id, $form_name, $user_info = null)
    {
        try {
            // บันทึก log การดาวน์โหลด
            $log_data = [
                'action' => 'FORM_DOWNLOADED',
                'form_id' => $form_id,
                'form_name' => $form_name,
                'downloaded_by' => $user_info['name'] ?? 'Anonymous',
                'user_type' => $user_info['user_type'] ?? 'guest',
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'downloaded_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Form download log: ' . json_encode($log_data, JSON_UNESCAPED_UNICODE));

            // หากต้องการบันทึกลงฐานข้อมูล
            if ($this->db->table_exists('tbl_download_log')) {
                $this->db->insert('tbl_download_log', [
                    'table_name' => 'tbl_esv_forms',
                    'record_id' => $form_id,
                    'action' => 'download',
                    'log_data' => json_encode($log_data, JSON_UNESCAPED_UNICODE),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Error logging form download: ' . $e->getMessage());
        }
    }




    public function view_form_file($form_id = null)
    {
        try {
            // ตรวจสอบการ login ของเจ้าหน้าที่
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์
            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_handle_permission($staff_data)) {
                show_404();
                return;
            }

            if (empty($form_id)) {
                show_404();
                return;
            }

            // ดึงข้อมูลฟอร์ม
            $this->db->select('*');
            $this->db->from('tbl_esv_forms');
            $this->db->where('form_id', $form_id);
            $this->db->where('form_status', 'active');
            $form = $this->db->get()->row();

            if (!$form || empty($form->form_file_path)) {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($form->form_file_path)) {
                show_404();
                return;
            }

            // อัปเดตจำนวนการดู
            $this->db->where('form_id', $form_id);
            $this->db->set('form_view_count', 'COALESCE(form_view_count, 0) + 1', FALSE);
            $this->db->update('tbl_esv_forms');

            // บันทึก log
            log_message('info', "Form file viewed: {$form->form_name} (ID: {$form_id}) by staff {$m_id}");

            // กำหนด Content-Type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $form->form_file_path);
            finfo_close($finfo);

            // ถ้าไม่สามารถตรวจจับได้ ใช้ extension
            if (!$mime_type) {
                $extension = strtolower(pathinfo($form->form_file_original, PATHINFO_EXTENSION));
                $mime_types = [
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ];
                $mime_type = $mime_types[$extension] ?? 'application/octet-stream';
            }

            // ส่ง headers สำหรับแสดงในเบราว์เซอร์
            header('Content-Type: ' . $mime_type);
            header('Content-Length: ' . filesize($form->form_file_path));
            header('Content-Disposition: inline; filename="' . $form->form_file_original . '"');
            header('Cache-Control: private, max-age=3600, must-revalidate');
            header('Pragma: public');

            // เพิ่ม headers สำหรับ security
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');

            // ส่งไฟล์
            readfile($form->form_file_path);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Error in view_form_file: ' . $e->getMessage());
            show_404();
        }
    }

    /**
     * ดูไฟล์ฟอร์มแบบ Public (ไม่ต้องล็อกอิน)
     */
    public function view_public_form($form_id = null)
    {
        try {
            if (empty($form_id)) {
                show_404();
                return;
            }

            // ดึงข้อมูลฟอร์ม (เฉพาะที่เปิดใช้งาน)
            $this->db->select('*');
            $this->db->from('tbl_esv_forms');
            $this->db->where('form_id', $form_id);
            $this->db->where('form_status', 'active');
            $form = $this->db->get()->row();

            if (!$form || empty($form->form_file_path)) {
                show_404();
                return;
            }

            // ตรวจสอบว่าไฟล์มีอยู่จริง
            if (!file_exists($form->form_file_path)) {
                show_404();
                return;
            }

            // อัปเดตจำนวนการดู
            $this->db->where('form_id', $form_id);
            $this->db->set('form_view_count', 'COALESCE(form_view_count, 0) + 1', FALSE);
            $this->db->update('tbl_esv_forms');

            // บันทึก log
            log_message('info', "Public form file viewed: {$form->form_name} (ID: {$form_id})");

            // กำหนด Content-Type
            $extension = strtolower(pathinfo($form->form_file_original, PATHINFO_EXTENSION));
            $mime_types = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            $mime_type = $mime_types[$extension] ?? 'application/octet-stream';

            // ส่ง headers
            header('Content-Type: ' . $mime_type);
            header('Content-Length: ' . filesize($form->form_file_path));
            header('Content-Disposition: inline; filename="' . $form->form_file_original . '"');
            header('Cache-Control: public, max-age=3600');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');

            // ส่งไฟล์
            readfile($form->form_file_path);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Error in view_public_form: ' . $e->getMessage());
            show_404();
        }
    }

    /**
     * API: ตรวจสอบว่าไฟล์สามารถแสดงในเบราว์เซอร์ได้หรือไม่
     */
    public function check_form_viewable($form_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // ตรวจสอบสิทธิ์
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                echo json_encode(['viewable' => false, 'reason' => 'unauthorized']);
                exit;
            }

            if (empty($form_id)) {
                echo json_encode(['viewable' => false, 'reason' => 'no_form_id']);
                exit;
            }

            // ดึงข้อมูลฟอร์ม
            $this->db->select('form_file, form_file_original, form_file_path');
            $this->db->from('tbl_esv_forms');
            $this->db->where('form_id', $form_id);
            $this->db->where('form_status', 'active');
            $form = $this->db->get()->row();

            if (!$form) {
                echo json_encode(['viewable' => false, 'reason' => 'form_not_found']);
                exit;
            }

            if (!file_exists($form->form_file_path)) {
                echo json_encode(['viewable' => false, 'reason' => 'file_not_found']);
                exit;
            }

            // ตรวจสอบประเภทไฟล์
            $extension = strtolower(pathinfo($form->form_file_original, PATHINFO_EXTENSION));
            $viewable_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
            $is_viewable = in_array($extension, $viewable_types);

            $result = [
                'viewable' => $is_viewable,
                'file_type' => $extension,
                'file_name' => $form->form_file_original,
                'file_size' => filesize($form->form_file_path),
                'view_url' => site_url("Esv_ods/view_form_file/{$form_id}"),
                'download_url' => site_url("Esv_ods/download_form/{$form_id}")
            ];

            if (!$is_viewable) {
                $result['reason'] = 'unsupported_file_type';
            }

            echo json_encode($result, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in check_form_viewable: ' . $e->getMessage());
            echo json_encode(['viewable' => false, 'reason' => 'system_error']);
        }

        exit;
    }

    /**
     * แสดงหน้าดูตัวอย่างฟอร์มแบบเต็มหน้า (สำหรับกรณีที่ iframe ไม่ทำงาน)
     */
    public function fullscreen_form_viewer($form_id = null)
    {
        try {
            // ตรวจสอบการ login
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบ');
                redirect('User');
                return;
            }

            // ตรวจสอบสิทธิ์
            $staff_data = $this->get_staff_data($m_id);
            if (!$staff_data || !$this->check_esv_handle_permission($staff_data)) {
                show_404();
                return;
            }

            if (empty($form_id)) {
                show_404();
                return;
            }

            // ดึงข้อมูลฟอร์ม
            $this->db->select('*');
            $this->db->from('tbl_esv_forms');
            $this->db->where('form_id', $form_id);
            $this->db->where('form_status', 'active');
            $form = $this->db->get()->row();

            if (!$form) {
                show_404();
                return;
            }

            // เตรียมข้อมูล
            $data = [
                'form' => $form,
                'view_url' => site_url("Esv_ods/view_form_file/{$form_id}"),
                'download_url' => site_url("Esv_ods/download_form/{$form_id}"),
                'back_url' => site_url('Esv_ods/manage_forms'),
                'page_title' => 'ดูตัวอย่างฟอร์ม: ' . $form->form_name
            ];

            // โหลด view แบบเต็มหน้า
            $this->load->view('reports/form_fullscreen_viewer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in fullscreen_form_viewer: ' . $e->getMessage());
            show_404();
        }
    }






    /**
     * หน้าแบบฟอร์มออนไลน์สำหรับประชาชน (แบบใหม่)
     */
    public function forms_online()
    {
        try {
            // เตรียมข้อมูลพื้นฐานสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            // ดึงข้อมูลแบบฟอร์มจัดกลุ่มแบบใหม่: ประเภท → หมวดหมู่ → แบบฟอร์ม
            $data['document_structure'] = $this->get_document_structure_for_public();

            // ดึงแบบฟอร์มยอดนิยม
            $data['popular_forms'] = $this->get_popular_forms_for_public(4);

            // สถิติแบบฟอร์ม
            $data['statistics'] = $this->get_forms_statistics();

            // ข้อมูลหน้า
            $data['page_title'] = 'แบบฟอร์มออนไลน์';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'แบบฟอร์มออนไลน์', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/forms_online', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in forms_online: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    /**
     * ดึงโครงสร้างเอกสาร: ประเภท → หมวดหมู่ → แบบฟอร์ม
     */
    private function get_document_structure_for_public()
    {
        try {
            // ดึงข้อมูลแบบฟอร์มพร้อม join ตาราง
            $this->db->select('
            f.form_id,
            f.form_name,
            f.form_description,
            f.form_file,
            f.form_file_original,
            f.form_download_count,
            f.form_view_count,
            f.form_order,
            t.esv_type_id,
            t.esv_type_name,
            t.esv_type_icon,
            t.esv_type_color,
            t.esv_type_order,
            t.esv_type_description,
            c.esv_category_id,
            c.esv_category_name,
            c.esv_category_description,
            c.esv_category_fee,
            c.esv_category_process_days,
            c.esv_category_order,
            c.esv_category_icon,
            c.esv_category_color
        ');
            $this->db->from('tbl_esv_forms f');
            $this->db->join('tbl_esv_type t', 'f.form_type_id = t.esv_type_id', 'left');
            $this->db->join('tbl_esv_category c', 'f.form_category_id = c.esv_category_id', 'left');
            $this->db->where('f.form_status', 'active');
            $this->db->where('t.esv_type_status', 'active');
            $this->db->where('c.esv_category_status', 'active');
            $this->db->order_by('t.esv_type_order', 'ASC');
            $this->db->order_by('c.esv_category_order', 'ASC');
            $this->db->order_by('f.form_order', 'ASC');
            $this->db->order_by('f.form_name', 'ASC');

            $query = $this->db->get();
            $forms = $query->result();

            // จัดโครงสร้างข้อมูล: ประเภท → หมวดหมู่ → แบบฟอร์ม
            $structure = [];

            foreach ($forms as $form) {
                $type_id = $form->esv_type_id ?: 'general';
                $category_id = $form->esv_category_id ?: 'general';

                // สร้างกลุ่มประเภทเอกสาร
                if (!isset($structure[$type_id])) {
                    $structure[$type_id] = [
                        'type_id' => $type_id,
                        'type_name' => $form->esv_type_name ?: 'แบบฟอร์มทั่วไป',
                        'type_description' => $form->esv_type_description ?: '',
                        'type_icon' => $form->esv_type_icon ?: 'fas fa-wpforms',
                        'type_color' => $form->esv_type_color ?: '#667eea',
                        'type_order' => $form->esv_type_order ?: 999,
                        'categories' => [],
                        'total_categories' => 0,
                        'total_forms' => 0
                    ];
                }

                // สร้างกลุ่มหมวดหมู่เอกสาร
                if (!isset($structure[$type_id]['categories'][$category_id])) {
                    $structure[$type_id]['categories'][$category_id] = [
                        'category_id' => $category_id,
                        'category_name' => $form->esv_category_name ?: 'ทั่วไป',
                        'category_description' => $form->esv_category_description ?: '',
                        'category_fee' => floatval($form->esv_category_fee) ?: 0,
                        'category_process_days' => intval($form->esv_category_process_days) ?: 0,
                        'category_icon' => $form->esv_category_icon ?: 'fas fa-folder',
                        'category_color' => $form->esv_category_color ?: $form->esv_type_color ?: '#667eea',
                        'category_order' => $form->esv_category_order ?: 999,
                        'forms' => []
                    ];
                    $structure[$type_id]['total_categories']++;
                }

                // เพิ่มแบบฟอร์ม
                $form_data = [
                    'form_id' => $form->form_id,
                    'form_name' => $form->form_name,
                    'form_description' => $form->form_description,
                    'form_file' => $form->form_file,
                    'form_file_original' => $form->form_file_original,
                    'download_count' => intval($form->form_download_count),
                    'view_count' => intval($form->form_view_count),
                    'download_url' => site_url('Esv_ods/download_form/' . $form->form_id),
                    'view_url' => site_url('Esv_ods/view_public_form/' . $form->form_id),
                    'download_count_text' => number_format($form->form_download_count) . ' ครั้ง',
                    'file_size' => $this->get_file_size_text($form->form_file)
                ];

                $structure[$type_id]['categories'][$category_id]['forms'][] = $form_data;
                $structure[$type_id]['total_forms']++;
            }

            // เรียงลำดับอีกครั้ง
            uasort($structure, function ($a, $b) {
                return $a['type_order'] - $b['type_order'];
            });

            foreach ($structure as &$type_data) {
                uasort($type_data['categories'], function ($a, $b) {
                    return $a['category_order'] - $b['category_order'];
                });
            }

            return $structure;

        } catch (Exception $e) {
            log_message('error', 'Error getting document structure: ' . $e->getMessage());
            return [];
        }
    }





    private function get_popular_forms_for_public($limit = 6)
    {
        try {
            $this->db->select('
            f.form_id,
            f.form_name,
            f.form_description,
            f.form_file_original,
            f.form_download_count,
            t.esv_type_name,
            t.esv_type_icon,
            t.esv_type_color,
            c.esv_category_name
        ');
            $this->db->from('tbl_esv_forms f');
            $this->db->join('tbl_esv_type t', 'f.form_type_id = t.esv_type_id', 'left');
            $this->db->join('tbl_esv_category c', 'f.form_category_id = c.esv_category_id', 'left');
            $this->db->where('f.form_status', 'active');
            $this->db->where('f.form_download_count >', 0);
            $this->db->order_by('f.form_download_count', 'DESC');
            $this->db->limit($limit);

            $query = $this->db->get();
            $forms = $query->result();

            foreach ($forms as $form) {
                $form->download_url = site_url('Esv_ods/download_form/' . $form->form_id);
                $form->view_url = site_url('Esv_ods/view_public_form/' . $form->form_id);
                $form->download_count_text = number_format($form->form_download_count) . ' ครั้ง';
                $form->category_display = $form->esv_category_name ?: 'ทั่วไป';
            }

            return $forms;

        } catch (Exception $e) {
            log_message('error', 'Error getting popular forms: ' . $e->getMessage());
            return [];
        }
    }






    private function get_forms_statistics()
    {
        try {
            // นับจำนวนแบบฟอร์มทั้งหมด
            $this->db->where('form_status', 'active');
            $total_forms = $this->db->count_all_results('tbl_esv_forms');

            // นับจำนวนประเภทเอกสาร
            $this->db->where('esv_type_status', 'active');
            $total_types = $this->db->count_all_results('tbl_esv_type');

            // นับจำนวนหมวดหมู่เอกสาร
            $this->db->where('esv_category_status', 'active');
            $total_categories = $this->db->count_all_results('tbl_esv_category');

            // นับจำนวนการดาวน์โหลดทั้งหมด
            $this->db->select_sum('form_download_count');
            $this->db->where('form_status', 'active');
            $query = $this->db->get('tbl_esv_forms');
            $result = $query->row();
            $total_downloads = intval($result->form_download_count) ?: 0;

            return [
                'total_forms' => $total_forms,
                'total_types' => $total_types,
                'total_categories' => $total_categories,
                'total_downloads' => $total_downloads
            ];

        } catch (Exception $e) {
            log_message('error', 'Error getting forms statistics: ' . $e->getMessage());
            return [
                'total_forms' => 0,
                'total_types' => 0,
                'total_categories' => 0,
                'total_downloads' => 0
            ];
        }
    }


    private function get_file_size_text($filename)
    {
        try {
            if (empty($filename))
                return '';

            $file_path = './docs/esv_forms/' . $filename;
            if (file_exists($file_path)) {
                $bytes = filesize($file_path);
                if ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 1) . ' MB';
                } elseif ($bytes >= 1024) {
                    return number_format($bytes / 1024, 1) . ' KB';
                } else {
                    return $bytes . ' B';
                }
            }
            return '';
        } catch (Exception $e) {
            return '';
        }
    }
    /**
     * ดึงแบบฟอร์มจัดกลุ่มตามประเภทและหมวดหมู่
     */
    private function get_grouped_forms_for_public()
    {
        try {
            $this->db->select('
            f.form_id,
            f.form_name,
            f.form_description,
            f.form_file,
            f.form_file_original,
            f.form_download_count,
            f.form_view_count,
            f.form_order,
            t.esv_type_id,
            t.esv_type_name,
            t.esv_type_icon,
            t.esv_type_color,
            t.esv_type_order,
            c.esv_category_id,
            c.esv_category_name,
            c.esv_category_fee,
            c.esv_category_process_days,
            c.esv_category_order
        ');
            $this->db->from('tbl_esv_forms f');
            $this->db->join('tbl_esv_type t', 'f.form_type_id = t.esv_type_id', 'left');
            $this->db->join('tbl_esv_category c', 'f.form_category_id = c.esv_category_id', 'left');
            $this->db->where('f.form_status', 'active');
            $this->db->where('t.esv_type_status', 'active');
            $this->db->order_by('t.esv_type_order', 'ASC');
            $this->db->order_by('c.esv_category_order', 'ASC');
            $this->db->order_by('f.form_order', 'ASC');
            $this->db->order_by('f.form_name', 'ASC');

            $query = $this->db->get();
            $forms = $query->result();

            // จัดกลุ่มข้อมูล
            $grouped = [];

            foreach ($forms as $form) {
                $type_name = $form->esv_type_name ?: 'แบบฟอร์มทั่วไป';
                $type_id = $form->esv_type_id ?: 'general';

                // สร้างกลุ่มประเภทถ้ายังไม่มี
                if (!isset($grouped[$type_id])) {
                    $grouped[$type_id] = [
                        'type_id' => $type_id,
                        'type_name' => $type_name,
                        'type_icon' => $form->esv_type_icon ?: 'fas fa-wpforms',
                        'type_color' => $form->esv_type_color ?: '#667eea',
                        'type_order' => $form->esv_type_order ?: 999,
                        'categories' => [],
                        'total_forms' => 0
                    ];
                }

                $category_name = $form->esv_category_name ?: 'ทั่วไป';
                $category_id = $form->esv_category_id ?: 'general';

                // สร้างกลุ่มหมวดหมู่ถ้ายังไม่มี
                if (!isset($grouped[$type_id]['categories'][$category_id])) {
                    $grouped[$type_id]['categories'][$category_id] = [
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'category_fee' => $form->esv_category_fee ?: 0,
                        'category_process_days' => $form->esv_category_process_days ?: 0,
                        'category_order' => $form->esv_category_order ?: 999,
                        'forms' => []
                    ];
                }

                // เพิ่มข้อมูลแบบฟอร์ม
                $form_data = [
                    'form_id' => $form->form_id,
                    'form_name' => $form->form_name,
                    'form_description' => $form->form_description,
                    'form_file' => $form->form_file,
                    'form_file_original' => $form->form_file_original,
                    'download_count' => intval($form->form_download_count),
                    'view_count' => intval($form->form_view_count),
                    'download_url' => site_url('Esv_ods/download_form/' . $form->form_id),
                    'view_url' => site_url('Esv_ods/view_public_form/' . $form->form_id),
                    'download_count_text' => number_format($form->form_download_count) . ' ครั้ง'
                ];

                $grouped[$type_id]['categories'][$category_id]['forms'][] = $form_data;
                $grouped[$type_id]['total_forms']++;
            }

            // เรียงลำดับอีกครั้ง
            uasort($grouped, function ($a, $b) {
                return $a['type_order'] - $b['type_order'];
            });

            foreach ($grouped as &$type_data) {
                uasort($type_data['categories'], function ($a, $b) {
                    return $a['category_order'] - $b['category_order'];
                });
            }

            return $grouped;

        } catch (Exception $e) {
            log_message('error', 'Error getting grouped forms: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * นับจำนวนแบบฟอร์มทั้งหมด
     */
    private function count_total_active_forms()
    {
        try {
            $this->db->where('form_status', 'active');
            return $this->db->count_all_results('tbl_esv_forms');
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * นับจำนวนการดาวน์โหลดทั้งหมด
     */
    private function count_total_downloads()
    {
        try {
            $this->db->select_sum('form_download_count');
            $this->db->where('form_status', 'active');
            $query = $this->db->get('tbl_esv_forms');
            $result = $query->row();
            return intval($result->form_download_count) ?: 0;
        } catch (Exception $e) {
            return 0;
        }
    }






    /**
     * Track การดาวน์โหลดแบบฟอร์ม (AJAX)
     */
    public function track_form_download()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $form_id = $this->input->post('form_id');

            if (!empty($form_id)) {
                // อัปเดตจำนวนการดาวน์โหลด
                $this->db->where('form_id', $form_id);
                $this->db->set('form_download_count', 'COALESCE(form_download_count, 0) + 1', FALSE);
                $this->db->update('tbl_esv_forms');

                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }

        exit;
    }




}