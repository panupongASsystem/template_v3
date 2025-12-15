<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Composer Auto-loading (Working Configuration)
| -------------------------------------------------------------------
|
| ใช้ third_party Google API Client ที่พบแล้ว
|
*/

// ใช้ third_party path ที่ใช้งานได้
$config['composer_autoload'] = APPPATH . 'third_party/google-api-php-client/autoload.php';

// หรือถ้าต้องการใช้ vendor (ถ้ามี Composer)
// $config['composer_autoload'] = FCPATH . 'vendor/autoload.php';