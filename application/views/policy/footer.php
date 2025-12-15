    </main>
    <!-- End Main Content -->

    <!-- Policy Footer -->
    <footer style="background: #1E293B; color: #94A3B8; padding: 60px 0 30px; margin-top: 80px;">
        <div class="container">
            <div class="row">
                <!-- Organization Info -->
                <div class="col-lg-4 mb-4">
                    <h5 style="color: white; font-family: 'Kanit', sans-serif; margin-bottom: 20px;">
                        <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?>
                    </h5>
                    <p style="line-height: 1.8;">
                        <?php echo isset($org['address']) ? $org['address'] : ''; ?>
                        <?php if(isset($org['subdistric'])): ?> ต.<?php echo $org['subdistric']; ?><?php endif; ?>
                        <?php if(isset($org['district'])): ?> อ.<?php echo $org['district']; ?><?php endif; ?>
                        <?php if(isset($org['province'])): ?> จ.<?php echo $org['province']; ?><?php endif; ?>
                        <?php if(isset($org['zip_code'])): ?> <?php echo $org['zip_code']; ?><?php endif; ?>
                    </p>
                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <?php if(!empty($org['facebook'])): ?>
                        <a href="<?php echo $org['facebook']; ?>" target="_blank" 
                           style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); 
                                  border-radius: 50%; display: flex; align-items: center; 
                                  justify-content: center; color: white; text-decoration: none; 
                                  transition: all 0.3s;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if(!empty($org['line'])): ?>
                        <a href="https://line.me/R/ti/p/<?php echo $org['line']; ?>" target="_blank"
                           style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); 
                                  border-radius: 50%; display: flex; align-items: center; 
                                  justify-content: center; color: white; text-decoration: none; 
                                  transition: all 0.3s;">
                            <i class="fab fa-line"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if(!empty($org['youtube'])): ?>
                        <a href="<?php echo $org['youtube']; ?>" target="_blank"
                           style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); 
                                  border-radius: 50%; display: flex; align-items: center; 
                                  justify-content: center; color: white; text-decoration: none; 
                                  transition: all 0.3s;">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-4 mb-4">
                    <h5 style="color: white; font-family: 'Kanit', sans-serif; margin-bottom: 20px;">
                        นโยบายและข้อกำหนด
                    </h5>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 12px;">
                            <a href="<?php echo site_url('policy/terms'); ?>" 
                               style="color: #94A3B8; text-decoration: none; transition: color 0.3s;">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i>
                                นโยบายเว็บไซต์
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="<?php echo site_url('policy/security'); ?>" 
                               style="color: #94A3B8; text-decoration: none; transition: color 0.3s;">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i>
                                ความมั่นคงปลอดภัย
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="<?php echo site_url('policy/pdpa'); ?>" 
                               style="color: #94A3B8; text-decoration: none; transition: color 0.3s;">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i>
                                คุ้มครองข้อมูลส่วนบุคคล
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="<?php echo site_url('policy/privacy'); ?>" 
                               style="color: #94A3B8; text-decoration: none; transition: color 0.3s;">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i>
                                ประกาศความเป็นส่วนตัว
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="<?php echo site_url('policy/cookie'); ?>" 
                               style="color: #94A3B8; text-decoration: none; transition: color 0.3s;">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i>
                                นโยบายคุกกี้
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="<?php echo site_url('policy/membership'); ?>" 
                               style="color: #94A3B8; text-decoration: none; transition: color 0.3s;">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.8rem;"></i>
                                การสมัครสมาชิก
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-4 mb-4">
                    <h5 style="color: white; font-family: 'Kanit', sans-serif; margin-bottom: 20px;">
                        ติดต่อเรา
                    </h5>
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-phone me-2"></i>
                        <span>โทรศัพท์: <?php echo isset($org['phone_1']) ? $org['phone_1'] : '0-4303-9711'; ?></span>
                        <?php if(!empty($org['phone_2'])): ?>
                            <br>
                            <span style="margin-left: 25px;"><?php echo $org['phone_2']; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($org['fax'])): ?>
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-fax me-2"></i>
                        <span>โทรสาร: <?php echo $org['fax']; ?></span>
                    </div>
                    <?php endif; ?>
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-envelope me-2"></i>
                        <span>อีเมล: <?php echo isset($org['email_1']) ? $org['email_1'] : 'saraban101@sawang.go.th'; ?></span>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-clock me-2"></i>
                        <span>เวลาทำการ: <?php echo isset($org['office_hours']) ? $org['office_hours'] : 'จันทร์-ศุกร์ 08:30-16:30 น.'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Divider -->
            <hr style="border-color: rgba(255,255,255,0.1); margin: 40px 0 20px;">
            
            <!-- Copyright -->
            <div class="text-center">
                <p style="margin-bottom: 10px;">
                    © <?php echo date('Y'); ?> <?php echo isset($org['fname']) ? $org['fname'] : 'องค์การบริหารส่วนตำบล'; ?> - สงวนลิขสิทธิ์
                </p>
                <p style="font-size: 0.9rem; opacity: 0.7;">
                    อัปเดตล่าสุด: 1 มกราคม 2568 | 
                    เวอร์ชัน: <?php echo $org['policy_version'] ?? '2.0'; ?>
                </p>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent Banner -->
    <div id="cookieConsent" style="position: fixed; bottom: 0; left: 0; right: 0; 
                                   background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); 
                                   padding: 20px; display: none; z-index: 1000;">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="font-size: 2rem;">🍪</div>
                    <div>
                        <strong>เว็บไซต์นี้ใช้คุกกี้</strong>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                            เพื่อพัฒนาประสบการณ์การใช้งานของท่าน 
                            <a href="<?php echo site_url('policy/cookie'); ?>" style="color: var(--primary);">
                                อ่านนโยบายคุกกี้
                            </a>
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="acceptCookies()" class="btn btn-primary btn-sm">
                        ยอมรับทั้งหมด
                    </button>
                    <button onclick="rejectCookies()" class="btn btn-outline-secondary btn-sm">
                        ปฏิเสธ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop" style="position: fixed; bottom: 30px; right: 30px; 
                                  width: 50px; height: 50px; background: var(--primary); 
                                  color: white; border: none; border-radius: 50%; 
                                  display: none; align-items: center; justify-content: center; 
                                  cursor: pointer; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3); 
                                  transition: all 0.3s ease; z-index: 100;">
        <i class="fas fa-chevron-up"></i>
    </button>

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

        // Hide loading
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('pageLoading').classList.add('hidden');
            }, 500);
        });

        // Mobile Menu
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Back to Top
        const backToTop = document.getElementById('backToTop');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });

        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Cookie Consent
        function checkCookieConsent() {
            const consent = localStorage.getItem('cookieConsent');
            if (!consent) {
                setTimeout(function() {
                    document.getElementById('cookieConsent').style.display = 'block';
                }, 2000);
            }
        }

        function acceptCookies() {
            localStorage.setItem('cookieConsent', 'accepted');
            localStorage.setItem('cookieConsentDate', new Date().toISOString());
            document.getElementById('cookieConsent').style.display = 'none';
        }

        function rejectCookies() {
            localStorage.setItem('cookieConsent', 'rejected');
            localStorage.setItem('cookieConsentDate', new Date().toISOString());
            document.getElementById('cookieConsent').style.display = 'none';
        }

        // Check cookie consent on page load
        checkCookieConsent();

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add hover effect to footer links
        document.querySelectorAll('footer a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.color = '#ffffff';
            });
            link.addEventListener('mouseleave', function() {
                this.style.color = '#94A3B8';
            });
        });

        // Print function for policies
        function printPolicy() {
            window.print();
        }

        // Copy link to clipboard
        function copyPolicyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(function() {
                alert('ลิงก์ถูกคัดลอกแล้ว');
            });
        }
    </script>

    <!-- Print Styles -->
    <style media="print">
        .top-navbar,
        .back-to-site,
        .mobile-sidebar,
        .mobile-overlay,
        #cookieConsent,
        #backToTop,
        footer {
            display: none !important;
        }
        
        body {
            padding-top: 0 !important;
        }
        
        .content-card {
            box-shadow: none !important;
            page-break-inside: avoid;
        }
    </style>
</body>
</html>
