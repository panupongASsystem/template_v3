<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_proxy extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        
        // Debug: Log session data
        log_message('debug', 'Session data: ' . print_r($this->session->userdata(), true));
    }

    // เพิ่ม index method
    public function index() {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'Asset proxy is working']));
    }

    public function login() {
        // Debug: Log request
        log_message('debug', 'Asset proxy login called');
        
        if (!$this->session->userdata('m_id')) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
            return;
        }

        $data = [
            'username' => $this->session->userdata('m_username'),
            'password' => $this->session->userdata('password'),
            'origin_domain' => 'tempc2.assystem.co.th'
        ];

        // Debug: Log request data
        log_message('debug', 'Sending data to API: ' . print_r($data, true));

        $ch = curl_init('https://assetssv1.assystem.co.th/api/login');
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-Key: sk_test_sawang123456'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        // Debug: Log response
        log_message('debug', 'API Response: ' . $response);
        log_message('debug', 'HTTP Code: ' . $httpCode);

        $this->output
            ->set_status_header($httpCode)
            ->set_content_type('application/json')
            ->set_output($response);
    }
}