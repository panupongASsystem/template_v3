

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

:root {
    --complaint-primary-orange: #ff7849;
    --complaint-secondary-orange: #e55a2b;
    --complaint-light-orange: #ffeee8;
    --complaint-very-light-orange: #fff7f0;
    --complaint-success-color: #00B73E;
    --complaint-warning-color: #FFC700;
    --complaint-danger-color: #FF0202;
    --complaint-text-dark: #2c3e50;
    --complaint-text-muted: #6c757d;
    --complaint-border-light: rgba(255, 120, 73, 0.1);
    --complaint-shadow-light: 0 4px 20px rgba(255, 120, 73, 0.1);
    --complaint-shadow-medium: 0 8px 30px rgba(255, 120, 73, 0.15);
    --complaint-shadow-strong: 0 15px 40px rgba(255, 120, 73, 0.2);
    --complaint-gradient-primary: linear-gradient(135deg, #ff7849 0%, #e55a2b 100%);
    --complaint-gradient-light: linear-gradient(135deg, #fff7f0 0%, #ffeee8 100%);
    --complaint-gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
}

* {
    font-family: 'Kanit', sans-serif;
}

.complaint-bg-pages {
    background: #ffffff;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(255, 120, 73, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(229, 90, 43, 0.03) 0%, transparent 50%),
        linear-gradient(135deg, rgba(255, 120, 73, 0.01) 0%, transparent 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.complaint-container-pages-news {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Page Header */
.complaint-page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.complaint-header-decoration {
    width: 120px;
    height: 6px;
    background: var(--complaint-gradient-primary);
    margin: 0 auto 2rem;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.complaint-header-decoration::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: complaintShimmer 2s infinite;
}

.complaint-page-title {
    font-size: 3rem;
    font-weight: 600;
    background: var(--complaint-gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.complaint-page-subtitle {
    font-size: 1.2rem;
    color: var(--complaint-text-muted);
    margin-bottom: 0;
    font-weight: 400;
}

/* Modern Card */
.complaint-modern-card {
    background: var(--complaint-gradient-card);
    border-radius: 24px;
    box-shadow: var(--complaint-shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 120, 73, 0.08);
    z-index: 50; /* ไม่ให้ซ้อนทับกับ header */
}

.complaint-modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--complaint-gradient-primary);
    z-index: 1;
}

/* User Info Card */
.complaint-user-info-card {
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
}

.complaint-card-gradient-bg {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.05) 0%, transparent 70%);
    border-radius: 50% 0 0 50%;
}

.complaint-user-info-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.complaint-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--complaint-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.3);
    position: relative;
    overflow: hidden;
}

.complaint-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: complaintAvatarShine 3s infinite;
}

.complaint-avatar-status {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 28px;
    height: 28px;
    background: var(--complaint-success-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    box-shadow: 0 2px 8px rgba(0, 183, 62, 0.4);
    border: 3px solid white;
    z-index: 3;
}

.complaint-user-details {
    flex: 1;
}

.complaint-user-name {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.complaint-user-email {
    color: var(--complaint-text-muted);
    margin-bottom: 0;
    font-size: 1rem;
}

.complaint-user-status {
    margin-left: auto;
}

.complaint-status-active {
    background: linear-gradient(135deg, rgba(0, 183, 62, 0.1), rgba(0, 183, 62, 0.05));
    color: var(--complaint-success-color);
    padding: 0.8rem 1.5rem;
    border-radius: 15px;
    font-weight: 600;
    border: 1px solid rgba(0, 183, 62, 0.2);
    display: inline-flex;
    align-items: center;
}

/* Stats Dashboard */
.complaint-stats-dashboard {
    padding: 2.5rem;
}

.complaint-dashboard-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.complaint-dashboard-header h4 {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.complaint-dashboard-subtitle {
    color: var(--complaint-text-muted);
    font-size: 1rem;
}

.complaint-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.complaint-stat-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 120, 73, 0.1);
}

.complaint-stat-card.complaint-clickable {
    cursor: pointer;
    user-select: none;
}

.complaint-stat-card.complaint-clickable:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--complaint-shadow-strong);
}

.complaint-stat-card.complaint-active {
    border: 2px solid var(--complaint-primary-orange);
    background: rgba(255, 120, 73, 0.05);
}

.complaint-stat-card.complaint-active .complaint-click-indicator {
    opacity: 1;
    transform: scale(1);
}

.complaint-stat-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 1;
}

.complaint-stat-card.complaint-total .complaint-stat-background { background: var(--complaint-gradient-primary); }
.complaint-stat-card.complaint-pending .complaint-stat-background { background: linear-gradient(135deg, #FFC700, #FFB700); }
.complaint-stat-card.complaint-processing .complaint-stat-background { background: linear-gradient(135deg, #e55a2b, #d04423); }
.complaint-stat-card.complaint-completed .complaint-stat-background { background: linear-gradient(135deg, #00B73E, #009A33); }

.complaint-stat-icon {
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

.complaint-total .complaint-stat-icon {
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.15), rgba(255, 120, 73, 0.05));
    color: var(--complaint-primary-orange);
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.2);
}

.complaint-pending .complaint-stat-icon {
    background: linear-gradient(135deg, rgba(255, 199, 0, 0.15), rgba(255, 199, 0, 0.05));
    color: #B8860B;
    box-shadow: 0 8px 25px rgba(255, 199, 0, 0.2);
}

.complaint-processing .complaint-stat-icon {
    background: linear-gradient(135deg, rgba(229, 90, 43, 0.15), rgba(229, 90, 43, 0.05));
    color: var(--complaint-secondary-orange);
    box-shadow: 0 8px 25px rgba(229, 90, 43, 0.2);
}

.complaint-completed .complaint-stat-icon {
    background: linear-gradient(135deg, rgba(0, 183, 62, 0.15), rgba(0, 183, 62, 0.05));
    color: var(--complaint-success-color);
    box-shadow: 0 8px 25px rgba(0, 183, 62, 0.2);
}

.complaint-stat-content h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--complaint-text-dark);
}

.complaint-stat-content p {
    color: var(--complaint-text-dark);
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.complaint-stat-trend {
    color: var(--complaint-text-muted);
    font-size: 0.9rem;
}

.complaint-click-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    background: var(--complaint-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

/* Quick Actions */
.complaint-quick-actions-card {
    padding: 2rem 2.5rem;
}

.complaint-actions-header {
    text-align: center;
    margin-bottom: 2rem;
}

.complaint-actions-header h5 {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.complaint-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.complaint-action-button {
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(255, 120, 73, 0.1);
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

.complaint-action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--complaint-gradient-primary);
    transition: all 0.3s ease;
    z-index: 0;
}

.complaint-action-button::before {
    background: linear-gradient(135deg, rgba(255, 120, 73, 0.6) 0%, rgba(229, 90, 43, 0.5) 100%); /* จางมาก */
}

.complaint-action-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.15); /* shadow จางมาก */
    border-color: rgba(255, 120, 73, 0.5);
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.complaint-action-button:hover .complaint-action-icon,
.complaint-action-button:hover .complaint-action-content {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, 0.8);
}

.complaint-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--complaint-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 6px 20px rgba(255, 120, 73, 0.3);
    position: relative;
    z-index: 1;
}

.complaint-action-content {
    position: relative;
    z-index: 1;
}

.complaint-action-content h6 {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 0.3rem;
}

.complaint-action-content small {
    color: var(--complaint-text-muted);
    font-size: 0.9rem;
}

/* Complaints List */
.complaint-complaints-list-card {
    padding: 0;
}

.complaint-list-header {
    padding: 2rem 2.5rem 1rem;
    border-bottom: 1px solid var(--complaint-border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.complaint-list-header h4 {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 0;
}

.complaint-list-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.complaint-list-count {
    background: var(--complaint-gradient-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.complaint-filter-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 120, 73, 0.1);
    color: var(--complaint-primary-orange);
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(255, 120, 73, 0.2);
}

.complaint-clear-filter {
    background: var(--complaint-danger-color);
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

.complaint-clear-filter:hover {
    transform: scale(1.1);
}

.complaint-complaints-container {
    padding: 1rem 2.5rem 2.5rem;
}

.complaint-complaint-item {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--complaint-border-light);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: complaintSlideUp 0.6s ease forwards;
}

.complaint-complaint-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--complaint-gradient-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.complaint-complaint-item:hover::before {
    transform: scaleX(1);
}

.complaint-complaint-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--complaint-shadow-strong);
}

.complaint-complaint-item.hidden {
    display: none;
}

.complaint-complaint-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.complaint-complaint-id-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.complaint-id-badge {
    background: var(--complaint-gradient-light);
    color: var(--complaint-primary-orange);
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 1px solid rgba(255, 120, 73, 0.2);
    display: inline-flex;
    align-items: center;
    width: fit-content;
}

.complaint-complaint-type {
    color: var(--complaint-text-muted);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.complaint-status-badge.complaint-modern {
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

.complaint-complaint-title {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    line-height: 1.3;
}

.complaint-complaint-meta {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.complaint-meta-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.complaint-meta-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--complaint-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--complaint-primary-orange);
    font-size: 0.9rem;
}

.complaint-meta-content {
    display: flex;
    flex-direction: column;
}

.complaint-meta-content small {
    color: var(--complaint-text-muted);
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.complaint-meta-content span {
    color: var(--complaint-text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}

.complaint-complaint-preview {
    background: var(--complaint-gradient-light);
    padding: 1.5rem;
    border-radius: 16px;
    border-left: 4px solid var(--complaint-primary-orange);
    margin-bottom: 1.5rem;
}

.complaint-complaint-preview p {
    color: var(--complaint-text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.complaint-complaint-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.complaint-action-btn {
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

.complaint-action-btn.complaint-primary {
    background: var(--complaint-gradient-primary);
    color: white;
}

.complaint-action-btn.complaint-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.4);
    color: white;
    text-decoration: none;
}

.complaint-action-btn.complaint-secondary {
    background: rgba(255, 120, 73, 0.1);
    color: var(--complaint-primary-orange);
    border: 1px solid rgba(255, 120, 73, 0.3);
}

.complaint-action-btn.complaint-secondary:hover {
    background: var(--complaint-gradient-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.3);
}

.complaint-action-btn.complaint-large {
    padding: 1rem 2rem;
    font-size: 1rem;
}

/* No Results */
.complaint-no-results {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--complaint-text-muted);
}

.complaint-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--complaint-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--complaint-primary-orange);
}

.complaint-no-results h5 {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.complaint-no-results p {
    margin-bottom: 2rem;
}

/* Empty State */
.complaint-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    position: relative;
}

.complaint-empty-illustration {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.complaint-empty-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--complaint-gradient-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: var(--complaint-primary-orange);
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(255, 120, 73, 0.2);
}

.complaint-icon-decoration {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 35px;
    height: 35px;
    background: var(--complaint-gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(255, 120, 73, 0.4);
    border: 3px solid white;
}

.complaint-empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
}

.complaint-circle {
    position: absolute;
    border: 2px solid rgba(255, 120, 73, 0.1);
    border-radius: 50%;
    animation: complaintPulse 2s infinite;
}

.complaint-circle-1 {
    width: 160px;
    height: 160px;
    top: -80px;
    left: -80px;
    animation-delay: 0s;
}

.complaint-circle-2 {
    width: 200px;
    height: 200px;
    top: -100px;
    left: -100px;
    animation-delay: 0.5s;
}

.complaint-circle-3 {
    width: 240px;
    height: 240px;
    top: -120px;
    left: -120px;
    animation-delay: 1s;
}

.complaint-empty-content h5 {
    color: var(--complaint-text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.complaint-empty-content p {
    color: var(--complaint-text-muted);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Animations */
@keyframes complaintShimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes complaintAvatarShine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

@keyframes complaintSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes complaintPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 0.1;
    }
}

@keyframes complaintFadeIn {
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
    .complaint-page-title {
        font-size: 2.2rem;
    }
    
    .complaint-page-subtitle {
        font-size: 1rem;
    }
    
    .complaint-user-info-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .complaint-user-status {
        margin-left: 0;
    }
    
    .complaint-stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    
    .complaint-stat-card {
        padding: 1.2rem 0.8rem;
    }
    
    .complaint-stat-content h3 {
        font-size: 1.8rem;
    }
    
    .complaint-stat-content p {
        font-size: 0.9rem;
    }
    
    .complaint-stat-trend small {
        font-size: 0.7rem;
    }
    
    .complaint-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .complaint-list-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .complaint-list-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .complaint-complaints-container {
        padding: 1rem;
    }
    
    .complaint-complaint-item {
        padding: 1.5rem;
    }
    
    .complaint-complaint-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .complaint-complaint-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .complaint-complaint-actions {
        flex-direction: column;
    }
    
    .complaint-action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .complaint-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .complaint-user-avatar {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .complaint-complaint-title {
        font-size: 1.2rem;
    }
}

@media print {
    .complaint-action-btn,
    .complaint-quick-actions-card,
    .complaint-click-indicator {
        display: none !important;
    }
    
    .complaint-modern-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .complaint-stat-card {
        cursor: default;
    }
}
</style>

<style>
/* 
=================================================================
เพิ่ม CSS สำหรับรูปโปรไฟล์
=================================================================
*/

.complaint-profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.complaint-profile-image:hover {
    transform: scale(1.05);
}

.complaint-profile-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--complaint-gradient-primary);
    border-radius: 50%;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.complaint-profile-initials {
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 2;
    position: relative;
}

.complaint-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(255, 120, 73, 0.3);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.complaint-user-avatar:hover {
    box-shadow: 0 12px 35px rgba(255, 120, 73, 0.4);
    transform: translateY(-2px);
}

.complaint-user-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: complaintAvatarShine 3s infinite;
    z-index: 1;
}

/* ลบ CSS ของจุดเขียว status indicator */
/* 
.complaint-avatar-status {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 28px;
    height: 28px;
    background: var(--complaint-success-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    box-shadow: 0 2px 8px rgba(0, 183, 62, 0.4);
    border: 3px solid white;
    z-index: 10;
}
*/

/* Responsive สำหรับรูปโปรไฟล์ */
@media (max-width: 768px) {
    .complaint-user-avatar {
        width: 60px;
        height: 60px;
    }
    
    .complaint-profile-initials {
        font-size: 1.5rem;
    }
    
    /* ลบ responsive CSS ของจุดเขียว */
    /* 
    .complaint-avatar-status {
        width: 22px;
        height: 22px;
        font-size: 0.7rem;
    }
    */
}

@media (max-width: 576px) {
    .complaint-user-avatar {
        width: 50px;
        height: 50px;
    }
    
    .complaint-profile-initials {
        font-size: 1.2rem;
    }
    
    /* ลบ responsive CSS ของจุดเขียว */
    /* 
    .complaint-avatar-status {
        width: 18px;
        height: 18px;
        font-size: 0.6rem;
    }
    */
}

/* เพิ่ม animation สำหรับ profile loading */
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

.complaint-profile-image {
    animation: profileLoad 0.6s ease-out;
}

.complaint-profile-fallback {
    animation: profileLoad 0.6s ease-out;
}

/* เพิ่ม hover effect สำหรับ initials */
.complaint-profile-fallback:hover .complaint-profile-initials {
    transform: scale(1.1);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

/* Gradient border สำหรับ avatar */
.complaint-user-avatar {
    background: var(--complaint-gradient-primary);
    padding: 3px;
}

.complaint-user-avatar .complaint-profile-image,
.complaint-user-avatar .complaint-profile-fallback {
    border-radius: 50%;
}
</style>




<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ตรวจสอบ login
if (!$this->session->userdata('mp_id')) {
    redirect('User');
    return;
}
?>

<div class="complaint-bg-pages">
    <div class="complaint-container-pages-news">
        
        <!-- Page Header -->
        <div class="complaint-page-header">
            <div class="complaint-header-decoration"></div>
            <h1 class="complaint-page-title">
                <i class="fas fa-clipboard-list me-3"></i>
                สถานะแจ้งเรื่อง ร้องเรียนของฉัน
            </h1>
            <p class="complaint-page-subtitle">ติดตามและจัดการแจ้งเรื่อง ร้องเรียนของคุณได้อย่างสะดวก</p>
        </div>

        <!-- User Info Card -->
        <div class="complaint-modern-card complaint-user-info-card">
    <div class="complaint-card-gradient-bg"></div>
    <div class="complaint-user-info-content">
        <div class="complaint-user-avatar">
            <?php 
            // *** เพิ่ม: ระบบจัดการรูปโปรไฟล์ ***
            $profile_img = $user_info['mp_img'];
            $mp_fname = $user_info['mp_fname'];
            $mp_lname = $user_info['mp_lname'];
            $mp_prefix = $user_info['mp_prefix'];
            
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
                     class="complaint-profile-image"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <!-- Fallback เมื่อโหลดรูปไม่ได้ -->
                <div class="complaint-profile-fallback" style="display: none;">
                    <span class="complaint-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                </div>
            <?php else: ?>
                <!-- แสดง initials เมื่อไม่มีรูปโปรไฟล์ -->
                <div class="complaint-profile-fallback">
                    <span class="complaint-profile-initials"><?php echo htmlspecialchars($initials); ?></span>
                </div>
            <?php endif; ?>
            
            <!-- ลบจุดเขียว status indicator ออก -->
            <!-- 
            <div class="complaint-avatar-status">
                <i class="fas fa-check"></i>
            </div>
            -->
        </div>
        <div class="complaint-user-details">
            <h4 class="complaint-user-name">
                <i class="fas fa-shield-alt me-2"></i>
                <?php 
                $full_name = trim($mp_prefix . ' ' . $mp_fname . ' ' . $mp_lname);
                echo htmlspecialchars($full_name ?: 'ผู้ใช้'); 
                ?>
            </h4>
            <p class="complaint-user-email">
                <i class="fas fa-envelope me-2"></i>
                <?php echo htmlspecialchars($user_info['mp_email']); ?>
            </p>
        </div>
        <div class="complaint-user-status">
            <span class="complaint-status-active">
                <i class="fas fa-check-circle me-1"></i>
                สมาชิกที่ยืนยันแล้ว
            </span>
        </div>
    </div>
</div>

		
		

        <!-- Statistics Dashboard -->
        <div class="complaint-modern-card complaint-stats-dashboard">
            <div class="complaint-dashboard-header">
                <h4><i class="fas fa-chart-pie me-2"></i>สถิติแจ้งเรื่อง ร้องเรียน</h4>
                <span class="complaint-dashboard-subtitle">คลิกเพื่อกรองรายการตามสถานะ</span>
            </div>
            
            <div class="complaint-stats-grid">
                <div class="complaint-stat-card complaint-clickable complaint-total complaint-active" onclick="filterComplaintsByStatus('all')" data-filter="all">
                    <div class="complaint-stat-background"></div>
                    <div class="complaint-stat-icon">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div class="complaint-stat-content">
                        <h3><?php echo $status_counts['total']; ?></h3>
                        <p>ทั้งหมด</p>
                        <div class="complaint-stat-trend">
                            <small>แจ้งเรื่อง ร้องเรียนทั้งหมด</small>
                        </div>
                    </div>
                    <div class="complaint-click-indicator">
                        <i class="fas fa-hand-pointer"></i>
                    </div>
                </div>
                
                <div class="complaint-stat-card complaint-clickable complaint-pending" onclick="filterComplaintsByStatus('pending')" data-filter="pending">
                    <div class="complaint-stat-background"></div>
                    <div class="complaint-stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="complaint-stat-content">
                        <h3><?php echo $status_counts['pending']; ?></h3>
                        <p>รอรับเรื่อง/รับเรื่องแล้ว</p>
                        <div class="complaint-stat-trend">
                            <small>กำลังรอการตรวจสอบ</small>
                        </div>
                    </div>
                    <div class="complaint-click-indicator">
                        <i class="fas fa-hand-pointer"></i>
                    </div>
                </div>
                
                <div class="complaint-stat-card complaint-clickable complaint-processing" onclick="filterComplaintsByStatus('processing')" data-filter="processing">
                    <div class="complaint-stat-background"></div>
                    <div class="complaint-stat-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="complaint-stat-content">
                        <h3><?php echo $status_counts['processing']; ?></h3>
                        <p>กำลังดำเนินการ</p>
                        <div class="complaint-stat-trend">
                            <small>อยู่ระหว่างการแก้ไข</small>
                        </div>
                    </div>
                    <div class="complaint-click-indicator">
                        <i class="fas fa-hand-pointer"></i>
                    </div>
                </div>
                
                <div class="complaint-stat-card complaint-clickable complaint-completed" onclick="filterComplaintsByStatus('completed')" data-filter="completed">
                    <div class="complaint-stat-background"></div>
                    <div class="complaint-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="complaint-stat-content">
                        <h3><?php echo $status_counts['completed']; ?></h3>
                        <p>เสร็จสิ้น</p>
                        <div class="complaint-stat-trend">
                            <small>ดำเนินการเรียบร้อย</small>
                        </div>
                    </div>
                    <div class="complaint-click-indicator">
                        <i class="fas fa-hand-pointer"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="complaint-modern-card complaint-quick-actions-card">
            <div class="complaint-actions-header">
                <h5><i class="fas fa-bolt me-2"></i>การดำเนินการด่วน</h5>
            </div>
            <div class="complaint-actions-grid">
                <a href="<?php echo site_url('Pages/adding_complain'); ?>" class="complaint-action-button complaint-new-complaint">
                    <div class="complaint-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="complaint-action-content">
                        <h6>แจ้งเรื่องใหม่</h6>
                        <small>สร้างแจ้งเรื่อง ร้องเรียนใหม่</small>
                    </div>
                </a>
                
                
            </div>
        </div>

        <!-- Complaints List -->
        <div class="complaint-modern-card complaint-complaints-list-card">
            <div class="complaint-list-header">
                <h4><i class="fas fa-list-ul me-2"></i>รายการแจ้งเรื่อง ร้องเรียน</h4>
                <div class="complaint-list-controls">
                    <span class="complaint-list-count" id="complaint-count"><?php echo count($complaints); ?> รายการ</span>
                    <div class="complaint-filter-indicator" id="complaint-filter-indicator">
                        <i class="fas fa-filter me-1"></i>
                        <span id="complaint-filter-text">ทั้งหมด</span>
                        <button class="complaint-clear-filter" onclick="filterComplaintsByStatus('all')" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <?php if (!empty($complaints)): ?>
                <div class="complaint-complaints-container">
                    <?php foreach ($complaints as $index => $complaint): 
                        // ข้อมูลได้ประมวลผลจาก controller แล้ว
                        $latest_status = $complaint['latest_status_display'];
                        $status_class = $complaint['status_class'];
                        $status_icon = $complaint['status_icon'];
                        $status_color = $complaint['status_color'];
                        
                        // Format date
                        $date = new DateTime($complaint['complain_datesave']);
                        $formatted_date = $date->format('d/m/Y H:i');
                        
                        // Latest update
                        $latest_update = '';
                        if ($complaint['latest_update']) {
                            $update_date = new DateTime($complaint['latest_update']);
                            $latest_update = $update_date->format('d/m/Y H:i');
                        }
                        
                        // Animation delay
                        $animation_delay = $index * 100;
                        
                        // Status mapping for filter
                        $filter_status = 'all';
                        switch ($status_class) {
                            case 'complaint-status-pending':
                                $filter_status = 'pending';
                                break;
                            case 'complaint-status-processing':
                                $filter_status = 'processing';
                                break;
                            case 'complaint-status-completed':
                                $filter_status = 'completed';
                                break;
                            case 'complaint-status-cancelled':
                                $filter_status = 'cancelled';
                                break;
                        }
                    ?>
                        <div class="complaint-complaint-item" 
                             style="animation-delay: <?php echo $animation_delay; ?>ms;"
                             data-status="<?php echo $filter_status; ?>"
                             data-original-status="<?php echo htmlspecialchars($latest_status); ?>">
                            <div class="complaint-complaint-header">
                                <div class="complaint-complaint-id-section">
                                    <div class="complaint-id-badge">
                                        <i class="fas fa-hashtag me-1"></i>
                                        <?php echo htmlspecialchars($complaint['complain_id']); ?>
                                    </div>
                                    <div class="complaint-complaint-type">
                                        <i class="fas fa-tags me-1"></i>
                                        <?php echo htmlspecialchars($complaint['complain_type']); ?>
                                    </div>
                                </div>
                                <div class="complaint-complaint-status-section">
                                    <span class="complaint-status-badge complaint-modern" style="background: <?php echo $status_color; ?>;">
                                        <i class="<?php echo $status_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($latest_status); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="complaint-complaint-body">
                                <h5 class="complaint-complaint-title">
                                    <?php echo htmlspecialchars($complaint['complain_topic']); ?>
                                </h5>
                                
                                <div class="complaint-complaint-meta">
                                    <div class="complaint-meta-item">
                                        <div class="complaint-meta-icon">
                                            <i class="fas fa-calendar-plus"></i>
                                        </div>
                                        <div class="complaint-meta-content">
                                            <small>แจ้งเมื่อ</small>
                                            <span><?php echo $formatted_date; ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($latest_update): ?>
                                    <div class="complaint-meta-item">
                                        <div class="complaint-meta-icon">
                                            <i class="fas fa-sync-alt"></i>
                                        </div>
                                        <div class="complaint-meta-content">
                                            <small>อัพเดทล่าสุด</small>
                                            <span><?php echo $latest_update; ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="complaint-complaint-preview">
                                    <p>
                                        <?php 
                                        $excerpt = mb_substr($complaint['complain_detail'], 0, 150);
                                        echo nl2br(htmlspecialchars($excerpt));
                                        if (mb_strlen($complaint['complain_detail']) > 150) echo '...';
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <div class="complaint-complaint-actions">
                                <a href="<?php echo site_url('complaints_public/detail/' . $complaint['complain_id']); ?>" 
                                   class="complaint-action-btn complaint-primary">
                                    <i class="fas fa-eye me-2"></i>ดูรายละเอียด
                                </a>
                                <button onclick="copyComplaintId('<?php echo $complaint['complain_id']; ?>')" 
                                        class="complaint-action-btn complaint-secondary">
                                    <i class="fas fa-copy me-2"></i>คัดลอกหมายเลข
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- No Results Message -->
                <div class="complaint-no-results" id="complaint-no-results" style="display: none;">
                    <div class="complaint-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5>ไม่พบรายการที่ตรงกับเงื่อนไข</h5>
                    <p>ลองเปลี่ยนตัวกรองหรือดูรายการทั้งหมด</p>
                    <button onclick="filterComplaintsByStatus('all')" class="complaint-action-btn complaint-primary">
                        <i class="fas fa-list me-2"></i>ดูทั้งหมด
                    </button>
                </div>
                
            <?php else: ?>
                <!-- Empty State -->
                <div class="complaint-empty-state">
                    <div class="complaint-empty-illustration">
                        <div class="complaint-empty-icon">
                            <i class="fas fa-inbox"></i>
                            <div class="complaint-icon-decoration">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="complaint-empty-circles">
                            <div class="complaint-circle complaint-circle-1"></div>
                            <div class="complaint-circle complaint-circle-2"></div>
                            <div class="complaint-circle complaint-circle-3"></div>
                        </div>
                    </div>
                    <div class="complaint-empty-content">
                        <h5>ยังไม่มีเรื่องร้องเรียน</h5>
                        <p>คุณยังไม่เคยแจ้งเรื่องร้องเรียนเข้ามาในระบบ<br>เริ่มต้นโดยการแจ้งเรื่องแรกของคุณ</p>
                        <a href="<?php echo site_url('Pages/adding_complain'); ?>" class="complaint-action-btn complaint-primary complaint-large">
                            <i class="fas fa-plus me-2"></i>แจ้งเรื่องแรก
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>






<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Current filter state
let currentComplaintFilter = 'all';

// Filter complaints function
function filterComplaintsByStatus(filter) {
    currentComplaintFilter = filter;
    
    // Update active state of stat cards
    document.querySelectorAll('.complaint-stat-card').forEach(card => {
        card.classList.remove('complaint-active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('complaint-active');
    
    // Filter complaint items
    const complaintItems = document.querySelectorAll('.complaint-complaint-item');
    const noResults = document.getElementById('complaint-no-results');
    let visibleCount = 0;
    
    complaintItems.forEach(item => {
        const itemStatus = item.getAttribute('data-status');
        if (filter === 'all' || itemStatus === filter) {
            item.style.display = 'block';
            item.classList.remove('hidden');
            visibleCount++;
            
            // Re-trigger animation
            item.style.animation = 'none';
            setTimeout(() => {
                item.style.animation = 'complaintFadeIn 0.5s ease forwards';
            }, 10);
        } else {
            item.style.display = 'none';
            item.classList.add('hidden');
        }
    });
    
    // Update complaint count
    const countElement = document.getElementById('complaint-count');
    if (countElement) {
        countElement.textContent = `${visibleCount} รายการ`;
    }
    
    // Update filter indicator
    const filterText = document.getElementById('complaint-filter-text');
    const clearFilterBtn = document.querySelector('.complaint-clear-filter');
    
    if (filterText) {
        const filterNames = {
            'all': 'ทั้งหมด',
            'pending': 'รอดำเนินการ',
            'processing': 'กำลังดำเนินการ',
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
            noResults.style.animation = 'complaintFadeIn 0.5s ease forwards';
        } else {
            noResults.style.display = 'none';
        }
    }
    
    // Add smooth scroll to complaints list
    if (filter !== 'all') {
        const complaintsList = document.querySelector('.complaint-complaints-list-card');
        if (complaintsList) {
            complaintsList.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}

// Copy Complain ID Function
function copyComplaintId(complainId) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(complainId).then(() => {
            showComplaintAlert('คัดลอกหมายเลข ' + complainId + ' สำเร็จ', 'success');
        }).catch(() => {
            fallbackCopyComplaintText(complainId);
        });
    } else {
        fallbackCopyComplaintText(complainId);
    }
}

function fallbackCopyComplaintText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showComplaintAlert('คัดลอกหมายเลข ' + text + ' สำเร็จ', 'success');
    } catch (err) {
        showComplaintAlert('ไม่สามารถคัดลอกได้', 'error');
    }
    document.body.removeChild(textArea);
}

function showComplaintAlert(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: message,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        // Enhanced fallback alert
        const alertDiv = document.createElement('div');
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#00B73E' : '#FF0202'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 15000;
            font-family: 'Kanit', sans-serif;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: complaintSlideInRight 0.3s ease;
        `;
        
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        alertDiv.innerHTML = `<i class="${icon}"></i> ${message}`;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.style.animation = 'complaintSlideOutRight 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }
}

// Initialize page animations
document.addEventListener('DOMContentLoaded', function() {
    // Check Font Awesome loading
    setTimeout(() => {
        const testIcon = document.createElement('i');
        testIcon.className = 'fas fa-check';
        testIcon.style.position = 'absolute';
        testIcon.style.left = '-9999px';
        document.body.appendChild(testIcon);
        
        const computedStyle = window.getComputedStyle(testIcon);
        const fontFamily = computedStyle.fontFamily;
        
        console.log('Font Awesome Status:', fontFamily.includes('Font Awesome') ? 'Loaded' : 'Not Loaded');
        document.body.removeChild(testIcon);
    }, 1000);
    
    // Animate stats cards
    const statCards = document.querySelectorAll('.complaint-stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animate action buttons
    const actionButtons = document.querySelectorAll('.complaint-action-button');
    actionButtons.forEach((button, index) => {
        button.style.opacity = '0';
        button.style.transform = 'translateX(-30px)';
        
        setTimeout(() => {
            button.style.transition = 'all 0.6s ease';
            button.style.opacity = '1';
            button.style.transform = 'translateX(0)';
        }, 300 + (index * 150));
    });
    
    // Add enhanced hover effects for stat cards
    statCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            if (!card.classList.contains('complaint-active')) {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            }
        });
        
        card.addEventListener('mouseleave', () => {
            if (!card.classList.contains('complaint-active')) {
                card.style.transform = 'translateY(0) scale(1)';
            }
        });
        
        // Add click effect
        card.addEventListener('click', () => {
            card.style.transform = 'scale(0.98)';
            setTimeout(() => {
                card.style.transform = card.classList.contains('complaint-active') ? 'translateY(-10px) scale(1.02)' : 'scale(1)';
            }, 150);
        });
    });
    
    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Initialize complaint count
    const initialCount = document.querySelectorAll('.complaint-complaint-item').length;
    const countElement = document.getElementById('complaint-count');
    if (countElement) {
        countElement.textContent = `${initialCount} รายการ`;
    }
});

// Add scroll animations
const observeComplaintElements = () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.complaint-modern-card').forEach(card => {
        observer.observe(card);
    });
};

// Initialize scroll animations after a short delay
setTimeout(observeComplaintElements, 500);

// Add CSS animations for alerts
const complaintAlertStyle = document.createElement('style');
complaintAlertStyle.textContent = `
    @keyframes complaintSlideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes complaintSlideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(complaintAlertStyle);
</script>



<script>
/*
=================================================================
เพิ่ม JavaScript สำหรับจัดการรูปโปรไฟล์
=================================================================
*/

document.addEventListener('DOMContentLoaded', function() {
    // จัดการการโหลดรูปโปรไฟล์
    const profileImages = document.querySelectorAll('.complaint-profile-image');
    
    profileImages.forEach(img => {
        // เพิ่ม loading effect
        img.addEventListener('load', function() {
            this.style.opacity = '0';
            this.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                this.style.transition = 'all 0.3s ease';
                this.style.opacity = '1';
                this.style.transform = 'scale(1)';
            }, 100);
        });
        
        // จัดการ error
        img.addEventListener('error', function() {
            console.log('Profile image failed to load:', this.src);
            this.style.display = 'none';
            
            const fallback = this.nextElementSibling;
            if (fallback && fallback.classList.contains('complaint-profile-fallback')) {
                fallback.style.display = 'flex';
                
                // เพิ่ม animation สำหรับ fallback
                fallback.style.opacity = '0';
                fallback.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    fallback.style.transition = 'all 0.3s ease';
                    fallback.style.opacity = '1';
                    fallback.style.transform = 'scale(1)';
                }, 100);
            }
        });
    });
    
    // เพิ่ม hover effect สำหรับ avatar
    const avatars = document.querySelectorAll('.complaint-user-avatar');
    avatars.forEach(avatar => {
        avatar.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        avatar.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Function สำหรับอัพเดทรูปโปรไฟล์ (เผื่อใช้ในอนาคต)
function updateProfileImage(newImageSrc) {
    const profileImg = document.querySelector('.complaint-profile-image');
    const fallback = document.querySelector('.complaint-profile-fallback');
    
    if (profileImg && newImageSrc) {
        profileImg.src = newImageSrc;
        profileImg.style.display = 'block';
        if (fallback) {
            fallback.style.display = 'none';
        }
    }
}
</script>
