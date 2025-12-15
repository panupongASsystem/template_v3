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
    --esv-primary-blue: #2563eb;
    --esv-secondary-blue: #1d4ed8;
    --esv-light-blue: #eff6ff;
    --esv-very-light-blue: #f8faff;
    --esv-success-color: #059669;
    --esv-warning-color: #d97706;
    --esv-danger-color: #dc2626;
    --esv-info-color: #0284c7;
    --esv-purple-color: #7c3aed;
    --esv-text-dark: #1f2937;
    --esv-text-muted: #6b7280;
    --esv-border-light: rgba(37, 99, 235, 0.1);
    --esv-shadow-light: 0 4px 20px rgba(37, 99, 235, 0.1);
    --esv-shadow-medium: 0 8px 30px rgba(37, 99, 235, 0.15);
    --esv-shadow-strong: 0 15px 40px rgba(37, 99, 235, 0.2);
    --esv-gradient-primary: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    --esv-gradient-light: linear-gradient(135deg, #f8faff 0%, #eff6ff 100%);
    --esv-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.esv-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(37, 99, 235, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(29, 78, 216, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(37, 99, 235, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.esv-container-pages {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.esv-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.esv-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--esv-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.esv-header-decoration::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: esvShimmer 2s infinite;
}

.esv-page-title {
    font-size: 3rem;
    font-weight: 600;
    background: var(--esv-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.esv-page-subtitle {
    font-size: 1.2rem;
    color: var(--esv-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.esv-modern-card {
    background: var(--esv-gradient-card);
    border-radius: 24px;
    box-shadow: var(--esv-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(37, 99, 235, 0.08);
    z-index: 50;
}

.esv-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--esv-gradient-primary);
    z-index: 1;
}

/* User Info Card */
.esv-user-info-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.esv-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.esv-user-info-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.esv-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--esv-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.esv-user-avatar:hover {
    box-shadow: 0 12px 35px rgba(37, 99, 235, 0.4);
    transform: translateY(-2px);
}

.esv-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: esvAvatarShine 3s infinite;
    z-index: 1;
}

.esv-profile-initials {
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 2;
    position: relative;
}

.esv-user-details {
    flex: 1;
}

.esv-user-name {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.esv-user-email {
    color: var(--esv-text-muted);
    margin-bottom: 0;
    font-size: 1rem;
}

.esv-user-status {
    margin-left: auto;
}

.esv-status-active {
    background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(5, 150, 105, 0.05));
    color: var(--esv-success-color);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid rgba(5, 150, 105, 0.2);
    display: inline-flex;
    align-items: center;
}

/* Stats Dashboard */
.esv-stats-dashboard {
    padding: 2.5rem;
}

.esv-dashboard-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.esv-dashboard-header h4 {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.esv-dashboard-subtitle {
    color: var(--esv-text-muted);
    font-size: 1rem;
}

.esv-stats-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1.5rem;
}

.esv-stat-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(37, 99, 235, 0.1);
    cursor: pointer;
    user-select: none;
}

.esv-stat-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--esv-shadow-strong);
}

.esv-stat-card.esv-active {
    border: 2px solid var(--esv-primary-blue);
    background: rgba(37, 99, 235, 0.05);
}

.esv-stat-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 1;
}

.esv-stat-card.esv-total .esv-stat-background { background: var(--esv-gradient-primary); }
.esv-stat-card.esv-pending .esv-stat-background { background: linear-gradient(135deg, #d97706, #b45309); }
.esv-stat-card.esv-processing .esv-stat-background { background: linear-gradient(135deg, #0284c7, #0369a1); }
.esv-stat-card.esv-completed .esv-stat-background { background: linear-gradient(135deg, #059669, #047857); }
.esv-stat-card.esv-rejected .esv-stat-background { background: linear-gradient(135deg, #dc2626, #b91c1c); }
.esv-stat-card.esv-cancelled .esv-stat-background { background: linear-gradient(135deg, #6b7280, #4b5563); }

.esv-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.5rem;
    position: relative;
    overflow: hidden;
}

.esv-total .esv-stat-icon {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(37, 99, 235, 0.05));
    color: var(--esv-primary-blue);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.2);
}

.esv-pending .esv-stat-icon {
    background: linear-gradient(135deg, rgba(217, 119, 6, 0.15), rgba(217, 119, 6, 0.05));
    color: #b45309;
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.2);
}

.esv-processing .esv-stat-icon {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.15), rgba(2, 132, 199, 0.05));
    color: var(--esv-info-color);
    box-shadow: 0 8px 25px rgba(2, 132, 199, 0.2);
}

.esv-completed .esv-stat-icon {
    background: linear-gradient(135deg, rgba(5, 150, 105, 0.15), rgba(5, 150, 105, 0.05));
    color: var(--esv-success-color);
    box-shadow: 0 8px 25px rgba(5, 150, 105, 0.2);
}

.esv-rejected .esv-stat-icon {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.15), rgba(220, 38, 38, 0.05));
    color: var(--esv-danger-color);
    box-shadow: 0 8px 25px rgba(220, 38, 38, 0.2);
}

.esv-cancelled .esv-stat-icon {
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.15), rgba(107, 114, 128, 0.05));
    color: #6b7280;
    box-shadow: 0 8px 25px rgba(107, 114, 128, 0.2);
}

.esv-stat-content h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--esv-text-dark);
}

.esv-stat-content p {
    color: var(--esv-text-dark);
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1rem;
}

.esv-stat-trend {
    color: var(--esv-text-muted);
    font-size: 0.8rem;
}

/* Quick Actions */
.esv-quick-actions-card {
    padding: 2rem 2.5rem;
}

.esv-actions-header {
    text-align: center;
    margin-bottom: 2rem;
}

.esv-actions-header h5 {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.esv-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.esv-action-button {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(37, 99, 235, 0.1);
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

.esv-action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.6) 0%, rgba(29, 78, 216, 0.5) 100%);
    transition: all 0.3s ease;
    z-index: 0;
}

.esv-action-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
    border-color: rgba(37, 99, 235, 0.5);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.esv-action-button:hover::before {
    left: 0;
}

.esv-action-button:hover .esv-action-icon,
.esv-action-button:hover .esv-action-content {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.9);
}

.esv-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--esv-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
    position: relative;
    z-index: 1;
}

.esv-action-content {
    position: relative;
    z-index: 1;
}

.esv-action-content h6 {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.esv-action-content small {
    color: var(--esv-text-muted);
    font-size: 0.9rem;
}

/* ESV Documents List */
.esv-list-card {
    padding: 0;
}

.esv-list-header {
    padding: 2rem 2.5rem 1rem;
    border-bottom: 1px solid var(--esv-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.esv-list-header h4 {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.esv-list-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.esv-list-count {
    background: var(--esv-gradient-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.esv-filter-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(37, 99, 235, 0.1);
    color: var(--esv-primary-blue);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(37, 99, 235, 0.2);
}

.esv-clear-filter {
    background: var(--esv-danger-color);
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

.esv-clear-filter:hover {
    transform: scale(1.1);
}

.esv-container {
    padding: 1rem 2.5rem 2.5rem;
}

.esv-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--esv-border-light);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: esvSlideUp 0.6s ease forwards;
}

.esv-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--esv-gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.esv-item:hover::before {
    transform: scaleX(1);
}

.esv-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--esv-shadow-strong);
}

.esv-item.hidden {
    display: none;
}

.esv-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.esv-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.esv-id-badge {
    background: var(--esv-gradient-light);
    color: var(--esv-primary-blue);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 1px solid rgba(37, 99, 235, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.esv-date {
    color: var(--esv-text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.esv-status-badge.esv-modern {
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

.esv-topic-display {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.4rem;
    line-height: 1.3;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.esv-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.esv-meta-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.esv-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--esv-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--esv-primary-blue);
    font-size: 0.9rem;
}

.esv-meta-content {
    display: flex;
    flex-direction: column;
}

.esv-meta-content small {
    color: var(--esv-text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.esv-meta-content span {
    color: var(--esv-text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}

.esv-preview {
    background: var(--esv-gradient-light);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--esv-primary-blue);
    margin-bottom: 1.5rem;
}

.esv-preview p {
    color: var(--esv-text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.esv-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.esv-action-btn {
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

.esv-action-btn.esv-primary {
    background: var(--esv-gradient-primary);
    color: white;
}

.esv-action-btn.esv-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
    color: white;
    text-decoration: none;
}

.esv-action-btn.esv-secondary {
    background: rgba(37, 99, 235, 0.1);
    color: var(--esv-primary-blue);
    border: 1px solid rgba(37, 99, 235, 0.3);
}

.esv-action-btn.esv-secondary:hover {
    background: var(--esv-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
}

.esv-action-btn.esv-warning {
    background: rgba(217, 119, 6, 0.1);
    color: #b45309;
    border: 1px solid rgba(217, 119, 6, 0.3);
}

.esv-action-btn.esv-warning:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.3);
}

/* Status Colors */
.esv-status-pending { background: var(--esv-warning-color) !important; }
.esv-status-processing { background: var(--esv-info-color) !important; }
.esv-status-completed { background: var(--esv-success-color) !important; }
.esv-status-rejected { background: var(--esv-danger-color) !important; }
.esv-status-cancelled { background: #6b7280 !important; }

/* No Results */
.esv-no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--esv-text-muted);
}

.esv-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--esv-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--esv-primary-blue);
}

.esv-no-results h5 {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.esv-no-results p {
    margin-bottom: 2rem;
}

/* Empty State */
.esv-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    position: relative;
}

.esv-empty-illustration {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.esv-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--esv-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: var(--esv-primary-blue);
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
}

.esv-icon-decoration {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 35px;
    height: 35px;
    background: var(--esv-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
    border: 3px solid white;
}

.esv-empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.esv-circle {
    position: absolute;
    border: 2px solid rgba(37, 99, 235, 0.1);
    border-radius: 50%;
    animation: esvPulse 2s infinite;
}

.esv-circle-1 {
    width: 160px;
    height: 160px;
    top: -80px;
    left: -80px;
    animation-delay: 0s;
}

.esv-circle-2 {
    width: 200px;
    height: 200px;
    top: -100px;
    left: -100px;
    animation-delay: 0.5s;
}

.esv-circle-3 {
    width: 240px;
    height: 240px;
    top: -120px;
    left: -120px;
    animation-delay: 1s;
}

.esv-empty-content h5 {
    color: var(--esv-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.esv-empty-content p {
    color: var(--esv-text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Animations */
@keyframes esvShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes esvAvatarShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes esvSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes esvPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 0.1;
    }
}

@keyframes esvFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .esv-page-title {
        font-size: 2.2rem;
    }
    
    .esv-page-subtitle {
        font-size: 1rem;
    }
    
    .esv-user-info-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .esv-user-status {
        margin-left: 0;
    }
    
    .esv-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .esv-stat-card {
        padding: 1.2rem 0.8rem;
    }
    
    .esv-stat-content h3 {
        font-size: 1.8rem;
    }
    
    .esv-stat-content p {
        font-size: 0.9rem;
    }
    
    .esv-stat-trend small {
        font-size: 0.7rem;
    }
    
    .esv-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .esv-list-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .esv-list-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .esv-container {
        padding: 1rem;
    }
    
    .esv-item {
        padding: 1.5rem;
    }
    
    .esv-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .esv-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .esv-actions {
        flex-direction: column;
    }
    
    .esv-action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .esv-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .esv-user-avatar {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .esv-topic-display {
        font-size: 1.2rem;
    }
}

@media print {
    .esv-action-btn,
    .esv-quick-actions-card {
        display: none !important;
    }
    
    .esv-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .esv-stat-card {
        cursor: default;
    }
}
</style>

<div class="esv-bg-pages">
    <div class="esv-container-pages">
        
        <!-- Page Header -->
        <div class="esv-page-header">
            <div class="esv-header-decoration"></div>
            <h1 class="esv-page-title">
                <i class="fas fa-file-alt me-3"></i>
                ระบบยื่นเอกสารออนไลน์

            </h1>
            <p class="esv-page-subtitle">ติดตามและจัดการยื่นเอกสารออนไลน์ที่คุณได้ยื่นไว้ในระบบ</p>
        </div>

        <!-- User Info Card -->
        <div class="esv-modern-card esv-user-info-card">
            <div class="esv-card-gradient-bg"></div>
            <div class="esv-user-info-content">
                <div class="esv-user-avatar">
                    <?php 
                    // สร้างชื่อเริ่มต้น (Initial) สำหรับ avatar
                    $initials = '';
                    if (!empty($member_info['name'])) {
                        $name_parts = explode(' ', trim($member_info['name']));
                        if (count($name_parts) >= 2) {
                            $initials = mb_substr($name_parts[0], 0, 1) . mb_substr($name_parts[1], 0, 1);
                        } else {
                            $initials = mb_substr($name_parts[0], 0, 2);
                        }
                    } else {
                        $initials = 'U';
                    }
                    ?>
                    <span class="esv-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                </div>
                <div class="esv-user-details">
                    <h4 class="esv-user-name">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo htmlspecialchars($member_info['name'] ?? 'สมาชิก'); ?>
                    </h4>
                    <p class="esv-user-email">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($member_info['email'] ?? ''); ?>
                    </p>
                </div>
                <div class="esv-user-status">
                    <span class="esv-status-active">
                        <i class="fas fa-check-circle me-1"></i>
                        สมาชิกที่ยืนยันแล้ว
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="esv-modern-card esv-stats-dashboard">
            <div class="esv-dashboard-header">
                <h4><i class="fas fa-chart-pie me-2"></i>สถิติเอกสารของคุณ</h4>
                <span class="esv-dashboard-subtitle">คลิกเพื่อกรองรายการตามสถานะ</span>
            </div>
            
            <div class="esv-stats-grid">
                <div class="esv-stat-card esv-total esv-active" data-filter="all">
                    <div class="esv-stat-background"></div>
                    <div class="esv-stat-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="esv-stat-content">
                        <h3><?php echo $document_stats['total'] ?? 0; ?></h3>
                        <p>ทั้งหมด</p>
                        <div class="esv-stat-trend">
                            <small>เอกสารทั้งหมด</small>
                        </div>
                    </div>
                </div>
                
                <div class="esv-stat-card esv-pending" data-filter="pending">
                    <div class="esv-stat-background"></div>
                    <div class="esv-stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="esv-stat-content">
                        <h3><?php echo $document_stats['pending'] ?? 0; ?></h3>
                        <p>รอดำเนินการ</p>
                        <div class="esv-stat-trend">
                            <small>รอเจ้าหน้าที่ตรวจสอบ</small>
                        </div>
                    </div>
                </div>
                
                <div class="esv-stat-card esv-processing" data-filter="processing">
                    <div class="esv-stat-background"></div>
                    <div class="esv-stat-icon">
                        <i class="fas fa-cog fa-spin"></i>
                    </div>
                    <div class="esv-stat-content">
                        <h3><?php echo $document_stats['processing'] ?? 0; ?></h3>
                        <p>กำลังดำเนินการ</p>
                        <div class="esv-stat-trend">
                            <small>อยู่ระหว่างประมวลผล</small>
                        </div>
                    </div>
                </div>
                
                <div class="esv-stat-card esv-completed" data-filter="completed">
                    <div class="esv-stat-background"></div>
                    <div class="esv-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="esv-stat-content">
                        <h3><?php echo $document_stats['completed'] ?? 0; ?></h3>
                        <p>เสร็จสิ้น</p>
                        <div class="esv-stat-trend">
                            <small>ดำเนินการครบถ้วน</small>
                        </div>
                    </div>
                </div>
                
                <div class="esv-stat-card esv-rejected" data-filter="rejected">
                    <div class="esv-stat-background"></div>
                    <div class="esv-stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="esv-stat-content">
                        <h3><?php echo $document_stats['rejected'] ?? 0; ?></h3>
                        <p>ไม่อนุมัติ</p>
                        <div class="esv-stat-trend">
                            <small>ไม่ผ่านการอนุมัติ</small>
                        </div>
                    </div>
                </div>
                
                <div class="esv-stat-card esv-cancelled" data-filter="cancelled">
                    <div class="esv-stat-background"></div>
                    <div class="esv-stat-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="esv-stat-content">
                        <h3><?php echo $document_stats['cancelled'] ?? 0; ?></h3>
                        <p>ยกเลิก</p>
                        <div class="esv-stat-trend">
                            <small>เอกสารที่ยกเลิก</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="esv-modern-card esv-quick-actions-card">
            <div class="esv-actions-header">
                <h5><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
            </div>
            <div class="esv-actions-grid">
                <a href="<?php echo site_url('Esv_ods/submit_document'); ?>" class="esv-action-button">
                    <div class="esv-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="esv-action-content">
                        <h6>ยื่นเอกสารใหม่</h6>
                        <small>สร้างคำขอใหม่ในระบบเอกสารออนไลน์</small>
                    </div>
                </a>
                
                
            </div>
        </div>

        <!-- ESV Documents List -->
        <div class="esv-modern-card esv-list-card">
            <div class="esv-list-header">
                <h4><i class="fas fa-file-alt me-2"></i>รายการเอกสารของฉัน</h4>
                <div class="esv-list-controls">
                    <span class="esv-list-count" id="esv-count"><?php echo count($documents ?? []); ?> รายการ</span>
                    <div class="esv-filter-indicator" id="esv-filter-indicator">
                        <i class="fas fa-filter me-1"></i>
                        <span id="esv-filter-text">ทั้งหมด</span>
                        <button class="esv-clear-filter" id="esv-clear-filter" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <?php if (!empty($documents)): ?>
                <div class="esv-container">
                    <?php foreach ($documents as $index => $doc): 
                        // ประมวลผลข้อมูลเอกสาร
                        $status_display = '';
                        $status_class = '';
                        $status_icon = '';
                        $status_color = '';
                        
                        // กำหนดการแสดงผลตามสถานะ
                        switch($doc->esv_ods_status) {
                            case 'pending':
                                $status_display = 'รอดำเนินการ';
                                $status_class = 'esv-status-pending';
                                $status_icon = 'fas fa-clock';
                                $status_color = '#d97706';
                                break;
                            case 'processing':
                                $status_display = 'กำลังดำเนินการ';
                                $status_class = 'esv-status-processing';
                                $status_icon = 'fas fa-cog fa-spin';
                                $status_color = '#0284c7';
                                break;
                            case 'completed':
                                $status_display = 'เสร็จสิ้น';
                                $status_class = 'esv-status-completed';
                                $status_icon = 'fas fa-check-circle';
                                $status_color = '#059669';
                                break;
                            case 'rejected':
                                $status_display = 'ไม่อนุมัติ';
                                $status_class = 'esv-status-rejected';
                                $status_icon = 'fas fa-times-circle';
                                $status_color = '#dc2626';
                                break;
                            case 'cancelled':
                                $status_display = 'ยกเลิก';
                                $status_class = 'esv-status-cancelled';
                                $status_icon = 'fas fa-ban';
                                $status_color = '#6b7280';
                                break;
                            default:
                                $status_display = 'รอดำเนินการ';
                                $status_class = 'esv-status-pending';
                                $status_icon = 'fas fa-clock';
                                $status_color = '#d97706';
                                break;
                        }
                        
                        // Format date เป็นรูปแบบไทย
                        $formatted_date = convertToThaiDate($doc->esv_ods_datesave ?? '');
                        
                        // แสดงข้อมูลแผนก/หมวดหมู่
                        $department_display = $doc->department_name ?? 'ไม่ระบุ';
                        $category_display = $doc->esv_category_name ?? 'ทั่วไป';
                        
                        // Animation delay
                        $animation_delay = $index * 100;
                    ?>
                        <div class="esv-item" 
                             style="animation-delay: <?php echo $animation_delay; ?>ms;"
                             data-status="<?php echo $doc->esv_ods_status; ?>"
                             data-original-status="<?php echo htmlspecialchars($status_display); ?>">
                            <div class="esv-header">
                                <div class="esv-id-section">
                                    <div class="esv-id-badge">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <?php echo htmlspecialchars($doc->esv_ods_reference_id ?? ''); ?>
                                    </div>
                                    <div class="esv-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        ยื่นวันที่: <?php echo $formatted_date; ?>
                                    </div>
                                </div>
                                <div class="esv-status-section">
                                    <span class="esv-status-badge esv-modern <?php echo $status_class; ?>" 
                                          style="background: <?php echo $status_color; ?>;">
                                        <i class="<?php echo $status_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($status_display); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="esv-body">
                                <h5 class="esv-topic-display">
                                    <i class="fas fa-file-alt"></i>
                                    <?php echo htmlspecialchars($doc->esv_ods_topic ?? ''); ?>
                                </h5>
                                
                                <div class="esv-meta">
                                    <div class="esv-meta-item">
                                        <div class="esv-meta-icon">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="esv-meta-content">
                                            <small>หน่วยงาน</small>
                                            <span><?php echo htmlspecialchars($department_display); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="esv-meta-item">
                                        <div class="esv-meta-icon">
                                            <i class="fas fa-tags"></i>
                                        </div>
                                        <div class="esv-meta-content">
                                            <small>หมวดหมู่</small>
                                            <span><?php echo htmlspecialchars($category_display); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="esv-meta-item">
                                        <div class="esv-meta-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="esv-meta-content">
                                            <small>ผู้ยื่น</small>
                                            <span><?php echo htmlspecialchars($doc->esv_ods_by ?? ''); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($doc->esv_ods_detail)): ?>
                                <div class="esv-preview">
                                    <p>
                                        <strong>รายละเอียด:</strong> 
                                        <?php 
                                        $detail = htmlspecialchars($doc->esv_ods_detail);
                                        echo mb_strlen($detail) > 200 ? mb_substr($detail, 0, 200) . '...' : $detail;
                                        ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="esv-actions">
                                <a href="<?php echo site_url('Esv_ods/my_document_detail/' . ($doc->esv_ods_reference_id ?? '')); ?>" 
                                   class="esv-action-btn esv-primary">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                                
                                <button onclick="copyEsvId('<?php echo htmlspecialchars($doc->esv_ods_reference_id ?? '', ENT_QUOTES); ?>')" 
                                        class="esv-action-btn esv-secondary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                                </button>
                                
                               
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Results Message -->
                <div class="esv-no-results" id="esv-no-results" style="display: none;">
                    <div class="esv-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>ไม่พบเอกสารที่ตรงกับเงื่อนไข</h5>
                    <p>ลองเปลี่ยนตัวกรองหรือดูรายการทั้งหมด</p>
                    <button onclick="filterEsvByStatus('all')" class="esv-action-btn esv-primary">
                        <i class="fas fa-list me-2"></i>ดูทั้งหมด
                    </button>
                </div>
                
            <?php else: ?>
                <!-- Empty State -->
                <div class="esv-empty-state">
                    <div class="esv-empty-illustration">
                        <div class="esv-empty-icon">
                            <i class="fas fa-file-alt"></i>
                            <div class="esv-icon-decoration">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="esv-empty-circles">
                            <div class="esv-circle esv-circle-1"></div>
                            <div class="esv-circle esv-circle-2"></div>
                            <div class="esv-circle esv-circle-3"></div>
                        </div>
                    </div>
                    <div class="esv-empty-content">
                        <h5>ยังไม่มีเอกสารในระบบ</h5>
                        <p>คุณยังไม่เคยยื่นเอกสารออนไลน์ในระบบ<br>เริ่มต้นโดยการยื่นเอกสารรายการแรกของคุณ</p>
                        <a href="<?php echo site_url('Esv_ods/submit_document'); ?>" class="esv-action-btn esv-primary">
                            <i class="fas fa-plus me-2"></i>ยื่นเอกสารออนไลน์
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Load jQuery และ Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// *** รอให้ jQuery โหลดเสร็จก่อน ***
$(document).ready(function() {
    console.log('🚀 เริ่มต้นระบบเอกสารออนไลน์ของฉัน');
    
    // *** ตัวแปรสำคัญ ***
    let currentEsvFilter = 'all';
    let originalStatusCounts = null;
    
    // *** บันทึกข้อมูลสถิติเดิม ***
    if (!originalStatusCounts) {
        originalStatusCounts = {
            total: parseInt($('.esv-stat-card.esv-total .esv-stat-content h3').text()) || 0,
            pending: parseInt($('.esv-stat-card.esv-pending .esv-stat-content h3').text()) || 0,
            processing: parseInt($('.esv-stat-card.esv-processing .esv-stat-content h3').text()) || 0,
            completed: parseInt($('.esv-stat-card.esv-completed .esv-stat-content h3').text()) || 0,
            rejected: parseInt($('.esv-stat-card.esv-rejected .esv-stat-content h3').text()) || 0,
            cancelled: parseInt($('.esv-stat-card.esv-cancelled .esv-stat-content h3').text()) || 0
        };
        console.log('📊 สถิติเดิม:', originalStatusCounts);
    }
    
    // *** ฟังก์ชันอัปเดต filter indicator ***
    function updateFilterIndicator(filter, visibleCount) {
        const filterText = $('#esv-filter-text');
        const clearFilterBtn = $('#esv-clear-filter');
        
        if (filterText.length) {
            const filterNames = {
                'all': 'ทั้งหมด',
                'pending': 'รอดำเนินการ',
                'processing': 'กำลังดำเนินการ',
                'completed': 'เสร็จสิ้น',
                'rejected': 'ไม่อนุมัติ',
                'cancelled': 'ยกเลิก'
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
    
    // *** ฟังก์ชันคัดลอกหมายเลขอ้างอิง ***
    window.copyEsvId = function(esvId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(esvId).then(() => {
                showEsvAlert('คัดลอกหมายเลข ' + esvId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopyEsvText(esvId);
            });
        } else {
            fallbackCopyEsvText(esvId);
        }
    };
    
    function fallbackCopyEsvText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showEsvAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
        } catch (err) {
            showEsvAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** Alert Functions ***
    function showEsvAlert(message, type) {
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
                confirmButtonColor: '#2563eb'
            });
        } else {
            // Enhanced fallback alert
            const alertDiv = document.createElement('div');
            const colors = {
                'success': '#059669',
                'error': '#dc2626',
                'warning': '#d97706',
                'info': '#0284c7'
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
                animation: esvAlertShow 0.3s ease;
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
                alertDiv.style.animation = 'esvAlertHide 0.3s ease';
                setTimeout(() => alertDiv.remove(), 300);
            }, 4000);
        }
    }
    
    // *** Event Listeners สำหรับการ์ดสถิติ ***
    $('.esv-stat-card').off('click').on('click', function(e) {
        e.preventDefault();
        
        const filter = $(this).attr('data-filter');
        if (filter) {
            console.log('🎯 คลิกการ์ดสถิติ:', filter);
            filterEsvByStatus(filter);
        }
    });
    
    // *** Event Listener สำหรับปุ่มล้างตัวกรอง ***
    $('#esv-clear-filter').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('🗑️ ล้างตัวกรอง');
        filterEsvByStatus('all');
    });
    
    // *** ฟังก์ชันค้นหาแบบ realtime ***
    let searchTimeout;
    window.searchDocuments = function(searchTerm) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            console.log('🔍 ค้นหา:', searchTerm);
            
            const esvItems = $('.esv-item');
            let visibleCount = 0;
            
            if (!searchTerm || searchTerm.trim() === '') {
                // แสดงทั้งหมดตาม filter ปัจจุบัน
                filterEsvByStatus(currentEsvFilter);
                return;
            }
            
            const searchLower = searchTerm.toLowerCase();
            
            esvItems.each(function() {
                const $item = $(this);
                const itemStatus = $item.attr('data-status');
                
                // ค้นหาในเนื้อหา
                const topic = $item.find('.esv-topic-display').text().toLowerCase();
                const refId = $item.find('.esv-id-badge').text().toLowerCase();
                const detail = $item.find('.esv-preview p').text().toLowerCase();
                
                const matchesSearch = topic.includes(searchLower) || 
                                    refId.includes(searchLower) || 
                                    detail.includes(searchLower);
                
                const matchesFilter = currentEsvFilter === 'all' || itemStatus === currentEsvFilter;
                
                if (matchesSearch && matchesFilter) {
                    $item.show().removeClass('hidden');
                    visibleCount++;
                    
                    // Highlight ผลการค้นหา
                    highlightSearchTerm($item, searchTerm);
                } else {
                    $item.hide().addClass('hidden');
                }
            });
            
            // อัปเดตจำนวน
            $('#esv-count').text(visibleCount + ' รายการ');
            
            // แสดง/ซ่อนข้อความไม่พบข้อมูล
            if (visibleCount === 0) {
                $('#esv-no-results').show();
            } else {
                $('#esv-no-results').hide();
            }
            
            console.log('🔍 ผลการค้นหา:', visibleCount, 'รายการ');
        }, 300);
    };
    
    // *** ฟังก์ชัน highlight คำค้น ***
    function highlightSearchTerm($item, searchTerm) {
        if (!searchTerm) return;
        
        const $topic = $item.find('.esv-topic-display');
        const $refId = $item.find('.esv-id-badge');
        
        const originalTopic = $topic.data('original') || $topic.text();
        const originalRefId = $refId.data('original') || $refId.text();
        
        // เก็บข้อมูลเดิม
        if (!$topic.data('original')) $topic.data('original', originalTopic);
        if (!$refId.data('original')) $refId.data('original', originalRefId);
        
        // Highlight
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        const highlightedTopic = originalTopic.replace(regex, '<mark style="background: #fef3c7; padding: 2px 4px; border-radius: 4px;">$1</mark>');
        const highlightedRefId = originalRefId.replace(regex, '<mark style="background: #fef3c7; padding: 2px 4px; border-radius: 4px;">$1</mark>');
        
        $topic.html('<i class="fas fa-file-alt"></i> ' + highlightedTopic);
        $refId.html('<i class="fas fa-hashtag me-1"></i> ' + highlightedRefId);
    }
    
    // *** ล้าง highlight เมื่อไม่มีการค้นหา ***
    function clearHighlight() {
        $('.esv-item').each(function() {
            const $item = $(this);
            const $topic = $item.find('.esv-topic-display');
            const $refId = $item.find('.esv-id-badge');
            
            const originalTopic = $topic.data('original');
            const originalRefId = $refId.data('original');
            
            if (originalTopic) {
                $topic.html('<i class="fas fa-file-alt"></i> ' + originalTopic);
            }
            if (originalRefId) {
                $refId.html('<i class="fas fa-hashtag me-1"></i> ' + originalRefId);
            }
        });
    }
    
    // *** เพิ่ม search box (ถ้าต้องการ) ***
    function addSearchBox() {
        const searchHtml = `
            <div class="esv-search-box" style="margin-bottom: 1rem;">
                <div class="input-group">
                    <span class="input-group-text" style="background: var(--esv-gradient-light); border: 1px solid rgba(37, 99, 235, 0.2); border-radius: 12px 0 0 12px;">
                        <i class="fas fa-search" style="color: var(--esv-primary-blue);"></i>
                    </span>
                    <input type="text" class="form-control" id="esv-search-input" 
                           placeholder="ค้นหาเอกสาร..." 
                           style="border: 1px solid rgba(37, 99, 235, 0.2); border-left: none; border-radius: 0 12px 12px 0; padding: 0.75rem;">
                    <button class="btn" type="button" id="esv-clear-search" 
                            style="background: var(--esv-gradient-primary); color: white; border: none; border-radius: 0 12px 12px 0; margin-left: -1px; display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('.esv-container').prepend(searchHtml);
        
        // Event listeners สำหรับ search
        $('#esv-search-input').on('input', function() {
            const searchTerm = $(this).val();
            searchDocuments(searchTerm);
            
            if (searchTerm.trim() !== '') {
                $('#esv-clear-search').show();
            } else {
                $('#esv-clear-search').hide();
                clearHighlight();
            }
        });
        
        $('#esv-clear-search').on('click', function() {
            $('#esv-search-input').val('');
            $(this).hide();
            clearHighlight();
            filterEsvByStatus(currentEsvFilter);
        });
    }
    
    // *** เรียกใช้ search box (ถ้าต้องการ) ***
    // addSearchBox();
    
    // *** เริ่มต้นด้วยการแสดงทั้งหมด ***
    filterEsvByStatus('all');
    
    // *** เพิ่ม keyboard shortcuts ***
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + F = เปิด search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = $('#esv-search-input');
            if (searchInput.length) {
                searchInput.focus();
            }
        }
        
        // Escape = ล้าง search และ filter
        if (e.key === 'Escape') {
            $('#esv-search-input').val('');
            $('#esv-clear-search').hide();
            clearHighlight();
            filterEsvByStatus('all');
        }
    });
    
    // *** เพิ่ม tooltip สำหรับ buttons ***
    $('[title]').each(function() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(this);
        }
    });
    
    // *** Animation ขณะโหลด ***
    $('.esv-item').each(function(index) {
        $(this).css('animation-delay', (index * 100) + 'ms');
    });
    
    // *** Smooth scroll สำหรับ internal links ***
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            target.get(0).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
    
    // *** เพิ่ม loading state สำหรับ external links ***
    $('a[href^="http"], a[href*="site_url"]').on('click', function() {
        const $this = $(this);
        const originalHtml = $this.html();
        
        $this.html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลด...')
             .prop('disabled', true);
        
        // คืนสถานะหลัง 3 วินาที (กรณี page ไม่ redirect)
        setTimeout(() => {
            $this.html(originalHtml).prop('disabled', false);
        }, 3000);
    });
    
    console.log('✅ ระบบเอกสารออนไลน์ของฉันพร้อมใช้งาน');
});

// *** CSS Animation สำหรับ Alert ***
const alertAnimationCSS = `
@keyframes esvAlertShow {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes esvAlertHide {
    from {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
}

/* Hover Effects สำหรับ Cards */
.esv-item:hover .esv-topic-display {
    color: var(--esv-primary-blue);
    transition: color 0.3s ease;
}

.esv-item:hover .esv-meta-icon {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Loading Animation */
.esv-loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.esv-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--esv-primary-blue);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Search Highlight Animation */
mark {
    animation: esvHighlight 0.5s ease-in-out;
}

@keyframes esvHighlight {
    0% {
        background-color: #fef3c7;
        transform: scale(1);
    }
    50% {
        background-color: #f59e0b;
        transform: scale(1.05);
    }
    100% {
        background-color: #fef3c7;
        transform: scale(1);
    }
}

/* Accessibility Improvements */
.esv-stat-card:focus,
.esv-action-btn:focus {
    outline: 2px solid var(--esv-primary-blue);
    outline-offset: 2px;
}

.esv-item:focus-within {
    box-shadow: var(--esv-shadow-strong);
    transform: translateY(-5px);
}

/* Print Styles */
@media print {
    .esv-actions,
    .esv-quick-actions-card,
    .esv-search-box {
        display: none !important;
    }
    
    .esv-item {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    
    .esv-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    :root {
        --esv-primary-blue: #1e40af;
        --esv-text-dark: #000000;
        --esv-text-muted: #4b5563;
    }
    
    .esv-item {
        border: 2px solid #000;
    }
    
    .esv-action-btn {
        border: 2px solid currentColor;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .esv-item:hover {
        transform: none;
    }
    
    .esv-stat-card:hover {
        transform: none;
    }
}
`;

// เพิ่ม CSS Animation เข้าไปใน head
const styleSheet = document.createElement('style');
styleSheet.textContent = alertAnimationCSS;
document.head.appendChild(styleSheet);
</script>