<?php
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
            default: return 'เด็กทั่วไป';
        }
    }
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== KID AW ODS SPECIFIC STYLES (ไม่ทับ header) ===== */
/* เพิ่ม namespace เฉพาะ kid-aw-ods เพื่อไม่ให้ทับกับ styles อื่น */

/* ===== ROOT VARIABLES FOR KID AW ODS ===== */
.kid-aw-ods-page {
    --kid-primary-color: #28a745;
    --kid-primary-light: #5cb85c;
    --kid-secondary-color: #f0fff4;
    --kid-success-color: #81c784;
    --kid-warning-color: #ffb74d;
    --kid-danger-color: #e57373;
    --kid-info-color: #64b5f6;
    --kid-purple-color: #ba68c8;
    --kid-light-bg: #fafbfc;
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

/* ===== GLOBAL STYLES สำหรับ KID AW ODS ===== */
.kid-aw-ods-page {
    background: linear-gradient(135deg, #f0fff4 0%, #fcfff7 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--kid-gray-700);
    min-height: 100vh;
}

.kid-aw-ods-page .kid-container-fluid {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    min-height: calc(100vh - 140px); /* ปรับให้ไม่ทับกับ navbar */
}

/* ===== PAGE HEADER สำหรับ KID ===== */
.kid-aw-ods-page .kid-page-header {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.8) 0%, rgba(92, 184, 92, 0.6) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--kid-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--kid-shadow-md);
    position: relative;
    overflow: hidden;
    margin-top: 1rem;
}

.kid-aw-ods-page .kid-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    border-radius: 50%;
}

.kid-aw-ods-page .kid-page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.08);
    position: relative;
    z-index: 1;
    color: #ffffff !important;
}

/* ===== HEADER ACTIONS (ฟันเฟือง) ===== */
.kid-aw-ods-page .kid-header-actions {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    z-index: 2;
    display: flex;
    gap: 0.75rem;
}

.kid-aw-ods-page .kid-action-btn {
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
    transition: var(--kid-transition);
    backdrop-filter: blur(10px);
}

.kid-aw-ods-page .kid-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.kid-aw-ods-page .kid-action-btn i {
    font-size: 1rem;
}

/* ===== FORMS MANAGEMENT STYLES ===== */
.kid-aw-ods-page .kid-forms-section {
    margin-bottom: 2rem;
}

.kid-aw-ods-page .kid-forms-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    padding: 2rem;
    box-shadow: var(--kid-shadow-md);
    border: 1px solid var(--kid-gray-100);
}

.kid-aw-ods-page .kid-forms-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--kid-gray-200);
}

.kid-aw-ods-page .kid-forms-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kid-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-aw-ods-page .kid-forms-actions {
    display: flex;
    gap: 0.75rem;
}

.kid-aw-ods-page .kid-form-item {
    background: var(--kid-gray-50);
    border: 1px solid var(--kid-gray-200);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: var(--kid-transition);
    position: relative;
}

.kid-aw-ods-page .kid-form-item:hover {
    border-color: var(--kid-primary-color);
    box-shadow: var(--kid-shadow-md);
    transform: translateY(-1px);
}

.kid-aw-ods-page .kid-form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.kid-aw-ods-page .kid-form-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--kid-gray-900);
    margin: 0;
    flex: 1;
}

.kid-aw-ods-page .kid-form-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.kid-aw-ods-page .kid-form-status.active {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.kid-aw-ods-page .kid-form-status.inactive {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.kid-aw-ods-page .kid-form-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: var(--kid-gray-600);
}

.kid-aw-ods-page .kid-form-meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.kid-aw-ods-page .kid-form-description {
    font-size: 0.875rem;
    color: var(--kid-gray-600);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.kid-aw-ods-page .kid-form-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.kid-aw-ods-page .kid-form-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: var(--kid-transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.kid-aw-ods-page .kid-form-btn.download {
    background: linear-gradient(135deg, var(--kid-info-color), #42a5f5);
    color: white;
}

.kid-aw-ods-page .kid-form-btn.edit {
    background: linear-gradient(135deg, var(--kid-warning-color), #ffb74d);
    color: white;
}

.kid-aw-ods-page .kid-form-btn.delete {
    background: linear-gradient(135deg, var(--kid-danger-color), #ef5350);
    color: white;
}

.kid-aw-ods-page .kid-form-btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
}

/* ===== STATISTICS CARDS สำหรับ KID ===== */
.kid-aw-ods-page .kid-stats-section {
    margin-bottom: 2rem;
}

.kid-aw-ods-page .kid-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.kid-aw-ods-page .kid-stat-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    padding: 1.5rem;
    box-shadow: var(--kid-shadow-md);
    position: relative;
    overflow: hidden;
    transition: var(--kid-transition);
    border: 1px solid var(--kid-gray-100);
}

.kid-aw-ods-page .kid-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--kid-shadow-lg);
}

.kid-aw-ods-page .kid-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--kid-primary-color), var(--kid-primary-light));
}

.kid-aw-ods-page .kid-stat-card.submitted::before { 
    background: linear-gradient(90deg, var(--kid-warning-color), #ffc107); 
}
.kid-aw-ods-page .kid-stat-card.reviewing::before { 
    background: linear-gradient(90deg, var(--kid-info-color), #42a5f5); 
}
.kid-aw-ods-page .kid-stat-card.approved::before { 
    background: linear-gradient(90deg, var(--kid-success-color), #66bb6a); 
}
.kid-aw-ods-page .kid-stat-card.rejected::before { 
    background: linear-gradient(90deg, var(--kid-danger-color), #ef5350); 
}
.kid-aw-ods-page .kid-stat-card.completed::before { 
    background: linear-gradient(90deg, var(--kid-purple-color), #ce93d8); 
}

.kid-aw-ods-page .kid-stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.kid-aw-ods-page .kid-stat-icon {
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

.kid-aw-ods-page .kid-stat-icon.total { 
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.8), rgba(92, 184, 92, 0.8)); 
}
.kid-aw-ods-page .kid-stat-icon.submitted { 
    background: linear-gradient(135deg, rgba(255, 183, 77, 0.8), rgba(255, 193, 7, 0.8)); 
}
.kid-aw-ods-page .kid-stat-icon.reviewing { 
    background: linear-gradient(135deg, rgba(100, 181, 246, 0.8), rgba(66, 165, 245, 0.8)); 
}
.kid-aw-ods-page .kid-stat-icon.approved { 
    background: linear-gradient(135deg, rgba(129, 199, 132, 0.8), rgba(102, 187, 106, 0.8)); 
}
.kid-aw-ods-page .kid-stat-icon.rejected { 
    background: linear-gradient(135deg, rgba(229, 115, 115, 0.8), rgba(239, 83, 80, 0.8)); 
}
.kid-aw-ods-page .kid-stat-icon.completed { 
    background: linear-gradient(135deg, rgba(186, 104, 200, 0.8), rgba(206, 147, 216, 0.8)); 
}

.kid-aw-ods-page .kid-stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--kid-gray-800);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.kid-aw-ods-page .kid-stat-label {
    color: var(--kid-gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

/* ===== FILTER SECTION สำหรับ KID ===== */
.kid-aw-ods-page .kid-filter-section {
    margin-bottom: 2rem;
}

.kid-aw-ods-page .kid-filter-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    padding: 2rem;
    box-shadow: var(--kid-shadow-md);
    border: 1px solid var(--kid-gray-100);
}

.kid-aw-ods-page .kid-filter-card h5 {
    color: var(--kid-gray-900);
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-aw-ods-page .kid-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.kid-aw-ods-page .kid-form-group {
    display: flex;
    flex-direction: column;
}

.kid-aw-ods-page .kid-form-label {
    font-weight: 600;
    color: var(--kid-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.kid-aw-ods-page .kid-form-select, 
.kid-aw-ods-page .kid-form-control {
    border: 2px solid var(--kid-gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--kid-transition);
    background-color: var(--kid-white);
}

.kid-aw-ods-page .kid-form-select:focus, 
.kid-aw-ods-page .kid-form-control:focus {
    border-color: var(--kid-primary-color);
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    outline: none;
}

.kid-aw-ods-page .kid-filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.kid-aw-ods-page .kid-btn {
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

.kid-aw-ods-page .kid-btn-primary {
    background: linear-gradient(135deg, var(--kid-primary-color), var(--kid-primary-light));
    color: white;
}

.kid-aw-ods-page .kid-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

.kid-aw-ods-page .kid-btn-secondary {
    background: var(--kid-gray-100);
    color: var(--kid-gray-700);
}

.kid-aw-ods-page .kid-btn-secondary:hover {
    background: var(--kid-gray-200);
    color: var(--kid-gray-800);
}

.kid-aw-ods-page .kid-btn-success {
    background: linear-gradient(135deg, var(--kid-success-color), #81c784);
    color: white;
}

.kid-aw-ods-page .kid-btn-success:hover {
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-lg);
    color: white;
}

/* ===== ANALYTICS SECTION สำหรับ KID ===== */
.kid-aw-ods-page .kid-analytics-section {
    margin-bottom: 2rem;
}

.kid-aw-ods-page .kid-analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.kid-aw-ods-page .kid-chart-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    padding: 2rem;
    box-shadow: var(--kid-shadow-md);
    border: 1px solid var(--kid-gray-100);
}

.kid-aw-ods-page .kid-chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--kid-gray-100);
}

.kid-aw-ods-page .kid-chart-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kid-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-aw-ods-page .kid-recent-kid .kid-item {
    padding: 1rem;
    border: 1px solid var(--kid-gray-100);
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: var(--kid-transition);
}

.kid-aw-ods-page .kid-recent-kid .kid-item:hover {
    border-color: var(--kid-primary-color);
    box-shadow: var(--kid-shadow-md);
}

.kid-aw-ods-page .kid-recent-kid .kid-item h6 a {
    color: var(--kid-primary-color);
    text-decoration: none;
    font-weight: 600;
}

.kid-aw-ods-page .kid-recent-kid .kid-item h6 a:hover {
    color: var(--kid-primary-light);
}

.kid-aw-ods-page .kid-type-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.kid-aw-ods-page .kid-type-stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--kid-gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--kid-primary-color);
}

.kid-aw-ods-page .kid-type-stat-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--kid-gray-700);
}

.kid-aw-ods-page .kid-type-stat-indicator {
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

.kid-aw-ods-page .kid-type-stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kid-gray-900);
}

/* ===== DATA TABLE SECTION สำหรับ KID ===== */
.kid-aw-ods-page .kid-table-section {
    margin-bottom: 2rem;
}

.kid-aw-ods-page .kid-table-card {
    background: var(--kid-white);
    border-radius: var(--kid-border-radius);
    overflow: hidden;
    box-shadow: var(--kid-shadow-md);
    border: 1px solid var(--kid-gray-100);
}

.kid-aw-ods-page .kid-table-header {
    background: var(--kid-gray-50);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--kid-gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.kid-aw-ods-page .kid-table-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kid-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-aw-ods-page .kid-table-actions {
    display: flex;
    gap: 0.5rem;
}

.kid-aw-ods-page .kid-btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

.kid-aw-ods-page .kid-btn-outline-primary {
    border: 2px solid var(--kid-primary-color);
    color: var(--kid-primary-color);
    background: transparent;
}

.kid-aw-ods-page .kid-btn-outline-primary:hover {
    background: var(--kid-primary-color);
    color: white;
}

/* ===== KID CARDS ===== */
.kid-aw-ods-page .kid-aw-ods-container {
    background: var(--kid-white);
    border: 2px solid var(--kid-gray-100);
    border-radius: var(--kid-border-radius);
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: var(--kid-shadow-md);
    transition: var(--kid-transition);
}

.kid-aw-ods-page .kid-aw-ods-container:hover {
    border-color: var(--kid-primary-light);
    box-shadow: var(--kid-shadow-lg);
    transform: translateY(-2px);
}

.kid-aw-ods-page .kid-aw-ods-header {
    background: linear-gradient(135deg, var(--kid-secondary-color) 0%, #d4edda 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--kid-gray-200);
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--kid-primary-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.kid-aw-ods-page .kid-aw-ods-number {
    background: linear-gradient(135deg, var(--kid-primary-color), var(--kid-primary-light));
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-left: auto;
}

.kid-aw-ods-page .kid-aw-ods-data-row {
    background: var(--kid-white);
    border-bottom: 1px solid var(--kid-gray-100);
    transition: var(--kid-transition);
}

.kid-aw-ods-page .kid-aw-ods-data-row:hover {
    background: var(--kid-gray-50);
}

.kid-aw-ods-page .kid-aw-ods-status-row {
    background: var(--kid-gray-50);
    border-left: 4px solid var(--kid-primary-color);
    border-bottom: none;
}

.kid-aw-ods-page .kid-table {
    margin: 0;
}

.kid-aw-ods-page .kid-table tbody td {
    padding: 1.25rem 1rem;
    border-color: var(--kid-gray-100);
    vertical-align: middle;
    font-size: 0.875rem;
}

/* ===== STATUS BADGES สำหรับ KID ===== */
.kid-aw-ods-page .kid-status-badge {
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

.kid-aw-ods-page .kid-status-badge.submitted {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border: 1px solid rgba(255, 152, 0, 0.3);
}

.kid-aw-ods-page .kid-status-badge.reviewing {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.kid-aw-ods-page .kid-status-badge.approved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.kid-aw-ods-page .kid-status-badge.rejected {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

.kid-aw-ods-page .kid-status-badge.completed {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border: 1px solid rgba(156, 39, 176, 0.3);
}

/* ===== OTHER BADGES สำหรับ KID ===== */
.kid-aw-ods-page .kid-priority-badge, 
.kid-aw-ods-page .kid-type-badge, 
.kid-aw-ods-page .kid-user-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.kid-aw-ods-page .kid-priority-badge.low {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.kid-aw-ods-page .kid-priority-badge.normal {
    background: var(--kid-gray-100);
    color: var(--kid-gray-700);
}

.kid-aw-ods-page .kid-priority-badge.high {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.kid-aw-ods-page .kid-priority-badge.urgent {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.kid-aw-ods-page .kid-type-badge.children {
    background: linear-gradient(135deg, var(--kid-secondary-color), #d4edda);
    color: var(--kid-primary-color);
}

.kid-aw-ods-page .kid-type-badge.disabled {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    color: #6a1b9a;
}

.kid-aw-ods-page .kid-user-type-badge.guest {
    background: var(--kid-gray-100);
    color: var(--kid-gray-600);
}

.kid-aw-ods-page .kid-user-type-badge.public {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.kid-aw-ods-page .kid-user-type-badge.staff {
    background: linear-gradient(135deg, var(--kid-secondary-color), #d4edda);
    color: var(--kid-primary-color);
}

/* ===== ACTION BUTTONS สำหรับ KID ===== */
.kid-aw-ods-page .kid-action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.kid-aw-ods-page .kid-btn-action {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--kid-transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 80px;
    justify-content: center;
    white-space: nowrap;
}

.kid-aw-ods-page .kid-btn-action.view {
    background: linear-gradient(135deg, rgba(100, 181, 246, 0.8), rgba(33, 150, 243, 0.8));
    color: white;
}

.kid-aw-ods-page .kid-btn-action.view:hover {
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.9), rgba(21, 101, 192, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
    color: white;
}

.kid-aw-ods-page .kid-btn-action.edit {
    background: linear-gradient(135deg, rgba(129, 199, 132, 0.8), rgba(76, 175, 80, 0.8));
    color: white;
}

.kid-aw-ods-page .kid-btn-action.edit:hover {
    background: linear-gradient(135deg, rgba(56, 142, 60, 0.9), rgba(46, 125, 50, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
    color: white;
}

.kid-aw-ods-page .kid-btn-action.assign {
    background: linear-gradient(135deg, rgba(186, 104, 200, 0.8), rgba(156, 39, 176, 0.8));
    color: white;
}

.kid-aw-ods-page .kid-btn-action.assign:hover {
    background: linear-gradient(135deg, rgba(123, 31, 162, 0.9), rgba(106, 27, 154, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
    color: white;
}

.kid-aw-ods-page .kid-btn-action.delete {
    background: linear-gradient(135deg, rgba(229, 115, 115, 0.8), rgba(244, 67, 54, 0.8));
    color: white;
}

.kid-aw-ods-page .kid-btn-action.delete:hover {
    background: linear-gradient(135deg, rgba(211, 47, 47, 0.9), rgba(198, 40, 40, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
    color: white;
}

/* ===== STATUS UPDATE BUTTONS สำหรับ KID ===== */
.kid-aw-ods-page .kid-status-cell {
    padding: 1.5rem !important;
    border-top: 1px solid var(--kid-gray-200) !important;
}

.kid-aw-ods-page .kid-status-update-row {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.kid-aw-ods-page .kid-status-label {
    font-weight: 600;
    color: var(--kid-gray-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.kid-aw-ods-page .kid-status-buttons-container {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.kid-aw-ods-page .kid-btn-status-row {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 2px solid transparent;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--kid-transition);
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

.kid-aw-ods-page .kid-btn-status-row.submitted {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border-color: rgba(255, 152, 0, 0.3);
}

.kid-aw-ods-page .kid-btn-status-row.submitted:hover:not(:disabled) {
    background: rgba(255, 152, 0, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
}

.kid-aw-ods-page .kid-btn-status-row.reviewing {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border-color: rgba(33, 150, 243, 0.3);
}

.kid-aw-ods-page .kid-btn-status-row.reviewing:hover:not(:disabled) {
    background: rgba(33, 150, 243, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
}

.kid-aw-ods-page .kid-btn-status-row.approved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border-color: rgba(76, 175, 80, 0.3);
}

.kid-aw-ods-page .kid-btn-status-row.approved:hover:not(:disabled) {
    background: rgba(76, 175, 80, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
}

.kid-aw-ods-page .kid-btn-status-row.rejected {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border-color: rgba(244, 67, 54, 0.3);
}

.kid-aw-ods-page .kid-btn-status-row.rejected:hover:not(:disabled) {
    background: rgba(244, 67, 54, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
}

.kid-aw-ods-page .kid-btn-status-row.completed {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border-color: rgba(156, 39, 176, 0.3);
}

.kid-aw-ods-page .kid-btn-status-row.completed:hover:not(:disabled) {
    background: rgba(156, 39, 176, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-md);
}

.kid-aw-ods-page .kid-btn-status-row.current {
    background: var(--kid-gray-100);
    color: var(--kid-gray-600);
    cursor: not-allowed;
    opacity: 0.8;
    border-color: var(--kid-gray-300);
}

.kid-aw-ods-page .kid-btn-status-row.current::before {
    content: "✓ ";
    font-weight: bold;
}

/* ===== เพิ่ม CSS สำหรับปุ่ม Disabled ===== */
.kid-aw-ods-page .kid-btn-status-row:disabled,
.kid-aw-ods-page .kid-btn-status-row.disabled {
    background: var(--kid-gray-100) !important;
    color: var(--kid-gray-400) !important;
    border-color: var(--kid-gray-200) !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    transform: none !important;
    box-shadow: none !important;
}

.kid-aw-ods-page .kid-btn-status-row:disabled:hover,
.kid-aw-ods-page .kid-btn-status-row.disabled:hover {
    background: var(--kid-gray-100) !important;
    color: var(--kid-gray-400) !important;
    border-color: var(--kid-gray-200) !important;
    transform: none !important;
    box-shadow: none !important;
}

.kid-aw-ods-page .kid-btn-status-row:disabled i,
.kid-aw-ods-page .kid-btn-status-row.disabled i {
    color: var(--kid-gray-400) !important;
    opacity: 0.5;
}

.kid-aw-ods-page .kid-btn-status-row:disabled::before,
.kid-aw-ods-page .kid-btn-status-row.disabled::before {
    content: "🔒 ";
    font-weight: bold;
    opacity: 0.7;
}

/* ===== FILE DISPLAY สำหรับ KID ===== */
.kid-aw-ods-page .kid-files-display {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.kid-aw-ods-page .kid-file-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    background: var(--kid-gray-50);
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    color: var(--kid-gray-600);
    border: 1px solid var(--kid-gray-200);
    transition: var(--kid-transition);
    cursor: pointer;
}

.kid-aw-ods-page .kid-file-item:hover {
    background: var(--kid-gray-100);
    border-color: var(--kid-primary-color);
    transform: translateY(-1px);
    box-shadow: var(--kid-shadow-sm);
}

.kid-aw-ods-page .kid-file-item i {
    color: var(--kid-primary-color);
    font-size: 0.875rem;
}

.kid-aw-ods-page .kid-file-item.image-file {
    padding: 0;
    border-radius: 8px;
    overflow: hidden;
    width: 40px;
    height: 40px;
    background: none;
    border: 2px solid var(--kid-gray-200);
}

.kid-aw-ods-page .kid-file-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}

.kid-aw-ods-page .kid-files-more-badge {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--kid-transition);
}

.kid-aw-ods-page .kid-files-more-badge:hover {
    transform: scale(1.05);
    box-shadow: var(--kid-shadow-md);
}

/* ===== PERSONAL INFO สำหรับ KID ===== */
.kid-aw-ods-page .kid-personal-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.kid-aw-ods-page .kid-personal-info-item {
    font-size: 0.8rem;
    color: var(--kid-gray-600);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* ===== การปรับปรุง Display Elements ===== */
.kid-aw-ods-page .kid-id-display {
    font-size: 1.1rem;
    color: var(--kid-primary-color);
    background: var(--kid-gray-50);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    border: 1px solid var(--kid-gray-200);
}

.kid-aw-ods-page .kid-date-display {
    text-align: center;
}

.kid-aw-ods-page .kid-date-part {
    font-weight: 600;
    color: var(--kid-gray-800);
    font-size: 0.9rem;
    margin-bottom: 0.2rem;
}

.kid-aw-ods-page .kid-time-part {
    color: var(--kid-gray-600);
    font-size: 0.8rem;
}

.kid-aw-ods-page .kid-name-display {
    font-size: 0.95rem;
    color: var(--kid-gray-900);
    margin-bottom: 0.3rem;
}

.kid-aw-ods-page .kid-id-card-display {
    color: var(--kid-gray-600);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.kid-aw-ods-page .kid-phone-display {
    color: var(--kid-gray-600);
    font-size: 0.8rem;
}

/* ===== PAGINATION สำหรับ KID ===== */
.kid-aw-ods-page .kid-pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--kid-gray-200);
    background: var(--kid-gray-50);
}

.kid-aw-ods-page .kid-pagination-info {
    color: var(--kid-gray-600);
    font-size: 0.875rem;
}

/* ===== EMPTY STATE สำหรับ KID ===== */
.kid-aw-ods-page .kid-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--kid-gray-500);
}

.kid-aw-ods-page .kid-empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.kid-aw-ods-page .kid-empty-state h5 {
    color: var(--kid-gray-600);
    margin-bottom: 0.5rem;
}

/* ===== RESPONSIVE DESIGN สำหรับ KID ===== */
@media (max-width: 768px) {
    .kid-aw-ods-page .kid-container-fluid {
        padding: 1rem;
        min-height: calc(100vh - 120px);
    }
    
    .kid-aw-ods-page .kid-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
        margin-top: 0.5rem;
    }
    
    .kid-aw-ods-page .kid-page-header h1 {
        font-size: 1.5rem;
    }
    
    .kid-aw-ods-page .kid-header-actions {
        position: relative;
        top: auto;
        right: auto;
        margin-top: 1rem;
        flex-direction: column;
        align-items: stretch;
    }
    
    .kid-aw-ods-page .kid-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .kid-aw-ods-page .kid-analytics-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .kid-aw-ods-page .kid-filter-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .kid-aw-ods-page .kid-filter-actions {
        justify-content: stretch;
    }
    
    .kid-aw-ods-page .kid-filter-actions .kid-btn {
        flex: 1;
    }
    
    .kid-aw-ods-page .kid-table-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .kid-aw-ods-page .kid-action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .kid-aw-ods-page .kid-btn-action {
        width: 100%;
        min-width: auto;
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    .kid-aw-ods-page .kid-status-buttons-container {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .kid-aw-ods-page .kid-btn-status-row {
        width: 100%;
        min-width: auto;
        padding: 0.5rem;
        font-size: 0.75rem;
        justify-content: flex-start;
    }
    
    .kid-aw-ods-page .kid-btn-status-row:disabled,
    .kid-aw-ods-page .kid-btn-status-row.disabled {
        background: var(--kid-gray-50) !important;
        color: var(--kid-gray-300) !important;
        border-color: var(--kid-gray-100) !important;
        opacity: 0.5 !important;
    }
    
    .kid-aw-ods-page .kid-aw-ods-container {
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    
    .kid-aw-ods-page .kid-aw-ods-header {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
    
    .kid-aw-ods-page .kid-aw-ods-number {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    
    .kid-aw-ods-page .kid-table tbody td {
        padding: 1rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .kid-aw-ods-page .kid-status-cell {
        padding: 1rem 0.75rem !important;
    }
    
    .kid-aw-ods-page .kid-status-label {
        font-size: 0.8rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 480px) {
    .kid-aw-ods-page .kid-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .kid-aw-ods-page .kid-stat-value {
        font-size: 1.8rem;
    }
    
    .kid-aw-ods-page .kid-aw-ods-header {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .kid-aw-ods-page .kid-aw-ods-number {
        margin-left: 0;
    }
}

/* ===== ANIMATIONS สำหรับ KID ===== */
@keyframes kidFadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kid-aw-ods-page .kid-aw-ods-container {
    animation: kidFadeInUp 0.3s ease-out;
}

.kid-aw-ods-page .kid-stat-card {
    animation: kidFadeInUp 0.3s ease-out;
}

.kid-aw-ods-page .kid-chart-card {
    animation: kidFadeInUp 0.3s ease-out;
}

/* ===== LOADING STATES สำหรับ KID ===== */
.kid-aw-ods-page .kid-loading {
    opacity: 0.6;
    pointer-events: none;
}

.kid-aw-ods-page .kid-loading::after {
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
    animation: kidSpin 1s linear infinite;
}

@keyframes kidSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ===== การแก้ไข Modal สำหรับ kid ===== */
.kid-aw-ods-page .modal-content {
    border: none;
    border-radius: var(--kid-border-radius);
    box-shadow: var(--kid-shadow-xl);
}

.kid-aw-ods-page .modal-header {
    border-bottom: 1px solid var(--kid-gray-200);
    padding: 1.5rem 2rem;
}

.kid-aw-ods-page .modal-title {
    font-weight: 700;
    color: var(--kid-gray-900);
}

.kid-aw-ods-page .modal-body {
    padding: 2rem;
}

.kid-aw-ods-page .modal-footer {
    border-top: 1px solid var(--kid-gray-200);
    padding: 1.5rem 2rem;
}

/* ===== การแก้ไข Form Control สำหรับ kid ===== */
.kid-aw-ods-page .form-control,
.kid-aw-ods-page .form-select {
    border: 2px solid var(--kid-gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--kid-transition);
    background-color: var(--kid-white);
}

.kid-aw-ods-page .form-control:focus,
.kid-aw-ods-page .form-select:focus {
    border-color: var(--kid-primary-color);
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    outline: none;
}

.kid-aw-ods-page .form-label {
    font-weight: 600;
    color: var(--kid-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

/* ===== การแก้ไข Alert สำหรับ kid ===== */
.kid-aw-ods-page .alert {
    border-radius: var(--kid-border-radius);
    border: none;
    padding: 1rem 1.5rem;
}

.kid-aw-ods-page .alert-info {
    background: linear-gradient(135deg, rgba(219, 234, 254, 0.8), rgba(191, 219, 254, 0.6));
    color: #0d47a1;
}

.kid-aw-ods-page .alert-danger {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
}

/* ===== การแก้ไข Bootstrap classes ที่อาจทับ ===== */
.kid-aw-ods-page .btn:not([class*="kid-btn"]) {
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

.kid-aw-ods-page .btn-primary:not([class*="kid-btn"]) {
    background: linear-gradient(135deg, var(--kid-primary-color), var(--kid-primary-light));
    color: white;
}

.kid-aw-ods-page .btn-secondary:not([class*="kid-btn"]) {
    background: var(--kid-gray-100);
    color: var(--kid-gray-700);
}

.kid-aw-ods-page .btn-success:not([class*="kid-btn"]) {
    background: linear-gradient(135deg, var(--kid-success-color), #81c784);
    color: white;
}

.kid-aw-ods-page .btn-danger:not([class*="kid-btn"]) {
    background: linear-gradient(135deg, var(--kid-danger-color), #ef5350);
    color: white;
}
</style>

<div class="kid-aw-ods-page">
    <div class="kid-container-fluid">
        <!-- ===== PAGE HEADER ===== -->
        <header class="kid-page-header">
            <h1><i class="fas fa-baby me-3"></i>จัดการการยื่นเงินอุดหนุนเด็กแรกเกิด</h1>
            
            <!-- Header Actions (ฟันเฟือง) -->
            <div class="kid-header-actions">
                <a href="<?= site_url('Kid_aw_ods/manage_forms') ?>" class="kid-action-btn" title="จัดการฟอร์ม">
                    <i class="fas fa-cog"></i>
                    <span>จัดการฟอร์ม</span>
                </a>
                <!-- <a href="<?= site_url('Kid_aw_ods/kid_tracking_admin') ?>" class="kid-action-btn" title="ติดตามสถานะ">
                    <i class="fas fa-search"></i>
                    <span>ติดตาม</span>
                </a> -->
            </div>
        </header>

        <!-- ===== STATISTICS SECTION ===== -->
        <section class="kid-stats-section">
            <div class="kid-stats-grid">
                <div class="kid-stat-card total">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon total">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= number_format($kid_summary['total'] ?? 0) ?></div>
                    <div class="kid-stat-label">ทั้งหมด</div>
                </div>

                <div class="kid-stat-card submitted">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon submitted">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= number_format($kid_summary['by_status']['submitted'] ?? 0) ?></div>
                    <div class="kid-stat-label">ยื่นเรื่องแล้ว</div>
                </div>

                <div class="kid-stat-card reviewing">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon reviewing">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= number_format($kid_summary['by_status']['reviewing'] ?? 0) ?></div>
                    <div class="kid-stat-label">กำลังพิจารณา</div>
                </div>

                <div class="kid-stat-card approved">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= number_format($kid_summary['by_status']['approved'] ?? 0) ?></div>
                    <div class="kid-stat-label">อนุมัติแล้ว</div>
                </div>

                <div class="kid-stat-card completed">
                    <div class="kid-stat-header">
                        <div class="kid-stat-icon completed">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                    <div class="kid-stat-value"><?= number_format($kid_summary['by_status']['completed'] ?? 0) ?></div>
                    <div class="kid-stat-label">เสร็จสิ้น</div>
                </div>
            </div>
        </section>

        <!-- ===== FILTER SECTION ===== -->
        <section class="kid-filter-section">
            <div class="kid-filter-card">
                <h5><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
                <form method="GET" action="<?= site_url('Kid_aw_ods/kid_aw_ods') ?>" id="filterForm">
                    <div class="kid-filter-grid">
                        <div class="kid-form-group">
                            <label class="kid-form-label">วันที่เริ่มต้น:</label>
                            <input type="date" class="kid-form-control" name="date_from" 
                                   value="<?= $filters['date_from'] ?? '' ?>">
                        </div>

                        <div class="kid-form-group">
                            <label class="kid-form-label">วันที่สิ้นสุด:</label>
                            <input type="date" class="kid-form-control" name="date_to" 
                                   value="<?= $filters['date_to'] ?? '' ?>">
                        </div>

                        <div class="kid-form-group">
                            <label class="kid-form-label">ค้นหา:</label>
                            <input type="text" class="kid-form-control" name="search" 
                                   placeholder="ค้นหาหมายเลข, ชื่อ, เบอร์โทร..."
                                   value="<?= $filters['search'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="kid-filter-actions">
                        <button type="submit" class="kid-btn kid-btn-primary">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <a href="<?= site_url('Kid_aw_ods/kid_aw_ods') ?>" class="kid-btn kid-btn-secondary">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </a>
                        <a href="<?= site_url('Kid_aw_ods/export_excel') ?>" class="kid-btn kid-btn-success">
                            <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- ===== ANALYTICS SECTION ===== -->
        <section class="kid-analytics-section">
            <div class="kid-analytics-grid">
                <!-- Recent Kid AW ODS -->
                <div class="kid-chart-card">
                    <div class="kid-chart-header">
                        <h3 class="kid-chart-title">
                            <i class="fas fa-clock me-2"></i>เงินอุดหนุนเด็กแรกเกิดล่าสุด
                        </h3>
                    </div>
                    <div class="kid-recent-kid">
                        <?php if (isset($recent_kid) && !empty($recent_kid)): ?>
                            <?php foreach (array_slice($recent_kid, 0, 5) as $recent): ?>
                                <div class="kid-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">
                                            <a href="<?= site_url('Kid_aw_ods/kid_detail/' . $recent->kid_aw_ods_id) ?>">
                                                #<?= $recent->kid_aw_ods_id ?> - <?= htmlspecialchars(mb_substr($recent->kid_aw_ods_by, 0, 20)) ?>
                                                <?= mb_strlen($recent->kid_aw_ods_by) > 20 ? '...' : '' ?>
                                            </a>
                                        </h6>
                                        <span class="kid-status-badge <?= get_kid_aw_ods_status_class($recent->kid_aw_ods_status) ?>">
                                            <?= get_kid_aw_ods_status_display($recent->kid_aw_ods_status) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        ประเภท: <?= get_kid_aw_ods_type_display($recent->kid_aw_ods_type ?? 'children') ?> 
                                        | โทร: <?= htmlspecialchars($recent->kid_aw_ods_phone) ?>
                                        | <?php 
                                            $thai_months = [
                                                '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                            ];
                                            
                                            $date = date('j', strtotime($recent->kid_aw_ods_datesave));
                                            $month = $thai_months[date('m', strtotime($recent->kid_aw_ods_datesave))];
                                            $year = date('Y', strtotime($recent->kid_aw_ods_datesave)) + 543;
                                            $time = date('H:i', strtotime($recent->kid_aw_ods_datesave));
                                            
                                            echo $date . ' ' . $month . ' ' . $year . ' ' . $time;
                                        ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="kid-empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>ยังไม่มีข้อมูลเงินอุดหนุนเด็กแรกเกิด</h5>
                                <p>ยังไม่มีการยื่นเรื่องเงินอุดหนุนเด็กแรกเกิด</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Type Statistics -->
                <div class="kid-chart-card">
                    <div class="kid-chart-header">
                        <h3 class="kid-chart-title">
                            <i class="fas fa-chart-pie me-2"></i>สถิติตามประเภท
                        </h3>
                    </div>
                    <div class="kid-type-stats">
                        <?php 
                        $type_labels = [
                            'children' => 'เด็กแรกเกิด'
                           
                        ];
                        $type_colors = [
                            'children' => '#28a745'
                           
                        ];
                        ?>
                        <?php if (isset($kid_summary['by_type'])): ?>
                            <?php foreach ($kid_summary['by_type'] as $type => $count): ?>
                                <div class="kid-type-stat-item">
                                    <div class="kid-type-stat-label">
                                        <div class="kid-type-stat-indicator" style="background-color: <?= $type_colors[$type] ?? '#9e9e9e' ?>;"></div>
                                        <span><?= $type_labels[$type] ?? $type ?></span>
                                    </div>
                                    <div class="kid-type-stat-value"><?= number_format($count) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="kid-empty-state">
                                <i class="fas fa-chart-pie"></i>
                                <h5>ไม่มีข้อมูลสถิติ</h5>
                                <p>ยังไม่มีข้อมูลเงินอุดหนุนเด็กแรกเกิดเพื่อแสดงสถิติ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== DATA TABLE SECTION ===== -->
        <section class="kid-table-section">
            <div class="kid-table-card">
                <div class="kid-table-header">
                    <h5 class="kid-table-title">
                        <i class="fas fa-list me-2"></i>รายการเงินอุดหนุนเด็กแรกเกิด

                    </h5>
                    <div class="kid-table-actions">
                        <button class="kid-btn kid-btn-outline-primary kid-btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                        </button>
                    </div>
                </div>
                
                <div class="kid-table-content">
                    <?php if (empty($kid_aw_ods)): ?>
                        <div class="kid-empty-state">
                            <i class="fas fa-baby"></i>
                            <h5>ไม่พบข้อมูลเงินสนับสนุนเด็ก</h5>
                            <p>กรุณาลองใช้ตัวกรองอื่น หรือเพิ่มข้อมูลใหม่</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($kid_aw_ods as $kid): ?>
                            <div class="kid-aw-ods-container" data-kid-id="<?= $kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'] ?>">
                                <!-- Kid AW ODS Header -->
                                <div class="kid-aw-ods-header">
                                    <i class="fas fa-baby"></i>
                                    <span>เงินสนับสนุนเด็ก <?= get_kid_aw_ods_type_display($kid->kid_aw_ods_type ?? $kid['kid_aw_ods_type'] ?? 'children') ?></span>
                                    <span class="kid-aw-ods-number">#<?= $kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'] ?></span>
                                </div>
                                
                                <!-- Kid AW ODS Content -->
                                <table class="kid-table mb-0">
                                    <tbody>
                                        <!-- Kid AW ODS Data Row -->
                                        <tr class="kid-aw-ods-data-row">
                                            <td style="width: 8%;">
                                                <div class="text-center">
                                                    <strong class="kid-id-display"><?= $kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'] ?></strong>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="kid-date-display">
                                                    <?php 
                                                    $thai_months = [
                                                        '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                        '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                        '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                                    ];
                                                    
                                                    $datesave = $kid->kid_aw_ods_datesave ?? $kid['kid_aw_ods_datesave'];
                                                    $date = date('j', strtotime($datesave));
                                                    $month = $thai_months[date('m', strtotime($datesave))];
                                                    $year = date('Y', strtotime($datesave)) + 543;
                                                    $time = date('H:i', strtotime($datesave));
                                                    ?>
                                                    <div class="kid-date-part"><?= $date ?> <?= $month ?> <?= $year ?></div>
                                                    <div class="kid-time-part"><?= $time ?> น.</div>
                                                </div>
                                            </td>
                                            <td style="width: 15%;">
                                                <div class="text-center">
                                                    <span class="kid-status-badge <?= get_kid_aw_ods_status_class($kid->kid_aw_ods_status ?? $kid['kid_aw_ods_status']) ?>">
                                                        <?= get_kid_aw_ods_status_display($kid->kid_aw_ods_status ?? $kid['kid_aw_ods_status']) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="text-center">
                                                    <span class="kid-priority-badge <?= $kid->kid_aw_ods_priority ?? $kid['kid_aw_ods_priority'] ?? 'normal' ?>">
                                                        <?php
                                                        $priority_labels = [
                                                            'low' => 'ต่ำ',
                                                            'normal' => 'ปกติ', 
                                                            'high' => 'สูง',
                                                            'urgent' => 'เร่งด่วน'
                                                        ];
                                                        echo $priority_labels[$kid->kid_aw_ods_priority ?? $kid['kid_aw_ods_priority'] ?? 'normal'];
                                                        ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="text-center">
                                                    <span class="kid-type-badge <?= $kid->kid_aw_ods_type ?? $kid['kid_aw_ods_type'] ?? 'children' ?>">
                                                        <?= get_kid_aw_ods_type_display($kid->kid_aw_ods_type ?? $kid['kid_aw_ods_type'] ?? 'children') ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="kid-files-display">
                                                    <?php if (!empty($kid->files)): ?>
                                                        <?php 
                                                        $displayFiles = array_slice($kid->files, 0, 2);
                                                        $remainingCount = count($kid->files) - count($displayFiles);
                                                        ?>
                                                        
                                                        <?php foreach ($displayFiles as $file): ?>
                                                            <?php 
                                                            $fileExtension = strtolower(pathinfo($file->kid_aw_ods_file_original_name, PATHINFO_EXTENSION));
                                                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            ?>
                                                            
                                                            <?php if ($isImage): ?>
                                                                <div class="kid-file-item image-file" 
                                                                     onclick="showImagePreview('<?= site_url('uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name) ?>', '<?= htmlspecialchars($file->kid_aw_ods_file_original_name, ENT_QUOTES) ?>')"
                                                                     title="<?= htmlspecialchars($file->kid_aw_ods_file_original_name) ?>">
                                                                    <img src="<?= site_url('uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name) ?>" 
                                                                         alt="<?= htmlspecialchars($file->kid_aw_ods_file_original_name) ?>" 
                                                                         class="kid-file-preview-img"
                                                                         loading="lazy">
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="kid-file-item" 
                                                                     onclick="downloadFile('<?= site_url('uploads/kid_aw_ods/' . $file->kid_aw_ods_file_name) ?>', '<?= htmlspecialchars($file->kid_aw_ods_file_original_name, ENT_QUOTES) ?>')"
                                                                     title="<?= htmlspecialchars($file->kid_aw_ods_file_original_name) ?>">
                                                                    <i class="fas fa-file"></i>
                                                                    <span class="file-name"><?= mb_substr($file->kid_aw_ods_file_original_name, 0, 6) ?><?= mb_strlen($file->kid_aw_ods_file_original_name) > 6 ? '...' : '' ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                        
                                                        <?php if ($remainingCount > 0): ?>
                                                            <div class="kid-files-more-badge" 
                                                                 onclick="showAllFiles('<?= $kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'] ?>')"
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
                                                <div class="kid-personal-info">
                                                    <div class="kid-personal-info-item kid-name-display">
                                                        <strong><?= htmlspecialchars($kid->kid_aw_ods_by ?? $kid['kid_aw_ods_by']) ?></strong>
                                                    </div>
                                                    <?php if (!empty($kid->kid_aw_ods_number ?? $kid['kid_aw_ods_number'])): ?>
                                                        <div class="kid-personal-info-item kid-id-card-display">
                                                            <i class="fas fa-id-card me-1"></i> 
                                                            <?php 
                                                            $id_card = $kid->kid_aw_ods_number ?? $kid['kid_aw_ods_number'];
                                                            echo htmlspecialchars(substr($id_card, 0, 3) . '-****-****-**-' . substr($id_card, -2));
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($kid->kid_aw_ods_phone ?? $kid['kid_aw_ods_phone'])): ?>
                                                        <div class="kid-personal-info-item kid-phone-display">
                                                            <i class="fas fa-phone me-1"></i> 
                                                            <?= htmlspecialchars($kid->kid_aw_ods_phone ?? $kid['kid_aw_ods_phone']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="text-center">
                                                    <span class="kid-user-type-badge <?= $kid->kid_aw_ods_user_type ?? $kid['kid_aw_ods_user_type'] ?? 'guest' ?>">
                                                        <?php
                                                        $user_type_labels = [
                                                            'guest' => 'ผู้ใช้ทั่วไป',
                                                            'public' => 'สมาชิก',
                                                            'staff' => 'เจ้าหน้าที่'
                                                        ];
                                                        echo $user_type_labels[$kid->kid_aw_ods_user_type ?? $kid['kid_aw_ods_user_type'] ?? 'guest'];
                                                        ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 13%;">
                                                <div class="kid-action-buttons">
                                                    <a href="<?= site_url('Kid_aw_ods/kid_detail/' . ($kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'])) ?>" 
                                                       class="kid-btn-action view" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>ดู
                                                    </a>
                                                    
                                                    <?php if ($can_delete_kid ?? false): ?>
                                                        <button type="button" 
                                                                class="kid-btn-action delete" 
                                                                onclick="confirmDeleteKid('<?= $kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'] ?>', '<?= htmlspecialchars($kid->kid_aw_ods_by ?? $kid['kid_aw_ods_by'], ENT_QUOTES) ?>')"
                                                                title="ลบข้อมูล">
                                                            <i class="fas fa-trash"></i>ลบ
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Kid AW ODS Status Management Row -->
                                        <tr class="kid-aw-ods-status-row">
                                            <td colspan="9" class="kid-status-cell">
                                                <div class="kid-status-update-row">
                                                    <div class="kid-status-label">
                                                        <i class="fas fa-sync-alt"></i>
                                                        อัปเดตสถานะ #<?= $kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'] ?>
                                                    </div>
                                                    <div class="kid-status-buttons-container">
                                                        <?php 
                                                        $current_status = $kid->kid_aw_ods_status ?? $kid['kid_aw_ods_status'];
                                                        $can_handle = $can_update_status ?? false;

                                                        // กำหนดสถานะที่สามารถเปลี่ยนได้ตามเงื่อนไข
                                                        switch($current_status) {
                                                            case 'submitted': // ยื่นเรื่องมา
                                                                $available_statuses = ['reviewing']; // เฉพาะ กำลังพิจารณา
                                                                break;
                                                            case 'reviewing': // กำลังพิจารณา
                                                                $available_statuses = ['approved', 'rejected'];
                                                                break;
                                                            case 'approved': // อนุมัติแล้ว
                                                            case 'rejected': // ไม่อนุมัติ
                                                                $available_statuses = ['completed'];
                                                                break;
                                                            case 'completed': // เสร็จสิ้น
                                                                $available_statuses = []; // ไม่สามารถเปลี่ยนได้แล้ว
                                                                break;
                                                        }
                                                        
                                                        $all_status_buttons = [
                                                            'submitted' => ['submitted', 'fas fa-file-alt', 'ยื่นเรื่องแล้ว'],
                                                            'reviewing' => ['reviewing', 'fas fa-search', 'กำลังพิจารณา'],
                                                            'approved' => ['approved', 'fas fa-check-circle', 'อนุมัติแล้ว'],
                                                            'rejected' => ['rejected', 'fas fa-times-circle', 'ไม่อนุมัติ'],
                                                            'completed' => ['completed', 'fas fa-trophy', 'เสร็จสิ้น']
                                                        ];
                                                        
                                                        foreach ($all_status_buttons as $status_key => $status_info): 
                                                            $status_class = $status_info[0];
                                                            $status_icon = $status_info[1];
                                                            $status_display = $status_info[2];
                                                            
                                                            $is_current = ($current_status === $status_key);
                                                            $is_available = in_array($status_key, $available_statuses);
                                                            $is_clickable = ($can_update_status && $is_available);

                                                            $button_classes = "kid-btn-status-row {$status_class}";
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
                                                                $kid_id_js = htmlspecialchars($kid->kid_aw_ods_id ?? $kid['kid_aw_ods_id'], ENT_QUOTES);
                                                                $by_js = htmlspecialchars($kid->kid_aw_ods_by ?? $kid['kid_aw_ods_by'], ENT_QUOTES);
                                                                $onclick_code = "onclick=\"updateKidStatusDirect('{$kid_id_js}', '{$status_key}', '{$by_js}', '{$status_display}')\"";
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
                    <div class="kid-pagination-container">
                        <div class="kid-pagination-info">
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
                    <i class="fas fa-sync-alt me-2"></i>อัปเดตสถานะเงินอุดหนุนเด็กแรกเกิด
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="statusKidId" name="kid_id">
                    <input type="hidden" id="statusNewStatus" name="new_status">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ผู้ยื่นเรื่อง:</strong> <span id="statusKidBy"></span>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-arrow-right me-2"></i>
                        <strong>เปลี่ยนสถานะเป็น:</strong> <span id="statusDisplayText" class="fw-bold"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ความสำคัญ:</label>
                        <select class="form-select" id="statusNewPriority" name="new_priority">
                            <option value="normal">ปกติ</option>
                            <option value="high">สูง</option>
                            <option value="urgent">เร่งด่วน</option>
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
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-warning me-2"></i>
                    <strong>คำเตือน:</strong> การดำเนินการนี้ไม่สามารถยกเลิกได้!
                </div>
                
                <p>คุณต้องการลบข้อมูลเงินอุดหนุนเด็กแรกเกิดนี้หรือไม่?</p>
                
                <div class="bg-light p-3 rounded">
                    <strong>หมายเลข:</strong> #<span id="deleteKidId"></span><br>
                    <strong>ผู้ยื่นเรื่อง:</strong> <span id="deleteKidBy"></span>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">เหตุผลในการลบ (ไม่บังคับ):</label>
                    <textarea class="form-control" id="deleteReason" rows="3" 
                              placeholder="ระบุเหตุผลในการลบข้อมูลนี้..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>ลบข้อมูล
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

const KidAwOdsConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Kid_aw_ods/update_kid_status") ?>',
    deleteUrl: '<?= site_url("Kid_aw_ods/delete_kid") ?>',
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
 * อัปเดตสถานะเงินสนับสนุนเด็กแบบตรงไปตรงมา
 */
function updateKidStatusDirect(kidId, newStatus, kidBy, statusDisplay) {
    console.log('updateKidStatusDirect called:', kidId, newStatus, kidBy, statusDisplay);
    
    if (!kidId || !newStatus) {
        console.error('Invalid parameters');
        showErrorAlert('ข้อมูลไม่ถูกต้อง');
        return;
    }
    
    // เตรียมข้อมูลสำหรับ Modal
    document.getElementById('statusKidId').value = kidId;
    document.getElementById('statusKidBy').textContent = kidBy || 'ไม่ระบุ';
    document.getElementById('statusNewStatus').value = newStatus;
    document.getElementById('statusDisplayText').textContent = statusDisplay;
    
    // แสดง Modal
    const statusModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    statusModal.show();
}

/**
 * ยืนยันการลบข้อมูลเงินสนับสนุนเด็ก
 */
function confirmDeleteKid(kidId, kidBy) {
    console.log('confirmDeleteKid called:', kidId, kidBy);
    
    if (!kidId) {
        showErrorAlert('ไม่พบหมายเลขข้อมูลเงินสนับสนุนเด็ก');
        return;
    }
    
    // ตั้งค่าข้อมูลใน Modal
    document.getElementById('deleteKidId').textContent = kidId;
    document.getElementById('deleteKidBy').textContent = kidBy || 'ไม่ระบุ';
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
        performDeleteKid(kidId, deleteReason, deleteModal);
    });
}

/**
 * ดำเนินการลบข้อมูลเงินสนับสนุนเด็ก
 */
function performDeleteKid(kidId, deleteReason, modal) {
    console.log('performDeleteKid called:', kidId, deleteReason);
    
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
    formData.append('kid_id', kidId);
    if (deleteReason) {
        formData.append('delete_reason', deleteReason);
    }
    
    fetch(KidAwOdsConfig.deleteUrl, {
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
 * แสดงไฟล์ทั้งหมด
 */
function showAllFiles(kidId) {
    console.log('Showing all files for kid:', kidId);
    window.open(`${KidAwOdsConfig.baseUrl}Kid_aw_ods/kid_detail/${kidId}`, '_blank');
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
        console.log('Submitting status update form');
        
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
        
        fetch(KidAwOdsConfig.updateStatusUrl, {
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
    console.log('🚀 Kid AW ODS Management System loading...');
    
    try {
        // Initialize core functionality
        handleStatusUpdateForm();
        handleSearchEnhancement();
        
        //console.log('✅ Kid AW ODS Management System initialized successfully');
        
        if (KidAwOdsConfig.debug) {
            console.log('🔧 Debug mode enabled');
            console.log('⚙️ Configuration:', KidAwOdsConfig);
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

//console.log("💼 Kid AW ODS Management System loaded successfully");
//console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
//console.log("📊 System Status: Ready");
</script>