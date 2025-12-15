<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Configuration for Verification System
 */
$domain = get_config_value('domain');
// SMTP Configuration
$config['protocol']    = 'smtp';
$config['smtp_host']   = 'thsv71.hostatom.com';  // 🔧 เปลี่ยนเป็นของคุณ
$config['smtp_user']   = 'no-reply@' . $domain . '.go.th';  // 🔧 อีเมลผู้ส่ง
$config['smtp_pass']   = '5436q^mmJ';  // 🔧 App Password (16 ตัวอักษร)
$config['smtp_port']   = 587;
$config['smtp_crypto'] = 'tls';  // tls หรือ ssl
$config['smtp_timeout']= 30;

// Email Format
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['wordwrap']    = TRUE;
$config['newline']     = "\r\n";
$config['crlf']        = "\r\n";
$config['validate']    = TRUE;

// Debug (ตั้งเป็น 0 ใน production)
$config['smtp_debug']  = 0;  // 0=ปิด, 2=แสดงรายละเอียด

// From Email Default
$config['from_email']  = 'no-reply@' . $domain . '.go.th';  // 🔧 เปลี่ยนเป็นของคุณ
$config['from_name']   = 'ระบบยืนยันตัวตน';