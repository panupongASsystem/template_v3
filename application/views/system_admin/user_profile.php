<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->session->userdata('tenant_name'); ?> - โปรไฟล์ผู้ใช้</title>
    
    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Kanit:300,400,500,600' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            min-height: 100vh;
            color: #1e293b;
            line-height: 1.5;
            padding: 20px 0;
        }

        .container {
            max-width: 1200px;
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
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark);
        }

        .section-title .title-left {
            display: flex;
            align-items: center;
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
            width: 16px;
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
            transition: all 0.3s ease;
        }

        .current-image:hover {
            transform: scale(1.05);
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

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-secondary-modern {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, var(--warning), #e0a800);
            color: white;
        }

        /* Toggle Edit Mode */
        .edit-toggle-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .edit-toggle-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
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

        .btn-info {
            background: var(--info);
            color: white;
        }

        .btn-2fa:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .info-item {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .info-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .info-label i {
            margin-right: 8px;
            color: var(--primary);
            width: 16px;
        }

        .info-value {
            color: #495057;
            font-size: 1rem;
            margin-left: 24px;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .status-inactive {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        /* Edit Mode Styles */
        .edit-mode {
            display: none;
        }

        .view-mode {
            display: block;
        }

        .is-editing .edit-mode {
            display: block;
        }

        .is-editing .view-mode {
            display: none;
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
            cursor: pointer;
        }

        .device-count-badge:hover {
            background: linear-gradient(135deg, #2980b9, var(--info));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border-left: 4px solid var(--warning);
        }

        .alert-info {
            background: rgba(52, 152, 219, 0.1);
            color: var(--info);
            border-left: 4px solid var(--info);
        }

        /* Back button styles */
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 8px 20px;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
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

        /* Invite 2FA Modal Styles */
        #invite2FAModal .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        #invite2FAModal .modal-header {
            border-radius: 15px 15px 0 0;
            border-bottom: none;
            padding: 25px 30px;
        }

        #invite2FAModal .modal-body {
            padding: 30px;
        }

        #invite2FAModal .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 20px 30px;
            border-radius: 0 0 15px 15px;
        }

        #invite2FAModal .btn-success {
            background: linear-gradient(135deg, var(--success), #1e7e34);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }

        #invite2FAModal .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        #invite2FAModal .btn-light {
            border: 2px solid #e9ecef;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        #invite2FAModal .btn-light:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            transform: translateY(-1px);
        }

        #invite2FAModal .alert-warning {
            border: none;
            background: linear-gradient(135deg, #fff3cd 0%, #fef5e7 100%);
            border-left: 4px solid #ffc107;
        }

        #invite2FAModal .bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        /* Benefits animation */
        #invite2FAModal .d-flex.align-items-start {
            animation: slideInLeft 0.6s ease-out;
            animation-fill-mode: both;
        }

        #invite2FAModal .d-flex.align-items-start:nth-child(1) { animation-delay: 0.1s; }
        #invite2FAModal .d-flex.align-items-start:nth-child(2) { animation-delay: 0.2s; }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Steps animation */
        #invite2FAModal .badge.bg-primary {
            animation: bounceIn 0.8s ease-out;
            animation-fill-mode: both;
        }

        #invite2FAModal .col-4:nth-child(1) .badge { animation-delay: 0.3s; }
        #invite2FAModal .col-4:nth-child(2) .badge { animation-delay: 0.4s; }
        #invite2FAModal .col-4:nth-child(3) .badge { animation-delay: 0.5s; }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Setup step styles */
        .setup-step {
            min-height: 300px;
        }

        .setup-step .card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .setup-step .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Responsive */
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

            .back-button {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 20px;
                align-self: flex-start;
            }

            .section-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="<?php echo site_url('User/choice'); ?>" class="back-button">
    <i class="bi bi-arrow-left"></i>
    กลับไปหน้าสมาร์ทออฟฟิต
</a>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Profile Header -->
            <div class="profile-card">
                <div class="profile-header">
                    <h1 class="profile-title">
                        <i class="bi bi-person-circle me-2"></i>
                        ข้อมูลโปรไฟล์
                    </h1>
                    <p class="profile-subtitle">ดูและจัดการข้อมูลส่วนตัวและการรักษาความปลอดภัย</p>
                </div>
                
                <div class="p-4">
                    <!-- Alert Messages -->
                    <div id="alertContainer"></div>

                    <!-- Profile Image Section -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="title-left">
                                <i class="bi bi-image"></i>
                                รูปโปรไฟล์
                            </div>
                            <button type="button" class="edit-toggle-btn" onclick="toggleEditMode('image')" id="imageEditBtn">
                                <i class="bi bi-pencil"></i> แก้ไข
                            </button>
                        </div>
                        
                        <!-- View Mode -->
                        <div class="view-mode" id="imageViewMode">
                            <div class="profile-image-section">
                                <!-- ใหม่ (ถูกต้อง) -->
								
								
<?php 
// ตรวจสอบว่าไฟล์เป็นรูปใหม่ (ขึ้นต้นด้วย profile_) หรือรูปเก่า
if (!empty($rsedit->m_img)) {
    if (strpos($rsedit->m_img, 'profile_') === 0) {
        // รูปใหม่ (หลังบีบ) -> ใน avatar folder
        $img_path = 'docs/img/avatar/' . $rsedit->m_img;
    } else {
        // รูปเก่า -> ใน img folder โดยตรง
        $img_path = 'docs/img/' . $rsedit->m_img;
    }
} else {
    // รูป default
    $img_path = 'docs/img/default_user.png';
}
?>
								
								
								
                                <img src="<?= base_url($img_path); ?>" class="current-image" alt="รูปโปรไฟล์ปัจจุบัน" id="currentImageDisplay">
                                
                                <?php if (!empty($rsedit->m_img)): ?>
                                    <div class="mt-2">
                                       <!--  <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> 
                                            รูปภาพถูกบีบและปรับขนาดอัตโนมัติเพื่อประหยัดพื้นที่
                                        </small>  -->
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <div class="edit-mode" id="imageEditMode" style="display: none;">
                            <form id="imageForm" enctype="multipart/form-data">
                                <div class="profile-image-section">
                                    <img src="<?= base_url($img_path); ?>" class="current-image" alt="รูปโปรไฟล์ปัจจุบัน" id="previewImage">
                                    <br>
                                    <div class="file-input-wrapper">
                                        <i class="bi bi-upload"></i>
                                        เลือกรูปภาพใหม่
                                        <input type="file" name="m_img" id="m_img" accept="image/*" onchange="previewImageFile(this)">
                                    </div>
                                    
                                    <!-- ข้อมูลไฟล์ -->
                                    <div id="fileInfo" style="display: none;"></div>
                                    
                                    <!-- คำแนะนำ -->
                                    <div class="mt-3">
                                        <div class="alert alert-info">
                                            <h6><i class="bi bi-lightbulb"></i> ข้อมูลที่ควรทราบ:</h6>
                                            <ul class="mb-0 small">
                                                <li>📁 รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 10MB)</li>
                                                <li>🗜️ ระบบจะบีบและปรับขนาดภาพอัตโนมัติเป็น 800x800 พิกเซล</li>
                                                <li>💾 ประหยัดพื้นที่เก็บข้อมูลโดยรักษาคุณภาพภาพ</li>
                                                <li>🔄 รูปภาพจะถูกแปลงเป็น JPG เพื่อการบีบที่ดีที่สุด</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <button type="button" class="btn-modern btn-success-modern me-2" onclick="saveImage()">
                                        <i class="bi bi-check-circle"></i>
                                        <span class="btn-text">บันทึก</span>
                                    </button>
                                    <button type="button" class="btn-modern btn-secondary-modern" onclick="cancelEdit('image')">
                                        <i class="bi bi-x-circle"></i>
                                        ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Basic Information Section -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="title-left">
                                <i class="bi bi-person-lines-fill"></i>
                                ข้อมูลพื้นฐาน
                            </div>
                            <button type="button" class="edit-toggle-btn" onclick="toggleEditMode('basic')" id="basicEditBtn">
                                <i class="bi bi-pencil"></i> แก้ไข
                            </button>
                        </div>
                        
                        <!-- View Mode -->
                        <div class="view-mode" id="basicViewMode">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bi bi-person"></i>
                                            ชื่อผู้ใช้งาน
                                        </div>
                                        <div class="info-value" id="display_username"><?php echo $rsedit->m_username; ?></div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bi bi-person-badge"></i>
                                            ชื่อ
                                        </div>
                                        <div class="info-value" id="display_fname"><?php echo $rsedit->m_fname; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bi bi-person-badge-fill"></i>
                                            นามสกุล
                                        </div>
                                        <div class="info-value" id="display_lname"><?php echo $rsedit->m_lname; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bi bi-envelope"></i>
                                            อีเมล
                                        </div>
                                        <div class="info-value" id="display_email"><?php echo $rsedit->m_email; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="bi bi-phone"></i>
                                            เบอร์มือถือ
                                        </div>
                                        <div class="info-value" id="display_phone"><?php echo !empty($rsedit->m_phone) ? $rsedit->m_phone : '-'; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <div class="edit-mode" id="basicEditMode" style="display: none;">
                            <form id="basicInfoForm">
                                <input type="hidden" name="m_id" value="<?php echo $rsedit->m_id; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-person"></i>
                                                ชื่อผู้ใช้งาน
                                            </label>
                                            <input type="text" class="form-control" name="m_username" id="edit_username" value="<?php echo $rsedit->m_username; ?>" required>
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-person-badge"></i>
                                                ชื่อ
                                            </label>
                                            <input type="text" class="form-control" name="m_fname" id="edit_fname" value="<?php echo $rsedit->m_fname; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-person-badge-fill"></i>
                                                นามสกุล
                                            </label>
                                            <input type="text" class="form-control" name="m_lname" id="edit_lname" value="<?php echo $rsedit->m_lname; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-envelope"></i>
                                                อีเมล
                                            </label>
                                            <input type="email" class="form-control" name="m_email" id="edit_email" value="<?php echo $rsedit->m_email; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-phone"></i>
                                                เบอร์มือถือ
                                            </label>
                                            <input type="tel" class="form-control" name="m_phone" id="edit_phone" value="<?php echo $rsedit->m_phone; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="button" class="btn-modern btn-success-modern me-2" onclick="saveBasicInfo()">
                                        <i class="bi bi-check-circle"></i>
                                        <span class="btn-text">บันทึก</span>
                                    </button>
                                    <button type="button" class="btn-modern btn-secondary-modern" onclick="cancelEdit('basic')">
                                        <i class="bi bi-x-circle"></i>
                                        ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="title-left">
                                <i class="bi bi-key"></i>
                                เปลี่ยนรหัสผ่าน
                            </div>
                            <button type="button" class="edit-toggle-btn" onclick="toggleEditMode('password')" id="passwordEditBtn">
                                <i class="bi bi-pencil"></i> เปลี่ยนรหัสผ่าน
                            </button>
                        </div>
                        
                        <!-- View Mode -->
                        <div class="view-mode" id="passwordViewMode">
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="bi bi-shield-lock"></i>
                                    รหัสผ่าน
                                </div>
                                <div class="info-value">••••••••••••</div>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <div class="edit-mode" id="passwordEditMode" style="display: none;">
                            <form id="passwordForm">
                                <input type="hidden" name="m_id" value="<?php echo $rsedit->m_id; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-lock"></i>
                                                รหัสผ่านใหม่
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="new_password" id="new_password" placeholder="กรอกรหัสผ่านใหม่" minlength="8">
                                                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                                    <i class="bi bi-eye" id="toggleIcon_new_password"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-lock-fill"></i>
                                                ยืนยันรหัสผ่าน
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="ยืนยันรหัสผ่านใหม่" minlength="8">
                                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                                    <i class="bi bi-eye" id="toggleIcon_confirm_password"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="button" class="btn-modern btn-success-modern me-2" onclick="savePassword()">
                                        <i class="bi bi-check-circle"></i>
                                        <span class="btn-text">เปลี่ยนรหัสผ่าน</span>
                                    </button>
                                    <button type="button" class="btn-modern btn-secondary-modern" onclick="cancelEdit('password')">
                                        <i class="bi bi-x-circle"></i>
                                        ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Status Section -->
                    <div class="section-card">
                        <div class="section-title">
                            <div class="title-left">
                                <i class="bi bi-shield-check"></i>
                                สถานะบัญชี
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-toggle-on"></i>
                                        สถานะการใช้งาน
                                    </div>
                                    <div class="info-value">
                                        <?php if ($rsedit->m_status == 1): ?>
                                            <span class="status-badge status-active">
                                                <i class="bi bi-check-circle"></i>ใช้งานได้
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">
                                                <i class="bi bi-x-circle"></i>ระงับการใช้งาน
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-calendar-plus"></i>
                                        วันที่สมัครสมาชิก
                                    </div>
                                    <div class="info-value">
                                        <?php 
                                        if (!empty($rsedit->m_datesave)) {
                                            echo date('d/m/Y H:i', strtotime($rsedit->m_datesave));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-clock-history"></i>
                                        เข้าสู่ระบบล่าสุด
                                    </div>
                                    <div class="info-value">
                                        <?php 
                                        if (!empty($rsedit->last_login_time)) {
                                            echo date('d/m/Y H:i', strtotime($rsedit->last_login_time));
                                            if (!empty($rsedit->last_login_ip)) {
                                                echo '<br><small class="text-muted">IP: ' . $rsedit->last_login_ip . '</small>';
                                            }
                                        } else {
                                            echo 'ไม่มีข้อมูล';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-shield-lock"></i>
                                        การยืนยันตัวตน 2FA
                                    </div>
                                    <div class="info-value">
                                        <?php if (!empty($rsedit->google2fa_secret) && $rsedit->google2fa_enabled == 1): ?>
                                            <span class="status-badge status-active">
                                                <i class="bi bi-shield-check"></i>เปิดใช้งาน
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">
                                                <i class="bi bi-shield-x"></i>ปิดใช้งาน
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-clock-history"></i>
                                        ประวัติการเข้าสู่ระบบ
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="showLoginHistory()">
                                            <i class="bi bi-eye"></i> ดูประวัติ
                                        </button>
                                    </div>
                                    <div class="info-value">
                                        <small class="text-muted">คลิก "ดูประวัติ" เพื่อดูการเข้าสู่ระบบย้อนหลัง 10 ครั้งล่าสุด</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Google Authenticator (2FA) Section -->
                    <div class="section-card" id="security-section">
                        <div class="section-title">
                            <div class="title-left">
                                <i class="bi bi-shield-check"></i>
                                การยืนยันตัวตนแบบ 2 ขั้นตอน (Google Authenticator)
                            </div>
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
                                    <button type="button" class="btn-2fa btn-info" onclick="show2FAInvitationAgain()" style="background: var(--info); color: white;">
                                        <i class="bi bi-info-circle"></i>
                                        ทำไมควรใช้ 2FA?
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
                                <div class="device-count-badge" id="deviceCountBadge" onclick="showDeviceList()" title="คลิกเพื่อดูรายการอุปกรณ์">
                                    <i class="bi bi-phone"></i>
                                    <span id="deviceCount">กำลังโหลด...</span> อุปกรณ์ที่ทำการล็อคอิน
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
                </div>
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
                                <h6 class="mb-1">มี Backup Codes</h6>
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
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" onclick="handleDontShowAgain()">
                    <i class="bi bi-x-circle me-1"></i>ข้ามไปก่อน
                </button>
                <button type="button" class="btn btn-success btn-lg" onclick="startSetup2FAFromInvite()" data-bs-dismiss="modal">
                    <i class="bi bi-shield-plus me-2"></i>เปิดใช้งาน 2FA เลย
                </button>
            </div>
        </div>
    </div>
</div>


<!-- รวม modals ต่างๆ สำหรับ 2FA -->
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

<!-- Modal สำหรับแสดงประวัติการเข้าสู่ระบบ -->
<div class="modal fade" id="loginHistoryModal" tabindex="-1" aria-labelledby="loginHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="loginHistoryModalLabel">
                    <i class="bi bi-clock-history"></i> ประวัติการเข้าสู่ระบบ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- สถิติ -->
                <div class="row mb-4" id="loginStatistics">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-primary mb-1" id="totalAttempts">-</h4>
                            <small class="text-muted">ครั้งทั้งหมด</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-success mb-1" id="successAttempts">-</h4>
                            <small class="text-muted">สำเร็จ</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-danger mb-1" id="failedAttempts">-</h4>
                            <small class="text-muted">ล้มเหลว</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-info mb-1" id="successRate">-</h4>
                            <small class="text-muted">อัตราสำเร็จ (%)</small>
                        </div>
                    </div>
                </div>

                <!-- ประวัติการเข้าสู่ระบบ -->
                <div id="loginHistoryContainer">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">กำลังโหลด...</span>
                        </div>
                        <p class="mt-2">กำลังดึงประวัติการเข้าสู่ระบบ...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="refreshLoginHistory()">
                    <i class="bi bi-arrow-clockwise"></i> รีเฟรช
                </button>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Global variables
    let countdownInterval;
    let qrSessionKey;
    let qrExpiresAt;
    
    // Edit mode management
    let currentEditMode = null;
    let originalData = {};

    // 2FA Invitation tracking
    let invitationShown = false;
	let inviteModalInstance = null; 

    // โหลดข้อมูลเมื่อหน้าโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Document ready, initializing...');
        
       
        
        
        <?php if (!empty($rsedit->google2fa_secret) && $rsedit->google2fa_enabled == 1): ?>
        // โหลดจำนวนอุปกรณ์สำหรับ 2FA
        refreshDeviceCount();
        <?php else: ?>
        // แสดง modal เชิญชวนให้เปิด 2FA สำหรับผู้ใช้ที่ยังไม่เปิด
        setTimeout(function() {
            show2FAInvitation();
        }, 1000);
        <?php endif; ?>

        // เพิ่ม event listener สำหรับ preview image
        const imageInput = document.getElementById('m_img');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const fileInfo = document.getElementById('fileInfo');
                if (this.files.length > 0) {
                    fileInfo.style.display = 'block';
                    fileInfo.innerHTML = `
                        <small class="text-info">
                            <i class="bi bi-file-image"></i> 
                            ไฟล์: ${this.files[0].name} (${formatFileSize(this.files[0].size)})
                        </small>
                    `;
                } else {
                    fileInfo.style.display = 'none';
                }
            });
        }
    });

    // ฟังก์ชันสำหรับ preview รูปภาพ
    function previewImageFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewImage = document.getElementById('previewImage');
                if (previewImage) {
                    previewImage.src = e.target.result;
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ฟังก์ชันสร้าง Modal HTML ด้วย JavaScript
    function createInviteModal() {
        // ตรวจสอบว่ามี modal อยู่แล้วหรือไม่
        if (document.getElementById('invite2FAModal')) {
            console.log('Modal already exists');
            return true;
        }

        console.log('Creating modal dynamically...');

        const modalHTML = `
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
                            <h4 class="text-dark mb-2">ปกป้องบัญชีของคุณด้วย 2FA</h4>
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
                                        <h6 class="mb-1">มี Backup Codes</h6>
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
                                    <p class="mb-0 mt-1">บัญชีที่ไม่มี 2FA มีความเสี่ยงถูกแฮค <strong>99% สูงกว่า</strong> บัญชีที่เปิดใช้งาน 2FA</p>
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
                            
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" onclick="handleDontShowAgain()">
                            <i class="bi bi-x-circle me-1"></i>ข้ามไปก่อน
                        </button>
                        <button type="button" class="btn btn-success btn-lg" onclick="startSetup2FAFromInvite()">
                            <i class="bi bi-shield-plus me-2"></i>เปิดใช้งาน 2FA เลย
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;

        // เพิ่ม modal ลงใน body
        document.body.insertAdjacentHTML('beforeend', modalHTML);

// ✅ เพิ่มส่วนนี้
// เพิ่ม event listeners หลังจากสร้าง modal แล้ว
const skipButton = document.getElementById('skipButton');
const setupButton = document.getElementById('setupButton');

if (skipButton) {
    skipButton.addEventListener('click', handleDontShowAgain);
}

if (setupButton) {
    setupButton.addEventListener('click', startSetup2FAFromInvite);
}

console.log('Modal HTML inserted with event listeners');
		
        
        console.log('Modal HTML inserted');
        
        // ตรวจสอบว่า modal ถูกสร้างแล้วหรือไม่
        const createdModal = document.getElementById('invite2FAModal');
        console.log('Modal created successfully:', !!createdModal);
        
        return !!createdModal;
    }

    // ฟังก์ชันแสดง modal เชิญชวนใช้ 2FA
    function show2FAInvitation() {
        try {
            // ตรวจสอบ Bootstrap
            if (typeof bootstrap === 'undefined') {
                console.log('Bootstrap not loaded');
                return;
            }

            // ตรวจสอบว่า user เคยเลือก "ไม่แสดงอีก" หรือไม่
            const dontShow = localStorage.getItem('2fa_invite_dont_show_<?php echo $rsedit->m_id; ?>');
            if (dontShow === 'true') {
                console.log('User chose not to show invite again');
                return;
            }

            // ตรวจสอบว่าเคยแสดงใน session นี้แล้วหรือไม่
            if (invitationShown) {
                console.log('Invitation already shown in this session');
                return;
            }

            // สร้าง modal ถ้ายังไม่มี
            const modalCreated = createInviteModal();
            if (!modalCreated) {
                console.log('Failed to create modal');
                return;
            }
            
            // หา modal element
            const modalElement = document.getElementById('invite2FAModal');
            if (!modalElement) {
                console.log('Modal element not found');
                return;
            }
            
            try {
    // สร้าง Bootstrap modal instance แบบปกติ (ไม่ใช้ static)
    inviteModalInstance = new bootstrap.Modal(modalElement, {
        backdrop: true,  // ✅ เปลี่ยนเป็น true
        keyboard: true   // ✅ เปลี่ยนเป็น true
    });
    
    // ✅ เพิ่ม event listener เมื่อ modal ถูกซ่อน
    modalElement.addEventListener('hidden.bs.modal', function() {
        console.log('Modal hidden, cleaning up...');
        inviteModalInstance = null;
        modalElement.remove();
    });
    
    // แสดง modal
    inviteModalInstance.show(); // ✅ เปลี่ยนจาก modal.show()
    invitationShown = true;
    console.log('2FA invitation modal shown');
    
} catch (modalError) {
    console.error('Error creating Bootstrap modal:', modalError);
}
            
        } catch (error) {
            console.error('Error showing 2FA invitation:', error);
        }
    }

    // ✅ แทนที่ทั้งฟังก์ชัน
function handleDontShowAgain() {
    try {
        console.log('Skip button clicked');
        
        const checkbox = document.getElementById('dontShowAgain');
        
        if (checkbox && checkbox.checked) {
            localStorage.setItem('2fa_invite_dont_show_<?php echo $rsedit->m_id; ?>', 'true');
            console.log('Saved dont show again preference');
        }
        
        // ปิด modal
        if (inviteModalInstance) {
            console.log('Hiding modal via instance');
            inviteModalInstance.hide();
        } else {
            const modalElement = document.getElementById('invite2FAModal');
            if (modalElement) {
                console.log('Hiding modal via DOM');
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.hide();
            }
        }
        
        showAlert('คุณสามารถเปิดใช้งาน 2FA ได้ในภายหลังจากปุ่ม "เปิดใช้งาน 2FA" ด้านล่าง', 'info', 3000);
        
    } catch (error) {
        console.error('Error in handleDontShowAgain:', error);
    }
}

    // ฟังก์ชันเริ่มต้นการตั้งค่า 2FA จาก modal เชิญชวน
    // ✅ แทนที่ทั้งฟังก์ชัน
function startSetup2FAFromInvite() {
    try {
        console.log('Setup button clicked');
        
        // ปิด modal เชิญชวน
        if (inviteModalInstance) {
            inviteModalInstance.hide();
        } else {
            const modalElement = document.getElementById('invite2FAModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.hide();
            }
        }
        
        // เริ่มต้นการตั้งค่า 2FA
        setTimeout(() => {
            setup2FA();
        }, 500);
        
    } catch (error) {
        console.error('Error in startSetup2FAFromInvite:', error);
    }
}

    // เพิ่มปุ่มใน section 2FA disabled เพื่อแสดง modal เชิญชวนอีกครั้ง
    function show2FAInvitationAgain() {
        try {
            invitationShown = false;
            show2FAInvitation();
        } catch (error) {
            console.error('Error in show2FAInvitationAgain:', error);
        }
    }

    // ฟังก์ชันสำหรับจัดการโหมดแก้ไข
    function toggleEditMode(section) {
        // console.log('Toggling edit mode for section:', section);
        
        if (currentEditMode && currentEditMode !== section) {
            showAlert('กรุณาบันทึกหรือยกเลิกการแก้ไขส่วนปัจจุบันก่อน', 'warning');
            return;
        }

        if (currentEditMode === section) {
            cancelEdit(section);
            return;
        }

        // เก็บข้อมูลเดิมไว้
        storeOriginalData(section);
        
        // เปลี่ยนเป็นโหมดแก้ไข
        const viewMode = document.getElementById(section + 'ViewMode');
        const editMode = document.getElementById(section + 'EditMode');
        const editBtn = document.getElementById(section + 'EditBtn');
        
        if (viewMode && editMode && editBtn) {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
            editBtn.innerHTML = '<i class="bi bi-x"></i> ยกเลิก';
            
            currentEditMode = section;

           
            
            // console.log('Edit mode activated for:', section);
        } else {
            console.error('Required elements not found for section:', section);
        }
    }

    function cancelEdit(section) {
        // console.log('Cancelling edit for section:', section);
        
        // คืนข้อมูลเดิม
        restoreOriginalData(section);
        
        // เปลี่ยนกลับเป็นโหมดดู
        const viewMode = document.getElementById(section + 'ViewMode');
        const editMode = document.getElementById(section + 'EditMode');
        const editBtn = document.getElementById(section + 'EditBtn');
        
        if (viewMode && editMode && editBtn) {
            viewMode.style.display = 'block';
            editMode.style.display = 'none';
            editBtn.innerHTML = '<i class="bi bi-pencil"></i> แก้ไข';
        }
        
        currentEditMode = null;
        
        // รีเซ็ตฟอร์ม
        if (section === 'password') {
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                passwordForm.reset();
            }
        } else if (section === 'image') {
            // รีเซ็ตรูปภาพ preview
            const previewImg = document.getElementById('previewImage');
            const currentImg = document.getElementById('currentImageDisplay');
            if (previewImg && currentImg) {
                previewImg.src = currentImg.src;
            }
            const imageForm = document.getElementById('imageForm');
            if (imageForm) {
                imageForm.reset();
            }
        }
    }

    function storeOriginalData(section) {
        if (section === 'basic') {
            const usernameEl = document.getElementById('display_username');
            const fnameEl = document.getElementById('display_fname');
            const lnameEl = document.getElementById('display_lname');
            const emailEl = document.getElementById('display_email');
            const phoneEl = document.getElementById('display_phone');
           
            
            if (usernameEl && fnameEl && lnameEl && emailEl && phoneEl) {
                originalData.basic = {
                    username: usernameEl.textContent,
                    fname: fnameEl.textContent,
                    lname: lnameEl.textContent,
                    email: emailEl.textContent,
                    phone: phoneEl.textContent,
                    
                };
            }
        } else if (section === 'image') {
            const currentImg = document.getElementById('currentImageDisplay');
            if (currentImg) {
                originalData.image = {
                    src: currentImg.src
                };
            }
        }
    }

    function restoreOriginalData(section) {
        if (section === 'basic' && originalData.basic) {
            const editUsername = document.getElementById('edit_username');
            const editFname = document.getElementById('edit_fname');
            const editLname = document.getElementById('edit_lname');
            const editEmail = document.getElementById('edit_email');
            const editPhone = document.getElementById('edit_phone');
            
            if (editUsername) editUsername.value = originalData.basic.username;
            if (editFname) editFname.value = originalData.basic.fname;
            if (editLname) editLname.value = originalData.basic.lname;
            if (editEmail) editEmail.value = originalData.basic.email;
            if (editPhone) editPhone.value = originalData.basic.phone;
        } else if (section === 'image' && originalData.image) {
            const previewImg = document.getElementById('previewImage');
            if (previewImg) {
                previewImg.src = originalData.image.src;
            }
        }
    }

    // ฟังก์ชันสำหรับแสดง alert
    function showAlert(message, type = 'info', timeout = 5000) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            console.error('Alert container not found');
            return;
        }
        
        const alertId = 'alert_' + Date.now();
        
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}" role="alert">
                <i class="bi bi-${getAlertIcon(type)} me-2"></i>
                <div class="alert-content">
                    ${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        alertContainer.insertAdjacentHTML('beforeend', alertHTML);
        
        if (timeout > 0) {
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, timeout);
        }
    }

    function getAlertIcon(type) {
        switch(type) {
            case 'success': return 'check-circle-fill';
            case 'danger': return 'exclamation-triangle-fill';
            case 'warning': return 'exclamation-triangle-fill';
            case 'info': return 'info-circle-fill';
            default: return 'info-circle-fill';
        }
    }

    // ฟังก์ชันสำหรับจัดการ loading state
    function setLoading(button, isLoading) {
        const btnText = button.querySelector('.btn-text');
        const btnIcon = button.querySelector('i');
        
        if (isLoading) {
            button.disabled = true;
            button.classList.add('loading');
            if (btnText) btnText.textContent = 'กำลังบันทึก...';
            if (btnIcon) {
                btnIcon.className = 'spinner-border spinner-border-sm';
            }
        } else {
            button.disabled = false;
            button.classList.remove('loading');
            if (btnText) btnText.textContent = btnText.getAttribute('data-original') || 'บันทึก';
            if (btnIcon) {
                btnIcon.className = 'bi bi-check-circle';
            }
        }
    }

    // ฟังก์ชันสำหรับบันทึกรูปภาพ
    function saveImage() {
        // console.log('Saving image...');
        
        const button = event.target.closest('.btn-modern');
        const formData = new FormData();
        const fileInput = document.getElementById('m_img');
        
        if (!fileInput || !fileInput.files[0]) {
            showAlert('กรุณาเลือกรูปภาพก่อน', 'warning');
            return;
        }

        // ตรวจสอบขนาดไฟล์ (10MB)
        if (fileInput.files[0].size > 10 * 1024 * 1024) {
            showAlert('ไฟล์รูปภาพต้องมีขนาดไม่เกิน 10MB', 'danger');
            return;
        }

        // แสดงข้อมูลไฟล์ต้นฉบับ
        const originalSize = formatFileSize(fileInput.files[0].size);
      //   console.log('ขนาดไฟล์ต้นฉบับ:', originalSize);

        // ส่งเฉพาะรูปภาพและ user ID
        formData.append('m_img', fileInput.files[0]);
        formData.append('m_id', '<?php echo $rsedit->m_id; ?>');

        setLoading(button, true);

        fetch('<?php echo site_url("system_admin/update_profile_ajax"); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            setLoading(button, false);
            
            if (data.success) {
    // อัพเดทรูปภาพใน UI
    if (data.profile && data.profile.m_img) {
        const avatarPath = '<?php echo base_url("docs/img/avatar/"); ?>';
        const newImageSrc = avatarPath + data.profile.m_img + '?t=' + Date.now();
        
        document.getElementById('currentImageDisplay').src = newImageSrc;
        const previewImg = document.getElementById('previewImage');
        if (previewImg) {
            previewImg.src = newImageSrc;
        }
    }
    
    // ✅ ข้อความสั้นๆ เท่านั้น
    showAlert('อัพเดทรูปภาพสำเร็จ!', 'success', 3000);
    cancelEdit('image');
}
			
			
			
			
			else {
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถอัพเดทรูปภาพได้'), 'danger');
            }
        })
        .catch(error => {
            setLoading(button, false);
            console.error('Error:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

    // ฟังก์ชันสร้างข้อความแสดงผลการบีบภาพ
    function createCompressionMessage(compressionInfo) {
        return `
            <div class="compression-info">
                <h6><i class="bi bi-check-circle-fill"></i> อัพเดทรูปภาพสำเร็จ!</h6>
                <div class="compression-details mt-2">
                    <small>
                        <strong>📊 ข้อมูลการบีบภาพ:</strong><br>
                        🔸 ขนาดเดิม: <span class="text-info">${compressionInfo.original_size}</span><br>
                        🔸 ขนาดหลังบีบ: <span class="text-success">${compressionInfo.compressed_size}</span><br>
                        🔸 ประหยัดพื้นที่: <span class="text-warning">${compressionInfo.saved_space}</span><br>
                        🔸 อัตราการบีบ: <span class="text-primary">${compressionInfo.compression_ratio}</span>
                    </small>
                </div>
            </div>
        `;
    }

    // ฟังก์ชันแปลงขนาดไฟล์เป็นรูปแบบที่อ่านง่าย
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // ฟังก์ชันสำหรับบันทึกข้อมูลพื้นฐาน
    function saveBasicInfo() {
       //  console.log('Saving basic info...');
        
        const button = event.target.closest('.btn-modern');
        const form = document.getElementById('basicInfoForm');
        
        if (!form) {
            showAlert('ไม่พบฟอร์มข้อมูลพื้นฐาน', 'danger');
            return;
        }
        
        const formData = new FormData(form);

        // Validation
        const requiredFields = ['m_username', 'm_fname', 'm_lname', 'm_email'];
        for (let field of requiredFields) {
            if (!formData.get(field)) {
                showAlert('กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');
                return;
            }
        }

        // Email validation
        const email = formData.get('m_email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showAlert('รูปแบบอีเมลไม่ถูกต้อง', 'warning');
            return;
        }

        setLoading(button, true);

        fetch('<?php echo site_url("system_admin/update_profile_ajax"); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            setLoading(button, false);
            
            if (data.success) {
                // อัพเดทข้อมูลใน UI
                const displayUsername = document.getElementById('display_username');
                const displayFname = document.getElementById('display_fname');
                const displayLname = document.getElementById('display_lname');
                const displayEmail = document.getElementById('display_email');
                const displayPhone = document.getElementById('display_phone');
                
                
                if (displayUsername) displayUsername.textContent = data.profile.m_username;
                if (displayFname) displayFname.textContent = data.profile.m_fname;
                if (displayLname) displayLname.textContent = data.profile.m_lname;
                if (displayEmail) displayEmail.textContent = data.profile.m_email;
                if (displayPhone) displayPhone.textContent = data.profile.m_phone || '-';
              
                
                showAlert('อัพเดทข้อมูลสำเร็จ!', 'success');
                cancelEdit('basic');
            } else {
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถอัพเดทข้อมูลได้'), 'danger');
            }
        })
        .catch(error => {
            setLoading(button, false);
            console.error('Error:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

    // ฟังก์ชันสำหรับเปลี่ยนรหัสผ่าน
    function savePassword() {
        console.log('Saving password...');
        
        const button = event.target.closest('.btn-modern');
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Validation
        if (!newPassword || !confirmPassword) {
            showAlert('กรุณากรอกรหัสผ่านให้ครบถ้วน', 'warning');
            return;
        }

        if (newPassword.length < 8) {
            showAlert('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร', 'warning');
            return;
        }

        if (newPassword !== confirmPassword) {
            showAlert('รหัสผ่านไม่ตรงกัน', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('m_id', '<?php echo $rsedit->m_id; ?>');
        formData.append('new_password', newPassword);
        formData.append('confirm_password', confirmPassword);

        setLoading(button, true);

        fetch('<?php echo site_url("system_admin/update_profile_ajax"); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            setLoading(button, false);
            
            if (data.success) {
                showAlert('เปลี่ยนรหัสผ่านสำเร็จ!', 'success');
                cancelEdit('password');
            } else {
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถเปลี่ยนรหัสผ่านได้'), 'danger');
            }
        })
        .catch(error => {
            setLoading(button, false);
            console.error('Error:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

    // ฟังก์ชันสำหรับ toggle รหัสผ่าน
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById('toggleIcon_' + fieldId);
        
        if (field && icon) {
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    }

   
    

    // ฟังก์ชันสำหรับแสดงประวัติการเข้าสู่ระบบ
    function showLoginHistory() {
        const modal = new bootstrap.Modal(document.getElementById('loginHistoryModal'));
        modal.show();
        
        loadLoginHistory();
    }

    // ฟังก์ชันโหลดประวัติการเข้าสู่ระบบ
    function loadLoginHistory() {
        fetch('<?php echo site_url("system_admin/get_login_history"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'user_id=<?php echo $rsedit->m_id; ?>&limit=10'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // อัพเดทสถิติ
                document.getElementById('totalAttempts').textContent = data.statistics.total;
                document.getElementById('successAttempts').textContent = data.statistics.success;
                document.getElementById('failedAttempts').textContent = data.statistics.failed;
                document.getElementById('successRate').textContent = data.statistics.success_rate + '%';

                // แสดงประวัติ
                const container = document.getElementById('loginHistoryContainer');
                
                if (data.history && data.history.length > 0) {
                    let historyHTML = '<div class="table-responsive"><table class="table table-striped">';
                    historyHTML += '<thead><tr><th>วันที่/เวลา</th><th>สถานะ</th><th>IP Address</th><th>Device ID</th></tr></thead><tbody>';
                    
                    data.history.forEach((record) => {
                        const statusBadge = getStatusBadge(record.status);
                        const deviceId = record.fingerprint ? record.fingerprint.substring(0, 8) + '...' : '-';
                        
                        historyHTML += `
                            <tr>
                                <td>${formatDateTime(record.attempt_time)}</td>
                                <td>${statusBadge}</td>
                                <td><code>${record.ip_address || '-'}</code></td>
                                <td><small class="text-muted">${deviceId}</small></td>
                            </tr>
                        `;
                    });
                    
                    historyHTML += '</tbody></table></div>';
                    container.innerHTML = historyHTML;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">ไม่พบประวัติการเข้าสู่ระบบ</h5>
                            <p class="text-muted">ยังไม่มีการบันทึกการเข้าสู่ระบบในระบบ</p>
                        </div>
                    `;
                }
            } else {
                document.getElementById('loginHistoryContainer').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        เกิดข้อผิดพลาด: ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading login history:', error);
            document.getElementById('loginHistoryContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    เกิดข้อผิดพลาดในการเชื่อมต่อ: ${error.message}
                </div>
            `;
        });
    }

    // ฟังก์ชันรีเฟรชประวัติการเข้าสู่ระบบ
    function refreshLoginHistory() {
        document.getElementById('loginHistoryContainer').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <p class="mt-2">กำลังรีเฟรชประวัติ...</p>
            </div>
        `;
        
        setTimeout(() => {
            loadLoginHistory();
        }, 500);
    }

    // ฟังก์ชันสำหรับสร้าง status badge
    function getStatusBadge(status) {
        switch(status) {
            case 'success':
                return '<span class="badge bg-success"><i class="bi bi-check-circle"></i> สำเร็จ</span>';
            case 'failed':
                return '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> ล้มเหลว</span>';
            case 'blocked':
                return '<span class="badge bg-warning"><i class="bi bi-shield-x"></i> ถูกบล็อค</span>';
            case 'reset':
                return '<span class="badge bg-info"><i class="bi bi-arrow-clockwise"></i> รีเซ็ต</span>';
            default:
                return '<span class="badge bg-secondary">' + status + '</span>';
        }
    }

    // ================ Script สำหรับ Device Count Badge เท่านั้น ================

    // แก้ไข refreshDeviceCount() ให้นับจาก modal โดยตรง
    function refreshDeviceCount() {
        // เรียก loadDeviceList เพื่อดึงข้อมูลและนับจำนวน
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
            const deviceCountElement = document.getElementById('deviceCount');
            
            if (data.success && data.devices) {
                // นับเฉพาะอุปกรณ์ที่ยังไม่หมดอายุ
                const activeDevices = data.devices.filter(device => !device.is_expired);
                const count = activeDevices.length;
                
                // อัพเดทจำนวนใน badge
                if (deviceCountElement) {
                    deviceCountElement.textContent = count;
                }
                
                // เปลี่ยนสีตามจำนวนอุปกรณ์
                updateDeviceCountBadgeColor(count);
                
            } else {
                if (deviceCountElement) {
                    deviceCountElement.textContent = '0';
                }
                updateDeviceCountBadgeColor(0);
            }
        })
        .catch(error => {
            console.error('Error loading device count:', error);
            const deviceCountElement = document.getElementById('deviceCount');
            if (deviceCountElement) {
                deviceCountElement.textContent = 'ไม่ทราบ';
            }
            updateDeviceCountBadgeColor('error');
        });
    }

    // ฟังก์ชันอัพเดทสี badge ตามจำนวนอุปกรณ์
    function updateDeviceCountBadgeColor(count) {
        const badge = document.getElementById('deviceCountBadge');
        
        if (!badge) return;
        
        if (count === 'error' || count === 'ไม่ทราบ') {
            badge.style.background = 'linear-gradient(135deg, var(--secondary), #6c757d)';
        } else if (typeof count === 'number') {
            if (count >= 3) {
                badge.style.background = 'linear-gradient(135deg, var(--success), #1e7e34)';
            } else if (count >= 2) {
                badge.style.background = 'linear-gradient(135deg, var(--warning), #e0a800)';
            } else if (count >= 1) {
                badge.style.background = 'linear-gradient(135deg, var(--info), #2980b9)';
            } else {
                badge.style.background = 'linear-gradient(135deg, var(--secondary), #6c757d)';
            }
        }
    }

    // แก้ไข showDeviceList() ให้อัพเดทจำนวนอุปกรณ์หลังจากโหลด modal เสร็จ
    function showDeviceList() {
       //  console.log('Showing device list...');
        
        const modal = new bootstrap.Modal(document.getElementById('deviceListModal'));
        modal.show();
        
        // โหลดข้อมูลใน modal และอัพเดทจำนวนอุปกรณ์
        loadDeviceListAndUpdateCount();
    }

    // ฟังก์ชันใหม่ที่รวม loadDeviceList และการอัพเดทจำนวนอุปกรณ์
    function loadDeviceListAndUpdateCount() {
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
                let activeDeviceCount = 0; // นับอุปกรณ์ที่ใช้งานได้
                
                data.devices.forEach((device, index) => {
                    const deviceIcon = getDeviceIcon(device.platform);
                    const deviceType = getDeviceType(device.platform);
                    const statusBadge = device.is_expired ? 
                        '<span class="badge bg-danger">หมดอายุ</span>' : 
                        '<span class="badge bg-success">ใช้งานได้</span>';
                    
                    // นับเฉพาะอุปกรณ์ที่ยังไม่หมดอายุ
                    if (!device.is_expired) {
                        activeDeviceCount++;
                    }
                    
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
                                    <button class="btn-remove-device mt-2" onclick="removeDeviceAndUpdateCount(${device.id})" title="ลบอุปกรณ์นี้">
                                        <i class="bi bi-trash"></i> ลบ
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = deviceHTML;
                
                // อัพเดทจำนวนอุปกรณ์ใน badge หลัก
                const deviceCountElement = document.getElementById('deviceCount');
                if (deviceCountElement) {
                    deviceCountElement.textContent = activeDeviceCount;
                }
                updateDeviceCountBadgeColor(activeDeviceCount);
                
            } else {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-phone-x" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">ไม่พบอุปกรณ์ที่ลงทะเบียน</h5>
                        <p class="text-muted">ยังไม่มีอุปกรณ์ที่เคยใช้งาน 2FA หรือข้อมูลหมดอายุแล้ว</p>
                    </div>
                `;
                
                // อัพเดทจำนวนอุปกรณ์เป็น 0
                const deviceCountElement = document.getElementById('deviceCount');
                if (deviceCountElement) {
                    deviceCountElement.textContent = '0';
                }
                updateDeviceCountBadgeColor(0);
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
            
            // อัพเดทเป็นไม่ทราบ
            const deviceCountElement = document.getElementById('deviceCount');
            if (deviceCountElement) {
                deviceCountElement.textContent = 'ไม่ทราบ';
            }
            updateDeviceCountBadgeColor('error');
        });
    }

    // ฟังก์ชัน removeDevice ที่อัพเดทจำนวนอุปกรณ์หลังลบสำเร็จ
    function removeDeviceAndUpdateCount(deviceId) {
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
                showAlert('ลบอุปกรณ์สำเร็จ', 'success');
                setTimeout(() => {
                    // โหลดข้อมูล modal ใหม่และอัพเดทจำนวนอุปกรณ์
                    loadDeviceListAndUpdateCount();
                }, 1000);
            } else {
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถลบอุปกรณ์ได้'), 'danger');
            }
        })
        .catch(error => {
            console.error('Error removing device:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

    // Helper functions สำหรับ device management
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
            backdrop: true,
            keyboard: true
        });
        modal.show();
        
        setTimeout(() => {
            nextStep(1);
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
            showAlert('กรุณากรอกรหัส OTP 6 หลัก', 'warning');
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
                showAlert('เปิดใช้งาน 2FA สำเร็จ!', 'success');
                closeModal();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('รหัส OTP ไม่ถูกต้อง กรุณาลองใหม่', 'danger');
            }
        })
        .catch(error => {
            console.error('Verify error:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

    // ฟังก์ชัน 2FA - Add Device
    function showQRCodeForNewDevice() {
       //  console.log('Showing QR Code for new device...');
        
        const modal = new bootstrap.Modal(document.getElementById('addDeviceModal'));
        modal.show();
        
        createQRSession();
    }

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

    function refreshQRCode() {
        // console.log('Refreshing QR Code...');
        
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

    // ฟังก์ชัน 2FA - Regenerate
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

    // ฟังก์ชัน 2FA - Disable
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
                showAlert('ปิดใช้งาน 2FA สำเร็จ กำลังรีเฟรชหน้า...', 'success');
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถปิดใช้งาน 2FA ได้'), 'danger');
            }
        })
        .catch(error => {
            loadingAlert.remove();
            console.error('Disable error:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

    // ฟังก์ชัน 2FA - Backup Codes
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
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถดึง backup codes ได้'), 'danger');
            }
        })
        .catch(error => {
            loadingAlert.remove();
            console.error('Backup codes error:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
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

    // ฟังก์ชัน Device Management
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
            loadDeviceListAndUpdateCount();
        }, 500);
    }

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
                showAlert('ลบอุปกรณ์สำเร็จ', 'success');
                setTimeout(() => {
                    refreshDeviceList();
                }, 1000);
            } else {
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถลบอุปกรณ์ได้'), 'danger');
            }
        })
        .catch(error => {
            console.error('Error removing device:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

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
                showAlert('ลบอุปกรณ์ทั้งหมดสำเร็จ', 'success');
                setTimeout(() => {
                    refreshDeviceList();
                }, 1000);
            } else {
                showAlert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถลบอุปกรณ์ได้'), 'danger');
            }
        })
        .catch(error => {
            console.error('Error removing all devices:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'danger');
        });
    }

    // Clean up interval เมื่อปิด modal
    document.getElementById('addDeviceModal').addEventListener('hidden.bs.modal', function () {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    });

    // เพิ่มการแสดง fileInfo เมื่อเลือกไฟล์
    document.addEventListener('DOMContentLoaded', function() {
        // เพิ่ม CSS สำหรับการแสดงผลที่สวยงาม
        const style = document.createElement('style');
        style.textContent = `
            .compression-info h6 {
                color: #28a745;
                margin-bottom: 10px;
            }
            
            .compression-details {
                background: rgba(40, 167, 69, 0.1);
                border-left: 3px solid #28a745;
                padding: 10px;
                border-radius: 4px;
            }
            
            .compression-details small {
                line-height: 1.6;
            }
            
            .alert-content {
                line-height: 1.5;
            }
            
            .file-input-wrapper p.text-muted {
                margin-top: 10px;
                margin-bottom: 5px;
            }
            
            #fileInfo {
                margin-top: 10px;
                padding: 8px;
                background: rgba(0,123,255,0.1);
                border-radius: 4px;
                border-left: 3px solid #007bff;
            }
        `;
        document.head.appendChild(style);
    });
	
	
	document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && inviteModalInstance) {
       //  console.log('ESC key pressed, closing modal');
        handleDontShowAgain();
    }
});
	
</script>
	
	
	

<!-- 🚨 REQUIRED: Session Warning Modals - สร้างแบบ dynamic จาก JS -->

<!-- 📚 REQUIRED: JavaScript Libraries -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.min.js"></script>

<!-- 🔧 REQUIRED: Session Manager (สำหรับเจ้าหน้าที่) -->
<script src="<?php echo base_url('asset/js/pri-session-manager.js'); ?>"></script>

<!-- 🚨 REQUIRED: Session Management Script สำหรับเจ้าหน้าที่ - แบบสั้น -->
<script>
    // ✅ กำหนด base_url
    window.base_url = '<?php echo base_url(); ?>';
    
    // ✅ เริ่มต้นระบบเมื่อหน้าโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function() {
       // console.log('📚 Document ready, initializing ADMIN session system...');
        
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

  
</script>

	

</body>
</html>