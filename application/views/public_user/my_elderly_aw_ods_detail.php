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

// ตรวจสอบข้อมูลเบี้ยยังชีพ
if (empty($elderly_detail)) {
    show_404();
    return;
}

$elderly = $elderly_detail;
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --elderly-primary-blue: #667eea;
    --elderly-secondary-blue: #764ba2;
    --elderly-light-blue: #e8f2ff;
    --elderly-very-light-blue: #f1f8ff;
    --elderly-success-color: #28a745;
    --elderly-warning-color: #ffc107;
    --elderly-danger-color: #dc3545;
    --elderly-info-color: #17a2b8;
    --elderly-purple-color: #6f42c1;
    --elderly-text-dark: #2c3e50;
    --elderly-text-muted: #6c757d;
    --elderly-border-light: rgba(102, 126, 234, 0.1);
    --elderly-shadow-light: 0 4px 20px rgba(102, 126, 234, 0.1);
    --elderly-shadow-medium: 0 8px 30px rgba(102, 126, 234, 0.15);
    --elderly-shadow-strong: 0 15px 40px rgba(102, 126, 234, 0.2);
    --elderly-gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --elderly-gradient-light: linear-gradient(135deg, #f1f8ff 0%, #e8f2ff 100%);
    --elderly-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.elderly-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(102, 126, 234, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(118, 75, 162, 0.03) 0%, transparent 50%);
    min-height: 100vh;
    padding: 2rem 0;
}

.elderly-container-pages {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Card */
.elderly-modern-card {
    background: var(--elderly-gradient-card);
    border-radius: 24px;
    box-shadow: var(--elderly-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(102, 126, 234, 0.08);
}

.elderly-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--elderly-gradient-primary);
}

/* Page Header */
.elderly-page-header {
    padding: 2.5rem;
    background: var(--elderly-gradient-primary);
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.elderly-page-header::before {
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

.elderly-page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
}

.elderly-page-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    position: relative;
    z-index: 2;
}

/* Breadcrumb */
.elderly-breadcrumb {
    padding: 1.5rem 2.5rem;
    background: rgba(102, 126, 234, 0.05);
    border-bottom: 1px solid var(--elderly-border-light);
}

.elderly-breadcrumb a {
    color: var(--elderly-primary-blue);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.elderly-breadcrumb a:hover {
    color: var(--elderly-secondary-blue);
    text-decoration: underline;
}

.elderly-breadcrumb .active {
    color: var(--elderly-text-muted);
    font-weight: 600;
}

/* Detail Header */
.elderly-detail-header {
    padding: 2.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.elderly-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.elderly-id-badge {
    background: var(--elderly-gradient-light);
    color: var(--elderly-primary-blue);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.3rem;
    border: 2px solid rgba(102, 126, 234, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.elderly-date-info {
    color: var(--elderly-text-muted);
    font-size: 1rem;
}

.elderly-status-section {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
}

.elderly-status-badge {
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

.elderly-type-badge {
    background: var(--elderly-gradient-primary);
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
.elderly-content-section {
    padding: 2.5rem;
    border-bottom: 1px solid var(--elderly-border-light);
}

.elderly-content-section:last-child {
    border-bottom: none;
}

.elderly-section-title {
    color: var(--elderly-text-dark);
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.elderly-section-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--elderly-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

/* Info Grid */
.elderly-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.elderly-info-item {
    background: var(--elderly-very-light-blue);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--elderly-primary-blue);
    transition: all 0.3s ease;
}

.elderly-info-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--elderly-shadow-medium);
}

.elderly-info-label {
    color: var(--elderly-text-muted);
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.elderly-info-value {
    color: var(--elderly-text-dark);
    font-size: 1.1rem;
    font-weight: 500;
    line-height: 1.4;
}

/* Address Section */
.elderly-address-card {
    background: var(--elderly-gradient-light);
    padding: 2rem;
    border-radius: 20px;
    border: 2px solid rgba(102, 126, 234, 0.1);
    position: relative;
    overflow: hidden;
}

.elderly-address-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
}

.elderly-address-content {
    position: relative;
    z-index: 2;
}

/* Files Section - แก้ไขให้ download ได้ */
.elderly-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.elderly-file-item {
    background: white;
    border: 2px solid var(--elderly-border-light);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.elderly-file-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--elderly-shadow-strong);
    border-color: var(--elderly-primary-blue);
}

.elderly-file-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.elderly-file-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.elderly-file-icon.pdf {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

.elderly-file-icon.image {
    background: var(--elderly-gradient-primary);
}

.elderly-file-info {
    flex: 1;
}

.elderly-file-name {
    color: var(--elderly-text-dark);
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.3rem;
    line-height: 1.3;
}

.elderly-file-meta {
    color: var(--elderly-text-muted);
    font-size: 0.85rem;
}

.elderly-file-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

/* ปุ่มไฟล์ - แก้ไขให้กดได้ */
.elderly-file-btn {
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

.elderly-file-btn:hover {
    transform: translateY(-2px);
    text-decoration: none !important;
}

.elderly-file-btn.primary {
    background: var(--elderly-gradient-primary) !important;
    color: white !important;
}

.elderly-file-btn.primary:hover {
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white !important;
}

.elderly-file-btn.secondary {
    background: rgba(102, 126, 234, 0.1) !important;
    color: var(--elderly-primary-blue) !important;
    border: 2px solid rgba(102, 126, 234, 0.3);
}

.elderly-file-btn.secondary:hover {
    background: var(--elderly-primary-blue) !important;
    color: white !important;
}

/* ป้องกันการคลิกซ้อนทับ */
.elderly-file-btn.clicking {
    opacity: 0.7;
    pointer-events: none;
    transform: scale(0.95);
}

.elderly-file-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    position: relative;
    z-index: 20;
}

/* History Timeline */
.elderly-timeline {
    position: relative;
    padding-left: 2rem;
}

.elderly-timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--elderly-gradient-primary);
    border-radius: 2px;
}

.elderly-timeline-item {
    position: relative;
    padding-bottom: 2rem;
    margin-left: 1.5rem;
}

.elderly-timeline-item::before {
    content: '';
    position: absolute;
    left: -2.8rem;
    top: 0.5rem;
    width: 16px;
    height: 16px;
    background: var(--elderly-gradient-primary);
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.elderly-timeline-content {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--elderly-border-light);
    box-shadow: var(--elderly-shadow-light);
}

.elderly-timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.elderly-timeline-action {
    color: var(--elderly-text-dark);
    font-weight: 600;
    font-size: 1rem;
}

.elderly-timeline-date {
    color: var(--elderly-text-muted);
    font-size: 0.85rem;
}

.elderly-timeline-description {
    color: var(--elderly-text-dark);
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.elderly-timeline-by {
    color: var(--elderly-text-muted);
    font-size: 0.9rem;
    font-style: italic;
}

/* Action Buttons */
.elderly-actions {
    padding: 2.5rem;
    text-align: center;
    background: var(--elderly-very-light-blue);
}

.elderly-action-btn {
    background: var(--elderly-gradient-primary);
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

.elderly-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.elderly-action-btn.secondary {
    background: rgba(102, 126, 234, 0.1);
    color: var(--elderly-primary-blue);
    border: 2px solid rgba(102, 126, 234, 0.3);
}

.elderly-action-btn.secondary:hover {
    background: var(--elderly-gradient-primary);
    color: white;
    border-color: var(--elderly-primary-blue);
}

/* Status Colors */
.elderly-status-submitted { background: var(--elderly-warning-color) !important; }
.elderly-status-reviewing { background: var(--elderly-info-color) !important; }
.elderly-status-approved { background: var(--elderly-success-color) !important; }
.elderly-status-rejected { background: var(--elderly-danger-color) !important; }
.elderly-status-completed { background: var(--elderly-purple-color) !important; }

/* Empty States */
.elderly-empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--elderly-text-muted);
}

.elderly-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--elderly-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--elderly-primary-blue);
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

.elderly-modern-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .elderly-container-pages {
        padding: 0 0.5rem;
    }
    
    .elderly-detail-header {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    
    .elderly-status-section {
        width: 100%;
        align-items: flex-start;
    }
    
    .elderly-info-grid {
        grid-template-columns: 1fr;
    }
    
    .elderly-files-grid {
        grid-template-columns: 1fr;
    }
    
    .elderly-page-title {
        font-size: 2rem;
    }
    
    .elderly-timeline {
        padding-left: 1rem;
    }
    
    .elderly-timeline-item {
        margin-left: 1rem;
    }
    
    .elderly-timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media print {
    .elderly-actions,
    .elderly-action-btn {
        display: none !important;
    }
    
    .elderly-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<div class="elderly-bg-pages">
    <div class="elderly-container-pages">
        
        <!-- Main Detail Card -->
        <div class="elderly-modern-card">
            
            <!-- Page Header -->
            <div class="elderly-page-header">
                <h1 class="elderly-page-title">
                    <i class="fas fa-file-medical-alt me-3"></i>
                    รายละเอียดเบี้ยยังชีพ
                </h1>
                <p class="elderly-page-subtitle">ข้อมูลการยื่นขอรับเบี้ยยังชีพผู้สูงอายุและผู้พิการ</p>
            </div>

            <!-- Breadcrumb -->
            <div class="elderly-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo site_url(); ?>">
                                <i class="fas fa-home me-1"></i>หน้าแรก
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo site_url('Elderly_aw_ods/my_elderly_aw_ods'); ?>">
                                <i class="fas fa-user-clock me-1"></i>เบี้ยยังชีพของฉัน
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-eye me-1"></i>รายละเอียด #<?php echo htmlspecialchars($elderly->elderly_aw_ods_id ?? ''); ?>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Detail Header -->
            <div class="elderly-detail-header">
                <div class="elderly-id-section">
                    <div class="elderly-id-badge">
                        <i class="fas fa-hashtag me-2"></i>
                        <?php echo htmlspecialchars($elderly->elderly_aw_ods_id ?? ''); ?>
                    </div>
                    <div class="elderly-date-info">
                        <i class="fas fa-calendar-alt me-1"></i>
                        ยื่นเรื่องวันที่: <?php echo convertToThaiDate($elderly->elderly_aw_ods_datesave ?? ''); ?>
                    </div>
                    <?php if (!empty($elderly->elderly_aw_ods_updated_at)): ?>
                    <div class="elderly-date-info">
                        <i class="fas fa-sync-alt me-1"></i>
                        อัปเดตล่าสุด: <?php echo convertToThaiDate($elderly->elderly_aw_ods_updated_at); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="elderly-status-section">
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
                        'elderly' => 'ผู้สูงอายุ',
                        'disabled' => 'ผู้พิการ'
                    ];
                    
                    $type_icons = [
                        'elderly' => 'fas fa-user-clock',
                        'disabled' => 'fas fa-wheelchair'
                    ];
                    
                    $current_status = $elderly->elderly_aw_ods_status ?? 'submitted';
                    $current_type = $elderly->elderly_aw_ods_type ?? 'elderly';
                    
                    $status_display = $status_displays[$current_status] ?? $current_status;
                    $status_icon = $status_icons[$current_status] ?? 'fas fa-file-alt';
                    $type_display = $type_displays[$current_type] ?? $current_type;
                    $type_icon = $type_icons[$current_type] ?? 'fas fa-user-clock';
                    ?>
                    
                    <span class="elderly-status-badge elderly-status-<?php echo $current_status; ?>">
                        <i class="<?php echo $status_icon; ?>"></i>
                        <?php echo htmlspecialchars($status_display); ?>
                    </span>
                    
                    <span class="elderly-type-badge">
                        <i class="<?php echo $type_icon; ?>"></i>
                        เบี้ยยังชีพ<?php echo htmlspecialchars($type_display); ?>
                    </span>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="elderly-content-section">
                <h3 class="elderly-section-title">
                    <div class="elderly-section-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    ข้อมูลผู้ยื่นคำขอ
                </h3>
                
                <div class="elderly-info-grid">
                    <div class="elderly-info-item">
                        <div class="elderly-info-label">ชื่อ-นามสกุล</div>
                        <div class="elderly-info-value">
                            <i class="fas fa-user me-2 text-primary"></i>
                            <?php echo htmlspecialchars($elderly->elderly_aw_ods_by ?? ''); ?>
                        </div>
                    </div>
                    
                    <div class="elderly-info-item">
                        <div class="elderly-info-label">เบอร์โทรศัพท์</div>
                        <div class="elderly-info-value">
                            <i class="fas fa-phone me-2 text-success"></i>
                            <?php echo htmlspecialchars($elderly->elderly_aw_ods_phone ?? ''); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($elderly->elderly_aw_ods_email)): ?>
                    <div class="elderly-info-item">
                        <div class="elderly-info-label">อีเมล</div>
                        <div class="elderly-info-value">
                            <i class="fas fa-envelope me-2 text-info"></i>
                            <?php echo htmlspecialchars($elderly->elderly_aw_ods_email); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($elderly->elderly_aw_ods_number)): ?>
                    <div class="elderly-info-item">
                        <div class="elderly-info-label">เลขบัตรประชาชน</div>
                        <div class="elderly-info-value">
                            <i class="fas fa-id-card me-2 text-warning"></i>
                            <?php 
                            // ซ่อนเลขบัตรประชาชน
                            $id_card = $elderly->elderly_aw_ods_number;
                            $masked_id = substr($id_card, 0, 3) . '-****-****-**-' . substr($id_card, -2);
                            echo htmlspecialchars($masked_id);
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Address Section -->
            <?php if (!empty($elderly->elderly_aw_ods_address)): ?>
            <div class="elderly-content-section">
                <h3 class="elderly-section-title">
                    <div class="elderly-section-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    ที่อยู่
                </h3>
                
                <div class="elderly-address-card">
                    <div class="elderly-address-content">
                        <div class="elderly-info-value" style="font-size: 1.1rem; line-height: 1.6;">
                            <i class="fas fa-home me-2 text-primary"></i>
                            <?php echo nl2br(htmlspecialchars($elderly->elderly_aw_ods_address)); ?>
                            
                            <?php 
                            // แสดงข้อมูลที่อยู่เพิ่มเติม
                            $address_parts = [];
                            if (!empty($elderly->guest_district)) $address_parts[] = 'ตำบล' . $elderly->guest_district;
                            if (!empty($elderly->guest_amphoe)) $address_parts[] = 'อำเภอ' . $elderly->guest_amphoe;
                            if (!empty($elderly->guest_province)) $address_parts[] = 'จังหวัด' . $elderly->guest_province;
                            if (!empty($elderly->guest_zipcode)) $address_parts[] = $elderly->guest_zipcode;
                            
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

            <!-- Files Section - แก้ให้ใช้ข้อมูลจาก Controller -->
            <?php if (!empty($elderly->files) && is_array($elderly->files)): ?>
            <div class="elderly-content-section">
                <h3 class="elderly-section-title">
                    <div class="elderly-section-icon">
                        <i class="fas fa-paperclip"></i>
                    </div>
                    เอกสารแนบ (<?php echo count($elderly->files); ?> ไฟล์)
                </h3>
                
                <div class="elderly-files-grid">
                    <?php foreach ($elderly->files as $file): 
                        // *** ใช้ข้อมูลจาก Controller โดยตรง ***
                        $file_name = $file['elderly_aw_ods_file_name'] ?? '';
                        $original_name = $file['elderly_aw_ods_file_original_name'] ?? '';
                        $file_type = $file['elderly_aw_ods_file_type'] ?? '';
                        $file_size = $file['elderly_aw_ods_file_size'] ?? 0;
                        $uploaded_at = $file['elderly_aw_ods_file_uploaded_at'] ?? '';
                        $uploaded_by = $file['elderly_aw_ods_file_uploaded_by'] ?? '';
                        $file_exists = $file['file_exists'] ?? false;
                        $download_url = $file['download_url'] ?? '';
                        
                        // ประเภทไฟล์
                        $is_pdf = strpos($file_type, 'pdf') !== false;
                        $file_icon_class = $is_pdf ? 'elderly-file-icon pdf' : 'elderly-file-icon image';
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
                    <div class="elderly-file-item">
                        <div class="elderly-file-header">
                            <div class="<?php echo $file_icon_class; ?>">
                                <i class="fas fa-<?php echo $icon_name; ?>"></i>
                            </div>
                            <div class="elderly-file-info">
                                <div class="elderly-file-name">
                                    <?php echo htmlspecialchars($original_name); ?>
                                </div>
                                <div class="elderly-file-meta">
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
                        
                        <div class="elderly-file-actions">
                            <?php if ($file_exists && !empty($download_url)): ?>
                                <!-- ปุ่ม Download -->
                                <button type="button" 
                                        class="elderly-file-btn primary"
                                        onclick="downloadFileButton('<?php echo $download_url; ?>', '<?php echo htmlspecialchars($original_name); ?>')">
                                    <i class="fas fa-download"></i>
                                    ดาวน์โหลด
                                </button>
                                
                                <!-- ปุ่ม View -->
                                <button type="button" 
                                        class="elderly-file-btn secondary"
                                        onclick="viewFileButton('<?php echo $download_url; ?>')">
                                    <i class="fas fa-external-link-alt"></i>
                                    ดูไฟล์
                                </button>
                            <?php else: ?>
                                <span class="elderly-file-btn secondary" style="opacity: 0.5; cursor: not-allowed;">
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
            <div class="elderly-content-section">
                <h3 class="elderly-section-title">
                    <div class="elderly-section-icon">
                        <i class="fas fa-paperclip"></i>
                    </div>
                    เอกสารแนบ
                </h3>
                
                <div class="elderly-empty-state">
                    <div class="elderly-empty-icon">
                        <i class="fas fa-file-circle-plus"></i>
                    </div>
                    <h5>ยังไม่มีเอกสารแนบ</h5>
                    <p>ไม่มีไฟล์เอกสารแนบในรายการนี้</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- History Section -->
            <?php if (!empty($elderly->history) && is_array($elderly->history)): ?>
            <div class="elderly-content-section">
                <h3 class="elderly-section-title">
                    <div class="elderly-section-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    ประวัติการดำเนินการ (<?php echo count($elderly->history); ?> รายการ)
                </h3>
                
                <div class="elderly-timeline">
                    <?php foreach ($elderly->history as $history): 
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
                    <div class="elderly-timeline-item">
                        <div class="elderly-timeline-content">
                            <div class="elderly-timeline-header">
                                <div class="elderly-timeline-action">
                                    <?php echo htmlspecialchars($action_display); ?>
                                </div>
                                <div class="elderly-timeline-date">
                                    <?php echo convertToThaiDate($action_date); ?>
                                </div>
                            </div>
                            
                            <div class="elderly-timeline-description">
                                <?php echo nl2br(htmlspecialchars($action_description)); ?>
                            </div>
                            
                            <?php if (!empty($action_by)): ?>
                            <div class="elderly-timeline-by">
                                โดย: <?php echo htmlspecialchars($action_by); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="elderly-content-section">
                <h3 class="elderly-section-title">
                    <div class="elderly-section-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    ประวัติการดำเนินการ
                </h3>
                
                <div class="elderly-empty-state">
                    <div class="elderly-empty-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5>ยังไม่มีประวัติการดำเนินการ</h5>
                    <p>ไม่มีข้อมูลประวัติการทำงานในรายการนี้</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons - ลบปุ่มติดตามสถานะ -->
            <div class="elderly-actions">
                <a href="<?php echo site_url('Elderly_aw_ods/my_elderly_aw_ods'); ?>" 
                   class="elderly-action-btn secondary">
                    <i class="fas fa-arrow-left"></i>
                    กลับไปรายการ
                </a>
                
                <?php 
                // ตรวจสอบสถานะเพื่อแสดงปุ่มแก้ไข
                $current_status = $elderly->elderly_aw_ods_status ?? 'submitted';
                $editable_statuses = ['submitted', 'reviewing'];
                $can_edit = in_array($current_status, $editable_statuses);
                ?>
                
                
                
                <button onclick="copyElderlyId('<?php echo htmlspecialchars($elderly->elderly_aw_ods_id ?? ''); ?>')" 
                        class="elderly-action-btn secondary">
                    <i class="fas fa-copy"></i>
                    คัดลอกหมายเลข
                </button>
                
                <button onclick="window.print()" class="elderly-action-btn secondary">
                    <i class="fas fa-print"></i>
                    พิมพ์รายงาน
                </button>
            </div>
            
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editElderlyModal" tabindex="-1" aria-labelledby="editElderlyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: var(--elderly-shadow-strong);">
            <div class="modal-header" style="background: var(--elderly-gradient-primary); color: white; border-radius: 20px 20px 0 0; border: none;">
                <h5 class="modal-title" id="editElderlyModalLabel">
                    <i class="fas fa-edit me-2"></i>แก้ไขข้อมูลและเพิ่มเอกสาร
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editElderlyForm" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 2rem;">
                    <input type="hidden" id="edit_elderly_id" name="elderly_id">
                    
                    <!-- แสดงข้อมูลปัจจุบัน -->
                    <div class="alert alert-info" style="border-radius: 12px; border: none; background: var(--elderly-light-blue);">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>ข้อมูลปัจจุบัน</h6>
                        <div id="current_elderly_info">
                            <!-- ข้อมูลจะถูกโหลดด้วย JavaScript -->
                        </div>
                    </div>
                    
                    <!-- ฟอร์มแก้ไขข้อมูล -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_elderly_phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>เบอร์โทรศัพท์
                            </label>
                            <input type="tel" class="form-control" id="edit_elderly_phone" name="elderly_phone" 
                                   style="border-radius: 12px; border: 2px solid var(--elderly-border-light); padding: 0.75rem;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_elderly_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>อีเมล (ไม่บังคับ)
                            </label>
                            <input type="email" class="form-control" id="edit_elderly_email" name="elderly_email" 
                                   style="border-radius: 12px; border: 2px solid var(--elderly-border-light); padding: 0.75rem;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_elderly_address" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i>ที่อยู่
                        </label>
                        <textarea class="form-control" id="edit_elderly_address" name="elderly_address" rows="3"
                                  style="border-radius: 12px; border: 2px solid var(--elderly-border-light); padding: 0.75rem;"></textarea>
                    </div>
                    
                    <!-- เพิ่มเอกสาร -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-paperclip me-1"></i>เพิ่มเอกสารประกอบ (รูปภาพ หรือ PDF)
                        </label>
                        <div class="file-upload-area" style="border: 2px dashed var(--elderly-border-light); border-radius: 12px; padding: 2rem; text-align: center; background: var(--elderly-very-light-blue); transition: all 0.3s ease;">
                            <div class="file-upload-icon" style="font-size: 3rem; color: var(--elderly-primary-blue); margin-bottom: 1rem;">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text" style="color: var(--elderly-text-muted); margin-bottom: 1rem;">
                                <strong>คลิกเพื่อเลือกไฟล์</strong> หรือลากไฟล์มาวางที่นี่<br>
                                <small>รองรับไฟล์: JPG, PNG, GIF, PDF (ขนาดไม่เกิน 5MB ต่อไฟล์)</small>
                            </div>
                            <input type="file" id="elderly_additional_files" name="elderly_additional_files[]" 
                                   multiple accept=".jpg,.jpeg,.png,.gif,.pdf" style="display: none;">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('elderly_additional_files').click();" 
                                    style="border-radius: 12px; border: 2px solid var(--elderly-primary-blue); color: var(--elderly-primary-blue); padding: 0.75rem 1.5rem;">
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
                    <button type="submit" class="btn btn-primary" id="save_elderly_btn" 
                            style="background: var(--elderly-gradient-primary); border: none; border-radius: 12px; padding: 0.75rem 1.5rem;">
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
    console.log('🚀 หน้ารายละเอียดเบี้ยยังชีพพร้อมใช้งาน');
    
    // *** ฟังก์ชันสำหรับดาวน์โหลดไฟล์ - ใช้ button ***
    window.downloadFileButton = function(url, filename) {
        console.log('Downloading file:', filename, 'from:', url);
        
        try {
            // สร้าง link element สำหรับดาวน์โหลด
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.style.display = 'none';
            
            // เพิ่มเข้า DOM และคลิก
            document.body.appendChild(link);
            link.click();
            
            // ลบออกจาก DOM
            setTimeout(() => {
                document.body.removeChild(link);
            }, 100);
            
            // แสดงข้อความสำเร็จ
            if (typeof showElderlyAlert === 'function') {
                showElderlyAlert('เริ่มดาวน์โหลดไฟล์: ' + filename, 'success');
            }
            
        } catch (error) {
            console.error('Download error:', error);
            
            // ถ้าไม่สามารถดาวน์โหลดได้ ให้เปิดในแท็บใหม่
            window.open(url, '_blank');
            
            if (typeof showElderlyAlert === 'function') {
                showElderlyAlert('เปิดไฟล์ในแท็บใหม่: ' + filename, 'info');
            }
        }
    };

    // *** ฟังก์ชันสำหรับดูไฟล์ - ใช้ button ***
    window.viewFileButton = function(url) {
        console.log('Viewing file:', url);
        
        try {
            // เปิดไฟล์ในแท็บใหม่
            window.open(url, '_blank', 'noopener,noreferrer');
            
        } catch (error) {
            console.error('View file error:', error);
            
            // fallback
            window.location.href = url;
        }
    };

    // *** ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่ ***
    function checkFileExists(url, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open('HEAD', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                callback(xhr.status === 200);
            }
        };
        xhr.send();
    }

    // *** Copy Function ***
    window.copyElderlyId = function(elderlyId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(elderlyId).then(() => {
                showElderlyAlert('คัดลอกหมายเลข ' + elderlyId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopyElderlyText(elderlyId);
            });
        } else {
            fallbackCopyElderlyText(elderlyId);
        }
    };
    
    function fallbackCopyElderlyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showElderlyAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
        } catch (err) {
            showElderlyAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** Alert Function ***
    function showElderlyAlert(message, type) {
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
                confirmButtonColor: '#667eea',
                zIndex: 99999
            });
        } else {
            alert(message);
        }
    }
    
    // *** Edit Modal Function ***
    window.openEditModal = function(elderlyId) {
        console.log('📝 เปิด Modal แก้ไข:', elderlyId);
        
        if (!elderlyId) {
            showElderlyAlert('ไม่พบหมายเลขอ้างอิง', 'error');
            return;
        }
        
        $('#edit_elderly_id').val(elderlyId);
        showEditModalLoading();
        
        const modal = new bootstrap.Modal(document.getElementById('editElderlyModal'));
        modal.show();
        
        loadElderlyDataFromServer(elderlyId);
    };
    
    function showEditModalLoading() {
        $('#current_elderly_info').html(`
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
        
        $('#edit_elderly_phone').val('');
        $('#edit_elderly_email').val('');
        $('#edit_elderly_address').val('');
    }
    
    function loadElderlyDataFromServer(elderlyId) {
        $.ajax({
            url: '<?php echo site_url("Elderly_aw_ods/get_elderly_data"); ?>',
            type: 'POST',
            data: { elderly_id: elderlyId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateEditForm(response.data);
                } else {
                    showElderlyAlert(response.message || 'ไม่สามารถโหลดข้อมูลได้', 'error');
                    bootstrap.Modal.getInstance(document.getElementById('editElderlyModal')).hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showElderlyAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                bootstrap.Modal.getInstance(document.getElementById('editElderlyModal')).hide();
            }
        });
    }
    
    function populateEditForm(data) {
        $('#current_elderly_info').html(`
            <div class="row">
                <div class="col-md-6">
                    <strong>หมายเลขอ้างอิง:</strong> ${data.elderly_aw_ods_id}<br>
                    <strong>ประเภท:</strong> ${data.elderly_aw_ods_type === 'elderly' ? 'ผู้สูงอายุ' : 'ผู้พิการ'}<br>
                    <strong>ผู้ยื่นคำขอ:</strong> ${data.elderly_aw_ods_by}
                </div>
                <div class="col-md-6">
                    <strong>สถานะ:</strong> ${getStatusDisplay(data.elderly_aw_ods_status)}<br>
                    <strong>วันที่ยื่น:</strong> ${formatThaiDate(data.elderly_aw_ods_datesave)}
                </div>
            </div>
        `);
        
        $('#edit_elderly_phone').val(data.elderly_aw_ods_phone || '');
        $('#edit_elderly_email').val(data.elderly_aw_ods_email || '');
        $('#edit_elderly_address').val(data.elderly_aw_ods_address || '');
        
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
                             style="background: var(--elderly-light-blue); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="${icon}" style="font-size: 1.5rem;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--elderly-text-dark);">${file.original_name}</div>
                                <small style="color: var(--elderly-text-muted);">${fileSize} • ${uploadDate}</small>
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
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
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
        const elderlyId = $('#edit_elderly_id').val();
        
        const fileItem = $(`.existing-file-item[data-file-id="${fileId}"]`);
        fileItem.find('.file-actions').html('<div class="spinner-border spinner-border-sm text-danger" role="status"></div>');
        
        $.ajax({
            url: '<?php echo site_url("Elderly_aw_ods/delete_elderly_file"); ?>',
            type: 'POST',
            data: {
                file_id: fileId,
                elderly_id: elderlyId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showElderlyAlert(`ลบไฟล์ "${fileName}" สำเร็จ`, 'success');
                    fileItem.fadeOut(300, function() {
                        $(this).remove();
                        if ($('.existing-file-item').length === 0) {
                            $('#existing_files_list').html('<p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>ยังไม่มีเอกสารแนบ</p>');
                        }
                    });
                } else {
                    showElderlyAlert(response.message || 'ไม่สามารถลบไฟล์ได้', 'error');
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
                showElderlyAlert('เกิดข้อผิดพลาดในการลบไฟล์', 'error');
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
    $('#editElderlyForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#save_elderly_btn');
        const originalText = submitBtn.html();
        
        const phone = $('#edit_elderly_phone').val().trim();
        if (!phone) {
            showElderlyAlert('กรุณากรอกเบอร์โทรศัพท์', 'warning');
            $('#edit_elderly_phone').focus();
            return;
        }
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?php echo site_url("Elderly_aw_ods/update_elderly_data"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showElderlyAlert(response.message || 'บันทึกการเปลี่ยนแปลงสำเร็จ', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editElderlyModal')).hide();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showElderlyAlert(response.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Submit error:', error);
                showElderlyAlert('เกิดข้อผิดพลาดในการบันทึก', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // *** File Upload Handling ***
    $('#elderly_additional_files').on('change', function() {
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
                showElderlyAlert(`ไฟล์ "${file.name}" ไม่ได้รับอนุญาต`, 'error');
                continue;
            }
            
            if (file.size > maxSize) {
                showElderlyAlert(`ไฟล์ "${file.name}" มีขนาดใหญ่เกิน 5MB`, 'error');
                continue;
            }
            
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileIcon = file.type.includes('image') ? 'fas fa-image text-primary' : 'fas fa-file-pdf text-danger';
            
            filesHtml += `
                <div class="selected-file-item mb-2" style="background: var(--elderly-very-light-blue); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="${fileIcon}" style="font-size: 1.5rem;"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--elderly-text-dark);">${file.name}</div>
                        <small style="color: var(--elderly-text-muted);">${fileSize} MB • ${file.type}</small>
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
        const fileInput = document.getElementById('elderly_additional_files');
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
            'border-color': 'var(--elderly-primary-blue)',
            'background': 'var(--elderly-light-blue)'
        });
    });
    
    fileUploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--elderly-border-light)',
            'background': 'var(--elderly-very-light-blue)'
        });
    });
    
    fileUploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--elderly-border-light)',
            'background': 'var(--elderly-very-light-blue)'
        });
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('elderly_additional_files').files = files;
            displaySelectedFiles(files);
        }
    });
    
    // *** Reset Modal เมื่อปิด ***
    $('#editElderlyModal').on('hidden.bs.modal', function() {
        $('#editElderlyForm')[0].reset();
        $('#selected_files_preview').hide();
        $('#save_elderly_btn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง');
        $('#current_elderly_info').html('');
        $('#existing_files_list').html('');
    });
    
    // *** ตรวจสอบลิงก์ไฟล์ทั้งหมดเมื่อโหลดหน้า - ลบออก ***
    console.log('🔗 ระบบไฟล์พร้อมใช้งาน - ไฟล์ถูกตรวจสอบจาก Controller แล้ว');
    
    console.log('✅ หน้ารายละเอียดเบี้ยยังชีพพร้อมใช้งานเต็มรูปแบบ');
});
</script>