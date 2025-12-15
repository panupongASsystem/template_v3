<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อกำหนดและเงื่อนไขการสมัครสมาชิก - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?></title>
    
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
            --gradient-membership: linear-gradient(135deg, #667eea 0%, #f093fb 100%);
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
            background: var(--gradient-membership);
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
        }

        /* Content Container */
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Services Grid */
        .services-intro {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .services-intro h2 {
            font-family: 'Kanit', sans-serif;
            color: var(--dark);
            font-size: 1.8rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .services-intro h2 i {
            color: var(--primary);
            font-size: 2rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-1);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 28px;
            color: white;
        }

        .icon-tax { background: linear-gradient(135deg, #667eea, #764ba2); }
        .icon-booking { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .icon-complaint { background: linear-gradient(135deg, #fa709a, #fee140); }
        .icon-appointment { background: linear-gradient(135deg, #30cfd0, #330867); }
        .icon-feedback { background: linear-gradient(135deg, #a8edea, #fed6e3); color: var(--dark) !important; }
        .icon-allowance { background: linear-gradient(135deg, #ff9a9e, #fecfef); }
        .icon-subsidy { background: linear-gradient(135deg, #ffecd2, #fcb69f); }
        .icon-document { background: linear-gradient(135deg, #f6d365, #fda085); }
        .icon-corruption { background: linear-gradient(135deg, #5ee7df, #b490ca); }
        .icon-survey { background: linear-gradient(135deg, #d299c2, #fef9d7); color: var(--dark) !important; }

        .service-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .service-subtitle {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 0;
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
            background: var(--gradient-membership);
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

        .section-content h3 {
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

        /* FAQ Section */
        .faq-section {
            margin-top: 40px;
        }

        .faq-section h2 {
            font-family: 'Kanit', sans-serif;
            color: var(--dark);
            font-size: 1.8rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .faq-section h2 i {
            color: var(--primary);
        }

        .faq-item {
            background: var(--light);
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--dark);
            transition: all 0.3s;
        }

        .faq-question:hover {
            background: #E2E8F0;
        }

        .faq-question i {
            transition: transform 0.3s;
            color: var(--primary);
        }

        .faq-item.active .faq-question {
            background: var(--primary);
            color: white;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
            color: white;
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s;
            color: var(--gray);
        }

        .faq-item.active .faq-answer {
            padding: 0 20px 20px;
            max-height: 500px;
        }

        /* Action Buttons */
        .action-section {
            background: var(--gradient-membership);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: white;
            margin: 40px 0;
        }

        .action-section h3 {
            font-family: 'Kanit', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .action-section p {
            margin-bottom: 25px;
            opacity: 0.95;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-custom {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: 'Sarabun', sans-serif;
        }

        .btn-primary-custom {
            background: white;
            color: var(--primary);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: var(--primary);
        }

        .btn-secondary-custom {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary-custom:hover {
            background: white;
            color: var(--primary);
        }

        /* Contact Box */
        .contact-box {
            background: var(--light);
            padding: 30px;
            border-radius: 15px;
            margin: 25px 0;
        }

        .contact-box h4 {
            font-family: 'Kanit', sans-serif;
            color: var(--dark);
            margin-bottom: 20px;
        }

        .contact-box p {
            margin-bottom: 10px;
            color: var(--gray);
        }

        .contact-box i {
            color: var(--primary);
            margin-right: 10px;
            width: 20px;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gradient-membership);
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

        /* Footer */
        .policy-footer {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin: 40px 0;
        }

        .policy-footer p {
            color: var(--gray);
            margin: 5px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .policy-title {
                font-size: 1.8rem;
            }
            
            .content-card, .services-intro {
                padding: 25px;
            }
            
            .section-content {
                padding-left: 0;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-custom {
                width: 100%;
                justify-content: center;
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
                    <i class="fas fa-users"></i>
                </div>
                <h1 class="policy-title" data-aos="fade-up" data-aos-delay="100">
                    ข้อกำหนดและเงื่อนไขการสมัครสมาชิก
                </h1>
                <p class="policy-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo isset($org['fname']) ? $org['fname'] : ''; ?>
                </p>
                <div class="policy-meta" data-aos="fade-up" data-aos-delay="300">
                    <div class="policy-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>ปรับปรุงล่าสุด: 1 มกราคม 2568</span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-tag"></i>
                        <span>เวอร์ชัน 1.5</span>
                    </div>
                    <div class="policy-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>อ่าน 12 นาที</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Pills -->
    <div class="container">
        <div class="nav-pills-container" data-aos="fade-up">
            <div class="nav-pills-custom">
                <a href="#section1" class="nav-pill-item active">ข้อกำหนดทั่วไป</a>
                <a href="#section2" class="nav-pill-item">ขั้นตอนสมัคร</a>
                <a href="#section3" class="nav-pill-item">เงื่อนไขแต่ละระบบ</a>
                <a href="#section4" class="nav-pill-item">จัดการบัญชี</a>
                <a href="#section5" class="nav-pill-item">ความเป็นส่วนตัว</a>
                <a href="#faq" class="nav-pill-item">คำถามที่พบบ่อย</a>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="content-container">
        
        <!-- Services Introduction -->
        <div class="services-intro" data-aos="fade-up">
            <h2><i class="fas fa-laptop-code"></i> ระบบบริการ e-Service สำหรับประชาชน</h2>
            <p>เลือกเข้าใช้งานระบบบริการ e-Service ได้ตามที่ต้องการ สะดวกและรวดเร็ว พร้อมให้บริการ 10 ระบบหลักดังนี้:</p>
        </div>

        <!-- Services Grid -->
        <div class="services-grid">
            <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                <div class="service-icon icon-tax">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="service-title">TAX SYSTEM</div>
                <div class="service-subtitle">ระบบจ่ายภาษี</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="150">
                <div class="service-icon icon-booking">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="service-title">BOOKING SYSTEM</div>
                <div class="service-subtitle">ระบบจองคิวรถ</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                <div class="service-icon icon-complaint">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="service-title">COMPLAINT SYSTEM</div>
                <div class="service-subtitle">แจ้งเรื่อง ร้องเรียน</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="250">
                <div class="service-icon icon-appointment">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="service-title">APPOINTMENT SYSTEM</div>
                <div class="service-subtitle">จองคิวติดต่อราชการ</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                <div class="service-icon icon-feedback">
                    <i class="fas fa-star"></i>
                </div>
                <div class="service-title">FEEDBACK</div>
                <div class="service-subtitle">รับฟังความคิดเห็น</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="350">
                <div class="service-icon icon-allowance">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="service-title">ALLOWANCE</div>
                <div class="service-subtitle">เบี้ยยังชีพผู้สูงอายุ/พิการ</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                <div class="service-icon icon-subsidy">
                    <i class="fas fa-baby"></i>
                </div>
                <div class="service-title">SUBSIDY</div>
                <div class="service-subtitle">เงินอุดหนุนเด็กแรกเกิด</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="450">
                <div class="service-icon icon-document">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div class="service-title">DOCUMENT</div>
                <div class="service-subtitle">ยื่นเอกสารออนไลน์</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="500">
                <div class="service-icon icon-corruption">
                    <i class="fas fa-user-secret"></i>
                </div>
                <div class="service-title">ANTI-CORRUPTION</div>
                <div class="service-subtitle">แจ้งเรื่องการทุจริต</div>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="550">
                <div class="service-icon icon-survey">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="service-title">SURVEY</div>
                <div class="service-subtitle">แบบสอบถามประเมินความพึงพอใจ</div>
            </div>
        </div>

        <!-- Section 1: General Terms -->
        <div id="section1" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">1</span>
                <h2 class="section-title">ข้อกำหนดทั่วไป</h2>
            </div>
            <div class="section-content">
                <h3>1.1 การยอมรับข้อกำหนด</h3>
                <p>การสมัครสมาชิกและใช้งานระบบ e-Service ของ <?php echo isset($org['fname']) ? $org['fname'] : ''; ?> ถือว่าท่านได้อ่าน เข้าใจ และยอมรับที่จะปฏิบัติตามข้อกำหนดและเงื่อนไขการใช้งานนี้ทุกประการ หากท่านไม่ยอมรับข้อกำหนดดังกล่าว กรุณาหยุดใช้งานระบบทันที</p>

                <h3>1.2 คุณสมบัติผู้สมัครสมาชิก</h3>
                <ul>
                    <li>ประชาชนทั่วไป ต้องมีอายุไม่ต่ำกว่า 13 ปีบริบูรณ์</li>
                    <li>ต้องมีที่อยู่ในพื้นที่รับผิดชอบของ <?php echo isset($org['fname']) ? $org['fname'] : ''; ?> หรือมีกิจการที่เกี่ยวข้อง</li>
                    <li>มีข้อมูลส่วนตัวที่เป็นจริงและสามารถติดต่อได้</li>
                </ul>

                <div class="alert-custom alert-info">
                    <i class="fas fa-info-circle alert-custom-icon"></i>
                    <div>
                        <strong>หมายเหตุ:</strong> การสมัครสมาชิกไม่มีค่าใช้จ่ายใดๆ ทั้งสิ้น และสามารถยกเลิกการเป็นสมาชิกได้ทุกเมื่อ
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Registration Process -->
        <div id="section2" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">2</span>
                <h2 class="section-title">ขั้นตอนการสมัครสมาชิก</h2>
            </div>
            <div class="section-content">
                <h3>2.1 ข้อมูลที่ต้องใช้ในการสมัคร</h3>
                <ul>
                    <li>ชื่อ-นามสกุล (ภาษาไทยและภาษาอังกฤษ)</li>
                    <li>หมายเลขบัตรประชาชน (ไม่บังคับ จำเป็นในบางระบบเท่านั้น)</li>
                    <li>ที่อยู่ปัจจุบันที่สามารถติดต่อได้</li>
                    <li>หมายเลขโทรศัพท์มือถือ</li>
                    <li>อีเมล (Email) ที่ใช้งานได้จริง</li>
                    <li>รูปถ่ายหน้าตรง (ไม่บังคับ)</li>
                </ul>

                <h3>2.2 การยืนยันตัวตน</h3>
                <p>ระบบจะส่งรหัส OTP (One-Time Password) ไปยังอีเมลที่ท่านลงทะเบียน เพื่อยืนยันตัวตนก่อนเปิดใช้งานบัญชี ท่านต้องยืนยันภายใน 24 ชั่วโมง มิฉะนั้นข้อมูลการสมัครจะถูกยกเลิกอัตโนมัติ</p>

                <div class="alert-custom alert-warning">
                    <i class="fas fa-exclamation-triangle alert-custom-icon"></i>
                    <div>
                        <strong>คำเตือน:</strong> การให้ข้อมูลเท็จในการสมัครสมาชิกถือเป็นความผิดตามกฎหมาย และอาจถูกดำเนินคดีตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Service Usage Terms -->
        <div id="section3" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">3</span>
                <h2 class="section-title">เงื่อนไขการใช้งานแต่ละระบบ</h2>
            </div>
            <div class="section-content">
                <h3>3.1 ระบบจ่ายภาษี (TAX SYSTEM)</h3>
                <ul>
                    <li>ต้องเป็นเจ้าของทรัพย์สินหรือผู้ที่ได้รับมอบอำนาจ</li>
                    <li>การชำระเงินผ่านระบบจะมีค่าธรรมเนียมธนาคารตามอัตราที่ธนาคารกำหนด</li>
                    <li>ใบเสร็จรับเงินอิเล็กทรอนิกส์มีผลสมบูรณ์ตามกฎหมาย</li>
                    <li>ระบบจะเก็บประวัติการชำระย้อนหลังได้ไม่น้อยกว่า 1 ปี</li>
                </ul>

                <h3>3.2 ระบบจองคิวรถ (BOOKING SYSTEM)</h3>
                <ul>
                    <li>จองได้ล่วงหน้าสูงสุด 7 วัน</li>
                    <li>ต้องยืนยันการจองภายใน 1 ชั่วโมงหลังจองคิว</li>
                    <li>สามารถยกเลิกได้ก่อนเวลานัดหมาย 12 ชั่วโมง</li>
                    <li>หากไม่มาตามนัดโดยไม่แจ้งยกเลิก 3 ครั้ง จะถูกระงับสิทธิ์การจอง 30 วัน</li>
                </ul>

                <h3>3.3 ระบบร้องเรียน (COMPLAINT SYSTEM)</h3>
                <ul>
                    <li>ต้องระบุข้อเท็จจริงที่ชัดเจนและสามารถตรวจสอบได้</li>
                    <li>ห้ามใช้ถ้อยคำหยาบคาย ดูหมิ่น หรือกล่าวหาโดยปราศจากหลักฐาน</li>
                    <li>ข้อมูลผู้ร้องเรียนจะถูกเก็บเป็นความลับตามกฎหมาย</li>
                    <li>จะได้รับการตอบกลับเบื้องต้นภายใน 24 ชั่วโมงในวันทำการ</li>
                </ul>

                <h3>3.4 ระบบจองคิวติดต่อราชการ (APPOINTMENT)</h3>
                <ul>
                    <li>จองได้เฉพาะในวันและเวลาทำการเท่านั้น</li>
                    <li>ต้องมาก่อนเวลานัดหมาย 15 นาที</li>
                    <li>หากมาสายเกิน 30 นาที คิวจะถูกยกเลิกอัตโนมัติ</li>
                    <li>สามารถเลื่อนนัดได้ 2 ครั้งต่อการจอง 1 ครั้ง</li>
                </ul>

                <h3>3.5 ระบบเบี้ยยังชีพ (ALLOWANCE)</h3>
                <ul>
                    <li>ผู้สมัครต้องมีคุณสมบัติตามที่กฎหมายกำหนด</li>
                    <li>เอกสารที่อัปโหลดต้องเป็นไฟล์ PDF หรือรูปภาพที่ชัดเจน ขนาดไม่เกิน 5 MB</li>
                    <li>การให้ข้อมูลเท็จถือเป็นความผิดและต้องคืนเงินที่ได้รับไป</li>
                    <li>ต้องปรับปรุงข้อมูลทุกปี มิฉะนั้นจะถูกระงับการจ่ายเงิน</li>
                </ul>

                <h3>3.6 ระบบเงินอุดหนุนเด็กแรกเกิด (SUBSIDY)</h3>
                <ul>
                    <li>ต้องลงทะเบียนภายใน 30 วันหลังคลอด</li>
                    <li>ต้องมีสูติบัตรของเด็กและเอกสารประกอบครบถ้วน</li>
                    <li>การรับเงินต้องผ่านบัญชีธนาคารของผู้ปกครองเท่านั้น</li>
                    <li>ต้องรายงานสถานะเด็กทุก 6 เดือน</li>
                </ul>

                <h3>3.7 ระบบยื่นเอกสารออนไลน์ (DOCUMENT)</h3>
                <ul>
                    <li>รองรับไฟล์นามสกุล .pdf, .jpg, .png, .doc, .docx เท่านั้น</li>
                    <li>ขนาดไฟล์รวมต้องไม่เกิน 20 MB ต่อคำร้อง</li>
                    <li>เอกสารต้องอ่านได้ชัดเจนและครบถ้วน</li>
                    <li>จะได้รับหมายเลขอ้างอิงเพื่อติดตามสถานะ</li>
                </ul>

                <h3>3.8 ระบบแจ้งเรื่องทุจริต (ANTI-CORRUPTION)</h3>
                <ul>
                    <li>สามารถแจ้งแบบระบุตัวตนหรือไม่ระบุตัวตนก็ได้</li>
                    <li>ข้อมูลผู้แจ้งจะถูกเข้ารหัสและเก็บเป็นความลับสูงสุด</li>
                    <li>ต้องมีข้อมูลหรือหลักฐานประกอบที่น่าเชื่อถือ</li>
                    <li>ห้ามแจ้งข้อมูลเท็จ มีโทษตามกฎหมาย</li>
                    <li>ได้รับการคุ้มครองตาม พ.ร.บ. การอำนวยความสะดวก</li>
                </ul>

                <div class="alert-custom alert-info">
                    <i class="fas fa-info-circle alert-custom-icon"></i>
                    <div>
                        <strong>บริการอื่นๆ:</strong> สำหรับระบบบริการใหม่ที่จะเปิดให้บริการในอนาคต เช่น E-Learning, E-Market จะมีข้อกำหนดและเงื่อนไขเพิ่มเติมที่จะแจ้งให้ทราบ
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Account Management -->
        <div id="section4" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">4</span>
                <h2 class="section-title">การจัดการบัญชีผู้ใช้</h2>
            </div>
            <div class="section-content">
                <h3>4.1 ความปลอดภัยของบัญชี</h3>
                <ul>
                    <li>ท่านต้องรักษารหัสผ่านให้เป็นความลับ ไม่เปิดเผยแก่ผู้อื่น</li>
                    <li>รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษ</li>
                    <li>ควรเปลี่ยนรหัสผ่านทุก 90 วัน</li>
                    <li>เปิดใช้งาน 2FA เพื่อเพิ่มความปลอดภัยให้กับบัญชี</li>
                    <li>หากพบการใช้งานผิดปกติ ให้แจ้งเจ้าหน้าที่ทันที</li>
                </ul>

                <h3>4.2 การระงับและยกเลิกบัญชี</h3>
                <p>องค์การฯ ขอสงวนสิทธิ์ในการระงับหรือยกเลิกบัญชีผู้ใช้ในกรณีดังต่อไปนี้:</p>
                <ul>
                    <li>ให้ข้อมูลเท็จในการสมัครสมาชิก</li>
                    <li>ใช้งานระบบในทางที่ผิดกฎหมายหรือขัดต่อศีลธรรม</li>
                    <li>พยายามเข้าถึงข้อมูลของผู้อื่นโดยไม่ได้รับอนุญาต</li>
                    <li>ทำให้ระบบเสียหายหรือหยุดชะงัก</li>
                    <li>ไม่ใช้งานเป็นเวลานานกว่า 365 วัน</li>
                    <li>ละเมิดข้อกำหนดและเงื่อนไขการใช้งาน</li>
                </ul>

                <div class="alert-custom alert-warning">
                    <i class="fas fa-exclamation-triangle alert-custom-icon"></i>
                    <div>
                        <strong>การอุทธรณ์:</strong> หากบัญชีถูกระงับ ท่านสามารถอุทธรณ์ได้ภายใน 30 วัน โดยติดต่อที่ <?php echo isset($org['email_1']) ? $org['email_1'] : ''; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Privacy and Data -->
        <div id="section5" class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">5</span>
                <h2 class="section-title">ความเป็นส่วนตัวและการคุ้มครองข้อมูล</h2>
            </div>
            <div class="section-content">
                <h3>5.1 การเก็บรวบรวมข้อมูล</h3>
                <p>องค์การฯ จะเก็บรวบรวมข้อมูลส่วนบุคคลเฉพาะที่จำเป็นต่อการให้บริการเท่านั้น และจะดำเนินการตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 อย่างเคร่งครัด</p>

                <h3>5.2 การใช้ข้อมูล</h3>
                <ul>
                    <li>เพื่อการให้บริการตามที่ท่านร้องขอ</li>
                    <li>เพื่อการยืนยันตัวตนและตรวจสอบสิทธิ์</li>
                    <li>เพื่อการติดต่อสื่อสารเกี่ยวกับบริการ</li>
                    <li>เพื่อการปรับปรุงและพัฒนาการให้บริการ</li>
                    <li>เพื่อการปฏิบัติตามกฎหมายที่เกี่ยวข้อง</li>
                </ul>

                <h3>5.3 การเปิดเผยข้อมูล</h3>
                <p>องค์การฯ จะไม่เปิดเผยข้อมูลส่วนบุคคลของท่านแก่บุคคลที่สาม เว้นแต่:</p>
                <ul>
                    <li>ได้รับความยินยอมจากท่าน</li>
                    <li>เป็นการปฏิบัติตามกฎหมายหรือคำสั่งศาล</li>
                    <li>เพื่อป้องกันอันตรายต่อชีวิต ร่างกาย หรือสุขภาพของบุคคล</li>
                    <li>เป็นการดำเนินการตามภารกิจเพื่อประโยชน์สาธารณะ</li>
                </ul>

                <div class="alert-custom alert-success">
                    <i class="fas fa-check-circle alert-custom-icon"></i>
                    <div>
                        <strong>สิทธิของท่าน:</strong> ท่านมีสิทธิขอเข้าถึง แก้ไข ลบ หรือโอนย้ายข้อมูลส่วนบุคคลของท่าน รวมถึงการคัดค้านการประมวลผลข้อมูล ตามที่กฎหมายกำหนด
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div id="faq" class="content-card" data-aos="fade-up">
            <div class="faq-section">
                <h2><i class="fas fa-question-circle"></i> คำถามที่พบบ่อย</h2>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        การสมัครสมาชิกมีค่าใช้จ่ายหรือไม่?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        การสมัครสมาชิกและใช้งานระบบ e-Service ไม่มีค่าใช้จ่ายใดๆ ทั้งสิ้น แต่การทำธุรกรรมบางอย่าง เช่น การชำระภาษีออนไลน์ อาจมีค่าธรรมเนียมธนาคารตามที่แต่ละธนาคารกำหนด
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        ลืมรหัสผ่านทำอย่างไร?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        ท่านสามารถกดปุ่ม "ลืมรหัสผ่าน" ที่หน้าเข้าสู่ระบบ แล้วกรอกอีเมลที่ลงทะเบียนไว้ ระบบจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ให้ท่าน หากยังมีปัญหา กรุณาติดต่อเจ้าหน้าที่
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        ข้อมูลส่วนบุคคลของฉันปลอดภัยหรือไม่?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        องค์การฯ ให้ความสำคัญสูงสุดกับความปลอดภัยของข้อมูล โดยใช้ระบบเข้ารหัส SSL/TLS และปฏิบัติตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 อย่างเคร่งครัด ข้อมูลของท่านจะถูกใช้เฉพาะเพื่อการให้บริการเท่านั้น
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        สามารถใช้งานระบบผ่านมือถือได้หรือไม่?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        ได้ ระบบ e-Service ของเราออกแบบมาให้รองรับการใช้งานผ่านอุปกรณ์ทุกประเภท ไม่ว่าจะเป็นคอมพิวเตอร์ แท็บเล็ต หรือสมาร์ทโฟน โดยจะปรับการแสดงผลให้เหมาะสมกับขนาดหน้าจออัตโนมัติ
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        จะยกเลิกการเป็นสมาชิกได้อย่างไร?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        ท่านสามารถยกเลิกการเป็นสมาชิกได้โดยการเข้าไปที่เมนู "ตั้งค่าบัญชี" แล้วเลือก "ยกเลิกบัญชี" หรือติดต่อเจ้าหน้าที่เพื่อขอยกเลิก ข้อมูลของท่านจะถูกลบออกจากระบบภายใน 30 วัน
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Section 
        <div class="action-section" data-aos="zoom-in">
            <h3>พร้อมเริ่มใช้งานแล้วหรือยัง?</h3>
            <p>สมัครสมาชิกง่ายๆ ใช้เวลาไม่ถึง 5 นาที เพื่อเข้าถึงบริการ e-Service ทั้งหมด</p>
            
            <div class="button-group">
                <button onclick="acceptTerms()" class="btn-custom btn-primary-custom">
                    <i class="fas fa-check-circle"></i>
                    ยอมรับข้อกำหนดและสมัครสมาชิก
                </button>
                <button onclick="window.print()" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-print"></i>
                    พิมพ์เอกสาร
                </button>
            </div>
        </div> -->

        <!-- Contact Information -->
        <div class="content-card" data-aos="fade-up">
            <div class="section-header">
                <span class="section-number">
                    <i class="fas fa-phone-alt"></i>
                </span>
                <h2 class="section-title">ติดต่อสอบถาม</h2>
            </div>
            <div class="section-content">
                <p>หากท่านมีข้อสงสัยหรือต้องการสอบถามเพิ่มเติมเกี่ยวกับข้อกำหนดและเงื่อนไข สามารถติดต่อได้ที่:</p>
                
                <div class="contact-box">
                    <h4><?php echo isset($org['fname']) ? $org['fname'] : ''; ?></h4>
                    <p><i class="fas fa-map-marker-alt"></i><?php echo isset($org['address']) ? $org['address'] : ''; ?>
                        <?php if(isset($org['subdistric'])): ?> ต.<?php echo $org['subdistric']; ?><?php endif; ?>
                        <?php if(isset($org['district'])): ?> อ.<?php echo $org['district']; ?><?php endif; ?>
                        <?php if(isset($org['province'])): ?> จ.<?php echo $org['province']; ?><?php endif; ?>
                        <?php if(isset($org['zip_code'])): ?> <?php echo $org['zip_code']; ?><?php endif; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo isset($org['phone_1']) ? $org['phone_1'] : ''; ?>
                        <?php if(!empty($org['phone_2'])): ?> , <?php echo $org['phone_2']; ?><?php endif; ?></p>
                    <p><i class="fas fa-envelope"></i> อีเมล: <?php echo isset($org['email_1']) ? $org['email_1'] : ''; ?></p>
                    <p><i class="fas fa-clock"></i> เวลาทำการ: จันทร์-ศุกร์ เวลา 08:30-16:30 น. (ยกเว้นวันหยุดราชการ)</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="policy-footer" data-aos="fade-up">
            <p><strong>© 2568 <?php echo isset($org['fname']) ? $org['fname'] : ''; ?> - สงวนลิขสิทธิ์</strong></p>
            <p>ข้อกำหนดและเงื่อนไขนี้มีผลบังคับใช้ตั้งแต่วันที่ 1 มกราคม 2568 เป็นต้นไป</p>
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

        // Toggle FAQ
        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            const allFaqItems = document.querySelectorAll('.faq-item');
            
            // Close all other items
            allFaqItems.forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current item
            faqItem.classList.toggle('active');
        }

        // Accept Terms
        function acceptTerms() {
            if (confirm('ท่านยืนยันที่จะยอมรับข้อกำหนดและเงื่อนไขการใช้งานนี้ใช่หรือไม่?')) {
                alert('บันทึกการยอมรับเรียบร้อยแล้ว กำลังไปหน้าสมัครสมาชิก...');
                // Redirect to register page
                // window.location.href = 'register.html';
            }
        }

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
                if (item.getAttribute('href').slice(1) === current) {
                    item.classList.add('active');
                }
            });
        });

        // Print styles
        const printStyles = `
            @media print {
                .nav-pills-container, .button-group, .back-to-top { 
                    display: none !important; 
                }
                .service-card, .content-card { 
                    page-break-inside: avoid; 
                }
                .policy-header {
                    padding: 40px 0;
                }
            }
        `;
        const style = document.createElement('style');
        style.textContent = printStyles;
        document.head.appendChild(style);
    </script>
</body>
</html>
                   