<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นโยบายเว็บไซต์และข้อกำหนดการใช้งาน - <?php echo $org['fname']; ?></title>
    
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
            background: var(--gradient-1);
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
            max-width: 1000px;
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
            background: var(--gradient-1);
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

        .section-content p {
            color: var(--dark);
            margin-bottom: 15px;
            text-align: justify;
        }

        .section-content ul, .section-content ol {
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

        /* Highlight Box */
        .highlight-box {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-left: 4px solid var(--primary);
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
        }

        .highlight-box-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .highlight-box-title i {
            font-size: 1.2rem;
        }

        /* Info Cards Grid */
        .info-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .info-card-item {
            background: var(--light);
            padding: 25px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .info-card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .info-card-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .info-card-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .info-card-desc {
            color: var(--gray);
            font-size: 0.95rem;
        }

        /* Table Styles */
        .custom-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin: 25px 0;
        }

        .custom-table thead {
            background: var(--gradient-1);
            color: white;
        }

        .custom-table th {
            padding: 15px;
            font-weight: 600;
            text-align: left;
        }

        .custom-table td {
            padding: 15px;
            border-bottom: 1px solid var(--light);
        }

        .custom-table tbody tr:hover {
            background: var(--light);
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

        /* Accordion Custom */
        .accordion-custom {
            margin: 30px 0;
        }

        .accordion-custom .accordion-item {
            border: none;
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .accordion-custom .accordion-button {
            background: white;
            color: var(--dark);
            font-weight: 600;
            padding: 20px;
            border: none;
            box-shadow: none;
        }

        .accordion-custom .accordion-button:not(.collapsed) {
            background: var(--gradient-1);
            color: white;
        }

        .accordion-custom .accordion-button::after {
            filter: none;
        }

        .accordion-custom .accordion-button:not(.collapsed)::after {
            filter: brightness(0) invert(1);
        }

        .accordion-custom .accordion-body {
            padding: 25px;
            background: var(--light);
        }

        /* Download Section */
        .download-section {
            background: var(--gradient-1);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: white;
            margin: 40px 0;
        }

        .download-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .download-desc {
            margin-bottom: 25px;
            opacity: 0.95;
        }

        .btn-download {
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
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: var(--primary);
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gradient-1);
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

        /* Footer Navigation */
        .footer-nav {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin: 40px 0;
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
            
            .info-cards-grid {
                grid-template-columns: 1fr;
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
                    <i class="fas fa-file-contract"></i>
                </div>
                <h1 class="policy-title" data-aos="fade-up" data-aos-delay="100">
                    นโยบายเว็บไซต์และข้อกำหนดการใช้งาน
                </h1>
                <p class="policy-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo $org['fname']; ?>
                </p>
                <div class="policy-meta" data-aos="fade-up" data-aos-delay="300">
                    <div class="policy-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>ปรับปรุงล่าสุด: 1 มกราคม 2568 </span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-tag"></i>
                        <span>เวอร์ชัน 2.0</span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>อ่าน 10 นาที</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Pills -->
    <div class="container">
        <div class="nav-pills-container" data-aos="fade-up">
            <div class="nav-pills-custom">
                <a href="#section1" class="nav-pill-item active">วัตถุประสงค์</a>
                <a href="#section2" class="nav-pill-item">ขอบเขตการให้บริการ</a>
                <a href="#section3" class="nav-pill-item">ข้อกำหนดการใช้งาน</a>
                <a href="#section4" class="nav-pill-item">สิทธิและหน้าที่</a>
                <a href="#section5" class="nav-pill-item">ลิขสิทธิ์</a>
                <a href="#section6" class="nav-pill-item">ข้อจำกัดความรับผิดชอบ</a>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="content-container">
        
        <!-- Section 1 -->
        <div id="section1" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">1</span>
                <h2 class="section-title">บทนำและวัตถุประสงค์</h2>
            </div>
            <div class="section-content">
                <p>เว็บไซต์ของ<?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?> จัดทำขึ้นภายใต้นโยบายรัฐบาลดิจิทัลเพื่อเศรษฐกิจและสังคม (Digital Economy and Society) และแผนพัฒนารัฐบาลดิจิทัลของประเทศไทย พ.ศ. 2566-2570 โดยมีวัตถุประสงค์หลักดังนี้:</p>
                
                <div class="info-cards-grid">
                    <div class="info-card-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="info-card-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="info-card-title">ศูนย์กลางข้อมูลข่าวสาร</div>
                        <div class="info-card-desc">เผยแพร่ข้อมูลข่าวสาร ประกาศ และกิจกรรมต่างๆ ให้ประชาชนรับทราบอย่างทั่วถึง</div>
                    </div>
                    
                    <div class="info-card-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="info-card-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="info-card-title">ระบบ e-Service</div>
                        <div class="info-card-desc">ให้บริการออนไลน์แบบครบวงจร ลดขั้นตอน ลดระยะเวลาในการติดต่อราชการ</div>
                    </div>
                    
                    <div class="info-card-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="info-card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="info-card-title">การมีส่วนร่วม</div>
                        <div class="info-card-desc">ส่งเสริมการมีส่วนร่วมของประชาชนในการพัฒนาท้องถิ่นผ่านช่องทางออนไลน์</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2 -->
        <div id="section2" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">2</span>
                <h2 class="section-title">ขอบเขตการให้บริการ</h2>
            </div>
            <div class="section-content">
                <div class="accordion accordion-custom" id="serviceAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#service1">
                                <i class="fas fa-newspaper me-2"></i> บริการข้อมูลข่าวสาร
                            </button>
                        </h2>
                        <div id="service1" class="accordion-collapse collapse show" data-bs-parent="#serviceAccordion">
                            <div class="accordion-body">
                                <ul>
                                    <li>ข่าวประชาสัมพันธ์และกิจกรรมขององค์กร</li>
                                    <li>แผนพัฒนาท้องถิ่น แผนดำเนินงาน และแผนงบประมาณประจำปี</li>
                                    <li>รายงานผลการดำเนินงานและการใช้จ่ายงบประมาณ</li>
                                    <li>ประกาศจัดซื้อจัดจ้างตาม พ.ร.บ. การจัดซื้อจัดจ้างและการบริหารพัสดุภาครัฐ พ.ศ. 2560</li>
                                    <li>กฎหมาย ระเบียบ ข้อบังคับ และคู่มือการปฏิบัติงาน</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#service2">
                                <i class="fas fa-laptop-code me-2"></i> บริการออนไลน์ (e-Service)
                            </button>
                        </h2>
                        <div id="service2" class="accordion-collapse collapse" data-bs-parent="#serviceAccordion">
                            <div class="accordion-body">
                                <ul>
                                    <li>ระบบยื่นคำร้องออนไลน์ (Online Request System)</li>
                                    <li>ระบบจองคิวออนไลน์ (Queue Booking System)</li>
                                    <li>ระบบชำระภาษีและค่าธรรมเนียมออนไลน์ (e-Payment)</li>
                                    <li>ระบบติดตามสถานะคำร้อง (Tracking System)</li>
                                    <li>ระบบแจ้งเรื่องร้องเรียน/ร้องทุกข์ (e-Complaint)</li>
                                    <li>ระบบประเมินความพึงพอใจ (e-Satisfaction)</li>
									<li>ระบบเบี้ยยังชีพ (ALLOWANCE)</li>
									<li>ระบบเงินอุดหนุนเด็กแรกเกิด (SUBSIDY)</li>
									<li>ระบบแจ้งเรื่องทุจริต (ANTI-CORRUPTION)</li>
								
									
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3 -->
        <div id="section3" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">3</span>
                <h2 class="section-title">ข้อกำหนดและเงื่อนไขการใช้งาน</h2>
            </div>
            <div class="section-content">
                <div class="highlight-box">
                    <div class="highlight-box-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        การยอมรับข้อกำหนด
                    </div>
                    <p>การเข้าใช้งานเว็บไซต์นี้ถือว่าผู้ใช้งานได้อ่าน เข้าใจ และยอมรับที่จะปฏิบัติตามข้อกำหนดและเงื่อนไขการใช้งานที่ระบุไว้ทั้งหมด หากไม่ยอมรับ ขอให้ยุติการใช้งานเว็บไซต์นี้ทันที</p>
                </div>

                <h4>คุณสมบัติของผู้ใช้งาน</h4>
                <ul>
                    <li>ผู้ใช้งานทั่วไปสามารถเข้าชมและใช้บริการพื้นฐานได้โดยไม่ต้องลงทะเบียน</li>
                    <li>การใช้บริการ e-Service ต้องลงทะเบียนเป็นสมาชิกและยืนยันตัวตน</li>
                    <li>ผู้สมัครสมาชิกต้องมีอายุไม่ต่ำกว่า 13 ปีบริบูรณ์</li>
                    <li>ข้อมูลที่ให้ในการลงทะเบียนต้องเป็นความจริงและเป็นปัจจุบัน</li>
                </ul>

                <div class="alert-custom alert-warning">
                    <i class="fas fa-exclamation-circle alert-custom-icon"></i>
                    <div>
                        <strong>การใช้งานที่ผิดกฎหมาย</strong><br>
                        ผู้ใช้งานต้องใช้เว็บไซต์เพื่อวัตถุประสงค์ที่ถูกต้องตามกฎหมายเท่านั้น ห้ามใช้ในลักษณะที่ผิดกฎหมาย ส่งไวรัส หรือพยายามเข้าถึงระบบโดยไม่ได้รับอนุญาต
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4 -->
        <div id="section4" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">4</span>
                <h2 class="section-title">สิทธิและหน้าที่ของผู้ใช้งาน</h2>
            </div>
            <div class="section-content">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="highlight-box" style="background: #D1FAE515; border-color: var(--success);">
                            <div class="highlight-box-title" style="color: var(--success);">
                                <i class="fas fa-check-circle"></i>
                                สิทธิของผู้ใช้งาน
                            </div>
                            <ul>
                                <li>เข้าถึงข้อมูลข่าวสารสาธารณะ</li>
                                <li>ใช้บริการ e-Service ตามสิทธิ</li>
                                <li>ร้องเรียนหรือแจ้งปัญหา</li>
                                <li>ขอแก้ไขข้อมูลส่วนบุคคล</li>
                                <li>ขอยกเลิกการเป็นสมาชิก</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="highlight-box" style="background: #FEE2E215; border-color: var(--danger);">
                            <div class="highlight-box-title" style="color: var(--danger);">
                                <i class="fas fa-tasks"></i>
                                หน้าที่ของผู้ใช้งาน
                            </div>
                            <ul>
                                <li>รักษาความลับของรหัสผ่าน</li>
                                <li>แจ้งเจ้าหน้าที่หากพบการใช้งานผิดปกติ</li>
                                <li>ให้ข้อมูลที่เป็นความจริง</li>
                                <li>ปฏิบัติตามกฎหมายและระเบียบ</li>
                                <li>รับผิดชอบการกระทำของตนเอง</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5 -->
        <div id="section5" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">5</span>
                <h2 class="section-title">ลิขสิทธิ์และทรัพย์สินทางปัญญา</h2>
            </div>
            <div class="section-content">
                <p>เนื้อหา ข้อมูล รูปภาพ บนเว็บไซต์นี้เป็นลิขสิทธิ์ของ<?php echo $org['fname']; ?>หรือเจ้าของลิขสิทธิ์ที่เกี่ยวข้อง</p>
                
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>การใช้งาน</th>
                            <th>อนุญาต</th>
                            <th>เงื่อนไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ใช้เพื่อการศึกษา</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td>ต้องอ้างอิงแหล่งที่มา</td>
                        </tr>
                        <tr>
                            <td>ใช้ส่วนตัว</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td>ไม่หาผลประโยชน์</td>
                        </tr>
                        <tr>
                            <td>ใช้เชิงพาณิชย์</td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td>ต้องขออนุญาตเป็นลายลักษณ์อักษร</td>
                        </tr>
                        <tr>
                            <td>แชร์ลิงก์</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td>ไม่บิดเบือนข้อมูล</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 6 -->
        <div id="section6" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">6</span>
                <h2 class="section-title">ข้อจำกัดความรับผิดชอบ</h2>
            </div>
            <div class="section-content">
                <div class="alert-custom alert-info">
                    <i class="fas fa-info-circle alert-custom-icon"></i>
                    <div>
                        องค์กรปกครองส่วนท้องถิ่นพยายามอย่างดีที่สุดในการนำเสนอข้อมูลที่ถูกต้องและเป็นปัจจุบัน แต่ไม่รับประกันความถูกต้อง ครบถ้วน หรือความเหมาะสมของข้อมูลสำหรับวัตถุประสงค์ใดๆ ผู้ใช้งานควรตรวจสอบข้อมูลก่อนนำไปใช้
                    </div>
                </div>
                
                <h4>การปรับปรุงข้อกำหนด</h4>
                <p><?php echo isset($org['fname']) ? $org['fname'] : ''; ?> ขอสงวนสิทธิ์ในการแก้ไข เพิ่มเติม หรือเปลี่ยนแปลงข้อกำหนดการใช้งานได้ตลอดเวลา โดยจะประกาศให้ทราบผ่านเว็บไซต์นี้</p>
            </div>
        </div>

       

        <!-- Footer Navigation -->
        <div class="footer-nav" data-aos="fade-up">
            <h4 class="text-center mb-4">นโยบายที่เกี่ยวข้อง</h4>
            <div class="footer-nav-grid">
                <a href="<?php echo site_url('policy/security'); ?>" class="footer-nav-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>ความมั่นคงปลอดภัย</span>
                </a>
                <a href="<?php echo site_url('policy/pdpa'); ?>" class="footer-nav-item">
                    <i class="fas fa-user-shield"></i>
                    <span>คุ้มครองข้อมูล PDPA</span>
                </a>
                <a href="<?php echo site_url('policy/privacy'); ?>" class="footer-nav-item">
                    <i class="fas fa-user-lock"></i>
                    <span>ความเป็นส่วนตัว</span>
                </a>
                <a href="<?php echo site_url('policy/cookie'); ?>" class="footer-nav-item">
                    <i class="fas fa-cookie-bite"></i>
                    <span>นโยบายคุกกี้</span>
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
