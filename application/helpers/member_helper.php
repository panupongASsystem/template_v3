<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ฟังก์ชั่นเช็คเมนูที่เปิดอยู่
function is_active_menu($segment_number, $segment_value) {
    $CI = &get_instance();
    $current_segment = $CI->uri->segment($segment_number);
    
    // Debug log
    log_message('debug', 'Current segment: ' . $current_segment);
    log_message('debug', 'Expected value: ' . $segment_value);
    
    return ($current_segment === $segment_value) ? 
        'bg-blue-50 text-blue-600 shadow-md scale-[1.02]' : 
        'text-gray-600 hover:bg-white';
}

// ฟังก์ชั่นเช็คสิทธิ์การจัดการสมาชิก
function has_member_management_permission() {
    $CI = &get_instance();
    
    // ถ้าเป็น system_admin หรือ super_admin ให้ผ่านเลย
    if (in_array($CI->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        return true;
    }
    
    // เช็คสิทธิ์จากตาราง tbl_member_user_permissions
    $member_id = $CI->session->userdata('m_id');
    $has_permission = $CI->db->where('member_id', $member_id)
                            ->where('permission_code', 'view')
                            ->join('tbl_member_module_menus', 'tbl_member_module_menus.id = tbl_member_user_permissions.system_id')
                            ->where('tbl_member_module_menus.module_id', 1) // module_id 1 คือระบบจัดการสมาชิก
                            ->get('tbl_member_user_permissions')
                            ->num_rows() > 0;
                            
    return $has_permission;
}

// ฟังก์ชั่นเช็คสิทธิ์การจัดการเว็บไซต์
function has_website_management_permission() {
    $CI = &get_instance();
    
    // ถ้าเป็น system_admin หรือ super_admin ให้ผ่านเลย
    if (in_array($CI->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        return true;
    }
    
    // เช็คว่ามีสิทธิ์ grant_system_ref_id = 2 หรือไม่
    $member_id = $CI->session->userdata('m_id');
    $member = $CI->db->where('m_id', $member_id)
                    ->get('tbl_member')
                    ->row();
    
    if ($member) {
        $grant_systems = explode(',', $member->grant_system_ref_id);
        return in_array('2', $grant_systems);
    }
    
    return false;
}

// ฟังก์ชั่นเช็คสิทธิ์สำหรับโมดูลที่กำหนด
function has_module_permission($module_id) {
    $CI = &get_instance();
    
    // ถ้าเป็น system_admin หรือ super_admin ให้ผ่านเลย
    if (in_array($CI->session->userdata('m_system'), ['system_admin', 'super_admin'])) {
        return true;
    }
    
    // เช็คว่ามีสิทธิ์ในโมดูลนี้หรือไม่
    $member_id = $CI->session->userdata('m_id');
    $member = $CI->db->where('m_id', $member_id)
                    ->get('tbl_member')
                    ->row();
    
    if ($member) {
        $grant_systems = explode(',', $member->grant_system_ref_id);
        return in_array((string)$module_id, $grant_systems);
    }
    
    return false;
}

// ฟังก์ชั่นเช็คสถานะการใช้งานของโมดูล
function is_module_active($module_id) {
    $CI = &get_instance();
    $module = $CI->db->where('id', $module_id)
                     ->where('status', 1)
                     ->get('tbl_member_modules')
                     ->row();
    return !empty($module);
}