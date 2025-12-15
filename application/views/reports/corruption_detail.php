<?php
// Helper function สำหรับแปลงขนาดไฟล์
if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

// Helper functions สำหรับแสดงข้อมูล
if (!function_exists('get_corruption_status_class')) {
    function get_corruption_status_class($status) {
        switch($status) {
            case 'pending': return 'pending';
            case 'under_review': return 'under_review';
            case 'investigating': return 'investigating';
            case 'resolved': return 'resolved';
            case 'dismissed': return 'dismissed';
            case 'closed': return 'closed';
            default: return 'pending';
        }
    }
}

if (!function_exists('get_corruption_status_display')) {
    function get_corruption_status_display($status) {
        switch($status) {
            case 'pending': return 'รอดำเนินการ';
            case 'under_review': return 'กำลังตรวจสอบ';
            case 'investigating': return 'กำลังสอบสวน';
            case 'resolved': return 'ดำเนินการแล้ว';
            case 'dismissed': return 'ยกเลิก';
            case 'closed': return 'ปิดเรื่อง';
            default: return 'รอดำเนินการ';
        }
    }
}

if (!function_exists('get_corruption_type_display')) {
    function get_corruption_type_display($type) {
        switch($type) {
            case 'embezzlement': return 'การยักยอกเงิน';
            case 'bribery': return 'การรับสินบน';
            case 'abuse_of_power': return 'การใช้อำนาจเกินตัว';
            case 'conflict_of_interest': return 'ผลประโยชน์ทับซ้อน';
            case 'procurement_fraud': return 'การทุจริตในการจัดซื้อ';
            case 'other': return 'อื่นๆ';
            default: return 'ทั่วไป';
        }
    }
}

if (!function_exists('get_corruption_priority_display')) {
    function get_corruption_priority_display($priority) {
        switch($priority) {
            case 'low': return 'ต่ำ';
            case 'normal': return 'ปกติ';
            case 'high': return 'สูง';
            case 'urgent': return 'เร่งด่วน';
            default: return 'ปกติ';
        }
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== CORRUPTION DETAIL SPECIFIC STYLES ===== */
.corruption-detail-page {
    --corruption-primary-color: #dc3545;
    --corruption-primary-light: #e57373;
    --corruption-secondary-color: #fff5f5;
    --corruption-success-color: #28a745;
    --corruption-warning-color: #ffc107;
    --corruption-danger-color: #dc3545;
    --corruption-info-color: #17a2b8;
    --corruption-purple-color: #6f42c1;
    --corruption-light-bg: #fafbfc;
    --corruption-white: #ffffff;
    --corruption-gray-50: #fafafa;
    --corruption-gray-100: #f5f5f5;
    --corruption-gray-200: #eeeeee;
    --corruption-gray-300: #e0e0e0;
    --corruption-gray-400: #bdbdbd;
    --corruption-gray-500: #9e9e9e;
    --corruption-gray-600: #757575;
    --corruption-gray-700: #616161;
    --corruption-gray-800: #424242;
    --corruption-gray-900: #212121;
    --corruption-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.03);
    --corruption-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04);
    --corruption-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04);
    --corruption-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.06), 0 4px 6px -4px rgb(0 0 0 / 0.04);
    --corruption-border-radius: 12px;
    --corruption-border-radius-lg: 16px;
    --corruption-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.corruption-detail-page {
    background: linear-gradient(135deg, #fff5f5 0%, #fcfcfc 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--corruption-gray-700);
    min-height: 100vh;
}

.corruption-detail-page .corruption-container-fluid {
    padding: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
    min-height: calc(100vh - 140px);
}

/* ===== PAGE HEADER ===== */
.corruption-detail-page .corruption-page-header {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.9) 0%, rgba(229, 115, 115, 0.7) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--corruption-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--corruption-shadow-md);
    position: relative;
    overflow: hidden;
    margin-top: 1rem;
}

.corruption-detail-page .corruption-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    border-radius: 50%;
}

.corruption-detail-page .corruption-page-header-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.corruption-detail-page .corruption-page-header h1 {
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.08);
    color: #ffffff !important;
}

.corruption-detail-page .corruption-report-id-display {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

/* ===== HEADER ACTIONS ===== */
.corruption-detail-page .corruption-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.corruption-detail-page .corruption-action-btn {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: var(--corruption-transition);
    backdrop-filter: blur(10px);
}

.corruption-detail-page .corruption-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ===== STATUS OVERVIEW ===== */
.corruption-detail-page .corruption-status-overview {
    margin-bottom: 2rem;
}

.corruption-detail-page .corruption-status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.corruption-detail-page .corruption-status-card {
    background: var(--corruption-white);
    border-radius: var(--corruption-border-radius);
    padding: 1.5rem;
    box-shadow: var(--corruption-shadow-md);
    border: 1px solid var(--corruption-gray-100);
    position: relative;
    overflow: hidden;
}

.corruption-detail-page .corruption-status-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--corruption-primary-color), var(--corruption-primary-light));
}

.corruption-detail-page .corruption-status-card.pending::before { 
    background: linear-gradient(90deg, #ffc107, #ffb74d); 
}
.corruption-detail-page .corruption-status-card.under_review::before { 
    background: linear-gradient(90deg, #17a2b8, #64b5f6); 
}
.corruption-detail-page .corruption-status-card.investigating::before { 
    background: linear-gradient(90deg, #6f42c1, #ba68c8); 
}
.corruption-detail-page .corruption-status-card.resolved::before { 
    background: linear-gradient(90deg, #28a745, #81c784); 
}
.corruption-detail-page .corruption-status-card.dismissed::before { 
    background: linear-gradient(90deg, #6c757d, #9e9e9e); 
}
.corruption-detail-page .corruption-status-card.closed::before { 
    background: linear-gradient(90deg, #dc3545, #e57373); 
}

.corruption-detail-page .corruption-status-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.corruption-detail-page .corruption-status-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
}

.corruption-detail-page .corruption-status-icon.pending { 
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.8), rgba(255, 183, 77, 0.8)); 
}
.corruption-detail-page .corruption-status-icon.under_review { 
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.8), rgba(100, 181, 246, 0.8)); 
}
.corruption-detail-page .corruption-status-icon.investigating { 
    background: linear-gradient(135deg, rgba(111, 66, 193, 0.8), rgba(186, 104, 200, 0.8)); 
}
.corruption-detail-page .corruption-status-icon.resolved { 
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.8), rgba(129, 199, 132, 0.8)); 
}
.corruption-detail-page .corruption-status-icon.dismissed { 
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.8), rgba(158, 158, 158, 0.8)); 
}
.corruption-detail-page .corruption-status-icon.closed { 
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.8), rgba(229, 115, 115, 0.8)); 
}

.corruption-detail-page .corruption-status-label {
    font-size: 0.875rem;
    color: var(--corruption-gray-600);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.corruption-detail-page .corruption-status-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--corruption-gray-800);
    margin-top: 0.25rem;
}

/* ===== MAIN CONTENT GRID ===== */
.corruption-detail-page .corruption-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

/* ===== REPORT DETAILS CARD ===== */
.corruption-detail-page .corruption-detail-card {
    background: var(--corruption-white);
    border-radius: var(--corruption-border-radius);
    box-shadow: var(--corruption-shadow-md);
    border: 1px solid var(--corruption-gray-100);
    overflow: hidden;
}

.corruption-detail-page .corruption-detail-header {
    background: var(--corruption-gray-50);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--corruption-gray-200);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.corruption-detail-page .corruption-detail-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--corruption-gray-900);
    margin: 0;
}

.corruption-detail-page .corruption-detail-body {
    padding: 2rem;
}

.corruption-detail-page .corruption-detail-section {
    margin-bottom: 2rem;
}

.corruption-detail-page .corruption-detail-section:last-child {
    margin-bottom: 0;
}

.corruption-detail-page .corruption-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--corruption-gray-800);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corruption-detail-page .corruption-field-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.corruption-detail-page .corruption-field {
    display: flex;
    flex-direction: column;
}

.corruption-detail-page .corruption-field-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--corruption-gray-600);
    margin-bottom: 0.5rem;
}

.corruption-detail-page .corruption-field-value {
    background: var(--corruption-gray-50);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid var(--corruption-gray-200);
    color: var(--corruption-gray-800);
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

.corruption-detail-page .corruption-field-value.full-width {
    grid-column: 1 / -1;
}

.corruption-detail-page .corruption-description-field {
    background: var(--corruption-gray-50);
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid var(--corruption-gray-200);
    color: var(--corruption-gray-800);
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* ===== STATUS BADGES ===== */
.corruption-detail-page .corruption-status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.corruption-detail-page .corruption-status-badge.pending {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border: 1px solid rgba(255, 152, 0, 0.3);
}

.corruption-detail-page .corruption-status-badge.under_review {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.corruption-detail-page .corruption-status-badge.investigating {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border: 1px solid rgba(156, 39, 176, 0.3);
}

.corruption-detail-page .corruption-status-badge.resolved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.corruption-detail-page .corruption-status-badge.dismissed {
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.8), rgba(233, 236, 239, 0.6));
    color: #495057;
    border: 1px solid rgba(108, 117, 125, 0.3);
}

.corruption-detail-page .corruption-status-badge.closed {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

/* ===== SIDEBAR ===== */
.corruption-detail-page .corruption-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.corruption-detail-page .corruption-sidebar-card {
    background: var(--corruption-white);
    border-radius: var(--corruption-border-radius);
    box-shadow: var(--corruption-shadow-md);
    border: 1px solid var(--corruption-gray-100);
    overflow: hidden;
}

.corruption-detail-page .corruption-sidebar-header {
    background: var(--corruption-gray-50);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--corruption-gray-200);
    font-weight: 600;
    color: var(--corruption-gray-800);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corruption-detail-page .corruption-sidebar-body {
    padding: 1.5rem;
}

/* ===== FILES SECTION ===== */
.corruption-detail-page .corruption-files-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.corruption-detail-page .corruption-file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: var(--corruption-gray-50);
    border-radius: 8px;
    border: 1px solid var(--corruption-gray-200);
    transition: var(--corruption-transition);
}

.corruption-detail-page .corruption-file-item:hover {
    background: var(--corruption-gray-100);
    border-color: var(--corruption-primary-color);
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-sm);
}

.corruption-detail-page .corruption-file-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.corruption-detail-page .corruption-file-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--corruption-primary-color), var(--corruption-primary-light));
    color: white;
    font-size: 1rem;
}

.corruption-detail-page .corruption-file-details {
    flex: 1;
}

.corruption-detail-page .corruption-file-name {
    font-weight: 600;
    color: var(--corruption-gray-800);
    margin-bottom: 0.25rem;
}

.corruption-detail-page .corruption-file-size {
    font-size: 0.8rem;
    color: var(--corruption-gray-600);
}

.corruption-detail-page .corruption-file-download {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.8), rgba(129, 199, 132, 0.8));
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--corruption-transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corruption-detail-page .corruption-file-download:hover {
    background: linear-gradient(135deg, rgba(56, 142, 60, 0.9), rgba(76, 175, 80, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
}

/* ===== TIMELINE ===== */
.corruption-detail-page .corruption-timeline {
    position: relative;
    padding-left: 2rem;
}

.corruption-detail-page .corruption-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--corruption-gray-200);
}

.corruption-detail-page .corruption-timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.corruption-detail-page .corruption-timeline-item::before {
    content: '';
    position: absolute;
    left: -0.5rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    background: var(--corruption-primary-color);
    border-radius: 50%;
    border: 3px solid var(--corruption-white);
    box-shadow: 0 0 0 2px var(--corruption-primary-color);
}

.corruption-detail-page .corruption-timeline-content {
    background: var(--corruption-gray-50);
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid var(--corruption-primary-color);
}

.corruption-detail-page .corruption-timeline-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.corruption-detail-page .corruption-timeline-title {
    font-weight: 600;
    color: var(--corruption-gray-800);
}

.corruption-detail-page .corruption-timeline-date {
    font-size: 0.8rem;
    color: var(--corruption-gray-600);
}

.corruption-detail-page .corruption-timeline-description {
    color: var(--corruption-gray-700);
    line-height: 1.5;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .corruption-detail-page .corruption-container-fluid {
        padding: 1rem;
    }
    
    .corruption-detail-page .corruption-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
    }
    
    .corruption-detail-page .corruption-page-header h1 {
        font-size: 1.5rem;
    }
    
    .corruption-detail-page .corruption-page-header-content {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .corruption-detail-page .corruption-header-actions {
        justify-content: stretch;
    }
    
    .corruption-detail-page .corruption-header-actions .corruption-action-btn {
        flex: 1;
        justify-content: center;
    }
    
    .corruption-detail-page .corruption-status-cards {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .corruption-detail-page .corruption-content-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .corruption-detail-page .corruption-field-group {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .corruption-detail-page .corruption-detail-body {
        padding: 1.5rem 1rem;
    }
    
    .corruption-detail-page .corruption-file-item {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .corruption-detail-page .corruption-file-download {
        align-self: center;
    }
}

@media (max-width: 480px) {
    .corruption-detail-page .corruption-status-cards {
        grid-template-columns: 1fr;
    }
    
    .corruption-detail-page .corruption-page-header {
        padding: 1rem;
    }
    
    .corruption-detail-page .corruption-detail-header {
        padding: 1rem;
    }
    
    .corruption-detail-page .corruption-sidebar-body {
        padding: 1rem;
    }
}

/* ===== ANIMATIONS ===== */
@keyframes corruptionDetailFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.corruption-detail-page .corruption-detail-card,
.corruption-detail-page .corruption-sidebar-card {
    animation: corruptionDetailFadeIn 0.3s ease-out;
}

/* ===== LOADING STATES ===== */
.corruption-detail-page .corruption-loading {
    opacity: 0.6;
    pointer-events: none;
}

.corruption-detail-page .corruption-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--corruption-gray-300);
    border-top: 2px solid var(--corruption-primary-color);
    border-radius: 50%;
    animation: corruptionDetailSpin 1s linear infinite;
}

@keyframes corruptionDetailSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="corruption-detail-page">
    <div class="corruption-container-fluid">
        <!-- ===== PAGE HEADER ===== -->
        <header class="corruption-page-header">
            <div class="corruption-page-header-content">
                <div>
                    <h1><i class="fas fa-shield-alt me-3"></i>รายละเอียดรายงานการทุจริต</h1>
                </div>
                <div class="corruption-report-id-display">
                    #<?= $report_detail->corruption_report_id ?? 'N/A' ?>
                </div>
            </div>
            
            <!-- Header Actions -->
            <div class="corruption-header-actions">
                <a href="<?= site_url('Corruption/admin_management') ?>" class="corruption-action-btn" title="กลับไปหน้าจัดการ">
                    <i class="fas fa-arrow-left"></i>
                    <span>กลับ</span>
                </a>
                <button type="button" class="corruption-action-btn" onclick="printReport()" title="พิมพ์รายงาน">
                    <i class="fas fa-print"></i>
                    <span>พิมพ์</span>
                </button>
            </div>
        </header>

        <!-- ===== STATUS OVERVIEW ===== -->
        <section class="corruption-status-overview">
            <div class="corruption-status-cards">
                <div class="corruption-status-card <?= get_corruption_status_class($report_detail->report_status ?? 'pending') ?>">
                    <div class="corruption-status-card-header">
                        <div class="corruption-status-icon <?= get_corruption_status_class($report_detail->report_status ?? 'pending') ?>">
                            <?php 
                            $status_icons = [
                                'pending' => 'fas fa-clock',
                                'under_review' => 'fas fa-search',
                                'investigating' => 'fas fa-gavel',
                                'resolved' => 'fas fa-check-circle',
                                'dismissed' => 'fas fa-times-circle',
                                'closed' => 'fas fa-lock'
                            ];
                            $current_status = $report_detail->report_status ?? 'pending';
                            ?>
                            <i class="<?= $status_icons[$current_status] ?? 'fas fa-circle' ?>"></i>
                        </div>
                        <div>
                            <div class="corruption-status-label">สถานะปัจจุบัน</div>
                            <div class="corruption-status-value">
                                <?= get_corruption_status_display($report_detail->report_status ?? 'pending') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="corruption-status-card">
                    <div class="corruption-status-card-header">
                        <div class="corruption-status-icon" style="background: linear-gradient(135deg, #6f42c1, #ba68c8);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="corruption-status-label">ระดับความสำคัญ</div>
                            <div class="corruption-status-value">
                                <?= get_corruption_priority_display($report_detail->priority_level ?? 'normal') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="corruption-status-card">
                    <div class="corruption-status-card-header">
                        <div class="corruption-status-icon" style="background: linear-gradient(135deg, #17a2b8, #64b5f6);">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="corruption-status-label">วันที่แจ้ง</div>
                            <div class="corruption-status-value">
                                <?php 
                                if (!empty($report_detail->created_at)) {
                                    $created_date = new DateTime($report_detail->created_at);
                                    echo $created_date->format('d/m/Y');
                                } else {
                                    echo 'ไม่ระบุ';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="corruption-status-card">
                    <div class="corruption-status-card-header">
                        <div class="corruption-status-icon" style="background: linear-gradient(135deg, #28a745, #81c784);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <div class="corruption-status-label">ไฟล์หลักฐาน</div>
                            <div class="corruption-status-value">
                                <?= count($report_detail->files ?? []) ?> ไฟล์
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="corruption-content-grid">
            <!-- ===== REPORT DETAILS ===== -->
            <div class="corruption-detail-card">
                <div class="corruption-detail-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h2 class="corruption-detail-title">รายละเอียดรายงาน</h2>
                </div>
                <div class="corruption-detail-body">
                    <!-- Basic Information -->
                    <div class="corruption-detail-section">
                        <h3 class="corruption-section-title">
                            <i class="fas fa-info-circle"></i>
                            ข้อมูลพื้นฐาน
                        </h3>
                        <div class="corruption-field-group">
                            <div class="corruption-field">
                                <label class="corruption-field-label">ประเภทการทุจริต</label>
                                <div class="corruption-field-value">
                                    <?= get_corruption_type_display($report_detail->corruption_type ?? '') ?>
                                    <?php if (!empty($report_detail->corruption_type_other)): ?>
                                        <span class="text-muted">(<?= htmlspecialchars($report_detail->corruption_type_other) ?>)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="corruption-field">
                                <label class="corruption-field-label">สถานะ</label>
                                <div class="corruption-field-value">
                                    <span class="corruption-status-badge <?= get_corruption_status_class($report_detail->report_status ?? 'pending') ?>">
                                        <?= get_corruption_status_display($report_detail->report_status ?? 'pending') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="corruption-field">
                            <label class="corruption-field-label">หัวข้อเรื่องร้องเรียน</label>
                            <div class="corruption-field-value full-width">
                                <?= htmlspecialchars($report_detail->complaint_subject ?? 'ไม่ระบุ') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint Details -->
                    <div class="corruption-detail-section">
                        <h3 class="corruption-section-title">
                            <i class="fas fa-file-text"></i>
                            รายละเอียดเหตุการณ์
                        </h3>
                        <div class="corruption-description-field">
                            <?= htmlspecialchars($report_detail->complaint_details ?? 'ไม่มีรายละเอียด') ?>
                        </div>
                    </div>

                    <!-- Incident Information -->
                    <div class="corruption-detail-section">
                        <h3 class="corruption-section-title">
                            <i class="fas fa-map-marker-alt"></i>
                            ข้อมูลเหตุการณ์
                        </h3>
                        <div class="corruption-field-group">
                            <div class="corruption-field">
                                <label class="corruption-field-label">วันที่เกิดเหตุ</label>
                                <div class="corruption-field-value">
                                    <?php 
                                    if (!empty($report_detail->incident_date)) {
                                        $incident_date = new DateTime($report_detail->incident_date);
                                        echo $incident_date->format('d/m/Y');
                                    } else {
                                        echo 'ไม่ระบุ';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="corruption-field">
                                <label class="corruption-field-label">เวลาที่เกิดเหตุ</label>
                                <div class="corruption-field-value">
                                    <?= !empty($report_detail->incident_time) ? $report_detail->incident_time . ' น.' : 'ไม่ระบุ' ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="corruption-field">
                            <label class="corruption-field-label">สถานที่เกิดเหตุ</label>
                            <div class="corruption-field-value full-width">
                                <?= htmlspecialchars($report_detail->incident_location ?? 'ไม่ระบุ') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Perpetrator Information -->
                    <div class="corruption-detail-section">
                        <h3 class="corruption-section-title">
                            <i class="fas fa-user-times"></i>
                            ข้อมูลผู้กระทำผิด
                        </h3>
                        <div class="corruption-field-group">
                            <div class="corruption-field">
                                <label class="corruption-field-label">ชื่อผู้กระทำผิด</label>
                                <div class="corruption-field-value">
                                    <?= htmlspecialchars($report_detail->perpetrator_name ?? 'ไม่ระบุ') ?>
                                </div>
                            </div>
                            <div class="corruption-field">
                                <label class="corruption-field-label">ตำแหน่ง</label>
                                <div class="corruption-field-value">
                                    <?= htmlspecialchars($report_detail->perpetrator_position ?? 'ไม่ระบุ') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="corruption-field">
                            <label class="corruption-field-label">หน่วยงาน/แผนก</label>
                            <div class="corruption-field-value full-width">
                                <?= htmlspecialchars($report_detail->perpetrator_department ?? 'ไม่ระบุ') ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($report_detail->other_involved)): ?>
                        <div class="corruption-field">
                            <label class="corruption-field-label">ผู้เกี่ยวข้องอื่นๆ</label>
                            <div class="corruption-description-field">
                                <?= htmlspecialchars($report_detail->other_involved) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Reporter Information -->
                    <?php if (!($report_detail->is_anonymous ?? false)): ?>
                    <div class="corruption-detail-section">
                        <h3 class="corruption-section-title">
                            <i class="fas fa-user"></i>
                            ข้อมูลผู้แจ้ง
                        </h3>
                        <div class="corruption-field-group">
                            <div class="corruption-field">
                                <label class="corruption-field-label">ชื่อผู้แจ้ง</label>
                                <div class="corruption-field-value">
                                    <?= htmlspecialchars($report_detail->reporter_name ?? 'ไม่ระบุ') ?>
                                </div>
                            </div>
                            <div class="corruption-field">
                                <label class="corruption-field-label">เบอร์โทรศัพท์</label>
                                <div class="corruption-field-value">
                                    <?= htmlspecialchars($report_detail->reporter_phone ?? 'ไม่ระบุ') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="corruption-field-group">
                            <div class="corruption-field">
                                <label class="corruption-field-label">อีเมล</label>
                                <div class="corruption-field-value">
                                    <?= htmlspecialchars($report_detail->reporter_email ?? 'ไม่ระบุ') ?>
                                </div>
                            </div>
                            <div class="corruption-field">
                                <label class="corruption-field-label">ความสัมพันธ์กับเหตุการณ์</label>
                                <div class="corruption-field-value">
                                    <?php
                                    $relations = [
                                        'witness' => 'เป็นผู้พบเห็นเหตุการณ์',
                                        'victim' => 'เป็นผู้เสียหาย',
                                        'colleague' => 'เป็นเพื่อนร่วมงาน',
                                        'whistleblower' => 'เป็นผู้รู้เหตุการณ์',
                                        'other' => 'อื่นๆ'
                                    ];
                                    echo $relations[$report_detail->reporter_relation ?? ''] ?? 'ไม่ระบุ';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="corruption-detail-section">
                        <h3 class="corruption-section-title">
                            <i class="fas fa-user-secret"></i>
                            ข้อมูลผู้แจ้ง
                        </h3>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>รายงานแบบไม่ระบุตัวตน</strong><br>
                            ผู้แจ้งขอไม่เปิดเผยข้อมูลส่วนตัว
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Evidence Description -->
                    <?php if (!empty($report_detail->evidence_description)): ?>
                    <div class="corruption-detail-section">
                        <h3 class="corruption-section-title">
                            <i class="fas fa-search"></i>
                            รายละเอียดหลักฐาน
                        </h3>
                        <div class="corruption-description-field">
                            <?= htmlspecialchars($report_detail->evidence_description) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== SIDEBAR ===== -->
            <div class="corruption-sidebar">
                <!-- Files -->
                <div class="corruption-sidebar-card">
                    <div class="corruption-sidebar-header">
                        <i class="fas fa-paperclip"></i>
                        ไฟล์หลักฐาน (<?= count($report_detail->files ?? []) ?>)
                    </div>
                    <div class="corruption-sidebar-body">
                        <?php if (!empty($report_detail->files)): ?>
                            <div class="corruption-files-list">
                                <?php foreach ($report_detail->files as $file): ?>
                                    <div class="corruption-file-item">
                                        <div class="corruption-file-info">
                                            <div class="corruption-file-icon">
                                                <?php
                                                $ext = strtolower($file->file_extension ?? '');
                                                $file_icons = [
                                                    'pdf' => 'fas fa-file-pdf',
                                                    'doc' => 'fas fa-file-word',
                                                    'docx' => 'fas fa-file-word',
                                                    'xls' => 'fas fa-file-excel',
                                                    'xlsx' => 'fas fa-file-excel',
                                                    'jpg' => 'fas fa-file-image',
                                                    'jpeg' => 'fas fa-file-image',
                                                    'png' => 'fas fa-file-image',
                                                    'gif' => 'fas fa-file-image'
                                                ];
                                                ?>
                                                <i class="<?= $file_icons[$ext] ?? 'fas fa-file' ?>"></i>
                                            </div>
                                            <div class="corruption-file-details">
                                                <div class="corruption-file-name">
                                                    <?= htmlspecialchars($file->file_original_name ?? 'ไม่ระบุชื่อไฟล์') ?>
                                                </div>
                                                <div class="corruption-file-size">
                                                    <?= formatFileSize($file->file_size ?? 0) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="corruption-file-download" 
                                                onclick="viewFile('<?= site_url('Corruption/view_evidence/' . ($file->file_id ?? '')) ?>', '<?= htmlspecialchars($file->file_original_name ?? '', ENT_QUOTES) ?>')">
                                            <i class="fas fa-eye"></i>
                                            ดูไฟล์
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-folder-open" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2">ไม่มีไฟล์หลักฐาน</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- History Timeline -->
                <div class="corruption-sidebar-card">
                    <div class="corruption-sidebar-header">
                        <i class="fas fa-history"></i>
                        ประวัติการดำเนินการ
                    </div>
                    <div class="corruption-sidebar-body">
                        <?php if (!empty($report_detail->history)): ?>
                            <div class="corruption-timeline">
                                <?php foreach ($report_detail->history as $history): ?>
                                    <div class="corruption-timeline-item">
                                        <div class="corruption-timeline-content">
                                            <div class="corruption-timeline-header">
                                                <div class="corruption-timeline-title">
                                                    <?php
                                                    $action_labels = [
                                                        'created' => 'สร้างรายงาน',
                                                        'status_changed' => 'เปลี่ยนสถานะ',
                                                        'assigned' => 'มอบหมายงาน',
                                                        'commented' => 'เพิ่มความคิดเห็น',
                                                        'evidence_added' => 'เพิ่มหลักฐาน',
                                                        'evidence_removed' => 'ลบหลักฐาน',
                                                        'archived' => 'เก็บถาวร'
                                                    ];
                                                    echo $action_labels[$history->action_type ?? ''] ?? $history->action_type ?? 'ดำเนินการ';
                                                    ?>
                                                </div>
                                                <div class="corruption-timeline-date">
                                                    <?php
                                                    if (!empty($history->action_date)) {
                                                        $action_date = new DateTime($history->action_date);
                                                        echo $action_date->format('d/m/Y H:i');
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="corruption-timeline-description">
                                                <?= htmlspecialchars($history->action_description ?? 'ไม่มีรายละเอียด') ?>
                                            </div>
                                            <?php if (!empty($history->action_by)): ?>
                                                <div class="corruption-timeline-by">
                                                    <small class="text-muted">โดย: <?= htmlspecialchars($history->action_by) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-clock" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2">ยังไม่มีประวัติการดำเนินการ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===================================================================
// *** CORRUPTION DETAIL PAGE FUNCTIONS ***
// ===================================================================

const CorruptionDetailConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Corruption/update_status") ?>',
    reportId: '<?= $report_detail->corruption_id ?? '' ?>',
    reportNumber: '<?= $report_detail->corruption_report_id ?? '' ?>'
};

/**
 * ดูไฟล์ในแท็บใหม่
 */
function viewFile(fileUrl, fileName) {
    //console.log('Viewing file:', fileUrl, fileName);
    
    try {
        // เปิดไฟล์ในแท็บใหม่
        const newWindow = window.open(fileUrl, '_blank');
        
        // ตรวจสอบว่าเปิดแท็บใหม่ได้หรือไม่ (popup blocker)
        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
            // ถ้าถูก popup blocker บล็อก ให้แสดงข้อความแจ้งเตือน
            Swal.fire({
                title: 'ไม่สามารถเปิดแท็บใหม่ได้',
                text: 'กรุณาอนุญาตให้เว็บไซต์เปิดป็อปอัพ หรือคลิกลิงก์ด้านล่างเพื่อดูไฟล์',
                icon: 'warning',
                html: `
                    <p>กรุณาอนุญาตให้เว็บไซต์เปิดป็อปอัพ หรือคลิกลิงก์ด้านล่างเพื่อดูไฟล์</p>
                    <a href="${fileUrl}" target="_blank" class="btn btn-primary mt-2">
                        <i class="fas fa-external-link-alt"></i> เปิดไฟล์ ${fileName}
                    </a>
                `,
                showConfirmButton: false,
                showCloseButton: true
            });
        } else {
            // แสดงข้อความว่ากำลังเปิดไฟล์
            Swal.fire({
                title: 'กำลังเปิดไฟล์',
                text: `กำลังเปิดไฟล์ "${fileName}" ในแท็บใหม่`,
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });
        }
        
    } catch (error) {
        console.error('View file error:', error);
        
        // ถ้าเกิดข้อผิดพลาด ให้เปิดในแท็บเดียวกัน
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'จะเปิดไฟล์ในหน้าต่างปัจจุบัน',
            icon: 'warning',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = fileUrl;
        });
    }
}

/**
 * พิมพ์รายงาน
 */
function printReport() {
    // สร้างเนื้อหาสำหรับพิมพ์
    const reportData = {
        reportNumber: '<?= $report_detail->corruption_report_id ?? '' ?>',
        reportType: '<?= get_corruption_type_display($report_detail->corruption_type ?? '') ?>',
        status: '<?= get_corruption_status_display($report_detail->report_status ?? '') ?>',
        priority: '<?= get_corruption_priority_display($report_detail->priority_level ?? 'normal') ?>',
        subject: <?= json_encode($report_detail->complaint_subject ?? '', JSON_UNESCAPED_UNICODE) ?>,
        details: <?= json_encode($report_detail->complaint_details ?? '', JSON_UNESCAPED_UNICODE) ?>,
        incidentDate: '<?= !empty($report_detail->incident_date) ? date('d/m/Y', strtotime($report_detail->incident_date)) : 'ไม่ระบุ' ?>',
        incidentTime: '<?= !empty($report_detail->incident_time) ? $report_detail->incident_time . ' น.' : 'ไม่ระบุ' ?>',
        incidentLocation: <?= json_encode($report_detail->incident_location ?? 'ไม่ระบุ', JSON_UNESCAPED_UNICODE) ?>,
        perpetratorName: <?= json_encode($report_detail->perpetrator_name ?? 'ไม่ระบุ', JSON_UNESCAPED_UNICODE) ?>,
        perpetratorPosition: <?= json_encode($report_detail->perpetrator_position ?? 'ไม่ระบุ', JSON_UNESCAPED_UNICODE) ?>,
        perpetratorDepartment: <?= json_encode($report_detail->perpetrator_department ?? 'ไม่ระบุ', JSON_UNESCAPED_UNICODE) ?>,
        isAnonymous: <?= ($report_detail->is_anonymous ?? false) ? 'true' : 'false' ?>,
        reporterName: <?= json_encode($report_detail->reporter_name ?? 'ไม่ระบุ', JSON_UNESCAPED_UNICODE) ?>,
        reporterPhone: <?= json_encode($report_detail->reporter_phone ?? 'ไม่ระบุ', JSON_UNESCAPED_UNICODE) ?>,
        reporterEmail: <?= json_encode($report_detail->reporter_email ?? 'ไม่ระบุ', JSON_UNESCAPED_UNICODE) ?>,
        createdDate: '<?= !empty($report_detail->created_at) ? date('d/m/Y H:i', strtotime($report_detail->created_at)) : 'ไม่ระบุ' ?>',
        evidenceDescription: <?= json_encode($report_detail->evidence_description ?? '', JSON_UNESCAPED_UNICODE) ?>,
        filesCount: <?= count($report_detail->files ?? []) ?>
    };
    
    // สร้างหน้าต่างใหม่สำหรับพิมพ์
    const printWindow = window.open('', '_blank');
    
    const printContent = `
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>รายงานการทุจริต #${reportData.reportNumber}</title>
            <style>
                /* ===== PRINT STYLES ===== */
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Sarabun', 'TH SarabunPSK', Arial, sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                    background: white;
                    margin: 0;
                    padding: 20px;
                }
                
                .print-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                }
                
                /* ===== HEADER ===== */
                .print-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding: 20px;
                    border: 2px solid #dc3545;
                    border-radius: 10px;
                    background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
                }
                
                .print-header h1 {
                    color: #dc3545;
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                
                .print-header h2 {
                    color: #666;
                    font-size: 18px;
                    font-weight: normal;
                    margin-bottom: 15px;
                }
                
                .print-header .report-number {
                    background: #dc3545;
                    color: white;
                    padding: 8px 20px;
                    border-radius: 20px;
                    font-size: 16px;
                    font-weight: bold;
                    display: inline-block;
                }
                
                /* ===== STATUS BAR ===== */
                .print-status-bar {
                    display: flex;
                    justify-content: space-around;
                    margin-bottom: 30px;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                    border: 1px solid #dee2e6;
                }
                
                .status-item {
                    text-align: center;
                    flex: 1;
                }
                
                .status-label {
                    font-size: 12px;
                    color: #666;
                    text-transform: uppercase;
                    margin-bottom: 5px;
                    font-weight: bold;
                }
                
                .status-value {
                    font-size: 14px;
                    font-weight: bold;
                    color: #333;
                    padding: 5px 10px;
                    background: white;
                    border-radius: 5px;
                    border: 1px solid #ddd;
                }
                
                /* ===== SECTIONS ===== */
                .print-section {
                    margin-bottom: 25px;
                    page-break-inside: avoid;
                }
                
                .section-title {
                    font-size: 16px;
                    font-weight: bold;
                    color: #dc3545;
                    margin-bottom: 15px;
                    padding: 10px 15px;
                    background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
                    border-left: 4px solid #dc3545;
                    border-radius: 5px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                
                .section-content {
                    padding: 0 15px;
                }
                
                /* ===== FIELDS ===== */
                .field-group {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 15px;
                }
                
                .field-group.single {
                    grid-template-columns: 1fr;
                }
                
                .field {
                    margin-bottom: 15px;
                }
                
                .field-label {
                    font-weight: bold;
                    color: #555;
                    margin-bottom: 5px;
                    font-size: 13px;
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                }
                
                .field-value {
                    background: #f8f9fa;
                    padding: 10px 12px;
                    border-radius: 5px;
                    border: 1px solid #e9ecef;
                    min-height: 35px;
                    display: flex;
                    align-items: center;
                    word-wrap: break-word;
                }
                
                .field-value.multiline {
                    white-space: pre-wrap;
                    line-height: 1.8;
                    min-height: 80px;
                    align-items: flex-start;
                    padding-top: 12px;
                }
                
                /* ===== ANONYMOUS NOTICE ===== */
                .anonymous-notice {
                    background: linear-gradient(135deg, #fff3cd 0%, #ffffff 100%);
                    border: 1px solid #ffeaa7;
                    border-radius: 8px;
                    padding: 15px;
                    text-align: center;
                    color: #856404;
                    font-weight: bold;
                    margin: 15px 0;
                }
                
                .anonymous-notice i {
                    margin-right: 8px;
                    font-size: 16px;
                }
                
                /* ===== FOOTER ===== */
                .print-footer {
                    margin-top: 40px;
                    padding: 20px;
                    border-top: 2px solid #dee2e6;
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                }
                
                .footer-info {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 15px;
                }
                
                .footer-item {
                    text-align: center;
                }
                
                .footer-label {
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                
                /* ===== PRINT SPECIFIC ===== */
                @media print {
                    body {
                        margin: 0;
                        padding: 15px;
                        font-size: 12px;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                    }
                    
                    .print-container {
                        max-width: none;
                        margin: 0;
                    }
                    
                    .print-header h1 {
                        font-size: 20px;
                    }
                    
                    .print-header h2 {
                        font-size: 16px;
                    }
                    
                    .section-title {
                        font-size: 14px;
                    }
                    
                    .field-group {
                        gap: 15px;
                    }
                    
                    .print-section {
                        page-break-inside: avoid;
                        margin-bottom: 20px;
                    }
                    
                    .print-footer {
                        page-break-inside: avoid;
                    }
                }
                
                /* ===== RESPONSIVE ===== */
                @media screen and (max-width: 768px) {
                    .field-group {
                        grid-template-columns: 1fr;
                        gap: 10px;
                    }
                    
                    .print-status-bar {
                        flex-direction: column;
                        gap: 10px;
                    }
                    
                    .footer-info {
                        grid-template-columns: 1fr;
                        gap: 10px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-container">
                <!-- Header -->
                <div class="print-header">
                    <h1>🛡️ รายงานการทุจริต</h1>
                    <h2>${reportData.reportType}</h2>
                    <div class="report-number">#${reportData.reportNumber}</div>
                </div>
                
                <!-- Status Bar -->
                <div class="print-status-bar">
                    <div class="status-item">
                        <div class="status-label">สถานะปัจจุบัน</div>
                        <div class="status-value">${reportData.status}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">ความสำคัญ</div>
                        <div class="status-value">${reportData.priority}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">วันที่แจ้ง</div>
                        <div class="status-value">${reportData.createdDate}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">ไฟล์หลักฐาน</div>
                        <div class="status-value">${reportData.filesCount} ไฟล์</div>
                    </div>
                </div>
                
                <!-- ข้อมูลพื้นฐาน -->
                <div class="print-section">
                    <div class="section-title">📋 ข้อมูลพื้นฐาน</div>
                    <div class="section-content">
                        <div class="field">
                            <div class="field-label">หัวข้อเรื่องร้องเรียน</div>
                            <div class="field-value">${reportData.subject}</div>
                        </div>
                    </div>
                </div>
                
                <!-- รายละเอียดเหตุการณ์ -->
                <div class="print-section">
                    <div class="section-title">📝 รายละเอียดเหตุการณ์</div>
                    <div class="section-content">
                        <div class="field">
                            <div class="field-label">รายละเอียด</div>
                            <div class="field-value multiline">${reportData.details}</div>
                        </div>
                    </div>
                </div>
                
                <!-- ข้อมูลเหตุการณ์ -->
                <div class="print-section">
                    <div class="section-title">📍 ข้อมูลเหตุการณ์</div>
                    <div class="section-content">
                        <div class="field-group">
                            <div class="field">
                                <div class="field-label">วันที่เกิดเหตุ</div>
                                <div class="field-value">${reportData.incidentDate}</div>
                            </div>
                            <div class="field">
                                <div class="field-label">เวลาที่เกิดเหตุ</div>
                                <div class="field-value">${reportData.incidentTime}</div>
                            </div>
                        </div>
                        <div class="field">
                            <div class="field-label">สถานที่เกิดเหตุ</div>
                            <div class="field-value">${reportData.incidentLocation}</div>
                        </div>
                    </div>
                </div>
                
                <!-- ข้อมูลผู้กระทำผิด -->
                <div class="print-section">
                    <div class="section-title">👤 ข้อมูลผู้กระทำผิด</div>
                    <div class="section-content">
                        <div class="field-group">
                            <div class="field">
                                <div class="field-label">ชื่อผู้กระทำผิด</div>
                                <div class="field-value">${reportData.perpetratorName}</div>
                            </div>
                            <div class="field">
                                <div class="field-label">ตำแหน่ง</div>
                                <div class="field-value">${reportData.perpetratorPosition}</div>
                            </div>
                        </div>
                        <div class="field">
                            <div class="field-label">หน่วยงาน/แผนก</div>
                            <div class="field-value">${reportData.perpetratorDepartment}</div>
                        </div>
                    </div>
                </div>
                
                <!-- ข้อมูลผู้แจ้ง -->
                <div class="print-section">
                    <div class="section-title">🗣️ ข้อมูลผู้แจ้ง</div>
                    <div class="section-content">
                        ${reportData.isAnonymous === 'true' ? `
                            <div class="anonymous-notice">
                                <i>🔒</i> รายงานแบบไม่ระบุตัวตน - ผู้แจ้งขอไม่เปิดเผยข้อมูลส่วนตัว
                            </div>
                        ` : `
                            <div class="field-group">
                                <div class="field">
                                    <div class="field-label">ชื่อผู้แจ้ง</div>
                                    <div class="field-value">${reportData.reporterName}</div>
                                </div>
                                <div class="field">
                                    <div class="field-label">เบอร์โทรศัพท์</div>
                                    <div class="field-value">${reportData.reporterPhone}</div>
                                </div>
                            </div>
                            <div class="field">
                                <div class="field-label">อีเมล</div>
                                <div class="field-value">${reportData.reporterEmail}</div>
                            </div>
                        `}
                    </div>
                </div>
                
                ${reportData.evidenceDescription ? `
                <!-- รายละเอียดหลักฐาน -->
                <div class="print-section">
                    <div class="section-title">🔍 รายละเอียดหลักฐาน</div>
                    <div class="section-content">
                        <div class="field">
                            <div class="field-value multiline">${reportData.evidenceDescription}</div>
                        </div>
                    </div>
                </div>
                ` : ''}
                
                <!-- Footer -->
                <div class="print-footer">
                    <div class="footer-info">
                        <div class="footer-item">
                            <div class="footer-label">วันที่พิมพ์</div>
                            <div>${new Date().toLocaleDateString('th-TH', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })}</div>
                        </div>
                        <div class="footer-item">
                            <div class="footer-label">เวลาที่พิมพ์</div>
                            <div>${new Date().toLocaleTimeString('th-TH')}</div>
                        </div>
                        <div class="footer-item">
                            <div class="footer-label">เอกสารเลขที่</div>
                            <div>${reportData.reportNumber}</div>
                        </div>
                    </div>
                    <hr style="margin: 15px 0; border: none; border-top: 1px solid #ddd;">
                    <p><strong>หมายเหตุ:</strong> เอกสารนี้เป็นการพิมพ์จากระบบจัดการรายงานการทุจริต</p>
                    <p style="margin-top: 5px; font-style: italic;">สำหรับการใช้งานภายในหน่วยงานเท่านั้น</p>
                </div>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // รอให้โหลดเสร็จแล้วพิมพ์
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 1000);
}

/**
 * จัดการ Form อัปเดตสถานะ
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Corruption Detail Page loading...');
    
    try {
        // เพิ่ม smooth scrolling สำหรับ anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // เพิ่ม tooltip สำหรับปุ่มต่างๆ
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // เพิ่ม keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P สำหรับพิมพ์
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printReport();
            }
            
            // ESC สำหรับปิด modal
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal.show');
                openModals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) modalInstance.hide();
                });
            }
        });
        
        //console.log('✅ Corruption Detail Page initialized successfully');
        
    } catch (error) {
        console.error('❌ Initialization error:', error);
    }
});

// ===================================================================
// *** FLASH MESSAGES ***
// ===================================================================

// Success message
<?php if (isset($success_message) && !empty($success_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'สำเร็จ!',
        text: <?= json_encode($success_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
});
<?php endif; ?>

// Error message
<?php if (isset($error_message) && !empty($error_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'เกิดข้อผิดพลาด',
        text: <?= json_encode($error_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'error'
    });
});
<?php endif; ?>

// Info message
<?php if (isset($info_message) && !empty($info_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'ข้อมูล',
        text: <?= json_encode($info_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'info',
        timer: 4000,
        showConfirmButton: false
    });
});
<?php endif; ?>

// Warning message
<?php if (isset($warning_message) && !empty($warning_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'คำเตือน',
        text: <?= json_encode($warning_message, JSON_UNESCAPED_UNICODE) ?>,
        icon: 'warning',
        timer: 4000,
        showConfirmButton: false
    });
});
<?php endif; ?>

// ===================================================================
// *** RESPONSIVE BEHAVIOR ***
// ===================================================================

// จัดการ responsive behavior
window.addEventListener('resize', function() {
    // ปรับขนาด elements ตามหน้าจอ
    const isMobile = window.innerWidth < 768;
    
    if (isMobile) {
        // ซ่อนข้อมูลที่ไม่จำเป็นใน mobile
        document.querySelectorAll('.corruption-timeline-date').forEach(date => {
            date.style.display = 'block';
            date.style.marginTop = '0.5rem';
        });
    } else {
        // แสดงข้อมูลเต็มใน desktop
        document.querySelectorAll('.corruption-timeline-date').forEach(date => {
            date.style.display = 'inline';
            date.style.marginTop = '0';
        });
    }
});

// เรียกใช้ครั้งแรก
window.dispatchEvent(new Event('resize'));

//console.log('📱 Corruption Detail Page loaded successfully');
</script>