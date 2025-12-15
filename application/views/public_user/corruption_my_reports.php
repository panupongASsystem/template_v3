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
        $year = $date->format('Y') + 543; // แปลงเป็นปี พ.ศ.
        $time = $date->format('H:i');
        
        return $day . ' ' . $thai_months[$month] . ' ' . $year . ' เวลา ' . $time . ' น.';
    } catch (Exception $e) {
        return $date_string; // คืนค่าเดิมถ้าแปลงไม่ได้
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
    font-size: 3rem;
    font-weight: 600;
    background: var(--corrupt-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.corrupt-page-subtitle {
    font-size: 1.2rem;
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

/* User Info Card */
.corrupt-user-info-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.corrupt-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.corrupt-user-info-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.corrupt-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--corrupt-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.corrupt-user-avatar:hover {
    box-shadow: 0 12px 35px rgba(220, 53, 69, 0.4);
    transform: translateY(-2px);
}

.corrupt-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: corruptAvatarShine 3s infinite;
    z-index: 1;
}

.corrupt-profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease;
    animation: profileLoad 0.6s ease-out;
}

.corrupt-profile-image:hover {
    transform: scale(1.05);
}

.corrupt-profile-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--corrupt-gradient-primary);
    border-radius: 50%;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    animation: profileLoad 0.6s ease-out;
}

.corrupt-profile-initials {
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 2;
    position: relative;
}

.corrupt-profile-fallback:hover .corrupt-profile-initials {
    transform: scale(1.1);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.corrupt-user-details {
    flex: 1;
}

.corrupt-user-name {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.corrupt-user-email {
    color: var(--corrupt-text-muted);
    margin-bottom: 0;
    font-size: 1rem;
}

.corrupt-user-status {
    margin-left: auto;
}

.corrupt-status-active {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
    color: var(--corrupt-success-color);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid rgba(40, 167, 69, 0.2);
    display: inline-flex;
    align-items: center;
}

/* Stats Dashboard */
.corrupt-stats-dashboard {
    padding: 2.5rem;
}

.corrupt-dashboard-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.corrupt-dashboard-header h4 {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.corrupt-dashboard-subtitle {
    color: var(--corrupt-text-muted);
    font-size: 1rem;
}

.corrupt-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.corrupt-stat-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(220, 53, 69, 0.1);
    cursor: pointer;
    user-select: none;
}

.corrupt-stat-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--corrupt-shadow-strong);
}

.corrupt-stat-card.corrupt-active {
    border: 2px solid var(--corrupt-primary-red);
    background: rgba(220, 53, 69, 0.05);
}

.corrupt-stat-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 1;
}

.corrupt-stat-card.corrupt-total .corrupt-stat-background { background: var(--corrupt-gradient-primary); }
.corrupt-stat-card.corrupt-pending .corrupt-stat-background { background: linear-gradient(135deg, #ffc107, #e0a800); }
.corrupt-stat-card.corrupt-progress .corrupt-stat-background { background: linear-gradient(135deg, #17a2b8, #138496); }
.corrupt-stat-card.corrupt-resolved .corrupt-stat-background { background: linear-gradient(135deg, #28a745, #1e7e34); }

.corrupt-stat-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    position: relative;
    overflow: hidden;
}

.corrupt-total .corrupt-stat-icon {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.15), rgba(220, 53, 69, 0.05));
    color: var(--corrupt-primary-red);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.2);
}

.corrupt-pending .corrupt-stat-icon {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
    color: #e0a800;
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
}

.corrupt-progress .corrupt-stat-icon {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.15), rgba(23, 162, 184, 0.05));
    color: var(--corrupt-info-color);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.2);
}

.corrupt-resolved .corrupt-stat-icon {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.15), rgba(40, 167, 69, 0.05));
    color: var(--corrupt-success-color);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
}

.corrupt-stat-content h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--corrupt-text-dark);
}

.corrupt-stat-content p {
    color: var(--corrupt-text-dark);
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.corrupt-stat-trend {
    color: var(--corrupt-text-muted);
    font-size: 0.9rem;
}

/* Quick Actions */
.corrupt-quick-actions-card {
    padding: 2rem 2.5rem;
}

.corrupt-actions-header {
    text-align: center;
    margin-bottom: 2rem;
}

.corrupt-actions-header h5 {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.corrupt-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.corrupt-action-button {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(220, 53, 69, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.corrupt-action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.6) 0%, rgba(200, 35, 51, 0.5) 100%);
    transition: all 0.3s ease;
    z-index: 0;
}

.corrupt-action-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.15);
    border-color: rgba(220, 53, 69, 0.5);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.corrupt-action-button:hover::before {
    left: 0;
}

.corrupt-action-button:hover .corrupt-action-icon,
.corrupt-action-button:hover .corrupt-action-content {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.9);
}

.corrupt-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--corrupt-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
    position: relative;
    z-index: 1;
}

.corrupt-action-content {
    position: relative;
    z-index: 1;
}

.corrupt-action-content h6 {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.corrupt-action-content small {
    color: var(--corrupt-text-muted);
    font-size: 0.9rem;
}

/* Reports List */
.corrupt-list-card {
    padding: 0;
}

.corrupt-list-header {
    padding: 2rem 2.5rem 1rem;
    border-bottom: 1px solid var(--corrupt-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.corrupt-list-header h4 {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.corrupt-list-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.corrupt-list-count {
    background: var(--corrupt-gradient-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.corrupt-filter-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(220, 53, 69, 0.1);
    color: var(--corrupt-primary-red);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.corrupt-clear-filter {
    background: var(--corrupt-danger-color);
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.corrupt-clear-filter:hover {
    transform: scale(1.1);
}

.corrupt-container {
    padding: 1rem 2.5rem 2.5rem;
}

.corrupt-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--corrupt-border-light);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: corruptSlideUp 0.6s ease forwards;
}

.corrupt-item::before {
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

.corrupt-item:hover::before {
    transform: scaleX(1);
}

.corrupt-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--corrupt-shadow-strong);
}

.corrupt-item.hidden {
    display: none;
}

.corrupt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.corrupt-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.corrupt-id-badge {
    background: var(--corrupt-gradient-light);
    color: var(--corrupt-primary-red);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 1px solid rgba(220, 53, 69, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.corrupt-date {
    color: var(--corrupt-text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.corrupt-status-badge.corrupt-modern {
    padding: 0.7rem 1.3rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.corrupt-type-display {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.4rem;
    line-height: 1.3;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.corrupt-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.corrupt-meta-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.corrupt-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--corrupt-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--corrupt-primary-red);
    font-size: 0.9rem;
}

.corrupt-meta-content {
    display: flex;
    flex-direction: column;
}

.corrupt-meta-content small {
    color: var(--corrupt-text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.corrupt-meta-content span {
    color: var(--corrupt-text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}

.corrupt-preview {
    background: var(--corrupt-gradient-light);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--corrupt-primary-red);
    margin-bottom: 1.5rem;
}

.corrupt-preview p {
    color: var(--corrupt-text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.corrupt-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.corrupt-action-btn {
    border: none;
    border-radius: 12px;
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.9rem;
}

.corrupt-action-btn.corrupt-primary {
    background: var(--corrupt-gradient-primary);
    color: white;
}

.corrupt-action-btn.corrupt-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
    color: white;
    text-decoration: none;
}

.corrupt-action-btn.corrupt-secondary {
    background: rgba(220, 53, 69, 0.1);
    color: var(--corrupt-primary-red);
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.corrupt-action-btn.corrupt-secondary:hover {
    background: var(--corrupt-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
}

.corrupt-action-btn.corrupt-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #e0a800;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.corrupt-action-btn.corrupt-warning:hover {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
}

/* Status Colors */
.corrupt-status-pending { background: var(--corrupt-warning-color) !important; }
.corrupt-status-under_review { background: var(--corrupt-info-color) !important; }
.corrupt-status-investigating { background: #007bff !important; }
.corrupt-status-resolved { background: var(--corrupt-success-color) !important; }
.corrupt-status-dismissed { background: var(--corrupt-danger-color) !important; }
.corrupt-status-closed { background: #6c757d !important; }

/* No Results */
.corrupt-no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--corrupt-text-muted);
}

.corrupt-no-results-icon {
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

.corrupt-no-results h5 {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.corrupt-no-results p {
    margin-bottom: 2rem;
}

/* Empty State */
.corrupt-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    position: relative;
}

.corrupt-empty-illustration {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.corrupt-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--corrupt-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: var(--corrupt-primary-red);
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
}

.corrupt-icon-decoration {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 35px;
    height: 35px;
    background: var(--corrupt-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
    border: 3px solid white;
}

.corrupt-empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.corrupt-circle {
    position: absolute;
    border: 2px solid rgba(220, 53, 69, 0.1);
    border-radius: 50%;
    animation: corruptPulse 2s infinite;
}

.corrupt-circle-1 {
    width: 160px;
    height: 160px;
    top: -80px;
    left: -80px;
    animation-delay: 0s;
}

.corrupt-circle-2 {
    width: 200px;
    height: 200px;
    top: -100px;
    left: -100px;
    animation-delay: 0.5s;
}

.corrupt-circle-3 {
    width: 240px;
    height: 240px;
    top: -120px;
    left: -120px;
    animation-delay: 1s;
}

.corrupt-empty-content h5 {
    color: var(--corrupt-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.corrupt-empty-content p {
    color: var(--corrupt-text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Animations */
@keyframes corruptShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes corruptAvatarShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes corruptSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes corruptPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 0.1;
    }
}

@keyframes corruptFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes profileLoad {
    0% {
        opacity: 0;
        transform: scale(0.8) rotate(-10deg);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.1) rotate(5deg);
    }
    100% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .corrupt-page-title {
        font-size: 2.2rem;
    }
    
    .corrupt-page-subtitle {
        font-size: 1rem;
    }
    
    .corrupt-user-info-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .corrupt-user-status {
        margin-left: 0;
    }
    
    .corrupt-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .corrupt-stat-card {
        padding: 1.2rem 0.8rem;
    }
    
    .corrupt-stat-content h3 {
        font-size: 1.8rem;
    }
    
    .corrupt-stat-content p {
        font-size: 0.9rem;
    }
    
    .corrupt-stat-trend small {
        font-size: 0.7rem;
    }
    
    .corrupt-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .corrupt-list-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .corrupt-list-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .corrupt-container {
        padding: 1rem;
    }
    
    .corrupt-item {
        padding: 1.5rem;
    }
    
    .corrupt-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .corrupt-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .corrupt-actions {
        flex-direction: column;
    }
    
    .corrupt-action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .corrupt-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .corrupt-user-avatar {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .corrupt-type-display {
        font-size: 1.2rem;
    }
}

@media print {
    .corrupt-action-btn,
    .corrupt-quick-actions-card {
        display: none !important;
    }
    
    .corrupt-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .corrupt-stat-card {
        cursor: default;
    }
}
</style>

<div class="corrupt-bg-pages">
    <div class="corrupt-container-pages">
        
        <!-- Page Header -->
        <div class="corrupt-page-header">
            <div class="corrupt-header-decoration"></div>
            <h1 class="corrupt-page-title">
                <i class="fas fa-shield-exclamation me-3"></i>
                รายงานสถานะการแจ้งการทุจริตและประพฤติมิชอบ
            </h1>
            <p class="corrupt-page-subtitle">ติดตามสถานะและจัดการรายงานการทุจริตที่คุณได้ยื่นไว้</p>
        </div>

        <!-- User Info Card -->
        <div class="corrupt-modern-card corrupt-user-info-card">
            <div class="corrupt-card-gradient-bg"></div>
            <div class="corrupt-user-info-content">
                <div class="corrupt-user-avatar">
                    <?php 
                    // ระบบจัดการรูปโปรไฟล์ (คล้ายกับระบบเงินสนับสนุนเด็ก)
                    $profile_img = $member_info['profile_img'] ?? '';
                    $mp_fname = $member_info['name'] ?? '';
                    $mp_email = $member_info['email'] ?? '';
                    
                    // สร้างชื่อเริ่มต้น (Initial) สำหรับ fallback
                    $name_parts = explode(' ', $mp_fname);
                    $initials = '';
                    foreach ($name_parts as $part) {
                        if (!empty($part)) {
                            $initials .= mb_substr($part, 0, 1);
                            if (strlen($initials) >= 2) break;
                        }
                    }
                    if (empty($initials)) {
                        $initials = 'U'; // User
                    }
                    ?>
                    
                    <?php if (!empty($profile_img)): ?>
                        <!-- แสดงรูปโปรไฟล์ -->
                        <img src="<?php echo base_url('docs/img/avatar/' . $profile_img); ?>" 
                             alt="Profile" 
                             class="corrupt-profile-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback เมื่อโหลดรูปไม่ได้ -->
                        <div class="corrupt-profile-fallback" style="display: none;">
                            <span class="corrupt-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php else: ?>
                        <!-- แสดง initials เมื่อไม่มีรูปโปรไฟล์ -->
                        <div class="corrupt-profile-fallback">
                            <span class="corrupt-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="corrupt-user-details">
                    <h4 class="corrupt-user-name">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo htmlspecialchars($member_info['name'] ?? 'ผู้ใช้'); ?>
                    </h4>
                    <p class="corrupt-user-email">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($member_info['email'] ?? ''); ?>
                    </p>
                </div>
                <div class="corrupt-user-status">
                    <span class="corrupt-status-active">
                        <i class="fas fa-check-circle me-1"></i>
                        สมาชิกที่ยืนยันแล้ว
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="corrupt-modern-card corrupt-stats-dashboard">
            <div class="corrupt-dashboard-header">
                <h4><i class="fas fa-chart-pie me-2"></i>สถิติรายงานการทุจริต</h4>
                <span class="corrupt-dashboard-subtitle">คลิกเพื่อกรองรายการตามสถานะ</span>
            </div>
            
            <div class="corrupt-stats-grid">
                <div class="corrupt-stat-card corrupt-total corrupt-active" data-filter="all">
                    <div class="corrupt-stat-background"></div>
                    <div class="corrupt-stat-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="corrupt-stat-content">
                        <h3><?php echo $report_stats['total'] ?? 0; ?></h3>
                        <p>ทั้งหมด</p>
                        <div class="corrupt-stat-trend">
                            <small>รายการทั้งหมด</small>
                        </div>
                    </div>
                </div>
                
                <div class="corrupt-stat-card corrupt-pending" data-filter="pending">
                    <div class="corrupt-stat-background"></div>
                    <div class="corrupt-stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="corrupt-stat-content">
                        <h3><?php echo $report_stats['pending'] ?? 0; ?></h3>
                        <p>รอดำเนินการ</p>
                        <div class="corrupt-stat-trend">
                            <small>รอการตรวจสอบ</small>
                        </div>
                    </div>
                </div>
                
                <div class="corrupt-stat-card corrupt-progress" data-filter="in_progress">
                    <div class="corrupt-stat-background"></div>
                    <div class="corrupt-stat-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="corrupt-stat-content">
                        <h3><?php echo $report_stats['in_progress'] ?? 0; ?></h3>
                        <p>กำลังดำเนินการ</p>
                        <div class="corrupt-stat-trend">
                            <small>อยู่ระหว่างการสอบสวน</small>
                        </div>
                    </div>
                </div>
                
                <div class="corrupt-stat-card corrupt-resolved" data-filter="resolved">
                    <div class="corrupt-stat-background"></div>
                    <div class="corrupt-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="corrupt-stat-content">
                        <h3><?php echo $report_stats['resolved'] ?? 0; ?></h3>
                        <p>เสร็จสิ้น</p>
                        <div class="corrupt-stat-trend">
                            <small>ดำเนินการครบถ้วน</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="corrupt-modern-card corrupt-quick-actions-card">
            <div class="corrupt-actions-header">
                <h5><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
            </div>
            <div class="corrupt-actions-grid">
                <a href="<?php echo site_url('Corruption/report_form'); ?>" class="corrupt-action-button">
                    <div class="corrupt-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="corrupt-action-content">
                        <h6>แจ้งเรื่องใหม่</h6>
                        <small>ยื่นรายงานการทุจริตใหม่อย่างปลอดภัย</small>
                    </div>
                </a>
                
            </div>
        </div>

        <!-- Reports List -->
        <div class="corrupt-modern-card corrupt-list-card">
            <div class="corrupt-list-header">
                <h4><i class="fas fa-shield-exclamation me-2"></i>รายการรายงานการทุจริตของฉัน</h4>
                <div class="corrupt-list-controls">
                    <span class="corrupt-list-count" id="corrupt-count"><?php echo count($reports ?? []); ?> รายการ</span>
                    <div class="corrupt-filter-indicator" id="corrupt-filter-indicator">
                        <i class="fas fa-filter me-1"></i>
                        <span id="corrupt-filter-text">ทั้งหมด</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($reports)): ?>
                <div class="corrupt-container">
                    <?php foreach ($reports as $index => $report): 
                        // ข้อมูลที่ประมวลผลแล้ว
                        $latest_status = getStatusDisplay($report->report_status);
                        $status_class = 'corrupt-status-' . $report->report_status;
                        
                        // Icon ตามสถานะ
                        $status_icons = [
                            'pending' => 'fas fa-clock',
                            'under_review' => 'fas fa-search',
                            'investigating' => 'fas fa-cogs',
                            'resolved' => 'fas fa-check-circle',
                            'dismissed' => 'fas fa-times-circle',
                            'closed' => 'fas fa-archive'
                        ];
                        $status_icon = $status_icons[$report->report_status] ?? 'fas fa-file-alt';
                        
                        // Format date เป็นรูปแบบไทย
                        $formatted_date = convertToThaiDate($report->created_at ?? '');
                        
                        // Latest update เป็นรูปแบบไทย
                        $latest_update = '';
                        if (!empty($report->updated_at) && $report->updated_at != '0000-00-00 00:00:00') {
                            $latest_update = convertToThaiDate($report->updated_at);
                        }
                        
                        // Animation delay
                        $animation_delay = $index * 100;
                        
                        // Status mapping for filter
                        $filter_status = 'all';
                        switch ($report->report_status) {
                            case 'pending':
                                $filter_status = 'pending';
                                break;
                            case 'under_review':
                            case 'investigating':
                                $filter_status = 'in_progress';
                                break;
                            case 'resolved':
                            case 'dismissed':
                            case 'closed':
                                $filter_status = 'resolved';
                                break;
                            default:
                                $filter_status = 'pending';
                                break;
                        }
                        
                        // Type display
                        $type_display = getCorruptionTypeDisplay($report->corruption_type);
                        $type_icons = [
                            'embezzlement' => 'fas fa-money-bill-wave',
                            'bribery' => 'fas fa-hand-holding-usd',
                            'abuse_of_power' => 'fas fa-gavel',
                            'conflict_of_interest' => 'fas fa-balance-scale',
                            'procurement_fraud' => 'fas fa-shopping-cart',
                            'other' => 'fas fa-exclamation-triangle'
                        ];
                        $type_icon = $type_icons[$report->corruption_type] ?? 'fas fa-exclamation-triangle';
                    ?>
                        <div class="corrupt-item" 
                             style="animation-delay: <?php echo $animation_delay; ?>ms;"
                             data-status="<?php echo $filter_status; ?>"
                             data-original-status="<?php echo htmlspecialchars($latest_status); ?>">
                            <div class="corrupt-header">
                                <div class="corrupt-id-section">
                                    <div class="corrupt-id-badge">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <?php echo htmlspecialchars($report->corruption_report_id ?? ''); ?>
                                    </div>
                                    <div class="corrupt-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        แจ้งวันที่: <?php echo $formatted_date; ?>
                                    </div>
                                </div>
                                <div class="corrupt-status-section">
                                    <span class="corrupt-status-badge corrupt-modern <?php echo $status_class; ?>">
                                        <i class="<?php echo $status_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($latest_status); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="corrupt-body">
                                <h5 class="corrupt-type-display">
                                    <i class="<?php echo $type_icon; ?>"></i>
                                    <?php echo htmlspecialchars($type_display); ?>
                                </h5>
                                
                                <div class="corrupt-meta">
                                    <div class="corrupt-meta-item">
                                        <div class="corrupt-meta-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="corrupt-meta-content">
                                            <small>ผู้แจ้ง</small>
                                            <span><?php echo htmlspecialchars($report->display_reporter_name ?? $report->reporter_name ?? 'ไม่ระบุตัวตน'); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="corrupt-meta-item">
                                        <div class="corrupt-meta-icon">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div class="corrupt-meta-content">
                                            <small>ผู้ถูกกล่าวหา</small>
                                            <span><?php echo htmlspecialchars($report->perpetrator_name ?? ''); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($latest_update): ?>
                                    <div class="corrupt-meta-item">
                                        <div class="corrupt-meta-icon">
                                            <i class="fas fa-sync-alt"></i>
                                        </div>
                                        <div class="corrupt-meta-content">
                                            <small>อัปเดตล่าสุด</small>
                                            <span><?php echo $latest_update; ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($report->file_count) && $report->file_count > 0): ?>
                                    <div class="corrupt-meta-item">
                                        <div class="corrupt-meta-icon">
                                            <i class="fas fa-paperclip"></i>
                                        </div>
                                        <div class="corrupt-meta-content">
                                            <small>ไฟล์แนบ</small>
                                            <span><?php echo $report->file_count; ?> ไฟล์</span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($report->complaint_subject)): ?>
                                <div class="corrupt-preview">
                                    <p>
                                        <strong>หัวข้อเรื่อง:</strong> 
                                        <?php echo htmlspecialchars($report->complaint_subject); ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="corrupt-actions">
                                <a href="<?php echo site_url('Corruption/my_report_detail/' . ($report->corruption_report_id ?? '')); ?>" 
                                   class="corrupt-action-btn corrupt-primary">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                                
                                <button onclick="copyReportId('<?php echo htmlspecialchars($report->corruption_report_id ?? '', ENT_QUOTES); ?>')" 
                                        class="corrupt-action-btn corrupt-secondary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                                </button>
                                
                                <a href="<?php echo site_url('Corruption/track_status?report_id=' . urlencode($report->corruption_report_id ?? '')); ?>" 
                                   class="corrupt-action-btn corrupt-secondary">
                                    <i class="fas fa-search me-2"></i>ติดตามสถานะ
                                </a>
                                
                                <?php if ($report->report_status === 'resolved'): ?>
                                <button onclick="printReport('<?php echo htmlspecialchars($report->corruption_report_id ?? '', ENT_QUOTES); ?>')" 
                                        class="corrupt-action-btn corrupt-warning">
                                    <i class="fas fa-print me-2"></i>พิมพ์
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Results Message -->
                <div class="corrupt-no-results" id="corrupt-no-results" style="display: none;">
                    <div class="corrupt-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>ไม่พบรายการที่ตรงกับเงื่อนไข</h5>
                    <p>ลองเปลี่ยนตัวกรองหรือดูรายการทั้งหมด</p>
                    <button onclick="filterCorruptByStatus('all')" class="corrupt-action-btn corrupt-primary">
                        <i class="fas fa-list me-2"></i>ดูทั้งหมด
                    </button>
                </div>
                
            <?php else: ?>
                <!-- Empty State -->
                <div class="corrupt-empty-state">
                    <div class="corrupt-empty-illustration">
                        <div class="corrupt-empty-icon">
                            <i class="fas fa-shield-exclamation"></i>
                            <div class="corrupt-icon-decoration">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="corrupt-empty-circles">
                            <div class="corrupt-circle corrupt-circle-1"></div>
                            <div class="corrupt-circle corrupt-circle-2"></div>
                            <div class="corrupt-circle corrupt-circle-3"></div>
                        </div>
                    </div>
                    <div class="corrupt-empty-content">
                        <h5>ยังไม่มีรายงานการทุจริต</h5>
                        <p>คุณยังไม่เคยแจ้งรายงานการทุจริตในระบบ<br>เริ่มต้นโดยการแจ้งเรื่องรายงานแรกของคุณ</p>
                        <a href="<?php echo site_url('Corruption/report_form'); ?>" class="corrupt-action-btn corrupt-primary corrupt-large">
                            <i class="fas fa-plus me-2"></i>แจ้งเรื่องการทุจริต
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Load jQuery ก่อนเสมอ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// *** รอให้ jQuery โหลดเสร็จก่อน ***
$(document).ready(function() {
    console.log('🚀 เริ่มต้นระบบรายงานการทุจริตของฉัน');
    
    // *** ตัวแปรสำคัญ ***
    let currentCorruptFilter = 'all';
    let originalStatusCounts = null;
    
    // *** บันทึกข้อมูลสถิติเดิม ***
    if (!originalStatusCounts) {
        originalStatusCounts = {
            total: parseInt($('.corrupt-stat-card.corrupt-total .corrupt-stat-content h3').text()) || 0,
            pending: parseInt($('.corrupt-stat-card.corrupt-pending .corrupt-stat-content h3').text()) || 0,
            in_progress: parseInt($('.corrupt-stat-card.corrupt-progress .corrupt-stat-content h3').text()) || 0,
            resolved: parseInt($('.corrupt-stat-card.corrupt-resolved .corrupt-stat-content h3').text()) || 0
        };
        console.log('📊 สถิติเดิม:', originalStatusCounts);
    }
    
    // *** ฟังก์ชันกรองรายงานการทุจริต ***
    window.filterCorruptByStatus = function(filter) {
        console.log('🔍 กรองสถิติ:', filter);
        
        currentCorruptFilter = filter;
        
        // *** เอาการอัปเดต active state ของ stat cards ***
        $('.corrupt-stat-card').removeClass('corrupt-active');
        $('.corrupt-stat-card[data-filter="' + filter + '"]').addClass('corrupt-active');
        
        // *** กรองรายการรายงาน ***
        const corruptItems = $('.corrupt-item');
        const noResults = $('#corrupt-no-results');
        let visibleCount = 0;
        
        // *** ซ่อน/แสดงรายการ ***
        corruptItems.each(function() {
            const $item = $(this);
            const itemStatus = $item.attr('data-status');
            
            if (filter === 'all' || itemStatus === filter) {
                $item.show().removeClass('hidden');
                visibleCount++;
                
                // Re-trigger animation
                $item.css('animation', 'none');
                setTimeout(() => {
                    $item.css('animation', 'corruptFadeIn 0.5s ease forwards');
                }, 10);
            } else {
                $item.hide().addClass('hidden');
            }
        });
        
        // *** อัปเดตจำนวนที่แสดง ***
        const countElement = $('#corrupt-count');
        if (countElement.length) {
            countElement.text(visibleCount + ' รายการ');
        }
        
        // *** อัปเดต filter indicator ***
        updateFilterIndicator(filter, visibleCount);
        
        // *** แสดง/ซ่อนข้อความไม่พบข้อมูล ***
        if (noResults.length) {
            if (visibleCount === 0 && filter !== 'all') {
                noResults.show().css('animation', 'corruptFadeIn 0.5s ease forwards');
            } else {
                noResults.hide();
            }
        }
        
        // *** Smooth scroll ***
        if (filter !== 'all') {
            const corruptList = $('.corrupt-list-card');
            if (corruptList.length) {
                corruptList.get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        console.log('✅ กรองเสร็จ - แสดง:', visibleCount, 'รายการ');
    };
    
    // *** ฟังก์ชันอัปเดต filter indicator ***
    function updateFilterIndicator(filter, visibleCount) {
        const filterText = $('#corrupt-filter-text');
        const clearFilterBtn = $('.corrupt-clear-filter');
        
        if (filterText.length) {
            const filterNames = {
                'all': 'ทั้งหมด',
                'pending': 'รอดำเนินการ',
                'in_progress': 'กำลังดำเนินการ',
                'resolved': 'เสร็จสิ้น'
            };
            filterText.text(filterNames[filter] || 'ทั้งหมด');
        }
        
        if (clearFilterBtn.length) {
            if (filter === 'all') {
                clearFilterBtn.hide();
            } else {
                clearFilterBtn.show();
            }
        }
    }
    
    // *** ฟังก์ชันคัดลอกหมายเลขรายงาน ***
    window.copyReportId = function(reportId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(reportId).then(() => {
                showCorruptAlert('คัดลอกหมายเลข ' + reportId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopyText(reportId);
            });
        } else {
            fallbackCopyText(reportId);
        }
    };
    
    function fallbackCopyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showCorruptAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
        } catch (err) {
            showCorruptAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** ฟังก์ชันพิมพ์รายงาน ***
    window.printReport = function(reportId) {
        Swal.fire({
            title: 'พิมพ์รายงาน',
            text: `คุณต้องการพิมพ์รายงาน ${reportId} หรือไม่?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'พิมพ์',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // เปิดหน้าพิมพ์ในหน้าต่างใหม่
                const printUrl = `<?php echo site_url('Corruption/print_report/'); ?>${reportId}`;
                window.open(printUrl, '_blank');
                
                showCorruptAlert('กำลังเปิดหน้าพิมพ์...', 'info');
            }
        });
    };
    
    // *** Alert Functions ***
    function showCorruptAlert(message, type) {
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
                toast: false,
                position: 'center',
                confirmButtonColor: '#dc3545'
            });
        } else {
            // Enhanced fallback alert
            const alertDiv = document.createElement('div');
            const colors = {
                'success': '#28a745',
                'error': '#dc3545',
                'warning': '#ffc107',
                'info': '#17a2b8'
            };
            
            const icons = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            };
            
            alertDiv.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                color: ${colors[type] || colors.info};
                padding: 2rem;
                border-radius: 12px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.3);
                z-index: 15000;
                font-family: 'Kanit', sans-serif;
                font-weight: 500;
                text-align: center;
                max-width: 400px;
                border: 3px solid ${colors[type] || colors.info};
                animation: corruptAlertShow 0.3s ease;
            `;
            
            const icon = icons[type] || icons.info;
            alertDiv.innerHTML = `
                <div style="margin-bottom: 1rem;">
                    <i class="${icon}" style="font-size: 3rem;"></i>
                </div>
                <div style="font-size: 1.2rem; line-height: 1.4;">
                    ${message}
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove
            setTimeout(() => {
                alertDiv.style.animation = 'corruptAlertHide 0.3s ease';
                setTimeout(() => alertDiv.remove(), 300);
            }, 4000);
        }
    }
    
    // *** Event Listeners สำหรับการ์ดสถิติ ***
    $('.corrupt-stat-card').off('click').on('click', function(e) {
        e.preventDefault();
        
        const filter = $(this).attr('data-filter');
        if (filter) {
            console.log('🎯 คลิกการ์ดสถิติ:', filter);
            filterCorruptByStatus(filter);
        }
    });
    
    // *** Event Listener สำหรับปุ่มล้างตัวกรอง ***
    $('.corrupt-clear-filter').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('🗑️ ล้างตัวกรอง');
        filterCorruptByStatus('all');
    });
    
    // *** เริ่มต้นด้วยการแสดงทั้งหมด ***
    filterCorruptByStatus('all');
    
    // *** ตรวจสอบการอัปเดตสถานะแบบ Real-time (ทุก 30 วินาที) ***
    function checkForUpdates() {
        $.ajax({
            url: '<?php echo site_url("Corruption/check_updates"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                last_check: Math.floor(Date.now() / 1000)
            },
            success: function(response) {
                if (response.success && response.has_updates) {
                    // แสดงการแจ้งเตือนมีการอัปเดต
                    showUpdateNotification(response.updates);
                }
            },
            error: function() {
                console.log('ไม่สามารถตรวจสอบการอัปเดตได้');
            }
        });
    }
    
    // ตรวจสอบการอัปเดตทุก 30 วินาที
    setInterval(checkForUpdates, 30000);
    
    // *** ฟังก์ชันแสดงการแจ้งเตือนอัปเดต ***
    function showUpdateNotification(updates) {
        if (updates && updates.length > 0) {
            const updateMessage = `มีการอัปเดตสถานะรายงาน ${updates.length} รายการ`;
            
            Swal.fire({
                title: 'มีการอัปเดตใหม่!',
                text: updateMessage,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'รีเฟรชหน้า',
                cancelButtonText: 'ภายหลัง'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }
    }
    
    // *** เพิ่มการจัดการ Hover Effects ***
    $('.corrupt-item').hover(
        function() {
            $(this).find('.corrupt-action-btn').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).find('.corrupt-action-btn').css('transform', 'translateY(0)');
        }
    );
    
    // *** เพิ่มการจัดการ Scroll to Top ***
    function addScrollToTop() {
        if ($(window).scrollTop() > 300) {
            if (!$('#scrollToTop').length) {
                const scrollBtn = $(`
                    <button id="scrollToTop" style="
                        position: fixed;
                        bottom: 20px;
                        right: 20px;
                        background: var(--corrupt-gradient-primary);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 50px;
                        height: 50px;
                        font-size: 1.2rem;
                        box-shadow: var(--corrupt-shadow-medium);
                        cursor: pointer;
                        z-index: 1000;
                        transition: all 0.3s ease;
                        animation: corruptFadeIn 0.3s ease;
                    ">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                `);
                
                scrollBtn.hover(
                    function() {
                        $(this).css('transform', 'scale(1.1)');
                    },
                    function() {
                        $(this).css('transform', 'scale(1)');
                    }
                );
                
                scrollBtn.click(function() {
                    $('html, body').animate({scrollTop: 0}, 500);
                });
                
                $('body').append(scrollBtn);
            }
        } else {
            $('#scrollToTop').fadeOut(300, function() {
                $(this).remove();
            });
        }
    }
    
    $(window).scroll(addScrollToTop);
    
    // *** เพิ่มการจัดการ Loading State ***
    function showLoading() {
        if (!$('#corruptLoading').length) {
            const loading = $(`
                <div id="corruptLoading" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    font-family: 'Kanit', sans-serif;
                ">
                    <div style="
                        background: white;
                        padding: 2rem;
                        border-radius: 12px;
                        text-align: center;
                        box-shadow: var(--corrupt-shadow-strong);
                    ">
                        <div style="
                            width: 40px;
                            height: 40px;
                            border: 4px solid var(--corrupt-light-red);
                            border-top: 4px solid var(--corrupt-primary-red);
                            border-radius: 50%;
                            animation: spin 1s linear infinite;
                            margin: 0 auto 1rem;
                        "></div>
                        <div style="color: var(--corrupt-text-dark); font-weight: 500;">
                            กำลังประมวลผล...
                        </div>
                    </div>
                </div>
            `);
            $('body').append(loading);
        }
    }
    
    function hideLoading() {
        $('#corruptLoading').fadeOut(300, function() {
            $(this).remove();
        });
    }
    
    // *** เพิ่ม CSS สำหรับ Loading Animation ***
    const loadingCSS = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    
    if (!$('#loadingStyles').length) {
        $('<style id="loadingStyles">').text(loadingCSS).appendTo('head');
    }
    
    // *** เพิ่มการจัดการ Keyboard Shortcuts ***
    $(document).keydown(function(e) {
        // Ctrl + F = ค้นหา
        if (e.ctrlKey && e.keyCode === 70) {
            e.preventDefault();
            const searchInput = $('#reportSearch');
            if (searchInput.length) {
                searchInput.focus();
            }
        }
        
        // Escape = ล้างตัวกรอง
        if (e.keyCode === 27) {
            filterCorruptByStatus('all');
        }
        
        // 1-4 = เลือกตัวกรองด่วน
        if (e.keyCode >= 49 && e.keyCode <= 52) {
            const filters = ['all', 'pending', 'in_progress', 'resolved'];
            const filterIndex = e.keyCode - 49;
            if (filters[filterIndex]) {
                filterCorruptByStatus(filters[filterIndex]);
            }
        }
    });
    
    console.log('✅ ระบบรายงานการทุจริตของฉันพร้อมใช้งาน');
    
    // *** เพิ่มการแสดงคำแนะนำ ***
    if (originalStatusCounts.total === 0) {
        setTimeout(() => {
            showCorruptAlert('เริ่มต้นการใช้งานด้วยการแจ้งเรื่องการทุจริตรายการแรกของคุณ', 'info');
        }, 2000);
    }
});

// *** CSS Animation สำหรับ Alert ***
const alertAnimationCSS = `
@keyframes corruptAlertShow {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes corruptAlertHide {
    from {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
}

/* Enhanced Hover Effects */
.corrupt-action-btn:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.corrupt-stat-card:hover .corrupt-stat-icon {
    transform: scale(1.1);
}

.corrupt-item:hover .corrupt-id-badge {
    background: var(--corrupt-gradient-primary);
    color: white;
    transform: scale(1.05);
}

/* Responsive Improvements */
@media (max-width: 480px) {
    .corrupt-stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .corrupt-stat-card {
        padding: 1rem;
    }
    
    .corrupt-stat-content h3 {
        font-size: 2rem;
    }
    
    .corrupt-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .corrupt-action-button {
        padding: 1rem;
    }
}

/* Print Styles */
@media print {
    .corrupt-action-btn,
    .corrupt-quick-actions-card,
    .corrupt-list-controls {
        display: none !important;
    }
    
    .corrupt-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
    }
    
    .corrupt-item {
        border: 1px solid #ddd;
        margin-bottom: 1rem;
        break-inside: avoid;
    }
    
    .corrupt-page-title {
        color: #333 !important;
        -webkit-text-fill-color: #333 !important;
    }
}

/* Accessibility Improvements */
.corrupt-action-btn:focus,
.corrupt-stat-card:focus {
    outline: 2px solid var(--corrupt-primary-red);
    outline-offset: 2px;
}

.corrupt-action-btn:focus:not(:focus-visible) {
    outline: none;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .corrupt-modern-card {
        border: 2px solid #000;
    }
    
    .corrupt-action-btn {
        border: 2px solid currentColor;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
`;

// เพิ่ม CSS เข้าไปใน head
const enhancedStyleSheet = document.createElement('style');
enhancedStyleSheet.textContent = alertAnimationCSS;
document.head.appendChild(enhancedStyleSheet);
</script>