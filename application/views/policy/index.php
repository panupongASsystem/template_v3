<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นโยบายและข้อกำหนดการใช้งานเว็บไซต์ - <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/policy.css'); ?>">
    <style>
        /* กำหนดขนาด logo */
        .org-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .org-logo img {
            max-width: 120px;
            max-height: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        
        @media (max-width: 768px) {
            .org-logo img {
                max-width: 80px;
                max-height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="org-logo">
                <img src="<?php echo base_url('docs/logo.png'); ?>" 
                     alt="<?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23667eea\'/%3E%3Ctext x=\'50\' y=\'50\' text-anchor=\'middle\' dy=\'.3em\' fill=\'white\' font-size=\'40\' font-family=\'Arial\'%3E🏛️%3C/text%3E%3C/svg%3E';">
            </div>
            <h1>นโยบายและข้อกำหนดการใช้งานเว็บไซต์</h1>
            <p class="subtitle"><?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบลสว่าง'; ?></p>
            <p class="subtitle">ระบบบริการประชาชน e-Service</p>
        </div>

        <!-- Navigation -->
        <div class="navigation">
            <div class="nav-tabs">
                <a href="<?php echo site_url('policy/terms'); ?>" class="nav-tab <?php echo ($this->uri->segment(2) == 'terms') ? 'active' : ''; ?>">
                    นโยบายเว็บไซต์
                </a>
                <a href="<?php echo site_url('policy/security'); ?>" class="nav-tab <?php echo ($this->uri->segment(2) == 'security') ? 'active' : ''; ?>">
                    ความมั่นคงปลอดภัย
                </a>
                <a href="<?php echo site_url('policy/pdpa'); ?>" class="nav-tab <?php echo ($this->uri->segment(2) == 'pdpa') ? 'active' : ''; ?>">
                    คุ้มครองข้อมูลส่วนบุคคล
                </a>
                <a href="<?php echo site_url('policy/privacy'); ?>" class="nav-tab <?php echo ($this->uri->segment(2) == 'privacy') ? 'active' : ''; ?>">
                    ประกาศความเป็นส่วนตัว
                </a>
                <a href="<?php echo site_url('policy/cookie'); ?>" class="nav-tab <?php echo ($this->uri->segment(2) == 'cookie') ? 'active' : ''; ?>">
                    นโยบายคุกกี้
                </a>
                <a href="<?php echo site_url('policy/membership'); ?>" class="nav-tab <?php echo ($this->uri->segment(2) == 'membership') ? 'active' : ''; ?>">
                    การสมัครสมาชิก
                </a>
            </div>
        </div>

        <!-- Policy Cards for Index Page -->
        <?php if($this->uri->segment(2) == '' || $this->uri->segment(2) == 'index'): ?>
        <div class="policy-grid">
            <div class="policy-card">
                <div class="policy-icon">📋</div>
                <h3>นโยบายเว็บไซต์และข้อกำหนดการใช้งาน</h3>
                <p>ข้อกำหนด เงื่อนไข และวัตถุประสงค์ในการให้บริการเว็บไซต์</p>
                <a href="<?php echo site_url('policy/terms'); ?>" class="btn btn-primary">อ่านเพิ่มเติม</a>
            </div>

            <div class="policy-card">
                <div class="policy-icon">🔒</div>
                <h3>นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์</h3>
                <p>มาตรการรักษาความปลอดภัย Web Application Firewall และการป้องกันภัยคุกคาม</p>
                <a href="<?php echo site_url('policy/security'); ?>" class="btn btn-primary">อ่านเพิ่มเติม</a>
            </div>

            <div class="policy-card">
                <div class="policy-icon">🛡️</div>
                <h3>นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)</h3>
                <p>การเก็บ ใช้ เปิดเผย และคุ้มครองข้อมูลส่วนบุคคลตาม พ.ร.บ.</p>
                <a href="<?php echo site_url('policy/pdpa'); ?>" class="btn btn-primary">อ่านเพิ่มเติม</a>
            </div>

            <div class="policy-card">
                <div class="policy-icon">👤</div>
                <h3>ประกาศความเป็นส่วนตัว</h3>
                <p>รายละเอียดการจัดการข้อมูลส่วนบุคคลและสิทธิของเจ้าของข้อมูล</p>
                <a href="<?php echo site_url('policy/privacy'); ?>" class="btn btn-primary">อ่านเพิ่มเติม</a>
            </div>

            <div class="policy-card">
                <div class="policy-icon">🍪</div>
                <h3>นโยบายการใช้คุกกี้</h3>
                <p>การใช้คุกกี้และเทคโนโลยีติดตามบนเว็บไซต์</p>
                <a href="<?php echo site_url('policy/cookie'); ?>" class="btn btn-primary">อ่านเพิ่มเติม</a>
            </div>

            <div class="policy-card">
                <div class="policy-icon">👥</div>
                <h3>ข้อกำหนดการสมัครสมาชิก e-Service</h3>
                <p>เงื่อนไขและขั้นตอนการสมัครใช้งานระบบบริการออนไลน์</p>
                <a href="<?php echo site_url('policy/membership'); ?>" class="btn btn-primary">อ่านเพิ่มเติม</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Contact Footer -->
    <div class="contact-info">
        <h4>ติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO)</h4>
        <p><strong><?php echo $org['fname']; ?></strong></p>
        <p>📍 เลขที่ <?php echo $org['address']; ?> ตำบล<?php echo $org['subdistric']; ?> 
           อำเภอ<?php echo $org['district']; ?> จังหวัด<?php echo $org['province']; ?> <?php echo $org['zip_code']; ?></p>
        <p>📞 โทรศัพท์: <?php echo $org['phone_1']; ?> 
           <?php if(!empty($org['phone_2'])): ?>| <?php echo $org['phone_2']; ?><?php endif; ?></p>
        <?php if(!empty($org['fax'])): ?>
        <p>📠 โทรสาร: <?php echo $org['fax']; ?></p>
        <?php endif; ?>
        <p>✉️ อีเมล: <?php echo $org['email_1']; ?>
           <?php if(!empty($org['email_2'])): ?> | <?php echo $org['email_2']; ?><?php endif; ?></p>
        
        <?php if(!empty($org['facebook'])): ?>
        <div class="social-links">
            <a href="<?php echo $org['facebook']; ?>" target="_blank" class="btn btn-facebook">
                <i class="fab fa-facebook"></i> Facebook
            </a>
            <?php if(!empty($org['message'])): ?>
            <a href="<?php echo $org['message']; ?>" target="_blank" class="btn btn-messenger">
                <i class="fab fa-facebook-messenger"></i> Messenger
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="<?php echo base_url('assets/js/policy.js'); ?>"></script>
</body>
</html>
