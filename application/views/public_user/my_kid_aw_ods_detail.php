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
        $year = $date->format('Y') + 543;
        $time = $date->format('H:i');
        
        return $day . ' ' . $thai_months[$month] . ' ' . $year . ' เวลา ' . $time . ' น.';
    } catch (Exception $e) {
        return $date_string;
    }
}

// ตรวจสอบข้อมูลเงินสนับสนุนเด็ก
if (empty($kid_detail)) {
    show_404();
    return;
}

$kid = $kid_detail;
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --kid-primary-green: #10b981;
    --kid-secondary-green: #059669;
    --kid-light-green: #d1fae5;
    --kid-very-light-green: #ecfdf5;
    --kid-success-color: #10b981;
    --kid-warning-color: #f59e0b;
    --kid-danger-color: #ef4444;
    --kid-info-color: #3b82f6;
    --kid-purple-color: #8b5cf6;
    --kid-text-dark: #1f2937;
    --kid-text-muted: #6b7280;
    --kid-border-light: rgba(16, 185, 129, 0.1);
    --kid-shadow-light: 0 4px 20px rgba(16, 185, 129, 0.1);
    --kid-shadow-medium: 0 8px 30px rgba(16, 185, 129, 0.15);
    --kid-shadow-strong: 0 15px 40px rgba(16, 185, 129, 0.2);
    --kid-gradient-primary: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --kid-gradient-light: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    --kid-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.kid-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(16, 185, 129, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(5, 150, 105, 0.03) 0%, transparent 50%);
    min-height: 100vh;
    padding: 2rem 0;
}

.kid-container-pages {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Card */
.kid-modern-card {
    background: var(--kid-gradient-card);
    border-radius: 24px;
    box-shadow: var(--kid-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(16, 185, 129, 0.08);
}

.kid-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--kid-gradient-primary);
}

/* Page Header */
.kid-page-header {
    padding: 2.5rem;
    background: var(--kid-gradient-primary);
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.kid-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: headerShine 3s infinite;
}

.kid-page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
}

.kid-page-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    position: relative;
    z-index: 2;
}

/* Breadcrumb */
.kid-breadcrumb {
    padding: 1.5rem 2.5rem;
    background: rgba(16, 185, 129, 0.05);
    border-bottom: 1px solid var(--kid-border-light);
}

.kid-breadcrumb a {
    color: var(--kid-primary-green);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.kid-breadcrumb a:hover {
    color: var(--kid-secondary-green);
    text-decoration: underline;
}

.kid-breadcrumb .active {
    color: var(--kid-text-muted);
    font-weight: 600;
}

/* Detail Header */
.kid-detail-header {
    padding: 2.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.kid-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.kid-id-badge {
    background: var(--kid-gradient-light);
    color: var(--kid-primary-green);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.3rem;
    border: 2px solid rgba(16, 185, 129, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.kid-date-info {
    color: var(--kid-text-muted);
    font-size: 1rem;
}

.kid-status-section {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.kid-status-badge {
    padding: 1rem 2rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1.1rem;
    color: white;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}

.kid-type-badge {
    background: var(--kid-gradient-primary);
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Content Sections */
.kid-content-section {
    padding: 2.5rem;
    border-bottom: 1px solid var(--kid-border-light);
}

.kid-content-section:last-child {
    border-bottom: none;
}

.kid-section-title {
    color: var(--kid-text-dark);
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.kid-section-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--kid-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

/* Info Grid */
.kid-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.kid-info-item {
    background: var(--kid-very-light-green);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--kid-primary-green);
    transition: all 0.3s ease;
}

.kid-info-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--kid-shadow-medium);
}

.kid-info-label {
    color: var(--kid-text-muted);
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kid-info-value {
    color: var(--kid-text-dark);
    font-size: 1.1rem;
    font-weight: 500;
    line-height: 1.4;
}

/* Address Section */
.kid-address-card {
    background: var(--kid-gradient-light);
    padding: 2rem;
    border-radius: 20px;
    border: 2px solid rgba(16, 185, 129, 0.1);
    position: relative;
    overflow: hidden;
}

.kid-address-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100%;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
}

.kid-address-content {
    position: relative;
    z-index: 2;
}

/* Files Section */
.kid-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.kid-file-item {
    background: white;
    border: 2px solid var(--kid-border-light);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.kid-file-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--kid-shadow-strong);
    border-color: var(--kid-primary-green);
}

.kid-file-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.kid-file-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.kid-file-icon.pdf {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.kid-file-icon.image {
    background: var(--kid-gradient-primary);
}

.kid-file-info {
    flex: 1;
}

.kid-file-name {
    color: var(--kid-text-dark);
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.3rem;
    line-height: 1.3;
}

.kid-file-meta {
    color: var(--kid-text-muted);
    font-size: 0.85rem;
}

.kid-file-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

/* ปุ่มไฟล์ */
.kid-file-btn {
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer !important;
    transition: all 0.3s ease;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    z-index: 10;
    pointer-events: auto !important;
}

.kid-file-btn:hover {
    transform: translateY(-2px);
    text-decoration: none !important;
}

.kid-file-btn.primary {
    background: var(--kid-gradient-primary) !important;
    color: white !important;
}

.kid-file-btn.primary:hover {
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    color: white !important;
}

.kid-file-btn.secondary {
    background: rgba(16, 185, 129, 0.1) !important;
    color: var(--kid-primary-green) !important;
    border: 2px solid rgba(16, 185, 129, 0.3);
}

.kid-file-btn.secondary:hover {
    background: var(--kid-primary-green) !important;
    color: white !important;
}

.kid-file-btn.danger {
    background: rgba(239, 68, 68, 0.1) !important;
    color: var(--kid-danger-color) !important;
    border: 2px solid rgba(239, 68, 68, 0.3);
}

.kid-file-btn.danger:hover {
    background: var(--kid-danger-color) !important;
    color: white !important;
}

/* History Timeline */
.kid-timeline {
    position: relative;
    padding-left: 2rem;
}

.kid-timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--kid-gradient-primary);
    border-radius: 2px;
}

.kid-timeline-item {
    position: relative;
    padding-bottom: 2rem;
    margin-left: 1.5rem;
}

.kid-timeline-item::before {
    content: '';
    position: absolute;
    left: -2.8rem;
    top: 0.5rem;
    width: 16px;
    height: 16px;
    background: var(--kid-gradient-primary);
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}

.kid-timeline-content {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--kid-border-light);
    box-shadow: var(--kid-shadow-light);
}

.kid-timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.kid-timeline-action {
    color: var(--kid-text-dark);
    font-weight: 600;
    font-size: 1rem;
}

.kid-timeline-date {
    color: var(--kid-text-muted);
    font-size: 0.85rem;
}

.kid-timeline-description {
    color: var(--kid-text-dark);
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.kid-timeline-by {
    color: var(--kid-text-muted);
    font-size: 0.9rem;
    font-style: italic;
}

/* Action Buttons */
.kid-actions {
    padding: 2.5rem;
    text-align: center;
    background: var(--kid-very-light-green);
}

.kid-action-btn {
    background: var(--kid-gradient-primary);
    color: white;
    border: none;
    border-radius: 15px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    margin: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
}

.kid-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    color: white;
    text-decoration: none;
}

.kid-action-btn.secondary {
    background: rgba(16, 185, 129, 0.1);
    color: var(--kid-primary-green);
    border: 2px solid rgba(16, 185, 129, 0.3);
}

.kid-action-btn.secondary:hover {
    background: var(--kid-gradient-primary);
    color: white;
    border-color: var(--kid-primary-green);
}

.kid-action-btn.warning {
    background: var(--kid-warning-color);
    color: white;
}

.kid-action-btn.warning:hover {
    background: #d97706;
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
}

/* Status Colors */
.kid-status-submitted { background: var(--kid-warning-color) !important; }
.kid-status-reviewing { background: var(--kid-info-color) !important; }
.kid-status-approved { background: var(--kid-success-color) !important; }
.kid-status-rejected { background: var(--kid-danger-color) !important; }
.kid-status-completed { background: var(--kid-purple-color) !important; }

/* Empty States */
.kid-empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--kid-text-muted);
}

.kid-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--kid-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--kid-primary-green);
}

/* Animations */
@keyframes headerShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kid-modern-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .kid-container-pages {
        padding: 0 0.5rem;
    }
    
    .kid-detail-header {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    
    .kid-status-section {
        width: 100%;
        align-items: flex-start;
    }
    
    .kid-info-grid {
        grid-template-columns: 1fr;
    }
    
    .kid-files-grid {
        grid-template-columns: 1fr;
    }
    
    .kid-page-title {
        font-size: 2rem;
    }
    
    .kid-timeline {
        padding-left: 1rem;
    }
    
    .kid-timeline-item {
        margin-left: 1rem;
    }
    
    .kid-timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media print {
    .kid-actions,
    .kid-action-btn {
        display: none !important;
    }
    
    .kid-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<div class="kid-bg-pages">
    <div class="kid-container-pages">
        
        <!-- Main Detail Card -->
        <div class="kid-modern-card">
            
            <!-- Page Header -->
            <div class="kid-page-header">
                <h1 class="kid-page-title">
                    <i class="fas fa-baby me-3"></i>
                    รายละเอียดเงินสนับสนุนเด็กแรกเกิด
                </h1>
                <p class="kid-page-subtitle">ข้อมูลการยื่นขอรับเงินสนับสนุนเด็กแรกเกิด</p>
            </div>

            <!-- Breadcrumb -->
            <div class="kid-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo site_url(); ?>">
                                <i class="fas fa-home me-1"></i>หน้าแรก
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo site_url('Kid_aw_ods/my_kid_aw_ods'); ?>">
                                <i class="fas fa-baby me-1"></i>เงินสนับสนุนเด็กของฉัน
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-eye me-1"></i>รายละเอียด #<?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? ''); ?>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Detail Header -->
            <div class="kid-detail-header">
                <div class="kid-id-section">
                    <div class="kid-id-badge">
                        <i class="fas fa-hashtag me-2"></i>
                        <?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? ''); ?>
                    </div>
                    <div class="kid-date-info">
                        <i class="fas fa-calendar-alt me-1"></i>
                        ยื่นเรื่องวันที่: <?php echo convertToThaiDate($kid['kid_aw_ods_datesave'] ?? ''); ?>
                    </div>
                    <?php if (!empty($kid['kid_aw_ods_updated_at'])): ?>
                    <div class="kid-date-info">
                        <i class="fas fa-sync-alt me-1"></i>
                        อัปเดตล่าสุด: <?php echo convertToThaiDate($kid['kid_aw_ods_updated_at']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="kid-status-section">
                    <?php
                    // Status mapping
                    $status_displays = [
                        'submitted' => 'ยื่นเรื่องแล้ว',
                        'reviewing' => 'กำลังพิจารณา',
                        'approved' => 'อนุมัติแล้ว',
                        'rejected' => 'ไม่อนุมัติ',
                        'completed' => 'เสร็จสิ้น'
                    ];
                    
                    $status_icons = [
                        'submitted' => 'fas fa-file-alt',
                        'reviewing' => 'fas fa-search',
                        'approved' => 'fas fa-check-circle',
                        'rejected' => 'fas fa-times-circle',
                        'completed' => 'fas fa-trophy'
                    ];
                    
                    $type_displays = [
                        'children' => 'เด็กทั่วไป',
                        'disabled' => 'เด็กพิการ'
                    ];
                    
                    $type_icons = [
                        'children' => 'fas fa-baby',
                        'disabled' => 'fas fa-wheelchair'
                    ];
                    
                    $current_status = $kid['kid_aw_ods_status'] ?? 'submitted';
                    $current_type = $kid['kid_aw_ods_type'] ?? 'children';
                    
                    $status_display = $status_displays[$current_status] ?? $current_status;
                    $status_icon = $status_icons[$current_status] ?? 'fas fa-file-alt';
                    $type_display = $type_displays[$current_type] ?? $current_type;
                    $type_icon = $type_icons[$current_type] ?? 'fas fa-baby';
                    ?>
                    
                    <span class="kid-status-badge kid-status-<?php echo $current_status; ?>">
                        <i class="<?php echo $status_icon; ?>"></i>
                        <?php echo htmlspecialchars($status_display); ?>
                    </span>
                    
                    <span class="kid-type-badge">
                        <i class="<?php echo $type_icon; ?>"></i>
                        เงินสนับสนุน<?php echo htmlspecialchars($type_display); ?>
                    </span>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="kid-content-section">
                <h3 class="kid-section-title">
                    <div class="kid-section-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    ข้อมูลผู้ยื่นคำขอ
                </h3>
                
                <div class="kid-info-grid">
                    <div class="kid-info-item">
                        <div class="kid-info-label">ชื่อ-นามสกุล</div>
                        <div class="kid-info-value">
                            <i class="fas fa-user me-2 text-primary"></i>
                            <?php echo htmlspecialchars($kid['kid_aw_ods_by'] ?? ''); ?>
                        </div>
                    </div>
                    
                    <div class="kid-info-item">
                        <div class="kid-info-label">เบอร์โทรศัพท์</div>
                        <div class="kid-info-value">
                            <i class="fas fa-phone me-2 text-success"></i>
                            <?php echo htmlspecialchars($kid['kid_aw_ods_phone'] ?? ''); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($kid['kid_aw_ods_email'])): ?>
                    <div class="kid-info-item">
                        <div class="kid-info-label">อีเมล</div>
                        <div class="kid-info-value">
                            <i class="fas fa-envelope me-2 text-info"></i>
                            <?php echo htmlspecialchars($kid['kid_aw_ods_email']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($kid['kid_aw_ods_number'])): ?>
                    <div class="kid-info-item">
                        <div class="kid-info-label">เลขบัตรประชาชน</div>
                        <div class="kid-info-value">
                            <i class="fas fa-id-card me-2 text-warning"></i>
                            <?php 
                            // ซ่อนเลขบัตรประชาชน
                            $id_card = $kid['kid_aw_ods_number'];
                            $masked_id = substr($id_card, 0, 3) . '-****-****-**-' . substr($id_card, -2);
                            echo htmlspecialchars($masked_id);
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Address Section -->
            <?php if (!empty($kid['kid_aw_ods_address'])): ?>
            <div class="kid-content-section">
                <h3 class="kid-section-title">
                    <div class="kid-section-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    ที่อยู่
                </h3>
                
                <div class="kid-address-card">
                    <div class="kid-address-content">
                        <div class="kid-info-value" style="font-size: 1.1rem; line-height: 1.6;">
                            <i class="fas fa-home me-2 text-primary"></i>
                            <?php echo nl2br(htmlspecialchars($kid['kid_aw_ods_address'])); ?>
                            
                            <?php 
                            // แสดงข้อมูลที่อยู่เพิ่มเติม
                            $address_parts = [];
                            if (!empty($kid['guest_district'])) $address_parts[] = 'ตำบล' . $kid['guest_district'];
                            if (!empty($kid['guest_amphoe'])) $address_parts[] = 'อำเภอ' . $kid['guest_amphoe'];
                            if (!empty($kid['guest_province'])) $address_parts[] = 'จังหวัด' . $kid['guest_province'];
                            if (!empty($kid['guest_zipcode'])) $address_parts[] = $kid['guest_zipcode'];
                            
                            if (!empty($address_parts)): ?>
                                <br><br>
                                <i class="fas fa-location-dot me-2 text-success"></i>
                                <?php echo implode(' ', $address_parts); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Files Section -->
            <?php if (!empty($kid['files']) && is_array($kid['files'])): ?>
            <div class="kid-content-section">
                <h3 class="kid-section-title">
                    <div class="kid-section-icon">
                        <i class="fas fa-paperclip"></i>
                    </div>
                    เอกสารแนบ (<?php echo count($kid['files']); ?> ไฟล์)
                </h3>
                
                <div class="kid-files-grid">
                    <?php foreach ($kid['files'] as $file): 
                        // ใช้ข้อมูลจาก Controller โดยตรง
                        $file_name = $file['kid_aw_ods_file_name'] ?? '';
                        $original_name = $file['kid_aw_ods_file_original_name'] ?? '';
                        $file_type = $file['kid_aw_ods_file_type'] ?? '';
                        $file_size = $file['kid_aw_ods_file_size'] ?? 0;
                        $uploaded_at = $file['kid_aw_ods_file_uploaded_at'] ?? '';
                        $uploaded_by = $file['kid_aw_ods_file_uploaded_by'] ?? '';
                        $file_exists = $file['file_exists'] ?? false;
                        $download_url = $file['download_url'] ?? '';
                        
                        // ประเภทไฟล์
                        $is_pdf = strpos($file_type, 'pdf') !== false;
                        $file_icon_class = $is_pdf ? 'kid-file-icon pdf' : 'kid-file-icon image';
                        $icon_name = $is_pdf ? 'file-pdf' : 'image';
                        
                        // ขนาดไฟล์
                        $file_size_display = '';
                        if ($file_size > 0) {
                            if ($file_size >= 1048576) {
                                $file_size_display = number_format($file_size / 1048576, 2) . ' MB';
                            } else if ($file_size >= 1024) {
                                $file_size_display = number_format($file_size / 1024, 2) . ' KB';
                            } else {
                                $file_size_display = $file_size . ' bytes';
                            }
                        }
                        
                        // วันที่อัปโหลด
                        $upload_date_display = '';
                        if (!empty($uploaded_at)) {
                            $upload_date_display = convertToThaiDate($uploaded_at);
                        }
                    ?>
                    <div class="kid-file-item">
                        <div class="kid-file-header">
                            <div class="<?php echo $file_icon_class; ?>">
                                <i class="fas fa-<?php echo $icon_name; ?>"></i>
                            </div>
                            <div class="kid-file-info">
                                <div class="kid-file-name">
                                    <?php echo htmlspecialchars($original_name); ?>
                                </div>
                                <div class="kid-file-meta">
                                    <?php if ($file_size_display): ?>
                                        <i class="fas fa-weight me-1"></i><?php echo $file_size_display; ?>
                                    <?php endif; ?>
                                    <?php if ($upload_date_display): ?>
                                        <br><i class="fas fa-clock me-1"></i><?php echo $upload_date_display; ?>
                                    <?php endif; ?>
                                    <?php if ($uploaded_by): ?>
                                        <br><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($uploaded_by); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="kid-file-actions">
                            <?php if ($file_exists && !empty($download_url)): ?>
                                <!-- ปุ่ม Download -->
                                <button type="button" 
                                        class="kid-file-btn primary"
                                        onclick="downloadFileButton('<?php echo $download_url; ?>', '<?php echo htmlspecialchars($original_name); ?>')">
                                    <i class="fas fa-download"></i>
                                    ดาวน์โหลด
                                </button>
                                
                                <!-- ปุ่ม View -->
                                <button type="button" 
                                        class="kid-file-btn secondary"
                                        onclick="viewFileButton('<?php echo $download_url; ?>')">
                                    <i class="fas fa-external-link-alt"></i>
                                    ดูไฟล์
                                </button>
                                
                                <!-- ปุ่มลบไฟล์ (เฉพาะสถานะที่แก้ไขได้) -->
                                <?php if ($kid['can_edit'] ?? false): ?>
                                <button type="button" 
                                        class="kid-file-btn danger"
                                        onclick="deleteFileButton('<?php echo $file['file_id'] ?? ''; ?>', '<?php echo htmlspecialchars($original_name); ?>')">
                                    <i class="fas fa-trash"></i>
                                    ลบ
                                </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="kid-file-btn secondary" style="opacity: 0.5; cursor: not-allowed;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    ไฟล์ไม่พร้อมใช้งาน
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="kid-content-section">
                <h3 class="kid-section-title">
                    <div class="kid-section-icon">
                        <i class="fas fa-paperclip"></i>
                    </div>
                    เอกสารแนบ
                </h3>
                
                <div class="kid-empty-state">
                    <div class="kid-empty-icon">
                        <i class="fas fa-file-circle-plus"></i>
                    </div>
                    <h5>ยังไม่มีเอกสารแนบ</h5>
                    <p>ไม่มีไฟล์เอกสารแนบในรายการนี้</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- History Section -->
            <?php if (!empty($kid['history']) && is_array($kid['history'])): ?>
            <div class="kid-content-section">
                <h3 class="kid-section-title">
                    <div class="kid-section-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    ประวัติการดำเนินการ (<?php echo count($kid['history']); ?> รายการ)
                </h3>
                
                <div class="kid-timeline">
                    <?php foreach ($kid['history'] as $history): 
                        $history_data = (object) $history;
                        
                        $action_types = [
                            'created' => 'สร้างรายการ',
                            'updated' => 'แก้ไขข้อมูล',
                            'status_changed' => 'เปลี่ยนสถานะ',
                            'file_uploaded' => 'อัปโหลดไฟล์',
                            'file_deleted' => 'ลบไฟล์',
                            'assigned' => 'มอบหมายงาน',
                            'note_added' => 'เพิ่มหมายเหตุ'
                        ];
                        
                        $action_type = isset($history_data->action_type) ? $history_data->action_type : '';
                        $action_description = isset($history_data->action_description) ? $history_data->action_description : '';
                        $action_date = isset($history_data->action_date) ? $history_data->action_date : '';
                        $action_by = isset($history_data->action_by) ? $history_data->action_by : '';
                        
                        $action_display = isset($action_types[$action_type]) ? $action_types[$action_type] : $action_type;
                    ?>
                    <div class="kid-timeline-item">
                        <div class="kid-timeline-content">
                            <div class="kid-timeline-header">
                                <div class="kid-timeline-action">
                                    <?php echo htmlspecialchars($action_display); ?>
                                </div>
                                <div class="kid-timeline-date">
                                    <?php echo convertToThaiDate($action_date); ?>
                                </div>
                            </div>
                            
                            <div class="kid-timeline-description">
                                <?php echo nl2br(htmlspecialchars($action_description)); ?>
                            </div>
                            
                            <?php if (!empty($action_by)): ?>
                            <div class="kid-timeline-by">
                                โดย: <?php echo htmlspecialchars($action_by); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="kid-content-section">
                <h3 class="kid-section-title">
                    <div class="kid-section-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    ประวัติการดำเนินการ
                </h3>
                
                <div class="kid-empty-state">
                    <div class="kid-empty-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5>ยังไม่มีประวัติการดำเนินการ</h5>
                    <p>ไม่มีข้อมูลประวัติการทำงานในรายการนี้</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="kid-actions">
                <a href="<?php echo site_url('Kid_aw_ods/my_kid_aw_ods'); ?>" 
                   class="kid-action-btn secondary">
                    <i class="fas fa-arrow-left"></i>
                    กลับไปรายการ
                </a>
                
                <?php 
                // ตรวจสอบสถานะเพื่อแสดงปุ่มแก้ไข
                $current_status = $kid['kid_aw_ods_status'] ?? 'submitted';
                $editable_statuses = ['submitted', 'reviewing'];
                $can_edit = in_array($current_status, $editable_statuses);
                ?>
                
                <?php if ($can_edit): ?>
                <button onclick="openEditModal('<?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? ''); ?>')" 
                        class="kid-action-btn warning">
                    <i class="fas fa-edit"></i>
                    แก้ไขข้อมูล
                </button>
                <?php endif; ?>
                
                <button onclick="copyKidId('<?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? ''); ?>')" 
                        class="kid-action-btn secondary">
                    <i class="fas fa-copy"></i>
                    คัดลอกหมายเลข
                </button>
                
                <button onclick="window.print()" class="kid-action-btn secondary">
                    <i class="fas fa-print"></i>
                    พิมพ์รายงาน
                </button>
            </div>
            
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editKidModal" tabindex="-1" aria-labelledby="editKidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: var(--kid-shadow-strong);">
            <div class="modal-header" style="background: var(--kid-gradient-primary); color: white; border-radius: 20px 20px 0 0; border: none;">
                <h5 class="modal-title" id="editKidModalLabel">
                    <i class="fas fa-edit me-2"></i>แก้ไขข้อมูลและเพิ่มเอกสาร
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editKidForm" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 2rem;">
                    <input type="hidden" id="edit_kid_id" name="kid_id">
                    
                    <!-- แสดงข้อมูลปัจจุบัน -->
                    <div class="alert alert-info" style="border-radius: 12px; border: none; background: var(--kid-light-green);">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>ข้อมูลปัจจุบัน</h6>
                        <div id="current_kid_info">
                            <!-- ข้อมูลจะถูกโหลดด้วย JavaScript -->
                        </div>
                    </div>
                    
                    <!-- ฟอร์มแก้ไขข้อมูล -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_kid_phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>เบอร์โทรศัพท์
                            </label>
                            <input type="tel" class="form-control" id="edit_kid_phone" name="kid_phone" 
                                   style="border-radius: 12px; border: 2px solid var(--kid-border-light); padding: 0.75rem;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_kid_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>อีเมล (ไม่บังคับ)
                            </label>
                            <input type="email" class="form-control" id="edit_kid_email" name="kid_email" 
                                   style="border-radius: 12px; border: 2px solid var(--kid-border-light); padding: 0.75rem;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_kid_address" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i>ที่อยู่
                        </label>
                        <textarea class="form-control" id="edit_kid_address" name="kid_address" rows="3"
                                  style="border-radius: 12px; border: 2px solid var(--kid-border-light); padding: 0.75rem;"></textarea>
                    </div>
                    
                    <!-- เพิ่มเอกสาร -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-paperclip me-1"></i>เพิ่มเอกสารประกอบ (รูปภาพ หรือ PDF)
                        </label>
                        <div class="file-upload-area" style="border: 2px dashed var(--kid-border-light); border-radius: 12px; padding: 2rem; text-align: center; background: var(--kid-very-light-green); transition: all 0.3s ease;">
                            <div class="file-upload-icon" style="font-size: 3rem; color: var(--kid-primary-green); margin-bottom: 1rem;">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text" style="color: var(--kid-text-muted); margin-bottom: 1rem;">
                                <strong>คลิกเพื่อเลือกไฟล์</strong> หรือลากไฟล์มาวางที่นี่<br>
                                <small>รองรับไฟล์: JPG, PNG, GIF, PDF (ขนาดไม่เกิน 5MB ต่อไฟล์)</small>
                            </div>
                            <input type="file" id="kid_additional_files" name="kid_additional_files[]" 
                                   multiple accept=".jpg,.jpeg,.png,.gif,.pdf" style="display: none;">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('kid_additional_files').click();" 
                                    style="border-radius: 12px; border: 2px solid var(--kid-primary-green); color: var(--kid-primary-green); padding: 0.75rem 1.5rem;">
                                <i class="fas fa-folder-open me-2"></i>เลือกไฟล์
                            </button>
                        </div>
                        
                        <!-- แสดงไฟล์ที่เลือก -->
                        <div id="selected_files_preview" class="mt-3" style="display: none;">
                            <h6><i class="fas fa-list me-2"></i>ไฟล์ที่เลือก:</h6>
                            <div id="files_list"></div>
                        </div>
                    </div>
                    
                    <!-- แสดงไฟล์ที่มีอยู่แล้ว -->
                    <div id="existing_files_section" class="mb-3">
                        <h6><i class="fas fa-file-alt me-2"></i>เอกสารที่มีอยู่แล้ว:</h6>
                        <div id="existing_files_list">
                            <!-- ไฟล์ที่มีอยู่จะถูกโหลดด้วย JavaScript -->
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer" style="border: none; padding: 1.5rem 2rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" 
                            style="border-radius: 12px; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-times me-2"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary" id="save_kid_btn" 
                            style="background: var(--kid-gradient-primary); border: none; border-radius: 12px; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Load Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('🚀 หน้ารายละเอียดเงินสนับสนุนเด็กพร้อมใช้งาน');
    
    // *** ฟังก์ชันสำหรับดาวน์โหลดไฟล์ ***
    window.downloadFileButton = function(url, filename) {
        console.log('Downloading file:', filename, 'from:', url);
        
        try {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.style.display = 'none';
            
            document.body.appendChild(link);
            link.click();
            
            setTimeout(() => {
                document.body.removeChild(link);
            }, 100);
            
            if (typeof showKidAlert === 'function') {
                showKidAlert('เริ่มดาวน์โหลดไฟล์: ' + filename, 'success');
            }
            
        } catch (error) {
            console.error('Download error:', error);
            window.open(url, '_blank');
            
            if (typeof showKidAlert === 'function') {
                showKidAlert('เปิดไฟล์ในแท็บใหม่: ' + filename, 'info');
            }
        }
    };

    // *** ฟังก์ชันสำหรับดูไฟล์ ***
    window.viewFileButton = function(url) {
        console.log('Viewing file:', url);
        
        try {
            window.open(url, '_blank', 'noopener,noreferrer');
        } catch (error) {
            console.error('View file error:', error);
            window.location.href = url;
        }
    };

    // *** ฟังก์ชันลบไฟล์ ***
    window.deleteFileButton = function(fileId, fileName) {
        if (!fileId) {
            showKidAlert('ไม่พบข้อมูลไฟล์', 'error');
            return;
        }

        Swal.fire({
            title: 'ยืนยันการลบไฟล์',
            text: `คุณต้องการลบไฟล์ "${fileName}" หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบไฟล์',
            cancelButtonText: 'ยกเลิก',
            zIndex: 99999
        }).then((result) => {
            if (result.isConfirmed) {
                deleteFileFromServer(fileId, fileName);
            }
        });
    };

    function deleteFileFromServer(fileId, fileName) {
        const kidId = '<?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? ''); ?>';
        
        $.ajax({
            url: '<?php echo site_url("Kid_aw_ods/delete_kid_file"); ?>',
            type: 'POST',
            data: {
                file_id: fileId,
                kid_id: kidId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showKidAlert(`ลบไฟล์ "${fileName}" สำเร็จ`, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showKidAlert(response.message || 'ไม่สามารถลบไฟล์ได้', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete file error:', error);
                showKidAlert('เกิดข้อผิดพลาดในการลบไฟล์', 'error');
            }
        });
    }

    // *** Copy Function ***
    window.copyKidId = function(kidId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(kidId).then(() => {
                showKidAlert('คัดลอกหมายเลข ' + kidId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopyKidText(kidId);
            });
        } else {
            fallbackCopyKidText(kidId);
        }
    };
    
    function fallbackCopyKidText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showKidAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
        } catch (err) {
            showKidAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** Alert Function ***
    function showKidAlert(message, type) {
        if (typeof Swal !== 'undefined') {
            const iconMap = {
                'success': 'success',
                'error': 'error',
                'warning': 'warning',
                'info': 'info'
            };
            
            Swal.fire({
                icon: iconMap[type] || 'info',
                title: message,
                timer: 3000,
                showConfirmButton: true,
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#10b981',
                zIndex: 99999
            });
        } else {
            alert(message);
        }
    }
    
    // *** Edit Modal Function ***
    window.openEditModal = function(kidId) {
        console.log('📝 เปิด Modal แก้ไข:', kidId);
        
        if (!kidId) {
            showKidAlert('ไม่พบหมายเลขอ้างอิง', 'error');
            return;
        }
        
        $('#edit_kid_id').val(kidId);
        showEditModalLoading();
        
        const modal = new bootstrap.Modal(document.getElementById('editKidModal'));
        modal.show();
        
        loadKidDataFromServer(kidId);
    };
    
    function showEditModalLoading() {
        $('#current_kid_info').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <div class="mt-2">กำลังโหลดข้อมูล...</div>
            </div>
        `);
        
        $('#existing_files_list').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <div class="mt-2">กำลังโหลดไฟล์...</div>
            </div>
        `);
        
        $('#edit_kid_phone').val('');
        $('#edit_kid_email').val('');
        $('#edit_kid_address').val('');
    }
    
    function loadKidDataFromServer(kidId) {
        $.ajax({
            url: '<?php echo site_url("Kid_aw_ods/get_kid_data"); ?>',
            type: 'POST',
            data: { kid_id: kidId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateEditForm(response.data);
                } else {
                    showKidAlert(response.message || 'ไม่สามารถโหลดข้อมูลได้', 'error');
                    bootstrap.Modal.getInstance(document.getElementById('editKidModal')).hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showKidAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                bootstrap.Modal.getInstance(document.getElementById('editKidModal')).hide();
            }
        });
    }
    
    function populateEditForm(data) {
        $('#current_kid_info').html(`
            <div class="row">
                <div class="col-md-6">
                    <strong>หมายเลขอ้างอิง:</strong> ${data.kid_aw_ods_id}<br>
                    <strong>ประเภท:</strong> ${data.kid_aw_ods_type === 'disabled' ? 'เด็กพิการ' : 'เด็กทั่วไป'}<br>
                    <strong>ผู้ยื่นคำขอ:</strong> ${data.kid_aw_ods_by}
                </div>
                <div class="col-md-6">
                    <strong>สถานะ:</strong> ${getStatusDisplay(data.kid_aw_ods_status)}<br>
                    <strong>วันที่ยื่น:</strong> ${formatThaiDate(data.kid_aw_ods_datesave)}
                </div>
            </div>
        `);
        
        $('#edit_kid_phone').val(data.kid_aw_ods_phone || '');
        $('#edit_kid_email').val(data.kid_aw_ods_email || '');
        $('#edit_kid_address').val(data.kid_aw_ods_address || '');
        
        displayExistingFiles(data.files || []);
    }
    
    function displayExistingFiles(files) {
        if (files.length > 0) {
            let filesHtml = '<div class="row">';
            files.forEach(file => {
                const icon = getFileIcon(file.file_type);
                const fileSize = formatFileSize(file.file_size);
                const uploadDate = formatThaiDate(file.uploaded_at);
                
                filesHtml += `
                    <div class="col-md-6 mb-2">
                        <div class="existing-file-item" data-file-id="${file.file_id}" 
                             style="background: var(--kid-light-green); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="${icon}" style="font-size: 1.5rem;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--kid-text-dark);">${file.original_name}</div>
                                <small style="color: var(--kid-text-muted);">${fileSize} • ${uploadDate}</small>
                            </div>
                            <div class="file-actions">
                                <a href="${file.download_url}" class="btn btn-sm btn-outline-primary me-1" 
                                   target="_blank" title="ดาวน์โหลด" style="border-radius: 6px;">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="removeExistingFile('${file.file_id}', '${file.original_name}')" 
                                        style="border-radius: 6px;" title="ลบไฟล์">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            filesHtml += '</div>';
            $('#existing_files_list').html(filesHtml);
        } else {
            $('#existing_files_list').html('<p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>ยังไม่มีเอกสารแนบ</p>');
        }
    }
    
    // *** Helper Functions ***
    function getStatusDisplay(status) {
        const statusMap = {
            'submitted': 'ยื่นเรื่องแล้ว',
            'reviewing': 'กำลังพิจารณา',
            'approved': 'อนุมัติแล้ว',
            'rejected': 'ไม่อนุมัติ',
            'completed': 'เสร็จสิ้น'
        };
        return statusMap[status] || status;
    }
    
    function getFileIcon(fileType) {
        if (fileType && fileType.includes('pdf')) {
            return 'fas fa-file-pdf text-danger';
        } else if (fileType && fileType.includes('image')) {
            return 'fas fa-image text-primary';
        } else {
            return 'fas fa-file text-secondary';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function formatThaiDate(dateString) {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            const thaiMonths = [
                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            const day = date.getDate();
            const month = thaiMonths[date.getMonth()];
            const year = date.getFullYear() + 543;
            const time = date.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
            return `${day} ${month} ${year} เวลา ${time} น.`;
        } catch (e) {
            return dateString;
        }
    }
    
    // *** Remove File Function ***
    window.removeExistingFile = function(fileId, fileName) {
        Swal.fire({
            title: 'ยืนยันการลบไฟล์',
            text: `คุณต้องการลบไฟล์ "${fileName}" หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบไฟล์',
            cancelButtonText: 'ยกเลิก',
            zIndex: 99999
        }).then((result) => {
            if (result.isConfirmed) {
                deleteFileFromServerInModal(fileId, fileName);
            }
        });
    };
    
    function deleteFileFromServerInModal(fileId, fileName) {
        const kidId = $('#edit_kid_id').val();
        
        const fileItem = $(`.existing-file-item[data-file-id="${fileId}"]`);
        fileItem.find('.file-actions').html('<div class="spinner-border spinner-border-sm text-danger" role="status"></div>');
        
        $.ajax({
            url: '<?php echo site_url("Kid_aw_ods/delete_kid_file"); ?>',
            type: 'POST',
            data: {
                file_id: fileId,
                kid_id: kidId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showKidAlert(`ลบไฟล์ "${fileName}" สำเร็จ`, 'success');
                    fileItem.fadeOut(300, function() {
                        $(this).remove();
                        if ($('.existing-file-item').length === 0) {
                            $('#existing_files_list').html('<p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>ยังไม่มีเอกสารแนบ</p>');
                        }
                    });
                } else {
                    showKidAlert(response.message || 'ไม่สามารถลบไฟล์ได้', 'error');
                    // รีเซ็ตปุ่ม
                    fileItem.find('.file-actions').html(`
                        <a href="#" class="btn btn-sm btn-outline-primary me-1" target="_blank" title="ดาวน์โหลด" style="border-radius: 6px;">
                            <i class="fas fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="removeExistingFile('${fileId}', '${fileName}')" 
                                style="border-radius: 6px;" title="ลบไฟล์">
                            <i class="fas fa-trash"></i>
                        </button>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete file error:', error);
                showKidAlert('เกิดข้อผิดพลาดในการลบไฟล์', 'error');
                // รีเซ็ตปุ่ม
                fileItem.find('.file-actions').html(`
                    <a href="#" class="btn btn-sm btn-outline-primary me-1" target="_blank" title="ดาวน์โหลด" style="border-radius: 6px;">
                        <i class="fas fa-download"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="removeExistingFile('${fileId}', '${fileName}')" 
                            style="border-radius: 6px;" title="ลบไฟล์">
                        <i class="fas fa-trash"></i>
                    </button>
                `);
            }
        });
    }
    
    // *** Form Submit ***
    $('#editKidForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#save_kid_btn');
        const originalText = submitBtn.html();
        
        const phone = $('#edit_kid_phone').val().trim();
        if (!phone) {
            showKidAlert('กรุณากรอกเบอร์โทรศัพท์', 'warning');
            $('#edit_kid_phone').focus();
            return;
        }
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?php echo site_url("Kid_aw_ods/update_kid_data"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showKidAlert(response.message || 'บันทึกการเปลี่ยนแปลงสำเร็จ', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editKidModal')).hide();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showKidAlert(response.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Submit error:', error);
                showKidAlert('เกิดข้อผิดพลาดในการบันทึก', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // *** File Upload Handling ***
    $('#kid_additional_files').on('change', function() {
        const files = this.files;
        if (files.length > 0) {
            displaySelectedFiles(files);
        }
    });
    
    function displaySelectedFiles(files) {
        let filesHtml = '';
        let totalSize = 0;
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            totalSize += file.size;
            
            if (!allowedTypes.includes(file.type)) {
                showKidAlert(`ไฟล์ "${file.name}" ไม่ได้รับอนุญาต`, 'error');
                continue;
            }
            
            if (file.size > maxSize) {
                showKidAlert(`ไฟล์ "${file.name}" มีขนาดใหญ่เกิน 5MB`, 'error');
                continue;
            }
            
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileIcon = file.type.includes('image') ? 'fas fa-image text-primary' : 'fas fa-file-pdf text-danger';
            
            filesHtml += `
                <div class="selected-file-item mb-2" style="background: var(--kid-very-light-green); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="${fileIcon}" style="font-size: 1.5rem;"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--kid-text-dark);">${file.name}</div>
                        <small style="color: var(--kid-text-muted);">${fileSize} MB • ${file.type}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="removeSelectedFile(${i})" 
                            style="border-radius: 8px;" title="ลบไฟล์">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }
        
        $('#files_list').html(filesHtml);
        $('#selected_files_preview').show();
        
        const totalSizeMB = (totalSize / 1024 / 1024).toFixed(2);
        $('#files_list').append(`<small class="text-muted">ขนาดรวม: ${totalSizeMB} MB</small>`);
    }
    
    window.removeSelectedFile = function(index) {
        const fileInput = document.getElementById('kid_additional_files');
        const dt = new DataTransfer();
        
        for (let i = 0; i < fileInput.files.length; i++) {
            if (i !== index) {
                dt.items.add(fileInput.files[i]);
            }
        }
        
        fileInput.files = dt.files;
        
        if (fileInput.files.length === 0) {
            $('#selected_files_preview').hide();
        } else {
            displaySelectedFiles(fileInput.files);
        }
    };
    
    // *** Drag and Drop Support ***
    const fileUploadArea = $('.file-upload-area');
    
    fileUploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--kid-primary-green)',
            'background': 'var(--kid-light-green)'
        });
    });
    
    fileUploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--kid-border-light)',
            'background': 'var(--kid-very-light-green)'
        });
    });
    
    fileUploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--kid-border-light)',
            'background': 'var(--kid-very-light-green)'
        });
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('kid_additional_files').files = files;
            displaySelectedFiles(files);
        }
    });
    
    // *** Reset Modal เมื่อปิด ***
    $('#editKidModal').on('hidden.bs.modal', function() {
        $('#editKidForm')[0].reset();
        $('#selected_files_preview').hide();
        $('#save_kid_btn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง');
        $('#current_kid_info').html('');
        $('#existing_files_list').html('');
    });
    
    console.log('✅ หน้ารายละเอียดเงินสนับสนุนเด็กพร้อมใช้งานเต็มรูปแบบ');
});
</script>