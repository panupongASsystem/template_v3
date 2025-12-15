<!-- Footer -->
<footer class="sticky-footer" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px 20px 0 0; margin-top: 40px; box-shadow: 0 -4px 20px rgba(0,0,0,0.1);">
    <div class="container my-auto">
        <div class="copyright text-center my-auto py-4">
            <span style="color: #fff; font-size: 14px; font-weight: 500;">
                © <?php echo date('Y'); ?> สงวนลิขสิทธิ์โดย 
                <a href="https://www.assystem.co.th" target="_blank" style="color: #a8edea; text-decoration: none; font-weight: 600;">ASSYSTEM.co.th</a> 
                | <a href="#" style="color: #fed6e3; text-decoration: none;">นโยบายเว็บไซต์</a> 
                | <a href="#" style="color: #fed6e3; text-decoration: none;">นโยบายความเป็นส่วนตัว</a>
            </span>
        </div>
    </div>
</footer>
<!-- End of Footer -->
</div>
<!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#" style="background: linear-gradient(135deg, #667eea, #764ba2); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
    <i class="fas fa-angle-up" style="color: #fff; font-size: 16px;"></i>
</a>

<!-- Original Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-radius: 20px 20px 0 0; border-bottom: none;">
                <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 600;">
                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                </h5>
                <button class="btn-close btn-close-white" type="button" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-question-circle" style="font-size: 3rem; color: #ffeaa7;"></i>
                </div>
                <p style="color: #4a5568; font-size: 16px;">คุณต้องการออกจากระบบหรือไม่?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button class="btn btn-secondary" type="button" data-dismiss="modal" style="border-radius: 12px; padding: 10px 25px; font-weight: 500;">ยกเลิก</button>
                <a class="btn btn-primary" href="<?php echo site_url('User/logout'); ?>" style="background: linear-gradient(135deg, #fd79a8, #fdcb6e); border: none; border-radius: 12px; padding: 10px 25px; font-weight: 500;">ออกจากระบบ</a>
            </div>
        </div>
    </div>
</div>

<!-- 🚨 REQUIRED: Session Warning Modals - สร้างแบบ dynamic จาก JS -->

<!-- 📚 REQUIRED: JavaScript Libraries -->
<!-- jQuery -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.min.js"></script> 

<!-- 🔧 REQUIRED: Session Manager (สำหรับเจ้าหน้าที่) -->
<script src="<?php echo base_url('asset/js/pri-session-manager.js'); ?>"></script>

<!-- 🚨 REQUIRED: Session Management Script สำหรับเจ้าหน้าที่ - แบบสั้น -->
<script>
    // ✅ กำหนด base_url
    window.base_url = '<?php echo base_url(); ?>';
    
    // ✅ เริ่มต้นระบบเมื่อหน้าโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📚 Document ready, initializing ADMIN session system...');
        
        // สร้าง modals ถ้ายังไม่มี
        if (typeof window.createAdminSessionModalsIfNeeded === 'function') {
            window.createAdminSessionModalsIfNeeded();
        }
        
        // เริ่มต้น Session Manager สำหรับเจ้าหน้าที่
        const sessionVars = {
            m_id: '<?php echo $this->session->userdata('m_id'); ?>',
            tenant_id: '<?php echo $this->session->userdata('tenant_id'); ?>',
            admin_id: '<?php echo $this->session->userdata('admin_id'); ?>',
            user_id: '<?php echo $this->session->userdata('user_id'); ?>',
            mp_id: '<?php echo $this->session->userdata('mp_id'); ?>',
            logged_in: '<?php echo $this->session->userdata('logged_in'); ?>',
            username: '<?php echo $this->session->userdata('username'); ?>'
        };
        
        // ตรวจสอบว่ามี session เจ้าหน้าที่หรือไม่
        const hasAdminSession = sessionVars.m_id || sessionVars.admin_id || sessionVars.user_id || 
                               (sessionVars.logged_in && !sessionVars.mp_id); // ไม่ใช่ประชาชน
        
        if (typeof window.initializeAdminSessionManager === 'function') {
            window.initializeAdminSessionManager(hasAdminSession);
        }
        
        // ตั้งค่า Event Listeners
        if (typeof window.setupAdminModalEventListeners === 'function') {
            window.setupAdminModalEventListeners();
        }

        // Setup scroll to top button
        setupScrollToTop();
    });

    // Setup scroll to top button
    function setupScrollToTop() {
        var scrollToTopBtn = document.querySelector('.scroll-to-top');
        if (scrollToTopBtn) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 100) {
                    scrollToTopBtn.style.display = 'flex';
                    scrollToTopBtn.style.opacity = '1';
                } else {
                    scrollToTopBtn.style.opacity = '0';
                    setTimeout(() => {
                        if (window.pageYOffset <= 100) {
                            scrollToTopBtn.style.display = 'none';
                        }
                    }, 300);
                }
            });
            
            scrollToTopBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Hover effects
            scrollToTopBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.05)';
                this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.6)';
            });
            
            scrollToTopBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.4)';
            });
        }
    }

    // 🧪 Quick Test Functions
    window.testAdminSessionWarning = function(type = '5min') {
        console.log(`🧪 Testing ADMIN ${type} warning...`);
        if (typeof window.showAdminSessionWarning === 'function') {
            window.showAdminSessionWarning(type);
        }
    };

    window.testAdminLogoutModal = function() {
        console.log('🧪 Testing ADMIN logout modal...');
        if (typeof window.showAdminLogoutModal === 'function') {
            window.showAdminLogoutModal();
        }
    };
</script>

</body>
</html>