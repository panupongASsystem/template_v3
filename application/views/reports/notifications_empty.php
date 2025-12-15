<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// ตั้งค่าเริ่มต้นถ้าไม่มีข้อมูลส่งมาจาก Controller
if (!isset($notifications)) $notifications = [];
if (!isset($unread_count)) $unread_count = 0;
if (!isset($total_notifications)) $total_notifications = 0;

// ฟังก์ชันจัดการ URL
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

function build_notification_url($url) {
    if (empty($url) || $url === '#') return '#';
    
    $clean_url = clean_notification_url($url);
    
    if (strpos($clean_url, 'http') === 0) {
        return $clean_url;
    }
    
    return base_url($clean_url);
}

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
        <div class="notifications-container">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <?php 
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
                    ?>
                    
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
                                </div>
                            <?php endif; ?>

                            <!-- Action Buttons -->
                            <div class="notification-actions">
                                <?php if ($isUnread): ?>
                                    <button class="btn btn-outline-success btn-sm" 
                                            onclick="markAsRead(<?php echo $notification->notification_id; ?>); event.stopPropagation();">
                                        <i class="bi bi-check-circle me-1"></i>ทำเครื่องหมายว่าอ่านแล้ว
                                    </button>
                                <?php endif; ?>
                                
                                <!-- ปิดปุ่มลบไว้ก่อน -->
                                <!-- 
                                <button class="btn btn-outline-danger btn-sm" 
                                        onclick="archiveNotification(<?php echo $notification->notification_id; ?>); event.stopPropagation();">
                                    <i class="bi bi-archive me-1"></i>ลบ
                                </button>
                                -->
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
                    <p>ยังไม่มีการแจ้งเตือนในขณะนี้<br>การแจ้งเตือนใหม่จะปรากฏที่นี่</p>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Staff Notifications Page - DOM Content Loaded');
    
    // ตรวจสอบว่ามี hash ใน URL หรือไม่
    if (window.location.hash) {
        const hash = window.location.hash.substring(1); // ลบ # ออก
        console.log('📍 Staff page loaded with hash:', hash);
        
        // รอให้เนื้อหาโหลดเสร็จ แล้วค่อย scroll
        setTimeout(() => {
            scrollToElement(hash);
        }, 1000);
    }
    
    // ตรวจสอบว่ามาจาก notification redirect หรือไม่
    const urlParams = new URLSearchParams(window.location.search);
    const fromNotification = urlParams.get('from_notification');
    if (fromNotification && window.location.hash) {
        console.log('📥 Redirected from notification with hash');
        // เพิ่มเวลารอเพื่อให้หน้าโหลดเสร็จสมบูรณ์
        setTimeout(() => {
            const hash = window.location.hash.substring(1);
            scrollToElementWithHighlight(hash);
        }, 1500);
    }
    
    // Handle URL parameters สำหรับ notification click
    handleNotificationUrlParams();
    
    console.log('✅ Staff Notifications Page initialized with hash support');
});

// *** ปรับปรุงฟังก์ชัน handleNotificationCardClick ให้ใช้งานได้จริง ***
function handleNotificationCardClick(notificationId, url, target, event) {
    // ตรวจสอบว่าคลิกที่ปุ่มหรือไม่
    if (event.target.closest('.notification-actions button')) {
        console.log('Button clicked, ignoring card click');
        return;
    }
    
    console.log('🔔 Notification card clicked:', { notificationId, url, target });
    
    if (url && url !== '' && url !== '#') {
        // ✅ แก้ไข: ตรวจสอบประเภท URL
        const isComplainDetail = url.includes('System_reports/complain_detail');
        const isQAPage = url.includes('Pages/q_a') || url.includes('q_a');
        const isExternal = url.startsWith('http');
        
        // Mark as read ก่อนเสมอ
        markAsReadSilent(notificationId).then(() => {
            if (isComplainDetail) {
                // ✅ Complain detail: same tab
                console.log('📋 Opening complain detail in same tab');
                window.location.href = url;
                
            } else if (isQAPage || isExternal) {
                // ✅ Q&A หรือ External: new tab
                console.log('🆕 Opening in new tab:', url);
                window.open(url, '_blank');
                
            } else {
                // ✅ อื่นๆ: same tab
                console.log('📄 Opening in same tab:', url);
                window.location.href = url;
            }
        });
    } else {
        // ไม่มี URL ให้ mark as read อย่างเดียว
        markAsReadSilent(notificationId);
    }
}
		
		
		
		
		
		function getUrlType(url) {
    if (!url || url === '#') return 'none';
    
    if (url.includes('System_reports/complain_detail')) return 'complain';
    if (url.includes('Pages/q_a') || url.includes('q_a')) return 'qa';
    if (url.startsWith('http')) return 'external';
    if (url.includes('backend') || url.includes('admin')) return 'admin';
    
    return 'internal';
}
		
		
		


// *** ฟังก์ชันทำความสะอาด hash ***
function cleanHashFromUrlParams(hash) {
    if (!hash) return hash;
    
    let cleaned = hash;
    
    // ลบ URL parameters ที่ไม่ต้องการ
    cleaned = cleaned.replace(/[&?]gsc\.tab=\d+/g, '');
    cleaned = cleaned.replace(/[&?]utm_[^&#]*/g, '');
    cleaned = cleaned.replace(/[&?]_ga=[^&#]*/g, '');
    cleaned = cleaned.replace(/[&?]_gl=[^&#]*/g, '');
    cleaned = cleaned.replace(/[&?]fbclid=[^&#]*/g, '');
    cleaned = cleaned.replace(/[&?]gclid=[^&#]*/g, '');
    cleaned = cleaned.replace(/[&?]PHPSESSID=[^&#]*/g, '');
    cleaned = cleaned.replace(/[&?]+$/, '');
    
    return cleaned;
}

// *** ปรับปรุงฟังก์ชัน scrollToElement ให้ทำงานได้ดีขึ้น ***
function scrollToElement(hash) {
    console.log('🎯 Staff scrolling to hash:', hash);
    
    const targetElement = document.getElementById(hash);
    if (targetElement) {
        console.log('✅ Found target element:', targetElement);
        
        // เพิ่ม highlight effect
        targetElement.style.transition = 'all 0.5s ease';
        targetElement.style.background = 'linear-gradient(135deg, rgba(69, 123, 157, 0.2) 0%, rgba(69, 123, 157, 0.1) 100%)';
        targetElement.style.border = '2px solid rgba(69, 123, 157, 0.5)';
        targetElement.style.transform = 'scale(1.02)';
        targetElement.style.boxShadow = '0 8px 25px rgba(69, 123, 157, 0.3)';
        
        // เลื่อนไปที่ element
        targetElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center',
            inline: 'nearest'
        });
        
        // แสดง notification ว่าพบกระทู้แล้ว
        setTimeout(() => {
            showAlert('🎯 พบกระทู้ที่ต้องการแล้ว', 'success');
        }, 500);
        
        // ลบ highlight หลัง 4 วินาที
        setTimeout(() => {
            targetElement.style.background = '';
            targetElement.style.border = '';
            targetElement.style.transform = '';
            targetElement.style.boxShadow = '';
        }, 4000);
        
        console.log('✅ Successfully scrolled to element:', hash);
        return true;
    } else {
        console.warn('❌ Element not found for hash:', hash);
        
        // ลองค้นหา element ที่เกี่ยวข้อง
        const relatedElement = findRelatedElement(hash);
        if (relatedElement) {
            console.log('🔍 Found related element:', relatedElement.id);
            scrollToElement(relatedElement.id);
            return true;
        }
        
        // ถ้าไม่พบ element ให้แสดง notification และค้นหาผ่าน API
        showAlert('❌ ไม่พบกระทู้ที่ระบุ กำลังค้นหา...', 'warning');
        
        // ลองค้นหาผ่าน API
        const topicId = extractTopicIdFromHash(hash);
        if (topicId && !isNaN(topicId)) {
            findTopicPageAndNavigate(topicId, hash);
        } else {
            // Fallback: reload หน้าพร้อม hash
            setTimeout(() => {
                window.location.href = window.location.pathname + window.location.search + '#' + hash;
            }, 2000);
        }
        
        return false;
    }
}

// *** ฟังก์ชัน scrollToElementWithHighlight (สำหรับกรณีมาจาก notification) ***
function scrollToElementWithHighlight(hash) {
    console.log('🎯 Staff scrolling to hash with highlight:', hash);
    
    const targetElement = document.getElementById(hash);
    if (targetElement) {
        console.log('✅ Found target element with highlight:', targetElement);
        
        // เพิ่ม highlight effect แบบพิเศษ
        targetElement.style.transition = 'all 0.5s ease';
        targetElement.style.background = 'linear-gradient(135deg, rgba(255, 215, 0, 0.3) 0%, rgba(255, 215, 0, 0.1) 100%)';
        targetElement.style.border = '3px solid rgba(255, 215, 0, 0.7)';
        targetElement.style.transform = 'scale(1.03)';
        targetElement.style.boxShadow = '0 12px 30px rgba(255, 215, 0, 0.4)';
        
        // เลื่อนไปที่ element
        targetElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center',
            inline: 'nearest'
        });
        
        // แสดง notification ว่าพบกระทู้แล้ว
        setTimeout(() => {
            showAlert('🎯 พบกระทู้จากการแจ้งเตือนแล้ว!', 'success');
        }, 500);
        
        // ลบ highlight หลัง 5 วินาที
        setTimeout(() => {
            targetElement.style.background = '';
            targetElement.style.border = '';
            targetElement.style.transform = '';
            targetElement.style.boxShadow = '';
        }, 5000);
        
        console.log('✅ Successfully scrolled to element with highlight:', hash);
        return true;
    } else {
        // ไม่พบ element - ใช้ฟังก์ชัน scrollToElement ปกติ
        return scrollToElement(hash);
    }
}

// *** ฟังก์ชันค้นหา element ที่เกี่ยวข้อง ***
function findRelatedElement(hash) {
    console.log('🔍 Searching for related element:', hash);
    
    // ลองหา element ที่มี ID คล้ายกัน
    const patterns = [
        hash,                    // ตัวเลขเต็ม
        `comment-${hash}`,       // comment-XX
        `reply-${hash}`,         // reply-XX
        `topic-${hash}`,         // topic-XX
        `post-${hash}`           // post-XX
    ];
    
    // ถ้า hash เป็นรูปแบบ comment-XX ให้ลองหา XX
    const commentMatch = hash.match(/comment-(\d+)/);
    if (commentMatch) {
        patterns.push(commentMatch[1]);
    }
    
    // ถ้า hash เป็นรูปแบบ reply-XX ให้ลองหา comment ที่เกี่ยวข้อง
    const replyMatch = hash.match(/reply-(\d+)/);
    if (replyMatch) {
        // ลองหาใน DOM ว่า reply นี้อยู่ใน comment ไหน
        const replyElements = document.querySelectorAll('[id^="reply-"]');
        for (let replyEl of replyElements) {
            const parentComment = replyEl.closest('[id^="comment-"]');
            if (parentComment) {
                patterns.push(parentComment.id);
            }
        }
    }
    
    for (let pattern of patterns) {
        const element = document.getElementById(pattern);
        if (element) {
            console.log('✅ Found related element with pattern:', pattern);
            return element;
        }
    }
    
    console.log('❌ No related element found');
    return null;
}

// *** ฟังก์ชันดึง Topic ID จาก hash ***
function extractTopicIdFromHash(hash) {
    if (!hash) return null;
    
    const patterns = [
        /comment-(\d+)/,  // comment-77
        /reply-(\d+)/,    // reply-123
        /topic-(\d+)/,    // topic-456
        /post-(\d+)/,     // post-789
        /^(\d+)$/         // 77 (ตัวเลขเปล่า)
    ];
    
    for (const pattern of patterns) {
        const match = hash.match(pattern);
        if (match && match[1]) {
            return parseInt(match[1]);
        }
    }
    
    return null;
}

// *** ฟังก์ชันค้นหาหน้าที่กระทู้อยู่ ***
function findTopicPageAndNavigate(topicId, hash) {
    console.log('🔍 Finding page for topic ID:', topicId);
    
    showStaffLoadingMessage('กำลังค้นหากระทู้...');
    
    // สร้าง URL สำหรับ API
    const apiUrl = '<?= site_url("Api_test/find_topic"); ?>';
    
    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `topic_id=${topicId}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('📊 API Response:', data);
        hideStaffLoadingMessage();
        
        if (data.success && data.page) {
            console.log(`✅ Topic found on page ${data.page}, navigating...`);
            
            // สร้าง URL ใหม่
            const baseUrl = '<?= site_url("Pages/q_a"); ?>';
            const newUrl = `${baseUrl}?page=${data.page}&from_notification=1#${hash}`;
            
            console.log('🚀 Navigating to:', newUrl);
            window.location.href = newUrl;
            
        } else {
            console.error('❌ Topic not found:', data.message || 'Unknown error');
            showStaffErrorMessage('ไม่พบกระทู้ที่ระบุ');
        }
    })
    .catch(error => {
        console.error('🚨 Error finding topic page:', error);
        hideStaffLoadingMessage();
        showStaffErrorMessage('เกิดข้อผิดพลาดในการค้นหา');
    });
}

// *** ฟังก์ชันแสดง/ซ่อน Loading Message ***
function showStaffLoadingMessage(message) {
    hideStaffLoadingMessage();
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'staff-loading-message';
    loadingDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: linear-gradient(135deg, #457b9d, #1d3557);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(69, 123, 157, 0.3);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        animation: slideInRight 0.3s ease;
        max-width: 300px;
    `;
    
    loadingDiv.innerHTML = `
        <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span>${message}</span>
    `;
    
    document.body.appendChild(loadingDiv);
}

function hideStaffLoadingMessage() {
    const loadingDiv = document.getElementById('staff-loading-message');
    if (loadingDiv) {
        loadingDiv.remove();
    }
}

function showStaffErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.id = 'staff-error-message';
    errorDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: linear-gradient(135deg, #e63946, #dc3545);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
        font-weight: 500;
        max-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
    
    errorDiv.innerHTML = `<span>${message}</span>`;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 4000);
}

// *** เพิ่มการ handle URL parameters สำหรับ notification click ***
function handleNotificationUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const notificationId = urlParams.get('notification_id');
    const action = urlParams.get('action');
    
    if (notificationId && action === 'mark_read') {
        console.log('📥 Processing notification URL params:', { notificationId, action });
        
        // ทำเครื่องหมายว่าอ่านแล้วและแสดง notification
        markAsReadSilent(notificationId);
        
        // ลบ URL parameters
        const newUrl = window.location.pathname + window.location.hash;
        window.history.replaceState({}, document.title, newUrl);
        
        // แสดง notification
        setTimeout(() => {
            showAlert('📋 การแจ้งเตือนถูกทำเครื่องหมายว่าอ่านแล้ว', 'info');
        }, 1000);
    }
}

// *** ฟังก์ชันทำเครื่องหมายว่าอ่านแล้ว (เงียบๆ) ***
function markAsReadSilent(notificationId) {
    return fetch('<?php echo site_url("System_reports/mark_notification_read"); ?>', {
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
                
                const markButton = notificationCard.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            }
            
            updateUnreadCount();
            console.log('✅ Notification marked as read silently:', notificationId);
            return true;
        } else {
            console.error('❌ Failed to mark as read:', data.message);
            return false;
        }
    })
    .catch(error => {
        console.error('❌ Error marking notification as read:', error);
        return false;
    });
}
		
		
		

// *** ฟังก์ชันทำเครื่องหมายว่าอ่านแล้ว (แสดง alert) ***
function markAsRead(notificationId) {
    console.log('Marking staff notification as read with alert:', notificationId);
    
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
                
                console.log('UI updated for staff notification:', notificationId);
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

// *** ฟังก์ชันทำเครื่องหมายทั้งหมดว่าอ่านแล้ว ***
function markAllAsRead() {
    if (!confirm('ต้องการทำเครื่องหมายการแจ้งเตือนทั้งหมดว่าอ่านแล้วใช่หรือไม่?')) {
        return;
    }

    fetch('<?php echo site_url("System_reports/mark_all_notifications_read"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // อัปเดต UI - ลบ class 'unread' จากทุก card
            document.querySelectorAll('.notification-card.unread').forEach(card => {
                card.classList.remove('unread');
                card.classList.add('read');
                
                // ลบปุ่ม "ทำเครื่องหมายว่าอ่านแล้ว"
                const markButton = card.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            });
            
            // อัปเดตจำนวนที่ยังไม่อ่าน
            updateUnreadCount();
            
            showAlert('ทำเครื่องหมายทั้งหมดสำเร็จ', 'success');
            
            // ซ่อนปุ่ม "ทำเครื่องหมายทั้งหมด"
            const markAllButton = document.querySelector('button[onclick="markAllAsRead()"]');
            if (markAllButton) {
                markAllButton.style.display = 'none';
            }
        } else {
            showAlert('เกิดข้อผิดพลาด: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'danger');
    });
}

// *** ฟังก์ชันอัปเดตจำนวนที่ยังไม่อ่าน ***
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

// *** ฟังก์ชันแสดง Alert ***
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

// *** Smooth scroll to top when clicking pagination ***
document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination .page-link')) {
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 100);
    }
});

// *** เพิ่ม CSS Animations ***
const style = document.createElement('style');
style.textContent = `
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
`;
document.head.appendChild(style);

// *** ฟังก์ชันทดสอบสำหรับ Debug ***
function testStaffNavigation(hash) {
    console.log('🧪 Testing staff navigation with hash:', hash);
    const testHash = hash || 'comment-90';
    console.log('Testing with hash:', testHash);
    scrollToElement(testHash);
}

function debugStaffElements() {
    console.log('=== STAFF DEBUG: Available Elements ===');
    console.log('Q&A Elements:');
    document.querySelectorAll('[id^="comment-"]').forEach(el => {
        console.log('- ' + el.id, el);
    });
    console.log('Reply Elements:');
    document.querySelectorAll('[id^="reply-"]').forEach(el => {
        console.log('- ' + el.id, el);
    });
    console.log('Current URL:', window.location.href);
    console.log('Current Hash:', window.location.hash);
    console.log('======================================');
}

// *** เพิ่มใน console สำหรับ debug ***
console.log('🔧 Staff Hash Navigation Functions Available:');
console.log('- testStaffNavigation("comment-90") - ทดสอบการ scroll');
console.log('- scrollToElement("comment-90") - scroll ไปที่ element');
console.log('- findRelatedElement("90") - ค้นหา element ที่เกี่ยวข้อง');
console.log('- debugStaffElements() - แสดงรายการ elements ทั้งหมด');
</script>