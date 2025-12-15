<?php

// ฟังก์ชันทำความสะอาด URL - ลบ parameters ที่ไม่ต้องการ
function clean_notification_url($url) {
    if (empty($url)) {
        return $url;
    }
    
    // ลบ Google Search Console และ tracking parameters
    $unwanted_params = [
        '/[&?]gsc\.tab=\d+/',           // Google Search Console
        '/[&?]utm_[^&#]*/',             // Google Analytics UTM
        '/[&?]fbclid=[^&#]*/',          // Facebook Click ID
        '/[&?]_ga=[^&#]*/',             // Google Analytics
        '/[&?]_gl=[^&#]*/',             // Google Linker
        '/[&?]gclid=[^&#]*/',           // Google Ads Click ID
    ];
    
    foreach ($unwanted_params as $pattern) {
        $url = preg_replace($pattern, '', $url);
    }
    
    // ลบ ? หรือ & ที่อาจเหลืออยู่ท้าย URL
    $url = rtrim($url, '?&');
    
    return $url;
}

// ฟังก์ชันสำหรับจัดการ URL ในการแจ้งเตือน
function build_notification_url($url) {
    if (empty($url) || $url === '#') {
        return '#';
    }
    
    // ทำความสะอาด URL ก่อน
    $clean_url = clean_notification_url($url);
    
    // ถ้าเป็น URL เต็มแล้ว (มี http/https)
    if (strpos($clean_url, 'http') === 0) {
        return $clean_url;
    }
    
    // ถ้าเป็น relative URL ให้เพิ่ม base_url โดยตรง
    return base_url($clean_url);
}

// ฟังก์ชันตรวจสอบว่าควรเปิดใน tab ใหม่หรือไม่
function should_open_new_tab($url) {
    // *** แก้ไข: การแจ้งเตือนทั้งหมดให้ใช้ tab เดิม (ไม่เปิด tab ใหม่) ***
    return false;
    
    /*
    // *** เก็บ logic เดิมไว้สำหรับอ้างอิง ***
    // ถ้าเป็น external URL (มี http/https)
    if (strpos($url, 'http') === 0) {
        return true;
    }
    
    // ถ้าเป็น internal URL แต่เป็น backend
    if (strpos($url, 'backend') !== false || strpos($url, 'admin') !== false) {
        return true;
    }
    
    // ถ้าเป็น Queue/my_queue_detail/, complaints_public/detail/ หรือ Corruption/my_report_detail/ ไม่ต้องเปิดหน้าใหม่
    if (strpos($url, 'Queue/my_queue_detail/') !== false || 
        strpos($url, 'complaints_public/detail/') !== false ||
        strpos($url, 'Corruption/my_report_detail/') !== false) {
        return false;
    }
    
    return false;
    */
}
?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #10b981;
            --accent: #f59e0b;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --success: #10b981;
            --light: #f8fafc;
            --dark: #1e293b;
            --border-color: #e2e8f0;
            --shadow-soft: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            --shadow-medium: 0 4px 6px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.06);
            --shadow-strong: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
            --border-radius-large: 16px;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            color: var(--dark);
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
            background: linear-gradient(to right, rgba(37, 99, 235, 0.02), white);
        }

        .notification-card.unread::before {
            display: none;
        }

        .notification-card:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
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
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05)); 
        }
        .icon-qa-new { 
            color: var(--success); 
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05)); 
        }
        .icon-qa-reply { 
            color: var(--accent); 
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05)); 
        }
        .icon-complain { 
            color: var(--danger); 
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05)); 
        }
        .icon-queue { 
            color: var(--info); 
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05)); 
        }
        .icon-queue-reminder { 
            color: var(--warning); 
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05)); 
        }
        .icon-system { 
            color: var(--warning); 
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05)); 
        }
        .icon-critical { 
            color: var(--danger); 
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05)); 
        }
        /* *** เพิ่ม: สีไอคอนสำหรับการทุจริต *** */
        .icon-corruption-report-confirmation,
        .icon-new-corruption-report,
        .icon-corruption-status-update,
        .icon-corruption-assigned,
        .icon-corruption-response-added { 
            color: #dc2626; 
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(220, 38, 38, 0.05)); 
        }

        /* Notification Content */
        .notification-content-large {
            flex: 1;
            min-width: 0;
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
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .priority-normal {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .priority-high {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .priority-critical {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .notification-message-large {
            font-size: 1rem;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        /* Additional Data - แก้ไขสีเป็นสีขาว */
        .notification-data {
            background: #ffffff; /* เปลี่ยนจาก #f8fafc เป็นสีขาว */
            border-radius: var(--border-radius);
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); /* เพิ่ม shadow เล็กน้อย */
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

        /* Notification Actions */
        .notification-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
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
            background: rgba(37, 99, 235, 0.1);
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

        /* เพิ่ม CSS สำหรับ clickable notification card */
        .clickable-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .clickable-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.05), transparent);
            transition: left 0.6s ease;
            z-index: 1;
        }

        .clickable-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.15) !important;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .clickable-card:hover::before {
            left: 100%;
        }

        .clickable-card:active {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2) !important;
        }

        .clickable-card .notification-actions {
            position: relative;
            z-index: 2;
        }

        .clickable-card .notification-content-large {
            position: relative;
            z-index: 1;
        }

        .clickable-indicator {
            animation: pulseIndicator 2s infinite;
        }

        @keyframes pulseIndicator {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .clickable-card:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
        }

        .clickable-card.unread:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, #ffffff 100%);
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
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

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
                        ดูการแจ้งเตือนและข่าวสารต่างๆ ที่เกี่ยวข้องกับบัญชีของคุณ
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

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="row align-items-center">
                <div class="col-md-6">
                    
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
        <div class="notifications-container">
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notification): ?>
            <?php 
            // *** เพิ่ม: ตรวจสอบสถานะการอ่านแบบ Individual Read Status ***
            $isUnread = !isset($notification->is_read_by_user) || $notification->is_read_by_user == 0;
            $isRead = !$isUnread;
            
            // *** แก้ไข FINAL: เตรียม URL สำหรับการคลิก - เพิ่ม corruption ***
            $hasUrl = false;
            $final_url = '';
            $target = '_self';

            if ($notification->type === 'complain') {
                // *** สำหรับ complain ให้ไปที่ complaints_public/detail/{case_id} ***
                $data = null;
                if ($notification->data) {
                    if (is_string($notification->data)) {
                        $data = json_decode($notification->data, true);
                    } elseif (is_object($notification->data) || is_array($notification->data)) {
                        $data = (array)$notification->data;
                    }
                }
                
                // หา case_id หรือ complain_id
                $case_id = null;
                if ($data && is_array($data)) {
                    $case_id = $data['complain_id'] ?? $data['case_id'] ?? $notification->reference_id ?? null;
                }
                
                if ($case_id) {
                    $final_url = site_url("complaints_public/detail/{$case_id}");
                    $hasUrl = true;
                    $target = '_self';
                }
            } elseif (in_array($notification->type, [
                'corruption_report_confirmation',
                'new_corruption_report', 
                'corruption_status_update', 
                'corruption_assigned', 
                'corruption_response_added'
            ])) {
                // *** เพิ่ม: สำหรับ corruption ให้ไปที่ Corruption/my_report_detail/{report_id} ***
                $data = null;
                if ($notification->data) {
                    if (is_string($notification->data)) {
                        $data = json_decode($notification->data, true);
                    } elseif (is_object($notification->data) || is_array($notification->data)) {
                        $data = (array)$notification->data;
                    }
                }
                
                // หา report_id หรือ corruption_id
                $report_id = null;
                if ($data && is_array($data)) {
                    $report_id = $data['report_id'] ?? $data['corruption_id'] ?? $data['id'] ?? $notification->reference_id ?? null;
                }
                
                if ($report_id) {
                    $final_url = site_url("Corruption/my_report_detail/{$report_id}");
                    $hasUrl = true;
                    $target = '_self';
                }
            } else {
                // *** สำหรับ type อื่นๆ ใช้ URL เดิม ***
                $hasUrl = $notification->url && $notification->url !== '#';
                $final_url = $hasUrl ? build_notification_url($notification->url) : '';
                $target = $hasUrl && should_open_new_tab($notification->url) ? '_blank' : '_self';
            }
            ?>
            
            <!-- *** แก้ไข: เพิ่มการคลิกได้ที่กล่องทั้งใบ *** -->
            <div class="notification-card <?php echo $isUnread ? 'unread' : 'read'; ?> <?php echo $hasUrl ? 'clickable-card' : ''; ?>" 
                 data-notification-id="<?php echo $notification->notification_id; ?>"
                 <?php if ($hasUrl): ?>
                     onclick="handleNotificationCardClick(<?php echo $notification->notification_id; ?>, '<?php echo htmlspecialchars($final_url, ENT_QUOTES); ?>', '<?php echo $target; ?>', event)"
                     style="cursor: pointer;"
                 <?php endif; ?>>
                
                <!-- Notification Icon -->
                <div class="notification-icon-large icon-<?php echo str_replace('_', '-', $notification->type); ?>">
                    <i class="<?php echo $notification->icon ?: 'fas fa-bell'; ?>"></i>
                </div>

                <!-- Notification Content -->
                <div class="notification-content-large">
                    <div class="notification-header-row">
                        <h5 class="notification-title-large">
                            <?php echo htmlspecialchars($notification->title); ?>
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
                            <!-- เพิ่มไอคอนบอกว่ากดได้ (ถ้ามี URL) -->
                            <?php if ($hasUrl): ?>
                                <div class="clickable-indicator" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0;">
                                    <span style="font-size: 0.85rem; color: #3b82f6; font-weight: 500;">
                                        <i class="bi bi-cursor-fill me-1"></i>
                                        <?php if ($notification->type === 'complain'): ?>
                                            คลิกเพื่อดูรายละเอียดเรื่องร้องเรียน
                                        <?php elseif (in_array($notification->type, [
                                            'corruption_report_confirmation',
                                            'new_corruption_report', 
                                            'corruption_status_update', 
                                            'corruption_assigned', 
                                            'corruption_response_added'
                                        ])): ?>
                                            คลิกเพื่อดูรายละเอียดการร้องเรียนการทุจริต
                                        <?php elseif (strpos($final_url, 'Queue/my_queue_detail/') !== false): ?>
                                            คลิกเพื่อดูรายละเอียดคิว
                                        <?php else: ?>
                                            คลิกเพื่อดูรายละเอียด
                                        <?php endif; ?>
                                    </span>
                                    <i class="bi bi-arrow-right" style="color: #3b82f6; font-size: 0.9rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- *** เพิ่ม: แสดงข้อมูลเฉพาะ corruption ***-->
                            <?php if (in_array($notification->type, [
                                'corruption_report_confirmation',
                                'new_corruption_report', 
                                'corruption_status_update', 
                                'corruption_assigned', 
                                'corruption_response_added'
                            ])): ?>
                                <?php if (isset($data['report_id']) || isset($data['corruption_id']) || isset($data['id'])): ?>
                                    <div class="data-item">
                                        <strong>เลขที่การร้องเรียน:</strong> 
                                        <?php echo htmlspecialchars($data['report_id'] ?? $data['corruption_id'] ?? $data['id']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['subject']) || isset($data['title'])): ?>
                                    <div class="data-item">
                                        <strong>หัวข้อ:</strong> 
                                        <?php echo htmlspecialchars($data['subject'] ?? $data['title']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['description']) && !empty($data['description'])): ?>
                                    <div class="data-item">
                                        <strong>รายละเอียด:</strong> 
                                        <?php echo htmlspecialchars(mb_substr($data['description'], 0, 100) . '...'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['status'])): ?>
                                    <div class="data-item">
                                        <strong>สถานะ:</strong> 
                                        <?php echo htmlspecialchars($data['status']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['reporter_name'])): ?>
                                    <div class="data-item">
                                        <strong>ผู้ร้องเรียน:</strong> 
                                        <?php echo htmlspecialchars($data['reporter_name']); ?>
                                    </div>
                                <?php endif; ?>
                            <!-- *** เพิ่ม: แสดงข้อมูลเฉพาะ complain ***-->
                            <?php elseif ($notification->type === 'complain'): ?>
                                <?php if (isset($data['complain_id'])): ?>
                                    <div class="data-item">
                                        <strong>เลขที่เรื่อง:</strong> <?php echo htmlspecialchars($data['complain_id']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['topic'])): ?>
                                    <div class="data-item">
                                        <strong>หัวข้อ:</strong> <?php echo htmlspecialchars($data['topic']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['detail']) && !empty($data['detail'])): ?>
                                    <div class="data-item">
                                        <strong>รายละเอียด:</strong> <?php echo htmlspecialchars(mb_substr($data['detail'], 0, 100) . '...'); ?>
                                    </div>
                                <?php endif; ?>
                            <!-- *** เพิ่ม: แสดงข้อมูลเฉพาะ queue ***-->
                            <?php elseif (strpos($final_url, 'Queue/my_queue_detail/') !== false): ?>
                                <?php if (isset($data['queue_id'])): ?>
                                    <div class="data-item">
                                        <strong>เลขที่คิว:</strong> <?php echo htmlspecialchars($data['queue_id']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['service_name'])): ?>
                                    <div class="data-item">
                                        <strong>บริการ:</strong> <?php echo htmlspecialchars($data['service_name']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['appointment_date'])): ?>
                                    <div class="data-item">
                                        <strong>วันที่นัดหมาย:</strong> <?php echo htmlspecialchars($data['appointment_date']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['appointment_time'])): ?>
                                    <div class="data-item">
                                        <strong>เวลานัดหมาย:</strong> <?php echo htmlspecialchars($data['appointment_time']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($data['status'])): ?>
                                    <div class="data-item">
                                        <strong>สถานะ:</strong> <?php echo htmlspecialchars($data['status']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- สำหรับ type อื่นๆ -->
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
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="notification-actions">
                        <!-- *** แก้ไข: ใช้ $isUnread แทน $notification->is_read *** -->
                        <?php if ($isUnread): ?>
                            <button class="btn btn-outline-success btn-sm" 
                                    onclick="markAsRead(<?php echo $notification->notification_id; ?>); event.stopPropagation();">
                                <i class="bi bi-check-circle me-1"></i>ทำเครื่องหมายว่าอ่านแล้ว
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($hasUrl): ?>
                            <button class="btn btn-primary btn-sm" 
                                    onclick="window.location.href='<?php echo $final_url; ?>'; event.stopPropagation();">
                                <i class="bi bi-eye me-1"></i>
                                <?php if ($notification->type === 'complain'): ?>
                                    ดูรายละเอียดเรื่องร้องเรียน
                                <?php elseif (in_array($notification->type, [
                                    'corruption_report_confirmation',
                                    'new_corruption_report', 
                                    'corruption_status_update', 
                                    'corruption_assigned', 
                                    'corruption_response_added'
                                ])): ?>
                                    ดูรายละเอียดการร้องเรียนการทุจริต
                                <?php elseif (strpos($final_url, 'Queue/my_queue_detail/') !== false): ?>
                                    ดูรายละเอียดคิว
                                <?php else: ?>
                                    ดูรายละเอียด
                                <?php endif; ?>
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
            <p>คุณยังไม่มีการแจ้งเตือนในขณะนี้<br>การแจ้งเตือนใหม่จะปรากฏที่นี่</p>
            <a href="<?php echo site_url('service_systems'); ?>" class="btn btn-primary">
                <i class="bi bi-house me-2"></i>กลับสู่หน้าหลัก
            </a>
        </div>
    <?php endif; ?>
</div>
		
		</div>

<!-- Pagination -->
<?php if (!empty($notifications) && isset($pagination) && $pagination): ?>
    <div class="pagination-container">
        <?php echo $pagination; ?>
    </div>
<?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
		
function handleNotificationCardClick(notificationId, url, target, event) {
    // ตรวจสอบว่าคลิกที่ปุ่มหรือไม่
    if (event.target.closest('.notification-actions button')) {
        console.log('Button clicked, ignoring card click');
        return;
    }
    
    console.log('🔔 Notification card clicked:', { notificationId, url, target });
    
    // ทำเครื่องหมายว่าอ่านแล้ว
    markAsReadSilent(notificationId);
    
    // เปิด URL
    if (url && url !== '' && url !== '#') {
        if (target === '_blank') {
            window.open(url, '_blank');
        } else {
            // *** แก้ไข: สำหรับ complain, corruption และ queue ให้ไปหน้าใหม่เลย ***
            if (url.includes('complaints_public/detail/')) {
                console.log('🚨 Navigating to complain detail page:', url);
                window.location.href = url;
            } else if (url.includes('Corruption/my_report_detail/')) {
                console.log('🚔 Navigating to corruption detail page:', url);
                window.location.href = url;
            } else if (url.includes('Queue/my_queue_detail/')) {
                console.log('📋 Navigating to queue detail page:', url);
                window.location.href = url;
            } else if (url.includes('#')) {
                // สำหรับ URL ที่มี hash (เช่น Q&A)
                const [pagePath, hash] = url.split('#');
                
                console.log('🔗 Processing URL with hash:', { pagePath, hash });
                
                if (pagePath.includes('q_a') || pagePath.includes('Pages/q_a')) {
                    console.log('📍 Navigating to Q&A page with notification tracking');
                    
                    const separator = pagePath.includes('?') ? '&' : '?';
                    const newUrl = `${pagePath}${separator}from_notification=1#${hash}`;
                    
                    console.log('🚀 Final URL:', newUrl);
                    window.location.href = newUrl;
                } else {
                    window.location.href = url;
                }
            } else {
                // URL ปกติไม่มี hash
                console.log('🚀 Navigating to URL without hash:', url);
                window.location.href = url;
            }
        }
    }
}

function markAsReadSilent(notificationId) {
    fetch('<?php echo site_url("notifications/mark_as_read"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // อัปเดต UI เงียบๆ
            const notificationCard = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationCard) {
                notificationCard.classList.remove('unread');
                notificationCard.classList.add('read');
                
                // ลบปุ่ม "ทำเครื่องหมายว่าอ่านแล้ว"
                const markButton = notificationCard.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            }
            
            // อัปเดตจำนวนที่ยังไม่อ่าน
            updateUnreadCount();
            
            console.log('✅ Notification marked as read silently:', notificationId);
        }
    })
    .catch(error => {
        console.error('❌ Error marking as read:', error);
    });
}

// *** เพิ่ม: ฟังก์ชันเฉพาะสำหรับ corruption ***
function goToCorruptionDetail(reportId, notificationId) {
    // ทำเครื่องหมายว่าอ่านแล้ว
    markAsReadSilent(notificationId);
    
    // ไปหน้า corruption detail
    const url = `<?php echo site_url('Corruption/my_report_detail/'); ?>${reportId}`;
    console.log('🚔 Going to corruption detail:', url);
    window.location.href = url;
}

// *** เพิ่ม: ฟังก์ชันเฉพาะสำหรับ complain ***
function goToComplainDetail(complainId, notificationId) {
    // ทำเครื่องหมายว่าอ่านแล้ว
    markAsReadSilent(notificationId);
    
    // ไปหน้า complain detail
    const url = `<?php echo site_url('complaints_public/detail/'); ?>${complainId}`;
    console.log('🚨 Going to complain detail:', url);
    window.location.href = url;
}

// *** เพิ่ม: ฟังก์ชันเฉพาะสำหรับ queue ***
function goToQueueDetail(queueId, notificationId) {
    // ทำเครื่องหมายว่าอ่านแล้ว
    markAsReadSilent(notificationId);
    
    // ไปหน้า queue detail
    const url = `<?php echo site_url('Queue/my_queue_detail/'); ?>${queueId}`;
    console.log('📋 Going to queue detail:', url);
    window.location.href = url;
}

function markAsRead(notificationId) {
    console.log('Marking notification as read with alert:', notificationId);
    
    fetch('<?php echo site_url("notifications/mark_as_read"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        console.log('Mark as read response:', data);
        
        if (data.status === 'success') {
            // อัปเดต UI
            const notificationCard = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationCard) {
                notificationCard.classList.remove('unread');
                notificationCard.classList.add('read');
                
                // ลบปุ่ม "ทำเครื่องหมายว่าอ่านแล้ว"
                const markButton = notificationCard.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
                
                console.log('UI updated for notification:', notificationId);
            }
            
            // อัปเดตจำนวนที่ยังไม่อ่าน
            updateUnreadCount();
            
            showAlert('ทำเครื่องหมายสำเร็จ', 'success');
        } else {
            showAlert('เกิดข้อผิดพลาด: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
    });
}

// ฟังก์ชันลบการแจ้งเตือน (archive)
function archiveNotification(notificationId) {
    if (!confirm('ต้องการลบการแจ้งเตือนนี้ใช่หรือไม่?')) {
        return;
    }
    
    fetch('<?php echo site_url("notifications/archive"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // ลบ card ออกจาก UI
            const notificationCard = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationCard) {
                notificationCard.style.transform = 'translateX(-100%)';
                notificationCard.style.opacity = '0';
                setTimeout(() => {
                    notificationCard.remove();
                    
                    // ตรวจสอบว่าเหลือการแจ้งเตือนอื่นอีกหรือไม่
                    const remainingCards = document.querySelectorAll('.notification-card');
                    if (remainingCards.length === 0) {
                        location.reload(); // รีเฟรชเพื่อแสดง empty state
                    }
                }, 300);
            }
            
            showAlert('ลบการแจ้งเตือนสำเร็จ', 'success');
        } else {
            showAlert('เกิดข้อผิดพลาด: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
    });
}

// ฟังก์ชันอัปเดตจำนวนที่ยังไม่อ่าน
function updateUnreadCount() {
    const unreadCards = document.querySelectorAll('.notification-card.unread');
    const unreadCountElement = document.querySelector('.stat-number.unread');
    
    if (unreadCountElement) {
        unreadCountElement.textContent = unreadCards.length;
    }
    
    // ซ่อนปุ่ม "ทำเครื่องหมายทั้งหมด" ถ้าไม่มีที่ยังไม่อ่าน
    if (unreadCards.length === 0) {
        const markAllButton = document.querySelector('button[onclick="markAllAsRead()"]');
        if (markAllButton) {
            markAllButton.style.display = 'none';
        }
    }
}

// ฟังก์ชันแสดงข้อความแจ้งเตือน
function showAlert(message, type = 'info') {
    // สร้าง alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show notification-alert`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // แทรกที่ body
    document.body.appendChild(alertDiv);
    
    // ลบ alert หลังจาก 5 วินาที
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Smooth scroll to top when clicking pagination
document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination .page-link')) {
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 100);
    }
});

// เพิ่มฟังก์ชันนี้ในส่วน <script> ของไฟล์
function markAllAsRead() {
    const unreadCards = document.querySelectorAll('.notification-card.unread');
    
    if (unreadCards.length === 0) {
        showAlert('ไม่มีการแจ้งเตือนที่ยังไม่ได้อ่าน', 'info');
        return;
    }
    
    if (!confirm(`ต้องการทำเครื่องหมายการแจ้งเตือนทั้งหมด ${unreadCards.length} รายการว่าอ่านแล้วใช่หรือไม่?`)) {
        return;
    }
    
    // เก็บ ID ของ notifications ที่ยังไม่อ่าน
    const notificationIds = Array.from(unreadCards).map(card => 
        card.getAttribute('data-notification-id')
    );
    
    console.log('Marking all as read:', notificationIds);
    
    // แสดง loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise spin me-2"></i>กำลังดำเนินการ...';
    button.disabled = true;
    
    // ส่งคำขอไปยัง server
    fetch('<?php echo site_url("notifications/mark_all_as_read"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_ids=' + encodeURIComponent(JSON.stringify(notificationIds))
    })
    .then(response => response.json())
    .then(data => {
        console.log('Mark all as read response:', data);
        
        if (data.status === 'success') {
            // อัปเดต UI สำหรับทุก notification
            unreadCards.forEach(card => {
                card.classList.remove('unread');
                card.classList.add('read');
                
                // ลบปุ่ม "ทำเครื่องหมายว่าอ่านแล้ว" ออกจากแต่ละ card
                const markButton = card.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            });
            
            // อัปเดตจำนวนที่ยังไม่อ่าน
            updateUnreadCount();
            
            // ซ่อนปุ่ม "ทำเครื่องหมายทั้งหมด"
            button.style.display = 'none';
            
            showAlert(`ทำเครื่องหมายการแจ้งเตือนทั้งหมด ${notificationIds.length} รายการสำเร็จ`, 'success');
        } else {
            showAlert('เกิดข้อผิดพลาด: ' + data.message, 'danger');
            
            // คืนค่าปุ่มเดิม
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
        
        // คืนค่าปุ่มเดิม
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// เพิ่ม CSS สำหรับ loading animation
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
    </script>