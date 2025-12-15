<div class="text-center pages-head">
    <span class="font-pages-head">แบบประเมินความพึงพอใจการให้บริการ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
/* Thank You Page Styles */
:root {
    --primary-color: #007AFF;
    --secondary-color: #5856D6;
    --success-color: #34C759;
    --warning-color: #FF9500;
    --error-color: #FF3B30;
    --text-primary: #1D1D1F;
    --text-secondary: #86868B;
    --background-primary: #F5F5F7;
    --background-secondary: #FFFFFF;
    --border-color: #D2D2D7;
    --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.04);
    --shadow-medium: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-heavy: 0 8px 32px rgba(0, 0, 0, 0.12);
    --border-radius: 12px;
    --border-radius-large: 20px;
}

* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #F5F5F7 0%, #FAFAFA 100%);
    color: var(--text-primary);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    line-height: 1.5;
}

.thank-you-container {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

.thank-you-card {
    background: var(--background-secondary);
    border-radius: var(--border-radius-large);
    padding: 60px 40px;
    text-align: center;
    box-shadow: var(--shadow-heavy);
    max-width: 600px;
    width: 100%;
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.thank-you-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--success-color), var(--primary-color), var(--secondary-color));
}

.success-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 30px;
    background: linear-gradient(135deg, var(--success-color), #28A745);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    animation: successPulse 2s ease-in-out infinite;
}

.success-icon::after {
    content: '✓';
    font-size: 3rem;
    color: white;
    font-weight: bold;
    animation: checkmark 0.6s ease-in-out;
}

@keyframes successPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes checkmark {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}

.organization-info {
    margin: 40px 0;
    padding: 30px;
    background: linear-gradient(135deg, rgba(52, 199, 89, 0.05), rgba(0, 122, 255, 0.05));
    border-radius: var(--border-radius);
    border: 1px solid rgba(52, 199, 89, 0.1);
}

.org-logo {
    width: 162px;
    height: 162px;
    margin: 0 auto 20px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: var(--shadow-medium);
    border: 3px solid var(--success-color);
}

.org-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.org-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
    background: linear-gradient(135deg, var(--success-color), var(--primary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.org-address {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
    margin-bottom: 20px;
}



.thank-you-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--success-color);
    margin-bottom: 20px;
    background: linear-gradient(135deg, var(--success-color), #28A745);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.thank-you-message {
    font-size: 1.25rem;
    color: var(--text-secondary);
    margin-bottom: 40px;
    line-height: 1.6;
}

.thank-you-details {
    background: var(--background-primary);
    border-radius: var(--border-radius);
    padding: 25px;
    margin: 30px 0;
    border: 1px solid var(--border-color);
}

.thank-you-details h4 {
    color: var(--text-primary);
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.thank-you-details p {
    color: var(--text-secondary);
    margin: 8px 0;
    font-size: 1rem;
}

.action-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 40px;
}

.btn {
    padding: 14px 28px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: var(--shadow-light);
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-heavy);
}

.btn-secondary {
    background: var(--background-secondary);
    color: var(--text-primary);
    border: 2px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--background-primary);
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.alert {
    padding: 16px 20px;
    margin-bottom: 24px;
    border-radius: var(--border-radius);
    border: none;
    box-shadow: var(--shadow-light);
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #E8F5E8 0%, #F0FFF0 100%);
    color: var(--success-color);
    border: 1px solid rgba(52, 199, 89, 0.2);
}

.alert-error {
    background: linear-gradient(135deg, #FFE5E5 0%, #FFF0F0 100%);
    color: var(--error-color);
    border: 1px solid rgba(255, 59, 48, 0.2);
}

.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.floating-element {
    position: absolute;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.floating-element:nth-child(1) {
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.floating-element:nth-child(2) {
    top: 20%;
    right: 10%;
    animation-delay: 1s;
}

.floating-element:nth-child(3) {
    bottom: 20%;
    left: 15%;
    animation-delay: 2s;
}

.floating-element:nth-child(4) {
    bottom: 10%;
    right: 20%;
    animation-delay: 3s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-10px) rotate(5deg); }
    50% { transform: translateY(0px) rotate(0deg); }
    75% { transform: translateY(-5px) rotate(-5deg); }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .thank-you-card {
        padding: 40px 25px;
        margin: 20px;
    }

    .thank-you-title {
        font-size: 2rem;
    }

    .thank-you-message {
        font-size: 1.125rem;
    }

    .organization-info {
        padding: 20px;
    }

    .org-name {
        font-size: 1.25rem;
    }

    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 280px;
        justify-content: center;
    }

    .org-logo {
        width: 120px;
        height: 120px;
    }


}
</style>

<!-- แสดง Flash Messages -->
<?php if (!empty($success_message)): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<div class="thank-you-container">
    <div class="thank-you-card">
        <!-- Floating Background Elements -->
        <div class="floating-elements">
            <div class="floating-element">
                <i class="fas fa-star" style="font-size: 24px; color: var(--success-color);"></i>
            </div>
            <div class="floating-element">
                <i class="fas fa-heart" style="font-size: 20px; color: var(--primary-color);"></i>
            </div>
            <div class="floating-element">
                <i class="fas fa-thumbs-up" style="font-size: 22px; color: var(--secondary-color);"></i>
            </div>
            <div class="floating-element">
                <i class="fas fa-smile" style="font-size: 26px; color: var(--warning-color);"></i>
            </div>
        </div>

        <!-- Success Icon -->
        <div class="success-icon"></div>

        <!-- Thank You Message -->
        <h1 class="thank-you-title">
            <i class="fas fa-heart"></i> ขอบคุณมากครับ!
        </h1>
        
        <p class="thank-you-message">
            ท่านได้ส่งแบบประเมินความพึงพอใจเรียบร้อยแล้ว<br>
            ข้อมูลของท่านจะถูกนำไปใช้เพื่อพัฒนาการให้บริการให้ดียิ่งขึ้น
        </p>

        <!-- Organization Info -->
        <div class="organization-info">
            <div class="org-logo">
                <img src="<?php echo base_url('docs/logo.png'); ?>" width="162" height="162" alt="Organization Logo">
            </div>
            <div class="org-name">
                <span class="font-link" style="white-space: nowrap; font-size: clamp(1rem, 4vw, 1.5rem); max-width: 100%; overflow: hidden; text-overflow: ellipsis; display: inline-block;"><?php echo get_config_value('fname'); ?></span>
            </div>
            <div class="org-address">
                <span class="font-link2" style="color: #333; font-size: 1em;"><?php echo get_config_value('address'); ?> ตำบล<?php echo get_config_value('subdistric'); ?> อำเภอ<?php echo get_config_value('district'); ?> จังหวัด<?php echo get_config_value('province'); ?> รหัสไปรษณีย์ <?php echo get_config_value('zip_code'); ?></span>
            </div>
        </div>

        <!-- Thank You Details -->
        <div class="thank-you-details">
            <h4>
                <i class="fas fa-info-circle"></i>
                รายละเอียดการส่งแบบประเมิน
            </h4>
            <p><i class="fas fa-calendar-alt"></i> <strong>วันที่ส่ง:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
            <p><i class="fas fa-clipboard-check"></i> <strong>สถานะ:</strong> บันทึกเรียบร้อยแล้ว</p>
            <p><i class="fas fa-shield-alt"></i> <strong>ความปลอดภัย:</strong> ข้อมูลของท่านได้รับการปกป้องอย่างเข้มงวด</p>
            <p><i class="fas fa-chart-line"></i> <strong>การใช้ประโยชน์:</strong> ข้อมูลจะถูกนำไปวิเคราะห์เพื่อพัฒนาบริการ</p>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?php echo base_url(); ?>" class="btn btn-primary">
                <i class="fas fa-home"></i>
                กลับสู่หน้าหลัก
            </a>
            <a href="<?php echo site_url('assessment'); ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i>
                ประเมินใหม่อีกครั้ง
            </a>
        </div>

        <!-- Additional Info -->
        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border-color);">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                <i class="fas fa-lightbulb"></i>
                หากท่านมีข้อสงสัยเพิ่มเติม สามารถติดต่อเราได้ที่ 
                <?php if (!empty(get_config_value('phone_1'))): ?>
                    <a href="tel:<?php echo get_config_value('phone_1'); ?>" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                        <?php echo get_config_value('phone_1'); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty(get_config_value('email_1'))): ?>
                    หรือ <a href="mailto:<?php echo get_config_value('email_1'); ?>" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                        <?php echo get_config_value('email_1'); ?>
                    </a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add celebration animation
    setTimeout(function() {
        // Create confetti effect (optional)
        if (typeof confetti !== 'undefined') {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }
    }, 500);

    // Auto redirect after 30 seconds (optional)
    let countdown = 30;
    const redirectTimer = setInterval(function() {
        countdown--;
        if (countdown <= 0) {
            clearInterval(redirectTimer);
            // Uncomment next line if you want auto redirect
            // window.location.href = '<?php echo base_url(); ?>';
        }
    }, 1000);

    // Add hover effects
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>