<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA) - <?php echo $org['fname']; ?></title>
    
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
            
            --gradient-pdpa: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-2: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: #F8FAFC;
            color: var(--dark);
            line-height: 1.7;
        }

        /* Header */
        .policy-header {
            background: var(--gradient-pdpa);
            padding: 80px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .policy-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23F8FAFC' fill-opacity='1' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
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
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
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

        /* Content Container */
        .content-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-pdpa);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-content h4 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .stat-content p {
            color: var(--gray);
            margin: 0;
            font-size: 0.9rem;
        }

        /* Content Card */
        .content-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gradient-pdpa);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        /* Rights Grid */
        .rights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .right-card {
            background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);
            border: 2px solid rgba(79, 172, 254, 0.2);
            border-radius: 16px;
            padding: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .right-card:hover {
            transform: translateY(-5px);
            border-color: #4facfe;
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.2);
        }

        .right-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-pdpa);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .right-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .right-desc {
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin: 25px 0;
        }

        .data-table thead {
            background: var(--gradient-pdpa);
            color: white;
        }

        .data-table th {
            padding: 15px;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid var(--light);
        }

        .data-table tbody tr:hover {
            background: var(--light);
        }

        .badge-required {
            background: var(--danger);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .badge-optional {
            background: var(--warning);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* Process Timeline */
        .process-timeline {
            position: relative;
            padding: 20px 0;
        }

        .process-timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--gradient-pdpa);
            transform: translateX(-50%);
        }

        .process-item {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            position: relative;
        }

        .process-item:nth-child(even) {
            flex-direction: row-reverse;
        }

        .process-content {
            flex: 1;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .process-item:nth-child(odd) .process-content {
            margin-right: 30px;
            text-align: right;
        }

        .process-item:nth-child(even) .process-content {
            margin-left: 30px;
        }

        .process-dot {
            width: 30px;
            height: 30px;
            background: var(--gradient-pdpa);
            border-radius: 50%;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 0 0 5px rgba(79, 172, 254, 0.2);
        }

        /* Legal Basis Cards */
        .legal-cards {
            display: grid;
            gap: 20px;
            margin: 30px 0;
        }

        .legal-card {
            background: white;
            border: 2px solid var(--light);
            border-radius: 16px;
            padding: 25px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            transition: all 0.3s ease;
        }

        .legal-card:hover {
            border-color: #4facfe;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .legal-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-pdpa);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .legal-content h4 {
            color: var(--dark);
            margin-bottom: 10px;
        }

        .legal-content p {
            color: var(--gray);
            margin: 0;
        }

        /* FAQ Accordion */
        .faq-accordion .accordion-item {
            border: none;
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .faq-accordion .accordion-button {
            background: white;
            color: var(--dark);
            font-weight: 600;
            padding: 20px;
            border: none;
            box-shadow: none;
        }

        .faq-accordion .accordion-button:not(.collapsed) {
            background: var(--gradient-pdpa);
            color: white;
        }

        .faq-accordion .accordion-button::after {
            filter: none;
        }

        .faq-accordion .accordion-button:not(.collapsed)::after {
            filter: brightness(0) invert(1);
        }

        /* Contact DPO */
        .dpo-card {
            background: var(--gradient-pdpa);
            border-radius: 20px;
            padding: 40px;
            color: white;
            text-align: center;
            margin: 40px 0;
        }

        .dpo-title {
            font-family: 'Kanit', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .dpo-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .dpo-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .dpo-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .policy-title {
                font-size: 1.8rem;
            }
            
            .process-timeline::before {
                left: 20px;
            }
            
            .process-item {
                flex-direction: column !important;
                padding-left: 60px;
            }
            
            .process-item:nth-child(odd) .process-content,
            .process-item:nth-child(even) .process-content {
                margin: 0;
                text-align: left;
            }
            
            .process-dot {
                left: 20px;
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
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1 class="policy-title" data-aos="fade-up" data-aos-delay="100">
                    นโยบายคุ้มครองข้อมูลส่วนบุคคล
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

    <!-- Content Container -->
    <div class="content-container">
        
        <!-- Stats -->
        <div class="stats-container" data-aos="fade-up">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-content">
                    <h4>100%</h4>
                    <p>ปฏิบัติตาม PDPA</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h4>8</h4>
                    <p>สิทธิของเจ้าของข้อมูล</p>
                </div>
            </div>
            <!--  <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-lock"></i>
                </div>
              <div class="stat-content">
                    <h4>256bit</h4>
                    <p>การเข้ารหัสข้อมูล</p>
                </div> 
            </div>--> 
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h4>2562</h4>
                    <p>ปีที่ประกาศใช้ พ.ร.บ.</p>
                </div>
            </div>
        </div>

        <!-- Introduction -->
        <div class="content-card" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-info-circle"></i>
                บทนำ
            </div>
            <h2 class="mb-3">ความเป็นมาและหลักการสำคัญ</h2>
            <p class="lead">
                <?php echo $org['fname']; ?> ในฐานะผู้ควบคุมข้อมูลส่วนบุคคล (Data Controller) 
                ตระหนักถึงความสำคัญของการคุ้มครองข้อมูลส่วนบุคคลตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA) 
                เราจึงมุ่งมั่นที่จะปกป้องความเป็นส่วนตัวและข้อมูลส่วนบุคคลของท่านด้วยมาตรการที่เหมาะสมและได้มาตรฐานสากล
            </p>
            
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-balance-scale fa-2x text-primary"></i>
                        <div>
                            <h5 class="mb-1">Lawfulness</h5>
                            <small class="text-muted">ความชอบด้วยกฎหมาย</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-bullseye fa-2x text-success"></i>
                        <div>
                            <h5 class="mb-1">Purpose Limitation</h5>
                            <small class="text-muted">จำกัดวัตถุประสงค์</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-compress fa-2x text-warning"></i>
                        <div>
                            <h5 class="mb-1">Data Minimization</h5>
                            <small class="text-muted">เก็บเท่าที่จำเป็น</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Collection -->
        <div class="content-card" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-database"></i>
                ข้อมูลที่เก็บรวบรวม
            </div>
            <h2 class="mb-3">ประเภทข้อมูลส่วนบุคคลที่เก็บรวบรวม</h2>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ประเภทข้อมูล</th>
                        <th>รายละเอียด</th>
                        <th>ความจำเป็น</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>ข้อมูลระบุตัวตน</strong></td>
                        <td>ชื่อ-นามสกุล </td>
                        <td><span class="badge-required">จำเป็น</span></td>
                    </tr>
					
                    <tr>
                        <td><strong>ข้อมูลการติดต่อ</strong></td>
                        <td>ที่อยู่, โทรศัพท์, อีเมล, Line ID</td>
                        <td><span class="badge-required">จำเป็น</span></td>
                    </tr>
					<tr>
                        <td><strong>ข้อมูลระบุตัวตน</strong></td>
                        <td>เลขบัตรประชาชน, วันเกิด, เพศ, รูปถ่าย</td>
                        <td><span class="badge-optional">ตามบริการ</span></td>
                    </tr>
                    <tr>
                        <td><strong>ข้อมูลการศึกษา</strong></td>
                        <td>ประวัติการศึกษา, คุณวุฒิ</td>
                        <td><span class="badge-optional">ตามบริการ</span></td>
                    </tr>
                    <tr>
                        <td><strong>ข้อมูลการทำงาน</strong></td>
                        <td>อาชีพ, ตำแหน่ง, สถานที่ทำงาน</td>
                        <td><span class="badge-optional">ตามบริการ</span></td>
                    </tr>
                    <tr>
                        <td><strong>ข้อมูลทางการเงิน</strong></td>
                        <td>เลขบัญชี, ประวัติการชำระเงิน</td>
                        <td><span class="badge-optional">ตามบริการ</span></td>
                    </tr>
                    <tr>
                        <td><strong>ข้อมูลการใช้งาน</strong></td>
                        <td>IP Address, Cookies, Log Files</td>
                        <td><span class="badge-required">อัตโนมัติ</span></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="alert alert-warning mt-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>ข้อมูลส่วนบุคคลที่มีความอ่อนไหว:</strong> 
                เราจะเก็บรวบรวมเฉพาะกรณีจำเป็นและได้รับความยินยอมโดยชัดแจ้ง เช่น ข้อมูลสุขภาพ, ศาสนา, ข้อมูลชีวภาพ
            </div>
        </div>

        <!-- Legal Basis -->
        <div class="content-card" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-gavel"></i>
                ฐานทางกฎหมาย
            </div>
            <h2 class="mb-3">ฐานทางกฎหมายในการประมวลผลข้อมูล</h2>
            
            <div class="legal-cards">
                <div class="legal-card">
                    <div class="legal-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="legal-content">
                        <h4>ความยินยอม (Consent)</h4>
                        <p>เมื่อท่านให้ความยินยอมในการประมวลผลข้อมูลส่วนบุคคล เช่น การสมัครรับข่าวสาร การใช้คุกกี้</p>
                    </div>
                </div>
                
                <div class="legal-card">
                    <div class="legal-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="legal-content">
                        <h4>สัญญา (Contract)</h4>
                        <p>เพื่อการปฏิบัติตามสัญญาหรือการให้บริการตามคำขอของท่าน เช่น การสมัครใช้บริการ e-Service</p>
                    </div>
                </div>
                
                <div class="legal-card">
                    <div class="legal-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="legal-content">
                        <h4>หน้าที่ตามกฎหมาย (Legal Obligation)</h4>
                        <p>เพื่อปฏิบัติตามกฎหมายที่องค์กรต้องปฏิบัติ เช่น การเก็บเอกสารทางบัญชี การรายงานต่อหน่วยงานกำกับ</p>
                    </div>
                </div>
                
                <div class="legal-card">
                    <div class="legal-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="legal-content">
                        <h4>ภารกิจเพื่อประโยชน์สาธารณะ (Public Task)</h4>
                        <p>การดำเนินภารกิจเพื่อประโยชน์สาธารณะหรือการใช้อำนาจรัฐตามกฎหมาย</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rights -->
        <div class="content-card" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-user-check"></i>
                สิทธิของท่าน
            </div>
            <h2 class="mb-3">สิทธิ 8 ประการของเจ้าของข้อมูลส่วนบุคคล</h2>
            
            <div class="rights-grid">
                <div class="right-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="right-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการเข้าถึง</h4>
                    <p class="right-desc">ขอเข้าถึงและขอรับสำเนาข้อมูลส่วนบุคคลของท่านที่เรามีอยู่</p>
                </div>
                
                <div class="right-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="right-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการแก้ไข</h4>
                    <p class="right-desc">ขอแก้ไขข้อมูลส่วนบุคคลให้ถูกต้อง เป็นปัจจุบัน และสมบูรณ์</p>
                </div>
                
                <div class="right-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="right-icon">
                        <i class="fas fa-trash"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการลบ</h4>
                    <p class="right-desc">ขอให้ลบหรือทำลายข้อมูลส่วนบุคคล (Right to be Forgotten)</p>
                </div>
                
                <div class="right-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="right-icon">
                        <i class="fas fa-pause"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการระงับ</h4>
                    <p class="right-desc">ขอให้ระงับการใช้ข้อมูลส่วนบุคคลชั่วคราว</p>
                </div>
                
                <div class="right-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="right-icon">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการคัดค้าน</h4>
                    <p class="right-desc">คัดค้านการประมวลผลข้อมูลส่วนบุคคลในบางกรณี</p>
                </div>
                
                <div class="right-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="right-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการพกพา</h4>
                    <p class="right-desc">ขอรับข้อมูลในรูปแบบที่สามารถอ่านได้ด้วยเครื่องมืออัตโนมัติ</p>
                </div>
                
                <div class="right-card" data-aos="fade-up" data-aos-delay="700">
                    <div class="right-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการเพิกถอน</h4>
                    <p class="right-desc">เพิกถอนความยินยอมเมื่อใดก็ได้</p>
                </div>
                
                <div class="right-card" data-aos="fade-up" data-aos-delay="800">
                    <div class="right-icon">
                        <i class="fas fa-flag"></i>
                    </div>
                    <h4 class="right-title">สิทธิในการร้องเรียน</h4>
                    <p class="right-desc">ร้องเรียนต่อสำนักงาน คปส. หากเห็นว่าไม่ชอบด้วยกฎหมาย</p>
                </div>
            </div>
        </div>

        <!-- Process -->
        <div class="content-card" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-cog"></i>
                กระบวนการ
            </div>
            <h2 class="mb-4 text-center">ขั้นตอนการใช้สิทธิ</h2>
            
            <div class="process-timeline">
                <div class="process-item">
                    <div class="process-content">
                        <h5>1. ยื่นคำร้อง</h5>
                        <p class="text-muted">กรอกแบบฟอร์มขอใช้สิทธิพร้อมเอกสารยืนยันตัวตน</p>
                    </div>
                    <div class="process-dot">1</div>
                </div>
                
                <div class="process-item">
                    <div class="process-content">
                        <h5>2. ตรวจสอบ</h5>
                        <p class="text-muted">เจ้าหน้าที่ตรวจสอบความถูกต้องของคำร้อง</p>
                    </div>
                    <div class="process-dot">2</div>
                </div>
                
                <div class="process-item">
                    <div class="process-content">
                        <h5>3. ดำเนินการ</h5>
                        <p class="text-muted">ดำเนินการตามสิทธิที่ร้องขอภายใน 30 วัน</p>
                    </div>
                    <div class="process-dot">3</div>
                </div>
                
                <div class="process-item">
                    <div class="process-content">
                        <h5>4. แจ้งผล</h5>
                        <p class="text-muted">แจ้งผลการดำเนินการให้ท่านทราบ</p>
                    </div>
                    <div class="process-dot">4</div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="content-card" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-question-circle"></i>
                คำถามที่พบบ่อย
            </div>
            <h2 class="mb-3">FAQ</h2>
            
            <div class="accordion faq-accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ข้อมูลของฉันจะถูกเก็บไว้นานแค่ไหน?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            เราจะเก็บข้อมูลตามระยะเวลาที่จำเป็นเพื่อให้บรรลุวัตถุประสงค์ หรือตามที่กฎหมายกำหนด 
                            โดยทั่วไปข้อมูลการให้บริการเก็บไว้ 5 ปีหลังสิ้นสุดการให้บริการ 
                            ข้อมูลทางบัญชีเก็บไว้ 5 ปีตามกฎหมาย
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ฉันจะขอลบข้อมูลส่วนบุคคลได้อย่างไร?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            ท่านสามารถติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO) เพื่อยื่นคำร้องขอลบข้อมูล 
                            เราจะพิจารณาและดำเนินการภายใน 30 วัน ยกเว้นกรณีที่กฎหมายกำหนดให้เก็บไว้
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ข้อมูลของฉันจะถูกแชร์ให้ใครบ้าง?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            เราจะไม่ขาย ให้เช่า หรือแลกเปลี่ยนข้อมูลของท่าน 
                            การเปิดเผยจะทำเฉพาะกรณีที่จำเป็น เช่น หน่วยงานราชการที่มีอำนาจ 
                            ผู้ให้บริการที่ช่วยดำเนินงาน โดยมีสัญญารักษาความลับ
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DPO Contact -->
        <div class="dpo-card" data-aos="zoom-in">
            <h3 class="dpo-title">ติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO)</h3>
            <p class="mb-0">หากท่านมีคำถามเกี่ยวกับนโยบายนี้ หรือต้องการใช้สิทธิของท่าน</p>
            
            <div class="dpo-info">
                <div class="dpo-item">
                    <div class="dpo-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div><?php echo $org['fname']; ?></div>
                </div>
                <div class="dpo-item">
                    <div class="dpo-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div><?php echo $org['phone_1']; ?></div>
                </div>
                <div class="dpo-item">
                    <div class="dpo-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div><?php echo $org['email_1']; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    </script>
</body>
</html>
