<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Header Section -->
<div class="text-center pages-head">
    <span class="font-pages-head">ยืนยันบัญชี - <?php echo $this->session->userdata('tenant_name') ?: 'ระบบยืนยันตัวตน'; ?></span>
</div>

<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<style>
/* ============================================
   🎨 Verify Account Page - Fixed Layout
   ============================================ */

/* Base Layout */
.verify-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 60px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.verify-container {
    max-width: 600px;
    width: 100%;
    margin: 0 auto;
}

/* Card Styles */
.verify-card-modern {
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

/* Header Section */
.verify-header-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 35px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.verify-header-modern::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
    animation: headerGlow 5s ease-in-out infinite;
}

@keyframes headerGlow {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.4;
    }
    50% { 
        transform: translate(20px, 20px) scale(1.1);
        opacity: 0.7;
    }
}

/* Status Icon */
.verify-icon-wrapper {
    position: relative;
    z-index: 1;
    margin-bottom: 20px;
}

.verify-icon-circle {
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    padding: 10px;
}

.verify-icon-circle.success {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(76, 175, 80, 0.4);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
}

.verify-icon-circle.error {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(244, 67, 54, 0.4);
    box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3);
}

.verify-icon-circle i {
    font-size: 60px;
    color: white;
}

.verify-icon-circle img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* Header Text */
.verify-title-modern {
    font-size: 28px;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
}

.verify-subtitle-modern {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.95);
    position: relative;
    z-index: 1;
}

/* Content Section */
.verify-content-modern {
    padding: 35px 35px;
}

/* Message Box */
.verify-message-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 30px;
    border-left: 5px solid #667eea;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.verify-message-modern.success {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(52, 199, 89, 0.05) 100%);
    border-left-color: #4CAF50;
}

.verify-message-modern.error {
    background: linear-gradient(135deg, rgba(244, 67, 54, 0.1) 0%, rgba(255, 59, 48, 0.05) 100%);
    border-left-color: #f44336;
}

.verify-message-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.verify-message-title i {
    font-size: 24px;
}

.verify-message-title i.fa-check-circle {
    color: #4CAF50;
}

.verify-message-title i.fa-exclamation-triangle {
    color: #f44336;
}

.verify-message-text {
    font-size: 15px;
    line-height: 1.6;
    color: #6c757d;
    margin: 0;
}

/* Info Box - New Style */
.verify-info-box {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 14px;
    padding: 22px;
    margin: 25px 0;
    border: 2px solid #e9ecef;
}

.verify-info-text {
    font-size: 14px;
    line-height: 1.8;
    color: #2c3e50;
    margin: 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.verify-info-text i {
    font-size: 18px;
    color: #667eea;
    width: 20px;
    text-align: center;
}

.verify-info-text strong {
    color: #495057;
    min-width: 110px;
    font-size: 14px;
}

.verify-info-text .success-status {
    color: #4CAF50;
    font-weight: 600;
}

/* Action Buttons */
.verify-actions-modern {
    display: flex;
    gap: 12px;
    margin: 30px 0;
}

.verify-btn-modern {
    flex: 1;
    padding: 14px 28px;
    border: none;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.verify-btn-modern::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.verify-btn-modern:hover::before {
    width: 300px;
    height: 300px;
}

.verify-btn-modern i {
    font-size: 16px;
    position: relative;
    z-index: 1;
}

.verify-btn-modern span {
    position: relative;
    z-index: 1;
}

.verify-btn-primary-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.verify-btn-primary-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
}

.verify-btn-secondary-modern {
    background: white;
    color: #667eea;
    border: 2px solid #e9ecef;
}

.verify-btn-secondary-modern:hover {
    background: #f8f9fa;
    border-color: #667eea;
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

/* Countdown Box */
.verify-countdown-modern {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 2px solid #ffc107;
    border-radius: 16px;
    padding: 22px;
    text-align: center;
    margin: 30px 0;
}

.verify-countdown-label {
    font-size: 14px;
    color: #856404;
    font-weight: 600;
    margin-bottom: 12px;
}

.verify-countdown-timer {
    font-size: 42px;
    font-weight: 700;
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 12px;
}

.verify-countdown-note {
    font-size: 14px;
    color: #856404;
    font-weight: 500;
}

/* Footer Note */
.verify-footer-note {
    text-align: center;
    padding: 20px;
    border-top: 2px solid #e9ecef;
    margin-top: 30px;
}

.verify-footer-text {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.verify-footer-text i {
    font-size: 16px;
    color: #667eea;
}

/* Alert Messages */
.verify-alert-modern {
    max-width: 700px;
    margin: 0 auto 25px;
    padding: 16px 25px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    font-size: 14px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.verify-alert-modern i {
    font-size: 20px;
}

.verify-alert-modern.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 2px solid #c3e6cb;
}

.verify-alert-modern.error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border: 2px solid #f5c6cb;
}

/* Responsive Design */
@media (max-width: 768px) {
    .verify-page-wrapper {
        padding: 40px 15px;
    }

    .verify-header-modern {
        padding: 40px 25px;
    }

    .verify-icon-circle {
        width: 100px;
        height: 100px;
    }

    .verify-icon-circle i {
        font-size: 50px;
    }

    .verify-title-modern {
        font-size: 28px;
    }

    .verify-subtitle-modern {
        font-size: 16px;
    }

    .verify-content-modern {
        padding: 40px 25px;
    }

    .verify-message-modern {
        padding: 28px;
    }

    .verify-message-title {
        font-size: 22px;
    }

    .verify-message-text {
        font-size: 16px;
    }

    .verify-actions-modern {
        flex-direction: column;
    }

    .verify-countdown-timer {
        font-size: 48px;
    }

    .verify-info-text {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}

@media (max-width: 480px) {
    .verify-title-modern {
        font-size: 24px;
    }

    .verify-icon-circle {
        width: 90px;
        height: 90px;
    }

    .verify-icon-circle i {
        font-size: 45px;
    }

    .verify-message-modern {
        padding: 22px;
    }

    .verify-message-title {
        font-size: 20px;
        flex-direction: column;
        gap: 10px;
    }

    .verify-countdown-timer {
        font-size: 40px;
    }

    .verify-btn-modern {
        padding: 16px 28px;
        font-size: 16px;
    }
}
</style>

<!-- แสดง Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="verify-alert-modern success">
        <i class="fas fa-check-circle"></i>
        <span><?php echo $this->session->flashdata('success'); ?></span>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="verify-alert-modern error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?php echo $this->session->flashdata('error'); ?></span>
    </div>
<?php endif; ?>

<div class="verify-page-wrapper">
    <div class="verify-container">
        <div class="verify-card-modern">
            <?php if ($status === 'success'): ?>
                <!-- ✅ Success State -->
                <div class="verify-header-modern">
                    <div class="verify-icon-wrapper">
                        <div class="verify-icon-circle success">
                            <img src="<?php echo base_url('docs/logo.png'); ?>" width="90" height="90" alt="Organization Logo" style="border-radius: 50%; object-fit: cover;">
                        </div>
                    </div>
                    <h1 class="verify-title-modern">ยืนยันบัญชีสำเร็จ!</h1>
                    <p class="verify-subtitle-modern">บัญชีของคุณได้รับการยืนยันเรียบร้อยแล้ว</p>
                </div>

                <div class="verify-content-modern">
                    <!-- Message Box -->
                    <div class="verify-message-modern success">
                        <h2 class="verify-message-title">
                            <i class="fas fa-check-circle"></i>
                            <span>การยืนยันเสร็จสมบูรณ์</span>
                        </h2>
                        <p class="verify-message-text">
                            <?php echo isset($message) ? $message : 'บัญชีของคุณได้รับการยืนยันเรียบร้อยแล้ว คุณสามารถดำเนินการลงทะเบียนต่อได้ทันที'; ?>
                        </p>
                    </div>

                    <?php if (!empty($verified_email)): ?>
                    <!-- Details Info Box -->
                    <div class="verify-info-box">
                        <p class="verify-info-text">
                            <i class="fas fa-envelope"></i> 
                            <strong>อีเมลที่ยืนยัน:</strong> <?php echo htmlspecialchars($verified_email); ?>
                        </p>
                        <p class="verify-info-text">
                            <i class="fas fa-clock"></i> 
                            <strong>เวลาที่ยืนยัน:</strong> <?php echo date('d/m/Y H:i:s'); ?>
                        </p>
                        <p class="verify-info-text">
                            <i class="fas fa-shield-alt"></i> 
                            <strong>สถานะบัญชี:</strong> <span class="success-status">ยืนยันแล้ว</span>
                        </p>
                        <p class="verify-info-text">
                            <i class="fas fa-lock"></i> 
                            <strong>ความปลอดภัย:</strong> บัญชีของคุณได้รับการปกป้อง
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- Countdown Box -->
                    <div class="verify-countdown-modern">
                        <div class="verify-countdown-label">คุณจะถูกนำไปยังหน้าลงทะเบียนอัตโนมัติใน</div>
                        <div class="verify-countdown-timer" id="countdown">5</div>
                        <div class="verify-countdown-note">วินาที</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="verify-actions-modern">
                        <a href="<?php echo isset($redirect_url) ? $redirect_url : site_url('Auth_public_mem/register_form'); ?>" 
                           class="verify-btn-modern verify-btn-primary-modern">
                            <i class="fas fa-arrow-right"></i>
                            <span>ดำเนินการลงทะเบียนต่อ</span>
                        </a>
                        <a href="<?php echo site_url(); ?>" 
                           class="verify-btn-modern verify-btn-secondary-modern">
                            <i class="fas fa-home"></i>
                            <span>กลับหน้าหลัก</span>
                        </a>
                    </div>

                    <!-- Footer Note -->
                    <div class="verify-footer-note">
                        <p class="verify-footer-text">
                            <i class="fas fa-info-circle"></i>
                            <span>หากมีข้อสงสัยเพิ่มเติม กรุณาติดต่อเจ้าหน้าที่</span>
                        </p>
                    </div>
                </div>

            <?php else: ?>
                <!-- ❌ Error State -->
                <div class="verify-header-modern">
                    <div class="verify-icon-wrapper">
                        <div class="verify-icon-circle error">
                            <img src="<?php echo base_url('docs/logo.png'); ?>" width="90" height="90" alt="Organization Logo" style="border-radius: 50%; object-fit: cover;">
                        </div>
                    </div>
                    <h1 class="verify-title-modern">ไม่สามารถยืนยันบัญชีได้</h1>
                    <p class="verify-subtitle-modern">เกิดข้อผิดพลาดในการยืนยันบัญชี</p>
                </div>

                <div class="verify-content-modern">
                    <!-- Message Box -->
                    <div class="verify-message-modern error">
                        <h2 class="verify-message-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>เกิดข้อผิดพลาด</span>
                        </h2>
                        <p class="verify-message-text">
                            <?php echo isset($message) ? $message : 'ไม่สามารถยืนยันบัญชีได้ กรุณาลองใหม่อีกครั้ง'; ?>
                        </p>
                    </div>

                    <!-- Error Info Box -->
                    <div class="verify-info-box">
                        <p class="verify-info-text">
                            <i class="fas fa-info-circle"></i> 
                            <strong></strong> ลิงก์อาจหมดอายุหรือถูกใช้งานไปแล้ว
                        </p>
                        <p class="verify-info-text">
                            <i class="fas fa-redo"></i> 
                            <strong></strong> กรุณาขอลิงก์ยืนยันใหม่อีกครั้ง
                        </p>
                        <p class="verify-info-text">
                            <i class="fas fa-clock"></i> 
                            <strong></strong> ลิงก์ยืนยันมีอายุ 10 นาที
                        </p>
                        <p class="verify-info-text">
                            <i class="fas fa-question-circle"></i> 
                            <strong></strong> ตรวจสอบให้แน่ใจว่าคุณคลิกลิงก์ที่ถูกต้อง
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="verify-actions-modern">
                        <a href="<?php echo site_url('Auth_public_mem/register_form'); ?>" 
                           class="verify-btn-modern verify-btn-primary-modern">
                            <i class="fas fa-redo"></i>
                            <span>ขอลิงก์ยืนยันใหม่</span>
                        </a>
                        <a href="<?php echo site_url(); ?>" 
                           class="verify-btn-modern verify-btn-secondary-modern">
                            <i class="fas fa-home"></i>
                            <span>กลับหน้าหลัก</span>
                        </a>
                    </div>

                    <!-- Footer Note -->
                    <div class="verify-footer-note">
                        <p class="verify-footer-text">
                            <i class="fas fa-lightbulb"></i>
                            <span>หากยังพบปัญหา กรุณาติดต่อเจ้าหน้าที่เพื่อขอความช่วยเหลือ</span>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($status === 'success'): ?>
        // Auto redirect countdown
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        const redirectUrl = '<?php echo isset($redirect_url) ? $redirect_url : site_url("Auth_public_mem/register_form"); ?>';

        const countdownInterval = setInterval(function() {
            seconds--;
            if (countdownElement) {
                countdownElement.textContent = seconds;
            }

            if (seconds <= 0) {
                clearInterval(countdownInterval);
                window.location.href = redirectUrl;
            }
        }, 1000);

        // Allow user to cancel auto redirect by clicking countdown
        const countdownBox = document.querySelector('.verify-countdown-modern');
        if (countdownBox) {
            countdownBox.style.cursor = 'pointer';
            countdownBox.addEventListener('click', function() {
                clearInterval(countdownInterval);
                this.innerHTML = '<div class="verify-countdown-note" style="padding: 15px; color: #6c757d;">การเปลี่ยนหน้าอัตโนมัติถูกยกเลิก</div>';
            });
        }
    <?php endif; ?>
});
</script>