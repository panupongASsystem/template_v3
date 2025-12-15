<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นโยบายและข้อกำหนด - <?php echo $org['fname']; ?></title>
    
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
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-4: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
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
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 450px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 60px 20px;
        }

        .hero-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: white;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .hero-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .hero-title {
            font-family: 'Kanit', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.95;
            font-weight: 300;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 50px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        /* Breadcrumb */
        .breadcrumb-section {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .breadcrumb {
            margin-bottom: 0;
            background: transparent;
        }

        .breadcrumb-item {
            font-size: 0.95rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-size: 1.2rem;
            color: var(--gray);
        }

        /* Policy Cards */
        .policy-section {
            padding: 60px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title {
            font-family: 'Kanit', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--gray);
        }

        .policy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .policy-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            cursor: pointer;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .policy-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .policy-card-header {
            padding: 30px 30px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }

        .policy-card:nth-child(2) .policy-card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .policy-card:nth-child(3) .policy-card-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .policy-card:nth-child(4) .policy-card-header {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .policy-card:nth-child(5) .policy-card-header {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .policy-card:nth-child(6) .policy-card-header {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        }

        .policy-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .policy-card-title {
            color: white;
            font-family: 'Kanit', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .policy-card-body {
            padding: 25px 30px 30px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .policy-description {
            color: var(--gray);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 25px;
            flex-grow: 1;
        }

        .policy-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .policy-features li {
            padding: 8px 0;
            color: var(--gray);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .policy-features li i {
            color: var(--success);
            margin-right: 10px;
            font-size: 0.9rem;
        }

        .policy-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 12px 24px;
            background: var(--light);
            border-radius: 12px;
            align-self: flex-start;
        }

        .policy-link:hover {
            background: var(--primary);
            color: white;
            transform: translateX(5px);
        }

        .policy-link i {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .policy-link:hover i {
            transform: translateX(5px);
        }

        /* Badge */
        .policy-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Info Section */
        .info-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .info-content {
            position: relative;
            z-index: 2;
        }

        .info-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        .info-title {
            font-family: 'Kanit', sans-serif;
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 25px;
            text-align: center;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .info-item {
            text-align: center;
            padding: 20px;
        }

        .info-icon {
            width: 70px;
            height: 70px;
            background: var(--gradient-1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: white;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 5px;
        }

        .info-value {
            font-family: 'Kanit', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        /* Contact Section */
        .contact-section {
            padding: 60px 0;
            background: white;
        }

        .contact-card {
            background: var(--light);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
        }

        .contact-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 30px;
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .contact-icon {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .contact-text {
            text-align: left;
        }

        .contact-label {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 2px;
        }

        .contact-value {
            font-weight: 600;
            color: var(--dark);
        }

        .btn-group-center {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn-modern {
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary-modern {
            background: var(--gradient-1);
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-outline-modern {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-modern:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 40px 0 20px;
            text-align: center;
        }

        .footer-content {
            margin-bottom: 20px;
        }

        .footer-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 15px;
            padding: 10px;
        }

        .footer-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .footer-text {
            color: rgba(255,255,255,0.7);
            margin-bottom: 10px;
        }

        .footer-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 30px 0 20px;
        }

        .footer-copyright {
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .policy-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .contact-info {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating Animation */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-pattern"></div>
        <div class="hero-content">
            <div class="hero-logo floating" data-aos="zoom-in">
                <img src="<?php echo base_url('docs/logo.png'); ?>" alt="Logo" style="max-width: 150px; max-height: 150px; width: auto; height: auto; object-fit: contain;"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect width=\'100\' height=\'100\' rx=\'20\' fill=\'%23667eea\'/%3E%3Ctext x=\'50\' y=\'50\' text-anchor=\'middle\' dy=\'.3em\' fill=\'white\' font-size=\'40\' font-family=\'Arial\'%3E🏛️%3C/text%3E%3C/svg%3E';">
            </div>
            <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100">นโยบายและข้อกำหนด</h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200"><?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบลสว่าง'; ?></p>
            <div class="hero-badge" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-shield-alt me-2"></i>
                ปรับปรุงล่าสุด: <?php echo date('d/m/') . (date('Y') + 543); ?>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">หน้าแรก</a></li>
                    <li class="breadcrumb-item active">นโยบายและข้อกำหนด</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Policy Cards Section -->
    <section class="policy-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">นโยบายที่เกี่ยวข้อง</h2>
                <p class="section-subtitle">เลือกดูนโยบายและข้อกำหนดต่างๆ ที่เกี่ยวข้องกับการใช้งานเว็บไซต์</p>
            </div>

            <div class="policy-grid">
                <!-- Policy Card 1 -->
                <div class="policy-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="policy-card-header">
                        <span class="policy-badge">จำเป็น</span>
                        <div class="policy-icon">
                            <i class="fas fa-file-contract" style="color: #667eea;"></i>
                        </div>
                        <h3 class="policy-card-title">นโยบายเว็บไซต์และข้อกำหนดการใช้งาน</h3>
                    </div>
                    <div class="policy-card-body">
                        <p class="policy-description">
                            ข้อกำหนด เงื่อนไข และวัตถุประสงค์ในการให้บริการเว็บไซต์ สิทธิและหน้าที่ของผู้ใช้งาน
                        </p>
                        <ul class="policy-features">
                            <li><i class="fas fa-check-circle"></i> ขอบเขตการให้บริการ</li>
                            <li><i class="fas fa-check-circle"></i> สิทธิและหน้าที่ผู้ใช้งาน</li>
                            <li><i class="fas fa-check-circle"></i> ข้อจำกัดความรับผิดชอบ</li>
                        </ul>
                        <a href="<?php echo site_url('policy/terms'); ?>" class="policy-link">
                            อ่านรายละเอียด <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Policy Card 2 -->
                <div class="policy-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="policy-card-header">
                        <span class="policy-badge">สำคัญ</span>
                        <div class="policy-icon">
                            <i class="fas fa-shield-alt" style="color: #f093fb;"></i>
                        </div>
                        <h3 class="policy-card-title">นโยบายความมั่นคงปลอดภัยเว็บไซต์</h3>
                    </div>
                    <div class="policy-card-body">
                        <p class="policy-description">
                            มาตรการรักษาความปลอดภัย Web Application Firewall และการป้องกันภัยคุกคามทางไซเบอร์
                        </p>
                        <ul class="policy-features">
                            <li><i class="fas fa-check-circle"></i> Web Application Firewall</li>
                            <li><i class="fas fa-check-circle"></i> การเข้ารหัส HTTPS/SSL</li>
                            <li><i class="fas fa-check-circle"></i> Malware Scanner</li>
                        </ul>
                        <a href="<?php echo site_url('policy/security'); ?>" class="policy-link">
                            อ่านรายละเอียด <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Policy Card 3 -->
                <div class="policy-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="policy-card-header">
                        <span class="policy-badge">กฎหมาย</span>
                        <div class="policy-icon">
                            <i class="fas fa-user-shield" style="color: #4facfe;"></i>
                        </div>
                        <h3 class="policy-card-title">นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)</h3>
                    </div>
                    <div class="policy-card-body">
                        <p class="policy-description">
                            การเก็บ ใช้ เปิดเผย และคุ้มครองข้อมูลส่วนบุคคลตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล
                        </p>
                        <ul class="policy-features">
                            <li><i class="fas fa-check-circle"></i> สิทธิของเจ้าของข้อมูล</li>
                            <li><i class="fas fa-check-circle"></i> ฐานทางกฎหมาย</li>
                            <li><i class="fas fa-check-circle"></i> มาตรการคุ้มครอง</li>
                        </ul>
                        <a href="<?php echo site_url('policy/pdpa'); ?>" class="policy-link">
                            อ่านรายละเอียด <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Policy Card 4 -->
                <div class="policy-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="policy-card-header">
                        <span class="policy-badge">แจ้งให้ทราบ</span>
                        <div class="policy-icon">
                            <i class="fas fa-user-lock" style="color: #43e97b;"></i>
                        </div>
                        <h3 class="policy-card-title">ประกาศความเป็นส่วนตัว</h3>
                    </div>
                    <div class="policy-card-body">
                        <p class="policy-description">
                            รายละเอียดการจัดการข้อมูลส่วนบุคคล วิธีการเก็บรวบรวม ใช้ และเปิดเผยข้อมูล
                        </p>
                        <ul class="policy-features">
                            <li><i class="fas fa-check-circle"></i> ประเภทข้อมูลที่เก็บ</li>
                            <li><i class="fas fa-check-circle"></i> วัตถุประสงค์การใช้</li>
                            <li><i class="fas fa-check-circle"></i> การแชร์ข้อมูล</li>
                        </ul>
                        <a href="<?php echo site_url('policy/privacy'); ?>" class="policy-link">
                            อ่านรายละเอียด <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Policy Card 5 -->
                <div class="policy-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="policy-card-header">
                        <span class="policy-badge">เทคโนโลยี</span>
                        <div class="policy-icon">
                            <i class="fas fa-cookie-bite" style="color: #fa709a;"></i>
                        </div>
                        <h3 class="policy-card-title">นโยบายการใช้คุกกี้</h3>
                    </div>
                    <div class="policy-card-body">
                        <p class="policy-description">
                            การใช้คุกกี้และเทคโนโลยีติดตามบนเว็บไซต์ เพื่อปรับปรุงประสบการณ์การใช้งาน
                        </p>
                        <ul class="policy-features">
                            <li><i class="fas fa-check-circle"></i> ประเภทคุกกี้</li>
                            <li><i class="fas fa-check-circle"></i> การจัดการคุกกี้</li>
                            <li><i class="fas fa-check-circle"></i> การยินยอม</li>
                        </ul>
                        <a href="<?php echo site_url('policy/cookie'); ?>" class="policy-link">
                            อ่านรายละเอียด <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Policy Card 6 -->
                <div class="policy-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="policy-card-header">
                        <span class="policy-badge">e-Service</span>
                        <div class="policy-icon">
                            <i class="fas fa-users" style="color: #30cfd0;"></i>
                        </div>
                        <h3 class="policy-card-title">ข้อกำหนดการสมัครสมาชิก</h3>
                    </div>
                    <div class="policy-card-body">
                        <p class="policy-description">
                            เงื่อนไขและขั้นตอนการสมัครใช้งานระบบบริการออนไลน์ e-Service
                        </p>
                        <ul class="policy-features">
                            <li><i class="fas fa-check-circle"></i> คุณสมบัติผู้สมัคร</li>
                            <li><i class="fas fa-check-circle"></i> ขั้นตอนการสมัคร</li>
                            <li><i class="fas fa-check-circle"></i> สิทธิประโยชน์</li>
                        </ul>
                        <a href="<?php echo site_url('policy/membership'); ?>" class="policy-link">
                            อ่านรายละเอียด <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <div class="info-content">
                <div class="info-card" data-aos="zoom-in">
                    <h3 class="info-title">ข้อมูลองค์กร</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="info-label">หน่วยงาน</div>
                            <div class="info-value"><?php echo $org['fname']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-label">ที่ตั้ง</div>
                            <div class="info-value">อ.<?php echo $org['district']; ?> จ.<?php echo $org['province']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-label">โทรศัพท์</div>
                            <div class="info-value"><?php echo $org['phone_1']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-label">อีเมล</div>
                            <div class="info-value"><?php echo $org['email_1']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-card" data-aos="fade-up">
                <h3 class="contact-title">ติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO)</h3>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="contact-text">
                            <div class="contact-label">ที่อยู่</div>
                            <div class="contact-value">
                                <?php echo $org['address']; ?> ต.<?php echo $org['subdistric']; ?> 
                                อ.<?php echo $org['district']; ?> จ.<?php echo $org['province']; ?> 
                                <?php echo $org['zip_code']; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-text">
                            <div class="contact-label">โทรศัพท์</div>
                            <div class="contact-value"><?php echo $org['phone_1']; ?></div>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <div class="contact-label">อีเมล</div>
                            <div class="contact-value"><?php echo $org['email_1']; ?></div>
                        </div>
                    </div>
                </div>

                <div class="btn-group-center">
                    <a href="<?php echo site_url('policy/download/all'); ?>" class="btn-modern btn-primary-modern">
                        <i class="fas fa-download"></i> ดาวน์โหลด PDF
                    </a>
                    <?php if(!empty($org['facebook'])): ?>
                    <a href="<?php echo $org['facebook']; ?>" target="_blank" class="btn-modern btn-outline-modern">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="<?php echo base_url('docs/logo.png'); ?>" alt="Logo" style="max-width: 80px; max-height: 80px; width: auto; height: auto; object-fit: contain;"
                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect width=\'100\' height=\'100\' rx=\'20\' fill=\'white\'/%3E%3Ctext x=\'50\' y=\'50\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23667eea\' font-size=\'40\' font-family=\'Arial\'%3E🏛️%3C/text%3E%3C/svg%3E';">
                </div>
                <p class="footer-text"><?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบลสว่าง'; ?></p>
                <p class="footer-text">
                    <?php if(isset($org['district'])): ?>อำเภอ<?php echo $org['district']; ?><?php endif; ?>
                    <?php if(isset($org['province'])): ?> จังหวัด<?php echo $org['province']; ?><?php endif; ?>
                </p>
            </div>
            <div class="footer-divider"></div>
            <p class="footer-copyright">
                © <?php echo date('Y') + 543; ?> สงวนลิขสิทธิ์ | พัฒนาโดย AS System
            </p>
        </div>
    </footer>

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

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
