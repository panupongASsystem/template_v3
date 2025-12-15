<!-- Footer -->
<footer class="footer mt-auto py-4 bg-light border-top">
    <div class="container">
        <!-- Divider -->
        <hr class="my-4">
        <!-- Copyright and Policies -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="mb-2 mb-md-0 text-muted small">© <?php echo date('Y'); ?> <a href="https://www.assystem.co.th" target="_blank" class="company-link">บริษัท เอเอส ซิสเต็ม จำกัด</a></p>
            <div class="d-flex policy-links">
                <a href="#" class="me-3 small">นโยบายความเป็นส่วนตัว</a>
                <a href="#" class="me-3 small">นโยบายคุกกี้</a>
                <a href="#" class="small">เงื่อนไขการใช้งาน</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-primary-custom btn-sm rounded-circle back-to-top">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- 🚨 REQUIRED: Session Warning Modals - สร้างแบบ dynamic จาก JS -->

<!-- 📚 REQUIRED: JavaScript Libraries -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- 🔧 REQUIRED: Public Session Manager -->
<script src="<?php echo base_url('asset/js/pri-session-manager.js'); ?>"></script>

<!-- 🚨 REQUIRED: Session Management Script - แบบสั้น -->
<script>
    // ✅ กำหนด base_url
    window.base_url = '<?php echo base_url(); ?>';
    
    // ✅ เริ่มต้นระบบเมื่อหน้าโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function() {
       // console.log('📚 Document ready, initializing PUBLIC session system...');
        
        // สร้าง modals ถ้ายังไม่มี
        if (typeof window.createSessionModalsIfNeeded === 'function') {
            window.createSessionModalsIfNeeded();
        }
        
        // เริ่มต้น Session Manager
        const isLoggedIn = <?php echo $this->session->userdata('mp_id') ? 'true' : 'false'; ?>;
        if (typeof window.initializePublicSessionManager === 'function') {
            window.initializePublicSessionManager(isLoggedIn);
        }
        
        // ตั้งค่า Event Listeners
        if (typeof window.setupModalEventListeners === 'function') {
            window.setupModalEventListeners();
        }

        // Back to Top Button
        setupBackToTopButton();
    });

    // Back to Top Button Function
    function setupBackToTopButton() {
        var backToTopBtn = document.getElementById('backToTop');
        if (backToTopBtn) {
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 300) {
                    backToTopBtn.style.display = 'block';
                } else {
                    backToTopBtn.style.display = 'none';
                }
            });
            backToTopBtn.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    }

    // 🧪 Quick Test Functions
    window.testSessionWarning = function(type = '5min') {
        console.log(`🧪 Testing ${type} warning...`);
        if (typeof window.showSessionWarning === 'function') {
            window.showSessionWarning(type);
        }
    };

    window.testLogoutModal = function() {
        console.log('🧪 Testing logout modal...');
        if (typeof window.showLogoutModal === 'function') {
            window.showLogoutModal();
        }
    };
</script>
</body>
</html>