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

// ฟังก์ชันแปลงสถานะ
function getStatusDisplay($status) {
    $status_map = [
        'pending' => 'รอดำเนินการ',
        'under_review' => 'กำลังตรวจสอบ',
        'investigating' => 'กำลังสอบสวน',
        'resolved' => 'แก้ไขแล้ว',
        'dismissed' => 'ไม่อนุมัติ',
        'closed' => 'ปิดเรื่อง'
    ];
    return $status_map[$status] ?? $status;
}

// ฟังก์ชันแปลงประเภทการทุจริต
function getCorruptionTypeDisplay($type) {
    $type_map = [
        'embezzlement' => 'การยักยอกเงิน',
        'bribery' => 'การรับสินบน',
        'abuse_of_power' => 'การใช้อำนาจหน้าที่มิชอบ',
        'conflict_of_interest' => 'ความขัดแย้งทางผลประโยชน์',
        'procurement_fraud' => 'การทุจริตในการจัดซื้อจัดจ้าง',
        'other' => 'อื่นๆ'
    ];
    return $type_map[$type] ?? $type;
}

// ข้อมูลรายงาน (จำลอง)
$report = $report_detail ?? null;
if (!$report) {
    redirect('Corruption/my_reports');
    return;
}

// ข้อมูลสถานะและไอคอน
$status_info = [
    'pending' => ['icon' => 'fas fa-clock', 'color' => '#ffc107', 'bg' => 'warning'],
    'under_review' => ['icon' => 'fas fa-search', 'color' => '#17a2b8', 'bg' => 'info'],
    'investigating' => ['icon' => 'fas fa-cogs', 'color' => '#007bff', 'bg' => 'primary'],
    'resolved' => ['icon' => 'fas fa-check-circle', 'color' => '#28a745', 'bg' => 'success'],
    'dismissed' => ['icon' => 'fas fa-times-circle', 'color' => '#dc3545', 'bg' => 'danger'],
    'closed' => ['icon' => 'fas fa-archive', 'color' => '#6c757d', 'bg' => 'secondary']
];

$current_status = $status_info[$report->report_status] ?? $status_info['pending'];
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --corrupt-primary-red: #dc3545;
    --corrupt-secondary-red: #c82333;
    --corrupt-light-red: #f8d7da;
    --corrupt-very-light-red: #fdf2f2;
    --corrupt-success-color: #28a745;
    --corrupt-warning-color: #ffc107;
    --corrupt-danger-color: #dc3545;
    --corrupt-info-color: #17a2b8;
    --corrupt-purple-color: #6f42c1;
    --corrupt-text-dark: #2c3e50;
    --corrupt-text-muted: #6c757d;
    --corrupt-border-light: rgba(220, 53, 69, 0.1);
    --corrupt-shadow-light: 0 4px 20px rgba(220, 53, 69, 0.1);
    --corrupt-shadow-medium: 0 8px 30px rgba(220, 53, 69, 0.15);
    --corrupt-shadow-strong: 0 15px 40px rgba(220, 53, 69, 0.2);
    --corrupt-gradient-primary: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    --corrupt-gradient-light: linear-gradient(135deg, #fdf2f2 0%, #f8d7da 100%);
    --corrupt-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.corrupt-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(220, 53, 69, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(200, 35, 51, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(220, 53, 69, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.corrupt-container-pages {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.corrupt-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.corrupt-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--corrupt-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.corrupt-header-decoration::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: corruptShimmer 2s infinite;
}

.corrupt-page-title {
    font-size: 2.5rem;
    font-weight: 600;
    background: var(--corrupt-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.corrupt-page-subtitle {
    font-size: 1.1rem;
    color: var(--corrupt-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.corrupt-modern-card {
    background: var(--corrupt-gradient-card);
    border-radius: 24px;
    box-shadow: var(--corrupt-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(220, 53, 69, 0.08);
    z-index: 50;
}

.corrupt-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--corrupt-gradient-primary);
    z-index: 1;
}

/* Report Header Card */
.corrupt-report-header {
    padding: 2.5rem;
    position: relative;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, transparent 100%);
}

.corrupt-report-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
    flex-wrap: wrap;
}

.corrupt-report-id-section {
    flex: 1;
}

.corrupt-report-id-badge {
    background: var(--corrupt-gradient-primary);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.3rem;
    display: inline-flex;
    align-items: center;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.corrupt-report-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--corrupt-text-dark);
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.corrupt-report-date {
    color: var(--corrupt-text-muted);
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corrupt-status-section {
    text-align: right;
}

.corrupt-status-badge-large {
    background: <?php echo $current_status['color']; ?>;
    color: white;
    padding: 1rem 2rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    margin-bottom: 0.5rem;
}

.corrupt-status-description {
    color: var(--corrupt-text-muted);
    font-size: 0.9rem;
}

/* Report Content Sections */
.corrupt-content-section {
    padding: 2.5rem;
    border-bottom: 1px solid var(--corrupt-border-light);
}

.corrupt-content-section:last-child {
    border-bottom: none;
}

.corrupt-section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.corrupt-section-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--corrupt-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--corrupt-primary-red);
    font-size: 1.2rem;
}

.corrupt-section-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--corrupt-text-dark);
    margin: 0;
}

/* Info Grid */
.corrupt-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.corrupt-info-item {
    background: rgba(255, 255, 255, 0.7);
    padding: 1.5rem;
    border-radius: 15px;
    border-left: 4px solid var(--corrupt-primary-red);
    transition: all 0.3s ease;
}

.corrupt-info-item:hover {
    transform: translateY(-3px);
    box-shadow: var(--corrupt-shadow-medium);
}

.corrupt-info-label {
    color: var(--corrupt-text-muted);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.corrupt-info-value {
    color: var(--corrupt-text-dark);
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
}

/* Text Content */
.corrupt-text-content {
    background: var(--corrupt-gradient-light);
    padding: 2rem;
    border-radius: 15px;
    border-left: 4px solid var(--corrupt-primary-red);
    margin-bottom: 1.5rem;
}

.corrupt-text-content p {
    color: var(--corrupt-text-dark);
    line-height: 1.7;
    margin-bottom: 0;
    font-size: 1.1rem;
}

/* Files Section */
.corrupt-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.corrupt-file-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--corrupt-border-light);
    border-radius: 15px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.corrupt-file-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--corrupt-gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.corrupt-file-item:hover::before {
    transform: scaleX(1);
}

.corrupt-file-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--corrupt-shadow-medium);
}

.corrupt-file-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.corrupt-file-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: white;
}

.corrupt-file-icon.pdf { background: linear-gradient(135deg, #dc3545, #c82333); }
.corrupt-file-icon.image { background: linear-gradient(135deg, #17a2b8, #138496); }
.corrupt-file-icon.document { background: linear-gradient(135deg, #28a745, #1e7e34); }
.corrupt-file-icon.other { background: linear-gradient(135deg, #6c757d, #545b62); }

.corrupt-file-info h6 {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
    line-height: 1.3;
}

.corrupt-file-meta {
    color: var(--corrupt-text-muted);
    font-size: 0.9rem;
}

.corrupt-file-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

/* File Viewer Enhancements */
.corrupt-file-preview-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.corrupt-file-preview-icon:hover {
    background: var(--corrupt-primary-red);
    transform: scale(1.1);
}

/* PDF Viewer Styles */
.pdf-viewer-container {
    width: 100%;
    height: 80vh;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.pdf-viewer-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* Timeline */
.corrupt-timeline {
    position: relative;
    padding-left: 2rem;
}

.corrupt-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--corrupt-gradient-primary);
}

.corrupt-timeline-item {
    position: relative;
    margin-bottom: 2rem;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    padding: 1.5rem;
    margin-left: 1rem;
    border: 1px solid var(--corrupt-border-light);
}

.corrupt-timeline-item::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 1.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--corrupt-primary-red);
    border: 3px solid white;
    box-shadow: 0 0 0 2px var(--corrupt-primary-red);
}

.corrupt-timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.corrupt-timeline-title {
    font-weight: 600;
    color: var(--corrupt-text-dark);
    margin: 0;
}

.corrupt-timeline-date {
    color: var(--corrupt-text-muted);
    font-size: 0.9rem;
}

.corrupt-timeline-content {
    color: var(--corrupt-text-dark);
    line-height: 1.6;
}

/* Action Buttons */
.corrupt-actions-card {
    padding: 2.5rem;
    text-align: center;
}

.corrupt-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.corrupt-action-btn {
    border: none;
    border-radius: 15px;
    padding: 1rem 1.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 1rem;
    min-height: 50px;
}

.corrupt-action-btn.corrupt-primary {
    background: var(--corrupt-gradient-primary);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.corrupt-action-btn.corrupt-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
    color: white;
    text-decoration: none;
}

.corrupt-action-btn.corrupt-secondary {
    background: rgba(220, 53, 69, 0.1);
    color: var(--corrupt-primary-red);
    border: 2px solid rgba(220, 53, 69, 0.3);
}

.corrupt-action-btn.corrupt-secondary:hover {
    background: var(--corrupt-gradient-primary);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
    text-decoration: none;
}

.corrupt-action-btn.corrupt-warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: white;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.corrupt-action-btn.corrupt-warning:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
    color: white;
    text-decoration: none;
}

.corrupt-action-btn.corrupt-info {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}

.corrupt-action-btn.corrupt-info:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
    color: white;
    text-decoration: none;
}

.corrupt-action-btn.corrupt-success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.corrupt-action-btn.corrupt-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    color: white;
    text-decoration: none;
}

/* Empty State */
.corrupt-empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--corrupt-text-muted);
}

.corrupt-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--corrupt-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--corrupt-primary-red);
}

/* Modal Enhancements */
.modal-xl {
    max-width: 95vw;
}

.image-container {
    max-height: 80vh;
    overflow: auto;
    border-radius: 8px;
    background: #f8f9fa;
    padding: 1rem;
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

/* Animations */
@keyframes corruptShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes corruptFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .corrupt-page-title {
        font-size: 2rem;
    }
    
    .corrupt-report-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .corrupt-status-section {
        text-align: center;
    }
    
    .corrupt-info-grid {
        grid-template-columns: 1fr;
    }
    
    .corrupt-files-grid {
        grid-template-columns: 1fr;
    }
    
    .corrupt-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .corrupt-timeline {
        padding-left: 1.5rem;
    }
    
    .corrupt-timeline-item {
        margin-left: 0.5rem;
    }
    
    .corrupt-timeline-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .corrupt-file-actions {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .corrupt-content-section {
        padding: 1.5rem;
    }
    
    .corrupt-report-header {
        padding: 1.5rem;
    }
    
    .corrupt-report-id-badge {
        font-size: 1.1rem;
        padding: 0.8rem 1.2rem;
    }
    
    .corrupt-report-title {
        font-size: 1.5rem;
    }
    
    .corrupt-status-badge-large {
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
    }
}

/* Print Styles */
@media print {
    .corrupt-actions-card,
    .corrupt-action-btn {
        display: none !important;
    }
    
    .corrupt-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
    }
    
    .corrupt-page-title {
        color: #333 !important;
        -webkit-text-fill-color: #333 !important;
    }
    
    .corrupt-timeline-item,
    .corrupt-info-item,
    .corrupt-file-item {
        break-inside: avoid;
    }
}
</style>

<div class="corrupt-bg-pages">
    <div class="corrupt-container-pages">
        
        <!-- Page Header -->
        <div class="corrupt-page-header">
            <div class="corrupt-header-decoration"></div>
            <h1 class="corrupt-page-title">
                <i class="fas fa-file-search me-3"></i>
                รายละเอียดรายงานแจ้งการทุจริตและประพฤติมิชอบ
            </h1>
            <p class="corrupt-page-subtitle">ข้อมูลครบถ้วนของรายงาน #<?php echo htmlspecialchars($report->corruption_report_id ?? ''); ?></p>
        </div>

        <!-- Report Header Card -->
        <div class="corrupt-modern-card">
            <div class="corrupt-report-header">
                <div class="corrupt-report-header-content">
                    <div class="corrupt-report-id-section">
                        <div class="corrupt-report-id-badge">
                            <i class="fas fa-hashtag me-2"></i>
                            <?php echo htmlspecialchars($report->corruption_report_id ?? ''); ?>
                        </div>
                        <h2 class="corrupt-report-title">
                            <?php echo htmlspecialchars($report->complaint_subject ?? 'ไม่ระบุหัวข้อ'); ?>
                        </h2>
                        <div class="corrupt-report-date">
                            <i class="fas fa-calendar-alt me-2"></i>
                            แจ้งเมื่อ: <?php echo convertToThaiDate($report->created_at ?? ''); ?>
                        </div>
                    </div>
                    <div class="corrupt-status-section">
                        <div class="corrupt-status-badge-large">
                            <i class="<?php echo $current_status['icon']; ?>"></i>
                            <?php echo getStatusDisplay($report->report_status ?? 'pending'); ?>
                        </div>
                        <div class="corrupt-status-description">
                            สถานะปัจจุบัน
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Information -->
        <div class="corrupt-modern-card">
            <div class="corrupt-content-section">
                <div class="corrupt-section-header">
                    <div class="corrupt-section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="corrupt-section-title">ข้อมูลการรายงาน</h3>
                </div>
                
                <div class="corrupt-info-grid">
                    <div class="corrupt-info-item">
                        <div class="corrupt-info-label">ประเภทการทุจริต</div>
                        <div class="corrupt-info-value">
                            <?php echo getCorruptionTypeDisplay($report->corruption_type ?? ''); ?>
                            <?php if (!empty($report->corruption_type_other)): ?>
                                <br><small class="text-muted">(<?php echo htmlspecialchars($report->corruption_type_other); ?>)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="corrupt-info-item">
                        <div class="corrupt-info-label">ผู้ถูกกล่าวหา</div>
                        <div class="corrupt-info-value">
                            <?php echo htmlspecialchars($report->perpetrator_name ?? 'ไม่ระบุ'); ?>
                            <?php if (!empty($report->perpetrator_position)): ?>
                                <br><small class="text-muted">ตำแหน่ง: <?php echo htmlspecialchars($report->perpetrator_position); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="corrupt-info-item">
                        <div class="corrupt-info-label">หน่วยงาน</div>
                        <div class="corrupt-info-value">
                            <?php echo htmlspecialchars($report->perpetrator_department ?? 'ไม่ระบุ'); ?>
                        </div>
                    </div>
                    
                    <div class="corrupt-info-item">
                        <div class="corrupt-info-label">ผู้แจ้ง</div>
                        <div class="corrupt-info-value">
                            <?php 
                            if ($report->is_anonymous == 1) {
                                echo 'ไม่ระบุตัวตน';
                            } else {
                                echo htmlspecialchars($report->reporter_name ?? 'ไม่ระบุ');
                            }
                            ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($report->incident_date)): ?>
                    <div class="corrupt-info-item">
                        <div class="corrupt-info-label">วันที่เกิดเหตุ</div>
                        <div class="corrupt-info-value">
                            <?php 
                            echo date('d/m/Y', strtotime($report->incident_date));
                            if (!empty($report->incident_time)): 
                                echo ' เวลา ' . $report->incident_time . ' น.';
                            endif;
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($report->incident_location)): ?>
                    <div class="corrupt-info-item">
                        <div class="corrupt-info-label">สถานที่เกิดเหตุ</div>
                        <div class="corrupt-info-value">
                            <?php echo htmlspecialchars($report->incident_location); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Report Details -->
        <div class="corrupt-modern-card">
            <div class="corrupt-content-section">
                <div class="corrupt-section-header">
                    <div class="corrupt-section-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="corrupt-section-title">รายละเอียดเหตุการณ์</h3>
                </div>
                
                <?php if (!empty($report->complaint_details)): ?>
                <div class="corrupt-text-content">
                    <p><?php echo nl2br(htmlspecialchars($report->complaint_details)); ?></p>
                </div>
                <?php else: ?>
                <div class="corrupt-empty-state">
                    <div class="corrupt-empty-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <p>ไม่มีรายละเอียดเพิ่มเติม</p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($report->other_involved)): ?>
                <div class="corrupt-info-item">
                    <div class="corrupt-info-label">ผู้เกี่ยวข้องอื่นๆ</div>
                    <div class="corrupt-info-value">
                        <?php echo nl2br(htmlspecialchars($report->other_involved)); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($report->evidence_description)): ?>
                <div class="corrupt-info-item">
                    <div class="corrupt-info-label">คำอธิบายหลักฐาน</div>
                    <div class="corrupt-info-value">
                        <?php echo nl2br(htmlspecialchars($report->evidence_description)); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Evidence Files -->
        <?php if (!empty($report->files) && count($report->files) > 0): ?>
        <div class="corrupt-modern-card">
            <div class="corrupt-content-section">
                <div class="corrupt-section-header">
                    <div class="corrupt-section-icon">
                        <i class="fas fa-paperclip"></i>
                    </div>
                    <h3 class="corrupt-section-title">ไฟล์หลักฐาน (<?php echo count($report->files); ?> ไฟล์)</h3>
                </div>
                
                <div class="corrupt-files-grid">
                    <?php foreach ($report->files as $file): 
                        $file_ext = strtolower($file->file_extension ?? '');
                        $file_class = 'other';
                        $file_icon = 'fas fa-file';
                        
                        if (in_array($file_ext, ['pdf'])) {
                            $file_class = 'pdf';
                            $file_icon = 'fas fa-file-pdf';
                        } elseif (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $file_class = 'image';
                            $file_icon = 'fas fa-image';
                        } elseif (in_array($file_ext, ['doc', 'docx', 'xls', 'xlsx'])) {
                            $file_class = 'document';
                            $file_icon = 'fas fa-file-word';
                        }
                        
                        $file_size = isset($file->file_size) ? number_format($file->file_size / 1024, 2) . ' KB' : 'ไม่ทราบขนาด';
                    ?>
                    <div class="corrupt-file-item">
                        <div class="corrupt-file-header">
                            <div class="corrupt-file-icon <?php echo $file_class; ?>">
                                <i class="<?php echo $file_icon; ?>"></i>
                            </div>
                            <div class="corrupt-file-info">
                                <h6><?php echo htmlspecialchars($file->file_original_name ?? 'ไม่ทราบชื่อไฟล์'); ?></h6>
                                <div class="corrupt-file-meta">
                                    <?php echo $file_size; ?> • อัปโหลดเมื่อ <?php echo convertToThaiDate($file->uploaded_at ?? ''); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Preview Icon -->
                        <?php if (in_array($file_ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif'])): ?>
                        <div class="corrupt-file-preview-icon" title="คลิกเพื่อดูตัวอย่าง">
                            <i class="fas fa-eye"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="corrupt-file-actions">
                            <?php if (in_array($file_ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif'])): ?>
                                <a href="<?php echo site_url('Corruption/view_evidence/' . ($file->file_id ?? '')); ?>" 
                                   class="corrupt-action-btn corrupt-info view-file-btn" 
                                   data-file-type="<?php echo $file_ext; ?>"
                                   data-file-name="<?php echo htmlspecialchars($file->file_original_name ?? '', ENT_QUOTES); ?>"
                                   target="_blank">
                                    <i class="fas fa-eye"></i> 
                                    <?php echo ($file_ext === 'pdf') ? 'ดู PDF' : 'ดูรูป'; ?>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo site_url('Corruption/view_evidence/' . ($file->file_id ?? '')); ?>" 
                                   class="corrupt-action-btn corrupt-info" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> เปิด
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('Corruption/download_evidence/' . ($file->file_id ?? '')); ?>" 
                               class="corrupt-action-btn corrupt-secondary download-file-btn">
                                <i class="fas fa-download"></i> ดาวน์โหลด
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- History Timeline -->
        <?php if (!empty($report->history) && count($report->history) > 0): ?>
        <div class="corrupt-modern-card">
            <div class="corrupt-content-section">
                <div class="corrupt-section-header">
                    <div class="corrupt-section-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="corrupt-section-title">ประวัติการดำเนินการ</h3>
                </div>
                
                <div class="corrupt-timeline">
                    <?php foreach ($report->history as $history): ?>
                    <div class="corrupt-timeline-item">
                        <div class="corrupt-timeline-header">
                            <h5 class="corrupt-timeline-title">
                                <?php 
                                $action_labels = [
                                    'created' => 'สร้างรายงาน',
                                    'status_changed' => 'เปลี่ยนสถานะ',
                                    'assigned' => 'มอบหมายงาน',
                                    'commented' => 'เพิ่มความคิดเห็น',
                                    'evidence_added' => 'เพิ่มหลักฐาน',
                                    'evidence_removed' => 'ลบหลักฐาน'
                                ];
                                echo $action_labels[$history->action_type] ?? $history->action_type;
                                ?>
                            </h5>
                            <span class="corrupt-timeline-date">
                                <?php echo convertToThaiDate($history->action_date ?? ''); ?>
                            </span>
                        </div>
                        <div class="corrupt-timeline-content">
                            <?php echo htmlspecialchars($history->action_description ?? ''); ?>
                            <?php if (!empty($history->action_by)): ?>
                                <br><small class="text-muted">โดย: <?php echo htmlspecialchars($history->action_by); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Response from Authority -->
        <?php if (!empty($report->response_message)): ?>
        <div class="corrupt-modern-card">
            <div class="corrupt-content-section">
                <div class="corrupt-section-header">
                    <div class="corrupt-section-icon">
                        <i class="fas fa-reply"></i>
                    </div>
                    <h3 class="corrupt-section-title">การตอบกลับจากหน่วยงาน</h3>
                </div>
                
                <div class="corrupt-text-content">
                    <p><?php echo nl2br(htmlspecialchars($report->response_message)); ?></p>
                    <?php if (!empty($report->response_date)): ?>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            ตอบกลับเมื่อ: <?php echo convertToThaiDate($report->response_date); ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="corrupt-modern-card">
            <div class="corrupt-actions-card">
                <h4 class="mb-4">
                    <i class="fas fa-cogs me-2"></i>
                    การดำเนินการ
                </h4>
                
                <div class="corrupt-actions-grid">
                    <a href="<?php echo site_url('Corruption/my_reports'); ?>" 
                       class="corrupt-action-btn corrupt-secondary">
                        <i class="fas fa-arrow-left"></i>
                        กลับไปรายการ
                    </a>
                    
                    <button onclick="copyReportId('<?php echo htmlspecialchars($report->corruption_report_id ?? '', ENT_QUOTES); ?>')" 
                            class="corrupt-action-btn corrupt-info">
                        <i class="fas fa-copy"></i>
                        คัดลอกหมายเลข
                    </button>
                    
                   
                    
                    <button onclick="printReport()" 
                            class="corrupt-action-btn corrupt-primary">
                        <i class="fas fa-print"></i>
                        พิมพ์รายงาน
                    </button>
                    
                    <a href="<?php echo site_url('Corruption/report_form'); ?>" 
                       class="corrupt-action-btn corrupt-primary">
                        <i class="fas fa-plus"></i>
                        แจ้งเรื่องใหม่
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Load Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('🔍 รายละเอียดรายงานการทุจริต - พร้อมใช้งาน');
    
    // *** ฟังก์ชันคัดลอกหมายเลขรายงาน ***
    window.copyReportId = function(reportId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(reportId).then(() => {
                showAlert('คัดลอกหมายเลข ' + reportId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopy(reportId);
            });
        } else {
            fallbackCopy(reportId);
        }
    };
    
    // *** ฟังก์ชันแชร์รายงาน ***
    window.shareReport = function() {
        const reportId = '<?php echo htmlspecialchars($report->corruption_report_id ?? '', ENT_QUOTES); ?>';
        const reportTitle = '<?php echo htmlspecialchars($report->complaint_subject ?? '', ENT_QUOTES); ?>';
        const shareUrl = window.location.href;
        
        if (navigator.share) {
            navigator.share({
                title: `รายงานการทุจริต #${reportId}`,
                text: `${reportTitle}`,
                url: shareUrl
            }).then(() => {
                showAlert('แชร์รายงานสำเร็จ', 'success');
            }).catch(() => {
                copyShareLink(shareUrl);
            });
        } else {
            copyShareLink(shareUrl);
        }
    };
    
    // *** ฟังก์ชันคัดลอกลิงค์แชร์ ***
    function copyShareLink(url) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                showAlert('คัดลอกลิงค์แชร์สำเร็จ', 'success');
            });
        } else {
            fallbackCopy(url);
        }
    }
    
    // *** ฟังก์ชันพิมพ์รายงาน ***
    window.printReport = function() {
        // ซ่อนปุ่มก่อนพิมพ์
        $('.corrupt-actions-card').hide();
        
        // เพิ่ม CSS สำหรับการพิมพ์
        const printCSS = `
        <style media="print">
            body * { visibility: hidden; }
            .corrupt-container-pages, .corrupt-container-pages * { visibility: visible; }
            .corrupt-container-pages { position: absolute; left: 0; top: 0; width: 100%; }
            .corrupt-actions-card { display: none !important; }
            .corrupt-modern-card { box-shadow: none; border: 1px solid #ddd; }
        </style>`;
        
        $('head').append(printCSS);
        
        // พิมพ์
        setTimeout(() => {
            window.print();
            
            // แสดงปุ่มกลับหลังพิมพ์
            setTimeout(() => {
                $('.corrupt-actions-card').show();
            }, 1000);
        }, 500);
    };
    
    // *** ฟังก์ชัน fallback สำหรับการคัดลอก ***
    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showAlert('คัดลอกสำเร็จ', 'success');
        } catch (err) {
            showAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** ฟังก์ชันแสดงการแจ้งเตือน ***
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
                confirmButtonColor: '#dc3545',
                position: 'top-end',
                toast: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        } else {
            alert(message);
        }
    }
    
    // *** การจัดการไฟล์ PDF และรูปภาพ ***
    $('.view-file-btn').click(function(e) {
        const fileName = $(this).data('file-name');
        const fileUrl = $(this).attr('href');
        const fileType = $(this).data('file-type');
        
        // สำหรับ PDF ให้เปิดใน tab ใหม่โดยตรง
        if (fileType === 'pdf') {
            // PDF จะเปิดใน tab ใหม่ผ่าน target="_blank" อยู่แล้ว
            showAlert('กำลังเปิด PDF ใน tab ใหม่...', 'info');
            return true; // ให้ browser ทำงานปกติ
        }
        
        // สำหรับรูปภาพ ให้แสดงใน Modal
        if (fileType && fileType.match(/^(jpg|jpeg|png|gif)$/i)) {
            e.preventDefault();
            showImageModal(fileName, fileUrl);
        }
    });
    
    // *** ฟังก์ชันแสดง Modal รูปภาพ ***
    function showImageModal(fileName, fileUrl) {
        const modal = `
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">
                                <i class="fas fa-image me-2"></i>${fileName}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <div class="image-container">
                                <div class="loading-container text-center p-4">
                                    <div class="loading-spinner"></div>
                                    <p class="mt-2">กำลังโหลดรูปภาพ...</p>
                                </div>
                                <img src="${fileUrl}" class="img-fluid d-none" alt="${fileName}" 
                                     style="max-width: 100%; height: auto; border-radius: 8px;"
                                     onload="this.classList.remove('d-none'); this.parentElement.querySelector('.loading-container').style.display='none';"
                                     onerror="this.parentElement.innerHTML='<div class=&quot;text-center p-4&quot;><i class=&quot;fas fa-exclamation-triangle fa-3x text-warning&quot;></i><p class=&quot;mt-2&quot;>ไม่สามารถโหลดรูปภาพได้</p></div>';">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="${fileUrl.replace('view_evidence', 'download_evidence')}" 
                               class="btn btn-outline-primary download-from-modal">
                                <i class="fas fa-download me-2"></i>ดาวน์โหลด
                            </a>
                            <a href="${fileUrl}" target="_blank" class="btn btn-info">
                                <i class="fas fa-external-link-alt me-2"></i>เปิดใน Tab ใหม่
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>ปิด
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // ลบ Modal เก่า (ถ้ามี)
        $('#imageModal').remove();
        
        // เพิ่ม Modal ใหม่
        $('body').append(modal);
        
        // แสดง Modal
        const modalElement = new bootstrap.Modal(document.getElementById('imageModal'));
        modalElement.show();
        
        // ลบ Modal หลังปิด
        $('#imageModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
    
    // *** การจัดการ Loading สำหรับการดาวน์โหลด ***
    $('.download-file-btn, .download-from-modal').click(function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.html('<span class="loading-spinner me-2"></span>กำลังดาวน์โหลด...');
        $btn.prop('disabled', true);
        
        // คืนสถานะปุ่มหลัง 3 วินาที
        setTimeout(() => {
            $btn.html(originalText);
            $btn.prop('disabled', false);
        }, 3000);
        
        showAlert('กำลังเริ่มดาวน์โหลดไฟล์...', 'info');
    });
    
    // *** เพิ่ม Click effect สำหรับไฟล์ที่มีตัวอย่าง ***
    $('.corrupt-file-preview-icon').click(function() {
        $(this).closest('.corrupt-file-item').find('.view-file-btn').click();
    });
    
    // *** การเลื่อนไปยังส่วนต่างๆ อย่างนุ่มนวล ***
    function smoothScrollTo(target) {
        if ($(target).length) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 800);
        }
    }
    
    // *** เพิ่ม Quick Navigation ***
    function addQuickNavigation() {
        const sections = [
            { id: '.corrupt-report-header', label: 'ข้อมูลหลัก', icon: 'fas fa-info' },
            { id: '.corrupt-content-section:has(.fa-info-circle)', label: 'ข้อมูลการรายงาน', icon: 'fas fa-file-alt' },
            { id: '.corrupt-content-section:has(.fa-paperclip)', label: 'ไฟล์หลักฐาน', icon: 'fas fa-paperclip' },
            { id: '.corrupt-content-section:has(.fa-history)', label: 'ประวัติ', icon: 'fas fa-history' }
        ];
        
        let navHtml = '<div class="quick-nav" style="position: fixed; right: 20px; top: 50%; transform: translateY(-50%); z-index: 1000; display: none;">';
        
        sections.forEach((section, index) => {
            if ($(section.id).length) {
                navHtml += `
                    <button class="quick-nav-btn" onclick="smoothScrollTo('${section.id}')" 
                            title="${section.label}" style="
                        display: block;
                        width: 45px;
                        height: 45px;
                        margin-bottom: 10px;
                        background: var(--corrupt-gradient-primary);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        box-shadow: 0 2px 10px rgba(220, 53, 69, 0.3);
                    ">
                        <i class="${section.icon}"></i>
                    </button>
                `;
            }
        });
        
        navHtml += '</div>';
        
        if (sections.some(section => $(section.id).length > 0)) {
            $('body').append(navHtml);
            
            // แสดง Quick Nav เมื่อ scroll ลง
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('.quick-nav').fadeIn();
                } else {
                    $('.quick-nav').fadeOut();
                }
            });
            
            // เพิ่ม hover effect
            $('.quick-nav-btn').hover(
                function() {
                    $(this).css('transform', 'scale(1.1)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                }
            );
        }
    }
    
    // เรียกใช้ Quick Navigation
    addQuickNavigation();
    
    // *** เพิ่ม Animation เมื่อโหลดหน้า ***
    $('.corrupt-modern-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(30px)'
        }).delay(index * 200).animate({
            'opacity': '1'
        }, {
            duration: 600,
            step: function(now) {
                $(this).css('transform', `translateY(${30 * (1 - now)}px)`);
            }
        });
    });
    

    
    // *** เพิ่ม Tooltip สำหรับปุ่มต่างๆ ***
    $('[title]').tooltip();
    
    // *** การจัดการ File Size Display ***
    $('.corrupt-file-meta').each(function() {
        const text = $(this).text();
        const sizeMatch = text.match(/(\d+(?:\.\d+)?)\s*KB/);
        if (sizeMatch) {
            const sizeKB = parseFloat(sizeMatch[1]);
            if (sizeKB > 1024) {
                const sizeMB = (sizeKB / 1024).toFixed(2);
                $(this).html(text.replace(/\d+(?:\.\d+)?\s*KB/, sizeMB + ' MB'));
            }
        }
    });
    
    console.log('✅ ระบบรายละเอียดรายงานพร้อมใช้งาน (รองรับการดูไฟล์ PDF และรูปภาพ)');
});

// *** ทำให้ฟังก์ชันเป็น Global ***
window.smoothScrollTo = function(target) {
    if ($(target).length) {
        $('html, body').animate({
            scrollTop: $(target).offset().top - 100
        }, 800);
    }
};

// *** เพิ่มการจัดการ Error สำหรับการโหลดรูปภาพ ***
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG') {
        console.error('Image load error:', e.target.src);
        const imgContainer = e.target.parentElement;
        if (imgContainer) {
            imgContainer.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                    <p class="mt-2">ไม่สามารถโหลดรูปภาพได้</p>
                    <small class="text-muted">กรุณาลองใหม่อีกครั้ง หรือดาวน์โหลดไฟล์</small>
                </div>
            `;
        }
    }
}, true);

// *** เพิ่ม Service Worker สำหรับ Cache ไฟล์ (ถ้าต้องการ) ***
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // สามารถเพิ่ม service worker สำหรับ cache ไฟล์ได้ในอนาคต
        console.log('Service Worker ready for future implementation');
    });
}

// *** ฟังก์ชันตรวจสอบการเชื่อมต่ออินเทอร์เน็ต ***
function checkConnection() {
    if (!navigator.onLine) {
        Swal.fire({
            icon: 'warning',
            title: 'ไม่มีการเชื่อมต่ออินเทอร์เน็ต',
            text: 'กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ตของคุณ',
            confirmButtonColor: '#dc3545'
        });
        return false;
    }
    return true;
}

// *** ตรวจสอบการเชื่อมต่อเมื่อมีการคลิกลิงค์ ***
$('.view-file-btn, .download-file-btn').click(function(e) {
    if (!checkConnection()) {
        e.preventDefault();
        return false;
    }
});

// *** เพิ่ม Event Listener สำหรับการเปลี่ยนสถานะการเชื่อมต่อ ***
window.addEventListener('online', function() {
    console.log('Connection restored');
});

window.addEventListener('offline', function() {
    console.log('Connection lost');
    Swal.fire({
        icon: 'warning',
        title: 'การเชื่อมต่อขาดหาย',
        text: 'บางฟีเจอร์อาจไม่ทำงาน กรุณาตรวจสอบการเชื่อมต่อ',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
});
</script>