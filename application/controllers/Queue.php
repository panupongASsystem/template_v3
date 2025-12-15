<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Queue extends CI_Controller
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

    // ===================================================================
    // *** NOTIFICATION HELPER ***
    // ===================================================================

    private function ensure_notification_lib()
    {
        if (!isset($this->Notification_lib)) {
            log_message('debug', 'Notification_lib not found, attempting to load...');

            try {
                $this->load->library('Notification_lib');

                if (isset($this->Notification_lib)) {
                    log_message('debug', 'Notification_lib loaded successfully');
                    return true;
                }
            } catch (Exception $e) {
                log_message('error', 'Failed to load Notification_lib: ' . $e->getMessage());
            }

            // ลองโหลดด้วยวิธี manual
            try {
                require_once(APPPATH . 'libraries/Notification_lib.php');
                $this->Notification_lib = new Notification_lib();
                log_message('debug', 'Notification_lib loaded manually');
                return true;
            } catch (Exception $e) {
                log_message('error', 'Manual loading failed: ' . $e->getMessage());
                return false;
            }
        }

        return true;
    }



    // ===================================================================
    // *** หน้าแสดงฟอร์มจองคิว ***
    // ===================================================================

    /**
     * ฟังก์ชันแสดงหน้าจองคิว - Full Version
     * รองรับการ redirect หลัง login และโหลดข้อมูลครบถ้วน
     */
    public function adding_queue()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar (ป้องกัน undefined variable)
            $data['qActivity'] = [];
            $data['qNews'] = [];
            $data['qAnnounce'] = [];
            $data['qOrder'] = [];
            $data['qProcurement'] = [];
            $data['qMui'] = [];
            $data['qGuide_work'] = [];
            $data['qLoadform'] = [];
            $data['qPppw'] = [];
            $data['qMsg_pres'] = [];
            $data['qHistory'] = [];
            $data['qOtop'] = [];
            $data['qGci'] = [];
            $data['qVision'] = [];
            $data['qAuthority'] = [];
            $data['qMission'] = [];
            $data['qMotto'] = [];
            $data['qCmi'] = [];
            $data['qExecutivepolicy'] = [];
            $data['qTravel'] = [];
            $data['qSi'] = [];

            // เพิ่มข้อมูลที่ navbar ต้องการ
            $data['qHotnews'] = [];
            $data['qWeather'] = [];
            $data['events'] = [];
            $data['qBanner'] = [];
            $data['qBackground_personnel'] = [];

            // โหลดข้อมูลที่จำเป็นสำหรับ navbar อย่างปลอดภัย
            try {
                // โหลดข้อมูลแบบเดียวกับ adding_complain (ใช้ _frontend methods)
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

                if (isset($this->calender_model) && method_exists($this->calender_model, 'get_events')) {
                    $result = $this->calender_model->get_events();
                    $data['events'] = (is_array($result) || is_object($result)) ? $result : [];
                }

                if (isset($this->banner_model) && method_exists($this->banner_model, 'banner_frontend')) {
                    $result = $this->banner_model->banner_frontend();
                    $data['qBanner'] = (is_array($result) || is_object($result)) ? $result : [];
                }

                if (isset($this->background_personnel_model) && method_exists($this->background_personnel_model, 'background_personnel_frontend')) {
                    $result = $this->background_personnel_model->background_personnel_frontend();
                    $data['qBackground_personnel'] = (is_array($result) || is_object($result)) ? $result : [];
                }

                // โหลดข้อมูลเพิ่มเติม (กรณีที่ไม่มี _frontend method)
                $additional_models = [
                    'news_model' => 'qNews',
                    'announce_model' => 'qAnnounce',
                    'order_model' => 'qOrder',
                    'procurement_model' => 'qProcurement',
                    'mui_model' => 'qMui',
                    'guide_work_model' => 'qGuide_work',
                    'loadform_model' => 'qLoadform',
                    'pppw_model' => 'qPppw',
                    'msg_pres_model' => 'qMsg_pres',
                    'history_model' => 'qHistory',
                    'otop_model' => 'qOtop',
                    'gci_model' => 'qGci',
                    'vision_model' => 'qVision',
                    'authority_model' => 'qAuthority',
                    'mission_model' => 'qMission',
                    'motto_model' => 'qMotto',
                    'cmi_model' => 'qCmi',
                    'executivepolicy_model' => 'qExecutivepolicy',
                    'travel_model' => 'qTravel',
                    'si_model' => 'qSi'
                ];

                foreach ($additional_models as $model_name => $data_key) {
                    if (isset($this->$model_name)) {
                        $result = null;
                        if (method_exists($this->$model_name, 'list_all')) {
                            $result = $this->$model_name->list_all();
                        } elseif (method_exists($this->$model_name, 'get_all')) {
                            $result = $this->$model_name->get_all();
                        }
                        if ($result !== null) {
                            $data[$data_key] = (is_array($result) || is_object($result)) ? $result : [];
                        }
                    }
                }

            } catch (Exception $e) {
                // หากมีข้อผิดพลาดในการโหลดข้อมูล navbar ให้ใช้ค่าเริ่มต้น
                log_message('error', 'Error loading navbar data in adding_queue: ' . $e->getMessage());
            }

            // *** ตรวจสอบการ redirect กลับจาก login ***
            $from_login = $this->input->get('from_login');
            if ($from_login === 'success') {
                $data['from_login'] = true;
                log_message('info', 'User returned to adding_queue after successful login');
            } else {
                $data['from_login'] = false;
            }

            // *** ตรวจสอบ redirect parameter จาก URL ***
            $redirect_url = $this->input->get('redirect');
            if ($redirect_url) {
                // เก็บ redirect URL ไว้ใน session สำหรับใช้หลัง login
                $this->session->set_userdata('redirect_after_login', $redirect_url);
                log_message('info', 'Redirect URL saved: ' . $redirect_url);
            }

            // ใช้ฟังก์ชันใหม่แบบเดียวกับ adding_complain (ถ้ามี)
            if (method_exists($this, 'get_user_login_info_with_detailed_address')) {
                $login_info = $this->get_user_login_info_with_detailed_address();
                $data['is_logged_in'] = $login_info['is_logged_in'];
                $data['user_info'] = $login_info['user_info'];
                $data['user_type'] = $login_info['user_type'];
                $data['user_address'] = $login_info['user_address'];

                log_message('info', 'Adding queue - Using detailed login info with address');
            } else {
                // ใช้วิธีเดิม (แต่ปรับปรุงให้ครบถ้วน)
                $data['is_logged_in'] = false;
                $data['user_info'] = null;
                $data['user_address'] = null;
                $data['user_type'] = 'guest';

                try {
                    // ตรวจสอบ public user
                    if ($this->session->userdata('mp_id') && $this->session->userdata('mp_email')) {
                        $mp_id = $this->session->userdata('mp_id');

                        // ดึงข้อมูลจากฐานข้อมูล
                        $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode');
                        $this->db->from('tbl_member_public');
                        $this->db->where('mp_id', $mp_id);
                        $user_data = $this->db->get()->row();

                        if ($user_data) {
                            $data['is_logged_in'] = true;
                            $data['user_type'] = 'public';
                            $data['user_info'] = [
                                'id' => $user_data->id,
                                'mp_id' => $user_data->mp_id,
                                'name' => trim(($user_data->mp_prefix ?: '') . ' ' . $user_data->mp_fname . ' ' . $user_data->mp_lname),
                                'prefix' => $user_data->mp_prefix,
                                'fname' => $user_data->mp_fname,
                                'lname' => $user_data->mp_lname,
                                'phone' => $user_data->mp_phone,
                                'email' => $user_data->mp_email
                            ];

                            // ข้อมูลที่อยู่แยกละเอียด
                            if (!empty($user_data->mp_address) || !empty($user_data->mp_district)) {
                                $data['user_address'] = [
                                    'additional_address' => $user_data->mp_address ?: '',
                                    'district' => $user_data->mp_district ?: '',
                                    'amphoe' => $user_data->mp_amphoe ?: '',
                                    'province' => $user_data->mp_province ?: '',
                                    'zipcode' => $user_data->mp_zipcode ?: '',
                                    'phone' => $user_data->mp_phone ?: '',
                                    'full_address' => trim($user_data->mp_address . ' ' . $user_data->mp_district . ' ' . $user_data->mp_amphoe . ' ' . $user_data->mp_province . ' ' . $user_data->mp_zipcode),
                                    'source' => 'detailed_columns',
                                    'parsed' => [
                                        'additional_address' => $user_data->mp_address ?: '',
                                        'district' => $user_data->mp_district ?: '',
                                        'amphoe' => $user_data->mp_amphoe ?: '',
                                        'province' => $user_data->mp_province ?: '',
                                        'zipcode' => $user_data->mp_zipcode ?: '',
                                        'full_address' => trim($user_data->mp_address . ' ' . $user_data->mp_district . ' ' . $user_data->mp_amphoe . ' ' . $user_data->mp_province . ' ' . $user_data->mp_zipcode)
                                    ]
                                ];
                            }

                            log_message('info', 'Adding queue - Public user logged in: ' . $user_data->mp_email);
                        }
                    }
                    // ตรวจสอบ staff user
                    elseif ($this->session->userdata('m_id') && $this->session->userdata('m_email')) {
                        $m_id = $this->session->userdata('m_id');

                        // ดึงข้อมูลจากฐานข้อมูล
                        $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system');
                        $this->db->from('tbl_member');
                        $this->db->where('m_id', $m_id);
                        $user_data = $this->db->get()->row();

                        if ($user_data) {
                            $data['is_logged_in'] = true;
                            $data['user_type'] = 'staff';
                            $data['user_info'] = [
                                'm_id' => $user_data->m_id,
                                'name' => trim($user_data->m_fname . ' ' . $user_data->m_lname),
                                'fname' => $user_data->m_fname,
                                'lname' => $user_data->m_lname,
                                'phone' => $user_data->m_phone,
                                'email' => $user_data->m_email,
                                'm_system' => $user_data->m_system
                            ];

                            log_message('info', 'Adding queue - Staff user logged in: ' . $user_data->m_email);
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error checking user login status in adding_queue: ' . $e->getMessage());
                }
            }

            // ข้อมูลเพิ่มเติมสำหรับหน้า
            $data['page_title'] = 'จองคิว';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'จองคิว', 'url' => '']
            ];

            // *** Debug information (เฉพาะใน development mode) ***
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Adding queue page data: ' . json_encode([
                    'is_logged_in' => $data['is_logged_in'],
                    'user_type' => $data['user_type'],
                    'from_login' => $data['from_login'],
                    'has_redirect' => !empty($redirect_url),
                    'has_user_info' => !empty($data['user_info']),
                    'has_user_address' => !empty($data['user_address'])
                ]));
            }

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/queue', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in adding_queue: ' . $e->getMessage());

            // ในกรณีที่เกิดข้อผิดพลาดร้ายแรง ให้แสดงหน้า error หรือ redirect
            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าจองคิว: ' . $e->getMessage(), 500);
            } else {
                // Production: redirect ไปหน้าหลักพร้อมแสดงข้อความ error
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    // ===================================================================
    // *** ฟังก์ชันบันทึกคิว ***
    // ===================================================================
    public function add_queue()
    {
        // *** บังคับ JSON header และหยุด output อื่น ***
        header('Content-Type: application/json; charset=utf-8');
        ob_start(); // เริ่ม output buffering

        try {
            log_message('info', '=== QUEUE SUBMIT START (WITH RECAPTCHA) ===');
            log_message('info', 'POST: ' . print_r($_POST, true));
            log_message('info', 'FILES: ' . print_r($_FILES, true));

            // ตรวจสอบข้อมูล POST
            if (!$_POST || empty($_POST)) {
                throw new Exception('ไม่มีข้อมูล POST');
            }

            // *** ⭐ เพิ่ม: ตรวจสอบ reCAPTCHA Token ***
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'queue_submit';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'queue_form';
            $user_type_detected = $this->input->post('user_type_detected') ?: 'guest';
            $is_ajax = $this->input->post('ajax_request') === '1';
            $dev_mode = $this->input->post('dev_mode') === '1';

            log_message('info', 'reCAPTCHA info for queue: ' . json_encode([
                'has_token' => !empty($recaptcha_token),
                'action' => $recaptcha_action,
                'source' => $recaptcha_source,
                'user_type_detected' => $user_type_detected,
                'is_ajax' => $is_ajax,
                'dev_mode' => $dev_mode
            ]));

            // *** ⭐ เพิ่ม: ตรวจสอบ reCAPTCHA (ยกเว้นโหมด development) ***
            if (!$dev_mode && !empty($recaptcha_token)) {
                // *** ใช้ reCAPTCHA Library ที่มีอยู่ โดยให้ Library กำหนด min_score เอง ***
                $recaptcha_options = [
                    'action' => $recaptcha_action,
                    'source' => $recaptcha_source,
                    'user_type_detected' => $user_type_detected,
                    'form_source' => 'queue_submission',
                    'client_timestamp' => $this->input->post('client_timestamp'),
                    'user_agent_info' => $this->input->post('user_agent_info')
                ];

                // *** กำหนด user_type ที่ถูกต้องสำหรับ Library ***
                $library_user_type = 'citizen'; // default
                if ($user_type_detected === 'staff' || $user_type_detected === 'admin') {
                    $library_user_type = 'staff';
                }

                // *** เรียกใช้ reCAPTCHA verification ให้ Library กำหนด min_score เอง ***
                $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $library_user_type, null, $recaptcha_options);

                log_message('info', 'reCAPTCHA verification result for queue: ' . json_encode([
                    'success' => $recaptcha_result['success'],
                    'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                    'action' => $recaptcha_action,
                    'source' => $recaptcha_source,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]));

                // *** ตรวจสอบผลลัพธ์ reCAPTCHA ***
                if (!$recaptcha_result['success']) {
                    log_message('debug', 'reCAPTCHA verification failed for queue: ' . json_encode([
                        'message' => $recaptcha_result['message'],
                        'user_type_detected' => $user_type_detected,
                        'library_user_type' => $library_user_type,
                        'action' => $recaptcha_action,
                        'source' => $recaptcha_source,
                        'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A'
                    ]));

                    ob_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'การยืนยันตัวตนไม่ผ่าน: ' . $recaptcha_result['message'],
                        'error_type' => 'recaptcha_failed',
                        'recaptcha_data' => $recaptcha_result['data']
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                log_message('info', 'reCAPTCHA verification successful for queue: ' . json_encode([
                    'score' => $recaptcha_result['data']['score'],
                    'action' => $recaptcha_action,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]));

            } else if (!$dev_mode) {
                // *** ไม่มี reCAPTCHA token ***
                log_message('info', 'No reCAPTCHA token provided for queue');

                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการยืนยันตัวตน',
                    'error_type' => 'recaptcha_missing'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** ⭐ เพิ่ม: ตรวจสอบคำหยาบและ URL (ก่อนการ validation) ***
            $queue_topic = trim($this->input->post('queue_topic'));
            $queue_detail = trim($this->input->post('queue_detail'));
            $combined_text = $queue_topic . ' ' . $queue_detail;

            // ตรวจสอบคำหยาบ
            if (method_exists($this, 'check_vulgar_word')) {
                $vulgar_result = $this->check_vulgar_word($combined_text);
                if ($vulgar_result['found']) {
                    log_message('debug', 'Vulgar words detected in queue: ' . json_encode([
                        'vulgar_words' => $vulgar_result['words'],
                        'topic' => $queue_topic
                    ]));

                    ob_clean();
                    echo json_encode([
                        'success' => false,
                        'vulgar_detected' => true,
                        'vulgar_words' => $vulgar_result['words'],
                        'message' => 'พบคำไม่เหมาะสม',
                        'error_type' => 'vulgar_content'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            // ตรวจสอบ URL
            if (method_exists($this, 'check_no_urls')) {
                $url_result = $this->check_no_urls($combined_text);
                if ($url_result['found']) {
                    log_message('debug', 'URLs detected in queue: ' . json_encode([
                        'urls' => $url_result['urls'],
                        'topic' => $queue_topic
                    ]));

                    ob_clean();
                    echo json_encode([
                        'success' => false,
                        'url_detected' => true,
                        'urls' => $url_result['urls'],
                        'message' => 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ',
                        'error_type' => 'url_content'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            // *** ดึงข้อมูล user ปัจจุบัน ***
            $login_info = $this->get_user_login_info_with_detailed_address();
            $is_logged_in = $login_info['is_logged_in'];
            $user_info = $login_info['user_info'];
            $user_address = $login_info['user_address'];

            log_message('info', 'Public user login verified: ' . ($is_logged_in && $user_info ? $user_info['email'] : 'guest'));
            log_message('debug', 'Current user status: ' . json_encode([
                'is_logged_in' => $is_logged_in,
                'user_type' => $login_info['user_type'],
                'user_id' => $is_logged_in && $user_info ? $user_info['id'] : null
            ]));

            // *** โหลด libraries และ models ***
            $this->load->library('form_validation');
            $this->load->model('queue_model');

            // *** Validation rules ***
            $this->form_validation->set_rules('queue_topic', 'เรื่องที่ต้องการติดต่อ', 'trim|required|min_length[4]');
            $this->form_validation->set_rules('queue_detail', 'รายละเอียด', 'trim|required|min_length[4]');
            $this->form_validation->set_rules('queue_date', 'วันที่', 'trim|required');
            $this->form_validation->set_rules('queue_time_slot', 'ช่วงเวลา', 'trim|required');

            // *** เพิ่ม validation rules เฉพาะเมื่อไม่ได้ login ***
            if (!$is_logged_in) {
                $this->form_validation->set_rules('queue_by', 'ชื่อผู้จอง', 'trim|required|min_length[4]');
                $this->form_validation->set_rules('queue_phone', 'เบอร์โทรศัพท์', 'trim|required|min_length[9]|max_length[10]');
                $this->form_validation->set_rules('queue_number', 'เลขบัตรประจำตัวประชาชน', 'trim|required|exact_length[13]|numeric');
                $this->form_validation->set_rules('queue_address', 'ที่อยู่', 'trim|required|min_length[5]');
                log_message('info', 'Applied validation rules for guest user');
            } else {
                log_message('info', 'Skipped personal info validation for logged-in user');
            }

            // *** ทำการ validation ***
            if ($this->form_validation->run() == FALSE) {
                log_message('error', 'Validation failed: ' . validation_errors());

                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ถูกต้อง',
                    'errors' => strip_tags(validation_errors())
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** สร้าง Queue ID ***
            $queue_id = $this->generate_queue_id();
            log_message('info', 'Generated queue ID: ' . $queue_id);

            // *** เพิ่มข้อมูล reCAPTCHA ลงใน queue data ***
            $recaptcha_score = null;
            $recaptcha_verified = 0;

            if (!$dev_mode && !empty($recaptcha_token) && isset($recaptcha_result) && $recaptcha_result['success']) {
                $recaptcha_score = $recaptcha_result['data']['score'];
                $recaptcha_verified = 1;
                log_message('info', 'Added reCAPTCHA data to queue: score=' . $recaptcha_score);
            }

            // *** เตรียมข้อมูลสำหรับบันทึก ***
            $queue_data = [
                'queue_id' => $queue_id,
                'queue_topic' => $queue_topic,
                'queue_detail' => $queue_detail,
                'queue_date' => $this->input->post('queue_date'),
                'queue_time_slot' => $this->input->post('queue_time_slot'),
                'queue_status' => 'รอยืนยันการจอง',
                'queue_ip_address' => $this->input->ip_address(),
                'queue_user_agent' => $this->input->user_agent(),
            ];

            // เก็บข้อมูล reCAPTCHA ไว้ใช้ใน response
            $recaptcha_info = [
                'verified' => $recaptcha_verified,
                'score' => $recaptcha_score
            ];

            // *** เพิ่มข้อมูล user ***
            if ($is_logged_in && $user_info) {
                $queue_data['queue_user_id'] = $user_info['id'];
                $queue_data['queue_user_type'] = $login_info['user_type'];
                $queue_data['queue_by'] = $user_info['name'];
                $queue_data['queue_phone'] = $user_info['phone'];
                $queue_data['queue_email'] = $user_info['email'];
                $queue_data['queue_number'] = $user_info['number'];

                // ใช้ที่อยู่จาก user account
                if ($user_address && isset($user_address['parsed'])) {
                    $queue_data['queue_address'] = $user_address['parsed']['full_address'];
                } elseif ($user_address && isset($user_address['full_address'])) {
                    $queue_data['queue_address'] = $user_address['full_address'];
                }
            } else {
                // Guest user
                $queue_data['queue_by'] = $this->input->post('queue_by');
                $queue_data['queue_phone'] = $this->input->post('queue_phone');
                $queue_data['queue_email'] = $this->input->post('queue_email');
                $queue_data['queue_number'] = $this->input->post('queue_number');
                $queue_data['queue_address'] = $this->input->post('queue_address');
            }

            log_message('debug', 'Queue data prepared: ' . json_encode($queue_data));

            // *** ส่งข้อมูลไปยัง Model ***
            try {
                // ส่งข้อมูล user ไป Model ผ่าน session ชั่วคราว (ถ้าจำเป็น)
                if ($is_logged_in && $user_info) {
                    $this->session->set_tempdata('temp_user_info', $user_info, 300);
                    $this->session->set_tempdata('temp_user_address', $user_address, 300);
                    $this->session->set_tempdata('temp_user_type', $login_info['user_type'], 300);
                    $this->session->set_tempdata('temp_is_logged_in', true, 300);
                }

                $result = $this->queue_model->add_queue($queue_data);

                // ลบข้อมูลชั่วคราว
                $this->session->unset_tempdata('temp_user_info');
                $this->session->unset_tempdata('temp_user_address');
                $this->session->unset_tempdata('temp_user_type');
                $this->session->unset_tempdata('temp_is_logged_in');

            } catch (Exception $e) {
                log_message('error', 'Database error in add_queue: ' . $e->getMessage());

                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage(),
                    'errors' => 'ข้อผิดพลาดฐานข้อมูล'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            log_message('info', 'Model returned result: ' . ($result ? 'true' : 'false'));

            if ($result) {
                log_message('info', "✅ Queue submitted successfully with reCAPTCHA. ID: {$queue_id}");

                // *** ⭐ เพิ่มโค้ดส่วนนี้: เรียกใช้ฟังก์ชันอัปโหลดไฟล์ที่แก้ไขแล้ว ***
                $uploader_name = $queue_data['queue_by'] ?? 'N/A';
                $this->handle_queue_status_files($queue_id, $uploader_name);
                // ***************************************************************

                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'จองคิวเรียบร้อยแล้ว',
                    'queue_id' => $queue_id,
                    'recaptcha_verified' => isset($recaptcha_info) ? $recaptcha_info['verified'] == 1 : false
                ], JSON_UNESCAPED_UNICODE);

            } else {
                log_message('error', "❌ Failed to save queue");

                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', "❌ Exception in add_queue: " . $e->getMessage());

            ob_clean();
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit; // *** สำคัญที่สุด: หยุดการทำงานทันที ***
    }


    /**
     * *** เพิ่มฟังก์ชันสร้าง queue_id ***
     */
    private function generate_queue_id()
    {
        // ปีไทย 2 ตัวท้าย (พ.ศ. 2568 = 68)
        $thai_year = (int) date('Y') + 543;
        $thai_year_short = substr($thai_year, -2);

        $max_attempts = 100; // จำกัดการลองไม่เกิน 100 ครั้ง
        $attempt = 0;

        do {
            // สุ่มตัวเลข 5 หลัก (00000-99999)
            $random_5digits = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);

            // รวมเป็นหมายเลขคิว
            $queue_id = 'Q' . $thai_year_short . $random_5digits;

            // ตรวจสอบว่าหมายเลขนี้มีอยู่แล้วหรือไม่
            $this->db->select('queue_id');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $exists = $this->db->get()->row();

            $attempt++;

            // หากไม่มีหมายเลขซ้ำ ให้ใช้หมายเลขนี้
            if (!$exists) {
                return $queue_id;
            }

        } while ($attempt < $max_attempts);

        // หากสุ่มครบ 100 ครั้งแล้วยังซ้ำ ให้ใช้ timestamp เป็น fallback
        $fallback_number = substr(str_replace('.', '', microtime(true)), -5);
        return 'Q' . $thai_year_short . $fallback_number;
    }


    // ===================================================================
    // *** หน้าติดตามสถานะคิว ***
    // ===================================================================

    /**
     * ฟังก์ชันแสดงหน้าติดตามสถานะคิว - แก้ไขแล้ว
     * รองรับการค้นหาคิว แสดงรายการคิวของผู้ใช้ และติดตามสถานะ
     */
    public function follow_queue()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar อย่างปลอดภัย
            $data = $this->prepare_navbar_data_safe();

            // ตรวจสอบการ redirect และ parameter
            $auto_search = $this->input->get('auto_search');
            $queue_id = $this->input->get('queue_id') ?: $this->input->get('q');
            $from_success = $this->input->get('from_success');

            $data['auto_search'] = $auto_search ?: $queue_id;
            $data['from_success'] = ($from_success === 'true' || $from_success === '1');

            // ตรวจสอบสถานะ User Login
            if (method_exists($this, 'get_user_login_info_with_detailed_address')) {
                $login_info = $this->get_user_login_info_with_detailed_address();
                $data['is_logged_in'] = $login_info['is_logged_in'];
                $data['user_info'] = $login_info['user_info'];
                $data['user_type'] = $login_info['user_type'];
                $data['user_address'] = $login_info['user_address'];
            } else {
                // ใช้วิธีเดิม
                $data['is_logged_in'] = false;
                $data['user_info'] = null;
                $data['user_address'] = null;
                $data['user_type'] = 'guest';

                try {
                    // ตรวจสอบ public user
                    if ($this->session->userdata('mp_id') && $this->session->userdata('mp_email')) {
                        $mp_id = $this->session->userdata('mp_id');

                        $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode');
                        $this->db->from('tbl_member_public');
                        $this->db->where('mp_id', $mp_id);
                        $user_data = $this->db->get()->row();

                        if ($user_data) {
                            $data['is_logged_in'] = true;
                            $data['user_type'] = 'public';
                            $data['user_info'] = [
                                'id' => $user_data->id,
                                'mp_id' => $user_data->mp_id,
                                'name' => trim(($user_data->mp_prefix ?: '') . ' ' . $user_data->mp_fname . ' ' . $user_data->mp_lname),
                                'prefix' => $user_data->mp_prefix,
                                'fname' => $user_data->mp_fname,
                                'lname' => $user_data->mp_lname,
                                'phone' => $user_data->mp_phone,
                                'email' => $user_data->mp_email
                            ];

                            // ข้อมูลที่อยู่
                            if (!empty($user_data->mp_address) || !empty($user_data->mp_district)) {
                                $data['user_address'] = [
                                    'additional_address' => $user_data->mp_address ?: '',
                                    'district' => $user_data->mp_district ?: '',
                                    'amphoe' => $user_data->mp_amphoe ?: '',
                                    'province' => $user_data->mp_province ?: '',
                                    'zipcode' => $user_data->mp_zipcode ?: '',
                                    'phone' => $user_data->mp_phone ?: '',
                                    'full_address' => trim($user_data->mp_address . ' ' . $user_data->mp_district . ' ' . $user_data->mp_amphoe . ' ' . $user_data->mp_province . ' ' . $user_data->mp_zipcode),
                                    'source' => 'detailed_columns'
                                ];
                            }
                        }
                    }
                    // ตรวจสอบ staff user
                    elseif ($this->session->userdata('m_id')) {
                        $m_id = $this->session->userdata('m_id');

                        $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system');
                        $this->db->from('tbl_member');
                        $this->db->where('m_id', $m_id);
                        $user_data = $this->db->get()->row();

                        if ($user_data) {
                            $data['is_logged_in'] = true;
                            $data['user_type'] = 'staff';
                            $data['user_info'] = [
                                'id' => $user_data->m_id,
                                'm_id' => $user_data->m_id,
                                'name' => trim($user_data->m_fname . ' ' . $user_data->m_lname),
                                'fname' => $user_data->m_fname,
                                'lname' => $user_data->m_lname,
                                'phone' => $user_data->m_phone,
                                'email' => $user_data->m_email,
                                'm_system' => $user_data->m_system
                            ];
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error checking user login status: ' . $e->getMessage());
                }
            }

            // *** ดึงรายการคิวของผู้ใช้ (เฉพาะผู้ที่ login) ***
            $data['user_queues'] = [];

            if ($data['is_logged_in'] && $data['user_info']) {
                try {
                    if (isset($data['user_info']['id']) && !empty($data['user_info']['id'])) {
                        $user_id = $data['user_info']['id'];
                        $user_type = $data['user_type'];

                        log_message('info', "Getting queues for logged user - user_id: {$user_id}, type: {$user_type}");

                        // เรียกใช้ method ใหม่
                        $queues_result = $this->queue_model->get_queues_by_user_id($user_id, $user_type);

                        if ($queues_result) {
                            $data['user_queues'] = array_map(function ($queue) {
                                if (is_object($queue)) {
                                    return [
                                        'queue_id' => $queue->queue_id ?? '',
                                        'queue_topic' => $queue->queue_topic ?? '',
                                        'queue_date' => $queue->queue_date ?? '',
                                        'queue_status' => $queue->queue_status ?? '',
                                        'queue_by' => $queue->queue_by ?? '',
                                        'queue_phone' => $queue->queue_phone ?? '',
                                        'queue_datesave' => $queue->queue_datesave ?? '',
                                        'queue_dateupdate' => $queue->queue_dateupdate ?? null,
                                        'queue_user_id' => $queue->queue_user_id ?? '',
                                        'queue_user_type' => $queue->queue_user_type ?? ''
                                    ];
                                }
                                return $queue;
                            }, $queues_result);
                        }

                        log_message('info', 'Found ' . count($data['user_queues']) . ' queues for user: ' . $user_id);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error loading user queues: ' . $e->getMessage());
                    $data['user_queues'] = [];
                }
            } else {
                // Guest User - ไม่แสดงรายการคิว
                log_message('info', 'Guest user - no queue list shown');
                $data['user_queues'] = [];
            }

            // ข้อมูลเพิ่มเติม
            $data['page_title'] = 'ติดตามสถานะคิว';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'ติดตามสถานะคิว', 'url' => '']
            ];

            // ข้อมูลสำหรับ JavaScript
            $data['js_config'] = [
                'search_url' => site_url('Queue/search_queue'),
                'cancel_url' => site_url('Queue/cancel_queue'),
                'auto_search' => $data['auto_search'],
                'is_logged_in' => $data['is_logged_in'],
                'user_type' => $data['user_type'],
                'user_id' => $data['user_info']['id'] ?? null,
                'has_user_queues' => !empty($data['user_queues'])
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // *** แก้ไข: ตรวจสอบ Activity Slider แบบปลอดภัย ***
            $data['has_activity_slider'] = $this->check_activity_slider_availability_safe();

            // โหลด view
            $this->load->view('frontend_templat/header', $data);
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/follow_queue', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in follow_queue: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                redirect('Pages/service_systems');
            }
        }
    }

    // ===================================================================
    // *** AJAX Functions ***
    // ===================================================================

    /**
     * ฟังก์ชันค้นหาคิวผ่าน AJAX - เพิ่ม reCAPTCHA
     */
    public function search_queue()
    {
        // *** เพิ่ม: Log debug สำหรับ reCAPTCHA ***
        log_message('info', '=== QUEUE SEARCH START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));
        log_message('info', 'User Agent: ' . $this->input->server('HTTP_USER_AGENT'));

        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        ini_set('display_errors', 0);

        try {
            // ตรวจสอบ request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Method not allowed'
                ], JSON_UNESCAPED_UNICODE);
                exit;
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
                log_message('info', 'Starting reCAPTCHA verification for queue search');

                try {
                    // *** ใช้ reCAPTCHA Library ที่มีอยู่ ***
                    $recaptcha_options = [
                        'action' => $recaptcha_action ?: 'queue_search',
                        'source' => $recaptcha_source ?: 'queue_search_form',
                        'user_type_detected' => $user_type_detected ?: 'guest',
                        'form_source' => 'queue_search',
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

                            echo json_encode([
                                'success' => false,
                                'message' => 'การยืนยันความปลอดภัยไม่ผ่าน กรุณาลองใหม่อีกครั้ง',
                                'error_type' => 'recaptcha_failed',
                                'recaptcha_data' => $recaptcha_result['data']
                            ], JSON_UNESCAPED_UNICODE);
                            exit;
                        }

                        log_message('info', 'reCAPTCHA verification successful for queue search');
                    } else {
                        log_message('error', 'reCAPTCHA library not loaded');
                    }

                } catch (Exception $e) {
                    log_message('error', 'reCAPTCHA verification error: ' . $e->getMessage());

                    echo json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการตรวจสอบความปลอดภัย',
                        'error_type' => 'recaptcha_error'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            } else if (!$dev_mode) {
                log_message('info', 'No reCAPTCHA token provided for queue search');
            } else {
                log_message('info', 'Development mode - skipping reCAPTCHA verification');
            }

            // รับข้อมูลจาก POST
            $queue_id = trim($this->input->post('queue_id'));
            $user_type = $this->input->post('user_type') ?: 'guest';
            $user_id = $this->input->post('user_id');
            $is_logged_in = $this->input->post('is_logged_in') === '1';

            // แสดงข้อความชัดเจนสำหรับ Guest
            if (!$is_logged_in || $user_type === 'guest') {
                log_message('info', "Guest user searching queue: {$queue_id}");
            }

            log_message('debug', "Queue search request: queue_id={$queue_id}, user_type={$user_type}, is_logged_in=" . ($is_logged_in ? 'true' : 'false'));

            if (empty($queue_id)) {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'invalid_input',
                    'message' => 'กรุณากรอกหมายเลขคิว'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ค้นหาข้อมูลคิวพื้นฐาน
            $this->db->select('queue_id, queue_user_type, queue_user_id, queue_topic, queue_by, queue_phone, queue_status, queue_email, queue_detail, queue_date, queue_datesave, queue_dateupdate');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $queue_query = $this->db->get();

            if ($queue_query->num_rows() === 0) {
                log_message('info', "Queue not found: {$queue_id}");
                echo json_encode([
                    'success' => false,
                    'error_type' => 'not_found',
                    'message' => 'ไม่พบหมายเลขคิวที่ระบุ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $queue_basic_info = $queue_query->row_array();
            log_message('info', "Queue found: {$queue_id}, type: {$queue_basic_info['queue_user_type']}");

            // *** ตรวจสอบสิทธิ์การเข้าถึงแบบใหม่ที่จำกัด Guest ***
            $permission_result = $this->check_queue_access_permission_for_guest($queue_basic_info, $user_type, $user_id, $is_logged_in);

            if (!$permission_result['allowed']) {
                log_message('warning', "Access denied for queue {$queue_id}: {$permission_result['message']}");

                // ส่งข้อความที่เหมาะสมตาม error_type
                $response_message = $permission_result['message'];

                // เพิ่มคำแนะนำสำหรับ Guest
                if ($permission_result['error_type'] === 'permission_denied' && (!$is_logged_in || $user_type === 'guest')) {
                    $response_message .= "\n\nหากต้องการติดตามคิวประเภทอื่น กรุณาเข้าสู่ระบบด้วยบัญชีของคุณ";
                }

                echo json_encode([
                    'success' => false,
                    'error_type' => $permission_result['error_type'],
                    'message' => $response_message
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ถ้ามีสิทธิ์ ให้ดึงข้อมูลคิวแบบละเอียด
            $queue_data = $this->queue_model->get_queue_details($queue_id);

            if ($queue_data) {
                // ดึงประวัติการอัพเดทสถานะ
                $queue_history = $this->queue_model->get_queue_history($queue_id);

                // ดึงไฟล์แนบ
                $queue_files = $this->queue_model->get_queue_files($queue_id);

                log_message('info', "Queue search successful: {$queue_id} by {$user_type} user");

                echo json_encode([
                    'success' => true,
                    'message' => 'พบข้อมูลคิว',
                    'data' => [
                        'queue_info' => $queue_data,
                        'queue_history' => $queue_history ?: [],
                        'queue_files' => $queue_files ?: []
                    ]
                ], JSON_UNESCAPED_UNICODE);

            } else {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'not_found',
                    'message' => 'ไม่พบข้อมูลคิวที่ระบุ'
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', 'Error in search_queue: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการค้นหา กรุณาลองใหม่อีกครั้ง',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        log_message('info', '=== QUEUE SEARCH END ===');
        exit;
    }




    /**
     * ตรวจสอบ staff session แบบยืดหยุ่น - แก้ไขปัญหา email null
     */
    private function verify_staff_session_flexible()
    {
        try {
            // ตรวจสอบ session ขั้นพื้นฐาน
            $m_id = $this->session->userdata('m_id');
            $m_email = $this->session->userdata('m_email');

            // Log session data สำหรับ debug
            log_message('debug', 'Session verification - m_id: ' . var_export($m_id, true) . ', m_email: ' . var_export($m_email, true));

            // *** แก้ไข: ตรวจสอบเฉพาะ m_id ถ้า email เป็น null ***
            if (empty($m_id)) {
                return [
                    'success' => false,
                    'message' => 'ไม่พบ User ID ใน Session',
                    'code' => 'NO_USER_ID'
                ];
            }

            // ดึงข้อมูล staff จาก database ด้วย m_id
            $this->db->select('m_id, m_fname, m_lname, m_phone, m_email, m_system, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1'); // เฉพาะ active เท่านั้น
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                // ลอง query ดูว่ามี record หรือไม่
                $this->db->select('m_id, m_email, m_status');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $m_id);
                $debug_data = $this->db->get()->row();

                log_message('error', 'Staff not found - m_id: ' . $m_id . ', debug_data: ' . json_encode($debug_data));

                return [
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลเจ้าหน้าที่หรือบัญชีถูกระงับ',
                    'code' => 'STAFF_NOT_FOUND',
                    'debug_info' => $debug_data
                ];
            }

            // *** แก้ไข: ตรวจสอบ email เฉพาะเมื่อมีใน session ***
            if (!empty($m_email) && $staff_data->m_email !== $m_email) {
                log_message('warning', 'Email mismatch - DB: ' . $staff_data->m_email . ', Session: ' . $m_email);
                // *** ไม่ return error เพราะ email ใน session อาจ null ***
            }

            // *** ถ้า session ไม่มี email ให้ใช้จาก database ***
            if (empty($m_email)) {
                log_message('info', 'Session email is null, using email from database: ' . $staff_data->m_email);

                // อัปเดต session ด้วย email จาก database
                $this->session->set_userdata('m_email', $staff_data->m_email);
            }

            return [
                'success' => true,
                'staff_data' => $staff_data,
                'message' => 'Staff verified successfully',
                'email_updated' => empty($m_email)
            ];

        } catch (Exception $e) {
            log_message('error', 'Session verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Verification error: ' . $e->getMessage(),
                'code' => 'VERIFICATION_ERROR'
            ];
        }
    }

    /**
     * อัปเดตสถานะคิวพร้อมไฟล์แนบ - แก้ไขปัญหา email null
     */
    public function update_queue_status_with_images()
    {
        // *** Step 1: Basic Setup และ Error Handling ***
        while (ob_get_level()) {
            ob_end_clean();
        }

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');

        // เปิด error reporting ชั่วคราวเพื่อ debug
        if (ENVIRONMENT === 'development') {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        }

        try {
            // *** Step 2: เช็ค Request Method ***
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 3: เช็ค POST Data ก่อน ***
            $queue_id = $this->input->post('queue_id');
            $new_status = $this->input->post('new_status');

            if (empty($queue_id) || empty($new_status)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required data',
                    'debug' => [
                        'queue_id' => $queue_id,
                        'new_status' => $new_status,
                        'all_post' => $this->input->post()
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 4: เช็ค Session แบบง่าย ***
            $m_id = $this->session->userdata('m_id');

            if (empty($m_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No session m_id found',
                    'debug' => [
                        'm_id' => $m_id,
                        'session_data' => array_keys($this->session->userdata())
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 5: ตรวจสอบ Staff ใน Database ***
            $this->db->select('m_id, m_fname, m_lname, m_email, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $staff_data = $this->db->get()->row();

            if (!$staff_data) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Staff not found in database',
                    'debug' => [
                        'm_id' => $m_id
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if ($staff_data->m_status != '1') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Staff account inactive',
                    'debug' => [
                        'status' => $staff_data->m_status
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 6: ตรวจสอบ Queue ใน Database (ขยายข้อมูลเพื่อใช้ใน notification) ***
            $this->db->select('queue_id, queue_status, queue_topic, queue_by, queue_phone, queue_user_type, queue_user_id');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $queue_data = $this->db->get()->row();

            if (!$queue_data) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Queue not found',
                    'debug' => [
                        'queue_id' => $queue_id
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 7: ตรวจสอบสถานะที่อนุญาต ***
            $allowed_statuses = [
                'รอยืนยันการจอง',
                'รับเรื่องพิจารณา',
                'คิวได้รับการยืนยัน',
                'รับเรื่องแล้ว',
                'กำลังดำเนินการ',
                'รอดำเนินการ',
                'เสร็จสิ้น',
                'ยกเลิก',
                'คิวได้ถูกยกเลิก'
            ];

            if (!in_array($new_status, $allowed_statuses)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid status',
                    'debug' => [
                        'new_status' => $new_status,
                        'allowed' => $allowed_statuses
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 8: รับ Comment ***
            $comment = trim($this->input->post('comment')) ?: trim($this->input->post('status_note'));

            // ตัดความยาวไม่เกิน 250 ตัวอักษร
            if (strlen($comment) > 250) {
                $comment = mb_substr($comment, 0, 247) . '...';
            }

            // *** Step 9: เริ่ม Database Transaction ***
            $this->db->trans_start();

            $staff_name = $staff_data->m_fname . ' ' . $staff_data->m_lname;
            $current_time = date('Y-m-d H:i:s');

            // *** Step 10: อัปเดต tbl_queue ***
            $update_data = [
                'queue_status' => $new_status,
                'queue_dateupdate' => $current_time
            ];

            // กรณียกเลิก
            if (in_array($new_status, ['ยกเลิก', 'คิวได้ถูกยกเลิก'])) {
                $update_data['queue_cancel_reason'] = $comment;
                $update_data['queue_cancelled_by'] = $staff_name;
                $update_data['queue_cancelled_at'] = $current_time;
            }

            $this->db->where('queue_id', $queue_id);
            $update_result = $this->db->update('tbl_queue', $update_data);

            if (!$update_result) {
                $this->db->trans_rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update tbl_queue',
                    'debug' => [
                        'db_error' => $this->db->error(),
                        'last_query' => $this->db->last_query()
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 11: เพิ่มใน tbl_queue_detail ***
            $default_comment = $comment ?: 'อัปเดตสถานะเป็น: ' . $new_status;
            if ($new_status === 'รับเรื่องพิจารณา' && empty($comment)) {
                $default_comment = 'เจ้าหน้าที่ได้รับเรื่องพิจารณาแล้ว กำลังตรวจสอบข้อมูลและดำเนินการ';
            }

            $detail_data = [
                'queue_detail_case_id' => $queue_id,
                'queue_detail_status' => $new_status,
                'queue_detail_by' => $staff_name,
                'queue_detail_com' => $default_comment,
                'queue_detail_datesave' => $current_time
            ];

            $detail_result = $this->db->insert('tbl_queue_detail', $detail_data);

            if (!$detail_result) {
                $this->db->trans_rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to insert tbl_queue_detail',
                    'debug' => [
                        'db_error' => $this->db->error(),
                        'last_query' => $this->db->last_query(),
                        'detail_data' => $detail_data
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 12: Commit Transaction ***
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Transaction failed'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** Step 13: สร้างการแจ้งเตือน (เพิ่มใหม่) ***
            try {
                $this->create_queue_status_change_notifications($queue_id, $queue_data, $new_status, $staff_data, $default_comment);
                log_message('info', "Notifications sent successfully for queue {$queue_id} status update to {$new_status}");
            } catch (Exception $e) {
                log_message('error', 'Failed to create notifications for queue status update: ' . $e->getMessage());
                // ไม่ให้ notification error ทำให้การอัพเดตสถานะล้มเหลว
            }

            // *** Step 14: ส่งผลลัพธ์สำเร็จ ***
            echo json_encode([
                'success' => true,
                'message' => 'อัปเดตสถานะสำเร็จ',
                'data' => [
                    'queue_id' => $queue_id,
                    'old_status' => $queue_data->queue_status,
                    'new_status' => $new_status,
                    'updated_by' => $staff_name,
                    'comment' => $default_comment,
                    'timestamp' => $current_time,
                    'notification_sent' => true
                ]
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // Rollback ถ้ามี transaction
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }

            echo json_encode([
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
                ]
            ], JSON_UNESCAPED_UNICODE);
        } catch (Error $e) {
            echo json_encode([
                'success' => false,
                'message' => 'PHP Error: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }




    private function validate_queue_status_workflow($current_status, $new_status)
    {
        // *** แก้ไข: เปลี่ยน workflow rules ***
        $workflow_rules = [
            'รอยืนยันการจอง' => [
                'allowed' => ['รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'ยกเลิก'],
                'denied' => ['รับเรื่องแล้ว', 'กำลังดำเนินการ', 'เสร็จสิ้น']
            ],
            'รับเรื่องพิจารณา' => [
                'allowed' => ['คิวได้รับการยืนยัน', 'รับเรื่องแล้ว', 'กำลังดำเนินการ', 'ยกเลิก'],
                'denied' => ['รอยืนยันการจอง', 'เสร็จสิ้น']
            ],
            'คิวได้รับการยืนยัน' => [
                'allowed' => ['รับเรื่องแล้ว', 'กำลังดำเนินการ', 'เสร็จสิ้น', 'ยกเลิก'],
                'denied' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา']
            ],
            'รับเรื่องแล้ว' => [
                'allowed' => ['กำลังดำเนินการ', 'รอดำเนินการ', 'เสร็จสิ้น', 'ยกเลิก'],
                'denied' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน']
            ],
            'กำลังดำเนินการ' => [
                'allowed' => ['รอดำเนินการ', 'เสร็จสิ้น', 'ยกเลิก'],
                'denied' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'รับเรื่องแล้ว']
            ],
            'รอดำเนินการ' => [
                'allowed' => ['กำลังดำเนินการ', 'เสร็จสิ้น', 'ยกเลิก'],
                'denied' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'รับเรื่องแล้ว']
            ],
            'เสร็จสิ้น' => [
                'allowed' => [],
                'denied' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'รับเรื่องแล้ว', 'กำลังดำเนินการ', 'รอดำเนินการ', 'ยกเลิก']
            ],
            'ยกเลิก' => [
                'allowed' => [],
                'denied' => ['รอยืนยันการจอง', 'รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'รับเรื่องแล้ว', 'กำลังดำเนินการ', 'รอดำเนินการ', 'เสร็จสิ้น']
            ]
        ];

        // หากไม่มี current status ใน rules ให้อนุญาตทุกการเปลี่ยนแปลง
        if (!isset($workflow_rules[$current_status])) {
            return ['valid' => true, 'message' => ''];
        }

        $rules = $workflow_rules[$current_status];

        // ตรวจสอบว่าสถานะใหม่อยู่ใน denied list หรือไม่
        if (in_array($new_status, $rules['denied'])) {
            return [
                'valid' => false,
                'message' => "ไม่สามารถเปลี่ยนสถานะจาก '{$current_status}' เป็น '{$new_status}' ได้"
            ];
        }

        // ตรวจสอบว่าสถานะใหม่อยู่ใน allowed list หรือไม่
        if (!in_array($new_status, $rules['allowed'])) {
            return [
                'valid' => false,
                'message' => "การเปลี่ยนสถานะจาก '{$current_status}' เป็น '{$new_status}' ไม่ได้รับอนุญาต"
            ];
        }

        return ['valid' => true, 'message' => ''];
    }


    /**
     * จัดการการอัปโหลดไฟล์แนบสำหรับคิว (ฉบับแก้ไข)
     * @param string $queue_id รหัสคิวสำหรับอ้างอิง
     * @param string $uploader_name ชื่อผู้อัปโหลด
     * @return array รายการไฟล์ที่อัปโหลดสำเร็จ
     */
    private function handle_queue_status_files($queue_id, $uploader_name)
    {
        // ตรวจสอบว่ามีไฟล์ส่งมาในชื่อ 'queue_files' หรือไม่
        if (empty($_FILES['queue_files']['name']) || !is_array($_FILES['queue_files']['name'])) {
            log_message('info', 'No files uploaded for queue ID: ' . $queue_id);
            return []; // ไม่มีไฟล์ให้อัปโหลด
        }

        $this->load->library('upload');

        // *** START OF CHANGE 1 ***
        // กำหนด Path สำหรับการอัปโหลด (Relative Path) และสำหรับบันทึกลง DB
        $relative_path = 'docs/queue_files/';
        $upload_path = FCPATH . $relative_path;
        // *** END OF CHANGE 1 ***

        if (!is_dir($upload_path)) {
            // สร้าง directory ถ้ายังไม่มี
            if (!mkdir($upload_path, 0755, true)) {
                log_message('error', 'Failed to create upload directory: ' . $upload_path);
                return []; // ไม่สามารถสร้างโฟลเดอร์ได้
            }
        }

        $config = [
            'upload_path' => $upload_path, // Path สำหรับให้ Library save ไฟล์ลง server
            'allowed_types' => 'jpg|jpeg|png|gif|pdf|doc|docx|txt|xlsx|xls',
            'max_size' => 10240, // 10MB
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE
        ];

        $uploaded_files = [];
        $file_count = count($_FILES['queue_files']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if (!empty($_FILES['queue_files']['name'][$i]) && $_FILES['queue_files']['error'][$i] === UPLOAD_ERR_OK) {

                // เตรียมข้อมูลไฟล์แต่ละรายการสำหรับ Library Upload
                $_FILES['single_file'] = [
                    'name' => $_FILES['queue_files']['name'][$i],
                    'type' => $_FILES['queue_files']['type'][$i],
                    'tmp_name' => $_FILES['queue_files']['tmp_name'][$i],
                    'error' => $_FILES['queue_files']['error'][$i],
                    'size' => $_FILES['queue_files']['size'][$i]
                ];

                $this->upload->initialize($config);

                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();

                    // *** START OF CHANGE 2 ***
                    // สร้าง Path สำหรับบันทึกลงฐานข้อมูลให้เป็น Relative Path ตามที่ต้องการ
                    $db_file_path = './' . $relative_path . $upload_data['file_name'];
                    // *** END OF CHANGE 2 ***

                    $file_data = [
                        'queue_file_ref_id' => $queue_id,
                        'queue_file_name' => $upload_data['file_name'],
                        'queue_file_original_name' => $upload_data['orig_name'],
                        'queue_file_type' => $upload_data['file_type'],
                        'queue_file_size' => $upload_data['file_size'] * 1024, // KB to Bytes
                        'queue_file_path' => $db_file_path, // <--- ใช้ Relative Path ที่สร้างขึ้นใหม่
                        'queue_file_uploaded_at' => date('Y-m-d H:i:s'),
                        'queue_file_uploaded_by' => $uploader_name,
                        'queue_file_status' => 'active'
                    ];

                    if ($this->db->insert('tbl_queue_files', $file_data)) {
                        $uploaded_files[] = [
                            'file_name' => $upload_data['file_name'],
                            'original_name' => $upload_data['orig_name']
                        ];
                        log_message('info', "File uploaded for queue {$queue_id}: {$upload_data['orig_name']}");
                    } else {
                        log_message('error', "Failed to save file data to DB for: {$upload_data['orig_name']}");
                    }
                } else {
                    $upload_errors = $this->upload->display_errors('', '');
                    log_message('error', "File upload failed for {$_FILES['queue_files']['name'][$i]}: {$upload_errors}");
                }
            }
        }

        return $uploaded_files;
    }





    /**
     * จัดการการอัปโหลดไฟล์แนบหลายไฟล์ (ฉบับปรับปรุง)
     * @param string $queue_id รหัสคิวสำหรับอ้างอิง
     * @param string $uploader_name ชื่อผู้อัปโหลด
     * @return array รายการไฟล์ที่อัปโหลดสำเร็จ
     */
    private function handle_multiple_files_upload($queue_id, $uploader_name)
    {
        // ตรวจสอบว่ามีไฟล์ส่งมาในชื่อ 'queue_files' หรือไม่
        if (empty($_FILES['queue_files']['name']) || !is_array($_FILES['queue_files']['name']) || empty($_FILES['queue_files']['name'][0])) {
            log_message('info', 'No files were selected for upload for queue ID: ' . $queue_id);
            return []; // ไม่มีไฟล์ให้อัปโหลด
        }

        $this->load->library('upload');

        // --- จุดที่ 1: การกำหนด Path (ใช้ FCPATH) ---
        // Best Practice: ใช้ FCPATH เพื่อให้ได้ Physical Path ที่ถูกต้องเสมอ
        $relative_path = 'docs/queue_files/';
        $physical_upload_path = FCPATH . $relative_path;

        // ตรวจสอบและสร้าง Directory ถ้ายังไม่มี
        if (!is_dir($physical_upload_path)) {
            if (!mkdir($physical_upload_path, 0777, true)) {
                log_message('error', 'Failed to create upload directory: ' . $physical_upload_path);
                return []; // ไม่สามารถสร้างโฟลเดอร์ได้
            }
        }

        // --- จุดที่ 2: การตั้งค่า Upload Library ---
        $config = [
            'upload_path' => $physical_upload_path, // Path จริงสำหรับให้ Library บันทึกไฟล์
            'allowed_types' => 'jpg|jpeg|png|gif|pdf|doc|docx|txt|xlsx|xls',
            'max_size' => 10240, // 10MB
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE
        ];

        $uploaded_files = [];
        $file_count = count($_FILES['queue_files']['name']);

        // --- จุดที่ 3: วนลูปเพื่ออัปโหลดไฟล์ทีละไฟล์ ---
        for ($i = 0; $i < $file_count; $i++) {
            // ตรวจสอบให้แน่ใจว่ามีไฟล์และไม่มี error จากฝั่ง client
            if (!empty($_FILES['queue_files']['name'][$i]) && $_FILES['queue_files']['error'][$i] === UPLOAD_ERR_OK) {

                // เตรียมข้อมูลไฟล์แต่ละรายการสำหรับ Library Upload
                $_FILES['single_file'] = [
                    'name' => $_FILES['queue_files']['name'][$i],
                    'type' => $_FILES['queue_files']['type'][$i],
                    'tmp_name' => $_FILES['queue_files']['tmp_name'][$i],
                    'error' => $_FILES['queue_files']['error'][$i],
                    'size' => $_FILES['queue_files']['size'][$i]
                ];

                $this->upload->initialize($config);

                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();

                    // --- จุดที่ 4: เตรียมข้อมูลสำหรับบันทึกลง Database ---
                    // สร้าง Path สำหรับคอลัมน์ในฐานข้อมูล (Relative Path)
                    $db_file_path = $relative_path . $upload_data['file_name'];

                    $file_data = [
                        'queue_file_ref_id' => $queue_id,
                        'queue_file_name' => $upload_data['file_name'],
                        'queue_file_original_name' => $upload_data['orig_name'],
                        'queue_file_type' => $upload_data['file_type'],
                        'queue_file_size' => $upload_data['file_size'] * 1024, // Library ส่งค่าเป็น KB, แปลงเป็น Bytes
                        'queue_file_path' => $db_file_path, // ใช้ Relative Path
                        'queue_file_uploaded_at' => date('Y-m-d H:i:s'),
                        'queue_file_uploaded_by' => $uploader_name,
                        'queue_file_status' => 'active'
                    ];

                    // บันทึกข้อมูลลงฐานข้อมูล
                    if ($this->db->insert('tbl_queue_files', $file_data)) {
                        $uploaded_files[] = [
                            'file_name' => $upload_data['file_name'],
                            'original_name' => $upload_data['orig_name']
                        ];
                        log_message('info', "File uploaded for queue {$queue_id}: {$upload_data['orig_name']}");
                    } else {
                        // หากบันทึก DB ไม่สำเร็จ ควรลบไฟล์ที่อัปโหลดไปแล้วทิ้ง
                        unlink($upload_data['full_path']);
                        log_message('error', "DB insert failed for: {$upload_data['orig_name']}. File was deleted.");
                    }
                } else {
                    $upload_errors = $this->upload->display_errors('', '');
                    log_message('error', "File upload failed for {$_FILES['queue_files']['name'][$i]}: {$upload_errors}");
                }
            }
        }

        return $uploaded_files;
    }

    private function create_queue_status_change_notifications($queue_id, $queue_data, $new_status, $staff_data, $comment)
    {
        try {
            // ตรวจสอบว่ามีตาราง notifications หรือไม่
            if (!$this->db->table_exists('tbl_notifications')) {
                log_message('warning', 'tbl_notifications table does not exist, skipping notification creation');
                return;
            }

            $staff_name = $staff_data->m_fname . ' ' . $staff_data->m_lname;
            $current_time = date('Y-m-d H:i:s');

            // *** แก้ไข: ข้อความแจ้งเตือนสำหรับ Staff ***
            $staff_notification_messages = [
                'รับเรื่องพิจารณา' => "คิว #{$queue_id} ได้รับเรื่องพิจารณาโดย {$staff_name}",
                'คิวได้รับการยืนยัน' => "คิว #{$queue_id} ได้รับการยืนยันโดย {$staff_name}",
                'รับเรื่องแล้ว' => "คิว #{$queue_id} ได้รับเรื่องแล้วโดย {$staff_name}",
                'กำลังดำเนินการ' => "คิว #{$queue_id} กำลังดำเนินการโดย {$staff_name}",
                'เสร็จสิ้น' => "คิว #{$queue_id} เสร็จสิ้นแล้วโดย {$staff_name}",
                'ยกเลิก' => "คิว #{$queue_id} ถูกยกเลิกโดย {$staff_name}"
            ];

            $staff_message = $staff_notification_messages[$new_status] ?? "คิว #{$queue_id} อัปเดตสถานะเป็น: {$new_status} โดย {$staff_name}";

            // 1. แจ้งเตือนสำหรับ Staff ทั้งหมด
            $staff_data_json = json_encode([
                'queue_id' => $queue_id,
                'topic' => $queue_data->queue_topic,
                'requester' => $queue_data->queue_by,
                'phone' => $queue_data->queue_phone,
                'old_status' => $queue_data->queue_status,
                'new_status' => $new_status,
                'updated_by' => $staff_name,
                'comment' => $comment,
                'timestamp' => $current_time,
                'type' => 'staff_status_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'queue_status_update',
                'title' => 'อัปเดตสถานะคิว',
                'message' => $staff_message,
                'reference_id' => 0,
                'reference_table' => 'tbl_queue',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => $this->get_status_icon($new_status),
                'url' => site_url("Queue/queue_detail/{$queue_id}"),
                'data' => $staff_data_json,
                'created_at' => $current_time,
                'created_by' => $staff_data->m_id,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $staff_result = $this->db->insert('tbl_notifications', $staff_notification);

            if ($staff_result) {
                log_message('info', "Staff notification created for queue status update: {$queue_id}");
            }

            // 2. แจ้งเตือนสำหรับเจ้าของคิว (ถ้าเป็น public user)
            if ($queue_data->queue_user_type === 'public' && !empty($queue_data->queue_user_id)) {

                // *** แก้ไข: ข้อความแจ้งเตือนสำหรับ User ***
                $user_notification_messages = [
                    'รับเรื่องพิจารณา' => 'เจ้าหน้าที่ได้รับเรื่องพิจารณาแล้ว กำลังตรวจสอบข้อมูล',
                    'คิวได้รับการยืนยัน' => 'คิวของคุณได้รับการยืนยันแล้ว กรุณาเตรียมตัวตามกำหนด',
                    'รับเรื่องแล้ว' => 'เจ้าหน้าที่ได้รับเรื่องของคุณแล้ว',
                    'กำลังดำเนินการ' => 'เจ้าหน้าที่กำลังดำเนินการคิวของคุณ',
                    'รอดำเนินการ' => 'กรุณารอการดำเนินการจากเจ้าหน้าที่',
                    'เสร็จสิ้น' => 'คิวของคุณดำเนินการเสร็จสิ้นแล้ว',
                    'ยกเลิก' => 'คิวของคุณถูกยกเลิก',
                    'คิวได้ถูกยกเลิก' => 'คิวของคุณถูกยกเลิก'
                ];

                $user_message = $user_notification_messages[$new_status] ?? "สถานะคิวของคุณเปลี่ยนเป็น: {$new_status}";

                if (!empty($comment) && in_array($new_status, ['รับเรื่องพิจารณา', 'คิวได้รับการยืนยัน', 'รับเรื่องแล้ว'])) {
                    $user_message .= " - " . $comment;
                }

                $user_data_json = json_encode([
                    'queue_id' => $queue_id,
                    'queue_topic' => $queue_data->queue_topic,
                    'old_status' => $queue_data->queue_status,
                    'new_status' => $new_status,
                    'comment' => $comment,
                    'updated_by' => $staff_name,
                    'timestamp' => $current_time,
                    'type' => 'user_status_update'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification = [
                    'type' => 'queue_status_update',
                    'title' => 'อัปเดตสถานะคิวของคุณ',
                    'message' => $user_message,
                    'reference_id' => 0,
                    'reference_table' => 'tbl_queue',
                    'target_role' => 'public',
                    'target_user_id' => intval($queue_data->queue_user_id),
                    'priority' => $this->get_notification_priority($new_status),
                    'icon' => $this->get_status_icon($new_status),
                    'url' => site_url("Queue/my_queue_detail/{$queue_id}"),
                    'data' => $user_data_json,
                    'created_at' => $current_time,
                    'created_by' => $staff_data->m_id,
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $user_result = $this->db->insert('tbl_notifications', $user_notification);

                if ($user_result) {
                    log_message('info', "User notification created for queue status update: {$queue_id}");
                }
            }

            log_message('info', "Notifications created successfully for queue {$queue_id} status change to {$new_status}");

        } catch (Exception $e) {
            log_message('error', 'Failed to create queue status notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * *** เพิ่ม: Helper functions สำหรับ notifications ***
     */
    private function get_status_icon($status)
    {
        $icons = [
            'รอยืนยันการจอง' => 'fas fa-hourglass-half',
            'รับเรื่องพิจารณา' => 'fas fa-file-import', // แก้ไขใหม่
            'คิวได้รับการยืนยัน' => 'fas fa-check-circle',
            'รับเรื่องแล้ว' => 'fas fa-inbox',
            'กำลังดำเนินการ' => 'fas fa-cogs',
            'รอดำเนินการ' => 'fas fa-clock',
            'เสร็จสิ้น' => 'fas fa-check-double',
            'ยกเลิก' => 'fas fa-times-circle',
            'คิวได้ถูกยกเลิก' => 'fas fa-times-circle'
        ];

        return $icons[$status] ?? 'fas fa-sync-alt';
    }

    private function get_notification_priority($status)
    {
        $priorities = [
            'รอยืนยันการจอง' => 'normal',
            'รับเรื่องพิจารณา' => 'high', // แก้ไขใหม่
            'คิวได้รับการยืนยัน' => 'high',
            'รับเรื่องแล้ว' => 'high',
            'กำลังดำเนินการ' => 'normal',
            'รอดำเนินการ' => 'normal',
            'เสร็จสิ้น' => 'high',
            'ยกเลิก' => 'high',
            'คิวได้ถูกยกเลิก' => 'high'
        ];

        return $priorities[$status] ?? 'normal';
    }





    private function validate_queue_form_detailed($current_user)
    {
        // ตรวจสอบเรื่องที่ต้องการติดต่อ
        $queue_topic = trim($this->input->post('queue_topic'));
        if (empty($queue_topic)) {
            return [
                'success' => false,
                'type' => 'required_field',
                'message' => 'กรุณากรอกเรื่องที่ต้องการติดต่อ',
                'field' => 'queue_topic',
                'focus_field' => 'input[name="queue_topic"]'
            ];
        }

        if (mb_strlen($queue_topic) < 4) {
            return [
                'success' => false,
                'type' => 'min_length',
                'message' => 'เรื่องที่ต้องการติดต่อต้องมีอย่างน้อย 4 ตัวอักษร',
                'field' => 'queue_topic',
                'focus_field' => 'input[name="queue_topic"]'
            ];
        }

        if (mb_strlen($queue_topic) > 100) {
            return [
                'success' => false,
                'type' => 'max_length',
                'message' => 'เรื่องที่ต้องการติดต่อต้องไม่เกิน 100 ตัวอักษร',
                'field' => 'queue_topic',
                'focus_field' => 'input[name="queue_topic"]'
            ];
        }

        // ตรวจสอบรายละเอียด
        $queue_detail = trim($this->input->post('queue_detail'));
        if (empty($queue_detail)) {
            return [
                'success' => false,
                'type' => 'required_field',
                'message' => 'กรุณากรอกรายละเอียดเพิ่มเติม',
                'field' => 'queue_detail',
                'focus_field' => 'textarea[name="queue_detail"]'
            ];
        }

        if (mb_strlen($queue_detail) < 4) {
            return [
                'success' => false,
                'type' => 'min_length',
                'message' => 'รายละเอียดต้องมีอย่างน้อย 4 ตัวอักษร',
                'field' => 'queue_detail',
                'focus_field' => 'textarea[name="queue_detail"]'
            ];
        }

        // ตรวจสอบวันที่
        $queue_date = $this->input->post('queue_date');
        if (empty($queue_date)) {
            return [
                'success' => false,
                'type' => 'required_field',
                'message' => 'กรุณาเลือกวันที่ที่ต้องการจองคิว',
                'field' => 'queue_date',
                'focus_field' => 'input[name="queue_date_temp"]'
            ];
        }

        // ตรวจสอบว่าวันที่เป็นอนาคต
        if (strtotime($queue_date) <= time()) {
            return [
                'success' => false,
                'type' => 'invalid_date',
                'message' => 'วันที่จองต้องเป็นวันในอนาคต ไม่สามารถเลือกวันนี้หรือวันที่ผ่านมาแล้วได้',
                'field' => 'queue_date',
                'focus_field' => 'input[name="queue_date_temp"]'
            ];
        }

        // ตรวจสอบช่วงเวลา
        $queue_time = $this->input->post('queue_time');
        if (empty($queue_time)) {
            return [
                'success' => false,
                'type' => 'required_field',
                'message' => 'กรุณาเลือกช่วงเวลาที่ต้องการ',
                'field' => 'queue_time',
                'focus_field' => '#time_slots_container'
            ];
        }

        // ตรวจสอบข้อมูลส่วนตัว (สำหรับ guest)
        if (!$current_user['is_logged_in']) {

            // ตรวจสอบชื่อ-นามสกุล
            $queue_by = trim($this->input->post('queue_by'));
            if (empty($queue_by)) {
                return [
                    'success' => false,
                    'type' => 'required_field',
                    'message' => 'กรุณากรอกชื่อ-นามสกุล',
                    'field' => 'queue_by',
                    'focus_field' => 'input[name="queue_by"]'
                ];
            }

            if (mb_strlen($queue_by) < 4) {
                return [
                    'success' => false,
                    'type' => 'min_length',
                    'message' => 'ชื่อ-นามสกุลต้องมีอย่างน้อย 4 ตัวอักษร',
                    'field' => 'queue_by',
                    'focus_field' => 'input[name="queue_by"]'
                ];
            }

            // ตรวจสอบเบอร์โทรศัพท์
            $queue_phone = trim($this->input->post('queue_phone'));
            if (empty($queue_phone)) {
                return [
                    'success' => false,
                    'type' => 'required_field',
                    'message' => 'กรุณากรอกเบอร์โทรศัพท์',
                    'field' => 'queue_phone',
                    'focus_field' => 'input[name="queue_phone"]'
                ];
            }

            if (!preg_match('/^\d{10}$/', $queue_phone)) {
                return [
                    'success' => false,
                    'type' => 'invalid_format',
                    'message' => 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 10 หลัก (เช่น 0812345678)',
                    'field' => 'queue_phone',
                    'focus_field' => 'input[name="queue_phone"]'
                ];
            }

            // ตรวจสอบเลขบัตรประชาชน
            $queue_number = trim($this->input->post('queue_number'));
            if (empty($queue_number)) {
                return [
                    'success' => false,
                    'type' => 'required_field',
                    'message' => 'กรุณากรอกเลขบัตรประจำตัวประชาชน',
                    'field' => 'queue_number',
                    'focus_field' => 'input[name="queue_number"]'
                ];
            }

            if (!preg_match('/^\d{13}$/', $queue_number)) {
                return [
                    'success' => false,
                    'type' => 'invalid_format',
                    'message' => 'เลขบัตรประจำตัวประชาชนต้องเป็นตัวเลข 13 หลักเท่านั้น',
                    'field' => 'queue_number',
                    'focus_field' => 'input[name="queue_number"]'
                ];
            }

            // ตรวจสอบด้วยอัลกอริทึมไทย
            if (!$this->validate_thai_id_card_relaxed($queue_number)) {
                return [
                    'success' => false,
                    'type' => 'invalid_id_card',
                    'message' => 'เลขบัตรประจำตัวประชาชนไม่ถูกต้องตามมาตรฐาน กรุณาตรวจสอบอีกครั้ง',
                    'field' => 'queue_number',
                    'focus_field' => 'input[name="queue_number"]'
                ];
            }

            // ตรวจสอบอีเมล (ถ้ากรอก)
            $queue_email = trim($this->input->post('queue_email'));
            if (!empty($queue_email) && !filter_var($queue_email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'type' => 'invalid_email',
                    'message' => 'รูปแบบอีเมลไม่ถูกต้อง กรุณากรอกอีเมลที่ถูกต้อง',
                    'field' => 'queue_email',
                    'focus_field' => 'input[name="queue_email"]'
                ];
            }

            // ตรวจสอบที่อยู่
            $queue_address = trim($this->input->post('queue_address'));
            if (empty($queue_address)) {
                return [
                    'success' => false,
                    'type' => 'required_field',
                    'message' => 'กรุณากรอกที่อยู่เพิ่มเติม (บ้านเลขที่ ซอย ถนน)',
                    'field' => 'queue_address',
                    'focus_field' => 'input[name="additional_address_field"]'
                ];
            }

            if (mb_strlen($queue_address) < 2) {
                return [
                    'success' => false,
                    'type' => 'min_length',
                    'message' => 'ที่อยู่เพิ่มเติมต้องมีอย่างน้อย 2 ตัวอักษร',
                    'field' => 'queue_address',
                    'focus_field' => 'input[name="additional_address_field"]'
                ];
            }

            // ตรวจสอบตำบล
            $guest_district = trim($this->input->post('guest_district'));
            if (empty($guest_district)) {
                return [
                    'success' => false,
                    'type' => 'required_field',
                    'message' => 'กรุณาเลือกตำบลหรือกรอกรหัสไปรษณีย์เพื่อค้นหาข้อมูลที่อยู่',
                    'field' => 'guest_district',
                    'focus_field' => 'select[name="district_field"]'
                ];
            }
        }

        // ตรวจสอบเลขบัตรประชาชนสำหรับ public user ที่ไม่มี
        if ($current_user['is_logged_in'] && $current_user['user_type'] === 'public') {
            if (empty($current_user['user_info']['number'])) {
                return [
                    'success' => false,
                    'type' => 'missing_id_card',
                    'message' => 'จำเป็นต้องมีเลขบัตรประจำตัวประชาชนเพื่อการให้บริการ กรุณาเพิ่มข้อมูลก่อนจองคิว',
                    'field' => 'user_id_card',
                    'focus_field' => null,
                    'show_id_card_modal' => true
                ];
            }
        }

        return ['success' => true];
    }






    private function validate_thai_id_card_relaxed($id_card)
    {
        // ตรวจสอบรูปแบบพื้นฐาน
        if (!preg_match('/^\d{13}$/', $id_card)) {
            return false;
        }

        // ตรวจสอบเลขซ้ำทั้งหมด
        if (preg_match('/^(\d)\1{12}$/', $id_card)) {
            return false;
        }

        // ตรวจสอบเลขที่เป็นไปไม่ได้
        $invalid_patterns = ['0000000000000', '1111111111111', '2222222222222'];
        if (in_array($id_card, $invalid_patterns)) {
            return false;
        }

        // ตรวจสอบด้วยอัลกอริทึม MOD 11
        $digits = str_split($id_card);
        $weights = [13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($digits[$i]) * $weights[$i];
        }

        $remainder = $sum % 11;
        $check_digit = ($remainder < 2) ? (1 - $remainder) : (11 - $remainder);

        return $check_digit == intval($digits[12]);
    }




    /**
     * Callback validation สำหรับเลขบัตรประชาชนไทย
     * @param string $id_card เลขบัตรประชาชน
     * @return bool
     */
    public function validate_thai_id_card_field($id_card)
    {
        // เพิ่ม debug logging
        log_message('debug', 'Validating ID Card: ' . $id_card);

        // ตรวจสอบพื้นฐานก่อน
        if (empty($id_card) || !preg_match('/^\d{13}$/', $id_card)) {
            $this->form_validation->set_message('validate_thai_id_card_field', 'เลขบัตรประจำตัวประชาชนต้องเป็นตัวเลข 13 หลัก');
            return false;
        }

        // *** แก้ไข: ใช้การตรวจสอบที่ยืดหยุ่นกว่า ***
        if (!$this->validate_thai_id_card_relaxed($id_card)) {
            $this->form_validation->set_message('validate_thai_id_card_field', 'รูปแบบเลขบัตรประจำตัวประชาชนไม่ถูกต้อง');
            return false;
        }

        return true;
    }

    /**
     * ตรวจสอบความถูกต้องของเลขบัตรประจำตัวประชาชนไทย
     * @param string $id_card เลขบัตรประชาชน 13 หลัก
     * @return bool true ถ้าถูกต้อง, false ถ้าไม่ถูกต้อง
     */
    private function validate_thai_id_card($id_card)
    {
        // ตรวจสอบรูปแบบพื้นฐาน
        if (!preg_match('/^\d{13}$/', $id_card)) {
            return false;
        }

        // ตรวจสอบเลขซ้ำทั้งหมด (เช่น 1111111111111)
        if (preg_match('/^(\d)\1{12}$/', $id_card)) {
            return false;
        }

        // ตรวจสอบด้วยอัลกอริทึม MOD 11 (Thai ID Card Algorithm)
        $digits = str_split($id_card);
        $weights = [13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($digits[$i]) * $weights[$i];
        }

        $remainder = $sum % 11;
        $check_digit = ($remainder < 2) ? (1 - $remainder) : (11 - $remainder);

        return $check_digit == intval($digits[12]);
    }


    /**
     * *** จัดการไฟล์แนบ ***
     */
    private function handle_file_uploads($queue_id)
    {
        $this->load->library('upload');

        $config['upload_path'] = './docs/queue_files/';
        $config['allowed_types'] = 'jpg|jpeg|png|pdf|gif';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE;

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->upload->initialize($config);

        for ($i = 0; $i < count($_FILES['queue_files']['name']); $i++) {
            if (!empty($_FILES['queue_files']['name'][$i])) {
                $_FILES['file']['name'] = $_FILES['queue_files']['name'][$i];
                $_FILES['file']['type'] = $_FILES['queue_files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['queue_files']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['queue_files']['error'][$i];
                $_FILES['file']['size'] = $_FILES['queue_files']['size'][$i];

                if ($this->upload->do_upload('file')) {
                    $upload_data = $this->upload->data();

                    $file_data = [
                        'queue_file_ref_id' => $queue_id,
                        'queue_file_name' => $upload_data['file_name'],
                        'queue_file_original_name' => $_FILES['queue_files']['name'][$i],
                        'queue_file_type' => $upload_data['file_type'],
                        'queue_file_size' => $upload_data['file_size'],
                        'queue_file_path' => $upload_data['full_path']
                    ];

                    $this->db->insert('tbl_queue_files', $file_data);
                }
            }
        }
    }









    private function check_activity_slider_availability_safe()
    {
        try {
            // ตรวจสอบว่ามี activity_model หรือไม่
            if (!isset($this->activity_model)) {
                log_message('debug', 'Activity model not found');
                return false;
            }

            // ตรวจสอบว่ามี method activity_frontend หรือไม่
            if (!method_exists($this->activity_model, 'activity_frontend')) {
                log_message('debug', 'Activity model method not found');
                return false;
            }

            // ตรวจสอบว่ามีข้อมูล activity หรือไม่
            $activities = $this->activity_model->activity_frontend();
            $has_activities = !empty($activities) && is_array($activities) && count($activities) > 0;

            // ตรวจสอบว่ามี JavaScript file สำหรับ slider หรือไม่
            $js_file_paths = [
                './assets/js/activity-slider.js',
                './assets/frontend/js/activity-slider.js',
                './assets/frontend/js/components/activity-slider.js',
                './assets/vendor/activity-slider/activity-slider.js'
            ];

            $js_file_exists = false;
            foreach ($js_file_paths as $path) {
                if (file_exists($path)) {
                    $js_file_exists = true;
                    log_message('debug', 'Activity slider JS found at: ' . $path);
                    break;
                }
            }

            log_message('debug', 'Activity slider check: activities=' . ($has_activities ? 'YES' : 'NO') .
                ', js_file=' . ($js_file_exists ? 'YES' : 'NO'));

            return $has_activities && $js_file_exists;

        } catch (Exception $e) {
            log_message('error', 'Error checking activity slider availability: ' . $e->getMessage());
            return false;
        }
    }






    private function check_queue_access_permission_for_guest($queue_info, $user_type, $user_id, $is_logged_in)
    {
        // Log การตรวจสอบ
        log_message('info', 'Queue Access Check (Guest Restricted): ' . json_encode([
            'queue_id' => $queue_info['queue_id'],
            'queue_user_type' => $queue_info['queue_user_type'],
            'queue_user_id' => $queue_info['queue_user_id'],
            'current_user_type' => $user_type,
            'current_user_id' => $user_id,
            'is_logged_in' => $is_logged_in
        ]));

        // *** Case 1: Staff - เข้าถึงได้ทุกคิว ***
        if ($user_type === 'staff' && $is_logged_in) {
            $this->db->select('m_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', intval($user_id));
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if ($staff_check) {
                log_message('info', 'Staff access granted');
                return ['allowed' => true, 'message' => 'Staff access granted'];
            } else {
                log_message('warning', 'Staff access denied: Invalid credentials');
                return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่มีสิทธิ์เข้าถึง'];
            }
        }

        // *** Case 2: Guest (ไม่ได้ login) - ดูได้เฉพาะคิว Guest เท่านั้น ***
        if (!$is_logged_in || $user_type === 'guest') {
            if ($queue_info['queue_user_type'] === 'guest') {
                log_message('info', 'Guest access granted: Guest queue only');
                return ['allowed' => true, 'message' => 'Guest can access guest queue only'];
            } else {
                log_message('warning', 'Guest access denied: Cannot access ' . $queue_info['queue_user_type'] . ' queue');
                return [
                    'allowed' => false,
                    'error_type' => 'permission_denied',
                    'message' => 'ผู้ใช้ทั่วไปสามารถติดตามได้เฉพาะคิวของผู้ใช้ทั่วไปเท่านั้น กรุณาเข้าสู่ระบบเพื่อดูคิวประเภทอื่น'
                ];
            }
        }

        // *** Case 3: Public user (login แล้ว) - ดูได้ทุกคิว ***
        if ($user_type === 'public' && $is_logged_in) {
            if (empty($user_id)) {
                return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่พบข้อมูลผู้ใช้'];
            }

            // ตรวจสอบ user ในฐานข้อมูล
            $this->db->select('id, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('id', intval($user_id));
            $this->db->where('mp_status', 1);
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่พบข้อมูลผู้ใช้หรือบัญชีถูกระงับ'];
            }

            // Public user ดูได้ทุกคิว (รวม guest และ public)
            log_message('info', 'Public user access granted: Can view all queues');
            return ['allowed' => true, 'message' => 'Public user can access all queues'];
        }

        // *** Default: ไม่มีสิทธิ์ ***
        return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่มีสิทธิ์เข้าถึงข้อมูลนี้'];
    }



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
            // *** แก้ไข: เพิ่มการตรวจสอบที่เข้มงวดสำหรับแต่ละ model ***
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
                        log_message('warning', "Error loading {$model_name}: " . $e->getMessage());
                        $data[$config['key']] = [];
                    }
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }




    private function check_activity_slider_availability()
    {
        try {
            // ตรวจสอบว่ามีข้อมูล activity หรือไม่
            if (isset($this->activity_model) && method_exists($this->activity_model, 'activity_frontend')) {
                $activities = $this->activity_model->activity_frontend();
                return !empty($activities);
            }
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error checking activity slider: ' . $e->getMessage());
            return false;
        }
    }





    private function check_queue_access_permission_strict($queue_info, $user_type, $user_id, $is_logged_in)
    {
        // Log การตรวจสอบ
        log_message('info', 'Strict Queue Access Check: ' . json_encode([
            'queue_id' => $queue_info['queue_id'],
            'queue_user_type' => $queue_info['queue_user_type'],
            'queue_user_id' => $queue_info['queue_user_id'],
            'current_user_type' => $user_type,
            'current_user_id' => $user_id,
            'is_logged_in' => $is_logged_in
        ]));

        // *** Case 1: Staff - เข้าถึงได้ทุกคิว ***
        if ($user_type === 'staff' && $is_logged_in) {
            $this->db->select('m_id');
            $this->db->from('tbl_member');
            $this->db->where('m_id', intval($user_id));
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if ($staff_check) {
                log_message('info', 'Staff access granted');
                return ['allowed' => true, 'message' => 'Staff access granted'];
            } else {
                log_message('warning', 'Staff access denied: Invalid credentials');
                return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่มีสิทธิ์เข้าถึง'];
            }
        }

        // *** Case 2: Guest (ไม่ได้ login) - ดูได้เฉพาะคิว Guest ***
        if (!$is_logged_in || $user_type === 'guest') {
            if ($queue_info['queue_user_type'] === 'guest') {
                log_message('info', 'Guest access granted: Guest queue');
                return ['allowed' => true, 'message' => 'Guest can access guest queue'];
            } else {
                log_message('info', 'Guest access denied: Not a guest queue - Type: ' . $queue_info['queue_user_type']);
                return [
                    'allowed' => false,
                    'error_type' => 'permission_denied',
                    'message' => 'ผู้ใช้ทั่วไปสามารถดูได้เฉพาะคิวของผู้ใช้ทั่วไปเท่านั้น'
                ];
            }
        }

        // *** Case 3: Public user (login แล้ว) - ดูได้ทุกคิว หรือเฉพาะของตนเอง ***
        if ($user_type === 'public' && $is_logged_in) {
            if (empty($user_id)) {
                return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่พบข้อมูลผู้ใช้'];
            }

            // ตรวจสอบ user ในฐานข้อมูล
            $this->db->select('id, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('id', intval($user_id));
            $this->db->where('mp_status', 1);
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่พบข้อมูลผู้ใช้หรือบัญชีถูกระงับ'];
            }

            // *** Option 1: Public user ดูได้ทุกคิว ***
            log_message('info', 'Public user access granted: Can view all queues');
            return ['allowed' => true, 'message' => 'Public user can access all queues'];

            // *** Option 2: Public user ดูได้เฉพาะคิวของตนเอง (uncomment ถ้าต้องการ) ***
            /*
            if ($queue_info['queue_user_type'] === 'public' && $queue_info['queue_user_id'] == $user_data->id) {
                return ['allowed' => true, 'message' => 'Public user owns this queue'];
            } else {
                return [
                    'allowed' => false, 
                    'error_type' => 'permission_denied', 
                    'message' => 'คุณสามารถดูได้เฉพาะคิวของตัวเองเท่านั้น'
                ];
            }
            */
        }

        // *** Default: ไม่มีสิทธิ์ ***
        return ['allowed' => false, 'error_type' => 'permission_denied', 'message' => 'ไม่มีสิทธิ์เข้าถึงข้อมูลนี้'];
    }




    private function check_queue_cancel_permission_strict($queue_info, $user_type, $user_id, $guest_phone = '', $guest_name = '')
    {
        log_message('info', 'Strict Cancel Permission Check: ' . json_encode([
            'queue_id' => $queue_info['queue_id'],
            'queue_user_type' => $queue_info['queue_user_type'],
            'current_user_type' => $user_type,
            'has_guest_verification' => !empty($guest_phone) && !empty($guest_name)
        ]));

        // *** Case 1: Staff - ยกเลิกได้ทุกคิว ***
        if ($user_type === 'staff') {
            $this->db->select('m_id, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', intval($user_id));
            $staff_check = $this->db->get()->row();

            if ($staff_check && $staff_check->m_status == '1') {
                return ['allowed' => true, 'message' => 'Staff can cancel any queue'];
            } else {
                return ['allowed' => false, 'message' => 'ไม่มีสิทธิ์เจ้าหน้าที่'];
            }
        }

        // *** Case 2: Guest - ยกเลิกได้ถ้าข้อมูลตรงกัน ***
        if ($user_type === 'guest') {
            // ตรวจสอบว่าคิวเป็น guest queue
            if ($queue_info['queue_user_type'] !== 'guest') {
                return ['allowed' => false, 'message' => 'คิวนี้ไม่ใช่คิวของผู้ใช้ทั่วไป'];
            }

            // ตรวจสอบข้อมูลยืนยันตัวตน
            if (empty($guest_phone) || empty($guest_name)) {
                return ['allowed' => false, 'message' => 'กรุณากรอกข้อมูลยืนยันตัวตน'];
            }

            // ตรวจสอบเบอร์โทรและชื่อ
            $phone_match = ($queue_info['queue_phone'] === $guest_phone);
            $name_match = (trim(strtolower($queue_info['queue_by'])) === trim(strtolower($guest_name)));

            if ($phone_match && $name_match) {
                log_message('info', 'Guest cancel permission granted: Identity verified');
                return ['allowed' => true, 'message' => 'Guest identity verified'];
            } else {
                log_message('warning', 'Guest cancel permission denied: Identity mismatch');
                return ['allowed' => false, 'message' => 'ข้อมูลยืนยันตัวตนไม่ถูกต้อง'];
            }
        }

        // *** Case 3: Public user - ยกเลิกได้เฉพาะคิวของตนเอง ***
        if ($user_type === 'public') {
            if ($queue_info['queue_user_type'] !== 'public') {
                return ['allowed' => false, 'message' => 'คุณสามารถยกเลิกได้เฉพาะคิวที่จองด้วยบัญชีของคุณ'];
            }

            $this->db->select('id, mp_status');
            $this->db->from('tbl_member_public');
            $this->db->where('id', intval($user_id));
            $user_data = $this->db->get()->row();

            if (!$user_data || $user_data->mp_status != 1) {
                return ['allowed' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้หรือบัญชีถูกระงับ'];
            }

            $user_id_match = ($queue_info['queue_user_id'] == $user_data->id);

            if ($user_id_match) {
                return ['allowed' => true, 'message' => 'Public user owns this queue'];
            } else {
                return ['allowed' => false, 'message' => 'คุณสามารถยกเลิกได้เฉพาะคิวของตนเองเท่านั้น'];
            }
        }

        return ['allowed' => false, 'message' => 'ไม่มีสิทธิ์ยกเลิกคิว'];
    }


    private function create_queue_creation_notifications($queue_id, $queue_data, $current_user)
    {
        if (!$this->db->table_exists('tbl_notifications')) {
            return;
        }

        try {
            // Notification สำหรับ Staff
            $staff_data_json = json_encode([
                'queue_id' => $queue_id,
                'topic' => $queue_data['queue_topic'],
                'requester' => $queue_data['queue_by'],
                'phone' => $queue_data['queue_phone'],
                'user_type' => $current_user['user_type'],
                'user_id' => $current_user['user_info']['id'] ?? null,
                'queue_date' => $queue_data['queue_date'],
                'queue_time_slot' => $queue_data['queue_time_slot'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
                'type' => 'staff_notification'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'queue',
                'title' => 'การจองคิวใหม่',
                'message' => "มีการจองคิวใหม่: {$queue_data['queue_topic']} โดย {$queue_data['queue_by']} ({$current_user['user_type']})",
                'reference_id' => 0,
                'reference_table' => 'tbl_queue',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-calendar-plus',
                'url' => site_url("Queue/queue_detail/{$queue_id}"),
                'data' => $staff_data_json,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => ($current_user['is_logged_in'] && isset($current_user['user_info']['id'])) ? intval($current_user['user_info']['id']) : 0,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $this->db->insert('tbl_notifications', $staff_notification);

            // Notification สำหรับ Public user (ถ้าเป็น public user ที่ login)
            if ($current_user['is_logged_in'] && $current_user['user_type'] === 'public') {
                $individual_data_json = json_encode([
                    'queue_id' => $queue_id,
                    'topic' => $queue_data['queue_topic'],
                    'status' => $queue_data['queue_status'],
                    'queue_by' => $queue_data['queue_by'],
                    'phone' => $queue_data['queue_phone'],
                    'queue_date' => $queue_data['queue_date'],
                    'queue_time_slot' => $queue_data['queue_time_slot'] ?? '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'follow_url' => site_url("Queue/follow_queue?auto_search={$queue_id}"),
                    'type' => 'individual_confirmation'
                ], JSON_UNESCAPED_UNICODE);

                $individual_notification = [
                    'type' => 'queue',
                    'title' => 'คุณได้จองคิวสำเร็จ',
                    'message' => "การจองคิว \"{$queue_data['queue_topic']}\" ของคุณได้รับการบันทึกเรียบร้อยแล้ว หมายเลขคิว: {$queue_id}",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_queue',
                    'target_role' => 'public',
                    'target_user_id' => intval($current_user['user_info']['id']),
                    'priority' => 'high',
                    'icon' => 'fas fa-check-circle',
                    'url' => site_url("Queue/my_queue_detail/{$queue_id}"),
                    'data' => $individual_data_json,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => intval($current_user['user_info']['id']),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $this->db->insert('tbl_notifications', $individual_notification);
            }

            log_message('info', "Queue creation notifications created for: {$queue_id}");

        } catch (Exception $e) {
            log_message('error', 'Failed to create queue creation notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * สร้างการแจ้งเตือนเมื่อยกเลิกคิว
     */
    private function create_queue_cancellation_notifications($queue_id, $queue_info, $cancel_reason, $user_type, $user_id)
    {
        if (!$this->db->table_exists('tbl_notifications')) {
            return;
        }

        try {
            // Notification สำหรับ Staff
            $staff_data_json = json_encode([
                'queue_id' => $queue_id,
                'topic' => $queue_info['queue_topic'] ?? '',
                'requester' => $queue_info['queue_by'],
                'phone' => $queue_info['queue_phone'],
                'cancel_reason' => $cancel_reason,
                'cancelled_by_type' => $user_type,
                'cancelled_at' => date('Y-m-d H:i:s'),
                'type' => 'queue_cancelled'
            ], JSON_UNESCAPED_UNICODE);

            $staff_notification = [
                'type' => 'queue_cancelled',
                'title' => 'คิวถูกยกเลิก',
                'message' => "คิว {$queue_id} ({$queue_info['queue_topic']}) ถูกยกเลิกโดย {$queue_info['queue_by']} ({$user_type})",
                'reference_id' => 0,
                'reference_table' => 'tbl_queue',
                'target_role' => 'staff',
                'priority' => 'normal',
                'icon' => 'fas fa-times-circle',
                'url' => site_url("Queue/queue_detail/{$queue_id}"),
                'data' => $staff_data_json,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => !empty($user_id) ? intval($user_id) : 0,
                'is_read' => 0,
                'is_system' => 1,
                'is_archived' => 0
            ];

            $this->db->insert('tbl_notifications', $staff_notification);

            // Notification สำหรับ Public user (ถ้าเป็น public user)
            if ($user_type === 'public' && !empty($user_id)) {
                $user_data_json = json_encode([
                    'queue_id' => $queue_id,
                    'queue_topic' => $queue_info['queue_topic'],
                    'cancel_reason' => $cancel_reason,
                    'cancelled_at' => date('Y-m-d H:i:s'),
                    'type' => 'user_queue_cancelled'
                ], JSON_UNESCAPED_UNICODE);

                $user_notification = [
                    'type' => 'queue_cancelled',
                    'title' => 'คุณได้ยกเลิกคิวเรียบร้อยแล้ว',
                    'message' => "คิว {$queue_id} \"{$queue_info['queue_topic']}\" ได้ถูกยกเลิกตามที่คุณร้องขอ",
                    'reference_id' => 0,
                    'reference_table' => 'tbl_queue',
                    'target_role' => 'public',
                    'target_user_id' => intval($user_id),
                    'priority' => 'high',
                    'icon' => 'fas fa-check-circle',
                    'url' => site_url("Queue/my_queue_status"),
                    'data' => $user_data_json,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => intval($user_id),
                    'is_read' => 0,
                    'is_system' => 1,
                    'is_archived' => 0
                ];

                $this->db->insert('tbl_notifications', $user_notification);
            }

            log_message('info', "Queue cancellation notifications created for: {$queue_id}");

        } catch (Exception $e) {
            log_message('error', 'Failed to create cancellation notifications: ' . $e->getMessage());
            throw $e;
        }
    }



    // *** เพิ่มฟังก์ชันตรวจสอบสิทธิ์การเข้าถึงคิว ***
// เพิ่มฟังก์ชันนี้ใหม่ในคลาส Queue

    private function check_queue_access_permission($queue_info, $user_type, $user_id, $is_logged_in)
    {
        // Log สำหรับ debug
        log_message('info', 'Queue Access Check: ' . json_encode([
            'queue_id' => $queue_info['queue_id'],
            'queue_user_type' => $queue_info['queue_user_type'],
            'queue_user_id' => $queue_info['queue_user_id'],
            'queue_phone' => $queue_info['queue_phone'] ?? 'not_set',
            'current_user_type' => $user_type,
            'current_user_id' => $user_id,
            'is_logged_in' => $is_logged_in
        ]));

        // *** Case 1: Staff - เข้าถึงได้ทุกคิว ***
        if ($user_type === 'staff') {
            log_message('info', 'Access granted: Staff user can access all queues');
            return true;
        }

        // *** Case 2: Guest (ไม่ได้ login) ***
        if (!$is_logged_in || $user_type === 'guest') {
            // Guest เข้าถึงได้เฉพาะคิวที่เป็น guest เท่านั้น
            $can_access = ($queue_info['queue_user_type'] === 'guest');
            log_message('info', 'Guest access check: ' . ($can_access ? 'GRANTED' : 'DENIED') . ' - Queue type: ' . $queue_info['queue_user_type']);
            return $can_access;
        }

        // *** Case 3: Public user (login แล้ว) - ตรวจสอบแบบเข้มงวด ***
        if ($user_type === 'public') {
            // *** ตรวจสอบด้วยเบอร์โทรศัพท์แทน user_id เพื่อความปลอดภัย ***
            if (empty($user_id)) {
                log_message('warning', 'Public user access denied: No user_id');
                return false;
            }

            // ดึงเบอร์โทรของ public user ปัจจุบัน
            $this->db->select('mp_phone');
            $this->db->from('tbl_member_public');
            $this->db->where('id', intval($user_id));
            $user_data = $this->db->get()->row();

            if (!$user_data || empty($user_data->mp_phone)) {
                log_message('warning', 'Public user not found or no phone: user_id=' . $user_id);
                return false;
            }

            // *** ตรวจสอบความเป็นเจ้าของคิว ***
            // 1. คิวต้องเป็นของ public user
            if ($queue_info['queue_user_type'] !== 'public') {
                log_message('info', 'Public user access denied: Queue is not public type (' . $queue_info['queue_user_type'] . ')');
                return false;
            }

            // 2. เบอร์โทรต้องตรงกัน
            $phone_match = ($queue_info['queue_phone'] === $user_data->mp_phone);
            if (!$phone_match) {
                log_message('warning', 'Public user access denied: Phone mismatch - Queue: ' . $queue_info['queue_phone'] . ', User: ' . $user_data->mp_phone);
                return false;
            }

            // 3. user_id ต้องตรงกัน (double check)
            $user_id_match = ($queue_info['queue_user_id'] == $user_id);
            if (!$user_id_match) {
                log_message('warning', 'Public user access denied: User ID mismatch - Queue: ' . $queue_info['queue_user_id'] . ', Current: ' . $user_id);
                return false;
            }

            log_message('info', 'Public user access granted: All checks passed');
            return true;
        }

        // *** Default: ไม่มีสิทธิ์ ***
        log_message('warning', 'Access denied: Unknown user type or permission rules');
        return false;
    }




    /**
     * ฟังก์ชันดึงรายการคิวของผู้ใช้ผ่าน AJAX
     */
    public function get_user_queues()
    {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบว่า Queue_model โหลดแล้วหรือไม่
            if (!isset($this->queue_model)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ระบบไม่พร้อมใช้งาน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบการ login
            $user_phone = null;
            $user_type = 'guest';

            if ($this->session->userdata('mp_phone')) {
                $user_phone = $this->session->userdata('mp_phone');
                $user_type = 'public';
            } elseif ($this->session->userdata('m_phone')) {
                $user_phone = $this->session->userdata('m_phone');
                $user_type = 'staff';
            }

            if (!$user_phone) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาเข้าสู่ระบบก่อน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $user_queues = $this->queue_model->get_queues_by_phone($user_phone);

            echo json_encode([
                'success' => true,
                'message' => 'ดึงข้อมูลสำเร็จ',
                'data' => [
                    'queues' => $user_queues ?: [],
                    'user_type' => $user_type
                ]
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Error in get_user_queues: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }
    /**
     * ยกเลิกคิว (สำหรับผู้ใช้)
     */
    public function cancel_queue()
    {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        ini_set('display_errors', 0);

        try {
            if (!$this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // รับข้อมูลจาก POST
            $queue_id = trim($this->input->post('queue_id'));
            $cancel_reason = trim($this->input->post('cancel_reason'));
            $user_type = $this->input->post('user_type') ?: 'guest';
            $user_id = $this->input->post('user_id');

            // ข้อมูลสำหรับ Guest
            $guest_phone = $this->input->post('guest_phone');
            $guest_name = $this->input->post('guest_name');

            log_message('info', "Cancel queue request - Queue: {$queue_id}, User Type: {$user_type}, User ID: {$user_id}");

            // Validation
            if (empty($queue_id) || empty($cancel_reason)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (mb_strlen($cancel_reason) < 5) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เหตุผลการยกเลิกต้องมีอย่างน้อย 5 ตัวอักษร'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Validation สำหรับ Guest
            if ($user_type === 'guest') {
                if (empty($guest_phone) || empty($guest_name)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'กรุณากรอกเบอร์โทรศัพท์และชื่อเพื่อยืนยันตัวตน'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                if (!preg_match('/^\d{10}$/', $guest_phone)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            // ค้นหาข้อมูลคิว
            $this->db->select('queue_id, queue_user_type, queue_user_id, queue_by, queue_phone, queue_status, queue_topic');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $queue_query = $this->db->get();

            if ($queue_query->num_rows() === 0) {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'not_found',
                    'message' => 'ไม่พบหมายเลขคิวที่ระบุ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $queue_info = $queue_query->row_array();

            // ตรวจสอบสิทธิ์ในการยกเลิก
            $permission_result = $this->check_queue_cancel_permission_strict($queue_info, $user_type, $user_id, $guest_phone, $guest_name);

            if (!$permission_result['allowed']) {
                echo json_encode([
                    'success' => false,
                    'error_type' => 'permission_denied',
                    'message' => $permission_result['message']
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบสถานะคิว
            if (in_array($queue_info['queue_status'], ['ยกเลิก', 'เสร็จสิ้น', 'คิวได้ถูกยกเลิก'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถยกเลิกคิวที่มีสถานะ ' . $queue_info['queue_status'] . ' ได้'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ยกเลิกคิว
            $this->db->trans_start();

            // อัพเดทข้อมูลในตาราง tbl_queue
            $update_data = [
                'queue_status' => 'ยกเลิก',
                'queue_dateupdate' => date('Y-m-d H:i:s'),
                'queue_cancel_reason' => $cancel_reason,
                'queue_cancelled_by' => $queue_info['queue_by'],
                'queue_cancelled_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('queue_id', $queue_id);
            $update_result = $this->db->update('tbl_queue', $update_data);

            if (!$update_result) {
                $this->db->trans_rollback();
                throw new Exception('Failed to update queue status');
            }

            // เพิ่มประวัติในตาราง tbl_queue_detail
            $detail_data = [
                'queue_detail_case_id' => $queue_id,
                'queue_detail_status' => 'ยกเลิก',
                'queue_detail_by' => $queue_info['queue_by'],
                'queue_detail_com' => $cancel_reason,
                'queue_detail_datesave' => date('Y-m-d H:i:s')
            ];

            $detail_result = $this->db->insert('tbl_queue_detail', $detail_data);

            if (!$detail_result) {
                $this->db->trans_rollback();
                throw new Exception('Failed to insert queue detail');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            log_message('info', "Queue {$queue_id} cancelled successfully");

            // สร้างการแจ้งเตือน
            try {
                $this->create_queue_cancellation_notifications($queue_id, $queue_info, $cancel_reason, $user_type, $user_id);
            } catch (Exception $e) {
                log_message('error', 'Failed to create cancellation notification: ' . $e->getMessage());
            }

            echo json_encode([
                'success' => true,
                'message' => 'ยกเลิกคิวเรียบร้อยแล้ว',
                'queue_id' => $queue_id,
                'cancel_reason' => $cancel_reason
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            log_message('error', 'Cancel queue error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }








    private function check_queue_detail_access_permission($queue_data, $user_type, $user_info)
    {
        // Staff สามารถดูได้ทุกคิว
        if ($user_type === 'staff' && !empty($user_info)) {
            // ตรวจสอบ staff ในฐานข้อมูล
            $staff_id = is_array($user_info) ? ($user_info['id'] ?? 0) : (is_object($user_info) ? ($user_info->id ?? 0) : 0);

            if ($staff_id) {
                $this->db->select('m_id');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $staff_id);
                $this->db->where('m_status', '1');
                $staff_check = $this->db->get()->row();

                return !empty($staff_check);
            }
        }

        // Guest ไม่สามารถเข้าถึงหน้านี้ได้
        if ($user_type === 'guest' || empty($user_info)) {
            return false;
        }

        // Public user สามารถดูได้เฉพาะคิวของตนเอง
        if ($user_type === 'public') {
            $user_phone = is_array($user_info) ? ($user_info['phone'] ?? '') : (is_object($user_info) ? ($user_info->phone ?? '') : '');
            $user_id = is_array($user_info) ? ($user_info['id'] ?? 0) : (is_object($user_info) ? ($user_info->id ?? 0) : 0);

            // ตรวจสอบความเป็นเจ้าของ
            return ($queue_data->queue_user_type === 'public' &&
                $queue_data->queue_user_id == $user_id &&
                $queue_data->queue_phone === $user_phone);
        }

        return false;
    }






    private function check_queue_cancel_permission($queue_info, $user_type, $user_id)
    {
        // Log สำหรับ debug
        log_message('info', 'Queue Cancel Permission Check: ' . json_encode([
            'queue_id' => $queue_info['queue_id'],
            'queue_user_type' => $queue_info['queue_user_type'],
            'queue_user_id' => $queue_info['queue_user_id'],
            'queue_phone' => $queue_info['queue_phone'],
            'current_user_type' => $user_type,
            'current_user_id' => $user_id
        ]));

        // *** Case 1: Staff - ยกเลิกได้ทุกคิว ***
        if ($user_type === 'staff') {
            log_message('info', 'Cancel permission granted: Staff can cancel all queues');
            return true;
        }

        // *** Case 2: Guest - ไม่สามารถยกเลิกได้ ***
        if ($user_type === 'guest' || empty($user_id)) {
            log_message('info', 'Cancel permission denied: Guest users cannot cancel queues');
            return false;
        }

        // *** Case 3: Public user - ตรวจสอบด้วยเบอร์โทรศัพท์ ***
        if ($user_type === 'public') {
            // ดึงข้อมูลเบอร์โทรของ user ปัจจุบัน
            $this->db->select('mp_phone');
            $this->db->from('tbl_member_public');
            $this->db->where('id', intval($user_id));
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                log_message('warning', "Public user not found: {$user_id}");
                return false;
            }

            // ตรวจสอบว่าเบอร์โทรตรงกันหรือไม่
            $can_cancel = ($queue_info['queue_phone'] === $user_data->mp_phone);

            log_message('info', 'Public user cancel permission: ' . ($can_cancel ? 'GRANTED' : 'DENIED') .
                ' - Queue phone: ' . $queue_info['queue_phone'] .
                ', User phone: ' . $user_data->mp_phone);

            return $can_cancel;
        }

        // *** Default: ไม่มีสิทธิ์ ***
        log_message('warning', 'Cancel permission denied: Unknown user type');
        return false;
    }






    public function update_id_card()
    {
        try {
            // ล้าง output buffer และตั้งค่า header
            while (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            ini_set('display_errors', 0);

            // ตรวจสอบ request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบการ login
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if (!$mp_id || !$mp_email) {
                echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // รับข้อมูลจาก POST
            $mp_number = $this->input->post('mp_number');

            // Validation
            if (empty($mp_number)) {
                echo json_encode(['success' => false, 'message' => 'กรุณากรอกเลขบัตรประจำตัวประชาชน'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (!preg_match('/^\d{13}$/', $mp_number)) {
                echo json_encode(['success' => false, 'message' => 'เลขบัตรประจำตัวประชาชนต้องเป็นตัวเลข 13 หลัก'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบว่าเลขบัตรนี้มีคนใช้แล้วหรือไม่
            $this->db->select('id, mp_email');
            $this->db->from('tbl_member_public');
            $this->db->where('mp_number', $mp_number);
            $this->db->where('mp_id !=', $mp_id); // ยกเว้นตัวเอง
            $existing_user = $this->db->get()->row();

            if ($existing_user) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เลขบัตรประจำตัวประชาชนนี้มีผู้ใช้งานแล้ว'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // อัพเดทข้อมูล
            $update_data = [
                'mp_number' => $mp_number,
                'mp_updated_by' => $mp_email,
                'mp_updated_date' => date('Y-m-d H:i:s')
            ];

            $this->db->where('mp_id', $mp_id);
            $result = $this->db->update('tbl_member_public', $update_data);

            if ($result) {
                // อัพเดท session (ถ้าจำเป็น)
                $this->session->set_userdata('mp_number', $mp_number);

                // Log การอัพเดท
                log_message('info', "ID Card updated for user {$mp_email}: {$mp_number}");

                echo json_encode([
                    'success' => true,
                    'message' => 'อัพเดทเลขบัตรประจำตัวประชาชนสำเร็จ',
                    'data' => [
                        'mp_number' => $mp_number
                    ]
                ], JSON_UNESCAPED_UNICODE);

            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัพเดทข้อมูลได้ กรุณาลองใหม่อีกครั้ง'
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', 'Update ID Card Error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }



    /**
     * ดาวน์โหลดไฟล์แนบ
     */
    public function download_file($file_name)
    {
        try {
            if (empty($file_name)) {
                show_404();
                return;
            }

            $file_path = './docs/queue_files/' . $file_name;

            if (!file_exists($file_path)) {
                show_404();
                return;
            }

            // ดึงชื่อไฟล์เดิมจากฐานข้อมูล
            $file_info = $this->queue_model->get_file_info($file_name);
            $original_name = $file_info ? $file_info->queue_file_original_name : $file_name;
            $file_type = $file_info ? $file_info->queue_file_type : 'application/octet-stream';

            // ตั้งค่า header สำหรับดาวน์โหลด
            header('Content-Type: ' . $file_type);
            header('Content-Disposition: attachment; filename="' . $original_name . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // อ่านและส่งไฟล์
            readfile($file_path);

        } catch (Exception $e) {
            log_message('error', 'Error in download_file: ' . $e->getMessage());
            show_404();
        }
    }

    /**
     * แสดงรูปภาพแนบ
     */
    public function view_image($file_name)
    {
        try {
            if (empty($file_name)) {
                show_404();
                return;
            }

            $file_path = './docs/queue_files/' . $file_name;

            if (!file_exists($file_path)) {
                show_404();
                return;
            }

            // ตรวจสอบว่าเป็นไฟล์รูปภาพ
            $file_info = pathinfo($file_path);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array(strtolower($file_info['extension']), $allowed_extensions)) {
                show_error('ไฟล์นี้ไม่ใช่รูปภาพ', 400);
                return;
            }

            // ดึงข้อมูลไฟล์จากฐานข้อมูล
            $file_data = $this->queue_model->get_file_info($file_name);
            $mime_type = $file_data ? $file_data->queue_file_type : 'image/jpeg';

            // ตั้งค่า header
            header('Content-Type: ' . $mime_type);
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: public, max-age=3600');

            // แสดงรูปภาพ
            readfile($file_path);

        } catch (Exception $e) {
            log_message('error', 'Error in view_image: ' . $e->getMessage());
            show_404();
        }
    }

    /**
     * ดึงข้อมูล login ของผู้ใช้ปัจจุบัน
     */
    public function get_current_user_info()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            $user_info = $this->get_current_user_detailed();

            // ลบข้อมูลที่ sensitive ออก
            $safe_user_info = $user_info;
            if (isset($safe_user_info['user_info']['mp_id'])) {
                unset($safe_user_info['user_info']['mp_id']);
            }
            if (isset($safe_user_info['user_info']['number'])) {
                unset($safe_user_info['user_info']['number']); // ไม่ส่งเลขบัตรประชาชน
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'user_info' => $safe_user_info
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', 'Error in get_current_user_info: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    // ===================================================================
    // *** HELPER FUNCTIONS ***
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

            if (isset($this->calender_model) && method_exists($this->calender_model, 'get_events')) {
                $result = $this->calender_model->get_events();
                $data['events'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->banner_model) && method_exists($this->banner_model, 'banner_frontend')) {
                $result = $this->banner_model->banner_frontend();
                $data['qBanner'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            if (isset($this->background_personnel_model) && method_exists($this->background_personnel_model, 'background_personnel_frontend')) {
                $result = $this->background_personnel_model->background_personnel_frontend();
                $data['qBackground_personnel'] = (is_array($result) || is_object($result)) ? $result : [];
            }

            // โหลดข้อมูลเพิ่มเติม
            $additional_models = [
                'news_model' => 'qNews',
                'announce_model' => 'qAnnounce',
                'order_model' => 'qOrder',
                'procurement_model' => 'qProcurement',
                'mui_model' => 'qMui',
                'guide_work_model' => 'qGuide_work',
                'loadform_model' => 'qLoadform',
                'pppw_model' => 'qPppw',
                'msg_pres_model' => 'qMsg_pres',
                'history_model' => 'qHistory',
                'otop_model' => 'qOtop',
                'gci_model' => 'qGci',
                'vision_model' => 'qVision',
                'authority_model' => 'qAuthority',
                'mission_model' => 'qMission',
                'motto_model' => 'qMotto',
                'cmi_model' => 'qCmi',
                'executivepolicy_model' => 'qExecutivepolicy',
                'travel_model' => 'qTravel',
                'si_model' => 'qSi'
            ];

            foreach ($additional_models as $model_name => $data_key) {
                if (isset($this->$model_name)) {
                    $result = null;
                    if (method_exists($this->$model_name, 'list_all')) {
                        $result = $this->$model_name->list_all();
                    } elseif (method_exists($this->$model_name, 'get_all')) {
                        $result = $this->$model_name->get_all();
                    }
                    if ($result !== null) {
                        $data[$data_key] = (is_array($result) || is_object($result)) ? $result : [];
                    }
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error loading navbar data: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * ดึงข้อมูล user ปัจจุบันแบบละเอียด
     */
    private function get_current_user_detailed()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];

        try {
            // ตรวจสอบ staff user
            $m_id = $this->session->userdata('m_id');
            if (!empty($m_id)) {
                $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system, m_img, m_status');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $m_id);
                $this->db->where('m_status', '1');
                $staff_data = $this->db->get()->row();

                if ($staff_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'staff';
                    $user_info['user_info'] = [
                        'id' => $staff_data->m_id,
                        'm_id' => $staff_data->m_id,
                        'name' => trim($staff_data->m_fname . ' ' . $staff_data->m_lname),
                        'fname' => $staff_data->m_fname,
                        'lname' => $staff_data->m_lname,
                        'phone' => $staff_data->m_phone,
                        'email' => $staff_data->m_email,
                        'm_system' => $staff_data->m_system,
                        'm_img' => $staff_data->m_img,
                        'number' => ''
                    ];

                    log_message('info', 'Staff user login verified: ' . $staff_data->m_email);
                    return $user_info;
                }
            }

            // ตรวจสอบ public user
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');
            if (!empty($mp_id) && !empty($mp_email)) {
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_number, mp_address, mp_district, mp_amphoe, mp_province, mp_zipcode, mp_status');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $this->db->where('mp_email', $mp_email);
                $this->db->where('mp_status', 1);
                $public_data = $this->db->get()->row();

                if ($public_data) {
                    $user_info['is_logged_in'] = true;
                    $user_info['user_type'] = 'public';
                    $user_info['user_info'] = [
                        'id' => $public_data->id,
                        'mp_id' => $public_data->mp_id,
                        'name' => trim(($public_data->mp_prefix ? $public_data->mp_prefix . ' ' : '') . $public_data->mp_fname . ' ' . $public_data->mp_lname),
                        'prefix' => $public_data->mp_prefix,
                        'fname' => $public_data->mp_fname,
                        'lname' => $public_data->mp_lname,
                        'phone' => $public_data->mp_phone,
                        'email' => $public_data->mp_email,
                        'number' => $public_data->mp_number
                    ];

                    // ข้อมูลที่อยู่
                    if (!empty($public_data->mp_address) || !empty($public_data->mp_district)) {
                        $user_info['user_address'] = [
                            'additional_address' => $public_data->mp_address ?: '',
                            'district' => $public_data->mp_district ?: '',
                            'amphoe' => $public_data->mp_amphoe ?: '',
                            'province' => $public_data->mp_province ?: '',
                            'zipcode' => $public_data->mp_zipcode ?: '',
                            'full_address' => trim($public_data->mp_address . ' ' . $public_data->mp_district . ' ' . $public_data->mp_amphoe . ' ' . $public_data->mp_province . ' ' . $public_data->mp_zipcode),
                            'parsed' => [
                                'additional_address' => $public_data->mp_address ?: '',
                                'district' => $public_data->mp_district ?: '',
                                'amphoe' => $public_data->mp_amphoe ?: '',
                                'province' => $public_data->mp_province ?: '',
                                'zipcode' => $public_data->mp_zipcode ?: '',
                                'full_address' => trim($public_data->mp_address . ' ' . $public_data->mp_district . ' ' . $public_data->mp_amphoe . ' ' . $public_data->mp_province . ' ' . $public_data->mp_zipcode)
                            ]
                        ];
                    }

                    log_message('info', 'Public user login verified: ' . $public_data->mp_email);
                    return $user_info;
                }
            }

            log_message('info', 'No valid login session found - user is guest');

        } catch (Exception $e) {
            log_message('error', 'Error in get_current_user_detailed: ' . $e->getMessage());
        }

        return [
            'is_logged_in' => false,
            'user_type' => 'guest',
            'user_info' => null,
            'user_address' => null
        ];
    }





    /**
     * ดึงข้อมูล login ของผู้ใช้แบบละเอียด (alias method)
     */

    private function get_user_login_info_with_detailed_address()
    {
        return $this->get_current_user_detailed();
    }






    /**
     * หน้าสถานะการจองคิวของฉัน
     * แสดงรายการคิวของผู้ใช้ที่ login พร้อมสถิติและการจัดการ
     */
    public function my_queue_status()
    {
        try {
            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data();

            // *** ตรวจสอบการ login ***
            if (method_exists($this, 'get_user_login_info_with_detailed_address')) {
                $login_info = $this->get_user_login_info_with_detailed_address();
                $data['is_logged_in'] = $login_info['is_logged_in'];
                $data['user_info'] = $login_info['user_info'];
                $data['user_type'] = $login_info['user_type'];
                $data['user_address'] = $login_info['user_address'];
            } else {
                $current_user = $this->get_current_user_detailed();
                $data['is_logged_in'] = $current_user['is_logged_in'];
                $data['user_info'] = $current_user['user_info'];
                $data['user_type'] = $current_user['user_type'];
                $data['user_address'] = $current_user['user_address'];
            }

            // *** ถ้าไม่ได้ login ให้ redirect ไป login ***
            if (!$data['is_logged_in']) {
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อนเพื่อดูสถานะการจองคิว');
                redirect('User');
                return;
            }

            // *** 🎯 ดึงรายการคิวของผู้ใช้ด้วย user_id ***
            $user_queues = [];
            if ($data['user_info'] && isset($data['user_info']['id']) && !empty($data['user_info']['id'])) {
                try {
                    // ✅ ใช้ queue_user_id เป็นหลัก
                    $queues_result = $this->queue_model->get_queues_by_user_id(
                        $data['user_info']['id'],
                        $data['user_type']
                    );

                    log_message('info', 'Found ' . count($queues_result) . ' queues for user_id: ' . $data['user_info']['id'] . ' (type: ' . $data['user_type'] . ')');

                    if ($queues_result) {
                        // แปลงข้อมูลให้เป็น array และเพิ่มข้อมูลสำหรับการแสดงผล
                        $user_queues = array_map(function ($queue) {
                            if (is_object($queue)) {
                                $queue_array = [
                                    'queue_id' => $queue->queue_id ?? '',
                                    'queue_topic' => $queue->queue_topic ?? '',
                                    'queue_detail' => $queue->queue_detail ?? '',
                                    'queue_date' => $queue->queue_date ?? '',
                                    'queue_time_slot' => $queue->queue_time_slot ?? '',
                                    'queue_status' => $queue->queue_status ?? '',
                                    'queue_by' => $queue->queue_by ?? '',
                                    'queue_phone' => $queue->queue_phone ?? '',
                                    'queue_datesave' => $queue->queue_datesave ?? '',
                                    'queue_dateupdate' => $queue->queue_dateupdate ?? null,
                                    'queue_user_id' => $queue->queue_user_id ?? '',
                                    'queue_user_type' => $queue->queue_user_type ?? ''
                                ];
                            } else {
                                $queue_array = $queue;
                            }

                            // เพิ่มข้อมูลสำหรับการแสดงผล
                            $queue_array['latest_status_display'] = $this->get_queue_status_display($queue_array['queue_status']);
                            $queue_array['status_class'] = $this->get_queue_status_class($queue_array['queue_status']);
                            $queue_array['status_icon'] = $this->get_queue_status_icon($queue_array['queue_status']);
                            $queue_array['status_color'] = $this->get_queue_status_color($queue_array['queue_status']);

                            // อัพเดทล่าสุด
                            $queue_array['latest_update'] = $queue_array['queue_dateupdate'] ?: $queue_array['queue_datesave'];

                            return $queue_array;
                        }, $queues_result);
                    }

                } catch (Exception $e) {
                    log_message('error', 'Error loading user queues by user_id in my_queue_status: ' . $e->getMessage());
                    $user_queues = [];
                }
            } else {
                log_message('warning', 'No user_id found for logged in user in my_queue_status');
            }

            $data['queues'] = $user_queues;

            // *** คำนวณสถิติคิว ***
            $data['status_counts'] = [
                'total' => count($user_queues),
                'pending' => 0,
                'confirmed' => 0,
                'completed' => 0,
                'cancelled' => 0
            ];

            foreach ($user_queues as $queue) {
                switch ($queue['queue_status']) {
                    case 'รอยืนยันการจอง':
                        $data['status_counts']['pending']++;
                        break;
                    case 'ยืนยันการจอง':
                    case 'คิวได้รับการยืนยัน':
                    case 'รับเรื่องพิจารณา':
                    case 'รับเรื่องแล้ว':
                    case 'กำลังดำเนินการ':
                    case 'รอดำเนินการ':
                        $data['status_counts']['confirmed']++;
                        break;
                    case 'เสร็จสิ้น':
                        $data['status_counts']['completed']++;
                        break;
                    case 'ยกเลิก':
                    case 'คิวได้ถูกยกเลิก':
                        $data['status_counts']['cancelled']++;
                        break;
                }
            }

            // *** ข้อมูลเพิ่มเติมสำหรับหน้า ***
            $data['page_title'] = 'สถานะการจองคิวของฉัน';
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'บริการประชาชน', 'url' => '#'],
                ['title' => 'สถานะการจองคิวของฉัน', 'url' => '']
            ];

            // *** Flash Messages ***
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // *** Debug information ***
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'My queue status page data: ' . json_encode([
                    'user_type' => $data['user_type'],
                    'user_id' => $data['user_info']['id'] ?? 'not_set',
                    'queues_count' => count($user_queues),
                    'status_counts' => $data['status_counts']
                ]));
            }

            // โหลด view
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_queue_status', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in my_queue_status: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้าสถานะคิว: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Queue/adding_queue');
            }
        }
    }






    public function my_queue_detail($queue_id = null)
    {
        try {
            // ตรวจสอบ queue_id
            if (empty($queue_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขคิวที่ระบุ');
                redirect('Queue/my_queue_status');
                return;
            }

            // กำหนดค่าเริ่มต้นให้ทุกตัวแปรสำหรับ navbar
            $data = $this->prepare_navbar_data();

            // ตรวจสอบการ login
            if (method_exists($this, 'get_user_login_info_with_detailed_address')) {
                $login_info = $this->get_user_login_info_with_detailed_address();
                $data['is_logged_in'] = $login_info['is_logged_in'];
                $data['user_info'] = $login_info['user_info'];
                $data['user_type'] = $login_info['user_type'];
                $data['user_address'] = $login_info['user_address'];
            } else {
                $current_user = $this->get_current_user_detailed();
                $data['is_logged_in'] = $current_user['is_logged_in'];
                $data['user_info'] = $current_user['user_info'];
                $data['user_type'] = $current_user['user_type'];
                $data['user_address'] = $current_user['user_address'];
            }

            // *** แก้ไข: ใช้ query database โดยตรงแทน queue_model ***
            $this->db->select('*');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $queue_data = $this->db->get()->row();

            if (!$queue_data) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลคิวที่ระบุ');
                redirect('Queue/my_queue_status');
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึง
            $has_permission = $this->check_queue_access_permission_for_detail($queue_data, $data['user_type'], $data['user_info']);

            if (!$has_permission) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลคิวนี้');
                redirect('Queue/my_queue_status');
                return;
            }

            // *** แก้ไข: ดึงประวัติการอัพเดทสถานะ ***
            $this->db->select('*');
            $this->db->from('tbl_queue_detail');
            $this->db->where('queue_detail_case_id', $queue_id);
            $this->db->order_by('queue_detail_id', 'ASC');
            $queue_history = $this->db->get()->result();

            // *** แก้ไข: ดึงไฟล์แนบ ***
            $this->db->select('*');
            $this->db->from('tbl_queue_files');
            $this->db->where('queue_file_ref_id', $queue_id);
            $queue_files_raw = $this->db->get()->result();

            // เตรียมข้อมูลไฟล์
            $queue_files = [];
            if ($queue_files_raw) {
                foreach ($queue_files_raw as $file) {
                    $file_data = (array) $file;

                    // เพิ่มข้อมูลเสริม
                    $file_data['is_image'] = $this->is_image_file($file_data['queue_file_type']);
                    $file_data['file_icon'] = $this->get_file_icon($file_data['queue_file_type']);
                    $file_data['file_size_formatted'] = $this->format_file_size($file_data['queue_file_size']);

                    $queue_files[] = (object) $file_data;
                }
            }

            // เตรียมข้อมูลสำหรับ view
            $data['queue_data'] = $this->prepare_queue_data_for_display($queue_data);
            $data['queue_details'] = $this->prepare_queue_history_for_display($queue_history);
            $data['queue_files'] = $queue_files;

            // ข้อมูลเพิ่มเติมสำหรับหน้า
            $data['page_title'] = 'รายละเอียดการจองคิว #' . $queue_id;
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => base_url()],
                ['title' => 'สถานะการจองคิว', 'url' => site_url('Queue/my_queue_status')],
                ['title' => 'รายละเอียด', 'url' => '']
            ];

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Queue detail page data: ' . json_encode([
                    'queue_id' => $queue_id,
                    'user_type' => $data['user_type'],
                    'has_history' => !empty($queue_history),
                    'has_files' => !empty($queue_files)
                ]));
            }

            // *** แก้ไข: ใช้ path เดียวกับ my_queue_status ***
            $this->load->view('public_user/templates/header', $data);
            $this->load->view('public_user/my_queue_detail', $data);
            $this->load->view('public_user/templates/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Critical error in queue_detail: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดรายละเอียดคิว: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Queue/my_queue_status');
            }
        }
    }



    /**
     * Helper methods สำหรับสถานะคิว
     */
    private function get_queue_status_display($status)
    {
        $status_map = [
            'รอยืนยันการจอง' => 'รอยืนยันการจอง',
            'ยืนยันการจอง' => 'ยืนยันการจองแล้ว',
            'คิวได้รับการยืนยัน' => 'ยืนยันการจองแล้ว',
            'รับเรื่องแล้ว' => 'รับเรื่องแล้ว',
            'กำลังดำเนินการ' => 'กำลังดำเนินการ',
            'รอดำเนินการ' => 'รอดำเนินการ',
            'เสร็จสิ้น' => 'เสร็จสิ้น',
            'ยกเลิก' => 'ยกเลิก',
            'คิวได้ถูกยกเลิก' => 'ยกเลิก'
        ];

        return $status_map[$status] ?? $status;
    }

    private function get_queue_status_class($status)
    {
        $class_map = [
            'รอยืนยันการจอง' => 'queue-status-pending',
            'ยืนยันการจอง' => 'queue-status-confirmed',
            'คิวได้รับการยืนยัน' => 'queue-status-confirmed',
            'รับเรื่องแล้ว' => 'queue-status-processing',
            'กำลังดำเนินการ' => 'queue-status-processing',
            'รอดำเนินการ' => 'queue-status-processing',
            'เสร็จสิ้น' => 'queue-status-completed',
            'ยกเลิก' => 'queue-status-cancelled',
            'คิวได้ถูกยกเลิก' => 'queue-status-cancelled'
        ];

        return $class_map[$status] ?? 'queue-status-unknown';
    }

    private function get_queue_status_icon($status)
    {
        $icon_map = [
            'รอยืนยันการจอง' => 'fas fa-hourglass-half',
            'ยืนยันการจอง' => 'fas fa-check-circle',
            'คิวได้รับการยืนยัน' => 'fas fa-check-circle',
            'รับเรื่องแล้ว' => 'fas fa-inbox',
            'กำลังดำเนินการ' => 'fas fa-cogs',
            'รอดำเนินการ' => 'fas fa-clock',
            'เสร็จสิ้น' => 'fas fa-check-double',
            'ยกเลิก' => 'fas fa-times-circle',
            'คิวได้ถูกยกเลิก' => 'fas fa-times-circle'
        ];

        return $icon_map[$status] ?? 'fas fa-question-circle';
    }

    private function get_queue_status_color($status)
    {
        $color_map = [
            'รอยืนยันการจอง' => '#FFC700',
            'ยืนยันการจอง' => '#17a2b8',
            'คิวได้รับการยืนยัน' => '#17a2b8',
            'รับเรื่องแล้ว' => '#9c6bdb',
            'กำลังดำเนินการ' => '#9c6bdb',
            'รอดำเนินการ' => '#9c6bdb',
            'เสร็จสิ้น' => '#00B73E',
            'ยกเลิก' => '#FF0202',
            'คิวได้ถูกยกเลิก' => '#FF0202'
        ];

        return $color_map[$status] ?? '#6c757d';
    }




    /**
     * หน้ารายละเอียดการจองคิว - Full Code แก้ไขแล้ว
     * แสดงข้อมูลคิวพร้อมประวัติการอัพเดทสถานะและข้อมูลที่อยู่ที่ครบถ้วน
     */
    public function queue_detail($queue_id = null)
    {
        try {
            // *** ตรวจสอบ queue_id ***
            if (empty($queue_id)) {
                $this->session->set_flashdata('error_message', 'ไม่พบหมายเลขคิวที่ระบุ');
                redirect('Queue/queue_report');
                return;
            }

            // *** ตรวจสอบสิทธิ์ - เฉพาะ Staff เท่านั้น (เหมือน queue_report) ***
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // *** ตรวจสอบว่าเป็น staff จริงๆ ***
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                redirect('User');
                return;
            }

            // โหลด models (เหมือน queue_report)
            $this->load->model('Queue_model', 'queue_model');

            // *** ใช้ prepare_reports_base_data เหมือน queue_report ***
            $data = $this->prepare_reports_base_data('รายละเอียดการจองคิว #' . $queue_id);

            // *** แก้ไข: ดึงข้อมูลคิวพร้อมข้อมูลที่อยู่ที่ครบถ้วน ***
            $this->db->select('
            q.*, 
            mp.mp_address, 
            mp.mp_district as mp_district, 
            mp.mp_amphoe as mp_amphoe, 
            mp.mp_province as mp_province, 
            mp.mp_zipcode as mp_zipcode,
            mp.mp_prefix,
            mp.mp_fname,
            mp.mp_lname,
            m.m_fname as staff_fname,
            m.m_lname as staff_lname
        ');
            $this->db->from('tbl_queue q');
            $this->db->join('tbl_member_public mp', 'q.queue_user_id = mp.id AND q.queue_user_type = "public"', 'left');
            $this->db->join('tbl_member m', 'q.queue_user_id = m.m_id AND q.queue_user_type = "staff"', 'left');
            $this->db->where('q.queue_id', $queue_id);
            $queue_data = $this->db->get()->row();

            if (!$queue_data) {
                $this->session->set_flashdata('error_message', 'ไม่พบข้อมูลคิวที่ระบุ');
                redirect('Queue/queue_report');
                return;
            }

            // *** Staff สามารถดูได้ทุกคิว (ไม่ต้องตรวจสอบ permission แยก) ***
            log_message('info', 'Staff user accessing queue detail: ' . $staff_check->m_fname . ' ' . $staff_check->m_lname . ' viewing queue: ' . $queue_id);

            // *** แก้ไข: Debug ข้อมูลที่อยู่ ***
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Queue Address Data: ' . json_encode([
                    'queue_user_type' => $queue_data->queue_user_type,
                    'queue_address' => $queue_data->queue_address ?? 'NULL',
                    'guest_district' => $queue_data->guest_district ?? 'NULL',
                    'guest_amphoe' => $queue_data->guest_amphoe ?? 'NULL',
                    'guest_province' => $queue_data->guest_province ?? 'NULL',
                    'guest_zipcode' => $queue_data->guest_zipcode ?? 'NULL',
                    'mp_address' => $queue_data->mp_address ?? 'NULL',
                    'mp_district' => $queue_data->mp_district ?? 'NULL',
                    'mp_amphoe' => $queue_data->mp_amphoe ?? 'NULL',
                    'mp_province' => $queue_data->mp_province ?? 'NULL',
                    'mp_zipcode' => $queue_data->mp_zipcode ?? 'NULL'
                ]));
            }

            // *** ดึงประวัติการอัพเดทสถานะ ***
            $this->db->select('*');
            $this->db->from('tbl_queue_detail');
            $this->db->where('queue_detail_case_id', $queue_id);
            $this->db->order_by('queue_detail_id', 'ASC');
            $queue_history = $this->db->get()->result();

            // *** ดึงไฟล์แนบ ***
            $this->db->select('*');
            $this->db->from('tbl_queue_files');
            $this->db->where('queue_file_ref_id', $queue_id);
            $queue_files_raw = $this->db->get()->result();

            // เตรียมข้อมูลไฟล์
            $queue_files = [];
            if ($queue_files_raw) {
                foreach ($queue_files_raw as $file) {
                    $file_data = (array) $file;

                    // เพิ่มข้อมูลเสริม
                    $file_data['is_image'] = $this->is_image_file($file_data['queue_file_type']);
                    $file_data['file_icon'] = $this->get_file_icon($file_data['queue_file_type']);
                    $file_data['file_size_formatted'] = $this->format_file_size($file_data['queue_file_size']);

                    $queue_files[] = (object) $file_data;
                }
            }

            // *** ดึงข้อมูลผู้ใช้ที่จองคิว (สำหรับ staff view) ***
            $user_details = $this->get_queue_user_details($queue_data);

            // *** แก้ไข: เตรียมข้อมูลสำหรับ view พร้อมข้อมูลที่อยู่ ***
            $data['queue_data'] = $this->prepare_queue_data_for_display_with_address($queue_data);
            $data['queue_details'] = $this->prepare_queue_history_for_display($queue_history);
            $data['queue_files'] = $queue_files;
            $data['user_details'] = $user_details;

            // *** ใช้ breadcrumb pattern เหมือน queue_report ***
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('/')],
                ['title' => 'ระบบรายงาน', 'url' => site_url('System_reports')],
                ['title' => 'รายงานการจองคิว', 'url' => site_url('Queue/queue_report')],
                ['title' => 'รายละเอียดคิว #' . $queue_id, 'url' => '']
            ];

            // *** เพิ่มข้อมูลสำหรับ Staff Actions ***
            $data['can_update_status'] = true; // Staff สามารถอัพเดทสถานะได้
            $data['staff_info'] = [
                'name' => $staff_check->m_fname . ' ' . $staff_check->m_lname,
                'system' => $staff_check->m_system
            ];

            // *** สถานะที่สามารถเปลี่ยนได้ (สำหรับ staff) ***
            $current_status = $queue_data->queue_status;
            $data['available_statuses'] = $this->get_available_status_transitions($current_status);

            // Flash Messages (เหมือน queue_report)
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['info_message'] = $this->session->flashdata('info_message');

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Queue detail page data (Staff): ' . json_encode([
                    'queue_id' => $queue_id,
                    'staff_user' => $staff_check->m_fname . ' ' . $staff_check->m_lname,
                    'user_type' => $data['user_type'],
                    'user_info_type' => gettype($data['user_info']),
                    'has_m_fname' => is_object($data['user_info']) && property_exists($data['user_info'], 'm_fname'),
                    'has_history' => !empty($queue_history),
                    'has_files' => !empty($queue_files),
                    'can_update_status' => $data['can_update_status'],
                    'address_data' => [
                        'user_type' => $queue_data->queue_user_type,
                        'has_queue_address' => !empty($queue_data->queue_address),
                        'has_guest_fields' => !empty($queue_data->guest_district),
                        'has_mp_fields' => !empty($queue_data->mp_address)
                    ]
                ]));
            }

            // *** โหลด view แบบเดียวกับ queue_report ***
            $this->load->view('reports/header', $data);
            $this->load->view('reports/queue_detail', $data);
            $this->load->view('reports/footer', $data); // ส่ง $data ไปเหมือน queue_report

        } catch (Exception $e) {
            log_message('error', 'Critical error in queue_detail (Staff): ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดรายละเอียดคิว: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Queue/queue_report');
            }
        }
    }

    /**
     * *** เพิ่ม Helper Method ใหม่สำหรับเตรียมข้อมูลคิวพร้อมที่อยู่ ***
     */
    private function prepare_queue_data_for_display_with_address($queue_data)
    {
        if (!$queue_data) {
            return null;
        }

        // แปลง object เป็น array
        $data = (array) $queue_data;

        // เพิ่มข้อมูลสำหรับการแสดงผล
        $data['status_display'] = $this->get_queue_status_display($data['queue_status']);
        $data['status_class'] = $this->get_queue_status_class($data['queue_status']);
        $data['status_icon'] = $this->get_queue_status_icon($data['queue_status']);
        $data['status_color'] = $this->get_queue_status_color($data['queue_status']);

        // *** แก้ไข: จัดการข้อมูลที่อยู่อย่างสมบูรณ์ ***
        $data['formatted_address'] = $this->format_address_complete($data);

        // Format วันที่
        if (!empty($data['queue_date'])) {
            $date = new DateTime($data['queue_date']);
            $data['formatted_date'] = $date->format('d/m/Y H:i');
            $data['date_thai'] = $this->format_thai_date($data['queue_date']);
        }

        if (!empty($data['queue_datesave'])) {
            $create_date = new DateTime($data['queue_datesave']);
            $data['created_date'] = $create_date->format('d/m/Y H:i');
            $data['created_thai'] = $this->format_thai_date($data['queue_datesave']);
        }

        return $data;
    }

    /**
     * *** เพิ่ม Helper Method ใหม่สำหรับจัดรูปแบบที่อยู่ที่สมบูรณ์ ***
     */
    private function format_address_complete($queue_data)
    {
        $address_parts = [];

        // ตรวจสอบรูปแบบข้อมูล
        if (is_array($queue_data)) {
            $data = $queue_data;
        } elseif (is_object($queue_data)) {
            $data = (array) $queue_data;
        } else {
            return 'ไม่ระบุ';
        }

        // *** สำหรับ Guest User ***
        if (isset($data['queue_user_type']) && $data['queue_user_type'] === 'guest') {
            // ที่อยู่เพิ่มเติม
            if (!empty($data['queue_address'])) {
                $address_parts[] = $data['queue_address'];
            }

            // ตำบล
            if (!empty($data['guest_district'])) {
                $address_parts[] = 'ตำบล' . $data['guest_district'];
            }

            // อำเภอ
            if (!empty($data['guest_amphoe'])) {
                $address_parts[] = 'อำเภอ' . $data['guest_amphoe'];
            }

            // จังหวัด
            if (!empty($data['guest_province'])) {
                $address_parts[] = 'จังหวัด' . $data['guest_province'];
            }

            // รหัสไปรษณีย์
            if (!empty($data['guest_zipcode'])) {
                $address_parts[] = $data['guest_zipcode'];
            }
        }

        // *** สำหรับ Public User ***
        elseif (isset($data['queue_user_type']) && $data['queue_user_type'] === 'public') {
            // ที่อยู่เพิ่มเติม
            if (!empty($data['mp_address'])) {
                $address_parts[] = $data['mp_address'];
            }

            // ตำบล
            if (!empty($data['mp_district'])) {
                $address_parts[] = 'ตำบล' . $data['mp_district'];
            }

            // อำเภอ
            if (!empty($data['mp_amphoe'])) {
                $address_parts[] = 'อำเภอ' . $data['mp_amphoe'];
            }

            // จังหวัด
            if (!empty($data['mp_province'])) {
                $address_parts[] = 'จังหวัด' . $data['mp_province'];
            }

            // รหัสไปรษณีย์
            if (!empty($data['mp_zipcode'])) {
                $address_parts[] = $data['mp_zipcode'];
            }
        }

        // *** สำหรับ Staff User ***
        elseif (isset($data['queue_user_type']) && $data['queue_user_type'] === 'staff') {
            return 'เจ้าหน้าที่ (ไม่ระบุที่อยู่)';
        }

        // *** Fallback: ลองหาจาก fields อื่น ***
        if (empty($address_parts)) {
            // ลองหาจากที่อยู่ในรูปแบบอื่น
            $fallback_fields = [
                'queue_address',
                'address',
                'full_address'
            ];

            foreach ($fallback_fields as $field) {
                if (!empty($data[$field])) {
                    $address_parts[] = $data[$field];
                    break;
                }
            }
        }

        // *** Debug Log ***
        if (ENVIRONMENT === 'development' && empty($address_parts)) {
            log_message('debug', 'Address formatting failed. Available fields: ' . implode(', ', array_keys($data)));
            log_message('debug', 'User type: ' . ($data['queue_user_type'] ?? 'unknown'));
        }

        return !empty($address_parts) ? implode(' ', $address_parts) : 'ไม่ระบุ';
    }



    private function get_available_status_transitions($current_status)
    {
        $workflow_rules = [
            'รอยืนยันการจอง' => [
                'รับเรื่องพิจารณา',
                'คิวได้รับการยืนยัน',
                'ยกเลิก'
            ],
            'รับเรื่องพิจารณา' => [
                'คิวได้รับการยืนยัน',
                'รับเรื่องแล้ว',
                'กำลังดำเนินการ',
                'ยกเลิก'
            ],
            'คิวได้รับการยืนยัน' => [
                'รับเรื่องแล้ว',
                'กำลังดำเนินการ',
                'เสร็จสิ้น',
                'ยกเลิก'
            ],
            'รับเรื่องแล้ว' => [
                'กำลังดำเนินการ',
                'รอดำเนินการ',
                'เสร็จสิ้น',
                'ยกเลิก'
            ],
            'กำลังดำเนินการ' => [
                'รอดำเนินการ',
                'เสร็จสิ้น',
                'ยกเลิก'
            ],
            'รอดำเนินการ' => [
                'กำลังดำเนินการ',
                'เสร็จสิ้น',
                'ยกเลิก'
            ],
            'เสร็จสิ้น' => [],
            'ยกเลิก' => []
        ];

        return $workflow_rules[$current_status] ?? [];
    }


    private function prepare_safe_reports_data($page_title = 'รายงาน')
    {
        try {
            // ดึงข้อมูล user แบบปลอดภัย
            $user_data = $this->get_safe_user_info_for_reports();

            $base_data = [
                'page_title' => $page_title,
                'user_info' => $user_data['user_info'], // รับประกันว่าเป็น object
                'is_logged_in' => $user_data['is_logged_in'],
                'user_type' => $user_data['user_type'],
                'tenant_name' => $user_data['tenant_name'] ?: 'ระบบรายงาน',
                'system_name' => 'ระบบรายงาน'
            ];

            // ตรวจสอบขั้นสุดท้าย
            if (!is_object($base_data['user_info'])) {
                log_message('warning', 'user_info is still not object, forcing conversion');
                $base_data['user_info'] = $this->create_default_user_object();
            }

            return $base_data;

        } catch (Exception $e) {
            log_message('error', 'Error in prepare_safe_reports_data: ' . $e->getMessage());

            // ส่งกลับข้อมูล default ที่ปลอดภัย
            return [
                'page_title' => $page_title,
                'user_info' => $this->create_default_user_object(),
                'is_logged_in' => false,
                'user_type' => 'guest',
                'tenant_name' => 'ระบบรายงาน',
                'system_name' => 'ระบบรายงาน'
            ];
        }
    }

    /**
     * *** เพิ่ม method ใหม่สำหรับดึงข้อมูล user แบบปลอดภัย ***
     */
    private function get_safe_user_info_for_reports()
    {
        $result = [
            'user_info' => null,
            'is_logged_in' => false,
            'user_type' => 'guest',
            'tenant_name' => $this->session->userdata('tenant_name') ?: 'ระบบรายงาน'
        ];

        try {
            // ตรวจสอบ staff user
            $m_id = $this->session->userdata('m_id');
            if (!empty($m_id)) {
                $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_img, m_system, m_status');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $m_id);
                $staff_data = $this->db->get()->row();

                if ($staff_data && ($staff_data->m_status == '1' || $staff_data->m_status == 1)) {
                    // สร้าง object ที่มี property ครบ
                    $result['user_info'] = (object) [
                        'm_id' => $staff_data->m_id,
                        'm_email' => $staff_data->m_email ?: '',
                        'm_fname' => $staff_data->m_fname ?: 'Unknown',
                        'm_lname' => $staff_data->m_lname ?: 'User',
                        'm_phone' => $staff_data->m_phone ?: '',
                        'm_img' => $staff_data->m_img ?: '',
                        'm_system' => $staff_data->m_system ?: '',
                        'm_status' => $staff_data->m_status,
                        'pname' => 'เจ้าหน้าที่',
                        'peng' => 'Staff'
                    ];
                    $result['is_logged_in'] = true;
                    $result['user_type'] = 'staff';

                    return $result;
                }
            }

            // ตรวจสอบ public user
            $mp_id = $this->session->userdata('mp_id');
            if (!empty($mp_id)) {
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_status');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $public_data = $this->db->get()->row();

                if ($public_data && ($public_data->mp_status == 1 || $public_data->mp_status == '1')) {
                    $result['user_info'] = (object) [
                        'm_id' => $public_data->id,
                        'm_email' => $public_data->mp_email ?: '',
                        'm_fname' => $public_data->mp_fname ?: 'Unknown',
                        'm_lname' => $public_data->mp_lname ?: 'User',
                        'm_phone' => $public_data->mp_phone ?: '',
                        'm_img' => '',
                        'm_system' => 'public',
                        'm_status' => $public_data->mp_status,
                        'pname' => 'สมาชิก',
                        'peng' => 'Public Member'
                    ];
                    $result['is_logged_in'] = true;
                    $result['user_type'] = 'public';

                    return $result;
                }
            }

        } catch (Exception $e) {
            log_message('error', 'Error in get_safe_user_info_for_reports: ' . $e->getMessage());
        }

        // กรณี guest หรือเกิดข้อผิดพลาด
        $result['user_info'] = $this->create_default_user_object();
        return $result;
    }

    /**
     * *** เพิ่ม method ใหม่สำหรับสร้าง default user object ***
     */
    private function create_default_user_object()
    {
        return (object) [
            'm_id' => 0,
            'm_email' => '',
            'm_fname' => 'Guest',
            'm_lname' => 'User',
            'm_phone' => '',
            'm_img' => '',
            'm_system' => '',
            'm_status' => '',
            'pname' => 'ผู้เยี่ยมชม',
            'peng' => 'Visitor'
        ];
    }


    /**
     * ตรวจสอบสิทธิ์การเข้าถึงรายละเอียดคิว
     */
    private function check_queue_access_permission_for_detail($queue_data, $user_type, $user_info)
    {
        // Staff สามารถดูได้ทุกคิว
        if ($user_type === 'staff') {
            return true;
        }

        // Guest ไม่สามารถเข้าถึงหน้านี้ได้
        if ($user_type === 'guest' || empty($user_info)) {
            return false;
        }

        // Public user สามารถดูได้เฉพาะคิวของตนเอง
        if ($user_type === 'public') {
            // ตรวจสอบด้วยเบอร์โทรศัพท์
            return ($queue_data->queue_phone === $user_info['phone']);
        }

        return false;
    }

    /**
     * เตรียมข้อมูลคิวสำหรับการแสดงผล
     */
    private function prepare_queue_data_for_display($queue_data)
    {
        if (!$queue_data) {
            return null;
        }

        // แปลง object เป็น array
        $data = (array) $queue_data;

        // เพิ่มข้อมูลสำหรับการแสดงผล
        $data['status_display'] = $this->get_queue_status_display($data['queue_status']);
        $data['status_class'] = $this->get_queue_status_class($data['queue_status']);
        $data['status_icon'] = $this->get_queue_status_icon($data['queue_status']);
        $data['status_color'] = $this->get_queue_status_color($data['queue_status']);

        // Format วันที่
        if (!empty($data['queue_date'])) {
            $date = new DateTime($data['queue_date']);
            $data['formatted_date'] = $date->format('d/m/Y H:i');
            $data['date_thai'] = $this->format_thai_date($data['queue_date']);
        }

        if (!empty($data['queue_datesave'])) {
            $create_date = new DateTime($data['queue_datesave']);
            $data['created_date'] = $create_date->format('d/m/Y H:i');
            $data['created_thai'] = $this->format_thai_date($data['queue_datesave']);
        }

        return $data;
    }

    /**
     * เตรียมประวัติคิวสำหรับการแสดงผล
     */
    private function prepare_queue_history_for_display($queue_history)
    {
        if (empty($queue_history)) {
            return [];
        }

        $prepared_history = [];
        foreach ($queue_history as $history) {
            $item = (array) $history;

            // เพิ่มข้อมูลสำหรับการแสดงผล
            $item['status_display'] = $this->get_queue_status_display($item['queue_detail_status']);
            $item['status_class'] = $this->get_queue_status_class($item['queue_detail_status']);
            $item['status_icon'] = $this->get_queue_status_icon($item['queue_detail_status']);
            $item['status_color'] = $this->get_queue_status_color($item['queue_detail_status']);

            // Format วันที่
            if (!empty($item['queue_detail_datesave'])) {
                $date = new DateTime($item['queue_detail_datesave']);
                $item['formatted_date'] = $date->format('d/m/Y H:i');
                $item['date_thai'] = $this->format_thai_date($item['queue_detail_datesave']);
            }

            $prepared_history[] = $item;
        }

        return $prepared_history;
    }

    /**
     * Format วันที่เป็นภาษาไทย
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
     * แสดงรูปภาพแนับคิว - แก้ไขแล้ว
     */
    public function view_queue_image($file_name)
    {
        try {
            if (empty($file_name)) {
                log_message('error', 'view_queue_image: Empty file name');
                show_404();
                return;
            }

            log_message('info', 'Attempting to view image: ' . $file_name);

            // ตรวจสอบไฟล์ที่ docs/queue_files/ (ตาม path ที่ผู้ใช้บอก)
            $file_path = './docs/queue_files/' . $file_name;

            if (!file_exists($file_path)) {
                log_message('error', 'Queue image file not found at: ' . $file_path);

                // ลองหาในโฟลเดอร์อื่น
                $alternative_paths = [
                    './docs/img/queue/' . $file_name,
                    './docs/files/queue/' . $file_name,
                    './uploads/queue/' . $file_name,
                    './docs/img/' . $file_name
                ];

                $found = false;
                foreach ($alternative_paths as $alt_path) {
                    if (file_exists($alt_path)) {
                        $file_path = $alt_path;
                        $found = true;
                        log_message('info', 'Found file at alternative path: ' . $alt_path);
                        break;
                    }
                }

                if (!$found) {
                    log_message('error', 'File not found in any location: ' . $file_name);
                    show_404();
                    return;
                }
            } else {
                log_message('info', 'File found at: ' . $file_path);
            }

            // ตรวจสอบว่าเป็นไฟล์รูปภาพ
            $file_info = pathinfo($file_path);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

            if (!in_array(strtolower($file_info['extension']), $allowed_extensions)) {
                log_message('error', 'Invalid image extension: ' . $file_info['extension']);
                show_error('ไฟล์นี้ไม่ใช่รูปภาพ', 400);
                return;
            }

            // กำหนด MIME type
            $mime_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'bmp' => 'image/bmp'
            ];

            $extension = strtolower($file_info['extension']);
            $mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'image/jpeg';

            // *** แก้ไข: ลดการตรวจสอบสิทธิ์เพื่อให้แสดงผลได้ ***
            $current_user = $this->get_current_user_detailed();

            // ตรวจสอบสิทธิ์แบบหลวม
            if ($current_user['is_logged_in']) {
                log_message('info', 'User logged in, checking permissions for: ' . $current_user['user_type']);

                // ดึงข้อมูลไฟล์จากฐานข้อมูล
                $this->db->select('queue_file_ref_id');
                $this->db->from('tbl_queue_files');
                $this->db->where('queue_file_name', $file_name);
                $file_data = $this->db->get()->row();

                if ($file_data) {
                    // ตรวจสอบคิวที่เกี่ยวข้อง
                    $this->db->select('queue_phone, queue_user_type, queue_user_id');
                    $this->db->from('tbl_queue');
                    $this->db->where('queue_id', $file_data->queue_file_ref_id);
                    $queue_data = $this->db->get()->row();

                    if ($queue_data) {
                        // Staff สามารถดูได้ทั้งหมด
                        if ($current_user['user_type'] === 'staff') {
                            log_message('info', 'Staff access granted');
                        }
                        // Public user ตรวจสอบเบอร์โทร
                        elseif (
                            $current_user['user_type'] === 'public' &&
                            isset($current_user['user_info']['phone']) &&
                            $current_user['user_info']['phone'] === $queue_data->queue_phone
                        ) {
                            log_message('info', 'Public user access granted - phone match');
                        }
                        // Guest หรือ public user ที่ไม่ตรงเบอร์
                        else {
                            log_message('warning', 'Access denied - user type: ' . $current_user['user_type'] . ', phone: ' . ($current_user['user_info']['phone'] ?? 'none'));
                            show_404();
                            return;
                        }
                    } else {
                        log_message('warning', 'Queue data not found for file: ' . $file_name);
                    }
                } else {
                    log_message('warning', 'File data not found in database: ' . $file_name);
                    // ไม่พบในฐานข้อมูล แต่ไฟล์มีอยู่ - อนุญาตให้ดูได้ (อาจเป็นไฟล์เก่า)
                }
            } else {
                log_message('info', 'Guest user accessing image');
                // Guest user - อนุญาตให้ดูได้บางกรณี (เช่น ไฟล์สาธารณะ)
            }

            // ตั้งค่า headers
            header('Content-Type: ' . $mime_type);
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: public, max-age=3600');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file_path)) . ' GMT');

            log_message('info', 'Serving image: ' . $file_name . ' (Type: ' . $mime_type . ', Size: ' . filesize($file_path) . ')');

            // ส่งไฟล์
            readfile($file_path);

        } catch (Exception $e) {
            log_message('error', 'Error in view_queue_image: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            show_404();
        }
    }


    /**
     * Helper functions
     */
    private function is_image_file($mime_type)
    {
        $image_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        return in_array(strtolower($mime_type), $image_types);
    }

    private function get_file_icon($mime_type)
    {
        $icons = [
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'application/vnd.ms-excel' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
            'image/jpeg' => 'fa-file-image',
            'image/jpg' => 'fa-file-image',
            'image/png' => 'fa-file-image',
            'image/gif' => 'fa-file-image',
            'image/webp' => 'fa-file-image',
            'text/plain' => 'fa-file-alt',
            'application/zip' => 'fa-file-archive',
            'application/rar' => 'fa-file-archive'
        ];

        return $icons[strtolower($mime_type)] ?? 'fa-file';
    }

    private function format_file_size($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // *** เพิ่มฟังก์ชัน get_queue_status_symbol สำหรับ view ***
    public function get_queue_status_symbol($status)
    {
        $symbol_map = [
            'รอยืนยันการจอง' => '⏳',
            'ยืนยันการจอง' => '✅',
            'คิวได้รับการยืนยัน' => '✅',
            'รับเรื่องแล้ว' => '📥',
            'กำลังดำเนินการ' => '⚙️',
            'รอดำเนินการ' => '⏳',
            'เสร็จสิ้น' => '🎉',
            'ยกเลิก' => '❌',
            'คิวได้ถูกยกเลิก' => '❌'
        ];

        return $symbol_map[$status] ?? '●';
    }


    /**
     * หน้ารายงานการจองคิว - แก้ไขครบถ้วน
     * *** เฉพาะ Staff เท่านั้น ***
     */
    private function check_queue_management_permissions()
    {
        $permissions = [
            'can_view' => false,
            'can_update_status' => false,
            'can_delete' => false,
            'can_manage_all' => false
        ];

        try {
            // ดึงข้อมูล user
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                return $permissions;
            }

            // ดึงข้อมูล member
            $this->db->select('m.m_id, m.m_fname, m.m_lname, m.m_system, m.grant_user_ref_id, p.pid, p.pname');
            $this->db->from('tbl_member m');
            $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
            $this->db->where('m.m_id', $m_id);
            $this->db->where('m.m_status', '1');
            $user_data = $this->db->get()->row();

            if (!$user_data) {
                return $permissions;
            }

            // แปลงชื่อ role เป็น lowercase
            $role = strtolower($user_data->m_system);

            // แปลง grant_user_ref_id จาก string CSV เป็น array
            $user_grants = !empty($user_data->grant_user_ref_id)
                ? explode(',', $user_data->grant_user_ref_id)
                : [];

            // Trim whitespace และแปลงเป็น integer
            $user_grants = array_map('intval', array_map('trim', $user_grants));

            switch ($role) {
                case 'system_admin':
                case 'super_admin':
                    $permissions['can_view'] = true;
                    $permissions['can_update_status'] = true;
                    $permissions['can_delete'] = true;
                    $permissions['can_manage_all'] = true;
                    break;

                case 'user_admin':
                    $permissions['can_view'] = true;

                    // ✅ ตรวจสอบว่ามีสิทธิ์ 106 ใน grant_user_ref_id หรือไม่
                    if (in_array(106, $user_grants)) {
                        $permissions['can_update_status'] = true;
                    }

                    $permissions['can_delete'] = false;
                    $permissions['can_manage_all'] = false;
                    break;

                default:
                    // บุคคลทั่วไป
                    $permissions['can_view'] = true;
                    $permissions['can_update_status'] = false;
                    $permissions['can_delete'] = false;
                    $permissions['can_manage_all'] = false;
                    break;
            }

            log_message('info', 'Queue permissions for user ' . $m_id .
                ' (ROLE: ' . $role . ', GRANTS: ' . implode(',', $user_grants) . '): ' .
                json_encode($permissions));

        } catch (Exception $e) {
            log_message('error', 'Error checking queue management permissions: ' . $e->getMessage());
        }

        return $permissions;
    }

    /**
     * ลบคิว (เฉพาะ System Admin และ Super Admin)
     */
    public function delete_queue()
    {
        // ล้าง output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        ini_set('display_errors', 0);

        try {
            // ตรวจสอบ request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบสิทธิ์
            $permissions = $this->check_queue_management_permissions();
            if (!$permissions['can_delete']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์ในการลบคิว'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // รับข้อมูล
            $queue_id = trim($this->input->post('queue_id'));
            $delete_reason = trim($this->input->post('delete_reason'));

            // Validation
            if (empty($queue_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาระบุหมายเลขคิว'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (empty($delete_reason)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'กรุณาระบุเหตุผลการลบ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (mb_strlen($delete_reason) < 5) {
                echo json_encode([
                    'success' => false,
                    'message' => 'เหตุผลการลบต้องมีอย่างน้อย 5 ตัวอักษร'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ตรวจสอบว่าคิวมีอยู่จริง
            $this->db->select('queue_id, queue_topic, queue_by, queue_phone, queue_status');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $queue_data = $this->db->get()->row();

            if (!$queue_data) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบหมายเลขคิวที่ระบุ'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // เริ่ม transaction
            $this->db->trans_start();

            // ดึงข้อมูล staff
            $m_id = $this->session->userdata('m_id');
            $this->db->select('m_fname, m_lname');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $staff_data = $this->db->get()->row();
            $staff_name = $staff_data ? $staff_data->m_fname . ' ' . $staff_data->m_lname : 'System';

            // บันทึกประวัติการลบใน tbl_queue_detail
            $detail_data = [
                'queue_detail_case_id' => $queue_id,
                'queue_detail_status' => 'ถูกลบ',
                'queue_detail_by' => $staff_name,
                'queue_detail_com' => 'ลบคิวโดย: ' . $staff_name . ' เหตุผล: ' . $delete_reason,
                'queue_detail_datesave' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tbl_queue_detail', $detail_data);

            // ลบข้อมูลไฟล์แนบ
            $this->db->select('queue_file_path');
            $this->db->from('tbl_queue_files');
            $this->db->where('queue_file_ref_id', $queue_id);
            $files = $this->db->get()->result();

            foreach ($files as $file) {
                if (file_exists($file->queue_file_path)) {
                    unlink($file->queue_file_path);
                }
            }

            // ลบข้อมูลไฟล์ในฐานข้อมูล
            $this->db->where('queue_file_ref_id', $queue_id);
            $this->db->delete('tbl_queue_files');

            // ลบข้อมูลประวัติ
            $this->db->where('queue_detail_case_id', $queue_id);
            $this->db->delete('tbl_queue_detail');

            // ลบคิวหลัก
            $this->db->where('queue_id', $queue_id);
            $delete_result = $this->db->delete('tbl_queue');

            if (!$delete_result) {
                $this->db->trans_rollback();
                throw new Exception('Failed to delete queue');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // บันทึก log
            log_message('info', "Queue {$queue_id} deleted by {$staff_name}. Reason: {$delete_reason}");

            // สร้างการแจ้งเตือน
            try {
                if ($this->db->table_exists('tbl_notifications')) {
                    $staff_data_json = json_encode([
                        'queue_id' => $queue_id,
                        'topic' => $queue_data->queue_topic,
                        'requester' => $queue_data->queue_by,
                        'phone' => $queue_data->queue_phone,
                        'delete_reason' => $delete_reason,
                        'deleted_by' => $staff_name,
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'type' => 'queue_deleted'
                    ], JSON_UNESCAPED_UNICODE);

                    $notification = [
                        'type' => 'queue_deleted',
                        'title' => 'คิวถูกลบ',
                        'message' => "คิว {$queue_id} ({$queue_data->queue_topic}) ถูกลบโดย {$staff_name}",
                        'reference_id' => 0,
                        'reference_table' => 'tbl_queue',
                        'target_role' => 'staff',
                        'priority' => 'high',
                        'icon' => 'fas fa-trash',
                        'url' => site_url("Queue/queue_report"),
                        'data' => $staff_data_json,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $m_id,
                        'is_read' => 0,
                        'is_system' => 1,
                        'is_archived' => 0
                    ];

                    $this->db->insert('tbl_notifications', $notification);
                }
            } catch (Exception $e) {
                log_message('error', 'Failed to create delete notification: ' . $e->getMessage());
            }

            echo json_encode([
                'success' => true,
                'message' => 'ลบคิวเรียบร้อยแล้ว',
                'queue_id' => $queue_id,
                'delete_reason' => $delete_reason
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            // Rollback transaction
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }

            log_message('error', 'Delete queue error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    /**
     * ปรับปรุง queue_report ให้รวมการตรวจสอบสิทธิ์
     */
    public function queue_report()
    {
        try {
            // ตรวจสอบสิทธิ์ - เฉพาะ Staff เท่านั้น
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                redirect('User');
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึงหน้า report
            $permissions = $this->check_queue_management_permissions();
            if (!$permissions['can_view']) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                redirect('System_reports');
                return;
            }

            // โหลด models
            $this->load->model('Queue_model', 'queue_model');

            // ใช้ฟังก์ชันที่แก้ไขแล้ว
            $reports_base_data = $this->prepare_reports_base_data('รายงานการจองคิว');

            // เพิ่มข้อมูลสิทธิ์
            $reports_base_data['permissions'] = $permissions;

            // ตัวกรองข้อมูล
            $filters = [
                'status' => $this->input->get('status'),
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

            // ดึงข้อมูลคิวพร้อมกรอง
            $queues_result = $this->queue_model->get_queues_with_filters($filters, $per_page, $offset);
            $queues = $queues_result['data'] ?? [];
            $total_rows = $queues_result['total'] ?? 0;

            // ดึงข้อมูลไฟล์แนบ
            if (!empty($queues)) {
                foreach ($queues as $queue) {
                    $queue->files = $this->queue_model->get_queue_files($queue->queue_id);
                }
            }

            // สถิติการจองคิว
            $queue_summary = $this->queue_model->get_queue_statistics_detailed();

            // ตัวเลือกสำหรับ Filter
            $status_options = $this->queue_model->get_all_queue_statuses_options();
            $user_type_options = [
                ['queue_user_type' => 'guest', 'display_name' => 'ผู้ใช้ทั่วไป (Guest)'],
                ['queue_user_type' => 'public', 'display_name' => 'สมาชิก (Public)'],
                ['queue_user_type' => 'staff', 'display_name' => 'เจ้าหน้าที่ (Staff)']
            ];

            // แนวโน้มรายวัน
            $queue_trends = $this->queue_model->get_daily_queue_trends(30);

            // *** แก้ไข: ใช้ method เดียวกับ queue_alerts ***
            $pending_queues = $this->get_pending_queues_for_alerts_report();

            // Pagination Setup
            $pagination_config = [
                'base_url' => site_url('Queue/queue_report'),
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

            // รวมข้อมูลทั้งหมด
            $data = array_merge($reports_base_data, [
                'queues' => $queues,
                'queue_summary' => $queue_summary,
                'queue_trends' => $queue_trends,
                'pending_queues' => $pending_queues,
                'filters' => $filters,
                'status_options' => $status_options,
                'user_type_options' => $user_type_options,
                'total_rows' => $total_rows,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'pagination' => $this->pagination->create_links(),
                'permissions' => $permissions
            ]);

            // Debug information
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Queue report final data: ' . json_encode([
                    'staff_user' => $staff_check->m_fname . ' ' . $staff_check->m_lname,
                    'is_logged_in' => $data['is_logged_in'],
                    'user_type' => $data['user_type'],
                    'queues_count' => count($queues),
                    'total_rows' => $total_rows,
                    'pending_queues_count' => count($pending_queues),
                    'permissions' => $permissions
                ]));
            }

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/queue_report', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Error in queue_report: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('System_reports');
            }
        }
    }







    private function get_pending_queues_for_alerts_report()
    {
        try {
            // *** ใช้ SQL เดียวกับ queue_alerts ***
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

            // เพิ่มข้อมูล priority สำหรับแต่ละ queue
            if ($result) {
                foreach ($result as $queue) {
                    $days = intval($queue->days_old);
                    if ($days >= 14) {
                        $queue->priority = 'critical';
                        $queue->priority_label = 'วิกฤต';
                    } elseif ($days >= 7) {
                        $queue->priority = 'danger';
                        $queue->priority_label = 'เร่งด่วน';
                    } else {
                        $queue->priority = 'warning';
                        $queue->priority_label = 'ติดตาม';
                    }
                }
            }

            log_message('info', 'Found ' . count($result) . ' pending queues for report alerts');
            return $result ?: [];

        } catch (Exception $e) {
            log_message('error', 'Error in get_pending_queues_for_alerts_report: ' . $e->getMessage());
            return [];
        }
    }




    public function get_queue_alerts_with_filters($filters = [], $limit = 25, $offset = 0)
    {
        try {
            $where_conditions = [];
            $params = [];

            // เงื่อนไขพื้นฐาน - เหมือนกับ get_pending_queues_for_alerts
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



    private function get_complete_user_info_for_reports()
    {
        $result = [
            'user_info' => null,
            'is_logged_in' => false,
            'user_type' => 'guest',
            'tenant_name' => $this->session->userdata('tenant_name') ?: 'ระบบรายงาน'
        ];

        try {
            // *** ตรวจสอบ staff user ก่อน - แก้ไขให้ยืดหยุ่นกว่า ***
            $m_id = $this->session->userdata('m_id');

            if (!empty($m_id)) {
                log_message('debug', "Checking staff user with m_id: {$m_id}");

                // *** แก้ไข: ตรวจสอบ m_id อย่างเดียวก่อน แล้วค่อยตรวจสอบเงื่อนไขอื่น ***
                $this->db->select('m.m_id, m.m_email, m.m_fname, m.m_lname, m.m_phone, m.m_img, m.m_system, m.m_status, COALESCE(p.pname, "เจ้าหน้าที่") as pname, COALESCE(p.peng, "Staff") as peng');
                $this->db->from('tbl_member m');
                $this->db->join('tbl_position p', 'm.ref_pid = p.pid', 'left');
                $this->db->where('m.m_id', $m_id);
                $staff_data = $this->db->get()->row();

                log_message('debug', "Staff query result: " . ($staff_data ? 'FOUND' : 'NOT_FOUND'));

                if ($staff_data) {
                    // *** ตรวจสอบสถานะหลังจากดึงข้อมูลแล้ว ***
                    if ($staff_data->m_status == '1' || $staff_data->m_status == 1) {
                        // *** แก้ไข: ส่งกลับเป็น object โดยตรง ***
                        $result['user_info'] = $staff_data;
                        $result['is_logged_in'] = true;
                        $result['user_type'] = 'staff';

                        log_message('info', "Staff user found for reports: {$staff_data->m_fname} {$staff_data->m_lname} ({$staff_data->pname})");
                        return $result;
                    } else {
                        log_message('warning', "Staff user found but inactive: m_id={$m_id}, status={$staff_data->m_status}");
                    }
                } else {
                    log_message('warning', "Staff user not found in database: m_id={$m_id}");

                    // *** Debug: ลอง query ด้วยเงื่อนไขพื้นฐานเท่านั้น ***
                    $this->db->select('*');
                    $this->db->from('tbl_member');
                    $this->db->where('m_id', $m_id);
                    $debug_result = $this->db->get()->row();

                    if ($debug_result) {
                        log_message('debug', "Debug - User exists in tbl_member: m_id={$m_id}, status={$debug_result->m_status}, fname={$debug_result->m_fname}");

                        // *** ถ้าพบข้อมูลแต่ JOIN ผิด ให้ใช้ข้อมูลพื้นฐาน ***
                        // *** แก้ไข: สร้าง object ใหม่แทนการใช้ array ***
                        $result['user_info'] = (object) [
                            'm_id' => $debug_result->m_id,
                            'm_email' => $debug_result->m_email,
                            'm_fname' => $debug_result->m_fname,
                            'm_lname' => $debug_result->m_lname,
                            'm_phone' => $debug_result->m_phone,
                            'm_img' => $debug_result->m_img,
                            'm_system' => $debug_result->m_system,
                            'm_status' => $debug_result->m_status,
                            'pname' => 'เจ้าหน้าที่',
                            'peng' => 'Staff'
                        ];
                        $result['is_logged_in'] = true;
                        $result['user_type'] = 'staff';

                        log_message('info', "Staff user loaded with basic info: {$debug_result->m_fname} {$debug_result->m_lname}");
                        return $result;
                    } else {
                        log_message('error', "User completely not found: m_id={$m_id}");
                    }
                }
            }

            // *** ตรวจสอบ public user ***
            $mp_id = $this->session->userdata('mp_id');

            if (!empty($mp_id)) {
                log_message('debug', "Checking public user with mp_id: {$mp_id}");

                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone, mp_number, mp_status');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $public_data = $this->db->get()->row();

                log_message('debug', "Public query result: " . ($public_data ? 'FOUND' : 'NOT_FOUND'));

                if ($public_data) {
                    // *** ตรวจสอบสถานะหลังจากดึงข้อมูล ***
                    if ($public_data->mp_status == 1 || $public_data->mp_status == '1') {
                        // *** แก้ไข: แปลงข้อมูล public user ให้ตรงกับรูปแบบ staff แต่ส่งกลับเป็น object ***
                        $result['user_info'] = (object) [
                            'm_id' => $public_data->id,
                            'm_email' => $public_data->mp_email,
                            'm_fname' => $public_data->mp_fname,
                            'm_lname' => $public_data->mp_lname,
                            'm_phone' => $public_data->mp_phone,
                            'm_img' => '',
                            'm_system' => 'public',
                            'm_status' => $public_data->mp_status,
                            'pname' => 'สมาชิก',
                            'peng' => 'Public Member'
                        ];
                        $result['is_logged_in'] = true;
                        $result['user_type'] = 'public';

                        log_message('info', "Public user info converted for reports: {$public_data->mp_fname} {$public_data->mp_lname}");
                        return $result;
                    } else {
                        log_message('warning', "Public user found but inactive: mp_id={$mp_id}, status={$public_data->mp_status}");
                    }
                } else {
                    log_message('warning', "Public user not found in database: mp_id={$mp_id}");
                }
            }

            // *** Session Debug Information ***
            $session_debug = [
                'm_id' => $this->session->userdata('m_id'),
                'm_email' => $this->session->userdata('m_email'),
                'm_fname' => $this->session->userdata('m_fname'),
                'mp_id' => $this->session->userdata('mp_id'),
                'mp_email' => $this->session->userdata('mp_email'),
                'tenant_name' => $this->session->userdata('tenant_name')
            ];
            log_message('warning', 'No valid user found. Session data: ' . json_encode($session_debug));

        } catch (Exception $e) {
            log_message('error', 'Error in get_complete_user_info_for_reports: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        }

        // *** กรณีที่ไม่พบข้อมูล user หรือเป็น guest ***
        log_message('warning', 'Falling back to guest user for reports');

        // *** แก้ไข: ส่งกลับ object แทน array ***
        $result['user_info'] = (object) [
            'm_id' => 0,
            'm_fname' => 'Guest',
            'm_lname' => 'User',
            'm_email' => '',
            'm_phone' => '',
            'm_img' => '',
            'm_system' => '',
            'm_status' => '',
            'pname' => 'ผู้เยี่ยมชม',
            'peng' => 'Visitor'
        ];

        return $result;
    }



    private function prepare_reports_base_data($page_title = 'รายงาน', $breadcrumb = [])
    {
        $user_data = $this->get_complete_user_info_for_reports();

        // *** เพิ่มการตรวจสอบว่า user_info เป็น object ***
        if (is_array($user_data['user_info'])) {
            log_message('warning', 'Converting user_info array to object');
            $user_data['user_info'] = (object) $user_data['user_info'];
        }

        // *** ตรวจสอบ property ที่จำเป็น ***
        if (is_object($user_data['user_info'])) {
            $required_properties = ['m_id', 'm_fname', 'm_lname', 'm_email', 'm_phone', 'm_img', 'm_system', 'm_status', 'pname', 'peng'];

            foreach ($required_properties as $prop) {
                if (!property_exists($user_data['user_info'], $prop)) {
                    $user_data['user_info']->$prop = $this->get_default_property_value($prop);
                }
            }
        } else {
            // *** สร้าง object ใหม่หาก user_info ไม่ใช่ object ***
            log_message('error', 'user_info is not an object, creating new one');
            $user_data['user_info'] = (object) [
                'm_id' => 0,
                'm_fname' => 'Unknown',
                'm_lname' => 'User',
                'm_email' => '',
                'm_phone' => '',
                'm_img' => '',
                'm_system' => '',
                'm_status' => '',
                'pname' => 'ผู้ใช้ไม่ทราบ',
                'peng' => 'Unknown'
            ];
        }

        $base_data = [
            'page_title' => $page_title,
            'user_info' => $user_data['user_info'], // *** ตรวจสอบแล้วว่าเป็น object ***
            'is_logged_in' => $user_data['is_logged_in'],
            'user_type' => $user_data['user_type'],
            'tenant_name' => $user_data['tenant_name'],
            'system_name' => 'ระบบรายงาน',
            'breadcrumb' => !empty($breadcrumb) ? $breadcrumb : [
                ['title' => 'หน้าแรก', 'url' => site_url('/')],
                ['title' => 'ระบบรายงาน', 'url' => site_url('System_reports')],
                ['title' => $page_title, 'url' => '']
            ]
        ];

        // *** Debug information - ตรวจสอบก่อนส่งไป view ***
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Reports base data prepared: ' . json_encode([
                'user_info_type' => gettype($base_data['user_info']),
                'has_m_fname' => is_object($base_data['user_info']) && property_exists($base_data['user_info'], 'm_fname'),
                'has_pname' => is_object($base_data['user_info']) && property_exists($base_data['user_info'], 'pname'),
                'm_fname_value' => is_object($base_data['user_info']) && property_exists($base_data['user_info'], 'm_fname') ? $base_data['user_info']->m_fname : 'NOT_SET',
                'is_logged_in' => $base_data['is_logged_in'],
                'user_type' => $base_data['user_type']
            ]));
        }

        return $base_data;
    }

    /**
     * *** เพิ่ม helper method ใหม่ ***
     */
    private function get_default_property_value($property)
    {
        $defaults = [
            'm_id' => 0,
            'm_fname' => 'Unknown',
            'm_lname' => 'User',
            'm_email' => '',
            'm_phone' => '',
            'm_img' => '',
            'm_system' => '',
            'm_status' => '',
            'pname' => 'ผู้ใช้ไม่ทราบ',
            'peng' => 'Unknown'
        ];

        return isset($defaults[$property]) ? $defaults[$property] : '';
    }



    /**
     * จัดการไฟล์แนบสำหรับการอัพเดทสถานะคิว
     */
    private function handle_queue_status_images($queue_id)
    {
        $this->load->library('upload');

        $upload_path = './docs/queue/status/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|pdf|doc|docx',
            'max_size' => 10240, // 10MB
            'encrypt_name' => TRUE
        ];

        $uploaded_files = [];
        $file_count = count($_FILES['status_images']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['status_images']['error'][$i] === UPLOAD_ERR_OK) {
                $_FILES['single_file'] = [
                    'name' => $_FILES['status_images']['name'][$i],
                    'type' => $_FILES['status_images']['type'][$i],
                    'tmp_name' => $_FILES['status_images']['tmp_name'][$i],
                    'error' => $_FILES['status_images']['error'][$i],
                    'size' => $_FILES['status_images']['size'][$i]
                ];

                $this->upload->initialize($config);

                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();

                    // บันทึกข้อมูลไฟล์
                    $file_data = [
                        'queue_file_ref_id' => $queue_id,
                        'queue_file_name' => $upload_data['file_name'],
                        'queue_file_original_name' => $upload_data['orig_name'],
                        'queue_file_type' => $upload_data['file_type'],
                        'queue_file_size' => $upload_data['file_size'] * 1024,
                        'queue_file_path' => $upload_data['full_path'],
                        'queue_file_uploaded_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_queue_files', $file_data);
                    $uploaded_files[] = $upload_data['file_name'];
                }
            }
        }

        return $uploaded_files;
    }

    /**
     * ส่ง notifications สำหรับการอัพเดทสถานะคิว
     */
    private function send_queue_status_notifications($queue_id, $new_status, $staff_name)
    {
        try {
            // โหลด Notification library
            $this->load->library('Notification_lib');

            // ดึงข้อมูลคิว
            $queue_data = $this->queue_model->get_queue_details($queue_id);

            if (!$queue_data) {
                log_message('warning', 'Queue not found for notification: ' . $queue_id);
                return;
            }

            // Notification สำหรับ Staff
            $this->Notification_lib->create_custom_notification(
                'queue_status_update',
                'อัพเดทสถานะคิว',
                "คิว #{$queue_id} อัพเดทสถานะเป็น: {$new_status} โดย {$staff_name}",
                'staff',
                [
                    'reference_id' => 0,
                    'reference_table' => 'tbl_queue',
                    'priority' => 'normal',
                    'icon' => 'fas fa-calendar-check',
                    'url' => site_url("Queue_backend/detail/{$queue_id}"),
                    'data' => [
                        'queue_id' => $queue_id,
                        'topic' => $queue_data->queue_topic,
                        'new_status' => $new_status,
                        'updated_by' => $staff_name
                    ]
                ]
            );

            // Notification สำหรับเจ้าของคิว (ถ้าเป็น public user)
            if ($queue_data->queue_user_type === 'public' && !empty($queue_data->queue_user_id)) {
                $this->Notification_lib->create_custom_notification(
                    'queue_status_update',
                    'อัพเดทสถานะคิวของคุณ',
                    "คิว \"{$queue_data->queue_topic}\" ได้รับการอัพเดทสถานะเป็น: {$new_status}",
                    'public',
                    [
                        'reference_id' => 0,
                        'reference_table' => 'tbl_queue',
                        'target_user_id' => $queue_data->queue_user_id,
                        'priority' => 'high',
                        'icon' => 'fas fa-bell',
                        'url' => site_url("Queue/my_queue_detail/{$queue_id}"),
                        'data' => [
                            'queue_id' => $queue_id,
                            'topic' => $queue_data->queue_topic,
                            'new_status' => $new_status,
                            'updated_by' => $staff_name
                        ]
                    ]
                );
            }

        } catch (Exception $e) {
            log_message('error', 'Failed to send queue status notifications: ' . $e->getMessage());
        }
    }



    /**
     * ดึงรายละเอียดผู้ใช้ที่จองคิว
     */
    private function get_queue_user_details($queue)
    {
        $details = [
            'user_type_display' => 'ไม่ทราบ',
            'user_info' => null,
            'full_address' => null
        ];

        try {
            if ($queue->queue_user_type === 'public' && !empty($queue->queue_user_id)) {
                $this->db->select('*');
                $this->db->from('tbl_member_public');
                $this->db->where('id', $queue->queue_user_id);
                $user_info = $this->db->get()->row();

                if ($user_info) {
                    $details['user_type_display'] = 'สมาชิก';
                    $details['user_info'] = $user_info;

                    // รวมที่อยู่
                    $address_parts = array_filter([
                        $user_info->mp_address,
                        $user_info->mp_district ? 'ต.' . $user_info->mp_district : '',
                        $user_info->mp_amphoe ? 'อ.' . $user_info->mp_amphoe : '',
                        $user_info->mp_province ? 'จ.' . $user_info->mp_province : '',
                        $user_info->mp_zipcode
                    ]);
                    $details['full_address'] = implode(' ', $address_parts);
                }
            } elseif ($queue->queue_user_type === 'staff' && !empty($queue->queue_user_id)) {
                $this->db->select('*');
                $this->db->from('tbl_member');
                $this->db->where('m_id', $queue->queue_user_id);
                $user_info = $this->db->get()->row();

                if ($user_info) {
                    $details['user_type_display'] = 'เจ้าหน้าที่';
                    $details['user_info'] = $user_info;
                }
            } elseif ($queue->queue_user_type === 'guest') {
                $details['user_type_display'] = 'ผู้ใช้ทั่วไป';

                // รวมที่อยู่สำหรับ guest
                $address_parts = array_filter([
                    $queue->queue_address,
                    $queue->guest_district ? 'ต.' . $queue->guest_district : '',
                    $queue->guest_amphoe ? 'อ.' . $queue->guest_amphoe : '',
                    $queue->guest_province ? 'จ.' . $queue->guest_province : '',
                    $queue->guest_zipcode
                ]);
                $details['full_address'] = implode(' ', $address_parts);
            }

        } catch (Exception $e) {
            log_message('error', 'Error getting queue user details: ' . $e->getMessage());
        }

        return $details;
    }






    /**
     * ส่งออกรายละเอียดคิวเป็น Excel
     */
    public function export_queue_excel($queue_id = null)
    {
        try {
            if (empty($queue_id)) {
                show_404();
                return;
            }

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in']) {
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อน');
                redirect('User');
                return;
            }

            // ดึงข้อมูลคิว
            $this->db->select('*');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $queue_id);
            $queue_data = $this->db->get()->row();

            if (!$queue_data) {
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์การเข้าถึง
            $has_permission = $this->check_queue_access_permission_for_detail($queue_data, $current_user['user_type'], $current_user['user_info']);

            if (!$has_permission) {
                show_error('คุณไม่มีสิทธิ์เข้าถึงข้อมูลคิวนี้', 403);
                return;
            }

            // ดึงประวัติ
            $this->db->select('*');
            $this->db->from('tbl_queue_detail');
            $this->db->where('queue_detail_case_id', $queue_id);
            $this->db->order_by('queue_detail_id', 'ASC');
            $queue_history = $this->db->get()->result();

            // ดึงไฟล์แนบ
            $this->db->select('*');
            $this->db->from('tbl_queue_files');
            $this->db->where('queue_file_ref_id', $queue_id);
            $queue_files = $this->db->get()->result();

            // สร้างชื่อไฟล์
            $filename = "queue_detail_{$queue_id}_" . date('Ymd_His') . ".csv";

            // ตั้งค่า headers สำหรับ CSV
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // เปิด output stream
            $output = fopen('php://output', 'w');

            // เพิ่ม BOM สำหรับ UTF-8
            fputs($output, "\xEF\xBB\xBF");

            // หัวข้อคอลัมน์
            $headers = [
                'หมายเลขคิว',
                'หัวข้อ',
                'รายละเอียด',
                'สถานะปัจจุบัน',
                'ชื่อผู้จอง',
                'เบอร์ติดต่อ',
                'อีเมล',
                'วันที่นัดหมาย',
                'เวลานัดหมาย',
                'วันที่สร้างคิว',
                'ประเภทผู้ใช้',
                'จำนวนไฟล์แนบ'
            ];
            fputcsv($output, $headers);

            // ข้อมูลคิวหลัก
            $row = [
                $queue_data->queue_id,
                $queue_data->queue_topic,
                $queue_data->queue_detail,
                $queue_data->queue_status,
                $queue_data->queue_by,
                $queue_data->queue_phone,
                $queue_data->queue_email ?: 'ไม่ระบุ',
                !empty($queue_data->queue_date) ? date('d/m/Y', strtotime($queue_data->queue_date)) : 'ไม่ระบุ',
                $queue_data->queue_time_slot ?: 'ไม่ระบุ',
                date('d/m/Y H:i', strtotime($queue_data->queue_datesave)),
                $this->get_user_type_display($queue_data->queue_user_type),
                count($queue_files)
            ];
            fputcsv($output, $row);

            // เพิ่มบรรทัดว่าง
            fputcsv($output, []);

            // ประวัติการดำเนินงาน
            fputcsv($output, ['ประวัติการดำเนินงาน']);
            $history_headers = ['วันที่', 'เวลา', 'สถานะ', 'รายละเอียด', 'ดำเนินการโดย'];
            fputcsv($output, $history_headers);

            // รายการประวัติ
            foreach ($queue_history as $detail) {
                $history_row = [
                    date('d/m/Y', strtotime($detail->queue_detail_datesave)),
                    date('H:i', strtotime($detail->queue_detail_datesave)),
                    $detail->queue_detail_status,
                    $detail->queue_detail_com,
                    $detail->queue_detail_by
                ];
                fputcsv($output, $history_row);
            }

            // เพิ่มบรรทัดว่าง
            fputcsv($output, []);

            // รายการไฟล์แนบ
            if (!empty($queue_files)) {
                fputcsv($output, ['ไฟล์แนบ']);
                $files_headers = ['ชื่อไฟล์', 'ขนาดไฟล์', 'ประเภทไฟล์', 'วันที่อัปโหลด'];
                fputcsv($output, $files_headers);

                foreach ($queue_files as $file) {
                    $file_row = [
                        $file->queue_file_original_name,
                        $this->format_file_size($file->queue_file_size),
                        $file->queue_file_type,
                        !empty($file->queue_file_uploaded_at) ? date('d/m/Y H:i', strtotime($file->queue_file_uploaded_at)) : 'ไม่ระบุ'
                    ];
                    fputcsv($output, $file_row);
                }
            }

            fclose($output);

        } catch (Exception $e) {
            log_message('error', 'Export queue Excel error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งออก Excel', 500);
        }
    }

    /**
     * Helper function สำหรับแปลงประเภทผู้ใช้
     */
    private function get_user_type_display($user_type)
    {
        switch ($user_type) {
            case 'public':
                return 'สมาชิก';
            case 'staff':
                return 'เจ้าหน้าที่';
            case 'guest':
                return 'ผู้ใช้ทั่วไป';
            default:
                return 'ไม่ทราบ';
        }
    }

    /**
     * ดาวน์โหลดไฟล์แนับคิว - แก้ไขเพิ่มเติม
     */
    public function download_queue_file($file_name)
    {
        try {
            if (empty($file_name)) {
                show_404();
                return;
            }

            // ตรวจสอบการ login
            $current_user = $this->get_current_user_detailed();
            if (!$current_user['is_logged_in']) {
                $this->session->set_flashdata('warning_message', 'กรุณาเข้าสู่ระบบก่อนดาวน์โหลดไฟล์');
                redirect('User');
                return;
            }

            // ลองหาไฟล์ในหลายโฟลเดอร์
            $possible_paths = [
                './docs/queue_files/' . $file_name,
                './docs/files/queue/' . $file_name,
                './uploads/queue/' . $file_name
            ];

            $file_path = null;
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    $file_path = $path;
                    break;
                }
            }

            if (!$file_path) {
                log_message('error', 'Queue file not found: ' . $file_name);
                show_404();
                return;
            }

            // ดึงข้อมูลไฟล์จากฐานข้อมูล
            $this->db->select('*');
            $this->db->from('tbl_queue_files');
            $this->db->where('queue_file_name', $file_name);
            $file_info = $this->db->get()->row();

            if (!$file_info) {
                log_message('error', 'Queue file info not found in database: ' . $file_name);
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์การดาวน์โหลด
            $this->db->select('*');
            $this->db->from('tbl_queue');
            $this->db->where('queue_id', $file_info->queue_file_ref_id);
            $queue_data = $this->db->get()->row();

            if (!$queue_data) {
                log_message('error', 'Queue not found for file: ' . $file_name);
                show_404();
                return;
            }

            $has_permission = $this->check_queue_access_permission_for_detail($queue_data, $current_user['user_type'], $current_user['user_info']);
            if (!$has_permission) {
                log_message('warning', 'Download permission denied for file: ' . $file_name . ' by user: ' . ($current_user['user_info']['name'] ?? 'unknown'));
                show_error('คุณไม่มีสิทธิ์ดาวน์โหลดไฟล์นี้', 403);
                return;
            }

            $original_name = $file_info->queue_file_original_name;
            $file_type = $file_info->queue_file_type;

            // ตั้งค่า header สำหรับดาวน์โหลด
            header('Content-Type: ' . $file_type);
            header('Content-Disposition: attachment; filename="' . $original_name . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // Log การดาวน์โหลด
            log_message('info', 'File downloaded: ' . $original_name . ' by user: ' . ($current_user['user_info']['name'] ?? 'unknown'));

            // อ่านและส่งไฟล์
            readfile($file_path);

        } catch (Exception $e) {
            log_message('error', 'Error in download_queue_file: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการดาวน์โหลดไฟล์', 500);
        }
    }







    /**
     * Helper function สำหรับ view - ไม่ต้องเป็น private
     */
    public function get_queue_status_class_for_view($status)
    {
        return $this->get_queue_status_class($status);
    }

    public function get_queue_status_color_for_view($status)
    {
        return $this->get_queue_status_color($status);
    }

    public function get_queue_status_icon_for_view($status)
    {
        return $this->get_queue_status_icon($status);
    }

    // *** แก้ไข queue_alerts method - เพิ่มข้อมูลที่ view ต้องการ ***
    public function queue_alerts()
    {
        try {
            // ตรวจสอบสิทธิ์ - เฉพาะ Staff เท่านั้น
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // ตรวจสอบว่าเป็น staff จริงๆ
            $this->db->select('m_id, m_fname, m_lname, m_system, m_status');
            $this->db->from('tbl_member');
            $this->db->where('m_id', $m_id);
            $this->db->where('m_status', '1');
            $staff_check = $this->db->get()->row();

            if (!$staff_check) {
                $this->session->set_flashdata('error_message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                redirect('User');
                return;
            }

            // โหลด models
            $this->load->model('Queue_model', 'queue_model');

            // ใช้ฟังก์ชันเตรียมข้อมูลพื้นฐาน
            $data = $this->prepare_reports_base_data('รายการคิวที่ต้องติดตาม');

            // ตัวกรองข้อมูล
            $filters = [
                'priority' => $this->input->get('priority'),
                'status' => $this->input->get('status'),
                'days_min' => $this->input->get('days_min') ?: 3,
                'days_max' => $this->input->get('days_max'),
                'search' => $this->input->get('search')
            ];

            // Pagination
            $this->load->library('pagination');
            $per_page = 25;
            $current_page = (int) ($this->input->get('page') ?? 1);
            $offset = ($current_page - 1) * $per_page;

            // ดึงข้อมูลคิวที่ต้องติดตาม
            $alerts_result = $this->queue_model->get_queue_alerts_with_filters($filters, $per_page, $offset);
            $queue_alerts = $alerts_result['data'] ?? [];
            $total_rows = $alerts_result['total'] ?? 0;

            // *** แก้ไข: เพิ่มข้อมูลสำหรับการแสดงผลในแต่ละ alert ***
            $grouped_alerts = [
                'critical' => [],
                'danger' => [],
                'warning' => []
            ];

            foreach ($queue_alerts as $alert) {
                $days = intval($alert->days_old);
                if ($days >= 14) {
                    $alert->priority = 'critical';
                    $alert->priority_label = 'วิกฤต';
                    $grouped_alerts['critical'][] = $alert;
                } elseif ($days >= 7) {
                    $alert->priority = 'danger';
                    $alert->priority_label = 'เร่งด่วน';
                    $grouped_alerts['danger'][] = $alert;
                } else {
                    $alert->priority = 'warning';
                    $alert->priority_label = 'ติดตาม';
                    $grouped_alerts['warning'][] = $alert;
                }

                // *** เพิ่มข้อมูลสำหรับ view ***
                $alert->status_class = $this->get_queue_status_class($alert->queue_status);
                $alert->status_color = $this->get_queue_status_color($alert->queue_status);
                $alert->status_icon = $this->get_queue_status_icon($alert->queue_status);
            }

            // สถิติ
            $stats = [
                'total' => count($queue_alerts),
                'critical' => count($grouped_alerts['critical']),
                'danger' => count($grouped_alerts['danger']),
                'warning' => count($grouped_alerts['warning']),
                'avg_days' => $queue_alerts ? round(array_sum(array_column($queue_alerts, 'days_old')) / count($queue_alerts), 1) : 0
            ];

            // ตัวเลือกสำหรับ Filter
            $priority_options = [
                ['value' => '', 'label' => 'ทุกระดับ'],
                ['value' => 'critical', 'label' => 'วิกฤต (14+ วัน)'],
                ['value' => 'danger', 'label' => 'เร่งด่วน (7-13 วัน)'],
                ['value' => 'warning', 'label' => 'ติดตาม (3-6 วัน)']
            ];

            $status_options = [
                ['value' => '', 'label' => 'ทุกสถานะ'],
                ['value' => 'รอยืนยันการจอง', 'label' => 'รอยืนยันการจอง'],
                ['value' => 'คิวได้รับการยืนยัน', 'label' => 'คิวได้รับการยืนยัน'],
                ['value' => 'รับเรื่องพิจารณา', 'label' => 'รับเรื่องพิจารณา'],
                ['value' => 'รอดำเนินการ', 'label' => 'รอดำเนินการ'],
                ['value' => 'กำลังดำเนินการ', 'label' => 'กำลังดำเนินการ']
            ];

            // Pagination Setup
            $pagination_config = [
                'base_url' => site_url('Queue/queue_alerts'),
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

            // *** เพิ่ม helper functions สำหรับ view ***
            $data['helper_functions'] = [
                'get_queue_status_class' => function ($status) {
                    return $this->get_queue_status_class($status);
                },
                'get_queue_status_color' => function ($status) {
                    return $this->get_queue_status_color($status);
                },
                'get_queue_status_icon' => function ($status) {
                    return $this->get_queue_status_icon($status);
                }
            ];

            // รวมข้อมูลทั้งหมด
            $data = array_merge($data, [
                'queue_alerts' => $queue_alerts,
                'grouped_alerts' => $grouped_alerts,
                'stats' => $stats,
                'filters' => $filters,
                'priority_options' => $priority_options,
                'status_options' => $status_options,
                'total_rows' => $total_rows,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'pagination' => $this->pagination->create_links()
            ]);

            // Breadcrumb
            $data['breadcrumb'] = [
                ['title' => 'หน้าแรก', 'url' => site_url('/')],
                ['title' => 'ระบบรายงาน', 'url' => site_url('System_reports')],
                ['title' => 'รายงานการจองคิว', 'url' => site_url('Queue/queue_report')],
                ['title' => 'คิวที่ต้องติดตาม', 'url' => '']
            ];

            // Flash Messages
            $data['success_message'] = $this->session->flashdata('success_message');
            $data['error_message'] = $this->session->flashdata('error_message');

            // โหลด View
            $this->load->view('reports/header', $data);
            $this->load->view('reports/queue_alerts', $data);
            $this->load->view('reports/footer');

        } catch (Exception $e) {
            log_message('error', 'Error in queue_alerts: ' . $e->getMessage());

            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการโหลดหน้า: ' . $e->getMessage(), 500);
            } else {
                $this->session->set_flashdata('error_message', 'เกิดข้อผิดพลาดในการโหลดหน้า กรุณาลองใหม่อีกครั้ง');
                redirect('Queue/queue_report');
            }
        }
    }

    // *** เพิ่ม method ใหม่สำหรับ export queue alerts ***
    public function export_queue_alerts()
    {
        try {
            // ตรวจสอบสิทธิ์ - เฉพาะ Staff เท่านั้น
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบด้วยบัญชีเจ้าหน้าที่');
                redirect('User');
                return;
            }

            // โหลด models
            $this->load->model('Queue_model', 'queue_model');

            // ตัวกรองข้อมูล (เดียวกับใน queue_alerts)
            $filters = [
                'priority' => $this->input->get('priority'),
                'status' => $this->input->get('status'),
                'days_min' => $this->input->get('days_min') ?: 3,
                'days_max' => $this->input->get('days_max'),
                'search' => $this->input->get('search')
            ];

            // ดึงข้อมูลทั้งหมด (ไม่มี limit)
            $alerts_result = $this->queue_model->get_queue_alerts_with_filters($filters, 10000, 0);
            $queue_alerts = $alerts_result['data'] ?? [];

            // สร้างชื่อไฟล์
            $filename = "queue_alerts_" . date('Ymd_His') . ".csv";

            // ตั้งค่า headers สำหรับ CSV
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // เปิด output stream
            $output = fopen('php://output', 'w');

            // เพิ่ม BOM สำหรับ UTF-8
            fputs($output, "\xEF\xBB\xBF");

            // หัวข้อคอลัมน์
            $headers = [
                'หมายเลขคิว',
                'หัวข้อ',
                'สถานะ',
                'ผู้จอง',
                'เบอร์ติดต่อ',
                'จำนวนวันที่ค้าง',
                'ระดับความสำคัญ',
                'วันที่สร้าง',
                'วันที่อัพเดทล่าสุด'
            ];
            fputcsv($output, $headers);

            // ข้อมูลแต่ละแถว
            foreach ($queue_alerts as $alert) {
                $days = intval($alert->days_old);
                $priority_label = 'ติดตาม';
                if ($days >= 14) {
                    $priority_label = 'วิกฤต';
                } elseif ($days >= 7) {
                    $priority_label = 'เร่งด่วน';
                }

                $row = [
                    $alert->queue_id,
                    $alert->queue_topic,
                    $alert->queue_status,
                    $alert->queue_by,
                    $alert->queue_phone,
                    $alert->days_old,
                    $priority_label,
                    date('d/m/Y H:i', strtotime($alert->queue_datesave)),
                    $alert->queue_dateupdate ? date('d/m/Y H:i', strtotime($alert->queue_dateupdate)) : 'ไม่มี'
                ];
                fputcsv($output, $row);
            }

            fclose($output);

        } catch (Exception $e) {
            log_message('error', 'Export queue alerts error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการส่งออก Excel', 500);
        }
    }

    /**
     * Export Queue เป็น CSV (รองรับภาษาไทย)
     * URL: /Queue/export_excel หรือ /Queue/export_excel/queue
     * 
     * เพิ่ม function นี้ลงใน Controller: application/controllers/Queue.php
     * ตำแหน่ง: หลังจาก function export_queue_alerts() บรรทัดที่ 6679
     */

    public function export_excel($type = 'queue')
    {
        try {
            // ตรวจสอบสิทธิ์ (optional - ปิดได้หากต้องการให้ทุกคนเข้าถึงได้)
            $m_id = $this->session->userdata('m_id');
            if (!$m_id) {
                // หากต้องการให้ต้อง login ให้ uncomment บรรทัดด้านล่าง
                // $this->session->set_flashdata('error_message', 'กรุณาเข้าสู่ระบบก่อนทำการ Export');
                // redirect('User');
                // return;
            }

            // โหลด Queue Model
            $this->load->model('Queue_model', 'queue_model');

            // ตั้งค่าตัวกรองข้อมูล
            $filters = [];

            // รับค่า filter จาก GET parameters
            $status = $this->input->get('status');
            $date_from = $this->input->get('date_from');
            $date_to = $this->input->get('date_to');
            $search = $this->input->get('search');
            $phone = $this->input->get('phone');
            $queue_id = $this->input->get('queue_id');

            // สร้าง query builder
            $this->db->select('
            queue_id,
            queue_status,
            queue_topic,
            queue_detail,
            queue_by,
            queue_phone,
            queue_number,
            queue_address,
            guest_district,
            guest_amphoe,
            guest_province,
            guest_zipcode,
            queue_date,
            queue_time_slot,
            queue_datesave,
            queue_dateupdate,
            queue_user_type,
            queue_email,
            DATE_FORMAT(queue_datesave, "%d/%m/%Y %H:%i") as created_date_thai,
            DATE_FORMAT(queue_dateupdate, "%d/%m/%Y %H:%i") as updated_date_thai,
            DATE_FORMAT(queue_date, "%d/%m/%Y") as appointment_date_thai
        ');
            $this->db->from('tbl_queue');

            // เพิ่มเงื่อนไขการค้นหา
            if (!empty($status)) {
                $this->db->where('queue_status', $status);
            }

            if (!empty($date_from)) {
                $this->db->where('DATE(queue_datesave) >=', $date_from);
            }

            if (!empty($date_to)) {
                $this->db->where('DATE(queue_datesave) <=', $date_to);
            }

            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('queue_topic', $search);
                $this->db->or_like('queue_detail', $search);
                $this->db->or_like('queue_by', $search);
                $this->db->group_end();
            }

            if (!empty($phone)) {
                $this->db->where('queue_phone', $phone);
            }

            if (!empty($queue_id)) {
                $this->db->where('queue_id', $queue_id);
            }

            // เรียงลำดับ
            $this->db->order_by('queue_datesave', 'DESC');

            // จำกัดจำนวน (ป้องกันการ export ข้อมูลมากเกินไป)
            $limit = $this->input->get('limit') ?: 10000; // default 10,000 records
            $this->db->limit($limit);

            // ดึงข้อมูล
            $query = $this->db->get();
            $queues = $query->result();

            // สร้างชื่อไฟล์
            $filename = "queue_export_" . date('Ymd_His') . ".csv";

            // ตั้งค่า HTTP Headers สำหรับ CSV (รองรับภาษาไทย)
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            // เปิด PHP output stream
            $output = fopen('php://output', 'w');

            // เพิ่ม UTF-8 BOM เพื่อให้ Excel เปิดภาษาไทยได้ถูกต้อง
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // สร้างหัวตาราง (ภาษาไทย)
            $headers = [
                'หมายเลขคิว',
                'สถานะ',
                'หัวข้อเรื่อง',
                'รายละเอียด',
                'ผู้จอง',
                'เบอร์โทรศัพท์',
                'เลขบัตรประชาชน',
                'ที่อยู่',
                'ตำบล',
                'อำเภอ',
                'จังหวัด',
                'รหัสไปรษณีย์',
                'วันที่นัดหมาย',
                'ช่วงเวลา',
                'ประเภทผู้ใช้',
                'อีเมล',
                'วันที่สร้าง',
                'วันที่อัพเดทล่าสุด'
            ];

            // เขียนหัวตาราง
            fputcsv($output, $headers);

            // เขียนข้อมูลแต่ละแถว
            if (!empty($queues)) {
                foreach ($queues as $queue) {
                    // แปลง queue_date และ queue_dateupdate เป็นรูปแบบไทย
                    $queue_date_thai = $queue->appointment_date_thai ?: '-';
                    $created_date = $queue->created_date_thai ?: '-';
                    $updated_date = $queue->updated_date_thai ?: '-';

                    $row = [
                        $queue->queue_id ?: '-',
                        $queue->queue_status ?: '-',
                        $queue->queue_topic ?: '-',
                        $queue->queue_detail ?: '-',
                        $queue->queue_by ?: '-',
                        $queue->queue_phone ?: '-',
                        $queue->queue_number ?: '-',
                        $queue->queue_address ?: '-',
                        $queue->guest_district ?: '-',
                        $queue->guest_amphoe ?: '-',
                        $queue->guest_province ?: '-',
                        $queue->guest_zipcode ?: '-',
                        $queue_date_thai,
                        $queue->queue_time_slot ?: '-',
                        $this->get_user_type_label($queue->queue_user_type),
                        $queue->queue_email ?: '-',
                        $created_date,
                        $updated_date
                    ];

                    fputcsv($output, $row);
                }
            }

            // ปิด output stream
            fclose($output);

            // Log การ export
            log_message('info', 'Queue CSV exported by user: ' . ($m_id ?: 'guest') . ' - Records: ' . count($queues));

            // หยุดการทำงานของ CodeIgniter framework เพื่อป้องกัน output อื่นๆ
            exit;

        } catch (Exception $e) {
            log_message('error', 'Export Excel Error: ' . $e->getMessage());

            // แสดง error แบบเรียบง่าย
            if (ENVIRONMENT === 'development') {
                show_error('เกิดข้อผิดพลาดในการส่งออกข้อมูล: ' . $e->getMessage(), 500);
            } else {
                show_error('เกิดข้อผิดพลาดในการส่งออกข้อมูล กรุณาลองใหม่อีกครั้ง', 500);
            }
        }
    }

    /**
     * Helper function: แปลงประเภทผู้ใช้เป็นภาษาไทย
     */
    private function get_user_type_label($user_type)
    {
        $types = [
            'guest' => 'ผู้ใช้ทั่วไป',
            'public' => 'สมาชิกสาธารณะ',
            'staff' => 'เจ้าหน้าที่'
        ];

        return isset($types[$user_type]) ? $types[$user_type] : $user_type;
    }



}

?>