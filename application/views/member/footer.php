<!-- Back to Top Button -->
<button id="backToTop" class="back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- 🚨 Session Warning Modals (ซ่อนไว้ทั้งหมด) -->
<div class="modal fade" id="adminSessionWarning5Min" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true" style="display: none !important;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แจ้งเตือนการหมดเวลา (เจ้าหน้าที่)</h5>
            </div>
            <div class="modal-body">
                <p>ระบบจะหมดเวลาใช้งานในอีก 5 นาที</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminSessionWarning1Min" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true" style="display: none !important;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แจ้งเตือนด่วน! (เจ้าหน้าที่)</h5>
            </div>
            <div class="modal-body">
                <p>ระบบจะหมดเวลาใช้งานในอีก 1 นาที!</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminSessionLogoutModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none !important;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">กำลังนำท่านกลับสู่ระบบล็อกอิน</h5>
            </div>
            <div class="modal-body">
                <p>Session หมดอายุแล้ว</p>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal (ซ่อนไว้) -->
<div id="errorModal" class="error-modal" style="display: none !important;">
    <div class="error-modal-content">
        <i class="fas fa-exclamation-circle error-icon"></i>
        <h3 class="error-title">ไม่สามารถเข้าถึงได้</h3>
        <p class="error-message">ท่านไม่มีสิทธิ์เข้าใช้งานระบบนี้ โปรดติดต่อผู้ดูแลระบบ</p>
        <button class="error-close-btn" onclick="closeErrorModal()">ตกลง</button>
    </div>
</div>

<!-- 📚 JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- 🔧 Session Manager (จาก pri-session-manager.js) -->
<script src="<?php echo base_url('asset/js/pri-session-manager.js'); ?>"></script>

<script>
// 🚨 Session Management Script (ซ่อน Modal ทั้งหมด)
document.addEventListener('DOMContentLoaded', function() {
   // console.log('📚 Footer Session Manager initializing (NO MODALS)...');
    
    // ✅ กำหนด base_url
    window.base_url = '<?php echo base_url(); ?>';
    
    // 🚫 Override Modal Functions ให้ไม่แสดง Modal
    window.showAdminSessionWarning = function(type) {
     //   console.log(`⚠️ Session Warning ${type} triggered but MODAL HIDDEN`);
      //  console.log('💡 Session system working but no modal shown');
        
        // แสดง console message แทน modal
        if (type === '5min') {
            console.log('🕐 5 minutes warning - Session will expire soon');
        } else if (type === '1min') {
            console.log('🚨 1 minute warning - Session will expire very soon!');
        } else if (type === 'expired') {
            console.log('⏰ Session expired - Should redirect to login');
            // ยังคงทำการ redirect เมื่อ session หมดอายุ
            setTimeout(() => {
                window.location.href = window.base_url + 'User/logout';
            }, 2000);
        }
        
        // แสดง toast notification แทน modal (ถ้ามี)
        if (typeof window.showToast === 'function') {
            if (type === '5min') {
                window.showToast('⚠️ Session จะหมดอายุในอีก 5 นาที', 'warning', 3000);
            } else if (type === '1min') {
                window.showToast('🚨 Session จะหมดอายุในอีก 1 นาที!', 'danger', 5000);
            }
        }
        
        return false; // ไม่แสดง modal
    };
    
    window.showAdminLogoutModal = function() {
      //  console.log('🚪 Logout Modal triggered but HIDDEN - redirecting...');
      //  console.log('💡 Redirecting to logout page...');
        
        // แสดง toast notification แทน modal
        if (typeof window.showToast === 'function') {
            window.showToast('🚪 Session หมดอายุ กำลังออกจากระบบ...', 'info', 2000);
        }
        
        // ยังคงทำการ redirect
        setTimeout(() => {
            window.location.href = window.base_url + 'User/logout';
        }, 2000);
        
        return false; // ไม่แสดง modal
    };
    
    // 🚫 Override Public Session Warning (ถ้ามี)
    window.showPublicSessionWarning = function(type) {
      //  console.log(`⚠️ Public Session Warning ${type} triggered but MODAL HIDDEN`);
        
        if (typeof window.showToast === 'function') {
            if (type === '5min') {
                window.showToast('⚠️ Session จะหมดอายุในอีก 5 นาที (Public)', 'warning', 3000);
            } else if (type === '1min') {
                window.showToast('🚨 Session จะหมดอายุในอีก 1 นาที! (Public)', 'danger', 5000);
            }
        }
        
        return false; // ไม่แสดง modal
    };
    
    window.showPublicLogoutModal = function() {
      //  console.log('🚪 Public Logout Modal triggered but HIDDEN - redirecting...');
        
        if (typeof window.showToast === 'function') {
            window.showToast('🚪 Session หมดอายุ กำลังออกจากระบบ... (Public)', 'info', 2000);
        }
        
        setTimeout(() => {
            window.location.href = window.base_url + 'Auth_public_mem/logout';
        }, 2000);
        
        return false; // ไม่แสดง modal
    };
    
    // 🚫 Override SweetAlert Warnings
    window.showAdminSweetAlertWarning = function(type) {
        // console.log(`⚠️ SweetAlert Warning ${type} triggered but HIDDEN`);
        
        if (typeof window.showToast === 'function') {
            window.showToast(`⚠️ Session Warning ${type} (Silent Mode)`, 'warning', 3000);
        }
        
        return false; // ไม่แสดง SweetAlert
    };
    
    window.showPublicSweetAlertWarning = function(type) {
       // console.log(`⚠️ Public SweetAlert Warning ${type} triggered but HIDDEN`);
        
        if (typeof window.showToast === 'function') {
            window.showToast(`⚠️ Public Session Warning ${type} (Silent Mode)`, 'warning', 3000);
        }
        
        return false; // ไม่แสดง SweetAlert
    };
    
    // 🛡️ ป้องกัน Modal แสดงเองจาก Library อื่น
    const originalBootstrapModalShow = bootstrap.Modal.prototype.show;
    bootstrap.Modal.prototype.show = function() {
        const modalElement = this._element;
        const modalId = modalElement.id;
        
        // ถ้าเป็น session modal ให้ไม่แสดง
        if (modalId && modalId.includes('Session')) {
            console.log(`🚫 Blocked modal: ${modalId}`);
            return false;
        }
        
        // แสดง modal อื่นๆ ปกติ
        return originalBootstrapModalShow.call(this);
    };
    
    // 🛡️ ป้องกัน SweetAlert แสดงเอง
    if (typeof Swal !== 'undefined') {
        const originalSwalFire = Swal.fire;
        Swal.fire = function(...args) {
            const config = args[0];
            
            // ตรวจสอบว่าเป็น session warning หรือไม่
            if (config && (
                (typeof config.title === 'string' && config.title.includes('แจ้งเตือน')) ||
                (typeof config.text === 'string' && config.text.includes('ระบบจะหมดเวลา'))
            )) {
                console.log('🚫 Blocked SweetAlert session warning');
                return Promise.resolve({ isConfirmed: true, isDismissed: false });
            }
            
            // แสดง SweetAlert อื่นๆ ปกติ
            return originalSwalFire.apply(this, args);
        };
    }
    
    // ✅ เริ่มต้น Session Manager แต่ไม่แสดง Modal
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
    
    if (hasAdminSession && typeof window.initializeAdminSessionManager === 'function') {
      //  console.log('✅ Initializing Admin Session Manager (Silent Mode)');
        window.initializeAdminSessionManager(hasAdminSession);
    }
    
    // เริ่มต้น Public Session Manager ถ้ามี
    const hasPublicSession = sessionVars.mp_id || (sessionVars.logged_in && !sessionVars.m_id);
    if (hasPublicSession && typeof window.initializePublicSessionManager === 'function') {
       // console.log('✅ Initializing Public Session Manager (Silent Mode)');
        window.initializePublicSessionManager(hasPublicSession);
    }
    
    // เริ่มต้น Back to Top
    initializeBackToTop();
    
    // Setup error prevention
    setupErrorPrevention();
    
   // console.log('✅ Session Management initialized in SILENT MODE');
   // console.log('📊 Session tracking: ACTIVE');
   // console.log('📱 Modals: HIDDEN');
   // console.log('🔄 Keep alive: WORKING');
   // console.log('🚪 Auto logout: ENABLED');
});

// Error Modal Functions (แต่ไม่แสดง)
function showAccessDeniedError() {
    console.log('🚫 Access denied error triggered but HIDDEN');
    
    if (typeof window.showToast === 'function') {
        window.showToast('❌ ไม่มีสิทธิ์เข้าใช้งานระบบนี้', 'danger', 3000);
    }
    
    return false; // ไม่แสดง modal
}

function closeErrorModal() {
    console.log('ℹ️ Close error modal called (but modal is hidden)');
    return false;
}

// Error Prevention Setup
function setupErrorPrevention() {
    // console.log('Setting up error prevention...');
    
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            console.log('Image not found:', this.src);
            this.style.display = 'none';
        });
    });
}

// Back to Top Functionality
function initializeBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    if (!backToTopBtn) return;

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });

    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}



window.checkSessionStatus = function() {
   // console.log('=== SESSION STATUS CHECK ===');
    
    if (window.SessionManager && typeof window.SessionManager.getState === 'function') {
        const state = window.SessionManager.getState();
       //console.log('Admin Session State:', state);
       // console.log('Time since last activity:', Math.round(state.timeSinceUserActivity / 1000), 'seconds');
       // console.log('Remaining time:', Math.round(state.remainingTime / 1000), 'seconds');
       // console.log('Session is active:', state.isInitialized);
    }
    
    if (window.PublicSessionManager && typeof window.PublicSessionManager.getState === 'function') {
        const state = window.PublicSessionManager.getState();
       // console.log('Public Session State:', state);
    }
    
    //console.log('=== END STATUS CHECK ===');
};

// 🔧 Force Enable Modals (ถ้าต้องการเปิดใช้ modal กลับมา)
window.enableSessionModals = function() {
   // console.log('🔓 Enabling session modals...');
    
    // ลบการซ่อน modal ด้วย CSS
    const modals = document.querySelectorAll('[id*="Session"]');
    modals.forEach(modal => {
        modal.style.display = '';
    });
    
    // Reset functions ให้แสดง modal ปกติ
    delete window.showAdminSessionWarning;
    delete window.showAdminLogoutModal;
    delete window.showPublicSessionWarning;
    delete window.showPublicLogoutModal;
    
    console.log('✅ Session modals enabled - reload page to take effect');
};
</script>

<!-- 🎨 Styles -->
<style>
/* ✅ Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.back-to-top:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.back-to-top i {
    font-size: 1.2rem;
}

/* 🚫 ซ่อน Modal ทั้งหมดด้วย CSS */
.modal[id*="Session"],
.modal[id*="session"],
.error-modal,
#errorModal,
#adminSessionWarning5Min,
#adminSessionWarning1Min,
#adminSessionLogoutModal,
#publicSessionWarning5Min,
#publicSessionWarning1Min,
#publicSessionLogoutModal {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    z-index: -9999 !important;
}

/* 🚫 ซ่อน Modal Backdrop */
.modal-backdrop[class*="session"],
.modal-backdrop {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

/* 🚫 Reset Body Modal State */
body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
}

/* ✅ Toast Notifications (ถ้ามี) */
.toast {
    z-index: 9999 !important;
}

/* 🎨 Toast Animations */
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.toast {
    animation: slideInRight 0.3s ease-out !important;
}

.toast.bg-success { 
    background: linear-gradient(135deg, #88d8c0, #6bb6ff) !important; 
}

.toast.bg-warning { 
    background: linear-gradient(135deg, #ffeaa7, #fab1a0) !important; 
}

.toast.bg-danger { 
    background: linear-gradient(135deg, #fd79a8, #fdcb6e) !important; 
}

.toast.bg-info { 
    background: linear-gradient(135deg, #74b9ff, #0984e3) !important; 
}

/* 🎯 Debug Info (แสดงใน Console แทน Modal) */
.session-debug {
    position: fixed;
    top: 10px;
    left: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px;
    border-radius: 5px;
    font-family: monospace;
    font-size: 12px;
    z-index: 999;
    display: none; /* ซ่อนไว้ */
}
</style>

</body>
</html>