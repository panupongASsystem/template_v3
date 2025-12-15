<?php
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
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== ELDERLY AW ODS SPECIFIC STYLES (ไม่ทับ header) ===== */
/* เพิ่ม namespace เฉพาะ elderly-aw-ods เพื่อไม่ให้ทับกับ styles อื่น */

/* ===== ROOT VARIABLES FOR ELDERLY AW ODS ===== */
.elderly-aw-ods-page {
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

/* ===== GLOBAL STYLES สำหรับ ELDERLY AW ODS ===== */
.elderly-aw-ods-page {
    background: linear-gradient(135deg, #f8fbff 0%, #fcf7ff 100%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    color: var(--elderly-gray-700);
    min-height: 100vh;
}

.elderly-aw-ods-page .elderly-container-fluid {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    min-height: calc(100vh - 140px); /* ปรับให้ไม่ทับกับ navbar */
}

/* ===== PAGE HEADER สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-page-header {
    background: linear-gradient(135deg, rgba(125, 179, 232, 0.8) 0%, rgba(165, 201, 234, 0.6) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--elderly-border-radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--elderly-shadow-md);
    position: relative;
    overflow: hidden;
    margin-top: 1rem;
}

.elderly-aw-ods-page .elderly-page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    border-radius: 50%;
}

.elderly-aw-ods-page .elderly-page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.08);
    position: relative;
    z-index: 1;
    color: #ffffff !important;
}

/* ===== HEADER ACTIONS (ฟันเฟือง) ===== */
.elderly-aw-ods-page .elderly-header-actions {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    z-index: 2;
    display: flex;
    gap: 0.75rem;
}

.elderly-aw-ods-page .elderly-action-btn {
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
    transition: var(--elderly-transition);
    backdrop-filter: blur(10px);
}

.elderly-aw-ods-page .elderly-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.elderly-aw-ods-page .elderly-action-btn i {
    font-size: 1rem;
}

/* ===== FORMS MANAGEMENT STYLES ===== */
.elderly-aw-ods-page .elderly-forms-section {
    margin-bottom: 2rem;
}

.elderly-aw-ods-page .elderly-forms-card {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    padding: 2rem;
    box-shadow: var(--elderly-shadow-md);
    border: 1px solid var(--elderly-gray-100);
}

.elderly-aw-ods-page .elderly-forms-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--elderly-gray-200);
}

.elderly-aw-ods-page .elderly-forms-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--elderly-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.elderly-aw-ods-page .elderly-forms-actions {
    display: flex;
    gap: 0.75rem;
}

.elderly-aw-ods-page .elderly-form-item {
    background: var(--elderly-gray-50);
    border: 1px solid var(--elderly-gray-200);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: var(--elderly-transition);
    position: relative;
}

.elderly-aw-ods-page .elderly-form-item:hover {
    border-color: var(--elderly-primary-color);
    box-shadow: var(--elderly-shadow-md);
    transform: translateY(-1px);
}

.elderly-aw-ods-page .elderly-form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.elderly-aw-ods-page .elderly-form-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--elderly-gray-900);
    margin: 0;
    flex: 1;
}

.elderly-aw-ods-page .elderly-form-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.elderly-aw-ods-page .elderly-form-status.active {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.elderly-aw-ods-page .elderly-form-status.inactive {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.elderly-aw-ods-page .elderly-form-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: var(--elderly-gray-600);
}

.elderly-aw-ods-page .elderly-form-meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.elderly-aw-ods-page .elderly-form-description {
    font-size: 0.875rem;
    color: var(--elderly-gray-600);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.elderly-aw-ods-page .elderly-form-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.elderly-aw-ods-page .elderly-form-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: var(--elderly-transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.elderly-aw-ods-page .elderly-form-btn.download {
    background: linear-gradient(135deg, var(--elderly-info-color), #42a5f5);
    color: white;
}

.elderly-aw-ods-page .elderly-form-btn.edit {
    background: linear-gradient(135deg, var(--elderly-warning-color), #ffb74d);
    color: white;
}

.elderly-aw-ods-page .elderly-form-btn.delete {
    background: linear-gradient(135deg, var(--elderly-danger-color), #ef5350);
    color: white;
}

.elderly-aw-ods-page .elderly-form-btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
}

/* ===== STATISTICS CARDS สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-stats-section {
    margin-bottom: 2rem;
}

.elderly-aw-ods-page .elderly-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.elderly-aw-ods-page .elderly-stat-card {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    padding: 1.5rem;
    box-shadow: var(--elderly-shadow-md);
    position: relative;
    overflow: hidden;
    transition: var(--elderly-transition);
    border: 1px solid var(--elderly-gray-100);
}

.elderly-aw-ods-page .elderly-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--elderly-shadow-lg);
}

.elderly-aw-ods-page .elderly-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--elderly-primary-color), var(--elderly-primary-light));
}

.elderly-aw-ods-page .elderly-stat-card.submitted::before { 
    background: linear-gradient(90deg, var(--elderly-warning-color), #ffc107); 
}
.elderly-aw-ods-page .elderly-stat-card.reviewing::before { 
    background: linear-gradient(90deg, var(--elderly-info-color), #42a5f5); 
}
.elderly-aw-ods-page .elderly-stat-card.approved::before { 
    background: linear-gradient(90deg, var(--elderly-success-color), #66bb6a); 
}
.elderly-aw-ods-page .elderly-stat-card.rejected::before { 
    background: linear-gradient(90deg, var(--elderly-danger-color), #ef5350); 
}
.elderly-aw-ods-page .elderly-stat-card.completed::before { 
    background: linear-gradient(90deg, var(--elderly-purple-color), #ce93d8); 
}

.elderly-aw-ods-page .elderly-stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.elderly-aw-ods-page .elderly-stat-icon {
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

.elderly-aw-ods-page .elderly-stat-icon.total { 
    background: linear-gradient(135deg, rgba(125, 179, 232, 0.8), rgba(165, 201, 234, 0.8)); 
}
.elderly-aw-ods-page .elderly-stat-icon.submitted { 
    background: linear-gradient(135deg, rgba(255, 183, 77, 0.8), rgba(255, 193, 7, 0.8)); 
}
.elderly-aw-ods-page .elderly-stat-icon.reviewing { 
    background: linear-gradient(135deg, rgba(100, 181, 246, 0.8), rgba(66, 165, 245, 0.8)); 
}
.elderly-aw-ods-page .elderly-stat-icon.approved { 
    background: linear-gradient(135deg, rgba(129, 199, 132, 0.8), rgba(102, 187, 106, 0.8)); 
}
.elderly-aw-ods-page .elderly-stat-icon.rejected { 
    background: linear-gradient(135deg, rgba(229, 115, 115, 0.8), rgba(239, 83, 80, 0.8)); 
}
.elderly-aw-ods-page .elderly-stat-icon.completed { 
    background: linear-gradient(135deg, rgba(186, 104, 200, 0.8), rgba(206, 147, 216, 0.8)); 
}

.elderly-aw-ods-page .elderly-stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--elderly-gray-800);
    margin-bottom: 0.25rem;
    line-height: 1;
}

.elderly-aw-ods-page .elderly-stat-label {
    color: var(--elderly-gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

/* ===== FILTER SECTION สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-filter-section {
    margin-bottom: 2rem;
}

.elderly-aw-ods-page .elderly-filter-card {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    padding: 2rem;
    box-shadow: var(--elderly-shadow-md);
    border: 1px solid var(--elderly-gray-100);
}

.elderly-aw-ods-page .elderly-filter-card h5 {
    color: var(--elderly-gray-900);
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.elderly-aw-ods-page .elderly-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.elderly-aw-ods-page .elderly-form-group {
    display: flex;
    flex-direction: column;
}

.elderly-aw-ods-page .elderly-form-label {
    font-weight: 600;
    color: var(--elderly-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.elderly-aw-ods-page .elderly-form-select, 
.elderly-aw-ods-page .elderly-form-control {
    border: 2px solid var(--elderly-gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--elderly-transition);
    background-color: var(--elderly-white);
}

.elderly-aw-ods-page .elderly-form-select:focus, 
.elderly-aw-ods-page .elderly-form-control:focus {
    border-color: var(--elderly-primary-color);
    box-shadow: 0 0 0 3px rgba(125, 179, 232, 0.1);
    outline: none;
}

.elderly-aw-ods-page .elderly-filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.elderly-aw-ods-page .elderly-btn {
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

.elderly-aw-ods-page .elderly-btn-primary {
    background: linear-gradient(135deg, var(--elderly-primary-color), var(--elderly-primary-light));
    color: white;
}

.elderly-aw-ods-page .elderly-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-lg);
    color: white;
}

.elderly-aw-ods-page .elderly-btn-secondary {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-700);
}

.elderly-aw-ods-page .elderly-btn-secondary:hover {
    background: var(--elderly-gray-200);
    color: var(--elderly-gray-800);
}

.elderly-aw-ods-page .elderly-btn-success {
    background: linear-gradient(135deg, var(--elderly-success-color), #81c784);
    color: white;
}

.elderly-aw-ods-page .elderly-btn-success:hover {
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-lg);
    color: white;
}

/* ===== ANALYTICS SECTION สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-analytics-section {
    margin-bottom: 2rem;
}

.elderly-aw-ods-page .elderly-analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.elderly-aw-ods-page .elderly-chart-card {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    padding: 2rem;
    box-shadow: var(--elderly-shadow-md);
    border: 1px solid var(--elderly-gray-100);
}

.elderly-aw-ods-page .elderly-chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--elderly-gray-100);
}

.elderly-aw-ods-page .elderly-chart-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--elderly-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.elderly-aw-ods-page .elderly-recent-elderly .elderly-item {
    padding: 1rem;
    border: 1px solid var(--elderly-gray-100);
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: var(--elderly-transition);
}

.elderly-aw-ods-page .elderly-recent-elderly .elderly-item:hover {
    border-color: var(--elderly-primary-color);
    box-shadow: var(--elderly-shadow-md);
}

.elderly-aw-ods-page .elderly-recent-elderly .elderly-item h6 a {
    color: var(--elderly-primary-color);
    text-decoration: none;
    font-weight: 600;
}

.elderly-aw-ods-page .elderly-recent-elderly .elderly-item h6 a:hover {
    color: var(--elderly-primary-light);
}

.elderly-aw-ods-page .elderly-type-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.elderly-aw-ods-page .elderly-type-stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--elderly-gray-50);
    border-radius: 8px;
    border-left: 4px solid var(--elderly-primary-color);
}

.elderly-aw-ods-page .elderly-type-stat-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--elderly-gray-700);
}

.elderly-aw-ods-page .elderly-type-stat-indicator {
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

.elderly-aw-ods-page .elderly-type-stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--elderly-gray-900);
}

/* ===== DATA TABLE SECTION สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-table-section {
    margin-bottom: 2rem;
}

.elderly-aw-ods-page .elderly-table-card {
    background: var(--elderly-white);
    border-radius: var(--elderly-border-radius);
    overflow: hidden;
    box-shadow: var(--elderly-shadow-md);
    border: 1px solid var(--elderly-gray-100);
}

.elderly-aw-ods-page .elderly-table-header {
    background: var(--elderly-gray-50);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--elderly-gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.elderly-aw-ods-page .elderly-table-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--elderly-gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.elderly-aw-ods-page .elderly-table-actions {
    display: flex;
    gap: 0.5rem;
}

.elderly-aw-ods-page .elderly-btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

.elderly-aw-ods-page .elderly-btn-outline-primary {
    border: 2px solid var(--elderly-primary-color);
    color: var(--elderly-primary-color);
    background: transparent;
}

.elderly-aw-ods-page .elderly-btn-outline-primary:hover {
    background: var(--elderly-primary-color);
    color: white;
}

/* ===== ELDERLY CARDS ===== */
.elderly-aw-ods-page .elderly-aw-ods-container {
    background: var(--elderly-white);
    border: 2px solid var(--elderly-gray-100);
    border-radius: var(--elderly-border-radius);
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: var(--elderly-shadow-md);
    transition: var(--elderly-transition);
}

.elderly-aw-ods-page .elderly-aw-ods-container:hover {
    border-color: var(--elderly-primary-light);
    box-shadow: var(--elderly-shadow-lg);
    transform: translateY(-2px);
}

.elderly-aw-ods-page .elderly-aw-ods-header {
    background: linear-gradient(135deg, var(--elderly-secondary-color) 0%, #c1e7ff 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--elderly-gray-200);
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--elderly-primary-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.elderly-aw-ods-page .elderly-aw-ods-number {
    background: linear-gradient(135deg, var(--elderly-primary-color), var(--elderly-primary-light));
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-left: auto;
}

.elderly-aw-ods-page .elderly-aw-ods-data-row {
    background: var(--elderly-white);
    border-bottom: 1px solid var(--elderly-gray-100);
    transition: var(--elderly-transition);
}

.elderly-aw-ods-page .elderly-aw-ods-data-row:hover {
    background: var(--elderly-gray-50);
}

.elderly-aw-ods-page .elderly-aw-ods-status-row {
    background: var(--elderly-gray-50);
    border-left: 4px solid var(--elderly-primary-color);
    border-bottom: none;
}

.elderly-aw-ods-page .elderly-table {
    margin: 0;
}

.elderly-aw-ods-page .elderly-table tbody td {
    padding: 1.25rem 1rem;
    border-color: var(--elderly-gray-100);
    vertical-align: middle;
    font-size: 0.875rem;
}

/* ===== STATUS BADGES สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-status-badge {
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

.elderly-aw-ods-page .elderly-status-badge.submitted {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border: 1px solid rgba(255, 152, 0, 0.3);
}

.elderly-aw-ods-page .elderly-status-badge.reviewing {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.elderly-aw-ods-page .elderly-status-badge.approved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.elderly-aw-ods-page .elderly-status-badge.rejected {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

.elderly-aw-ods-page .elderly-status-badge.completed {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border: 1px solid rgba(156, 39, 176, 0.3);
}

/* ===== OTHER BADGES สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-priority-badge, 
.elderly-aw-ods-page .elderly-type-badge, 
.elderly-aw-ods-page .elderly-user-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.elderly-aw-ods-page .elderly-priority-badge.low {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.elderly-aw-ods-page .elderly-priority-badge.normal {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-700);
}

.elderly-aw-ods-page .elderly-priority-badge.high {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    color: #e65100;
}

.elderly-aw-ods-page .elderly-priority-badge.urgent {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
}

.elderly-aw-ods-page .elderly-type-badge.elderly {
    background: linear-gradient(135deg, var(--elderly-secondary-color), #c1e7ff);
    color: var(--elderly-primary-color);
}

.elderly-aw-ods-page .elderly-type-badge.disabled {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    color: #6a1b9a;
}

.elderly-aw-ods-page .elderly-user-type-badge.guest {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-600);
}

.elderly-aw-ods-page .elderly-user-type-badge.public {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
}

.elderly-aw-ods-page .elderly-user-type-badge.staff {
    background: linear-gradient(135deg, var(--elderly-secondary-color), #c1e7ff);
    color: var(--elderly-primary-color);
}

/* ===== ACTION BUTTONS สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.elderly-aw-ods-page .elderly-btn-action {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--elderly-transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 80px;
    justify-content: center;
    white-space: nowrap;
}

.elderly-aw-ods-page .elderly-btn-action.view {
    background: linear-gradient(135deg, rgba(100, 181, 246, 0.8), rgba(33, 150, 243, 0.8));
    color: white;
}

.elderly-aw-ods-page .elderly-btn-action.view:hover {
    background: linear-gradient(135deg, rgba(25, 118, 210, 0.9), rgba(21, 101, 192, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
    color: white;
}

.elderly-aw-ods-page .elderly-btn-action.edit {
    background: linear-gradient(135deg, rgba(129, 199, 132, 0.8), rgba(76, 175, 80, 0.8));
    color: white;
}

.elderly-aw-ods-page .elderly-btn-action.edit:hover {
    background: linear-gradient(135deg, rgba(56, 142, 60, 0.9), rgba(46, 125, 50, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
    color: white;
}

.elderly-aw-ods-page .elderly-btn-action.assign {
    background: linear-gradient(135deg, rgba(186, 104, 200, 0.8), rgba(156, 39, 176, 0.8));
    color: white;
}

.elderly-aw-ods-page .elderly-btn-action.assign:hover {
    background: linear-gradient(135deg, rgba(123, 31, 162, 0.9), rgba(106, 27, 154, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
    color: white;
}

.elderly-aw-ods-page .elderly-btn-action.delete {
    background: linear-gradient(135deg, rgba(229, 115, 115, 0.8), rgba(244, 67, 54, 0.8));
    color: white;
}

.elderly-aw-ods-page .elderly-btn-action.delete:hover {
    background: linear-gradient(135deg, rgba(211, 47, 47, 0.9), rgba(198, 40, 40, 0.9));
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
    color: white;
}

/* ===== STATUS UPDATE BUTTONS สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-status-cell {
    padding: 1.5rem !important;
    border-top: 1px solid var(--elderly-gray-200) !important;
}

.elderly-aw-ods-page .elderly-status-update-row {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.elderly-aw-ods-page .elderly-status-label {
    font-weight: 600;
    color: var(--elderly-gray-700);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.elderly-aw-ods-page .elderly-status-buttons-container {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.elderly-aw-ods-page .elderly-btn-status-row {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 2px solid transparent;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--elderly-transition);
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

.elderly-aw-ods-page .elderly-btn-status-row.submitted {
    background: linear-gradient(135deg, rgba(255, 248, 225, 0.8), rgba(255, 236, 179, 0.6));
    color: #e65100;
    border-color: rgba(255, 152, 0, 0.3);
}

.elderly-aw-ods-page .elderly-btn-status-row.submitted:hover:not(:disabled) {
    background: rgba(255, 152, 0, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
}

.elderly-aw-ods-page .elderly-btn-status-row.reviewing {
    background: linear-gradient(135deg, rgba(227, 242, 253, 0.8), rgba(187, 222, 251, 0.6));
    color: #0d47a1;
    border-color: rgba(33, 150, 243, 0.3);
}

.elderly-aw-ods-page .elderly-btn-status-row.reviewing:hover:not(:disabled) {
    background: rgba(33, 150, 243, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
}

.elderly-aw-ods-page .elderly-btn-status-row.approved {
    background: linear-gradient(135deg, rgba(232, 245, 232, 0.8), rgba(200, 230, 201, 0.6));
    color: #1b5e20;
    border-color: rgba(76, 175, 80, 0.3);
}

.elderly-aw-ods-page .elderly-btn-status-row.approved:hover:not(:disabled) {
    background: rgba(76, 175, 80, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
}

.elderly-aw-ods-page .elderly-btn-status-row.rejected {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
    border-color: rgba(244, 67, 54, 0.3);
}

.elderly-aw-ods-page .elderly-btn-status-row.rejected:hover:not(:disabled) {
    background: rgba(244, 67, 54, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
}

.elderly-aw-ods-page .elderly-btn-status-row.completed {
    background: linear-gradient(135deg, rgba(243, 229, 245, 0.8), rgba(225, 190, 231, 0.6));
    color: #4a148c;
    border-color: rgba(156, 39, 176, 0.3);
}

.elderly-aw-ods-page .elderly-btn-status-row.completed:hover:not(:disabled) {
    background: rgba(156, 39, 176, 0.8);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-md);
}

.elderly-aw-ods-page .elderly-btn-status-row.current {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-600);
    cursor: not-allowed;
    opacity: 0.8;
    border-color: var(--elderly-gray-300);
}

.elderly-aw-ods-page .elderly-btn-status-row.current::before {
    content: "✓ ";
    font-weight: bold;
}

/* ===== เพิ่ม CSS สำหรับปุ่ม Disabled ===== */
.elderly-aw-ods-page .elderly-btn-status-row:disabled,
.elderly-aw-ods-page .elderly-btn-status-row.disabled {
    background: var(--elderly-gray-100) !important;
    color: var(--elderly-gray-400) !important;
    border-color: var(--elderly-gray-200) !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    transform: none !important;
    box-shadow: none !important;
}

.elderly-aw-ods-page .elderly-btn-status-row:disabled:hover,
.elderly-aw-ods-page .elderly-btn-status-row.disabled:hover {
    background: var(--elderly-gray-100) !important;
    color: var(--elderly-gray-400) !important;
    border-color: var(--elderly-gray-200) !important;
    transform: none !important;
    box-shadow: none !important;
}

.elderly-aw-ods-page .elderly-btn-status-row:disabled i,
.elderly-aw-ods-page .elderly-btn-status-row.disabled i {
    color: var(--elderly-gray-400) !important;
    opacity: 0.5;
}

.elderly-aw-ods-page .elderly-btn-status-row:disabled::before,
.elderly-aw-ods-page .elderly-btn-status-row.disabled::before {
    content: "🔒 ";
    font-weight: bold;
    opacity: 0.7;
}

/* ===== FILE DISPLAY สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-files-display {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.elderly-aw-ods-page .elderly-file-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    background: var(--elderly-gray-50);
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    color: var(--elderly-gray-600);
    border: 1px solid var(--elderly-gray-200);
    transition: var(--elderly-transition);
    cursor: pointer;
}

.elderly-aw-ods-page .elderly-file-item:hover {
    background: var(--elderly-gray-100);
    border-color: var(--elderly-primary-color);
    transform: translateY(-1px);
    box-shadow: var(--elderly-shadow-sm);
}

.elderly-aw-ods-page .elderly-file-item i {
    color: var(--elderly-primary-color);
    font-size: 0.875rem;
}

.elderly-aw-ods-page .elderly-file-item.image-file {
    padding: 0;
    border-radius: 8px;
    overflow: hidden;
    width: 40px;
    height: 40px;
    background: none;
    border: 2px solid var(--elderly-gray-200);
}

.elderly-aw-ods-page .elderly-file-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}

.elderly-aw-ods-page .elderly-files-more-badge {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--elderly-transition);
}

.elderly-aw-ods-page .elderly-files-more-badge:hover {
    transform: scale(1.05);
    box-shadow: var(--elderly-shadow-md);
}

/* ===== PERSONAL INFO สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-personal-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.elderly-aw-ods-page .elderly-personal-info-item {
    font-size: 0.8rem;
    color: var(--elderly-gray-600);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* ===== การปรับปรุง Display Elements ===== */
.elderly-aw-ods-page .elderly-id-display {
    font-size: 1.1rem;
    color: var(--elderly-primary-color);
    background: var(--elderly-gray-50);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    border: 1px solid var(--elderly-gray-200);
}

.elderly-aw-ods-page .elderly-date-display {
    text-align: center;
}

.elderly-aw-ods-page .elderly-date-part {
    font-weight: 600;
    color: var(--elderly-gray-800);
    font-size: 0.9rem;
    margin-bottom: 0.2rem;
}

.elderly-aw-ods-page .elderly-time-part {
    color: var(--elderly-gray-600);
    font-size: 0.8rem;
}

.elderly-aw-ods-page .elderly-name-display {
    font-size: 0.95rem;
    color: var(--elderly-gray-900);
    margin-bottom: 0.3rem;
}

.elderly-aw-ods-page .elderly-id-card-display {
    color: var(--elderly-gray-600);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.elderly-aw-ods-page .elderly-phone-display {
    color: var(--elderly-gray-600);
    font-size: 0.8rem;
}

/* ===== PAGINATION สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--elderly-gray-200);
    background: var(--elderly-gray-50);
}

.elderly-aw-ods-page .elderly-pagination-info {
    color: var(--elderly-gray-600);
    font-size: 0.875rem;
}

/* ===== EMPTY STATE สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--elderly-gray-500);
}

.elderly-aw-ods-page .elderly-empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.elderly-aw-ods-page .elderly-empty-state h5 {
    color: var(--elderly-gray-600);
    margin-bottom: 0.5rem;
}

/* ===== RESPONSIVE DESIGN สำหรับ ELDERLY ===== */
@media (max-width: 768px) {
    .elderly-aw-ods-page .elderly-container-fluid {
        padding: 1rem;
        min-height: calc(100vh - 120px);
    }
    
    .elderly-aw-ods-page .elderly-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
        margin-top: 0.5rem;
    }
    
    .elderly-aw-ods-page .elderly-page-header h1 {
        font-size: 1.5rem;
    }
    
    .elderly-aw-ods-page .elderly-header-actions {
        position: relative;
        top: auto;
        right: auto;
        margin-top: 1rem;
        flex-direction: column;
        align-items: stretch;
    }
    
    .elderly-aw-ods-page .elderly-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .elderly-aw-ods-page .elderly-analytics-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .elderly-aw-ods-page .elderly-filter-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .elderly-aw-ods-page .elderly-filter-actions {
        justify-content: stretch;
    }
    
    .elderly-aw-ods-page .elderly-filter-actions .elderly-btn {
        flex: 1;
    }
    
    .elderly-aw-ods-page .elderly-table-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .elderly-aw-ods-page .elderly-action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .elderly-aw-ods-page .elderly-btn-action {
        width: 100%;
        min-width: auto;
        padding: 0.5rem;
        font-size: 0.75rem;
    }
    
    .elderly-aw-ods-page .elderly-status-buttons-container {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .elderly-aw-ods-page .elderly-btn-status-row {
        width: 100%;
        min-width: auto;
        padding: 0.5rem;
        font-size: 0.75rem;
        justify-content: flex-start;
    }
    
    .elderly-aw-ods-page .elderly-btn-status-row:disabled,
    .elderly-aw-ods-page .elderly-btn-status-row.disabled {
        background: var(--elderly-gray-50) !important;
        color: var(--elderly-gray-300) !important;
        border-color: var(--elderly-gray-100) !important;
        opacity: 0.5 !important;
    }
    
    .elderly-aw-ods-page .elderly-aw-ods-container {
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    
    .elderly-aw-ods-page .elderly-aw-ods-header {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
    
    .elderly-aw-ods-page .elderly-aw-ods-number {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    
    .elderly-aw-ods-page .elderly-table tbody td {
        padding: 1rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .elderly-aw-ods-page .elderly-status-cell {
        padding: 1rem 0.75rem !important;
    }
    
    .elderly-aw-ods-page .elderly-status-label {
        font-size: 0.8rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 480px) {
    .elderly-aw-ods-page .elderly-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .elderly-aw-ods-page .elderly-stat-value {
        font-size: 1.8rem;
    }
    
    .elderly-aw-ods-page .elderly-aw-ods-header {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .elderly-aw-ods-page .elderly-aw-ods-number {
        margin-left: 0;
    }
}

/* ===== ANIMATIONS สำหรับ ELDERLY ===== */
@keyframes elderlyFadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.elderly-aw-ods-page .elderly-aw-ods-container {
    animation: elderlyFadeInUp 0.3s ease-out;
}

.elderly-aw-ods-page .elderly-stat-card {
    animation: elderlyFadeInUp 0.3s ease-out;
}

.elderly-aw-ods-page .elderly-chart-card {
    animation: elderlyFadeInUp 0.3s ease-out;
}

/* ===== LOADING STATES สำหรับ ELDERLY ===== */
.elderly-aw-ods-page .elderly-loading {
    opacity: 0.6;
    pointer-events: none;
}

.elderly-aw-ods-page .elderly-loading::after {
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
    animation: elderlySpin 1s linear infinite;
}

@keyframes elderlySpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ===== การแก้ไข Modal สำหรับ elderly ===== */
.elderly-aw-ods-page .modal-content {
    border: none;
    border-radius: var(--elderly-border-radius);
    box-shadow: var(--elderly-shadow-xl);
}

.elderly-aw-ods-page .modal-header {
    border-bottom: 1px solid var(--elderly-gray-200);
    padding: 1.5rem 2rem;
}

.elderly-aw-ods-page .modal-title {
    font-weight: 700;
    color: var(--elderly-gray-900);
}

.elderly-aw-ods-page .modal-body {
    padding: 2rem;
}

.elderly-aw-ods-page .modal-footer {
    border-top: 1px solid var(--elderly-gray-200);
    padding: 1.5rem 2rem;
}

/* ===== การแก้ไข Form Control สำหรับ elderly ===== */
.elderly-aw-ods-page .form-control,
.elderly-aw-ods-page .form-select {
    border: 2px solid var(--elderly-gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--elderly-transition);
    background-color: var(--elderly-white);
}

.elderly-aw-ods-page .form-control:focus,
.elderly-aw-ods-page .form-select:focus {
    border-color: var(--elderly-primary-color);
    box-shadow: 0 0 0 3px rgba(125, 179, 232, 0.1);
    outline: none;
}

.elderly-aw-ods-page .form-label {
    font-weight: 600;
    color: var(--elderly-gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

/* ===== การแก้ไข Alert สำหรับ elderly ===== */
.elderly-aw-ods-page .alert {
    border-radius: var(--elderly-border-radius);
    border: none;
    padding: 1rem 1.5rem;
}

.elderly-aw-ods-page .alert-info {
    background: linear-gradient(135deg, rgba(219, 234, 254, 0.8), rgba(191, 219, 254, 0.6));
    color: #0d47a1;
}

.elderly-aw-ods-page .alert-danger {
    background: linear-gradient(135deg, rgba(255, 235, 238, 0.8), rgba(255, 205, 210, 0.6));
    color: #b71c1c;
}

/* ===== การแก้ไข Bootstrap classes ที่อาจทับ ===== */
.elderly-aw-ods-page .btn:not([class*="elderly-btn"]) {
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

.elderly-aw-ods-page .btn-primary:not([class*="elderly-btn"]) {
    background: linear-gradient(135deg, var(--elderly-primary-color), var(--elderly-primary-light));
    color: white;
}

.elderly-aw-ods-page .btn-secondary:not([class*="elderly-btn"]) {
    background: var(--elderly-gray-100);
    color: var(--elderly-gray-700);
}

.elderly-aw-ods-page .btn-success:not([class*="elderly-btn"]) {
    background: linear-gradient(135deg, var(--elderly-success-color), #81c784);
    color: white;
}

.elderly-aw-ods-page .btn-danger:not([class*="elderly-btn"]) {
    background: linear-gradient(135deg, var(--elderly-danger-color), #ef5350);
    color: white;
}
</style>

<div class="elderly-aw-ods-page">
    <div class="elderly-container-fluid">
        <!-- ===== PAGE HEADER ===== -->
        <header class="elderly-page-header">
            <h1><i class="fas fa-user-clock me-3"></i>จัดการเบี้ยยังชีพผู้สูงอายุ / ผู้พิการ</h1>
            
            <!-- Header Actions (ฟันเฟือง) -->
            <div class="elderly-header-actions">
                <a href="<?= site_url('Elderly_aw_ods/manage_forms') ?>" class="elderly-action-btn" title="จัดการฟอร์ม">
                    <i class="fas fa-cog"></i>
                    <span>จัดการฟอร์ม</span>
                </a>
               <!--  <a href="<?= site_url('Elderly_aw_ods/elderly_tracking_admin') ?>" class="elderly-action-btn" title="ติดตามสถานะ">
                    <i class="fas fa-search"></i>
                    <span>ติดตาม</span>
                </a> -->
            </div>
            
            <!-- Breadcrumb 
            <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumb as $index => $item): ?>
                            <?php if ($index === count($breadcrumb) - 1): ?>
                                <li class="breadcrumb-item active" aria-current="page"><?= $item['title'] ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item">
                                    <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>   -->
        </header>

        <!-- ===== STATISTICS SECTION ===== -->
        <section class="elderly-stats-section">
            <div class="elderly-stats-grid">
                <div class="elderly-stat-card total">
                    <div class="elderly-stat-header">
                        <div class="elderly-stat-icon total">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="elderly-stat-value"><?= number_format($elderly_summary['total'] ?? 0) ?></div>
                    <div class="elderly-stat-label">ทั้งหมด</div>
                </div>

                <div class="elderly-stat-card submitted">
                    <div class="elderly-stat-header">
                        <div class="elderly-stat-icon submitted">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="elderly-stat-value"><?= number_format($elderly_summary['by_status']['submitted'] ?? 0) ?></div>
                    <div class="elderly-stat-label">ยื่นเรื่องแล้ว</div>
                </div>

                <div class="elderly-stat-card reviewing">
                    <div class="elderly-stat-header">
                        <div class="elderly-stat-icon reviewing">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="elderly-stat-value"><?= number_format($elderly_summary['by_status']['reviewing'] ?? 0) ?></div>
                    <div class="elderly-stat-label">กำลังพิจารณา</div>
                </div>

                <div class="elderly-stat-card approved">
                    <div class="elderly-stat-header">
                        <div class="elderly-stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="elderly-stat-value"><?= number_format($elderly_summary['by_status']['approved'] ?? 0) ?></div>
                    <div class="elderly-stat-label">อนุมัติแล้ว</div>
                </div>

                <div class="elderly-stat-card completed">
                    <div class="elderly-stat-header">
                        <div class="elderly-stat-icon completed">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                    <div class="elderly-stat-value"><?= number_format($elderly_summary['by_status']['completed'] ?? 0) ?></div>
                    <div class="elderly-stat-label">เสร็จสิ้น</div>
                </div>
            </div>
        </section>

        <!-- ===== FILTER SECTION ===== -->
        <section class="elderly-filter-section">
            <div class="elderly-filter-card">
                <h5><i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล</h5>
                <form method="GET" action="<?= site_url('Elderly_aw_ods/elderly_aw_ods') ?>" id="filterForm">
                    <div class="elderly-filter-grid">
                        <div class="elderly-form-group">
                            <label class="elderly-form-label">วันที่เริ่มต้น:</label>
                            <input type="date" class="elderly-form-control" name="date_from" 
                                   value="<?= $filters['date_from'] ?? '' ?>">
                        </div>

                        <div class="elderly-form-group">
                            <label class="elderly-form-label">วันที่สิ้นสุด:</label>
                            <input type="date" class="elderly-form-control" name="date_to" 
                                   value="<?= $filters['date_to'] ?? '' ?>">
                        </div>

                        <div class="elderly-form-group">
                            <label class="elderly-form-label">ค้นหา:</label>
                            <input type="text" class="elderly-form-control" name="search" 
                                   placeholder="ค้นหาหมายเลข, ชื่อ, เบอร์โทร..."
                                   value="<?= $filters['search'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="elderly-filter-actions">
                        <button type="submit" class="elderly-btn elderly-btn-primary">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <a href="<?= site_url('Elderly_aw_ods/elderly_aw_ods') ?>" class="elderly-btn elderly-btn-secondary">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </a>
                        <a href="<?= site_url('Elderly_aw_ods/export_excel') ?>" class="elderly-btn elderly-btn-success">
                            <i class="fas fa-file-excel me-1"></i>ส่งออก Excel
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- ===== ANALYTICS SECTION ===== -->
        <section class="elderly-analytics-section">
            <div class="elderly-analytics-grid">
                <!-- Recent Elderly AW ODS -->
                <div class="elderly-chart-card">
                    <div class="elderly-chart-header">
                        <h3 class="elderly-chart-title">
                            <i class="fas fa-clock me-2"></i>เบี้ยยังชีพล่าสุด
                        </h3>
                    </div>
                    <div class="elderly-recent-elderly">
                        <?php if (isset($recent_elderly) && !empty($recent_elderly)): ?>
                            <?php foreach (array_slice($recent_elderly, 0, 5) as $recent): ?>
                                <div class="elderly-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">
                                            <a href="<?= site_url('Elderly_aw_ods/elderly_detail/' . $recent->elderly_aw_ods_id) ?>">
                                                #<?= $recent->elderly_aw_ods_id ?> - <?= htmlspecialchars(mb_substr($recent->elderly_aw_ods_by, 0, 20)) ?>
                                                <?= mb_strlen($recent->elderly_aw_ods_by) > 20 ? '...' : '' ?>
                                            </a>
                                        </h6>
                                        <span class="elderly-status-badge <?= get_elderly_aw_ods_status_class($recent->elderly_aw_ods_status) ?>">
                                            <?= get_elderly_aw_ods_status_display($recent->elderly_aw_ods_status) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        ประเภท: <?= get_elderly_aw_ods_type_display($recent->elderly_aw_ods_type ?? 'elderly') ?> 
                                        | โทร: <?= htmlspecialchars($recent->elderly_aw_ods_phone) ?>
                                        | <?php 
                                            $thai_months = [
                                                '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                            ];
                                            
                                            $date = date('j', strtotime($recent->elderly_aw_ods_datesave));
                                            $month = $thai_months[date('m', strtotime($recent->elderly_aw_ods_datesave))];
                                            $year = date('Y', strtotime($recent->elderly_aw_ods_datesave)) + 543;
                                            $time = date('H:i', strtotime($recent->elderly_aw_ods_datesave));
                                            
                                            echo $date . ' ' . $month . ' ' . $year . ' ' . $time;
                                        ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="elderly-empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>ยังไม่มีข้อมูลเบี้ยยังชีพ</h5>
                                <p>ยังไม่มีการยื่นเรื่องเบี้ยยังชีพ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Type Statistics -->
                <div class="elderly-chart-card">
                    <div class="elderly-chart-header">
                        <h3 class="elderly-chart-title">
                            <i class="fas fa-chart-pie me-2"></i>สถิติตามประเภท
                        </h3>
                    </div>
                    <div class="elderly-type-stats">
                        <?php 
                        $type_labels = [
                            'elderly' => 'ผู้สูงอายุ',
                            'disabled' => 'ผู้พิการ'
                        ];
                        $type_colors = [
                            'elderly' => '#1565c0',
                            'disabled' => '#6a1b9a'
                        ];
                        ?>
                        <?php if (isset($elderly_summary['by_type'])): ?>
                            <?php foreach ($elderly_summary['by_type'] as $type => $count): ?>
                                <div class="elderly-type-stat-item">
                                    <div class="elderly-type-stat-label">
                                        <div class="elderly-type-stat-indicator" style="background-color: <?= $type_colors[$type] ?? '#9e9e9e' ?>;"></div>
                                        <span><?= $type_labels[$type] ?? $type ?></span>
                                    </div>
                                    <div class="elderly-type-stat-value"><?= number_format($count) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="elderly-empty-state">
                                <i class="fas fa-chart-pie"></i>
                                <h5>ไม่มีข้อมูลสถิติ</h5>
                                <p>ยังไม่มีข้อมูลเบี้ยยังชีพเพื่อแสดงสถิติ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== DATA TABLE SECTION ===== -->
        <section class="elderly-table-section">
            <div class="elderly-table-card">
                <div class="elderly-table-header">
                    <h5 class="elderly-table-title">
                        <i class="fas fa-list me-2"></i>รายการเบี้ยยังชีพ
                    </h5>
                    <div class="elderly-table-actions">
                        <button class="elderly-btn elderly-btn-outline-primary elderly-btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                        </button>
                    </div>
                </div>
                
                <div class="elderly-table-content">
                    <?php if (empty($elderly_aw_ods)): ?>
                        <div class="elderly-empty-state">
                            <i class="fas fa-user-clock"></i>
                            <h5>ไม่พบข้อมูลเบี้ยยังชีพ</h5>
                            <p>กรุณาลองใช้ตัวกรองอื่น หรือเพิ่มข้อมูลใหม่</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($elderly_aw_ods as $elderly): ?>
                            <div class="elderly-aw-ods-container" data-elderly-id="<?= $elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'] ?>">
                                <!-- Elderly AW ODS Header -->
                                <div class="elderly-aw-ods-header">
                                    <i class="fas fa-user-clock"></i>
                                    <span>เบี้ยยังชีพ <?= get_elderly_aw_ods_type_display($elderly->elderly_aw_ods_type ?? $elderly['elderly_aw_ods_type'] ?? 'elderly') ?></span>
                                    <span class="elderly-aw-ods-number">#<?= $elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'] ?></span>
                                </div>
                                
                                <!-- Elderly AW ODS Content -->
                                <table class="elderly-table mb-0">
                                    <tbody>
                                        <!-- Elderly AW ODS Data Row -->
                                        <tr class="elderly-aw-ods-data-row">
                                            <td style="width: 8%;">
                                                <div class="text-center">
                                                    <strong class="elderly-id-display"><?= $elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'] ?></strong>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="elderly-date-display">
                                                    <?php 
                                                    $thai_months = [
                                                        '01' => 'ม.ค.', '02' => 'ก.พ.', '03' => 'มี.ค.', '04' => 'เม.ย.',
                                                        '05' => 'พ.ค.', '06' => 'มิ.ย.', '07' => 'ก.ค.', '08' => 'ส.ค.',
                                                        '09' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
                                                    ];
                                                    
                                                    $datesave = $elderly->elderly_aw_ods_datesave ?? $elderly['elderly_aw_ods_datesave'];
                                                    $date = date('j', strtotime($datesave));
                                                    $month = $thai_months[date('m', strtotime($datesave))];
                                                    $year = date('Y', strtotime($datesave)) + 543;
                                                    $time = date('H:i', strtotime($datesave));
                                                    ?>
                                                    <div class="elderly-date-part"><?= $date ?> <?= $month ?> <?= $year ?></div>
                                                    <div class="elderly-time-part"><?= $time ?> น.</div>
                                                </div>
                                            </td>
                                            <td style="width: 15%;">
                                                <div class="text-center">
                                                    <span class="elderly-status-badge <?= get_elderly_aw_ods_status_class($elderly->elderly_aw_ods_status ?? $elderly['elderly_aw_ods_status']) ?>">
                                                        <?= get_elderly_aw_ods_status_display($elderly->elderly_aw_ods_status ?? $elderly['elderly_aw_ods_status']) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="text-center">
                                                    <span class="elderly-priority-badge <?= $elderly->elderly_aw_ods_priority ?? $elderly['elderly_aw_ods_priority'] ?? 'normal' ?>">
                                                        <?php
                                                        $priority_labels = [
                                                            'low' => 'ต่ำ',
                                                            'normal' => 'ปกติ', 
                                                            'high' => 'สูง',
                                                            'urgent' => 'เร่งด่วน'
                                                        ];
                                                        echo $priority_labels[$elderly->elderly_aw_ods_priority ?? $elderly['elderly_aw_ods_priority'] ?? 'normal'];
                                                        ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="text-center">
                                                    <span class="elderly-type-badge <?= $elderly->elderly_aw_ods_type ?? $elderly['elderly_aw_ods_type'] ?? 'elderly' ?>">
                                                        <?= get_elderly_aw_ods_type_display($elderly->elderly_aw_ods_type ?? $elderly['elderly_aw_ods_type'] ?? 'elderly') ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 12%;">
                                                <div class="elderly-files-display">
                                                    <?php if (!empty($elderly->files)): ?>
                                                        <?php 
                                                        $displayFiles = array_slice($elderly->files, 0, 2);
                                                        $remainingCount = count($elderly->files) - count($displayFiles);
                                                        ?>
                                                        
                                                        <?php foreach ($displayFiles as $file): ?>
                                                            <?php 
                                                            $fileExtension = strtolower(pathinfo($file->elderly_aw_ods_file_original_name, PATHINFO_EXTENSION));
                                                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            ?>
                                                            
                                                            <?php if ($isImage): ?>
                                                                <div class="elderly-file-item image-file" 
                                                                     onclick="showImagePreview('<?= site_url('uploads/elderly_aw_ods/' . $file->elderly_aw_ods_file_name) ?>', '<?= htmlspecialchars($file->elderly_aw_ods_file_original_name, ENT_QUOTES) ?>')"
                                                                     title="<?= htmlspecialchars($file->elderly_aw_ods_file_original_name) ?>">
                                                                    <img src="<?= site_url('uploads/elderly_aw_ods/' . $file->elderly_aw_ods_file_name) ?>" 
                                                                         alt="<?= htmlspecialchars($file->elderly_aw_ods_file_original_name) ?>" 
                                                                         class="elderly-file-preview-img"
                                                                         loading="lazy">
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="elderly-file-item" 
                                                                     onclick="downloadFile('<?= site_url('uploads/elderly_aw_ods/' . $file->elderly_aw_ods_file_name) ?>', '<?= htmlspecialchars($file->elderly_aw_ods_file_original_name, ENT_QUOTES) ?>')"
                                                                     title="<?= htmlspecialchars($file->elderly_aw_ods_file_original_name) ?>">
                                                                    <i class="fas fa-file"></i>
                                                                    <span class="file-name"><?= mb_substr($file->elderly_aw_ods_file_original_name, 0, 6) ?><?= mb_strlen($file->elderly_aw_ods_file_original_name) > 6 ? '...' : '' ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                        
                                                        <?php if ($remainingCount > 0): ?>
                                                            <div class="elderly-files-more-badge" 
                                                                 onclick="showAllFiles('<?= $elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'] ?>')"
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
                                                <div class="elderly-personal-info">
                                                    <div class="elderly-personal-info-item elderly-name-display">
                                                        <strong><?= htmlspecialchars($elderly->elderly_aw_ods_by ?? $elderly['elderly_aw_ods_by']) ?></strong>
                                                    </div>
                                                    <?php if (!empty($elderly->elderly_aw_ods_number ?? $elderly['elderly_aw_ods_number'])): ?>
                                                        <div class="elderly-personal-info-item elderly-id-card-display">
                                                            <i class="fas fa-id-card me-1"></i> 
                                                            <?php 
                                                            $id_card = $elderly->elderly_aw_ods_number ?? $elderly['elderly_aw_ods_number'];
                                                            echo htmlspecialchars(substr($id_card, 0, 3) . '-****-****-**-' . substr($id_card, -2));
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($elderly->elderly_aw_ods_phone ?? $elderly['elderly_aw_ods_phone'])): ?>
                                                        <div class="elderly-personal-info-item elderly-phone-display">
                                                            <i class="fas fa-phone me-1"></i> 
                                                            <?= htmlspecialchars($elderly->elderly_aw_ods_phone ?? $elderly['elderly_aw_ods_phone']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="width: 10%;">
                                                <div class="text-center">
                                                    <span class="elderly-user-type-badge <?= $elderly->elderly_aw_ods_user_type ?? $elderly['elderly_aw_ods_user_type'] ?? 'guest' ?>">
                                                        <?php
                                                        $user_type_labels = [
                                                            'guest' => 'ผู้ใช้ทั่วไป',
                                                            'public' => 'สมาชิก',
                                                            'staff' => 'เจ้าหน้าที่'
                                                        ];
                                                        echo $user_type_labels[$elderly->elderly_aw_ods_user_type ?? $elderly['elderly_aw_ods_user_type'] ?? 'guest'];
                                                        ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="width: 13%;">
                                                <div class="elderly-action-buttons">
                                                    <a href="<?= site_url('Elderly_aw_ods/elderly_detail/' . ($elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'])) ?>" 
                                                       class="elderly-btn-action view" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>ดู
                                                    </a>
                                                    
                                                    <?php if ($can_delete_elderly ?? false): ?>
                                                        <button type="button" 
                                                                class="elderly-btn-action delete" 
                                                                onclick="confirmDeleteElderly('<?= $elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'] ?>', '<?= htmlspecialchars($elderly->elderly_aw_ods_by ?? $elderly['elderly_aw_ods_by'], ENT_QUOTES) ?>')"
                                                                title="ลบข้อมูล">
                                                            <i class="fas fa-trash"></i>ลบ
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Elderly AW ODS Status Management Row -->
                                        <tr class="elderly-aw-ods-status-row">
                                            <td colspan="9" class="elderly-status-cell">
                                                <div class="elderly-status-update-row">
                                                    <div class="elderly-status-label">
                                                        <i class="fas fa-sync-alt"></i>
                                                        อัปเดตสถานะ #<?= $elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'] ?>
                                                    </div>
                                                    <div class="elderly-status-buttons-container">
                                                        <?php 
                                                        $current_status = $elderly->elderly_aw_ods_status ?? $elderly['elderly_aw_ods_status'];
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
$is_clickable = ($can_update_status && $is_available); // *** เปลี่ยนจาก $can_handle ***

$button_classes = "elderly-btn-status-row {$status_class}";
if ($is_current) {
    $button_classes .= ' current';
}
                                                            
                                                            $tooltip_text = '';
if ($is_current) {
    $tooltip_text = 'สถานะปัจจุบัน';
} elseif (!$can_update_status) { // *** เปลี่ยนจาก !$can_handle ***
    $tooltip_text = 'คุณไม่มีสิทธิ์เปลี่ยนสถานะ';
} elseif ($is_available) {
    $tooltip_text = 'คลิกเพื่อเปลี่ยนเป็น ' . $status_display;
} else {
    $tooltip_text = 'ไม่สามารถเปลี่ยนเป็นสถานะนี้ได้ในขณะนี้';
}
                                                            
                                                            $onclick_code = '';
                                                            if ($is_clickable) {
                                                                $elderly_id_js = htmlspecialchars($elderly->elderly_aw_ods_id ?? $elderly['elderly_aw_ods_id'], ENT_QUOTES);
                                                                $by_js = htmlspecialchars($elderly->elderly_aw_ods_by ?? $elderly['elderly_aw_ods_by'], ENT_QUOTES);
                                                                $onclick_code = "onclick=\"updateElderlyStatusDirect('{$elderly_id_js}', '{$status_key}', '{$by_js}', '{$status_display}')\"";
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
                    <div class="elderly-pagination-container">
                        <div class="elderly-pagination-info">
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

<!-- Status Update Modal (แบบใหม่ - ไม่มี dropdown สถานะ) -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-sync-alt me-2"></i>อัปเดตสถานะเบี้ยยังชีพ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="statusElderlyId" name="elderly_id">
                    <input type="hidden" id="statusNewStatus" name="new_status">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ผู้ยื่นเรื่อง:</strong> <span id="statusElderlyBy"></span>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-arrow-right me-2"></i>
                        <strong>เปลี่ยนสถานะเป็น:</strong> <span id="statusDisplayText" class="fw-bold"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ความสำคัญ:</label>
                        <select class="form-select" id="statusNewPriority" name="new_priority">
                          <!--  <option value="low">ต่ำ</option> -->
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

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>มอบหมายงาน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignmentForm">
                <div class="modal-body">
                    <input type="hidden" id="assignElderlyId" name="elderly_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ผู้ยื่นเรื่อง:</strong> <span id="assignElderlyBy"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">มอบหมายให้เจ้าหน้าที่ <span class="text-danger">*</span>:</label>
                                <select class="form-select" id="assignStaffId" name="assigned_to" required>
                                    <option value="">เลือกเจ้าหน้าที่</option>
                                    <?php if (isset($staff_list) && !empty($staff_list)): ?>
                                        <?php foreach ($staff_list as $staff): ?>
                                            <option value="<?= $staff['id'] ?>">
                                                <?= $staff['name'] ?> (<?= $staff['system'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">ความสำคัญ:</label>
                                <select class="form-select" id="assignPriority" name="priority">
                                    <option value="normal">ปกติ</option>
                                    <option value="high">สูง</option>
                                    <option value="urgent">เร่งด่วน</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุการมอบหมาย:</label>
                        <textarea class="form-control" id="assignNote" name="note" rows="4"
                                  placeholder="หมายเหตุสำหรับเจ้าหน้าที่ผู้รับมอบหมาย..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus me-1"></i>มอบหมาย
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
                
                <p>คุณต้องการลบข้อมูลเบี้ยยังชีพนี้หรือไม่?</p>
                
                <div class="bg-light p-3 rounded">
                    <strong>หมายเลข:</strong> #<span id="deleteElderlyId"></span><br>
                    <strong>ผู้ยื่นเรื่อง:</strong> <span id="deleteElderlyBy"></span>
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

const ElderlyAwOdsConfig = {
    baseUrl: '<?= site_url() ?>',
    updateStatusUrl: '<?= site_url("Elderly_aw_ods/update_elderly_status") ?>',
    assignUrl: '<?= site_url("Elderly_aw_ods/assign_elderly") ?>',
    deleteUrl: '<?= site_url("Elderly_aw_ods/delete_elderly") ?>',
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
 * อัปเดตสถานะเบี้ยยังชีพแบบตรงไปตรงมา (ไม่ผ่าน modal dropdown)
 */
function updateElderlyStatusDirect(elderlyId, newStatus, elderlyBy, statusDisplay) {
    console.log('updateElderlyStatusDirect called:', elderlyId, newStatus, elderlyBy, statusDisplay);
    
    if (!elderlyId || !newStatus) {
        console.error('Invalid parameters');
        showErrorAlert('ข้อมูลไม่ถูกต้อง');
        return;
    }
    
    // เตรียมข้อมูลสำหรับ Modal
    document.getElementById('statusElderlyId').value = elderlyId;
    document.getElementById('statusElderlyBy').textContent = elderlyBy || 'ไม่ระบุ';
    document.getElementById('statusNewStatus').value = newStatus;
    document.getElementById('statusDisplayText').textContent = statusDisplay;
    
    // แสดง Modal
    const statusModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    statusModal.show();
}

/**
 * อัปเดตสถานะเบี้ยยังชีพ (ฟังก์ชันเดิม - ใช้สำหรับ backward compatibility)
 */
function updateElderlyStatus(elderlyId, newStatus, elderlyBy) {
    // เรียกใช้ฟังก์ชันใหม่
    const statusDisplayMap = {
        'submitted': 'ยื่นเรื่องแล้ว',
        'reviewing': 'กำลังพิจารณา',
        'approved': 'อนุมัติแล้ว',
        'rejected': 'ไม่อนุมัติ',
        'completed': 'เสร็จสิ้น'
    };
    
    const statusDisplay = statusDisplayMap[newStatus] || newStatus;
    updateElderlyStatusDirect(elderlyId, newStatus, elderlyBy, statusDisplay);
}

/**
 * แสดง Modal มอบหมายงาน
 */
function showAssignModal(elderlyId, elderlyBy) {
    console.log('showAssignModal called but disabled in this version');
    // ฟังก์ชันนี้ถูกปิดการใช้งาน
    return;
}

/**
 * ยืนยันการลบข้อมูลเบี้ยยังชีพ
 */
function confirmDeleteElderly(elderlyId, elderlyBy) {
    console.log('confirmDeleteElderly called:', elderlyId, elderlyBy);
    
    if (!elderlyId) {
        showErrorAlert('ไม่พบหมายเลขข้อมูลเบี้ยยังชีพ');
        return;
    }
    
    // ตั้งค่าข้อมูลใน Modal
    document.getElementById('deleteElderlyId').textContent = elderlyId;
    document.getElementById('deleteElderlyBy').textContent = elderlyBy || 'ไม่ระบุ';
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
        performDeleteElderly(elderlyId, deleteReason, deleteModal);
    });
}

/**
 * ดำเนินการลบข้อมูลเบี้ยยังชีพ
 */
function performDeleteElderly(elderlyId, deleteReason, modal) {
    console.log('performDeleteElderly called:', elderlyId, deleteReason);
    
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
    formData.append('elderly_id', elderlyId);
    if (deleteReason) {
        formData.append('delete_reason', deleteReason);
    }
    
    fetch(ElderlyAwOdsConfig.deleteUrl, {
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
function showAllFiles(elderlyId) {
    console.log('Showing all files for elderly:', elderlyId);
    window.open(`${ElderlyAwOdsConfig.baseUrl}Elderly_aw_ods/elderly_detail/${elderlyId}`, '_blank');
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
        
        fetch(ElderlyAwOdsConfig.updateStatusUrl, {
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
 * จัดการ Form Submit สำหรับมอบหมายงาน
 */
function handleAssignmentForm() {
    console.log('Assignment form handler disabled in this version');
    // ฟังก์ชันนี้ถูกปิดการใช้งาน
    return;
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
    console.log('🚀 Elderly AW ODS Management System loading...');
    
    try {
        // Initialize core functionality
        handleStatusUpdateForm();
        // handleAssignmentForm(); // ปิดการใช้งาน
        handleSearchEnhancement();
        
        //console.log('✅ Elderly AW ODS Management System initialized successfully');
        
        if (ElderlyAwOdsConfig.debug) {
            console.log('🔧 Debug mode enabled');
            console.log('⚙️ Configuration:', ElderlyAwOdsConfig);
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

//console.log("💼 Elderly AW ODS Management System loaded successfully");
//console.log("🔧 Environment: <?= defined('ENVIRONMENT') ? ENVIRONMENT : 'production' ?>");
//console.log("📊 System Status: Ready");
</script>