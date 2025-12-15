<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ✅ แก้ไข: ใช้ CodeIgniter instance แบบถูกต้องใน View
$staff_notifications = [];
$staff_unread_count = 0;
$staff_total_notifications = 0;

// ✅ แก้ไข: ใช้ get_instance() แทน $this ใน View
$CI =& get_instance();
$m_id = $CI->session->userdata('m_id');

if ($m_id) {
    try {
        // ✅ แก้ไข: ตรวจสอบและโหลด notification_lib แบบถูกต้อง
        if (!isset($CI->notification_lib)) {
            // ตรวจสอบว่าไฟล์ library มีอยู่จริงหรือไม่
            $library_path = APPPATH . 'libraries/Notification_lib.php';
            if (file_exists($library_path)) {
                $CI->load->library('notification_lib');
                log_message('info', 'Header: notification_lib loaded successfully via CI instance');
            } else {
                log_message('error', 'Header: notification_lib file not found at: ' . $library_path);
                throw new Exception('notification_lib not available');
            }
        }
        
        // ✅ แก้ไข: ตรวจสอบ method ก่อนเรียกใช้
        if (method_exists($CI->notification_lib, 'get_staff_notifications_with_corruption_filter') && 
            method_exists($CI->notification_lib, 'get_staff_unread_count_with_corruption_filter')) {
            
            $staff_notifications = $CI->notification_lib->get_staff_notifications_with_corruption_filter($m_id, 5, 0);
            $staff_unread_count = $CI->notification_lib->get_staff_unread_count_with_corruption_filter($m_id);
            
            log_message('info', 'Header: Using corruption filter methods - Count: ' . count($staff_notifications) . ', Unread: ' . $staff_unread_count);
            
        } elseif (method_exists($CI->notification_lib, 'get_user_notifications') && 
                  method_exists($CI->notification_lib, 'get_unread_count')) {
            
            // Fallback: ใช้ method ปกติ
            $staff_notifications = $CI->notification_lib->get_user_notifications('staff', 5, 0);
            $staff_unread_count = $CI->notification_lib->get_unread_count('staff');
            
            log_message('info', 'Header: Using standard methods - Count: ' . count($staff_notifications) . ', Unread: ' . $staff_unread_count);
            
        } else {
            // Fallback: ใช้ Raw SQL
            log_message('warning', 'Header: notification_lib methods not available, using fallback');
            $staff_notifications = get_header_notifications_fallback($m_id);
            $staff_unread_count = get_header_unread_count_fallback($m_id);
            
            log_message('info', 'Header: Using fallback methods - Count: ' . count($staff_notifications) . ', Unread: ' . $staff_unread_count);
        }
        
    } catch (Exception $e) {
        log_message('error', 'Header: All notification methods failed: ' . $e->getMessage());
        
        // ✅ Final Fallback: Simple Raw SQL
        try {
            $staff_notifications = get_header_notifications_simple($m_id);
            $staff_unread_count = get_header_unread_count_simple($m_id);
            log_message('info', 'Header: Using simple fallback - Count: ' . count($staff_notifications) . ', Unread: ' . $staff_unread_count);
        } catch (Exception $e2) {
            log_message('error', 'Header: Even simple fallback failed: ' . $e2->getMessage());
            $staff_notifications = [];
            $staff_unread_count = 0;
        }
    }
} else {
    log_message('info', 'Header: No user ID in session');
}

// Debug info
$header_debug = [
    'user_id' => $m_id,
    'notifications_count' => count($staff_notifications),
    'unread_count' => $staff_unread_count,
    'method_used' => 'ci_instance_safe_loading',
    'corruption_notifications' => 0,
    'library_loaded' => isset($CI->notification_lib) ? 'YES' : 'NO'
];

// นับ corruption notifications
if (!empty($staff_notifications)) {
    foreach ($staff_notifications as $notif) {
        if (isset($notif->reference_table) && $notif->reference_table === 'tbl_corruption_reports') {
            $header_debug['corruption_notifications']++;
        }
    }
}

log_message('info', 'Header Final Debug (CI Instance): ' . json_encode($header_debug));

// ✅ เพิ่ม: Fallback functions สำหรับกรณีที่ notification_lib ไม่พร้อมใช้งาน
if (!function_exists('get_header_notifications_fallback')) {
    function get_header_notifications_fallback($user_id) {
        $CI =& get_instance();
        
        try {
            // ตรวจสอบสิทธิ์ corruption
            $has_corruption_permission = check_header_corruption_permission($user_id);
            
            if ($has_corruption_permission) {
                // มีสิทธิ์: แสดงทั้งหมด
                $sql = "
                    SELECT n.*, 
                           nr.read_at as user_read_at, 
                           CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                    FROM tbl_notifications n
                    LEFT JOIN tbl_notification_reads nr ON (
                        n.notification_id = nr.notification_id 
                        AND nr.user_id = ? 
                        AND nr.user_type = 'staff'
                    )
                    WHERE n.target_role = 'staff' 
                      AND n.is_archived = 0
                    ORDER BY n.created_at DESC
                    LIMIT 5
                ";
            } else {
                // ไม่มีสิทธิ์: ซ่อน corruption
                $sql = "
                    SELECT n.*, 
                           nr.read_at as user_read_at, 
                           CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read_by_user
                    FROM tbl_notifications n
                    LEFT JOIN tbl_notification_reads nr ON (
                        n.notification_id = nr.notification_id 
                        AND nr.user_id = ? 
                        AND nr.user_type = 'staff'
                    )
                    WHERE n.target_role = 'staff' 
                      AND n.is_archived = 0
                      AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                    ORDER BY n.created_at DESC
                    LIMIT 5
                ";
            }
            
            $query = $CI->db->query($sql, [$user_id]);
            
            if ($query && $query->num_rows() > 0) {
                $results = $query->result();
                
                // แปลง JSON data
                foreach ($results as $notification) {
                    if ($notification->data && is_string($notification->data)) {
                        $notification->data = json_decode($notification->data);
                    }
                }
                
                return $results;
            }
            
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_header_notifications_fallback: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('get_header_unread_count_fallback')) {
    function get_header_unread_count_fallback($user_id) {
        $CI =& get_instance();
        
        try {
            // ตรวจสอบสิทธิ์ corruption
            $has_corruption_permission = check_header_corruption_permission($user_id);
            
            if ($has_corruption_permission) {
                // มีสิทธิ์: นับทั้งหมด
                $sql = "
                    SELECT COUNT(*) as unread_count
                    FROM tbl_notifications n
                    LEFT JOIN tbl_notification_reads nr ON (
                        n.notification_id = nr.notification_id 
                        AND nr.user_id = ? 
                        AND nr.user_type = 'staff'
                    )
                    WHERE n.target_role = 'staff' 
                      AND n.is_archived = 0 
                      AND nr.id IS NULL
                ";
            } else {
                // ไม่มีสิทธิ์: ซ่อน corruption
                $sql = "
                    SELECT COUNT(*) as unread_count
                    FROM tbl_notifications n
                    LEFT JOIN tbl_notification_reads nr ON (
                        n.notification_id = nr.notification_id 
                        AND nr.user_id = ? 
                        AND nr.user_type = 'staff'
                    )
                    WHERE n.target_role = 'staff' 
                      AND n.is_archived = 0 
                      AND nr.id IS NULL
                      AND (n.reference_table IS NULL OR n.reference_table != 'tbl_corruption_reports')
                ";
            }
            
            $query = $CI->db->query($sql, [$user_id]);
            
            if ($query) {
                $result = $query->row();
                return $result ? (int)$result->unread_count : 0;
            }
            
            return 0;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_header_unread_count_fallback: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('get_header_notifications_simple')) {
    function get_header_notifications_simple($user_id) {
        $CI =& get_instance();
        
        try {
            // Simple query ไม่มีการกรอง corruption
            $sql = "
                SELECT n.*, 0 as is_read_by_user
                FROM tbl_notifications n
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
                ORDER BY n.created_at DESC
                LIMIT 5
            ";
            
            $query = $CI->db->query($sql);
            
            if ($query && $query->num_rows() > 0) {
                return $query->result();
            }
            
            return [];
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_header_notifications_simple: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('get_header_unread_count_simple')) {
    function get_header_unread_count_simple($user_id) {
        $CI =& get_instance();
        
        try {
            // Simple count ไม่มีการกรอง
            $sql = "
                SELECT COUNT(*) as unread_count
                FROM tbl_notifications n
                WHERE n.target_role = 'staff' 
                  AND n.is_archived = 0
            ";
            
            $query = $CI->db->query($sql);
            
            if ($query) {
                $result = $query->row();
                return $result ? (int)$result->unread_count : 0;
            }
            
            return 0;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_header_unread_count_simple: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('check_header_corruption_permission')) {
    function check_header_corruption_permission($user_id) {
        $CI =& get_instance();
        
        try {
            if (!$CI->db->table_exists('tbl_member')) {
                return false;
            }
            
            $CI->db->select('m.m_id, m.m_system, m.grant_user_ref_id');
            $CI->db->from('tbl_member m');
            $CI->db->where('m.m_id', intval($user_id));
            $CI->db->where('m.m_status', '1');
            $query = $CI->db->get();
            
            if (!$query || $query->num_rows() == 0) {
                return false;
            }
            
            $staff_data = $query->row();
            
            // system_admin และ super_admin
            if (in_array($staff_data->m_system, ['system_admin', 'super_admin'])) {
                return true;
            }
            
            // user_admin ที่มี grant 107
            if ($staff_data->m_system === 'user_admin') {
                if (empty($staff_data->grant_user_ref_id)) {
                    return false;
                }
                
                $grant_ids = explode(',', $staff_data->grant_user_ref_id);
                $grant_ids = array_map('trim', $grant_ids);
                
                return in_array('107', $grant_ids);
            }
            
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Error in check_header_corruption_permission: ' . $e->getMessage());
            return false;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo $this->session->userdata('tenant_name'); ?> - ระบบ e-Service</title>
    
    <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    
    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	
	
	
	
	
	
	
    
    <!-- Custom CSS -->
    <style>
        <?php $this->load->view('reports/style'); ?>
        
        /* ===== APPLE-INSPIRED CSS VARIABLES ===== */
        :root {
            /* Apple-inspired soft colors */
            --apple-bg: #fafafa;
            --apple-surface: rgba(255, 255, 255, 0.8);
            --apple-surface-hover: rgba(255, 255, 255, 0.95);
            --apple-border: rgba(0, 0, 0, 0.08);
            --apple-text: #1d1d1f;
            --apple-text-secondary: #86868b;
            --apple-accent: #007aff;
            --apple-accent-hover: #0056cc;
            --apple-success: #30d158;
            --apple-warning: #ff9f0a;
            --apple-danger: #ff3b30;
            --apple-purple: #af52de;
            --apple-pink: #ff2d92;
            --apple-mint: #00c7be;
            
            /* Refined shadows */
            --shadow-soft: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-medium: 0 4px 16px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-strong: 0 8px 32px rgba(0, 0, 0, 0.12), 0 4px 16px rgba(0, 0, 0, 0.06);
            --shadow-notification: 0 12px 40px rgba(0, 0, 0, 0.15), 0 6px 20px rgba(0, 0, 0, 0.08);
            
            /* Apple-style radius */
            --border-radius: 12px;
            --border-radius-large: 20px;
            --border-radius-small: 8px;
            
            /* Backdrop blur */
            --backdrop-blur: blur(20px);
            --backdrop-blur-light: blur(10px);
        }

        /* ===== APPLE-STYLE ANIMATIONS ===== */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes ringBell {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-8deg); }
            75% { transform: rotate(8deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* ===== APPLE-STYLE NAVBAR ===== */
        .navbar-light.bg-white {
            background: var(--apple-surface) !important;
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-bottom: 1px solid var(--apple-border);
            box-shadow: var(--shadow-soft);
        }
        
        .nav-link {
            color: var(--apple-text) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.6rem 1rem !important;
            border-radius: var(--border-radius-small);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            margin: 0 0.2rem;
        }
        
        .nav-link.active {
            background: var(--apple-accent) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(0, 122, 255, 0.3);
        }
        
        .nav-link:hover:not(.active) {
            background: rgba(0, 122, 255, 0.05) !important;
            color: var(--apple-accent) !important;
            transform: translateY(-1px);
        }

        .nav-link .badge {
            background: var(--apple-danger);
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 10px;
            font-weight: 600;
        }

        /* ===== APPLE-STYLE NOTIFICATION BELL ===== */
        .notification-bell-container {
            position: relative;
            margin-right: 15px;
        }

        .notification-bell {
            width: 44px;
            height: 44px;
            background: var(--apple-surface);
            backdrop-filter: var(--backdrop-blur-light);
            -webkit-backdrop-filter: var(--backdrop-blur-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--apple-border);
            position: relative;
        }

        .notification-bell:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: var(--shadow-strong);
            background: var(--apple-surface-hover);
        }

        .notification-bell i {
            font-size: 1.2rem;
            color: var(--apple-text);
            transition: all 0.3s ease;
        }

        .notification-bell:hover i {
            color: var(--apple-accent);
            animation: ringBell 0.5s ease-in-out;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--apple-danger);
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--apple-surface);
            box-shadow: var(--shadow-soft);
            animation: pulse 2s infinite;
        }

        /* ===== APPLE-STYLE NOTIFICATION DROPDOWN ===== */
        .notification-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            width: 360px;
            max-height: 480px;
            background: var(--apple-surface);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-radius: var(--border-radius-large);
            box-shadow: var(--shadow-notification);
            border: 1px solid var(--apple-border);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            z-index: 9999;
        }

        .notification-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .notification-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--apple-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--apple-surface);
        }

        .notification-header h6 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--apple-text);
        }

        .notification-header h6 i {
            color: var(--apple-accent);
            margin-right: 8px;
            font-size: 1rem;
        }

        .notification-count {
            background: var(--apple-accent);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: var(--shadow-soft);
        }

        .btn-mark-all {
            background: var(--apple-surface-hover);
            border: 1px solid var(--apple-border);
            color: var(--apple-accent);
            padding: 4px 8px;
            border-radius: var(--border-radius-small);
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-mark-all:hover {
            background: var(--apple-accent);
            color: white;
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
            border-color: var(--apple-accent);
        }

        /* ===== APPLE-STYLE NOTIFICATION LIST ===== */
        .notification-list {
            max-height: 320px;
            overflow-y: auto;
            padding: 8px 0;
        }

        .notification-item {
            padding: 16px 20px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 2px 8px;
            border-radius: var(--border-radius);
        }

        .notification-item:not(.unread) {
            background: transparent;
        }

        .notification-item:not(.unread):hover {
            background: rgba(0, 122, 255, 0.04);
            transform: translateX(2px);
        }

        .notification-item.unread {
            background: rgba(0, 122, 255, 0.06);
        }

        .notification-item.unread:hover {
            background: rgba(0, 122, 255, 0.08);
            transform: translateX(2px);
        }

        .notification-item.unread::after {
            content: '';
            position: absolute;
            top: 20px;
            right: 16px;
            width: 8px;
            height: 8px;
            background: var(--apple-accent);
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(0, 122, 255, 0.2);
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
            transition: all 0.2s ease;
        }

        .notification-item:not(.unread) .notification-icon {
            background: rgba(134, 134, 139, 0.1);
            color: var(--apple-text-secondary);
        }

        .notification-item.unread .notification-icon {
            background: rgba(0, 122, 255, 0.1);
            color: var(--apple-accent);
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-size: 0.9rem;
            margin-bottom: 4px;
            line-height: 1.3;
            transition: all 0.2s ease;
            font-weight: 600;
            color: var(--apple-text);
        }

        .notification-item.unread .notification-title {
            color: var(--apple-text);
        }

        .notification-message {
            font-size: 0.8rem;
            line-height: 1.4;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            color: var(--apple-text-secondary);
            transition: all 0.2s ease;
        }

        .notification-time {
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--apple-text-secondary);
            transition: all 0.2s ease;
        }

        .notification-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--apple-text-secondary);
        }

        .notification-empty i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        .notification-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--apple-border);
            text-align: center;
            background: var(--apple-surface);
        }

        .view-all-link {
            color: var(--apple-accent);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .view-all-link:hover {
            color: var(--apple-accent-hover);
            text-decoration: none;
            transform: translateX(2px);
        }

        /* ===== APPLE-STYLE UTILITIES ===== */
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
            background: var(--apple-surface);
            backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--apple-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-strong);
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        .notification-card {
            transition: all 0.2s ease;
            background: var(--apple-surface);
            border: 1px solid var(--apple-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
        }

        .notification-card.read {
            opacity: 0.7;
            background: var(--apple-surface);
        }

        .notification-card.unread {
            background: rgba(0, 122, 255, 0.04);
            border-left: 3px solid var(--apple-accent);
        }

        .filter-btn.active {
            background: var(--apple-accent) !important;
            color: white !important;
        }

        .status-changing {
            transition: all 0.4s ease;
            background: rgba(48, 209, 88, 0.06) !important;
        }

        /* Scrollbar styling for webkit browsers */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(134, 134, 139, 0.3);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(134, 134, 139, 0.5);
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .notification-dropdown {
                width: 320px;
                right: -50px;
            }
            
            .notification-bell {
                width: 45px;
                height: 45px;
            }
            
            .notification-bell i {
                font-size: 1.2rem;
            }

            .notification-item {
                margin: 2px 4px;
                padding: 12px 16px;
            }
            
            .notification-unread-indicator {
                width: 10px;
                height: 10px;
                top: 12px;
                right: 12px;
            }
        }

        /* ===== LOADING STATES ===== */
        .notification-loading {
            text-align: center;
            padding: 20px;
            color: #1e40af;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* ===== APPLE-STYLE GLOBAL COMPONENTS ===== */
        body {
            background: var(--apple-bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Kanit', 'Segoe UI', system-ui, sans-serif;
            color: var(--apple-text);
            line-height: 1.6;
            font-weight: 400;
        }

        .page-wrapper {
            padding-top: 76px;
            min-height: 100vh;
            background: var(--apple-bg);
        }

        .container-fluid {
            padding: 24px;
        }

        /* Brand styling */
        .brand-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--apple-text);
        }

        .brand-subtitle {
            font-size: 0.8rem;
            color: var(--apple-text-secondary);
            font-weight: 500;
        }

        /* User info styling */
        .user-info .user-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--apple-text);
        }

        .user-info .user-role {
            font-size: 0.7rem;
            color: var(--apple-text-secondary);
        }

        /* Apple-style dropdown */
        .dropdown-menu {
            background: var(--apple-surface);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--apple-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-strong);
            padding: 8px;
        }

        .dropdown-item {
            color: var(--apple-text);
            font-size: 0.85rem;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: var(--border-radius-small);
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: rgba(0, 122, 255, 0.08);
            color: var(--apple-accent);
        }

        .dropdown-item.text-danger {
            color: var(--apple-danger);
        }

        .dropdown-item.text-danger:hover {
            background: rgba(255, 59, 48, 0.08);
            color: var(--apple-danger);
        }

        .dropdown-header {
            color: var(--apple-text);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .dropdown-divider {
            border-color: var(--apple-border);
        }

        /* ✅ แก้ไข: เพิ่ม debug styles */
        .debug-info {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 11px;
            max-width: 300px;
            z-index: 10000;
        }

        /* ✅ เพิ่ม: Corruption notification styles */
        .notification-item[data-type="corruption"] .notification-icon {
            background: rgba(255, 59, 48, 0.1) !important;
            color: var(--apple-danger) !important;
        }

        .notification-item[data-type="corruption"].unread {
            background: rgba(255, 59, 48, 0.06) !important;
            border-left: 3px solid var(--apple-danger) !important;
        }

        .notification-item[data-type="corruption"].unread::after {
            background: var(--apple-danger) !important;
        }

        /* ✅ เพิ่ม: ESV notification styles */
        .notification-item[data-type="esv"] .notification-icon {
            background: rgba(175, 82, 222, 0.1) !important;
            color: var(--apple-purple) !important;
        }

        .notification-item[data-type="esv"].unread {
            background: rgba(175, 82, 222, 0.06) !important;
            border-left: 3px solid var(--apple-purple) !important;
        }

        .notification-item[data-type="esv"].unread::after {
            background: var(--apple-purple) !important;
        }

        /* ✅ เพิ่ม: Complain notification styles */
        .notification-item[data-type="complain"] .notification-icon {
            background: rgba(255, 159, 10, 0.1) !important;
            color: var(--apple-warning) !important;
        }

        .notification-item[data-type="complain"].unread {
            background: rgba(255, 159, 10, 0.06) !important;
            border-left: 3px solid var(--apple-warning) !important;
        }

        .notification-item[data-type="complain"].unread::after {
            background: var(--apple-warning) !important;
        }
    </style>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <meta name="description" content="ระบบ e-Service - <?php echo $this->session->userdata('tenant_name'); ?>">
    <meta name="keywords" content="รายงาน, สถิติ, ข้อมูล, dashboard">
    <meta name="author" content="<?php echo $this->session->userdata('tenant_name'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo base_url('docs/logo.png'); ?>">
</head>
<body>

<?php 
// ตัวแปรช่วยในการตรวจสอบหน้าปัจจุบัน
$current_uri = uri_string();
$current_controller = $this->router->fetch_class();

// ตรวจสอบว่าอยู่ในหน้ารายงานระบบหรือไม่
$is_system_reports = ($current_controller == 'System_reports' || strpos($current_uri, 'System_reports') !== false);
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid px-4">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="<?php echo site_url('System_reports'); ?>">
            <img src="<?php echo base_url('docs/logo.png'); ?>" alt="Logo" width="45" height="45" class="me-3 rounded-circle shadow-sm">
            <div>
                <div class="brand-title">ระบบ e-Service</div>
                <div class="brand-subtitle"><?php echo $this->session->userdata('tenant_name'); ?></div>
            </div>
        </a>

        <!-- Toggle button for mobile -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_uri == 'home' ? 'active' : ''; ?>" 
                       href="<?php echo site_url('home'); ?>">
                        <i class="fas fa-home me-2"></i>หน้าหลัก
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_uri == 'User/choice' ? 'active' : ''; ?>" 
                       href="<?php echo site_url('User/choice'); ?>">
                        <i class="fas fa-building me-2"></i>สมาร์ทออฟฟิศ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($is_system_reports && ($current_uri == 'System_reports' || $current_uri == 'System_reports/index')) ? 'active' : ''; ?>" 
                       href="<?php echo site_url('System_reports/index'); ?>">
                        <i class="fas fa-chart-bar me-2"></i>ระบบ e-Service
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_uri, 'Pages/q_a') !== false || $current_controller == 'Pages' && strpos($current_uri, 'q_a') !== false) ? 'active' : ''; ?>" 
                       href="<?php echo site_url('Pages/q_a'); ?>">
                        <i class="fas fa-comments me-2"></i>กระทู้ ถาม-ตอบ
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo $is_system_reports && strpos($current_uri, 'notifications') !== false ? 'active' : ''; ?>" 
                       href="<?php echo site_url('System_reports/notifications'); ?>">
                        <i class="fas fa-bell me-2"></i>แจ้งเตือน
                        <?php if ($staff_unread_count > 0): ?>
                            <span class="badge bg-danger ms-1"><?php echo $staff_unread_count > 99 ? '99+' : $staff_unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>

            <!-- Notification Bell + User Menu -->
            <ul class="navbar-nav">
                <!-- Notification Bell -->
                <li class="nav-item">
                    <div class="notification-bell-container">
                        <div class="notification-bell" onclick="toggleStaffNotifications()" title="การแจ้งเตือน">
                            <i class="bi bi-bell-fill"></i>
                            <?php if ($staff_unread_count > 0): ?>
                                <span class="notification-badge"><?php echo $staff_unread_count > 99 ? '99+' : $staff_unread_count; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Notification Dropdown -->
                        <div class="notification-dropdown" id="staffNotificationDropdown">
                            <div class="notification-header">
                                <h6><i class="bi bi-bell me-2"></i>การแจ้งเตือน</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="notification-count"><?php echo $staff_unread_count; ?> ใหม่</span>
                                    <?php if ($staff_unread_count > 0): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary btn-mark-all" 
                                                onclick="markAllStaffNotificationsRead()" 
                                                title="ทำเครื่องหมายทั้งหมดว่าอ่านแล้ว">
                                            <i class="bi bi-check-all"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="notification-list">
                                <?php if (!empty($staff_notifications)): ?>
                                    <?php foreach ($staff_notifications as $notification): ?>
                                        <?php
                                        // ตรวจสอบสถานะการอ่าน
                                        $isUnread = !isset($notification->is_read_by_user) || $notification->is_read_by_user == 0;
                                        
                                        // *** แก้ไข: กำหนดประเภท notification สำหรับสี + เพิ่ม corruption types ***
                                        $notificationType = '';
                                        if (stripos($notification->title, 'พื้นที่') !== false || stripos($notification->title, 'storage') !== false) {
                                            $notificationType = 'storage';
                                        } elseif (stripos($notification->title, 'ร้องเรียน') !== false || stripos($notification->title, 'complain') !== false || 
                                                  in_array($notification->type, ['complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated'])) {
                                            $notificationType = 'complain';
                                        } elseif (stripos($notification->title, 'คำถาม') !== false || stripos($notification->title, 'qa') !== false) {
                                            $notificationType = 'qa';
                                        } elseif (stripos($notification->title, 'ระบบ') !== false || stripos($notification->title, 'system') !== false) {
                                            $notificationType = 'system';
                                        } elseif (stripos($notification->title, 'การทุจริต') !== false || stripos($notification->title, 'corruption') !== false || 
                                                  $notification->reference_table === 'tbl_corruption_reports') {
                                            $notificationType = 'corruption';
                                        } elseif (stripos($notification->title, 'เอกสาร') !== false || stripos($notification->title, 'esv') !== false || 
                                                  in_array($notification->type, ['esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'])) {
                                            $notificationType = 'esv';
                                        }
                                        ?>
                                        <div class="notification-item <?php echo $isUnread ? 'unread' : ''; ?>" 
                                             <?php echo $notificationType ? 'data-type="' . $notificationType . '"' : ''; ?>
                                             onclick="handleStaffNotificationClick(<?php echo $notification->notification_id; ?>, '<?php echo htmlspecialchars($notification->url ?: '#', ENT_QUOTES); ?>')">
                                            <div class="notification-icon">
                                                <i class="<?php echo $notification->icon ?: 'bi bi-bell'; ?>"></i>
                                            </div>
                                            <div class="notification-content">
                                                <div class="notification-title"><?php echo htmlspecialchars($notification->title); ?></div>
                                                <div class="notification-message"><?php echo htmlspecialchars($notification->message); ?></div>
                                                <div class="notification-time">
                                                    <?php 
                                                    if (function_exists('timeago')) {
                                                        echo timeago($notification->created_at);
                                                    } else {
                                                        echo date('d/m/Y H:i', strtotime($notification->created_at));
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="notification-empty">
                                        <i class="bi bi-bell-slash"></i>
                                        <p>ไม่มีการแจ้งเตือน</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="notification-footer">
                                <a href="<?php echo site_url('System_reports/notifications'); ?>" class="view-all-link">
                                    ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                
                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                       role="button" data-bs-toggle="dropdown">
                        <?php $img_path = !empty($user_info->m_img) ? 'docs/img/avatar/' . $user_info->m_img : 'docs/img/avatar/default_user.png'; ?>
                        <img src="<?= base_url($img_path); ?>" alt="User" width="35" height="35" 
                             class="rounded-circle me-2 border">
                        <div class="user-info">
                            <div class="user-name"><?php echo $user_info->m_fname . ' ' . $user_info->m_lname; ?></div>
                            <div class="user-role"><?php echo $user_info->pname; ?></div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-user-circle me-2"></i>
                                <?php echo $user_info->m_fname . ' ' . $user_info->m_lname; ?>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?php echo site_url('User/choice'); ?>">
                                <i class="fas fa-building me-2"></i>สมาร์ทออฟฟิศ
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo site_url('System_admin/user_profile'); ?>">
                                <i class="fas fa-user-edit me-2"></i>แก้ไขโปรไฟล์
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?php echo site_url('User/logout'); ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ✅ เพิ่ม: Debug info สำหรับ development - แสดง Staff Corruption Filter Info (reference_table)
<?php if (ENVIRONMENT === 'development' || $this->session->userdata('m_system') === 'system_admin'): ?>
<div class="debug-info">
    <strong>Staff Debug Info (with Corruption Filter via reference_table):</strong><br>
    User ID: <?php echo $header_debug['user_id']; ?><br>
    Notifications: <?php echo $header_debug['notifications_count']; ?><br>
    Unread: <?php echo $header_debug['unread_count']; ?><br>
    First ID: <?php echo $header_debug['first_notification_id']; ?><br>
    First Type: <?php echo $header_debug['first_notification_type']; ?><br>
    Filter: <?php echo $header_debug['staff_corruption_filter']; ?>
</div>
<?php endif; ?>
-->

<!-- Page Content Wrapper -->
<div class="page-wrapper">
    <div class="container-fluid px-4 py-3">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ✅ แก้ไข: JavaScript สำหรับ Staff Notifications - แก้ปัญหา Auto-refresh + Staff Corruption Filter
let staffNotificationDropdownOpen = false;
let notificationRefreshInterval = null; // เก็บ reference ของ interval

document.addEventListener('DOMContentLoaded', function() {
    //console.log('=== STAFF NOTIFICATION SYSTEM LOADED (WITH CORRUPTION FILTER) ===');
    //console.log('Staff notifications:', <?php echo json_encode($staff_notifications, JSON_UNESCAPED_UNICODE); ?>);
    //console.log('Staff unread count:', <?php echo $staff_unread_count; ?>);
    //console.log('Debug info:', <?php echo json_encode($header_debug, JSON_UNESCAPED_UNICODE); ?>);
    
    // ✅ แก้ไข: ตรวจสอบและแสดงข้อมูล debug
    const notificationBadge = document.querySelector('.notification-badge');
    const notificationCount = document.querySelector('.notification-count');
    const notificationList = document.querySelector('.notification-list');
    
   // console.log('Elements found:');
    //console.log('- Badge element:', notificationBadge ? 'YES' : 'NO');
   // console.log('- Count element:', notificationCount ? 'YES' : 'NO'); 
    //console.log('- List element:', notificationList ? 'YES' : 'NO');
    
    if (notificationBadge) {
       // console.log('Badge text:', notificationBadge.textContent);
    }
    if (notificationCount) {
       // console.log('Count text:', notificationCount.textContent);
    }
    if (notificationList) {
       // console.log('List children:', notificationList.children.length);
    }
    
    // ปิด notification dropdown เมื่อคลิกที่อื่น
    document.addEventListener('click', function(event) {
        const bellContainer = document.querySelector('.notification-bell-container');
        if (!bellContainer.contains(event.target)) {
            closeStaffNotificationDropdown();
        }
    });
    
    // ✅ แก้ไข: รีเฟรช notifications ทุก 30 วินาที - ใช้ setInterval ที่ถูกต้อง
    //console.log('🔄 Setting up notification refresh interval...');
    notificationRefreshInterval = setInterval(function() {
        //console.log('🔄 Auto-refreshing notifications at:', new Date().toLocaleTimeString());
        refreshStaffNotifications();
    }, 30000); // 30 วินาที
    
    //console.log('✅ Notification refresh interval setup completed');
    
    // เพิ่ม keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && staffNotificationDropdownOpen) {
            closeStaffNotificationDropdown();
        }
    });
    
    // ✅ แก้ไข: เพิ่ม CSS โดยไม่ใช้ตัวแปร style ที่อาจจะซ้ำ
    initializeDynamicStyles();
});

// ✅ แก้ไข: ย้าย CSS ไปเป็นฟังก์ชันแยก
function initializeDynamicStyles() {
    // ตรวจสอบว่ามี style element แล้วหรือไม่
    const existingStyle = document.getElementById('notification-dynamic-styles');
    if (existingStyle) {
        return; // มีแล้วไม่ต้องเพิ่มใหม่
    }
    
    const dynamicStyleElement = document.createElement('style');
    dynamicStyleElement.id = 'notification-dynamic-styles';
    dynamicStyleElement.textContent = `
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        .notification-card {
            transition: all 0.3s ease;
        }

        .notification-card.read {
            opacity: 0.7;
            background: #f8f9fa !important;
        }

        .notification-card.unread {
            background: linear-gradient(135deg, rgba(219, 234, 254, 0.7), rgba(191, 219, 254, 0.5)) !important;
            border-left: 4px solid #60a5fa !important;
        }

        .filter-btn.active {
            background: #3b82f6 !important;
            color: white !important;
        }
        
        .status-changing {
            transition: all 0.6s ease;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.05)) !important;
        }
    `;
    
    document.head.appendChild(dynamicStyleElement);
   // console.log('✅ Dynamic styles initialized successfully');
}

function toggleStaffNotifications() {
  //  console.log('🔔 Toggle staff notifications clicked');
    if (staffNotificationDropdownOpen) {
        closeStaffNotificationDropdown();
    } else {
        openStaffNotificationDropdown();
    }
}

function openStaffNotificationDropdown() {
    const dropdown = document.getElementById('staffNotificationDropdown');
    if (dropdown) {
        dropdown.classList.add('show');
        staffNotificationDropdownOpen = true;
       // console.log('✅ Staff notification dropdown opened');
        
        // เพิ่ม animation effect
        setTimeout(() => {
            dropdown.style.transform = 'translateY(0)';
        }, 10);
    } else {
        console.error('❌ Dropdown element not found');
    }
}

function closeStaffNotificationDropdown() {
    const dropdown = document.getElementById('staffNotificationDropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
        staffNotificationDropdownOpen = false;
       // console.log('✅ Staff notification dropdown closed');
    }
}

function handleStaffNotificationClick(notificationId, url) {
   // console.log('🔔 Handle staff notification click:', notificationId, url);
    
    // ทำเครื่องหมายว่าอ่านแล้ว
    markStaffNotificationAsRead(notificationId);
    
    // ปิด dropdown
    closeStaffNotificationDropdown();
    
    // นำทางไปยัง URL
    if (url && url !== '' && url !== '#') {
       // console.log('🌐 Staff navigating to URL:', url);
        
        if (url.startsWith('http') || url.startsWith('//')) {
            window.open(url, '_blank');
        } else {
            // ตรวจสอบว่า URL มี hash fragment หรือไม่
            if (url.includes('#')) {
                const [pagePath, hash] = url.split('#');
                const currentPath = window.location.pathname;
                
                if (currentPath.endsWith(pagePath) || window.location.href.includes(pagePath)) {
                    scrollToElement(hash);
                } else {
                    const fullUrl = url.startsWith('/') ? url : '/' + url;
                    window.location.href = fullUrl;
                }
            } else {
                const fullUrl = url.startsWith('/') ? url : '/' + url;
                window.location.href = fullUrl;
            }
        }
    }
}

function markStaffNotificationAsRead(notificationId) {
   // console.log('📖 Marking notification as read:', notificationId);
    
    // เพิ่ม visual feedback ทันทีก่อนส่ง request
    const notificationItem = document.querySelector(`[onclick*="${notificationId}"]`);
    if (notificationItem && notificationItem.classList.contains('unread')) {
        // เพิ่ม class สำหรับ animation
        notificationItem.classList.add('status-changing');
        
        // เปลี่ยนสีทันทีเพื่อให้ user เห็น feedback
        notificationItem.style.transition = 'all 0.6s ease';
    }
    
    fetch('<?php echo site_url("System_reports/mark_notification_read"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
       // console.log('📖 Mark read response:', data);
        
        if (data.status === 'success') {
            if (notificationItem) {
                // ลบ class 'unread' หลังจาก animation เสร็จ
                setTimeout(() => {
                    notificationItem.classList.remove('unread');
                    notificationItem.classList.remove('status-changing');
                    
                    // เพิ่ม subtle flash effect
                    notificationItem.style.background = 'linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.05))';
                    setTimeout(() => {
                        notificationItem.style.background = '';
                    }, 1000);
                }, 300);
            }
            
            // อัปเดต badge count
            updateStaffNotificationBadge();
            
            //console.log('✅ Staff notification marked as read:', notificationId);
        } else {
            console.error('❌ Failed to mark staff notification as read:', data.message);
            
            // คืนค่าเดิมถ้า error
            if (notificationItem) {
                notificationItem.classList.remove('status-changing');
                notificationItem.style.transition = '';
            }
        }
    })
    .catch(error => {
        console.error('❌ Error marking staff notification as read:', error);
        
        // คืนค่าเดิมถ้า error
        if (notificationItem) {
            notificationItem.classList.remove('status-changing');
            notificationItem.style.transition = '';
        }
    });
}

// ✅ แก้ไข: ปรับปรุง refreshStaffNotifications ให้ทำงานถูกต้อง + Staff Corruption Filter
function refreshStaffNotifications() {
   // console.log('🔄 Refreshing staff notifications with Corruption Filter...');
    
    // ตรวจสอบว่ามี URL ที่ถูกต้องหรือไม่
    const refreshUrl = '<?php echo site_url("System_reports/get_recent_notifications"); ?>';
   // console.log('🔄 Refresh URL:', refreshUrl);
    
    fetch(refreshUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        }
    })
    .then(response => {
       // console.log('🔄 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        //console.log('🔄 Refresh response (with Corruption Filter):', data);
        
        if (data.status === 'success') {
            updateStaffNotificationList(data.notifications);
            updateStaffNotificationBadge(data.unread_count);
          //  console.log('✅ Staff notifications refreshed successfully with Corruption Filter');
            
            // แสดงข้อความสำเร็จใน console
            if (data.notifications && data.notifications.length > 0) {
              //  console.log(`📊 Found ${data.notifications.length} notifications, ${data.unread_count} unread (Corruption Filtered)`);
            }
        } else {
            console.error('❌ Failed to refresh staff notifications:', data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('❌ Error refreshing staff notifications:', error);
        console.error('❌ Error details:', {
            message: error.message,
            stack: error.stack
        });
    });
}

function updateStaffNotificationList(notifications) {
    const notificationList = document.querySelector('.notification-list');
    
    if (!notificationList) {
        console.error('❌ Notification list element not found');
        return;
    }
    
    if (!notifications || notifications.length === 0) {
        notificationList.innerHTML = `
            <div class="notification-empty">
                <i class="bi bi-bell-slash"></i>
                <p>ไม่มีการแจ้งเตือน</p>
            </div>
        `;
       // console.log('📭 No notifications to display');
        return;
    }
    
   // console.log('📝 Updating notification list with', notifications.length, 'items');
    
    let html = '';
    notifications.forEach((notification, index) => {
        const isUnread = !notification.is_read_by_user || notification.is_read_by_user == 0;
        
        // *** แก้ไข: กำหนดประเภท notification + เพิ่ม corruption types ***
        let notificationType = '';
        if (notification.title.includes('พื้นที่') || notification.title.includes('storage')) {
            notificationType = 'storage';
        } else if (notification.title.includes('ร้องเรียน') || notification.title.includes('complain') || 
                   ['complain_assigned', 'complain_status_update_staff', 'complain_response_required', 'complain_escalated'].includes(notification.type)) {
            notificationType = 'complain';
        } else if (notification.title.includes('คำถาม') || notification.title.includes('qa')) {
            notificationType = 'qa';
        } else if (notification.title.includes('ระบบ') || notification.title.includes('system')) {
            notificationType = 'system';
        } else if (notification.title.includes('การทุจริต') || notification.title.includes('corruption') || 
                   ['corruption_report_assigned', 'new_corruption_report_for_staff', 'corruption_status_update_staff', 'corruption_investigation_assigned', 'corruption_response_required'].includes(notification.type)) {
            notificationType = 'corruption';
        } else if (notification.title.includes('เอกสาร') || notification.title.includes('esv') || 
                   ['esv_document_assigned', 'esv_document_review_required', 'esv_document_approval_required'].includes(notification.type)) {
            notificationType = 'esv';
        }
        
        // เพิ่ม delay สำหรับ animation
        const animationDelay = index * 0.1;
        
        html += `
            <div class="notification-item ${isUnread ? 'unread' : ''}" 
                 ${notificationType ? 'data-type="' + notificationType + '"' : ''}
                 onclick="handleStaffNotificationClick(${notification.notification_id}, '${escapeHtml(notification.url || '#')}')"
                 style="animation-delay: ${animationDelay}s;">
                <div class="notification-icon">
                    <i class="${notification.icon || 'bi bi-bell'}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${escapeHtml(notification.title)}</div>
                    <div class="notification-message">${escapeHtml(notification.message)}</div>
                    <div class="notification-time">${timeago(notification.created_at)}</div>
                </div>
                ${isUnread ? '<div class="notification-unread-indicator"></div>' : ''}
            </div>
        `;
    });
    
    notificationList.innerHTML = html;
    
    // เพิ่ม fade-in animation สำหรับ items ใหม่
    const items = notificationList.querySelectorAll('.notification-item');
    items.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
  //  console.log('✅ Notification list updated successfully with Corruption Filter');
}

function updateStaffNotificationBadge(count = null) {
    if (count === null) {
        count = document.querySelectorAll('.notification-item.unread').length;
    }
    
    const badge = document.querySelector('.notification-badge');
    const countElement = document.querySelector('.notification-count');
    const navBadge = document.querySelector('.nav-link .badge');
    
    //console.log('🔢 Updating badge count to:', count);
    
    if (count > 0) {
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        }
        if (countElement) {
            countElement.textContent = count + ' ใหม่';
        }
        if (navBadge) {
            navBadge.textContent = count > 99 ? '99+' : count;
            navBadge.style.display = 'inline';
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
        if (countElement) {
            countElement.textContent = '0 ใหม่';
        }
        if (navBadge) {
            navBadge.style.display = 'none';
        }
    }
}

function scrollToElement(hash) {
   // console.log('Scrolling to hash:', hash);
    
    const targetElement = document.getElementById(hash);
    if (targetElement) {
        targetElement.style.transition = 'all 0.5s ease';
        targetElement.style.background = 'linear-gradient(135deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0.1) 100%)';
        targetElement.style.border = '2px solid rgba(255, 215, 0, 0.5)';
        targetElement.style.transform = 'scale(1.02)';
        targetElement.style.boxShadow = '0 8px 25px rgba(255, 215, 0, 0.3)';
        
        targetElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center',
            inline: 'nearest'
        });
        
        setTimeout(() => {
            targetElement.style.background = '';
            targetElement.style.border = '';
            targetElement.style.transform = '';
            targetElement.style.boxShadow = '';
        }, 3000);
        
       // console.log('✅ Successfully scrolled to element:', hash);
    } else {
        console.warn('❌ Element not found for hash:', hash);
        window.location.href = window.location.pathname + '#' + hash;
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function timeago(dateString) {
    try {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'เมื่อสักครู่';
        if (diff < 3600) return Math.floor(diff / 60) + ' นาทีที่แล้ว';
        if (diff < 86400) return Math.floor(diff / 3600) + ' ชั่วโมงที่แล้ว';
        if (diff < 604800) return Math.floor(diff / 86400) + ' วันที่แล้ว';
        
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        console.warn('Error parsing date:', dateString, e);
        return 'เมื่อสักครู่';
    }
}

// ฟังก์ชันสำหรับทำเครื่องหมายทั้งหมดว่าอ่านแล้ว
function markAllStaffNotificationsRead() {
    if (confirm('ต้องการทำเครื่องหมายการแจ้งเตือนทั้งหมดว่าอ่านแล้วหรือไม่?')) {
        
        // แสดง loading state
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
        button.disabled = true;
        
        fetch('<?php echo site_url("System_reports/mark_all_notifications_read"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
           // console.log('📖 Mark all response:', data);
            
            if (data.status === 'success') {
                // อัปเดต UI
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                
                updateStaffNotificationBadge(0);
               // console.log('✅ All staff notifications marked as read');
                
                // แสดงข้อความสำเร็จ
                showNotificationMessage('ทำเครื่องหมายทั้งหมดสำเร็จ', 'success');
                
                // ซ่อนปุ่มเพราะไม่มี unread แล้ว
                button.style.display = 'none';
                
            } else {
                console.error('❌ Failed to mark all notifications as read:', data.message);
                showNotificationMessage('เกิดข้อผิดพลาด', 'error');
                
                // คืนค่าปุ่ม
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('❌ Error marking all notifications as read:', error);
            showNotificationMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            
            // คืนค่าปุ่ม
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}

// ฟังก์ชันแสดงข้อความแจ้งเตือนชั่วคราว
function showNotificationMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show alert-floating`;
    
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // ลบ alert หลัง 3 วินาที
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 150);
        }
    }, 3000);
}

// ✅ เพิ่ม: ฟังก์ชันที่ขาดหายไป handleNotificationCardClick
function handleNotificationCardClick(notificationId, url) {
   // console.log('📰 Handle notification card click:', notificationId, url);
    
    // ใช้ฟังก์ชันเดียวกันกับ bell notification
    handleStaffNotificationClick(notificationId, url);
}

// ✅ เพิ่ม: ฟังก์ชันสำหรับปุ่ม Mark as Read ในการ์ด
function markNotificationAsReadFromCard(event, notificationId) {
   // console.log('📖 Mark as read from card:', notificationId);
    
    // หยุด event bubbling เพื่อไม่ให้เปิด notification
    event.stopPropagation();
    event.preventDefault();
    
    markStaffNotificationAsRead(notificationId);
    
    // อัปเดต UI ของการ์ด
    const card = event.target.closest('.notification-card');
    if (card) {
        card.classList.remove('unread');
        card.classList.add('read');
        
        // ซ่อนปุ่ม mark as read
        const markReadBtn = card.querySelector('.btn-mark-read');
        if (markReadBtn) {
            markReadBtn.style.display = 'none';
        }
        
        // แสดง visual feedback
        card.style.backgroundColor = '#d1fae5';
        setTimeout(() => {
            card.style.backgroundColor = '';
        }, 2000);
    }
}

// ✅ เพิ่ม: ฟังก์ชันสำหรับลบ notification
function archiveNotificationFromCard(event, notificationId) {
   // console.log('🗑️ Archive notification from card:', notificationId);
    
    // หยุด event bubbling
    event.stopPropagation();
    event.preventDefault();
    
    if (confirm('ต้องการลบการแจ้งเตือนนี้หรือไม่?')) {
        fetch('<?php echo site_url("System_reports/archive_notification"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'notification_id=' + notificationId
        })
        .then(response => response.json())
        .then(data => {
           // console.log('🗑️ Archive response:', data);
            
            if (data.status === 'success') {
                // ลบการ์ดออกจาก DOM
                const card = event.target.closest('.notification-card');
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(-100%)';
                    
                    setTimeout(() => {
                        card.remove();
                    }, 300);
                }
                
                // อัปเดต badge count
                updateStaffNotificationBadge();
                
                showNotificationMessage('ลบการแจ้งเตือนสำเร็จ', 'success');
            } else {
                showNotificationMessage('ไม่สามารถลบได้: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Error archiving notification:', error);
            showNotificationMessage('เกิดข้อผิดพลาดในการลบ', 'error');
        });
    }
}

// ✅ เพิ่ม: ฟังก์ชันสำหรับ filter notifications (สำหรับหน้า notifications)
function filterNotifications(filterType) {
    //console.log('🔍 Filter notifications:', filterType);
    
    const cards = document.querySelectorAll('.notification-card');
    cards.forEach(card => {
        let shouldShow = false;
        
        switch(filterType) {
            case 'all':
                shouldShow = true;
                break;
            case 'unread':
                shouldShow = card.classList.contains('unread');
                break;
            case 'read':
                shouldShow = !card.classList.contains('unread');
                break;
            case 'storage':
                shouldShow = card.dataset.type === 'storage';
                break;
            case 'complain':
                shouldShow = card.dataset.type === 'complain';
                break;
            case 'corruption':
                shouldShow = card.dataset.type === 'corruption';
                break;
            case 'esv':
                shouldShow = card.dataset.type === 'esv';
                break;
            case 'system':
                shouldShow = card.dataset.type === 'system';
                break;
            default:
                shouldShow = true;
        }
        
        if (shouldShow) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });
    
    // อัปเดต active filter button
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.filter === filterType) {
            btn.classList.add('active');
        }
    });
}

// ✅ เพิ่ม: ฟังก์ชันสำหรับ refresh notifications manually
function refreshNotificationsManually() {
    //console.log('🔄 Manual refresh notifications');
    
    const refreshBtn = document.querySelector('.btn-refresh');
    if (refreshBtn) {
        // แสดง loading state
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> กำลังโหลด...';
        refreshBtn.disabled = true;
        
        // เรียก refresh function
        refreshStaffNotifications();
        
        // คืนค่าปุ่มหลังจาก 2 วินาที
        setTimeout(() => {
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
            showNotificationMessage('รีเฟรชข้อมูลสำเร็จ', 'success');
        }, 2000);
    }
}

// ✅ เพิ่ม: ฟังก์ชันสำหรับ mark all notifications as read (สำหรับหน้า notifications)
function markAllNotificationsAsReadFromPage() {
    //console.log('📖 Mark all notifications as read from page');
    
    if (confirm('ต้องการทำเครื่องหมายการแจ้งเตือนทั้งหมดว่าอ่านแล้วหรือไม่?')) {
        markAllStaffNotificationsRead();
        
        // อัปเดต UI ของการ์ดทั้งหมด
        setTimeout(() => {
            const unreadCards = document.querySelectorAll('.notification-card.unread');
            unreadCards.forEach(card => {
                card.classList.remove('unread');
                card.classList.add('read');
                
                const markReadBtn = card.querySelector('.btn-mark-read');
                if (markReadBtn) {
                    markReadBtn.style.display = 'none';
                }
            });
        }, 1000);
    }
}

// ✅ เพิ่ม: ฟังก์ชันสำหรับ pagination
function loadNotificationsPage(page) {
   // console.log('📄 Load notifications page:', page);
    
    // สร้าง URL ใหม่
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    
    // นำทางไปยังหน้าใหม่
    window.location.href = url.toString();
}

// ✅ เพิ่ม: Global error handler สำหรับ notifications
window.addEventListener('error', function(event) {
    if (event.message && event.message.includes('handleNotificationCardClick')) {
        console.warn('⚠️ handleNotificationCardClick error caught and handled');
        event.preventDefault();
        return false;
    }
});

// ✅ แก้ไข: ป้องกัน memory leaks และเก็บ reference ของ interval
window.addEventListener('beforeunload', function() {
    // ยกเลิก interval เมื่อออกจากหน้า
    if (notificationRefreshInterval) {
        clearInterval(notificationRefreshInterval);
      //  console.log('🧹 Cleared notification refresh interval');
    }
});

</script>