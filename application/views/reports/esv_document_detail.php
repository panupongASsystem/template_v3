<?php
// ===================================================================
// esv_document_detail.php - หน้ารายละเอียดเอกสารออนไลน์ ESV
// ===================================================================

// Helper function สำหรับ CSS class ของสถานะเอกสาร ESV
if (!function_exists('get_esv_ods_status_class')) {
    function get_esv_ods_status_class($status) {
        switch($status) {
            case 'pending': return 'pending';
            case 'processing': return 'processing';
            case 'completed': return 'completed';
            case 'rejected': return 'rejected';
            case 'cancelled': return 'cancelled';
            default: return 'pending';
        }
    }
}

// Helper function สำหรับแสดงสถานะเป็นภาษาไทย
if (!function_exists('get_esv_ods_status_display')) {
    function get_esv_ods_status_display($status) {
        switch($status) {
            case 'pending': return 'รอดำเนินการ';
            case 'processing': return 'กำลังดำเนินการ';
            case 'completed': return 'เสร็จสิ้น';
            case 'rejected': return 'ไม่อนุมัติ';
            case 'cancelled': return 'ยกเลิก';
            default: return 'รอดำเนินการ';
        }
    }
}

// Helper function สำหรับความสำคัญ
if (!function_exists('get_esv_ods_priority_display')) {
    function get_esv_ods_priority_display($priority) {
        switch($priority) {
            case 'normal': return 'ปกติ';
            case 'urgent': return 'เร่งด่วน';
            case 'very_urgent': return 'เร่งด่วนมาก';
            default: return 'ปกติ';
        }
    }
}

// Helper function สำหรับประเภทผู้ใช้
if (!function_exists('get_esv_ods_user_type_display')) {
    function get_esv_ods_user_type_display($user_type) {
        switch($user_type) {
            case 'guest': return 'ผู้ใช้ทั่วไป';
            case 'public': return 'สมาชิก';
            case 'staff': return 'เจ้าหน้าที่';
            default: return 'ผู้ใช้ทั่วไป';
        }
    }
}

// ตรวจสอบข้อมูลเอกสาร
$document = $document_detail ?? null;
if (!$document) {
    show_404();
    return;
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ===== ESV DOCUMENT DETAIL PAGE STYLES ===== */
.esv-detail-page {
    --esv-primary-color: #4f46e5;
    --esv-primary-light: #6366f1;
    --esv-secondary-color: #f0f9ff;
    --esv-success-color: #10b981;
    --esv-warning-color: #f59e0b;
    --esv-danger-color: #ef4444;
    --esv-info-color: #3b82f6;
    --esv-purple-color: #8b5cf6;
    --esv-light-bg: #fafbfc;
    --esv-white: #ffffff;
    --esv-gray-50: #f9fafb;
    --esv-gray-100: #f3f4f6;
    --esv-gray-200: #e5e7eb;
    --esv-gray-300: #d1d5db;
    --esv-gray-400: #9ca3af;
    --esv-gray-500: #6b7280;
    --esv-gray-600: #4b5563;
    --esv-gray-700: #374151;
    --esv-gray-800: #1f2937;
    --esv-gray-900: #111827;
    --esv-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.03);
    --esv-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04);
    --esv-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.06), 0 2px 4px -2px rgb(0 0 0 / 0.04);
    --esv-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.06), 0 4px 6px -4px rgb(0 0 0 / 0.04);
    --esv-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.06), 0 8px 10px -6px rgb(0 0 0 / 0.04);
    --esv-border-radius: 12px;
    --esv-border-radius-lg: 16px;
    --esv-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.esv-detail-page {
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--esv-gray-700);
    min-height: 100vh;
    padding: 2rem 0;
}

.esv-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* ===== PAGE HEADER ===== */
.esv-detail-header {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(99, 102, 241, 0.08) 100%);
    color: var(--esv-gray-800);
    padding: 2rem;
    border-radius: var(--esv-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--esv-shadow-lg);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(79, 70, 229, 0.15);
}

.esv-detail-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(79, 70, 229, 0.05) 0%, transparent 70%);
    border-radius: 50%;
}

.esv-detail-header h1 {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    text-shadow: none;
    position: relative;
    z-index: 1;
    color: var(--esv-gray-900);
}

.esv-detail-subtitle {
    font-size: 1.1rem;
    opacity: 0.8;
    margin: 0;
    position: relative;
    z-index: 1;
    color: var(--esv-gray-700);
}

.esv-detail-actions {
    position: absolute;
    top: 2rem;
    right: 2rem;
    display: flex;
    gap: 1rem;
    z-index: 2;
}

.esv-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--esv-transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    cursor: pointer;
}

.esv-btn-white {
    background: rgba(255, 255, 255, 0.95);
    color: var(--esv-primary-color);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(79, 70, 229, 0.2);
}

.esv-btn-white:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-lg);
    color: var(--esv-primary-color);
}

/* ===== CONTENT GRID ===== */
.esv-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

/* ===== MAIN CONTENT CARD ===== */
.esv-main-card {
    background: var(--esv-white);
    border-radius: var(--esv-border-radius);
    box-shadow: var(--esv-shadow-md);
    overflow: hidden;
    border: 1px solid var(--esv-gray-100);
}

.esv-card-header {
    background: linear-gradient(135deg, var(--esv-gray-50) 0%, var(--esv-gray-100) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--esv-gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.esv-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--esv-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.esv-status-badge {
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

.esv-status-badge.pending {
    background: linear-gradient(135deg, rgba(254, 243, 199, 0.8), rgba(253, 230, 138, 0.6));
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.esv-status-badge.processing {
    background: linear-gradient(135deg, rgba(219, 234, 254, 0.8), rgba(191, 219, 254, 0.6));
    color: #1d4ed8;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.esv-status-badge.completed {
    background: linear-gradient(135deg, rgba(209, 250, 229, 0.8), rgba(167, 243, 208, 0.6));
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.esv-status-badge.rejected {
    background: linear-gradient(135deg, rgba(254, 226, 226, 0.8), rgba(252, 165, 165, 0.6));
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.esv-status-badge.cancelled {
    background: linear-gradient(135deg, rgba(243, 244, 246, 0.8), rgba(229, 231, 235, 0.6));
    color: #6b7280;
    border: 1px solid rgba(156, 163, 175, 0.3);
}

.esv-card-body {
    padding: 2rem;
}

/* ===== INFO SECTIONS ===== */
.esv-info-section {
    margin-bottom: 2rem;
}

.esv-info-section:last-child {
    margin-bottom: 0;
}

.esv-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--esv-gray-900);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--esv-gray-100);
}

.esv-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.esv-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.esv-info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--esv-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.esv-info-value {
    font-size: 1rem;
    color: var(--esv-gray-900);
    padding: 0.75rem 1rem;
    background: var(--esv-gray-50);
    border-radius: 8px;
    border: 1px solid var(--esv-gray-200);
    min-height: 2.5rem;
    display: flex;
    align-items: center;
}

.esv-info-value.highlight {
    background: linear-gradient(135deg, var(--esv-secondary-color), #e3f2fd);
    border-color: var(--esv-primary-light);
    color: var(--esv-primary-color);
    font-weight: 600;
}

/* ===== SIDEBAR CARDS ===== */
.esv-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.esv-sidebar-card {
    background: var(--esv-white);
    border-radius: var(--esv-border-radius);
    box-shadow: var(--esv-shadow-md);
    overflow: hidden;
    border: 1px solid var(--esv-gray-100);
}

.esv-sidebar-header {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(99, 102, 241, 0.08) 100%);
    color: var(--esv-gray-800);
    padding: 1rem 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid rgba(79, 70, 229, 0.15);
    border-bottom: 1px solid var(--esv-gray-200);
}

.esv-sidebar-body {
    padding: 1.5rem;
}

/* ===== QUICK INFO CARD ===== */
.esv-quick-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.esv-quick-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: var(--esv-gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--esv-primary-color);
}

.esv-quick-label {
    font-size: 0.875rem;
    color: var(--esv-gray-600);
    font-weight: 500;
}

.esv-quick-value {
    font-weight: 600;
    color: var(--esv-gray-900);
}

/* ===== FILES SECTION ===== */
.esv-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.esv-file-card {
    background: var(--esv-gray-50);
    border: 2px solid var(--esv-gray-200);
    border-radius: var(--esv-border-radius);
    padding: 1.5rem;
    text-align: center;
    transition: var(--esv-transition);
    cursor: pointer;
    position: relative;
}

.esv-file-card:hover {
    border-color: var(--esv-primary-color);
    background: var(--esv-secondary-color);
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-md);
}

.esv-file-card.pdf-file {
    border-color: #e74c3c;
}

.esv-file-card.pdf-file:hover {
    border-color: #c0392b;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.05));
    box-shadow: 0 4px 20px rgba(231, 76, 60, 0.2);
}

.esv-file-icon {
    font-size: 2.5rem;
    color: var(--esv-primary-color);
    margin-bottom: 1rem;
}

.esv-file-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--esv-gray-900);
    margin-bottom: 0.5rem;
    word-break: break-word;
}

.esv-file-meta {
    font-size: 0.75rem;
    color: var(--esv-gray-600);
}

.esv-file-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
}

/* PDF Viewer Badge */
.esv-pdf-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(231, 76, 60, 0.3);
}

/* ===== HISTORY SECTION ===== */
.esv-history-timeline {
    position: relative;
    padding-left: 2rem;
}

.esv-history-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--esv-gray-200);
}

.esv-history-item {
    position: relative;
    margin-bottom: 1.5rem;
    background: var(--esv-white);
    border: 1px solid var(--esv-gray-200);
    border-radius: var(--esv-border-radius);
    padding: 1.5rem;
    box-shadow: var(--esv-shadow-sm);
}

.esv-history-item::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 1.5rem;
    width: 12px;
    height: 12px;
    background: var(--esv-primary-color);
    border-radius: 50%;
    border: 3px solid var(--esv-white);
    box-shadow: 0 0 0 2px var(--esv-primary-color);
}

.esv-history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.esv-history-action {
    font-weight: 600;
    color: var(--esv-gray-900);
}

.esv-history-date {
    font-size: 0.875rem;
    color: var(--esv-gray-600);
}

.esv-history-description {
    color: var(--esv-gray-700);
    line-height: 1.5;
}

.esv-history-by {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--esv-gray-600);
    font-style: italic;
}

/* ===== ACTION BUTTONS ===== */
.esv-action-section {
    background: var(--esv-white);
    border-radius: var(--esv-border-radius);
    box-shadow: var(--esv-shadow-md);
    border: 1px solid var(--esv-gray-100);
    margin-top: 2rem;
}

.esv-action-header {
    background: linear-gradient(135deg, var(--esv-gray-50) 0%, var(--esv-gray-100) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--esv-gray-200);
}

.esv-action-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--esv-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.esv-action-body {
    padding: 2rem;
}

.esv-action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.esv-btn-action {
    padding: 1rem 1.5rem;
    border-radius: var(--esv-border-radius);
    border: 2px solid transparent;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--esv-transition);
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    min-height: 3rem;
}

.esv-btn-action.primary {
    background: linear-gradient(135deg, var(--esv-primary-color), var(--esv-primary-light));
    color: white;
}

.esv-btn-action.primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-action.success {
    background: linear-gradient(135deg, var(--esv-success-color), #34d399);
    color: white;
}

.esv-btn-action.success:hover {
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-action.warning {
    background: linear-gradient(135deg, var(--esv-warning-color), #fbbf24);
    color: white;
}

.esv-btn-action.warning:hover {
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-action.danger {
    background: linear-gradient(135deg, var(--esv-danger-color), #f87171);
    color: white;
}

.esv-btn-action.danger:hover {
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-btn-action.secondary {
    background: var(--esv-gray-100);
    color: var(--esv-gray-700);
    border-color: var(--esv-gray-300);
}

.esv-btn-action.secondary:hover {
    background: var(--esv-gray-200);
    color: var(--esv-gray-800);
}

/* ===== OTHER BADGES ===== */
.esv-priority-badge, 
.esv-user-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.esv-priority-badge.normal {
    background: var(--esv-gray-100);
    color: var(--esv-gray-700);
}

.esv-priority-badge.urgent {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.esv-priority-badge.very_urgent {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.esv-user-type-badge.guest {
    background: var(--esv-gray-100);
    color: var(--esv-gray-600);
}

.esv-user-type-badge.public {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.esv-user-type-badge.staff {
    background: linear-gradient(135deg, var(--esv-secondary-color), #dbeafe);
    color: var(--esv-primary-color);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .esv-detail-page {
        padding: 1rem 0;
    }
    
    .esv-detail-container {
        padding: 0 0.5rem;
    }
    
    .esv-detail-header {
        padding: 1.5rem;
        text-align: center;
    }
    
    .esv-detail-header h1 {
        font-size: 1.8rem;
    }
    
    .esv-detail-actions {
        position: static;
        margin-top: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .esv-detail-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .esv-card-body {
        padding: 1.5rem;
    }
    
    .esv-info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .esv-files-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .esv-action-grid {
        grid-template-columns: 1fr;
    }
    
    .esv-history-timeline {
        padding-left: 1.5rem;
    }
    
    .esv-history-item::before {
        left: -1.5rem;
    }
}

@media (max-width: 480px) {
    .esv-detail-header h1 {
        font-size: 1.5rem;
    }
    
    .esv-btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
    
    .esv-card-header {
        padding: 1rem 1.5rem;
    }
    
    .esv-card-body {
        padding: 1rem;
    }
    
    .esv-sidebar-body {
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

.esv-main-card,
.esv-sidebar-card,
.esv-action-section {
    animation: fadeInUp 0.5s ease-out;
}

/* ===== LOADING STATES ===== */
.esv-loading {
    opacity: 0.6;
    pointer-events: none;
}

.esv-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--esv-gray-300);
    border-top: 2px solid var(--esv-primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="esv-detail-page">
    <div class="esv-detail-container">
        <!-- ===== PAGE HEADER ===== -->
        <header class="esv-detail-header">
            <div class="esv-detail-actions">
                <a href="<?= site_url('Esv_ods/admin_management') ?>" class="esv-btn esv-btn-white">
                    <i class="fas fa-arrow-left"></i>
                    กลับไปรายการ
                </a>
                <?php if (isset($can_handle_document) && $can_handle_document): ?>
                    <button class="esv-btn esv-btn-white" onclick="printPage()">
                        <i class="fas fa-print"></i>
                        พิมพ์
                    </button>
                    
                <?php endif; ?>
            </div>
            
            <h1>
                <i class="fas fa-file-alt me-3"></i>
                รายละเอียดเอกสารออนไลน์ #<?= htmlspecialchars($document->esv_ods_reference_id) ?>
            </h1>
            <p class="esv-detail-subtitle">
                เรื่อง: <?= htmlspecialchars($document->esv_ods_topic) ?>
                | ยื่นโดย: <?= htmlspecialchars($document->esv_ods_by) ?>
                | วันที่: <?php
                    $thai_months = [
                        '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
                        '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
                        '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                    ];
                    
                    $date = date('j', strtotime($document->esv_ods_datesave));
                    $month = $thai_months[date('m', strtotime($document->esv_ods_datesave))];
                    $year = date('Y', strtotime($document->esv_ods_datesave)) + 543;
                    
                    echo $date . ' ' . $month . ' ' . $year;
                ?>
            </p>
        </header>

        <!-- ===== MAIN CONTENT GRID ===== -->
        <div class="esv-detail-grid">
            <!-- ===== MAIN CONTENT ===== -->
            <div class="esv-main-content">
                <!-- ===== DOCUMENT INFORMATION CARD ===== -->
                <div class="esv-main-card">
                    <div class="esv-card-header">
                        <h2 class="esv-card-title">
                            <i class="fas fa-info-circle"></i>
                            ข้อมูลเอกสาร
                        </h2>
                        <span class="esv-status-badge <?= get_esv_ods_status_class($document->esv_ods_status) ?>">
                            <?= get_esv_ods_status_display($document->esv_ods_status) ?>
                        </span>
                    </div>
                    <div class="esv-card-body">
                        <div class="esv-info-section">
                            <h3 class="esv-section-title">
                                <i class="fas fa-file-alt"></i>
                                ข้อมูลพื้นฐาน
                            </h3>
                            <div class="esv-info-grid">
                                <div class="esv-info-item">
                                    <span class="esv-info-label">หมายเลขอ้างอิง</span>
                                    <div class="esv-info-value highlight">
                                        #<?= htmlspecialchars($document->esv_ods_reference_id) ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">เรื่อง</span>
                                    <div class="esv-info-value">
                                        <?= htmlspecialchars($document->esv_ods_topic) ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">ประเภทเอกสาร</span>
                                    <div class="esv-info-value">
                                        <?= !empty($document->type_name) ? htmlspecialchars($document->type_name) : 'ไม่ระบุ' ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">แผนกปลายทาง</span>
                                    <div class="esv-info-value">
                                        <?= !empty($document->department_name) ? htmlspecialchars($document->department_name) : 
                                            (!empty($document->esv_ods_department_other) ? htmlspecialchars($document->esv_ods_department_other) : 'ไม่ระบุ') ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">หมวดหมู่</span>
                                    <div class="esv-info-value">
                                        <?= !empty($document->category_name) ? htmlspecialchars($document->category_name) : 
                                            (!empty($document->esv_ods_category_other) ? htmlspecialchars($document->esv_ods_category_other) : 'ไม่ระบุ') ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">ความสำคัญ</span>
                                    <div class="esv-info-value">
                                        <span class="esv-priority-badge <?= $document->esv_ods_priority ?? 'normal' ?>">
                                            <?= get_esv_ods_priority_display($document->esv_ods_priority ?? 'normal') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="esv-info-section">
                            <h3 class="esv-section-title">
                                <i class="fas fa-align-left"></i>
                                รายละเอียด
                            </h3>
                            <div class="esv-info-value" style="min-height: auto; white-space: pre-wrap;">
                                <?= nl2br(htmlspecialchars($document->esv_ods_detail)) ?>
                            </div>
                        </div>

                        <div class="esv-info-section">
                            <h3 class="esv-section-title">
                                <i class="fas fa-user"></i>
                                ข้อมูลผู้ยื่นเรื่อง
                            </h3>
                            <div class="esv-info-grid">
                                <div class="esv-info-item">
                                    <span class="esv-info-label">ชื่อ-นามสกุล</span>
                                    <div class="esv-info-value">
                                        <?= htmlspecialchars($document->esv_ods_by) ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">เบอร์โทรศัพท์</span>
                                    <div class="esv-info-value">
                                        <?= htmlspecialchars($document->esv_ods_phone) ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">อีเมล</span>
                                    <div class="esv-info-value">
                                        <?= !empty($document->esv_ods_email) ? htmlspecialchars($document->esv_ods_email) : 'ไม่ระบุ' ?>
                                    </div>
                                </div>
                                <div class="esv-info-item">
                                    <span class="esv-info-label">ประเภทผู้ใช้</span>
                                    <div class="esv-info-value">
                                        <span class="esv-user-type-badge <?= $document->esv_ods_user_type ?? 'guest' ?>">
                                            <?= get_esv_ods_user_type_display($document->esv_ods_user_type ?? 'guest') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="esv-info-item" style="grid-column: 1 / -1;">
                                    <span class="esv-info-label">ที่อยู่</span>
                                    <div class="esv-info-value">
                                        <?= htmlspecialchars($document->esv_ods_address) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($document->esv_ods_response)): ?>
                            <div class="esv-info-section">
                                <h3 class="esv-section-title">
                                    <i class="fas fa-reply"></i>
                                    การตอบกลับ
                                </h3>
                                <div class="esv-info-value" style="min-height: auto; white-space: pre-wrap;">
                                    <?= nl2br(htmlspecialchars($document->esv_ods_response)) ?>
                                </div>
                                <?php if (!empty($document->esv_ods_response_by)): ?>
                                    <div style="margin-top: 1rem; font-size: 0.875rem; color: var(--esv-gray-600);">
                                        <strong>ตอบกลับโดย:</strong> <?= htmlspecialchars($document->esv_ods_response_by) ?>
                                        <?php if (!empty($document->esv_ods_response_date)): ?>
                                            | <strong>เมื่อวันที่:</strong> 
                                            <?php
                                            $response_date = date('j', strtotime($document->esv_ods_response_date));
                                            $response_month = $thai_months[date('m', strtotime($document->esv_ods_response_date))];
                                            $response_year = date('Y', strtotime($document->esv_ods_response_date)) + 543;
                                            $response_time = date('H:i', strtotime($document->esv_ods_response_date));
                                            echo $response_date . ' ' . $response_month . ' ' . $response_year . ' เวลา ' . $response_time . ' น.';
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        
                    </div>
                </div>

                <!-- ===== FILES SECTION ===== -->
                <?php if (!empty($document->files) && is_array($document->files)): ?>
                    <div class="esv-main-card">
                        <div class="esv-card-header">
                            <h2 class="esv-card-title">
                                <i class="fas fa-paperclip"></i>
                                ไฟล์แนบ (<?= count($document->files) ?> ไฟล์)
                            </h2>
                        </div>
                        <div class="esv-card-body">
                            <div class="esv-files-grid">
                                <?php foreach ($document->files as $file): ?>
                                    <?php 
                                    $file_extension = strtolower(pathinfo($file->esv_file_original_name, PATHINFO_EXTENSION));
                                    $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $is_pdf = ($file_extension === 'pdf');
                                    $file_url = site_url('docs/esv_files/' . $file->esv_file_name);
                                    
                                    // กำหนด action สำหรับแต่ละประเภทไฟล์
                                    if ($is_pdf) {
                                        $file_action = "openPdfViewer('$file_url', '" . htmlspecialchars($file->esv_file_original_name, ENT_QUOTES) . "')";
                                        $additional_class = 'pdf-file';
                                    } elseif ($is_image) {
                                        $file_action = "showImagePreview('$file_url', '" . htmlspecialchars($file->esv_file_original_name, ENT_QUOTES) . "')";
                                        $additional_class = '';
                                    } else {
                                        $file_action = "downloadFile('$file_url', '" . htmlspecialchars($file->esv_file_original_name, ENT_QUOTES) . "')";
                                        $additional_class = '';
                                    }
                                    ?>
                                    
                                    <div class="esv-file-card <?= $additional_class ?>" 
                                         onclick="<?= $file_action ?>">
                                        
                                        <?php if ($is_pdf): ?>
                                            <div class="esv-pdf-badge">
                                                <i class="fas fa-eye"></i> ดู PDF
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($is_image): ?>
                                            <img src="<?= $file_url ?>" 
                                                 alt="<?= htmlspecialchars($file->esv_file_original_name) ?>" 
                                                 class="esv-file-image"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div class="esv-file-icon">
                                                <?php 
                                                switch($file_extension) {
                                                    case 'pdf': echo '<i class="fas fa-file-pdf" style="color: #e74c3c;"></i>'; break;
                                                    case 'doc':
                                                    case 'docx': echo '<i class="fas fa-file-word" style="color: #3498db;"></i>'; break;
                                                    case 'xls':
                                                    case 'xlsx': echo '<i class="fas fa-file-excel" style="color: #27ae60;"></i>'; break;
                                                    case 'txt': echo '<i class="fas fa-file-alt" style="color: #95a5a6;"></i>'; break;
                                                    default: echo '<i class="fas fa-file" style="color: #4f46e5;"></i>'; break;
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="esv-file-name">
                                            <?= htmlspecialchars($file->esv_file_original_name) ?>
                                        </div>
                                        <div class="esv-file-meta">
                                            <?= number_format($file->esv_file_size / 1024, 1) ?> KB
                                            | <?= date('d/m/Y H:i', strtotime($file->esv_file_uploaded_at)) ?>
                                            <?php if ($is_pdf): ?>
                                                
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ===== HISTORY SECTION ===== -->
                <?php if (!empty($document->history) && is_array($document->history)): ?>
                    <div class="esv-main-card">
                        <div class="esv-card-header">
                            <h2 class="esv-card-title">
                                <i class="fas fa-history"></i>
                                ประวัติการดำเนินการ
                            </h2>
                        </div>
                        <div class="esv-card-body">
                            <div class="esv-history-timeline">
                                <?php foreach ($document->history as $history): ?>
                                    <div class="esv-history-item">
                                        <div class="esv-history-header">
                                            <div class="esv-history-action">
                                                <?= htmlspecialchars($history->esv_history_description) ?>
                                            </div>
                                            <div class="esv-history-date">
                                                <?php
                                                $history_date = date('j', strtotime($history->esv_history_created_at));
                                                $history_month = $thai_months[date('m', strtotime($history->esv_history_created_at))];
                                                $history_year = date('Y', strtotime($history->esv_history_created_at)) + 543;
                                                $history_time = date('H:i', strtotime($history->esv_history_created_at));
                                                echo $history_date . ' ' . $history_month . ' ' . $history_year . ' เวลา ' . $history_time . ' น.';
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($history->esv_history_old_status) && !empty($history->esv_history_new_status)): ?>
                                            <div class="esv-history-description">
                                                เปลี่ยนสถานะจาก 
                                                <span class="esv-status-badge <?= get_esv_ods_status_class($history->esv_history_old_status) ?>" style="display: inline-block; margin: 0 0.5rem;">
                                                    <?= get_esv_ods_status_display($history->esv_history_old_status) ?>
                                                </span>
                                                เป็น
                                                <span class="esv-status-badge <?= get_esv_ods_status_class($history->esv_history_new_status) ?>" style="display: inline-block; margin: 0 0.5rem;">
                                                    <?= get_esv_ods_status_display($history->esv_history_new_status) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="esv-history-by">
                                            โดย: <?= htmlspecialchars($history->esv_history_by) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ===== SIDEBAR ===== -->
            <div class="esv-sidebar">
                <!-- ===== QUICK INFO CARD ===== -->
                <div class="esv-sidebar-card">
                    <div class="esv-sidebar-header">
                        <i class="fas fa-info-circle"></i>
                        ข้อมูลสรุป
                    </div>
                    <div class="esv-sidebar-body">
                        <div class="esv-quick-info">
                            <div class="esv-quick-item">
                                <span class="esv-quick-label">สถานะปัจจุบัน</span>
                                <span class="esv-status-badge <?= get_esv_ods_status_class($document->esv_ods_status) ?>">
                                    <?= get_esv_ods_status_display($document->esv_ods_status) ?>
                                </span>
                            </div>
                            <div class="esv-quick-item">
                                <span class="esv-quick-label">ความสำคัญ</span>
                                <span class="esv-priority-badge <?= $document->esv_ods_priority ?? 'normal' ?>">
                                    <?= get_esv_ods_priority_display($document->esv_ods_priority ?? 'normal') ?>
                                </span>
                            </div>
                            <div class="esv-quick-item">
                                <span class="esv-quick-label">ประเภทผู้ใช้</span>
                                <span class="esv-user-type-badge <?= $document->esv_ods_user_type ?? 'guest' ?>">
                                    <?= get_esv_ods_user_type_display($document->esv_ods_user_type ?? 'guest') ?>
                                </span>
                            </div>
                            <div class="esv-quick-item">
                                <span class="esv-quick-label">วันที่ยื่นเรื่อง</span>
                                <span class="esv-quick-value">
                                    <?php
                                    $date = date('j', strtotime($document->esv_ods_datesave));
                                    $month = $thai_months[date('m', strtotime($document->esv_ods_datesave))];
                                    $year = date('Y', strtotime($document->esv_ods_datesave)) + 543;
                                    echo $date . ' ' . $month . ' ' . $year;
                                    ?>
                                </span>
                            </div>
                            <?php if (!empty($document->esv_ods_updated_at)): ?>
                                <div class="esv-quick-item">
                                    <span class="esv-quick-label">อัปเดตล่าสุด</span>
                                    <span class="esv-quick-value">
                                        <?php
                                        $update_date = date('j', strtotime($document->esv_ods_updated_at));
                                        $update_month = $thai_months[date('m', strtotime($document->esv_ods_updated_at))];
                                        $update_year = date('Y', strtotime($document->esv_ods_updated_at)) + 543;
                                        echo $update_date . ' ' . $update_month . ' ' . $update_year;
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($document->esv_ods_updated_by)): ?>
                                <div class="esv-quick-item">
                                    <span class="esv-quick-label">อัปเดตโดย</span>
                                    <span class="esv-quick-value">
                                        <?= htmlspecialchars($document->esv_ods_updated_by) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($document->esv_ods_viewed_count)): ?>
                                <div class="esv-quick-item">
                                    <span class="esv-quick-label">จำนวนครั้งที่ดู</span>
                                    <span class="esv-quick-value">
                                        <?= number_format($document->esv_ods_viewed_count) ?> ครั้ง
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ===== CONTACT CARD ===== -->
                <div class="esv-sidebar-card">
                    <div class="esv-sidebar-header">
                        <i class="fas fa-phone"></i>
                        ข้อมูลติดต่อ
                    </div>
                    <div class="esv-sidebar-body">
                        <div class="esv-quick-info">
                            <div class="esv-quick-item">
                                <span class="esv-quick-label">เบอร์โทรศัพท์</span>
                                <span class="esv-quick-value">
                                    <a href="tel:<?= htmlspecialchars($document->esv_ods_phone) ?>" 
                                       style="color: var(--esv-primary-color); text-decoration: none;">
                                        <?= htmlspecialchars($document->esv_ods_phone) ?>
                                    </a>
                                </span>
                            </div>
                            <?php if (!empty($document->esv_ods_email)): ?>
                                <div class="esv-quick-item">
                                    <span class="esv-quick-label">อีเมล</span>
                                    <span class="esv-quick-value">
                                        <a href="mailto:<?= htmlspecialchars($document->esv_ods_email) ?>" 
                                           style="color: var(--esv-primary-color); text-decoration: none;">
                                            <?= htmlspecialchars($document->esv_ods_email) ?>
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

const EsvDetailConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Esv_ods/update_document_status") ?>',
    deleteUrl: '<?= site_url("Esv_ods/delete_document") ?>',
    editUrl: '<?= site_url("Esv_ods/edit_document") ?>',
    addNoteUrl: '<?= site_url("Esv_ods/add_document_note") ?>',
    exportUrl: '<?= site_url("Esv_ods/export_document") ?>',
    referenceId: '<?= $document->esv_ods_reference_id ?>',
    debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
};

const statusDisplayMap = {
    'pending': 'รอดำเนินการ',
    'processing': 'กำลังดำเนินการ',
    'completed': 'เสร็จสิ้น',
    'rejected': 'ไม่อนุมัติ',
    'cancelled': 'ยกเลิก'
};

// ===================================================================
// *** CORE FUNCTIONS ***
// ===================================================================

/**
 * อัปเดตสถานะเอกสาร
 */
function updateDocumentStatus(referenceId, newStatus, documentBy, statusDisplay) {
    console.log('Updating document status:', referenceId, newStatus, documentBy, statusDisplay);
    
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        html: `
            <div style="text-align: left; padding: 1rem;">
                <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #2196f3;">
                    <strong>เอกสาร:</strong> #${referenceId}<br>
                    <strong>ผู้ยื่นเรื่อง:</strong> ${documentBy}<br>
                    <strong>เปลี่ยนเป็น:</strong> <span style="color: #1976d2; font-weight: 600;">${statusDisplay}</span>
                </div>
                <div style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">ความสำคัญ:</label>
                    <select id="statusPriority" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="normal">ปกติ</option>
                        <option value="urgent">เร่งด่วน</option>
                        <option value="very_urgent">เร่งด่วนมาก</option>
                    </select>
                </div>
                <div style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">หมายเหตุ (ไม่บังคับ):</label>
                    <textarea id="statusNote" style="width: 100%; height: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" placeholder="ระบุหมายเหตุเพิ่มเติม..."></textarea>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'swal2-popup-large'
        },
        preConfirm: () => {
            const priority = document.getElementById('statusPriority').value;
            const note = document.getElementById('statusNote').value.trim();
            return { priority, note };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { priority, note } = result.value;
            performUpdateDocumentStatus(referenceId, newStatus, priority, note);
        }
    });
}

/**
 * ดำเนินการอัปเดตสถานะ
 */
function performUpdateDocumentStatus(referenceId, newStatus, priority, note) {
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
    formData.append('reference_id', referenceId);
    formData.append('new_status', newStatus);
    formData.append('new_priority', priority);
    if (note) {
        formData.append('note', note);
    }
    
    fetch(EsvDetailConfig.updateStatusUrl, {
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
function addDocumentNote(referenceId) {
    console.log('Adding note for document:', referenceId);
    
    Swal.fire({
        title: 'เพิ่มหมายเหตุ',
        html: `
            <div style="text-align: left; padding: 1rem;">
                <div style="background: #f3f4f6; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <strong>เอกสาร:</strong> #${referenceId}
                </div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">หมายเหตุ:</label>
                <textarea id="documentNote" style="width: 100%; height: 120px; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;" placeholder="กรอกหมายเหตุ..."></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#9e9e9e',
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'swal2-popup-large'
        },
        preConfirm: () => {
            const note = document.getElementById('documentNote').value.trim();
            if (!note) {
                Swal.showValidationMessage('กรุณากรอกหมายเหตุ');
                return false;
            }
            return note;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const note = result.value;
            performAddDocumentNote(referenceId, note);
        }
    });
}

/**
 * ดำเนินการเพิ่มหมายเหตุ
 */
function performAddDocumentNote(referenceId, note) {
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
    formData.append('reference_id', referenceId);
    formData.append('note', note);
    
    fetch(EsvDetailConfig.addNoteUrl, {
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
 * ยืนยันการลบเอกสาร
 */
function confirmDeleteDocument(referenceId, documentTitle) {
    console.log('Confirming delete document:', referenceId, documentTitle);
    
    if (!referenceId) {
        showErrorAlert('ไม่พบหมายเลขอ้างอิงเอกสาร');
        return;
    }
    
    Swal.fire({
        title: 'ยืนยันการยกเลิกเอกสาร',
        html: `
            <div style="text-align: left; padding: 1rem;">
                <div style="background: #ffebee; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #f44336;">
                    <strong style="color: #d32f2f;">⚠️ คำเตือน:</strong> การดำเนินการนี้ไม่สามารถยกเลิกได้!
                </div>
                <p><strong>หมายเลขอ้างอิง:</strong> #${referenceId}</p>
                <p><strong>เรื่อง:</strong> ${documentTitle}</p>
                <div style="margin-top: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">เหตุผลในการยกเลิก (ไม่บังคับ):</label>
                    <textarea id="deleteReason" style="width: 100%; height: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" placeholder="ระบุเหตุผลในการยกเลิกเอกสารนี้..."></textarea>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9e9e9e',
        confirmButtonText: 'ยกเลิกเอกสาร',
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
            performDeleteDocument(referenceId, deleteReason);
        }
    });
}

/**
 * ดำเนินการลบเอกสาร
 */
function performDeleteDocument(referenceId, deleteReason) {
    console.log('Performing delete document:', referenceId, deleteReason);
    
    // แสดง loading
    Swal.fire({
        title: 'กำลังยกเลิก...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData();
    formData.append('reference_id', referenceId);
    formData.append('new_status', 'cancelled');
    if (deleteReason) {
        formData.append('note', deleteReason);
    }
    
    fetch(EsvDetailConfig.updateStatusUrl, {
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
                title: 'ยกเลิกสำเร็จ!',
                text: data.message || 'ยกเลิกเอกสารเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = EsvDetailConfig.baseUrl + 'Esv_ods/admin_management';
            });
        } else {
            showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการยกเลิก');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
    });
}

/**
 * แก้ไขเอกสาร
 */
function editDocument(referenceId) {
    console.log('Editing document:', referenceId);
    
    // เปิดหน้าแก้ไขในแท็บใหม่หรือ redirect
    window.location.href = EsvDetailConfig.editUrl + '/' + referenceId;
}

/**
 * ส่งออกเอกสาร
 */
function exportDocument(referenceId) {
    console.log('Exporting document:', referenceId);
    
    Swal.fire({
        title: 'ส่งออกข้อมูล',
        text: 'เลือกรูปแบบที่ต้องการส่งออก',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#9e9e9e',
        confirmButtonText: '<i class="fas fa-file-excel"></i> Excel',
        cancelButtonText: '<i class="fas fa-file-pdf"></i> PDF',
        showDenyButton: true,
        denyButtonText: 'ยกเลิก',
        denyButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // Export Excel
            window.open(EsvDetailConfig.exportUrl + '/' + referenceId + '?format=excel', '_blank');
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Export PDF
            window.open(EsvDetailConfig.exportUrl + '/' + referenceId + '?format=pdf', '_blank');
        }
    });
}

/**
 * เปิดไฟล์ PDF ในแท็บใหม่ (ฟังก์ชันใหม่)
 */
function openPdfViewer(pdfUrl, fileName) {
    console.log('Opening PDF viewer:', pdfUrl, fileName);
    
    try {
        // เปิด PDF ในแท็บใหม่
        const newTab = window.open(pdfUrl, '_blank');
        
        // ตรวจสอบว่าแท็บเปิดสำเร็จหรือไม่
        if (newTab) {
            // แสดงการแจ้งเตือนว่ากำลังเปิด PDF
            Swal.fire({
                title: 'เปิด PDF',
                text: `กำลังเปิดไฟล์ "${fileName}" ในแท็บใหม่`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // หากเบราว์เซอร์บล็อกป๊อปอัพ
            throw new Error('Popup blocked');
        }
        
    } catch (error) {
        console.error('PDF viewer error:', error);
        
        // หากไม่สามารถเปิดแท็บใหม่ได้ แสดงตัวเลือกทางเลือก
        Swal.fire({
            title: 'ไม่สามารถเปิดแท็บใหม่ได้',
            text: 'เบราว์เซอร์อาจบล็อกป๊อปอัพ คุณต้องการดาวน์โหลดไฟล์แทนหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-download"></i> ดาวน์โหลด',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                downloadFile(pdfUrl, fileName);
            }
        });
    }
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
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
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
    const actionSections = document.querySelectorAll('.esv-action-section, .esv-detail-actions');
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
        confirmButtonColor: '#4f46e5'
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
            window.location.href = EsvDetailConfig.baseUrl + 'Esv_ods/admin_management';
        }
        
        // Ctrl/Cmd + E = Export
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            if (typeof exportDocument === 'function') {
                exportDocument(EsvDetailConfig.referenceId);
            }
        }
    });
}

/**
 * จัดการ Responsive Images
 */
function handleResponsiveImages() {
    const images = document.querySelectorAll('.esv-file-image');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const parent = this.closest('.esv-file-card');
            if (parent) {
                const icon = document.createElement('div');
                icon.className = 'esv-file-icon';
                icon.innerHTML = '<i class="fas fa-image" style="color: #ef4444;"></i>';
                parent.insertBefore(icon, this);
            }
        });
    });
}

/**
 * จัดการ Auto Refresh (สำหรับสถานะที่เปลี่ยนแปลงบ่อย)
 */
function handleAutoRefresh() {
    // Auto refresh ทุก 5 นาที หากสถานะเป็น processing
    const currentStatus = '<?= $document->esv_ods_status ?>';
    
    if (currentStatus === 'processing' || currentStatus === 'pending') {
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
    fetch(EsvDetailConfig.baseUrl + 'Esv_ods/check_status/' + EsvDetailConfig.referenceId, {
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

/**
 * จัดการ File Viewer Enhancement
 */
function handleFileViewerEnhancement() {
    // เพิ่มการแสดง tooltip สำหรับไฟล์
    const fileCards = document.querySelectorAll('.esv-file-card');
    fileCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const fileName = this.querySelector('.esv-file-name').textContent;
            const fileMeta = this.querySelector('.esv-file-meta').textContent;
            const isPdf = this.classList.contains('pdf-file');
            
            let tooltipText = `${fileName}\n${fileMeta}`;
            if (isPdf) {
                tooltipText += '\nคลิกเพื่อเปิด PDF ในแท็บใหม่';
            } else {
                tooltipText += '\nคลิกเพื่อดาวน์โหลดหรือดูตัวอย่าง';
            }
            
            this.setAttribute('title', tooltipText);
        });
    });
    
    // เพิ่ม hover effect พิเศษสำหรับไฟล์ PDF
    const pdfCards = document.querySelectorAll('.esv-file-card.pdf-file');
    pdfCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const badge = this.querySelector('.esv-pdf-badge');
            if (badge) {
                badge.style.transform = 'scale(1.1)';
                badge.style.boxShadow = '0 4px 8px rgba(231, 76, 60, 0.4)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const badge = this.querySelector('.esv-pdf-badge');
            if (badge) {
                badge.style.transform = 'scale(1)';
                badge.style.boxShadow = '0 2px 4px rgba(231, 76, 60, 0.3)';
            }
        });
    });
}

// ===================================================================
// *** DOCUMENT READY & INITIALIZATION ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 ESV Document Detail Page loading...');
    
    try {
        // Initialize functionality
        handleKeyboardShortcuts();
        handleResponsiveImages();
        handleAutoRefresh();
        handleFileViewerEnhancement();
        
        console.log('✅ ESV Document Detail Page initialized successfully');
        
        if (EsvDetailConfig.debug) {
            console.log('🔧 Debug mode enabled');
            console.log('⚙️ Configuration:', EsvDetailConfig);
            console.log('📄 Document Data:', {
                referenceId: EsvDetailConfig.referenceId,
                status: '<?= $document->esv_ods_status ?>',
                by: '<?= htmlspecialchars($document->esv_ods_by, ENT_QUOTES) ?>',
                topic: '<?= htmlspecialchars($document->esv_ods_topic, ENT_QUOTES) ?>'
            });
        }
        
        // เพิ่มการนับจำนวนครั้งที่ดู (ถ้าต้องการ)
        incrementViewCount();
        
    } catch (error) {
        console.error('❌ Initialization error:', error);
        showErrorAlert('เกิดข้อผิดพลาดในการโหลดหน้า กรุณารีเฟรชหน้า');
    }
});

/**
 * เพิ่มการนับจำนวนครั้งที่ดู
 */
function incrementViewCount() {
    try {
        fetch(EsvDetailConfig.baseUrl + 'Esv_ods/increment_view/' + EsvDetailConfig.referenceId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(error => {
            console.log('View count increment failed:', error);
        });
    } catch (error) {
        console.log('View count increment error:', error);
    }
}

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
        confirmButtonColor: '#4f46e5'
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
        confirmButtonColor: '#4f46e5'
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
        color: var(--esv-gray-900) !important;
    }
    
    .swal2-confirm {
        background-color: #4f46e5 !important;
        border-radius: 8px !important;
    }
    
    .swal2-cancel {
        background-color: #ef4444 !important;
        border-radius: 8px !important;
    }
    
    .swal2-deny {
        background-color: #6c757d !important;
        border-radius: 8px !important;
    }
    
    @media print {
        .esv-detail-actions,
        .esv-action-section {
            display: none !important;
        }
        
        .esv-detail-page {
            background: white !important;
            -webkit-print-color-adjust: exact !important;
        }
        
        .esv-main-card,
        .esv-sidebar-card {
            break-inside: avoid !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        
        .esv-detail-header {
            background: rgba(248, 250, 252, 0.95) !important;
            color: #333 !important;
            border: 1px solid #e5e7eb !important;
        }
        
        .esv-status-badge {
            border: 1px solid #333 !important;
        }
    }
    
    /* เพิ่ม Animation สำหรับ Loading States */
    .esv-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .esv-loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #4f46e5;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    /* เพิ่ม Hover Effects สำหรับ Interactive Elements */
    .esv-quick-item:hover {
        background: var(--esv-gray-100) !important;
        transform: translateX(2px);
        transition: var(--esv-transition);
    }
    
    .esv-info-value:hover {
        background: var(--esv-gray-100) !important;
        transition: var(--esv-transition);
    }
    
    /* เพิ่ม Responsive Text */
    @media (max-width: 480px) {
        .esv-detail-subtitle {
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .esv-quick-label,
        .esv-quick-value {
            font-size: 0.8rem;
        }
        
        .esv-info-label {
            font-size: 0.75rem;
        }
        
        .esv-info-value {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
    }
    
    /* Enhanced PDF file styles */
    .esv-file-card.pdf-file .esv-file-icon {
        color: #e74c3c !important;
        filter: drop-shadow(0 2px 4px rgba(231, 76, 60, 0.2));
    }
    
    .esv-file-card.pdf-file .esv-file-name {
        color: #c0392b !important;
        font-weight: 700 !important;
    }
    
    .esv-pdf-badge {
        transition: all 0.3s ease !important;
    }
    
    /* Animation for PDF badge */
    @keyframes pulse-pdf {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .esv-file-card.pdf-file:hover .esv-pdf-badge {
        animation: pulse-pdf 2s infinite;
    }
`;
document.head.appendChild(style);

console.log("📄 ESV Document Detail System loaded successfully");
console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
console.log("📊 Detail Status: Ready");
console.log("📋 Reference ID: " + EsvDetailConfig.referenceId);
console.log("🎯 Enhanced Features: PDF Viewer, Image Preview, Auto-refresh, Keyboard Shortcuts");

</script>