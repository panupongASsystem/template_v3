<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Policy extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load models
        $this->load->model('System_model');
        $this->load->model('Policy_model');
        $this->load->helper('url');
        
        // ดึงข้อมูล config องค์กร
        $this->data['org'] = $this->System_model->get_config();
    }

    public function index()
    {
        redirect('policy/terms');
    }

    // หน้านโยบายเว็บไซต์
    public function terms()
    {
        $this->data['title'] = 'นโยบายเว็บไซต์และข้อกำหนดการใช้งาน';
        $this->data['page'] = 'terms';
        
        // Log การเข้าดู
        $user_id = $this->session->userdata('user_id');
        $this->Policy_model->log_policy_view('terms', $user_id);
        
        // Load views
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/terms', $this->data);
        $this->load->view('policy/footer', $this->data);
    }

    // หน้าความมั่นคงปลอดภัย
    public function security()
    {
        $this->data['title'] = 'นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์';
        $this->data['page'] = 'security';
        
        // Log การเข้าดู
        $user_id = $this->session->userdata('user_id');
        $this->Policy_model->log_policy_view('security', $user_id);
        
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/security', $this->data);
        $this->load->view('policy/footer', $this->data);
    }

    // หน้า PDPA
    public function pdpa()
    {
        $this->data['title'] = 'นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)';
        $this->data['page'] = 'pdpa';
        
        // ดึงข้อมูลสิทธิและฐานกฎหมาย
        $this->data['pdpa_rights'] = $this->Policy_model->get_pdpa_rights();
        $this->data['legal_basis'] = $this->Policy_model->get_legal_basis();
        $this->data['faqs'] = $this->Policy_model->get_policy_faq('pdpa');
        
        // Log การเข้าดู
        $user_id = $this->session->userdata('user_id');
        $this->Policy_model->log_policy_view('pdpa', $user_id);
        
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/pdpa', $this->data);
        $this->load->view('policy/footer', $this->data);
    }

    // หน้าประกาศความเป็นส่วนตัว
    public function privacy()
    {
        $this->data['title'] = 'ประกาศความเป็นส่วนตัว';
        $this->data['page'] = 'privacy';
        
        // ดึงข้อมูลสิทธิ
        $this->data['pdpa_rights'] = $this->Policy_model->get_pdpa_rights();
        $this->data['faqs'] = $this->Policy_model->get_policy_faq('privacy');
        
        // Log การเข้าดู
        $user_id = $this->session->userdata('user_id');
        $this->Policy_model->log_policy_view('privacy', $user_id);
        
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/privacy', $this->data);
        $this->load->view('policy/footer', $this->data);
    }

    // หน้านโยบายคุกกี้
    public function cookie()
    {
        $this->data['title'] = 'นโยบายการใช้คุกกี้';
        $this->data['page'] = 'cookie';
        
        // ดึงข้อมูล cookie categories
        $this->data['cookie_categories'] = $this->Policy_model->get_cookie_categories();
        $this->data['faqs'] = $this->Policy_model->get_policy_faq('cookie');
        
        // Log การเข้าดู
        $user_id = $this->session->userdata('user_id');
        $this->Policy_model->log_policy_view('cookie', $user_id);
        
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/cookie', $this->data);
        $this->load->view('policy/footer', $this->data);
    }

    // หน้าข้อกำหนดการสมัครสมาชิก
    public function membership()
    {
        $this->data['title'] = 'ข้อกำหนดและเงื่อนไขการสมัครสมาชิก';
        $this->data['page'] = 'membership';
        
        // Log การเข้าดู
        $user_id = $this->session->userdata('user_id');
        $this->Policy_model->log_policy_view('membership', $user_id);
        
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/membership', $this->data);
        $this->load->view('policy/footer', $this->data);
    }

    // บันทึกการยอมรับ Cookie (AJAX)
    public function save_cookie_consent()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'policy_type' => 'cookie',
            'consent_type' => $this->input->post('consent_type'),
            'details' => [
                'necessary' => true, // Always true
                'functional' => $this->input->post('functional') == 'true',
                'analytics' => $this->input->post('analytics') == 'true',
                'marketing' => $this->input->post('marketing') == 'true'
            ]
        ];

        $result = $this->Policy_model->save_policy_consent($data);
        
        echo json_encode(['success' => $result]);
    }

    // บันทึกการยอมรับนโยบายอื่นๆ (AJAX)
    public function save_consent()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'policy_type' => $this->input->post('policy_type'),
            'consent_type' => $this->input->post('consent_type'),
            'details' => []
        ];

        $result = $this->Policy_model->save_policy_consent($data);
        
        echo json_encode(['success' => $result]);
    }

    // ตรวจสอบสถานะการยอมรับ (AJAX)
    public function check_consent()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $policy_type = $this->input->post('policy_type');
        
        if (!$user_id) {
            echo json_encode(['accepted' => false, 'message' => 'Not logged in']);
            return;
        }

        $accepted = $this->Policy_model->check_user_consent($user_id, $policy_type);
        $needs_update = $this->Policy_model->has_policy_updated_since_consent($user_id, $policy_type);
        
        echo json_encode([
            'accepted' => $accepted && !$needs_update,
            'needs_update' => $needs_update
        ]);
    }

    // ดาวน์โหลด PDF
    public function download($type = 'terms')
    {
        $allowed_types = ['terms', 'security', 'pdpa', 'privacy', 'cookie', 'membership'];
        
        if (!in_array($type, $allowed_types)) {
            show_404();
            return;
        }

        // ในที่นี้จะ redirect ไปยังไฟล์ PDF หรือสร้าง PDF แบบ dynamic
        // ตัวอย่าง: redirect ไปยัง PDF ที่เตรียมไว้
        $pdf_file = base_url('assets/pdf/policy_' . $type . '.pdf');
        
        // หรือสร้าง PDF แบบ dynamic (ต้อง load library เพิ่ม)
        // $this->load->library('pdf');
        // $html = $this->load->view('policy/'.$type.'_pdf', $this->data, true);
        // $this->pdf->generate($html, 'policy_'.$type);
        
        redirect($pdf_file);
    }

    // หน้าแสดงนโยบายทั้งหมด
    public function all()
    {
        $this->data['title'] = 'นโยบายและข้อกำหนดทั้งหมด';
        $this->data['page'] = 'all';
        $this->data['policies'] = $this->Policy_model->get_all_policies();
        
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/all', $this->data);
        $this->load->view('policy/footer', $this->data);
    }

    // หน้าสถิติ (สำหรับ Admin)
    public function statistics()
    {
        // ตรวจสอบสิทธิ์ admin
        // if (!$this->session->userdata('is_admin')) {
        //     redirect('policy');
        //     return;
        // }

        $this->data['title'] = 'สถิติการเข้าดูนโยบาย';
        $this->data['page'] = 'statistics';
        
        $this->data['stats_today'] = $this->Policy_model->get_policy_statistics('today');
        $this->data['stats_week'] = $this->Policy_model->get_policy_statistics('week');
        $this->data['stats_month'] = $this->Policy_model->get_policy_statistics('month');
        $this->data['consent_stats'] = $this->Policy_model->get_consent_statistics();
        
        $this->load->view('policy/header', $this->data);
        $this->load->view('policy/statistics', $this->data);
        $this->load->view('policy/footer', $this->data);
    }
}
