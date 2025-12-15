<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_line extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_log_model');
    }
    
    public function index() {
        echo '<h1>Line Notification Test Page</h1>';
        echo '<p>This page tests the Line notification functionality.</p>';
        
        $message = "🔔 ทดสอบการส่งข้อความจาก Test_line Controller\n";
        $message .= "⏰ เวลา: " . date('Y-m-d H:i:s');
        
        $result = $this->user_log_model->send_line_alert($message);
        
        if ($result) {
            echo '<div style="color:green; font-weight:bold;">✅ ส่งข้อความสำเร็จ!</div>';
        } else {
            echo '<div style="color:red; font-weight:bold;">❌ เกิดข้อผิดพลาดในการส่งข้อความ!</div>';
            echo '<p>โปรดตรวจสอบ log ของระบบเพื่อดูรายละเอียดเพิ่มเติม</p>';
        }
    }
    
    public function direct_test() {
        // ทดสอบการส่งข้อความโดยตรงไม่ผ่าน model
        $channelAccessToken = 'cUrLS1xxTWV4NlpIEEXiOftAQpBZKbKAbtIC5TRfQt/7alqWiiXkTO3U/U7WpFAmWOfEtVJr+HHgVJ8c+ZeUcTgb72u5AH9y8iUXokPvh8kAJqQIveN+EjcdbheIKpyvSBtUbHUenswBQq6mmNCvlQdB04t89/1O/w1cDnyilFU=';
        $groupId = "Ca22dd5c6d24bf3790433676526bbaf65";
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $channelAccessToken
        ];
        
        $message = "🔔 ทดสอบโดยตรงจาก direct_test method\n";
        $message .= "⏰ เวลา: " . date('Y-m-d H:i:s');
        
        $data = [
            'to' => $groupId,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message
                ]
            ]
        ];
        
        echo '<h1>Direct Line API Test</h1>';
        echo '<pre>Request Data: ' . print_r($data, true) . '</pre>';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/push');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        
        echo '<h2>Response</h2>';
        echo '<pre>HTTP Code: ' . $info['http_code'] . '</pre>';
        echo '<pre>Response: ' . $response . '</pre>';
        
        if ($error) {
            echo '<pre>Error: ' . $error . '</pre>';
        }
        
        if ($info['http_code'] === 200) {
            echo '<div style="color:green; font-weight:bold;">✅ ส่งข้อความสำเร็จ!</div>';
        } else {
            echo '<div style="color:red; font-weight:bold;">❌ เกิดข้อผิดพลาดในการส่งข้อความ!</div>';
        }
        
        echo '<h2>cURL Info</h2>';
        echo '<pre>' . print_r($info, true) . '</pre>';
        
        curl_close($ch);
    }
}