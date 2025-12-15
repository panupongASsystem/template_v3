<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pages extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // ป้องกันการแคชและการคัดลอกเนื้อหา
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
        $this->output->set_header('Pragma: no-cache');

        // ป้องกันการฝัง iframe
        $this->output->set_header('X-Frame-Options: DENY');

        // ป้องกันการคาดเดา MIME type
        $this->output->set_header('X-Content-Type-Options: nosniff');

        // ป้องกัน XSS attacks
        $this->output->set_header('X-XSS-Protection: 1; mode=block');

        // ป้องกันการเก็บข้อมูลโดย browsers (ใช้ในข้อมูลละเอียดอ่อน)
        $this->output->set_header('Referrer-Policy: same-origin');

        // ป้องกันการดาวน์โหลดไฟล์แทนการแสดงในเบราว์เซอร์
        $this->output->set_header('Content-Disposition: inline');

        // กำหนดเวลาหมดอายุของหน้าเว็บ
        $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        // โหลด models อื่นๆ
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
        $this->load->model('queue_model');
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

        $this->load->model('e_mag_model');
		
		$this->load->model('population_cache_model');


        $this->load->library('vulgar_check');
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

    public function e_mags_view()
    {
        // รับค่า page และ search จาก URL
        $page = (int) $this->input->get('page');
        $search = $this->input->get('search');
        $per_page = 24;

        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $per_page;

        // ถ้ามีการค้นหา
        if (!empty($search)) {
            $e_mags = $this->e_mag_model->search_paginated($search, $per_page, $offset);
            $total_records = $this->e_mag_model->count_search_results($search);
        } else {
            $e_mags = $this->e_mag_model->get_for_frontend_paginated($per_page, $offset);
            $total_records = $this->e_mag_model->count_all_for_frontend();
        }

        // เพิ่ม URL เต็มให้กับข้อมูล
        foreach ($e_mags as &$magazine) {
            $magazine['pdf_url'] = base_url('uploads/e_mags/' . $magazine['file_name']);
            $magazine['cover_url'] = !empty($magazine['cover_image'])
                ? base_url('uploads/e_mags/covers/' . $magazine['cover_image'])
                : base_url('assets/images/default_cover.png');
        }

        // คำนวณ pagination
        $total_pages = ceil($total_records / $per_page);

        $data['e_mags'] = $e_mags;
        $data['current_page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['total_records'] = $total_records;
        $data['per_page'] = $per_page;
        $data['search'] = $search;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/e_mags_view', $data);
        $this->load->view('components/e_mags_modal', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_e_mag_view($id)
    {
        // เรียกใช้ model เพื่อเพิ่มยอดเข้าชม
        $this->e_mag_model->increment_view($id);
    }

    public function personnel($peng)
    {
        $this->load->model('dynamic_position_model');

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

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/personnel', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer');
    }


    private function get_qa_model()
    {
        try {
            if (!isset($this->q_a_model)) {
                $this->load->model('q_a_model');
            }
            return $this->q_a_model;
        } catch (Exception $e) {
            log_message('error', 'Failed to load Q_a_model: ' . $e->getMessage());
            return null;
        }
    }



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



    private function createTopicDeleteNotification($topic_id, $topic_title, $deleted_by)
    {
        try {
            log_message('info', "Creating delete notification for topic {$topic_id}...");

            // ตรวจสอบและโหลด Notification_lib
            $this->ensure_notification_lib();

            // วิธีที่ 1: ใช้ Notification_lib (ถ้ามี)
            if (isset($this->Notification_lib) && method_exists($this->Notification_lib, 'qa_delete')) {
                log_message('info', 'Using Notification_lib->qa_delete method');

                try {
                    $notification_result = $this->Notification_lib->qa_delete($topic_id, $topic_title, $deleted_by);

                    if ($notification_result) {
                        log_message('info', 'SUCCESS: Topic delete notification created via Notification_lib');
                        return true;
                    }
                } catch (Exception $e) {
                    log_message('error', 'EXCEPTION in Notification_lib->qa_delete: ' . $e->getMessage());
                }
            }

            // วิธีที่ 2: สร้างการแจ้งเตือนโดยตรง
            if ($this->db->table_exists('tbl_notification')) {
                log_message('info', 'Creating delete notification directly in database');

                $notification_data = [
                    'title' => 'ลบ', // *** เพิ่ม: ใส่คำว่า "ลบ" ใน title ***
                    'notification_title' => 'ลบกระทู้: ' . $topic_title,
                    'notification_message' => $deleted_by . ' ได้ลบกระทู้ "' . $topic_title . '"',
                    'notification_type' => 'qa_delete',
                    'notification_ref_id' => $topic_id,
                    'notification_from_user' => $deleted_by,
                    'notification_date' => date('Y-m-d H:i:s'),
                    'notification_status' => 'unread'
                ];

                // เพิ่ม fields เพิ่มเติมถ้ามี
                if ($this->db->field_exists('notification_icon', 'tbl_notification')) {
                    $notification_data['notification_icon'] = 'fas fa-trash';
                }

                if ($this->db->field_exists('notification_priority', 'tbl_notification')) {
                    $notification_data['notification_priority'] = 'high';
                }

                $current_user_id = $this->getCurrentUserId();
                if ($current_user_id && $this->db->field_exists('notification_user_id', 'tbl_notification')) {
                    $notification_data['notification_user_id'] = $current_user_id;
                }

                $insert_result = $this->db->insert('tbl_notification', $notification_data);

                if ($insert_result) {
                    log_message('info', 'SUCCESS: Direct delete notification created');
                    return true;
                } else {
                    log_message('error', 'FAILED: Direct delete notification insert failed');
                }
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Exception in createTopicDeleteNotification: ' . $e->getMessage());
            return false;
        }
    }






    public function logout()
    {
        // บันทึก log การ logout
        if ($this->session->userdata('mp_id')) {
            $log_data = array(
                'user_id' => $this->session->userdata('mp_id'),
                'user_type' => 'member_public',  // เพิ่ม user_type
                'action' => 'logout',
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent()
            );
            $this->tax_user_log_model->insert_log($log_data);
        }

        $this->session->unset_userdata(array('mp_id', 'mp_email', 'mp_fname', 'mp_lname', 'mp_phone', 'mp_number'));
        $this->session->set_flashdata('logout_success', TRUE);
        redirect('Home', 'refresh');
    }
    // end login / register ------------------------------------

    public function edit_profile()
    {
        if (!$this->session->userdata('mp_id')) {
            redirect('Pages/form_login');
        }

        $data['user'] = $this->member_public_model->get_member_by_id($this->session->userdata('mp_id'));

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/edit_profile', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer_other');
    }

    public function update_profile()
    {
        $config['upload_path'] = './docs/img/avatar';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);

        $current_user = $this->member_public_model->get_member_by_id($this->session->userdata('mp_id'));

        $data = array(
            'mp_fname' => $this->input->post('mp_fname'),
            'mp_lname' => $this->input->post('mp_lname'),
            'mp_phone' => $this->input->post('mp_phone'),
            'mp_email' => $this->input->post('mp_email'),
            'mp_address' => $this->input->post('mp_address'),
            'mp_number' => $this->input->post('mp_number')
        );

        if (!empty($this->input->post('mp_password'))) {
            $data['mp_password'] = sha1($this->input->post('mp_password'));
        }

        if ($this->upload->do_upload('mp_img')) {
            $upload_data = $this->upload->data();
            $data['mp_img'] = $upload_data['file_name'];
        }

        $update = $this->member_public_model->update_full_profile(
            $this->session->userdata('mp_id'),
            $data,
            $current_user->mp_img
        );

        if ($update) {
            $this->session->set_flashdata('save_success', TRUE);
        }

        redirect('Pages/edit_profile');
    }

    public function get_signboard_details($payment_id)
    {
        $details = $this->tax_model->get_signboard_details($payment_id);

        // ดึง subdomain
        $parts = explode('.', $_SERVER['HTTP_HOST']);
        $subdomain = $parts[0];

        echo json_encode([
            'status' => 'success',
            'details' => $details,
            'subdomain' => $subdomain
        ]);
    }



    public function announce_oap()
    {

        $data['query'] = $this->announce_oap_model->announce_oap_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/announce_oap', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }


    public function announce_oap_detail($announce_oap_id)
    {

        $this->announce_oap_model->increment_view($announce_oap_id);

        $data['rsData'] = $this->announce_oap_model->read($announce_oap_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer_other');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->announce_oap_model->read_pdf($announce_oap_id);
        $data['rsDoc'] = $this->announce_oap_model->read_doc($announce_oap_id);
        $data['rsImg'] = $this->announce_oap_model->read_img($announce_oap_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/announce_oap_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_announce_oap($announce_oap_file_id)
    {
        $this->announce_oap_model->increment_download_announce_oap($announce_oap_file_id);
    }

    public function announce_win()
    {

        $data['query'] = $this->announce_win_model->announce_win_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/announce_win', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function announce_win_detail($announce_win_id)
    {

        $this->announce_win_model->increment_view($announce_win_id);

        $data['rsData'] = $this->announce_win_model->read($announce_win_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->announce_win_model->read_pdf($announce_win_id);
        $data['rsDoc'] = $this->announce_win_model->read_doc($announce_win_id);
        $data['rsImg'] = $this->announce_win_model->read_img($announce_win_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/announce_win_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_announce_win($announce_win_file_id)
    {
        $this->announce_win_model->increment_download_announce_win($announce_win_file_id);
    }

    public function plan_progress()
    {

        $data['query'] = $this->plan_progress_model->plan_progress_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_progress', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_progress_detail($plan_progress_id)
    {

        $this->plan_progress_model->increment_view($plan_progress_id);

        $data['rsData'] = $this->plan_progress_model->read($plan_progress_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer_other');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_progress_model->read_pdf($plan_progress_id);
        $data['rsDoc'] = $this->plan_progress_model->read_doc($plan_progress_id);
        $data['rsImg'] = $this->plan_progress_model->read_img($plan_progress_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_progress_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_progress($plan_progress_file_id)
    {
        $this->plan_progress_model->increment_download_plan_progress($plan_progress_file_id);
    }
    public function ethics_strategy()
    {

        $data['query'] = $this->ethics_strategy_model->ethics_strategy_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/ethics_strategy', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function ethics_strategy_detail($ethics_strategy_id)
    {

        $this->ethics_strategy_model->increment_view($ethics_strategy_id);

        $data['rsData'] = $this->ethics_strategy_model->read($ethics_strategy_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer_other');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->ethics_strategy_model->read_pdf($ethics_strategy_id);
        $data['rsDoc'] = $this->ethics_strategy_model->read_doc($ethics_strategy_id);
        $data['rsImg'] = $this->ethics_strategy_model->read_img($ethics_strategy_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/ethics_strategy_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_ethics_strategy($ethics_strategy_file_id)
    {
        $this->ethics_strategy_model->increment_download_ethics_strategy($ethics_strategy_file_id);
    }
    public function pbsv_statistics()
    {

        $data['query'] = $this->pbsv_statistics_model->pbsv_statistics_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_statistics', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_statistics_detail($pbsv_statistics_id)
    {

        $this->pbsv_statistics_model->increment_view($pbsv_statistics_id);

        $data['rsData'] = $this->pbsv_statistics_model->read($pbsv_statistics_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer_other');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_statistics_model->read_pdf($pbsv_statistics_id);
        $data['rsDoc'] = $this->pbsv_statistics_model->read_doc($pbsv_statistics_id);
        $data['rsImg'] = $this->pbsv_statistics_model->read_img($pbsv_statistics_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_statistics_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_statistics($pbsv_statistics_file_id)
    {
        $this->pbsv_statistics_model->increment_download_pbsv_statistics($pbsv_statistics_file_id);
    }

    public function arevenuec()
    {


        $data['query'] = $this->arevenuec_model->arevenuec_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/arevenuec', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function arevenuec_detail($arevenuec_id)
    {


        $this->arevenuec_model->increment_view($arevenuec_id);

        $data['rsData'] = $this->arevenuec_model->read($arevenuec_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->arevenuec_model->read_pdf($arevenuec_id);
        $data['rsDoc'] = $this->arevenuec_model->read_doc($arevenuec_id);
        $data['rsImg'] = $this->arevenuec_model->read_img($arevenuec_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/arevenuec_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_arevenuec($arevenuec_file_id)
    {
        $this->arevenuec_model->increment_download_arevenuec($arevenuec_file_id);
    }

    public function activity()
    {


        $data['query'] = $this->activity_model->activity_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/activity', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function activity_detail($activity_id)
    {


        $this->activity_model->increment_activity_view($activity_id);

        $data['rsActivity'] = $this->activity_model->read_activity($activity_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsActivity']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }
        $data['rsImg'] = $this->activity_model->read_img_activity_font($activity_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/activity_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function news()
    {

        $data['query'] = $this->news_model->news_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/news', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function news_detail($news_id)
    {

        $data['query'] = $this->news_model->news_frontend_list();

        $this->news_model->increment_view($news_id);

        $data['rsData'] = $this->news_model->read($news_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->news_model->read_pdf($news_id);
        $data['rsDoc'] = $this->news_model->read_doc($news_id);
        $data['rsImg'] = $this->news_model->read_img($news_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/news_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_news($news_file_id)
    {
        $this->news_model->increment_download_news($news_file_id);
    }
    public function order()
    {


        $data['query'] = $this->order_model->order_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/order', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function order_detail($order_id)
    {


        $this->order_model->increment_view($order_id);

        $data['rsData'] = $this->order_model->read($order_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->order_model->read_pdf($order_id);
        $data['rsDoc'] = $this->order_model->read_doc($order_id);
        $data['rsImg'] = $this->order_model->read_img($order_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/order_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_order($order_file_id)
    {
        $this->order_model->increment_download_order($order_file_id);
    }
    public function announce()
    {


        $data['query'] = $this->announce_model->announce_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/announce', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function announce_detail($announce_id)
    {


        $this->announce_model->increment_view($announce_id);

        $data['rsData'] = $this->announce_model->read($announce_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->announce_model->read_pdf($announce_id);
        $data['rsDoc'] = $this->announce_model->read_doc($announce_id);
        $data['rsImg'] = $this->announce_model->read_img($announce_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/announce_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_announce($announce_file_id)
    {
        $this->announce_model->increment_download_announce($announce_file_id);
    }
    public function procurement()
    {


        $data['query'] = $this->procurement_model->procurement_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function procurement_detail($procurement_id)
    {


        $this->procurement_model->increment_view($procurement_id);

        $data['rsData'] = $this->procurement_model->read($procurement_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->procurement_model->read_pdf($procurement_id);
        $data['rsDoc'] = $this->procurement_model->read_doc($procurement_id);
        $data['rsImg'] = $this->procurement_model->read_img($procurement_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_procurement($procurement_file_id)
    {
        $this->procurement_model->increment_download_procurement($procurement_file_id);
    }
    public function mui()
    {


        $data['query'] = $this->mui_model->mui_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/mui', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function mui_detail($mui_id)
    {


        $this->mui_model->increment_view($mui_id);

        $data['rsData'] = $this->mui_model->read($mui_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->mui_model->read_pdf($mui_id);
        $data['rsDoc'] = $this->mui_model->read_doc($mui_id);
        $data['rsImg'] = $this->mui_model->read_img($mui_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/mui_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_mui($mui_file_id)
    {
        $this->mui_model->increment_download_mui($mui_file_id);
    }
    public function guide_work()
    {


        $data['query'] = $this->guide_work_model->guide_work_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/guide_work', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function guide_work_detail($guide_work_id)
    {


        $this->guide_work_model->increment_view($guide_work_id);

        $data['rsData'] = $this->guide_work_model->read($guide_work_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->guide_work_model->read_pdf($guide_work_id);
        $data['rsDoc'] = $this->guide_work_model->read_doc($guide_work_id);
        $data['rsImg'] = $this->guide_work_model->read_img($guide_work_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/guide_work_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_guide_work($guide_work_file_id)
    {
        $this->guide_work_model->increment_download_guide_work($guide_work_file_id);
    }
    public function loadform()
    {


        $data['query'] = $this->loadform_model->loadform_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/loadform', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function loadform_detail($loadform_id)
    {


        $this->loadform_model->increment_view($loadform_id);

        $data['rsData'] = $this->loadform_model->read($loadform_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->loadform_model->read_pdf($loadform_id);
        $data['rsDoc'] = $this->loadform_model->read_doc($loadform_id);
        $data['rsImg'] = $this->loadform_model->read_img($loadform_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/loadform_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_loadform($loadform_file_id)
    {
        $this->loadform_model->increment_download_loadform($loadform_file_id);
    }
    public function pppw()
    {


        $data['query'] = $this->pppw_model->pppw_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pppw', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pppw_detail($pppw_id)
    {


        $this->pppw_model->increment_view($pppw_id);

        $data['rsData'] = $this->pppw_model->read($pppw_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pppw_model->read_pdf($pppw_id);
        $data['rsDoc'] = $this->pppw_model->read_doc($pppw_id);
        $data['rsImg'] = $this->pppw_model->read_img($pppw_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pppw_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pppw($pppw_file_id)
    {
        $this->pppw_model->increment_download_pppw($pppw_file_id);
    }
    public function egp()
    {
        // // URL of the Open API
        // $api_url = 'https://opend.data.go.th/govspending/cgdcontract?api-key=TH3JFBwJZlaXdDCpcVfSFGuoofCJ1heX&year=2566&dept_code=6320120&budget_start=0&budget_end=1000000000&offset=0&limit=500&keyword=&winner_tin=';

        // // Fetch data from the API
        // $api_data = file_get_contents($api_url);

        // // Check if data is fetched successfully
        // if ($api_data !== FALSE) {
        // 	// Decode the JSON data
        // 	$json_data = json_decode($api_data, TRUE);

        // 	// Check if JSON decoding is successful
        // 	if ($json_data !== NULL) {
        // 		// Pass JSON data to the view
        // 		$data['json_data'] = $json_data;


        $data['q2567'] = $this->intra_egp_model->egp_y2567();
        $data['q2566'] = $this->intra_egp_model->egp_y2566();
        $data['q2565'] = $this->intra_egp_model->egp_y2565();
        $data['q2564'] = $this->intra_egp_model->egp_y2564();
        $data['q2563'] = $this->intra_egp_model->egp_y2563();


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/e_gp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
        // 	}
        // }
    }
    public function otop()
    {


        $otops = $this->otop_model->get_otops();

        foreach ($otops as $otop) {
            $otop->images = $this->otop_model->get_images_for_otop($otop->otop_id);
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/otop', ['otops' => $otops]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function history()
    {


        $history = $this->history_model->list_all();

        foreach ($history as $pdf) {
            $pdf->pdf = $this->history_model->list_all_pdf($pdf->history_id);
        }
        foreach ($history as $img) {
            $img->img = $this->history_model->list_all_img($img->history_id);
        }

        $this->history_model->increment_view();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/history', ['history' => $history]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_history($history_pdf_id)
    {
        $this->history_model->increment_download_history($history_pdf_id);
    }

    public function vision()
    {


        $vision = $this->vision_model->list_all();

        foreach ($vision as $pdf) {
            $pdf->pdf = $this->vision_model->list_all_pdf($pdf->vision_id);
        }
        foreach ($vision as $img) {
            $img->img = $this->vision_model->list_all_img($img->vision_id);
        }

        $this->vision_model->increment_view();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/vision', ['vision' => $vision]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_vision($vision_pdf_id)
    {
        $this->vision_model->increment_download_vision($vision_pdf_id);
    }
    public function gci()
    {


        $gci = $this->gci_model->list_all();

        foreach ($gci as $pdf) {
            $pdf->pdf = $this->gci_model->list_all_pdf($pdf->gci_id);
        }
        foreach ($gci as $img) {
            $img->img = $this->gci_model->list_all_img($img->gci_id);
        }

        $this->gci_model->increment_view();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/gci', ['gci' => $gci]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_gci($gci_pdf_id)
    {
        $this->gci_model->increment_download_gci($gci_pdf_id);
    }

    public function authority()
    {


        $authority = $this->authority_model->list_all();

        foreach ($authority as $pdf) {
            $pdf->pdf = $this->authority_model->list_all_pdf($pdf->authority_id);
        }
        foreach ($authority as $img) {
            $img->img = $this->authority_model->list_all_img($img->authority_id);
        }

        $this->authority_model->increment_view();


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/authority', ['authority' => $authority]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_authority($authority_pdf_id)
    {
        $this->authority_model->increment_download_authority($authority_pdf_id);
    }

    public function mission()
    {


        $mission = $this->mission_model->list_all();

        foreach ($mission as $pdf) {
            $pdf->pdf = $this->mission_model->list_all_pdf($pdf->mission_id);
        }
        foreach ($mission as $img) {
            $img->img = $this->mission_model->list_all_img($img->mission_id);
        }

        $this->mission_model->increment_view();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/mission', ['mission' => $mission]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_mission($mission_pdf_id)
    {
        $this->mission_model->increment_download_mission($mission_pdf_id);
    }
    public function motto()
    {


        $data['qMotto'] = $this->motto_model->motto_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/motto', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    /**
     * 🆕 แสดงหน้าข้อมูลชุมชน - ปรับให้รองรับ Manual/API Mode + Cache
     */
    public function ci()
    {
        // อ่านค่า config แหล่งข้อมูล
        $ci_data_source = get_config_value('ci_data_source') ?: 'manual';
        
        log_message('info', 'CI Page - Data source mode: ' . $ci_data_source);

        // ✅ ถ้าเป็นโหมด Manual → ใช้ข้อมูลจาก Database
        if ($ci_data_source === 'manual') {
            $this->ci_manual_mode();
            return;
        }

        // ✅ ถ้าเป็นโหมด API → ใช้ข้อมูลจาก API + Cache
        $this->ci_api_mode();
    }

    /**
     * โหมด Manual - แสดงข้อมูลจาก Database (ไม่เปลี่ยน)
     */
    private function ci_manual_mode()
    {
        log_message('info', 'CI Page - Using MANUAL mode (database)');

        $data['qCi'] = $this->cmi_model->ci_frontend();
        $data['data_source'] = 'database';
        $data['selected_yymm'] = null;

        if ($this->input->is_ajax_request()) {
            log_message('info', 'CI Page - Returning AJAX response (manual mode)');
            header('Content-Type: application/json');
            echo json_encode($data);
            return;
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/ci', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    /**
     * 🆕 โหมด API - ดึงจาก API ก่อน → บันทึก DB → Fallback
     */
    private function ci_api_mode()
    {
        log_message('info', 'CI Page - Using API mode (API First with Cache)');

        // รับค่าเดือน-ปี
        $selected_yymm = $this->input->get('yymm');

        if (!$selected_yymm) {
            $current_year = (int)date('Y') + 543;
            $current_month = (int)date('m');
            $current_month--;
            if ($current_month < 1) {
                $current_month = 12;
                $current_year--;
            }
            $selected_yymm = substr($current_year, -2) . str_pad($current_month, 2, '0', STR_PAD_LEFT);
            log_message('info', 'CI Population - Default YYMM selected: ' . $selected_yymm);
        } else {
            log_message('info', 'CI Population - User selected YYMM: ' . $selected_yymm);
        }

        // ⭐ เตรียมข้อมูล location codes
        $province_name = get_config_value('province');
        $district_name = get_config_value('district');
        $subdistric_name = get_config_value('subdistric');
        $zip_code = get_config_value('zip_code');

        log_message('info', 'CI Population - Location: ' . $province_name . '/' . $district_name . '/' . $subdistric_name);

        // หารหัสที่อยู่
        $location_codes = $this->get_location_codes_for_population(
            $subdistric_name,
            $district_name,
            $province_name,
            $zip_code
        );

        if (!$location_codes) {
            log_message('error', 'CI Population - Failed to get location codes, using manual database');
            $data['qCi'] = $this->cmi_model->ci_frontend();
            $data['data_source'] = 'database_manual_no_codes';
            $data['selected_yymm'] = $selected_yymm;
            $this->output_view($data);
            return;
        }

        $province_code = $location_codes['province_code'];
        $district_code = $location_codes['district_code'];
        $subdistric_code = $location_codes['subdistric_code'];

        // ⭐ 1. ลองเรียก DOPA API ก่อนเสมอ
        log_message('info', 'CI Population - Step 1: Calling DOPA API for YYMM: ' . $selected_yymm);
        $api_data = $this->call_population_api($selected_yymm, $province_code, $district_code, $subdistric_code);

        if (!empty($api_data)) {
            // ✅ API สำเร็จ → บันทึกลง Database
            log_message('info', 'CI Population - Step 2: API success (' . count($api_data) . ' records), saving to database');
            
            $save_result = $this->population_cache_model->save_api_data(
                $selected_yymm,
                $api_data,
                $province_code,
                $district_code,
                $subdistric_code
            );

            if ($save_result) {
                log_message('info', 'CI Population - Step 3: Successfully saved to database');
            } else {
                log_message('warning', 'CI Population - Step 3: Failed to save to database (but API data is valid)');
            }

            // ส่งข้อมูลจาก API
            $data['qCi'] = $api_data;
            $data['data_source'] = 'api';
            $data['selected_yymm'] = $selected_yymm;
            $data['saved_to_db'] = $save_result;
            $this->output_view($data);
            return;
        }

        // ⚠️ API ล้มเหลว → ลอง Fallback to Database Cache
        log_message('warning', 'CI Population - Step 2: API failed, trying database cache');
        
        $cached_data = $this->population_cache_model->get_cached_data(
            $selected_yymm,
            $province_code,
            $district_code,
            $subdistric_code
        );

        if ($cached_data !== false) {
            // ✅ มีข้อมูลใน Cache
            log_message('info', 'CI Population - Step 3: Found ' . count($cached_data) . ' records in database cache');
            $data['qCi'] = $cached_data;
            $data['data_source'] = 'database_cache';
            $data['selected_yymm'] = $selected_yymm;
            $this->output_view($data);
            return;
        }

        // ❌ ทั้ง API และ Cache ล้มเหลว → ใช้ Manual Database
        log_message('error', 'CI Population - Step 3: Both API and cache failed, using manual database');
        $data['qCi'] = $this->cmi_model->ci_frontend();
        $data['data_source'] = 'database_manual_fallback';
        $data['selected_yymm'] = $selected_yymm;
        $this->output_view($data);
    }

    /**
     * Helper: แสดงผล view
     */
    private function output_view($data)
    {
        if ($this->input->is_ajax_request()) {
            log_message('info', 'CI Population - Returning AJAX response');
            header('Content-Type: application/json');
            echo json_encode($data);
            return;
        }

        log_message('info', 'CI Population - Loading normal view');
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/ci', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    /**
     * เรียก Population API (รับ parameters แทน)
     */
    private function call_population_api($yymm, $cc, $rcode, $tt)
    {
        log_message('info', 'API Call - Parameters: cc=' . $cc . ', rcode=' . $rcode . ', tt=' . $tt . ', yymm=' . $yymm);

        $api_url = "https://stat.bora.dopa.go.th/stat/statnew/connectSAPI/stat_forward.php?API=/api/statpophouse/v1/statpop/list?action=45&yymmBegin={$yymm}&yymmEnd={$yymm}&statType=0&statSubType=999&subType=99&cc={$cc}&rcode={$rcode}&tt={$tt}";

        log_message('info', 'API Call - URL: ' . $api_url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        log_message('info', 'API Response - HTTP Code: ' . $http_code);

        if ($http_code != 200 || empty($response)) {
            if (!empty($curl_error)) {
                log_message('error', 'API Call - CURL Error: ' . $curl_error);
            }
            log_message('error', 'API Call - Failed with HTTP code: ' . $http_code);
            return [];
        }

        log_message('debug', 'API Response - Raw data: ' . substr($response, 0, 500));

        $data = json_decode($response, true);

        if (empty($data)) {
            log_message('error', 'API Call - JSON decode failed or empty data');
            log_message('debug', 'API Call - JSON Error: ' . json_last_error_msg());
            return [];
        }

        log_message('info', 'API Call - Successfully decoded ' . count($data) . ' records');

        // แปลงข้อมูล
        $result = [];
        foreach ($data as $item) {
            $obj = new stdClass();
            
            // ข้อมูลพื้นฐาน
            $obj->ci_name = $item['lsmmDesc'];
            $obj->ci_home = '-';
            
            // ข้อมูลไทย
            $obj->male_thai = (int)$item['lssumtotMaleThai'];
            $obj->female_thai = (int)$item['lssumtotFemaleThai'];
            $obj->total_thai = (int)$item['lssumtotTotThai'];
            
            // ข้อมูลทั้งหมด
            $obj->male_all = (int)$item['lssumtotMale'];
            $obj->female_all = (int)$item['lssumtotFemale'];
            $obj->total_all = (int)$item['lssumtotTot'];
            
            // คำนวณต่างชาติ
            $obj->male_foreign = $obj->male_all - $obj->male_thai;
            $obj->female_foreign = $obj->female_all - $obj->female_thai;
            $obj->total_foreign = $obj->male_foreign + $obj->female_foreign;
            
            // Backward compatibility
            $obj->ci_man = $obj->male_thai;
            $obj->ci_woman = $obj->female_thai;
            $obj->ci_total = $obj->total_thai;
            
            // ⭐ เก็บข้อมูลดิบ (สำหรับบันทึก DB)
            $obj->lsmmDesc = $item['lsmmDesc'];
            $obj->lsmmCode = isset($item['lsmmCode']) ? $item['lsmmCode'] : null;
            $obj->lssumtotMaleThai = $item['lssumtotMaleThai'];
            $obj->lssumtotFemaleThai = $item['lssumtotFemaleThai'];
            $obj->lssumtotTotThai = $item['lssumtotTotThai'];
            $obj->lssumtotMale = $item['lssumtotMale'];
            $obj->lssumtotFemale = $item['lssumtotFemale'];
            $obj->lssumtotTot = $item['lssumtotTot'];

            $result[] = $obj;
        }

        log_message('info', 'API Call - Data transformation completed with ' . count($result) . ' villages');

        return $result;
    }

    /**
     * หารหัสที่อยู่จากชื่อ (เหมือนเดิม)
     */
    private function get_location_codes_for_population($subdistric, $district, $province, $zip_code)
    {
        log_message('info', 'Getting location codes for population API');
        
        $province_code = $this->get_province_code_by_name($province);
        if (!$province_code) {
            log_message('error', 'Province code not found for: ' . $province);
            return null;
        }
        
        if (!$zip_code || strlen($zip_code) != 5) {
            log_message('error', 'Invalid zipcode: ' . $zip_code);
            return null;
        }
        
        $api_url = "https://addr.assystem.co.th/index.php/zip_api/address/" . $zip_code;
        
        $ch = curl_init($api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code != 200) {
            log_message('error', 'Address API returned HTTP ' . $http_code);
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['status']) || $data['status'] !== 'success' || !isset($data['data'])) {
            log_message('error', 'Address API returned invalid data');
            return null;
        }
        
        $district_code = null;
        foreach ($data['data'] as $item) {
            if (isset($item['amphoe_name']) && $this->compare_thai_names($item['amphoe_name'], $district)) {
                $district_code = $item['amphoe_code'];
                log_message('info', 'District code found: ' . $district_code);
                break;
            }
        }
        
        $subdistric_code = null;
        foreach ($data['data'] as $item) {
            if (isset($item['district_name']) && $this->compare_thai_names($item['district_name'], $subdistric)) {
                $subdistric_code = $item['district_code'];
                log_message('info', 'Subdistric code found: ' . $subdistric_code);
                break;
            }
        }
        
        if (!$district_code || !$subdistric_code) {
            log_message('error', 'Missing codes');
            return null;
        }
        
        return [
            'province_code' => $province_code,
            'district_code' => $district_code,
            'subdistric_code' => $subdistric_code
        ];
    }

    /**
     * หารหัสจังหวัด (Hardcoded - เหมือนเดิม)
     */
    private function get_province_code_by_name($province_name)
    {
        $provinces = [
            '10' => ['กรุงเทพมหานคร', 'กทม', 'Bangkok'],
            '11' => ['สมุทรปราการ', 'Samut Prakan'],
            '12' => ['นนทบุรี', 'Nonthaburi'],
            '13' => ['ปทุมธานี', 'Pathum Thani'],
            '14' => ['พระนครศรีอยุธยา', 'อยุธยา', 'Phra Nakhon Si Ayutthaya'],
            '15' => ['อ่างทอง', 'Ang Thong'],
            '16' => ['ลพบุรี', 'Lopburi'],
            '17' => ['สิงห์บุรี', 'Sing Buri'],
            '18' => ['ชัยนาท', 'Chai Nat'],
            '19' => ['สระบุรี', 'Saraburi'],
            '20' => ['ชลบุรี', 'Chonburi'],
            '21' => ['ระยอง', 'Rayong'],
            '22' => ['จันทบุรี', 'Chanthaburi'],
            '23' => ['ตราด', 'Trat'],
            '24' => ['ฉะเชิงเทรา', 'Chachoengsao'],
            '25' => ['ปราจีนบุรี', 'Prachin Buri'],
            '26' => ['นครนายก', 'Nakhon Nayok'],
            '27' => ['สระแก้ว', 'Sa Kaeo'],
            '30' => ['นครราชสีมา', 'โคราช', 'Nakhon Ratchasima'],
            '31' => ['บุรีรัมย์', 'Buriram'],
            '32' => ['สุรินทร์', 'Surin'],
            '33' => ['ศีสะเกษ', 'Sisaket'],
            '34' => ['อุบลราชธานี', 'Ubon Ratchathani'],
            '35' => ['ยโสธร', 'Yasothon'],
            '36' => ['ชัยภูมิ', 'Chaiyaphum'],
            '37' => ['อำนาจเจริญ', 'Amnat Charoen'],
            '38' => ['บึงกาฬ', 'Bueng Kan'],
            '39' => ['หนองบัวลำภู', 'Nong Bua Lam Phu'],
            '40' => ['ขอนแก่น', 'Khon Kaen'],
            '41' => ['อุดรธานี', 'Udon Thani'],
            '42' => ['เลย', 'Loei'],
            '43' => ['หนองคาย', 'Nong Khai'],
            '44' => ['มหาสารคาม', 'Maha Sarakham'],
            '45' => ['ร้อยเอ็ด', 'Roi Et'],
            '46' => ['กาฬสินธุ์', 'Kalasin'],
            '47' => ['สกลนคร', 'Sakon Nakhon'],
            '48' => ['นครพนม', 'Nakhon Phanom'],
            '49' => ['มุกดาหาร', 'Mukdahan'],
            '50' => ['เชียงใหม่', 'Chiang Mai'],
            '51' => ['ลำพูน', 'Lamphun'],
            '52' => ['ลำปาง', 'Lampang'],
            '53' => ['อุตรดิตถ์', 'Uttaradit'],
            '54' => ['แพร่', 'Phrae'],
            '55' => ['น่าน', 'Nan'],
            '56' => ['พะเยา', 'Phayao'],
            '57' => ['เชียงราย', 'Chiang Rai'],
            '58' => ['แม่ฮ่องสอน', 'Mae Hong Son'],
            '60' => ['นครสวรรค์', 'Nakhon Sawan'],
            '61' => ['อุทัยธานี', 'Uthai Thani'],
            '62' => ['กำแพงเพชร', 'Kamphaeng Phet'],
            '63' => ['ตาก', 'Tak'],
            '64' => ['สุโขทัย', 'Sukhothai'],
            '65' => ['พิษณุโลก', 'Phitsanulok'],
            '66' => ['พิจิตร', 'Phichit'],
            '67' => ['เพชรบูรณ์', 'Phetchabun'],
            '70' => ['ราชบุรี', 'Ratchaburi'],
            '71' => ['กาญจนบุรี', 'Kanchanaburi'],
            '72' => ['สุพรรณบุรี', 'Suphan Buri'],
            '73' => ['นครปฐม', 'Nakhon Pathom'],
            '74' => ['สมุทรสาคร', 'Samut Sakhon'],
            '75' => ['สมุทรสงคราม', 'Samut Songkhram'],
            '76' => ['เพชรบุรี', 'Phetchaburi'],
            '77' => ['ประจวบคีรีขันธ์', 'Prachuap Khiri Khan'],
            '80' => ['นครศรีธรรมราช', 'Nakhon Si Thammarat'],
            '81' => ['กระบี่', 'Krabi'],
            '82' => ['พังงา', 'Phang Nga'],
            '83' => ['ภูเก็ต', 'Phuket'],
            '84' => ['สุราษฎร์ธานี', 'Surat Thani'],
            '85' => ['ระนอง', 'Ranong'],
            '86' => ['ชุมพร', 'Chumphon'],
            '90' => ['สงขลา', 'Songkhla'],
            '91' => ['สตูล', 'Satun'],
            '92' => ['ตรัง', 'Trang'],
            '93' => ['พัทลุง', 'Phatthalung'],
            '94' => ['ปัตตานี', 'Pattani'],
            '95' => ['ยะลา', 'Yala'],
            '96' => ['นราธิวาส', 'Narathiwat']
        ];
        
        foreach ($provinces as $code => $names) {
            foreach ($names as $name) {
                if ($this->compare_thai_names($name, $province_name)) {
                    return $code;
                }
            }
        }
        
        return null;
    }

    /**
     * เปรียบเทียบชื่อ (เหมือนเดิม)
     */
    private function compare_thai_names($name1, $name2)
    {
        $clean1 = mb_strtolower(trim($name1));
        $clean2 = mb_strtolower(trim($name2));
        return $clean1 === $clean2;
    }


    public function executivepolicy()
    {


        $executivepolicy = $this->executivepolicy_model->list_all();

        foreach ($executivepolicy as $pdf) {
            $pdf->pdf = $this->executivepolicy_model->list_all_pdf($pdf->executivepolicy_id);
        }
        foreach ($executivepolicy as $img) {
            $img->img = $this->executivepolicy_model->list_all_img($img->executivepolicy_id);
        }

        $data['qExecutivepolicy'] = $this->executivepolicy_model->executivepolicy_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/executivepolicy', ['executivepolicy' => $executivepolicy]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_executivepolicy($executivepolicy_pdf_id)
    {
        $this->executivepolicy_model->increment_download_executivepolicy($executivepolicy_pdf_id);
    }
    public function msg_pres()
    {




        $this->msg_pres_model->increment_view();

        $msg_pres = $this->msg_pres_model->list_all();

        foreach ($msg_pres as $pdf) {
            $pdf->pdf = $this->msg_pres_model->list_all_pdf($pdf->msg_pres_id);
        }
        foreach ($msg_pres as $img) {
            $img->img = $this->msg_pres_model->list_all_img($img->msg_pres_id);
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/msg_pres', ['msg_pres' => $msg_pres]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_msg_pres($msg_pres_id)
    {
        $this->msg_pres_model->increment_download_msg_pres($msg_pres_id);
    }
    public function travel()
    {


        $data['query'] = $this->travel_model->travel_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/travel', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function travel_detail($travel_id)
    {


        $this->travel_model->increment_travel_view($travel_id);

        $data['rsTravel'] = $this->travel_model->read_travel($travel_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsTravel']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsImg'] = $this->travel_model->read_img_travel_font($travel_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/travel_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function si()
    {


        $si = $this->si_model->list_all();

        foreach ($si as $pdf) {
            $pdf->pdf = $this->si_model->list_all_pdf($pdf->si_id);
        }
        foreach ($si as $img) {
            $img->img = $this->si_model->list_all_img($img->si_id);
        }

        $this->si_model->increment_view();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/si', ['si' => $si]);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_si($si_pdf_id)
    {
        $this->si_model->increment_download_si($si_pdf_id);
    }
    public function canon_bgps()
    {


        $data['query'] = $this->canon_bgps_model->canon_bgps_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_bgps', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function canon_bgps_detail($canon_bgps_id)
    {


        $this->canon_bgps_model->increment_view($canon_bgps_id);

        $data['rsData'] = $this->canon_bgps_model->read($canon_bgps_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->canon_bgps_model->read_pdf($canon_bgps_id);
        $data['rsDoc'] = $this->canon_bgps_model->read_doc($canon_bgps_id);
        $data['rsImg'] = $this->canon_bgps_model->read_img($canon_bgps_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_bgps_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_canon_bgps($canon_bgps_file_id)
    {
        $this->canon_bgps_model->increment_download_canon_bgps($canon_bgps_file_id);
    }
    public function canon_chh()
    {


        $data['query'] = $this->canon_chh_model->canon_chh_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_chh', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function canon_chh_detail($canon_chh_id)
    {


        $this->canon_chh_model->increment_view($canon_chh_id);

        $data['rsData'] = $this->canon_chh_model->read($canon_chh_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->canon_chh_model->read_pdf($canon_chh_id);
        $data['rsDoc'] = $this->canon_chh_model->read_doc($canon_chh_id);
        $data['rsImg'] = $this->canon_chh_model->read_img($canon_chh_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_chh_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_canon_chh($canon_chh_file_id)
    {
        $this->canon_chh_model->increment_download_canon_chh($canon_chh_file_id);
    }
    public function canon_ritw()
    {


        $data['query'] = $this->canon_ritw_model->canon_ritw_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_ritw', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function canon_ritw_detail($canon_ritw_id)
    {


        $this->canon_ritw_model->increment_view($canon_ritw_id);

        $data['rsData'] = $this->canon_ritw_model->read($canon_ritw_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->canon_ritw_model->read_pdf($canon_ritw_id);
        $data['rsDoc'] = $this->canon_ritw_model->read_doc($canon_ritw_id);
        $data['rsImg'] = $this->canon_ritw_model->read_img($canon_ritw_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_ritw_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_canon_ritw($canon_ritw_file_id)
    {
        $this->canon_ritw_model->increment_download_canon_ritw($canon_ritw_file_id);
    }
    public function canon_market()
    {


        $data['query'] = $this->canon_market_model->canon_market_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_market', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function canon_market_detail($canon_market_id)
    {


        $this->canon_market_model->increment_view($canon_market_id);

        $data['rsData'] = $this->canon_market_model->read($canon_market_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->canon_market_model->read_pdf($canon_market_id);
        $data['rsDoc'] = $this->canon_market_model->read_doc($canon_market_id);
        $data['rsImg'] = $this->canon_market_model->read_img($canon_market_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_market_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_canon_market($canon_market_file_id)
    {
        $this->canon_market_model->increment_download_canon_market($canon_market_file_id);
    }
    public function canon_rmwp()
    {


        $data['query'] = $this->canon_rmwp_model->canon_rmwp_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_rmwp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function canon_rmwp_detail($canon_rmwp_id)
    {


        $this->canon_rmwp_model->increment_view($canon_rmwp_id);

        $data['rsData'] = $this->canon_rmwp_model->read($canon_rmwp_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->canon_rmwp_model->read_pdf($canon_rmwp_id);
        $data['rsDoc'] = $this->canon_rmwp_model->read_doc($canon_rmwp_id);
        $data['rsImg'] = $this->canon_rmwp_model->read_img($canon_rmwp_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_rmwp_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_canon_rmwp($canon_rmwp_file_id)
    {
        $this->canon_rmwp_model->increment_download_canon_rmwp($canon_rmwp_file_id);
    }
    public function canon_rcsp()
    {


        $data['query'] = $this->canon_rcsp_model->canon_rcsp_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_rcsp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function canon_rcsp_detail($canon_rcsp_id)
    {


        $this->canon_rcsp_model->increment_view($canon_rcsp_id);

        $data['rsData'] = $this->canon_rcsp_model->read($canon_rcsp_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->canon_rcsp_model->read_pdf($canon_rcsp_id);
        $data['rsDoc'] = $this->canon_rcsp_model->read_doc($canon_rcsp_id);
        $data['rsImg'] = $this->canon_rcsp_model->read_img($canon_rcsp_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_rcsp_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_canon_rcsp($canon_rcsp_file_id)
    {
        $this->canon_rcsp_model->increment_download_canon_rcsp($canon_rcsp_file_id);
    }
    public function canon_rcp()
    {


        $data['query'] = $this->canon_rcp_model->canon_rcp_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_rcp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function canon_rcp_detail($canon_rcp_id)
    {


        $this->canon_rcp_model->increment_view($canon_rcp_id);

        $data['rsData'] = $this->canon_rcp_model->read($canon_rcp_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->canon_rcp_model->read_pdf($canon_rcp_id);
        $data['rsDoc'] = $this->canon_rcp_model->read_doc($canon_rcp_id);
        $data['rsImg'] = $this->canon_rcp_model->read_img($canon_rcp_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/canon_rcp_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_canon_rcp($canon_rcp_file_id)
    {
        $this->canon_rcp_model->increment_download_canon_rcp($canon_rcp_file_id);
    }
    public function plan_pdl()
    {


        $data['query'] = $this->plan_pdl_model->plan_pdl_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pdl', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pdl_detail($plan_pdl_id)
    {


        $this->plan_pdl_model->increment_view($plan_pdl_id);

        $data['rsData'] = $this->plan_pdl_model->read($plan_pdl_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pdl_model->read_pdf($plan_pdl_id);
        $data['rsDoc'] = $this->plan_pdl_model->read_doc($plan_pdl_id);
        $data['rsImg'] = $this->plan_pdl_model->read_img($plan_pdl_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pdl_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pdl($plan_pdl_file_id)
    {
        $this->plan_pdl_model->increment_download_plan_pdl($plan_pdl_file_id);
    }
    public function plan_pc3y()
    {


        $data['query'] = $this->plan_pc3y_model->plan_pc3y_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pc3y', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pc3y_detail($pc3y_id)
    {


        $this->plan_pc3y_model->increment_view($pc3y_id);

        $data['rsData'] = $this->plan_pc3y_model->read($pc3y_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pc3y_model->read_pdf($pc3y_id);
        $data['rsDoc'] = $this->plan_pc3y_model->read_doc($pc3y_id);
        $data['rsImg'] = $this->plan_pc3y_model->read_img($pc3y_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pc3y_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pc3y($pc3y_file_id)
    {
        $this->plan_pc3y_model->increment_download_plan_pc3y($pc3y_file_id);
    }
    public function plan_pds3y()
    {


        $data['query'] = $this->plan_pds3y_model->plan_pds3y_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pds3y', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pds3y_detail($plan_pds3y_id)
    {


        $this->plan_pds3y_model->increment_view($plan_pds3y_id);

        $data['rsData'] = $this->plan_pds3y_model->read($plan_pds3y_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pds3y_model->read_pdf($plan_pds3y_id);
        $data['rsDoc'] = $this->plan_pds3y_model->read_doc($plan_pds3y_id);
        $data['rsImg'] = $this->plan_pds3y_model->read_img($plan_pds3y_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pds3y_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pds3y($plan_pds3y_file_id)
    {
        $this->plan_pds3y_model->increment_download_plan_pds3y($plan_pds3y_file_id);
    }
    public function plan_pdpa()
    {


        $data['query'] = $this->plan_pdpa_model->plan_pdpa_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pdpa', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pdpa_detail($plan_pdpa_id)
    {


        $this->plan_pdpa_model->increment_view($plan_pdpa_id);

        $data['rsData'] = $this->plan_pdpa_model->read($plan_pdpa_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pdpa_model->read_pdf($plan_pdpa_id);
        $data['rsDoc'] = $this->plan_pdpa_model->read_doc($plan_pdpa_id);
        $data['rsImg'] = $this->plan_pdpa_model->read_img($plan_pdpa_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pdpa_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pdpa($plan_pdpa_file_id)
    {
        $this->plan_pdpa_model->increment_download_plan_pdpa($plan_pdpa_file_id);
    }
    public function plan_dpy()
    {


        $data['query'] = $this->plan_dpy_model->plan_dpy_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_dpy', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_dpy_detail($plan_dpy_id)
    {


        $this->plan_dpy_model->increment_view($plan_dpy_id);

        $data['rsData'] = $this->plan_dpy_model->read($plan_dpy_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_dpy_model->read_pdf($plan_dpy_id);
        $data['rsDoc'] = $this->plan_dpy_model->read_doc($plan_dpy_id);
        $data['rsImg'] = $this->plan_dpy_model->read_img($plan_dpy_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_dpy_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_dpy($plan_dpy_file_id)
    {
        $this->plan_dpy_model->increment_download_plan_dpy($plan_dpy_file_id);
    }
    public function plan_poa()
    {


        $data['query'] = $this->plan_poa_model->plan_poa_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_poa', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_poa_detail($plan_poa_id)
    {


        $this->plan_poa_model->increment_view($plan_poa_id);

        $data['rsData'] = $this->plan_poa_model->read($plan_poa_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_poa_model->read_pdf($plan_poa_id);
        $data['rsDoc'] = $this->plan_poa_model->read_doc($plan_poa_id);
        $data['rsImg'] = $this->plan_poa_model->read_img($plan_poa_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_poa_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_poa($plan_poa_file_id)
    {
        $this->plan_poa_model->increment_download_plan_poa($plan_poa_file_id);
    }
    public function plan_pcra()
    {


        $data['query'] = $this->plan_pcra_model->plan_pcra_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pcra', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pcra_detail($plan_pcra_id)
    {


        $this->plan_pcra_model->increment_view($plan_pcra_id);

        $data['rsData'] = $this->plan_pcra_model->read($plan_pcra_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pcra_model->read_pdf($plan_pcra_id);
        $data['rsDoc'] = $this->plan_pcra_model->read_doc($plan_pcra_id);
        $data['rsImg'] = $this->plan_pcra_model->read_img($plan_pcra_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pcra_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pcra($plan_pcra_file_id)
    {
        $this->plan_pcra_model->increment_download_plan_pcra($plan_pcra_file_id);
    }
    public function plan_pdm()
    {


        $data['query'] = $this->plan_pdm_model->plan_pdm_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pdm', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pdm_detail($plan_pdm_id)
    {


        $this->plan_pdm_model->increment_view($plan_pdm_id);

        $data['rsData'] = $this->plan_pdm_model->read($plan_pdm_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pdm_model->read_pdf($plan_pdm_id);
        $data['rsDoc'] = $this->plan_pdm_model->read_doc($plan_pdm_id);
        $data['rsImg'] = $this->plan_pdm_model->read_img($plan_pdm_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pdm_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pdm($plan_pdm_file_id)
    {
        $this->plan_pdm_model->increment_download_plan_pdm($plan_pdm_file_id);
    }
    public function plan_pop()
    {


        $data['query'] = $this->plan_pop_model->plan_pop_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pop', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pop_detail($plan_pop_id)
    {


        $this->plan_pop_model->increment_view($plan_pop_id);

        $data['rsData'] = $this->plan_pop_model->read($plan_pop_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pop_model->read_pdf($plan_pop_id);
        $data['rsDoc'] = $this->plan_pop_model->read_doc($plan_pop_id);
        $data['rsImg'] = $this->plan_pop_model->read_img($plan_pop_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pop_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pop($plan_pop_file_id)
    {
        $this->plan_pop_model->increment_download_plan_pop($plan_pop_file_id);
    }
    public function plan_paca()
    {


        $data['query'] = $this->plan_paca_model->plan_paca_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_paca', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_paca_detail($plan_paca_id)
    {


        $this->plan_paca_model->increment_view($plan_paca_id);

        $data['rsData'] = $this->plan_paca_model->read($plan_paca_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_paca_model->read_pdf($plan_paca_id);
        $data['rsDoc'] = $this->plan_paca_model->read_doc($plan_paca_id);
        $data['rsImg'] = $this->plan_paca_model->read_img($plan_paca_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_paca_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_paca($plan_paca_file_id)
    {
        $this->plan_paca_model->increment_download_plan_paca($plan_paca_file_id);
    }
    public function plan_psi()
    {


        $data['query'] = $this->plan_psi_model->plan_psi_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_psi', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_psi_detail($plan_psi_id)
    {


        $this->plan_psi_model->increment_view($plan_psi_id);

        $data['rsData'] = $this->plan_psi_model->read($plan_psi_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_psi_model->read_pdf($plan_psi_id);
        $data['rsDoc'] = $this->plan_psi_model->read_doc($plan_psi_id);
        $data['rsImg'] = $this->plan_psi_model->read_img($plan_psi_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_psi_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_psi($plan_psi_file_id)
    {
        $this->plan_psi_model->increment_download_plan_psi($plan_psi_file_id);
    }
    public function plan_pmda()
    {


        $data['query'] = $this->plan_pmda_model->plan_pmda_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pmda', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function plan_pmda_detail($plan_pmda_id)
    {


        $this->plan_pmda_model->increment_view($plan_pmda_id);

        $data['rsData'] = $this->plan_pmda_model->read($plan_pmda_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->plan_pmda_model->read_pdf($plan_pmda_id);
        $data['rsDoc'] = $this->plan_pmda_model->read_doc($plan_pmda_id);
        $data['rsImg'] = $this->plan_pmda_model->read_img($plan_pmda_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/plan_pmda_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_plan_pmda($plan_pmda_file_id)
    {
        $this->plan_pmda_model->increment_download_plan_pmda($plan_pmda_file_id);
    }
    public function pbsv_cac()
    {

        $data['query'] = $this->pbsv_cac_model->pbsv_cac_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_cac', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_cac_detail($pbsv_cac_id)
    {
        $this->pbsv_cac_model->increment_view($pbsv_cac_id);

        $data['rsData'] = $this->pbsv_cac_model->read($pbsv_cac_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_cac_model->read_pdf($pbsv_cac_id);
        $data['rsDoc'] = $this->pbsv_cac_model->read_doc($pbsv_cac_id);
        $data['rsImg'] = $this->pbsv_cac_model->read_img($pbsv_cac_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_cac_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_cac($pbsv_cac_file_id)
    {
        $this->pbsv_cac_model->increment_download_pbsv_cac($pbsv_cac_file_id);
    }
    public function pbsv_cig()
    {

        $data['query'] = $this->pbsv_cig_model->pbsv_cig_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_cig', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_cig_detail($pbsv_cig_id)
    {

        $this->pbsv_cig_model->increment_view($pbsv_cig_id);

        $data['rsData'] = $this->pbsv_cig_model->read($pbsv_cig_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_cig_model->read_pdf($pbsv_cig_id);
        $data['rsDoc'] = $this->pbsv_cig_model->read_doc($pbsv_cig_id);
        $data['rsImg'] = $this->pbsv_cig_model->read_img($pbsv_cig_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_cig_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_cig($pbsv_cig_file_id)
    {
        $this->pbsv_cig_model->increment_download_pbsv_cig($pbsv_cig_file_id);
    }
    public function pbsv_cjc()
    {


        $data['query'] = $this->pbsv_cjc_model->pbsv_cjc_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_cjc', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_cjc_detail($pbsv_cjc_id)
    {
        $this->pbsv_cjc_model->increment_view($pbsv_cjc_id);

        $data['rsData'] = $this->pbsv_cjc_model->read($pbsv_cjc_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_cjc_model->read_pdf($pbsv_cjc_id);
        $data['rsDoc'] = $this->pbsv_cjc_model->read_doc($pbsv_cjc_id);
        $data['rsImg'] = $this->pbsv_cjc_model->read_img($pbsv_cjc_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_cjc_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_cjc($pbsv_cjc_file_id)
    {
        $this->pbsv_cjc_model->increment_download_pbsv_cjc($pbsv_cjc_file_id);
    }
    public function pbsv_utilities()
    {


        $data['query'] = $this->pbsv_utilities_model->pbsv_utilities_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_utilities', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_utilities_detail($pbsv_utilities_id)
    {


        $this->pbsv_utilities_model->increment_view($pbsv_utilities_id);

        $data['rsData'] = $this->pbsv_utilities_model->read($pbsv_utilities_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_utilities_model->read_pdf($pbsv_utilities_id);
        $data['rsDoc'] = $this->pbsv_utilities_model->read_doc($pbsv_utilities_id);
        $data['rsImg'] = $this->pbsv_utilities_model->read_img($pbsv_utilities_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_utilities_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_utilities($pbsv_utilities_file_id)
    {
        $this->pbsv_utilities_model->increment_download_pbsv_utilities($pbsv_utilities_file_id);
    }
    public function pbsv_sags()
    {

        $data['query'] = $this->pbsv_sags_model->pbsv_sags_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_sags', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_sags_detail($pbsv_sags_id)
    {
        $this->pbsv_sags_model->increment_view($pbsv_sags_id);

        $data['rsData'] = $this->pbsv_sags_model->read($pbsv_sags_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_sags_model->read_pdf($pbsv_sags_id);
        $data['rsDoc'] = $this->pbsv_sags_model->read_doc($pbsv_sags_id);
        $data['rsImg'] = $this->pbsv_sags_model->read_img($pbsv_sags_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_sags_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_sags($pbsv_sags_file_id)
    {
        $this->pbsv_sags_model->increment_download_pbsv_sags($pbsv_sags_file_id);
    }
    public function pbsv_ahs()
    {


        $data['query'] = $this->pbsv_ahs_model->pbsv_ahs_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_ahs', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_ahs_detail($pbsv_ahs_id)
    {


        $this->pbsv_ahs_model->increment_view($pbsv_ahs_id);

        $data['rsData'] = $this->pbsv_ahs_model->read($pbsv_ahs_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_ahs_model->read_pdf($pbsv_ahs_id);
        $data['rsDoc'] = $this->pbsv_ahs_model->read_doc($pbsv_ahs_id);
        $data['rsImg'] = $this->pbsv_ahs_model->read_img($pbsv_ahs_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_ahs_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_ahs($pbsv_ahs_file_id)
    {
        $this->pbsv_ahs_model->increment_download_pbsv_ahs($pbsv_ahs_file_id);
    }
    public function pbsv_oppr()
    {

        $data['query'] = $this->pbsv_oppr_model->pbsv_oppr_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_oppr', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_oppr_detail($pbsv_oppr_id)
    {
        $this->pbsv_oppr_model->increment_view($pbsv_oppr_id);

        $data['rsData'] = $this->pbsv_oppr_model->read($pbsv_oppr_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_oppr_model->read_pdf($pbsv_oppr_id);
        $data['rsDoc'] = $this->pbsv_oppr_model->read_doc($pbsv_oppr_id);
        $data['rsImg'] = $this->pbsv_oppr_model->read_img($pbsv_oppr_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_oppr_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_oppr($pbsv_oppr_file_id)
    {
        $this->pbsv_oppr_model->increment_download_pbsv_oppr($pbsv_oppr_file_id);
    }
    public function pbsv_ems()
    {

        $data['query'] = $this->pbsv_ems_model->pbsv_ems_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_ems', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_ems_detail($pbsv_ems_id)
    {
        $this->pbsv_ems_model->increment_view($pbsv_ems_id);

        $data['rsData'] = $this->pbsv_ems_model->read($pbsv_ems_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_ems_model->read_pdf($pbsv_ems_id);
        $data['rsDoc'] = $this->pbsv_ems_model->read_doc($pbsv_ems_id);
        $data['rsImg'] = $this->pbsv_ems_model->read_img($pbsv_ems_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_ems_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_ems($pbsv_ems_file_id)
    {
        $this->pbsv_ems_model->increment_download_pbsv_ems($pbsv_ems_file_id);
    }
    public function pbsv_gup()
    {


        $data['query'] = $this->pbsv_gup_model->pbsv_gup_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_gup', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_gup_detail($pbsv_gup_id)
    {
        $this->pbsv_gup_model->increment_view($pbsv_gup_id);

        $data['rsData'] = $this->pbsv_gup_model->read($pbsv_gup_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_gup_model->read_pdf($pbsv_gup_id);
        $data['rsDoc'] = $this->pbsv_gup_model->read_doc($pbsv_gup_id);
        $data['rsImg'] = $this->pbsv_gup_model->read_img($pbsv_gup_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_gup_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_gup($pbsv_gup_file_id)
    {
        $this->pbsv_gup_model->increment_download_pbsv_gup($pbsv_gup_file_id);
    }
    public function pbsv_e_book()
    {


        $data['query'] = $this->pbsv_e_book_model->pbsv_e_book_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_e_book', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function pbsv_e_book_detail($pbsv_e_book_id)
    {


        $this->pbsv_e_book_model->increment_view($pbsv_e_book_id);

        $data['rsData'] = $this->pbsv_e_book_model->read($pbsv_e_book_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->pbsv_e_book_model->read_pdf($pbsv_e_book_id);
        $data['rsDoc'] = $this->pbsv_e_book_model->read_doc($pbsv_e_book_id);
        $data['rsImg'] = $this->pbsv_e_book_model->read_img($pbsv_e_book_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/pbsv_e_book_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_pbsv_e_book($pbsv_e_book_file_id)
    {
        $this->pbsv_e_book_model->increment_download_pbsv_e_book($pbsv_e_book_file_id);
    }
    public function operation_reauf_topic()
    {



        $data['query'] = $this->operation_reauf_model->operation_reauf_frontend_list_topic();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_reauf_topic', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_reauf($operation_reauf_type_id)
    {


        $data['query'] = $this->operation_reauf_model->operation_reauf_frontend_list($operation_reauf_type_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_reauf', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_reauf_detail($operation_reauf_id)
    {


        $this->operation_reauf_model->increment_view($operation_reauf_id);

        $data['rsData'] = $this->operation_reauf_model->read($operation_reauf_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_reauf_model->read_pdf($operation_reauf_id);
        $data['rsDoc'] = $this->operation_reauf_model->read_doc($operation_reauf_id);
        $data['rsImg'] = $this->operation_reauf_model->read_img($operation_reauf_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_reauf_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_operation_reauf($operation_reauf_file_id)
    {
        $this->operation_reauf_model->increment_download_operation_reauf($operation_reauf_file_id);
    }
    public function p_sopopaortsr()
    {


        $data['query'] = $this->p_sopopaortsr_model->p_sopopaortsr_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_sopopaortsr', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function p_sopopaortsr_detail($p_sopopaortsr_id)
    {


        $this->p_sopopaortsr_model->increment_view($p_sopopaortsr_id);

        $data['rsData'] = $this->p_sopopaortsr_model->read($p_sopopaortsr_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->p_sopopaortsr_model->read_pdf($p_sopopaortsr_id);
        $data['rsDoc'] = $this->p_sopopaortsr_model->read_doc($p_sopopaortsr_id);
        $data['rsImg'] = $this->p_sopopaortsr_model->read_img($p_sopopaortsr_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_sopopaortsr_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_p_sopopaortsr($p_sopopaortsr_file_id)
    {
        $this->p_sopopaortsr_model->increment_download_p_sopopaortsr($p_sopopaortsr_file_id);
    }
    public function p_sopopip()
    {


        $data['query'] = $this->p_sopopip_model->p_sopopip_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_sopopip', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function p_sopopip_detail($p_sopopip_id)
    {


        $this->p_sopopip_model->increment_view($p_sopopip_id);

        $data['rsData'] = $this->p_sopopip_model->read($p_sopopip_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->p_sopopip_model->read_pdf($p_sopopip_id);
        $data['rsDoc'] = $this->p_sopopip_model->read_doc($p_sopopip_id);
        $data['rsImg'] = $this->p_sopopip_model->read_img($p_sopopip_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_sopopip_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_p_sopopip($p_sopopip_file_id)
    {
        $this->p_sopopip_model->increment_download_p_sopopip($p_sopopip_file_id);
    }
    public function operation_report()
    {


        $data['query'] = $this->operation_report_model->operation_report_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_report', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_report_detail($operation_report_id)
    {


        $this->operation_report_model->increment_view($operation_report_id);

        $data['rsData'] = $this->operation_report_model->read($operation_report_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_report_model->read_pdf($operation_report_id);
        $data['rsDoc'] = $this->operation_report_model->read_doc($operation_report_id);
        $data['rsImg'] = $this->operation_report_model->read_img($operation_report_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_report_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_report($operation_report_file_id)
    {
        $this->operation_report_model->increment_download_operation_report($operation_report_file_id);
    }
    public function p_rpobuy()
    {


        $data['query'] = $this->p_rpobuy_model->p_rpobuy_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_rpobuy', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function p_rpobuy_detail($p_rpobuy_id)
    {


        $this->p_rpobuy_model->increment_view($p_rpobuy_id);

        $data['rsData'] = $this->p_rpobuy_model->read($p_rpobuy_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->p_rpobuy_model->read_pdf($p_rpobuy_id);
        $data['rsDoc'] = $this->p_rpobuy_model->read_doc($p_rpobuy_id);
        $data['rsImg'] = $this->p_rpobuy_model->read_img($p_rpobuy_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_rpobuy_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_p_rpobuy($p_rpobuy_file_id)
    {
        $this->p_rpobuy_model->increment_download_p_rpobuy($p_rpobuy_file_id);
    }
    public function p_rpo()
    {


        $data['query'] = $this->p_rpo_model->p_rpo_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_rpo', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function p_rpo_detail($p_rpo_id)
    {


        $this->p_rpo_model->increment_view($p_rpo_id);

        $data['rsData'] = $this->p_rpo_model->read($p_rpo_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->p_rpo_model->read_pdf($p_rpo_id);
        $data['rsDoc'] = $this->p_rpo_model->read_doc($p_rpo_id);
        $data['rsImg'] = $this->p_rpo_model->read_img($p_rpo_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_rpo_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_p_rpo($p_rpo_file_id)
    {
        $this->p_rpo_model->increment_download_p_rpo($p_rpo_file_id);
    }
    public function p_reb()
    {


        $data['query'] = $this->p_reb_model->p_reb_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_reb', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function p_reb_detail($p_reb_id)
    {


        $this->p_reb_model->increment_view($p_reb_id);

        $data['rsData'] = $this->p_reb_model->read($p_reb_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->p_reb_model->read_pdf($p_reb_id);
        $data['rsDoc'] = $this->p_reb_model->read_doc($p_reb_id);
        $data['rsImg'] = $this->p_reb_model->read_img($p_reb_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/p_reb_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_p_reb($p_reb_file_id)
    {
        $this->p_reb_model->increment_download_p_reb($p_reb_file_id);
    }
    public function operation_sap()
    {


        $data['query'] = $this->operation_sap_model->operation_sap_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_sap', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_sap_detail($operation_sap_id)
    {


        $this->operation_sap_model->increment_view($operation_sap_id);

        $data['rsData'] = $this->operation_sap_model->read($operation_sap_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_sap_model->read_pdf($operation_sap_id);
        $data['rsDoc'] = $this->operation_sap_model->read_doc($operation_sap_id);
        $data['rsImg'] = $this->operation_sap_model->read_img($operation_sap_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_sap_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_sap($operation_sap_file_id)
    {
        $this->operation_sap_model->increment_download_operation_sap($operation_sap_file_id);
    }
    public function operation_pm()
    {


        $data['query'] = $this->operation_pm_model->operation_pm_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_pm', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_pm_detail($operation_pm_id)
    {


        $this->operation_pm_model->increment_view($operation_pm_id);

        $data['rsData'] = $this->operation_pm_model->read($operation_pm_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_pm_model->read_pdf($operation_pm_id);
        $data['rsDoc'] = $this->operation_pm_model->read_doc($operation_pm_id);
        $data['rsImg'] = $this->operation_pm_model->read_img($operation_pm_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_pm_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_pm($operation_pm_file_id)
    {
        $this->operation_pm_model->increment_download_operation_pm($operation_pm_file_id);
    }
    public function operation_mr()
    {


        $data['query'] = $this->operation_mr_model->operation_mr_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_mr', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_mr_detail($operation_mr_id)
    {


        $this->operation_mr_model->increment_view($operation_mr_id);

        $data['rsData'] = $this->operation_mr_model->read($operation_mr_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_mr_model->read_pdf($operation_mr_id);
        $data['rsDoc'] = $this->operation_mr_model->read_doc($operation_mr_id);
        $data['rsImg'] = $this->operation_mr_model->read_img($operation_mr_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_mr_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_mr($operation_mr_file_id)
    {
        $this->operation_mr_model->increment_download_operation_mr($operation_mr_file_id);
    }
    public function video()
    {


        $data['query'] = $this->video_model->video_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/video', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function video_detail($video_id)
    {


        $this->video_model->increment_view($video_id);
        $data['rsData'] = $this->video_model->read($video_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/video_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_policy_hr()
    {


        $data['query'] = $this->operation_policy_hr_model->operation_policy_hr_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_policy_hr', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_policy_hr_detail($operation_policy_hr_id)
    {


        $this->operation_policy_hr_model->increment_view($operation_policy_hr_id);

        $data['rsData'] = $this->operation_policy_hr_model->read($operation_policy_hr_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_policy_hr_model->read_pdf($operation_policy_hr_id);
        $data['rsDoc'] = $this->operation_policy_hr_model->read_doc($operation_policy_hr_id);
        $data['rsImg'] = $this->operation_policy_hr_model->read_img($operation_policy_hr_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_policy_hr_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_policy_hr($operation_policy_hr_file_id)
    {
        $this->operation_policy_hr_model->increment_download_operation_policy_hr($operation_policy_hr_file_id);
    }
    public function operation_am_hr()
    {


        $data['query'] = $this->operation_am_hr_model->operation_am_hr_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_am_hr', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_am_hr_detail($operation_am_hr_id)
    {


        $this->operation_am_hr_model->increment_view($operation_am_hr_id);

        $data['rsData'] = $this->operation_am_hr_model->read($operation_am_hr_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_am_hr_model->read_pdf($operation_am_hr_id);
        $data['rsDoc'] = $this->operation_am_hr_model->read_doc($operation_am_hr_id);
        $data['rsImg'] = $this->operation_am_hr_model->read_img($operation_am_hr_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_am_hr_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_am_hr($operation_am_hr_file_id)
    {
        $this->operation_am_hr_model->increment_download_operation_am_hr($operation_am_hr_file_id);
    }
    public function operation_rdam_hr()
    {


        $data['query'] = $this->operation_rdam_hr_model->operation_rdam_hr_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_rdam_hr', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_rdam_hr_detail($operation_rdam_hr_id)
    {


        $this->operation_rdam_hr_model->increment_view($operation_rdam_hr_id);

        $data['rsData'] = $this->operation_rdam_hr_model->read($operation_rdam_hr_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_rdam_hr_model->read_pdf($operation_rdam_hr_id);
        $data['rsDoc'] = $this->operation_rdam_hr_model->read_doc($operation_rdam_hr_id);
        $data['rsImg'] = $this->operation_rdam_hr_model->read_img($operation_rdam_hr_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_rdam_hr_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_rdam_hr($operation_rdam_hr_file_id)
    {
        $this->operation_rdam_hr_model->increment_download_operation_rdam_hr($operation_rdam_hr_file_id);
    }
    public function operation_cdm_topic()
    {


        $data['query'] = $this->operation_cdm_model->operation_cdm_frontend_list_topic();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_cdm_topic', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_cdm($operation_cdm_type_id)
    {


        $data['query'] = $this->operation_cdm_model->operation_cdm_frontend_list($operation_cdm_type_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_cdm', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_cdm_detail($operation_cdm_id)
    {


        $this->operation_cdm_model->increment_view($operation_cdm_id);

        $data['rsData'] = $this->operation_cdm_model->read($operation_cdm_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_cdm_model->read_pdf($operation_cdm_id);
        $data['rsDoc'] = $this->operation_cdm_model->read_doc($operation_cdm_id);
        $data['rsImg'] = $this->operation_cdm_model->read_img($operation_cdm_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_cdm_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_operation_cdm($operation_cdm_file_id)
    {
        $this->operation_cdm_model->increment_download_operation_cdm($operation_cdm_file_id);
    }
    public function operation_po()
    {


        $data['query'] = $this->operation_po_model->operation_po_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_po', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_po_detail($operation_po_id)
    {


        $this->operation_po_model->increment_view($operation_po_id);

        $data['rsData'] = $this->operation_po_model->read($operation_po_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_po_model->read_pdf($operation_po_id);
        $data['rsDoc'] = $this->operation_po_model->read_doc($operation_po_id);
        $data['rsImg'] = $this->operation_po_model->read_img($operation_po_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_po_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_po($operation_po_file_id)
    {
        $this->operation_po_model->increment_download_operation_po($operation_po_file_id);
    }
    public function operation_eco_topic()
    {


        $data['query'] = $this->operation_eco_model->operation_eco_frontend_list_topic();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_eco_topic', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_eco($operation_eco_type_id)
    {


        $data['query'] = $this->operation_eco_model->operation_eco_frontend_list($operation_eco_type_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_eco', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_eco_detail($operation_eco_id)
    {


        $this->operation_eco_model->increment_view($operation_eco_id);

        $data['rsData'] = $this->operation_eco_model->read($operation_eco_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_eco_model->read_pdf($operation_eco_id);
        $data['rsDoc'] = $this->operation_eco_model->read_doc($operation_eco_id);
        $data['rsImg'] = $this->operation_eco_model->read_img($operation_eco_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_eco_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_operation_eco($operation_eco_file_id)
    {
        $this->operation_eco_model->increment_download_operation_eco($operation_eco_file_id);
    }
    public function operation_meeting_topic()
    {


        $data['query'] = $this->operation_meeting_model->operation_meeting_frontend_list_topic();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_meeting_topic', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_meeting($operation_meeting_type_id)
    {


        $data['query'] = $this->operation_meeting_model->operation_meeting_frontend_list($operation_meeting_type_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_meeting', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_meeting_detail($operation_meeting_id)
    {


        $this->operation_meeting_model->increment_view($operation_meeting_id);

        $data['rsData'] = $this->operation_meeting_model->read($operation_meeting_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_meeting_model->read_pdf($operation_meeting_id);
        $data['rsDoc'] = $this->operation_meeting_model->read_doc($operation_meeting_id);
        $data['rsImg'] = $this->operation_meeting_model->read_img($operation_meeting_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_meeting_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_operation_meeting($operation_meeting_file_id)
    {
        $this->operation_meeting_model->increment_download_operation_meeting($operation_meeting_file_id);
    }

    public function finance_topic()
    {


        $data['query'] = $this->finance_model->finance_frontend_list_topic();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/finance_topic', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function finance($finance_type_id)
    {


        $data['query'] = $this->finance_model->finance_frontend_list($finance_type_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/finance', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function finance_detail($finance_id)
    {


        $this->finance_model->increment_view($finance_id);

        $data['rsData'] = $this->finance_model->read($finance_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->finance_model->read_pdf($finance_id);
        $data['rsDoc'] = $this->finance_model->read_doc($finance_id);
        $data['rsImg'] = $this->finance_model->read_img($finance_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/finance_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_finance($finance_file_id)
    {
        $this->finance_model->increment_download_finance($finance_file_id);
    }

    public function taepts_topic()
    {


        $data['query'] = $this->taepts_model->taepts_frontend_list_topic();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/taepts_topic', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function taepts($taepts_type_id)
    {


        $data['query'] = $this->taepts_model->taepts_frontend_list($taepts_type_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/taepts', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function taepts_detail($taepts_id)
    {


        $this->taepts_model->increment_view($taepts_id);

        $data['rsData'] = $this->taepts_model->read($taepts_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->taepts_model->read_pdf($taepts_id);
        $data['rsDoc'] = $this->taepts_model->read_doc($taepts_id);
        $data['rsImg'] = $this->taepts_model->read_img($taepts_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/taepts_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_taepts($taepts_file_id)
    {
        $this->taepts_model->increment_download_taepts($taepts_file_id);
    }

    public function operation_pgn()
    {


        $data['query'] = $this->operation_pgn_model->operation_pgn_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_pgn', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_pgn_detail($operation_pgn_id)
    {


        $this->operation_pgn_model->increment_view($operation_pgn_id);

        $data['rsData'] = $this->operation_pgn_model->read($operation_pgn_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_pgn_model->read_pdf($operation_pgn_id);
        $data['rsDoc'] = $this->operation_pgn_model->read_doc($operation_pgn_id);
        $data['rsImg'] = $this->operation_pgn_model->read_img($operation_pgn_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_pgn_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_pgn($operation_pgn_file_id)
    {
        $this->operation_pgn_model->increment_download_operation_pgn($operation_pgn_file_id);
    }
    public function operation_mcc()
    {


        $data['query'] = $this->operation_mcc_model->operation_mcc_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_mcc', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_mcc_detail($operation_mcc_id)
    {


        $this->operation_mcc_model->increment_view($operation_mcc_id);

        $data['rsData'] = $this->operation_mcc_model->read($operation_mcc_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_mcc_model->read_pdf($operation_mcc_id);
        $data['rsDoc'] = $this->operation_mcc_model->read_doc($operation_mcc_id);
        $data['rsImg'] = $this->operation_mcc_model->read_img($operation_mcc_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_mcc_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_mcc($operation_mcc_file_id)
    {
        $this->operation_mcc_model->increment_download_operation_mcc($operation_mcc_file_id);
    }
    public function operation_aca()
    {


        $data['query'] = $this->operation_aca_model->operation_aca_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_aca', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_aca_detail($operation_aca_id)
    {


        $this->operation_aca_model->increment_view($operation_aca_id);

        $data['rsData'] = $this->operation_aca_model->read($operation_aca_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_aca_model->read_pdf($operation_aca_id);
        $data['rsDoc'] = $this->operation_aca_model->read_doc($operation_aca_id);
        $data['rsImg'] = $this->operation_aca_model->read_img($operation_aca_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_aca_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_aca($operation_aca_file_id)
    {
        $this->operation_aca_model->increment_download_operation_aca($operation_aca_file_id);
    }
    public function lpa()
    {


        $data['query'] = $this->lpa_model->lpa_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/lpa', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function lpa_detail($lpa_id)
    {


        $this->lpa_model->increment_view($lpa_id);

        $data['rsData'] = $this->lpa_model->read($lpa_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->lpa_model->read_pdf($lpa_id);
        $data['rsDoc'] = $this->lpa_model->read_doc($lpa_id);
        $data['rsImg'] = $this->lpa_model->read_img($lpa_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/lpa_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_lpa($lpa_file_id)
    {
        $this->lpa_model->increment_download_lpa($lpa_file_id);
    }
    public function ita_all()
    {


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/ita_all');
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function ita()
    {


        $data['query'] = $this->ita_model->ita_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/ita', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function ita_detail($ita_id)
    {


        $this->ita_model->increment_view($ita_id);

        $data['rsData'] = $this->ita_model->read($ita_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->ita_model->read_pdf($ita_id);
        $data['rsDoc'] = $this->ita_model->read_doc($ita_id);
        $data['rsImg'] = $this->ita_model->read_img($ita_id);


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/ita_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_ita($ita_file_id)
    {
        $this->ita_model->increment_download_ita($ita_file_id);
    }

    public function ita_year()
    {


        $data['query'] = $this->ita_year_model->ita_year_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/ita_year', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function ita_year_detail($ita_year_id)
    {


        $data['query'] = $this->ita_year_model->read($ita_year_id);
        $data['query_topic'] = $this->ita_year_model->get_ita_year_data($ita_year_id);
        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['query']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/ita_year_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_aditn()
    {


        $data['query'] = $this->operation_aditn_model->operation_aditn_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_aditn', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_aditn_detail($operation_aditn_id)
    {


        $this->operation_aditn_model->increment_view($operation_aditn_id);

        $data['rsData'] = $this->operation_aditn_model->read($operation_aditn_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_aditn_model->read_pdf($operation_aditn_id);
        $data['rsDoc'] = $this->operation_aditn_model->read_doc($operation_aditn_id);
        $data['rsImg'] = $this->operation_aditn_model->read_img($operation_aditn_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_aditn_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_aditn($operation_aditn_file_id)
    {
        $this->operation_aditn_model->increment_download_operation_aditn($operation_aditn_file_id);
    }
    public function operation_procurement()
    {


        $data['query'] = $this->operation_procurement_model->operation_procurement_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_procurement', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_procurement_detail($operation_procurement_id)
    {


        $this->operation_procurement_model->increment_view($operation_procurement_id);

        $data['rsData'] = $this->operation_procurement_model->read($operation_procurement_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsFile'] = $this->operation_procurement_model->read_file($operation_procurement_id);
        $data['rsImg'] = $this->operation_procurement_model->read_img($operation_procurement_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_procurement_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_procurement($operation_procurement_file_id)
    {
        $this->operation_procurement_model->increment_download_operation_procurement($operation_procurement_file_id);
    }
    public function operation_aa()
    {


        $data['query'] = $this->operation_aa_model->operation_aa_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_aa', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function operation_aa_detail($operation_aa_id)
    {


        $this->operation_aa_model->increment_view($operation_aa_id);

        $data['rsData'] = $this->operation_aa_model->read($operation_aa_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->operation_aa_model->read_pdf($operation_aa_id);
        $data['rsDoc'] = $this->operation_aa_model->read_doc($operation_aa_id);
        $data['rsImg'] = $this->operation_aa_model->read_img($operation_aa_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/operation_aa_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_operation_aa($operation_aa_file_id)
    {
        $this->operation_aa_model->increment_download_operation_aa($operation_aa_file_id);
    }
    public function newsletter()
    {


        $data['query'] = $this->newsletter_model->newsletter_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/newsletter', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function newsletter_detail($newsletter_id)
    {


        $this->newsletter_model->increment_view($newsletter_id);

        $data['rsData'] = $this->newsletter_model->read($newsletter_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->newsletter_model->read_pdf($newsletter_id);
        $data['rsDoc'] = $this->newsletter_model->read_doc($newsletter_id);
        $data['rsImg'] = $this->newsletter_model->read_img($newsletter_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/newsletter_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_newsletter($newsletter_file_id)
    {
        $this->newsletter_model->increment_download_newsletter($newsletter_file_id);
    }


    public function q_a()
    {
        try {
            log_message('info', '--- เริ่มการทำงานของฟังก์ชัน q_a ---');

            $this->load->library('form_validation');
            $this->load->library('vulgar_check');

            $qa_model = $this->get_qa_model();
            if (!$qa_model) {
                log_message('error', 'q_a: ไม่สามารถโหลด Q_a_model ได้');
                show_error('ไม่สามารถโหลด Q_a_model ได้');
                return;
            }

            // ดึงข้อมูล Q&A ทั้งหมด
            $data['query'] = $qa_model->list_all();
            log_message('debug', 'q_a: ดึงข้อมูล Q&A ทั้งหมดได้ ' . count($data['query']) . ' รายการ');


            // ดึงข้อมูล Reply สำหรับแต่ละ Q&A
            $data['rsReply'] = array();
            foreach ($data['query'] as $rs) {
                $q_a_id = $rs->q_a_id;
                $data['rsReply'][$q_a_id] = $qa_model->read_reply($q_a_id);
            }
            log_message('debug', 'q_a: ดึงข้อมูล Reply สำหรับ Q&A ทั้งหมดเรียบร้อย');


            // *** ตรวจสอบสถานะการเข้าสู่ระบบที่ controller และใช้ fixUserIdOverflow ***
            $login_status = $qa_model->check_user_login();
            $data['is_logged_in'] = $login_status['is_logged_in'];
            $data['user_info'] = $login_status['user_info'];
            $data['user_type'] = $login_status['user_type'];
            log_message('info', 'q_a: ตรวจสอบสถานะ Login - is_logged_in: ' . ($data['is_logged_in'] ? 'Yes' : 'No'));
            log_message('debug', 'q_a: ข้อมูลผู้ใช้ (user_info): ' . print_r($data['user_info'], true));


            // *** เพิ่มข้อมูลสำหรับ debug และแสดงใน frontend ***
            if ($data['is_logged_in'] && isset($data['user_info']['user_id'])) {
                // เก็บข้อมูล original session id สำหรับเปรียบเทียบ
                $original_session_id = $data['user_type'] === 'public'
                    ? $this->session->userdata('mp_id')
                    : $this->session->userdata('m_id');

                $data['debug_info'] = [
                    'original_session_id' => $original_session_id,
                    'fixed_user_id' => $data['user_info']['user_id'],
                    'is_user_id_fixed' => ($original_session_id != $data['user_info']['user_id']),
                    'user_email' => $data['user_info']['email'] ?? null
                ];

                // Log ที่มีอยู่แล้ว (ดีอยู่แล้ว)
                log_message('info', 'Q&A page user debug: ' . print_r($data['debug_info'], true));
            }

            // เพิ่มข้อมูลสำหรับ navbar
            $data['qActivity'] = $this->activity_model->activity_frontend();
            $data['qHotnews'] = $this->HotNews_model->hotnews_frontend();
            $data['qWeather'] = $this->Weather_report_model->weather_reports_frontend();
            $data['events'] = $this->calender_model->get_events();
            $data['qBanner'] = $this->banner_model->banner_frontend();
            $data['qBackground_personnel'] = $this->background_personnel_model->background_personnel_frontend();
            log_message('debug', 'q_a: โหลดข้อมูลสำหรับ Navbar และส่วนประกอบอื่นๆ เรียบร้อย');


            // กำหนด validation rules (ส่วนนี้จะทำงานเมื่อมีการ submit form ในหน้าเดียวกัน แต่จากโค้ด เหมือนจะแยกฟอร์ม)
            $this->form_validation->set_rules(
                'q_a_msg',
                'หัวข้อคำถาม',
                'trim|required|min_length[4]|callback_check_vulgar|callback_check_no_urls',
                array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 4 ตัว')
            );
            // ... (validation rules อื่นๆ)


            log_message('info', 'q_a: กำลังจะแสดงผลหน้า View');
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');
            $this->load->view('frontend/q_a', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_templat/footer');
            log_message('info', '--- สิ้นสุดการทำงานของฟังก์ชัน q_a ---');

        } catch (Exception $e) {
            log_message('error', 'Q&A page error: ' . $e->getMessage());
            show_error('เกิดข้อผิดพลาดในการโหลดหน้า Q&A');
        }
    }

    // ========================================================================
    // การแก้ไข adding_q_a() Method - เพิ่ม reCAPTCHA verification
    // ========================================================================

    public function adding_q_a()
    {
        log_message('info', '--- เริ่มการทำงานของฟังก์ชัน adding_q_a (แสดงฟอร์ม/รับข้อมูล) ---');

        $this->load->library('form_validation');
        $this->load->library('vulgar_check');
        $this->load->library('upload');
        $this->load->helper(array('form', 'url', 'file'));

        $qa_model = $this->get_qa_model();
        if (!$qa_model) {
            log_message('error', 'adding_q_a: ไม่สามารถโหลด Q_a_model ได้');
            show_error('ไม่สามารถโหลด Q_a_model ได้');
            return;
        }

        // โหลดข้อมูลสำหรับหน้า navbar
        $data['qActivity'] = $this->activity_model->activity_frontend();
        $data['qHotnews'] = $this->HotNews_model->hotnews_frontend();
        $data['qWeather'] = $this->Weather_report_model->weather_reports_frontend();
        $data['events'] = $this->calender_model->get_events();
        $data['qBanner'] = $this->banner_model->banner_frontend();
        $data['qBackground_personnel'] = $this->background_personnel_model->background_personnel_frontend();
        log_message('debug', 'adding_q_a: โหลดข้อมูลสำหรับ Navbar เรียบร้อย');

        // *** ตรวจสอบสถานะการเข้าสู่ระบบพร้อมแก้ไข user_id overflow ***
        $login_status = $qa_model->check_user_login();
        log_message('debug', 'adding_q_a: ผลลัพธ์เริ่มต้นจาก check_user_login(): ' . print_r($login_status, true));
        $fixed_user_info = $this->fix_user_id_overflow($login_status);
        log_message('info', 'adding_q_a: ข้อมูลผู้ใช้หลัง fix_user_id_overflow: ' . print_r($fixed_user_info, true));

        $data['is_logged_in'] = $fixed_user_info['is_logged_in'];
        $data['user_info'] = $fixed_user_info;

        // ตรวจสอบว่ามีการส่งข้อมูลฟอร์มมาหรือไม่
        if ($this->input->post()) {
            log_message('info', 'adding_q_a: ตรวจพบการส่งข้อมูลแบบ POST');
            log_message('debug', 'adding_q_a: ข้อมูล POST ที่ได้รับ: ' . print_r($this->input->post(), true));

            // *** เพิ่ม: ตรวจสอบ reCAPTCHA ก่อนดำเนินการอื่น ***
            $recaptcha_token = $this->input->post('g-recaptcha-response');

            if (empty($recaptcha_token)) {
                log_message('debug', 'adding_q_a: reCAPTCHA token missing');

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'กรุณายืนยันตัวตน reCAPTCHA',
                        'error_type' => 'recaptcha_missing'
                    ]);
                    return;
                }

                $this->session->set_flashdata('save_error', 'กรุณายืนยันตัวตน reCAPTCHA');
                redirect('Pages/adding_q_a');
                return;
            }

            // *** กำหนด user_type สำหรับ reCAPTCHA ***
            $current_user_type = $login_status['user_type'] ?? 'guest';
            $staff_types = ['staff', 'system_admin', 'super_admin', 'user_admin'];
            $recaptcha_user_type = in_array($current_user_type, $staff_types) ? 'staff' : 'citizen';

            log_message('info', "adding_q_a: User type mapping - {$current_user_type} -> {$recaptcha_user_type}");

            // *** ตรวจสอบ reCAPTCHA ***
            //$this->load->library('recaptcha_lib');
            $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $recaptcha_user_type);

            if (!$recaptcha_result['success']) {
                log_message('error', "adding_q_a: reCAPTCHA failed for {$current_user_type} -> {$recaptcha_user_type}: " . $recaptcha_result['message']);

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'การยืนยันตัวตนไม่ผ่าน กรุณาลองใหม่',
                        'error_type' => 'recaptcha_failed',
                        'user_type_detected' => $current_user_type
                    ]);
                    return;
                }

                $this->session->set_flashdata('save_error', 'การยืนยันตัวตนไม่ผ่าน กรุณาลองใหม่');
                redirect('Pages/adding_q_a');
                return;
            }

            log_message('info', "adding_q_a: reCAPTCHA verified successfully for {$current_user_type} -> {$recaptcha_user_type}");

            $is_logged_in = $fixed_user_info['is_logged_in'];

            // *** เรียกใช้ Model method ***
            log_message('info', 'adding_q_a: Calling Model add_q_a method...');
            $result = $qa_model->add_q_a();
            log_message('info', 'adding_q_a: Model result: ' . ($result ? 'Q&A ID ' . $result : 'FALSE'));

            // *** ตรวจสอบ vulgar flash data ก่อน ***
            if ($this->session->flashdata('save_vulgar')) {
                $vulgar_words = $this->session->flashdata('vulgar_words') ?: array();
                $vulgar_message = $this->session->flashdata('vulgar_message') ?: 'พบคำไม่เหมาะสม';

                log_message('debug', 'adding_q_a: Vulgar content detected: ' . implode(', ', $vulgar_words));

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'vulgar_detected' => true,
                        'vulgar_words' => $vulgar_words,
                        'message' => $vulgar_message,
                        'error_type' => 'vulgar_content'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_vulgar', TRUE);
                $this->session->set_flashdata('vulgar_words', $vulgar_words);
                redirect('Pages/adding_q_a');
                return;
            }

            // *** ตรวจสอบ URL detection flash data ***
            if ($this->session->flashdata('save_url_detected')) {
                $url_message = $this->session->flashdata('url_message') ?: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';

                log_message('debug', 'adding_q_a: URL detected in Q&A content');

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'url_detected' => true,
                        'message' => $url_message,
                        'error_type' => 'url_content'
                    ));
                    return;
                }

                $this->session->set_flashdata('validation_error', true);
                $this->session->set_flashdata('error_message', $url_message);
                $this->session->set_flashdata('form_data', $this->input->post());
                redirect('Pages/adding_q_a');
                return;
            }

            // *** ตรวจสอบผลลัพธ์จาก Model ***
            if ($result) {
                log_message('info', 'adding_q_a: SUCCESS - Q&A created with ID: ' . $result);

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => true,
                        'qa_id' => $result,
                        'message' => 'บันทึกกระทู้สำเร็จ ขอบคุณที่ส่งคำถาม',
                        'timestamp' => date('Y-m-d H:i:s')
                    ));
                    return;
                }

                $this->session->set_flashdata('save_success', 'บันทึกกระทู้สำเร็จ ขอบคุณที่ส่งคำถาม');
                $this->session->set_flashdata('new_qa_id', $result);
                redirect('Pages/q_a');

            } else {
                log_message('error', 'adding_q_a: FAILED - Model returned false');

                $error_message = 'เกิดข้อผิดพลาดในการบันทึกกระทู้';

                if ($this->session->flashdata('save_error')) {
                    $error_message = $this->session->flashdata('save_error');
                } elseif ($this->session->flashdata('validation_error')) {
                    $error_message = $this->session->flashdata('validation_error');
                }

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'message' => $error_message,
                        'error_type' => 'database_error'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_error', $error_message);
                redirect('Pages/adding_q_a');
            }

            return;
        }

        // แสดงฟอร์ม
        log_message('info', 'adding_q_a: กำลังจะแสดงผลหน้า View (ฟอร์มเพิ่มกระทู้)');
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/q_a_form_add', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer');
        log_message('info', '--- สิ้นสุดการทำงานของฟังก์ชัน adding_q_a ---');
    }

    // ========================================================================
    // การแก้ไข add_q_a() Method - เพิ่ม reCAPTCHA verification
    // ========================================================================

    public function add_q_a()
    {
        try {
            log_message('info', '=== CONTROLLER ADD Q&A START - Enhanced Response with reCAPTCHA ===');

            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                log_message('debug', 'add_q_a: เข้าถึงด้วย Method ที่ไม่ใช่ POST. กำลัง redirect.');
                redirect('Pages/adding_q_a');
                return;
            }

            // ✅ ใช้ฟังก์ชันตรวจสอบ reCAPTCHA ที่ปรับปรุงแล้ว
            $recaptcha_result = $this->verify_recaptcha_for_qa();

            if (!$recaptcha_result['success']) {
                log_message('error', 'add_q_a: reCAPTCHA verification failed: ' . $recaptcha_result['message']);

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode($recaptcha_result);
                    return;
                }

                $this->session->set_flashdata('save_error', $recaptcha_result['message']);
                redirect('Pages/adding_q_a');
                return;
            }

            log_message('info', 'add_q_a: reCAPTCHA verified successfully with data: ' . json_encode([
                'action' => $recaptcha_result['recaptcha_action_used'] ?? 'unknown',
                'source' => $recaptcha_result['recaptcha_source_used'] ?? 'unknown',
                'user_type' => $recaptcha_result['user_type_detected'] ?? 'unknown'
            ]));

            $qa_model = $this->get_qa_model();
            if (!$qa_model) {
                log_message('error', 'add_q_a: ไม่สามารถโหลด Q_a_model ได้');

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'ไม่สามารถโหลด model ได้',
                        'error_type' => 'system_error'
                    ]);
                    return;
                }

                $this->session->set_flashdata('save_error', 'ไม่สามารถโหลด model ได้');
                redirect('Pages/adding_q_a');
                return;
            }

            log_message('info', 'Calling Model add_q_a method...');
            $result = $qa_model->add_q_a();
            log_message('info', 'Model result: ' . ($result ? 'ID ' . $result : 'FALSE'));

            // *** ตรวจสอบ vulgar flash data ก่อน ***
            if ($this->session->flashdata('save_vulgar')) {
                $vulgar_words = $this->session->flashdata('vulgar_words') ?: array();
                $vulgar_message = $this->session->flashdata('vulgar_message') ?: 'พบคำไม่เหมาะสม';

                log_message('debug', 'Vulgar content detected: ' . implode(', ', $vulgar_words));

                if ($this->input->is_ajax_request()) {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(array(
                        'success' => false,
                        'vulgar_detected' => true,
                        'vulgar_words' => $vulgar_words,
                        'message' => $vulgar_message,
                        'error_type' => 'vulgar_content'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_vulgar', TRUE);
                $this->session->set_flashdata('vulgar_words', $vulgar_words);
                redirect('Pages/adding_q_a');
                return;
            }

            // *** ตรวจสอบ URL detection flash data ***
            if ($this->session->flashdata('save_url_detected')) {
                $url_message = $this->session->flashdata('url_message') ?: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';
                $url_fields = $this->session->flashdata('url_fields') ?: array();

                log_message('debug', 'URL content detected: ' . $url_message);

                if ($this->input->is_ajax_request()) {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(array(
                        'success' => false,
                        'url_detected' => true,
                        'url_fields' => $url_fields,
                        'message' => $url_message,
                        'error_type' => 'url_content'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_error', $url_message);
                redirect('Pages/adding_q_a');
                return;
            }

            if ($result) {
                log_message('info', 'SUCCESS: Q&A created via Model with ID: ' . $result);

                if ($this->input->is_ajax_request()) {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(array(
                        'success' => true,
                        'qa_id' => $result,
                        'message' => 'บันทึกกระทู้สำเร็จ',
                        'recaptcha_info' => [
                            'action_used' => $recaptcha_result['recaptcha_action_used'] ?? 'unknown',
                            'source_used' => $recaptcha_result['recaptcha_source_used'] ?? 'unknown',
                            'user_type' => $recaptcha_result['user_type_detected'] ?? 'unknown'
                        ]
                    ));
                    return;
                }

                $this->session->set_flashdata('save_success', 'บันทึกกระทู้สำเร็จ');
                redirect('Pages/q_a');
            } else {
                log_message('error', 'FAILED: Model returned false');

                $error_message = 'เกิดข้อผิดพลาดในการบันทึก';

                if ($this->session->flashdata('save_error')) {
                    $error_message = $this->session->flashdata('save_error');
                } elseif ($this->session->flashdata('error_message')) {
                    $error_message = $this->session->flashdata('error_message');
                }

                if ($this->input->is_ajax_request()) {
                    if (!headers_sent()) {
                        header('Content-Type: application/json');
                    }
                    echo json_encode(array(
                        'success' => false,
                        'message' => $error_message,
                        'error_type' => 'database_error'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_error', $error_message);
                redirect('Pages/adding_q_a');
            }

        } catch (Exception $e) {
            log_message('error', 'Controller Exception in add_q_a: ' . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                echo json_encode(array(
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage(),
                    'error_type' => 'system_error'
                ));
                return;
            }

            $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดระบบ');
            redirect('Pages/adding_q_a');
        }
    }


    /**
     * ตรวจสอบฟอร์ม - แก้ไขให้ใช้ Library อย่างถูกต้อง
     * แทนที่ check_form() เดิมใน Controller ด้วยโค้ดนี้
     */
    public function check_form($form_data, $fields_to_check = null)
    {
        log_message('info', '--- Running Controller check_form (FIXED - Use Library Correctly) ---');

        try {
            if (empty($form_data)) {
                log_message('error', 'check_form: Form data is empty.');
                return ['status' => 'error', 'message' => 'ไม่พบข้อมูลฟอร์ม'];
            }

            // โหลด vulgar_check library
            if (!isset($this->vulgar_check)) {
                $this->load->library('vulgar_check');
            }

            if ($fields_to_check === null) {
                $fields_to_check = ['q_a_msg', 'q_a_by', 'q_a_detail', 'q_a_email', 'q_a_reply_by', 'q_a_reply_detail', 'q_a_reply_email'];
            }
            log_message('debug', 'check_form: Fields to check: ' . implode(', ', $fields_to_check));

            $results = [];
            $has_vulgar = false;
            $has_url = false;
            $all_vulgar_words = array();
            $url_detected_fields = array();

            foreach ($fields_to_check as $field) {
                if (isset($form_data[$field]) && !empty($form_data[$field])) {
                    log_message('debug', 'check_form: Checking field "' . $field . '"...');

                    // ตรวจสอบ URL (ข้ามฟิลด์อีเมล)
                    if (!in_array($field, ['q_a_email', 'q_a_reply_email'])) {
                        if (!$this->vulgar_check->check_no_urls($form_data[$field])) {
                            $has_url = true;
                            $url_detected_fields[] = $field;
                            log_message('debug', 'check_form: URL detected in field "' . $field . '": ' . $form_data[$field]);

                            $results[$field] = [
                                'has_vulgar' => false,
                                'has_url' => true,
                                'original_text' => $form_data[$field]
                            ];
                            continue; // พบ URL แล้ว ไม่ต้องเช็ค vulgar
                        }
                    }

                    // *** แก้ไข: ใช้ check_text() แทนการเรียก private methods ***
                    $check_result = $this->vulgar_check->check_text($form_data[$field]);

                    // ตรวจสอบผลลัพธ์จาก check_text()
                    if (isset($check_result['data']['has_vulgar_words']) && $check_result['data']['has_vulgar_words']) {
                        log_message('debug', 'check_form: Vulgar word found in "' . $field . '"');
                        $has_vulgar = true;
                        $results[$field] = [
                            'has_vulgar' => true,
                            'has_url' => false,
                            'original_text' => $form_data[$field],
                            'censored_text' => $check_result['data']['censored_content'],
                            'vulgar_words' => $check_result['data']['vulgar_words']
                        ];

                        $all_vulgar_words = array_merge($all_vulgar_words, $check_result['data']['vulgar_words']);
                    } else {
                        // ไม่พบคำไม่สุภาพ
                        $results[$field] = [
                            'has_vulgar' => false,
                            'has_url' => false,
                            'original_text' => $form_data[$field]
                        ];
                    }
                }
            }

            $final_result = [
                'status' => 'success',
                'has_vulgar' => $has_vulgar,
                'has_url' => $has_url,
                'block_submit' => ($has_vulgar || $has_url),
                'results' => $results,
                'all_vulgar_words' => array_unique($all_vulgar_words),
                'url_detected_fields' => $url_detected_fields
            ];

            log_message('info', 'check_form: Finished. Overall has_vulgar: ' . ($has_vulgar ? 'Yes' : 'No') . ', has_url: ' . ($has_url ? 'Yes' : 'No'));
            if ($has_url) {
                log_message('debug', 'check_form: URLs detected in fields: ' . implode(', ', $url_detected_fields));
            }
            log_message('debug', 'check_form: All vulgar words found: ' . implode(', ', array_unique($all_vulgar_words)));

            return $final_result;

        } catch (Exception $e) {
            log_message('error', 'check_form: Exception caught: ' . $e->getMessage());
            log_message('error', 'check_form: Stack trace: ' . $e->getTraceAsString());

            return [
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบฟอร์ม: ' . $e->getMessage(),
                'has_vulgar' => false,
                'has_url' => false,
                'block_submit' => true, // บล็อกเพื่อความปลอดภัย
                'results' => [],
                'all_vulgar_words' => [],
                'url_detected_fields' => []
            ];
        }
    }


    public function q_a_chat($q_a_id)
    {
        log_message('info', '--- เริ่มการทำงานของฟังก์ชัน q_a_chat สำหรับ ID: ' . $q_a_id . ' ---');
        if (empty($q_a_id) || !is_numeric($q_a_id)) {
            log_message('error', 'q_a_chat: q_a_id ไม่ถูกต้อง: ' . $q_a_id);
            show_404();
            return;
        }

        $data['rsData'] = $this->q_a_model->read($q_a_id);
        $data['rsReply'] = $this->q_a_model->read_reply($q_a_id);

        log_message('debug', 'q_a_chat: ข้อมูลกระทู้ (rsData): ' . print_r($data['rsData'], true));
        log_message('debug', 'q_a_chat: จำนวนคำตอบ (rsReply): ' . count($data['rsReply']));


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/q_a_chat', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer');
        log_message('info', '--- สิ้นสุดการทำงานของฟังก์ชัน q_a_chat ---');
    }


    // ========================================================================
    // การแก้ไข add_reply_q_a() Method - เพิ่ม reCAPTCHA verification
    // ========================================================================

    public function add_reply_q_a()
    {
        try {
            log_message('info', '=== CONTROLLER ADD_REPLY_Q_A START - Enhanced with reCAPTCHA ===');

            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                log_message('debug', 'add_reply_q_a: เข้าถึงด้วย Method ที่ไม่ใช่ POST.');

                if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Method not allowed',
                        'error_type' => 'method_error'
                    ]);
                    return;
                }

                redirect('Pages/q_a');
                return;
            }

            // ✅ ใช้ฟังก์ชันตรวจสอบ reCAPTCHA ที่ปรับปรุงแล้ว
            $recaptcha_result = $this->verify_recaptcha_for_qa();

            if (!$recaptcha_result['success']) {
                log_message('error', 'add_reply_q_a: reCAPTCHA verification failed: ' . $recaptcha_result['message']);

                if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                    header('Content-Type: application/json');
                    echo json_encode($recaptcha_result);
                    return;
                }

                $this->session->set_flashdata('save_error', $recaptcha_result['message']);
                redirect('Pages/q_a');
                return;
            }

            log_message('info', 'add_reply_q_a: reCAPTCHA verified successfully with data: ' . json_encode([
                'action' => $recaptcha_result['recaptcha_action_used'] ?? 'unknown',
                'source' => $recaptcha_result['recaptcha_source_used'] ?? 'unknown',
                'user_type' => $recaptcha_result['user_type_detected'] ?? 'unknown'
            ]));

            $qa_model = $this->get_qa_model();
            if (!$qa_model) {
                log_message('error', 'add_reply_q_a: ไม่สามารถโหลด Q_a_model ได้');

                if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'ไม่สามารถโหลด model ได้',
                        'error_type' => 'system_error'
                    ]);
                    return;
                }

                $this->session->set_flashdata('save_error', 'ไม่สามารถโหลด model ได้');
                redirect('Pages/q_a');
                return;
            }

            log_message('info', 'add_reply_q_a: Calling Model add_reply_q_a method...');

            // *** เรียกใช้ Model method ***
            $result = $qa_model->add_reply_q_a();
            log_message('info', 'add_reply_q_a: Model result: ' . ($result ? 'Reply ID ' . $result : 'FALSE'));

            // *** ตรวจสอบ vulgar flash data ก่อน ***
            if ($this->session->flashdata('save_vulgar')) {
                $vulgar_words = $this->session->flashdata('vulgar_words') ?: array();
                $vulgar_message = $this->session->flashdata('vulgar_message') ?: 'พบคำไม่เหมาะสม';

                log_message('debug', 'add_reply_q_a: Vulgar content detected: ' . implode(', ', $vulgar_words));

                if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'vulgar_detected' => true,
                        'vulgar_words' => $vulgar_words,
                        'message' => $vulgar_message,
                        'error_type' => 'vulgar_content'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_vulgar', TRUE);
                $this->session->set_flashdata('vulgar_words', $vulgar_words);
                redirect('Pages/q_a');
                return;
            }

            // *** ตรวจสอบ URL detection flash data ***
            if ($this->session->flashdata('save_url_detected')) {
                $url_message = $this->session->flashdata('url_message') ?: 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';

                log_message('debug', 'add_reply_q_a: URL detected in reply content');

                if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'url_detected' => true,
                        'message' => $url_message,
                        'error_type' => 'url_content'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_error', $url_message);
                redirect('Pages/q_a');
                return;
            }

            // *** ตรวจสอบผลลัพธ์จาก Model ***
            if ($result) {
                log_message('info', 'add_reply_q_a: SUCCESS - Reply created with ID: ' . $result);

                if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => true,
                        'reply_id' => $result,
                        'message' => 'บันทึกการตอบกลับสำเร็จ',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'recaptcha_info' => [
                            'action_used' => $recaptcha_result['recaptcha_action_used'] ?? 'unknown',
                            'source_used' => $recaptcha_result['recaptcha_source_used'] ?? 'unknown',
                            'user_type' => $recaptcha_result['user_type_detected'] ?? 'unknown'
                        ]
                    ));
                    return;
                }

                $this->session->set_flashdata('save_success', 'บันทึกการตอบกลับสำเร็จ');
                $this->session->set_flashdata('new_reply_id', $result);
                redirect('Pages/q_a');

            } else {
                log_message('error', 'add_reply_q_a: FAILED - Model returned false');

                $error_message = 'เกิดข้อผิดพลาดในการบันทึกการตอบกลับ';

                if ($this->session->flashdata('save_error')) {
                    $error_message = $this->session->flashdata('save_error');
                } elseif ($this->session->flashdata('validation_error')) {
                    $error_message = $this->session->flashdata('validation_error');
                }

                if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'success' => false,
                        'message' => $error_message,
                        'error_type' => 'database_error'
                    ));
                    return;
                }

                $this->session->set_flashdata('save_error', $error_message);
                redirect('Pages/q_a');
            }

        } catch (Exception $e) {
            log_message('error', 'add_reply_q_a: Controller Exception: ' . $e->getMessage());
            log_message('error', 'add_reply_q_a: Exception trace: ' . $e->getTraceAsString());

            $error_message = 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage();

            if ($this->input->is_ajax_request() || $this->input->post('ajax_request')) {
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => false,
                    'message' => $error_message,
                    'error_type' => 'system_error',
                    'debug_info' => [
                        'exception_message' => $e->getMessage(),
                        'exception_file' => $e->getFile(),
                        'exception_line' => $e->getLine()
                    ]
                ));
                return;
            }

            $this->session->set_flashdata('save_error', 'เกิดข้อผิดพลาดระบบ');
            redirect('Pages/q_a');
        }
    }

    // ========================================================================
    // เพิ่มฟังก์ชันช่วย verify_recaptcha_for_qa() 
    // ========================================================================

    /**
     * ฟังก์ชันตรวจสอบ reCAPTCHA สำหรับ Q&A System
     * 
     * @return array ผลลัพธ์การตรวจสอบ
     */
    private function verify_recaptcha_for_qa()
    {
        $recaptcha_token = $this->input->post('g-recaptcha-response');

        if (empty($recaptcha_token)) {
            return [
                'success' => false,
                'message' => 'กรุณายืนยันตัวตน reCAPTCHA',
                'error_type' => 'recaptcha_missing'
            ];
        }

        // ✅ อ่านข้อมูล action และ source จาก JavaScript
        $recaptcha_action = $this->input->post('recaptcha_action') ?: 'default_action';
        $recaptcha_source = $this->input->post('recaptcha_source') ?: 'unknown';
        $user_type_detected = $this->input->post('user_type_detected') ?: 'unknown';

        log_message('debug', 'reCAPTCHA Debug: Data from JavaScript: ' . json_encode([
            'action' => $recaptcha_action,
            'source' => $recaptcha_source,
            'user_type_detected' => $user_type_detected,
            'token_length' => strlen($recaptcha_token)
        ]));

        // ตรวจสอบ user type จาก Model
        $qa_model = $this->get_qa_model();
        if (!$qa_model) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถโหลด model ได้',
                'error_type' => 'system_error'
            ];
        }

        $login_status = $qa_model->check_user_login();
        $current_user_type = $login_status['user_type'] ?? 'guest';

        // ✅ แปลงเป็น reCAPTCHA user type ตาม Controller logic เดิม
        $staff_types = ['staff', 'system_admin', 'super_admin', 'user_admin'];
        $recaptcha_user_type = in_array($current_user_type, $staff_types) ? 'staff' : 'citizen';

        // ✅ ใช้ action ที่ได้รับจาก JavaScript หรือใช้ fallback
        $expected_actions = [
            'staff' => 'qa_admin_submit',
            'citizen' => 'qa_public_submit'
        ];

        $expected_action = $expected_actions[$recaptcha_user_type] ?? 'qa_public_submit';

        // ✅ ตรวจสอบว่า action ที่ได้รับตรงกับที่คาดหวังหรือไม่
        if ($recaptcha_action !== 'default_action' && $recaptcha_action !== $expected_action) {
            log_message('debug', "reCAPTCHA Debug: Action mismatch: {$recaptcha_action} (expected: {$expected_action})");
        }

        // ✅ ใช้ action ที่ถูกต้อง
        $final_action = ($recaptcha_action !== 'default_action') ? $recaptcha_action : $expected_action;

        // ✅ กำหนด min_score ตาม user type
        $min_score = ($recaptcha_user_type === 'staff') ? 0.6 : 0.5;

        // ✅ เตรียม options array สำหรับส่งไปยัง library
        $options = [
            'action' => $final_action,
            'source' => $recaptcha_source,
            'user_type_detected' => $user_type_detected,
            'original_user_type' => $current_user_type
        ];

        log_message('info', "reCAPTCHA verification: {$current_user_type} -> {$recaptcha_user_type} | Action: {$final_action} | Source: {$recaptcha_source}");

        // ✅ เรียก reCAPTCHA library ด้วยพารามิเตอร์ที่ถูกต้อง
        log_message('debug', 'reCAPTCHA Debug: Calling library verify() with parameters: ' . json_encode([
            'user_type' => $recaptcha_user_type,
            'min_score' => $min_score,
            'options' => $options
        ]));

        $result = $this->recaptcha_lib->verify($recaptcha_token, $recaptcha_user_type, $min_score, $options);

        // ✅ เพิ่มข้อมูลเพิ่มเติมใน log
        log_message('debug', 'reCAPTCHA Debug: Library result: ' . json_encode([
            'success' => $result['success'],
            'message' => $result['message'] ?? 'N/A',
            'score' => isset($result['data']['score']) ? $result['data']['score'] : 'N/A',
            'final_action_used' => $final_action,
            'final_source_used' => $recaptcha_source
        ]));

        if (!$result['success']) {
            log_message('error', "reCAPTCHA failed: {$current_user_type} -> {$recaptcha_user_type}: " . $result['message']);
            return [
                'success' => false,
                'message' => 'การยืนยันตัวตนไม่ผ่าน กรุณาลองใหม่',
                'error_type' => 'recaptcha_failed',
                'user_type_detected' => $current_user_type,
                'recaptcha_action_used' => $final_action,
                'recaptcha_source_used' => $recaptcha_source,
                'debug_info' => [
                    'score' => isset($result['data']['score']) ? $result['data']['score'] : 'N/A',
                    'min_score_required' => $min_score,
                    'library_message' => $result['message']
                ]
            ];
        } else {
            log_message('info', "reCAPTCHA passed: {$current_user_type} -> {$recaptcha_user_type} | Final action: {$final_action} | Score: " . (isset($result['data']['score']) ? $result['data']['score'] : 'N/A'));
            return [
                'success' => true,
                'message' => 'reCAPTCHA verified successfully',
                'user_type_detected' => $current_user_type,
                'recaptcha_user_type' => $recaptcha_user_type,
                'recaptcha_action_used' => $final_action,
                'recaptcha_source_used' => $recaptcha_source,
                'verification_data' => [
                    'score' => isset($result['data']['score']) ? $result['data']['score'] : 'N/A',
                    'min_score_required' => $min_score,
                    'response_time' => isset($result['data']['response_time']) ? $result['data']['response_time'] : 'N/A'
                ]
            ];
        }
    }

    public function check_vulgar($text)
    {
        // Log นี้อาจทำให้ไฟล์ log ใหญ่เร็วมาก หากต้องการ debug เฉพาะกรณี ค่อยเปิดใช้งาน
        // log_message('debug', 'Callback check_vulgar: กำลังตรวจสอบข้อความ: "' . $text . '"');
        return $this->vulgar_check->check_vulgar($text);
    }


    public function check_no_urls($text)
    {
        // Log นี้อาจทำให้ไฟล์ log ใหญ่เร็วมาก หากต้องการ debug เฉพาะกรณี ค่อยเปิดใช้งาน
        // log_message('debug', 'Callback check_no_urls: กำลังตรวจสอบข้อความ: "' . $text . '"');
        return $this->vulgar_check->check_no_urls($text);
    }





    private function fix_user_id_overflow($login_status)
    {
        // ตรวจสอบว่า $login_status เป็น array และมีข้อมูลครบ
        if (!is_array($login_status) || !isset($login_status['is_logged_in']) || !$login_status['is_logged_in']) {
            return array(
                'is_logged_in' => false,
                'user_info' => array('name' => '', 'email' => '', 'user_id' => null),
                'user_type' => 'guest',
                'user_id' => null,
                'name' => '',
                'email' => ''
            );
        }

        $fixed_info = $login_status;

        // ✅ ดึงข้อมูลจาก user_info มาไว้ที่ root level
        if (isset($login_status['user_info']) && is_array($login_status['user_info'])) {
            $user_info = $login_status['user_info'];

            // นำข้อมูลสำคัญมาไว้ที่ root level เพื่อให้ใช้งานง่าย
            $fixed_info['name'] = $user_info['name'] ?? '';
            $fixed_info['email'] = $user_info['email'] ?? '';  // ✅ ดึง email จาก user_info
            $fixed_info['username'] = $user_info['username'] ?? '';
            $fixed_info['user_id'] = $user_info['user_id'] ?? null;

            // ตรวจสอบและแก้ไข user_id overflow (เหมือนเดิม)
            if (isset($user_info['user_id'])) {
                $user_id = $user_info['user_id'];

                if (is_numeric($user_id) && ($user_id < 0 || $user_id > 2147483647)) {
                    log_message('error', 'User ID overflow detected: ' . $user_id);

                    $fixed_user_id = abs($user_id) % 2147483647;
                    if ($fixed_user_id == 0) {
                        $fixed_user_id = null;
                    }

                    $fixed_info['user_info']['user_id'] = $fixed_user_id;
                    $fixed_info['user_id'] = $fixed_user_id;

                    log_message('info', 'Fixed user_id from ' . $user_id . ' to ' . $fixed_user_id);
                }
            }

            // Debug ข้อมูล
            log_message('info', 'User email from model: ' . $fixed_info['email']);
            log_message('info', 'User name from model: ' . $fixed_info['name']);
        }

        // ตรวจสอบว่ามีค่าเริ่มต้นที่จำเป็น
        if (!isset($fixed_info['user_type'])) {
            $fixed_info['user_type'] = $login_status['user_type'] ?? 'guest';
        }

        if (!isset($fixed_info['user_id'])) {
            $fixed_info['user_id'] = null;
        }

        return $fixed_info;
    }


    //public หน้าระบบบริการอิเล็กทรอนิกส์  
    public function service_systems()
    {
        // ตรวจสอบการ login
        if (!$this->session->userdata('mp_id')) {
            redirect('User');
            return;
        }

        // โหลด Library และ Model สำหรับ notifications
        $this->load->library('notification_lib');
        $this->load->model('Notification_model');
        $this->load->helper('timeago');

        // เตรียมข้อมูลการแจ้งเตือนด้วยระบบใหม่ (Individual Read Status)
        $data = [];
        try {
            // เปลี่ยนจากการใช้ Model โดยตรงเป็นใช้ Library
            $data['notifications'] = $this->notification_lib->get_user_notifications('public', 5);
            $data['unread_count'] = $this->notification_lib->get_unread_count('public');
            $data['total_notifications'] = $this->Notification_model->count_notifications_by_role('public');

        } catch (Exception $e) {
            log_message('error', 'Failed to load notifications in service_systems: ' . $e->getMessage());

            // ถ้าเกิดข้อผิดพลาดให้ใช้ค่าเริ่มต้น
            $data['notifications'] = [];
            $data['unread_count'] = 0;
            $data['total_notifications'] = 0;

            // ลองใช้ระบบเก่าเป็น fallback
            try {
                $data['notifications'] = $this->Notification_model->get_notifications_by_role('public', 5, 0);
                $data['unread_count'] = $this->Notification_model->count_unread_by_role('public');
            } catch (Exception $fallback_error) {
                log_message('error', 'Fallback also failed: ' . $fallback_error->getMessage());
            }
        }

        // โหลด Views พร้อมส่งข้อมูล
        $this->load->view('public_user/templates/header');
        $this->load->view('public_user/css');
        $this->load->view('public_user/e_service', $data); // ส่งข้อมูล notifications ไปด้วย
        $this->load->view('public_user/js');
        $this->load->view('public_user/templates/footer');
    }





    private function safe_create_reply_notification($ref_id, $reply_by, $detail, $reply_id)
    {
        try {
            // ตรวจสอบว่ามี method createReplyNotification หรือไม่
            if (method_exists($this, 'createReplyNotification')) {
                $this->createReplyNotification($ref_id, $reply_by, $detail, $reply_id);
            } else {
                // สร้าง notification แบบง่าย
                $notification_data = array(
                    'notification_type' => 'reply',
                    'notification_ref_id' => $ref_id,
                    'notification_message' => 'มีการตอบกระทู้ใหม่โดย ' . $reply_by,
                    'notification_date' => date('Y-m-d H:i:s'),
                    'notification_status' => 'unread'
                );

                // ตรวจสอบว่ามีตาราง notification หรือไม่
                if ($this->db->table_exists('tbl_notifications')) {
                    $this->db->insert('tbl_notifications', $notification_data);
                }
            }
        } catch (Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Notification error: ' . $e->getMessage());
            }
            // ไม่ส่ง error ออกไป เพราะไม่ใช่ส่วนสำคัญ
        }
    }


    private function safe_handle_reply_images($reply_id)
    {
        try {
            if (!empty($_FILES['q_a_reply_imgs']['name'][0])) {
                $this->load->library('upload');

                $upload_path = './docs/img/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }

                $config = array(
                    'upload_path' => $upload_path,
                    'allowed_types' => 'gif|jpg|png|jpeg|webp',
                    'max_size' => 5120, // 5MB
                    'encrypt_name' => TRUE
                );

                $this->upload->initialize($config);

                $files = $_FILES['q_a_reply_imgs'];
                $file_count = count($files['name']);

                for ($i = 0; $i < $file_count; $i++) {
                    if (!empty($files['name'][$i])) {
                        $_FILES['single_file'] = array(
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        );

                        if ($this->upload->do_upload('single_file')) {
                            $upload_data = $this->upload->data();

                            $image_data = array(
                                'q_a_reply_img_ref_id' => $reply_id,
                                'q_a_reply_img_img' => $upload_data['file_name']
                            );

                            $this->db->insert('tbl_q_a_reply_img', $image_data);
                        } else {
                            if (function_exists('log_message')) {
                                log_message('error', 'Reply image upload error: ' . $this->upload->display_errors());
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Image upload error: ' . $e->getMessage());
            }
        }
    }


    private function safe_fix_user_id_overflow($login_status)
    {
        // กำหนดค่าเริ่มต้น
        $default_info = array(
            'is_logged_in' => false,
            'user_info' => array(
                'name' => '',
                'email' => '',
                'user_id' => null
            ),
            'user_type' => 'guest',
            'user_id' => null
        );

        // ตรวจสอบว่า $login_status เป็น array
        if (!is_array($login_status)) {
            return $default_info;
        }

        $fixed_info = array_merge($default_info, $login_status);

        // ตรวจสอบและแก้ไข user_id overflow
        if (isset($login_status['user_info']['user_id'])) {
            $user_id = $login_status['user_info']['user_id'];

            if (is_numeric($user_id)) {
                // ตรวจสอบ overflow
                if ($user_id < 0 || $user_id > 2147483647) {
                    $fixed_user_id = abs(intval($user_id)) % 2147483647;
                    if ($fixed_user_id == 0) {
                        $fixed_user_id = null;
                    }

                    $fixed_info['user_info']['user_id'] = $fixed_user_id;
                    $fixed_info['user_id'] = $fixed_user_id;

                    // แก้ไข: ตรวจสอบว่า function มีอยู่ก่อนใช้
                    if (function_exists('log_message')) {
                        // แก้ไข: เปลี่ยนจาก 'debug' เป็น 'info'
                        log_message('info', 'Fixed user_id from ' . $user_id . ' to ' . $fixed_user_id);
                    }
                }
            }
        }

        return $fixed_info;
    }


    // ฟังก์ชันจัดการรูปภาพแบบง่าย
    private function simple_handle_reply_images($reply_id)
    {
        if (!empty($_FILES['q_a_reply_imgs']['name'][0])) {
            $this->load->library('upload');

            $upload_path = './docs/img/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config = array(
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|png|jpeg|webp',
                'max_size' => 5120, // 5MB
                'encrypt_name' => TRUE
            );

            $this->upload->initialize($config);

            $files = $_FILES['q_a_reply_imgs'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if (!empty($files['name'][$i])) {
                    $_FILES['single_file'] = array(
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    );

                    if ($this->upload->do_upload('single_file')) {
                        $upload_data = $this->upload->data();

                        $image_data = array(
                            'q_a_reply_img_ref_id' => $reply_id,
                            'q_a_reply_img_img' => $upload_data['file_name']
                        );

                        $this->db->insert('tbl_q_a_reply_img', $image_data);
                        log_message('debug', 'Reply image uploaded: ' . $upload_data['file_name']);
                    } else {
                        log_message('error', 'Reply image upload error: ' . $this->upload->display_errors());
                    }
                }
            }
        }
    }




    private function createReplyNotification($q_a_id, $reply_by, $reply_detail, $reply_id)
    {
        try {
            log_message('info', "Creating reply notification for Q&A {$q_a_id} by {$reply_by}...");

            // ดึงข้อมูลกระทู้เดิม
            $original_topic = $this->db->select('q_a_msg, q_a_by, q_a_user_id, q_a_email')
                ->where('q_a_id', $q_a_id)
                ->get('tbl_q_a')
                ->row();

            if (!$original_topic) {
                log_message('error', 'Original topic not found for reply notification: ' . $q_a_id);
                return false;
            }

            // ตรวจสอบและโหลด Notification_lib
            $this->ensure_notification_lib();

            // วิธีที่ 1: ใช้ Notification_lib (ถ้ามี)
            if (isset($this->Notification_lib) && method_exists($this->Notification_lib, 'qa_reply')) {
                log_message('info', 'Using Notification_lib->qa_reply method');

                try {
                    $notification_result = $this->Notification_lib->qa_reply($q_a_id, $reply_by, $reply_detail);

                    if ($notification_result) {
                        log_message('info', 'SUCCESS: Reply notification created via Notification_lib');
                        // ยังต้องสร้างใน tbl_notification ด้วย
                    }
                } catch (Exception $e) {
                    log_message('error', 'EXCEPTION in Notification_lib->qa_reply: ' . $e->getMessage());
                }
            }

            // วิธีที่ 2: สร้างการแจ้งเตือนโดยตรง (หลัก)
            if ($this->db->table_exists('tbl_notification')) {
                log_message('info', 'Creating reply notification directly in database');

                // ตัดข้อความให้สั้นลง
                $short_detail = mb_strlen($reply_detail) > 100 ?
                    mb_substr($reply_detail, 0, 100) . '...' :
                    $reply_detail;

                $notification_data = [
                    'title' => 'ตอบกระทู้', // *** เพิ่ม: ใส่คำว่า "ตอบกระทู้" ใน title ***
                    'notification_title' => 'มีการตอบกระทู้: ' . $original_topic->q_a_msg,
                    'notification_message' => $reply_by . ' ได้ตอบกระทู้ "' . $original_topic->q_a_msg . '": ' . $short_detail,
                    'notification_type' => 'qa_reply',
                    'notification_ref_id' => $q_a_id,
                    'notification_from_user' => $reply_by,
                    'notification_date' => date('Y-m-d H:i:s'),
                    'notification_status' => 'unread'
                ];

                // เพิ่ม fields เพิ่มเติมถ้ามี
                if ($this->db->field_exists('notification_url', 'tbl_notification')) {
                    $notification_data['notification_url'] = 'Pages/q_a#comment-' . $q_a_id;
                }

                if ($this->db->field_exists('notification_icon', 'tbl_notification')) {
                    $notification_data['notification_icon'] = 'fas fa-reply';
                }

                if ($this->db->field_exists('notification_priority', 'tbl_notification')) {
                    $notification_data['notification_priority'] = 'normal';
                }

                // เพิ่ม user_id ของผู้ตอบ
                $current_user_id = $this->getCurrentUserId();
                if ($current_user_id && $this->db->field_exists('notification_user_id', 'tbl_notification')) {
                    $notification_data['notification_user_id'] = $current_user_id;
                }

                // เพิ่ม reply_id ถ้ามี field นี้
                if ($this->db->field_exists('notification_reply_id', 'tbl_notification')) {
                    $notification_data['notification_reply_id'] = $reply_id;
                }

                // เพิ่ม target_user_id (ผู้ที่จะได้รับการแจ้งเตือน = เจ้าของกระทู้)
                if ($this->db->field_exists('notification_target_user_id', 'tbl_notification')) {
                    $notification_data['notification_target_user_id'] = $original_topic->q_a_user_id;
                }

                log_message('info', 'Reply notification data: ' . print_r($notification_data, true));

                $insert_result = $this->db->insert('tbl_notification', $notification_data);

                if ($insert_result) {
                    $notification_id = $this->db->insert_id();
                    log_message('info', 'SUCCESS: Direct reply notification created with ID: ' . $notification_id);
                    return true;
                } else {
                    log_message('error', 'FAILED: Direct reply notification insert failed');
                    log_message('error', 'DB Error: ' . print_r($this->db->error(), true));
                }
            } else {
                log_message('debug', 'tbl_notification table does not exist');
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Exception in createReplyNotification: ' . $e->getMessage());
            return false;
        }
    }




    private function handle_reply_images($reply_id)
    {
        if (!empty($_FILES['q_a_reply_imgs']['name'][0])) {
            $this->load->library('upload');

            $upload_path = './docs/img/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|png|jpeg|webp',
                'max_size' => 5120, // 5MB
                'encrypt_name' => true
            ];

            $this->upload->initialize($config);

            $files = $_FILES['q_a_reply_imgs'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if (!empty($files['name'][$i])) {
                    $_FILES['single_file'] = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    if ($this->upload->do_upload('single_file')) {
                        $upload_data = $this->upload->data();

                        // สร้างไฟล์สำหรับ LINE (ถ้าต้องการ)
                        $file_ext = pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                        $line_filename = 'line_reply_' . time() . '_' . uniqid() . '.' . $file_ext;
                        $final_line_filename = $upload_data['file_name']; // default

                        if (copy($upload_data['full_path'], $upload_path . $line_filename)) {
                            $final_line_filename = $line_filename;
                        }

                        $image_data = [
                            'q_a_reply_img_ref_id' => $reply_id,
                            'q_a_reply_img_img' => $upload_data['file_name']
                        ];

                        // เพิ่ม line filename ถ้ามี field นี้
                        if ($this->db->field_exists('q_a_reply_img_line', 'tbl_q_a_reply_img')) {
                            $image_data['q_a_reply_img_line'] = $final_line_filename;
                        }

                        $this->db->insert('tbl_q_a_reply_img', $image_data);
                        log_message('info', 'Reply image uploaded: ' . $upload_data['file_name']);
                    } else {
                        log_message('error', 'Reply image upload error: ' . $this->upload->display_errors());
                    }
                }
            }
        }
    }



    private function process_qa_images($q_a_id)
    {
        // ตรวจสอบว่ามีไฟล์อัพโหลดหรือไม่
        if (empty($_FILES['q_a_imgs']['name'][0])) {
            return;
        }

        // ตั้งค่าการอัพโหลด
        $upload_path = './docs/img/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = true;

        $this->upload->initialize($config);

        // จัดการไฟล์แต่ละไฟล์
        $files = $_FILES['q_a_imgs'];
        $file_count = count($files['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if (empty($files['name'][$i]))
                continue;

            // เตรียมข้อมูลไฟล์
            $_FILES['single_file'] = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];

            // อัพโหลดไฟล์
            if ($this->upload->do_upload('single_file')) {
                $upload_data = $this->upload->data();
                $uploaded_filename = $upload_data['file_name'];

                // บันทึกลงฐานข้อมูล
                $img_data = [
                    'q_a_img_ref_id' => $q_a_id,
                    'q_a_img_img' => $uploaded_filename
                ];
                $this->db->insert('tbl_q_a_img', $img_data);

                log_message('info', "Q&A Image uploaded: {$uploaded_filename} for Q&A ID: {$q_a_id}");
            } else {
                $error = $this->upload->display_errors('', '');
                log_message('error', "Q&A Image upload failed: {$error}");
            }
        }
    }

    /**
     * ฟังก์ชันประมวลผลรูปภาพสำหรับ Reply
     */
    private function process_reply_images($reply_id)
    {
        try {
            log_message('info', 'Starting process_reply_images for reply_id: ' . $reply_id);

            // ตรวจสอบว่ามีไฟล์อัพโหลดหรือไม่
            if (empty($_FILES['q_a_reply_imgs']['name'][0])) {
                log_message('info', 'No files to upload');
                return true;
            }

            // ตรวจสอบและสร้างโฟลเดอร์ถ้าไม่มี
            $upload_path = './docs/img/';
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', 'Cannot create upload directory: ' . $upload_path);
                    throw new Exception('ไม่สามารถสร้างโฟลเดอร์สำหรับอัพโหลดได้');
                }
            }

            // ตรวจสอบ permission
            if (!is_writable($upload_path)) {
                log_message('error', 'Upload directory is not writable: ' . $upload_path);
                throw new Exception('ไม่มีสิทธิ์เขียนไฟล์ในโฟลเดอร์อัพโหลด');
            }

            // กำหนดค่า upload config
            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|jpeg|png|webp',
                'max_size' => 5120, // 5MB
                'encrypt_name' => true,
                'remove_spaces' => true,
                'detect_mime' => true
            ];

            // โหลด upload library
            $this->load->library('upload');
            $this->upload->initialize($config);

            $files = $_FILES['q_a_reply_imgs'];
            $file_count = count($files['name']);
            $uploaded_count = 0;
            $errors = [];

            log_message('info', 'Processing ' . $file_count . ' files for reply_id: ' . $reply_id);

            for ($i = 0; $i < $file_count; $i++) {
                // ข้าม file ที่ว่าง
                if (empty($files['name'][$i]) || $files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                // ตรวจสอบ upload error
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    $error_msg = $this->getUploadErrorMessage($files['error'][$i]);
                    log_message('error', "File upload error for file {$i}: {$error_msg}");
                    $errors[] = "ไฟล์ {$files['name'][$i]}: {$error_msg}";
                    continue;
                }

                // ตรวจสอบขนาดไฟล์
                if ($files['size'][$i] > (5 * 1024 * 1024)) { // 5MB
                    log_message('error', "File too large: {$files['name'][$i]} ({$files['size'][$i]} bytes)");
                    $errors[] = "ไฟล์ {$files['name'][$i]} มีขนาดใหญ่เกินไป";
                    continue;
                }

                // ตรวจสอบประเภทไฟล์
                $allowed_mime_types = [
                    'image/gif',
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'image/webp'
                ];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detected_mime = finfo_file($finfo, $files['tmp_name'][$i]);
                finfo_close($finfo);

                if (!in_array($detected_mime, $allowed_mime_types)) {
                    log_message('error', "Invalid file type: {$files['name'][$i]} (detected: {$detected_mime})");
                    $errors[] = "ไฟล์ {$files['name'][$i]} ประเภทไฟล์ไม่ถูกต้อง";
                    continue;
                }

                // สร้าง temporary $_FILES สำหรับ CodeIgniter upload
                $_FILES['single_file'] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];

                // ทำการอัพโหลด
                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();
                    $uploaded_filename = $upload_data['file_name'];

                    // ตรวจสอบว่าตาราง tbl_q_a_reply_img มีอยู่จริง
                    if (!$this->db->table_exists('tbl_q_a_reply_img')) {
                        log_message('error', 'Table tbl_q_a_reply_img does not exist');
                        throw new Exception('ตารางฐานข้อมูลไม่พร้อมใช้งาน');
                    }

                    // บันทึกลงตาราง tbl_q_a_reply_img
                    $img_data = [
                        'q_a_reply_img_ref_id' => $reply_id,
                        'q_a_reply_img_img' => $uploaded_filename,
                        'q_a_reply_img_date' => date('Y-m-d H:i:s') // เพิ่ม timestamp ถ้าตารางมี column นี้
                    ];

                    // ตรวจสอบ column ที่มีอยู่จริงในตาราง
                    $fields = $this->db->list_fields('tbl_q_a_reply_img');
                    $filtered_data = array_intersect_key($img_data, array_flip($fields));

                    $insert_result = $this->db->insert('tbl_q_a_reply_img', $filtered_data);

                    if (!$insert_result) {
                        log_message('error', 'Database insert failed for image: ' . $uploaded_filename);
                        // ลบไฟล์ที่อัพโหลดแล้วเนื่องจาก database error
                        @unlink($upload_path . $uploaded_filename);
                        throw new Exception('ไม่สามารถบันทึกข้อมูลรูปภาพลงฐานข้อมูลได้');
                    }

                    $uploaded_count++;
                    log_message('info', "Reply Image uploaded successfully: {$uploaded_filename} for Reply ID: {$reply_id}");

                } else {
                    $upload_errors = $this->upload->display_errors('', '');
                    log_message('error', "Reply Image upload failed for file {$i}: {$upload_errors}");
                    $errors[] = "ไฟล์ {$files['name'][$i]}: {$upload_errors}";
                }

                // ล้าง temporary $_FILES
                unset($_FILES['single_file']);
            }

            log_message('info', "Image upload completed. Uploaded: {$uploaded_count}, Errors: " . count($errors));

            // ถ้ามี error แต่มีไฟล์อัพโหลดสำเร็จบ้าง ให้ log warning
            if (!empty($errors) && $uploaded_count > 0) {
                log_message('debug', 'Some files failed to upload: ' . implode(', ', $errors));
            }

            // ถ้าทุกไฟล์ล้มเหลว ให้ throw exception
            if ($uploaded_count === 0 && !empty($errors)) {
                throw new Exception('ไม่สามารถอัพโหลดไฟล์ใดได้: ' . implode(', ', $errors));
            }

            return true;

        } catch (Exception $e) {
            log_message('error', 'process_reply_images exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Re-throw เพื่อให้ caller จัดการต่อ
        }
    }

    /**
     * ฟังก์ชันแปล upload error code เป็นข้อความ
     */
    private function getUploadErrorMessage($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'ไฟล์มีขนาดใหญ่เกิน upload_max_filesize';
            case UPLOAD_ERR_FORM_SIZE:
                return 'ไฟล์มีขนาดใหญ่เกิน MAX_FILE_SIZE';
            case UPLOAD_ERR_PARTIAL:
                return 'ไฟล์อัพโหลดไม่สมบูรณ์';
            case UPLOAD_ERR_NO_FILE:
                return 'ไม่มีไฟล์อัพโหลด';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'ไม่มีโฟลเดอร์ temporary';
            case UPLOAD_ERR_CANT_WRITE:
                return 'ไม่สามารถเขียนไฟล์ลงดิสก์';
            case UPLOAD_ERR_EXTENSION:
                return 'การอัพโหลดถูกหยุดโดย PHP extension';
            default:
                return 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ (Error: ' . $error_code . ')';
        }
    }
    // API สำหรับตรวจสอบสถานะการเข้าสู่ระบบ (สำหรับ AJAX)
    public function check_login_status()
    {
        $login_status = $this->q_a_model->check_user_login();

        $response = array(
            'success' => true,
            'is_logged_in' => $login_status['is_logged_in'],
            'user_type' => $login_status['user_type'],
            'user_info' => $login_status['user_info']
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }



    /**
     * ฟังก์ชันดึงข้อมูลกระทู้สำหรับแก้ไข
     */
    public function get_topic_data($topic_id)
    {
        try {
            if (!$topic_id) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสกระทู้']);
                return;
            }

            // ดึงข้อมูลผู้ใช้ที่แก้ไขแล้ว
            $user_info = $this->get_fixed_user_info();

            // ตรวจสอบสิทธิ์ในการแก้ไข
            $topic = $this->db->where('q_a_id', $topic_id)->get('tbl_q_a')->row();

            if (!$topic) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบกระทู้']);
                return;
            }

            $can_edit = false;

            if ($user_info['is_logged_in']) {
                // Staff/Admin แก้ไขได้ทุกกระทู้
                if (in_array($user_info['user_type'], ['staff', 'system_admin', 'super_admin', 'user_admin'])) {
                    $can_edit = true;
                }
                // เจ้าของกระทู้แก้ไขได้
                elseif ($topic->q_a_user_id == $user_info['user_id']) {
                    $can_edit = true;
                }
            } else {
                // Guest user ตรวจสอบ session (ถ้าต้องการ)
                if ($topic->q_a_user_type === 'guest') {
                    $can_edit = true; // อนุญาตให้แก้ไขได้ชั่วคราว
                }
            }

            if (!$can_edit) {
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์แก้ไขกระทู้นี้']);
                return;
            }

            // ดึงรูปภาพที่เกี่ยวข้อง
            $images = $this->db->where('q_a_img_ref_id', $topic_id)
                ->get('tbl_q_a_img')
                ->result();

            echo json_encode([
                'success' => true,
                'topic' => $topic,
                'images' => $images
            ]);

        } catch (Exception $e) {
            log_message('error', 'Get topic data error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล']);
        }
    }


    /**
     * 🔧 Enhanced update_topic() Method - เพิ่ม Vulgar check และ URL check
     * 
     * ลำดับการทำงาน:
     * 1. Validation เบื้องต้น
     * 2. Permission check
     * 3. Vulgar check
     * 4. URL check
     * 5. Image upload
     * 6. Database update
     * 7. Response
     */
    public function update_topic()
    {
        // เปิด error reporting เพื่อ debug
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // ปิดเพื่อไม่ให้แสดงใน JSON response

        // Log การเริ่มต้น
        log_message('info', '=== UPDATE TOPIC ENHANCED START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));
        log_message('info', 'FILES data: ' . print_r($_FILES, true));

        try {
            // ========================================================================
            // 1. การตรวจสอบเบื้องต้น (Initial Validation)
            // ========================================================================

            // ตรวจสอบ HTTP Method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                log_message('error', 'Invalid HTTP method: ' . $this->input->server('REQUEST_METHOD'));
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed', 'error_type' => 'method_error']);
                exit();
            }

            // รับข้อมูลจากฟอร์ม
            $topic_id = $this->input->post('topic_id');
            $q_a_msg = $this->input->post('q_a_msg');
            $q_a_detail = $this->input->post('q_a_detail');
            $q_a_by = $this->input->post('q_a_by');
            $q_a_email = $this->input->post('q_a_email');

            log_message('info', "Received data - ID: {$topic_id}, MSG: {$q_a_msg}, BY: {$q_a_by}");

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($topic_id) || empty($q_a_msg) || empty($q_a_detail) || empty($q_a_by)) {
                log_message('error', 'Missing required fields');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน',
                    'error_type' => 'validation_error'
                ]);
                exit();
            }

            // ตรวจสอบว่ามีกระทู้นี้อยู่จริงหรือไม่
            $this->db->select('q_a_id, q_a_user_type, q_a_user_id, q_a_by, q_a_email, q_a_msg, q_a_detail');
            $this->db->where('q_a_id', $topic_id);
            $query = $this->db->get('tbl_q_a');

            if ($this->db->error()['code'] !== 0) {
                log_message('error', 'Database error: ' . print_r($this->db->error(), true));
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection error',
                    'error_type' => 'database_error'
                ]);
                exit();
            }

            $existing_topic = $query->row();
            log_message('info', 'Existing topic: ' . print_r($existing_topic, true));

            if (!$existing_topic) {
                log_message('error', 'Topic not found: ' . $topic_id);
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบกระทู้ที่ต้องการแก้ไข',
                    'error_type' => 'topic_not_found'
                ]);
                exit();
            }

            // ========================================================================
            // 2. ตรวจสอบสิทธิ์การแก้ไข (Permission Check)
            // ========================================================================

            $can_edit = $this->checkEditPermission($existing_topic);
            log_message('info', 'Can edit: ' . ($can_edit ? 'YES' : 'NO'));

            if (!$can_edit) {
                log_message('error', 'No permission to edit topic: ' . $topic_id);
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์แก้ไขกระทู้นี้',
                    'error_type' => 'permission_denied'
                ]);
                exit();
            }

            // ========================================================================
            // 3. ตรวจสอบคำหยาบ (Vulgar Check)
            // ========================================================================
// ========================================================================
// 3. ตรวจสอบคำหยาบ (Vulgar Check) - ANALYSIS & FIX
// ========================================================================

            // 🔍 ปัญหาที่พบ:
// 1. ใช้ $_POST['q_a_ref_id'] แต่ Model ต้องการ $_POST['q_a_reply_ref_id']
// 2. การเรียก add_reply_q_a() อาจไม่เข้าถึง vulgar check logic
// 3. Flash data อาจไม่ถูกตั้งค่าเพราะ Model return เร็ว

            log_message('info', 'Starting vulgar content check...');

            // *** รับข้อมูลจาก POST ***
            $topic_id = $this->input->post('topic_id');
            $q_a_msg = $this->input->post('q_a_msg');
            $q_a_detail = $this->input->post('q_a_detail');
            $q_a_by = $this->input->post('q_a_by');
            $q_a_email = $this->input->post('q_a_email');

            $combined_content = $q_a_msg . ' ' . $q_a_detail;

            log_message('info', 'Combined content to check: ' . $combined_content);
            log_message('debug', 'Topic ID: ' . $topic_id . ', By: ' . $q_a_by . ', Email: ' . $q_a_email);

            // *** โหลด Q_a_model ***
            $qa_model = $this->get_qa_model();
            if (!$qa_model) {
                log_message('error', 'update_topic: ไม่สามารถโหลด Q_a_model ได้');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถโหลด model ได้',
                    'error_type' => 'system_error'
                ]);
                exit();
            }

            log_message('info', 'Q_a_model loaded successfully');

            // *** 🔧 แก้ไข: ใช้ vulgar_check library โดยตรงแทนการเรียก add_reply_q_a() ***
            $this->load->library('vulgar_check');

            // เตรียม field data สำหรับการตรวจสอบ
            $check_fields = [
                'q_a_msg' => $q_a_msg,
                'q_a_detail' => $q_a_detail,
                'q_a_by' => $q_a_by,
                'q_a_email' => $q_a_email
            ];

            log_message('info', 'Checking vulgar content with fields: ' . json_encode(array_keys($check_fields)));

            // *** เรียกใช้ vulgar_check library โดยตรง ***
            $vulgar_result = $this->vulgar_check->check_form($check_fields);

            log_message('debug', 'Vulgar check result: ' . json_encode($vulgar_result));

            // *** ตรวจสอบผลลัพธ์ ***
            $vulgar_detected = false;
            $vulgar_words = array();
            $vulgar_message = '';

            if (isset($vulgar_result['has_vulgar']) && $vulgar_result['has_vulgar']) {
                $vulgar_detected = true;

                // รวบรวมคำหยาบจากทุก field
                $all_vulgar_words = [];
                if (isset($vulgar_result['results']) && is_array($vulgar_result['results'])) {
                    foreach ($vulgar_result['results'] as $field => $field_result) {
                        if (isset($field_result['vulgar_words']) && is_array($field_result['vulgar_words'])) {
                            $all_vulgar_words = array_merge($all_vulgar_words, $field_result['vulgar_words']);
                        }
                    }
                }

                $vulgar_words = array_unique($all_vulgar_words);
                $vulgar_message = 'พบคำไม่เหมาะสม: ' . implode(', ', $vulgar_words);

                log_message('debug', 'Vulgar words detected in topic update: ' . implode(', ', $vulgar_words));
            }

            // *** ตรวจสอบ URL detection ***
            $url_detected = false;
            if (isset($vulgar_result['has_url']) && $vulgar_result['has_url']) {
                $url_detected = true;
                $vulgar_message = 'ไม่อนุญาตให้มี URL ในข้อความ';

                log_message('debug', 'URL detected in topic update content');
            }

            // *** ส่ง JSON Response เมื่อพบคำหยาบหรือ URL ***
            if ($vulgar_detected || $url_detected) {

                // ตั้งค่า flash data สำหรับการแสดงผล
                if ($vulgar_detected) {
                    $this->session->set_flashdata('save_vulgar', true);
                    $this->session->set_flashdata('vulgar_words', $vulgar_words);
                    $this->session->set_flashdata('vulgar_message', $vulgar_message);
                }

                if ($url_detected) {
                    $this->session->set_flashdata('save_url_detected', true);
                    $this->session->set_flashdata('url_message', $vulgar_message);
                }

                // ปิด output buffering
                while (ob_get_level()) {
                    ob_end_clean();
                }

                // ตั้งค่า headers
                header('Content-Type: application/json; charset=utf-8');
                header('Cache-Control: no-cache, must-revalidate');
                http_response_code(400);

                // ส่ง JSON response
                $response_data = [
                    'success' => false,
                    'message' => $vulgar_message,
                    'error_type' => $vulgar_detected ? 'vulgar_content' : 'url_content'
                ];

                if ($vulgar_detected) {
                    $response_data['vulgar_detected'] = true;
                    $response_data['vulgar_words'] = $vulgar_words;
                }

                if ($url_detected) {
                    $response_data['url_detected'] = true;
                }

                $json_response = json_encode($response_data, JSON_UNESCAPED_UNICODE);
                log_message('info', 'Sending vulgar/URL detection JSON response: ' . $json_response);

                echo $json_response;

                if (function_exists('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                } else {
                    flush();
                }

                exit();
            }

            log_message('info', 'Vulgar and URL check passed - no inappropriate content detected');

            // *** Debug log สำหรับการทดสอบ ***
            if (stripos($combined_content, 'สุภาพ') !== false) {
                log_message('debug', 'TEST WORD "สุภาพ" found but not detected by vulgar system!');
                log_message('debug', 'Content checked: ' . $combined_content);
                log_message('debug', 'Vulgar check result: ' . json_encode($vulgar_result));
                log_message('debug', 'Please check vulgar detection configuration');
            }

            if (stripos($combined_content, 'Son of a bitch') !== false) {
                log_message('debug', 'TEST PHRASE "Son of a bitch" found but not detected by vulgar system!');
                log_message('debug', 'Content checked: ' . $combined_content);
                log_message('debug', 'Vulgar check result: ' . json_encode($vulgar_result));
                log_message('debug', 'Please verify API connection and word database');
            }

            // *** เพิ่มการตรวจสอบว่าควรจะพบคำหยาบหรือไม่ ***
            if (stripos($combined_content, 'สุภาพ') !== false) {
                log_message('debug', 'TEST WORD "สุภาพ" found but not detected by vulgar system!');
                log_message('debug', 'Content checked: ' . $combined_content);
                log_message('debug', 'Please check vulgar detection configuration');
            }


            // ========================================================================
            // 4. ตรวจสอบ URL (URL Check) - FIXED VERSION
            // ========================================================================

            log_message('info', 'Starting URL detection check...');
            $url_detected = false;
            $url_message = '';

            // ตรวจสอบผ่าน Model method หรือ Library
            if (method_exists($this, 'checkForUrls')) {
                $url_result = $this->checkForUrls($combined_content);
                if ($url_result) {
                    $url_detected = true;
                    $url_message = is_array($url_result) && isset($url_result['message'])
                        ? $url_result['message']
                        : 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';
                }
            } elseif (method_exists($this, 'detect_url')) {
                $url_result = $this->detect_url($combined_content);
                if ($url_result) {
                    $url_detected = true;
                    $url_message = 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';
                }
            }

            // ถ้ามี url detection library
            if (!$url_detected && isset($this->url_detection_lib)) {
                $url_result = $this->url_detection_lib->check($combined_content);
                if ($url_result['detected']) {
                    $url_detected = true;
                    $url_message = $url_result['message'] ?? 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';
                }
            }

            // Simple URL pattern check ถ้าไม่มี method อื่น
            if (!$url_detected) {
                $url_patterns = [
                    '/https?:\/\//i',
                    '/www\./i',
                    '/[a-zA-Z0-9-]+\.[a-zA-Z]{2,}/i',
                    '/bit\.ly|tinyurl|short\.link/i'
                ];

                foreach ($url_patterns as $pattern) {
                    if (preg_match($pattern, $combined_content)) {
                        $url_detected = true;
                        $url_message = 'ไม่อนุญาตให้มี URL หรือลิงก์ในข้อความ';
                        break;
                    }
                }
            }

            // *** 🔥 ส่วนที่ต้องเพิ่ม: ส่ง JSON Response ทันทีเมื่อพบ URL ***
            if ($url_detected) {
                log_message('debug', 'URL detected in topic update content');

                // ตั้งค่า flash data สำหรับการแสดงผล
                $this->session->set_flashdata('save_url_detected', true);
                $this->session->set_flashdata('url_message', $url_message);

                // *** 🎯 ส่วนที่ขาดหายไป: ส่ง JSON Response ***
                // ปิด output buffering
                while (ob_get_level()) {
                    ob_end_clean();
                }

                // ตั้งค่า headers
                header('Content-Type: application/json; charset=utf-8');
                header('Cache-Control: no-cache, must-revalidate');
                http_response_code(400);

                // ส่ง JSON response
                $response_data = [
                    'success' => false,
                    'url_detected' => true,
                    'message' => $url_message,
                    'error_type' => 'url_content',
                    'debug_info' => [
                        'topic_id' => $topic_id,
                        'detected_content' => $combined_content,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ];

                $json_response = json_encode($response_data, JSON_UNESCAPED_UNICODE);
                log_message('info', 'Sending URL detection JSON response: ' . $json_response);

                echo $json_response;

                // บังคับ flush
                if (function_exists('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                } else {
                    flush();
                }

                exit(); // *** สำคัญ: ต้องมี exit() ***
            }

            log_message('info', 'URL check passed - no URLs detected');

            // ========================================================================
            // 5. เตรียมข้อมูลสำหรับอัพเดท (Prepare Update Data)
            // ========================================================================

            $update_data = [
                'q_a_msg' => $q_a_msg,
                'q_a_detail' => $q_a_detail,
                'q_a_by' => $q_a_by
            ];

            // เพิ่ม timestamp ถ้ามี column
            if ($this->db->field_exists('q_a_date_update', 'tbl_q_a')) {
                $update_data['q_a_date_update'] = date('Y-m-d H:i:s');
                log_message('info', 'Added update timestamp');
            }

            // เพิ่ม user id ถ้ามี column
            if ($this->db->field_exists('q_a_update_by', 'tbl_q_a')) {
                $current_user_id = $this->getCurrentUserId();
                if ($current_user_id) {
                    $update_data['q_a_update_by'] = $current_user_id;
                    log_message('info', 'Added update user ID: ' . $current_user_id);
                }
            }

            // อัพเดท email ถ้าไม่ใช่ user ที่ login แล้ว
            if (empty($this->session->userdata('mp_id')) && empty($this->session->userdata('m_id'))) {
                if (!empty($q_a_email)) {
                    $update_data['q_a_email'] = $q_a_email;
                    log_message('info', 'Updated email for guest user');
                }
            }

            log_message('info', 'Update data prepared: ' . print_r($update_data, true));

            // ========================================================================
            // 6. จัดการการอัพโหลดรูปภาพ (Image Upload Handling)
            // ========================================================================

            if (!empty($_FILES['q_a_imgs']['name'][0])) {
                log_message('info', 'Processing image uploads...');

                // เรียกใช้ฟังก์ชันอัพโหลดรูปภาพ
                $upload_result = $this->handleImageUploadForUpdate($topic_id);

                log_message('info', 'Upload result type: ' . gettype($upload_result));
                log_message('info', 'Upload result content: ' . $upload_result);

                // ตรวจสอบ JSON error ทันที
                if (is_string($upload_result) && !empty($upload_result)) {
                    // ลองแปลง JSON
                    $json_data = json_decode($upload_result, true);

                    // ถ้าแปลง JSON สำเร็จ และเป็น error
                    if (json_last_error() === JSON_ERROR_NONE && is_array($json_data)) {
                        if (isset($json_data['success']) && $json_data['success'] === false) {

                            log_message('debug', 'Image upload validation failed: ' . $json_data['message']);
                            log_message('info', 'Sending JSON error response to frontend');

                            // ปิด output buffering
                            while (ob_get_level()) {
                                ob_end_clean();
                            }

                            // ตั้งค่า headers
                            header('Content-Type: application/json; charset=utf-8');
                            header('Cache-Control: no-cache, must-revalidate');
                            http_response_code(400);

                            // Response ที่ตรงกับ Frontend Modal
                            $response_data = [
                                'success' => false,
                                'message' => $json_data['message'],
                                'error_type' => $json_data['error_type'] ?? 'image_upload_error',
                                'data' => $json_data['data'] ?? null
                            ];

                            $json_response = json_encode($response_data, JSON_UNESCAPED_UNICODE);
                            log_message('info', 'JSON Response: ' . $json_response);

                            echo $json_response;

                            // บังคับ flush
                            if (function_exists('fastcgi_finish_request')) {
                                fastcgi_finish_request();
                            } else {
                                flush();
                            }

                            exit();
                        }
                    }

                    // ถ้าไม่ใช่ JSON error = เป็นชื่อไฟล์ปกติ
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($json_data) || !isset($json_data['success'])) {
                        log_message('info', 'Images uploaded successfully: ' . $upload_result);

                        // อัพเดทข้อมูลรูปภาพในตาราง tbl_q_a หากมี column q_a_imgs
                        if ($this->db->field_exists('q_a_imgs', 'tbl_q_a')) {
                            $update_data['q_a_imgs'] = $upload_result;
                        }
                    }
                } else if ($upload_result === false) {
                    log_message('debug', 'Image upload failed but continuing with text update');
                    // ไม่ return error เพราะ text update ยังทำได้
                }
            }

            log_message('info', 'Continuing with database update...');

            // ========================================================================
            // 7. อัพเดทข้อมูลในฐานข้อมูล (Database Update)
            // ========================================================================

            // เริ่ม Transaction
            $this->db->trans_start();

            // อัพเดทข้อมูลในฐานข้อมูล
            $this->db->where('q_a_id', $topic_id);
            $update_result = $this->db->update('tbl_q_a', $update_data);

            log_message('info', 'Update result: ' . ($update_result ? 'SUCCESS' : 'FAILED'));
            log_message('info', 'Last query: ' . $this->db->last_query());

            if ($this->db->error()['code'] !== 0) {
                $this->db->trans_rollback();
                log_message('error', 'Update error: ' . print_r($this->db->error(), true));
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->db->error()['message'],
                    'error_type' => 'database_error'
                ]);
                exit();
            }

            if (!$update_result) {
                $this->db->trans_rollback();
                log_message('error', 'Update failed but no DB error');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัพเดทข้อมูลได้',
                    'error_type' => 'update_failed'
                ]);
                exit();
            }

            // เพิ่ม: สร้างการแจ้งเตือนสำหรับการแก้ไขกระทู้
            if (method_exists($this, 'createTopicUpdateNotification')) {
                $this->createTopicUpdateNotification($topic_id, $existing_topic, $q_a_msg, $q_a_by);
            }

            // Commit Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed during topic update');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการทำ Transaction',
                    'error_type' => 'transaction_failed'
                ]);
                exit();
            }

            // ========================================================================
            // 8. ส่งกลับผลลัพธ์สำเร็จ (Success Response)
            // ========================================================================

            // Log การแก้ไข
            log_message('info', "Topic {$topic_id} updated successfully by: " . $this->getCurrentUserInfo());

            // ส่งกลับผลลัพธ์สำเร็จ
            $this->session->set_flashdata('save_success', true);
            $this->session->set_flashdata('topic_updated', $topic_id);

            log_message('info', '=== UPDATE TOPIC ENHANCED END (SUCCESS) ===');

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'แก้ไขกระทู้สำเร็จ',
                'topic_id' => $topic_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Update topic exception: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดภายในระบบ: ' . $e->getMessage(),
                'error_type' => 'system_error',
                'debug_info' => [
                    'exception_message' => $e->getMessage(),
                    'exception_file' => $e->getFile(),
                    'exception_line' => $e->getLine()
                ]
            ]);
        } catch (Error $e) {
            log_message('error', 'Update topic fatal error: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดร้าแรง: ' . $e->getMessage(),
                'error_type' => 'fatal_error'
            ]);
        }
    }



    /**
     * 🔧 2. แทนที่ฟังก์ชัน handleImageUploadForUpdate() ที่มีอยู่แล้ว
     * 
     * ตำแหน่ง: หาบรรทัดที่ขึ้นต้นด้วย:
     * private function handleImageUploadForUpdate($topic_id)
     * 
     * วิธีการ: แทนที่ทั้งฟังก์ชันด้วยโค้ดด้านล่าง
     */
    private function handleImageUploadForUpdate($topic_id)
    {
        try {
            log_message('info', 'Starting handleImageUploadForUpdate for topic_id: ' . $topic_id);

            if (empty($_FILES['q_a_imgs']['name'][0])) {
                log_message('info', 'No images to upload');
                return '';
            }

            // *** ตรวจสอบจำนวนรูปภาพเก่าที่มีอยู่แล้ว ***
            $existing_images_count = 0;

            // นับจากตาราง tbl_q_a_img
            if ($this->db->table_exists('tbl_q_a_img')) {
                $this->db->where('q_a_img_ref_id', $topic_id);
                $existing_images_count = $this->db->count_all_results('tbl_q_a_img');
                log_message('info', "Existing images in tbl_q_a_img: {$existing_images_count}");
            }

            // หรือนับจาก column q_a_imgs ในตาราง tbl_q_a (ถ้ามี)
            if ($existing_images_count == 0 && $this->db->field_exists('q_a_imgs', 'tbl_q_a')) {
                $this->db->select('q_a_imgs');
                $this->db->where('q_a_id', $topic_id);
                $query = $this->db->get('tbl_q_a');
                $topic_data = $query->row();

                if ($topic_data && !empty($topic_data->q_a_imgs)) {
                    $existing_images = explode(',', $topic_data->q_a_imgs);
                    $existing_images_count = count(array_filter($existing_images, 'trim'));
                    log_message('info', "Existing images in q_a_imgs column: {$existing_images_count}");
                }
            }

            // นับจำนวนไฟล์ใหม่ที่จะอัพโหลด
            $new_files_count = 0;
            for ($i = 0; $i < count($_FILES['q_a_imgs']['name']); $i++) {
                if (!empty($_FILES['q_a_imgs']['name'][$i]) && $_FILES['q_a_imgs']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $new_files_count++;
                }
            }

            // *** ตรวจสอบจำนวนรูปรวม ***
            $max_images = 5;
            $total_images = $existing_images_count + $new_files_count;

            log_message('info', "Image count check - Existing: {$existing_images_count}, New: {$new_files_count}, Total: {$total_images}, Max: {$max_images}");

            // *** 🎯 ส่วนสำคัญ: ตรวจสอบ Image Limit และส่ง JSON Response ***
            if ($total_images > $max_images) {
                $remaining_slots = max(0, $max_images - $existing_images_count);
                $error_message = "สามารถเพิ่มรูปภาพได้อีกเพียง {$remaining_slots} รูป (ปัจจุบันมี {$existing_images_count} รูป จากทั้งหมด {$max_images} รูป)";

                log_message('debug', "Image limit exceeded: {$error_message}");

                // *** ส่ง JSON response ที่ตรงกับ Frontend Modal ***
                return json_encode([
                    'success' => false,
                    'error_type' => 'image_limit_exceeded', // *** สำคัญ: ต้องตรงกับ Frontend ***
                    'message' => $error_message,
                    'data' => [
                        'existing_count' => $existing_images_count,
                        'new_count' => $new_files_count,
                        'total_count' => $total_images,
                        'max_allowed' => $max_images,
                        'remaining_slots' => $remaining_slots
                    ]
                ], JSON_UNESCAPED_UNICODE);
            }

            // ตรวจสอบและสร้างโฟลเดอร์
            $upload_path = './docs/img/';
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', 'Cannot create upload directory: ' . $upload_path);
                    throw new Exception('ไม่สามารถสร้างโฟลเดอร์สำหรับอัพโหลดได้');
                }
                log_message('info', 'Created upload directory: ' . $upload_path);
            }

            // ตรวจสอบ permission
            if (!is_writable($upload_path)) {
                log_message('error', 'Upload directory is not writable: ' . $upload_path);
                throw new Exception('ไม่มีสิทธิ์เขียนไฟล์ในโฟลเดอร์อัพโหลด');
            }

            // โหลด upload library
            if (!class_exists('CI_Upload')) {
                $this->load->library('upload');
            }

            $uploaded_files = [];
            $files_count = count($_FILES['q_a_imgs']['name']);
            $errors = [];

            log_message('info', "Processing {$files_count} image files for topic_id: {$topic_id}");

            // *** จำกัดจำนวนไฟล์ที่จะประมวลผล ***
            $remaining_slots = $max_images - $existing_images_count;
            $files_to_process = min($files_count, $remaining_slots);

            log_message('info', "Will process {$files_to_process} files (remaining slots: {$remaining_slots})");

            for ($i = 0; $i < $files_to_process; $i++) {
                // ข้าม file ที่ว่าง
                if (empty($_FILES['q_a_imgs']['name'][$i]) || $_FILES['q_a_imgs']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                // ตรวจสอบ upload error
                if ($_FILES['q_a_imgs']['error'][$i] !== UPLOAD_ERR_OK) {
                    $error_msg = $this->getUploadErrorMessage($_FILES['q_a_imgs']['error'][$i]);
                    log_message('error', "File upload error for file {$i}: {$error_msg}");
                    $errors[] = "ไฟล์ {$_FILES['q_a_imgs']['name'][$i]}: {$error_msg}";
                    continue;
                }

                // ตรวจสอบขนาดไฟล์
                if ($_FILES['q_a_imgs']['size'][$i] > (5 * 1024 * 1024)) { // 5MB
                    log_message('error', "File too large: {$_FILES['q_a_imgs']['name'][$i]} ({$_FILES['q_a_imgs']['size'][$i]} bytes)");
                    $errors[] = "ไฟล์ {$_FILES['q_a_imgs']['name'][$i]} มีขนาดใหญ่เกินไป";
                    continue;
                }

                // ตรวจสอบประเภทไฟล์
                $allowed_mime_types = [
                    'image/gif',
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'image/webp'
                ];

                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $detected_mime = finfo_file($finfo, $_FILES['q_a_imgs']['tmp_name'][$i]);
                    finfo_close($finfo);

                    if (!in_array($detected_mime, $allowed_mime_types)) {
                        log_message('error', "Invalid file type: {$_FILES['q_a_imgs']['name'][$i]} (detected: {$detected_mime})");
                        $errors[] = "ไฟล์ {$_FILES['q_a_imgs']['name'][$i]} ประเภทไฟล์ไม่ถูกต้อง";
                        continue;
                    }
                }

                // สร้างชื่อไฟล์ใหม่ที่ปลอดภัย
                $file_ext = strtolower(pathinfo($_FILES['q_a_imgs']['name'][$i], PATHINFO_EXTENSION));
                $allowed_extensions = ['gif', 'jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($file_ext, $allowed_extensions)) {
                    log_message('error', "Invalid file extension: {$file_ext}");
                    $errors[] = "ไฟล์ {$_FILES['q_a_imgs']['name'][$i]} นามสกุลไฟล์ไม่ถูกต้อง";
                    continue;
                }

                $new_filename = 'qa_edit_' . $topic_id . '_' . time() . '_' . $i . '_' . rand(1000, 9999) . '.' . $file_ext;

                // ตั้งค่าการอัพโหลด
                $config = [
                    'upload_path' => $upload_path,
                    'allowed_types' => 'gif|jpg|jpeg|png|webp',
                    'max_size' => 5120, // 5MB
                    'file_name' => $new_filename,
                    'encrypt_name' => FALSE,
                    'remove_spaces' => TRUE,
                    'detect_mime' => TRUE,
                    'mod_mime_fix' => TRUE
                ];

                $this->upload->initialize($config);

                // สร้าง temporary $_FILES สำหรับ CodeIgniter upload
                $_FILES['single_file'] = [
                    'name' => $_FILES['q_a_imgs']['name'][$i],
                    'type' => $_FILES['q_a_imgs']['type'][$i],
                    'tmp_name' => $_FILES['q_a_imgs']['tmp_name'][$i],
                    'error' => $_FILES['q_a_imgs']['error'][$i],
                    'size' => $_FILES['q_a_imgs']['size'][$i]
                ];

                log_message('info', "Uploading file {$i}: {$_FILES['q_a_imgs']['name'][$i]} as {$new_filename}");

                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();
                    $uploaded_files[] = $upload_data['file_name'];
                    log_message('info', 'File uploaded successfully: ' . $upload_data['file_name']);

                    // บันทึกข้อมูลรูปภาพในตาราง tbl_q_a_img (ถ้ามี)
                    if ($this->db->table_exists('tbl_q_a_img')) {
                        $img_data = [
                            'q_a_img_ref_id' => $topic_id,
                            'q_a_img_img' => $upload_data['file_name']
                        ];

                        // เพิ่ม q_a_img_line ถ้ามี column นี้
                        if ($this->db->field_exists('q_a_img_line', 'tbl_q_a_img')) {
                            $img_data['q_a_img_line'] = $upload_data['file_name'];
                        }

                        // เพิ่ม timestamp ถ้ามี column นี้
                        if ($this->db->field_exists('q_a_img_date', 'tbl_q_a_img')) {
                            $img_data['q_a_img_date'] = date('Y-m-d H:i:s');
                        }

                        // ตรวจสอบ column ที่มีอยู่จริงในตาราง
                        $fields = $this->db->list_fields('tbl_q_a_img');
                        $filtered_data = array_intersect_key($img_data, array_flip($fields));

                        $insert_result = $this->db->insert('tbl_q_a_img', $filtered_data);

                        if (!$insert_result) {
                            log_message('error', 'Database insert failed for image: ' . $upload_data['file_name']);
                            log_message('error', 'Database error: ' . print_r($this->db->error(), true));

                            // ลบไฟล์ที่อัพโหลดแล้วเนื่องจาก database error
                            @unlink($upload_path . $upload_data['file_name']);

                            $errors[] = "ไม่สามารถบันทึกข้อมูลรูปภาพ {$upload_data['file_name']} ลงฐานข้อมูลได้";
                            continue;
                        }

                        log_message('info', 'Image record inserted into tbl_q_a_img');
                    } else {
                        log_message('debug', 'Table tbl_q_a_img does not exist, skipping database insert');
                    }
                } else {
                    $upload_errors = $this->upload->display_errors('', '');
                    log_message('error', 'File upload failed for file ' . $i . ': ' . $upload_errors);
                    $errors[] = "ไฟล์ {$_FILES['q_a_imgs']['name'][$i]}: {$upload_errors}";
                }

                // ล้าง temporary $_FILES
                unset($_FILES['single_file']);
            }

            // *** แจ้งเตือนถ้ามีไฟล์เกินที่ไม่ได้ประมวลผล ***
            if ($files_count > $files_to_process) {
                $skipped_files = $files_count - $files_to_process;
                log_message('debug', "Skipped {$skipped_files} files due to image limit");
                $errors[] = "ข้ามไฟล์ {$skipped_files} ไฟล์ เนื่องจากเกินจำนวนที่กำหนด (สูงสุด {$max_images} รูป)";
            }

            // Log ผลลัพธ์
            log_message('info', "Image upload completed. Uploaded: " . count($uploaded_files) . ", Errors: " . count($errors));

            // ถ้ามี error แต่มีไฟล์อัพโหลดสำเร็จบ้าง ให้ log warning
            if (!empty($errors) && !empty($uploaded_files)) {
                log_message('debug', 'Some files failed to upload: ' . implode(', ', $errors));
            }

            // ถ้าทุกไฟล์ล้มเหลว และมีไฟล์ที่พยายามอัพโหลด ให้ log error
            if (empty($uploaded_files) && !empty($errors)) {
                log_message('error', 'All files failed to upload: ' . implode(', ', $errors));
                // ไม่ throw exception เพื่อให้ text update ดำเนินการต่อไปได้
            }

            $result = empty($uploaded_files) ? '' : implode(',', $uploaded_files);
            log_message('info', 'handleImageUploadForUpdate completed. Result: ' . $result);
            return $result;

        } catch (Exception $e) {
            log_message('error', 'handleImageUploadForUpdate exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // ไม่ throw exception เพื่อให้ text update ดำเนินการต่อไปได้
            return false;
        }
    }



    // *** เพิ่มฟังก์ชันช่วยตรวจสอบ JSON ที่ถูกต้อง ***
    private function isValidJson($string)
    {
        if (!is_string($string) || empty($string)) {
            return false;
        }

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }



    /**
     * สร้างการแจ้งเตือนสำหรับการแก้ไขกระทู้
     */
    private function createTopicUpdateNotification($topic_id, $original_topic, $new_title, $updated_by)
    {
        try {
            log_message('info', "Creating update notification for topic {$topic_id}...");

            // ตรวจสอบและโหลด Notification_lib
            $this->ensure_notification_lib();

            // วิธีที่ 1: ใช้ Notification_lib (ถ้ามี)
            if (isset($this->Notification_lib) && method_exists($this->Notification_lib, 'qa_update')) {
                log_message('info', 'Using Notification_lib->qa_update method');

                try {
                    $notification_result = $this->Notification_lib->qa_update($topic_id, $new_title, $updated_by);

                    if ($notification_result) {
                        log_message('info', 'SUCCESS: Topic update notification created via Notification_lib');
                        return true;
                    } else {
                        log_message('debug', 'FAILED: Notification_lib->qa_update returned false');
                    }
                } catch (Exception $e) {
                    log_message('error', 'EXCEPTION in Notification_lib->qa_update: ' . $e->getMessage());
                }
            }

            // วิธีที่ 2: สร้างการแจ้งเตือนโดยตรง (ถ้า Notification_lib ไม่ทำงาน)
            if ($this->db->table_exists('tbl_notification')) {
                log_message('info', 'Creating notification directly in database');

                // ตรวจสอบว่ามีการเปลี่ยนแปลงจริงหรือไม่
                $has_changes = ($original_topic->q_a_msg !== $new_title) ||
                    ($original_topic->q_a_detail !== $this->input->post('q_a_detail'));

                if ($has_changes) {
                    $notification_data = [
                        'title' => 'แก้ไข', // *** เพิ่ม: ใส่คำว่า "แก้ไข" ใน title ***
                        'notification_title' => 'แก้ไขกระทู้: ' . $new_title,
                        'notification_message' => $updated_by . ' ได้แก้ไขกระทู้ "' . $new_title . '"',
                        'notification_type' => 'qa_update',
                        'notification_ref_id' => $topic_id,
                        'notification_from_user' => $updated_by,
                        'notification_date' => date('Y-m-d H:i:s'),
                        'notification_status' => 'unread'
                    ];

                    // เพิ่ม fields เพิ่มเติมถ้ามี
                    if ($this->db->field_exists('notification_url', 'tbl_notification')) {
                        $notification_data['notification_url'] = 'Pages/q_a#comment-' . $topic_id;
                    }

                    if ($this->db->field_exists('notification_icon', 'tbl_notification')) {
                        $notification_data['notification_icon'] = 'fas fa-edit';
                    }

                    if ($this->db->field_exists('notification_priority', 'tbl_notification')) {
                        $notification_data['notification_priority'] = 'normal';
                    }

                    // เพิ่ม user_id ของผู้แก้ไข
                    $current_user_id = $this->getCurrentUserId();
                    if ($current_user_id && $this->db->field_exists('notification_user_id', 'tbl_notification')) {
                        $notification_data['notification_user_id'] = $current_user_id;
                    }

                    log_message('info', 'Notification data: ' . print_r($notification_data, true));

                    $insert_result = $this->db->insert('tbl_notification', $notification_data);

                    if ($insert_result) {
                        $notification_id = $this->db->insert_id();
                        log_message('info', 'SUCCESS: Direct notification created with ID: ' . $notification_id);
                        return true;
                    } else {
                        log_message('error', 'FAILED: Direct notification insert failed');
                        log_message('error', 'DB Error: ' . print_r($this->db->error(), true));
                    }
                } else {
                    log_message('info', 'No significant changes detected, skipping notification');
                    return true;
                }
            } else {
                log_message('debug', 'tbl_notification table does not exist');
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Exception in createTopicUpdateNotification: ' . $e->getMessage());
            return false;
        }
    }





    public function get_existing_images_count()
    {
        try {
            // ตรวจสอบ HTTP Method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $topic_id = $this->input->post('topic_id');

            if (empty($topic_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing topic_id']);
                return;
            }

            $existing_images_count = 0;

            // นับจากตาราง tbl_q_a_img
            if ($this->db->table_exists('tbl_q_a_img')) {
                $this->db->where('q_a_img_ref_id', $topic_id);
                $existing_images_count = $this->db->count_all_results('tbl_q_a_img');
                log_message('info', "get_existing_images_count - tbl_q_a_img count: {$existing_images_count} for topic_id: {$topic_id}");
            }

            // หรือนับจาก column q_a_imgs ในตาราง tbl_q_a (ถ้ามี)
            if ($existing_images_count == 0 && $this->db->field_exists('q_a_imgs', 'tbl_q_a')) {
                $this->db->select('q_a_imgs');
                $this->db->where('q_a_id', $topic_id);
                $query = $this->db->get('tbl_q_a');
                $topic_data = $query->row();

                if ($topic_data && !empty($topic_data->q_a_imgs)) {
                    $existing_images = explode(',', $topic_data->q_a_imgs);
                    $existing_images_count = count(array_filter($existing_images, 'trim'));
                    log_message('info', "get_existing_images_count - q_a_imgs column count: {$existing_images_count} for topic_id: {$topic_id}");
                }
            }

            // ส่งผลลัพธ์กลับ
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'existing_count' => $existing_images_count,
                'max_allowed' => 5,
                'remaining_slots' => max(0, 5 - $existing_images_count)
            ]);

        } catch (Exception $e) {
            log_message('error', 'get_existing_images_count exception: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดภายในระบบ'
            ]);
        }
    }



    public function delete_topic()
    {
        log_message('info', '=== DELETE TOPIC DEBUG START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));

        try {
            // ตรวจสอบ HTTP Method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $topic_id = $this->input->post('topic_id');

            if (empty($topic_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสกระทู้']);
                return;
            }

            // ตรวจสอบว่ามีกระทู้นี้อยู่จริงหรือไม่
            $existing_topic = $this->db->select('q_a_id, q_a_user_type, q_a_user_id, q_a_by, q_a_msg')
                ->where('q_a_id', $topic_id)
                ->get('tbl_q_a')
                ->row();

            if (!$existing_topic) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'ไม่พบกระทู้ที่ต้องการลบ']);
                return;
            }

            // ตรวจสอบสิทธิ์การลบ
            $can_delete = $this->checkEditPermission($existing_topic);

            if (!$can_delete) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบกระทู้นี้']);
                return;
            }

            // เก็บข้อมูลสำหรับ notification ก่อนลบ
            $topic_title = $existing_topic->q_a_msg;
            $deleted_by = $existing_topic->q_a_by; // *** แก้ไข: ใช้ชื่อจากกระทู้แทน ***

            // *** แก้ไข: สร้างการแจ้งเตือนก่อนลบ (เหมือน add_q_a และ reply) ***
            log_message('info', 'Creating delete notification for topic: ' . $topic_id);

            // ตรวจสอบว่า Notification_lib โหลดแล้วหรือยัง
            if (isset($this->Notification_lib)) {
                // *** ใช้ method ที่มีอยู่แล้ว หรือสร้างแบบง่าย ***
                $delete_notification_result = $this->create_delete_notification_simple($topic_id, $topic_title, $deleted_by);

                if ($delete_notification_result) {
                    log_message('info', 'Delete notification created successfully for ID: ' . $topic_id);
                } else {
                    log_message('error', 'Failed to create delete notification for ID: ' . $topic_id);
                }
            } else {
                log_message('error', 'Notification_lib not loaded in Pages controller - delete_topic method');

                // *** Fallback: สร้างการแจ้งเตือนโดยตรงในฐานข้อมูล ***
                $this->create_delete_notification_direct($topic_id, $topic_title, $deleted_by);
            }

            // เริ่ม Transaction
            $this->db->trans_start();

            // ลบรูปภาพที่เกี่ยวข้อง (ถ้ามี)
            if ($this->db->table_exists('tbl_q_a_img')) {
                $images = $this->db->where('q_a_img_ref_id', $topic_id)
                    ->get('tbl_q_a_img')
                    ->result();

                foreach ($images as $img) {
                    $file_path = './docs/img/' . $img->q_a_img_img;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                        log_message('info', 'Deleted image file: ' . $img->q_a_img_img);
                    }
                }

                // ลบ records ในตาราง tbl_q_a_img
                $this->db->where('q_a_img_ref_id', $topic_id);
                $this->db->delete('tbl_q_a_img');
            }

            // ลบ replies ที่เกี่ยวข้อง (ถ้ามี)
            if ($this->db->table_exists('tbl_q_a_reply')) {
                // ลบรูปภาพของ replies
                if ($this->db->table_exists('tbl_q_a_reply_img')) {
                    $reply_images = $this->db->select('tbl_q_a_reply_img.*')
                        ->from('tbl_q_a_reply_img')
                        ->join('tbl_q_a_reply', 'tbl_q_a_reply.q_a_reply_id = tbl_q_a_reply_img.q_a_reply_img_ref_id')
                        ->where('tbl_q_a_reply.q_a_reply_ref_id', $topic_id)
                        ->get()
                        ->result();

                    foreach ($reply_images as $img) {
                        $file_path = './docs/img/' . $img->q_a_reply_img_img;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                            log_message('info', 'Deleted reply image file: ' . $img->q_a_reply_img_img);
                        }
                    }

                    // ลบ records รูปภาพ replies
                    $this->db->query("DELETE FROM tbl_q_a_reply_img WHERE q_a_reply_img_ref_id IN (SELECT q_a_reply_id FROM tbl_q_a_reply WHERE q_a_reply_ref_id = ?)", [$topic_id]);
                }

                // ลบ replies
                $this->db->where('q_a_reply_ref_id', $topic_id);
                $this->db->delete('tbl_q_a_reply');
            }

            // ลบการแจ้งเตือนเก่าที่เกี่ยวข้องกับกระทู้นี้ (ยกเว้นการแจ้งเตือนการลบที่เพิ่งสร้าง)
            if ($this->db->table_exists('tbl_notification')) {
                $this->db->where('notification_ref_id', $topic_id);
                $this->db->where_in('notification_type', ['qa_new', 'qa_reply', 'qa_update']);
                $this->db->delete('tbl_notification');
                log_message('info', 'Deleted old notifications for topic: ' . $topic_id);
            }

            // ลบกระทู้หลัก
            $this->db->where('q_a_id', $topic_id);
            $delete_result = $this->db->delete('tbl_q_a');

            if (!$delete_result) {
                $this->db->trans_rollback();
                log_message('error', 'Failed to delete topic: ' . $this->db->last_query());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบกระทู้']);
                return;
            }

            // Commit Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed during topic deletion');
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการทำ Transaction']);
                return;
            }

            // Log การลบ
            log_message('info', "Topic {$topic_id} ('{$topic_title}') deleted successfully by: {$deleted_by}");

            log_message('info', '=== DELETE TOPIC DEBUG END (SUCCESS) ===');

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'ลบกระทู้สำเร็จ'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Delete topic exception: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายในระบบ']);
        }
    }

    /**
     * *** เพิ่ม: ฟังก์ชันสร้างการแจ้งเตือนการลบแบบง่าย ***
     */
    private function create_delete_notification_simple($topic_id, $topic_title, $deleted_by)
    {
        try {
            log_message('info', "Creating simple delete notification for topic {$topic_id}...");

            // ใช้วิธีเดียวกับ add_q_a - เรียกผ่าน Notification_lib
            if (method_exists($this->Notification_lib, 'qa_delete')) {
                // ถ้ามี method qa_delete
                $result = $this->Notification_lib->qa_delete($topic_id, $topic_title, $deleted_by);
                return $result;
            } elseif (method_exists($this->Notification_lib, 'create')) {
                // ถ้ามี method create แบบทั่วไป (เหมือนในระบบ notification ใหม่)
                $notification_data = [
                    'type' => 'qa_delete',
                    'title' => 'ลบกระทู้: ' . $topic_title,
                    'message' => $deleted_by . ' ได้ลบกระทู้ "' . $topic_title . '"',
                    'reference_id' => $topic_id,
                    'reference_table' => 'tbl_q_a',
                    'target_role' => 'admin', // แจ้งเตือน admin
                    'priority' => 'normal',
                    'icon' => 'fas fa-trash',
                    'url' => 'System_reports/notifications#',
                    'data' => [
                        'action' => 'delete',
                        'deleted_by' => $deleted_by,
                        'original_title' => $topic_title
                    ]
                ];

                $result = $this->Notification_lib->create($notification_data);
                return $result;
            } else {
                // Fallback ไปใช้วิธีสร้างโดยตรง
                return $this->create_delete_notification_direct($topic_id, $topic_title, $deleted_by);
            }

        } catch (Exception $e) {
            log_message('error', 'Exception in create_delete_notification_simple: ' . $e->getMessage());
            return $this->create_delete_notification_direct($topic_id, $topic_title, $deleted_by);
        }
    }

    /**
     * *** เพิ่ม: ฟังก์ชันสร้างการแจ้งเตือนการลบโดยตรงในฐานข้อมูล ***
     */
    private function create_delete_notification_direct($topic_id, $topic_title, $deleted_by)
    {
        try {
            log_message('info', "Creating direct delete notification for topic {$topic_id}...");

            // ตรวจสอบตารางการแจ้งเตือน
            if ($this->db->table_exists('tbl_notification')) {
                $notification_data = [
                    'title' => 'ลบกระทู้', // *** สำคัญ: ใส่ title ***
                    'notification_title' => 'ลบกระทู้: ' . $topic_title,
                    'notification_message' => $deleted_by . ' ได้ลบกระทู้ "' . $topic_title . '"',
                    'notification_type' => 'qa_delete',
                    'notification_ref_id' => $topic_id,
                    'notification_from_user' => $deleted_by,
                    'notification_date' => date('Y-m-d H:i:s'),
                    'notification_status' => 'unread'
                ];

                // เพิ่ม fields เพิ่มเติมถ้ามี
                if ($this->db->field_exists('notification_url', 'tbl_notification')) {
                    $notification_data['notification_url'] = 'Pages/q_a';
                }

                if ($this->db->field_exists('notification_icon', 'tbl_notification')) {
                    $notification_data['notification_icon'] = 'fas fa-trash';
                }

                if ($this->db->field_exists('notification_priority', 'tbl_notification')) {
                    $notification_data['notification_priority'] = 'normal';
                }

                $insert_result = $this->db->insert('tbl_notification', $notification_data);

                if ($insert_result) {
                    $notification_id = $this->db->insert_id();
                    log_message('info', 'SUCCESS: Direct delete notification created with ID: ' . $notification_id);
                    return true;
                } else {
                    log_message('error', 'FAILED: Direct delete notification insert failed');
                    log_message('error', 'DB Error: ' . print_r($this->db->error(), true));
                }
            }

            // ตรวจสอบตารางใหม่
            if ($this->db->table_exists('tbl_notifications')) {
                log_message('info', 'Using new notification table: tbl_notifications');

                $new_notification_data = [
                    'type' => 'qa_delete',
                    'title' => 'ลบกระทู้: ' . $topic_title,
                    'message' => $deleted_by . ' ได้ลบกระทู้ "' . $topic_title . '"',
                    'reference_id' => $topic_id,
                    'reference_table' => 'tbl_q_a',
                    'target_role' => 'staff',
                    'priority' => 'high',
                    'icon' => 'fas fa-trash',
                    'url' => 'System_reports/notifications#',
                    'data' => json_encode([
                        'action' => 'delete',
                        'deleted_by' => $deleted_by,
                        'original_title' => $topic_title
                    ]),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->getCurrentUserId() ?: 0
                ];


                $insert_result = $this->db->insert('tbl_notifications', $new_notification_data);

                if ($insert_result) {
                    $notification_id = $this->db->insert_id();
                    log_message('info', 'SUCCESS: New table delete notification created with ID: ' . $notification_id);
                    return true;
                }
            }

            return false;

        } catch (Exception $e) {
            log_message('error', 'Exception in create_delete_notification_direct: ' . $e->getMessage());
            return false;
        }
    }

    private function handleTopicImageUpload($topic_id)
    {
        if (empty($_FILES['q_a_imgs']['name'][0])) {
            return false;
        }

        // ตั้งค่าการอัพโหลด
        $config['upload_path'] = './docs/img/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|webp';
        $config['max_size'] = '5120'; // 5MB
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        $uploaded_files = [];
        $files = $_FILES['q_a_imgs'];

        for ($i = 0; $i < count($files['name']); $i++) {
            if (!empty($files['name'][$i])) {
                $_FILES['q_a_img']['name'] = $files['name'][$i];
                $_FILES['q_a_img']['type'] = $files['type'][$i];
                $_FILES['q_a_img']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['q_a_img']['error'] = $files['error'][$i];
                $_FILES['q_a_img']['size'] = $files['size'][$i];

                if ($this->upload->do_upload('q_a_img')) {
                    $upload_data = $this->upload->data();
                    $uploaded_files[] = $upload_data['file_name'];

                    // บันทึกข้อมูลรูปภาพในฐานข้อมูล
                    $img_data = [
                        'q_a_img_ref_id' => $topic_id,
                        'q_a_img_img' => $upload_data['file_name']
                    ];
                    $this->db->insert('tbl_q_a_img', $img_data);
                }
            }
        }

        return $uploaded_files;
    }





    /**
     * API endpoint สำหรับตรวจสอบ schema (สำหรับ debug)
     */
    public function check_schema()
    {
        $schema_ok = $this->checkDatabaseSchema();

        $response = [
            'schema_ok' => $schema_ok,
            'table_exists' => $this->db->table_exists('tbl_q_a'),
            'fields' => []
        ];

        if ($this->db->table_exists('tbl_q_a')) {
            $fields = $this->db->list_fields('tbl_q_a');
            $response['fields'] = $fields;
        }

        echo json_encode($response);
    }

    public function find_topic_page()
    {
        // ตรวจสอบว่าเป็น AJAX request
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        // ตั้งค่า header สำหรับ JSON response
        header('Content-Type: application/json');

        $topic_id = $this->input->post('topic_id');

        // ตรวจสอบ input
        if (empty($topic_id) || !is_numeric($topic_id)) {
            log_message('error', 'Invalid topic_id in find_topic_page: ' . $topic_id);
            echo json_encode([
                'success' => false,
                'message' => 'ไม่พบ ID กระทู้ที่ต้องการค้นหา'
            ]);
            return;
        }

        try {
            log_message('info', "Pages/find_topic_page: Searching for topic ID: {$topic_id}");

            // ลองค้นหาใน tbl_q_a ก่อน (กระทู้หลัก)
            $topic_query = $this->db->select('q_a_id, q_a_datesave, q_a_msg')
                ->where('q_a_id', $topic_id)
                ->get('tbl_q_a');

            $found_topic = null;
            $is_reply = false;

            if ($topic_query->num_rows() > 0) {
                // พบกระทู้หลัก
                $found_topic = $topic_query->row();
                log_message('info', "Found main topic: {$topic_id} - {$found_topic->q_a_msg}");
            } else {
                log_message('info', 'Main topic not found, checking replies...');

                // ไม่พบกระทู้หลัก ลองค้นหาใน reply
                if ($this->db->table_exists('tbl_q_a_reply')) {
                    $reply_query = $this->db->select('q_a_reply_id, q_a_reply_ref_id, q_a_reply_datesave')
                        ->where('q_a_reply_id', $topic_id)
                        ->get('tbl_q_a_reply');

                    if ($reply_query->num_rows() > 0) {
                        // พบ reply แต่ต้องหากระทู้หลักที่เป็นของ reply นี้
                        $reply_data = $reply_query->row();
                        log_message('info', "Found reply {$topic_id}, belongs to topic: {$reply_data->q_a_reply_ref_id}");

                        $main_topic_query = $this->db->select('q_a_id, q_a_datesave, q_a_msg')
                            ->where('q_a_id', $reply_data->q_a_reply_ref_id)
                            ->get('tbl_q_a');

                        if ($main_topic_query->num_rows() > 0) {
                            $found_topic = $main_topic_query->row();
                            $is_reply = true;
                            log_message('info', "Found parent topic: {$found_topic->q_a_id} - {$found_topic->q_a_msg}");
                        }
                    }
                }
            }

            if (!$found_topic) {
                log_message('error', "Topic/Reply {$topic_id} not found in database");
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบกระทู้หรือการตอบกลับที่ต้องการ'
                ]);
                return;
            }

            // คำนวณหาหน้าที่กระทู้อยู่
            $page = $this->calculateTopicPageFixed($found_topic->q_a_id);

            log_message('info', "Topic {$found_topic->q_a_id} found on page: {$page}");

            // ส่งผลลัพธ์
            $response = [
                'success' => true,
                'page' => $page,
                'topic_id' => $found_topic->q_a_id,
                'topic_title' => $found_topic->q_a_msg,
                'is_reply' => $is_reply,
                'original_search_id' => $topic_id,
                'message' => $is_reply
                    ? "พบการตอบกลับในกระทู้ #{$found_topic->q_a_id} หน้า {$page}"
                    : "พบกระทู้ #{$found_topic->q_a_id} หน้า {$page}"
            ];

            log_message('info', 'Pages/find_topic_page Response: ' . json_encode($response));

            echo json_encode($response);

        } catch (Exception $e) {
            log_message('error', 'Error in Pages/find_topic_page: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการค้นหากระทู้: ' . $e->getMessage(),
                'error_type' => 'server_error'
            ]);
        }
    }





    private function calculateTopicPageFixed($target_topic_id)
    {
        try {
            // ดึงกระทู้ทั้งหมด เรียงตามวันที่สร้าง (ใหม่ไปเก่า) - ไม่ใช้ q_a_status
            $all_topics = $this->db->select('q_a_id')
                ->order_by('q_a_datesave', 'DESC')
                ->get('tbl_q_a')
                ->result();

            if (empty($all_topics)) {
                return 1;
            }

            // หาตำแหน่งของกระทู้ที่ต้องการ
            $position = 0;
            foreach ($all_topics as $index => $topic) {
                if ($topic->q_a_id == $target_topic_id) {
                    $position = $index + 1; // index เริ่มจาก 0 แต่ position เริ่มจาก 1
                    break;
                }
            }

            if ($position === 0) {
                log_message('debug', "Topic {$target_topic_id} not found in topics list");
                return 1;
            }

            // คำนวณหน้า (5 กระทู้ต่อหน้า)
            $items_per_page = 5;
            $page = ceil($position / $items_per_page);

            log_message('info', "Topic {$target_topic_id} is at position {$position}, page {$page}");

            return $page;

        } catch (Exception $e) {
            log_message('error', 'Error in calculateTopicPageFixed: ' . $e->getMessage());
            return 1; // fallback to page 1
        }
    }


    /**
     * คำนวณหาหน้าที่กระทู้อยู่
     * @param int $target_topic_id ID ของกระทู้ที่ต้องการหา
     * @return int หมายเลขหน้า
     */
    private function calculateTopicPage($target_topic_id)
    {
        try {
            // ดึงกระทู้ทั้งหมดที่เปิดใช้งาน เรียงตามวันที่สร้าง (ใหม่ไปเก่า)
            $all_topics = $this->db->select('q_a_id')
                ->where('q_a_status', '1')
                ->order_by('q_a_datesave', 'DESC')
                ->get('tbl_q_a')
                ->result();

            if (empty($all_topics)) {
                return 1;
            }

            // หาตำแหน่งของกระทู้ที่ต้องการ
            $position = 0;
            foreach ($all_topics as $index => $topic) {
                if ($topic->q_a_id == $target_topic_id) {
                    $position = $index + 1; // index เริ่มจาก 0 แต่ position เริ่มจาก 1
                    break;
                }
            }

            if ($position === 0) {
                log_message('debug', "Topic {$target_topic_id} not found in active topics list");
                return 1;
            }

            // คำนวณหน้า (5 กระทู้ต่อหน้า)
            $items_per_page = 5;
            $page = ceil($position / $items_per_page);

            log_message('info', "Topic {$target_topic_id} is at position {$position}, page {$page}");

            return $page;

        } catch (Exception $e) {
            log_message('error', 'Error in calculateTopicPage: ' . $e->getMessage());
            return 1; // fallback to page 1
        }
    }

    public function test_find_page($topic_id = null)
    {
        if (!$topic_id) {
            echo "<h3>ทดสอบการค้นหาหน้าสำหรับกระทู้</h3>";
            echo "<p>กรุณาระบุ topic_id</p>";
            echo "<p>ตัวอย่าง: " . site_url("Pages/test_find_page/77") . "</p>";

            // แสดงกระทู้ 10 อันล่าสุด - ไม่ใช้ q_a_status
            $recent_topics = $this->db->select('q_a_id, q_a_msg, q_a_datesave')
                ->order_by('q_a_datesave', 'DESC')
                ->limit(10)
                ->get('tbl_q_a')
                ->result();

            echo "<h4>กระทู้ล่าสุด 10 อัน:</h4>";
            echo "<ul>";
            foreach ($recent_topics as $topic) {
                $test_url = site_url("Pages/test_find_page/{$topic->q_a_id}");
                echo "<li><a href='{$test_url}'>ID: {$topic->q_a_id} - {$topic->q_a_msg}</a></li>";
            }
            echo "</ul>";
            return;
        }

        if (!is_numeric($topic_id)) {
            echo "<p style='color: red;'>topic_id ต้องเป็นตัวเลข</p>";
            return;
        }

        echo "<h3>ทดสอบการค้นหาหน้าสำหรับกระทู้ ID: {$topic_id}</h3>";

        // ดึงข้อมูลกระทู้ - ไม่ใช้ q_a_status
        $topic = $this->db->where('q_a_id', $topic_id)
            ->get('tbl_q_a')
            ->row();

        if (!$topic) {
            echo "<p style='color: red;'>❌ ไม่พบกระทู้ ID: {$topic_id}</p>";

            // ตรวจสอบว่าเป็น reply หรือไม่
            $reply = $this->db->where('q_a_reply_id', $topic_id)
                ->get('tbl_q_a_reply')
                ->row();

            if ($reply) {
                echo "<p style='color: orange;'>🔄 พบ Reply ID: {$topic_id} ที่อ้างอิงกระทู้ ID: {$reply->q_a_reply_ref_id}</p>";

                $main_topic = $this->db->where('q_a_id', $reply->q_a_reply_ref_id)
                    ->get('tbl_q_a')
                    ->row();

                if ($main_topic) {
                    $page = $this->calculateTopicPageFixed($main_topic->q_a_id);
                    echo "<p style='color: green;'>✅ กระทู้หลัก: {$main_topic->q_a_msg}</p>";
                    echo "<p>📄 อยู่ในหน้า: <strong>{$page}</strong></p>";

                    $link = site_url("Pages/q_a?page={$page}#reply-{$topic_id}");
                    echo "<p>🔗 ลิงก์ไป Reply: <a href='{$link}' target='_blank'>{$link}</a></p>";
                }
            }
            return;
        }

        // คำนวณหน้า
        $page = $this->calculateTopicPageFixed($topic_id);

        echo "<p style='color: green;'>✅ พบกระทู้: {$topic->q_a_msg}</p>";
        echo "<p>📅 วันที่สร้าง: {$topic->q_a_datesave}</p>";
        echo "<p>📄 อยู่ในหน้า: <strong>{$page}</strong></p>";

        // แสดงลิงก์ไปหน้านั้น
        $link = site_url("Pages/q_a?page={$page}#comment-{$topic_id}");
        echo "<p>🔗 ลิงก์: <a href='{$link}' target='_blank'>{$link}</a></p>";

        // แสดงกระทู้ทั้งหมดในหน้านั้น
        echo "<h4>กระทู้ทั้งหมดในหน้า {$page}:</h4>";
        echo "<ol>";

        $items_per_page = 5;
        $offset = ($page - 1) * $items_per_page;

        $page_topics = $this->db->select('q_a_id, q_a_msg, q_a_datesave')
            ->order_by('q_a_datesave', 'DESC')
            ->limit($items_per_page, $offset)
            ->get('tbl_q_a')
            ->result();

        foreach ($page_topics as $t) {
            $highlight = ($t->q_a_id == $topic_id) ? " style='background: yellow; font-weight: bold;'" : "";
            echo "<li{$highlight}>ID: {$t->q_a_id} - {$t->q_a_msg} ({$t->q_a_datesave})</li>";
        }
        echo "</ol>";

        // ทดสอบ API call
        echo "<h4>ทดสอบ API:</h4>";
        echo "<button onclick='testAPI({$topic_id})'>ทดสอบ find_topic_page API</button>";
        echo "<div id='api-result'></div>";

        echo "
    <script>
    function testAPI(topicId) {
        const resultDiv = document.getElementById('api-result');
        resultDiv.innerHTML = 'กำลังทดสอบ...';
        
        fetch('" . site_url('Pages/find_topic_page') . "', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'topic_id=' + topicId
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = '<h5>ผลลัพธ์ API:</h5><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            resultDiv.innerHTML = '<p style=\"color: red;\">Error: ' + error.message + '</p>';
        });
    }
    </script>";
    }




    private function getCurrentUserInfo()
    {
        $mp_id = $this->session->userdata('mp_id');
        $mp_email = $this->session->userdata('mp_email');
        $m_id = $this->session->userdata('m_id');
        $m_email = $this->session->userdata('m_email');

        if ($mp_id) {
            return "Public User (mp_id: {$mp_id}, email: {$mp_email})";
        }

        if ($m_id) {
            return "Staff User (m_id: {$m_id}, email: {$m_email})";
        }

        return "Guest User";
    }


    private function handleImageUpload($field_name)
    {
        if (empty($_FILES[$field_name]['name'][0])) {
            return '';
        }

        $this->load->library('upload');
        $uploaded_files = [];
        $files_count = count($_FILES[$field_name]['name']);

        for ($i = 0; $i < $files_count; $i++) {
            if (!empty($_FILES[$field_name]['name'][$i])) {
                // สร้างชื่อไฟล์ใหม่
                $file_ext = pathinfo($_FILES[$field_name]['name'][$i], PATHINFO_EXTENSION);
                $new_filename = 'qa_edit_' . time() . '_' . $i . '.' . $file_ext;

                // ตั้งค่าการอัพโหลด
                $config = [
                    'upload_path' => './docs/img/',
                    'allowed_types' => 'gif|jpg|png|jpeg|webp',
                    'max_size' => 5120, // 5MB
                    'file_name' => $new_filename,
                    'encrypt_name' => FALSE
                ];

                $this->upload->initialize($config);

                // จัดการไฟล์แต่ละไฟล์
                $_FILES['single_file']['name'] = $_FILES[$field_name]['name'][$i];
                $_FILES['single_file']['type'] = $_FILES[$field_name]['type'][$i];
                $_FILES['single_file']['tmp_name'] = $_FILES[$field_name]['tmp_name'][$i];
                $_FILES['single_file']['error'] = $_FILES[$field_name]['error'][$i];
                $_FILES['single_file']['size'] = $_FILES[$field_name]['size'][$i];

                if ($this->upload->do_upload('single_file')) {
                    $upload_data = $this->upload->data();
                    $uploaded_files[] = $upload_data['file_name'];
                    log_message('info', 'File uploaded successfully: ' . $upload_data['file_name']);
                } else {
                    $error = $this->upload->display_errors('', '');
                    log_message('error', 'File upload failed: ' . $error);
                    // ถ้าอัพโหลดไฟล์ไม่สำเร็จ ให้ลบไฟล์ที่อัพโหลดไปแล้ว
                    foreach ($uploaded_files as $uploaded_file) {
                        $file_path = './docs/img/' . $uploaded_file;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    return false;
                }
            }
        }

        return empty($uploaded_files) ? '' : implode(',', $uploaded_files);
    }

    private function checkVulgarWords($text)
    {
        // ถ้าคุณมีระบบตรวจสอบคำหยาบ ให้เรียกใช้ที่นี่
        // ตัวอย่าง:
        /*
        $vulgar_words = ['คำหยาบ1', 'คำหยาบ2']; // หรือโหลดจากฐานข้อมูล
        $text_lower = strtolower($text);

        foreach ($vulgar_words as $vulgar) {
            if (strpos($text_lower, strtolower($vulgar)) !== false) {
                return true;
            }
        }
        */

        return false; // ถ้าไม่มีระบบตรวจสอบคำหยาบ
    }


    private function getCurrentUserId()
    {
        $mp_id = $this->session->userdata('mp_id');
        $mp_email = $this->session->userdata('mp_email');
        $m_id = $this->session->userdata('m_id');
        $m_email = $this->session->userdata('m_email');

        if ($mp_id && $mp_email) {
            return $this->fixUserIdOverflow($mp_id, $mp_email);
        }

        if ($m_id && $m_email) {
            return $this->fixUserIdOverflow($m_id, $m_email);
        }

        return null;
    }





    private function checkEditPermission($topic)
    {
        // ตรวจสอบว่าเป็น Staff/Admin หรือไม่
        $m_id = $this->session->userdata('m_id');
        $m_system = $this->session->userdata('m_system');

        if ($m_id) {
            // Staff/Admin สามารถแก้ไขได้ทุกกระทู้
            if (in_array($m_system, ['system_admin', 'super_admin', 'user_admin']) || !empty($m_id)) {
                log_message('info', "Staff/Admin {$m_id} allowed to edit topic {$topic->q_a_id}");
                return true;
            }
        }

        // ตรวจสอบ Public User
        $mp_id = $this->session->userdata('mp_id');
        $mp_email = $this->session->userdata('mp_email');

        if ($mp_id && $mp_email) {
            // *** ใช้ fixUserIdOverflow เหมือนกับที่ใช้ในหน้า View ***
            $fixed_user_id = $this->fixUserIdOverflow($mp_id, $mp_email);

            // ตรวจสอบความเป็นเจ้าของ
            if ($topic->q_a_user_id && $fixed_user_id == $topic->q_a_user_id) {
                log_message('info', "Public user {$fixed_user_id} owns topic {$topic->q_a_id}");
                return true;
            }

            // *** สำคัญ: ตรวจสอบกระทู้ที่มี overflow user_id ***
            if (
                ($topic->q_a_user_id == 2147483647 || $topic->q_a_user_id == '2147483647') &&
                $topic->q_a_user_type === 'public' &&
                !empty($topic->q_a_email)
            ) {

                // ตรวจสอบว่าอีเมลตรงกันหรือไม่
                if ($topic->q_a_email === $mp_email) {
                    log_message('info', "Public user {$fixed_user_id} owns overflow topic {$topic->q_a_id} via email match");

                    // *** อัพเดท user_id ในฐานข้อมูลให้ถูกต้อง ***
                    $this->db->where('q_a_id', $topic->q_a_id)
                        ->update('tbl_q_a', ['q_a_user_id' => $fixed_user_id]);

                    log_message('info', "Auto-fixed topic {$topic->q_a_id} user_id: {$topic->q_a_user_id} -> {$fixed_user_id}");
                    return true;
                }
            }

            // ตรวจสอบกระทู้เก่าที่ไม่มี user_id
            if (($topic->q_a_user_type === 'public' || $topic->q_a_user_type === 'staff') && empty($topic->q_a_user_id)) {
                log_message('info', "Allowing edit for legacy topic {$topic->q_a_id} without user_id");
                return true;
            }
        }

        // ตรวจสอบ Guest User
        if (empty($mp_id) && empty($m_id)) {
            if ($topic->q_a_user_type === 'guest') {
                // สำหรับ guest ต้องตรวจสอบ session token (ถ้ามี)
                $guest_session_token = $this->input->post('guest_session_token');
                if ($guest_session_token) {
                    $session_data = json_decode($guest_session_token, true);
                    if (is_array($session_data) && isset($session_data['created_at'])) {
                        $session_time = strtotime($session_data['created_at']);
                        $current_time = time();
                        $time_diff = ($current_time - $session_time) / 3600; // ชั่วโมง

                        if ($time_diff <= 24) { // อนุญาตแก้ไขได้ 24 ชั่วโมง
                            log_message('info', "Guest user allowed to edit topic {$topic->q_a_id} (session valid for {$time_diff} hours)");
                            return true;
                        }
                    }
                }
            }
        }

        log_message('info', "Edit permission denied for topic {$topic->q_a_id}");
        return false;
    }


    public function check_edit_permission()
    {
        header('Content-Type: application/json');

        try {
            log_message('info', '=== CHECK EDIT PERMISSION API START ===');

            $action = $this->input->post('action');
            $topic_id = $this->input->post('topic_id');
            $user_id = $this->input->post('user_id');
            $user_type = $this->input->post('user_type');

            log_message('info', "API Params: action={$action}, topic_id={$topic_id}, user_id={$user_id}, user_type={$user_type}");

            if ($action !== 'check_edit_permission' || empty($topic_id)) {
                log_message('error', 'Invalid API parameters');
                echo json_encode([
                    'success' => false,
                    'can_edit' => false,
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            // โหลด model และดึงข้อมูลกระทู้
            $this->load->model('q_a_model');
            $topic = $this->q_a_model->read($topic_id);

            if (!$topic) {
                log_message('error', "Topic {$topic_id} not found");
                echo json_encode([
                    'success' => false,
                    'can_edit' => false,
                    'message' => 'Topic not found'
                ]);
                return;
            }

            log_message('info', "Original topic data: user_id={$topic->q_a_user_id}, user_type={$topic->q_a_user_type}, email={$topic->q_a_email}");

            // *** ตรวจสอบและแก้ไข overflow user_id ***
            $original_user_id = $topic->q_a_user_id;
            $can_edit = false;
            $auto_fixed = false;

            // *** กรณี Staff/Admin - อนุญาตได้ทุกกระทู้ ***
            $session_m_id = $this->session->userdata('m_id');
            $session_m_system = $this->session->userdata('m_system');

            if ($session_m_id) {
                $can_edit = true;
                $reason = "Staff/Admin can edit all topics";
                log_message('info', "Staff/Admin {$session_m_id} allowed to edit topic {$topic_id}");
            }
            // *** กรณี Public User ***
            else {
                $session_mp_id = $this->session->userdata('mp_id');
                $session_mp_email = $this->session->userdata('mp_email');

                if ($session_mp_id && $session_mp_email) {
                    // ใช้ฟังก์ชัน fixUserIdOverflow เหมือนใน view
                    $fixed_user_id = $this->fixUserIdOverflow($session_mp_id, $session_mp_email);

                    log_message('info', "Public user: session_mp_id={$session_mp_id}, fixed_user_id={$fixed_user_id}");

                    // ตรวจสอบความเป็นเจ้าของ
                    if ($topic->q_a_user_id && $fixed_user_id == $topic->q_a_user_id) {
                        $can_edit = true;
                        $reason = "User owns this topic";
                    }
                    // *** สำคัญ: ตรวจสอบกระทู้ที่มี overflow user_id ***
                    elseif (
                        ($topic->q_a_user_id == 2147483647 || $topic->q_a_user_id == '2147483647') &&
                        $topic->q_a_user_type === 'public' &&
                        !empty($topic->q_a_email)
                    ) {

                        log_message('info', "Checking overflow topic: topic_email={$topic->q_a_email}, session_email={$session_mp_email}");

                        // ตรวจสอบว่าอีเมลตรงกันหรือไม่
                        if ($topic->q_a_email === $session_mp_email) {
                            $can_edit = true;
                            $reason = "User owns overflow topic via email match";

                            // *** อัพเดท user_id ในฐานข้อมูลให้ถูกต้อง ***
                            $update_result = $this->db->where('q_a_id', $topic_id)
                                ->update('tbl_q_a', ['q_a_user_id' => $fixed_user_id]);

                            if ($update_result) {
                                $auto_fixed = true;
                                $topic->q_a_user_id = $fixed_user_id; // อัพเดทใน object
                                log_message('info', "SUCCESS: Auto-fixed topic {$topic_id} user_id: {$original_user_id} -> {$fixed_user_id}");
                            } else {
                                log_message('error', "FAILED: Could not update topic {$topic_id} user_id");
                            }
                        } else {
                            $reason = "Email mismatch: topic={$topic->q_a_email}, session={$session_mp_email}";
                        }
                    } else {
                        $reason = "User does not own this topic";
                    }
                } else {
                    $reason = "User not logged in as public user";
                }
            }

            // ส่งผลลัพธ์
            $response = [
                'success' => true,
                'can_edit' => $can_edit,
                'topic_id' => $topic_id,
                'message' => $reason,
                'auto_fixed' => $auto_fixed,
                'debug_info' => [
                    'original_topic_user_id' => $original_user_id,
                    'current_topic_user_id' => $topic->q_a_user_id,
                    'topic_user_type' => $topic->q_a_user_type,
                    'topic_email' => $topic->q_a_email,
                    'session_mp_id' => $this->session->userdata('mp_id'),
                    'session_m_id' => $this->session->userdata('m_id'),
                    'session_email' => $this->session->userdata('mp_email') ?: $this->session->userdata('m_email'),
                    'requested_user_id' => $user_id,
                    'requested_user_type' => $user_type
                ]
            ];

            log_message('info', "API Result: can_edit={$can_edit}, reason={$reason}, auto_fixed={$auto_fixed}");
            log_message('info', '=== CHECK EDIT PERMISSION API END ===');

            echo json_encode($response);

        } catch (Exception $e) {
            log_message('error', 'Check edit permission API error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'can_edit' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }




    public function fix_overflow_data()
    {
        header('Content-Type: application/json');

        // เฉพาะ admin เท่านั้นที่เรียกได้
        if (!$this->session->userdata('m_id')) {
            log_message('debug', 'Unauthorized access to fix_overflow_data');
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }

        try {
            log_message('info', '=== BULK FIX OVERFLOW DATA START ===');

            $fixed_topics = 0;
            $fixed_replies = 0;

            // 1. แก้ไขข้อมูลในตาราง tbl_q_a
            $overflow_topics = $this->db->select('q_a_id, q_a_email, q_a_user_id, q_a_user_type')
                ->where('q_a_user_id', '2147483647')
                ->or_where('q_a_user_id', 2147483647)
                ->get('tbl_q_a')
                ->result();

            log_message('info', 'Found ' . count($overflow_topics) . ' topics with overflow user_id');

            foreach ($overflow_topics as $topic) {
                if (!empty($topic->q_a_email)) {
                    $correct_user_id = $this->getCorrectUserIdByEmail($topic->q_a_email, $topic->q_a_user_type);

                    if ($correct_user_id && $correct_user_id != $topic->q_a_user_id) {
                        $update_result = $this->db->where('q_a_id', $topic->q_a_id)
                            ->update('tbl_q_a', ['q_a_user_id' => $correct_user_id]);

                        if ($update_result) {
                            log_message('info', "Fixed topic {$topic->q_a_id}: {$topic->q_a_user_id} -> {$correct_user_id}");
                            $fixed_topics++;
                        }
                    }
                }
            }

            // 2. แก้ไขข้อมูลในตาราง tbl_q_a_reply
            $overflow_replies = $this->db->select('q_a_reply_id, q_a_reply_email, q_a_reply_user_id, q_a_reply_user_type')
                ->where('q_a_reply_user_id', '2147483647')
                ->or_where('q_a_reply_user_id', 2147483647)
                ->get('tbl_q_a_reply')
                ->result();

            log_message('info', 'Found ' . count($overflow_replies) . ' replies with overflow user_id');

            foreach ($overflow_replies as $reply) {
                if (!empty($reply->q_a_reply_email)) {
                    $correct_user_id = $this->getCorrectUserIdByEmail($reply->q_a_reply_email, $reply->q_a_reply_user_type);

                    if ($correct_user_id && $correct_user_id != $reply->q_a_reply_user_id) {
                        $update_result = $this->db->where('q_a_reply_id', $reply->q_a_reply_id)
                            ->update('tbl_q_a_reply', ['q_a_reply_user_id' => $correct_user_id]);

                        if ($update_result) {
                            log_message('info', "Fixed reply {$reply->q_a_reply_id}: {$reply->q_a_reply_user_id} -> {$correct_user_id}");
                            $fixed_replies++;
                        }
                    }
                }
            }

            log_message('info', "=== BULK FIX COMPLETED: {$fixed_topics} topics, {$fixed_replies} replies ===");

            echo json_encode([
                'success' => true,
                'message' => "แก้ไขข้อมูลสำเร็จ: {$fixed_topics} กระทู้, {$fixed_replies} ความคิดเห็น",
                'data' => [
                    'topics' => $fixed_topics,
                    'replies' => $fixed_replies,
                    'total_processed' => $fixed_topics + $fixed_replies
                ]
            ]);

        } catch (Exception $e) {
            log_message('error', 'Fix overflow data error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }





    private function fixUserIdOverflow($session_id, $email)
    {
        // ตรวจสอบว่าเป็น overflow หรือไม่
        if ($session_id == 2147483647 || $session_id == '2147483647' || empty($session_id)) {
            log_message('info', "Controller detected user_id overflow: {$session_id} for email: {$email}");
        }

        // *** ใช้ auto increment id เสมอสำหรับ public user ***

        // ตรวจสอบใน tbl_member_public ก่อน
        $public_user = $this->db->select('id, mp_id')
            ->where('mp_email', $email)
            ->get('tbl_member_public')
            ->row();

        if ($public_user) {
            log_message('info', "Controller using consistent auto increment ID: {$public_user->id} for email: {$email} (original mp_id: {$session_id})");
            return $public_user->id; // *** ใช้ auto increment id เสมอ ***
        }

        // ถ้าไม่เจอ ตรวจสอบใน tbl_member
        $staff_user = $this->db->select('m_id')
            ->where('m_email', $email)
            ->get('tbl_member')
            ->row();

        if ($staff_user) {
            log_message('info', "Controller using staff m_id: {$staff_user->m_id} for email: {$email}");
            return $staff_user->m_id;
        }

        // ถ้าไม่เจออะไรเลย return session_id เดิม
        log_message('error', "Controller could not find user for email: {$email}, returning original session_id: {$session_id}");
        return $session_id;
    }



    /**
     * ฟังก์ชันจัดการอัปโหลดรูปภาพสำหรับกระทู้ (สำหรับการแก้ไข)
     */
    private function handle_topic_images($topic_id)
    {
        if (!empty($_FILES['q_a_imgs']['name'][0])) {
            $this->load->library('upload');

            $upload_path = './docs/img/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config = [
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|png|jpeg|webp',
                'max_size' => 5120, // 5MB
                'encrypt_name' => true
            ];

            $this->upload->initialize($config);

            $files = $_FILES['q_a_imgs'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if (!empty($files['name'][$i])) {
                    $_FILES['single_file'] = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    if ($this->upload->do_upload('single_file')) {
                        $upload_data = $this->upload->data();

                        // สร้างไฟล์สำหรับ LINE (ถ้าต้องการ)
                        $file_ext = pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
                        $line_filename = 'line_' . time() . '_' . uniqid() . '.' . $file_ext;
                        $final_line_filename = $upload_data['file_name']; // default

                        if (copy($upload_data['full_path'], $upload_path . $line_filename)) {
                            $final_line_filename = $line_filename;
                        }

                        $image_data = [
                            'q_a_img_ref_id' => $topic_id,
                            'q_a_img_img' => $upload_data['file_name'],
                            'q_a_img_line' => $final_line_filename
                        ];

                        $this->db->insert('tbl_q_a_img', $image_data);
                        log_message('info', 'New image uploaded for topic edit: ' . $upload_data['file_name']);
                    } else {
                        log_message('error', 'Image upload error in edit: ' . $this->upload->display_errors());
                    }
                }
            }
        }
    }

    /**
     * ฟังก์ชันตรวจสอบและดึงข้อมูลผู้ใช้ที่ถูกต้อง (เหมือนเดิม)
     */
    private function get_fixed_user_info()
    {
        $user_info = [
            'is_logged_in' => false,
            'user_id' => null,
            'user_type' => 'guest',
            'name' => '',
            'email' => ''
        ];

        // ตรวจสอบ Public User
        if ($this->session->userdata('mp_id')) {
            $session_mp_id = $this->session->userdata('mp_id');
            $user_email = $this->session->userdata('mp_email');
            $fixed_user_id = $this->fix_user_id_overflow($session_mp_id, $user_email, 'public');

            $user_info = [
                'is_logged_in' => true,
                'user_id' => $fixed_user_id,
                'user_type' => 'public',
                'name' => trim($this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname')),
                'email' => $user_email
            ];
        }
        // ตรวจสอบ Staff User
        elseif ($this->session->userdata('m_id')) {
            $session_m_id = $this->session->userdata('m_id');
            $user_email = $this->session->userdata('m_email');
            $fixed_user_id = $this->fix_user_id_overflow($session_m_id, $user_email, 'staff');

            // กำหนดระดับ staff
            $m_system = $this->session->userdata('m_system');
            $actual_user_type = 'staff';

            if ($m_system) {
                switch ($m_system) {
                    case 'system_admin':
                        $actual_user_type = 'system_admin';
                        break;
                    case 'super_admin':
                        $actual_user_type = 'super_admin';
                        break;
                    case 'user_admin':
                        $actual_user_type = 'user_admin';
                        break;
                    default:
                        $actual_user_type = 'staff';
                }
            }

            $user_info = [
                'is_logged_in' => true,
                'user_id' => $fixed_user_id,
                'user_type' => $actual_user_type,
                'name' => trim($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname')),
                'email' => $user_email
            ];
        }

        return $user_info;
    }

    /**
     * 🔧 Enhanced update_reply() Method - เพิ่ม Combined Vulgar & URL check
     * 
     * ลำดับการทำงาน:
     * 1. Validation เบื้องต้น
     * 2. Permission check
     * 3. Combined Vulgar & URL check
     * 4. Image handling (remove old/upload new)
     * 5. Database update
     * 6. Response
     */
    public function update_reply()
    {
        // เปิด error reporting เพื่อ debug
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // ปิดเพื่อไม่ให้แสดงใน JSON response

        // Log การเริ่มต้น
        log_message('info', '=== UPDATE REPLY ENHANCED START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));
        log_message('info', 'FILES data: ' . print_r($_FILES, true));

        try {
            // ========================================================================
            // 1. การตรวจสอบเบื้องต้น (Initial Validation)
            // ========================================================================

            // ตรวจสอบ HTTP Method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                log_message('error', 'Invalid HTTP method: ' . $this->input->server('REQUEST_METHOD'));
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed', 'error_type' => 'method_error']);
                exit();
            }

            // รับข้อมูลจากฟอร์ม
            $reply_id = $this->input->post('reply_id');
            $q_a_reply_detail = $this->input->post('q_a_reply_detail');
            $q_a_reply_by = $this->input->post('q_a_reply_by');
            $q_a_reply_email = $this->input->post('q_a_reply_email');
            $remove_old_images = $this->input->post('remove_old_images');

            log_message('info', "Received data - Reply ID: {$reply_id}, Detail: " . substr($q_a_reply_detail, 0, 100) . "..., BY: {$q_a_reply_by}");

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($reply_id) || empty($q_a_reply_detail) || empty($q_a_reply_by)) {
                log_message('error', 'Missing required fields');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน',
                    'error_type' => 'validation_error'
                ]);
                exit();
            }

            // ตรวจสอบว่ามี reply นี้อยู่จริงหรือไม่
            $this->db->select('q_a_reply_id, q_a_reply_user_type, q_a_reply_user_id, q_a_reply_by, q_a_reply_email, q_a_reply_detail');
            $this->db->where('q_a_reply_id', $reply_id);
            $query = $this->db->get('tbl_q_a_reply');

            if ($this->db->error()['code'] !== 0) {
                log_message('error', 'Database error: ' . print_r($this->db->error(), true));
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection error',
                    'error_type' => 'database_error'
                ]);
                exit();
            }

            $existing_reply = $query->row();
            log_message('info', 'Existing reply: ' . print_r($existing_reply, true));

            if (!$existing_reply) {
                log_message('error', 'Reply not found: ' . $reply_id);
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบการตอบกลับที่ต้องการแก้ไข',
                    'error_type' => 'reply_not_found'
                ]);
                exit();
            }

            // ========================================================================
            // 2. ตรวจสอบสิทธิ์การแก้ไข (Permission Check)
            // ========================================================================

            $can_edit = $this->checkReplyEditPermission($existing_reply);
            log_message('info', 'Can edit reply: ' . ($can_edit ? 'YES' : 'NO'));

            if (!$can_edit) {
                log_message('error', 'No permission to edit reply: ' . $reply_id);
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์แก้ไขการตอบกลับนี้',
                    'error_type' => 'permission_denied'
                ]);
                exit();
            }

            // ========================================================================
            // 3. ตรวจสอบคำหยาบและ URL (Combined Vulgar & URL Check)
            // ========================================================================

            log_message('info', 'Starting vulgar and URL content check for reply...');

            $combined_content = $q_a_reply_detail; // Reply มีแค่ detail field

            log_message('info', 'Reply content to check: ' . $combined_content);
            log_message('debug', 'Reply ID: ' . $reply_id . ', By: ' . $q_a_reply_by . ', Email: ' . $q_a_reply_email);

            // *** โหลด vulgar_check library โดยตรง ***
            $this->load->library('vulgar_check');

            // เตรียม field data สำหรับการตรวจสอบ (update_reply specific fields)
            $check_fields = [
                'q_a_reply_detail' => $q_a_reply_detail,
                'q_a_reply_by' => $q_a_reply_by,
                'q_a_reply_email' => $q_a_reply_email
            ];

            log_message('info', 'Checking vulgar and URL content with reply fields: ' . json_encode(array_keys($check_fields)));

            // *** เรียกใช้ vulgar_check library โดยตรง ***
            $vulgar_result = $this->vulgar_check->check_form($check_fields);

            log_message('debug', 'Vulgar check result: ' . json_encode($vulgar_result));

            // *** ตรวจสอบผลลัพธ์ ***
            $vulgar_detected = false;
            $url_detected = false;
            $vulgar_words = array();
            $error_message = '';
            $error_type = '';

            // ตรวจสอบคำหยาบ
            if (isset($vulgar_result['has_vulgar']) && $vulgar_result['has_vulgar']) {
                $vulgar_detected = true;

                // รวบรวมคำหยาบจากทุก field
                $all_vulgar_words = [];
                if (isset($vulgar_result['results']) && is_array($vulgar_result['results'])) {
                    foreach ($vulgar_result['results'] as $field => $field_result) {
                        if (isset($field_result['vulgar_words']) && is_array($field_result['vulgar_words'])) {
                            $all_vulgar_words = array_merge($all_vulgar_words, $field_result['vulgar_words']);
                        }
                    }
                }

                $vulgar_words = array_unique($all_vulgar_words);
                $error_message = 'พบคำไม่เหมาะสม: ' . implode(', ', $vulgar_words);
                $error_type = 'vulgar_content';

                log_message('debug', 'Vulgar words detected in reply update: ' . implode(', ', $vulgar_words));
            }

            // ตรวจสอบ URL
            if (!$vulgar_detected && isset($vulgar_result['has_url']) && $vulgar_result['has_url']) {
                $url_detected = true;
                $error_message = 'ไม่อนุญาตให้มี URL ในข้อความตอบกลับ';
                $error_type = 'url_content';

                log_message('debug', 'URL detected in reply update content');
            }

            // *** ส่ง JSON Response เมื่อพบคำหยาบหรือ URL ***
            if ($vulgar_detected || $url_detected) {

                // ตั้งค่า flash data สำหรับการแสดงผล
                if ($vulgar_detected) {
                    $this->session->set_flashdata('save_vulgar', true);
                    $this->session->set_flashdata('vulgar_words', $vulgar_words);
                    $this->session->set_flashdata('vulgar_message', $error_message);
                }

                if ($url_detected) {
                    $this->session->set_flashdata('save_url_detected', true);
                    $this->session->set_flashdata('url_message', $error_message);
                }

                // ปิด output buffering
                while (ob_get_level()) {
                    ob_end_clean();
                }

                // ตั้งค่า headers
                header('Content-Type: application/json; charset=utf-8');
                header('Cache-Control: no-cache, must-revalidate');
                http_response_code(400);

                // ส่ง JSON response
                $response_data = [
                    'success' => false,
                    'message' => $error_message,
                    'error_type' => $error_type
                ];

                if ($vulgar_detected) {
                    $response_data['vulgar_detected'] = true;
                    $response_data['vulgar_words'] = $vulgar_words;
                }

                if ($url_detected) {
                    $response_data['url_detected'] = true;
                }

                // เพิ่ม debug info สำหรับ reply
                $response_data['debug_info'] = [
                    'reply_id' => $reply_id,
                    'detected_content' => substr($combined_content, 0, 100) . '...',
                    'timestamp' => date('Y-m-d H:i:s')
                ];

                $json_response = json_encode($response_data, JSON_UNESCAPED_UNICODE);
                log_message('info', 'Sending reply vulgar/URL detection JSON response: ' . $json_response);

                echo $json_response;

                if (function_exists('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                } else {
                    flush();
                }

                exit();
            }

            log_message('info', 'Reply vulgar and URL check passed - no inappropriate content detected');

            // *** Debug log สำหรับการทดสอบ reply ***
            if (stripos($combined_content, 'สุภาพ') !== false) {
                log_message('debug', 'REPLY TEST WORD "สุภาพ" found but not detected by vulgar system!');
                log_message('debug', 'Reply content checked: ' . $combined_content);
                log_message('debug', 'Reply vulgar check result: ' . json_encode($vulgar_result));
                log_message('debug', 'Please check vulgar detection configuration for replies');
            }

            if (stripos($combined_content, 'Son of a bitch') !== false) {
                log_message('debug', 'REPLY TEST PHRASE "Son of a bitch" found but not detected by vulgar system!');
                log_message('debug', 'Reply content checked: ' . $combined_content);
                log_message('debug', 'Reply vulgar check result: ' . json_encode($vulgar_result));
                log_message('debug', 'Please verify API connection and word database for replies');
            }

            if (preg_match('/https?:\/\/|www\./i', $combined_content)) {
                log_message('debug', 'REPLY TEST URL found but not detected by URL system!');
                log_message('debug', 'Reply content checked: ' . $combined_content);
                log_message('debug', 'Reply URL check result: ' . json_encode($vulgar_result));
                log_message('debug', 'Please check URL detection configuration for replies');
            }

            // ========================================================================
            // 4. เตรียมข้อมูลสำหรับอัพเดท (Prepare Update Data)
            // ========================================================================

            $update_data = [
                'q_a_reply_detail' => $q_a_reply_detail,
                'q_a_reply_by' => $q_a_reply_by
            ];

            // เพิ่ม timestamp ถ้ามี column
            if ($this->db->field_exists('q_a_reply_date_update', 'tbl_q_a_reply')) {
                $update_data['q_a_reply_date_update'] = date('Y-m-d H:i:s');
                log_message('info', 'Added update timestamp');
            }

            // เพิ่ม user id ถ้ามี column
            if ($this->db->field_exists('q_a_reply_update_by', 'tbl_q_a_reply')) {
                $current_user_id = $this->getCurrentUserId();
                if ($current_user_id) {
                    $update_data['q_a_reply_update_by'] = $current_user_id;
                    log_message('info', 'Added update user ID: ' . $current_user_id);
                }
            }

            // อัพเดท email ถ้าไม่ใช่ user ที่ login แล้ว
            if (empty($this->session->userdata('mp_id')) && empty($this->session->userdata('m_id'))) {
                if (!empty($q_a_reply_email)) {
                    $update_data['q_a_reply_email'] = $q_a_reply_email;
                    log_message('info', 'Updated email for guest user');
                }
            }

            log_message('info', 'Update data prepared: ' . print_r($update_data, true));

            // ========================================================================
            // 5. เริ่ม Transaction และจัดการรูปภาพ (Transaction Start & Image Handling)
            // ========================================================================

            // เริ่ม Transaction
            $this->db->trans_start();

            // *** ลบรูปภาพเก่าทั้งหมดก่อน (ถ้าเลือกลบ) ***
            if ($remove_old_images == '1' && $this->db->table_exists('tbl_q_a_reply_img')) {
                log_message('info', 'Removing old reply images for reply_id: ' . $reply_id);

                // ดึงรายการรูปเก่า
                $old_images = $this->db->where('q_a_reply_img_ref_id', $reply_id)
                    ->get('tbl_q_a_reply_img')
                    ->result();

                // ลบไฟล์รูปจาก server
                foreach ($old_images as $img) {
                    $file_path = './docs/img/' . $img->q_a_reply_img_img;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                        log_message('info', 'Deleted old reply image file: ' . $img->q_a_reply_img_img);
                    }

                    // ลบไฟล์ line (ถ้ามี)
                    if (isset($img->q_a_reply_img_line)) {
                        $line_file_path = './docs/img/' . $img->q_a_reply_img_line;
                        if (file_exists($line_file_path)) {
                            unlink($line_file_path);
                            log_message('info', 'Deleted old reply line image file: ' . $img->q_a_reply_img_line);
                        }
                    }
                }

                // ลบ records ในตาราง tbl_q_a_reply_img
                $this->db->where('q_a_reply_img_ref_id', $reply_id);
                $this->db->delete('tbl_q_a_reply_img');

                log_message('info', 'Removed ' . count($old_images) . ' old reply images');
            }

            // จัดการการอัพโหลดรูปภาพใหม่ (ถ้ามี)
            if (!empty($_FILES['q_a_reply_imgs']['name'][0])) {
                log_message('info', 'Processing new reply image uploads...');

                // เรียกใช้ฟังก์ชันอัพโหลดรูปภาพสำหรับ reply
                if (method_exists($this, 'process_reply_images')) {
                    $upload_result = $this->process_reply_images($reply_id);

                    // ตรวจสอบผลลัพธ์การอัพโหลด
                    if ($upload_result === false) {
                        log_message('debug', 'Reply image upload failed but continuing with text update');
                        // ไม่ return error เพราะ text update ยังทำได้
                    } else {
                        log_message('info', 'Reply images uploaded successfully');
                    }
                } else {
                    log_message('debug', 'process_reply_images method not found, skipping image upload');
                }
            }

            // ========================================================================
            // 6. อัพเดทข้อมูลในฐานข้อมูล (Database Update)
            // ========================================================================

            // อัพเดทข้อมูลในฐานข้อมูล
            $this->db->where('q_a_reply_id', $reply_id);
            $update_result = $this->db->update('tbl_q_a_reply', $update_data);

            log_message('info', 'Update result: ' . ($update_result ? 'SUCCESS' : 'FAILED'));
            log_message('info', 'Last query: ' . $this->db->last_query());

            if ($this->db->error()['code'] !== 0) {
                $this->db->trans_rollback();
                log_message('error', 'Update error: ' . print_r($this->db->error(), true));
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $this->db->error()['message'],
                    'error_type' => 'database_error'
                ]);
                exit();
            }

            if (!$update_result) {
                $this->db->trans_rollback();
                log_message('error', 'Update failed but no DB error');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถอัพเดทข้อมูลได้',
                    'error_type' => 'update_failed'
                ]);
                exit();
            }

            // เพิ่ม: สร้างการแจ้งเตือนสำหรับการแก้ไข reply
            if (method_exists($this, 'createReplyUpdateNotification')) {
                $this->createReplyUpdateNotification($reply_id, $existing_reply, $q_a_reply_detail, $q_a_reply_by);
            }

            // Commit Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed during reply update');
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการทำ Transaction',
                    'error_type' => 'transaction_failed'
                ]);
                exit();
            }

            // ========================================================================
            // 7. ส่งกลับผลลัพธ์สำเร็จ (Success Response)
            // ========================================================================

            // Log การแก้ไข
            log_message('info', "Reply {$reply_id} updated successfully by: " . $this->getCurrentUserInfo());

            // ส่งกลับผลลัพธ์สำเร็จ
            $this->session->set_flashdata('save_success', true);
            $this->session->set_flashdata('reply_updated', $reply_id);

            log_message('info', '=== UPDATE REPLY ENHANCED END (SUCCESS) ===');

            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'แก้ไขการตอบกลับสำเร็จ',
                'reply_id' => $reply_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Update reply exception: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดภายในระบบ: ' . $e->getMessage(),
                'error_type' => 'system_error',
                'debug_info' => [
                    'exception_message' => $e->getMessage(),
                    'exception_file' => $e->getFile(),
                    'exception_line' => $e->getLine()
                ]
            ]);
        } catch (Error $e) {
            log_message('error', 'Update reply fatal error: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดร้าแรง: ' . $e->getMessage(),
                'error_type' => 'fatal_error'
            ]);
        }
    }


    /**
     * ฟังก์ชันลบ Reply
     */
    public function delete_reply()
    {
        header('Content-Type: application/json');

        try {
            log_message('info', '=== DELETE REPLY DEBUG START ===');

            // ตรวจสอบ HTTP Method
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $reply_id = $this->input->post('reply_id');

            if (empty($reply_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสการตอบกลับ']);
                return;
            }

            // ตรวจสอบว่ามี reply นี้อยู่จริงหรือไม่
            $existing_reply = $this->db->select('q_a_reply_id, q_a_reply_user_type, q_a_reply_user_id, q_a_reply_by, q_a_reply_detail, q_a_reply_ref_id')
                ->where('q_a_reply_id', $reply_id)
                ->get('tbl_q_a_reply')
                ->row();

            if (!$existing_reply) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'ไม่พบการตอบกลับที่ต้องการลบ']);
                return;
            }

            // ตรวจสอบสิทธิ์การลบ
            $can_delete = $this->checkReplyEditPermission($existing_reply);

            if (!$can_delete) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบการตอบกลับนี้']);
                return;
            }

            // เก็บข้อมูลสำหรับ notification ก่อนลบ
            $reply_detail = $existing_reply->q_a_reply_detail;
            $deleted_by = $this->getCurrentUserInfo();

            // เริ่ม Transaction
            $this->db->trans_start();

            // ลบรูปภาพที่เกี่ยวข้อง (ถ้ามี)
            if ($this->db->table_exists('tbl_q_a_reply_img')) {
                $images = $this->db->where('q_a_reply_img_ref_id', $reply_id)
                    ->get('tbl_q_a_reply_img')
                    ->result();

                foreach ($images as $img) {
                    $file_path = './docs/img/' . $img->q_a_reply_img_img;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                        log_message('info', 'Deleted reply image file: ' . $img->q_a_reply_img_img);
                    }
                }

                // ลบ records ในตาราง tbl_q_a_reply_img
                $this->db->where('q_a_reply_img_ref_id', $reply_id);
                $this->db->delete('tbl_q_a_reply_img');
            }

            // ลบการแจ้งเตือนที่เกี่ยวข้องกับ reply นี้
            if ($this->db->table_exists('tbl_notification')) {
                $this->db->where('notification_ref_id', $existing_reply->q_a_reply_ref_id);
                $this->db->where('notification_type', 'qa_reply');
                $this->db->delete('tbl_notification');
            }

            // สร้างการแจ้งเตือนสำหรับการลบ reply
            $this->createReplyDeleteNotification($reply_id, $existing_reply, $deleted_by);

            // ลบ reply
            $this->db->where('q_a_reply_id', $reply_id);
            $delete_result = $this->db->delete('tbl_q_a_reply');

            if (!$delete_result) {
                $this->db->trans_rollback();
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบการตอบกลับ']);
                return;
            }

            // Commit Transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการทำ Transaction']);
                return;
            }

            log_message('info', "Reply {$reply_id} deleted successfully by: {$deleted_by}");

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'ลบการตอบกลับสำเร็จ'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Delete reply exception: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายในระบบ']);
        }
    }

    /**
     * API สำหรับตรวจสอบสิทธิ์การแก้ไข Reply
     */
    public function check_reply_edit_permission()
    {
        header('Content-Type: application/json');

        try {
            $action = $this->input->post('action');
            $reply_id = $this->input->post('reply_id');
            $user_id = $this->input->post('user_id');
            $user_type = $this->input->post('user_type');

            if ($action !== 'check_reply_edit_permission' || empty($reply_id)) {
                echo json_encode([
                    'success' => false,
                    'can_edit' => false,
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            // ดึงข้อมูล reply
            $reply = $this->db->select('q_a_reply_id, q_a_reply_user_type, q_a_reply_user_id, q_a_reply_email')
                ->where('q_a_reply_id', $reply_id)
                ->get('tbl_q_a_reply')
                ->row();

            if (!$reply) {
                echo json_encode([
                    'success' => false,
                    'can_edit' => false,
                    'message' => 'Reply not found'
                ]);
                return;
            }

            $can_edit = $this->checkReplyEditPermission($reply);

            echo json_encode([
                'success' => true,
                'can_edit' => $can_edit,
                'reply_id' => $reply_id
            ]);

        } catch (Exception $e) {
            log_message('error', 'Check reply edit permission error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'can_edit' => false,
                'message' => 'Server error'
            ]);
        }
    }

    /**
     * ฟังก์ชันตรวจสอบสิทธิ์การแก้ไข Reply
     */
    private function checkReplyEditPermission($reply)
    {
        // ตรวจสอบว่าเป็น Staff/Admin หรือไม่
        $m_id = $this->session->userdata('m_id');
        $m_system = $this->session->userdata('m_system');

        if ($m_id) {
            // Staff/Admin สามารถแก้ไขได้ทุก reply
            if (in_array($m_system, ['system_admin', 'super_admin', 'user_admin']) || !empty($m_id)) {
                return true;
            }
        }

        // ตรวจสอบ Public User
        $mp_id = $this->session->userdata('mp_id');
        $mp_email = $this->session->userdata('mp_email');

        if ($mp_id && $mp_email) {
            $fixed_user_id = $this->fixUserIdOverflow($mp_id, $mp_email);

            // ตรวจสอบความเป็นเจ้าของ
            if ($reply->q_a_reply_user_id && $fixed_user_id == $reply->q_a_reply_user_id) {
                return true;
            }

            // ตรวจสอบกระทู้ที่มี overflow user_id
            if (
                ($reply->q_a_reply_user_id == 2147483647 || $reply->q_a_reply_user_id == '2147483647') &&
                $reply->q_a_reply_user_type === 'public' &&
                !empty($reply->q_a_reply_email)
            ) {

                if ($reply->q_a_reply_email === $mp_email) {
                    // อัพเดท user_id ในฐานข้อมูลให้ถูกต้อง
                    $this->db->where('q_a_reply_id', $reply->q_a_reply_id)
                        ->update('tbl_q_a_reply', ['q_a_reply_user_id' => $fixed_user_id]);
                    return true;
                }
            }

            // ตรวจสอบ reply เก่าที่ไม่มี user_id
            if (($reply->q_a_reply_user_type === 'public' || $reply->q_a_reply_user_type === 'staff') && empty($reply->q_a_reply_user_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * สร้างการแจ้งเตือนสำหรับการแก้ไข Reply
     */
    private function createReplyUpdateNotification($reply_id, $original_reply, $new_detail, $updated_by)
    {
        try {
            // สร้างการแจ้งเตือนแบบง่าย
            if ($this->db->table_exists('tbl_notification')) {
                $notification_data = [
                    'title' => 'แก้ไขตอบกลับ',
                    'notification_title' => 'แก้ไขการตอบกลับ',
                    'notification_message' => $updated_by . ' ได้แก้ไขการตอบกลับ',
                    'notification_type' => 'qa_reply_update',
                    'notification_ref_id' => $original_reply->q_a_reply_ref_id,
                    'notification_from_user' => $updated_by,
                    'notification_date' => date('Y-m-d H:i:s'),
                    'notification_status' => 'unread'
                ];

                if ($this->db->field_exists('notification_reply_id', 'tbl_notification')) {
                    $notification_data['notification_reply_id'] = $reply_id;
                }

                $this->db->insert('tbl_notification', $notification_data);
            }

        } catch (Exception $e) {
            log_message('error', 'Reply update notification error: ' . $e->getMessage());
        }
    }

    /**
     * สร้างการแจ้งเตือนสำหรับการลบ Reply
     */
    private function createReplyDeleteNotification($reply_id, $reply_data, $deleted_by)
    {
        try {
            if ($this->db->table_exists('tbl_notification')) {
                $notification_data = [
                    'title' => 'ลบตอบกลับ',
                    'notification_title' => 'ลบการตอบกลับ',
                    'notification_message' => $deleted_by . ' ได้ลบการตอบกลับ',
                    'notification_type' => 'qa_reply_delete',
                    'notification_ref_id' => $reply_data->q_a_reply_ref_id,
                    'notification_from_user' => $deleted_by,
                    'notification_date' => date('Y-m-d H:i:s'),
                    'notification_status' => 'unread'
                ];

                $this->db->insert('tbl_notification', $notification_data);
            }

        } catch (Exception $e) {
            log_message('error', 'Reply delete notification error: ' . $e->getMessage());
        }
    }





    private function parse_address($full_address)
    {
        $parsed = [
            'additional_address' => '',
            'district' => '',
            'amphoe' => '',
            'province' => '',
            'zipcode' => ''
        ];

        if (empty($full_address)) {
            return $parsed;
        }

        try {
            // แยกรหัสไปรษณีย์ (5 หลักสุดท้าย)
            if (preg_match('/(\d{5})(?:\s*)$/', $full_address, $matches)) {
                $parsed['zipcode'] = $matches[1];
                $full_address = preg_replace('/\s*' . preg_quote($matches[1]) . '\s*$/', '', $full_address);
            }

            // แยกจังหวัด
            if (preg_match('/จังหวัด([^\s]+)/', $full_address, $matches)) {
                $parsed['province'] = trim($matches[1]);
                $full_address = preg_replace('/\s*จังหวัด[^\s]+\s*/', ' ', $full_address);
            }

            // แยกอำเภอ
            if (preg_match('/อำเภอ([^\s]+)/', $full_address, $matches)) {
                $parsed['amphoe'] = trim($matches[1]);
                $full_address = preg_replace('/\s*อำเภอ[^\s]+\s*/', ' ', $full_address);
            }

            // แยกตำบล
            if (preg_match('/ตำบล([^\s]+)/', $full_address, $matches)) {
                $parsed['district'] = trim($matches[1]);
                $full_address = preg_replace('/\s*ตำบล[^\s]+\s*/', ' ', $full_address);
            }

            // ที่เหลือเป็นที่อยู่เพิ่มเติม
            $parsed['additional_address'] = trim($full_address);

            log_message('info', 'Parsed address: ' . json_encode($parsed));

        } catch (Exception $e) {
            log_message('error', 'Error parsing address: ' . $e->getMessage());
            $parsed['additional_address'] = $full_address;
        }

        return $parsed;
    }







    /**
     * ⭐ แจ้งเรื่อง ร้องเรียน
     */
    public function adding_complain($type = 'general')
    {
        try {
            // ⭐ รับ category_id จาก URL
            $selected_category_id = $this->input->get('cat_id');

            // สร้าง mapping ระหว่าง code กับข้อความภาษาไทย
            $complain_types = [
                'general' => [
                    'title' => 'แจ้งเรื่อง ร้องเรียน/ร้องทุกข์',
                    'subtitle' => 'ระบบรับเรื่องร้องเรียนทั่วไป',
                    'icon' => 'fa-comments',
                    'color' => '#e55a2b'
                ],
                'hr' => [
                    'title' => 'แจ้งเรื่องร้องเรียน ด้านทรัพยากรส่วนบุคคล',
                    'subtitle' => 'ระบบรับเรื่องร้องเรียนด้าน HR',
                    'icon' => 'fa-users',
                    'color' => '#3498db'
                ]
            ];

            // ตั้งค่า page title
            if (isset($complain_types[$type])) {
                $data['page_title'] = $complain_types[$type]['title'];
                $data['page_subtitle'] = $complain_types[$type]['subtitle'];
                $data['page_icon'] = $complain_types[$type]['icon'];
                $data['page_color'] = $complain_types[$type]['color'];
                $data['complain_type'] = $type;
            } else {
                $data['page_title'] = $complain_types['general']['title'];
                $data['page_subtitle'] = $complain_types['general']['subtitle'];
                $data['page_icon'] = $complain_types['general']['icon'];
                $data['page_color'] = $complain_types['general']['color'];
                $data['complain_type'] = 'general';
            }

            // ⭐ ส่ง selected_category_id ไปยัง View
            $data['selected_category_id'] = $selected_category_id;

            // ดึงข้อมูลเดิม
            $data['qActivity'] = $this->activity_model->activity_frontend();
            $data['qHotnews'] = $this->HotNews_model->hotnews_frontend();
            $data['qWeather'] = $this->Weather_report_model->weather_reports_frontend();
            $data['events'] = $this->calender_model->get_events();
            $data['qBanner'] = $this->banner_model->banner_frontend();
            $data['qBackground_personnel'] = $this->background_personnel_model->background_personnel_frontend();

            // ⭐ ใช้ฟังก์ชันใหม่ที่รองรับข้อมูลที่อยู่แยกย่อย
            $login_info = $this->get_user_login_info_with_detailed_address();
            $data['is_logged_in'] = $login_info['is_logged_in'];
            $data['user_info'] = $login_info['user_info'];
            $data['user_type'] = $login_info['user_type'];
            $data['user_address'] = $login_info['user_address'];

            // *** ⭐ โหลดหมวดหมู่เรื่องร้องเรียนจาก Database ***
            $this->load->model('complain_category_model');

            if (!$this->db->table_exists('tbl_complain_category')) {
                log_message('error', 'Table tbl_complain_category does not exist');
                $data['complain_categories'] = [];
                $data['category_error'] = 'ยังไม่ได้ตั้งค่าหมวดหมู่ กรุณาติดต่อผู้ดูแลระบบ';
            } else {
                $categories = $this->complain_category_model->get_active_categories();
                $data['complain_categories'] = $categories;

                if (empty($categories)) {
                    $data['category_error'] = 'ยังไม่มีหมวดหมู่ กรุณาติดต่อผู้ดูแลระบบ';
                    log_message('debug', 'No active categories found');
                }
            }

            // Debug log
            log_message('info', 'Complain page - Type: ' . $type);
            log_message('info', 'Complain page - Selected Category ID: ' . ($selected_category_id ?? 'none'));
            log_message('info', 'Complain page - Login info: ' . json_encode($login_info));
            log_message('info', 'Complain page - Categories loaded: ' . count($data['complain_categories']));

        } catch (Exception $e) {
            log_message('error', 'Error in adding_complain: ' . $e->getMessage());
            $data['complain_categories'] = [];
            $data['category_error'] = 'เกิดข้อผิดพลาดในการโหลดหมวดหมู่';
            $data['page_title'] = 'ร้องเรียน / ร้องทุกข์';
            $data['selected_category_id'] = null;
        }

        $this->load->view('frontend_templat/header', $data);
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/complain', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }


    public function add_complain()
    {
        // *** บังคับ JSON header และหยุด output อื่น ***
        header('Content-Type: application/json; charset=utf-8');
        ob_start(); // เริ่ม output buffering

        try {
            log_message('info', '=== ADD COMPLAIN START (WITH RECAPTCHA) ===');
            log_message('info', 'POST: ' . print_r($_POST, true));
            log_message('info', 'FILES: ' . print_r($_FILES, true));

            // *** ตรวจสอบการมีอยู่ของตาราง ***
            if (!$this->db->table_exists('tbl_complain')) {
                throw new Exception('ตาราง tbl_complain ไม่พบในฐานข้อมูล');
            }

            if (!$this->db->table_exists('tbl_complain_category')) {
                throw new Exception('ตาราง tbl_complain_category ไม่พบในฐานข้อมูล กรุณาติดต่อผู้ดูแลระบบ');
            }

            // *** ตรวจสอบ column complain_category_id ***
            $columns = $this->db->list_fields('tbl_complain');
            if (!in_array('complain_category_id', $columns)) {
                throw new Exception('กรุณาเพิ่ม column complain_category_id ในตาราง tbl_complain');
            }

            // ตรวจสอบข้อมูล POST
            if (!$_POST || empty($_POST)) {
                throw new Exception('ไม่มีข้อมูล POST');
            }

            // *** ⭐ เพิ่ม: ตรวจสอบ reCAPTCHA Token ***
            $recaptcha_token = $this->input->post('g-recaptcha-response');
            $recaptcha_action = $this->input->post('recaptcha_action') ?: 'complain_submit';
            $recaptcha_source = $this->input->post('recaptcha_source') ?: 'complain_form';
            $user_type_detected = $this->input->post('user_type_detected') ?: 'guest';
            $is_ajax = $this->input->post('ajax_request') === '1';
            $dev_mode = $this->input->post('dev_mode') === '1';

            log_message('info', 'reCAPTCHA info: ' . json_encode([
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
                    'form_source' => 'complain_submission',
                    'client_timestamp' => $this->input->post('client_timestamp'),
                    'user_agent_info' => $this->input->post('user_agent_info'),
                    'is_anonymous' => $this->input->post('is_anonymous') === '1'
                ];

                // *** กำหนด user_type ที่ถูกต้องสำหรับ Library ***
                $library_user_type = 'citizen'; // default
                if ($user_type_detected === 'staff' || $user_type_detected === 'admin') {
                    $library_user_type = 'staff';
                }

                // *** เรียกใช้ reCAPTCHA verification ให้ Library กำหนด min_score เอง ***
                $recaptcha_result = $this->recaptcha_lib->verify($recaptcha_token, $library_user_type, null, $recaptcha_options);

                log_message('info', 'reCAPTCHA verification result', [
                    'success' => $recaptcha_result['success'],
                    'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A',
                    'action' => $recaptcha_action,
                    'source' => $recaptcha_source,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]);

                // *** ตรวจสอบผลลัพธ์ reCAPTCHA ***
                if (!$recaptcha_result['success']) {
                    log_message('debug', 'reCAPTCHA verification failed', [
                        'message' => $recaptcha_result['message'],
                        'user_type_detected' => $user_type_detected,
                        'library_user_type' => $library_user_type,
                        'action' => $recaptcha_action,
                        'source' => $recaptcha_source,
                        'score' => isset($recaptcha_result['data']['score']) ? $recaptcha_result['data']['score'] : 'N/A'
                    ]);

                    ob_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'การยืนยันตัวตนไม่ผ่าน: ' . $recaptcha_result['message'],
                        'error_type' => 'recaptcha_failed',
                        'recaptcha_data' => $recaptcha_result['data']
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                log_message('info', 'reCAPTCHA verification successful', [
                    'score' => $recaptcha_result['data']['score'],
                    'action' => $recaptcha_action,
                    'user_type_detected' => $user_type_detected,
                    'library_user_type' => $library_user_type
                ]);

            } else if (!$dev_mode) {
                // *** ไม่มี reCAPTCHA token ***
                log_message('debug', 'No reCAPTCHA token provided');

                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการยืนยันตัวตน',
                    'error_type' => 'recaptcha_missing'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // *** ⭐ เพิ่ม: ตรวจสอบคำหยาบและ URL (ก่อนการ validation) ***
            $complain_topic = trim($this->input->post('complain_topic'));
            $complain_detail = trim($this->input->post('complain_detail'));
            $combined_text = $complain_topic . ' ' . $complain_detail;

            // ตรวจสอบคำหยาบ
            if (method_exists($this, 'check_vulgar_word')) {
                $vulgar_result = $this->check_vulgar_word($combined_text);
                if ($vulgar_result['found']) {
                    log_message('debug', 'Vulgar words detected in complain', [
                        'vulgar_words' => $vulgar_result['words'],
                        'topic' => $complain_topic
                    ]);

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
                    log_message('debug', 'URLs detected in complain', [
                        'urls' => $url_result['urls'],
                        'topic' => $complain_topic
                    ]);

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

            // *** โค้ดเดิมที่เหลือ (ไม่แก้ไข) ***
            $this->load->library('form_validation');
            $this->load->model('complain_category_model');

            // *** ตรวจสอบโหมดไม่ระบุตัวตน ***
            $is_anonymous = $this->input->post('is_anonymous') == '1' || $this->input->post('anonymous_mode') == 'true';
            log_message('info', 'Anonymous mode: ' . ($is_anonymous ? 'YES' : 'NO'));

            // *** ดึงข้อมูล user ปัจจุบัน ***
            $login_info = $this->get_user_login_info_with_detailed_address();
            $is_logged_in = $login_info['is_logged_in'];
            $user_info = $login_info['user_info'];
            $user_address = $login_info['user_address'];

            log_message('info', 'User login status: ' . ($is_logged_in ? 'YES' : 'NO'));

            // *** ⭐ อัปเดต validation rules ให้ใช้ complain_category_id ***
            $this->form_validation->set_rules('complain_category_id', 'หมวดหมู่เรื่องร้องเรียน', 'trim|required|numeric|greater_than[0]');
            $this->form_validation->set_rules('complain_topic', 'หัวข้อเรื่องร้องเรียน', 'trim|required|min_length[4]');
            $this->form_validation->set_rules('complain_detail', 'รายละเอียด', 'trim|required|min_length[4]');

            // *** ⭐ ตรวจสอบว่า category_id ที่ส่งมาเป็นของหมวดหมู่ที่ active หรือไม่ ***
            $category_id = $this->input->post('complain_category_id');
            if ($category_id) {
                $category = $this->complain_category_model->get_category_by_id($category_id);
                if (!$category || $category->cat_status != 1) {
                    ob_clean();
                    echo json_encode([
                        'success' => false,
                        'message' => 'หมวดหมู่ที่เลือกไม่ถูกต้องหรือไม่สามารถใช้งานได้',
                        'errors' => 'หมวดหมู่ไม่ถูกต้อง'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            // *** เพิ่ม validation rules เฉพาะเมื่อไม่ได้ login หรือเป็นโหมด anonymous ***
            if (!$is_logged_in || $is_anonymous) {
                $this->form_validation->set_rules('complain_by', 'ชื่อผู้ร้องเรียน', 'trim|required|min_length[4]');
                $this->form_validation->set_rules('complain_phone', 'เบอร์โทรศัพท์', 'trim|required|min_length[9]|max_length[10]');

                if (!$is_logged_in) {
                    // Guest user ต้องกรอกที่อยู่
                    $this->form_validation->set_rules('complain_address', 'ที่อยู่', 'trim|required|min_length[5]');
                }
                log_message('info', 'Applied validation rules for guest/anonymous user');
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

            // *** ส่งข้อมูลไปยัง Model พร้อมข้อมูล user ***
            $this->load->model('complain_model');

            // ส่งข้อมูล user ไป Model ผ่าน session ชั่วคราว
            if ($is_logged_in && $user_info) {
                $this->session->set_tempdata('temp_user_info', $user_info, 300);
                $this->session->set_tempdata('temp_user_address', $user_address, 300);
                $this->session->set_tempdata('temp_user_type', $login_info['user_type'], 300);
                $this->session->set_tempdata('temp_is_logged_in', true, 300);
                log_message('info', 'Set temp user data for model');
            } else {
                $this->session->set_tempdata('temp_is_logged_in', false, 300);
                log_message('info', 'Set temp guest data for model');
            }

            // *** เรียกใช้ Model และจัดการ Error ***
            try {
                $complain_id = $this->complain_model->add_complain();
            } catch (Exception $e) {
                log_message('error', 'Database error in add_complain: ' . $e->getMessage());

                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage(),
                    'errors' => 'ข้อผิดพลาดฐานข้อมูล'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ลบข้อมูลชั่วคราว
            $this->session->unset_tempdata('temp_user_info');
            $this->session->unset_tempdata('temp_user_address');
            $this->session->unset_tempdata('temp_user_type');
            $this->session->unset_tempdata('temp_is_logged_in');

            log_message('info', 'Model returned complain_id: ' . ($complain_id ? $complain_id : 'false'));

            if ($complain_id) {
                log_message('info', "✅ Complain submitted successfully with reCAPTCHA. ID: {$complain_id}");

                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => $is_anonymous ?
                        'ส่งเรื่องร้องเรียนแบบไม่ระบุตัวตนเรียบร้อยแล้ว' :
                        'ส่งเรื่องร้องเรียนเรียบร้อยแล้ว',
                    'complain_id' => $complain_id,
                    'is_anonymous' => $is_anonymous,
                    'recaptcha_verified' => !$dev_mode
                ], JSON_UNESCAPED_UNICODE);

            } else {
                log_message('error', "❌ Failed to save complain");

                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            log_message('error', "❌ Exception in add_complain: " . $e->getMessage());

            ob_clean();
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit; // *** สำคัญที่สุด: หยุดการทำงานทันที ***
    }

    /**
     * *** ปรับปรุง: หน้าติดตามสถานะเรื่องร้องเรียน (เพิ่มการแจ้งเตือน + reCAPTCHA) ***
     */
    public function follow_complain()
    {
        // *** เพิ่ม: Log debug สำหรับ reCAPTCHA ***
        log_message('info', '=== FOLLOW COMPLAIN START ===');
        log_message('info', 'POST data: ' . print_r($_POST, true));
        log_message('info', 'User Agent: ' . $this->input->server('HTTP_USER_AGENT'));

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
            log_message('info', 'Starting reCAPTCHA verification for follow_complain');

            try {
                // *** ใช้ reCAPTCHA Library ที่มีอยู่ ***
                $recaptcha_options = [
                    'action' => $recaptcha_action ?: 'follow_complain_search',
                    'source' => $recaptcha_source ?: 'follow_complain_form',
                    'user_type_detected' => $user_type_detected ?: 'guest',
                    'form_source' => 'follow_complain_search',
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

                        // *** ถ้าเป็น AJAX request ให้ return JSON ***
                        if ($is_ajax) {
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode([
                                'success' => false,
                                'message' => 'การยืนยันตัวตนไม่ผ่าน: ' . $recaptcha_result['message'],
                                'error_type' => 'recaptcha_failed',
                                'recaptcha_data' => $recaptcha_result['data']
                            ], JSON_UNESCAPED_UNICODE);
                            exit;
                        }

                        // *** ถ้าไม่ใช่ AJAX ให้ redirect กลับไปหน้าเดิม ***
                        log_message('info', 'reCAPTCHA failed, redirecting back to form');
                        redirect('Pages/follow_complain');
                        return;
                    }

                    log_message('info', 'reCAPTCHA verification successful for follow_complain');
                } else {
                    log_message('error', 'reCAPTCHA library not loaded');
                }

            } catch (Exception $e) {
                log_message('error', 'reCAPTCHA verification error: ' . $e->getMessage());

                // *** ถ้าเป็น AJAX request ให้ return JSON ***
                if ($is_ajax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการตรวจสอบความปลอดภัย',
                        'error_type' => 'recaptcha_error'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
        } else if (!$dev_mode) {
            log_message('info', 'No reCAPTCHA token provided for follow_complain');
        } else {
            log_message('info', 'Development mode - skipping reCAPTCHA verification');
        }

        // *** โค้ดเดิม: ประกาศตัวแปรข้อมูลพื้นฐาน ***
        $data = array(
            'qActivity' => $this->activity_model->activity_frontend(),
            'qHotnews' => $this->HotNews_model->hotnews_frontend(),
            'qWeather' => $this->Weather_report_model->weather_reports_frontend(),
            'events' => $this->calender_model->get_events(),
            'qBanner' => $this->banner_model->banner_frontend(),
            'qBackground_personnel' => $this->background_personnel_model->background_personnel_frontend()
        );

        // รับค่าจากฟอร์ม
        $complain_id = $this->input->post('complain_id');

        log_message('info', 'Search complain_id: ' . ($complain_id ?: 'empty'));

        // เพิ่มตัวแปรสำหรับการค้นหา
        $data['search_attempted'] = !empty($complain_id);

        // Query เพื่อดึงข้อมูลจาก tbl_complain
        if (!empty($complain_id)) {
            log_message('info', 'Querying database for complain_id: ' . $complain_id);

            $data['complain_data'] = $this->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row_array();

            // ถ้าพบข้อมูลเรื่องร้องเรียน
            if (!empty($data['complain_data'])) {
                log_message('info', 'Found complain data for ID: ' . $complain_id);

                // Query เพื่อดึงข้อมูลจาก tbl_complain_detail  
                $data['complain_details'] = $this->db->get_where('tbl_complain_detail', array('complain_detail_case_id' => $complain_id))->result_array();

                log_message('info', 'Found ' . count($data['complain_details']) . ' complain details');

                // *** สำคัญ: ดึงข้อมูลรูปภาพจาก tbl_complain_img ***
                $complain_images = $this->db->get_where('tbl_complain_img', array('complain_img_ref_id' => $complain_id))->result_array();

                // เพิ่มข้อมูลรูปภาพเข้าไปใน complain_data
                $data['complain_data']['images'] = $complain_images;

                // Log สำหรับ debug
                log_message('info', "Found " . count($complain_images) . " images for complain ID: {$complain_id}");

                // ตรวจสอบการมีอยู่ของไฟล์รูปภาพ (ถ้าต้องการ)
                foreach ($complain_images as $image) {
                    $image_path = FCPATH . 'assets/uploads/complain/' . $image['complain_img_img'];
                    if (file_exists($image_path)) {
                        log_message('info', "Image exists: " . $image['complain_img_img']);
                    } else {
                        log_message('error', "Image not found: " . $image['complain_img_img']);
                    }
                }
            } else {
                // ไม่พบข้อมูลเรื่องร้องเรียน
                log_message('info', 'No complain data found for ID: ' . $complain_id);
                $data['complain_data'] = null;
                $data['complain_details'] = array();
            }
        } else {
            // ไม่มีการค้นหา
            log_message('info', 'No complain_id provided - showing empty form');
            $data['complain_data'] = null;
            $data['complain_details'] = array();
        }

        // *** เดิม: ทำเครื่องหมายการแจ้งเตือนที่เกี่ยวข้องว่าอ่านแล้ว ***
        if ($complain_id && isset($this->Notification_lib)) {
            try {
                // หา notifications ที่เกี่ยวข้องกับ complain_id นี้
                $related_notifications = $this->db
                    ->where('reference_id', $complain_id)
                    ->where('reference_table', 'tbl_complain')
                    ->where('is_archived', 0)
                    ->get('tbl_notifications')
                    ->result();

                foreach ($related_notifications as $notification) {
                    $this->Notification_lib->mark_as_read($notification->notification_id);
                }

                if (!empty($related_notifications)) {
                    log_message('info', "Marked " . count($related_notifications) . " notifications as read for complain ID: {$complain_id}");
                }

            } catch (Exception $e) {
                log_message('error', "Error marking notifications as read: " . $e->getMessage());
            }
        }

        // *** เพิ่ม: Log สำหรับ user login status ***
        $login_info = $this->get_user_login_info_with_detailed_address();
        $data['is_logged_in'] = $login_info['is_logged_in'];
        $data['user_info'] = $login_info['user_info'];

        log_message('info', 'User login status: ' . ($data['is_logged_in'] ? 'logged_in' : 'guest'));

        log_message('info', '=== FOLLOW COMPLAIN END ===');

        // โหลด View
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');
        $this->load->view('frontend/follow_complain', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }





    private function get_category_name($category_id)
    {
        // ตรวจสอบว่ามี category_id หรือไม่
        if (empty($category_id)) {
            return 'อื่นๆ';
        }

        try {
            // โหลด model ถ้ายังไม่ได้โหลด
            if (!isset($this->complain_category_model)) {
                $this->load->model('complain_category_model');
            }

            // ดึงข้อมูลหมวดหมู่จากฐานข้อมูล
            $this->db->select('cat_name');
            $this->db->from('tbl_complain_category');
            $this->db->where('cat_id', $category_id);
            $this->db->where('cat_status', 1); // เฉพาะที่เปิดใช้งาน
            $category = $this->db->get()->row();

            if ($category && !empty($category->cat_name)) {
                log_message('info', "Found category name: {$category->cat_name} for ID: {$category_id}");
                return $category->cat_name;
            } else {
                log_message('debug', "Category not found for ID: {$category_id}, using default");
                return 'อื่นๆ';
            }

        } catch (Exception $e) {
            log_message('error', 'Error in get_category_name: ' . $e->getMessage());
            return 'อื่นๆ';
        }
    }




    public function get_user_detailed_address()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            $login_info = $this->get_user_login_info_with_detailed_address();

            // ส่งข้อมูลที่อยู่ในรูปแบบที่ JavaScript สามารถใช้งานได้ง่าย
            $address_for_js = null;
            if ($login_info['user_address']) {
                $address_for_js = [
                    'additional_address' => $login_info['user_address']['additional_address'] ?? '',
                    'district' => $login_info['user_address']['district'] ?? '',
                    'district_code' => $login_info['user_address']['district_code'] ?? '',
                    'amphoe' => $login_info['user_address']['amphoe'] ?? '',
                    'amphoe_code' => $login_info['user_address']['amphoe_code'] ?? '',
                    'province' => $login_info['user_address']['province'] ?? '',
                    'province_code' => $login_info['user_address']['province_code'] ?? '',
                    'zipcode' => $login_info['user_address']['zipcode'] ?? '',
                    'phone' => $login_info['user_address']['phone'] ?? '',
                    'full_address' => $login_info['user_address']['full_address'] ?? '',
                    'source' => $login_info['user_address']['source'] ?? 'none'
                ];
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'is_logged_in' => $login_info['is_logged_in'],
                    'user_type' => $login_info['user_type'],
                    'address_info' => $address_for_js,
                    'debug' => [
                        'has_address' => !empty($login_info['user_address']),
                        'address_source' => $login_info['user_address']['source'] ?? 'none'
                    ]
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', 'Error in get_user_detailed_address: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลที่อยู่',
                    'error' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }




    private function get_user_login_info()
    {
        $login_info = [
            'is_logged_in' => false,
            'user_info' => null,
            'user_type' => 'guest'
        ];

        try {
            // ตรวจสอบ Public User (tbl_member_public)
            $mp_id = $this->session->userdata('mp_id');
            $mp_email = $this->session->userdata('mp_email');

            if ($mp_id) {
                log_message('info', '🔍 Found public user session: ' . $mp_id);

                // ดึงข้อมูล public user จากฐานข้อมูล
                $this->db->select('id, mp_id, mp_email, mp_prefix, mp_fname, mp_lname, mp_phone');
                $this->db->from('tbl_member_public');
                $this->db->where('mp_id', $mp_id);
                $public_user = $this->db->get()->row_array();

                if ($public_user) {
                    $login_info = [
                        'is_logged_in' => true,
                        'user_info' => [
                            'id' => $public_user['id'],
                            'mp_id' => $public_user['mp_id'],
                            'email' => $public_user['mp_email'],
                            'name' => trim($public_user['mp_prefix'] . ' ' . $public_user['mp_fname'] . ' ' . $public_user['mp_lname']),
                            'prefix' => $public_user['mp_prefix'],
                            'fname' => $public_user['mp_fname'],
                            'lname' => $public_user['mp_lname'],
                            'phone' => $public_user['mp_phone']
                        ],
                        'user_type' => 'public'
                    ];

                    log_message('info', '✅ Public user info loaded: ' . $public_user['mp_email']);
                } else {
                    log_message('debug', '⚠️ Public user not found in database: ' . $mp_id);
                }
            }

            // ตรวจสอบ Staff User (tbl_member) หากไม่ใช่ public
            if (!$login_info['is_logged_in']) {
                $m_id = $this->session->userdata('m_id');
                $m_email = $this->session->userdata('m_email');

                if ($m_id) {
                    log_message('info', '🔍 Found staff user session: ' . $m_id);

                    // ดึงข้อมูล staff user จากฐานข้อมูล
                    $this->db->select('m_id, m_email, m_fname, m_lname, m_phone, m_system');
                    $this->db->from('tbl_member');
                    $this->db->where('m_id', $m_id);
                    $staff_user = $this->db->get()->row_array();

                    if ($staff_user) {
                        $login_info = [
                            'is_logged_in' => true,
                            'user_info' => [
                                'm_id' => $staff_user['m_id'],
                                'email' => $staff_user['m_email'],
                                'name' => trim($staff_user['m_fname'] . ' ' . $staff_user['m_lname']),
                                'fname' => $staff_user['m_fname'],
                                'lname' => $staff_user['m_lname'],
                                'phone' => $staff_user['m_phone'],
                                'm_system' => $staff_user['m_system']
                            ],
                            'user_type' => 'staff'
                        ];

                        log_message('info', '✅ Staff user info loaded: ' . $staff_user['m_email']);
                    } else {
                        log_message('debug', '⚠️ Staff user not found in database: ' . $m_id);
                    }
                }
            }

        } catch (Exception $e) {
            log_message('error', '❌ Error in get_user_login_info: ' . $e->getMessage());
        }

        return $login_info;
    }

    /**
     * *** เพิ่มใหม่: ดึงข้อมูล login พร้อมที่อยู่ ***
     */
    private function get_user_login_info_with_address()
    {
        // เริ่มต้นด้วยข้อมูล login พื้นฐาน
        $login_info = $this->get_user_login_info();

        // เพิ่มข้อมูลที่อยู่
        $login_info['user_address'] = null;

        if ($login_info['is_logged_in'] && $login_info['user_info']) {
            try {
                if ($login_info['user_type'] === 'public') {
                    // ดึงข้อมูลจาก tbl_member_public
                    $user_id = $login_info['user_info']['id'] ?? $login_info['user_info']['mp_id'] ?? null;

                    if ($user_id) {
                        $this->db->select('mp_address, mp_phone');
                        $this->db->from('tbl_member_public');
                        $this->db->where('id', $user_id);
                        $address_data = $this->db->get()->row_array();

                        if ($address_data && !empty($address_data['mp_address'])) {
                            $login_info['user_address'] = [
                                'full_address' => $address_data['mp_address'],
                                'phone' => $address_data['mp_phone'],
                                'parsed' => $this->parse_address($address_data['mp_address'])
                            ];

                            log_message('info', '✅ Found address for public user: ' . $user_id);
                        }
                    }

                } elseif ($login_info['user_type'] === 'staff') {
                    // ดึงข้อมูลจาก tbl_member (staff)
                    $user_id = $login_info['user_info']['m_id'] ?? null;

                    if ($user_id) {
                        // สมมติว่ามีฟิลด์ m_address ใน tbl_member (ถ้าไม่มีให้ข้าม)
                        $this->db->select('m_phone');
                        $this->db->from('tbl_member');
                        $this->db->where('m_id', $user_id);
                        $address_data = $this->db->get()->row_array();


                        if ($address_data) {
                            $login_info['user_address'] = [
                                // *** แยกเก็บข้อมูลที่อยู่ ***
                                'additional_address' => $address_data['mp_address'] ?: '', // เฉพาะที่อยู่เพิ่มเติม
                                'district' => $address_data['mp_district'] ?: '',
                                'amphoe' => $address_data['mp_amphoe'] ?: '',
                                'province' => $address_data['mp_province'] ?: '',
                                'zipcode' => $address_data['mp_zipcode'] ?: '',
                                'phone' => $address_data['mp_phone'] ?: '',
                                'source' => 'detailed_columns'
                            ];

                            log_message('info', '✅ Found detailed address for public user: ' . $user_id);
                        }

                    }
                }

            } catch (Exception $e) {
                log_message('error', '❌ Error fetching user address: ' . $e->getMessage());
            }
        }

        return $login_info;
    }



    public function get_user_address()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            $login_info = $this->get_user_login_info_with_address();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'address_info' => $login_info['user_address'],
                    'debug' => [
                        'is_logged_in' => $login_info['is_logged_in'],
                        'user_type' => $login_info['user_type'],
                        'has_address' => !empty($login_info['user_address'])
                    ]
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', 'Error in get_user_address: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลที่อยู่',
                    'error' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
        }
    }





    public function get_category_by_id($cat_id)
    {
        $this->db->where('cat_id', $cat_id);
        $query = $this->db->get('tbl_complain_category');

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }


    /**
     * *** เพิ่มใหม่: API ดูสถิติการแจ้งตามประเภทผู้ใช้ ***
     */
    public function get_complain_statistics()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์ (เฉพาะ staff เท่านั้น)
            if (!$this->session->userdata('m_id')) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Access denied'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ดึงสถิติ
            $user_type_stats = $this->complain_model->get_complain_stats_by_user_type();
            $anonymous_location_stats = $this->complain_model->get_anonymous_complain_stats_by_location();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => [
                        'user_type_stats' => $user_type_stats,
                        'anonymous_location_stats' => $anonymous_location_stats
                    ]
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', 'Error in get_complain_statistics: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลสถิติ'
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * *** เพิ่มใหม่: API ดูรายการเรื่องร้องเรียนแบบไม่ระบุตัวตน ***
     */
    public function get_anonymous_complains()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            // ตรวจสอบสิทธิ์ (เฉพาะ staff เท่านั้น)
            if (!$this->session->userdata('m_id')) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Access denied'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            $limit = $this->input->get('limit') ? (int) $this->input->get('limit') : 10;
            $anonymous_complains = $this->complain_model->get_anonymous_complains($limit);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => $anonymous_complains,
                    'count' => count($anonymous_complains)
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', 'Error in get_anonymous_complains: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * *** เพิ่มใหม่: API สำหรับดึงข้อมูล user ปัจจุบัน (สำหรับ AJAX) ***
     */
    public function get_current_user_info()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            $login_info = $this->get_user_login_info();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'login_info' => $login_info
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



    /**
     * *** เพิ่มใหม่: API สำหรับตรวจสอบสถานะเรื่องร้องเรียน (AJAX) ***
     */
    public function check_complain_status()
    {
        try {
            if (!$this->input->is_ajax_request()) {
                show_404();
                return;
            }

            $complain_id = $this->input->post('complain_id');

            if (!$complain_id) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'กรุณาระบุหมายเลขร้องเรียน'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ดึงข้อมูลเรื่องร้องเรียน
            $complain_data = $this->db->get_where('tbl_complain', array('complain_id' => $complain_id))->row_array();

            if (!$complain_data) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'ไม่พบหมายเลขร้องเรียนที่ระบุ'
                    ], JSON_UNESCAPED_UNICODE));
                return;
            }

            // ดึงประวัติการดำเนินงาน
            $complain_details = $this->db
                ->where('complain_detail_case_id', $complain_id)
                ->order_by('complain_detail_datesave', 'ASC')
                ->get('tbl_complain_detail')
                ->result_array();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'complain_data' => $complain_data,
                    'complain_details' => $complain_details
                ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            log_message('error', 'Error in check_complain_status: ' . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ'
                ], JSON_UNESCAPED_UNICODE));
        }
    }




    /**
     * *** เพิ่มใหม่: ดึงข้อมูล login พร้อมที่อยู่แยกย่อย ***
     */
    private function get_user_login_info_with_detailed_address()
    {
        // เริ่มต้นด้วยข้อมูล login พื้นฐาน
        $login_info = $this->get_user_login_info();

        // เพิ่มข้อมูลที่อยู่
        $login_info['user_address'] = null;

        if ($login_info['is_logged_in'] && $login_info['user_info']) {
            try {
                if ($login_info['user_type'] === 'public') {
                    // ดึงข้อมูลจาก tbl_member_public
                    $user_id = $login_info['user_info']['id'] ?? $login_info['user_info']['mp_id'] ?? null;

                    if ($user_id) {
                        // *** อัปเดต: ดึงข้อมูลที่อยู่แยกย่อยด้วย ***
                        $this->db->select('mp_address, mp_phone, mp_district, mp_amphoe, mp_province, mp_zipcode');
                        $this->db->from('tbl_member_public');
                        $this->db->where('id', $user_id);
                        $address_data = $this->db->get()->row_array();

                        if ($address_data) {
                            $login_info['user_address'] = [
                                // *** แยกเก็บข้อมูลที่อยู่ ***
                                'additional_address' => $address_data['mp_address'] ?: '',
                                'district' => $address_data['mp_district'] ?: '',
                                'amphoe' => $address_data['mp_amphoe'] ?: '',
                                'province' => $address_data['mp_province'] ?: '',
                                'zipcode' => $address_data['mp_zipcode'] ?: '',
                                'phone' => $address_data['mp_phone'] ?: '',
                                'source' => 'detailed_columns'
                            ];

                            // สร้าง full_address รวม
                            $full_address_parts = [];
                            if ($address_data['mp_address'])
                                $full_address_parts[] = $address_data['mp_address'];
                            if ($address_data['mp_district'])
                                $full_address_parts[] = 'ตำบล' . $address_data['mp_district'];
                            if ($address_data['mp_amphoe'])
                                $full_address_parts[] = 'อำเภอ' . $address_data['mp_amphoe'];
                            if ($address_data['mp_province'])
                                $full_address_parts[] = 'จังหวัด' . $address_data['mp_province'];
                            if ($address_data['mp_zipcode'])
                                $full_address_parts[] = $address_data['mp_zipcode'];

                            $login_info['user_address']['full_address'] = implode(' ', $full_address_parts);

                            log_message('info', '✅ Found detailed address for public user: ' . $user_id);
                        }
                    }

                } elseif ($login_info['user_type'] === 'staff') {
                    // ดึงข้อมูลจาก tbl_member (staff)
                    $user_id = $login_info['user_info']['m_id'] ?? null;

                    if ($user_id) {
                        $this->db->select('m_phone');
                        $this->db->from('tbl_member');
                        $this->db->where('m_id', $user_id);
                        $address_data = $this->db->get()->row_array();

                        if ($address_data) {
                            $login_info['user_address'] = [
                                'additional_address' => 'ข้อมูลจากบัญชีเจ้าหน้าที่',
                                'district' => '',
                                'amphoe' => '',
                                'province' => '',
                                'zipcode' => '',
                                'phone' => $address_data['m_phone'] ?: '',
                                'full_address' => 'ข้อมูลจากบัญชีเจ้าหน้าที่',
                                'source' => 'staff_account'
                            ];

                            log_message('info', '✅ Found staff address for user: ' . $user_id);
                        }
                    }
                }

            } catch (Exception $e) {
                log_message('error', '❌ Error fetching user address: ' . $e->getMessage());
            }
        }

        return $login_info;
    }





    private function build_full_address_from_parts($additional, $district, $amphoe, $province, $zipcode)
    {
        $parts = array();

        if (!empty($additional))
            $parts[] = $additional;
        if (!empty($district))
            $parts[] = 'ตำบล' . $district;
        if (!empty($amphoe))
            $parts[] = 'อำเภอ' . $amphoe;
        if (!empty($province))
            $parts[] = 'จังหวัด' . $province;
        if (!empty($zipcode))
            $parts[] = $zipcode;

        return implode(' ', $parts);
    }

















    public function adding_corruption()
    {


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/corruption');
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function add_corruption()
    {
        $data['qActivity'] = $this->activity_model->activity_frontend();
        $data['qHotnews'] = $this->HotNews_model->hotnews_frontend();
        $data['qWeather'] = $this->Weather_report_model->weather_reports_frontend();
        $data['events'] = $this->calender_model->get_events();
        $data['qBanner'] = $this->banner_model->banner_frontend();
        $data['qBackground_personnel'] = $this->background_personnel_model->background_personnel_frontend();

        // โหลด form_validation library ก่อนใช้งาน
        $this->load->library('form_validation');

        $this->form_validation->set_rules(
            'corruption_topic',
            'เรื่องเหตุการทุจริต',
            'trim|required|min_length[4]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 4 ตัว')
        );
        $this->form_validation->set_rules(
            'corruption_by',
            'ชื่อ-นามสกุล',
            'trim|required|min_length[4]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 4 ตัว')
        );
        $this->form_validation->set_rules(
            'corruption_phone',
            'เบอร์โทรศัพท์',
            'trim|required|min_length[9]|max_length[10]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 9 ตัว', 'max_length' => 'กรุณากรอกข้อมูลไม่เกิน 10 ตัว')
        );
        $this->form_validation->set_rules(
            'corruption_address',
            'ที่อยู่',
            'trim|required|min_length[4]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 4 ตัว')
        );
        $this->form_validation->set_rules(
            'corruption_detail',
            'รายละเอียด',
            'trim|required|min_length[4]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 4 ตัว')
        );

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/corruption');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            // ลบบรรทัดนี้ออกเพราะ form_validation โหลดแล้ว: $this->load->library('form_validation');
        } else {
            $corruption_id = $this->corruption_model->add_corruption();
            // ตั้งค่า flash data สำหรับ corruption_id
            $this->session->set_flashdata('corruption_id', $corruption_id);

            redirect('Pages/adding_corruption');
        }
    }









    public function file_check($str)
    {
        if (empty($_FILES['esv_ods_file']['name'])) {
            $this->form_validation->set_message('file_check', 'กรุณาแนบเอกสาร.');
            return FALSE;
        }
        return TRUE;
    }

    public function questions()
    {


        $data['query'] = $this->questions_model->list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/questions', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function site_map()
    {

        $data['query'] = $this->site_map_model->site_map_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/site_map', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function site_map_detail($site_map_id)
    {
        $this->site_map_model->increment_view($site_map_id);

        $data['rsData'] = $this->site_map_model->read($site_map_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->site_map_model->read_pdf($site_map_id);
        $data['rsImg'] = $this->site_map_model->read_img($site_map_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/site_map_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_site_map($site_map_file_id)
    {
        $this->site_map_model->increment_download_site_map($site_map_file_id);
    }

    public function menu_eservice()
    {


        // echo '<pre>';
        // print_r($data['rsData']);
        // echo '</pre>';
        // exit();
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/menu_eservice');
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function contact()
    {


        // echo '<pre>';
        // print_r($data['rsData']);
        // echo '</pre>';
        // exit();
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/contact');
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function all_web()
    {


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/all_web');
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function laws_topic()
    {


        $data['query'] = $this->laws_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_topic', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function laws_detail($laws_topic_id)
    {



        $data['query'] = $this->laws_model->list_all_laws($laws_topic_id);
        $data['query_topic'] = $this->laws_model->read($laws_topic_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }


    public function laws_all()
    {



        $data['query'] = $this->laws_la_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_all', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function laws_ral()
    {
        $data['query'] = $this->laws_ral_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_ral', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function laws_rl_folder()
    {
        $data['query'] = $this->laws_rl_folder_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_rl_folder', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function laws_rl_file()
    {
        $data['query'] = $this->laws_rl_file_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_rl_file', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function laws_rm()
    {
        $data['query'] = $this->laws_rm_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_rm', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function laws_act()
    {
        $data['query'] = $this->laws_act_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_act', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function laws_ec()
    {
        $data['query'] = $this->laws_ec_model->list_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_ec', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function laws_la($laws_la_id)
    {
        $data['rsData'] = $this->laws_la_model->read($laws_la_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/laws_la', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function km()
    {


        $data['query'] = $this->km_model->km_frontend_list();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/km', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function km_detail($km_id)
    {
        $this->km_model->increment_view($km_id);

        $data['rsData'] = $this->km_model->read($km_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');

            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->km_model->read_pdf($km_id);
        $data['rsDoc'] = $this->km_model->read_doc($km_id);
        $data['rsImg'] = $this->km_model->read_img($km_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/km_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function increment_download_km($km_file_id)
    {
        $this->km_model->increment_download_km($km_file_id);
    }

    public function news_dla()
    {


        // เรียกใช้ฟังก์ชันเพื่อโหลดข้อมูล RSS
        $rssData = $this->loadNewsDlaData();

        // ตรวจสอบว่าข้อมูล RSS ใช้งานได้หรือไม่
        if ($rssData !== FALSE) {
            // รวมข้อมูล RSS กับข้อมูลอื่น ๆ
            $data['rssData'] = $rssData;
        } else {
            // ถ้า RSS ใช้งานไม่ได้ ไม่ต้องส่งข้อมูลไปที่หน้า home
            $data['rssData'] = []; // หรือสามารถไม่กำหนดค่านี้เลยตามความเหมาะสม
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/news_dla', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    private function loadNewsDlaData()
    {
        // Load the XML data from the URL
        $xml = @simplexml_load_file("https://www.dla.go.th/servlet/RssServlet");

        // Initialize row color flag
        $row_color = '#FFFFFF'; // Start with white

        // Array to store document data
        $documents = [];

        // Check if XML data is loaded successfully
        if ($xml !== FALSE) {
            // Loop through each DOCUMENT tag
            foreach ($xml->DOCUMENT as $document) {
                // Alternate row color
                $row_color = ($row_color == '#FFFFFF') ? '#73e3f9' : '#FFFFFF';

                // Extract data from XML
                $date = (string) $document->DOCUMENT_DATE;
                $organization = (string) $document->ORG;
                $doc_number = (string) $document->DOCUMENT_NO;
                $topic = (string) $document->DOCUMENT_TOPIC;
                $upload_file1 = (string) $document->UPLOAD_FILE1;

                // Initialize topic with no hyperlink
                $topic_html = $topic;

                // Check if UPLOAD_FILE1 exists for the topic
                if (isset($document->UPLOAD_FILE1)) {
                    // Get UPLOAD_FILE1 link
                    $upload_file1 = (string) $document->UPLOAD_FILE1;
                    // Create hyperlink for the topic
                    $topic_html = '<a href="' . $upload_file1 . '">' . $topic . '</a>';
                }

                // Check if there are additional UPLOAD_FILE and UPLOAD_DESC
                for ($i = 2; $i <= 5; $i++) {
                    $upload_file = (isset($document->{"UPLOAD_FILE$i"})) ? (string) $document->{"UPLOAD_FILE$i"} : '';
                    $upload_desc = (isset($document->{"UPLOAD_DESC$i"})) ? (string) $document->{"UPLOAD_DESC$i"} : '';
                    if (!empty($upload_file)) {
                        $topic_html .= '<br><a href="' . $upload_file . '">' . $upload_desc . '</a>';
                    }
                }

                // Generate data array for the view
                $documents[] = [
                    'date' => $date,
                    'organization' => $organization,
                    'doc_number' => $doc_number,
                    'topic' => $topic_html
                ];
            }
        } else {
            // Handle error: XML data could not be loaded
            $documents = [];
        }

        // Sort documents by date in descending order
        usort($documents, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a['date']);
            $dateB = DateTime::createFromFormat('d/m/Y', $b['date']);
            return $dateB <=> $dateA; // Descending order
        });

        // Return the array of documents
        return $documents;
    }

    public function prov_local_doc()
    {


        $data['query'] = $this->prov_local_doc_model->get_local_docs_all();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/prov_local_doc', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }


    public function kid_aw_ods()
    {


        $data['kid_aw_form'] = $this->kid_aw_form_model->kid_aw_form_frontend();


        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/kid_aw_ods', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function add_kid_aw_ods()
    {

        $data['elderly_aw_form'] = $this->elderly_aw_form_model->elderly_aw_form_frontend();

        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        $this->form_validation->set_rules(
            'kid_aw_ods_by',
            'รายละเอียด',
            'trim|required|min_length[10]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูล')
        );
        $this->form_validation->set_rules(
            'kid_aw_ods_phone',
            'เบอร์โทรศัพท์',
            'trim|required|exact_length[10]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'exact_length' => 'กรุณากรอกเบอร์โทรศัพท์ให้ครบ')
        );
        $this->form_validation->set_rules(
            'kid_aw_ods_number',
            'หมายเลขประจำตัวประชาชน',
            'trim|required|exact_length[13]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'exact_length' => 'กรุณากรอกหมายเลขประจำตัวประชาชนให้ครบ 13 หลัก')
        );
        $this->form_validation->set_rules(
            'kid_aw_ods_address',
            'รายละเอียด',
            'trim|required|min_length[1]',
            array('required' => 'กรุณากรอกข้อมูล %s.', 'min_length' => 'กรุณากรอกข้อมูลขั้นต่ำ 1 ตัว')
        );


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('save_required', 'กรุณากรอกข้อมูลที่มี ให้ครบทุกช่อง');
            $data['kid_aw_form'] = $this->kid_aw_form_model->kid_aw_form_frontend();

            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/kid_aw_ods', $data);
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
        } else {
            $this->kid_aw_ods_model->add_kid_aw_ods();
            redirect('Pages/add_kid_aw_ods');
        }
    }

    public function kid_aw()
    {

        $data['elderly_aw_form'] = $this->elderly_aw_form_model->elderly_aw_form_frontend();

        // Load form validation library
        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules(
            'kid_aw_id_num_eligible',
            'หมายเลขประจำตัวประชาชน',
            'trim|required|exact_length[13]',
            array(
                'required' => 'กรุณากรอก %s.',
                'exact_length' => 'กรุณากรอก %s ให้ครบ 13 หลัก'
            )
        );

        // รับค่าจากฟอร์ม
        $kid_aw_id_num_eligible = $this->input->post('kid_aw_id_num_eligible');
        $kid_aw_period_payment = $this->input->post('kid_aw_period_payment');

        // ตรวจสอบว่าแบบฟอร์มผ่านการตรวจสอบหรือไม่
        if ($this->form_validation->run() == FALSE) {
            // กรณีแบบฟอร์มไม่ผ่านการตรวจสอบ
            $data['kid_aw_data'] = array();
            $data['error_message'] = validation_errors();
            // Set flashdata for JavaScript alert
            // $this->session->set_flashdata('save_id_crad', TRUE);
        } else {
            // Query เพื่อดึงข้อมูลจาก tbl_kid_aw
            $this->db->like('kid_aw_period_payment', $kid_aw_period_payment);
            $this->db->where('kid_aw_id_num_eligible', $kid_aw_id_num_eligible);
            $query = $this->db->get('tbl_kid_aw');
            $kid_aw_data = $query->result_array();

            if (empty($kid_aw_data)) {
                // ไม่มีข้อมูลที่ตรงกันในฐานข้อมูล
                $data['error_message'] = 'ไม่พบข้อมูลสำหรับหมายเลขประจำตัวประชาชนและคำค้นหาที่ใกล้เคียง';
                $kid_aw_data = array(); // กำหนดข้อมูลเป็น array ว่าง
            } else {
                $data['error_message'] = '';
            }

            // ส่งข้อมูลไปยัง View
            $data['kid_aw_data'] = $kid_aw_data;
        }

        // โหลด View
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/kid_aw', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function odata()
    {


        $data['query'] = $this->odata_model->odata_frontend();

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/odata', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function odata_sub($odata_id)
    {


        $data['query'] = $this->odata_model->read($odata_id);
        $data['query_odata_sub'] = $this->odata_model->list_all_odata_sub($odata_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['query']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/odata_sub', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function odata_sub_file($odata_sub_id)
    {


        $data['query'] = $this->odata_model->read_odata_sub($odata_sub_id);
        $data['query_odata_sub_file'] = $this->odata_model->list_all_odata_sub_file($odata_sub_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['query']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/odata_sub_file', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function increment_download_odata_sub_file($odata_sub_file_id)
    {
        $this->odata_model->increment_download_odata_sub_file($odata_sub_file_id);
    }
    public function manual_esv_detail($manual_esv_id)
    {

        $this->manual_esv_model->increment_view($manual_esv_id);

        $data['rsData'] = $this->manual_esv_model->read($manual_esv_id);

        // เพิ่มเงื่อนไขเพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$data['rsData']) {
            $this->load->view('frontend_templat/header');
            $this->load->view('frontend_asset/css');
            $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
            $this->load->view('frontend/empty_detail_pages');
            $this->load->view('frontend_asset/js');
            $this->load->view('frontend_asset/home_calendar');
            $this->load->view('frontend_templat/footer');
            return; // ให้จบการทำงานที่นี่
        }

        $data['rsPdf'] = $this->manual_esv_model->read_pdf($manual_esv_id);
        $data['rsDoc'] = $this->manual_esv_model->read_doc($manual_esv_id);
        $data['rsImg'] = $this->manual_esv_model->read_img($manual_esv_id);

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/manual_esv_detail', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }
    public function procurement_tbl_w0_search()
    {


        $selectedOption = 'procurement_tbl_w0_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_w0($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_w0_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_p0_search()
    {



        $selectedOption = 'procurement_tbl_p0_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_p0($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_p0_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_15_search()
    {



        $selectedOption = 'procurement_tbl_15_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_15($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_15_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_b0_search()
    {


        $selectedOption = 'procurement_tbl_b0_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_b0($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_b0_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_d0_search()
    {



        $selectedOption = 'procurement_tbl_d0_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_d0($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_d0_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_d1_search()
    {



        $selectedOption = 'procurement_tbl_d1_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_d1($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_d1_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_w1_search()
    {


        $selectedOption = 'procurement_tbl_w1_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_w1($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_w1_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_d2_search()
    {



        $selectedOption = 'procurement_tbl_d2_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_d2($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_d2_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }

    public function procurement_tbl_w2_search()
    {


        $selectedOption = 'procurement_tbl_w2_search';
        $this->session->set_userdata('selected_option', $selectedOption);

        $search = $this->input->post('search');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // ถ้ามีคำค้นหาหรือมีวันที่เริ่มต้น-สิ้นสุดที่กรอก
        if (!empty($search) || (!empty($start_date) && !empty($end_date))) {
            $data['query'] = $this->procurement_egp_model->search_tbl_w2($search, $start_date, $end_date);
        } else {
            // ถ้าไม่มีคำค้นหา และไม่มีวันที่เริ่มต้น-สิ้นสุดที่กรอก ดึงข้อมูลทั้งหมด
            $data['query'] = $this->procurement_egp_model->get_tbl_w2_all();
        }

        // ส่งค่าที่เลือกไปยัง View
        $data['selected_option'] = $selectedOption;

        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar_other');  // ส่งข้อมูลไปที่ navbar
        $this->load->view('frontend/procurement_egp', $data);
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_asset/home_calendar');
        $this->load->view('frontend_templat/footer');
    }


    public function check_vulgar_with_details($text)
    {
        return $this->vulgar_check->check_vulgar_with_details($text);
    }

    // 6. สำหรับการ debug - ฟังก์ชันแสดงคำไม่สุภาพทั้งหมดในระบบ
    public function show_vulgar_words()
    {
        // ตรวจสอบสิทธิ์ (เฉพาะ admin)
        if ($this->session->userdata('user_level') !== 'admin') {
            show_404();
            return;
        }

        echo "<h2>รายการคำไม่สุภาพในระบบ</h2>";

        $vulgar_words = $this->db->order_by('vulgar_com', 'ASC')->get('tbl_vulgar')->result_array();

        if (!empty($vulgar_words)) {
            echo "<p>จำนวนคำทั้งหมด: " . count($vulgar_words) . " คำ</p>";
            echo "<ol>";
            foreach ($vulgar_words as $word) {
                echo "<li>" . htmlspecialchars($word['vulgar_com']) . "</li>";
            }
            echo "</ol>";
        } else {
            echo "<p>ไม่มีคำไม่สุภาพในระบบ</p>";
        }

        // ฟอร์มทดสอบ
        echo "<hr><h3>ทดสอบการตรวจสอบคำไม่สุภาพ</h3>";
        echo '<form method="post">';
        echo '<textarea name="test_text" rows="3" cols="50" placeholder="ใส่ข้อความที่ต้องการทดสอบ">' . htmlspecialchars($this->input->post('test_text')) . '</textarea><br><br>';
        echo '<button type="submit">ทดสอบ</button>';
        echo '</form>';

        if ($this->input->post('test_text')) {
            $test_text = $this->input->post('test_text');
            $result = $this->vulgar_check->check_text($test_text);

            echo "<h4>ผลการทดสอบ:</h4>";
            echo "<pre>" . print_r($result, true) . "</pre>";
        }
    }

}
