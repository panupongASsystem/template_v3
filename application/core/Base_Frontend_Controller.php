<?php
// ===================================================================
// application/core/Base_Frontend_Controller.php - Base Controller (Updated)
// ===================================================================
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_Frontend_Controller extends CI_Controller 
{
    protected $common_data = [];

    public function __construct()
    {
        parent::__construct();
        $this->set_security_headers();
        $this->load_common_models();
        $this->ensure_notification_lib();
    }

    protected function set_security_headers()
    {
        $headers = [
            'Cache-Control: no-store, no-cache, must-revalidate',
            'Cache-Control: post-check=0, pre-check=0, max-age=0',
            'Pragma: no-cache',
            'X-Frame-Options: DENY',
            'X-Content-Type-Options: nosniff',
            'X-XSS-Protection: 1; mode=block',
            'Referrer-Policy: same-origin',
            'Content-Disposition: inline',
            'Expires: Sat, 26 Jul 1997 05:00:00 GMT'
        ];

        foreach ($headers as $header) {
            $this->output->set_header($header);
        }
    }

    protected function load_common_models()
    {
        $models = [
            // Core_Content_Controller.php
            'activity_model', 'news_model', 'announce_model', 'order_model',
            'procurement_model', 'mui_model', 'guide_work_model', 'loadform_model',
            'pppw_model', 'msg_pres_model', 'history_model', 'otop_model',
            'gci_model', 'vision_model', 'authority_model', 'mission_model',
            'motto_model', 'cmi_model', 'executivepolicy_model', 'travel_model',
            'si_model', 'km_model',
            
            // News_Media_Controller.php
            'HotNews_model', 'Weather_report_model', 'calender_model',
            'banner_model', 'background_personnel_model', 'video_model',
            'newsletter_model',
            
            // Plan.php
            'plan_pdl_model', 'plan_pc3y_model', 'plan_pds3y_model', 'plan_pdpa_model',
            'plan_dpy_model', 'plan_poa_model', 'plan_pcra_model', 'plan_pdm_model',
            'plan_pop_model', 'plan_paca_model', 'plan_psi_model', 'plan_pmda_model',
            'plan_progress_model',
            
            // Canon.php
            'canon_bgps_model', 'canon_chh_model', 'canon_ritw_model', 'canon_market_model',
            'canon_rmwp_model', 'canon_rcp_model', 'canon_rcsp_model',
            
            // PBSV_Controller.php
            'pbsv_cac_model', 'pbsv_cig_model', 'pbsv_cjc_model', 'pbsv_utilities_model',
            'pbsv_sags_model', 'pbsv_ahs_model', 'pbsv_oppr_model', 'pbsv_ems_model',
            'pbsv_gup_model', 'pbsv_e_book_model', 'pbsv_statistics_model',
            
            // Operation_Controller.php
            'operation_reauf_model', 'operation_report_model', 'operation_sap_model',
            'operation_pm_model', 'operation_mr_model', 'operation_policy_hr_model',
            'operation_am_hr_model', 'operation_rdam_hr_model', 'operation_cdm_model',
            'operation_po_model', 'operation_eco_model', 'operation_meeting_model',
            'operation_pgn_model', 'operation_mcc_model', 'operation_aca_model',
            'operation_aditn_model', 'operation_procurement_model', 'operation_aa_model',
            
            // Personnel_Controller.php
            'p_executives_model', 'p_council_model', 'p_unit_leaders_model', 'p_deputy_model',
            'p_treasury_model', 'p_maintenance_model', 'p_public_model', 'p_learder_model',
            'p_welfare_model', 'p_office_model', 'p_plumbing_model', 'p_bads_model',
            'p_other_model', 'p_education_model', 'p_audit_model',
            
            // Special_Operation_Controller.php
            'p_sopopip_model', 'p_sopopaortsr_model', 'p_rpobuy_model', 'p_rpo_model',
            'p_reb_model',
            
            // Laws_Controller.php
            'laws_ral_model', 'laws_rl_folder_model', 'laws_rl_file_model', 'laws_rm_model',
            'laws_act_model', 'laws_ec_model', 'laws_la_model', 'laws_model',
            
            // Finance_Tax_Controller.php
            'finance_model', 'taepts_model', 'arevenuec_model',
            
            // E_service_and_form_Controller.php
            'form_esv_model', 'esv_ods_model', 'manual_esv_model',
            'elderly_aw_form_model', 'elderly_aw_ods_model',
            'kid_aw_form_model', 'kid_aw_ods_model',
            'q_a_model', 'complain_model', 'queue_model', 'corruption_model',
            'suggestions_model', 'questions_model', 'assessment_model',
            
            // Data_and_Information.php
            'odata_model', 'ita_model', 'ita_year_model', 'lpa_model',
            'site_map_model', 'prov_local_doc_model',
            
            // Internal_and_Special.php
            'intra_egp_model', 'procurement_egp_model',
            'announce_oap_model', 'announce_win_model', 'ethics_strategy_model',
            
            // User_Management_Controller.php
            'member_public_model'
        ];
        
        foreach ($models as $model) {
            $this->load->model($model);
        }
    }

    protected function get_common_data()
    {
        return [
            'qActivity' => $this->activity_model->activity_frontend(),
            'qHotnews' => $this->HotNews_model->hotnews_frontend(),
            'qWeather' => $this->Weather_report_model->weather_reports_frontend(),
            'events' => $this->calender_model->get_events(),
            'qBanner' => $this->banner_model->banner_frontend(),
            'qBackground_personnel' => $this->background_personnel_model->background_personnel_frontend()
        ];
    }

    protected function load_views($view_name, $data = [], $with_calendar = true)
    {
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar', $data);
        $this->load->view("frontend/{$view_name}", $data);
        $this->load->view('frontend_asset/js');
        if ($with_calendar) {
            $this->load->view('frontend_asset/home_calendar');
        }
        $this->load->view('frontend_templat/footer');
    }

    protected function show_404_page()
    {
        $data = $this->get_common_data();
        $this->load->view('frontend_templat/header');
        $this->load->view('frontend_asset/css');
        $this->load->view('frontend_templat/navbar', $data);
        $this->load->view('frontend/empty_detail_pages');
        $this->load->view('frontend_asset/js');
        $this->load->view('frontend_templat/footer_other');
    }

    protected function ensure_notification_lib()
    {
        if (!isset($this->Notification_lib)) {
            try {
                $this->load->library('Notification_lib');
                if (isset($this->Notification_lib)) {
                    log_message('debug', 'Notification_lib loaded successfully');
                    return true;
                }
            } catch (Exception $e) {
                log_message('error', 'Failed to load Notification_lib: ' . $e->getMessage());
                try {
                    require_once(APPPATH . 'libraries/Notification_lib.php');
                    $this->Notification_lib = new Notification_lib();
                    log_message('debug', 'Notification_lib loaded manually');
                    return true;
                } catch (Exception $e2) {
                    log_message('error', 'Manual loading also failed: ' . $e2->getMessage());
                    return false;
                }
            }
        }
        return true;
    }

    protected function handle_form_validation_error($redirect_url, $anchor = '')
    {
        $this->session->set_flashdata('validation_error', true);
        $this->session->set_flashdata('error_message', validation_errors());
        $this->session->set_flashdata('form_data', $this->input->post());
        redirect($redirect_url . ($anchor ? '#' . $anchor : ''));
    }

    protected function setup_form_validation()
    {
        $this->load->library(['form_validation', 'vulgar_check']);
    }

    public function check_vulgar($text)
    {
        $check_result = $this->vulgar_check->check_text($text);
        
        if (isset($check_result['data']['has_vulgar_words']) && $check_result['data']['has_vulgar_words']) {
            $vulgar_words = isset($check_result['data']['vulgar_words']) ? $check_result['data']['vulgar_words'] : [];
            
            if (!empty($vulgar_words)) {
                $words_list = implode(', ', $vulgar_words);
                $error_message = "พบคำไม่เหมาะสม: \"{$words_list}\" กรุณาแก้ไขและลองใหม่";
                $this->form_validation->set_message('check_vulgar', $error_message);
            } else {
                $this->form_validation->set_message('check_vulgar', 'พบคำไม่เหมาะสม กรุณาแก้ไขและลองใหม่');
            }
            
            return false;
        }
        
        return true;
    }

    public function check_no_urls($text)
    {
        if (empty($text)) {
            return true;
        }

        $field_name = '';
        if (isset($this->form_validation->_field_data)) {
            foreach ($this->form_validation->_field_data as $field => $data) {
                if (isset($data['postdata']) && $data['postdata'] === $text) {
                    $field_name = $field;
                    break;
                }
            }
        }

        if ($field_name === 'q_a_email' || $field_name === 'q_a_reply_email') {
            return true;
        }

        if (filter_var($text, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        $words = preg_split('/\s+/', $text);
        $found_urls = [];

        $url_patterns = [
            '/https?:\/\/[^\s]+/i' => 'http/https links',
            '/\bwww\.[^\s]+/i' => 'www links',
            '/\b[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.(com|net|org|info|io|co|th|biz|app|dev|me)\b/i' => 'domain links'
        ];

        foreach ($words as $word) {
            if (filter_var($word, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            foreach ($url_patterns as $pattern => $type) {
                if (preg_match($pattern, $word)) {
                    $found_urls[] = $word;
                    break;
                }
            }
        }

        if (!empty($found_urls)) {
            $urls_list = implode(', ', array_unique($found_urls));
            $error_message = "พบลิงก์หรือ URL ที่ไม่อนุญาต: \"{$urls_list}\" กรุณาลบออกและลองใหม่";
            $this->form_validation->set_message('check_no_urls', $error_message);
            return false;
        }

        return true;
    }
}