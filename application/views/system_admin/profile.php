<style>
    :root {
        --primary: #4A90E2;
        --primary-dark: #357ABD;
        --secondary: #50C878;
        --accent: #F39C12;
        --danger: #E74C3C;
        --warning: #F1C40F;
        --info: #3498DB;
        --success: #28A745;
        --light: #F8F9FA;
        --dark: #2C3E50;
        --border-color: #E9ECEF;
        --shadow: 0 4px 15px rgba(0,0,0,0.1);
        --border-radius: 12px;
    }

    /* Modal z-index fixes */
    .modal {
        z-index: 9999 !important;
    }

    .modal-backdrop {
        z-index: 9998 !important;
    }

    .modal-dialog {
        z-index: 10000 !important;
        position: relative;
    }

    .modal-content {
        position: relative;
        z-index: 10001 !important;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .profile-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        opacity: 0.5;
    }

    .profile-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 5px;
        position: relative;
        z-index: 2;
    }

    .profile-subtitle {
        opacity: 0.9;
        font-size: 1.1rem;
        position: relative;
        z-index: 2;
    }

    .section-card {
        background: var(--light);
        border-radius: var(--border-radius);
        padding: 30px;
        margin-bottom: 25px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .section-card:hover {
        box-shadow: var(--shadow);
        transform: translateY(-2px);
    }

    .section-title {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--dark);
    }

    .section-title i {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        font-size: 1rem;
    }

    .form-label i {
        margin-right: 8px;
        color: var(--primary);
        width: 10px;
    }

    .form-control {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        background: white;
    }

    .input-group {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--primary);
        cursor: pointer;
        z-index: 3;
        padding: 5px;
    }

    .profile-image-section {
        text-align: center;
        margin-bottom: 25px;
    }

    .current-image {
        width: 180px;
        height: 180px;
        border-radius: 15px;
        object-fit: cover;
        border: 4px solid var(--border-color);
        margin-bottom: 15px;
        box-shadow: var(--shadow);
    }

    .file-input-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
        overflow: hidden;
        border-radius: 8px;
        background: var(--primary);
        color: white;
        padding: 12px 24px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .file-input-wrapper:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .btn-modern {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 500;
        border: none;
        transition: all 0.3s ease;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .btn-success-modern {
        background: linear-gradient(135deg, var(--success), #1e7e34);
        color: white;
    }

    .btn-danger-modern {
        background: linear-gradient(135deg, var(--danger), #c82333);
        color: white;
    }

    /* 2FA Styles */
    .twofa-status {
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 2px solid;
    }

    .twofa-enabled {
        background: rgba(40, 167, 69, 0.1);
        border-color: var(--success);
        color: var(--success);
    }

    .twofa-disabled {
        background: rgba(255, 193, 7, 0.1);
        border-color: var(--warning);
        color: #856404;
    }

    .twofa-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .btn-2fa {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-enable-2fa {
        background: var(--success);
        color: white;
    }

    .btn-regenerate-2fa {
        background: var(--warning);
        color: white;
    }

    .btn-disable-2fa {
        background: var(--danger);
        color: white;
    }

    .btn-backup-codes {
        background: var(--info);
        color: white;
    }

    .btn-add-device {
        background: var(--secondary);
        color: white;
    }

    .btn-2fa:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .row.form-row {
        margin-bottom: 20px;
    }

    .form-control-label {
        font-weight: 500;
        color: var(--dark);
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }

    .form-control-label i {
        margin-right: 8px;
        color: var(--primary);
    }

    #email-error {
        font-size: 14px;
        margin-top: 5px;
    }

    .text-danger {
        color: var(--danger) !important;
        font-size: 14px;
    }

    /* ซ่อนข้อความ error สำหรับชื่อ */
    .hide-error {
        display: none !important;
    }

    /* Device count badge */
    .device-count-badge {
        background: linear-gradient(135deg, var(--info), #2980b9);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .device-count-badge:hover {
        background: linear-gradient(135deg, #2980b9, var(--info));
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .qr-code-container {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 10px;
        border: 2px dashed var(--border-color);
        margin: 20px 0;
    }

    /* Device list styles */
    .device-item {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .device-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-color: var(--primary);
    }

    .device-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .device-details h6 {
        margin: 0 0 5px 0;
        color: var(--dark);
        font-weight: 600;
    }

    .device-details p {
        margin: 0;
        font-size: 13px;
        color: #666;
    }

    .device-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 15px;
    }

    .device-desktop {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }

    .device-mobile {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        color: white;
    }

    .device-tablet {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
    }

    .device-unknown {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        color: white;
    }

    .btn-remove-device {
        background: none;
        border: 1px solid var(--danger);
        color: var(--danger);
        padding: 5px 12px;
        border-radius: 5px;
        font-size: 12px;
        transition: all 0.3s ease;
    }

    .btn-remove-device:hover {
        background: var(--danger);
        color: white;
    }

    /* QR Code Countdown Styles */
    .countdown-display {
        font-family: 'Courier New', monospace;
    }

    .progress {
        transition: all 0.3s ease;
    }

    .progress-bar {
        transition: all 0.3s ease;
    }

    #qrCountdownAlert {
        border-left: 4px solid #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #fef5e7 100%);
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .badge.bg-danger {
        animation: pulse 1s infinite;
    }

    @media (max-width: 768px) {
        .section-card {
            padding: 20px;
        }
        
        .twofa-buttons {
            flex-direction: column;
        }
        
        .btn-2fa {
            width: 100%;
            justify-content: center;
        }
    }
	
	.form-label {
    display: flex;
    align-items: center;
}

.form-label i {
    margin-right: 8px;
    color: var(--primary);
    width: 50px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Profile Header -->
            <div class="profile-card">
                <div class="profile-header">
                    <h1 class="profile-title">
                        <i class="bi bi-person-gear me-2"></i>
                        แก้ไขโปรไฟล์
                    </h1>
                    <p class="profile-subtitle">จัดการข้อมูลส่วนตัวและการรักษาความปลอดภัย</p>
                </div>
                
                <div class="p-4">
                    <form action="<?php echo site_url('system_admin/edit_Profile/' . $rsedit->m_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validateForm()">
                        
                        <!-- Profile Image Section -->
                        <div class="section-card">
                            <div class="section-title">
                                <i class="bi bi-image"></i>
                                รูปโปรไฟล์
                            </div>
                            <div class="profile-image-section">
                                <?php $img_path = !empty($rsedit->m_img) ? 'docs/img/' . $rsedit->m_img : 'docs/img/default_user.png'; ?>
                                <img src="<?= base_url($img_path); ?>" class="current-image" alt="รูปโปรไฟล์ปัจจุบัน">
                                <br>
                                <div class="file-input-wrapper">
                                    <i class="bi bi-camera"></i>
                                    เลือกรูปภาพใหม่
                                    <input type="file" name="m_img" accept="image/*">
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information Section -->
                        <div class="section-card">
                            <div class="section-title">
                                <i class="bi bi-person-lines-fill"></i>
                                ข้อมูลพื้นฐาน
                            </div>
                            
                            <div class="row form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-person"></i>
                                            ชื่อผู้ใช้งาน
                                        </label>
                                        <input type="text" name="m_username" class="form-control" value="<?php echo $rsedit->m_username; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
    <div class="form-group">
        <label class="form-label">
            <i class="bi bi-briefcase"></i>
            ตำแหน่งงาน
        </label>
        <input type="text" class="form-control" name="ref_pid" required value="<?php echo $rsedit->pname; ?>" readonly>
        <input type="hidden" name="ref_pid_id" value="<?php echo $rsedit->ref_pid; ?>">
    </div>
</div>
                            </div>

                            <div class="row form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-person-badge"></i>
                                            ชื่อ <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="m_fname" class="form-control" required value="<?php echo $rsedit->m_fname; ?>">
                                        <small class="text-danger hide-error">กรุณากรอกคำนำหน้าชื่อ</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-person-badge-fill"></i>
                                            นามสกุล <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="m_lname" class="form-control" required value="<?php echo $rsedit->m_lname; ?>">
                                        <input type="hidden" name="m_id" value="<?php echo $rsedit->m_id; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-envelope"></i>
                                            อีเมล <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="m_email" id="m_email" class="form-control" required value="<?php echo $rsedit->m_email; ?>">
                                        <div id="email-error" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-phone"></i>
                                            เบอร์มือถือ
                                        </label>
                                        <input type="text" name="m_phone" class="form-control" pattern="\d{9,10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 9 หรือ 10 ตัว" value="<?php echo $rsedit->m_phone; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="section-card">
                            <div class="section-title">
                                <i class="bi bi-key"></i>
                                เปลี่ยนรหัสผ่าน
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>หมายเหตุ:</strong> หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นว่างไว้
                            </div>
                            
                            <div class="row form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-lock"></i>
                                            รหัสผ่านใหม่
                                        </label>
                                        <div class="input-group">
                                            <input type="password" id="m_password" name="current_password" class="form-control" placeholder="เว้นว่างหากไม่ต้องการเปลี่ยน" autocomplete="new-password">
                                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('m_password')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-lock-fill"></i>
                                            ยืนยันรหัสผ่าน
                                        </label>
                                        <div class="input-group">
                                            <input type="password" id="confirm_password" name="current_password2" class="form-control" placeholder="ยืนยันรหัสผ่านใหม่" autocomplete="new-password">
                                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Google Authenticator (2FA) Section -->
                        <div class="section-card">
                            <div class="section-title">
                                <i class="bi bi-shield-check"></i>
                                การยืนยันตัวตนแบบ 2 ขั้นตอน (Google Authenticator)
                            </div>
                            
                            <?php if (empty($rsedit->google2fa_secret) || $rsedit->google2fa_enabled == 0): ?>
                                <!-- 2FA Not Enabled -->
                                <div class="twofa-status twofa-disabled">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h5 class="mb-1">ยังไม่ได้เปิดใช้งาน 2FA</h5>
                                            <p class="mb-0">การยืนยันตัวตนแบบ 2 ขั้นตอนจะช่วยเพิ่มความปลอดภัยให้กับบัญชีของคุณ</p>
                                        </div>
                                    </div>
                                    <div class="twofa-buttons">
                                        <button type="button" class="btn-2fa btn-enable-2fa" onclick="setup2FA()">
                                            <i class="bi bi-plus-circle"></i>
                                            เปิดใช้งาน 2FA
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- 2FA Enabled -->
                                <div class="twofa-status twofa-enabled">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-shield-check-fill me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h5 class="mb-1">เปิดใช้งาน 2FA แล้ว</h5>
                                            <p class="mb-0">บัญชีของคุณได้รับการปกป้องด้วยการยืนยันตัวตนแบบ 2 ขั้นตอน</p>
                                        </div>
                                    </div>
                                    
                                    <!-- แสดงจำนวนอุปกรณ์ -->
                                    <div class="device-count-badge" id="deviceCountBadge" onclick="showDeviceList()" style="cursor: pointer;" title="คลิกเพื่อดูรายการอุปกรณ์">
                                        <i class="bi bi-phone"></i>
                                        <span id="deviceCount">กำลังโหลด...</span> อุปกรณ์ที่ลงทะเบียน
                                    </div>
                                    
                                    <div class="twofa-buttons">
                                        <button type="button" class="btn-2fa btn-add-device" onclick="showQRCodeForNewDevice()">
                                            <i class="bi bi-plus-circle"></i>
                                            เพิ่มอุปกรณ์ใหม่
                                        </button>
                                        <button type="button" class="btn-2fa btn-regenerate-2fa" onclick="regenerate2FA()">
                                            <i class="bi bi-arrow-clockwise"></i>
                                            สร้างรหัสใหม่
                                        </button>
                                        <button type="button" class="btn-2fa btn-backup-codes" onclick="show2FABackupCodes()">
                                            <i class="bi bi-key"></i>
                                            ดู Backup Codes
                                        </button>
                                        <button type="button" class="btn-2fa btn-disable-2fa" onclick="disable2FA()">
                                            <i class="bi bi-x-circle"></i>
                                            ปิดใช้งาน 2FA
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- 2FA Info Box -->
                            <div class="alert alert-info mt-3" role="alert">
                                <h6 class="alert-heading">
                                    <i class="bi bi-info-circle me-2"></i>
                                    เกี่ยวกับ Google Authenticator
                                </h6>
                                <p class="mb-2">
                                    Google Authenticator เป็นแอปพลิเคชันที่ช่วยเพิ่มความปลอดภัยให้กับบัญชีของคุณ โดยสร้างรหัสยืนยัน 6 หลักที่เปลี่ยนแปลงทุก 30 วินาที ทำให้แม้จะมีคนรู้รหัสผ่านของคุณ ก็ไม่สามารถเข้าใช้งานได้หากไม่มีรหัสจากมือถือของคุณ
                                </p>
                                <p class="mb-0">
                                    <strong>💡 เทคนิค:</strong> คุณสามารถเพิ่มบัญชีเดียวกันในหลายอุปกรณ์ได้ เพื่อเป็น backup กรณีมือถือหลักสูญหาย
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="section-card">
                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <button type="submit" class="btn-modern btn-success-modern">
                                    <i class="bi bi-check-circle"></i>
                                    บันทึกข้อมูล
                                </button>
                                <button type="button" class="btn-modern btn-danger-modern" onclick="history.back();">
                                    <i class="bi bi-x-circle"></i>
                                    ยกเลิก
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับแสดงรายการอุปกรณ์ที่ลงทะเบียน -->
<div class="modal fade" id="deviceListModal" tabindex="-1" aria-labelledby="deviceListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="deviceListModalLabel">
                    <i class="bi bi-devices"></i> อุปกรณ์ที่ลงทะเบียน Google Authenticator
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>หมายเหตุ:</strong> รายการนี้แสดงอุปกรณ์ที่เคยใช้ 2FA เข้าสู่ระบบ หรือที่คุณจำไว้ 30 วัน
                </div>
                
                <div id="deviceListContainer">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">กำลังโหลด...</span>
                        </div>
                        <p class="mt-2">กำลังดึงรายการอุปกรณ์...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" onclick="removeAllDevices()">
                    <i class="bi bi-trash"></i> ลบทั้งหมด
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="refreshDeviceList()">
                    <i class="bi bi-arrow-clockwise"></i> รีเฟรช
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับแสดง QR Code เพื่อเพิ่มอุปกรณ์ใหม่ พร้อม Countdown -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addDeviceModalLabel">
                    <i class="bi bi-plus-circle"></i> เพิ่มอุปกรณ์ใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Countdown Timer -->
                <div class="alert alert-warning" id="qrCountdownAlert">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-clock me-2"></i>
                            <strong>QR Code หมดอายุใน:</strong>
                        </div>
                        <div class="countdown-display">
                            <span id="countdownTimer" class="badge bg-success fs-6">10:00</span>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar bg-success" id="countdownProgress" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>หมายเหตุ:</strong> คุณสามารถเพิ่มบัญชี Google Authenticator เดียวกันในหลายอุปกรณ์ได้ โดยสแกน QR Code เดียวกันนี้
                </div>
                
                <div class="qr-code-container" id="qrCodeDisplay">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">กำลังโหลด...</span>
                        </div>
                        <p class="mt-2">กำลังสร้าง QR Code...</p>
                    </div>
                </div>
                
                <!-- QR Code Expired Section -->
                <div id="qrExpiredSection" class="text-center" style="display: none;">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>QR Code หมดอายุแล้ว!</strong>
                        <p class="mt-2 mb-0">เพื่อความปลอดภัย กรุณาขอ QR Code ใหม่</p>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="refreshQRCode()">
                        <i class="bi bi-arrow-clockwise"></i> ขอ QR Code ใหม่
                    </button>
                </div>
                
                <div class="text-center mb-3">
                    <strong>วิธีการเพิ่มอุปกรณ์:</strong>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-qr-code-scan" style="font-size: 2rem; color: var(--primary);"></i>
                                <h6 class="mt-2">วิธีที่ 1: สแกน QR Code</h6>
                                <ol class="text-start small">
                                    <li>เปิดแอป Google Authenticator ในอุปกรณ์ใหม่</li>
                                    <li>แตะเครื่องหมาย + เพื่อเพิ่มบัญชี</li>
                                    <li>เลือก "สแกน QR Code"</li>
                                    <li>สแกน QR Code ด้านบน</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-secondary h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-keyboard" style="font-size: 2rem; color: var(--secondary);"></i>
                                <h6 class="mt-2">วิธีที่ 2: ใส่รหัสด้วยตนเอง</h6>
                                <p class="small">Secret Key:</p>
                                <code id="secretKeyDisplay" class="d-block p-2 bg-light small">กำลังโหลด...</code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>สำคัญ:</strong> หลังจากเพิ่มอุปกรณ์แล้ว รหัส OTP ที่แสดงในทุกอุปกรณ์จะเหมือนกันและเปลี่ยนแปลงพร้อมกันทุก 30 วินาที
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-warning" onclick="refreshQRCode()">
                    <i class="bi bi-arrow-clockwise"></i>
                    รีเฟรช QR Code
                </button>
                <button type="button" class="btn btn-success" onclick="refreshDeviceCount()">
                    <i class="bi bi-arrow-clockwise"></i>
                    อัปเดตจำนวนอุปกรณ์
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับ Backup Codes -->
<div class="modal fade" id="backupCodesModal" tabindex="-1" aria-labelledby="backupCodesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="backupCodesModalLabel">
                    <i class="bi bi-key"></i> Backup Codes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>สำคัญ!</strong> เก็บรหัสเหล่านี้ไว้ในที่ปลอดภัย ใช้ได้เมื่อมือถือสูญหาย
                </div>
                <div id="backupCodesList">
                    <!-- Backup codes จะถูกแสดงที่นี่ -->
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary" onclick="downloadBackupCodes()">
                        <i class="bi bi-download"></i> ดาวน์โหลด
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="printBackupCodes()">
                        <i class="bi bi-printer"></i> พิมพ์
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับสร้างรหัส 2FA ใหม่ -->
<div class="modal fade" id="regenerate2FAModal" tabindex="-1" aria-labelledby="regenerate2FAModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="regenerate2FAModalLabel">
                    <i class="bi bi-arrow-clockwise"></i> สร้างรหัส 2FA ใหม่
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>คำเตือน!</strong> การสร้างรหัส 2FA ใหม่จะทำให้รหัสเก่าไม่สามารถใช้ได้อีกต่อไป
                </div>
                <p>คุณต้องการสร้างรหัส 2FA ใหม่หรือไม่? ซึ่งจะต้อง:</p>
                <ul>
                    <li>ลบบัญชีเก่าออกจาก Google Authenticator ในทุกอุปกรณ์</li>
                    <li>สแกน QR Code ใหม่ในทุกอุปกรณ์</li>
                    <li>ยืนยันรหัส OTP ใหม่</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning" onclick="confirmRegenerate2FA()">
                    <i class="bi bi-arrow-clockwise"></i> สร้างรหัสใหม่
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับปิดใช้งาน 2FA -->
<div class="modal fade" id="disable2FAModal" tabindex="-1" aria-labelledby="disable2FAModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="disable2FAModalLabel">
                    <i class="bi bi-x-circle"></i> ปิดใช้งาน 2FA
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>คำเตือน!</strong> การปิดใช้งาน 2FA จะลดระดับความปลอดภัยของบัญชีของคุณ
                </div>
                <p>การปิดใช้งาน 2FA จะส่งผลให้:</p>
                <ul>
                    <li>บัญชีของคุณมีความปลอดภัยน้อยลง</li>
                    <li>ไม่ต้องใช้รหัส OTP ในการเข้าสู่ระบบ</li>
                    <li>สามารถเข้าสู่ระบบได้ด้วยรหัสผ่านเท่านั้น</li>
                </ul>
                <div class="form-group mt-3">
                    <label class="form-label">กรุณาพิมพ์ <strong>"ยืนยันการปิดใช้งาน"</strong> เพื่อยืนยัน:</label>
                    <input type="text" class="form-control" id="disableConfirmText" placeholder="พิมพ์ข้อความยืนยัน">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDisable2FA" disabled onclick="confirmDisable2FA()">
                    <i class="bi bi-x-circle"></i> ปิดใช้งาน 2FA
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ตัวแปรสำหรับ countdown
    let countdownInterval;
    let qrSessionKey;
    let qrExpiresAt;

    // โหลดจำนวนอุปกรณ์เมื่อหน้าโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($rsedit->google2fa_secret) && $rsedit->google2fa_enabled == 1): ?>
        refreshDeviceCount();
        <?php endif; ?>
    });

    // Email validation
    document.addEventListener('DOMContentLoaded', function() {
        var emailInput = document.getElementById('m_email');
        var emailError = document.getElementById('email-error');
        var originalEmail = '<?php echo $rsedit->m_email; ?>';
        var submitButton = document.querySelector('button[type="submit"]');

        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                var email = this.value;
                if (email !== originalEmail) {
                    fetch('<?php echo site_url("member_backend/check_email"); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'email=' + encodeURIComponent(email)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                emailError.textContent = 'อีเมลนี้มีอยู่ในระบบแล้ว';
                                submitButton.disabled = true;
                            } else {
                                emailError.textContent = '';
                                submitButton.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                } else {
                    emailError.textContent = '';
                    submitButton.disabled = false;
                }
            });
        }
    });

    // Password visibility toggle
    function togglePasswordVisibility(inputId) {
        var input = document.getElementById(inputId);
        if (input) {
            var icon = input.nextElementSibling.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    }

    // Form validation
    function validateForm() {
        var password = document.getElementById("m_password").value;
        var confirmPassword = document.getElementById("confirm_password").value;

        if (password !== "" || confirmPassword !== "") {
            if (password !== confirmPassword) {
                alert("รหัสผ่านไม่ตรงกัน กรุณากรอกใหม่");
                return false;
            }
            
            if (password.length < 6) {
                alert("รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร");
                return false;
            }
        }
        
        return true;
    }

    // ฟังก์ชันแสดงรายการอุปกรณ์
    function showDeviceList() {
        console.log('Showing device list...');
        
        const modal = new bootstrap.Modal(document.getElementById('deviceListModal'));
        modal.show();
        
        loadDeviceList();
    }

    // ฟังก์ชันโหลดรายการอุปกรณ์
    function loadDeviceList() {
        fetch('<?php echo site_url("system_admin/get_device_list"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>'
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('deviceListContainer');
            
            if (data.success && data.devices && data.devices.length > 0) {
                let deviceHTML = '';
                
                data.devices.forEach((device, index) => {
                    const deviceIcon = getDeviceIcon(device.platform);
                    const deviceType = getDeviceType(device.platform);
                    const statusBadge = device.is_expired ? 
                        '<span class="badge bg-danger">หมดอายุ</span>' : 
                        '<span class="badge bg-success">ใช้งานได้</span>';
                    
                    deviceHTML += `
                        <div class="device-item">
                            <div class="device-info">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="device-icon device-${deviceType}">
                                        <i class="bi ${deviceIcon}"></i>
                                    </div>
                                    <div class="device-details">
                                        <h6>${device.browser} ${device.version} บน ${device.platform}</h6>
                                        <p><i class="bi bi-geo-alt"></i> IP: ${device.ip_address}</p>
                                        <p><i class="bi bi-clock"></i> ใช้ล่าสุด: ${formatDateTime(device.last_used_at)}</p>
                                        <p><i class="bi bi-calendar-plus"></i> เพิ่มเมื่อ: ${formatDateTime(device.created_at)}</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    ${statusBadge}
                                    <button class="btn-remove-device mt-2" onclick="removeDevice(${device.id})" title="ลบอุปกรณ์นี้">
                                        <i class="bi bi-trash"></i> ลบ
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = deviceHTML;
            } else {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-phone-x" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">ไม่พบอุปกรณ์ที่ลงทะเบียน</h5>
                        <p class="text-muted">ยังไม่มีอุปกรณ์ที่เคยใช้งาน 2FA หรือข้อมูลหมดอายุแล้ว</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading device list:', error);
            document.getElementById('deviceListContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    เกิดข้อผิดพลาดในการโหลดรายการอุปกรณ์: ${error.message}
                </div>
            `;
        });
    }

    // ฟังก์ชันรีเฟรชรายการอุปกรณ์
    function refreshDeviceList() {
        const container = document.getElementById('deviceListContainer');
        container.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <p class="mt-2">กำลังรีเฟรชรายการอุปกรณ์...</p>
            </div>
        `;
        
        setTimeout(() => {
            loadDeviceList();
            refreshDeviceCount();
        }, 500);
    }

    // ฟังก์ชันลบอุปกรณ์
    function removeDevice(deviceId) {
        if (!confirm('คุณต้องการลบอุปกรณ์นี้หรือไม่?')) {
            return;
        }
        
        fetch('<?php echo site_url("system_admin/remove_device"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `device_id=${deviceId}&user_id=<?php echo $rsedit->m_id; ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show';
                successAlert.innerHTML = `
                    <i class="bi bi-check-circle"></i> ลบอุปกรณ์สำเร็จ
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('#deviceListModal .modal-body').prepend(successAlert);
                
                setTimeout(() => {
                    refreshDeviceList();
                }, 1000);
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถลบอุปกรณ์ได้'));
            }
        })
        .catch(error => {
            console.error('Error removing device:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
        });
    }

    // ฟังก์ชันลบอุปกรณ์ทั้งหมด
    function removeAllDevices() {
        if (!confirm('คุณต้องการลบอุปกรณ์ทั้งหมดหรือไม่?\n\nการกระทำนี้จะทำให้คุณต้องเข้าสู่ระบบด้วย 2FA ทุกครั้ง')) {
            return;
        }
        
        fetch('<?php echo site_url("system_admin/remove_all_devices"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `user_id=<?php echo $rsedit->m_id; ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show';
                successAlert.innerHTML = `
                    <i class="bi bi-check-circle"></i> ลบอุปกรณ์ทั้งหมดสำเร็จ
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('#deviceListModal .modal-body').prepend(successAlert);
                
                setTimeout(() => {
                    refreshDeviceList();
                }, 1000);
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถลบอุปกรณ์ได้'));
            }
        })
        .catch(error => {
            console.error('Error removing all devices:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
        });
    }

    // ฟังก์ชันช่วยเหลือ
    function getDeviceIcon(platform) {
        platform = platform.toLowerCase();
        if (platform.includes('windows')) return 'bi-windows';
        if (platform.includes('mac') || platform.includes('darwin')) return 'bi-apple';
        if (platform.includes('linux')) return 'bi-ubuntu';
        if (platform.includes('android')) return 'bi-android2';
        if (platform.includes('ios') || platform.includes('iphone') || platform.includes('ipad')) return 'bi-phone';
        return 'bi-device-hdd';
    }

    function getDeviceType(platform) {
        platform = platform.toLowerCase();
        if (platform.includes('mobile') || platform.includes('android') || platform.includes('ios') || platform.includes('iphone')) return 'mobile';
        if (platform.includes('tablet') || platform.includes('ipad')) return 'tablet';
        if (platform.includes('windows') || platform.includes('mac') || platform.includes('linux')) return 'desktop';
        return 'unknown';
    }

    function formatDateTime(dateString) {
        if (!dateString) return 'ไม่ทราบ';
        const date = new Date(dateString);
        return date.toLocaleString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // ฟังก์ชันรีเฟรชจำนวนอุปกรณ์
    function refreshDeviceCount() {
        console.log('Refreshing device count...');
        
        fetch('<?php echo site_url("system_admin/get_device_count"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>'
        })
        .then(response => response.json())
        .then(data => {
            const deviceCountElement = document.getElementById('deviceCount');
            if (data.success) {
                deviceCountElement.textContent = data.count;
                
                const badge = document.getElementById('deviceCountBadge');
                if (data.count >= 3) {
                    badge.style.background = 'linear-gradient(135deg, var(--success), #1e7e34)';
                } else if (data.count >= 2) {
                    badge.style.background = 'linear-gradient(135deg, var(--warning), #e0a800)';
                } else {
                    badge.style.background = 'linear-gradient(135deg, var(--info), #2980b9)';
                }
            } else {
                deviceCountElement.textContent = 'ไม่ทราบ';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('deviceCount').textContent = 'ไม่ทราบ';
        });
    }

    // ฟังก์ชันแสดง QR Code สำหรับเพิ่มอุปกรณ์ใหม่ พร้อม Countdown
    function showQRCodeForNewDevice() {
        console.log('Showing QR Code for new device...');
        
        const modal = new bootstrap.Modal(document.getElementById('addDeviceModal'));
        modal.show();
        
        createQRSession();
    }

    // ฟังก์ชันสร้าง QR session
    function createQRSession() {
        fetch('<?php echo site_url("system_admin/create_qr_session"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>&domain=<?php echo $_SERVER['HTTP_HOST']; ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                qrSessionKey = data.session_key;
                qrExpiresAt = Date.now() + (data.expires_in * 1000);
                
                startCountdown(data.expires_in);
                loadQRCodeWithSession();
            } else {
                console.error('Failed to create QR session:', data.message);
                loadQRCodeWithSession();
            }
        })
        .catch(error => {
            console.error('Error creating QR session:', error);
            loadQRCodeWithSession();
        });
    }

    // ฟังก์ชันโหลด QR Code พร้อม session
    function loadQRCodeWithSession() {
        const requestBody = 'user_id=<?php echo $rsedit->m_id; ?>&domain=<?php echo $_SERVER['HTTP_HOST']; ?>' + 
                           (qrSessionKey ? '&session_key=' + encodeURIComponent(qrSessionKey) : '');
        
        fetch('<?php echo site_url("system_admin/get_existing_qr_code"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: requestBody
        })
        .then(response => response.json())
        .then(data => {
            const qrContainer = document.getElementById('qrCodeDisplay');
            const secretDisplay = document.getElementById('secretKeyDisplay');
            const expiredSection = document.getElementById('qrExpiredSection');
            
            if (data.success) {
                qrContainer.innerHTML = 
                    '<img src="' + data.qr_code + '" alt="QR Code" class="img-fluid" style="max-width: 200px;">' +
                    '<p class="mt-2 text-muted small">สแกน QR Code นี้ด้วยอุปกรณ์ใหม่</p>';
                
                secretDisplay.textContent = data.secret;
                
                expiredSection.style.display = 'none';
                qrContainer.style.display = 'block';
                
                if (data.remaining_time) {
                    qrExpiresAt = Date.now() + (data.remaining_time * 1000);
                    startCountdown(data.remaining_time);
                }
                
            } else if (data.expired) {
                showExpiredQR();
            } else {
                qrContainer.innerHTML = 
                    '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถโหลด QR Code ได้') + '</div>';
                
                secretDisplay.textContent = 'ไม่สามารถโหลดได้';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('qrCodeDisplay').innerHTML = 
                '<div class="alert alert-danger">เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message + '</div>';
            
            document.getElementById('secretKeyDisplay').textContent = 'เกิดข้อผิดพลาด';
        });
    }

    // ฟังก์ชันเริ่ม countdown
    function startCountdown(seconds) {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        let remainingSeconds = seconds;
        const totalSeconds = seconds;
        
        countdownInterval = setInterval(() => {
            remainingSeconds--;
            
            const minutes = Math.floor(remainingSeconds / 60);
            const secs = remainingSeconds % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            
            const timerElement = document.getElementById('countdownTimer');
            const progressElement = document.getElementById('countdownProgress');
            
            if (timerElement) {
                timerElement.textContent = timeString;
                
                if (remainingSeconds <= 60) {
                    timerElement.className = 'badge bg-danger fs-6';
                } else if (remainingSeconds <= 180) {
                    timerElement.className = 'badge bg-warning fs-6';
                } else {
                    timerElement.className = 'badge bg-success fs-6';
                }
            }
            
            if (progressElement) {
                const progress = (remainingSeconds / totalSeconds) * 100;
                progressElement.style.width = progress + '%';
                
                if (progress <= 20) {
                    progressElement.className = 'progress-bar bg-danger';
                } else if (progress <= 50) {
                    progressElement.className = 'progress-bar bg-warning';
                } else {
                    progressElement.className = 'progress-bar bg-success';
                }
            }
            
            if (remainingSeconds <= 0) {
                clearInterval(countdownInterval);
                showExpiredQR();
            }
        }, 1000);
    }

    // ฟังก์ชันแสดง QR Code หมดอายุ
    function showExpiredQR() {
        document.getElementById('qrCodeDisplay').style.display = 'none';
        document.getElementById('qrExpiredSection').style.display = 'block';
        
        const timerElement = document.getElementById('countdownTimer');
        if (timerElement) {
            timerElement.textContent = '00:00';
            timerElement.className = 'badge bg-danger fs-6';
        }
        
        const progressElement = document.getElementById('countdownProgress');
        if (progressElement) {
            progressElement.style.width = '0%';
            progressElement.className = 'progress-bar bg-danger';
        }
    }

    // ฟังก์ชันรีเฟรช QR Code
    function refreshQRCode() {
        console.log('Refreshing QR Code...');
        
        document.getElementById('qrCodeDisplay').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังสร้าง QR Code ใหม่...</span>
                </div>
                <p class="mt-2">กำลังสร้าง QR Code ใหม่...</p>
            </div>
        `;
        
        document.getElementById('qrCodeDisplay').style.display = 'block';
        document.getElementById('qrExpiredSection').style.display = 'none';
        
        if (qrSessionKey) {
            fetch('<?php echo site_url("system_admin/refresh_qr_session"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `user_id=<?php echo $rsedit->m_id; ?>&domain=<?php echo $_SERVER['HTTP_HOST']; ?>&old_session_key=${encodeURIComponent(qrSessionKey)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    qrSessionKey = data.session_key;
                    qrExpiresAt = Date.now() + (data.expires_in * 1000);
                    startCountdown(data.expires_in);
                }
                
                setTimeout(() => {
                    loadQRCodeWithSession();
                }, 500);
            })
            .catch(error => {
                console.error('Error refreshing QR session:', error);
                setTimeout(() => {
                    loadQRCodeWithSession();
                }, 500);
            });
        } else {
            createQRSession();
        }
    }

    // ล้าง interval เมื่อปิด modal
    document.getElementById('addDeviceModal').addEventListener('hidden.bs.modal', function () {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    });

    // 2FA Functions
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
                        <button type="button" class="btn-close btn-close-white" onclick="closeModal()"></button>
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
            nextStep(2);
            generateQRCode();
        }, 500);
    }

    function closeModal() {
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
        fetch('<?php echo site_url("system_admin/generate_2fa_secret"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>&domain=<?php echo $_SERVER['HTTP_HOST']; ?>'
        })
        .then(response => response.json())
        .then(data => {
            const qrContainer = document.getElementById('qrCodeContainer');
            if (data.success) {
                qrContainer.innerHTML = 
                    '<img src="' + data.qr_code + '" alt="QR Code" class="img-fluid" style="max-width: 200px;">' +
                    '<p class="mt-2"><small>หรือใส่รหัสนี้ด้วยตนเอง: <strong>' + data.secret + '</strong></small></p>';
            } else {
                qrContainer.innerHTML = 
                    '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถสร้าง QR Code ได้') + '</div>';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            const qrContainer = document.getElementById('qrCodeContainer');
            if (qrContainer) {
                qrContainer.innerHTML = 
                    '<div class="alert alert-danger">เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message + '</div>';
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
            alert('กรุณากรอกรหัส OTP 6 หลัก');
            return;
        }

        fetch('<?php echo site_url("system_admin/verify_2fa_setup"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>&otp=' + encodeURIComponent(otp)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('เปิดใช้งาน 2FA สำเร็จ!');
                closeModal();
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                alert('รหัส OTP ไม่ถูกต้อง กรุณาลองใหม่');
            }
        })
        .catch(error => {
            console.error('Verify error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
        });
    }

    function regenerate2FA() {
        const modal = new bootstrap.Modal(document.getElementById('regenerate2FAModal'));
        modal.show();
    }

    function confirmRegenerate2FA() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('regenerate2FAModal'));
        modal.hide();
        
        setTimeout(() => {
            setup2FA();
        }, 300);
    }

    function disable2FA() {
        const modal = new bootstrap.Modal(document.getElementById('disable2FAModal'));
        modal.show();
        
        const confirmInput = document.getElementById('disableConfirmText');
        const confirmButton = document.getElementById('confirmDisable2FA');
        
        confirmInput.addEventListener('input', function() {
            if (this.value === 'ยืนยันการปิดใช้งาน') {
                confirmButton.disabled = false;
            } else {
                confirmButton.disabled = true;
            }
        });
    }

    function confirmDisable2FA() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('disable2FAModal'));
        modal.hide();
        
        const loadingAlert = document.createElement('div');
        loadingAlert.className = 'alert alert-info position-fixed';
        loadingAlert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        loadingAlert.innerHTML = '<i class="bi bi-gear-fill"></i> กำลังปิดใช้งาน 2FA...';
        document.body.appendChild(loadingAlert);
        
        fetch('<?php echo site_url("system_admin/disable_2fa"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>'
        })
        .then(response => response.json())
        .then(data => {
            loadingAlert.remove();
            
            if (data.success) {
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success position-fixed';
                successAlert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                successAlert.innerHTML = '<i class="bi bi-check-circle"></i> ปิดใช้งาน 2FA สำเร็จ กำลังรีเฟรชหน้า...';
                document.body.appendChild(successAlert);
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถปิดใช้งาน 2FA ได้'));
            }
        })
        .catch(error => {
            loadingAlert.remove();
            console.error('Disable error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
        });
    }

    function show2FABackupCodes() {
        const loadingAlert = document.createElement('div');
        loadingAlert.className = 'alert alert-info position-fixed';
        loadingAlert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        loadingAlert.innerHTML = '<i class="bi bi-gear-fill"></i> กำลังดึงข้อมูล Backup Codes...';
        document.body.appendChild(loadingAlert);
        
        fetch('<?php echo site_url("system_admin/get_backup_codes"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>'
        })
        .then(response => response.json())
        .then(data => {
            loadingAlert.remove();
            
            if (data.success) {
                let codesList = '<div class="row">';
                data.codes.forEach((code, index) => {
                    codesList += `<div class="col-md-6"><code class="d-block p-2 mb-2 bg-light">${code}</code></div>`;
                });
                codesList += '</div>';
                
                document.getElementById('backupCodesList').innerHTML = codesList;
                const modal = new bootstrap.Modal(document.getElementById('backupCodesModal'));
                modal.show();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถดึง backup codes ได้'));
            }
        })
        .catch(error => {
            loadingAlert.remove();
            console.error('Backup codes error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
        });
    }

    function downloadBackupCodes() {
        const codes = document.querySelectorAll('#backupCodesList code');
        let content = 'Google Authenticator Backup Codes\n';
        content += '=====================================\n\n';
        codes.forEach((code, index) => {
            content += `${index + 1}. ${code.textContent}\n`;
        });
        content += '\n⚠️ เก็บรหัสเหล่านี้ไว้ในที่ปลอดภัย';
        
        const blob = new Blob([content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '2FA_Backup_Codes.txt';
        a.click();
        window.URL.revokeObjectURL(url);
    }

    function printBackupCodes() {
        const printContent = document.getElementById('backupCodesList').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>2FA Backup Codes</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        h1 { text-align: center; }
                        code { background: #f5f5f5; padding: 10px; display: block; margin: 5px 0; }
                        .warning { color: red; font-weight: bold; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <h1>Google Authenticator Backup Codes</h1>
                    ${printContent}
                    <div class="warning">⚠️ เก็บรหัสเหล่านี้ไว้ในที่ปลอดภัย</div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
</script>