<?php
// ===================================================================
// application/controllers/Canon.php - Canon Controller
// ===================================================================
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'core/Base_Frontend_Controller.php');

class Canon extends Base_Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // === Canon BGPS (Budget Guidelines and Procedures System) ===
    public function bgps()
    {
        $data = $this->get_common_data();
        $data['query'] = $this->canon_bgps_model->canon_bgps_frontend_list();
        $this->load_views('canon_bgps', $data);
    }

    public function bgps_detail($canon_bgps_id)
    {
        $data = $this->get_common_data();
        $this->canon_bgps_model->increment_view($canon_bgps_id);
        $data['rsData'] = $this->canon_bgps_model->read($canon_bgps_id);

        if (!$data['rsData']) {
            $this->show_404_page();
            return;
        }

        $data['rsPdf'] = $this->canon_bgps_model->read_pdf($canon_bgps_id);
        $data['rsDoc'] = $this->canon_bgps_model->read_doc($canon_bgps_id);
        $data['rsImg'] = $this->canon_bgps_model->read_img($canon_bgps_id);

        $this->load_views('canon_bgps_detail', $data);
    }

    public function increment_download_bgps($canon_bgps_file_id)
    {
        $this->canon_bgps_model->increment_download_canon_bgps($canon_bgps_file_id);
    }

    // === Canon CHH (Community Health and Hygiene) ===
    public function chh()
    {
        $data = $this->get_common_data();
        $data['query'] = $this->canon_chh_model->canon_chh_frontend_list();
        $this->load_views('canon_chh', $data);
    }

    public function chh_detail($canon_chh_id)
    {
        $data = $this->get_common_data();
        $this->canon_chh_model->increment_view($canon_chh_id);
        $data['rsData'] = $this->canon_chh_model->read($canon_chh_id);

        if (!$data['rsData']) {
            $this->show_404_page();
            return;
        }

        $data['rsPdf'] = $this->canon_chh_model->read_pdf($canon_chh_id);
        $data['rsDoc'] = $this->canon_chh_model->read_doc($canon_chh_id);
        $data['rsImg'] = $this->canon_chh_model->read_img($canon_chh_id);

        $this->load_views('canon_chh_detail', $data);
    }

    public function increment_download_chh($canon_chh_file_id)
    {
        $this->canon_chh_model->increment_download_canon_chh($canon_chh_file_id);
    }

    // === Canon RITW (Rural Infrastructure and Transportation Works) ===
    public function ritw()
    {
        $data = $this->get_common_data();
        $data['query'] = $this->canon_ritw_model->canon_ritw_frontend_list();
        $this->load_views('canon_ritw', $data);
    }

    public function ritw_detail($canon_ritw_id)
    {
        $data = $this->get_common_data();
        $this->canon_ritw_model->increment_view($canon_ritw_id);
        $data['rsData'] = $this->canon_ritw_model->read($canon_ritw_id);

        if (!$data['rsData']) {
            $this->show_404_page();
            return;
        }

        $data['rsPdf'] = $this->canon_ritw_model->read_pdf($canon_ritw_id);
        $data['rsDoc'] = $this->canon_ritw_model->read_doc($canon_ritw_id);
        $data['rsImg'] = $this->canon_ritw_model->read_img($canon_ritw_id);

        $this->load_views('canon_ritw_detail', $data);
    }

    public function increment_download_ritw($canon_ritw_file_id)
    {
        $this->canon_ritw_model->increment_download_canon_ritw($canon_ritw_file_id);
    }

    // === Canon Market ===
    public function market()
    {
        $data = $this->get_common_data();
        $data['query'] = $this->canon_market_model->canon_market_frontend_list();
        $this->load_views('canon_market', $data);
    }

    public function market_detail($canon_market_id)
    {
        $data = $this->get_common_data();
        $this->canon_market_model->increment_view($canon_market_id);
        $data['rsData'] = $this->canon_market_model->read($canon_market_id);

        if (!$data['rsData']) {
            $this->show_404_page();
            return;
        }

        $data['rsPdf'] = $this->canon_market_model->read_pdf($canon_market_id);
        $data['rsDoc'] = $this->canon_market_model->read_doc($canon_market_id);
        $data['rsImg'] = $this->canon_market_model->read_img($canon_market_id);

        $this->load_views('canon_market_detail', $data);
    }

    public function increment_download_market($canon_market_file_id)
    {
        $this->canon_market_model->increment_download_canon_market($canon_market_file_id);
    }

    // === Canon RMWP (Resource Management and Water Projects) ===
    public function rmwp()
    {
        $data = $this->get_common_data();
        $data['query'] = $this->canon_rmwp_model->canon_rmwp_frontend_list();
        $this->load_views('canon_rmwp', $data);
    }

    public function rmwp_detail($canon_rmwp_id)
    {
        $data = $this->get_common_data();
        $this->canon_rmwp_model->increment_view($canon_rmwp_id);
        $data['rsData'] = $this->canon_rmwp_model->read($canon_rmwp_id);

        if (!$data['rsData']) {
            $this->show_404_page();
            return;
        }

        $data['rsPdf'] = $this->canon_rmwp_model->read_pdf($canon_rmwp_id);
        $data['rsDoc'] = $this->canon_rmwp_model->read_doc($canon_rmwp_id);
        $data['rsImg'] = $this->canon_rmwp_model->read_img($canon_rmwp_id);

        $this->load_views('canon_rmwp_detail', $data);
    }

    public function increment_download_rmwp($canon_rmwp_file_id)
    {
        $this->canon_rmwp_model->increment_download_canon_rmwp($canon_rmwp_file_id);
    }

    // === Canon RCP (Rural Community Projects) ===
    public function rcp()
    {
        $data = $this->get_common_data();
        $data['query'] = $this->canon_rcp_model->canon_rcp_frontend_list();
        $this->load_views('canon_rcp', $data);
    }

    public function rcp_detail($canon_rcp_id)
    {
        $data = $this->get_common_data();
        $this->canon_rcp_model->increment_view($canon_rcp_id);
        $data['rsData'] = $this->canon_rcp_model->read($canon_rcp_id);

        if (!$data['rsData']) {
            $this->show_404_page();
            return;
        }

        $data['rsPdf'] = $this->canon_rcp_model->read_pdf($canon_rcp_id);
        $data['rsDoc'] = $this->canon_rcp_model->read_doc($canon_rcp_id);
        $data['rsImg'] = $this->canon_rcp_model->read_img($canon_rcp_id);

        $this->load_views('canon_rcp_detail', $data);
    }

    public function increment_download_rcp($canon_rcp_file_id)
    {
        $this->canon_rcp_model->increment_download_canon_rcp($canon_rcp_file_id);
    }

    // === Canon RCSP (Rural Community Social Programs) ===
    public function rcsp()
    {
        $data = $this->get_common_data();
        $data['query'] = $this->canon_rcsp_model->canon_rcsp_frontend_list();
        $this->load_views('canon_rcsp', $data);
    }

    public function rcsp_detail($canon_rcsp_id)
    {
        $data = $this->get_common_data();
        $this->canon_rcsp_model->increment_view($canon_rcsp_id);
        $data['rsData'] = $this->canon_rcsp_model->read($canon_rcsp_id);

        if (!$data['rsData']) {
            $this->show_404_page();
            return;
        }

        $data['rsPdf'] = $this->canon_rcsp_model->read_pdf($canon_rcsp_id);
        $data['rsDoc'] = $this->canon_rcsp_model->read_doc($canon_rcsp_id);
        $data['rsImg'] = $this->canon_rcsp_model->read_img($canon_rcsp_id);

        $this->load_views('canon_rcsp_detail', $data);
    }

    public function increment_download_rcsp($canon_rcsp_file_id)
    {
        $this->canon_rcsp_model->increment_download_canon_rcsp($canon_rcsp_file_id);
    }
}