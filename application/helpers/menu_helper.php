<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('is_active_menu')) {
    function is_active_menu($segment_number, $segment_value) {
        $CI = &get_instance();
        $current_segment = $CI->uri->segment($segment_number);
        return ($current_segment === $segment_value) ? 
            'bg-blue-50 text-blue-600 shadow-md scale-[1.02]' : 
            'text-gray-600 hover:bg-white';
    }
}

if (!function_exists('get_active_modules')) {
    function get_active_modules() {
        $CI = &get_instance();
        return $CI->db->where('status', 1)
                      ->order_by('display_order', 'ASC')
                      ->get('tbl_member_modules')
                      ->result();
    }
}

if (!function_exists('check_member_permission')) {
    function check_member_permission() {
        $CI = &get_instance();
        $user_type = $CI->session->userdata('m_system');
        $member_id = $CI->session->userdata('m_id');
        
        if (in_array($user_type, ['system_admin', 'super_admin'])) {
            return true;
        }
        
        if ($user_type === 'user_admin') {
            return $CI->db->where('member_id', $member_id)
                         ->where('system_id', 1)
                         ->where('is_active', 1)
                         ->get('tbl_member_user_permissions')
                         ->num_rows() > 0;
        }
        
        return false;
    }
}