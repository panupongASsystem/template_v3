</div> <!-- End container-fluid -->
</div> <!-- End page-wrapper -->

<!-- Footer -->
<footer class="footer mt-auto">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="footer-info">
                    <span class="text-muted">
                        &copy; <?php echo date('Y'); ?> 
                        <a href="https://www.assystem.co.th" target="_blank" rel="noopener noreferrer">
    <strong>บริษัท เอเอส ซิสเต็ม จำกัด</strong>
</a>
                       
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="footer-links text-md-end">
                    <span class="text-muted me-3">
                        <i class="fas fa-clock me-1"></i>
                        อัปเดตล่าสุด: <?php echo date('d/m/Y H:i:s'); ?>
                    </span>
                   
                </div>
            </div>
        </div>
        
        <!-- System Status Indicator -->
        <div class="row mt-3">
    <div class="col-12">
        <div class="system-status d-flex align-items-center justify-content-center">
            <span class="status-indicator online me-3" style="width: 16px; height: 16px;"></span>
            <span class="text-muted h6 mb-0">ระบบทำงานปกติ</span>
        </div>
    </div>
</div>
    </div>
</footer>

<!-- Alert Container -->
<div class="alert-container" id="alertContainer"></div>

<!-- Back to Top Button -->
<button class="btn-back-to-top" id="backToTop" title="กลับไปด้านบน">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- ✅ JavaScript Libraries - ปรับลำดับใหม่ -->
<!-- jQuery - ต้องโหลดก่อนเสมอ -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" 
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
        crossorigin="anonymous"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
        crossorigin="anonymous"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.min.js"></script>

<!-- ✅ เช็คว่า jQuery โหลดสำเร็จแล้วก่อนดำเนินการต่อ -->
<script>
// ตรวจสอบ jQuery
if (typeof jQuery === 'undefined') {
    console.error('jQuery ไม่ได้ถูกโหลด กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต');
    alert('เกิดข้อผิดพลาดในการโหลด jQuery กรุณารีเฟรชหน้า');
} else {
   // console.log('✅ jQuery โหลดสำเร็จ - Version:', jQuery.fn.jquery);
}

// ตรวจสอบ Bootstrap
if (typeof bootstrap === 'undefined') {
    console.warn('Bootstrap JS ไม่ได้ถูกโหลด');
}

// ✅ ใช้ jQuery โหลดแบบปลอดภัย
jQuery(document).ready(function($) {
    //console.log('✅ jQuery Document Ready - เริ่มต้นระบบ');

    // Initialize tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize popovers
    if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Back to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#backToTop').fadeIn();
        } else {
            $('#backToTop').fadeOut();
        }
    });

    $('#backToTop').click(function() {
        $('html, body').animate({scrollTop: 0}, 600);
        return false;
    });

    // Loading overlay functions
    window.showLoading = function() {
        $('#loadingOverlay').fadeIn(300);
    };

    window.hideLoading = function() {
        $('#loadingOverlay').fadeOut(300);
    };

    // Alert functions
    window.showAlert = function(message, type = 'info', duration = 5000) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert" role="alert">
                <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('#alertContainer').append(alertHtml);
        
        // Auto remove after duration
        setTimeout(function() {
            $('.custom-alert').first().alert('close');
        }, duration);
    };

    function getAlertIcon(type) {
        switch(type) {
            case 'success': return 'check-circle';
            case 'danger': return 'exclamation-triangle';
            case 'warning': return 'exclamation-circle';
            case 'info': return 'info-circle';
            default: return 'info-circle';
        }
    }

    // Auto-refresh timestamp
    setInterval(function() {
        const now = new Date();
        const timestamp = now.toLocaleDateString('th-TH') + ' ' + 
                         now.toLocaleTimeString('th-TH');
        $('.footer-links .text-muted').first().html(
            '<i class="fas fa-clock me-1"></i>อัปเดตล่าสุด: ' + timestamp
        );
    }, 60000); // Update every minute

    // Navbar active link highlighting
    $('.navbar-nav .nav-link').on('click', function() {
        $('.navbar-nav .nav-link').removeClass('active');
        $(this).addClass('active');
    });

    // Auto-close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.navbar').length) {
            $('.navbar-collapse').collapse('hide');
        }
    });

    // Form validation enhancement
    $('form').on('submit', function() {
        const form = this;
        if (form.checkValidity()) {
            if (typeof showLoading === 'function') {
                showLoading();
            }
        }
    });

    // AJAX setup
    $.ajaxSetup({
        beforeSend: function() {
            if (typeof showLoading === 'function') {
                showLoading();
            }
        },
        complete: function() {
            if (typeof hideLoading === 'function') {
                hideLoading();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            if (typeof showAlert === 'function') {
                showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง', 'danger');
            }
        }
    });

    // Performance monitoring
    if ('performance' in window) {
        window.addEventListener('load', function() {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
           // console.log('Page Load Time:', loadTime + 'ms');
        });
    }

    // Setup scroll to top button
    setupScrollToTop();
});

// ✅ Global utility functions (ไม่ต้องรอ jQuery)
window.formatNumber = function(number, decimals = 0) {
    return new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
};

window.formatCurrency = function(amount, currency = 'THB') {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: currency
    }).format(amount);
};

window.formatDate = function(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    return new Date(date).toLocaleDateString('th-TH', {...defaultOptions, ...options});
};

window.formatDateTime = function(datetime) {
    return new Date(datetime).toLocaleString('th-TH');
};

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

// Browser compatibility checks
if (!window.fetch) {
    console.warn('Fetch API not supported. Consider using a polyfill.');
}

if (!window.Promise) {
    console.warn('Promise not supported. Consider using a polyfill.');
}
</script>

<!-- ✅ Custom JavaScript - โหลดหลัง jQuery -->
<script>
    <?php $this->load->view('reports/main_js'); ?>
</script>

<?php if (isset($page_scripts)): ?>
<!-- Page-specific scripts -->
<?php echo $page_scripts; ?>
<?php endif; ?>

<!-- ✅ Session Management Script - โหลดหลัง jQuery -->
<script src="<?php echo base_url('asset/js/pri-session-manager.js'); ?>"></script>

<script>
// ✅ Session Management (ใช้ jQuery อย่างปลอดภัย)
(function() {
    'use strict';
    
    // กำหนด base_url
    window.base_url = '<?php echo base_url(); ?>';
    
    // รอให้ jQuery โหลดเสร็จก่อน
    function initSessionSystem() {
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery ยังไม่พร้อม กำลังลองใหม่...');
            setTimeout(initSessionSystem, 100);
            return;
        }
        
        //console.log('📚 เริ่มต้นระบบ Session Management...');
        
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
                               (sessionVars.logged_in && !sessionVars.mp_id);
        
        if (typeof window.initializeAdminSessionManager === 'function') {
            window.initializeAdminSessionManager(hasAdminSession);
        }
        
        // ตั้งค่า Event Listeners
        if (typeof window.setupAdminModalEventListeners === 'function') {
            window.setupAdminModalEventListeners();
        }
    }
    
    // เริ่มต้นระบบ
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSessionSystem);
    } else {
        initSessionSystem();
    }
    
    // ✅ เริ่มต้น Reports Index หลัง jQuery โหลดเสร็จ
    function initReportsIndex() {
        if (typeof window.reportsIndex === 'object' && typeof window.reportsIndex.init === 'function') {
            window.reportsIndex.init();
        }
    }
    
    // รอ jQuery พร้อมก่อนเรียก Reports Index
    function waitForjQueryThenInitReports() {
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function() {
                initReportsIndex();
            });
        } else {
            setTimeout(waitForjQueryThenInitReports, 50);
        }
    }
    
    waitForjQueryThenInitReports();
    
    // 🧪 Quick Test Functions
    window.testAdminSessionWarning = function(type = '5min') {
        //console.log(`🧪 Testing ADMIN ${type} warning...`);
        if (typeof window.showAdminSessionWarning === 'function') {
            window.showAdminSessionWarning(type);
        }
    };

    window.testAdminLogoutModal = function() {
       // console.log('🧪 Testing ADMIN logout modal...');
        if (typeof window.showAdminLogoutModal === 'function') {
            window.showAdminLogoutModal();
        }
    };
})();
</script>

</body>
</html>