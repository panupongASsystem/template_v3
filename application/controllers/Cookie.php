<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cookie extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        
        // เพิ่ม CORS headers
        header('Access-Control-Allow-Origin: *');  // หรือระบุ domain ที่อนุญาต เช่น https://wareesawat.go.th
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        // สำหรับ preflight request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }
        
        $this->load->model('cookie_model');
        $this->load->library('user_agent');
    }
    public function accept()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // สร้างข้อมูลเหมือนเดิม
        $cookie_types = ['คุกกี้พื้นฐานที่จำเป็น'];
        if (!empty($data['analytics'])) {
            $cookie_types[] = 'คุกกี้ในส่วนวิเคราะห์';
        }
        if (!empty($data['marketing'])) {
            $cookie_types[] = 'คุกกี้ในส่วนการตลาด';
        }

        $cookie_type_value = (count($cookie_types) === 3) ? 'ทั้งหมด' : implode(',', $cookie_types);

		// ดึงชื่อองค์กรจากฐานข้อมูล
        $organization_name = get_config_value('fname');
		
        $consent_data = [
            'sao_name' => $organization_name,
            'session_id' => $data['session_id'],
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $data['device'],
            'status' => 'accepted',
            'category' => $cookie_type_value
        ];

        // ส่งข้อมูลไปยังเว็บ cookie
        $ch = curl_init('https://cookie.assystem.co.th/Cookie/receive_consent');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($consent_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // เพิ่มตัวนี้เพื่อป้องกันปัญหา SSL
        
        $response = curl_exec($ch);
        curl_close($ch);

        // ส่งผลลัพธ์กลับไปยัง client
        $this->output
            ->set_content_type('application/json')
            ->set_output($response);
    }

    // public function accept() {
    //     $json = file_get_contents('php://input');
    //     $data = json_decode($json, true);

    //     // เริ่มต้นด้วยคุกกี้พื้นฐานเสมอ
    //     $cookie_types = ['คุกกี้พื้นฐานที่จำเป็น'];

    //     // เพิ่มคุกกี้ตามที่เลือก
    //     if (!empty($data['analytics'])) {
    //         $cookie_types[] = 'คุกกี้ในส่วนวิเคราะห์';
    //     }
    //     if (!empty($data['marketing'])) {
    //         $cookie_types[] = 'คุกกี้ในส่วนการตลาด';
    //     }

    //     // ถ้ามีทั้ง 3 ประเภทให้เซ็ตเป็น 'all'
    //     $cookie_type_value = (count($cookie_types) === 3) ? 'ทั้งหมด' : implode(',', $cookie_types);

    //     $consent_data = [
    //         'category' => 'องค์การบริหารส่วนตำบลสว่าง',
    //         'session_id' => $data['session_id'],
    //         'ip_address' => $this->input->ip_address(),
    //         'user_agent' => $data['device'],
    //         'status' => 'accepted',
    //         'category' => $cookie_type_value
    //     ];

    //     $result = $this->cookie_model->save_consent($consent_data);

    //     $this->output
    //         ->set_content_type('application/json')
    //         ->set_output(json_encode([
    //             'success' => $result,
    //             'message' => $result ? 'Consent saved successfully' : 'Failed to save consent'
    //         ]));
    // }
}
