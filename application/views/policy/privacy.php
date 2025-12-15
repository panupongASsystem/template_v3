<style>
    /* Privacy Policy Specific Styles */
    .privacy-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 80px 0 100px;
        position: relative;
        overflow: hidden;
    }

    .privacy-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .privacy-header-content {
        position: relative;
        z-index: 2;
    }

    .icon-box {
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

    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 10;
    }

    /* Navigation Pills - แบบ Horizontal */
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
        background: #667eea;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
    }

    .nav-pill-item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    .content-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .info-box {
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        border-left: 4px solid #667eea;
        padding: 25px;
        border-radius: 12px;
        margin: 25px 0;
    }

    .data-category {
        background: var(--light);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .data-category:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .category-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        float: left;
        margin-right: 20px;
    }

    .purpose-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .purpose-card {
        background: white;
        border: 2px solid var(--light);
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .purpose-card:hover {
        border-color: #667eea;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .purpose-icon {
        font-size: 48px;
        margin-bottom: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Back to Top */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 999;
    }

    .back-to-top.show {
        opacity: 1;
        visibility: visible;
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-card {
            padding: 25px;
        }

        .nav-pill-item {
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .purpose-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="privacy-header">
    <div class="privacy-header-content container text-center">
        <div class="icon-box" data-aos="zoom-in">
            <i class="fas fa-user-lock"></i>
        </div>
        <h1 class="text-white mb-3" data-aos="fade-up" data-aos-delay="100">
            ประกาศความเป็นส่วนตัว
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

<div class="content-wrapper">
    <!-- Navigation Pills -->
    <div class="nav-pills-container" data-aos="fade-down">
        <div class="nav-pills-custom">
            <a href="#introduction" class="nav-pill-item active">บทนำ</a>
            <a href="#data-collection" class="nav-pill-item">ข้อมูลที่เก็บ</a>
            <a href="#purpose" class="nav-pill-item">วัตถุประสงค์</a>
            <a href="#sharing" class="nav-pill-item">การแบ่งปัน</a>
            <a href="#retention" class="nav-pill-item">ระยะเวลา</a>
            <a href="#security" class="nav-pill-item">การรักษาความปลอดภัย</a>
            <a href="#rights" class="nav-pill-item">สิทธิของท่าน</a>
            <a href="#contact" class="nav-pill-item">ติดต่อ</a>
        </div>
    </div>

    <!-- Introduction -->
    <div id="introduction" class="content-card" data-aos="fade-up">
        <h2 class="mb-4">
            <i class="fas fa-info-circle text-primary me-2"></i>
            บทนำ
        </h2>
        <p class="lead">
            <?php echo $org['fname']; ?> ("เรา") เคารพสิทธิความเป็นส่วนตัวของท่าน 
            และมุ่งมั่นที่จะปกป้องข้อมูลส่วนบุคคลของท่านตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562
        </p>
        
        <div class="info-box">
            <h5 class="mb-3">
                <i class="fas fa-shield-alt me-2"></i>
                คำมั่นสัญญาของเรา
            </h5>
            <ul class="mb-0">
                <li>เก็บข้อมูลเท่าที่จำเป็นเพื่อการให้บริการ</li>
                <li>ไม่ขายหรือแลกเปลี่ยนข้อมูลของท่านกับบุคคลที่สาม</li>
                <li>ใช้มาตรการรักษาความปลอดภัยที่เหมาะสม</li>
                <li>เคารพสิทธิของท่านในการควบคุมข้อมูลส่วนบุคคล</li>
            </ul>
        </div>
        
        <p>
            ประกาศความเป็นส่วนตัวนี้อธิบายถึงวิธีการที่เราเก็บรวบรวม ใช้ เปิดเผย 
            และปกป้องข้อมูลส่วนบุคคลของท่าน รวมถึงสิทธิของท่านที่เกี่ยวข้องกับข้อมูลเหล่านั้น
        </p>
    </div>

    <!-- Data Collection -->
    <div id="data-collection" class="content-card" data-aos="fade-up">
        <h2 class="mb-4">
            <i class="fas fa-database text-primary me-2"></i>
            ข้อมูลที่เราเก็บรวบรวม
        </h2>
        
        <div class="data-category">
            <div class="category-icon">
                <i class="fas fa-user"></i>
            </div>
            <div style="overflow: hidden;">
                <h5>ข้อมูลส่วนตัว</h5>
                <p class="text-muted mb-2">ข้อมูลที่ใช้ระบุตัวตนของท่าน:</p>
                <ul>
                    <li>ชื่อ-นามสกุล </li>
                    <li>ที่อยู่, หมายเลขโทรศัพท์, อีเมล</li>
                    <li>วันเกิด, เพศ (ถ้ามี)</li>
                    <li>หมายเลขบัตรประชาชน/หนังสือเดินทาง (ถ้ามี)</li>
                    <li>รูปถ่าย (ถ้ามี)</li>
                </ul>
            </div>
        </div>
        
        <div class="data-category">
            <div class="category-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div style="overflow: hidden;">
                <h5>ข้อมูลการใช้บริการ</h5>
                <p class="text-muted mb-2">ข้อมูลเกี่ยวกับการใช้บริการของเรา:</p>
                <ul>
                    <li>ประวัติการทำธุรกรรม (ถ้ามี)</li>
                    <li>ข้อมูลการชำระเงิน (ถ้ามี)</li>
                    <li>การร้องขอบริการต่างๆ</li>
                    <li>การติดต่อสื่อสาร</li>
                </ul>
            </div>
        </div>
        
        <div class="data-category">
            <div class="category-icon">
                <i class="fas fa-laptop"></i>
            </div>
            <div style="overflow: hidden;">
                <h5>ข้อมูลทางเทคนิค</h5>
                <p class="text-muted mb-2">ข้อมูลที่เก็บอัตโนมัติเมื่อท่านใช้เว็บไซต์:</p>
                <ul>
                    <li>IP Address</li>
                    <li>ประเภทและเวอร์ชันของ Browser</li>
                    <li>ระบบปฏิบัติการ</li>
                    <li>Cookies และ Local Storage</li>
                    <li>พฤติกรรมการใช้งานเว็บไซต์</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Purpose -->
    <div id="purpose" class="content-card" data-aos="fade-up">
        <h2 class="mb-4">
            <i class="fas fa-bullseye text-primary me-2"></i>
            วัตถุประสงค์การใช้ข้อมูล
        </h2>
        
        <div class="purpose-grid">
            <div class="purpose-card">
                <div class="purpose-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h5>การให้บริการ</h5>
                <p class="text-muted">ให้บริการตามที่ท่านร้องขอและปฏิบัติตามสัญญา</p>
            </div>
            
            <div class="purpose-card">
                <div class="purpose-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <h5>ยืนยันตัวตน</h5>
                <p class="text-muted">ตรวจสอบและยืนยันตัวตนเพื่อความปลอดภัย</p>
            </div>
            
            <div class="purpose-card">
                <div class="purpose-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h5>ติดต่อสื่อสาร</h5>
                <p class="text-muted">ติดต่อเพื่อแจ้งข้อมูลและตอบคำถาม</p>
            </div>
            
            <div class="purpose-card">
                <div class="purpose-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h5>ปรับปรุงบริการ</h5>
                <p class="text-muted">วิเคราะห์เพื่อพัฒนาคุณภาพการให้บริการ</p>
            </div>
            
            <div class="purpose-card">
                <div class="purpose-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h5>ปฏิบัติตามกฎหมาย</h5>
                <p class="text-muted">ดำเนินการตามที่กฎหมายกำหนด</p>
            </div>
            
            <div class="purpose-card">
                <div class="purpose-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5>ป้องกันการทุจริต</h5>
                <p class="text-muted">ตรวจสอบและป้องกันการใช้งานที่ผิดปกติ</p>
            </div>
        </div>
    </div>

    <!-- Sharing -->
    <div id="sharing" class="content-card" data-aos="fade-up">
        <h2 class="mb-4">
            <i class="fas fa-share-alt text-primary me-2"></i>
            การแบ่งปันข้อมูล
        </h2>
        
        <p>เราอาจเปิดเผยข้อมูลส่วนบุคคลของท่านให้กับบุคคลหรือหน่วยงานดังต่อไปนี้:</p>
        
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ผู้รับข้อมูล</th>
                        <th>วัตถุประสงค์</th>
                        <th>ตัวอย่าง</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>หน่วยงานราชการ</strong></td>
                        <td>ปฏิบัติตามกฎหมาย</td>
                        <td>กรมสรรพากร, สำนักงาน ปปง.</td>
                    </tr>
                    <tr>
                        <td><strong>ผู้ให้บริการภายนอก</strong></td>
                        <td>สนับสนุนการดำเนินงาน</td>
                        <td>ผู้ให้บริการ Cloud, ระบบชำระเงิน</td>
                    </tr>
                    <tr>
                        <td><strong>ที่ปรึกษา</strong></td>
                        <td>ให้คำปรึกษาทางกฎหมาย/บัญชี</td>
                        <td>สำนักงานกฎหมาย, ผู้สอบบัญชี</td>
                    </tr>
                    <tr>
                        <td><strong>หน่วยงานฉุกเฉิน</strong></td>
                        <td>กรณีฉุกเฉินหรือภัยคุกคาม</td>
                        <td>ตำรวจ, หน่วยแพทย์ฉุกเฉิน</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="alert alert-warning mt-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>หมายเหตุ:</strong> เราจะไม่ขาย ให้เช่า หรือแลกเปลี่ยนข้อมูลส่วนบุคคลของท่านเพื่อประโยชน์ทางการค้า
        </div>
    </div>

    <!-- Retention -->
    <div id="retention" class="content-card" data-aos="fade-up">
        <h2 class="mb-4">
            <i class="fas fa-clock text-primary me-2"></i>
            ระยะเวลาการเก็บรักษาข้อมูล
        </h2>
        
        <p>เราจะเก็บรักษาข้อมูลส่วนบุคคลของท่านตามระยะเวลาที่จำเป็น:</p>
        
        <ul class="list-unstyled mt-4">
            <li class="mb-3">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>ข้อมูลการให้บริการ:</strong> 5 ปีนับจากวันสิ้นสุดการให้บริการ
            </li>
            <li class="mb-3">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>ข้อมูลทางบัญชี/การเงิน:</strong> 5 ปีตามที่กฎหมายกำหนด
            </li>
            <li class="mb-3">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>ข้อมูลการติดต่อสื่อสาร:</strong> 2 ปีนับจากการติดต่อครั้งสุดท้าย
            </li>
            <li class="mb-3">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>Cookies:</strong> ตามที่ระบุในนโยบายคุกกี้
            </li>
            
        </ul>
        
        <div class="info-box">
            <p class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                เมื่อพ้นกำหนดระยะเวลาแล้ว เราจะลบหรือทำลายข้อมูลส่วนบุคคลของท่านอย่างปลอดภัย 
                หรือทำให้ข้อมูลไม่สามารถระบุตัวบุคคลได้
            </p>
        </div>
    </div>

    <!-- Security -->
    <div id="security" class="content-card" data-aos="fade-up">
        <h2 class="mb-4">
            <i class="fas fa-lock text-primary me-2"></i>
            การรักษาความปลอดภัยของข้อมูล
        </h2>
        
        <p>เราใช้มาตรการรักษาความปลอดภัยทางเทคนิคและการบริหารจัดการที่เหมาะสม:</p>
        
        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <h5>
                    <i class="fas fa-server text-warning me-2"></i>
                    มาตรการทางเทคนิค
                </h5>
                <ul>
                    <li>การเข้ารหัสข้อมูล (Encryption)</li>
                    <li>ระบบ Web Application Firewall และ Malware Scanner</li>
                    <li>การสำรองข้อมูล (Backup)</li>
                    <li>การควบคุมการเข้าถึงระบบ</li>
                    <li>SSL Certificate สำหรับเว็บไซต์</li>
                </ul>
            </div>
            <div class="col-md-6 mb-3">
                <h5>
                    <i class="fas fa-users-cog text-info me-2"></i>
                    มาตรการทางการบริหาร
                </h5>
                <ul>
                    <li>การฝึกอบรมพนักงาน</li>
                     <!-- <li>ข้อตกลงการรักษาความลับ</li> -->
                    <li>นโยบายความปลอดภัยข้อมูล</li>
                     <!-- <li>การตรวจสอบและประเมินความเสี่ยง</li>
                    <li>แผนรองรับเหตุการณ์ละเมิดข้อมูล</li> -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Rights -->
    <div id="rights" class="content-card" data-aos="fade-up">
        <h2 class="mb-4">
            <i class="fas fa-user-check text-primary me-2"></i>
            สิทธิของเจ้าของข้อมูลส่วนบุคคล
        </h2>
        
        <p>ท่านมีสิทธิตามกฎหมายคุ้มครองข้อมูลส่วนบุคคล ดังนี้:</p>
        
        <div class="accordion mt-4" id="rightsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#right1">
                        1. สิทธิในการเข้าถึงข้อมูล (Right of Access)
                    </button>
                </h2>
                <div id="right1" class="accordion-collapse collapse show" data-bs-parent="#rightsAccordion">
                    <div class="accordion-body">
                        ท่านมีสิทธิขอเข้าถึงและขอรับสำเนาข้อมูลส่วนบุคคลของท่านที่อยู่ในความรับผิดชอบของเรา
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#right2">
                        2. สิทธิในการแก้ไขข้อมูล (Right to Rectification)
                    </button>
                </h2>
                <div id="right2" class="accordion-collapse collapse" data-bs-parent="#rightsAccordion">
                    <div class="accordion-body">
                        ท่านมีสิทธิขอแก้ไขข้อมูลส่วนบุคคลของท่านให้ถูกต้อง เป็นปัจจุบัน สมบูรณ์ และไม่ก่อให้เกิดความเข้าใจผิด
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#right3">
                        3. สิทธิในการลบข้อมูล (Right to Erasure)
                    </button>
                </h2>
                <div id="right3" class="accordion-collapse collapse" data-bs-parent="#rightsAccordion">
                    <div class="accordion-body">
                        ท่านมีสิทธิขอให้ลบหรือทำลายข้อมูลส่วนบุคคล หรือทำให้ข้อมูลเป็นข้อมูลที่ไม่สามารถระบุตัวบุคคลได้
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#right4">
                        4. สิทธิในการระงับการใช้ข้อมูล (Right to Restriction)
                    </button>
                </h2>
                <div id="right4" class="accordion-collapse collapse" data-bs-parent="#rightsAccordion">
                    <div class="accordion-body">
                        ท่านมีสิทธิขอให้ระงับการใช้ข้อมูลส่วนบุคคลชั่วคราวในกรณีที่เรากำลังตรวจสอบตามคำร้องขอ
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo site_url('policy/pdpa'); ?>" class="btn btn-primary">
                <i class="fas fa-book me-2"></i>
                อ่านนโยบาย PDPA ฉบับเต็ม
            </a>
        </div>
    </div>

    <!-- Contact -->
    <div id="contact" class="content-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;" data-aos="fade-up">
        <h2 class="mb-4 text-white">
            <i class="fas fa-phone-alt me-2"></i>
            ติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล
        </h2>
        
        <p>หากท่านมีคำถามเกี่ยวกับประกาศความเป็นส่วนตัวนี้ หรือต้องการใช้สิทธิของท่าน กรุณาติดต่อ:</p>
        
        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <h5 class="text-white">เจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO)</h5>
                <p class="mb-1">
                    <i class="fas fa-building me-2"></i>
                    <?php echo $org['fname']; ?>
                </p>
                <p class="mb-1">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <?php echo $org['address']; ?>
                    <?php if(isset($org['subdistric'])): ?> ต.<?php echo $org['subdistric']; ?><?php endif; ?>
                    <?php if(isset($org['district'])): ?> อ.<?php echo $org['district']; ?><?php endif; ?>
                    <?php if(isset($org['province'])): ?> จ.<?php echo $org['province']; ?><?php endif; ?>
                    <?php if(isset($org['zip_code'])): ?> <?php echo $org['zip_code']; ?><?php endif; ?>
                </p>
               
            </div>
            <div class="col-md-6 mb-3">
                <h5 class="text-white">ช่องทางการติดต่อ</h5>
                <p class="mb-1">
                    <i class="fas fa-phone me-2"></i>
                    โทรศัพท์: <?php echo $org['phone_1']; ?>
                </p>
                <p class="mb-1">
                    <i class="fas fa-envelope me-2"></i>
                    อีเมล: <?php echo $org['email_1']; ?>
                </p>
                <p class="mb-1">
                    <i class="fas fa-clock me-2"></i>
                    วันเวลาทำการ: จันทร์-ศุกร์ 08:30-16:30 น.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Back to Top -->
<div class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
</div>

<script>
    // Smooth scroll and active nav
    document.querySelectorAll('.nav-pill-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class
            document.querySelectorAll('.nav-pill-item').forEach(nav => {
                nav.classList.remove('active');
            });
            
            // Add active class
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
    
    // Update active nav on scroll
    window.addEventListener('scroll', function() {
        let current = '';
        const sections = document.querySelectorAll('.content-card');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (scrollY >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });
        
        document.querySelectorAll('.nav-pill-item').forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === '#' + current) {
                item.classList.add('active');
            }
        });
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
</script>