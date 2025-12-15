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
    --kid-primary-orange: #ff8c42;
    --kid-secondary-orange: #ff6b1a;
    --kid-light-orange: #fff4f0;
    --kid-very-light-orange: #fffbf9;
    --kid-success-color: #28a745;
    --kid-warning-color: #ffc107;
    --kid-danger-color: #dc3545;
    --kid-info-color: #17a2b8;
    --kid-purple-color: #6f42c1;
    --kid-text-dark: #2c3e50;
    --kid-text-muted: #6c757d;
    --kid-border-light: rgba(255, 140, 66, 0.1);
    --kid-shadow-light: 0 4px 20px rgba(255, 140, 66, 0.1);
    --kid-shadow-medium: 0 8px 30px rgba(255, 140, 66, 0.15);
    --kid-shadow-strong: 0 15px 40px rgba(255, 140, 66, 0.2);
    --kid-gradient-primary: linear-gradient(135deg, #ff8c42 0%, #ff6b1a 100%);
    --kid-gradient-light: linear-gradient(135deg, #fffbf9 0%, #fff4f0 100%);
    --kid-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.kid-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(255, 140, 66, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(255, 107, 26, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(255, 140, 66, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.kid-container-pages {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.kid-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.kid-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--kid-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.kid-header-decoration::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: kidShimmer 2s infinite;
}

.kid-page-title {
    font-size: 3rem;
    font-weight: 600;
    background: var(--kid-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.kid-page-subtitle {
    font-size: 1.2rem;
    color: var(--kid-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.kid-modern-card {
    background: var(--kid-gradient-card);
    border-radius: 24px;
    box-shadow: var(--kid-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 140, 66, 0.08);
    z-index: 50;
}

.kid-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--kid-gradient-primary);
    z-index: 1;
}

/* User Info Card */
.kid-user-info-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.kid-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 140, 66, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.kid-user-info-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.kid-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--kid-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(255, 140, 66, 0.3);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.kid-user-avatar:hover {
    box-shadow: 0 12px 35px rgba(255, 140, 66, 0.4);
    transform: translateY(-2px);
}

.kid-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: kidAvatarShine 3s infinite;
    z-index: 1;
}

.kid-profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease;
    animation: profileLoad 0.6s ease-out;
}

.kid-profile-image:hover {
    transform: scale(1.05);
}

.kid-profile-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--kid-gradient-primary);
    border-radius: 50%;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    animation: profileLoad 0.6s ease-out;
}

.kid-profile-initials {
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 2;
    position: relative;
}

.kid-profile-fallback:hover .kid-profile-initials {
    transform: scale(1.1);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.kid-user-details {
    flex: 1;
}

.kid-user-name {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.kid-user-email {
    color: var(--kid-text-muted);
    margin-bottom: 0;
    font-size: 1rem;
}

.kid-user-status {
    margin-left: auto;
}

.kid-status-active {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
    color: var(--kid-success-color);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid rgba(40, 167, 69, 0.2);
    display: inline-flex;
    align-items: center;
}

/* Stats Dashboard */
.kid-stats-dashboard {
    padding: 2.5rem;
}

.kid-dashboard-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.kid-dashboard-header h4 {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.kid-dashboard-subtitle {
    color: var(--kid-text-muted);
    font-size: 1rem;
}

.kid-stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.5rem;
}

.kid-stat-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 140, 66, 0.1);
    cursor: pointer;
    user-select: none;
}

.kid-stat-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--kid-shadow-strong);
}

.kid-stat-card.kid-active {
    border: 2px solid var(--kid-primary-orange);
    background: rgba(255, 140, 66, 0.05);
}

.kid-stat-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 1;
}

.kid-stat-card.kid-total .kid-stat-background { background: var(--kid-gradient-primary); }
.kid-stat-card.kid-submitted .kid-stat-background { background: linear-gradient(135deg, #ffc107, #e0a800); }
.kid-stat-card.kid-reviewing .kid-stat-background { background: linear-gradient(135deg, #17a2b8, #138496); }
.kid-stat-card.kid-approved .kid-stat-background { background: linear-gradient(135deg, #28a745, #1e7e34); }
.kid-stat-card.kid-completed .kid-stat-background { background: linear-gradient(135deg, #6f42c1, #5a32a3); }

.kid-stat-icon {
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

.kid-total .kid-stat-icon {
    background: linear-gradient(135deg, rgba(255, 140, 66, 0.15), rgba(255, 140, 66, 0.05));
    color: var(--kid-primary-orange);
    box-shadow: 0 8px 25px rgba(255, 140, 66, 0.2);
}

.kid-submitted .kid-stat-icon {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
    color: #e0a800;
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
}

.kid-reviewing .kid-stat-icon {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.15), rgba(23, 162, 184, 0.05));
    color: var(--kid-info-color);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.2);
}

.kid-approved .kid-stat-icon {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.15), rgba(40, 167, 69, 0.05));
    color: var(--kid-success-color);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
}

.kid-completed .kid-stat-icon {
    background: linear-gradient(135deg, rgba(111, 66, 193, 0.15), rgba(111, 66, 193, 0.05));
    color: var(--kid-purple-color);
    box-shadow: 0 8px 25px rgba(111, 66, 193, 0.2);
}

.kid-stat-content h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--kid-text-dark);
}

.kid-stat-content p {
    color: var(--kid-text-dark);
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.kid-stat-trend {
    color: var(--kid-text-muted);
    font-size: 0.9rem;
}

/* Quick Actions */
.kid-quick-actions-card {
    padding: 2rem 2.5rem;
}

.kid-actions-header {
    text-align: center;
    margin-bottom: 2rem;
}

.kid-actions-header h5 {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.kid-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.kid-action-button {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(255, 140, 66, 0.1);
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

.kid-action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 140, 66, 0.6) 0%, rgba(255, 107, 26, 0.5) 100%);
    transition: all 0.3s ease;
    z-index: 0;
}

.kid-action-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(255, 140, 66, 0.15);
    border-color: rgba(255, 140, 66, 0.5);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.kid-action-button:hover::before {
    left: 0;
}

.kid-action-button:hover .kid-action-icon,
.kid-action-button:hover .kid-action-content {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.9);
}

.kid-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--kid-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 6px 20px rgba(255, 140, 66, 0.3);
    position: relative;
    z-index: 1;
}

.kid-action-content {
    position: relative;
    z-index: 1;
}

.kid-action-content h6 {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.kid-action-content small {
    color: var(--kid-text-muted);
    font-size: 0.9rem;
}

/* Kid AW ODS List */
.kid-list-card {
    padding: 0;
}

.kid-list-header {
    padding: 2rem 2.5rem 1rem;
    border-bottom: 1px solid var(--kid-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.kid-list-header h4 {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.kid-list-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.kid-list-count {
    background: var(--kid-gradient-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.kid-filter-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 140, 66, 0.1);
    color: var(--kid-primary-orange);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(255, 140, 66, 0.2);
}

.kid-clear-filter {
    background: var(--kid-danger-color);
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

.kid-clear-filter:hover {
    transform: scale(1.1);
}

.kid-container {
    padding: 1rem 2.5rem 2.5rem;
}

.kid-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--kid-border-light);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: kidSlideUp 0.6s ease forwards;
}

.kid-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--kid-gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.kid-item:hover::before {
    transform: scaleX(1);
}

.kid-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--kid-shadow-strong);
}

.kid-item.hidden {
    display: none;
}

.kid-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.kid-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.kid-id-badge {
    background: var(--kid-gradient-light);
    color: var(--kid-primary-orange);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 1px solid rgba(255, 140, 66, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.kid-date {
    color: var(--kid-text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.kid-status-badge.kid-modern {
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

.kid-type-display {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.4rem;
    line-height: 1.3;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kid-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.kid-meta-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.kid-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--kid-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--kid-primary-orange);
    font-size: 0.9rem;
}

.kid-meta-content {
    display: flex;
    flex-direction: column;
}

.kid-meta-content small {
    color: var(--kid-text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.kid-meta-content span {
    color: var(--kid-text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}

.kid-preview {
    background: var(--kid-gradient-light);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--kid-primary-orange);
    margin-bottom: 1.5rem;
}

.kid-preview p {
    color: var(--kid-text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.kid-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.kid-action-btn {
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

.kid-action-btn.kid-primary {
    background: var(--kid-gradient-primary);
    color: white;
}

.kid-action-btn.kid-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 140, 66, 0.4);
    color: white;
    text-decoration: none;
}

.kid-action-btn.kid-secondary {
    background: rgba(255, 140, 66, 0.1);
    color: var(--kid-primary-orange);
    border: 1px solid rgba(255, 140, 66, 0.3);
}

.kid-action-btn.kid-secondary:hover {
    background: var(--kid-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 140, 66, 0.3);
}

.kid-action-btn.kid-warning {
    background: rgba(255, 193, 7, 0.1);
    color: #e0a800;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.kid-action-btn.kid-warning:hover {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
}

/* Status Colors */
.kid-status-submitted { background: var(--kid-warning-color) !important; }
.kid-status-reviewing { background: var(--kid-info-color) !important; }
.kid-status-approved { background: var(--kid-success-color) !important; }
.kid-status-rejected { background: var(--kid-danger-color) !important; }
.kid-status-completed { background: var(--kid-purple-color) !important; }

/* No Results */
.kid-no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--kid-text-muted);
}

.kid-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--kid-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--kid-primary-orange);
}

.kid-no-results h5 {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.kid-no-results p {
    margin-bottom: 2rem;
}

/* Empty State */
.kid-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    position: relative;
}

.kid-empty-illustration {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.kid-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--kid-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: var(--kid-primary-orange);
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(255, 140, 66, 0.2);
}

.kid-icon-decoration {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 35px;
    height: 35px;
    background: var(--kid-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.4);
    border: 3px solid white;
}

.kid-empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.kid-circle {
    position: absolute;
    border: 2px solid rgba(255, 140, 66, 0.1);
    border-radius: 50%;
    animation: kidPulse 2s infinite;
}

.kid-circle-1 {
    width: 160px;
    height: 160px;
    top: -80px;
    left: -80px;
    animation-delay: 0s;
}

.kid-circle-2 {
    width: 200px;
    height: 200px;
    top: -100px;
    left: -100px;
    animation-delay: 0.5s;
}

.kid-circle-3 {
    width: 240px;
    height: 240px;
    top: -120px;
    left: -120px;
    animation-delay: 1s;
}

.kid-empty-content h5 {
    color: var(--kid-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.kid-empty-content p {
    color: var(--kid-text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Animations */
@keyframes kidShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes kidAvatarShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes kidSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes kidPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 0.1;
    }
}

@keyframes kidFadeIn {
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
    .kid-page-title {
        font-size: 2.2rem;
    }
    
    .kid-page-subtitle {
        font-size: 1rem;
    }
    
    .kid-user-info-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .kid-user-status {
        margin-left: 0;
    }
    
    .kid-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .kid-stat-card {
        padding: 1.2rem 0.8rem;
    }
    
    .kid-stat-content h3 {
        font-size: 1.8rem;
    }
    
    .kid-stat-content p {
        font-size: 0.9rem;
    }
    
    .kid-stat-trend small {
        font-size: 0.7rem;
    }
    
    .kid-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .kid-list-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .kid-list-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .kid-container {
        padding: 1rem;
    }
    
    .kid-item {
        padding: 1.5rem;
    }
    
    .kid-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .kid-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .kid-actions {
        flex-direction: column;
    }
    
    .kid-action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .kid-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .kid-user-avatar {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .kid-type-display {
        font-size: 1.2rem;
    }
}

@media print {
    .kid-action-btn,
    .kid-quick-actions-card {
        display: none !important;
    }
    
    .kid-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .kid-stat-card {
        cursor: default;
    }
}
</style>

<div class="kid-bg-pages">
    <div class="kid-container-pages">
        
        <!-- Page Header -->
        <div class="kid-page-header">
            <div class="kid-header-decoration"></div>
            <h1 class="kid-page-title">
                <i class="fas fa-baby me-3"></i>
                เงินสนับสนุนเด็กแรกเกิดของฉัน
            </h1>
            <p class="kid-page-subtitle">ติดตามสถานะและจัดการการยื่นขอรับเงินสนับสนุนเด็กแรกเกิด</p>
        </div>

        <!-- User Info Card -->
        <div class="kid-modern-card kid-user-info-card">
            <div class="kid-card-gradient-bg"></div>
            <div class="kid-user-info-content">
                <div class="kid-user-avatar">
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
                             class="kid-profile-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback เมื่อโหลดรูปไม่ได้ -->
                        <div class="kid-profile-fallback" style="display: none;">
                            <span class="kid-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php else: ?>
                        <!-- แสดง initials เมื่อไม่มีรูปโปรไฟล์ -->
                        <div class="kid-profile-fallback">
                            <span class="kid-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="kid-user-details">
                    <h4 class="kid-user-name">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php 
                        $full_name = trim($mp_prefix . ' ' . $mp_fname . ' ' . $mp_lname);
                        echo htmlspecialchars($full_name ?: 'ผู้ใช้'); 
                        ?>
                    </h4>
                    <p class="kid-user-email">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($user_info['email'] ?? $user_info['mp_email'] ?? ''); ?>
                    </p>
                </div>
                <div class="kid-user-status">
                    <span class="kid-status-active">
                        <i class="fas fa-check-circle me-1"></i>
                        สมาชิกที่ยืนยันแล้ว
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="kid-modern-card kid-stats-dashboard">
            <div class="kid-dashboard-header">
                <h4><i class="fas fa-chart-pie me-2"></i>สถิติการยื่นขอเงินสนับสนุนเด็ก</h4>
                <span class="kid-dashboard-subtitle">คลิกเพื่อกรองรายการตามสถานะ</span>
            </div>
            
            <div class="kid-stats-grid">
                <div class="kid-stat-card kid-total kid-active" data-filter="all">
                    <div class="kid-stat-background"></div>
                    <div class="kid-stat-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="kid-stat-content">
                        <h3><?php echo $status_counts['total'] ?? 0; ?></h3>
                        <p>ทั้งหมด</p>
                        <div class="kid-stat-trend">
                            <small>รายการทั้งหมด</small>
                        </div>
                    </div>
                </div>
                
                <div class="kid-stat-card kid-submitted" data-filter="submitted">
                    <div class="kid-stat-background"></div>
                    <div class="kid-stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="kid-stat-content">
                        <h3><?php echo $status_counts['submitted'] ?? 0; ?></h3>
                        <p>ยื่นเรื่องแล้ว</p>
                        <div class="kid-stat-trend">
                            <small>รอตรวจสอบ</small>
                        </div>
                    </div>
                </div>
                
                <div class="kid-stat-card kid-reviewing" data-filter="reviewing">
                    <div class="kid-stat-background"></div>
                    <div class="kid-stat-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="kid-stat-content">
                        <h3><?php echo $status_counts['reviewing'] ?? 0; ?></h3>
                        <p>กำลังพิจารณา</p>
                        <div class="kid-stat-trend">
                            <small>อยู่ระหว่างตรวจสอบ</small>
                        </div>
                    </div>
                </div>
                
                <div class="kid-stat-card kid-approved" data-filter="approved">
                    <div class="kid-stat-background"></div>
                    <div class="kid-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kid-stat-content">
                        <h3><?php echo $status_counts['approved'] ?? 0; ?></h3>
                        <p>อนุมัติแล้ว</p>
                        <div class="kid-stat-trend">
                            <small>ผ่านการอนุมัติ</small>
                        </div>
                    </div>
                </div>
                
                <div class="kid-stat-card kid-completed" data-filter="completed">
                    <div class="kid-stat-background"></div>
                    <div class="kid-stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="kid-stat-content">
                        <h3><?php echo $status_counts['completed'] ?? 0; ?></h3>
                        <p>เสร็จสิ้น</p>
                        <div class="kid-stat-trend">
                            <small>ดำเนินการครบถ้วน</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="kid-modern-card kid-quick-actions-card">
            <div class="kid-actions-header">
                <h5><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
            </div>
            <div class="kid-actions-grid">
                <a href="<?php echo site_url('Kid_aw_ods/adding_kid_aw_ods'); ?>" class="kid-action-button">
                    <div class="kid-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="kid-action-content">
                        <h6>ยื่นขอเงินสนับสนุนใหม่</h6>
                        <small>สร้างคำขอใหม่สำหรับเงินสนับสนุนเด็กแรกเกิด</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- Kid AW ODS List -->
        <div class="kid-modern-card kid-list-card">
            <div class="kid-list-header">
                <h4><i class="fas fa-baby me-2"></i>รายการเงินสนับสนุนเด็กของฉัน</h4>
                <div class="kid-list-controls">
                    <span class="kid-list-count" id="kid-count"><?php echo count($kid_aw_ods ?? []); ?> รายการ</span>
                    <div class="kid-filter-indicator" id="kid-filter-indicator">
                        <i class="fas fa-filter me-1"></i>
                        <span id="kid-filter-text">ทั้งหมด</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($kid_aw_ods)): ?>
                <div class="kid-container">
                    <?php foreach ($kid_aw_ods as $index => $kid): 
                        // ข้อมูลที่ประมวลผลแล้ว
                        $latest_status = $kid['status_display'] ?? 'ยื่นเรื่องแล้ว';
                        $status_class = $kid['status_class'] ?? 'kid-status-submitted';
                        $status_icon = $kid['status_icon'] ?? 'fas fa-file-alt';
                        $status_color = $kid['status_color'] ?? '#ffc107';
                        
                        // Format date เป็นรูปแบบไทย
                        $formatted_date = convertToThaiDate($kid['kid_aw_ods_datesave'] ?? '');
                        
                        // Latest update เป็นรูปแบบไทย
                        $latest_update = '';
                        if (!empty($kid['kid_aw_ods_updated_at'])) {
                            $latest_update = convertToThaiDate($kid['kid_aw_ods_updated_at']);
                        }
                        
                        // Animation delay
                        $animation_delay = $index * 100;
                        
                        // Status mapping for filter
                        $filter_status = 'all';
                        $actual_status = $kid['kid_aw_ods_status'] ?? 'submitted';
                        
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
                        switch($kid['kid_aw_ods_type'] ?? 'children') {
                            case 'children':
                                $type_display = 'เด็กทั่วไป';
                                $type_icon = 'fas fa-baby';
                                break;
                            case 'disabled':
                                $type_display = 'เด็กพิการ';
                                $type_icon = 'fas fa-wheelchair';
                                break;
                            default:
                                $type_display = 'เด็กทั่วไป';
                                $type_icon = 'fas fa-baby';
                                break;
                        }
                        
                        // *** แก้ไข: ตรวจสอบสิทธิ์การแก้ไข ***
                        $item_can_edit = false;
                        
                        // วิธีที่ 1: ตรวจสอบจากข้อมูลในแต่ละรายการ
                        if (isset($kid['can_edit'])) {
                            $item_can_edit = $kid['can_edit'];
                        } 
                        // วิธีที่ 2: ตรวจสอบจากตัวแปรทั่วไปและสถานะ
                        elseif (isset($can_edit) && $can_edit) {
                            $editable_statuses = ['submitted', 'reviewing'];
                            $item_can_edit = in_array($actual_status, $editable_statuses);
                        }
                        // วิธีที่ 3: ตรวจสอบจากสิทธิ์ user และสถานะ
                        elseif (isset($user_permissions['can_edit']) && $user_permissions['can_edit']) {
                            $editable_statuses = ['submitted', 'reviewing'];
                            $item_can_edit = in_array($actual_status, $editable_statuses);
                        }
                        // วิธีที่ 4: Default - ตรวจสอบสถานะโดยตรง
                        else {
                            $editable_statuses = ['submitted', 'reviewing'];
                            $item_can_edit = in_array($actual_status, $editable_statuses);
                        }
                        
                        $edit_reason = $item_can_edit ? '' : 'ไม่สามารถแก้ไขได้ในสถานะ' . $latest_status;
                    ?>
                        <div class="kid-item" 
                             style="animation-delay: <?php echo $animation_delay; ?>ms;"
                             data-status="<?php echo $filter_status; ?>"
                             data-original-status="<?php echo htmlspecialchars($latest_status); ?>">
                            <div class="kid-header">
                                <div class="kid-id-section">
                                    <div class="kid-id-badge">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? ''); ?>
                                    </div>
                                    <div class="kid-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        ยื่นวันที่: <?php echo $formatted_date; ?>
                                    </div>
                                </div>
                                <div class="kid-status-section">
                                    <span class="kid-status-badge kid-modern <?php echo $status_class; ?>" style="background: <?php echo $status_color; ?>;">
                                        <i class="<?php echo $status_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($latest_status); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="kid-body">
                                <h5 class="kid-type-display">
                                    <i class="<?php echo $type_icon; ?>"></i>
                                    <?php echo htmlspecialchars($type_display); ?>
                                </h5>
                                
                                <div class="kid-meta">
                                    <div class="kid-meta-item">
                                        <div class="kid-meta-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="kid-meta-content">
                                            <small>ผู้ยื่นคำขอ</small>
                                            <span><?php echo htmlspecialchars($kid['kid_aw_ods_by'] ?? ''); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="kid-meta-item">
                                        <div class="kid-meta-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="kid-meta-content">
                                            <small>เบอร์โทรศัพท์</small>
                                            <span><?php echo htmlspecialchars($kid['kid_aw_ods_phone'] ?? ''); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($latest_update): ?>
                                    <div class="kid-meta-item">
                                        <div class="kid-meta-icon">
                                            <i class="fas fa-sync-alt"></i>
                                        </div>
                                        <div class="kid-meta-content">
                                            <small>อัปเดตล่าสุด</small>
                                            <span><?php echo $latest_update; ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($kid['kid_aw_ods_address'])): ?>
                                <div class="kid-preview">
                                    <p>
                                        <strong>ที่อยู่:</strong> 
                                        <?php echo nl2br(htmlspecialchars($kid['kid_aw_ods_address'])); ?>
                                        <?php 
                                        // แสดงข้อมูลที่อยู่เพิ่มเติม
                                        $address_parts = [];
                                        if (!empty($kid['guest_district'])) $address_parts[] = 'ตำบล' . $kid['guest_district'];
                                        if (!empty($kid['guest_amphoe'])) $address_parts[] = 'อำเภอ' . $kid['guest_amphoe'];
                                        if (!empty($kid['guest_province'])) $address_parts[] = 'จังหวัด' . $kid['guest_province'];
                                        if (!empty($kid['guest_zipcode'])) $address_parts[] = $kid['guest_zipcode'];
                                        
                                        if (!empty($address_parts)) {
                                            echo '<br>' . implode(' ', $address_parts);
                                        }
                                        ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="kid-actions">
                                <a href="<?php echo site_url('Kid_aw_ods/my_kid_aw_ods_detail/' . ($kid['kid_aw_ods_id'] ?? '')); ?>" 
                                   class="kid-action-btn kid-primary">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                                
                                <?php if ($item_can_edit): ?>
                                    <button onclick="openEditModal('<?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? '', ENT_QUOTES); ?>')" 
                                            class="kid-action-btn kid-warning"
                                            data-kid-id="<?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? '', ENT_QUOTES); ?>"
                                            data-kid-status="<?php echo htmlspecialchars($actual_status, ENT_QUOTES); ?>"
                                            title="แก้ไขข้อมูลและเพิ่มเอกสาร">
                                        <i class="fas fa-edit me-2"></i>แก้ไข/เพิ่มเอกสาร
                                    </button>
                                <?php else: ?>
                                    <button class="kid-action-btn kid-secondary" 
                                            disabled 
                                            title="<?php echo htmlspecialchars($edit_reason, ENT_QUOTES); ?>"
                                            style="opacity: 0.6; cursor: not-allowed;">
                                        <i class="fas fa-lock me-2"></i>ไม่สามารถแก้ไข
                                    </button>
                                <?php endif; ?>
                                
                                <button onclick="copyKidId('<?php echo htmlspecialchars($kid['kid_aw_ods_id'] ?? '', ENT_QUOTES); ?>')" 
                                        class="kid-action-btn kid-secondary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                                </button>
                                <a href="<?php echo site_url('Kid_aw_ods/follow_kid_aw_ods?ref=' . urlencode($kid['kid_aw_ods_id'] ?? '')); ?>" 
                                   class="kid-action-btn kid-secondary">
                                    <i class="fas fa-search me-2"></i>ติดตามสถานะ
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Results Message -->
                <div class="kid-no-results" id="kid-no-results" style="display: none;">
                    <div class="kid-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>ไม่พบรายการที่ตรงกับเงื่อนไข</h5>
                    <p>ลองเปลี่ยนตัวกรองหรือดูรายการทั้งหมด</p>
                    <button onclick="filterKidByStatus('all')" class="kid-action-btn kid-primary">
                        <i class="fas fa-list me-2"></i>ดูทั้งหมด
                    </button>
                </div>
                
            <?php else: ?>
                <!-- Empty State -->
                <div class="kid-empty-state">
                    <div class="kid-empty-illustration">
                        <div class="kid-empty-icon">
                            <i class="fas fa-baby"></i>
                            <div class="kid-icon-decoration">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="kid-empty-circles">
                            <div class="kid-circle kid-circle-1"></div>
                            <div class="kid-circle kid-circle-2"></div>
                            <div class="kid-circle kid-circle-3"></div>
                        </div>
                    </div>
                    <div class="kid-empty-content">
                        <h5>ยังไม่มีการยื่นขอเงินสนับสนุนเด็ก</h5>
                        <p>คุณยังไม่เคยยื่นขอรับเงินสนับสนุนเด็กแรกเกิดในระบบ<br>เริ่มต้นโดยการยื่นขอเงินสนับสนูนรายการแรกของคุณ</p>
                        <a href="<?php echo site_url('Kid_aw_ods/adding_kid_aw_ods'); ?>" class="kid-action-btn kid-primary kid-large">
                            <i class="fas fa-plus me-2"></i>ยื่นขอเงินสนับสนุนเด็ก
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editKidModal" tabindex="-1" aria-labelledby="editKidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: var(--kid-shadow-strong);">
            <div class="modal-header" style="background: var(--kid-gradient-primary); color: white; border-radius: 20px 20px 0 0; border: none;">
                <h5 class="modal-title" id="editKidModalLabel">
                    <i class="fas fa-edit me-2"></i>แก้ไขข้อมูลและเพิ่มเอกสาร
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editKidForm" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 2rem;">
                    <input type="hidden" id="edit_kid_id" name="kid_id">
                    
                    <!-- แสดงข้อมูลปัจจุบัน -->
                    <div class="alert alert-info" style="border-radius: 12px; border: none; background: var(--kid-light-orange);">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>ข้อมูลปัจจุบัน</h6>
                        <div id="current_kid_info">
                            <!-- ข้อมูลจะถูกโหลดด้วย JavaScript -->
                        </div>
                    </div>
                    
                    <!-- ฟอร์มแก้ไขข้อมูล -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_kid_phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>เบอร์โทรศัพท์
                            </label>
                            <input type="tel" class="form-control" id="edit_kid_phone" name="kid_phone" 
                                   style="border-radius: 12px; border: 2px solid var(--kid-border-light); padding: 0.75rem;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_kid_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>อีเมล (ไม่บังคับ)
                            </label>
                            <input type="email" class="form-control" id="edit_kid_email" name="kid_email" 
                                   style="border-radius: 12px; border: 2px solid var(--kid-border-light); padding: 0.75rem;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_kid_address" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i>ที่อยู่
                        </label>
                        <textarea class="form-control" id="edit_kid_address" name="kid_address" rows="3"
                                  style="border-radius: 12px; border: 2px solid var(--kid-border-light); padding: 0.75rem;"></textarea>
                    </div>
                    
                    <!-- เพิ่มเอกสาร -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-paperclip me-1"></i>เพิ่มเอกสารประกอบ (รูปภาพ หรือ PDF)
                        </label>
                        <div class="file-upload-area" style="border: 2px dashed var(--kid-border-light); border-radius: 12px; padding: 2rem; text-align: center; background: var(--kid-very-light-orange); transition: all 0.3s ease;">
                            <div class="file-upload-icon" style="font-size: 3rem; color: var(--kid-primary-orange); margin-bottom: 1rem;">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text" style="color: var(--kid-text-muted); margin-bottom: 1rem;">
                                <strong>คลิกเพื่อเลือกไฟล์</strong> หรือลากไฟล์มาวางที่นี่<br>
                                <small>รองรับไฟล์: JPG, PNG, GIF, PDF (ขนาดไม่เกิน 5MB ต่อไฟล์)</small>
                            </div>
                            <input type="file" id="kid_additional_files" name="kid_additional_files[]" 
                                   multiple accept=".jpg,.jpeg,.png,.gif,.pdf" style="display: none;">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('kid_additional_files').click();" 
                                    style="border-radius: 12px; border: 2px solid var(--kid-primary-orange); color: var(--kid-primary-orange); padding: 0.75rem 1.5rem;">
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
                    <button type="submit" class="btn btn-primary" id="save_kid_btn" 
                            style="background: var(--kid-gradient-primary); border: none; border-radius: 12px; padding: 0.75rem 1.5rem;">
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
    console.log('🚀 เริ่มต้นระบบเงินสนับสนุนเด็กของฉัน');
    
    // *** ตัวแปรสำคัญ ***
    let currentKidFilter = 'all';
    let originalStatusCounts = null;
    
    // *** บันทึกข้อมูลสถิติเดิม ***
    if (!originalStatusCounts) {
        originalStatusCounts = {
            total: parseInt($('.kid-stat-card.kid-total .kid-stat-content h3').text()) || 0,
            submitted: parseInt($('.kid-stat-card.kid-submitted .kid-stat-content h3').text()) || 0,
            reviewing: parseInt($('.kid-stat-card.kid-reviewing .kid-stat-content h3').text()) || 0,
            approved: parseInt($('.kid-stat-card.kid-approved .kid-stat-content h3').text()) || 0,
            completed: parseInt($('.kid-stat-card.kid-completed .kid-stat-content h3').text()) || 0
        };
        console.log('📊 สถิติเดิม:', originalStatusCounts);
    }
    
    // *** ฟังก์ชันกรองเงินสนับสนุนเด็ก ***
    window.filterKidByStatus = function(filter) {
        console.log('🔍 กรองสถิติ:', filter);
        
        currentKidFilter = filter;
        
        // *** เอาการอัปเดต active state ของ stat cards ***
        $('.kid-stat-card').removeClass('kid-active');
        $('.kid-stat-card[data-filter="' + filter + '"]').addClass('kid-active');
        
        // *** กรองรายการเงินสนับสนุนเด็ก ***
        const kidItems = $('.kid-item');
        const noResults = $('#kid-no-results');
        let visibleCount = 0;
        
        // *** ซ่อน/แสดงรายการ ***
        kidItems.each(function() {
            const $item = $(this);
            const itemStatus = $item.attr('data-status');
            
            if (filter === 'all' || itemStatus === filter) {
                $item.show().removeClass('hidden');
                visibleCount++;
                
                // Re-trigger animation
                $item.css('animation', 'none');
                setTimeout(() => {
                    $item.css('animation', 'kidFadeIn 0.5s ease forwards');
                }, 10);
            } else {
                $item.hide().addClass('hidden');
            }
        });
        
        // *** อัปเดตจำนวนที่แสดง ***
        const countElement = $('#kid-count');
        if (countElement.length) {
            countElement.text(visibleCount + ' รายการ');
        }
        
        // *** อัปเดต filter indicator ***
        updateFilterIndicator(filter, visibleCount);
        
        // *** แสดง/ซ่อนข้อความไม่พบข้อมูล ***
        if (noResults.length) {
            if (visibleCount === 0 && filter !== 'all') {
                noResults.show().css('animation', 'kidFadeIn 0.5s ease forwards');
            } else {
                noResults.hide();
            }
        }
        
        // *** Smooth scroll ***
        if (filter !== 'all') {
            const kidList = $('.kid-list-card');
            if (kidList.length) {
                kidList.get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        console.log('✅ กรองเสร็จ - แสดง:', visibleCount, 'รายการ');
    };
    
    // *** ฟังก์ชันอัปเดต filter indicator ***
    function updateFilterIndicator(filter, visibleCount) {
        const filterText = $('#kid-filter-text');
        const clearFilterBtn = $('.kid-clear-filter');
        
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
    window.openEditModal = function(kidId) {
        console.log('📝 เปิด Modal แก้ไข:', kidId);
        
        // ตรวจสอบ kidId
        if (!kidId || kidId.trim() === '') {
            showKidAlert('ไม่พบหมายเลขอ้างอิง', 'error');
            console.error('❌ kidId is empty or invalid:', kidId);
            return;
        }
        
        // ล้างข้อมูลเก่า
        $('#edit_kid_id').val('');
        $('#edit_kid_phone').val('');
        $('#edit_kid_email').val('');
        $('#edit_kid_address').val('');
        $('#selected_files_preview').hide();
        $('#kid_additional_files').val('');
        
        // เก็บ kid ID ใน hidden field
        $('#edit_kid_id').val(kidId);
        
        // แสดง loading
        showEditModalLoading();
        
        // เปิด Modal
        const modal = new bootstrap.Modal(document.getElementById('editKidModal'));
        modal.show();
        
        // โหลดข้อมูลจาก server
        loadKidDataFromServer(kidId);
    };
    
    // *** แสดง Loading ใน Modal ***
    function showEditModalLoading() {
        $('#current_kid_info').html(`
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
        $('#edit_kid_phone').val('');
        $('#edit_kid_email').val('');
        $('#edit_kid_address').val('');
    }
    
    // *** โหลดข้อมูลจาก Server ***
    function loadKidDataFromServer(kidId) {
        console.log('🔄 โหลดข้อมูลจาก Server:', kidId);
        
        $.ajax({
            url: '<?php echo site_url("Kid_aw_ods/get_kid_data"); ?>',
            type: 'POST',
            data: {
                kid_id: kidId
            },
            dataType: 'json',
            timeout: 10000, // 10 วินาที
            beforeSend: function() {
                console.log('📤 ส่ง AJAX request:', kidId);
            },
            success: function(response) {
                console.log('✅ ได้รับ response:', response);
                
                if (response.success && response.data) {
                    populateEditForm(response.data);
                } else {
                    console.error('❌ Response error:', response.message);
                    showKidAlert(response.message || 'ไม่สามารถโหลดข้อมูลได้', 'error');
                    bootstrap.Modal.getInstance(document.getElementById('editKidModal')).hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    readyState: xhr.readyState,
                    status_code: xhr.status
                });
                
                let errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
                
                if (xhr.status === 404) {
                    errorMessage = 'ไม่พบ URL ที่ระบุ';
                } else if (xhr.status === 500) {
                    errorMessage = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์';
                } else if (status === 'timeout') {
                    errorMessage = 'การเชื่อมต่อหมดเวลา';
                }
                
                showKidAlert(errorMessage, 'error');
                bootstrap.Modal.getInstance(document.getElementById('editKidModal')).hide();
            },
            complete: function() {
                console.log('🏁 AJAX request completed');
            }
        });
    }
    
    // *** เติมข้อมูลในฟอร์ม ***
    function populateEditForm(data) {
        console.log('📋 เติมข้อมูลในฟอร์ม:', data);
        
        try {
            // แสดงข้อมูลปัจจุบัน
            const statusDisplay = getStatusDisplay(data.kid_aw_ods_status || 'submitted');
            const typeDisplay = data.kid_aw_ods_type === 'disabled' ? 'เด็กพิการ' : 'เด็กทั่วไป';
            const formattedDate = formatThaiDate(data.kid_aw_ods_datesave);
            
            $('#current_kid_info').html(`
                <div class="row">
                    <div class="col-md-6">
                        <strong>หมายเลขอ้างอิง:</strong> ${data.kid_aw_ods_id || 'ไม่ระบุ'}<br>
                        <strong>ประเภท:</strong> ${typeDisplay}<br>
                        <strong>ผู้ยื่นคำขอ:</strong> ${data.kid_aw_ods_by || 'ไม่ระบุ'}
                    </div>
                    <div class="col-md-6">
                        <strong>สถานะ:</strong> <span class="badge bg-primary">${statusDisplay}</span><br>
                        <strong>วันที่ยื่น:</strong> ${formattedDate || 'ไม่ระบุ'}
                    </div>
                </div>
            `);
            
            // กรอกข้อมูลในฟอร์ม
            $('#edit_kid_phone').val(data.kid_aw_ods_phone || '');
            $('#edit_kid_email').val(data.kid_aw_ods_email || '');
            $('#edit_kid_address').val(data.kid_aw_ods_address || '');
            
            // แสดงไฟล์ที่มีอยู่
            displayExistingFiles(data.files || []);
            
            console.log('✅ เติมข้อมูลสำเร็จ');
            
        } catch (error) {
            console.error('❌ Error in populateEditForm:', error);
            showKidAlert('เกิดข้อผิดพลาดในการแสดงข้อมูล', 'error');
        }
    }
    
    // *** แสดงไฟล์ที่มีอยู่แล้ว ***
    function displayExistingFiles(files) {
        console.log('📁 แสดงไฟล์ที่มีอยู่:', files);
        
        if (!Array.isArray(files)) {
            console.warn('⚠️ Files is not an array:', files);
            files = [];
        }
        
        if (files.length > 0) {
            let filesHtml = '<div class="row">';
            files.forEach(file => {
                const icon = getFileIcon(file.file_type || '');
                const fileSize = formatFileSize(file.file_size || 0);
                const uploadDate = formatThaiDate(file.uploaded_at || '');
                const downloadUrl = file.download_url || '#';
                const fileExists = file.file_exists !== false;
                
                filesHtml += `
                    <div class="col-md-6 mb-2">
                        <div class="existing-file-item" data-file-id="${file.file_id || ''}" 
                             style="background: var(--kid-light-orange); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="${icon}" style="font-size: 1.5rem;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--kid-text-dark);">${file.original_name || 'ไม่ระบุชื่อไฟล์'}</div>
                                <small style="color: var(--kid-text-muted);">${fileSize} • ${uploadDate}</small>
                                ${!fileExists ? '<br><small class="text-danger">ไฟล์ไม่พบในระบบ</small>' : ''}
                            </div>
                            <div class="file-actions">
                                ${fileExists ? `<a href="${downloadUrl}" class="btn btn-sm btn-outline-primary me-1" 
                                   target="_blank" title="ดาวน์โหลด" style="border-radius: 6px;">
                                    <i class="fas fa-download"></i>
                                </a>` : ''}
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="removeExistingFile('${file.file_id || ''}', '${(file.original_name || '').replace(/'/g, '\\\'')}')" 
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
        const kidId = $('#edit_kid_id').val();
        
        // แสดง loading บนไฟล์ที่กำลังลบ
        const fileItem = $(`.existing-file-item[data-file-id="${fileId}"]`);
        fileItem.find('.file-actions').html('<div class="spinner-border spinner-border-sm text-danger" role="status"></div>');
        
        $.ajax({
            url: '<?php echo site_url("Kid_aw_ods/delete_kid_file"); ?>',
            type: 'POST',
            data: {
                file_id: fileId,
                kid_id: kidId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showKidAlert(`ลบไฟล์ "${fileName}" สำเร็จ`, 'success');
                    // ลบ element ออกจาก DOM
                    fileItem.fadeOut(300, function() {
                        $(this).remove();
                        // ตรวจสอบว่าเหลือไฟล์หรือไม่
                        if ($('.existing-file-item').length === 0) {
                            $('#existing_files_list').html('<p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>ยังไม่มีเอกสารแนบ</p>');
                        }
                    });
                } else {
                    showKidAlert(response.message || 'ไม่สามารถลบไฟล์ได้', 'error');
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
                showKidAlert('เกิดข้อผิดพลาดในการลบไฟล์', 'error');
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
    $('#editKidForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#save_kid_btn');
        const originalText = submitBtn.html();
        
        // Validation
        const phone = $('#edit_kid_phone').val().trim();
        if (!phone) {
            showKidAlert('กรุณากรอกเบอร์โทรศัพท์', 'warning');
            $('#edit_kid_phone').focus();
            return;
        }
        
        // แสดง Loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...');
        
        // สร้าง FormData
        const formData = new FormData(this);
        
        // ส่งข้อมูลไป Server
        $.ajax({
            url: '<?php echo site_url("Kid_aw_ods/update_kid_data"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showKidAlert(response.message || 'บันทึกการเปลี่ยนแปลงสำเร็จ', 'success');
                    
                    // ปิด Modal
                    bootstrap.Modal.getInstance(document.getElementById('editKidModal')).hide();
                    
                    // รีเฟรชหน้าหลังจาก 1.5 วินาที
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                    
                } else {
                    showKidAlert(response.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Submit error:', error);
                showKidAlert('เกิดข้อผิดพลาดในการบันทึก', 'error');
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
    $('#kid_additional_files').on('change', function() {
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
                showKidAlert(`ไฟล์ "${file.name}" ไม่ได้รับอนุญาต`, 'error');
                continue;
            }
            
            // ตรวจสอบขนาดไฟล์
            if (file.size > maxSize) {
                showKidAlert(`ไฟล์ "${file.name}" มีขนาดใหญ่เกิน 5MB`, 'error');
                continue;
            }
            
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileIcon = file.type.includes('image') ? 'fas fa-image text-primary' : 'fas fa-file-pdf text-danger';
            
            filesHtml += `
                <div class="selected-file-item mb-2" style="background: var(--kid-very-light-orange); padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="${fileIcon}" style="font-size: 1.5rem;"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--kid-text-dark);">${file.name}</div>
                        <small style="color: var(--kid-text-muted);">${fileSize} MB • ${file.type}</small>
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
        const fileInput = document.getElementById('kid_additional_files');
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
            'border-color': 'var(--kid-primary-orange)',
            'background': 'var(--kid-light-orange)'
        });
    });
    
    fileUploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--kid-border-light)',
            'background': 'var(--kid-very-light-orange)'
        });
    });
    
    fileUploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).css({
            'border-color': 'var(--kid-border-light)',
            'background': 'var(--kid-very-light-orange)'
        });
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('kid_additional_files').files = files;
            displaySelectedFiles(files);
        }
    });
    
    // *** Reset Modal เมื่อปิด ***
    $('#editKidModal').on('hidden.bs.modal', function() {
        $('#editKidForm')[0].reset();
        $('#selected_files_preview').hide();
        $('#save_kid_btn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง');
        $('#current_kid_info').html('');
        $('#existing_files_list').html('');
    });
    
    window.copyKidId = function(kidId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(kidId).then(() => {
                showKidAlert('คัดลอกหมายเลข ' + kidId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopyKidText(kidId);
            });
        } else {
            fallbackCopyKidText(kidId);
        }
    };
    
    function fallbackCopyKidText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showKidAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
        } catch (err) {
            showKidAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** Alert Functions ***
    function showKidAlert(message, type) {
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
                confirmButtonColor: '#ff8c42'
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
                animation: kidAlertShow 0.3s ease;
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
                alertDiv.style.animation = 'kidAlertHide 0.3s ease';
                setTimeout(() => alertDiv.remove(), 300);
            }, 4000);
        }
    }
    
    // *** Event Listeners สำหรับการ์ดสถิติ ***
    $('.kid-stat-card').off('click').on('click', function(e) {
        e.preventDefault();
        
        const filter = $(this).attr('data-filter');
        if (filter) {
            console.log('🎯 คลิกการ์ดสถิติ:', filter);
            filterKidByStatus(filter);
        }
    });
    
    // *** Event Listener สำหรับปุ่มล้างตัวกรอง ***
    $('.kid-clear-filter').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('🗑️ ล้างตัวกรอง');
        filterKidByStatus('all');
    });
    
    // *** เริ่มต้นด้วยการแสดงทั้งหมด ***
    filterKidByStatus('all');
    
    console.log('✅ ระบบเงินสนับสนุนเด็กของฉันพร้อมใช้งาน');
    
    // *** เพิ่มข้อมูลสำหรับ JavaScript ***
    window.userPermissions = <?php echo json_encode($user_permissions ?? []); ?>;
    window.canEditGlobal = <?php echo json_encode($can_edit ?? false); ?>;

    // *** Debug function สำหรับตรวจสอบสิทธิ์ ***
    window.debugUserPermissions = function() {
        console.log('🛡️ User Permissions:', window.userPermissions);
        console.log('✏️ Can Edit Global:', window.canEditGlobal);
        
        // ตรวจสอบปุ่มแก้ไขแต่ละรายการ
        $('.kid-action-btn.kid-warning').each(function(index) {
            const kidId = $(this).attr('data-kid-id');
            const status = $(this).attr('data-kid-status');
            const isDisabled = $(this).prop('disabled');
            console.log(`${index + 1}. Kid: ${kidId}, Status: ${status}, Can Edit: ${!isDisabled}`);
        });
    };

    // เรียกใช้ debug เมื่อโหลดเสร็จ (development only)
    <?php if (ENVIRONMENT === 'development'): ?>
    setTimeout(function() {
        window.debugUserPermissions();
    }, 1000);
    <?php endif; ?>

    // *** เปิดใช้งาน Bootstrap tooltips ***
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // *** เพิ่ม hover effect สำหรับปุ่มที่ disable ***
    $('.kid-action-btn[disabled]').hover(
        function() {
            $(this).css('transform', 'none');
        },
        function() {
            $(this).css('transform', 'none');
        }
    );
});

// *** CSS Animation สำหรับ Alert ***
const alertAnimationCSS = `
@keyframes kidAlertShow {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes kidAlertHide {
    from {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
}

/* File Upload Styles */
.file-upload-area:hover {
    border-color: var(--kid-primary-orange) !important;
    background: var(--kid-light-orange) !important;
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
    background: var(--kid-primary-orange) !important;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
}

.existing-file-item:hover small {
    color: rgba(255, 255, 255, 0.8) !important;
}

.form-control:focus {
    border-color: var(--kid-primary-orange);
    box-shadow: 0 0 0 0.25rem rgba(255, 140, 66, 0.25);
}
`;

// เพิ่ม CSS Animation เข้าไปใน head
const styleSheet2 = document.createElement('style');
styleSheet2.textContent = alertAnimationCSS;
document.head.appendChild(styleSheet2);
</script>