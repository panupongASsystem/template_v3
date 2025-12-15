<?php
if (!function_exists('thai_date')) {
    function thai_date($date, $formatDay = false)
    {
        if ($date == '0000-00-00' || $date == null) {
            return '-';
        }

        $timestamp = strtotime($date);
        $thai_month_arr = array(
            "0" => "",
            "1" => "มกราคม",
            "2" => "กุมภาพันธ์",
            "3" => "มีนาคม",
            "4" => "เมษายน",
            "5" => "พฤษภาคม",
            "6" => "มิถุนายน",
            "7" => "กรกฎาคม",
            "8" => "สิงหาคม",
            "9" => "กันยายน",
            "10" => "ตุลาคม",
            "11" => "พฤศจิกายน",
            "12" => "ธันวาคม"
        );

        $thai_date = date("d", $timestamp);
        $thai_month = $thai_month_arr[date("n", $timestamp)];
        $thai_year = date("Y", $timestamp) + 543;

        return "$thai_date $thai_month $thai_year";
    }

    function thai_date_month($date)
    {
        $thai_month_arr = array(
            "01" => "มกราคม",
            "02" => "กุมภาพันธ์",
            "03" => "มีนาคม",
            "04" => "เมษายน",
            "05" => "พฤษภาคม",
            "06" => "มิถุนายน",
            "07" => "กรกฎาคม",
            "08" => "สิงหาคม",
            "09" => "กันยายน",
            "10" => "ตุลาคม",
            "11" => "พฤศจิกายน",
            "12" => "ธันวาคม"
        );

        $parts = explode('-', $date);
        if (count($parts) != 2) return '-';

        return $parts[0] . ' เดือน ' . $thai_month_arr[$parts[1]];
    }

    if (!function_exists('get_thai_month')) {
        function get_thai_month($month_number) {
            $thai_month_arr = [
                "01" => "มกราคม",
                "02" => "กุมภาพันธ์",
                "03" => "มีนาคม",
                "04" => "เมษายน",
                "05" => "พฤษภาคม",
                "06" => "มิถุนายน",
                "07" => "กรกฎาคม",
                "08" => "สิงหาคม",
                "09" => "กันยายน",
                "10" => "ตุลาคม",
                "11" => "พฤศจิกายน",
                "12" => "ธันวาคม"
            ];
            return isset($thai_month_arr[$month_number]) ? $thai_month_arr[$month_number] : '';
        }
    }

 if (!function_exists('get_due_date')) {
    function get_due_date($tax_type, $tax_year) {
        $CI =& get_instance();
        
        if (!isset($CI->tax_due_date_model)) {
            $CI->load->model('tax_due_date_model');
        }
        
        // เชื่อมต่อกับฐานข้อมูล tax
        $tax_db = $CI->load->database('db_tax', TRUE);
        
        // ดึงข้อมูลกำหนดวันจากตาราง tax_due_dates ตาม tax_type
        $due_date = $tax_db->where('tax_type', $tax_type)
                          ->get('tbl_tax_due_dates')
                          ->row();
                          
        if ($due_date) {
            // รวมวันที่จาก tax_due_dates กับปีจาก tax_payments
            $parts = explode('-', $due_date->due_date);
            $day = $parts[0];
            $month = $parts[1];
            
            // แปลงรูปแบบเป็น "วันที่ เดือน ปี พ.ศ."
            return $day . ' ' . get_thai_month($month) . ' ' . $tax_year;
        }
        return '-';
    }
}
}
