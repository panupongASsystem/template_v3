/* ========================================
   Reports System CSS
   ======================================== */

/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary-color: #3b82f6;
    --primary-dark: #1d4ed8;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    --light-gray: #f8fafc;
    --medium-gray: #e2e8f0;
    --dark-gray: #334155;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-radius: 12px;
    --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --box-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    font-family: 'Kanit', sans-serif;
    background-color: #ffffff;
    color: var(--text-primary);
    line-height: 1.6;
    overflow-x: hidden;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.text-primary { color: var(--primary-color) !important; }
.text-secondary { color: var(--text-secondary) !important; }
.text-success { color: var(--success-color) !important; }
.text-warning { color: var(--warning-color) !important; }
.text-danger { color: var(--danger-color) !important; }
.text-info { color: var(--info-color) !important; }

/* Layout */
.page-wrapper {
    min-height: 100vh;
    padding-top: 85px;
    padding-bottom: 60px;
    display: flex;
    flex-direction: column;
}

/* Navigation */
.navbar {
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--medium-gray);
    transition: var(--transition);
}

.navbar-brand {
    text-decoration: none;
}

.brand-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1;
}

.brand-subtitle {
    font-size: 0.8rem;
    color: var(--text-secondary);
    line-height: 1;
}

.nav-link {
    color: var(--text-secondary) !important;
    font-weight: 500;
    padding: 0.5rem 1rem !important;
    border-radius: 8px;
    margin: 0 0.25rem;
    transition: var(--transition);
}

.nav-link:hover {
    color: var(--primary-color) !important;
    background-color: rgba(59, 130, 246, 0.1);
}

.nav-link.active {
    color: var(--primary-color) !important;
    background-color: rgba(59, 130, 246, 0.1);
    font-weight: 600;
}

.user-info {
    text-align: left;
}

.user-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1;
}

.user-role {
    font-size: 0.75rem;
    color: var(--text-secondary);
    line-height: 1;
}

.dropdown-menu {
    border: none;
    box-shadow: var(--box-shadow-lg);
    border-radius: var(--border-radius);
    margin-top: 0.5rem;
}

.dropdown-item {
    padding: 0.75rem 1.25rem;
    font-weight: 500;
    transition: var(--transition);
}

.dropdown-item:hover {
    background-color: var(--light-gray);
    color: var(--primary-color);
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--box-shadow);
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    color: white;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 10px 0 0 0;
}

.page-header .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: var(--transition);
}

.page-header .breadcrumb-item a:hover {
    color: white;
}

.page-header .breadcrumb-item.active {
    color: rgba(255, 255, 255, 1);
}

/* Cards */
.card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: var(--box-shadow-lg);
}

.card-header {
    background-color: var(--light-gray);
    border-bottom: 1px solid var(--medium-gray);
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    padding: 1.25rem;
}

.card-body {
    padding: 1.5rem;
}

/* Stats Cards */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--box-shadow-lg);
}

.stat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: var(--box-shadow);
}

.stat-icon.primary { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); }
.stat-icon.success { background: linear-gradient(135deg, var(--success-color), #059669); }
.stat-icon.warning { background: linear-gradient(135deg, var(--warning-color), #d97706); }
.stat-icon.danger { background: linear-gradient(135deg, var(--danger-color), #dc2626); }
.stat-icon.info { background: linear-gradient(135deg, var(--info-color), #0891b2); }

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 0.9rem;
    font-weight: 500;
}

.stat-change {
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
}

.stat-change.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.stat-change.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

/* Chart Cards */
.chart-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    margin-bottom: 2rem;
}

.chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--medium-gray);
    padding-bottom: 1rem;
}

.chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.chart-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-chart {
    padding: 0.5rem 1rem;
    border: 1px solid var(--medium-gray);
    background: white;
    border-radius: 8px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: var(--transition);
    color: var(--text-secondary);
    font-weight: 500;
}

.btn-chart:hover {
    background: var(--light-gray);
    border-color: #cbd5e1;
    color: var(--text-primary);
}

.btn-chart.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Tables */
.table-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
}

.table-card .table {
    margin: 0;
}

.table-card .table thead th {
    background: var(--light-gray);
    border: none;
    font-weight: 600;
    color: var(--text-primary);
    padding: 1rem;
    border-bottom: 2px solid var(--medium-gray);
}

.table-card .table tbody td {
    padding: 1rem;
    border-color: #f1f5f9;
    vertical-align: middle;
}

.table-card .table tbody tr:hover {
    background-color: rgba(59, 130, 246, 0.02);
}

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.normal {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.status-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.status-badge.critical {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

/* Buttons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 1.25rem;
    transition: var(--transition);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-dark), #1e40af);
    transform: translateY(-1px);
}

.btn-outline-primary {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

/* Forms */
.form-control {
    border-radius: 8px;
    border: 1px solid var(--medium-gray);
    padding: 0.75rem;
    transition: var(--transition);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.form-select {
    border-radius: 8px;
    border: 1px solid var(--medium-gray);
    padding: 0.75rem;
}

.input-group {
    border-radius: 8px;
    overflow: hidden;
}

/* Footer */
.footer {
    background-color: white;
    border-top: 1px solid var(--medium-gray);
    padding: 1.5rem 0;
    margin-top: auto;
}

.footer-info {
    color: var(--text-secondary);
}

.footer-links {
    color: var(--text-secondary);
}

.system-status {
    font-size: 0.8rem;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.status-indicator.online {
    background-color: var(--success-color);
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
}

.status-indicator.offline {
    background-color: var(--danger-color);
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3);
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.9);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid var(--medium-gray);
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

.loading-text {
    color: var(--text-secondary);
    font-weight: 500;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Alert Container */
.alert-container {
    position: fixed;
    top: 100px;
    right: 20px;
    z-index: 1050;
    max-width: 400px;
}

.custom-alert {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow-lg);
    backdrop-filter: blur(10px);
}

/* Back to Top Button */
.btn-back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    transition: var(--transition);
    z-index: 1000;
    display: none;
    box-shadow: var(--box-shadow);
}

.btn-back-to-top:hover {
    transform: translateY(-2px);
    box-shadow: var(--box-shadow-lg);
}

/* Storage Specific Styles */
.storage-usage-circle {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto 2rem;
}

.usage-percentage {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.usage-percentage .value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.usage-percentage .label {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.storage-breakdown {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 1.5rem;
}

.breakdown-item {
    text-align: center;
    padding: 1rem;
    background: var(--light-gray);
    border-radius: var(--border-radius);
}

.breakdown-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.breakdown-label {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

.file-types-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.file-type-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.file-type-item:last-child {
    border-bottom: none;
}

.file-type-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.file-type-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
}

.file-type-icon.images { background: var(--primary-color); }
.file-type-icon.documents { background: var(--success-color); }
.file-type-icon.videos { background: var(--warning-color); }
.file-type-icon.others { background: var(--secondary-color); }

.file-type-details h6 {
    margin: 0;
    font-weight: 600;
    color: var(--text-primary);
}

.file-type-details small {
    color: var(--text-secondary);
}

.file-type-size {
    font-weight: 600;
    color: var(--text-primary);
}

/* Index Page Specific Styles */
.index-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.index-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-top: 1rem;
}

.index-logo {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    box-shadow: var(--box-shadow);
    display: block;
    background: white;
    padding: 5px;
}

.index-title {
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.index-subtitle {
    font-size: 1.25rem;
    color: var(--text-secondary);
    font-weight: 300;
    margin-bottom: 0.5rem;
}

.breadcrumb-nav {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
}

.breadcrumb-nav a {
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.9rem;
    transition: var(--transition);
}

.breadcrumb-nav a:hover {
    color: var(--primary-color);
}

.user-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem 2rem;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

.user-info-index {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--medium-gray);
    box-shadow: var(--box-shadow);
}

.user-details h5 {
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.user-details p {
    color: var(--text-secondary);
    margin: 0;
    font-size: 0.9rem;
}

.back-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.5rem;
    background: linear-gradient(135deg, var(--secondary-color), #475569);
    color: white;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    box-shadow: var(--box-shadow);
}

.back-button:hover {
    background: linear-gradient(135deg, #475569, var(--dark-gray));
    transform: translateY(-2px);
    box-shadow: var(--box-shadow-lg);
    color: white;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.summary-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--box-shadow-lg);
}

.summary-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.summary-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.summary-icon.storage {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
}

.summary-icon.complain {
    background: linear-gradient(135deg, var(--warning-color), #d97706);
}

.summary-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
}

.summary-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 0.75rem;
    background: rgba(248, 250, 252, 0.8);
    border-radius: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.stat-label {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin: 0 auto;
    max-width: 1200px;
}

.card-wrapper {
    position: relative;
    height: 280px;
    width: 100%;
}

.card-index {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    border-radius: 1.5rem;
    padding: 2rem;
    text-align: center;
    transition: var(--transition);
    text-decoration: none;
    color: inherit;
    box-shadow: var(--box-shadow);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.card-index::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 1.5rem;
    transition: var(--transition);
    opacity: 0.7;
}

.card-storage::before { 
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(29, 78, 216, 0.1)); 
}
.card-complain::before { 
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(217, 119, 6, 0.1)); 
}

.card-index:hover {
    transform: translateY(-8px);
    box-shadow: var(--box-shadow-lg);
    text-decoration: none;
}

.card-index:hover::before {
    opacity: 1;
}

.card-index:hover .icon-circle {
    transform: scale(1.1);
    box-shadow: var(--box-shadow);
}

.card-content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.icon-circle {
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.card-description {
    font-size: 0.9rem;
    color: var(--text-secondary);
    text-align: center;
    line-height: 1.5;
}

.card-icon {
    font-size: 2.2rem;
    display: block;
    background: linear-gradient(45deg, #4f46e5, #818cf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-wrapper {
    animation: fadeInUp 0.6s ease forwards;
}

.card-wrapper:nth-child(1) { animation-delay: 0.1s; }
.card-wrapper:nth-child(2) { animation-delay: 0.2s; }

/* Responsive Design */
@media (max-width: 1200px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

@media (max-width: 768px) {
    .page-wrapper {
        padding-top: 70px;
    }
    
    .navbar-brand .brand-title {
        font-size: 1rem;
    }
    
    .navbar-brand .brand-subtitle {
        font-size: 0.7rem;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
    }
    
    .storage-breakdown {
        grid-template-columns: 1fr;
    }
    
    .chart-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .grid {
        grid-template-columns: 1fr;
    }
    
    .user-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .index-title {
        font-size: 2rem;
    }
    
    .index-subtitle {
        font-size: 1rem;
    }
    
    .summary-stats {
        grid-template-columns: 1fr;
    }
    
    .btn-back-to-top {
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
    }
}

@media (max-width: 576px) {
    .index-container {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .chart-card {
        padding: 1rem;
    }
    
    .user-header {
        padding: 1rem;
    }
}

/* Print Styles */
@media print {
    .navbar,
    .footer,
    .btn-back-to-top,
    .chart-actions,
    .loading-overlay,
    .alert-container {
        display: none !important;
    }
    
    .page-wrapper {
        padding-top: 0;
        padding-bottom: 0;
    }
    
    .card,
    .stat-card,
    .chart-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .page-header {
        background: #f8f9fa !important;
        color: #333 !important;
    }
}