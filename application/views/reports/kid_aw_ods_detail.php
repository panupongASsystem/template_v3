<?php
// ===================================================================
// kid_aw_ods_detail.php - หน้ารายละเอียดเงินสนับสนุนเด็กแรกเกิด
// ===================================================================

// Helper function สำหรับ CSS class ของสถานะเงินสนับสนุนเด็ก
if (!function_exists('get_kid_aw_ods_status_class')) {
    function get_kid_aw_ods_status_class($status) {
        switch($status) {
            case 'submitted': return 'submitted';
            case 'reviewing': return 'reviewing';
            case 'approved': return 'approved';
            case 'rejected': return 'rejected';
            case 'completed': return 'completed';
            default: return 'submitted';
        }
    }
}

// Helper function สำหรับแสดงสถานะเป็นภาษาไทย
if (!function_exists('get_kid_aw_ods_status_display')) {
    function get_kid_aw_ods_status_display($status) {
        switch($status) {
            case 'submitted': return 'ยื่นเรื่องแล้ว';
            case 'reviewing': return 'กำลังพิจารณา';
            case 'approved': return 'อนุมัติแล้ว';
            case 'rejected': return 'ไม่อนุมัติ';
            case 'completed': return 'เสร็จสิ้น';
            default: return 'ยื่นเรื่องแล้ว';
        }
    }
}

// Helper function สำหรับแสดงประเภทเงินสนับสนุนเด็ก
if (!function_exists('get_kid_aw_ods_type_display')) {
    function get_kid_aw_ods_type_display($type) {
        switch($type) {
            case 'children': return 'เด็กทั่วไป';
            case 'disabled': return 'เด็กพิการ';
            default: return 'เด็กทั่วไป';
        }
    }
}

// Helper function สำหรับความสำคัญ
if (!function_exists('get_kid_aw_ods_priority_display')) {
    function get_kid_aw_ods_priority_display($priority) {
        switch($priority) {
            case 'low': return 'ต่ำ';
            case 'normal': return 'ปกติ';
            case 'high': return 'สูง';
            case 'urgent': return 'เร่งด่วน';
            default: return 'ปกติ';
        }
    }
}

// Helper function สำหรับประเภทผู้ใช้
if (!function_exists('get_kid_aw_ods_user_type_display')) {
    function get_kid_aw_ods_user_type_display($user_type) {
        switch($user_type) {
            case 'guest': return 'ผู้ใช้ทั่วไป';
            case 'public': return 'สมาชิก';
            case 'staff': return 'เจ้าหน้าที่';
            default: return 'ผู้ใช้ทั่วไป';
        }
    }
}

// ตรวจสอบข้อมูลเงินสนับสนุนเด็ก
$kid_data = $kid_aw_ods_detail ?? null;
if (!$kid_data) {
    show_404();
    return;
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ===== KID AW ODS DETAIL PAGE STYLES ===== */
.kid-detail-page {
    --kid-primary-color: #4fc3f7;
    --kid-primary-light: #81c9f7;
    --kid-secondary-color: #e1f5fe;
    --kid-success-color: #66bb6a;
    --kid-warning-color: #ffa726;
    --kid-danger-color: #ef5350;
    --kid-info-color: #42a5f5;
    --kid-purple-color: #ab47bc;
    --kid-light-bg: #f8fdff;
    --kid-white: #ffffff;
    --kid-gray-50: #fafafa;
    --kid-gray-100: #f5f5f5;
    --kid-gray-200: #eeeeee;
    --kid-gray-300: #e0e0e0;
    --kid-gray-400: #bdbdbd;
    --kid-gray-500: #9e9e9e;
    --kid-gray-600: #757575;
    --kid-gray-700: #616161;
    --kid-gray-800: #424242;
    --kid-gray-900: #212121;
    --kid-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.03);
    --kid-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04);
    --kid-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04);
    --kid-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.06), 0 4px 6px -4px rgb(0 0 0 / 0.04);
    --kid-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.06), 0 8px 10px -6px rgb(0 0 0 / 0.04);
    --kid-border-radius: 12px;
    --kid-border-radius-lg: 16px;
    --kid-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.kid-detail-page {
    background: linear-gradient(135deg, #e1f5fe 0%, #f3e5f5 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--kid-gray-700);
    min-height: 100vh;
    padding: 2rem 0;
}

.kid-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* ===== PAGE HEADER ===== */
.kid-detail-header {
    background: linear-gradient(135deg, rgba(79, 195, 247, 0.9) 0%, rgba(129, 201, 247, 0.7) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--kid-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--kid-shadow-lg);
    position: relative;
    overflow: hidden;
}

.kid-detail-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.kid-detail-header h1 {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
}

.kid-detail-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
    position: relative;
    z-index: 1;
}

.kid-detail-actions {
    position: absolute;
    top: 2rem;
    right: 2rem;
    display: flex;
    gap: 1rem;
    z-index: 2;
}

.kid-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--kid-transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    cursor: pointer;
}

.kid-btn-white {
    background: rgba(255, 255, 255, 0.9);
    color: var(--kid-primary-color);
    backdrop-filter: blur(10px);
}

.kid-btn-white:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-lg);
    color: var(--kid-primary-color);
}

/* ===== CONTENT GRID ===== */
.kid-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

/* ===== MAIN CONTENT CARD ===== */
.kid-main-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    box-shadow: var(--kid-shadow-md);
    overflow: hidden;
    border: 1px solid var(--kid-gray-100);
}

.kid-card-header {
    background: linear-gradient(135deg, var(--kid-gray-50) 0%, var(--kid-gray-100) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--kid-gray-200);
    display: flex;
    align-items: center;
    justify-content: between;
}

.kid-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kid-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.kid-status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-left: auto;
}

.kid-status-badge.submitted {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border: 1px solid rgba(255, 152, 0, 0.3);
}

.kid-status-badge.reviewing {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.kid-status-badge.approved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.kid-status-badge.rejected {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

.kid-status-badge.completed {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border: 1px solid rgba(156, 39, 176, 0.3);
}

.kid-card-body {
    padding: 2rem;
}

/* ===== INFO SECTIONS ===== */
.kid-info-section {
    margin-bottom: 2rem;
}

.kid-info-section:last-child {
    margin-bottom: 0;
}

.kid-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--kid-gray-900);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--kid-gray-100);
}

.kid-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.kid-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.kid-info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--kid-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.kid-info-value {
    font-size: 1rem;
    color: var(--kid-gray-900);
    padding: 0.75rem 1rem;
    background: var(--kid-gray-50);
    border-radius: 8px;
    border: 1px solid var(--kid-gray-200);
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

.kid-info-value.highlight {
    background: linear-gradient(135deg, var(--kid-secondary-color), #e3f2fd);
    border-color: var(--kid-primary-light);
    color: var(--kid-primary-color);
    font-weight: 600;
}

/* ===== SIDEBAR CARDS ===== */
.kid-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.kid-sidebar-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    box-shadow: var(--kid-shadow-md);
    overflow: hidden;
    border: 1px solid var(--kid-gray-100);
}

.kid-sidebar-header {
    background: linear-gradient(135deg, var(--kid-primary-color), var(--kid-primary-light));
    color: white;
    padding: 1rem 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-sidebar-body {
    padding: 1.5rem;
}

/* ===== QUICK INFO CARD ===== */
.kid-quick-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.kid-quick-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: var(--kid-gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--kid-primary-color);
}

.kid-quick-label {
    font-size: 0.875rem;
    color: var(--kid-gray-600);
    font-weight: 500;
}

.kid-quick-value {
    font-weight: 600;
    color: var(--kid-gray-900);
}

/* ===== FILES SECTION ===== */
.kid-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.kid-file-card {
    background: var(--kid-gray-50);
    border: 2px solid var(--kid-gray-200);
    border-radius: var(--kid-border-radius);
    padding: 1.5rem;
    text-align: center;
    transition: var(--kid-transition);
    cursor: pointer;
}

.kid-file-card:hover {
    border-color: var(--kid-primary-color);
    background: var(--kid-secondary-color);
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-md);
}

.kid-file-icon {
    font-size: 2.5rem;
    color: var(--kid-primary-color);
    margin-bottom: 1rem;
}

.kid-file-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--kid-gray-900);
    margin-bottom: 0.5rem;
    word-break: break-word;
}

.kid-file-meta {
    font-size: 0.75rem;
    color: var(--kid-gray-600);
}

.kid-file-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
}

/* ===== HISTORY SECTION ===== */
.kid-history-timeline {
    position: relative;
    padding-left: 2rem;
}

.kid-history-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--kid-gray-200);
}

.kid-history-item {
    position: relative;
    margin-bottom: 1.5rem;
    background: var(--kid-white);
    border: 1px solid var(--kid-gray-200);
    border-radius: var(--kid-border-radius);
    padding: 1.5rem;
    box-shadow: var(--kid-shadow-sm);
}

.kid-history-item::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 1.5rem;
    width: 12px;
    height: 12px;
    background: var(--kid-primary-color);
    border-radius: 50%;
    border: 3px solid var(--kid-white);
    box-shadow: 0 0 0 2px var(--kid-primary-color);
}

.kid-history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.kid-history-action {
    font-weight: 600;
    color: var(--kid-gray-900);
}

.kid-history-date {
    font-size: 0.875rem;
    color: var(--kid-gray-600);
}

.kid-history-description {
    color: var(--kid-gray-700);
    line-height: 1.5;
}

.kid-history-by {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--kid-gray-600);
    font-style: italic;
}

/* ===== ACTION BUTTONS ===== */
.kid-action-section {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    box-shadow: var(--kid-shadow-md);
    border: 1px solid var(--kid-gray-100);
    margin-top: 2rem;
}

.kid-action-header {
    background: linear-gradient(135deg, var(--kid-gray-50) 0%, var(--kid-gray-100) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--kid-gray-200);
}

.kid-action-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--kid-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-action-body {
    padding: 2rem;
}

.kid-action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.kid-btn-action {
    padding: 1rem 1.5rem;
    border-radius: var(--kid-border-radius);
    border: 2px solid transparent;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--kid-transition);
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    min-height: 3rem;
}

.kid-btn-action.primary {
    background: linear-gradient(135deg, var(--kid-primary-color), var(--kid-primary-light));
    color: white;
}

.kid-btn-action.primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

.kid-btn-action.success {
    background: linear-gradient(135deg, var(--kid-success-color), #66bb6a);
    color: white;
}

.kid-btn-action.success:hover {
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

.kid-btn-action.warning {
    background: linear-gradient(135deg, var(--kid-warning-color), #ffa726);
    color: white;
}

.kid-btn-action.warning:hover {
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

.kid-btn-action.danger {
    background: linear-gradient(135deg, var(--kid-danger-color), #ef5350);
    color: white;
}

.kid-btn-action.danger:hover {
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

.kid-btn-action.secondary {
    background: var(--kid-gray-100);
    color: var(--kid-gray-700);
    border-color: var(--kid-gray-300);
}

.kid-btn-action.secondary:hover {
    background: var(--kid-gray-200);
    color: var(--kid-gray-800);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .kid-detail-page {
        padding: 1rem 0;
    }
    
    .kid-detail-container {
        padding: 0 0.5rem;
    }
    
    .kid-detail-header {
        padding: 1.5rem;
        text-align: center;
    }
    
    .kid-detail-header h1 {
        font-size: 1.8rem;
    }
    
    .kid-detail-actions {
        position: static;
        margin-top: 1rem;
        justify-content: center;
    }
    
    .kid-detail-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .kid-card-body {
        padding: 1.5rem;
    }
    
    .kid-info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .kid-files-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .kid-action-grid {
        grid-template-columns: 1fr;
    }
    
    .kid-history-timeline {
        padding-left: 1.5rem;
    }
    
    .kid-history-item::before {
        left: -1.5rem;
    }
}

@media (max-width: 480px) {
    .kid-detail-header h1 {
        font-size: 1.5rem;
    }
    
    .kid-btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
    
    .kid-card-header {
        padding: 1rem 1.5rem;
    }
    
    .kid-card-body {
        padding: 1rem;
    }
    
    .kid-sidebar-body {
        padding: 1rem;
    }
}

/* ===== ANIMATIONS ===== */
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

.kid-main-card,
.kid-sidebar-card,
.kid-action-section {
    animation: fadeInUp 0.5s ease-out;
}

/* ===== LOADING STATES ===== */
.kid-loading {
    opacity: 0.6;
    pointer-events: none;
}

.kid-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--kid-gray-300);
    border-top: 2px solid var(--kid-primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ===== OTHER BADGES ===== */
.kid-priority-badge, 
.kid-type-badge, 
.kid-user-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.kid-priority-badge.low {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.kid-priority-badge.normal {
    background: var(--kid-gray-100);
    color: var(--kid-gray-700);
}

.kid-priority-badge.high {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.kid-priority-badge.urgent {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.kid-type-badge.children {
    background: linear-gradient(135deg, var(--kid-secondary-color), #c1e7ff);
    color: var(--kid-primary-color);
}

.kid-type-badge.disabled {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    color: #6a1b9a;
}

.kid-user-type-badge.guest {
    background: var(--kid-gray-100);
    color: var(--kid-gray-600);
}

.kid-user-type-badge.public {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.kid-user-type-badge.staff {
    background: linear-gradient(135deg, var(--kid-secondary-color), #c1e7ff);
    color: var(--kid-primary-color);
}
</style>

<div class="kid-detail-page">
    <div class="kid-detail-container">
        <!-- ===== PAGE HEADER ===== -->
        <header class="kid-detail-header">
            <div class="kid-detail-actions">
                <a href="<?= site_url('Kid_aw_ods/kid_aw_ods') ?>" class="kid-btn kid-btn-white">
                    <i class="fas fa-arrow-left"></i>
                    กลับไปรายการ
                </a>
                <?php if (isset($can_handle_kid) && $can_handle_kid): ?>
                    <button class="kid-btn kid-btn-white" onclick="printPage()">
                        <i class="fas fa-print"></i>
                        พิมพ์
                    </button>
                <?php endif; ?>
            </div>
            
            <h1>
                <i class="fas fa-baby me-3"></i>
                รายละเอียดเงินอุดหนุนเด็กแรกเกิด #<?= htmlspecialchars($kid_data->kid_aw_ods_id) ?>
            </h1>
            <p class="kid-detail-subtitle">
                ประเภท: <?= get_kid_aw_ods_type_display($kid_data->kid_aw_ods_type ?? 'children') ?>
                | ยื่นโดย: <?= htmlspecialchars($kid_data->kid_aw_ods_by) ?>
                | วันที่: <?php
                    $thai_months = [
                        '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
                        '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
                        '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                    ];
                    
                    $date = date('j', strtotime($kid_data->kid_aw_ods_datesave));
                    $month = $thai_months[date('m', strtotime($kid_data->kid_aw_ods_datesave))];
                    $year = date('Y', strtotime($kid_data->kid_aw_ods_datesave)) + 543;
                    
                    echo $date . ' ' . $month . ' ' . $year;
                ?>
            </p>
        </header>

        <!-- ===== MAIN CONTENT GRID ===== -->
        <div class="kid-detail-grid">
            <!-- ===== MAIN CONTENT ===== -->
            <div class="kid-main-content">
                <!-- ===== PERSONAL INFORMATION CARD ===== -->
                <div class="kid-main-card">
                    <div class="kid-card-header">
                        <h2 class="kid-card-title">
                            <i class="fas fa-user"></i>
                            ข้อมูลส่วนบุคคล
                        </h2>
                        <span class="kid-status-badge <?= get_kid_aw_ods_status_class($kid_data->kid_aw_ods_status) ?>">
                            <?= get_kid_aw_ods_status_display($kid_data->kid_aw_ods_status) ?>
                        </span>
                    </div>
                    <div class="kid-card-body">
                        <div class="kid-info-section">
                            <h3 class="kid-section-title">
                                <i class="fas fa-id-card"></i>
                                ข้อมูลพื้นฐาน
                            </h3>
                            <div class="kid-info-grid">
                                <div class="kid-info-item">
                                    <span class="kid-info-label">หมายเลขอ้างอิง</span>
                                    <div class="kid-info-value highlight">
                                        #<?= htmlspecialchars($kid_data->kid_aw_ods_id) ?>
                                    </div>
                                </div>
                                <div class="kid-info-item">
                                    <span class="kid-info-label">ชื่อ-นามสกุล</span>
                                    <div class="kid-info-value">
                                        <?= htmlspecialchars($kid_data->kid_aw_ods_by) ?>
                                    </div>
                                </div>
                                <div class="kid-info-item">
                                    <span class="kid-info-label">เบอร์โทรศัพท์</span>
                                    <div class="kid-info-value">
                                        <?= htmlspecialchars($kid_data->kid_aw_ods_phone) ?>
                                    </div>
                                </div>
                                <div class="kid-info-item">
                                    <span class="kid-info-label">อีเมล</span>
                                    <div class="kid-info-value">
                                        <?= !empty($kid_data->kid_aw_ods_email) ? htmlspecialchars($kid_data->kid_aw_ods_email) : 'ไม่ระบุ' ?>
                                    </div>
                                </div>
                                <div class="kid-info-item">
                                    <span class="kid-info-label">เลขบัตรประชาชน</span>
                                    <div class="kid-info-value">
                                        <?php if (!empty($kid_data->kid_aw_ods_number)): ?>
                                            <?= htmlspecialchars(substr($kid_data->kid_aw_ods_number, 0, 3) . '-****-****-**-' . substr($kid_data->kid_aw_ods_number, -2)) ?>
                                        <?php else: ?>
                                            ไม่ระบุ
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="kid-info-item">
                                    <span class="kid-info-label">ประเภทเงินสนับสนุน</span>
                                    <div class="kid-info-value">
                                        <span class="kid-type-badge <?= $kid_data->kid_aw_ods_type ?? 'children' ?>">
                                            <?= get_kid_aw_ods_type_display($kid_data->kid_aw_ods_type ?? 'children') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kid-info-section">
                            <h3 class="kid-section-title">
                                <i class="fas fa-map-marker-alt"></i>
                                ที่อยู่
                            </h3>
                            <div class="kid-info-grid">
                                <div class="kid-info-item" style="grid-column: 1 / -1;">
                                    <span class="kid-info-label">ที่อยู่หลัก</span>
                                    <div class="kid-info-value">
                                        <?= htmlspecialchars($kid_data->kid_aw_ods_address) ?>
                                    </div>
                                </div>
                                <?php if (!empty($kid_data->guest_province) || !empty($kid_data->guest_amphoe) || !empty($kid_data->guest_district)): ?>
                                    <div class="kid-info-item">
                                        <span class="kid-info-label">จังหวัด</span>
                                        <div class="kid-info-value">
                                            <?= htmlspecialchars($kid_data->guest_province ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                    <div class="kid-info-item">
                                        <span class="kid-info-label">อำเภอ</span>
                                        <div class="kid-info-value">
                                            <?= htmlspecialchars($kid_data->guest_amphoe ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                    <div class="kid-info-item">
                                        <span class="kid-info-label">ตำบล</span>
                                        <div class="kid-info-value">
                                            <?= htmlspecialchars($kid_data->guest_district ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                    <div class="kid-info-item">
                                        <span class="kid-info-label">รหัสไปรษณีย์</span>
                                        <div class="kid-info-value">
                                            <?= htmlspecialchars($kid_data->guest_zipcode ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($kid_data->kid_aw_ods_notes)): ?>
                            <div class="kid-info-section">
                                <h3 class="kid-section-title">
                                    <i class="fas fa-sticky-note"></i>
                                    หมายเหตุ
                                </h3>
                                <div class="kid-info-value" style="min-height: auto;">
                                    <?= nl2br(htmlspecialchars($kid_data->kid_aw_ods_notes)) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ===== FILES SECTION ===== -->
                <?php if (!empty($kid_data->files) && is_array($kid_data->files)): ?>
                    <div class="kid-main-card">
                        <div class="kid-card-header">
                            <h2 class="kid-card-title">
                                <i class="fas fa-paperclip"></i>
                                ไฟล์แนบ (<?= count($kid_data->files) ?> ไฟล์)
                            </h2>
                        </div>
                        <div class="kid-card-body">
                            <div class="kid-files-grid">
                                <?php foreach ($kid_data->files as $file): ?>
                                    <?php 
                                    $file_extension = strtolower(pathinfo($file->kid_aw_ods_file_original_name, PATHINFO_EXTENSION));
                                    $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $file_url = site_url('uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name);
                                    ?>
                                    
                                    <div class="kid-file-card" 
                                         onclick="<?= $is_image ? "showImagePreview('$file_url', '" . htmlspecialchars($file->kid_aw_ods_file_original_name, ENT_QUOTES) . "')" : "downloadFile('$file_url', '" . htmlspecialchars($file->kid_aw_ods_file_original_name, ENT_QUOTES) . "')" ?>">
                                        
                                        <?php if ($is_image): ?>
                                            <img src="<?= $file_url ?>" 
                                                 alt="<?= htmlspecialchars($file->kid_aw_ods_file_original_name) ?>" 
                                                 class="kid-file-image"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div class="kid-file-icon">
                                                <?php 
                                                switch($file_extension) {
                                                    case 'pdf': echo '<i class="fas fa-file-pdf" style="color: #e74c3c;"></i>'; break;
                                                    case 'doc':
                                                    case 'docx': echo '<i class="fas fa-file-word" style="color: #3498db;"></i>'; break;
                                                    case 'xls':
                                                    case 'xlsx': echo '<i class="fas fa-file-excel" style="color: #27ae60;"></i>'; break;
                                                    case 'txt': echo '<i class="fas fa-file-alt" style="color: #95a5a6;"></i>'; break;
                                                    default: echo '<i class="fas fa-file" style="color: #4fc3f7;"></i>'; break;
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="kid-file-name">
                                            <?= htmlspecialchars($file->kid_aw_ods_file_original_name) ?>
                                        </div>
                                        <div class="kid-file-meta">
                                            <?= number_format($file->kid_aw_ods_file_size / 1024, 1) ?> KB
                                            | <?= date('d/m/Y H:i', strtotime($file->kid_aw_ods_file_uploaded_at)) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ===== HISTORY SECTION ===== -->
                <?php if (!empty($kid_data->history) && is_array($kid_data->history)): ?>
                    <div class="kid-main-card">
                        <div class="kid-card-header">
                            <h2 class="kid-card-title">
                                <i class="fas fa-history"></i>
                                ประวัติการดำเนินการ
                            </h2>
                        </div>
                        <div class="kid-card-body">
                            <div class="kid-history-timeline">
                                <?php foreach ($kid_data->history as $history): ?>
                                    <div class="kid-history-item">
                                        <div class="kid-history-header">
                                            <div class="kid-history-action">
                                                <?= htmlspecialchars($history->action_description) ?>
                                            </div>
                                            <div class="kid-history-date">
                                                <?php
                                                $history_date = date('j', strtotime($history->action_date));
                                                $history_month = $thai_months[date('m', strtotime($history->action_date))];
                                                $history_year = date('Y', strtotime($history->action_date)) + 543;
                                                $history_time = date('H:i', strtotime($history->action_date));
                                                echo $history_date . ' ' . $history_month . ' ' . $history_year . ' เวลา ' . $history_time . ' น.';
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($history->old_status) && !empty($history->new_status)): ?>
                                            <div class="kid-history-description">
                                                เปลี่ยนสถานะจาก 
                                                <span class="kid-status-badge <?= get_kid_aw_ods_status_class($history->old_status) ?>" style="display: inline-block; margin: 0 0.5rem;">
                                                    <?= get_kid_aw_ods_status_display($history->old_status) ?>
                                                </span>
                                                เป็น
                                                <span class="kid-status-badge <?= get_kid_aw_ods_status_class($history->new_status) ?>" style="display: inline-block; margin: 0 0.5rem;">
                                                    <?= get_kid_aw_ods_status_display($history->new_status) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="kid-history-by">
                                            โดย: <?= htmlspecialchars($history->action_by) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ===== SIDEBAR ===== -->
            <div class="kid-sidebar">
                <!-- ===== QUICK INFO CARD ===== -->
                <div class="kid-sidebar-card">
                    <div class="kid-sidebar-header">
                        <i class="fas fa-info-circle"></i>
                        ข้อมูลสรุป
                    </div>
                    <div class="kid-sidebar-body">
                        <div class="kid-quick-info">
                            <div class="kid-quick-item">
                                <span class="kid-quick-label">สถานะปัจจุบัน</span>
                                <span class="kid-status-badge <?= get_kid_aw_ods_status_class($kid_data->kid_aw_ods_status) ?>">
                                    <?= get_kid_aw_ods_status_display($kid_data->kid_aw_ods_status) ?>
                                </span>
                            </div>
                            <div class="kid-quick-item">
                                <span class="kid-quick-label">ประเภท</span>
                                <span class="kid-type-badge <?= $kid_data->kid_aw_ods_type ?? 'children' ?>">
                                    <?= get_kid_aw_ods_type_display($kid_data->kid_aw_ods_type ?? 'children') ?>
                                </span>
                            </div>
                            <div class="kid-quick-item">
                                <span class="kid-quick-label">ความสำคัญ</span>
                                <span class="kid-priority-badge <?= $kid_data->kid_aw_ods_priority ?? 'normal' ?>">
                                    <?= get_kid_aw_ods_priority_display($kid_data->kid_aw_ods_priority ?? 'normal') ?>
                                </span>
                            </div>
                            <div class="kid-quick-item">
                                <span class="kid-quick-label">ประเภทผู้ใช้</span>
                                <span class="kid-user-type-badge <?= $kid_data->kid_aw_ods_user_type ?? 'guest' ?>">
                                    <?= get_kid_aw_ods_user_type_display($kid_data->kid_aw_ods_user_type ?? 'guest') ?>
                                </span>
                            </div>
                            <div class="kid-quick-item">
                                <span class="kid-quick-label">วันที่ยื่นเรื่อง</span>
                                <span class="kid-quick-value">
                                    <?php
                                    $date = date('j', strtotime($kid_data->kid_aw_ods_datesave));
                                    $month = $thai_months[date('m', strtotime($kid_data->kid_aw_ods_datesave))];
                                    $year = date('Y', strtotime($kid_data->kid_aw_ods_datesave)) + 543;
                                    echo $date . ' ' . $month . ' ' . $year;
                                    ?>
                                </span>
                            </div>
                            <?php if (!empty($kid_data->kid_aw_ods_updated_at)): ?>
                                <div class="kid-quick-item">
                                    <span class="kid-quick-label">อัปเดตล่าสุด</span>
                                    <span class="kid-quick-value">
                                        <?php
                                        $update_date = date('j', strtotime($kid_data->kid_aw_ods_updated_at));
                                        $update_month = $thai_months[date('m', strtotime($kid_data->kid_aw_ods_updated_at))];
                                        $update_year = date('Y', strtotime($kid_data->kid_aw_ods_updated_at)) + 543;
                                        echo $update_date . ' ' . $update_month . ' ' . $update_year;
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($kid_data->assigned_staff_name)): ?>
                                <div class="kid-quick-item">
                                    <span class="kid-quick-label">เจ้าหน้าที่ผู้รับผิดชอบ</span>
                                    <span class="kid-quick-value">
                                        <?= htmlspecialchars($kid_data->assigned_staff_name) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ===== CONTACT CARD ===== -->
                <div class="kid-sidebar-card">
                    <div class="kid-sidebar-header">
                        <i class="fas fa-phone"></i>
                        ข้อมูลติดต่อ
                    </div>
                    <div class="kid-sidebar-body">
                        <div class="kid-quick-info">
                            <div class="kid-quick-item">
                                <span class="kid-quick-label">เบอร์โทรศัพท์</span>
                                <span class="kid-quick-value">
                                    <a href="tel:<?= htmlspecialchars($kid_data->kid_aw_ods_phone) ?>" 
                                       style="color: var(--kid-primary-color); text-decoration: none;">
                                        <?= htmlspecialchars($kid_data->kid_aw_ods_phone) ?>
                                    </a>
                                </span>
                            </div>
                            <?php if (!empty($kid_data->kid_aw_ods_email)): ?>
                                <div class="kid-quick-item">
                                    <span class="kid-quick-label">อีเมล</span>
                                    <span class="kid-quick-value">
                                        <a href="mailto:<?= htmlspecialchars($kid_data->kid_aw_ods_email) ?>" 
                                           style="color: var(--kid-primary-color); text-decoration: none;">
                                            <?= htmlspecialchars($kid_data->kid_aw_ods_email) ?>
                                        </a>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
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
// *** CONFIGURATION & VARIABLES ***
// ===================================================================

const KidDetailConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Kid_aw_ods/update_kid_status") ?>',
    deleteUrl: '<?= site_url("Kid_aw_ods/delete_kid") ?>',
    editUrl: '<?= site_url("Kid_aw_ods/edit_kid") ?>',
    addNoteUrl: '<?= site_url("Kid_aw_ods/add_note") ?>',
    kidId: '<?= $kid_data->kid_aw_ods_id ?>',
    debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
};

const statusDisplayMap = {
    'submitted': 'ยื่นเรื่องแล้ว',
    'reviewing': 'กำลังพิจารณา',
    'approved': 'อนุมัติแล้ว',
    'rejected': 'ไม่อนุมัติ',
    'completed': 'เสร็จสิ้น'
};

// ===================================================================
// *** CORE FUNCTIONS ***
// ===================================================================

/**
 * อัปเดตสถานะเงินสนับสนุนเด็ก
 */
function updateKidStatus(kidId, newStatus) {
    console.log('Updating kid status:', kidId, newStatus);
    
    const statusText = statusDisplayMap[newStatus] || newStatus;
    
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        text: `คุณต้องการเปลี่ยนสถานะเป็น "${statusText}" หรือไม่?`,
        icon: 'question',
        input: 'textarea',
        inputLabel: 'หมายเหตุ (ไม่บังคับ)',
        inputPlaceholder: 'ระบุหมายเหตุเพิ่มเติม...',
        showCancelButton: true,
        confirmButtonColor: '#4fc3f7',
        cancelButtonColor: '#ef5350',
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const note = result.value || '';
            performUpdateKidStatus(kidId, newStatus, note);
        }
    });
}

/**
 * ดำเนินการอัปเดตสถานะ
 */
function performUpdateKidStatus(kidId, newStatus, note) {
    // แสดง loading
    Swal.fire({
        title: 'กำลังบันทึก...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('kid_id', kidId);
    formData.append('new_status', newStatus);
    formData.append('note', note);
    
    fetch(KidDetailConfig.updateStatusUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Update status response:', data);
        
        if (data.success) {
            Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: data.message || 'อัปเดตสถานะเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการบันทึก');
        }
    })
    .catch(error => {
        console.error('Update status error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * เพิ่มหมายเหตุ
 */
function addKidNote(kidId) {
    console.log('Adding note for kid:', kidId);
    
    Swal.fire({
        title: 'เพิ่มหมายเหตุ',
        input: 'textarea',
        inputLabel: 'หมายเหตุ',
        inputPlaceholder: 'กรอกหมายเหตุ...',
        inputAttributes: {
            'aria-label': 'กรอกหมายเหตุ'
        },
        showCancelButton: true,
        confirmButtonColor: '#4fc3f7',
        cancelButtonColor: '#9e9e9e',
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก',
        inputValidator: (value) => {
            if (!value || value.trim() === '') {
                return 'กรุณากรอกหมายเหตุ';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const note = result.value.trim();
            performAddKidNote(kidId, note);
        }
    });
}

/**
 * ดำเนินการเพิ่มหมายเหตุ
 */
function performAddKidNote(kidId, note) {
    // แสดง loading
    Swal.fire({
        title: 'กำลังบันทึก...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('kid_id', kidId);
    formData.append('note', note);
    
    fetch(KidDetailConfig.addNoteUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Add note response:', data);
        
        if (data.success) {
            Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: data.message || 'เพิ่มหมายเหตุเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการบันทึก');
        }
    })
    .catch(error => {
        console.error('Add note error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * ยืนยันการลบข้อมูลเงินสนับสนุนเด็ก
 */
function confirmDeleteKid(kidId, kidBy) {
    console.log('Confirming delete kid:', kidId, kidBy);
    
    if (!kidId) {
        showErrorAlert('ไม่พบหมายเลขข้อมูลเงินสนับสนุนเด็ก');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการลบ',
        html: `
            <div style="text-align: left; padding: 1rem;">
                <div style="background: #ffebee; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #f44336;">
                    <strong style="color: #d32f2f;">⚠️ คำเตือน:</strong> การดำเนินการนี้ไม่สามารถยกเลิกได้!
                </div>
                <p><strong>หมายเลข:</strong> #${kidId}</p>
                <p><strong>ผู้ยื่นเรื่อง:</strong> ${kidBy}</p>
                <div style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">เหตุผลในการลบ:</label>
                    <textarea id="deleteReason" style="width: 100%; height: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" placeholder="ระบุเหตุผลในการลบข้อมูลนี้..."></textarea>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef5350',
        cancelButtonColor: '#9e9e9e',
        confirmButtonText: 'ลบข้อมูล',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'swal2-popup-large'
        },
        preConfirm: () => {
            const deleteReason = document.getElementById('deleteReason').value.trim();
            return deleteReason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const deleteReason = result.value || '';
            performDeleteKid(kidId, deleteReason);
        }
    });
}

/**
 * ดำเนินการลบข้อมูลเงินสนับสนุนเด็ก
 */
function performDeleteKid(kidId, deleteReason) {
    console.log('Performing delete kid:', kidId, deleteReason);
    
    // แสดง loading
    Swal.fire({
        title: 'กำลังลบ...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('kid_id', kidId);
    if (deleteReason) {
        formData.append('delete_reason', deleteReason);
    }
    
    fetch(KidDetailConfig.deleteUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Delete response:', data);
        
        if (data.success) {
            Swal.fire({
                title: 'ลบสำเร็จ!',
                text: data.message || 'ลบข้อมูลเงินสนับสนุนเด็กเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = KidDetailConfig.baseUrl + 'Kid_aw_ods/kid_aw_ods';
            });
        } else {
            showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการลบ');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * แสดงตัวอย่างรูปภาพ
 */
function showImagePreview(imageUrl, fileName) {
    console.log('Opening image preview:', imageUrl, fileName);
    
    const modalId = 'imagePreviewModal_' + Date.now();
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = modalId;
    modal.tabIndex = -1;
    modal.innerHTML = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${fileName}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${imageUrl}" class="img-fluid" alt="${fileName}" style="max-height: 70vh; border-radius: 8px;">
                </div>
                <div class="modal-footer">
                    <a href="${imageUrl}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i>เปิดในแท็บใหม่
                    </a>
                    <a href="${imageUrl}" download="${fileName}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>ดาวน์โหลด
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

/**
 * ดาวน์โหลดไฟล์
 */
function downloadFile(fileUrl, fileName) {
    console.log('Downloading file:', fileUrl, fileName);
    
    try {
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = fileName;
        link.target = '_blank';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        Swal.fire({
            title: 'กำลังดาวน์โหลด',
            text: `กำลังดาวน์โหลดไฟล์ "${fileName}"`,
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
        
    } catch (error) {
        console.error('Download error:', error);
        
        Swal.fire({
            title: 'ไม่สามารถดาวน์โหลดได้',
            text: 'จะเปิดไฟล์ในแท็บใหม่แทน',
            icon: 'warning',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.open(fileUrl, '_blank');
        });
    }
}

/**
 * พิมพ์หน้า
 */
function printPage() {
    console.log('Printing page...');
    
    // ซ่อนปุ่มการดำเนินการก่อนพิมพ์
    const actionSections = document.querySelectorAll('.kid-action-section, .kid-detail-actions');
    actionSections.forEach(section => {
        section.style.display = 'none';
    });
    
    // พิมพ์
    window.print();
    
    // แสดงปุ่มกลับคืนหลังพิมพ์
    setTimeout(() => {
        actionSections.forEach(section => {
            section.style.display = '';
        });
    }, 1000);
}

/**
 * แสดง Error Alert
 */
function showErrorAlert(message) {
    Swal.fire({
        title: 'เกิดข้อผิดพลาด',
        text: message,
        icon: 'error',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#4fc3f7'
    });
}

/**
 * แสดง Success Alert
 */
function showSuccessAlert(message) {
    Swal.fire({
        title: 'สำเร็จ!',
        text: message,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
}

// ===================================================================
// *** EVENT HANDLERS ***
// ===================================================================

/**
 * จัดการ Keyboard Shortcuts
 */
function handleKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P = Print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            if (typeof printPage === 'function') {
                printPage();
            }
        }
        
        // Escape = กลับไปรายการ
        if (e.key === 'Escape') {
            window.location.href = KidDetailConfig.baseUrl + 'Kid_aw_ods/kid_aw_ods';
        }
    });
}

/**
 * จัดการ Responsive Images
 */
function handleResponsiveImages() {
    const images = document.querySelectorAll('.kid-file-image');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const parent = this.closest('.kid-file-card');
            if (parent) {
                const icon = document.createElement('div');
                icon.className = 'kid-file-icon';
                icon.innerHTML = '<i class="fas fa-image" style="color: #ef5350;"></i>';
                parent.insertBefore(icon, this);
            }
        });
    });
}

/**
 * จัดการ Auto Refresh (สำหรับสถานะที่เปลี่ยนแปลงบ่อย)
 */
function handleAutoRefresh() {
    // Auto refresh ทุก 5 นาที หากสถานะเป็น reviewing
    const currentStatus = '<?= $kid_data->kid_aw_ods_status ?>';
    
    if (currentStatus === 'reviewing' || currentStatus === 'submitted') {
        setInterval(() => {
            // Check for updates without full page reload
            checkForUpdates();
        }, 300000); // 5 minutes
    }
}

/**
 * ตรวจสอบการอัปเดตข้อมูล
 */
function checkForUpdates() {
    fetch(KidDetailConfig.baseUrl + 'Kid_aw_ods/check_status/' + KidDetailConfig.kidId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.updated) {
            // แสดงการแจ้งเตือนว่ามีการอัปเดต
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            toast.fire({
                icon: 'info',
                title: 'มีการอัปเดตข้อมูลใหม่',
                text: 'คลิกเพื่อรีเฟรชหน้า'
            }).then(() => {
                location.reload();
            });
        }
    })
    .catch(error => {
        console.log('Auto refresh check failed:', error);
    });
}

// ===================================================================
// *** DOCUMENT READY & INITIALIZATION ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Kid AW ODS Detail Page loading...');
    
    try {
        // Initialize functionality
        handleKeyboardShortcuts();
        handleResponsiveImages();
        handleAutoRefresh();
        
        console.log('✅ Kid AW ODS Detail Page initialized successfully');
        
        if (KidDetailConfig.debug) {
            console.log('🔧 Debug mode enabled');
            console.log('⚙️ Configuration:', KidDetailConfig);
            console.log('📄 Kid Data:', {
                id: KidDetailConfig.kidId,
                status: '<?= $kid_data->kid_aw_ods_status ?>',
                type: '<?= $kid_data->kid_aw_ods_type ?? "children" ?>',
                by: '<?= htmlspecialchars($kid_data->kid_aw_ods_by, ENT_QUOTES) ?>'
            });
        }
        
    } catch (error) {
        console.error('❌ Initialization error:', error);
        showErrorAlert('เกิดข้อผิดพลาดในการโหลดหน้า กรุณารีเฟรชหน้า');
    }
});

// ===================================================================
// *** FLASH MESSAGES ***
// ===================================================================

// Success message
<?php if (isset($success_message) && !empty($success_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    showSuccessAlert(<?= json_encode($success_message, JSON_UNESCAPED_UNICODE) ?>);
});
<?php endif; ?>

// Error message
<?php if (isset($error_message) && !empty($error_message)): ?>
document.addEventListener('DOMContentLoaded', function() {
    showErrorAlert(<?= json_encode($error_message, JSON_UNESCAPED_UNICODE) ?>);
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
        showConfirmButton: false,
        confirmButtonColor: '#4fc3f7'
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
        showConfirmButton: false,
        confirmButtonColor: '#4fc3f7'
    });
});
<?php endif; ?>

// ===================================================================
// *** CUSTOM STYLES FOR SWAL ===== 
// ===================================================================

const style = document.createElement('style');
style.textContent = `
    .swal2-popup-large {
        width: 600px !important;
        max-width: 90vw !important;
    }
    
    .swal2-popup {
        border-radius: 12px !important;
    }
    
    .swal2-title {
        color: var(--kid-gray-900) !important;
    }
    
    .swal2-confirm {
        background-color: #4fc3f7 !important;
        border-radius: 8px !important;
    }
    
    .swal2-cancel {
        background-color: #ef5350 !important;
        border-radius: 8px !important;
    }
    
    @media print {
        .kid-detail-actions,
        .kid-action-section {
            display: none !important;
        }
        
        .kid-detail-page {
            background: white !important;
            -webkit-print-color-adjust: exact !important;
        }
        
        .kid-main-card,
        .kid-sidebar-card {
            break-inside: avoid !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        
        .kid-detail-header {
            background: #f5f5f5 !important;
            color: #333 !important;
        }
    }
`;
document.head.appendChild(style);

//console.log("💼 Kid AW ODS Detail System loaded successfully");
//console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
//console.log("📊 Detail Status: Ready");
//console.log("📄 Kid ID: " + KidDetailConfig.kidId);

	</script>