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

// ฟังก์ชันแปลงขนาดไฟล์
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = array('Bytes', 'KB', 'MB', 'GB');
    $i = floor(log($bytes) / log($k));
    return round(($bytes / pow($k, $i)), 2) . ' ' . $sizes[$i];
}

// ตัวแปรเอกสาร
$doc = $document_detail ?? null;
if (!$doc) {
    show_404();
    return;
}

// ข้อมูลการแสดงผลสถานะ - สีอ่อน
$status_info = [
    'pending' => ['display' => 'รอดำเนินการ', 'class' => 'esv-status-pending', 'icon' => 'fas fa-clock', 'color' => '#fcd34d'],
    'processing' => ['display' => 'กำลังดำเนินการ', 'class' => 'esv-status-processing', 'icon' => 'fas fa-cog fa-spin', 'color' => '#7dd3fc'],
    'completed' => ['display' => 'เสร็จสิ้น', 'class' => 'esv-status-completed', 'icon' => 'fas fa-check-circle', 'color' => '#86efac'],
    'rejected' => ['display' => 'ไม่อนุมัติ', 'class' => 'esv-status-rejected', 'icon' => 'fas fa-times-circle', 'color' => '#fca5a5'],
    'cancelled' => ['display' => 'ยกเลิก', 'class' => 'esv-status-cancelled', 'icon' => 'fas fa-ban', 'color' => '#d1d5db']
];

$current_status = $status_info[$doc->esv_ods_status] ?? $status_info['pending'];
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --esv-primary-blue: #93c5fd;
    --esv-secondary-blue: #60a5fa;
    --esv-light-blue: #f0f9ff;
    --esv-very-light-blue: #f8faff;
    --esv-success-color: #86efac;
    --esv-warning-color: #fcd34d;
    --esv-danger-color: #fca5a5;
    --esv-info-color: #7dd3fc;
    --esv-purple-color: #c4b5fd;
    --esv-text-dark: #1f2937;
    --esv-text-muted: #4b5563;
    --esv-border-light: rgba(147, 197, 253, 0.2);
    --esv-shadow-light: 0 4px 20px rgba(147, 197, 253, 0.15);
    --esv-shadow-medium: 0 8px 30px rgba(147, 197, 253, 0.2);
    --esv-shadow-strong: 0 15px 40px rgba(147, 197, 253, 0.25);
    --esv-gradient-primary: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
    --esv-gradient-light: linear-gradient(135deg, #f8faff 0%, #f0f9ff 100%);
    --esv-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.98) 0%, rgba(255,255,255,0.95) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.esv-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(37, 99, 235, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(29, 78, 216, 0.03) 0%, transparent 50%);
    min-height: 100vh;
    padding: 2rem 0;
}

.esv-container-pages {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.esv-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.esv-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--esv-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.esv-page-title {
    font-size: 2.5rem;
    font-weight: 600;
    background: var(--esv-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.esv-page-subtitle {
    font-size: 1.1rem;
    color: var(--esv-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.esv-modern-card {
    background: var(--esv-gradient-card);
    border-radius: 24px;
    box-shadow: var(--esv-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(37, 99, 235, 0.08);
}

.esv-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--esv-gradient-primary);
    z-index: 1;
}

/* Document Header Card */
.esv-doc-header-card {
    padding: 2.5rem;
    position: relative;
}

.esv-doc-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
    flex-wrap: wrap;
}

.esv-doc-info {
    flex: 1;
}

.esv-doc-id {
    background: var(--esv-gradient-light);
    color: var(--esv-primary-blue);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    margin-bottom: 1rem;
    border: 1px solid rgba(37, 99, 235, 0.2);
}

.esv-doc-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--esv-text-dark);
    margin-bottom: 1rem;
    line-height: 1.3;
}

.esv-doc-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.esv-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--esv-text-muted);
    font-size: 0.95rem;
}

.esv-doc-status {
    text-align: right;
}

.esv-status-badge {
    padding: 1rem 1.5rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1rem;
    color: white;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.esv-doc-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Content Grid */
.esv-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

/* Document Content */
.esv-doc-content {
    padding: 2.5rem;
}

.esv-section {
    margin-bottom: 2.5rem;
}

.esv-section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--esv-text-dark);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.esv-detail-content {
    background: var(--esv-gradient-light);
    padding: 2rem;
    border-radius: 16px;
    border-left: 4px solid var(--esv-primary-blue);
    line-height: 1.7;
    color: var(--esv-text-dark);
}

/* Address Display */
.esv-address-display {
    background: rgba(255, 255, 255, 0.8);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--esv-border-light);
}

/* Sidebar */
.esv-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Info Card */
.esv-info-card {
    padding: 2rem;
}

.esv-info-grid {
    display: grid;
    gap: 1.5rem;
}

.esv-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.esv-info-label {
    font-size: 0.85rem;
    color: var(--esv-text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.esv-info-value {
    font-size: 1rem;
    color: var(--esv-text-dark);
    font-weight: 600;
}

/* Files Card */
.esv-files-card {
    padding: 2rem;
}

.esv-file-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.esv-file-item {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid var(--esv-border-light);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.esv-file-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-medium);
    border-color: var(--esv-primary-blue);
}

.esv-file-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.esv-file-icon.pdf {
    background: linear-gradient(135deg, #fca5a5, #f87171);
}

.esv-file-icon.image {
    background: linear-gradient(135deg, #93c5fd, #7dd3fc);
}

.esv-file-icon.doc {
    background: linear-gradient(135deg, #86efac, #6ee7b7);
}

.esv-file-info {
    flex: 1;
}

.esv-file-name {
    font-weight: 600;
    color: var(--esv-text-dark);
    margin-bottom: 0.3rem;
}

.esv-file-details {
    color: var(--esv-text-muted);
    font-size: 0.9rem;
}

.esv-file-actions {
    display: flex;
    gap: 0.5rem;
}

/* History Card */
.esv-history-card {
    padding: 2rem;
}

.esv-timeline {
    position: relative;
    padding-left: 2rem;
}

.esv-timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--esv-gradient-primary);
}

.esv-timeline-item {
    position: relative;
    padding-bottom: 2rem;
}

.esv-timeline-item::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 1rem;
    height: 1rem;
    background: white;
    border: 3px solid var(--esv-primary-blue);
    border-radius: 50%;
    z-index: 2;
}

.esv-timeline-item:last-child {
    padding-bottom: 0;
}

.esv-timeline-content {
    background: rgba(255, 255, 255, 0.8);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--esv-border-light);
    margin-left: 1rem;
}

.esv-timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.esv-timeline-action {
    font-weight: 600;
    color: var(--esv-text-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.esv-timeline-date {
    color: var(--esv-text-muted);
    font-size: 0.85rem;
    text-align: right;
}

.esv-timeline-description {
    color: var(--esv-text-dark);
    line-height: 1.5;
    margin-top: 0.5rem;
}

.esv-timeline-by {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: var(--esv-text-muted);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Action Buttons */
.esv-action-btn {
    border: none;
    border-radius: 12px;
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.9rem;
}

.esv-action-btn.esv-primary {
    background: var(--esv-gradient-primary);
    color: white;
}

.esv-action-btn.esv-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
    color: white;
    text-decoration: none;
}

.esv-action-btn.esv-secondary {
    background: rgba(37, 99, 235, 0.1);
    color: var(--esv-primary-blue);
    border: 1px solid rgba(37, 99, 235, 0.3);
}

.esv-action-btn.esv-secondary:hover {
    background: var(--esv-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    text-decoration: none;
}

.esv-action-btn.esv-success {
    background: rgba(5, 150, 105, 0.1);
    color: var(--esv-success-color);
    border: 1px solid rgba(5, 150, 105, 0.3);
}

.esv-action-btn.esv-success:hover {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(5, 150, 105, 0.3);
    text-decoration: none;
}

/* Empty States */
.esv-empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--esv-text-muted);
}

.esv-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--esv-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: var(--esv-primary-blue);
}

/* Status Colors - สีอ่อน */
.esv-status-pending { background: #fcd34d !important; color: #92400e !important; }
.esv-status-processing { background: #7dd3fc !important; color: #0c4a6e !important; }
.esv-status-completed { background: #86efac !important; color: #14532d !important; }
.esv-status-rejected { background: #fca5a5 !important; color: #991b1b !important; }
.esv-status-cancelled { background: #d1d5db !important; color: #374151 !important; }

/* Responsive Design */
@media (max-width: 768px) {
    .esv-content-grid {
        grid-template-columns: 1fr;
    }
    
    .esv-doc-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .esv-doc-status {
        text-align: center;
    }
    
    .esv-doc-actions {
        justify-content: center;
    }
    
    .esv-page-title {
        font-size: 2rem;
    }
    
    .esv-doc-title {
        font-size: 1.5rem;
    }
    
    .esv-doc-meta {
        justify-content: center;
    }
    
    .esv-timeline {
        padding-left: 1.5rem;
    }
    
    .esv-timeline::before {
        left: 0.75rem;
    }
    
    .esv-timeline-item::before {
        left: -1.5rem;
    }
    
    .esv-timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .esv-timeline-date {
        text-align: left;
    }
}

@media print {
    .esv-action-btn,
    .esv-doc-actions {
        display: none !important;
    }
    
    .esv-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .esv-bg-pages {
        background: white;
    }
}

/* Edit Modal Styles */
.esv-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
}

.esv-modal-content {
    background: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 800px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: var(--esv-shadow-strong);
    animation: esvModalShow 0.3s ease;
}

.esv-modal-header {
    background: var(--esv-gradient-primary);
    color: white;
    padding: 2rem;
    border-radius: 20px 20px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.esv-modal-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
}

.esv-modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    padding: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.esv-modal-close:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.1);
}

.esv-modal-body {
    padding: 2rem;
}

.esv-form-group {
    margin-bottom: 1.5rem;
}

.esv-form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--esv-text-dark);
}

.esv-form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--esv-border-light);
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.esv-form-control:focus {
    outline: none;
    border-color: var(--esv-primary-blue);
    box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.1);
}

.esv-file-upload-area {
    border: 2px dashed var(--esv-border-light);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: var(--esv-very-light-blue);
    transition: all 0.3s ease;
    cursor: pointer;
}

.esv-file-upload-area:hover {
    border-color: var(--esv-primary-blue);
    background: var(--esv-light-blue);
}

.esv-file-upload-area.dragover {
    border-color: var(--esv-primary-blue);
    background: var(--esv-light-blue);
    transform: scale(1.02);
}

.esv-upload-icon {
    font-size: 3rem;
    color: var(--esv-primary-blue);
    margin-bottom: 1rem;
}

.esv-upload-text {
    color: var(--esv-text-dark);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.esv-upload-hint {
    color: var(--esv-text-muted);
    font-size: 0.9rem;
}

.esv-file-preview {
    margin-top: 1rem;
    display: none;
}

.esv-file-preview-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255,255,255,0.8);
    border: 1px solid var(--esv-border-light);
    border-radius: 10px;
    margin-bottom: 0.5rem;
}

.esv-file-preview-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.esv-file-preview-info {
    flex: 1;
}

.esv-file-preview-name {
    font-weight: 600;
    color: var(--esv-text-dark);
    margin-bottom: 0.2rem;
}

.esv-file-preview-size {
    color: var(--esv-text-muted);
    font-size: 0.9rem;
}

.esv-file-remove {
    background: var(--esv-danger-color);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.esv-file-remove:hover {
    transform: scale(1.1);
}

.esv-modal-footer {
    padding: 2rem;
    border-top: 1px solid var(--esv-border-light);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

/* Animations */
@keyframes esvFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.esv-modern-card {
    animation: esvFadeIn 0.6s ease forwards;
}

.esv-timeline-item {
    animation: esvFadeIn 0.6s ease forwards;
}

.esv-modern-card {
    animation: esvFadeIn 0.6s ease forwards;
}

.esv-timeline-item {
    animation: esvFadeIn 0.6s ease forwards;
}
</style>

<div class="esv-bg-pages">
    <div class="esv-container-pages">
        
        <!-- Page Header -->
        <div class="esv-page-header">
            <div class="esv-header-decoration"></div>
            <h1 class="esv-page-title">
                <i class="fas fa-file-alt me-3"></i>
                รายละเอียดเอกสาร
            </h1>
            <p class="esv-page-subtitle">
                หมายเลขอ้างอิง: <?php echo htmlspecialchars($doc->esv_ods_reference_id); ?>
            </p>
        </div>

        <!-- Document Header Card -->
        <div class="esv-modern-card esv-doc-header-card">
            <div class="esv-doc-header-content">
                <div class="esv-doc-info">
                    <div class="esv-doc-id">
                        <i class="fas fa-hashtag me-2"></i>
                        <?php echo htmlspecialchars($doc->esv_ods_reference_id); ?>
                    </div>
                    
                    <h2 class="esv-doc-title">
                        <?php echo htmlspecialchars($doc->esv_ods_topic ?? 'ไม่ระบุหัวข้อ'); ?>
                    </h2>
                    
                    <div class="esv-doc-meta">
                        <div class="esv-meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>ยื่นวันที่: <?php echo convertToThaiDate($doc->esv_ods_datesave); ?></span>
                        </div>
                        
                        <div class="esv-meta-item">
                            <i class="fas fa-user"></i>
                            <span>ผู้ยื่น: <?php echo htmlspecialchars($doc->esv_ods_by ?? ''); ?></span>
                        </div>
                        
                        <div class="esv-meta-item">
                            <i class="fas fa-building"></i>
                            <span>หน่วยงาน: <?php echo htmlspecialchars($doc->department_name ?? 'ไม่ระบุ'); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="esv-doc-status">
                    <div class="esv-status-badge <?php echo $current_status['class']; ?>" 
                         style="background: <?php echo $current_status['color']; ?>;">
                        <i class="<?php echo $current_status['icon']; ?>"></i>
                        <?php echo $current_status['display']; ?>
                    </div>
                    
                    <div class="esv-doc-actions">
                        <a href="<?php echo site_url('Esv_ods/my_documents'); ?>" 
                           class="esv-action-btn esv-secondary">
                            <i class="fas fa-arrow-left"></i>
                            กลับรายการ
                        </a>
                        
                        <?php 
                        // ตรวจสอบสิทธิ์แก้ไข - แก้ไขได้ถ้าสถานะยังไม่ completed, rejected, cancelled
                        $can_edit = in_array($doc->esv_ods_status, ['pending', 'processing']);
                        ?>
                        
                        <?php if ($can_edit): ?>
                        <button onclick="openEditModal()" class="esv-action-btn esv-success">
                            <i class="fas fa-edit"></i>
                            แก้ไขเอกสาร
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="esv-content-grid">
            
            <!-- Main Content -->
            <div class="esv-main-content">
                
                <!-- Document Detail -->
                <div class="esv-modern-card esv-doc-content">
                    <div class="esv-section">
                        <h3 class="esv-section-title">
                            <i class="fas fa-file-text"></i>
                            รายละเอียดเอกสาร
                        </h3>
                        
                        <?php if (!empty($doc->esv_ods_detail)): ?>
                        <div class="esv-detail-content">
                            <?php echo nl2br(htmlspecialchars($doc->esv_ods_detail)); ?>
                        </div>
                        <?php else: ?>
                        <div class="esv-empty-state">
                            <div class="esv-empty-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <p>ไม่มีรายละเอียดเพิ่มเติม</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                   
                    
                    <!-- Response Section -->
                    <?php if (!empty($doc->esv_ods_response)): ?>
                    <div class="esv-section">
                        <h3 class="esv-section-title">
                            <i class="fas fa-comment-dots"></i>
                            หมายเหตุจากเจ้าหน้าที่
                        </h3>
                        
                        <div class="esv-detail-content">
                            <?php echo nl2br(htmlspecialchars($doc->esv_ods_response)); ?>
                            
                            <?php if (!empty($doc->esv_ods_response_by) && !empty($doc->esv_ods_response_date)): ?>
                            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid rgba(37, 99, 235, 0.2);">
                            <div style="text-align: right; color: var(--esv-text-muted); font-size: 0.9rem;">
                                <i class="fas fa-user"></i> โดย: <?php echo htmlspecialchars($doc->esv_ods_response_by); ?><br>
                                <i class="fas fa-clock"></i> เมื่อ: <?php echo convertToThaiDate($doc->esv_ods_response_date); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Files Section -->
                <div class="esv-modern-card esv-files-card">
                    <h3 class="esv-section-title">
                        <i class="fas fa-paperclip"></i>
                        ไฟล์แนบ (<?php echo count($doc->files ?? []); ?> ไฟล์)
                    </h3>
                    
                    <?php if (!empty($doc->files)): ?>
                    <div class="esv-file-list">
                        <?php foreach ($doc->files as $file): 
                            // กำหนดไอคอนและสีตามประเภทไฟล์
                            $file_ext = strtolower($file->esv_file_extension ?? '');
                            $icon_class = 'esv-file-icon ';
                            $icon = 'fas fa-file';
                            
                            if (in_array($file_ext, ['pdf'])) {
                                $icon_class .= 'pdf';
                                $icon = 'fas fa-file-pdf';
                            } elseif (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $icon_class .= 'image';
                                $icon = 'fas fa-image';
                            } elseif (in_array($file_ext, ['doc', 'docx'])) {
                                $icon_class .= 'doc';
                                $icon = 'fas fa-file-word';
                            } else {
                                $icon_class .= 'pdf';
                            }
                        ?>
                        <div class="esv-file-item">
                            <div class="<?php echo $icon_class; ?>">
                                <i class="<?php echo $icon; ?>"></i>
                            </div>
                            
                            <div class="esv-file-info">
                                <div class="esv-file-name">
                                    <?php echo htmlspecialchars($file->esv_file_original_name ?? $file->esv_file_name); ?>
                                    <?php if (!empty($file->esv_file_is_main) && $file->esv_file_is_main == 1): ?>
                                    <span class="badge bg-primary ms-2" style="font-size: 0.7rem;">ไฟล์หลัก</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="esv-file-details">
                                    ขนาด: <?php echo formatFileSize($file->esv_file_size ?? 0); ?>
                                    <?php if (!empty($file->esv_file_uploaded_at)): ?>
                                    • อัปโหลดเมื่อ: <?php echo convertToThaiDate($file->esv_file_uploaded_at); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="esv-file-actions">
                                <button onclick="viewFile('<?php echo htmlspecialchars($file->esv_file_id ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($file->esv_file_original_name ?? '', ENT_QUOTES); ?>')" 
                                        class="esv-action-btn esv-primary" 
                                        title="เปิดไฟล์ในแท็บใหม่">
                                    <i class="fas fa-external-link-alt"></i>
                                    เปิดไฟล์
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="esv-empty-state">
                        <div class="esv-empty-icon">
                            <i class="fas fa-paperclip"></i>
                        </div>
                        <p>ไม่มีไฟล์แนบ</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="esv-sidebar">
                
                <!-- Document Info -->
                <div class="esv-modern-card esv-info-card">
                    <h3 class="esv-section-title">
                        <i class="fas fa-info-circle"></i>
                        ข้อมูลเอกสาร
                    </h3>
                    
                    <div class="esv-info-grid">
                        <div class="esv-info-item">
                            <div class="esv-info-label">หมายเลขอ้างอิง</div>
                            <div class="esv-info-value"><?php echo htmlspecialchars($doc->esv_ods_reference_id); ?></div>
                        </div>
                        
                        <div class="esv-info-item">
                            <div class="esv-info-label">สถานะปัจจุบัน</div>
                            <div class="esv-info-value"><?php echo $current_status['display']; ?></div>
                        </div>
                        
                        <div class="esv-info-item">
                            <div class="esv-info-label">ประเภทเอกสาร</div>
                            <div class="esv-info-value"><?php echo htmlspecialchars($doc->esv_type_name ?? 'เอกสารทั่วไป'); ?></div>
                        </div>
                        
                        <div class="esv-info-item">
                            <div class="esv-info-label">หมวดหมู่</div>
                            <div class="esv-info-value"><?php echo htmlspecialchars($doc->esv_category_name ?? 'ทั่วไป'); ?></div>
                        </div>
                        
                        <div class="esv-info-item">
                            <div class="esv-info-label">วันที่ยื่น</div>
                            <div class="esv-info-value"><?php echo convertToThaiDate($doc->esv_ods_datesave); ?></div>
                        </div>
                        
                        <?php if (!empty($doc->esv_ods_updated_at)): ?>
                        <div class="esv-info-item">
                            <div class="esv-info-label">อัปเดตล่าสุด</div>
                            <div class="esv-info-value"><?php echo convertToThaiDate($doc->esv_ods_updated_at); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="esv-info-item">
                            <div class="esv-info-label">เบอร์โทรติดต่อ</div>
                            <div class="esv-info-value"><?php echo htmlspecialchars($doc->esv_ods_phone ?? 'ไม่ระบุ'); ?></div>
                        </div>
                        
                        <?php if (!empty($doc->esv_ods_email)): ?>
                        <div class="esv-info-item">
                            <div class="esv-info-label">อีเมล</div>
                            <div class="esv-info-value"><?php echo htmlspecialchars($doc->esv_ods_email); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- History Timeline -->
                <div class="esv-modern-card esv-history-card">
                    <h3 class="esv-section-title">
                        <i class="fas fa-history"></i>
                        ประวัติการดำเนินการ
                    </h3>
                    
                    <?php if (!empty($doc->history)): ?>
                    <div class="esv-timeline">
                        <?php 
                        // เรียงลำดับประวัติจากใหม่ไปเก่า
                        $sorted_history = array_reverse($doc->history); 
                        foreach ($sorted_history as $index => $history): 
                            // กำหนดไอคอนและสีตามประเภทการดำเนินการ
                            $action_icon = 'fas fa-circle';
                            $action_color = '#6b7280';
                            $action_display = htmlspecialchars($history->esv_history_action ?? 'การดำเนินการ');
                            
                            switch(strtolower($history->esv_history_action ?? '')) {
                                case 'created':
                                    $action_icon = 'fas fa-plus-circle';
                                    $action_color = '#10b981';
                                    $action_display = 'สร้างเอกสาร';
                                    break;
                                case 'updated':
                                    $action_icon = 'fas fa-edit';
                                    $action_color = '#3b82f6';
                                    $action_display = 'แก้ไขเอกสาร';
                                    break;
                                case 'status_changed':
                                    $action_icon = 'fas fa-sync-alt';
                                    $action_color = '#f59e0b';
                                    $action_display = 'เปลี่ยนสถานะ';
                                    break;
                                case 'completed':
                                    $action_icon = 'fas fa-check-circle';
                                    $action_color = '#059669';
                                    $action_display = 'เสร็จสิ้น';
                                    break;
                                case 'rejected':
                                    $action_icon = 'fas fa-times-circle';
                                    $action_color = '#dc2626';
                                    $action_display = 'ไม่อนุมัติ';
                                    break;
                                case 'note_added':
                                    $action_icon = 'fas fa-sticky-note';
                                    $action_color = '#8b5cf6';
                                    $action_display = 'เพิ่มหมายเหตุ';
                                    break;
                                default:
                                    // ใช้ค่าเดิม
                                    break;
                            }
                        ?>
                        <div class="esv-timeline-item">
                            <div class="esv-timeline-content">
                                <div class="esv-timeline-header">
                                    <div class="esv-timeline-action">
                                        <i class="<?php echo $action_icon; ?>" style="color: <?php echo $action_color; ?>;"></i>
                                        <?php echo $action_display; ?>
                                    </div>
                                    <div class="esv-timeline-date">
                                        <?php echo convertToThaiDate($history->esv_history_created_at ?? ''); ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($history->esv_history_description)): ?>
                                <div class="esv-timeline-description">
                                    <?php echo nl2br(htmlspecialchars($history->esv_history_description)); ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($history->esv_history_by)): ?>
                                <div class="esv-timeline-by">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($history->esv_history_by); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="esv-empty-state">
                        <div class="esv-empty-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <p>ไม่มีประวัติการดำเนินการ</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="esv-modal">
    <div class="esv-modal-content">
        <div class="esv-modal-header">
            <h3 class="esv-modal-title">
                <i class="fas fa-edit me-2"></i>
                แก้ไขเอกสาร
            </h3>
            <button class="esv-modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editDocumentForm" enctype="multipart/form-data">
            <div class="esv-modal-body">
                <input type="hidden" id="document_id" value="<?php echo $doc->esv_ods_id; ?>">
                
                <div class="esv-form-group">
                    <label class="esv-form-label">
                        <i class="fas fa-heading me-1"></i>
                        หัวข้อเอกสาร
                    </label>
                    <input type="text" class="esv-form-control" id="edit_topic" 
                           value="<?php echo htmlspecialchars($doc->esv_ods_topic ?? '', ENT_QUOTES); ?>" 
                           required>
                </div>
                
                <div class="esv-form-group">
                    <label class="esv-form-label">
                        <i class="fas fa-file-text me-1"></i>
                        รายละเอียดเอกสาร
                    </label>
                    <textarea class="esv-form-control" id="edit_detail" rows="5" required><?php echo htmlspecialchars($doc->esv_ods_detail ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="esv-form-group">
                            <label class="esv-form-label">
                                <i class="fas fa-phone me-1"></i>
                                เบอร์โทรศัพท์
                            </label>
                            <input type="tel" class="esv-form-control" id="edit_phone" 
                                   value="<?php echo htmlspecialchars($doc->esv_ods_phone ?? '', ENT_QUOTES); ?>" 
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="esv-form-group">
                            <label class="esv-form-label">
                                <i class="fas fa-envelope me-1"></i>
                                อีเมล (ไม่บังคับ)
                            </label>
                            <input type="email" class="esv-form-control" id="edit_email" 
                                   value="<?php echo htmlspecialchars($doc->esv_ods_email ?? '', ENT_QUOTES); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="esv-form-group">
                    <label class="esv-form-label">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        ที่อยู่ติดต่อ
                    </label>
                    <textarea class="esv-form-control" id="edit_address" rows="3" required><?php echo htmlspecialchars($doc->esv_ods_address ?? ''); ?></textarea>
                </div>
                
                <div class="esv-form-group">
                    <label class="esv-form-label">
                        <i class="fas fa-paperclip me-1"></i>
                        เพิ่มไฟล์แนบ (ไม่เกิน 5 ไฟล์, รวมไม่เกิน 15MB)
                    </label>
                    
                    <div class="esv-file-upload-area" onclick="document.getElementById('additional_files').click()">
                        <div class="esv-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="esv-upload-text">คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</div>
                        <div class="esv-upload-hint">รองรับ: JPG, PNG, PDF, DOC, DOCX (ไม่เกิน 5MB ต่อไฟล์)</div>
                        <input type="file" id="additional_files" name="additional_files[]" 
                               style="display: none;" multiple 
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
                    
                    <div id="file_preview" class="esv-file-preview">
                        <h6 style="margin: 1rem 0 0.5rem 0; color: var(--esv-text-dark);">
                            <i class="fas fa-list me-1"></i>ไฟล์ที่เลือก:
                        </h6>
                        <div id="file_list"></div>
                        <div id="file_summary" style="margin-top: 0.5rem; color: var(--esv-text-muted); font-size: 0.9rem;"></div>
                    </div>
                </div>
            </div>
            
            <div class="esv-modal-footer">
                <button type="button" class="esv-action-btn esv-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times me-2"></i>ยกเลิก
                </button>
                <button type="submit" class="esv-action-btn esv-success" id="save_btn">
                    <i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Load Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('🚀 เริ่มต้นหน้ารายละเอียดเอกสาร');
    
    // ตัวแปรสำหรับจัดการไฟล์
    let selectedFiles = [];
    const maxFiles = 5;
    const maxTotalSize = 15 * 1024 * 1024; // 15MB
    const maxFileSize = 5 * 1024 * 1024; // 5MB per file
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    // ฟังก์ชันดูไฟล์
    window.viewFile = function(fileId, fileName) {
        if (!fileId) {
            showAlert('ไม่พบข้อมูลไฟล์', 'error');
            return;
        }
        
        // แสดง loading
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังเปิด...';
        button.disabled = true;
        
        // สร้าง URL สำหรับดูไฟล์
        const viewUrl = '<?php echo site_url("Esv_ods/view_file/"); ?>' + fileId;
        
        // เปิดในแท็บใหม่
        const newWindow = window.open(viewUrl, '_blank');
        
        // ตรวจสอบว่าเปิดได้หรือไม่
        if (!newWindow) {
            showAlert('กรุณาอนุญาตให้เปิด Pop-up ในเบราว์เซอร์', 'warning');
        }
        
        // คืนสถานะปุ่ม
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
    };
    
    // ฟังก์ชันเปิด Modal แก้ไข
    window.openEditModal = function() {
        document.getElementById('editModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    };
    
    // ฟังก์ชันปิด Modal แก้ไข
    window.closeEditModal = function() {
        document.getElementById('editModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // รีเซ็ตฟอร์ม
        resetEditForm();
    };
    
    // รีเซ็ตฟอร์ม
    function resetEditForm() {
        selectedFiles = [];
        updateFilePreview();
        document.getElementById('additional_files').value = '';
    }
    
    // จัดการการเลือกไฟล์
    document.getElementById('additional_files').addEventListener('change', function(e) {
        handleFileSelection(e.target.files);
    });
    
    // Drag and Drop
    const uploadArea = document.querySelector('.esv-file-upload-area');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragover'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragover'), false);
    });
    
    uploadArea.addEventListener('drop', function(e) {
        const files = e.dataTransfer.files;
        handleFileSelection(files);
    });
    
    // จัดการการเลือกไฟล์
    function handleFileSelection(files) {
        const newFiles = Array.from(files);
        
        // ตรวจสอบจำนวนไฟล์
        if (selectedFiles.length + newFiles.length > maxFiles) {
            showAlert(`สามารถเลือกได้ไม่เกิน ${maxFiles} ไฟล์`, 'warning');
            return;
        }
        
        let totalSize = selectedFiles.reduce((sum, file) => sum + file.size, 0);
        
        for (let file of newFiles) {
            // ตรวจสอบประเภทไฟล์
            if (!allowedTypes.includes(file.type)) {
                showAlert(`ไฟล์ "${file.name}" ไม่ได้รับอนุญาต`, 'warning');
                continue;
            }
            
            // ตรวจสอบขนาดไฟล์
            if (file.size > maxFileSize) {
                showAlert(`ไฟล์ "${file.name}" มีขนาดใหญ่เกิน 5MB`, 'warning');
                continue;
            }
            
            // ตรวจสอบขนาดรวม
            if (totalSize + file.size > maxTotalSize) {
                showAlert('ขนาดไฟล์รวมเกิน 15MB', 'warning');
                break;
            }
            
            selectedFiles.push(file);
            totalSize += file.size;
        }
        
        updateFilePreview();
    }
    
    // อัปเดตการแสดงไฟล์
    function updateFilePreview() {
        const preview = document.getElementById('file_preview');
        const fileList = document.getElementById('file_list');
        const summary = document.getElementById('file_summary');
        
        if (selectedFiles.length === 0) {
            preview.style.display = 'none';
            return;
        }
        
        preview.style.display = 'block';
        fileList.innerHTML = '';
        
        let totalSize = 0;
        
        selectedFiles.forEach((file, index) => {
            totalSize += file.size;
            
            const item = document.createElement('div');
            item.className = 'esv-file-preview-item';
            
            const icon = getFileIcon(file.type);
            const size = formatFileSize(file.size);
            
            item.innerHTML = `
                <div class="esv-file-preview-icon" style="background: ${icon.color};">
                    <i class="${icon.class}"></i>
                </div>
                <div class="esv-file-preview-info">
                    <div class="esv-file-preview-name">${file.name}</div>
                    <div class="esv-file-preview-size">${size}</div>
                </div>
                <button type="button" class="esv-file-remove" onclick="removeFile(${index})" title="ลบไฟล์">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            fileList.appendChild(item);
        });
        
        summary.innerHTML = `
            <i class="fas fa-info-circle me-1"></i>
            ${selectedFiles.length} ไฟล์ | ขนาดรวม: ${formatFileSize(totalSize)} / 15MB
        `;
    }
    
    // ลบไฟล์
    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        updateFilePreview();
    };
    
    // ไอคอนไฟล์
    function getFileIcon(type) {
        if (type.includes('pdf')) {
            return { class: 'fas fa-file-pdf', color: '#fca5a5' };
        } else if (type.includes('image')) {
            return { class: 'fas fa-image', color: '#93c5fd' };
        } else if (type.includes('word')) {
            return { class: 'fas fa-file-word', color: '#86efac' };
        }
        return { class: 'fas fa-file', color: '#d1d5db' };
    }
    
    // ฟอร์แมตขนาดไฟล์
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Submit ฟอร์มแก้ไข
    document.getElementById('editDocumentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('save_btn');
        const originalText = saveBtn.innerHTML;
        
        // แสดง loading
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...';
        saveBtn.disabled = true;
        
        // เตรียมข้อมูล
        const formData = new FormData();
        formData.append('document_id', document.getElementById('document_id').value);
        formData.append('topic', document.getElementById('edit_topic').value);
        formData.append('detail', document.getElementById('edit_detail').value);
        formData.append('phone', document.getElementById('edit_phone').value);
        formData.append('email', document.getElementById('edit_email').value);
        formData.append('address', document.getElementById('edit_address').value);
        
        // เพิ่มไฟล์
        selectedFiles.forEach((file) => {
            formData.append('additional_files[]', file);
        });
        
        // ส่งข้อมูล
        fetch('<?php echo site_url("Esv_ods/update_my_document"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message || 'บันทึกการเปลี่ยนแปลงสำเร็จ', 'success');
                closeEditModal();
                
                // รีโหลดหน้า
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(data.message || 'ไม่สามารถบันทึกได้', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('เกิดข้อผิดพลาดในการบันทึก', 'error');
        })
        .finally(() => {
            // คืนสถานะปุ่ม
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    });
    
    function showAlert(message, type) {
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
                confirmButtonColor: '#93c5fd'
            });
        } else {
            alert(message);
        }
    }
    
    // ปิด Modal เมื่อคลิกนอกพื้นที่
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('editModal');
        if (e.target === modal) {
            closeEditModal();
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape = ปิด modal หรือกลับหน้าก่อน
        if (e.key === 'Escape') {
            const modal = document.getElementById('editModal');
            if (modal.style.display === 'block') {
                closeEditModal();
            } else {
                window.history.back();
            }
        }
    });
    
    // เพิ่ม function สำหรับจัดการ Timeline animation
    function animateTimeline() {
        const timelineItems = document.querySelectorAll('.esv-timeline-item');
        timelineItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.6s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 200);
        });
    }
    
    // เรียกใช้ animation เมื่อโหลดหน้าเสร็จ
    setTimeout(animateTimeline, 500);
    
    console.log('✅ หน้ารายละเอียดเอกสารพร้อมใช้งาน');
});

// Print function
function printDocument() {
    window.print();
}

// Share function (ถ้าต้องการ)
function shareDocument() {
    if (navigator.share) {
        navigator.share({
            title: 'เอกสาร <?php echo htmlspecialchars($doc->esv_ods_topic, ENT_QUOTES); ?>',
            text: 'หมายเลขอ้างอิง: <?php echo htmlspecialchars($doc->esv_ods_reference_id, ENT_QUOTES); ?>',
            url: window.location.href
        });
    } else {
        // Fallback - copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'คัดลอกลิงก์สำเร็จ',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }
}
</script>