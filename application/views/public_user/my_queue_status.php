<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ตรวจสอบ login
if (!$this->session->userdata('mp_id') && !$this->session->userdata('m_id')) {
    redirect('User');
    return;
}

// ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย
function formatThaiDate($date_string, $format = 'full') {
    if (empty($date_string)) return '';
    
    try {
        $date = new DateTime($date_string);
        
        // เดือนไทย
        $thai_months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        
        $day = $date->format('j');
        $month = $thai_months[(int)$date->format('n')];
        $year = $date->format('Y') + 543; // แปลงเป็นปีไทย
        $time = $date->format('H:i');
        
        if ($format === 'full') {
            return "{$day} {$month} {$year} เวลา {$time} น.";
        } elseif ($format === 'short') {
            return "{$day} {$month} {$year}";
        } elseif ($format === 'date_only') {
            return "{$day} {$month} {$year}";
        }
        
        return "{$day} {$month} {$year} เวลา {$time} น.";
    } catch (Exception $e) {
        return $date_string; // fallback ถ้าแปลงไม่ได้
    }
}
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --queue-primary-purple: #9c6bdb;
    --queue-secondary-purple: #8a5ac7;
    --queue-light-purple: #f3efff;
    --queue-very-light-purple: #faf8ff;
    --queue-success-color: #00B73E;
    --queue-warning-color: #FFC700;
    --queue-danger-color: #FF0202;
    --queue-info-color: #17a2b8;
    --queue-text-dark: #2c3e50;
    --queue-text-muted: #6c757d;
    --queue-border-light: rgba(156, 107, 219, 0.1);
    --queue-shadow-light: 0 4px 20px rgba(156, 107, 219, 0.1);
    --queue-shadow-medium: 0 8px 30px rgba(156, 107, 219, 0.15);
    --queue-shadow-strong: 0 15px 40px rgba(156, 107, 219, 0.2);
    --queue-gradient-primary: linear-gradient(135deg, #9c6bdb 0%, #8a5ac7 100%);
    --queue-gradient-light: linear-gradient(135deg, #faf8ff 0%, #f3efff 100%);
    --queue-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.queue-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(156, 107, 219, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(138, 90, 199, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(156, 107, 219, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.queue-container-pages-news {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.queue-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.queue-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--queue-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.queue-header-decoration::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: queueShimmer 2s infinite;
}

.queue-page-title {
    font-size: 3rem;
    font-weight: 600;
    background: var(--queue-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.queue-page-subtitle {
    font-size: 1.2rem;
    color: var(--queue-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.queue-modern-card {
    background: var(--queue-gradient-card);
    border-radius: 24px;
    box-shadow: var(--queue-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(156, 107, 219, 0.08);
    z-index: 50;
}

.queue-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--queue-gradient-primary);
    z-index: 1;
}

/* User Info Card */
.queue-user-info-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.queue-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(156, 107, 219, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.queue-user-info-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.queue-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--queue-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(156, 107, 219, 0.3);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.queue-user-avatar:hover {
    box-shadow: 0 12px 35px rgba(156, 107, 219, 0.4);
    transform: translateY(-2px);
}

.queue-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: queueAvatarShine 3s infinite;
    z-index: 1;
}

.queue-profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease;
    animation: profileLoad 0.6s ease-out;
}

.queue-profile-image:hover {
    transform: scale(1.05);
}

.queue-profile-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--queue-gradient-primary);
    border-radius: 50%;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    animation: profileLoad 0.6s ease-out;
}

.queue-profile-initials {
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 2;
    position: relative;
}

.queue-profile-fallback:hover .queue-profile-initials {
    transform: scale(1.1);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.queue-user-details {
    flex: 1;
}

.queue-user-name {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.queue-user-email {
    color: var(--queue-text-muted);
    margin-bottom: 0;
    font-size: 1rem;
}

.queue-user-status {
    margin-left: auto;
}

.queue-status-active {
    background: linear-gradient(135deg, rgba(0, 183, 62, 0.1), rgba(0, 183, 62, 0.05));
    color: var(--queue-success-color);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid rgba(0, 183, 62, 0.2);
    display: inline-flex;
    align-items: center;
}

/* Stats Dashboard */
.queue-stats-dashboard {
    padding: 2.5rem;
}

.queue-dashboard-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.queue-dashboard-header h4 {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.queue-dashboard-subtitle {
    color: var(--queue-text-muted);
    font-size: 1rem;
}

.queue-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.queue-stat-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(156, 107, 219, 0.1);
    cursor: pointer;
    user-select: none;
}

.queue-stat-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--queue-shadow-strong);
}

.queue-stat-card.queue-active {
    border: 2px solid var(--queue-primary-purple);
    background: rgba(156, 107, 219, 0.05);
}

.queue-stat-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 1;
}

.queue-stat-card.queue-total .queue-stat-background { background: var(--queue-gradient-primary); }
.queue-stat-card.queue-pending .queue-stat-background { background: linear-gradient(135deg, #FFC700, #FFB700); }
.queue-stat-card.queue-confirmed .queue-stat-background { background: linear-gradient(135deg, #17a2b8, #138496); }
.queue-stat-card.queue-completed .queue-stat-background { background: linear-gradient(135deg, #00B73E, #009A33); }

.queue-stat-icon {
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

.queue-total .queue-stat-icon {
    background: linear-gradient(135deg, rgba(156, 107, 219, 0.15), rgba(156, 107, 219, 0.05));
    color: var(--queue-primary-purple);
    box-shadow: 0 8px 25px rgba(156, 107, 219, 0.2);
}

.queue-pending .queue-stat-icon {
    background: linear-gradient(135deg, rgba(255, 199, 0, 0.15), rgba(255, 199, 0, 0.05));
    color: #B8860B;
    box-shadow: 0 8px 25px rgba(255, 199, 0, 0.2);
}

.queue-confirmed .queue-stat-icon {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.15), rgba(23, 162, 184, 0.05));
    color: var(--queue-info-color);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.2);
}

.queue-completed .queue-stat-icon {
    background: linear-gradient(135deg, rgba(0, 183, 62, 0.15), rgba(0, 183, 62, 0.05));
    color: var(--queue-success-color);
    box-shadow: 0 8px 25px rgba(0, 183, 62, 0.2);
}

.queue-stat-content h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--queue-text-dark);
}

.queue-stat-content p {
    color: var(--queue-text-dark);
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.queue-stat-trend {
    color: var(--queue-text-muted);
    font-size: 0.9rem;
}

/* Quick Actions */
.queue-quick-actions-card {
    padding: 2rem 2.5rem;
}

.queue-actions-header {
    text-align: center;
    margin-bottom: 2rem;
}

.queue-actions-header h5 {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.queue-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.queue-action-button {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(156, 107, 219, 0.1);
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

.queue-action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(156, 107, 219, 0.6) 0%, rgba(138, 90, 199, 0.5) 100%);
    transition: all 0.3s ease;
    z-index: 0;
}

.queue-action-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(156, 107, 219, 0.15);
    border-color: rgba(156, 107, 219, 0.5);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.queue-action-button:hover::before {
    left: 0;
}

.queue-action-button:hover .queue-action-icon,
.queue-action-button:hover .queue-action-content {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.9);
}

.queue-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--queue-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 6px 20px rgba(156, 107, 219, 0.3);
    position: relative;
    z-index: 1;
}

.queue-action-content {
    position: relative;
    z-index: 1;
}

.queue-action-content h6 {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.queue-action-content small {
    color: var(--queue-text-muted);
    font-size: 0.9rem;
}

/* Queues List */
.queue-queues-list-card {
    padding: 0;
}

.queue-list-header {
    padding: 2rem 2.5rem 1rem;
    border-bottom: 1px solid var(--queue-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.queue-list-header h4 {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.queue-list-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.queue-list-count {
    background: var(--queue-gradient-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.queue-filter-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(156, 107, 219, 0.1);
    color: var(--queue-primary-purple);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(156, 107, 219, 0.2);
}

.queue-clear-filter {
    background: var(--queue-danger-color);
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

.queue-clear-filter:hover {
    transform: scale(1.1);
}

.queue-queues-container {
    padding: 1rem 2.5rem 2.5rem;
}

.queue-queue-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--queue-border-light);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: queueSlideUp 0.6s ease forwards;
}

.queue-queue-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--queue-gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.queue-queue-item:hover::before {
    transform: scaleX(1);
}

.queue-queue-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--queue-shadow-strong);
}

.queue-queue-item.hidden {
    display: none;
}

.queue-queue-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.queue-queue-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.queue-id-badge {
    background: var(--queue-gradient-light);
    color: var(--queue-primary-purple);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 1px solid rgba(156, 107, 219, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.queue-queue-date {
    color: var(--queue-text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    line-height: 1.4;
}

.queue-status-badge.queue-modern {
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

.queue-queue-title {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    line-height: 1.3;
}

.queue-queue-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.queue-meta-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.queue-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--queue-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--queue-primary-purple);
    font-size: 0.9rem;
}

.queue-meta-content {
    display: flex;
    flex-direction: column;
}

.queue-meta-content small {
    color: var(--queue-text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.queue-meta-content span {
    color: var(--queue-text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}

.queue-queue-preview {
    background: var(--queue-gradient-light);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--queue-primary-purple);
    margin-bottom: 1.5rem;
}

.queue-queue-preview p {
    color: var(--queue-text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.queue-queue-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.queue-action-btn {
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

.queue-action-btn.queue-primary {
    background: var(--queue-gradient-primary);
    color: white;
}

.queue-action-btn.queue-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(156, 107, 219, 0.4);
    color: white;
    text-decoration: none;
}

.queue-action-btn.queue-secondary {
    background: rgba(156, 107, 219, 0.1);
    color: var(--queue-primary-purple);
    border: 1px solid rgba(156, 107, 219, 0.3);
}

.queue-action-btn.queue-secondary:hover {
    background: var(--queue-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(156, 107, 219, 0.3);
}

.queue-action-btn.queue-danger {
    background: rgba(255, 2, 2, 0.1);
    color: var(--queue-danger-color);
    border: 1px solid rgba(255, 2, 2, 0.3);
}

.queue-action-btn.queue-danger:hover {
    background: var(--queue-danger-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 2, 2, 0.3);
}

/* Status Colors */
.queue-status-pending { background: var(--queue-warning-color) !important; }
.queue-status-confirmed { background: var(--queue-info-color) !important; }
.queue-status-processing { background: var(--queue-primary-purple) !important; }
.queue-status-completed { background: var(--queue-success-color) !important; }
.queue-status-cancelled { background: var(--queue-danger-color) !important; }

/* No Results */
.queue-no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--queue-text-muted);
}

.queue-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--queue-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--queue-primary-purple);
}

.queue-no-results h5 {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.queue-no-results p {
    margin-bottom: 2rem;
}

/* Empty State */
.queue-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    position: relative;
}

.queue-empty-illustration {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.queue-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--queue-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: var(--queue-primary-purple);
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(156, 107, 219, 0.2);
}

.queue-icon-decoration {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 35px;
    height: 35px;
    background: var(--queue-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(156, 107, 219, 0.4);
    border: 3px solid white;
}

.queue-empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.queue-circle {
    position: absolute;
    border: 2px solid rgba(156, 107, 219, 0.1);
    border-radius: 50%;
    animation: queuePulse 2s infinite;
}

.queue-circle-1 {
    width: 160px;
    height: 160px;
    top: -80px;
    left: -80px;
    animation-delay: 0s;
}

.queue-circle-2 {
    width: 200px;
    height: 200px;
    top: -100px;
    left: -100px;
    animation-delay: 0.5s;
}

.queue-circle-3 {
    width: 240px;
    height: 240px;
    top: -120px;
    left: -120px;
    animation-delay: 1s;
}

.queue-empty-content h5 {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.queue-empty-content p {
    color: var(--queue-text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* ===================================================================
   *** CANCEL QUEUE MODAL STYLES ***
   ================================================================= */

.queue-cancel-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    backdrop-filter: blur(8px);
    animation: queueModalFadeIn 0.3s ease;
}

.queue-cancel-modal.show {
    display: flex;
}

.queue-modal-container {
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    animation: queueModalSlideUp 0.3s ease;
}

.queue-modal-header {
    padding: 2rem 2rem 1rem;
    text-align: center;
    border-bottom: 1px solid var(--queue-border-light);
    position: relative;
}

.queue-modal-close {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--queue-text-muted);
    cursor: pointer;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.queue-modal-close:hover {
    background: rgba(255, 2, 2, 0.1);
    color: var(--queue-danger-color);
    transform: scale(1.1);
}

.queue-modal-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255, 2, 2, 0.15), rgba(255, 2, 2, 0.05));
    color: var(--queue-danger-color);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(255, 2, 2, 0.2);
    animation: queueIconPulse 2s infinite;
}

.queue-modal-title {
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.queue-modal-subtitle {
    color: var(--queue-text-muted);
    font-size: 1rem;
    margin-bottom: 0;
}

.queue-modal-body {
    padding: 2rem;
}

.queue-modal-queue-info {
    background: var(--queue-gradient-light);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(156, 107, 219, 0.2);
}

.queue-modal-queue-id {
    font-weight: 700;
    color: var(--queue-primary-purple);
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.queue-modal-queue-topic {
    color: var(--queue-text-dark);
    font-weight: 500;
    margin-bottom: 0;
}

.queue-form-group {
    margin-bottom: 1.5rem;
}

.queue-form-label {
    display: block;
    color: var(--queue-text-dark);
    font-weight: 600;
    margin-bottom: 0.8rem;
    font-size: 1rem;
}

.queue-form-label.required::after {
    content: ' *';
    color: var(--queue-danger-color);
}

.queue-form-textarea {
    width: 100%;
    min-height: 120px;
    border: 2px solid var(--queue-border-light);
    border-radius: 12px;
    padding: 1rem;
    font-family: 'Kanit', sans-serif;
    font-size: 1rem;
    resize: vertical;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
}

.queue-form-textarea:focus {
    outline: none;
    border-color: var(--queue-primary-purple);
    box-shadow: 0 0 0 3px rgba(156, 107, 219, 0.1);
    background: white;
}

.queue-form-textarea.error {
    border-color: var(--queue-danger-color);
    box-shadow: 0 0 0 3px rgba(255, 2, 2, 0.1);
}

.queue-form-help {
    font-size: 0.9rem;
    color: var(--queue-text-muted);
    margin-top: 0.5rem;
}

.queue-form-error {
    font-size: 0.9rem;
    color: var(--queue-danger-color);
    margin-top: 0.5rem;
    display: none;
}

.queue-form-error.show {
    display: block;
}

.queue-char-counter {
    text-align: right;
    font-size: 0.8rem;
    color: var(--queue-text-muted);
    margin-top: 0.3rem;
}

.queue-char-counter.warning {
    color: var(--queue-warning-color);
}

.queue-char-counter.danger {
    color: var(--queue-danger-color);
}

.queue-modal-footer {
    padding: 1.5rem 2rem 2rem;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    border-top: 1px solid var(--queue-border-light);
}

.queue-modal-btn {
    border: none;
    border-radius: 12px;
    padding: 0.8rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 120px;
    justify-content: center;
    font-family: 'Kanit', sans-serif;
}

.queue-modal-btn.queue-btn-cancel {
    background: rgba(108, 117, 125, 0.1);
    color: var(--queue-text-muted);
    border: 1px solid rgba(108, 117, 125, 0.3);
}

.queue-modal-btn.queue-btn-cancel:hover {
    background: rgba(108, 117, 125, 0.2);
    color: var(--queue-text-dark);
    transform: translateY(-2px);
}

.queue-modal-btn.queue-btn-confirm {
    background: linear-gradient(135deg, var(--queue-danger-color), #e30000);
    color: white;
    border: 1px solid var(--queue-danger-color);
}

.queue-modal-btn.queue-btn-confirm:hover {
    background: linear-gradient(135deg, #e30000, #cc0000);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 2, 2, 0.4);
}

.queue-modal-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.queue-modal-btn.loading {
    position: relative;
    color: transparent;
}

.queue-modal-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 2px solid currentColor;
    animation: queueSpinner 1s linear infinite;
}

/* Animations */
@keyframes queueShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes queueAvatarShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes queueSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes queuePulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 0.1;
    }
}

@keyframes queueFadeIn {
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

@keyframes queueModalFadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes queueModalSlideUp {
    from {
        opacity: 0;
        transform: translateY(50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes queueIconPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 25px rgba(255, 2, 2, 0.2);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 12px 35px rgba(255, 2, 2, 0.3);
    }
}

@keyframes queueSpinner {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .queue-page-title {
        font-size: 2.2rem;
    }
    
    .queue-page-subtitle {
        font-size: 1rem;
    }
    
    .queue-user-info-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .queue-user-status {
        margin-left: 0;
    }
    
    .queue-stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    
    .queue-stat-card {
        padding: 1.2rem 0.8rem;
    }
    
    .queue-stat-content h3 {
        font-size: 1.8rem;
    }
    
    .queue-stat-content p {
        font-size: 0.9rem;
    }
    
    .queue-stat-trend small {
        font-size: 0.7rem;
    }
    
    .queue-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .queue-list-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .queue-list-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .queue-queues-container {
        padding: 1rem;
    }
    
    .queue-queue-item {
        padding: 1.5rem;
    }
    
    .queue-queue-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .queue-queue-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .queue-queue-actions {
        flex-direction: column;
    }
    
    .queue-action-btn {
        width: 100%;
        justify-content: center;
    }
    
    /* Modal Responsive */
    .queue-modal-container {
        width: 95%;
        max-width: none;
    }
    
    .queue-modal-header,
    .queue-modal-body,
    .queue-modal-footer {
        padding: 1.5rem;
    }
    
    .queue-modal-footer {
        flex-direction: column;
    }
    
    .queue-modal-btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .queue-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .queue-user-avatar {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .queue-queue-title {
        font-size: 1.2rem;
    }
}

@media print {
    .queue-action-btn,
    .queue-quick-actions-card {
        display: none !important;
    }
    
    .queue-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .queue-stat-card {
        cursor: default;
    }
    
    .queue-cancel-modal {
        display: none !important;
    }
}
</style>

<div class="queue-bg-pages">
    <div class="queue-container-pages-news">
        
        <!-- Page Header -->
        <div class="queue-page-header">
            <div class="queue-header-decoration"></div>
            <h1 class="queue-page-title">
                <i class="fas fa-calendar-check me-3"></i>
                สถานะการจองคิวของฉัน
            </h1>
            <p class="queue-page-subtitle">ติดตามและจัดการการจองคิวของคุณได้อย่างสะดวก</p>
        </div>

        <!-- User Info Card -->
        <div class="queue-modern-card queue-user-info-card">
            <div class="queue-card-gradient-bg"></div>
            <div class="queue-user-info-content">
                <div class="queue-user-avatar">
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
                             class="queue-profile-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback เมื่อโหลดรูปไม่ได้ -->
                        <div class="queue-profile-fallback" style="display: none;">
                            <span class="queue-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php else: ?>
                        <!-- แสดง initials เมื่อไม่มีรูปโปรไฟล์ -->
                        <div class="queue-profile-fallback">
                            <span class="queue-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="queue-user-details">
                    <h4 class="queue-user-name">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php 
                        $full_name = trim($mp_prefix . ' ' . $mp_fname . ' ' . $mp_lname);
                        echo htmlspecialchars($full_name ?: 'ผู้ใช้'); 
                        ?>
                    </h4>
                    <p class="queue-user-email">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($user_info['email'] ?? $user_info['mp_email'] ?? ''); ?>
                    </p>
                </div>
                <div class="queue-user-status">
                    <span class="queue-status-active">
                        <i class="fas fa-check-circle me-1"></i>
                        สมาชิกที่ยืนยันแล้ว
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="queue-modern-card queue-stats-dashboard">
            <div class="queue-dashboard-header">
                <h4><i class="fas fa-chart-pie me-2"></i>สถิติการจองคิว</h4>
                <span class="queue-dashboard-subtitle">คลิกเพื่อกรองรายการตามสถานะ</span>
            </div>
            
            <div class="queue-stats-grid">
                <div class="queue-stat-card queue-total queue-active" onclick="filterQueuesByStatus('all')" data-filter="all">
                    <div class="queue-stat-background"></div>
                    <div class="queue-stat-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="queue-stat-content">
                        <h3><?php echo $status_counts['total']; ?></h3>
                        <p>ทั้งหมด</p>
                        <div class="queue-stat-trend">
                            <small>การจองคิวทั้งหมด</small>
                        </div>
                    </div>
                </div>
                
                <div class="queue-stat-card queue-pending" onclick="filterQueuesByStatus('pending')" data-filter="pending">
                    <div class="queue-stat-background"></div>
                    <div class="queue-stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="queue-stat-content">
                        <h3><?php echo $status_counts['pending']; ?></h3>
                        <p>รอยืนยัน</p>
                        <div class="queue-stat-trend">
                            <small>กำลังรอการยืนยัน</small>
                        </div>
                    </div>
                </div>
                
                <div class="queue-stat-card queue-confirmed" onclick="filterQueuesByStatus('confirmed')" data-filter="confirmed">
                    <div class="queue-stat-background"></div>
                    <div class="queue-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="queue-stat-content">
                        <h3><?php echo $status_counts['confirmed']; ?></h3>
                        <p>ยืนยันแล้ว</p>
                        <div class="queue-stat-trend">
                            <small>ได้รับการยืนยันแล้ว</small>
                        </div>
                    </div>
                </div>
                
                <div class="queue-stat-card queue-completed" onclick="filterQueuesByStatus('completed')" data-filter="completed">
                    <div class="queue-stat-background"></div>
                    <div class="queue-stat-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="queue-stat-content">
                        <h3><?php echo $status_counts['completed']; ?></h3>
                        <p>เสร็จสิ้น</p>
                        <div class="queue-stat-trend">
                            <small>ดำเนินการเรียบร้อย</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="queue-modern-card queue-quick-actions-card">
            <div class="queue-actions-header">
                <h5><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
            </div>
            <div class="queue-actions-grid">
                <a href="<?php echo site_url('Queue/adding_queue'); ?>" class="queue-action-button">
                    <div class="queue-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="queue-action-content">
                        <h6>จองคิวใหม่</h6>
                        <small>สร้างการจองคิวใหม่</small>
                    </div>
                </a>
                
                
            </div>
        </div>

        <!-- Queues List -->
        <div class="queue-modern-card queue-queues-list-card">
            <div class="queue-list-header">
                <h4><i class="fas fa-list-ul me-2"></i>รายการการจองคิว</h4>
                <div class="queue-list-controls">
                    <span class="queue-list-count" id="queue-count"><?php echo count($queues); ?> รายการ</span>
                    <div class="queue-filter-indicator" id="queue-filter-indicator">
                        <i class="fas fa-filter me-1"></i>
                        <span id="queue-filter-text">ทั้งหมด</span>
                        <button class="queue-clear-filter" onclick="filterQueuesByStatus('all')" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <?php if (!empty($queues)): ?>
                <div class="queue-queues-container">
                    <?php foreach ($queues as $index => $queue): 
                        // ข้อมูลที่ประมวลผลแล้ว
                        $latest_status = $queue['latest_status_display'];
                        $status_class = $queue['status_class'];
                        $status_icon = $queue['status_icon'];
                        $status_color = $queue['status_color'];
                        
                        // Format date ด้วยฟังก์ชันไทย
                        $formatted_date = formatThaiDate($queue['queue_date'] ?: $queue['queue_datesave']);
                        
                        // Latest update ด้วยฟังก์ชันไทย
                        $latest_update = '';
                        if ($queue['latest_update']) {
                            $latest_update = formatThaiDate($queue['latest_update']);
                        }
                        
                        // Animation delay
                        $animation_delay = $index * 100;
                        
                        // Status mapping for filter
                        $filter_status = 'all';
                        switch ($status_class) {
                            case 'queue-status-pending':
                                $filter_status = 'pending';
                                break;
                            case 'queue-status-confirmed':
                                $filter_status = 'confirmed';
                                break;
                            case 'queue-status-processing':
                                $filter_status = 'confirmed';
                                break;
                            case 'queue-status-completed':
                                $filter_status = 'completed';
                                break;
                            case 'queue-status-cancelled':
                                $filter_status = 'cancelled';
                                break;
                        }
                    ?>
                        <div class="queue-queue-item" 
                             style="animation-delay: <?php echo $animation_delay; ?>ms;"
                             data-status="<?php echo $filter_status; ?>"
                             data-original-status="<?php echo htmlspecialchars($latest_status); ?>">
                            <div class="queue-queue-header">
                                <div class="queue-queue-id-section">
                                    <div class="queue-id-badge">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <?php echo htmlspecialchars($queue['queue_id']); ?>
                                    </div>
                                    <div class="queue-queue-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        จองวันที่: <?php echo $formatted_date; ?>
                                        <?php if (!empty($queue['queue_time_slot'])): ?>
                                            <br><span class="ms-4">
                                                <i class="fas fa-clock me-1"></i>
                                                ช่วงเวลา: <?php echo htmlspecialchars($queue['queue_time_slot']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="queue-queue-status-section">
                                    <span class="queue-status-badge queue-modern <?php echo $status_class; ?>" style="background: <?php echo $status_color; ?>;">
                                        <i class="<?php echo $status_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($latest_status); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="queue-queue-body">
                                <h5 class="queue-queue-title">
                                    <?php echo htmlspecialchars($queue['queue_topic']); ?>
                                </h5>
                                
                                <div class="queue-queue-meta">
                                    <div class="queue-meta-item">
                                        <div class="queue-meta-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="queue-meta-content">
                                            <small>ผู้จอง</small>
                                            <span><?php echo htmlspecialchars($queue['queue_by']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="queue-meta-item">
                                        <div class="queue-meta-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="queue-meta-content">
                                            <small>เบอร์โทร</small>
                                            <span><?php echo htmlspecialchars($queue['queue_phone']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($latest_update): ?>
                                    <div class="queue-meta-item">
                                        <div class="queue-meta-icon">
                                            <i class="fas fa-sync-alt"></i>
                                        </div>
                                        <div class="queue-meta-content">
                                            <small>อัพเดทล่าสุด</small>
                                            <span><?php echo $latest_update; ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="queue-queue-preview">
                                    <p>
                                        <?php 
                                        $excerpt = mb_substr($queue['queue_detail'], 0, 150);
                                        echo nl2br(htmlspecialchars($excerpt));
                                        if (mb_strlen($queue['queue_detail']) > 150) echo '...';
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <div class="queue-queue-actions">
                                <a href="<?php echo site_url('Queue/my_queue_detail/' . $queue['queue_id']); ?>" 
                                   class="queue-action-btn queue-primary">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                                <button onclick="copyQueueId('<?php echo $queue['queue_id']; ?>')" 
                                        class="queue-action-btn queue-secondary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                                </button>
                                <?php if (in_array($queue['queue_status'], ['รอยืนยันการจอง', 'ยืนยันการจอง'])): ?>
                                <button onclick="showCancelModal('<?php echo $queue['queue_id']; ?>', '<?php echo htmlspecialchars($queue['queue_topic'], ENT_QUOTES); ?>')" 
                                        class="queue-action-btn queue-danger">
                                    <i class="fas fa-times me-2"></i>ยกเลิกคิว
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Results Message -->
                <div class="queue-no-results" id="queue-no-results" style="display: none;">
                    <div class="queue-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>ไม่พบรายการที่ตรงกับเงื่อนไข</h5>
                    <p>ลองเปลี่ยนตัวกรองหรือดูรายการทั้งหมด</p>
                    <button onclick="filterQueuesByStatus('all')" class="queue-action-btn queue-primary">
                        <i class="fas fa-list me-2"></i>ดูทั้งหมด
                    </button>
                </div>
                
            <?php else: ?>
                <!-- Empty State -->
                <div class="queue-empty-state">
                    <div class="queue-empty-illustration">
                        <div class="queue-empty-icon">
                            <i class="fas fa-calendar-times"></i>
                            <div class="queue-icon-decoration">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="queue-empty-circles">
                            <div class="queue-circle queue-circle-1"></div>
                            <div class="queue-circle queue-circle-2"></div>
                            <div class="queue-circle queue-circle-3"></div>
                        </div>
                    </div>
                    <div class="queue-empty-content">
                        <h5>ยังไม่มีการจองคิว</h5>
                        <p>คุณยังไม่เคยจองคิวเข้ามาในระบบ<br>เริ่มต้นโดยการจองคิวแรกของคุณ</p>
                        <a href="<?php echo site_url('Queue/adding_queue'); ?>" class="queue-action-btn queue-primary queue-large">
                            <i class="fas fa-plus me-2"></i>จองคิวแรก
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ===================================================================
     *** CANCEL QUEUE MODAL ***
     ================================================================= -->
<div class="queue-cancel-modal" id="queueCancelModal">
    <div class="queue-modal-container">
        <div class="queue-modal-header">
            <button type="button" class="queue-modal-close" onclick="hideCancelModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="queue-modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 class="queue-modal-title">ยืนยันการยกเลิกคิว</h4>
            <p class="queue-modal-subtitle">กรุณากรอกเหตุผลการยกเลิกเพื่อดำเนินการต่อ</p>
        </div>
        
        <div class="queue-modal-body">
            <div class="queue-modal-queue-info">
                <div class="queue-modal-queue-id" id="modalQueueId">
                    <i class="fas fa-hashtag me-1"></i>
                    หมายเลขคิว: -
                </div>
                <div class="queue-modal-queue-topic" id="modalQueueTopic">
                    หัวข้อ: -
                </div>
            </div>
            
            <form id="queueCancelForm">
                <div class="queue-form-group">
                    <label for="cancelReason" class="queue-form-label required">
                        <i class="fas fa-comment-alt me-1"></i>
                        เหตุผลการยกเลิก
                    </label>
                    <textarea 
                        id="cancelReason" 
                        class="queue-form-textarea" 
                        placeholder="กรุณาระบุเหตุผลที่ต้องการยกเลิกคิว เช่น เปลี่ยนแปลงแผนการ, ไม่สามารถมาได้ตามเวลา, หรือมีเหตุฉุกเฉิน..."
                        maxlength="500"
                        rows="4"></textarea>
                    <div class="queue-form-help">
                        ระบุเหตุผลอย่างน้อย 5 ตัวอักษร เพื่อให้เจ้าหน้าที่สามารถปรับปรุงการบริการได้
                    </div>
                    <div class="queue-char-counter">
                        <span id="charCount">0</span>/500 ตัวอักษร
                    </div>
                    <div class="queue-form-error" id="cancelReasonError">
                        กรุณากรอกเหตุผลการยกเลิกอย่างน้อย 5 ตัวอักษร
                    </div>
                </div>
            </form>
        </div>
        
        <div class="queue-modal-footer">
            <button type="button" class="queue-modal-btn queue-btn-cancel" onclick="hideCancelModal()">
                <i class="fas fa-arrow-left me-1"></i>
                ยกเลิก
            </button>
            <button type="button" class="queue-modal-btn queue-btn-confirm" id="confirmCancelBtn" onclick="confirmCancelQueue()">
                <i class="fas fa-times me-1"></i>
                ยืนยันการยกเลิก
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===================================================================
// *** GLOBAL VARIABLES ***
// ===================================================================

// Current filter state
let currentQueueFilter = 'all';
let currentQueueToCancel = null;
let currentQueueTopic = null;

// ===================================================================
// *** FILTER FUNCTIONS ***
// ===================================================================

// Filter queues function
function filterQueuesByStatus(filter) {
    currentQueueFilter = filter;
    
    // Update active state of stat cards
    document.querySelectorAll('.queue-stat-card').forEach(card => {
        card.classList.remove('queue-active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('queue-active');
    
    // Filter queue items
    const queueItems = document.querySelectorAll('.queue-queue-item');
    const noResults = document.getElementById('queue-no-results');
    let visibleCount = 0;
    
    queueItems.forEach(item => {
        const itemStatus = item.getAttribute('data-status');
        if (filter === 'all' || itemStatus === filter) {
            item.style.display = 'block';
            item.classList.remove('hidden');
            visibleCount++;
            
            // Re-trigger animation
            item.style.animation = 'none';
            setTimeout(() => {
                item.style.animation = 'queueFadeIn 0.5s ease forwards';
            }, 10);
        } else {
            item.style.display = 'none';
            item.classList.add('hidden');
        }
    });
    
    // Update queue count
    const countElement = document.getElementById('queue-count');
    if (countElement) {
        countElement.textContent = `${visibleCount} รายการ`;
    }
    
    // Update filter indicator
    const filterText = document.getElementById('queue-filter-text');
    const clearFilterBtn = document.querySelector('.queue-clear-filter');
    
    if (filterText) {
        const filterNames = {
            'all': 'ทั้งหมด',
            'pending': 'รอยืนยัน',
            'confirmed': 'ยืนยันแล้ว',
            'completed': 'เสร็จสิ้น',
            'cancelled': 'ยกเลิก'
        };
        filterText.textContent = filterNames[filter] || 'ทั้งหมด';
    }
    
    if (clearFilterBtn) {
        clearFilterBtn.style.display = filter === 'all' ? 'none' : 'flex';
    }
    
    // Show/hide no results message
    if (noResults) {
        if (visibleCount === 0 && filter !== 'all') {
            noResults.style.display = 'block';
            noResults.style.animation = 'queueFadeIn 0.5s ease forwards';
        } else {
            noResults.style.display = 'none';
        }
    }
    
    // Add smooth scroll to queues list
    if (filter !== 'all') {
        const queuesList = document.querySelector('.queue-queues-list-card');
        if (queuesList) {
            queuesList.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}

// ===================================================================
// *** COPY QUEUE ID FUNCTION ***
// ===================================================================

// Copy Queue ID Function
function copyQueueId(queueId) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(queueId).then(() => {
            showQueueAlert('คัดลอกหมายเลข ' + queueId + ' สำเร็จ', 'success');
        }).catch(() => {
            fallbackCopyQueueText(queueId);
        });
    } else {
        fallbackCopyQueueText(queueId);
    }
}

function fallbackCopyQueueText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showQueueAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
    } catch (err) {
        showQueueAlert('ไม่สามารถคัดลอกได้', 'error');
    }
    document.body.removeChild(textArea);
}

// ===================================================================
// *** CANCEL QUEUE MODAL FUNCTIONS ***
// ===================================================================

// Show Cancel Modal
function showCancelModal(queueId, queueTopic) {
    currentQueueToCancel = queueId;
    currentQueueTopic = queueTopic;
    
    // Update modal content
    document.getElementById('modalQueueId').innerHTML = `<i class="fas fa-hashtag me-1"></i>หมายเลขคิว: ${queueId}`;
    document.getElementById('modalQueueTopic').textContent = `หัวข้อ: ${queueTopic}`;
    
    // Clear form
    const cancelReason = document.getElementById('cancelReason');
    const cancelReasonError = document.getElementById('cancelReasonError');
    const confirmBtn = document.getElementById('confirmCancelBtn');
    
    cancelReason.value = '';
    cancelReason.classList.remove('error');
    cancelReasonError.classList.remove('show');
    confirmBtn.disabled = false;
    confirmBtn.classList.remove('loading');
    
    updateCharCounter();
    
    // Show modal
    const modal = document.getElementById('queueCancelModal');
    modal.classList.add('show');
    
    // Focus on textarea
    setTimeout(() => {
        cancelReason.focus();
    }, 300);
    
    // Prevent background scroll
    document.body.style.overflow = 'hidden';
}

// Hide Cancel Modal
function hideCancelModal() {
    const modal = document.getElementById('queueCancelModal');
    modal.classList.remove('show');
    
    // Reset variables
    currentQueueToCancel = null;
    currentQueueTopic = null;
    
    // Restore background scroll
    document.body.style.overflow = '';
}

// Update Character Counter
function updateCharCounter() {
    const cancelReason = document.getElementById('cancelReason');
    const charCount = document.getElementById('charCount');
    const charCounter = document.querySelector('.queue-char-counter');
    
    const currentLength = cancelReason.value.length;
    charCount.textContent = currentLength;
    
    // Update color based on length
    charCounter.classList.remove('warning', 'danger');
    if (currentLength > 400) {
        charCounter.classList.add('danger');
    } else if (currentLength > 300) {
        charCounter.classList.add('warning');
    }
}

// Validate Cancel Form
function validateCancelForm() {
    const cancelReason = document.getElementById('cancelReason');
    const cancelReasonError = document.getElementById('cancelReasonError');
    const confirmBtn = document.getElementById('confirmCancelBtn');
    
    const reason = cancelReason.value.trim();
    
    // Check if reason is provided and meets minimum length
    if (reason.length < 5) {
        cancelReason.classList.add('error');
        cancelReasonError.textContent = reason.length === 0 ? 
            'กรุณากรอกเหตุผลการยกเลิก' : 
            `เหตุผลต้องมีอย่างน้อย 5 ตัวอักษร (ปัจจุบัน ${reason.length} ตัวอักษร)`;
        cancelReasonError.classList.add('show');
        confirmBtn.disabled = true;
        return false;
    } else {
        cancelReason.classList.remove('error');
        cancelReasonError.classList.remove('show');
        confirmBtn.disabled = false;
        return true;
    }
}

// Confirm Cancel Queue
function confirmCancelQueue() {
    // Validate form
    if (!validateCancelForm()) {
        return;
    }
    
    if (!currentQueueToCancel) {
        showQueueAlert('เกิดข้อผิดพลาด: ไม่พบหมายเลขคิว', 'error');
        return;
    }
    
    const cancelReason = document.getElementById('cancelReason').value.trim();
    const confirmBtn = document.getElementById('confirmCancelBtn');
    
    // Show loading state
    confirmBtn.disabled = true;
    confirmBtn.classList.add('loading');
    
    // Prepare form data
    const formData = new URLSearchParams();
    formData.append('queue_id', currentQueueToCancel);
    formData.append('cancel_reason', cancelReason);
    formData.append('user_type', '<?php echo isset($user_type) ? $user_type : "public"; ?>');
    formData.append('user_id', '<?php echo isset($user_info["id"]) ? $user_info["id"] : ""; ?>');
    
    // Send AJAX request
    fetch('<?php echo site_url("Queue/cancel_queue"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        // Reset button state
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('loading');
        
        if (data.success) {
            // Hide modal first
            hideCancelModal();
            
            // Show success message
            showQueueAlert('ยกเลิกคิวเรียบร้อยแล้ว', 'success');
            
            // Reload page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
            
        } else {
            const errorMessage = data.message || 'เกิดข้อผิดพลาดในการยกเลิกคิว';
            showQueueAlert(errorMessage, 'error');
            
            // Show debug info if available
            if (data.debug && window.console) {
                console.error('Cancel Queue Debug:', data.debug);
            }
        }
    })
    .catch(error => {
        // Reset button state
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('loading');
        
        console.error('Cancel Queue Error:', error);
        
        let errorMessage = 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์';
        
        if (error.message.includes('HTTP 500')) {
            errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์ กรุณาติดต่อผู้ดูแลระบบ';
        } else if (error.message.includes('HTTP 404')) {
            errorMessage = 'ไม่พบหน้าที่ร้องขอ กรุณาตรวจสอบ URL';
        } else if (error.message.includes('Failed to fetch')) {
            errorMessage = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
        }
        
        showQueueAlert(errorMessage, 'error');
    });
}

// ===================================================================
// *** ALERT FUNCTIONS ***
// ===================================================================

function showQueueAlert(message, type) {
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
            position: 'center'
        });
    } else {
        // Enhanced fallback alert
        const alertDiv = document.createElement('div');
        const colors = {
            'success': '#00B73E',
            'error': '#FF0202',
            'warning': '#FFC700',
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
            animation: queueAlertShow 0.3s ease;
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
            alertDiv.style.animation = 'queueAlertHide 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }, 4000);
    }
}

// ===================================================================
// *** EVENT LISTENERS ***
// ===================================================================

document.addEventListener('DOMContentLoaded', function() {
    // *** Character counter for cancel reason ***
    const cancelReason = document.getElementById('cancelReason');
    if (cancelReason) {
        cancelReason.addEventListener('input', function() {
            updateCharCounter();
            validateCancelForm();
        });
        
        cancelReason.addEventListener('blur', validateCancelForm);
    }
    
    // *** Modal close on outside click ***
    const modal = document.getElementById('queueCancelModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideCancelModal();
            }
        });
    }
    
    // *** ESC key to close modal ***
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (modal && modal.classList.contains('show')) {
                hideCancelModal();
            }
        }
    });
    
    // *** Animate stats cards ***
    const statCards = document.querySelectorAll('.queue-stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // *** Animate action buttons ***
    const actionButtons = document.querySelectorAll('.queue-action-button');
    actionButtons.forEach((button, index) => {
        button.style.opacity = '0';
        button.style.transform = 'translateX(-30px)';
        
        setTimeout(() => {
            button.style.transition = 'all 0.6s ease';
            button.style.opacity = '1';
            button.style.transform = 'translateX(0)';
        }, 300 + (index * 150));
    });
    
    // *** Enhanced hover effects for stat cards ***
    statCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            if (!card.classList.contains('queue-active')) {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            }
        });
        
        card.addEventListener('mouseleave', () => {
            if (!card.classList.contains('queue-active')) {
                card.style.transform = 'translateY(0) scale(1)';
            }
        });
        
        // Add click effect
        card.addEventListener('click', () => {
            card.style.transform = 'scale(0.98)';
            setTimeout(() => {
                card.style.transform = card.classList.contains('queue-active') ? 'translateY(-10px) scale(1.02)' : 'scale(1)';
            }, 150);
        });
    });
    
    // *** Initialize queue count ***
    const initialCount = document.querySelectorAll('.queue-queue-item').length;
    const countElement = document.getElementById('queue-count');
    if (countElement) {
        countElement.textContent = `${initialCount} รายการ`;
    }
});

// ===================================================================
// *** CSS ANIMATIONS (if not already added) ***
// ===================================================================

if (!document.getElementById('queue-alert-animations')) {
    const alertStyle = document.createElement('style');
    alertStyle.id = 'queue-alert-animations';
    alertStyle.textContent = `
        @keyframes queueAlertShow {
            from {
                transform: translate(-50%, -50%) scale(0.7);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }
        
        @keyframes queueAlertHide {
            from {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
            to {
                transform: translate(-50%, -50%) scale(0.7);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(alertStyle);
}
</script>