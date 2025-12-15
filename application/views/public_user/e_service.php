<?php
defined('BASEPATH') or exit('No direct script access allowed');

// 🚨 ตรวจสอบ login ก่อน
if (!$this->session->userdata('mp_id')) {
    redirect('User');
    return;
}

// 🔔 ตั้งค่าเริ่มต้นถ้าไม่มีข้อมูลส่งมาจาก Controller
if (!isset($notifications)) $notifications = [];
if (!isset($unread_count)) $unread_count = 0;
if (!isset($total_notifications)) $total_notifications = 0;
?>

<div class="bg-pages">
    <!-- 🔔 Notification Bell ที่มุมขวาบน -->
<div class="notification-bell-container">
    <div class="notification-bell" onclick="toggleNotifications()">
        <i class="bi bi-bell-fill"></i>
        <?php if ($unread_count > 0): ?>
            <span class="notification-badge"><?php echo $unread_count > 99 ? '99+' : $unread_count; ?></span>
        <?php endif; ?>
    </div>
    
    <!-- Notification Dropdown -->
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h6><i class="bi bi-bell me-2"></i>การแจ้งเตือน</h6>
            <span class="notification-count"><?php echo $unread_count; ?> ใหม่</span>
        </div>
        <div class="notification-list">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    // *** แก้ไข: ใช้ is_read_by_user แทน is_read ***
                    $isUnread = !isset($notification->is_read_by_user) || $notification->is_read_by_user == 0;
                    ?>
                    <div class="notification-item <?php echo $isUnread ? 'unread' : ''; ?>" 
                         onclick="handleNotificationClick(<?php echo $notification->notification_id; ?>, '<?php echo htmlspecialchars($notification->url ?: '#', ENT_QUOTES); ?>')">
                        <div class="notification-icon">
                            <i class="<?php echo $notification->icon ?: 'bi bi-bell'; ?>"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title"><?php echo htmlspecialchars($notification->title); ?></div>
                            <div class="notification-message"><?php echo htmlspecialchars($notification->message); ?></div>
                            <div class="notification-time">
                                <?php 
                                // ใช้ timeago helper ถ้ามี หรือใช้ date ธรรมดา
                                if (function_exists('timeago')) {
                                    echo timeago($notification->created_at);
                                } else {
                                    echo date('d/m/Y H:i', strtotime($notification->created_at));
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notification-empty">
                    <i class="bi bi-bell-slash"></i>
                    <p>ไม่มีการแจ้งเตือน</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="notification-footer">
            <a href="<?php echo site_url('notifications/all'); ?>" class="view-all-link">
                ดูทั้งหมด <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

    <div class="container-pages-news" style="position: relative; z-index: 10;">
        <!-- ข้อความต้อนรับ -->
        <div class="welcome-section">
            <div class="user-section">
                <!-- ✅ แสดง user greeting เสมอ (เพราะแน่ใจแล้วว่า login แล้ว) -->
                <a href="<?php echo site_url('Auth_public_mem/profile'); ?>" class="user-greeting user-greeting-link">
                    <div class="user-greeting-content">
                        <i class="bi bi-person-check-fill"></i>
                        <span>สวัสดี, คุณ <?php echo $this->session->userdata('mp_fname') . ' ' . $this->session->userdata('mp_lname'); ?></span>
                    </div>
                    <i class="bi bi-arrow-right-circle ms-2" style="font-size: 1rem; opacity: 0.7;"></i>
                </a>
                
                <?php 
                // ตรวจสอบสถานะ 2FA และแสดงข้อความเตือนแยกต่างหาก
                $mp_id = $this->session->userdata('mp_id');
                if ($mp_id) {
                    $this->load->model('member_public_model');
                    $user_2fa_info = $this->member_public_model->get_2fa_info($mp_id);
                    $is_2fa_enabled = isset($user_2fa_info) && $user_2fa_info && !empty($user_2fa_info->google2fa_secret) && $user_2fa_info->google2fa_enabled == 1;
                    
                    if (!$is_2fa_enabled): ?>
                        <div class="security-warning">
                            <i class="bi bi-shield-exclamation me-2"></i>
                            <span>ยังไม่ได้ทำการเปิดระบบยืนยันตัวตน</span>
                            <a href="<?php echo site_url('Auth_public_mem/profile'); ?>" class="setup-2fa-link ms-2">
                                <i class="bi bi-arrow-right-circle"></i>
                            </a>
                        </div>
                    <?php endif;
                } ?>
            </div>
            
            <h3 class="service-header mt-4">ระบบบริการ e-Service สำหรับประชาชน</h3>
            <p class="service-subheader">
                เลือกเข้าใช้งานระบบบริการ e-Service ได้ตามที่ต้องการ สะดวกและรวดเร็ว
            </p>
        </div>
        
        <!-- แสดง success message หลังจาก login สำเร็จ -->
        <?php if ($this->session->flashdata('login_success')): ?>
            <div class="alert alert-success login-alert">
                <i class="bi bi-check-circle-fill"></i> เข้าสู่ระบบสำเร็จ ยินดีต้อนรับเข้าสู่ระบบบริการ e-Service
            </div>
        <?php endif; ?>
        
        <!-- แสดงข้อความแจ้งเตือนหากเกิดข้อผิดพลาด -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger login-alert" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>
        
        <!-- 🆕 ส่วนของการ์ดระบบบริการ - Enhanced Design (แถวที่ 1: 3 ปุ่ม) -->
        <div class="services-grid-enhanced">
            <!-- ระบบจ่ายภาษี - Enhanced -->
            <div class="service-card-enhanced tax-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-cash-stack"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-receipt"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-credit-card"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="service-badge">
                            <span>TAX SYSTEM</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">ระบบจ่ายภาษี</h4>
                        <p class="service-description">
                            ชำระภาษีออนไลน์ได้ตลอด 24 ชั่วโมง
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>โอนชำระได้ทุกธนาคาร</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>แจ้งใบเสร็จในระบบ</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ตรวจสอบสถานะได้</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Member_public_sso/redirect_to_service/tax/localtax.assystem.co.th'); ?>" 
                           class="btn-service-enhanced tax-btn-enhanced" target="_blank">
                            <span class="btn-text">เข้าสู่ระบบ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ระบบจองคิวรถ - Enhanced -->
            <div class="service-card-enhanced queue-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-car-front"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-calendar2-week"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-clock"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="service-badge">
                            <span>BOOKING SYSTEM</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">ระบบจองคิวรถ</h4>
                        <p class="service-description">
                            จองคิวรถล่วงหน้าได้
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>จองคิวล่วงหน้าได้ 7 วัน</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>แจ้งเตือนก่อนเวลา</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>แจ้งยกเลิก แก้ไขได้</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Member_public_sso/redirect_to_service/qcar/carbooking.assystem.co.th'); ?>" 
                           class="btn-service-enhanced queue-btn-enhanced" target="_blank">
                            <span class="btn-text">เข้าสู่ระบบ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ระบบแจ้งเรื่อง ร้องเรียน - New Enhanced -->
            <div class="service-card-enhanced complaint-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-chat-dots"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-megaphone"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-headset"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-chat-square-dots"></i>
                        </div>
                        <div class="service-badge">
                            <span>COMPLAINT SYSTEM</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">แจ้งเรื่อง ร้องเรียน</h4>
                        <p class="service-description">
                            แจ้งปัญหาและข้อเสนอแนะ
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ตอบกลับภายใน 24 ชม.</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ติดตามสถานะได้</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ระบบปกป้องข้อมูล</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('complaints_public/status'); ?>" 
                           class="btn-service-enhanced complaint-btn-enhanced">
                            <span class="btn-text">แจ้งเรื่อง และดูสถานะ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🆕 แถวที่ 2: 3 ปุ่ม -->
        <div class="services-grid-enhanced second-row">
            <!-- จองคิวติดต่อราชการ - New Enhanced -->
            <div class="service-card-enhanced appointment-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-building"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-person-check"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-calendar-event"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div class="service-badge">
                            <span>APPOINTMENT SYSTEM</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">จองคิวติดต่อราชการ</h4>
                        <p class="service-description">
                            จองคิวล่วงหน้าสำหรับติดต่อราชการ<br>
                            ประหยัดเวลาและลดการรอคอย
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>เลือกวันเวลาได้</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>แจ้งเตือนล่วงหน้า</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>จัดการคิวง่าย</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Queue/my_queue_status'); ?>" 
                           class="btn-service-enhanced appointment-btn-enhanced">
                            <span class="btn-text">จองคิว และดูสถานะ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- รับฟังความคิดเห็น -->
            <div class="service-card-enhanced feedback-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-hand-thumbs-up"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-star"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-heart"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-emoji-smile"></i>
                        </div>
                        <div class="service-badge">
                            <span>FEEDBACK</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">รับฟังความคิดเห็น</h4>
                        <p class="service-description">
                            แสดงความคิดเห็นและข้อเสนอแนะ<br>
                            เพื่อพัฒนาการบริการ
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>แสดงความคิดเห็น</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>สำรวจความพึ่งพอใจ</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ข้อเสนอแนะพัฒนา</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Suggestions/my_suggestions'); ?>" 
                           class="btn-service-enhanced feedback-btn-enhanced">
                            <span class="btn-text">แสดงความคิดเห็น</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- เบี้ยยังชีพผู้สูงอายุ / ผู้พิการ -->
            <div class="service-card-enhanced allowance-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-people"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-heart-pulse"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-shield-heart"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-person-hearts"></i>
                        </div>
                        <div class="service-badge">
                            <span>ALLOWANCE</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">เบี้ยยังชีพผู้สูงอายุ/พิการ</h4>
                        <p class="service-description">
                            สมัครและตรวจสอบสถานะ<br>
                            เบี้ยยังชีพต่างๆ
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>สมัครออนไลน์</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ตรวจสอบสถานะ</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ดาวน์โหลดเอกสาร</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Elderly_aw_ods/my_elderly_aw_ods'); ?>" 
                           class="btn-service-enhanced allowance-btn-enhanced">
                            <span class="btn-text">ยื่นเอกสาร และดูสถานะ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🆕 แถวที่ 3: 3 ปุ่ม -->
        <div class="services-grid-enhanced third-row">
            

            <!-- เงินอุดหนุนเด็กแรกเกิด -->
            <div class="service-card-enhanced subsidy-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-balloon-heart"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-gift"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-house-heart"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="service-badge">
                            <span>SUBSIDY</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">เงินอุดหนุนเด็กแรกเกิด</h4>
                        <p class="service-description">
                            สมัครเงินอุดหนุนเด็กแรกเกิด และสวัสดิการสำหรับครอบครัว
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>สมัครง่าย รวดเร็ว</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ติดตามสถานะ</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ระบบแจ้งเตือน</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Kid_aw_ods/my_kid_aw_ods'); ?>" 
                           class="btn-service-enhanced subsidy-btn-enhanced">
                            <span class="btn-text">ยื่นเอกสาร และดูสถานะ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ยื่นเอกสารออนไลน์ -->
            <div class="service-card-enhanced document-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-cloud-upload"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-check2-square"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-file-earmark-arrow-up"></i>
                        </div>
                        <div class="service-badge">
                            <span>DOCUMENT</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">ยื่นเอกสารออนไลน์</h4>
                        <p class="service-description">
                            ยื่นเอกสารและคำร้องต่างๆ ผ่านระบบออนไลน์
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>อัปโหลดเอกสาร</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ติดตามความคืบหน้า</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>รับแจ้งผลออนไลน์</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Esv_ods/my_documents'); ?>" 
                           class="btn-service-enhanced document-btn-enhanced">
                            <span class="btn-text">ยื่นเอกสาร และดูสถานะ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
			
			
			
			
			<!-- แจ้งเรื่องร้องเรียนการทุจริต - NEW -->
            <div class="service-card-enhanced corruption-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-shield-exclamation"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-eye"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-clipboard-check"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-shield-x"></i>
                        </div>
                        <div class="service-badge">
                            <span>ANTI-CORRUPTION</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">แจ้งเรื่องการทุจริต</h4>
                        <p class="service-description">
                            แจ้งข้อมูลการทุจริตและการประพฤติมิชอบ ระบบปกป้องผู้แจ้ง
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>แจ้งได้ทั้งเปิดเผยและไม่ระบุตัวตน</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ระบบรักษาความลับ</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ติดตามผลการดำเนินการ</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Corruption/my_reports'); ?>" 
                           class="btn-service-enhanced corruption-btn-enhanced">
                            <span class="btn-text">แจ้งเรื่องทุจริต และดูสถานะ</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
			
			
			
			
        </div>

        <!-- 🆕 แถวที่ 4: 3 ปุ่ม (เริ่มจากแบบสอบถาม + รอ 2 ปุ่มใหม่) -->
        <div class="services-grid-enhanced fourth-row">
            <!-- แบบสอบถามประเมินความพึงพอใจ -->
            <div class="service-card-enhanced survey-service">
                <div class="service-card-background">
                    <div class="floating-elements">
                        <div class="floating-icon pos-1"><i class="bi bi-clipboard-check"></i></div>
                        <div class="floating-icon pos-2"><i class="bi bi-graph-up"></i></div>
                        <div class="floating-icon pos-3"><i class="bi bi-award"></i></div>
                    </div>
                </div>
                
                <div class="service-card-content">
                    <div class="service-header-section">
                        <div class="service-icon-enhanced">
                            <div class="icon-glow"></div>
                            <i class="bi bi-clipboard2-check"></i>
                        </div>
                        <div class="service-badge">
                            <span>SURVEY</span>
                        </div>
                    </div>
                    
                    <div class="service-info">
                        <h4 class="service-title">แบบสอบถามประเมินความพึงพอใจ</h4>
                        <p class="service-description">
                            ประเมินความพึงพอใจการให้บริการ เพื่อพัฒนาคุณภาพการบริการ
                        </p>
                        
                        <div class="service-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>แบบสอบถามออนไลน์</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ใช้เวลาไม่เกิน 5 นาที</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>ช่วยพัฒนาการบริการ</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <a href="<?php echo site_url('Assessment'); ?>" 
                           class="btn-service-enhanced survey-btn-enhanced">
                            <span class="btn-text">ทำแบบสอบถาม</span>
                            <div class="btn-icon">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 🔳 พื้นที่ว่างสำหรับการ์ดใหม่ที่ 1 -->
            <!-- เตรียมไว้สำหรับการ์ดใหม่ -->

            <!-- 🔳 พื้นที่ว่างสำหรับการ์ดใหม่ที่ 2 -->
            <!-- เตรียมไว้สำหรับการ์ดใหม่ -->
        </div>
    </div>
</div>

<!-- Modal แจ้งเตือน (คงไว้เผื่อใช้ในอนาคต) -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">ไม่สามารถเข้าถึงได้</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-3"></i>
                <p>ท่านไม่มีสิทธิ์เข้าใช้งานระบบนี้ โปรดติดต่อผู้ดูแลระบบ</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แจ้งเตือนเชิญชวนให้เปิดใช้งาน 2FA -->
<div class="modal fade" id="invite2FAModal" tabindex="-1" aria-labelledby="invite2FAModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title" id="invite2FAModalLabel">
                    <i class="bi bi-shield-plus"></i> เพิ่มความปลอดภัยให้บัญชีของคุณ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Hero Section -->
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 80px; height: 80px; margin-bottom: 20px;">
                        <i class="bi bi-shield-check" style="font-size: 2.5rem; color: var(--success);"></i>
                    </div>
                    <h4 class="text-dark mb-2">ยืนยันตัวตนปกป้องบัญชีของคุณด้วย 2FA</h4>
                    <p class="text-muted">การยืนยันตัวตนแบบ 2 ขั้นตอนเป็นชั้นความปลอดภัยเพิ่มเติมที่สำคัญมาก</p>
                </div>

                <!-- Benefits -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">ความปลอดภัยสูงสุด</h6>
                                <small class="text-muted">แม้มีคนรู้รหัสผ่าน ก็ไม่สามารถเข้าใช้งานได้หากไม่มีมือถือของคุณ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-clock-fill text-primary me-3" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">ใช้งานง่าย</h6>
                                <small class="text-muted">แค่สแกน QR Code ครั้งเดียว ก็ใช้งานได้ทันที</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-phone-fill text-info me-3" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">ทำงานแบบออฟไลน์</h6>
                                <small class="text-muted">ไม่ต้องการอินเทอร์เน็ต รหัสสร้างขึ้นในมือถือของคุณ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-key-fill text-warning me-3" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">มีรหัสสำรอง</h6>
                                <small class="text-muted">กรณีมือถือหาย ยังมีรหัสสำรองให้ใช้งาน</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning Section -->
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            <strong>คุณรู้หรือไม่?</strong> 
                            <p class="mb-0 mt-1">บัญชีที่ไม่มี 2FA มีความเสี่ยงสูงที่จะถูกแฮคกว่า <strong>สูงกว่า 99% </strong> บัญชีท่านควรที่เปิดใช้งาน 2FA</p>
                        </div>
                    </div>
                </div>

                <!-- Steps Preview -->
                <div class="bg-light rounded p-3 mb-4">
                    <h6 class="mb-3"><i class="bi bi-list-ol me-2"></i>ขั้นตอนการตั้งค่า (ใช้เวลาแค่ 2 นาที)</h6>
                    <div class="row">
                        <div class="col-4 text-center">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 30px; height: 30px; line-height: 18px;">1</div>
                            <small class="d-block">ติดตั้งแอป</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 30px; height: 30px; line-height: 18px;">2</div>
                            <small class="d-block">สแกน QR Code</small>
                        </div>
                        <div class="col-4 text-center">
                            <div class="badge bg-primary rounded-circle mb-2" style="width: 30px; height: 30px; line-height: 18px;">3</div>
                            <small class="d-block">ยืนยันรหัส</small>
                        </div>
                    </div>
                </div>

                <!-- Don't show again option -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="dontShowAgain">
                    <label class="form-check-label text-muted" for="dontShowAgain">
                        ไม่ต้องแสดงข้อความนี้อีก (สามารถเปิดใช้งานได้ในภายหลัง)
                    </label>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" onclick="handleDontShowAgain()">
                    <i class="bi bi-x-circle me-1"></i>ข้ามไปก่อน
                </button>
                <button type="button" class="btn btn-success btn-lg" onclick="goToProfileFor2FA()">
                    <i class="bi bi-shield-plus me-2"></i>เปิดใช้งาน 2FA เลย
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== ENHANCED SERVICE CARDS STYLES - ปุ่มจะไม่เลื่อน ===== */
:root {
    --primary: #8B9DC3;
    --primary-dark: #6B7FA3;
    --secondary: #A8D8A8;
    --accent: #F4C2A1;
    --danger: #E8A5A5;
    --warning: #F5D76E;
    --info: #89CDF1;
    --success: #9BCFA0;
    --light: #FDFDFD;
    --dark: #5A6C7D;
    --border-color: #E8ECF4;
    --shadow-soft: 0 8px 32px rgba(139, 157, 195, 0.15);
    --shadow-medium: 0 12px 40px rgba(139, 157, 195, 0.2);
    --shadow-strong: 0 20px 60px rgba(139, 157, 195, 0.25);
    --border-radius: 20px;
    --border-radius-large: 28px;
    --backdrop-blur: blur(10px);
    
    /* 🆕 Enhanced Color Palette */
    --tax-primary: #FF6B6B;
    --tax-secondary: #FF8E8E;
    --tax-accent: #FFB6B6;
    --queue-primary: #4ECDC4;
    --queue-secondary: #45B7B8;
    --queue-accent: #7ED6DF;
    --complaint-primary: #9B59B6;
    --complaint-secondary: #8E44AD;
    --complaint-accent: #BB6BD9;
    --appointment-primary: #3498DB;
    --appointment-secondary: #2980B9;
    --appointment-accent: #5DADE2;
    --feedback-primary: #27AE60;
    --feedback-secondary: #229954;
    --feedback-accent: #58D68D;
    --allowance-primary: #E67E22;
    --allowance-secondary: #D35400;
    --allowance-accent: #F39C12;
    --corruption-primary: #DC143C;
    --corruption-secondary: #B22222;
    --corruption-accent: #F08080;
    --subsidy-primary: #E91E63;
    --subsidy-secondary: #C2185B;
    --subsidy-accent: #F06292;
    --document-primary: #34495E;
    --document-secondary: #2C3E50;
    --document-accent: #5D6D7E;
    --survey-primary: #16A085;
    --survey-secondary: #1ABC9C;
    --survey-accent: #48C9B0;
}

.bg-pages {
    background: linear-gradient(135deg, #FAFBFF 0%, #F0F4F8 50%, #E8F0FE 100%);
    min-height: 85vh;
    padding: 40px 0;
    position: relative;
    margin-top: -24px;
}

.bg-pages::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 25% 25%, rgba(139, 157, 195, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(168, 216, 168, 0.03) 0%, transparent 50%);
    pointer-events: none;
}

/* 🔔 Notification Bell Styles */
.notification-bell-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.notification-bell {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.95), rgba(139, 157, 195, 0.85));
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: var(--shadow-medium);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.notification-bell:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: var(--shadow-strong);
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
}

.notification-bell i {
    font-size: 1.5rem;
    color: white;
    transition: all 0.3s ease;
}

.notification-bell:hover i {
    animation: ringBell 0.5s ease-in-out;
}

@keyframes ringBell {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #ff4757, #ff3742);
    color: white;
    border-radius: 50%;
    min-width: 22px;
    height: 22px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Notification Dropdown */
.notification-dropdown {
    position: absolute;
    top: 65px;
    right: 0;
    width: 380px;
    max-height: 500px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-strong);
    border: 1px solid rgba(255, 255, 255, 0.3);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.notification-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.notification-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(232, 236, 244, 0.5);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, rgba(250, 251, 255, 0.8), rgba(240, 244, 248, 0.8));
}

.notification-header h6 {
    margin: 0;
    font-weight: 600;
    color: var(--dark);
    font-size: 1.1rem;
}

.notification-count {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.notification-list {
    max-height: 320px;
    overflow-y: auto;
    padding: 8px 0;
}

.notification-item {
    padding: 16px 24px;
    border-bottom: 1px solid rgba(232, 236, 244, 0.3);
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.notification-item:hover {
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.05), rgba(139, 157, 195, 0.08));
    transform: translateX(3px);
}

.notification-item.unread {
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.08), rgba(139, 157, 195, 0.05));
    border-left: 4px solid var(--primary);
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 20px;
    right: 20px;
    width: 8px;
    height: 8px;
    background: linear-gradient(135deg, #ff4757, #ff3742);
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(255, 71, 87, 0.5);
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.15), rgba(139, 157, 195, 0.25));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    flex-shrink: 0;
    margin-top: 2px;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.95rem;
    margin-bottom: 4px;
    line-height: 1.3;
}

.notification-message {
    color: rgba(90, 108, 125, 0.8);
    font-size: 0.85rem;
    line-height: 1.4;
    margin-bottom: 6px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.notification-time {
    color: rgba(90, 108, 125, 0.6);
    font-size: 0.75rem;
    font-weight: 400;
}

.notification-empty {
    text-align: center;
    padding: 40px 20px;
    color: rgba(90, 108, 125, 0.6);
}

.notification-empty i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.notification-footer {
    padding: 16px 24px;
    border-top: 1px solid rgba(232, 236, 244, 0.5);
    text-align: center;
    background: linear-gradient(135deg, rgba(250, 251, 255, 0.8), rgba(240, 244, 248, 0.8));
}

.view-all-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    color: var(--primary-dark);
    text-decoration: none;
    transform: translateX(3px);
}

.container-pages-news {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
    position: relative;
    z-index: 1;
}

.welcome-section {
    text-align: center;
    margin-bottom: 50px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    padding: 40px;
    border-radius: var(--border-radius-large);
    box-shadow: var(--shadow-medium);
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
    overflow: hidden;
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    border-radius: var(--border-radius-large) var(--border-radius-large) 0 0;
}

.user-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 25px;
}

.user-greeting {
    background: linear-gradient(135deg, rgba(155, 207, 160, 0.9), rgba(155, 207, 160, 0.7));
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    color: var(--dark);
    padding: 18px 28px;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    margin-bottom: 0;
    font-weight: 500;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(255, 255, 255, 0.4);
    transition: all 0.4s ease;
}

.user-greeting:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.user-greeting i {
    margin-right: 12px;
    font-size: 1.3rem;
    opacity: 0.8;
}

.security-warning {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8));
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    color: #e67e22;
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 400;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(230, 126, 34, 0.2);
    transition: all 0.3s ease;
    margin-top: 12px;
    max-width: fit-content;
}

.security-warning:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-medium);
    background: linear-gradient(135deg, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0.9));
}

.security-warning i {
    font-size: 1rem;
    opacity: 0.8;
}

.setup-2fa-link {
    color: #e67e22;
    text-decoration: none;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.setup-2fa-link:hover {
    color: #d35400;
    transform: translateX(2px);
    opacity: 1;
    text-decoration: none;
}

.user-greeting-link {
    text-decoration: none !important;
    color: inherit;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.user-greeting-link:hover {
    text-decoration: none !important;
    color: var(--dark);
    background: linear-gradient(135deg, rgba(155, 207, 160, 1), rgba(155, 207, 160, 0.8));
    transform: translateY(-3px) scale(1.02);
    box-shadow: var(--shadow-strong);
}

.user-greeting-link:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(155, 207, 160, 0.4);
    text-decoration: none !important;
}

.user-greeting-link:active {
    transform: translateY(-1px) scale(0.98);
    text-decoration: none !important;
}

.user-greeting-link:visited {
    text-decoration: none !important;
    color: inherit;
}

.user-greeting-content {
    display: flex;
    align-items: center;
    flex: 1;
}

.user-greeting-link::before {
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

.user-greeting-link:active::before {
    width: 300px;
    height: 300px;
}

.user-greeting-link .bi-arrow-right-circle {
    transition: all 0.3s ease;
    color: rgba(90, 108, 125, 0.6);
}

.user-greeting-link:hover .bi-arrow-right-circle {
    transform: translateX(3px);
    color: rgba(90, 108, 125, 0.8);
}

.service-header {
    font-size: 2.2rem;
    font-weight: 400;
    color: var(--dark);
    margin-bottom: 18px;
    letter-spacing: -0.01em;
}

.service-subheader {
    font-size: 1.15rem;
    color: rgba(90, 108, 125, 0.8);
    line-height: 1.7;
    max-width: 600px;
    margin: 0 auto;
}

.login-alert {
    border: none;
    border-radius: var(--border-radius);
    padding: 24px;
    margin-bottom: 35px;
    font-weight: 400;
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* 🆕 ENHANCED SERVICE CARDS - ปุ่มไม่เลื่อน */
.services-grid-enhanced {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 60px;
    padding: 0 20px;
}

.services-grid-enhanced.second-row {
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 40px;
}

.services-grid-enhanced.third-row {
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 40px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.services-grid-enhanced.fourth-row {
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 40px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.service-card-enhanced {
    position: relative;
    height: 500px;
    border-radius: 30px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.services-grid-enhanced.second-row .service-card-enhanced,
.services-grid-enhanced.third-row .service-card-enhanced,
.services-grid-enhanced.fourth-row .service-card-enhanced {
    height: 480px;
}

.service-card-enhanced:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
}

/* Service Card Background with Floating Elements */
.service-card-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    overflow: hidden;
    opacity: 0.6;
}

.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
}

.floating-icon {
    position: absolute;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.floating-icon.pos-1 {
    top: 10%;
    right: 15%;
    animation-delay: 0s;
}

.floating-icon.pos-2 {
    top: 60%;
    left: 10%;
    animation-delay: -2s;
}

.floating-icon.pos-3 {
    top: 30%;
    right: 5%;
    animation-delay: -4s;
}

@keyframes float {
    0%, 100% { 
        transform: translateY(0) rotate(0deg); 
    }
    33% { 
        transform: translateY(-20px) rotate(5deg); 
    }
    66% { 
        transform: translateY(10px) rotate(-3deg); 
    }
}

/* Tax Service Styling */
.tax-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(255, 107, 107, 0.05) 0%, 
        rgba(255, 182, 182, 0.03) 50%, 
        rgba(255, 138, 138, 0.05) 100%);
}

.tax-service .floating-icon {
    background: linear-gradient(135deg, var(--tax-primary), var(--tax-secondary));
    color: white;
}

/* Queue Service Styling */
.queue-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(78, 205, 196, 0.05) 0%, 
        rgba(126, 214, 223, 0.03) 50%, 
        rgba(69, 183, 184, 0.05) 100%);
}

.queue-service .floating-icon {
    background: linear-gradient(135deg, var(--queue-primary), var(--queue-secondary));
    color: white;
}

/* Complaint Service Styling */
.complaint-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(155, 89, 182, 0.05) 0%, 
        rgba(187, 107, 217, 0.03) 50%, 
        rgba(142, 68, 173, 0.05) 100%);
}

.complaint-service .floating-icon {
    background: linear-gradient(135deg, var(--complaint-primary), var(--complaint-secondary));
    color: white;
}

/* Appointment Service Styling */
.appointment-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(52, 152, 219, 0.05) 0%, 
        rgba(93, 173, 226, 0.03) 50%, 
        rgba(41, 128, 185, 0.05) 100%);
}

.appointment-service .floating-icon {
    background: linear-gradient(135deg, var(--appointment-primary), var(--appointment-secondary));
    color: white;
}

/* Feedback Service Styling */
.feedback-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(39, 174, 96, 0.05) 0%, 
        rgba(88, 214, 141, 0.03) 50%, 
        rgba(34, 153, 84, 0.05) 100%);
}

.feedback-service .floating-icon {
    background: linear-gradient(135deg, var(--feedback-primary), var(--feedback-secondary));
    color: white;
}

/* Allowance Service Styling */
.allowance-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(230, 126, 34, 0.05) 0%, 
        rgba(243, 156, 18, 0.03) 50%, 
        rgba(211, 84, 0, 0.05) 100%);
}

.allowance-service .floating-icon {
    background: linear-gradient(135deg, var(--allowance-primary), var(--allowance-secondary));
    color: white;
}

/* Corruption Service Styling */
.corruption-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(220, 20, 60, 0.05) 0%, 
        rgba(240, 128, 128, 0.03) 50%, 
        rgba(178, 34, 34, 0.05) 100%);
}

.corruption-service .floating-icon {
    background: linear-gradient(135deg, var(--corruption-primary), var(--corruption-secondary));
    color: white;
}

/* Subsidy Service Styling */
.subsidy-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(233, 30, 99, 0.05) 0%, 
        rgba(240, 98, 146, 0.03) 50%, 
        rgba(194, 24, 91, 0.05) 100%);
}

.subsidy-service .floating-icon {
    background: linear-gradient(135deg, var(--subsidy-primary), var(--subsidy-secondary));
    color: white;
}

/* Document Service Styling */
.document-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(52, 73, 94, 0.05) 0%, 
        rgba(93, 109, 126, 0.03) 50%, 
        rgba(44, 62, 80, 0.05) 100%);
}

.document-service .floating-icon {
    background: linear-gradient(135deg, var(--document-primary), var(--document-secondary));
    color: white;
}

/* Survey Service Styling */
.survey-service .service-card-background {
    background: linear-gradient(135deg, 
        rgba(22, 160, 133, 0.05) 0%, 
        rgba(72, 201, 176, 0.03) 50%, 
        rgba(26, 188, 156, 0.05) 100%);
}

.survey-service .floating-icon {
    background: linear-gradient(135deg, var(--survey-primary), var(--survey-secondary));
    color: white;
}

/* ⭐ Service Card Content - FIX ปุ่มไม่เลื่อน */
.service-card-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 35px;
    padding-bottom: 90px; /* เพิ่ม space สำหรับปุ่มที่อยู่ด้านล่าง */
}

.service-header-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
}

.service-icon-enhanced {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.service-icon-enhanced .icon-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 20px;
    opacity: 0;
    transition: opacity 0.5s ease;
}

.service-card-enhanced:hover .service-icon-enhanced .icon-glow {
    opacity: 1;
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    from {
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    }
    to {
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.6);
    }
}

/* Tax Icon Styling */
.tax-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--tax-primary), var(--tax-secondary));
}

.tax-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--tax-primary), var(--tax-accent));
}

/* Queue Icon Styling */
.queue-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--queue-primary), var(--queue-secondary));
}

.queue-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--queue-primary), var(--queue-accent));
}

/* Complaint Icon Styling */
.complaint-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--complaint-primary), var(--complaint-secondary));
}

.complaint-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--complaint-primary), var(--complaint-accent));
}

/* Appointment Icon Styling */
.appointment-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--appointment-primary), var(--appointment-secondary));
}

.appointment-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--appointment-primary), var(--appointment-accent));
}

/* Feedback Icon Styling */
.feedback-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--feedback-primary), var(--feedback-secondary));
}

.feedback-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--feedback-primary), var(--feedback-accent));
}

/* Allowance Icon Styling */
.allowance-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--allowance-primary), var(--allowance-secondary));
}

.allowance-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--allowance-primary), var(--allowance-accent));
}

/* Corruption Icon Styling */
.corruption-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--corruption-primary), var(--corruption-secondary));
}

.corruption-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--corruption-primary), var(--corruption-accent));
}

/* Subsidy Icon Styling */
.subsidy-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--subsidy-primary), var(--subsidy-secondary));
}

.subsidy-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--subsidy-primary), var(--subsidy-accent));
}

/* Document Icon Styling */
.document-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--document-primary), var(--document-secondary));
}

.document-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--document-primary), var(--document-accent));
}

/* Survey Icon Styling */
.survey-service .service-icon-enhanced {
    background: linear-gradient(135deg, var(--survey-primary), var(--survey-secondary));
}

.survey-service .service-icon-enhanced .icon-glow {
    background: linear-gradient(135deg, var(--survey-primary), var(--survey-accent));
}

.service-badge {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 1px;
    color: var(--dark);
    text-transform: uppercase;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.service-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.service-title {
    font-size: 1.6rem;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 12px;
    line-height: 1.2;
    height: 3.8rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.service-description {
    font-size: 1rem;
    color: rgba(90, 108, 125, 0.75);
    line-height: 1.6;
    margin-bottom: 25px;
    height: 3.2rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.service-features {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 30px;
    min-height: 120px;
    max-height: 120px;
    overflow: hidden;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.9rem;
    color: rgba(90, 108, 125, 0.9);
    line-height: 1.3;
    min-height: 24px;
}

.feature-item span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

.feature-item i {
    font-size: 1rem;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.service-card-enhanced:hover .feature-item i {
    transform: scale(1.1);
}

/* Tax Features Styling */
.tax-service .feature-item i {
    color: var(--tax-primary);
}

/* Queue Features Styling */
.queue-service .feature-item i {
    color: var(--queue-primary);
}

/* Complaint Features Styling */
.complaint-service .feature-item i {
    color: var(--complaint-primary);
}

/* Appointment Features Styling */
.appointment-service .feature-item i {
    color: var(--appointment-primary);
}

/* Feedback Features Styling */
.feedback-service .feature-item i {
    color: var(--feedback-primary);
}

/* Allowance Features Styling */
.allowance-service .feature-item i {
    color: var(--allowance-primary);
}

/* Corruption Features Styling */
.corruption-service .feature-item i {
    color: var(--corruption-primary);
}

/* Subsidy Features Styling */
.subsidy-service .feature-item i {
    color: var(--subsidy-primary);
}

/* Document Features Styling */
.document-service .feature-item i {
    color: var(--document-primary);
}

/* Survey Features Styling */
.survey-service .feature-item i {
    color: var(--survey-primary);
}

/* ⭐ Service Action - ปุ่มที่ไม่เลื่อน */
.service-action {
    position: absolute;
    bottom: 35px;
    left: 35px;
    right: 35px;
    z-index: 3;
}

.btn-service-enhanced {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 55px;
    padding: 0 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    position: relative;
    overflow: hidden;
    border: none;
    color: white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    min-height: 55px;
    max-height: 55px;
}

.btn-service-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.8s ease;
}

.btn-service-enhanced:hover::before {
    left: 100%;
}

.btn-service-enhanced:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    text-decoration: none;
    color: white;
}

.btn-text {
    font-weight: 600;
    flex: 1;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: calc(100% - 50px);
    line-height: 1.2;
}

.btn-icon {
    width: 35px;
    height: 35px;
    min-width: 35px;
    min-height: 35px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s ease;
    flex-shrink: 0;
}

.btn-service-enhanced:hover .btn-icon {
    transform: translateX(5px) scale(1.1);
    background: rgba(255, 255, 255, 0.3);
}

/* Tax Button Styling */
.tax-btn-enhanced {
    background: linear-gradient(135deg, var(--tax-primary), var(--tax-secondary));
}

.tax-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--tax-secondary), var(--tax-accent));
}

/* Queue Button Styling */
.queue-btn-enhanced {
    background: linear-gradient(135deg, var(--queue-primary), var(--queue-secondary));
}

.queue-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--queue-secondary), var(--queue-accent));
}

/* Complaint Button Styling */
.complaint-btn-enhanced {
    background: linear-gradient(135deg, var(--complaint-primary), var(--complaint-secondary));
}

.complaint-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--complaint-secondary), var(--complaint-accent));
}

/* Appointment Button Styling */
.appointment-btn-enhanced {
    background: linear-gradient(135deg, var(--appointment-primary), var(--appointment-secondary));
}

.appointment-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--appointment-secondary), var(--appointment-accent));
}

/* Feedback Button Styling */
.feedback-btn-enhanced {
    background: linear-gradient(135deg, var(--feedback-primary), var(--feedback-secondary));
}

.feedback-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--feedback-secondary), var(--feedback-accent));
}

/* Allowance Button Styling */
.allowance-btn-enhanced {
    background: linear-gradient(135deg, var(--allowance-primary), var(--allowance-secondary));
}

.allowance-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--allowance-secondary), var(--allowance-accent));
}

/* Corruption Button Styling */
.corruption-btn-enhanced {
    background: linear-gradient(135deg, var(--corruption-primary), var(--corruption-secondary));
}

.corruption-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--corruption-secondary), var(--corruption-accent));
}

/* Subsidy Button Styling */
.subsidy-btn-enhanced {
    background: linear-gradient(135deg, var(--subsidy-primary), var(--subsidy-secondary));
}

.subsidy-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--subsidy-secondary), var(--subsidy-accent));
}

/* Document Button Styling */
.document-btn-enhanced {
    background: linear-gradient(135deg, var(--document-primary), var(--document-secondary));
}

.document-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--document-secondary), var(--document-accent));
}

/* Survey Button Styling */
.survey-btn-enhanced {
    background: linear-gradient(135deg, var(--survey-primary), var(--survey-secondary));
}

.survey-btn-enhanced:hover {
    background: linear-gradient(135deg, var(--survey-secondary), var(--survey-accent));
}

/* Prevent text decoration on links */
.service-card-enhanced a,
.service-card-enhanced a:hover,
.service-card-enhanced a:focus,
.service-card-enhanced a:visited,
.service-card-enhanced a:active {
    text-decoration: none !important;
}

/* Focus states */
.btn-service-enhanced:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.5);
}

/* Modal Styles remain the same... */
#invite2FAModal .modal-content {
    border: none;
    border-radius: var(--border-radius-large);
    box-shadow: var(--shadow-strong);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
}

#invite2FAModal .modal-header {
    border-radius: var(--border-radius-large) var(--border-radius-large) 0 0;
    border-bottom: none;
    padding: 30px 35px;
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.9), rgba(139, 157, 195, 0.8));
    color: var(--dark);
}

#invite2FAModal .modal-body {
    padding: 35px;
}

#invite2FAModal .modal-footer {
    border-top: 1px solid rgba(232, 236, 244, 0.5);
    padding: 25px 35px;
    border-radius: 0 0 var(--border-radius-large) var(--border-radius-large);
    background: rgba(250, 251, 255, 0.8);
}

#invite2FAModal .btn-success {
    background: linear-gradient(135deg, rgba(155, 207, 160, 0.9), rgba(155, 207, 160, 0.8));
    border: none;
    padding: 14px 35px;
    font-weight: 500;
    box-shadow: var(--shadow-soft);
    transition: all 0.4s ease;
    border-radius: 50px;
    color: var(--dark);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

#invite2FAModal .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
    background: linear-gradient(135deg, var(--secondary), #88C888);
    color: white;
}

#invite2FAModal .btn-light {
    border: 1px solid rgba(232, 236, 244, 0.8);
    padding: 12px 24px;
    font-weight: 400;
    transition: all 0.4s ease;
    border-radius: 50px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    color: var(--dark);
}

#invite2FAModal .btn-light:hover {
    background: rgba(255, 255, 255, 1);
    border-color: var(--primary);
    transform: translateY(-1px);
    box-shadow: var(--shadow-soft);
    color: var(--primary);
}

#invite2FAModal .alert-warning {
    border: none;
    background: linear-gradient(135deg, rgba(245, 215, 110, 0.15), rgba(245, 215, 110, 0.1));
    border-left: 4px solid var(--warning);
    border-radius: var(--border-radius);
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    color: rgba(90, 108, 125, 0.9);
}

#invite2FAModal .bg-light {
    background: linear-gradient(135deg, rgba(248, 250, 255, 0.8), rgba(240, 244, 248, 0.8)) !important;
    backdrop-filter: var(--backdrop-blur);
    -webkit-backdrop-filter: var(--backdrop-blur);
    border-radius: var(--border-radius);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Benefits animation */
#invite2FAModal .d-flex.align-items-start {
    animation: slideInLeft 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    animation-fill-mode: both;
}

#invite2FAModal .d-flex.align-items-start:nth-child(1) { animation-delay: 0.15s; }
#invite2FAModal .d-flex.align-items-start:nth-child(2) { animation-delay: 0.3s; }

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Steps animation */
#invite2FAModal .badge.bg-primary {
    animation: bounceIn 1s cubic-bezier(0.4, 0, 0.2, 1);
    animation-fill-mode: both;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
    box-shadow: var(--shadow-soft);
}

#invite2FAModal .col-4:nth-child(1) .badge { animation-delay: 0.4s; }
#invite2FAModal .col-4:nth-child(2) .badge { animation-delay: 0.5s; }
#invite2FAModal .col-4:nth-child(3) .badge { animation-delay: 0.6s; }

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.03);
    }
    70% {
        transform: scale(0.95);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .services-grid-enhanced,
    .services-grid-enhanced.second-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }
    
    .services-grid-enhanced.third-row,
    .services-grid-enhanced.fourth-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        max-width: 800px;
    }
}

@media (max-width: 768px) {
    .notification-bell-container {
        top: 15px;
        right: 15px;
    }
    
    .notification-bell {
        width: 50px;
        height: 50px;
    }
    
    .notification-bell i {
        font-size: 1.3rem;
    }
    
    .notification-dropdown {
        width: 320px;
        right: -10px;
    }
    
    .services-grid-enhanced,
    .services-grid-enhanced.second-row,
    .services-grid-enhanced.third-row,
    .services-grid-enhanced.fourth-row {
        grid-template-columns: 1fr;
        gap: 25px;
        margin-top: 40px;
        padding: 0 10px;
        max-width: none;
    }
    
    .services-grid-enhanced.second-row,
    .services-grid-enhanced.third-row,
    .services-grid-enhanced.fourth-row {
        margin-top: 30px;
    }
    
    .service-card-enhanced {
        height: 450px;
        margin: 0 5px;
    }
    
    .services-grid-enhanced.second-row .service-card-enhanced,
    .services-grid-enhanced.third-row .service-card-enhanced,
    .services-grid-enhanced.fourth-row .service-card-enhanced {
        height: 430px;
    }
    
    .service-card-content {
        padding: 25px;
        padding-bottom: 85px;
    }
    
    .service-action {
        bottom: 25px;
        left: 25px;
        right: 25px;
    }
    
    .service-title {
        font-size: 1.5rem;
        height: 3.6rem;
    }
    
    .service-description {
        font-size: 0.95rem;
        height: 3.1rem;
    }
    
    .service-features {
        min-height: 110px;
        max-height: 110px;
    }
    
    .btn-service-enhanced {
        height: 52px;
        min-height: 52px;
        max-height: 52px;
        padding: 0 20px;
        font-size: 0.95rem;
    }
    
    .btn-icon {
        width: 33px;
        height: 33px;
        min-width: 33px;
        min-height: 33px;
    }
    
    .service-header-section {
        margin-bottom: 20px;
    }
    
    .service-icon-enhanced {
        width: 70px;
        height: 70px;
        font-size: 2rem;
    }
    
    .welcome-section {
        padding: 30px 25px;
        margin: 0 10px 40px;
    }
    
    .user-section {
        margin-bottom: 20px;
    }
    
    .user-greeting {
        padding: 15px 22px;
        font-size: 0.95rem;
    }
    
    .user-greeting-link .bi-arrow-right-circle {
        font-size: 0.9rem;
        margin-left: 8px;
    }
    
    .security-warning {
        padding: 8px 16px;
        font-size: 0.8rem;
        margin-top: 10px;
    }
    
    .bg-pages {
        padding: 30px 0;
        margin-top: -24px;
    }
}

@media (max-width: 480px) {
    .notification-dropdown {
        width: 280px;
        right: -20px;
    }
    
    .welcome-section {
        margin: 0 5px 30px;
        padding: 25px 20px;
    }
    
    .services-grid-enhanced,
    .services-grid-enhanced.second-row,
    .services-grid-enhanced.third-row,
    .services-grid-enhanced.fourth-row {
        gap: 20px;
        padding: 0 5px;
    }
    
    .service-card-enhanced {
        margin: 0;
        height: 420px;
    }
    
    .services-grid-enhanced.second-row .service-card-enhanced,
    .services-grid-enhanced.third-row .service-card-enhanced,
    .services-grid-enhanced.fourth-row .service-card-enhanced {
        height: 420px;
    }
    
    .service-card-content {
        padding: 20px;
        padding-bottom: 80px;
    }
    
    .service-action {
        bottom: 20px;
        left: 20px;
        right: 20px;
    }
    
    .service-header-section {
        margin-bottom: 20px;
    }
    
    .service-icon-enhanced {
        width: 60px;
        height: 60px;
        font-size: 1.8rem;
    }
    
    .service-title {
        font-size: 1.4rem;
        line-height: 1.2;
        height: 3.4rem;
    }
    
    .service-description {
        font-size: 0.9rem;
        margin-bottom: 20px;
        height: 2.9rem;
    }
    
    .service-features {
        min-height: 100px;
        max-height: 100px;
    }
    
    .feature-item {
        font-size: 0.85rem;
        gap: 10px;
        min-height: 22px;
    }
    
    .btn-service-enhanced {
        height: 50px;
        min-height: 50px;
        max-height: 50px;
        padding: 0 18px;
        font-size: 0.9rem;
    }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        min-width: 32px;
        min-height: 32px;
    }
    
    .btn-text {
        max-width: calc(100% - 45px);
    }
    
    .bg-pages {
        padding: 20px 0;
    }
}

/* Smooth scroll and focus states */
html {
    scroll-behavior: smooth;
}

.btn-service-enhanced:focus,
#invite2FAModal .btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 157, 195, 0.3);
}

.btn-service-enhanced:active {
    transform: translateY(-1px) scale(0.98);
}
</style>

<script>
// 🎯 JavaScript เฉพาะสำหรับหน้า Service Systems
// (Session Management จะมาจาก header/footer แล้ว)

// ตัวแปรสำหรับ 2FA modal
let invitationShown = false;

// 🔔 เพิ่ม: ตัวแปรสำหรับ notification
let notificationDropdownOpen = false;

// โหลดข้อมูลเมื่อหน้าโหลดเสร็จ - เฉพาะ 2FA
document.addEventListener('DOMContentLoaded', function() {
    //console.log('Service Systems page ready, initializing 2FA and notifications...');
    
    // *** เพิ่ม Debug ข้อมูล Notification ***
    //console.log('=== E_SERVICE NOTIFICATION DEBUG ===');
    //console.log('Total notifications:', <?php echo $total_notifications; ?>);
    //console.log('Unread count:', <?php echo $unread_count; ?>);
    //console.log('Loaded notifications:', <?php echo count($notifications); ?>);
    //console.log('Notifications data:', <?php echo json_encode($notifications, JSON_UNESCAPED_UNICODE); ?>);
    //console.log('====================================');
    
    // ตรวจสอบสถานะ 2FA และแสดง modal เชิญชวนถ้าจำเป็น
    check2FAStatusAndShowInvitation();
    
    // เริ่มต้นระบบการแจ้งเตือน
    initializeNotifications();
    
    // ปิด notification dropdown เมื่อคลิกที่อื่น
    document.addEventListener('click', function(event) {
        const bellContainer = document.querySelector('.notification-bell-container');
        if (!bellContainer.contains(event.target)) {
            closeNotificationDropdown();
        }
    });
    
    // 🆕 เพิ่ม: Enhanced Card Interactions
    initializeEnhancedCards();
});

// 🆕 เพิ่ม: ฟังก์ชันเริ่มต้น Enhanced Cards
function initializeEnhancedCards() {
    const cards = document.querySelectorAll('.service-card-enhanced');
    
    cards.forEach(card => {
        // เพิ่ม parallax effect เมื่อ mouse move
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            card.style.transform = `translateY(-15px) scale(1.02) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
        
        // รีเซ็ต transform เมื่อ mouse leave
        card.addEventListener('mouseleave', function() {
            card.style.transform = '';
        });
        
        // เพิ่ม ripple effect เมื่อคลิก
        card.addEventListener('click', function(e) {
            if (e.target.closest('.btn-service-enhanced')) {
                createRippleEffect(e.target.closest('.btn-service-enhanced'), e);
            }
        });
    });
}

// 🆕 เพิ่ม: สร้าง Ripple Effect
function createRippleEffect(button, event) {
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    
    ripple.style.position = 'absolute';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.style.width = '0';
    ripple.style.height = '0';
    ripple.style.borderRadius = '50%';
    ripple.style.background = 'rgba(255, 255, 255, 0.6)';
    ripple.style.transform = 'translate(-50%, -50%)';
    ripple.style.animation = 'ripple 0.6s linear';
    ripple.style.pointerEvents = 'none';
    
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// เพิ่ม CSS animation สำหรับ ripple
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            width: 200px;
            height: 200px;
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// 🔔 เพิ่ม: ฟังก์ชันเริ่มต้นระบบการแจ้งเตือน
function initializeNotifications() {
    // ตรวจสอบการแจ้งเตือนใหม่ทุก 30 วินาที
    setInterval(function() {
        refreshNotifications();
    }, 30000);
    
   // console.log('Notifications system initialized');
}

// 🔔 เพิ่ม: ฟังก์ชันสลับการแสดง/ซ่อน notification dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    
    if (notificationDropdownOpen) {
        closeNotificationDropdown();
    } else {
        openNotificationDropdown();
    }
}

// 🔔 เพิ่ม: ฟังก์ชันเปิด notification dropdown
function openNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.add('show');
    notificationDropdownOpen = true;
    
    // ทำเครื่องหมายว่าเปิดดูแล้ว (ถ้าต้องการ)
    markNotificationsAsViewed();
}

// 🔔 เพิ่ม: ฟังก์ชันปิด notification dropdown
function closeNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.remove('show');
    notificationDropdownOpen = false;
}

// 🔔 เพิ่ม: ฟังก์ชันจัดการเมื่อคลิกที่การแจ้งเตือน
function handleNotificationClick(notificationId, url) {
    markNotificationAsRead(notificationId);
    closeNotificationDropdown();
    
    if (url && url !== '' && url !== '#') {
        if (url.startsWith('http') || url.startsWith('//')) {
            window.open(url, '_blank');
        } else {
            // ตรวจสอบว่า URL มี hash fragment หรือไม่
            if (url.includes('#')) {
                const [pagePath, hash] = url.split('#');
                const currentPath = window.location.pathname;
                
                // ถ้าอยู่ในหน้าเดียวกันแล้ว ให้ scroll ไปที่ element
                if (currentPath.endsWith(pagePath) || window.location.href.includes(pagePath)) {
                    scrollToElement(hash);
                } else {
                    // ถ้าไม่ใช่หน้าเดียวกัน ให้ navigate ไปหน้านั้นพร้อม hash
                    const fullUrl = url.startsWith('/') ? url : '/' + url;
                    window.location.href = fullUrl;
                }
            } else {
                // URL ปกติ ไม่มี hash
                const fullUrl = url.startsWith('/') ? url : '/' + url;
                window.location.href = fullUrl;
            }
        }
    }
}

// 🔔 เพิ่ม: ฟังก์ชันทำเครื่องหมายการแจ้งเตือนว่าอ่านแล้ว
function markNotificationAsRead(notificationId) {
    fetch('<?php echo site_url("notifications/mark_as_read"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // อัปเดต UI - ลบ class 'unread'
            const notificationItem = document.querySelector(`[onclick*="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
            }
            
            // อัปเดต badge count
            updateNotificationBadge();
            
            console.log('Notification marked as read:', notificationId);
        } else {
            console.error('Failed to mark notification as read:', data.message);
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// 🔔 เพิ่ม: ฟังก์ชันทำเครื่องหมายว่าดูการแจ้งเตือนแล้ว (ไม่จำเป็นต้องอ่าน)
function markNotificationsAsViewed() {
    // สามารถเพิ่ม logic ตรงนี้ถ้าต้องการติดตาม "ดู" vs "อ่าน"
    //console.log('Notifications viewed');
}

// 🔔 เพิ่ม: ฟังก์ชันรีเฟรชการแจ้งเตือน
function refreshNotifications() {
    fetch('<?php echo site_url("notifications/get_recent"); ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateNotificationList(data.notifications);
            updateNotificationBadge(data.unread_count);
          //  console.log('Notifications refreshed successfully');
        } else {
            console.error('Failed to refresh notifications:', data.message);
        }
    })
    .catch(error => {
        console.error('Error refreshing notifications:', error);
    });
}

// 🔔 เพิ่ม: ฟังก์ชันอัปเดตรายการการแจ้งเตือน
function updateNotificationList(notifications) {
    const notificationList = document.querySelector('.notification-list');
    
    if (!notifications || notifications.length === 0) {
        notificationList.innerHTML = `
            <div class="notification-empty">
                <i class="bi bi-bell-slash"></i>
                <p>ไม่มีการแจ้งเตือน</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    notifications.forEach(notification => {
        // *** แก้ไข: ใช้ is_read_by_user แทน is_read ***
        const isUnread = !notification.is_read_by_user || notification.is_read_by_user == 0;
        
        html += `
            <div class="notification-item ${isUnread ? 'unread' : ''}" 
                 onclick="handleNotificationClick(${notification.notification_id}, '${notification.url || '#'}')">
                <div class="notification-icon">
                    <i class="${notification.icon || 'bi bi-bell'}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${escapeHtml(notification.title)}</div>
                    <div class="notification-message">${escapeHtml(notification.message)}</div>
                    <div class="notification-time">${timeago(notification.created_at)}</div>
                </div>
            </div>
        `;
    });
    
    notificationList.innerHTML = html;
}

function scrollToElement(hash) {
   // console.log('Scrolling to hash:', hash);
    
    const targetElement = document.getElementById(hash);
    if (targetElement) {
        // เพิ่ม highlight effect
        targetElement.style.transition = 'all 0.5s ease';
        targetElement.style.background = 'linear-gradient(135deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0.1) 100%)';
        targetElement.style.border = '2px solid rgba(255, 215, 0, 0.5)';
        targetElement.style.transform = 'scale(1.02)';
        targetElement.style.boxShadow = '0 8px 25px rgba(255, 215, 0, 0.3)';
        
        // เลื่อนไปที่ element
        targetElement.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center',
            inline: 'nearest'
        });
        
        // ลบ highlight หลัง 3 วินาที
        setTimeout(() => {
            targetElement.style.background = '';
            targetElement.style.border = '';
            targetElement.style.transform = '';
            targetElement.style.boxShadow = '';
        }, 3000);
        
       // console.log('✅ Successfully scrolled to element:', hash);
    } else {
        console.warn('❌ Element not found for hash:', hash);
        // Fallback: reload หน้าพร้อม hash
        window.location.href = window.location.pathname + '#' + hash;
    }
}

// 🔔 เพิ่ม: ฟังก์ชันอัปเดต badge การแจ้งเตือน
function updateNotificationBadge(count = null) {
    if (count === null) {
        // ถ้าไม่ได้ส่ง count มา ให้นับจาก UI
        count = document.querySelectorAll('.notification-item.unread').length;
    }
    
    const badge = document.querySelector('.notification-badge');
    const countElement = document.querySelector('.notification-count');
    
    if (count > 0) {
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        }
        if (countElement) {
            countElement.textContent = count + ' ใหม่';
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
        if (countElement) {
            countElement.textContent = '0 ใหม่';
        }
    }
}

// 🔔 เพิ่ม: ฟังก์ชัน escape HTML เพื่อป้องกัน XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 🔔 เพิ่ม: ฟังก์ชันแสดงเวลาแบบ timeago (ง่ายๆ)
function timeago(dateString) {
    try {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'เมื่อสักครู่';
        if (diff < 3600) return Math.floor(diff / 60) + ' นาทีที่แล้ว';
        if (diff < 86400) return Math.floor(diff / 3600) + ' ชั่วโมงที่แล้ว';
        if (diff < 604800) return Math.floor(diff / 86400) + ' วันที่แล้ว';
        
        return date.toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return 'เมื่อสักครู่';
    }
}

// เหลือส่วนของ 2FA functions เหมือนเดิม...
function check2FAStatusAndShowInvitation() {
    // ตรวจสอบว่า user เคยเลือก "ไม่แสดงอีก" หรือไม่ (ใช้ key ใหม่)
    const dontShow = localStorage.getItem('2fa_invite_dont_show_v2_<?php echo $this->session->userdata('mp_id'); ?>');
    if (dontShow === 'true') {
        console.log('User chose not to show invite again (v2)');
        return;
    }

    // *** ล้าง localStorage เก่าที่อาจมีปัญหา ***
    localStorage.removeItem('2fa_invite_dont_show_<?php echo $this->session->userdata('mp_id'); ?>');

    // ตรวจสอบสถานะ 2FA แบบเดียวกับหน้าโปรไฟล์ (ใช้ model เดียวกัน)
    <?php 
    $mp_id = $this->session->userdata('mp_id');
    
    // *** ใช้ model เดียวกับหน้าโปรไฟล์ ***
    $this->load->model('member_public_model');
    $user_2fa_info = $this->member_public_model->get_2fa_info($mp_id);
    
    // เงื่อนไขการตรวจสอบแบบเดียวกับหน้าโปรไฟล์
    $is_2fa_enabled = isset($user_2fa_info) && $user_2fa_info && !empty($user_2fa_info->google2fa_secret) && $user_2fa_info->google2fa_enabled == 1;
    ?>
        // console.log('=== 2FA Status Check ===');
        // console.log('User ID:', '<?php echo $mp_id; ?>');
       // console.log('2FA Info Found:', <?php echo json_encode($user_2fa_info ? true : false); ?>);
       // console.log('Has Secret:', <?php echo json_encode(!empty($user_2fa_info->google2fa_secret) ? true : false); ?>);
       // console.log('2FA Enabled:', <?php echo json_encode($user_2fa_info && $user_2fa_info->google2fa_enabled == 1 ? true : false); ?>);
       // console.log('Final Status:', <?php echo json_encode($is_2fa_enabled); ?>);
       // console.log('========================');
        
        <?php if (!$is_2fa_enabled): ?>
            // ถ้ายังไม่เปิด 2FA ให้แสดง modal เชิญชวน
            setTimeout(function() {
                show2FAInvitation();
            }, 2000); // แสดงหลังจากโหลดหน้าเสร็จ 2 วินาที
        <?php else: ?>
           // console.log('✅ 2FA is already enabled, not showing invitation modal');
        <?php endif; ?>
}

// ฟังก์ชันแสดง modal เชิญชวนใช้ 2FA
function show2FAInvitation() {
    try {
        // ตรวจสอบ Bootstrap
        if (typeof bootstrap === 'undefined') {
            console.log('Bootstrap not loaded');
            return;
        }

        // ตรวจสอบว่าเคยแสดงใน session นี้แล้วหรือไม่
        if (invitationShown) {
            console.log('Invitation already shown in this session');
            return;
        }

        // หา modal element
        const modalElement = document.getElementById('invite2FAModal');
        if (!modalElement) {
            console.log('Modal element not found');
            return;
        }
        
        try {
            // สร้าง Bootstrap modal instance
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            
            // แสดง modal
            modal.show();
            invitationShown = true;
           // console.log('2FA invitation modal shown');
            
        } catch (modalError) {
            console.error('Error creating Bootstrap modal:', modalError);
        }
        
    } catch (error) {
        console.error('Error showing 2FA invitation:', error);
    }
}

function handleDontShowAgain() {
    try {
        const checkbox = document.getElementById('dontShowAgain');
        
        if (checkbox && checkbox.checked) {
            // *** เปลี่ยน key ใหม่เพื่อแก้ปัญหา localStorage เก่า ***
            localStorage.setItem('2fa_invite_dont_show_v2_<?php echo $this->session->userdata('mp_id'); ?>', 'true');
        }
        
        // ปิด modal
        const modalElement = document.getElementById('invite2FAModal');
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    } catch (error) {
        console.error('Error in handleDontShowAgain:', error);
    }
}

// ฟังก์ชันเริ่มต้นการตั้งค่า 2FA จาก modal เชิญชวน (แสดง modal setup เลย)
function goToProfileFor2FA() {
    try {
        // ปิด modal เชิญชวน
        const modalElement = document.getElementById('invite2FAModal');
        if (modalElement) {
            const inviteModal = bootstrap.Modal.getInstance(modalElement);
            if (inviteModal) {
                inviteModal.hide();
            }
        }
        
        // เริ่มต้นการตั้งค่า 2FA เลย
        setTimeout(() => {
            setup2FA();
        }, 500);
        
    } catch (error) {
        console.error('Error in goToProfileFor2FA:', error);
    }
}

// ฟังก์ชัน 2FA - Setup
function setup2FA() {
    console.log('Starting 2FA setup...');
    
    const existingModal = document.getElementById('setup2FAModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const modalHTML = `
    <div class="modal fade" id="setup2FAModal" tabindex="-1" style="z-index: 99999 !important;">
        <div class="modal-dialog modal-lg" style="z-index: 100000 !important;">
            <div class="modal-content" style="z-index: 100001 !important;">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-shield-check"></i> ตั้งค่า Google Authenticator
                    </h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeSetupModal()"></button>
                </div>
                <div class="modal-body">
                    <div id="step1" class="setup-step">
                        <h6><i class="bi bi-1-circle"></i> ขั้นตอนที่ 1: ติดตั้งแอป Google Authenticator</h6>
                        <div class="row text-center mb-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <i class="bi bi-apple" style="font-size: 3rem; color: #007aff;"></i>
                                        <h6>iOS</h6>
                                        <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank" class="btn btn-primary btn-sm">Download</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <i class="bi bi-google-play" style="font-size: 3rem; color: #34a853;"></i>
                                        <h6>Android</h6>
                                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="btn btn-success btn-sm">Download</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">ติดตั้งแล้ว ไปขั้นตอนถัดไป</button>
                        </div>
                    </div>

                    <div id="step2" class="setup-step" style="display: none;">
                        <h6><i class="bi bi-2-circle"></i> ขั้นตอนที่ 2: สแกน QR Code</h6>
                        <div class="text-center mb-3">
                            <div id="qrCodeContainer">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">กำลังโหลด...</span>
                                    </div>
                                    <p class="mt-2">กำลังสร้าง QR Code...</p>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>วิธีการ:</strong>
                            <ol>
                                <li>เปิดแอป Google Authenticator</li>
                                <li>แตะเครื่องหมาย + เพื่อเพิ่มบัญชี</li>
                                <li>เลือก "สแกน QR Code"</li>
                                <li>สแกน QR Code ด้านบน</li>
                            </ol>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-secondary" onclick="nextStep(1)">ย้อนกลับ</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">สแกนแล้ว ไปขั้นตอนถัดไป</button>
                        </div>
                    </div>

                    <div id="step3" class="setup-step" style="display: none;">
                        <h6><i class="bi bi-3-circle"></i> ขั้นตอนที่ 3: ยืนยันรหัส OTP</h6>
                        <div class="alert alert-warning">
                            กรอกรหัส 6 หลักจากแอป Google Authenticator เพื่อยืนยันการตั้งค่า
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">รหัส OTP (6 หลัก)</label>
                                    <input type="text" class="form-control text-center" id="setupOTP" maxlength="6" pattern="\\d{6}" placeholder="000000">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-secondary" onclick="nextStep(2)">ย้อนกลับ</button>
                            <button type="button" class="btn btn-success" onclick="verify2FASetup()">
                                <i class="bi bi-check-circle"></i> ยืนยันและเปิดใช้งาน
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    const modal = new bootstrap.Modal(document.getElementById('setup2FAModal'), {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();
    
    setTimeout(() => {
        nextStep(1);
        generateQRCode();
    }, 500);
}

function closeSetupModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('setup2FAModal'));
    if (modal) {
        modal.hide();
    }
    setTimeout(() => {
        const modalElement = document.getElementById('setup2FAModal');
        if (modalElement) {
            modalElement.remove();
        }
    }, 300);
}

function generateQRCode() {
    const qrContainer = document.getElementById('qrCodeContainer');
    
    if (!qrContainer) {
        console.error('QR Container not found');
        return;
    }

    // แสดง loading
    qrContainer.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">กำลังโหลด...</span>
            </div>
            <p class="mt-2">กำลังสร้าง QR Code...</p>
        </div>
    `;

    console.log('Starting QR code generation...');

    fetch('<?php echo site_url("Auth_public_mem/setup_2fa"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=enable_2fa'
    })
    .then(response => {
       // console.log('Response status:', response.status);
        
        // ตรวจสอบ Content-Type
        const contentType = response.headers.get('content-type');
        console.log('Content-Type:', contentType);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // ตรวจสอบว่าเป็น JSON จริงหรือไม่
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // ถ้าไม่ใช่ JSON ให้ดู response text
            return response.text().then(text => {
                console.error('Expected JSON but got:', text.substring(0, 500));
                throw new Error('Server ส่งข้อมูลที่ไม่ใช่ JSON กลับมา กรุณาตรวจสอบ PHP errors');
            });
        }
    })
    .then(data => {
      //  console.log('Parsed response data:', data);
        
        if (data.status === 'success') {
            qrContainer.innerHTML = `
                <div class="text-center">
                    <img src="${data.qr_code_url}" alt="QR Code" class="img-fluid" style="max-width: 200px;" />
                    <p class="mt-2">
                        <small class="text-muted">หรือใส่รหัสนี้ด้วยตนเอง:</small><br>
                        <strong class="text-primary">${data.secret}</strong>
                    </p>
                    <!-- เพิ่ม hidden input เพื่อเก็บ secret -->
                    <input type="hidden" id="hiddenSecret" value="${data.secret}">
                </div>
            `;
            
            // เก็บ secret สำหรับใช้ในการ verify (2 วิธี เพื่อความแน่ใจ)
            window.tempSecret = data.secret;
            
            console.log('Secret stored:', data.secret);
            
        } else {
            throw new Error(data.message || 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ');
        }
    })
    .catch(error => {
        console.error('Error in generateQRCode:', error);
        
        qrContainer.innerHTML = `
            <div class="alert alert-danger">
                <h6><i class="bi bi-exclamation-triangle"></i> เกิดข้อผิดพลาด</h6>
                <p class="mb-2">${error.message}</p>
                <button class="btn btn-sm btn-outline-danger mt-2" onclick="generateQRCode()">
                    <i class="bi bi-arrow-clockwise"></i> ลองใหม่อีกครั้ง
                </button>
            </div>
        `;
        
        // แสดง error alert ด้วย
        if (typeof showAlert === 'function') {
            showAlert('เกิดข้อผิดพลาดในการสร้าง QR Code: ' + error.message, 'danger');
        }
    });
}

function nextStep(step) {
    document.querySelectorAll('.setup-step').forEach(el => el.style.display = 'none');
    const targetStep = document.getElementById('step' + step);
    if (targetStep) {
        targetStep.style.display = 'block';
    }
}

function verify2FASetup() {
    const otp = document.getElementById('setupOTP').value;
    
    if (otp.length !== 6) {
        if (typeof showAlert === 'function') {
            showAlert('กรุณากรอกรหัส OTP 6 หลัก', 'warning');
        } else {
            alert('กรุณากรอกรหัส OTP 6 หลัก');
        }
        return;
    }

    // ตรวจสอบ secret (ลองหลายวิธี)
    let secret = window.tempSecret;
    
    // ถ้าไม่มีใน window.tempSecret ให้ลองหาจาก hidden input
    if (!secret) {
        const hiddenSecret = document.getElementById('hiddenSecret');
        if (hiddenSecret) {
            secret = hiddenSecret.value;
        }
    }
    
    if (!secret) {
        const errorMsg = 'เกิดข้อผิดพลาด: ไม่พบ secret กรุณาเริ่มต้นใหม่';
        if (typeof showAlert === 'function') {
            showAlert(errorMsg, 'danger');
        } else {
            alert(errorMsg);
        }
        console.error('Secret not found! window.tempSecret:', window.tempSecret);
        return;
    }

    console.log('Verifying OTP with secret:', secret);

    // แสดงสถานะกำลังประมวลผล
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>กำลังตรวจสอบ...';
    submitBtn.disabled = true;

    // เตรียมข้อมูลที่จะส่ง
    const formData = new FormData();
    formData.append('action', 'verify_setup');
    formData.append('otp', otp);
    formData.append('secret', secret);  // *** สำคัญ: ส่ง secret ไปด้วย ***

    fetch('<?php echo site_url("Auth_public_mem/setup_2fa"); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData  // ใช้ FormData แทนการ encode manual
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
       // console.log('Verification response:', data);
        
        if (data.status === 'success') {
            if (typeof showAlert === 'function') {
                showAlert('เปิดใช้งาน 2FA สำเร็จ!', 'success');
            } else {
                alert('เปิดใช้งาน 2FA สำเร็จ!');
            }
            closeSetupModal();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            const errorMsg = data.message || 'รหัส OTP ไม่ถูกต้อง กรุณาลองใหม่';
            if (typeof showAlert === 'function') {
                showAlert(errorMsg, 'danger');
            } else {
                alert(errorMsg);
            }
            
            // คืนค่าปุ่มกลับสู่สถานะเดิม
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // เลือกข้อความใน input OTP เพื่อให้ผู้ใช้พิมพ์ใหม่ได้ง่าย
            document.getElementById('setupOTP').select();
        }
    })
    .catch(error => {
        console.error('Verify error:', error);
        const errorMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message;
        if (typeof showAlert === 'function') {
            showAlert(errorMsg, 'danger');
        } else {
            alert(errorMsg);
        }
        
        // คืนค่าปุ่มกลับสู่สถานะเดิม
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>