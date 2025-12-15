<?php
// Helper function สำหรับ CSS class ของสถานะ ESV
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

// Helper function สำหรับแสดงประเภทผู้ใช้
if (!function_exists('get_esv_ods_user_type_display')) {
    function get_esv_ods_user_type_display($type) {
        switch($type) {
            case 'guest': return 'ผู้ใช้ทั่วไป';
            case 'public': return 'สมาชิก';
            case 'staff': return 'เจ้าหน้าที่';
            default: return 'ผู้ใช้ทั่วไป';
        }
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== ESV ODS SPECIFIC STYLES (ไม่ทับ header) ===== */
/* เพิ่ม namespace เฉพาะ esv-ods เพื่อไม่ให้ทับกับ styles อื่น */

/* ===== ROOT VARIABLES FOR ESV ODS ===== */
.esv-ods-page {
    --esv-primary-color: #8b9cc7;
    --esv-primary-light: #a5b4d0;
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
    --esv-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --esv-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --esv-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --esv-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --esv-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    --esv-border-radius: 12px;
    --esv-border-radius-lg: 16px;
    --esv-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ===== GLOBAL STYLES สำหรับ ESV ODS ===== */
.esv-ods-page {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--esv-gray-700);
    min-height: 100vh;
}

.esv-ods-page .esv-container-fluid {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    min-height: calc(100vh - 140px);
}

/* ===== PAGE HEADER สำหรับ ESV (แก้ไขสีให้อ่อนลง) ===== */
.esv-ods-page .esv-page-header {
    background: linear-gradient(135deg, var(--esv-primary-color) 0%, var(--esv-primary-light) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--esv-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--esv-shadow-md);
    position: relative;
    overflow: hidden;
    margin-top: 1rem;
    display: flex; /* เพิ่ม */
    justify-content: space-between; /* เพิ่ม */
    align-items: center; /* เพิ่ม */
}

.esv-ods-page .esv-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.esv-ods-page .esv-page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
    color: #ffffff !important;
}

/* ===== HEADER ACTIONS ===== */
.esv-ods-page .esv-header-actions {
    position: static !important; /* เปลี่ยนจาก absolute */
    top: auto !important;
    right: auto !important;
    z-index: 2;
    display: flex;
    gap: 0.75rem;
    margin-top: 0 !important;
    flex-direction: row !important;
    align-items: center !important;
}

.esv-ods-page .esv-action-btn {
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
    transition: var(--esv-transition);
    backdrop-filter: blur(10px);
}

.esv-ods-page .esv-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ===== STATISTICS CARDS สำหรับ ESV (เพิ่มกล่องยกเลิก) ===== */
.esv-ods-page .esv-stats-section {
    margin-bottom: 2rem;
}

.esv-ods-page .esv-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.esv-ods-page .esv-stat-card {
    background: var(--esv-white);
    border-radius: var(--esv-border-radius);
    padding: 1.5rem;
    box-shadow: var(--esv-shadow-md);
    position: relative;
    overflow: hidden;
    transition: var(--esv-transition);
    border: 1px solid var(--esv-gray-100);
}

.esv-ods-page .esv-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--esv-shadow-lg);
}

.esv-ods-page .esv-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--esv-primary-color), var(--esv-primary-light));
}

.esv-ods-page .esv-stat-card.pending::before { 
    background: linear-gradient(90deg, var(--esv-warning-color), #fbbf24); 
}
.esv-ods-page .esv-stat-card.processing::before { 
    background: linear-gradient(90deg, var(--esv-info-color), #60a5fa); 
}
.esv-ods-page .esv-stat-card.completed::before { 
    background: linear-gradient(90deg, var(--esv-success-color), #34d399); 
}
.esv-ods-page .esv-stat-card.rejected::before { 
    background: linear-gradient(90deg, var(--esv-danger-color), #f87171); 
}
.esv-ods-page .esv-stat-card.cancelled::before { 
    background: linear-gradient(90deg, var(--esv-gray-500), var(--esv-gray-400)); 
}

.esv-ods-page .esv-stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.esv-ods-page .esv-stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    margin-right: 1rem;
}

.esv-ods-page .esv-stat-icon.total { 
    background: linear-gradient(135deg, var(--esv-primary-color), var(--esv-primary-light)); 
}
.esv-ods-page .esv-stat-icon.pending { 
    background: linear-gradient(135deg, var(--esv-warning-color), #fbbf24); 
}
.esv-ods-page .esv-stat-icon.processing { 
    background: linear-gradient(135deg, var(--esv-info-color), #60a5fa); 
}
.esv-ods-page .esv-stat-icon.completed { 
    background: linear-gradient(135deg, var(--esv-success-color), #34d399); 
}
.esv-ods-page .esv-stat-icon.rejected { 
    background: linear-gradient(135deg, var(--esv-danger-color), #f87171); 
}
.esv-ods-page .esv-stat-icon.cancelled { 
    background: linear-gradient(135deg, var(--esv-gray-500), var(--esv-gray-400)); 
}

.esv-ods-page .esv-stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--esv-gray-800);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.esv-ods-page .esv-stat-label {
    color: var(--esv-gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

/* ===== FILTER SECTION สำหรับ ESV ===== */
.esv-ods-page .esv-filter-section {
    margin-bottom: 2rem;
}

.esv-ods-page .esv-filter-card {
    background: var(--esv-white);
    border-radius: var(--esv-border-radius);
    padding: 2rem;
    box-shadow: var(--esv-shadow-md);
    border: 1px solid var(--esv-gray-100);
}

.esv-ods-page .esv-filter-card h5 {
    color: var(--esv-gray-900);
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.esv-ods-page .esv-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.esv-ods-page .esv-form-group {
    display: flex;
    flex-direction: column;
}

.esv-ods-page .esv-form-label {
    font-weight: 600;
    color: var(--esv-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.esv-ods-page .esv-form-select, 
.esv-ods-page .esv-form-control {
    border: 2px solid var(--esv-gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--esv-transition);
    background-color: var(--esv-white);
}

.esv-ods-page .esv-form-select:focus, 
.esv-ods-page .esv-form-control:focus {
    border-color: var(--esv-primary-color);
    box-shadow: 0 0 0 3px rgba(139, 156, 199, 0.1);
    outline: none;
}

.esv-ods-page .esv-filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.esv-ods-page .esv-btn {
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

.esv-ods-page .esv-btn-primary {
    background: linear-gradient(135deg, var(--esv-primary-color), var(--esv-primary-light));
    color: white;
}

.esv-ods-page .esv-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

.esv-ods-page .esv-btn-secondary {
    background: var(--esv-gray-100);
    color: var(--esv-gray-700);
}

.esv-ods-page .esv-btn-secondary:hover {
    background: var(--esv-gray-200);
    color: var(--esv-gray-800);
}

.esv-ods-page .esv-btn-success {
    background: linear-gradient(135deg, var(--esv-success-color), #34d399);
    color: white;
}

.esv-ods-page .esv-btn-success:hover {
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-lg);
    color: white;
}

/* ===== DATA TABLE SECTION สำหรับ ESV ===== */
.esv-ods-page .esv-table-section {
    margin-bottom: 2rem;
}

.esv-ods-page .esv-table-card {
    background: var(--esv-white);
    border-radius: var(--esv-border-radius);
    overflow: hidden;
    box-shadow: var(--esv-shadow-md);
    border: 1px solid var(--esv-gray-100);
}

.esv-ods-page .esv-table-header {
    background: var(--esv-gray-50);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--esv-gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.esv-ods-page .esv-table-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--esv-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ===== ESV CARDS ===== */
.esv-ods-page .esv-ods-container {
    background: var(--esv-white);
    border: 2px solid var(--esv-gray-100);
    border-radius: var(--esv-border-radius);
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: var(--esv-shadow-md);
    transition: var(--esv-transition);
}

.esv-ods-page .esv-ods-container:hover {
    border-color: var(--esv-primary-light);
    box-shadow: var(--esv-shadow-lg);
    transform: translateY(-2px);
}

.esv-ods-page .esv-ods-header {
    background: linear-gradient(135deg, var(--esv-secondary-color) 0%, #dbeafe 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--esv-gray-200);
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--esv-primary-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.esv-ods-page .esv-ods-number {
    background: linear-gradient(135deg, var(--esv-primary-color), var(--esv-primary-light));
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-left: auto;
}

.esv-ods-page .esv-ods-data-row {
    background: var(--esv-white);
    border-bottom: 1px solid var(--esv-gray-100);
    transition: var(--esv-transition);
}

.esv-ods-page .esv-ods-data-row:hover {
    background: var(--esv-gray-50);
}

.esv-ods-page .esv-ods-status-row {
    background: var(--esv-gray-50);
    border-left: 4px solid var(--esv-primary-color);
    border-bottom: none;
}

.esv-ods-page .esv-table {
    margin: 0;
}

.esv-ods-page .esv-table tbody td {
    padding: 1.25rem 1rem;
    border-color: var(--esv-gray-100);
    vertical-align: middle;
    font-size: 0.875rem;
}

/* ===== STATUS BADGES สำหรับ ESV ===== */
.esv-ods-page .esv-status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    min-width: 120px;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.esv-ods-page .esv-status-badge.pending {
    background: linear-gradient(135deg, rgba(254, 243, 199, 0.8), rgba(253, 230, 138, 0.6));
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.esv-ods-page .esv-status-badge.processing {
    background: linear-gradient(135deg, rgba(219, 234, 254, 0.8), rgba(191, 219, 254, 0.6));
    color: #1d4ed8;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.esv-ods-page .esv-status-badge.completed {
    background: linear-gradient(135deg, rgba(209, 250, 229, 0.8), rgba(167, 243, 208, 0.6));
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.esv-ods-page .esv-status-badge.rejected {
    background: linear-gradient(135deg, rgba(254, 226, 226, 0.8), rgba(252, 165, 165, 0.6));
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.esv-ods-page .esv-status-badge.cancelled {
    background: linear-gradient(135deg, rgba(243, 244, 246, 0.8), rgba(229, 231, 235, 0.6));
    color: #6b7280;
    border: 1px solid rgba(156, 163, 175, 0.3);
}

/* ===== OTHER BADGES สำหรับ ESV ===== */
.esv-ods-page .esv-priority-badge, 
.esv-ods-page .esv-user-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.esv-ods-page .esv-priority-badge.normal {
    background: var(--esv-gray-100);
    color: var(--esv-gray-700);
}

.esv-ods-page .esv-priority-badge.urgent {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.esv-ods-page .esv-priority-badge.very_urgent {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.esv-ods-page .esv-user-type-badge.guest {
    background: var(--esv-gray-100);
    color: var(--esv-gray-600);
}

.esv-ods-page .esv-user-type-badge.public {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.esv-ods-page .esv-user-type-badge.staff {
    background: linear-gradient(135deg, var(--esv-secondary-color), #dbeafe);
    color: var(--esv-primary-color);
}

/* ===== ACTION BUTTONS สำหรับ ESV ===== */
.esv-ods-page .esv-action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.esv-ods-page .esv-btn-action {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--esv-transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 80px;
    justify-content: center;
    white-space: nowrap;
}

.esv-ods-page .esv-btn-action.view {
    background: linear-gradient(135deg, var(--esv-info-color), #60a5fa);
    color: white;
}

.esv-ods-page .esv-btn-action.view:hover {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-md);
    color: white;
}

.esv-ods-page .esv-btn-action.delete {
    background: linear-gradient(135deg, var(--esv-danger-color), #f87171);
    color: white;
}

.esv-ods-page .esv-btn-action.delete:hover {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-md);
    color: white;
}

/* ===== STATUS UPDATE BUTTONS สำหรับ ESV ===== */
.esv-ods-page .esv-status-cell {
    padding: 1.5rem !important;
    border-top: 1px solid var(--esv-gray-200) !important;
}

.esv-ods-page .esv-status-update-row {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.esv-ods-page .esv-status-label {
    font-weight: 600;
    color: var(--esv-gray-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.esv-ods-page .esv-status-buttons-container {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.esv-ods-page .esv-btn-status-row {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 2px solid transparent;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--esv-transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 130px;
    justify-content: center;
    white-space: nowrap;
    text-align: center;
    height: 40px;
}

.esv-ods-page .esv-btn-status-row.pending {
    background: linear-gradient(135deg, rgba(254, 243, 199, 0.8), rgba(253, 230, 138, 0.6));
    color: #d97706;
    border-color: rgba(245, 158, 11, 0.3);
}

.esv-ods-page .esv-btn-status-row.pending:hover:not(:disabled) {
    background: rgba(245, 158, 11, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-md);
}

.esv-ods-page .esv-btn-status-row.processing {
    background: linear-gradient(135deg, rgba(219, 234, 254, 0.8), rgba(191, 219, 254, 0.6));
    color: #1d4ed8;
    border-color: rgba(59, 130, 246, 0.3);
}

.esv-ods-page .esv-btn-status-row.processing:hover:not(:disabled) {
    background: rgba(59, 130, 246, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-md);
}

.esv-ods-page .esv-btn-status-row.completed {
    background: linear-gradient(135deg, rgba(209, 250, 229, 0.8), rgba(167, 243, 208, 0.6));
    color: #059669;
    border-color: rgba(16, 185, 129, 0.3);
}

.esv-ods-page .esv-btn-status-row.completed:hover:not(:disabled) {
    background: rgba(16, 185, 129, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-md);
}

.esv-ods-page .esv-btn-status-row.rejected {
    background: linear-gradient(135deg, rgba(254, 226, 226, 0.8), rgba(252, 165, 165, 0.6));
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.3);
}

.esv-ods-page .esv-btn-status-row.rejected:hover:not(:disabled) {
    background: rgba(239, 68, 68, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-md);
}

.esv-ods-page .esv-btn-status-row.cancelled {
    background: linear-gradient(135deg, rgba(243, 244, 246, 0.8), rgba(229, 231, 235, 0.6));
    color: #6b7280;
    border-color: rgba(156, 163, 175, 0.3);
}

.esv-ods-page .esv-btn-status-row.cancelled:hover:not(:disabled) {
    background: rgba(107, 114, 128, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-md);
}

.esv-ods-page .esv-btn-status-row.current {
    background: var(--esv-gray-100);
    color: var(--esv-gray-600);
    cursor: not-allowed;
    opacity: 0.8;
    border-color: var(--esv-gray-300);
}

.esv-ods-page .esv-btn-status-row.current::before {
    content: "✓ ";
    font-weight: bold;
}

/* ===== เพิ่ม CSS สำหรับปุ่ม Disabled ===== */
.esv-ods-page .esv-btn-status-row:disabled,
.esv-ods-page .esv-btn-status-row.disabled {
    background: var(--esv-gray-100) !important;
    color: var(--esv-gray-400) !important;
    border-color: var(--esv-gray-200) !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    transform: none !important;
    box-shadow: none !important;
}

/* ===== FILE DISPLAY สำหรับ ESV ===== */
.esv-ods-page .esv-files-display {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.esv-ods-page .esv-file-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    background: var(--esv-gray-50);
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    color: var(--esv-gray-600);
    border: 1px solid var(--esv-gray-200);
    transition: var(--esv-transition);
    cursor: pointer;
}

.esv-ods-page .esv-file-item:hover {
    background: var(--esv-gray-100);
    border-color: var(--esv-primary-color);
    transform: translateY(-1px);
    box-shadow: var(--esv-shadow-sm);
}

.esv-ods-page .esv-file-item i {
    color: var(--esv-primary-color);
    font-size: 0.875rem;
}

/* ===== PERSONAL INFO สำหรับ ESV ===== */
.esv-ods-page .esv-personal-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.esv-ods-page .esv-personal-info-item {
    font-size: 0.8rem;
    color: var(--esv-gray-600);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* ===== การปรับปรุง Display Elements ===== */
.esv-ods-page .esv-id-display {
    font-size: 1.1rem;
    color: var(--esv-primary-color);
    background: var(--esv-gray-50);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    border: 1px solid var(--esv-gray-200);
}

.esv-ods-page .esv-date-display {
    text-align: center;
}

.esv-ods-page .esv-date-part {
    font-weight: 600;
    color: var(--esv-gray-800);
    font-size: 0.9rem;
    margin-bottom: 0.2rem;
}

.esv-ods-page .esv-time-part {
    color: var(--esv-gray-600);
    font-size: 0.8rem;
}

.esv-ods-page .esv-name-display {
    font-size: 0.95rem;
    color: var(--esv-gray-900);
    margin-bottom: 0.3rem;
}

.esv-ods-page .esv-phone-display {
    color: var(--esv-gray-600);
    font-size: 0.8rem;
}

/* ===== PAGINATION สำหรับ ESV ===== */
.esv-ods-page .esv-pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--esv-gray-200);
    background: var(--esv-gray-50);
}

.esv-ods-page .esv-pagination-info {
    color: var(--esv-gray-600);
    font-size: 0.875rem;
}

/* ===== EMPTY STATE สำหรับ ESV ===== */
.esv-ods-page .esv-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--esv-gray-500);
}

.esv-ods-page .esv-empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.esv-ods-page .esv-empty-state h5 {
    color: var(--esv-gray-600);
    margin-bottom: 0.5rem;
}

/* ===== RESPONSIVE DESIGN สำหรับ ESV ===== */
@media (max-width: 768px) {
    .esv-ods-page .esv-container-fluid {
        padding: 1rem;
        min-height: calc(100vh - 120px);
    }
    
    .esv-ods-page .esv-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
        margin-top: 0.5rem;
    }
    
    .esv-ods-page .esv-page-header h1 {
        font-size: 1.5rem;
    }
    
    .esv-ods-page .esv-header-actions {
        position: relative;
        top: auto;
        right: auto;
        margin-top: 1rem;
        flex-direction: column;
        align-items: stretch;
    }
    
    .esv-ods-page .esv-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .esv-ods-page .esv-filter-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .esv-ods-page .esv-filter-actions {
        justify-content: stretch;
    }
    
    .esv-ods-page .esv-filter-actions .esv-btn {
        flex: 1;
    }
}

@media (max-width: 480px) {
    .esv-ods-page .esv-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>




<style>
/* แก้ไข dropdown z-index แบบ force */
.esv-ods-page .btn-group .dropdown-menu,
.esv-ods-page .dropdown-menu {
    z-index: 9999 !important;
    position: absolute !important;
    transform: none !important;
    will-change: auto !important;
}

/* แก้ไข overflow ของ parent containers */
.esv-ods-page .esv-page-header,
.esv-ods-page .esv-header-actions,
.esv-ods-page .btn-group {
    overflow: visible !important;
    position: relative !important;
    z-index: 1000 !important;
}

/* แก้ไข Bootstrap dropdown default styles */
.dropdown-menu.show {
    z-index: 9999 !important;
    display: block !important;
}

/* ป้องกัน elements อื่นทับ dropdown */
.esv-ods-page .esv-stats-section,
.esv-ods-page .esv-filter-section,
.esv-ods-page .esv-table-section {
    position: relative;
    z-index: 1;
}

.esv-ods-page .esv-stat-card,
.esv-ods-page .esv-filter-card,
.esv-ods-page .esv-table-card {
    position: relative;
    z-index: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .esv-ods-page .btn-group {
        position: static !important;
    }
    
    .esv-ods-page .dropdown-menu {
        position: absolute !important;
        z-index: 9999 !important;
    }
}
	
	
	@media (max-width: 768px) {
    .esv-ods-page .esv-page-header {
        flex-direction: column; /* เปลี่ยนเป็น column */
        align-items: flex-start; /* จัดซ้าย */
        gap: 1rem; /* เพิ่มระยะห่าง */
    }
    
    .esv-ods-page .esv-header-actions {
        align-self: stretch; /* ให้ยืดเต็มความกว้าง */
        flex-direction: column;
    }
}
</style>


<div class="esv-ods-page">
    <div class="esv-container-fluid">
        <!-- ===== PAGE HEADER ===== -->
        <header class="esv-page-header">
            <h1><i class="fas fa-file-alt me-3"></i>จัดการเอกสารออนไลน์</h1>
            
            <!-- Header Actions -->
            <!-- Header Actions -->
<div class="esv-header-actions">
    <!-- ปุ่มจัดการประเภทและหมวดหมู่เอกสาร -->
    <div class="dropdown me-2">
        <button class="esv-action-btn dropdown-toggle" type="button" id="documentManagementDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog"></i>
            <span>จัดการข้อมูลเอกสาร</span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="documentManagementDropdown">
            <li>
                <a class="dropdown-item" href="<?= site_url('Esv_ods/manage_document_types') ?>">
                    <i class="fas fa-file-alt me-2"></i>จัดการประเภทเอกสาร
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= site_url('Esv_ods/manage_categories') ?>">
                    <i class="fas fa-tags me-2"></i>จัดการหมวดหมู่เอกสาร
                </a>
            </li>
            <!-- เพิ่มเมนูจัดการแบบฟอร์ม -->
            <li>
                <a class="dropdown-item" href="<?= site_url('Esv_ods/manage_forms') ?>">
                    <i class="fas fas fa-file me-2"></i>จัดการแบบฟอร์ม
                </a>
            </li>
        </ul>
    </div>
    
    <!-- ปุ่มส่งออก Excel (เดิม) -->
    <a href="<?= site_url('Esv_ods/export_excel?' . http_build_query($filters ?? [])) ?>" class="esv-action-btn" title="ส่งออก Excel">
        <i class="fas fa-download"></i>
        <span>ส่งออก Excel</span>
    </a>
</div>
			
			
			
        </header>

        <!-- Flash Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ===== STATISTICS SECTION (เพิ่มกล่องยกเลิก) ===== -->
        <section class="esv-stats-section">
            <div class="esv-stats-grid">
                <div class="esv-stat-card total">
                    <div class="esv-stat-header">
                        <div class="esv-stat-icon total">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="esv-stat-value"><?= number_format($status_counts['total'] ?? 0) ?></div>
                    <div class="esv-stat-label">ทั้งหมด</div>
                </div>

                <div class="esv-stat-card pending">
                    <div class="esv-stat-header">
                        <div class="esv-stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="esv-stat-value"><?= number_format($status_counts['pending'] ?? 0) ?></div>
                    <div class="esv-stat-label">รอดำเนินการ</div>
                </div>

                <div class="esv-stat-card processing">
                    <div class="esv-stat-header">
                        <div class="esv-stat-icon processing">
                            <i class="fas fa-cog"></i>
                        </div>
                    </div>
                    <div class="esv-stat-value"><?= number_format($status_counts['processing'] ?? 0) ?></div>
                    <div class="esv-stat-label">กำลังดำเนินการ</div>
                </div>

                <div class="esv-stat-card completed">
                    <div class="esv-stat-header">
                        <div class="esv-stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="esv-stat-value"><?= number_format($status_counts['completed'] ?? 0) ?></div>
                    <div class="esv-stat-label">เสร็จสิ้น</div>
                </div>

                <div class="esv-stat-card rejected">
                    <div class="esv-stat-header">
                        <div class="esv-stat-icon rejected">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="esv-stat-value"><?= number_format($status_counts['rejected'] ?? 0) ?></div>
                    <div class="esv-stat-label">ไม่อนุมัติ</div>
                </div>

                <!-- เพิ่มกล่องสถิติ "ยกเลิก" -->
                <div class="esv-stat-card cancelled">
                    <div class="esv-stat-header">
                        <div class="esv-stat-icon cancelled">
                            <i class="fas fa-ban"></i>
                        </div>
                    </div>
                    <div class="esv-stat-value"><?= number_format($status_counts['cancelled'] ?? 0) ?></div>
                    <div class="esv-stat-label">ยกเลิก</div>
                </div>
            </div>
        </section>

        <!-- ===== FILTER SECTION ===== -->
        <section class="esv-filter-section">
            <div class="esv-filter-card">
                <h5><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
                <form method="GET" action="<?= site_url('Esv_ods/admin_management') ?>" id="filterForm">
                    <div class="esv-filter-grid">
                        <div class="esv-form-group">
                            <label class="esv-form-label">สถานะ:</label>
                            <select name="status" class="esv-form-select">
                                <option value="">-- ทุกสถานะ --</option>
                                <?php if (isset($status_options) && is_array($status_options)): ?>
                                    <?php foreach ($status_options as $option): ?>
                                        <option value="<?= $option['value'] ?>" 
                                                <?= ($filters['status'] ?? '') === $option['value'] ? 'selected' : '' ?>>
                                            <?= $option['label'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="esv-form-group">
                            <label class="esv-form-label">แผนก:</label>
                            <select name="department" class="esv-form-select">
                                <option value="">-- ทุกแผนก --</option>
                                <?php if (isset($departments) && is_array($departments)): ?>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept->pid ?>" 
                                                <?= ($filters['department'] ?? '') == $dept->pid ? 'selected' : '' ?>>
                                            <?= $dept->pname ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="esv-form-group">
                            <label class="esv-form-label">ประเภทผู้ใช้:</label>
                            <select name="user_type" class="esv-form-select">
                                <option value="">-- ทุกประเภท --</option>
                                <?php if (isset($user_type_options) && is_array($user_type_options)): ?>
                                    <?php foreach ($user_type_options as $option): ?>
                                        <option value="<?= $option['value'] ?>" 
                                                <?= ($filters['user_type'] ?? '') === $option['value'] ? 'selected' : '' ?>>
                                            <?= $option['label'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="esv-form-group">
                            <label class="esv-form-label">ค้นหา:</label>
                            <input type="text" name="search" class="esv-form-control" 
                                   placeholder="หมายเลขอ้างอิง, ชื่อ, เรื่อง..."
                                   value="<?= $filters['search'] ?? '' ?>">
                        </div>

                        <div class="esv-form-group">
                            <label class="esv-form-label">จากวันที่:</label>
                            <input type="date" name="date_from" class="esv-form-control" 
                                   value="<?= $filters['date_from'] ?? '' ?>">
                        </div>

                        <div class="esv-form-group">
                            <label class="esv-form-label">ถึงวันที่:</label>
                            <input type="date" name="date_to" class="esv-form-control" 
                                   value="<?= $filters['date_to'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="esv-filter-actions">
                        <button type="submit" class="esv-btn esv-btn-primary">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <a href="<?= site_url('Esv_ods/admin_management') ?>" class="esv-btn esv-btn-secondary">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </a>
                        <a href="<?= site_url('Esv_ods/export_excel') ?>" class="esv-btn esv-btn-success">
                            <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- ===== DATA TABLE SECTION ===== -->
        <section class="esv-table-section">
            <div class="esv-table-card">
                <div class="esv-table-header">
                    <h5 class="esv-table-title">
                        <i class="fas fa-list me-2"></i>รายการเอกสารออนไลน์
                        <span class="badge bg-info text-white ms-2">
                            <?= number_format($total_rows ?? 0) ?> รายการ
                        </span>
                    </h5>
                </div>
                
                <div class="esv-table-content">
                    <?php if (empty($documents)): ?>
                        <div class="esv-empty-state">
                            <i class="fas fa-file-alt"></i>
                            <h5>ไม่พบข้อมูลเอกสาร</h5>
                            <p>กรุณาลองใช้ตัวกรองอื่น หรือเพิ่มข้อมูลใหม่</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="esv-ods-container" data-doc-id="<?= $doc->esv_ods_reference_id ?>">
                                <!-- ESV ODS Header -->
                                <div class="esv-ods-header">
                                    <i class="fas fa-file-alt"></i>
                                    <span>เอกสารออนไลน์</span>
                                    <span class="esv-ods-number"><?= $doc->esv_ods_reference_id ?></span>
                                </div>
                                
                                <!-- ESV ODS Content -->
                                <table class="esv-table mb-0">
                                    <tbody>
                                        <!-- ESV ODS Data Row -->
                                        <tr class="esv-ods-data-row">
                                            <td style="width: 10%;">
                                                <div class="text-center">
                                                    <strong class="esv-id-display"><?= $doc->esv_ods_reference_id ?></strong>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="esv-date-display">
                                                    <?php 
                                                    $thai_months = [
                                                        '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                        '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                        '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                                    ];
                                                    
                                                    $datesave = $doc->esv_ods_datesave;
                                                    $date = date('j', strtotime($datesave));
                                                    $month = $thai_months[date('m', strtotime($datesave))];
                                                    $year = date('Y', strtotime($datesave)) + 543;
                                                    $time = date('H:i', strtotime($datesave));
                                                    ?>
                                                    <div class="esv-date-part"><?= $date ?> <?= $month ?> <?= $year ?></div>
                                                    <div class="esv-time-part"><?= $time ?> น.</div>
                                                </div>
                                            </td>
                                            <td style="width: 15%;">
                                                <div class="text-center">
                                                    <span class="esv-status-badge <?= get_esv_ods_status_class($doc->esv_ods_status) ?>">
                                                        <?= get_esv_ods_status_display($doc->esv_ods_status) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 8%;">
                                                <div class="text-center">
                                                    <span class="esv-priority-badge <?= $doc->esv_ods_priority ?? 'normal' ?>">
                                                        <?php
                                                        $priority_labels = [
                                                            'normal' => 'ปกติ', 
                                                            'urgent' => 'เร่งด่วน',
                                                            'very_urgent' => 'เร่งด่วนมาก'
                                                        ];
                                                        echo $priority_labels[$doc->esv_ods_priority ?? 'normal'];
                                                        ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 25%;">
                                                <div>
                                                    <div class="fw-bold text-truncate mb-1" style="max-width: 280px;" 
                                                         title="<?= htmlspecialchars($doc->esv_ods_topic) ?>">
                                                        <?= htmlspecialchars($doc->esv_ods_topic) ?>
                                                    </div>
                                                    <?php if (!empty($doc->esv_ods_detail)): ?>
                                                        <small class="text-muted text-truncate d-block" style="max-width: 280px;">
                                                            <?= htmlspecialchars(mb_substr($doc->esv_ods_detail, 0, 80)) ?>
                                                            <?= mb_strlen($doc->esv_ods_detail) > 80 ? '...' : '' ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="esv-files-display">
                                                    <?php if (!empty($doc->files) && is_array($doc->files)): ?>
                                                        <?php 
                                                        $displayFiles = array_slice($doc->files, 0, 2);
                                                        $remainingCount = count($doc->files) - count($displayFiles);
                                                        ?>
                                                        
                                                        <?php foreach ($displayFiles as $file): ?>
                                                            <div class="esv-file-item" 
                                                                 onclick="downloadFile('<?= site_url('docs/esv_files/' . $file->esv_file_name) ?>', '<?= htmlspecialchars($file->esv_file_original_name, ENT_QUOTES) ?>')"
                                                                 title="<?= htmlspecialchars($file->esv_file_original_name) ?>">
                                                                <i class="fas fa-file"></i>
                                                                <span class="file-name"><?= mb_substr($file->esv_file_original_name, 0, 8) ?><?= mb_strlen($file->esv_file_original_name) > 8 ? '...' : '' ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        
                                                        <?php if ($remainingCount > 0): ?>
                                                            <span class="badge bg-secondary">+<?= $remainingCount ?></span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <div class="text-center">
                                                            <span class="text-muted small">ไม่มีไฟล์</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="width: 15%;">
                                                <div class="esv-personal-info">
                                                    <div class="esv-personal-info-item esv-name-display">
                                                        <strong><?= htmlspecialchars($doc->esv_ods_by) ?></strong>
                                                    </div>
                                                    <?php if (!empty($doc->esv_ods_phone)): ?>
                                                        <div class="esv-personal-info-item esv-phone-display">
                                                            <i class="fas fa-phone me-1"></i> 
                                                            <?= htmlspecialchars($doc->esv_ods_phone) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($doc->esv_ods_email)): ?>
                                                        <div class="esv-personal-info-item">
                                                            <i class="fas fa-envelope me-1"></i> 
                                                            <?= htmlspecialchars(mb_substr($doc->esv_ods_email, 0, 20)) ?><?= mb_strlen($doc->esv_ods_email) > 20 ? '...' : '' ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="width: 8%;">
                                                <div class="text-center">
                                                    <span class="esv-user-type-badge <?= $doc->esv_ods_user_type ?? 'guest' ?>">
                                                        <?= get_esv_ods_user_type_display($doc->esv_ods_user_type ?? 'guest') ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="esv-action-buttons">
                                                    <a href="<?= site_url('Esv_ods/document_detail/' . $doc->esv_ods_reference_id) ?>" 
                                                       class="esv-btn-action view" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>ดู
                                                    </a>
                                                    
                                                    <?php if ($can_delete_document ?? false): ?>
                                                        <button type="button" 
                                                                class="esv-btn-action delete" 
                                                                onclick="confirmDeleteDocument('<?= $doc->esv_ods_reference_id ?>', '<?= htmlspecialchars($doc->esv_ods_topic, ENT_QUOTES) ?>')"
                                                                title="ลบเอกสาร (สำหรับ Admin เท่านั้น)">
                                                            <i class="fas fa-trash"></i>ลบ
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- ESV ODS Status Management Row -->
                                        <tr class="esv-ods-status-row">
                                            <td colspan="9" class="esv-status-cell">
                                                <div class="esv-status-update-row">
                                                    <div class="esv-status-label">
                                                        <i class="fas fa-sync-alt"></i>
                                                        อัปเดตสถานะ <?= $doc->esv_ods_reference_id ?>
                                                    </div>
                                                    <div class="esv-status-buttons-container">
                                                        <?php 
                                                        $current_status = $doc->esv_ods_status;
                                                        $can_handle = $can_update_status ?? false;

                                                        // กำหนดสถานะที่สามารถเปลี่ยนได้ตามเงื่อนไข
                                                        switch($current_status) {
                                                            case 'pending': // รอดำเนินการ
                                                                $available_statuses = ['processing', 'cancelled']; // เพิ่ม cancelled
                                                                break;
                                                            case 'processing': // กำลังดำเนินการ
                                                                $available_statuses = ['completed', 'rejected', 'cancelled']; // เพิ่ม cancelled
                                                                break;
                                                            case 'completed': // เสร็จสิ้น
                                                                $available_statuses = []; // ✨ เสร็จสิ้นแล้วไม่ให้เปลี่ยนแปลงอะไรได้
                                                                break;
                                                            case 'rejected': // ไม่อนุมัติ
                                                                $available_statuses = ['cancelled']; // ให้ยกเลิกได้
                                                                break;
                                                            case 'cancelled': // ยกเลิก
                                                                $available_statuses = []; // ยกเลิกแล้วไม่สามารถเปลี่ยนได้
                                                                break;
                                                        }
                                                        
                                                        $all_status_buttons = [
                                                            'pending' => ['pending', 'fas fa-clock', 'รอดำเนินการ'],
                                                            'processing' => ['processing', 'fas fa-cog', 'กำลังดำเนินการ'],
                                                            'completed' => ['completed', 'fas fa-check-circle', 'เสร็จสิ้น'],
                                                            'rejected' => ['rejected', 'fas fa-times-circle', 'ไม่อนุมัติ'],
                                                            'cancelled' => ['cancelled', 'fas fa-ban', 'ยกเลิก']
                                                        ];
                                                        
                                                        foreach ($all_status_buttons as $status_key => $status_info): 
                                                            $status_class = $status_info[0];
                                                            $status_icon = $status_info[1];
                                                            $status_display = $status_info[2];
                                                            
                                                            $is_current = ($current_status === $status_key);
                                                            $is_available = in_array($status_key, $available_statuses);
                                                            $is_clickable = ($can_update_status && $is_available);

                                                            $button_classes = "esv-btn-status-row {$status_class}";
                                                            if ($is_current) {
                                                                $button_classes .= ' current';
                                                            }
                                                            
                                                            $tooltip_text = '';
                                                            if ($is_current) {
                                                                $tooltip_text = 'สถานะปัจจุบัน';
                                                            } elseif (!$can_update_status) {
                                                                $tooltip_text = 'คุณไม่มีสิทธิ์เปลี่ยนสถานะ';
                                                            } elseif ($is_available) {
                                                                $tooltip_text = 'คลิกเพื่อเปลี่ยนเป็น ' . $status_display;
                                                            } else {
                                                                $tooltip_text = 'ไม่สามารถเปลี่ยนเป็นสถานะนี้ได้ในขณะนี้';
                                                            }
                                                            
                                                            $onclick_code = '';
                                                            if ($is_clickable) {
                                                                $doc_ref_js = htmlspecialchars($doc->esv_ods_reference_id, ENT_QUOTES);
                                                                $by_js = htmlspecialchars($doc->esv_ods_by, ENT_QUOTES);
                                                                $onclick_code = "onclick=\"updateDocumentStatusDirect('{$doc_ref_js}', '{$status_key}', '{$by_js}', '{$status_display}')\"";
                                                            }
                                                        ?>
                                                            <button class="<?= $button_classes ?>"
                                                                    <?= (!$is_clickable) ? 'disabled' : '' ?>
                                                                    <?= $onclick_code ?>
                                                                    title="<?= $tooltip_text ?>">
                                                                <i class="<?= $status_icon ?>"></i>
                                                                <span><?= $status_display ?></span>
                                                            </button>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination) && !empty($pagination)): ?>
                    <div class="esv-pagination-container">
                        <div class="esv-pagination-info">
                            แสดง <?= number_format((($current_page ?? 1) - 1) * ($per_page ?? 20) + 1) ?> - 
                            <?= number_format(min(($current_page ?? 1) * ($per_page ?? 20), $total_rows ?? 0)) ?> 
                            จาก <?= number_format($total_rows ?? 0) ?> รายการ
                        </div>
                        <div>
                            <?= $pagination ?? '' ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- ===== MODALS ===== -->

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-sync-alt me-2"></i>อัปเดตสถานะเอกสาร
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="statusDocumentRef" name="reference_id">
                    <input type="hidden" id="statusNewStatus" name="new_status">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ผู้ยื่นเรื่อง:</strong> <span id="statusDocumentBy"></span>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-arrow-right me-2"></i>
                        <strong>เปลี่ยนสถานะเป็น:</strong> <span id="statusDisplayText" class="fw-bold"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ความสำคัญ:</label>
                        <select class="form-select" id="statusNewPriority" name="new_priority">
                            <option value="normal">ปกติ</option>
                            <option value="urgent">เร่งด่วน</option>
                            <option value="very_urgent">เร่งด่วนมาก</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ (ถ้ามี):</label>
                        <textarea class="form-control" id="statusNote" name="note" rows="4"
                                  placeholder="หมายเหตุเพิ่มเติมสำหรับผู้ยื่นเรื่อง..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบเอกสาร
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-warning me-2"></i>
                    <strong>คำเตือน:</strong> การลบเอกสารนี้จะไม่สามารถกู้คืนได้! (Hard Delete)
                </div>
                
                <p><strong>การดำเนินการนี้จะลบ:</strong></p>
                <ul class="text-danger">
                    <li>ข้อมูลเอกสารทั้งหมด</li>
                    <li>ไฟล์แนบทั้งหมด</li>
                    <li>ประวัติการดำเนินการ</li>
                    <li>ข้อมูลที่เกี่ยวข้องทั้งหมด</li>
                </ul>
                
                <div class="bg-light p-3 rounded">
                    <strong>หมายเลขอ้างอิง:</strong> <span id="deleteDocumentRef"></span><br>
                    <strong>เรื่อง:</strong> <span id="deleteDocumentTitle"></span>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">เหตุผลในการลบ (บังคับ):</label>
                    <textarea class="form-control" id="deleteReason" rows="3" required
                              placeholder="ระบุเหตุผลในการลบเอกสารนี้ออกจากระบบ..."></textarea>
                    <!-- Error message จะแสดงที่นี่ -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>ลบเอกสาร
                </button>
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

const EsvOdsConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Esv_ods/update_document_status") ?>',
    deleteUrl: '<?= site_url("Esv_ods/delete_document") ?>',
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
 * อัปเดตสถานะเอกสารแบบตรงไปตรงมา
 */
function updateDocumentStatusDirect(documentRef, newStatus, documentBy, statusDisplay) {
   // console.log('updateDocumentStatusDirect called:', documentRef, newStatus, documentBy, statusDisplay);
    
    if (!documentRef || !newStatus) {
        console.error('Invalid parameters');
        showErrorAlert('ข้อมูลไม่ถูกต้อง');
        return;
    }
    
    // เตรียมข้อมูลสำหรับ Modal
    document.getElementById('statusDocumentRef').value = documentRef;
    document.getElementById('statusDocumentBy').textContent = documentBy || 'ไม่ระบุ';
    document.getElementById('statusNewStatus').value = newStatus;
    document.getElementById('statusDisplayText').textContent = statusDisplay;
    
    // แสดง Modal
    const statusModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    statusModal.show();
}

/**
 * ยืนยันการยกเลิกเอกสาร
 */
function confirmDeleteDocument(documentRef, documentTitle) {
    console.log('confirmDeleteDocument called:', documentRef, documentTitle);
    
    if (!documentRef) {
        showErrorAlert('ไม่พบหมายเลขอ้างอิงเอกสาร');
        return;
    }
    
    // ตั้งค่าข้อมูลใน Modal
    document.getElementById('deleteDocumentRef').textContent = documentRef;
    document.getElementById('deleteDocumentTitle').textContent = documentTitle || 'ไม่ระบุ';
    document.getElementById('deleteReason').value = '';
    
    // แสดง Modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
    
    // ตั้งค่า event handler สำหรับปุ่มยืนยัน
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    // ลบ event listener เก่า
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // เพิ่ม event listener ใหม่
    newConfirmBtn.addEventListener('click', function() {
        const deleteReason = document.getElementById('deleteReason').value.trim();
        
        // ✅ ตรวจสอบเหตุผลใน Modal โดยไม่เปลี่ยนเป็น SweetAlert
        if (!deleteReason) {
            // แสดง error ใน modal เลย
            const reasonTextarea = document.getElementById('deleteReason');
            const existingError = document.querySelector('.delete-reason-error');
            
            // ลบ error เก่า (ถ้ามี)
            if (existingError) {
                existingError.remove();
            }
            
            // เพิ่ม class error
            reasonTextarea.classList.add('is-invalid');
            
            // สร้าง error message ใน modal
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback delete-reason-error';
            errorDiv.textContent = 'กรุณาระบุเหตุผลในการลบ';
            reasonTextarea.parentNode.appendChild(errorDiv);
            
            // focus ไปที่ textarea
            reasonTextarea.focus();
            return;
        }
        
        // ลบ error class ถ้ามี
        const reasonTextarea = document.getElementById('deleteReason');
        reasonTextarea.classList.remove('is-invalid');
        const existingError = document.querySelector('.delete-reason-error');
        if (existingError) {
            existingError.remove();
        }
        
        performDeleteDocument(documentRef, deleteReason, deleteModal);
    });
}

/**
 * ดำเนินการลบเอกสาร (Hard Delete)
 */
function performDeleteDocument(documentRef, deleteReason, modal) {
    //console.log('performDeleteDocument called:', documentRef, deleteReason);
    
    // ตรวจสอบเหตุผลอีกครั้ง
    if (!deleteReason) {
        // ไม่ปิด modal แค่แสดง error ใน modal
        const reasonTextarea = document.getElementById('deleteReason');
        const existingError = document.querySelector('.delete-reason-error');
        
        if (existingError) {
            existingError.remove();
        }
        
        reasonTextarea.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback delete-reason-error';
        errorDiv.textContent = 'กรุณาระบุเหตุผลในการลบ';
        reasonTextarea.parentNode.appendChild(errorDiv);
        
        reasonTextarea.focus();
        return;
    }
    
    // ปิด Modal ก่อน
    if (modal) {
        modal.hide();
    }
    
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
    formData.append('reference_id', documentRef);
    formData.append('action', 'hard_delete'); // แยกระหว่างลบและยกเลิก
    if (deleteReason) {
        formData.append('reason', deleteReason);
    }
    
    fetch(EsvOdsConfig.deleteUrl, {
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
                text: data.message || 'ลบเอกสารเรียบร้อยแล้ว',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
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
 * แสดง Error Alert
 */
function showErrorAlert(message) {
    Swal.fire({
        title: 'เกิดข้อผิดพลาด',
        text: message,
        icon: 'error',
        confirmButtonText: 'ตกลง'
    });
}

/**
 * ดาวน์โหลดไฟล์
 */
function downloadFile(fileUrl, fileName) {
   // console.log('Downloading file:', fileUrl, fileName);
    
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
            text: 'เปิดไฟล์ในแท็บใหม่แทน',
            icon: 'warning',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.open(fileUrl, '_blank');
        });
    }
}

// ===================================================================
// *** EVENT HANDLERS ***
// ===================================================================

/**
 * จัดการ Form Submit สำหรับอัปเดตสถานะ
 */
function handleStatusUpdateForm() {
    const form = document.getElementById('statusUpdateForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
       // console.log('Submitting status update form');
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังอัปเดต...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(EsvOdsConfig.updateStatusUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'อัปเดตสำเร็จ!',
                    text: data.message || 'อัปเดตสถานะเรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal'));
                    if (modal) modal.hide();
                    location.reload();
                });
            } else {
                showErrorAlert(data.message || 'เกิดข้อผิดพลาดในการอัปเดต');
            }
        })
        .catch(error => {
            console.error('Form submit error:', error);
            showErrorAlert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        });
    });
}

/**
 * จัดการ Search Enhancement
 */
function handleSearchEnhancement() {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;
    
    // Enter key to submit
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('filterForm').submit();
        }
    });
}

// ===================================================================
// *** DOCUMENT READY & INITIALIZATION ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
   // console.log('🚀 ESV ODS Management System loading...');
    
    try {
        // Initialize core functionality
        handleStatusUpdateForm();
        handleSearchEnhancement();
        
        if (EsvOdsConfig.debug) {
            //console.log('🔧 Debug mode enabled');
            //console.log('⚙️ Configuration:', EsvOdsConfig);
        }
        
    } catch (error) {
        console.error('❌ Initialization error:', error);
        alert('เกิดข้อผิดพลาดในการโหลดระบบ กรุณารีเฟรชหน้า');
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

</script>