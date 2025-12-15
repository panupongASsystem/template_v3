<?php
// =====================================================
// 📁 application/hooks/Auto_token_refresh_hook.php
// Hook สำหรับเรียกใช้อัตโนมัติ
// =====================================================

class Auto_token_refresh_hook {
    
    public function check_google_drive_token() {
        $CI =& get_instance();
        
        // เฉพาะหน้าที่เกี่ยวข้องกับ Google Drive
        $controller = $CI->router->fetch_class();
        $method = $CI->router->fetch_method();
        
        $google_drive_pages = [
            'google_drive_system',
            'google_drive_user', 
            'google_drive'
        ];
        
        if (in_array($controller, $google_drive_pages)) {
            $CI->load->library('google_drive_auto_refresh');
            $CI->google_drive_auto_refresh->auto_check_and_refresh();
        }
    }
}