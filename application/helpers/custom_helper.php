<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('convert_to_thai_numerals')) {
    function convert_to_thai_numerals($number) {
        $thai_numerals = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
        return str_replace(range(0, 9), $thai_numerals, $number);
    }
}

if (!function_exists('get_thai_month_year')) {
    function get_thai_month_year($date) {
        $months = [
            '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
            '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
            '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
        ];
        $date_obj = new DateTime($date);
        $month = $date_obj->format('m');
        $year = $date_obj->format('Y') + 543;
        return $months[$month] . ' ' . convert_to_thai_numerals((string)$year);
    }
}

// system config
if (!function_exists('get_config_value')) {
    function get_config_value($key) {
        $CI =& get_instance();
        
        if (!isset($CI->config_data)) {
            $CI->load->model('system_config_model');
            $CI->config_data = $CI->system_config_model->get_all_config();
        }
        
        return isset($CI->config_data[$key]) ? $CI->config_data[$key] : '';
    }
}

// โครงสร้างบุคลากร 
if (!function_exists('get_personnel')) {
    function get_personnel($pid) {
        $CI = &get_instance();
        $CI->load->model('position_model');

        return $CI->position_model->get_personnel_by_id($pid);
    }
	}

	// โครงสร้างบุคลากร แบบ dynamic
if (!function_exists('get_position_types')) {
    function get_position_types($status = 'show')
    {
        $CI = &get_instance();

        // เงื่อนไข peng ไม่เป็นค่าว่าง
        $CI->db->where('peng !=', '');
        $CI->db->where('peng IS NOT NULL');

        // ถ้า $status เป็น null หรือ false ให้ดึงทั้งหมด
        if ($status !== null && $status !== false) {
            $CI->db->where('pstatus', $status);
        }

        $CI->db->order_by('porder', 'ASC');
        $CI->db->order_by('pname', 'ASC'); // เรียงตามชื่อเป็นรอง
        return $CI->db->get('tbl_position')->result();
    }
}