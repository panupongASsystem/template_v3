<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HtmlMinifyHook {
    function minify_html() {
        $CI =& get_instance();
        $buffer = $CI->output->get_output();
        
        // เก็บส่วนของ <script> และ <pre> ไว้ก่อน
        $scripts = array();
        $script_count = 0;
        $pre_blocks = array();
        $pre_count = 0;
        
        // เก็บ script blocks
        $buffer = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function($matches) use (&$scripts, &$script_count) {
            $scripts[$script_count] = $matches[0];
            $placeholder = '<script_placeholder_' . $script_count . '>';
            $script_count++;
            return $placeholder;
        }, $buffer);
        
        // เก็บ pre blocks
        $buffer = preg_replace_callback('/<pre\b[^>]*>(.*?)<\/pre>/is', function($matches) use (&$pre_blocks, &$pre_count) {
            $pre_blocks[$pre_count] = $matches[0];
            $placeholder = '<pre_placeholder_' . $pre_count . '>';
            $pre_count++;
            return $placeholder;
        }, $buffer);
        
        // ลบช่องว่างและคอมเมนต์
        $search = array(
            '/\>[^\S ]+/s',     // ลบช่องว่างหลัง tags
            '/[^\S ]+\</s',     // ลบช่องว่างก่อน tags
            '/(\s)+/s',         // ลบช่องว่างซ้ำ
        );
        
        $replace = array('>', '<', '\\1');
        $buffer = preg_replace($search, $replace, $buffer);
        
        // ลบคอมเมนต์ HTML เท่านั้น (แบบปลอดภัยกว่า)
        $buffer = preg_replace_callback('/<!--([\s\S]*?)-->/s', function($matches) {
            // เก็บคอมเมนต์สำคัญไว้ (เช่น conditional comments สำหรับ IE)
            if (strpos($matches[1], '[if') !== false || strpos($matches[1], '<![endif') !== false) {
                return $matches[0];
            }
            return ''; // ลบคอมเมนต์ทั่วไป
        }, $buffer);
        
        // ใส่ script blocks กลับเข้าไป
        for ($i = 0; $i < $script_count; $i++) {
            $buffer = str_replace('<script_placeholder_' . $i . '>', $scripts[$i], $buffer);
        }
        
        // ใส่ pre blocks กลับเข้าไป
        for ($i = 0; $i < $pre_count; $i++) {
            $buffer = str_replace('<pre_placeholder_' . $i . '>', $pre_blocks[$i], $buffer);
        }
        
        $CI->output->set_output($buffer);
        $CI->output->_display();
        exit;
    }
}