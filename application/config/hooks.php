<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/

// Hook สำหรับตรวจสอบ Cookie Consent
$hook['post_controller_constructor'][] = array(
    'class'    => 'Cookie_consent',
    'function' => 'check_consent',
    'filename' => 'Cookie_consent.php',
    'filepath' => 'hooks'
);

// Hook สำหรับตรวจสอบการรีเฟรช Google Drive Token อัตโนมัติ
$hook['post_controller_constructor'][] = array(
    'class'    => 'Auto_token_refresh_hook',
    'function' => 'check_google_drive_token',
    'filename' => 'Auto_token_refresh_hook.php',
    'filepath' => 'hooks'
);

// Hook สำหรับการ minify HTML
$hook['display_override'] = array(
    'class'    => 'HtmlMinifyHook',
    'function' => 'minify_html',
    'filename' => 'HtmlMinifyHook.php',
    'filepath' => 'hooks'
);

// Hook สำหรับตรวจสอบการแจ้งเตือน
$hook['post_controller'] = array(
    'class' => 'Notification_hook',
    'function' => 'check_notifications',
    'filename' => 'Notification_hook.php',
    'filepath' => 'hooks'
);