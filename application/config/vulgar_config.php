<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Vulgar Word API Configuration
| -------------------------------------------------------------------
| Configuration settings for the Vulgar Word API
|
*/
// API Key สำหรับเชื่อมต่อกับระบบตรวจสอบคำไม่สุภาพ
$config['api_key'] = get_config_value('vulgar_api_key');
// URL ของ API endpoint
$config['api_url'] = get_config_value('vulgar_api_url');
// ค่าตัวเลือกสำหรับการตรวจสอบ
$config['options'] = [
    'auto_censor' => true,  // เซ็นเซอร์อัตโนมัติหรือไม่
    'block_submit' => true, // บล็อกการส่งฟอร์มหรือไม่หากพบคำไม่สุภาพ
    'highlight' => true,    // เน้นคำไม่สุภาพในข้อความหรือไม่
    'fields' => [           // ฟิลด์ที่ต้องการตรวจสอบ
        'q_a_msg',
        'q_a_by',
        'q_a_detail',
        'q_a_email',
        'q_a_reply_by',
        'q_a_reply_detail',
        'q_a_reply_email'
    ]
];

// ตั้งค่า modal
$config['modal_id'] = 'vulgar_warning_modal';
$config['modal_title'] = 'คำเตือน: พบข้อความที่ไม่เหมาะสม';
$config['modal_button_text'] = 'ตกลง';