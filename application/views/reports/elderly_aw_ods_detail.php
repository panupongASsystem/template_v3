<?php
// ===================================================================
// elderly_aw_ods_detail.php - หน้ารายละเอียดเบี้ยยังชีพผู้สูงอายุ/ผู้พิการ
// ===================================================================

// Helper function สำหรับ CSS class ของสถานะเบี้ยยังชีพ
if (!function_exists('get_elderly_aw_ods_status_class')) {
    function get_elderly_aw_ods_status_class($status) {
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
if (!function_exists('get_elderly_aw_ods_status_display')) {
    function get_elderly_aw_ods_status_display($status) {
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

// Helper function สำหรับแสดงประเภทเบี้ยยังชีพ
if (!function_exists('get_elderly_aw_ods_type_display')) {
    function get_elderly_aw_ods_type_display($type) {
        switch($type) {
            case 'elderly': return 'ผู้สูงอายุ';
            case 'disabled': return 'ผู้พิการ';
            default: return 'ผู้สูงอายุ';
        }
    }
}

// Helper function สำหรับความสำคัญ
if (!function_exists('get_elderly_aw_ods_priority_display')) {
    function get_elderly_aw_ods_priority_display($priority) {
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
if (!function_exists('get_elderly_aw_ods_user_type_display')) {
    function get_elderly_aw_ods_user_type_display($user_type) {
        switch($user_type) {
            case 'guest': return 'ผู้ใช้ทั่วไป';
            case 'public': return 'สมาชิก';
            case 'staff': return 'เจ้าหน้าที่';
            default: return 'ผู้ใช้ทั่วไป';
        }
    }
}

// ตรวจสอบข้อมูลเบี้ยยังชีพ
$elderly_data = $elderly_aw_ods_detail ?? null;
if (!$elderly_data) {
    show_404();
    return;
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ===== ELDERLY AW ODS DETAIL PAGE STYLES ===== */
.elderly-detail-page {
    --elderly-primary-color: #7db3e8;
    --elderly-primary-light: #a5c9ea;
    --elderly-secondary-color: #f0f8ff;
    --elderly-success-color: #81c784;
    --elderly-warning-color: #ffb74d;
    --elderly-danger-color: #e57373;
    --elderly-info-color: #64b5f6;
    --elderly-purple-color: #ba68c8;
    --elderly-light-bg: #fafbfc;
    --elderly-white: #ffffff;
    --elderly-gray-50: #fafafa;
    --elderly-gray-100: #f5f5f5;
    --elderly-gray-200: #eeeeee;
    --elderly-gray-300: #e0e0e0;
    --elderly-gray-400: #bdbdbd;
    --elderly-gray-500: #9e9e9e;
    --elderly-gray-600: #757575;
    --elderly-gray-700: #616161;
    --elderly-gray-800: #424242;
    --elderly-gray-900: #212121;
    --elderly-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.03);
    --elderly-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04);
    --elderly-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04);
    --elderly-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.06), 0 4px 6px -4px rgb(0 0 0 / 0.04);
    --elderly-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.06), 0 8px 10px -6px rgb(0 0 0 / 0.04);
    --elderly-border-radius: 12px;
    --elderly-border-radius-lg: 16px;
    --elderly-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.elderly-detail-page {
    background: linear-gradient(135deg, #f8fbff 0%, #fcf7ff 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--elderly-gray-700);
    min-height: 100vh;
    padding: 2rem 0;
}

.elderly-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* ===== PAGE HEADER ===== */
.elderly-detail-header {
    background: linear-gradient(135deg, rgba(125, 179, 232, 0.9) 0%, rgba(165, 201, 234, 0.7) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--elderly-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--elderly-shadow-lg);
    position: relative;
    overflow: hidden;
}

.elderly-detail-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.elderly-detail-header h1 {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
}

.elderly-detail-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
    position: relative;
    z-index: 1;
}

.elderly-detail-actions {
    position: absolute;
    top: 2rem;
    right: 2rem;
    display: flex;
    gap: 1rem;
    z-index: 2;
}

.elderly-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--elderly-transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    cursor: pointer;
}

.elderly-btn-white {
    background: rgba(255, 255, 255, 0.9);
    color: var(--elderly-primary-color);
    backdrop-filter: blur(10px);
}

.elderly-btn-white:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: var(--elderly-shadow-lg);
    color: var(--elderly-primary-color);
}

/* ===== CONTENT GRID ===== */
.elderly-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

/* ===== MAIN CONTENT CARD ===== */
.elderly-main-card {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    box-shadow: var(--elderly-shadow-md);
    overflow: hidden;
    border: 1px solid var(--elderly-gray-100);
}

.elderly-card-header {
    background: linear-gradient(135deg, var(--elderly-gray-50) 0%, var(--elderly-gray-100) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--elderly-gray-200);
    display: flex;
    align-items: center;
    justify-content: between;
}

.elderly-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--elderly-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.elderly-status-badge {
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

.elderly-status-badge.submitted {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border: 1px solid rgba(255, 152, 0, 0.3);
}

.elderly-status-badge.reviewing {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.elderly-status-badge.approved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.elderly-status-badge.rejected {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

.elderly-status-badge.completed {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border: 1px solid rgba(156, 39, 176, 0.3);
}

.elderly-card-body {
    padding: 2rem;
}

/* ===== INFO SECTIONS ===== */
.elderly-info-section {
    margin-bottom: 2rem;
}

.elderly-info-section:last-child {
    margin-bottom: 0;
}

.elderly-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--elderly-gray-900);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--elderly-gray-100);
}

.elderly-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.elderly-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.elderly-info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--elderly-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.elderly-info-value {
    font-size: 1rem;
    color: var(--elderly-gray-900);
    padding: 0.75rem 1rem;
    background: var(--elderly-gray-50);
    border-radius: 8px;
    border: 1px solid var(--elderly-gray-200);
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

.elderly-info-value.highlight {
    background: linear-gradient(135deg, var(--elderly-secondary-color), #e3f2fd);
    border-color: var(--elderly-primary-light);
    color: var(--elderly-primary-color);
    font-weight: 600;
}

/* ===== SIDEBAR CARDS ===== */
.elderly-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.elderly-sidebar-card {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    box-shadow: var(--elderly-shadow-md);
    overflow: hidden;
    border: 1px solid var(--elderly-gray-100);
}

.elderly-sidebar-header {
    background: linear-gradient(135deg, var(--elderly-primary-color), var(--elderly-primary-light));
    color: white;
    padding: 1rem 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.elderly-sidebar-body {
    padding: 1.5rem;
}

/* ===== QUICK INFO CARD ===== */
.elderly-quick-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.elderly-quick-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: var(--elderly-gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--elderly-primary-color);
}

.elderly-quick-label {
    font-size: 0.875rem;
    color: var(--elderly-gray-600);
    font-weight: 500;
}

.elderly-quick-value {
    font-weight: 600;
    color: var(--elderly-gray-900);
}

/* ===== FILES SECTION ===== */
.elderly-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.elderly-file-card {
    background: var(--elderly-gray-50);
    border: 2px solid var(--elderly-gray-200);
    border-radius: var(--elderly-border-radius);
    padding: 1.5rem;
    text-align: center;
    transition: var(--elderly-transition);
    cursor: pointer;
}

.elderly-file-card:hover {
    border-color: var(--elderly-primary-color);
    background: var(--elderly-secondary-color);
    transform: translateY(-2px);
    box-shadow: var(--elderly-shadow-md);
}

.elderly-file-icon {
    font-size: 2.5rem;
    color: var(--elderly-primary-color);
    margin-bottom: 1rem;
}

.elderly-file-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--elderly-gray-900);
    margin-bottom: 0.5rem;
    word-break: break-word;
}

.elderly-file-meta {
    font-size: 0.75rem;
    color: var(--elderly-gray-600);
}

.elderly-file-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
}

/* ===== HISTORY SECTION ===== */
.elderly-history-timeline {
    position: relative;
    padding-left: 2rem;
}

.elderly-history-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--elderly-gray-200);
}

.elderly-history-item {
    position: relative;
    margin-bottom: 1.5rem;
    background: var(--elderly-white);
    border: 1px solid var(--elderly-gray-200);
    border-radius: var(--elderly-border-radius);
    padding: 1.5rem;
    box-shadow: var(--elderly-shadow-sm);
}

.elderly-history-item::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 1.5rem;
    width: 12px;
    height: 12px;
    background: var(--elderly-primary-color);
    border-radius: 50%;
    border: 3px solid var(--elderly-white);
    box-shadow: 0 0 0 2px var(--elderly-primary-color);
}

.elderly-history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.elderly-history-action {
    font-weight: 600;
    color: var(--elderly-gray-900);
}

.elderly-history-date {
    font-size: 0.875rem;
    color: var(--elderly-gray-600);
}

.elderly-history-description {
    color: var(--elderly-gray-700);
    line-height: 1.5;
}

.elderly-history-by {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--elderly-gray-600);
    font-style: italic;
}

/* ===== ACTION BUTTONS ===== */
.elderly-action-section {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    box-shadow: var(--elderly-shadow-md);
    border: 1px solid var(--elderly-gray-100);
    margin-top: 2rem;
}

.elderly-action-header {
    background: linear-gradient(135deg, var(--elderly-gray-50) 0%, var(--elderly-gray-100) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--elderly-gray-200);
}

.elderly-action-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--elderly-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.elderly-action-body {
    padding: 2rem;
}

.elderly-action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.elderly-btn-action {
    padding: 1rem 1.5rem;
    border-radius: var(--elderly-border-radius);
    border: 2px solid transparent;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--elderly-transition);
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    min-height: 3rem;
}

.elderly-btn-action.primary {
    background: linear-gradient(135deg, var(--elderly-primary-color), var(--elderly-primary-light));
    color: white;
}

.elderly-btn-action.primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--elderly-shadow-lg);
    color: white;
}

.elderly-btn-action.success {
    background: linear-gradient(135deg, var(--elderly-success-color), #81c784);
    color: white;
}

.elderly-btn-action.success:hover {
    transform: translateY(-2px);
    box-shadow: var(--elderly-shadow-lg);
    color: white;
}

.elderly-btn-action.warning {
    background: linear-gradient(135deg, var(--elderly-warning-color), #ffb74d);
    color: white;
}

.elderly-btn-action.warning:hover {
    transform: translateY(-2px);
    box-shadow: var(--elderly-shadow-lg);
    color: white;
}

.elderly-btn-action.danger {
    background: linear-gradient(135deg, var(--elderly-danger-color), #ef5350);
    color: white;
}

.elderly-btn-action.danger:hover {
    transform: translateY(-2px);
    box-shadow: var(--elderly-shadow-lg);
    color: white;
}

.elderly-btn-action.secondary {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-700);
    border-color: var(--elderly-gray-300);
}

.elderly-btn-action.secondary:hover {
    background: var(--elderly-gray-200);
    color: var(--elderly-gray-800);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .elderly-detail-page {
        padding: 1rem 0;
    }
    
    .elderly-detail-container {
        padding: 0 0.5rem;
    }
    
    .elderly-detail-header {
        padding: 1.5rem;
        text-align: center;
    }
    
    .elderly-detail-header h1 {
        font-size: 1.8rem;
    }
    
    .elderly-detail-actions {
        position: static;
        margin-top: 1rem;
        justify-content: center;
    }
    
    .elderly-detail-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .elderly-card-body {
        padding: 1.5rem;
    }
    
    .elderly-info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .elderly-files-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .elderly-action-grid {
        grid-template-columns: 1fr;
    }
    
    .elderly-history-timeline {
        padding-left: 1.5rem;
    }
    
    .elderly-history-item::before {
        left: -1.5rem;
    }
}

@media (max-width: 480px) {
    .elderly-detail-header h1 {
        font-size: 1.5rem;
    }
    
    .elderly-btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
    
    .elderly-card-header {
        padding: 1rem 1.5rem;
    }
    
    .elderly-card-body {
        padding: 1rem;
    }
    
    .elderly-sidebar-body {
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

.elderly-main-card,
.elderly-sidebar-card,
.elderly-action-section {
    animation: fadeInUp 0.5s ease-out;
}

/* ===== LOADING STATES ===== */
.elderly-loading {
    opacity: 0.6;
    pointer-events: none;
}

.elderly-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--elderly-gray-300);
    border-top: 2px solid var(--elderly-primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ===== OTHER BADGES ===== */
.elderly-priority-badge, 
.elderly-type-badge, 
.elderly-user-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.elderly-priority-badge.low {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.elderly-priority-badge.normal {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-700);
}

.elderly-priority-badge.high {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.elderly-priority-badge.urgent {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.elderly-type-badge.elderly {
    background: linear-gradient(135deg, var(--elderly-secondary-color), #c1e7ff);
    color: var(--elderly-primary-color);
}

.elderly-type-badge.disabled {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    color: #6a1b9a;
}

.elderly-user-type-badge.guest {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-600);
}

.elderly-user-type-badge.public {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.elderly-user-type-badge.staff {
    background: linear-gradient(135deg, var(--elderly-secondary-color), #c1e7ff);
    color: var(--elderly-primary-color);
}
</style>

<div class="elderly-detail-page">
    <div class="elderly-detail-container">
        <!-- ===== PAGE HEADER ===== -->
        <header class="elderly-detail-header">
            <div class="elderly-detail-actions">
                <a href="<?= site_url('Elderly_aw_ods/elderly_aw_ods') ?>" class="elderly-btn elderly-btn-white">
                    <i class="fas fa-arrow-left"></i>
                    กลับไปรายการ
                </a>
                <?php if (isset($can_handle_elderly) && $can_handle_elderly): ?>
                    <button class="elderly-btn elderly-btn-white" onclick="printPage()">
                        <i class="fas fa-print"></i>
                        พิมพ์
                    </button>
                <?php endif; ?>
            </div>
            
            <h1>
                <i class="fas fa-user-clock me-3"></i>
                รายละเอียดเบี้ยยังชีพ #<?= htmlspecialchars($elderly_data->elderly_aw_ods_id) ?>
            </h1>
            <p class="elderly-detail-subtitle">
                ประเภท: <?= get_elderly_aw_ods_type_display($elderly_data->elderly_aw_ods_type ?? 'elderly') ?>
                | ยื่นโดย: <?= htmlspecialchars($elderly_data->elderly_aw_ods_by) ?>
                | วันที่: <?php
                    $thai_months = [
                        '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
                        '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
                        '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                    ];
                    
                    $date = date('j', strtotime($elderly_data->elderly_aw_ods_datesave));
                    $month = $thai_months[date('m', strtotime($elderly_data->elderly_aw_ods_datesave))];
                    $year = date('Y', strtotime($elderly_data->elderly_aw_ods_datesave)) + 543;
                    
                    echo $date . ' ' . $month . ' ' . $year;
                ?>
            </p>
        </header>

        <!-- ===== MAIN CONTENT GRID ===== -->
        <div class="elderly-detail-grid">
            <!-- ===== MAIN CONTENT ===== -->
            <div class="elderly-main-content">
                <!-- ===== PERSONAL INFORMATION CARD ===== -->
                <div class="elderly-main-card">
                    <div class="elderly-card-header">
                        <h2 class="elderly-card-title">
                            <i class="fas fa-user"></i>
                            ข้อมูลส่วนบุคคล
                        </h2>
                        <span class="elderly-status-badge <?= get_elderly_aw_ods_status_class($elderly_data->elderly_aw_ods_status) ?>">
                            <?= get_elderly_aw_ods_status_display($elderly_data->elderly_aw_ods_status) ?>
                        </span>
                    </div>
                    <div class="elderly-card-body">
                        <div class="elderly-info-section">
                            <h3 class="elderly-section-title">
                                <i class="fas fa-id-card"></i>
                                ข้อมูลพื้นฐาน
                            </h3>
                            <div class="elderly-info-grid">
                                <div class="elderly-info-item">
                                    <span class="elderly-info-label">หมายเลขอ้างอิง</span>
                                    <div class="elderly-info-value highlight">
                                        #<?= htmlspecialchars($elderly_data->elderly_aw_ods_id) ?>
                                    </div>
                                </div>
                                <div class="elderly-info-item">
                                    <span class="elderly-info-label">ชื่อ-นามสกุล</span>
                                    <div class="elderly-info-value">
                                        <?= htmlspecialchars($elderly_data->elderly_aw_ods_by) ?>
                                    </div>
                                </div>
                                <div class="elderly-info-item">
                                    <span class="elderly-info-label">เบอร์โทรศัพท์</span>
                                    <div class="elderly-info-value">
                                        <?= htmlspecialchars($elderly_data->elderly_aw_ods_phone) ?>
                                    </div>
                                </div>
                                <div class="elderly-info-item">
                                    <span class="elderly-info-label">อีเมล</span>
                                    <div class="elderly-info-value">
                                        <?= !empty($elderly_data->elderly_aw_ods_email) ? htmlspecialchars($elderly_data->elderly_aw_ods_email) : 'ไม่ระบุ' ?>
                                    </div>
                                </div>
                                <div class="elderly-info-item">
                                    <span class="elderly-info-label">เลขบัตรประชาชน</span>
                                    <div class="elderly-info-value">
                                        <?php if (!empty($elderly_data->elderly_aw_ods_number)): ?>
                                            <?= htmlspecialchars(substr($elderly_data->elderly_aw_ods_number, 0, 3) . '-****-****-**-' . substr($elderly_data->elderly_aw_ods_number, -2)) ?>
                                        <?php else: ?>
                                            ไม่ระบุ
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="elderly-info-item">
                                    <span class="elderly-info-label">ประเภทเบี้ยยังชีพ</span>
                                    <div class="elderly-info-value">
                                        <span class="elderly-type-badge <?= $elderly_data->elderly_aw_ods_type ?? 'elderly' ?>">
                                            <?= get_elderly_aw_ods_type_display($elderly_data->elderly_aw_ods_type ?? 'elderly') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="elderly-info-section">
                            <h3 class="elderly-section-title">
                                <i class="fas fa-map-marker-alt"></i>
                                ที่อยู่
                            </h3>
                            <div class="elderly-info-grid">
                                <div class="elderly-info-item" style="grid-column: 1 / -1;">
                                    <span class="elderly-info-label">ที่อยู่หลัก</span>
                                    <div class="elderly-info-value">
                                        <?= htmlspecialchars($elderly_data->elderly_aw_ods_address) ?>
                                    </div>
                                </div>
                                <?php if (!empty($elderly_data->guest_province) || !empty($elderly_data->guest_amphoe) || !empty($elderly_data->guest_district)): ?>
                                    <div class="elderly-info-item">
                                        <span class="elderly-info-label">จังหวัด</span>
                                        <div class="elderly-info-value">
                                            <?= htmlspecialchars($elderly_data->guest_province ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                    <div class="elderly-info-item">
                                        <span class="elderly-info-label">อำเภอ</span>
                                        <div class="elderly-info-value">
                                            <?= htmlspecialchars($elderly_data->guest_amphoe ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                    <div class="elderly-info-item">
                                        <span class="elderly-info-label">ตำบล</span>
                                        <div class="elderly-info-value">
                                            <?= htmlspecialchars($elderly_data->guest_district ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                    <div class="elderly-info-item">
                                        <span class="elderly-info-label">รหัสไปรษณีย์</span>
                                        <div class="elderly-info-value">
                                            <?= htmlspecialchars($elderly_data->guest_zipcode ?? 'ไม่ระบุ') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($elderly_data->elderly_aw_ods_notes)): ?>
                            <div class="elderly-info-section">
                                <h3 class="elderly-section-title">
                                    <i class="fas fa-sticky-note"></i>
                                    หมายเหตุ
                                </h3>
                                <div class="elderly-info-value" style="min-height: auto;">
                                    <?= nl2br(htmlspecialchars($elderly_data->elderly_aw_ods_notes)) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ===== FILES SECTION ===== -->
                <?php if (!empty($elderly_data->files) && is_array($elderly_data->files)): ?>
                    <div class="elderly-main-card">
                        <div class="elderly-card-header">
                            <h2 class="elderly-card-title">
                                <i class="fas fa-paperclip"></i>
                                ไฟล์แนบ (<?= count($elderly_data->files) ?> ไฟล์)
                            </h2>
                        </div>
                        <div class="elderly-card-body">
                            <div class="elderly-files-grid">
                                <?php foreach ($elderly_data->files as $file): ?>
                                    <?php 
                                    $file_extension = strtolower(pathinfo($file->elderly_aw_ods_file_original_name, PATHINFO_EXTENSION));
                                    $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $file_url = site_url('uploads/elderly_aw_ods/' . $file->elderly_aw_ods_file_name);
                                    ?>
                                    
                                    <div class="elderly-file-card" 
                                         onclick="<?= $is_image ? "showImagePreview('$file_url', '" . htmlspecialchars($file->elderly_aw_ods_file_original_name, ENT_QUOTES) . "')" : "downloadFile('$file_url', '" . htmlspecialchars($file->elderly_aw_ods_file_original_name, ENT_QUOTES) . "')" ?>">
                                        
                                        <?php if ($is_image): ?>
                                            <img src="<?= $file_url ?>" 
                                                 alt="<?= htmlspecialchars($file->elderly_aw_ods_file_original_name) ?>" 
                                                 class="elderly-file-image"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div class="elderly-file-icon">
                                                <?php 
                                                switch($file_extension) {
                                                    case 'pdf': echo '<i class="fas fa-file-pdf" style="color: #e74c3c;"></i>'; break;
                                                    case 'doc':
                                                    case 'docx': echo '<i class="fas fa-file-word" style="color: #3498db;"></i>'; break;
                                                    case 'xls':
                                                    case 'xlsx': echo '<i class="fas fa-file-excel" style="color: #27ae60;"></i>'; break;
                                                    case 'txt': echo '<i class="fas fa-file-alt" style="color: #95a5a6;"></i>'; break;
                                                    default: echo '<i class="fas fa-file" style="color: #7db3e8;"></i>'; break;
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="elderly-file-name">
                                            <?= htmlspecialchars($file->elderly_aw_ods_file_original_name) ?>
                                        </div>
                                        <div class="elderly-file-meta">
                                            <?= number_format($file->elderly_aw_ods_file_size / 1024, 1) ?> KB
                                            | <?= date('d/m/Y H:i', strtotime($file->elderly_aw_ods_file_uploaded_at)) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ===== HISTORY SECTION ===== -->
                <?php if (!empty($elderly_data->history) && is_array($elderly_data->history)): ?>
                    <div class="elderly-main-card">
                        <div class="elderly-card-header">
                            <h2 class="elderly-card-title">
                                <i class="fas fa-history"></i>
                                ประวัติการดำเนินการ
                            </h2>
                        </div>
                        <div class="elderly-card-body">
                            <div class="elderly-history-timeline">
                                <?php foreach ($elderly_data->history as $history): ?>
                                    <div class="elderly-history-item">
                                        <div class="elderly-history-header">
                                            <div class="elderly-history-action">
                                                <?= htmlspecialchars($history->action_description) ?>
                                            </div>
                                            <div class="elderly-history-date">
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
                                            <div class="elderly-history-description">
                                                เปลี่ยนสถานะจาก 
                                                <span class="elderly-status-badge <?= get_elderly_aw_ods_status_class($history->old_status) ?>" style="display: inline-block; margin: 0 0.5rem;">
                                                    <?= get_elderly_aw_ods_status_display($history->old_status) ?>
                                                </span>
                                                เป็น
                                                <span class="elderly-status-badge <?= get_elderly_aw_ods_status_class($history->new_status) ?>" style="display: inline-block; margin: 0 0.5rem;">
                                                    <?= get_elderly_aw_ods_status_display($history->new_status) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="elderly-history-by">
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
            <div class="elderly-sidebar">
                <!-- ===== QUICK INFO CARD ===== -->
                <div class="elderly-sidebar-card">
                    <div class="elderly-sidebar-header">
                        <i class="fas fa-info-circle"></i>
                        ข้อมูลสรุป
                    </div>
                    <div class="elderly-sidebar-body">
                        <div class="elderly-quick-info">
                            <div class="elderly-quick-item">
                                <span class="elderly-quick-label">สถานะปัจจุบัน</span>
                                <span class="elderly-status-badge <?= get_elderly_aw_ods_status_class($elderly_data->elderly_aw_ods_status) ?>">
                                    <?= get_elderly_aw_ods_status_display($elderly_data->elderly_aw_ods_status) ?>
                                </span>
                            </div>
                            <div class="elderly-quick-item">
                                <span class="elderly-quick-label">ประเภท</span>
                                <span class="elderly-type-badge <?= $elderly_data->elderly_aw_ods_type ?? 'elderly' ?>">
                                    <?= get_elderly_aw_ods_type_display($elderly_data->elderly_aw_ods_type ?? 'elderly') ?>
                                </span>
                            </div>
                            <div class="elderly-quick-item">
                                <span class="elderly-quick-label">ความสำคัญ</span>
                                <span class="elderly-priority-badge <?= $elderly_data->elderly_aw_ods_priority ?? 'normal' ?>">
                                    <?= get_elderly_aw_ods_priority_display($elderly_data->elderly_aw_ods_priority ?? 'normal') ?>
                                </span>
                            </div>
                            <div class="elderly-quick-item">
                                <span class="elderly-quick-label">ประเภทผู้ใช้</span>
                                <span class="elderly-user-type-badge <?= $elderly_data->elderly_aw_ods_user_type ?? 'guest' ?>">
                                    <?= get_elderly_aw_ods_user_type_display($elderly_data->elderly_aw_ods_user_type ?? 'guest') ?>
                                </span>
                            </div>
                            <div class="elderly-quick-item">
                                <span class="elderly-quick-label">วันที่ยื่นเรื่อง</span>
                                <span class="elderly-quick-value">
                                    <?php
                                    $date = date('j', strtotime($elderly_data->elderly_aw_ods_datesave));
                                    $month = $thai_months[date('m', strtotime($elderly_data->elderly_aw_ods_datesave))];
                                    $year = date('Y', strtotime($elderly_data->elderly_aw_ods_datesave)) + 543;
                                    echo $date . ' ' . $month . ' ' . $year;
                                    ?>
                                </span>
                            </div>
                            <?php if (!empty($elderly_data->elderly_aw_ods_updated_at)): ?>
                                <div class="elderly-quick-item">
                                    <span class="elderly-quick-label">อัปเดตล่าสุด</span>
                                    <span class="elderly-quick-value">
                                        <?php
                                        $update_date = date('j', strtotime($elderly_data->elderly_aw_ods_updated_at));
                                        $update_month = $thai_months[date('m', strtotime($elderly_data->elderly_aw_ods_updated_at))];
                                        $update_year = date('Y', strtotime($elderly_data->elderly_aw_ods_updated_at)) + 543;
                                        echo $update_date . ' ' . $update_month . ' ' . $update_year;
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($elderly_data->assigned_staff_name)): ?>
                                <div class="elderly-quick-item">
                                    <span class="elderly-quick-label">เจ้าหน้าที่ผู้รับผิดชอบ</span>
                                    <span class="elderly-quick-value">
                                        <?= htmlspecialchars($elderly_data->assigned_staff_name) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ===== CONTACT CARD ===== -->
                <div class="elderly-sidebar-card">
                    <div class="elderly-sidebar-header">
                        <i class="fas fa-phone"></i>
                        ข้อมูลติดต่อ
                    </div>
                    <div class="elderly-sidebar-body">
                        <div class="elderly-quick-info">
                            <div class="elderly-quick-item">
                                <span class="elderly-quick-label">เบอร์โทรศัพท์</span>
                                <span class="elderly-quick-value">
                                    <a href="tel:<?= htmlspecialchars($elderly_data->elderly_aw_ods_phone) ?>" 
                                       style="color: var(--elderly-primary-color); text-decoration: none;">
                                        <?= htmlspecialchars($elderly_data->elderly_aw_ods_phone) ?>
                                    </a>
                                </span>
                            </div>
                            <?php if (!empty($elderly_data->elderly_aw_ods_email)): ?>
                                <div class="elderly-quick-item">
                                    <span class="elderly-quick-label">อีเมล</span>
                                    <span class="elderly-quick-value">
                                        <a href="mailto:<?= htmlspecialchars($elderly_data->elderly_aw_ods_email) ?>" 
                                           style="color: var(--elderly-primary-color); text-decoration: none;">
                                            <?= htmlspecialchars($elderly_data->elderly_aw_ods_email) ?>
                                        </a>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ACTION SECTION (สำหรับเจ้าหน้าที่) ===== -->
        
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===================================================================
// *** CONFIGURATION & VARIABLES ***
// ===================================================================

const ElderlyDetailConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Elderly_aw_ods/update_elderly_status") ?>',
    deleteUrl: '<?= site_url("Elderly_aw_ods/delete_elderly") ?>',
    editUrl: '<?= site_url("Elderly_aw_ods/edit_elderly") ?>',
    addNoteUrl: '<?= site_url("Elderly_aw_ods/add_note") ?>',
    elderlyId: '<?= $elderly_data->elderly_aw_ods_id ?>',
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
 * อัปเดตสถานะเบี้ยยังชีพ
 */
function updateStatus(elderlyId, newStatus) {
    console.log('Updating status:', elderlyId, newStatus);
    
    const statusText = statusDisplayMap[newStatus] || newStatus;
    
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        text: `คุณต้องการเปลี่ยนสถานะเป็น "${statusText}" หรือไม่?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7db3e8',
        cancelButtonColor: '#e57373',
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
            performAddNote(elderlyId, note);
        }
    });
}

/**
 * ดำเนินการเพิ่มหมายเหตุ
 */
function performAddNote(elderlyId, note) {
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
    formData.append('elderly_id', elderlyId);
    formData.append('note', note);
    
    fetch(ElderlyDetailConfig.addNoteUrl, {
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
 * ยืนยันการลบข้อมูลเบี้ยยังชีพ
 */
function confirmDeleteElderly(elderlyId, elderlyBy) {
    console.log('Confirming delete elderly:', elderlyId, elderlyBy);
    
    if (!elderlyId) {
        showErrorAlert('ไม่พบหมายเลขข้อมูลเบี้ยยังชีพ');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการลบ',
        html: `
            <div style="text-align: left; padding: 1rem;">
                <div style="background: #ffebee; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #f44336;">
                    <strong style="color: #d32f2f;">⚠️ คำเตือน:</strong> การดำเนินการนี้ไม่สามารถยกเลิกได้!
                </div>
                <p><strong>หมายเลข:</strong> #${elderlyId}</p>
                <p><strong>ผู้ยื่นเรื่อง:</strong> ${elderlyBy}</p>
                <div style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">เหตุผลในการลบ:</label>
                    <textarea id="deleteReason" style="width: 100%; height: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" placeholder="ระบุเหตุผลในการลบข้อมูลนี้..."></textarea>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e57373',
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
            performDeleteElderly(elderlyId, deleteReason);
        }
    });
}

/**
 * ดำเนินการลบข้อมูลเบี้ยยังชีพ
 */
function performDeleteElderly(elderlyId, deleteReason) {
    console.log('Performing delete elderly:', elderlyId, deleteReason);
    
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
    formData.append('elderly_id', elderlyId);
    if (deleteReason) {
        formData.append('delete_reason', deleteReason);
    }
    
    fetch(ElderlyDetailConfig.deleteUrl, {
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
                text: data.message || 'ลบข้อมูลเบี้ยยังชีพเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = ElderlyDetailConfig.baseUrl + 'Elderly_aw_ods/elderly_aw_ods';
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
    const actionSections = document.querySelectorAll('.elderly-action-section, .elderly-detail-actions');
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
        confirmButtonColor: '#7db3e8'
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
            window.location.href = ElderlyDetailConfig.baseUrl + 'Elderly_aw_ods/elderly_aw_ods';
        }
    });
}

/**
 * จัดการ Responsive Images
 */
function handleResponsiveImages() {
    const images = document.querySelectorAll('.elderly-file-image');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const parent = this.closest('.elderly-file-card');
            if (parent) {
                const icon = document.createElement('div');
                icon.className = 'elderly-file-icon';
                icon.innerHTML = '<i class="fas fa-image" style="color: #e57373;"></i>';
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
    const currentStatus = '<?= $elderly_data->elderly_aw_ods_status ?>';
    
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
    fetch(ElderlyDetailConfig.baseUrl + 'Elderly_aw_ods/check_status/' + ElderlyDetailConfig.elderlyId, {
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
    console.log('🚀 Elderly AW ODS Detail Page loading...');
    
    try {
        // Initialize functionality
        handleKeyboardShortcuts();
        handleResponsiveImages();
        handleAutoRefresh();
        
        console.log('✅ Elderly AW ODS Detail Page initialized successfully');
        
        if (ElderlyDetailConfig.debug) {
            console.log('🔧 Debug mode enabled');
            console.log('⚙️ Configuration:', ElderlyDetailConfig);
            console.log('📄 Elderly Data:', {
                id: ElderlyDetailConfig.elderlyId,
                status: '<?= $elderly_data->elderly_aw_ods_status ?>',
                type: '<?= $elderly_data->elderly_aw_ods_type ?? "elderly" ?>',
                by: '<?= htmlspecialchars($elderly_data->elderly_aw_ods_by, ENT_QUOTES) ?>'
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
        confirmButtonColor: '#7db3e8'
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
        confirmButtonColor: '#7db3e8'
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
        color: var(--elderly-gray-900) !important;
    }
    
    .swal2-confirm {
        background-color: #7db3e8 !important;
        border-radius: 8px !important;
    }
    
    .swal2-cancel {
        background-color: #e57373 !important;
        border-radius: 8px !important;
    }
    
    @media print {
        .elderly-detail-actions,
        .elderly-action-section {
            display: none !important;
        }
        
        .elderly-detail-page {
            background: white !important;
            -webkit-print-color-adjust: exact !important;
        }
        
        .elderly-main-card,
        .elderly-sidebar-card {
            break-inside: avoid !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        
        .elderly-detail-header {
            background: #f5f5f5 !important;
            color: #333 !important;
        }
    }
`;
document.head.appendChild(style);

console.log("💼 Elderly AW ODS Detail System loaded successfully");
console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
console.log("📊 Detail Status: Ready");
console.log("📄 Elderly ID: " + ElderlyDetailConfig.elderlyId);
</script>