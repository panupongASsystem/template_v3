</div>

</div>

</div>

<?php
/**
 * Footer with Simple Session Management - Hidden Modal Mode
 * Session ทำงานปกติ แต่ Modal ถูกซ่อนไว้ แสดง Toast แทน
 */

// ตรวจสอบสถานะการเข้าสู่ระบบ
$is_logged_in = false;
$user_info = [];
$user_type = '';

// ตรวจสอบผู้ใช้ประชาชน (Public User)
if ($this->session->userdata('mp_id')) {
    $is_logged_in = true;
    $user_type = 'public';
    $user_info = [
        'id' => $this->session->userdata('mp_id'),
        'name' => trim($this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname')),
        'email' => $this->session->userdata('mp_email'),
        'img' => $this->session->userdata('mp_img'),
        'login_type' => 'ประชาชน'
    ];
}
// ตรวจสอบเจ้าหน้าที่ (Staff User)
elseif ($this->session->userdata('m_id')) {
    $is_logged_in = true;
    $user_type = 'staff';
    $user_info = [
        'id' => $this->session->userdata('m_id'),
        'name' => trim($this->session->userdata('m_fname') . ' ' . $this->session->userdata('m_lname')),
        'username' => $this->session->userdata('m_username'),
        'img' => $this->session->userdata('m_img'),
        'level' => $this->session->userdata('m_level'),
        'login_type' => 'เจ้าหน้าที่'
    ];
}
?>

<?php
$controller = $this->router->fetch_class();
$method = $this->router->fetch_method();

$is_home_page = (strtolower($controller) === 'home' && strtolower($method) === 'index');

$show_service_links = $is_home_page;
$margin_top = $is_home_page ? '70px' : '180px';
$bg_class = $is_home_page ? 'bg-link2' : 'bg-link2-other';
?>

<div class="bg-footer">
    <img class="animation-wind-R animation-wind-9" src="<?php echo base_url('docs/animation-leaf2.png'); ?>">

        <img class="cloud-animation cloud-animation-1" src="<?php echo base_url('docs/cloud-footer1.png'); ?>">
        <img class="cloud-animation cloud-animation-2" src="<?php echo base_url('docs/cloud-footer2.png'); ?>">
        <img class="cloud-animation cloud-animation-3" src="<?php echo base_url('docs/cloud-footer1.png'); ?>">
        <img class="cloud-animation cloud-animation-4" src="<?php echo base_url('docs/cloud-footer2.png'); ?>">


        <!-- ใช้ตัวแปร $bg_class แทน strpos แบบเก่า -->
        <div class="<?php echo $bg_class; ?>">
    <footer class="footer">
            <div style="position: absolute; z-index: 2; top: 457px; left: 301px;">
                <img src="<?php echo base_url("docs/bird.gif"); ?>">
            </div>

            <div class="animation-text-orbortor-footer">
                <img src="<?php echo base_url("docs/text-orbortor-footer.png"); ?>">
            </div>

            <div class="google-map-footer">
                <iframe src="<?php echo get_config_value('google_map'); ?>" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
			
            <?php for ($i = 1; $i <= 6; $i++) : ?>
                <img class="wipwap dot-news-animation-<?php echo $i; ?>" src="<?php echo base_url('docs/light-1.png'); ?>">
            <?php endfor; ?>
            <?php for ($i = 7; $i <= 11; $i++) : ?>
                <img class="wipwap dot-news-animation-<?php echo $i; ?>" src="<?php echo base_url('docs/light-2.png'); ?>">
            <?php endfor; ?>

            <?php if ($show_service_links): ?>
                <!-- Service Links Slider (Footer) -->
                <div class="service-slider">
                    <div class="slider-container">
                        <!-- ในส่วน View - เปลี่ยนชื่อตัวแปรใหม่ -->

                        <div class="slider-container">
                            <div class="slider-wrapper" id="sliderWrapper">
                                <?php if (!empty($province_links['Province'])): ?>
                                    <div class="slide-service-link">
                                        <a href="<?= $province_links['Province'] ?>" target="_blank">
                                            <img src="docs/link1.png" alt="จังหวัด">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($province_links['PAO'])): ?>
                                    <div class="slide-service-link">
                                        <a href="<?= $province_links['PAO'] ?>" target="_blank">
                                            <img src="docs/link2.png" alt="อบจ">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($province_links['PPHO'])): ?>
                                    <div class="slide-service-link">
                                        <a href="<?= $province_links['PPHO'] ?>" target="_blank">
                                            <img src="docs/link3.png" alt="สสจ">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="slide-service-link">
                                    <a href="https://www.cgd.go.th/cs/internet/internet/%E0%B8%AB%E0%B8%99%E0%B9%89%E0%B8%B2%E0%B8%AB%E0%B8%A5%E0%B8%B1%E0%B8%812.html?page_locale=th_TH" target="_blank">
                                        <img src="docs/link4.png" alt="กรมการปกครอง">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://moi.go.th/moi/" target="_blank">
                                        <img src="docs/link5.png" alt="กระทรวงมหาดไทย">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://www.doe.go.th/" target="_blank">
                                        <img src="docs/link6.png" alt="กรมการจัดหางาน">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://www.nhso.go.th/" target="_blank">
                                        <img src="docs/link7.png" alt="สำนักงานหลักประกันสุขภาพแห่งชาติ">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://www.mdes.go.th/service?a=29" target="_blank">
                                        <img src="docs/mdes.png" alt="กระทรวงการพัฒนาสังคมและความมั่นคงของมนุษย์">
                                    </a>
                                </div>

                                <?php if (!empty($province_links['Damrongdhama'])): ?>
                                    <div class="slide-service-link">
                                        <a href="<?= $province_links['Damrongdhama'] ?>" target="_blank">
                                            <img src="docs/link8.png" alt="ศูนย์ดำรงธรรม">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="slide-service-link">
                                    <a href="https://www.admincourt.go.th/admincourt/site/09illustration.html" target="_blank">
                                        <img src="docs/link9.png" alt="ศาลปกครองกลาง">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://www.dla.go.th/index.jsp" target="_blank">
                                        <img src="docs/link10.png" alt="กรมการปกครองส่วนท้องถิ่น">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://info.go.th/" target="_blank">
                                        <img src="docs/link11.png" alt="ศูนย์ข้อมูลข่าวสารของราชการ">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://moi.go.th/moi/about-us/%E0%B8%82%E0%B9%89%E0%B8%AD%E0%B8%A1%E0%B8%B9%E0%B8%A5%E0%B8%97%E0%B8%B1%E0%B9%88%E0%B8%A7%E0%B9%84%E0%B8%9B%E0%B9%80%E0%B8%81%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%81%E0%B8%B1%E0%B8%9A%E0%B8%81/%E0%B8%A1%E0%B8%AB%E0%B8%B2%E0%B8%94%E0%B9%84%E0%B8%97%E0%B8%A2%E0%B8%8A%E0%B8%A7%E0%B8%99%E0%B8%A3%E0%B8%B9%E0%B9%89/" target="_blank">
                                        <img src="docs/link12.png" alt="มหาดไทยช่วนรู้">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://www.dla.go.th/servlet/EbookServlet?ebookGroup=2" target="_blank">
                                        <img src="docs/link13.png" alt="E-book กรมการปกครองส่วนท้องถิ่น">
                                    </a>
                                </div>

                                <div class="slide-service-link">
                                    <a href="https://www.oic.go.th/web2017/km/index.html" target="_blank">
                                        <img src="docs/link14.png" alt="สำนักงานคณะกรรมการข้อมูลข่าวสารของราชการ">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="custom-button-prev" id="prevBtn">
                        <img src="docs/pre-home.png" alt="Previous">
                    </div>
                    <div class="custom-button-next" id="nextBtn">
                        <img src="docs/next-home.png" alt="Next">
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center" style="position: relative; z-index: 5; margin-top: <?php echo $margin_top; ?>; margin-left: 0px">
                <span class="font-link">
                    <?php echo get_config_value('fname'); ?> <?php echo get_config_value('address'); ?> ตำบล<?php echo get_config_value('subdistric'); ?> อำเภอ<?php echo get_config_value('district'); ?> จังหวัด<?php echo get_config_value('province'); ?> <?php echo get_config_value('zip_code'); ?><br>
                    <?php
                    $phone_1 = get_config_value('phone_1');
                    $phone_2 = get_config_value('phone_2');
                    $fax = get_config_value('fax');

                    // ตรวจสอบว่าโทรศัพท์และโทรสารเหมือนกันไหม
                    if (!empty($phone_1) && !empty($fax) && $phone_1 == $fax) { ?>
                        โทรศัพท์/โทรสาร :
                        <?php echo $phone_1;
                        if (!empty($phone_2)) {
                            echo ', ' . $phone_2;
                        } ?>
                    <?php } else { ?>
                        <?php if (!empty($phone_1)) { ?>
                            โทรศัพท์ :
                            <?php
                            echo $phone_1;
                            if (!empty($phone_2)) {
                                echo ', ' . $phone_2;
                            }
                            ?>
                        <?php } ?>
                        <?php if (!empty($fax)) { ?>
                            โทรสาร : <?php echo $fax; ?>
                        <?php } ?>
                    <?php } ?>

                    <?php if (!empty(get_config_value('email_1'))) { ?>
                        e-mail :
                        <?php
                        echo get_config_value('email_1');
                        if (!empty(get_config_value('email_2'))) {
                            echo ', ' . get_config_value('email_2');
                        }
                        ?>
                    <?php } ?>
                </span>
            </div>
		
		<div class="policy-notice" 
     style="
        position: absolute;
        z-index: 4;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 18px;
        font-size: 20px;
        color: #000000;
        margin-top: 20px;
    ">
    <i class="fas fa-certificate" style="color: #3b82f6; font-size: 16px;"></i>
    <a href="/policy/terms" target="_blank" 
       style="color: #000000; text-decoration: none; font-weight: 500; transition: all 0.3s ease;"
       onmouseover="this.style.color='#3b82f6'; this.style.textDecoration='underline'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.color='#000000'; this.style.textDecoration='none'; this.style.transform='translateY(0)';">
        ข้อกำหนดใช้งานเว็บไซต์
    </a>
    
    <i class="fas fa-shield-alt" style="color: #8b5cf6; font-size: 16px;"></i>
    <a href="/policy/security" target="_blank" 
       style="color: #000000; text-decoration: none; font-weight: 500; transition: all 0.3s ease;"
       onmouseover="this.style.color='#8b5cf6'; this.style.textDecoration='underline'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.color='#000000'; this.style.textDecoration='none'; this.style.transform='translateY(0)';">
        นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์
    </a>
    
    <i class="fas fa-user-shield" style="color: #057d19; font-size: 16px;"></i>
    <a href="/policy/privacy" target="_blank" 
       style="color: #000000; text-decoration: none; font-weight: 500; transition: all 0.3s ease;"
       onmouseover="this.style.color='#057d19'; this.style.textDecoration='underline'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.color='#000000'; this.style.textDecoration='none'; this.style.transform='translateY(0)';">
        นโยบายความเป็นส่วนตัว
    </a>
</div>
		
		
		

            <div class="row" style="margin: auto; position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); width: 100%;">
                <div class="col-12" style="text-align: center;">
                    <div class="">
                        <span class="font-footer underline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-c-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.146 4.992c.961 0 1.641.633 1.729 1.512h1.295v-.088c-.094-1.518-1.348-2.572-3.03-2.572-2.068 0-3.269 1.377-3.269 3.638v1.073c0 2.267 1.178 3.603 3.27 3.603 1.675 0 2.93-1.02 3.029-2.467v-.093H9.875c-.088.832-.75 1.418-1.729 1.418-1.224 0-1.927-.891-1.927-2.461v-1.06c0-1.583.715-2.503 1.927-2.503" />
                            </svg>
                            สงวนลิขสิทธิ์ <?php echo date('Y') + 543; ?> โดย <a href="https://www.assystem.co.th/" target="_blank" style="font-weight: 600; color: #fff !important;">บริษัท เอเอส ซิสเต็ม จำกัด</a>&nbsp;&nbsp;
                            <img src="<?php echo base_url('docs/aslicense.png'); ?>" alt="AS SYSTEM" width="35" height="25" style="vertical-align:middle;">
                            &nbsp;&nbsp;ฝ่ายขายโทร หรือ Line id : &nbsp;<a href="tel:<?php echo get_sales_phone(); ?>" style="font-weight: 600; text-decoration: none; color: inherit;"><b><?php echo get_sales_phone(); ?></b></a>
							&nbsp;&nbsp;&nbsp;&nbsp;

<a href="google_drive_legal/privacy" 
   target="_blank" 
   rel="noopener noreferrer"
   style="font-size: 0.85em;
          font-weight: 500; 
          color: #fff !important; 
          text-decoration: none; 
          padding: 4px 14px; 
          border: 1px solid rgba(255,255,255,0.25); 
          border-radius: 20px; 
          transition: all 0.25s ease; 
          display: inline-flex; 
          align-items: center; 
          gap: 6px;
          background: rgba(255,255,255,0.2);"
   onmouseover="this.style.backgroundColor='rgba(0,0,0,0.1)'; 
                this.style.borderColor='rgba(255,255,255,0.5)'; 
                this.style.boxShadow='0 2px 8px rgba(0,0,0,0.2)';" 
   onmouseout="this.style.backgroundColor='rgba(255,255,255,0.2)'; 
               this.style.borderColor='rgba(255,255,255,0.25)'; 
               this.style.boxShadow='none';">
    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
    </svg>
    <span style="color: #fff;">นโยบายความเป็นส่วนตัว</span>
    <span style="font-weight: 600; letter-spacing: 0.5px;">
        <span style="color: #4285F4; text-shadow: 0 0 3px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.8);">G</span><span style="color: #EA4335; text-shadow: 0 0 3px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.8);">o</span><span style="color: #FBBC04; text-shadow: 0 0 3px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.8);">o</span><span style="color: #4285F4; text-shadow: 0 0 3px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.8);">g</span><span style="color: #34A853; text-shadow: 0 0 3px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.8);">l</span><span style="color: #EA4335; text-shadow: 0 0 3px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.8);">e</span>
    </span>
</a>

							
                            &nbsp;&nbsp;&nbsp; สถิติผู้เข้าชม

                            <!-- Web Analytics Counter อยู่ในบรรทัดเดียวกัน -->
                            <span id="counter-container" style="display: inline-block; margin-left: 10px; vertical-align: middle;">
                                <!-- Counter script will be loaded here -->
                            </span>
                        </span>
                    </div>
                </div>
            </div>
    </footer>
        </div>
</div>

<!-- 🎨 CSS สำหรับ Animations (เดิม) -->
<style>
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes cloudFloat {

        0%,
        100% {
            transform: translateX(0px) translateY(0px);
        }

        25% {
            transform: translateX(20px) translateY(-10px);
        }

        50% {
            transform: translateX(-10px) translateY(5px);
        }

        75% {
            transform: translateX(15px) translateY(-5px);
        }
    }

    @keyframes twinkle {

        0%,
        100% {
            opacity: 0.3;
            transform: scale(1);
        }

        50% {
            opacity: 1;
            transform: scale(1.2);
        }
    }

    /* .cloud-animation {
        position: absolute;
        z-index: 1;
        animation: cloudFloat 8s ease-in-out infinite;
    }

    .cloud-animation-1 {
        top: 20px;
        left: 10%;
        animation-delay: 0s;
    }

    .cloud-animation-2 {
        top: 50px;
        right: 15%;
        animation-delay: 2s;
    } */

    /* .wipwap {
        position: absolute;
        z-index: 2;
        animation: twinkle 2s ease-in-out infinite;
    }

    .dot-news-animation-1 {
        top: 80px;
        left: 20%;
        animation-delay: 0.1s;
    }

    .dot-news-animation-2 {
        top: 120px;
        right: 25%;
        animation-delay: 0.3s;
    }

    .dot-news-animation-3 {
        top: 60px;
        left: 50%;
        animation-delay: 0.5s;
    }

    .dot-news-animation-4 {
        top: 100px;
        right: 40%;
        animation-delay: 0.7s;
    }

    .dot-news-animation-5 {
        top: 140px;
        left: 70%;
        animation-delay: 0.9s;
    }

    .dot-news-animation-6 {
        top: 90px;
        right: 60%;
        animation-delay: 1.1s;
    }

    .dot-news-animation-7 {
        top: 160px;
        left: 30%;
        animation-delay: 1.3s;
    }

    .dot-news-animation-8 {
        top: 110px;
        right: 20%;
        animation-delay: 1.5s;
    }

    .dot-news-animation-9 {
        top: 70px;
        left: 80%;
        animation-delay: 1.7s;
    }

    .dot-news-animation-10 {
        top: 130px;
        right: 35%;
        animation-delay: 1.9s;
    }

    .dot-news-animation-11 {
        top: 150px;
        left: 60%;
        animation-delay: 2.1s;
    } */

    /* Responsive adjustments */
    @media (max-width: 768px) {

        .cloud-animation,
        .wipwap {
            display: none;
        }
    }

    /* 🚫 ซ่อน Modal ทั้งหมดด้วย CSS (แบบ Simple Mode) */
    .modal[id*="Session"],
    .modal[id*="session"],
    .modal[id*="Warning"],
    .modal[id*="warning"],
    .modal[id*="Logout"],
    .modal[id*="logout"],
    .error-modal,
    #errorModal {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        z-index: -9999 !important;
    }

    /* Toast Styles */
    .simple-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 500px;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        z-index: 99999;
        animation: slideInRight 0.3s ease-out;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.4;
        border: none;
    }

    .simple-toast.success {
        background: linear-gradient(135deg, #88d8c0, #6bb6ff);
        color: #fff;
    }

    .simple-toast.warning {
        background: linear-gradient(135deg, #ffeaa7, #fab1a0);
        color: #2d3748;
    }

    .simple-toast.danger {
        background: linear-gradient(135deg, #fd79a8, #fdcb6e);
        color: #fff;
    }

    .simple-toast.info {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
        color: #fff;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
</style>

<script>
    // นำฟังก์ชันไปใช้กับองค์ประกอบที่ต้องการ
    var animations = document.querySelectorAll('.wipwap');
    animations.forEach(function(animation) {
        // สุ่มการหน่วงเวลาแอนิเมชัน
        randomizeAnimationDelay(animation);

        // กำหนดค่าเริ่มต้น
        randomizePosition(animation);

        // เพิ่ม event listener เพื่อตรวจสอบการเปลี่ยนแปลงของ opacity
        animation.addEventListener('animationiteration', function() {
            // ตั้งเวลาเพื่อให้เกิดการเปลี่ยนแปลงตำแหน่งเมื่อ opacity = 0
            setTimeout(function() {
                randomizePosition(animation);
            }, 1500); // 50% ของเวลาแอนิเมชัน 3s
        });
    });
</script>

<!-- 🚨 REQUIRED: JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($is_logged_in): ?>
    <!-- 🔐 Session Manager Scripts -->
    <script src="<?php echo base_url('asset/js/pri-session-manager.js'); ?>"></script>

    <!-- 🚀 Simple Session Management (แบบ Hidden Modal) -->
    <script>
        // 🚨 Simple Session Management (ซ่อน Modal, Session ทำงานปกติ)
        document.addEventListener('DOMContentLoaded', function() {
            // console.log('📚 Simple Footer Session Manager initializing...');

            // ✅ กำหนด base_url
            window.base_url = '<?php echo base_url(); ?>';

            // User session info
            const sessionVars = {
                user_type: '<?php echo $user_type; ?>',
                is_logged_in: <?php echo $is_logged_in ? 'true' : 'false'; ?>,
                user_name: '<?php echo addslashes($user_info['name']); ?>',
                login_type: '<?php echo $user_info['login_type']; ?>',
                <?php if ($user_type === 'public'): ?>
                    mp_id: '<?php echo $this->session->userdata('mp_id'); ?>',
                    mp_fname: '<?php echo $this->session->userdata('mp_fname'); ?>',
                    mp_lname: '<?php echo $this->session->userdata('mp_lname'); ?>',
                    logged_in: '<?php echo $this->session->userdata('logged_in'); ?>'
                <?php elseif ($user_type === 'staff'): ?>
                    m_id: '<?php echo $this->session->userdata('m_id'); ?>',
                    tenant_id: '<?php echo $this->session->userdata('tenant_id'); ?>',
                    admin_id: '<?php echo $this->session->userdata('admin_id'); ?>',
                    user_id: '<?php echo $this->session->userdata('user_id'); ?>',
                    username: '<?php echo $this->session->userdata('username'); ?>',
                    logged_in: '<?php echo $this->session->userdata('logged_in'); ?>'
                <?php endif; ?>
            };

            <?php if ($user_type === 'public'): ?>
                // 🚫 Override Public Session Warning Functions (ซ่อน Modal แต่ Session ทำงาน)
                window.showSessionWarning = function(type) {
                    // console.log(`⚠️ Public Session Warning ${type} - HIDDEN but session working`);

                    if (type === '5min') {
                        //console.log('🕐 5 minutes warning - Public Session will expire soon');
                        showSimpleToast('⚠️ Session จะหมดอายุในอีก 5 นาที', 'warning', 3000);
                    } else if (type === '1min') {
                        //console.log('🚨 1 minute warning - Public Session will expire very soon!');
                        showSimpleToast('🚨 Session จะหมดอายุในอีก 1 นาที!', 'danger', 5000);
                    } else if (type === 'expired') {
                        //console.log('⏰ Public Session expired - Redirecting...');
                        showSimpleToast('🚪 Session หมดอายุ กำลังรีเฟรชหน้า...', 'info', 2000);
                        setTimeout(() => {
                            window.location.reload(true);
                        }, 2000);
                    }

                    return false; // ไม่แสดง modal
                };

                window.showLogoutModal = function() {
                    // console.log('🚪 Public Logout Modal - HIDDEN but logging out...');
                    showSimpleToast('🚪 กำลังออกจากระบบ...', 'info', 2000);

                    setTimeout(() => {
                        window.location.reload(true);
                    }, 2000);

                    return false;
                };

            <?php elseif ($user_type === 'staff'): ?>
                // 🚫 Override Admin Session Warning Functions (ซ่อน Modal แต่ Session ทำงาน)
                window.showAdminSessionWarning = function(type) {
                    // console.log(`⚠️ Admin Session Warning ${type} - HIDDEN but session working`);

                    if (type === '5min') {
                        // console.log('🕐 5 minutes warning - Admin Session will expire soon');
                        showSimpleToast('⚠️ Session จะหมดอายุในอีก 5 นาที', 'warning', 3000);
                    } else if (type === '1min') {
                        // console.log('🚨 1 minute warning - Admin Session will expire very soon!');
                        showSimpleToast('🚨 Session จะหมดอายุในอีก 1 นาที!', 'danger', 5000);
                    } else if (type === 'expired') {
                        // console.log('⏰ Admin Session expired - Redirecting...');
                        showSimpleToast('🚪 Session หมดอายุ กำลังรีเฟรชหน้า...', 'info', 2000);
                        setTimeout(() => {
                            window.location.reload(true);
                        }, 2000);
                    }

                    return false; // ไม่แสดง modal
                };

                window.showAdminLogoutModal = function() {
                    //console.log('🚪 Admin Logout Modal - HIDDEN but logging out...');
                    showSimpleToast('🚪 กำลังออกจากระบบ...', 'info', 2000);

                    setTimeout(() => {
                        window.location.reload(true);
                    }, 2000);

                    return false;
                };
            <?php endif; ?>

            // ✅ เริ่มต้น Session Manager (ทำงานปกติ)
            <?php if ($user_type === 'public'): ?>
                const hasPublicSession = sessionVars.mp_id || (sessionVars.logged_in && sessionVars.user_type === 'public');
                if (hasPublicSession && typeof window.initializePublicSessionManager === 'function') {
                    // console.log('✅ Initializing Public Session Manager (HIDDEN MODE)');
                    window.initializePublicSessionManager(hasPublicSession);
                }
            <?php elseif ($user_type === 'staff'): ?>
                const hasAdminSession = sessionVars.m_id || sessionVars.admin_id || sessionVars.user_id ||
                    (sessionVars.logged_in && sessionVars.user_type === 'staff');
                if (hasAdminSession && typeof window.initializeAdminSessionManager === 'function') {
                    // console.log('✅ Initializing Admin Session Manager (HIDDEN MODE)');
                    window.initializeAdminSessionManager(hasAdminSession);
                }
            <?php endif; ?>

            // โหลด Analytics Counter
            loadAnalyticsCounter();

            // console.log('✅ Simple Footer Session Management initialized for <?php echo $user_type; ?>');
            //console.log('📊 Session tracking: ACTIVE');
            // console.log('📱 Modals: HIDDEN');
            //  console.log('🔔 Notifications: TOAST');
            //  console.log('🔄 Keep alive: WORKING');
            //  console.log('🚪 Auto logout: ENABLED (refresh current page)');
        });

        // 🔔 Simple Toast Function
        function showSimpleToast(message, type = 'info', timeout = 3000) {
            try {
                const toastId = 'simple_toast_' + Date.now();
                const iconMap = {
                    'success': 'fa-check-circle',
                    'warning': 'fa-exclamation-triangle',
                    'danger': 'fa-exclamation-triangle',
                    'info': 'fa-info-circle'
                };

                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `simple-toast ${type}`;
                toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas ${iconMap[type] || 'fa-info-circle'}" style="font-size: 1.1em;"></i>
                <span style="flex: 1;">${message}</span>
                <button onclick="closeSimpleToast('${toastId}')" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1.2em; padding: 0; margin-left: 10px;">×</button>
            </div>
        `;

                document.body.appendChild(toast);

                if (timeout > 0) {
                    setTimeout(() => {
                        closeSimpleToast(toastId);
                    }, timeout);
                }

                //console.log(`🔔 Toast shown: ${message}`);

            } catch (error) {
                console.error('❌ Error showing toast:', error);
                console.log(`📢 FALLBACK: ${message}`);
            }
        }

        function closeSimpleToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }

        // 📊 โหลด Analytics Counter
        function loadAnalyticsCounter() {
            try {
                const script1 = document.createElement('script');
                script1.src = 'https://webanalytics.assystem.co.th/counter/show?domain=<?php echo get_config_value('domain'); ?>.go.th';
                script1.async = true;
                document.head.appendChild(script1);

                const script2 = document.createElement('script');
                script2.src = 'https://webanalytics.assystem.co.th/api/counter?domain=<?php echo get_config_value('domain'); ?>.go.th';
                script2.async = true;

                const counterContainer = document.getElementById('counter-container');
                if (counterContainer) {
                    counterContainer.appendChild(script2);
                }

                // console.log('✅ Analytics Counter loading...');
            } catch (error) {
                console.log('⚠️ Analytics Counter failed to load:', error);
            }
        }




        window.checkSessionStatus = function() {
            //  console.log('=== SESSION STATUS CHECK ===');

            <?php if ($user_type === 'public'): ?>
                if (window.PublicSessionManager && typeof window.PublicSessionManager.getState === 'function') {
                    const state = window.PublicSessionManager.getState();
                    // console.log('Public Session State:', state);
                    if (state.timeSinceUserActivity !== undefined) {
                        //   console.log('Time since last activity:', Math.round(state.timeSinceUserActivity / 1000), 'seconds');
                    }
                    if (state.remainingTime !== undefined) {
                        //    console.log('Remaining time:', Math.round(state.remainingTime / 1000), 'seconds');
                    }
                    //  console.log('Session is active:', state.isInitialized || false);
                }
            <?php elseif ($user_type === 'staff'): ?>
                if (window.SessionManager && typeof window.SessionManager.getState === 'function') {
                    const state = window.SessionManager.getState();
                    // console.log('Admin Session State:', state);
                    if (state.timeSinceUserActivity !== undefined) {
                        //     console.log('Time since last activity:', Math.round(state.timeSinceUserActivity / 1000), 'seconds');
                    }
                    if (state.remainingTime !== undefined) {
                        //    console.log('Remaining time:', Math.round(state.remainingTime / 1000), 'seconds');
                    }
                    //   console.log('Session is active:', state.isInitialized || false);
                }
            <?php endif; ?>

            // console.log('=== END STATUS CHECK ===');
        };

        //console.log('🚀 Simple Footer Session Management loaded for <?php echo $user_type; ?> user');
    </script>

<?php else: ?>
    <!-- 🚫 NO SESSION MANAGEMENT (ไม่ได้ Login) -->
    <script>
        // console.log('ℹ️ User not logged in - No Session Management loaded');

        // โหลดเฉพาะ Analytics Counter
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const script1 = document.createElement('script');
                script1.src = 'https://webanalytics.assystem.co.th/counter/show?domain=<?php echo get_config_value('domain'); ?>.go.th';
                script1.async = true;
                document.head.appendChild(script1);

                const script2 = document.createElement('script');
                script2.src = 'https://webanalytics.assystem.co.th/api/counter?domain=<?php echo get_config_value('domain'); ?>.go.th';
                script2.async = true;

                const counterContainer = document.getElementById('counter-container');
                if (counterContainer) {
                    counterContainer.appendChild(script2);
                }

                // console.log('✅ Analytics Counter loading (Guest mode)...');
            } catch (error) {
                console.log('⚠️ Analytics Counter failed to load:', error);
            }
        });
    </script>
<?php endif; ?>

</main>

<!-- เพิ่ม ลด ตัวอักษร  -->
<div class="font-size-controller">
    <!-- ปุ่มซ่อน (×) อยู่ขวาบนของ A+ -->
    <div style="position: relative;">
        <button class="font-size-hide-btn" id="fontSizeHideBtn" title="ซ่อนปุ่มควบคุม">×</button>
        <button class="increase-btn" id="increaseFontBtn" title="เพิ่มขนาดตัวอักษร">
            <span class="icon">ก+</span>
        </button>
    </div>
    
    <button class="decrease-btn" id="decreaseFontBtn" title="ลดขนาดตัวอักษร">
        <span class="icon">ก−</span>
    </button>
    <button class="reset-btn" id="resetFontBtn" title="รีเซ็ตขนาดตัวอักษร">
        <span class="icon">ก</span>
    </button>
</div>

<!-- ปุ่มแสดง (เมื่อซ่อน) -->
<button class="font-size-toggle" id="fontSizeToggle" title="แสดงปุ่มควบคุม">
    <span id="toggleIcon">ก</span>
</button>

<script>
(function() {
    'use strict';
    
    console.log('🚀 เริ่มต้นระบบควบคุมขนาดตัวอักษร');
    
    const body = document.body;
    const controller = document.querySelector('.font-size-controller');
    const toggleBtn = document.getElementById('fontSizeToggle');
    const hideBtn = document.getElementById('fontSizeHideBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    const increaseBtn = document.getElementById('increaseFontBtn');
    const decreaseBtn = document.getElementById('decreaseFontBtn');
    const resetBtn = document.getElementById('resetFontBtn');
    
    const fontSizes = [75, 87.5, 100, 112.5, 125, 137.5, 150];
    const fontSizeLabels = ['75', '87', '100', '112', '125', '137', '150'];
    
    let currentIndex = parseInt(localStorage.getItem('fontSizeIndex')) || 2;
    let isControllerVisible = localStorage.getItem('controllerVisible') !== 'false';
    
    // ตั้งค่าเริ่มต้นของ controller
    if (!isControllerVisible) {
        controller.classList.add('hidden');
        toggleBtn.classList.add('visible');
    }
    
    // ฟังก์ชันซ่อน controller
    hideBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        controller.classList.add('hidden');
        toggleBtn.classList.add('visible');
        isControllerVisible = false;
        localStorage.setItem('controllerVisible', 'false');
        console.log('🙈 ซ่อน controller');
    });
    
    // ฟังก์ชันแสดง controller
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        controller.classList.remove('hidden');
        toggleBtn.classList.remove('visible');
        isControllerVisible = true;
        localStorage.setItem('controllerVisible', 'true');
        console.log('👁️  แสดง controller');
    });
    
    // เก็บขนาด font-size เดิมของทุก element
    const originalSizes = new WeakMap();
    let isInitialized = false;
    
    function initializeElements() {
        if (isInitialized) return;
        
        console.log('💾 กำลังบันทึกขนาดเดิม...');
        
        const elements = document.querySelectorAll('body *:not(.font-size-controller):not(.font-size-controller *):not(.font-size-toggle)');
        let count = 0;
        
        elements.forEach(el => {
            const style = window.getComputedStyle(el);
            const fontSize = style.fontSize;
            
            if (fontSize && fontSize !== '0px') {
                originalSizes.set(el, fontSize);
                count++;
                
                if (el.className && typeof el.className === 'string') {
                    if (el.className.includes('font-welcome') || el.className.includes('news-dla')) {
                        console.log(`  └─ ${el.className}: ${fontSize}`);
                    }
                }
            }
        });
        
        console.log(`✅ บันทึกเสร็จ ${count} elements`);
        isInitialized = true;
    }
    
    function applyFontScale(scale) {
        console.log(`━━━━━━━━━━━━━━━━━━━━━`);
        console.log(`🎨 ปรับขนาด: ${scale}%`);
        
        const multiplier = scale / 100;
        let successCount = 0;
        let failCount = 0;
        
        const elements = document.querySelectorAll('body *:not(.font-size-controller):not(.font-size-controller *):not(.font-size-toggle)');
        
        elements.forEach(el => {
            const originalSize = originalSizes.get(el);
            if (originalSize) {
                try {
                    const originalPx = parseFloat(originalSize);
                    const newSize = originalPx * multiplier;
                    
                    el.style.setProperty('font-size', newSize + 'px', 'important');
                    successCount++;
                    
                    if (el.className && typeof el.className === 'string') {
                        if (el.className.includes('font-welcome') || el.className.includes('news-dla')) {
                            console.log(`  └─ ${el.className}: ${originalSize} → ${newSize}px`);
                        }
                    }
                } catch (e) {
                    failCount++;
                    console.error(`  ❌ Error: ${e.message}`);
                }
            }
        });
        
        console.log(`✅ สำเร็จ: ${successCount} | ❌ ล้มเหลว: ${failCount}`);
    }
    
    function setFontSize(index) {
        if (index < 0) index = 0;
        if (index >= fontSizes.length) index = fontSizes.length - 1;
        
        console.log(`🔄 setFontSize(${index}) = ${fontSizes[index]}%`);
        
        body.setAttribute('data-font-scale', fontSizeLabels[index]);
        applyFontScale(fontSizes[index]);
        
        localStorage.setItem('fontSizeIndex', index);
        currentIndex = index;
        
        console.log(`💾 บันทึกลง localStorage: index=${index}`);
        
        showNotification(fontSizes[index]);
    }
    
    function showNotification(size) {
        const oldNotif = document.querySelector('.font-size-notification');
        if (oldNotif) oldNotif.remove();
        
        const percentage = Math.round(size);
        let label = '';
        
        if (size <= 75) label = 'เล็กสุด';
        else if (size <= 87.5) label = 'เล็ก';
        else if (size <= 100) label = 'ปกติ';
        else if (size <= 125) label = 'ใหญ่';
        else label = 'ใหญ่มาก';
        
        const notification = document.createElement('div');
        notification.className = 'font-size-notification';
        notification.textContent = `ขนาด${label} ${percentage}%`;
        notification.style.opacity = '1';
        notification.style.animation = 'fadeInOut 2.5s ease-in-out';
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 2500);
    }
    
    // Event listeners
    increaseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('🖱️  คลิก: เพิ่มขนาด');
        
        if (currentIndex < fontSizes.length - 1) {
            setFontSize(currentIndex + 1);
        } else {
            console.log('⛔ ถึงขนาดสูงสุดแล้ว');
            this.style.animation = 'shake 0.5s';
            setTimeout(() => { this.style.animation = ''; }, 500);
            showNotification(fontSizes[currentIndex]);
        }
    });
    
    decreaseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('🖱️  คลิก: ลดขนาด');
        
        if (currentIndex > 0) {
            setFontSize(currentIndex - 1);
        } else {
            console.log('⛔ ถึงขนาดต่ำสุดแล้ว');
            this.style.animation = 'shake 0.5s';
            setTimeout(() => { this.style.animation = ''; }, 500);
            showNotification(fontSizes[currentIndex]);
        }
    });
    
    resetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('🖱️  คลิก: รีเซ็ต');
        setFontSize(2);
    });
    
    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOMContentLoaded');
            setTimeout(function() {
                initializeElements();
                if (currentIndex !== 2) {
                    console.log('🔄 กำลังใช้ขนาดที่บันทึก:', fontSizes[currentIndex] + '%');
                    setFontSize(currentIndex);
                }
            }, 500);
        });
    } else {
        console.log('📄 Document พร้อมแล้ว');
        setTimeout(function() {
            initializeElements();
            if (currentIndex !== 2) {
                console.log('🔄 กำลังใช้ขนาดที่บันทึก:', fontSizes[currentIndex] + '%');
                setFontSize(currentIndex);
            }
        }, 500);
    }
    
    console.log('✅ ระบบพร้อมใช้งาน');
})();
</script>



</body>

</html>