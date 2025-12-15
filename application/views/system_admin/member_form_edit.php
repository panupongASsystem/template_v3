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

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Kanit', sans-serif;
        min-height: 100vh;
    }

    .main-container {
        max-width: 1200px;
        margin: 20px auto;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 30px 40px;
        position: relative;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        opacity: 0.5;
    }

    .page-title {
        font-size: 2.2rem;
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-subtitle {
        opacity: 0.9;
        font-size: 1.1rem;
        margin: 10px 0 0 0;
        position: relative;
        z-index: 2;
    }

    .form-container {
        padding: 40px;
    }

    .section-divider {
        background: var(--light);
        border-radius: var(--border-radius);
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .section-divider:hover {
        box-shadow: var(--shadow);
        transform: translateY(-2px);
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--dark);
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 15px;
    }

    .section-header i {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.3rem;
    }

    .form-row {
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
        width: 20px;
    }

    .form-control {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: white;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        background: white;
        outline: none;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: center;
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
        width: 200px;
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
        margin-top: 10px;
    }

    .file-input-wrapper:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: -9999px;
    }

    .system-selector {
        background: white;
        border: 2px solid var(--primary);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .permission-item {
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .permission-item:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow);
    }

    .permission-item.selected {
        border-color: var(--success);
        background: rgba(40, 167, 69, 0.05);
    }

    .permission-checkbox {
        margin-right: 10px;
        transform: scale(1.2);
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
        cursor: pointer;
        text-decoration: none;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        text-decoration: none;
    }

    .btn-success-modern {
        background: linear-gradient(135deg, var(--success), #1e7e34);
        color: white;
    }

    .btn-danger-modern {
        background: linear-gradient(135deg, var(--danger), #c82333);
        color: white;
    }

    .btn-info-modern {
        background: linear-gradient(135deg, var(--info), #0b5394);
        color: white;
    }

    /* 2FA Styles */
    .twofa-section {
        background: linear-gradient(135deg, rgba(74, 144, 226, 0.05), rgba(53, 122, 189, 0.05));
        border: 2px solid var(--primary);
        border-radius: 12px;
        padding: 25px;
        margin-top: 20px;
    }

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

    .btn-2fa:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .hidden {
        display: none !important;
    }

    .text-danger {
        color: var(--danger) !important;
        font-size: 14px;
    }

    .red {
        color: var(--danger);
        font-size: 14px;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid transparent;
    }

    .alert-info {
        background: rgba(52, 152, 219, 0.1);
        border-color: var(--info);
        color: #1f5582;
    }

    .alert-warning {
        background: rgba(255, 193, 7, 0.1);
        border-color: var(--warning);
        color: #856404;
    }

    .action-buttons {
        text-align: center;
        padding: 30px;
        background: var(--light);
        border-top: 1px solid var(--border-color);
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 20px;
        }
        
        .permission-grid {
            grid-template-columns: 1fr;
        }
        
        .twofa-buttons {
            flex-direction: column;
        }
        
        .btn-2fa {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="main-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-person-gear"></i>
            แก้ไขข้อมูลสมาชิก
        </h1>
        <p class="page-subtitle">จัดการข้อมูลส่วนตัว สิทธิ์การเข้าถึง และการรักษาความปลอดภัย</p>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form id="memberForm" action="member_backend/edit_Member/1" method="post" class="form-horizontal" enctype="multipart/form-data" autocomplete="off">
            
            <!-- Profile Image Section -->
            <div class="section-divider">
                <div class="section-header">
                    <i class="bi bi-image"></i>
                    รูปโปรไฟล์
                </div>
                <div class="profile-image-section">
                    <div>
                        <img src="/api/placeholder/200/180" class="current-image" alt="รูปโปรไฟล์ปัจจุบัน">
                        <div class="file-input-wrapper">
                            <i class="bi bi-camera"></i>
                            เลือกรูปภาพใหม่
                            <input type="file" name="m_img" accept="image/*">
                        </div>
                        <div style="margin-top: 10px;">
                            <small class="text-muted">รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 5MB)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information Section -->
            <div class="section-divider">
                <div class="section-header">
                    <i class="bi bi-person-lines-fill"></i>
                    ข้อมูลพื้นฐาน
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-row">
                            <label class="form-label">
                                <i class="bi bi-person"></i>
                                ชื่อผู้ใช้งาน
                            </label>
                            <input type="text" name="m_username" class="form-control" value="admin_user" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <label class="form-label">
                                <i class="bi bi-envelope"></i>
                                อีเมล <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="m_email" id="m_email" class="form-control" value="admin@example.com" required autocomplete="off">
                            <div id="email-error" class="text-danger"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-row">
                            <label class="form-label">
                                <i class="bi bi-person-badge"></i>
                                ชื่อ <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="m_fname" class="form-control" value="นาย สมชาย" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <label class="form-label">
                                <i class="bi bi-person-badge-fill"></i>
                                นามสกุล <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="m_lname" class="form-control" value="ใจดี" required autocomplete="off">
                            <input type="hidden" name="m_id" value="1">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-row">
                            <label class="form-label">
                                <i class="bi bi-phone"></i>
                                เบอร์มือถือ
                            </label>
                            <input type="text" name="m_phone" class="form-control" pattern="\d{9,10}" title="กรุณากรอกเบอร์มือถือเป็นตัวเลข 9 หรือ 10 ตัว" value="0812345678" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="section-divider">
                <div class="section-header">
                    <i class="bi bi-key"></i>
                    เปลี่ยนรหัสผ่าน
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>หมายเหตุ:</strong> หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นว่างไว้
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-row">
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
                        <div class="form-row">
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

            <!-- System Selection Section -->
            <div class="section-divider">
                <div class="section-header">
                    <i class="bi bi-gear"></i>
                    การเลือกระบบและสิทธิ์
                </div>
                
                <div class="system-selector">
                    <label class="form-label">
                        <i class="bi bi-list-task"></i>
                        เลือกระบบที่ต้องการ
                    </label>
                    <select class="form-control" name="m_system" id="system-select">
                        <option value="system_admin" selected>ระบบ Admin</option>
                        <option value="system_back_office">ระบบ Back Office</option>
                    </select>
                </div>

                <!-- ระบบ Admin -->
                <div id="admin-form" class="system-selector">
                    <label class="form-label">
                        <i class="bi bi-briefcase"></i>
                        ตำแหน่งงาน (Admin)
                    </label>
                    <select class="form-control" name="ref_pid" id="admin-select">
                        <option value="1">ผู้ดูแลระบบ</option>
                        <option value="2">ผู้จัดการ</option>
                        <option value="3" selected>Super Admin</option>
                    </select>
                </div>

                <!-- ระบบ Back Office -->
                <div id="back_office-form" class="system-selector hidden">
                    <label class="form-label">
                        <i class="bi bi-briefcase"></i>
                        ตำแหน่งงาน (Back Office)
                    </label>
                    <select class="form-control" name="ref_pid" id="back_office-select" disabled>
                        <option value="4">เจ้าหน้าที่ขายหน้าร้าน</option>
                        <option value="5">เจ้าหน้าที่คลัง</option>
                    </select>
                </div>

                <!-- Grant User Permissions -->
                <div id="grant-user-form">
                    <label class="form-label">
                        <i class="bi bi-shield-check"></i>
                        สิทธิ์การเข้าถึง
                    </label>
                    
                    <div class="permission-item">
                        <input type="checkbox" id="check-all" name="check-all" class="permission-checkbox" />
                        <label for="check-all" style="margin: 0; font-weight: 600;">เลือกทั้งหมด</label>
                    </div>
                    
                    <div class="permission-grid">
                        <div class="permission-item selected">
                            <input type="checkbox" class="check-item permission-checkbox" name="grant_user[]" value="1" checked id="grant_1" />
                            <label for="grant_1" style="margin: 0;">จัดการผู้ใช้งาน</label>
                        </div>
                        <div class="permission-item selected">
                            <input type="checkbox" class="check-item permission-checkbox" name="grant_user[]" value="2" checked id="grant_2" />
                            <label for="grant_2" style="margin: 0;">จัดการสินค้า</label>
                        </div>
                        <div class="permission-item">
                            <input type="checkbox" class="check-item permission-checkbox" name="grant_user[]" value="3" id="grant_3" />
                            <label for="grant_3" style="margin: 0;">จัดการคำสั่งซื้อ</label>
                        </div>
                        <div class="permission-item selected">
                            <input type="checkbox" class="check-item permission-checkbox" name="grant_user[]" value="4" checked id="grant_4" />
                            <label for="grant_4" style="margin: 0;">รายงาน</label>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="grant_user_ref_id" name="grant_user_ref_id" value="">
            </div>

            <!-- Google Authenticator (2FA) Section -->
            <div class="section-divider">
                <div class="section-header">
                    <i class="bi bi-shield-check"></i>
                    การยืนยันตัวตนแบบ 2 ขั้นตอน (Google Authenticator)
                </div>
                
                <div class="twofa-section">
                    <!-- 2FA Not Enabled -->
                    <div class="twofa-status twofa-disabled">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h5 class="mb-1">ยังไม่ได้เปิดใช้งาน 2FA</h5>
                                <p class="mb-0">การยืนยันตัวตนแบบ 2 ขั้นตอนจะช่วยเพิ่มความปลอดภัยให้กับบัญชีของผู้ใช้</p>
                            </div>
                        </div>
                        <div class="twofa-buttons">
                            <button type="button" class="btn-2fa btn-enable-2fa" onclick="setup2FA()">
                                <i class="bi bi-plus-circle"></i>
                                เปิดใช้งาน 2FA
                            </button>
                        </div>
                    </div>
                    
                    <!-- 2FA Info Box -->
                    <div class="alert alert-info mt-3" role="alert">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            เกี่ยวกับ Google Authenticator
                        </h6>
                        <p class="mb-0">
                            Google Authenticator เป็นแอปพลิเคชันที่ช่วยเพิ่มความปลอดภัยให้กับบัญชี โดยสร้างรหัสยืนยัน 6 หลักที่เปลี่ยนแปลงทุก 30 วินาที 
                            ทำให้แม้จะมีคนรู้รหัสผ่าน ก็ไม่สามารถเข้าใช้งานได้หากไม่มีรหัสจากมือถือ
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button type="submit" class="btn-modern btn-success-modern" form="memberForm">
            <i class="bi bi-check-circle"></i>
            บันทึกข้อมูล
        </button>
        <a href="#" class="btn-modern btn-danger-modern">
            <i class="bi bi-x-circle"></i>
            ยกเลิก
        </a>
        <button type="button" class="btn-modern btn-info-modern" onclick="previewData()">
            <i class="bi bi-eye"></i>
            ดูตัวอย่างข้อมูล
        </button>
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
                    <strong>สำคัญ!</strong> เก็บรหัสเหล่านี้ไว้ในที่ปลอดภัย ใช้ได้เมื่อสูญหายมือถือ
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
        setupEventListeners();
        updateCombinedValues();
        updateCheckAllStatus();
        clearPasswordFields();
    });

    function clearPasswordFields() {
        // เคลียร์ค่ารหัสผ่านเพื่อป้องกัน autocomplete
        document.getElementById('m_password').value = '';
        document.getElementById('confirm_password').value = '';
    }

    function initializeForm() {
        toggleForms();
        
        // ลบ required attribute ออกจากฟิลด์ที่ไม่จำเป็น
        removeUnnecessaryRequired();
    }

    function removeUnnecessaryRequired() {
        // ไม่ต้องลบ required เพราะฟิลด์มีข้อมูลเดิมอยู่แล้ว
        // และต้องการให้ user ต้องกรอกข้อมูลเหล่านี้เสมอ
        console.log('Form initialized with existing data');
    }

    function setupEventListeners() {
        // Email validation
        const emailInput = document.getElementById('m_email');
        const emailError = document.getElementById('email-error');
        const originalEmail = 'admin@example.com'; // ค่าเดิมจากฐานข้อมูล
        const submitButton = document.querySelector('button[type="submit"]');

        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value;
                if (email !== originalEmail && email.trim() !== '') {
                    // จำลองการตรวจสอบอีเมล
                    const existingEmails = ['test@example.com', 'user@example.com'];
                    if (existingEmails.includes(email)) {
                        emailError.textContent = 'อีเมลนี้มีอยู่ในระบบแล้ว';
                        if (submitButton) submitButton.disabled = true;
                    } else {
                        emailError.textContent = '';
                        if (submitButton) submitButton.disabled = false;
                    }
                } else {
                    emailError.textContent = '';
                    if (submitButton) submitButton.disabled = false;
                }
            });
        }

        // System selection changes
        const systemSelect = document.getElementById('system-select');
        const adminSelect = document.getElementById('admin-select');
        const checkAll = document.getElementById('check-all');
        const checkItems = document.querySelectorAll('.check-item');

        if (systemSelect) {
            systemSelect.addEventListener('change', toggleForms);
        }

        if (adminSelect) {
            adminSelect.addEventListener('change', function() {
                const grantuserForm = document.getElementById('grant-user-form');
                if (this.value === '3') {
                    grantuserForm.classList.remove('hidden');
                } else {
                    grantuserForm.classList.add('hidden');
                }
            });
        }

        // Checkbox handling
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkItems.forEach(function(checkbox) {
                    checkbox.checked = checkAll.checked;
                    updatePermissionItemStyle(checkbox);
                });
                updateCombinedValues();
            });
        }

        checkItems.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateCheckAllStatus();
                updateCombinedValues();
                updatePermissionItemStyle(this);
            });
        });

        // Form submission
        const form = document.getElementById('memberForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateForm()) {
                    // อัพเดทค่า combined values ก่อนส่ง
                    updateCombinedValues();
                    
                    // แสดงข้อความสำเร็จ (จำลอง)
                    alert('บันทึกข้อมูลสำเร็จ!');
                    
                    // ในระบบจริงจะส่งข้อมูลไปยัง server
                    // this.submit();
                }
            });
        }
    }

    function toggleForms() {
        const systemSelect = document.getElementById('system-select');
        const adminForm = document.getElementById('admin-form');
        const backOfficeForm = document.getElementById('back_office-form');
        const adminSelect = document.getElementById('admin-select');
        const backOfficeSelect = document.getElementById('back_office-select');
        const grantuserForm = document.getElementById('grant-user-form');

        if (!systemSelect) return;

        const value = systemSelect.value;

        // ซ่อนฟอร์มและปิดการใช้งานทุกฟอร์มก่อน
        if (adminForm) adminForm.classList.add('hidden');
        if (backOfficeForm) backOfficeForm.classList.add('hidden');
        if (grantuserForm) grantuserForm.classList.add('hidden');
        if (adminSelect) adminSelect.disabled = true;
        if (backOfficeSelect) backOfficeSelect.disabled = true;

        // แสดงฟอร์มที่เลือก
        if (value === 'system_admin' && adminForm && adminSelect) {
            adminForm.classList.remove('hidden');
            adminSelect.disabled = false;

            // ตรวจสอบค่าตำแหน่งใน adminSelect
            if (adminSelect.value === '3' && grantuserForm) {
                grantuserForm.classList.remove('hidden');
            }
        } else if (value === 'system_back_office' && backOfficeForm && backOfficeSelect) {
            backOfficeForm.classList.remove('hidden');
            backOfficeSelect.disabled = false;
        }
    }

    function updateCheckAllStatus() {
        const checkAll = document.getElementById('check-all');
        const checkItems = document.querySelectorAll('.check-item');
        
        if (!checkAll || !checkItems.length) return;

        const checkedItems = Array.from(checkItems).filter(item => item.checked);
        const allChecked = checkedItems.length === checkItems.length;
        
        checkAll.checked = allChecked;
        checkAll.indeterminate = checkedItems.length > 0 && checkedItems.length < checkItems.length;
    }

    function updateCombinedValues() {
        const checkItems = document.querySelectorAll('.check-item');
        const combinedValuesInput = document.getElementById('grant_user_ref_id');
        
        if (!combinedValuesInput) return;

        const values = Array.from(checkItems)
            .filter(function(checkbox) {
                return checkbox.checked;
            })
            .map(function(checkbox) {
                return checkbox.value;
            });
        combinedValuesInput.value = values.join(',');
    }

    function updatePermissionItemStyle(checkbox) {
        const permissionItem = checkbox.closest('.permission-item');
        if (permissionItem) {
            if (checkbox.checked) {
                permissionItem.classList.add('selected');
            } else {
                permissionItem.classList.remove('selected');
            }
        }
    }

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            const icon = input.nextElementSibling.querySelector('i');
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

    function validateForm() {
        const password = document.getElementById("m_password").value.trim();
        const confirmPassword = document.getElementById("confirm_password").value.trim();
        
        // ตรวจสอบฟิลด์ที่จำเป็น (เฉพาะที่ว่างเปล่า)
        const requiredFields = {
            'm_fname': 'ชื่อ',
            'm_lname': 'นามสกุล',
            'm_email': 'อีเมล'
        };
        
        for (const [fieldName, fieldLabel] of Object.entries(requiredFields)) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field || field.value.trim() === '') {
                alert(`กรุณากรอก${fieldLabel}`);
                if (field) field.focus();
                return false;
            }
        }

        // ตรวจสอบรหัสผ่าน (เฉพาะเมื่อมีการกรอกใดๆ ในช่องรหัสผ่าน)
        if (password !== "" && confirmPassword !== "") {
            if (password !== confirmPassword) {
                alert("รหัสผ่านไม่ตรงกัน กรุณากรอกใหม่");
                document.getElementById("confirm_password").focus();
                return false;
            }
            
            if (password.length < 6) {
                alert("รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร");
                document.getElementById("m_password").focus();
                return false;
            }
        } else if (password !== "" && confirmPassword === "") {
            alert("กรุณายืนยันรหัสผ่าน");
            document.getElementById("confirm_password").focus();
            return false;
        } else if (password === "" && confirmPassword !== "") {
            alert("กรุณากรอกรหัสผ่านใหม่");
            document.getElementById("m_password").focus();
            return false;
        }
        
        // ตรวจสอบอีเมล format
        const email = document.getElementById('m_email').value.trim();
        if (email !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("รูปแบบอีเมลไม่ถูกต้อง");
                document.getElementById('m_email').focus();
                return false;
            }
        }
        
        return true;
    }

    function previewData() {
        const formData = new FormData(document.getElementById('memberForm'));
        let preview = "ข้อมูลที่จะบันทึก:\n\n";
        
        preview += "ชื่อผู้ใช้: " + formData.get('m_username') + "\n";
        preview += "ชื่อ: " + formData.get('m_fname') + "\n";
        preview += "นามสกุล: " + formData.get('m_lname') + "\n";
        preview += "อีเมล: " + formData.get('m_email') + "\n";
        preview += "เบอร์โทร: " + formData.get('m_phone') + "\n";
        preview += "ระบบ: " + formData.get('m_system') + "\n";
        
        const grantUserRefId = document.getElementById('grant_user_ref_id').value;
        if (grantUserRefId) {
            preview += "สิทธิ์: " + grantUserRefId + "\n";
        }
        
        alert(preview);
    }

    // 2FA Functions
    function setup2FA() {
        alert('เริ่มการตั้งค่า 2FA\n(ในระบบจริงจะเปิด Modal สำหรับสแกน QR Code)');
    }

    function regenerate2FA() {
        if (confirm('ต้องการสร้างรหัส 2FA ใหม่หรือไม่?')) {
            alert('สร้างรหัส 2FA ใหม่สำเร็จ');
        }
    }

    function disable2FA() {
        if (confirm('ต้องการปิดใช้งาน 2FA หรือไม่?')) {
            alert('ปิดใช้งาน 2FA แล้ว');
        }
    }

    function show2FABackupCodes() {
        alert('แสดง Backup Codes\n(ในระบบจริงจะเปิด Modal แสดงรหัสสำรอง)');
    }

    function downloadBackupCodes() {
        alert('ดาวน์โหลด Backup Codes');
    }

    function printBackupCodes() {
        alert('พิมพ์ Backup Codes');
    }
</script>