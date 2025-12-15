<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
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
            
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-security: linear-gradient(135deg, #667eea 0%, #06B6D4 100%);
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
        }

        /* Header */
        .policy-header {
            background: var(--gradient-security);
            padding: 80px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .policy-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .policy-header-content {
            position: relative;
            z-index: 2;
        }

        .policy-icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 36px;
            color: white;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .policy-title {
            color: white;
            font-family: 'Kanit', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 15px;
        }

        .policy-subtitle {
            color: rgba(255,255,255,0.9);
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .policy-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .policy-meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
        }

        .policy-meta-item i {
            font-size: 1.1rem;
        }

        /* Navigation Pills */
        .nav-pills-container {
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            margin-top: -50px;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 40px;
        }

        .nav-pills-custom {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .nav-pills-custom::-webkit-scrollbar {
            height: 4px;
        }

        .nav-pills-custom::-webkit-scrollbar-track {
            background: var(--light);
            border-radius: 10px;
        }

        .nav-pills-custom::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        .nav-pill-item {
            padding: 10px 20px;
            background: var(--light);
            color: var(--dark);
            border-radius: 50px;
            text-decoration: none;
            white-space: nowrap;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .nav-pill-item:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .nav-pill-item.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Content Container */
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Content Card */
        .content-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-number {
            display: inline-flex;
            width: 40px;
            height: 40px;
            background: var(--gradient-security);
            color: white;
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .section-content {
            padding-left: 55px;
        }

        .section-content h4 {
            font-family: 'Kanit', sans-serif;
            color: var(--primary);
            margin: 25px 0 15px;
            font-size: 1.2rem;
        }

        .section-content p {
            color: var(--dark);
            margin-bottom: 15px;
            text-align: justify;
        }

        .section-content ul {
            margin: 20px 0;
            padding-left: 25px;
        }

        .section-content li {
            margin-bottom: 12px;
            color: var(--dark);
            position: relative;
        }

        .section-content ul li::before {
            content: '✓';
            position: absolute;
            left: -25px;
            color: var(--success);
            font-weight: bold;
        }

        /* Security Features Grid */
        .security-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .security-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(6, 182, 212, 0.05) 100%);
            padding: 30px;
            border-radius: 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        .security-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
            border-color: var(--primary);
        }

        .security-card .icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
        }

        .security-card h4 {
            font-family: 'Kanit', sans-serif;
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .security-card p {
            color: var(--gray);
            font-size: 0.95rem;
            margin: 0;
        }

        /* Info Boxes */
        .info-box, .warning-box, .success-box {
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
            border-left: 4px solid;
        }

        .info-box {
            background: #DBEAFE;
            border-color: #3B82F6;
        }

        .info-box strong {
            color: #1E40AF;
        }

        .warning-box {
            background: #FEF3C7;
            border-color: #F59E0B;
        }

        .warning-box h4 {
            color: #92400E;
            margin-bottom: 15px;
        }

        .success-box {
            background: #D1FAE5;
            border-color: #10B981;
        }

        .success-box h4 {
            color: #065F46;
            margin-bottom: 15px;
        }

        /* Alert Boxes */
        .alert-custom {
            padding: 20px;
            border-radius: 12px;
            margin: 25px 0;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .alert-custom-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .alert-info {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .alert-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        .alert-success {
            background: #D1FAE5;
            color: #065F46;
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background: var(--light);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-security);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin: 0 auto 15px;
        }

        .stat-value {
            font-family: 'Kanit', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.95rem;
        }

        /* Contact Section */
        .contact-section {
            background: var(--gradient-security);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: white;
            margin: 40px 0;
        }

        .contact-section h3 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .contact-section p {
            margin-bottom: 10px;
            opacity: 0.95;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .contact-item {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
        }

        .contact-item i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        /* Button Group */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .btn-download, .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: var(--primary);
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: 'Sarabun', sans-serif;
        }

        .btn-download:hover, .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: var(--primary);
        }

        /* Footer Navigation */
        .footer-nav {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin: 40px 0;
        }

        .footer-nav h4 {
            font-family: 'Kanit', sans-serif;
            text-align: center;
            margin-bottom: 25px;
            color: var(--dark);
        }

        .footer-nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .footer-nav-item {
            padding: 15px;
            background: var(--light);
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s ease;
        }

        .footer-nav-item:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        .footer-nav-item i {
            display: block;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .footer-nav-item span {
            font-weight: 500;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gradient-security);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .policy-title {
                font-size: 1.8rem;
            }
            
            .content-card {
                padding: 25px;
            }
            
            .section-content {
                padding-left: 0;
            }
            
            .security-features {
                grid-template-columns: 1fr;
            }

            .nav-pills-custom {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- Policy Header -->
    <div class="policy-header">
        <div class="container">
            <div class="policy-header-content">
                <div class="policy-icon-box" data-aos="zoom-in">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="policy-title" data-aos="fade-up" data-aos-delay="100">
                    นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์
                </h1>
                <p class="policy-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>
                </p>
                <div class="policy-meta" data-aos="fade-up" data-aos-delay="300">
                    <div class="policy-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>ปรับปรุงล่าสุด: 1 มกราคม 2568</span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-tag"></i>
                        <span>เวอร์ชัน 2.0</span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>อ่าน 15 นาที</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Pills -->
    <div class="container">
        <div class="nav-pills-container" data-aos="fade-up">
            <div class="nav-pills-custom">
                <a href="#section1" class="nav-pill-item active">บทนำ</a>
                <a href="#section2" class="nav-pill-item">มาตรฐานความปลอดภัย</a>
                <a href="#section3" class="nav-pill-item">การบำรุงรักษา</a>
                <a href="#section4" class="nav-pill-item">การเก็บ Log ระบบ</a>
                <a href="#section5" class="nav-pill-item">คำแนะนำผู้ใช้</a>
                <a href="#section6" class="nav-pill-item">รายงานเหตุการณ์</a>
                <a href="#section7" class="nav-pill-item">กฎหมายและมาตรฐาน</a>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="content-container">
        
        <!-- Section 1: บทนำ -->
        <div id="section1" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">1</span>
                <h2 class="section-title">บทนำและความสำคัญ</h2>
            </div>
            <div class="section-content">
                <p><?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?> ให้ความสำคัญสูงสุดกับการรักษาความมั่นคงปลอดภัยทางไซเบอร์ (Cybersecurity) เพื่อสร้างความเชื่อมั่นให้กับประชาชนผู้ใช้บริการเว็บไซต์และระบบ e-Service ของเรา โดยปฏิบัติตามพระราชบัญญัติการรักษาความมั่นคงปลอดภัยไซเบอร์ พ.ศ. 2562 </p>

                <div class="stats-grid">
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-value">24/7</div>
                        <div class="stat-label">เฝ้าระวังระบบ</div>
                    </div>
                    
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="stat-value">HTTPS/SSL</div>
                        <div class="stat-label">การเข้ารหัส</div>
                    </div>
                    
                    <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="stat-value">Diary</div>
                        <div class="stat-label">Backup Strategy</div>
                    </div>
                    
                   <!-- <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value">ISO</div>
                        <div class="stat-label">27001 Certified</div>
                    </div> -->
                </div>
            </div>
        </div>

        <!-- Section 2: มาตรฐานความปลอดภัย -->
        <div id="section2" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">2</span>
                <h2 class="section-title">มาตรฐานความปลอดภัยระดับสูง</h2>
            </div>
            <div class="section-content">
                <div class="security-features">
                    <div class="security-card" data-aos="fade-up" data-aos-delay="100">
                        <span class="icon">🛡️</span>
                        <h4>Web Application Firewall</h4>
                        <p>ระบบป้องกันการโจมตีเว็บแอปพลิเคชันขั้นสูง ตรวจจับและป้องกันภัยคุกคามแบบ Real-time ตลอด 24 ชั่วโมง</p>
                    </div>
                    
                    <div class="security-card" data-aos="fade-up" data-aos-delay="200">
                        <span class="icon">🔐</span>
                        <h4>HTTPS/SSL Encryption</h4>
                        <p>การเข้ารหัสข้อมูลด้วย HTTPS และใบรับรอง SSL ที่ได้มาตรฐาน เพื่อความปลอดภัยในการรับส่งข้อมูล</p>
                    </div>
					<!-- เพิ่มในส่วน Section 2: มาตรฐานความปลอดภัย หลังจาก security-features grid -->

<div class="security-card" data-aos="fade-up" data-aos-delay="400">
    <span class="icon">🔑</span>
    <h4>Two-Factor Authentication (2FA)</h4>
    <p>ระบบยืนยันตัวตนสองชั้น เพิ่มความปลอดภัยให้กับบัญชีผู้ใช้ ป้องกันการเข้าถึงโดยไม่ได้รับอนุญาต</p>
</div>
                    
                    <div class="security-card" data-aos="fade-up" data-aos-delay="300">
                        <span class="icon">🦠</span>
                        <h4>Malware Scanner</h4>
                        <p>ระบบสแกนและตรวจจับมัลแวร์อัตโนมัติ ป้องกันไฟล์อันตรายและโค้ดที่เป็นภัยต่อเว็บไซต์</p>
                    </div>
                    
                    <div class="security-card" data-aos="fade-up" data-aos-delay="500">
                        <span class="icon">🔒</span>
                        <h4>Access Control</h4>
                        <p>ระบบควบคุมการเข้าถึงด้วย Permission-based Access ป้องกันการเข้าถึงข้อมูลโดยไม่ได้รับอนุญาต</p>
                    </div>
                    
                    <div class="security-card" data-aos="fade-up" data-aos-delay="600">
                        <span class="icon">🤖</span>
                        <h4>Anti-Bot Protection</h4>
                        <p>ป้องกัน Bot และการโจมตีอัตโนมัติด้วย Google reCAPTCHA และระบบตรวจจับพฤติกรรม</p>
                    </div>
                    
                    <div class="security-card" data-aos="fade-up" data-aos-delay="700">
                        <span class="icon">📝</span>
                        <h4>Traffic Log System</h4>
                        <p>เก็บบันทึก Log การใช้งานไม่น้อยกว่า 90 วัน ตาม พ.ร.บ. คอมพิวเตอร์ พ.ศ. 2560</p>
                    </div>
                    
                    <div class="security-card" data-aos="fade-up" data-aos-delay="800">
                        <span class="icon">🚫</span>
                        <h4>Content Filtering</h4>
                        <p>ระบบกรองคำหยาบคายและเนื้อหาไม่เหมาะสม เพื่อรักษาความเหมาะสมของเว็บไซต์</p>
                    </div>
                </div>

                <!-- <h4>2.1 การป้องกัน OWASP Top 10</h4>
                <p>ระบบของเราป้องกันช่องโหว่ความปลอดภัยตามมาตรฐาน OWASP Top 10 ครบทุกประเภท:</p>
                <ul>
                    <li>SQL Injection Prevention - ป้องกันการโจมตีฐานข้อมูล</li>
                    <li>Cross-Site Scripting (XSS) Protection - ป้องกันการฝัง JavaScript อันตราย</li>
                    <li>CSRF Prevention - ป้องกันการปลอมแปลงคำร้องขอ</li>
                    <li>DDoS Mitigation - ป้องกันการโจมตีแบบ Distributed Denial of Service</li>
                    <li>Bot Management - จัดการ Bot ด้วย AI/Machine Learning</li>
                </ul> -->

                <h4>2.1 การเข้ารหัสข้อมูล</h4>
                <div class="alert-custom alert-info">
                    <i class="fas fa-info-circle alert-custom-icon"></i>
                    <div>
                        <strong>HTTPS/TLS Protection</strong><br>
                        เราใช้การเข้ารหัส SSL/TLS เพื่อความปลอดภัยสูงสุด
                    </div>
                </div>

                <h4>2.2 ระบบตรวจจับมัลแวร์</h4>
                <ul>
                    <li>Real-time Scanning - สแกนไฟล์แบบเรียลไทม์</li>
                    <li>Signature-based Detection - ฐานข้อมูลอัปเดต</li>

                </ul>
				<!-- เพิ่มหลัง h4 ระบบตรวจจับมัลแวร์ -->

<h4>2.3 ระบบยืนยันตัวตนสองชั้น (Two-Factor Authentication - 2FA)</h4>
<p>เรามีระบบ 2FA เพื่อเพิ่มความปลอดภัยให้กับบัญชีผู้ใช้:</p>
<ul>
    <li><strong>Authenticator App:</strong> รองรับการใช้งานแอปพลิเคชันยืนยันตัวตน เช่น Google Authenticator</li>
    <li><strong>Backup Codes:</strong> รหัสสำรองสำหรับกรณีฉุกเฉิน</li>
    <li><strong>Time-Based:</strong> รหัส OTP มีอายุจำกัด เพื่อความปลอดภัย</li>
</ul>

<div class="alert-custom alert-success">
    <i class="fas fa-shield-alt alert-custom-icon"></i>
    <div>
        <strong>แนะนำให้เปิดใช้งาน 2FA:</strong><br>
        การเปิดใช้งาน 2FA จะช่วยป้องกันการเข้าถึงบัญชีโดยไม่ได้รับอนุญาต แม้ว่ารหัสผ่านจะรั่วไหลก็ตาม ผู้ไม่หวังดีจะไม่สามารถเข้าสู่ระบบได้หากไม่มีรหัส OTP จากอุปกรณ์ของท่าน
    </div>
</div>
				

<h4>2.4 ระบบควบคุมการเข้าถึง (Access Control)</h4>
<p>เรามีระบบควบคุมการเข้าถึงข้อมูลและระบบอย่างเข้มงวด:</p>
<ul>
    <li><strong>Role-Based Access Control (RBAC):</strong> กำหนดสิทธิ์การเข้าถึงตามบทบาทหน้าที่</li>
    <li><strong>Permission-Based System:</strong> ควบคุมการเข้าถึงแต่ละฟังก์ชันอย่างละเอียด</li>
    <li><strong>Least Privilege Principle:</strong> ให้สิทธิ์เฉพาะที่จำเป็นต่อการทำงาน</li>
    <li><strong>Access Logging:</strong> บันทึกการเข้าถึงทุกครั้งเพื่อตรวจสอบย้อนหลัง</li>
    <li><strong>Session Management:</strong> จัดการ Session อย่างปลอดภัย มีการหมดอายุอัตโนมัติ</li>
</ul>

<h4>2.5 การป้องกัน Bot (Anti-Bot Protection)</h4>
<p>เราใช้เทคโนโลยีป้องกัน Bot และการโจมตีอัตโนมัติ:</p>
<ul>
    <li><strong>Google reCAPTCHA v3:</strong> ตรวจจับ Bot แบบไม่รบกวนผู้ใช้งาน</li>
    <li><strong>Behavioral Analysis:</strong> วิเคราะห์พฤติกรรมการใช้งานเพื่อตรวจจับ Bot</li>
    <li><strong>Rate Limiting:</strong> จำกัดจำนวนคำขอต่อช่วงเวลา ป้องกัน Brute Force</li>
    <li><strong>IP Reputation Checking:</strong> ตรวจสอบชื่อเสียงของ IP Address</li>
    <li><strong>Challenge-Response:</strong> แสดง CAPTCHA เมื่อสงสัยว่าเป็น Bot</li>
</ul>

<div class="alert-custom alert-info">
    <i class="fas fa-robot alert-custom-icon"></i>
    <div>
        <strong>การทำงานแบบอัตโนมัติ</strong><br>
        ระบบป้องกัน Bot ทำงานโดยอัตโนมัติในพื้นหลัง ไม่รบกวนประสบการณ์การใช้งานของผู้ใช้จริง
    </div>
</div>

<h4>2.6 ระบบบันทึก Traffic Log</h4>
<p>เราเก็บบันทึกการเข้าใช้งานระบบตามกฎหมาย:</p>
<ul>
    <li><strong>ระยะเวลาเก็บ:</strong> เก็บ Log ไม่น้อยกว่า 90 วัน ตาม พ.ร.บ. คอมพิวเตอร์ พ.ศ. 2560</li>
    <li><strong>ข้อมูลที่บันทึก:</strong> IP Address, Timestamp, URL, User Agent, HTTP Method</li>
    <li><strong>การเข้ารหัส:</strong> Log Files เข้ารหัสด้วย AES-256</li>
    <li><strong>การควบคุมการเข้าถึง:</strong> เฉพาะผู้มีอำนาจเท่านั้นที่เข้าถึง Log ได้</li>
    <li><strong>Audit Trail:</strong> บันทึกการเข้าถึง Log Files เพื่อตรวจสอบ</li>
</ul>

<div class="alert-custom alert-warning">
    <i class="fas fa-gavel alert-custom-icon"></i>
    <div>
        <strong>การปฏิบัติตามกฎหมาย</strong><br>
        การเก็บ Log เป็นไปตามมาตรา 26 แห่ง พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ พ.ศ. 2560 และสามารถเปิดเผยให้เจ้าหน้าที่ตามคำสั่งศาลเท่านั้น
    </div>
</div>

<h4>2.7 ระบบกรองเนื้อหา (Content Filtering)</h4>
<p>เราใช้ระบบกรองคำหยาบคายและเนื้อหาไม่เหมาะสม:</p>
<ul>
    <li><strong>Keyword Filtering:</strong> กรองคำหยาบคายและคำที่ไม่เหมาะสมโดยอัตโนมัติ</li>
    <li><strong>Pattern Matching:</strong> ตรวจจับรูปแบบการเขียนหลีกเลี่ยงการกรอง</li>
    <li><strong>Context Analysis:</strong> วิเคราะห์บริบทของข้อความเพื่อความแม่นยำ</li>
    <li><strong>Multi-Language Support:</strong> รองรับการกรองภาษาไทยและภาษาอังกฤษ</li>
    <li><strong>Real-time Filtering:</strong> กรองแบบเรียลไทม์ก่อนแสดงผลบนเว็บไซต์</li>
    <li><strong>Admin Review:</strong> ผู้ดูแลระบบสามารถตรวจสอบและอนุมัติเนื้อหาที่ถูกกรอง</li>
</ul>

<div class="alert-custom alert-success">
    <i class="fas fa-check-circle alert-custom-icon"></i>
    <div>
        <strong>สภาพแวดล้อมที่ปลอดภัย</strong><br>
        ระบบกรองเนื้อหาช่วยสร้างสภาพแวดล้อมออนไลน์ที่เหมาะสมและปลอดภัยสำหรับผู้ใช้งานทุกวัย
    </div>
</div>
            </div>
        </div>

        <!-- Section 3: การบำรุงรักษา -->
        <div id="section3" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">3</span>
                <h2 class="section-title">การจัดการและบำรุงรักษาระบบ</h2>
            </div>
            <div class="section-content">
                <h4>3.1 กลยุทธ์การสำรองข้อมูล</h4>
                <div class="info-box">
                    <p><strong>เราปฏิบัติสำรองข้อมูลทุกวัน:</strong></p>
                    <ul>
                        <li>เราสำรองข้อมูลทุกวันเพื่อให้มั่นใจได้ว่าข้อมูลไม่หาย</li>

                    </ul>
                </div>

                <h4>3.2 ตารางการบำรุงรักษา</h4>
                <ul>
                    <li>Automated Backup - สำรองอัตโนมัติทุกวัน เวลา 01:00 น.</li>
                   <!-- <li>Security Patches - ติดตั้งภายใน 24 ชั่วโมง</li>
                    <li>System Updates - อัปเดตทุกเดือน</li>
                    <li>Security Audit - ตรวจสอบทุก 6 เดือน</li>
                    <li>Penetration Testing - ทดสอบปีละ 2 ครั้ง</li>
                    <li>Disaster Recovery Test - ทดสอบทุก 3 เดือน</li>  -->
                </ul>

                <h4>3.3 ระบบเฝ้าระวัง 24/7</h4>
                <div class="alert-custom alert-success">
                    <i class="fas fa-check-circle alert-custom-icon"></i>
                    <div>
                        <strong>Monitoring & Logging</strong><br>
                        เฝ้าระวังระบบตลอด 24 ชั่วโมง พร้อมแจ้งเตือนทันทีผ่าน Email เมื่อพบเหตุการณ์ผิดปกติ บันทึก Log ครบถ้วนและเก็บไว้อย่างน้อย 90 วัน
                    </div>
                </div>
            </div>
        </div>


        <!-- Section 4: การเก็บ Log ระบบ (ใหม่) -->
        <div id="section4" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">4</span>
                <h2 class="section-title">การเก็บบันทึกข้อมูล Traffic Log</h2>
            </div>
            <div class="section-content">
                <p>เพื่อปฏิบัติตามพระราชบัญญัติว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ พ.ศ. 2560 และพระราชบัญญัติการรักษาความมั่นคงปลอดภัยไซเบอร์ พ.ศ. 2562 เราดำเนินการเก็บบันทึกข้อมูล Traffic Log ของระบบอย่างเคร่งครัด</p>

                <div class="alert-custom alert-warning">
                    <i class="fas fa-gavel alert-custom-icon"></i>
                    <div>
                        <strong>ฐานทางกฎหมาย</strong><br>
                        ตามมาตรา 26 แห่ง พ.ร.บ. คอมพิวเตอร์ พ.ศ. 2560 ผู้ให้บริการต้องเก็บข้อมูล Traffic Log ไม่น้อยกว่า 90 วัน เพื่อใช้ในการสอบสวนและดำเนินคดี
                    </div>
                </div>

                <h4>4.1 ข้อมูลที่เก็บบันทึก (Traffic Log)</h4>
                <p>เราเก็บบันทึกข้อมูลดังต่อไปนี้เพื่อวัตถุประสงค์ทางกฎหมายและความปลอดภัย:</p>

                <div class="table-responsive" style="margin: 25px 0;">
                    <table class="table table-bordered table-hover">
                        <thead style="background: var(--gradient-security); color: white;">
                            <tr>
                                <th>ประเภทข้อมูล</th>
                                <th>รายละเอียด</th>
                                <th>ระยะเวลาเก็บ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>IP Address</strong></td>
                                <td>ที่อยู่ IP ของผู้เข้าใช้งาน</td>
                                <td>90 วัน</td>
                            </tr>
                            <tr>
                                <td><strong>Timestamp</strong></td>
                                <td>วันและเวลาที่เข้าใช้งาน</td>
                                <td>90 วัน</td>
                            </tr>
                            <tr>
                                <td><strong>URL ที่เข้าถึง</strong></td>
                                <td>หน้าเว็บและ Resource ที่เข้าชม</td>
                                <td>90 วัน</td>
                            </tr>
                            <tr>
                                <td><strong>User Agent</strong></td>
                                <td>ข้อมูล Browser และ Device</td>
                                <td>90 วัน</td>
                            </tr>
                            <tr>
                                <td><strong>HTTP Method</strong></td>
                                <td>รูปแบบคำขอ (GET, POST, etc.)</td>
                                <td>90 วัน</td>
                            </tr>
                            <tr>
                                <td><strong>Response Code</strong></td>
                                <td>สถานะการตอบกลับของระบบ</td>
                                <td>90 วัน</td>
                            </tr>
                            <tr>
                                <td><strong>Referrer URL</strong></td>
                                <td>แหล่งที่มาก่อนเข้าสู่เว็บไซต์</td>
                                <td>90 วัน</td>
                            </tr>
                            <tr>
                                <td><strong>Session ID</strong></td>
                                <td>รหัส Session ของผู้ใช้ (ถ้ามี)</td>
                                <td>90 วัน</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h4>4.2 Log การเข้าสู่ระบบ (Login Log)</h4>
                <p>สำหรับระบบที่มีการ Authentication เราเก็บข้อมูลเพิ่มเติม:</p>
                <ul>
                    <li><strong>Username/User ID:</strong> รหัสผู้ใช้ที่พยายามเข้าสู่ระบบ</li>
                    <li><strong>Login Status:</strong> สถานะสำเร็จ/ไม่สำเร็จ</li>
                    <li><strong>Login Timestamp:</strong> วันเวลาที่เข้าสู่ระบบ</li>
                    <li><strong>Logout Timestamp:</strong> วันเวลาที่ออกจากระบบ</li>
                    <li><strong>IP Address:</strong> ที่อยู่ IP ที่ใช้ Login</li>
                    <li><strong>Failed Login Attempts:</strong> จำนวนครั้งที่พยายาม Login ไม่สำเร็จ</li>
                    <li><strong>Account Actions:</strong> การเปลี่ยนแปลงข้อมูลบัญชี เช่น เปลี่ยนรหัสผ่าน</li>
                </ul>

                <div class="alert-custom alert-info">
                    <i class="fas fa-shield-alt alert-custom-icon"></i>
                    <div>
                        <strong>การรักษาความปลอดภัยของ Log</strong><br>
                        Log Files ทั้งหมดได้รับการเข้ารหัสและจัดเก็บอย่างปลอดภัย มีการควบคุมการเข้าถึงอย่างเคร่งครัด เฉพาะผู้มีอำนาจเท่านั้นที่สามารถเข้าถึงได้
                    </div>
                </div>

                <h4>4.3 วัตถุประสงค์ในการเก็บ Log</h4>
                <div class="security-features">
                    <div class="security-card">
                        <span class="icon">⚖️</span>
                        <h4>ปฏิบัติตามกฎหมาย</h4>
                        <p>เพื่อให้เป็นไปตามพระราชบัญญัติคอมพิวเตอร์และกฎหมายที่เกี่ยวข้อง</p>
                    </div>

                    <div class="security-card">
                        <span class="icon">🔍</span>
                        <h4>การสอบสวน</h4>
                        <p>ช่วยในการสอบสวนเมื่อเกิดเหตุการณ์ความปลอดภัย หรือการใช้งานที่ผิดกฎหมาย</p>
                    </div>

                    <div class="security-card">
                        <span class="icon">📊</span>
                        <h4>วิเคราะห์และป้องกัน</h4>
                        <p>วิเคราะห์รูปแบบการโจมตี เพื่อเสริมสร้างมาตรการป้องกันที่ดีขึ้น</p>
                    </div>

                    <div class="security-card">
                        <span class="icon">🛡️</span>
                        <h4>ตรวจจับภัยคุกคาม</h4>
                        <p>ตรวจจับพฤติกรรมผิดปกติและการพยายามเข้าถึงโดยไม่ได้รับอนุญาต</p>
                    </div>
                </div>

                <h4>4.4 การเปิดเผยข้อมูล Log</h4>
                <p>ข้อมูล Traffic Log จะถูกเก็บเป็นความลับและจะเปิดเผยเฉพาะในกรณีดังต่อไปนี้:</p>
                <ul>
                    <li><strong>ตามคำสั่งศาล:</strong> เมื่อได้รับหมายศาลหรือคำสั่งจากหน่วยงานที่มีอำนาจตามกฎหมาย</li>
                    <li><strong>การสอบสวนคดีอาญา:</strong> เมื่อเจ้าหน้าที่ตำรวจหรือพนักงานสอบสวนร้องขอตามกฎหมาย</li>
                    <li><strong>ภัยคุกคามด้านความมั่นคง:</strong> เมื่อจำเป็นต่อความมั่นคงของชาติตามที่กฎหมายกำหนด</li>
                    <li><strong>การป้องกันอาชญากรรม:</strong> เพื่อป้องกันหรือหยุดยั้งการกระทำความผิดที่กำลังจะเกิดขึ้น</li>
                </ul>

                <div class="alert-custom alert-danger">
                    <i class="fas fa-exclamation-triangle alert-custom-icon"></i>
                    <div>
                        <strong>คำเตือนสำหรับผู้ใช้งาน</strong><br>
                        การใช้งานเว็บไซต์และบริการของเราต้องเป็นไปตามกฎหมายเท่านั้น การพยายามโจมตีระบบ ทำลายข้อมูล หรือกระทำการใดๆ ที่ผิดกฎหมาย จะถูกบันทึกและดำเนินคดีตามกฎหมาย
                    </div>
                </div>

                <h4>4.5 การจัดการและทำลาย Log</h4>
                <ul>
                    <li><strong>ระยะเวลาเก็บรักษา:</strong> Log Files จะถูกเก็บไว้ไม่น้อยกว่า 90 วัน ตามที่กฎหมายกำหนด</li>
                    <li><strong>การเข้ารหัส:</strong> Log Files ได้รับการเข้ารหัสด้วยมาตรฐาน AES-256</li>
                    <li><strong>Backup Log:</strong> มีการสำรอง Log Files เพื่อป้องกันการสูญหาย</li>
                    <li><strong>Audit Trail:</strong> มีการบันทึกการเข้าถึง Log Files เพื่อตรวจสอบย้อนหลัง</li>
                </ul>

                   <!-- <div class="alert-custom alert-success">
                    <i class="fas fa-check-circle alert-custom-icon"></i>
                    <div>
                        <strong>มาตรฐานการจัดการ Log</strong><br>
                        การเก็บและจัดการ Log ของเราเป็นไปตามมาตรฐาน ISO/IEC 27001:2013 ข้อกำหนด A.12.4 (Logging and Monitoring) และ NIST Cybersecurity Framework
                    </div>
                </div> -->
            </div>
        </div>
        <!-- Section 4: คำแนะนำผู้ใช้ -->
        <div id="section5" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">5</span>
                <h2 class="section-title">คำแนะนำความปลอดภัยสำหรับผู้ใช้งาน</h2>
            </div>
            <div class="section-content">
                <h4>5.1 การสร้างรหัสผ่านที่แข็งแรง</h4>
                <div class="info-box">
                    <p><strong>รหัสผ่านที่ดีควรมีลักษณะ:</strong></p>
                    <ul>
                        <li>ความยาวอย่างน้อย 8 ตัวอักษร </li>
                        <li>ผสม ตัวพิมพ์ใหญ่-เล็ก + ตัวเลข + อักขระพิเศษ</li>
                        <li>ไม่ใช้ข้อมูลส่วนตัวที่เดาง่าย เช่น วันเกิด ชื่อ</li>
                        <li>ไม่ใช้รหัสเดิมซ้ำกับเว็บไซต์อื่น</li>
                        <li>เปลี่ยนรหัสผ่านทุก 90 วัน</li>
                        <li>เปิดใช้งาน Two-Factor Authentication (2FA)</li>
                    </ul>
                </div>

                <h4>5.2 ระวังภัย Phishing และ Social Engineering</h4>
                <div class="warning-box">
                    <h4>⚠️ สัญญาณเตือนอีเมล Phishing</h4>
                    <ul>
                        <li>อีเมลผู้ส่งที่ดูน่าสงสัย ไม่ใช่โดเมนอย่างเป็นทางการ</li>
                        <li>มีความเร่งด่วนให้คลิกลิงก์หรือกรอกข้อมูล</li>
                        <li>มีการสะกดผิด หรือใช้ภาษาแปลกๆ</li>
                        <li>ขอข้อมูลส่วนตัวหรือรหัสผ่าน</li>
                        <li>ลิงก์ที่ดูแปลกหรือไม่ตรงกับชื่อองค์กร</li>
                    </ul>
                </div>

                <h4>5.3 การใช้งานอย่างปลอดภัย</h4>
                <ul>
                    <li>ตรวจสอบแม่กุญแจ 🔒 และ https:// ก่อนกรอกข้อมูล</li>
                    <li>Logout ทันทีเมื่อใช้งานเสร็จ โดยเฉพาะคอมพิวเตอร์สาธารณะ</li>
                    <li>ไม่บันทึกรหัสผ่านในเบราว์เซอร์สาธารณะ</li>
                    <li>ใช้ VPN เมื่อเชื่อมต่อ Wi-Fi สาธารณะ</li>
                    <li>อัปเดต Browser และระบบปฏิบัติการให้เป็นเวอร์ชันล่าสุด</li>
                    <li>เปิดใช้งาน Automatic Security Updates</li>
                    <li>สำรองข้อมูลสำคัญอย่างสม่ำเสมอ</li>
                </ul>

                <div class="alert-custom alert-warning">
                    <i class="fas fa-exclamation-triangle alert-custom-icon"></i>
                    <div>
                        <strong>คำเตือน!</strong><br>
                        หน่วยงานของเราจะไม่มีวันขอรหัสผ่านหรือข้อมูลส่วนตัวผ่านอีเมล โทรศัพท์ หรือช่องทางอื่นใด หากได้รับการติดต่อแบบนี้ กรุณาติดต่อกลับผ่านช่องทางอย่างเป็นทางการ
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: รายงานเหตุการณ์ -->
        <div id="section6" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">6</span>
                <h2 class="section-title">การรายงานเหตุการณ์ด้านความปลอดภัย</h2>
            </div>
            <div class="section-content">
                <p>หากท่านพบเห็นกิจกรรมที่น่าสงสัย ช่องโหว่ด้านความปลอดภัย หรือการละเมิดความปลอดภัย กรุณาแจ้งให้เราทราบทันทีผ่านช่องทางดังนี้:</p>

                <div class="contact-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">
                    <div class="alert-custom alert-info" style="margin: 0;">
                        <i class="fas fa-envelope alert-custom-icon"></i>
                        <div>
                            <strong>อีเมล ฉุกเฉิน</strong><br>
                            <?php echo $org['email_1']; ?> และ support@assystem.co.th
                        </div>
                    </div>
                    
                    <div class="alert-custom alert-info" style="margin: 0;">
                        <i class="fas fa-phone alert-custom-icon"></i>
                        <div>
                            <strong>โทรศัพท์</strong><br>
                            <?php echo $org['phone_1']; ?><br>
                            <small>(ในเวลาราชการ)</small>
                        </div>
                    </div>
                    
                    <div class="alert-custom alert-info" style="margin: 0;">
                        <i class="fab fa-line alert-custom-icon"></i>
                        <div>
                            <strong>Line Official</strong><br>
                            @assystem<br>
                            <small>(ตอบรับภายใน 24 ชม.)</small>
                        </div>
                    </div>
                </div>

                <div class="success-box">
                    <h4>✅ Responsible Disclosure Program</h4>
                    <p>เรายินดีรับฟังข้อเสนอแนะและรายงานช่องโหว่จากผู้เชี่ยวชาญด้านความปลอดภัย หากท่านพบช่องโหว่และรายงานตามหลัก Responsible Disclosure เราจะดำเนินการแก้ไขโดยเร่งด่วน</p>
                </div>

                <h4>ขั้นตอนการรายงาน</h4>
                <ul>
                    <li>แจ้งรายละเอียดของช่องโหว่หรือเหตุการณ์ที่พบ</li>
                    <li>ระบุขั้นตอนการทำซ้ำ (Reproduction Steps) ถ้ามี</li>
                    <li>แนบหลักฐาน Screenshot หรือ Log ถ้าเป็นไปได้</li>
                    <li>ทีมงานจะตอบกลับภายใน 24-48 ชั่วโมง</li>
                    <li>เราจะแก้ไขปัญหาและแจ้งผลการดำเนินการ</li>
                </ul>
            </div>
        </div>

        <!-- Section 6: กฎหมายและมาตรฐาน -->
        <div id="section7" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">7</span>
                <h2 class="section-title">การปฏิบัติตามกฎหมายและมาตรฐาน</h2>
            </div>
            <div class="section-content">
                <p>เราปฏิบัติตามกฎหมายและมาตรฐานด้านความปลอดภัยทางไซเบอร์อย่างเคร่งครัด:</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 30px 0;">
                    <div class="security-card">
                        <span class="icon">⚖️</span>
                        <h4>กฎหมายไทย</h4>
                        <ul style="text-align: left; padding-left: 20px; margin-top: 15px;">
                            <li>พ.ร.บ. ความมั่นคงปลอดภัยไซเบอร์ 2562</li>
                            <li>พ.ร.บ. คอมพิวเตอร์ 2560</li>
                            <li>พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล 2562</li>
                        </ul>
                    </div>
                    
                    <!-- <div class="security-card">
                        <span class="icon">🏆</span>
                        <h4>มาตรฐานสากล</h4>
                        <ul style="text-align: left; padding-left: 20px; margin-top: 15px;">
                            <li>ISO/IEC 27001:2013</li>
                            <li>ISO/IEC 27701:2019</li>
                            <li>NIST Cybersecurity Framework</li>
                        </ul>
                    </div>
                    
                    <div class="security-card">
                        <span class="icon">🔍</span>
                        <h4>Best Practices</h4>
                        <ul style="text-align: left; padding-left: 20px; margin-top: 15px;">
                            <li>OWASP Top 10 Guidelines</li>
                            <li>CIS Controls</li>
                            <li>SANS Security Policies</li>
                        </ul>
                    </div> -->
                </div>

                <div class="alert-custom alert-success">
                    <i class="fas fa-certificate alert-custom-icon"></i>
                    <div>
                        <strong>การตรวจสอบและรับรอง</strong><br>
                        เว็บไซต์ของเราได้รับการตรวจสอบอยู่เสม่ำเสมอ และมีการทบทวนนโยบายความปลอดภัยอย่างสม่ำเสมอเพื่อให้ทันต่อภัยคุกคามใหม่ๆ
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="contact-section" data-aos="zoom-in">
            <h3>ติดต่อเจ้าหน้าที่ความปลอดภัยสารสนเทศ</h3>
            <p><strong><?php echo $org['fname']; ?></strong></p>
            <p>📍 <?php echo isset($org['address']) ? $org['address'] : ''; ?>
                        <?php if(isset($org['subdistric'])): ?> ต.<?php echo $org['subdistric']; ?><?php endif; ?>
                        <?php if(isset($org['district'])): ?> อ.<?php echo $org['district']; ?><?php endif; ?>
                        <?php if(isset($org['province'])): ?> จ.<?php echo $org['province']; ?><?php endif; ?>
                        <?php if(isset($org['zip_code'])): ?> <?php echo $org['zip_code']; ?><?php endif; ?></p>
            <p>📞 <?php echo isset($org['phone_1']) ? $org['phone_1'] : ''; ?>
                        <?php if(!empty($org['phone_2'])): ?> , <?php echo $org['phone_2']; ?><?php endif; ?> | ✉️ <?php echo isset($org['email_1']) ? $org['email_1'] : ''; ?> และ support@assystem.co.th</p>
            
            
        </div>

        <!-- Footer Navigation -->
        <div class="footer-nav" data-aos="fade-up">
            <h4>นโยบายที่เกี่ยวข้อง</h4>
            <div class="footer-nav-grid">
                <a href="#" class="footer-nav-item">
                    <i class="fas fa-file-contract"></i>
                    <span>ข้อกำหนดการใช้งาน</span>
                </a>
                <a href="#" class="footer-nav-item">
                    <i class="fas fa-user-shield"></i>
                    <span>คุ้มครองข้อมูล PDPA</span>
                </a>
                <a href="#" class="footer-nav-item">
                    <i class="fas fa-user-lock"></i>
                    <span>ความเป็นส่วนตัว</span>
                </a>
                <a href="#" class="footer-nav-item">
                    <i class="fas fa-cookie-bite"></i>
                    <span>นโยบายคุกกี้</span>
                </a>
                <a href="#" class="footer-nav-item">
                    <i class="fas fa-users"></i>
                    <span>สมาชิก</span>
                </a>
                <a href="#" class="footer-nav-item">
                    <i class="fas fa-home"></i>
                    <span>กลับหน้าหลัก</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Back to Top -->
    <div class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });

        // Back to Top
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Smooth Scroll for Navigation
        document.querySelectorAll('.nav-pill-item').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all
                document.querySelectorAll('.nav-pill-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active to clicked
                this.classList.add('active');
                
                // Smooth scroll
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Active nav on scroll
        window.addEventListener('scroll', function() {
            let current = '';
            const sections = document.querySelectorAll('.content-card');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.nav-pill-item').forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href').slice(1) === current) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>