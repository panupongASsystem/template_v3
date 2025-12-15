<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ตั้งค่าเริ่มต้นถ้าไม่มีข้อมูลส่งมาจาก Controller
if (!isset($notifications)) $notifications = [];
if (!isset($unread_count)) $unread_count = 0;
if (!isset($total_notifications)) $total_notifications = 0;

// *** แก้ไข: ไม่ต้องกรองข้อมูลใน View เพราะ Controller กรองให้แล้ว ***
$is_system_admin = isset($is_system_admin) ? $is_system_admin : false;
$has_corruption_permission = isset($has_corruption_permission) ? $has_corruption_permission : false;

// *** ลบการกรองซ้ำ - ใช้ข้อมูลที่ Controller ส่งมาตรงๆ ***
// $filtered_notifications = $notifications; // ไม่ต้องกรองแล้ว
// $filtered_unread_count = $unread_count;   // ไม่ต้องนับใหม่
// $filtered_total = $total_notifications;   // ใช้ของเดิม

// *** Debug info สำหรับ Development ***
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development' && $is_system_admin) {
    echo "<!-- Debug Notification Info (No Double Filtering) -->";
    echo "<!-- Total notifications: $total_notifications -->";
    echo "<!-- Unread count: $unread_count -->";
    echo "<!-- Notifications loaded: " . count($notifications) . " -->";
    echo "<!-- Has corruption permission: " . ($has_corruption_permission ? 'YES' : 'NO') . " -->";
    echo "<!-- Is system admin: " . ($is_system_admin ? 'YES' : 'NO') . " -->";
    echo "<!-- Note: No filtering in View - all done in Controller -->";
}

// *** เพิ่มฟังก์ชันที่หายไป (เหมือนเดิม) ***
if (!function_exists('clean_notification_url')) {
    function clean_notification_url($url) {
        if (empty($url)) return $url;
        
        $unwanted_params = [
            '/[&?]gsc\.tab=\d+/',
            '/[&?]utm_[^&#]*/',
            '/[&?]fbclid=[^&#]*/',
            '/[&?]_ga=[^&#]*/',
            '/[&?]_gl=[^&#]*/',
            '/[&?]gclid=[^&#]*/',
        ];
        
        foreach ($unwanted_params as $pattern) {
            $url = preg_replace($pattern, '', $url);
        }
        
        return rtrim($url, '?&');
    }
}

if (!function_exists('build_notification_url')) {
    function build_notification_url($url) {
        if (empty($url) || $url === '#') return '#';
        
        $clean_url = clean_notification_url($url);
        
        if (strpos($clean_url, 'http') === 0) {
            return $clean_url;
        }
        
        // ใช้ base_url() ของ CodeIgniter
        $CI =& get_instance();
        return $CI->config->site_url($clean_url);
    }
}

if (!function_exists('should_open_new_tab')) {
    function should_open_new_tab($url) {
        if (empty($url) || $url === '#') return false;
        
        // ✅ กระทู้ Q&A ให้เปิดหน้าใหม่
        if (strpos($url, 'Pages/q_a') !== false || strpos($url, 'q_a') !== false) {
            return true;
        }
        
        // ✅ Complain detail ไม่เปิดหน้าใหม่ (same tab)
        if (strpos($url, 'System_reports/complain_detail') !== false) {
            return false;
        }
        
        // *** เพิ่ม: Corruption reports ไม่เปิดหน้าใหม่ (same tab) ***
        if (strpos($url, 'corruption') !== false || strpos($url, 'System_reports/corruption_detail') !== false) {
            return false;
        }
        
        // ✅ External links เปิดหน้าใหม่
        if (strpos($url, 'http') === 0) {
            return true;
        }
        
        // ✅ Backend/Admin pages เปิดหน้าใหม่
        if (strpos($url, 'backend') !== false || strpos($url, 'admin') !== false) {
            return true;
        }
        
        // ✅ Default: same tab
        return false;
    }
}

if (!function_exists('timeago') && !function_exists('smart_timeago')) {
    function timeago($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'เมื่อสักครู่';
        if ($time < 3600) return floor($time/60) . ' นาทีที่แล้ว';
        if ($time < 86400) return floor($time/3600) . ' ชั่วโมงที่แล้ว';
        if ($time < 604800) return floor($time/86400) . ' วันที่แล้ว';
        
        return date('d/m/Y H:i', strtotime($datetime));
    }
}
?>



    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    
    <style>
        :root {
            --primary: #457b9d;
            --primary-dark: #1d3557;
            --secondary: #a8dadc;
            --accent: #f1faee;
            --danger: #e63946;
            --warning: #f77f00;
            --info: #219ebc;
            --success: #8ecae6;
            --light: #f8f9fa;
            --dark: #1d3557;
            --border-color: #e2e8f0;
            --shadow-soft: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            --shadow-medium: 0 4px 6px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.06);
            --shadow-strong: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
            --border-radius-large: 16px;
        }

        body {
            font-family: 'Kanit', sans-serif;
            min-height: 100vh;
            color: var(--dark);
            padding-top: 80px;
        }

        .container-main {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 15px;
        }

        /* Header Section */
        .notifications-header {
            background: white;
            padding: 32px;
            border-radius: var(--border-radius-large);
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .notifications-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--info));
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            font-size: 1rem;
            color: #64748b;
            margin: 0;
            line-height: 1.5;
        }

        .notification-stats {
            display: flex;
            gap: 24px;
            justify-content: flex-end;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
        }

        .stat-number.unread {
            color: var(--danger);
        }

        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }

        /* Action Bar */
        .action-bar {
            background: white;
            padding: 20px 24px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        /* *** เพิ่ม Admin Actions Section *** */
        .admin-actions {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #f59e0b;
            border-radius: var(--border-radius);
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-soft);
        }

        .admin-actions h5 {
            color: #92400e;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-danger-admin {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .btn-danger-admin:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            color: white;
        }

        .btn-warning-admin {
            background: linear-gradient(135deg, #d97706, #b45309);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .btn-warning-admin:hover {
            background: linear-gradient(135deg, #b45309, #92400e);
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            color: white;
        }

        /* *** Fixed Modal Styles *** */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1055;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            outline: 0;
            display: none;
        }

        .modal.show {
            display: block !important;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
        }

        .modal-backdrop.show {
            display: block !important;
            opacity: 0.5;
        }

        .modal-backdrop.fade {
            opacity: 0;
            transition: opacity 0.15s linear;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translate(0, -50px);
        }

        .modal.show .modal-dialog {
            transform: none;
        }

        .modal-header.bg-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: white;
        }

        .modal-header.bg-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            color: white;
        }

        .confirm-text {
            background: #f8f9fa;
            border: 2px dashed #6c757d;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            text-align: center;
            color: #495057;
        }

        .confirm-input {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            text-align: center;
            font-size: 1.1rem;
        }

        .modal-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 12px;
            margin: 10px 0;
        }

        .modal-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 12px;
            margin: 10px 0;
        }

        /* Notifications Container */
        .notifications-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Notification Card */
        .notification-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
            display: flex;
            gap: 20px;
            position: relative;
            overflow: hidden;
        }

        .notification-card.unread {
            border-left: 4px solid var(--primary);
            background: linear-gradient(to right, rgba(69, 123, 157, 0.02), white);
        }

        .notification-card:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
        }

        /* Clickable Card Styles */
        .clickable-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .clickable-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(69, 123, 157, 0.05), transparent);
            transition: left 0.6s ease;
            z-index: 1;
        }

        .clickable-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(69, 123, 157, 0.15) !important;
            border-color: rgba(69, 123, 157, 0.3);
        }

        .clickable-card:hover::before {
            left: 100%;
        }

        .clickable-card:active {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 8px 20px rgba(69, 123, 157, 0.2) !important;
        }

        /* Notification Icon */
        .notification-icon-large {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: var(--shadow-soft);
        }

        /* Icon colors by type */
        .icon-qa { 
            color: var(--info); 
            background: linear-gradient(135deg, rgba(33, 158, 188, 0.1), rgba(33, 158, 188, 0.05)); 
        }
        .icon-qa-new { 
            color: var(--success); 
            background: linear-gradient(135deg, rgba(142, 202, 230, 0.1), rgba(142, 202, 230, 0.05)); 
        }
        .icon-qa-reply { 
            color: var(--warning); 
            background: linear-gradient(135deg, rgba(247, 127, 0, 0.1), rgba(247, 127, 0, 0.05)); 
        }
        .icon-system { 
            color: var(--warning); 
            background: linear-gradient(135deg, rgba(247, 127, 0, 0.1), rgba(247, 127, 0, 0.05)); 
        }
        .icon-critical { 
            color: var(--danger); 
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.1), rgba(230, 57, 70, 0.05)); 
        }
        .icon-complain {
            color: var(--danger);
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.1), rgba(230, 57, 70, 0.05));
        }
        .icon-queue {
            color: var(--info);
            background: linear-gradient(135deg, rgba(33, 158, 188, 0.1), rgba(33, 158, 188, 0.05));
        }
        .icon-suggestion {
            color: var(--success);
            background: linear-gradient(135deg, rgba(142, 202, 230, 0.1), rgba(142, 202, 230, 0.05));
        }

        /* Notification Content */
        .notification-content-large {
            flex: 1;
            min-width: 0;
            position: relative;
            z-index: 2;
        }

        .notification-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .notification-title-large {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            line-height: 1.4;
            flex: 1;
        }

        .notification-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .notification-time-large {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }

        .notification-priority {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .priority-low {
            background: rgba(142, 202, 230, 0.1);
            color: var(--success);
        }

        .priority-normal {
            background: rgba(33, 158, 188, 0.1);
            color: var(--info);
        }

        .priority-high {
            background: rgba(247, 127, 0, 0.1);
            color: var(--warning);
        }

        .priority-critical {
            background: rgba(230, 57, 70, 0.1);
            color: var(--danger);
        }

        .notification-message-large {
            font-size: 1rem;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        /* Additional Data */
        .notification-data {
            background: #f8fafc;
            border-radius: var(--border-radius);
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
        }

        .data-item {
            margin-bottom: 6px;
            font-size: 0.9rem;
            color: #475569;
        }

        .data-item:last-child {
            margin-bottom: 0;
        }

        .data-item strong {
            color: var(--dark);
            font-weight: 600;
        }

        .clickable-indicator {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            animation: pulseIndicator 2s infinite;
        }

        @keyframes pulseIndicator {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        /* Notification Actions */
        .notification-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            position: relative;
            z-index: 3;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            box-shadow: var(--shadow-soft);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-outline-success {
            border: 2px solid var(--success);
            color: var(--success);
            background: transparent;
        }

        .btn-outline-success:hover {
            background: var(--success);
            color: white;
        }

        .btn-outline-danger {
            border: 2px solid var(--danger);
            color: var(--danger);
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            color: white;
        }

        /* Empty State */
        .empty-notifications {
            text-align: center;
            padding: 64px 32px;
            background: white;
            border-radius: var(--border-radius-large);
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-color);
        }

        .empty-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 24px;
        }

        .empty-notifications h4 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .empty-notifications p {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        /* Pagination */
        .pagination-container {
            margin-top: 32px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            background: white;
            border-radius: var(--border-radius);
            padding: 12px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-color);
        }

        .pagination .page-link {
            color: var(--primary);
            border: none;
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 6px;
            transition: all 0.2s ease;
            background: transparent;
        }

        .pagination .page-link:hover {
            background: rgba(69, 123, 157, 0.1);
            color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow-soft);
        }

        /* Alert styles */
        .notification-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            box-shadow: var(--shadow-strong);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            background: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-main {
                padding: 20px 10px;
            }

            .notifications-header {
                padding: 24px 20px;
                text-align: center;
            }
            
            .notification-stats {
                justify-content: center;
                margin-top: 16px;
                gap: 16px;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .notification-card {
                padding: 20px;
                flex-direction: column;
                text-align: center;
            }
            
            .notification-icon-large {
                width: 56px;
                height: 56px;
                font-size: 1.25rem;
                margin: 0 auto 12px;
            }
            
            .notification-header-row {
                flex-direction: column;
                text-align: center;
                gap: 12px;
            }
            
            .notification-meta {
                align-items: center;
            }
            
            .notification-actions {
                justify-content: center;
            }

            .action-bar .row {
                text-align: center;
            }
            
            .action-bar .col-md-6:last-child {
                margin-top: 12px;
            }

            .notification-alert {
                left: 10px;
                right: 10px;
                min-width: auto;
            }

            /* *** Admin Actions Responsive *** */
            .admin-actions {
                text-align: center;
            }

            .admin-actions .btn {
                width: 100%;
                margin-bottom: 8px;
            }
        }

        @media (max-width: 480px) {
            .empty-notifications {
                padding: 48px 20px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }

            .stat-number {
                font-size: 1.75rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-card {
            animation: fadeInUp 0.4s ease-out;
            animation-fill-mode: both;
        }

        .notification-card:nth-child(1) { animation-delay: 0.05s; }
        .notification-card:nth-child(2) { animation-delay: 0.1s; }
        .notification-card:nth-child(3) { animation-delay: 0.15s; }
        .notification-card:nth-child(4) { animation-delay: 0.2s; }
        .notification-card:nth-child(5) { animation-delay: 0.25s; }

        /* Animation styles */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>

    <div class="container-main">
        <!-- Header Section -->
        <div class="notifications-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="page-title">
                        <i class="bi bi-bell-fill me-3"></i>การแจ้งเตือนทั้งหมด
                    </h2>
                    <p class="page-subtitle">
                        ดูการแจ้งเตือนและข่าวสารต่างๆ สำหรับเจ้าหน้าที่
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="notification-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $total_notifications ?? 0; ?></span>
                            <div class="stat-label">ทั้งหมด</div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number unread"><?php echo $unread_count ?? 0; ?></span>
                            <div class="stat-label">ยังไม่อ่าน</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- *** Admin Actions Section - แสดงเฉพาะ System Admin *** -->
        <?php if ($is_system_admin): ?>
            <div class="admin-actions">
                <h5>
                    <i class="bi bi-shield-exclamation text-warning"></i>
                    ฟังก์ชันสำหรับ System Admin
                </h5>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-danger-admin" onclick="showClearAllModal()" id="clearAllBtn">
                            <i class="bi bi-trash3 me-2"></i>ล้างการแจ้งเตือนทั้งหมด
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        การล้างข้อมูลจะลบข้อมูลในตาราง tbl_notifications และ tbl_notification_reads อย่างถาวร
                    </small>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="<?php echo site_url('System_reports'); ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>กลับสู่ระบบรายงาน
                    </a>
                </div>
                <div class="col-md-6 text-end">
                    <?php if (($unread_count ?? 0) > 0): ?>
                        <button class="btn btn-success" onclick="markAllAsRead()">
                            <i class="bi bi-check-double me-2"></i>ทำเครื่องหมายทั้งหมดว่าอ่านแล้ว
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <!-- Notifications List -->
        <div class="notifications-container">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <?php 
                    // *** เพิ่ม: ตรวจสอบ Corruption Permission แบบ Double Check ***
                    $is_corruption = (isset($notification->reference_table) && $notification->reference_table === 'tbl_corruption_reports');
                    
                    // ถ้าไม่มีสิทธิ์และเป็น corruption notification ให้ข้าม
                    if (!$has_corruption_permission && $is_corruption) {
                        continue; // ข้ามการแสดงผล corruption notifications
                    }
                    
                    // ตรวจสอบสถานะการอ่านแบบ Individual Read Status
                    $isUnread = !isset($notification->is_read_by_user) || $notification->is_read_by_user == 0;
                    $isRead = !$isUnread;
			
			        if (!isset($notification->is_read_by_user)) {
                        // ถ้าไม่มี is_read_by_user ให้ใช้ is_read (Legacy)
                        $isUnread = !isset($notification->is_read) || $notification->is_read == 0;
                        $isRead = !$isUnread;
                        log_message('warning', "Using legacy is_read field for notification {$notification->notification_id}");
                    }
                    
                    // เตรียม URL สำหรับการคลิก
                    $hasUrl = $notification->url && $notification->url !== '#';
                    $final_url = $hasUrl ? build_notification_url($notification->url) : '';
                    $target = $hasUrl && should_open_new_tab($notification->url) ? '_blank' : '_self';
                    
                    // *** เพิ่ม: กำหนดสีและไอคอนตาม Corruption Type ***
                    $notification_type_class = str_replace('_', '-', $notification->type);
                    if ($is_corruption) {
                        $notification_type_class = 'corruption'; // เพิ่ม class พิเศษสำหรับ corruption
                    }
                    ?>
                    
                    <div class="notification-card <?php echo $isUnread ? 'unread' : 'read'; ?> <?php echo $hasUrl ? 'clickable-card' : ''; ?> <?php echo $is_corruption ? 'corruption-notification' : ''; ?>" 
                         data-notification-id="<?php echo $notification->notification_id; ?>"
                         data-type="<?php echo $notification->type; ?>"
                         data-reference-table="<?php echo $notification->reference_table ?: ''; ?>"
                         <?php if ($hasUrl): ?>
                             onclick="handleNotificationCardClick(<?php echo $notification->notification_id; ?>, '<?php echo htmlspecialchars($final_url, ENT_QUOTES); ?>', '<?php echo $target; ?>', event)"
                             style="cursor: pointer;"
                         <?php endif; ?>>
                        
                        <!-- Notification Icon -->
                        <div class="notification-icon-large icon-<?php echo $notification_type_class; ?>">
                            <i class="<?php echo $notification->icon ?: 'fas fa-bell'; ?>"></i>
                        </div>

                        <!-- Notification Content -->
                        <div class="notification-content-large">
                            <div class="notification-header-row">
                                <h5 class="notification-title-large">
                                    <?php echo htmlspecialchars($notification->title); ?>
                                    <?php if ($is_corruption): ?>
                                        <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">การทุจริต</span>
                                    <?php endif; ?>
                                </h5>
                                <div class="notification-meta">
                                    <span class="notification-time-large">
                                        <?php 
                                        if (function_exists('smart_timeago')) {
                                            echo smart_timeago($notification->created_at);
                                        } elseif (function_exists('timeago')) {
                                            echo timeago($notification->created_at);
                                        } else {
                                            echo date('d/m/Y H:i', strtotime($notification->created_at));
                                        }
                                        ?>
                                    </span>
                                    <span class="notification-priority priority-<?php echo $notification->priority; ?>">
                                        <?php 
                                        $priority_text = [
                                            'low' => 'ต่ำ',
                                            'normal' => 'ปกติ', 
                                            'high' => 'สูง',
                                            'critical' => 'วิกฤต'
                                        ];
                                        echo $priority_text[$notification->priority] ?? 'ปกติ';
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <div class="notification-message-large">
                                <?php echo nl2br(htmlspecialchars($notification->message)); ?>
                            </div>

                            <!-- Additional Data (ถ้ามี) -->
                            <?php 
                            $data = null;
                            if ($notification->data) {
                                if (is_string($notification->data)) {
                                    $data = json_decode($notification->data, true);
                                } elseif (is_object($notification->data) || is_array($notification->data)) {
                                    $data = (array)$notification->data;
                                }
                            }

                            if (!empty($data) && is_array($data)): 
                            ?>
                                <div class="notification-data">
                                    <!-- ไอคอนบอกว่ากดได้ (ถ้ามี URL) -->
                                    <?php if ($hasUrl): ?>
                                        <div class="clickable-indicator">
                                            <span style="font-size: 0.85rem; color: #457b9d; font-weight: 500;">
                                                <i class="bi bi-cursor-fill me-1"></i>คลิกเพื่อดูรายละเอียด
                                            </span>
                                            <i class="bi bi-arrow-right" style="color: #457b9d; font-size: 0.9rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($data['original_topic'])): ?>
                                        <div class="data-item">
                                            <strong>กระทู้:</strong> <?php echo htmlspecialchars($data['original_topic']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($data['replied_by'])): ?>
                                        <div class="data-item">
                                            <strong>ตอบโดย:</strong> <?php echo htmlspecialchars($data['replied_by']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($data['reply_detail']) && !empty($data['reply_detail'])): ?>
                                        <div class="data-item">
                                            <strong>เนื้อหา:</strong> <?php echo htmlspecialchars($data['reply_detail']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($data['author'])): ?>
                                        <div class="data-item">
                                            <strong>ผู้เขียน:</strong> <?php echo htmlspecialchars($data['author']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($data['detail']) && !empty($data['detail'])): ?>
                                        <div class="data-item">
                                            <strong>รายละเอียด:</strong> <?php echo htmlspecialchars($data['detail']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($data['complainant'])): ?>
                                        <div class="data-item">
                                            <strong>ผู้ร้องเรียน:</strong> <?php echo htmlspecialchars($data['complainant']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($data['suggester'])): ?>
                                        <div class="data-item">
                                            <strong>ผู้เสนอแนะ:</strong> <?php echo htmlspecialchars($data['suggester']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($data['requester'])): ?>
                                        <div class="data-item">
                                            <strong>ผู้จองคิว:</strong> <?php echo htmlspecialchars($data['requester']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- *** เพิ่ม: ข้อมูลเฉพาะ Corruption *** -->
                                    <?php if ($is_corruption): ?>
                                        <?php if (isset($data['report_id'])): ?>
                                            <div class="data-item">
                                                <strong>รหัสรายงาน:</strong> <?php echo htmlspecialchars($data['report_id']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($data['reporter'])): ?>
                                            <div class="data-item">
                                                <strong>ผู้รายงาน:</strong> <?php echo htmlspecialchars($data['reporter']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($data['severity'])): ?>
                                            <div class="data-item">
                                                <strong>ระดับความรุนแรง:</strong> 
                                                <span class="badge bg-<?php echo $data['severity'] === 'high' ? 'danger' : ($data['severity'] === 'medium' ? 'warning' : 'info'); ?>">
                                                    <?php 
                                                    $severity_text = [
                                                        'low' => 'ต่ำ',
                                                        'medium' => 'ปานกลาง',
                                                        'high' => 'สูง',
                                                        'critical' => 'วิกฤต'
                                                    ];
                                                    echo $severity_text[$data['severity']] ?? $data['severity'];
                                                    ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($data['investigation_status'])): ?>
                                            <div class="data-item">
                                                <strong>สถานะการสอบสวน:</strong> 
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($data['investigation_status']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            

                            <!-- Action Buttons -->
                            <div class="notification-actions">
                                <?php if ($isUnread): ?>
                                    <button class="btn btn-outline-success btn-sm btn-mark-read" 
                                            onclick="markAsRead(<?php echo $notification->notification_id; ?>); event.stopPropagation();">
                                        <i class="bi bi-check-circle me-1"></i>ทำเครื่องหมายว่าอ่านแล้ว
                                    </button>
                                <?php endif; ?>
                                
                                <!-- *** เพิ่ม: ปุ่มพิเศษสำหรับ Corruption *** -->
                                <?php if ($is_corruption && $has_corruption_permission): ?>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="event.stopPropagation(); alert('ฟีเจอร์การจัดการรายงานการทุจริต');">
                                        <i class="bi bi-shield-exclamation me-1"></i>จัดการรายงาน
                                    </button>
                                <?php endif; ?>
                                
                                <!-- *** เพิ่ม: ปุ่ม Archive สำหรับ Admin *** -->
                                <?php if ($is_system_admin): ?>
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            onclick="archiveNotification(<?php echo $notification->notification_id; ?>); event.stopPropagation();">
                                        <i class="bi bi-archive me-1"></i>เก็บถาวร
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-notifications">
                    <div class="empty-icon">
                        <i class="bi bi-bell-slash"></i>
                    </div>
                    <h4>ไม่มีการแจ้งเตือน</h4>
                    <p>
                        ยังไม่มีการแจ้งเตือนในขณะนี้<br>
                        การแจ้งเตือนใหม่จะปรากฏที่นี่
                        <?php if (!$has_corruption_permission): ?>
                            <br><small class="text-muted">(ไม่รวมการแจ้งเตือนเรื่องการทุจริต)</small>
                        <?php endif; ?>
                    </p>
                    <a href="<?php echo site_url('System_reports'); ?>" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>กลับสู่ระบบรายงาน
                    </a>
                </div>
            <?php endif; ?>
        </div>
		
		

        <!-- Pagination -->
        <?php if (!empty($notifications) && isset($pagination) && $pagination): ?>
            <div class="pagination-container">
                <?php echo $pagination; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- *** เพิ่ม Modal HTML สำหรับ Clear All Notifications *** -->
    <?php if ($is_system_admin): ?>
        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade" id="clearAllBackdrop" style="display: none;"></div>
        
        <!-- Modal -->
        <div class="modal fade" id="clearAllModal" tabindex="-1" aria-labelledby="clearAllModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="clearAllModalLabel">
                            <i class="bi bi-exclamation-diamond me-2"></i>ล้างการแจ้งเตือนทั้งหมด
                        </h5>
                        <button type="button" class="btn-close btn-close-white" onclick="hideClearAllModal()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-danger">
                            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>คำเตือนสำคัญ:</strong>
                            การดำเนินการนี้จะลบการแจ้งเตือนทั้งหมดอย่างถาวร และไม่สามารถกู้คืนได้!
                        </div>
                        
                        <p class="mt-3"><strong>ระบบจะลบข้อมูลต่อไปนี้:</strong></p>
                        <ul class="text-danger">
                            <li><strong>ทุกการแจ้งเตือน</strong> ใน tbl_notifications</li>
                            <li><strong>ทุกสถานะการอ่าน</strong> ใน tbl_notification_reads</li>
                            <li><strong>รีเซ็ต AUTO_INCREMENT</strong> เป็น 1</li>
                        </ul>
                        
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>ข้อมูลที่ลบจะหายไปถาวร!</strong>
                        </div>
                        
                        <div class="confirm-text">
                            กรุณาพิมพ์: DELETE ALL NOTIFICATIONS
                        </div>
                        
                        <input type="text" class="form-control confirm-input" id="confirmAllText" 
                               placeholder="พิมพ์ DELETE ALL NOTIFICATIONS เพื่อยืนยัน" maxlength="24">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="hideClearAllModal()">ยกเลิก</button>
                        <button type="button" class="btn btn-danger" id="confirmClearAll" disabled onclick="executeClearAll()">
                            <i class="bi bi-trash3 me-2"></i>ลบการแจ้งเตือนทั้งหมด
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
// กำหนด boolean สำหรับการตรวจสอบสิทธิ์ Admin
var isSystemAdmin = <?php echo $is_system_admin ? 'true' : 'false'; ?>;

// สร้าง URLs สำหรับ AJAX calls
var siteUrls = {
    clearAllNotifications: '<?php echo site_url("System_reports/clear_all_notifications"); ?>',
    markAllNotificationsRead: '<?php echo site_url("System_reports/mark_all_notifications_read"); ?>',
    markNotificationRead: '<?php echo site_url("System_reports/mark_notification_read"); ?>',
    findTopic: '<?php echo site_url("Api_test/find_topic"); ?>',
    systemReports: '<?php echo site_url("System_reports"); ?>',
    qaPages: '<?php echo site_url("Pages/q_a"); ?>'
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Staff Notifications Page - DOM Content Loaded');
    
    // จัดการการพิมพ์ข้อความยืนยัน
    var confirmAllText = document.getElementById('confirmAllText');
    var confirmClearAllBtn = document.getElementById('confirmClearAll');
    
    if (confirmAllText && confirmClearAllBtn) {
        confirmAllText.addEventListener('input', function() {
            var isValid = this.value.trim() === 'DELETE ALL NOTIFICATIONS';
            confirmClearAllBtn.disabled = !isValid;
            
            if (isValid) {
                confirmClearAllBtn.classList.remove('btn-danger');
                confirmClearAllBtn.classList.add('btn-success');
            } else {
                confirmClearAllBtn.classList.remove('btn-success');
                confirmClearAllBtn.classList.add('btn-danger');
            }
        });
    }
    
    // ตรวจสอบว่ามี hash ใน URL หรือไม่
    if (window.location.hash) {
        var hash = window.location.hash.substring(1);
        setTimeout(function() {
            scrollToElement(hash);
        }, 1000);
    }
    
    // ตรวจสอบว่ามาจาก notification redirect หรือไม่
    var urlParams = new URLSearchParams(window.location.search);
    var fromNotification = urlParams.get('from_notification');
    if (fromNotification && window.location.hash) {
        setTimeout(function() {
            var hash = window.location.hash.substring(1);
            scrollToElementWithHighlight(hash);
        }, 1500);
    }
    
    // Handle URL parameters สำหรับ notification click
    handleNotificationUrlParams();
    
    //console.log('✅ Staff Notifications Page initialized with hash support');
});

// *** ฟังก์ชันแสดง Modal ***
function showClearAllModal() {
   // console.log('🔔 showClearAllModal called');
    
    try {
        var modal = document.getElementById('clearAllModal');
        var backdrop = document.getElementById('clearAllBackdrop');
        var confirmText = document.getElementById('confirmAllText');
        
        if (!modal || !backdrop) {
            console.error('❌ Modal elements not found');
            if (confirm('⚠️ ต้องการล้างการแจ้งเตือนทั้งหมดหรือไม่?\n\nข้อมูลจะหายไปถาวร!')) {
                if (confirm('🔴 ยืนยันอีกครั้ง: ลบการแจ้งเตือนทั้งหมด?')) {
                    clearAllNotifications();
                }
            }
            return;
        }
        
        // แสดง modal
        modal.style.display = 'block';
        backdrop.style.display = 'block';
        modal.style.zIndex = '1055';
        backdrop.style.zIndex = '1050';
        
        // เพิ่ม classes
        setTimeout(function() {
            modal.classList.add('show');
            backdrop.classList.add('show');
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = '17px';
            
            if (confirmText) {
                confirmText.focus();
                confirmText.value = '';
            }
            
            //console.log('✅ Modal shown successfully');
        }, 50);
        
    } catch (error) {
        console.error('Error showing modal:', error);
        if (confirm('⚠️ ต้องการล้างการแจ้งเตือนทั้งหมดหรือไม่?\n\nข้อมูลจะหายไปถาวร!')) {
            if (confirm('🔴 ยืนยันอีกครั้ง: ลบการแจ้งเตือนทั้งหมด?')) {
                clearAllNotifications();
            }
        }
    }
}

function hideClearAllModal() {
    console.log('🔔 hideClearAllModal called');
    
    try {
        var modal = document.getElementById('clearAllModal');
        var backdrop = document.getElementById('clearAllBackdrop');
        var confirmText = document.getElementById('confirmAllText');
        var confirmBtn = document.getElementById('confirmClearAll');
        
        if (modal && backdrop) {
            modal.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            setTimeout(function() {
                modal.style.display = 'none';
                backdrop.style.display = 'none';
            }, 300);
            
            if (confirmText && confirmBtn) {
                confirmText.value = '';
                confirmBtn.disabled = true;
                confirmBtn.classList.remove('btn-success');
                confirmBtn.classList.add('btn-danger');
            }
        }
    } catch (error) {
        console.error('Error hiding modal:', error);
    }
}

function executeClearAll() {
    console.log('🔔 executeClearAll called');
    
    try {
        var confirmText = document.getElementById('confirmAllText');
        
        if (confirmText && confirmText.value.trim() === 'DELETE ALL NOTIFICATIONS') {
          //  console.log('✅ Confirmation text correct, proceeding...');
            
            hideClearAllModal();
            
            setTimeout(function() {
                clearAllNotifications();
            }, 300);
        } else {
            console.log('❌ Confirmation text incorrect:', confirmText ? confirmText.value : 'null');
            showAlert('❌ กรุณาพิมพ์ข้อความยืนยันให้ถูกต้อง', 'warning');
        }
    } catch (error) {
        console.error('Error executing clear all:', error);
        showAlert('❌ เกิดข้อผิดพลาด', 'danger');
    }
}

// Event Listeners
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'clearAllBackdrop') {
        hideClearAllModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modal = document.getElementById('clearAllModal');
        if (modal && modal.classList.contains('show')) {
            hideClearAllModal();
        }
    }
});

// *** ฟังก์ชันล้างการแจ้งเตือนทั้งหมด ***
function clearAllNotifications() {
    console.log('🔔 Starting clearAllNotifications...');
    
    showStaffLoadingMessage('กำลังล้างข้อมูลการแจ้งเตือนทั้งหมด...');
    
    fetch(siteUrls.clearAllNotifications, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=clear_all'
    })
    .then(function(response) {
        console.log('Response status:', response.status);
        
        if (response.status === 404) {
            throw new Error('API endpoint not found (404). Check your URL routing.');
        } else if (response.status === 403) {
            throw new Error('Access forbidden (403). Check your System Admin permissions.');
        } else if (response.status === 500) {
            throw new Error('Internal server error (500). Check server logs.');
        } else if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status + ' (' + response.statusText + ')');
        }
        
        return response.text().then(function(text) {
           // console.log('Raw response text (first 1000 chars):', text.substring(0, 1000));
            
            var trimmedText = text.trim();
            
            if (trimmedText.startsWith('<!DOCTYPE') || trimmedText.startsWith('<html')) {
                console.log('❌ Response starts with HTML, checking for JSON at the end...');
                
                var lastBraceIndex = text.lastIndexOf('}');
                if (lastBraceIndex > -1) {
                    var jsonStart = -1;
                    var braceCount = 0;
                    
                    for (var i = lastBraceIndex; i >= 0; i--) {
                        if (text[i] === '}') {
                            braceCount++;
                        } else if (text[i] === '{') {
                            braceCount--;
                            if (braceCount === 0) {
                                jsonStart = i;
                                break;
                            }
                        }
                    }
                    
                    if (jsonStart > -1) {
                        var possibleJson = text.substring(jsonStart, lastBraceIndex + 1).trim();
                       // console.log('🔍 Found possible JSON at end:', possibleJson.substring(0, 200));
                        
                        try {
                            var data = JSON.parse(possibleJson);
                          //  console.log('✅ Successfully extracted JSON from mixed response:', data);
                            
                            setTimeout(function() {
                                showAlert('⚠️ มี PHP Error เกิดขึ้น แต่การลบข้อมูลสำเร็จแล้ว', 'warning');
                            }, 1000);
                            
                            return data;
                        } catch (parseError) {
                            console.error('❌ Failed to parse extracted JSON:', parseError);
                        }
                    }
                }
                
                if (text.includes('Fatal error') || text.includes('Parse error')) {
                    throw new Error('PHP Fatal/Parse error occurred. Check server error logs.');
                } else if (text.includes('404') && text.includes('Not Found')) {
                    throw new Error('Controller method not found. Check if clear_all_notifications method exists.');
                } else if (text.includes('403') && text.includes('Forbidden')) {
                    throw new Error('Access forbidden. Check your System Admin permissions.');
                } else {
                    throw new Error('Server returned HTML error page instead of JSON. Check server error logs for details.');
                }
            }
            
            if (trimmedText.startsWith('{') && trimmedText.endsWith('}')) {
                try {
                    var data = JSON.parse(trimmedText);
                   // console.log('✅ Successfully parsed clean JSON:', data);
                    return data;
                } catch (parseError) {
                    console.error('❌ JSON parse error:', parseError);
                    throw new Error('Invalid JSON response: ' + parseError.message);
                }
            }
            
            if (trimmedText.includes('Fatal error') || trimmedText.includes('Parse error')) {
                throw new Error('PHP Fatal/Parse error occurred. Check server error logs.');
            }
            
            if (trimmedText.includes('404') && trimmedText.includes('Not Found')) {
                throw new Error('Controller method not found. Check if clear_all_notifications method exists.');
            }
            
            if (trimmedText.includes('403') && trimmedText.includes('Forbidden')) {
                throw new Error('Access forbidden. Check your System Admin permissions.');
            }
            
            console.error('❌ Unknown response format:', trimmedText.substring(0, 200));
            throw new Error('Server returned unexpected content: ' + trimmedText.substring(0, 100));
        });
    })
    .then(function(data) {
        hideStaffLoadingMessage();
        
       // console.log('Success response:', data);
        
        if (data && data.status === 'success') {
            showAlert('✅ ล้างการแจ้งเตือนทั้งหมดสำเร็จ (' + (data.deleted_notifications || 0) + ' การแจ้งเตือน, ' + (data.deleted_reads || 0) + ' สถานะการอ่าน)', 'success');
            
            setTimeout(function() {
                window.location.reload();
            }, 3000);
        } else {
            showAlert('❌ เกิดข้อผิดพลาด: ' + (data ? data.message : 'Unknown error'), 'danger');
        }
    })
    .catch(function(error) {
        hideStaffLoadingMessage();
        console.error('Error:', error);
        
        var errorMessage = 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ';
        
        if (error.message.includes('HTML page instead of JSON')) {
            errorMessage = '❌ ระบบส่ง HTML กลับมา - อาจเป็นปัญหา URL หรือสิทธิ์การเข้าถึง';
        } else if (error.message.includes('404') || error.message.includes('not found')) {
            errorMessage = '❌ ไม่พบ API endpoint - ตรวจสอบ URL หรือ Controller method';
        } else if (error.message.includes('403') || error.message.includes('forbidden')) {
            errorMessage = '❌ ไม่มีสิทธิ์เข้าถึง - ต้องเป็น System Admin';
        } else if (error.message.includes('500') || error.message.includes('server error')) {
            errorMessage = '❌ เกิดข้อผิดพลาดในเซิร์ฟเวอร์ - ตรวจสอบ server logs';
        } else if (error.message.includes('Invalid JSON')) {
            errorMessage = '❌ เซิร์ฟเวอร์ส่งข้อมูล JSON ที่ไม่ถูกต้อง';
        } else if (error.message.includes('Fatal error') || error.message.includes('Parse error')) {
            errorMessage = '❌ เกิด PHP Error - ตรวจสอบ syntax ใน Controller';
        }
        
        showAlert(errorMessage, 'danger');
        
        setTimeout(function() {
            showAlert('💡 แนะนำ: ลองเรียก testControllerConnection() ใน console เพื่อ debug', 'info');
        }, 2000);
    });
}

// *** ฟังก์ชัน handleNotificationCardClick ที่แก้ไขแล้ว ***
function handleNotificationCardClick(notificationId, url, target, event) {
    if (event.target.closest('.notification-actions button')) {
        console.log('Button clicked, ignoring card click');
        return;
    }
    
    console.log('🔔 Notification card clicked:', { notificationId: notificationId, url: url, target: target });
    
    if (url && url !== '' && url !== '#') {
        var isComplainDetail = url.includes('System_reports/complain_detail');
        var isQAPage = url.includes('Pages/q_a') || url.includes('q_a');
        var isExternal = url.startsWith('http');
        
        // *** เพิ่ม: ตรวจสอบว่าเป็น corruption report หรือไม่ ***
        var isCorruptionReport = false;
        var currentCard = event.currentTarget || event.target.closest('.notification-card');
        if (currentCard) {
            var referenceTable = currentCard.getAttribute('data-reference-table');
            var notificationType = currentCard.getAttribute('data-type');
            
            // ตรวจสอบทั้งจาก reference table และ type
            isCorruptionReport = (referenceTable === 'tbl_corruption_reports') || 
                                (notificationType && notificationType.includes('corruption')) ||
                                (url.includes('corruption'));
        }
        
        markAsReadSilent(notificationId).then(function() {
            if (isComplainDetail || isCorruptionReport) {
                // *** รายงานทุจริตและ complain detail เปิดใน tab เดิม ***
                console.log('📋 Opening in same tab (complain or corruption):', url);
                window.location.href = url;
            } else if (isQAPage || isExternal) {
                console.log('🆕 Opening in new tab:', url);
                window.open(url, '_blank');
            } else {
                console.log('📄 Opening in same tab:', url);
                window.location.href = url;
            }
        });
    } else {
        markAsReadSilent(notificationId);
    }
}

// *** ฟังก์ชันทำเครื่องหมายทั้งหมดว่าอ่านแล้ว ***
function markAllAsRead() {
    if (!confirm('ต้องการทำเครื่องหมายการแจ้งเตือนทั้งหมดว่าอ่านแล้วใช่หรือไม่?')) {
        return;
    }

    fetch(siteUrls.markAllNotificationsRead, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.status === 'success') {
            document.querySelectorAll('.notification-card.unread').forEach(function(card) {
                card.classList.remove('unread');
                card.classList.add('read');
                
                var markButton = card.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            });
            
            updateUnreadCount();
            showAlert('ทำเครื่องหมายทั้งหมดสำเร็จ', 'success');
            
            var markAllButton = document.querySelector('button[onclick="markAllAsRead()"]');
            if (markAllButton) {
                markAllButton.style.display = 'none';
            }
        } else {
            showAlert('เกิดข้อผิดพลาด: ' + data.message, 'danger');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
    });
}

// *** ฟังก์ชัน markAsRead สำหรับปุ่ม ***
function markAsRead(notificationId) {
    fetch(siteUrls.markNotificationRead, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.status === 'success') {
            var card = document.querySelector('[data-notification-id="' + notificationId + '"]');
            if (card) {
                card.classList.remove('unread');
                card.classList.add('read');
                
                var markButton = card.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            }
            
            updateUnreadCount();
            showAlert('ทำเครื่องหมายสำเร็จ', 'success');
        } else {
            showAlert('เกิดข้อผิดพลาด: ' + data.message, 'danger');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
    });
}

// *** ฟังก์ชัน markAsReadSilent สำหรับการคลิก card ***
function markAsReadSilent(notificationId) {
    return fetch(siteUrls.markNotificationRead, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.status === 'success') {
            var card = document.querySelector('[data-notification-id="' + notificationId + '"]');
            if (card) {
                card.classList.remove('unread');
                card.classList.add('read');
                
                var markButton = card.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            }
            
            updateUnreadCount();
        }
        return data;
    })
    .catch(function(error) {
        console.error('Error marking as read:', error);
        return { status: 'error', message: error.message };
    });
}

function scrollToElement(hash) {
    //console.log('🎯 Staff scrolling to hash:', hash);
    
    var targetElement = document.getElementById(hash);
    if (targetElement) {
       // console.log('✅ Found target element:', targetElement);
        
        targetElement.style.transition = 'all 0.5s ease';
        targetElement.style.background = 'linear-gradient(135deg, rgba(69, 123, 157, 0.2) 0%, rgba(69, 123, 157, 0.1) 100%)';
        targetElement.style.border = '2px solid rgba(69, 123, 157, 0.5)';
        targetElement.style.transform = 'scale(1.02)';
        targetElement.style.boxShadow = '0 8px 25px rgba(69, 123, 157, 0.3)';
        
        targetElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center',
            inline: 'nearest'
        });
        
        setTimeout(function() {
            showAlert('🎯 พบกระทู้ที่ต้องการแล้ว', 'success');
        }, 500);
        
        setTimeout(function() {
            targetElement.style.background = '';
            targetElement.style.border = '';
            targetElement.style.transform = '';
            targetElement.style.boxShadow = '';
        }, 4000);
        
        //console.log('✅ Successfully scrolled to element:', hash);
        return true;
    } else {
        console.warn('❌ Element not found for hash:', hash);
        
        var relatedElement = findRelatedElement(hash);
        if (relatedElement) {
            console.log('🔍 Found related element:', relatedElement.id);
            scrollToElement(relatedElement.id);
            return true;
        }
        
        showAlert('❌ ไม่พบกระทู้ที่ระบุ กำลังค้นหา...', 'warning');
        
        var topicId = extractTopicIdFromHash(hash);
        if (topicId && !isNaN(topicId)) {
            findTopicPageAndNavigate(topicId, hash);
        } else {
            setTimeout(function() {
                window.location.href = window.location.pathname + window.location.search + '#' + hash;
            }, 2000);
        }
        
        return false;
    }
}

function scrollToElementWithHighlight(hash) {
    console.log('🎯 Staff scrolling to hash with highlight:', hash);
    
    var targetElement = document.getElementById(hash);
    if (targetElement) {
       // console.log('✅ Found target element with highlight:', targetElement);
        
        targetElement.style.transition = 'all 0.5s ease';
        targetElement.style.background = 'linear-gradient(135deg, rgba(255, 215, 0, 0.3) 0%, rgba(255, 215, 0, 0.1) 100%)';
        targetElement.style.border = '3px solid rgba(255, 215, 0, 0.7)';
        targetElement.style.transform = 'scale(1.03)';
        targetElement.style.boxShadow = '0 12px 30px rgba(255, 215, 0, 0.4)';
        
        targetElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center',
            inline: 'nearest'
        });
        
        setTimeout(function() {
            showAlert('🎯 พบกระทู้จากการแจ้งเตือนแล้ว!', 'success');
        }, 500);
        
        setTimeout(function() {
            targetElement.style.background = '';
            targetElement.style.border = '';
            targetElement.style.transform = '';
            targetElement.style.boxShadow = '';
        }, 5000);
        
       // console.log('✅ Successfully scrolled to element with highlight:', hash);
        return true;
    } else {
        return scrollToElement(hash);
    }
}

function findRelatedElement(hash) {
    //console.log('🔍 Searching for related element:', hash);
    
    var patterns = [
        hash,
        'comment-' + hash,
        'reply-' + hash,
        'topic-' + hash,
        'post-' + hash
    ];
    
    var commentMatch = hash.match(/comment-(\d+)/);
    if (commentMatch) {
        patterns.push(commentMatch[1]);
    }
    
    var replyMatch = hash.match(/reply-(\d+)/);
    if (replyMatch) {
        var replyElements = document.querySelectorAll('[id^="reply-"]');
        for (var i = 0; i < replyElements.length; i++) {
            var replyEl = replyElements[i];
            var parentComment = replyEl.closest('[id^="comment-"]');
            if (parentComment) {
                patterns.push(parentComment.id);
            }
        }
    }
    
    for (var j = 0; j < patterns.length; j++) {
        var element = document.getElementById(patterns[j]);
        if (element) {
            //console.log('✅ Found related element with pattern:', patterns[j]);
            return element;
        }
    }
    
    console.log('❌ No related element found');
    return null;
}

function extractTopicIdFromHash(hash) {
    if (!hash) return null;
    
    var patterns = [
        /comment-(\d+)/,
        /reply-(\d+)/,
        /topic-(\d+)/,
        /post-(\d+)/,
        /^(\d+)$/
    ];
    
    for (var i = 0; i < patterns.length; i++) {
        var match = hash.match(patterns[i]);
        if (match && match[1]) {
            return parseInt(match[1]);
        }
    }
    
    return null;
}

function findTopicPageAndNavigate(topicId, hash) {
    //console.log('🔍 Finding page for topic ID:', topicId);
    
    showStaffLoadingMessage('กำลังค้นหากระทู้...');
    
    fetch(siteUrls.findTopic, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'topic_id=' + topicId
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
       // console.log('📊 API Response:', data);
        hideStaffLoadingMessage();
        
        if (data.success && data.page) {
           // console.log('✅ Topic found on page ' + data.page + ', navigating...');
            
            var newUrl = siteUrls.qaPages + '?page=' + data.page + '&from_notification=1#' + hash;
            
            console.log('🚀 Navigating to:', newUrl);
            window.location.href = newUrl;
            
        } else {
            console.error('❌ Topic not found:', data.message || 'Unknown error');
            showStaffErrorMessage('ไม่พบกระทู้ที่ระบุ');
        }
    })
    .catch(function(error) {
        console.error('🚨 Error finding topic page:', error);
        hideStaffLoadingMessage();
        showStaffErrorMessage('เกิดข้อผิดพลาดในการค้นหา');
    });
}

function showStaffLoadingMessage(message) {
    try {
        hideStaffLoadingMessage();
        
        var loadingDiv = document.createElement('div');
        loadingDiv.id = 'staff-loading-message';
        loadingDiv.style.cssText = [
            'position: fixed',
            'top: 20px',
            'right: 20px',
            'z-index: 9999',
            'background: linear-gradient(135deg, #457b9d, #1d3557)',
            'color: white',
            'padding: 1rem 1.5rem',
            'border-radius: 10px',
            'box-shadow: 0 4px 15px rgba(69, 123, 157, 0.3)',
            'font-weight: 500',
            'display: flex',
            'align-items: center',
            'gap: 0.5rem',
            'animation: slideInRight 0.3s ease',
            'max-width: 300px'
        ].join(';');
        
        loadingDiv.innerHTML = [
            '<div class="spinner-border spinner-border-sm" role="status">',
            '<span class="visually-hidden">Loading...</span>',
            '</div>',
            '<span>' + message + '</span>'
        ].join('');
        
        document.body.appendChild(loadingDiv);
       // console.log('Loading message shown:', message);
    } catch (error) {
        console.error('Error showing loading message:', error);
    }
}

function hideStaffLoadingMessage() {
    try {
        var loadingDiv = document.getElementById('staff-loading-message');
        if (loadingDiv && loadingDiv.parentNode) {
            loadingDiv.remove();
        }
        console.log('Loading message hidden');
    } catch (error) {
        console.error('Error hiding loading message:', error);
    }
}

function showStaffErrorMessage(message) {
    try {
        var errorDiv = document.createElement('div');
        errorDiv.id = 'staff-error-message';
        errorDiv.style.cssText = [
            'position: fixed',
            'top: 20px',
            'right: 20px',
            'z-index: 9999',
            'background: linear-gradient(135deg, #e63946, #dc3545)',
            'color: white',
            'padding: 1rem 1.5rem',
            'border-radius: 10px',
            'box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3)',
            'font-weight: 500',
            'max-width: 300px',
            'animation: slideInRight 0.3s ease'
        ].join(';');
        
        errorDiv.innerHTML = '<span>' + message + '</span>';
        document.body.appendChild(errorDiv);
        
        setTimeout(function() {
            if (errorDiv && errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 4000);
        
        console.log('Error message shown:', message);
    } catch (error) {
        console.error('Error showing error message:', error);
        alert('Error: ' + message);
    }
}

function handleNotificationUrlParams() {
    var urlParams = new URLSearchParams(window.location.search);
    var notificationId = urlParams.get('notification_id');
    var action = urlParams.get('action');
    
    if (notificationId && action === 'mark_read') {
       // console.log('📥 Processing notification URL params:', { notificationId: notificationId, action: action });
        
        markAsReadSilent(notificationId);
        
        var newUrl = window.location.pathname + window.location.hash;
        window.history.replaceState({}, document.title, newUrl);
        
        setTimeout(function() {
            showAlert('📋 การแจ้งเตือนถูกทำเครื่องหมายว่าอ่านแล้ว', 'info');
        }, 1000);
    }
}

function updateUnreadCount() {
    var unreadCards = document.querySelectorAll('.notification-card.unread');
    var unreadCountElement = document.querySelector('.stat-number.unread');
    
    if (unreadCountElement) {
        unreadCountElement.textContent = unreadCards.length;
    }
    
    if (unreadCards.length === 0) {
        var markAllButton = document.querySelector('button[onclick="markAllAsRead()"]');
        if (markAllButton) {
            markAllButton.style.display = 'none';
        }
    }
}

function showAlert(message, type) {
    if (!type) type = 'info';
    
    try {
        var existingAlerts = document.querySelectorAll('.notification-alert');
        existingAlerts.forEach(function(alert) {
            if (alert.parentNode) {
                alert.remove();
            }
        });
        
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show notification-alert';
        alertDiv.innerHTML = [
            message,
            '<button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close"></button>'
        ].join('');
        
        document.body.appendChild(alertDiv);
        
        setTimeout(function() {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
        
        console.log('Alert shown:', message, type);
    } catch (error) {
        console.error('Error showing alert:', error);
        alert(message);
    }
}

// Smooth scroll to top when clicking pagination
document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination .page-link')) {
        setTimeout(function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 100);
    }
});

// Debug functions
//console.log('🔧 Staff Hash Navigation Functions Available:');
//console.log('- scrollToElement("comment-90") - scroll ไปที่ element');
//console.log('- findRelatedElement("90") - ค้นหา element ที่เกี่ยวข้อง');

if (isSystemAdmin) {
    console.log('🔧 Admin Functions Available (System Admin only):');
    console.log('- showClearAllModal() - แสดง Modal ล้างข้อมูล');
    console.log('- hideClearAllModal() - ซ่อน Modal');
    console.log('- clearAllNotifications() - ล้างการแจ้งเตือนทั้งหมด');
    console.log('⚠️  Admin functions use Bootstrap modal elements');
} else {
    console.log('❌ Admin functions not available (not System Admin)');
}
    </script>