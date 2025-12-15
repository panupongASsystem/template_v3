<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assessment extends CI_Controller
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

        // ✅ เพิ่ม: โหลด reCAPTCHA Library
        $this->load->library('recaptcha_lib');

        // ✅ เพิ่ม: ตั้งค่า reCAPTCHA จาก database
        if (function_exists('get_config_value')) {
            $recaptcha_config = [
                'secret_key' => get_config_value('secret_key_recaptchar'),
                'site_key' => get_config_value('recaptcha'),
                'enabled' => true,
                'debug_mode' => (ENVIRONMENT === 'development')
            ];
            $this->recaptcha_lib->initialize($recaptcha_config);
            log_message('debug', 'reCAPTCHA Library initialized for Assessment');
        }

        // โหลด models ทั้งหมด (รวมเดิมและใหม่)
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



        $this->load->model('plan_pdl_model');
        $this->load->model('plan_pc3y_model');
        $this->load->model('plan_pds3y_model');
        $this->load->model('plan_pdpa_model');
        $this->load->model('plan_dpy_model');
        $this->load->model('plan_poa_model');
        $this->load->model('plan_pcra_model');
        $this->load->model('plan_pop_model');
        $this->load->model('plan_paca_model');
        $this->load->model('plan_psi_model');
        $this->load->model('plan_pmda_model');

        $this->load->model('canon_bgps_model');
        $this->load->model('canon_chh_model');
        $this->load->model('canon_ritw_model');
        $this->load->model('canon_market_model');
        $this->load->model('canon_rmwp_model');
        $this->load->model('canon_rcp_model');
        $this->load->model('canon_rcsp_model');

        $this->load->model('pbsv_cac_model');
        $this->load->model('pbsv_cig_model');
        $this->load->model('pbsv_cjc_model');
        $this->load->model('pbsv_utilities_model');
        $this->load->model('pbsv_sags_model');
        $this->load->model('pbsv_ahs_model');
        $this->load->model('pbsv_oppr_model');
        $this->load->model('pbsv_ems_model');
        $this->load->model('pbsv_gup_model');
        $this->load->model('pbsv_e_book_model');

        $this->load->model('operation_reauf_model');
        $this->load->model('p_sopopip_model');
        $this->load->model('operation_report_model');
        $this->load->model('p_sopopaortsr_model');
        $this->load->model('p_rpobuy_model');
        $this->load->model('p_rpo_model');
        $this->load->model('p_reb_model');
        $this->load->model('operation_sap_model');
        $this->load->model('operation_pm_model');
        $this->load->model('operation_mr_model');
        $this->load->model('operation_policy_hr_model');
        $this->load->model('video_model');
        $this->load->model('operation_am_hr_model');
        $this->load->model('operation_rdam_hr_model');
        $this->load->model('operation_cdm_model');
        $this->load->model('operation_po_model');
        $this->load->model('operation_eco_model');
        $this->load->model('operation_meeting_model');
        $this->load->model('operation_pgn_model');
        $this->load->model('operation_mcc_model');
        $this->load->model('operation_aca_model');
        $this->load->model('lpa_model');
        $this->load->model('ita_model');
        $this->load->model('operation_procurement_model');
        $this->load->model('operation_aa_model');
        $this->load->model('operation_aditn_model');

        $this->load->model('newsletter_model');
        $this->load->model('q_a_model', 'Q_a_model');
        $this->load->model('complain_model');
        $this->load->model('Queue_model', 'queue_model');
        $this->load->model('corruption_model');
        $this->load->model('suggestions_model');
        $this->load->model('questions_model');
        $this->load->model('site_map_model');
        $this->load->model('laws_ral_model');
        $this->load->model('laws_rl_folder_model');
        $this->load->model('laws_rl_file_model');
        $this->load->model('laws_rm_model');
        $this->load->model('laws_act_model');
        $this->load->model('laws_ec_model');
        $this->load->model('laws_la_model');
        $this->load->model('laws_model');
        $this->load->model('form_esv_model');
        $this->load->model('esv_ods_model');
        $this->load->model('ita_year_model');
        $this->load->model('km_model');

        $this->load->model('prov_local_doc_model');

        $this->load->model('elderly_aw_form_model');
        $this->load->model('elderly_aw_ods_model');
        $this->load->model('kid_aw_form_model');
        $this->load->model('kid_aw_ods_model');
        $this->load->model('odata_model');

        $this->load->model('intra_egp_model');
        $this->load->model('manual_esv_model');
        $this->load->model('procurement_egp_model');
        $this->load->model('finance_model');
        $this->load->model('taepts_model');
        $this->load->model('arevenuec_model');
        $this->load->model('assessment_model');
        $this->load->model('HotNews_model');
        $this->load->model('Weather_report_model');
        $this->load->model('calender_model');
        $this->load->model('banner_model');
        $this->load->model('background_personnel_model');
        $this->load->model('pbsv_statistics_model');
        $this->load->model('ethics_strategy_model');
        $this->load->model('plan_progress_model');
        $this->load->model('announce_oap_model');
        $this->load->model('announce_win_model');
        $this->load->model('member_public_model');

        // โหลด libraries และ helpers
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }

    // หน้าแสดงแบบประเมิน (ดึงข้อมูลจาก DB)
    public function index()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar อย่างปลอดภัย
            $data = $this->prepare_navbar_data_safe();

            // ไม่ต้องตรวจสอบ login - ให้ทุกคนเข้าตอบได้
            $data['is_logged_in'] = false;
            $data['user_info'] = null;
            $data['user_address'] = null;
            $data['user_type'] = 'guest';

            // ดึงข้อมูลแบบประเมินทั้งหมดจาก Database
            $data['assessment'] = $this->assessment_model->get_full_assessment();
            $data['settings'] = $this->assessment_model->get_all_settings();

            // **แก้ไขปัญหา: ลบ options ออกจากคำถาม textarea**
            foreach ($data['assessment'] as $category) {
                foreach ($category->questions as $question) {
                    if ($question->question_type === 'textarea') {
                        // ลบ options ที่อาจถูกสร้างมาผิดพลาด
                        unset($question->options);
                    }
                }
            }

            // ข้อมูลเพิ่มเติม
            $data['page_title'] = isset($data['settings']['site_title']) ? $data['settings']['site_title'] : 'แบบประเมินความพึงพอใจการให้บริการ';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'แบบประเมินความพึงพอใจ', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // ตรวจสอบ Activity Slider แบบปลอดภัย
            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view แบบเดียวกับ Queue
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/assessment_dynamic_form', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in assessment index: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    // หน้าขอบคุณ
    public function thank_you()
    {
        try {
            // กำหนดค่าเริ่มต้นสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            // ไม่ต้องตรวจสอบ login
            $data['is_logged_in'] = false;
            $data['user_info'] = null;
            $data['user_type'] = 'guest';

            // ดึงการตั้งค่า
            $data['settings'] = $this->assessment_model->get_all_settings();

            // ข้อมูลเพิ่มเติม
            $data['page_title'] = 'ขอบคุณสำหรับการประเมิน';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'แบบประเมินความพึงพอใจ', 'url' => site_url('assessment')],
                ['title' => 'ขอบคุณ', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');

            // ตรวจสอบ Activity Slider
            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/assessment_thank_you', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in thank_you page: ' . $e->getMessage());
            redirect('assessment');
        }
    }


    // ✅ แก้ไขเฉพาะฟังก์ชัน submit() - เพิ่ม reCAPTCHA check พร้อม action และ source
    public function submit()
    {
        // ✅ เพิ่มการตรวจสอบ reCAPTCHA ก่อนทำอย่างอื่น
        $recaptcha_token = $this->input->post('recaptcha_token') ?: $this->input->post('g-recaptcha-response');

        if (!empty($recaptcha_token)) {
            log_message('debug', "Verifying reCAPTCHA for assessment submission");

            // ✅ อ่านข้อมูล action และ source จาก JavaScript
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'assessment_submit';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'guest_assessment_form';
            $user_type_detected = $this->input->post('user_type_detected') ?: 'guest';

            log_message('debug', 'Assessment reCAPTCHA data from JavaScript: ' . json_encode([
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => $user_type_detected,
                'token_length' => strlen($recaptcha_token)
            ]));

            // ✅ กำหนด user type สำหรับ reCAPTCHA (Assessment default เป็น citizen แต่ source จะระบุ guest)
            $recaptcha_user_type = 'citizen';
            $min_score = 0.3; // Assessment ใช้คะแนนต่ำกว่า

            // ✅ เตรียม options สำหรับส่งไปยัง library
            $options = [
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => $user_type_detected,
                'context' => 'assessment_submission'
            ];

            log_message('debug', 'Assessment calling reCAPTCHA verify with: ' . json_encode([
                'user_type' => $recaptcha_user_type,
                'min_score' => $min_score,
                'options' => $options
            ]));

            // ✅ ใช้ verify() แทน verify_citizen() เพื่อส่ง options
            $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $recaptcha_user_type, $min_score, $options);

            if (!$recaptcha_result['success']) {
                log_message('error', "Assessment reCAPTCHA verification failed: " . $recaptcha_result['message']);

                // ส่ง response ตาม format เดิม
                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'การยืนยันตัวตน reCAPTCHA ล้มเหลว กรุณาลองใหม่อีกครั้ง'
                    ]);
                    return;
                } else {
                    $this->session->set_flashdata('error_message', 'การยืนยันตัวตน reCAPTCHA ล้มเหลว กรุณาลองใหม่อีกครั้ง');
                    redirect('assessment');
                    return;
                }
            }

            log_message('info', "✅ Assessment reCAPTCHA verification successful with action: {$recaptcha_action}, source: {$recaptcha_source}");
        } else {
            log_message('debug', 'Assessment: No reCAPTCHA token provided, skipping verification');
        }

        // ✅ ส่วนนี้เหมือนเดิมทั้งหมด - แค่เพิ่มการตรวจสอบการส่งซ้ำ
        $allow_multiple = $this->assessment_model->get_setting('allow_multiple_submissions');
        if ($allow_multiple !== '1') {
            $existing = $this->check_existing_submission();
            if ($existing) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'เครื่องนี้ได้ส่งแบบประเมินในวันนี้แล้ว หากมีความจำเป็น กรุณาติดต่อเจ้าหน้าที่'
                    ]);
                    return;
                } else {
                    $this->session->set_flashdata('error_message', 'เครื่องนี้ได้ส่งแบบประเมินในวันนี้แล้ว หากมีความจำเป็น กรุณาติดต่อเจ้าหน้าที่');
                    redirect('assessment');
                    return;
                }
            }
        }

        // เริ่ม Transaction (ตามเดิม)
        $this->db->trans_start();

        // สร้าง browser fingerprint (ตามเดิม)
        $user_agent = $this->input->user_agent();
        $accept_language = $this->input->server('HTTP_ACCEPT_LANGUAGE') ?: '';
        $browser_fingerprint = md5($user_agent . $accept_language . $this->input->ip_address());

        // สร้าง response record (ตามเดิม)
        $response_data = [
            'session_id' => session_id(),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $user_agent,
            'browser_fingerprint' => $browser_fingerprint,
            'is_completed' => 0
        ];

        $response_id = $this->assessment_model->create_response($response_data);

        if (!$response_id) {
            $this->db->trans_rollback();
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                ]);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                redirect('assessment');
            }
            return;
        }

        // บันทึกคำตอบทุกข้อ (ตามเดิม)
        $post_data = $this->input->post();
        $questions = $this->assessment_model->get_questions();
        $answers_saved = 0;

        foreach ($questions as $question) {
            $field_name = 'question_' . $question->id;

            if (isset($post_data[$field_name]) && !empty($post_data[$field_name])) {
                $answer_data = [
                    'response_id' => $response_id,
                    'question_id' => $question->id,
                    'answer_value' => $post_data[$field_name]
                ];

                // สำหรับคำถาม textarea และ text input
                if ($question->question_type === 'textarea') {
                    $answer_data['answer_text'] = $post_data[$field_name];
                }

                // สำหรับคำถามที่มี "อื่นๆ"
                $other_field = $field_name . '_other';
                if (isset($post_data[$other_field]) && !empty($post_data[$other_field])) {
                    $answer_data['answer_text'] = $post_data[$other_field];
                }

                // บันทึกคำตอบ
                if ($this->assessment_model->add_answer($answer_data)) {
                    $answers_saved++;
                }
            }
        }

        // ตรวจสอบว่ามีการบันทึกคำตอบหรือไม่ (ตามเดิม)
        if ($answers_saved === 0) {
            $this->db->trans_rollback();
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลที่จะบันทึก กรุณาตรวจสอบการกรอกข้อมูล'
                ]);
            } else {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลที่จะบันทึก กรุณาตรวจสอบการกรอกข้อมูล');
                redirect('assessment');
            }
            return;
        }

        // อัพเดทสถานะเป็นเสร็จสิ้น (ตามเดิม)
        $this->assessment_model->complete_response($response_id);

        // Commit transaction (ตามเดิม)
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                ]);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                redirect('assessment');
            }
        } else {
            // ✅ แก้ไขเฉพาะส่วนนี้ - รองรับ AJAX response
            log_message('info', "✅ Assessment submission successful");

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'ส่งแบบประเมินเรียบร้อยแล้ว ขอบคุณสำหรับความร่วมมือ',
                    'redirect_url' => site_url('assessment/thank_you')
                ]);
            } else {
                $this->session->set_flashdata('success_message', 'ส่งแบบประเมินเรียบร้อยแล้ว ขอบคุณสำหรับความร่วมมือ');
                redirect('assessment/thank_you');
            }
        }
    }

    // ตรวจสอบการส่งซ้ำ (เช็คจาก IP Address เท่านั้น)
    private function check_existing_submission()
    {
        $ip_address = $this->input->ip_address();
        $user_agent = $this->input->user_agent();
        $today = date('Y-m-d');

        // สร้าง browser fingerprint จาก user agent + accept language
        $accept_language = $this->input->server('HTTP_ACCEPT_LANGUAGE') ?: '';
        $browser_fingerprint = md5($user_agent . $accept_language . $ip_address);

        // เช็คจาก browser fingerprint แทน IP เดียว
        $this->db->where('browser_fingerprint', $browser_fingerprint);
        $this->db->where('is_completed', 1);
        $this->db->where('DATE(completed_at)', $today);
        $result = $this->db->get('tbl_assessment_responses');

        return $result->num_rows() > 0;
    }

    private function generate_fingerprint()
    {
        $user_agent = $this->input->user_agent();
        $accept_language = $this->input->server('HTTP_ACCEPT_LANGUAGE') ?: '';
        $accept_encoding = $this->input->server('HTTP_ACCEPT_ENCODING') ?: '';
        $device_fingerprint = $this->input->post('device_fingerprint') ?: '';

        return md5($user_agent . $accept_language . $accept_encoding . $device_fingerprint . $this->input->ip_address());
    }

    // ตรวจสอบ reCAPTCHA
    private function verify_recaptcha()
    {
        $recaptcha_response = $this->input->post('g-recaptcha-response');
        $secret_key = $this->Assessment_model->get_setting('recaptcha_secret_key');

        // ถ้าไม่ได้ตั้งค่า reCAPTCHA ให้ผ่านไป
        if (empty($secret_key)) {
            return true;
        }

        if (empty($recaptcha_response)) {
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secret_key,
            'response' => $recaptcha_response,
            'remoteip' => $this->input->ip_address()
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            return true; // ถ้าเกิดข้อผิดพลาดในการเชื่อมต่อ ให้ผ่าน
        }

        $response = json_decode($result);
        return $response && $response->success;
    }

    // ======== หน้าผู้ดูแลระบบ ========

    public function admin()
    {
        $this->check_admin_auth();

        $data['categories'] = $this->Assessment_model->get_categories(false);
        $data['statistics'] = $this->Assessment_model->get_statistics();
        $data['recent_responses'] = $this->Assessment_model->get_responses(10);

        $this->load->view('assessment/admin/dashboard', $data);
    }

    // จัดการหมวดหมู่
    public function admin_categories()
    {
        $this->check_admin_auth();

        try {
            if ($this->input->method() === 'post') {
                $action = $this->input->post('action');

                switch ($action) {
                    case 'add':
                        $this->add_category();
                        break;
                    case 'edit':
                        $this->edit_category();
                        break;
                    case 'delete':
                        $this->delete_category();
                        break;
                    case 'reorder':
                        $this->reorder_categories();
                        break;
                }
            }

            // กำหนดค่าเริ่มต้นสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            $data['categories'] = $this->assessment_model->get_categories(false);
            $data['page_title'] = 'จัดการหมวดหมู่แบบประเมิน';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');

            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/assessment_admin_categories', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in admin_categories: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            redirect('assessment/admin');
        }
    }

    // จัดการคำถาม
    public function admin_questions($category_id = null)
    {
        $this->check_admin_auth();

        try {
            if ($this->input->method() === 'post') {
                $action = $this->input->post('action');

                switch ($action) {
                    case 'add':
                        $this->add_question();
                        break;
                    case 'edit':
                        $this->edit_question();
                        break;
                    case 'delete':
                        $this->delete_question();
                        break;
                    case 'reorder':
                        $this->reorder_questions();
                        break;
                }
            }

            // กำหนดค่าเริ่มต้นสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            $data['categories'] = $this->assessment_model->get_categories(false);
            $data['current_category'] = $category_id;

            if ($category_id) {
                $data['questions'] = $this->assessment_model->get_questions($category_id, false);
                $data['category_info'] = $this->assessment_model->get_category($category_id);
            }

            $data['page_title'] = 'จัดการคำถามแบบประเมิน';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');

            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/assessment_admin_questions', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in admin_questions: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            redirect('assessment/admin');
        }
    }

    // รายงานผลการประเมิน
    public function admin_reports()
    {
        $this->check_admin_auth();

        try {
            $page = (int) ($this->input->get('page') ?? 1);
            $limit = 20;
            $offset = ($page - 1) * $limit;

            // กำหนดค่าเริ่มต้นสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            $data['responses'] = $this->assessment_model->get_responses($limit, $offset);
            $data['statistics'] = $this->assessment_model->get_statistics();
            $data['current_page'] = $page;

            // นับจำนวนหน้าทั้งหมด
            $total_responses = $data['statistics']['total_responses'] ?? 0;
            $data['total_pages'] = ceil($total_responses / $limit);

            $data['page_title'] = 'รายงานผลการประเมิน';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');

            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/assessment_admin_reports', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in admin_reports: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            redirect('assessment/admin');
        }
    }

    // การตั้งค่าระบบ
    public function admin_settings()
    {
        $this->check_admin_auth();

        try {
            if ($this->input->method() === 'post') {
                $settings = $this->input->post();

                foreach ($settings as $key => $value) {
                    if ($key !== 'submit') { // ไม่บันทึกปุ่ม submit
                        $this->assessment_model->update_setting($key, $value);
                    }
                }

                $this->session->set_flashdata('success_message', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
                redirect('assessment/admin_settings');
            }

            // กำหนดค่าเริ่มต้นสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            $data['settings'] = $this->assessment_model->get_all_settings();
            $data['page_title'] = 'การตั้งค่าระบบประเมิน';

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');

            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/assessment_admin_settings', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in admin_settings: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            redirect('assessment/admin');
        }
    }

    // ======== CRUD Methods ========

    private function add_category()
    {
        $this->form_validation->set_rules('category_name', 'ชื่อหมวดหมู่', 'required|trim');

        if ($this->form_validation->run()) {
            // หา order ใหม่
            $this->db->select_max('category_order');
            $result = $this->db->get('tbl_assessment_categories')->row();
            $new_order = ($result->category_order ?? 0) + 1;

            $data = [
                'category_name' => $this->input->post('category_name'),
                'category_order' => $new_order,
                'is_active' => $this->input->post('is_active') ?? 1
            ];

            if ($this->Assessment_model->add_category($data)) {
                $this->session->set_flashdata('success', 'เพิ่มหมวดหมู่เรียบร้อยแล้ว');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
    }

    private function edit_category()
    {
        $id = $this->input->post('category_id');
        $this->form_validation->set_rules('category_name', 'ชื่อหมวดหมู่', 'required|trim');

        if ($this->form_validation->run()) {
            $data = [
                'category_name' => $this->input->post('category_name'),
                'category_order' => $this->input->post('category_order'),
                'is_active' => $this->input->post('is_active')
            ];

            if ($this->Assessment_model->update_category($id, $data)) {
                $this->session->set_flashdata('success', 'แก้ไขหมวดหมู่เรียบร้อยแล้ว');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการแก้ไขหมวดหมู่');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
    }

    private function delete_category()
    {
        $id = $this->input->post('category_id');

        // ตรวจสอบว่ามีคำถามในหมวดหมู่นี้หรือไม่
        $questions = $this->Assessment_model->get_questions($id, false);
        if (!empty($questions)) {
            $this->session->set_flashdata('error', 'ไม่สามารถลบหมวดหมู่ที่มีคำถามอยู่ได้ กรุณาลบคำถามก่อน');
            return;
        }

        if ($this->Assessment_model->delete_category($id)) {
            $this->session->set_flashdata('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
        } else {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการลบหมวดหมู่');
        }
    }

    private function add_question()
    {
        $this->form_validation->set_rules('question_text', 'คำถาม', 'required|trim');
        $this->form_validation->set_rules('category_id', 'หมวดหมู่', 'required|integer');

        if ($this->form_validation->run()) {
            // หา order ใหม่ในหมวดหมู่
            $category_id = $this->input->post('category_id');
            $this->db->select_max('question_order');
            $this->db->where('category_id', $category_id);
            $result = $this->db->get('tbl_assessment_questions')->row();
            $new_order = ($result->question_order ?? 0) + 1;

            $data = [
                'category_id' => $category_id,
                'question_text' => $this->input->post('question_text'),
                'question_order' => $new_order,
                'question_type' => $this->input->post('question_type') ?? 'radio',
                'is_required' => $this->input->post('is_required') ?? 1,
                'is_active' => $this->input->post('is_active') ?? 1
            ];

            $question_id = $this->Assessment_model->add_question($data);

            if ($question_id) {
                // ถ้าเป็นคำถามแบบ radio ให้สร้างตัวเลือกพื้นฐาน
                if ($data['question_type'] === 'radio') {
                    $this->create_default_options($question_id, $category_id);
                }

                $this->session->set_flashdata('success', 'เพิ่มคำถามเรียบร้อยแล้ว');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการเพิ่มคำถาม');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
    }

    // สร้างตัวเลือกพื้นฐานสำหรับคำถามใหม่
    private function create_default_options($question_id, $category_id)
    {
        $category = $this->assessment_model->get_category($category_id);
        $question = $this->assessment_model->get_question($question_id);

        // **แก้ไข: ถ้าเป็น textarea ไม่ต้องสร้าง options**
        if ($question->question_type === 'textarea') {
            return; // ออกจากฟังก์ชันทันที
        }

        // ถ้าเป็นหมวดข้อมูลทั่วไป ไม่ต้องสร้างตัวเลือกคะแนน
        if (strpos($category->category_name, 'ข้อมูลทั่วไป') !== false) {
            return;
        }

        // ถ้าเป็นหมวดประเมิน ให้สร้างตัวเลือกคะแนน 1-5
        if (
            strpos($category->category_name, 'การให้บริการ') !== false ||
            strpos($category->category_name, 'บุคลากร') !== false ||
            strpos($category->category_name, 'สถานที่') !== false
        ) {

            $rating_options = [
                ['text' => 'ควรปรับปรุง (1 คะแนน)', 'value' => '1'],
                ['text' => 'พอใช้ (2 คะแนน)', 'value' => '2'],
                ['text' => 'ปานกลาง (3 คะแนน)', 'value' => '3'],
                ['text' => 'ดี (4 คะแนน)', 'value' => '4'],
                ['text' => 'ดีมาก (5 คะแนน)', 'value' => '5']
            ];

            foreach ($rating_options as $index => $option) {
                $option_data = [
                    'question_id' => $question_id,
                    'option_text' => $option['text'],
                    'option_value' => $option['value'],
                    'option_order' => $index + 1,
                    'is_active' => 1
                ];
                $this->assessment_model->add_option($option_data);
            }
        }
    }


    private function edit_question()
    {
        $id = $this->input->post('question_id');
        $this->form_validation->set_rules('question_text', 'คำถาม', 'required|trim');

        if ($this->form_validation->run()) {
            $data = [
                'question_text' => $this->input->post('question_text'),
                'question_order' => $this->input->post('question_order'),
                'question_type' => $this->input->post('question_type'),
                'is_required' => $this->input->post('is_required'),
                'is_active' => $this->input->post('is_active')
            ];

            if ($this->Assessment_model->update_question($id, $data)) {
                $this->session->set_flashdata('success', 'แก้ไขคำถามเรียบร้อยแล้ว');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการแก้ไขคำถาม');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
    }

    private function delete_question()
    {
        $id = $this->input->post('question_id');

        if ($this->Assessment_model->delete_question($id)) {
            $this->session->set_flashdata('success', 'ลบคำถามเรียบร้อยแล้ว');
        } else {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการลบคำถาม');
        }
    }

    private function add_option()
    {
        $this->form_validation->set_rules('option_text', 'ตัวเลือก', 'required|trim');
        $this->form_validation->set_rules('question_id', 'คำถาม', 'required|integer');

        if ($this->form_validation->run()) {
            // หา order ใหม่ในคำถาม
            $question_id = $this->input->post('question_id');
            $this->db->select_max('option_order');
            $this->db->where('question_id', $question_id);
            $result = $this->db->get('tbl_assessment_options')->row();
            $new_order = ($result->option_order ?? 0) + 1;

            $data = [
                'question_id' => $question_id,
                'option_text' => $this->input->post('option_text'),
                'option_value' => $this->input->post('option_value') ?: $this->input->post('option_text'),
                'option_order' => $new_order,
                'is_active' => $this->input->post('is_active') ?? 1
            ];

            if ($this->Assessment_model->add_option($data)) {
                $this->session->set_flashdata('success', 'เพิ่มตัวเลือกเรียบร้อยแล้ว');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการเพิ่มตัวเลือก');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
    }

    private function edit_option()
    {
        $id = $this->input->post('option_id');
        $this->form_validation->set_rules('option_text', 'ตัวเลือก', 'required|trim');

        if ($this->form_validation->run()) {
            $data = [
                'option_text' => $this->input->post('option_text'),
                'option_value' => $this->input->post('option_value') ?: $this->input->post('option_text'),
                'option_order' => $this->input->post('option_order'),
                'is_active' => $this->input->post('is_active')
            ];

            if ($this->Assessment_model->update_option($id, $data)) {
                $this->session->set_flashdata('success', 'แก้ไขตัวเลือกเรียบร้อยแล้ว');
            } else {
                $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการแก้ไขตัวเลือก');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }
    }

    private function delete_option()
    {
        $id = $this->input->post('option_id');

        if ($this->Assessment_model->delete_option($id)) {
            $this->session->set_flashdata('success', 'ลบตัวเลือกเรียบร้อยแล้ว');
        } else {
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาดในการลบตัวเลือก');
        }
    }

    // ======== Helper Methods ========

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
            // เพิ่มการตรวจสอบที่เข้มงวดสำหรับแต่ละ model
            $safe_models = [
                'activity_model' => ['method' => 'activity_frontend', 'key' => 'qActivity'],
                'HotNews_model' => ['method' => 'hotnews_frontend', 'key' => 'qHotnews'],
                'Weather_report_model' => ['method' => 'weather_reports_frontend', 'key' => 'qWeather'],
                'calender_model' => ['method' => 'get_events', 'key' => 'events'],
                'banner_model' => ['method' => 'banner_frontend', 'key' => 'qBanner'],
                'background_personnel_model' => ['method' => 'background_personnel_frontend', 'key' => 'qBackground_personnel']
            ];

            foreach ($safe_models as $model_name => $config) {
                if (isset($this->$model_name) && method_exists($this->$model_name, $config['method'])) {
                    try {
                        $result = $this->$model_name->{$config['method']}();
                        $data[$config['key']] = (is_array($result) || is_object($result)) ? $result : [];
                    } catch (Exception $e) {
                        log_message('debug', "Error loading {$model_name}: " . $e->getMessage());
                        $data[$config['key']] = [];
                    }
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }

    private function check_activity_slider_availability_safe()
    {
        try {
            if (isset($this->activity_model) && method_exists($this->activity_model, 'activity_frontend')) {
                $activities = $this->activity_model->activity_frontend();
                return !empty($activities);
            }
        } catch (Exception $e) {
            log_message('debug', 'Error checking activity slider: ' . $e->getMessage());
        }
        return false;
    }

    // ตรวจสอบสิทธิ์ผู้ดูแลระบบ
    private function check_admin_auth()
    {
        // ตรวจสอบ session หรือ cookie สำหรับผู้ดูแลระบบ
        if (!$this->session->userdata('is_admin')) {
            // สำหรับการทดสอบ อนุญาตให้เข้าได้
            // ในการใช้งานจริงควร redirect ไปหน้า login
            // redirect('auth/login');
        }
    }

    // Export ข้อมูลเป็น CSV
    public function export_csv()
    {
        $this->check_admin_auth();

        $responses = $this->assessment_model->get_responses();
        $categories = $this->assessment_model->get_categories();

        // สร้าง CSV header
        $csv_data = [];
        $headers = ['วันที่ส่ง', 'IP Address'];

        foreach ($categories as $category) {
            $questions = $this->assessment_model->get_questions($category->id);
            foreach ($questions as $question) {
                $headers[] = $question->question_text;
            }
        }
        $csv_data[] = $headers;

        // เขียนข้อมูล
        foreach ($responses as $response) {
            $detail = $this->assessment_model->get_response_detail($response->id);

            $row = [$response->completed_at, $response->ip_address];

            foreach ($detail as $answer) {
                $value = $answer->answer_text ?: $answer->answer_value;
                $row[] = $value;
            }
            $csv_data[] = $row;
        }

        // Output CSV
        $filename = 'assessment_results_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // เพิ่ม BOM สำหรับ UTF-8
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        foreach ($csv_data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    }


    // Preview แบบประเมิน
    public function preview()
    {
        $this->check_admin_auth();

        try {
            // กำหนดค่าเริ่มต้นสำหรับ navbar
            $data = $this->prepare_navbar_data_safe();

            $data['assessment'] = $this->assessment_model->get_full_assessment();
            $data['settings'] = $this->assessment_model->get_all_settings();
            $data['is_preview'] = true;
            $data['page_title'] = 'ตัวอย่างแบบประเมิน';

            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/assessment_dynamic_form', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Error in preview: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            redirect('assessment/admin');
        }
    }






    // เพิ่มใน Assessment.php Controller

    // ============= API Methods for Admin Management =============

    /**
     * API: ดึงข้อมูลหมวดหมู่
     */
    public function api_get_category($id)
    {
        $this->check_admin_auth();

        $category = $this->assessment_model->get_category($id);

        if ($category) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'category' => $category
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบหมวดหมู่'
                ]));
        }
    }

    /**
     * API: เพิ่มหมวดหมู่
     */
    public function api_add_category()
    {
        $this->check_admin_auth();

        $this->form_validation->set_rules('category_name', 'ชื่อหมวดหมู่', 'required|trim');
        $this->form_validation->set_rules('category_order', 'ลำดับ', 'required|integer');

        if ($this->form_validation->run()) {
            $data = [
                'category_name' => $this->input->post('category_name'),
                'category_order' => $this->input->post('category_order'),
                'is_active' => $this->input->post('is_active') ?: 1
            ];

            if ($this->assessment_model->add_category($data)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'เพิ่มหมวดหมู่เรียบร้อยแล้ว'
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่'
                    ]));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => validation_errors()
                ]));
        }
    }

    /**
     * API: อัพเดทหมวดหมู่
     */
    public function api_update_category()
    {
        $this->check_admin_auth();

        $id = $this->input->post('category_id');
        $this->form_validation->set_rules('category_name', 'ชื่อหมวดหมู่', 'required|trim');

        if ($this->form_validation->run()) {
            $data = [
                'category_name' => $this->input->post('category_name'),
                'category_order' => $this->input->post('category_order'),
                'is_active' => $this->input->post('is_active')
            ];

            if ($this->assessment_model->update_category($id, $data)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'อัพเดทหมวดหมู่เรียบร้อยแล้ว'
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการอัพเดท'
                    ]));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => validation_errors()
                ]));
        }
    }

    /**
     * API: ลบหมวดหมู่
     */
    public function api_delete_category()
    {
        $this->check_admin_auth();

        $input = json_decode($this->input->raw_input_stream, true);
        $id = $input['category_id'];

        // ตรวจสอบว่ามีคำถามในหมวดหมู่นี้หรือไม่
        $questions = $this->assessment_model->get_questions($id, false);
        if (!empty($questions)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถลบหมวดหมู่ที่มีคำถามอยู่ได้ กรุณาลบคำถามก่อน'
                ]));
            return;
        }

        if ($this->assessment_model->delete_category($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'ลบหมวดหมู่เรียบร้อยแล้ว'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการลบหมวดหมู่'
                ]));
        }
    }

    /**
     * API: ดึงคำถามในหมวดหมู่
     */
    public function api_get_questions($category_id)
    {
        $this->check_admin_auth();

        $questions = $this->assessment_model->get_questions($category_id, false);
        $category = $this->assessment_model->get_category($category_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'questions' => $questions,
                'category_name' => $category ? $category->category_name : ''
            ]));
    }

    /**
     * API: ดึงข้อมูลคำถาม
     */
    public function api_get_question($id)
    {
        $this->check_admin_auth();

        $question = $this->assessment_model->get_question($id);

        if ($question) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'question' => $question
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่พบคำถาม'
                ]));
        }
    }

    /**
     * API: เพิ่มคำถาม
     */
    public function api_add_question()
    {
        $this->check_admin_auth();

        $this->form_validation->set_rules('question_text', 'คำถาม', 'required|trim');
        $this->form_validation->set_rules('category_id', 'หมวดหมู่', 'required|integer');

        if ($this->form_validation->run()) {
            $data = [
                'category_id' => $this->input->post('category_id'),
                'question_text' => $this->input->post('question_text'),
                'question_order' => $this->input->post('question_order'),
                'question_type' => $this->input->post('question_type') ?: 'radio',
                'is_required' => $this->input->post('is_required') ?: 1,
                'is_active' => $this->input->post('is_active') ?: 1
            ];

            $question_id = $this->assessment_model->add_question($data);

            if ($question_id) {
                // สร้างตัวเลือกพื้นฐานถ้าเป็น radio
                if ($data['question_type'] === 'radio') {
                    $this->create_default_options($question_id, $data['category_id']);
                }

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'เพิ่มคำถามเรียบร้อยแล้ว'
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการเพิ่มคำถาม'
                    ]));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => validation_errors()
                ]));
        }
    }

    /**
     * API: อัพเดทคำถาม
     */
    public function api_update_question()
    {
        $this->check_admin_auth();

        $id = $this->input->post('question_id');
        $this->form_validation->set_rules('question_text', 'คำถาม', 'required|trim');

        if ($this->form_validation->run()) {
            $data = [
                'question_text' => $this->input->post('question_text'),
                'question_order' => $this->input->post('question_order'),
                'question_type' => $this->input->post('question_type'),
                'is_required' => $this->input->post('is_required'),
                'is_active' => $this->input->post('is_active')
            ];

            if ($this->assessment_model->update_question($id, $data)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => true,
                        'message' => 'อัพเดทคำถามเรียบร้อยแล้ว'
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการอัพเดท'
                    ]));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => validation_errors()
                ]));
        }
    }

    /**
     * API: ลบคำถาม
     */
    public function api_delete_question()
    {
        $this->check_admin_auth();

        $input = json_decode($this->input->raw_input_stream, true);
        $id = $input['question_id'];

        if ($this->assessment_model->delete_question($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'ลบคำถามเรียบร้อยแล้ว'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการลบคำถาม'
                ]));
        }
    }

    /**
     * API: บันทึกการตั้งค่า
     */
    public function api_save_settings()
    {
        $this->check_admin_auth();

        $settings = $this->input->post();
        $success_count = 0;

        foreach ($settings as $key => $value) {
            if ($key !== 'submit') {
                if ($this->assessment_model->update_setting($key, $value)) {
                    $success_count++;
                }
            }
        }

        if ($success_count > 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'ไม่มีการเปลี่ยนแปลง'
                ]));
        }
    }



}
?>