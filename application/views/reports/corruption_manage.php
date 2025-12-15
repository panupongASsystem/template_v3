<?php
// Helper function สำหรับ CSS class ของสถานะการทุจริต
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

// Helper function สำหรับแสดงสถานะเป็นภาษาไทย
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

// Helper function สำหรับแสดงประเภทการทุจริต
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

// Helper function สำหรับแสดงระดับความสำคัญ
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
/* ===== CORRUPTION MANAGEMENT SPECIFIC STYLES ===== */
/* เพิ่ม namespace เฉพาะ corruption-manage เพื่อไม่ให้ทับกับ styles อื่น */

/* ===== ROOT VARIABLES FOR CORRUPTION MANAGEMENT ===== */
.corruption-manage-page {
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
    --corruption-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.06), 0 8px 10px -6px rgb(0 0 0 / 0.04);
    --corruption-border-radius: 12px;
    --corruption-border-radius-lg: 16px;
    --corruption-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ===== GLOBAL STYLES สำหรับ CORRUPTION MANAGEMENT ===== */
.corruption-manage-page {
    background: linear-gradient(135deg, #fff5f5 0%, #fcfcfc 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--corruption-gray-700);
    min-height: 100vh;
}

.corruption-manage-page .corruption-container-fluid {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    min-height: calc(100vh - 140px);
}

/* ===== PAGE HEADER สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-page-header {
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

.corruption-manage-page .corruption-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    border-radius: 50%;
}

.corruption-manage-page .corruption-page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.08);
    position: relative;
    z-index: 1;
    color: #ffffff !important;
}

/* ===== HEADER ACTIONS ===== */
.corruption-manage-page .corruption-header-actions {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    z-index: 2;
    display: flex;
    gap: 0.75rem;
}

.corruption-manage-page .corruption-action-btn {
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

.corruption-manage-page .corruption-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ===== STATISTICS CARDS สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-stats-section {
    margin-bottom: 2rem;
}

.corruption-manage-page .corruption-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.corruption-manage-page .corruption-stat-card {
    background: var(--corruption-white);
    border-radius: var(--corruption-border-radius);
    padding: 1.5rem;
    box-shadow: var(--corruption-shadow-md);
    position: relative;
    overflow: hidden;
    transition: var(--corruption-transition);
    border: 1px solid var(--corruption-gray-100);
}

.corruption-manage-page .corruption-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--corruption-shadow-lg);
}

.corruption-manage-page .corruption-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--corruption-primary-color), var(--corruption-primary-light));
}

.corruption-manage-page .corruption-stat-card.pending::before { 
    background: linear-gradient(90deg, #ffc107, #ffb74d); 
}
.corruption-manage-page .corruption-stat-card.under_review::before { 
    background: linear-gradient(90deg, #17a2b8, #64b5f6); 
}
.corruption-manage-page .corruption-stat-card.investigating::before { 
    background: linear-gradient(90deg, #6f42c1, #ba68c8); 
}
.corruption-manage-page .corruption-stat-card.resolved::before { 
    background: linear-gradient(90deg, #28a745, #81c784); 
}
.corruption-manage-page .corruption-stat-card.dismissed::before { 
    background: linear-gradient(90deg, #6c757d, #9e9e9e); 
}
.corruption-manage-page .corruption-stat-card.closed::before { 
    background: linear-gradient(90deg, #dc3545, #e57373); 
}

.corruption-manage-page .corruption-stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.corruption-manage-page .corruption-stat-icon {
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

.corruption-manage-page .corruption-stat-icon.total { 
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.8), rgba(229, 115, 115, 0.8)); 
}
.corruption-manage-page .corruption-stat-icon.pending { 
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.8), rgba(255, 183, 77, 0.8)); 
}
.corruption-manage-page .corruption-stat-icon.under_review { 
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.8), rgba(100, 181, 246, 0.8)); 
}
.corruption-manage-page .corruption-stat-icon.investigating { 
    background: linear-gradient(135deg, rgba(111, 66, 193, 0.8), rgba(186, 104, 200, 0.8)); 
}
.corruption-manage-page .corruption-stat-icon.resolved { 
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.8), rgba(129, 199, 132, 0.8)); 
}
.corruption-manage-page .corruption-stat-icon.dismissed { 
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.8), rgba(158, 158, 158, 0.8)); 
}
.corruption-manage-page .corruption-stat-icon.closed { 
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.8), rgba(229, 115, 115, 0.8)); 
}

.corruption-manage-page .corruption-stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--corruption-gray-800);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.corruption-manage-page .corruption-stat-label {
    color: var(--corruption-gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

/* ===== FILTER SECTION สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-filter-section {
    margin-bottom: 2rem;
}

.corruption-manage-page .corruption-filter-card {
    background: var(--corruption-white);
    border-radius: var(--corruption-border-radius);
    padding: 2rem;
    box-shadow: var(--corruption-shadow-md);
    border: 1px solid var(--corruption-gray-100);
}

.corruption-manage-page .corruption-filter-card h5 {
    color: var(--corruption-gray-900);
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corruption-manage-page .corruption-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.corruption-manage-page .corruption-form-group {
    display: flex;
    flex-direction: column;
}

.corruption-manage-page .corruption-form-label {
    font-weight: 600;
    color: var(--corruption-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.corruption-manage-page .corruption-form-select, 
.corruption-manage-page .corruption-form-control {
    border: 2px solid var(--corruption-gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--corruption-transition);
    background-color: var(--corruption-white);
}

.corruption-manage-page .corruption-form-select:focus, 
.corruption-manage-page .corruption-form-control:focus {
    border-color: var(--corruption-primary-color);
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    outline: none;
}

.corruption-manage-page .corruption-filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.corruption-manage-page .corruption-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--corruption-transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    cursor: pointer;
}

.corruption-manage-page .corruption-btn-primary {
    background: linear-gradient(135deg, var(--corruption-primary-color), var(--corruption-primary-light));
    color: white;
}

.corruption-manage-page .corruption-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-lg);
    color: white;
}

.corruption-manage-page .corruption-btn-secondary {
    background: var(--corruption-gray-100);
    color: var(--corruption-gray-700);
}

.corruption-manage-page .corruption-btn-secondary:hover {
    background: var(--corruption-gray-200);
    color: var(--corruption-gray-800);
}

.corruption-manage-page .corruption-btn-success {
    background: linear-gradient(135deg, var(--corruption-success-color), #81c784);
    color: white;
}

.corruption-manage-page .corruption-btn-success:hover {
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-lg);
    color: white;
}

/* ===== ANALYTICS SECTION สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-analytics-section {
    margin-bottom: 2rem;
}

.corruption-manage-page .corruption-analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.corruption-manage-page .corruption-chart-card {
    background: var(--corruption-white);
    border-radius: var(--corruption-border-radius);
    padding: 2rem;
    box-shadow: var(--corruption-shadow-md);
    border: 1px solid var(--corruption-gray-100);
}

.corruption-manage-page .corruption-chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--corruption-gray-100);
}

.corruption-manage-page .corruption-chart-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--corruption-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corruption-manage-page .corruption-recent-reports .corruption-item {
    padding: 1rem;
    border: 1px solid var(--corruption-gray-100);
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: var(--corruption-transition);
}

.corruption-manage-page .corruption-recent-reports .corruption-item:hover {
    border-color: var(--corruption-primary-color);
    box-shadow: var(--corruption-shadow-md);
}

.corruption-manage-page .corruption-recent-reports .corruption-item h6 a {
    color: var(--corruption-primary-color);
    text-decoration: none;
    font-weight: 600;
}

.corruption-manage-page .corruption-recent-reports .corruption-item h6 a:hover {
    color: var(--corruption-primary-light);
}

.corruption-manage-page .corruption-type-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.corruption-manage-page .corruption-type-stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--corruption-gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--corruption-primary-color);
}

.corruption-manage-page .corruption-type-stat-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--corruption-gray-700);
}

.corruption-manage-page .corruption-type-stat-indicator {
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

.corruption-manage-page .corruption-type-stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--corruption-gray-900);
}

/* ===== DATA TABLE SECTION สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-table-section {
    margin-bottom: 2rem;
}

.corruption-manage-page .corruption-table-card {
    background: var(--corruption-white);
    border-radius: var(--corruption-border-radius);
    overflow: hidden;
    box-shadow: var(--corruption-shadow-md);
    border: 1px solid var(--corruption-gray-100);
}

.corruption-manage-page .corruption-table-header {
    background: var(--corruption-gray-50);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--corruption-gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.corruption-manage-page .corruption-table-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--corruption-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corruption-manage-page .corruption-table-actions {
    display: flex;
    gap: 0.5rem;
}

.corruption-manage-page .corruption-btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

.corruption-manage-page .corruption-btn-outline-primary {
    border: 2px solid var(--corruption-primary-color);
    color: var(--corruption-primary-color);
    background: transparent;
}

.corruption-manage-page .corruption-btn-outline-primary:hover {
    background: var(--corruption-primary-color);
    color: white;
}

/* ===== CORRUPTION CARDS ===== */
.corruption-manage-page .corruption-report-container {
    background: var(--corruption-white);
    border: 2px solid var(--corruption-gray-100);
    border-radius: var(--corruption-border-radius);
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: var(--corruption-shadow-md);
    transition: var(--corruption-transition);
}

.corruption-manage-page .corruption-report-container:hover {
    border-color: var(--corruption-primary-light);
    box-shadow: var(--corruption-shadow-lg);
    transform: translateY(-2px);
}

.corruption-manage-page .corruption-report-header {
    background: linear-gradient(135deg, var(--corruption-secondary-color) 0%, #ffebee 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--corruption-gray-200);
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--corruption-primary-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.corruption-manage-page .corruption-report-number {
    background: linear-gradient(135deg, var(--corruption-primary-color), var(--corruption-primary-light));
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-left: auto;
}

.corruption-manage-page .corruption-report-data-row {
    background: var(--corruption-white);
    border-bottom: 1px solid var(--corruption-gray-100);
    transition: var(--corruption-transition);
}

.corruption-manage-page .corruption-report-data-row:hover {
    background: var(--corruption-gray-50);
}

.corruption-manage-page .corruption-report-status-row {
    background: var(--corruption-gray-50);
    border-left: 4px solid var(--corruption-primary-color);
    border-bottom: none;
}

.corruption-manage-page .corruption-table {
    margin: 0;
}

.corruption-manage-page .corruption-table tbody td {
    padding: 1.25rem 1rem;
    border-color: var(--corruption-gray-100);
    vertical-align: middle;
    font-size: 0.875rem;
}

/* ===== STATUS BADGES สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-status-badge {
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

.corruption-manage-page .corruption-status-badge.pending {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border: 1px solid rgba(255, 152, 0, 0.3);
}

.corruption-manage-page .corruption-status-badge.under_review {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.corruption-manage-page .corruption-status-badge.investigating {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border: 1px solid rgba(156, 39, 176, 0.3);
}

.corruption-manage-page .corruption-status-badge.resolved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.corruption-manage-page .corruption-status-badge.dismissed {
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.8), rgba(233, 236, 239, 0.6));
    color: #495057;
    border: 1px solid rgba(108, 117, 125, 0.3);
}

.corruption-manage-page .corruption-status-badge.closed {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

/* ===== OTHER BADGES สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-priority-badge, 
.corruption-manage-page .corruption-type-badge, 
.corruption-manage-page .corruption-user-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.corruption-manage-page .corruption-priority-badge.low {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.corruption-manage-page .corruption-priority-badge.normal {
    background: var(--corruption-gray-100);
    color: var(--corruption-gray-700);
}

.corruption-manage-page .corruption-priority-badge.high {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.corruption-manage-page .corruption-priority-badge.urgent {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.corruption-manage-page .corruption-type-badge.embezzlement {
    background: linear-gradient(135deg, #fff5f5, #ffebee);
    color: var(--corruption-primary-color);
}

.corruption-manage-page .corruption-type-badge.bribery {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    color: #6a1b9a;
}

.corruption-manage-page .corruption-type-badge.abuse_of_power {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.corruption-manage-page .corruption-type-badge.conflict_of_interest {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.corruption-manage-page .corruption-type-badge.procurement_fraud {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    color: #0d47a1;
}

.corruption-manage-page .corruption-type-badge.other {
    background: var(--corruption-gray-100);
    color: var(--corruption-gray-600);
}

.corruption-manage-page .corruption-user-type-badge.guest {
    background: var(--corruption-gray-100);
    color: var(--corruption-gray-600);
}

.corruption-manage-page .corruption-user-type-badge.public {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.corruption-manage-page .corruption-user-type-badge.staff {
    background: linear-gradient(135deg, var(--corruption-secondary-color), #ffebee);
    color: var(--corruption-primary-color);
}

/* ===== ACTION BUTTONS สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.corruption-manage-page .corruption-btn-action {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--corruption-transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 80px;
    justify-content: center;
    white-space: nowrap;
}

.corruption-manage-page .corruption-btn-action.view {
    background: linear-gradient(135deg, rgba(100, 181, 246, 0.8), rgba(33, 150, 243, 0.8));
    color: white;
}

.corruption-manage-page .corruption-btn-action.view:hover {
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.9), rgba(21, 101, 192, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
    color: white;
}

.corruption-manage-page .corruption-btn-action.delete {
    background: linear-gradient(135deg, rgba(229, 115, 115, 0.8), rgba(244, 67, 54, 0.8));
    color: white;
}

.corruption-manage-page .corruption-btn-action.delete:hover {
    background: linear-gradient(135deg, rgba(211, 47, 47, 0.9), rgba(198, 40, 40, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
    color: white;
}

/* ===== STATUS UPDATE BUTTONS สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-status-cell {
    padding: 1.5rem !important;
    border-top: 1px solid var(--corruption-gray-200) !important;
}

.corruption-manage-page .corruption-status-update-row {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.corruption-manage-page .corruption-status-label {
    font-weight: 600;
    color: var(--corruption-gray-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.corruption-manage-page .corruption-status-buttons-container {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.corruption-manage-page .corruption-btn-status-row {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 2px solid transparent;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--corruption-transition);
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

.corruption-manage-page .corruption-btn-status-row.pending {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border-color: rgba(255, 152, 0, 0.3);
}

.corruption-manage-page .corruption-btn-status-row.pending:hover:not(:disabled) {
    background: rgba(255, 152, 0, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
}

.corruption-manage-page .corruption-btn-status-row.under_review {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border-color: rgba(33, 150, 243, 0.3);
}

.corruption-manage-page .corruption-btn-status-row.under_review:hover:not(:disabled) {
    background: rgba(33, 150, 243, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
}

.corruption-manage-page .corruption-btn-status-row.investigating {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border-color: rgba(156, 39, 176, 0.3);
}

.corruption-manage-page .corruption-btn-status-row.investigating:hover:not(:disabled) {
    background: rgba(156, 39, 176, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
}

.corruption-manage-page .corruption-btn-status-row.resolved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border-color: rgba(76, 175, 80, 0.3);
}

.corruption-manage-page .corruption-btn-status-row.resolved:hover:not(:disabled) {
    background: rgba(76, 175, 80, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
}

.corruption-manage-page .corruption-btn-status-row.dismissed {
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.8), rgba(233, 236, 239, 0.6));
    color: #495057;
    border-color: rgba(108, 117, 125, 0.3);
}

.corruption-manage-page .corruption-btn-status-row.dismissed:hover:not(:disabled) {
    background: rgba(108, 117, 125, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
}

.corruption-manage-page .corruption-btn-status-row.closed {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border-color: rgba(244, 67, 54, 0.3);
}

.corruption-manage-page .corruption-btn-status-row.closed:hover:not(:disabled) {
    background: rgba(244, 67, 54, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-md);
}

.corruption-manage-page .corruption-btn-status-row.current {
    background: var(--corruption-gray-100);
    color: var(--corruption-gray-600);
    cursor: not-allowed;
    opacity: 0.8;
    border-color: var(--corruption-gray-300);
}

.corruption-manage-page .corruption-btn-status-row.current::before {
    content: "✓ ";
    font-weight: bold;
}

/* ===== เพิ่ม CSS สำหรับปุ่ม Disabled ===== */
.corruption-manage-page .corruption-btn-status-row:disabled,
.corruption-manage-page .corruption-btn-status-row.disabled {
    background: var(--corruption-gray-100) !important;
    color: var(--corruption-gray-400) !important;
    border-color: var(--corruption-gray-200) !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    transform: none !important;
    box-shadow: none !important;
}

.corruption-manage-page .corruption-btn-status-row:disabled:hover,
.corruption-manage-page .corruption-btn-status-row.disabled:hover {
    background: var(--corruption-gray-100) !important;
    color: var(--corruption-gray-400) !important;
    border-color: var(--corruption-gray-200) !important;
    transform: none !important;
    box-shadow: none !important;
}

.corruption-manage-page .corruption-btn-status-row:disabled i,
.corruption-manage-page .corruption-btn-status-row.disabled i {
    color: var(--corruption-gray-400) !important;
    opacity: 0.5;
}

.corruption-manage-page .corruption-btn-status-row:disabled::before,
.corruption-manage-page .corruption-btn-status-row.disabled::before {
    content: "🔒 ";
    font-weight: bold;
    opacity: 0.7;
}

/* ===== FILE DISPLAY สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-files-display {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.corruption-manage-page .corruption-file-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    background: var(--corruption-gray-50);
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    color: var(--corruption-gray-600);
    border: 1px solid var(--corruption-gray-200);
    transition: var(--corruption-transition);
    cursor: pointer;
}

.corruption-manage-page .corruption-file-item:hover {
    background: var(--corruption-gray-100);
    border-color: var(--corruption-primary-color);
    transform: translateY(-1px);
    box-shadow: var(--corruption-shadow-sm);
}

.corruption-manage-page .corruption-file-item i {
    color: var(--corruption-primary-color);
    font-size: 0.875rem;
}

.corruption-manage-page .corruption-files-more-badge {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--corruption-transition);
}

.corruption-manage-page .corruption-files-more-badge:hover {
    transform: scale(1.05);
    box-shadow: var(--corruption-shadow-md);
}

/* ===== PERSONAL INFO สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-personal-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.corruption-manage-page .corruption-personal-info-item {
    font-size: 0.8rem;
    color: var(--corruption-gray-600);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* ===== การปรับปรุง Display Elements ===== */
.corruption-manage-page .corruption-id-display {
    font-size: 1.1rem;
    color: var(--corruption-primary-color);
    background: var(--corruption-gray-50);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    border: 1px solid var(--corruption-gray-200);
}

.corruption-manage-page .corruption-date-display {
    text-align: center;
}

.corruption-manage-page .corruption-date-part {
    font-weight: 600;
    color: var(--corruption-gray-800);
    font-size: 0.9rem;
    margin-bottom: 0.2rem;
}

.corruption-manage-page .corruption-time-part {
    color: var(--corruption-gray-600);
    font-size: 0.8rem;
}

.corruption-manage-page .corruption-name-display {
    font-size: 0.95rem;
    color: var(--corruption-gray-900);
    margin-bottom: 0.3rem;
}

.corruption-manage-page .corruption-phone-display {
    color: var(--corruption-gray-600);
    font-size: 0.8rem;
}

/* ===== PAGINATION สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--corruption-gray-200);
    background: var(--corruption-gray-50);
}

.corruption-manage-page .corruption-pagination-info {
    color: var(--corruption-gray-600);
    font-size: 0.875rem;
}

/* ===== EMPTY STATE สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--corruption-gray-500);
}

.corruption-manage-page .corruption-empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.corruption-manage-page .corruption-empty-state h5 {
    color: var(--corruption-gray-600);
    margin-bottom: 0.5rem;
}

/* ===== RESPONSIVE DESIGN สำหรับ CORRUPTION ===== */
@media (max-width: 768px) {
    .corruption-manage-page .corruption-container-fluid {
        padding: 1rem;
        min-height: calc(100vh - 120px);
    }
    
    .corruption-manage-page .corruption-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
        margin-top: 0.5rem;
    }
    
    .corruption-manage-page .corruption-page-header h1 {
        font-size: 1.5rem;
    }
    
    .corruption-manage-page .corruption-header-actions {
        position: relative;
        top: auto;
        right: auto;
        margin-top: 1rem;
        flex-direction: column;
        align-items: stretch;
    }
    
    .corruption-manage-page .corruption-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .corruption-manage-page .corruption-analytics-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .corruption-manage-page .corruption-filter-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .corruption-manage-page .corruption-filter-actions {
        justify-content: stretch;
    }
    
    .corruption-manage-page .corruption-filter-actions .corruption-btn {
        flex: 1;
    }
    
    .corruption-manage-page .corruption-table-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .corruption-manage-page .corruption-action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .corruption-manage-page .corruption-btn-action {
        width: 100%;
        min-width: auto;
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    .corruption-manage-page .corruption-status-buttons-container {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .corruption-manage-page .corruption-btn-status-row {
        width: 100%;
        min-width: auto;
        padding: 0.5rem;
        font-size: 0.75rem;
        justify-content: flex-start;
    }
    
    .corruption-manage-page .corruption-report-container {
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    
    .corruption-manage-page .corruption-report-header {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
    
    .corruption-manage-page .corruption-report-number {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    
    .corruption-manage-page .corruption-table tbody td {
        padding: 1rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .corruption-manage-page .corruption-status-cell {
        padding: 1rem 0.75rem !important;
    }
    
    .corruption-manage-page .corruption-status-label {
        font-size: 0.8rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 480px) {
    .corruption-manage-page .corruption-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .corruption-manage-page .corruption-stat-value {
        font-size: 1.8rem;
    }
    
    .corruption-manage-page .corruption-report-header {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .corruption-manage-page .corruption-report-number {
        margin-left: 0;
    }
}

/* ===== ANIMATIONS สำหรับ CORRUPTION ===== */
@keyframes corruptionFadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.corruption-manage-page .corruption-report-container {
    animation: corruptionFadeInUp 0.3s ease-out;
}

.corruption-manage-page .corruption-stat-card {
    animation: corruptionFadeInUp 0.3s ease-out;
}

.corruption-manage-page .corruption-chart-card {
    animation: corruptionFadeInUp 0.3s ease-out;
}

/* ===== LOADING STATES สำหรับ CORRUPTION ===== */
.corruption-manage-page .corruption-loading {
    opacity: 0.6;
    pointer-events: none;
}

.corruption-manage-page .corruption-loading::after {
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
    animation: corruptionSpin 1s linear infinite;
}

@keyframes corruptionSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="corruption-manage-page">
    <div class="corruption-container-fluid">
        <!-- ===== PAGE HEADER ===== -->
        <header class="corruption-page-header">
            <h1><i class="fas fa-shield-alt me-3"></i>จัดการรายงานการทุจริต</h1>
            
            <!-- Header Actions -->
            <div class="corruption-header-actions">
                <a href="<?= site_url('Corruption/export_excel') ?>" class="corruption-action-btn" title="ส่งออกรายงาน">
                    <i class="fas fa-file-excel"></i>
                    <span>ส่งออก Excel</span>
                </a>
                <a href="<?= site_url('Corruption/track_status') ?>" class="corruption-action-btn" title="ติดตามสถานะ">
                    <i class="fas fa-search"></i>
                    <span>ติดตาม</span>
                </a>
            </div>
        </header>

        <!-- ===== STATISTICS SECTION ===== -->
        <section class="corruption-stats-section">
            <div class="corruption-stats-grid">
                <div class="corruption-stat-card total">
                    <div class="corruption-stat-header">
                        <div class="corruption-stat-icon total">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="corruption-stat-value"><?= number_format($corruption_summary['total'] ?? 0) ?></div>
                    <div class="corruption-stat-label">ทั้งหมด</div>
                </div>

                <div class="corruption-stat-card pending">
                    <div class="corruption-stat-header">
                        <div class="corruption-stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="corruption-stat-value"><?= number_format($corruption_summary['by_status']['pending'] ?? 0) ?></div>
                    <div class="corruption-stat-label">รอดำเนินการ</div>
                </div>

                <div class="corruption-stat-card under_review">
                    <div class="corruption-stat-header">
                        <div class="corruption-stat-icon under_review">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="corruption-stat-value"><?= number_format($corruption_summary['by_status']['under_review'] ?? 0) ?></div>
                    <div class="corruption-stat-label">กำลังตรวจสอบ</div>
                </div>

                <div class="corruption-stat-card investigating">
                    <div class="corruption-stat-header">
                        <div class="corruption-stat-icon investigating">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                    <div class="corruption-stat-value"><?= number_format($corruption_summary['by_status']['investigating'] ?? 0) ?></div>
                    <div class="corruption-stat-label">กำลังสอบสวน</div>
                </div>

                <div class="corruption-stat-card resolved">
                    <div class="corruption-stat-header">
                        <div class="corruption-stat-icon resolved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="corruption-stat-value"><?= number_format($corruption_summary['by_status']['resolved'] ?? 0) ?></div>
                    <div class="corruption-stat-label">ดำเนินการแล้ว</div>
                </div>

                <div class="corruption-stat-card dismissed">
                    <div class="corruption-stat-header">
                        <div class="corruption-stat-icon dismissed">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="corruption-stat-value"><?= number_format($corruption_summary['by_status']['dismissed'] ?? 0) ?></div>
                    <div class="corruption-stat-label">ยกเลิก</div>
                </div>

                <div class="corruption-stat-card closed">
                    <div class="corruption-stat-header">
                        <div class="corruption-stat-icon closed">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="corruption-stat-value"><?= number_format($corruption_summary['by_status']['closed'] ?? 0) ?></div>
                    <div class="corruption-stat-label">ปิดเรื่อง</div>
                </div>
            </div>
        </section>

        <!-- ===== FILTER SECTION ===== -->
        <section class="corruption-filter-section">
            <div class="corruption-filter-card">
                <h5><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
                <form method="GET" action="<?= site_url('Corruption/admin_management') ?>" id="filterForm">
                    <div class="corruption-filter-grid">
                        <div class="corruption-form-group">
                            <label class="corruption-form-label">สถานะ:</label>
                            <select class="corruption-form-select" name="status">
                                <option value="">ทุกสถานะ</option>
                                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>รอดำเนินการ</option>
                                <option value="under_review" <?= ($filters['status'] ?? '') === 'under_review' ? 'selected' : '' ?>>กำลังตรวจสอบ</option>
                                <option value="investigating" <?= ($filters['status'] ?? '') === 'investigating' ? 'selected' : '' ?>>กำลังสอบสวน</option>
                                <option value="resolved" <?= ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>ดำเนินการแล้ว</option>
                                <option value="dismissed" <?= ($filters['status'] ?? '') === 'dismissed' ? 'selected' : '' ?>>ยกเลิก</option>
                                <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : '' ?>>ปิดเรื่อง</option>
                            </select>
                        </div>

                        <div class="corruption-form-group">
                            <label class="corruption-form-label">ประเภทการทุจริต:</label>
                            <select class="corruption-form-select" name="corruption_type">
                                <option value="">ทุกประเภท</option>
                                <option value="embezzlement" <?= ($filters['corruption_type'] ?? '') === 'embezzlement' ? 'selected' : '' ?>>การยักยอกเงิน</option>
                                <option value="bribery" <?= ($filters['corruption_type'] ?? '') === 'bribery' ? 'selected' : '' ?>>การรับสินบน</option>
                                <option value="abuse_of_power" <?= ($filters['corruption_type'] ?? '') === 'abuse_of_power' ? 'selected' : '' ?>>การใช้อำนาจเกินตัว</option>
                                <option value="conflict_of_interest" <?= ($filters['corruption_type'] ?? '') === 'conflict_of_interest' ? 'selected' : '' ?>>ผลประโยชน์ทับซ้อน</option>
                                <option value="procurement_fraud" <?= ($filters['corruption_type'] ?? '') === 'procurement_fraud' ? 'selected' : '' ?>>การทุจริตในการจัดซื้อ</option>
                                <option value="other" <?= ($filters['corruption_type'] ?? '') === 'other' ? 'selected' : '' ?>>อื่นๆ</option>
                            </select>
                        </div>

                        <div class="corruption-form-group">
                            <label class="corruption-form-label">วันที่เริ่มต้น:</label>
                            <input type="date" class="corruption-form-control" name="date_from" 
                                   value="<?= $filters['date_from'] ?? '' ?>">
                        </div>

                        <div class="corruption-form-group">
                            <label class="corruption-form-label">วันที่สิ้นสุด:</label>
                            <input type="date" class="corruption-form-control" name="date_to" 
                                   value="<?= $filters['date_to'] ?? '' ?>">
                        </div>

                        <div class="corruption-form-group">
                            <label class="corruption-form-label">ค้นหา:</label>
                            <input type="text" class="corruption-form-control" name="search" 
                                   placeholder="ค้นหาหมายเลข, ชื่อ, หัวข้อ..."
                                   value="<?= $filters['search'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="corruption-filter-actions">
                        <button type="submit" class="corruption-btn corruption-btn-primary">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <a href="<?= site_url('Corruption/admin_management') ?>" class="corruption-btn corruption-btn-secondary">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </a>
                        <a href="<?= site_url('Corruption/export_excel') ?>" class="corruption-btn corruption-btn-success">
                            <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- ===== ANALYTICS SECTION ===== -->
        <section class="corruption-analytics-section">
            <div class="corruption-analytics-grid">
                <!-- Recent Corruption Reports -->
                <div class="corruption-chart-card">
                    <div class="corruption-chart-header">
                        <h3 class="corruption-chart-title">
                            <i class="fas fa-clock me-2"></i>รายงานการทุจริตล่าสุด
                        </h3>
                    </div>
                    <div class="corruption-recent-reports">
                        <?php if (isset($recent_reports) && !empty($recent_reports)): ?>
                            <?php foreach (array_slice($recent_reports, 0, 5) as $recent): ?>
                                <div class="corruption-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">
                                            <a href="<?= site_url('Corruption/report_detail/' . $recent->corruption_report_id) ?>">
                                                #<?= $recent->corruption_report_id ?> - <?= htmlspecialchars(mb_substr($recent->complaint_subject, 0, 30)) ?>
                                                <?= mb_strlen($recent->complaint_subject) > 30 ? '...' : '' ?>
                                            </a>
                                        </h6>
                                        <span class="corruption-status-badge <?= get_corruption_status_class($recent->report_status) ?>">
                                            <?= get_corruption_status_display($recent->report_status) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        ประเภท: <?= get_corruption_type_display($recent->corruption_type) ?> 
                                        | ผู้แจ้ง: <?= $recent->is_anonymous ? 'ไม่ระบุตัวตน' : htmlspecialchars($recent->reporter_name) ?>
                                        | <?php 
                                            $thai_months = [
                                                '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                            ];
                                            
                                            $date = date('j', strtotime($recent->created_at));
                                            $month = $thai_months[date('m', strtotime($recent->created_at))];
                                            $year = date('Y', strtotime($recent->created_at)) + 543;
                                            $time = date('H:i', strtotime($recent->created_at));
                                            
                                            echo $date . ' ' . $month . ' ' . $year . ' ' . $time;
                                        ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="corruption-empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>ยังไม่มีรายงานการทุจริต</h5>
                                <p>ยังไม่มีการแจ้งเรื่องร้องเรียนการทุจริต</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Type Statistics -->
                <div class="corruption-chart-card">
                    <div class="corruption-chart-header">
                        <h3 class="corruption-chart-title">
                            <i class="fas fa-chart-pie me-2"></i>สถิติตามประเภทการทุจริต
                        </h3>
                    </div>
                    <div class="corruption-type-stats">
                        <?php 
                        $type_labels = [
                            'embezzlement' => 'การยักยอกเงิน',
                            'bribery' => 'การรับสินบน',
                            'abuse_of_power' => 'การใช้อำนาจเกินตัว',
                            'conflict_of_interest' => 'ผลประโยชน์ทับซ้อน',
                            'procurement_fraud' => 'การทุจริตในการจัดซื้อ',
                            'other' => 'อื่นๆ'
                        ];
                        $type_colors = [
                            'embezzlement' => '#dc3545',
                            'bribery' => '#6f42c1',
                            'abuse_of_power' => '#28a745',
                            'conflict_of_interest' => '#ffc107',
                            'procurement_fraud' => '#17a2b8',
                            'other' => '#6c757d'
                        ];
                        ?>
                        <?php if (isset($corruption_summary['by_type'])): ?>
                            <?php foreach ($corruption_summary['by_type'] as $type => $count): ?>
                                <div class="corruption-type-stat-item">
                                    <div class="corruption-type-stat-label">
                                        <div class="corruption-type-stat-indicator" style="background-color: <?= $type_colors[$type] ?? '#9e9e9e' ?>;"></div>
                                        <span><?= $type_labels[$type] ?? $type ?></span>
                                    </div>
                                    <div class="corruption-type-stat-value"><?= number_format($count) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="corruption-empty-state">
                                <i class="fas fa-chart-pie"></i>
                                <h5>ไม่มีข้อมูลสถิติ</h5>
                                <p>ยังไม่มีรายงานการทุจริตเพื่อแสดงสถิติ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== DATA TABLE SECTION ===== -->
        <section class="corruption-table-section">
            <div class="corruption-table-card">
                <div class="corruption-table-header">
                    <h5 class="corruption-table-title">
                        <i class="fas fa-list me-2"></i>รายการรายงานการทุจริต
                    </h5>
                    <div class="corruption-table-actions">
                        <button class="corruption-btn corruption-btn-outline-primary corruption-btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                        </button>
                    </div>
                </div>
                
                <div class="corruption-table-content">
                    <?php if (empty($corruption_reports)): ?>
                        <div class="corruption-empty-state">
                            <i class="fas fa-shield-alt"></i>
                            <h5>ไม่พบรายงานการทุจริต</h5>
                            <p>กรุณาลองใช้ตัวกรองอื่น หรือรอรายงานใหม่</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($corruption_reports as $report): ?>
                            <div class="corruption-report-container" data-report-id="<?= $report->corruption_id ?? $report['corruption_id'] ?>">
                                <!-- Corruption Report Header -->
                                <div class="corruption-report-header">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>รายงานการทุจริต <?= get_corruption_type_display($report->corruption_type ?? $report['corruption_type']) ?></span>
                                    <span class="corruption-report-number">#<?= $report->corruption_report_id ?? $report['corruption_report_id'] ?></span>
                                </div>
                                
                                <!-- Corruption Report Content -->
                                <table class="corruption-table mb-0">
                                    <tbody>
                                        <!-- Corruption Report Data Row -->
                                        <tr class="corruption-report-data-row">
                                            <td style="width: 8%;">
                                                <div class="text-center">
                                                    <strong class="corruption-id-display"><?= $report->corruption_report_id ?? $report['corruption_report_id'] ?></strong>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="corruption-date-display">
                                                    <?php 
                                                    $thai_months = [
                                                        '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                        '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                        '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                                    ];
                                                    
                                                    $created_at = $report->created_at ?? $report['created_at'];
                                                    $date = date('j', strtotime($created_at));
                                                    $month = $thai_months[date('m', strtotime($created_at))];
                                                    $year = date('Y', strtotime($created_at)) + 543;
                                                    $time = date('H:i', strtotime($created_at));
                                                    ?>
                                                    <div class="corruption-date-part"><?= $date ?> <?= $month ?> <?= $year ?></div>
                                                    <div class="corruption-time-part"><?= $time ?> น.</div>
                                                </div>
                                            </td>
                                            <td style="width: 15%;">
                                                <div class="text-center">
                                                    <span class="corruption-status-badge <?= get_corruption_status_class($report->report_status ?? $report['report_status']) ?>">
                                                        <?= get_corruption_status_display($report->report_status ?? $report['report_status']) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="text-center">
                                                    <span class="corruption-priority-badge <?= $report->priority_level ?? $report['priority_level'] ?? 'normal' ?>">
                                                        <?= get_corruption_priority_display($report->priority_level ?? $report['priority_level'] ?? 'normal') ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="text-center">
                                                    <span class="corruption-type-badge <?= $report->corruption_type ?? $report['corruption_type'] ?>">
                                                        <?= get_corruption_type_display($report->corruption_type ?? $report['corruption_type']) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="corruption-files-display">
                                                    <?php if (!empty($report->files)): ?>
                                                        <?php 
                                                        $displayFiles = array_slice($report->files, 0, 2);
                                                        $remainingCount = count($report->files) - count($displayFiles);
                                                        ?>
                                                        
                                                        <?php foreach ($displayFiles as $file): ?>
                                                            <div class="corruption-file-item" 
                                                                 onclick="downloadFile('<?= site_url('uploads/corruption_evidence/' . $file->file_name) ?>', '<?= htmlspecialchars($file->file_original_name, ENT_QUOTES) ?>')"
                                                                 title="<?= htmlspecialchars($file->file_original_name) ?>">
                                                                <i class="fas fa-file"></i>
                                                                <span class="file-name"><?= mb_substr($file->file_original_name, 0, 6) ?><?= mb_strlen($file->file_original_name) > 6 ? '...' : '' ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        
                                                        <?php if ($remainingCount > 0): ?>
                                                            <div class="corruption-files-more-badge" 
                                                                 onclick="showAllFiles('<?= $report->corruption_report_id ?? $report['corruption_report_id'] ?>')"
                                                                 title="ดูไฟล์ทั้งหมด">
                                                                +<?= $remainingCount ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <div class="text-center">
                                                            <span class="text-muted small">ไม่มีไฟล์</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="width: 18%;">
                                                <div class="corruption-personal-info">
                                                    <div class="corruption-personal-info-item corruption-name-display">
                                                        <strong>
                                                            <?php if ($report->is_anonymous ?? $report['is_anonymous']): ?>
                                                                ไม่ระบุตัวตน
                                                            <?php else: ?>
                                                                <?= htmlspecialchars($report->reporter_name ?? $report['reporter_name']) ?>
                                                            <?php endif; ?>
                                                        </strong>
                                                    </div>
                                                    <?php if (!($report->is_anonymous ?? $report['is_anonymous']) && !empty($report->reporter_phone ?? $report['reporter_phone'])): ?>
                                                        <div class="corruption-personal-info-item corruption-phone-display">
                                                            <i class="fas fa-phone me-1"></i> 
                                                            <?= htmlspecialchars($report->reporter_phone ?? $report['reporter_phone']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="corruption-personal-info-item">
                                                        <i class="fas fa-file-alt me-1"></i>
                                                        <small><?= htmlspecialchars(mb_substr($report->complaint_subject ?? $report['complaint_subject'], 0, 30)) ?>
                                                        <?= mb_strlen($report->complaint_subject ?? $report['complaint_subject']) > 30 ? '...' : '' ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="text-center">
                                                    <span class="corruption-user-type-badge <?= $report->reporter_user_type ?? $report['reporter_user_type'] ?? 'guest' ?>">
                                                        <?php
                                                        $user_type_labels = [
                                                            'guest' => 'ผู้ใช้ทั่วไป',
                                                            'public' => 'สมาชิก',
                                                            'staff' => 'เจ้าหน้าที่'
                                                        ];
                                                        echo $user_type_labels[$report->reporter_user_type ?? $report['reporter_user_type'] ?? 'guest'];
                                                        ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 13%;">
                                                <div class="corruption-action-buttons">
                                                    <a href="<?= site_url('Corruption/report_detail/' . ($report->corruption_report_id ?? $report['corruption_report_id'])) ?>" 
                                                       class="corruption-btn-action view" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>ดู
                                                    </a>
                                                    
                                                    <?php if ($can_delete ?? false): ?>
                                                        <button type="button" 
                                                                class="corruption-btn-action delete" 
                                                                onclick="confirmDeleteReport('<?= $report->corruption_id ?? $report['corruption_id'] ?>', '<?= htmlspecialchars($report->complaint_subject ?? $report['complaint_subject'], ENT_QUOTES) ?>')"
                                                                title="ลบรายงาน">
                                                            <i class="fas fa-trash"></i>ลบ
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Corruption Report Status Management Row -->
                                        <tr class="corruption-report-status-row">
                                            <td colspan="9" class="corruption-status-cell">
                                                <div class="corruption-status-update-row">
                                                    <div class="corruption-status-label">
                                                        <i class="fas fa-sync-alt"></i>
                                                        อัปเดตสถานะ #<?= $report->corruption_report_id ?? $report['corruption_report_id'] ?>
                                                    </div>
                                                    <div class="corruption-status-buttons-container">
                                                        <?php 
                                                        $current_status = $report->report_status ?? $report['report_status'];
                                                        $can_handle = $can_update_status ?? false;

                                                        // กำหนดสถานะที่สามารถเปลี่ยนได้ตามเงื่อนไข
                                                        switch($current_status) {
                                                            case 'pending': // รอดำเนินการ
                                                                $available_statuses = ['under_review']; // เฉพาะ กำลังตรวจสอบ
                                                                break;
                                                            case 'under_review': // กำลังตรวจสอบ
                                                                $available_statuses = ['investigating', 'dismissed'];
                                                                break;
                                                            case 'investigating': // กำลังสอบสวน
                                                                $available_statuses = ['resolved', 'dismissed'];
                                                                break;
                                                            case 'resolved': // ดำเนินการแล้ว
                                                            case 'dismissed': // ยกเลิก
                                                                $available_statuses = ['closed'];
                                                                break;
                                                            case 'closed': // ปิดเรื่อง
                                                                $available_statuses = []; // ไม่สามารถเปลี่ยนได้แล้ว
                                                                break;
                                                        }
                                                        
                                                        $all_status_buttons = [
                                                            'pending' => ['pending', 'fas fa-clock', 'รอดำเนินการ'],
                                                            'under_review' => ['under_review', 'fas fa-search', 'กำลังตรวจสอบ'],
                                                            'investigating' => ['investigating', 'fas fa-gavel', 'กำลังสอบสวน'],
                                                            'resolved' => ['resolved', 'fas fa-check-circle', 'ดำเนินการแล้ว'],
                                                            'dismissed' => ['dismissed', 'fas fa-times-circle', 'ยกเลิก'],
                                                            'closed' => ['closed', 'fas fa-lock', 'ปิดเรื่อง']
                                                        ];
                                                        
                                                        foreach ($all_status_buttons as $status_key => $status_info): 
                                                            $status_class = $status_info[0];
                                                            $status_icon = $status_info[1];
                                                            $status_display = $status_info[2];
                                                            
                                                            $is_current = ($current_status === $status_key);
                                                            $is_available = in_array($status_key, $available_statuses);
                                                            $is_clickable = ($can_update_status && $is_available);

                                                            $button_classes = "corruption-btn-status-row {$status_class}";
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
                                                                $report_id_js = htmlspecialchars($report->corruption_id ?? $report['corruption_id'], ENT_QUOTES);
                                                                $subject_js = htmlspecialchars($report->complaint_subject ?? $report['complaint_subject'], ENT_QUOTES);
                                                                $onclick_code = "onclick=\"updateReportStatusDirect('{$report_id_js}', '{$status_key}', '{$subject_js}', '{$status_display}')\"";
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
                    <div class="corruption-pagination-container">
                        <div class="corruption-pagination-info">
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
                    <i class="fas fa-sync-alt me-2"></i>อัปเดตสถานะรายงานการทุจริต
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="statusReportId" name="report_id">
                    <input type="hidden" id="statusNewStatus" name="new_status">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>เรื่อง:</strong> <span id="statusReportSubject"></span>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-arrow-right me-2"></i>
                        <strong>เปลี่ยนสถานะเป็น:</strong> <span id="statusDisplayText" class="fw-bold"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ระดับความสำคัญ:</label>
                        <select class="form-select" id="statusNewPriority" name="new_priority">
                            <option value="low">ต่ำ</option>
                            <option value="normal">ปกติ</option>
                            <option value="high">สูง</option>
                            <option value="urgent">เร่งด่วน</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ (ถ้ามี):</label>
                        <textarea class="form-control" id="statusNote" name="note" rows="4"
                                  placeholder="หมายเหตุเพิ่มเติมสำหรับการดำเนินการ..."></textarea>
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
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-warning me-2"></i>
                    <strong>คำเตือน:</strong> การดำเนินการนี้ไม่สามารถยกเลิกได้!
                </div>
                
                <p>คุณต้องการลบรายงานการทุจริตนี้หรือไม่?</p>
                
                <div class="bg-light p-3 rounded">
                    <strong>รายงาน:</strong> #<span id="deleteReportId"></span><br>
                    <strong>เรื่อง:</strong> <span id="deleteReportSubject"></span>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">
                        เหตุผลในการลบ <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="deleteReason" rows="3" 
                              placeholder="กรุณาระบุเหตุผลในการลบรายงานนี้..." 
                              required></textarea>
                    <div class="invalid-feedback" id="deleteReasonError">
                        กรุณากรอกเหตุผลในการลบ
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> 
                        เหตุผลในการลบจะถูกบันทึกไว้ในประวัติการดำเนินการ
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>ลบรายงาน
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

const CorruptionConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Corruption/update_status") ?>',
    deleteUrl: '<?= site_url("Corruption/delete_report") ?>',
    debug: <?= (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 'true' : 'false' ?>
};

const statusDisplayMap = {
    'pending': 'รอดำเนินการ',
    'under_review': 'กำลังตรวจสอบ',
    'investigating': 'กำลังสอบสวน',
    'resolved': 'ดำเนินการแล้ว',
    'dismissed': 'ยกเลิก',
    'closed': 'ปิดเรื่อง'
};

// ===================================================================
// *** CORE FUNCTIONS ***
// ===================================================================

/**
 * อัปเดตสถานะรายงานการทุจริตแบบตรงไปตรงมา
 */
function updateReportStatusDirect(reportId, newStatus, reportSubject, statusDisplay) {
    console.log('updateReportStatusDirect called:', reportId, newStatus, reportSubject, statusDisplay);
    
    if (!reportId || !newStatus) {
        console.error('Invalid parameters');
        showErrorAlert('ข้อมูลไม่ถูกต้อง');
        return;
    }
    
    // เตรียมข้อมูลสำหรับ Modal
    document.getElementById('statusReportId').value = reportId;
    document.getElementById('statusReportSubject').textContent = reportSubject || 'ไม่ระบุ';
    document.getElementById('statusNewStatus').value = newStatus;
    document.getElementById('statusDisplayText').textContent = statusDisplay;
    
    // แสดง Modal
    const statusModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    statusModal.show();
}

/**
 * ยืนยันการลบรายงานการทุจริต
 */
function confirmDeleteReport(reportId, reportSubject) {
    //console.log('confirmDeleteReport called:', reportId, reportSubject);
    
    if (!reportId) {
        showErrorAlert('ไม่พบหมายเลขรายงาน');
        return;
    }
    
    // ตั้งค่าข้อมูลใน Modal
    document.getElementById('deleteReportId').textContent = reportId;
    document.getElementById('deleteReportSubject').textContent = reportSubject || 'ไม่ระบุ';
    
    // ล้างข้อมูลเก่า
    const deleteReasonField = document.getElementById('deleteReason');
    const deleteReasonError = document.getElementById('deleteReasonError');
    
    deleteReasonField.value = '';
    deleteReasonField.classList.remove('is-invalid');
    deleteReasonError.style.display = 'none';
    
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
        validateAndDeleteReport(reportId, deleteModal);
    });
}

function validateAndDeleteReport(reportId, modal) {
    const deleteReasonField = document.getElementById('deleteReason');
    const deleteReasonError = document.getElementById('deleteReasonError');
    const deleteReason = deleteReasonField.value.trim();
    
    // ตรวจสอบว่ากรอกเหตุผลหรือไม่
    if (!deleteReason) {
        deleteReasonField.classList.add('is-invalid');
        deleteReasonError.style.display = 'block';
        deleteReasonError.textContent = 'กรุณากรอกเหตุผลในการลบ';
        deleteReasonField.focus();
        return;
    }
    
    // ตรวจสอบความยาวข้อความ
    if (deleteReason.length < 10) {
        deleteReasonField.classList.add('is-invalid');
        deleteReasonError.style.display = 'block';
        deleteReasonError.textContent = 'เหตุผลในการลบต้องมีอย่างน้อย 10 ตัวอักษร';
        deleteReasonField.focus();
        return;
    }
    
    if (deleteReason.length > 500) {
        deleteReasonField.classList.add('is-invalid');
        deleteReasonError.style.display = 'block';
        deleteReasonError.textContent = 'เหตุผลในการลบต้องไม่เกิน 500 ตัวอักษร';
        deleteReasonField.focus();
        return;
    }
    
    // ล้าง error state
    deleteReasonField.classList.remove('is-invalid');
    deleteReasonError.style.display = 'none';
    
    // ดำเนินการลบ
    performDeleteReport(reportId, deleteReason, modal);
}

/**
 * ดำเนินการลบรายงานการทุจริต
 */
function performDeleteReport(reportId, deleteReason, modal) {
    console.log('performDeleteReport called:', reportId, deleteReason);
    
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
    formData.append('report_id', reportId);
    formData.append('delete_reason', deleteReason);
    
    fetch(CorruptionConfig.deleteUrl, {
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
                text: data.message || 'ลบรายงานการทุจริตเรียบร้อยแล้ว',
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
 * รีเฟรชตาราง
 */
function refreshTable() {
    console.log('Refreshing table...');
    
    const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
    if (refreshBtn) {
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>กำลังโหลด...';
        refreshBtn.disabled = true;
        
        setTimeout(() => {
            location.reload();
        }, 500);
    } else {
        location.reload();
    }
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
 * แสดงไฟล์ทั้งหมด
 */
function showAllFiles(reportId) {
    console.log('Showing all files for report:', reportId);
    window.open(`${CorruptionConfig.baseUrl}Corruption/report_detail/${reportId}`, '_blank');
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
        
        fetch(CorruptionConfig.updateStatusUrl, {
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
   // console.log('🚀 Corruption Management System loading...');
    
    try {
        // Initialize core functionality
        handleStatusUpdateForm();
        handleSearchEnhancement();
        
        // เพิ่ม real-time validation สำหรับการลบ
        const deleteReasonField = document.getElementById('deleteReason');
        const deleteReasonError = document.getElementById('deleteReasonError');
        
        if (deleteReasonField) {
            deleteReasonField.addEventListener('input', function() {
                const value = this.value.trim();
                
                if (value.length > 0) {
                    this.classList.remove('is-invalid');
                    deleteReasonError.style.display = 'none';
                }
                
                // แสดงจำนวนตัวอักษร
                const charCount = value.length;
                const maxChars = 500;
                
                // อัปเดต placeholder หรือ helper text
                const helpText = this.parentNode.querySelector('.form-text');
                if (helpText) {
                    helpText.innerHTML = `
                        <i class="fas fa-info-circle"></i> 
                        เหตุผลในการลบจะถูกบันทึกไว้ในประวัติการดำเนินการ 
                        <span class="text-muted">(${charCount}/${maxChars} ตัวอักษร)</span>
                    `;
                }
                
                // เตือนเมื่อใกล้เต็ม
                if (charCount > maxChars - 50) {
                    this.classList.add('border-warning');
                } else {
                    this.classList.remove('border-warning');
                }
            });
        }
        
        if (CorruptionConfig.debug) {
            //console.log('🔧 Debug mode enabled');
            //console.log('⚙️ Configuration:', CorruptionConfig);
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