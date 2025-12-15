<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Timeago Helper - สำหรับแสดงเวลาภาษาไทย
 * ไฟล์: application/helpers/timeago_helper.php
 */

if (!function_exists('timeago')) {
    function timeago($datetime) {
        if (empty($datetime)) {
            return 'ไม่ทราบเวลา';
        }
        
        try {
            $time = time() - strtotime($datetime);
            
            if ($time < 60) {
                return 'เมื่อสักครู่';
            } elseif ($time < 3600) {
                $minutes = round($time / 60);
                return $minutes . ' นาทีที่แล้ว';
            } elseif ($time < 86400) {
                $hours = round($time / 3600);
                return $hours . ' ชั่วโมงที่แล้ว';
            } elseif ($time < 2592000) {
                $days = round($time / 86400);
                return $days . ' วันที่แล้ว';
            } elseif ($time < 31536000) {
                $months = round($time / 2592000);
                return $months . ' เดือนที่แล้ว';
            } else {
                $years = round($time / 31536000);
                return $years . ' ปีที่แล้ว';
            }
        } catch (Exception $e) {
            return date('d/m/Y H:i', strtotime($datetime));
        }
    }
}

if (!function_exists('smart_timeago')) {
    function smart_timeago($datetime) {
        if (empty($datetime)) {
            return 'ไม่ทราบเวลา';
        }
        
        try {
            $timestamp = strtotime($datetime);
            $time_diff = time() - $timestamp;
            
            if ($time_diff < 60) {
                return 'เมื่อสักครู่';
            } elseif ($time_diff < 3600) {
                $minutes = floor($time_diff / 60);
                return $minutes . ' นาทีที่แล้ว';
            } elseif ($time_diff < 86400) {
                $hours = floor($time_diff / 3600);
                return $hours . ' ชั่วโมงที่แล้ว';
            } elseif ($time_diff < 604800) {
                $days = floor($time_diff / 86400);
                return $days . ' วันที่แล้ว';
            } else {
                return date('d/m/Y H:i', $timestamp);
            }
        } catch (Exception $e) {
            return date('d/m/Y H:i', strtotime($datetime));
        }
    }
}

if (!function_exists('format_thai_date')) {
    function format_thai_date($datetime, $format = 'd/m/Y H:i') {
        if (empty($datetime)) {
            return 'ไม่ทราบวันที่';
        }
        
        try {
            $thai_months = [
                '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.',
                '04' => 'เม.ย.', '05' => 'พ.ค.', '06' => 'มิ.ย.',
                '07' => 'ก.ค.', '08' => 'ส.ค.', '09' => 'ก.ย.',
                '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
            ];
            
            $timestamp = strtotime($datetime);
            $formatted = date($format, $timestamp);
            
            foreach ($thai_months as $num => $thai) {
                $formatted = str_replace('/' . $num . '/', '/' . $thai . '/', $formatted);
            }
            
            return $formatted;
        } catch (Exception $e) {
            return date('d/m/Y H:i', strtotime($datetime));
        }
    }
}

if (!function_exists('thai_date_time')) {
    function thai_date_time($datetime) {
        $thai_months = array(
            'January' => 'มกราคม',
            'February' => 'กุมภาพันธ์', 
            'March' => 'มีนาคม',
            'April' => 'เมษายน',
            'May' => 'พฤษภาคม',
            'June' => 'มิถุนายน',
            'July' => 'กรกฎาคม',
            'August' => 'สิงหาคม',
            'September' => 'กันยายน',
            'October' => 'ตุลาคม',
            'November' => 'พฤศจิกายน',
            'December' => 'ธันวาคม'
        );
        
        $date = date('j F Y เวลา H:i น.', strtotime($datetime));
        return strtr($date, $thai_months);
    }
}

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        // แก้ไขปัญหา deprecated - ใช้ตัวแปรแยกแทนการสร้าง dynamic property
        $weeks = floor($diff->d / 7);
        $days_remaining = $diff->d - ($weeks * 7);

        $string = array(
            'y' => $diff->y > 0 ? $diff->y . ' ปี' : null,
            'm' => $diff->m > 0 ? $diff->m . ' เดือน' : null,
            'w' => $weeks > 0 ? $weeks . ' สัปดาห์' : null,
            'd' => $days_remaining > 0 ? $days_remaining . ' วัน' : null,
            'h' => $diff->h > 0 ? $diff->h . ' ชั่วโมง' : null,
            'i' => $diff->i > 0 ? $diff->i . ' นาที' : null,
            's' => $diff->s > 0 ? $diff->s . ' วินาที' : null,
        );
        
        // กรองค่าที่เป็น null ออก
        $string = array_filter($string);

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }
        
        return $string ? implode(', ', $string) . ' ที่แล้ว' : 'เมื่อสักครู่';
    }
}
?>