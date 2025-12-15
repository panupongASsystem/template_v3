<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ตรวจสอบ login
if (!$this->session->userdata('mp_id') && !$this->session->userdata('m_id')) {
    redirect('User');
    return;
}

// ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย
function convertToThaiDate($date_string) {
    if (empty($date_string)) return '';
    
    $thai_months = array(
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    );
    
    try {
        $date = new DateTime($date_string);
        $day = $date->format('j');
        $month = (int)$date->format('n');
        $year = $date->format('Y') + 543; // แปลงเป็นปี พ.ศ.
        $time = $date->format('H:i');
        
        return $day . ' ' . $thai_months[$month] . ' ' . $year . ' เวลา ' . $time . ' น.';
    } catch (Exception $e) {
        return $date_string; // คืนค่าเดิมถ้าแปลงไม่ได้
    }
}
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --detail-primary-green: #4caf50;
    --detail-secondary-green: #66bb6a;
    --detail-light-green: #e8f5e9;
    --detail-very-light-green: #f1f8e9;
    --detail-success-color: #2e7d32;
    --detail-warning-color: #ff9800;
    --detail-danger-color: #f44336;
    --detail-info-color: #2196f3;
    --detail-text-dark: #2e5233;
    --detail-text-muted: #6c757d;
    --detail-border-light: rgba(76, 175, 80, 0.1);
    --detail-shadow-light: 0 4px 20px rgba(76, 175, 80, 0.1);
    --detail-shadow-medium: 0 8px 30px rgba(76, 175, 80, 0.15);
    --detail-shadow-strong: 0 15px 40px rgba(76, 175, 80, 0.2);
    --detail-gradient-primary: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
    --detail-gradient-light: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);
    --detail-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.detail-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(76, 175, 80, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(46, 125, 50, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(76, 175, 80, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Breadcrumb */
.detail-breadcrumb {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--detail-shadow-light);
    border: 1px solid var(--detail-border-light);
}

.detail-breadcrumb-list {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    padding: 0;
    list-style: none;
}

.detail-breadcrumb-item {
    display: flex;
    align-items: center;
    color: var(--detail-text-muted);
}

.detail-breadcrumb-item a {
    color: var(--detail-primary-green);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.detail-breadcrumb-item a:hover {
    color: var(--detail-secondary-green);
}

.detail-breadcrumb-separator {
    margin: 0 0.5rem;
    color: var(--detail-text-muted);
}

/* Modern Card */
.detail-modern-card {
    background: var(--detail-gradient-card);
    border-radius: 24px;
    box-shadow: var(--detail-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(76, 175, 80, 0.08);
    z-index: 50;
}

.detail-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--detail-gradient-primary);
    z-index: 1;
}

/* Header Card */
.detail-header-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.detail-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.detail-header-content {
    position: relative;
    z-index: 2;
}

.detail-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.detail-suggestion-id {
    background: var(--detail-gradient-light);
    color: var(--detail-primary-green);
    padding: 1rem 1.5rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.5rem;
    border: 2px solid rgba(76, 175, 80, 0.2);
    display: inline-flex;
    align-items: center;
    box-shadow: var(--detail-shadow-light);
}

.detail-status-badge {
    padding: 1rem 1.5rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.detail-suggestion-title {
    color: var(--detail-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 2rem;
    line-height: 1.3;
}

.detail-suggestion-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.detail-meta-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.6);
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid var(--detail-border-light);
}

.detail-meta-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--detail-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--detail-primary-green);
    font-size: 1.2rem;
    box-shadow: var(--detail-shadow-light);
}

.detail-meta-content h6 {
    color: var(--detail-text-muted);
    font-size: 0.9rem;
    margin-bottom: 0.3rem;
    font-weight: 500;
}

.detail-meta-content span {
    color: var(--detail-text-dark);
    font-weight: 600;
    font-size: 1rem;
}

/* Content Card */
.detail-content-card {
    padding: 2.5rem;
}

.detail-section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--detail-border-light);
}

.detail-section-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--detail-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: var(--detail-shadow-medium);
}

.detail-section-title {
    color: var(--detail-text-dark);
    font-weight: 600;
    margin: 0;
    font-size: 1.5rem;
}

.detail-content-text {
    background: var(--detail-gradient-light);
    padding: 2rem;
    border-radius: 16px;
    border-left: 4px solid var(--detail-primary-green);
    color: var(--detail-text-dark);
    line-height: 1.8;
    font-size: 1.1rem;
    white-space: pre-wrap;
    box-shadow: var(--detail-shadow-light);
}

/* Reply Section */
.detail-reply-section {
    margin-top: 2rem;
    padding: 2rem;
    background: linear-gradient(135deg, rgba(46, 125, 50, 0.05) 0%, rgba(76, 175, 80, 0.02) 100%);
    border-radius: 16px;
    border: 1px solid rgba(46, 125, 50, 0.1);
}

.detail-reply-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.detail-reply-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.detail-reply-title {
    color: var(--detail-success-color);
    font-weight: 600;
    margin: 0;
    font-size: 1.3rem;
}

.detail-reply-content {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid var(--detail-success-color);
    color: var(--detail-text-dark);
    line-height: 1.6;
    margin-bottom: 1rem;
    box-shadow: var(--detail-shadow-light);
}

.detail-reply-by {
    color: var(--detail-text-muted);
    font-size: 0.9rem;
    text-align: right;
}

/* History Timeline */
.detail-history-card {
    padding: 2.5rem;
}

.detail-timeline {
    position: relative;
    padding-left: 3rem;
}

.detail-timeline::before {
    content: '';
    position: absolute;
    left: 1.5rem;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, var(--detail-primary-green) 0%, var(--detail-secondary-green) 100%);
    border-radius: 2px;
}

.detail-timeline-item {
    position: relative;
    padding-bottom: 2rem;
    margin-bottom: 1.5rem;
}

.detail-timeline-item::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    background: white;
    border: 4px solid var(--detail-primary-green);
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
    z-index: 2;
}

.detail-timeline-item.current::before {
    background: var(--detail-primary-green);
    animation: timelinePulse 2s infinite;
}

.detail-timeline-content {
    background: rgba(255, 255, 255, 0.8);
    padding: 1.5rem;
    border-radius: 15px;
    border: 1px solid var(--detail-border-light);
    box-shadow: var(--detail-shadow-light);
    transition: all 0.3s ease;
}

.detail-timeline-content:hover {
    transform: translateY(-2px);
    box-shadow: var(--detail-shadow-medium);
}

.detail-timeline-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.detail-timeline-action {
    color: var(--detail-text-dark);
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0;
}

.detail-timeline-date {
    color: var(--detail-text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}

.detail-timeline-by {
    color: var(--detail-primary-green);
    font-weight: 600;
    font-size: 0.95rem;
}

/* Files Section */
.detail-files-card {
    padding: 2.5rem;
}

.detail-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.detail-file-item {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--detail-border-light);
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: var(--detail-shadow-light);
}

.detail-file-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--detail-shadow-strong);
    border-color: rgba(76, 175, 80, 0.3);
}

.detail-file-preview {
    width: 100%;
    height: 150px;
    border-radius: 12px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    overflow: hidden;
    position: relative;
}

.detail-file-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.detail-file-preview img:hover {
    transform: scale(1.05);
}

.detail-file-icon {
    font-size: 3rem;
    color: var(--detail-text-muted);
}

.detail-file-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 8px;
}

.detail-file-item:hover .detail-file-overlay {
    opacity: 1;
}

.detail-file-overlay i {
    color: white;
    font-size: 2rem;
}

.detail-file-info h6 {
    color: var(--detail-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    word-break: break-word;
    line-height: 1.3;
}

.detail-file-size {
    color: var(--detail-text-muted);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.detail-file-actions {
    display: flex;
    gap: 0.5rem;
}

.detail-file-btn {
    flex: 1;
    padding: 0.6rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
}

.detail-file-btn.view {
    background: rgba(33, 150, 243, 0.1);
    color: var(--detail-info-color);
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.detail-file-btn.view:hover {
    background: var(--detail-info-color);
    color: white;
}

.detail-file-btn.download {
    background: rgba(76, 175, 80, 0.1);
    color: var(--detail-primary-green);
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.detail-file-btn.download:hover {
    background: var(--detail-primary-green);
    color: white;
}

/* Actions */
.detail-actions-card {
    padding: 2rem 2.5rem;
    text-align: center;
}

.detail-actions-grid {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.detail-action-btn {
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    font-size: 1rem;
    gap: 0.5rem;
}

.detail-action-btn.primary {
    background: var(--detail-gradient-primary);
    color: white;
    box-shadow: var(--detail-shadow-medium);
}

.detail-action-btn.primary:hover {
    transform: translateY(-3px);
    box-shadow: var(--detail-shadow-strong);
    color: white;
    text-decoration: none;
}

.detail-action-btn.secondary {
    background: rgba(76, 175, 80, 0.1);
    color: var(--detail-primary-green);
    border: 2px solid rgba(76, 175, 80, 0.3);
}

.detail-action-btn.secondary:hover {
    background: var(--detail-gradient-primary);
    color: white;
    transform: translateY(-3px);
    box-shadow: var(--detail-shadow-medium);
}

/* Status Colors */
.detail-status-received { background: var(--detail-warning-color) !important; }
.detail-status-reviewing { background: var(--detail-info-color) !important; }
.detail-status-replied { background: var(--detail-success-color) !important; }
.detail-status-closed { background: var(--detail-text-muted) !important; }

/* Animations */
@keyframes timelinePulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
    }
    50% {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.6), 0 0 0 8px rgba(76, 175, 80, 0.1);
    }
}

@keyframes detailFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.detail-fade-in {
    animation: detailFadeIn 0.6s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .detail-container {
        padding: 0 0.5rem;
    }
    
    .detail-header-top {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .detail-suggestion-title {
        font-size: 1.5rem;
    }
    
    .detail-suggestion-meta {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .detail-meta-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .detail-timeline {
        padding-left: 2rem;
    }
    
    .detail-timeline::before {
        left: 1rem;
    }
    
    .detail-timeline-item::before {
        left: -1.25rem;
    }
    
    .detail-files-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-actions-grid {
        flex-direction: column;
    }
    
    .detail-action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media print {
    .detail-actions-card {
        display: none !important;
    }
    
    .detail-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<div class="detail-bg-pages">
    <div class="detail-container">
        
        <!-- Breadcrumb -->
        <div class="detail-breadcrumb detail-fade-in">
            <ul class="detail-breadcrumb-list">
                <li class="detail-breadcrumb-item">
                    <a href="<?php echo site_url('/'); ?>">
                        <i class="fas fa-home me-1"></i>หน้าแรก
                    </a>
                </li>
                <li class="detail-breadcrumb-separator">
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li class="detail-breadcrumb-item">
                    <a href="<?php echo site_url('Suggestions/my_suggestions'); ?>">
                        <i class="fas fa-lightbulb me-1"></i>ข้อเสนอแนะของฉัน
                    </a>
                </li>
                <li class="detail-breadcrumb-separator">
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li class="detail-breadcrumb-item">
                    <span>รายละเอียด #<?php echo htmlspecialchars($suggestion_data['suggestions_id']); ?></span>
                </li>
            </ul>
        </div>

        <!-- Header Card -->
        <div class="detail-modern-card detail-header-card detail-fade-in" style="animation-delay: 0.1s;">
            <div class="detail-card-gradient-bg"></div>
            <div class="detail-header-content">
                <div class="detail-header-top">
                    <div class="detail-suggestion-id">
                        <i class="fas fa-hashtag me-2"></i>
                        <?php echo htmlspecialchars($suggestion_data['suggestions_id']); ?>
                    </div>
                    <span class="detail-status-badge detail-status-<?php echo $suggestion_data['suggestions_status']; ?>">
                        <i class="<?php echo $suggestion_data['status_icon']; ?> me-2"></i>
                        <?php echo htmlspecialchars($suggestion_data['status_display']); ?>
                    </span>
                </div>
                
                <h1 class="detail-suggestion-title">
                    <?php echo htmlspecialchars($suggestion_data['suggestions_topic']); ?>
                </h1>
                
                <div class="detail-suggestion-meta">
                    <div class="detail-meta-item">
                        <div class="detail-meta-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="detail-meta-content">
                            <h6>วันที่ส่ง</h6>
                            <span><?php echo convertToThaiDate($suggestion_data['suggestions_datesave']); ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-meta-item">
                        <div class="detail-meta-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="detail-meta-content">
                            <h6>ประเภท</h6>
                            <span><?php echo htmlspecialchars($suggestion_data['type_display']); ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-meta-item">
                        <div class="detail-meta-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="detail-meta-content">
                            <h6>ผู้ส่ง</h6>
                            <span><?php echo htmlspecialchars($suggestion_data['suggestions_by']); ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($suggestion_data['updated_date'])): ?>
                    <div class="detail-meta-item">
                        <div class="detail-meta-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="detail-meta-content">
                            <h6>อัพเดทล่าสุด</h6>
                            <span><?php echo convertToThaiDate($suggestion_data['updated_date']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="detail-modern-card detail-content-card detail-fade-in" style="animation-delay: 0.2s;">
            <div class="detail-section-header">
                <div class="detail-section-icon">
                    <i class="fas fa-align-left"></i>
                </div>
                <h3 class="detail-section-title">รายละเอียดข้อเสนอแนะ</h3>
            </div>
            
            <div class="detail-content-text">
                <?php echo nl2br(htmlspecialchars($suggestion_data['suggestions_detail'])); ?>
            </div>
            
            <?php if (!empty($suggestion_data['suggestions_reply'])): ?>
            <div class="detail-reply-section">
                <div class="detail-reply-header">
                    <div class="detail-reply-icon">
                        <i class="fas fa-reply"></i>
                    </div>
                    <h4 class="detail-reply-title">การตอบกลับจากเจ้าหน้าที่</h4>
                </div>
                <div class="detail-reply-content">
                    <?php echo nl2br(htmlspecialchars($suggestion_data['suggestions_reply'])); ?>
                </div>
                <?php if (!empty($suggestion_data['suggestions_replied_by'])): ?>
                <div class="detail-reply-by">
                    <i class="fas fa-user me-1"></i>
                    โดย: <?php echo htmlspecialchars($suggestion_data['suggestions_replied_by']); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- History Timeline -->
        <?php if (!empty($suggestion_history)): ?>
        <div class="detail-modern-card detail-history-card detail-fade-in" style="animation-delay: 0.3s;">
            <div class="detail-section-header">
                <div class="detail-section-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="detail-section-title">ประวัติการดำเนินการ</h3>
            </div>
            
            <div class="detail-timeline">
                <?php foreach ($suggestion_history as $index => $history): 
                    $isLatest = $index === 0;
                    $timelineClass = $isLatest ? 'current' : '';
                ?>
                    <div class="detail-timeline-item <?php echo $timelineClass; ?>">
                        <div class="detail-timeline-content">
                            <div class="detail-timeline-header">
                                <h5 class="detail-timeline-action">
                                    <?php echo htmlspecialchars($history['action_description'] ?? $history['action_type']); ?>
                                </h5>
                                <div class="detail-timeline-date">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo convertToThaiDate($history['action_date']); ?>
                                </div>
                            </div>
                            <div class="detail-timeline-by">
                                <i class="fas fa-user me-1"></i>
                                <?php echo htmlspecialchars($history['action_by']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Files Section -->
        <?php if (!empty($suggestion_files)): ?>
        <div class="detail-modern-card detail-files-card detail-fade-in" style="animation-delay: 0.4s;">
            <div class="detail-section-header">
                <div class="detail-section-icon">
                    <i class="fas fa-paperclip"></i>
                </div>
                <h3 class="detail-section-title">ไฟล์แนบ</h3>
            </div>
            
            <div class="detail-files-grid">
                <?php foreach ($suggestion_files as $file): ?>
                    <div class="detail-file-item">
                        <div class="detail-file-preview">
                            <?php if ($file->is_image): ?>
                                <img src="<?php echo site_url('Suggestions/view_image/' . $file->suggestions_file_name); ?>" 
                                     alt="<?php echo htmlspecialchars($file->suggestions_file_original_name); ?>"
                                     onerror="showImageError(this)">
                                <div class="detail-file-overlay">
                                    <i class="fas fa-eye"></i>
                                </div>
                            <?php else: ?>
                                <i class="<?php echo $file->file_icon; ?> detail-file-icon"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="detail-file-info">
                            <h6><?php echo htmlspecialchars($file->suggestions_file_original_name); ?></h6>
                            <div class="detail-file-size">
                                <i class="fas fa-file me-1"></i>
                                <?php echo $file->file_size_formatted; ?>
                            </div>
                            
                            <div class="detail-file-actions">
                                <?php if ($file->is_image): ?>
                                    <button class="detail-file-btn view" onclick="viewImagePreview('<?php echo site_url('Suggestions/view_image/' . $file->suggestions_file_name); ?>', '<?php echo htmlspecialchars($file->suggestions_file_original_name); ?>')">
                                        <i class="fas fa-eye"></i>
                                        ดู
                                    </button>
                                <?php else: ?>
                                    <button class="detail-file-btn view" onclick="viewFilePreview('<?php echo site_url('Suggestions/download_file/' . $file->suggestions_file_name); ?>', '<?php echo htmlspecialchars($file->suggestions_file_original_name); ?>')">
                                        <i class="fas fa-eye"></i>
                                        ดู
                                    </button>
                                <?php endif; ?>
                                <button class="detail-file-btn download" onclick="downloadFile('<?php echo $file->suggestions_file_name; ?>')">
                                    <i class="fas fa-download"></i>
                                    ดาวน์โหลด
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="detail-modern-card detail-actions-card detail-fade-in" style="animation-delay: 0.5s;">
            <div class="detail-actions-grid">
                <a href="<?php echo site_url('Suggestions/my_suggestions'); ?>" class="detail-action-btn secondary">
                    <i class="fas fa-arrow-left"></i>
                    กลับไปรายการ
                </a>
                
                <button onclick="copySuggestionId('<?php echo $suggestion_data['suggestions_id']; ?>')" class="detail-action-btn secondary">
                    <i class="fas fa-copy"></i>
                    คัดลอกหมายเลข
                </button>
                
                <a href="<?php echo site_url('Suggestions/adding_suggestions'); ?>" class="detail-action-btn primary">
                    <i class="fas fa-plus"></i>
                    ส่งข้อเสนอแนะใหม่
                </a>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ===================================================================
// *** UTILITY FUNCTIONS ***
// ===================================================================

// Copy Suggestion ID
function copySuggestionId(suggestionId) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(suggestionId).then(() => {
            showAlert('success', 'คัดลอกสำเร็จ', 'คัดลอกหมายเลข ' + suggestionId + ' สำเร็จ');
        }).catch(() => {
            fallbackCopyText(suggestionId);
        });
    } else {
        fallbackCopyText(suggestionId);
    }
}

function fallbackCopyText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showAlert('success', 'คัดลอกสำเร็จ', 'คัดลอกหมายเลข ' + text + ' สำเร็จ');
    } catch (err) {
        showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถคัดลอกได้');
    }
    document.body.removeChild(textArea);
}

// Download file
function downloadFile(fileName) {
    window.open('<?php echo site_url("Suggestions/download_file/"); ?>' + fileName, '_blank');
}

// View image preview
function viewImagePreview(imageUrl, fileName) {
    Swal.fire({
        title: fileName,
        html: `
            <div style="text-align: center;">
                <img src="${imageUrl}" alt="${fileName}" style="max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.3s;" onload="this.style.opacity='1'">
            </div>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        width: 'auto',
        padding: '1rem',
        background: '#fff',
        customClass: {
            popup: 'image-preview-modal'
        }
    });
}

// View file preview
function viewFilePreview(fileUrl, fileName) {
    const newWindow = window.open('', '_blank');
    newWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${fileName}</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { margin: 0; padding: 0; background: #f5f5f5; font-family: 'Kanit', sans-serif; }
                .header { background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%); color: white; padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header h1 { margin: 0; font-size: 1.2rem; font-weight: 600; }
                .file-container { width: 100%; height: calc(100vh - 80px); border: none; }
                .loading { display: flex; align-items: center; justify-content: center; height: 200px; font-size: 1.1rem; color: #666; }
                .download-btn { position: fixed; bottom: 20px; right: 20px; background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%); color: white; border: none; padding: 12px 20px; border-radius: 50px; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); cursor: pointer; font-weight: 600; transition: all 0.3s ease; z-index: 1000; }
                .download-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4); }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📄 ${fileName}</h1>
            </div>
            <div class="loading" id="loading">
                <div style="text-align: center;">
                    <div style="display: inline-block; width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #4caf50; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 10px;"></div>
                    <br>กำลังโหลดไฟล์...
                </div>
            </div>
            <embed src="${fileUrl}" type="application/pdf" class="file-container" onload="document.getElementById('loading').style.display='none';">
            <button class="download-btn" onclick="window.open('${fileUrl}', '_self')">📥 ดาวน์โหลด</button>
        </body>
        </html>
    `);
    newWindow.document.close();
}

// Show image error
function showImageError(imgElement) {
    imgElement.style.display = 'none';
    imgElement.parentElement.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #f44336;">
            <i class="fas fa-image" style="font-size: 3rem; margin-bottom: 0.5rem;"></i>
            <span style="font-size: 0.9rem;">ไม่สามารถโหลดรูปภาพได้</span>
        </div>
    `;
}

// Alert function
function showAlert(icon, title, text) {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonColor: '#4caf50',
        confirmButtonText: 'ตกลง'
    });
}

// ===================================================================
// *** INITIALIZATION ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.detail-modern-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Add hover effects to timeline items
    const timelineItems = document.querySelectorAll('.detail-timeline-item');
    timelineItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.querySelector('.detail-timeline-content').style.transform = 'translateX(10px)';
        });
        
        item.addEventListener('mouseleave', () => {
            item.querySelector('.detail-timeline-content').style.transform = 'translateX(0)';
        });
    });
    
   // console.log('✅ Suggestion detail page initialized');
});
</script>