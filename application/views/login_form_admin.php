<!DOCTYPE html>
<html lang="th">

<head>
    <script>
        window.base_url = '<?php echo base_url(); ?>';
        window.RECAPTCHA_KEY = '<?php echo get_config_value("recaptcha"); ?>';

        // Debug logging
        console.log('🔑 Base URL:', window.base_url);
        console.log('🔑 reCAPTCHA Key:', window.RECAPTCHA_KEY ? window.RECAPTCHA_KEY.substring(0, 20) + '...' : 'NOT SET');

        // ตรวจสอบว่าค่าว่างหรือไม่
        if (!window.RECAPTCHA_KEY || window.RECAPTCHA_KEY === '' || window.RECAPTCHA_KEY === 'undefined') {
            console.error('❌ reCAPTCHA Site Key is not configured!');
            console.error('❌ Please check get_config_value("recaptcha") in your config');
        }


        // *** เพิ่ม: ตั้งค่า temp_user_type จาก PHP ***
        <?php if (isset($temp_user_type)): ?>
            window.temp_user_type = '<?php echo $temp_user_type; ?>';
            console.log('Temp user type from PHP:', window.temp_user_type);
        <?php endif; ?>

        // *** เพิ่ม: ตั้งค่า requires_2fa จาก PHP ***
        <?php if (isset($requires_2fa) && $requires_2fa): ?>
            window.requires_2fa = true;
            console.log('2FA required from PHP');
        <?php endif; ?>
    </script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $this->security->get_csrf_hash(); ?>">
    <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    <title><?php echo get_config_value('fname'); ?> - ระบบบริการออนไลน์</title>

    <!-- Fonts & Icons -->
    <link href='https://fonts.googleapis.com/css?family=Kanit:300,400,500,600,700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo get_config_value('recaptcha'); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ เพิ่มการตรวจสอบ reCAPTCHA Key ก่อนโหลด -->
    <?php if (get_config_value('recaptcha')): ?>
        <script>
            // ตรวจสอบ reCAPTCHA Key ก่อนโหลด script
            window.recaptcha_site_key = '<?php echo get_config_value("recaptcha"); ?>';
            console.log('🔑 Loading reCAPTCHA with site key:', window.recaptcha_site_key.substring(0, 10) + '...');
        </script>
        <script
            src="https://www.google.com/recaptcha/api.js?render=<?php echo get_config_value('recaptcha'); ?>&onload=onRecaptchaLoad"
            async defer></script>
    <?php else: ?>
        <script>
            console.error('❌ reCAPTCHA Site Key not configured in database');
            window.RECAPTCHA_KEY = '';
        </script>
    <?php endif; ?>
    <style>
        :root {
            --primary: #4A89DC;
            --primary-dark: #3D71BA;
            --secondary: #8CC152;
            --secondary-dark: #76A938;
            --accent: #F6BB42;
            --accent-dark: #E8AA2E;
            --light: #F5F7FA;
            --dark: #434A54;
            --error: #FC6E51;
            --success: #48CFAD;
            --text-color: #434A54;
            --border-color: #E6E9ED;
            --form-bg: rgba(255, 255, 255, 0.95);
            --shadow-light: rgba(0, 0, 0, 0.05);
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-dark: rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }


        body {
            min-height: 100vh;
            /* background-image: url('<?php echo base_url("docs/welcome-btm-light-other.png"); ?>');  */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--text-color);
            display: flex;
            flex-direction: column;
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(74, 137, 220, 0.4), rgba(140, 193, 82, 0.4));
            z-index: -1;
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .login-container {
            width: 100%;
            max-width: 900px;
            margin: 40px auto;
            padding: 0;
            position: relative;
            z-index: 10;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo {
            width: 120px;
            height: 120px;
            margin-bottom: 10px;
            filter: drop-shadow(0 5px 15px var(--shadow));
            transition: transform 0.3s ease;
        }

        .login-logo:hover {
            transform: scale(1.05);
        }

        .login-title {
            font-size: 32px;
            font-weight: 600;
            color: white;
            text-shadow: 0 2px 4px var(--shadow-dark);
            margin-top: 10px;
        }

        .login-subtitle {
            font-size: 18px;
            color: white;
            text-shadow: 0 1px 2px var(--shadow-dark);
            margin-top: 5px;
        }

        /* ปรับแต่ง Login Tabs ให้มีความชัดเจนมากขึ้น */
        .login-tabs {
            display: flex;
            margin-bottom: 0;
            border-radius: 15px 15px 0 0;
            overflow: hidden;
            box-shadow: 0 -5px 15px var(--shadow-light);
        }

        .tab-btn {
            flex: 1;
            padding: 18px 15px;
            /* เพิ่มความสูง */
            background-color: rgba(255, 255, 255, 0.7);
            color: var(--dark);
            font-size: 18px;
            /* เพิ่มขนาดตัวอักษร */
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            border-bottom: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }

        /* สำหรับแท็บประชาชน */
        .tab-btn[data-tab="citizen"] {
            background-color: rgba(236, 246, 255, 0.9);
            color: #2C77D1;
            border-bottom: 4px solid transparent;
        }

        .tab-btn[data-tab="citizen"].active {
            background-color: var(--form-bg);
            border-bottom: 4px solid #2C77D1;
            font-weight: 600;
            font-size: 18.5px;
            /* ขยายเล็กน้อยเมื่อแอคทีฟ */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05) inset;
        }

        .tab-btn[data-tab="citizen"]:hover:not(.active) {
            background-color: rgba(245, 249, 255, 0.95);
        }

        .tab-btn[data-tab="citizen"] i {
            color: #4389E3;
        }

        /* สำหรับแท็บเจ้าหน้าที่ */
        .tab-btn[data-tab="staff"] {
            background-color: rgba(245, 245, 252, 0.9);
            color: #6355C2;
            border-bottom: 4px solid transparent;
        }

        .tab-btn[data-tab="staff"].active {
            background-color: var(--form-bg);
            border-bottom: 4px solid #6355C2;
            font-weight: 600;
            font-size: 18.5px;
            /* ขยายเล็กน้อยเมื่อแอคทีฟ */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05) inset;
        }

        .tab-btn[data-tab="staff"]:hover:not(.active) {
            background-color: rgba(245, 243, 255, 0.95);
        }

        .tab-btn[data-tab="staff"] i {
            color: #7D67E0;
        }

        /* ปรับปรุงไอคอน */
        .tab-btn i {
            margin-right: 10px;
            font-size: 20px;
            /* เพิ่มขนาดไอคอน */
            vertical-align: middle;
        }

        .login-card {
            background-color: var(--form-bg);
            border-radius: 0 0 15px 15px;
            padding: 30px;
            box-shadow: 0 8px 30px var(--shadow);
        }

        .login-form {
            display: none;
        }

        .login-form.active {
            display: block;
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--primary);
            text-align: center;
        }

        .form-group {
            margin-bottom: 22px;
            position: relative;
            text-align: center;
            /* จัดให้ form group อยู่ตรงกลาง */
        }

        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 137, 220, 0.2);
            outline: none;
        }

        .input-field::placeholder {
            color: #AAB2BD;
        }

        .login-btn {
            display: block;
            width: 80%;
            /* กำหนดความกว้างของปุ่ม */
            padding: 14px;
            margin: 15px auto 0;
            /* จัดให้ปุ่มอยู่ตรงกลาง */
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(74, 137, 220, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(74, 137, 220, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(74, 137, 220, 0.3);
        }

        .register-link {
            margin-top: 20px;
            text-align: center;
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .forgot-link a {
            color: var(--accent-dark);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        /* จัดการตำแหน่งของฟอร์ม login */
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: var(--dark);
            text-align: center;
            /* จัดให้ label อยู่ตรงกลาง */
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 80%;
            /* กำหนดความกว้างของ input wrapper */
            margin: 0 auto;
            /* จัดให้อยู่ตรงกลาง */
        }

        .input-icon {
            position: absolute;
            left: 15px;
            color: var(--primary);
            font-size: 18px;
        }

        .input-field {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            margin: 0 auto;
            /* จัดให้อยู่ตรงกลาง */
        }

        .forgot-link {
            text-align: center;
            /* จัดให้ลิงก์ลืมรหัสผ่านอยู่ตรงกลาง */
            margin-top: 10px;
        }

        .required-star {
            color: var(--error);
            margin-left: 3px;
        }

        .forgot-link a:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background-color: var(--border-color);
        }

        .divider-text {
            padding: 0 15px;
            color: #AAB2BD;
            font-size: 14px;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #656D78;
            font-size: 14px;
        }

        .footer-text a {
            color: var(--primary);
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        .support-badge {
            display: inline-block;
            padding: 8px 12px;
            margin-top: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 2px 10px var(--shadow);
            transition: all 0.3s ease;
        }

        .support-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--shadow);
        }

        .support-badge i {
            color: var(--success);
            margin-right: 5px;
        }

        .support-badge a {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
        }

        .support-badge a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                margin: 30px auto;
                padding: 0 15px;
            }

            .login-card {
                padding: 20px;
            }

            .login-title {
                font-size: 28px;
            }

            .login-logo {
                width: 100px;
                height: 100px;
            }

            .tab-btn {
                font-size: 17px;
                padding: 16px 10px;
            }

            .tab-btn.active {
                font-size: 17.5px;
            }
        }

        @media (max-width: 576px) {
            .login-tabs {
                flex-direction: column;
                border-radius: 15px 15px 0 0;
            }

            .tab-btn {
                border-radius: 0;
                padding: 14px;
                font-size: 16px;
            }

            .tab-btn.active {
                font-size: 16.5px;
            }

            .tab-btn i {
                font-size: 18px;
            }

            .tab-btn:first-child {
                border-radius: 15px 15px 0 0;
            }

            .login-card {
                border-radius: 0 0 15px 15px;
            }

            .login-title {
                font-size: 24px;
            }

            .login-subtitle {
                font-size: 16px;
            }
        }

        /* Card flip effect */
        .card-3d-wrapper {
            perspective: 1500px;
        }

        .card-3d-container {
            transform-style: preserve-3d;
            transition: transform 0.6s;
            position: relative;
            min-height: 400px;
        }

        .card-face {
            width: 100%;
            height: 100%;
            position: absolute;
            backface-visibility: hidden;
            top: 0;
            left: 0;
        }

        .card-face-back {
            transform: rotateY(180deg);
        }

        .slideshow-wrapper {
            position: relative;
            /* เปลี่ยนจาก fixed เป็น relative */
            width: 100%;
            margin-top: -70px;
            /* เพิ่ม margin-top เพื่อให้มีระยะห่างจากกล่อง login */
            margin-bottom: 50px;
            /* เพิ่ม margin-bottom เพื่อให้มีระยะห่างจากขอบล่างของหน้าจอ */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        /* ปรับแต่ง CSS สำหรับ body และ background */
        body {
            min-height: 100vh;
            /*  background-image: url('<?php echo base_url("docs/welcome-btm-light-other.png"); ?>'); */
            background-attachment: fixed;
            /* ทำให้พื้นหลังคงที่เมื่อเลื่อนหน้าจอ */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            /* อนุญาตให้เลื่อนหน้าจอได้ */
        }

        .container {
            padding-bottom: 50px;
            /* เพิ่มระยะห่างด้านล่างของ container */
        }

        /* ปรับโครงสร้างการแสดงผล slideshow */
        .slideshow-container {
            max-width: 1200px;
            width: 90%;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 50px 0;
            position: relative;
            cursor: grab;
            display: flex;
        }

        .slideshow-container::-webkit-scrollbar {
            display: none;
        }

        .slide-track {
            display: flex;
            gap: 20px;
            padding: 0 20px;
        }

        /* ปรับขนาดและสไตล์ของการ์ด */
        .card {
            width: 230px;
            height: 330px;
            flex-shrink: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: inline-block;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            margin: 10px;
            white-space: normal;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            width: 100%;
            height: 110px;
            /* ลดขนาด */
            object-fit: cover;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 10px;
            /* ลดพื้นที่ padding */
            text-align: center;
        }

        .card-title {
            font-size: 16px;
            /* ลดขนาดฟอนต์ */
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 42px;
        }

        .card-text {
            font-size: 13px;
            /* ลดขนาดฟอนต์ */
            color: var(--text-color);
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 55px;
        }

        .btn {
            display: inline-block;
            padding: 6px 15px;
            /* ลดขนาด */
            background: var(--primary);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 3px 8px rgba(74, 137, 220, 0.3);
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            /* ลดขนาดฟอนต์ */
        }

        /* ปรับขนาดปุ่มนำทาง */
        .prev,
        .next {
            width: 30px;
            height: 30px;
            margin-top: -15px;
            line-height: 30px;
            font-size: 14px;
        }

        /* Animation for particles */
        .floating-particles .particle:nth-child(1) {
            top: 10%;
            left: 20%;
            animation-duration: 25s;
        }

        .floating-particles .particle:nth-child(2) {
            top: 30%;
            left: 60%;
            animation-duration: 35s;
        }

        .floating-particles .particle:nth-child(3) {
            top: 60%;
            left: 40%;
            animation-duration: 30s;
        }

        .floating-particles .particle:nth-child(4) {
            top: 90%;
            left: 80%;
            animation-duration: 20s;
        }

        .floating-particles .particle:nth-child(5) {
            top: 40%;
            left: 10%;
            animation-duration: 40s;
        }

        .floating-particles .particle:nth-child(6) {
            top: 70%;
            left: 30%;
            animation-duration: 28s;
        }

        .floating-particles .particle:nth-child(7) {
            top: 20%;
            left: 70%;
            animation-duration: 33s;
        }

        .floating-particles .particle:nth-child(8) {
            top: 50%;
            left: 90%;
            animation-duration: 22s;
        }

        .floating-particles .particle:nth-child(9) {
            top: 80%;
            left: 50%;
            animation-duration: 38s;
        }

        .floating-particles .particle:nth-child(10) {
            top: 5%;
            left: 85%;
            animation-duration: 32s;
        }

        .floating-particles .particle:nth-child(11) {
            top: 25%;
            left: 35%;
            animation-duration: 27s;
        }

        .floating-particles .particle:nth-child(12) {
            top: 55%;
            left: 15%;
            animation-duration: 36s;
        }

        .as-highlight {
            color: #E67E22;
            /* สีส้มเข้ม */
            font-weight: 500;
        }

        .company-name {
            font-size: 1.0rem;
            font-weight: 500;
        }

        .company-name a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .company-name a:hover {
            color: var(--primary);
        }
    </style>


    <style>
        /* Google Authenticator Modal Styles - ปรับปรุงใหม่ */
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 5px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .otp-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(74, 137, 220, 0.25);
            outline: none;
        }

        .btn-verify {
            background: linear-gradient(45deg, var(--primary), var(--primary-dark));
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: box-shadow 0.3s ease, filter 0.3s ease;
        }

        .btn-verify:hover {
            box-shadow: 0 6px 20px rgba(74, 137, 220, 0.4);
            filter: brightness(1.05);
        }

        .btn-outline-secondary {
            transition: box-shadow 0.3s ease, filter 0.3s ease;
            border-color: #e9ecef !important;
            color: #6c757d !important;
            background-color: transparent !important;
        }

        .btn-outline-secondary:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            filter: brightness(0.98);
            border-color: #dee2e6 !important;
            color: #5a6268 !important;
            background-color: #f8f9fa !important;
        }

        /* ปรับปรุงส่วนข้อความแทนนาฬิกานับถอยหลัง */
        .security-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-left: 4px solid var(--primary);
            padding: 1rem;
            border-radius: 0 10px 10px 0;
            margin: 1rem 0;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: default;
        }

        .security-info.clickable {
            cursor: pointer;
            background: linear-gradient(135deg, #e8f4fd, #d1ecf1);
            border-left: 4px solid var(--success);
        }

        .security-info.clickable:hover {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 207, 173, 0.3);
        }

        .security-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(74, 137, 220, 0.1), transparent);
            animation: shimmer 2s infinite;
        }

        .security-info.clickable::before {
            background: linear-gradient(90deg, transparent, rgba(72, 207, 173, 0.15), transparent);
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .security-text {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            z-index: 1;
        }

        .security-info.clickable .security-text {
            color: var(--success);
            font-weight: 600;
        }

        .pulse-icon {
            animation: pulse 2s infinite;
            color: var(--success);
        }

        .security-info.clickable .pulse-icon {
            animation: pulse 1.5s infinite;
            color: var(--success);
        }

        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.1);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .instruction {
            background: #f8f9fa;
            border-left: 4px solid var(--primary);
            padding: 1rem;
            border-radius: 0 10px 10px 0;
            margin: 0.3rem 0 1rem 0;
        }

        .app-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #4285f4, #34a853);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        /* Responsive สำหรับหน้าจอเล็ก */
        @media (max-width: 576px) {
            .button-container {
                flex-direction: column !important;
                gap: 10px !important;
            }

            .button-container .btn {
                width: 100% !important;
                min-width: unset !important;
            }
        }

        /* Animation สำหรับการแสดง/ซ่อนปุ่ม */
        .button-container .btn {
            transition: opacity 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
        }

        /* เพิ่มเอฟเฟกต์สำหรับหัวข้อ modal */
        .modal-title-wrapper {
            position: relative;
            overflow: hidden;
        }

        .modal-title-wrapper::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(45deg, var(--primary), var(--primary-dark));
            border-radius: 2px;
        }




        .form-label.fw-bold {
            font-size: 18px !important;
            /* เพิ่มขนาดตัวอักษร */
            font-weight: 600 !important;
            /* เพิ่มความหนา */
            color: #495057 !important;
            /* สีเข้ม */
            margin-bottom: 12px !important;
            /* เพิ่มระยะห่าง */
        }

        /* ถ้าต้องการใหญ่มากขึ้นอีก */
        .otp-label-large {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #212529 !important;
            margin-bottom: 15px !important;
        }
    </style>



    <style>
        /* Password form styling - label และ input แยกบรรทัด */
        .password-label-container {
            width: 100%;
            text-align: left;
            margin-bottom: 8px;
        }

        .password-label {
            display: block;
            font-weight: 600;
            font-size: 16px;
            color: var(--dark);
            margin: 0;
            padding-left: 5%;
        }

        .input-container {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 90%;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            color: var(--primary);
            font-size: 18px;
            z-index: 2;
        }

        .input-field {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .password-hint {
            width: 100%;
            text-align: center;
            margin-top: 5px;
        }

        .password-hint small {
            font-size: 13px;
            color: #6c757d;
        }

        /* Password strength indicator */
        .password-strength {
            margin: 10px 0;
        }

        .strength-meter {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-text {
            font-size: 12px;
            font-weight: 500;
        }

        /* Form validation styles */
        .input-field.is-valid {
            border-color: var(--success) !important;
            box-shadow: 0 0 0 0.2rem rgba(72, 207, 173, 0.25) !important;
        }

        .input-field.is-invalid {
            border-color: var(--error) !important;
            box-shadow: 0 0 0 0.2rem rgba(252, 110, 81, 0.25) !important;
        }

        /* Reset password modal specific styles */
        #resetPasswordPublicModal .modal-content {
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        #resetPasswordPublicModal .form-group {
            margin-bottom: 20px;
        }

        #resetPasswordPublicModal .input-container .input-wrapper {
            margin-bottom: 0;
        }

        /* Button hover effects for reset modal */
        #submitResetPassword:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(72, 207, 173, 0.4);
            filter: brightness(1.05);
        }

        #submitResetPassword:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Responsive design for reset modal */
        @media (max-width: 768px) {
            .password-label {
                font-size: 15px;
                padding-left: 8%;
            }

            .input-container .input-wrapper {
                width: 92%;
            }
        }

        @media (max-width: 576px) {
            #resetPasswordPublicModal .modal-dialog {
                margin: 10px;
            }

            .password-label {
                font-size: 14px;
                padding-left: 10%;
            }

            .input-container .input-wrapper {
                width: 95%;
            }

            .input-field {
                font-size: 14px;
                padding: 10px 12px 10px 40px;
            }

            .input-icon {
                font-size: 16px;
                left: 12px;
            }

            #submitResetPassword {
                width: 90% !important;
                font-size: 15px;
            }
        }



        /* เพิ่ม CSS นี้ในส่วน <style> ของไฟล์ - แก้ปัญหา Label ติด Input */

        /* ===== Reset Password Form Specific Styles ===== */
        #resetPasswordPublicModal .password-label-container {
            width: 100% !important;
            text-align: left !important;
            margin-bottom: 12px !important;
            display: block !important;
        }

        #resetPasswordPublicModal .password-label {
            display: block !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            color: var(--dark) !important;
            margin: 0 !important;
            padding-left: 5% !important;
            text-align: left !important;
            width: 100% !important;
            line-height: 1.5 !important;
        }

        #resetPasswordPublicModal .input-container {
            width: 100% !important;
            display: block !important;
            text-align: center !important;
            margin-bottom: 8px !important;
            margin-top: 8px !important;
        }

        #resetPasswordPublicModal .input-wrapper {
            position: relative !important;
            display: inline-block !important;
            width: 90% !important;
            margin: 0 auto !important;
        }

        #resetPasswordPublicModal .input-icon {
            position: absolute !important;
            left: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: var(--primary) !important;
            font-size: 18px !important;
            z-index: 2 !important;
        }

        #resetPasswordPublicModal .input-field {
            width: 100% !important;
            padding: 12px 15px 12px 45px !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-size: 16px !important;
            transition: all 0.3s ease !important;
            margin: 0 !important;
            display: block !important;
        }

        #resetPasswordPublicModal .password-hint {
            width: 100% !important;
            text-align: center !important;
            margin-top: 8px !important;
            margin-bottom: 15px !important;
            display: block !important;
        }

        #resetPasswordPublicModal .password-hint small {
            font-size: 13px !important;
            color: #6c757d !important;
            display: block !important;
            line-height: 1.4 !important;
        }

        /* ===== Form Group Spacing ===== */
        #resetPasswordPublicModal .form-group {
            margin-bottom: 25px !important;
            display: block !important;
            width: 100% !important;
        }

        #resetPasswordPublicModal .form-group.mb-3 {
            margin-bottom: 25px !important;
        }

        #resetPasswordPublicModal .form-group.mb-4 {
            margin-bottom: 30px !important;
        }

        /* ===== Password Strength Indicator ===== */
        .password-strength {
            margin: 15px 0 !important;
            display: block !important;
            width: 90% !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .strength-meter {
            height: 4px !important;
            background: #e0e0e0 !important;
            border-radius: 2px !important;
            overflow: hidden !important;
            margin-bottom: 5px !important;
        }

        .strength-fill {
            height: 100% !important;
            width: 0% !important;
            transition: all 0.3s ease !important;
            border-radius: 2px !important;
        }

        .strength-text {
            font-size: 12px !important;
            font-weight: 500 !important;
            text-align: center !important;
            display: block !important;
        }

        /* ===== Form Validation Styles ===== */
        #resetPasswordPublicModal .input-field.is-valid {
            border-color: var(--success) !important;
            box-shadow: 0 0 0 0.2rem rgba(72, 207, 173, 0.25) !important;
        }

        #resetPasswordPublicModal .input-field.is-invalid {
            border-color: var(--error) !important;
            box-shadow: 0 0 0 0.2rem rgba(252, 110, 81, 0.25) !important;
        }

        /* ===== Focus States ===== */
        #resetPasswordPublicModal .input-field:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(74, 137, 220, 0.2) !important;
            outline: none !important;
        }

        /* ===== Button Styling ===== */
        #resetPasswordPublicModal #submitResetPassword:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 15px rgba(72, 207, 173, 0.4) !important;
            filter: brightness(1.05) !important;
        }

        #resetPasswordPublicModal #submitResetPassword:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }

        /* ===== Responsive Design ===== */
        @media (max-width: 768px) {
            #resetPasswordPublicModal .password-label {
                font-size: 15px !important;
                padding-left: 8% !important;
            }

            #resetPasswordPublicModal .input-wrapper {
                width: 92% !important;
            }
        }

        @media (max-width: 576px) {
            #resetPasswordPublicModal .modal-dialog {
                margin: 10px !important;
            }

            #resetPasswordPublicModal .password-label {
                font-size: 14px !important;
                padding-left: 10% !important;
            }

            #resetPasswordPublicModal .input-wrapper {
                width: 95% !important;
            }

            #resetPasswordPublicModal .input-field {
                font-size: 14px !important;
                padding: 10px 12px 10px 40px !important;
            }

            #resetPasswordPublicModal .input-icon {
                font-size: 16px !important;
                left: 12px !important;
            }

            #resetPasswordPublicModal #submitResetPassword {
                width: 90% !important;
                font-size: 15px !important;
            }
        }

        /* ===== Clear any conflicting styles ===== */
        #resetPasswordPublicModal .form-label.text-start.w-100 {
            display: none !important;
        }

        #resetPasswordPublicModal .input-wrapper[style*="width: 90%"] {
            width: 90% !important;
            margin: 0 auto !important;
        }

        #resetPasswordPublicModal small.text-muted.mt-1.d-block {
            margin-top: 8px !important;
            text-align: center !important;
        }



        /* เพิ่ม CSS นี้หลังจาก CSS เดิมที่มีอยู่ */

        /* Enhanced Password Strength Text Styling */
        #resetPasswordPublicModal .strength-text {
            font-size: 16px !important;
            /* เพิ่มขนาดจาก 12px เป็น 16px */
            font-weight: 600 !important;
            /* เพิ่มความหนาจาก 500 เป็น 600 */
            text-align: center !important;
            display: block !important;
            margin: 5px 0 !important;
            padding: 5px 10px !important;
            border-radius: 6px !important;
            transition: all 0.3s ease !important;
            background-color: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid transparent !important;
        }

        /* สีสำหรับระดับต่างๆ */
        #resetPasswordPublicModal .strength-text.weak {
            color: #dc3545 !important;
            /* แดง - อ่อนแอ */
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-color: rgba(220, 53, 69, 0.2) !important;
        }

        #resetPasswordPublicModal .strength-text.fair {
            color: #fd7e14 !important;
            /* ส้ม - ปานกลาง */
            background-color: rgba(253, 126, 20, 0.1) !important;
            border-color: rgba(253, 126, 20, 0.2) !important;
        }

        #resetPasswordPublicModal .strength-text.good {
            color: #198754 !important;
            /* เขียวอ่อน - แข็งแกร่ง */
            background-color: rgba(25, 135, 84, 0.1) !important;
            border-color: rgba(25, 135, 84, 0.2) !important;
        }

        #resetPasswordPublicModal .strength-text.strong {
            color: #0f5132 !important;
            /* เขียวเข้ม - แข็งแกร่งมาก */
            background-color: rgba(15, 81, 50, 0.1) !important;
            border-color: rgba(15, 81, 50, 0.2) !important;
        }

        /* เพิ่มไอคอนสำหรับแต่ละระดับ */
        #resetPasswordPublicModal .strength-text::before {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
            margin-right: 8px !important;
            font-size: 14px !important;
        }

        #resetPasswordPublicModal .strength-text.weak::before {
            content: "\f071" !important;
            /* fa-exclamation-triangle */
        }

        #resetPasswordPublicModal .strength-text.fair::before {
            content: "\f06a" !important;
            /* fa-exclamation-circle */
        }

        #resetPasswordPublicModal .strength-text.good::before {
            content: "\f00c" !important;
            /* fa-check */
        }

        #resetPasswordPublicModal .strength-text.strong::before {
            content: "\f058" !important;
            /* fa-check-circle */
        }

        /* Animation สำหรับการเปลี่ยนแปลง */
        #resetPasswordPublicModal .strength-text {
            animation: strengthPulse 0.5s ease-in-out !important;
        }

        /* เพิ่ม CSS นี้สำหรับ Public Modal */
        #resetPasswordPublicModal .toggle-password {
            position: absolute !important;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #6c757d !important;
            font-size: 18px !important;
            z-index: 15 !important;
            cursor: pointer !important;
            padding: 8px !important;
            border-radius: 4px !important;
            background-color: transparent !important;
            transition: all 0.3s ease !important;
        }

        #resetPasswordPublicModal .toggle-password:hover {
            color: var(--primary) !important;
            background-color: rgba(74, 137, 220, 0.1) !important;
            transform: translateY(-50%) scale(1.1) !important;
        }

        #resetPasswordPublicModal .toggle-password:active {
            transform: translateY(-50%) scale(0.95) !important;
            background-color: rgba(74, 137, 220, 0.2) !important;
        }

        /* Toggle Password States */
        #resetPasswordPublicModal .toggle-password.fa-eye {
            color: #6c757d !important;
        }

        #resetPasswordPublicModal .toggle-password.fa-eye-slash {
            color: var(--primary) !important;
        }

        @keyframes strengthPulse {
            0% {
                transform: scale(0.95);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.02);
                opacity: 0.9;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Responsive สำหรับหน้าจอเล็ก */
        @media (max-width: 576px) {
            #resetPasswordPublicModal .strength-text {
                font-size: 14px !important;
                padding: 4px 8px !important;
            }

            #resetPasswordPublicModal .strength-text::before {
                font-size: 12px !important;
                margin-right: 6px !important;
            }
        }

        /* เพิ่ม CSS นี้หลังจาก CSS เดิมที่มีอยู่ */

        /* ===== Staff Reset Password Modal Specific Styles ===== */
        #resetPasswordStaffModal .password-label-container {
            width: 100% !important;
            text-align: left !important;
            margin-bottom: 12px !important;
            display: block !important;
            clear: both !important;
        }

        #resetPasswordStaffModal .password-label {
            display: block !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            color: var(--dark) !important;
            margin: 0 0 8px 0 !important;
            padding: 0 0 0 5% !important;
            text-align: left !important;
            width: 100% !important;
            line-height: 1.5 !important;
            float: none !important;
        }

        #resetPasswordStaffModal .input-container {
            width: 100% !important;
            display: block !important;
            text-align: center !important;
            margin: 8px 0 !important;
            clear: both !important;
            float: none !important;
        }

        #resetPasswordStaffModal .input-container .input-wrapper {
            position: relative !important;
            display: inline-block !important;
            width: 90% !important;
            margin: 0 auto !important;
            text-align: left !important;
        }

        #resetPasswordStaffModal .input-icon {
            position: absolute !important;
            left: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #6355C2 !important;
            /* สีม่วงสำหรับบุคลากร */
            font-size: 18px !important;
            z-index: 10 !important;
            pointer-events: none !important;
        }

        /* Staff Toggle Password Styling */
        #resetPasswordStaffModal .staff-toggle-password {
            position: absolute !important;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #6c757d !important;
            font-size: 18px !important;
            z-index: 15 !important;
            cursor: pointer !important;
            padding: 8px !important;
            border-radius: 4px !important;
            background-color: transparent !important;
            transition: all 0.3s ease !important;
        }

        #resetPasswordStaffModal .staff-toggle-password:hover {
            color: #6355C2 !important;
            background-color: rgba(99, 85, 194, 0.1) !important;
            transform: translateY(-50%) scale(1.1) !important;
        }

        #resetPasswordStaffModal .staff-toggle-password:active {
            transform: translateY(-50%) scale(0.95) !important;
            background-color: rgba(99, 85, 194, 0.2) !important;
        }

        /* Staff Toggle Password States */
        #resetPasswordStaffModal .staff-toggle-password.fa-eye {
            color: #6c757d !important;
        }

        #resetPasswordStaffModal .staff-toggle-password.fa-eye-slash {
            color: #6355C2 !important;
        }

        #resetPasswordStaffModal .input-field {
            width: 100% !important;
            padding: 12px 50px 12px 45px !important;
            /* เพิ่ม padding ขวากลับมา */
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-size: 16px !important;
            transition: all 0.3s ease !important;
            margin: 0 !important;
            display: block !important;
            box-sizing: border-box !important;
            background-color: #fff !important;
        }

        #resetPasswordStaffModal .input-field:focus {
            border-color: #6355C2 !important;
            /* สีม่วงสำหรับบุคลากร */
            box-shadow: 0 0 0 3px rgba(99, 85, 194, 0.2) !important;
            outline: none !important;
        }

        #resetPasswordStaffModal .password-hint {
            width: 100% !important;
            text-align: center !important;
            margin: 8px 0 15px 0 !important;
            display: block !important;
            clear: both !important;
        }

        #resetPasswordStaffModal .password-hint small {
            font-size: 13px !important;
            color: #6c757d !important;
            display: block !important;
            line-height: 1.4 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Staff Password Strength Styling */
        #resetPasswordStaffModal .password-strength-staff {
            margin: 15px auto !important;
            display: block !important;
            width: 90% !important;
            clear: both !important;
        }

        #resetPasswordStaffModal .strength-meter {
            height: 4px !important;
            background: #e0e0e0 !important;
            border-radius: 2px !important;
            overflow: hidden !important;
            margin-bottom: 10px !important;
            width: 100% !important;
        }

        #resetPasswordStaffModal .strength-fill-staff {
            height: 100% !important;
            width: 0% !important;
            transition: all 0.3s ease !important;
            border-radius: 2px !important;
        }

        #resetPasswordStaffModal .strength-text-staff {
            font-size: 16px !important;
            font-weight: 600 !important;
            text-align: center !important;
            display: block !important;
            margin: 5px 0 !important;
            padding: 8px 12px !important;
            border-radius: 6px !important;
            transition: all 0.3s ease !important;
            background-color: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid transparent !important;
        }

        #resetPasswordStaffModal .strength-text-staff::before {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
            margin-right: 8px !important;
            font-size: 14px !important;
        }

        /* Staff Password Strength Colors */
        #resetPasswordStaffModal .strength-text-staff.weak {
            color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-color: rgba(220, 53, 69, 0.2) !important;
        }

        #resetPasswordStaffModal .strength-text-staff.weak::before {
            content: "\f071" !important;
            /* fa-exclamation-triangle */
        }

        #resetPasswordStaffModal .strength-text-staff.fair {
            color: #fd7e14 !important;
            background-color: rgba(253, 126, 20, 0.1) !important;
            border-color: rgba(253, 126, 20, 0.2) !important;
        }

        #resetPasswordStaffModal .strength-text-staff.fair::before {
            content: "\f06a" !important;
            /* fa-exclamation-circle */
        }

        #resetPasswordStaffModal .strength-text-staff.good {
            color: #198754 !important;
            background-color: rgba(25, 135, 84, 0.1) !important;
            border-color: rgba(25, 135, 84, 0.2) !important;
        }

        #resetPasswordStaffModal .strength-text-staff.good::before {
            content: "\f00c" !important;
            /* fa-check */
        }

        #resetPasswordStaffModal .strength-text-staff.strong {
            color: #0f5132 !important;
            background-color: rgba(15, 81, 50, 0.1) !important;
            border-color: rgba(15, 81, 50, 0.2) !important;
        }

        #resetPasswordStaffModal .strength-text-staff.strong::before {
            content: "\f058" !important;
            /* fa-check-circle */
        }

        /* Form layout */
        #resetPasswordStaffModal .form-group {
            margin-bottom: 25px !important;
            display: block !important;
            width: 100% !important;
            overflow: hidden !important;
        }

        #resetPasswordStaffModal .form-group::after {
            content: "" !important;
            display: block !important;
            clear: both !important;
        }

        /* Button styling */
        #resetPasswordStaffModal #submitResetStaffPassword:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 15px rgba(99, 85, 194, 0.4) !important;
            filter: brightness(1.05) !important;
        }

        #resetPasswordStaffModal #submitResetStaffPassword:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }

        /* Staff Modal specific validation colors */
        #resetPasswordStaffModal .input-field.is-valid {
            border-color: var(--success) !important;
            box-shadow: 0 0 0 0.2rem rgba(72, 207, 173, 0.25) !important;
        }

        #resetPasswordStaffModal .input-field.is-invalid {
            border-color: var(--error) !important;
            box-shadow: 0 0 0 0.2rem rgba(252, 110, 81, 0.25) !important;
        }

        /* Responsive adjustments for staff modal */
        @media (max-width: 768px) {
            #resetPasswordStaffModal .password-label {
                font-size: 15px !important;
                padding-left: 8% !important;
            }

            #resetPasswordStaffModal .input-container .input-wrapper {
                width: 92% !important;
            }
        }

        @media (max-width: 576px) {
            #resetPasswordStaffModal .modal-dialog {
                margin: 10px !important;
            }

            #resetPasswordStaffModal .password-label {
                font-size: 14px !important;
                padding-left: 10% !important;
            }

            #resetPasswordStaffModal .input-container .input-wrapper {
                width: 95% !important;
            }

            #resetPasswordStaffModal .input-field {
                font-size: 14px !important;
                padding: 10px 42px 10px 38px !important;
                /* เพิ่ม padding ขวากลับมา */
            }

            #resetPasswordStaffModal .input-icon,
            #resetPasswordStaffModal .staff-toggle-password {
                font-size: 15px !important;
            }

            #resetPasswordStaffModal .input-icon {
                left: 10px !important;
            }

            #resetPasswordStaffModal .staff-toggle-password {
                right: 10px !important;
            }

            #resetPasswordStaffModal #submitResetStaffPassword {
                width: 90% !important;
                font-size: 15px !important;
            }
        }

        /* ===== เพิ่ม Keyboard Support สำหรับแสดง/ซ่อนรหัสผ่าน ===== */
        #resetPasswordStaffModal .input-field {
            position: relative !important;
        }


        /* reCAPTCHA Notice - ปรับให้มนๆ ไม่มีเหลี่ยม */
        .recaptcha-notice {
            background: linear-gradient(135deg, #E8F5E8 0%, #F0FFF0 100%);
            border: 1px solid rgba(52, 199, 89, 0.2);
            border-radius: 20px;
            /* เพิ่มความโค้งมน จาก var(--border-radius) เป็น 20px */
            padding: 20px 24px;
            /* เพิ่ม padding ด้านข้างเล็กน้อย */
            margin: 20px 0;
            text-align: center;
            color: var(--success-color);
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(52, 199, 89, 0.1);
            /* เพิ่ม shadow ให้นุ่มนวลขึ้น */
            transition: all 0.3s ease;
            /* เพิ่ม transition สำหรับ hover effect */
        }

        .recaptcha-notice:hover {
            transform: translateY(-2px);
            /* เพิ่ม hover effect */
            box-shadow: 0 12px 35px rgba(52, 199, 89, 0.15);
        }

        .recaptcha-notice i {
            margin-right: 8px;
            color: var(--success-color);
        }

        .recaptcha-notice a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            /* เพิ่มความมนให้ลิงก์ */
            padding: 2px 6px;
            transition: all 0.2s ease;
        }

        .recaptcha-notice a:hover {
            background-color: rgba(74, 137, 220, 0.1);
            text-decoration: underline;
        }
    </style>
	
	<style>
/* ======================== TOUR GUIDE STYLES ======================== */

.tour-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    z-index: 9998;
    display: none;
    animation: fadeIn 0.3s ease;
}

.tour-overlay.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.spotlight {
    position: fixed;
    background: transparent; /* โปร่งใส */
    border: none; /* ✅ ลบกรอบออก */
    border-radius: 0;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.75); /* เหลือแค่พื้นหลังมืด */
    z-index: 9999;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
    opacity: 0; /* ✅ ซ่อนทั้งหมด ไม่ให้เห็นเลย */
}

.spotlight.pulse {
    animation: none; /* ✅ ปิด animation */
}

/* ลบ animation ที่ไม่ใช้แล้ว */
@keyframes pulse-spotlight-border {
    /* ไม่ต้องใช้แล้ว */
}

/* Tooltip Box */
.tour-tooltip {
    position: fixed;
    background: white;
    border-radius: 16px;
    padding: 24px 28px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
    z-index: 10000;
    min-width: 320px;
    max-width: 450px;
    animation: tooltipSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes tooltipSlideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tour-tooltip-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.tour-tooltip-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4A89DC 0%, #3D71BA 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
}

.tour-tooltip-icon.warning {
    background: linear-gradient(135deg, #F6BB42 0%, #E8AA2E 100%);
}

.tour-tooltip-icon.success {
    background: linear-gradient(135deg, #48CFAD 0%, #8CC152 100%);
}

.tour-tooltip-title {
    font-size: 20px;
    font-weight: 600;
    color: #2d3436;
    flex: 1;
}

.tour-tooltip-content {
    color: #5a6c7d;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 20px;
}

.tour-tooltip-content strong {
    color: #2d3436;
}

.tour-tooltip-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #e9ecef;
}

.tour-progress {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #7f8c8d;
    font-size: 14px;
}

.tour-progress-dots {
    display: flex;
    gap: 6px;
}

.tour-progress-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #dee2e6;
    transition: all 0.3s;
}

.tour-progress-dot.active {
    background: #4A89DC;
    width: 24px;
    border-radius: 4px;
}

.tour-buttons {
    display: flex;
    gap: 10px;
}

.tour-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tour-btn-skip {
    background: #e9ecef;
    color: #6c757d;
}

.tour-btn-skip:hover {
    background: #dee2e6;
}

.tour-btn-prev {
    background: white;
    color: #4A89DC;
    border: 2px solid #4A89DC;
}

.tour-btn-prev:hover {
    background: #4A89DC;
    color: white;
}

.tour-btn-next,
.tour-btn-finish {
    background: linear-gradient(135deg, #4A89DC 0%, #3D71BA 100%);
    color: white;
}

.tour-btn-next:hover,
.tour-btn-finish:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 137, 220, 0.4);
}

/* Arrow Pointer */
.tour-arrow {
    display: none !important; /* ✅ ซ่อนลูกศรทั้งหมด */
}

/* ลบ animation ที่ไม่ใช้แล้ว */
@keyframes float-arrow {
    /* ไม่ต้องใช้แล้ว */
}

@keyframes rotate-arrow {
    /* ไม่ต้องใช้แล้ว */
}

/* Label Box */
.tour-label {
    position: fixed;
    background: linear-gradient(135deg, #4A89DC 0%, #3D71BA 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    z-index: 10001;
    box-shadow: 0 8px 24px rgba(74, 137, 220, 0.4);
    pointer-events: none;
    animation: labelPulse 2s ease-in-out infinite;
    white-space: nowrap;
}

@keyframes labelPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.tour-label::before {
    content: '';
    position: absolute;
    width: 12px;
    height: 12px;
    background: inherit;
    transform: rotate(45deg);
}

.tour-label.top::before {
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
}

.tour-label.bottom::before {
    top: -6px;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
}

.tour-label.left::before {
    right: -6px;
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
}

.tour-label.right::before {
    left: -6px;
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
}

/* Start Tour Button */
.start-tour-btn {
    position: fixed;
    top: 30px;;
    right: 30px;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4A89DC 0%, #3D71BA 100%);
    color: white;
    border: none;
    cursor: pointer;
    font-size: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(74, 137, 220, 0.4);
    transition: all 0.3s;
    z-index: 999;
    animation: startBtnPulse 2s infinite;
}

.start-tour-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 10px 30px rgba(74, 137, 220, 0.6);
}

@keyframes startBtnPulse {
    0%, 100% {
        box-shadow: 0 8px 24px rgba(74, 137, 220, 0.4);
    }
    50% {
        box-shadow: 0 8px 24px rgba(74, 137, 220, 0.6),
                    0 0 0 20px rgba(74, 137, 220, 0.1);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .tour-tooltip {
        min-width: 280px;
        max-width: calc(100vw - 40px);
        padding: 20px;
    }

    .tour-arrow {
        width: 60px;
        height: 60px;
    }

    .start-tour-btn {
        width: 60px;
        height: 60px;
        font-size: 24px;
        top: 20px;
        right: 20px;
    }

    .tour-label {
        font-size: 13px;
        padding: 10px 16px;
    }
}
</style>
	


</head>

<?php
/**
 * 🎯 AUTO REDIRECT SYSTEM สำหรับหน้า LOGIN
 * ตรวจสอบสถานะการ login และ redirect อัตโนมัติ
 * ให้วางโค้ดนี้หลังจาก <head> และก่อน <body>
 */

// ตรวจสอบ Public User (ประชาชน) ที่ login อยู่แล้ว
if ($this->session->userdata('mp_id')) {
    // ✅ ประชาชน login อยู่แล้ว - redirect ไปหน้าบริการ
    log_message('info', 'Public user (mp_id: ' . $this->session->userdata('mp_id') . ') already logged in, redirecting to service systems');

    // เพิ่ม flashdata เพื่อแจ้งให้ทราบ
    $this->session->set_flashdata('info_message', 'คุณเข้าสู่ระบบอยู่แล้ว');

    redirect('Pages/service_systems');
    exit; // หยุดการทำงานของสคริปต์
}

// ตรวจสอบ Staff User (บุคลากรภายใน) ที่ login อยู่แล้ว
if ($this->session->userdata('m_id')) {
    // ✅ บุคลากรภายใน login อยู่แล้ว - redirect ไป dashboard
    log_message('info', 'Staff user (m_id: ' . $this->session->userdata('m_id') . ') already logged in, redirecting to dashboard');

    // ตรวจสอบสถานะบัญชี (ถ้าต้องการ)
    $staff_member = $this->db->select('m_id, m_status, m_fname, m_lname')
        ->from('tbl_member')
        ->where('m_id', $this->session->userdata('m_id'))
        ->get()
        ->row();

    if ($staff_member && $staff_member->m_status == '1') {
        // บัญชียังใช้งานได้ - redirect ไป dashboard
        $this->session->set_flashdata('success_message', 'ยินดีต้อนรับกลับ ' . $staff_member->m_fname . ' ' . $staff_member->m_lname);
        redirect('User/choice');
        exit;
    } else {
        // บัญชีถูกปิดการใช้งาน - ลบ session และแสดงข้อความ
        log_message('warning', 'Inactive staff account (m_id: ' . $this->session->userdata('m_id') . ') attempted auto-login');

        $this->session->sess_destroy();
        $this->session->set_flashdata('error_message', 'บัญชีของคุณถูกปิดการใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
        // ให้แสดงหน้า login ต่อไป (ไม่ redirect)
    }
}

// 📝 บันทึก log การเข้าถึงหน้า login (สำหรับผู้ที่ยังไม่ได้ login)
$access_log = [
    'page_accessed' => 'login',
    'ip_address' => $this->input->ip_address(),
    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    'access_time' => date('Y-m-d H:i:s'),
    'session_id' => session_id()
];

// บันทึกลง log file
log_message('info', 'Login page accessed from IP: ' . $this->input->ip_address());

// ตรวจสอบ URL parameters สำหรับการจัดการพิเศษ
$redirect_after_login = $this->input->get('redirect');
if ($redirect_after_login) {
    // เก็บ redirect URL ไว้ใน session สำหรับใช้หลัง login สำเร็จ
    $this->session->set_userdata('redirect_after_login', $redirect_after_login);
}

// ตรวจสอบ Flash Messages จากการ redirect
$login_errors = $this->session->flashdata('login_error');
$login_messages = $this->session->flashdata('login_message');

?>

<script>
    /**
     * 🔧 JavaScript สำหรับจัดการ Flash Messages
     */
    document.addEventListener('DOMContentLoaded', function () {
        // แสดง Error Messages
        <?php if ($login_errors): ?>
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถเข้าสู่ระบบได้',
                text: '<?php echo addslashes($login_errors); ?>',
                confirmButtonText: 'ตกลง'
            });
        <?php endif; ?>

        // แสดง Info Messages
        <?php if ($login_messages): ?>
            Swal.fire({
                icon: 'info',
                title: 'แจ้งเตือน',
                text: '<?php echo addslashes($login_messages); ?>',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        <?php endif; ?>

        // ตรวจสอบสถานะการ logout
        <?php if ($this->session->flashdata('logout_success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'ออกจากระบบสำเร็จ',
                text: 'ขอบคุณที่ใช้บริการ',
                timer: 2000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        <?php endif; ?>

        console.log('🔍 Login page loaded - checking session status');
        console.log('User not logged in - showing login form');
    });

    /**
     * 🛡️ ฟังก์ชันเพิ่มเติมสำหรับความปลอดภัย
     */
    // ป้องกันการ back button หลัง logout
    if (window.history && window.history.pushState) {
        window.history.pushState('', null, window.location.href);
        window.addEventListener('popstate', function () {
            // ตรวจสอบว่ามาจากการ logout หรือไม่
            <?php if ($this->session->flashdata('logout_success')): ?>
                Swal.fire({
                    title: 'คุณได้ออกจากระบบแล้ว',
                    text: 'กรุณาเข้าสู่ระบบใหม่',
                    icon: 'info',
                    confirmButtonText: 'ตกลง'
                });
            <?php endif; ?>
        });
    }

    // เพิ่ม protection จากการ iframe embedding
    if (window.top !== window.self) {
        window.top.location = window.self.location;
    }
</script>

<body>
	

<!-- Start Tour Button -->
<div class="tour-overlay" id="tourOverlay"></div>
<button class="start-tour-btn" onclick="startTour()"><i class="fas fa-question"></i></button>

	
	
    <!-- Background Overlay -->
    <div class="bg-overlay"></div>

    <!-- Floating Particles -->
    <div class="floating-particles">
        <?php for ($i = 1; $i <= 15; $i++): ?>
            <div class="particle"></div>
        <?php endfor; ?>
    </div>

    <div class="container">
        <!-- Login Container -->
        <div class="login-container">
            <div class="login-header">
                <img src="<?php echo base_url("docs/logo.png"); ?>" alt="โลโก้" class="login-logo">
                <h1 class="login-title"><?php echo get_config_value('fname'); ?></h1>
                <p class="login-subtitle">ระบบบริการออนไลน์ ก้าวไปสู่ระบบราชการ 4.0</p>
            </div>

            <!-- Login Tabs -->
    <div class="login-tabs" data-tour="step1">
                <button class="tab-btn active" data-tab="citizen">
                    <i class="fas fa-users"></i> สำหรับประชาชน
                </button>
    <button class="tab-btn" data-tab="staff" data-tour="step2">
                    <i class="fas fa-user-tie"></i> สำหรับบุคลากรภายใน
                </button>
            </div>

            <!-- Login Card -->
            <div class="login-card card-3d-wrapper">
                <!-- Citizen Login Form -->
                <div class="login-form active" id="citizen-form">
                    <h2 class="form-title">เข้าสู่ระบบสำหรับประชาชน</h2>
                    <form id="citizenLoginForm" action="<?php echo site_url('auth_api/check_login'); ?>" method="post"
                        class="form-horizontal">
                        <input type="hidden" name="user_type" value="public">
                        <div class="form-group">
                            <label class="form-label"><span
                                    style="display:inline-block; width:60px; text-align:left;">อีเมล</span> <span
                                    class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="mp_email" class="input-field" required
                                    placeholder="example@youremail.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <span style="display:inline-block; width:60px; text-align:left;">รหัสผ่าน</span>
                                <span class="required-star">*</span>
                            </label>
                            <div class="input-wrapper" style="position: relative;">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="mp_password" id="citizenPassword" class="input-field" required
                                    placeholder="รหัสผ่านของคุณ">
                                <i class="fas fa-eye toggle-password" id="toggleCitizenPassword"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                            </div>
                        </div>

                        <div class="forgot-link">
                            <a href="#" class="forgotpwd-public" data-bs-toggle="modal"
                                data-bs-target="#forgotPasswordPublicModal">ลืมรหัสผ่าน?</a>
                        </div>

                        <button type="submit" class="login-btn" data-action="submit" data-callback="onSubmit"
                            data-sitekey="<?php echo get_config_value('recaptcha'); ?>">
                            <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                        </button>

                        <div class="register-link">
                            ยังไม่เป็นสมาชิก? <a
                                href="<?php echo site_url('Auth_public_mem/register'); ?>">สมัครสมาชิกที่นี่</a>
                        </div>
                    </form>
                </div>

                <!-- Staff Login Form -->
                <div class="login-form" id="staff-form">
                    <h2 class="form-title">เข้าสู่ระบบสำหรับบุคลากรภายใน</h2>
                    <form id="staffLoginForm" action="<?php echo site_url('User/check2'); ?>" method="post">
                        <input type="hidden" name="user_type" value="staff">
                        <div class="form-group">
                            <label class="form-label"><span
                                    style="display:inline-block; width:80px; text-align:left;">ชื่อผู้ใช้งาน</span>
                                <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="m_username" class="input-field" required
                                    placeholder="อีเมลของคุณ : test@example.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <span style="display:inline-block; width:80px; text-align:left;">รหัสผ่าน</span>
                                <span class="required-star">*</span>
                            </label>
                            <div class="input-wrapper" style="position: relative;">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="m_password" id="staffPassword" class="input-field" required
                                    placeholder="รหัสผ่านของคุณ">
                                <i class="fas fa-eye toggle-password" id="toggleStaffPassword"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                            </div>
                        </div>

                        <div class="forgot-link">
                            <a href="#" class="forgotpwd" data-bs-toggle="modal"
                                data-bs-target="#forgotPasswordModal">ลืมรหัสผ่าน?</a>
                        </div>

                        <button type="submit" class="login-btn" data-action="submit" data-callback="onSubmit"
                            data-sitekey="<?php echo get_config_value('recaptcha'); ?>">
                            <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                        </button>
                    </form>
                </div>
            </div>

            <div class="footer-text">
                <div class="recaptcha-notice">
                    <i class="fas fa-shield-alt"></i>
                    เว็บไซต์นี้ได้รับการปกป้องและคุ้มครองโดย reCAPTCHA และ 2FA โปรดอ่าน 
                    <a href="policy/privacy" target="_blank"
                        style="color: var(--primary-color);">นโยบายความเป็นส่วนตัว</a>
                    และ
                    <a href="policy/security" target="_blank"
                        style="color: var(--primary-color);">ความมั่นคงปลอดภัยเว็บไซต์
</a>
                    
                </div>
                
            </div>
        </div>

        <!-- Google Authenticator Modal - ปรับปรุงใหม่ -->
        <div class="modal fade" id="googleAuthModal" tabindex="-1" aria-labelledby="googleAuthModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"
                    style="border-radius: 15px; background: var(--form-bg); box-shadow: 0 15px 35px var(--shadow-dark);">
                    <div class="modal-header border-0 text-center">
                        <div class="w-100">
                            <div class="app-icon">
                                <i class="bi bi-shield-check text-white" style="font-size: 2rem;"></i>
                            </div>
                            <div class="modal-title-wrapper">
                                <h5 class="modal-title" id="googleAuthModalLabel"
                                    style="color: var(--primary); font-weight: 600; font-size: 1.5rem;">
                                    ยืนยันตัวตน
                                </h5>
                            </div>
                            <p class="mb-0 opacity-75" style="color: var(--text-color);">Google Authenticator</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="instruction instruction-large">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-phone me-2 text-primary"></i>
                                <strong>วิธีการใช้งาน:</strong>
                            </div>
                            <ol class="mb-0 small">
                                <li>เปิดแอป Google Authenticator บนมือถือ</li>
                                <li>ค้นหาชื่อบัญชีของคุณ</li>
                                <li>กรอกรหัส 6 หลักในช่องด้านล่าง</li>
                                <li>รหัส OTP จะเปลี่ยนทุก 30 วินาที เพื่อความปลอดภัยสูงสุด</li>
                            </ol>
                        </div>

                        <form id="otpVerifyForm" action="<?php echo site_url('User/verify_otp'); ?>" method="post">
                            <div class="text-center mb-4">
                                <label class="form-label fw-bold otp-label-large">กรอกรหัส 6 หลัก</label>

                                <div class="d-flex justify-content-center">
                                    <input type="text" class="otp-input" maxlength="1" data-index="0">
                                    <input type="text" class="otp-input" maxlength="1" data-index="1">
                                    <input type="text" class="otp-input" maxlength="1" data-index="2">
                                    <input type="text" class="otp-input" maxlength="1" data-index="3">
                                    <input type="text" class="otp-input" maxlength="1" data-index="4">
                                    <input type="text" class="otp-input" maxlength="1" data-index="5">
                                </div>
                                <input type="hidden" name="otp" id="otpValue">
                                <input type="hidden" name="remember_device" id="rememberDeviceValue">
                            </div>

                            <!-- เพิ่ม Checkbox สำหรับจำเครื่อง -->
                            <div class="text-center mb-3">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input" type="checkbox" id="rememberDevice"
                                        onchange="updateRememberDevice()">
                                    <label class="form-check-label" for="rememberDevice"
                                        style="font-size: 16px; color: var(--text-color);">
                                        <i class="bi bi-shield-lock me-1"></i>
                                        จำเครื่องนี้ไว้เพื่อไม่ต้องกรอกรหัส 30 วัน
                                    </label>
                                </div>
                                <br>
                                <small class="text-muted" style="font-size: 14px;">
                                    <i class="bi bi-exclamation-triangle me-1" style="color: #ffc107;"></i>
                                    อย่าเลือกหากใช้เครื่องสาธารณะ
                                </small>
                            </div>

                            <!-- แทนที่ส่วนนับถอยหลังด้วยข้อความความปลอดภัย -->
                            <div class="text-center mb-3">
                                <div class="security-info" id="securityInfo" onclick="submitOTPWhenReady()">
                                    <p class="security-text" id="securityText">
                                        <i class="bi bi-shield-check pulse-icon"></i>
                                        การยืนยันตัวตนเพื่อความปลอดภัย
                                    </p>
                                </div>

                            </div>

                            <!-- ปุ่มยกเลิก - แสดงเสมอ -->
                            <div class="button-container"
                                style="display: flex; justify-content: center; width: 100%; margin: 3rem 0 1rem 0;">
                                <button type="button" id="cancelBtn" class="btn btn-outline-secondary"
                                    onclick="cancelGoogleAuth()"
                                    style="padding: 14px 30px; border-radius: 8px; font-weight: 400; min-width: 180px; border-color: #e9ecef; color: #6c757d; background-color: transparent;">
                                    <i class="bi bi-arrow-left me-2" style="opacity: 0.7;"></i>ยกเลิก
                                </button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>




        <!-- เก็บ Modal ลืมรหัสผ่านต่างๆ ไว้เหมือนเดิม -->
        <!-- start modal pop up ลืมรหัสผ่าน ------------------------------------------- -->
        <!-- Modal ลืมรหัสผ่านเจ้าหน้าที่ -->
        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"
                    style="border-radius: 15px; background: var(--form-bg); box-shadow: 0 15px 35px var(--shadow-dark);">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="forgotPasswordModalLabel"
                            style="color: var(--primary); font-weight: 600; font-size: 1.5rem;">ลืมรหัสผ่าน
                            (สำหรับบุคลากรภายใน)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="<?php echo base_url("docs/logo.png"); ?>" width="100" height="100" class="mb-3"
                            style="filter: drop-shadow(0 3px 8px var(--shadow));">
                        <p class="text-muted mb-4">กรุณากรอกอีเมลที่ใช้ลงทะเบียน
                            เราจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปให้คุณ</p>

                        <form id="forgotPasswordForm">
                            <div class="form-group mb-4">
                                <div class="input-wrapper" style="width: 90%; margin: 0 auto;">
                                    <i class="fas fa-envelope input-icon" style="color: var(--primary);"></i>
                                    <input type="email" name="email" id="modal-email" class="input-field" required
                                        placeholder="กรุณากรอกอีเมล"
                                        style="padding: 12px 15px 12px 45px; border: 1px solid var(--border-color); border-radius: 8px;">
                                </div>
                            </div>

                            <!-- Loading indicator -->
                            <div id="loadingIndicator" style="display: none; text-align: center; margin: 15px 0;">
                                <div class="spinner-border text-primary" role="status"
                                    style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">กำลังส่งอีเมล กรุณารอสักครู่...</p>
                            </div>

                            <!-- Success message -->
                            <div class="alert alert-success mt-3" id="successAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(72, 207, 173, 0.2); border-color: var(--success); color: var(--dark);">
                                <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                                ส่งอีเมลสำเร็จ! กรุณาตรวจสอบอีเมลของคุณเพื่อรีเซ็ตรหัสผ่าน
                            </div>

                            <!-- Error message -->
                            <div class="alert alert-danger mt-3" id="errorAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(252, 110, 81, 0.2); border-color: var(--error); color: var(--dark);">
                                <i class="fas fa-exclamation-circle me-2" style="color: var(--error);"></i>
                                <span id="errorMessage">เกิดข้อผิดพลาด</span>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" id="submitForgotPassword"
                                style="display: block; width: 80%; padding: 14px; margin: 20px auto 0; background: linear-gradient(to right, var(--primary), var(--primary-dark)); border: none; border-radius: 8px; color: white; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(74, 137, 220, 0.3);">
                                <i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal ลืมรหัสผ่านประชาชน -->
        <div class="modal fade" id="forgotPasswordPublicModal" tabindex="-1"
            aria-labelledby="forgotPasswordPublicModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"
                    style="border-radius: 15px; background: var(--form-bg); box-shadow: 0 15px 35px var(--shadow-dark);">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="forgotPasswordPublicModalLabel"
                            style="color: var(--primary); font-weight: 600; font-size: 1.5rem;">ลืมรหัสผ่าน
                            (สำหรับประชาชน)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="<?php echo base_url("docs/logo.png"); ?>" width="100" height="100" class="mb-3"
                            style="filter: drop-shadow(0 3px 8px var(--shadow));">
                        <p class="text-muted mb-4">กรุณากรอกอีเมลที่ใช้ลงทะเบียน
                            เราจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปให้คุณ</p>

                        <form id="forgotPasswordPublicForm">
                            <div class="form-group mb-4">
                                <div class="input-wrapper" style="width: 90%; margin: 0 auto;">
                                    <i class="fas fa-envelope input-icon" style="color: var(--primary);"></i>
                                    <input type="email" name="email" id="modal-public-email" class="input-field"
                                        required placeholder="กรุณากรอกอีเมล"
                                        style="padding: 12px 15px 12px 45px; border: 1px solid var(--border-color); border-radius: 8px;">
                                </div>
                            </div>

                            <!-- Loading indicator -->
                            <div id="loadingPublicIndicator" style="display: none; text-align: center; margin: 15px 0;">
                                <div class="spinner-border text-primary" role="status"
                                    style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">กำลังส่งอีเมล กรุณารอสักครู่...</p>
                            </div>

                            <!-- Success message -->
                            <div class="alert alert-success mt-3" id="successPublicAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(72, 207, 173, 0.2); border-color: var(--success); color: var(--dark);">
                                <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                                ส่งอีเมลสำเร็จ! กรุณาตรวจสอบอีเมลของคุณเพื่อรีเซ็ตรหัสผ่าน
                            </div>

                            <!-- Error message -->
                            <div class="alert alert-danger mt-3" id="errorPublicAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(252, 110, 81, 0.2); border-color: var(--error); color: var(--dark);">
                                <i class="fas fa-exclamation-circle me-2" style="color: var(--error);"></i>
                                <span id="errorPublicMessage">เกิดข้อผิดพลาด</span>
                            </div>

                            <!-- Submit button -->
                            <button type="button" id="submitForgotPasswordPublic"
                                onclick="submitForgotPasswordPublicForm()"
                                style="display: block; width: 80%; padding: 14px; margin: 20px auto 0; background: linear-gradient(to right, var(--primary), var(--primary-dark)); border: none; border-radius: 8px; color: white; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(74, 137, 220, 0.3);">
                                <i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Reset Password สำหรับประชาชน -->
        <div class="modal fade" id="resetPasswordPublicModal" tabindex="-1"
            aria-labelledby="resetPasswordPublicModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"
                    style="border-radius: 15px; background: var(--form-bg); box-shadow: 0 15px 35px var(--shadow-dark);">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="resetPasswordPublicModalLabel"
                            style="color: var(--primary); font-weight: 600; font-size: 1.5rem;">
                            <i class="fas fa-key me-2"></i>เปลี่ยนรหัสผ่านใหม่
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="<?php echo base_url("docs/logo.png"); ?>" width="100" height="100" class="mb-3"
                            style="filter: drop-shadow(0 3px 8px var(--shadow));">

                        <div class="alert alert-info"
                            style="border-radius: 8px; background-color: rgba(74, 137, 220, 0.1); border-color: var(--primary); color: var(--dark);">
                            <i class="fas fa-info-circle me-2" style="color: var(--primary);"></i>
                            กรุณากำหนดรหัสผ่านใหม่สำหรับบัญชี: <strong id="resetEmailDisplay"></strong>
                        </div>

                        <form id="resetPasswordPublicForm">
                            <input type="hidden" id="resetPublicEmail" name="email">
                            <input type="hidden" id="resetPublicToken" name="reset_token">

                            <!-- รหัสผ่านใหม่ -->
                            <div class="form-group mb-3">
                                <div class="password-label-container">
                                    <label class="form-label password-label">รหัสผ่านใหม่</label>
                                </div>
                                <div class="input-container">
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="new_password" id="newPassword" class="input-field"
                                            required placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)"
                                            minlength="8">
                                        <i class="fas fa-eye toggle-password" data-target="newPassword"
                                            title="แสดง/ซ่อนรหัสผ่าน"></i>
                                    </div>
                                </div>
                                <div class="password-hint">
                                    <small class="text-muted">รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร</small>
                                </div>
                            </div>

                            <!-- ยืนยันรหัสผ่านใหม่ -->
                            <div class="form-group mb-4">
                                <div class="password-label-container">
                                    <label class="form-label password-label">ยืนยันรหัสผ่านใหม่</label>
                                </div>
                                <div class="input-container">
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="confirm_password" id="confirmPassword"
                                            class="input-field" required placeholder="กรอกรหัสผ่านใหม่อีกครั้ง"
                                            minlength="8">
                                        <i class="fas fa-eye toggle-password" data-target="confirmPassword"
                                            title="แสดง/ซ่อนรหัสผ่าน"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Password strength indicator -->
                            <div class="password-strength mb-3" style="display: none;">
                                <div class="strength-meter"
                                    style="height: 4px; background: #e0e0e0; border-radius: 2px; overflow: hidden;">
                                    <div class="strength-fill"
                                        style="height: 100%; width: 0%; transition: all 0.3s ease; border-radius: 2px;">
                                    </div>
                                </div>
                                <small class="strength-text text-muted mt-1 d-block"></small>
                            </div>

                            <!-- Loading indicator -->
                            <div id="resetLoadingIndicator" style="display: none; text-align: center; margin: 15px 0;">
                                <div class="spinner-border text-primary" role="status"
                                    style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">กำลังเปลี่ยนรหัสผ่าน กรุณารอสักครู่...</p>
                            </div>

                            <!-- Success message -->
                            <div class="alert alert-success mt-3" id="resetSuccessAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(72, 207, 173, 0.2); border-color: var(--success); color: var(--dark);">
                                <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                                เปลี่ยนรหัสผ่านสำเร็จ! กำลังนำคุณไปหน้าเข้าสู่ระบบ...
                            </div>

                            <!-- Error message -->
                            <div class="alert alert-danger mt-3" id="resetErrorAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(252, 110, 81, 0.2); border-color: var(--error); color: var(--dark);">
                                <i class="fas fa-exclamation-circle me-2" style="color: var(--error);"></i>
                                <span id="resetErrorMessage">เกิดข้อผิดพลาด</span>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" id="submitResetPassword"
                                style="display: block; width: 80%; padding: 14px; margin: 20px auto 0; background: linear-gradient(to right, var(--success), var(--secondary-dark)); border: none; border-radius: 8px; color: white; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(72, 207, 173, 0.3);">
                                <i class="fas fa-save me-2"></i> บันทึกรหัสผ่านใหม่
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Reset Password สำหรับบุคลากรภายใน -->
        <div class="modal fade" id="resetPasswordStaffModal" tabindex="-1"
            aria-labelledby="resetPasswordStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"
                    style="border-radius: 15px; background: var(--form-bg); box-shadow: 0 15px 35px var(--shadow-dark);">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="resetPasswordStaffModalLabel"
                            style="color: var(--primary); font-weight: 600; font-size: 1.5rem;">
                            <i class="fas fa-key me-2"></i>เปลี่ยนรหัสผ่านใหม่ (บุคลากรภายใน)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="<?php echo base_url("docs/logo.png"); ?>" width="100" height="100" class="mb-3"
                            style="filter: drop-shadow(0 3px 8px var(--shadow));">

                        <div class="alert alert-info"
                            style="border-radius: 8px; background-color: rgba(99, 85, 194, 0.1); border-color: #6355C2; color: var(--dark);">
                            <i class="fas fa-user-tie me-2" style="color: #6355C2;"></i>
                            กรุณากำหนดรหัสผ่านใหม่สำหรับบัญชี: <strong id="resetStaffEmailDisplay"></strong>
                        </div>

                        <form id="resetPasswordStaffForm">
                            <input type="hidden" id="resetStaffEmail" name="email">
                            <input type="hidden" id="resetStaffToken" name="reset_token">

                            <!-- รหัสผ่านใหม่ -->
                            <div class="form-group mb-3">
                                <div class="password-label-container">
                                    <label class="form-label password-label">รหัสผ่านใหม่</label>
                                </div>
                                <div class="input-container">
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="new_password" id="newStaffPassword"
                                            class="input-field" required
                                            placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)" minlength="8">
                                        <i class="fas fa-eye toggle-password staff-toggle-password"
                                            data-target="newStaffPassword" title="แสดง/ซ่อนรหัสผ่าน"></i>
                                    </div>
                                </div>
                                <div class="password-hint">
                                    <small class="text-muted">รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร</small>
                                </div>
                            </div>

                            <!-- ยืนยันรหัสผ่านใหม่ -->
                            <div class="form-group mb-4">
                                <div class="password-label-container">
                                    <label class="form-label password-label">ยืนยันรหัสผ่านใหม่</label>
                                </div>
                                <div class="input-container">
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="confirm_password" id="confirmStaffPassword"
                                            class="input-field" required placeholder="กรอกรหัสผ่านใหม่อีกครั้ง"
                                            minlength="8">
                                        <i class="fas fa-eye toggle-password staff-toggle-password"
                                            data-target="confirmStaffPassword" title="แสดง/ซ่อนรหัสผ่าน"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Password strength indicator -->
                            <div class="password-strength-staff mb-3" style="display: none;">
                                <div class="strength-meter"
                                    style="height: 4px; background: #e0e0e0; border-radius: 2px; overflow: hidden;">
                                    <div class="strength-fill-staff"
                                        style="height: 100%; width: 0%; transition: all 0.3s ease; border-radius: 2px;">
                                    </div>
                                </div>
                                <small class="strength-text-staff text-muted mt-1 d-block"></small>
                            </div>

                            <!-- Loading indicator -->
                            <div id="resetStaffLoadingIndicator"
                                style="display: none; text-align: center; margin: 15px 0;">
                                <div class="spinner-border text-primary" role="status"
                                    style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">กำลังเปลี่ยนรหัสผ่าน กรุณารอสักครู่...</p>
                            </div>

                            <!-- Success message -->
                            <div class="alert alert-success mt-3" id="resetStaffSuccessAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(72, 207, 173, 0.2); border-color: var(--success); color: var(--dark);">
                                <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                                เปลี่ยนรหัสผ่านสำเร็จ! กำลังนำคุณไปหน้าเข้าสู่ระบบ...
                            </div>

                            <!-- Error message -->
                            <div class="alert alert-danger mt-3" id="resetStaffErrorAlert"
                                style="display: none; border-radius: 8px; background-color: rgba(252, 110, 81, 0.2); border-color: var(--error); color: var(--dark);">
                                <i class="fas fa-exclamation-circle me-2" style="color: var(--error);"></i>
                                <span id="resetStaffErrorMessage">เกิดข้อผิดพลาด</span>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" id="submitResetStaffPassword"
                                style="display: block; width: 80%; padding: 14px; margin: 20px auto 0; background: linear-gradient(to right, #6355C2, #5a4fcf); border: none; border-radius: 8px; color: white; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(99, 85, 194, 0.3);">
                                <i class="fas fa-save me-2"></i> บันทึกรหัสผ่านใหม่
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



		
		
		
		
		<!-- ✅ กระทู้แนะนำจาก Webboard - Button Toggle Style -->
    <div class="webboard-section" data-tour="step3" style="margin: 0rem 0; padding: 0rem 0;">
    <div class="container">
        <!-- Header -->
        <div class="section-header">
            <h3>
                <i class="fas fa-comments animated-icon" ></i>
                <span>กระทู้แนะนำจากกระดานสนทนาภายในเครือข่าย</span>
            </h3>
            <p>แจ้งข่าว ประชาสัมพันธ์ แบ่งปันความรู้และประสบการณ์ร่วมกันในเครือข่าย<br>(สำหรับบุคลากรภายใน)</p>
			
        </div>
        
        <!-- Toggle Buttons -->
        <div class="toggle-buttons">
            <button class="toggle-btn active" data-type="latest">
                <i class="fas fa-clock tab-icon-clock"></i>
                <span>กระทู้ล่าสุด</span>
            </button>
            <button class="toggle-btn" data-type="hot">
                <i class="fas fa-fire tab-icon-fire"></i>
                <span>กระทู้ Hot</span>
            </button>
        </div>
        
        <!-- Posts Content -->
<div class="webboard-posts-container">
    <!-- Loading -->
    <div id="posts-loading" class="loading-state">
        <div class="spinner-border spinner-border-sm text-primary"></div>
        <span>กำลังโหลด...</span>
    </div>
    
    <!-- Posts List -->
    <div id="posts-list" class="posts-list"></div>
    
    <!-- Error -->
    <div id="posts-error" class="error-state" style="display: none;">
        <i class="fas fa-exclamation-circle"></i>
        <span>ไม่สามารถโหลดข้อมูลได้</span>
    </div>
</div>
        
        <!-- View All Button -->
        <div class="view-all-btn">
            <a href="https://webboard.assystem.co.th" target="_blank">
                <span>ดูทั้งหมด</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<style>
/* ===== Section Header ===== */
.webboard-section {
    border-radius: 0;
}

.section-header {
    text-align: center;
    margin-bottom: 0rem;
}

.section-header h3 {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.section-header p {
    color: #6c757d;
    margin-top: 0.5rem;
    font-size: 0.95rem;
}

/* ===== Header Icon Animation ===== */
.animated-icon {
    background: linear-gradient(135deg, #4A90E2, #6BA3E8, #5CB85C);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: 
        gradientShift 4s ease infinite,
        floatBounce 3s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes floatBounce {
    0%, 100% { transform: translateY(0) scale(1); }
    25% { transform: translateY(-3px) scale(1.05); }
    75% { transform: translateY(3px) scale(0.95); }
}

/* ===== Toggle Buttons ===== */
.toggle-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.toggle-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 2rem;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    color: #6c757d;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.toggle-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #4A90E2;
}

.toggle-btn.active {
    background: linear-gradient(135deg, #4A90E2, #6BA3E8);
    color: white;
    border-color: #4A90E2;
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
}

.toggle-btn i {
    font-size: 1.1rem;
}

/* ===== Button Icon Animations ===== */
.tab-icon-clock {
    background: linear-gradient(135deg, #4A90E2, #6BA3E8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-block;
    animation: clockRotate 3s ease-in-out infinite;
}

@keyframes clockRotate {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-5deg); }
    75% { transform: rotate(5deg); }
}

.tab-icon-fire {
    background: linear-gradient(135deg, #ff6b6b, #ffa500, #ff4757);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-block;
    animation: fireFlicker 1.5s ease-in-out infinite;
}

@keyframes fireFlicker {
    0%, 100% { 
        transform: scale(1) translateY(0);
        background-position: 0% 50%;
    }
    25% { 
        transform: scale(1.1) translateY(-2px);
        background-position: 50% 50%;
    }
    50% { 
        transform: scale(0.95) translateY(1px);
        background-position: 100% 50%;
    }
    75% { 
        transform: scale(1.05) translateY(-1px);
        background-position: 50% 50%;
    }
}

/* Active Button - Enhanced Icon Animation */
.toggle-btn.active .tab-icon-clock {
    -webkit-text-fill-color: white;
    animation: clockRotate 2s ease-in-out infinite;
}

.toggle-btn.active .tab-icon-fire {
    -webkit-text-fill-color: white;
    animation: fireFlicker 1s ease-in-out infinite;
}

/* ===== Webboard Posts Container ===== */
.webboard-posts-container {
    max-width: 1200px; /* เพิ่มความกว้าง */
    margin: 0 auto;
    padding: 0 1rem;
}

.loading-state,
.error-state {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 3rem;
    color: #6c757d;
    font-size: 0.95rem;
}

/* ===== Posts List ===== */
.posts-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.post-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
}

.post-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Rank Badge */
.post-rank {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1rem;
    color: white;
}

.rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
.rank-2 { background: linear-gradient(135deg, #C0C0C0, #A8A8A8); }
.rank-3 { background: linear-gradient(135deg, #CD7F32, #A0522D); }
.rank-default { background: linear-gradient(135deg, #4A90E2, #6BA3E8); }

/* Post Content */
.post-main {
    flex: 1;
    min-width: 0;
}

.post-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.post-category {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.6rem;
    border-radius: 5px;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
}

.post-category i {
    font-size: 0.7rem;
}

.post-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.8rem;
    color: #6c757d;
}

.post-info-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.post-info-item i {
    font-size: 0.75rem;
}

.tenant-name {
    color: #4A90E2;
    font-weight: 600;
}

/* Post Stats */
.post-stats {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-left: auto;
    padding-left: 1rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
}

.stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #4A90E2;
    line-height: 1;
}

.stat-label {
    font-size: 0.7rem;
    color: #6c757d;
    line-height: 1;
}

/* ===== View All Button ===== */
.view-all-btn {
    text-align: center;
    margin-top: 2rem;
}

.view-all-btn a {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.75rem;
    background: linear-gradient(135deg, #4A90E2, #6BA3E8);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-shadow: 0 3px 8px rgba(74, 144, 226, 0.3);
}

.view-all-btn a:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
}

.view-all-btn a i {
    font-size: 0.85rem;
    transition: transform 0.3s ease;
}

.view-all-btn a:hover i {
    transform: translateX(3px);
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .toggle-buttons {
        gap: 0.5rem;
    }
    
    .toggle-btn {
        padding: 0.6rem 1.25rem;
        font-size: 0.85rem;
    }
    
    .post-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .post-stats {
        width: 100%;
        justify-content: space-around;
        margin-left: 0;
        padding-left: 0;
        padding-top: 0.75rem;
        border-top: 1px solid #e9ecef;
    }
}
</style>
		
		
		
		


        <!-- Services Slideshow (Original Style) -->
        <div class="slideshow-wrapper">
            <div class="slideshow-container" id="slideshow-container">
                <div class="slide-track" id="slide-track">
                    <?php if (isset($api_data1) && is_array($api_data1)): ?>
                        <?php foreach ($api_data1 as $service): ?>
                            <div class="card">
                                <img class="card-img-top"
                                    src="https://www.assystem.co.th/asset/img_services/<?php echo $service['service_img']; ?>"
                                    alt="<?php echo $service['service_title']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $service['service_title']; ?></h5>
                                    <p class="card-text"><?php echo $service['service_intro']; ?></p>
                                    <a href="https://www.assystem.co.th/service/detail/<?php echo $service['service_id']; ?>"
                                        target="_blank" class="btn">เพิ่มเติม</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback Service Cards -->
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <div class="card">
                                <img class="card-img-top" src="<?php echo base_url("docs/service-" . $i . ".jpg"); ?>"
                                    alt="บริการที่ <?php echo $i; ?>">
                                <div class="card-body">
                                    <h5 class="card-title">บริการออนไลน์ <?php echo $i; ?></h5>
                                    <p class="card-text">บริการออนไลน์สำหรับประชาชน ใช้งานง่าย สะดวก รวดเร็ว ทุกที่ทุกเวลา</p>
                                    <a href="#" class="btn">เพิ่มเติม</a>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
            </div>
            <a class="prev" onclick="plusSlides(-1)"></a>
            <a class="next" onclick="plusSlides(1)"></a>
        </div>
		
		
		            <div class="footer-text">
                
                <p class="mt-3 company-name">© <?php echo date('Y'); ?> <a href="https://www.assystem.co.th"
                        target="_blank">บริษัท <span class="as-highlight">เอเอส</span> ซิสเต็ม จำกัด</a> All rights
                    reserved.</p>
            </div>
		
		
    </div>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/74345a2175.js" crossorigin="anonymous"></script>


    <script>
        /**
     * ✅ Fixed Complete Login & 2FA System
     * แก้ไขปัญหาการส่ง OTP และปรับปรุงระบบให้สมบูรณ์
     * รวม reCAPTCHA + 2FA + Login System + Security Features
     */

        console.log('=== FIXED COMPLETE LOGIN & 2FA SYSTEM START ===');

        // ======================== GLOBAL VARIABLES ========================
        window.recaptchaReady = false;
        window.grecaptcha = window.grecaptcha || {};
        let is2FAActive = false;
        let isSubmitting = false;
        let modalInstance = null;

        // ======================== RECAPTCHA FUNCTIONS ========================

        // reCAPTCHA Callback
        window.onRecaptchaLoad = function () {
            console.log('🔒 reCAPTCHA loaded successfully');
            window.recaptchaReady = true;

            if (window.RECAPTCHA_KEY) {
                console.log('🔑 reCAPTCHA Site Key:', window.RECAPTCHA_KEY.substring(0, 10) + '...');
                testRecaptcha();
            } else {
                console.error('❌ reCAPTCHA Site Key not found');
            }
        };

        // Test reCAPTCHA
        async function testRecaptcha() {
            try {
                const testToken = await grecaptcha.execute(window.RECAPTCHA_KEY, { action: 'test' });
                if (testToken) {
                    console.log('✅ reCAPTCHA test successful');
                }
            } catch (error) {
                console.error('❌ reCAPTCHA test failed:', error);
            }
        }

        // Wait for reCAPTCHA
        function waitForRecaptcha(maxWait = 10000) {
            return new Promise((resolve, reject) => {
                const startTime = Date.now();

                function check() {
                    if (window.recaptchaReady && window.RECAPTCHA_KEY) {
                        resolve(true);
                    } else if (Date.now() - startTime > maxWait) {
                        reject(new Error('reCAPTCHA ไม่พร้อมใช้งานภายในเวลาที่กำหนด'));
                    } else {
                        setTimeout(check, 100);
                    }
                }
                check();
            });
        }

        // Get reCAPTCHA Token
        async function getRecaptchaToken(action) {
            try {
                if (!window.recaptchaReady) {
                    let attempts = 0;
                    while (!window.recaptchaReady && attempts < 50) {
                        await new Promise(resolve => setTimeout(resolve, 100));
                        attempts++;
                    }

                    if (!window.recaptchaReady) {
                        throw new Error('reCAPTCHA ไม่พร้อมใช้งาน กรุณารีเฟรชหน้าและลองใหม่');
                    }
                }

                if (!window.RECAPTCHA_KEY) {
                    throw new Error('ไม่พบ reCAPTCHA Site Key');
                }

                console.log(`🔄 Executing reCAPTCHA for ${action}...`);
                const token = await grecaptcha.execute(window.RECAPTCHA_KEY, { action: action });

                if (token) {
                    console.log(`✅ reCAPTCHA token generated for ${action}`);
                    return token;
                } else {
                    throw new Error('ไม่สามารถสร้าง reCAPTCHA token ได้');
                }

            } catch (error) {
                console.error(`💥 reCAPTCHA execution error for ${action}:`, error);
                throw error;
            }
        }

        // Add reCAPTCHA Token to Form
        function addRecaptchaToken(formElement, token) {
            try {
                const existingToken = formElement.querySelector('input[name="g-recaptcha-response"]');
                if (existingToken) {
                    existingToken.remove();
                }

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = 'g-recaptcha-response';
                tokenInput.value = token;
                formElement.appendChild(tokenInput);

                console.log('✅ reCAPTCHA token added to form');
                return true;

            } catch (error) {
                console.error('💥 Error adding reCAPTCHA token to form:', error);
                return false;
            }
        }

        // ======================== INITIALIZATION ========================

        $(document).ready(function () {
            console.log('📄 Document ready - setting up fixed system');

            // ตรวจสอบสถานะ 2FA จาก PHP
            if (typeof window.requires_2fa !== 'undefined' && window.requires_2fa) {
                console.log('2FA required from PHP, showing modal...');
                is2FAActive = true;
                setTimeout(function () {
                    showGoogleAuthModal();
                }, 1000);
            }

            // Setup all components
            setupCSRFTokens();
            setupTabSwitching();
            setupLoginHandlers();
            setupOTPInputs();
            setupSlideshow();
            createFloatingParticles();
            setupForgotPasswordModals();
            setupPasswordToggle();
            setupSecurityFeatures();

            setTimeout(function () {
                // Core Functions
                setupModalEventHandlers();

                // Staff Reset Password System
                checkAndShowResetPasswordModal();
                setupResetPasswordForm();
                setupStaffPasswordStrength();
                setupStaffPasswordMatching();

                // Public Reset Password System
                setupPublicForgotPasswordForm();
                checkAndShowPublicResetModal();
                setupPublicResetPasswordForm();
                setupPublicPasswordStrength();
                setupPublicPasswordMatching();

                console.log('✅ Complete password system initialized successfully');
            }, 1000);

            console.log('✅ Fixed complete system loaded successfully');
        });

        // ======================== SETUP FUNCTIONS ========================

        // Setup CSRF Tokens
        function setupCSRFTokens() {
            const csrfTokenName = $('meta[name="csrf-token-name"]').attr('content') || 'ci_csrf_token';
            const csrfHash = $('meta[name="csrf-token"]').attr('content') || '';

            if (csrfHash) {
                if (!$('#citizenLoginForm').find(`input[name="${csrfTokenName}"]`).length) {
                    $('#citizenLoginForm').append(`<input type="hidden" name="${csrfTokenName}" value="${csrfHash}">`);
                }
                if (!$('#staffLoginForm').find(`input[name="${csrfTokenName}"]`).length) {
                    $('#staffLoginForm').append(`<input type="hidden" name="${csrfTokenName}" value="${csrfHash}">`);
                }
                console.log('🔐 CSRF tokens added');
            }
        }

        // Setup Tab Switching
        function setupTabSwitching() {
            $(document).on('click', '.tab-btn', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const tab = $(this).data('tab');
                if ($(this).hasClass('active')) return;

                $('.tab-btn').removeClass('active');
                $(this).addClass('active');
                $('.login-form').removeClass('active');
                $(`#${tab}-form`).addClass('active');

                // Clear temp user type เมื่อเปลี่ยน tab
                delete window.temp_user_type;

                // Reset OTP inputs ถ้า modal เปิดอยู่
                if ($('#googleAuthModal').is(':visible')) {
                    clearOTPInputs();
                    updateSecurityInfo();
                }

                console.log('🔄 Tab switched to:', tab);
            });
        }

        // Setup Login Handlers
        function setupLoginHandlers() {
            $('#citizenLoginForm').on('submit', async function (e) {
                e.preventDefault();
                await handleLoginSubmit($(this), 'citizen', 'citizen_login');
            });

            $('#staffLoginForm').on('submit', async function (e) {
                e.preventDefault();
                await handleLoginSubmit($(this), 'staff', 'staff_login');
            });
        }

        // Setup Security Features
        function setupSecurityFeatures() {
            // Back button protection during 2FA
            if (window.history && window.history.pushState) {
                $(window).on('popstate', function () {
                    if (is2FAActive) {
                        console.log('Back button pressed during 2FA');
                        history.pushState(null, null, location.href);

                        Swal.fire({
                            title: 'กำลังยืนยันตัวตน',
                            text: 'กรุณาทำการยืนยัน 2FA ให้เสร็จสิ้นก่อน',
                            icon: 'warning',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            }

            // Auto-add device fingerprint when OTP modal is shown
            $(document).on('shown.bs.modal', '#googleAuthModal', function () {
                setTimeout(() => {
                    addDeviceFingerprint();
                }, 100);
            });

            console.log('🔒 Security features enabled');
        }

        // ======================== LOGIN HANDLERS ========================

        // Generic Login Submit Handler
        async function handleLoginSubmit(form, userType, recaptchaAction) {
            console.log(`👤 ${userType} login form submitted`);

            if (isSubmitting) {
                console.log(`${userType} form submission already in progress`);
                return false;
            }

            isSubmitting = true;
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            try {
                // Step 1: Check reCAPTCHA readiness
                console.log('🔒 Checking reCAPTCHA readiness...');

                if (!window.recaptchaReady || !window.RECAPTCHA_KEY) {
                    await waitForRecaptcha(10000);
                }

                // Step 2: Show loading for reCAPTCHA
                submitBtn.prop('disabled', true).html('<i class="fas fa-shield-alt me-2"></i> กำลังยืนยันตัวตน...');

                // Step 3: Get reCAPTCHA Token
                const recaptchaToken = await getRecaptchaToken(recaptchaAction);

                if (!addRecaptchaToken(form[0], recaptchaToken)) {
                    throw new Error('ไม่สามารถเพิ่ม reCAPTCHA token ได้');
                }

                // Step 4: Show login loading
                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> กำลังเข้าสู่ระบบ...');

                // Step 5: Submit to server
                const ajaxConfig = getAjaxConfig(userType, form);

                $.ajax({
                    ...ajaxConfig,
                    success: function (response) {
                        console.log(`${userType} login response:`, response);
                        isSubmitting = false;
                        handleLoginResponse(response, submitBtn, originalText, userType);
                    },
                    error: function (xhr, status, error) {
                        console.error(`${userType} login AJAX error:`, status, error);
                        isSubmitting = false;
                        submitBtn.prop('disabled', false).html(originalText);
                        handleAjaxError(status, xhr.status);
                    }
                });

            } catch (error) {
                console.error(`💥 ${userType} login error:`, error);
                isSubmitting = false;
                submitBtn.prop('disabled', false).html(originalText);

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message || 'ไม่สามารถเข้าสู่ระบบได้ กรุณาลองใหม่อีกครั้ง',
                    confirmButtonText: 'ลองใหม่'
                });
            }
        }

        // Get AJAX Configuration
        function getAjaxConfig(userType, form) {
            const baseConfig = {
                type: 'POST',
                data: form.serialize(),
                timeout: 15000,
                beforeSend: function (xhr) {
                    console.log(`Sending ${userType} login request...`);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                }
            };

            if (userType === 'citizen') {
                return {
                    ...baseConfig,
                    url: window.base_url + 'Auth_api/check_login',
                    dataType: 'text'
                };
            } else {
                return {
                    ...baseConfig,
                    url: window.base_url + 'User/check2',
                    dataType: 'json'
                };
            }
        }

        // ======================== RESPONSE HANDLERS ========================

        // Handle Login Response
        function handleLoginResponse(responseData, submitBtn, originalText, userType) {
            let response = responseData;

            // Parse mixed response for citizen
            if (userType === 'citizen' && typeof responseData === 'string') {
                response = parseResponse(responseData);
            }

            console.log(`Handling ${userType} response:`, response);

            if (response && response.status === 'success') {
                handleSuccessResponse(response, userType);
            } else if (response && response.status === 'requires_2fa') {
                handle2FAResponse(response, submitBtn, originalText, userType);
            } else if (response && response.status === 'blocked') {
                handleBlockedResponse(response, submitBtn, originalText);
            } else if (response && response.status === 'error') {
                handleErrorResponse(response, submitBtn, originalText, userType);
            } else {
                handleUnknownResponse(submitBtn, originalText);
            }
        }

        // Parse Response (for mixed responses)
        function parseResponse(responseText) {
            try {
                const jsonMatch = responseText.match(/\{[^{}]*"status"[^{}]*\}$/);
                if (jsonMatch) {
                    return JSON.parse(jsonMatch[0]);
                } else {
                    return JSON.parse(responseText);
                }
            } catch (parseError) {
                console.error('Failed to parse response:', parseError);
                return {
                    status: 'error',
                    message: 'เกิดข้อผิดพลาดในการประมวลผลข้อมูล กรุณารีเฟรชหน้าเว็บแล้วลองใหม่'
                };
            }
        }

        // Handle Success Response
        function handleSuccessResponse(response, userType) {
            console.log(`${userType} login successful`);

            const savedRedirectUrl = sessionStorage.getItem('redirect_after_login');
            let redirectUrl = response.redirect;

            if (!redirectUrl) {
                redirectUrl = userType === 'citizen' ?
                    window.base_url + 'Pages/service_systems' :
                    window.base_url + 'User/choice';
            }

            if (savedRedirectUrl) {
                console.log(`Found saved redirect URL for ${userType}:`, savedRedirectUrl);
                redirectUrl = savedRedirectUrl;
                sessionStorage.setItem('redirect_after_login', savedRedirectUrl);
            }

            Swal.fire({
                icon: 'success',
                title: 'เข้าสู่ระบบสำเร็จ',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                console.log(`Redirecting ${userType} to:`, redirectUrl);
                window.location.href = redirectUrl;
            });
        }

        // Handle 2FA Response - 🔧 แก้ไขแล้ว
        function handle2FAResponse(response, submitBtn, originalText, userType) {
            console.log(`2FA required for ${userType}`);

            is2FAActive = true;
            submitBtn.prop('disabled', false).html(originalText);

            // 🔧 แก้ไข: ตั้งค่า temp_user_type ให้ถูกต้อง
            if (response.temp_user_type) {
                window.temp_user_type = response.temp_user_type;
            } else {
                // Fallback: ใช้ userType ที่ได้จากการ login
                window.temp_user_type = userType === 'citizen' ? 'public' : 'staff';
            }

            console.log('Set temp_user_type to:', window.temp_user_type);

            $('.login-container').fadeOut(300, function () {
                console.log(`Login container hidden, showing 2FA modal for ${userType}`);
                showGoogleAuthModal();
            });
        }

        // Handle Blocked Response
        function handleBlockedResponse(response, submitBtn, originalText) {
            console.log('User blocked:', response);
            submitBtn.prop('disabled', false).html(originalText);
            showBlockedModal(response);
        }

        // Handle Error Response
        function handleErrorResponse(response, submitBtn, originalText, userType) {
            submitBtn.prop('disabled', false).html(originalText);

            let errorMessage = response.message || (userType === 'citizen' ?
                'อีเมลหรือรหัสผ่านไม่ถูกต้อง' :
                'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            let alertType = 'error';

            if (response.remaining_attempts !== undefined && response.remaining_attempts > 0) {
                errorMessage += `\n\n⚠️ คำเตือน: เหลือโอกาสอีก ${response.remaining_attempts} ครั้ง`;
                errorMessage += '\nหากล็อกอินผิดอีก ระบบจะล็อคชั่วคราว';
                alertType = 'warning';
            }

            Swal.fire({
                icon: alertType,
                title: 'ไม่สามารถเข้าสู่ระบบได้',
                text: errorMessage,
                confirmButtonText: 'ลองใหม่'
            });
        }

        // Handle Unknown Response
        function handleUnknownResponse(submitBtn, originalText) {
            submitBtn.prop('disabled', false).html(originalText);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเข้าสู่ระบบได้ กรุณาลองใหม่อีกครั้ง'
            });
        }

        // Handle AJAX Error
        function handleAjaxError(status, httpStatus) {
            let errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';

            if (status === 'timeout') {
                errorMessage = 'การเชื่อมต่อใช้เวลานานเกินไป';
            } else if (httpStatus === 500) {
                errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์';
            } else if (httpStatus === 0) {
                errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้';
            } else if (status === 'parsererror') {
                errorMessage = 'เกิดข้อผิดพลาดในการประมวลผลข้อมูล';
            }

            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: errorMessage
            });
        }

        // ======================== 2FA MODAL FUNCTIONS ========================

        // Show Google Auth Modal - 🔧 แก้ไขแล้ว
        function showGoogleAuthModal() {
            console.log('🔐 Showing Google Auth Modal');

            try {
                const modalElement = document.getElementById('googleAuthModal');
                if (!modalElement) {
                    console.error('❌ Google Auth Modal not found in DOM');
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถแสดงหน้า 2FA ได้'
                    });
                    return;
                }

                // Reset modal state
                clearOTPInputs();
                updateSecurityInfo();

                // Create modal instance
                modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });

                // Event listeners
                modalElement.addEventListener('shown.bs.modal', function () {
                    console.log('2FA Modal shown successfully');

                    // Push state เพื่อป้องกัน back button
                    if (window.history && window.history.pushState) {
                        history.pushState(null, null, location.href);
                    }

                    // Focus input แรก
                    setTimeout(() => {
                        $('.otp-input').first().focus();
                    }, 300);

                    updateButtonVisibility();
                }, { once: true });

                modalElement.addEventListener('hidden.bs.modal', function () {
                    console.log('2FA Modal hidden');

                    // ถ้าไม่ได้ยกเลิกโดยตัวเอง ให้ redirect
                    if (is2FAActive) {
                        console.log('Modal hidden unexpectedly during 2FA');
                        setTimeout(() => {
                            if (is2FAActive) {
                                console.log('Force redirect after unexpected modal hide');
                                window.location.href = window.base_url + 'User/logout';
                            }
                        }, 1000);
                    }
                }, { once: true });

                modalInstance.show();

                console.log('✅ Google Auth Modal shown successfully');

            } catch (error) {
                console.error('❌ Error showing Google Auth Modal:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถแสดงหน้า 2FA ได้: ' + error.message
                });
            }
        }

        // Clear OTP Inputs
        function clearOTPInputs() {
            $('.otp-input').val('').removeClass('is-valid is-invalid');
            $('#otpValue').val('');
            console.log('🧹 OTP inputs cleared');
        }

        // Update Security Info
        function updateSecurityInfo() {
            const securityInfo = $('#securityInfo');
            const securityText = $('#securityText');

            if (securityInfo.length && securityText.length) {
                securityInfo.removeClass('clickable').off('click');
                securityText.html('<i class="bi bi-shield-check pulse-icon"></i> การยืนยันตัวตนเพื่อความปลอดภัย');
            }

            console.log('🔄 Security info updated');
        }

        // Global function for button visibility (accessible from outside setupOTPInputs)
        function updateButtonVisibility() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const securityInfo = document.getElementById('securityInfo');
            const securityText = document.getElementById('securityText');

            if (!otpInputs.length) return;

            const otp = Array.from(otpInputs).map(input => input.value).join('');
            const otpValueInput = document.getElementById('otpValue');

            if (otpValueInput) {
                otpValueInput.value = otp;
            }

            // ตรวจสอบจำนวนหลักและเปลี่ยนสถานะ
            if (otp.length === 6) {
                // กรอกครบ 6 หลัก - เปลี่ยนเป็นปุ่มยืนยัน
                if (securityInfo && securityText) {
                    securityInfo.classList.add('clickable');
                    securityText.innerHTML = '<i class="bi bi-shield-check pulse-icon"></i>ยืนยัน';
                }
            } else {
                // กรอกไม่ครบ 6 หลัก - แสดงข้อความเดิม
                if (securityInfo && securityText) {
                    securityInfo.classList.remove('clickable');
                    securityText.innerHTML = '<i class="bi bi-shield-check pulse-icon"></i>การยืนยันตัวตนเพื่อความปลอดภัย';
                }
            }
        }

        // Cancel Google Auth
        function cancelGoogleAuth() {
            console.log('❌ Cancelling Google Auth');

            is2FAActive = false;

            if (modalInstance) {
                modalInstance.hide();
            }

            // Clear inputs
            $('.otp-input').val('');
            $('#otpValue').val('');

            // Show confirmation before redirect
            Swal.fire({
                title: 'ยกเลิกการยืนยันตัวตน?',
                text: 'คุณจะถูกนำกลับไปหน้าเข้าสู่ระบบ',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'กลับ'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = window.base_url + 'User';
                } else {
                    // ถ้าไม่ยืนยัน ให้แสดง modal อีกครั้ง
                    is2FAActive = true;
                    showGoogleAuthModal();
                }
            });
        }

        // Submit OTP When Ready
        function submitOTPWhenReady() {
            const otpValue = document.getElementById('otpValue').value;
            const securityInfo = document.getElementById('securityInfo');

            // ตรวจสอบว่ากรอกครบ 6 หลักและเป็นสถานะ clickable
            if (otpValue && otpValue.length === 6 && securityInfo && securityInfo.classList.contains('clickable')) {
                $('#otpVerifyForm').submit();
            }
        }

        // Update Remember Device
        function updateRememberDevice() {
            const checkbox = document.getElementById('rememberDevice');
            const hiddenInput = document.getElementById('rememberDeviceValue');
            if (checkbox && hiddenInput) {
                hiddenInput.value = checkbox.checked ? '1' : '0';
                console.log('📱 Remember device updated:', checkbox.checked);
            }
        }

        // ======================== OTP HANDLING - 🔧 แก้ไขสำคัญ ========================

        // Setup OTP Inputs - 🔧 แก้ไขแล้ว
        function setupOTPInputs() {
            console.log('🔢 Setting up OTP inputs');

            const otpInputs = document.querySelectorAll('.otp-input');

            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function (e) {
                    const value = e.target.value;

                    // Allow only numbers
                    if (!/^\d$/.test(value)) {
                        e.target.value = '';
                        updateButtonVisibility();
                        return;
                    }

                    // Move to next input
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }

                    updateButtonVisibility();
                });

                input.addEventListener('keydown', function (e) {
                    // Backspace - go to previous input
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }

                    // เรียก update หลังจาก backspace
                    setTimeout(() => {
                        updateButtonVisibility();
                    }, 10);

                    // Enter - submit form if complete
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const otp = Array.from(otpInputs).map(input => input.value).join('');
                        if (otp.length === 6) {
                            $('#otpVerifyForm').submit();
                        }
                    }
                });

                // Select all on focus
                input.addEventListener('focus', function (e) {
                    e.target.select();
                });

                // Handle paste
                input.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const numbers = paste.replace(/\D/g, '').slice(0, 6);

                    numbers.split('').forEach((digit, idx) => {
                        if (otpInputs[idx]) {
                            otpInputs[idx].value = digit;
                        }
                    });

                    updateButtonVisibility();

                    // Focus next input or last input
                    const nextIndex = Math.min(numbers.length, 5);
                    if (otpInputs[nextIndex]) {
                        otpInputs[nextIndex].focus();
                    }
                });
            });

            // 🔧 แก้ไข: OTP Form submit handler ที่ถูกต้อง
            $('#otpVerifyForm').on('submit', function (e) {
                e.preventDefault();
                console.log('=== OTP FORM SUBMITTED (FIXED) ===');

                const otpValue = $('#otpValue').val();
                if (!otpValue || otpValue.length !== 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'รหัส OTP ไม่ครบ',
                        text: 'กรุณากรอกรหัส OTP ให้ครบ 6 หลัก'
                    });
                    return;
                }

                // 🔧 แก้ไข: การตรวจสอบประเภทผู้ใช้ที่แม่นยำยิ่งขึ้น
                let userType = 'staff'; // default
                let endpoint = window.base_url + 'User/verify_otp'; // default

                // วิธีที่ 1: ตรวจสอบจาก session temp data (แม่นยำที่สุด)
                if (typeof window.temp_user_type !== 'undefined' && window.temp_user_type === 'public') {
                    userType = 'public';
                    endpoint = window.base_url + 'Auth_api/verify_otp_public';
                }
                // วิธีที่ 2: ตรวจสอบจาก active tab
                else {
                    const activeTab = $('.tab-btn.active');
                    console.log('Active tab found:', activeTab.length);
                    console.log('Active tab data-tab:', activeTab.data('tab'));

                    if (activeTab.length > 0 && activeTab.data('tab') === 'citizen') {
                        userType = 'public';
                        endpoint = window.base_url + 'Auth_api/verify_otp_public';
                    }
                }

                // วิธีที่ 3: ตรวจสอบจาก hidden input ในฟอร์มล็อกอิน
                const citizenForm = $('#citizen-form');
                const staffForm = $('#staff-form');

                if (citizenForm.hasClass('active') || citizenForm.is(':visible')) {
                    userType = 'public';
                    endpoint = window.base_url + 'Auth_api/verify_otp_public';
                } else if (staffForm.hasClass('active') || staffForm.is(':visible')) {
                    userType = 'staff';
                    endpoint = window.base_url + 'User/verify_otp';
                }

                // วิธีที่ 4: ตรวจสอบจาก URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const userTypeParam = urlParams.get('user_type');
                if (userTypeParam === 'public') {
                    userType = 'public';
                    endpoint = window.base_url + 'Auth_api/verify_otp_public';
                }

                // 🔧 แก้ไข: Debug logging
                console.log('=== OTP VERIFICATION DEBUG ===');
                console.log('Detected user type:', userType);
                console.log('Using endpoint:', endpoint);
                console.log('Active tab data:', $('.tab-btn.active').data('tab'));
                console.log('Citizen form active:', $('#citizen-form').hasClass('active'));
                console.log('Staff form active:', $('#staff-form').hasClass('active'));
                console.log('=== END DEBUG ===');

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();

                // เพิ่ม device fingerprint
                addDeviceFingerprint();

                // ปิดปุ่มชั่วคราว
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> กำลังตรวจสอบ...');

                // 🔧 แก้ไข: ส่ง form data ที่ถูกต้อง (ใช้ form serialization แทน manual object)
                const formData = form.serializeArray();
                formData.push({ name: 'user_type_detected', value: userType });

                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    data: $.param(formData), // 🔧 แก้ไข: ใช้ serialized form data
                    dataType: 'json',
                    timeout: 30000,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('X-User-Type', userType); // 🔧 แก้ไข: เพิ่ม header
                        console.log('Sending OTP request to:', endpoint);
                        console.log('User type:', userType);
                    },
                    success: function (response) {
                        console.log('OTP Response:', response);

                        if (response && response.status === 'success') {
                            // 🔧 แก้ไข: ตรวจสอบ redirect URL จาก sessionStorage
                            const savedRedirectUrl = sessionStorage.getItem('redirect_after_login');
                            let redirectUrl = response.redirect;

                            if (savedRedirectUrl) {
                                console.log('Found saved redirect URL after OTP:', savedRedirectUrl);
                                redirectUrl = savedRedirectUrl;
                                // เก็บไว้ใน sessionStorage เพื่อใช้หลังจาก redirect
                                sessionStorage.setItem('redirect_after_login', savedRedirectUrl);
                            } else if (!redirectUrl) {
                                // กำหนด redirect URL ตาม user type
                                redirectUrl = userType === 'public' ?
                                    window.base_url + 'Pages/service_systems' :
                                    window.base_url + 'User/choice';
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'ยืนยันสำเร็จ',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                console.log('Redirecting after OTP to:', redirectUrl);
                                window.location.href = redirectUrl;
                            });
                        } else {
                            // จัดการ error
                            handleOTPError(response, submitBtn, originalText);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('OTP AJAX Error:', { status, error, responseText: xhr.responseText });
                        console.error('Request was sent to:', endpoint);
                        console.error('Detected user type was:', userType);

                        handleOTPError({
                            status: 'error',
                            message: getAjaxErrorMessage(status, xhr.status)
                        }, submitBtn, originalText);
                    }
                });
            });

            // เรียกใช้ครั้งแรกเพื่อตั้งค่าเริ่มต้น
            updateButtonVisibility();
        }

        // 🔧 แก้ไข: ฟังก์ชัน handleOTPError ที่สมบูรณ์
        function handleOTPError(response, submitBtn, originalText) {
            console.error('OTP Error:', response);

            // คืนค่าปุ่มเป็นปกติ
            if (submitBtn && originalText) {
                submitBtn.prop('disabled', false).html(originalText);
            }

            // ล้าง OTP inputs
            $('.otp-input').val('');
            $('#otpValue').val('');
            updateButtonVisibility();

            // แสดงข้อความ error
            let errorMessage = 'เกิดข้อผิดพลาดในการยืนยัน OTP';

            if (response && response.message) {
                errorMessage = response.message;
            } else if (response && response.status === 'error') {
                errorMessage = 'รหัส OTP ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
            }

            // แสดง error alert
            Swal.fire({
                icon: 'error',
                title: 'การยืนยันล้มเหลว',
                text: errorMessage,
                confirmButtonText: 'ลองใหม่',
                didClose: function () {
                    // Focus ไปที่ input แรกหลังจากปิด alert
                    setTimeout(() => {
                        $('.otp-input').first().focus();
                    }, 100);
                }
            });

            // ตรวจสอบว่าต้อง redirect หรือไม่ (กรณี session หมดอายุ)
            if (response && response.redirect) {
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 2000);
            }
        }

        // 🔧 แก้ไข: ฟังก์ชันแปลง AJAX Error เป็นข้อความ
        function getAjaxErrorMessage(status, httpStatus) {
            console.log('AJAX Error Status:', status, 'HTTP Status:', httpStatus);

            let errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';

            switch (status) {
                case 'timeout':
                    errorMessage = 'การเชื่อมต่อใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
                    break;
                case 'abort':
                    errorMessage = 'การเชื่อมต่อถูกยกเลิก';
                    break;
                case 'parsererror':
                    errorMessage = 'เกิดข้อผิดพลาดในการประมวลผลข้อมูล';
                    break;
                case 'error':
                    switch (httpStatus) {
                        case 0:
                            errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                            break;
                        case 400:
                            errorMessage = 'ข้อมูลที่ส่งไม่ถูกต้อง';
                            break;
                        case 401:
                            errorMessage = 'ไม่มีสิทธิ์เข้าถึง กรุณาเข้าสู่ระบบใหม่';
                            break;
                        case 403:
                            errorMessage = 'ไม่อนุญาตให้เข้าถึง';
                            break;
                        case 404:
                            errorMessage = 'ไม่พบหน้าที่ร้องขอ';
                            break;
                        case 405:
                            errorMessage = 'วิธีการร้องขอไม่ถูกต้อง';
                            break;
                        case 429:
                            errorMessage = 'ส่งคำขอมากเกินไป กรุณารอสักครู่แล้วลองใหม่';
                            break;
                        case 500:
                            errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์';
                            break;
                        case 502:
                            errorMessage = 'เซิร์ฟเวอร์ไม่ตอบสนอง';
                            break;
                        case 503:
                            errorMessage = 'เซิร์ฟเวอร์ไม่พร้อมใช้งาน';
                            break;
                        case 504:
                            errorMessage = 'เซิร์ฟเวอร์ตอบสนองช้าเกินไป';
                            break;
                        default:
                            errorMessage = `เกิดข้อผิดพลาด (รหัส: ${httpStatus})`;
                    }
                    break;
                default:
                    errorMessage = 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ';
            }

            return errorMessage;
        }

        // ฟังก์ชันเพิ่มข้อมูล device fingerprint
        function addDeviceFingerprint() {
            // เพิ่ม hidden fields สำหรับ device fingerprinting
            const form = document.getElementById('otpVerifyForm');
            if (!form) return;

            // Screen resolution
            const existingScreen = form.querySelector('input[name="screen_resolution"]');
            if (!existingScreen) {
                const screenInput = document.createElement('input');
                screenInput.type = 'hidden';
                screenInput.name = 'screen_resolution';
                screenInput.value = screen.width + 'x' + screen.height;
                form.appendChild(screenInput);
            }

            // Timezone
            const existingTimezone = form.querySelector('input[name="timezone"]');
            if (!existingTimezone) {
                const timezoneInput = document.createElement('input');
                timezoneInput.type = 'hidden';
                timezoneInput.name = 'timezone';
                timezoneInput.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
                form.appendChild(timezoneInput);
            }

            console.log('📱 Device fingerprint added to OTP form');
        }

        // ======================== UTILITY FUNCTIONS ========================

        // Show Blocked Modal
        function showBlockedModal(response) {
            const remainingTime = response.remaining_time || 0;
            const blockLevel = response.block_level || 1;
            const message = response.message || 'คุณถูกบล็อคชั่วคราว';

            let blockLevelText = '';
            if (blockLevel === 2) {
                blockLevelText = 'ระดับที่ 2 (ล็อกอินผิด 6 ครั้ง) - บล็อค 10 นาที';
            } else {
                blockLevelText = 'ระดับที่ 1 (ล็อกอินผิด 3 ครั้ง) - บล็อค 3 นาที';
            }

            Swal.fire({
                icon: 'warning',
                title: '🔒 ระบบถูกล็อค',
                html: `
        <div style="text-align: center;">
            <p style="font-size: 16px; margin: 15px 0;">
                <strong>${message}</strong>
            </p>
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <div id="countdown-display" style="font-size: 24px; font-weight: bold; color: #856404; margin-bottom: 10px;">
                    เหลือเวลา: <span id="countdown-time">${formatTime(remainingTime)}</span>
                </div>
                <p style="font-size: 14px; color: #856404; margin: 5px 0;">
                    ${blockLevelText}
                </p>
            </div>
        </div>
        `,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    startCountdown(remainingTime, () => {
                        Swal.close();
                        Swal.fire({
                            icon: 'info',
                            title: 'สามารถลองใหม่ได้แล้ว',
                            text: 'ระบบพร้อมให้คุณเข้าสู่ระบบใหม่',
                            confirmButtonText: 'ตกลง'
                        });
                    });
                }
            });
        }

        // Format Time
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        }

        // Start Countdown
        function startCountdown(seconds, onComplete) {
            const countdownElement = document.getElementById('countdown-time');

            const timer = setInterval(() => {
                seconds--;

                if (countdownElement) {
                    countdownElement.textContent = formatTime(seconds);
                }

                if (seconds <= 0) {
                    clearInterval(timer);
                    onComplete();
                }
            }, 1000);
        }

        // Validate Email
        function validateEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        // ======================== MISSING COMPONENTS - เติมส่วนที่ขาด ========================

        // Setup Slideshow
        function setupSlideshow() {
            console.log('🖼️ Setting up slideshow');

            const slideTrack = document.getElementById('slide-track');
            const container = document.getElementById('slideshow-container');

            if (slideTrack && container) {
                let autoSlideInterval;
                let isDragging = false;
                let startPosition;
                let startScrollLeft;

                container.addEventListener('mouseenter', () => {
                    clearInterval(autoSlideInterval);
                });

                container.addEventListener('mouseleave', () => {
                    isDragging = false;
                    container.style.cursor = 'grab';
                    startAutoSlide();
                });

                container.addEventListener('mousedown', (e) => {
                    isDragging = true;
                    startPosition = e.pageX;
                    startScrollLeft = container.scrollLeft;
                    container.style.cursor = 'grabbing';
                });

                container.addEventListener('mousemove', (e) => {
                    if (!isDragging) return;
                    e.preventDefault();
                    const x = e.pageX;
                    const walk = (x - startPosition) * 2;
                    container.scrollLeft = startScrollLeft - walk;
                });

                container.addEventListener('mouseup', () => {
                    isDragging = false;
                    container.style.cursor = 'grab';
                });

                container.addEventListener('touchstart', (e) => {
                    isDragging = true;
                    startPosition = e.touches[0].pageX;
                    startScrollLeft = container.scrollLeft;
                    clearInterval(autoSlideInterval);
                });

                container.addEventListener('touchmove', (e) => {
                    if (!isDragging) return;
                    const x = e.touches[0].pageX;
                    const walk = (x - startPosition) * 2;
                    container.scrollLeft = startScrollLeft - walk;
                });

                container.addEventListener('touchend', () => {
                    isDragging = false;
                    startAutoSlide();
                });

                function startAutoSlide() {
                    clearInterval(autoSlideInterval);
                    autoSlideInterval = setInterval(() => {
                        const scrollWidth = slideTrack.scrollWidth;
                        const containerWidth = container.clientWidth;
                        const maxScrollLeft = scrollWidth - containerWidth;

                        if (container.scrollLeft >= maxScrollLeft - 10) {
                            container.scrollTo({
                                left: 0,
                                behavior: 'smooth'
                            });
                        } else {
                            container.scrollBy({
                                left: 240,
                                behavior: 'smooth'
                            });
                        }
                    }, 3000);
                }

                startAutoSlide();

                window.plusSlides = function (direction) {
                    clearInterval(autoSlideInterval);
                    const cardWidth = 240;
                    container.scrollBy({
                        left: cardWidth * direction,
                        behavior: 'smooth'
                    });

                    if (!container.matches(':hover')) {
                        setTimeout(startAutoSlide, 1000);
                    }
                };
            }

            console.log('✅ Slideshow setup completed');
        }

        // Create Floating Particles
        function createFloatingParticles() {
            console.log('✨ Creating floating particles');

            const particlesContainer = document.querySelector('.floating-particles');
            if (!particlesContainer) return;

            const numberOfParticles = 30;
            particlesContainer.innerHTML = '';

            for (let i = 0; i < numberOfParticles; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const top = Math.random() * 100;
                const left = Math.random() * 100;
                const size = Math.random() * 6 + 2;
                const duration = Math.random() * 20 + 15;
                const opacity = Math.random() * 0.5 + 0.1;

                particle.style.top = `${top}%`;
                particle.style.left = `${left}%`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.opacity = opacity;
                particle.style.animationDuration = `${duration}s`;
                particle.style.animationDelay = `${Math.random() * 10}s`;

                particlesContainer.appendChild(particle);
            }

            console.log('✅ Floating particles created');
        }

        // Setup Forgot Password Modals
        function setupForgotPasswordModals() {
            console.log('🔑 Setting up forgot password modals');

            // Staff Forgot Password Form
            $('#forgotPasswordForm').off('submit').on('submit', function (e) {
                e.preventDefault();
                var email = $('#modal-email').val();

                if (!validateEmail(email)) {
                    $('#errorAlert').show();
                    $('#errorMessage').text('กรุณากรอกอีเมลให้ถูกต้อง');
                    return;
                }

                $('#successAlert').hide();
                $('#errorAlert').hide();
                $('#loadingIndicator').show();
                $('#submitForgotPassword').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> กำลังดำเนินการ...');

                $.ajax({
                    url: window.base_url + 'user/sendEmailAjax',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function (response) {
                        $('#loadingIndicator').hide();

                        if (response && response.status === 'success') {
                            $('#successAlert').show();
                            $('#errorAlert').hide();
                            $('#forgotPasswordForm')[0].reset();

                            $('#successAlert').html(`
                        <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                        ส่งอีเมลสำเร็จ! กรุณาตรวจสอบอีเมลของคุณเพื่อรีเซ็ตรหัสผ่าน
                    `);

                            let autoCloseTimer = setTimeout(function () {
                                closeModalSafely('forgotPasswordModal');
                            }, 5000);

                            $('#submitForgotPassword').html('<i class="fas fa-check-circle me-2"></i> ตกลง').off('click').on('click', function () {
                                clearTimeout(autoCloseTimer);
                                closeModalSafely('forgotPasswordModal');
                            });

                        } else {
                            $('#errorAlert').show();
                            $('#errorMessage').text(response && response.message ? response.message : 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                            $('#successAlert').hide();
                            $('#submitForgotPassword').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน');
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#loadingIndicator').hide();
                        $('#errorAlert').show();
                        $('#errorMessage').text('เกิดข้อผิดพลาดในการส่งคำขอ');
                        $('#successAlert').hide();
                        $('#submitForgotPassword').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน');
                    }
                });
            });

            console.log('✅ Forgot password modals setup completed');
        }

        // Close Modal Safely
        function closeModalSafely(modalId) {
            try {
                const modalElement = document.getElementById(modalId);

                if (modalElement) {
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);

                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        $('#' + modalId).modal('hide');
                    }

                    setTimeout(function () {
                        cleanupModalBackdrop();
                    }, 300);
                }
            } catch (error) {
                console.error('Error closing modal:', error);
                cleanupModalBackdrop();
            }
        }

        // Cleanup Modal Backdrop
        function cleanupModalBackdrop() {
            try {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'padding-right': '',
                    'overflow': '',
                    'position': ''
                });
                $('html').removeClass('modal-open');
                console.log('Modal backdrop cleaned up successfully');
            } catch (error) {
                console.error('Error cleaning up modal backdrop:', error);
            }
        }

        // Setup Password Toggle
        function setupPasswordToggle() {
            console.log('👁️ Setting up password toggle');

            $(document).on('click', '.toggle-password', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const targetId = $(this).data('target');
                const targetInput = $('#' + targetId);
                const eyeIcon = $(this);
                const inputWrapper = $(this).closest('.input-wrapper');

                if (targetInput.length === 0) {
                    console.error('Target input not found:', targetId);
                    return;
                }

                togglePasswordVisibility(targetInput, eyeIcon, inputWrapper);
            });

            console.log('✅ Password toggle setup completed');
        }

        // Toggle Password Visibility
        function togglePasswordVisibility(inputElement, eyeIcon, inputWrapper) {
            const isPasswordVisible = inputElement.attr('type') === 'text';

            eyeIcon.addClass('toggling');

            setTimeout(() => {
                if (isPasswordVisible) {
                    inputElement.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                    eyeIcon.attr('title', 'แสดงรหัสผ่าน');
                    inputWrapper.removeClass('password-visible');
                    console.log('Password hidden for input:', inputElement.attr('id'));
                } else {
                    inputElement.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                    eyeIcon.attr('title', 'ซ่อนรหัสผ่าน');
                    inputWrapper.addClass('password-visible');
                    console.log('Password shown for input:', inputElement.attr('id'));

                    setTimeout(() => {
                        if (inputElement.attr('type') === 'text') {
                            togglePasswordVisibility(inputElement, eyeIcon, inputWrapper);
                        }
                    }, 10000);
                }

                eyeIcon.removeClass('toggling');
                inputElement.focus();
            }, 150);
        }

        // *** STAFF RESET PASSWORD SYSTEM ***

        // ตรวจสอบและแสดง Reset Password Modal
        function checkAndShowResetPasswordModal() {
            console.log('🔍 Checking for reset password modal flag...');

            if (typeof window.show_reset_modal !== 'undefined' && window.show_reset_modal === true) {
                console.log('🔑 Show reset modal flag found - setting up modal');

                // ตั้งค่าข้อมูลใน Modal
                if (typeof window.reset_email !== 'undefined' && window.reset_email) {
                    $('#resetStaffEmail').val(window.reset_email);
                    $('#resetStaffEmailDisplay').text(window.reset_email);
                    console.log('📧 Email set to modal:', window.reset_email);
                }

                if (typeof window.reset_token !== 'undefined' && window.reset_token) {
                    $('#resetStaffToken').val(window.reset_token);
                    console.log('🔑 Token set to modal');
                }

                // แสดง Modal หลังจาก DOM พร้อม
                setTimeout(function () {
                    console.log('📋 Showing reset password modal...');
                    $('#resetPasswordStaffModal').modal('show');
                }, 1000);
            } else {
                console.log('🔍 No reset modal flag found');
            }
        }

        // Setup Reset Password Form Handler
        function setupResetPasswordForm() {
            console.log('🔧 Setting up reset password form handler');

            $('#resetPasswordStaffForm').off('submit').on('submit', function (e) {
                e.preventDefault();
                console.log('📝 Reset password form submitted');

                var newPassword = $('#newStaffPassword').val();
                var confirmPassword = $('#confirmStaffPassword').val();
                var email = $('#resetStaffEmail').val();
                var resetToken = $('#resetStaffToken').val();

                console.log('📊 Form data:', {
                    email: email,
                    newPasswordLength: newPassword ? newPassword.length : 0,
                    confirmPasswordLength: confirmPassword ? confirmPassword.length : 0,
                    hasToken: !!resetToken
                });

                // Validation
                if (!newPassword || !confirmPassword) {
                    showResetStaffError('กรุณากรอกข้อมูลให้ครบถ้วน');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    showResetStaffError('รหัสผ่านและรหัสผ่านยืนยันไม่ตรงกัน');
                    return;
                }

                if (newPassword.length < 8) {
                    showResetStaffError('รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร');
                    return;
                }

                if (!email || !resetToken) {
                    showResetStaffError('ข้อมูลรีเซ็ตไม่ครบถ้วน กรุณาลองใหม่');
                    return;
                }

                // แสดง Loading
                $('#resetStaffSuccessAlert').hide();
                $('#resetStaffErrorAlert').hide();
                $('#resetStaffLoadingIndicator').show();
                $('#submitResetStaffPassword').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> กำลังดำเนินการ...');

                console.log('📤 Sending reset password request...');

                // ส่งข้อมูลไป Backend
                $.ajax({
                    url: window.base_url + 'user/changePasswordAjax',
                    type: 'POST',
                    data: {
                        email: email,
                        reset_token: resetToken,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log('📥 Reset password response:', response);
                        $('#resetStaffLoadingIndicator').hide();

                        if (response && response.status === 'success') {
                            console.log('✅ Password reset successful');
                            $('#resetStaffSuccessAlert').show();
                            $('#resetStaffErrorAlert').hide();
                            $('#resetPasswordStaffForm')[0].reset();

                            $('#resetStaffSuccessAlert').html(`
                        <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                        เปลี่ยนรหัสผ่านสำเร็จ! กำลังนำคุณไปหน้าเข้าสู่ระบบ...
                    `);

                            // Redirect หลัง 3 วินาที
                            setTimeout(function () {
                                var redirectUrl = response.redirect_url || (window.base_url + 'user');
                                console.log('🔄 Redirecting to:', redirectUrl);
                                window.location.href = redirectUrl;
                            }, 3000);

                        } else {
                            console.log('❌ Password reset failed:', response.message);
                            showResetStaffError(response && response.message ? response.message : 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('❌ Reset password ajax error:', error);
                        $('#resetStaffLoadingIndicator').hide();
                        showResetStaffError('เกิดข้อผิดพลาดในการส่งคำขอ กรุณาลองใหม่อีกครั้ง');
                    }
                });
            });

            console.log('✅ Reset password form handler setup completed');
        }

        // Helper function สำหรับแสดง Error
        function showResetStaffError(message) {
            $('#resetStaffErrorAlert').show();
            $('#resetStaffErrorMessage').text(message);
            $('#resetStaffSuccessAlert').hide();
            $('#submitResetStaffPassword').prop('disabled', false).html('<i class="fas fa-save me-2"></i> บันทึกรหัสผ่านใหม่');
        }

        // Setup Password Strength สำหรับบุคลากรภายใน
        function setupStaffPasswordStrength() {
            console.log('💪 Setting up staff password strength checker');

            // เรียกใช้เมื่อผู้ใช้พิมพ์รหัสผ่านใหม่
            $('#newStaffPassword').on('input', function () {
                const password = $(this).val();
                checkPasswordStrength(password, 'staff');
            });

            console.log('✅ Staff password strength checker setup completed');
        }

        // เพิ่มการตรวจสอบ Real-time Matching สำหรับบุคลากรภายใน
        function setupStaffPasswordMatching() {
            console.log('🔄 Setting up staff password matching checker');

            function checkPasswordMatch() {
                const newPassword = $('#newStaffPassword').val();
                const confirmPassword = $('#confirmStaffPassword').val();

                // ไม่ตรวจสอบหากยังไม่มีข้อมูลในช่องใดช่องหนึ่ง
                if (!newPassword || !confirmPassword) {
                    return;
                }

                // ตรวจสอบว่าตรงกันหรือไม่
                const isMatching = newPassword === confirmPassword;
                const confirmInput = $('#confirmStaffPassword');

                if (isMatching) {
                    confirmInput.removeClass('is-invalid').addClass('is-valid');
                    console.log('✅ Staff passwords match');
                } else {
                    confirmInput.removeClass('is-valid').addClass('is-invalid');
                    console.log('❌ Staff passwords do not match');
                }
            }

            // เรียกใช้เมื่อมีการเปลี่ยนแปลงในช่องรหัสผ่าน
            $('#newStaffPassword, #confirmStaffPassword').on('input', checkPasswordMatch);

            console.log('✅ Staff password matching checker setup completed');
        }

        // *** PUBLIC RESET PASSWORD SYSTEM ***

        // Public Forgot Password Form Handler
        function setupPublicForgotPasswordForm() {
            console.log('🏛️ Setting up public forgot password form');

            $('#forgotPasswordPublicForm').off('submit').on('submit', function (e) {
                e.preventDefault();

                var email = $('#modal-public-email').val();

                if (!validateEmail(email)) {
                    $('#errorPublicAlert').show();
                    $('#errorPublicMessage').text('กรุณากรอกอีเมลให้ถูกต้อง');
                    return;
                }

                $('#successPublicAlert').hide();
                $('#errorPublicAlert').hide();
                $('#loadingPublicIndicator').show();
                $('#submitForgotPasswordPublic').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> กำลังดำเนินการ...');

                console.log('📤 Sending public forgot password request for:', email);

                $.ajax({
                    url: window.base_url + 'user/sendEmailPublicAjax',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function (response) {
                        console.log('📥 Public forgot password response:', response);
                        $('#loadingPublicIndicator').hide();

                        if (response && response.status === 'success') {
                            $('#successPublicAlert').show();
                            $('#errorPublicAlert').hide();
                            $('#forgotPasswordPublicForm')[0].reset();

                            $('#successPublicAlert').html(`
                        <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                        ส่งอีเมลสำเร็จ! กรุณาตรวจสอบอีเมลของคุณเพื่อรีเซ็ตรหัสผ่าน
                    `);

                            let autoCloseTimer = setTimeout(function () {
                                closeModalSafely('forgotPasswordPublicModal');
                            }, 5000);

                            $('#submitForgotPasswordPublic').html('<i class="fas fa-check-circle me-2"></i> ตกลง').off('click').on('click', function () {
                                clearTimeout(autoCloseTimer);
                                closeModalSafely('forgotPasswordPublicModal');
                            });

                        } else {
                            $('#errorPublicAlert').show();
                            $('#errorPublicMessage').text(response && response.message ? response.message : 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                            $('#successPublicAlert').hide();
                            $('#submitForgotPasswordPublic').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('❌ Public forgot password error:', error);
                        $('#loadingPublicIndicator').hide();
                        $('#errorPublicAlert').show();
                        $('#errorPublicMessage').text('เกิดข้อผิดพลาดในการส่งคำขอ');
                        $('#successPublicAlert').hide();
                        $('#submitForgotPasswordPublic').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน');
                    }
                });
            });

            console.log('✅ Public forgot password form setup completed');
        }

        // Public Reset Password Modal Handler
        function checkAndShowPublicResetModal() {
            console.log('🔍 Checking for public reset password modal flag...');

            if (typeof window.show_reset_public_modal !== 'undefined' && window.show_reset_public_modal === true) {
                console.log('🏛️ Show public reset modal flag found - setting up modal');

                // ตั้งค่าข้อมูลใน Modal
                if (typeof window.reset_public_email !== 'undefined' && window.reset_public_email) {
                    $('#resetPublicEmail').val(window.reset_public_email);
                    $('#resetEmailDisplay').text(window.reset_public_email);
                    console.log('📧 Public email set to modal:', window.reset_public_email);
                }

                if (typeof window.reset_public_token !== 'undefined' && window.reset_public_token) {
                    $('#resetPublicToken').val(window.reset_public_token);
                    console.log('🔑 Public token set to modal');
                }

                // แสดง Modal หลังจาก DOM พร้อม
                setTimeout(function () {
                    console.log('📋 Showing public reset password modal...');
                    $('#resetPasswordPublicModal').modal('show');
                }, 1000);
            } else {
                console.log('🔍 No public reset modal flag found');
            }
        }

        // Public Reset Password Form Handler
        function setupPublicResetPasswordForm() {
            console.log('🔧 Setting up public reset password form handler');

            $('#resetPasswordPublicForm').off('submit').on('submit', function (e) {
                e.preventDefault();
                console.log('📝 Public reset password form submitted');

                var newPassword = $('#newPassword').val();
                var confirmPassword = $('#confirmPassword').val();
                var email = $('#resetPublicEmail').val();
                var resetToken = $('#resetPublicToken').val();

                console.log('📊 Public form data:', {
                    email: email,
                    newPasswordLength: newPassword ? newPassword.length : 0,
                    confirmPasswordLength: confirmPassword ? confirmPassword.length : 0,
                    hasToken: !!resetToken
                });

                // Validation
                if (!newPassword || !confirmPassword) {
                    showPublicResetError('กรุณากรอกข้อมูลให้ครบถ้วน');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    showPublicResetError('รหัสผ่านและรหัสผ่านยืนยันไม่ตรงกัน');
                    return;
                }

                if (newPassword.length < 8) {
                    showPublicResetError('รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร');
                    return;
                }

                if (!email || !resetToken) {
                    showPublicResetError('ข้อมูลรีเซ็ตไม่ครบถ้วน กรุณาลองใหม่');
                    return;
                }

                // แสดง Loading
                $('#resetSuccessAlert').hide();
                $('#resetErrorAlert').hide();
                $('#resetLoadingIndicator').show();
                $('#submitResetPassword').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> กำลังดำเนินการ...');

                console.log('📤 Sending public reset password request...');

                // ส่งข้อมูลไป Backend
                $.ajax({
                    url: window.base_url + 'user/changePasswordPublicAjax',
                    type: 'POST',
                    data: {
                        email: email,
                        reset_token: resetToken,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log('📥 Public reset password response:', response);
                        $('#resetLoadingIndicator').hide();

                        if (response && response.status === 'success') {
                            console.log('✅ Public password reset successful');
                            $('#resetSuccessAlert').show();
                            $('#resetErrorAlert').hide();
                            $('#resetPasswordPublicForm')[0].reset();

                            $('#resetSuccessAlert').html(`
                        <i class="fas fa-check-circle me-2" style="color: var(--success);"></i>
                        เปลี่ยนรหัสผ่านสำเร็จ! กำลังนำคุณไปหน้าเข้าสู่ระบบ...
                    `);

                            // Redirect หลัง 3 วินาที
                            setTimeout(function () {
                                var redirectUrl = response.redirect_url || (window.base_url + 'user');
                                console.log('🔄 Redirecting to:', redirectUrl);
                                window.location.href = redirectUrl;
                            }, 3000);

                        } else {
                            console.log('❌ Public password reset failed:', response.message);
                            showPublicResetError(response && response.message ? response.message : 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('❌ Public reset password ajax error:', error);
                        $('#resetLoadingIndicator').hide();
                        showPublicResetError('เกิดข้อผิดพลาดในการส่งคำขอ กรุณาลองใหม่อีกครั้ง');
                    }
                });
            });

            console.log('✅ Public reset password form handler setup completed');
        }

        // Helper function สำหรับแสดง Error (Public)
        function showPublicResetError(message) {
            $('#resetErrorAlert').show();
            $('#resetErrorMessage').text(message);
            $('#resetSuccessAlert').hide();
            $('#submitResetPassword').prop('disabled', false).html('<i class="fas fa-save me-2"></i> บันทึกรหัสผ่านใหม่');
        }

        // Function สำหรับปุ่ม onclick (สำหรับ submitForgotPasswordPublic)
        function submitForgotPasswordPublicForm() {
            console.log('🔘 Public forgot password button clicked');
            $('#forgotPasswordPublicForm').trigger('submit');
        }

        // Password Strength Checker สำหรับประชาชน
        function setupPublicPasswordStrength() {
            console.log('💪 Setting up public password strength checker');

            $('#newPassword').on('input', function () {
                checkPasswordStrength($(this).val(), 'public');
            });

            console.log('✅ Public password strength checker setup completed');
        }

        // *** UNIVERSAL PASSWORD STRENGTH CHECKER ***

        // Function ตรวจสอบความแข็งแรงของรหัสผ่าน (แก้ไขเฉพาะส่วนสี)
        function checkPasswordStrength(password, type) {
            console.log(`🔍 Checking password strength for ${type} user`);

            // กำหนด elements ตาม type
            let strengthMeter, strengthFill, strengthText;
            if (type === 'staff') {
                strengthMeter = $('.password-strength-staff');
                strengthFill = $('.strength-fill-staff');
                strengthText = $('.strength-text-staff');
            } else {
                strengthMeter = $('.password-strength');
                strengthFill = $('.strength-fill');
                strengthText = $('.strength-text');
            }

            // ซ่อน indicator หากไม่มีรหัสผ่าน
            if (password.length === 0) {
                strengthMeter.hide();
                return;
            }

            // แสดง indicator
            strengthMeter.show();

            let strength = 0;
            let feedback = '';
            let criteriaCount = 0;
            let criteria = [];

            // ตรวจสอบเกณฑ์ต่างๆ
            // 1. ความยาวอย่างน้อย 8 ตัวอักษร
            if (password.length >= 8) {
                strength += 25;
                criteriaCount++;
                criteria.push('ความยาวเพียงพอ');
            }

            // 2. มีทั้งตัวพิมพ์เล็กและใหญ่
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                strength += 25;
                criteriaCount++;
                criteria.push('ตัวพิมพ์เล็ก/ใหญ่');
            }

            // 3. มีตัวเลข
            if (/\d/.test(password)) {
                strength += 25;
                criteriaCount++;
                criteria.push('ตัวเลข');
            }

            // 4. มีอักขระพิเศษ
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                strength += 25;
                criteriaCount++;
                criteria.push('อักขระพิเศษ');
            }

            // กำหนดสีและข้อความตามระดับความแข็งแรง
            let backgroundColor;

            if (strength < 50) {
                backgroundColor = '#dc3545'; // แดง
                feedback = `รหัสผ่านอ่อน (${criteriaCount}/4 เกณฑ์)`;
            } else if (strength < 75) {
                backgroundColor = '#ffc107'; // เหลือง
                feedback = `รหัสผ่านปานกลาง (${criteriaCount}/4 เกณฑ์)`;
            } else if (strength < 100) {
                backgroundColor = '#fd7e14'; // ส้ม
                feedback = `รหัสผ่านดี (${criteriaCount}/4 เกณฑ์)`;
            } else {
                backgroundColor = '#28a745'; // เขียว
                feedback = `รหัสผ่านแข็งแรง (${criteriaCount}/4 เกณฑ์)`;
            }

            // อัปเดต UI - ใช้ setProperty เพื่อ force override
            if (strengthFill.length > 0) {
                strengthFill[0].style.setProperty('background-color', backgroundColor, 'important');
                strengthFill[0].style.setProperty('width', strength + '%', 'important');
            }

            strengthText.text(feedback);
            strengthText.attr('title', 'เกณฑ์ที่ผ่าน: ' + criteria.join(', '));

            console.log(`📊 Password strength: ${strength}% (${criteriaCount}/4 criteria)`);

            return {
                strength: strength,
                criteriaCount: criteriaCount,
                criteria: criteria,
                feedback: feedback
            };
        }

        // *** PASSWORD MATCHING VALIDATORS ***

        // Public Password Matching Checker
        function setupPublicPasswordMatching() {
            console.log('🔄 Setting up public password matching checker');

            function checkPasswordMatch() {
                const newPassword = $('#newPassword').val();
                const confirmPassword = $('#confirmPassword').val();

                // ไม่ตรวจสอบหากยังไม่มีข้อมูลในช่องใดช่องหนึ่ง
                if (!newPassword || !confirmPassword) {
                    return;
                }

                // ตรวจสอบว่าตรงกันหรือไม่
                const isMatching = newPassword === confirmPassword;
                const confirmInput = $('#confirmPassword');

                if (isMatching) {
                    confirmInput.removeClass('is-invalid').addClass('is-valid');
                    console.log('✅ Public passwords match');
                } else {
                    confirmInput.removeClass('is-valid').addClass('is-invalid');
                    console.log('❌ Public passwords do not match');
                }
            }

            // เรียกใช้เมื่อมีการเปลี่ยนแปลงในช่องรหัสผ่าน
            $('#newPassword, #confirmPassword').on('input', checkPasswordMatch);

            console.log('✅ Public password matching checker setup completed');
        }

        // *** MODAL EVENT HANDLERS ***

        // Setup Modal Event Handlers
        function setupModalEventHandlers() {
            console.log('🎭 Setting up modal event handlers');

            // Staff Forgot Password Modal Events
            $('#forgotPasswordModal').off('show.bs.modal').on('show.bs.modal', function () {
                resetForgotPasswordForm('staff');
            });

            $('#forgotPasswordModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                cleanupModalBackdrop();
                resetForgotPasswordForm('staff');
            });

            // Public Forgot Password Modal Events
            $('#forgotPasswordPublicModal').off('show.bs.modal').on('show.bs.modal', function () {
                resetForgotPasswordForm('public');
            });

            $('#forgotPasswordPublicModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                cleanupModalBackdrop();
                resetForgotPasswordForm('public');
            });

            // Staff Reset Password Modal Events
            $('#resetPasswordStaffModal').off('show.bs.modal').on('show.bs.modal', function () {
                resetResetPasswordForm('staff');
            });

            $('#resetPasswordStaffModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                cleanupModalBackdrop();
                resetResetPasswordForm('staff');
            });

            // Public Reset Password Modal Events
            $('#resetPasswordPublicModal').off('show.bs.modal').on('show.bs.modal', function () {
                resetResetPasswordForm('public');
            });

            $('#resetPasswordPublicModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                cleanupModalBackdrop();
                resetResetPasswordForm('public');
            });

            console.log('✅ Modal event handlers setup completed');
        }

        // *** FORM RESET FUNCTIONS ***

        // Reset Forgot Password Form
        function resetForgotPasswordForm(type) {
            console.log(`🔄 Resetting forgot password form for ${type}`);

            if (type === 'staff') {
                $('#forgotPasswordForm')[0].reset();
                $('#successAlert').hide();
                $('#errorAlert').hide();
                $('#loadingIndicator').hide();
                $('#submitForgotPassword').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน');
            } else if (type === 'public') {
                $('#forgotPasswordPublicForm')[0].reset();
                $('#successPublicAlert').hide();
                $('#errorPublicAlert').hide();
                $('#loadingPublicIndicator').hide();
                $('#submitForgotPasswordPublic').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน');
            }
        }

        // Reset Reset Password Form
        function resetResetPasswordForm(type) {
            console.log(`🔄 Resetting reset password form for ${type}`);

            if (type === 'staff') {
                $('#resetPasswordStaffForm')[0].reset();
                $('#resetStaffSuccessAlert').hide();
                $('#resetStaffErrorAlert').hide();
                $('#resetStaffLoadingIndicator').hide();
                $('.password-strength-staff').hide();
                $('#submitResetStaffPassword').prop('disabled', false).html('<i class="fas fa-save me-2"></i> บันทึกรหัสผ่านใหม่');

                // Reset validation classes
                $('#newStaffPassword, #confirmStaffPassword').removeClass('is-valid is-invalid');
            } else if (type === 'public') {
                $('#resetPasswordPublicForm')[0].reset();
                $('#resetSuccessAlert').hide();
                $('#resetErrorAlert').hide();
                $('#resetLoadingIndicator').hide();
                $('.password-strength').hide();
                $('#submitResetPassword').prop('disabled', false).html('<i class="fas fa-save me-2"></i> บันทึกรหัสผ่านใหม่');

                // Reset validation classes
                $('#newPassword, #confirmPassword').removeClass('is-valid is-invalid');
            }
        }

        // *** RATE LIMITING FUNCTIONS ***

        // Handle Countdown Timer for Rate Limiting
        function handleCountdownTimer(buttonId, remainingSeconds) {
            console.log(`⏱️ Starting countdown timer for ${buttonId}: ${remainingSeconds} seconds`);

            const button = $('#' + buttonId);
            let timeLeft = remainingSeconds;

            const countdownInterval = setInterval(function () {
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    button.prop('disabled', false)
                        .html('<i class="fas fa-paper-plane me-2"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน');
                    console.log(`✅ Countdown completed for ${buttonId}`);
                    return;
                }

                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                const timeDisplay = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                button.html(`<i class="fas fa-clock me-2"></i> รอ ${timeDisplay} นาที`);
                timeLeft--;
            }, 1000);
        }

        // *** SECURITY FUNCTIONS ***

        // Auto-hide Password Notification
        function showPasswordAutoHideNotification(inputElement) {
            const notification = $(`
                <div class="password-auto-hide-notification" style="
                    position: absolute;
                    top: -30px;
                    right: 0;
                    background: rgba(255, 193, 7, 0.9);
                    color: #856404;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 11px;
                    z-index: 1000;
                    animation: fadeInOut 2s ease-in-out;
                ">
                    🔒 ซ่อนรหัสผ่านอัตโนมัติเพื่อความปลอดภัย
                </div>
            `);

            const inputWrapper = inputElement.closest('.input-wrapper');
            inputWrapper.css('position', 'relative').append(notification);

            // ลบ notification หลังจาก 2 วินาที
            setTimeout(() => {
                notification.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 2000);

            // เพิ่ม animation CSS ถ้ายังไม่มี
            if (!$('#password-auto-hide-animation').length) {
                $('head').append(`
                    <style id="password-auto-hide-animation">
                        @keyframes fadeInOut {
                            0%, 100% { opacity: 0; transform: translateY(-5px); }
                            20%, 80% { opacity: 1; transform: translateY(0); }
                        }
                        .toggling {
                            transform: scale(0.8) !important;
                            opacity: 0.6 !important;
                        }
                    </style>
                `);
            }
        }

        // *** VALIDATION FUNCTIONS ***

        // Email Validation Function
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // *** SAFETY CLEANUP ***

        // Safety Cleanup ทุก 5 วินาที
        setInterval(function () {
            // ตรวจสอบว่ามี backdrop หลงเหลืออยู่หรือไม่ โดยที่ไม่มี modal ที่เปิดอยู่
            if ($('.modal-backdrop').length > 0 && $('.modal.show').length === 0) {
                console.log('🧹 Cleaning up orphaned modal backdrop');
                cleanupModalBackdrop();
            }
        }, 5000);

        // ======================== ERROR HANDLERS ========================

        // reCAPTCHA Fallback
        setTimeout(function () {
            if (!window.recaptchaReady) {
                console.warn('⚠️ reCAPTCHA not loaded after 10 seconds');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'การโหลด reCAPTCHA ล่าช้า',
                        text: 'หาก reCAPTCHA ไม่โหลด กรุณารีเฟรชหน้าเว็บ',
                        toast: true,
                        position: 'top-end',
                        timer: 5000,
                        showConfirmButton: false
                    });
                }
            }
        }, 10000);

        // Global Error Handler
        window.addEventListener('error', function (e) {
            if (e.error && e.error.message && e.error.message.includes('reCAPTCHA')) {
                console.error('reCAPTCHA Error captured:', e.error);
            }

            if (e.error && e.error.message && e.error.message.includes('Chart is not defined')) {
                console.warn('Chart.js not loaded - this is expected on login page');
                return true;
            }
        });

        // ======================== DEBUG FUNCTIONS ========================

        // Debug reCAPTCHA
        window.debugRecaptcha = function () {
            console.log('=== reCAPTCHA Debug Info ===');
            console.log('recaptchaReady:', window.recaptchaReady);
            console.log('RECAPTCHA_KEY:', window.RECAPTCHA_KEY ? window.RECAPTCHA_KEY.substring(0, 10) + '...' : 'NOT SET');
            console.log('grecaptcha object:', typeof window.grecaptcha);
            console.log('base_url:', window.base_url);
            console.log('isSubmitting:', isSubmitting);
        };

        // Debug Tabs
        window.debugTabs = function () {
            console.log('=== TAB DEBUG INFO ===');
            console.log('Tab buttons found:', $('.tab-btn').length);
            console.log('Forms found:', $('.login-form').length);
        };

        // Debug 2FA
        window.debug2FA = function () {
            console.log('=== 2FA DEBUG INFO ===');
            console.log('is2FAActive:', is2FAActive);
            console.log('isSubmitting:', isSubmitting);
            console.log('temp_user_type:', window.temp_user_type);
            console.log('modalInstance:', modalInstance);
            console.log('googleAuthModal exists:', $('#googleAuthModal').length > 0);
            console.log('OTP inputs count:', $('.otp-input').length);
            console.log('Current OTP value:', $('#otpValue').val());
        };

        // ======================== GLOBAL EXPORTS ========================

        window.loginSystem = {
            // Main functions
            showGoogleAuthModal,
            cancelGoogleAuth,
            submitOTPWhenReady,
            updateRememberDevice,

            // Utility functions
            clearOTPInputs,
            updateSecurityInfo,
            validateEmail,
            addDeviceFingerprint,

            // Debug functions
            debugRecaptcha: window.debugRecaptcha,
            debugTabs: window.debugTabs,
            debug2FA: window.debug2FA,

            // State getters
            get is2FAActive() { return is2FAActive; },
            get isSubmitting() { return isSubmitting; },
            get modalInstance() { return modalInstance; }
        };

        console.log('=== FIXED COMPLETE LOGIN & 2FA SYSTEM END ===');
    </script>
	
	
	<script>
// ======================== TOUR GUIDE SYSTEM (FIXED VERSION) ========================

const tourSteps = [
    {
        target: '[data-tab="citizen"]',
        title: 'เข้าสู่ระบบสำหรับประชาชน',
        content: 'นี่คือแท็บ <strong>เข้าสู่ระบบบริการอิเล็กทรอนิกส์ สำหรับประชาชน</strong><br><br>👤 ใช้สำหรับประชาชนทั่วไปที่ต้องการเข้าใช้บริการออนไลน์<br>📧 เข้าสู่ระบบด้วยอีเมลและรหัสผ่าน',
        icon: 'fa-users',
        label: 'เข้าสู่ระบบสำหรับประชาชน',
        labelPosition: 'top',
        arrowDirection: 'down'
    },
    {
        target: '[data-tab="staff"]',
        title: 'เข้าสู่ระบบบุคลากรภายใน',
        content: 'นี่คือแท็บ <strong>เข้าสู่ระบบบสำหรับบุคลากรภายใน</strong><br><br>👔 ใช้สำหรับเจ้าหน้าที่และบุคลากรภายในองค์กร<br>🔐 เข้าสู่ระบบด้วยอีเมลและรหัสผ่าน',
        icon: 'fa-user-tie',
        iconType: 'warning',
        label: 'เข้าสู่ระบบบุคลากรภายใน',
        labelPosition: 'top',
        arrowDirection: 'down'
    },
    {
        target: '.webboard-section h3',
        title: 'กระทู้แนะนำจากกระดานสนทนาภายใน',
        content: '<strong>📢 กระทู้แนะนำและข่าวสาร</strong><br><br>🎯 แสดงกระทู้ล่าสุดและยอดนิยมจากกระดานสนทนาภายในเครือข่าย<br>💬 สำหรับบุคลากรภายในแลกเปลี่ยนความรู้และประสบการณ์<br>🔐 เข้าสู่ระบบบุคลากรภายในก่อนใช้งาน',
        icon: 'fa-comments',
        iconType: 'success',
        label: 'กระดานสนทนภายในเครือข่าย',
        labelPosition: 'top',
        arrowDirection: 'down'
    }
];

let currentStep = 0;
let isActive = false;

// ✅ สร้าง Safe Click Handler
function createSafeClickHandler() {
    return function(e) {
        // ตรวจสอบทุกอย่างก่อนทำงาน
        if (!isActive) {
            return;
        }
        
        const tooltip = document.querySelector('.tour-tooltip');
        
        // ตรวจสอบว่า tooltip มีอยู่และอยู่ใน DOM
        if (!tooltip || !document.body.contains(tooltip)) {
            return;
        }
        
        // ใช้ try-catch เพื่อความปลอดภัย
        try {
            if (!tooltip.contains(e.target)) {
                const closeBtn = e.target.closest('.tour-close');
                const nextBtn = e.target.closest('.tour-next');
                const prevBtn = e.target.closest('.tour-prev');
                const finishBtn = e.target.closest('.tour-finish');
                
                if (!closeBtn && !nextBtn && !prevBtn && !finishBtn) {
                    console.log('Clicked outside tooltip');
                }
            }
        } catch (error) {
            console.error('Error in click handler:', error);
            // ถ้าเกิด error ให้ปิด tour
            if (isActive) {
                endTour();
            }
        }
    };
}

// ✅ สร้าง Safe Key Handler
function createSafeKeyHandler() {
    return function(e) {
        if (!isActive) {
            return;
        }
        
        try {
            if (e.key === 'Escape') {
                console.log('ESC pressed - ending tour');
                endTour();
            } else if (e.key === 'ArrowRight') {
                const nextBtn = document.querySelector('.tour-next');
                if (nextBtn) nextBtn.click();
            } else if (e.key === 'ArrowLeft') {
                const prevBtn = document.querySelector('.tour-prev');
                if (prevBtn) prevBtn.click();
            }
        } catch (error) {
            console.error('Error in key handler:', error);
        }
    };
}

function startTour() {
    console.log('🎯 Starting tour...');
    
    // ✅ ลบ event listeners เก่าก่อน
    if (window.tourClickHandler) {
        document.removeEventListener('click', window.tourClickHandler);
        window.tourClickHandler = null;
        console.log('🗑️ Removed old click listener');
    }
    if (window.tourKeyHandler) {
        document.removeEventListener('keydown', window.tourKeyHandler);
        window.tourKeyHandler = null;
        console.log('🗑️ Removed old keydown listener');
    }
    
    // ✅ ตั้งค่า flag
    isActive = true;
    currentStep = 0;
    
    // ซ่อนปุ่ม Start Tour
    const startBtn = document.querySelector('.start-tour-btn');
    if (startBtn) {
        startBtn.style.display = 'none';
    }
    
    // แสดง overlay
    const overlay = document.getElementById('tourOverlay');
    if (overlay) {
        overlay.classList.add('active');
    }
    
    // ✅ สร้าง event handlers ใหม่ที่ปลอดภัย
    window.tourClickHandler = createSafeClickHandler();
    window.tourKeyHandler = createSafeKeyHandler();
    
    // เพิ่ม event listeners
    document.addEventListener('click', window.tourClickHandler);
    document.addEventListener('keydown', window.tourKeyHandler);
    console.log('✅ Event listeners attached');
    
    // เริ่มแสดง step แรก
    showStep(0);
    
    console.log('✅ Tour started successfully');
}

function showStep(stepIndex) {
    console.log(`📍 Showing step ${stepIndex + 1}/${tourSteps.length}`);
    
    if (stepIndex < 0 || stepIndex >= tourSteps.length) {
        console.error('Invalid step index:', stepIndex);
        return;
    }
    
    currentStep = stepIndex;
    const step = tourSteps[stepIndex];
    const targetElement = document.querySelector(step.target);
    
    if (!targetElement) {
        console.error('Target element not found:', step.target);
        return;
    }
    
    // ลบ elements เก่า
    removeExistingTourElements();
    
    // Scroll ไปยัง element
    targetElement.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
    
    // รอให้ scroll เสร็จก่อนแสดง elements
    setTimeout(() => {
        if (isActive) {  // ✅ เช็คว่ายังทำงานอยู่หรือไม่
            createSpotlight(targetElement);
            createArrow(targetElement, step.arrowDirection);
            createLabel(targetElement, step.label, step.labelPosition);
            createTooltip(step, stepIndex);
            
            console.log(`✅ Step ${stepIndex + 1} displayed`);
        }
    }, 300);
}

function createSpotlight(element) {
    const rect = element.getBoundingClientRect();
    const spotlight = document.createElement('div');
    spotlight.className = 'spotlight pulse';
    spotlight.id = 'tourSpotlight';
    
    const padding = 5;
    
    spotlight.style.top = (rect.top + window.scrollY - padding) + 'px';
    spotlight.style.left = (rect.left + window.scrollX - padding) + 'px';
    spotlight.style.width = (rect.width + padding * 2) + 'px';
    spotlight.style.height = (rect.height + padding * 2) + 'px';
    
    document.body.appendChild(spotlight);
}

function createArrow(element, direction) {
    // ไม่ทำอะไร - ลูกศรจะถูกซ่อนด้วย CSS
    return;
}

function createLabel(element, text, position) {
    const rect = element.getBoundingClientRect();
    const label = document.createElement('div');
    label.className = `tour-label ${position}`;
    label.id = 'tourLabel';
    label.textContent = text;
    
    document.body.appendChild(label);
    
    const labelRect = label.getBoundingClientRect();
    let top, left;
    
    const extraPadding = 30;
    
    switch(position) {
        case 'top':
            top = rect.top + window.scrollY - labelRect.height - 20 - extraPadding;
            left = rect.left + window.scrollX + (rect.width / 2) - (labelRect.width / 2);
            break;
        case 'bottom':
            top = rect.bottom + window.scrollY + 20;
            left = rect.left + window.scrollX + (rect.width / 2) - (labelRect.width / 2);
            break;
        case 'left':
            top = rect.top + window.scrollY + (rect.height / 2) - (labelRect.height / 2);
            left = rect.left + window.scrollX - labelRect.width - 20;
            break;
        case 'right':
            top = rect.top + window.scrollY + (rect.height / 2) - (labelRect.height / 2);
            left = rect.right + window.scrollX + 20;
            break;
    }
    
    label.style.top = top + 'px';
    label.style.left = left + 'px';
}

function createTooltip(step, stepIndex) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tour-tooltip';
    tooltip.id = 'tourTooltip';
    
    const iconClass = step.iconType || 'primary';
    
    tooltip.innerHTML = `
        <div class="tour-tooltip-header">
            <div class="tour-tooltip-icon ${iconClass}">
                <i class="fas ${step.icon}"></i>
            </div>
            <div class="tour-tooltip-title">${step.title}</div>
        </div>
        <div class="tour-tooltip-content">${step.content}</div>
        <div class="tour-tooltip-footer">
            <div class="tour-progress">
                <div class="tour-progress-dots">
                    ${tourSteps.map((_, i) => `
                        <div class="tour-progress-dot ${i === stepIndex ? 'active' : ''}"></div>
                    `).join('')}
                </div>
                <div class="tour-progress-text">${stepIndex + 1}/${tourSteps.length}</div>
            </div>
            <div class="tour-buttons">
                ${stepIndex > 0 ? 
                    '<button class="tour-btn tour-prev"><i class="fas fa-arrow-left"></i> ก่อนหน้า</button>' : 
                    '<button class="tour-btn tour-close" style="background: #E74C3C;">ปิดทัวร์</button>'}
                ${stepIndex < tourSteps.length - 1 ? 
                    '<button class="tour-btn tour-next">ถัดไป <i class="fas fa-arrow-right"></i></button>' : 
                    '<button class="tour-btn tour-finish" style="background: #27AE60;">เสร็จสิ้น <i class="fas fa-check"></i></button>'}
            </div>
        </div>
    `;
    
    document.body.appendChild(tooltip);
    
    setTimeout(() => {
        positionTooltip(tooltip);
        attachTooltipEventListeners(tooltip, stepIndex);
    }, 50);
}

function positionTooltip(tooltip) {
    const rect = tooltip.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    
    let top = (viewportHeight - rect.height) / 2 + window.scrollY;
    let left = (viewportWidth - rect.width) / 2;
    
    // ✅ สำหรับ Step 3 ให้ขยับขึ้นด้านบนมากขึ้น
    if (currentStep === 2) {  // Step 3 คือ index 2
        top = window.scrollY + 0;  // ห่างจากด้านบน 80px
    }
    
    top = Math.max(20, Math.min(top, window.scrollY + viewportHeight - rect.height - 20));
    left = Math.max(20, Math.min(left, viewportWidth - rect.width - 20));
    
    tooltip.style.top = top + 'px';
    tooltip.style.left = left + 'px';
}

function attachTooltipEventListeners(tooltip, stepIndex) {
    const closeBtn = tooltip.querySelector('.tour-close');
    const nextBtn = tooltip.querySelector('.tour-next');
    const prevBtn = tooltip.querySelector('.tour-prev');
    const finishBtn = tooltip.querySelector('.tour-finish');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            endTour();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (stepIndex < tourSteps.length - 1) {
                showStep(stepIndex + 1);
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (stepIndex > 0) {
                showStep(stepIndex - 1);
            }
        });
    }
    
    if (finishBtn) {
        finishBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            endTour();
        });
    }
}

function removeExistingTourElements() {
    console.log('🧹 Removing tour elements...');
    
    const spotlight = document.getElementById('tourSpotlight');
    if (spotlight) {
        spotlight.remove();
        console.log('  ✓ Spotlight removed');
    }
    
    const arrow = document.getElementById('tourArrow');
    if (arrow) {
        arrow.remove();
        console.log('  ✓ Arrow removed');
    }
    
    const label = document.getElementById('tourLabel');
    if (label) {
        label.remove();
        console.log('  ✓ Label removed');
    }
    
    const tooltip = document.getElementById('tourTooltip');
    if (tooltip) {
        tooltip.remove();
        console.log('  ✓ Tooltip removed');
    }
    
    console.log('✅ All tour elements removed');
}

function endTour() {
    console.log('🛑 Ending tour...');
    
    // ✅ 1. ตั้งค่า flag เป็น false ก่อนเป็นอันดับแรก
    isActive = false;
    
    // ✅ 2. ลบ event listeners ทันที
    if (window.tourClickHandler) {
        document.removeEventListener('click', window.tourClickHandler);
        window.tourClickHandler = null;
        console.log('🗑️ Click listener removed');
    }
    if (window.tourKeyHandler) {
        document.removeEventListener('keydown', window.tourKeyHandler);
        window.tourKeyHandler = null;
        console.log('🗑️ Keydown listener removed');
    }
    
    // ✅ 3. จากนั้นค่อยลบ elements
    removeExistingTourElements();
    
    // ซ่อน overlay
    const overlay = document.getElementById('tourOverlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
    
    // รอสักครู่ก่อนแสดงปุ่ม Start Tour
    setTimeout(() => {
        const startBtn = document.querySelector('.start-tour-btn');
        if (startBtn) {
            startBtn.style.display = 'flex';
            console.log('✅ Start Tour button shown');
        }
    }, 100);
    
    // เลื่อนขึ้นด้านบนแบบ smooth
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    
    // บันทึกว่าเคยดู tour แล้ว
    localStorage.setItem('tour_completed', 'true');
    
    console.log('✅ Tour ended successfully');
}

// Handle window resize
let resizeTimeout;
window.addEventListener('resize', () => {
    if (!isActive) return;
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        showStep(currentStep);
    }, 250);
});

// เริ่มทัวร์อัตโนมัติสำหรับผู้ใช้ใหม่ (ถ้าต้องการ)
window.addEventListener('load', () => {
    const autoStart = false;
    
    if (autoStart && !localStorage.getItem('tour_completed')) {
        setTimeout(() => {
            console.log('🚀 Auto-starting tour...');
            startTour();
        }, 2000);
    }
});

console.log('✅ Tour Guide System loaded successfully!');
</script>
		
		
		
<script>
/**
 * ✅ Webboard Posts Loader - Button Toggle Version
 */
document.addEventListener('DOMContentLoaded', function() {
    let currentType = 'latest';
    
    // Toggle Button Click
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            
            // Update active state
            toggleBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Load posts
            if (type !== currentType) {
                currentType = type;
                loadPosts(type);
            }
        });
    });
    
    // Load initial posts
    loadPosts('latest');
});

// ✅ Load Posts Function
async function loadPosts(type) {
    const API_URL = type === 'latest' 
        ? 'https://webboard.assystem.co.th/webboard_api/recent?limit=5'
        : 'https://webboard.assystem.co.th/webboard_api/trending?limit=5';
    
    const loading = document.getElementById('posts-loading');
    const list = document.getElementById('posts-list');
    const error = document.getElementById('posts-error');
    
    try {
        loading.style.display = 'flex';
        list.style.display = 'none';
        error.style.display = 'none';
        list.innerHTML = '';
        
        const response = await fetch(API_URL);
        const contentType = response.headers.get('content-type');
        
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response type');
        }
        
        const result = await response.json();
        const posts = result.data?.data || result.data || [];
        
        if (posts.length > 0) {
            displayPosts(posts);
        } else {
            throw new Error('No posts found');
        }
        
    } catch (err) {
        console.error(`Error loading ${type} posts:`, err);
        loading.style.display = 'none';
        error.style.display = 'flex';
    }
}

// ✅ Display Posts
function displayPosts(posts) {
    const loading = document.getElementById('posts-loading');
    const list = document.getElementById('posts-list');
    
    loading.style.display = 'none';
    list.style.display = 'flex';
    list.innerHTML = '';
    
    posts.forEach((post, index) => {
        const rankClass = index < 3 ? `rank-${index + 1}` : 'rank-default';
        
        const item = document.createElement('a');
        item.href = post.url;
        item.target = '_blank';
        item.className = 'post-item';
        
        item.innerHTML = `
            <div class="post-rank ${rankClass}">${index + 1}</div>
            
            <div class="post-main">
                <h4 class="post-title">${escapeHtml(post.title)}</h4>
                
                <div class="post-meta">
                    <div class="post-category" style="background: ${post.category.color};">
                        <i class="${post.category.icon}"></i>
                        <span>${escapeHtml(post.category.name)}</span>
                    </div>
                    
                    <div class="post-info">
    <div class="post-info-item">
        <i class="far fa-user"></i>
        <span>${censorName(post.author_name)}</span>
    </div>
    ${post.tenant_name ? `
    <div class="post-info-item tenant-name">
        ${censorTenant(post.tenant_name)}
    </div>
    ` : ''}
    <div class="post-info-item">
        <i class="far fa-clock"></i>
        <span>${formatTimeAgo(post.created_at)}</span>
    </div>
</div>
                </div>
            </div>
            
            <div class="post-stats">
                <div class="stat-item">
                    <span class="stat-value">${formatNum(post.stats.replies)}</span>
                    <span class="stat-label">ความคิดเห็น</span>
                </div>
            </div>
        `;
        
        list.appendChild(item);
    });
}

		
// ✅ Helper Functions
function formatNum(num) {
    num = parseInt(num) || 0;
    if (num >= 1000) return (num / 1000).toFixed(1) + 'k';
    return num.toString();
}

function formatTimeAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'เมื่อสักครู่';
    if (diff < 3600) return Math.floor(diff / 60) + ' นาทีที่แล้ว';
    if (diff < 86400) return Math.floor(diff / 3600) + ' ชั่วโมงที่แล้ว';
    if (diff < 604800) return Math.floor(diff / 86400) + ' วันที่แล้ว';
    return date.toLocaleDateString('th-TH', { month: 'short', day: 'numeric' });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
		
		
// ✅ เซ็นเซอร์ชื่อคน - แสดง 5-6 ตัวแรก + ***
function censorName(name) {
    if (!name) return 'ไม่ระบุชื่อ';
    
    const cleaned = name.trim();
    if (cleaned.length <= 6) return cleaned;
    
    const visible = cleaned.substring(0, 5);
    return visible + '***';
}

// ✅ เซ็นเซอร์ชื่อหน่วยงาน - แสดง 6-8 ตัวแรก + ...
function censorTenant(tenant) {
    if (!tenant) return '';
    
    const cleaned = tenant.trim();
    if (cleaned.length <= 8) return cleaned;
    
    const visible = cleaned.substring(0, 10);
    return visible + '...';
}

</script>
	
	
	<script>
        // Toggle password for Citizen form
        document.getElementById('toggleCitizenPassword').addEventListener('click', function() {
            const passwordField = document.getElementById('citizenPassword');
            const icon = this;

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Toggle password for Staff form
        document.getElementById('toggleStaffPassword').addEventListener('click', function() {
            const passwordField = document.getElementById('staffPassword');
            const icon = this;

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
	

</body>

</html>