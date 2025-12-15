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
    --suggestions-primary-green: #4caf50;
    --suggestions-secondary-green: #66bb6a;
    --suggestions-light-green: #e8f5e9;
    --suggestions-very-light-green: #f1f8e9;
    --suggestions-success-color: #2e7d32;
    --suggestions-warning-color: #ff9800;
    --suggestions-danger-color: #f44336;
    --suggestions-info-color: #2196f3;
    --suggestions-text-dark: #2e5233;
    --suggestions-text-muted: #6c757d;
    --suggestions-border-light: rgba(76, 175, 80, 0.1);
    --suggestions-shadow-light: 0 4px 20px rgba(76, 175, 80, 0.1);
    --suggestions-shadow-medium: 0 8px 30px rgba(76, 175, 80, 0.15);
    --suggestions-shadow-strong: 0 15px 40px rgba(76, 175, 80, 0.2);
    --suggestions-gradient-primary: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
    --suggestions-gradient-light: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);
    --suggestions-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.suggestions-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(76, 175, 80, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(46, 125, 50, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(76, 175, 80, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.suggestions-container-pages-news {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.suggestions-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.suggestions-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--suggestions-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.suggestions-header-decoration::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: suggestionsShimmer 2s infinite;
}

.suggestions-page-title {
    font-size: 3rem;
    font-weight: 600;
    background: var(--suggestions-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.suggestions-page-subtitle {
    font-size: 1.2rem;
    color: var(--suggestions-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.suggestions-modern-card {
    background: var(--suggestions-gradient-card);
    border-radius: 24px;
    box-shadow: var(--suggestions-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(76, 175, 80, 0.08);
    z-index: 50;
}

.suggestions-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--suggestions-gradient-primary);
    z-index: 1;
}

/* User Info Card */
.suggestions-user-info-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.suggestions-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.suggestions-user-info-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.suggestions-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--suggestions-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.suggestions-user-avatar:hover {
    box-shadow: 0 12px 35px rgba(76, 175, 80, 0.4);
    transform: translateY(-2px);
}

.suggestions-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: suggestionsAvatarShine 3s infinite;
    z-index: 1;
}

.suggestions-profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease;
    animation: profileLoad 0.6s ease-out;
}

.suggestions-profile-image:hover {
    transform: scale(1.05);
}

.suggestions-profile-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--suggestions-gradient-primary);
    border-radius: 50%;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    animation: profileLoad 0.6s ease-out;
}

.suggestions-profile-initials {
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 2;
    position: relative;
}

.suggestions-profile-fallback:hover .suggestions-profile-initials {
    transform: scale(1.1);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.suggestions-user-details {
    flex: 1;
}

.suggestions-user-name {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.suggestions-user-email {
    color: var(--suggestions-text-muted);
    margin-bottom: 0;
    font-size: 1rem;
}

.suggestions-user-status {
    margin-left: auto;
}

.suggestions-status-active {
    background: linear-gradient(135deg, rgba(46, 125, 50, 0.1), rgba(46, 125, 50, 0.05));
    color: var(--suggestions-success-color);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid rgba(46, 125, 50, 0.2);
    display: inline-flex;
    align-items: center;
}

/* Stats Dashboard */
.suggestions-stats-dashboard {
    padding: 2.5rem;
}

.suggestions-dashboard-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.suggestions-dashboard-header h4 {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.suggestions-dashboard-subtitle {
    color: var(--suggestions-text-muted);
    font-size: 1rem;
}

.suggestions-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.suggestions-stat-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(76, 175, 80, 0.1);
    cursor: pointer;
    user-select: none;
}

.suggestions-stat-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--suggestions-shadow-strong);
}

.suggestions-stat-card.suggestions-active {
    border: 2px solid var(--suggestions-primary-green);
    background: rgba(76, 175, 80, 0.05);
}

.suggestions-stat-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 1;
}

.suggestions-stat-card.suggestions-total .suggestions-stat-background { background: var(--suggestions-gradient-primary); }
.suggestions-stat-card.suggestions-received .suggestions-stat-background { background: linear-gradient(135deg, #ff9800, #f57c00); }
.suggestions-stat-card.suggestions-reviewing .suggestions-stat-background { background: linear-gradient(135deg, #2196f3, #1976d2); }
.suggestions-stat-card.suggestions-replied .suggestions-stat-background { background: linear-gradient(135deg, #4caf50, #2e7d32); }

.suggestions-stat-icon {
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

.suggestions-total .suggestions-stat-icon {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(76, 175, 80, 0.05));
    color: var(--suggestions-primary-green);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2);
}

.suggestions-received .suggestions-stat-icon {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.15), rgba(255, 152, 0, 0.05));
    color: #f57c00;
    box-shadow: 0 8px 25px rgba(255, 152, 0, 0.2);
}

.suggestions-reviewing .suggestions-stat-icon {
    background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(33, 150, 243, 0.05));
    color: var(--suggestions-info-color);
    box-shadow: 0 8px 25px rgba(33, 150, 243, 0.2);
}

.suggestions-replied .suggestions-stat-icon {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(76, 175, 80, 0.05));
    color: var(--suggestions-success-color);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.2);
}

.suggestions-stat-content h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--suggestions-text-dark);
}

.suggestions-stat-content p {
    color: var(--suggestions-text-dark);
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.suggestions-stat-trend {
    color: var(--suggestions-text-muted);
    font-size: 0.9rem;
}

/* Quick Actions */
.suggestions-quick-actions-card {
    padding: 2rem 2.5rem;
}

.suggestions-actions-header {
    text-align: center;
    margin-bottom: 2rem;
}

.suggestions-actions-header h5 {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.suggestions-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.suggestions-action-button {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(76, 175, 80, 0.1);
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

.suggestions-action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.6) 0%, rgba(46, 125, 50, 0.5) 100%);
    transition: all 0.3s ease;
    z-index: 0;
}

.suggestions-action-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.15);
    border-color: rgba(76, 175, 80, 0.5);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.suggestions-action-button:hover::before {
    left: 0;
}

.suggestions-action-button:hover .suggestions-action-icon,
.suggestions-action-button:hover .suggestions-action-content {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.9);
}

.suggestions-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--suggestions-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
    position: relative;
    z-index: 1;
}

.suggestions-action-content {
    position: relative;
    z-index: 1;
}

.suggestions-action-content h6 {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.suggestions-action-content small {
    color: var(--suggestions-text-muted);
    font-size: 0.9rem;
}

/* Suggestions List */
.suggestions-list-card {
    padding: 0;
}

.suggestions-list-header {
    padding: 2rem 2.5rem 1rem;
    border-bottom: 1px solid var(--suggestions-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.suggestions-list-header h4 {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.suggestions-list-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.suggestions-list-count {
    background: var(--suggestions-gradient-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.suggestions-filter-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(76, 175, 80, 0.1);
    color: var(--suggestions-primary-green);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(76, 175, 80, 0.2);
}

.suggestions-clear-filter {
    background: var(--suggestions-danger-color);
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

.suggestions-clear-filter:hover {
    transform: scale(1.1);
}

.suggestions-container {
    padding: 1rem 2.5rem 2.5rem;
}

.suggestions-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--suggestions-border-light);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: suggestionsSlideUp 0.6s ease forwards;
}

.suggestions-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--suggestions-gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.suggestions-item:hover::before {
    transform: scaleX(1);
}

.suggestions-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--suggestions-shadow-strong);
}

.suggestions-item.hidden {
    display: none;
}

.suggestions-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.suggestions-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.suggestions-id-badge {
    background: var(--suggestions-gradient-light);
    color: var(--suggestions-primary-green);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 1px solid rgba(76, 175, 80, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.suggestions-date {
    color: var(--suggestions-text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.suggestions-status-badge.suggestions-modern {
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

.suggestions-title {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    line-height: 1.3;
}

.suggestions-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.suggestions-meta-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.suggestions-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--suggestions-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--suggestions-primary-green);
    font-size: 0.9rem;
}

.suggestions-meta-content {
    display: flex;
    flex-direction: column;
}

.suggestions-meta-content small {
    color: var(--suggestions-text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.suggestions-meta-content span {
    color: var(--suggestions-text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}

.suggestions-preview {
    background: var(--suggestions-gradient-light);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--suggestions-primary-green);
    margin-bottom: 1.5rem;
}

.suggestions-preview p {
    color: var(--suggestions-text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.suggestions-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.suggestions-action-btn {
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

.suggestions-action-btn.suggestions-primary {
    background: var(--suggestions-gradient-primary);
    color: white;
}

.suggestions-action-btn.suggestions-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
    color: white;
    text-decoration: none;
}

.suggestions-action-btn.suggestions-secondary {
    background: rgba(76, 175, 80, 0.1);
    color: var(--suggestions-primary-green);
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.suggestions-action-btn.suggestions-secondary:hover {
    background: var(--suggestions-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
}

/* Status Colors */
.suggestions-status-received { background: var(--suggestions-warning-color) !important; }
.suggestions-status-reviewing { background: var(--suggestions-info-color) !important; }
.suggestions-status-replied { background: var(--suggestions-success-color) !important; }
.suggestions-status-closed { background: var(--suggestions-text-muted) !important; }

/* No Results */
.suggestions-no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--suggestions-text-muted);
}

.suggestions-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--suggestions-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--suggestions-primary-green);
}

.suggestions-no-results h5 {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.suggestions-no-results p {
    margin-bottom: 2rem;
}

/* Empty State */
.suggestions-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    position: relative;
}

.suggestions-empty-illustration {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.suggestions-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--suggestions-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: var(--suggestions-primary-green);
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(76, 175, 80, 0.2);
}

.suggestions-icon-decoration {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 35px;
    height: 35px;
    background: var(--suggestions-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
    border: 3px solid white;
}

.suggestions-empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.suggestions-circle {
    position: absolute;
    border: 2px solid rgba(76, 175, 80, 0.1);
    border-radius: 50%;
    animation: suggestionsPulse 2s infinite;
}

.suggestions-circle-1 {
    width: 160px;
    height: 160px;
    top: -80px;
    left: -80px;
    animation-delay: 0s;
}

.suggestions-circle-2 {
    width: 200px;
    height: 200px;
    top: -100px;
    left: -100px;
    animation-delay: 0.5s;
}

.suggestions-circle-3 {
    width: 240px;
    height: 240px;
    top: -120px;
    left: -120px;
    animation-delay: 1s;
}

.suggestions-empty-content h5 {
    color: var(--suggestions-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.suggestions-empty-content p {
    color: var(--suggestions-text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Animations */
@keyframes suggestionsShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes suggestionsAvatarShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes suggestionsSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes suggestionsPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 0.1;
    }
}

@keyframes suggestionsFadeIn {
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
    .suggestions-page-title {
        font-size: 2.2rem;
    }
    
    .suggestions-page-subtitle {
        font-size: 1rem;
    }
    
    .suggestions-user-info-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .suggestions-user-status {
        margin-left: 0;
    }
    
    .suggestions-stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .suggestions-stat-card {
        padding: 1.2rem 0.8rem;
    }
    
    .suggestions-stat-content h3 {
        font-size: 1.8rem;
    }
    
    .suggestions-stat-content p {
        font-size: 0.9rem;
    }
    
    .suggestions-stat-trend small {
        font-size: 0.7rem;
    }
    
    .suggestions-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .suggestions-list-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .suggestions-list-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .suggestions-container {
        padding: 1rem;
    }
    
    .suggestions-item {
        padding: 1.5rem;
    }
    
    .suggestions-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .suggestions-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .suggestions-actions {
        flex-direction: column;
    }
    
    .suggestions-action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .suggestions-stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .suggestions-user-avatar {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .suggestions-title {
        font-size: 1.2rem;
    }
}

@media print {
    .suggestions-action-btn,
    .suggestions-quick-actions-card {
        display: none !important;
    }
    
    .suggestions-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .suggestions-stat-card {
        cursor: default;
    }
}
</style>

<div class="suggestions-bg-pages">
    <div class="suggestions-container-pages-news">
        
        <!-- Page Header -->
        <div class="suggestions-page-header">
            <div class="suggestions-header-decoration"></div>
            <h1 class="suggestions-page-title">
                <i class="fas fa-lightbulb me-3"></i>
                ข้อเสนอแนะ และความคิดเห็นของฉัน
            </h1>
            <p class="suggestions-page-subtitle">ข้อเสนอแนะของคุณคือพลังขับเคลื่อนสำคัญ ที่จะนำชุมชนเราไปสู่การพัฒนาที่ยั่งยืน</p>
        </div>

        <!-- User Info Card -->
        <div class="suggestions-modern-card suggestions-user-info-card">
            <div class="suggestions-card-gradient-bg"></div>
            <div class="suggestions-user-info-content">
                <div class="suggestions-user-avatar">
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
                             class="suggestions-profile-image"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback เมื่อโหลดรูปไม่ได้ -->
                        <div class="suggestions-profile-fallback" style="display: none;">
                            <span class="suggestions-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php else: ?>
                        <!-- แสดง initials เมื่อไม่มีรูปโปรไฟล์ -->
                        <div class="suggestions-profile-fallback">
                            <span class="suggestions-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="suggestions-user-details">
                    <h4 class="suggestions-user-name">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php 
                        $full_name = trim($mp_prefix . ' ' . $mp_fname . ' ' . $mp_lname);
                        echo htmlspecialchars($full_name ?: 'ผู้ใช้'); 
                        ?>
                    </h4>
                    <p class="suggestions-user-email">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($user_info['email'] ?? $user_info['mp_email'] ?? ''); ?>
                    </p>
                </div>
                <div class="suggestions-user-status">
                    <span class="suggestions-status-active">
                        <i class="fas fa-check-circle me-1"></i>
                        สมาชิกที่ยืนยันแล้ว
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="suggestions-modern-card suggestions-stats-dashboard">
            <div class="suggestions-dashboard-header">
                <h4><i class="fas fa-chart-pie me-2"></i>สถิติข้อเสนอแนะ</h4>
                <span class="suggestions-dashboard-subtitle">คลิกเพื่อกรองรายการตามสถานะ</span>
            </div>
            
            <div class="suggestions-stats-grid">
                <div class="suggestions-stat-card suggestions-total suggestions-active" data-filter="all">
                    <div class="suggestions-stat-background"></div>
                    <div class="suggestions-stat-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="suggestions-stat-content">
                        <h3><?php echo $status_counts['total']; ?></h3>
                        <p>ทั้งหมด</p>
                        <div class="suggestions-stat-trend">
                            <small>ข้อเสนอแนะทั้งหมด</small>
                        </div>
                    </div>
                </div>
                
                <div class="suggestions-stat-card suggestions-received" data-filter="received">
                    <div class="suggestions-stat-background"></div>
                    <div class="suggestions-stat-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="suggestions-stat-content">
                        <h3><?php echo $status_counts['received']; ?></h3>
                        <p>เรื่องแนะนำส่งแล้ว</p>
                        <div class="suggestions-stat-trend">
                            <small>เรื่องแนะนำได้ทำการส่งแล้ว</small>
                        </div>
                    </div>
                </div>
                
                <div class="suggestions-stat-card suggestions-replied" data-filter="replied">
                    <div class="suggestions-stat-background"></div>
                    <div class="suggestions-stat-icon">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="suggestions-stat-content">
                        <h3><?php echo $status_counts['replied']; ?></h3>
                        <p>เจ้าหน้าที่ได้รับเรื่องแนะนำแล้ว</p>
                        <div class="suggestions-stat-trend">
                            <small>เจ้าหน้าที่ได้รับเรื่องแนะนำแล้ว</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="suggestions-modern-card suggestions-quick-actions-card">
            <div class="suggestions-actions-header">
                <h5><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
            </div>
            <div class="suggestions-actions-grid">
                <a href="<?php echo site_url('Suggestions/adding_suggestions'); ?>" class="suggestions-action-button">
                    <div class="suggestions-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="suggestions-action-content">
                        <h6>ส่งข้อเสนอแนะใหม่</h6>
                        <small>สร้างข้อเสนอแนะใหม่</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- Suggestions List -->
        <div class="suggestions-modern-card suggestions-list-card">
            <div class="suggestions-list-header">
                <h4><i class="fas fa-lightbulb me-2"></i>รายการข้อเสนอแนะ</h4>
                <div class="suggestions-list-controls">
                    <span class="suggestions-list-count" id="suggestions-count"><?php echo count($suggestions); ?> รายการ</span>
                    <div class="suggestions-filter-indicator" id="suggestions-filter-indicator">
                        <i class="fas fa-filter me-1"></i>
                        <span id="suggestions-filter-text">ทั้งหมด</span>
                       <!--  <button class="suggestions-clear-filter" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button> -->
                    </div>
                </div>
            </div>

            <?php if (!empty($suggestions)): ?>
                <div class="suggestions-container">
                    <?php foreach ($suggestions as $index => $suggestion): 
                        // ข้อมูลที่ประมวลผลแล้ว
                        $latest_status = $suggestion['status_display'];
                        $status_class = $suggestion['status_class'];
                        $status_icon = $suggestion['status_icon'];
                        $status_color = $suggestion['status_color'];
                        
                        // Format date เป็นรูปแบบไทย
                        $formatted_date = convertToThaiDate($suggestion['suggestions_datesave']);
                        
                        // Latest update เป็นรูปแบบไทย
                        $latest_update = '';
                        if ($suggestion['latest_update']) {
                            $latest_update = convertToThaiDate($suggestion['latest_update']);
                        }
                        
                        // Animation delay
                        $animation_delay = $index * 100;
                        
                        // Status mapping for filter - แก้ไขให้ตรงกับ suggestions_status จริง
                        $filter_status = 'all';
                        $actual_status = $suggestion['suggestions_status'] ?? 'received';
                        
                        switch ($actual_status) {
                            case 'received':
                                $filter_status = 'received';
                                break;
                            case 'reviewing':
                                $filter_status = 'reviewing';
                                break;
                            case 'replied':
                                $filter_status = 'replied';
                                break;
                            case 'closed':
                                $filter_status = 'closed';
                                break;
                            default:
                                $filter_status = 'received'; // default fallback
                                break;
                        }
                        
                        // Debug: แสดงข้อมูลการ map
                        if (ENVIRONMENT === 'development') {
                            error_log("Suggestion ID: {$suggestion['suggestions_id']}, actual_status: {$actual_status}, filter_status: {$filter_status}, status_class: {$status_class}");
                        }
                    ?>
                        <div class="suggestions-item" 
                             style="animation-delay: <?php echo $animation_delay; ?>ms;"
                             data-status="<?php echo $filter_status; ?>"
                             data-original-status="<?php echo htmlspecialchars($latest_status); ?>">
                            <div class="suggestions-header">
                                <div class="suggestions-id-section">
                                    <div class="suggestions-id-badge">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <?php echo htmlspecialchars($suggestion['suggestions_id']); ?>
                                    </div>
                                    <div class="suggestions-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        ส่งวันที่: <?php echo $formatted_date; ?>
                                    </div>
                                </div>
                                <div class="suggestions-status-section">
                                    <span class="suggestions-status-badge suggestions-modern <?php echo $status_class; ?>" style="background: <?php echo $status_color; ?>;">
                                        <i class="<?php echo $status_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($latest_status); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="suggestions-body">
                                <h5 class="suggestions-title">
                                    <?php echo htmlspecialchars($suggestion['suggestions_topic']); ?>
                                </h5>
                                
                                <div class="suggestions-meta">
                                    <div class="suggestions-meta-item">
                                        <div class="suggestions-meta-icon">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div class="suggestions-meta-content">
                                            <small>ประเภท</small>
                                            <span><?php echo htmlspecialchars($suggestion['type_display']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="suggestions-meta-item">
                                        <div class="suggestions-meta-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="suggestions-meta-content">
                                            <small>ผู้ส่ง</small>
                                            <span><?php echo htmlspecialchars($suggestion['suggestions_by']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($latest_update): ?>
                                    <div class="suggestions-meta-item">
                                        <div class="suggestions-meta-icon">
                                            <i class="fas fa-sync-alt"></i>
                                        </div>
                                        <div class="suggestions-meta-content">
                                            <small>อัพเดทล่าสุด</small>
                                            <span><?php echo $latest_update; ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="suggestions-preview">
                                    <p>
                                        <?php 
                                        $excerpt = mb_substr($suggestion['suggestions_detail'], 0, 150);
                                        echo nl2br(htmlspecialchars($excerpt));
                                        if (mb_strlen($suggestion['suggestions_detail']) > 150) echo '...';
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <div class="suggestions-actions">
                                <a href="<?php echo site_url('Suggestions/my_suggestion_detail/' . $suggestion['suggestions_id']); ?>" 
                                   class="suggestions-action-btn suggestions-primary">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                                <button onclick="copySuggestionId('<?php echo $suggestion['suggestions_id']; ?>')" 
                                        class="suggestions-action-btn suggestions-secondary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Results Message -->
                <div class="suggestions-no-results" id="suggestions-no-results" style="display: none;">
                    <div class="suggestions-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>ไม่พบรายการที่ตรงกับเงื่อนไข</h5>
                    <p>ลองเปลี่ยนตัวกรองหรือดูรายการทั้งหมด</p>
                    <button onclick="filterSuggestionsByStatus('all')" class="suggestions-action-btn suggestions-primary">
                        <i class="fas fa-list me-2"></i>ดูทั้งหมด
                    </button>
                </div>
                
            <?php else: ?>
                <!-- Empty State -->
                <div class="suggestions-empty-state">
                    <div class="suggestions-empty-illustration">
                        <div class="suggestions-empty-icon">
                            <i class="fas fa-lightbulb"></i>
                            <div class="suggestions-icon-decoration">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="suggestions-empty-circles">
                            <div class="suggestions-circle suggestions-circle-1"></div>
                            <div class="suggestions-circle suggestions-circle-2"></div>
                            <div class="suggestions-circle suggestions-circle-3"></div>
                        </div>
                    </div>
                    <div class="suggestions-empty-content">
                        <h5>ยังไม่มีข้อเสนอแนะ</h5>
                        <p>คุณยังไม่เคยส่งข้อเสนอแนะเข้ามาในระบบ<br>เริ่มต้นโดยการส่งข้อเสนอแนะแรกของคุณ</p>
                        <a href="<?php echo site_url('Suggestions/adding_suggestions'); ?>" class="suggestions-action-btn suggestions-primary suggestions-large">
                            <i class="fas fa-plus me-2"></i>ส่งข้อเสนอแนะแรก
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- โหลด jQuery ก่อนเสมอ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// *** รอให้ jQuery โหลดเสร็จก่อน ***
$(document).ready(function() {
    console.log('🚀 เริ่มต้นระบบข้อเสนอแนะ');
    
    // *** ตัวแปรสำคัญ ***
    let currentSuggestionsFilter = 'all';
    let originalStatusCounts = null;
    
    // *** บันทึกข้อมูลสถิติเดิม ***
    if (!originalStatusCounts) {
        originalStatusCounts = {
            total: parseInt($('.suggestions-stat-card.suggestions-total .suggestions-stat-content h3').text()) || 0,
            received: parseInt($('.suggestions-stat-card.suggestions-received .suggestions-stat-content h3').text()) || 0,
            replied: parseInt($('.suggestions-stat-card.suggestions-replied .suggestions-stat-content h3').text()) || 0
        };
        console.log('📊 สถิติเดิม:', originalStatusCounts);
    }
    
    // *** ฟังก์ชันกรองข้อเสนอแนะ ***
    window.filterSuggestionsByStatus = function(filter) {
        console.log('🔍 กรองสถิติ:', filter);
        
        currentSuggestionsFilter = filter;
        
        // *** เอาการอัปเดต active state ของ stat cards ***
        $('.suggestions-stat-card').removeClass('suggestions-active');
        $('.suggestions-stat-card[data-filter="' + filter + '"]').addClass('suggestions-active');
        
        // *** กรองรายการข้อเสนอแนะ ***
        const suggestionItems = $('.suggestions-item');
        const noResults = $('#suggestions-no-results');
        let visibleCount = 0;
        
        // *** ซ่อน/แสดงรายการ ***
        suggestionItems.each(function() {
            const $item = $(this);
            const itemStatus = $item.attr('data-status');
            
            if (filter === 'all' || itemStatus === filter) {
                $item.show().removeClass('hidden');
                visibleCount++;
                
                // Re-trigger animation
                $item.css('animation', 'none');
                setTimeout(() => {
                    $item.css('animation', 'suggestionsFadeIn 0.5s ease forwards');
                }, 10);
            } else {
                $item.hide().addClass('hidden');
            }
        });
        
        // *** อัปเดตจำนวนที่แสดง ***
        const countElement = $('#suggestions-count');
        if (countElement.length) {
            countElement.text(visibleCount + ' รายการ');
        }
        
        // *** อัปเดต filter indicator ***
        updateFilterIndicator(filter, visibleCount);
        
        // *** แสดง/ซ่อนข้อความไม่พบข้อมูล ***
        if (noResults.length) {
            if (visibleCount === 0 && filter !== 'all') {
                noResults.show().css('animation', 'suggestionsFadeIn 0.5s ease forwards');
            } else {
                noResults.hide();
            }
        }
        
        // *** Smooth scroll ***
        if (filter !== 'all') {
            const suggestionsList = $('.suggestions-list-card');
            if (suggestionsList.length) {
                suggestionsList.get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        //console.log('✅ กรองเสร็จ - แสดง:', visibleCount, 'รายการ');
    };
    
    // *** ฟังก์ชันอัปเดต filter indicator ***
    function updateFilterIndicator(filter, visibleCount) {
        const filterText = $('#suggestions-filter-text');
        const clearFilterBtn = $('.suggestions-clear-filter');
        
        if (filterText.length) {
            const filterNames = {
                'all': 'ทั้งหมด',
                'received': 'ได้รับแล้ว',
                'replied': 'ตอบกลับแล้ว',
                'closed': 'ปิดเรื่อง'
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
    
    // *** Copy Suggestion ID Function ***
    window.copySuggestionId = function(suggestionId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(suggestionId).then(() => {
                showSuggestionAlert('คัดลอกหมายเลข ' + suggestionId + ' สำเร็จ', 'success');
            }).catch(() => {
                fallbackCopySuggestionText(suggestionId);
            });
        } else {
            fallbackCopySuggestionText(suggestionId);
        }
    };
    
    function fallbackCopySuggestionText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showSuggestionAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
        } catch (err) {
            showSuggestionAlert('ไม่สามารถคัดลอกได้', 'error');
        }
        document.body.removeChild(textArea);
    }
    
    // *** Alert Functions ***
    function showSuggestionAlert(message, type) {
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
                'success': '#4caf50',
                'error': '#f44336',
                'warning': '#ff9800',
                'info': '#2196f3'
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
                animation: suggestionsAlertShow 0.3s ease;
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
                alertDiv.style.animation = 'suggestionsAlertHide 0.3s ease';
                setTimeout(() => alertDiv.remove(), 300);
            }, 4000);
        }
    }
    
    // *** Event Listeners สำหรับการ์ดสถิติ ***
    $('.suggestions-stat-card').off('click').on('click', function(e) {
        e.preventDefault();
        
        const filter = $(this).attr('data-filter');
        if (filter) {
            console.log('🎯 คลิกการ์ดสถิติ:', filter);
            filterSuggestionsByStatus(filter);
        }
    });
    
    // *** Event Listener สำหรับปุ่มล้างตัวกรอง ***
    $('.suggestions-clear-filter').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('🗑️ ล้างตัวกรอง');
        filterSuggestionsByStatus('all');
    });
    
    // *** Animate stats cards ***
    function animateStatsCards() {
        const statCards = $('.suggestions-stat-card');
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
        const actionButtons = $('.suggestions-action-button');
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
        const statCards = $('.suggestions-stat-card');
        
        statCards.each(function() {
            const $card = $(this);
            
            $card.on('mouseenter', function() {
                if (!$card.hasClass('suggestions-active')) {
                    $card.css('transform', 'translateY(-10px) scale(1.02)');
                }
            });
            
            $card.on('mouseleave', function() {
                if (!$card.hasClass('suggestions-active')) {
                    $card.css('transform', 'translateY(0) scale(1)');
                }
            });
            
            // Add click effect
            $card.on('mousedown', function() {
                $card.css('transform', 'scale(0.98)');
            });
            
            $card.on('mouseup', function() {
                setTimeout(() => {
                    const transform = $card.hasClass('suggestions-active') ? 
                        'translateY(-10px) scale(1.02)' : 'scale(1)';
                    $card.css('transform', transform);
                }, 150);
            });
        });
    }
    
    // *** เริ่มต้นด้วยการแสดงทั้งหมด ***
    filterSuggestionsByStatus('all');
    
    // *** เริ่มต้น animations ***
    animateStatsCards();
    animateActionButtons();
    addHoverEffects();
    
    //console.log('✅ ระบบข้อเสนอแนะพร้อมใช้งาน');
    
    // *** Debug function - เพิ่มฟังก์ชันตรวจสอบ ***
    window.debugSuggestionsStats = function() {
        console.log('🐛 DEBUG - สถิติข้อเสนอแนะ');
        console.log('📊 สถิติเดิม:', originalStatusCounts);
        console.log('🎯 ตัวกรองปัจจุบัน:', currentSuggestionsFilter);
        console.log('📋 จำนวนรายการทั้งหมด:', $('.suggestions-item').length);
        console.log('👀 รายการที่แสดง:', $('.suggestions-item:visible').length);
        console.log('🔢 การ์ดสถิติ:', {
            total: $('.suggestions-stat-card.suggestions-total .suggestions-stat-content h3').text(),
            received: $('.suggestions-stat-card.suggestions-received .suggestions-stat-content h3').text(),
            replied: $('.suggestions-stat-card.suggestions-replied .suggestions-stat-content h3').text()
        });
        
        // *** Debug data-status attributes ***
        console.log('🏷️ Data-Status รายการทั้งหมด:');
        $('.suggestions-item').each(function(index) {
            const $item = $(this);
            const itemId = $item.find('.suggestions-id-badge').text().trim();
            const itemStatus = $item.attr('data-status');
            const itemOriginalStatus = $item.attr('data-original-status');
            console.log(`   ${index + 1}. ID: ${itemId}, data-status: "${itemStatus}", original-status: "${itemOriginalStatus}"`);
        });
        
        // *** นับจำนวนจริงตาม data-status ***
        const actualCounts = {
            all: $('.suggestions-item').length,
            received: $('.suggestions-item[data-status="received"]').length,
            replied: $('.suggestions-item[data-status="replied"]').length,
            closed: $('.suggestions-item[data-status="closed"]').length
        };
        console.log('📊 จำนวนจริงตาม data-status:', actualCounts);
        
        // *** เปรียบเทียบกับสถิติที่แสดง ***
        const displayedCounts = {
            total: parseInt($('.suggestions-stat-card.suggestions-total .suggestions-stat-content h3').text()) || 0,
            received: parseInt($('.suggestions-stat-card.suggestions-received .suggestions-stat-content h3').text()) || 0,
            replied: parseInt($('.suggestions-stat-card.suggestions-replied .suggestions-stat-content h3').text()) || 0
        };
        
        console.log('⚖️ เปรียบเทียบ:');
        Object.keys(actualCounts).forEach(key => {
            if (displayedCounts.hasOwnProperty(key)) {
                const match = actualCounts[key] === displayedCounts[key];
                console.log(`   ${key}: จริง=${actualCounts[key]}, แสดง=${displayedCounts[key]}, ตรงกัน=${match ? '✅' : '❌'}`);
            }
        });
    };
});

// *** CSS Animation สำหรับ Alert ***
const alertAnimationCSS = `
@keyframes suggestionsAlertShow {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes suggestionsAlertHide {
    from {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
}
`;

// เพิ่ม CSS Animation เข้าไปใน head
const styleSheet = document.createElement('style');
styleSheet.textContent = alertAnimationCSS;
document.head.appendChild(styleSheet);
</script>