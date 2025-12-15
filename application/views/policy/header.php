<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $title; ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>">
    <meta name="keywords" content="นโยบาย, PDPA, ความเป็นส่วนตัว, คุกกี้, <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>">
    <meta name="author" content="<?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>">
    
    <title><?php echo $title; ?> - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo base_url('docs/logo.png'); ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Global Policy Styles -->
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --primary-light: #818CF8;
            --secondary: #06B6D4;
            --accent: #F59E0B;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --dark: #1E293B;
            --gray: #64748B;
            --light: #F1F5F9;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: #F8FAFC;
            color: var(--dark);
            line-height: 1.7;
            padding-top: 70px;
        }

        /* Top Navigation */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            z-index: 1030;
            height: 70px;
        }

        .top-navbar .container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: var(--dark);
        }

        .navbar-brand img {
            max-height: 45px;
            width: auto;
            object-fit: contain;
        }

        .navbar-brand-text {
            display: flex;
            flex-direction: column;
        }

        .navbar-brand-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .navbar-brand-subtitle {
            font-size: 0.85rem;
            color: var(--gray);
        }

        /* Policy Navigation */
        .policy-nav {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .policy-nav-item {
            padding: 8px 16px;
            border-radius: 50px;
            text-decoration: none;
            color: var(--dark);
            font-size: 0.95rem;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .policy-nav-item:hover {
            background: var(--light);
            color: var(--primary);
        }

        .policy-nav-item.active {
            background: var(--primary);
            color: white;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
        }

        /* Back to Main Site */
        .back-to-site {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .back-to-site:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1040;
            padding: 20px;
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            right: 0;
        }

        .mobile-sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--light);
        }

        .mobile-sidebar-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
        }

        .mobile-nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .mobile-nav .policy-nav-item {
            display: block;
            padding: 12px 20px;
            border-radius: 10px;
        }

        /* Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            z-index: 1035;
        }

        .mobile-overlay.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .policy-nav {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .navbar-brand-subtitle {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .back-to-site {
                bottom: 20px;
                left: 20px;
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .navbar-brand img {
                height: 35px;
            }
            
            .navbar-brand-title {
                font-size: 0.95rem;
            }
        }

        /* Loading Animation */
        .page-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .page-loading.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--light);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading -->
    <div class="page-loading" id="pageLoading">
        <div class="spinner"></div>
    </div>

    <!-- Top Navigation -->
    <nav class="top-navbar">
        <div class="container">
            <a href="<?php echo base_url(); ?>" class="navbar-brand">
                <img src="<?php echo base_url('docs/logo.png'); ?>" alt="<?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>" style="max-height: 50px; width: auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: none; align-items: center; justify-content: center;">
                    <i class="fas fa-shield-alt" style="color: white; font-size: 24px;"></i>
                </div>
                <div class="navbar-brand-text">
                    <span class="navbar-brand-title"><?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?></span>
                    <span class="navbar-brand-subtitle">นโยบายและข้อกำหนด</span>
                </div>
            </a>
            
            <!-- Desktop Navigation -->
            <div class="policy-nav">
                <a href="<?php echo site_url('policy/terms'); ?>" 
                   class="policy-nav-item <?php echo ($page == 'terms') ? 'active' : ''; ?>">
                    <i class="fas fa-file-contract me-1"></i> เว็บไซต์
                </a>
                <a href="<?php echo site_url('policy/security'); ?>" 
                   class="policy-nav-item <?php echo ($page == 'security') ? 'active' : ''; ?>">
                    <i class="fas fa-shield-alt me-1"></i> ความปลอดภัย
                </a>
                <a href="<?php echo site_url('policy/pdpa'); ?>" 
                   class="policy-nav-item <?php echo ($page == 'pdpa') ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield me-1"></i> PDPA
                </a>
                <a href="<?php echo site_url('policy/privacy'); ?>" 
                   class="policy-nav-item <?php echo ($page == 'privacy') ? 'active' : ''; ?>">
                    <i class="fas fa-user-lock me-1"></i> ความเป็นส่วนตัว
                </a>
                <a href="<?php echo site_url('policy/cookie'); ?>" 
                   class="policy-nav-item <?php echo ($page == 'cookie') ? 'active' : ''; ?>">
                    <i class="fas fa-cookie-bite me-1"></i> คุกกี้
                </a>
                <a href="<?php echo site_url('policy/membership'); ?>" 
                   class="policy-nav-item <?php echo ($page == 'membership') ? 'active' : ''; ?>">
                    <i class="fas fa-users me-1"></i> สมาชิก
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-header">
            <h5 class="m-0">นโยบายและข้อกำหนด</h5>
            <button class="mobile-sidebar-close" onclick="toggleMobileMenu()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="mobile-nav">
            <a href="<?php echo site_url('policy/terms'); ?>" 
               class="policy-nav-item <?php echo ($page == 'terms') ? 'active' : ''; ?>">
                <i class="fas fa-file-contract me-2"></i> นโยบายเว็บไซต์
            </a>
            <a href="<?php echo site_url('policy/security'); ?>" 
               class="policy-nav-item <?php echo ($page == 'security') ? 'active' : ''; ?>">
                <i class="fas fa-shield-alt me-2"></i> ความมั่นคงปลอดภัย
            </a>
            <a href="<?php echo site_url('policy/pdpa'); ?>" 
               class="policy-nav-item <?php echo ($page == 'pdpa') ? 'active' : ''; ?>">
                <i class="fas fa-user-shield me-2"></i> คุ้มครองข้อมูล PDPA
            </a>
            <a href="<?php echo site_url('policy/privacy'); ?>" 
               class="policy-nav-item <?php echo ($page == 'privacy') ? 'active' : ''; ?>">
                <i class="fas fa-user-lock me-2"></i> ความเป็นส่วนตัว
            </a>
            <a href="<?php echo site_url('policy/cookie'); ?>" 
               class="policy-nav-item <?php echo ($page == 'cookie') ? 'active' : ''; ?>">
                <i class="fas fa-cookie-bite me-2"></i> นโยบายคุกกี้
            </a>
            <a href="<?php echo site_url('policy/membership'); ?>" 
               class="policy-nav-item <?php echo ($page == 'membership') ? 'active' : ''; ?>">
                <i class="fas fa-users me-2"></i> การสมัครสมาชิก
            </a>
        </nav>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>

    <!-- Back to Main Site -->
    <a href="<?php echo base_url(); ?>" class="back-to-site">
        <i class="fas fa-arrow-left"></i>
        <span>กลับหน้าหลัก</span>
    </a>

    <!-- Start Main Content -->
    <main>
