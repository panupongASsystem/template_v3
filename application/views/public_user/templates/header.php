<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบบริการระบบบริการออนไลน์ - <?php echo $this->session->userdata('tenant_name'); ?></title>
	<link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
	
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css" crossorigin="anonymous">
	
	
	
	
	<script>
// === DISABLE ALL DEBUG/CONSOLE LOGS ===
(function() {
    // ปิด console ทั้งหมด
    console.log = function() {};
    console.warn = function() {};
    console.info = function() {};
    console.debug = function() {};
    // เก็บ console.error ไว้เฉพาะ error ที่สำคัญ (optional)
    // console.error = function() {};
})();
</script>
	
	
    <style>
        /* === CLEAN WHITE DESIGN SYSTEM === */
        :root {
            /* Soft Neutral Colors */
            --white: #ffffff;
            --gray-25: #fafbfc;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;

            /* Soft Accent Colors */
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-200: #bfdbfe;
            --blue-300: #93c5fd;
            --blue-400: #60a5fa;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;

            --emerald-50: #ecfdf5;
            --emerald-100: #d1fae5;
            --emerald-200: #a7f3d0;
            --emerald-500: #10b981;

            --amber-50: #fffbeb;
            --amber-100: #fef3c7;
            --amber-200: #fde68a;
            --amber-500: #f59e0b;

            --rose-50: #fff1f2;
            --rose-100: #ffe4e6;
            --rose-200: #fecdd3;
            --rose-500: #f43f5e;

            /* Soft Shadows */
            --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);

            /* Animations */
            --transition: 0.2s ease-out;
            --transition-slow: 0.3s ease-out;
            
            /* Border Radius */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 9999px;

            /* Legacy Variables for Compatibility */
            --header-primary: var(--blue-500);
            --header-primary-dark: var(--blue-600);
            --header-secondary: var(--emerald-500);
            --header-accent: var(--amber-500);
            --header-dark: var(--gray-700);
            --header-light: var(--gray-50);
            --header-border: var(--gray-200);
            --header-shadow-soft: var(--shadow-md);
            --header-shadow-medium: var(--shadow-lg);
            --header-backdrop-blur: blur(8px);
        }

        /* 🚨 REQUIRED: Modal z-index fixes - ต้องมีในทุกหน้า */
        .modal {
            z-index: 10050 !important;
        }
        .modal-backdrop {
            z-index: 10040 !important;
        }
        .modal-dialog {
            z-index: 10060 !important;
            position: relative;
        }
        .modal-content {
            position: relative;
            z-index: 10070 !important;
            border: none;
            box-shadow: var(--shadow-xl);
            border-radius: var(--radius-xl);
        }
        
        /* 🚨 REQUIRED: Session Warning Modals Styles - ต้องมีในทุกหน้า */
        .timeout-icon i, .logout-icon i {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .timeout-title, .logout-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--gray-800);
        }
        .timeout-message, .logout-message {
            line-height: 1.6;
            color: var(--gray-600);
        }
        /* Responsive สำหรับ Session Modals */
        @media (max-width: 576px) {
            .timeout-icon i, .logout-icon i {
                font-size: 3rem !important;
            }
            .timeout-title, .logout-title {
                font-size: 1.2rem;
            }
        }

        /* === NOTIFICATION BELL STYLES === */
        .notification-bell {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-full);
            color: var(--gray-600);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
        }
        
        .notification-bell:hover {
            background: var(--gray-25);
            color: var(--blue-600);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
            border-color: var(--blue-200);
        }
        
        .notification-bell i {
            font-size: 1rem;
            transition: transform var(--transition);
        }
        
        .notification-bell:hover i {
            animation: bellRing 0.5s ease-in-out;
        }
        
        @keyframes bellRing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-8deg); }
            75% { transform: rotate(8deg); }
        }
        
        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--rose-500);
            color: var(--white);
            border-radius: var(--radius-full);
            min-width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
            box-shadow: var(--shadow-sm);
            animation: badgePulse 2s infinite;
            z-index: 10;
        }
        
        /* Badge สำหรับ navbar menu */
        .nav-notification-badge {
            position: absolute;
            top: 4px;
            right: 8px;
            background: var(--rose-500);
            color: var(--white);
            border-radius: var(--radius-full);
            min-width: 16px;
            height: 16px;
            font-size: 0.65rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
            box-shadow: var(--shadow-sm);
            animation: badgePulse 2s infinite;
            z-index: 10;
            opacity: 0;
            transform: scale(0.5);
            transition: all var(--transition);
        }
        
        .nav-notification-badge.show {
            opacity: 1;
            transform: scale(1);
        }
        
        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        
        /* Loading state สำหรับ notification badge */
        .notification-loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .notification-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 14px;
            height: 14px;
            margin: -7px 0 0 -7px;
            border: 2px solid var(--gray-200);
            border-top: 2px solid var(--blue-500);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .notification-menu-item {
            color: var(--gray-600) !important;
            font-weight: 500;
            transition: all var(--transition);
            border-radius: var(--radius-md);
            margin: 0 4px;
            padding: 0.75rem 1rem !important;
            position: relative;
            text-decoration: none;
        }
        
        .notification-menu-item:hover, .notification-menu-item.active {
            color: var(--blue-600) !important;
            background: var(--blue-50);
            transform: translateX(2px);
            text-decoration: none;
        }
        
        .notification-menu-item i {
            margin-right: 8px;
            transition: all var(--transition);
            width: 16px;
            text-align: center;
        }

        /* === PROFILE IMAGE STYLES === */
        .profile-image {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: var(--radius-full);
            border: 2px solid var(--gray-200);
            transition: all var(--transition);
            box-shadow: var(--shadow-xs);
        }
        
        .profile-image:hover {
            transform: scale(1.05);
            border-color: var(--blue-300);
            box-shadow: var(--shadow-sm);
        }
        
        .profile-placeholder {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--blue-500), var(--blue-600));
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1rem;
            border: 2px solid var(--gray-200);
            transition: all var(--transition);
        }
        
        .profile-placeholder:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-sm);
        }

        /* === HEADER STYLES === */
        .header {
            background: var(--white);
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000 !important;
            transition: all var(--transition-slow);
        }
        
        .header.scrolled {
            box-shadow: var(--shadow-lg);
            border-bottom-color: var(--gray-300);
        }
        
        .header-logo {
            transition: all var(--transition);
            border-radius: var(--radius-lg);
            padding: 4px;
            background: var(--white);
            box-shadow: var(--shadow-xs);
            border: 1px solid var(--gray-200);
        }
        
        .header-logo:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            border-color: var(--blue-200);
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: var(--gray-800);
            letter-spacing: -0.01em;
        }
        
        .header-subtitle {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--gray-500);
            margin: 0;
        }
        
        .header-logo-link {
            text-decoration: none;
            display: inline-block;
            transition: all var(--transition);
            border-radius: var(--radius-lg);
        }
        
        .btn-primary-custom {
            background: var(--blue-500);
            border: 1px solid var(--blue-500);
            color: var(--white);
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-full);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary-custom:hover {
            background: var(--blue-600);
            border-color: var(--blue-600);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: var(--white);
            text-decoration: none;
        }
        
        .btn-outline-secondary {
            background: var(--white);
            border: 1px solid var(--gray-300);
            color: var(--gray-600);
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-full);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-outline-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
            color: var(--gray-700);
            transform: translateY(-1px);
            text-decoration: none;
        }
        
        .bg-light-custom {
            background: var(--white) !important;
            border-top: 1px solid var(--gray-200);
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-xs);
        }
        
        .nav-link {
            color: var(--gray-600) !important;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all var(--transition);
            border-radius: var(--radius-md);
            margin: 0 0.125rem;
            padding: 0.625rem 0.875rem !important;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--blue-600) !important;
            background: var(--blue-50);
            transform: translateY(-1px);
        }
        
        .nav-link i {
            font-size: 1rem;
        }
        
        /* === DROPDOWN === */
        .dropdown {
            position: relative;
            z-index: 1100;
        }
        
        .dropdown-menu {
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-lg);
            background: var(--white);
            margin-top: 0.5rem;
            min-width: 220px;
            padding: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition);
            transform: translateY(-8px);
        }
        
        .dropdown.show .dropdown-menu,
        .dropdown-menu.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            display: block !important;
        }
        
        .dropdown-item {
            padding: 0.625rem 0.875rem;
            transition: all var(--transition);
            color: var(--gray-600);
            border-radius: var(--radius-md);
            margin-bottom: 0.125rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
        }
        
        .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--gray-700);
            transform: translateX(2px);
        }
        
        .dropdown-item i {
            width: 16px;
            text-align: center;
            font-size: 1rem;
            color: var(--gray-500);
        }
        
        .dropdown-toggle {
            background: var(--white) !important;
            border: 1px solid var(--gray-200) !important;
            color: var(--gray-600) !important;
            padding: 0.625rem 1rem !important;
            border-radius: var(--radius-full) !important;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
            display: flex !important;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            position: relative;
            z-index: 1100;
        }
        
        .dropdown-toggle:hover,
        .dropdown-toggle:focus,
        .dropdown-toggle.show {
            background: var(--gray-25) !important;
            border-color: var(--blue-200) !important;
            color: var(--blue-600) !important;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .dropdown-toggle::after {
            margin-left: 0.25rem;
            transition: transform var(--transition);
        }
        
        .dropdown-toggle.show::after {
            transform: rotate(180deg);
        }

        /* === NAVBAR TOGGLER === */
        .navbar-toggler {
            border: 1px solid var(--gray-200) !important;
            padding: 0.5rem;
            background: var(--white);
            border-radius: var(--radius-md);
            transition: all var(--transition);
            box-shadow: var(--shadow-xs);
        }
        
        .navbar-toggler:hover {
            background: var(--gray-25);
            border-color: var(--gray-300);
            box-shadow: var(--shadow-sm);
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 2px var(--blue-100);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2875, 85, 99, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* === BODY STYLES === */
        body {
            font-family: 'Prompt', sans-serif;
            line-height: 1.6;
            color: var(--gray-700);
            background: var(--gray-25);
            min-height: 100vh;
        }

        main {
            min-height: calc(100vh - 200px);
            position: relative;
        }
        
        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .header-title {
                font-size: 1.25rem;
            }
            
            .header-subtitle {
                font-size: 0.8rem;
            }
            
            .notification-bell .notification-text {
                display: none;
            }
            
            .notification-bell {
                padding: 0.625rem;
                border-radius: var(--radius-full);
                width: 40px;
                height: 40px;
                justify-content: center;
            }
            
            .notification-bell i {
                margin-right: 0;
            }
            
            .notification-badge {
                min-width: 16px;
                height: 16px;
                font-size: 0.65rem;
                top: -4px;
                right: -4px;
            }
            
            .nav-notification-badge {
                min-width: 14px;
                height: 14px;
                font-size: 0.6rem;
                top: 2px;
                right: 6px;
            }
        }
        
        @media (max-width: 576px) {
            .header-logo {
                width: 48px;
                height: 48px;
            }
            
            .btn-outline-secondary {
                display: none;
            }
            
            .nav-notification-badge {
                top: 0;
                right: 4px;
                min-width: 12px;
                height: 12px;
                font-size: 0.55rem;
            }
        }

        /* === ENHANCED ANIMATIONS === */
        .navbar-collapse {
            transition: all var(--transition-slow);
        }
        
        .dropdown-menu {
            pointer-events: auto !important;
        }
        
        .dropdown-toggle {
            pointer-events: auto !important;
        }
        
        .dropdown-item:hover {
            pointer-events: auto !important;
        }

        /* === CARD EFFECTS === */
        .card-clean {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
        }

        .card-clean:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        /* === UTILITIES === */
        .text-soft {
            color: var(--gray-600);
        }

        .border-soft {
            border-color: var(--gray-200) !important;
        }

        .bg-soft {
            background-color: var(--gray-50) !important;
        }

        .shadow-soft {
            box-shadow: var(--shadow-sm) !important;
        }

        .rounded-soft {
            border-radius: var(--radius-md) !important;
        }
		
		
		
		
		
		/* === FOOTER STYLES === */
.footer {
    background: var(--white);
    color: var(--gray-600);
    border-top: 1px solid var(--gray-200);
    box-shadow: 0 -2px 8px 0 rgb(0 0 0 / 0.05);
    margin-top: auto;
}

.footer hr {
    border-color: var(--gray-200);
    opacity: 0.6;
}

.footer .company-link {
    color: var(--blue-600) !important;
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition);
    padding: 2px 4px;
    border-radius: var(--radius-sm);
}

.footer .company-link:hover {
    color: var(--blue-700) !important;
    background: var(--blue-50);
    text-decoration: none;
}

.footer .policy-links a {
    color: var(--gray-500) !important;
    text-decoration: none;
    font-weight: 400;
    transition: all var(--transition);
    padding: 4px 8px;
    border-radius: var(--radius-sm);
}

.footer .policy-links a:hover {
    color: var(--gray-700) !important;
    background: var(--gray-50);
    text-decoration: none;
}

.footer .text-muted {
    color: var(--gray-500) !important;
}

/* === BACK TO TOP BUTTON === */
.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none;
    z-index: 1000;
    width: 48px;
    height: 48px;
    padding: 0;
    background: var(--white);
    border: 1px solid var(--gray-200);
    color: var(--gray-600);
    border-radius: var(--radius-full);
    transition: all var(--transition);
    box-shadow: var(--shadow-md);
    justify-content: center;
    align-items: center;
}

.back-to-top:hover {
    background: var(--blue-50);
    border-color: var(--blue-200);
    color: var(--blue-600);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.back-to-top i {
    font-size: 1.25rem;
}
		
		
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header shadow-sm">
        <div class="container">
            <div class="py-3 d-flex align-items-center justify-content-between">
                <!-- Logo & Agency Name -->
                <div class="d-flex align-items-center">
                    <a href="<?php echo base_url(); ?>" class="header-logo-link">
                        <img src="<?php echo base_url('docs/logo.png'); ?>" alt="Logo" class="header-logo" width="60">
                    </a>
                    <div class="ms-3">
                        <h1 class="header-title"><?php echo $this->session->userdata('tenant_name'); ?></h1>
                        <p class="header-subtitle mb-0">ระบบบริการอิเล็กทรอนิกส์</p>
                    </div>
                </div>

                <!-- User or Login Section -->
                <div class="d-flex align-items-center gap-3">
                    <?php if ($this->session->userdata('mp_id')): ?>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle d-flex align-items-center" type="button"
                                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                    // เช็ครูปโปรไฟล์ตามลำดับ docs/img/avatar -> docs/img -> default
                                    $profile_img = $this->session->userdata('mp_img');
                                    $mp_fname = $this->session->userdata('mp_fname');
                                    $mp_lname = $this->session->userdata('mp_lname');
                                    
                                    // กำหนด path สำหรับรูปโปรไฟล์
                                    $profile_path = '';
                                    $found_location = 'none';
                                    
                                    if (!empty($profile_img)) {
                                        // ลำดับที่ 1: ลองหาใน docs/img/avatar/ ก่อน (รูปจาก register)
                                        $avatar_path = 'docs/img/avatar/' . $profile_img;
                                        if (file_exists(FCPATH . $avatar_path)) {
                                            $profile_path = $avatar_path;
                                            $found_location = 'avatar';
                                        } else {
                                            // ลำดับที่ 2: ลองหาใน docs/img/ (รูปที่เปลี่ยนเอง)
                                            $user_path = 'docs/img/' . $profile_img;
                                            if (file_exists(FCPATH . $user_path)) {
                                                $profile_path = $user_path;
                                                $found_location = 'user';
                                            }
                                        }
                                    }
                                    
                                    // ลำดับที่ 3: ถ้าไม่เจอรูปใดๆ ใช้ default
                                    if (empty($profile_path)) {
                                        $profile_path = 'docs/img/User.png';
                                        $found_location = 'default';
                                    }
                                    
                                    // Debug info
                                    if (isset($_GET['debug_profile']) && $_GET['debug_profile'] == 'true') {
                                        echo "<!-- Profile Debug: img={$profile_img}, path={$profile_path}, location={$found_location} -->";
                                    }
                                ?>
                                
                                <?php if (!empty($profile_path)): ?>
                                    <img src="<?php echo base_url($profile_path); ?>" 
                                         alt="Profile" 
                                         class="profile-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="profile-placeholder" style="display: none;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo base_url('docs/img/User.png'); ?>" 
                                         alt="Profile" 
                                         class="profile-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="profile-placeholder" style="display: none;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <span class="d-none d-md-inline">
                                    <?php echo !empty($mp_fname) ? $mp_fname : 'ผู้ใช้'; ?>
                                </span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?php echo site_url('Auth_public_mem/profile'); ?>">
                                    <i class="bi bi-person"></i>ข้อมูลส่วนตัว</a></li>
                                <li><a class="dropdown-item" href="<?php echo site_url('Pages/service_systems'); ?>">
                                    <i class="bi bi-grid"></i>ระบบบริการ</a></li>
                                <li><a class="dropdown-item" href="<?php echo site_url('notifications/all'); ?>">
                                    <i class="bi bi-bell"></i>การแจ้งเตือน</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo site_url('Auth_public_mem/logout'); ?>">
                                    <i class="bi bi-box-arrow-right"></i>ออกจากระบบ</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo site_url('User'); ?>" class="btn btn-primary-custom me-2">
                            <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                        </a>
                        <a href="<?php echo site_url('Auth_public_mem/register_form'); ?>"
                            class="btn btn-outline-secondary d-none d-md-inline-flex">
                            <i class="bi bi-person-plus"></i> สมัครสมาชิก
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light-custom">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (uri_string() == '' || uri_string() == 'Home') ? 'active' : ''; ?>"
                                href="<?php echo base_url(); ?>">
                                <i class="bi bi-house"></i> หน้าหลัก
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (uri_string() == 'Pages/service_systems') ? 'active' : ''; ?>"
                                href="<?php echo site_url('Pages/service_systems'); ?>">
                                <i class="bi bi-grid"></i> ระบบบริการ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('Esv_ods/forms_online'); ?>">
                                <i class="bi bi-journal-text"></i> เอกสารดาวน์โหลด
                            </a>
                        </li>
						<li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('Pages/q_a'); ?>">
                                <i class="bi bi-chat-dots"></i> กระทู้ ถาม-ตอบ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('Pages/questions'); ?>">
                                <i class="bi bi-question-circle"></i> คำถามที่พบบ่อย
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo site_url('Pages/contact'); ?>">
                                <i class="bi bi-telephone"></i> ติดต่อเรา
                            </a>
                        </li>
                        <?php if ($this->session->userdata('mp_id')): ?>
                            <li class="nav-item" style="position: relative;">
                                <a class="nav-link notification-menu-item <?php echo (uri_string() == 'notifications/all') ? 'active' : ''; ?>" 
                                   href="<?php echo site_url('notifications/all'); ?>"
                                   id="notificationNavLink">
                                    <i class="bi bi-bell"></i> การแจ้งเตือน
                                </a>
                                <span class="nav-notification-badge" id="navNotificationBadge">0</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- ปุ่มออกจากระบบ ด้านขวา -->
                    <?php if ($this->session->userdata('mp_id')): ?>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('Auth_public_mem/logout'); ?>">
                                    <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Clean JavaScript สำหรับ notification badge และ clean interactions -->
    <?php if ($this->session->userdata('mp_id')): ?>
    <script>
    // Clean & Minimal Notification Management System
    class CleanNotificationManager {
        constructor() {
            this.isLoading = false;
            this.lastUpdateTime = 0;
            this.updateInterval = 30000; // 30 วินาที
            this.retryCount = 0;
            this.maxRetries = 3;
            this.init();
        }

        init() {
            console.log('🔔 Clean Notification System Initialized');
            this.setupEventListeners();
            this.startAutoUpdate();
        }

        setupEventListeners() {
            // Clean notification bell interactions
            const notificationBell = document.querySelector('.notification-bell');
            if (notificationBell) {
                notificationBell.addEventListener('mouseenter', this.onNotificationHover.bind(this));
                notificationBell.addEventListener('click', this.onNotificationClick.bind(this));
            }

            // Responsive handling
            window.addEventListener('resize', this.handleResize.bind(this));
            
            // Visibility change for performance
            document.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));
        }

        onNotificationHover(e) {
            const icon = e.target.querySelector('i');
            if (icon) {
                icon.style.animation = 'bellRing 0.5s ease-in-out';
                setTimeout(() => { if (icon) icon.style.animation = ''; }, 500);
            }
        }

        onNotificationClick(e) {
            console.log('🔔 Notification clicked');
        }

        handleResize() {
            const notificationText = document.querySelector('.notification-text');
            if (notificationText) {
                notificationText.style.display = window.innerWidth < 768 ? 'none' : 'inline';
            }
        }

        handleVisibilityChange() {
            if (!document.hidden) {
                setTimeout(() => this.loadNotificationCount(), 1000);
            }
        }

        async getUserInfo() {
            try {
                return {
                    session_mp_id: '<?php echo $this->session->userdata('mp_id'); ?>',
                    actual_user_id: <?php 
                        $mp_email = $this->session->userdata('mp_email');
                        $actual_user_id = null;
                        if ($mp_email) {
                            $public_user = $this->db->select('id, mp_id')
                                                 ->where('mp_email', $mp_email)
                                                 ->get('tbl_member_public')
                                                 ->row();
                            if ($public_user) {
                                $actual_user_id = $public_user->id;
                            }
                        }
                        echo $actual_user_id ? $actual_user_id : 'null';
                    ?>,
                    email: '<?php echo $this->session->userdata('mp_email'); ?>',
                    is_logged_in: true,
                    user_type: 'public'
                };
            } catch (error) {
                console.error('❌ Error getting user info:', error);
                return { is_logged_in: false };
            }
        }

        async loadNotificationCount() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            const navBadge = document.getElementById('navNotificationBadge');
            const headerBadge = document.getElementById('headerNotificationBadge');
            const navLink = document.getElementById('notificationNavLink');
            
            try {
                if (navLink) {
                    navLink.classList.add('notification-loading');
                }
                
                const userInfo = await this.getUserInfo();
                
                if (!userInfo.is_logged_in || !userInfo.actual_user_id) {
                    this.hideBadges();
                    return;
                }
                
                const response = await fetch('<?php echo site_url("notifications/get_recent"); ?>?limit=1&t=' + Date.now(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    this.updateBadges(data.unread_count || 0);
                    this.lastUpdateTime = Date.now();
                    this.retryCount = 0;
                } else {
                    throw new Error(data.message || 'API Error');
                }
                
            } catch (error) {
                console.error('🚨 Error loading notifications:', error);
                this.handleError();
            } finally {
                this.isLoading = false;
                
                if (navLink) {
                    navLink.classList.remove('notification-loading');
                }
            }
        }

        updateBadges(count) {
            const navBadge = document.getElementById('navNotificationBadge');
            const headerBadge = document.getElementById('headerNotificationBadge');
            
            if (count > 0) {
                const displayCount = count > 99 ? '99+' : count.toString();
                
                if (navBadge) {
                    navBadge.textContent = displayCount;
                    navBadge.classList.add('show');
                }
                
                if (headerBadge) {
                    headerBadge.textContent = displayCount;
                    headerBadge.style.display = 'flex';
                }
                
            } else {
                this.hideBadges();
            }
        }

        hideBadges() {
            const navBadge = document.getElementById('navNotificationBadge');
            const headerBadge = document.getElementById('headerNotificationBadge');
            
            if (navBadge) navBadge.classList.remove('show');
            if (headerBadge) headerBadge.style.display = 'none';
        }

        handleError() {
            this.retryCount++;
            
            if (this.retryCount <= this.maxRetries) {
                setTimeout(() => {
                    this.isLoading = false;
                    this.loadNotificationCount();
                }, 5000);
            } else {
                this.hideBadges();
            }
        }

        startAutoUpdate() {
            this.loadNotificationCount();
            
            setInterval(() => {
                if (!this.isLoading && 
                    !document.hidden && 
                    (Date.now() - this.lastUpdateTime) >= this.updateInterval) {
                    this.loadNotificationCount();
                }
            }, this.updateInterval);
        }

        // Public methods
        testBadge(count = 3) {
            console.log('🧪 Testing badge...');
            this.updateBadges(count);
            setTimeout(() => this.loadNotificationCount(), 3000);
        }

        refresh() {
            this.loadNotificationCount();
        }
    }

    // Clean Dropdown Management
    class CleanDropdownManager {
        constructor() {
            this.init();
        }

        init() {
            this.setupDropdownBehavior();
            console.log('📋 Clean Dropdown System Initialized');
        }

        setupDropdownBehavior() {
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                if (toggle && menu) {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleDropdown(dropdown, toggle, menu);
                    });

                    document.addEventListener('click', (e) => {
                        if (!dropdown.contains(e.target)) {
                            this.closeDropdown(dropdown, toggle, menu);
                        }
                    });

                    const items = menu.querySelectorAll('.dropdown-item');
                    items.forEach(item => {
                        item.addEventListener('click', (e) => {
                            if (item.getAttribute('href') && item.getAttribute('href') !== '#') {
                                setTimeout(() => this.closeDropdown(dropdown, toggle, menu), 100);
                            }
                        });
                    });
                }
            });
        }

        toggleDropdown(dropdown, toggle, menu) {
            this.closeAllDropdowns(dropdown);
            
            const isOpen = menu.classList.contains('show');
            
            if (isOpen) {
                this.closeDropdown(dropdown, toggle, menu);
            } else {
                this.openDropdown(dropdown, toggle, menu);
            }
        }

        openDropdown(dropdown, toggle, menu) {
            menu.classList.add('show');
            toggle.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
            dropdown.classList.add('show');
        }

        closeDropdown(dropdown, toggle, menu) {
            menu.classList.remove('show');
            toggle.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
            dropdown.classList.remove('show');
        }

        closeAllDropdowns(except = null) {
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                if (dropdown !== except) {
                    const menu = dropdown.querySelector('.dropdown-menu');
                    const toggle = dropdown.querySelector('.dropdown-toggle');
                    if (menu && toggle) {
                        this.closeDropdown(dropdown, toggle, menu);
                    }
                }
            });
        }
    }

    // Header Scroll Effect
    class CleanHeaderScrollEffect {
        constructor() {
            this.header = document.querySelector('.header');
            this.ticking = false;
            this.init();
        }

        init() {
            window.addEventListener('scroll', this.onScroll.bind(this));
            console.log('📜 Clean Header Scroll Effect Initialized');
        }

        onScroll() {
            if (!this.ticking) {
                requestAnimationFrame(this.updateHeader.bind(this));
                this.ticking = true;
            }
        }

        updateHeader() {
            const scrollY = window.scrollY;
            
            if (this.header) {
                if (scrollY > 50) {
                    this.header.classList.add('scrolled');
                } else {
                    this.header.classList.remove('scrolled');
                }
            }
            
            this.ticking = false;
        }
    }

    // Initialize clean systems
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✨ Clean UI System Starting...');
        
        window.cleanNotificationManager = new CleanNotificationManager();
        window.cleanDropdownManager = new CleanDropdownManager();
        window.cleanHeaderScrollEffect = new CleanHeaderScrollEffect();
        
        console.log('✅ Clean UI System Ready!');
    });

    // Global utility functions
    window.testNotificationBadge = function(count = 3) {
        if (window.cleanNotificationManager) {
            window.cleanNotificationManager.testBadge(count);
        }
    };

    window.refreshNotificationCount = function() {
        if (window.cleanNotificationManager) {
            window.cleanNotificationManager.refresh();
        }
    };

    console.log('🔔 Clean Notification Manager loaded');
    </script>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Main Content -->
    <main class="py-4">