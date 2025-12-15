<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Drive System Configuration v1.0.0
 * การตั้งค่าสำหรับ Centralized Google Drive Storage
 * 
 * @author   System Developer
 * @version  1.0.0
 * @since    2025-01-20
 */

// ===================================================
// ระบบหลัก (System Core Settings)
// ===================================================

// โหมดการทำงานของระบบ
$config['system_storage_mode'] = 'user_based'; // 'user_based' หรือ 'centralized'

// เปิด/ปิดการใช้งาน System Storage
$config['system_storage_enabled'] = false;

// การ Debug สำหรับ System Storage
$config['system_debug_mode'] = (ENVIRONMENT === 'development');

// ===================================================
// การตั้งค่า Storage (Storage Settings)
// ===================================================

// ขีดจำกัด Storage ของระบบ (bytes) - 100GB เริ่มต้น
$config['system_storage_limit'] = 107374182400;

// Quota เริ่มต้นสำหรับ User (bytes) - 1GB เริ่มต้น
$config['default_user_quota'] = 1073741824;

// ขนาดไฟล์สูงสุดสำหรับ System Storage (bytes) - 100MB เริ่มต้น
$config['max_file_size_system'] = 104857600;

// ประเภทไฟล์ที่อนุญาตใน System Storage
$config['allowed_file_types_system'] = [
    // เอกสาร
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp',
    // รูปภาพ
    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico',
    // วิดีโอ
    'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv',
    // เสียง
    'mp3', 'wav', 'flac', 'aac', 'ogg', 'wma',
    // ไฟล์บีบอัด
    'zip', 'rar', '7z', 'tar', 'gz',
    // อื่นๆ
    'csv', 'json', 'xml', 'html', 'css', 'js'
];

// ===================================================
// โครงสร้างโฟลเดอร์ (Folder Structure)
// ===================================================

// สร้างโฟลเดอร์ User อัตโนมัติ
$config['auto_create_user_folders'] = true;

// สร้างโฟลเดอร์แผนกอัตโนมัติ
$config['auto_create_department_folders'] = true;

// โครงสร้างโฟลเดอร์เริ่มต้น
$config['default_folder_structure'] = [
    'Organization Drive' => [
        'type' => 'root',
        'description' => 'โฟลเดอร์หลักของระบบ',
        'permission' => 'system',
        'subfolders' => [
            'Admin' => [
                'type' => 'admin',
                'description' => 'โฟลเดอร์สำหรับผู้ดูแลระบบ',
                'permission' => 'admin_only'
            ],
            'Departments' => [
                'type' => 'system',
                'description' => 'โฟลเดอร์แผนกต่างๆ',
                'permission' => 'department_based',
                'auto_create_subfolders' => true
            ],
            'Shared' => [
                'type' => 'shared',
                'description' => 'โฟลเดอร์ส่วนกลาง',
                'permission' => 'public',
                'subfolders' => [
                    'Public Documents' => [
                        'type' => 'shared',
                        'permission' => 'read_all'
                    ],
                    'Templates' => [
                        'type' => 'shared',
                        'permission' => 'read_all'
                    ],
                    'Forms' => [
                        'type' => 'shared',
                        'permission' => 'read_all'
                    ]
                ]
            ],
            'Users' => [
                'type' => 'system',
                'description' => 'โฟลเดอร์ส่วนตัวของผู้ใช้',
                'permission' => 'user_private',
                'auto_create_subfolders' => true
            ]
        ]
    ]
];

// ===================================================
// การจัดการสิทธิ์ (Permission Management)
// ===================================================

// สิทธิ์ตามตำแหน่ง (Position-based Permissions)
$config['position_permissions'] = [
    1 => [ // System Admin
        'type' => 'system_admin',
        'access_level' => 'full',
        'can_create_folder' => true,
        'can_upload' => true,
        'can_download' => true,
        'can_delete' => true,
        'can_share' => true,
        'can_manage_users' => true,
        'storage_quota' => 'unlimited',
        'accessible_folders' => ['all']
    ],
    2 => [ // Super Admin
        'type' => 'system_admin',
        'access_level' => 'full',
        'can_create_folder' => true,
        'can_upload' => true,
        'can_download' => true,
        'can_delete' => true,
        'can_share' => true,
        'can_manage_users' => true,
        'storage_quota' => 'unlimited',
        'accessible_folders' => ['all']
    ],
    3 => [ // Department Admin
        'type' => 'department_admin',
        'access_level' => 'department',
        'can_create_folder' => true,
        'can_upload' => true,
        'can_download' => true,
        'can_delete' => true,
        'can_share' => true,
        'can_manage_users' => false,
        'storage_quota' => 10737418240, // 10GB
        'accessible_folders' => ['department', 'shared', 'own']
    ],
    4 => [ // Staff
        'type' => 'department_user',
        'access_level' => 'limited',
        'can_create_folder' => true,
        'can_upload' => true,
        'can_download' => true,
        'can_delete' => false,
        'can_share' => false,
        'can_manage_users' => false,
        'storage_quota' => 2147483648, // 2GB
        'accessible_folders' => ['shared', 'own']
    ]
];

// ===================================================
// การ Logging และ Monitoring
// ===================================================

// เปิด/ปิดการบันทึก Log
$config['system_logging_enabled'] = true;

// ระดับ Log ที่ต้องการบันทึก
$config['system_log_levels'] = [
    'upload', 'download', 'create_folder', 'delete_folder', 
    'delete_file', 'grant_access', 'revoke_access', 'setup',
    'error', 'warning', 'info'
];

// เก็บ Log กี่วัน
$config['system_log_retention_days'] = 90;

// เปิด/ปิดการส่งอีเมลแจ้งเตือน
$config['system_email_notifications'] = false;

// อีเมลผู้ดูแลระบบ
$config['system_admin_emails'] = [
    'admin@example.com'
];

// ===================================================
// การปรับแต่ง Performance
// ===================================================

// เปิด/ปิด Cache
$config['system_cache_enabled'] = true;

// ระยะเวลา Cache (วินาที)
$config['system_cache_duration'] = 3600; // 1 ชั่วโมง

// จำนวนไฟล์สูงสุดที่แสดงต่อหน้า
$config['files_per_page'] = 50;

// จำนวน Activity Logs ที่แสดงต่อหน้า
$config['activities_per_page'] = 20;

// ===================================================
// การตั้งค่า Security
// ===================================================

// ป้องกันการอัปโหลดไฟล์อันตราย
$config['security_scan_enabled'] = true;

// ประเภทไฟล์ที่ห้ามอัปโหลด
$config['blocked_file_types'] = [
    'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 
    'jar', 'ws', 'wsh', 'php', 'asp', 'aspx', 'jsp'
];

// ขนาดไฟล์สูงสุดสำหรับการ Scan (bytes)
$config['max_scan_file_size'] = 52428800; // 50MB

// การเข้ารหัสชื่อไฟล์
$config['encrypt_file_names'] = false;

// ===================================================
// การตั้งค่า Google Drive API
// ===================================================

// ข้อมูลสำหรับเชื่อมต่อ Google Drive (จะถูกโหลดจากฐานข้อมูล)
$config['google_drive_api_settings'] = [
    'application_name' => 'Centralized Google Drive Storage v1.0.0',
    'scopes' => [
        'https://www.googleapis.com/auth/drive',
        'https://www.googleapis.com/auth/drive.file',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile'
    ],
    'access_type' => 'offline',
    'prompt' => 'consent',
    'include_granted_scopes' => true
];

// Timeout สำหรับการเชื่อมต่อ Google API (วินาที)
$config['google_api_timeout'] = 30;

// จำนวนครั้งที่ลองใหม่เมื่อเกิด Error
$config['google_api_retry_attempts'] = 3;

// ===================================================
// การตั้งค่า UI/UX
// ===================================================

// ธีมของ System Storage
$config['system_theme'] = 'modern'; // 'modern', 'classic', 'dark'

// ภาษาเริ่มต้น
$config['system_default_language'] = 'thai';

// การแสดงผลรูปภาพ Thumbnail
$config['show_thumbnails'] = true;

// ขนาด Thumbnail (pixels)
$config['thumbnail_size'] = 150;

// การแสดงผล Breadcrumbs
$config['show_breadcrumbs'] = true;

// ===================================================
// การตั้งค่า Integration
// ===================================================

// เชื่อมต่อกับระบบอื่น
$config['external_integrations'] = [
    'email_system' => false,
    'notification_system' => false,
    'ldap_integration' => false,
    'sso_integration' => false
];

// ===================================================
// การตั้งค่า Backup และ Recovery
// ===================================================

// เปิด/ปิดการสำรองข้อมูลอัตโนมัติ
$config['auto_backup_enabled'] = false;

// ความถี่ในการสำรองข้อมูล (ชั่วโมง)
$config['backup_frequency_hours'] = 24;

// จำนวนไฟล์สำรองที่เก็บไว้
$config['backup_retention_count'] = 7;

// ===================================================
// การตั้งค่า Migration
// ===================================================

// เปิด/ปิดโหมด Migration จาก User-based เป็น Centralized
$config['migration_mode'] = false;

// ขนาดแบทช์สำหรับ Migration
$config['migration_batch_size'] = 100;

// เก็บข้อมูลเดิมหลัง Migration
$config['keep_original_data'] = true;

// ===================================================
// การตั้งค่า API และ Webhook
// ===================================================

// เปิด/ปิด API สำหรับระบบภายนอก
$config['api_enabled'] = false;

// API Key สำหรับการเชื่อมต่อ
$config['api_key'] = '';

// Webhook URL สำหรับแจ้งเตือน
$config['webhook_urls'] = [];

// ===================================================
// ข้อมูลเพิ่มเติมสำหรับระบบ
// ===================================================

// เวอร์ชันของ System Storage
$config['system_version'] = '1.0.0';

// วันที่อัปเดตล่าสุด
$config['system_last_updated'] = '2025-01-20';

// ผู้พัฒนาระบบ
$config['system_developer'] = 'System Development Team';

// ลิขสิทธิ์
$config['system_copyright'] = '© 2025 Organization. All rights reserved.';