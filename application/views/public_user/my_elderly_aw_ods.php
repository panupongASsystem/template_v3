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
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --elderly-primary-blue: #667eea;
    --elderly-secondary-blue: #764ba2;
    --elderly-light-blue: #e8f2ff;
    --elderly-very-light-blue: #f1f8ff;
    --elderly-success-color: #28a745;
    --elderly-warning-color: #ffc107;
    --elderly-danger-color: #dc3545;
    --elderly-info-color: #17a2b8;
    --elderly-purple-color: #6f42c1;
    --elderly-text-dark: #2c3e50;
    --elderly-text-muted: #6c757d;
    --elderly-border-light: rgba(102, 126, 234, 0.1);
    --elderly-shadow-light: 0 4px 20px rgba(102, 126, 234, 0.1);
    --elderly-shadow-medium: 0 8px 30px rgba(102, 126, 234, 0.15);
    --elderly-shadow-strong: 0 15px 40px rgba(102, 126, 234, 0.2);
    --elderly-gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --elderly-gradient-light: linear-gradient(135deg, #f1f8ff 0%, #e8f2ff 100%);
    --elderly-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.elderly-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(102, 126, 234, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(118, 75, 162, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(102, 126, 234, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.elderly-container-pages {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.elderly-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.elderly-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--elderly-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.elderly-header-decoration::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: elderlyShimmer 2s infinite;
}

.elderly-page-title {
    font-size: 3rem;
    font-weight: 600;
    background: var(--elderly-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.elderly-page-subtitle {
    font-size: 1.2rem;
    color: var(--elderly-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.elderly-modern-card {
    background: var(--elderly-gradient-card);
    border-radius: 24px;
    box-shadow: var(--elderly-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(102, 126, 234, 0.08);
    z-index: 50;
}

.elderly-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--elderly-gradient-primary);
    z-index: 1;
}

/* User Info Card */
.elderly-user-info-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.elderly-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.elderly-user-info-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.elderly-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--elderly-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.elderly-user-avatar:hover {
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    transform: translateY(-2px);
}

.elderly-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: elderlyAvatarShine 3s infinite;
    z-index: 1;
}

.elderly-profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease;
    animation: profileLoad 0.6s ease-out;
}

.elderly-profile-image:hover {
    transform: scale(1.05);
}

.elderly-profile-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--elderly-gradient-primary);
    border-radius: 50%;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    animation: profileLoad 0.6s ease-out;
}

.elderly-profile-initials {
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 2;
    position: relative;
}

.elderly-profile-fallback:hover .elderly-profile-initials {
    transform: scale(1.1);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.elderly-user-details {
    flex: 1;
}

.elderly-user-name {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.elderly-user-email {
    color: var(--elderly-text-muted);
    margin-bottom: 0;
    font-size: 1rem;
}

.elderly-user-status {
    margin-left: auto;
}

.elderly-status-active {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
    color: var(--elderly-success-color);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid rgba(40, 167, 69, 0.2);
    display: inline-flex;
    align-items: center;
}

/* Stats Dashboard */
.elderly-stats-dashboard {
    padding: 2.5rem;
}

.elderly-dashboard-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.elderly-dashboard-header h4 {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.elderly-dashboard-subtitle {
    color: var(--elderly-text-muted);
    font-size: 1rem;
}

.elderly-stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.5rem;
}

.elderly-stat-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(102, 126, 234, 0.1);
    cursor: pointer;
    user-select: none;
}

.elderly-stat-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--elderly-shadow-strong);
}

.elderly-stat-card.elderly-active {
    border: 2px solid var(--elderly-primary-blue);
    background: rgba(102, 126, 234, 0.05);
}

.elderly-stat-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 1;
}

.elderly-stat-card.elderly-total .elderly-stat-background { background: var(--elderly-gradient-primary); }
.elderly-stat-card.elderly-submitted .elderly-stat-background { background: linear-gradient(135deg, #ffc107, #e0a800); }
.elderly-stat-card.elderly-reviewing .elderly-stat-background { background: linear-gradient(135deg, #17a2b8, #138496); }
.elderly-stat-card.elderly-approved .elderly-stat-background { background: linear-gradient(135deg, #28a745, #1e7e34); }
.elderly-stat-card.elderly-completed .elderly-stat-background { background: linear-gradient(135deg, #6f42c1, #5a32a3); }

.elderly-stat-icon {
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

.elderly-total .elderly-stat-icon {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(102, 126, 234, 0.05));
    color: var(--elderly-primary-blue);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
}

.elderly-submitted .elderly-stat-icon {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
    color: #e0a800;
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
}

.elderly-reviewing .elderly-stat-icon {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.15), rgba(23, 162, 184, 0.05));
    color: var(--elderly-info-color);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.2);
}

.elderly-approved .elderly-stat-icon {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.15), rgba(40, 167, 69, 0.05));
    color: var(--elderly-success-color);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
}

.elderly-completed .elderly-stat-icon {
    background: linear-gradient(135deg, rgba(111, 66, 193, 0.15), rgba(111, 66, 193, 0.05));
    color: var(--elderly-purple-color);
    box-shadow: 0 8px 25px rgba(111, 66, 193, 0.2);
}

.elderly-stat-content h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--elderly-text-dark);
}

.elderly-stat-content p {
    color: var(--elderly-text-dark);
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.elderly-stat-trend {
    color: var(--elderly-text-muted);
    font-size: 0.9rem;
}

/* Quick Actions */
.elderly-quick-actions-card {
    padding: 2rem 2.5rem;
}

.elderly-actions-header {
    text-align: center;
    margin-bottom: 2rem;
}

.elderly-actions-header h5 {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.elderly-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.elderly-action-button {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(102, 126, 234, 0.1);
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

.elderly-action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.6) 0%, rgba(118, 75, 162, 0.5) 100%);
    transition: all 0.3s ease;
    z-index: 0;
}

.elderly-action-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    border-color: rgba(102, 126, 234, 0.5);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.elderly-action-button:hover::before {
    left: 0;
}

.elderly-action-button:hover .elderly-action-icon,
.elderly-action-button:hover .elderly-action-content {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.9);
}

.elderly-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--elderly-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    position: relative;
    z-index: 1;
}

.elderly-action-content {
    position: relative;
    z-index: 1;
}

.elderly-action-content h6 {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.elderly-action-content small {
    color: var(--elderly-text-muted);
    font-size: 0.9rem;
}

/* Elderly AW ODS List */
.elderly-list-card {
    padding: 0;
}

.elderly-list-header {
    padding: 2rem 2.5rem 1rem;
    border-bottom: 1px solid var(--elderly-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.elderly-list-header h4 {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.elderly-list-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.elderly-list-count {
    background: var(--elderly-gradient-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.elderly-filter-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(102, 126, 234, 0.1);
    color: var(--elderly-primary-blue);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.elderly-clear-filter {
    background: var(--elderly-danger-color);
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

.elderly-clear-filter:hover {
    transform: scale(1.1);
}

.elderly-container {
    padding: 1rem 2.5rem 2.5rem;
}

.elderly-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--elderly-border-light);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: elderlySlideUp 0.6s ease forwards;
}

.elderly-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--elderly-gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.elderly-item:hover::before {
    transform: scaleX(1);
}

.elderly-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--elderly-shadow-strong);
}

.elderly-item.hidden {
    display: none;
}

.elderly-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.elderly-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.elderly-id-badge {
    background: var(--elderly-gradient-light);
    color: var(--elderly-primary-blue);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 1px solid rgba(102, 126, 234, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.elderly-date {
    color: var(--elderly-text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.elderly-status-badge.elderly-modern {
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

.elderly-type-display {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.4rem;
    line-height: 1.3;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.elderly-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.elderly-meta-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.elderly-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--elderly-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--elderly-primary-blue);
    font-size: 0.9rem;
}

.elderly-meta-content {
    display: flex;
    flex-direction: column;
}

.elderly-meta-content small {
    color: var(--elderly-text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.elderly-meta-content span {
    color: var(--elderly-text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}

.elderly-preview {
    background: var(--elderly-gradient-light);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--elderly-primary-blue);
    margin-bottom: 1.5rem;
}

.elderly-preview p {
    color: var(--elderly-text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.elderly-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.elderly-action-btn {
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

.elderly-action-btn.elderly-primary {
    background: var(--elderly-gradient-primary);
    color: white;
}

.elderly-action-btn.elderly-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.elderly-action-btn.elderly-secondary {
    background: rgba(102, 126, 234, 0.1);
    color: var(--elderly-primary-blue);
    border: 1px solid rgba(102, 126, 234, 0.3);
}

.elderly-action-btn.elderly-secondary:hover {
    background: var(--elderly-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.elderly-action-btn.elderly-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #e0a800;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.elderly-action-btn.elderly-warning:hover {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
}

/* Status Colors */
.elderly-status-submitted { background: var(--elderly-warning-color) !important; }
.elderly-status-reviewing { background: var(--elderly-info-color) !important; }
.elderly-status-approved { background: var(--elderly-success-color) !important; }
.elderly-status-rejected { background: var(--elderly-danger-color) !important; }
.elderly-status-completed { background: var(--elderly-purple-color) !important; }

/* No Results */
.elderly-no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--elderly-text-muted);
}

.elderly-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--elderly-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--elderly-primary-blue);
}

.elderly-no-results h5 {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.elderly-no-results p {
    margin-bottom: 2rem;
}

/* Empty State */
.elderly-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    position: relative;
}

.elderly-empty-illustration {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.elderly-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--elderly-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: var(--elderly-primary-blue);
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.elderly-icon-decoration {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 35px;
    height: 35px;
    background: var(--elderly-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    border: 3px solid white;
}

.elderly-empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.elderly-circle {
    position: absolute;
    border: 2px solid rgba(102, 126, 234, 0.1);
    border-radius: 50%;
    animation: elderlyPulse 2s infinite;
}

.elderly-circle-1 {
    width: 160px;
    height: 160px;
    top: -80px;
    left: -80px;
    animation-delay: 0s;
}

.elderly-circle-2 {
    width: 200px;
    height: 200px;
    top: -100px;
    left: -100px;
    animation-delay: 0.5s;
}

.elderly-circle-3 {
    width: 240px;
    height: 240px;
    top: -120px;
    left: -120px;
    animation-delay: 1s;
}

.elderly-empty-content h5 {
    color: var(--elderly-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.elderly-empty-content p {
    color: var(--elderly-text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Animations */
@keyframes elderlyShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes elderlyAvatarShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes elderlySlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes elderlyPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 0.1;
    }
}

@keyframes elderlyFadeIn {
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
    .elderly-page-title {
        font-size: 2.2rem;
    }
    
    .elderly-page-subtitle {
        font-size: 1rem;
    }
    
    .elderly-user-info-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .elderly-user-status {
        margin-left: 0;
    }
    
    .elderly-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .elderly-stat-card {
        padding: 1.2rem 0.8rem;
    }
    
    .elderly-stat-content h3 {
        font-size: 1.8rem;
    }
    
    .elderly-stat-content p {
        font-size: 0.9rem;
    }
    
    .elderly-stat-trend small {
        font-size: 0.7rem;
    }
    
    .elderly-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .elderly-list-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .elderly-list-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .elderly-container {
        padding: 1rem;
    }
    
    .elderly-item {
        padding: 1.5rem;
    }
    
    .elderly-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .elderly-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .elderly-actions {
        flex-direction: column;
    }
    
    .elderly-action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .elderly-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .elderly-user-avatar {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .elderly-type-display {
        font-size: 1.2rem;
    }
}

@media print {
    .elderly-action-btn,
    .elderly-quick-actions-card {
        display: none !important;
    }
    
    .elderly-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .elderly-stat-card {
        cursor: default;
    }
}
</style>

<div class="elderly-bg-pages">
    <div class="elderly-container-pages">
        
        <!-- Page Header -->
        <div class="elderly-page-header">
            <div class="elderly-header-decoration"></div>
            <h1 class="elderly-page-title">
                <i class="fas fa-user-clock me-3"></i>
                เบี้ยยังชีพผู้สูงอายุ หรือ ผู้พิการ
            </h1>
            <p class="elderly-page-subtitle">ติดตามสถานะและจัดการการยื่นขอรับเบี้ยยังชีพผู้สูงอายุ หรือ ผู้พิการ</p>
        </div>

        <!-- User Info Card -->
        <div class="elderly-modern-card elderly-user-info-card">
            <div class="elderly-card-gradient-bg"></div>
            <div class="elderly-user-info-content">
                <div class="elderly-user-avatar">
                    <?php 
                    // ระบบจัดการรูปโปรไฟล์
                    $profile_img = $user_info['mp_img'] ?? '';
                    $mp_fname = $user_info['mp_fname'] ?? $user_info['fname'] ?? '';
                    $mp_lname = $user_info['mp_lname'] ?? $user_info['lname'] ?? '';
                    $mp_prefix = $user_info['mp_prefix'] ?? $user_info['prefix'] ?? '';
                    
                    // กำหนด path สำหรับรูปโปรไฟล์
                    $profile_path = '';
                    $show_image = false;
                    
                    if (!empty($profile_img)) {
                        // ลำดับที่ 1: ลองหาใน docs/img/avatar/ ก่อน (รูปจาก register)
                        $avatar_path = 'docs/img/avatar/' . $profile_img;
                        if (file_exists(FCPATH . $avatar_path)) {
                            $profile_path = $avatar_path;
                            $show_image = true;
                        } else {
                            // ลำดับที่ 2: ลองหาใน docs/img/ (รูปที่เปลี่ยนเอง)
                            $user_path = 'docs/img/' . $profile_img;
                            if (file_exists(FCPATH . $user_path)) {
                                $profile_path = $user_path;
                                $show_image = true;
                            }
                        }
                    }
                    
                    // สร้างชื่อเริ่มต้น (Initial) สำหรับ fallback
                    $initials = '';
                    if (!empty($mp_fname)) {
                        $initials .= mb_substr($mp_fname, 0, 1);
                    }
                    if (!empty($mp_lname)) {
                        $initials .= mb_substr($mp_lname, 0, 1);
                    }
                    if (empty($initials)) {
                        $initials = 'U'; // User
                    }
                    ?>
                    
                    <?php if ($show_image): ?>
                        <!-- แสดงรูปโปรไฟล์ -->
                        <img src="<?php echo base_url($profile_path); ?>" 
                             alt="Profile" 
                             class="elderly-profile-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback เมื่อโหลดรูปไม่ได้ -->
                        <div class="elderly-profile-fallback" style="display: none;">
                            <span class="elderly-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php else: ?>
                        <!-- แสดง initials เมื่อไม่มีรูปโปรไฟล์ -->
                        <div class="elderly-profile-fallback">
                            <span class="elderly-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="elderly-user-details">
                    <h4 class="elderly-user-name">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php 
                        $full_name = trim($mp_prefix . ' ' . $mp_fname . ' ' . $mp_lname);
                        echo htmlspecialchars($full_name ?: 'ผู้ใช้'); 
                        ?>
                    </h4>
                    <p class="elderly-user-email">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($user_info['email'] ?? $user_info['mp_email'] ?? ''); ?>
                    </p>
                </div>
                <div class="elderly-user-status">
                    <span class="elderly-status-active">
                        <i class="fas fa-check-circle me-1"></i>
                        สมาชิกที่ยืนยันแล้ว
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="elderly-modern-card elderly-stats-dashboard">
            <div class="elderly-dashboard-header">
                <h4><i class="fas fa-chart-pie me-2"></i>สถิติการยื่นขอเบี้ยยังชีพ</h4>
                <span class="elderly-dashboard-subtitle">คลิกเพื่อกรองรายการตามสถานะ</span>
            </div>
            
            <div class="elderly-stats-grid">
                <div class="elderly-stat-card elderly-total elderly-active" data-filter="all">
                    <div class="elderly-stat-background"></div>
                    <div class="elderly-stat-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="elderly-stat-content">
                        <h3><?php echo $status_counts['total']; ?></h3>
                        <p>ทั้งหมด</p>
                        <div class="elderly-stat-trend">
                            <small>รายการทั้งหมด</small>
                        </div>
                    </div>
                </div>
                
                <div class="elderly-stat-card elderly-submitted" data-filter="submitted">
                    <div class="elderly-stat-background"></div>
                    <div class="elderly-stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="elderly-stat-content">
                        <h3><?php echo $status_counts['submitted']; ?></h3>
                        <p>ยื่นเรื่องแล้ว</p>
                        <div class="elderly-stat-trend">
                            <small>รอตรวจสอบ</small>
                        </div>
                    </div>
                </div>
                
                <div class="elderly-stat-card elderly-reviewing" data-filter="reviewing">
                    <div class="elderly-stat-background"></div>
                    <div class="elderly-stat-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="elderly-stat-content">
                        <h3><?php echo $status_counts['reviewing']; ?></h3>
                        <p>กำลังพิจารณา</p>
                        <div class="elderly-stat-trend">
                            <small>อยู่ระหว่างตรวจสอบ</small>
                        </div>
                    </div>
                </div>
                
                <div class="elderly-stat-card elderly-approved" data-filter="approved">
                    <div class="elderly-stat-background"></div>
                    <div class="elderly-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="elderly-stat-content">
                        <h3><?php echo $status_counts['approved']; ?></h3>
                        <p>อนุมัติแล้ว</p>
                        <div class="elderly-stat-trend">
                            <small>ผ่านการอนุมัติ</small>
                        </div>
                    </div>
                </div>
                
                <div class="elderly-stat-card elderly-completed" data-filter="completed">
                    <div class="elderly-stat-background"></div>
                    <div class="elderly-stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="elderly-stat-content">
                        <h3><?php echo $status_counts['completed']; ?></h3>
                        <p>เสร็จสิ้น</p>
                        <div class="elderly-stat-trend">
                            <small>ดำเนินการครบถ้วน</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="elderly-modern-card elderly-quick-actions-card">
            <div class="elderly-actions-header">
                <h5><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
            </div>
            <div class="elderly-actions-grid">
                <a href="<?php echo site_url('Elderly_aw_ods/adding_elderly_aw_ods'); ?>" class="elderly-action-button">
                    <div class="elderly-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="elderly-action-content">
                        <h6>ยื่นขอเบี้ยยังชีพใหม่</h6>
                        <small>สร้างคำขอใหม่สำหรับเบี้ยยังชีพ</small>
                    </div>
                </a>
                
            </div>
        </div>

        <!-- Elderly AW ODS List -->
        <div class="elderly-modern-card elderly-list-card">
            <div class="elderly-list-header">
                <h4><i class="fas fa-user-clock me-2"></i>รายการเบี้ยยังชีพของฉัน</h4>
                <div class="elderly-list-controls">
                    <span class="elderly-list-count" id="elderly-count"><?php echo count($elderly_aw_ods); ?> รายการ</span>
                    <div class="elderly-filter-indicator" id="elderly-filter-indicator">
                        <i class="fas fa-filter me-1"></i>
                        <span id="elderly-filter-text">ทั้งหมด</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($elderly_aw_ods)): ?>
                <div class="elderly-container">
                    <?php foreach ($elderly_aw_ods as $index => $elderly): 
                        // ข้อมูลที่ประมวลผลแล้ว
                        $latest_status = $elderly->status_display ?? 'ยื่นเรื่องแล้ว';
                        $status_class = $elderly->status_class ?? 'elderly-status-submitted';
                        $status_icon = $elderly->status_icon ?? 'fas fa-file-alt';
                        $status_color = $elderly->status_color ?? '#ffc107';
                        
                        // Format date เป็นรูปแบบไทย
                        $formatted_date = convertToThaiDate($elderly->elderly_aw_ods_datesave ?? '');
                        
                        // Latest update เป็นรูปแบบไทย
                        $latest_update = '';
                        if (!empty($elderly->elderly_aw_ods_updated_at)) {
                            $latest_update = convertToThaiDate($elderly->elderly_aw_ods_updated_at);
                        }
                        
                        // Animation delay
                        $animation_delay = $index * 100;
                        
                        // Status mapping for filter
                        $filter_status = 'all';
                        $actual_status = $elderly->elderly_aw_ods_status ?? 'submitted';
                        
                        switch ($actual_status) {
                            case 'submitted':
                                $filter_status = 'submitted';
                                break;
                            case 'reviewing':
                                $filter_status = 'reviewing';
                                break;
                            case 'approved':
                                $filter_status = 'approved';
                                break;
                            case 'rejected':
                                $filter_status = 'rejected';
                                break;
                            case 'completed':
                                $filter_status = 'completed';
                                break;
                            default:
                                $filter_status = 'submitted';
                                break;
                        }
                        
                        // Type display
                        $type_display = '';
                        $type_icon = '';
                        switch($elderly->elderly_aw_ods_type ?? 'elderly') {
                            case 'elderly':
                                $type_display = 'เบี้ยยังชีพผู้สูงอายุ';
                                $type_icon = 'fas fa-user-clock';
                                break;
                            case 'disabled':
                                $type_display = 'เบี้ยยังชีพผู้พิการ';
                                $type_icon = 'fas fa-wheelchair';
                                break;
                            default:
                                $type_display = 'เบี้ยยังชีพผู้สูงอายุ';
                                $type_icon = 'fas fa-user-clock';
                                break;
                        }
                    ?>
                        <div class="elderly-item" 
                             style="animation-delay: <?php echo $animation_delay; ?>ms;"
                             data-status="<?php echo $filter_status; ?>"
                             data-original-status="<?php echo htmlspecialchars($latest_status); ?>">
                            <div class="elderly-header">
                                <div class="elderly-id-section">
                                    <div class="elderly-id-badge">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <?php echo htmlspecialchars($elderly->elderly_aw_ods_id ?? ''); ?>
                                    </div>
                                    <div class="elderly-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        ยื่นวันที่: <?php echo $formatted_date; ?>
                                    </div>
                                </div>
                                <div class="elderly-status-section">
                                    <span class="elderly-status-badge elderly-modern <?php echo $status_class; ?>" style="background: <?php echo $status_color; ?>;">
                                        <i class="<?php echo $status_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($latest_status); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="elderly-body">
                                <h5 class="elderly-type-display">
                                    <i class="<?php echo $type_icon; ?>"></i>
                                    <?php echo htmlspecialchars($type_display); ?>
                                </h5>
                                
                                <div class="elderly-meta">
                                    <div class="elderly-meta-item">
                                        <div class="elderly-meta-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="elderly-meta-content">
                                            <small>ผู้ยื่นคำขอ</small>
                                            <span><?php echo htmlspecialchars($elderly->elderly_aw_ods_by ?? ''); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="elderly-meta-item">
                                        <div class="elderly-meta-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="elderly-meta-content">
                                            <small>เบอร์โทรศัพท์</small>
                                            <span><?php echo htmlspecialchars($elderly->elderly_aw_ods_phone ?? ''); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($latest_update): ?>
                                    <div class="elderly-meta-item">
                                        <div class="elderly-meta-icon">
                                            <i class="fas fa-sync-alt"></i>
                                        </div>
                                        <div class="elderly-meta-content">
                                            <small>อัปเดตล่าสุด</small>
                                            <span><?php echo $latest_update; ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($elderly->elderly_aw_ods_address)): ?>
                                <div class="elderly-preview">
                                    <p>
                                        <strong>ที่อยู่:</strong> 
                                        <?php echo nl2br(htmlspecialchars($elderly->elderly_aw_ods_address)); ?>
                                        <?php 
                                        // แสดงข้อมูลที่อยู่เพิ่มเติม
                                        $address_parts = [];
                                        if (!empty($elderly->guest_district)) $address_parts[] = 'ตำบล' . $elderly->guest_district;
                                        if (!empty($elderly->guest_amphoe)) $address_parts[] = 'อำเภอ' . $elderly->guest_amphoe;
                                        if (!empty($elderly->guest_province)) $address_parts[] = 'จังหวัด' . $elderly->guest_province;
                                        if (!empty($elderly->guest_zipcode)) $address_parts[] = $elderly->guest_zipcode;
                                        
                                        if (!empty($address_parts)) {
                                            echo '<br>' . implode(' ', $address_parts);
                                        }
                                        ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="elderly-actions">
                                <a href="<?php echo site_url('Elderly_aw_ods/my_elderly_aw_ods_detail/' . ($elderly->elderly_aw_ods_id ?? '')); ?>" 
                                   class="elderly-action-btn elderly-primary">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                                
                                <?php 
                                // ตรวจสอบสถานะเพื่อแสดงปุ่มแก้ไข
                                $current_status = $elderly->elderly_aw_ods_status ?? 'submitted';
                                $editable_statuses = ['submitted', 'reviewing']; // สถานะที่สามารถแก้ไขได้
                                $can_edit = in_array($current_status, $editable_statuses);
                                ?>
                                
                                <?php if ($can_edit): ?>
                                <button onclick="openEditModal('<?php echo htmlspecialchars($elderly->elderly_aw_ods_id ?? ''); ?>')" 
                                        class="elderly-action-btn elderly-warning">
                                    <i class="fas fa-edit me-2"></i>แก้ไข/เพิ่มเอกสาร
                                </button>
                                <?php endif; ?>
                                
                                <button onclick="copyElderlyId('<?php echo htmlspecialchars($elderly->elderly_aw_ods_id ?? ''); ?>')" 
                                        class="elderly-action-btn elderly-secondary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                                </button>
                                <a href="<?php echo site_url('Elderly_aw_ods/follow_elderly_aw_ods?ref=' . urlencode($elderly->elderly_aw_ods_id ?? '')); ?>" 
                                   class="elderly-action-btn elderly-secondary">
                                    <i class="fas fa-search me-2"></i>ติดตามสถานะ
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Results Message -->
                <div class="elderly-no-results" id="elderly-no-results" style="display: none;">
                    <div class="elderly-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>ไม่พบรายการที่ตรงกับเงื่อนไข</h5>
                    <p>ลองเปลี่ยนตัวกรองหรือดูรายการทั้งหมด</p>
                    <button onclick="filterElderlyByStatus('all')" class="elderly-action-btn elderly-primary">
                        <i class="fas fa-list me-2"></i>ดูทั้งหมด
                    </button>
                </div>
                
            <?php else: ?>
                <!-- Empty State -->
                <div class="elderly-empty-state">
                    <div class="elderly-empty-illustration">
                        <div class="elderly-empty-icon">
                            <i class="fas fa-user-clock"></i>
                            <div class="elderly-icon-decoration">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="elderly-empty-circles">
                            <div class="elderly-circle elderly-circle-1"></div>
                            <div class="elderly-circle elderly-circle-2"></div>
                            <div class="elderly-circle elderly-circle-3"></div>
                        </div>
                    </div>
                    <div class="elderly-empty-content">
                        <h5>ยังไม่มีการยื่นขอเบี้ยยังชีพ</h5>
                        <p>คุณยังไม่เคยยื่นขอรับเบี้ยยังชีพในระบบ<br>เริ่มต้นโดยการยื่นขอเบี้ยยังชีพรายการแรกของคุณ</p>
                        <a href="<?php echo site_url('Elderly_aw_ods/adding_elderly_aw_ods'); ?>" class="elderly-action-btn elderly-primary elderly-large">
                            <i class="fas fa-plus me-2"></i>ยื่นขอเบี้ยยังชีพ
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editElderlyModal" tabindex="-1" aria-labelledby="editElderlyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: var(--elderly-shadow-strong);">
            <div class="modal-header" style="background: var(--elderly-gradient-primary); color: white; border-radius: 20px 20px 0 0; border: none;">
                <h5 class="modal-title" id="editElderlyModalLabel">
                    <i class="fas fa-edit me-2"></i>แก้ไขข้อมูลและเพิ่มเอกสาร
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editElderlyForm" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 2rem;">
                    <input type="hidden" id="edit_elderly_id" name="elderly_id">
                    
                    <!-- แสดงข้อมูลปัจจุบัน -->
                    <div class="alert alert-info" style="border-radius: 12px; border: none; background: var(--elderly-light-blue);">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>ข้อมูลปัจจุบัน</h6>
                        <div id="current_elderly_info">
                            <!-- ข้อมูลจะถูกโหลดด้วย JavaScript -->
                        </div>
                    </div>
                    
                    <!-- ฟอร์มแก้ไขข้อมูล -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_elderly_phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>เบอร์โทรศัพท์
                            </label>
                            <input type="tel" class="form-control" id="edit_elderly_phone" name="elderly_phone" 
                                   style="border-radius: 12px; border: 2px solid var(--elderly-border-light); padding: 0.75rem;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_elderly_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>อีเมล (ไม่บังคับ)
                            </label>
                            <input type="email" class="form-control" id="edit_elderly_email" name="elderly_email" 
                                   style="border-radius: 12px; border: 2px solid var(--elderly-border-light); padding: 0.75rem;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_elderly_address" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i>ที่อยู่
                        </label>
                        <textarea class="form-control" id="edit_elderly_address" name="elderly_address" rows="3"
                                  style="border-radius: 12px; border: 2px solid var(--elderly-border-light); padding: 0.75rem;"></textarea>
                    </div>
                    
                    <!-- เพิ่มเอกสาร -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-paperclip me-1"></i>เพิ่มเอกสารประกอบ (รูปภาพ หรือ PDF)
                        </label>
                        <div class="file-upload-area" style="border: 2px dashed var(--elderly-border-light); border-radius: 12px; padding: 2rem; text-align: center; background: var(--elderly-very-light-blue); transition: all 0.3s ease;">
                            <div class="file-upload-icon" style="font-size: 3rem; color: var(--elderly-primary-blue); margin-bottom: 1rem;">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text" style="color: var(--elderly-text-muted); margin-bottom: 1rem;">
                                <strong>คลิกเพื่อเลือกไฟล์</strong> หรือลากไฟล์มาวางที่นี่<br>
                                <small>รองรับไฟล์: JPG, PNG, GIF, PDF (ขนาดไม่เกิน 5MB ต่อไฟล์)</small>
                            </div>
                            <input type="file" id="elderly_additional_files" name="elderly_additional_files[]" 
                                   multiple accept=".jpg,.jpeg,.png,.gif,.pdf" style="display: none;">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('elderly_additional_files').click();" 
                                    style="border-radius: 12px; border: 2px solid var(--elderly-primary-blue); color: var(--elderly-primary-blue); padding: 0.75rem 1.5rem;">
                                <i class="fas fa-folder-open me-2"></i>เลือกไฟล์
                            </button>
                        </div>
                        
                        <!-- แสดงไฟล์ที่เลือก -->
                        <div id="selected_files_preview" class="mt-3" style="display: none;">
                            <h6><i class="fas fa-list me-2"></i>ไฟล์ที่เลือก:</h6>
                            <div id="files_list"></div>
                        </div>
                    </div>
                    
                    <!-- แสดงไฟล์ที่มีอยู่แล้ว -->
                    <div id="existing_files_section" class="mb-3">
                        <h6><i class="fas fa-file-alt me-2"></i>เอกสารที่มีอยู่แล้ว:</h6>
                        <div id="existing_files_list">
                            <!-- ไฟล์ที่มีอยู่จะถูกโหลดด้วย JavaScript -->
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer" style="border: none; padding: 1.5rem 2rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" 
                            style="border-radius: 12px; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-times me-2"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary" id="save_elderly_btn" 
                            style="background: var(--elderly-gradient-primary); border: none; border-radius: 12px; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
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
    console.log('🚀 เริ่มต้นระบบเบี้ยยังชีพของฉัน');
    
    // *** ตัวแปรสำคัญ ***
    let currentElderlyFilter = 'all';
    let originalStatusCounts = null;
    
    // *** บันทึกข้อมูลสถิติเดิม ***
    if (!originalStatusCounts) {
        originalStatusCounts = {
            total: parseInt($('.elderly-stat-card.elderly-total .elderly-stat-content h3').text()) || 0,
            submitted: parseInt($('.elderly-stat-card.elderly-submitted .elderly-stat-content h3').text()) || 0,
            reviewing: parseInt($('.elderly-stat-card.elderly-reviewing .elderly-stat-content h3').text()) || 0,
            approved: parseInt($('.elderly-stat-card.elderly-approved .elderly-stat-content h3').text()) || 0,
            completed: parseInt($('.elderly-stat-card.elderly-completed .elderly-stat-content h3').text()) || 0
        };
        console.log('📊 สถิติเดิม:', originalStatusCounts);
    }
    
    // *** ฟังก์ชันกรองเบี้ยยังชีพ ***
    window.filterElderlyByStatus = function(filter) {
        console.log('🔍 กรองสถิติ:', filter);
        
        currentElderlyFilter = filter;
        
        // *** เอาการอัปเดต active state ของ stat cards ***
        $('.elderly-stat-card').removeClass('elderly-active');
        $('.elderly-stat-card[data-filter="' + filter + '"]').addClass('elderly-active');
        
        // *** กรองรายการเบี้ยยังชีพ ***
        const elderlyItems = $('.elderly-item');
        const noResults = $('#elderly-no-results');
        let visibleCount = 0;
        
        // *** ซ่อน/แสดงรายการ ***
        elderlyItems.each(function() {
            const $item = $(this);
            const itemStatus = $item.attr('data-status');
            
            if (filter === 'all' || itemStatus === filter) {
                $item.show().removeClass('hidden');
                visibleCount++;
                
                // Re-trigger animation
                $item.css('animation', 'none');
                setTimeout(() => {
                    $item.css('animation', 'elderlyFadeIn 0.5s ease forwards');
                }, 10);
            } else {
                $item.hide().addClass('hidden');
            }
        });
        
        // *** อัปเดตจำนวนที่แสดง ***
        const countElement = $('#elderly-count');
        if (countElement.length) {
            countElement.text(visibleCount + ' รายการ');
        }
        
        // *** อัปเดต filter indicator ***
        updateFilterIndicator(filter, visibleCount);
        
        // *** แสดง/ซ่อนข้อความไม่พบข้อมูล ***
        if (noResults.length) {
            if (visibleCount === 0 && filter !== 'all') {
                noResults.show().css('animation', 'elderlyFadeIn 0.5s ease forwards');
            } else {
                noResults.hide();
            }
        }
        
        // *** Smooth scroll ***
        if (filter !== 'all') {
            const elderlyList = $('.elderly-list-card');
            if (elderlyList.length) {
                elderlyList.get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        console.log('✅ กรองเสร็จ - แสดง:', visibleCount, 'รายการ');
    };
    
    // *** ฟังก์ชันอัปเดต filter indicator ***
    function updateFilterIndicator(filter, visibleCount) {
        const filterText = $('#elderly-filter-text');
        const clearFilterBtn = $('.elderly-clear-filter');
        
        if (filterText.length) {
            const filterNames = {
                'all': 'ทั้งหมด',
                'submitted': 'ยื่นเรื่องแล้ว',
                'reviewing': 'กำลังพิจารณา',
                'approved': 'อนุมัติแล้ว',
                'rejected': 'ไม่อนุมัติ',
                'completed': 'เสร็จสิ้น'
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
    
    // *** Open Edit Modal Function - ระบบจริง ***
    window.openEditModal = function(elderlyId) {
        console.log('📝 เปิด Modal แก้ไข:', elderlyId);
        
        if (!elderlyId) {
            showElderlyAlert('ไม่พบหมายเลขอ้างอิง', 'error');
            return;
        }
        
        // เก็บ elderly ID ใน hidden field
        $('#edit_elderly_id').val(elderlyId);
        
        // แสดง loading
        showEditModalLoading();
        
        // เปิด Modal
        const modal = new bootstrap.Modal(document.getElementById('editElderlyModal'));
        modal.show();
        
        // โหลดข้อมูลจาก server
        loadElderlyDataFromServer(elderlyId);
    };
    
    // *** แสดง Loading ใน Modal ***
    function showEditModalLoading() {
        $('#current_elderly_info').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <div class="mt-2">กำลังโหลดข้อมูล...</div>
            </div>
        `);
        
        $('#existing_files_list').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <div class="mt-2">กำลังโหลดไฟล์...</div>
            </div>
        `);
        
        // ล้างฟอร์ม
        $('#edit_elderly_phone').val('');
        $('#edit_elderly_email').val('');
        $('#edit_elderly_address').val('');
    }
    
    // *** โหลดข้อมูลจาก Server ***
    function loadElderlyDataFromServer(elderlyId) {
        $.ajax({
            url: '<?php echo site_url("Elderly_aw_ods/get_elderly_data"); ?>',
            type: 'POST',
            data: {
                elderly_id: elderlyId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateEditForm(response.data);
                } else {
                    showElderlyAlert(response.message || 'ไม่สามารถโหลดข้อมูลได้', 'error');
                    bootstrap.Modal.getInstance(document.getElementById('editElderlyModal')).hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showElderlyAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                bootstrap.Modal.getInstance(document.getElementById('editElderlyModal')).hide();
            }
        });
    }
    
    // *** เติมข้อมูลในฟอร์ม ***
    function populateEditForm(data) {
        // แสดงข้อมูลปัจจุบัน
        $('#current_elderly_info').html(`
            <div class="row">
                <div class="col-md-6">
                    <strong>หมายเลขอ้างอิง:</strong> ${data.elderly_aw_ods_id}<br>
                    <strong>ประเภท:</strong> ${data.elderly_aw_ods_type === 'elderly' ? 'ผู้สูงอายุ' : 'ผู้พิการ'}<br>
                    <strong>ผู้ยื่นคำขอ:</strong> ${data.elderly_aw_ods_by}
                </div>
                <div class="col-md-6">
                    <strong>สถานะ:</strong> ${getStatusDisplay(data.elderly_aw_ods_status)}<br>
                    <strong>วันที่ยื่น:</strong> ${formatThaiDate(data.elderly_aw_ods_datesave)}
                </div>
            </div>
        `);
        
        // กรอกข้อมูลในฟอร์ม
        $('#edit_elderly_phone').val(data.elderly_aw_ods_phone || '');
        $('#edit_elderly_email').val(data.elderly_aw_ods_email || '');
        $('#edit_elderly_address').val(data.elderly_aw_ods_address || '');
        
        // โหลดไฟล์ที่มีอยู่
        displayExistingFiles(data.files || []);
    }
    
    // *** แสดงไฟล์ที่มีอยู่แล้ว ***
    function displayExistingFiles(files) {
        if (files.length > 0) {
            let filesHtml = '<div class="row">';
            files.forEach(file => {
                const icon = getFileIcon(file.file_type);
                const fileSize = formatFileSize(file.file_size);
                const uploadDate = formatThaiDate(file.uploaded_at);
                
                filesHtml += `
                    <div class="col-md-6 mb-2">
                        <div class="existing-file-item" data-file-id="${file.file_id}" 
                             style="background: var(--elderly-light-blue); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="${icon}" style="font-size: 1.5rem;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--elderly-text-dark);">${file.original_name}</div>
                                <small style="color: var(--elderly-text-muted);">${fileSize} • ${uploadDate}</small>
                            </div>
                            <div class="file-actions">
                                <a href="${file.download_url}" class="btn btn-sm btn-outline-primary me-1" 
                                   target="_blank" title="ดาวน์โหลด" style="border-radius: 6px;">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="removeExistingFile('${file.file_id}', '${file.original_name}')" 
                                        style="border-radius: 6px;" title="ลบไฟล์">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            filesHtml += '</div>';
            $('#existing_files_list').html(filesHtml);
        } else {
            $('#existing_files_list').html('<p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>ยังไม่มีเอกสารแนบ</p>');
        }
    }
    
    window.removeExistingFile = function(fileId, fileName) {
    Swal.fire({
        title: 'ยืนยันการลบไฟล์',
        text: `คุณต้องการลบไฟล์ "${fileName}" หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ลบไฟล์',
        cancelButtonText: 'ยกเลิก',
        zIndex: 99999  // *** เพิ่ม z-index สูงสุด ***
    }).then((result) => {
        if (result.isConfirmed) {
            deleteFileFromServer(fileId, fileName);
        }
    });
};

// *** เพิ่ม CSS สำหรับ SweetAlert z-index ***
const modalFixCSS = `
.swal2-container {
    z-index: 99999 !important;
}
.swal2-backdrop {
    z-index: 99998 !important;
}
`;

// เพิ่ม CSS เข้าไปใน head
const styleSheet = document.createElement('style');
styleSheet.textContent = modalFixCSS;
document.head.appendChild(styleSheet);
	
	
	
    // *** ลบไฟล์จาก Server ***
    function deleteFileFromServer(fileId, fileName) {
        const elderlyId = $('#edit_elderly_id').val();
        
        // แสดง loading บนไฟล์ที่กำลังลบ
        const fileItem = $(`.existing-file-item[data-file-id="${fileId}"]`);
        fileItem.find('.file-actions').html('<div class="spinner-border spinner-border-sm text-danger" role="status"></div>');
        
        $.ajax({
            url: '<?php echo site_url("Elderly_aw_ods/delete_elderly_file"); ?>',
            type: 'POST',
            data: {
                file_id: fileId,
                elderly_id: elderlyId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showElderlyAlert(`ลบไฟล์ "${fileName}" สำเร็จ`, 'success');
                    // ลบ element ออกจาก DOM
                    fileItem.fadeOut(300, function() {
                        $(this).remove();
                        // ตรวจสอบว่าเหลือไฟล์หรือไม่
                        if ($('.existing-file-item').length === 0) {
                            $('#existing_files_list').html('<p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>ยังไม่มีเอกสารแนบ</p>');
                        }
                    });
                } else {
                    showElderlyAlert(response.message || 'ไม่สามารถลบไฟล์ได้', 'error');
                    // คืนค่าปุ่ม
                    fileItem.find('.file-actions').html(`
                        <a href="#" class="btn btn-sm btn-outline-primary me-1" target="_blank" title="ดาวน์โหลด" style="border-radius: 6px;">
                            <i class="fas fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="removeExistingFile('${fileId}', '${fileName}')" 
                                style="border-radius: 6px;" title="ลบไฟล์">
                            <i class="fas fa-trash"></i>
                        </button>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete file error:', error);
                showElderlyAlert('เกิดข้อผิดพลาดในการลบไฟล์', 'error');
                // คืนค่าปุ่ม
                fileItem.find('.file-actions').html(`
                    <a href="#" class="btn btn-sm btn-outline-primary me-1" target="_blank" title="ดาวน์โหลด" style="border-radius: 6px;">
                        <i class="fas fa-download"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="removeExistingFile('${fileId}', '${fileName}')" 
                            style="border-radius: 6px;" title="ลบไฟล์">
                        <i class="fas fa-trash"></i>
                    </button>
                `);
            }
        });
    }
    
    // *** Form Submit - ระบบจริง ***
    $('#editElderlyForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#save_elderly_btn');
        const originalText = submitBtn.html();
        
        // Validation
        const phone = $('#edit_elderly_phone').val().trim();
        if (!phone) {
            showElderlyAlert('กรุณากรอกเบอร์โทรศัพท์', 'warning');
            $('#edit_elderly_phone').focus();
            return;
        }
        
        // แสดง Loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...');
        
        // สร้าง FormData
        const formData = new FormData(this);
        
        // ส่งข้อมูลไป Server
        $.ajax({
            url: '<?php echo site_url("Elderly_aw_ods/update_elderly_data"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showElderlyAlert(response.message || 'บันทึกการเปลี่ยนแปลงสำเร็จ', 'success');
                    
                    // ปิด Modal
                    bootstrap.Modal.getInstance(document.getElementById('editElderlyModal')).hide();
                    
                    // รีเฟรชหน้าหลังจาก 1.5 วินาที
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                    
                } else {
                    showElderlyAlert(response.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Submit error:', error);
                showElderlyAlert('เกิดข้อผิดพลาดในการบันทึก', 'error');
            },
            complete: function() {
                // คืนค่าปุ่ม
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // *** Helper Functions ***
    function getStatusDisplay(status) {
        const statusMap = {
            'submitted': 'ยื่นเรื่องแล้ว',
            'reviewing': 'กำลังพิจารณา',
            'approved': 'อนุมัติแล้ว',
            'rejected': 'ไม่อนุมัติ',
            'completed': 'เสร็จสิ้น'
        };
        return statusMap[status] || status;
    }
    
    function getFileIcon(fileType) {
        if (fileType && fileType.includes('pdf')) {
            return 'fas fa-file-pdf text-danger';
        } else if (fileType && fileType.includes('image')) {
            return 'fas fa-image text-primary';
        } else {
            return 'fas fa-file text-secondary';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function formatThaiDate(dateString) {
        if (!dateString) return '';
        
        try {
            const date = new Date(dateString);
            const thaiMonths = [
                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            
            const day = date.getDate();
            const month = thaiMonths[date.getMonth()];
            const year = date.getFullYear() + 543;
            const time = date.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
            
            return `${day} ${month} ${year} เวลา ${time} น.`;
        } catch (e) {
            return dateString;
        }
    }
    
    // *** File Upload Handling ***
    $('#elderly_additional_files').on('change', function() {
        const files = this.files;
        if (files.length > 0) {
            displaySelectedFiles(files);
        }
    });
    
    function displaySelectedFiles(files) {
        let filesHtml = '';
        let totalSize = 0;
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            totalSize += file.size;
            
            // ตรวจสอบประเภทไฟล์
            if (!allowedTypes.includes(file.type)) {
                showElderlyAlert(`ไฟล์ "${file.name}" ไม่ได้รับอนุญาต`, 'error');
                continue;
            }
            
            // ตรวจสอบขนาดไฟล์
            if (file.size > maxSize) {
                showElderlyAlert(`ไฟล์ "${file.name}" มีขนาดใหญ่เกิน 5MB`, 'error');
                continue;
            }
            
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileIcon = file.type.includes('image') ? 'fas fa-image text-primary' : 'fas fa-file-pdf text-danger';
            
            filesHtml += `
                <div class="selected-file-item mb-2" style="background: var(--elderly-very-light-blue); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="${fileIcon}" style="font-size: 1.5rem;"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--elderly-text-dark);">${file.name}</div>
                        <small style="color: var(--elderly-text-muted);">${fileSize} MB • ${file.type}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="removeSelectedFile(${i})" 
                            style="border-radius: 8px;" title="ลบไฟล์">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }
        
        $('#files_list').html(filesHtml);
        $('#selected_files_preview').show();
        
        // แสดงขนาดรวม
        const totalSizeMB = (totalSize / 1024 / 1024).toFixed(2);
        $('#files_list').append(`<small class="text-muted">ขนาดรวม: ${totalSizeMB} MB</small>`);
    }
    
    window.removeSelectedFile = function(index) {
        const fileInput = document.getElementById('elderly_additional_files');
        const dt = new DataTransfer();
        
        for (let i = 0; i < fileInput.files.length; i++) {
            if (i !== index) {
                dt.items.add(fileInput.files[i]);
            }
        }
        
        fileInput.files = dt.files;
        
        if (fileInput.files.length === 0) {
            $('#selected_files_preview').hide();
        } else {
            displaySelectedFiles(fileInput.files);
        }
    };
    
    // *** Drag and Drop Support ***
    const fileUploadArea = $('.file-upload-area');
    
    fileUploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--elderly-primary-blue)',
            'background': 'var(--elderly-light-blue)'
        });
    });
    
    fileUploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--elderly-border-light)',
            'background': 'var(--elderly-very-light-blue)'
        });
    });
    
    fileUploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--elderly-border-light)',
            'background': 'var(--elderly-very-light-blue)'
        });
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('elderly_additional_files').files = files;
            displaySelectedFiles(files);
        }
    });
    
    // *** Form Submit - ระบบจริง ***
    $('#editElderlyForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#save_elderly_btn');
        const originalText = submitBtn.html();
        
        // Validation
        const phone = $('#edit_elderly_phone').val().trim();
        if (!phone) {
            showElderlyAlert('กรุณากรอกเบอร์โทรศัพท์', 'warning');
            $('#edit_elderly_phone').focus();
            return;
        }
        
        // แสดง Loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...');
        
        // สร้าง FormData
        const formData = new FormData(this);
        
        // ส่งข้อมูลไป Server
        $.ajax({
            url: '<?php echo site_url("Elderly_aw_ods/update_elderly_data"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showElderlyAlert(response.message || 'บันทึกการเปลี่ยนแปลงสำเร็จ', 'success');
                    
                    // ปิด Modal
                    bootstrap.Modal.getInstance(document.getElementById('editElderlyModal')).hide();
                    
                    // รีเฟรชหน้าหลังจาก 1.5 วินาที
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                    
                } else {
                    showElderlyAlert(response.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Submit error:', error);
                showElderlyAlert('เกิดข้อผิดพลาดในการบันทึก', 'error');
            },
            complete: function() {
                // คืนค่าปุ่ม
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // *** Reset Modal เมื่อปิด ***
    $('#editElderlyModal').on('hidden.bs.modal', function() {
        $('#editElderlyForm')[0].reset();
        $('#selected_files_preview').hide();
        $('#save_elderly_btn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง');
        $('#current_elderly_info').html('');
        $('#existing_files_list').html('');
    });
    window.copyElderlyId = function(elderlyId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(elderlyId).then(() => {
                showElderlyAlert('คัดลอกหมายเลข ' + elderlyId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopyElderlyText(elderlyId);
            });
        } else {
            fallbackCopyElderlyText(elderlyId);
        }
    };
    
    function fallbackCopyElderlyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showElderlyAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
        } catch (err) {
            showElderlyAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** Alert Functions ***
    function showElderlyAlert(message, type) {
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
                confirmButtonColor: '#667eea'
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
                animation: elderlyAlertShow 0.3s ease;
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
                alertDiv.style.animation = 'elderlyAlertHide 0.3s ease';
                setTimeout(() => alertDiv.remove(), 300);
            }, 4000);
        }
    }
    
    // *** Event Listeners สำหรับการ์ดสถิติ ***
    $('.elderly-stat-card').off('click').on('click', function(e) {
        e.preventDefault();
        
        const filter = $(this).attr('data-filter');
        if (filter) {
            console.log('🎯 คลิกการ์ดสถิติ:', filter);
            filterElderlyByStatus(filter);
        }
    });
    
    // *** Event Listener สำหรับปุ่มล้างตัวกรอง ***
    $('.elderly-clear-filter').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('🗑️ ล้างตัวกรอง');
        filterElderlyByStatus('all');
    });
    
    // *** Animate stats cards ***
    function animateStatsCards() {
        const statCards = $('.elderly-stat-card');
        statCards.each(function(index) {
            const $card = $(this);
            $card.css({
                'opacity': '0',
                'transform': 'translateY(30px)'
            });
            
            setTimeout(() => {
                $card.css({
                    'transition': 'all 0.6s ease',
                    'opacity': '1',
                    'transform': 'translateY(0)'
                });
            }, index * 100);
        });
    }
    
    // *** Animate action buttons ***
    function animateActionButtons() {
        const actionButtons = $('.elderly-action-button');
        actionButtons.each(function(index) {
            const $button = $(this);
            $button.css({
                'opacity': '0',
                'transform': 'translateX(-30px)'
            });
            
            setTimeout(() => {
                $button.css({
                    'transition': 'all 0.6s ease',
                    'opacity': '1',
                    'transform': 'translateX(0)'
                });
            }, 300 + (index * 150));
        });
    }
    
    // *** Enhanced hover effects ***
    function addHoverEffects() {
        const statCards = $('.elderly-stat-card');
        
        statCards.each(function() {
            const $card = $(this);
            
            $card.on('mouseenter', function() {
                if (!$card.hasClass('elderly-active')) {
                    $card.css('transform', 'translateY(-10px) scale(1.02)');
                }
            });
            
            $card.on('mouseleave', function() {
                if (!$card.hasClass('elderly-active')) {
                    $card.css('transform', 'translateY(0) scale(1)');
                }
            });
            
            // Add click effect
            $card.on('mousedown', function() {
                $card.css('transform', 'scale(0.98)');
            });
            
            $card.on('mouseup', function() {
                setTimeout(() => {
                    const transform = $card.hasClass('elderly-active') ? 
                        'translateY(-10px) scale(1.02)' : 'scale(1)';
                    $card.css('transform', transform);
                }, 150);
            });
        });
    }
    
    // *** Scroll to top functionality ***
    function addScrollToTop() {
        let scrollToTopBtn = $('<button>', {
            id: 'scrollToTop',
            html: '<i class="fas fa-chevron-up"></i>',
            css: {
                position: 'fixed',
                bottom: '2rem',
                right: '2rem',
                width: '50px',
                height: '50px',
                borderRadius: '50%',
                background: 'var(--elderly-gradient-primary)',
                color: 'white',
                border: 'none',
                cursor: 'pointer',
                display: 'none',
                zIndex: '1000',
                boxShadow: '0 4px 15px rgba(102, 126, 234, 0.3)',
                transition: 'all 0.3s ease'
            }
        });
        
        $('body').append(scrollToTopBtn);
        
        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                scrollToTopBtn.fadeIn();
            } else {
                scrollToTopBtn.fadeOut();
            }
        });
        
        scrollToTopBtn.click(function() {
            $('html, body').animate({scrollTop: 0}, 'smooth');
        });
        
        scrollToTopBtn.hover(
            function() {
                $(this).css('transform', 'scale(1.1)');
            },
            function() {
                $(this).css('transform', 'scale(1)');
            }
        );
    }
    
    // *** Progress indicator for long lists ***
    function addProgressIndicator() {
        if ($('.elderly-item').length > 5) {
            const progressBar = $('<div>', {
                id: 'readingProgress',
                css: {
                    position: 'fixed',
                    top: '0',
                    left: '0',
                    width: '0%',
                    height: '3px',
                    background: 'var(--elderly-gradient-primary)',
                    zIndex: '9999',
                    transition: 'width 0.3s ease'
                }
            });
            
            $('body').prepend(progressBar);
            
            $(window).scroll(function() {
                const scrollTop = $(window).scrollTop();
                const docHeight = $(document).height() - $(window).height();
                const scrollPercent = (scrollTop / docHeight) * 100;
                
                progressBar.css('width', scrollPercent + '%');
            });
        }
    }
    
    // *** Search functionality ***
    function addSearchFunctionality() {
        const searchBox = $('<div>', {
            class: 'elderly-search-box',
            html: `
                <div style="position: relative; margin-bottom: 1rem;">
                    <input type="text" id="elderly-search" placeholder="ค้นหาเบี้ยยังชีพ..." 
                           style="width: 100%; padding: 0.75rem 1rem 0.75rem 3rem; border: 2px solid var(--elderly-border-light); 
                                  border-radius: 12px; font-size: 1rem; transition: all 0.3s ease;">
                    <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); 
                                                   color: var(--elderly-text-muted);"></i>
                </div>
            `
        });
        
        $('.elderly-list-header').after(searchBox);
        
        $('#elderly-search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            let visibleCount = 0;
            
            $('.elderly-item').each(function() {
                const $item = $(this);
                const elderlyId = $item.find('.elderly-id-badge').text().toLowerCase();
                const elderlyBy = $item.find('.elderly-meta-content span:contains("ผู้ยื่นคำขอ")').parent().find('span:last').text().toLowerCase();
                const elderlyPhone = $item.find('.elderly-meta-content span:contains("เบอร์โทรศัพท์")').parent().find('span:last').text().toLowerCase();
                
                if (elderlyId.includes(searchTerm) || elderlyBy.includes(searchTerm) || elderlyPhone.includes(searchTerm)) {
                    $item.show();
                    visibleCount++;
                } else {
                    $item.hide();
                }
            });
            
            $('#elderly-count').text(visibleCount + ' รายการ');
            
            if (visibleCount === 0) {
                $('#elderly-no-results').show();
            } else {
                $('#elderly-no-results').hide();
            }
        });
        
        // Focus effect
        $('#elderly-search').focus(function() {
            $(this).css({
                'border-color': 'var(--elderly-primary-blue)',
                'box-shadow': '0 0 0 3px rgba(102, 126, 234, 0.1)'
            });
        }).blur(function() {
            $(this).css({
                'border-color': 'var(--elderly-border-light)',
                'box-shadow': 'none'
            });
        });
    }
    
    // *** Auto refresh functionality ***
    function addAutoRefresh() {
        let refreshInterval;
        
        const refreshBtn = $('<button>', {
            class: 'elderly-action-btn elderly-secondary',
            html: '<i class="fas fa-sync-alt me-2"></i>รีเฟรช',
            style: 'margin-left: 1rem;'
        });
        
        $('.elderly-list-controls').append(refreshBtn);
        
        refreshBtn.click(function() {
            const $icon = $(this).find('i');
            $icon.addClass('fa-spin');
            
            setTimeout(() => {
                location.reload();
            }, 1000);
        });
        
        // Auto refresh every 5 minutes
        refreshInterval = setInterval(() => {
            console.log('🔄 Auto refreshing...');
            location.reload();
        }, 300000); // 5 minutes
        
        // Clear interval when page is about to unload
        $(window).on('beforeunload', function() {
            clearInterval(refreshInterval);
        });
    }
    
    // *** เริ่มต้นด้วยการแสดงทั้งหมด ***
    filterElderlyByStatus('all');
    
    // *** เริ่มต้น animations และ features ***
    animateStatsCards();
    animateActionButtons();
    addHoverEffects();
    addScrollToTop();
    addProgressIndicator();
    addSearchFunctionality();
    addAutoRefresh();
    
    console.log('✅ ระบบเบี้ยยังชีพของฉันพร้อมใช้งาน');
    
    // *** Debug function - เพิ่มฟังก์ชันตรวจสอบ ***
    window.debugElderlyStats = function() {
        console.log('🐛 DEBUG - สถิติเบี้ยยังชีพ');
        console.log('📊 สถิติเดิม:', originalStatusCounts);
        console.log('🎯 ตัวกรองปัจจุบัน:', currentElderlyFilter);
        console.log('📋 จำนวนรายการทั้งหมด:', $('.elderly-item').length);
        console.log('👀 รายการที่แสดง:', $('.elderly-item:visible').length);
        console.log('🔢 การ์ดสถิติ:', {
            total: $('.elderly-stat-card.elderly-total .elderly-stat-content h3').text(),
            submitted: $('.elderly-stat-card.elderly-submitted .elderly-stat-content h3').text(),
            reviewing: $('.elderly-stat-card.elderly-reviewing .elderly-stat-content h3').text(),
            approved: $('.elderly-stat-card.elderly-approved .elderly-stat-content h3').text(),
            completed: $('.elderly-stat-card.elderly-completed .elderly-stat-content h3').text()
        });
        
        // *** Debug data-status attributes ***
        console.log('🏷️ Data-Status รายการทั้งหมด:');
        $('.elderly-item').each(function(index) {
            const $item = $(this);
            const itemId = $item.find('.elderly-id-badge').text().trim();
            const itemStatus = $item.attr('data-status');
            const itemOriginalStatus = $item.attr('data-original-status');
            console.log(`   ${index + 1}. ID: ${itemId}, data-status: "${itemStatus}", original-status: "${itemOriginalStatus}"`);
        });
        
        // *** นับจำนวนจริงตาม data-status ***
        const actualCounts = {
            all: $('.elderly-item').length,
            submitted: $('.elderly-item[data-status="submitted"]').length,
            reviewing: $('.elderly-item[data-status="reviewing"]').length,
            approved: $('.elderly-item[data-status="approved"]').length,
            rejected: $('.elderly-item[data-status="rejected"]').length,
            completed: $('.elderly-item[data-status="completed"]').length
        };
        console.log('📊 จำนวนจริงตาม data-status:', actualCounts);
    };
    
    // *** Keyboard shortcuts ***
    $(document).keydown(function(e) {
        // Ctrl/Cmd + F สำหรับค้นหา
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 70) {
            e.preventDefault();
            $('#elderly-search').focus();
        }
        
        // Escape เพื่อล้างการค้นหา
        if (e.keyCode === 27) {
            $('#elderly-search').val('').trigger('input');
            filterElderlyByStatus('all');
        }
        
        // เลขคีย์บอร์ด 1-5 สำหรับกรองสถานะ
        if (e.keyCode >= 49 && e.keyCode <= 53) {
            const filters = ['all', 'submitted', 'reviewing', 'approved', 'completed'];
            const filterIndex = e.keyCode - 49;
            if (filters[filterIndex]) {
                filterElderlyByStatus(filters[filterIndex]);
            }
        }
    });
    
    // *** Performance monitoring ***
    if (performance.timing) {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        console.log('⚡ หน้าโหลดใน:', loadTime + 'ms');
        
        if (loadTime > 3000) {
            console.warn('⚠️ หน้าโหลดช้า - พิจารณาปรับปรุงประสิทธิภาพ');
        }
    }
});

// *** CSS Animation สำหรับ Alert ***
const alertAnimationCSS = `
@keyframes elderlyAlertShow {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes elderlyAlertHide {
    from {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
}

/* Search box styles */
.elderly-search-box {
    padding: 0 2.5rem;
    margin: 1rem 0;
}

/* Loading states */
.fa-spin {
    animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Smooth transitions */
.elderly-item {
    transition: all 0.3s ease;
}

.elderly-item:hover {
    transform: translateY(-5px);
}

/* Enhanced status badges */
.elderly-status-badge {
    transition: all 0.3s ease;
}

.elderly-status-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

/* File Upload Styles */
.file-upload-area:hover {
    border-color: var(--elderly-primary-blue) !important;
    background: var(--elderly-light-blue) !important;
    cursor: pointer;
}

.selected-file-item {
    animation: fileSlideIn 0.3s ease;
}

@keyframes fileSlideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.existing-file-item {
    transition: all 0.3s ease;
}

.existing-file-item:hover {
    background: var(--elderly-primary-blue) !important;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.existing-file-item:hover small {
    color: rgba(255, 255, 255, 0.8) !important;
}

.existing-file-item:hover .btn-outline-danger {
    background: rgba(220, 53, 69, 0.2);
    border-color: rgba(220, 53, 69, 0.5);
    color: #dc3545;
}

/* Modal Enhancements */
.modal-content {
    overflow: hidden;
}

.modal-header {
    position: relative;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--elderly-gradient-primary);
    opacity: 0.9;
    z-index: -1;
}

.form-control:focus {
    border-color: var(--elderly-primary-blue);
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

/* Loading Animation */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal.show .modal-dialog {
    animation: modalSlideIn 0.3s ease;
}
`;

// เพิ่ม CSS Animation เข้าไปใน head
const styleSheet = document.createElement('style');
styleSheet.textContent = alertAnimationCSS;
document.head.appendChild(styleSheet);
</script>